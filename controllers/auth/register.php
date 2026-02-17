<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/User.php';

startSecureSession();

$title = 'Registro';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $agree_terms = isset($_POST['agree_terms']);

    // Validaciones
    if (empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Todos los campos son obligatorios';
    } elseif (!isValidEmail($email)) {
        $error = 'Email inválido';
    } elseif (!isValidPassword($password)) {
        $error = 'La contraseña debe tener al menos 8 caracteres';
    } elseif ($password !== $confirm_password) {
        $error = 'Las contraseñas no coinciden';
    } elseif (!$agree_terms) {
        $error = 'Debes aceptar los términos y condiciones';
        } else {
            // Registrar usuario
            $user = new User();
            $result = $user->register($email, $password);
            
            if ($result['success']) {
                // Logging para diagnóstico
                $log_dir = __DIR__ . '/../../logs';
                if (!file_exists($log_dir)) {
                    mkdir($log_dir, 0777, true);
                }
                $log_file = $log_dir . '/register_debug.log';
                $log_message = "[" . date('Y-m-d H:i:s') . "] Usuario registrado: $email\n";
                $log_message .= "Token generado: " . $result['verification_token'] . "\n";
                $log_message .= "BASE_URL: " . BASE_URL . "\n";
                
                // Enviar email de verificación usando SMTP
                $verification_link = BASE_URL . '/verify?token=' . $result['verification_token'];
                $log_message .= "Verification link: $verification_link\n";
                
                // Incluir configuración SMTP y enviar email
                require_once __DIR__ . '/../../config/smtp.php';
                $log_message .= "SMTP_ENABLED: " . (defined('SMTP_ENABLED') && SMTP_ENABLED ? 'true' : 'false') . "\n";
                
                // Guardar log antes de enviar email
                file_put_contents($log_file, $log_message, FILE_APPEND);
                
                // Intentar enviar email
                $email_sent = sendVerificationEmail($email, $result['verification_token']);
                
                // Registrar resultado
                $result_log = "[" . date('Y-m-d H:i:s') . "] Email enviado a $email: " . ($email_sent ? 'Éxito' : 'Falló') . "\n";
                file_put_contents($log_file, $result_log, FILE_APPEND);
                
                if ($email_sent) {
                    $success = '¡Registro exitoso! Se ha enviado un email de verificación a tu dirección.';
                } else {
                    $success = '¡Registro exitoso! Sin embargo, hubo un problema al enviar el email de verificación. Por favor contacta a soporte.';
                }
                // No redirigimos para mostrar el mensaje de éxito
            } else {
                $error = $result['message'];
            }
    }
}

// Vista
ob_start();
?>
<div class="auth-container">
    <div class="auth-card">
        <h2 class="auth-title">Crear una cuenta</h2>
        <p class="auth-subtitle">Regístrate para comenzar a crear tarjetas digitales</p>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <form method="POST" class="auth-form">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" 
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" 
                       required placeholder="tu@email.com">
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required 
                       placeholder="Mínimo 8 caracteres">
                <small class="form-text">La contraseña debe tener al menos 8 caracteres</small>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirmar Contraseña</label>
                <input type="password" id="confirm_password" name="confirm_password" required 
                       placeholder="Repite tu contraseña">
            </div>
            
            <div class="form-group checkbox-group">
                <input type="checkbox" id="agree_terms" name="agree_terms" required>
                <label for="agree_terms">
                    Acepto los <a href="#" target="_blank">términos y condiciones</a> y la 
                    <a href="#" target="_blank">política de privacidad</a>
                </label>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Registrarse</button>
        </form>
        
        <div class="auth-links">
            <p>¿Ya tienes una cuenta? <a href="login">Inicia sesión aquí</a></p>
        </div>
    </div>
</div>

<style>
.auth-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 70vh;
    padding: 2rem 0;
}

.auth-card {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
    padding: 2.5rem;
    width: 100%;
    max-width: 450px;
}

.auth-title {
    font-size: 1.8rem;
    margin-bottom: 0.5rem;
    color: var(--dark);
    text-align: center;
}

.auth-subtitle {
    color: var(--gray);
    text-align: center;
    margin-bottom: 2rem;
}

.auth-form {
    margin-top: 1.5rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: var(--dark);
}

.form-group input[type="email"],
.form-group input[type="password"],
.form-group input[type="text"] {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid var(--gray-light);
    border-radius: var(--border-radius);
    font-size: 1rem;
    transition: border-color 0.3s;
}

.form-group input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

.form-text {
    display: block;
    margin-top: 0.25rem;
    color: var(--gray);
    font-size: 0.875rem;
}

.checkbox-group {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
}

.checkbox-group input[type="checkbox"] {
    margin-top: 0.25rem;
}

.checkbox-group label {
    margin-bottom: 0;
    font-size: 0.95rem;
    line-height: 1.4;
}

.checkbox-group a {
    color: var(--primary);
    text-decoration: none;
}

.checkbox-group a:hover {
    text-decoration: underline;
}

.btn-block {
    width: 100%;
    padding: 0.875rem;
    font-size: 1rem;
}

.auth-links {
    margin-top: 1.5rem;
    text-align: center;
    color: var(--gray);
}

.auth-links a {
    color: var(--primary);
    text-decoration: none;
    font-weight: 500;
}

.auth-links a:hover {
    text-decoration: underline;
}
</style>
<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../views/layouts/base.php';
?>