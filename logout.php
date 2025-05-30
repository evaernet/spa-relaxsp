<?php
session_start();
session_unset();
session_destroy();
// Evito cache para que Atrás no recupere la sesión destruida
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');
// Vuelvo al home público
header("Location: index.php");
exit;
