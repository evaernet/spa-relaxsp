<?php
// verificar_login.php
session_start();

// 1) Conexión centralizada
require_once __DIR__ . '/config.php';

// 2) Recibo form
$usuario  = trim($_POST['usuario']  ?? '');
$password = $_POST['password']       ?? '';

// 3) Busco al usuario
$stmt = $conexion->prepare(
    "SELECT usuario, password, rol FROM usuarios WHERE usuario = ?"
);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();

// 4) Verifico credenciales
if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    // password_verify en lugar de compararlo plano
    if ($password === $user['password']) {
        $_SESSION['usuario'] = $user['usuario'];
        $_SESSION['rol']     = $user['rol'];
        header("Location: index.php");
        exit;
    }
}

// 5) Si algo falla, vuelvo al login con flag de error
header("Location: login.php?error=1");
exit;
