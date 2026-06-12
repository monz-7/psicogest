<?php
// ==========================================================================
// ARCHIVO: INTERFAZ DEL LOGIN
// ==========================================================================

// Inicia o reanuda la sesión actual del usuario
session_start();

// Obtiene los campos que generaron un error de validación
$errorFields = $_SESSION["error_fields"] ?? [];
// Elimina el dato de la sesión después de recuperarlo
unset($_SESSION["error_fields"]);

// Obtiene el mensaje de error almacenado temporalmente
$error = $_SESSION["error"] ?? "";
// Elimina el mensaje de error para que solo se muestre una vez
unset($_SESSION["error"]);

// Permite la persistencia de valores en el formulario tras un error

// Recupera el rol seleccionado previamente
$oldRole = $_SESSION["old_role"] ?? "";
//  Recupera el usuario o correo ingresado previamente
$oldLogin = $_SESSION["old_login"] ?? "";
// Elimina los valores persistentes de la sesión después de haberlos recuperado
unset($_SESSION["old_login"], $_SESSION["old_role"]);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel ="icon" href="../assets/icon.ico" type="image/x-icon">
    <title>PsicoGest | Iniciar Sesión</title>

    <link rel="stylesheet" href="../assets/css/main.css">
</head>

<body class="page-login">
    <!-- HEADER EN EL LOGIN INYECTADO -->
    <div id="header-container"></div> 

    <!-- CONTENEDOR PRINCIPAL -->
    <div class="login-container"> 

        <!-- TARJETA CONTENEDORA -->
        <div class="card-login"> 

            <!-- Título de la página -->
            <h2>INGRESO AL SISTEMA</h2> 

            <!-- Contenedor para mostrar mensajes de error -->
            <!-- Verifica que la variable $error no esté vacía -->
            <?php if (!empty($error)): ?>
                <!-- Caja visual donde se mostrará el mensaje de error -->
                <div class="error-box">
                    <!-- Muestra el mensaje de error de forma segura -->
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <!-- FORMULARIO -->
            <form action="../php/login_process.php" method="POST"> 
                <!-- CONTENEDOR DEL CAMPO: rol de usuario -->
                <div class="field-container"> 
                    <!-- Label -->
                    <h3>SELECCIONA TU TIPO DE USUARIO: </h3> 
                    <!-- Campo -->
                    <div class="input-box"> 
                        <!-- Dropdown personalizado -->
                        <div class="dropdown <?php echo !empty($errorFields['role']) ? 'input-error' : ''; ?>" id="role-dropdown"> 
                            <!-- Elemento que se ve seleccionado: dropdown cerrado -->
                            <div class="dropdown-selected"> 
                                <!-- Hint/Placeholder -->
                                <span class="dropdown-text">TIPO DE USUARIO</span> 
                                <!-- Icono de flecha -->
                                <svg class="arrow-icon"><use href="#down-arrow"></use></svg> 
                            </div>
                            <!-- Opciones: dropdown abierto -->
                            <ul class="dropdown-options"> 
                                <li data-value="admin">ADMINISTRADOR</li>
                                <li data-value="psicologo">PSICÓLOGO</li>
                                <li data-value="paciente">PACIENTE</li>
                            </ul>
                        </div>
                    </div>  
                </div>

                <!-- Select nativo: necesario para enviar la información del form -->
                <input type="hidden" name="role" id="role"> 

                <!-- CAMPO DE TIPO INPUT: usuario o correo -->
                <div class="field-input <?php echo !empty($errorFields['login']) ? 'input-error' : ''; ?>"> 
                    <!-- Icono de usuario -->
                    <svg class="user-icon"><use href="#user"></use></svg>
                    <input 
                        type="text" 
                        id="login" 
                        name="login" 
                        minlength="5"
                        placeholder="USUARIO O CORREO ELECTRÓNICO" 
                        value="<?php echo htmlspecialchars($oldLogin); ?>"
                        required
                    >
                </div>

                <!-- CAMPO DE TIPO INPUT: contraseña -->
                <div class="field-input <?php echo !empty($errorFields['password']) ? 'input-error' : ''; ?>">
                    <!-- Icono de contraseña -->
                    <svg class="pass-icon"><use href="#password"></use></svg>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        minlength="8"
                        placeholder="CONTRASEÑA" 
                        required
                    >
                    <!-- Botón para mostrar / ocultar contraseña -->
                    <button type="button" class="toggle-password" aria-label="Mostrar contraseña">
                        <!-- Icono de ojo -->
                        <svg class="icon"><use xlink:href="#eye"></use></svg>
                    </button>
                </div> 

                <!--BOTÓN PARA INICIAR LA SESIÓN -->
                <button type="submit" class="btn-login"> 
                    INGRESAR
                </button>
                <!-- Línea horizontal divisora -->
                <hr class="divider-line-login">

                <!-- CONTENEDOR DE LOS ENLACES DE RECUPERACIÓN -->
                <div class="forgot-links"> 
                    <a href="">OLVIDÉ MI USUARIO</a>
                    <a href="">OLVIDÉ MI CONTRASEÑA</a>
                </div>
                <!-- ENLACE AL REGISTRO -->
                <p class="register-link"> 
                    ¿NO TIENES UN USUARIO? <a href="registration.php">REGÍSTRATE</a>
                </p>
            </form>
        </div>
    </div>

    <!-- Script para mostrar el rol del usuario en el userRole -->
    <script>
        window.oldRole = "<?php echo $oldRole; ?>";
        window.errorFields = <?php echo json_encode($errorFields); ?>;
    </script>

    <script src="../assets/js/icons.js"></script>
    <script src="../assets/js/header_login.js"></script>
    <script src="../assets/js/dropdowns.js"></script>
    <!-- Scripts específicos para esta página -->
    <script src="../assets/js/toggle_password.js"></script>
    <script src="../assets/js/login.js"></script>
</body>

</html>