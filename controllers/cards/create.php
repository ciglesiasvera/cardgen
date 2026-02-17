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

$title = 'Crear Tarjeta';
$user_id = $_SESSION['user_id'];
$error = '';
$success = '';
$card_types = ['bank' => 'Tarjeta Bancaria', 'tax' => 'Tarjeta Tributaria', 'custom' => 'Tarjeta Personalizada'];

// Si estamos editando una tarjeta existente
$edit_id = $_GET['edit'] ?? null;
$card_data = null;
$card = null;

if ($edit_id) {
    $cardModel = new Card();
    $card = $cardModel->getById($edit_id, $user_id);
    if (!$card) {
        redirectWithMessage('dashboard', 'error', 'Tarjeta no encontrada');
        exit();
    }
    $card_data = $card['card_data'];
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $card_type = sanitizeInput($_POST['card_type'] ?? '');
    $background_color = sanitizeInput($_POST['background_color'] ?? '#FFFFFF');
    $text_color = sanitizeInput($_POST['text_color'] ?? '#000000');
    $format_ratio = sanitizeInput($_POST['format_ratio'] ?? '16:9');
    $font_family = sanitizeInput($_POST['font_family'] ?? 'Arial');
    $font_size = intval($_POST['font_size'] ?? 14);
    $alignment = sanitizeInput($_POST['alignment'] ?? 'left');
    
    // Validaciones básicas
    if (!in_array($card_type, ['bank', 'tax', 'custom'])) {
        $error = 'Tipo de tarjeta inválido';
    } elseif (!preg_match('/^#[0-9A-Fa-f]{6}$/', $background_color)) {
        $error = 'Color de fondo inválido';
    } elseif (!preg_match('/^#[0-9A-Fa-f]{6}$/', $text_color)) {
        $error = 'Color de texto inválido';
    } elseif ($font_size < 8 || $font_size > 72) {
        $error = 'Tamaño de fuente inválido (8-72)';
    } else {
        // Procesar datos según el tipo de tarjeta
        $data = ['card_type' => $card_type];
        
        if ($card_type === 'bank') {
            $bank_data = [
                'bank_name' => sanitizeInput($_POST['bank_name'] ?? ''),
                'account_type' => sanitizeInput($_POST['account_type'] ?? ''),
                'account_number' => sanitizeInput($_POST['account_number'] ?? ''),
                'name' => sanitizeInput($_POST['name'] ?? ''),
                'rut' => sanitizeInput($_POST['rut'] ?? ''),
                'email' => sanitizeInput($_POST['email'] ?? '')
            ];
            $data['card_data'] = $bank_data;
            
        } elseif ($card_type === 'tax') {
            $tax_data = [
                'company_name' => sanitizeInput($_POST['company_name'] ?? ''),
                'business_name' => sanitizeInput($_POST['business_name'] ?? ''),
                'industry' => sanitizeInput($_POST['industry'] ?? ''),
                'address' => sanitizeInput($_POST['address'] ?? ''),
                'phone' => sanitizeInput($_POST['phone'] ?? ''),
                'email' => sanitizeInput($_POST['email'] ?? ''),
                'rut' => sanitizeInput($_POST['rut'] ?? '')
            ];
            $data['card_data'] = $tax_data;
            
        } elseif ($card_type === 'custom') {
            $custom_title = sanitizeInput($_POST['custom_title'] ?? '');
            $fields = [];
            
            if (isset($_POST['field_keys']) && isset($_POST['field_values'])) {
                $keys = $_POST['field_keys'];
                $values = $_POST['field_values'];
                
                for ($i = 0; $i < count($keys); $i++) {
                    if (!empty(trim($keys[$i])) && !empty(trim($values[$i]))) {
                        $fields[] = [
                            'key' => sanitizeInput($keys[$i]),
                            'value' => sanitizeInput($values[$i])
                        ];
                    }
                }
            }
            
            $data['card_data'] = [
                'title' => $custom_title,
                'fields' => $fields
            ];
        }
        
        // Configuración visual
        $data['background_color'] = $background_color;
        $data['text_color'] = $text_color;
        $data['format_ratio'] = $format_ratio;
        $data['font_family'] = $font_family;
        $data['font_size'] = $font_size;
        $data['alignment'] = $alignment;
        
        // Manejar subida de logos (simulado por ahora)
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            // En un entorno real, aquí se procesaría la imagen
            $data['logo_url'] = '/public/uploads/temp/logo_' . uniqid() . '.png';
        }
        
        if (isset($_FILES['logo2']) && $_FILES['logo2']['error'] === UPLOAD_ERR_OK) {
            $data['logo_url2'] = '/public/uploads/temp/logo2_' . uniqid() . '.png';
        }
        
        // Guardar tarjeta
        $cardModel = new Card();
        
        if ($edit_id) {
            // Actualizar tarjeta existente
            $result = $cardModel->update($edit_id, $user_id, $data);
        } else {
            // Crear nueva tarjeta
            $result = $cardModel->create($user_id, $data);
        }
        
        if ($result['success']) {
            $card_id = $edit_id ?? $result['card_id'];
            redirectWithMessage('/preview?id=' . $card_id, 'success', 
                $edit_id ? '¡Tarjeta actualizada correctamente!' : '¡Tarjeta creada correctamente!');
            exit();
        } else {
            $error = $result['message'];
        }
    }
}

