<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'empleado') {
    header("Location: login.php");
    exit;
}

$usuario = $_SESSION['usuario'] ?? '';
require_once __DIR__ . '/config.php';


// Obtener ID del empleado actual
$sqlEmpleado = "SELECT e.id, e.nombre FROM empleados e JOIN usuarios u ON e.usuario_id = u.id WHERE u.usuario = ?";
$stmt = $conexion->prepare($sqlEmpleado);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();
$empleado = $result->fetch_assoc();

$idEmpleado = $empleado['id'] ?? null;
$nombreEmpleado = $empleado['nombre'] ?? $usuario;

$turnos = [];
if ($idEmpleado) {
    $sqlTurnos = "SELECT t.id, t.fecha, t.hora, t.estado, s.nombre AS servicio, c.nombre AS cliente
                  FROM turnos t
                  JOIN servicios s ON t.id_servicio = s.id
                  JOIN clientes c ON t.id_cliente = c.id
                  WHERE t.id_empleado = ?
                  ORDER BY t.fecha, t.hora";
    $stmtTurnos = $conexion->prepare($sqlTurnos);
    $stmtTurnos->bind_param("i", $idEmpleado);
    $stmtTurnos->execute();
    $resultadoTurnos = $stmtTurnos->get_result();

    while ($fila = $resultadoTurnos->fetch_assoc()) {
        $turnos[] = $fila;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel del Empleado - RelaxSp</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="styles/style.css">
</head>
<body class="admin-page">
<div class="container mt-5">
  <h2 class="mb-3 text-center">Bienvenido/a <?= htmlspecialchars($nombreEmpleado) ?></h2>

  <a href="index.php" class="btn btn-outline-secondary mb-3">
    <i class="bi bi-arrow-left me-1"></i> Volver al inicio
  </a>

  <div class="d-flex justify-content-end mb-3">
    <a href="logout.php" class="btn btn-outline-danger">Cerrar sesión</a>
  </div>

  <h4>Turnos asignados</h4>
  <div class="table-responsive">
    <table class="table table-striped shadow-sm">
      <thead class="table-light">
        <tr>
          <th>Cliente</th>
          <th>Servicio</th>
          <th>Fecha</th>
          <th>Hora</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($turnos)): ?>
          <?php foreach ($turnos as $turno): ?>
            <tr>
              <td><?= htmlspecialchars($turno['cliente']) ?></td>
              <td><?= htmlspecialchars($turno['servicio']) ?></td>
              <td><?= $turno['fecha'] ?></td>
              <td><?= $turno['hora'] ?></td>
              <td><?= ucfirst($turno['estado']) ?></td>
              <td>
                <?php if ($turno['estado'] === 'pendiente'): ?>
                  <div class="d-flex gap-1">
                    <a href="turno_estado.php?id=<?= $turno['id'] ?>&estado=cumplido" class="btn btn-sm btn-success"
                       onclick="return confirm('¿Marcar como cumplido?')">Cumplido</a>
                    <a href="turno_estado.php?id=<?= $turno['id'] ?>&estado=cancelado" class="btn btn-sm btn-danger"
                       onclick="return confirm('¿Cancelar este turno?')">Cancelar</a>
                  </div>
                <?php else: ?>
                  <span class="text-muted">Sin acciones</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="6" class="text-center">No tenés turnos asignados por el momento.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
