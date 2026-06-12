<?php
// ==========================================================================
// ARCHIVO: INTERFAZ DEL AGENDAMIENTO DE CITAS (SCHEDULING)
// ==========================================================================

// Middleware de seguridad y control de accesos
require_once("../php/auth.php");
require_once("../php/permissions.php");

// Módulo exclusivo para pacientes
requireRole("paciente");

$role = $_SESSION["role"] ?? "";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PsicoGest | Agendamiento de citas</title>
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
            <!-- Titulo de la página -->
            <h2> 
                <svg class="icon-title">
                    <use href="#new-appointment"></use>
                </svg>
                AGENDAMIENTO
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
        <div class="card-scheduling"> 
            <!-- FORMULARIO -->
            <form class="appointment-form"> 
                <!-- FILA: TIPO DE SESIÓN Y PSICÓLOGO -->
                <div class="row two-cols"> 
                    <!-- CONTENEDOR DEL CAMPO: TIPO DE SESIÓN -->
                    <div class="field-container"> 
                        <svg class="icon"><use href="#calendar-edit"></use></svg>
                        <div class="input-box"> 
                            <!-- DROPDOWN PERSONALIZADO -->
                            <div class="dropdown" id="session-dropdown"> 
                                <div class="dropdown-selected"> 
                                    <span class="dropdown-text">TIPO DE SESIÓN</span> 
                                    <svg class="arrow-icon"><use href="#down-arrow"></use></svg> 
                                </div>
                                <ul class="dropdown-options">
                                    <li data-value="presencial">PRESENCIAL</li>
                                    <li data-value="virtual">VIRTUAL</li>
                                </ul>
                            </div>
                        </div>  
                    </div>
                    
                    <!-- CONTENEDOR DEL CAMPO: PSICOLOGO -->    
                    <div class="field-container"> 
                        <svg class="icon"><use href="#human-brain"></use></svg>
                        <div class="input-box"> 
                            <!-- DROPDOWN PERSONALIZADO -->
                            <div class="dropdown" id="psychologist-dropdown"> 
                                <div class="dropdown-selected"> 
                                    <span class="dropdown-text">PSICÓLOGO</span> 
                                    <svg class="arrow-icon"><use href="#down-arrow"></use></svg> 
                                </div>
                                <ul class="dropdown-options"> 
                                    <li data-value="ps1">AMELIA PEREZ MOLINA (CLÍNICA)</li>
                                    <li data-value="ps2">BEATRIZ MARIA SUAZA RUA (INFANCIA Y ADOLESCENCIA)</li>
                                    <li data-value="ps3">BRUNO CASTA BRENTT (NEUROPSICOLOGÍA)</li>
                                    <li data-value="ps4">SANTIAGO BOTERO CALLE (CLÍNICA)</li>
                                </ul>                                    
                            </div>
                        </div> 
                    </div>
                </div>

                <!-- FILA FECHA: MES, DIA Y HORA --> 
                <div class="row three-cols"> 
                    <!-- CONTENEDOR DEL CAMPO: MES -->
                    <div class="field-container"> 
                        <svg class="icon"><use href="#calendar-month"></use></svg>
                        <div class="input-box"> 
                            <div class="dropdown" id="month-dropdown"> 
                                <div class="dropdown-selected"> 
                                    <span class="dropdown-text">MES</span> 
                                    <svg class="arrow-icon"><use href="#down-arrow"></use></svg> 
                                </div>
                                <div class="dropdown-options month-picker"> 
                                    <div class="month-header"> 
                                        <button type="button" class="year-btn" id="prev-year">◀</button>
                                        <span class="year-label">2026</span> 
                                        <button type="button" class="year-btn" id="next-year">▶</button> 
                                    </div>
                                    <div class="month-grid"> 
                                        <div data-value="0">ENE.</div>
                                        <div data-value="1">FEB.</div>
                                        <div data-value="2">MAR.</div>
                                        <div data-value="3">APR.</div>
                                        <div data-value="4">MAY.</div>
                                        <div data-value="5">JUN.</div>
                                        <div data-value="6">JUL.</div>
                                        <div data-value="7">AGO.</div>
                                        <div data-value="8">SEP.</div>
                                        <div data-value="9">OCT.</div>
                                        <div data-value="10">NOV.</div>
                                        <div data-value="11">DIC.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CONTENEDOR DEL CAMPO: DIA -->
                    <div class="field-container"> 
                        <svg class="icon"><use href="#calendar-day"></use></svg>
                        <div class="input-box"> 
                            <div class="dropdown disabled" id="day-dropdown">  
                                <div class="dropdown-selected"> 
                                    <span class="dropdown-text">DÍA</span> 
                                    <svg class="arrow-icon"><use href="#down-arrow"></use></svg> 
                                </div>
                                <div class="dropdown-options day-picker"> 
                                    <div class="day-header"> 
                                        <span class="day-month-label">MES AÑO</span>
                                    </div>
                                    <div class="week-header">  
                                        <span>L</span><span>M</span><span>M</span><span>J</span><span>V</span><span>S</span><span>D</span>
                                    </div>
                                    <div class="day-grid"></div> 
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CONTENEDOR DEL CAMPO: HORA -->    
                    <div class="field-container"> 
                        <svg class="icon"><use href="#calendar-hour"></use></svg>
                        <div class="input-box"> 
                            <div class="dropdown disabled" id="hour-dropdown"> 
                                <div class="dropdown-selected"> 
                                    <span class="dropdown-text">HORA</span> 
                                    <svg class="arrow-icon"><use href="#down-arrow"></use></svg> 
                                </div>
                                <ul class="dropdown-options"> 
                                    <li data-value="hour">08:00 A.M.</li>
                                    <li data-value="hour">09:00 A.M.</li>
                                    <li data-value="hour">10:30 A.M.</li>
                                    <li data-value="hour">11:30 A.M.</li>
                                    <li data-value="hour">03:00 P.M.</li>
                                    <li data-value="hour">04:40 P.M.</li>
                                </ul>
                            </div>
                        </div> 
                    </div>
                </div>

                <!-- FILA FINAL: MOTIVO DE CONSULTA -->
                <div class="row reason-appointment">
                    <div class="field-container"> 
                        <p class="form-disclaimer"> 
                            Escribe un motivo de consulta breve (por ejemplo: <strong>estrés, problemas familiares, etc.</strong>). Es solo para darle contexto al psicólogo(a), tú puedes hablar de lo que quieras en la sesión.
                        </p>
                    </div>
                    <div class="field-container full-width text-area-container"> 
                        <textarea id="reason" name="reason" maxlength="80" placeholder="MOTIVO DE CONSULTA"></textarea> 
                        <span class="char-hint">Máximo 80 caracteres</span> 
                    </div>
                </div>

                <!-- BOTÓN DE AGENDAMIENTO (Se movió dentro del formulario para heredar el comportamiento del flujo) -->
                <div class="form-actions">
                    <button type="submit" class="new-appmnt-button">AGENDAR ESTA CITA</button> 
                </div>
            </form>
        </div>
    </main>

    <!-- Script para mostrar el rol del usuario en el userRole -->
    <script>
        window.USER_ROLE = "<?= $_SESSION['role'] ?? 'usuario' ?>";
    </script>

    <script src="../assets/js/icons.js"></script>
    <script src="../assets/js/header.js"></script>
    <script src="../assets/js/dropdowns.js"></script>
    
    <!-- Script específico para esta página -->
    <script src="../assets/js/scheduling.js"></script> 
</body>
</html>