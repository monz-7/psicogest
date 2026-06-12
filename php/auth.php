<?php
// ==========================================================================
// ARCHIVO: AUTENTICACIÓN Y AUTORIZACIÓN DE VISUALIZACIÓN 
// ==========================================================================

// Inicia una nueva sesión o reanuda una sesión existente
session_start();

// Verifica si existe una sesión activa del usuario
if (!isset($_SESSION["uuid_credential"])) {
    // Si no hay sesión, redirige al login
    header("Location: ../pages/login.php");
    // Detiene la ejecución del script
    exit();
}