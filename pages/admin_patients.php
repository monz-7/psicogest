<?php
// ==========================================================================
// ARCHIVO: INTERFAZ DE LA GESTIÓN DE LOS PACIENTES (VISTA DEL ADMIN)
// ==========================================================================

// Middleware de seguridad y control de accesos
require_once("../php/auth.php");
require_once("../php/permissions.php");

// Middleware de conexión con la base de datos
require_once("../php/db.php");

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
        up.phone_number,
        c.email,
        c.status,
        up.country
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
    
    <link rel="icon" href="../assets/icon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>

    <div id="header-container"></div>
    
    <main class="main-content"> 
        
        <div class="top-bar">        
            <h2>
                <svg class="icon-title">
                    <use href="#patients"></use>
                </svg>
                GESTIÓN DE PACIENTES
            </h2>

            <a href="home.php" class="return-button"> 
                <svg class="icon">
                    <use href="#home"></use>
                </svg>
                VOLVER AL INICIO
            </a>
        </div>

        <div class="table-patients-card">
            <div class="table-container">
                <table class="users-table">
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
                                                <button class="btn-edit disabled" disabled>
                                                    ACTUALIZAR
                                                </button>
                                                <span class="tooltip-text">
                                                    Usuario inactivo. No se puede actualizar.
                                                </span>
                                            </div>
                                        <?php else: ?>
                                            <button 
                                                type="button" 
                                                class="btn-edit"
                                                onclick='editUser(<?= json_encode($row); ?>, "patient")'
                                            >
                                                ACTUALIZAR
                                            </button>
                                        <?php endif; ?>

                                        <?php if($row['status'] === 'activo'): ?>
                                            <a
                                                href="../php/soft_delete.php?uuid=<?= urlencode($row['uuid_user_profile']); ?>&status=inactivo&redirect=admin_patients.php"
                                                class="btn-block"
                                                onclick="return confirm('¿Seguro que quieres BLOQUEAR este paciente?');"
                                            >
                                                BLOQUEAR
                                            </a>
                                        <?php else: ?>
                                            <a
                                                href="../php/soft_delete.php?uuid=<?= urlencode($row['uuid_user_profile']); ?>&status=activo&redirect=admin_patients.php"
                                                class="btn-unblock"
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
            <span class="message">
                No hay más pacientes registrados.
            </span>
        <?php endif; ?>
    </main>

    <!-- Script para mostrar el rol del usuario en el userRole -->
    <script>
        window.USER_ROLE = "<?= $_SESSION['role'] ?? 'usuario' ?>";
    </script>

    <script src="../assets/js/icons.js"></script>
    <script src="../assets/js/header.js"></script>
    <!-- Script específico para esta página-->
    <script src="../assets/js/update_user.js"></script>
</body>
</html>