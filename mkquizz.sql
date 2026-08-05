-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 05, 2026 at 06:34 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mkquizz`
--

-- --------------------------------------------------------

--
-- Table structure for table `attempt_questions`
--

CREATE TABLE `attempt_questions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `attempt_id` bigint(20) UNSIGNED NOT NULL,
  `question_id` bigint(20) UNSIGNED NOT NULL,
  `question_order` int(10) UNSIGNED NOT NULL,
  `score` decimal(8,2) NOT NULL DEFAULT 1.00,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attempt_questions`
--

INSERT INTO `attempt_questions` (`id`, `attempt_id`, `question_id`, `question_order`, `score`, `created_at`) VALUES
(1, 1, 1, 1, 10.00, '2026-08-05 03:36:09'),
(2, 1, 2, 2, 10.00, '2026-08-05 03:36:09'),
(3, 1, 3, 3, 10.00, '2026-08-05 03:36:09'),
(4, 1, 4, 4, 10.00, '2026-08-05 03:36:09'),
(5, 1, 5, 5, 10.00, '2026-08-05 03:36:09'),
(6, 1, 6, 6, 10.00, '2026-08-05 03:36:09'),
(7, 1, 7, 7, 10.00, '2026-08-05 03:36:09'),
(8, 1, 8, 8, 10.00, '2026-08-05 03:36:09'),
(9, 1, 9, 9, 10.00, '2026-08-05 03:36:09'),
(10, 1, 10, 10, 10.00, '2026-08-05 03:36:09');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `entity_type` varchar(100) DEFAULT NULL,
  `entity_id` bigint(20) UNSIGNED DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `materials`
--

CREATE TABLE `materials` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(30) DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `materials`
--

INSERT INTO `materials` (`id`, `code`, `title`, `description`, `created_by`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'MAT001', 'Awareness ISO 27001', 'Materi dasar awareness keamanan informasi dan ISO/IEC 27001.', 2, 1, '2026-08-05 03:17:28', '2026-08-05 03:17:28'),
(2, 'MAT002', 'Product Knowledge', 'Materi pengetahuan produk untuk karyawan.', 3, 1, '2026-08-05 03:17:28', '2026-08-05 03:17:28');

-- --------------------------------------------------------

--
-- Table structure for table `participants`
--

CREATE TABLE `participants` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `session_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `participant_token` varchar(100) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `joined_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `participants`
--

INSERT INTO `participants` (`id`, `session_id`, `name`, `participant_token`, `ip_address`, `user_agent`, `joined_at`, `created_at`) VALUES
(1, 1, 'Andi Saputra', 'PART-ANDI-001', '192.168.1.25', 'Mozilla/5.0 Chrome', '2026-08-05 09:03:00', '2026-08-05 03:26:38');

-- --------------------------------------------------------

--
-- Table structure for table `participant_answers`
--

CREATE TABLE `participant_answers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `attempt_id` bigint(20) UNSIGNED NOT NULL,
  `question_id` bigint(20) UNSIGNED NOT NULL,
  `selected_option_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_correct` tinyint(1) DEFAULT NULL,
  `score_received` decimal(8,2) NOT NULL DEFAULT 0.00,
  `answered_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `participant_answers`
--

INSERT INTO `participant_answers` (`id`, `attempt_id`, `question_id`, `selected_option_id`, `is_correct`, `score_received`, `answered_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 2, 1, 10.00, '2026-08-05 09:03:40', '2026-08-05 03:36:09', '2026-08-05 03:36:09'),
(2, 1, 2, 5, 1, 10.00, '2026-08-05 09:04:10', '2026-08-05 03:36:09', '2026-08-05 03:36:09'),
(3, 1, 3, 9, 0, 0.00, '2026-08-05 09:04:40', '2026-08-05 03:36:09', '2026-08-05 03:36:09'),
(4, 1, 4, 14, 1, 10.00, '2026-08-05 09:05:10', '2026-08-05 03:36:09', '2026-08-05 03:36:09'),
(5, 1, 5, 20, 1, 10.00, '2026-08-05 09:05:40', '2026-08-05 03:36:09', '2026-08-05 03:36:09'),
(6, 1, 6, 22, 0, 0.00, '2026-08-05 09:06:10', '2026-08-05 03:36:09', '2026-08-05 03:36:09'),
(7, 1, 7, 28, 1, 10.00, '2026-08-05 09:06:40', '2026-08-05 03:36:09', '2026-08-05 03:36:09'),
(8, 1, 8, 30, 1, 10.00, '2026-08-05 09:07:10', '2026-08-05 03:36:09', '2026-08-05 03:36:09'),
(9, 1, 9, 36, 1, 10.00, '2026-08-05 09:07:40', '2026-08-05 03:36:09', '2026-08-05 03:36:09'),
(10, 1, 10, 37, 1, 10.00, '2026-08-05 09:08:10', '2026-08-05 03:36:09', '2026-08-05 03:36:09');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `material_id` bigint(20) UNSIGNED NOT NULL,
  `question_type` enum('MULTIPLE_CHOICE','TRUE_FALSE') NOT NULL DEFAULT 'MULTIPLE_CHOICE',
  `question_text` text NOT NULL,
  `explanation` text DEFAULT NULL,
  `default_score` decimal(8,2) NOT NULL DEFAULT 1.00,
  `difficulty` enum('EASY','MEDIUM','HARD') DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `material_id`, `question_type`, `question_text`, `explanation`, `default_score`, `difficulty`, `created_by`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'MULTIPLE_CHOICE', 'Apa kepanjangan CIA dalam keamanan informasi?', 'CIA adalah Confidentiality, Integrity, dan Availability.', 10.00, 'EASY', 2, 1, '2026-08-05 03:17:28', '2026-08-05 03:17:28'),
