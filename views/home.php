<?php
require_once __DIR__ . '/../config/database.php';
startSecureSession();

$title = 'CardGen Pro - Generador de Tarjetas Digitales';
ob_start();
?>
<div class="home-page">
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">Crea tarjetas digitales personalizadas en minutos</h1>
            <p class="hero-subtitle">Genera tarjetas bancarias, tributarias y personalizadas con diseño profesional y descarga en alta calidad.</p>
            
            <div class="hero-actions">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="dashboard" class="btn btn-primary btn-lg">
                        <i class="fas fa-tachometer-alt"></i> Ir al Dashboard
                    </a>
                    <a href="/create" class="btn btn-outline btn-lg">
                        <i class="fas fa-plus-circle"></i> Crear Tarjeta
                    </a>
                <?php else: ?>
                <a href="register" class="btn btn-primary btn-lg">
                    <i class="fas fa-user-plus"></i> Comenzar Gratis
                </a>
                <a href="login" class="btn btn-outline btn-lg">
                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                </a>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="hero-image">
            <div class="card-showcase">
                <div class="card-sample card-sample-1">
                    <div class="card-sample-content">
                        <h4>Banco Nacional</h4>
                        <p><strong>Cuenta:</strong> 123456789</p>
                        <p><strong>Nombre:</strong> Juan Pérez</p>
                        <p><strong>RUT:</strong> 12.345.678-9</p>
                    </div>
                </div>
                <div class="card-sample card-sample-2">
                    <div class="card-sample-content">
                        <h4>Empresa S.A.</h4>
                        <p><strong>Razón Social:</strong> Empresa Sociedad Anónima</p>
                        <p><strong>Giro:</strong> Tecnología</p>
                        <p><strong>RUT:</strong> 76.543.210-K</p>
                    </div>
                </div>
                <div class="card-sample card-sample-3">
                    <div class="card-sample-content">
                        <h4>Tarjeta Personal</h4>
                        <p><strong>Nombre:</strong> María González</p>
                        <p><strong>Cargo:</strong> Desarrolladora</p>
                        <p><strong>Teléfono:</strong> +56 9 1234 5678</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Google Ads -->
    <div class="ads-section" style="margin: 40px auto; text-align: center;">
        <ins class="adsbygoogle"
             style="display:block"
             data-ad-client="ca-pub-XXXXXXXXXXXXXXX"
             data-ad-slot="1122334455"
             data-ad-format="auto"
             data-full-width-responsive="true"></ins>
        <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
    </div>
    
    <!-- Features Section -->
    <section class="features-section">
        <h2 class="section-title">¿Por qué elegir CardGen Pro?</h2>
        <p class="section-subtitle">Todas las herramientas que necesitas para crear tarjetas digitales profesionales</p>
        
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-bolt"></i>
                </div>
                <h3>Rápido y Fácil</h3>
                <p>Crea tarjetas en minutos con nuestro editor intuitivo. No se requieren habilidades de diseño.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-palette"></i>
                </div>
                <h3>Personalización Total</h3>
                <p>Personaliza colores, fuentes, formato y agrega tus logos para un diseño único.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-download"></i>
                </div>
                <h3>Descarga en Alta Calidad</h3>
                <p>Exporta tus tarjetas en PNG o JPG en diferentes resoluciones, incluyendo 300 DPI.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3>Seguro y Privado</h3>
                <p>Tus datos están protegidos con encriptación y no compartimos tu información.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <h3>Completamente Responsive</h3>
                <p>Diseña y visualiza tus tarjetas desde cualquier dispositivo, móvil o desktop.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-gratipay"></i>
                </div>
                <h3>Completamente Gratis</h3>
                <p>Genera todas las tarjetas que necesites sin costo. Solo soportado por publicidad.</p>
            </div>
        </div>
    </section>
    
    <!-- How It Works -->
    <section class="how-it-works">
        <h2 class="section-title">Cómo funciona</h2>
        <p class="section-subtitle">Solo sigue estos simples pasos</p>
        
        <div class="steps">
            <div class="step">
                <div class="step-number">1</div>
                <h3>Regístrate Gratis</h3>
                <p>Crea tu cuenta en menos de un minuto. Solo necesitas un email.</p>
            </div>
            
            <div class="step">
                <div class="step-number">2</div>
                <h3>Selecciona el Tipo</h3>
                <p>Elige entre tarjetas bancarias, tributarias o personalizadas.</p>
            </div>
            
            <div class="step">
                <div class="step-number">3</div>
                <h3>Personaliza tu Diseño</h3>
                <p>Ajusta colores, fuentes, formato y agrega tus datos.</p>
            </div>
            
            <div class="step">
                <div class="step-number">4</div>
                <h3>Descarga y Comparte</h3>
                <p>Exporta en PNG/JPG y comparte tu tarjeta digital.</p>
            </div>
        </div>
        
        <div class="cta-section">
            <?php if (!isset($_SESSION['user_id'])): ?>
                <h2>¿Listo para comenzar?</h2>
                <p>Únete a miles de usuarios que ya crean sus tarjetas con CardGen Pro</p>
                <a href="register" class="btn btn-primary btn-lg">
                    <i class="fas fa-rocket"></i> Comenzar Ahora
                </a>
            <?php else: ?>
                <h2>¿Listo para crear más tarjetas?</h2>
                <p>Continúa diseñando tarjetas profesionales con todas las funciones</p>
                <a href="/create" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus-circle"></i> Crear Nueva Tarjeta
                </a>
            <?php endif; ?>
        </div>
    </section>
    
    <!-- Footer Ads -->
    <div class="ads-section" style="margin: 40px auto; text-align: center;">
        <ins class="adsbygoogle"
             style="display:block"
             data-ad-client="ca-pub-XXXXXXXXXXXXXXX"
             data-ad-slot="5566778899"
             data-ad-format="auto"
             data-full-width-responsive="true"></ins>
        <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
    </div>
