<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Conexión
require_once __DIR__ . '/config.php';


// 1) Turnos de hoy
$sqlTurnosHoy = "SELECT COUNT(*) AS total FROM turnos WHERE fecha = CURDATE()";
$res = $mysqli->query($sqlTurnosHoy);
$countTurnosHoy = $res->fetch_assoc()['total'] ?? 0;

// 2) Clientes activos
$sqlClientes = "SELECT COUNT(*) AS total FROM clientes";
$res = $mysqli->query($sqlClientes);
$countClientes = $res->fetch_assoc()['total'] ?? 0;

// 3) Servicios disponibles
$sqlServicios = "SELECT COUNT(*) AS total FROM servicios";
$res = $mysqli->query($sqlServicios);
$countServicios = $res->fetch_assoc()['total'] ?? 0;

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Panel de Administración - RelaxSp</title>
  <!-- Bootstrap + Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="styles/style.css">
</head>

<body class="admin-page">
  <div class="container-fluid">
    <div class="row">

      <!-- Menú lateral -->
      <nav class="col-12 col-md-2 barra-nav py-4 text-center">
        <img src="image/logo.png" alt="RelaxSp Logo" height="50">
        <h4 class="mt-2">RelaxSp</h4>
        <a href="panel_admin.php"><i class="bi bi-house-door me-2"></i>Inicio</a>
        <a href="admin_turnos.php"><i class="bi bi-calendar-check me-2"></i>Turnos</a>
        <a href="admin_servicios.php"><i class="bi bi-heart-pulse me-2"></i>Servicios</a>
        <a href="admin_clientes.php"><i class="bi bi-person-lines-fill me-2"></i>Clientes</a>
        <a href="admin_empleados.php"><i class="bi bi-person-workspace me-2"></i>Empleados</a>
        <a href="auditoria_turnos.php"><i class="bi bi-shield-check me-2"></i>Auditoría</a>
        <a href="logout.php" class="text-danger"><i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión</a>
      </nav>

      <!-- Contenido principal -->
      <main class="col-12 col-md-10 main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h2>Panel de Administración</h2>
          <a href="index.php" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left-circle me-1"></i>Volver al sitio
          </a>
        </div>

        <div class="row g-4">
          <!-- Tarjeta 1: Turnos de hoy -->
          <div class="col-md-4">
            <div class="card p-4 shadow-sm h-100">
              <h5><i class="bi bi-calendar-event me-2"></i>Turnos de hoy</h5>
              <p class="fs-3 mb-0"><?= $countTurnosHoy ?></p>
              <small class="text-muted">turnos programados para hoy</small>
            </div>
          </div>

          <!-- Tarjeta 2: Clientes activos -->
          <div class="col-md-4">
            <div class="card p-4 shadow-sm h-100">
              <h5><i class="bi bi-people-fill me-2"></i>Clientes activos</h5>
              <p class="fs-3 mb-0"><?= $countClientes ?></p>
              <small class="text-muted">clientes registrados</small>
            </div>
          </div>

          <!-- Tarjeta 3: Servicios disponibles -->
          <div class="col-md-4">
            <div class="card p-4 shadow-sm h-100">
              <h5><i class="bi bi-heart-pulse me-2"></i>Servicios disponibles</h5>
              <p class="fs-3 mb-0"><?= $countServicios ?></p>
              <small class="text-muted">tipos de servicio activos</small>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
