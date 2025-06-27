<?php
session_start();
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], ['empleado', 'admin'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/config.php';

$idCliente = isset($_GET['id_cliente']) ? (int)$_GET['id_cliente'] : 0;
if (!$idCliente) {
    header("Location: panel_empleado.php");
    exit;
}

// 1) Consultar todos los turnos de este cliente, ordenados de más reciente a antiguo,
//    incluyendo LEFT JOIN a historial_atenciones para el detalle si existe.
$sqlHistCliente = "
    SELECT
      t.id             AS id_turno,
      t.fecha,
      t.hora,
      t.estado,
      s.nombre         AS servicio,
      e.nombre         AS empleado,
      h.detalle        AS detalle_historial
    FROM turnos t
    JOIN servicios s   ON t.id_servicio = s.id
    JOIN empleados e   ON t.id_empleado  = e.id
    LEFT JOIN historial_atenciones h ON h.id_turno = t.id
    WHERE t.id_cliente = ?
    ORDER BY t.fecha DESC, t.hora DESC
";
$stmt = $conexion->prepare($sqlHistCliente);
$stmt->bind_param("i", $idCliente);
$stmt->execute();
$res = $stmt->get_result();

$turnosCliente = [];
while ($row = $res->fetch_assoc()) {
    $turnosCliente[] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Historial de Cliente – RelaxSp</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Bootstrap 5 CSS y Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    /* Badges para estados */
    .badge-pendiente   { background-color: #6c757d; color: #fff; }
    .badge-cumplido    { background-color: #198754; color: #fff; }
    .badge-cancelado   { background-color: #dc3545; color: #fff; }
  </style>
</head>
<body class="admin-page">
  <div class="container mt-5">
    <h2 class="mb-4">Historial del Cliente #<?= htmlspecialchars($idCliente) ?></h2>

    <a href="panel_empleado.php" class="btn btn-outline-secondary mb-4">
      <i class="bi bi-arrow-left me-1"></i> Volver al panel
    </a>

    <div class="table-responsive">
      <table class="table table-striped shadow-sm">
        <thead class="table-light">
          <tr>
            <th>Fecha</th>
            <th>Hora</th>
            <th>Empleado</th>
            <th>Servicio</th>
            <th>Estado</th>
            <th>Detalle</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($turnosCliente)): ?>
            <?php foreach ($turnosCliente as $t): ?>
              <tr>
                <td><?= htmlspecialchars($t['fecha']) ?></td>
                <td><?= htmlspecialchars($t['hora']) ?></td>
                <td><?= htmlspecialchars($t['empleado']) ?></td>
                <td><?= htmlspecialchars($t['servicio']) ?></td>
                <td>
                  <?php
                    $estadoCli = trim((string)$t['estado']);
                    if ($estadoCli === '') {
                        // Si vino vacío (NULL o ''), mostrar “Pendiente”
                        echo '<span class="badge badge-pendiente">Pendiente</span>';
                    } else {
                        switch ($estadoCli) {
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
                            echo ucfirst(htmlspecialchars($estadoCli));
                        }
                    }
                  ?>
                </td>
                <td>
                  <?= $t['detalle_historial'] !== null
                       ? nl2br(htmlspecialchars($t['detalle_historial']))
                       : '<span class="text-muted">—</span>'
                  ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" class="text-center">Este cliente no tiene turnos registrados.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Bootstrap JS (opcional) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
