<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'cliente') {
    header("Location: login.php");
    exit;
}

$usuario = $_SESSION['usuario'] ?? '';
require_once __DIR__ . '/config.php';

$sqlCliente = "SELECT c.id FROM clientes c JOIN usuarios u ON c.usuario_id = u.id WHERE u.usuario = ?";
$stmtCliente = $conexion->prepare($sqlCliente);
$stmtCliente->bind_param("s", $usuario);
$stmtCliente->execute();
$resultCliente = $stmtCliente->get_result();
$cliente = $resultCliente->fetch_assoc();
$idCliente = $cliente['id'] ?? null;

$servicios = [];
$resultServicios = $conexion->query("SELECT id, nombre FROM servicios");
while ($row = $resultServicios->fetch_assoc()) {
    $servicios[] = $row;
}

$empleados = [];
$resultEmpleados = $conexion->query("SELECT id, nombre FROM empleados");
while ($row = $resultEmpleados->fetch_assoc()) {
    $empleados[] = $row;
}

$horasDisponibles = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fecha']) && isset($_POST['servicio'])) {
    $fecha = $_POST['fecha'];
    $servicio = $_POST['servicio'];
    for ($h = 8; $h <= 17; $h++) {
        foreach (["00", "30"] as $m) {
            $hora = sprintf("%02d:%s", $h, $m);
            $stmtVerificar = $conexion->prepare("SELECT id FROM turnos WHERE id_servicio = ? AND fecha = ? AND hora = ?");
            $stmtVerificar->bind_param("iss", $servicio, $fecha, $hora);
            $stmtVerificar->execute();
            $resVerificar = $stmtVerificar->get_result();
            if ($resVerificar->num_rows === 0) {
                $horasDisponibles[] = $hora;
            }
        }
    }
}

$mensaje = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar']) && $idCliente) {
    $idServicio = $_POST['servicio'] ?? '';
    $idEmpleado = $_POST['empleado'] ?? '';
    $fecha = $_POST['fecha'] ?? '';
    $hora = $_POST['hora'] ?? '';

    if ($idServicio && $idEmpleado && $fecha && $hora) {
        $stmtVerificar = $conexion->prepare("SELECT id FROM turnos WHERE id_servicio = ? AND fecha = ? AND hora = ?");
        $stmtVerificar->bind_param("iss", $idServicio, $fecha, $hora);
        $stmtVerificar->execute();
        $resVerificar = $stmtVerificar->get_result();

        if ($resVerificar->num_rows > 0) {
            $mensaje = "Ese turno ya está reservado.";
        } else {
            $sqlInsert = "INSERT INTO turnos (id_cliente, id_empleado, id_servicio, fecha, hora, estado) VALUES (?, ?, ?, ?, ?, 'pendiente')";
            $stmtInsert = $conexion->prepare($sqlInsert);
            $stmtInsert->bind_param("iiiss", $idCliente, $idEmpleado, $idServicio, $fecha, $hora);
            if ($stmtInsert->execute()) {
                $turnoID = $stmtInsert->insert_id;
                header("Location: turno_confirmado.php?id=$turnoID");
                exit;
            } else {
                $mensaje = "Error al guardar el turno.";
            }
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
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const fechaInput = document.getElementById('fecha');
      const hoy = new Date();
      hoy.setDate(hoy.getDate() + 2);
      fechaInput.min = hoy.toISOString().split('T')[0];
    });
  </script>
</head>
<body class="admin-page">
<div class="container mt-5">
  <h2 class="mb-4">Agendá tu turno</h2>

  <a href="panel_cliente.php" class="btn btn-outline-secondary mb-3">
    <i class="bi bi-arrow-left me-1"></i> Volver al panel
  </a>

  <?php if ($mensaje): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($mensaje) ?></div>
  <?php endif; ?>

  <form method="POST" class="card p-4 shadow-sm">
    <div class="row g-3">
      <div class="col-md-6">
        <label for="servicio" class="form-label">Servicio</label>
        <select name="servicio" id="servicio" class="form-select" required onchange="this.form.submit()">
          <option value="">Seleccionar servicio</option>
          <?php foreach ($servicios as $servicio): ?>
            <option value="<?= $servicio['id'] ?>" <?= isset($_POST['servicio']) && $_POST['servicio'] == $servicio['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($servicio['nombre']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-md-6">
        <label for="empleado" class="form-label">Profesional</label>
        <select name="empleado" id="empleado" class="form-select" required>
          <option value="">Seleccionar profesional</option>
          <?php foreach ($empleados as $empleado): ?>
            <option value="<?= $empleado['id'] ?>"><?= htmlspecialchars($empleado['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-md-6">
        <label for="fecha" class="form-label">Fecha</label>
        <input type="date" name="fecha" id="fecha" class="form-control" required value="<?= htmlspecialchars($_POST['fecha'] ?? '') ?>" onchange="this.form.submit()">
      </div>

      <div class="col-md-6">
        <label for="hora" class="form-label">Horario disponible</label>
        <select name="hora" id="hora" class="form-select" required>
          <option value="">Seleccionar horario</option>
          <?php foreach ($horasDisponibles as $hora): ?>
            <option value="<?= $hora ?>"><?= $hora ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-12">
        <button type="submit" name="confirmar" class="btn btn-success w-100">Confirmar turno</button>
      </div>
    </div>
  </form>
</div>
</body>
</html>
