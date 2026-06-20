<?php

// ==========================================================================
// ARCHIVO: INTERFAZ DE LA GESTIÓN DE LOS PSICÓLOGOS (VISTA DEL ADMIN)
// ==========================================================================

// Middleware de seguridad y control de accesos
require_once("../php/auth/auth.php");
require_once("../php/auth/permissions.php");

// Middleware de conexión con la base de datos
require_once("../php/config/db.php");

// Restricción de acceso: solo ADMIN
requireRole("admin");

// Captura el rol activo del usuario en la sesión
$role = $_SESSION["role"] ?? "";

// CONSULTA PARA OBTENER LOS PSICOLOGOS EN LA TABLA (READ)
$sql = "
    SELECT
        up.uuid_user_profile,
        up.doc_type,
        up.doc_number,
        up.names,
        up.surnames,
        up.phone_number,
        c.email,
        c.status,
        p.license_number
    FROM user_profile up
    INNER JOIN credential c
        ON up.uuid_credential = c.uuid_credential
    INNER JOIN psychologist p
        ON up.uuid_user_profile = p.uuid_user_profile
    WHERE c.role = 'psicologo'
    ORDER BY up.names ASC
";
$result = $conn->query($sql);

if (!$result) {
    die("Error SQL: " . $conn->error);
}

$hasPsychologists = $result->num_rows > 0;
?>