(2, 1, 'MULTIPLE_CHOICE', 'Apa yang dimaksud dengan Confidentiality?', 'Confidentiality berarti informasi hanya dapat diakses oleh pihak yang berwenang.', 10.00, 'EASY', 2, 1, '2026-08-05 03:17:28', '2026-08-05 03:17:28'),
(3, 1, 'MULTIPLE_CHOICE', 'Apa yang dimaksud dengan Integrity?', 'Integrity memastikan informasi tetap akurat, lengkap, dan tidak berubah tanpa izin.', 10.00, 'EASY', 2, 1, '2026-08-05 03:17:28', '2026-08-05 03:17:28'),
(4, 1, 'MULTIPLE_CHOICE', 'Apa yang dimaksud dengan Availability?', 'Availability memastikan informasi dan sistem tersedia ketika dibutuhkan.', 10.00, 'EASY', 2, 1, '2026-08-05 03:17:28', '2026-08-05 03:17:28'),
(5, 1, 'MULTIPLE_CHOICE', 'ISO/IEC 27001:2022 berkaitan dengan sistem manajemen apa?', 'ISO/IEC 27001:2022 merupakan standar Sistem Manajemen Keamanan Informasi.', 10.00, 'MEDIUM', 2, 1, '2026-08-05 03:17:28', '2026-08-05 03:17:28'),
(6, 1, 'MULTIPLE_CHOICE', 'Kontrol keamanan fisik pada ISO/IEC 27001:2022 berada pada kelompok Annex berapa?', 'Annex 7 berisi Physical Controls.', 10.00, 'MEDIUM', 2, 1, '2026-08-05 03:17:28', '2026-08-05 03:17:28'),
(7, 1, 'MULTIPLE_CHOICE', 'Apa tindakan yang tepat jika menerima email mencurigakan?', 'Email mencurigakan sebaiknya tidak dibuka atau diklik dan segera dilaporkan sesuai prosedur keamanan.', 10.00, 'MEDIUM', 2, 1, '2026-08-05 03:17:28', '2026-08-05 03:17:28'),
(8, 1, 'MULTIPLE_CHOICE', 'Password yang baik seharusnya seperti apa?', 'Password sebaiknya panjang, unik, sulit ditebak, dan tidak digunakan untuk banyak akun.', 10.00, 'EASY', 2, 1, '2026-08-05 03:17:28', '2026-08-05 03:17:28'),
(9, 1, 'MULTIPLE_CHOICE', 'Siapa yang bertanggung jawab terhadap keamanan informasi perusahaan?', 'Keamanan informasi merupakan tanggung jawab seluruh pihak sesuai peran dan kewenangannya.', 10.00, 'MEDIUM', 2, 1, '2026-08-05 03:17:28', '2026-08-05 03:17:28'),
(10, 1, 'MULTIPLE_CHOICE', 'Apa tujuan utama melakukan backup data?', 'Backup dilakukan agar data dapat dipulihkan apabila terjadi kehilangan atau kerusakan.', 10.00, 'EASY', 2, 1, '2026-08-05 03:17:28', '2026-08-05 03:17:28');

