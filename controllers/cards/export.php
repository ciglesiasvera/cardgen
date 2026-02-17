<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Card.php';

startSecureSession();

// Verificar autenticación
if (!isset($_SESSION['user_id'])) {
    redirectWithMessage('/login', 'error', 'Por favor inicia sesión primero');
    exit();
}

$user_id = $_SESSION['user_id'];
$card_id = $_GET['id'] ?? null;
$format = $_GET['format'] ?? 'png';
$resolution = $_GET['resolution'] ?? 'high';

if (!$card_id) {
    redirectWithMessage('/dashboard', 'error', 'ID de tarjeta no proporcionado');
    exit();
}

// Obtener tarjeta
$cardModel = new Card();
$card = $cardModel->getById($card_id, $user_id);

if (!$card) {
    redirectWithMessage('/dashboard', 'error', 'Tarjeta no encontrada');
    exit();
}

// Procesar exportación
if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['download'])) {
    // En un entorno real, aquí usaríamos html2canvas o similar
    // Para este MVP, simularemos la exportación con un mensaje
    
    $filename = 'cardgen_' . $card_id . '_' . date('Ymd_His') . '.' . $format;
    
    // Simular generación de imagen
    // En producción, esto generaría la imagen real usando JavaScript/html2canvas
    
    // Redireccionar con mensaje de éxito
    redirectWithMessage('/dashboard', 'success', 
        "¡Tarjeta exportada como $filename! En la versión completa, se descargaría el archivo.");
    exit();
}

