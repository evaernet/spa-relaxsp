<?php
// guardar_registro.php

// 1) Conexión centralizada
require_once __DIR__ . '/config.php';

// 2) Obtengo datos y limpio espacios
$nombre   = trim($_POST['nombre']   ?? '');
$email    = trim($_POST['email']    ?? '');
$password = $_POST['password']      ?? '';
$rol      = $_POST['rol']           ?? 'cliente';

// 3) Validación mínima
if (!$nombre || !$email || !$password) {
    header("Location: registro.html?error=faltan_datos");
    exit;
}

// 4) Verifico que no exista ya el usuario
$stmt = $conexion->prepare("SELECT id FROM usuarios WHERE usuario = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows > 0) {
    header("Location: registro.html?error=email_ya_registrado");
    exit;
}

// 5) Hasheo la contraseña
$hash = password_hash($password, PASSWORD_DEFAULT);

// 6) Inserto en usuarios
$stmt = $conexion->prepare(
    "INSERT INTO usuarios (usuario, password, rol) VALUES (?, ?, ?)"
);
$stmt->bind_param("sss", $email, $hash, $rol);
$stmt->execute();
$idUsuario = $stmt->insert_id;

// 7) Si es cliente, inserto también en la tabla clientes
if ($rol === 'cliente') {
    $telefono = '';  // o recoge otro campo si lo tienes
    $stmt = $conexion->prepare(
        "INSERT INTO clientes (nombre, email, telefono, usuario_id)
         VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param("sssi", $nombre, $email, $telefono, $idUsuario);
    $stmt->execute();
}

// 8) Redirijo al login con flag de éxito
header("Location: login.php?registro=ok");
exit;
