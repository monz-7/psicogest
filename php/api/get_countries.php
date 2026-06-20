<?php
// ==========================================================================
// ARCHIVO: MAPEO DE PAÍSES EN FORMATO JSON PARA FRONT-END (select_country)
// ==========================================================================

header('Content-Type: application/json; charset=utf-8');


require_once '../helpers/utils.php'; 

try {
    if (!function_exists('getCountryCodeByName')) {
        throw new Exception('Helper no disponible');
    }
    
    // Listado para el frontend:
    $countries = [
        // --- SUDAMÉRICA ---
        ["name" => "Argentina", "code" => "ar"],
        ["name" => "Bolivia", "code" => "bo"],
        ["name" => "Brasil", "code" => "br"],
        ["name" => "Chile", "code" => "cl"],
        ["name" => "Colombia", "code" => "co"],
        ["name" => "Ecuador", "code" => "ec"],
        ["name" => "Guayana Francesa", "code" => "gf"],
        ["name" => "Guyana", "code" => "gy"],
        ["name" => "Paraguay", "code" => "py"],
        ["name" => "Perú", "code" => "pe"],
        ["name" => "Surinam", "code" => "sr"],
        ["name" => "Uruguay", "code" => "uy"],
        ["name" => "Venezuela", "code" => "ve"],

        // --- CENTROAMÉRICA Y CARIBE ---
        ["name" => "Aruba", "code" => "aw"],
        ["name" => "Bahamas", "code" => "bs"],
        ["name" => "Costa Rica", "code" => "cr"],
        ["name" => "Cuba", "code" => "cu"],
        ["name" => "Curazao", "code" => "cw"],
        ["name" => "El Salvador", "code" => "sv"],
        ["name" => "Guatemala", "code" => "gt"],
        ["name" => "Haití", "code" => "ht"],
        ["name" => "Honduras", "code" => "hn"],
        ["name" => "Jamaica", "code" => "jm"],
        ["name" => "Nicaragua", "code" => "ni"],
        ["name" => "Panamá", "code" => "pa"],
        ["name" => "Puerto Rico", "code" => "pr"],
        ["name" => "República Dominicana", "code" => "do"],

        // --- NORTEAMÉRICA ---
        ["name" => "Canadá", "code" => "ca"],
        ["name" => "Estados Unidos", "code" => "us"],
        ["name" => "México", "code" => "mx"],

        // --- EUROPA ---
        ["name" => "Alemania", "code" => "de"],
        ["name" => "Bélgica", "code" => "be"],
        ["name" => "España", "code" => "es"],
        ["name" => "Francia", "code" => "fr"],
        ["name" => "Irlanda", "code" => "ie"],
        ["name" => "Italia", "code" => "it"],
        ["name" => "Países Bajos", "code" => "nl"],
        ["name" => "Portugal", "code" => "pt"],
        ["name" => "Reino Unido", "code" => "gb"],
        ["name" => "Suiza", "code" => "ch"],

        // --- OTROS ---
        ["name" => "Andorra", "code" => "ad"],
        ["name" => "Australia", "code" => "au"],
        ["name" => "Austria", "code" => "at"],
        ["name" => "Bangladesh", "code" => "bd"],
        ["name" => "China", "code" => "cn"],
        ["name" => "Ciudad del Vaticano", "code" => "va"],
        ["name" => "Corea del Sur", "code" => "kr"],
        ["name" => "Emiratos Árabes Unidos", "code" => "ae"],
        ["name" => "Grecia", "code" => "gr"],
        ["name" => "Israel", "code" => "il"],
        ["name" => "Japón", "code" => "jp"],
        ["name" => "Marruecos", "code" => "ma"],
        ["name" => "Nueva Zelanda", "code" => "nz"],
        ["name" => "Rusia", "code" => 'ru'],
        ["name" => "Sudáfrica", "code" => 'za'],
        ["name" => "Turquía", "code" => 'tr'],
        ["name" => "Qatar", "code" => 'qa'],   
    ];

    // Ordenar alfabéticamente respetando tildes y eñes en español
    setlocale(LC_COLLATE, 'es_ES.UTF-8', 'es_ES', 'esp');
    usort($countries, function($a, $b) {
        return strcoll($a['name'], $b['name']);
    });
    echo json_encode(["success" => true, "data" => $countries], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}