<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../config/smtp.php';

startSecureSession();

$title = 'Recuperar Contraseña';
$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email'] ?? '');
    
    if (empty($email)) {
        $message = 'Por favor ingresa tu dirección de email';
    } elseif (!isValidEmail($email)) {
        $message = 'Email inválido';
    } else {
        $user = new User();
        $result = $user->requestPasswordReset($email);
        
        if ($result['success']) {
            // Enviar email de recuperación usando SMTP
            if (sendPasswordResetEmail($email, $result['reset_token'])) {
                $success = true;
                $message = 'Se ha enviado un enlace de recuperación a tu email. Revisa tu bandeja de entrada.';
            } else {
                $message = 'Error al enviar el email de recuperación. Por favor intenta de nuevo más tarde.';
            }
        } else {
            $message = $result['message'];
        }
    }
}

// Vista
ob_start();
?>
<div class="auth-container">
    <div class="auth-card">
        <h2 class="auth-title">Recuperar Contraseña</h2>
        <p class="auth-subtitle">Ingresa tu email para recibir un enlace de recuperación</p>
        
        <?php if ($message): ?>
            <div class="alert <?php echo $success ? 'alert-success' : 'alert-error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <?php if (!$success): ?>
            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" 
                           required placeholder="tu@email.com">
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    Enviar Enlace de Recuperación
                </button>
            </form>
        <?php endif; ?>
        
        <div class="auth-links">
            <p>¿Recordaste tu contraseña? <a href="login">Inicia sesión aquí</a></p>
            <p>¿No tienes una cuenta? <a href="register">Regístrate aquí</a></p>
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

.form-group input[type="email"] {
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

.auth-links p {
    margin-bottom: 0.5rem;
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