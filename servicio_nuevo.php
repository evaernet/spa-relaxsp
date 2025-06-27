<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $precio = $_POST['precio'] ?? '';

    if ($nombre && $precio !== '') {
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=relaxsp;charset=utf8mb4", "root", "", [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            $stmt = $pdo->prepare("INSERT INTO servicios (nombre, descripcion, precio) VALUES (?, ?, ?)");
            $stmt->execute([$nombre, $descripcion, $precio]);
            header("Location: admin_servicios.php");
            exit;
        } catch (PDOException $e) {
            $error = "Error al guardar: " . $e->getMessage();
        }
    } else {
        $error = "Nombre y precio son obligatorios.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Nuevo Servicio - RelaxSp</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="styles/style.css">
</head>
<body class="admin-page" style="background-color: #f8f9fa;">
  <div class="container my-5">
    <h2 class="mb-4 text-center" style="color: #9C7CC1;">Agregar Nuevo Servicio</h2>
    <p class="text-muted text-center mb-4">Completá los siguientes datos para registrar un nuevo servicio en el sistema.</p>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" class="card p-4 shadow-sm">
      <div class="mb-3">
        <label for="nombre" class="form-label">Nombre del servicio</label>
        <input type="text" name="nombre" id="nombre" class="form-control" required placeholder="Ej: Masajes terapéuticos">
      </div>
      <div class="mb-3">
        <label for="descripcion" class="form-label">Descripción</label>
        <textarea name="descripcion" id="descripcion" class="form-control" rows="3" placeholder="Ej: Relajación muscular profunda..."></textarea>
      </div>
      <div class="mb-3">
        <label for="precio" class="form-label">Precio ($)</label>
        <input type="number" name="precio" id="precio" class="form-control" step="0.01" required placeholder="Ej: 300">
      </div>
      <div class="d-flex justify-content-between">
        <a href="admin_servicios.php" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-success">Guardar Servicio</button>
      </div>
    </form>
  </div>
</body>
</html>
