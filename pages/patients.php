<?php

// ==========================================================================
// ARCHIVO: INTERFAZ DE PACIENTES (VISTA DEL PSICÓLOGO)
// ==========================================================================

// Middleware de seguridad y control de accesos
require_once("../php/auth/auth.php");
require_once("../php/auth/permissions.php");

// Restricción de acceso: solo psicologos
requireRole("psicologo");

// Captura el rol activo del usuario en la sesión
$role = $_SESSION["role"] ?? "";

?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  
  <title>PsicoGest | Mis pacientes</title>
  <link rel="icon" href="../assets/img/icon.ico" type="image/x-icon" />

  <link rel="stylesheet" href="../assets/css/base/main.css" />
</head>

<body>
  <!-- HEADER -->
  <div id="header-container"></div>
  <!-- CONTENIDO PRINCIPAL -->
    <main class="main-content">
        <!-- BARRA SUPERIOR -->
        <div class="top-bar"> 
            <!-- Titulo de la página -->
            <h2 class="top-bar-title">  
                <svg class="icon-title">
                    <use href="#patients"></use>
                </svg>
                MIS PACIENTES
            </h2>
            <!-- Botón de retorno al inicio -->
            <a href="home.php" class="return-button"> 
                <svg class="icon">
                    <use xlink:href="#home"></use>
                </svg>
                VOLVER AL INICIO
            </a>
        </div>
        <!-- TARJETA CONTENEDORA -->
        <div class="patients-card">

            <!-- Selector de pestañas (Tabs) -->
            <div class="tabs">
                <button class="tab-button" data-tab="tab1">PACIENTES</button>
                <button class="tab-button" data-tab="tab2">REGISTRO DE ASISTENCIA</button>
            </div>

            <!-- Contenedor unificado de contenido -->
            <div class="tab-container">

                <!-- PESTAÑA 1: Pacientes (Activa por defecto) -->
                <div class="tab-content" id="tab1">
                    <div class="instruction-patients">
                        <p>
                            PUEDES ORDENAR LA LISTA DE PACIENTES ALFABÉTICAMENTE (A–Z o Z–A) Y/O
                            FILTRAR POR NOMBRE(S) O APELLIDOS DEL PACIENTE AQUÍ:
                        </p>
                    </div>

                    <div class="filters-container-patients">
                        <div class="order-selector">
                            <!-- Dropdown personalizado para el filtro de meses -->
                            <div class="dropdown" id="order-dropdown">
                                <div class="dropdown-selected">
                                    <span class="dropdown-text">A - Z</span>
                                    <svg class="arrow-icon"><use href="#down-arrow"></use></svg>
                                </div>
                                <ul class="dropdown-options">
                                    <li data-value="order1">A - Z</li>
                                    <li data-value="order2">Z - A</li>
                                </ul>
                            </div>
                        </div>

                        <div class="filter-bar">
                            <svg class="filter-icon">
                                <use href="#search"></use>
                            </svg>
                            <input
                                type="text"
                                class="filter-input"
                                data-target=""
                                data-columns="0,1"
                                data-message=""
                                placeholder="BUSCAR POR NOMBRE O APELLIDO"
                                autocomplete="off"
                            >
                        </div>
                    </div>

                    <hr class="line-h-patients">

                    <!-- Paciente 1 (ejemplo estático) -->

                    <div class="patient-content">  
                        <span class="patient-name">
                            <svg class="icon"><use href="#user"></use></svg>
                            ANTONELLA MARÍA ROJAS CABRERA
                        </span>
                        <!-- Línea divisora -->
                        <hr class="line-v-patients">
                        <!-- Botones -->
                        <div class="patients-buttons-container"> 
                            <button class="patients-button" id="view-patient-button">VER PACIENTE</button>
                            <button class="patients-button" id="block-user-button">BLOQUEAR USUARIO</button>
                        </div>
                    </div>

                    <hr class="line-h-patients">


                    <!-- Paciente 2 (ejemplo estático) -->
                    <div class="patient-content">  
                        <span class="patient-name">
                            <svg class="icon"><use href="#user"></use></svg>
                            JUAN MONTERO LÓPEZ
                        </span>
                        <!-- Línea divisora -->
                        <hr class="line-v-patients">
                        <!-- Botones -->
                        <div class="patients-buttons-container"> 
                            <button class="patients-button" id="view-patient-button">VER PACIENTE</button>
                            <button class="patients-button" id="block-user-button">BLOQUEAR USUARIO</button>
                        </div>
                    </div>

                    <hr class="line-h-patients">

                    <!-- Paciente 3 (ejemplo estático) -->
                    <div class="patient-container">
                        <div class="patient-content">  
                            <span class="patient-name">
                                <svg class="icon"><use href="#user"></use></svg>
                                MARIANA ROBLEDO SUAZA
                            </span>
                            <!-- Línea divisora -->
                            <hr class="line-v-patients">
                            <!-- Botones -->
                            <div class="patients-buttons-container"> 
                                <button class="patients-button" id="view-patient-button">VER PACIENTE</button>
                                <button class="patients-button" id="block-user-button">BLOQUEAR USUARIO</button>
                            </div>
                        </div>
                    </div>

                    <hr class="line-h-patients">

                    <!-- Mensajes dinámicos e informativos del historial -->
                    <span class="patients-message" id="">
                        No tienes más pacientes.
                    </span> 

                </div>

                <!-- PESTAÑA 2: Asistencia -->
                <div class="tab-content" id="tab2">
                    <div class="instruction-patients">
                        <p>
                            AQUÍ PUEDES REGISTRAR LA ASISTENCIA DE TUS PACIENTES A SUS SESIONES.<br>
                            <br>
                            PUEDES FILTRAR POR MES Y/O NOMBRE(S) O APELLIDOS DEL PACIENTE AQUÍ:
                        </p>
                    </div>

                    <div class="filters-container-patients">
                        <div class="month-filter">
                            <svg class="icon"><use href="#calendar-month"></use></svg>
                            <div class="filter-box">
                                
                                <!-- Dropdown personalizado para el filtro de meses -->
                                <div class="dropdown-filter" id="month-filter-dropdown">
                                    <div class="dropdown-selected-month">
                                        <span class="dropdown-filter-text">ENERO</span>
                                        <svg class="arrow-icon"><use href="#down-arrow"></use></svg>
                                    </div>
                                    <ul class="dropdown-months-options">
                                        <li data-value="month">ENERO</li>
                                        <li data-value="month">FEBRERO</li>
                                        <li data-value="month">MARZO</li>
                                        <li data-value="month">ABRIL</li>
                                        <li data-value="month">MAYO</li>
                                        <li data-value="month">JUNIO</li>
                                        <li data-value="month">JULIO</li>
                                        <li data-value="month">AGOSTO</li>
                                        <li data-value="month">SEPTIEMBRE</li>
                                        <li data-value="month">OCTUBRE</li>
                                        <li data-value="month">NOVIEMBRE</li>
                                        <li data-value="month">DICIEMBRE</li>
                                    </ul>
                                </div>

                            </div>
                        </div>  


                        <div class="filter-bar">
                            <svg class="filter-icon">
                                <use href="#search"></use>
                            </svg>
                            <input
                                type="text"
                                class="filter-input"
                                data-target=""
                                data-columns="0,1"
                                data-message=""
                                placeholder="BUSCAR POR NOMBRE O APELLIDO"
                                autocomplete="off"
                            >
                        </div>
                    </div>

                    <!-- Cita Historial 1 -->
                    <div class="history-container" data-month="ENERO">
                        <div class="history-content">  
                            <p>
                                <svg class="icon"><use href="#calendar-day"></use></svg>LUNES, 12 DE ENERO DEL 2026<br>
                                <svg class="icon"><use href="#time"></use></svg>09:30 A.M.<br>
                                <svg class="icon"><use href="#patients"></use></svg>MARIANA ROBLEDO SUAZA<br>
                                <svg class="icon"><use href="#check-box"></use></svg>PRESENCIAL<br>
                                <svg class="icon"><use href="#location"></use></svg>CL 10 #43A - 25, TORRE MÉDICA / CONSULTORIO 502 - EL POBLADO, MEDELLÍN<br>
                                <svg class="icon"><use href="#money-symbol"></use></svg>70.000<br>
                            </p>
                            <!-- Línea divisora -->
                            <hr class="line-v-history">
                            <!-- Estado de la cita y cambio -->
                            <div class="actions-container">
                                <span class="appointment-status" id="status-2">INCUMPLIDA</span>
                                <div class="status-selector">
                                    <!-- Dropdown personalizado para el cambio de estado -->
                                    <div class="dropdown" id="status-dropdown">
                                        <div class="dropdown-selected">
                                            <span class="dropdown-text">ACTUALIZAR ESTADO</span>
                                            <svg class="arrow-icon"><use href="#down-arrow"></use></svg>
                                        </div>
                                        <ul class="dropdown-options">
                                            <li data-value="status1">PACIENTE ASISTIÓ</li>
                                            <li data-value="status2">PACIENTE NO ASISTIÓ</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>    
                        </div>
                    </div>

                    <!-- Cita Historial 2 -->
                    <div class="history-container" data-month="ENERO">
                        <div class="history-content">
                            <p>
                                <svg class="icon"><use href="#calendar-day"></use></svg>MIÉRCOLES, 21 DE ENERO DEL 2026<br>
                                <svg class="icon"><use href="#time"></use></svg>01:40 P.M.<br>
                                <svg class="icon"><use href="#patients"></use></svg>JUAN MONTERO LÓPEZ<br>
                                <svg class="icon"><use href="#check-box"></use></svg>VIRTUAL<br>
                                <svg class="icon"><use href="#money-symbol"></use></svg>70.000<br>
                            </p>

                            <!-- Línea divisora -->
                            <hr class="line-v-history">
                            <!-- Estado de la cita y cambio -->
                            <div class="actions-container">
                                <span class="appointment-status" id="status-1">CUMPLIDA</span>
                                <div class="status-selector">
                                    <!-- Dropdown personalizado para el cambio de estado -->
                                    <div class="dropdown" id="status-dropdown">
                                        <div class="dropdown-selected">
                                            <span class="dropdown-text">ACTUALIZAR ESTADO</span>
                                            <svg class="arrow-icon"><use href="#down-arrow"></use></svg>
                                        </div>
                                        <ul class="dropdown-options">
                                            <li data-value="status1">PACIENTE ASISTIÓ</li>
                                            <li data-value="status2">PACIENTE NO ASISTIÓ</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>    
                        </div>
                    </div>

                    <!-- Cita Historial 3 -->
                    <div class="history-container" data-month="ENERO">
                        <div class="history-content">
                            <p>
                                
                                <svg class="icon"><use href="#calendar-day"></use></svg>MARTES, 26 DE ENERO DEL 2026<br>
                                <svg class="icon"><use href="#time"></use></svg>04:00 P.M.<br>
                                <svg class="icon"><use href="#patients"></use></svg>ANTONELLA MARÍA ROJAS CABRERA<br>
                                <svg class="icon"><use href="#check-box"></use></svg>VIRTUAL<br>
                                <svg class="icon"><use href="#money-symbol"></use></svg>70.000<br>
                            </p>
                            
                            <!-- Línea divisora -->
                            <hr class="line-v-history">
                            <!-- Estado de la cita y cambio -->
                            <div class="actions-container">
                                <span class="appointment-status" id="status-1">CUMPLIDA</span>
                                <div class="status-selector">
                                    <!-- Dropdown personalizado para el cambio de estado -->
                                    <div class="dropdown" id="status-dropdown">
                                        <div class="dropdown-selected">
                                            <span class="dropdown-text">ACTUALIZAR ESTADO</span>
                                            <svg class="arrow-icon"><use href="#down-arrow"></use></svg>
                                        </div>
                                        <ul class="dropdown-options">
                                            <li data-value="status1">PACIENTE ASISTIÓ</li>
                                            <li data-value="status2">PACIENTE NO ASISTIÓ</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>    
                        </div>
                    </div>

                    <!-- Mensajes dinámicos e informativos del historial -->
                    <span class="attendance-message" id="appointments-message">
                        No tienes más citas en el historial.
                    </span>    

                    <span class="history-message" id="history-message">
                        No tienes citas en el historial de este mes.
                    </span>

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
    <script src="../assets/js/components/dropdowns.js"></script>
    <!-- Script específico para esta página -->
    <script src="../assets/js/components/tabs_control.js"></script>
</body>
</html>
