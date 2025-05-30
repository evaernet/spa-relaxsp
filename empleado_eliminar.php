<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: admin_empleados.php");
    exit;
}

$id = intval($_GET['id']);
$usuario = $_SESSION['usuario'];

require_once __DIR__ . '/config.php';


// Verificar si el empleado tiene turnos asignados
$sqlVerifica = "SELECT COUNT(*) AS total FROM turnos WHERE id_empleado = ?";
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
          <h4 class='alert-heading'>No se puede eliminar el empleado</h4>
          <p>Este empleado tiene turnos asignados. Debe cancelarlos o reasignarlos antes de eliminarlo.</p>
          <a href='admin_empleados.php' class='btn btn-secondary mt-3'>Volver</a>
        </div>
      </div>
    </body>
    </html>";
    exit;
}

// Obtener datos del empleado para registrar en auditoría
$stmtEmp = $conexion->prepare("SELECT nombre, email FROM empleados WHERE id = ?");
$stmtEmp->bind_param("i", $id);
$stmtEmp->execute();
$resultEmp = $stmtEmp->get_result();
$empleado = $resultEmp->fetch_assoc();

// Eliminar empleado
$sql = "DELETE FROM empleados WHERE id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

// Registrar en auditoría si fue eliminado
if ($empleado) {
    $detalle = "Empleado eliminado: Nombre = {$empleado['nombre']}, Email = {$empleado['email']}";
    $accion = "eliminacion_empleado";

    $sqlAuditoria = "INSERT INTO auditoria_turnos (turno_id, accion, usuario, detalle)
                     VALUES (NULL, ?, ?, ?)";
    $stmtAudit = $conexion->prepare($sqlAuditoria);
    $stmtAudit->bind_param("sss", $accion, $usuario, $detalle);
    $stmtAudit->execute();
}

header("Location: admin_empleados.php");
exit;
?>
