<?php
// Front Controller - CardGen Pro
require_once __DIR__ . '/config/database.php';

startSecureSession();

// Configuración básica
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$base = $protocol . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
define('BASE_URL', rtrim($base, '/'));
define('UPLOAD_PATH', __DIR__ . '/public/uploads/temp/');

// Función helper para construir URLs sin doble slash
function buildUrl($path = '') {
    $path = ltrim($path, '/');
    return BASE_URL . ($path ? '/' . $path : '');
}

// Enrutamiento básico
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);
$base_path = dirname($_SERVER['SCRIPT_NAME']);

// Remover el directorio base de la ruta
if ($base_path !== '/') {
    $path = substr($path, strlen($base_path));
}

$path = trim($path, '/');

// Rutas definidas
$routes = [
    '' => 'views/home.php',
    'home' => 'views/home.php',
    'register' => 'controllers/auth/register.php',
    'login' => 'controllers/auth/login.php',
    'logout' => 'controllers/auth/logout.php',
    'verify' => 'controllers/auth/verify.php',
    'dashboard' => 'controllers/cards/dashboard.php',
    'create' => 'controllers/cards/create.php',
    'preview' => 'controllers/cards/preview.php',
    'export' => 'controllers/cards/export.php',
    'forgot-password' => 'controllers/auth/forgot-password.php',
    'reset-password' => 'controllers/auth/reset-password.php',
];

// Verificar si el usuario está autenticado
function isAuthenticated() {
    return isset($_SESSION['user_id']);
}

// Verificar si el usuario está verificado
function isVerified() {
    return isset($_SESSION['user_verified']) && $_SESSION['user_verified'] === true;
}

// Cargar la ruta correspondiente
if (isset($routes[$path])) {
    $file = $routes[$path];
    
    // Proteger rutas que requieren autenticación
    $protected_routes = ['dashboard', 'create', 'preview', 'export'];
    if (in_array($path, $protected_routes) && !isAuthenticated()) {
        header('Location: ' . buildUrl('login'));
        exit();
    }
    
    // Verificar si el archivo existe
    if (file_exists(__DIR__ . '/' . $file)) {
        require_once __DIR__ . '/' . $file;
    } else {
        http_response_code(404);
        require_once __DIR__ . '/views/errors/404.php';
    }
} else {
    // Ruta no encontrada
    http_response_code(404);
    require_once __DIR__ . '/views/errors/404.php';
}
?>