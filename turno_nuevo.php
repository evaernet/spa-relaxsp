<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'cliente') {
    header("Location: login.php");
    exit;
}

$usuario = $_SESSION['usuario'] ?? '';
require_once __DIR__ . '/config.php';


// Obtener ID del cliente
$sqlCliente = "SELECT c.id FROM clientes c JOIN usuarios u ON c.usuario_id = u.id WHERE u.usuario = ?";
$stmtCliente = $conexion->prepare($sqlCliente);
$stmtCliente->bind_param("s", $usuario);
$stmtCliente->execute();
$resultCliente = $stmtCliente->get_result();
$cliente = $resultCliente->fetch_assoc();
$idCliente = $cliente['id'] ?? null;

// Cargar servicios
$servicios = [];
$resultServicios = $conexion->query("SELECT id, nombre FROM servicios");
while ($row = $resultServicios->fetch_assoc()) {
    $servicios[] = $row;
}

// Cargar empleados
$empleados = [];
$resultEmpleados = $conexion->query("SELECT id, nombre FROM empleados");
while ($row = $resultEmpleados->fetch_assoc()) {
    $empleados[] = $row;
}

// Procesar formulario
$mensaje = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $idCliente) {
    $idServicio = $_POST['servicio'] ?? '';
    $idEmpleado = $_POST['empleado'] ?? '';
    $fecha = $_POST['fecha'] ?? '';
    $hora = $_POST['hora'] ?? '';

    if ($idServicio && $idEmpleado && $fecha && $hora) {
        $sqlInsert = "INSERT INTO turnos (id_cliente, id_empleado, id_servicio, fecha, hora, estado)
                      VALUES (?, ?, ?, ?, ?, 'pendiente')";
        $stmtInsert = $conexion->prepare($sqlInsert);
        $stmtInsert->bind_param("iiiss", $idCliente, $idEmpleado, $idServicio, $fecha, $hora);
        if ($stmtInsert->execute()) {
            header("Location: panel_cliente.php");
            exit;
        } else {
            $mensaje = "Error al guardar el turno.";
        }
    } else {
        $mensaje = "Todos los campos son obligatorios.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Nuevo Turno - RelaxSp</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="styles/style.css">
</head>
<body class="admin-page">
<div class="container mt-5">
  <h2 class="mb-3">Agendar nuevo turno</h2>

  <a href="panel_cliente.php" class="btn btn-outline-secondary mb-4">
    <i class="bi bi-arrow-left me-1"></i> Volver al panel
  </a>

  <?php if ($mensaje): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($mensaje) ?></div>
  <?php endif; ?>

  <form method="POST" class="row g-3">
    <div class="col-md-6">
      <label for="servicio" class="form-label">Servicio</label>
      <select name="servicio" id="servicio" class="form-select" required>
        <option value="">Seleccionar...</option>
        <?php foreach ($servicios as $servicio): ?>
          <option value="<?= $servicio['id'] ?>"><?= htmlspecialchars($servicio['nombre']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-6">
      <label for="empleado" class="form-label">Empleado</label>
      <select name="empleado" id="empleado" class="form-select" required>
        <option value="">Seleccionar...</option>
        <?php foreach ($empleados as $empleado): ?>
          <option value="<?= $empleado['id'] ?>"><?= htmlspecialchars($empleado['nombre']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-6">
      <label for="fecha" class="form-label">Fecha</label>
      <input type="date" name="fecha" id="fecha" class="form-control" required>
    </div>

    <div class="col-md-6">
      <label for="hora" class="form-label">Hora</label>
      <input type="time" name="hora" id="hora" class="form-control" required>
    </div>

    <div class="col-12">
      <button type="submit" class="btn btn-success">Confirmar turno</button>
      <a href="panel_cliente.php" class="btn btn-secondary">Cancelar</a>
    </div>
  </form>
</div>
</body>
</html>
