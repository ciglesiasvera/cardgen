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

// Vista de configuración de exportación
$title = 'Exportar Tarjeta';
ob_start();
?>
<!-- html2canvas CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

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
                $title_color = $card['title_color'] ?? $text_color;
                $font_size = $card['font_size'] ?? 14;
                $font_family = $card['font_family'] ?? 'Arial';
                $alignment = $card['alignment'] ?? 'left';
                $format_ratio = $card['format_ratio'] ?? '16:9';
                
                $width = 800;
                $height = 450;
                
                if ($format_ratio === '1:1') {
                    $width = $height = 600;
                } elseif ($format_ratio === '2x4') {
                    $width = 400;
                    $height = 800;
                }
                ?>
                <div class="export-card" id="export-card" style="
                    background: <?php echo htmlspecialchars($bg_color); ?>;
                    color: <?php echo htmlspecialchars($text_color); ?>;
                    font-family: <?php echo htmlspecialchars($font_family); ?>;
                    font-size: <?php echo htmlspecialchars($font_size); ?>px;
                    text-align: <?php echo htmlspecialchars($alignment); ?>;
                    width: <?php echo $width; ?>px;
                    max-width: 100%;
                    padding: 2rem;
                    border-radius: 8px;
                    margin: 1rem auto;
                    position: relative;
                    box-sizing: border-box;
                ">
                    <?php if ($card['card_type'] === 'bank'): ?>
                        <h2 style="color: <?php echo htmlspecialchars($title_color); ?>;"><?php echo htmlspecialchars($card_data['bank_name'] ?? 'Banco'); ?></h2>
                        <p><strong>Tipo de Cuenta:</strong> <?php echo htmlspecialchars($card_data['account_type'] ?? ''); ?></p>
                        <p><strong>Número:</strong> <?php echo htmlspecialchars($card_data['account_number'] ?? ''); ?></p>
                        <p><strong>Nombre:</strong> <?php echo htmlspecialchars($card_data['name'] ?? ''); ?></p>
                        <p><strong>RUT:</strong> <?php echo htmlspecialchars($card_data['rut'] ?? ''); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($card_data['email'] ?? ''); ?></p>
                    <?php elseif ($card['card_type'] === 'tax'): ?>
                        <h2 style="color: <?php echo htmlspecialchars($title_color); ?>;"><?php echo htmlspecialchars($card_data['company_name'] ?? 'Empresa'); ?></h2>
                        <p><strong>Razón Social:</strong> <?php echo htmlspecialchars($card_data['business_name'] ?? ''); ?></p>
                        <p><strong>Giro:</strong> <?php echo htmlspecialchars($card_data['industry'] ?? ''); ?></p>
                        <p><strong>Dirección:</strong> <?php echo htmlspecialchars($card_data['address'] ?? ''); ?></p>
                        <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($card_data['phone'] ?? ''); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($card_data['email'] ?? ''); ?></p>
                        <p><strong>RUT:</strong> <?php echo htmlspecialchars($card_data['rut'] ?? ''); ?></p>
                    <?php elseif ($card['card_type'] === 'custom'): ?>
                        <h2 style="color: <?php echo htmlspecialchars($title_color); ?>;"><?php echo htmlspecialchars($card_data['title'] ?? 'Tarjeta Personal'); ?></h2>
                        <?php if (isset($card_data['fields']) && is_array($card_data['fields'])): ?>
                            <?php foreach ($card_data['fields'] as $field): ?>
                                <p><strong><?php echo htmlspecialchars($field['key'] ?? ''); ?>:</strong> <?php echo htmlspecialchars($field['value'] ?? ''); ?></p>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php if (!empty($card['logo_url'])): ?>
                        <div style="position: absolute; top: 1rem; right: 1rem;">
                            <img src="<?php echo htmlspecialchars($card['logo_url']); ?>" alt="Logo" style="max-width: 80px; max-height: 60px; object-fit: contain; border-radius: 4px;">
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($card['logo_url2'])): ?>
                        <div style="position: absolute; top: 1rem; left: 1rem;">
                            <img src="<?php echo htmlspecialchars($card['logo_url2']); ?>" alt="Logo 2" style="max-width: 80px; max-height: 60px; object-fit: contain; border-radius: 4px;">
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="export-settings">
            <h3>Opciones de Exportación</h3>
            
            <form id="export-form">
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
                            <input type="radio" name="resolution" value="1">
                            <div class="resolution-content">
                                <span>Baja (Web)</span>
                                <small>1x, ideal para web</small>
                            </div>
                        </label>
                        
                        <label class="resolution-option">
                            <input type="radio" name="resolution" value="2" checked>
                            <div class="resolution-content">
                                <span>Media</span>
                                <small>2x, buena calidad</small>
                            </div>
                        </label>
                        
                        <label class="resolution-option">
                            <input type="radio" name="resolution" value="3">
                            <div class="resolution-content">
                                <span>Alta</span>
                                <small>3x, calidad impresión</small>
                            </div>
                        </label>
                    </div>
                </div>
                
                <div class="form-actions">
                    <a href="/preview?id=<?php echo $card_id; ?>" class="btn btn-outline">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                    <button type="button" id="download-btn" class="btn btn-primary">
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
                    <li>Resolución alta (3x) es ideal para impresión profesional</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
