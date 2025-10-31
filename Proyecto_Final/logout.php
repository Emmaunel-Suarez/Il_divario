<?php
session_start();
session_unset();    // limpiar variables de sesión
session_destroy();  // destruir la sesión

// redirigir al inicio
header("Location: 1.php");
exit;
?>
