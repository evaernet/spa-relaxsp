<?php
session_start();
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], ['cliente', 'admin'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: panel_cliente.php");
    exit;
}

$idTurno = intval($_GET['id']);
$usuario = $_SESSION['usuario'];
$rol = $_SESSION['rol'];

require_once __DIR__ . '/config.php';


// Validar que el turno pertenezca al cliente si el rol es cliente
if ($rol === 'cliente') {
    $sqlCliente = "SELECT c.id FROM clientes c JOIN usuarios u ON c.usuario_id = u.id WHERE u.usuario = ?";
    $stmt = $conexion->prepare($sqlCliente);
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    $cliente = $result->fetch_assoc();
    $idCliente = $cliente['id'] ?? null;

    if (!$idCliente) {
        header("Location: login.php");
        exit;
    }

    $sqlVerifica = "SELECT id FROM turnos WHERE id = ? AND id_cliente = ?";
    $stmt = $conexion->prepare($sqlVerifica);
    $stmt->bind_param("ii", $idTurno, $idCliente);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        header("Location: panel_cliente.php");
        exit;
    }
}

// Obtener datos del turno
$sqlDatos = "SELECT t.fecha, t.hora, s.nombre AS servicio FROM turnos t 
             JOIN servicios s ON t.id_servicio = s.id WHERE t.id = ?";
$stmt = $conexion->prepare($sqlDatos);
$stmt->bind_param("i", $idTurno);
$stmt->execute();
$result = $stmt->get_result();
$turno = $result->fetch_assoc();

// Cancelar el turno
$sqlCancel = "UPDATE turnos SET estado = 'cancelado' WHERE id = ?";
$stmt = $conexion->prepare($sqlCancel);
$stmt->bind_param("i", $idTurno);
$stmt->execute();

// Registrar en auditoría
if ($turno) {
    $detalle = "Turno cancelado: Servicio = {$turno['servicio']}, Fecha = {$turno['fecha']}, Hora = {$turno['hora']}";
    $accion = "cancelacion_turno";

    $sqlAuditoria = "INSERT INTO auditoria_turnos (turno_id, accion, usuario, detalle)
                     VALUES (?, ?, ?, ?)";
    $stmtAudit = $conexion->prepare($sqlAuditoria);
    $stmtAudit->bind_param("isss", $idTurno, $accion, $usuario, $detalle);
    $stmtAudit->execute();
}

// Redirigir según el rol
if ($rol === 'admin') {
    header("Location: admin_turnos.php");
} else {
    header("Location: panel_cliente.php");
}
exit;
?>
