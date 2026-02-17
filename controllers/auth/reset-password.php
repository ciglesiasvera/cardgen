<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../config/smtp.php';

startSecureSession();

$title = 'Restablecer Contraseña';
$message = '';
$success = false;

$token = $_GET['token'] ?? '';

if (empty($token)) {
    redirectWithMessage('/forgot-password', 'error', 'Token de restablecimiento no proporcionado');
    exit();
}

// Verificar token
$user = new User();
$token_valid = $user->verifyResetToken($token);

if (!$token_valid['success']) {
    redirectWithMessage('/forgot-password', 'error', $token_valid['message']);
    exit();
}

$user_id = $token_valid['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($password) || empty($confirm_password)) {
        $message = 'Ambos campos son requeridos';
    } elseif (!isValidPassword($password)) {
        $message = 'La contraseña debe tener al menos 8 caracteres';
    } elseif ($password !== $confirm_password) {
        $message = 'Las contraseñas no coinciden';
    } else {
        $result = $user->resetPasswordById($user_id, $password);
        
        if ($result['success']) {
            $success = true;
            $message = '¡Contraseña restablecida exitosamente! Ahora puedes iniciar sesión con tu nueva contraseña.';
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
        <h2 class="auth-title">Restablecer Contraseña</h2>
        <p class="auth-subtitle">Ingresa tu nueva contraseña</p>
        
        <?php if ($message): ?>
            <div class="alert <?php echo $success ? 'alert-success' : 'alert-error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <?php if (!$success): ?>
            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="password">Nueva Contraseña</label>
                    <input type="password" id="password" name="password" required 
                           placeholder="Mínimo 8 caracteres">
                    <div class="form-text">La contraseña debe tener al menos 8 caracteres</div>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirmar Contraseña</label>
                    <input type="password" id="confirm_password" name="confirm_password" required 
                           placeholder="Repite tu contraseña">
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    Restablecer Contraseña
                </button>
            </form>
        <?php endif; ?>
        
        <div class="auth-links">
            <p>¿Recordaste tu contraseña? <a href="login">Inicia sesión aquí</a></p>
            <p>¿Necesitas otro enlace? <a href="forgot-password">Solicitar nuevo enlace</a></p>
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

.form-group input[type="password"] {
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
    font-size: 0.875rem;
    color: var(--gray);
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