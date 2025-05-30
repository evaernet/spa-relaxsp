<?php
// config.php — configuración centralizada de la conexión MySQL

// Detectamos si estamos en local o en el host remoto
if (in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1'])) {
    // === ENTORNO LOCAL (XAMPP) ===
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'relaxsp');
} else {
    // === ENTORNO REMOTO (InfinityFree) ===
    define('DB_HOST', 'sql101.infinityfree.com');
    define('DB_USER', 'if0_39061544');
    define('DB_PASS', 'spasentirsebien');
    define('DB_NAME', 'if0_39061544_relaxsp');
}

// Creamos la conexión
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Si falla, mostramos el error
if ($mysqli->connect_error) {
    die("Error de conexión MySQL: " . $mysqli->connect_error);
}

// Forzamos UTF-8
$mysqli->set_charset('utf8');

$conexion = $mysqli;
