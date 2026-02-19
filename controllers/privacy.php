<?php
// Controlador para la página de políticas de privacidad
$title = 'Política de Privacidad';

// Vista
ob_start();
require_once __DIR__ . '/../views/privacy.php';
$content = ob_get_clean();

// Cargar layout
require_once __DIR__ . '/../views/layouts/base.php';
?>