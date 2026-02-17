<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/User.php';

startSecureSession();

$title = 'Verificar Cuenta';
$message = '';
$success = false;

$token = $_GET['token'] ?? '';

if (empty($token)) {
    $message = 'Token de verificación no proporcionado';
} else {
    $user = new User();
    $result = $user->verifyAccount($token);
    
    if ($result['success']) {
        $success = true;
        $message = '¡Tu cuenta ha sido verificada exitosamente! Ahora puedes iniciar sesión.';
        
        // Si el usuario ya está logueado, actualizar sesión
        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $result['user_id']) {
            $_SESSION['user_verified'] = true;
        }
    } else {
        $message = $result['message'];
    }
}

// Vista
ob_start();
?>
<div class="verify-container">
    <div class="verify-card">
        <div class="verify-icon">
            <?php if ($success): ?>
                <i class="fas fa-check-circle"></i>
            <?php else: ?>
                <i class="fas fa-exclamation-circle"></i>
            <?php endif; ?>
        </div>
        
        <h2 class="verify-title">
            <?php echo $success ? '¡Verificación Exitosa!' : 'Error de Verificación'; ?>
        </h2>
        
        <p class="verify-message"><?php echo htmlspecialchars($message); ?></p>
        
        <div class="verify-actions">
            <?php if ($success): ?>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="dashboard" class="btn btn-primary">Ir al Dashboard</a>
                <?php else: ?>
                    <a href="login" class="btn btn-primary">Iniciar Sesión</a>
                <?php endif; ?>
            <?php else: ?>
                <a href="/" class="btn btn-outline">Volver al Inicio</a>
                <a href="register" class="btn btn-primary">Registrarse</a>
            <?php endif; ?>
        </div>
        
        <?php if (!$success): ?>
            <div class="verify-help">
                <h4>¿Necesitas ayuda?</h4>
                <p>Si tienes problemas para verificar tu cuenta, por favor:</p>
                <ul>
                    <li>Asegúrate de que el enlace sea completo y no esté cortado</li>
                    <li>El token de verificación expira después de 24 horas</li>
                    <li>Si el token ha expirado, inicia sesión para solicitar uno nuevo</li>
                    <li>Contacta a soporte si el problema persiste</li>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.verify-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 70vh;
    padding: 2rem 0;
}

.verify-card {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
    padding: 3rem;
    width: 100%;
    max-width: 600px;
    text-align: center;
}

.verify-icon {
    font-size: 4rem;
    margin-bottom: 2rem;
}

.verify-icon .fa-check-circle {
    color: var(--secondary);
}

.verify-icon .fa-exclamation-circle {
    color: var(--warning);
}

.verify-title {
    font-size: 2rem;
    margin-bottom: 1rem;
    color: var(--dark);
}

.verify-message {
    font-size: 1.125rem;
    color: var(--gray);
    margin-bottom: 2rem;
    line-height: 1.6;
}

.verify-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
}

.verify-help {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid var(--gray-light);
    text-align: left;
}

.verify-help h4 {
    margin-bottom: 1rem;
    color: var(--dark);
}

.verify-help p {
    margin-bottom: 1rem;
    color: var(--gray);
}

.verify-help ul {
    padding-left: 1.5rem;
    color: var(--gray);
}

.verify-help li {
    margin-bottom: 0.5rem;
}

@media (max-width: 768px) {
    .verify-card {
        padding: 2rem;
    }
    
    .verify-icon {
        font-size: 3rem;
    }
    
    .verify-title {
        font-size: 1.5rem;
    }
    
    .verify-actions {
        flex-direction: column;
    }
}
</style>
<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../views/layouts/base.php';
?>