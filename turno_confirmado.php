<?php
require_once __DIR__ . '/config.php';

$idTurno = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$idTurno) {
    echo "Turno no encontrado.";
    exit;
}

// Obtener datos del turno
$sql = "
  SELECT t.*, s.nombre AS servicio, e.nombre AS empleado, c.nombre AS cliente
  FROM turnos t
  JOIN servicios s ON t.id_servicio = s.id
  JOIN empleados e ON t.id_empleado = e.id
  JOIN clientes c ON t.id_cliente = c.id
  WHERE t.id = ?
";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $idTurno);
$stmt->execute();
$res = $stmt->get_result();
$turno = $res->fetch_assoc();

if (!$turno) {
    echo "Turno no encontrado.";
    exit;
}

// Generar código QR
include 'phpqrcode/qrlib.php';
$qrData = "Turno ID: {$turno['id']} | Cliente: {$turno['cliente']} | Servicio: {$turno['servicio']} | Fecha: {$turno['fecha']} {$turno['hora']}";
$qrFile = "qr_{$turno['id']}.png";
QRcode::png($qrData, $qrFile);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Turno Confirmado - RelaxSp</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    @media print {
      .no-print {
        display: none;
      }
    }
  </style>
</head>
<body class="bg-light">
<div class="container py-5">
  <div class="card shadow-sm p-4 text-center">
    <h2 class="mb-4 text-success">✅ ¡Turno confirmado!</h2>
    <p class="fs-5"><strong>Cliente:</strong> <?= htmlspecialchars($turno['cliente']) ?></p>
    <p class="fs-5"><strong>Servicio:</strong> <?= htmlspecialchars($turno['servicio']) ?></p>
    <p class="fs-5"><strong>Empleado:</strong> <?= htmlspecialchars($turno['empleado']) ?></p>
    <p class="fs-5"><strong>Fecha:</strong> <?= htmlspecialchars($turno['fecha']) ?></p>
    <p class="fs-5"><strong>Hora:</strong> <?= htmlspecialchars($turno['hora']) ?></p>

    <div class="my-4">
      <p><strong>Código QR del turno:</strong></p>
      <img src="<?= htmlspecialchars($qrFile) ?>" alt="Código QR del turno" class="img-fluid" style="max-width: 200px;">
    </div>

    <div class="no-print mt-4 d-flex justify-content-center gap-3">
      <a href="panel_cliente.php" class="btn btn-outline-secondary">Volver al panel</a>
      <button onclick="window.print()" class="btn btn-primary">Imprimir comprobante</button>
    </div>
  </div>
</div>
</body>
</html>
