<?php
// ==========================================================================
// ARCHIVO: INTERFAZ DE LA GESTIÓN DE LOS PACIENTES (VISTA DEL ADMIN)
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

// CONSULTA PARA OBTENER LOS PACIENTES EN LA TABLA (READ)
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
        c.status
    FROM user_profile up
    INNER JOIN credential c
        ON up.uuid_credential = c.uuid_credential
    INNER JOIN patient p
        ON up.uuid_user_profile = p.uuid_user_profile
    WHERE c.role = 'paciente'
    ORDER BY up.names ASC
";

$result = $conn->query($sql);

if (!$result) {
    die("Error SQL: " . $conn->error);
}

$hasPatients = $result->num_rows > 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PsicoGest | Gestionar pacientes</title>
    
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
                    <use href="#patients"></use>
                </svg>
                GESTIÓN DE PACIENTES
            </h2>

            <a class="return-button" href="home.php"> 
                <svg class="icon">
                    <use href="#home"></use>
                </svg>
                VOLVER AL INICIO
            </a>
        </div>

        <!-- TARJETA CONTENEDORA -->
        <div class="patient-table-card">

            <!-- BARRA DE BÚSQUEDA -->
            <div class="filter-bar">
                <svg class="filter-icon">
                    <use href="#search"></use>
                </svg>
                <input
                    type="text"
                    class="filter-input"
                    data-target="patients-table"
                    data-columns="0,1"
                    data-message="patients-message"
                    placeholder="BUSCAR POR NOMBRE O APELLIDO"
                    autocomplete="off"
                >
            </div>

            <!-- CONTENEDOR DE LA TABLA -->
            <div class="table-container">
                <!-- TABLA -->
                <table class="users-table" id="patients-table">
                    <thead>
                        <tr>
                            <th>NOMBRE(S)</th>
                            <th>APELLIDOS</th>
                            <th>DOCUMENTO</th>
                            <th>NÚMERO</th>
                            <th>FECHA DE NACIMIENTO</th>
                            <th>EDAD</th>
                            <th>TELÉFONO</th>
                            <th>CORREO</th>
                            <th>UBICACIÓN</th>
                            <th>ESTADO</th>
                            <th>ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($hasPatients): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <?php
                                    // Cálculo dinámico de la edad del paciente
                                    $birthDate = new DateTime($row['birth_date']);
                                    $today = new DateTime();
                                    $age = $today->diff($birthDate)->y;

                                    // Diccionario de tipos de documento
                                    $docTypes = [
                                        "cc" => "Cédula de Ciudadanía",
                                        "ce" => "Cédula de Extranjería",
                                        "ti" => "Tarjeta de Identidad",
                                        "pa" => "Pasaporte",
                                        "ot" => "Otro (Extranjero)",
                                    ];
                                ?>
                                <tr id="row-<?= htmlspecialchars($row['uuid_user_profile']); ?>">
                                    <td><?= htmlspecialchars($row['names']); ?></td>
                                    <td><?= htmlspecialchars($row['surnames']); ?></td>
                                    <td>
                                        <?= htmlspecialchars($docTypes[$row['doc_type']] ?? $row['doc_type']); ?>
                                    </td>
                                    <td><?= htmlspecialchars($row['doc_number']); ?></td>
                                    <td><?= htmlspecialchars($row['birth_date']); ?></td>
                                    <td><?= $age; ?> años</td>
                                    <td><?= htmlspecialchars($row['phone_number']); ?></td>
                                    <td><?= htmlspecialchars($row['email']); ?></td>
                                    <td><?= htmlspecialchars($row['country']); ?></td>
                                    <td>
                                        <span class="status-badge <?= htmlspecialchars($row['status']); ?>">
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
                                                onclick='editUser(<?= json_encode($row); ?>, "patient")'
                                            >
                                                ACTUALIZAR
                                            </button>
                                        <?php endif; ?>

                                        <?php if($row['status'] === 'activo'): ?>
                                            <a
                                                href="../php/users/soft_delete.php?uuid=<?= urlencode($row['uuid_user_profile']); ?>&status=inactivo&redirect=admin_patients.php"
                                                class="block-button"
                                                onclick="return confirm('¿Seguro que quieres BLOQUEAR este paciente?');"
                                            >
                                                BLOQUEAR
                                            </a>
                                        <?php else: ?>
                                            <a
                                                href="../php/users/soft_delete.php?uuid=<?= urlencode($row['uuid_user_profile']); ?>&status=activo&redirect=admin_patients.php"
                                                class="unblock-button"
                                                onclick="return confirm('¿Seguro que quieres DESBLOQUEAR este paciente?');"
                                            >
                                                DESBLOQUEAR
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="11" class="empty-state">
                                    No hay pacientes registrados.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php if ($hasPatients): ?>
            <span class="table-message" id="patients-message">
                No hay más pacientes registrados.
            </span>
        <?php endif; ?>
    </main>

    <!-- Script para mostrar el rol del usuario en el userRole -->
    <script>
        window.USER_ROLE = "<?= $_SESSION['role'] ?? 'usuario' ?>";
    </script>

    <script src="../assets/js/components/icons.js"></script>
    <script src="../assets/js/components/menu_sidebar.js"></script>
    <script src="../assets/js/components/header.js"></script>
    <!-- Script específico para esta página-->
    <script src="../assets/js/users/update_user.js"></script>
    <!-- Script para el filtrado -->
    <script src="../assets/js/components/user_filter.js"></script>
</body>
</html>