<?php
require_once __DIR__ . '/config.php';

$servicios_nuevos = [
    "Masajes anti-stress",
    "Masajes descontracturantes",
    "Masajes con piedras calientes",
    "Masajes circulatorios",
    "Lifting de pestaña",
    "Depilación facial",
    "Belleza de manos y pies",
    "Punta de diamante",
    "Limpieza profunda + hidratación",
    "Criofrecuencia facial",
    "VelaSlim",
    "DermoHealth",
    "Criofrecuencia corporal",
    "Ultracavitación",
    "Hidromasajes",
    "Yoga"
];

$agregados = 0;
foreach ($servicios_nuevos as $nombre) {
    $nombre = trim($nombre);
    $stmt = $conexion->prepare("SELECT id FROM servicios WHERE LOWER(nombre) = LOWER(?)");
    $stmt->bind_param("s", $nombre);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows === 0) {
        $insert = $conexion->prepare("INSERT INTO servicios (nombre) VALUES (?)");
        $insert->bind_param("s", $nombre);
        if ($insert->execute()) {
            $agregados++;
        }
    }
}

echo "Se agregaron $agregados servicios nuevos.";
?>
