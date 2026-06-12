<?php

// ==========================================================================
// ARCHIVO: INTERFAZ DEL PERFIL DE USUARIO (PROFILE) 
// Nota: por ahora solo la interfaz del perfil del usuario paciente
// ==========================================================================

// Middleware de seguridad y control de accesos
require_once("../php/auth.php");
require_once("../php/permissions.php");

// Middleware de conexión con la base de datos
require_once("../php/db.php");

// Middleware de acceso al archivo externo para las banderas de países
require_once("../php/utils.php");

// Restricción de acceso: módulo exclusivo para pacientes y psicólogos
requireRoles(["paciente", "psicologo"]);

// Captura el rol activo del usuario en la sesión
$role = $_SESSION["role"] ?? "";

// Captura el identificador único del perfil
$user_uuid = $_SESSION["uuid_user_profile"] ?? "";

if (empty($user_uuid)) {
    die("Error: No se ha encontrado una sesión de usuario válida.");
}

// CONSULTA DE DATOS (PERFIL Y CREDENCIAL)
$sql = "
    SELECT
        up.uuid_user_profile,
        up.doc_type,
        up.doc_number,
        up.names,
        up.surnames,
        p.birth_date,
        up.phone_number,
        c.email,
        c.status,
        up.country
    FROM user_profile up
    INNER JOIN credential c
        ON up.uuid_credential = c.uuid_credential
    LEFT JOIN patient p
        ON up.uuid_user_profile = p.uuid_user_profile
    WHERE up.uuid_user_profile = ?
    LIMIT 1
";
// Consulta preparada (mejor que inyección SQL
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Error en la preparación de la consulta: " . $conn->error);
}

// Asocia los valores a los parámetros de la consulta
$stmt->bind_param("s", $user_uuid);
// Ejecuta
$stmt->execute();
// Obtiene el resultado
$result = $stmt->get_result();
// Obtiene los datos del usuario en forma de arreglo asociativo
$userData = $result->fetch_assoc();

if (!$userData) {
    die("Error: No se encontraron los datos del perfil del usuario.");
}

// CALCULAR LA EDAD DINÁMICAMENTE SI EXISTE FECHA DE NACIMIENTO
$ageText = "No especificada.";
$formattedBirthDate = "No especificada.";

if (!empty($userData['birth_date'])) {
    $birthDate = new DateTime($userData['birth_date']);
    $currentDate = new DateTime();
    $dateDifference = $currentDate->diff($birthDate);
    $ageText = $dateDifference->y . " años";
    
    // Formatear la fecha de nacimiento visual a DD/MM/AAAA
    $formattedBirthDate = $birthDate->format('d/m/Y');
} 

/// Combinar y convertir a mayúsculas soportando acentos (UTF-8)
$fullName = mb_strtoupper($userData['names'] . " " . $userData['surnames'], 'UTF-8');
$phoneNumber = $userData['phone_number'] ?? '';
$countryName = $userData['country'] ?? 'No especificado.';

// Genera código para las banderas desde utils.php
$countryCode = getCountryCodeByPhone($phoneNumber);
$locationCountryCode = getCountryCodeByName($countryName);

// Cierre explícito del statement para liberar memoria del servidor
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>PsicoGest | Mi perfil</title>
    <link rel ="icon" href="../assets/icon.ico" type="image/x-icon">
    
    <!-- Librería para poner iconos de banderas de países -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icons/6.6.6/css/flag-icons.min.css"> 

    <link rel="stylesheet" href="../assets/css/main.css">

