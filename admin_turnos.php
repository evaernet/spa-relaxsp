<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/config.php';

$sql = "SELECT t.id, t.fecha, t.hora, t.estado, 
               c.nombre AS cliente, 
               s.nombre AS servicio 
        FROM turnos t
        JOIN clientes c ON t.id_cliente = c.id
        JOIN servicios s ON t.id_servicio = s.id
        ORDER BY t.fecha, t.hora";

$resultado = $conexion->query($sql);
$turnos = [];
if ($resultado && $resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $turnos[] = $fila;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Turnos - RelaxSp</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
      <a href="admin_turnos.php" class="active"><i class="bi bi-calendar-check me-2"></i>Turnos</a>
      <a href="admin_servicios.php"><i class="bi bi-heart-pulse me-2"></i>Servicios</a>
      <a href="admin_clientes.php"><i class="bi bi-person-lines-fill me-2"></i>Clientes</a>
      <a href="admin_empleados.php"><i class="bi bi-person-workspace me-2"></i>Empleados</a>
      <a href="auditoria_turnos.php"><i class="bi bi-shield-check me-2"></i>Auditoría</a>
      <a href="logout.php" class="text-danger"><i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión</a>
    </nav>

    <!-- Contenido principal -->
    <main class="col-12 col-md-10 main-content p-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="bi bi-calendar-check me-2"></i>Gestión de Turnos</h2>
        <a href="turno_nuevo_admin.php" class="btn btn-success"><i class="bi bi-plus-circle me-1"></i>Nuevo turno</a>
      </div>

      <div class="table-responsive">
        <table class="table table-bordered table-hover shadow-sm">
          <thead class="table-light">
            <tr>
              <th>ID</th>
              <th>Cliente</th>
              <th>Servicio</th>
              <th>Fecha</th>
              <th>Hora</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($turnos)): ?>
              <?php foreach ($turnos as $turno): ?>
                <tr>
                  <td><?= $turno['id'] ?></td>
                  <td><?= htmlspecialchars($turno['cliente']) ?></td>
                  <td><?= htmlspecialchars($turno['servicio']) ?></td>
                  <td><?= $turno['fecha'] ?></td>
                  <td><?= $turno['hora'] ?></td>
                  <td><?= ucfirst($turno['estado']) ?></td>
                  <td>
                    <a href="turno_editar.php?id=<?= $turno['id'] ?>" class="btn btn-sm btn-warning">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <a href="turno_cancelar.php?id=<?= $turno['id'] ?>" class="btn btn-sm btn-danger"
                       onclick="return confirm('¿Deseás cancelar este turno?')">
                      <i class="bi bi-x-circle"></i>
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="7" class="text-center">No hay turnos registrados.</td></tr>
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