// Vista de configuración de exportación
$title = 'Exportar Tarjeta';
ob_start();
?>
<div class="export-page">
    <h1>Exportar Tarjeta</h1>
    <p>Configura las opciones de exportación para tu tarjeta digital</p>
    
    <div class="export-container">
        <div class="export-preview">
            <h3>Vista Previa de Exportación</h3>
            <div class="card-export-preview">
                <?php
                $card_data = $card['card_data'];
                $bg_color = $card['background_color'] ?? '#FFFFFF';
                $text_color = $card['text_color'] ?? '#000000';
                $font_size = $card['font_size'] ?? 14;
                $font_family = $card['font_family'] ?? 'Arial';
                $alignment = $card['alignment'] ?? 'left';
                ?>
                <div class="export-card" style="
                    background: <?php echo htmlspecialchars($bg_color); ?>;
                    color: <?php echo htmlspecialchars($text_color); ?>;
                    font-family: <?php echo htmlspecialchars($font_family); ?>;
                    font-size: <?php echo htmlspecialchars($font_size); ?>px;
                    text-align: <?php echo htmlspecialchars($alignment); ?>;
                    padding: 2rem;
                    border-radius: 8px;
                    margin: 1rem 0;
                ">
                    <?php if ($card['card_type'] === 'bank'): ?>
                        <h2><?php echo htmlspecialchars($card_data['bank_name'] ?? 'Banco'); ?></h2>
                        <p><strong>Tipo de Cuenta:</strong> <?php echo htmlspecialchars($card_data['account_type'] ?? ''); ?></p>
                        <p><strong>Número:</strong> <?php echo htmlspecialchars($card_data['account_number'] ?? ''); ?></p>
                        <p><strong>Nombre:</strong> <?php echo htmlspecialchars($card_data['name'] ?? ''); ?></p>
                        <p><strong>RUT:</strong> <?php echo htmlspecialchars($card_data['rut'] ?? ''); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($card_data['email'] ?? ''); ?></p>
                    <?php elseif ($card['card_type'] === 'tax'): ?>
                        <h2><?php echo htmlspecialchars($card_data['company_name'] ?? 'Empresa'); ?></h2>
                        <p><strong>Razón Social:</strong> <?php echo htmlspecialchars($card_data['business_name'] ?? ''); ?></p>
                        <p><strong>Giro:</strong> <?php echo htmlspecialchars($card_data['industry'] ?? ''); ?></p>
                        <p><strong>Dirección:</strong> <?php echo htmlspecialchars($card_data['address'] ?? ''); ?></p>
                        <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($card_data['phone'] ?? ''); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($card_data['email'] ?? ''); ?></p>
                        <p><strong>RUT:</strong> <?php echo htmlspecialchars($card_data['rut'] ?? ''); ?></p>
                    <?php elseif ($card['card_type'] === 'custom'): ?>
                        <h2><?php echo htmlspecialchars($card_data['title'] ?? 'Tarjeta Personal'); ?></h2>
                        <?php if (isset($card_data['fields']) && is_array($card_data['fields'])): ?>
                            <?php foreach ($card_data['fields'] as $field): ?>
                                <p><strong><?php echo htmlspecialchars($field['key'] ?? ''); ?>:</strong> <?php echo htmlspecialchars($field['value'] ?? ''); ?></p>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="export-settings">
            <h3>Opciones de Exportación</h3>
            
            <form method="GET" action="">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($card_id); ?>">
                
                <div class="form-section">
                    <h4><i class="fas fa-file-image"></i> Formato</h4>
                    <div class="format-options">
                        <label class="format-option">
                            <input type="radio" name="format" value="png" checked>
                            <div class="format-content">
                                <i class="fas fa-file-image"></i>
                                <span>PNG</span>
                                <small>Calidad perfecta, fondo transparente</small>
                            </div>
                        </label>
                        
                        <label class="format-option">
                            <input type="radio" name="format" value="jpg">
                            <div class="format-content">
                                <i class="fas fa-file-image"></i>
                                <span>JPG</span>
                                <small>Menor tamaño, compatible universal</small>
                            </div>
                        </label>
                    </div>
                </div>
                
                <div class="form-section">
                    <h4><i class="fas fa-expand"></i> Resolución</h4>
                    <div class="resolution-options">
                        <label class="resolution-option">
                            <input type="radio" name="resolution" value="low" checked>
                            <div class="resolution-content">
                                <span>Baja (Web)</span>
                                <small>72 DPI, ideal para web</small>
                            </div>
                        </label>
                        
                        <label class="resolution-option">
                            <input type="radio" name="resolution" value="medium">
                            <div class="resolution-content">
                                <span>Media</span>
                                <small>150 DPI, buena calidad</small>
                            </div>
                        </label>
                        
                        <label class="resolution-option">
                            <input type="radio" name="resolution" value="high">
                            <div class="resolution-content">
                                <span>Alta</span>
                                <small>300 DPI, calidad impresión</small>
                            </div>
                        </label>
                    </div>
                </div>
                
                <div class="form-section">
                    <h4><i class="fas fa-ruler-combined"></i> Tamaño</h4>
                    <div class="size-options">
                        <label class="size-option">
                            <input type="radio" name="size" value="original" checked>
                            <div class="size-content">
                                <span>Formato Original</span>
                                <small><?php echo htmlspecialchars($card['format_ratio'] ?? '16:9'); ?></small>
                            </div>
                        </label>
                        
                        <label class="size-option">
                            <input type="radio" name="size" value="custom">
                            <div class="size-content">
                                <span>Personalizado</span>
                                <small>Define ancho y alto</small>
                            </div>
                        </label>
                    </div>
                    
                    <div id="custom-size" style="display: none; margin-top: 1rem;">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Ancho (px)</label>
                                <input type="number" name="custom_width" min="100" max="4000" value="800">
                            </div>
                            <div class="form-group">
                                <label>Alto (px)</label>
                                <input type="number" name="custom_height" min="100" max="4000" value="450">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-actions">
                    <a href="/preview?id=<?php echo $card_id; ?>" class="btn btn-outline">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                    <button type="submit" name="download" value="1" class="btn btn-primary">
                        <i class="fas fa-download"></i> Descargar Ahora
                    </button>
                </div>
            </form>
            
            <div class="export-info">
                <h4><i class="fas fa-info-circle"></i> Información</h4>
                <ul>
                    <li>Las tarjetas se exportan con la máxima calidad posible</li>
                    <li>PNG es recomendado para fondos transparentes</li>
                    <li>JPG es mejor para compartir en redes sociales</li>
                    <li>300 DPI es ideal para impresión profesional</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
