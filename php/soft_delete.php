<?php
// ==========================================================================
// ARCHIVO: INACTIVACIÓN / ACTIVACIÓN LÓGICA DE USUARIOS (SOFT DELETE)
// ==========================================================================

// Incluye la seguridad y la conexión a la db
require_once("auth.php");
require_once("db.php");

// CAPTURA DE DATOS DESDE LA URL (MÉTODO GET)
$uuid = $_GET['uuid'] ?? '';
$status = $_GET['status'] ?? '';

// Define a dónde debe regresar el navegador siempre (Éxito o Fallo)
$redirect = basename($_GET["redirect"] ?? "");

$allowedRedirects = [
    "admin_patients.php",
    "admin_psychologists.php"
];

if (!in_array($redirect, $allowedRedirects)) {
    $redirect = "admin_patients.php";
}

$fallbackUrl = "../pages/" . $redirect;

// Si los parámetros no son válidos, regresa a la página sin romper el flujo con un die()
if (empty($uuid) || !in_array($status, ['activo', 'inactivo'])) {
    header("Location: " . $fallbackUrl);
    exit;
}

// EJECUCIÓN DE LA ACTUALIZACIÓN
try {
    $sql = "
        UPDATE credential c
        INNER JOIN user_profile up 
            ON c.uuid_credential = up.uuid_credential
        SET c.status = ?
        WHERE up.uuid_user_profile = ?
    ";

    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("ss", $status, $uuid);
        $stmt->execute();
        $stmt->close();
    }

} catch (Exception $e) {

}

// RETORNO AUTOMÁTICO A LA MISMA PÁGINA
header("Location: " . $fallbackUrl);
exit;