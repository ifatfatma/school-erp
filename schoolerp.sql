-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 09, 2026 at 06:16 PM
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
(1, 1, '2026-27', '2026-01-08', '2027-01-08', 1, 1, NULL, '2026-08-08 02:22:41', '2026-08-08 02:22:41', NULL);

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
(1, 'default', 'created', 'App\\Models\\School', 'created', 1, 'App\\Models\\User', 1, '{\"attributes\":{\"id\":1,\"name\":\"St. George English Day School\",\"code\":\"S001\",\"email\":\"info@gmail.com\",\"phone\":\"03355629768\",\"address\":\"G335 , meher manzil lane , garden reach\",\"city\":\"Kolkata\",\"state\":\"West Bengal\",\"country\":\"India\",\"postal_code\":\"700024\",\"logo\":null,\"status\":true,\"created_at\":\"2026-08-08T07:48:56.000000Z\",\"updated_at\":\"2026-08-08T07:48:56.000000Z\",\"deleted_at\":null}}', NULL, '2026-08-08 02:18:56', '2026-08-08 02:18:56'),
(2, 'default', 'created', 'App\\Models\\AcademicSession', 'created', 1, 'App\\Models\\User', 1, '{\"attributes\":{\"id\":1,\"school_id\":1,\"name\":\"2026-27\",\"start_date\":\"2026-01-08T00:00:00.000000Z\",\"end_date\":\"2027-01-08T00:00:00.000000Z\",\"is_current\":true,\"status\":true,\"description\":null,\"created_at\":\"2026-08-08T07:52:41.000000Z\",\"updated_at\":\"2026-08-08T07:52:41.000000Z\",\"deleted_at\":null}}', NULL, '2026-08-08 02:22:41', '2026-08-08 02:22:41'),
(3, 'default', 'created', 'App\\Models\\Section', 'created', 1, 'App\\Models\\User', 1, '{\"attributes\":{\"id\":1,\"school_id\":1,\"class_id\":1,\"name\":\"A\",\"capacity\":10,\"status\":true,\"created_at\":\"2026-08-08T07:57:02.000000Z\",\"updated_at\":\"2026-08-08T07:57:02.000000Z\",\"deleted_at\":null}}', NULL, '2026-08-08 02:27:02', '2026-08-08 02:27:02'),
(4, 'default', 'created', 'App\\Models\\Section', 'created', 2, 'App\\Models\\User', 1, '{\"attributes\":{\"id\":2,\"school_id\":1,\"class_id\":1,\"name\":\"B\",\"capacity\":10,\"status\":true,\"created_at\":\"2026-08-08T07:57:16.000000Z\",\"updated_at\":\"2026-08-08T07:57:16.000000Z\",\"deleted_at\":null}}', NULL, '2026-08-08 02:27:16', '2026-08-08 02:27:16'),
(5, 'default', 'created', 'App\\Models\\Section', 'created', 3, 'App\\Models\\User', 1, '{\"attributes\":{\"id\":3,\"school_id\":1,\"class_id\":2,\"name\":\"A\",\"capacity\":10,\"status\":true,\"created_at\":\"2026-08-08T07:57:27.000000Z\",\"updated_at\":\"2026-08-08T07:57:27.000000Z\",\"deleted_at\":null}}', NULL, '2026-08-08 02:27:27', '2026-08-08 02:27:27'),
(6, 'default', 'created', 'App\\Models\\Section', 'created', 4, 'App\\Models\\User', 1, '{\"attributes\":{\"id\":4,\"school_id\":1,\"class_id\":2,\"name\":\"B\",\"capacity\":10,\"status\":true,\"created_at\":\"2026-08-08T07:57:41.000000Z\",\"updated_at\":\"2026-08-08T07:57:41.000000Z\",\"deleted_at\":null}}', NULL, '2026-08-08 02:27:41', '2026-08-08 02:27:41'),
(7, 'default', 'created', 'App\\Models\\Section', 'created', 5, 'App\\Models\\User', 1, '{\"attributes\":{\"id\":5,\"school_id\":1,\"class_id\":3,\"name\":\"A\",\"capacity\":10,\"status\":true,\"created_at\":\"2026-08-08T07:57:58.000000Z\",\"updated_at\":\"2026-08-08T07:57:58.000000Z\",\"deleted_at\":null}}', NULL, '2026-08-08 02:27:58', '2026-08-08 02:27:58'),
(8, 'default', 'created', 'App\\Models\\Section', 'created', 6, 'App\\Models\\User', 1, '{\"attributes\":{\"id\":6,\"school_id\":1,\"class_id\":4,\"name\":\"A\",\"capacity\":10,\"status\":true,\"created_at\":\"2026-08-08T07:58:10.000000Z\",\"updated_at\":\"2026-08-08T07:58:10.000000Z\",\"deleted_at\":null}}', NULL, '2026-08-08 02:28:10', '2026-08-08 02:28:10'),
(9, 'default', 'created', 'App\\Models\\Section', 'created', 7, 'App\\Models\\User', 1, '{\"attributes\":{\"id\":7,\"school_id\":1,\"class_id\":5,\"name\":\"A\",\"capacity\":10,\"status\":true,\"created_at\":\"2026-08-08T07:58:22.000000Z\",\"updated_at\":\"2026-08-08T07:58:22.000000Z\",\"deleted_at\":null}}', NULL, '2026-08-08 02:28:22', '2026-08-08 02:28:22'),
(10, 'default', 'created', 'App\\Models\\Section', 'created', 8, 'App\\Models\\User', 1, '{\"attributes\":{\"id\":8,\"school_id\":1,\"class_id\":6,\"name\":\"A\",\"capacity\":10,\"status\":true,\"created_at\":\"2026-08-08T07:58:31.000000Z\",\"updated_at\":\"2026-08-08T07:58:31.000000Z\",\"deleted_at\":null}}', NULL, '2026-08-08 02:28:31', '2026-08-08 02:28:31'),
(11, 'default', 'created', 'App\\Models\\Subject', 'created', 1, 'App\\Models\\User', 1, '{\"attributes\":{\"id\":1,\"name\":\"English\",\"code\":\"S1\",\"type\":\"Theory\",\"description\":\"eng\",\"status\":true,\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-08-08T07:59:27.000000Z\",\"updated_at\":\"2026-08-08T07:59:27.000000Z\",\"deleted_at\":null}}', NULL, '2026-08-08 02:29:27', '2026-08-08 02:29:27'),
(12, 'default', 'created', 'App\\Models\\Subject', 'created', 2, 'App\\Models\\User', 1, '{\"attributes\":{\"id\":2,\"name\":\"Hindi\",\"code\":\"H1\",\"type\":\"Theory\",\"description\":\"test\",\"status\":true,\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-08-08T08:00:00.000000Z\",\"updated_at\":\"2026-08-08T08:00:00.000000Z\",\"deleted_at\":null}}', NULL, '2026-08-08 02:30:00', '2026-08-08 02:30:00'),
(13, 'default', 'created', 'App\\Models\\Subject', 'created', 3, 'App\\Models\\User', 1, '{\"attributes\":{\"id\":3,\"name\":\"Math\",\"code\":\"M1\",\"type\":\"Theory\",\"description\":\"math\",\"status\":true,\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-08-08T08:06:24.000000Z\",\"updated_at\":\"2026-08-08T08:06:24.000000Z\",\"deleted_at\":null}}', NULL, '2026-08-08 02:36:24', '2026-08-08 02:36:24'),
(14, 'default', 'created', 'App\\Models\\Timetable', 'created', 1, 'App\\Models\\User', 1, '{\"attributes\":{\"id\":1,\"school_id\":1,\"class_id\":1,\"section_id\":1,\"subject_id\":1,\"teacher_id\":1,\"day\":\"Monday\",\"start_time\":\"09:08:00\",\"end_time\":\"11:08:00\",\"room\":\"1\",\"status\":true,\"created_by\":1,\"updated_by\":null,\"created_at\":\"2026-08-08T12:33:05.000000Z\",\"updated_at\":\"2026-08-08T12:33:05.000000Z\",\"deleted_at\":null}}', NULL, '2026-08-08 07:03:05', '2026-08-08 07:03:05');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('school-erp-cache-spatie.permission.cache', 'a:3:{s:5:\"alias\";a:4:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";}s:11:\"permissions\";a:17:{i:0;a:4:{s:1:\"a\";i:1;s:1:\"b\";s:14:\"dashboard.view\";s:1:\"c\";s:7:\"sanctum\";s:1:\"r\";a:1:{i:0;i:6;}}i:1;a:4:{s:1:\"a\";i:2;s:1:\"b\";s:11:\"school.view\";s:1:\"c\";s:7:\"sanctum\";s:1:\"r\";a:1:{i:0;i:6;}}i:2;a:4:{s:1:\"a\";i:3;s:1:\"b\";s:13:\"school.create\";s:1:\"c\";s:7:\"sanctum\";s:1:\"r\";a:1:{i:0;i:6;}}i:3;a:4:{s:1:\"a\";i:4;s:1:\"b\";s:13:\"school.update\";s:1:\"c\";s:7:\"sanctum\";s:1:\"r\";a:1:{i:0;i:6;}}i:4;a:4:{s:1:\"a\";i:5;s:1:\"b\";s:13:\"school.delete\";s:1:\"c\";s:7:\"sanctum\";s:1:\"r\";a:1:{i:0;i:6;}}i:5;a:4:{s:1:\"a\";i:6;s:1:\"b\";s:12:\"student.view\";s:1:\"c\";s:7:\"sanctum\";s:1:\"r\";a:1:{i:0;i:6;}}i:6;a:4:{s:1:\"a\";i:7;s:1:\"b\";s:14:\"student.create\";s:1:\"c\";s:7:\"sanctum\";s:1:\"r\";a:1:{i:0;i:6;}}i:7;a:4:{s:1:\"a\";i:8;s:1:\"b\";s:14:\"student.update\";s:1:\"c\";s:7:\"sanctum\";s:1:\"r\";a:1:{i:0;i:6;}}i:8;a:4:{s:1:\"a\";i:9;s:1:\"b\";s:14:\"student.delete\";s:1:\"c\";s:7:\"sanctum\";s:1:\"r\";a:1:{i:0;i:6;}}i:9;a:4:{s:1:\"a\";i:10;s:1:\"b\";s:12:\"teacher.view\";s:1:\"c\";s:7:\"sanctum\";s:1:\"r\";a:1:{i:0;i:6;}}i:10;a:4:{s:1:\"a\";i:11;s:1:\"b\";s:14:\"teacher.create\";s:1:\"c\";s:7:\"sanctum\";s:1:\"r\";a:1:{i:0;i:6;}}i:11;a:4:{s:1:\"a\";i:12;s:1:\"b\";s:14:\"teacher.update\";s:1:\"c\";s:7:\"sanctum\";s:1:\"r\";a:1:{i:0;i:6;}}i:12;a:4:{s:1:\"a\";i:13;s:1:\"b\";s:14:\"teacher.delete\";s:1:\"c\";s:7:\"sanctum\";s:1:\"r\";a:1:{i:0;i:6;}}i:13;a:4:{s:1:\"a\";i:14;s:1:\"b\";s:10:\"class.view\";s:1:\"c\";s:7:\"sanctum\";s:1:\"r\";a:1:{i:0;i:6;}}i:14;a:4:{s:1:\"a\";i:15;s:1:\"b\";s:12:\"class.create\";s:1:\"c\";s:7:\"sanctum\";s:1:\"r\";a:1:{i:0;i:6;}}i:15;a:4:{s:1:\"a\";i:16;s:1:\"b\";s:12:\"class.update\";s:1:\"c\";s:7:\"sanctum\";s:1:\"r\";a:1:{i:0;i:6;}}i:16;a:4:{s:1:\"a\";i:17;s:1:\"b\";s:12:\"class.delete\";s:1:\"c\";s:7:\"sanctum\";s:1:\"r\";a:1:{i:0;i:6;}}}s:5:\"roles\";a:1:{i:0;a:3:{s:1:\"a\";i:6;s:1:\"b\";s:11:\"Super Admin\";s:1:\"c\";s:7:\"sanctum\";}}}', 1786261735);

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
(1, 1, 'KG', 1, 'Kinder Garden', 1, 1, NULL, '2026-08-08 02:25:09', '2026-08-08 02:25:09', NULL),
(2, 1, 'Class 1', 1, 'Class 1', 1, 1, NULL, '2026-08-08 02:25:45', '2026-08-08 02:25:45', NULL),
(3, 1, 'Class 2', 2, 'Class 2', 1, 1, NULL, '2026-08-08 02:26:00', '2026-08-08 02:26:00', NULL),
(4, 1, 'Class 3', 3, 'Class 3', 1, 1, NULL, '2026-08-08 02:26:16', '2026-08-08 02:26:16', NULL),
(5, 1, 'Class 4', 4, 'Class 4', 1, 1, NULL, '2026-08-08 02:26:28', '2026-08-08 02:26:28', NULL),
(6, 1, 'Class 5', 5, 'Class 5', 1, 1, NULL, '2026-08-08 02:26:40', '2026-08-08 02:26:40', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

CREATE TABLE `exams` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `academic_session_id` bigint(20) UNSIGNED NOT NULL,
  `exam_type_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `result_publish_date` date DEFAULT NULL,
  `is_marks_locked` tinyint(1) NOT NULL DEFAULT 0,
  `is_result_published` tinyint(1) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `remarks` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exams`
--

INSERT INTO `exams` (`id`, `school_id`, `academic_session_id`, `exam_type_id`, `name`, `code`, `description`, `start_date`, `end_date`, `result_publish_date`, `is_marks_locked`, `is_result_published`, `status`, `remarks`, `created_by`, `updated_by`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 'Half Yearly', 'HY01', 'Half Yearly', '2026-08-08', '2026-08-21', NULL, 0, 0, 1, NULL, 1, NULL, NULL, '2026-08-08 07:35:37', '2026-08-08 07:35:37');

-- --------------------------------------------------------

--
-- Table structure for table `exam_marks`
--

CREATE TABLE `exam_marks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `exam_schedule_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `theory_marks` decimal(8,2) NOT NULL DEFAULT 0.00,
  `practical_marks` decimal(8,2) NOT NULL DEFAULT 0.00,
  `viva_marks` decimal(8,2) NOT NULL DEFAULT 0.00,
  `project_marks` decimal(8,2) NOT NULL DEFAULT 0.00,
  `total_marks` decimal(8,2) NOT NULL DEFAULT 0.00,
  `attendance` varchar(20) NOT NULL DEFAULT 'Present',
  `remarks` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exam_schedules`
--

CREATE TABLE `exam_schedules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `exam_id` bigint(20) UNSIGNED NOT NULL,
  `class_id` bigint(20) UNSIGNED NOT NULL,
  `section_id` bigint(20) UNSIGNED NOT NULL,
  `subject_id` bigint(20) UNSIGNED NOT NULL,
  `teacher_id` bigint(20) UNSIGNED DEFAULT NULL,
  `exam_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `room_no` varchar(50) DEFAULT NULL,
  `full_marks` decimal(8,2) NOT NULL DEFAULT 100.00,
  `pass_marks` decimal(8,2) NOT NULL DEFAULT 40.00,
  `instructions` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exam_schedules`
--

INSERT INTO `exam_schedules` (`id`, `school_id`, `exam_id`, `class_id`, `section_id`, `subject_id`, `teacher_id`, `exam_date`, `start_time`, `end_time`, `room_no`, `full_marks`, `pass_marks`, `instructions`, `status`, `created_by`, `updated_by`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 1, 1, 1, '2026-08-10', '10:59:00', '11:59:00', '1', 100.00, 40.00, 'test', 1, 1, NULL, NULL, '2026-08-08 07:59:44', '2026-08-08 07:59:44');

-- --------------------------------------------------------

--
-- Table structure for table `exam_subjects`
--

CREATE TABLE `exam_subjects` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `exam_id` bigint(20) UNSIGNED NOT NULL,
  `class_id` bigint(20) UNSIGNED NOT NULL,
  `subject_id` bigint(20) UNSIGNED NOT NULL,
  `full_marks` decimal(8,2) NOT NULL DEFAULT 100.00,
  `pass_marks` decimal(8,2) NOT NULL DEFAULT 40.00,
  `theory_marks` decimal(8,2) NOT NULL DEFAULT 100.00,
  `practical_marks` decimal(8,2) NOT NULL DEFAULT 0.00,
  `viva_marks` decimal(8,2) NOT NULL DEFAULT 0.00,
  `project_marks` decimal(8,2) NOT NULL DEFAULT 0.00,
  `display_order` int(11) NOT NULL DEFAULT 1,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exam_subjects`
--

INSERT INTO `exam_subjects` (`id`, `school_id`, `exam_id`, `class_id`, `subject_id`, `full_marks`, `pass_marks`, `theory_marks`, `practical_marks`, `viva_marks`, `project_marks`, `display_order`, `status`, `created_by`, `updated_by`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 1, 100.00, 40.00, 100.00, 0.00, 0.00, 0.00, 1, 1, 1, NULL, NULL, '2026-08-08 07:51:25', '2026-08-08 07:51:25');

-- --------------------------------------------------------

--
-- Table structure for table `exam_types`
--

CREATE TABLE `exam_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(20) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exam_types`
--

INSERT INTO `exam_types` (`id`, `school_id`, `name`, `code`, `description`, `status`, `created_by`, `updated_by`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'Exam Type1', 'UT1', 'Exam Type1', 1, 1, NULL, NULL, '2026-08-08 07:33:36', '2026-08-08 07:33:36');

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
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(20) NOT NULL,
  `min_percentage` decimal(5,2) NOT NULL,
  `max_percentage` decimal(5,2) NOT NULL,
  `grade_point` decimal(5,2) NOT NULL DEFAULT 0.00,
  `is_pass` tinyint(1) NOT NULL DEFAULT 1,
  `description` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `grades`
--

INSERT INTO `grades` (`id`, `school_id`, `name`, `min_percentage`, `max_percentage`, `grade_point`, `is_pass`, `description`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'A+', 90.00, 100.00, 10.00, 1, 'A+', 1, 1, NULL, '2026-08-09 09:51:14', '2026-08-09 09:51:14', NULL),
(2, 1, 'A', 70.00, 89.00, 8.00, 1, 'test', 1, 1, NULL, '2026-08-09 09:52:06', '2026-08-09 09:52:06', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `guardians`
--

CREATE TABLE `guardians` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED DEFAULT NULL,
  `guardian_type` enum('Father','Mother','Grandfather','Grandmother','Uncle','Aunt','Brother','Sister','Legal Guardian','Other') NOT NULL DEFAULT 'Father',
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `mobile` varchar(20) NOT NULL,
  `alternate_mobile` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `occupation` varchar(255) DEFAULT NULL,
  `annual_income` decimal(12,2) DEFAULT 0.00,
  `education` varchar(255) DEFAULT NULL,
  `aadhaar_no` varchar(20) DEFAULT NULL,
  `pan_no` varchar(20) DEFAULT NULL,
  `relation` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `pincode` varchar(10) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
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
  `total_days` decimal(5,2) NOT NULL,
  `reason` text NOT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `status` tinyint(10) NOT NULL DEFAULT 0,
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
(24, '2026_07_30_160602_create_leave_requests_table', 11),
(25, '2026_07_31_142124_create_teacher_attendances_table', 12),
(26, '2026_08_01_051629_create_students_table', 13),
(27, '2026_08_01_115058_create_student_attendances_table', 14),
(28, '2026_08_01_130450_create_guardians_table', 15),
(29, '2026_08_01_130627_create_student_guardians_table', 15),
(30, '2026_08_02_095923_create_student_promotions_table', 16),
(31, '2026_08_02_110016_add_academic_session_id_to_students_table', 17),
(32, '2026_08_02_115733_create_exam_types_table', 18),
(33, '2026_08_03_080721_create_exams_table', 19),
(34, '2026_08_03_081328_add_description_to_exams_table', 20),
(35, '2026_08_03_081630_add_is_result_published_to_exams_table', 21),
(36, '2026_08_08_064821_create_exam_marks_table', 22),
(37, '2026_08_09_144701_create_grades_table', 23);

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
(2, 'App\\Models\\User', 1, 'admin', '1817b804e8fdf177bc933e45a4d691e9a5b6824d2db3d6d56d4fef03e09ae313', '[\"*\"]', '2026-08-09 10:35:38', NULL, '2026-08-09 07:49:05', '2026-08-09 10:35:38');

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
(1, 'St. George English Day School', 'S001', 'info@gmail.com', '03355629768', 'G335 , meher manzil lane , garden reach', 'Kolkata', 'West Bengal', 'India', '700024', NULL, 1, '2026-08-08 02:18:56', '2026-08-08 02:18:56', NULL);

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
(1, 1, 1, 'A', 10, 1, '2026-08-08 02:27:02', '2026-08-08 02:27:02', NULL),
(2, 1, 1, 'B', 10, 1, '2026-08-08 02:27:16', '2026-08-08 02:27:16', NULL),
(3, 1, 2, 'A', 10, 1, '2026-08-08 02:27:27', '2026-08-08 02:27:27', NULL),
(4, 1, 2, 'B', 10, 1, '2026-08-08 02:27:41', '2026-08-08 02:27:41', NULL),
(5, 1, 3, 'A', 10, 1, '2026-08-08 02:27:58', '2026-08-08 02:27:58', NULL),
(6, 1, 4, 'A', 10, 1, '2026-08-08 02:28:10', '2026-08-08 02:28:10', NULL),
(7, 1, 5, 'A', 10, 1, '2026-08-08 02:28:22', '2026-08-08 02:28:22', NULL),
(8, 1, 6, 'A', 10, 1, '2026-08-08 02:28:31', '2026-08-08 02:28:31', NULL);

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
('cjZq9zeHDSt3IPfN7qwHNRvjMAl9FxN96XCCpqyR', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRFZNOWJSNHFtMXVIVUg5WThvUm4wZWEzbFp4bm8zNzFlWUY0a2RydSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1786281489),
('gPZlcxwX6lH2yTgPNMByh52FiUFKkwFQhayTuadK', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMHh3R3hxVHZ4YU1rM2hBYVRpak1jQVlXakF2aXpRZVRGWGNZS0owSiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NzA6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC8ud2VsbC1rbm93bi9hcHBzcGVjaWZpYy9jb20uY2hyb21lLmRldnRvb2xzLmpzb24iO3M6NToicm91dGUiO047fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1786195316),
('iI0dkKDXGaA31CNB4vfTsRJ5AfUsuxVlT6pnavuH', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQmJXdFF1ZWVRaTN5Y1hzcTVnbzlyQnp3RHE2ZGxVWnVyejAwUm5ldCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1786189633),
('oj4th2zAyREUDe9MJEP29a7psGZDh71GOjBLRHMn', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZVVGUU5jczluN0wxQktPSndiWWlIUXk4bWJSMEJ6cm01azkwM0ZiTiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NzA6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC8ud2VsbC1rbm93bi9hcHBzcGVjaWZpYy9jb20uY2hyb21lLmRldnRvb2xzLmpzb24iO3M6NToicm91dGUiO047fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1786291562);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `academic_session_id` bigint(20) UNSIGNED DEFAULT NULL,
  `class_id` bigint(20) UNSIGNED NOT NULL,
  `section_id` bigint(20) UNSIGNED NOT NULL,
  `admission_no` varchar(255) NOT NULL,
  `roll_no` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `dob` date DEFAULT NULL,
  `admission_date` date NOT NULL,
  `blood_group` varchar(255) DEFAULT NULL,
  `religion` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `aadhaar_no` varchar(255) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `father_name` varchar(255) NOT NULL,
  `father_mobile` varchar(255) DEFAULT NULL,
  `father_occupation` varchar(255) DEFAULT NULL,
  `mother_name` varchar(255) DEFAULT NULL,
  `mother_mobile` varchar(255) DEFAULT NULL,
  `guardian_name` varchar(255) DEFAULT NULL,
  `guardian_mobile` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `school_id`, `academic_session_id`, `class_id`, `section_id`, `admission_no`, `roll_no`, `first_name`, `last_name`, `gender`, `dob`, `admission_date`, `blood_group`, `religion`, `category`, `aadhaar_no`, `mobile`, `email`, `address`, `father_name`, `father_mobile`, `father_occupation`, `mother_name`, `mother_mobile`, `guardian_name`, `guardian_mobile`, `photo`, `status`, `created_by`, `updated_by`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 1, 'A01', '1', 'Ahmed Hussain', 'Khan', 'Male', '2023-02-18', '2026-08-09', 'A+', NULL, NULL, '123456789123', '9007963069', 'ahmed@gmail.com', 'G218 ,Meher Manzil , Garden Reach Road , Kolkata - 24', 'Nurullah', '9831203456', 'Self Emplyed', 'Akhtari B egum', '9007963069', NULL, NULL, 'students/c6yi5CWC9l4LGKeRW9cdmIilj82gMMlb2nWYEfAK.png', 1, 1, NULL, NULL, '2026-08-09 08:19:06', '2026-08-09 08:19:06');

-- --------------------------------------------------------

--
-- Table structure for table `student_attendances`
--

CREATE TABLE `student_attendances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `class_id` bigint(20) UNSIGNED NOT NULL,
  `section_id` bigint(20) UNSIGNED NOT NULL,
  `attendance_date` date NOT NULL,
  `status` enum('Present','Absent','Late','Half Day','Leave') NOT NULL DEFAULT 'Present',
  `check_in` time DEFAULT NULL,
  `check_out` time DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_guardians`
--

CREATE TABLE `student_guardians` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `guardian_id` bigint(20) UNSIGNED NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_promotions`
--

CREATE TABLE `student_promotions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `from_session_id` bigint(20) UNSIGNED NOT NULL,
  `to_session_id` bigint(20) UNSIGNED NOT NULL,
  `from_class_id` bigint(20) UNSIGNED NOT NULL,
  `to_class_id` bigint(20) UNSIGNED NOT NULL,
  `from_section_id` bigint(20) UNSIGNED DEFAULT NULL,
  `to_section_id` bigint(20) UNSIGNED DEFAULT NULL,
  `promotion_date` date NOT NULL,
  `promotion_type` enum('Promoted','Repeated','Transferred','Graduated','Dropped') NOT NULL DEFAULT 'Promoted',
  `remarks` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(1, 'English', 'S1', 'Theory', 'eng', 1, 1, NULL, '2026-08-08 02:29:27', '2026-08-08 02:29:27', NULL),
(2, 'Hindi', 'H1', 'Theory', 'test', 1, 1, NULL, '2026-08-08 02:30:00', '2026-08-08 02:30:00', NULL),
(3, 'Math', 'M1', 'Theory', 'math', 1, 1, NULL, '2026-08-08 02:36:24', '2026-08-08 02:36:24', NULL);

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
(1, 1, 'E001', 'Imtiaz Alam', 'Khan', 'Male', '1988-08-29', 'itiaz@gmail.com', '22650262', 'Graduate', '5', '2026-08-01', 'test addrtess', 'teachers/aIVAnd1MJHri1xI2HbB2nazleoFZhK1mKh4W18il.png', 1, 1, NULL, '2026-08-08 06:27:06', '2026-08-08 06:27:06', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `teacher_attendances`
--

CREATE TABLE `teacher_attendances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `teacher_id` bigint(20) UNSIGNED NOT NULL,
  `attendance_date` date NOT NULL,
  `status` enum('Present','Absent','Late','Half Day','Leave') NOT NULL DEFAULT 'Present',
  `check_in` time DEFAULT NULL,
  `check_out` time DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(1, 1, 1, 1, 1, 1, 'Monday', '09:08:00', '11:08:00', '1', 1, 1, NULL, '2026-08-08 07:03:05', '2026-08-08 07:03:05', NULL);

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
-- Indexes for table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `exam_unique` (`school_id`,`academic_session_id`,`exam_type_id`,`name`),
  ADD KEY `exams_academic_session_id_foreign` (`academic_session_id`),
  ADD KEY `exams_exam_type_id_foreign` (`exam_type_id`),
  ADD KEY `exams_created_by_foreign` (`created_by`),
  ADD KEY `exams_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `exam_marks`
--
ALTER TABLE `exam_marks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `exam_marks_exam_schedule_id_student_id_unique` (`exam_schedule_id`,`student_id`),
  ADD KEY `exam_marks_school_id_foreign` (`school_id`),
  ADD KEY `exam_marks_student_id_foreign` (`student_id`),
  ADD KEY `exam_marks_created_by_foreign` (`created_by`),
  ADD KEY `exam_marks_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `exam_schedules`
--
ALTER TABLE `exam_schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_schedules_school_id_foreign` (`school_id`);

--
-- Indexes for table `exam_subjects`
--
ALTER TABLE `exam_subjects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_subjects_school_id_foreign` (`school_id`),
  ADD KEY `exam_subjects_exam_id_foreign` (`exam_id`);

--
-- Indexes for table `exam_types`
--
ALTER TABLE `exam_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `exam_types_school_id_name_unique` (`school_id`,`name`),
  ADD KEY `exam_types_created_by_foreign` (`created_by`),
  ADD KEY `exam_types_updated_by_foreign` (`updated_by`),
  ADD KEY `exam_types_status_index` (`status`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `grades_created_by_foreign` (`created_by`),
  ADD KEY `grades_updated_by_foreign` (`updated_by`),
  ADD KEY `grades_school_id_status_index` (`school_id`,`status`);

--
-- Indexes for table `guardians`
--
ALTER TABLE `guardians`
  ADD PRIMARY KEY (`id`),
  ADD KEY `guardians_created_by_foreign` (`created_by`),
  ADD KEY `guardians_updated_by_foreign` (`updated_by`),
  ADD KEY `guardians_school_id_status_index` (`school_id`,`status`),
  ADD KEY `guardians_guardian_type_index` (`guardian_type`),
  ADD KEY `guardians_mobile_index` (`mobile`),
  ADD KEY `guardians_email_index` (`email`);

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
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `students_admission_no_unique` (`admission_no`),
  ADD KEY `students_class_id_foreign` (`class_id`),
  ADD KEY `students_section_id_foreign` (`section_id`),
  ADD KEY `students_created_by_foreign` (`created_by`),
  ADD KEY `students_updated_by_foreign` (`updated_by`),
  ADD KEY `students_school_id_class_id_section_id_index` (`school_id`,`class_id`,`section_id`),
  ADD KEY `students_admission_no_index` (`admission_no`),
  ADD KEY `students_father_mobile_index` (`father_mobile`),
  ADD KEY `students_academic_session_id_foreign` (`academic_session_id`);

--
-- Indexes for table `student_attendances`
--
ALTER TABLE `student_attendances`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_attendance_unique` (`student_id`,`attendance_date`),
  ADD KEY `student_attendances_section_id_foreign` (`section_id`),
  ADD KEY `student_attendances_created_by_foreign` (`created_by`),
  ADD KEY `student_attendances_updated_by_foreign` (`updated_by`),
  ADD KEY `student_attendances_school_id_attendance_date_index` (`school_id`,`attendance_date`),
  ADD KEY `student_attendances_student_id_attendance_date_index` (`student_id`,`attendance_date`),
  ADD KEY `student_attendances_class_id_section_id_index` (`class_id`,`section_id`),
  ADD KEY `student_attendances_attendance_date_status_index` (`attendance_date`,`status`);

--
-- Indexes for table `student_guardians`
--
ALTER TABLE `student_guardians`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_guardians_student_id_guardian_id_unique` (`student_id`,`guardian_id`),
  ADD KEY `student_guardians_guardian_id_foreign` (`guardian_id`);

--
-- Indexes for table `student_promotions`
--
ALTER TABLE `student_promotions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_session_unique` (`student_id`,`to_session_id`),
  ADD KEY `student_promotions_to_session_id_foreign` (`to_session_id`),
  ADD KEY `student_promotions_to_class_id_foreign` (`to_class_id`),
  ADD KEY `student_promotions_from_section_id_foreign` (`from_section_id`),
  ADD KEY `student_promotions_to_section_id_foreign` (`to_section_id`),
  ADD KEY `student_promotions_created_by_foreign` (`created_by`),
  ADD KEY `student_promotions_updated_by_foreign` (`updated_by`),
  ADD KEY `student_promotions_school_id_student_id_index` (`school_id`,`student_id`),
  ADD KEY `student_promotions_from_session_id_to_session_id_index` (`from_session_id`,`to_session_id`),
  ADD KEY `student_promotions_from_class_id_to_class_id_index` (`from_class_id`,`to_class_id`),
  ADD KEY `student_promotions_promotion_type_index` (`promotion_type`);

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
-- Indexes for table `teacher_attendances`
--
ALTER TABLE `teacher_attendances`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `teacher_attendances_teacher_id_attendance_date_unique` (`teacher_id`,`attendance_date`),
  ADD KEY `teacher_attendances_school_id_foreign` (`school_id`),
  ADD KEY `teacher_attendances_created_by_foreign` (`created_by`),
  ADD KEY `teacher_attendances_updated_by_foreign` (`updated_by`);

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `exam_marks`
--
ALTER TABLE `exam_marks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exam_schedules`
--
ALTER TABLE `exam_schedules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `exam_subjects`
--
ALTER TABLE `exam_subjects`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `exam_types`
--
ALTER TABLE `exam_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `guardians`
--
ALTER TABLE `guardians`
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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `student_attendances`
--
ALTER TABLE `student_attendances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_guardians`
--
ALTER TABLE `student_guardians`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_promotions`
--
ALTER TABLE `student_promotions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `teacher_attendances`
--
ALTER TABLE `teacher_attendances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

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
-- Constraints for table `exams`
--
ALTER TABLE `exams`
  ADD CONSTRAINT `exams_academic_session_id_foreign` FOREIGN KEY (`academic_session_id`) REFERENCES `academic_sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exams_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `exams_exam_type_id_foreign` FOREIGN KEY (`exam_type_id`) REFERENCES `exam_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exams_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exams_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `exam_marks`
--
ALTER TABLE `exam_marks`
  ADD CONSTRAINT `exam_marks_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `exam_marks_exam_schedule_id_foreign` FOREIGN KEY (`exam_schedule_id`) REFERENCES `exam_schedules` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_marks_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_marks_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_marks_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `exam_schedules`
--
ALTER TABLE `exam_schedules`
  ADD CONSTRAINT `exam_schedules_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_subjects`
--
ALTER TABLE `exam_subjects`
  ADD CONSTRAINT `exam_subjects_exam_id_foreign` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_subjects_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_types`
--
ALTER TABLE `exam_types`
  ADD CONSTRAINT `exam_types_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `exam_types_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_types_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `grades`
--
ALTER TABLE `grades`
  ADD CONSTRAINT `grades_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `grades_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `grades_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `guardians`
--
ALTER TABLE `guardians`
  ADD CONSTRAINT `guardians_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `guardians_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `guardians_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

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
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_academic_session_id_foreign` FOREIGN KEY (`academic_session_id`) REFERENCES `academic_sessions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `students_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `students_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `students_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `students_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `students_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `student_attendances`
--
ALTER TABLE `student_attendances`
  ADD CONSTRAINT `student_attendances_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_attendances_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `student_attendances_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_attendances_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_attendances_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_attendances_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `student_guardians`
--
ALTER TABLE `student_guardians`
  ADD CONSTRAINT `student_guardians_guardian_id_foreign` FOREIGN KEY (`guardian_id`) REFERENCES `guardians` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_guardians_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_promotions`
--
ALTER TABLE `student_promotions`
  ADD CONSTRAINT `student_promotions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `student_promotions_from_class_id_foreign` FOREIGN KEY (`from_class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_promotions_from_section_id_foreign` FOREIGN KEY (`from_section_id`) REFERENCES `sections` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `student_promotions_from_session_id_foreign` FOREIGN KEY (`from_session_id`) REFERENCES `academic_sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_promotions_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_promotions_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_promotions_to_class_id_foreign` FOREIGN KEY (`to_class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_promotions_to_section_id_foreign` FOREIGN KEY (`to_section_id`) REFERENCES `sections` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `student_promotions_to_session_id_foreign` FOREIGN KEY (`to_session_id`) REFERENCES `academic_sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_promotions_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

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
-- Constraints for table `teacher_attendances`
--
ALTER TABLE `teacher_attendances`
  ADD CONSTRAINT `teacher_attendances_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `teacher_attendances_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `teacher_attendances_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `teacher_attendances_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
