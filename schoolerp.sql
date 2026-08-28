-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 28, 2026 at 06:12 PM
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
-- Table structure for table `authors`
--

CREATE TABLE `authors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `biography` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `authors`
--

INSERT INTO `authors` (`id`, `school_id`, `name`, `code`, `email`, `phone`, `biography`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Imtiaz Alam kHan', 'AUTH01', 'imtiaz@gmail.com', '9007963069', NULL, 1, 1, 1, '2026-08-15 09:56:33', '2026-08-15 09:56:58', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `publisher_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `book_code` varchar(255) NOT NULL,
  `isbn` varchar(255) DEFAULT NULL,
  `edition` varchar(255) DEFAULT NULL,
  `language` varchar(255) DEFAULT NULL,
  `publication_year` smallint(5) UNSIGNED DEFAULT NULL,
  `pages` int(10) UNSIGNED DEFAULT NULL,
  `price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `description` text DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `school_id`, `category_id`, `publisher_id`, `title`, `book_code`, `isbn`, `edition`, `language`, `publication_year`, `pages`, `price`, `description`, `cover_image`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 2, 1, 'Jungle Book', 'JUN001', 'ISBN01', '1st Edition', 'Hindi', 2026, 500, 500.00, 'test', NULL, 1, 1, 1, '2026-08-16 08:55:25', '2026-08-16 08:55:25', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `book_authors`
--

CREATE TABLE `book_authors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `book_id` bigint(20) UNSIGNED NOT NULL,
  `author_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `book_authors`
--

INSERT INTO `book_authors` (`id`, `book_id`, `author_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2026-08-16 08:55:25', '2026-08-16 08:55:25');

-- --------------------------------------------------------

--
-- Table structure for table `book_categories`
--

CREATE TABLE `book_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `book_categories`
--

INSERT INTO `book_categories` (`id`, `school_id`, `name`, `code`, `description`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Hindi', 'H01', NULL, 1, 1, 1, '2026-08-15 09:55:45', '2026-08-15 09:55:45', NULL),
(2, 1, 'English', 'ENG01', NULL, 1, 1, 1, '2026-08-15 09:55:56', '2026-08-15 09:55:56', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `book_copies`
--

CREATE TABLE `book_copies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `book_id` bigint(20) UNSIGNED NOT NULL,
  `accession_no` varchar(255) NOT NULL,
  `barcode` varchar(255) DEFAULT NULL,
  `copy_no` varchar(255) DEFAULT NULL,
  `shelf_no` varchar(255) DEFAULT NULL,
  `rack_no` varchar(255) DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `purchase_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `condition` enum('New','Good','Fair','Damaged','Lost') NOT NULL DEFAULT 'New',
  `status` enum('Available','Issued','Reserved','Lost','Damaged','Maintenance') NOT NULL DEFAULT 'Available',
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `book_copies`
--

INSERT INTO `book_copies` (`id`, `school_id`, `book_id`, `accession_no`, `barcode`, `copy_no`, `shelf_no`, `rack_no`, `purchase_date`, `purchase_price`, `condition`, `status`, `notes`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 'LIB-0001', '100091', NULL, 'S-01', 'E-01', '2026-08-16', 500.00, 'New', 'Issued', NULL, 1, 1, '2026-08-16 09:55:38', '2026-08-21 08:19:01', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `book_issues`
--

CREATE TABLE `book_issues` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `library_member_id` bigint(20) UNSIGNED NOT NULL,
  `book_id` bigint(20) UNSIGNED NOT NULL,
  `book_copy_id` bigint(20) UNSIGNED NOT NULL,
  `issue_no` varchar(255) NOT NULL,
  `issue_date` date NOT NULL,
  `due_date` date NOT NULL,
  `return_date` date DEFAULT NULL,
  `status` enum('Issued','Returned','Overdue','Lost','Damaged') NOT NULL DEFAULT 'Issued',
  `notes` text DEFAULT NULL,
  `issued_by` bigint(20) UNSIGNED DEFAULT NULL,
  `returned_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `book_issues`
--

