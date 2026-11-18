<?php
// ==========================================================================
// ARCHIVO: INTERFAZ DE LA CONFIGURACIÓN DEL SISTEMA (ADMINISTRADOR)
// ==========================================================================

// Middleware de seguridad y control de accesos
require_once("../php/auth/auth.php");
require_once("../php/auth/permissions.php");

// Middleware de conexión con la base de datos
require_once("../php/config/db.php");

// Restricción de acceso: solo ADMIN
requireRole("admin");

// Lee las configuraciones y las deja como arreglo: $config['item'] = 'value'
$sql = "
    SELECT 
        item, 
        value 
    FROM settings
";
$result= $conn->query($sql);

if (!$result) {
    die("Error SQL: " . $conn->error);
}

// Convierte el resultado en un arreglo: $config['item'] = 'value'
$config = [];
while ($row = $result->fetch_assoc()) {
    $config[$row['item']] = $row['value'];
}
$result->free();

// Captura los valores de configuración actuales
$price    = $config['appointment_price'] ?? '';
$address = $config['clinic_address'] ?? '';

// Captura el rol activo del usuario en la sesión
$role = $_SESSION["role"] ?? "";

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PsicoGest | Configuración global</title>
    
    <link rel="icon" href="../assets/img/icon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../assets/css/base/main.css">
</head>
<body>

    <!-- HEADER --> 
    <div id="header-container"></div>
    
    <!-- CONTENIDO PRINCIPAL -->
    <main class="main-content"> 
        
        <!-- BARRA SUPERIOR -->
        <div class="top-bar">        
            <h2 class="top-bar-title">
                <svg class="icon-title">
                    <use href="#config"></use>
                </svg>
                CONFIGURACIÓN GLOBAL DEL SISTEMA
            </h2>

            <a class="return-button" href="home.php"> 
                <svg class="icon">
                    <use href="#home"></use>
                </svg>
                VOLVER AL INICIO
            </a>
        </div>

        <!-- TARJETA CONTENEDORA -->
        <div class="config-card">

            <!-- MENSAJE DE ÉXITO -->
            <div class="success-box hidden" id="config-success-box" ></div>

            <!-- FILA 1: Tarjetas -->

            <!-- Contenedor de las tarjetas de los elementos de configuración actuales -->
            <div class="items-container">

                <!-- Contenedor de cada tarjeta por cada item de configuración -->
                <div class="item-container">

                    <!-- Tarjeta con item de configuración -->
                    <span class="item-title">TARIFA ACTUAL DE LA CONSULTA (COP)</span> 

                    <div class="item-card">

                        <svg class="icon" id="item-config-icon">
                            <use href="#money-symbol"></use>
                        </svg>

                        <span class="item-value" id="value-appointment-price">
                            <?= number_format((int)$price, 0, ',', '.') ?>
                        </span>
                        <small class="item-note">*Aplica para todos los psicólogos del consultorio.</small>
                    
                    </div>    

                </div> 

                <!-- Contenedor de cada tarjeta por cada item de configuración -->
                <div class="item-container">

                    <!-- Tarjeta con item de configuración -->
                    <span class="item-title">DIRECCIÓN ACTUAL DEL CONSULTORIO</span> 

                    <div class="item-card">

                        <svg class="icon" id="item-config-icon">
                            <use href="#location"></use>
                        </svg>

                        <span class="item-value" id="value-clinic-address">
                            <?= htmlspecialchars($address) ?>
                        </span>
                        <small class="item-note"></small>
                    
                    </div>    

                </div>    
            </div> 
            
            <!-- FILA 2: Línea divisora -->
                            
            <hr class="line-h-config">

            <!-- FILA 3: Botones de acción -->
            
            <div class="config-buttons-container">

                <button type="button" class="edit-config-button" 
                        data-item="appointment_price"
                        data-title="CAMBIAR TARIFA"
                        data-type="number"
                        data-placeholder="NUEVA TARIFA (COP)"
                        data-icon="#money-symbol">
                    <svg class="icon"><use href="#edit"></use></svg>
                    CAMBIAR TARIFA
                </button>

                <button type="button" class="edit-config-button" 
                        data-item="clinic_address"
                        data-title="CAMBIAR DIRECCIÓN"
                        data-type="text"
                        data-placeholder="NUEVA DIRECCIÓN"
                        data-icon="#location">
                    <svg class="icon"><use href="#edit"></use></svg>
                    CAMBIAR DIRECCIÓN
                </button>
            </div>   

        </div>

        <!-- Modal para editar un ajuste -->
        <div class="modal hidden" id="config-modal">
            <div class="modal-content admin-change-password">

                <h1 class="modal-title" id="config-modal-title"></h1>

                <!-- Mensaje de error -->
                <div id="config-modal-error" class="error-box hidden"></div>

                <!-- Campo del nuevo valor -->
                <div class="field-input">
                    <svg class="icon"><use id="config-modal-icon" href="#edit"></use></svg>
                    <input type="text" id="config-modal-input" class="input">
                </div>

                <!-- Botones -->
                <div class="modal-buttons admin-modal">
                    <button type="button" id="config-cancel-button" class="close-button">
                        CANCELAR
                    </button>
                    <button type="button" id="config-save-button" class="save-button-modal">
                        GUARDAR
                    </button>
                </div>

            </div>
        </div>
        
        

           
        
    </main>

    <!-- Script para mostrar el rol del usuario en el userRole -->
    <script>
        window.USER_ROLE = "<?= $_SESSION['role'] ?? 'usuario' ?>";
    </script>

    <script src="../assets/js/components/icons.js"></script>
    <script src="../assets/js/components/menu_sidebar.js"></script>
    <script src="../assets/js/components/header.js"></script>

    <!-- Script específico para esta página-->
    <script src="../assets/js/pages/admin_config.js"></script>
</body>
</html>