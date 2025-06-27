<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'empleado') {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/config.php';

$usuario = $_SESSION['usuario'] ?? '';
$idTurno = $_SERVER['REQUEST_METHOD'] === 'POST'
    ? (int) ($_POST['id_turno'] ?? 0)
    : (int) ($_GET['id_turno'] ?? 0);

$accion = $_GET['accion'] ?? '';
$mensajeError = "";

// Obtener ID del empleado
$idEmpleado = null;
$stmtEmp = $conexion->prepare("
    SELECT e.id FROM empleados e
    JOIN usuarios u ON e.usuario_id = u.id
    WHERE u.usuario = ?
");
$stmtEmp->bind_param("s", $usuario);
$stmtEmp->execute();
$resEmp = $stmtEmp->get_result();
if ($filaEmp = $resEmp->fetch_assoc()) {
    $idEmpleado = $filaEmp['id'];
}
$stmtEmp->close();

if (!$idEmpleado) {
    header("Location: panel_empleado.php");
    exit;
}

// Obtener cliente del turno
$idClienteTurno = null;
$stmtCli = $conexion->prepare("SELECT id_cliente FROM turnos WHERE id = ? LIMIT 1");
$stmtCli->bind_param("i", $idTurno);
$stmtCli->execute();
$resCli = $stmtCli->get_result();
if ($filaC = $resCli->fetch_assoc()) {
    $idClienteTurno = $filaC['id_cliente'];
}
$stmtCli->close();

if (!$idClienteTurno) {
    header("Location: panel_empleado.php");
    exit;
}

// Traer detalle previo
$detalleGuardado = '';
$stmtHist = $conexion->prepare("SELECT detalle FROM historial_atenciones WHERE id_turno = ? LIMIT 1");
$stmtHist->bind_param("i", $idTurno);
$stmtHist->execute();
$resHist = $stmtHist->get_result();
if ($filaH = $resHist->fetch_assoc()) {
    $detalleGuardado = $filaH['detalle'];
}
$stmtHist->close();

// Estado seleccionado (para mantener selección)
$estadoSeleccionado = $_SERVER['REQUEST_METHOD'] === 'POST'
    ? ($_POST['estado'] ?? '')
    : (in_array($accion, ['cumplido', 'cancelado']) ? $accion : '');

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevoEstado = trim(strtolower($_POST['estado'] ?? ''));
    $nuevoDetalle = trim($_POST['detalle'] ?? '');

    if ($nuevoEstado === '' && $nuevoDetalle !== '') {
        $nuevoEstado = 'cumplido';
    }

    if (!in_array($nuevoEstado, ['cumplido', 'cancelado'], true)) {
        $mensajeError = "Debe seleccionar un estado válido o ingresar un detalle.";
    } else {
        // ✅ FORZAR UPDATE DEL ESTADO
        $stmtUpdate = $conexion->prepare("UPDATE turnos SET estado = ? WHERE id = ?");
        $stmtUpdate->bind_param("si", $nuevoEstado, $idTurno);
        $stmtUpdate->execute();
        $stmtUpdate->close();

        // ✅ Insertar o actualizar detalle en historial
        $stmtCheck = $conexion->prepare("SELECT id FROM historial_atenciones WHERE id_turno = ? LIMIT 1");
        $stmtCheck->bind_param("i", $idTurno);
        $stmtCheck->execute();
        $resCheck = $stmtCheck->get_result();

        if ($resCheck->num_rows > 0) {
            $idHist = $resCheck->fetch_assoc()['id'];
            $stmtCheck->close();

            $stmtUpdHist = $conexion->prepare("
                UPDATE historial_atenciones
                SET detalle = ?, fecha_hora = NOW(), id_empleado = ?, id_cliente = ?
                WHERE id = ?
            ");
            $stmtUpdHist->bind_param("siii", $nuevoDetalle, $idEmpleado, $idClienteTurno, $idHist);
            $stmtUpdHist->execute();
            $stmtUpdHist->close();
        } else {
            $stmtCheck->close();

            $stmtInsHist = $conexion->prepare("
                INSERT INTO historial_atenciones (id_turno, id_cliente, id_empleado, detalle)
                VALUES (?, ?, ?, ?)
            ");
            $stmtInsHist->bind_param("iiis", $idTurno, $idClienteTurno, $idEmpleado, $nuevoDetalle);
            $stmtInsHist->execute();
            $stmtInsHist->close();
        }

        header("Location: panel_empleado.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Actualizar Turno – RelaxSp</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h3 class="mb-4">Detalle del Turno #<?= htmlspecialchars($idTurno) ?></h3>

  <?php if ($mensajeError): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($mensajeError) ?></div>
  <?php endif; ?>

  <form method="POST" class="row g-3">
    <input type="hidden" name="id_turno" value="<?= $idTurno ?>">

    <div class="col-md-6">
      <label for="estado" class="form-label">Estado</label>
      <select name="estado" id="estado" class="form-select">
        <option value="">Seleccionar estado…</option>
        <option value="cumplido" <?= $estadoSeleccionado === 'cumplido' ? 'selected' : '' ?>>Cumplido</option>
        <option value="cancelado" <?= $estadoSeleccionado === 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
      </select>
      <small class="text-muted">Si solo escribís un detalle, se marcará como <strong>cumplido</strong>.</small>
    </div>

    <div class="col-12">
      <label for="detalle" class="form-label">Detalle (opcional)</label>
      <textarea name="detalle" id="detalle" rows="4" class="form-control"><?= htmlspecialchars($detalleGuardado) ?></textarea>
    </div>

    <div class="col-12 d-flex justify-content-between">
      <a href="panel_empleado.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Volver
      </a>
      <button type="submit" class="btn btn-primary">
        <i class="bi bi-check-circle me-1"></i> Guardar
      </button>
    </div>
  </form>
</div>
</body>
</html>