.export-page {
    padding: 2rem 0;
}

.export-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
    margin-top: 2rem;
}

.export-preview, .export-settings {
    background: white;
    border-radius: var(--border-radius);
    padding: 2rem;
    box-shadow: var(--shadow);
}

.export-preview h3, .export-settings h3 {
    margin-bottom: 1.5rem;
    color: var(--dark);
    border-bottom: 2px solid var(--primary);
    padding-bottom: 0.5rem;
}

.card-export-preview {
    padding: 1rem;
    background: var(--light);
    border-radius: var(--border-radius);
    border: 1px solid var(--gray-light);
}

.export-card {
    min-height: 300px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.form-section {
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid var(--gray-light);
}

.form-section:last-child {
    border-bottom: none;
}

.form-section h4 {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
    color: var(--dark);
}

.format-options, .resolution-options, .size-options {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
}

.format-option input[type="radio"],
.resolution-option input[type="radio"],
.size-option input[type="radio"] {
    display: none;
}

.format-content, .resolution-content, .size-content {
    padding: 1rem;
    border: 2px solid var(--gray-light);
    border-radius: var(--border-radius);
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
}

.format-content i {
    font-size: 2rem;
    margin-bottom: 0.5rem;
    color: var(--gray);
}

.format-content span, .resolution-content span, .size-content span {
    display: block;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.format-content small, .resolution-content small, .size-content small {
    display: block;
    font-size: 0.8rem;
    color: var(--gray);
}

.format-option input[type="radio"]:checked + .format-content,
.resolution-option input[type="radio"]:checked + .resolution-content,
.size-option input[type="radio"]:checked + .size-content {
    border-color: var(--primary);
    background: rgba(79, 70, 229, 0.1);
}

.format-option input[type="radio"]:checked + .format-content i {
    color: var(--primary);
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.form-actions {
    display: flex;
    justify-content: space-between;
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--gray-light);
}

.export-info {
    margin-top: 2rem;
    padding: 1.5rem;
    background: var(--light);
    border-radius: var(--border-radius);
}

.export-info h4 {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
    color: var(--dark);
}

.export-info ul {
    padding-left: 1.5rem;
    color: var(--gray);
}

.export-info li {
    margin-bottom: 0.5rem;
}

@media (max-width: 1024px) {
    .export-container {
        grid-template-columns: 1fr;
    }
    
    .format-options, .resolution-options, .size-options {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .export-container {
        gap: 2rem;
    }
    
    .export-preview, .export-settings {
        padding: 1.5rem;
    }
    
    .format-options, .resolution-options, .size-options {
        grid-template-columns: 1fr;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        flex-direction: column;
        gap: 1rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mostrar/ocultar tamaño personalizado
    const sizeOptions = document.querySelectorAll('input[name="size"]');
    const customSizeDiv = document.getElementById('custom-size');
    
    sizeOptions.forEach(option => {
        option.addEventListener('change', function() {
            customSizeDiv.style.display = this.value === 'custom' ? 'block' : 'none';
        });
    });
    
    // Establecer valores predeterminados según el formato de la tarjeta
    const formatRatio = "<?php echo $card['format_ratio'] ?? '16:9'; ?>";
    let defaultWidth = 800;
    let defaultHeight = 450;
    
    if (formatRatio === '1:1') {
        defaultWidth = defaultHeight = 600;
    } else if (formatRatio === '2x4') {
        defaultWidth = 400;
        defaultHeight = 800;
    }
    
    // Actualizar valores predeterminados si existen
    const widthInput = document.querySelector('input[name="custom_width"]');
    const heightInput = document.querySelector('input[name="custom_height"]');
    
    if (widthInput && heightInput) {
        widthInput.value = defaultWidth;
        heightInput.value = defaultHeight;
    }
});
</script>
<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../views/layouts/base.php';
?>