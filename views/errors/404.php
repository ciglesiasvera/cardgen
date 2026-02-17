<?php
http_response_code(404);
$title = 'Página no encontrada';
ob_start();
?>
<div class="error-page">
    <div class="error-container">
        <div class="error-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        
        <h1 class="error-title">404</h1>
        <h2 class="error-subtitle">Página no encontrada</h2>
        
        <p class="error-message">
            Lo sentimos, la página que estás buscando no existe o ha sido movida.
        </p>
        
        <div class="error-actions">
            <a href="/" class="btn btn-primary">
                <i class="fas fa-home"></i> Volver al Inicio
            </a>
            <a href="dashboard" class="btn btn-outline">
                <i class="fas fa-tachometer-alt"></i> Ir al Dashboard
            </a>
            <a href="javascript:history.back()" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Volver Atrás
            </a>
        </div>
        
        <div class="error-help">
            <h4>¿Necesitas ayuda?</h4>
            <ul>
                <li>Verifica que la URL esté escrita correctamente</li>
                <li>Asegúrate de que estés logueado si intentas acceder a una página privada</li>
                <li>Si crees que esto es un error, contacta a soporte</li>
                <li>Puedes navegar usando el menú superior</li>
            </ul>
        </div>
    </div>
</div>

<style>
.error-page {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 80vh;
    padding: 2rem 0;
}

.error-container {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
    padding: 3rem;
    width: 100%;
    max-width: 600px;
    text-align: center;
}

.error-icon {
    font-size: 4rem;
    margin-bottom: 2rem;
    color: var(--warning);
}

.error-title {
    font-size: 5rem;
    margin-bottom: 0.5rem;
    color: var(--dark);
    line-height: 1;
}

.error-subtitle {
    font-size: 1.5rem;
    margin-bottom: 1.5rem;
    color: var(--gray);
}

.error-message {
    font-size: 1.125rem;
    color: var(--gray);
    margin-bottom: 2rem;
    line-height: 1.6;
}

.error-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
}

.error-help {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid var(--gray-light);
    text-align: left;
}

.error-help h4 {
    margin-bottom: 1rem;
    color: var(--dark);
}

.error-help ul {
    padding-left: 1.5rem;
    color: var(--gray);
}

.error-help li {
    margin-bottom: 0.5rem;
}

@media (max-width: 768px) {
    .error-container {
        padding: 2rem;
    }
    
    .error-title {
        font-size: 3rem;
    }
    
    .error-actions {
        flex-direction: column;
    }
    
    .error-help {
        text-align: center;
    }
    
    .error-help ul {
        text-align: left;
        display: inline-block;
    }
}
</style>
<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../views/layouts/base.php';
?>