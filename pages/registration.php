<?php
// ==========================================================================
// ARCHIVO: INTERFAZ DEL REGISTRO DE PACIENTES (PRINCIPAL)
// ==========================================================================

// Middleware de conexión con la base de datos
require_once("../php/config/db.php");

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel ="icon" href="../assets/img/icon.ico" type="image/x-icon">
    <title>PsicoGest | Registrarse</title>

    <!-- Libreria para los números de teléfono internacionales -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/css/intlTelInput.css"/>
    <!-- Libreria para poner iconos de banderas de países -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icons/6.6.6/css/flag-icons.min.css">
    <link rel="stylesheet" href="../assets/css/base/main.css">
</head>

<body class="page-registration">

    <!-- HEADER EN EL LOGIN INYECTADO -->
    <div id="header-container"></div> 
    <!-- CONTENIDO PRINCIPAL -->
    <main class="registration-container">

        <!-- TARJETA CONTENEDORA -->
        <div class="registration-card">

            <!-- Título de la página -->
            <h2>REGISTRO EN EL SISTEMA</h2> 
            <!-- Instrucción -->
            <P>Por favor, ingresa todos los datos solicitados a continuación.</p>

            <!-- FORMULARIO (CREATE) -->
            <form class="registration-form" id="registration-form" action="" method="POST">
                <!-- FILA: TIPO Y NÚMERO DE DOCUMENTO -->
                <div class="row two-columns registration-row">

                    <!-- CONTENEDOR DEL CAMPO: tipo de documento -->
                    <div class="field-container"> 
                        <!-- Label -->
                        <svg class="icon"><use href="#id-card"></use></svg>

                        <!-- CAMPO: tipo de documento -->
                        <div class="input-box"> 
                            <!-- Dropdown personalizado -->
                            <div class="dropdown" id="doc-type-dropdown">
                                <!-- Elemento que se ve seleccionado: dropdown cerrado -->
                                <div class="dropdown-selected"> 

                                    <!-- Hint/Placeholder -->
                                    <span class="dropdown-text">TIPO DE DOCUMENTO</span> 
                                    <!-- Icono de flecha -->
                                    <svg class="arrow-icon"><use href="#down-arrow"></use></svg> 
                                </div>
                                <!-- Opciones: dropdown abierto -->
                                <ul class="dropdown-options"> 
                                    <li data-value="cc">Cédula de Ciudadanía</li>
                                    <li data-value="ce">Cédula de Extranjería</li>
                                    <li data-value="ti">Tarjeta de Identidad</li>
                                    <li data-value="pa">Pasaporte</li>
                                    <li data-value="ot">Otro (Extranjero)</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Select nativo: necesario para enviar la información del form -->
                    <input type="hidden" name="doc-type" id="doc-type" value="cc"> 

                    <!-- CONTENEDOR DEL CAMPO: número de documento -->
                    <div class="field-input"> 
                            <!-- Icono de usuario -->
                            <svg class="icon"><use href="#id-card"></use></svg>
                            <input 
                                type="text" 
                                id="doc-number" 
                                name="doc-number" 
                                minlength="5"
                                placeholder="NÚMERO DE DOCUMENTO" 
                                value=""
                                required
                            >
                    </div>
                    
                </div>

                <!-- FILA: NOMBRES Y APELLIDOS -->
                <div class="row two-columns registration-row">

                    <!-- CONTENEDOR DEL CAMPO: nombres -->
                    <div class="field-input"> 
                            <!-- Icono de usuario -->
                            <svg class="icon"><use href="#user"></use></svg>
                            <input 
                                type="text" 
                                id="names" 
                                name="names" 
                                minlength="5"
                                placeholder="NOMBRE(S)" 
                                value=""
                                required
                            >
                    </div>

                    <!-- CONTENEDOR DEL CAMPO: apellidos -->
                    <div class="field-input"> 
                            <!-- Icono de usuario -->
                            <svg class="icon"><use href="#user"></use></svg>
                            <input 
                                type="text" 
                                id="surnames" 
                                name="surnames" 
                                minlength="5"
                                placeholder="APELLIDOS" 
                                value=""
                                required
                            >
                    </div>

                </div>


                <!-- FILA: FECHA DE NACIMIENTO Y NÚMERO DE TELÉFONO -->
                <div class="row two-columns registration-row">
                    <!-- CONTENEDOR DEL CAMPO: fecha de nacimiento -->
                    <div class="field-input"> 
                            <!-- Icono de usuario -->
                            <svg class="icon"><use href="#calendar-day"></use></svg>
                            <input 
                                type="text" 
                                id="birth-date" 
                                name="birth-date" 
                                maxlength="14"
                                placeholder="DD / MM / AAAA" 
                                value=""
                                required
                            >
                    </div>

                    <!-- CONTENEDOR DEL CAMPO: número de teléfono -->
                    <div class="field-input"> 
                            <!-- Icono de usuario -->
                            <svg class="icon"><use href="#phone"></use></svg>
                            <input 
                                type="tel" 
                                id="phone-input" 
                                name="phone-input"
                                placeholder="NÚMERO DE TELÉFONO" 
                                required
                            >

                            <input type="hidden" name="phone-full" id="phone-full">
                    </div>
                </div>

                <!-- FILA: CORREO Y SU CONFIRMACIÓN -->
                <div class="row two-columns registration-row">

                    <!-- CONTENEDOR DEL CAMPO: correo -->
                    <div class="field-input"> 
                            <!-- Icono de usuario -->
                            <svg class="icon"><use href="#email"></use></svg>
                            <input 
                                type="email" 
                                id="email-input" 
                                name="email-input" 
                                minlength="5"
                                placeholder="CORREO ELECTRÓNICO" 
                                value=""
                                required
                            >
                    </div>

                    <!-- CONTENEDOR DEL CAMPO: confirmación del correo -->
                    <div class="field-input"> 
                            <!-- Icono de usuario -->
                            <svg class="icon"><use href="#email"></use></svg>
                            <input 
                                type="email" 
                                id="email-confirm" 
                                name="email-confirm" 
                                minlength="5"
                                placeholder="REPETIR CORREO ELECTRÓNICO" 
                                value=""
                                required
                            >
                    </div>
                </div>

                 <!-- FILA: CONTRASEÑA Y SU CONFIRMACIÓN -->
                <div class="row two-columns registration-row">

                    <!-- CONTENEDOR DEL CAMPO: contraseña -->
                    <div class="field-input"> 
                            <!-- Icono de usuario -->
                            <svg class="icon"><use href="#password"></use></svg>
                            <input 
                                type="password" 
                                id="password-input" 
                                name="password-input" 
                                minlength="8"
                                placeholder="CONTRASEÑA" 
                                title="Mínimo 8 caracteres, incluyendo una letra mayúscula, una letra minúscula, un número y un carácter especial."
                                value=""
                                required
                            >

                            <!-- Botón para mostrar / ocultar contraseña -->
                            <button type="button" class="toggle-password" aria-label="Mostrar contraseña">
                                <!-- Icono de ojo -->
                                <svg class="icon"><use xlink:href="#eye"></use></svg>
                            </button>
                    </div>

                    <!-- CONTENEDOR DEL CAMPO: confirmación de la contraseña -->
                     <div class="field-input"> 
                            <!-- Icono de usuario -->
                            <svg class="icon"><use href="#password"></use></svg>
                            <input 
                                type="password" 
                                id="password-confirm" 
                                name="password-confirm" 
                                minlength="8"
                                placeholder="REPETIR CONTRASEÑA" 
                                value=""
                                required
                            >

                            <!-- Botón para mostrar / ocultar contraseña -->
                            <button type="button" class="toggle-password" aria-label="Mostrar contraseña">
                                <!-- Icono de ojo -->
                                <svg class="icon"><use xlink:href="#eye"></use></svg>
                            </button>
                    </div>
                </div>

                <!-- Instrucción -->
                <span class="location-label">UBICACIÓN:</span> 
                    
                <p class="location-instruction"><strong>¿DÓNDE TE ENCUENTRAS?</strong><br>
                <br>
                Por favor, marca y elige <strong>solo una</strong> de las siguientes opciones según sea tu caso:</p>

                 <!-- FILA: UBICACIÓN -->
                <div class="row two-columns registration-row location-row">
                        <div class="radio-group">
                            <label>
                                <input 
                                    type="radio" 
                                    name="location" 
                                    value="colombia"
                                    required
                                >
                                <span class="radio-custom"></span>
                                EN COLOMBIA
                            </label>

                            <label>
                                <input 
                                    type="radio" 
                                    name="location" 
                                    value="otro"
                                >
                                <span class="radio-custom"></span>
                                EN EL EXTRANJERO
                            </label>
                        </div>

                        <div class="field-input" id="country-select-wrapper">

                            <svg class="icon"><use href="#location"></use></svg>

                            <div class="dropdown" id="country-dropdown">
                                <div class="dropdown-selected">
                                    <span class="dropdown-text">INDICA EL PAÍS</span>
                                    <svg class="arrow-icon"><use href="#down-arrow"></use></svg>

                                </div>
                                <ul class="dropdown-options" id="country-options"></ul>
                            </div>

                            <input type="hidden" id="country" name="country">

                        </div>
                </div>

                <!-- Línea horizontal divisora -->
                <hr class="line-h-registration">

                <div class="row row-checkboxes row-registration">
    
                    <div class="checkbox-container">
                        <label class="checkbox-label">
                            <input type="checkbox" name="terms" id="terms" required>
                            <span class="checkbox-custom"></span>
                            <span> 
                                HE LEÍDO Y ACEPTO LOS 
                                <a href="" class="legal-link">TÉRMINOS Y CONDICIONES DE USO.</a>
                            </span>
                        </label>
                    </div>

                    <div class="checkbox-container">
                        <label class="checkbox-label">
                            <input type="checkbox" name="data-policy" id="data-policy" required>
                            <span class="checkbox-custom"></span>
                            <span>
                                AUTORIZO EL TRATAMIENTO DE MIS DATOS PERSONALES Y SENSIBLES CONFORME A LA 
                                <a href="" class="legal-link">POLÍTICA DE PRIVACIDAD.</a>
                            </span>
                        </label>
                    </div>

                </div>

                <!-- Boton para enviar el formulario --> 
                <button 
                    type="submit" 
                    class="register-button"
                >
                    REGISTRARME
                </button>  
            </form>

            <!-- ENLACE AL REGISTRO -->
            <p class="login-link"> 
                ¿YA TIENES UN USUARIO? <a href="login.php">INGRESA AQUÍ</a>
            </p>
        
        </div>

        <div id="success-modal" class="modal hidden">
            <div class="modal-content">
                <h1 class="modal-title">PSIC🧠GEST • REGISTRO EXITOSO ✓</h1>

                <div id="modal-body"></div>

                <button class="ok-button" id="close-modal">ACEPTAR</button>
            </div>
        </div>

    </main>

    <!-- Libreria para los números de teléfono internacionales -->
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/js/intlTelInput.min.js"></script>

    <script src="../assets/js/components/intl_phone_input.js"></script>
    
    <script src="../assets/js/components/icons.js"></script>
    <script src="../assets/js/components/header_variation.js"></script>
    <script src="../assets/js/components/dropdowns.js"></script>

    <!-- Scripts específicos para esta página -->
    <script src="../assets/js/components/toggle_password.js"></script>
    <script src="../assets/js/components/birthdate_mask.js"></script>
    <script src="../assets/js/components/countries_select.js"></script>
    <script src="../assets/js/pages/registration.js"></script>
</body>
</html>