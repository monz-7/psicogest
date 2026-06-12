<?php
// ==========================================================================
// ARCHIVO: BIBLIOTECA DE UTILIDADES GLOBALES
// - Procesar banderas de países
// - Procesar conversiones de nombre del país y mapeos internacionales ISO
// ==========================================================================

// Extrae el código ISO de 2 letras de un país basándose en su prefijo telefónico
// Mapea códigos de marcación internacional ordenados por longitud

if (!function_exists('getCountryCodeByPhone')) {
    function getCountryCodeByPhone($phone) {
        $phone = trim($phone);
        
        // Si el número no empieza con +, aborta de forma segura
        if (strpos($phone, '+') !== 0) {
            return 'un'; // Bandera de Naciones Unidas (desconocido)
        }

        // Listado completo y estandarizado de prefijos mundiales 
        // Pre-ordenado de mayor a menor longitud
        $countryCodes = [
            // Prefijos de 4 dígitos
            '+1-242'=> 'bs', '+1-441'=> 'bm', 
            '+351' => 'pt', '+352' => 'lu', '+353' => 'ie', '+354' => 'is', '+358' => 'fi', 
            '+372' => 'ee', '+380' => 'ua', '+501' => 'bz', '+502' => 'gt', '+503' => 'sv', 
            '+504' => 'hn', '+505' => 'ni', '+506' => 'cr', '+507' => 'pa', '+508' => 'pm', 
            '+509' => 'ht', '+591' => 'bo', '+592' => 'gy', '+593' => 'ec', '+594' => 'gf', 
            '+595' => 'py', '+596' => 'mq', '+597' => 'sr', '+598' => 'uy', '+599' => 'cw', 
            '+961' => 'lb', '+962' => 'jo', '+964' => 'iq', '+965' => 'kw', '+966' => 'sa', 
            '+971' => 'ae', '+972' => 'il', '+974' => 'qa', '+995' => 'ge', '+212' => 'ma', 
            '+234' => 'ng', '+254' => 'ke',
            
            // Prefijos de 3 dígitos
            '+297' => 'aw', '+376' => 'ad', '+880' => 'bd',
            '+30'  => 'gr', '+31'  => 'nl', '+32'  => 'be', '+33'  => 'fr', '+34'  => 'es', 
            '+36'  => 'hu', '+39'  => 'it', '+40'  => 'ro', '+41'  => 'ch', '+43'  => 'at', 
            '+44'  => 'gb', '+45'  => 'dk', '+46'  => 'se', '+47'  => 'no', '+48'  => 'pl', 
            '+49'  => 'de', '+51'  => 'pe', '+52'  => 'mx', '+53'  => 'cu', '+54'  => 'ar', 
            '+55'  => 'br', '+56'  => 'cl', '+57'  => 'co', '+58'  => 've', '+60'  => 'my', 
            '+61'  => 'au', '+62'  => 'id', '+63'  => 'ph', '+64'  => 'nz', '+65'  => 'sg', 
            '+66'  => 'th', '+81'  => 'jp', '+82'  => 'kr', '+84'  => 'vn', '+86'  => 'cn', 
            '+90'  => 'tr', '+91'  => 'in', '+92'  => 'pk', '+94'  => 'lk', '+95'  => 'mm', 
            '+98'  => 'ir', '+20'  => 'eg', '+27'  => 'za',
            
            // Prefijos de 2 dígitos o menos
            '+7'   => 'ru',
            '+1'   => 'us' // Controla tanto US como Canadá
        ];

        // Buscar coincidencia exacta del prefijo al inicio de la cadena
        foreach ($countryCodes as $prefix => $code) {
            if (strpos($phone, $prefix) === 0) {
                return $code;
            }
        }
        
        return 'un';
    }
}

// Convierte el nombre de un país a su identificador ISO de 2 letras
if (!function_exists('getCountryCodeByName')) {
    function getCountryCodeByName($countryName) {
        $countryName = trim(mb_strtolower($countryName, 'UTF-8'));
        
        $countryMap = [
            // --- SUDAMÉRICA ---
            'colombia'         => 'co',
            'argentina'        => 'ar',
            'bolivia'          => 'bo',
            'brasil'           => 'br',
            'brazil'           => 'br',
            'chile'            => 'cl',
            'ecuador'          => 'ec',
            'guayana francesa' => 'gf',
            'guyana'           => 'gy',
            'paraguay'         => 'py',
            'perú'             => 'pe',
            'peru'             => 'pe',
            'surinam'          => 'sr',
            'uruguay'          => 'uy',
            'venezuela'        => 've',
            
            // --- CENTROAMÉRICA Y CARIBE ---
            'aruba'            => 'aw',
            'bahamas'          => 'bs',
            'costa rica'       => 'cr',
            'cuba'             => 'cu',
            'curazao'          => 'cw',
            'el salvador'      => 'sv',
            'guatemala'        => 'gt',
            'haití'            => 'ht',
            'haiti'            => 'ht',
            'honduras'         => 'hn',
            'jamaica'          => 'jm',
            'nicaragua'        => 'ni',
            'panamá'           => 'pa',
            'panama'           => 'pa',
            'puerto rico'      => 'pr',
            'república dominicana' => 'do',
            'republica dominicana' => 'do',
            
            // --- NORTEAMÉRICA ---
            'canadá'           => 'ca',
            'canada'           => 'ca',
            'estados unidos'   => 'us',
            'usa'              => 'us',
            'eeuu'             => 'us',
            'ee.uu.'           => 'us',
            'méxico'           => 'mx',
            'mexico'           => 'mx',
            
            // --- EUROPA ---
            'alemania'         => 'de',
            'bélgica'          => 'be',
            'belgica'          => 'be',
            'españa'           => 'es',
            'espana'           => 'es',
            'francia'          => 'fr',
            'irlanda'          => 'ie',
            'italia'           => 'it',
            'países bajos'     => 'nl',
            'paises bajos'     => 'nl',
            'holanda'          => 'nl',
            'portugal'         => 'pt',
            'reino unido'      => 'gb',
            'inglaterra'       => 'gb',
            'uk'               => 'gb',
            'suiza'            => 'ch',

            // --- OTROS ---
            'andorra'          => 'ad',
            'australia'        => 'au',
            'austria'           => 'at',
            'bangladesh'       => 'bd',
            'ciudad del vaticano' => 'va',
            'china'            => 'cn',
            'corea del sur'    => 'kr',
            'emiratos árabes unidos' => 'ae',
            'emiratos arabes unidos' => 'ae',
            'grecia' => 'gr',
            'israel'           => 'il',
            'japón'            => 'jp',
            'japon'            => 'jp',
            'marruecos'        => 'ma',
            'nueva zelanda'    => 'nz',
            'rusia'            => 'ru',
            'sudáfrica'        => 'za',
            'sudafrica'        => 'za',
            'turquía'          => 'tr',
            'qa'               => 'qa',    
            
        ];

        return $countryMap[$countryName] ?? 'un';
    }
}