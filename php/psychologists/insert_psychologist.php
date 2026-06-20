<?php

// ==========================================================================
// ARCHIVO: INSERTAR NUEVO PSICÓLOGO 
// ==========================================================================

header('Content-Type: application/json');

// Middleware de autorización y permisos
require_once("../auth/auth.php");
require_once("../auth/permissions.php");

// Middleware de conexión con la base de datos
require_once '../config/db.php';

requireRole("admin");

// Valida el método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false, 
        'message' => 'Método no permitido.']);
    exit;
}

// Función para generar UUIDs
function generateUUID() {
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff)
    );
}

// Captura de datos
$docType = trim($_POST['doc-type'] ?? '');
$docNumber = trim($_POST['doc-number'] ?? '');
$licenseNumber = trim($_POST['license-number'] ?? '');
$names = trim($_POST['names'] ?? '');
$surnames = trim($_POST['surnames'] ?? '');
$phoneNumber = trim($_POST['phone-full'] ?? ''); // ← Viene del hidden #phone-full
$email = trim($_POST['email-input'] ?? '');
$specialtiesJson = $_POST['specialties'] ?? '[]'; // JSON 

// Validaciones básicas
if (empty($docType) || empty($docNumber) || empty($licenseNumber) || 
    empty($names) || empty($surnames) || empty($email) || empty($phoneNumber)) {
    echo json_encode([
        'success' => false, 
        'message' => 'Todos los campos obligatorios son requeridos.']);
    exit;
}

// GENERAR CREDENCIALES

// Username
$username = $docType . $docNumber;

// Password
// Genera una contraseña temporal random
$plainPassword = bin2hex(random_bytes(4));
// Hash de la contraseã, no texto plano
$hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

// VALORES POR DEFECTO
$role = "psicologo";
$status = "activo";

$sessionDuration = 45;

// GENERAR UUIDs
$uuidCredential = generateUUID();
$uuidUserProfile = generateUUID();
$uuidPsychologist = generateUUID();

// VALIDAR DUPLICADOS

// Correo
$stmt = $conn->prepare("
    SELECT uuid_credential
    FROM credential
    WHERE email = ?
");
$stmt->bind_param("s", $email);
$stmt->execute();

if ($stmt->get_result()->num_rows > 0) {
    echo json_encode([
        'success' => false,
        'message' => 'El correo ya existe.'
    ]);
    exit;
}

// Documento
$stmt = $conn->prepare("
    SELECT uuid_user_profile
    FROM user_profile
    WHERE doc_number = ?
");
$stmt->bind_param("s", $docNumber);
$stmt->execute();

if ($stmt->get_result()->num_rows > 0) {
    echo json_encode([
        'success' => false,
        'message' => 'El documento ya existe.'
    ]);
    exit;
}

// INICIO DE LA TRANSACCIÓN 

$conn->begin_transaction();

try {
    // Insertar en credential
    $stmt = $conn->prepare("
    INSERT INTO credential (
            uuid_credential, 
            username,
            email, 
            password, 
            role, 
            status
        ) 
        VALUES (?, ?, ?, ?, ?, ?)
        
    ");

    // Parámetros
    $stmt->bind_param(
        "ssssss", 
        $uuidCredential,
        $username,
        $email, 
        $hashedPassword,
        $role,
        $status
    );

    // Ejecuta
    $stmt->execute();
    
    // Insertar en user_profile
    $stmt = $conn->prepare("
        INSERT INTO user_profile (
            uuid_user_profile, 
            uuid_credential, 
            doc_type, 
            doc_number, 
            names, 
            surnames, 
            phone_number
        ) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    // Parámetros
    $stmt->bind_param(
        "sssssss", 
        $uuidUserProfile, 
        $uuidCredential, 
        $docType, 
        $docNumber, 
        $names, 
        $surnames, 
        $phoneNumber
    );

    // Ejecuta
    $stmt->execute();

    // Insertar en psychologist
    $stmt = $conn->prepare("
        INSERT INTO psychologist (
            uuid_psychologist, 
            uuid_user_profile, 
            license_number, 
            session_duration
        ) 
        VALUES (?, ?, ?, ?)"
    );

    // Parámetros
    $stmt->bind_param(
        "ssss", 
        $uuidPsychologist,
        $uuidUserProfile,
        $licenseNumber, 
        $sessionDuration
    );

    // Ejecuta
    $stmt->execute();

    // Insertar especialidades

    // Manejo correcto del JSON del frontend
    $specialties = json_decode($specialtiesJson, true);
    if (json_last_error() === JSON_ERROR_NONE && !empty($specialties)) {

        // Insertar en psychologist_specialty
        $stmt = $conn->prepare("
            INSERT INTO psychologist_specialty (
                uuid_psychologist, 
                uuid_specialty
            ) 
            VALUES (?, ?)
        ");

        foreach ($specialties as $specialtyUuid) {
            if (!empty($specialtyUuid)) {
                // Parámetros
                $stmt->bind_param(
                    "ss", 
                    $uuidPsychologist, 
                    $specialtyUuid
                );

                // Ejecuta
                $stmt->execute();
            }
        }
    }

    // Termina la transacción
    $conn->commit();

    // Respuesta que se envía si hay éxito
    echo json_encode([
        'success' => true,
        'credentials' => [
            'username' => $username,
            'email' => $email,
            'password' => $plainPassword
        ],
        'psychologist' => [
            'names' => $names,
            'surnames' => $surnames
        ]
    ]);

} catch (Exception $e) {
    // Hace rollback a la transacción
    $conn->rollback();
    // Respuesta que se envía si NO hay éxito
    echo json_encode([
        'success' => false, 
        'message' => 'Error al registrar: ' . $e->getMessage()
    ]);
}
?>