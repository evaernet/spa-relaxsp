<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/config.php';


$empleados = [];
$sql = "SELECT * FROM empleados";
$resultado = $conexion->query($sql);
if ($resultado && $resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $empleados[] = $fila;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Empleados - RelaxSp</title>
  <link rel="stylesheet" href="styles/style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="admin-page">
<div class="container-fluid">
  <div class="row">
    <!-- Menú lateral -->
    <nav class="col-12 text-center col-md-2 barra-nav py-4">
      <img src="image/logo.png" alt="RelaxSp Logo" height="50">
      <h4 class="text-center text-dark mt-2">RelaxSp</h4>
      <a href="panel_admin.php"><i class="bi bi-house-door me-2"></i>Inicio</a>
      <a href="admin_turnos.php"><i class="bi bi-calendar-check me-2"></i>Turnos</a>
      <a href="admin_servicios.php"><i class="bi bi-heart-pulse me-2"></i>Servicios</a>
      <a href="admin_clientes.php"><i class="bi bi-person-lines-fill me-2"></i>Clientes</a>
      <a href="admin_empleados.php" class="active"><i class="bi bi-person-workspace me-2"></i>Empleados</a>
      <a href="auditoria_turnos.php"><i class="bi bi-shield-check me-2"></i>Auditoría</a>
      <a href="logout.php" class="text-danger"><i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión</a>
    </nav>

    <!-- Contenido principal -->
    <main class="col-12 col-md-10 main-content p-4">
      <h2 class="mb-4 text-center text-md-start">
        <i class="bi bi-person-workspace me-2"></i>Empleados registrados
      </h2>

      <div class="d-flex justify-content-end mb-3">
        <a href="empleado_nuevo.php" class="btn btn-primary">
          <i class="bi bi-plus-circle me-1"></i>Nuevo empleado
        </a>
      </div>

      <div class="table-responsive">
        <table class="table table-bordered table-hover shadow-sm">
          <thead class="table-light">
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Email</th>
              <th>Teléfono</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($empleados)): ?>
              <?php foreach ($empleados as $empleado): ?>
                <tr>
                  <td><?= $empleado['id'] ?></td>
                  <td><?= htmlspecialchars($empleado['nombre']) ?></td>
                  <td><?= htmlspecialchars($empleado['email']) ?></td>
                  <td><?= htmlspecialchars($empleado['telefono']) ?></td>
                  <td>
                    <a href="empleado_editar.php?id=<?= $empleado['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                    <a href="empleado_eliminar.php?id=<?= $empleado['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Seguro que deseas eliminar este empleado?')">Eliminar</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="5" class="text-center">No hay empleados registrados.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
