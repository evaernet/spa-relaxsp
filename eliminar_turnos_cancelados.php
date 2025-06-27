<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/config.php';

// Eliminar auditorías asociadas a turnos cancelados
$sqlDeleteAudit = "
    DELETE FROM auditoria_turnos 
    WHERE turno_id IN (
        SELECT id FROM turnos WHERE estado = 'cancelado'
    )";
$conexion->query($sqlDeleteAudit);

// Eliminar turnos cancelados
$sqlDeleteTurnos = "DELETE FROM turnos WHERE estado = 'cancelado'";
$conexion->query($sqlDeleteTurnos);

// Redirigir con mensaje de éxito
header("Location: admin_turnos.php?eliminados=1");
exit;
?>
