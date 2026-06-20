<?php

// ==========================================================================
// ARCHIVO: ENDPOINT PARA CAMBIO DE CONTRASEÑA
// ==========================================================================

header('Content-Type: application/json');

set_exception_handler(function ($e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Error interno: ' . $e->getMessage()]);
    exit;
});

set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

// Middleawares de autorización y permisos
require_once("auth.php");
require_once("permissions.php");
// Middleawares de conexión a la db
require_once("../config/db.php");

// Roles requeridos para el endpoint
requireRoles(["admin", "paciente", "psicologo"]);

// Valida el método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit;
}

// Verifica la sesión activa
$user_uuid = $_SESSION["uuid_user_profile"] ?? "";
if (empty($user_uuid)) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Sesión inválida.']);
    exit;
}

// Lee y decodifica el JSON enviado desde el fetch
$input = json_decode(file_get_contents('php://input'), true);

$current_password = trim($input['current_password'] ?? '');
$new_password     = trim($input['new_password']     ?? '');
$repeat_password  = trim($input['repeat_password']  ?? '');

// VALIDACIONES

// Campos vacíos
if (empty($current_password) || empty($new_password) || empty($repeat_password)) {
    $fields = [];
    if (empty($current_password)) $fields[] = 'current_password';
    if (empty($new_password)) $fields[] = 'new_password';
    if (empty($repeat_password)) $fields[] = 'repeat_password';
    echo json_encode([
        'success' => false, 
        'message' => 'Todos los campos son obligatorios.',
        'error_fields' => $fields
    ]);
    exit;
}

// Nuevas contraseñas no coinciden
if ($new_password !== $repeat_password) {
    echo json_encode([
        'success' => false, 
        'message' => 'Las contraseñas nuevas no coinciden.',
        'error_fields' => ['new_password', 'repeat_password']
    ]);
    exit;
}

// Requisitos de seguridad: mín. 8 chars, mayúscula, minúscula, número y carácter especial (@, #, $, *)
if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#$*]).{8,}$/', $new_password)) {
    echo json_encode([
        'success' => false, 
        'message' => 'La nueva contraseña no cumple los requisitos de seguridad.',
        'error_fields' => ['new_password']
    ]);
    exit;
}

// OBTIENE EL HASH ACTUAL DE LA BD
$sql = "
    SELECT c.password
    FROM credential c
    INNER JOIN user_profile up ON c.uuid_credential = up.uuid_credential
    WHERE up.uuid_user_profile = ?
    LIMIT 1
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        'success' => false, 
        'message' => 'Error interno del servidor.'
    ]);
    exit;
}

$stmt->bind_param(
    "s", 
    $user_uuid
);

$stmt->execute();

$result = $stmt->get_result();

$row = $result->fetch_assoc();

$stmt->close();

// Verifica que el usuario exista
if (!$row) {
    echo json_encode([
        'success' => false, 
        'message' => 'Usuario no encontrado.'
    ]);
    exit;
}

// Verifica que la contraseña actual sea correcta
if (!password_verify($current_password, $row['password'])) {
    echo json_encode([
        'success' => false, 
        'message' => 'La contraseña actual es incorrecta.'
    ]);
    exit;
}

// Verifica que la nueva contraseña sea diferente a la actual
if (password_verify($new_password, $row['password'])) {
    echo json_encode([
        'success' => false, 
        'message' => 'La nueva contraseña debe ser diferente a la actual.'
    ]);
    exit;
}

// ACTUALIZA LA CONTRASEÑA

// Encriptar contraseña
$new_hash = password_hash($new_password, PASSWORD_DEFAULT);

$sql_update = "
    UPDATE credential c
    INNER JOIN user_profile up ON c.uuid_credential = up.uuid_credential
    SET c.password = ?
    WHERE up.uuid_user_profile = ?
";

$stmt_update = $conn->prepare($sql_update);

if (!$stmt_update) {
    echo json_encode([
        'success' => false, 
        'message' => 'Error al preparar la actualización.']);
    exit;
}

$stmt_update->bind_param(
    "ss", 
    $new_hash, 
    $user_uuid
);

if ($stmt_update->execute()) {
    echo json_encode([
        'success' => true, 
        'message' => 'Contraseña actualizada correctamente.'
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Error al guardar los cambios. Inténtalo de nuevo.'
    ]);
}

$stmt_update->close();
?>