</head>

    <body>

        <!-- HEADER -->
        <div id="header-container"></div> 
        <!-- CONTENIDO PRINCIPAL -->
        <main class="main-content"> 
            <!-- BARRA SUPERIOR EN LA PÁGINA -->
            <div class="top-bar"> 
                <!-- Titulo de la página -->
                <h2> 
                    <svg class="icon-title">
                        <use href="#user"></use>
                    </svg>
                    PERFIL
                </h2>
                <!-- Botón de retorno al inicio -->
                <a href="home.php" class="return-button"> 
                    <svg class="icon">
                        <use href="#home"></use>
                    </svg>
                    VOLVER AL INICIO
                </a>
            </div>

            <!--=============== PACIENTE ===============-->
            <?php if ($role === "paciente"): ?>
                <!-- TARJETA CONTENEDORA -->
                <div class="card-profile"> 
                    <!-- CONTENEDOR DEL CONTENIDO DEL PERFIL -->
                    <div class="container-profile"> 
                        <!-- Contenido a la izquierda -->
                        <div class="left-column"> 
                            <!-- Contenedor de los botones -->
                            <div class="container-buttons"> 
                                <!-- Linea horizontal que une cada botón con la linea divisora vertical -->
                                <div class="line-h"></div> 
                                <!-- Botón para actualizar datos -->
                                <button class="profile-button" id="edit-prof-button"> 
                                    <svg class="icon">
                                        <use href="#user-edit"></use>
                                    </svg>
                                    <span class="button-text">ACTUALIZAR MIS DATOS</span>
                                </button>
                            </div>
                            <!-- Contenedor de los botones -->
                            <div class="container-buttons"> 
                                <!-- Linea horizontal que une cada boton con la linea divisora vertical -->
                                <div class="line-h"></div> 
                                <!-- Botón para cambiar la contraseña -->
                                <button class="profile-button" id="change-pass-button"> 
                                    <svg class="icon">
                                        <use href="#password"></use>
                                    </svg>
                                    <span class="button-text">MODIFICAR MI CONTRASEÑA</span>
                                </button>
                            </div>
                                    
                        </div>

                        <!-- Linea divisora vertical de las columnas -->
                        <div class="line-v-divider"></div>  
                        <!-- Contenido a la derecha -->
                        <div class="right-column"> 
                            <!-- Nombre del usuario -->
                            <p class="profile-name"> 
                                <svg class="icon">
                                    <use href="#user-profile"></use>
                                </svg>
                                <?= htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8') ?>
                            </p>
                            <!-- FILA: Ubicacion / Edad-->
                            <div class="row-profile"> 
                                <!-- Ubicacion -->
                                <div class="field-profile"> 
                                    <svg class="icon">
                                        <use href="#location"></use>
                                    </svg>
                                    <div class="text profile-box">
                                        <div class="prefix-container">
                                            <span class="fi fi-<?= $locationCountryCode ?>"></span>
                                        </div>
                                        <span class="info-value"><?= htmlspecialchars($countryName, ENT_QUOTES, 'UTF-8') ?></span>
                                    </div>
                                </div>
                                <!-- Edad -->
                                <div class="field-profile"> 
                                    <svg class="icon">
                                        <use href="#user"></use>
                                    </svg>
                                    <span class="text"><?= $ageText ?></span>
                                </div>
                    
                            </div>

                            <!-- FILA: Documento / Fecha de nacimiento -->
                            <div class="row-profile"> 
                                <!-- Documento -->
                                <div class="field-profile"> 
                                    <svg class="icon">
                                        <use href="#id-card"></use>
                                    </svg>

                                    <div class="text profile-box">
                                        <div class="prefix-container label-prefix">
                                            <?= htmlspecialchars(mb_strtoupper($userData['doc_type'] ?? '', 'UTF-8'), ENT_QUOTES, 'UTF-8') ?>
                                        </div>
                                        <span class="info-value"><?= htmlspecialchars($userData['doc_number'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                                    </div>
                                </div>

                                <!-- Dia de nacimiento -->
                                <div class="field-profile"> 
                                    <svg class="icon">
                                        <use href="#calendar-day"></use>
                                    </svg>
                                    <span class="text"><?= $formattedBirthDate ?></span>
                                </div>

                            </div> 

                            <!-- FILA: Telefono / Correo -->
                            <div class="row-profile"> 
                                <!-- Teléfono -->
                                <div class="field-profile">
                                    <svg class="icon">
                                        <use href="#phone"></use>
                                    </svg>
                                    <div class="text profile-box">
                                        <div class="prefix-container">
                                            <span class="fi fi-<?= $countryCode ?>"></span>
                                        </div>
                                        <span class="info-value"><?= htmlspecialchars($phoneNumber, ENT_QUOTES, 'UTF-8') ?></span>
                                    </div>
                                </div>

                                <!-- Correo -->
                                <div class="field-profile"> 
                                    <svg class="icon">
                                        <use href="#email"></use>
                                    </svg>
                                    <span class="text"><?= htmlspecialchars($userData['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                            </div> 
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!--=============== PSICÓLOGO ===============-->
            <?php if ($role === "psicologo"): ?>
                <!-- TARJETA CONTENEDORA -->
                <div class="card-home">

                <p>
                    [ DESARROLLO DE LA INTERFAZ PENDIENTE ]
                </p>

                </div>
            <?php endif; ?>
        </main>

        <!-- Script para mostrar el rol del usuario en el userRole -->
        <script>
            window.USER_ROLE = "<?= $_SESSION['role'] ?? 'usuario' ?>";
        </script>

        <script src="../assets/js/icons.js"></script>
        <script src="../assets/js/header.js"></script>
    </body>
</html>