</div>

<style>
.home-page {
    padding: 2rem 0;
}

/* Hero Section */
.hero-section {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4rem;
    align-items: center;
    margin-bottom: 4rem;
}

.hero-content {
    animation: fadeInUp 0.8s ease-out;
}

.hero-title {
    font-size: 3rem;
    line-height: 1.2;
    margin-bottom: 1.5rem;
    color: var(--dark);
    background: linear-gradient(90deg, var(--primary), #8b5cf6);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.hero-subtitle {
    font-size: 1.25rem;
    color: #bdc4d3;
    margin-bottom: 2rem;
    line-height: 1.6;
}

.hero-actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.hero-image {
    position: relative;
    animation: float 6s ease-in-out infinite;
}

.card-showcase {
    position: relative;
    height: 400px;
}

.card-sample {
    position: absolute;
    width: 280px;
    height: 180px;
    border-radius: 12px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    padding: 1.5rem;
    transition: transform 0.3s ease;
}

.card-sample:hover {
    transform: translateY(-10px);
}

.card-sample-1 {
    top: 0;
    left: 0;
    background: linear-gradient(135deg, #1e3a8a, #3b82f6);
    color: white;
    z-index: 3;
    transform: rotate(-5deg);
}

.card-sample-2 {
    top: 60px;
    right: 20px;
    background: linear-gradient(135deg, #047857, #10b981);
    color: white;
    z-index: 2;
    transform: rotate(3deg);
}

.card-sample-3 {
    bottom: 0;
    left: 40px;
    background: linear-gradient(135deg, #7c3aed, #a78bfa);
    color: white;
    z-index: 1;
    transform: rotate(-2deg);
}

.card-sample-content h4 {
    margin-bottom: 1rem;
    font-size: 1.25rem;
}

.card-sample-content p {
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
    opacity: 0.9;
}

/* Features Section */
.features-section {
    margin: 6rem 0;
}

.section-title {
    text-align: center;
    font-size: 2.5rem;
    margin-bottom: 1rem;
    color: var(--dark);
}

.section-subtitle {
    text-align: center;
    color: #bdc4d3;
    font-size: 1.125rem;
    margin-bottom: 3rem;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-top: 3rem;
}

.feature-card {
    background: white;
    border-radius: var(--border-radius);
    padding: 2rem;
    box-shadow: var(--shadow);
    transition: all 0.3s ease;
    text-align: center;
}

.feature-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
}

.feature-icon {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    color: white;
    font-size: 1.8rem;
}

.feature-card h3 {
    margin-bottom: 1rem;
    color: var(--dark);
}

.feature-card p {
    color: var(--gray);
    line-height: 1.6;
}

/* How It Works */
.how-it-works {
    margin: 6rem 0;
}

.steps {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 2rem;
    margin: 3rem 0;
}

.step {
    text-align: center;
    position: relative;
}

.step-number {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    color: white;
    font-size: 1.5rem;
    font-weight: bold;
    box-shadow: 0 10px 20px rgba(79, 70, 229, 0.3);
}

.step h3 {
    margin-bottom: 1rem;
    color: var(--dark);
}

.step p {
    color: #bdc4d3;
    line-height: 1.6;
}

.cta-section {
    text-align: center;
    padding: 4rem 2rem;
    background: linear-gradient(135deg, var(--primary), #8b5cf6);
    border-radius: var(--border-radius);
    color: white;
    margin-top: 4rem;
}

.cta-section h2 {
    color: white;
    font-size: 2.5rem;
    margin-bottom: 1rem;
}

.cta-section p {
    color: rgba(255, 255, 255, 0.9);
    font-size: 1.125rem;
    margin-bottom: 2rem;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

.cta-section .btn-primary {
    background: white;
    color: var(--primary);
    border: none;
}

.cta-section .btn-primary:hover {
    background: var(--light);
    transform: translateY(-3px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes float {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-20px);
    }
}

/* Responsive */
@media (max-width: 1024px) {
    .hero-section {
        grid-template-columns: 1fr;
        gap: 3rem;
    }
    
    .hero-title {
        font-size: 2.5rem;
    }
    
    .card-showcase {
        height: 300px;
    }
    
    .card-sample {
        width: 220px;
        height: 150px;
    }
}

@media (max-width: 768px) {
    .hero-title {
        font-size: 2rem;
    }
    
    .hero-subtitle {
        font-size: 1.125rem;
    }
    
    .hero-actions {
        flex-direction: column;
        align-items: stretch;
    }
    
    .section-title {
        font-size: 2rem;
    }
    
    .features-grid {
        grid-template-columns: 1fr;
    }
    
    .steps {
        grid-template-columns: 1fr;
        gap: 3rem;
    }
    
    .cta-section {
        padding: 3rem 1rem;
    }
    
    .cta-section h2 {
        font-size: 2rem;
    }
}
</style>

<script>
// Inicializar anuncios de Google
document.addEventListener('DOMContentLoaded', function() {
    // Los anuncios ya se cargan automáticamente
    // Podemos agregar animaciones adicionales
    
    // Animación para las tarjetas de muestra
    const cards = document.querySelectorAll('.card-sample');
    cards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.2}s`;
    });
});
</script>
<?php
$content = ob_get_clean();
require_once __DIR__ . '/layouts/base.php';
?>