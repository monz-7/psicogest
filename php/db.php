<?php

// ==========================================================================
// ARCHIVO: CONEXIÓN CON LA BASE DE DATOS
// ==========================================================================

// Parámetros de conexión
$host     = "localhost"; // Servidor local
$user     = "root"; // Usuario por defecto en Xampp
$password = ""; // Contraseña vacía por defecto en Xampp
$database = "psicogest"; // Nombre de la base de datos

// Crea la conexión
$conn = new mysqli(
    $host,
    $user,
    $password,
    $database
);

// Valida el estado de la conexión
if ($conn->connect_error) {
    // Interrumpe la ejecución del script y reporta el error de conexión
    die("Error de conexión: " . $conn->connect_error);
}