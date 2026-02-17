<?php
// Test de configuración SMTP para CardGen Pro
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>";
echo "<html><head><title>Test SMTP Configuration</title>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
    .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    h1 { color: #333; border-bottom: 2px solid #4f46e5; padding-bottom: 10px; }
    .section { margin: 20px 0; padding: 15px; border-left: 4px solid #4f46e5; background: #f9fafb; }
    .success { color: #10b981; font-weight: bold; }
    .error { color: #ef4444; font-weight: bold; }
    .warning { color: #f59e0b; font-weight: bold; }
    pre { background: #1f2937; color: #f3f4f6; padding: 15px; border-radius: 5px; overflow-x: auto; }
    .btn { display: inline-block; padding: 10px 20px; background: #4f46e5; color: white; text-decoration: none; border-radius: 5px; margin: 10px 0; }
</style></head><body>";
echo "<div class='container'>";
echo "<h1>Test de Configuración SMTP - CardGen Pro</h1>";

// Sección 1: Verificar configuración básica
echo "<div class='section'>";
echo "<h2>1. Configuración del Servidor</h2>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>Server Host:</strong> " . ($_SERVER['HTTP_HOST'] ?? 'N/A') . "</p>";
echo "<p><strong>Server Software:</strong> " . ($_SERVER['SERVER_SOFTWARE'] ?? 'N/A') . "</p>";
echo "</div>";

// Sección 2: Verificar PHPMailer
echo "<div class='section'>";
echo "<h2>2. Verificación de PHPMailer</h2>";

$phpmailer_paths = [
    __DIR__ . '/vendor/PHPMailer/src/',
    __DIR__ . '/vendor/phpmailer/phpmailer/src/',
    __DIR__ . '/PHPMailer/src/',
];

$phpmailer_loaded = false;
$phpmailer_path = '';

foreach ($phpmailer_paths as $path) {
    if (file_exists($path . 'PHPMailer.php')) {
        $phpmailer_loaded = true;
        $phpmailer_path = $path;
        echo "<p class='success'>✓ PHPMailer encontrado en: " . htmlspecialchars($path) . "</p>";
        
        // Intentar cargar PHPMailer
        try {
            require_once $path . 'PHPMailer.php';
            require_once $path . 'SMTP.php';
            require_once $path . 'Exception.php';
            echo "<p class='success'>✓ PHPMailer cargado correctamente</p>";
        } catch (Exception $e) {
            echo "<p class='error'>✗ Error al cargar PHPMailer: " . htmlspecialchars($e->getMessage()) . "</p>";
            $phpmailer_loaded = false;
        }
        break;
    }
}

if (!$phpmailer_loaded) {
    echo "<p class='warning'>⚠ PHPMailer no encontrado. Se usará la función mail() de PHP.</p>";
}
echo "</div>";

// Sección 3: Verificar configuración SMTP
echo "<div class='section'>";
echo "<h2>3. Configuración SMTP</h2>";

// Intentar cargar configuración SMTP
$config_path = __DIR__ . '/config/smtp.php';
if (file_exists($config_path)) {
    echo "<p class='success'>✓ Archivo de configuración SMTP encontrado</p>";
    
    // Leer valores sin ejecutar todo el archivo
    $config_content = file_get_contents($config_path);
    preg_match("/define\('SMTP_HOST',\s*'([^']+)'/", $config_content, $host_match);
    preg_match("/define\('SMTP_USERNAME',\s*'([^']+)'/", $config_content, $user_match);
    
    if (!empty($host_match[1])) {
        echo "<p><strong>SMTP Host:</strong> " . htmlspecialchars($host_match[1]) . "</p>";
    } else {
        echo "<p class='warning'>⚠ SMTP_HOST no configurado</p>";
    }
    
    if (!empty($user_match[1]) && $user_match[1] !== 'tu_email@gmail.com') {
        echo "<p><strong>SMTP Usuario:</strong> " . htmlspecialchars(substr($user_match[1], 0, 3)) . "***@***</p>";
        echo "<p class='success'>✓ Credenciales configuradas</p>";
    } else {
        echo "<p class='error'>✗ Credenciales SMTP no configuradas (usando valores por defecto)</p>";
    }
} else {
    echo "<p class='error'>✗ Archivo de configuración SMTP no encontrado</p>";
}
echo "</div>";

// Sección 4: Probar conexión SMTP
echo "<div class='section'>";
echo "<h2>4. Prueba de Conexión SMTP</h2>";

if ($phpmailer_loaded && class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    try {
        // Cargar configuración
        require_once $config_path;
        
        // Crear instancia de PHPMailer
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        // Configurar SMTP
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port = SMTP_PORT;
        $mail->SMTPDebug = 2; // Nivel de debug máximo
        $mail->Debugoutput = function($str, $level) {
            echo "<pre>SMTP Debug ($level): " . htmlspecialchars($str) . "</pre>";
        };
        
        echo "<p>Intentando conexión a " . SMTP_HOST . ":" . SMTP_PORT . "...</p>";
        
        // Probar conexión
        $mail->smtpConnect();
        echo "<p class='success'>✓ Conexión SMTP exitosa</p>";
        $mail->smtpClose();
        
        // Probar envío de email (opcional)
        echo "<h3>Probar envío de email:</h3>";
        echo "<form method='POST'>";
        echo "<input type='email' name='test_email' placeholder='Email de prueba' required>";
        echo "<button type='submit' name='send_test' class='btn'>Enviar Email de Prueba</button>";
        echo "</form>";
        
        if (isset($_POST['send_test']) && !empty($_POST['test_email'])) {
            $test_email = $_POST['test_email'];
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USERNAME;
            $mail->Password = SMTP_PASSWORD;
            $mail->SMTPSecure = SMTP_SECURE;
            $mail->Port = SMTP_PORT;
            $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
            $mail->addAddress($test_email);
            $mail->Subject = 'Test SMTP - CardGen Pro';
            $mail->Body = '<h1>¡Test exitoso!</h1><p>El sistema SMTP de CardGen Pro está funcionando correctamente.</p>';
            $mail->isHTML(true);
            
            if ($mail->send()) {
                echo "<p class='success'>✓ Email de prueba enviado a: " . htmlspecialchars($test_email) . "</p>";
            } else {
                echo "<p class='error'>✗ Error al enviar email: " . htmlspecialchars($mail->ErrorInfo) . "</p>";
            }
        }
        
    } catch (Exception $e) {
        echo "<p class='error'>✗ Error en conexión SMTP: " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    }
} else {
    echo "<p class='warning'>⚠ No se puede probar conexión SMTP (PHPMailer no cargado)</p>";
}
echo "</div>";

// Sección 5: Resumen y recomendaciones
echo "<div class='section'>";
echo "<h2>5. Resumen y Recomendaciones</h2>";

if ($phpmailer_loaded) {
    echo "<p class='success'>✓ PHPMailer está correctamente instalado</p>";
    
    // Verificar si las credenciales son las predeterminadas
    if (!empty($user_match[1]) && $user_match[1] === 'tu_email@gmail.com') {
        echo "<p class='error'>✗ <strong>IMPORTANTE:</strong> Debes configurar tus credenciales reales en config/smtp.php</p>";
        echo "<p>Edita el archivo <code>config/smtp.php</code> y reemplaza:</p>";
        echo "<pre>define('SMTP_USERNAME', 'tu_email@gmail.com');
define('SMTP_PASSWORD', 'tu_contraseña_app');
define('SMTP_FROM_EMAIL', 'tu_email@gmail.com');</pre>";
        echo "<p>con tus credenciales reales de Gmail.</p>";
    } else {
        echo "<p class='success'>✓ Credenciales SMTP configuradas</p>";
    }
} else {
    echo "<p class='error'>✗ PHPMailer no está instalado. Se recomienda instalarlo para mejor funcionalidad.</p>";
    echo "<p>Para instalar PHPMailer manualmente:</p>";
    echo "<pre>cd public_html
mkdir -p vendor
cd vendor
wget https://github.com/PHPMailer/PHPMailer/archive/refs/tags/v6.9.1.tar.gz
tar -xzf v6.9.1.tar.gz
mv PHPMailer-6.9.1 PHPMailer
rm v6.9.1.tar.gz</pre>";
}

echo "</div>";

echo "<hr>";
echo "<p><a href='/' class='btn'>Volver a CardGen Pro</a></p>";
echo "</div>";
echo "</body></html>";
?>