.export-page { padding: 2rem 0; }
.export-container { display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; margin-top: 2rem; }
.export-preview, .export-settings { background: white; border-radius: var(--border-radius); padding: 2rem; box-shadow: var(--shadow); }
.export-preview h3, .export-settings h3 { margin-bottom: 1.5rem; color: var(--dark); border-bottom: 2px solid var(--primary); padding-bottom: 0.5rem; }
.card-export-preview { padding: 1rem; background: var(--light); border-radius: var(--border-radius); border: 1px solid var(--gray-light); display: flex; justify-content: center; }
.export-card { min-height: 300px; display: flex; flex-direction: column; justify-content: center; }
.form-section { margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 1px solid var(--gray-light); }
.form-section:last-child { border-bottom: none; }
.form-section h4 { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem; color: var(--dark); }
.format-options, .resolution-options { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; }
.format-option input[type="radio"], .resolution-option input[type="radio"] { display: none; }
.format-content, .resolution-content { padding: 1rem; border: 2px solid var(--gray-light); border-radius: var(--border-radius); text-align: center; cursor: pointer; transition: all 0.3s; }
.format-content i { font-size: 2rem; margin-bottom: 0.5rem; color: var(--gray); }
.format-content span, .resolution-content span { display: block; font-weight: 600; margin-bottom: 0.25rem; }
.format-content small, .resolution-content small { display: block; font-size: 0.8rem; color: var(--gray); }
.format-option input[type="radio"]:checked + .format-content, .resolution-option input[type="radio"]:checked + .resolution-content { border-color: var(--primary); background: rgba(79, 70, 229, 0.1); }
.format-option input[type="radio"]:checked + .format-content i { color: var(--primary); }
.form-actions { display: flex; justify-content: space-between; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--gray-light); }
.export-info { margin-top: 2rem; padding: 1.5rem; background: var(--light); border-radius: var(--border-radius); }
.export-info h4 { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem; color: var(--dark); }
.export-info ul { padding-left: 1.5rem; color: var(--gray); }
.export-info li { margin-bottom: 0.5rem; }
@media (max-width: 1024px) { .export-container { grid-template-columns: 1fr; } }
@media (max-width: 768px) { .export-container { gap: 2rem; } .export-preview, .export-settings { padding: 1.5rem; } .form-actions { flex-direction: column; gap: 1rem; } }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const downloadBtn = document.getElementById('download-btn');
    const exportCard = document.getElementById('export-card');
    
    downloadBtn.addEventListener('click', function() {
        const format = document.querySelector('input[name="format"]:checked').value;
        const scale = parseInt(document.querySelector('input[name="resolution"]:checked').value);
        
        downloadBtn.disabled = true;
        downloadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando...';
        
        html2canvas(exportCard, {
            backgroundColor: format === 'png' ? null : '#ffffff',
            scale: scale,
            useCORS: true,
            allowTaint: true,
            logging: false
        }).then(canvas => {
            const link = document.createElement('a');
            const timestamp = Date.now();
            
            if (format === 'png') {
                link.download = `cardgen_tarjeta_${timestamp}.png`;
                link.href = canvas.toDataURL('image/png');
            } else {
                link.download = `cardgen_tarjeta_${timestamp}.jpg`;
                link.href = canvas.toDataURL('image/jpeg', 0.95);
            }
            
            link.click();
            
            downloadBtn.disabled = false;
            downloadBtn.innerHTML = '<i class="fas fa-download"></i> Descargar Ahora';
        }).catch(err => {
            console.error('Error al exportar:', err);
            alert('Hubo un error al generar la imagen. Por favor, intenta de nuevo.');
            downloadBtn.disabled = false;
            downloadBtn.innerHTML = '<i class="fas fa-download"></i> Descargar Ahora';
        });
    });
});
</script>
<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../views/layouts/base.php';
?>