<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>PsicoGest | Gestionar psicólogos</title>
    <link rel="icon" href="../assets/img/icon.ico" type="image/x-icon" />

    <!-- Libreria para los números de teléfono internacionales -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/css/intlTelInput.css"/>
    <link rel="stylesheet" href="../assets/css/base/main.css" />
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
                    <use href="#human-brain"></use>
                </svg>
                GESTIÓN DE PSICÓLOGOS
            </h2>

            <a href="home.php" class="return-button"> 
                <svg class="icon">
                    <use href="#home"></use>
                </svg>
                VOLVER AL INICIO
            </a>
        </div> 
        
        <!-- TARJETA CONTENEDORA -->
        <div class="psychologist-management-card"> 
            
            <!-- Selector de pestañas (Tabs) -->
            <div class="tabs">
                <button 
                    class="tab-button" 
                    data-tab="tab1"
                >
                    PSICÓLOGOS REGISTRADOS
                </button> 
                <button 
                    class="tab-button" 
                    data-tab="tab2"
                >
                    REGISTRAR NUEVO PSICÓLOGO
                </button> 
            </div>

            <div class="tab-container"> 
                <div class="tab-content" id="tab1"> 

                 

                    <div class="filter-bar">
                        <svg class="filter-icon"><use href="#search"></use></svg>
                        <input
                            type="text"
                            class="filter-input"
                            data-target="psychologists-table"
                            data-columns="0,1"
                            data-message="psychologists-message"
                            placeholder="BUSCAR POR NOMBRE O APELLIDO"
                            autocomplete="off"
                        >
                    </div>

                    <div class="table-container">
                        <table class="users-table" id="psychologists-table">
                            <thead>
                                <tr>
                                    <th>NOMBRE(S)</th>
                                    <th>APELLIDOS</th>
                                    <th>DOCUMENTO</th>
                                    <th>NÚMERO</th>
                                    <th>TARJETA PROFESIONAL</th>
                                    <th>TELÉFONO</th>
                                    <th>CORREO</th>
                                    <th>ESTADO</th>
                                    <th>ACCIONES</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if ($result->num_rows > 0): ?>
                                    <?php while($row = $result->fetch_assoc()): ?>
                                        <?php
                                        $docTypes = [
                                            "cc" => "Cédula de Ciudadanía",
                                            "ce" => "Cédula de Extranjería",
                                            "pa" => "Pasaporte",
                                            "ot" => "Otro (Extranjero)",
                                        ];
                                        ?>
                                        <tr id="row-<?php echo $row['uuid_user_profile']; ?>">
                                            <td><?php echo htmlspecialchars($row['names']); ?></td>
                                            <td><?php echo htmlspecialchars($row['surnames']); ?></td>
                                            <td>
                                                <?php 
                                                echo htmlspecialchars(
                                                $docTypes[$row['doc_type']] ?? $row['doc_type']
                                                ); 
                                                ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($row['doc_number']); ?></td>
                                            <td><?php echo htmlspecialchars($row['license_number']); ?></td>
                                            <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
                                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                                            <td>
                                                <span class="status-badge <?= $row['status']; ?>">
                                                    <?= $row['status'] === 'activo' ? 'Activo' : 'Inactivo'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($row['status'] !== 'activo'): ?>
                                                    <div class="tooltip-wrapper">
                                                        <button class="edit-button disabled" disabled>
                                                            ACTUALIZAR
                                                        </button>
                                                        <span class="tooltip-text">
                                                            Usuario inactivo. No se puede actualizar.
                                                        </span>
                                                    </div>
                                                    <?php else: ?>
                                                    <button 
                                                        type="button" 
                                                        class="edit-button"
                                                        onclick='editUser(<?php echo json_encode($row); ?>, "psychologist")'
                                                    >
                                                        ACTUALIZAR
                                                    </button>
                                                <?php endif; ?>

                                                <?php if($row['status'] === 'activo'): ?>
                                                    <a
                                                        href="../php/users/soft_delete.php?uuid=<?php echo urlencode($row["uuid_user_profile"]); ?>&status=inactivo&redirect=admin_psychologists.php"
                                                        class="block-button"
                                                        onclick="return confirm('¿Seguro que quieres BLOQUEAR este psicólogo?');"
                                                    >
                                                        BLOQUEAR
                                                    </a>
                                                    <?php else: ?>
                                                    <a
                                                        href="../php/users/soft_delete.php?uuid=<?php echo urlencode($row["uuid_user_profile"]); ?>&status=activo&redirect=admin_psychologists.php"
                                                        class="unblock-button"
                                                        onclick="return confirm('¿Seguro que quieres DESBLOQUEAR este psicólogo?');"
                                                    >
                                                        DESBLOQUEAR
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                <tr>
                                    <td colspan="9" class="empty-state">
                                        No hay psicólogos registrados.
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($hasPsychologists): ?>
                        <span class="table-message" id="psychologists-message">
                            No hay más psicólogos registrados.
                        </span>
                    <?php endif; ?>
                    
                </div>  
                    
                

                <div class="tab-content" id="tab2"> 

                    <div class="psychologist-form-container">
                        <h3 class="title-form">DATOS DEL PSICÓLOGO</h3>

                        <hr class="line-h-psychologists">

                        <form id="psychologist-form">
                
                            <div class="field">
                                <label for="doc-type">TIPO DE DOCUMENTO</label>

                                <div class="dropdown" id="doc-type-dropdown">
                                    <div class="dropdown-selected">
                                        <span class="dropdown-text">TIPO DE DOCUMENTO</span>
                                        <svg class="arrow-icon"><use href="#down-arrow"></use></svg>
                                    </div>
                                    <ul class="dropdown-options"> 
                                        <li data-value="cc">Cédula de Ciudadanía</li>
                                        <li data-value="ce">Cédula de Extranjería</li>
                                        <li data-value="pa">Pasaporte</li>
                                        <li data-value="ot">Otro (Extranjero)</li>
                                    </ul>
                                </div>
                            </div>   

                            <input type="hidden" id="doc-type" name="doc-type" value="">
                                

                            <div class="field">
                                <label for="doc-number">NÚMERO DE DOCUMENTO</label>
                                <input 
                                    type="text" 
                                    id="doc-number" 
                                    name="doc-number" 
                                    minlength="5"
                                    placeholder="NÚMERO DE DOCUMENTO" 
                                    required
                                >
                            </div>

                            <div class="field">
                                <label for="license-number">TARJETA PROFESIONAL</label>
                                <input 
                                    type="text" 
                                    id="license-number" 
                                    name="license-number" 
                                    minlength="5"
                                    placeholder="TARJETA PROFESIONAL" 
                                    required
                                >
                            </div>

                            <div class="field">
                                <label for="names">NOMBRE(S)</label>

                                <input 
                                    type="text" 
                                    id="names" 
                                    name="names" 
                                    minlength="3"
                                    placeholder="NOMBRE(S)" 
                                    required
                                >
                            </div>

                            <div class="field">
                                <label for="surnames">APELLIDOS</label>
                                <input 
                                    type="text" 
                                    id="surnames" 
                                    name="surnames" 
                                    minlength="3"
                                    placeholder="APELLIDOS" 
                                    required
                                >
                            </div>
                            
            
                            <div class="field">
                                <label for="specialties">ESPECIALIDADES</label>
                                <p>
                                    • Seleccionar una especialidad y hacer clic en "AGREGAR".<br>
                                    • Repetir el proceso para añadir más especialidades.<br>
                                    • Marcar la "x" en caso de querer eliminar alguna especialidad de la selección.
                                </p>

                                <div class="specialty-row">
                                    <div class="dropdown" id="specialty-dropdown">
                                        <div class="dropdown-selected">
                                            <span class="dropdown-text">ESPECIALIDADES</span>
                                            <svg class="arrow-icon"><use href="#down-arrow"></use></svg>
                                        </div>
                                            <ul class="dropdown-options"> 
                                                <li data-value="f2ee4107-5fbd-49bd-a101-98b14782e21e">Clínica</li>
                                                <li data-value="201b1563-5dc9-458b-8bd1-71458f343081">Cognitivo-Conductual</li>
                                                <li data-value="c9f211af-da87-4629-8dba-b78227496851">Familiar y de Pareja</li>
                                                <li data-value="eb0b2f29-c4f2-4aee-a41d-f44d04926914">Humanista</li>
                                                <li data-value="a0c64975-c73c-4dce-a2ba-3519b76c0f97">Infancia y Adolescencia</li>
                                                <li data-value="cca7ae69-95e1-4a60-b916-de4dc9decb8d">Neuropsicología</li>
                                                <li data-value="284a646f-f660-48d8-960a-c3355b6e8165">Psicoanalítica y Psicodinámica</li>
                                                <li data-value="e731a111-a4b6-43a4-82af-7c928ff4fce0">Sexual</li>
                                            </ul>
                                        </div>

                                        <button type="button" class="add-specialty-button" id="add-specialty">
                                            AGREGAR
                                        </button>
                                    </div>
                                </div>   

                            <div id="selected-specialties"></div>

                            <input
                                type="hidden"
                                name="specialties"
                                id="specialties-input"
                            >

                            <div class="field">
                                <label for="phone-input">NÚMERO DE TELÉFONO</label>

                                <input 
                                    type="tel" 
                                    id="phone-input" 
                                    name="phone-input"
                                    placeholder="NÚMERO DE TELÉFONO" 
                                    required
                                >

                                <input type="hidden" name="phone-full" id="phone-full" value="">
                            </div>

                            <div class="field">
                                <label for="email">CORREO ELECTRÓNICO</label>
                                
                                <input 
                                    type="email" 
                                    id="email-input" 
                                    name="email-input" 
                                    placeholder="CORREO ELECTRÓNICO" 
                                    required
                                >
                            </div>

                            <hr class="line-h-psychologists">

                            <button 
                                type="submit" 
                                class="register-psychology-button"
                                id="submit-button"
                            >
                                REGISTRAR PSICÓLOGO
                            </button>    
                        </form>
                    </div>


                    
                </div>
            </div>            
        </div>

        <div id="success-modal" class="modal hidden">
            <div class="modal-content">
                <h1 class="modal-title">PSIC🧠GEST • REGISTRO EXITOSO ✓</h1>

                <div id="modal-body"></div>

                <div class="modal-buttons">
                    <button class="copy-button" id="copy-button">COPIAR CREDENCIALES</button>
                    <button class="close-button" id="close-modal">CERRAR</button>
                </div>
            </div>
        </div>
        
        
    </main> 

    <!-- Script para mostrar el rol del usuario en el userRole -->
    <script>
        window.USER_ROLE = "<?= $_SESSION['role'] ?? 'usuario' ?>";
    </script>

    <!-- Librería para números de teléfono internacionales -->                    
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/js/intlTelInput.min.js"></script>

    <script src="../assets/js/components/icons.js"></script>
    <script src="../assets/js/components/menu_sidebar.js"></script>
    <script src="../assets/js/components/header.js"></script>
    <script src="../assets/js/components/dropdowns.js"></script>
    <!-- Scripts específicos para esta página -->
    <script src="../assets/js/components/intl_phone_input.js"></script>
    <script src="../assets/js/users/update_user.js"></script>
    <!-- Script para el filtrado -->
    <script src="../assets/js/components/user_filter.js"></script>
    <script src="../assets/js/components/tabs_control.js"></script>
    <script src="../assets/js/pages/admin_psychologists.js"></script>
    
    
</body>
</html>