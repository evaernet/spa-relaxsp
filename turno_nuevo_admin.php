<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/config.php';


// Cargar clientes
$clientes = [];
$resultClientes = $conexion->query("SELECT id, nombre FROM clientes");
while ($row = $resultClientes->fetch_assoc()) {
    $clientes[] = $row;
}

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

$mensaje = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idCliente = $_POST['cliente'] ?? '';
    $idServicio = $_POST['servicio'] ?? '';
    $idEmpleado = $_POST['empleado'] ?? '';
    $fecha = $_POST['fecha'] ?? '';
    $hora = $_POST['hora'] ?? '';

    if ($idCliente && $idServicio && $idEmpleado && $fecha && $hora) {
        $sql = "INSERT INTO turnos (id_cliente, id_empleado, id_servicio, fecha, hora, estado)
                VALUES (?, ?, ?, ?, ?, 'pendiente')";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("iiiss", $idCliente, $idEmpleado, $idServicio, $fecha, $hora);
        if ($stmt->execute()) {
            header("Location: admin_turnos.php");
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
  <title>Nuevo Turno (Admin) - RelaxSp</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="admin-page">
<div class="container mt-5">
  <h2 class="mb-4"><i class="bi bi-plus-circle me-2"></i>Nuevo Turno - Administrador</h2>

  <a href="admin_turnos.php" class="btn btn-outline-secondary mb-4">
    <i class="bi bi-arrow-left me-1"></i> Volver a turnos
  </a>

  <?php if ($mensaje): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($mensaje) ?></div>
  <?php endif; ?>

  <form method="POST" class="row g-3">
    <div class="col-md-6">
      <label for="cliente" class="form-label">Cliente</label>
      <select name="cliente" id="cliente" class="form-select" required>
        <option value="">Seleccionar cliente...</option>
        <?php foreach ($clientes as $cliente): ?>
          <option value="<?= $cliente['id'] ?>"><?= htmlspecialchars($cliente['nombre']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-6">
      <label for="servicio" class="form-label">Servicio</label>
      <select name="servicio" id="servicio" class="form-select" required>
        <option value="">Seleccionar servicio...</option>
        <?php foreach ($servicios as $servicio): ?>
          <option value="<?= $servicio['id'] ?>"><?= htmlspecialchars($servicio['nombre']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-6">
      <label for="empleado" class="form-label">Empleado</label>
      <select name="empleado" id="empleado" class="form-select" required>
        <option value="">Seleccionar empleado...</option>
        <?php foreach ($empleados as $empleado): ?>
          <option value="<?= $empleado['id'] ?>"><?= htmlspecialchars($empleado['nombre']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-3">
      <label for="fecha" class="form-label">Fecha</label>
      <input type="date" name="fecha" id="fecha" class="form-control" required>
    </div>

    <div class="col-md-3">
      <label for="hora" class="form-label">Hora</label>
      <input type="time" name="hora" id="hora" class="form-control" required>
    </div>

    <div class="col-12">
      <button type="submit" class="btn btn-success"><i class="bi bi-check-circle me-1"></i>Confirmar turno</button>
    </div>
  </form>
</div>
</body>
</html>
