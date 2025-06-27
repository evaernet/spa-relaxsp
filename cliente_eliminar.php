<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/config.php';

$id = intval($_GET['id']);
$usuario = $_SESSION['usuario'];

// 1. Verificar si tiene turnos activos (no cancelados)
$sqlVerifica = "SELECT COUNT(*) AS total FROM turnos WHERE id_cliente = ? AND estado != 'cancelado'";
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
          <p>Este cliente tiene turnos activos. Debe cancelarlos o eliminarlos antes de continuar.</p>
          <a href='admin_clientes.php' class='btn btn-secondary mt-3'>Volver</a>
        </div>
      </div>
    </body>
    </html>";
    exit;
}

// 2. Eliminar auditoría de sus turnos cancelados
$sqlDeleteAudit = "
    DELETE FROM auditoria_turnos 
    WHERE turno_id IN (
        SELECT id FROM turnos WHERE id_cliente = ? AND estado = 'cancelado'
    )";
$stmtAudit = $conexion->prepare($sqlDeleteAudit);
$stmtAudit->bind_param("i", $id);
$stmtAudit->execute();

// 3. Eliminar sus turnos cancelados
$sqlDeleteTurnos = "DELETE FROM turnos WHERE id_cliente = ? AND estado = 'cancelado'";
$stmtDelTurnos = $conexion->prepare($sqlDeleteTurnos);
$stmtDelTurnos->bind_param("i", $id);
$stmtDelTurnos->execute();

// 4. Obtener datos del cliente para la auditoría
$stmtCli = $conexion->prepare("SELECT nombre, email FROM clientes WHERE id = ?");
$stmtCli->bind_param("i", $id);
$stmtCli->execute();
$resultCli = $stmtCli->get_result();
$cliente = $resultCli->fetch_assoc();

// 5. Eliminar cliente
$stmt = $conexion->prepare("DELETE FROM clientes WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

// 6. Registrar auditoría del cliente eliminado
if ($cliente) {
    $detalle = "Cliente eliminado: Nombre = {$cliente['nombre']}, Email = {$cliente['email']}";
    $accion = "eliminacion_cliente";

    $stmtAudit = $conexion->prepare("INSERT INTO auditoria_turnos (turno_id, accion, usuario, detalle)
                                     VALUES (NULL, ?, ?, ?)");
    $stmtAudit->bind_param("sss", $accion, $usuario, $detalle);
    $stmtAudit->execute();
}

header("Location: admin_clientes.php");
exit;
?>
