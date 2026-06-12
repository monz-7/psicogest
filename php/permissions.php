<?php

// ==========================================================================
// ARCHIVO: CONTROL DE ACCESO Y SEGURIDAD POR ROLES
// - Verifica los permisos del usuario autenticado en la sesión actual
// - Protege las vistas del sistema
// ==========================================================================

// Verifica que el usuario tenga un rol específico para acceder
function requireRole($role)
{
    if ($_SESSION["role"] !== $role) {
        header("Location: ../pages/home.php");
        exit();
    }
}

// Verifica que el usuario tenga al menos uno de los roles permitidos en el arreglo
function requireRoles(array $roles)
{
    if (!in_array($_SESSION["role"], $roles)) {
        header("Location: ../pages/home.php");
        exit();
    }
}