<?php

// ==========================================================================
// ARCHIVO: INSERTAR NUEVO PACIENTE (REGISTRO)
// ==========================================================================

header('Content-Type: application/json');

// Middleware de conexión con la base de datos
require_once '../config/db.php'; 

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
$email = trim($_POST['email-input'] ?? '');
$password = trim($_POST['password-input'] ?? '');
$names = trim($_POST['names'] ?? '');
$surnames = trim($_POST['surnames'] ?? '');
$docType = trim($_POST['doc-type'] ?? '');
$docNumber = trim($_POST['doc-number'] ?? '');
$phoneNumber = trim($_POST['phone-full'] ?? ''); // ← Viene del hidden #phone-full
$locationRadio = trim($_POST['location'] ?? ''); //
$country = trim($_POST['country'] ?? '');
$birthDateRaw = trim($_POST['birth-date'] ?? ''); //

// Si eligió Colombia, asignar el país manualmente
if ($locationRadio === 'colombia' || empty($country)) {
    $country = 'Colombia';
}

// Convertir formato "DD / MM / AAAA" a "AAAA-MM-DD" para MySQL
$birthDate = '';
if (!empty($birthDateRaw)) {
    // Remover espacios extras si los hay
    $cleanDate = str_replace(' ', '', $birthDateRaw); // Queda "DD/MM/AAAA"
    $parts = explode('/', $cleanDate);
    if (count($parts) === 3) {
        $birthDate = $parts[2] . '-' . $parts[1] . '-' . $parts[0]; // "AAAA-MM-DD"
    }
}

// Valida que la contraseña ingresada tenga los caracteres y elementos necesarios
$passwordRegex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&._\-\/#])[A-Za-z\d@$!%*?&._\-\/#]{8,}$/';

if (!preg_match($passwordRegex, $password)) {
    echo json_encode([
        'success' => false,
        'message' => 'La contraseña no cumple con los requisitos mínimos de seguridad.'
    ]);
    exit;
}

// GENERAR CREDENCIALES

// Username
$username = $docType . $docNumber;

// Password (hash no texto plano)
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// VALORES POR DEFECTO
$role = "paciente";
$status = "activo";

// GENERAR UUIDs
$uuidCredential = generateUUID();
$uuidUserProfile = generateUUID();
$uuidPatient = generateUUID();

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

    // Insertar en patient
    $stmt = $conn->prepare("
        INSERT INTO patient (
            uuid_patient, 
            uuid_user_profile, 
            birth_date,
            country
        ) 
        VALUES (?, ?, ?, ?)"
    );

    // Parámetros
    $stmt->bind_param(
        "ssss", 
        $uuidPatient,
        $uuidUserProfile,
        $birthDate,
        $country
    );

    // Ejecuta
    $stmt->execute();

    // Termina la transacción
    $conn->commit();

    // Respuesta que se envía si hay éxito
    echo json_encode([
        'success' => true,
        'credentials' => [
            'username' => $username,
            'email' => $email,
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