-- --------------------------------------------------------

--
-- Table structure for table `question_options`
--

CREATE TABLE `question_options` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `question_id` bigint(20) UNSIGNED NOT NULL,
  `option_key` varchar(10) DEFAULT NULL,
  `option_text` text NOT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `question_options`
--

INSERT INTO `question_options` (`id`, `question_id`, `option_key`, `option_text`, `is_correct`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 1, 'A', 'Control, Information, Access', 0, 1, '2026-08-05 03:17:28', '2026-08-05 03:17:28'),
(2, 1, 'B', 'Confidentiality, Integrity, Availability', 1, 2, '2026-08-05 03:17:28', '2026-08-05 03:17:28'),
(3, 1, 'C', 'Confidentiality, Internet, Authentication', 0, 3, '2026-08-05 03:17:28', '2026-08-05 03:17:28'),
(4, 1, 'D', 'Control, Integrity, Authentication', 0, 4, '2026-08-05 03:17:28', '2026-08-05 03:17:28'),
(5, 2, 'A', 'Informasi hanya dapat diakses oleh pihak yang berwenang', 1, 1, '2026-08-05 03:17:28', '2026-08-05 03:17:28'),
(6, 2, 'B', 'Informasi selalu tersedia setiap saat', 0, 2, '2026-08-05 03:17:28', '2026-08-05 03:17:28'),
(7, 2, 'C', 'Informasi tidak boleh dilakukan backup', 0, 3, '2026-08-05 03:17:28', '2026-08-05 03:17:28'),
(8, 2, 'D', 'Informasi boleh diberikan kepada siapa saja', 0, 4, '2026-08-05 03:17:28', '2026-08-05 03:17:28'),
(9, 3, 'A', 'Informasi dapat diakses seluruh karyawan', 0, 1, '2026-08-05 03:17:28', '2026-08-05 03:17:28'),
(10, 3, 'B', 'Informasi selalu tersedia', 0, 2, '2026-08-05 03:17:28', '2026-08-05 03:17:28'),
(11, 3, 'C', 'Informasi tetap akurat dan tidak berubah tanpa izin', 1, 3, '2026-08-05 03:17:28', '2026-08-05 03:17:28'),
(12, 3, 'D', 'Informasi wajib dicetak', 0, 4, '2026-08-05 03:17:28', '2026-08-05 03:17:28'),
(13, 4, 'A', 'Informasi hanya boleh dibaca manager', 0, 1, '2026-08-05 03:17:28', '2026-08-05 03:17:28'),
(14, 4, 'B', 'Informasi dan sistem dapat digunakan ketika dibutuhkan', 1, 2, '2026-08-05 03:17:28', '2026-08-05 03:17:28'),
(15, 4, 'C', 'Informasi tidak boleh disimpan secara digital', 0, 3, '2026-08-05 03:17:28', '2026-08-05 03:17:28'),
(16, 4, 'D', 'Informasi harus selalu dipublikasikan', 0, 4, '2026-08-05 03:17:28', '2026-08-05 03:17:28'),
(17, 5, 'A', 'Sistem Manajemen Keuangan', 0, 1, '2026-08-05 03:17:28', '2026-08-05 03:17:28'),
(18, 5, 'B', 'Sistem Manajemen Sumber Daya Manusia', 0, 2, '2026-08-05 03:17:28', '2026-08-05 03:17:28'),
(19, 5, 'C', 'Sistem Manajemen Operasional Retail', 0, 3, '2026-08-05 03:17:28', '2026-08-05 03:17:28'),
(20, 5, 'D', 'Sistem Manajemen Keamanan Informasi', 1, 4, '2026-08-05 03:17:28', '2026-08-05 03:17:28'),
(21, 6, 'A', 'Annex 5', 0, 1, '2026-08-05 03:17:28', '2026-08-05 03:17:28'),
(22, 6, 'B', 'Annex 6', 0, 2, '2026-08-05 03:17:28', '2026-08-05 03:17:28'),
(23, 6, 'C', 'Annex 7', 1, 3, '2026-08-05 03:17:28', '2026-08-05 03:17:28'),
(24, 6, 'D', 'Annex 8', 0, 4, '2026-08-05 03:17:28', '2026-08-05 03:17:28'),
(25, 7, 'A', 'Membuka semua attachment untuk memastikan isinya', 0, 1, '2026-08-05 03:17:28', '2026-08-05 03:17:28'),
(26, 7, 'B', 'Membalas email untuk menanyakan identitas pengirim', 0, 2, '2026-08-05 03:17:28', '2026-08-05 03:17:28'),
(27, 7, 'C', 'Meneruskan email kepada teman kerja', 0, 3, '2026-08-05 03:17:28', '2026-08-05 03:17:28'),
(28, 7, 'D', 'Tidak mengklik link dan melaporkannya sesuai prosedur', 1, 4, '2026-08-05 03:17:28', '2026-08-05 03:17:28'),
(29, 8, 'A', '12345678', 0, 1, '2026-08-05 03:17:28', '2026-08-05 03:17:28'),
(30, 8, 'B', 'Panjang, unik, dan sulit ditebak', 1, 2, '2026-08-05 03:17:28', '2026-08-05 03:17:28'),
(31, 8, 'C', 'Menggunakan nama sendiri', 0, 3, '2026-08-05 03:17:28', '2026-08-05 03:17:28'),
(32, 8, 'D', 'Menggunakan password yang sama untuk semua aplikasi', 0, 4, '2026-08-05 03:17:28', '2026-08-05 03:17:28'),
(33, 9, 'A', 'Hanya bagian IT', 0, 1, '2026-08-05 03:17:28', '2026-08-05 03:17:28'),
(34, 9, 'B', 'Hanya Direktur', 0, 2, '2026-08-05 03:17:28', '2026-08-05 03:17:28'),
(35, 9, 'C', 'Hanya HRD', 0, 3, '2026-08-05 03:17:28', '2026-08-05 03:17:28'),
(36, 9, 'D', 'Seluruh pihak sesuai peran dan kewenangannya', 1, 4, '2026-08-05 03:17:28', '2026-08-05 03:17:28'),
(37, 10, 'A', 'Agar data dapat dipulihkan ketika terjadi kehilangan atau kerusakan', 1, 1, '2026-08-05 03:17:28', '2026-08-05 03:17:28'),
(38, 10, 'B', 'Agar kapasitas penyimpanan cepat penuh', 0, 2, '2026-08-05 03:17:28', '2026-08-05 03:17:28'),
(39, 10, 'C', 'Agar semua data dapat dilihat publik', 0, 3, '2026-08-05 03:17:28', '2026-08-05 03:17:28'),
(40, 10, 'D', 'Agar data lama otomatis terhapus', 0, 4, '2026-08-05 03:17:28', '2026-08-05 03:17:28');

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `material_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `duration_minutes` int(10) UNSIGNED NOT NULL DEFAULT 15,
  `passing_score` decimal(5,2) NOT NULL DEFAULT 75.00,
  `shuffle_questions` tinyint(1) NOT NULL DEFAULT 0,
  `shuffle_options` tinyint(1) NOT NULL DEFAULT 0,
  `show_score` tinyint(1) NOT NULL DEFAULT 1,
  `show_correct_answer` tinyint(1) NOT NULL DEFAULT 1,
  `show_explanation` tinyint(1) NOT NULL DEFAULT 1,
  `allow_review` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `status` enum('DRAFT','ACTIVE','INACTIVE') NOT NULL DEFAULT 'DRAFT',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quizzes`