// Vista
ob_start();
?>
<div class="card-creator">
    <h1><?php echo $edit_id ? 'Editar Tarjeta' : 'Crear Nueva Tarjeta'; ?></h1>
    <p>Diseña tu tarjeta digital personalizada</p>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <div class="creator-layout">
        <!-- Panel izquierdo: Formulario -->
        <div class="form-panel">
            <form id="card-form" method="POST" enctype="multipart/form-data">
                <!-- Tipo de tarjeta -->
                <div class="form-section">
                    <h3><i class="fas fa-layer-group"></i> Tipo de Tarjeta</h3>
                    <div class="card-type-selector">
                        <?php foreach ($card_types as $value => $label): ?>
                            <label class="card-type-option">
                                <input type="radio" name="card_type" value="<?php echo $value; ?>" 
                                       required <?php echo ($card['card_type'] ?? '') === $value ? 'checked' : ''; ?>>
                                <div class="card-type-content">
                                    <i class="fas <?php echo $value === 'bank' ? 'fa-university' : ($value === 'tax' ? 'fa-building' : 'fa-cogs'); ?>"></i>
                                    <span><?php echo $label; ?></span>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Campos dinámicos según tipo -->
                <div class="form-section" id="bank-fields" style="display: none;">
                    <h3><i class="fas fa-university"></i> Datos Bancarios</h3>
                    <div class="form-group">
                        <label>Nombre del Banco</label>
                        <input type="text" name="bank_name" 
                               value="<?php echo htmlspecialchars($card_data['bank_name'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Tipo de Cuenta</label>
                        <input type="text" name="account_type" 
                               value="<?php echo htmlspecialchars($card_data['account_type'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Número de Cuenta</label>
                        <input type="text" name="account_number" 
                               value="<?php echo htmlspecialchars($card_data['account_number'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Nombre Empresa/Persona</label>
                        <input type="text" name="name" 
                               value="<?php echo htmlspecialchars($card_data['name'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>RUT</label>
                        <input type="text" name="rut" 
                               value="<?php echo htmlspecialchars($card_data['rut'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Correo</label>
                        <input type="email" name="email" 
                               value="<?php echo htmlspecialchars($card_data['email'] ?? ''); ?>">
                    </div>
                </div>
                
                <div class="form-section" id="tax-fields" style="display: none;">
                    <h3><i class="fas fa-building"></i> Datos Tributarios</h3>
                    <div class="form-group">
                        <label>Nombre Empresa</label>
                        <input type="text" name="company_name" 
                               value="<?php echo htmlspecialchars($card_data['company_name'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Razón Social</label>
                        <input type="text" name="business_name" 
                               value="<?php echo htmlspecialchars($card_data['business_name'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Giro</label>
                        <input type="text" name="industry" 
                               value="<?php echo htmlspecialchars($card_data['industry'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Dirección</label>
                        <input type="text" name="address" 
                               value="<?php echo htmlspecialchars($card_data['address'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Teléfono</label>
                        <input type="tel" name="phone" 
                               value="<?php echo htmlspecialchars($card_data['phone'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Correo</label>
                        <input type="email" name="email" 
                               value="<?php echo htmlspecialchars($card_data['email'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>RUT</label>
                        <input type="text" name="rut" 
                               value="<?php echo htmlspecialchars($card_data['rut'] ?? ''); ?>">
                    </div>
                </div>
                
                <div class="form-section" id="custom-fields" style="display: none;">
                    <h3><i class="fas fa-cogs"></i> Campos Personalizados</h3>
                    <div class="form-group">
                        <label>Título de la Tarjeta</label>
                        <input type="text" name="custom_title" 
                               value="<?php echo htmlspecialchars($card_data['title'] ?? ''); ?>">
                    </div>
                    
                    <div id="custom-fields-container">
                        <?php if (!empty($card_data['fields'])): ?>
                            <?php foreach ($card_data['fields'] as $index => $field): ?>
                                <div class="field-row" data-index="<?php echo $index; ?>">
                                    <input type="text" name="field_keys[]" placeholder="Etiqueta" 
                                           value="<?php echo htmlspecialchars($field['key']); ?>">
                                    <input type="text" name="field_values[]" placeholder="Valor" 
                                           value="<?php echo htmlspecialchars($field['value']); ?>">
                                    <button type="button" class="btn btn-danger remove-field"><i class="fas fa-trash"></i></button>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="field-row" data-index="0">
                                <input type="text" name="field_keys[]" placeholder="Etiqueta">
                                <input type="text" name="field_values[]" placeholder="Valor">
                                <button type="button" class="btn btn-danger remove-field"><i class="fas fa-trash"></i></button>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <button type="button" id="add-field" class="btn btn-outline">
                        <i class="fas fa-plus"></i> Agregar Campo
                    </button>
                </div>
                
                <!-- Personalización visual -->
                <div class="form-section">
                    <h3><i class="fas fa-palette"></i> Personalización Visual</h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Color de Fondo</label>
                            <input type="color" name="background_color" 
                                   value="<?php echo htmlspecialchars($card['background_color'] ?? '#FFFFFF'); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>Color de Texto</label>
                            <input type="color" name="text_color" 
                                   value="<?php echo htmlspecialchars($card['text_color'] ?? '#000000'); ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Formato/Proporción</label>
                        <select name="format_ratio">
                            <option value="1:1" <?php echo ($card['format_ratio'] ?? '') === '1:1' ? 'selected' : ''; ?>>1:1 (Cuadrado)</option>
                            <option value="16:9" <?php echo ($card['format_ratio'] ?? '16:9') === '16:9' ? 'selected' : ''; ?>>16:9 (Pantalla)</option>
                            <option value="2x4" <?php echo ($card['format_ratio'] ?? '') === '2x4' ? 'selected' : ''; ?>>2x4 Vertical</option>
                            <option value="custom" <?php echo ($card['format_ratio'] ?? '') === 'custom' ? 'selected' : ''; ?>>Personalizado</option>
                        </select>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Tipografía</label>
                            <select name="font_family">
                                <option value="Arial" <?php echo ($card['font_family'] ?? 'Arial') === 'Arial' ? 'selected' : ''; ?>>Arial</option>
                                <option value="Helvetica" <?php echo ($card['font_family'] ?? '') === 'Helvetica' ? 'selected' : ''; ?>>Helvetica</option>
                                <option value="Times New Roman" <?php echo ($card['font_family'] ?? '') === 'Times New Roman' ? 'selected' : ''; ?>>Times New Roman</option>
                                <option value="Georgia" <?php echo ($card['font_family'] ?? '') === 'Georgia' ? 'selected' : ''; ?>>Georgia</option>
                                <option value="Courier New" <?php echo ($card['font_family'] ?? '') === 'Courier New' ? 'selected' : ''; ?>>Courier New</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Tamaño de Fuente</label>
                            <input type="number" name="font_size" min="8" max="72" 
                                   value="<?php echo htmlspecialchars($card['font_size'] ?? 14); ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Alineación</label>
                        <select name="alignment">
                            <option value="left" <?php echo ($card['alignment'] ?? 'left') === 'left' ? 'selected' : ''; ?>>Izquierda</option>
                            <option value="center" <?php echo ($card['alignment'] ?? '') === 'center' ? 'selected' : ''; ?>>Centro</option>
                            <option value="right" <?php echo ($card['alignment'] ?? '') === 'right' ? 'selected' : ''; ?>>Derecha</option>
                        </select>
                    </div>
                </div>
                
                <!-- Subida de logos -->
                <div class="form-section">
                    <h3><i class="fas fa-images"></i> Logos</h3>
                    <div class="form-group">
                        <label>Logo Principal (PNG/JPG, max 2MB)</label>
                        <input type="file" name="logo" accept="image/png,image/jpeg">
                    </div>
                    <div class="form-group">
                        <label>Logo Adicional (Opcional)</label>
                        <input type="file" name="logo2" accept="image/png,image/jpeg">
                    </div>
                </div>
                
                <!-- Google Ads -->
                <div class="ads-section" style="margin: 20px 0; text-align: center;">
                    <ins class="adsbygoogle"
                         style="display:block"
                         data-ad-client="ca-pub-XXXXXXXXXXXXXXX"
                         data-ad-slot="5678901234"
                         data-ad-format="auto"
                         data-full-width-responsive="true"></ins>
                    <script>
                        (adsbygoogle = window.adsbygoogle || []).push({});
                    </script>
                </div>
                
                <!-- Botones -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-large">
                        <i class="fas fa-save"></i> <?php echo $edit_id ? 'Actualizar Tarjeta' : 'Guardar y Continuar'; ?>
                    </button>
                    <a href="/dashboard" class="btn btn-outline">Cancelar</a>
                </div>
            </form>
        </div>
        
        <!-- Panel derecho: Vista Previa -->
        <div class="preview-panel">
            <h3><i class="fas fa-eye"></i> Vista Previa</h3>
            <div class="preview-container">
                <div id="card-preview" class="card-preview-render">
                    <!-- La vista previa se actualiza con JavaScript -->
                    <div class="preview-placeholder">
                        <i class="fas fa-id-card fa-4x"></i>
                        <p>La vista previa aparecerá aquí</p>
                    </div>
                </div>
                
                <div class="preview-controls">
                    <button id="update-preview" class="btn btn-outline">
                        <i class="fas fa-sync"></i> Actualizar Vista Previa
                    </button>
                    <button id="export-preview" class="btn btn-primary" disabled>
                        <i class="fas fa-download"></i> Descargar
                    </button>
                </div>
            </div>
            
            <div class="preview-info">
                <h4>Instrucciones:</h4>
                <ul>
                    <li>Completa los campos del formulario</li>
                    <li>La vista previa se actualiza automáticamente</li>
                    <li>Puedes personalizar colores, fuentes y formato</li>
                    <li>Haz clic en "Guardar y Continuar" para descargar</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
