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

if (!$card_id) {
    redirectWithMessage('dashboard', 'error', 'ID de tarjeta no proporcionado');
    exit();
}

// Obtener tarjeta
$cardModel = new Card();
$card = $cardModel->getById($card_id, $user_id);

if (!$card) {
    redirectWithMessage('dashboard', 'error', 'Tarjeta no encontrada');
    exit();
}

$title = 'Vista Previa de Tarjeta';
$card_data = $card['card_data'];

// Vista
ob_start();
?>
<!-- html2canvas CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<div class="preview-page">
    <div class="preview-header">
        <h1>Vista Previa de Tarjeta</h1>
        <p>Visualiza tu tarjeta digital antes de descargarla</p>
        
        <div class="preview-actions">
            <a href="/create?edit=<?php echo $card_id; ?>" class="btn btn-outline">
                <i class="fas fa-edit"></i> Editar Tarjeta
            </a>
            <button id="download-png" class="btn btn-primary">
                <i class="fas fa-download"></i> Descargar PNG
            </button>
            <button id="download-jpg" class="btn btn-secondary">
                <i class="fas fa-download"></i> Descargar JPG
            </button>
            <a href="dashboard" class="btn btn-light">
                <i class="fas fa-arrow-left"></i> Volver al Dashboard
            </a>
        </div>
    </div>
    
    <!-- Google Ads -->
    <div class="ads-section" style="margin: 20px 0; text-align: center;">
        <ins class="adsbygoogle"
             style="display:block"
             data-ad-client="ca-pub-XXXXXXXXXXXXXXX"
             data-ad-slot="3344556677"
             data-ad-format="auto"
             data-full-width-responsive="true"></ins>
        <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
    </div>
    
    <div class="preview-content">
        <!-- Vista previa de la tarjeta -->
        <div class="card-preview-container">
            <div class="card-preview" id="card-preview">
                <?php
                $bg_color = $card['background_color'] ?? '#FFFFFF';
                $text_color = $card['text_color'] ?? '#000000';
                $title_color = $card['title_color'] ?? $text_color;
                $font_size = $card['font_size'] ?? 14;
                $font_family = $card['font_family'] ?? 'Arial';
                $alignment = $card['alignment'] ?? 'left';
                $format_ratio = $card['format_ratio'] ?? '16:9';
                
                // Determinar dimensiones según el formato
                $width = 800;
                $height = 450;
                
                if ($format_ratio === '1:1') {
                    $width = $height = 600;
                } elseif ($format_ratio === '2x4') {
                    $width = 400;
                    $height = 800;
                }
                ?>
                <div class="card-render" id="card-render" style="
                    background: <?php echo htmlspecialchars($bg_color); ?>;
                    color: <?php echo htmlspecialchars($text_color); ?>;
                    font-family: <?php echo htmlspecialchars($font_family); ?>;
                    font-size: <?php echo htmlspecialchars($font_size); ?>px;
                    text-align: <?php echo htmlspecialchars($alignment); ?>;
                    width: <?php echo $width; ?>px;
                    height: <?php echo $height; ?>px;
                    max-width: 100%;
                    margin: 0 auto;
                    padding: 2rem;
                    border-radius: 12px;
                    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
                    position: relative;
                    overflow: hidden;
                    box-sizing: border-box;
                ">
                    <?php if ($card['card_type'] === 'bank'): ?>
                        <h2 style="margin-bottom: 1.5rem; font-size: 1.8em; color: <?php echo htmlspecialchars($title_color); ?>;"><?php echo htmlspecialchars($card_data['bank_name'] ?? 'Banco'); ?></h2>
                        <div class="card-details">
                            <p><strong>Tipo de Cuenta:</strong> <?php echo htmlspecialchars($card_data['account_type'] ?? ''); ?></p>
                            <p><strong>Número de Cuenta:</strong> <?php echo htmlspecialchars($card_data['account_number'] ?? ''); ?></p>
                            <p><strong>Nombre:</strong> <?php echo htmlspecialchars($card_data['name'] ?? ''); ?></p>
                            <p><strong>RUT:</strong> <?php echo htmlspecialchars($card_data['rut'] ?? ''); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($card_data['email'] ?? ''); ?></p>
                        </div>
                        
                    <?php elseif ($card['card_type'] === 'tax'): ?>
                        <h2 style="margin-bottom: 1.5rem; font-size: 1.8em; color: <?php echo htmlspecialchars($title_color); ?>;"><?php echo htmlspecialchars($card_data['company_name'] ?? 'Empresa'); ?></h2>
                        <div class="card-details">
                            <p><strong>Razón Social:</strong> <?php echo htmlspecialchars($card_data['business_name'] ?? ''); ?></p>
                            <p><strong>Giro:</strong> <?php echo htmlspecialchars($card_data['industry'] ?? ''); ?></p>
                            <p><strong>Dirección:</strong> <?php echo htmlspecialchars($card_data['address'] ?? ''); ?></p>
                            <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($card_data['phone'] ?? ''); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($card_data['email'] ?? ''); ?></p>
                            <p><strong>RUT:</strong> <?php echo htmlspecialchars($card_data['rut'] ?? ''); ?></p>
                        </div>
                        
                    <?php elseif ($card['card_type'] === 'custom'): ?>
                        <h2 style="margin-bottom: 1.5rem; font-size: 1.8em; color: <?php echo htmlspecialchars($title_color); ?>;"><?php echo htmlspecialchars($card_data['title'] ?? 'Tarjeta Personal'); ?></h2>
                        <div class="card-details">
                            <?php if (isset($card_data['fields']) && is_array($card_data['fields'])): ?>
                                <?php foreach ($card_data['fields'] as $field): ?>
                                    <p><strong><?php echo htmlspecialchars($field['key'] ?? ''); ?>:</strong> <?php echo htmlspecialchars($field['value'] ?? ''); ?></p>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($card['logo_url'])): ?>
                        <div class="card-logo" style="position: absolute; top: 1rem; right: 1rem;">
                            <img src="<?php echo htmlspecialchars($card['logo_url']); ?>" alt="Logo" style="max-width: 80px; max-height: 60px; object-fit: contain; border-radius: 4px;">
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($card['logo_url2'])): ?>
                        <div class="card-logo-2" style="position: absolute; top: 1rem; left: 1rem;">
                            <img src="<?php echo htmlspecialchars($card['logo_url2']); ?>" alt="Logo 2" style="max-width: 80px; max-height: 60px; object-fit: contain; border-radius: 4px;">
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="preview-controls">
                <div class="control-group">
                    <h4>Opciones de Visualización</h4>
                    <div class="controls">
                        <button id="zoom-in" class="btn btn-sm btn-light">
                            <i class="fas fa-search-plus"></i> Zoom In
                        </button>
                        <button id="zoom-out" class="btn btn-sm btn-light">
                            <i class="fas fa-search-minus"></i> Zoom Out
                        </button>
                        <button id="reset-zoom" class="btn btn-sm btn-light">
                            <i class="fas fa-sync"></i> Restablecer
                        </button>
                        <button id="toggle-bg" class="btn btn-sm btn-light">
                            <i class="fas fa-adjust"></i> Fondo
                        </button>
                    </div>
                </div>
                
                <div class="control-group">
                    <h4>Información de la Tarjeta</h4>
                    <div class="card-info">
                        <p><strong>Tipo:</strong> 
                            <?php 
                            $type_names = [
                                'bank' => 'Bancaria',
                                'tax' => 'Tributaria', 
                                'custom' => 'Personalizada'
                            ];
                            echo htmlspecialchars($type_names[$card['card_type']] ?? $card['card_type']);
                            ?>
                        </p>
                        <p><strong>Formato:</strong> <?php echo htmlspecialchars($card['format_ratio']); ?></p>
                        <p><strong>Fuente:</strong> <?php echo htmlspecialchars($card['font_family']); ?> (<?php echo $card['font_size']; ?>px)</p>
                        <p><strong>Alineación:</strong> 
                            <?php 
                            $align_names = [
                                'left' => 'Izquierda',
                                'center' => 'Centro',
                                'right' => 'Derecha'
                            ];
                            echo htmlspecialchars($align_names[$card['alignment']] ?? $card['alignment']);
                            ?>
                        </p>
                        <p><strong>Creada:</strong> <?php echo date('d/m/Y H:i', strtotime($card['created_at'])); ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="preview-actions-bottom">
            <button id="download-png-lg" class="btn btn-primary btn-lg">
                <i class="fas fa-file-image"></i> Descargar PNG
            </button>
            <button id="download-jpg-lg" class="btn btn-secondary btn-lg">
                <i class="fas fa-file-image"></i> Descargar JPG
            </button>
            <a href="/create?edit=<?php echo $card_id; ?>" class="btn btn-outline btn-lg">
                <i class="fas fa-edit"></i> Editar Tarjeta
            </a>
            <a href="dashboard" class="btn btn-light btn-lg">
                <i class="fas fa-list"></i> Ver todas las tarjetas
            </a>
        </div>
    </div>
