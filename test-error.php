<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>";
echo "<html><head><title>Test Error</title></head>";
echo "<body>";
echo "<h1>Test de PHP y Errores</h1>";

// Verificar si podemos incluir config/smtp.php
echo "<h2>Probando inclusión de config/smtp.php</h2>";
try {
    require_once __DIR__ . '/config/smtp.php';
    echo "<p style='color: green;'>✓ config/smtp.php incluido correctamente</p>";
    
    // Verificar constantes definidas
    echo "<h3>Constantes SMTP:</h3>";
    echo "<ul>";
    echo "<li>SMTP_HOST: " . (defined('SMTP_HOST') ? SMTP_HOST : 'No definida') . "</li>";
    echo "<li>SMTP_USERNAME: " . (defined('SMTP_USERNAME') ? substr(SMTP_USERNAME, 0, 3) . '***' : 'No definida') . "</li>";
    echo "<li>SMTP_PASSWORD: " . (defined('SMTP_PASSWORD') ? '***' . (strlen(SMTP_PASSWORD) > 0 ? ' (configurada)' : ' (vacía)') : 'No definida') . "</li>";
    echo "</ul>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error al incluir config/smtp.php: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

// Verificar PHPMailer
echo "<h2>Probando PHPMailer</h2>";
$phpmailer_path = __DIR__ . '/vendor/PHPMailer/src/PHPMailer.php';
if (file_exists($phpmailer_path)) {
    echo "<p style='color: green;'>✓ PHPMailer encontrado en: " . htmlspecialchars($phpmailer_path) . "</p>";
    
    try {
        require_once $phpmailer_path;
        require_once __DIR__ . '/vendor/PHPMailer/src/SMTP.php';
        require_once __DIR__ . '/vendor/PHPMailer/src/Exception.php';
        echo "<p style='color: green;'>✓ PHPMailer cargado correctamente</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Error al cargar PHPMailer: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
} else {
    echo "<p style='color: orange;'>⚠ PHPMailer no encontrado en la ruta esperada</p>";
}

// Verificar base de datos
echo "<h2>Probando conexión a base de datos</h2>";
try {
    require_once __DIR__ . '/config/database.php';
    $db = getDBConnection();
    echo "<p style='color: green;'>✓ Conexión a base de datos exitosa</p>";
    
    // Verificar tabla users
    $stmt = $db->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    echo "<p>Usuarios en la base de datos: " . htmlspecialchars($result['count']) . "</p>";
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Error de base de datos: " . htmlspecialchars($e->getMessage()) . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error general: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Fin
echo "<hr>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>Server Software:</strong> " . ($_SERVER['SERVER_SOFTWARE'] ?? 'N/A') . "</p>";
echo "</body></html>";
?>