<?php
// Debug para rutas
echo "<pre>";
echo "=== DEBUG DE RUTAS ===\n\n";

echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'NO DEFINIDO') . "\n";
echo "SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'NO DEFINIDO') . "\n";
echo "PHP_SELF: " . ($_SERVER['PHP_SELF'] ?? 'NO DEFINIDO') . "\n";
echo "QUERY_STRING: " . ($_SERVER['QUERY_STRING'] ?? 'NO DEFINIDO') . "\n";
echo "PATH_INFO: " . ($_SERVER['PATH_INFO'] ?? 'NO DEFINIDO') . "\n\n";

// Procesar como en index.php
$request = $_SERVER['REQUEST_URI'] ?? '';
$path = parse_url($request, PHP_URL_PATH);
$base_path = dirname($_SERVER['SCRIPT_NAME'] ?? '/');

echo "path después de parse_url: $path\n";
echo "base_path: $base_path\n";

// Remover el directorio base de la ruta
if ($base_path !== '/') {
    $path = substr($path, strlen($base_path));
}

echo "path después de remover base_path: $path\n";

$path = trim($path, '/');
echo "path final (trim '/'): '$path'\n\n";

echo "GET parameters:\n";
print_r($_GET);

echo "\n=== FIN DEBUG ===\n";
echo "</pre>";

// Probar acceso directo al archivo
$test_file = __DIR__ . '/controllers/auth/register.php';
echo "\n¿Existe register.php? " . (file_exists($test_file) ? 'SÍ' : 'NO');
if (file_exists($test_file)) {
    echo " - Ruta: $test_file";
}
?>