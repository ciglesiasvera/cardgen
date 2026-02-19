<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CardGen Pro - <?php echo htmlspecialchars($title ?? 'Generador de Tarjetas Digitales'); ?></title>
    
    <!-- Google Ads -->
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-XXXXXXXXXXXXXXX" crossorigin="anonymous"></script>
    
    <!-- Estilos -->
    <link rel="stylesheet" href="/public/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Color picker -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/spectrum/1.8.1/spectrum.min.css">
    
    <!-- Fuentes de Google -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #4338ca;
            --secondary: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
            --light: #f9fafb;
            --dark: #1f2937;
            --gray: #6b7280;
            --gray-light: #e5e7eb;
            --border-radius: 8px;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: var(--dark);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
        }
        
        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .logo i {
            font-size: 1.8rem;
        }
        
        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }
        
        .nav-links a {
            text-decoration: none;
            color: var(--gray);
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .nav-links a:hover {
            color: var(--primary);
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }
        
        .btn-outline {
            background: transparent;
            border: 2px solid var(--primary);
            color: var(--primary);
        }
        
        .btn-outline:hover {
            background: var(--primary);
            color: white;
        }
        
        .alert {
            padding: 1rem;
            border-radius: var(--border-radius);
            margin: 1rem 0;
            border: 1px solid transparent;
        }
        
        .alert-success {
            background-color: #d1fae5;
            border-color: #a7f3d0;
            color: #065f46;
        }
        
        .alert-error {
            background-color: #fee2e2;
            border-color: #fecaca;
            color: #991b1b;
        }
        
        .alert-warning {
            background-color: #fef3c7;
            border-color: #fde68a;
            color: #92400e;
        }
        
        .alert-info {
            background-color: #dbeafe;
            border-color: #bfdbfe;
            color: #1e40af;
        }
        
        .main-content {
            min-height: calc(100vh - 140px);
            padding: 2rem 0;
        }
        
        .footer {
            background: rgba(255, 255, 255, 0.95);
            padding: 2rem 0;
            margin-top: 3rem;
            border-top: 1px solid var(--gray-light);
        }
        
        .footer-content {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 2rem;
        }
        
        .footer-section h3 {
            margin-bottom: 1rem;
            color: var(--dark);
        }
        
        .footer-links {
            list-style: none;
        }
        
        .footer-links li {
            margin-bottom: 0.5rem;
        }
        
        .footer-links a {
            color: var(--gray);
            text-decoration: none;
        }
        
        .footer-links a:hover {
            color: var(--primary);
        }
        
        .copyright {
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid var(--gray-light);
            color: var(--gray);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .nav-container {
                flex-direction: column;
                gap: 1rem;
            }
            
            .nav-links {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .footer-content {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Google Ads Header -->
    <div class="ads-header container" style="margin: 10px auto; text-align: center;">
        <!-- Espacio para anuncio -->
        <ins class="adsbygoogle"
             style="display:block"
             data-ad-client="ca-pub-XXXXXXXXXXXXXXX"
             data-ad-slot="1234567890"
             data-ad-format="auto"
             data-full-width-responsive="true"></ins>
        <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
    </div>
    
    <nav class="navbar">
        <div class="container nav-container">
            <a href="/" class="logo">
                <i class="fas fa-id-card"></i>
                CardGen Pro
            </a>
            
            <div class="nav-links">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    <a href="/create"><i class="fas fa-plus-circle"></i> Crear Tarjeta</a>
                    <a href="/logout"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
                <?php else: ?>
                    <a href="/"><i class="fas fa-home"></i> Inicio</a>
                    <a href="login"><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</a>
                    <a href="register" class="btn btn-primary"><i class="fas fa-user-plus"></i> Registrarse</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    
    <main class="main-content">
        <div class="container">
            <?php 
            // Mostrar mensajes flash
            if (function_exists('displayFlashMessage')) {
                displayFlashMessage();
            }
            ?>
            
            <?php echo $content ?? ''; ?>
        </div>
    </main>
    
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>CardGen Pro</h3>
                    <p>Generador de tarjetas digitales personalizadas para uso profesional.</p>
                </div>
                
                <div class="footer-section">
                    <h3>Enlaces Rápidos</h3>
                    <ul class="footer-links">
                        <li><a href="/">Inicio</a></li>
                        <li><a href="register">Registrarse</a></li>
                        <li><a href="login">Iniciar Sesión</a></li>
                        <li><a href="/privacy">Política de Privacidad</a></li>
                        <li><a href="#">Términos de Servicio</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3>Contacto</h3>
                    <p><i class="fas fa-envelope"></i> soporte@cardgenpro.com</p>
                    <p><i class="fas fa-phone"></i> +56 9 1234 5678</p>
                </div>
            </div>
            
            <div class="copyright">
                <p>&copy; <?php echo date('Y'); ?> CardGen Pro. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/spectrum/1.8.1/spectrum.min.js"></script>
    <script src="/public/assets/js/main.js"></script>
    
    <?php if (isset($scripts) && is_array($scripts)): ?>
        <?php foreach ($scripts as $script): ?>
            <script src="<?php echo htmlspecialchars($script); ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>