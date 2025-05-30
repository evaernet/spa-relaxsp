<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Conexión a la base de datos
require_once __DIR__ . '/config.php';


// Obtener servicios
$sql = "SELECT * FROM servicios";
$resultado = $conexion->query($sql);

$servicios = [];
if ($resultado && $resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $servicios[] = $fila;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Servicios - RelaxSp</title>
  <link rel="stylesheet" href="styles/style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container mt-5">
    <h2 class="mb-4">Listado de Servicios</h2>
    <a href="servicio_nuevo.php" class="btn btn-primary mb-3">Agregar nuevo servicio</a>
    <table class="table table-bordered table-hover">
      <thead class="table-light">
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Descripción</th>
          <th>Precio</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($servicios)): ?>
          <?php foreach ($servicios as $servicio): ?>
            <tr>
              <td><?= $servicio['id'] ?></td>
              <td><?= $servicio['nombre'] ?></td>
              <td><?= $servicio['descripcion'] ?></td>
              <td>$<?= number_format($servicio['precio'], 2, ',', '.') ?></td>
              <td>
                <a href="servicio_editar.php?id=<?= $servicio['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                <a href="servicio_eliminar.php?id=<?= $servicio['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Seguro que deseas eliminar este servicio?')">Eliminar</a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="5" class="text-center">No hay servicios cargados.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
    <a href="panel_admin.php" class="btn btn-secondary">Volver al Panel</a>
  </div>
</body>
</html>
