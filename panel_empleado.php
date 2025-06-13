<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'empleado') {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/config.php'; // Aquí tu conexión mysqli ($conexion)

$usuario = $_SESSION['usuario'] ?? '';

// 1) Obtener ID y nombre del empleado actual
$sqlEmpleado = "
    SELECT e.id, e.nombre 
    FROM empleados e 
    JOIN usuarios u ON e.usuario_id = u.id 
    WHERE u.usuario = ?
";
$stmt = $conexion->prepare($sqlEmpleado);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();
$empleado = $result->fetch_assoc();

$idEmpleado = $empleado['id'] ?? null;
$nombreEmpleado = $empleado['nombre'] ?? ucfirst($usuario);

// 2) FECHA de hoy para filtrar
$hoy = date('Y-m-d');

// 3) === Sección A: Turnos de hoy ===
$turnosHoy = [];
if ($idEmpleado) {
    $sqlTurnosHoy = "
      SELECT
        t.id,
        t.id_cliente,        -- para enlace al historial del cliente
        t.fecha,
        t.hora,
        t.estado,
        c.nombre AS cliente,
        s.nombre AS servicio
      FROM turnos t
      JOIN clientes c  ON t.id_cliente = c.id
      JOIN servicios s ON t.id_servicio = s.id
      WHERE t.id_empleado = ?
        AND t.fecha = ?
      ORDER BY t.hora
    ";
    $stmtHoy = $conexion->prepare($sqlTurnosHoy);
    $stmtHoy->bind_param("is", $idEmpleado, $hoy);
    $stmtHoy->execute();
    $resHoy = $stmtHoy->get_result();
    while ($fila = $resHoy->fetch_assoc()) {
        $turnosHoy[] = $fila;
    }
    $stmtHoy->close();
}

// 4) === Sección B: Próximos turnos (fecha > hoy) ===
$turnosFuturos = [];
if ($idEmpleado) {
    $sqlFuturos = "
      SELECT
        t.id,
        t.id_cliente,        -- para enlace al historial del cliente
        t.fecha,
        t.hora,
        t.estado,
        c.nombre AS cliente,
        s.nombre AS servicio
      FROM turnos t
      JOIN clientes c  ON t.id_cliente = c.id
      JOIN servicios s ON t.id_servicio = s.id
      WHERE t.id_empleado = ?
        AND t.fecha > ?
      ORDER BY t.fecha, t.hora
    ";
    $stmtFut = $conexion->prepare($sqlFuturos);
    $stmtFut->bind_param("is", $idEmpleado, $hoy);
    $stmtFut->execute();
    $resFut = $stmtFut->get_result();
    while ($fila = $resFut->fetch_assoc()) {
        $turnosFuturos[] = $fila;
    }
    $stmtFut->close();
}

// 5) === Sección C: Historial de turnos pasados (fecha < hoy) + detalle (de historial_atenciones) ===
$turnosPasados = [];
if ($idEmpleado) {
    $sqlPasados = "
      SELECT
        t.id             AS id_turno,
        t.id_cliente,          -- para enlace al historial del cliente
        t.fecha,
        t.hora,
        t.estado,
        c.nombre         AS cliente,
        s.nombre         AS servicio,
        h.detalle        AS detalle_historial
      FROM turnos t
      JOIN clientes c  ON t.id_cliente  = c.id
      JOIN servicios s ON t.id_servicio = s.id
      LEFT JOIN historial_atenciones h ON h.id_turno = t.id
      WHERE t.id_empleado = ?
        AND t.fecha < ?
      ORDER BY t.fecha DESC, t.hora DESC
    ";
    $stmtPas = $conexion->prepare($sqlPasados);
    $stmtPas->bind_param("is", $idEmpleado, $hoy);
    $stmtPas->execute();
    $resPas = $stmtPas->get_result();
    while ($fila = $resPas->fetch_assoc()) {
        $turnosPasados[] = $fila;
    }
    $stmtPas->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel del Empleado – RelaxSp</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Bootstrap 5 CSS y Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="styles/style.css">
  <style>
    /* Algunos estilos rápidos para badges */
    .badge-pendiente   { background-color: #6c757d; color: #fff; } /* gris */
    .badge-cumplido    { background-color: #198754; color: #fff; } /* verde */
    .badge-cancelado   { background-color: #dc3545; color: #fff; } /* rojo */
  </style>
</head>
<body class="admin-page">
<div class="container mt-5">

  <!-- Título -->
  <h2 class="mb-4 text-center">Bienvenido/a, <?= htmlspecialchars($nombreEmpleado) ?></h2>

  <!-- Botones de volver / cerrar sesión -->
  <div class="d-flex justify-content-between mb-4">
    <a href="index.php" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left me-1"></i> Volver al inicio
    </a>
    <a href="logout.php" class="btn btn-outline-danger"><i class="bi bi-box-arrow-right me-1"></i> Cerrar sesión</a>
  </div>

  <!-- ========  SECCIÓN A: Turnos de hoy  ======== -->
  <h4>Turnos de hoy (<?= $hoy ?>)</h4>
  <div class="table-responsive mb-5">
    <table class="table table-striped shadow-sm">
      <thead class="table-light">
        <tr>
          <th>Cliente</th>
          <th>Servicio</th>
          <th>Hora</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($turnosHoy)): ?>
          <?php foreach ($turnosHoy as $t): ?>
            <tr>
              <!-- Nombre del cliente como enlace al historial -->
              <td>
                <a href="historial_cliente.php?id_cliente=<?= $t['id_cliente'] ?>" class="link-primary">
                  <?= htmlspecialchars($t['cliente']) ?>
                </a>
              </td>
              <td><?= htmlspecialchars($t['servicio']) ?></td>
              <td><?= htmlspecialchars($t['hora']) ?></td>
              <td>
                <?php
                  // Si viene NULL o vacío, lo tratamos como 'pendiente'
                  $estadoHoy = trim((string)$t['estado']);
                  if ($estadoHoy === '') {
                      echo '<span class="badge badge-pendiente">Pendiente</span>';
                  } else {
                      switch ($estadoHoy) {
                        case 'pendiente':
                          echo '<span class="badge badge-pendiente">Pendiente</span>';
                          break;
                        case 'cumplido':
                          echo '<span class="badge badge-cumplido">Cumplido</span>';
                          break;
                        case 'cancelado':
                          echo '<span class="badge badge-cancelado">Cancelado</span>';
                          break;
                        default:
                          echo ucfirst(htmlspecialchars($estadoHoy));
                      }
                  }
                ?>
              </td>
              <td>
                <?php if ($estadoHoy === '' || $estadoHoy === 'pendiente'): ?>
                  <div class="d-flex gap-1">
                    <!-- Al hacer clic, se abre turno_actualizar.php para "cumplido" -->
                    <a href="turno_actualizar.php?id_turno=<?= $t['id'] ?>&accion=cumplido"
                       class="btn btn-sm btn-success"
                       title="Registrar como cumplido">
                      <i class="bi bi-check2-circle"></i>
                    </a>
                    <!-- Al hacer clic, se abre turno_actualizar.php para "cancelado" -->
                    <a href="turno_actualizar.php?id_turno=<?= $t['id'] ?>&accion=cancelado"
                       class="btn btn-sm btn-danger"
                       title="Registrar como cancelado">
                      <i class="bi bi-x-circle"></i>
                    </a>
                  </div>
                <?php else: ?>
                  <span class="text-muted">Sin acciones</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="5" class="text-center">No tenés turnos asignados para hoy.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- ========  SECCIÓN B: Próximos turnos  ======== -->
  <h4>Próximos turnos</h4>
  <div class="table-responsive mb-5">
    <table class="table table-striped shadow-sm">
      <thead class="table-light">
        <tr>
          <th>Fecha</th>
          <th>Hora</th>
          <th>Cliente</th>
          <th>Servicio</th>
          <th>Estado</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($turnosFuturos)): ?>
          <?php foreach ($turnosFuturos as $t): ?>
            <tr>
              <td><?= htmlspecialchars($t['fecha']) ?></td>
              <td><?= htmlspecialchars($t['hora']) ?></td>
              <td>
                <a href="historial_cliente.php?id_cliente=<?= $t['id_cliente'] ?>" class="link-primary">
                  <?= htmlspecialchars($t['cliente']) ?>
                </a>
              </td>
              <td><?= htmlspecialchars($t['servicio']) ?></td>
              <td>
                <?php
                  $estadoFut = trim((string)$t['estado']);
                  if ($estadoFut === '') {
                      echo '<span class="badge badge-pendiente">Pendiente</span>';
                  } else {
                      switch ($estadoFut) {
                        case 'pendiente':
                          echo '<span class="badge badge-pendiente">Pendiente</span>';
                          break;
                        case 'cumplido':
                          echo '<span class="badge badge-cumplido">Cumplido</span>';
                          break;
                        case 'cancelado':
                          echo '<span class="badge badge-cancelado">Cancelado</span>';
                          break;
                        default:
                          echo ucfirst(htmlspecialchars($estadoFut));
                      }
                  }
                ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="5" class="text-center">No tenés turnos programados para días posteriores.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- ========  SECCIÓN C: Historial de turnos pasados  ======== -->
  <h4>Historial de turnos pasados</h4>
  <div class="table-responsive mb-5">
    <table class="table table-striped shadow-sm">
      <thead class="table-light">
        <tr>
          <th>Fecha</th>
          <th>Hora</th>
          <th>Cliente</th>
          <th>Servicio</th>
          <th>Estado</th>
          <th>Detalle</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($turnosPasados)): ?>
          <?php foreach ($turnosPasados as $t): ?>
            <tr>
              <td><?= htmlspecialchars($t['fecha']) ?></td>
              <td><?= htmlspecialchars($t['hora']) ?></td>
              <td>
                <a href="historial_cliente.php?id_cliente=<?= $t['id_cliente'] ?>" class="link-primary">
                  <?= htmlspecialchars($t['cliente']) ?>
                </a>
              </td>
              <td><?= htmlspecialchars($t['servicio']) ?></td>
              <td>
                <?php
                  $estadoPas = trim((string)$t['estado']);
                  if ($estadoPas === '') {
                      // Si quedó vacío (NULL o ''), mostrar “Pendiente” gris
                      echo '<span class="badge badge-pendiente">Pendiente</span>';
                  } else {
                      switch ($estadoPas) {
                        case 'pendiente':
                          echo '<span class="badge badge-pendiente">Pendiente</span>';
                          break;
                        case 'cumplido':
                          echo '<span class="badge badge-cumplido">Atendido</span>';
                          break;
                        case 'cancelado':
                          echo '<span class="badge badge-cancelado">Cancelado</span>';
                          break;
                        default:
                          // Si fuera, por ej., 'confirmado', imprimirlo sin badge o con ucfirst
                          echo ucfirst(htmlspecialchars($estadoPas));
                      }
                  }
                ?>
              </td>

              <!-- Columna DETALLE: muestra h.detalle si existe, si no “—” -->
              <td>
                <?= $t['detalle_historial'] !== null
                     ? nl2br(htmlspecialchars($t['detalle_historial']))
                     : '<span class="text-muted">—</span>'
                ?>
              </td>

              <!-- ACCIONES: si no hay detalle, “Agregar”; si ya hay detalle, “Editar” -->
              <td>
                <?php if ($t['detalle_historial'] === null): ?>
                  <a href="turno_actualizar.php?id_turno=<?= $t['id_turno'] ?>&origen=historial"
                     class="btn btn-sm btn-outline-primary"
                     title="Agregar detalle">
                    <i class="bi bi-pencil-square"></i> Agregar
                  </a>
                <?php else: ?>
                  <a href="turno_actualizar.php?id_turno=<?= $t['id_turno'] ?>&origen=historial"
                     class="btn btn-sm btn-outline-secondary"
                     title="Editar detalle">
                    <i class="bi bi-pencil"></i> Editar
                  </a>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="7" class="text-center">No hay turnos registrados para fechas anteriores.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

</div>

<!-- Bootstrap JS (para dropdowns, etc.) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