--

INSERT INTO `quizzes` (`id`, `material_id`, `title`, `description`, `duration_minutes`, `passing_score`, `shuffle_questions`, `shuffle_options`, `show_score`, `show_correct_answer`, `show_explanation`, `allow_review`, `created_by`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Post Test Awareness ISO 27001', 'Quiz setelah peserta mengikuti materi Awareness ISO 27001.', 10, 70.00, 1, 0, 1, 1, 1, 1, 2, 'ACTIVE', '2026-08-05 03:17:28', '2026-08-05 03:17:28');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_attempts`
--

CREATE TABLE `quiz_attempts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `participant_id` bigint(20) UNSIGNED NOT NULL,
  `session_id` bigint(20) UNSIGNED NOT NULL,
  `quiz_id` bigint(20) UNSIGNED NOT NULL,
  `started_at` datetime NOT NULL,
  `expires_at` datetime NOT NULL,
  `submitted_at` datetime DEFAULT NULL,
  `total_questions` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `total_answered` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `total_correct` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `total_wrong` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `total_score` decimal(10,2) NOT NULL DEFAULT 0.00,
  `max_score` decimal(10,2) NOT NULL DEFAULT 0.00,
  `final_score` decimal(5,2) NOT NULL DEFAULT 0.00,
  `passed` tinyint(1) DEFAULT NULL,
  `status` enum('IN_PROGRESS','SUBMITTED','EXPIRED') NOT NULL DEFAULT 'IN_PROGRESS',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz_attempts`
--

INSERT INTO `quiz_attempts` (`id`, `participant_id`, `session_id`, `quiz_id`, `started_at`, `expires_at`, `submitted_at`, `total_questions`, `total_answered`, `total_correct`, `total_wrong`, `total_score`, `max_score`, `final_score`, `passed`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, '2026-08-05 09:03:10', '2026-08-05 09:13:10', '2026-08-05 09:10:30', 10, 10, 8, 2, 80.00, 100.00, 80.00, 1, 'SUBMITTED', '2026-08-05 03:26:39', '2026-08-05 03:26:39'),
(2, 1, 1, 1, '2026-08-05 09:03:10', '2026-08-05 09:13:10', '2026-08-05 09:10:30', 10, 10, 8, 2, 80.00, 100.00, 80.00, 1, 'SUBMITTED', '2026-08-05 03:36:09', '2026-08-05 03:36:09');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_questions`
--

CREATE TABLE `quiz_questions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `quiz_id` bigint(20) UNSIGNED NOT NULL,
  `question_id` bigint(20) UNSIGNED NOT NULL,
  `score` decimal(8,2) NOT NULL DEFAULT 1.00,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz_questions`
--

INSERT INTO `quiz_questions` (`id`, `quiz_id`, `question_id`, `score`, `sort_order`, `created_at`) VALUES
(1, 1, 1, 10.00, 1, '2026-08-05 03:17:28'),
(2, 1, 2, 10.00, 2, '2026-08-05 03:17:28'),
(3, 1, 3, 10.00, 3, '2026-08-05 03:17:28'),
(4, 1, 4, 10.00, 4, '2026-08-05 03:17:28'),
(5, 1, 5, 10.00, 5, '2026-08-05 03:17:28'),
(6, 1, 6, 10.00, 6, '2026-08-05 03:17:28'),
(7, 1, 7, 10.00, 7, '2026-08-05 03:17:28'),
(8, 1, 8, 10.00, 8, '2026-08-05 03:17:28'),
(9, 1, 9, 10.00, 9, '2026-08-05 03:17:28'),
(10, 1, 10, 10.00, 10, '2026-08-05 03:17:28');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_sessions`
--

CREATE TABLE `quiz_sessions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `quiz_id` bigint(20) UNSIGNED NOT NULL,
  `session_name` varchar(200) NOT NULL,
  `session_token` varchar(100) NOT NULL,
  `pin` varchar(10) NOT NULL,
  `pin_valid_minutes` int(10) UNSIGNED NOT NULL DEFAULT 5,
  `pin_valid_from` datetime NOT NULL,
  `pin_valid_until` datetime NOT NULL,
  `max_participants` int(10) UNSIGNED DEFAULT NULL,
  `allow_duplicate_name` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('DRAFT','WAITING','OPEN','CLOSED') NOT NULL DEFAULT 'WAITING',
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `opened_at` datetime DEFAULT NULL,
  `closed_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz_sessions`
--

INSERT INTO `quiz_sessions` (`id`, `quiz_id`, `session_name`, `session_token`, `pin`, `pin_valid_minutes`, `pin_valid_from`, `pin_valid_until`, `max_participants`, `allow_duplicate_name`, `status`, `created_by`, `opened_at`, `closed_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'Training ISO 27001 Batch 1', 'ISO2026BATCH01', '583921', 5, '2026-08-05 09:00:00', '2026-08-05 09:05:00', 100, 0, 'OPEN', 2, '2026-08-05 09:00:00', NULL, '2026-08-05 03:17:28', '2026-08-05 03:17:28');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('SUPERADMIN','ADMIN','PRESENTER') NOT NULL DEFAULT 'PRESENTER',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'superadmin@quiz.local', '$2y$10$EXAMPLE_HASH_SUPERADMIN', 'SUPERADMIN', 1, '2026-08-05 03:17:28', '2026-08-05 03:17:28'),
(2, 'Budi Santoso', 'budi@quiz.local', '$2y$10$EXAMPLE_HASH_PRESENTER_1', 'PRESENTER', 1, '2026-08-05 03:17:28', '2026-08-05 03:17:28'),
(3, 'Siti Rahma', 'siti@quiz.local', '$2y$10$EXAMPLE_HASH_PRESENTER_2', 'PRESENTER', 1, '2026-08-05 03:17:28', '2026-08-05 03:17:28');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attempt_questions`
--
ALTER TABLE `attempt_questions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_attempt_question` (`attempt_id`,`question_id`),
  ADD KEY `fk_attempt_question_question` (`question_id`),
  ADD KEY `idx_attempt_question_order` (`attempt_id`,`question_order`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_audit_entity` (`entity_type`,`entity_id`),
  ADD KEY `idx_audit_user` (`user_id`);

