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

$title = 'Dashboard';
$user_id = $_SESSION['user_id'];

// Obtener usuario y estadísticas
$userModel = new User();
$user = $userModel->getUserById($user_id);

$cardModel = new Card();
$stats = $cardModel->getUserStats($user_id);
$cards = $cardModel->getByUser($user_id, 10);

// Vista
ob_start();
?>
<div class="dashboard">
    <div class="dashboard-header">
        <h1 class="dashboard-title">Dashboard</h1>
        <p class="dashboard-subtitle">Bienvenido, <?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
        <a href="/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Crear Nueva Tarjeta
        </a>
    </div>

    <!-- Google Ads entre secciones -->
    <div class="ads-section" style="margin: 20px 0; text-align: center;">
        <ins class="adsbygoogle"
             style="display:block"
             data-ad-client="ca-pub-XXXXXXXXXXXXXXX"
             data-ad-slot="9876543210"
             data-ad-format="auto"
             data-full-width-responsive="true"></ins>
        <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
    </div>

    <!-- Estadísticas -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: #4f46e5;">
                <i class="fas fa-id-card"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['total_cards'] ?? 0; ?></h3>
                <p>Tarjetas Totales</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: #10b981;">
                <i class="fas fa-university"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['bank_cards'] ?? 0; ?></h3>
                <p>Tarjetas Bancarias</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: #f59e0b;">
                <i class="fas fa-building"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['tax_cards'] ?? 0; ?></h3>
                <p>Tarjetas Tributarias</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: #3b82f6;">
                <i class="fas fa-cogs"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['custom_cards'] ?? 0; ?></h3>
                <p>Tarjetas Personalizadas</p>
            </div>
        </div>
    </div>

    <!-- Tarjetas recientes -->
    <div class="recent-cards">
        <h2>Tarjetas Recientes</h2>
        
        <?php if (empty($cards)): ?>
            <div class="empty-state">
                <i class="fas fa-id-card fa-3x"></i>
                <h3>No tienes tarjetas aún</h3>
                <p>Crea tu primera tarjeta para comenzar</p>
                <a href="/create" class="btn btn-primary">Crear Primera Tarjeta</a>
            </div>
        <?php else: ?>
            <div class="cards-grid">
                <?php foreach ($cards as $card): ?>
                    <div class="card-preview">
                        <div class="card-preview-header" 
                             style="background: <?php echo htmlspecialchars($card['background_color']); ?>;">
                            <h4 style="color: <?php echo htmlspecialchars($card['text_color']); ?>;">
                                <?php 
                                $type_names = [
                                    'bank' => 'Bancaria',
                                    'tax' => 'Tributaria', 
                                    'custom' => 'Personalizada'
                                ];
                                echo htmlspecialchars($type_names[$card['card_type']] ?? $card['card_type']);
                                ?>
                            </h4>
                            <span class="card-format"><?php echo htmlspecialchars($card['format_ratio']); ?></span>
                        </div>
                        <div class="card-preview-body">
                            <?php 
                            $card_data = $card['card_data'];
                            if ($card['card_type'] === 'bank' && isset($card_data['bank_name'])): ?>
                                <p><strong>Banco:</strong> <?php echo htmlspecialchars($card_data['bank_name']); ?></p>
                                <p><strong>Nombre:</strong> <?php echo htmlspecialchars($card_data['name'] ?? ''); ?></p>
                            <?php elseif ($card['card_type'] === 'tax' && isset($card_data['company_name'])): ?>
                                <p><strong>Empresa:</strong> <?php echo htmlspecialchars($card_data['company_name']); ?></p>
                                <p><strong>RUT:</strong> <?php echo htmlspecialchars($card_data['rut'] ?? ''); ?></p>
                            <?php elseif ($card['card_type'] === 'custom' && isset($card_data['title'])): ?>
                                <p><strong><?php echo htmlspecialchars($card_data['title']); ?></strong></p>
                                <?php if (isset($card_data['fields']) && is_array($card_data['fields'])): ?>
                                    <p><small><?php echo count($card_data['fields']); ?> campos</small></p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        <div class="card-preview-actions">
                            <a href="/preview?id=<?php echo $card['id']; ?>" class="btn btn-outline btn-sm">
                                <i class="fas fa-eye"></i> Ver
                            </a>
                            <a href="/create?edit=<?php echo $card['id']; ?>" class="btn btn-outline btn-sm">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <?php if (count($cards) >= 10): ?>
                <div class="text-center" style="margin-top: 2rem;">
                    <a href="#" class="btn btn-outline">Ver todas las tarjetas</a>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<style>
.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.dashboard-title {
    font-size: 2rem;
    color: var(--dark);
    margin: 0;
}

.dashboard-subtitle {
    color: var(--gray);
    margin: 0.5rem 0 0 0;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin: 2rem 0;
}

.stat-card {
    background: white;
    border-radius: var(--border-radius);
    padding: 1.5rem;
    box-shadow: var(--shadow);
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: transform 0.3s;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
}

.stat-info h3 {
    font-size: 1.8rem;
    margin: 0;
    color: var(--dark);
}

.stat-info p {
    margin: 0.25rem 0 0 0;
    color: var(--gray);
}

.recent-cards {
    margin-top: 3rem;
}

.recent-cards h2 {
    font-size: 1.5rem;
    margin-bottom: 1.5rem;
    color: var(--dark);
}

.empty-state {
    text-align: center;
    padding: 3rem;
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
}

.empty-state i {
    color: var(--gray-light);
    margin-bottom: 1rem;
}

.empty-state h3 {
    margin: 1rem 0 0.5rem 0;
    color: var(--dark);
}

.empty-state p {
    color: var(--gray);
    margin-bottom: 1.5rem;
}

.cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.5rem;
}

.card-preview {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
    overflow: hidden;
    transition: transform 0.3s;
}

.card-preview:hover {
    transform: translateY(-5px);
}

.card-preview-header {
    padding: 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-preview-header h4 {
    margin: 0;
    font-size: 1rem;
}

.card-format {
    background: rgba(255, 255, 255, 0.2);
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.8rem;
}

.card-preview-body {
    padding: 1rem;
}

.card-preview-body p {
    margin: 0.5rem 0;
    font-size: 0.9rem;
}

.card-preview-actions {
    padding: 1rem;
    border-top: 1px solid var(--gray-light);
    display: flex;
    gap: 0.5rem;
}

.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
}

.text-center {
    text-align: center;
}

@media (max-width: 768px) {
    .dashboard-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .cards-grid {
        grid-template-columns: 1fr;
    }
}
</style>
<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../views/layouts/base.php';
?>