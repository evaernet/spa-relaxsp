<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/config.php';


$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: admin_clientes.php");
    exit;
}

$mensaje = "";

// Obtener datos actuales del cliente
$stmtCli = $conexion->prepare("SELECT * FROM clientes WHERE id = ?");
$stmtCli->bind_param("i", $id);
$stmtCli->execute();
$result = $stmtCli->get_result();
$cliente = $result->fetch_assoc();

if (!$cliente) {
    die("Cliente no encontrado.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $email = $_POST['email'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $activo = isset($_POST['activo']) ? 1 : 0;
    $usuario = $_SESSION['usuario'];

    if ($nombre && $email) {
        $sql = "UPDATE clientes SET nombre = ?, email = ?, telefono = ?, activo = ? WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("sssii", $nombre, $email, $telefono, $activo, $id);

        if ($stmt->execute()) {
            // Registrar en auditoría
            $detalle = "Cliente editado: ID = $id, Nombre = $nombre, Email = $email, Teléfono = $telefono, Activo = $activo";
            $accion = "edicion_cliente";

            $stmtAudit = $conexion->prepare("INSERT INTO auditoria_turnos (turno_id, accion, usuario, detalle) VALUES (NULL, ?, ?, ?)");
            $stmtAudit->bind_param("sss", $accion, $usuario, $detalle);
            $stmtAudit->execute();

            header("Location: admin_clientes.php");
            exit;
        } else {
            $mensaje = "Error al actualizar.";
        }
    } else {
        $mensaje = "El nombre y el email son obligatorios.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Cliente - RelaxSp</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="admin-page">
<div class="container mt-5">
  <h2 class="mb-4">Editar Cliente #<?= $id ?></h2>

  <a href="admin_clientes.php" class="btn btn-outline-secondary mb-4">
    <i class="bi bi-arrow-left me-1"></i> Volver a clientes
  </a>

  <?php if ($mensaje): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($mensaje) ?></div>
  <?php endif; ?>

  <form method="POST" class="row g-3">
    <div class="col-md-6">
      <label for="nombre" class="form-label">Nombre</label>
      <input type="text" name="nombre" id="nombre" class="form-control" required value="<?= htmlspecialchars($cliente['nombre']) ?>">
    </div>
    <div class="col-md-6">
      <label for="email" class="form-label">Email</label>
      <input type="email" name="email" id="email" class="form-control" required value="<?= htmlspecialchars($cliente['email']) ?>">
    </div>
    <div class="col-md-6">
      <label for="telefono" class="form-label">Teléfono</label>
      <input type="text" name="telefono" id="telefono" class="form-control" value="<?= htmlspecialchars($cliente['telefono']) ?>">
    </div>
    <div class="col-md-6">
      <label class="form-check-label d-block mt-4">
        <input type="checkbox" name="activo" class="form-check-input" <?= $cliente['activo'] ? 'checked' : '' ?>> Cliente activo
      </label>
    </div>
    <div class="col-12">
      <button type="submit" class="btn btn-warning"><i class="bi bi-save me-1"></i>Guardar cambios</button>
    </div>
  </form>
</div>
</body>
</html>
