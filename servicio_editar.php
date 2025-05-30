
<?php
$pdo = new PDO("mysql:host=localhost;dbname=relaxsp;charset=utf8mb4", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

$id = $_GET['id'] ?? null;
if (!$id) {
    die("ID no proporcionado.");
}

$stmt = $pdo->prepare("SELECT * FROM servicios WHERE id = ?");
$stmt->execute([$id]);
$servicio = $stmt->fetch();

if (!$servicio) {
    die("Servicio no encontrado.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $precio = $_POST['precio'] ?? '';

    if ($nombre && $precio !== '') {
        $stmt = $pdo->prepare("UPDATE servicios SET nombre=?, descripcion=?, precio=? WHERE id=?");
        $stmt->execute([$nombre, $descripcion, $precio, $id]);
        header("Location: admin_servicios.php");
        exit;
    } else {
        $error = "Nombre y precio son obligatorios.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Servicio - RelaxSp</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="styles/style.css">
</head>
<body class="admin-page">
  <div class="container my-5">
    <h2 class="mb-4 text-center text-md-start" style="color: #9C7CC1;">Editar Servicio</h2>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" class="card p-4 shadow-sm">
      <div class="mb-3">
        <label class="form-label">Nombre</label>
        <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($servicio['nombre']) ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Descripción</label>
        <textarea name="descripcion" class="form-control" rows="3"><?= htmlspecialchars($servicio['descripcion']) ?></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label">Precio</label>
        <input type="number" name="precio" class="form-control" step="0.01" value="<?= htmlspecialchars($servicio['precio']) ?>" required>
      </div>
      <div class="d-flex justify-content-between">
        <a href="admin_servicios.php" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
      </div>
    </form>
  </div>
</body>
</html>
