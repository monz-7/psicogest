<?php

// ==========================================================================
// ARCHIVO: CIERRE DE SESIÓN
// - Destruye de forma segura las variables de entorno del usuario 
// - Limpia la memoria
// ==========================================================================

// Inicia una nueva sesión o reanuda la sesión actual
session_start();
// Vacía todas las variables almacenadas en la sesión
$_SESSION = [];
// Destruye completamente la sesión del usuario
session_destroy();
// Redirige al usuario a la página de inicio de sesión
header("Location: ../pages/login.php");
// Detiene la ejecución del script
exit();