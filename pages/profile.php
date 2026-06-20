<?php

// ==========================================================================
// ARCHIVO: INTERFAZ DEL PERFIL DE USUARIO (PROFILE) 
// Nota: por ahora solo la interfaz del perfil del usuario paciente
// ==========================================================================

// Middleware de seguridad y control de accesos
require_once("../php/auth/auth.php");
require_once("../php/auth/permissions.php");

// Middleware de conexión con la base de datos
require_once("../php/config/db.php");

// Middleware de acceso al archivo externo para las banderas de países
require_once("../php/helpers/utils.php");

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
        p.country,
        up.phone_number,
        c.email,
        c.status,
        psy.license_number,
        GROUP_CONCAT(s.name ORDER BY s.name ASC SEPARATOR ', ') AS specialties
    FROM user_profile up
    INNER JOIN credential c
        ON up.uuid_credential = c.uuid_credential
    LEFT JOIN patient p
        ON up.uuid_user_profile = p.uuid_user_profile
    LEFT JOIN psychologist psy
        ON up.uuid_user_profile = psy.uuid_user_profile
    LEFT JOIN psychologist_specialty ps
        ON psy.uuid_psychologist = ps.uuid_psychologist
    LEFT JOIN specialty s
        ON ps.uuid_specialty = s.uuid_specialty
    WHERE up.uuid_user_profile = ?
    GROUP BY up.uuid_user_profile, psy.uuid_psychologist
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
    $formattedBirthDate = $birthDate->format('d / m / Y');
} 

/// Combinar y convertir a mayúsculas soportando acentos (UTF-8)
$fullName = mb_strtoupper($userData['names'] . " " . $userData['surnames'], 'UTF-8');
$phoneNumber = $userData['phone_number'] ?? '';
$countryName = $userData['country'] ?? 'No especificado.';

// Genera código para las banderas desde utils.php
$countryCode = getCountryCodeByPhone($phoneNumber);
$locationCountryCode = getCountryCodeByName($countryName);

// Obtener todas las especialidades disponibles en la base de datos para el formulario
$allSpecialties = [];
if ($role === "psicologo") {
    $sql_all_specs = "SELECT uuid_specialty, name FROM specialty ORDER BY name ASC";
    $result_specs = $conn->query($sql_all_specs);
    if ($result_specs) {
        while ($row_spec = $result_specs->fetch_assoc()) {
            $allSpecialties[] = $row_spec;
        }
    }
    
    // Convertir las especialidades actuales del usuario en un array para comparar fácilmente en los checkboxes
    $userSpecialtiesArray = !empty($userData['specialties']) 
        ? array_map('trim', explode(',', $userData['specialties'])) 
        : [];
}

// Cierre explícito del statement para liberar memoria del servidor
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>PsicoGest | Mi perfil</title>
    <link rel ="icon" href="../assets/img/icon.ico" type="image/x-icon">

    <!-- Librería para los números de teléfono internacionales -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/css/intlTelInput.css"/>    
    <!-- Librería para poner iconos de banderas de países -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icons/6.6.6/css/flag-icons.min.css"> 

    <link rel="stylesheet" href="../assets/css/base/main.css">

