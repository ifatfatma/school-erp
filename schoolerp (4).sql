-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 30, 2026 at 06:41 PM
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
-- Database: `schoolerp`
--

-- --------------------------------------------------------

--
-- Table structure for table `academic_sessions`
--

CREATE TABLE `academic_sessions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_current` tinyint(1) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `academic_sessions`
--

INSERT INTO `academic_sessions` (`id`, `school_id`, `name`, `start_date`, `end_date`, `is_current`, `status`, `description`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, '2026-2027', '2026-07-30', '2027-06-29', 0, 1, NULL, '2026-07-28 23:43:35', '2026-07-28 23:45:25', '2026-07-28 23:45:25'),
(2, 1, '2026-27', '2026-07-29', '2026-08-09', 1, 1, NULL, '2026-07-28 23:45:50', '2026-07-28 23:45:50', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `log_name` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `subject_type` varchar(255) DEFAULT NULL,
  `event` varchar(255) DEFAULT NULL,
  `subject_id` bigint(20) UNSIGNED DEFAULT NULL,
  `causer_type` varchar(255) DEFAULT NULL,
  `causer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`properties`)),
  `batch_uuid` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `log_name`, `description`, `subject_type`, `event`, `subject_id`, `causer_type`, `causer_id`, `properties`, `batch_uuid`, `created_at`, `updated_at`) VALUES
(1, 'default', 'updated', 'App\\Models\\School', 'updated', 1, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"Demo School test\",\"updated_at\":\"2026-07-28T15:29:12.000000Z\"},\"old\":{\"name\":\"Demo School\",\"updated_at\":\"2026-07-28T11:39:31.000000Z\"}}', NULL, '2026-07-28 09:59:12', '2026-07-28 09:59:12'),
(2, 'default', 'updated', 'App\\Models\\School', 'updated', 1, 'App\\Models\\User', 1, '{\"attributes\":{\"status\":false,\"updated_at\":\"2026-07-28T15:33:44.000000Z\"},\"old\":{\"status\":true,\"updated_at\":\"2026-07-28T15:29:12.000000Z\"}}', NULL, '2026-07-28 10:03:44', '2026-07-28 10:03:44'),
(3, 'default', 'updated', 'App\\Models\\School', 'updated', 1, 'App\\Models\\User', 1, '{\"attributes\":{\"status\":true,\"updated_at\":\"2026-07-28T15:33:51.000000Z\"},\"old\":{\"status\":false,\"updated_at\":\"2026-07-28T15:33:44.000000Z\"}}', NULL, '2026-07-28 10:03:51', '2026-07-28 10:03:51'),
(4, 'default', 'created', 'App\\Models\\AcademicSession', 'created', 1, 'App\\Models\\User', 1, '{\"attributes\":{\"id\":1,\"school_id\":1,\"name\":\"26-27\",\"start_date\":\"2026-07-30T00:00:00.000000Z\",\"end_date\":\"2027-06-29T00:00:00.000000Z\",\"is_current\":true,\"status\":true,\"description\":null,\"created_at\":\"2026-07-29T05:13:35.000000Z\",\"updated_at\":\"2026-07-29T05:13:35.000000Z\",\"deleted_at\":null}}', NULL, '2026-07-28 23:43:35', '2026-07-28 23:43:35'),
(5, 'default', 'updated', 'App\\Models\\AcademicSession', 'updated', 1, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"2026-2027\",\"is_current\":false,\"updated_at\":\"2026-07-29T05:15:12.000000Z\"},\"old\":{\"name\":\"26-27\",\"is_current\":true,\"updated_at\":\"2026-07-29T05:13:35.000000Z\"}}', NULL, '2026-07-28 23:45:12', '2026-07-28 23:45:12'),
(6, 'default', 'deleted', 'App\\Models\\AcademicSession', 'deleted', 1, 'App\\Models\\User', 1, '{\"old\":{\"id\":1,\"school_id\":1,\"name\":\"2026-2027\",\"start_date\":\"2026-07-30T00:00:00.000000Z\",\"end_date\":\"2027-06-29T00:00:00.000000Z\",\"is_current\":false,\"status\":true,\"description\":null,\"created_at\":\"2026-07-29T05:13:35.000000Z\",\"updated_at\":\"2026-07-29T05:15:25.000000Z\",\"deleted_at\":\"2026-07-29T05:15:25.000000Z\"}}', NULL, '2026-07-28 23:45:25', '2026-07-28 23:45:25'),
(7, 'default', 'created', 'App\\Models\\AcademicSession', 'created', 2, 'App\\Models\\User', 1, '{\"attributes\":{\"id\":2,\"school_id\":1,\"name\":\"2026-27\",\"start_date\":\"2026-07-29T00:00:00.000000Z\",\"end_date\":\"2026-08-09T00:00:00.000000Z\",\"is_current\":true,\"status\":true,\"description\":null,\"created_at\":\"2026-07-29T05:15:50.000000Z\",\"updated_at\":\"2026-07-29T05:15:50.000000Z\",\"deleted_at\":null}}', NULL, '2026-07-28 23:45:50', '2026-07-28 23:45:50'),
(8, 'default', 'created', 'App\\Models\\Subject', 'created', 1, 'App\\Models\\User', 1, '{\"attributes\":{\"id\":1,\"name\":\"English\",\"code\":\"ss\",\"type\":\"Theory\",\"description\":\"ss\",\"status\":true,\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-07-29T15:29:53.000000Z\",\"updated_at\":\"2026-07-29T15:29:53.000000Z\",\"deleted_at\":null}}', NULL, '2026-07-29 09:59:54', '2026-07-29 09:59:54'),
(9, 'default', 'updated', 'App\\Models\\Subject', 'updated', 1, 'App\\Models\\User', 1, '{\"attributes\":{\"description\":\"ssssss\",\"updated_by\":1,\"updated_at\":\"2026-07-29T16:00:18.000000Z\"},\"old\":{\"description\":\"ss\",\"updated_by\":null,\"updated_at\":\"2026-07-29T15:29:53.000000Z\"}}', NULL, '2026-07-29 10:30:18', '2026-07-29 10:30:18'),
(10, 'default', 'updated', 'App\\Models\\Subject', 'updated', 1, 'App\\Models\\User', 1, '{\"attributes\":{\"type\":\"Practical\",\"updated_at\":\"2026-07-29T16:00:35.000000Z\"},\"old\":{\"type\":\"Theory\",\"updated_at\":\"2026-07-29T16:00:18.000000Z\"}}', NULL, '2026-07-29 10:30:35', '2026-07-29 10:30:35'),
(11, 'default', 'created', 'App\\Models\\Subject', 'created', 2, 'App\\Models\\User', 1, '{\"attributes\":{\"id\":2,\"name\":\"Math\",\"code\":\"m\",\"type\":\"Theory\",\"description\":\"mmm\",\"status\":true,\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-07-29T16:01:01.000000Z\",\"updated_at\":\"2026-07-29T16:01:01.000000Z\",\"deleted_at\":null}}', NULL, '2026-07-29 10:31:01', '2026-07-29 10:31:01'),
(12, 'default', 'created', 'App\\Models\\Section', 'created', 1, 'App\\Models\\User', 1, '{\"attributes\":{\"id\":1,\"school_id\":1,\"class_id\":2,\"name\":\"A\",\"capacity\":10,\"status\":true,\"created_at\":\"2026-07-30T10:39:20.000000Z\",\"updated_at\":\"2026-07-30T10:39:20.000000Z\",\"deleted_at\":null}}', NULL, '2026-07-30 05:09:20', '2026-07-30 05:09:20'),
(13, 'default', 'updated', 'App\\Models\\Section', 'updated', 1, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"B\",\"updated_at\":\"2026-07-30T10:39:43.000000Z\"},\"old\":{\"name\":\"A\",\"updated_at\":\"2026-07-30T10:39:20.000000Z\"}}', NULL, '2026-07-30 05:09:43', '2026-07-30 05:09:43'),
(14, 'default', 'created', 'App\\Models\\Section', 'created', 2, 'App\\Models\\User', 1, '{\"attributes\":{\"id\":2,\"school_id\":1,\"class_id\":3,\"name\":\"22\",\"capacity\":22,\"status\":true,\"created_at\":\"2026-07-30T15:27:32.000000Z\",\"updated_at\":\"2026-07-30T15:27:32.000000Z\",\"deleted_at\":null}}', NULL, '2026-07-30 09:57:32', '2026-07-30 09:57:32'),
(15, 'default', 'updated', 'App\\Models\\Section', 'updated', 2, 'App\\Models\\User', 1, '{\"attributes\":{\"name\":\"D\",\"updated_at\":\"2026-07-30T15:27:44.000000Z\"},\"old\":{\"name\":\"22\",\"updated_at\":\"2026-07-30T15:27:32.000000Z\"}}', NULL, '2026-07-30 09:57:44', '2026-07-30 09:57:44'),
(16, 'default', 'created', 'App\\Models\\Timetable', 'created', 1, 'App\\Models\\User', 1, '{\"attributes\":{\"id\":1,\"school_id\":1,\"class_id\":2,\"section_id\":2,\"subject_id\":2,\"teacher_id\":2,\"day\":\"Monday\",\"start_time\":\"00:11:00\",\"end_time\":\"14:12:00\",\"room\":\"1\",\"status\":true,\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-07-30T15:38:42.000000Z\",\"updated_at\":\"2026-07-30T15:38:42.000000Z\",\"deleted_at\":null}}', NULL, '2026-07-30 10:08:42', '2026-07-30 10:08:42'),
(17, 'default', 'updated', 'App\\Models\\Timetable', 'updated', 1, 'App\\Models\\User', 1, '{\"attributes\":{\"start_time\":\"11:00:00\",\"end_time\":\"15:12:00\",\"updated_by\":1,\"updated_at\":\"2026-07-30T16:02:23.000000Z\"},\"old\":{\"start_time\":\"00:11:00\",\"end_time\":\"14:12:00\",\"updated_by\":null,\"updated_at\":\"2026-07-30T15:38:42.000000Z\"}}', NULL, '2026-07-30 10:32:23', '2026-07-30 10:32:23');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `numeric_name` int(11) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `school_id`, `name`, `numeric_name`, `remarks`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(2, 1, 'KG', 1, 'Kinder Garden', 1, 1, 1, '2026-07-29 08:32:48', '2026-07-29 09:23:32', NULL),
(3, 1, 'UN', 2, '2', 1, 1, 1, '2026-07-29 09:26:17', '2026-07-30 02:54:11', NULL),
(5, 1, 'ss', 22, '222', 1, 1, NULL, '2026-07-30 02:36:55', '2026-07-30 02:36:55', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_requests`
--

CREATE TABLE `leave_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `teacher_id` bigint(20) UNSIGNED NOT NULL,
  `leave_type_id` bigint(20) UNSIGNED NOT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `days` decimal(5,2) NOT NULL,
  `reason` text NOT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_types`
--

CREATE TABLE `leave_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `max_days` int(11) NOT NULL DEFAULT 0,
  `is_paid` tinyint(1) NOT NULL DEFAULT 1,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0000_07_28_113438_create_schools_table', 1),
(2, '0001_01_01_000000_create_users_table', 1),
(3, '0001_01_01_000001_create_cache_table', 1),
(4, '0001_01_01_000002_create_jobs_table', 1),
(5, '2026_07_28_073433_create_permission_tables', 1),
(6, '2026_07_28_080922_create_personal_access_tokens_table', 1),
(7, '2026_07_28_081041_create_activity_log_table', 1),
(8, '2026_07_28_081042_add_event_column_to_activity_log_table', 1),
(9, '2026_07_28_081043_add_batch_uuid_column_to_activity_log_table', 1),
(10, '2026_07_28_161642_create_academic_sessions_table', 2),
(11, '2026_07_29_112937_create_classes_table', 3),
(12, '2026_07_29_142704_create_subjects_table', 4),
(13, '2026_07_30_044443_create_sections_table', 5),
(14, '2026_07_30_081755_add_school_id_to_classes_table', 6),
(21, '2026_07_30_120637_create_teachers_table', 8),
(22, '2026_07_30_120640_create_timetables_table', 9),
(23, '2026_07_30_161001_create_leave_types_table', 10),
(24, '2026_07_30_160602_create_leave_requests_table', 11);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(6, 'App\\Models\\User', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'dashboard.view', 'sanctum', '2026-07-28 06:22:55', '2026-07-28 06:22:55'),
(2, 'school.view', 'sanctum', '2026-07-28 06:22:55', '2026-07-28 06:22:55'),
(3, 'school.create', 'sanctum', '2026-07-28 06:22:55', '2026-07-28 06:22:55'),
(4, 'school.update', 'sanctum', '2026-07-28 06:22:55', '2026-07-28 06:22:55'),
(5, 'school.delete', 'sanctum', '2026-07-28 06:22:55', '2026-07-28 06:22:55'),
(6, 'student.view', 'sanctum', '2026-07-28 06:22:55', '2026-07-28 06:22:55'),
(7, 'student.create', 'sanctum', '2026-07-28 06:22:55', '2026-07-28 06:22:55'),
(8, 'student.update', 'sanctum', '2026-07-28 06:22:55', '2026-07-28 06:22:55'),
(9, 'student.delete', 'sanctum', '2026-07-28 06:22:55', '2026-07-28 06:22:55'),
(10, 'teacher.view', 'sanctum', '2026-07-28 06:22:55', '2026-07-28 06:22:55'),
(11, 'teacher.create', 'sanctum', '2026-07-28 06:22:55', '2026-07-28 06:22:55'),
(12, 'teacher.update', 'sanctum', '2026-07-28 06:22:55', '2026-07-28 06:22:55'),
(13, 'teacher.delete', 'sanctum', '2026-07-28 06:22:55', '2026-07-28 06:22:55'),
(14, 'class.view', 'sanctum', '2026-07-28 06:22:55', '2026-07-28 06:22:55'),
(15, 'class.create', 'sanctum', '2026-07-28 06:22:55', '2026-07-28 06:22:55'),
(16, 'class.update', 'sanctum', '2026-07-28 06:22:55', '2026-07-28 06:22:55'),
(17, 'class.delete', 'sanctum', '2026-07-28 06:22:55', '2026-07-28 06:22:55');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\User', 1, 'admin', 'f4b40075eff10238f58d9db42e19cf7b815f9dec963ae7985cc2aecfc7a83c87', '[\"*\"]', '2026-07-28 07:10:16', NULL, '2026-07-28 06:47:02', '2026-07-28 07:10:16'),
(19, 'App\\Models\\User', 1, 'admin', '5c655de360071321bb783541c3caf518bc31405473d596d0b1c7d442e4f8b93c', '[\"*\"]', '2026-07-28 10:43:09', NULL, '2026-07-28 10:40:46', '2026-07-28 10:43:09'),
(33, 'App\\Models\\User', 1, 'admin', 'c282496b06ffff0a9507e6436af75b3e9b78fa626cf1fb0e923fa9a4783d304d', '[\"*\"]', '2026-07-30 03:04:08', NULL, '2026-07-29 07:29:44', '2026-07-30 03:04:08'),
(69, 'App\\Models\\User', 1, 'admin', '5f63486f1943c19bfec9a60961fff8e4ec0ab9ea93f4d9a8ec1abfdd042c0480', '[\"*\"]', '2026-07-30 10:49:24', NULL, '2026-07-30 10:03:15', '2026-07-30 10:49:24');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(6, 'Super Admin', 'sanctum', '2026-07-28 06:21:26', '2026-07-28 06:21:26'),
(7, 'School Admin', 'sanctum', '2026-07-28 06:21:26', '2026-07-28 06:21:26'),
(8, 'Principal', 'sanctum', '2026-07-28 06:21:26', '2026-07-28 06:21:26'),
(9, 'Teacher', 'sanctum', '2026-07-28 06:21:26', '2026-07-28 06:21:26'),
(10, 'Student', 'sanctum', '2026-07-28 06:21:26', '2026-07-28 06:21:26'),
(11, 'Parent', 'sanctum', '2026-07-28 06:21:26', '2026-07-28 06:21:26'),
(12, 'Accountant', 'sanctum', '2026-07-28 06:21:26', '2026-07-28 06:21:26'),
(13, 'Librarian', 'sanctum', '2026-07-28 06:21:26', '2026-07-28 06:21:26'),
(14, 'Staff', 'sanctum', '2026-07-28 06:21:26', '2026-07-28 06:21:26');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 6),
(2, 6),
(3, 6),
(4, 6),
(5, 6),
(6, 6),
(7, 6),
(8, 6),
(9, 6),
(10, 6),
(11, 6),
(12, 6),
(13, 6),
(14, 6),
(15, 6),
(16, 6),
(17, 6);

-- --------------------------------------------------------

--
-- Table structure for table `schools`
--

CREATE TABLE `schools` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `postal_code` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `schools`
--

INSERT INTO `schools` (`id`, `name`, `code`, `email`, `phone`, `address`, `city`, `state`, `country`, `postal_code`, `logo`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Demo School test', 'SCH001', 'info@school.com', '9999999999', 'Demo Address', 'Kolkata', 'West Bengal', 'India', '700001', NULL, 1, '2026-07-28 06:09:31', '2026-07-28 10:03:51', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `class_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `capacity` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`id`, `school_id`, `class_id`, `name`, `capacity`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 2, 'B', 10, 1, '2026-07-30 05:09:20', '2026-07-30 05:09:43', NULL),
(2, 1, 3, 'D', 22, 1, '2026-07-30 09:57:32', '2026-07-30 09:57:44', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('2cF2i7G5cwlkkNiLZjsSpDCigh1m3Ff1TLHppqxI', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRklsY3hOeXlFOU5JV09wNFFXbTVFdVBEQkxBSFNHOTVhZzB5VjBYdSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1785428363);

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(20) NOT NULL,
  `type` enum('Theory','Practical','Both') NOT NULL DEFAULT 'Theory',
  `description` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `name`, `code`, `type`, `description`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'English', 'ss', 'Practical', 'ssssss', 1, 1, 1, '2026-07-29 09:59:53', '2026-07-29 10:30:35', NULL),
