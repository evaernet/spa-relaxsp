
<?php
$id = $_GET['id'] ?? null;

if (!$id) {
    die("ID de servicio no especificado.");
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=relaxsp;charset=utf8mb4", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $stmt = $pdo->prepare("DELETE FROM servicios WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: admin_servicios.php");
    exit;
} catch (PDOException $e) {
    die("Error al eliminar el servicio: " . $e->getMessage());
}
?>
