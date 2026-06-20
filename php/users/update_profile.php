<?php

// ==========================================================================
// ARCHIVO: PROCESADOR DE LA ACTUALIZACIÓN DE DATOS (PACIENTE Y PSICÓLOGO)
// ==========================================================================

header('Content-Type: application/json; charset=utf-8');

// Middleware de autorización y permisos
require_once("../auth/auth.php");
require_once("../auth/permissions.php");
// Middleware de la conexión a la db
require_once("../config/db.php");
// Middleware al helper para el selecctor del país
require_once("../helpers/utils.php");

// Roles requeridos para este endpoint
requireRoles(["paciente", "psicologo"]);

// COMPROBACIÓN DE LA SESIÓN
$user_uuid = $_SESSION["uuid_user_profile"] ?? "";
$role = $_SESSION["role"] ?? "";

if (empty($user_uuid)) {
    echo json_encode([
        "success" => false, 
        "message" => "Sesión inválida."]);
    exit;
}

// COMPROBACIÓN DE LOS DATOS
$inputData = json_decode(file_get_contents("php://input"), true);
if (!$inputData) {
    echo json_encode([
        "success" => false, 
        "message" => "No se suministraron datos válidos."]);
    exit;
}

// Captura de datos
$names = trim($inputData["names"] ?? "");
$surnames = trim($inputData["surnames"] ?? "");
$phone_number = trim($inputData["phone_number"] ?? "");
$email = trim($inputData["email"] ?? "");

// Campos condicionales
$country      = trim($inputData["country"] ?? ""); 
$birthdateRaw = trim($inputData["birthdate"] ?? ""); 
$specialties  = $inputData["specialties"] ?? []; // Array de UUIDs de especialidades enviado por el psicólogo

// Validaciones comunes
$errorFields = [];
if (empty($names)) $errorFields[] = "names";
if (empty($surnames)) $errorFields[] = "surnames";
if (empty($phone_number)) $errorFields[] = "phone_number";
if (empty($email)) $errorFields[] = "email";

// Validación específica para Paciente
if ($role === "paciente") {
    if (empty($country)) $errorFields[] = "country";
    if (empty($birthdateRaw)) $errorFields[] = "birth_date";
}

// Validación de campos vacíos
if (!empty($errorFields)) {
    echo json_encode([
        "success" => false, 
        "message" => "Existen campos requeridos sin diligenciar.", 
        "error_fields" => $errorFields]);
    exit;
}

$calculatedAge = null;
$dbBirthDate = null;

// Procesar fecha de nacimiento si es paciente
if ($role === "paciente") {
    $cleanDateStr = str_replace(' ', '', $birthdateRaw);
    $dateParts = explode('/', $cleanDateStr);

    if (count($dateParts) !== 3 || !checkdate((int)$dateParts[1], (int)$dateParts[0], (int)$dateParts[2])) {
        echo json_encode([
            "success" => false, 
            "message" => "La fecha de nacimiento no es válida.", 
            "error_fields" => ["birth_date"]]);
        exit;
    }

    $dbBirthDate = sprintf("%04d-%02d-%02d", $dateParts[2], $dateParts[1], $dateParts[0]);
    $birthYear = (int)$dateParts[2];
    $currentYear = (int)date('Y');
    if ($birthYear < 1920 || $birthYear > $currentYear) {
        echo json_encode([
            "success" => false, 
            "message" => "El año de nacimiento no es coherente.", 
            "error_fields" => ["birth_date"]]);
        exit;
    }

    $birthDateTime = new DateTime($dbBirthDate);
    $currentDateTime = new DateTime();
    $calculatedAge = $currentDateTime->diff($birthDateTime)->y;
}

// VALIDAR EMAIL DUPLICADO
$sqlCheckEmail = "
    SELECT c.uuid_credential 
    FROM credential c
    INNER JOIN user_profile up ON up.uuid_credential = c.uuid_credential
    WHERE c.email = ? AND up.uuid_user_profile != ? 
    LIMIT 1
";
$stmtCheck = $conn->prepare($sqlCheckEmail);
$stmtCheck->bind_param("ss", $email, $user_uuid);
$stmtCheck->execute();
$checkResult = $stmtCheck->get_result();
if ($checkResult->num_rows > 0) {
    echo json_encode([
        "success" => false, 
        "message" => "El correo electrónico ya se encuentra registrado por otro usuario.", 
        "error_fields" => ["email"]]);
    $stmtCheck->close();
    exit;
}
$stmtCheck->close();

// ACTUALIZACIÓN POR TRANSACCIÓN
$conn->begin_transaction();

