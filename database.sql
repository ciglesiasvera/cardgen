-- CardGen Pro - Base de datos MySQL
-- Versión: 1.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `skylabsc_cardgen_pro`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cards`
--

CREATE TABLE `cards` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `card_type` enum('bank','tax','custom') NOT NULL,
  `card_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`card_data`)),
  `background_color` varchar(7) DEFAULT '#FFFFFF',
  `text_color` varchar(7) DEFAULT '#000000',
  `title_color` varchar(7) DEFAULT '#000000',
  `format_ratio` varchar(10) DEFAULT '16:9',
  `font_family` varchar(50) DEFAULT 'Arial',
  `font_size` int(11) DEFAULT 14,
  `alignment` enum('left','center','right') DEFAULT 'left',
  `logo_url` text DEFAULT NULL,
  `logo_url2` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `cards`
--

INSERT INTO `cards` (`id`, `user_id`, `card_type`, `card_data`, `background_color`, `text_color`, `title_color`, `format_ratio`, `font_family`, `font_size`, `alignment`, `logo_url`, `logo_url2`, `created_at`, `updated_at`) VALUES
(1, 1, 'bank', '{\"bank_name\": \"Banco de Chile\", \"account_type\": \"Cuenta Corriente\", \"account_number\": \"123456789\", \"name\": \"Juan Pérez\", \"rut\": \"12.345.678-9\", \"email\": \"juan@example.com\"}', '#1E3A8A', '#FFFFFF', '#000000', '16:9', 'Arial', 14, 'left', NULL, NULL, '2026-02-13 19:32:26', '2026-02-13 19:32:26'),
(2, 1, 'tax', '{\"company_name\": \"Empresa S.A.\", \"business_name\": \"Empresa Sociedad Anónima\", \"industry\": \"Tecnología\", \"address\": \"Av. Principal 123\", \"phone\": \"+56 9 8765 4321\", \"email\": \"contacto@empresa.cl\", \"rut\": \"76.543.210-K\"}', '#047857', '#FFFFFF', '#000000', '2x4', 'Arial', 14, 'left', NULL, NULL, '2026-02-13 19:32:26', '2026-02-13 19:32:26'),
(3, 1, 'custom', '{\"title\": \"Tarjeta Personal\", \"fields\": [{\"key\": \"Nombre\", \"value\": \"María González\"}, {\"key\": \"Cargo\", \"value\": \"Desarrolladora\"}, {\"key\": \"Teléfono\", \"value\": \"+56 9 1234 5678\"}]}', '#7C3AED', '#F9FAFB', '#000000', '1:1', 'Arial', 14, 'left', NULL, NULL, '2026-02-13 19:32:26', '2026-02-13 19:32:26'),
(5, 8, 'bank', '{\"bank_name\":\"Banco Estado\",\"account_type\":\"Cuenta Rut\",\"account_number\":\"9997374\",\"name\":\"Cristian Alejandro Iglesias Vera\",\"rut\":\"9.997.374-9\",\"email\":\"ciglesiasvera@gmail.com\"}', '#dc8118', '#ffffff', '#000000', '16:9', 'Arial', 20, 'center', '/public/uploads/logos/logo_8_6994ff1ae0fea.jpg', NULL, '2026-02-17 23:51:54', '2026-02-17 23:51:54');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `verification_token` varchar(64) DEFAULT NULL,
  `verification_token_expires` datetime DEFAULT NULL,
  `password_reset_token` varchar(64) DEFAULT NULL,
  `password_reset_expires` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `email`, `password_hash`, `is_verified`, `verification_token`, `verification_token_expires`, `password_reset_token`, `password_reset_expires`, `created_at`, `updated_at`) VALUES
(1, 'test@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NULL, NULL, NULL, NULL, '2026-02-13 19:32:10', '2026-02-13 19:32:10'),
(8, 'ciglesiasvera@gmail.com', '$2y$10$MuF8v/0K6WiitlXO2V/OwuiGQUaCWA/OrO.1i0/5Ux/ASD1G9wDZm', 1, NULL, NULL, NULL, NULL, '2026-02-17 23:49:05', '2026-02-17 23:49:30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_sessions`
--

CREATE TABLE `user_sessions` (
  `id` varchar(128) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `last_activity` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `cards`
--
ALTER TABLE `cards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`);

--
-- Indices de la tabla `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cards`
--
ALTER TABLE `cards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `cards`
--
ALTER TABLE `cards`
  ADD CONSTRAINT `cards_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD CONSTRAINT `user_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- Insertar usuario de prueba (contraseña: Test1234)
INSERT INTO users (email, password_hash, is_verified) VALUES 
('test@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', TRUE);

-- Insertar tarjeta de ejemplo
INSERT INTO cards (user_id, card_type, card_data, background_color, text_color, format_ratio) VALUES
(1, 'bank', '{"bank_name": "Banco de Chile", "account_type": "Cuenta Corriente", "account_number": "123456789", "name": "Juan Pérez", "rut": "12.345.678-9", "email": "juan@example.com"}', '#1E3A8A', '#FFFFFF', '16:9'),
(1, 'tax', '{"company_name": "Empresa S.A.", "business_name": "Empresa Sociedad Anónima", "industry": "Tecnología", "address": "Av. Principal 123", "phone": "+56 9 8765 4321", "email": "contacto@empresa.cl", "rut": "76.543.210-K"}', '#047857', '#FFFFFF', '2x4'),
(1, 'custom', '{"title": "Tarjeta Personal", "fields": [{"key": "Nombre", "value": "María González"}, {"key": "Cargo", "value": "Desarrolladora"}, {"key": "Teléfono", "value": "+56 9 1234 5678"}]}', '#7C3AED', '#F9FAFB', '1:1');