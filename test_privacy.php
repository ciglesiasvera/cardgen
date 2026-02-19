<?php
// Test simple para verificar la página de políticas de privacidad
echo "=== Test de página de políticas de privacidad ===\n\n";

// 1. Verificar que los archivos existen
echo "1. Verificando archivos...\n";
$files = [
    'controllers/privacy.php',
    'views/privacy.php',
    'views/layouts/base.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "  - $file: EXISTE\n";
    } else {
        echo "  - $file: NO EXISTE\n";
    }
}

// 2. Verificar que el enlace en el footer está correcto
echo "\n2. Verificando enlace en footer...\n";
$base_content = file_get_contents('views/layouts/base.php');
if (strpos($base_content, 'href="/privacy"') !== false) {
    echo "  - Enlace '/privacy' encontrado en base.php: SI\n";
} else {
    echo "  - Enlace '/privacy' encontrado en base.php: NO\n";
}

// 3. Verificar contenido de la página de políticas
echo "\n3. Verificando contenido de la página...\n";
$privacy_content = file_get_contents('views/privacy.php');
$required_sections = [
    'Política de Privacidad – CardGen Pro',
    'Introducción y Aceptación',
    'Responsable del Tratamiento de Datos',
    'Datos que Recopilamos',
    'Finalidades del Tratamiento',
    'privacidad@skylabs.cl'
];

foreach ($required_sections as $section) {
    if (strpos($privacy_content, $section) !== false) {
        echo "  - Sección '$section': ENCONTRADA\n";
    } else {
        echo "  - Sección '$section': NO ENCONTRADA\n";
    }
}

// 4. Verificar que la página se puede acceder
echo "\n4. Verificando accesibilidad...\n";
// Simular acceso básico
session_start();
$_SESSION = []; // Limpiar sesión para test

// Incluir el controlador
ob_start();
require_once 'controllers/privacy.php';
$output = ob_get_clean();

if (strpos($output, 'Política de Privacidad') !== false) {
    echo "  - Página se renderiza correctamente: SI\n";
} else {
    echo "  - Página se renderiza correctamente: NO\n";
}

echo "\n=== Test completado ===\n";
?>