(2, 'Math', 'm', 'Theory', 'mmm', 1, 1, NULL, '2026-07-29 10:31:01', '2026-07-29 10:31:01', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` varchar(50) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `gender` varchar(20) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `qualification` varchar(255) DEFAULT NULL,
  `experience` varchar(255) DEFAULT NULL,
  `joining_date` date NOT NULL,
  `address` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`id`, `school_id`, `employee_id`, `first_name`, `last_name`, `gender`, `date_of_birth`, `email`, `phone`, `qualification`, `experience`, `joining_date`, `address`, `photo`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, '101', 'Ahmed', 'Khan', 'Male', '2026-07-30', 'ahmed@gmail.com', '22650262', 'Grduate', '10', '2026-08-02', 'test address', 'teachers/ycMQppJ1S41hOcyC1PHU1dDKF8MQ7IZQEZVOQ5aH.png', 1, 1, NULL, '2026-07-30 08:44:35', '2026-07-30 08:44:35', NULL),
(2, 1, '102', 'Amber', 'Hussain', 'Male', '1983-07-18', 'amber@gmail.com', '12345678', 'MASTER', '12', '2026-07-24', 'test address goes here', 'teachers/XYSSJ4Uie7oQoDuYikXg3wQbcjT0ShojWWXQrRzU.jpg', 1, 1, NULL, '2026-07-30 08:55:53', '2026-07-30 08:55:53', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `timetables`
--

CREATE TABLE `timetables` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `class_id` bigint(20) UNSIGNED NOT NULL,
  `section_id` bigint(20) UNSIGNED NOT NULL,
  `subject_id` bigint(20) UNSIGNED NOT NULL,
  `teacher_id` bigint(20) UNSIGNED NOT NULL,
  `day` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `room` varchar(50) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `timetables`
--

INSERT INTO `timetables` (`id`, `school_id`, `class_id`, `section_id`, `subject_id`, `teacher_id`, `day`, `start_time`, `end_time`, `room`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 2, 2, 2, 2, 'Monday', '11:00:00', '15:12:00', '1', 1, 1, 1, '2026-07-30 10:08:42', '2026-07-30 10:32:23', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `user_type` varchar(50) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `school_id`, `name`, `username`, `email`, `phone`, `user_type`, `email_verified_at`, `password`, `status`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 1, 'Super Admin', 'admin', 'admin@school.com', NULL, 'super_admin', NULL, '$2y$12$4rmgd7jOygTLtonuFPrGf./0gMBWfozebZWvv8BXm.RFYiZnLhg7S', 1, NULL, '2026-07-28 06:09:31', '2026-07-28 06:24:49');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academic_sessions`
--
ALTER TABLE `academic_sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `academic_sessions_school_id_name_unique` (`school_id`,`name`),
  ADD KEY `academic_sessions_school_id_index` (`school_id`),
  ADD KEY `academic_sessions_status_index` (`status`),
  ADD KEY `academic_sessions_is_current_index` (`is_current`),
  ADD KEY `academic_sessions_school_id_status_index` (`school_id`,`status`);

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject` (`subject_type`,`subject_id`),
  ADD KEY `causer` (`causer_type`,`causer_id`),
  ADD KEY `activity_log_log_name_index` (`log_name`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `classes_school_id_foreign` (`school_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leave_requests_teacher_id_foreign` (`teacher_id`),
  ADD KEY `leave_requests_approved_by_foreign` (`approved_by`),
  ADD KEY `leave_requests_created_by_foreign` (`created_by`),
  ADD KEY `leave_requests_updated_by_foreign` (`updated_by`),
  ADD KEY `leave_requests_school_id_teacher_id_index` (`school_id`,`teacher_id`),
  ADD KEY `leave_requests_school_id_status_index` (`school_id`,`status`),
  ADD KEY `leave_requests_leave_type_id_index` (`leave_type_id`),
  ADD KEY `leave_requests_from_date_to_date_index` (`from_date`,`to_date`);

--
-- Indexes for table `leave_types`
--
ALTER TABLE `leave_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leave_types_school_id_foreign` (`school_id`),
  ADD KEY `leave_types_created_by_foreign` (`created_by`),
  ADD KEY `leave_types_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `schools`
--
ALTER TABLE `schools`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `schools_code_unique` (`code`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sections_school_id_class_id_name_unique` (`school_id`,`class_id`,`name`),
  ADD KEY `sections_class_id_foreign` (`class_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `subjects_code_unique` (`code`),
  ADD KEY `subjects_created_by_foreign` (`created_by`),
  ADD KEY `subjects_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `teachers_employee_id_unique` (`employee_id`),
  ADD UNIQUE KEY `teachers_email_unique` (`email`),
  ADD KEY `teachers_created_by_foreign` (`created_by`),
  ADD KEY `teachers_updated_by_foreign` (`updated_by`),
  ADD KEY `teachers_school_id_status_index` (`school_id`,`status`);

--
-- Indexes for table `timetables`
--
ALTER TABLE `timetables`
  ADD PRIMARY KEY (`id`),
  ADD KEY `timetables_school_id_index` (`school_id`),
  ADD KEY `timetables_class_id_index` (`class_id`),
  ADD KEY `timetables_section_id_index` (`section_id`),
  ADD KEY `timetables_subject_id_index` (`subject_id`),
  ADD KEY `timetables_teacher_id_index` (`teacher_id`),
  ADD KEY `timetables_day_index` (`day`),
  ADD KEY `timetables_school_id_class_id_section_id_day_index` (`school_id`,`class_id`,`section_id`,`day`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD KEY `users_school_id_foreign` (`school_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `academic_sessions`
--
ALTER TABLE `academic_sessions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leave_requests`
--
ALTER TABLE `leave_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leave_types`
--
ALTER TABLE `leave_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `schools`
--
ALTER TABLE `schools`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `timetables`
--
ALTER TABLE `timetables`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `academic_sessions`
--
ALTER TABLE `academic_sessions`
  ADD CONSTRAINT `academic_sessions_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `classes_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD CONSTRAINT `leave_requests_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `leave_requests_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `leave_requests_leave_type_id_foreign` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `leave_requests_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `leave_requests_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `leave_requests_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `leave_types`
--
ALTER TABLE `leave_types`
  ADD CONSTRAINT `leave_types_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `leave_types_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `leave_types_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sections`
--
ALTER TABLE `sections`
  ADD CONSTRAINT `sections_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sections_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subjects`
--
ALTER TABLE `subjects`
  ADD CONSTRAINT `subjects_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `subjects_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `teachers`
--
ALTER TABLE `teachers`
  ADD CONSTRAINT `teachers_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `teachers_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `teachers_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
