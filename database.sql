-- CardGen Pro - Base de datos MySQL
-- Versión: 1.0

DROP DATABASE IF EXISTS cardgen_pro;
CREATE DATABASE cardgen_pro CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cardgen_pro;

-- Tabla de usuarios
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(191) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    is_verified BOOLEAN DEFAULT FALSE,
    verification_token VARCHAR(64) NULL,
    verification_token_expires DATETIME NULL,
    password_reset_token VARCHAR(64) NULL,
    password_reset_expires DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de tarjetas
CREATE TABLE cards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    card_type ENUM('bank', 'tax', 'custom') NOT NULL,
    card_data JSON NOT NULL,
    background_color VARCHAR(7) DEFAULT '#FFFFFF',
    text_color VARCHAR(7) DEFAULT '#000000',
    format_ratio VARCHAR(10) DEFAULT '16:9',
    font_family VARCHAR(50) DEFAULT 'Arial',
    font_size INT DEFAULT 14,
    alignment ENUM('left', 'center', 'right') DEFAULT 'left',
    logo_url TEXT NULL,
    logo_url2 TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id)
);

-- Tabla para sesiones (opcional, puede usarse para control de sesiones)
CREATE TABLE user_sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INT NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabla para logs de actividad (opcional)
CREATE TABLE activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    action VARCHAR(100) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Insertar usuario de prueba (contraseña: Test1234)
INSERT INTO users (email, password_hash, is_verified) VALUES 
('test@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', TRUE);

-- Insertar tarjeta de ejemplo
INSERT INTO cards (user_id, card_type, card_data, background_color, text_color, format_ratio) VALUES
(1, 'bank', '{"bank_name": "Banco de Chile", "account_type": "Cuenta Corriente", "account_number": "123456789", "name": "Juan Pérez", "rut": "12.345.678-9", "email": "juan@example.com"}', '#1E3A8A', '#FFFFFF', '16:9'),
(1, 'tax', '{"company_name": "Empresa S.A.", "business_name": "Empresa Sociedad Anónima", "industry": "Tecnología", "address": "Av. Principal 123", "phone": "+56 9 8765 4321", "email": "contacto@empresa.cl", "rut": "76.543.210-K"}', '#047857', '#FFFFFF', '2x4'),
(1, 'custom', '{"title": "Tarjeta Personal", "fields": [{"key": "Nombre", "value": "María González"}, {"key": "Cargo", "value": "Desarrolladora"}, {"key": "Teléfono", "value": "+56 9 1234 5678"}]}', '#7C3AED', '#F9FAFB', '1:1');