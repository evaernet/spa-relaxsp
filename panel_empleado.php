<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'empleado') {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/config.php';

$usuario = $_SESSION['usuario'] ?? '';
$hoy = date('Y-m-d');
$horaActual = date('H:i:s');

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

function mostrarEstado($estado) {
    $estado = strtolower(trim($estado));
    switch ($estado) {
        case 'pendiente':
        case '':
            return '<span class="badge badge-pendiente">Pendiente</span>';
        case 'cumplido':
        case 'realizado':
            return '<span class="badge badge-cumplido">Cumplido</span>';
        case 'cancelado':
            return '<span class="badge badge-cancelado">Cancelado</span>';
        case 'confirmado':
            return '<span class="badge bg-info text-white">Confirmado</span>';
        default:
            return ucfirst(htmlspecialchars($estado));
    }
}

$turnosHoy = $turnosFuturos = $turnosPasados = [];

if ($idEmpleado) {
    $stmtHoy = $conexion->prepare("SELECT t.id, t.fecha, t.hora, t.estado, c.nombre AS cliente, s.nombre AS servicio FROM turnos t JOIN clientes c ON t.id_cliente = c.id JOIN servicios s ON t.id_servicio = s.id WHERE t.id_empleado = ? AND t.fecha = ? AND t.hora >= ? ORDER BY t.hora");
    $stmtHoy->bind_param("iss", $idEmpleado, $hoy, $horaActual);
    $stmtHoy->execute();
    $resHoy = $stmtHoy->get_result();
    while ($fila = $resHoy->fetch_assoc()) {
        $fila['estado'] = strtolower(trim($fila['estado']));
        $turnosHoy[] = $fila;
    }
    $stmtHoy->close();

    $stmtFut = $conexion->prepare("SELECT t.id, t.fecha, t.hora, t.estado, c.nombre AS cliente, s.nombre AS servicio FROM turnos t JOIN clientes c ON t.id_cliente = c.id JOIN servicios s ON t.id_servicio = s.id WHERE t.id_empleado = ? AND t.fecha > ? ORDER BY t.fecha, t.hora");
    $stmtFut->bind_param("is", $idEmpleado, $hoy);
    $stmtFut->execute();
    $resFut = $stmtFut->get_result();
    while ($fila = $resFut->fetch_assoc()) {
        $fila['estado'] = strtolower(trim($fila['estado']));
        $turnosFuturos[] = $fila;
    }
    $stmtFut->close();

    $stmtPas = $conexion->prepare("SELECT t.id AS id_turno, t.fecha, t.hora, t.estado AS estado_turno, c.nombre AS cliente, s.nombre AS servicio, h.detalle AS detalle_historial FROM turnos t JOIN clientes c ON t.id_cliente = c.id JOIN servicios s ON t.id_servicio = s.id LEFT JOIN historial_atenciones h ON h.id_turno = t.id WHERE t.id_empleado = ? AND (t.fecha < ? OR (t.fecha = ? AND t.hora < ?)) ORDER BY t.fecha DESC, t.hora DESC");
    $stmtPas->bind_param("isss", $idEmpleado, $hoy, $hoy, $horaActual);
    $stmtPas->execute();
    $resPas = $stmtPas->get_result();
    while ($fila = $resPas->fetch_assoc()) {
        $fila['estado'] = !empty($fila['detalle_historial']) ? 'cumplido' : strtolower(trim($fila['estado_turno']));
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
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    .badge-pendiente { background-color: #6c757d; color: white; }
    .badge-cumplido { background-color: #198754; color: white; }
    .badge-cancelado { background-color: #dc3545; color: white; }
  </style>
</head>
<body>
<div class="container mt-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Bienvenido, <?= htmlspecialchars($nombreEmpleado) ?></h2>
    <div>
      <a href="index.php" class="btn btn-outline-secondary"><i class="bi bi-house"></i> Inicio</a>
      <a href="logout.php" class="btn btn-outline-danger"><i class="bi bi-box-arrow-right"></i> Salir</a>
    </div>
  </div>

  <h4 class="mt-4">Turnos de Hoy (<?= $hoy ?>)</h4>
  <table class="table table-bordered">
    <thead><tr><th>Cliente</th><th>Servicio</th><th>Hora</th><th>Estado</th><th>Acciones</th></tr></thead>
    <tbody>
    <?php if ($turnosHoy): foreach ($turnosHoy as $t): ?>
      <tr>
        <td><?= htmlspecialchars($t['cliente']) ?></td>
        <td><?= htmlspecialchars($t['servicio']) ?></td>
        <td><?= htmlspecialchars($t['hora']) ?></td>
        <td><?= mostrarEstado($t['estado']) ?></td>
        <td><span class="text-muted">—</span></td>
      </tr>
    <?php endforeach; else: ?>
      <tr><td colspan="5">No hay turnos para hoy.</td></tr>
    <?php endif; ?>
    </tbody>
  </table>

  <h4 class="mt-4">Próximos Turnos</h4>
  <table class="table table-bordered">
    <thead><tr><th>Fecha</th><th>Hora</th><th>Cliente</th><th>Servicio</th><th>Estado</th></tr></thead>
    <tbody>
    <?php if ($turnosFuturos): foreach ($turnosFuturos as $t): ?>
      <tr>
        <td><?= htmlspecialchars($t['fecha']) ?></td>
        <td><?= htmlspecialchars($t['hora']) ?></td>
        <td><?= htmlspecialchars($t['cliente']) ?></td>
        <td><?= htmlspecialchars($t['servicio']) ?></td>
        <td><?= mostrarEstado($t['estado']) ?></td>
      </tr>
    <?php endforeach; else: ?>
      <tr><td colspan="5">No hay próximos turnos.</td></tr>
    <?php endif; ?>
    </tbody>
  </table>

  <h4 class="mt-4">Historial de Turnos</h4>
  <table class="table table-bordered">
    <thead><tr><th>Fecha</th><th>Hora</th><th>Cliente</th><th>Servicio</th><th>Estado</th><th>Detalle</th></tr></thead>
    <tbody>
    <?php if ($turnosPasados): foreach ($turnosPasados as $t): ?>
      <tr>
        <td><?= htmlspecialchars($t['fecha']) ?></td>
        <td><?= htmlspecialchars($t['hora']) ?></td>
        <td><?= htmlspecialchars($t['cliente']) ?></td>
        <td><?= htmlspecialchars($t['servicio']) ?></td>
        <td><?= mostrarEstado($t['estado']) ?></td>
        <td>
          <?= $t['detalle_historial'] !== null
            ? nl2br(htmlspecialchars($t['detalle_historial'])) . ' <a href="turno_actualizar.php?id_turno=' . $t['id_turno'] . '" class="btn btn-sm btn-link">Editar</a>'
            : '<a href="turno_actualizar.php?id_turno=' . $t['id_turno'] . '" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i> Completar</a>'
          ?>
        </td>
      </tr>
    <?php endforeach; else: ?>
      <tr><td colspan="6">No hay historial disponible.</td></tr>
    <?php endif; ?>
    </tbody>
  </table>
</div>
</body>
</html>
