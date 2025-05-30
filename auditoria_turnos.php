<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/config.php';


$auditoria = [];
$sql = "SELECT * FROM auditoria_turnos ORDER BY fecha DESC";
$resultado = $conexion->query($sql);
if ($resultado && $resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $auditoria[] = $fila;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Auditoría - RelaxSp</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="styles/style.css">
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
      <a href="admin_empleados.php"><i class="bi bi-person-workspace me-2"></i>Empleados</a>
      <a href="auditoria_turnos.php" class="active"><i class="bi bi-shield-check me-2"></i>Auditoría</a>
      <a href="logout.php" class="text-danger"><i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión</a>
    </nav>

    <!-- Contenido principal -->
    <main class="col-12 col-md-10 main-content p-4">
      <h2 class="mb-4 text-center text-md-start">
        <i class="bi bi-shield-check me-2"></i>Registro de Auditoría
      </h2>

      <div class="table-responsive">
        <table class="table table-bordered table-hover shadow-sm">
          <thead class="table-light">
            <tr>
              <th>Fecha</th>
              <th>Acción</th>
              <th>Usuario</th>
              <th>Detalle</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($auditoria)): ?>
              <?php foreach ($auditoria as $registro): ?>
                <tr>
                  <td><?= $registro['fecha'] ?></td>
                  <td><?= ucfirst($registro['accion']) ?></td>
                  <td><?= htmlspecialchars($registro['usuario']) ?></td>
                  <td><?= htmlspecialchars($registro['detalle']) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="4" class="text-center">No hay registros de auditoría.</td>
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
