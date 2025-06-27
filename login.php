<?php
session_start();
if (isset($_SESSION['rol'])) {
    header('Location: index.php');
    exit;
}

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Iniciar Sesión - RelaxSp</title>
  <link rel="stylesheet" href="styles/style.css">
  <style>
    .conteiner-login {
      width: 90%;
      max-width: 400px;
      margin: 0 auto;
      padding: 40px 20px;
    }
    .form-login {
      display: flex;
      flex-direction: column;
      gap: 15px;
      width: 100%;
    }
    .form-login input {
      width: 100%;
      box-sizing: border-box;
    }
    .boton-ingresar {
      width: 100%;
    }
  </style>
</head>
<body class="pagina-login">
  <div class="conteiner-login">
    <img src="image/logo.png" alt="RelaxSp logo" style="max-width:80px; margin-bottom:20px;">
    <h2 style="margin-bottom:30px; color:#9C7CC1;">Iniciar Sesión</h2>

    <?php if (!empty($_GET['error'])): ?>
      <div class="alert alert-danger" style="margin-bottom:20px;">
        Usuario o contraseña inválidos.
      </div>
    <?php elseif (!empty($_GET['registro']) && $_GET['registro'] === 'ok'): ?>
      <div class="alert alert-success" style="margin-bottom:20px;">
        ¡Registro exitoso! Ya podés iniciar sesión.
      </div>
    <?php endif; ?>

    <form action="verificar_login.php" method="POST" class="form-login">
      <input type="text" name="usuario" placeholder="Usuario" required>
      <input type="password" name="password" placeholder="Contraseña" required>
      <button type="submit" class="boton-ingresar">Enviar</button>
    </form>

    <div class="extra-text" style="margin-top:25px; font-size:0.9em;">
      <p>¿Olvidaste tu contraseña? <a href="recuperar.html">Recupérala acá</a></p>
      <p>¿No tenés cuenta? <a href="registro.html">Registrate acá</a></p>
    </div>
  </div>
</body>
</html>
