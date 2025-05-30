<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/config.php';


$idTurno = $_GET['id'] ?? null;
$mensaje = "";

if (!$idTurno) {
    header("Location: admin_turnos.php");
    exit;
}

// Obtener datos del turno
$sql = "SELECT * FROM turnos WHERE id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $idTurno);
$stmt->execute();
$resultado = $stmt->get_result();
$turno = $resultado->fetch_assoc();

if (!$turno) {
    die("Turno no encontrado.");
}

// Obtener servicios
$servicios = [];
$resultServicios = $conexion->query("SELECT id, nombre FROM servicios");
while ($row = $resultServicios->fetch_assoc()) {
    $servicios[] = $row;
}

// Obtener empleados
$empleados = [];
$resultEmpleados = $conexion->query("SELECT id, nombre FROM empleados");
while ($row = $resultEmpleados->fetch_assoc()) {
    $empleados[] = $row;
}

// Procesar actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha = $_POST['fecha'] ?? '';
    $hora = $_POST['hora'] ?? '';
    $idServicio = $_POST['servicio'] ?? '';
    $idEmpleado = $_POST['empleado'] ?? '';

    if ($fecha && $hora && $idServicio && $idEmpleado) {
        $sqlUpdate = "UPDATE turnos SET fecha = ?, hora = ?, id_servicio = ?, id_empleado = ? WHERE id = ?";
        $stmtUpdate = $conexion->prepare($sqlUpdate);
        $stmtUpdate->bind_param("ssiii", $fecha, $hora, $idServicio, $idEmpleado, $idTurno);
        if ($stmtUpdate->execute()) {
            header("Location: admin_turnos.php");
            exit;
        } else {
            $mensaje = "Error al actualizar el turno.";
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
  <title>Editar Turno - RelaxSp</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="admin-page">
<div class="container mt-5">
  <h2 class="mb-4"><i class="bi bi-pencil me-2"></i>Editar Turno #<?= $idTurno ?></h2>

  <a href="admin_turnos.php" class="btn btn-outline-secondary mb-4">
    <i class="bi bi-arrow-left me-1"></i> Volver a turnos
  </a>

  <?php if ($mensaje): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($mensaje) ?></div>
  <?php endif; ?>

  <form method="POST" class="row g-3">
    <div class="col-md-6">
      <label for="fecha" class="form-label">Fecha</label>
      <input type="date" name="fecha" id="fecha" class="form-control" required value="<?= $turno['fecha'] ?>">
    </div>
    <div class="col-md-6">
      <label for="hora" class="form-label">Hora</label>
      <input type="time" name="hora" id="hora" class="form-control" required value="<?= $turno['hora'] ?>">
    </div>
    <div class="col-md-6">
      <label for="servicio" class="form-label">Servicio</label>
      <select name="servicio" id="servicio" class="form-select" required>
        <?php foreach ($servicios as $servicio): ?>
          <option value="<?= $servicio['id'] ?>" <?= $servicio['id'] == $turno['id_servicio'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($servicio['nombre']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-6">
      <label for="empleado" class="form-label">Empleado</label>
      <select name="empleado" id="empleado" class="form-select" required>
        <?php foreach ($empleados as $empleado): ?>
          <option value="<?= $empleado['id'] ?>" <?= $empleado['id'] == $turno['id_empleado'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($empleado['nombre']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-12">
      <button type="submit" class="btn btn-warning"><i class="bi bi-save me-1"></i>Guardar cambios</button>
    </div>
  </form>
</div>
</body>
</html>
