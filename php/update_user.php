<?php
// ==========================================================================
//  ARCHIVO: PROCESAMIENTO CRUD DE ACTUALIZACIÓN DE USUARIOS
// ==========================================================================

// Incluye el archivo de conexión a la base de datos
require_once("db.php");

header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 0);

// ==========================================================================
// RECEPCIÓN Y VALIDACIÓN DE DATOS DE ENTRADA
// ==========================================================================

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data["uuid_user_profile"])) {
    echo json_encode([
        "success" => false, 
        "message" => "Datos inválidos o incompletos."
    ]);
    exit;
}

$userType = $data["user_type"] ?? "";

if (!in_array($userType, ["patient", "psychologist"])) {
    echo json_encode([
        "success" => false,
        "message" => "Tipo de usuario inválido."
    ]);
    exit;
}

// ==========================================================================
// INICIO DE LA TRANSACCIÓN DE BASE DE DATOS
// ==========================================================================

$conn->begin_transaction();

try {
    // ----------------------------------------------------------------------
    // Obtener UUID de la credential asociada
    // ----------------------------------------------------------------------
    $get = $conn->prepare("
        SELECT uuid_credential 
        FROM user_profile 
        WHERE uuid_user_profile = ?
    ");
    
    if (!$get) {
        throw new Exception(
            "Error al preparar SELECT credential: " . $conn->error
        );
    }
    
    $get->bind_param(
        "s", 
        $data["uuid_user_profile"]
    );

    $get->execute();

    $res = $get->get_result()->fetch_assoc();
    $get->close(); // Liberación del buffer de lectura

    if (!$res) {
        throw new Exception(
            "Usuario no encontrado en el sistema."
        );
    }

    $uuidCredential = $res["uuid_credential"];

    // ----------------------------------------------------------------------
    // Generación automática de username (tipo + número de documento)
    // ----------------------------------------------------------------------
    $username = strtolower($data["doc_type"]) . $data["doc_number"];

    // ==========================================================================
    // PREPARACIÓN DE DECLARACIONES DE ACTUALIZACIÓN
    // ==========================================================================

    $stmt = $conn->prepare("
        UPDATE user_profile 
        SET 
            names = ?, 
            surnames = ?, 
            doc_type = ?, 
            doc_number = ?, 
            phone_number = ?
        WHERE uuid_user_profile = ?
    ");

    if (!$stmt) throw new Exception(
        "Error al preparar UPDATE user_profile."
    );

    $stmt->bind_param(
        "ssssss",
        $data["names"],
        $data["surnames"],
        $data["doc_type"],
        $data["doc_number"],
        $data["phone_number"],
        $data["uuid_user_profile"]
    );

    // Actualización de credenciales de acceso
    $stmt2 = $conn->prepare("
        UPDATE credential 
        SET 
            username = ?, 
            email = ? 
        WHERE uuid_credential = ?
    ");

    if (!$stmt2) throw new Exception(
        "Error al preparar UPDATE credential."
    );

    $stmt2->bind_param(
        "sss",
        $username,
        $data["email"],
        $uuidCredential
    );

    // ======================================================================
    // UPDATE ESPECÍFICO DEL ROL
    // ======================================================================

    $stmt3 = null;
    $ok3 = true;

    // ----------------------------------------------------------------------
    // SI EL USUARIO A ACTUALIZAR ES PACIENTE
    // ----------------------------------------------------------------------

    if ($userType === "patient") {

        $getPatient = $conn->prepare("
            SELECT uuid_patient
            FROM patient
            WHERE uuid_user_profile = ?
        ");

        if (!$getPatient) {
            throw new Exception(
                "Error al preparar SELECT patient."
            );
        }

        $getPatient->bind_param(
            "s",
            $data["uuid_user_profile"]
        );

        $getPatient->execute();

        $patientRes = $getPatient
            ->get_result()
            ->fetch_assoc();

        $getPatient->close();

        if (!$patientRes) {
            throw new Exception(
                "Paciente no encontrado."
            );
        }

        $uuidPatient = $patientRes["uuid_patient"];

        $stmt3 = $conn->prepare("
            UPDATE patient
            SET birth_date = ?
            WHERE uuid_patient = ?
        ");

        if (!$stmt3) {
            throw new Exception(
                "Error al preparar UPDATE patient."
            );
        }

        $stmt3->bind_param(
            "ss",
            $data["birth_date"],
            $uuidPatient
        );
    }

    // ----------------------------------------------------------------------
    // SI EL USUARIO A ACTUALIZAR ES PSICÓLOGO
    // ----------------------------------------------------------------------

    if ($userType === "psychologist") {

        $getPsychologist = $conn->prepare("
            SELECT uuid_psychologist
            FROM psychologist
            WHERE uuid_user_profile = ?
        ");

        if (!$getPsychologist) {
            throw new Exception(
                "Error al preparar SELECT psychologist."
            );
        }

        $getPsychologist->bind_param(
            "s",
            $data["uuid_user_profile"]
        );

        $getPsychologist->execute();

        $psychologistRes = $getPsychologist
            ->get_result()
            ->fetch_assoc();

        $getPsychologist->close();

        if (!$psychologistRes) {
            throw new Exception(
                "Psicólogo no encontrado."
            );
        }

        $uuidPsychologist =
            $psychologistRes["uuid_psychologist"];

        $stmt3 = $conn->prepare("
            UPDATE psychologist
            SET
                license_number = ?
            WHERE uuid_psychologist = ?
        ");

        if (!$stmt3) {
            throw new Exception(
                "Error al preparar UPDATE psychologist."
            );
        }

        $stmt3->bind_param(
            "ss",
            $data["license_number"],
            $uuidPsychologist
        );
    }


    // ==========================================================================
    // EJECUCIÓN Y CONFIRMACIÓN DE LA TRANSACCIÓN
    // ==========================================================================

    $ok1 = $stmt->execute();
    $ok2 = $stmt2->execute();

    if ($stmt3) {
        $ok3 = $stmt3->execute();
    }

    // Si todas las ejecuciones son exitosas, consolida los cambios
    if ($ok1 && $ok2 && $ok3) {
        $conn->commit();
        
        echo json_encode([
            "success" => true,
            "user_profile_updated" => $ok1,
            "credential_updated" => $ok2,
            "entity_updated" => $ok3,
            "affected_rows_user_profile" => $stmt->affected_rows,
            "affected_rows_credential" => $stmt2->affected_rows,
            "affected_rows_entity" => $stmt3 ? $stmt3->affected_rows : 0
        ]);
    } else {
        // Falló la ejecución de alguna sentencia
        throw new Exception(
            "Error durante la actualización.
        ");
    }

    // Cierre de descriptores de comandos
    $stmt->close();
    $stmt2->close();


    if ($stmt3) {
        $stmt3->close();
    }

} catch (Exception $e) {
    // Ante cualquier excepción o corte detectado, se deshacen todos los cambios de inmediato
    $conn->rollback();

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage(),
        "error_user_profile" => isset($stmt) ? $stmt->error : null,
        "error_credential" => isset($stmt2) ? $stmt2->error : null,
        "error_entity" => isset($stmt3) && $stmt3 ? $stmt3->error : null
    ]);
}