</div>

<style>
.preview-page {
    padding: 2rem 0;
}

.preview-header {
    margin-bottom: 2rem;
}

.preview-header h1 {
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
    color: var(--dark);
}

.preview-header p {
    color: var(--gray);
    margin-bottom: 1.5rem;
}

.preview-actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.preview-content {
    margin-top: 2rem;
}

.card-preview-container {
    background: white;
    border-radius: var(--border-radius);
    padding: 2rem;
    box-shadow: var(--shadow);
    margin-bottom: 2rem;
}

.card-preview {
    margin-bottom: 2rem;
    display: flex;
    justify-content: center;
    padding: 2rem;
    background: #f5f5f5;
    border-radius: var(--border-radius);
}

.card-render {
    transition: transform 0.3s ease;
}

.card-details p {
    margin-bottom: 0.75rem;
    font-size: 1.1em;
}

.preview-controls {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid var(--gray-light);
}

.control-group h4 {
    margin-bottom: 1rem;
    color: var(--dark);
}

.controls {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.card-info p {
    margin-bottom: 0.5rem;
    color: var(--gray);
}

.preview-actions-bottom {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-top: 2rem;
}

@media (max-width: 768px) {
    .preview-header h1 {
        font-size: 2rem;
    }
    
    .preview-actions {
        flex-direction: column;
    }
    
    .preview-controls {
        grid-template-columns: 1fr;
    }
    
    .card-render {
        width: 100% !important;
        height: auto !important;
        min-height: 300px;
    }
    
    .preview-actions-bottom {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cardRender = document.querySelector('.card-render');
    const zoomInBtn = document.getElementById('zoom-in');
    const zoomOutBtn = document.getElementById('zoom-out');
    const resetZoomBtn = document.getElementById('reset-zoom');
    const toggleBgBtn = document.getElementById('toggle-bg');
    
    let currentScale = 1;
    const minScale = 0.5;
    const maxScale = 2;
    const scaleStep = 0.1;
    
    let originalBackground = cardRender.style.background;
    let isAlternateBackground = false;
    
    // Zoom In
    zoomInBtn.addEventListener('click', function() {
        if (currentScale < maxScale) {
            currentScale += scaleStep;
            updateScale();
        }
    });
    
    // Zoom Out
    zoomOutBtn.addEventListener('click', function() {
        if (currentScale > minScale) {
            currentScale -= scaleStep;
            updateScale();
        }
    });
    
    // Reset Zoom
    resetZoomBtn.addEventListener('click', function() {
        currentScale = 1;
        updateScale();
    });
    
    // Toggle Background
    toggleBgBtn.addEventListener('click', function() {
        isAlternateBackground = !isAlternateBackground;
        
        if (isAlternateBackground) {
            cardRender.style.background = '#f0f0f0';
            cardRender.style.boxShadow = 'inset 0 0 20px rgba(0,0,0,0.1)';
        } else {
            cardRender.style.background = originalBackground;
            cardRender.style.boxShadow = '0 20px 40px rgba(0, 0, 0, 0.2)';
        }
    });
    
    function updateScale() {
        cardRender.style.transform = `scale(${currentScale})`;
        cardRender.style.transformOrigin = 'center';
        
        // Actualizar estado de botones
        zoomInBtn.disabled = currentScale >= maxScale;
        zoomOutBtn.disabled = currentScale <= minScale;
    }
    
    // Inicializar estado de botones
    updateScale();
    
    // Permitir zoom con rueda del mouse
    cardRender.addEventListener('wheel', function(e) {
        e.preventDefault();
        
        if (e.deltaY < 0) {
            if (currentScale < maxScale) {
                currentScale += scaleStep;
                updateScale();
            }
        } else {
            if (currentScale > minScale) {
                currentScale -= scaleStep;
                updateScale();
            }
        }
    });
    
    // Función para descargar la tarjeta
    function downloadCard(format) {
        const cardElement = document.getElementById('card-render');
        
        const originalTransform = cardElement.style.transform;
        cardElement.style.transform = 'scale(1)';
        
        html2canvas(cardElement, {
            backgroundColor: format === 'png' ? null : '#ffffff',
            scale: 2,
            useCORS: true,
            allowTaint: true,
            logging: false
        }).then(canvas => {
            cardElement.style.transform = originalTransform;
            
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
        }).catch(err => {
            console.error('Error al exportar:', err);
            alert('Hubo un error al generar la imagen. Por favor, intenta de nuevo.');
            cardElement.style.transform = originalTransform;
        });
    }
    
    // Eventos de descarga
    document.getElementById('download-png').addEventListener('click', function() {
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando...';
        this.disabled = true;
        const btn = this;
        setTimeout(() => {
            downloadCard('png');
            btn.innerHTML = '<i class="fas fa-download"></i> Descargar PNG';
            btn.disabled = false;
        }, 100);
    });
    
    document.getElementById('download-jpg').addEventListener('click', function() {
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando...';
        this.disabled = true;
        const btn = this;
        setTimeout(() => {
            downloadCard('jpg');
            btn.innerHTML = '<i class="fas fa-download"></i> Descargar JPG';
            btn.disabled = false;
        }, 100);
    });
    
    document.getElementById('download-png-lg').addEventListener('click', function() {
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando...';
        this.disabled = true;
        const btn = this;
        setTimeout(() => {
            downloadCard('png');
            btn.innerHTML = '<i class="fas fa-file-image"></i> Descargar PNG';
            btn.disabled = false;
        }, 100);
    });
    
    document.getElementById('download-jpg-lg').addEventListener('click', function() {
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando...';
        this.disabled = true;
        const btn = this;
        setTimeout(() => {
            downloadCard('jpg');
            btn.innerHTML = '<i class="fas fa-file-image"></i> Descargar JPG';
            btn.disabled = false;
        }, 100);
    });
});
</script>
<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../views/layouts/base.php';
?>