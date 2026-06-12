<?php

// ==========================================================================
// ARCHIVO: INTERFAZ DEL DIRECTORIO
// ==========================================================================

// Middleware de seguridad y control de accesos
require_once("../php/auth.php");
require_once("../php/permissions.php");

// Middleware de conexión con la base de datos
require_once("../php/db.php");

// Restricción de acceso: solo pacientes
requireRole("paciente");

// Captura el rol activo del usuario en la sesión
$role = $_SESSION["role"] ?? "";

// CONSULTA PARA OBTENER LOS PSICÓLOGOS ACTIVOS (READ)
$sql = "
    SELECT
        up.uuid_user_profile,
        up.names,
        up.surnames,
        up.phone_number,
        c.email,
        c.status,
        p.license_number,
        GROUP_CONCAT(
            s.name
            ORDER BY s.name
            SEPARATOR ', '
        ) AS specialties
    FROM user_profile up
    INNER JOIN credential c
        ON up.uuid_credential = c.uuid_credential
    INNER JOIN psychologist p
        ON up.uuid_user_profile = p.uuid_user_profile
    LEFT JOIN psychologist_specialty ps
        ON p.uuid_psychologist = ps.uuid_psychologist
    LEFT JOIN specialty s
        ON ps.uuid_specialty = s.uuid_specialty
    WHERE c.role = 'psicologo' 
      AND c.status = 'activo'
    GROUP BY
        up.uuid_user_profile,
        up.names,
        up.surnames,
        up.phone_number,
        c.email,
        c.status,
        p.license_number
    ORDER BY up.names ASC
";

$result = $conn->query($sql);

if (!$result) {
    die("Error SQL: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>PsicoGest | Directorio de psicólogos</title>
    <link rel="icon" href="../assets/icon.ico" type="image/x-icon">
    
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>
    <!-- HEADER -->
    <div id="header-container"></div>
    <!-- CONTENIDO PRINCIPAL -->
    <main class="main-content">
        <!-- BARRA SUPERIOR -->
        <div class="top-bar">
            <h2>
                <svg class="icon-title">
                    <use href="#directory"></use>
                </svg>
                DIRECTORIO
            </h2>

            <a href="home.php" class="return-button">
                <svg class="icon">
                    <use href="#home"></use>
                </svg>
                VOLVER AL INICIO
            </a>
        </div>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <!-- TARJETA CONTENEDORA -->
                <div class="card-directory">
                  <!-- DETALLES DEL PSICÓLOGO -->
                    <div class="psycho-detail">
                        
                        <svg><use href="#human-brain"></use></svg>
                        <!-- LÍNEA DIVISORA -->
                        <div class="divider-v"></div>
                        <!-- INFORMACIÓN DEL PSICÓLOGO -->
                        <div class="info-ps-content">
                            <h3><?= htmlspecialchars(mb_strtoupper($row['names'] . ' ' . $row['surnames'])) ?></h3>
                            
                            <p>
                                <strong>Especialidades:</strong> <?= htmlspecialchars($row['specialties'] ?? 'No especificadas') ?>.<br>
                                <strong>Tarjeta profesional:</strong> <?= htmlspecialchars($row['license_number']) ?>.<br>
                                <strong>Teléfono:</strong> <?= htmlspecialchars($row['phone_number']) ?>.<br>
                                <strong>Correo:</strong> <?= htmlspecialchars($row['email']) ?>.
                            </p>
                            <!-- BOTÓN DE ACCIONES -->
                            <a class="scheduling-with" href="scheduling.php">
                                Agendar una cita con este(a) psicólogo(a)
                            </a>
                            </div>
                        </div>

                    </div>
                </div>

            <?php endwhile; ?>
        <?php else: ?>
            <!-- MENSAJE SI NO HYA PSICÓLOGOS REGISTRADOS -->
            <div class="card-directory">
                <p style="text-align: center; padding: 20px;">
                    Actualmente no hay psicólogos disponibles en el directorio.
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