<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'cliente') {
    header("Location: login.php");
    exit;
}

$usuario = $_SESSION['usuario'] ?? '';

require_once __DIR__ . '/config.php';


// Obtener ID y nombre del cliente a través de usuario_id
$sqlCliente = "SELECT c.id, c.nombre 
               FROM clientes c 
               JOIN usuarios u ON c.usuario_id = u.id 
               WHERE u.usuario = ?";
$stmt = $conexion->prepare($sqlCliente);
$stmt->bind_param("s", $usuario);    // <-- Aquí estaba el typo
$stmt->execute();
$result = $stmt->get_result();
$cliente = $result->fetch_assoc();

$idCliente = $cliente['id'] ?? null;
$nombreCliente = $cliente['nombre'] ?? $usuario;

$turnos = [];

if ($idCliente) {
    $sqlTurnos = "SELECT t.id, t.fecha, t.hora, t.estado, s.nombre AS servicio, e.nombre AS empleado
                  FROM turnos t
                  JOIN servicios s ON t.id_servicio = s.id
                  JOIN empleados e ON t.id_empleado = e.id
                  WHERE t.id_cliente = ?
                  ORDER BY t.fecha, t.hora";
    $stmtTurnos = $conexion->prepare($sqlTurnos);
    $stmtTurnos->bind_param("i", $idCliente);
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
  <title>Panel del Cliente - RelaxSp</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="styles/style.css">
</head>
<body class="admin-page">
  <div class="container mt-5">
    <h2 class="mb-4 text-center">Bienvenido/a <?= htmlspecialchars($nombreCliente) ?></h2>

    <div class="d-flex justify-content-between mb-3">
      <a href="turno_nuevo.php" class="btn btn-success">Agendar nuevo turno</a>
      <a href="index.php"           class="btn btn-primary">Ir al Inicio</a>
      <a href="logout.php"          class="btn btn-outline-danger">Cerrar sesión</a>
    </div>

    <h4>Mis turnos</h4>
    <div class="table-responsive">
      <table class="table table-striped shadow-sm">
        <thead class="table-light">
          <tr>
            <th>Fecha</th>
            <th>Hora</th>
            <th>Servicio</th>
            <th>Empleado</th>
            <th>Estado</th>
            <th>Acción</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($turnos)): ?>
            <?php foreach ($turnos as $turno): ?>
              <tr>
                <td><?= $turno['fecha'] ?></td>
                <td><?= $turno['hora'] ?></td>
                <td><?= htmlspecialchars($turno['servicio']) ?></td>
                <td><?= htmlspecialchars($turno['empleado']) ?></td>
                <td><?= ucfirst($turno['estado']) ?></td>
                <td>
                  <?php if ($turno['estado'] === 'pendiente'): ?>
                    <a href="turno_cancelar.php?id=<?= $turno['id'] ?>"
                       class="btn btn-sm btn-danger"
                       onclick="return confirm('¿Deseás cancelar este turno?')">
                      Cancelar
                    </a>
                  <?php else: ?>
                    <span class="text-muted">-</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" class="text-center">Aún no tenés turnos registrados.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
