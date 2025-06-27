<?php
$id = $_GET['id'] ?? null;

if (!$id) {
    echo mostrarMensaje("ID de servicio no especificado.");
    exit;
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=relaxsp;charset=utf8mb4", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Verificar si el servicio está vinculado a algún turno
    $stmtVerifica = $pdo->prepare("SELECT COUNT(*) FROM turnos WHERE id_servicio = ?");
    $stmtVerifica->execute([$id]);
    $cantidad = $stmtVerifica->fetchColumn();

    if ($cantidad > 0) {
        echo mostrarMensaje("No se puede eliminar este servicio porque tiene turnos asociados. Debe eliminar o cancelar esos turnos primero.");
        exit;
    }

    // Si no tiene turnos, eliminar
    $stmt = $pdo->prepare("DELETE FROM servicios WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: admin_servicios.php");
    exit;

} catch (PDOException $e) {
    echo mostrarMensaje("Error al eliminar el servicio: " . $e->getMessage());
    exit;
}

// Función para mostrar mensaje con botón "Volver"
function mostrarMensaje($mensaje)
{
    return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Error al eliminar</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container mt-5">
    <div class="alert alert-warning text-center shadow-sm">
      <h4 class="alert-heading">Atención</h4>
      <p>$mensaje</p>
      <a href="admin_servicios.php" class="btn btn-secondary mt-3">Volver</a>
    </div>
  </div>
</body>
</html>
HTML;
}
?>
