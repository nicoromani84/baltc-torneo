-- Crear tabla para inscripciones de pádel
CREATE TABLE IF NOT EXISTS `padel_reservations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `partner_id` int(11) NOT NULL,
  `gender` char(1) NOT NULL COMMENT 'M o F',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  FOREIGN KEY (`user_id`) REFERENCES `players`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`partner_id`) REFERENCES `players`(`id`) ON DELETE CASCADE,
  INDEX `gender` (`gender`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