--
-- Indexes for table `materials`
--
ALTER TABLE `materials`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `fk_material_created_by` (`created_by`);

--
-- Indexes for table `participants`
--
ALTER TABLE `participants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `participant_token` (`participant_token`),
  ADD KEY `idx_participant_session` (`session_id`);

--
-- Indexes for table `participant_answers`
--
ALTER TABLE `participant_answers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_attempt_question` (`attempt_id`,`question_id`),
  ADD KEY `idx_answer_question` (`question_id`),
  ADD KEY `idx_answer_option` (`selected_option_id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_question_material` (`material_id`),
  ADD KEY `fk_question_creator` (`created_by`);

--
-- Indexes for table `question_options`
--
ALTER TABLE `question_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_question_option` (`question_id`);

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_quiz_material` (`material_id`),
  ADD KEY `fk_quiz_creator` (`created_by`);

--
-- Indexes for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_attempt_participant` (`participant_id`),
  ADD KEY `idx_attempt_session` (`session_id`),
  ADD KEY `idx_attempt_quiz` (`quiz_id`),
  ADD KEY `idx_attempt_status` (`status`);

--
-- Indexes for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_quiz_question` (`quiz_id`,`question_id`),
  ADD KEY `fk_qq_question` (`question_id`),
  ADD KEY `idx_qq_quiz` (`quiz_id`);

