<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/User.php';

startSecureSession();

$title = 'Iniciar Sesión';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    // Validaciones
    if (empty($email) || empty($password)) {
        $error = 'Email y contraseña son requeridos';
    } elseif (!isValidEmail($email)) {
        $error = 'Email inválido';
    } else {
        // Intentar login
        $user = new User();
        $result = $user->login($email, $password);
        
        if ($result['success']) {
            // Establecer sesión
            $_SESSION['user_id'] = $result['user_id'];
            $_SESSION['user_email'] = $result['email'];
            $_SESSION['user_verified'] = $result['is_verified'];
            
            // Si el usuario seleccionó "recordarme"
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                $expiry = time() + (30 * 24 * 60 * 60); // 30 días
                
                // Guardar token en cookie
                setcookie('remember_token', $token, $expiry, '/', '', isset($_SERVER['HTTPS']), true);
                
                // Guardar token en base de datos (opcional)
                // $db = getDBConnection();
                // $stmt = $db->prepare("INSERT INTO user_sessions (user_id, token, expires) VALUES (?, ?, ?)");
                // $stmt->execute([$result['user_id'], $token, date('Y-m-d H:i:s', $expiry)]);
            }
            
            // Redirigir al dashboard
            redirectWithMessage('/dashboard', 'success', '¡Inicio de sesión exitoso!');
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
        <h2 class="auth-title">Iniciar Sesión</h2>
        <p class="auth-subtitle">Ingresa a tu cuenta para continuar</p>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
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
                       placeholder="Tu contraseña">
                <div class="form-actions">
                    <a href="/forgot-password" class="forgot-password">¿Olvidaste tu contraseña?</a>
                </div>
            </div>
            
            <div class="form-group checkbox-group">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Recordarme en este dispositivo</label>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Iniciar Sesión</button>
        </form>
        
        <div class="auth-links">
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

.form-group input[type="email"],
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

.form-actions {
    display: flex;
    justify-content: flex-end;
    margin-top: 0.5rem;
}

.forgot-password {
    color: var(--primary);
    text-decoration: none;
    font-size: 0.9rem;
}

.forgot-password:hover {
    text-decoration: underline;
}

.checkbox-group {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.checkbox-group input[type="checkbox"] {
    width: auto;
}

.checkbox-group label {
    margin-bottom: 0;
    font-size: 0.95rem;
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