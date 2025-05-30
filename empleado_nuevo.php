
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $email = $_POST['email'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $rol = $_POST['rol'] ?? '';

    if ($nombre && $rol) {
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=relaxsp;charset=utf8mb4", "root", "", [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            $stmt = $pdo->prepare("INSERT INTO empleados (nombre, email, telefono, rol) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nombre, $email, $telefono, $rol]);
            header("Location: admin_empleados.php");
            exit;
        } catch (PDOException $e) {
            $error = "Error al guardar: " . $e->getMessage();
        }
    } else {
        $error = "Nombre y rol son obligatorios.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Nuevo Empleado - RelaxSp</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="styles/style.css">
</head>
<body class="admin-page">
  <div class="container my-5">
    <h2 class="mb-4 text-center text-md-start" style="color: #9C7CC1;">Agregar Nuevo Empleado</h2>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" class="card p-4 shadow-sm">
      <div class="mb-3">
        <label class="form-label">Nombre</label>
        <input type="text" name="nombre" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control">
      </div>
      <div class="mb-3">
        <label class="form-label">Teléfono</label>
        <input type="text" name="telefono" class="form-control">
      </div>
      <div class="mb-3">
        <label class="form-label">Rol</label>
        <input type="text" name="rol" class="form-control" required>
      </div>
      <div class="d-flex justify-content-between">
        <a href="admin_empleados.php" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-success">Guardar Empleado</button>
      </div>
    </form>
  </div>
</body>
</html>
