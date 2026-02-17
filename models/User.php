<?php
class User {
    private $db;
    private $id;
    private $email;
    private $password_hash;
    private $is_verified;
    private $verification_token;
    private $verification_token_expires;
    private $password_reset_token;
    private $password_reset_expires;
    private $created_at;
    private $updated_at;

    public function __construct() {
        $this->db = getDBConnection();
    }

    // Método para registrar un nuevo usuario
    public function register($email, $password) {
        // Verificar si el email ya existe
        if ($this->emailExists($email)) {
            return ['success' => false, 'message' => 'El email ya está registrado'];
        }

        // Hash de la contraseña
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        
        // Generar token de verificación
        $verification_token = bin2hex(random_bytes(32));
        $verification_token_expires = date('Y-m-d H:i:s', strtotime('+24 hours'));

        try {
            $stmt = $this->db->prepare("
                INSERT INTO users (email, password_hash, verification_token, verification_token_expires) 
                VALUES (:email, :password_hash, :verification_token, :verification_token_expires)
            ");
            
            $stmt->execute([
                ':email' => $email,
                ':password_hash' => $password_hash,
                ':verification_token' => $verification_token,
                ':verification_token_expires' => $verification_token_expires
            ]);

            $this->id = $this->db->lastInsertId();
            
            return [
                'success' => true, 
                'user_id' => $this->id,
                'verification_token' => $verification_token
            ];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error al registrar usuario: ' . $e->getMessage()];
        }
    }

    // Verificar si el email ya existe
    public function emailExists($email) {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch() !== false;
    }

    // Verificar credenciales de login
    public function login($email, $password) {
        $stmt = $this->db->prepare("
            SELECT id, email, password_hash, is_verified 
            FROM users 
            WHERE email = :email
        ");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if (!$user) {
            return ['success' => false, 'message' => 'Email o contraseña incorrectos'];
        }

        // Verificar contraseña
        if (!password_verify($password, $user['password_hash'])) {
            return ['success' => false, 'message' => 'Email o contraseña incorrectos'];
        }

        // Verificar si la cuenta está verificada
        if (!$user['is_verified']) {
            return ['success' => false, 'message' => 'Por favor verifica tu email antes de iniciar sesión'];
        }

        return [
            'success' => true,
            'user_id' => $user['id'],
            'email' => $user['email'],
            'is_verified' => $user['is_verified']
        ];
    }

    // Verificar token de verificación
    public function verifyAccount($token) {
        $stmt = $this->db->prepare("
            SELECT id, verification_token_expires 
            FROM users 
            WHERE verification_token = :token AND is_verified = 0
        ");
        $stmt->execute([':token' => $token]);
        $user = $stmt->fetch();

        if (!$user) {
            return ['success' => false, 'message' => 'Token inválido o cuenta ya verificada'];
        }

        // Verificar que el token no haya expirado
        if (strtotime($user['verification_token_expires']) < time()) {
            return ['success' => false, 'message' => 'El token ha expirado'];
        }

        // Actualizar usuario como verificado
        $stmt = $this->db->prepare("
            UPDATE users 
            SET is_verified = 1, verification_token = NULL, verification_token_expires = NULL 
            WHERE id = :id
        ");
        $stmt->execute([':id' => $user['id']]);

        return ['success' => true, 'user_id' => $user['id']];
    }

    // Solicitar recuperación de contraseña
    public function requestPasswordReset($email) {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = :email AND is_verified = 1");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if (!$user) {
            return ['success' => false, 'message' => 'Email no encontrado o cuenta no verificada'];
        }

        // Generar token de recuperación
        $reset_token = bin2hex(random_bytes(32));
        $reset_expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $stmt = $this->db->prepare("
            UPDATE users 
            SET password_reset_token = :reset_token, password_reset_expires = :reset_expires 
            WHERE id = :id
        ");
        $stmt->execute([
            ':reset_token' => $reset_token,
            ':reset_expires' => $reset_expires,
            ':id' => $user['id']
        ]);

        return ['success' => true, 'reset_token' => $reset_token, 'user_id' => $user['id']];
    }

    // Verificar token de recuperación de contraseña
    public function verifyResetToken($token) {
        $stmt = $this->db->prepare("
            SELECT id, password_reset_expires 
            FROM users 
            WHERE password_reset_token = :token
        ");
        $stmt->execute([':token' => $token]);
        $user = $stmt->fetch();

        if (!$user) {
            return ['success' => false, 'message' => 'Token inválido'];
        }

        // Verificar que el token no haya expirado
        if (strtotime($user['password_reset_expires']) < time()) {
            return ['success' => false, 'message' => 'El token ha expirado'];
        }

        return ['success' => true, 'user_id' => $user['id']];
    }

    // Resetear contraseña con token
    public function resetPassword($token, $new_password) {
        $stmt = $this->db->prepare("
            SELECT id, password_reset_expires 
            FROM users 
            WHERE password_reset_token = :token
        ");
        $stmt->execute([':token' => $token]);
        $user = $stmt->fetch();

        if (!$user) {
            return ['success' => false, 'message' => 'Token inválido'];
        }

        // Verificar que el token no haya expirado
        if (strtotime($user['password_reset_expires']) < time()) {
            return ['success' => false, 'message' => 'El token ha expirado'];
        }

        // Hash de la nueva contraseña
        $password_hash = password_hash($new_password, PASSWORD_BCRYPT);

        // Actualizar contraseña y limpiar tokens
        $stmt = $this->db->prepare("
            UPDATE users 
            SET password_hash = :password_hash, 
                password_reset_token = NULL, 
                password_reset_expires = NULL 
            WHERE id = :id
        ");
        $stmt->execute([
            ':password_hash' => $password_hash,
            ':id' => $user['id']
        ]);

        return ['success' => true, 'user_id' => $user['id']];
    }

    // Resetear contraseña por ID de usuario
    public function resetPasswordById($user_id, $new_password) {
        // Hash de la nueva contraseña
        $password_hash = password_hash($new_password, PASSWORD_BCRYPT);

        // Actualizar contraseña
        $stmt = $this->db->prepare("
            UPDATE users 
            SET password_hash = :password_hash
            WHERE id = :id
        ");
        $stmt->execute([
            ':password_hash' => $password_hash,
            ':id' => $user_id
        ]);

        return ['success' => true, 'affected_rows' => $stmt->rowCount()];
    }

    // Obtener usuario por ID
    public function getUserById($id) {
        $stmt = $this->db->prepare("
            SELECT id, email, is_verified, created_at 
            FROM users 
            WHERE id = :id
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    // Métodos getter
    public function getId() { return $this->id; }
    public function getEmail() { return $this->email; }
    public function isVerified() { return $this->is_verified; }
}
?>