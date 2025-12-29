-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 29, 2025 at 03:22 AM
-- Server version: 8.0.30
-- PHP Version: 8.2.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `simpeg`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('simantap-cache-livewire-rate-limiter:056fc329aaaa757d31db450f525da23fde4d1b36', 'i:2;', 1766978391),
('simantap-cache-livewire-rate-limiter:056fc329aaaa757d31db450f525da23fde4d1b36:timer', 'i:1766978391;', 1766978391),
('simantap-cache-setting_ip', 's:6:\"182.13\";', 1766981862),
('simantap-cache-setting_max_leave_days', 'i:12;', 1766981840),
('simantap-cache-setting_max_permission_days', 'i:6;', 1766981840);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chairs`
--

CREATE TABLE `chairs` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` tinyint UNSIGNED NOT NULL,
  `head_id` tinyint UNSIGNED DEFAULT NULL,
  `unit_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chairs`
--

INSERT INTO `chairs` (`id`, `name`, `level`, `head_id`, `unit_id`, `created_at`, `updated_at`) VALUES
(1, 'Direktur', 1, NULL, 1, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(2, 'Kepala Seksi Pelayanan & Keperawatan', 2, 1, 1, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(3, 'Kepala Seksi Mutu & Data Informasi', 2, 1, 1, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(4, 'Kepala Seksi Pelayanan & Sarana Penunjang', 2, 1, 1, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(5, 'Kepala Sub Bagian Tata Usaha', 2, 1, 1, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(6, 'Koordinator Pengembangan Mutu', 3, 3, 1, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(7, 'Koordinator Keperawatan', 3, 2, 1, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(8, 'Koordinator Pelayanan Penunjang', 3, 4, 1, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(9, 'Koordinator RM & Casemix', 3, 3, 1, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(10, 'Koordinator Pelayanan Medis', 3, 2, 1, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(11, 'Koordinator Keuangan & Akuntansi', 3, 5, 1, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(12, 'Koordinator Sarana Pelayanan', 3, 4, 1, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(13, 'Koordinator Umum & Kepegawaian', 3, 5, 1, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(14, 'Koordiantor Perencanaan, Evaluasi & Pelaporan', 3, 5, 1, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(15, 'Koordinator Data dan Informasi', 3, 3, 1, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(16, 'Koor HD', 4, 7, 2, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(17, 'Perawat HD', 4, 7, 2, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(18, 'Koor OK', 4, 7, 3, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(19, 'Perawat OK', 4, 7, 3, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(20, 'Koor Poliklinik', 4, 7, 4, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(21, 'Perawat Poliklinik', 4, 7, 4, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(22, 'Koor Bima', 4, 7, 5, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(23, 'Perawat Bima', 4, 7, 5, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(24, 'Koor Shinta', 4, 7, 6, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(25, 'Perawat Shinta', 4, 7, 6, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(26, 'Koor Rama', 4, 7, 7, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(27, 'Perawat Rama', 4, 7, 7, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(28, 'Koor ICU', 4, 7, 8, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(29, 'Perawat ICU', 4, 7, 8, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(30, 'Koor UGD', 4, 7, 9, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(31, 'Perawat UGD', 4, 7, 9, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(32, 'Perawat Anestesi', 4, 7, 10, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(33, 'Koor Bidan', 4, 7, 10, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(34, 'Pelaksana Bidan', 4, 7, 10, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(35, 'Koor Farmasi', 4, 8, 11, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(36, 'Apoteker', 4, 8, 11, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(37, 'Tenaga Teknis Farmasi', 4, 8, 11, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(38, 'Administrasi Farmasi', 4, 8, 11, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(39, 'Koor Laboratorium', 4, 8, 12, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(40, 'Pelaksana Laboratorium', 4, 8, 12, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(41, 'Koor Radiologi', 4, 8, 13, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(42, 'Radiografer', 4, 8, 13, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(43, 'Koor Pendaftaran', 4, 9, 14, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(44, 'Pelaksana Pendaftaran', 4, 9, 14, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(45, 'Koor RM', 4, 9, 15, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(46, 'Pelaksana Rekam Medis', 4, 9, 15, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(47, 'Koor Casemix', 4, 9, 16, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(48, 'Pelaksana Casemix', 4, 9, 16, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(49, 'Pelaksana Keuangan', 4, 11, 17, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(50, 'Koor Kassa', 4, 11, 18, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(51, 'Pelaksana Kassa', 4, 11, 18, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(52, 'Sekretariat', 4, 13, 19, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(53, 'Humas Marketing', 4, 6, 19, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(54, 'Staf SDM', 4, 13, 19, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(55, 'Staf Diklat', 4, 6, 19, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(56, 'Fisioterapis', 4, 8, 20, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(57, 'Koor Gizi', 4, 8, 21, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(58, 'Ahli Gizi', 4, 8, 21, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(59, 'Pelaksana Gizi', 4, 8, 21, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(60, 'Pelaksana Logistik', 4, 13, 22, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(61, 'Kepala Unit Sanitasi & CSSD', 4, 12, 23, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(62, 'Sanitarian', 4, 12, 23, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(63, 'Pelaksana CSSD', 4, 11, 23, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(64, 'Kepala Unit Elektromedis & Teknisi', 4, 12, 24, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(65, 'Elektromedis', 4, 12, 24, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(66, 'Pelaksana Teknisi', 4, 12, 24, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(67, 'Koor Satpam-Umum', 4, 13, 25, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(68, 'Pelaksana Satpam', 4, 13, 25, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(69, 'Pelaksana Umum', 4, 13, 26, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(70, 'Analis Sistem', 4, 15, 27, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(71, 'Analis Hardware', 4, 15, 27, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(72, 'Programer', 4, 15, 27, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(73, 'UX Designer', 4, 15, 27, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(74, 'Koor Laundry', 4, 12, 28, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(75, 'Pelaksana Laundry', 4, 12, 28, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(76, 'Pelaksana Driver', 4, 13, 29, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(77, 'Koor Dokter', 4, 10, 30, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(78, 'Dokter Umum', 4, 10, 30, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(79, 'Dokter Gigi', 4, 10, 31, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(80, 'Spesialis Anestesi', 4, 10, 30, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(81, 'Spesialis Radiologi', 4, 10, 30, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(82, 'Spesialis Anak', 4, 10, 30, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(83, 'Spesialis Penyakit Dalam', 4, 10, 30, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(84, 'Spesialis Jantung & Pembuluh Darah', 4, 10, 30, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(85, 'Spesialis Saraf', 4, 10, 30, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(86, 'Spesialis Bedah', 4, 10, 30, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(87, 'Spesialis Obsgyn', 4, 10, 30, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(88, 'Spesialis Ortopaedi', 4, 10, 30, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(89, 'Spesialis Kedokteran Jiwa', 4, 10, 30, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(90, 'Spesialis THT-KL', 4, 10, 30, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(91, 'Sub Spesialis Ginjal Hipertensi', 4, 10, 30, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(92, 'Spesialis Patologi Klinik', 4, 10, 30, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(93, 'Spesialis Kedokteran Fisik dan Rehabilitasi', 4, 10, 30, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(94, 'Spesialis Mata', 4, 10, 30, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(95, 'Spesialis Dematologi dan Venereologi', 4, 10, 30, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(96, 'Spesialis Urologi', 4, 10, 30, '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(97, 'Dokter Internship', 4, 10, 30, '2025-11-08 00:05:06', '2025-11-08 00:05:06');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Direktur', '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(2, 'Tenaga Medis', '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(3, 'Tenaga Keperawatan', '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(4, 'Tenaga Kebidanan', '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(5, 'Tenaga Kefarmasian', '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(6, 'Tenaga Kesehatan Masyarakat', '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(7, 'Tenaga Kesehatan Lingkungan', '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(8, 'Tenaga Gizi', '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(9, 'Tenaga Teknik Biomedika', '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(10, 'Tenaga Keterapian Fisik', '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(11, 'Tenaga Keteknisian Medis', '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(12, 'Tenaga Lainnya', '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(13, 'Tenaga Internship', '2025-11-08 00:05:06', '2025-11-08 00:05:06');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at`) VALUES
(1, 'default', '{\"uuid\":\"5407d1e8-afb2-4f7f-8d3f-0bd21b487753\",\"displayName\":\"Filament\\\\Notifications\\\\DatabaseNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:6;}s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"staff\\\";i:1;s:11:\\\"staff.chair\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:43:\\\"Filament\\\\Notifications\\\\DatabaseNotification\\\":2:{s:4:\\\"data\\\";a:11:{s:7:\\\"actions\\\";a:0:{}s:4:\\\"body\\\";N;s:5:\\\"color\\\";N;s:8:\\\"duration\\\";s:10:\\\"persistent\\\";s:4:\\\"icon\\\";s:23:\\\"heroicon-o-check-circle\\\";s:9:\\\"iconColor\\\";s:7:\\\"success\\\";s:6:\\\"status\\\";s:7:\\\"success\\\";s:5:\\\"title\\\";s:16:\\\"Lembur diketahui\\\";s:4:\\\"view\\\";N;s:8:\\\"viewData\\\";a:0:{}s:6:\\\"format\\\";s:8:\\\"filament\\\";}s:2:\\\"id\\\";s:36:\\\"a4211a91-3d94-46ef-ba96-6068309bd680\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"},\"createdAt\":1764557149,\"delay\":null}', 0, NULL, 1764557149, 1764557149),
(2, 'default', '{\"uuid\":\"301a515c-371a-46ff-a807-ff3d5f2dc43d\",\"displayName\":\"Filament\\\\Notifications\\\\DatabaseNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:6;}s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"staff\\\";i:1;s:11:\\\"staff.chair\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:43:\\\"Filament\\\\Notifications\\\\DatabaseNotification\\\":2:{s:4:\\\"data\\\";a:11:{s:7:\\\"actions\\\";a:0:{}s:4:\\\"body\\\";N;s:5:\\\"color\\\";N;s:8:\\\"duration\\\";s:10:\\\"persistent\\\";s:4:\\\"icon\\\";s:23:\\\"heroicon-o-check-circle\\\";s:9:\\\"iconColor\\\";s:7:\\\"success\\\";s:6:\\\"status\\\";s:7:\\\"success\\\";s:5:\\\"title\\\";s:16:\\\"Lembur diketahui\\\";s:4:\\\"view\\\";N;s:8:\\\"viewData\\\";a:0:{}s:6:\\\"format\\\";s:8:\\\"filament\\\";}s:2:\\\"id\\\";s:36:\\\"d8a07203-f88f-4ebd-9cfe-673c78ebd56e\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"},\"createdAt\":1764557585,\"delay\":null}', 0, NULL, 1764557585, 1764557585),
(3, 'default', '{\"uuid\":\"1bf376fa-0923-443d-bf74-d37276d4747c\",\"displayName\":\"Filament\\\\Notifications\\\\DatabaseNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:6;}s:9:\\\"relations\\\";a:2:{i:0;s:5:\\\"staff\\\";i:1;s:11:\\\"staff.chair\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:43:\\\"Filament\\\\Notifications\\\\DatabaseNotification\\\":2:{s:4:\\\"data\\\";a:11:{s:7:\\\"actions\\\";a:1:{i:0;a:22:{s:4:\\\"name\\\";s:4:\\\"read\\\";s:5:\\\"color\\\";N;s:5:\\\"event\\\";N;s:9:\\\"eventData\\\";a:0:{}s:17:\\\"dispatchDirection\\\";b:0;s:19:\\\"dispatchToComponent\\\";N;s:15:\\\"extraAttributes\\\";a:0:{}s:4:\\\"icon\\\";N;s:12:\\\"iconPosition\\\";E:42:\\\"Filament\\\\Support\\\\Enums\\\\IconPosition:Before\\\";s:8:\\\"iconSize\\\";N;s:10:\\\"isOutlined\\\";b:0;s:10:\\\"isDisabled\\\";b:0;s:5:\\\"label\\\";s:4:\\\"Read\\\";s:11:\\\"shouldClose\\\";b:0;s:16:\\\"shouldMarkAsRead\\\";b:1;s:18:\\\"shouldMarkAsUnread\\\";b:0;s:21:\\\"shouldOpenUrlInNewTab\\\";b:0;s:15:\\\"shouldPostToUrl\\\";b:0;s:4:\\\"size\\\";E:33:\\\"Filament\\\\Support\\\\Enums\\\\Size:Small\\\";s:7:\\\"tooltip\\\";N;s:3:\\\"url\\\";s:28:\\\"http:\\/\\/simpeg.test\\/overtimes\\\";s:4:\\\"view\\\";s:33:\\\"filament::components.button.index\\\";}}s:4:\\\"body\\\";s:45:\\\"Lembur yang telah Anda ajukan telah diketahui\\\";s:5:\\\"color\\\";N;s:8:\\\"duration\\\";s:10:\\\"persistent\\\";s:4:\\\"icon\\\";s:23:\\\"heroicon-o-check-circle\\\";s:9:\\\"iconColor\\\";s:7:\\\"success\\\";s:6:\\\"status\\\";s:7:\\\"success\\\";s:5:\\\"title\\\";s:26:\\\"Pengajuan Lembur Diketahui\\\";s:4:\\\"view\\\";N;s:8:\\\"viewData\\\";a:0:{}s:6:\\\"format\\\";s:8:\\\"filament\\\";}s:2:\\\"id\\\";s:36:\\\"883c71ec-d0fe-498f-a599-0a5eb31f9d6e\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"},\"createdAt\":1764557982,\"delay\":null}', 0, NULL, 1764557982, 1764557982),
(4, 'default', '{\"uuid\":\"e9bdf81f-1202-43a3-83b2-e6b23ee82157\",\"displayName\":\"Filament\\\\Notifications\\\\DatabaseNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:15;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:43:\\\"Filament\\\\Notifications\\\\DatabaseNotification\\\":2:{s:4:\\\"data\\\";a:11:{s:7:\\\"actions\\\";a:1:{i:0;a:22:{s:4:\\\"name\\\";s:4:\\\"read\\\";s:5:\\\"color\\\";N;s:5:\\\"event\\\";N;s:9:\\\"eventData\\\";a:0:{}s:17:\\\"dispatchDirection\\\";b:0;s:19:\\\"dispatchToComponent\\\";N;s:15:\\\"extraAttributes\\\";a:0:{}s:4:\\\"icon\\\";N;s:12:\\\"iconPosition\\\";E:42:\\\"Filament\\\\Support\\\\Enums\\\\IconPosition:Before\\\";s:8:\\\"iconSize\\\";N;s:10:\\\"isOutlined\\\";b:0;s:10:\\\"isDisabled\\\";b:0;s:5:\\\"label\\\";s:4:\\\"Read\\\";s:11:\\\"shouldClose\\\";b:0;s:16:\\\"shouldMarkAsRead\\\";b:1;s:18:\\\"shouldMarkAsUnread\\\";b:0;s:21:\\\"shouldOpenUrlInNewTab\\\";b:0;s:15:\\\"shouldPostToUrl\\\";b:0;s:4:\\\"size\\\";E:33:\\\"Filament\\\\Support\\\\Enums\\\\Size:Small\\\";s:7:\\\"tooltip\\\";N;s:3:\\\"url\\\";s:28:\\\"http:\\/\\/simpeg.test\\/overtimes\\\";s:4:\\\"view\\\";s:33:\\\"filament::components.button.index\\\";}}s:4:\\\"body\\\";s:45:\\\"Lembur yang telah Anda ajukan telah diketahui\\\";s:5:\\\"color\\\";N;s:8:\\\"duration\\\";s:10:\\\"persistent\\\";s:4:\\\"icon\\\";s:23:\\\"heroicon-o-check-circle\\\";s:9:\\\"iconColor\\\";s:7:\\\"success\\\";s:6:\\\"status\\\";s:7:\\\"success\\\";s:5:\\\"title\\\";s:26:\\\"Pengajuan Lembur Diketahui\\\";s:4:\\\"view\\\";N;s:8:\\\"viewData\\\";a:0:{}s:6:\\\"format\\\";s:8:\\\"filament\\\";}s:2:\\\"id\\\";s:36:\\\"6d427f0a-c752-49c3-9361-460d673dfde8\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"},\"createdAt\":1764559118,\"delay\":null}', 0, NULL, 1764559118, 1764559118),
(5, 'default', '{\"uuid\":\"6e4c81bc-910c-4fe9-bcdd-160ce6d485b3\",\"displayName\":\"Filament\\\\Notifications\\\\DatabaseNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:15;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:43:\\\"Filament\\\\Notifications\\\\DatabaseNotification\\\":2:{s:4:\\\"data\\\";a:11:{s:7:\\\"actions\\\";a:1:{i:0;a:22:{s:4:\\\"name\\\";s:4:\\\"read\\\";s:5:\\\"color\\\";N;s:5:\\\"event\\\";N;s:9:\\\"eventData\\\";a:0:{}s:17:\\\"dispatchDirection\\\";b:0;s:19:\\\"dispatchToComponent\\\";N;s:15:\\\"extraAttributes\\\";a:0:{}s:4:\\\"icon\\\";N;s:12:\\\"iconPosition\\\";E:42:\\\"Filament\\\\Support\\\\Enums\\\\IconPosition:Before\\\";s:8:\\\"iconSize\\\";N;s:10:\\\"isOutlined\\\";b:0;s:10:\\\"isDisabled\\\";b:0;s:5:\\\"label\\\";s:4:\\\"Read\\\";s:11:\\\"shouldClose\\\";b:0;s:16:\\\"shouldMarkAsRead\\\";b:1;s:18:\\\"shouldMarkAsUnread\\\";b:0;s:21:\\\"shouldOpenUrlInNewTab\\\";b:0;s:15:\\\"shouldPostToUrl\\\";b:0;s:4:\\\"size\\\";E:33:\\\"Filament\\\\Support\\\\Enums\\\\Size:Small\\\";s:7:\\\"tooltip\\\";N;s:3:\\\"url\\\";s:28:\\\"http:\\/\\/simpeg.test\\/overtimes\\\";s:4:\\\"view\\\";s:33:\\\"filament::components.button.index\\\";}}s:4:\\\"body\\\";s:45:\\\"Lembur yang telah Anda ajukan telah diketahui\\\";s:5:\\\"color\\\";N;s:8:\\\"duration\\\";s:10:\\\"persistent\\\";s:4:\\\"icon\\\";s:23:\\\"heroicon-o-check-circle\\\";s:9:\\\"iconColor\\\";s:7:\\\"success\\\";s:6:\\\"status\\\";s:7:\\\"success\\\";s:5:\\\"title\\\";s:26:\\\"Pengajuan Lembur Diketahui\\\";s:4:\\\"view\\\";N;s:8:\\\"viewData\\\";a:0:{}s:6:\\\"format\\\";s:8:\\\"filament\\\";}s:2:\\\"id\\\";s:36:\\\"89559408-8036-4f77-b109-2654a15d9270\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"},\"createdAt\":1764559434,\"delay\":null}', 0, NULL, 1764559434, 1764559434),
(6, 'default', '{\"uuid\":\"9b7f9b2f-682f-4baf-8386-4cbbbc738868\",\"displayName\":\"Filament\\\\Notifications\\\\DatabaseNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:15;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:43:\\\"Filament\\\\Notifications\\\\DatabaseNotification\\\":2:{s:4:\\\"data\\\";a:11:{s:7:\\\"actions\\\";a:0:{}s:4:\\\"body\\\";s:10:\\\"Halo Tamam\\\";s:5:\\\"color\\\";N;s:8:\\\"duration\\\";s:10:\\\"persistent\\\";s:4:\\\"icon\\\";N;s:9:\\\"iconColor\\\";N;s:6:\\\"status\\\";N;s:5:\\\"title\\\";s:17:\\\"Tes dari Terminal\\\";s:4:\\\"view\\\";N;s:8:\\\"viewData\\\";a:0:{}s:6:\\\"format\\\";s:8:\\\"filament\\\";}s:2:\\\"id\\\";s:36:\\\"b48de70e-eee6-41da-89e1-9c21a3e087b3\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"},\"createdAt\":1764559829,\"delay\":null}', 0, NULL, 1764559829, 1764559829),
(7, 'default', '{\"uuid\":\"0e305518-4650-46ec-b909-4e30d800fb5b\",\"displayName\":\"Filament\\\\Notifications\\\\DatabaseNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:15;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:43:\\\"Filament\\\\Notifications\\\\DatabaseNotification\\\":2:{s:4:\\\"data\\\";a:11:{s:7:\\\"actions\\\";a:0:{}s:4:\\\"body\\\";s:10:\\\"Halo Tamam\\\";s:5:\\\"color\\\";N;s:8:\\\"duration\\\";s:10:\\\"persistent\\\";s:4:\\\"icon\\\";N;s:9:\\\"iconColor\\\";N;s:6:\\\"status\\\";N;s:5:\\\"title\\\";s:17:\\\"Tes dari Terminal\\\";s:4:\\\"view\\\";N;s:8:\\\"viewData\\\";a:0:{}s:6:\\\"format\\\";s:8:\\\"filament\\\";}s:2:\\\"id\\\";s:36:\\\"2273760a-da04-49da-8e8c-394d11c5904c\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"},\"createdAt\":1764560106,\"delay\":null}', 0, NULL, 1764560106, 1764560106),
(8, 'default', '{\"uuid\":\"237f8a3a-6ff7-4bb4-9d7b-a387b0ce067b\",\"displayName\":\"Filament\\\\Notifications\\\\DatabaseNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:15;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:43:\\\"Filament\\\\Notifications\\\\DatabaseNotification\\\":2:{s:4:\\\"data\\\";a:11:{s:7:\\\"actions\\\";a:0:{}s:4:\\\"body\\\";s:10:\\\"Halo Tamam\\\";s:5:\\\"color\\\";N;s:8:\\\"duration\\\";s:10:\\\"persistent\\\";s:4:\\\"icon\\\";N;s:9:\\\"iconColor\\\";N;s:6:\\\"status\\\";N;s:5:\\\"title\\\";s:17:\\\"Tes dari Terminal\\\";s:4:\\\"view\\\";N;s:8:\\\"viewData\\\";a:0:{}s:6:\\\"format\\\";s:8:\\\"filament\\\";}s:2:\\\"id\\\";s:36:\\\"48822b49-8ad9-470a-a7ff-41d5aac78f8b\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"},\"createdAt\":1764560746,\"delay\":null}', 0, NULL, 1764560746, 1764560746),
(9, 'default', '{\"uuid\":\"bb838e95-15e3-4391-8ef3-ab1cd0e2569c\",\"displayName\":\"Filament\\\\Notifications\\\\DatabaseNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:15;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:43:\\\"Filament\\\\Notifications\\\\DatabaseNotification\\\":2:{s:4:\\\"data\\\";a:11:{s:7:\\\"actions\\\";a:0:{}s:4:\\\"body\\\";s:10:\\\"Halo Tamam\\\";s:5:\\\"color\\\";N;s:8:\\\"duration\\\";s:10:\\\"persistent\\\";s:4:\\\"icon\\\";N;s:9:\\\"iconColor\\\";N;s:6:\\\"status\\\";N;s:5:\\\"title\\\";s:17:\\\"Tes dari Terminal\\\";s:4:\\\"view\\\";N;s:8:\\\"viewData\\\";a:0:{}s:6:\\\"format\\\";s:8:\\\"filament\\\";}s:2:\\\"id\\\";s:36:\\\"0cb2ad02-0521-47c4-8965-0a70962df4d8\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"},\"createdAt\":1764560792,\"delay\":null}', 0, NULL, 1764560792, 1764560792),
(10, 'default', '{\"uuid\":\"b634a783-7705-41f9-adc8-93f86b48a047\",\"displayName\":\"Filament\\\\Notifications\\\\DatabaseNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:15;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:43:\\\"Filament\\\\Notifications\\\\DatabaseNotification\\\":2:{s:4:\\\"data\\\";a:11:{s:7:\\\"actions\\\";a:1:{i:0;a:22:{s:4:\\\"name\\\";s:4:\\\"read\\\";s:5:\\\"color\\\";N;s:5:\\\"event\\\";N;s:9:\\\"eventData\\\";a:0:{}s:17:\\\"dispatchDirection\\\";b:0;s:19:\\\"dispatchToComponent\\\";N;s:15:\\\"extraAttributes\\\";a:0:{}s:4:\\\"icon\\\";N;s:12:\\\"iconPosition\\\";E:42:\\\"Filament\\\\Support\\\\Enums\\\\IconPosition:Before\\\";s:8:\\\"iconSize\\\";N;s:10:\\\"isOutlined\\\";b:0;s:10:\\\"isDisabled\\\";b:0;s:5:\\\"label\\\";s:4:\\\"Read\\\";s:11:\\\"shouldClose\\\";b:0;s:16:\\\"shouldMarkAsRead\\\";b:1;s:18:\\\"shouldMarkAsUnread\\\";b:0;s:21:\\\"shouldOpenUrlInNewTab\\\";b:0;s:15:\\\"shouldPostToUrl\\\";b:0;s:4:\\\"size\\\";E:33:\\\"Filament\\\\Support\\\\Enums\\\\Size:Small\\\";s:7:\\\"tooltip\\\";N;s:3:\\\"url\\\";s:28:\\\"http:\\/\\/simpeg.test\\/overtimes\\\";s:4:\\\"view\\\";s:33:\\\"filament::components.button.index\\\";}}s:4:\\\"body\\\";s:45:\\\"Lembur yang telah Anda ajukan telah diketahui\\\";s:5:\\\"color\\\";N;s:8:\\\"duration\\\";s:10:\\\"persistent\\\";s:4:\\\"icon\\\";s:23:\\\"heroicon-o-check-circle\\\";s:9:\\\"iconColor\\\";s:7:\\\"success\\\";s:6:\\\"status\\\";s:7:\\\"success\\\";s:5:\\\"title\\\";s:26:\\\"Pengajuan Lembur Diketahui\\\";s:4:\\\"view\\\";N;s:8:\\\"viewData\\\";a:0:{}s:6:\\\"format\\\";s:8:\\\"filament\\\";}s:2:\\\"id\\\";s:36:\\\"d85f5045-1618-4949-b20a-9e52472de247\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"},\"createdAt\":1764560858,\"delay\":null}', 0, NULL, 1764560858, 1764560858),
(11, 'default', '{\"uuid\":\"e2fd1474-0783-4e2f-af26-f24fbda00c1e\",\"displayName\":\"Filament\\\\Notifications\\\\DatabaseNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:15;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:43:\\\"Filament\\\\Notifications\\\\DatabaseNotification\\\":2:{s:4:\\\"data\\\";a:11:{s:7:\\\"actions\\\";a:1:{i:0;a:22:{s:4:\\\"name\\\";s:4:\\\"read\\\";s:5:\\\"color\\\";N;s:5:\\\"event\\\";N;s:9:\\\"eventData\\\";a:0:{}s:17:\\\"dispatchDirection\\\";b:0;s:19:\\\"dispatchToComponent\\\";N;s:15:\\\"extraAttributes\\\";a:0:{}s:4:\\\"icon\\\";N;s:12:\\\"iconPosition\\\";E:42:\\\"Filament\\\\Support\\\\Enums\\\\IconPosition:Before\\\";s:8:\\\"iconSize\\\";N;s:10:\\\"isOutlined\\\";b:0;s:10:\\\"isDisabled\\\";b:0;s:5:\\\"label\\\";s:4:\\\"Read\\\";s:11:\\\"shouldClose\\\";b:0;s:16:\\\"shouldMarkAsRead\\\";b:1;s:18:\\\"shouldMarkAsUnread\\\";b:0;s:21:\\\"shouldOpenUrlInNewTab\\\";b:0;s:15:\\\"shouldPostToUrl\\\";b:0;s:4:\\\"size\\\";E:33:\\\"Filament\\\\Support\\\\Enums\\\\Size:Small\\\";s:7:\\\"tooltip\\\";N;s:3:\\\"url\\\";s:28:\\\"http:\\/\\/simpeg.test\\/overtimes\\\";s:4:\\\"view\\\";s:33:\\\"filament::components.button.index\\\";}}s:4:\\\"body\\\";s:45:\\\"Lembur yang telah Anda ajukan telah diketahui\\\";s:5:\\\"color\\\";N;s:8:\\\"duration\\\";s:10:\\\"persistent\\\";s:4:\\\"icon\\\";s:23:\\\"heroicon-o-check-circle\\\";s:9:\\\"iconColor\\\";s:7:\\\"success\\\";s:6:\\\"status\\\";s:7:\\\"success\\\";s:5:\\\"title\\\";s:26:\\\"Pengajuan Lembur Diketahui\\\";s:4:\\\"view\\\";N;s:8:\\\"viewData\\\";a:0:{}s:6:\\\"format\\\";s:8:\\\"filament\\\";}s:2:\\\"id\\\";s:36:\\\"aa45c6bf-d3b8-435f-98b0-b2f1c80c5589\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"},\"createdAt\":1764560929,\"delay\":null}', 0, NULL, 1764560929, 1764560929),
(12, 'default', '{\"uuid\":\"d3d7f9f2-8f24-4794-bd48-86b87200a547\",\"displayName\":\"Filament\\\\Notifications\\\\DatabaseNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:15;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:43:\\\"Filament\\\\Notifications\\\\DatabaseNotification\\\":2:{s:4:\\\"data\\\";a:11:{s:7:\\\"actions\\\";a:1:{i:0;a:22:{s:4:\\\"name\\\";s:4:\\\"read\\\";s:5:\\\"color\\\";N;s:5:\\\"event\\\";N;s:9:\\\"eventData\\\";a:0:{}s:17:\\\"dispatchDirection\\\";b:0;s:19:\\\"dispatchToComponent\\\";N;s:15:\\\"extraAttributes\\\";a:0:{}s:4:\\\"icon\\\";N;s:12:\\\"iconPosition\\\";E:42:\\\"Filament\\\\Support\\\\Enums\\\\IconPosition:Before\\\";s:8:\\\"iconSize\\\";N;s:10:\\\"isOutlined\\\";b:0;s:10:\\\"isDisabled\\\";b:0;s:5:\\\"label\\\";s:4:\\\"Read\\\";s:11:\\\"shouldClose\\\";b:0;s:16:\\\"shouldMarkAsRead\\\";b:1;s:18:\\\"shouldMarkAsUnread\\\";b:0;s:21:\\\"shouldOpenUrlInNewTab\\\";b:0;s:15:\\\"shouldPostToUrl\\\";b:0;s:4:\\\"size\\\";E:33:\\\"Filament\\\\Support\\\\Enums\\\\Size:Small\\\";s:7:\\\"tooltip\\\";N;s:3:\\\"url\\\";s:28:\\\"http:\\/\\/simpeg.test\\/overtimes\\\";s:4:\\\"view\\\";s:33:\\\"filament::components.button.index\\\";}}s:4:\\\"body\\\";s:45:\\\"Lembur yang telah Anda ajukan telah diketahui\\\";s:5:\\\"color\\\";N;s:8:\\\"duration\\\";s:10:\\\"persistent\\\";s:4:\\\"icon\\\";s:23:\\\"heroicon-o-check-circle\\\";s:9:\\\"iconColor\\\";s:7:\\\"success\\\";s:6:\\\"status\\\";s:7:\\\"success\\\";s:5:\\\"title\\\";s:26:\\\"Pengajuan Lembur Diketahui\\\";s:4:\\\"view\\\";N;s:8:\\\"viewData\\\";a:0:{}s:6:\\\"format\\\";s:8:\\\"filament\\\";}s:2:\\\"id\\\";s:36:\\\"674c7290-02ae-4f0c-860f-e495eff328d4\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"},\"createdAt\":1764561071,\"delay\":null}', 0, NULL, 1764561071, 1764561071),
(13, 'default', '{\"uuid\":\"f3e8d743-61ac-4197-b2ba-2765d1c10f9e\",\"displayName\":\"Filament\\\\Notifications\\\\DatabaseNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:15;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:43:\\\"Filament\\\\Notifications\\\\DatabaseNotification\\\":2:{s:4:\\\"data\\\";a:11:{s:7:\\\"actions\\\";a:0:{}s:4:\\\"body\\\";N;s:5:\\\"color\\\";N;s:8:\\\"duration\\\";s:10:\\\"persistent\\\";s:4:\\\"icon\\\";N;s:9:\\\"iconColor\\\";N;s:6:\\\"status\\\";N;s:5:\\\"title\\\";s:18:\\\"Saved successfully\\\";s:4:\\\"view\\\";N;s:8:\\\"viewData\\\";a:0:{}s:6:\\\"format\\\";s:8:\\\"filament\\\";}s:2:\\\"id\\\";s:36:\\\"f71e2ee8-9a24-4c4a-bd85-d4e134a629aa\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"},\"createdAt\":1764561071,\"delay\":null}', 0, NULL, 1764561071, 1764561071),
(14, 'default', '{\"uuid\":\"59800384-9829-4914-8433-8c3ba2377005\",\"displayName\":\"Filament\\\\Notifications\\\\DatabaseNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:15;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:43:\\\"Filament\\\\Notifications\\\\DatabaseNotification\\\":2:{s:4:\\\"data\\\";a:11:{s:7:\\\"actions\\\";a:1:{i:0;a:22:{s:4:\\\"name\\\";s:4:\\\"read\\\";s:5:\\\"color\\\";N;s:5:\\\"event\\\";N;s:9:\\\"eventData\\\";a:0:{}s:17:\\\"dispatchDirection\\\";b:0;s:19:\\\"dispatchToComponent\\\";N;s:15:\\\"extraAttributes\\\";a:0:{}s:4:\\\"icon\\\";N;s:12:\\\"iconPosition\\\";E:42:\\\"Filament\\\\Support\\\\Enums\\\\IconPosition:Before\\\";s:8:\\\"iconSize\\\";N;s:10:\\\"isOutlined\\\";b:0;s:10:\\\"isDisabled\\\";b:0;s:5:\\\"label\\\";s:4:\\\"Read\\\";s:11:\\\"shouldClose\\\";b:0;s:16:\\\"shouldMarkAsRead\\\";b:1;s:18:\\\"shouldMarkAsUnread\\\";b:0;s:21:\\\"shouldOpenUrlInNewTab\\\";b:0;s:15:\\\"shouldPostToUrl\\\";b:0;s:4:\\\"size\\\";E:33:\\\"Filament\\\\Support\\\\Enums\\\\Size:Small\\\";s:7:\\\"tooltip\\\";N;s:3:\\\"url\\\";s:28:\\\"http:\\/\\/simpeg.test\\/overtimes\\\";s:4:\\\"view\\\";s:33:\\\"filament::components.button.index\\\";}}s:4:\\\"body\\\";s:45:\\\"Lembur yang telah Anda ajukan telah diketahui\\\";s:5:\\\"color\\\";N;s:8:\\\"duration\\\";s:10:\\\"persistent\\\";s:4:\\\"icon\\\";s:23:\\\"heroicon-o-check-circle\\\";s:9:\\\"iconColor\\\";s:7:\\\"success\\\";s:6:\\\"status\\\";s:7:\\\"success\\\";s:5:\\\"title\\\";s:26:\\\"Pengajuan Lembur Diketahui\\\";s:4:\\\"view\\\";N;s:8:\\\"viewData\\\";a:0:{}s:6:\\\"format\\\";s:8:\\\"filament\\\";}s:2:\\\"id\\\";s:36:\\\"bb21b888-5757-42ae-a5ae-8353a9a26917\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"},\"createdAt\":1764561114,\"delay\":null}', 0, NULL, 1764561114, 1764561114),
(15, 'default', '{\"uuid\":\"441cd2bf-6dac-4f22-80b3-ca159ab937d4\",\"displayName\":\"Filament\\\\Notifications\\\\DatabaseNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:15;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:43:\\\"Filament\\\\Notifications\\\\DatabaseNotification\\\":2:{s:4:\\\"data\\\";a:11:{s:7:\\\"actions\\\";a:0:{}s:4:\\\"body\\\";N;s:5:\\\"color\\\";N;s:8:\\\"duration\\\";s:10:\\\"persistent\\\";s:4:\\\"icon\\\";N;s:9:\\\"iconColor\\\";N;s:6:\\\"status\\\";N;s:5:\\\"title\\\";s:18:\\\"Saved successfully\\\";s:4:\\\"view\\\";N;s:8:\\\"viewData\\\";a:0:{}s:6:\\\"format\\\";s:8:\\\"filament\\\";}s:2:\\\"id\\\";s:36:\\\"abb50585-9364-447d-abfc-d441bd54b4be\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\"},\"createdAt\":1764561114,\"delay\":null}', 0, NULL, 1764561114, 1764561114);

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leaves`
--

CREATE TABLE `leaves` (
  `id` bigint UNSIGNED NOT NULL,
  `type` enum('Cuti','Izin') COLLATE utf8mb4_unicode_ci NOT NULL,
  `subtype` enum('Tahunan','Melahirkan','Duka','Menikah','Ibadah Haji','Khitan Anak','Baptis Anak','Non-Sakit','Sakit') COLLATE utf8mb4_unicode_ci NOT NULL,
  `staff_id` bigint UNSIGNED NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `remaining` tinyint UNSIGNED DEFAULT NULL,
  `replacement_id` bigint UNSIGNED NOT NULL,
  `evidence` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_replaced` tinyint UNSIGNED DEFAULT NULL,
  `replacement_at` date DEFAULT NULL,
  `status` enum('Menunggu','Diketahui Kepala Unit','Diketahui Koordinator','Disetujui Kepala Seksi','Disetujui Direktur','Ditolak') COLLATE utf8mb4_unicode_ci NOT NULL,
  `known_by` bigint UNSIGNED DEFAULT NULL,
  `known_at` date DEFAULT NULL,
  `approver_id` bigint UNSIGNED DEFAULT NULL,
  `approve_at` date DEFAULT NULL,
  `is_verified` tinyint UNSIGNED DEFAULT NULL,
  `verified_by` bigint UNSIGNED DEFAULT NULL,
  `verified_at` date DEFAULT NULL,
  `adverb` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leaves`
--

INSERT INTO `leaves` (`id`, `type`, `subtype`, `staff_id`, `start_date`, `end_date`, `reason`, `remaining`, `replacement_id`, `evidence`, `is_replaced`, `replacement_at`, `status`, `known_by`, `known_at`, `approver_id`, `approve_at`, `is_verified`, `verified_by`, `verified_at`, `adverb`, `created_at`, `updated_at`) VALUES
(1, 'Izin', 'Sakit', 10, '2025-11-09', '2025-11-11', 'tes', NULL, 11, 'surat-cuti/01K9H52MM08RPNC04D8871G4P4.pdf', NULL, NULL, 'Menunggu', NULL, NULL, NULL, NULL, 0, NULL, NULL, 'ga valid ajah', '2025-11-08 00:14:48', '2025-11-09 19:35:39'),
(2, 'Izin', 'Non-Sakit', 8, '2025-11-09', '2025-11-10', 'tes', 6, 7, NULL, 1, '2025-11-25', 'Disetujui Kepala Seksi', 4, '2025-11-25', 2, '2025-12-20', 1, 6, '2025-11-25', NULL, '2025-11-08 00:40:05', '2025-12-20 06:56:18');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000001_create_cache_table', 1),
(2, '0001_01_01_000002_create_jobs_table', 1),
(3, '2025_10_23_023126_create_chairs_table', 1),
(4, '2025_10_23_023257_create_units_table', 1),
(5, '2025_10_23_032131_create_staff_statuses_table', 1),
(6, '2025_10_23_032138_create_groups_table', 1),
(7, '2025_10_23_061849_create_roles_table', 1),
(8, '2025_10_23_062616_create_staff_table', 1),
(9, '2025_10_23_067289_create_users_table', 1),
(10, '2025_10_25_034922_create_leaves_table', 1),
(11, '2025_10_28_044930_create_system_rules_table', 1),
(12, '2025_10_30_035124_create_staff_entry_education_table', 1),
(13, '2025_10_30_035132_create_staff_work_education_table', 1),
(14, '2025_10_30_035146_create_staff_work_experiences_table', 1),
(15, '2025_10_30_035156_create_staff_contracts_table', 1),
(16, '2025_10_30_035205_create_staff_appointments_table', 1),
(17, '2025_10_30_035214_create_staff_adjustments_table', 1),
(18, '2025_11_06_022432_create_staff_administrations_table', 1),
(22, '2025_11_10_024936_create_overtimes_table', 2),
(24, '2025_11_10_064610_create_presences_table', 3),
(33, '2025_11_19_131201_create_pre_staff_table', 5),
(35, '2025_12_01_092750_create_notifications_table', 6),
(36, '2025_12_04_133534_create_staff_trainings_table', 7),
(39, '2025_11_19_093225_create_performance_periods_table', 8),
(40, '2025_11_19_094750_create_staff_performances_table', 8),
(41, '2025_11_19_102427_create_performance_appraisals_table', 8),
(42, '2025_12_09_094209_create_shifts_table', 9),
(43, '2025_12_09_094218_create_schedules_table', 9),
(44, '2025_12_24_092513_create_work_histories_table', 10);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint UNSIGNED NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES
('369c6566-1cdd-428f-9fd2-8644ca2cb53f', 'Filament\\Notifications\\DatabaseNotification', 'App\\Models\\User', 9, '{\"actions\":[{\"name\":\"read\",\"color\":null,\"event\":null,\"eventData\":[],\"dispatchDirection\":false,\"dispatchToComponent\":null,\"extraAttributes\":[],\"icon\":null,\"iconPosition\":\"before\",\"iconSize\":null,\"isOutlined\":false,\"isDisabled\":false,\"label\":\"Read\",\"shouldClose\":false,\"shouldMarkAsRead\":true,\"shouldMarkAsUnread\":false,\"shouldOpenUrlInNewTab\":false,\"shouldPostToUrl\":false,\"size\":\"sm\",\"tooltip\":null,\"url\":\"http:\\/\\/simpeg.test\\/leaves\",\"view\":\"filament::components.button.index\"}],\"body\":\"Izin Anda untuk tanggal 09 November 2025 telah disetujui Kepala Seksi\",\"color\":null,\"duration\":\"persistent\",\"icon\":\"heroicon-o-check-circle\",\"iconColor\":\"success\",\"status\":\"success\",\"title\":\"Izin Anda telah disetujui Kepala Seksi\",\"view\":null,\"viewData\":[],\"format\":\"filament\"}', NULL, '2025-12-20 06:56:18', '2025-12-20 06:56:18');

-- --------------------------------------------------------

--
-- Table structure for table `overtimes`
--

CREATE TABLE `overtimes` (
  `id` bigint UNSIGNED NOT NULL,
  `staff_id` bigint UNSIGNED NOT NULL,
  `overtime_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time DEFAULT NULL,
  `command` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `hours` decimal(8,1) DEFAULT NULL,
  `month_year` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_known` tinyint UNSIGNED DEFAULT NULL,
  `known_by` bigint UNSIGNED DEFAULT NULL,
  `known_at` date DEFAULT NULL,
  `is_verified` tinyint UNSIGNED DEFAULT NULL,
  `verified_by` bigint UNSIGNED DEFAULT NULL,
  `verified_at` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `overtimes`
--

INSERT INTO `overtimes` (`id`, `staff_id`, `overtime_date`, `start_time`, `end_time`, `command`, `hours`, `month_year`, `is_known`, `known_by`, `known_at`, `is_verified`, `verified_by`, `verified_at`, `created_at`, `updated_at`) VALUES
(3, 8, '2025-11-11', '07:30:00', '14:54:00', 'Tesssssssss', 7.4, '2025-11', NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-11 07:50:10', '2025-11-24 02:24:37'),
(4, 9, '2025-11-11', '16:30:00', '14:55:00', 'tesssasdasdasdasd', 22.4, '2025-11', 2, 4, '2025-11-25', 1, 6, '2025-11-25', '2025-11-11 07:55:10', '2025-11-25 03:57:56'),
(5, 14, '2025-12-02', '15:30:00', '10:06:00', 'Anuu', 18.6, '2025-12', 2, 5, '2025-12-02', 1, 6, '2025-12-03', '2025-12-01 02:34:01', '2025-12-03 03:31:07');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `performance_appraisals`
--

CREATE TABLE `performance_appraisals` (
  `id` bigint UNSIGNED NOT NULL,
  `target_id` bigint UNSIGNED NOT NULL,
  `appraiser_id` bigint UNSIGNED NOT NULL,
  `score` decimal(5,2) NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `performance_appraisals`
--

INSERT INTO `performance_appraisals` (`id`, `target_id`, `appraiser_id`, `score`, `notes`, `created_at`, `updated_at`) VALUES
(2, 1, 7, 90.00, 'tess', '2025-12-05 08:44:05', '2025-12-05 08:44:05'),
(3, 2, 3, 86.00, 'mantap', '2025-12-05 08:56:32', '2025-12-08 07:16:22'),
(4, 5, 3, 48.00, 'jelek', '2025-12-08 02:55:37', '2025-12-10 02:50:33');

-- --------------------------------------------------------

--
-- Table structure for table `performance_periods`
--

CREATE TABLE `performance_periods` (
  `id` bigint UNSIGNED NOT NULL,
  `year` int UNSIGNED NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` tinyint UNSIGNED DEFAULT NULL,
  `score` decimal(5,2) DEFAULT NULL COMMENT 'EMPTY',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `performance_periods`
--

INSERT INTO `performance_periods` (`id`, `year`, `start_date`, `end_date`, `status`, `score`, `created_at`, `updated_at`) VALUES
(1, 2026, '2026-01-01', '2026-06-30', 0, 74.67, '2025-12-05 03:30:02', '2025-12-18 06:46:25'),
(5, 2025, '2025-12-01', '2025-12-31', 1, NULL, '2025-12-18 06:46:16', '2025-12-18 06:46:16');

-- --------------------------------------------------------

--
-- Table structure for table `presences`
--

CREATE TABLE `presences` (
  `id` bigint UNSIGNED NOT NULL,
  `staff_id` bigint UNSIGNED NOT NULL,
  `presence_date` date NOT NULL,
  `check_in` time DEFAULT NULL,
  `check_out` time DEFAULT NULL,
  `method` enum('network','coordinate') COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fingerprint` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lattitude` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `longitude` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `radius` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `presences`
--

INSERT INTO `presences` (`id`, `staff_id`, `presence_date`, `check_in`, `check_out`, `method`, `ip`, `fingerprint`, `lattitude`, `longitude`, `radius`, `created_at`, `updated_at`) VALUES
(1, 9, '2025-11-18', '13:51:50', '14:46:19', 'coordinate', NULL, '4aade4f1341a0ea2', '-7.7141269', '110.4486289', '37.005482181756', '2025-11-18 06:51:50', '2025-11-18 07:46:19'),
(2, 4, '2025-11-22', '14:56:56', '14:57:09', 'network', '114.10.150.31', '4aade4f1341a0ea2', NULL, NULL, NULL, '2025-11-22 07:56:56', '2025-11-22 07:57:09'),
(3, 8, '2025-12-04', '10:55:14', NULL, 'network', '114.10.151.76', '4aade4f1341a0ea2', NULL, NULL, NULL, '2025-11-24 03:55:14', '2025-11-24 03:55:14'),
(4, 8, '2025-12-05', '14:09:07', '14:09:11', 'network', '114.10.152.119', '4aade4f1341a0ea2', NULL, NULL, NULL, '2025-11-25 07:09:07', '2025-11-25 07:09:11'),
(5, 14, '2025-12-10', '10:16:13', '15:35:46', 'network', '114.10.150.29', '4aade4f1341a0ea2', NULL, NULL, NULL, '2025-11-26 03:16:13', '2025-12-11 04:14:46'),
(6, 6, '2025-11-27', '14:01:31', '15:15:47', 'coordinate', NULL, '4aade4f1341a0ea2', '-7.71436215', '110.44847755', '53.141049553701', '2025-11-27 07:01:31', '2025-11-27 08:15:47'),
(7, 6, '2025-11-28', '10:46:39', NULL, 'network', '114.10.152.31', '21e64010c5413112', NULL, NULL, NULL, '2025-11-28 03:46:39', '2025-11-28 03:46:39'),
(8, 6, '2025-11-29', '15:32:48', '15:33:51', 'coordinate', NULL, '21e64010c5413112', '-7.7146647', '110.4483663', '85.963423135702', '2025-11-29 08:32:48', '2025-11-29 08:33:51'),
(9, 14, '2025-12-11', '11:15:11', '15:23:14', 'network', '114.10.153.58', '5417e1657f5998f8', NULL, NULL, NULL, '2025-12-11 04:15:11', '2025-12-11 04:15:14'),
(10, 14, '2025-12-15', '13:54:51', NULL, 'network', '114.10.153.36', '5417e1657f5998f8', NULL, NULL, NULL, '2025-12-15 06:54:51', '2025-12-15 06:54:51'),
(11, 6, '2025-12-17', '09:42:23', NULL, 'network', '114.10.153.7', '5417e1657f5998f8', NULL, NULL, NULL, '2025-12-17 02:42:23', '2025-12-17 02:42:23'),
(12, 6, '2025-12-18', '13:47:54', NULL, 'network', '114.10.151.67', '5417e1657f5998f8', NULL, NULL, NULL, '2025-12-18 06:47:54', '2025-12-18 06:47:54'),
(13, 6, '2025-12-22', '10:19:00', NULL, 'network', '114.10.153.86', '5417e1657f5998f8', NULL, NULL, NULL, '2025-12-22 03:19:00', '2025-12-22 03:19:00'),
(14, 6, '2025-12-23', '09:15:38', '14:07:57', 'network', '114.10.150.62', '5417e1657f5998f8', NULL, NULL, NULL, '2025-12-23 02:15:38', '2025-12-23 07:07:57'),
(16, 6, '2025-12-29', '10:19:48', NULL, 'network', '182.13.96.128', '5417e1657f5998f8', NULL, NULL, NULL, '2025-12-29 03:19:48', '2025-12-29 03:19:48');

-- --------------------------------------------------------

--
-- Table structure for table `pre_staff`
--

CREATE TABLE `pre_staff` (
  `id` bigint UNSIGNED NOT NULL,
  `nik` bigint UNSIGNED NOT NULL,
  `nip` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `birth_date` date NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `staff_status_id` bigint UNSIGNED DEFAULT NULL,
  `chair_id` bigint UNSIGNED DEFAULT NULL,
  `group_id` bigint UNSIGNED DEFAULT NULL,
  `unit_id` bigint UNSIGNED DEFAULT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('Menunggu','Diverifikasi','Ditolak') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pre_staff`
--

INSERT INTO `pre_staff` (`id`, `nik`, `nip`, `name`, `birth_date`, `email`, `phone`, `staff_status_id`, `chair_id`, `group_id`, `unit_id`, `token`, `status`, `created_at`, `updated_at`) VALUES
(1, 1682847192937132, '5678.9876.567.8', 'safira', '2005-11-02', 'cici93@example.org', '0891-2381-9238', NULL, NULL, NULL, NULL, '900662af-69da-45d8-ba21-7db8ac5882de', 'Menunggu', '2025-11-20 00:27:05', '2025-11-20 00:27:05');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Admin', '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(2, 'User', '2025-11-08 00:05:06', '2025-11-08 00:05:06');

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `id` bigint UNSIGNED NOT NULL,
  `staff_id` bigint UNSIGNED NOT NULL,
  `shift_id` bigint UNSIGNED NOT NULL,
  `schedule_date` date NOT NULL,
  `is_locked` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`id`, `staff_id`, `shift_id`, `schedule_date`, `is_locked`, `created_at`, `updated_at`) VALUES
(1, 10, 3, '2025-12-01', 0, '2025-12-09 04:43:52', '2025-12-10 08:06:04'),
(2, 10, 3, '2025-12-02', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(3, 10, 3, '2025-12-03', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(4, 10, 3, '2025-12-04', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(5, 10, 3, '2025-12-05', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(6, 10, 3, '2025-12-06', 0, '2025-12-09 04:43:52', '2025-12-10 08:05:34'),
(7, 10, 4, '2025-12-07', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(8, 10, 3, '2025-12-08', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(9, 10, 3, '2025-12-09', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(10, 10, 3, '2025-12-10', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(11, 10, 3, '2025-12-11', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(12, 10, 3, '2025-12-12', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(13, 10, 3, '2025-12-13', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(14, 10, 4, '2025-12-14', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(15, 10, 3, '2025-12-15', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(16, 10, 3, '2025-12-16', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(17, 10, 3, '2025-12-17', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(18, 10, 3, '2025-12-18', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(19, 10, 3, '2025-12-19', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(20, 10, 3, '2025-12-20', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(21, 10, 4, '2025-12-21', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(22, 10, 3, '2025-12-22', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(23, 10, 3, '2025-12-23', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(24, 10, 3, '2025-12-24', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(25, 10, 3, '2025-12-25', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(26, 10, 3, '2025-12-26', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(27, 10, 3, '2025-12-27', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(28, 10, 4, '2025-12-28', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(29, 10, 3, '2025-12-29', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(30, 10, 3, '2025-12-30', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(31, 10, 3, '2025-12-31', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(32, 11, 3, '2025-12-01', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(33, 11, 3, '2025-12-02', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(34, 11, 3, '2025-12-03', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(35, 11, 3, '2025-12-04', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(36, 11, 3, '2025-12-05', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(37, 11, 3, '2025-12-06', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(38, 11, 4, '2025-12-07', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(39, 11, 3, '2025-12-08', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(40, 11, 3, '2025-12-09', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(41, 11, 3, '2025-12-10', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(42, 11, 3, '2025-12-11', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(43, 11, 3, '2025-12-12', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(44, 11, 3, '2025-12-13', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(45, 11, 4, '2025-12-14', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(46, 11, 3, '2025-12-15', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(47, 11, 3, '2025-12-16', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(48, 11, 3, '2025-12-17', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(49, 11, 3, '2025-12-18', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(50, 11, 3, '2025-12-19', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(51, 11, 3, '2025-12-20', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(52, 11, 4, '2025-12-21', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(53, 11, 3, '2025-12-22', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(54, 11, 3, '2025-12-23', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(55, 11, 3, '2025-12-24', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(56, 11, 3, '2025-12-25', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(57, 11, 3, '2025-12-26', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(58, 11, 3, '2025-12-27', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(59, 11, 4, '2025-12-28', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(60, 11, 3, '2025-12-29', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(61, 11, 3, '2025-12-30', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(62, 11, 3, '2025-12-31', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(63, 14, 3, '2025-12-01', 0, '2025-12-09 04:43:52', '2025-12-11 08:08:09'),
(64, 14, 3, '2025-12-02', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(65, 14, 3, '2025-12-03', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(66, 14, 3, '2025-12-04', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(67, 14, 3, '2025-12-05', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(68, 14, 3, '2025-12-06', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(69, 14, 4, '2025-12-07', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(70, 14, 3, '2025-12-08', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(71, 14, 3, '2025-12-09', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(72, 14, 3, '2025-12-10', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(73, 14, 3, '2025-12-11', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(74, 14, 3, '2025-12-12', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(75, 14, 3, '2025-12-13', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(76, 14, 4, '2025-12-14', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(77, 14, 3, '2025-12-15', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(78, 14, 3, '2025-12-16', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(79, 14, 3, '2025-12-17', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(80, 14, 3, '2025-12-18', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(81, 14, 3, '2025-12-19', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(82, 14, 3, '2025-12-20', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(83, 14, 4, '2025-12-21', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(84, 14, 3, '2025-12-22', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(85, 14, 3, '2025-12-23', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(86, 14, 3, '2025-12-24', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(87, 14, 3, '2025-12-25', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(88, 14, 3, '2025-12-26', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(89, 14, 3, '2025-12-27', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(90, 14, 4, '2025-12-28', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(91, 14, 3, '2025-12-29', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(92, 14, 3, '2025-12-30', 0, '2025-12-09 04:43:52', '2025-12-09 04:43:52'),
(94, 10, 3, '2025-11-01', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(95, 10, 4, '2025-11-02', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(96, 10, 3, '2025-11-03', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(97, 10, 3, '2025-11-04', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(98, 10, 3, '2025-11-05', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(99, 10, 3, '2025-11-06', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(100, 10, 3, '2025-11-07', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(101, 10, 3, '2025-11-08', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(102, 10, 4, '2025-11-09', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(103, 10, 3, '2025-11-10', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(104, 10, 3, '2025-11-11', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(105, 10, 3, '2025-11-12', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(106, 10, 3, '2025-11-13', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(107, 10, 3, '2025-11-14', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(108, 10, 3, '2025-11-15', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(109, 10, 4, '2025-11-16', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(110, 10, 3, '2025-11-17', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(111, 10, 3, '2025-11-18', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(112, 10, 3, '2025-11-19', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(113, 10, 3, '2025-11-20', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(114, 10, 3, '2025-11-21', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(115, 10, 3, '2025-11-22', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(116, 10, 4, '2025-11-23', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(117, 10, 3, '2025-11-24', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(118, 10, 3, '2025-11-25', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(119, 10, 3, '2025-11-26', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(120, 10, 3, '2025-11-27', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(121, 10, 3, '2025-11-28', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(122, 10, 3, '2025-11-29', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(123, 10, 4, '2025-11-30', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(124, 11, 3, '2025-11-01', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(125, 11, 4, '2025-11-02', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(126, 11, 3, '2025-11-03', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(127, 11, 3, '2025-11-04', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(128, 11, 3, '2025-11-05', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(129, 11, 3, '2025-11-06', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(130, 11, 3, '2025-11-07', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(131, 11, 3, '2025-11-08', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(132, 11, 4, '2025-11-09', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(133, 11, 3, '2025-11-10', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(134, 11, 3, '2025-11-11', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(135, 11, 3, '2025-11-12', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(136, 11, 3, '2025-11-13', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(137, 11, 3, '2025-11-14', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(138, 11, 3, '2025-11-15', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(139, 11, 4, '2025-11-16', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(140, 11, 3, '2025-11-17', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(141, 11, 3, '2025-11-18', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(142, 11, 3, '2025-11-19', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(143, 11, 3, '2025-11-20', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(144, 11, 3, '2025-11-21', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(145, 11, 3, '2025-11-22', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(146, 11, 4, '2025-11-23', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(147, 11, 3, '2025-11-24', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(148, 11, 3, '2025-11-25', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(149, 11, 3, '2025-11-26', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(150, 11, 3, '2025-11-27', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(151, 11, 3, '2025-11-28', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(152, 11, 3, '2025-11-29', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(153, 11, 4, '2025-11-30', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(154, 14, 3, '2025-11-01', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(155, 14, 4, '2025-11-02', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(156, 14, 3, '2025-11-03', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(157, 14, 3, '2025-11-04', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(158, 14, 3, '2025-11-05', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(159, 14, 3, '2025-11-06', 0, '2025-12-11 04:02:22', '2025-12-11 04:02:22'),
(160, 14, 3, '2025-11-07', 0, '2025-12-11 04:02:23', '2025-12-11 04:02:23'),
(161, 14, 3, '2025-11-08', 0, '2025-12-11 04:02:23', '2025-12-11 04:02:23'),
(162, 14, 4, '2025-11-09', 0, '2025-12-11 04:02:23', '2025-12-11 04:02:23'),
(163, 14, 3, '2025-11-10', 0, '2025-12-11 04:02:23', '2025-12-11 04:02:23'),
(164, 14, 3, '2025-11-11', 0, '2025-12-11 04:02:23', '2025-12-11 04:02:23'),
(165, 14, 3, '2025-11-12', 0, '2025-12-11 04:02:23', '2025-12-11 04:02:23'),
(166, 14, 3, '2025-11-13', 0, '2025-12-11 04:02:23', '2025-12-11 04:02:23'),
(167, 14, 3, '2025-11-14', 0, '2025-12-11 04:02:23', '2025-12-11 04:02:23'),
(168, 14, 3, '2025-11-15', 0, '2025-12-11 04:02:23', '2025-12-11 04:02:23'),
(169, 14, 4, '2025-11-16', 0, '2025-12-11 04:02:23', '2025-12-11 04:02:23'),
(170, 14, 3, '2025-11-17', 0, '2025-12-11 04:02:23', '2025-12-11 04:02:23'),
(171, 14, 3, '2025-11-18', 0, '2025-12-11 04:02:23', '2025-12-11 04:02:23'),
(172, 14, 3, '2025-11-19', 0, '2025-12-11 04:02:23', '2025-12-11 04:02:23'),
(173, 14, 3, '2025-11-20', 0, '2025-12-11 04:02:23', '2025-12-11 04:02:23'),
(174, 14, 3, '2025-11-21', 0, '2025-12-11 04:02:23', '2025-12-11 04:02:23'),
(175, 14, 3, '2025-11-22', 0, '2025-12-11 04:02:23', '2025-12-11 04:02:23'),
(176, 14, 4, '2025-11-23', 0, '2025-12-11 04:02:23', '2025-12-11 04:02:23'),
(177, 14, 3, '2025-11-24', 0, '2025-12-11 04:02:23', '2025-12-11 04:02:23'),
(178, 14, 3, '2025-11-25', 0, '2025-12-11 04:02:23', '2025-12-11 04:02:23'),
(179, 14, 3, '2025-11-26', 0, '2025-12-11 04:02:23', '2025-12-11 04:02:23'),
(180, 14, 3, '2025-11-27', 0, '2025-12-11 04:02:23', '2025-12-11 04:02:23'),
(181, 14, 3, '2025-11-28', 0, '2025-12-11 04:02:23', '2025-12-11 04:02:23'),
(182, 14, 3, '2025-11-29', 0, '2025-12-11 04:02:23', '2025-12-11 04:02:23'),
(183, 14, 4, '2025-11-30', 0, '2025-12-11 04:02:23', '2025-12-11 04:02:23'),
(186, 7, 5, '2025-12-01', 0, '2025-12-12 03:50:15', '2025-12-12 04:15:36'),
(188, 8, 6, '2025-12-01', 0, '2025-12-12 04:18:10', '2025-12-12 04:18:10'),
(189, 9, 7, '2025-12-01', 0, '2025-12-12 04:18:13', '2025-12-12 04:18:13'),
(190, 1, 9, '2025-12-01', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(191, 1, 9, '2025-12-02', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(192, 1, 9, '2025-12-03', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(193, 1, 9, '2025-12-04', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(194, 1, 9, '2025-12-05', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(195, 1, 9, '2025-12-06', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(196, 1, 10, '2025-12-07', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(197, 1, 9, '2025-12-08', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(198, 1, 9, '2025-12-09', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(199, 1, 9, '2025-12-10', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(200, 1, 9, '2025-12-11', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(201, 1, 9, '2025-12-12', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(202, 1, 9, '2025-12-13', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(203, 1, 10, '2025-12-14', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(204, 1, 9, '2025-12-15', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(205, 1, 9, '2025-12-16', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(206, 1, 9, '2025-12-17', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(207, 1, 9, '2025-12-18', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(208, 1, 9, '2025-12-19', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(209, 1, 9, '2025-12-20', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(210, 1, 10, '2025-12-21', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(211, 1, 9, '2025-12-22', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(212, 1, 9, '2025-12-23', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(213, 1, 9, '2025-12-24', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(214, 1, 9, '2025-12-25', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(215, 1, 9, '2025-12-26', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(216, 1, 9, '2025-12-27', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(217, 1, 10, '2025-12-28', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(218, 1, 9, '2025-12-29', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(219, 1, 9, '2025-12-30', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(220, 1, 9, '2025-12-31', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(221, 2, 9, '2025-12-01', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(222, 2, 9, '2025-12-02', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(223, 2, 9, '2025-12-03', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(224, 2, 9, '2025-12-04', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(225, 2, 9, '2025-12-05', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(226, 2, 9, '2025-12-06', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(227, 2, 10, '2025-12-07', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(228, 2, 9, '2025-12-08', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(229, 2, 9, '2025-12-09', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(230, 2, 9, '2025-12-10', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(231, 2, 9, '2025-12-11', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(232, 2, 9, '2025-12-12', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(233, 2, 9, '2025-12-13', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(234, 2, 10, '2025-12-14', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(235, 2, 9, '2025-12-15', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(236, 2, 9, '2025-12-16', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(237, 2, 9, '2025-12-17', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(238, 2, 9, '2025-12-18', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(239, 2, 9, '2025-12-19', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(240, 2, 9, '2025-12-20', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(241, 2, 10, '2025-12-21', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(242, 2, 9, '2025-12-22', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(243, 2, 9, '2025-12-23', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(244, 2, 9, '2025-12-24', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(245, 2, 9, '2025-12-25', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(246, 2, 9, '2025-12-26', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(247, 2, 9, '2025-12-27', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(248, 2, 10, '2025-12-28', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(249, 2, 9, '2025-12-29', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(250, 2, 9, '2025-12-30', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(251, 2, 9, '2025-12-31', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(252, 3, 9, '2025-12-01', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(253, 3, 9, '2025-12-02', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(254, 3, 9, '2025-12-03', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(255, 3, 9, '2025-12-04', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(256, 3, 9, '2025-12-05', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(257, 3, 9, '2025-12-06', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(258, 3, 10, '2025-12-07', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(259, 3, 9, '2025-12-08', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(260, 3, 9, '2025-12-09', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(261, 3, 9, '2025-12-10', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(262, 3, 9, '2025-12-11', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(263, 3, 9, '2025-12-12', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(264, 3, 9, '2025-12-13', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(265, 3, 10, '2025-12-14', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(266, 3, 9, '2025-12-15', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(267, 3, 9, '2025-12-16', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(268, 3, 9, '2025-12-17', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(269, 3, 9, '2025-12-18', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(270, 3, 9, '2025-12-19', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(271, 3, 9, '2025-12-20', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(272, 3, 10, '2025-12-21', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(273, 3, 9, '2025-12-22', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(274, 3, 9, '2025-12-23', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(275, 3, 9, '2025-12-24', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(276, 3, 9, '2025-12-25', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(277, 3, 9, '2025-12-26', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(278, 3, 9, '2025-12-27', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(279, 3, 10, '2025-12-28', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(280, 3, 9, '2025-12-29', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(281, 3, 9, '2025-12-30', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(282, 3, 9, '2025-12-31', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(283, 4, 9, '2025-12-01', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(284, 4, 9, '2025-12-02', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(285, 4, 9, '2025-12-03', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(286, 4, 9, '2025-12-04', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(287, 4, 9, '2025-12-05', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(288, 4, 9, '2025-12-06', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(289, 4, 10, '2025-12-07', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(290, 4, 9, '2025-12-08', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(291, 4, 9, '2025-12-09', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(292, 4, 9, '2025-12-10', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(293, 4, 9, '2025-12-11', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(294, 4, 9, '2025-12-12', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(295, 4, 9, '2025-12-13', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(296, 4, 10, '2025-12-14', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(297, 4, 9, '2025-12-15', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(298, 4, 9, '2025-12-16', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(299, 4, 9, '2025-12-17', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(300, 4, 9, '2025-12-18', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(301, 4, 9, '2025-12-19', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(302, 4, 9, '2025-12-20', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(303, 4, 10, '2025-12-21', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(304, 4, 9, '2025-12-22', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(305, 4, 9, '2025-12-23', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(306, 4, 9, '2025-12-24', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(307, 4, 9, '2025-12-25', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(308, 4, 9, '2025-12-26', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(309, 4, 9, '2025-12-27', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(310, 4, 10, '2025-12-28', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(311, 4, 9, '2025-12-29', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(312, 4, 9, '2025-12-30', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(313, 4, 9, '2025-12-31', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(314, 5, 9, '2025-12-01', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(315, 5, 9, '2025-12-02', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(316, 5, 9, '2025-12-03', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(317, 5, 9, '2025-12-04', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(318, 5, 9, '2025-12-05', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(319, 5, 9, '2025-12-06', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(320, 5, 10, '2025-12-07', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(321, 5, 9, '2025-12-08', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(322, 5, 9, '2025-12-09', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(323, 5, 9, '2025-12-10', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(324, 5, 9, '2025-12-11', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(325, 5, 9, '2025-12-12', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(326, 5, 9, '2025-12-13', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(327, 5, 10, '2025-12-14', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(328, 5, 9, '2025-12-15', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(329, 5, 9, '2025-12-16', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(330, 5, 9, '2025-12-17', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(331, 5, 9, '2025-12-18', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(332, 5, 9, '2025-12-19', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(333, 5, 9, '2025-12-20', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(334, 5, 10, '2025-12-21', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(335, 5, 9, '2025-12-22', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(336, 5, 9, '2025-12-23', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(337, 5, 9, '2025-12-24', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(338, 5, 9, '2025-12-25', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(339, 5, 9, '2025-12-26', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(340, 5, 9, '2025-12-27', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(341, 5, 10, '2025-12-28', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(342, 5, 9, '2025-12-29', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(343, 5, 9, '2025-12-30', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(344, 5, 9, '2025-12-31', 0, '2025-12-17 04:23:47', '2025-12-17 04:23:47'),
(345, 6, 11, '2025-12-01', 0, '2025-12-22 04:31:24', '2025-12-22 04:31:24'),
(346, 6, 11, '2025-12-02', 0, '2025-12-22 04:31:24', '2025-12-22 04:31:24'),
(347, 6, 11, '2025-12-03', 0, '2025-12-22 04:31:24', '2025-12-22 04:31:24'),
(348, 6, 11, '2025-12-04', 0, '2025-12-22 04:31:24', '2025-12-22 04:31:24'),
(349, 6, 11, '2025-12-05', 0, '2025-12-22 04:31:24', '2025-12-22 04:31:24'),
(350, 6, 11, '2025-12-06', 0, '2025-12-22 04:31:24', '2025-12-22 04:31:24'),
(351, 6, 12, '2025-12-07', 0, '2025-12-22 04:31:24', '2025-12-22 04:31:24'),
(352, 6, 11, '2025-12-08', 0, '2025-12-22 04:31:24', '2025-12-22 04:31:24'),
(353, 6, 11, '2025-12-09', 0, '2025-12-22 04:31:24', '2025-12-22 04:31:24'),
(354, 6, 11, '2025-12-10', 0, '2025-12-22 04:31:24', '2025-12-22 04:31:24'),
(355, 6, 11, '2025-12-11', 0, '2025-12-22 04:31:24', '2025-12-22 04:31:24'),
(356, 6, 11, '2025-12-12', 0, '2025-12-22 04:31:24', '2025-12-22 04:31:24'),
(357, 6, 11, '2025-12-13', 0, '2025-12-22 04:31:24', '2025-12-22 04:31:24'),
(358, 6, 12, '2025-12-14', 0, '2025-12-22 04:31:24', '2025-12-22 04:31:24'),
(359, 6, 11, '2025-12-15', 0, '2025-12-22 04:31:24', '2025-12-22 04:31:24'),
(360, 6, 11, '2025-12-16', 0, '2025-12-22 04:31:24', '2025-12-22 04:31:24'),
(361, 6, 11, '2025-12-17', 0, '2025-12-22 04:31:24', '2025-12-22 04:31:24'),
(362, 6, 11, '2025-12-18', 0, '2025-12-22 04:31:24', '2025-12-22 04:31:24'),
(363, 6, 11, '2025-12-19', 0, '2025-12-22 04:31:24', '2025-12-22 04:31:24'),
(364, 6, 11, '2025-12-20', 0, '2025-12-22 04:31:24', '2025-12-22 04:31:24'),
(365, 6, 12, '2025-12-21', 0, '2025-12-22 04:31:24', '2025-12-22 04:31:24'),
(366, 6, 11, '2025-12-22', 0, '2025-12-22 04:31:24', '2025-12-22 04:31:24'),
(367, 6, 11, '2025-12-23', 0, '2025-12-22 04:31:24', '2025-12-22 04:31:24'),
(368, 6, 11, '2025-12-24', 0, '2025-12-22 04:31:24', '2025-12-22 04:31:24'),
(369, 6, 11, '2025-12-25', 0, '2025-12-22 04:31:24', '2025-12-22 04:31:24'),
(370, 6, 11, '2025-12-26', 0, '2025-12-22 04:31:24', '2025-12-22 04:31:24'),
(371, 6, 11, '2025-12-27', 0, '2025-12-22 04:31:24', '2025-12-22 04:31:24'),
(372, 6, 12, '2025-12-28', 0, '2025-12-22 04:31:24', '2025-12-22 04:31:24'),
(373, 6, 11, '2025-12-29', 0, '2025-12-22 04:31:24', '2025-12-22 04:31:24'),
(374, 6, 11, '2025-12-30', 0, '2025-12-22 04:31:24', '2025-12-22 04:31:24'),
(375, 6, 11, '2025-12-31', 0, '2025-12-22 04:31:24', '2025-12-22 04:31:24');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('nTBVErgD2KsDx9Pp0wvkogP4HLLwSIZVoPhIzYL4', 7, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo3OntzOjY6Il90b2tlbiI7czo0MDoiRWxITTd2bU5zRWVONno0SlI1emdNOGFTTFhQSWQwNlJkaGJISG1PNyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6NztzOjE3OiJwYXNzd29yZF9oYXNoX3dlYiI7czo2MDoiJDJ5JDEyJG9hWjM2RWxLakQ2OGxRRWlwU1VHa3VKNExVc2ZaVXl5bDZOeERNL1NWaEpQS0ZES2VnOFllIjtzOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czoxODoiaHR0cDovL3NpbXBlZy50ZXN0IjtzOjU6InJvdXRlIjtzOjMwOiJmaWxhbWVudC5hZG1pbi5wYWdlcy5kYXNoYm9hcmQiO31zOjExOiJkZXZpY2VfaW5mbyI7YTo0OntzOjI6ImlwIjtzOjEzOiIxODIuMTMuOTYuMTI4IjtzOjk6ImRldmljZV9pZCI7czoxNjoiNTQxN2UxNjU3ZjU5OThmOCI7czoxMToiZGV2aWNlX2luZm8iO3M6MTExOiJNb3ppbGxhLzUuMCAoV2luZG93cyBOVCAxMC4wOyBXaW42NDsgeDY0KSBBcHBsZVdlYktpdC81MzcuMzYgKEtIVE1MLCBsaWtlIEdlY2tvKSBDaHJvbWUvMTQzLjAuMC4wIFNhZmFyaS81MzcuMzYiO3M6ODoicGxhdGZvcm0iO3M6NToiV2luMzIiO31zOjg6ImZpbGFtZW50IjthOjA6e319', 1766978571);

-- --------------------------------------------------------

--
-- Table structure for table `shifts`
--

CREATE TABLE `shifts` (
  `id` bigint UNSIGNED NOT NULL,
  `unit_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `is_off` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shifts`
--

INSERT INTO `shifts` (`id`, `unit_id`, `name`, `code`, `start_time`, `end_time`, `is_off`, `created_at`, `updated_at`) VALUES
(3, 27, 'Reguler', 'R', '08:30:00', '15:30:00', 0, '2025-12-09 04:43:39', '2025-12-09 04:43:39'),
(4, 27, 'Libur', 'L', NULL, NULL, 1, '2025-12-09 04:43:39', '2025-12-09 04:43:39'),
(5, 2, 'Pagi', 'P', '07:30:00', '14:30:00', 0, '2025-12-09 04:52:37', '2025-12-09 04:53:30'),
(6, 2, 'Siang', 'S', '14:30:00', '21:30:00', 0, '2025-12-09 04:52:37', '2025-12-09 04:53:30'),
(7, 2, 'Libur', 'L', NULL, NULL, 1, '2025-12-09 04:52:37', '2025-12-09 04:52:37'),
(8, 2, 'Malam', 'M', '21:30:00', '07:30:00', 0, '2025-12-09 04:53:30', '2025-12-09 04:53:30'),
(9, 1, 'Reguler', 'R', '07:30:00', '14:30:00', 0, '2025-12-17 04:23:41', '2025-12-17 04:23:41'),
(10, 1, 'Libur', 'L', NULL, NULL, 1, '2025-12-17 04:23:41', '2025-12-17 04:23:41'),
(11, 19, 'Reguler', 'R', '08:00:00', '15:00:00', 0, '2025-12-22 04:31:19', '2025-12-22 04:31:19'),
(12, 19, 'Libur', 'L', NULL, NULL, 1, '2025-12-22 04:31:19', '2025-12-22 04:31:19');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` bigint UNSIGNED NOT NULL,
  `pas` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nik` bigint UNSIGNED NOT NULL,
  `nip` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `birth_place` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `birth_date` date NOT NULL,
  `sex` enum('L','P') COLLATE utf8mb4_unicode_ci NOT NULL,
  `marital` enum('Lajang','Menikah','Cerai Hidup','Cerai Mati') COLLATE utf8mb4_unicode_ci NOT NULL,
  `origin` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `domicile` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `other_phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `other_phone_adverb` enum('Suami','Istri','Orang tua','Wali','Saudara','Lainnya') COLLATE utf8mb4_unicode_ci NOT NULL,
  `entry_date` date NOT NULL,
  `retirement_date` date NOT NULL,
  `staff_status_id` bigint UNSIGNED NOT NULL,
  `chair_id` bigint UNSIGNED NOT NULL,
  `group_id` bigint UNSIGNED NOT NULL,
  `unit_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`id`, `pas`, `nik`, `nip`, `name`, `birth_place`, `birth_date`, `sex`, `marital`, `origin`, `domicile`, `email`, `phone`, `other_phone`, `other_phone_adverb`, `entry_date`, `retirement_date`, `staff_status_id`, `chair_id`, `group_id`, `unit_id`, `created_at`, `updated_at`) VALUES
(1, NULL, 6590387347389418, '3988.4450.068.5\r\n', 'Jumari Prasetyo', 'Malang', '2003-05-02', 'L', 'Cerai Mati', 'Illo quia nihil sapiente harum magnam exercitationem iste iure. Aut alias minima repellat dolores.', 'Ipsum sit et architecto ab quia sunt. Enim voluptatem omnis et quia qui delectus voluptatem ipsam.', 'ipuspasari@example.com', '080426622232', '088386358745', 'Lainnya', '2006-04-28', '2024-10-12', 1, 1, 1, 1, '2025-11-08 00:05:08', '2025-11-08 00:05:08'),
(2, NULL, 9489832995985336, '1611.7761.257.6\r\n', 'Jati Putu Gunarto M.M.', 'Pangkal Pinang', '2013-05-13', 'L', 'Menikah', 'Perspiciatis adipisci autem provident et. Ea nemo quo harum.', 'Aliquid soluta soluta qui. Molestiae provident reprehenderit non similique ut dolorem quos.', 'widodo.ami@example.com', '081889419862', '089157788920', 'Orang tua', '1989-10-14', '1995-03-05', 1, 2, 12, 1, '2025-11-08 00:05:09', '2025-11-08 00:05:09'),
(3, NULL, 4186476680514912, '6262.2961.214.2\r\n', 'Jono Heru Wasita S.Farm', 'Banjarbaru', '1972-09-09', 'L', 'Menikah', 'Veritatis amet quia tenetur nesciunt unde autem rem. Commodi reprehenderit ab ipsa ipsa.', 'Quam fuga aliquam optio deserunt ut iste est et. Illum laboriosam animi amet voluptates odit.', 'saragih.irma@example.org', '081911394823', '082319222508', 'Orang tua', '1983-03-19', '1978-07-23', 1, 3, 12, 1, '2025-11-08 00:05:09', '2025-11-08 00:05:09'),
(4, NULL, 6551572354006950, '4473.8300.284.3\r\n', 'Sakura Hesti Pertiwi S.Pd', 'Administrasi Jakarta Timur', '2011-12-11', 'L', 'Menikah', 'Laudantium et voluptatem placeat sequi quam at culpa distinctio. Fugit molestiae id ut dolor et facilis.', 'Laborum impedit officiis labore. Est voluptatum et itaque rem ex optio temporibus.', 'gasti.prasasta@example.net', '084105229472', '087042204243', 'Istri', '1980-12-17', '2025-01-31', 1, 7, 12, 1, '2025-11-08 00:05:09', '2025-11-08 00:05:09'),
(5, NULL, 5823409825377353, '0139.2867.494.0\r\n', 'Elisa Namaga', 'Manado', '2001-07-02', 'L', 'Menikah', 'Quia itaque voluptates ea necessitatibus. Consequatur cum cupiditate molestias sunt mollitia asperiores voluptatem.', 'Aperiam id numquam nostrum nihil repellendus eos quia. Dolor et explicabo adipisci reprehenderit voluptatum quam dolorem.', 'yance41@example.net', '085391165229', '089701783452', 'Lainnya', '1981-08-09', '2002-08-05', 1, 15, 12, 1, '2025-11-08 00:05:09', '2025-11-08 00:05:09'),
(6, 'profile/01K9H4MH954KWQ4WKA197GD9K6.jpg', 839399756766224, '1179.2961.112.8\r\n', 'Putri Handayani', 'Semarang', '2018-01-05', 'P', 'Cerai Mati', 'Fugiat repudiandae laborum totam quam maiores. Similique et et optio aperiam.', 'Doloremque ea sequi expedita ullam aspernatur. Quas alias eaque ut aliquam.', 'gandi.wijaya@example.org', '0872-1500-3263', '0804-7269-1673', 'Orang tua', '1980-02-10', '1988-06-12', 1, 54, 12, 19, '2025-11-08 00:05:10', '2025-11-08 00:07:05'),
(7, NULL, 5360046759248690, '9655.6882.489.6\r\n', 'Pia Oktaviani', 'Tangerang Selatan', '1975-09-18', 'P', 'Lajang', 'Necessitatibus culpa illo delectus illo voluptate facilis tenetur. Voluptate fugit sint modi consequatur ut consequuntur ullam.', 'Eaque repellat consectetur natus aperiam mollitia vel. Ad nisi sunt id ipsam est est.', 'nasyiah.suci@example.net', '081694542403', '087905654107', 'Saudara', '2003-01-13', '2007-04-14', 2, 16, 3, 2, '2025-11-08 00:05:10', '2025-11-08 00:05:10'),
(8, NULL, 8658101026641727, '8256.9045.544.4\r\n', 'Ulya Hasna Riyanti S.Farm', 'Tanjungbalai', '1995-12-28', 'L', 'Menikah', 'Corrupti mollitia omnis qui. Sed est harum deleniti quaerat id.', 'Voluptatum illum alias nostrum nihil aut. Velit libero eligendi qui facere voluptatibus debitis.', 'dalima.marpaung@example.org', '085643566309', '082934586680', 'Saudara', '1981-05-26', '1981-01-29', 4, 17, 3, 2, '2025-11-08 00:05:10', '2025-11-08 00:05:10'),
(9, NULL, 8429599118654964, '1600.4838.274.3\r\n', 'Mursita Zulkarnain', 'Cimahi', '2018-11-17', 'L', 'Menikah', 'Nihil voluptate assumenda qui error. Aliquid quidem ut optio dolorem quam incidunt.', 'Et quia repudiandae adipisci molestiae. Nemo ut quaerat vitae.', 'drahayu@example.net', '087886490601', '085771482123', 'Suami', '2020-01-30', '2003-08-23', 6, 17, 3, 2, '2025-11-08 00:05:10', '2025-11-08 00:05:10'),
(10, NULL, 2542014620641220, '1450.1565.254.2\r\n', 'Virman Wibowo', 'Banjar', '1988-01-29', 'L', 'Menikah', 'Rerum quis totam tempore. Ipsum aliquid corporis error adipisci porro molestiae consequatur.', 'Consequuntur ullam excepturi enim sequi. Non similique atque voluptates sint.', 'asman49@example.net', '086664484913', '081287605217', 'Suami', '2022-06-28', '1980-06-28', 1, 72, 12, 27, '2025-11-08 00:05:11', '2025-11-08 00:05:11'),
(11, NULL, 9830335275882182, '9005.3807.991.1\r\n', 'Laswi Mustofa', 'Pematangsiantar', '1987-05-18', 'P', 'Cerai Hidup', 'Maxime cum qui dolores magnam eligendi quod autem. Quis sit sit sint illum enim.', 'Saepe molestias ex autem laudantium. Excepturi sunt neque numquam veritatis et facilis cum illum.', 'rangga.anggriawan@example.org', '080380606042', '087204672665', 'Suami', '1988-10-09', '2010-11-13', 2, 73, 12, 27, '2025-11-08 00:05:11', '2025-11-08 00:05:11'),
(14, NULL, 3326111608030041, '2510.0308.441.1', 'Tamam Muhammad', 'Pekalongan', '2003-08-16', 'L', 'Lajang', 'Bojong, Pekalongan', 'Sewon, Bantul', 'akunkunomer03@gmail.com', '0896-8824-1425', '0823-1457-0051', 'Istri', '2025-10-22', '2059-08-16', 1, 72, 12, 27, '2025-11-26 03:09:57', '2025-12-24 04:24:08');

-- --------------------------------------------------------

--
-- Table structure for table `staff_adjustments`
--

CREATE TABLE `staff_adjustments` (
  `id` bigint UNSIGNED NOT NULL,
  `staff_id` bigint UNSIGNED NOT NULL,
  `decree_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `decree_date` date NOT NULL,
  `class` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `decree` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `staff_adjustments`
--

INSERT INTO `staff_adjustments` (`id`, `staff_id`, `decree_number`, `decree_date`, `class`, `decree`, `created_at`, `updated_at`) VALUES
(1, 2, '352/3/SK/YMP/X/2015', '1997-12-09', 'IVa', NULL, '2025-11-08 00:05:09', '2025-11-08 00:05:09'),
(2, 6, '433/11/SK/YMP/VII/2005', '1981-03-13', 'Ie', NULL, '2025-11-08 00:05:10', '2025-12-17 04:42:59'),
(5, 14, '18/05/BJG-PKL/2025', '2025-12-08', 'Xa', 'surat-penyesuaian/01KD79K74DF12V4ZKD7XS8TRFE.pdf', '2025-12-24 04:24:08', '2025-12-24 04:24:08');

-- --------------------------------------------------------

--
-- Table structure for table `staff_administrations`
--

CREATE TABLE `staff_administrations` (
  `id` bigint UNSIGNED NOT NULL,
  `staff_id` bigint UNSIGNED NOT NULL,
  `sip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sip_expiry` date DEFAULT NULL,
  `str` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `str_expiry` date DEFAULT NULL,
  `mcu` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mcu_expiry` date DEFAULT NULL,
  `spk` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `spk_expiry` date DEFAULT NULL,
  `rkk` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rkk_expiry` date DEFAULT NULL,
  `utw` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `utw_expiry` date DEFAULT NULL,
  `is_verified` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `staff_administrations`
--

INSERT INTO `staff_administrations` (`id`, `staff_id`, `sip`, `sip_expiry`, `str`, `str_expiry`, `mcu`, `mcu_expiry`, `spk`, `spk_expiry`, `rkk`, `rkk_expiry`, `utw`, `utw_expiry`, `is_verified`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2025-11-08 00:05:09', '2025-11-08 00:05:09'),
(2, 2, 'sip/01K9H6X08RBKENCCCW5QB4VG3A.pdf', '2025-12-25', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-11-08 00:05:09', '2025-12-19 03:25:43'),
(3, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2025-11-08 00:05:09', '2025-11-08 00:05:09'),
(4, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2025-11-08 00:05:09', '2025-11-08 00:05:09'),
(5, 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2025-11-08 00:05:10', '2025-11-08 00:05:10'),
(6, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2025-11-08 00:05:10', '2025-11-08 00:05:10'),
(7, 7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2025-11-08 00:05:10', '2025-11-08 00:05:10'),
(8, 8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2025-11-08 00:05:10', '2025-11-08 00:05:10'),
(9, 9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2025-11-08 00:05:11', '2025-11-08 00:05:11'),
(10, 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2025-11-08 00:05:11', '2025-11-08 00:05:11'),
(11, 11, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2025-11-08 00:05:11', '2025-11-08 00:05:11'),
(14, 14, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2025-11-26 03:09:57', '2025-11-26 03:09:57');

-- --------------------------------------------------------

--
-- Table structure for table `staff_appointments`
--

CREATE TABLE `staff_appointments` (
  `id` bigint UNSIGNED NOT NULL,
  `staff_id` bigint UNSIGNED NOT NULL,
  `decree_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `decree_date` date NOT NULL,
  `class` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `decree` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `staff_appointments`
--

INSERT INTO `staff_appointments` (`id`, `staff_id`, `decree_number`, `decree_date`, `class`, `decree`, `created_at`, `updated_at`) VALUES
(1, 1, '922/6/SK/YMP/X/1980', '2018-05-16', 'IIIa', NULL, '2025-11-08 00:05:09', '2025-11-08 00:05:09'),
(2, 2, '576/10/SK/YMP/VI/2020', '1972-07-28', 'IIe', NULL, '2025-11-08 00:05:09', '2025-11-08 00:05:09'),
(3, 3, '927/5/SK/YMP/IX/1998', '1976-01-07', 'Vb', NULL, '2025-11-08 00:05:09', '2025-11-08 00:05:09'),
(4, 4, '298/10/SK/YMP/VIII/1986', '1971-07-08', 'IVc', NULL, '2025-11-08 00:05:09', '2025-11-08 00:05:09'),
(5, 5, '922/6/SK/YMP/II/2023', '1975-01-18', 'IVb', NULL, '2025-11-08 00:05:10', '2025-11-08 00:05:10'),
(6, 6, '267/8/SK/YMP/X/2001', '1972-11-21', 'IIId', NULL, '2025-11-08 00:05:10', '2025-12-17 04:42:59'),
(7, 10, '59/7/SK/YMP/IX/1982', '2021-09-10', 'IVe', NULL, '2025-11-08 00:05:11', '2025-11-08 00:05:11'),
(10, 14, '10/05/GCI-SMP/2025', '2025-12-01', 'IXa', 'surat-pengangkatan/01KD78EG062MPTD1PQW7R480WJ.pdf', '2025-12-24 04:04:05', '2025-12-24 04:24:08');

-- --------------------------------------------------------

--
-- Table structure for table `staff_contracts`
--

CREATE TABLE `staff_contracts` (
  `id` bigint UNSIGNED NOT NULL,
  `staff_id` bigint UNSIGNED NOT NULL,
  `contract_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `decree` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `staff_contracts`
--

INSERT INTO `staff_contracts` (`id`, `staff_id`, `contract_number`, `start_date`, `end_date`, `decree`, `created_at`, `updated_at`) VALUES
(1, 7, '663/2/KK/YMP-U/V/2015', '2019-04-14', '2009-01-15', NULL, '2025-11-08 00:05:10', '2025-11-08 00:05:10'),
(2, 11, '875/3/KK/YMP-U/V/2002', '2005-01-13', '2016-01-14', NULL, '2025-11-08 00:05:11', '2025-11-08 00:05:11');

-- --------------------------------------------------------

--
-- Table structure for table `staff_entry_education`
--

CREATE TABLE `staff_entry_education` (
  `id` bigint UNSIGNED NOT NULL,
  `staff_id` bigint UNSIGNED NOT NULL,
  `level` enum('Dokter','Dokter Gigi','Spesialis','S2','S1','Profesi Ners','Profesi Apoteker','DIV','DIII','DIII Anestesi','DIV Anestesi','SMK','SMA','SMP') COLLATE utf8mb4_unicode_ci NOT NULL,
  `institution` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `certificate_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `certificate_date` date NOT NULL,
  `certificate` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nonformal_education` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adverb` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `staff_entry_education`
--

INSERT INTO `staff_entry_education` (`id`, `staff_id`, `level`, `institution`, `certificate_number`, `certificate_date`, `certificate`, `nonformal_education`, `adverb`, `created_at`, `updated_at`) VALUES
(1, 1, 'Spesialis', 'Fa Suwarno (Persero) Tbk', 'DN-92/BAN-PT/2001/88023', '1997-12-10', NULL, NULL, NULL, '2025-11-08 00:05:09', '2025-11-08 00:05:09'),
(2, 2, 'SMA', 'UD Manullang', 'DN-63/BAN-PT/1992/30141', '1976-03-08', NULL, NULL, NULL, '2025-11-08 00:05:09', '2025-11-08 00:05:09'),
(3, 3, 'Profesi Ners', 'PT Yuliarti', 'DN-76/BAN-PT/2004/39315', '1999-08-18', NULL, NULL, NULL, '2025-11-08 00:05:09', '2025-11-08 00:05:09'),
(4, 4, 'SMA', 'UD Saragih', 'DN-7/BAN-PT/2023/11351', '2008-01-18', NULL, NULL, NULL, '2025-11-08 00:05:09', '2025-11-08 00:05:09'),
(5, 5, 'Profesi Ners', 'Yayasan Mulyani Maryati', 'DN-65/BAN-PT/1971/23726', '1977-05-10', NULL, NULL, NULL, '2025-11-08 00:05:10', '2025-11-08 00:05:10'),
(6, 6, 'SMP', 'CV Rahimah Permadi (Persero) Tbk', 'DN-2/BAN-PT/1988/28303', '2007-12-01', 'ijazah-awal/01KCN9WPQ1N0NK41N5YYFS2R7V.pdf', NULL, NULL, '2025-11-08 00:05:10', '2025-12-17 04:42:59'),
(7, 7, 'Profesi Ners', 'Fa Anggriawan Saputra', 'DN-20/BAN-PT/2003/91169', '2013-12-06', NULL, NULL, NULL, '2025-11-08 00:05:10', '2025-11-08 00:05:10'),
(8, 8, 'Profesi Apoteker', 'Perum Mangunsong Rahayu Tbk', 'DN-31/BAN-PT/1988/98672', '1986-01-24', NULL, NULL, NULL, '2025-11-08 00:05:10', '2025-11-08 00:05:10'),
(9, 9, 'Dokter Gigi', 'UD Namaga', 'DN-4/BAN-PT/2001/13526', '2016-04-28', NULL, NULL, NULL, '2025-11-08 00:05:11', '2025-11-08 00:05:11'),
(10, 10, 'DIII Anestesi', 'UD Siregar Oktaviani (Persero) Tbk', 'DN-36/BAN-PT/2008/90059', '1980-08-07', NULL, NULL, NULL, '2025-11-08 00:05:11', '2025-11-08 00:05:11'),
(11, 11, 'SMP', 'CV Uyainah Pudjiastuti', 'DN-48/BAN-PT/2021/72277', '2001-07-08', NULL, NULL, NULL, '2025-11-08 00:05:11', '2025-11-08 00:05:11'),
(12, 14, 'S1', 'Universitas Teknologi Yogyakarta', 'DN-18/BAN-PT/2025/10518', '2025-07-11', 'ijazah-awal/01KAZ4MC9WP4JEP9JG4RN9M8FB.pdf', 'MDS IV', 'Lulus 2025', '2025-11-26 03:52:04', '2025-12-24 04:24:08');

-- --------------------------------------------------------

--
-- Table structure for table `staff_performances`
--

CREATE TABLE `staff_performances` (
  `id` bigint UNSIGNED NOT NULL,
  `staff_id` bigint UNSIGNED NOT NULL,
  `period_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `staff_performances`
--

INSERT INTO `staff_performances` (`id`, `staff_id`, `period_id`, `title`, `description`, `created_at`, `updated_at`) VALUES
(1, 9, 1, 'TEs', 'TESSSIBIBIBBBIBISCBIBSCIBSIHCB', '2025-12-05 06:27:19', '2025-12-05 06:27:19'),
(2, 11, 1, 'iadsbifbsdvsidvb', 'aosndoansdonaosd oasndoanodsoads iajsdonaosd jdsanf', '2025-12-05 08:15:40', '2025-12-05 08:15:40'),
(5, 14, 1, 'asidnasd', 'bidsvibhnoij\ndsnclsdlkcsd\nlksmdlcscd', '2025-12-08 02:42:10', '2025-12-08 02:42:10');

-- --------------------------------------------------------

--
-- Table structure for table `staff_statuses`
--

CREATE TABLE `staff_statuses` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `staff_statuses`
--

INSERT INTO `staff_statuses` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Tetap', '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(2, 'Kontrak', '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(3, 'Parttime', '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(4, 'Training', '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(5, 'PHL', '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(6, 'Internship', '2025-11-08 00:05:06', '2025-11-08 00:05:06');

-- --------------------------------------------------------

--
-- Table structure for table `staff_trainings`
--

CREATE TABLE `staff_trainings` (
  `id` bigint UNSIGNED NOT NULL,
  `staff_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `training_date` date NOT NULL,
  `duration` int NOT NULL,
  `certificate` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `staff_trainings`
--

INSERT INTO `staff_trainings` (`id`, `staff_id`, `name`, `description`, `training_date`, `duration`, `certificate`, `notes`, `created_at`, `updated_at`) VALUES
(1, 6, 'Refreshing Code Red', 'test', '2025-12-01', 3, NULL, NULL, '2025-12-04 07:31:01', '2025-12-04 07:31:01'),
(2, 6, 'TESSS2', NULL, '2025-12-03', 5, NULL, NULL, '2025-12-04 07:50:54', '2025-12-04 07:50:54');

-- --------------------------------------------------------

--
-- Table structure for table `staff_work_education`
--

CREATE TABLE `staff_work_education` (
  `id` bigint UNSIGNED NOT NULL,
  `staff_id` bigint UNSIGNED NOT NULL,
  `level` enum('Dokter','Dokter Gigi','Spesialis','S2','S1','Profesi Ners','Profesi Apoteker','DIV','DIII','DIII Anestesi','DIV Anestesi','SMK','SMA','SMP') COLLATE utf8mb4_unicode_ci NOT NULL,
  `major` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `institution` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `certificate_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `certificate_date` date DEFAULT NULL,
  `certificate` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `staff_work_education`
--

INSERT INTO `staff_work_education` (`id`, `staff_id`, `level`, `major`, `institution`, `certificate_number`, `certificate_date`, `certificate`, `created_at`, `updated_at`) VALUES
(1, 1, 'Profesi Ners', 'Dignissimos ut ipsum incidunt et quas aut dolores et.', 'Yayasan Wijayanti Rajata', 'DN-46/BAN-PT/2004/88075', '1983-05-13', NULL, '2025-11-08 00:05:09', '2025-11-08 00:05:09'),
(2, 2, 'DIII Anestesi', 'Eligendi suscipit deserunt alias dolor accusantium non.', 'PT Pudjiastuti Yolanda Tbk', 'DN-32/BAN-PT/1976/51371', '1975-09-26', NULL, '2025-11-08 00:05:09', '2025-11-08 00:05:09'),
(3, 4, 'S1', 'Ullam eius non corporis assumenda nisi.', 'Yayasan Marpaung (Persero) Tbk', 'DN-31/BAN-PT/2010/60128', '1996-08-29', NULL, '2025-11-08 00:05:09', '2025-11-08 00:05:09'),
(4, 10, 'DIII Anestesi', 'Deserunt fugiat cupiditate alias nobis eaque.', 'Perum Palastri Mangunsong', 'DN-19/BAN-PT/1981/10505', '1999-03-14', NULL, '2025-11-08 00:05:11', '2025-11-08 00:05:11');

-- --------------------------------------------------------

--
-- Table structure for table `staff_work_experiences`
--

CREATE TABLE `staff_work_experiences` (
  `id` bigint UNSIGNED NOT NULL,
  `staff_id` bigint UNSIGNED NOT NULL,
  `institution` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `work_length` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `admission` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `certificate` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `staff_work_experiences`
--

INSERT INTO `staff_work_experiences` (`id`, `staff_id`, `institution`, `work_length`, `admission`, `certificate`, `created_at`, `updated_at`) VALUES
(1, 2, 'PJ Rajasa', '3years', 'Perspiciatis eos quis voluptas ratione non possimus.', NULL, '2025-11-08 00:05:09', '2025-11-08 00:05:09'),
(2, 5, 'UD Budiyanto Hariyah', '8years', 'Enim sit est sed ad voluptas iste sint et.', NULL, '2025-11-08 00:05:10', '2025-11-08 00:05:10'),
(3, 6, 'PT Wastuti (Persero) Tbk', '6 tahun', 'Et non ut qui aspernatur est vel.', NULL, '2025-11-08 00:05:10', '2025-12-17 04:42:59'),
(4, 9, 'CV Purwanti Tbk', '5years', 'Officiis neque dolorum repudiandae quibusdam sunt sunt eum aut.', NULL, '2025-11-08 00:05:11', '2025-11-08 00:05:11');

-- --------------------------------------------------------

--
-- Table structure for table `system_rules`
--

CREATE TABLE `system_rules` (
  `id` bigint UNSIGNED NOT NULL,
  `group` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'string',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_rules`
--

INSERT INTO `system_rules` (`id`, `group`, `key`, `value`, `type`, `description`, `created_at`, `updated_at`) VALUES
(1, 'company', 'company_name', 'RSU Mitra Paramedika', 'string', 'Nama Perusahaan', '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(2, 'company', 'company_address', 'Widomartani, Ngemplak, Sleman, DIY', 'string', 'Alamat Perusahaan', '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(3, 'company', 'company_email', 'rsumpyk@gmail.com', 'string', 'Email Perusahaan', '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(4, 'system', 'system_name', 'Sistem Informasi Manajemen Tenaga dan Pegawai', 'string', 'Nama Sistem', '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(5, 'policy', 'max_leave_days', '12', 'integer', 'Maksimal Cuti Setahun', '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(6, 'policy', 'max_permission_days', '6', 'integer', 'Maksimal Izin Setahun', '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(7, 'system', 'ip', '182.13', 'string', 'IP untuk Absen', '2025-11-13 07:21:45', '2025-12-29 03:15:37'),
(8, 'system', 'lat', '-7.713892', 'string', 'Lattitude Rumah Sakit', '2025-11-17 04:18:03', '2025-11-17 04:18:03'),
(9, 'system', 'lng', '110.448391', 'string', 'Longitude Rumah Sakit', '2025-11-17 04:18:47', '2025-11-17 04:18:47');

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `leader_id` bigint UNSIGNED DEFAULT NULL,
  `work_system` enum('Tetap','Shift') COLLATE utf8mb4_unicode_ci DEFAULT 'Tetap',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`id`, `name`, `leader_id`, `work_system`, `created_at`, `updated_at`) VALUES
(1, 'Manajerial', 1, 'Tetap', '2025-11-08 00:05:06', '2025-11-08 00:05:10'),
(2, 'HD', 16, 'Shift', '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(3, 'OK', 18, 'Shift', '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(4, 'POLI', 20, 'Shift', '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(5, 'BIMA', 22, 'Shift', '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(6, 'SHINTA', 24, 'Shift', '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(7, 'RAMA', 26, 'Shift', '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(8, 'ICU', 28, 'Shift', '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(9, 'UGD', 30, 'Shift', '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(10, 'VK KIA', 33, 'Shift', '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(11, 'Farmasi', 35, 'Shift', '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(12, 'Laboratorium', 39, 'Shift', '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(13, 'Radiologi', 41, 'Shift', '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(14, 'Pendaftaran', 43, 'Shift', '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(15, 'Rekam Medis', 45, 'Shift', '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(16, 'Casemix', 47, 'Shift', '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(17, 'Keuangan', NULL, 'Shift', '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(18, 'Kassa', 50, 'Shift', '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(19, 'Sekretariat, SDM Diklat, Humas Marketing', NULL, 'Tetap', '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(20, 'Fisioterapi', NULL, 'Shift', '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(21, 'Gizi', 57, 'Shift', '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(22, 'Logistik', NULL, 'Shift', '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(23, 'Sanitasi & Central Sterile Supply Departrment (CSSD)', 61, 'Tetap', '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(24, 'Elektromedis & Teknis', 64, 'Tetap', '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(25, 'Satuan Keamanan (SATPAM)', 67, 'Shift', '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(26, 'Umum', 67, 'Shift', '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(27, 'Sistem Informasi Rumah Sakit (SIRS)', NULL, 'Tetap', '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(28, 'Laundry', 74, 'Shift', '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(29, 'Driver', NULL, 'Shift', '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(30, 'Dokter Umum', 77, 'Shift', '2025-11-08 00:05:06', '2025-11-08 00:05:06'),
(31, 'Poli Gigi', NULL, 'Shift', '2025-11-08 00:05:06', '2025-11-08 00:05:06');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL,
  `staff_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role_id`, `staff_id`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@gmail.com', '$2y$12$lOsx00U60bhHbW.vDKDWr.BSiPi1eKURmlZcgi/QdQj1tA8giOusW', 1, NULL, '2025-11-08 00:05:08', '2025-11-08 00:05:08'),
(2, 'Jumari Prasetyo', 'ipuspasari@example.com', '$2y$12$i9e3S7pkyD229tVCgAujL.tluY9iksUhJAoXI20oeIuZ.hXyNCBrO', 2, 1, '2025-11-08 00:05:09', '2025-11-08 00:05:09'),
(3, 'Jati Putu Gunarto M.M.', 'widodo.ami@example.com', '$2y$12$sAdPe/aS6Ni.QzSF3nElJed7wbzq2dSfcI0PPzzmtmMfuGMlphmJu', 2, 2, '2025-11-08 00:05:09', '2025-11-08 00:05:09'),
(4, 'Jono Heru Wasita S.Farm', 'saragih.irma@example.org', '$2y$12$7B9WSjFhxq.W1/mmQAhfdu2p0ZAQZnQk6GJXFtB4lPu2ogyHkzmUW', 2, 3, '2025-11-08 00:05:09', '2025-11-08 00:05:09'),
(5, 'Sakura Hesti Pertiwi S.Pd', 'gasti.prasasta@example.net', '$2y$12$.XxnmttktnZMbWHCDb2Dp.OaIDV6dlFRJSkybURzCdA3cxNuSBRAK', 2, 4, '2025-11-08 00:05:09', '2025-11-08 00:05:09'),
(6, 'Elisa Namaga', 'yance41@example.net', '$2y$12$0GQdp6Pa9m6wjO7fSrFGs.0kUqy8eaH4vXBbnANoAA4jMGlR/ZsdC', 2, 5, '2025-11-08 00:05:10', '2025-11-08 00:05:10'),
(7, 'Putri Handayani', 'gandi.wijaya@example.org', '$2y$12$oaZ36ElKjD68lQEipSUGkuJ4LUsfZUyyl6NxDM/SVhJPKFDKeg8Ye', 1, 6, '2025-11-08 00:05:10', '2025-11-08 00:05:10'),
(8, 'Pia Oktaviani', 'nasyiah.suci@example.net', '$2y$12$ZZa1C2mE3ovb7DU5B71FgeUq4q2zTGLJGjhMXBZRYEIHYR7jIe.qe', 2, 7, '2025-11-08 00:05:10', '2025-11-08 00:05:10'),
(9, 'Ulya Hasna Riyanti S.Farm', 'dalima.marpaung@example.org', '$2y$12$iIXU4jSisMoREMcS.u4Vmuwk3NIhHJgHOq7v.9qpQMFfF7L7IMoze', 2, 8, '2025-11-08 00:05:10', '2025-11-08 00:05:10'),
(10, 'Mursita Zulkarnain', 'drahayu@example.net', '$2y$12$52PCz0PAAgqg4KrsNZV60OEOp9Xc97qjScwqqqkA9Zc31Mjx7MwBO', 2, 9, '2025-11-08 00:05:11', '2025-11-08 00:05:11'),
(11, 'Virman Wibowo', 'asman49@example.net', '$2y$12$VrHCatgMM3gChAZad3RhVO3nDxvtlz30.yOq14m6w9Mw25tpbv7x.', 2, 10, '2025-11-08 00:05:11', '2025-11-08 00:05:11'),
(12, 'Laswi Mustofa', 'rangga.anggriawan@example.org', '$2y$12$wsDsPssPA9LJ.TJYPiuHxuhnbrdvD1b3pKcmWLzxmuf1ltT0Taa9C', 2, 11, '2025-11-08 00:05:11', '2025-11-08 00:05:11'),
(15, 'Tamam Muhammad', 'akunkunomer03@gmail.com', '$2y$12$IZAT.3xQs/WGjqhuWb6uYuBNmfuBWjYKPkFxhGR0kEY1UGeg8kiu2', 2, 14, '2025-11-26 03:09:57', '2025-11-26 03:09:57');

-- --------------------------------------------------------

--
-- Table structure for table `work_histories`
--

CREATE TABLE `work_histories` (
  `id` bigint UNSIGNED NOT NULL,
  `staff_id` bigint UNSIGNED NOT NULL,
  `unit_id` bigint UNSIGNED NOT NULL,
  `chair_id` bigint UNSIGNED NOT NULL,
  `staff_status_id` bigint UNSIGNED NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `decree_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `decree_date` date DEFAULT NULL,
  `class` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `decree` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `work_histories`
--

INSERT INTO `work_histories` (`id`, `staff_id`, `unit_id`, `chair_id`, `staff_status_id`, `start_date`, `end_date`, `decree_number`, `decree_date`, `class`, `decree`, `created_at`, `updated_at`) VALUES
(1, 14, 27, 72, 6, '2025-10-22', '2025-12-01', NULL, NULL, NULL, NULL, NULL, '2025-12-24 04:04:05'),
(10, 14, 27, 72, 1, '2025-12-01', '2025-12-07', '10/05/GCI-SMP/2025', '2025-12-01', 'IXa', 'surat-pengangkatan/01KD78EG062MPTD1PQW7R480WJ.pdf', '2025-12-24 04:04:05', '2025-12-24 04:06:46'),
(13, 14, 27, 72, 1, '2025-12-08', NULL, '18/05/BJG-PKL/2025', '2025-12-08', 'Xa', 'surat-penyesuaian/01KD79K74DF12V4ZKD7XS8TRFE.pdf', '2025-12-24 04:24:08', '2025-12-24 04:24:08');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `chairs`
--
ALTER TABLE `chairs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chairs_unit_id` (`unit_id`) USING BTREE;

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `leaves`
--
ALTER TABLE `leaves`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leaves_staff_id` (`staff_id`),
  ADD KEY `leaves_replacement_id` (`replacement_id`),
  ADD KEY `leaves_approver_id` (`approver_id`),
  ADD KEY `leaves_verified_by` (`verified_by`),
  ADD KEY `leaves_known_by` (`known_by`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indexes for table `overtimes`
--
ALTER TABLE `overtimes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `overtimes_staff_id` (`staff_id`),
  ADD KEY `overtimes_month_year_staff_id_index` (`month_year`,`staff_id`),
  ADD KEY `overtimes_known_by` (`known_by`),
  ADD KEY `overtimes_verified_by` (`verified_by`),
  ADD KEY `known_by` (`known_by`),
  ADD KEY `verified_by` (`verified_by`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `performance_appraisals`
--
ALTER TABLE `performance_appraisals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `performance_appraisals_target_id` (`target_id`),
  ADD KEY `performance_appraisals_appraiser_id` (`appraiser_id`) USING BTREE;

--
-- Indexes for table `performance_periods`
--
ALTER TABLE `performance_periods`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `presences`
--
ALTER TABLE `presences`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_staff_date` (`staff_id`,`presence_date`),
  ADD UNIQUE KEY `unique_device_date` (`fingerprint`,`presence_date`);

--
-- Indexes for table `pre_staff`
--
ALTER TABLE `pre_staff`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pre_staff_nik_unique` (`nik`),
  ADD UNIQUE KEY `pre_staff_nip_unique` (`nip`),
  ADD UNIQUE KEY `pre_staff_email_unique` (`email`),
  ADD UNIQUE KEY `pre_staff_phone_unique` (`phone`),
  ADD KEY `pre_staff_staff_status_id` (`staff_status_id`),
  ADD KEY `pre_staff_chair_id` (`chair_id`),
  ADD KEY `pre_staff_group_id` (`group_id`),
  ADD KEY `pre_staff_unit_id` (`unit_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `schedules_staff_id_schedule_date_unique` (`staff_id`,`schedule_date`),
  ADD KEY `schedules_shift_id` (`shift_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `shifts`
--
ALTER TABLE `shifts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shifts_unit_id` (`unit_id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `staff_nik_unique` (`nik`),
  ADD UNIQUE KEY `staff_nip_unique` (`nip`),
  ADD UNIQUE KEY `staff_email_unique` (`email`),
  ADD UNIQUE KEY `staff_phone_unique` (`phone`),
  ADD UNIQUE KEY `staff_other_phone_unique` (`other_phone`),
  ADD KEY `staff_staff_status_id` (`staff_status_id`),
  ADD KEY `staff_chair_id` (`chair_id`),
  ADD KEY `staff_group_id` (`group_id`),
  ADD KEY `staff_unit_id` (`unit_id`);

--
-- Indexes for table `staff_adjustments`
--
ALTER TABLE `staff_adjustments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `staff_adjustments_staff_id` (`staff_id`);

--
-- Indexes for table `staff_administrations`
--
ALTER TABLE `staff_administrations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `staff_administration_staff_id` (`staff_id`);

--
-- Indexes for table `staff_appointments`
--
ALTER TABLE `staff_appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `staff_appointments_staff_id` (`staff_id`);

--
-- Indexes for table `staff_contracts`
--
ALTER TABLE `staff_contracts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `staff_contracts_staff_id` (`staff_id`);

--
-- Indexes for table `staff_entry_education`
--
ALTER TABLE `staff_entry_education`
  ADD PRIMARY KEY (`id`),
  ADD KEY `staff_entry_education_staff_id` (`staff_id`);

--
-- Indexes for table `staff_performances`
--
ALTER TABLE `staff_performances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `staff_performances_staff_id` (`staff_id`),
  ADD KEY `staff_performances_period_id` (`period_id`);

--
-- Indexes for table `staff_statuses`
--
ALTER TABLE `staff_statuses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `staff_trainings`
--
ALTER TABLE `staff_trainings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `staff_trainings_staff_id` (`staff_id`);

--
-- Indexes for table `staff_work_education`
--
ALTER TABLE `staff_work_education`
  ADD PRIMARY KEY (`id`),
  ADD KEY `staff_work_education_staff_id` (`staff_id`);

--
-- Indexes for table `staff_work_experiences`
--
ALTER TABLE `staff_work_experiences`
  ADD PRIMARY KEY (`id`),
  ADD KEY `staff_work_experiences_staff_id` (`staff_id`);

--
-- Indexes for table `system_rules`
--
ALTER TABLE `system_rules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `system_rules_key_unique` (`key`),
  ADD KEY `system_rules_group_index` (`group`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`),
  ADD KEY `units_leader_id` (`leader_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_role_id` (`role_id`),
  ADD KEY `users_staff_id` (`staff_id`);

--
-- Indexes for table `work_histories`
--
ALTER TABLE `work_histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `work_histories_staff_id` (`staff_id`),
  ADD KEY `work_histories_unit_id` (`unit_id`),
  ADD KEY `work_histories_chair_id` (`chair_id`),
  ADD KEY `work_histories_staff_status_id` (`staff_status_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chairs`
--
ALTER TABLE `chairs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `leaves`
--
ALTER TABLE `leaves`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `overtimes`
--
ALTER TABLE `overtimes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `performance_appraisals`
--
ALTER TABLE `performance_appraisals`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `performance_periods`
--
ALTER TABLE `performance_periods`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `presences`
--
ALTER TABLE `presences`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `pre_staff`
--
ALTER TABLE `pre_staff`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=376;

--
-- AUTO_INCREMENT for table `shifts`
--
ALTER TABLE `shifts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `staff_adjustments`
--
ALTER TABLE `staff_adjustments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `staff_administrations`
--
ALTER TABLE `staff_administrations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `staff_appointments`
--
ALTER TABLE `staff_appointments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `staff_contracts`
--
ALTER TABLE `staff_contracts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `staff_entry_education`
--
ALTER TABLE `staff_entry_education`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `staff_performances`
--
ALTER TABLE `staff_performances`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `staff_statuses`
--
ALTER TABLE `staff_statuses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `staff_trainings`
--
ALTER TABLE `staff_trainings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `staff_work_education`
--
ALTER TABLE `staff_work_education`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `staff_work_experiences`
--
ALTER TABLE `staff_work_experiences`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `system_rules`
--
ALTER TABLE `system_rules`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `work_histories`
--
ALTER TABLE `work_histories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chairs`
--
ALTER TABLE `chairs`
  ADD CONSTRAINT `chairs_ibfk_1` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Constraints for table `leaves`
--
ALTER TABLE `leaves`
  ADD CONSTRAINT `leaves_approver_id` FOREIGN KEY (`approver_id`) REFERENCES `staff` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `leaves_known_by` FOREIGN KEY (`known_by`) REFERENCES `staff` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  ADD CONSTRAINT `leaves_replacement_id` FOREIGN KEY (`replacement_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `leaves_staff_id` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `leaves_verified_by` FOREIGN KEY (`verified_by`) REFERENCES `staff` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT;

--
-- Constraints for table `overtimes`
--
ALTER TABLE `overtimes`
  ADD CONSTRAINT `overtimes_known_by` FOREIGN KEY (`known_by`) REFERENCES `staff` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  ADD CONSTRAINT `overtimes_staff_id` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `overtimes_verified_by` FOREIGN KEY (`verified_by`) REFERENCES `staff` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT;

--
-- Constraints for table `performance_appraisals`
--
ALTER TABLE `performance_appraisals`
  ADD CONSTRAINT `performance_appraisals_reviewer_id` FOREIGN KEY (`appraiser_id`) REFERENCES `staff` (`id`),
  ADD CONSTRAINT `performance_appraisals_target_id` FOREIGN KEY (`target_id`) REFERENCES `staff_performances` (`id`);

--
-- Constraints for table `presences`
--
ALTER TABLE `presences`
  ADD CONSTRAINT `presences_staff_id` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pre_staff`
--
ALTER TABLE `pre_staff`
  ADD CONSTRAINT `pre_staff_chair_id` FOREIGN KEY (`chair_id`) REFERENCES `chairs` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pre_staff_group_id` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pre_staff_staff_status_id` FOREIGN KEY (`staff_status_id`) REFERENCES `staff_statuses` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pre_staff_unit_id` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_shift_id` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `schedules_staff_id` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `shifts`
--
ALTER TABLE `shifts`
  ADD CONSTRAINT `shifts_unit_id` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `staff`
--
ALTER TABLE `staff`
  ADD CONSTRAINT `staff_chair_id` FOREIGN KEY (`chair_id`) REFERENCES `chairs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `staff_group_id` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `staff_staff_status_id` FOREIGN KEY (`staff_status_id`) REFERENCES `staff_statuses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `staff_unit_id` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `staff_adjustments`
--
ALTER TABLE `staff_adjustments`
  ADD CONSTRAINT `staff_adjustments_staff_id` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `staff_administrations`
--
ALTER TABLE `staff_administrations`
  ADD CONSTRAINT `staff_administration_staff_id` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `staff_appointments`
--
ALTER TABLE `staff_appointments`
  ADD CONSTRAINT `staff_appointments_staff_id` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `staff_contracts`
--
ALTER TABLE `staff_contracts`
  ADD CONSTRAINT `staff_contracts_staff_id` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `staff_entry_education`
--
ALTER TABLE `staff_entry_education`
  ADD CONSTRAINT `staff_entry_education_staff_id` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `staff_performances`
--
ALTER TABLE `staff_performances`
  ADD CONSTRAINT `staff_performances_period_id` FOREIGN KEY (`period_id`) REFERENCES `performance_periods` (`id`),
  ADD CONSTRAINT `staff_performances_staff_id` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`);

--
-- Constraints for table `staff_trainings`
--
ALTER TABLE `staff_trainings`
  ADD CONSTRAINT `staff_trainings_staff_id` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `staff_work_education`
--
ALTER TABLE `staff_work_education`
  ADD CONSTRAINT `staff_work_education_staff_id` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `staff_work_experiences`
--
ALTER TABLE `staff_work_experiences`
  ADD CONSTRAINT `staff_work_experiences_staff_id` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `units`
--
ALTER TABLE `units`
  ADD CONSTRAINT `units_leader_id` FOREIGN KEY (`leader_id`) REFERENCES `chairs` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_role_id` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `users_staff_id` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `work_histories`
--
ALTER TABLE `work_histories`
  ADD CONSTRAINT `work_histories_chair_id` FOREIGN KEY (`chair_id`) REFERENCES `chairs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `work_histories_staff_id` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `work_histories_staff_status_id` FOREIGN KEY (`staff_status_id`) REFERENCES `staff_statuses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `work_histories_unit_id` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
