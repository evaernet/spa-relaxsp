<?php
// guardar_registro.php

require_once __DIR__ . '/config.php';

$nombre   = trim($_POST['nombre']   ?? '');
$email    = trim($_POST['email']    ?? '');
$password = $_POST['password']      ?? '';
$rol      = $_POST['rol']           ?? 'cliente';

if (!$nombre || !$email || !$password) {
    header("Location: registro.html?error=faltan_datos");
    exit;
}

// Verifico que no exista ya el usuario
$stmt = $conexion->prepare("SELECT id FROM usuarios WHERE usuario = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows > 0) {
    header("Location: registro.html?error=email_ya_registrado");
    exit;
}

// Guardar contraseña sin hash (texto plano)
$stmt = $conexion->prepare("INSERT INTO usuarios (usuario, password, rol) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $email, $password, $rol);
$stmt->execute();
$idUsuario = $stmt->insert_id;

// Si es cliente, también lo guardo en la tabla clientes
if ($rol === 'cliente') {
    $telefono = '';
    $stmt = $conexion->prepare(
        "INSERT INTO clientes (nombre, email, telefono, usuario_id)
         VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param("sssi", $nombre, $email, $telefono, $idUsuario);
    $stmt->execute();
}

header("Location: login.php?registro=ok");
exit;
