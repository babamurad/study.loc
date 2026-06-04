-- --------------------------------------------------------
-- Хост:                         127.0.0.1
-- Версия сервера:               5.7.39 - MySQL Community Server (GPL)
-- Операционная система:         Win64
-- HeidiSQL Версия:              12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Дамп структуры базы данных studydb
DROP DATABASE IF EXISTS `studydb`;
CREATE DATABASE IF NOT EXISTS `studydb` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;
USE `studydb`;

-- Дамп структуры для таблица studydb.quizzes
DROP TABLE IF EXISTS `quizzes`;
CREATE TABLE IF NOT EXISTS `quizzes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `pass_threshold` tinyint(3) unsigned NOT NULL DEFAULT '70',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Дамп данных таблицы studydb.quizzes: ~1 rows (приблизительно)
DELETE FROM `quizzes`;
INSERT INTO `quizzes` (`id`, `title`, `description`, `pass_threshold`, `created_at`, `updated_at`) VALUES
	(1, 'Основы HTML5 и Семантика', '', 70, '2026-06-04 06:16:37', '2026-06-04 06:16:37');

-- Дамп структуры для таблица studydb.quiz_answers
DROP TABLE IF EXISTS `quiz_answers`;
CREATE TABLE IF NOT EXISTS `quiz_answers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `quiz_question_id` bigint(20) unsigned NOT NULL,
  `answer` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quiz_answers_quiz_question_id_foreign` (`quiz_question_id`),
  CONSTRAINT `quiz_answers_quiz_question_id_foreign` FOREIGN KEY (`quiz_question_id`) REFERENCES `quiz_questions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Дамп данных таблицы studydb.quiz_answers: ~8 rows (приблизительно)
DELETE FROM `quiz_answers`;
INSERT INTO `quiz_answers` (`id`, `quiz_question_id`, `answer`, `is_correct`, `created_at`, `updated_at`) VALUES
	(1, 1, '<header>', 0, '2026-06-04 06:17:51', '2026-06-04 06:17:51'),
	(2, 1, '<nav>', 1, '2026-06-04 06:17:51', '2026-06-04 06:17:51'),
	(3, 1, '<section>', 0, '2026-06-04 06:17:51', '2026-06-04 06:17:51'),
	(4, 1, '<menu>', 0, '2026-06-04 06:17:51', '2026-06-04 06:17:51'),
	(5, 2, '<a href="page.html"> <img src="image.png"> </a>', 1, '2026-06-04 06:24:56', '2026-06-04 06:24:56'),
	(6, 2, '<img src="image.png" href="page.html">', 0, '2026-06-04 06:24:56', '2026-06-04 06:24:56'),
	(7, 2, '<a src="page.html"> <img href="image.png"> </a>', 0, '2026-06-04 06:24:56', '2026-06-04 06:24:56'),
	(8, 2, '<link to="page.html"> <img src="image.png"> </link>', 0, '2026-06-04 06:24:56', '2026-06-04 06:24:56');

-- Дамп структуры для таблица studydb.quiz_questions
DROP TABLE IF EXISTS `quiz_questions`;
CREATE TABLE IF NOT EXISTS `quiz_questions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `quiz_id` bigint(20) unsigned NOT NULL,
  `question` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `order` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quiz_questions_quiz_id_foreign` (`quiz_id`),
  CONSTRAINT `quiz_questions_quiz_id_foreign` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Дамп данных таблицы studydb.quiz_questions: ~2 rows (приблизительно)
DELETE FROM `quiz_questions`;
INSERT INTO `quiz_questions` (`id`, `quiz_id`, `question`, `order`, `created_at`, `updated_at`) VALUES
	(1, 1, 'Какой семантический тег HTML5 лучше всего использовать для разметки главного навигационного меню сайта (списка ссылок на другие страницы)?', 0, '2026-06-04 06:17:51', '2026-06-04 06:17:51'),
	(2, 1, 'Какая HTML-структура для этого правильная?', 1, '2026-06-04 06:24:56', '2026-06-04 06:24:56');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