</head>

    <body>

        <!-- HEADER -->
        <div id="header-container"></div> 
        <!-- CONTENIDO PRINCIPAL -->
        <main class="main-content"> 
            <!-- BARRA SUPERIOR EN LA PÁGINA -->
            <div class="top-bar"> 
                <!-- Titulo de la página -->
                <h2 class="top-bar-title"> 
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

            <!-- TARJETA CONTENEDORA -->
            <div class="profile-card"> 
                <!-- CONTENEDOR DEL CONTENIDO DEL PERFIL -->
                <div class="profile-container"> 
                    <!-- Contenido a la izquierda -->
                    <div class="left-column"> 
                        <!-- Contenedor de los botones -->
                        <div class="container-buttons"> 
                            <!-- Linea horizontal que une cada botón con la linea divisora vertical -->
                            <div class="line-h-buttons" id="line-edit-prof"></div> 
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
                            <!-- Linea horizontal que une cada botón con la linea divisora vertical -->
                            <div class="line-h-buttons" id="line-change-pass"></div> 
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
                    <div class="line-v-profile"></div>  
                    <!-- Contenido a la derecha -->
                    <div class="right-column"> 
                        <!-- Cajas para mensajes de error y de éxito -->
                        <div id="profile-error-box" class="error-box hidden"></div>
                        <div id="profile-success-box" class="success-box hidden"></div>
                        
                        <!-- VISTA DEL PERFIL (visible por defecto) -->
                        <div id="profile-info">
                            <!--=============== PACIENTE ===============-->
                            <?php if ($role === "paciente"): ?>

                                <!-- Nombre del usuario -->
                                <div class="name-patient"> 
                                    <svg class="icon">
                                        <use href="#user-profile"></use>
                                    </svg>
                                    <span id="view-fullname"><?= htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                                <!-- FILA: Ubicación / Edad-->
                                <div class="row-profile"> 
                                    <!-- Ubicación -->
                                    <div class="field-profile"> 
                                        <svg class="icon">
                                            <use href="#location"></use>
                                        </svg>
                                        <div class="text profile-box">
                                            <div class="prefix-container">
                                                <span class="fi fi-<?= $locationCountryCode ?>" id="view-flag-country"></span>
                                            </div>
                                            <span class="info-value" id="view-country"><?= htmlspecialchars($countryName, ENT_QUOTES, 'UTF-8') ?></span>
                                        </div>
                                    </div>
                                    <!-- Edad -->
                                    <div class="field-profile"> 
                                        <svg class="icon">
                                            <use href="#user"></use>
                                        </svg>
                                        <span class="text" id="view-age"><?= $ageText ?></span>
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
                                        <span class="text" id="view-birthdate"><?= $formattedBirthDate ?></span>
                                    </div>
                                </div> 

                                <!-- FILA: Teléfono / Correo -->
                                <div class="row-profile"> 
                                    <!-- Teléfono -->
                                    <div class="field-profile">
                                        <svg class="icon">
                                            <use href="#phone"></use>
                                        </svg>
                                        <div class="text profile-box">
                                            <div class="prefix-container">
                                                <span class="fi fi-<?= $countryCode ?>" id="view-flag-phone"></span>
                                            </div>
                                            <span class="info-value" id="view-phone"><?= htmlspecialchars($phoneNumber, ENT_QUOTES, 'UTF-8') ?></span>
                                        </div>
                                    </div>

                                    <!-- Correo -->
                                    <div class="field-profile"> 
                                        <svg class="icon">
                                            <use href="#email"></use>
                                        </svg>
                                        <span class="text" id="view-email"><?= htmlspecialchars($userData['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                                    </div>
                                </div> 

                            <?php endif; ?>

                            <!--=============== PSICÓLOGO ===============-->
                            <?php if ($role === "psicologo"): ?>

                                <!-- Nombre del usuario -->
                                <div class="name-psychologist"> 
                                    <svg class="icon">
                                        <use href="#user-profile"></use>
                                    </svg>
                                    <span id="view-fullname"><?= htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8') ?></span>
                                </div>

                                <!-- Especialidades -->
                                <div class="specialties"> 
                                    <svg class="icon">
                                        <use href="#psychology"></use>
                                    </svg>
                                    <span id="view-specialties"><?= htmlspecialchars($userData['specialties'] ?? '', ENT_QUOTES, 'UTF-8') ?>.</span>
                                </div>

                                <!-- FILA: Documento / Tarjeta profesional -->
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
                                    <!-- Tarjeta profesional -->
                                    <div class="field-profile"> 
                                        <svg class="icon">
                                            <use href="#id-card"></use>
                                        </svg>
                                        <div class="text profile-box">
                                            <div class="prefix-container label-prefix">
                                                TP
                                            </div>
                                            <span class="info-value"><?= htmlspecialchars($userData['license_number'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- FILA: Teléfono / Correo -->
                                <div class="row-profile"> 
                                    <!-- Teléfono -->
                                    <div class="field-profile">
                                        <svg class="icon">
                                            <use href="#phone"></use>
                                        </svg>
                                        <div class="text profile-box">
                                            <div class="prefix-container">
                                                <span class="fi fi-<?= $countryCode ?>" id="view-flag-phone"></span>
                                            </div>
                                            <span class="info-value" id="view-phone"><?= htmlspecialchars($phoneNumber, ENT_QUOTES, 'UTF-8') ?></span>
                                        </div>
                                    </div>

                                    <!-- Correo -->
                                    <div class="field-profile"> 
                                        <svg class="icon">
                                            <use href="#email"></use>
                                        </svg>
                                        <span class="text" id="view-email"><?= htmlspecialchars($userData['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                                    </div>
                                </div> 
                            <?php endif; ?>
                        </div>
                            
                        <!-- FORMULARIO CAMBIO DE DATOS EN EL PERFIL (oculto por defecto) -->
                        <div id="edit-profile-form" class="hidden">

                            <!--=============== PACIENTE ===============-->
                            <?php if ($role === "paciente"): ?>

                                <!-- FILA: Nombre y apellidos -->
                                <div class="name-patient">
                                    <svg class="icon"><use href="#user-profile"></use></svg>
                                    <input type="text" id="edit-names" placeholder="NOMBRES" value="<?= htmlspecialchars($userData['names'], ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="text" id="edit-surnames" placeholder="APELLIDOS" value="<?= htmlspecialchars($userData['surnames'], ENT_QUOTES, 'UTF-8') ?>">
                                </div>

                                <!-- FILA: Ubicación / Edad -->
                                <div class="row-profile">
                                    <!-- Ubicación -->
                                    <div class="field-profile" id="country-select-wrapper">
                                        <svg class="icon"><use href="#location"></use></svg>
                                        <div class="dropdown" id="country-dropdown">
                                            <div class="dropdown-selected">
                                                <span class="dropdown-text">
                                                    <?= ($countryName !== 'No especificado.')
                                                        ? htmlspecialchars($countryName, ENT_QUOTES, 'UTF-8')
                                                        : 'PAÍS DE RESIDENCIA' ?>
                                                </span>
                                                <svg class="arrow-icon"><use href="#down-arrow"></use></svg>
                                            </div>
                                            <ul class="dropdown-options" id="country-options"></ul>
                                        </div>
                                        <input type="hidden" id="country" name="country" value="<?= ($countryName !== 'No especificado.') ? htmlspecialchars($countryName, ENT_QUOTES, 'UTF-8') : '' ?>">
                                    </div>

                                    <!-- Edad -->
                                    <div class="field-profile" id="view-age-form"> 
                                        <svg class="icon">
                                            <use href="#user"></use>
                                        </svg>
                                        <span class="text" id="view-age" title="Este campo no puede modificarse."><?= $ageText ?></span>
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
                                            <div class="prefix-container label-prefix" title="Este campo no puede modificarse.">
                                                <?= htmlspecialchars(mb_strtoupper($userData['doc_type'] ?? '', 'UTF-8'), ENT_QUOTES, 'UTF-8') ?>
                                            </div>
                                            <span class="info-value" title="Este campo no puede modificarse."><?= htmlspecialchars($userData['doc_number'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                                        </div>
                                    </div>

                                    <!-- Dia de nacimiento -->
                                    <div class="field-profile"> 
                                        <svg class="icon">
                                            <use href="#calendar-day"></use>
                                        </svg>
                                            <input 
                                            type="text" 
                                            id="birth-date" 
                                            name="birth-date" 
                                            maxlength="14"
                                            placeholder="DD / MM / AAAA" 
                                            value="<?= $formattedBirthDate ?>"
                                            required
                                        >
                                    </div>

                                </div> 

                                <!-- FILA: Teléfono / Correo -->
                                <div class="row-profile">
                                    <!-- Teléfono -->
                                    <div class="field-profile" id="container-phone_number">
                                        <svg class="icon"><use href="#phone"></use></svg>
                                        <input type="tel" id="edit-phone" name="phone-input" placeholder="NÚMERO DE TELÉFONO" value="<?= htmlspecialchars($phoneNumber, ENT_QUOTES, 'UTF-8') ?>">
                                    
                                        <input type="hidden" name="phone-full" id="phone-full">
                                    </div>
                                    <!-- Correo -->
                                    <div class="field-profile" id="container-email">
                                        <svg class="icon"><use href="#email"></use></svg>
                                        <input type="email" id="edit-email" name="email-input" placeholder="CORREO ELECTRÓNICO" value="<?= htmlspecialchars($userData['email'], ENT_QUOTES, 'UTF-8') ?>">
                                    </div>
                                </div>

                            <?php endif; ?>    

                            <!--=============== PSICÓLOGO ===============-->
                            <?php if ($role === "psicologo"): ?>

                                <!-- Nombre del usuario -->
                                <div class="name-psychologist"> 
                                    <svg class="icon">
                                        <use href="#user-profile"></use>
                                    </svg>
                                    <input type="text" id="edit-names" placeholder="NOMBRES" value="<?= htmlspecialchars($userData['names'], ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="text" id="edit-surnames" placeholder="APELLIDOS" value="<?= htmlspecialchars($userData['surnames'], ENT_QUOTES, 'UTF-8') ?>">
                                </div>

                                <!-- Especialidades -->
                                <div class="specialties-edit-container"> 
                                    <div class="specialties-label">
                                        <svg class="icon">
                                            <use href="#psychology"></use>
                                        </svg>
                                        <span>EDICIÓN DE ESPECIALIDADES</span>
                                    </div>

                                    <div class="checkbox-group-specialties" id="container-specialties">
                                        <?php foreach ($allSpecialties as $spec): 
                                            // Valida si el psicólogo ya tiene esta especialidad asignada
                                            $isChecked = in_array($spec['name'], $userSpecialtiesArray) ? 'checked' : '';
                                        ?>
                                            <div class="profile-checkbox-container">
                                                <label class="checkbox-label">
                                                    <input 
                                                        type="checkbox" 
                                                        name="specialties[]" 
                                                        value="<?= htmlspecialchars($spec['uuid_specialty'], ENT_QUOTES, 'UTF-8') ?>" 
                                                        <?= $isChecked ?>
                                                    >
                                                    <span class="checkbox-custom"></span>
                                                    <span class="checkbox-text"><?= htmlspecialchars($spec['name'], ENT_QUOTES, 'UTF-8') ?></span>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <!-- FILA: Documento / Tarjeta profesional -->
                                <div class="row-profile"> 
                                    <!-- Documento -->
                                    <div class="field-profile"> 
                                        <svg class="icon">
                                            <use href="#id-card"></use>
                                        </svg>
                                        <div class="text profile-box">
                                            <div class="prefix-container label-prefix" title="Este campo no puede modificarse.">
                                                <?= htmlspecialchars(mb_strtoupper($userData['doc_type'] ?? '', 'UTF-8'), ENT_QUOTES, 'UTF-8') ?>
                                            </div>
                                            <span class="info-value" title="Este campo no puede modificarse."><?= htmlspecialchars($userData['doc_number'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                                        </div>
                                    </div>
                                    <!-- Tarjeta profesional -->
                                    <div class="field-profile"> 
                                        <svg class="icon">
                                            <use href="#id-card"></use>
                                        </svg>
                                        <div class="text profile-box">
                                            <div class="prefix-container label-prefix" title="Este campo no puede modificarse.">
                                                TP
                                            </div>
                                            <span class="info-value" title="Este campo no puede modificarse."><?= htmlspecialchars($userData['license_number'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- FILA: Teléfono / Correo -->
                                <div class="row-profile">
                                    <!-- Teléfono -->
                                    <div class="field-profile" id="container-phone_number">
                                        <svg class="icon"><use href="#phone"></use></svg>
                                        <input type="tel" id="edit-phone" name="phone-input" placeholder="NÚMERO DE TELÉFONO" value="<?= htmlspecialchars($phoneNumber, ENT_QUOTES, 'UTF-8') ?>">
                                    
                                        <input type="hidden" name="phone-full" id="phone-full">
                                    </div>
                                    <!-- Correo -->
                                    <div class="field-profile" id="container-email">
                                        <svg class="icon"><use href="#email"></use></svg>
                                        <input type="email" id="edit-email" name="email-input" placeholder="CORREO ELECTRÓNICO" value="<?= htmlspecialchars($userData['email'], ENT_QUOTES, 'UTF-8') ?>">
                                    </div>
                                </div>

                            <?php endif; ?>        
                        </div>

                        <!-- FORMULARIO CAMBIO DE CONTRASEÑA (oculto por defecto) -->
                        <div id="change-password-form" class="hidden">
                            <!-- Campo: contraseña actual -->
                            <div class="field-input" id="container-current_password">
                                <svg class="icon"><use href="#password-unlock"></use></svg>
                                <input 
                                    type="password" 
                                    id="current-password" 
                                    class="password-input"
                                    placeholder="CONTRASEÑA ACTUAL" 
                                    autocomplete="current-password"
                                >
                                <!-- Botón para mostrar / ocultar contraseña -->
                                <button type="button" class="toggle-password" data-target="current-password" aria-label="Mostrar contraseña">
                                    <svg class="icon"><use href="#eye"></use></svg>
                                </button>
                            </div>

                            <!-- Texto con los requisitos -->
                            <p class="password-requirements">
                                La contraseña debe tener al menos 8 caracteres, 
                                incluir una letra mayúscula, una letra minúscula, 
                                un número y un carácter especial 
                                (ej: @, $, !, %, *, ?, &, _, -, ., /, #)."
                            </p>

                            <!-- Campo: nueva contraseña -->
                            <div class="field-input" id="container-new_password">
                                <svg class="icon"><use href="#password"></use></svg>

                                <input 
                                    type="password" 
                                    id="new-password" 
                                    class="password-input"
                                    placeholder="NUEVA CONTRASEÑA" 
                                    autocomplete="new-password"
                                >
                                <button type="button" class="toggle-password" data-target="new-password" aria-label="Mostrar contraseña">
                                    <svg class="icon"><use href="#eye"></use></svg>
                                </button>
                            </div>

                            <!-- Campo: repetir nueva contraseña -->
                            <div class="field-input" id="container-repeat_password">
                                <svg class="icon"><use href="#password"></use></svg>

                                <input 
                                    type="password" 
                                    id="repeat-password" 
                                    class="password-input"
                                    placeholder="REPETIR NUEVA CONTRASEÑA" 
                                    autocomplete="new-password"
                                >

                                <button type="button" class="toggle-password" data-target="repeat-password" aria-label="Mostrar contraseña">
                                    <svg class="icon"><use href="#eye"></use></svg>
                                </button>
                            </div>
                        </div>    
                    </div>
                </div>
            </div>

            <!-- BOTONES DE ACCIÓN (visibles al abrir el formulario de contraseña) -->
            <div id="action-buttons-profile" class="action-buttons-profile hidden">
                <button id="cancel-button-profile" class="action-button-profile cancel-button-profile">
                    <svg class="icon"><use href="#x-mark"></use></svg>
                    CANCELAR
                </button>
                <button id="save-button-profile" class="action-button-profile save-button-profile">
                    <svg class="icon"><use href="#save"></use></svg>
                </button>
            </div>      
        </main>

        <!-- MODAL: CAMBIO EXITOSO -->
        <div id="success-modal" class="modal hidden">
            <div class="modal-content modal-change-pass">
                <h1 class="modal-title">PSIC🧠GEST • CAMBIO EXITOSO ✓</h1>
                <div class="modal-message">Tu contraseña ha sido actualizada correctamente.</div>
                <button class="ok-button" id="close-modal">ACEPTAR</button>
            </div>
        </div>

        <!-- Script para mostrar el rol del usuario en el userRole -->
        <script>
            window.USER_ROLE = "<?= $_SESSION['role'] ?? 'usuario' ?>";
        </script>

        <!-- Librería para los números de teléfono internacionales -->
        <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/js/intlTelInput.min.js"></script>

        <script src="../assets/js/components/intl_phone_input.js"></script>

        <script src="../assets/js/components/icons.js"></script>
        <script src="../assets/js/components/menu_sidebar.js"></script>
        <script src="../assets/js/components/header.js"></script>
        <script src="../assets/js/components/dropdowns.js"></script>
        <!-- Scripts específicos para esta página -->
        <script src="../assets/js/components/toggle_password.js"></script>
        <script src="../assets/js/components/birthdate_mask.js"></script>
        <script src="../assets/js/components/countries_select.js"></script>
        <script src="../assets/js/auth/change_password.js"></script>
        <script src="../assets/js/users/update_profile.js"></script>
        <script src="../assets/js/pages/profile.js"></script>
    </body>
</html>