try {
    // Actualizar user_profile 
    // (El país se actualiza si viene, de lo contrario guarda vacío o mantiene el que ya estaba)
    $sqlUpdateProfile = "
        UPDATE user_profile 
        SET 
            names = ?, 
            surnames = ?, 
            phone_number = ? 
        WHERE uuid_user_profile = ?
    ";

    $stmtUp = $conn->prepare($sqlUpdateProfile);

    $stmtUp->bind_param(
        "ssss", 
        $names, 
        $surnames, 
        $phone_number, 
        $user_uuid
    );

    $stmtUp->execute();

    $stmtUp->close();

    // Paciente
    if ($role === "paciente") {
        $sqlUpdatePatient = "
            UPDATE patient 
            SET 
                birth_date = ?, 
                country = ? 
            WHERE uuid_user_profile = ?
        ";

        $stmtPat = $conn->prepare($sqlUpdatePatient);

        $stmtPat->bind_param(
            "sss", 
            $dbBirthDate, 
            $country, 
            $user_uuid
        );

        $stmtPat->execute();

        $stmtPat->close();
    }

    // Psicólogo
    $specialtiesNamesString = "";

    if ($role === "psicologo") {
        // Obtener el uuid_psychologist correspondiente
        $sqlGetPsyUuid = "
            SELECT uuid_psychologist 
            FROM psychologist 
            WHERE uuid_user_profile = ? LIMIT 1
        ";

        $stmtPsy = $conn->prepare($sqlGetPsyUuid);

        $stmtPsy->bind_param(
            "s", 
            $user_uuid
        );

        $stmtPsy->execute();

        $resPsy = $stmtPsy->get_result()->fetch_assoc();

        $stmtPsy->close();

        if (!$resPsy) {
            throw new Exception("No se encontró el registro del psicólogo asociado.");
        }

        $uuid_psychologist = $resPsy['uuid_psychologist'];

        // Limpiar las especialidades anteriores asignadas
        $sqlDeleteSpecs = "
            DELETE FROM psychologist_specialty 
            WHERE uuid_psychologist = ?
        ";

        $stmtDelSpecs = $conn->prepare($sqlDeleteSpecs);

        $stmtDelSpecs->bind_param("s", $uuid_psychologist);

        $stmtDelSpecs->execute();

        $stmtDelSpecs->close();

        // Insertar las nuevas especialidades seleccionadas
        if (!empty($specialties) && is_array($specialties)) {
            $sqlInsertSpec = "
                INSERT INTO psychologist_specialty (
                    uuid_psychologist, 
                    uuid_specialty
                ) VALUES (?, ?)
            ";

            $stmtInsSpec = $conn->prepare($sqlInsertSpec);

            foreach ($specialties as $uuid_specialty) {

                $stmtInsSpec->bind_param(
                    "ss", 
                    $uuid_psychologist, 
                    $uuid_specialty
                );
                
                $stmtInsSpec->execute();
            }

            $stmtInsSpec->close();
        }

        // Obtener los nombres actualizados de las especialidades para devolverlos al front
        $sqlGetSpecsNames = "
            SELECT GROUP_CONCAT(s.name ORDER BY s.name ASC SEPARATOR ', ') AS specialties_list
            FROM psychologist_specialty ps
            INNER JOIN specialty s ON ps.uuid_specialty = s.uuid_specialty
            WHERE ps.uuid_psychologist = ?
            GROUP BY ps.uuid_psychologist
        ";

        $stmtNames = $conn->prepare($sqlGetSpecsNames);

        $stmtNames->bind_param("s", $uuid_psychologist);

        $stmtNames->execute();

        $resNames = $stmtNames->get_result()->fetch_assoc();

        $specialtiesNamesString = $resNames['specialties_list'] ?? "";

        $stmtNames->close();
    }

    // Actualizar credential
    $sqlUpdateCred = "
        UPDATE credential c
        INNER JOIN user_profile up ON up.uuid_credential = c.uuid_credential
        SET c.email = ?
        WHERE up.uuid_user_profile = ?
    ";

    $stmtCred = $conn->prepare($sqlUpdateCred);

    $stmtCred->bind_param("ss", $email, $user_uuid);

    $stmtCred->execute();

    $stmtCred->close();

    $conn->commit();

    $codePhone = getCountryCodeByPhone($phone_number);

    $codeCountry = !empty($country) ? getCountryCodeByName($country) : '';

    // Respuesta JSON
    $responseOutput = [
        "success" => true,
        "code_phone" => $codePhone
    ];

    if ($role === "paciente") {
        $responseOutput["age"] = $calculatedAge;
        $responseOutput["code_country"] = $codeCountry;
    }

    if ($role === "psicologo") {
        $responseOutput["specialties_names"] = $specialtiesNamesString;
    }

    echo json_encode($responseOutput);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode([
        "success" => false, 
        "message" => "Fallo al actualizar los datos en el sistema."
    ]);
}