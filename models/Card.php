<?php
class Card {
    private $db;
    private $id;
    private $user_id;
    private $card_type;
    private $card_data;
    private $background_color;
    private $text_color;
    private $title_color;
    private $format_ratio;
    private $font_family;
    private $font_size;
    private $alignment;
    private $logo_url;
    private $logo_url2;
    private $created_at;
    private $updated_at;

    public function __construct() {
        $this->db = getDBConnection();
    }

    public function create($user_id, $data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO cards (
                    user_id, card_type, card_data, background_color, text_color, title_color,
                    format_ratio, font_family, font_size, alignment, logo_url, logo_url2
                ) VALUES (
                    :user_id, :card_type, :card_data, :background_color, :text_color, :title_color,
                    :format_ratio, :font_family, :font_size, :alignment, :logo_url, :logo_url2
                )
            ");
            
            $stmt->execute([
                ':user_id' => $user_id,
                ':card_type' => $data['card_type'],
                ':card_data' => json_encode($data['card_data'], JSON_UNESCAPED_UNICODE),
                ':background_color' => $data['background_color'] ?? '#FFFFFF',
                ':text_color' => $data['text_color'] ?? '#000000',
                ':title_color' => $data['title_color'] ?? ($data['text_color'] ?? '#000000'),
                ':format_ratio' => $data['format_ratio'] ?? '16:9',
                ':font_family' => $data['font_family'] ?? 'Arial',
                ':font_size' => $data['font_size'] ?? 14,
                ':alignment' => $data['alignment'] ?? 'left',
                ':logo_url' => $data['logo_url'] ?? null,
                ':logo_url2' => $data['logo_url2'] ?? null
            ]);

            $this->id = $this->db->lastInsertId();
            return ['success' => true, 'card_id' => $this->id];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error al crear tarjeta: ' . $e->getMessage()];
        }
    }

    public function getByUser($user_id, $limit = 20, $offset = 0) {
        $stmt = $this->db->prepare("
            SELECT id, card_type, card_data, background_color, text_color, title_color,
                   format_ratio, font_family, font_size, alignment, logo_url, logo_url2, created_at
            FROM cards 
            WHERE user_id = :user_id 
            ORDER BY created_at DESC 
            LIMIT :limit OFFSET :offset
        ");
        
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        $cards = $stmt->fetchAll();
        
        foreach ($cards as &$card) {
            $card['card_data'] = json_decode($card['card_data'], true);
            if (empty($card['title_color'])) {
                $card['title_color'] = $card['text_color'];
            }
        }
        
        return $cards;
    }

    public function getById($id, $user_id = null) {
        $sql = "SELECT * FROM cards WHERE id = :id";
        $params = [':id' => $id];
        
        if ($user_id !== null) {
            $sql .= " AND user_id = :user_id";
            $params[':user_id'] = $user_id;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        $card = $stmt->fetch();
        
        if ($card) {
            $card['card_data'] = json_decode($card['card_data'], true);
            if (empty($card['title_color'])) {
                $card['title_color'] = $card['text_color'];
            }
        }
        
        return $card;
    }

    public function update($id, $user_id, $data) {
        try {
            $existing = $this->getById($id, $user_id);
            if (!$existing) {
                return ['success' => false, 'message' => 'Tarjeta no encontrada'];
            }

            $update_fields = [];
            $params = [':id' => $id, ':user_id' => $user_id];

            if (isset($data['card_data'])) {
                $update_fields[] = 'card_data = :card_data';
                $params[':card_data'] = json_encode($data['card_data'], JSON_UNESCAPED_UNICODE);
            }
            
            if (isset($data['background_color'])) {
                $update_fields[] = 'background_color = :background_color';
                $params[':background_color'] = $data['background_color'];
            }
            
            if (isset($data['text_color'])) {
                $update_fields[] = 'text_color = :text_color';
                $params[':text_color'] = $data['text_color'];
            }
            
            if (isset($data['title_color'])) {
                $update_fields[] = 'title_color = :title_color';
                $params[':title_color'] = $data['title_color'];
            }
            
            if (isset($data['format_ratio'])) {
                $update_fields[] = 'format_ratio = :format_ratio';
                $params[':format_ratio'] = $data['format_ratio'];
            }
            
            if (isset($data['font_family'])) {
                $update_fields[] = 'font_family = :font_family';
                $params[':font_family'] = $data['font_family'];
            }
            
            if (isset($data['font_size'])) {
                $update_fields[] = 'font_size = :font_size';
                $params[':font_size'] = $data['font_size'];
            }
            
            if (isset($data['alignment'])) {
                $update_fields[] = 'alignment = :alignment';
                $params[':alignment'] = $data['alignment'];
            }
            
            if (isset($data['logo_url'])) {
                $update_fields[] = 'logo_url = :logo_url';
                $params[':logo_url'] = $data['logo_url'];
            }
            
            if (isset($data['logo_url2'])) {
                $update_fields[] = 'logo_url2 = :logo_url2';
                $params[':logo_url2'] = $data['logo_url2'];
            }

            if (empty($update_fields)) {
                return ['success' => false, 'message' => 'No hay datos para actualizar'];
            }

            $sql = "UPDATE cards SET " . implode(', ', $update_fields) . 
                   " WHERE id = :id AND user_id = :user_id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            return ['success' => true, 'affected_rows' => $stmt->rowCount()];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error al actualizar tarjeta: ' . $e->getMessage()];
        }
    }

    public function delete($id, $user_id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM cards WHERE id = :id AND user_id = :user_id");
            $stmt->execute([':id' => $id, ':user_id' => $user_id]);
            
            return ['success' => true, 'affected_rows' => $stmt->rowCount()];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error al eliminar tarjeta: ' . $e->getMessage()];
        }
    }

    public function getUserStats($user_id) {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_cards,
                COUNT(CASE WHEN card_type = 'bank' THEN 1 END) as bank_cards,
                COUNT(CASE WHEN card_type = 'tax' THEN 1 END) as tax_cards,
                COUNT(CASE WHEN card_type = 'custom' THEN 1 END) as custom_cards,
                MAX(created_at) as last_created
            FROM cards 
            WHERE user_id = :user_id
        ");
        
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetch();
    }

    public function getId() { return $this->id; }
    public function getUserId() { return $this->user_id; }
    public function getCardType() { return $this->card_type; }
    public function getCardData() { return $this->card_data; }
    public function getBackgroundColor() { return $this->background_color; }
    public function getTextColor() { return $this->text_color; }
    public function getTitleColor() { return $this->title_color; }
    public function getFormatRatio() { return $this->format_ratio; }
    public function getCreatedAt() { return $this->created_at; }
}
?>