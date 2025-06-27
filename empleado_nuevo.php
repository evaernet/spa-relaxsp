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
        $error = "El nombre y el rol son obligatorios.";
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
<body class="admin-page" style="background-color: #f8f9fa;">
  <div class="container my-5">
    <h2 class="mb-3 text-center" style="color: #9C7CC1;">Agregar Nuevo Empleado</h2>
    <p class="text-muted text-center mb-4">Completá los siguientes datos para registrar un nuevo miembro del equipo.</p>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" class="card p-4 shadow-sm">
      <div class="mb-3">
        <label for="nombre" class="form-label">Nombre completo</label>
        <input type="text" name="nombre" id="nombre" class="form-control" required placeholder="Ej: Laura Pérez">
      </div>
      <div class="mb-3">
        <label for="email" class="form-label">Correo electrónico</label>
        <input type="email" name="email" id="email" class="form-control" placeholder="Ej: laura@email.com">
      </div>
      <div class="mb-3">
        <label for="telefono" class="form-label">Teléfono</label>
        <input type="text" name="telefono" id="telefono" class="form-control" placeholder="Ej: 3624000000">
      </div>
      <div class="mb-3">
        <label for="rol" class="form-label">Rol</label>
        <input type="text" name="rol" id="rol" class="form-control" required placeholder="Ej: masajista, recepcionista, etc.">
      </div>
      <div class="d-flex justify-content-between">
        <a href="admin_empleados.php" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-success">Guardar Empleado</button>
      </div>
    </form>
  </div>
</body>
</html>
