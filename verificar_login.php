<?php
session_start();
require_once __DIR__ . '/config.php';

$usuario  = trim($_POST['usuario']  ?? '');
$password = $_POST['password']       ?? '';

$stmt = $conexion->prepare("SELECT usuario, password, rol FROM usuarios WHERE usuario = ?");
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    
    if ($password === $user['password']) {
 
        $_SESSION['usuario'] = $user['usuario'];
        $_SESSION['rol']     = $user['rol'];
        header("Location: index.php");
        exit;
    }
}

header("Location: login.php?error=1");
exit;
