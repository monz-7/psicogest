# PSIC🧠GEST

## PROYECTO DEL SOFTWARE A CONSTRUIR

Sistema web para la gestión de sesiones psicológicas para un consultorio psicológico independiente. Permite administrar usuarios, citas y el seguimiento de la atención psicológica de manera organizada y fácil.

## Características

- Agendamiento y control de citas próximas y pasadas.
- Configuración de horarios disponibles.
- Gestión de pacientes.
- Administración y gestión de usuarios.

## Módulos

| Módulo                    | Descripción                                                                                        |
| ------------------------- | -------------------------------------------------------------------------------------------------- |
| Gestión de usuarios       | Permite registrar, consultar, actualizar y administrar la información de los usuarios del sistema. |
| Gestión de pacientes      | Permite administrar la información de los pacientes y consultar sus datos dentro del sistema.      |
| Gestión de citas          | Permite programar, reprogramar, cancelar y consultar citas psicológicas.                           |
| Gestión de horarios       | Permite configurar y administrar la disponibilidad de atención para la programación de citas.      |
| Autenticación y seguridad | Control de acceso mediante inicio de sesión y validación de permisos según el rol del usuario.     |

## Roles del Sistema

- Administrador
- Psicólogo
- Paciente

# FASE DE DESARROLLO

![Estado](<https://img.shields.io/badge/Estado-En%20desarrollo%20(sin%20terminar)-orange>)

## Tecnologías utilizadas (a la fecha)

HTML — CSS — JavaScript — PHP — MySQL

Herramienta de versionamiento: Git y GitHub

## Aprendiz

**Mónica López Bedoya — Análisis y Desarrollo de Software — SENA — Ficha 3186647**

---

# EVIDENCIAS DESARROLLADAS

## Semana de entrega: 02 de junio al 09 de junio.

| Evidencia                                                            | Código                 |
| -------------------------------------------------------------------- | ---------------------- |
| Codificación de módulos del software stand-alone, web y móvil.       | GA7-220501096-AA3-EV01 |
| Módulos de software stand-alone, web y móvil codificados y probados. | GA7-220501096-AA3-EV02 |

### Actividades desarrolladas

- Configuración del repositorio Git del proyecto.
- Creación de las ramas **main** (vacía) y **develop** para el desarrollo del software.
- Carga de la estructura front-end de las interfaces desarrolladas en evidencias anteriores.
- Finalización de algunas vistas del sistema.
- Implementación del módulo de inicio de sesión funcional.
- Implementación de funcionalidades CRUD en diferentes módulos del sistema:

| Operación  | Funcionalidades implementadas                                                                                                                                                           |
| ---------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **CREATE** | Registro de usuarios en el formulario principal de registro (vista del paciente) y registro de nuevos psicólogos (vista del administrador) para facilitar la escalabilidad del sistema. |
| **READ**   | Consulta de usuarios registrados (vista del administrador) y consulta de psicólogos activos en el directorio (vista del paciente).                                                      |
| **UPDATE** | Actualización de datos de usuarios para la gestión administrativa (vista del administrador).                                                                                            |
| **DELETE** | Implementación de borrado lógico mediante estados **Activo** e **Inactivo** para la gestión de usuarios (vista del administrador).                                                      |