.card-creator {
    padding: 1rem 0;
}

.creator-layout {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    margin-top: 2rem;
}

.form-panel {
    background: white;
    border-radius: var(--border-radius);
    padding: 1.5rem;
    box-shadow: var(--shadow);
    max-height: 80vh;
    overflow-y: auto;
}

.preview-panel {
    background: white;
    border-radius: var(--border-radius);
    padding: 1.5rem;
    box-shadow: var(--shadow);
    display: flex;
    flex-direction: column;
}

.form-section {
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid var(--gray-light);
}

.form-section:last-child {
    border-bottom: none;
}

.form-section h3 {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
    color: var(--dark);
    font-size: 1.1rem;
}

.card-type-selector {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.5rem;
}

.card-type-option input[type="radio"] {
    display: none;
}

.card-type-content {
    padding: 1rem;
    border: 2px solid var(--gray-light);
    border-radius: var(--border-radius);
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
}

.card-type-content i {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
    color: var(--gray);
}

.card-type-content span {
    display: block;
    font-weight: 500;
}

.card-type-option input[type="radio"]:checked + .card-type-content {
    border-color: var(--primary);
    background: rgba(79, 70, 229, 0.1);
}

.card-type-option input[type="radio"]:checked + .card-type-content i {
    color: var(--primary);
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: var(--dark);
}