INSERT INTO `book_issues` (`id`, `school_id`, `library_member_id`, `book_id`, `book_copy_id`, `issue_no`, `issue_date`, `due_date`, `return_date`, `status`, `notes`, `issued_by`, `returned_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 1, 1, 'ISS-202608-0001', '2026-08-19', '2026-09-02', '2026-08-19', 'Returned', 'test', 1, NULL, '2026-08-19 08:58:54', '2026-08-19 09:06:22', NULL),
(2, 1, 1, 1, 1, 'ISS-202608-0002', '2026-08-01', '2026-10-04', NULL, 'Issued', NULL, 1, NULL, '2026-08-21 08:19:01', '2026-08-21 08:19:25', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `book_renewals`
--

CREATE TABLE `book_renewals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `book_issue_id` bigint(20) UNSIGNED NOT NULL,
  `library_member_id` bigint(20) UNSIGNED NOT NULL,
  `book_id` bigint(20) UNSIGNED NOT NULL,
  `book_copy_id` bigint(20) UNSIGNED NOT NULL,
  `renewal_no` varchar(255) NOT NULL,
  `renewal_date` date NOT NULL,
  `old_due_date` date NOT NULL,
  `new_due_date` date NOT NULL,
  `renewal_count` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `remarks` text DEFAULT NULL,
  `renewed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `book_renewals`
--

INSERT INTO `book_renewals` (`id`, `school_id`, `book_issue_id`, `library_member_id`, `book_id`, `book_copy_id`, `renewal_no`, `renewal_date`, `old_due_date`, `new_due_date`, `renewal_count`, `remarks`, `renewed_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 2, 1, 1, 1, 'REN-202608-0001', '2026-08-21', '2026-09-20', '2026-10-04', 1, NULL, 1, '2026-08-21 08:19:25', '2026-08-21 08:19:25', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `book_returns`
--

CREATE TABLE `book_returns` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `book_issue_id` bigint(20) UNSIGNED NOT NULL,
  `library_member_id` bigint(20) UNSIGNED NOT NULL,
  `book_id` bigint(20) UNSIGNED NOT NULL,
  `book_copy_id` bigint(20) UNSIGNED NOT NULL,
  `return_no` varchar(255) NOT NULL,
  `return_date` date NOT NULL,
  `due_date` date NOT NULL,
  `overdue_days` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `fine_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `fine_status` enum('No Fine','Pending','Paid') NOT NULL DEFAULT 'No Fine',
  `book_condition` enum('Good','Damaged','Lost') NOT NULL DEFAULT 'Good',
  `remarks` text DEFAULT NULL,
  `returned_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `book_returns`
--

INSERT INTO `book_returns` (`id`, `school_id`, `book_issue_id`, `library_member_id`, `book_id`, `book_copy_id`, `return_no`, `return_date`, `due_date`, `overdue_days`, `fine_amount`, `fine_status`, `book_condition`, `remarks`, `returned_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 1, 1, 1, 'RET-202608-0001', '2026-08-19', '2026-09-02', 0, 0.00, 'No Fine', 'Good', NULL, 1, '2026-08-19 09:06:22', '2026-08-19 09:06:22', NULL);

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
(1, 1, 'KG', 1, 'Kinder Garden', 1, 1, NULL, '2026-08-08 02:25:09', '2026-08-08 02:25:09', NULL),
(2, 1, 'Class 1', 1, 'Class 1', 1, 1, NULL, '2026-08-08 02:25:45', '2026-08-08 02:25:45', NULL),
(3, 1, 'Class 2', 2, 'Class 2', 1, 1, NULL, '2026-08-08 02:26:00', '2026-08-08 02:26:00', NULL),
(4, 1, 'Class 3', 3, 'Class 3', 1, 1, NULL, '2026-08-08 02:26:16', '2026-08-08 02:26:16', NULL),
(5, 1, 'Class 4', 4, 'Class 4', 1, 1, NULL, '2026-08-08 02:26:28', '2026-08-08 02:26:28', NULL),
(6, 1, 'Class 5', 5, 'Class 5', 1, 1, NULL, '2026-08-08 02:26:40', '2026-08-08 02:26:40', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `discounts`
--

CREATE TABLE `discounts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `discount_type` enum('Percentage','Fixed') NOT NULL,
  `value` decimal(10,2) NOT NULL DEFAULT 0.00,
  `max_discount` decimal(10,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `discounts`
--

INSERT INTO `discounts` (`id`, `school_id`, `name`, `code`, `discount_type`, `value`, `max_discount`, `description`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'New Admission', 'NEW01', 'Percentage', 5.00, NULL, NULL, 1, 1, 1, '2026-08-13 09:44:29', '2026-08-13 09:44:29', NULL);

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

--
-- Dumping data for table `exam_marks`
--

INSERT INTO `exam_marks` (`id`, `school_id`, `exam_schedule_id`, `student_id`, `theory_marks`, `practical_marks`, `viva_marks`, `project_marks`, `total_marks`, `attendance`, `remarks`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 1, 90.00, 0.00, 0.00, 0.00, 90.00, 'Present', NULL, 1, 1, '2026-08-11 07:29:27', '2026-08-11 07:42:19', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `exam_ranks`
--

CREATE TABLE `exam_ranks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `academic_session_id` bigint(20) UNSIGNED NOT NULL,
  `exam_id` bigint(20) UNSIGNED NOT NULL,
  `class_id` bigint(20) UNSIGNED NOT NULL,
  `section_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `total_full_marks` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_obtained_marks` decimal(10,2) NOT NULL DEFAULT 0.00,
  `percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `rank` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exam_ranks`
--

INSERT INTO `exam_ranks` (`id`, `school_id`, `academic_session_id`, `exam_id`, `class_id`, `section_id`, `student_id`, `total_full_marks`, `total_obtained_marks`, `percentage`, `rank`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 1, 1, 1, 100.00, 90.00, 90.00, 1, '2026-08-12 07:32:07', '2026-08-12 07:32:07');

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
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `expense_category_id` bigint(20) UNSIGNED NOT NULL,
  `expense_date` date NOT NULL,
  `expense_no` varchar(255) DEFAULT NULL,
  `payee_name` varchar(255) NOT NULL,
  `payment_method` varchar(255) NOT NULL DEFAULT 'Cash',
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `reference_no` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `status` enum('Paid','Pending','Cancelled') NOT NULL DEFAULT 'Paid',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `school_id`, `expense_category_id`, `expense_date`, `expense_no`, `payee_name`, `payment_method`, `amount`, `reference_no`, `description`, `attachment`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, '2026-08-15', '1', 'CESC', 'Online', 5000.00, 'RFC00783464564', 'test', 'expenses/YY5Jd8qBkKwHMZitj254qq0FdOpWbwImlIwHrR6b.pdf', 'Paid', 1, 1, '2026-08-15 03:11:38', '2026-08-15 03:11:38', NULL),
(2, 1, 1, '2026-08-15', NULL, 'test', 'Cash', 5000.00, NULL, NULL, NULL, 'Pending', 1, 1, '2026-08-15 05:15:46', '2026-08-15 05:15:46', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `expense_categories`
--

CREATE TABLE `expense_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expense_categories`
--

INSERT INTO `expense_categories` (`id`, `school_id`, `name`, `code`, `description`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Electricity Bill', 'ELECB', NULL, 1, 1, 1, '2026-08-15 02:46:37', '2026-08-15 02:46:37', NULL),
(2, 1, 'Stationery Items', 'STAI', NULL, 1, 1, 1, '2026-08-15 02:46:59', '2026-08-15 02:46:59', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `expense_payments`
--

CREATE TABLE `expense_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `expense_id` bigint(20) UNSIGNED NOT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `payment_method` enum('Cash','Card','Bank Transfer','UPI','Cheque','Online','Other') NOT NULL DEFAULT 'Cash',
  `transaction_no` varchar(255) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `cheque_no` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `status` enum('Success','Pending','Cancelled') NOT NULL DEFAULT 'Success',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expense_payments`
--

INSERT INTO `expense_payments` (`id`, `school_id`, `expense_id`, `payment_date`, `amount`, `payment_method`, `transaction_no`, `bank_name`, `cheque_no`, `remarks`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2026-08-15', 5000.00, 'Cash', 't0001', 'BOI', NULL, NULL, 'Success', 1, 1, '2026-08-15 04:41:31', '2026-08-15 04:41:31'),
(2, 1, 2, '2026-08-15', 500.00, 'Cash', NULL, NULL, NULL, NULL, 'Success', 1, 1, '2026-08-15 05:16:28', '2026-08-15 05:16:28');

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
-- Table structure for table `fee_payments`
--

CREATE TABLE `fee_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `academic_session_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `receipt_no` varchar(100) NOT NULL,
  `payment_date` date NOT NULL,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_method` enum('Cash','Card','Bank Transfer','UPI','Cheque','Online','Other') NOT NULL DEFAULT 'Cash',
  `transaction_no` varchar(150) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `cheque_no` varchar(100) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `status` enum('Success','Pending','Cancelled') NOT NULL DEFAULT 'Success',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fee_payments`
--

INSERT INTO `fee_payments` (`id`, `school_id`, `academic_session_id`, `student_id`, `receipt_no`, `payment_date`, `total_amount`, `payment_method`, `transaction_no`, `bank_name`, `cheque_no`, `remarks`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 'RCPT-2026-000001', '2026-08-13', 5500.00, 'Cash', NULL, NULL, NULL, NULL, 'Success', 1, 1, '2026-08-13 06:08:35', '2026-08-13 06:08:35');

-- --------------------------------------------------------

--
-- Table structure for table `fee_payment_items`
--

CREATE TABLE `fee_payment_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `fee_payment_id` bigint(20) UNSIGNED NOT NULL,
  `student_fee_due_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fee_payment_items`
--

INSERT INTO `fee_payment_items` (`id`, `fee_payment_id`, `student_fee_due_id`, `amount`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 5500.00, '2026-08-13 06:08:35', '2026-08-13 06:08:35');

-- --------------------------------------------------------

--
-- Table structure for table `fee_structures`
--

CREATE TABLE `fee_structures` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `academic_session_id` bigint(20) UNSIGNED NOT NULL,
  `class_id` bigint(20) UNSIGNED NOT NULL,
  `fee_type_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `effective_from` date DEFAULT NULL,
  `effective_to` date DEFAULT NULL,
  `due_day` int(11) DEFAULT NULL COMMENT 'Day of month when fee becomes due',
  `late_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fee_structures`
--

INSERT INTO `fee_structures` (`id`, `school_id`, `academic_session_id`, `class_id`, `fee_type_id`, `amount`, `effective_from`, `effective_to`, `due_day`, `late_fee`, `status`, `created_by`, `updated_by`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 1, 5000.00, '2026-08-12', '2026-08-20', 10, 500.00, 1, 1, NULL, NULL, '2026-08-12 09:30:38', '2026-08-12 09:30:38');

-- --------------------------------------------------------

--
-- Table structure for table `fee_types`
--

CREATE TABLE `fee_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `frequency` enum('One Time','Monthly','Quarterly','Half Yearly','Yearly') NOT NULL DEFAULT 'Monthly',
  `description` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fee_types`
--

INSERT INTO `fee_types` (`id`, `school_id`, `name`, `code`, `frequency`, `description`, `status`, `created_by`, `updated_by`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'Session Fee', 'SF', 'Quarterly', NULL, 1, 1, NULL, NULL, '2026-08-12 08:28:46', '2026-08-12 08:28:46');

-- --------------------------------------------------------

--
-- Table structure for table `fine_collections`
--

CREATE TABLE `fine_collections` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `library_fine_id` bigint(20) UNSIGNED NOT NULL,
  `library_member_id` bigint(20) UNSIGNED NOT NULL,
  `collection_no` varchar(255) NOT NULL,
  `receipt_no` varchar(255) DEFAULT NULL,
  `collection_date` date NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_mode` enum('Cash','Card','Bank Transfer','UPI','Cheque','Online') NOT NULL DEFAULT 'Cash',
  `transaction_no` varchar(255) DEFAULT NULL,
  `collected_by` bigint(20) UNSIGNED DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
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
-- Table structure for table `incomes`
--

CREATE TABLE `incomes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `income_category_id` bigint(20) UNSIGNED NOT NULL,
  `income_date` date NOT NULL,
  `income_no` varchar(255) DEFAULT NULL,
  `received_from` varchar(255) NOT NULL,
  `payment_method` varchar(255) NOT NULL DEFAULT 'Cash',
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `reference_no` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `status` enum('Received','Pending','Cancelled') NOT NULL DEFAULT 'Received',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `incomes`
--

INSERT INTO `incomes` (`id`, `school_id`, `income_category_id`, `income_date`, `income_no`, `received_from`, `payment_method`, `amount`, `reference_no`, `description`, `attachment`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 2, '2026-08-15', 'IN01', 'School', 'Cash', 59000.00, NULL, NULL, NULL, 'Received', 1, 1, '2026-08-15 07:12:47', '2026-08-15 07:12:47', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `income_categories`
--

CREATE TABLE `income_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `income_categories`
--

INSERT INTO `income_categories` (`id`, `school_id`, `name`, `code`, `description`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Donation', 'DON', NULL, 1, 1, 1, '2026-08-15 06:29:39', '2026-08-15 06:29:39', NULL),
(2, 1, 'School Fees', 'SCFE', NULL, 1, 1, 1, '2026-08-15 06:29:53', '2026-08-15 06:29:53', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `income_payments`
--

CREATE TABLE `income_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `income_id` bigint(20) UNSIGNED NOT NULL,
  `receipt_no` varchar(100) NOT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `payment_method` varchar(100) NOT NULL DEFAULT 'Cash',
  `transaction_no` varchar(150) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `cheque_no` varchar(100) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `status` enum('Success','Pending','Cancelled') NOT NULL DEFAULT 'Success',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `income_payments`
--

INSERT INTO `income_payments` (`id`, `school_id`, `income_id`, `receipt_no`, `payment_date`, `amount`, `payment_method`, `transaction_no`, `bank_name`, `cheque_no`, `remarks`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'INR-2026-000001', '2026-08-15', 59000.00, 'Cash', 'trsn01', 'boi', NULL, NULL, 'Success', 1, 1, '2026-08-15 08:49:31', '2026-08-15 08:49:31');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_categories`
--

CREATE TABLE `inventory_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventory_categories`
--

INSERT INTO `inventory_categories` (`id`, `school_id`, `name`, `code`, `description`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Statiotnery', 'STATS', NULL, 1, '2026-08-27 06:49:13', '2026-08-27 06:49:28', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `inventory_items`
--

CREATE TABLE `inventory_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `inventory_category_id` bigint(20) UNSIGNED NOT NULL,
  `inventory_unit_id` bigint(20) UNSIGNED NOT NULL,
  `inventory_supplier_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `sku` varchar(255) DEFAULT NULL,
  `barcode` varchar(255) DEFAULT NULL,
  `opening_stock` decimal(12,2) NOT NULL DEFAULT 0.00,
  `reorder_level` decimal(12,2) NOT NULL DEFAULT 0.00,
  `cost_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `sale_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventory_items`
--

INSERT INTO `inventory_items` (`id`, `school_id`, `inventory_category_id`, `inventory_unit_id`, `inventory_supplier_id`, `name`, `sku`, `barcode`, `opening_stock`, `reorder_level`, `cost_price`, `sale_price`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 1, 1, 'Item 1', 'ss', 'ss', 100.00, 20.00, 11.00, 22.00, 1, '2026-08-27 09:03:09', '2026-08-27 09:03:27', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `inventory_purchases`
--

CREATE TABLE `inventory_purchases` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `inventory_supplier_id` bigint(20) UNSIGNED DEFAULT NULL,
  `purchase_no` varchar(255) NOT NULL,
  `purchase_date` date NOT NULL,
  `invoice_no` varchar(255) DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tax` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Received',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventory_purchases`
--

INSERT INTO `inventory_purchases` (`id`, `school_id`, `inventory_supplier_id`, `purchase_no`, `purchase_date`, `invoice_no`, `subtotal`, `discount`, `tax`, `total_amount`, `notes`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 'PUR-202608-0001', '2026-08-27', 'INV001', 22.00, 0.00, 0.00, 22.00, NULL, 'Received', '2026-08-27 09:34:29', '2026-08-27 09:34:29', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `inventory_purchase_items`
--

CREATE TABLE `inventory_purchase_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `inventory_purchase_id` bigint(20) UNSIGNED NOT NULL,
  `inventory_item_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `unit_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tax` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventory_purchase_items`
--

INSERT INTO `inventory_purchase_items` (`id`, `school_id`, `inventory_purchase_id`, `inventory_item_id`, `quantity`, `unit_price`, `discount`, `tax`, `total`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 1.00, 11.00, 0.00, 0.00, 11.00, '2026-08-27 09:34:29', '2026-08-27 09:34:29'),
(2, 1, 1, 1, 1.00, 11.00, 0.00, 0.00, 11.00, '2026-08-27 09:34:29', '2026-08-27 09:34:29');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_stock_ins`
--

CREATE TABLE `inventory_stock_ins` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `inventory_supplier_id` bigint(20) UNSIGNED DEFAULT NULL,
  `stock_in_no` varchar(255) NOT NULL,
  `stock_in_date` date NOT NULL,
  `invoice_no` varchar(255) DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tax` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` varchar(255) NOT NULL DEFAULT 'Received',
  `remarks` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventory_stock_ins`
--

INSERT INTO `inventory_stock_ins` (`id`, `school_id`, `inventory_supplier_id`, `stock_in_no`, `stock_in_date`, `invoice_no`, `subtotal`, `discount`, `tax`, `total_amount`, `status`, `remarks`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 'STI-202608-0001', '2026-08-27', 'INV001', 22.00, 0.00, 0.00, 22.00, 'Received', NULL, 1, '2026-08-27 10:28:32', '2026-08-27 10:28:32', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `inventory_stock_in_items`
--

CREATE TABLE `inventory_stock_in_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `inventory_stock_in_id` bigint(20) UNSIGNED NOT NULL,
  `inventory_item_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `unit_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tax` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventory_stock_in_items`
--

INSERT INTO `inventory_stock_in_items` (`id`, `school_id`, `inventory_stock_in_id`, `inventory_item_id`, `quantity`, `unit_price`, `discount`, `tax`, `total`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 1.00, 11.00, 0.00, 0.00, 11.00, '2026-08-27 10:28:32', '2026-08-27 10:28:32'),
(2, 1, 1, 1, 1.00, 11.00, 0.00, 0.00, 11.00, '2026-08-27 10:28:32', '2026-08-27 10:28:32');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_stock_movements`
--

CREATE TABLE `inventory_stock_movements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `inventory_item_id` bigint(20) UNSIGNED NOT NULL,
  `movement_no` varchar(255) NOT NULL,
  `movement_type` varchar(255) NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `unit_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `stock_before` decimal(12,2) NOT NULL DEFAULT 0.00,
  `stock_after` decimal(12,2) NOT NULL DEFAULT 0.00,
  `movement_date` date NOT NULL,
  `reference_type` varchar(255) DEFAULT NULL,
  `reference_id` bigint(20) UNSIGNED DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventory_stock_movements`
--

INSERT INTO `inventory_stock_movements` (`id`, `school_id`, `inventory_item_id`, `movement_no`, `movement_type`, `quantity`, `unit_price`, `total_amount`, `stock_before`, `stock_after`, `movement_date`, `reference_type`, `reference_id`, `remarks`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'MOV-202608-000001', 'IN', 1.00, 11.00, 11.00, 100.00, 101.00, '2026-08-27', 'StockIn', 1, NULL, 1, '2026-08-27 10:28:32', '2026-08-27 10:28:32'),
(2, 1, 1, 'MOV-202608-000002', 'IN', 1.00, 11.00, 11.00, 101.00, 102.00, '2026-08-27', 'StockIn', 1, NULL, 1, '2026-08-27 10:28:32', '2026-08-27 10:28:32');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_stock_outs`
--

CREATE TABLE `inventory_stock_outs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `stock_out_no` varchar(255) NOT NULL,
  `stock_out_date` date NOT NULL,
  `reference_no` varchar(255) DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` varchar(255) NOT NULL DEFAULT 'Issued',
  `remarks` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_stock_out_items`
--

CREATE TABLE `inventory_stock_out_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `inventory_stock_out_id` bigint(20) UNSIGNED NOT NULL,
  `inventory_item_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `unit_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tax` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_suppliers`
--

CREATE TABLE `inventory_suppliers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(100) DEFAULT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `tax_number` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventory_suppliers`
--

INSERT INTO `inventory_suppliers` (`id`, `school_id`, `name`, `code`, `contact_person`, `phone`, `email`, `address`, `city`, `state`, `country`, `tax_number`, `notes`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'SN Enterprise', 'SN001', 'Hamid Hussain', '9007963060', 'sn@gmail.com', 'supplier address goes here', 'kolkata', 'West Bengal', 'India', NULL, NULL, 1, '2026-08-27 08:10:17', '2026-08-27 08:10:17', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `inventory_units`
--

CREATE TABLE `inventory_units` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(100) DEFAULT NULL,
  `short_name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventory_units`
--

INSERT INTO `inventory_units` (`id`, `school_id`, `name`, `code`, `short_name`, `description`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Piece', 'PCS', NULL, NULL, 1, '2026-08-27 07:09:47', '2026-08-27 07:09:47', NULL);

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
-- Table structure for table `late_fees`
--

CREATE TABLE `late_fees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `late_fee_type` enum('Fixed','Percentage') NOT NULL,
  `value` decimal(10,2) NOT NULL DEFAULT 0.00,
  `grace_days` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `max_amount` decimal(10,2) DEFAULT NULL,
  `per_day` tinyint(1) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `late_fees`
--

INSERT INTO `late_fees` (`id`, `school_id`, `name`, `code`, `late_fee_type`, `value`, `grace_days`, `max_amount`, `per_day`, `description`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'July', 'JL01', 'Fixed', 1000.00, 20, 500.00, 1, NULL, 1, 1, 1, '2026-08-15 02:18:35', '2026-08-15 02:18:35', NULL);

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
-- Table structure for table `library_fines`
--

CREATE TABLE `library_fines` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `library_member_id` bigint(20) UNSIGNED NOT NULL,
  `book_issue_id` bigint(20) UNSIGNED DEFAULT NULL,
  `book_id` bigint(20) UNSIGNED DEFAULT NULL,
  `book_copy_id` bigint(20) UNSIGNED DEFAULT NULL,
  `lost_damaged_book_id` bigint(20) UNSIGNED DEFAULT NULL,
  `fine_no` varchar(255) NOT NULL,
  `fine_date` date NOT NULL,
  `fine_type` enum('Overdue','Lost','Damaged','Other') NOT NULL,
  `description` text DEFAULT NULL,
  `overdue_days` int(11) NOT NULL DEFAULT 0,
  `fine_per_day` decimal(12,2) NOT NULL DEFAULT 0.00,
  `fine_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `waiver_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `payable_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` enum('Pending','Partially Paid','Paid','Waived') NOT NULL DEFAULT 'Pending',
  `remarks` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `library_members`
--

CREATE TABLE `library_members` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `member_no` varchar(255) NOT NULL,
  `member_type` enum('Student','Staff') NOT NULL,
  `student_id` bigint(20) UNSIGNED DEFAULT NULL,
  `staff_id` bigint(20) UNSIGNED DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `membership_date` date NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `issue_limit` int(10) UNSIGNED NOT NULL DEFAULT 3,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `library_members`
--

INSERT INTO `library_members` (`id`, `school_id`, `member_no`, `member_type`, `student_id`, `staff_id`, `phone`, `email`, `membership_date`, `expiry_date`, `issue_limit`, `status`, `notes`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'LIB-STU-001', 'Student', 1, NULL, '9007963069', 'ahmed@gmail.com', '2026-08-16', '2027-03-16', 3, 1, NULL, 1, 1, '2026-08-16 10:31:53', '2026-08-16 10:31:53', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lost_damaged_books`
--

CREATE TABLE `lost_damaged_books` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED DEFAULT NULL,
  `book_issue_id` bigint(20) UNSIGNED NOT NULL,
  `book_copy_id` bigint(20) UNSIGNED NOT NULL,
  `library_member_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('Lost','Damaged') NOT NULL,
  `reported_date` date NOT NULL,
  `book_value` decimal(10,2) NOT NULL DEFAULT 0.00,
  `fine_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `description` text DEFAULT NULL,
  `status` enum('Reported','Resolved','Cancelled') NOT NULL DEFAULT 'Reported',
  `reported_by` bigint(20) UNSIGNED DEFAULT NULL,
  `resolved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
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
(37, '2026_08_09_144701_create_grades_table', 23),
(38, '2026_08_10_145831_create_result_grades_table', 24),
(39, '2026_08_11_162954_create_exam_ranks_table', 25),
(40, '2026_08_12_133615_create_fee_types_table', 26),
(41, '2026_08_12_133623_create_fee_structures_table', 27),
(42, '2026_08_12_133631_create_student_fee_dues_table', 28),
(43, '2026_08_12_133638_create_fee_payments_table', 29),
(44, '2026_08_12_133646_create_fee_payment_items_table', 30),
(45, '2026_08_13_150050_create_discounts_table', 31),
(46, '2026_08_13_151552_create_late_fees_table', 32),
(47, '2026_08_15_080552_create_expense_categories_table', 33),
(49, '2026_08_15_081746_create_expenses_table', 34),
(50, '2026_08_15_093319_create_expense_payments_table', 35),
(51, '2026_08_15_101454_create_recurring_expenses_table', 36),
(52, '2026_08_15_113747_create_income_categories_table', 37),
(53, '2026_08_15_121454_create_incomes_table', 38),
(54, '2026_08_15_133453_create_income_payments_table', 39),
(55, '2026_08_15_140013_create_recurring_incomes_table', 40),
(56, '2026_08_15_144646_create_book_categories_table', 41),
(57, '2026_08_15_150001_create_authors_table', 42),
(58, '2026_08_16_052559_create_publishers_table', 43),
(59, '2026_08_16_142119_create_books_table', 44),
(60, '2026_08_16_142342_create_book_authors_table', 45),
(61, '2026_08_16_142804_create_book_copies_table', 46),
(62, '2026_08_16_152641_create_library_members_table', 47),
(63, '2026_08_17_142817_create_book_issues_table', 48),
(65, '2026_08_17_152438_create_book_returns_table', 49),
(67, '2026_08_17_160111_create_book_renewals_table', 50),
(68, '2026_08_19_150906_create_lost_damaged_books_table', 51),
(69, '2026_08_20_015236_create_settings_table', 52),
(70, '2026_08_21_145132_create_inventory_categories_table', 53),
(71, '2026_08_21_145445_create_inventory_units_table', 54),
(73, '2026_08_24_152141_create_inventory_stock_ins_table', 56),
(74, '2026_08_24_152256_create_inventory_stock_in_items_table', 57),
(75, '2026_08_24_153936_create_inventory_purchase_items_table', 58),
(76, '2026_08_27_123855_add_code_description_status_to_inventory_units_table', 59),
(77, '2026_08_21_150843_create_inventory_suppliers_table', 60),
(78, '2026_08_21_151157_create_inventory_stock_movements_table', 61),
(79, '2026_08_28_151504_create_transport_vehicles_table', 62),
(80, '2026_08_28_154758_create_transport_drivers_table', 63),
(81, '2026_08_28_160119_create_transport_routes_table', 64),
(82, '2026_08_28_160122_create_transport_route_stops_table', 65),
(83, '2026_08_28_160727_create_transport_students_table', 66);

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
(8, 'App\\Models\\User', 1, 'admin', '562e6472e7df0940710a0b90901fdcbeec38b43bfd620f097cbaf4dd6f2f0a79', '[\"*\"]', '2026-08-21 09:13:41', NULL, '2026-08-21 08:24:54', '2026-08-21 09:13:41'),
(11, 'App\\Models\\User', 1, 'admin', '02fbc3f6580f19905bd4c116657f7b864cea2db18948aa4cafe8d3587eabe3cb', '[\"*\"]', '2026-08-28 10:36:14', NULL, '2026-08-27 06:34:54', '2026-08-28 10:36:14');

-- --------------------------------------------------------

--
-- Table structure for table `publishers`
--

CREATE TABLE `publishers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `publishers`
--

INSERT INTO `publishers` (`id`, `school_id`, `name`, `code`, `email`, `phone`, `address`, `website`, `description`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Imtiaz Alam Khan', 'PUB-001', 'imtiaz@gmail.com', '9007963069', NULL, NULL, NULL, 1, 1, 1, '2026-08-16 00:15:04', '2026-08-16 00:15:04', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `recurring_expenses`
--

CREATE TABLE `recurring_expenses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `expense_category_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `payee_name` varchar(255) NOT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `frequency` enum('Daily','Weekly','Monthly','Quarterly','Yearly') NOT NULL DEFAULT 'Monthly',
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `next_due_date` date NOT NULL,
  `payment_method` varchar(255) NOT NULL DEFAULT 'Cash',
  `description` text DEFAULT NULL,
  `auto_create` tinyint(1) NOT NULL DEFAULT 1,
  `status` enum('Active','Paused','Completed') NOT NULL DEFAULT 'Active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `recurring_expenses`
--

INSERT INTO `recurring_expenses` (`id`, `school_id`, `expense_category_id`, `name`, `payee_name`, `amount`, `frequency`, `start_date`, `end_date`, `next_due_date`, `payment_method`, `description`, `auto_create`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 'School', 'test', 5000.00, 'Monthly', '2026-08-15', NULL, '2026-09-15', 'Cash', NULL, 1, 'Active', 1, 1, '2026-08-15 05:15:08', '2026-08-15 05:15:46', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `recurring_incomes`
--

CREATE TABLE `recurring_incomes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `income_category_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `reference_no` varchar(255) DEFAULT NULL,
  `received_from` varchar(255) NOT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `payment_method` varchar(100) NOT NULL DEFAULT 'Cash',
  `frequency` enum('Daily','Weekly','Monthly','Quarterly','Yearly') NOT NULL DEFAULT 'Monthly',
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `next_date` date DEFAULT NULL,
  `auto_create` tinyint(1) NOT NULL DEFAULT 1,
  `status` enum('Active','Paused','Completed','Cancelled') NOT NULL DEFAULT 'Active',
  `description` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `recurring_incomes`
--

INSERT INTO `recurring_incomes` (`id`, `school_id`, `income_category_id`, `name`, `reference_no`, `received_from`, `amount`, `payment_method`, `frequency`, `start_date`, `end_date`, `next_date`, `auto_create`, `status`, `description`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'Ahmed', 'ref01', 'test', 59000.00, 'Cash', 'Monthly', '2026-08-15', NULL, '2026-09-15', 1, 'Active', NULL, 1, 1, '2026-08-15 08:51:48', '2026-08-15 08:51:48');

-- --------------------------------------------------------

--
-- Table structure for table `result_grades`
--

CREATE TABLE `result_grades` (
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
-- Dumping data for table `result_grades`
--

INSERT INTO `result_grades` (`id`, `school_id`, `name`, `min_percentage`, `max_percentage`, `grade_point`, `is_pass`, `description`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'A+', 90.00, 100.00, 10.00, 1, 'test description', 1, 1, NULL, '2026-08-11 06:17:38', '2026-08-11 06:17:38', NULL),
(2, 1, 'A', 70.00, 89.00, 8.00, 1, 'test', 1, 1, NULL, '2026-08-11 06:18:21', '2026-08-11 06:18:21', NULL),
(3, 1, 'B', 60.00, 79.00, 7.00, 1, 'test', 1, 1, NULL, '2026-08-11 06:19:01', '2026-08-11 06:19:01', NULL);

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
('FVxzElNOGu9nGFbjjiN39pKspGQAHVpRv6ISl2yF', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWkJBU1hmdGVvalZhMWJnS1dncGJrSnFRVlZET0ZabkI2ZVlCSUwzbCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9pbnZlbnRvcnkvc3RvY2staW5zIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1787846708),
('HMGAaWAQNGVOg7ptP4wPezLB77x2O9qA7tDyEjYx', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTWFIRmxNMk9zSlpCY21sbWVCV0J5ZEl6VUhvNGdrQ08xdUE3RFRYZSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC90cmFuc3BvcnQvdmVoaWNsZXMiO3M6NToicm91dGUiO047fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1787933173);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `group` varchar(255) NOT NULL DEFAULT 'general',
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `type` enum('string','text','integer','decimal','boolean','json') NOT NULL DEFAULT 'string',
  `description` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Table structure for table `student_fee_dues`
--

CREATE TABLE `student_fee_dues` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `academic_session_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `class_id` bigint(20) UNSIGNED NOT NULL,
  `section_id` bigint(20) UNSIGNED DEFAULT NULL,
  `fee_type_id` bigint(20) UNSIGNED NOT NULL,
  `fee_structure_id` bigint(20) UNSIGNED DEFAULT NULL,
  `fee_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `period` varchar(50) DEFAULT NULL COMMENT 'Example: June 2026',
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `late_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `net_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `paid_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `balance_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('Pending','Partial','Paid','Cancelled') NOT NULL DEFAULT 'Pending',
  `remarks` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `student_fee_dues`
--

INSERT INTO `student_fee_dues` (`id`, `school_id`, `academic_session_id`, `student_id`, `class_id`, `section_id`, `fee_type_id`, `fee_structure_id`, `fee_date`, `due_date`, `period`, `amount`, `discount`, `late_fee`, `net_amount`, `paid_amount`, `balance_amount`, `status`, `remarks`, `created_by`, `updated_by`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 1, 1, 1, 1, '2026-08-15', NULL, 'August 2026', 5000.00, 0.00, 500.00, 5500.00, 5500.00, 0.00, 'Paid', NULL, 1, 1, NULL, '2026-08-12 10:02:58', '2026-08-13 06:08:35');

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
-- Table structure for table `transport_drivers`
--

CREATE TABLE `transport_drivers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `license_no` varchar(255) DEFAULT NULL,
  `license_expiry` date DEFAULT NULL,
  `joining_date` date DEFAULT NULL,
  `address` text DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transport_routes`
--

CREATE TABLE `transport_routes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `start_location` varchar(255) DEFAULT NULL,
  `end_location` varchar(255) DEFAULT NULL,
  `distance` decimal(10,2) DEFAULT NULL,
  `travel_time` varchar(255) DEFAULT NULL,
  `fare` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` varchar(255) NOT NULL DEFAULT 'Active',
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transport_route_stops`
--

CREATE TABLE `transport_route_stops` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `transport_route_id` bigint(20) UNSIGNED NOT NULL,
  `stop_name` varchar(255) NOT NULL,
  `stop_code` varchar(255) DEFAULT NULL,
  `stop_order` int(11) NOT NULL DEFAULT 1,
  `pickup_time` time DEFAULT NULL,
  `drop_time` time DEFAULT NULL,
  `fare` decimal(12,2) NOT NULL DEFAULT 0.00,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transport_students`
--

CREATE TABLE `transport_students` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `transport_route_id` bigint(20) UNSIGNED NOT NULL,
  `transport_route_stop_id` bigint(20) UNSIGNED DEFAULT NULL,
  `transport_vehicle_id` bigint(20) UNSIGNED DEFAULT NULL,
  `transport_driver_id` bigint(20) UNSIGNED DEFAULT NULL,
  `academic_session` varchar(255) DEFAULT NULL,
  `pickup_type` varchar(255) NOT NULL DEFAULT 'Both',
  `transport_fee` decimal(12,2) NOT NULL DEFAULT 0.00,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Active',
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transport_vehicles`
--

CREATE TABLE `transport_vehicles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `vehicle_no` varchar(255) NOT NULL,
  `registration_no` varchar(255) DEFAULT NULL,
  `vehicle_type` varchar(255) DEFAULT NULL,
  `model` varchar(255) DEFAULT NULL,
  `capacity` int(10) UNSIGNED DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `insurance_expiry` date DEFAULT NULL,
  `fitness_expiry` date DEFAULT NULL,
  `pollution_expiry` date DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Active',
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transport_vehicles`
--

INSERT INTO `transport_vehicles` (`id`, `school_id`, `vehicle_no`, `registration_no`, `vehicle_type`, `model`, `capacity`, `purchase_date`, `insurance_expiry`, `fitness_expiry`, `pollution_expiry`, `status`, `remarks`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'BWB017880', 'WB01874655384', 'Bus', 'M001', NULL, '2026-08-28', '2040-08-29', '2040-08-28', '2050-08-28', 'Active', 'test', '2026-08-28 10:11:51', '2026-08-28 10:11:51', NULL);

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
-- Indexes for table `authors`
--
ALTER TABLE `authors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `authors_school_id_code_unique` (`school_id`,`code`),
  ADD KEY `authors_created_by_foreign` (`created_by`),
  ADD KEY `authors_updated_by_foreign` (`updated_by`),
  ADD KEY `authors_school_id_status_index` (`school_id`,`status`),
  ADD KEY `authors_school_id_name_index` (`school_id`,`name`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `books_school_id_book_code_unique` (`school_id`,`book_code`),
  ADD KEY `books_category_id_foreign` (`category_id`),
  ADD KEY `books_publisher_id_foreign` (`publisher_id`),
  ADD KEY `books_created_by_foreign` (`created_by`),
  ADD KEY `books_updated_by_foreign` (`updated_by`),
  ADD KEY `books_school_id_status_index` (`school_id`,`status`),
  ADD KEY `books_school_id_title_index` (`school_id`,`title`),
  ADD KEY `books_school_id_category_id_index` (`school_id`,`category_id`),
  ADD KEY `books_school_id_publisher_id_index` (`school_id`,`publisher_id`);

--
-- Indexes for table `book_authors`
--
ALTER TABLE `book_authors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `book_authors_book_id_author_id_unique` (`book_id`,`author_id`),
  ADD KEY `book_authors_author_id_index` (`author_id`);

--
-- Indexes for table `book_categories`
--
ALTER TABLE `book_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `book_categories_school_id_code_unique` (`school_id`,`code`),
  ADD KEY `book_categories_created_by_foreign` (`created_by`),
  ADD KEY `book_categories_updated_by_foreign` (`updated_by`),
  ADD KEY `book_categories_school_id_status_index` (`school_id`,`status`);

--
-- Indexes for table `book_copies`
--
ALTER TABLE `book_copies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `book_copies_school_id_accession_no_unique` (`school_id`,`accession_no`),
  ADD UNIQUE KEY `book_copies_school_id_barcode_unique` (`school_id`,`barcode`),
  ADD KEY `book_copies_book_id_foreign` (`book_id`),
  ADD KEY `book_copies_created_by_foreign` (`created_by`),
  ADD KEY `book_copies_updated_by_foreign` (`updated_by`),
  ADD KEY `book_copies_school_id_book_id_index` (`school_id`,`book_id`),
  ADD KEY `book_copies_school_id_status_index` (`school_id`,`status`),
  ADD KEY `book_copies_school_id_condition_index` (`school_id`,`condition`);

--
-- Indexes for table `book_issues`
--
ALTER TABLE `book_issues`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `book_issues_school_id_issue_no_unique` (`school_id`,`issue_no`),
  ADD KEY `book_issues_library_member_id_foreign` (`library_member_id`),
  ADD KEY `book_issues_book_id_foreign` (`book_id`),
  ADD KEY `book_issues_book_copy_id_foreign` (`book_copy_id`),
  ADD KEY `book_issues_issued_by_foreign` (`issued_by`),
  ADD KEY `book_issues_returned_by_foreign` (`returned_by`),
  ADD KEY `book_issues_school_id_library_member_id_index` (`school_id`,`library_member_id`),
  ADD KEY `book_issues_school_id_book_id_index` (`school_id`,`book_id`),
  ADD KEY `book_issues_school_id_book_copy_id_index` (`school_id`,`book_copy_id`),
  ADD KEY `book_issues_school_id_status_index` (`school_id`,`status`),
  ADD KEY `book_issues_school_id_due_date_index` (`school_id`,`due_date`);

--
-- Indexes for table `book_renewals`
--
ALTER TABLE `book_renewals`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `book_renewals_school_id_renewal_no_unique` (`school_id`,`renewal_no`),
  ADD KEY `book_renewals_book_issue_id_foreign` (`book_issue_id`),
  ADD KEY `book_renewals_library_member_id_foreign` (`library_member_id`),
  ADD KEY `book_renewals_book_id_foreign` (`book_id`),
  ADD KEY `book_renewals_book_copy_id_foreign` (`book_copy_id`),
  ADD KEY `book_renewals_renewed_by_foreign` (`renewed_by`),
  ADD KEY `book_renewals_school_id_book_issue_id_index` (`school_id`,`book_issue_id`),
  ADD KEY `book_renewals_school_id_library_member_id_index` (`school_id`,`library_member_id`),
  ADD KEY `book_renewals_school_id_book_id_index` (`school_id`,`book_id`),
  ADD KEY `book_renewals_school_id_book_copy_id_index` (`school_id`,`book_copy_id`),
  ADD KEY `book_renewals_school_id_renewal_date_index` (`school_id`,`renewal_date`);

--
-- Indexes for table `book_returns`
--
ALTER TABLE `book_returns`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `book_returns_school_id_return_no_unique` (`school_id`,`return_no`),
  ADD UNIQUE KEY `book_returns_book_issue_id_unique` (`book_issue_id`),
  ADD KEY `book_returns_library_member_id_foreign` (`library_member_id`),
  ADD KEY `book_returns_book_id_foreign` (`book_id`),
  ADD KEY `book_returns_book_copy_id_foreign` (`book_copy_id`),
  ADD KEY `book_returns_returned_by_foreign` (`returned_by`),
  ADD KEY `book_returns_school_id_library_member_id_index` (`school_id`,`library_member_id`),
  ADD KEY `book_returns_school_id_book_id_index` (`school_id`,`book_id`),
  ADD KEY `book_returns_school_id_book_copy_id_index` (`school_id`,`book_copy_id`),
  ADD KEY `book_returns_school_id_return_date_index` (`school_id`,`return_date`),
  ADD KEY `book_returns_school_id_fine_status_index` (`school_id`,`fine_status`),
  ADD KEY `book_returns_school_id_book_condition_index` (`school_id`,`book_condition`);

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
-- Indexes for table `discounts`
--
ALTER TABLE `discounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `discounts_school_id_status_index` (`school_id`,`status`),
  ADD KEY `discounts_school_id_code_index` (`school_id`,`code`);

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
-- Indexes for table `exam_ranks`
--
ALTER TABLE `exam_ranks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `exam_rank_unique` (`exam_id`,`class_id`,`section_id`,`student_id`),
  ADD KEY `exam_ranks_school_id_foreign` (`school_id`),
  ADD KEY `exam_ranks_academic_session_id_foreign` (`academic_session_id`),
  ADD KEY `exam_ranks_class_id_foreign` (`class_id`),
  ADD KEY `exam_ranks_section_id_foreign` (`section_id`),
  ADD KEY `exam_ranks_student_id_foreign` (`student_id`);

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
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `expenses_expense_category_id_foreign` (`expense_category_id`),
  ADD KEY `expenses_school_id_expense_date_index` (`school_id`,`expense_date`),
  ADD KEY `expenses_school_id_expense_category_id_index` (`school_id`,`expense_category_id`),
  ADD KEY `expenses_school_id_status_index` (`school_id`,`status`),
  ADD KEY `expenses_school_id_payment_method_index` (`school_id`,`payment_method`);

--
-- Indexes for table `expense_categories`
--
ALTER TABLE `expense_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `expense_categories_school_id_status_index` (`school_id`,`status`),
  ADD KEY `expense_categories_school_id_code_index` (`school_id`,`code`);

--
-- Indexes for table `expense_payments`
--
ALTER TABLE `expense_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `expense_payments_expense_id_foreign` (`expense_id`),
  ADD KEY `expense_payments_school_id_expense_id_index` (`school_id`,`expense_id`),
  ADD KEY `expense_payments_school_id_payment_date_index` (`school_id`,`payment_date`),
  ADD KEY `expense_payments_school_id_status_index` (`school_id`,`status`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `fee_payments`
--
ALTER TABLE `fee_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fee_payments_school_id_receipt_no_unique` (`school_id`,`receipt_no`),
  ADD KEY `fee_payments_academic_session_id_foreign` (`academic_session_id`),
  ADD KEY `fee_payments_student_id_foreign` (`student_id`),
  ADD KEY `fee_payments_created_by_foreign` (`created_by`),
  ADD KEY `fee_payments_updated_by_foreign` (`updated_by`),
  ADD KEY `fee_payments_school_id_student_id_payment_date_index` (`school_id`,`student_id`,`payment_date`);

--
-- Indexes for table `fee_payment_items`
--
ALTER TABLE `fee_payment_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fee_payment_items_student_fee_due_id_foreign` (`student_fee_due_id`),
  ADD KEY `fee_payment_items_fee_payment_id_student_fee_due_id_index` (`fee_payment_id`,`student_fee_due_id`);

--
-- Indexes for table `fee_structures`
--
ALTER TABLE `fee_structures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fee_structures_academic_session_id_foreign` (`academic_session_id`),
  ADD KEY `fee_structures_class_id_foreign` (`class_id`),
  ADD KEY `fee_structures_fee_type_id_foreign` (`fee_type_id`),
  ADD KEY `fee_structures_created_by_foreign` (`created_by`),
  ADD KEY `fee_structures_updated_by_foreign` (`updated_by`),
  ADD KEY `fee_structures_school_id_academic_session_id_class_id_index` (`school_id`,`academic_session_id`,`class_id`);

--
-- Indexes for table `fee_types`
--
ALTER TABLE `fee_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fee_types_created_by_foreign` (`created_by`),
  ADD KEY `fee_types_updated_by_foreign` (`updated_by`),
  ADD KEY `fee_types_school_id_status_index` (`school_id`,`status`);

--
-- Indexes for table `fine_collections`
--
ALTER TABLE `fine_collections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fine_collections_school_id_foreign` (`school_id`);

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
-- Indexes for table `incomes`
--
ALTER TABLE `incomes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `incomes_income_category_id_foreign` (`income_category_id`),
  ADD KEY `incomes_created_by_foreign` (`created_by`),
  ADD KEY `incomes_updated_by_foreign` (`updated_by`),
  ADD KEY `incomes_school_id_income_category_id_index` (`school_id`,`income_category_id`),
  ADD KEY `incomes_school_id_income_date_index` (`school_id`,`income_date`),
  ADD KEY `incomes_school_id_status_index` (`school_id`,`status`);

--
-- Indexes for table `income_categories`
--
ALTER TABLE `income_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `income_categories_created_by_foreign` (`created_by`),
  ADD KEY `income_categories_updated_by_foreign` (`updated_by`),
  ADD KEY `income_categories_school_id_status_index` (`school_id`,`status`);

--
-- Indexes for table `income_payments`
--
ALTER TABLE `income_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `income_payments_school_id_receipt_no_unique` (`school_id`,`receipt_no`),
  ADD KEY `income_payments_income_id_foreign` (`income_id`),
  ADD KEY `income_payments_created_by_foreign` (`created_by`),
  ADD KEY `income_payments_updated_by_foreign` (`updated_by`),
  ADD KEY `income_payments_school_id_income_id_index` (`school_id`,`income_id`),
  ADD KEY `income_payments_school_id_payment_date_index` (`school_id`,`payment_date`),
  ADD KEY `income_payments_school_id_status_index` (`school_id`,`status`);

--
-- Indexes for table `inventory_categories`
--
ALTER TABLE `inventory_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventory_categories_school_id_index` (`school_id`);

--
-- Indexes for table `inventory_items`
--
ALTER TABLE `inventory_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventory_items_school_id_index` (`school_id`),
  ADD KEY `inventory_items_inventory_category_id_index` (`inventory_category_id`),
  ADD KEY `inventory_items_inventory_unit_id_index` (`inventory_unit_id`),
  ADD KEY `inventory_items_inventory_supplier_id_index` (`inventory_supplier_id`);

--
-- Indexes for table `inventory_purchases`
--
ALTER TABLE `inventory_purchases`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `inventory_purchases_school_id_purchase_no_unique` (`school_id`,`purchase_no`),
  ADD KEY `inventory_purchases_school_id_index` (`school_id`),
  ADD KEY `inventory_purchases_inventory_supplier_id_index` (`inventory_supplier_id`);

--
-- Indexes for table `inventory_purchase_items`
--
ALTER TABLE `inventory_purchase_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventory_purchase_items_school_id_index` (`school_id`),
  ADD KEY `inventory_purchase_items_inventory_purchase_id_index` (`inventory_purchase_id`),
  ADD KEY `inventory_purchase_items_inventory_item_id_index` (`inventory_item_id`);

--
-- Indexes for table `inventory_stock_ins`
--
ALTER TABLE `inventory_stock_ins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `inventory_stock_ins_school_id_stock_in_no_unique` (`school_id`,`stock_in_no`),
  ADD KEY `inventory_stock_ins_school_id_index` (`school_id`),
  ADD KEY `inventory_stock_ins_inventory_supplier_id_index` (`inventory_supplier_id`),
  ADD KEY `inventory_stock_ins_stock_in_date_index` (`stock_in_date`),
  ADD KEY `inventory_stock_ins_created_by_foreign` (`created_by`);

--
-- Indexes for table `inventory_stock_in_items`
--
ALTER TABLE `inventory_stock_in_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventory_stock_in_items_school_id_index` (`school_id`),
  ADD KEY `inventory_stock_in_items_inventory_stock_in_id_index` (`inventory_stock_in_id`),
  ADD KEY `inventory_stock_in_items_inventory_item_id_index` (`inventory_item_id`);

--
-- Indexes for table `inventory_stock_movements`
--
ALTER TABLE `inventory_stock_movements`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `inventory_stock_movements_school_id_movement_no_unique` (`school_id`,`movement_no`),
  ADD KEY `inventory_stock_movements_school_id_index` (`school_id`),
  ADD KEY `inventory_stock_movements_inventory_item_id_index` (`inventory_item_id`),
  ADD KEY `inventory_stock_movements_movement_type_index` (`movement_type`),
  ADD KEY `inventory_stock_movements_movement_date_index` (`movement_date`),
  ADD KEY `inventory_stock_movements_reference_id_index` (`reference_id`),
  ADD KEY `inventory_stock_movements_created_by_foreign` (`created_by`);

--
-- Indexes for table `inventory_stock_outs`
--
ALTER TABLE `inventory_stock_outs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventory_stock_outs_school_id_index` (`school_id`),
  ADD KEY `inventory_stock_outs_stock_out_date_index` (`stock_out_date`),
  ADD KEY `inventory_stock_outs_created_by_index` (`created_by`);

--
-- Indexes for table `inventory_stock_out_items`
--
ALTER TABLE `inventory_stock_out_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventory_stock_out_items_school_id_index` (`school_id`),
  ADD KEY `inventory_stock_out_items_stock_out_id_index` (`inventory_stock_out_id`),
  ADD KEY `inventory_stock_out_items_item_id_index` (`inventory_item_id`);

--
-- Indexes for table `inventory_suppliers`
--
ALTER TABLE `inventory_suppliers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventory_suppliers_school_id_index` (`school_id`),
  ADD KEY `inventory_suppliers_school_id_name_index` (`school_id`,`name`),
  ADD KEY `inventory_suppliers_school_id_code_index` (`school_id`,`code`);

--
-- Indexes for table `inventory_units`
--
ALTER TABLE `inventory_units`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventory_units_school_id_index` (`school_id`);

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
-- Indexes for table `late_fees`
--
ALTER TABLE `late_fees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `late_fees_school_id_status_index` (`school_id`,`status`),
  ADD KEY `late_fees_school_id_code_index` (`school_id`,`code`);

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
-- Indexes for table `library_fines`
--
ALTER TABLE `library_fines`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `library_fines_school_id_fine_no_unique` (`school_id`,`fine_no`),
  ADD KEY `library_fines_library_member_id_index` (`library_member_id`),
  ADD KEY `library_fines_book_issue_id_index` (`book_issue_id`),
  ADD KEY `library_fines_book_id_index` (`book_id`),
  ADD KEY `library_fines_book_copy_id_index` (`book_copy_id`),
  ADD KEY `library_fines_lost_damaged_book_id_index` (`lost_damaged_book_id`),
  ADD KEY `library_fines_created_by_index` (`created_by`);

--
-- Indexes for table `library_members`
--
ALTER TABLE `library_members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `library_members_school_id_member_no_unique` (`school_id`,`member_no`),
  ADD KEY `library_members_created_by_foreign` (`created_by`),
  ADD KEY `library_members_updated_by_foreign` (`updated_by`),
  ADD KEY `library_members_school_id_member_type_index` (`school_id`,`member_type`),
  ADD KEY `library_members_school_id_student_id_index` (`school_id`,`student_id`),
  ADD KEY `library_members_school_id_staff_id_index` (`school_id`,`staff_id`),
  ADD KEY `library_members_school_id_status_index` (`school_id`,`status`);

--
-- Indexes for table `lost_damaged_books`
--
ALTER TABLE `lost_damaged_books`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lost_damaged_books_school_id_index` (`school_id`),
  ADD KEY `lost_damaged_books_book_issue_id_index` (`book_issue_id`),
  ADD KEY `lost_damaged_books_book_copy_id_index` (`book_copy_id`),
  ADD KEY `lost_damaged_books_library_member_id_index` (`library_member_id`),
  ADD KEY `lost_damaged_books_status_index` (`status`);

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
-- Indexes for table `publishers`
--
ALTER TABLE `publishers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `publishers_school_id_code_unique` (`school_id`,`code`),
  ADD KEY `publishers_created_by_foreign` (`created_by`),
  ADD KEY `publishers_updated_by_foreign` (`updated_by`),
  ADD KEY `publishers_school_id_status_index` (`school_id`,`status`),
  ADD KEY `publishers_school_id_name_index` (`school_id`,`name`);

--
-- Indexes for table `recurring_expenses`
--
ALTER TABLE `recurring_expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recurring_expenses_expense_category_id_foreign` (`expense_category_id`),
  ADD KEY `recurring_expenses_school_id_next_due_date_index` (`school_id`,`next_due_date`),
  ADD KEY `recurring_expenses_school_id_status_index` (`school_id`,`status`),
  ADD KEY `recurring_expenses_school_id_expense_category_id_index` (`school_id`,`expense_category_id`);

--
-- Indexes for table `recurring_incomes`
--
ALTER TABLE `recurring_incomes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recurring_incomes_income_category_id_foreign` (`income_category_id`),
  ADD KEY `recurring_incomes_created_by_foreign` (`created_by`),
  ADD KEY `recurring_incomes_updated_by_foreign` (`updated_by`),
  ADD KEY `recurring_incomes_school_id_status_index` (`school_id`,`status`),
  ADD KEY `recurring_incomes_school_id_next_date_index` (`school_id`,`next_date`),
  ADD KEY `recurring_incomes_school_id_frequency_index` (`school_id`,`frequency`);

--
-- Indexes for table `result_grades`
--
ALTER TABLE `result_grades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `result_grades_created_by_foreign` (`created_by`),
  ADD KEY `result_grades_updated_by_foreign` (`updated_by`),
  ADD KEY `result_grades_school_id_status_index` (`school_id`,`status`);

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
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_school_id_group_key_unique` (`school_id`,`group`,`key`),
  ADD KEY `settings_school_id_group_index` (`school_id`,`group`),
  ADD KEY `settings_created_by_foreign` (`created_by`),
  ADD KEY `settings_updated_by_foreign` (`updated_by`);

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
-- Indexes for table `student_fee_dues`
--
ALTER TABLE `student_fee_dues`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_fee_dues_academic_session_id_foreign` (`academic_session_id`),
  ADD KEY `student_fee_dues_class_id_foreign` (`class_id`),
  ADD KEY `student_fee_dues_section_id_foreign` (`section_id`),
  ADD KEY `student_fee_dues_fee_type_id_foreign` (`fee_type_id`),
  ADD KEY `student_fee_dues_fee_structure_id_foreign` (`fee_structure_id`),
  ADD KEY `student_fee_dues_created_by_foreign` (`created_by`),
  ADD KEY `student_fee_dues_updated_by_foreign` (`updated_by`),
  ADD KEY `student_fee_dues_school_id_academic_session_id_student_id_index` (`school_id`,`academic_session_id`,`student_id`),
  ADD KEY `student_fee_dues_student_id_status_index` (`student_id`,`status`);

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
-- Indexes for table `transport_drivers`
--
ALTER TABLE `transport_drivers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transport_drivers_school_id_index` (`school_id`),
  ADD KEY `transport_drivers_license_no_index` (`license_no`),
  ADD KEY `transport_drivers_status_index` (`status`);

--
-- Indexes for table `transport_routes`
--
ALTER TABLE `transport_routes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transport_routes_school_id_index` (`school_id`),
  ADD KEY `transport_routes_status_index` (`status`);

--
-- Indexes for table `transport_route_stops`
--
ALTER TABLE `transport_route_stops`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transport_route_stops_school_id_index` (`school_id`),
  ADD KEY `transport_route_stops_transport_route_id_index` (`transport_route_id`);

--
-- Indexes for table `transport_students`
--
ALTER TABLE `transport_students`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transport_students_school_id_index` (`school_id`),
  ADD KEY `transport_students_student_id_index` (`student_id`),
  ADD KEY `transport_students_transport_route_id_index` (`transport_route_id`),
  ADD KEY `transport_students_transport_route_stop_id_index` (`transport_route_stop_id`),
  ADD KEY `transport_students_transport_vehicle_id_index` (`transport_vehicle_id`),
  ADD KEY `transport_students_transport_driver_id_index` (`transport_driver_id`),
  ADD KEY `transport_students_status_index` (`status`);

--
-- Indexes for table `transport_vehicles`
--
ALTER TABLE `transport_vehicles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transport_vehicles_school_id_index` (`school_id`),
  ADD KEY `transport_vehicles_registration_no_index` (`registration_no`),
  ADD KEY `transport_vehicles_status_index` (`status`);

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
-- AUTO_INCREMENT for table `authors`
--
ALTER TABLE `authors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `book_authors`
--
ALTER TABLE `book_authors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `book_categories`
--
ALTER TABLE `book_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `book_copies`
--
ALTER TABLE `book_copies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `book_issues`
--
ALTER TABLE `book_issues`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `book_renewals`
--
ALTER TABLE `book_renewals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `book_returns`
--
ALTER TABLE `book_returns`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `discounts`
--
ALTER TABLE `discounts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `exam_marks`
--
ALTER TABLE `exam_marks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `exam_ranks`
--
ALTER TABLE `exam_ranks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `expense_categories`
--
ALTER TABLE `expense_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `expense_payments`
--
ALTER TABLE `expense_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fee_payments`
--
ALTER TABLE `fee_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `fee_payment_items`
--
ALTER TABLE `fee_payment_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `fee_structures`
--
ALTER TABLE `fee_structures`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `fee_types`
--
ALTER TABLE `fee_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `fine_collections`
--
ALTER TABLE `fine_collections`
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
-- AUTO_INCREMENT for table `incomes`
--
ALTER TABLE `incomes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `income_categories`
--
ALTER TABLE `income_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `income_payments`
--
ALTER TABLE `income_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `inventory_categories`
--
ALTER TABLE `inventory_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `inventory_items`
--
ALTER TABLE `inventory_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `inventory_purchases`
--
ALTER TABLE `inventory_purchases`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `inventory_purchase_items`
--
ALTER TABLE `inventory_purchase_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `inventory_stock_ins`
--
ALTER TABLE `inventory_stock_ins`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `inventory_stock_in_items`
--
ALTER TABLE `inventory_stock_in_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `inventory_stock_movements`
--
ALTER TABLE `inventory_stock_movements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `inventory_stock_outs`
--
ALTER TABLE `inventory_stock_outs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_stock_out_items`
--
ALTER TABLE `inventory_stock_out_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_suppliers`
--
ALTER TABLE `inventory_suppliers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `inventory_units`
--
ALTER TABLE `inventory_units`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `late_fees`
--
ALTER TABLE `late_fees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
-- AUTO_INCREMENT for table `library_fines`
--
ALTER TABLE `library_fines`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `library_members`
--
ALTER TABLE `library_members`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `lost_damaged_books`
--
ALTER TABLE `lost_damaged_books`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `publishers`
--
ALTER TABLE `publishers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `recurring_expenses`
--
ALTER TABLE `recurring_expenses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `recurring_incomes`
--
ALTER TABLE `recurring_incomes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `result_grades`
--
ALTER TABLE `result_grades`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

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
-- AUTO_INCREMENT for table `student_fee_dues`
--
ALTER TABLE `student_fee_dues`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
-- AUTO_INCREMENT for table `transport_drivers`
--
ALTER TABLE `transport_drivers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transport_routes`
--
ALTER TABLE `transport_routes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transport_route_stops`
--
ALTER TABLE `transport_route_stops`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transport_students`
--
ALTER TABLE `transport_students`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transport_vehicles`
--
ALTER TABLE `transport_vehicles`
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
-- Constraints for table `authors`
--
ALTER TABLE `authors`
  ADD CONSTRAINT `authors_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `authors_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `authors_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `book_categories` (`id`),
  ADD CONSTRAINT `books_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `books_publisher_id_foreign` FOREIGN KEY (`publisher_id`) REFERENCES `publishers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `books_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `books_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `book_authors`
--
ALTER TABLE `book_authors`
  ADD CONSTRAINT `book_authors_author_id_foreign` FOREIGN KEY (`author_id`) REFERENCES `authors` (`id`),
  ADD CONSTRAINT `book_authors_book_id_foreign` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `book_categories`
--
ALTER TABLE `book_categories`
  ADD CONSTRAINT `book_categories_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `book_categories_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `book_categories_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `book_copies`
--
ALTER TABLE `book_copies`
  ADD CONSTRAINT `book_copies_book_id_foreign` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `book_copies_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `book_copies_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `book_copies_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `book_issues`
--
ALTER TABLE `book_issues`
  ADD CONSTRAINT `book_issues_book_copy_id_foreign` FOREIGN KEY (`book_copy_id`) REFERENCES `book_copies` (`id`),
  ADD CONSTRAINT `book_issues_book_id_foreign` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`),
  ADD CONSTRAINT `book_issues_issued_by_foreign` FOREIGN KEY (`issued_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `book_issues_library_member_id_foreign` FOREIGN KEY (`library_member_id`) REFERENCES `library_members` (`id`),
  ADD CONSTRAINT `book_issues_returned_by_foreign` FOREIGN KEY (`returned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `book_issues_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `book_renewals`
--
ALTER TABLE `book_renewals`
  ADD CONSTRAINT `book_renewals_book_copy_id_foreign` FOREIGN KEY (`book_copy_id`) REFERENCES `book_copies` (`id`),
  ADD CONSTRAINT `book_renewals_book_id_foreign` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`),
  ADD CONSTRAINT `book_renewals_book_issue_id_foreign` FOREIGN KEY (`book_issue_id`) REFERENCES `book_issues` (`id`),
  ADD CONSTRAINT `book_renewals_library_member_id_foreign` FOREIGN KEY (`library_member_id`) REFERENCES `library_members` (`id`),
  ADD CONSTRAINT `book_renewals_renewed_by_foreign` FOREIGN KEY (`renewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `book_renewals_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `book_returns`
--
ALTER TABLE `book_returns`
  ADD CONSTRAINT `book_returns_book_copy_id_foreign` FOREIGN KEY (`book_copy_id`) REFERENCES `book_copies` (`id`),
  ADD CONSTRAINT `book_returns_book_id_foreign` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`),
  ADD CONSTRAINT `book_returns_book_issue_id_foreign` FOREIGN KEY (`book_issue_id`) REFERENCES `book_issues` (`id`),
  ADD CONSTRAINT `book_returns_library_member_id_foreign` FOREIGN KEY (`library_member_id`) REFERENCES `library_members` (`id`),
  ADD CONSTRAINT `book_returns_returned_by_foreign` FOREIGN KEY (`returned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `book_returns_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `classes_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `discounts`
--
ALTER TABLE `discounts`
  ADD CONSTRAINT `discounts_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE;

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
-- Constraints for table `exam_ranks`
--
ALTER TABLE `exam_ranks`
  ADD CONSTRAINT `exam_ranks_academic_session_id_foreign` FOREIGN KEY (`academic_session_id`) REFERENCES `academic_sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_ranks_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_ranks_exam_id_foreign` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_ranks_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_ranks_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_ranks_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

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
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_expense_category_id_foreign` FOREIGN KEY (`expense_category_id`) REFERENCES `expense_categories` (`id`),
  ADD CONSTRAINT `expenses_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `expense_categories`
--
ALTER TABLE `expense_categories`
  ADD CONSTRAINT `expense_categories_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `expense_payments`
--
ALTER TABLE `expense_payments`
  ADD CONSTRAINT `expense_payments_expense_id_foreign` FOREIGN KEY (`expense_id`) REFERENCES `expenses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `expense_payments_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `fee_payments`
--
ALTER TABLE `fee_payments`
  ADD CONSTRAINT `fee_payments_academic_session_id_foreign` FOREIGN KEY (`academic_session_id`) REFERENCES `academic_sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fee_payments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fee_payments_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fee_payments_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fee_payments_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `fee_payment_items`
--
ALTER TABLE `fee_payment_items`
  ADD CONSTRAINT `fee_payment_items_fee_payment_id_foreign` FOREIGN KEY (`fee_payment_id`) REFERENCES `fee_payments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fee_payment_items_student_fee_due_id_foreign` FOREIGN KEY (`student_fee_due_id`) REFERENCES `student_fee_dues` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `fee_structures`
--
ALTER TABLE `fee_structures`
  ADD CONSTRAINT `fee_structures_academic_session_id_foreign` FOREIGN KEY (`academic_session_id`) REFERENCES `academic_sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fee_structures_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fee_structures_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fee_structures_fee_type_id_foreign` FOREIGN KEY (`fee_type_id`) REFERENCES `fee_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fee_structures_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fee_structures_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `fee_types`
--
ALTER TABLE `fee_types`
  ADD CONSTRAINT `fee_types_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fee_types_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fee_types_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `fine_collections`
--
ALTER TABLE `fine_collections`
  ADD CONSTRAINT `fine_collections_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE;

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
-- Constraints for table `incomes`
--
ALTER TABLE `incomes`
  ADD CONSTRAINT `incomes_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `incomes_income_category_id_foreign` FOREIGN KEY (`income_category_id`) REFERENCES `income_categories` (`id`),
  ADD CONSTRAINT `incomes_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `incomes_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `income_categories`
--
ALTER TABLE `income_categories`
  ADD CONSTRAINT `income_categories_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `income_categories_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `income_categories_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `income_payments`
--
ALTER TABLE `income_payments`
  ADD CONSTRAINT `income_payments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `income_payments_income_id_foreign` FOREIGN KEY (`income_id`) REFERENCES `incomes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `income_payments_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `income_payments_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `inventory_categories`
--
ALTER TABLE `inventory_categories`
  ADD CONSTRAINT `inventory_categories_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inventory_items`
--
ALTER TABLE `inventory_items`
  ADD CONSTRAINT `inventory_items_inventory_category_id_foreign` FOREIGN KEY (`inventory_category_id`) REFERENCES `inventory_categories` (`id`),
  ADD CONSTRAINT `inventory_items_inventory_unit_id_foreign` FOREIGN KEY (`inventory_unit_id`) REFERENCES `inventory_units` (`id`),
  ADD CONSTRAINT `inventory_items_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inventory_purchases`
--
ALTER TABLE `inventory_purchases`
  ADD CONSTRAINT `inventory_purchases_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inventory_purchase_items`
--
ALTER TABLE `inventory_purchase_items`
  ADD CONSTRAINT `inventory_purchase_items_inventory_item_id_foreign` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`),
  ADD CONSTRAINT `inventory_purchase_items_inventory_purchase_id_foreign` FOREIGN KEY (`inventory_purchase_id`) REFERENCES `inventory_purchases` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inventory_purchase_items_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inventory_stock_ins`
--
ALTER TABLE `inventory_stock_ins`
  ADD CONSTRAINT `inventory_stock_ins_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inventory_stock_ins_inventory_supplier_id_foreign` FOREIGN KEY (`inventory_supplier_id`) REFERENCES `inventory_suppliers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inventory_stock_ins_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inventory_stock_in_items`
--
ALTER TABLE `inventory_stock_in_items`
  ADD CONSTRAINT `inventory_stock_in_items_inventory_item_id_foreign` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`),
  ADD CONSTRAINT `inventory_stock_in_items_inventory_stock_in_id_foreign` FOREIGN KEY (`inventory_stock_in_id`) REFERENCES `inventory_stock_ins` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inventory_stock_in_items_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inventory_stock_movements`
--
ALTER TABLE `inventory_stock_movements`
  ADD CONSTRAINT `inventory_stock_movements_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inventory_stock_movements_inventory_item_id_foreign` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`),
  ADD CONSTRAINT `inventory_stock_movements_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inventory_suppliers`
--
ALTER TABLE `inventory_suppliers`
  ADD CONSTRAINT `inventory_suppliers_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inventory_units`
--
ALTER TABLE `inventory_units`
  ADD CONSTRAINT `inventory_units_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `late_fees`
--
ALTER TABLE `late_fees`
  ADD CONSTRAINT `late_fees_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE;

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
-- Constraints for table `library_fines`
--
ALTER TABLE `library_fines`
  ADD CONSTRAINT `library_fines_book_copy_id_foreign` FOREIGN KEY (`book_copy_id`) REFERENCES `book_copies` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `library_fines_book_id_foreign` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `library_fines_book_issue_id_foreign` FOREIGN KEY (`book_issue_id`) REFERENCES `book_issues` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `library_fines_library_member_id_foreign` FOREIGN KEY (`library_member_id`) REFERENCES `library_members` (`id`),
  ADD CONSTRAINT `library_fines_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `library_members`
--
ALTER TABLE `library_members`
  ADD CONSTRAINT `library_members_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `library_members_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `library_members_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

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
-- Constraints for table `publishers`
--
ALTER TABLE `publishers`
  ADD CONSTRAINT `publishers_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `publishers_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `publishers_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `recurring_expenses`
--
ALTER TABLE `recurring_expenses`
  ADD CONSTRAINT `recurring_expenses_expense_category_id_foreign` FOREIGN KEY (`expense_category_id`) REFERENCES `expense_categories` (`id`),
  ADD CONSTRAINT `recurring_expenses_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `recurring_incomes`
--
ALTER TABLE `recurring_incomes`
  ADD CONSTRAINT `recurring_incomes_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `recurring_incomes_income_category_id_foreign` FOREIGN KEY (`income_category_id`) REFERENCES `income_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `recurring_incomes_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `recurring_incomes_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `result_grades`
--
ALTER TABLE `result_grades`
  ADD CONSTRAINT `result_grades_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `result_grades_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `result_grades_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

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
-- Constraints for table `settings`
--
ALTER TABLE `settings`
  ADD CONSTRAINT `settings_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `settings_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `settings_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

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
-- Constraints for table `student_fee_dues`
--
ALTER TABLE `student_fee_dues`
  ADD CONSTRAINT `student_fee_dues_academic_session_id_foreign` FOREIGN KEY (`academic_session_id`) REFERENCES `academic_sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_fee_dues_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_fee_dues_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `student_fee_dues_fee_structure_id_foreign` FOREIGN KEY (`fee_structure_id`) REFERENCES `fee_structures` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `student_fee_dues_fee_type_id_foreign` FOREIGN KEY (`fee_type_id`) REFERENCES `fee_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_fee_dues_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_fee_dues_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `student_fee_dues_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_fee_dues_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

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
