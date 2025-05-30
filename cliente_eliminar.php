<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/config.php';


$id = intval($_GET['id']);
$usuario = $_SESSION['usuario'];

$conexion = new mysqli("localhost", "root", "", "relaxsp");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Verificar si el cliente tiene turnos asignados
$sqlVerifica = "SELECT COUNT(*) AS total FROM turnos WHERE id_cliente = ?";
$stmtVerifica = $conexion->prepare($sqlVerifica);
$stmtVerifica->bind_param("i", $id);
$stmtVerifica->execute();
$resultVerifica = $stmtVerifica->get_result();
$data = $resultVerifica->fetch_assoc();

if ($data['total'] > 0) {
    echo "<!DOCTYPE html>
    <html lang='es'>
    <head>
      <meta charset='UTF-8'>
      <title>Error al eliminar - RelaxSp</title>
      <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
    </head>
    <body class='bg-light'>
      <div class='container mt-5'>
        <div class='alert alert-warning text-center'>
          <h4 class='alert-heading'>No se puede eliminar el cliente</h4>
          <p>Este cliente tiene turnos registrados. Debe cancelarlos antes de eliminarlo.</p>
          <a href='admin_clientes.php' class='btn btn-secondary mt-3'>Volver</a>
        </div>
      </div>
    </body>
    </html>";
    exit;
}

// Obtener datos del cliente para la auditoría
$stmtCli = $conexion->prepare("SELECT nombre, email FROM clientes WHERE id = ?");
$stmtCli->bind_param("i", $id);
$stmtCli->execute();
$resultCli = $stmtCli->get_result();
$cliente = $resultCli->fetch_assoc();

// Eliminar cliente
$sql = "DELETE FROM clientes WHERE id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

// Registrar auditoría
if ($cliente) {
    $detalle = "Cliente eliminado: Nombre = {$cliente['nombre']}, Email = {$cliente['email']}";
    $accion = "eliminacion_cliente";

    $sqlAuditoria = "INSERT INTO auditoria_turnos (turno_id, accion, usuario, detalle)
                     VALUES (NULL, ?, ?, ?)";
    $stmtAudit = $conexion->prepare($sqlAuditoria);
    $stmtAudit->bind_param("sss", $accion, $usuario, $detalle);
    $stmtAudit->execute();
}

header("Location: admin_clientes.php");
exit;
?>
