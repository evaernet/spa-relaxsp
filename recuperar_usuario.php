
<?php
// Configuración de remitente
$remitente = "no-responder@relaxsp.com";  // Cambiar si se usa servidor real

// Verificamos si llegó el correo
if (!isset($_POST['email']) || empty($_POST['email'])) {
    die("Debes ingresar un correo electrónico.");
}

$email = $_POST['email'];
$archivo = 'usuarios.json';

if (!file_exists($archivo)) {
    die("No hay registros disponibles.");
}

$contenido = file_get_contents($archivo);
$usuarios = json_decode($contenido, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die("Error al leer el archivo de usuarios.");
}

// Buscar usuario
$usuarioEncontrado = null;
foreach ($usuarios as $usuario) {
    if (isset($usuario['email']) && $usuario['email'] === $email) {
        $usuarioEncontrado = $usuario;
        break;
    }
}

if (!$usuarioEncontrado) {
    echo "No se encontró ningún usuario con ese correo.";
    exit;
}

// Enviar correo (nota: aquí solo simulamos el envío)
$asunto = "Recuperación de cuenta - RelaxSp";
$mensaje = "Hola " . $usuarioEncontrado['nombre'] . ",\n\n";
$mensaje .= "Recibimos una solicitud para recuperar tu cuenta.\n";
$mensaje .= "Tu usuario registrado es: " . $usuarioEncontrado['email'] . "\n";
$mensaje .= "Por razones de seguridad, no podemos enviarte tu contraseña.\n";
$mensaje .= "Si no hiciste esta solicitud, podés ignorar este mensaje.\n\n";
$mensaje .= "Equipo RelaxSp";

// En entorno real se debe configurar correctamente el servidor de correo
// mail($email, $asunto, $mensaje, "From:" . $remitente);

echo "<h2>Correo enviado</h2><p>Se ha enviado la información de recuperación a: $email</p><p><a href='login.php'>Volver a ingresar</a></p>";
?>
