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
        ORDER BY t.fecha DESC, t.hora";

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
  <style>
    th.sortable:hover { cursor: pointer; text-decoration: underline; }
    .control-sm input.form-control {
      height: 32px;
      font-size: 0.875rem;
    }
    .control-sm .btn {
      font-size: 0.875rem;
      padding: 6px 10px;
    }
  </style>
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
      <a href="logout.php" class="text-danger"><i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión</a>
    </nav>

    <!-- Contenido principal -->
    <main class="col-12 col-md-10 main-content p-4">
      <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <h2 class="mb-2 mb-md-0"><i class="bi bi-calendar-check me-2"></i>Gestión de Turnos</h2>
        <div class="d-flex flex-wrap gap-2 control-sm">
          <input id="searchInput" type="text" class="form-control" placeholder="Buscar turno...">
          <a href="turno_nuevo_admin.php" class="btn btn-success"><i class="bi bi-plus-circle me-1"></i>Nuevo turno</a>
          <button onclick="window.print()" class="btn btn-outline-primary"><i class="bi bi-printer"></i> Imprimir</button>
        </div>
      </div>

      <a href="eliminar_turnos_cancelados.php" class="btn btn-danger mb-3"
         onclick="return confirm('¿Estás seguro de eliminar TODOS los turnos cancelados?')">
        <i class="bi bi-trash3"></i> Eliminar turnos cancelados
      </a>

      <div class="table-responsive">
        <table id="turnosTable" class="table table-bordered table-hover shadow-sm">
          <thead class="table-light">
            <tr>
              <th>ID</th>
              <th>Cliente</th>
              <th>Servicio</th>
              <th class="sortable">Fecha <i class="bi bi-arrow-down-up"></i></th>
              <th>Hora</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody id="turnosBody">
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
          </tbody>
        </table>
      </div>
    </main>
  </div>
</div>

<script>
// Filtro por texto
const searchInput = document.getElementById("searchInput");
searchInput.addEventListener("keyup", function () {
  const filter = this.value.toLowerCase();
  const rows = document.querySelectorAll("#turnosBody tr");
  rows.forEach(row => {
    const text = row.innerText.toLowerCase();
    row.style.display = text.includes(filter) ? "" : "none";
  });
});

// Ordenamiento solo por fecha
const fechaHeader = document.querySelector("th.sortable");
let sortAsc = true;
fechaHeader.addEventListener("click", () => {
  const rows = Array.from(document.querySelectorAll("#turnosBody tr"));
  rows.sort((a, b) => {
    const fechaA = a.cells[3].innerText;
    const fechaB = b.cells[3].innerText;
    return sortAsc ? fechaA.localeCompare(fechaB) : fechaB.localeCompare(fechaA);
  });
  const tbody = document.getElementById("turnosBody");
  rows.forEach(row => tbody.appendChild(row));
  sortAsc = !sortAsc;
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
