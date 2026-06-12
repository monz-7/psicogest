<?php
// ==========================================================================
// ARCHIVO: AUTENTICACIÓN Y VALIDACIONES
// - Valida credenciales
// - Comprueba roles
// - Asocia perfiles
// - Asegura la sesión activa
// ==========================================================================

// Inicia una nueva sesión o reanuda una sesión existente
session_start();
// Incluye el archivo de conexión a la base de datos
require_once("db.php");

// ==========================================================================
// Función auxiliar para centralizar el manejo de errores de autenticación
// Carga el estado de error en la sesión y redirige a la vista de login
// ==========================================================================
function handleLoginError($message, $fields, $role, $login) {
    $_SESSION["error"] = $message;
    $_SESSION["error_fields"] = $fields;
    $_SESSION["old_role"] = $role;
    $_SESSION["old_login"] = $login;
    
    header("Location: ../pages/login.php");
    exit();
}

// VERIFICA QUE LLEGUE POR POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    // Si alguien intenta acceder directamente al archivo, lo redirige al login
    header("Location: ../pages/login.php");
    // Detiene la ejecución del script
    exit();
}
// LIMPIA Y OBTIENE DE DATOS DE LOS CAMPOS
$role = trim($_POST["role"] ?? "");
$login = trim($_POST["login"] ?? "");
$password = trim($_POST["password"] ?? "");

// VALIDACIÓN DE CAMPOS VACÍOS
if (empty($role) || empty($login) || empty($password)) {

    handleLoginError(
        "Todos los campos son obligatorios.",
        ["role" => empty($role), "login" => empty($login), "password" => empty($password)],
        $role,
        $login
    );
}

// CONSULTA DE CREDENCIALES EN BASE DE DATOS
$sql = "SELECT * FROM credential
        WHERE username = ?
        OR email = ?";

// Prepara la consulta para evitar inyecciones SQL
$stmt = mysqli_prepare($conn, $sql);

// Asocia los valores a los parámetros de la consulta preparada
mysqli_stmt_bind_param(
    $stmt,
    "ss", // Dos parámetros de tipo string
    $login,
    $login
);

// Ejecuta la consulta
mysqli_stmt_execute($stmt);

// Obtiene el resultado de la consulta
$result = mysqli_stmt_get_result($stmt);

// Obtiene los datos del usuario en forma de arreglo asociativo
$user = mysqli_fetch_assoc($result);

// Verifica que exista exactamente un usuario coincidente
// Si no se encuentra se aborta con error genérico
if (!$user) {
    mysqli_stmt_close($stmt);
    handleLoginError(
        "Usuario o contraseña incorrectos.",
        ["login" => true, "password" => true],
        $role,
        $login
    );
}

// COMPROBACIÓN DE ESTADO DE CUENTA
if ($user["status"] !== "activo") {
    mysqli_stmt_close($stmt);
    handleLoginError(
        "Usuario inactivo. Contacta al administrador.",
        ["login" => true],
        $role,
        $login
    );
}

// VERIFICACIÓN DE LA CONTRASEÑA
if (!password_verify($password, $user["password"])) {
    mysqli_stmt_close($stmt);
    handleLoginError(
        "Usuario o contraseña incorrectos.",
        ["login" => true, "password" => true],
        $role,
        $login
    );
}

// COINCIDENCIA DE ROL SELECCIONADO VS ROL REAL
if ($role !== $user["role"]) {
    mysqli_stmt_close($stmt);
    handleLoginError(
        "El rol es incorrecto para este usuario.",
        ["role" => true],
        $role,
        $login
    );
}

// Libera el statement de credenciales ya validado
mysqli_stmt_close($stmt);

// ==========================================================================
// PROCESO EXITOSO DE AUTENTICACIÓN Y APERTURA DE SESIÓN
// ==========================================================================

// Regenera el ID de sesión después del login
session_regenerate_id(true);

// Guarda las variables de sesión base de la credencial
$_SESSION["uuid_credential"] = $user["uuid_credential"];
$_SESSION["role"] = $user["role"];
$_SESSION["username"] = $user["username"];

// RESTRICCIÓN Y CARGA DEL PERFIL ASOCIADO
$sqlProfile = "SELECT uuid_user_profile, names
            FROM user_profile
            WHERE uuid_credential = ?";

// Prepara la consulta para evitar inyecciones SQL
$stmtProfile = mysqli_prepare($conn, $sqlProfile);

// Asocia los valores a los parámetros de la consulta preparada
mysqli_stmt_bind_param(
    $stmtProfile,
    "s", // Un parámetro de tipo string
    $user["uuid_credential"]
);

// Ejecuta la consulta
mysqli_stmt_execute($stmtProfile);

// Obtiene el resultado de la consulta
$resultProfile = mysqli_stmt_get_result($stmtProfile);

// Obtiene los datos del usuario en forma de arreglo asociativo
$profile = mysqli_fetch_assoc($resultProfile);

if ($profile) {
    // Guarda los identificadores específicos de perfil en el entorno global
    $_SESSION["uuid_user_profile"] = $profile["uuid_user_profile"];
    $_SESSION["names"] = $profile["names"];
}

mysqli_stmt_close($stmtProfile);

// Redirección directa al inicio
header("Location: ../pages/home.php");
exit();