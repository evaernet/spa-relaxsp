<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'empleado') {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/config.php'; // Confirma que config.php define $conexion

$usuario       = $_SESSION['usuario'] ?? '';
$idTurno       = isset($_REQUEST['id_turno']) ? (int) $_REQUEST['id_turno'] : 0;
$accion        = $_GET['accion'] ?? '';   // “cumplido” o “cancelado”
$mensajeError  = "";

// -----------------------------------------------------------------------------
// 1) Obtener el ID del empleado logueado (para el INSERT/UPDATE en historial_atenciones)
// -----------------------------------------------------------------------------
$idEmpleado = null;
$sqlEmp = "
    SELECT e.id 
    FROM empleados e
    JOIN usuarios u ON e.usuario_id = u.id
    WHERE u.usuario = ?
    LIMIT 1
";
$stmtEmp = $conexion->prepare($sqlEmp);
$stmtEmp->bind_param("s", $usuario);
$stmtEmp->execute();
$resEmp = $stmtEmp->get_result();
if ($filaEmp = $resEmp->fetch_assoc()) {
    $idEmpleado = $filaEmp['id'];
}
$stmtEmp->close();

if (!$idEmpleado) {
    // Si no encontramos empleado, redirigimos
    header("Location: panel_empleado.php");
    exit;
}

// -----------------------------------------------------------------------------
// 2) Obtener el ID del cliente asociado a este turno (para el INSERT/UPDATE en historial_atenciones)
// -----------------------------------------------------------------------------
$idClienteTurno = null;
if ($idTurno) {
    $sqlGetCliente = "SELECT id_cliente FROM turnos WHERE id = ? LIMIT 1";
    $stmtGetCli = $conexion->prepare($sqlGetCliente);
    $stmtGetCli->bind_param("i", $idTurno);
    $stmtGetCli->execute();
    $resGetCli = $stmtGetCli->get_result();
    if ($filaC = $resGetCli->fetch_assoc()) {
        $idClienteTurno = $filaC['id_cliente'];
    }
    $stmtGetCli->close();
}

if (!$idClienteTurno) {
    // Si no existe ese turno o no tiene cliente, redirigimos
    header("Location: panel_empleado.php");
    exit;
}

// -----------------------------------------------------------------------------
// 3) Traer el detalle ya guardado en historial_atenciones (si existe)
// -----------------------------------------------------------------------------
$detalleGuardado = "";
if ($idTurno) {
    $sqlHist = "SELECT detalle FROM historial_atenciones WHERE id_turno = ? LIMIT 1";
    $stmtHist = $conexion->prepare($sqlHist);
    $stmtHist->bind_param("i", $idTurno);
    $stmtHist->execute();
    $resHist = $stmtHist->get_result();
    if ($filaH = $resHist->fetch_assoc()) {
        $detalleGuardado = $filaH['detalle'];
    }
    $stmtHist->close();
}

// -----------------------------------------------------------------------------
// 4) Preseleccionar estado según GET “accion” (cumplido/cancelado)
// -----------------------------------------------------------------------------
$estadoSeleccionado = in_array($accion, ['cumplido', 'cancelado'], true)
                     ? $accion
                     : "";

// -----------------------------------------------------------------------------
// 5) Si envían el formulario por POST, procesar actualización e historial
// -----------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevoEstado  = $_POST['estado']  ?? "";
    $nuevoDetalle = trim($_POST['detalle'] ?? "");

    if (!$idTurno || !in_array($nuevoEstado, ['cumplido', 'cancelado'], true)) {
        $mensajeError = "Faltan datos obligatorios.";
    } else {
        // a) Actualizar el estado en la tabla `turnos`
        $sqlUpdateTurno = "UPDATE turnos SET estado = ? WHERE id = ?";
        $stmtUpd = $conexion->prepare($sqlUpdateTurno);
        $stmtUpd->bind_param("si", $nuevoEstado, $idTurno);
        $stmtUpd->execute();
        $stmtUpd->close();

        // b) Insertar o actualizar en `historial_atenciones`
        $sqlCheck = "SELECT id FROM historial_atenciones WHERE id_turno = ? LIMIT 1";
        $stmtChk = $conexion->prepare($sqlCheck);
        $stmtChk->bind_param("i", $idTurno);
        $stmtChk->execute();
        $resChk = $stmtChk->get_result();

        if ($resChk->num_rows > 0) {
            // Ya existe un registro de historial -> UPDATE
            $filaChk = $resChk->fetch_assoc();
            $idHist = $filaChk['id'];
            $stmtChk->close();

            $sqlUpdHist = "
                UPDATE historial_atenciones
                SET detalle     = ?,
                    fecha_hora  = NOW(),
                    id_empleado = ?,
                    id_cliente  = ?
                WHERE id = ?
            ";
            $stmtUpdHist = $conexion->prepare($sqlUpdHist);
            $stmtUpdHist->bind_param("siii",
                $nuevoDetalle,      // detalle (s)
                $idEmpleado,        // id_empleado (i)
                $idClienteTurno,    // id_cliente (i)
                $idHist             // id (i)
            );
            $stmtUpdHist->execute();
            $stmtUpdHist->close();
        } else {
            // No existe registro -> INSERT
            $stmtChk->close();

            $sqlInsHist = "
                INSERT INTO historial_atenciones
                  (id_turno, id_cliente, id_empleado, detalle)
                VALUES
                  (?, ?, ?, ?)
            ";
            $stmtInsHist = $conexion->prepare($sqlInsHist);
            $stmtInsHist->bind_param("iiis",
                $idTurno,           // id_turno (i)
                $idClienteTurno,    // id_cliente (i)
                $idEmpleado,        // id_empleado (i)
                $nuevoDetalle       // detalle (s)
            );
            $stmtInsHist->execute();
            $stmtInsHist->close();
        }

        // c) Redirigir al panel del empleado
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
  <!-- Bootstrap 5 CSS y Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h3 class="mb-4">Detalle del Turno #<?= htmlspecialchars($idTurno) ?></h3>

  <!-- Mensaje de error -->
  <?php if ($mensajeError): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($mensajeError) ?></div>
  <?php endif; ?>

  <!-- Formulario -->
  <form method="POST" class="row g-3">
    <div class="col-md-6">
      <label for="estado" class="form-label">Estado</label>
      <select name="estado" id="estado" class="form-select" required>
        <option value="">Seleccionar estado…</option>
        <option value="cumplido"   <?= $estadoSeleccionado === 'cumplido'   ? 'selected' : '' ?>>Cumplido</option>
        <option value="cancelado"  <?= $estadoSeleccionado === 'cancelado'  ? 'selected' : '' ?>>Cancelado</option>
      </select>
    </div>

    <div class="col-12">
      <label for="detalle" class="form-label">Detalle (opcional)</label>
      <textarea name="detalle" id="detalle" rows="4" class="form-control"
                placeholder="Describe brevemente qué realizaste o motivo de cancelación…"><?= htmlspecialchars($detalleGuardado) ?></textarea>
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

<!-- Bootstrap JS (opcional) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