--
-- Indexes for table `quiz_sessions`
--
ALTER TABLE `quiz_sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `session_token` (`session_token`),
  ADD KEY `fk_session_quiz` (`quiz_id`),
  ADD KEY `fk_session_creator` (`created_by`),
  ADD KEY `idx_session_token` (`session_token`),
  ADD KEY `idx_session_pin` (`pin`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attempt_questions`
--
ALTER TABLE `attempt_questions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `materials`
--
ALTER TABLE `materials`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `participants`
--
ALTER TABLE `participants`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `participant_answers`
--
ALTER TABLE `participant_answers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `question_options`
--
ALTER TABLE `question_options`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `quiz_sessions`
--
ALTER TABLE `quiz_sessions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attempt_questions`
--
ALTER TABLE `attempt_questions`
  ADD CONSTRAINT `fk_attempt_question_attempt` FOREIGN KEY (`attempt_id`) REFERENCES `quiz_attempts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_attempt_question_question` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`);

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `materials`
--
ALTER TABLE `materials`
  ADD CONSTRAINT `fk_material_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `participants`
--
ALTER TABLE `participants`
  ADD CONSTRAINT `fk_participant_session` FOREIGN KEY (`session_id`) REFERENCES `quiz_sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `participant_answers`
--
ALTER TABLE `participant_answers`
  ADD CONSTRAINT `fk_answer_attempt` FOREIGN KEY (`attempt_id`) REFERENCES `quiz_attempts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_answer_option` FOREIGN KEY (`selected_option_id`) REFERENCES `question_options` (`id`),
  ADD CONSTRAINT `fk_answer_question` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`);

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `fk_question_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_question_material` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`);

--
-- Constraints for table `question_options`
--
ALTER TABLE `question_options`
  ADD CONSTRAINT `fk_option_question` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD CONSTRAINT `fk_quiz_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_quiz_material` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`);

--
-- Constraints for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD CONSTRAINT `fk_attempt_participant` FOREIGN KEY (`participant_id`) REFERENCES `participants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_attempt_quiz` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`),
  ADD CONSTRAINT `fk_attempt_session` FOREIGN KEY (`session_id`) REFERENCES `quiz_sessions` (`id`);

--
-- Constraints for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  ADD CONSTRAINT `fk_qq_question` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`),
  ADD CONSTRAINT `fk_qq_quiz` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_sessions`
--
ALTER TABLE `quiz_sessions`
  ADD CONSTRAINT `fk_session_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_session_quiz` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
