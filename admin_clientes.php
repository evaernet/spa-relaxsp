<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Conexión a la base de datos
require_once __DIR__ . '/config.php';


// Obtener clientes
$sql = "SELECT * FROM clientes";
$resultado = $conexion->query($sql);

$clientes = [];
if ($resultado && $resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $clientes[] = $fila;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Clientes - RelaxSp</title>
  <link rel="stylesheet" href="styles/style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container mt-5">
    <h2 class="mb-4">Listado de Clientes</h2>
    <a href="cliente_nuevo.php" class="btn btn-primary mb-3">Agregar nuevo cliente</a>
    <table class="table table-bordered table-hover">
      <thead class="table-light">
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Email</th>
          <th>Teléfono</th>
          <th>Activo</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($clientes)): ?>
          <?php foreach ($clientes as $cliente): ?>
            <tr>
              <td><?= $cliente['id'] ?></td>
              <td><?= $cliente['nombre'] ?></td>
              <td><?= $cliente['email'] ?></td>
              <td><?= $cliente['telefono'] ?></td>
              <td><?= $cliente['activo'] ? 'Sí' : 'No' ?></td>
              <td>
                <a href="cliente_editar.php?id=<?= $cliente['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                <a href="cliente_eliminar.php?id=<?= $cliente['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Seguro que deseas eliminar este cliente?')">Eliminar</a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="6" class="text-center">No hay clientes registrados.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
    <a href="panel_admin.php" class="btn btn-secondary">Volver al Panel</a>
  </div>
</body>
</html>