.form-group input[type="text"],
.form-group input[type="email"],
.form-group input[type="tel"],
.form-group input[type="number"],
.form-group input[type="color"],
.form-group select {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid var(--gray-light);
    border-radius: var(--border-radius);
    font-size: 1rem;
    transition: border-color 0.3s;
}

.form-group input:focus,
.form-group select:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.field-row {
    display: grid;
    grid-template-columns: 1fr 1fr auto;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
    align-items: center;
}

.remove-field {
    padding: 0.5rem;
    min-width: auto;
}

.preview-container {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.card-preview-render {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--light);
    border-radius: var(--border-radius);
    border: 2px dashed var(--gray-light);
    margin-bottom: 1rem;
    padding: 2rem;
    min-height: 300px;
}

.preview-placeholder {
    text-align: center;
    color: var(--gray);
}

.preview-placeholder i {
    margin-bottom: 1rem;
}

.preview-controls {
    display: flex;
    gap: 0.5rem;
}

.btn-large {
    padding: 1rem 2rem;
    font-size: 1.1rem;
}

.preview-info {
    margin-top: 2rem;
    padding: 1rem;
    background: var(--light);
    border-radius: var(--border-radius);
}

.preview-info h4 {
    margin-bottom: 0.5rem;
    color: var(--dark);
}

