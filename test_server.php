<?php
// Archivo de prueba para verificar configuración del servidor
echo "<!DOCTYPE html>";
echo "<html><head><title>Test Server</title></head>";
echo "<body style='font-family: Arial, sans-serif; padding: 20px;'>";
echo "<h1>Test del Servidor CardGen</h1>";

// Información del servidor
echo "<h2>Información del Servidor:</h2>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>Server Software:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p><strong>Current Directory:</strong> " . __DIR__ . "</p>";
echo "<p><strong>Request URI:</strong> " . $_SERVER['REQUEST_URI'] . "</p>";

// Verificar extensiones
echo "<h2>Extensiones PHP:</h2>";
echo "<p><strong>PDO MySQL:</strong> " . (extension_loaded('pdo_mysql') ? '✓ Instalada' : '✗ No instalada') . "</p>";
echo "<p><strong>GD Library:</strong> " . (extension_loaded('gd') ? '✓ Instalada' : '✗ No instalada') . "</p>";

// Verificar permisos de directorios
echo "<h2>Verificación de Directorios:</h2>";
$directories = [
    'public/uploads' => 'uploads/',
    'public/uploads/temp' => 'uploads/temp/',
    '.' => 'directorio actual'
];

foreach ($directories as $dir => $name) {
    $writable = is_writable($dir) ? '✓ Escritura permitida' : '✗ Sin permiso de escritura';
    $exists = file_exists($dir) ? '✓ Existe' : '✗ No existe';
    echo "<p><strong>$name:</strong> $exists, $writable</p>";
}

// Verificar archivos de configuración
echo "<h2>Archivos de Configuración:</h2>";
$config_files = [
    'config/database.php',
    'config/smtp.php',
    'index.php',
    '.htaccess'
];

foreach ($config_files as $file) {
    $exists = file_exists($file) ? '✓ Existe' : '✗ No existe';
    echo "<p><strong>$file:</strong> $exists</p>";
}

// Fin
echo "<hr>";
echo "<p><strong>Fecha/Hora:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<p><strong>IP del Visitante:</strong> " . $_SERVER['REMOTE_ADDR'] . "</p>";
echo "</body></html>";
?>