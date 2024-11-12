-- Adminer 4.8.1 MySQL 8.0.39-0ubuntu0.22.04.1 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `file`;
CREATE TABLE `file` (
  `id` int NOT NULL AUTO_INCREMENT,
  `folder_id` int DEFAULT NULL,
  `user_id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `mime_type` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `file_size` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `folder_id` (`folder_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `file_ibfk_1` FOREIGN KEY (`folder_id`) REFERENCES `folder` (`id`) ON DELETE SET NULL,
  CONSTRAINT `file_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `file` (`id`, `folder_id`, `user_id`, `name`, `file_path`, `mime_type`, `file_size`, `created_at`) VALUES
(1,	5,	1,	'Screenshot from 2024-11-08 16-15-21.png',	'../uploads/Screenshot from 2024-11-08 16-15-21.png',	'image/png',	84935,	'2024-11-08 09:55:33'),
(2,	3,	1,	'Screenshot from 2024-11-11 15-35-26.png',	'../uploads/Screenshot from 2024-11-11 15-35-26.png',	'image/png',	24875,	'2024-11-12 04:15:18'),
(3,	1,	1,	'data_laporan.xlsx',	'../uploads/data_laporan.xlsx',	'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',	985719,	'2024-11-12 04:37:27'),
(5,	NULL,	1,	'Screenshot from 2024-11-11 15-35-26.png',	'../uploads/Screenshot from 2024-11-11 15-35-26.png',	'image/png',	24875,	'2024-11-12 04:55:08'),
(6,	1,	1,	'Makalah Kelompok 8.pdf',	'../uploads/Makalah Kelompok 8.pdf',	'application/pdf',	3292110,	'2024-11-12 08:11:48');

DROP TABLE IF EXISTS `folder`;
CREATE TABLE `folder` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `parent_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `folder_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  CONSTRAINT `folder_ibfk_2` FOREIGN KEY (`parent_id`) REFERENCES `folder` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `folder` (`id`, `user_id`, `name`, `parent_id`, `created_at`) VALUES
(1,	1,	'Hello',	NULL,	'2024-11-06 09:52:58'),
(2,	1,	'Test',	NULL,	'2024-11-06 09:57:30'),
(3,	1,	'Test',	NULL,	'2024-11-06 09:57:35'),
(4,	1,	'Test',	NULL,	'2024-11-06 09:57:37'),
(5,	1,	'hai',	NULL,	'2024-11-06 10:00:34'),
(6,	1,	'hai',	NULL,	'2024-11-06 10:01:50'),
(7,	1,	'hai',	NULL,	'2024-11-06 10:02:45'),
(8,	1,	'Sub Folder',	5,	'2024-11-12 04:33:38');

DROP TABLE IF EXISTS `share`;
CREATE TABLE `share` (
  `id` int NOT NULL AUTO_INCREMENT,
  `file_id` int DEFAULT NULL,
  `folder_id` int DEFAULT NULL,
  `user_id` int NOT NULL,
  `access_type` enum('view','edit') NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `file_id` (`file_id`),
  KEY `folder_id` (`folder_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `share_ibfk_1` FOREIGN KEY (`file_id`) REFERENCES `file` (`id`) ON DELETE CASCADE,
  CONSTRAINT `share_ibfk_2` FOREIGN KEY (`folder_id`) REFERENCES `folder` (`id`) ON DELETE CASCADE,
  CONSTRAINT `share_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `user` (`id`, `username`, `email`, `password`, `created_at`) VALUES
(1,	'ryzen500',	'ryzen500@gmail.com',	'$2y$10$dU7CCEWM8c9kHcDBU8y8.u9sMY2FixksVsYXu1kDMf64zrya3px5u',	'2024-11-06 09:32:36');

-- 2024-11-12 09:30:52