.preview-info ul {
    padding-left: 1.5rem;
    color: var(--gray);
}

.preview-info li {
    margin-bottom: 0.25rem;
}

@media (max-width: 1024px) {
    .creator-layout {
        grid-template-columns: 1fr;
    }
    
    .card-type-selector {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mostrar/ocultar campos según tipo de tarjeta
    const cardTypeRadios = document.querySelectorAll('input[name="card_type"]');
    const bankFields = document.getElementById('bank-fields');
    const taxFields = document.getElementById('tax-fields');
    const customFields = document.getElementById('custom-fields');
    
    function showFieldsForType(type) {
        bankFields.style.display = type === 'bank' ? 'block' : 'none';
        taxFields.style.display = type === 'tax' ? 'block' : 'none';
        customFields.style.display = type === 'custom' ? 'block' : 'none';
    }
    
    // Establecer tipo inicial si estamos editando
    const initialType = document.querySelector('input[name="card_type"]:checked');
    if (initialType) {
        showFieldsForType(initialType.value);
    }
    
    cardTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            showFieldsForType(this.value);
        });
    });
    
    // Manejar campos personalizados dinámicos
    const addFieldBtn = document.getElementById('add-field');
    const fieldsContainer = document.getElementById('custom-fields-container');
    let fieldIndex = document.querySelectorAll('.field-row').length;
    
    addFieldBtn.addEventListener('click', function() {
        const row = document.createElement('div');
        row.className = 'field-row';
        row.setAttribute('data-index', fieldIndex);
        
        row.innerHTML = `
            <input type="text" name="field_keys[]" placeholder="Etiqueta">
            <input type="text" name="field_values[]" placeholder="Valor">
            <button type="button" class="btn btn-danger remove-field"><i class="fas fa-trash"></i></button>
        `;
        
        fieldsContainer.appendChild(row);
        fieldIndex++;
        
        // Agregar evento para eliminar
        row.querySelector('.remove-field').addEventListener('click', function() {
            row.remove();
        });
    });
    
    // Eliminar campos existentes
    document.querySelectorAll('.remove-field').forEach(btn => {
        btn.addEventListener('click', function() {
            this.closest('.field-row').remove();
        });
    });
    
    // Actualizar vista previa
    const updatePreviewBtn = document.getElementById('update-preview');
    const cardPreview = document.getElementById('card-preview');
    const exportBtn = document.getElementById('export-preview');
    
    updatePreviewBtn.addEventListener('click', updatePreview);
    
    // También actualizar al cambiar campos
    document.querySelectorAll('#card-form input, #card-form select').forEach(element => {
        element.addEventListener('input', updatePreview);
    });
    
    function updatePreview() {
        const form = document.getElementById('card-form');
        const formData = new FormData(form);
        const data = {};
        
        // Convertir FormData a objeto simple
        for (let [key, value] of formData.entries()) {
            data[key] = value;
        }
        
        // Crear HTML de vista previa
        let previewHTML = '';
        const bgColor = data.background_color || '#FFFFFF';
        const textColor = data.text_color || '#000000';
        const fontSize = data.font_size || '14';
        const fontFamily = data.font_family || 'Arial';
        const alignment = data.alignment || 'left';
        
        previewHTML = `
            <div class="preview-card" style="
                background: ${bgColor};
                color: ${textColor};
                font-family: ${fontFamily};
                font-size: ${fontSize}px;
                text-align: ${alignment};
                padding: 2rem;
                border-radius: 8px;
                width: 100%;
                height: 100%;
                display: flex;
                flex-direction: column;
                justify-content: center;
            ">
        `;
        
        // Contenido según tipo
        const cardType = data.card_type;
        
        if (cardType === 'bank' && data.bank_name) {
            previewHTML += `
                <h2 style="margin-bottom: 1rem;">${data.bank_name}</h2>
                <p><strong>Tipo de Cuenta:</strong> ${data.account_type || ''}</p>
                <p><strong>Número:</strong> ${data.account_number || ''}</p>
                <p><strong>Nombre:</strong> ${data.name || ''}</p>
                <p><strong>RUT:</strong> ${data.rut || ''}</p>
                <p><strong>Email:</strong> ${data.email || ''}</p>
            `;
        } else if (cardType === 'tax' && data.company_name) {
            previewHTML += `
                <h2 style="margin-bottom: 1rem;">${data.company_name}</h2>
                <p><strong>Razón Social:</strong> ${data.business_name || ''}</p>
                <p><strong>Giro:</strong> ${data.industry || ''}</p>
                <p><strong>Dirección:</strong> ${data.address || ''}</p>
                <p><strong>Teléfono:</strong> ${data.phone || ''}</p>
                <p><strong>Email:</strong> ${data.email || ''}</p>
                <p><strong>RUT:</strong> ${data.rut || ''}</p>
            `;
        } else if (cardType === 'custom' && data.custom_title) {
            previewHTML += `<h2 style="margin-bottom: 1rem;">${data.custom_title}</h2>`;
            
            // Campos dinámicos
            const keys = Array.from(document.querySelectorAll('input[name="field_keys[]"]'));
            const values = Array.from(document.querySelectorAll('input[name="field_values[]"]'));
            
            for (let i = 0; i < keys.length; i++) {
                if (keys[i].value && values[i].value) {
                    previewHTML += `<p><strong>${keys[i].value}:</strong> ${values[i].value}</p>`;
                }
            }
        } else {
            previewHTML += `
                <div class="preview-placeholder">
                    <i class="fas fa-id-card fa-3x"></i>
                    <p>Completa el formulario para ver la vista previa</p>
                </div>
            `;
        }
        
        previewHTML += '</div>';
        cardPreview.innerHTML = previewHTML;
        
        // Habilitar botón de exportación si hay contenido
        const hasContent = (cardType === 'bank' && data.bank_name) || 
                          (cardType === 'tax' && data.company_name) || 
                          (cardType === 'custom' && data.custom_title);
        
        exportBtn.disabled = !hasContent;
    }
    
    // Exportar (simulado)
    exportBtn.addEventListener('click', function() {
        alert('En la versión completa, esto descargaría la tarjeta como PNG/JPG');
        // En la implementación real, aquí se llamaría al endpoint de exportación
        // window.location.href = '/export?from=preview&data=' + encodeURIComponent(JSON.stringify(data));
    });
    
    // Inicializar vista previa
    updatePreview();
});
</script>
<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../views/layouts/base.php';
?>