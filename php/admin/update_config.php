
<?php
// ==========================================================================
// ARCHIVO: ACTUALIZA UN AJUSTE DE LA CONFIGURACIÓN GLOBAL (SOLO ADMIN)
// ==========================================================================

header("Content-Type: application/json; charset=UTF-8");

// Middleware de seguridad y permisos
require_once ("../auth/auth.php");
require_once ("../auth/permissions.php");
// Middleware de conexión a la base de datos
require_once ("../config/db.php");

// Restricción de acceso: solo ADMIN
requireRole("admin");

// Solo se acepta POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Método no permitido."
    ]);
    exit;
}

// Lee el JSON enviado por fetch
$data = json_decode(file_get_contents("php://input"), true);

if (!is_array($data)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Los datos recibidos no son válidos."
    ]);
    exit;
}

$item = $data["item"] ?? "";
$value = trim((string)($data["value"] ?? ""));

// Solo estos ajustes se pueden modificar
$allowedItems = ["appointment_price", "clinic_address"];

if (!in_array($item, $allowedItems, true)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Ajuste no válido."
    ]);
    exit;
}

// Validaciones según el ajuste
if ($value === "") {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "El campo no puede estar vacío."
    ]);
    exit;
}

if ($item === "appointment_price") {
    if (!ctype_digit($value) || (int)$value <= 0) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "La tarifa debe ser un número entero mayor a 0."
        ]);
        exit;
    }
}

if ($item === "clinic_address") {
    $length = mb_strlen($value, "UTF-8");

    if ($length < 5 || $length > 255) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "La dirección debe tener entre 5 y 255 caracteres."
        ]);
        exit;
    }
}

// Actualiza la configuración mediante sentencia preparada
$stmt = $conn->prepare(
    "UPDATE settings SET `value` = ? WHERE item = ?"
);

if (!$stmt) {
    error_log("Error preparando UPDATE settings: " . $conn->error);

    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "No se pudo preparar la actualización."
    ]);
    exit;
}

$stmt->bind_param("ss", $value, $item);

if (!$stmt->execute()) {
    error_log("Error actualizando settings: " . $stmt->error);

    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "No se pudo guardar el cambio."
    ]);

    $stmt->close();
    exit;
}

$stmt->close();

// Respuesta exitosa
echo json_encode([
    "success" => true,
    "value" => $value
]);

exit;
