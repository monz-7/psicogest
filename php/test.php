<?php

// ==========================================================================
// ARCHIVO: PRUEBA DE LA CONEXIÓN
// Se utiliza exclusivamente para verificar que la comunicación
// con la base de datos a través de 'db.php' se realice de manera correcta.
// ==========================================================================

// Incorpora el archivo de configuración y conexión
require_once "db.php";

// Si el script llega a este punto sin lanzar el 'die()' de db.php, la conexión fue exitosa
echo "Conexión exitosa con la base de datos.";