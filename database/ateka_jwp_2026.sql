-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 28 Okt 2026 pada 01.02
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ateka_jwp_2026`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('persediaan_barang_alat_tulis_kantor_berbasis_web_cache_5c785c036466adea360111aa28563bfd556b5fba', 'i:1;', 1793063021),
('persediaan_barang_alat_tulis_kantor_berbasis_web_cache_5c785c036466adea360111aa28563bfd556b5fba:timer', 'i:1793063021;', 1793063021);

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `kode_barang` varchar(50) DEFAULT NULL,
  `kd_rincian` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `categories`
--

INSERT INTO `categories` (`id`, `name`, `kode_barang`, `kd_rincian`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Pensil', '1010301001', '10.01.1', 'Kode Barang BMN: 1010301001 (Alat Tulis)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(2, 'Pulpen', '1010301001', '10.01.2', 'Kode Barang BMN: 1010301001 (Alat Tulis)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(3, 'Spidol', '1010301001', '10.01.3', 'Kode Barang BMN: 1010301001 (Alat Tulis)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(4, 'Stabilo', '1010301001', '10.01.4', 'Kode Barang BMN: 1010301001 (Alat Tulis)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(5, 'Tinta Pulpen', '1010301002', '10.02.1', 'Kode Barang BMN: 1010301002 (Tinta Tulis, Tinta Stempel)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(6, 'Binder Clip', '1010301003', '10.03.1', 'Kode Barang BMN: 1010301003 (Penjepit Kertas)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(7, 'Paper Clip', '1010301003', '10.03.2', 'Kode Barang BMN: 1010301003 (Penjepit Kertas)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(8, 'Penghapus', '1010301004', '10.04.1', 'Kode Barang BMN: 1010301004 (Penghapus/Korektor)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(9, 'Tipe Ex', '1010301004', '10.04.2', 'Kode Barang BMN: 1010301004 (Penghapus/Korektor)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(10, 'Buku', '1010301005', '10.05.1', 'Kode Barang BMN: 1010301005 (Buku Tulis)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(11, 'Ordner', '1010301006', '10.06.1', 'Kode Barang BMN: 1010301006 (Ordner Dan Map)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(12, 'Clear Holder', '1010301006', '10.06.2', 'Kode Barang BMN: 1010301006 (Ordner Dan Map)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(13, 'Box File', '1010301006', '10.06.3', 'Kode Barang BMN: 1010301006 (Ordner Dan Map)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(14, 'Sheet Protector', '1010301006', '10.06.4', 'Kode Barang BMN: 1010301006 (Ordner Dan Map)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(15, 'Map', '1010301006', '10.06.5', 'Kode Barang BMN: 1010301006 (Ordner Dan Map)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(16, 'Business File', '1010301006', '10.06.6', 'Kode Barang BMN: 1010301006 (Ordner Dan Map)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(17, 'Penggaris', '1010301007', '10.07.1', 'Kode Barang BMN: 1010301007 (Penggaris)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(18, 'Cutter', '1010301008', '10.08.1', 'Kode Barang BMN: 1010301008 (Cutter (Alat Tulis Kantor))', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(19, 'Lem', '1010301010', '10.10.1', 'Kode Barang BMN: 1010301010 (Alat Perekat)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(20, 'Double Tape', '1010301010', '10.10.2', 'Kode Barang BMN: 1010301010 (Alat Perekat)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(21, 'Selotip', '1010301010', '10.10.3', 'Kode Barang BMN: 1010301010 (Alat Perekat)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(22, 'Lakban', '1010301010', '10.10.4', 'Kode Barang BMN: 1010301010 (Alat Perekat)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(23, 'Label Sticker', '1010301010', '10.10.5', 'Kode Barang BMN: 1010301010 (Alat Perekat)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(24, 'Post It', '1010301010', '10.10.6', 'Kode Barang BMN: 1010301010 (Alat Perekat)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(25, 'Stapler', '1010301012', '10.12.1', 'Kode Barang BMN: 1010301012 (Staples)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(26, 'Isi Staples', '1010301013', '10.13.1', 'Kode Barang BMN: 1010301013 (Isi Staples)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(27, 'Elevated Tray', '1010301999', '19.99.1', 'Kode Barang BMN: 1010301999 (Alat Tulis Kantor Lainnya)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(28, 'Gunting', '1010301999', '19.99.2', 'Kode Barang BMN: 1010301999 (Alat Tulis Kantor Lainnya)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(29, 'Kalkulator', '1010301999', '19.99.3', 'Kode Barang BMN: 1010301999 (Alat Tulis Kantor Lainnya)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(30, 'Pembolong Kertas', '1010301999', '19.99.4', 'Kode Barang BMN: 1010301999 (Alat Tulis Kantor Lainnya)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(31, 'Rautan Pensil', '1010301999', '19.99.5', 'Kode Barang BMN: 1010301999 (Alat Tulis Kantor Lainnya)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(32, 'Tape Cutter', '1010301999', '19.99.6', 'Kode Barang BMN: 1010301999 (Alat Tulis Kantor Lainnya)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(33, 'Tape Dispenser / Dispenser Tape', '1010301999', '19.99.7', 'Kode Barang BMN: 1010301999 (Alat Tulis Kantor Lainnya)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(34, 'Laser pointer', '1010301999', '19.99.8', 'Kode Barang BMN: 1010301999 (Alat Tulis Kantor Lainnya)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(35, 'Bak Stempel', '1010301999', '19.99.9', 'Kode Barang BMN: 1010301999 (Alat Tulis Kantor Lainnya)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(36, 'Book Stand', '1010301999', '19.99.10', 'Kode Barang BMN: 1010301999 (Alat Tulis Kantor Lainnya)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(37, 'V-Tech Desk Organizer', '1010301999', '19.99.11', 'Kode Barang BMN: 1010301999 (Alat Tulis Kantor Lainnya)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(38, 'V-Tech Round Pencil Cup', '1010301999', '19.99.12', 'Kode Barang BMN: 1010301999 (Alat Tulis Kantor Lainnya)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(39, 'Kertas', '1010302001', '20.01.1', 'Kode Barang BMN: 1010302001 (Kertas HVS)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(40, 'Amplop', '1010302004', '20.04.1', 'Kode Barang BMN: 1010302004 (Amplop)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(41, 'Transparant Sheet', '1010303001', '30.01.1', 'Kode Barang BMN: 1010303001 (Transparant Sheet)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(42, 'Pita', '1010304003', '40.03.1', 'Kode Barang BMN: 1010304003 (Pita Printer)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(43, 'Tinta Brother', '1010304004', '40.04.1', 'Kode Barang BMN: 1010304004 (Tinta/Toner Printer)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(44, 'Tinta Canon', '1010304004', '40.04.2', 'Kode Barang BMN: 1010304004 (Tinta/Toner Printer)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(45, 'Tinta Epson', '1010304004', '40.04.3', 'Kode Barang BMN: 1010304004 (Tinta/Toner Printer)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(46, 'Tinta HP', '1010304004', '40.04.4', 'Kode Barang BMN: 1010304004 (Tinta/Toner Printer)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(47, 'Toner Printer', '1010304004', '40.04.5', 'Kode Barang BMN: 1010304004 (Tinta/Toner Printer)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(48, 'Converter / USB HUB', '1010304006', '40.06.1', 'Kode Barang BMN: 1010304006 (USB/Flash Disk)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(49, 'USB', '1010304006', '40.06.2', 'Kode Barang BMN: 1010304006 (USB/Flash Disk)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(50, 'Flashdisk', '1010304006', '40.06.3', 'Kode Barang BMN: 1010304006 (USB/Flash Disk)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(51, 'Mouse Wireless', '1010304010', '40.10.1', 'Kode Barang BMN: 1010304010 (Mouse)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(52, 'Hardisk Eksternal', '1010304999', '49.99.1', 'Kode Barang BMN: 1010304999 (Bahan Komputer Lainnya)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(53, 'Keyboard', '1010304999', '49.99.2', 'Kode Barang BMN: 1010304999 (Bahan Komputer Lainnya)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(54, 'Baterai', '1010306010', '60.10.1', 'Kode Barang BMN: 1010306010 (Batu Baterai)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(55, 'Recorder', '1010301999', '19.99.13', 'Kode Barang BMN: 1010301999 (Alat Tulis Kantor Lainnya)', '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(56, 'Ring O-Bin', '1010301999', '19.99.14', 'Kode Barang BMN: 1010301999 (Alat Tulis Kantor Lainnya)', '2026-10-26 00:34:53', '2026-10-26 00:34:53');

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
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
-- Struktur dari tabel `jobs`
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
-- Struktur dari tabel `job_batches`
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
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_06_06_135939_create_categories_table', 1),
(5, '2026_06_06_135954_create_products_table', 1),
(6, '2026_06_06_140004_create_stock_transactions_table', 1),
(7, '2026_09_23_034031_create_requests_tables', 1),
(8, '2026_09_26_090000_add_distribution_fields_to_requests_table', 1),
(9, '2026_09_26_130000_add_recipient_type_to_stock_transactions_table', 1),
(10, '2026_09_30_010452_add_purchase_unit_fields_to_stock_transactions_table', 1),
(11, '2026_10_05_004930_add_verification_and_approval_fields_to_requests_table', 1),
(12, '2026_10_05_010412_add_procurement_fields_to_requests_table', 1),
(13, '2026_10_05_013500_add_procurement_request_support', 1),
(14, '2026_10_05_054503_add_nip_to_users_table', 1),
(15, '2026_10_07_013033_add_title_to_users_table', 1),
(16, '2026_10_07_024000_create_user_notifications_table', 1),
(17, '2026_10_08_025828_add_distribution_proof_to_requests_table', 1),
(18, '2026_10_17_093000_remove_subdit_role_users', 1),
(19, '2026_10_17_095500_remove_arsiparis_role_users', 1),
(20, '2026_10_17_104500_convert_existing_utc_timestamps_to_wib', 1),
(21, '2026_10_17_113000_add_bmn_and_units_columns_to_products_table', 1),
(22, '2026_10_19_094000_add_is_distributor_to_users_table', 1),
(23, '2026_10_19_111451_add_signed_proof_to_requests_table', 1),
(24, '2026_10_20_103446_add_image_to_products_table', 1),
(25, '2026_10_20_113909_remove_unique_from_products_code', 1),
(26, '2026_10_20_114032_add_kd_rincian_to_categories_table', 1),
(27, '2026_10_20_150034_add_kode_barang_to_categories_table', 1),
(28, '2026_10_21_110003_add_is_fulfilled_to_request_details_table', 1),
(29, '2026_10_25_093500_add_is_report_examiner_to_users_table', 1),
(30, '2026_10_26_080000_update_kasubag_to_kasubbag_position', 2),
(31, '2026_10_27_094000_sync_bmn_catalog_codes_and_categories', 3);

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) NOT NULL,
  `bmn_code` varchar(50) DEFAULT NULL,
  `bmn_group` varchar(150) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `unit` varchar(255) NOT NULL DEFAULT 'pcs',
  `available_units` varchar(255) DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `minimum_stock` int(11) NOT NULL DEFAULT 10,
  `purchase_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `products`
--

INSERT INTO `products` (`id`, `category_id`, `code`, `bmn_code`, `bmn_group`, `name`, `unit`, `available_units`, `stock`, `minimum_stock`, `purchase_price`, `description`, `image`, `created_at`, `updated_at`) VALUES
(1, 1, '10.01.1', '1010301001', 'Alat Tulis', 'Pensil Mekanik Faber Castel', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301001 (Alat Tulis) | Kode Rincian: 10.01.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(2, 1, '10.01.1', '1010301001', 'Alat Tulis', 'Pensil Mekanik Pillot H-165', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301001 (Alat Tulis) | Kode Rincian: 10.01.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(3, 1, '10.01.1', '1010301001', 'Alat Tulis', 'Pensil Mekanik Kenko', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301001 (Alat Tulis) | Kode Rincian: 10.01.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(4, 1, '10.01.1', '1010301001', 'Alat Tulis', 'Pensil Kayu 2B Faber Castel', 'Kotak', 'Kotak', 0, 5, 0.00, 'Satuan Standar: Kotak | BMN: 1010301001 (Alat Tulis) | Kode Rincian: 10.01.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(5, 1, '10.01.1', '1010301001', 'Alat Tulis', 'Pensil Kayu Steadler 2B', 'Kotak', 'Kotak', 0, 5, 0.00, 'Satuan Standar: Kotak | BMN: 1010301001 (Alat Tulis) | Kode Rincian: 10.01.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(6, 1, '10.01.1', '1010301001', 'Alat Tulis', 'Pensil Kayu Steadler HB', 'Kotak', 'Kotak', 0, 5, 0.00, 'Satuan Standar: Kotak | BMN: 1010301001 (Alat Tulis) | Kode Rincian: 10.01.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(7, 1, '10.01.1', '1010301001', 'Alat Tulis', 'Isi Pensil Mekanik 2B', 'Kotak', 'Kotak', 0, 5, 0.00, 'Satuan Standar: Kotak | BMN: 1010301001 (Alat Tulis) | Kode Rincian: 10.01.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(8, 2, '10.01.2', '1010301001', 'Alat Tulis', 'Pulpen Kenko Easy Gel 0,5 mm (warna Merah)', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301001 (Alat Tulis) | Kode Rincian: 10.01.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(9, 2, '10.01.2', '1010301001', 'Alat Tulis', 'Pulpen Kenko Easy Gel 0,5 mm (warna Hitam)', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301001 (Alat Tulis) | Kode Rincian: 10.01.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(10, 2, '10.01.2', '1010301001', 'Alat Tulis', 'Pulpen Kenko Easy Gel 0,5 mm (warna biru)', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301001 (Alat Tulis) | Kode Rincian: 10.01.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(11, 2, '10.01.2', '1010301001', 'Alat Tulis', 'Ballpoint Kenko K-Gold (isi 12)', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301001 (Alat Tulis) | Kode Rincian: 10.01.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(12, 2, '10.01.2', '1010301001', 'Alat Tulis', 'Ballpoint Kenko K1 (isi 12)', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301001 (Alat Tulis) | Kode Rincian: 10.01.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(13, 2, '10.01.2', '1010301001', 'Alat Tulis', 'Pulpen Gel Joyko GP-265 (warna biru)', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301001 (Alat Tulis) | Kode Rincian: 10.01.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(14, 2, '10.01.2', '1010301001', 'Alat Tulis', 'Pulpen Gel Joyko GP-265 (warna hitam)', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301001 (Alat Tulis) | Kode Rincian: 10.01.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(15, 2, '10.01.2', '1010301001', 'Alat Tulis', 'Pulpen Joyko Gel Biru 1.0 mm', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301001 (Alat Tulis) | Kode Rincian: 10.01.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(16, 2, '10.01.2', '1010301001', 'Alat Tulis', 'Puplen Joyko Gel Hitam 1.0 mm', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301001 (Alat Tulis) | Kode Rincian: 10.01.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(17, 2, '10.01.2', '1010301001', 'Alat Tulis', 'Ballpoint AE 7 (isi 12)', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301001 (Alat Tulis) | Kode Rincian: 10.01.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(18, 2, '10.01.2', '1010301001', 'Alat Tulis', 'Ballpoint Balliner Hitam', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301001 (Alat Tulis) | Kode Rincian: 10.01.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(19, 2, '10.01.2', '1010301001', 'Alat Tulis', 'Ballpoint Balliner Biru', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301001 (Alat Tulis) | Kode Rincian: 10.01.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(20, 2, '10.01.2', '1010301001', 'Alat Tulis', 'Ballpoint Faster C6 (isi 12)', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301001 (Alat Tulis) | Kode Rincian: 10.01.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(21, 2, '10.01.2', '1010301001', 'Alat Tulis', 'Ballpoint Faster C600 (isi 12)', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301001 (Alat Tulis) | Kode Rincian: 10.01.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(22, 2, '10.01.2', '1010301001', 'Alat Tulis', 'Ballpoint Standard R3 0,5', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301001 (Alat Tulis) | Kode Rincian: 10.01.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(23, 2, '10.01.2', '1010301001', 'Alat Tulis', 'Ballpoint Greebel Riter 0,5', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301001 (Alat Tulis) | Kode Rincian: 10.01.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(24, 2, '10.01.2', '1010301001', 'Alat Tulis', 'Ballpoint Standard G\'Soft 0,5', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301001 (Alat Tulis) | Kode Rincian: 10.01.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(25, 2, '10.01.2', '1010301001', 'Alat Tulis', 'Ballpoint Zebra Sarasa 1,0 (isi 12)', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301001 (Alat Tulis) | Kode Rincian: 10.01.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(26, 2, '10.01.2', '1010301001', 'Alat Tulis', 'Ballpoint Pilot Balliner (isi 12)', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301001 (Alat Tulis) | Kode Rincian: 10.01.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(27, 2, '10.01.2', '1010301001', 'Alat Tulis', 'Ballpoint Pilot Frixion', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301001 (Alat Tulis) | Kode Rincian: 10.01.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(28, 3, '10.01.3', '1010301001', 'Alat Tulis', 'Spidol Snowman (Whiteboard) warna biru', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301001 (Alat Tulis) | Kode Rincian: 10.01.3', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(29, 3, '10.01.3', '1010301001', 'Alat Tulis', 'Spidol Snowman (Permanent) warna hitam', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301001 (Alat Tulis) | Kode Rincian: 10.01.3', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(30, 3, '10.01.3', '1010301001', 'Alat Tulis', 'Spidol Snowman (Whiteboard) warna hitam', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301001 (Alat Tulis) | Kode Rincian: 10.01.3', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(31, 3, '10.01.3', '1010301001', 'Alat Tulis', 'Spidol Artline 500 (Whiteboard)', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301001 (Alat Tulis) | Kode Rincian: 10.01.3', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(32, 3, '10.01.3', '1010301001', 'Alat Tulis', 'Spidol Artline 70 (Permanent)', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301001 (Alat Tulis) | Kode Rincian: 10.01.3', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(33, 3, '10.01.3', '1010301001', 'Alat Tulis', 'Spidol Gold Diamond', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301001 (Alat Tulis) | Kode Rincian: 10.01.3', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(34, 4, '10.01.4', '1010301001', 'Alat Tulis', 'Stabillo Boss Warna Pink', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301001 (Alat Tulis) | Kode Rincian: 10.01.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(35, 4, '10.01.4', '1010301001', 'Alat Tulis', 'Stabillo Boss Warna Kuning', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301001 (Alat Tulis) | Kode Rincian: 10.01.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(36, 4, '10.01.4', '1010301001', 'Alat Tulis', 'Stabillo Boss Warna Hijau', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301001 (Alat Tulis) | Kode Rincian: 10.01.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(37, 4, '10.01.4', '1010301001', 'Alat Tulis', 'Stabillo Boss Warna Biru', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301001 (Alat Tulis) | Kode Rincian: 10.01.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(38, 4, '10.01.4', '1010301001', 'Alat Tulis', 'Stabillo Boss Warna Orange', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301001 (Alat Tulis) | Kode Rincian: 10.01.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(39, 4, '10.01.4', '1010301001', 'Alat Tulis', 'Stabillo (isi 4 warna pastel)', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010301001 (Alat Tulis) | Kode Rincian: 10.01.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(40, 4, '10.01.4', '1010301001', 'Alat Tulis', 'Stabillo Steadtler', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301001 (Alat Tulis) | Kode Rincian: 10.01.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(41, 4, '10.01.4', '1010301001', 'Alat Tulis', 'Stabillo Boss', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301001 (Alat Tulis) | Kode Rincian: 10.01.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(42, 4, '10.01.4', '1010301001', 'Alat Tulis', 'Stabillo Boss + tempat (isi 6 batang)', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010301001 (Alat Tulis) | Kode Rincian: 10.01.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(43, 5, '10.02.1', '1010301002', 'Tinta Tulis, Tinta Stempel', 'Isi Ulang Tinta Sarasa 0,7 (isi 3)', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301002 (Tinta Tulis, Tinta Stempel) | Kode Rincian: 10.02.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(44, 6, '10.03.1', '1010301003', 'Penjepit Kertas', 'Binder Clip No. 105', 'Doz', 'Doz', 0, 5, 0.00, 'Satuan Standar: Doz | BMN: 1010301003 (Penjepit Kertas) | Kode Rincian: 10.03.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(45, 6, '10.03.1', '1010301003', 'Penjepit Kertas', 'Binder Clip No. 106', 'Doz', 'Doz', 0, 5, 0.00, 'Satuan Standar: Doz | BMN: 1010301003 (Penjepit Kertas) | Kode Rincian: 10.03.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(46, 6, '10.03.1', '1010301003', 'Penjepit Kertas', 'Binder Clip No. 107', 'Doz', 'Doz', 0, 5, 0.00, 'Satuan Standar: Doz | BMN: 1010301003 (Penjepit Kertas) | Kode Rincian: 10.03.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(47, 6, '10.03.1', '1010301003', 'Penjepit Kertas', 'Binder Clip No.111', 'Doz', 'Doz', 0, 5, 0.00, 'Satuan Standar: Doz | BMN: 1010301003 (Penjepit Kertas) | Kode Rincian: 10.03.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(48, 6, '10.03.1', '1010301003', 'Penjepit Kertas', 'Binder Clip No. 155', 'Doz', 'Doz', 0, 5, 0.00, 'Satuan Standar: Doz | BMN: 1010301003 (Penjepit Kertas) | Kode Rincian: 10.03.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(49, 6, '10.03.1', '1010301003', 'Penjepit Kertas', 'Binder Clip No. 200', 'Doz', 'Doz', 0, 5, 0.00, 'Satuan Standar: Doz | BMN: 1010301003 (Penjepit Kertas) | Kode Rincian: 10.03.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(50, 6, '10.03.1', '1010301003', 'Penjepit Kertas', 'Binder Clip No. 260', 'Doz', 'Doz', 0, 5, 0.00, 'Satuan Standar: Doz | BMN: 1010301003 (Penjepit Kertas) | Kode Rincian: 10.03.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(51, 6, '10.03.1', '1010301003', 'Penjepit Kertas', 'Binder Clip No. 280', 'Doz', 'Doz', 0, 5, 0.00, 'Satuan Standar: Doz | BMN: 1010301003 (Penjepit Kertas) | Kode Rincian: 10.03.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(52, 6, '10.03.1', '1010301003', 'Penjepit Kertas', 'Binder Clip No. 300', 'Doz', 'Doz', 0, 5, 0.00, 'Satuan Standar: Doz | BMN: 1010301003 (Penjepit Kertas) | Kode Rincian: 10.03.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(53, 7, '10.03.2', '1010301003', 'Penjepit Kertas', 'Paper Clip No.01', 'Kotak', 'Kotak', 0, 5, 0.00, 'Satuan Standar: Kotak | BMN: 1010301003 (Penjepit Kertas) | Kode Rincian: 10.03.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(54, 7, '10.03.2', '1010301003', 'Penjepit Kertas', 'Paper Clip No.03', 'Kotak', 'Kotak', 0, 5, 0.00, 'Satuan Standar: Kotak | BMN: 1010301003 (Penjepit Kertas) | Kode Rincian: 10.03.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(55, 7, '10.03.2', '1010301003', 'Penjepit Kertas', 'Paper Clip No.05', 'Kotak', 'Kotak', 0, 5, 0.00, 'Satuan Standar: Kotak | BMN: 1010301003 (Penjepit Kertas) | Kode Rincian: 10.03.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(56, 7, '10.03.2', '1010301003', 'Penjepit Kertas', 'Paper Clip Warna', 'Kotak', 'Kotak', 0, 5, 0.00, 'Satuan Standar: Kotak | BMN: 1010301003 (Penjepit Kertas) | Kode Rincian: 10.03.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(57, 7, '10.03.2', '1010301003', 'Penjepit Kertas', 'Triagonal Clip No. 3', 'Kotak', 'Kotak', 0, 5, 0.00, 'Satuan Standar: Kotak | BMN: 1010301003 (Penjepit Kertas) | Kode Rincian: 10.03.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(58, 8, '10.04.1', '1010301004', 'Penghapus/Korektor', 'Penghapus Faber Castell (kecil)', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301004 (Penghapus/Korektor) | Kode Rincian: 10.04.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(59, 8, '10.04.1', '1010301004', 'Penghapus/Korektor', 'Penghapus Faber Castellm (Besar)', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301004 (Penghapus/Korektor) | Kode Rincian: 10.04.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(60, 8, '10.04.1', '1010301004', 'Penghapus/Korektor', 'Penghapus Boxy (kecil)', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301004 (Penghapus/Korektor) | Kode Rincian: 10.04.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(61, 8, '10.04.1', '1010301004', 'Penghapus/Korektor', 'Penghapus Boxy (Besar)', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301004 (Penghapus/Korektor) | Kode Rincian: 10.04.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(62, 8, '10.04.1', '1010301004', 'Penghapus/Korektor', 'Penghapus Staedler (Kecil)', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301004 (Penghapus/Korektor) | Kode Rincian: 10.04.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(63, 8, '10.04.1', '1010301004', 'Penghapus/Korektor', 'Penghapus Staedler (Besar)', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301004 (Penghapus/Korektor) | Kode Rincian: 10.04.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(64, 8, '10.04.1', '1010301004', 'Penghapus/Korektor', 'Penghapus Whiteboard Besar', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301004 (Penghapus/Korektor) | Kode Rincian: 10.04.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(65, 8, '10.04.1', '1010301004', 'Penghapus/Korektor', 'Penghapus Whiteboard Kecil', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301004 (Penghapus/Korektor) | Kode Rincian: 10.04.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(66, 9, '10.04.2', '1010301004', 'Penghapus/Korektor', 'Tipe Ex Cair', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301004 (Penghapus/Korektor) | Kode Rincian: 10.04.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(67, 9, '10.04.2', '1010301004', 'Penghapus/Korektor', 'Tipe Ex Pentel', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301004 (Penghapus/Korektor) | Kode Rincian: 10.04.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(68, 9, '10.04.2', '1010301004', 'Penghapus/Korektor', 'Tipe Ex Joyko', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301004 (Penghapus/Korektor) | Kode Rincian: 10.04.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(69, 9, '10.04.2', '1010301004', 'Penghapus/Korektor', 'Tipe Ex Correction Tape Faber Castell (Tipe X Kertas)', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010301004 (Penghapus/Korektor) | Kode Rincian: 10.04.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(70, 9, '10.04.2', '1010301004', 'Penghapus/Korektor', 'Faber Castel Correction Tape QJR-506', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010301004 (Penghapus/Korektor) | Kode Rincian: 10.04.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(71, 9, '10.04.2', '1010301004', 'Penghapus/Korektor', 'Corection Full Mark F583', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301004 (Penghapus/Korektor) | Kode Rincian: 10.04.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(72, 10, '10.05.1', '1010301005', 'Buku Tulis', 'Buku Ekspedisi Besar Uk. Folio (isi 100 lembar)', 'buku', 'buku', 0, 5, 0.00, 'Satuan Standar: buku | BMN: 1010301005 (Buku Tulis) | Kode Rincian: 10.05.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(73, 10, '10.05.1', '1010301005', 'Buku Tulis', 'Buku Ekspedisi (isi 200)', 'buku', 'buku', 0, 5, 0.00, 'Satuan Standar: buku | BMN: 1010301005 (Buku Tulis) | Kode Rincian: 10.05.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(74, 10, '10.05.1', '1010301005', 'Buku Tulis', 'Buku Ekspedisi 1/2 Folio', 'buku', 'buku', 0, 5, 0.00, 'Satuan Standar: buku | BMN: 1010301005 (Buku Tulis) | Kode Rincian: 10.05.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(75, 10, '10.05.1', '1010301005', 'Buku Tulis', 'Buku 1/2 Folio', 'buku', 'buku', 0, 5, 0.00, 'Satuan Standar: buku | BMN: 1010301005 (Buku Tulis) | Kode Rincian: 10.05.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(76, 10, '10.05.1', '1010301005', 'Buku Tulis', 'Buku Folio (isi 100)', 'buku', 'buku', 0, 5, 0.00, 'Satuan Standar: buku | BMN: 1010301005 (Buku Tulis) | Kode Rincian: 10.05.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(77, 10, '10.05.1', '1010301005', 'Buku Tulis', 'Buku Folio (isi 200)', 'buku', 'buku', 0, 5, 0.00, 'Satuan Standar: buku | BMN: 1010301005 (Buku Tulis) | Kode Rincian: 10.05.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(78, 10, '10.05.1', '1010301005', 'Buku Tulis', 'Buku Folio (isi 300)', 'buku', 'buku', 0, 5, 0.00, 'Satuan Standar: buku | BMN: 1010301005 (Buku Tulis) | Kode Rincian: 10.05.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(79, 10, '10.05.1', '1010301005', 'Buku Tulis', 'Buku Folio (isi 500)', 'buku', 'buku', 0, 5, 0.00, 'Satuan Standar: buku | BMN: 1010301005 (Buku Tulis) | Kode Rincian: 10.05.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(80, 10, '10.05.1', '1010301005', 'Buku Tulis', 'Buku Kwitansi Besar', 'buku', 'buku', 0, 5, 0.00, 'Satuan Standar: buku | BMN: 1010301005 (Buku Tulis) | Kode Rincian: 10.05.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(81, 10, '10.05.1', '1010301005', 'Buku Tulis', 'Buku Kwitansi Kecil', 'buku', 'buku', 0, 5, 0.00, 'Satuan Standar: buku | BMN: 1010301005 (Buku Tulis) | Kode Rincian: 10.05.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(82, 10, '10.05.1', '1010301005', 'Buku Tulis', 'Buku Faktur', 'buku', 'buku', 0, 5, 0.00, 'Satuan Standar: buku | BMN: 1010301005 (Buku Tulis) | Kode Rincian: 10.05.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(83, 10, '10.05.1', '1010301005', 'Buku Tulis', 'Buku Notebook ukuran A5', 'buku', 'buku', 0, 5, 0.00, 'Satuan Standar: buku | BMN: 1010301005 (Buku Tulis) | Kode Rincian: 10.05.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(84, 10, '10.05.1', '1010301005', 'Buku Tulis', 'Buku Notebook ukuran A6', 'buku', 'buku', 0, 5, 0.00, 'Satuan Standar: buku | BMN: 1010301005 (Buku Tulis) | Kode Rincian: 10.05.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(85, 10, '10.05.1', '1010301005', 'Buku Tulis', 'Buku Writing Pad/Note Book B7', 'buku', 'buku', 0, 5, 0.00, 'Satuan Standar: buku | BMN: 1010301005 (Buku Tulis) | Kode Rincian: 10.05.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(86, 10, '10.05.1', '1010301005', 'Buku Tulis', 'Buku SSP', 'buku', 'buku', 0, 5, 0.00, 'Satuan Standar: buku | BMN: 1010301005 (Buku Tulis) | Kode Rincian: 10.05.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(87, 10, '10.05.1', '1010301005', 'Buku Tulis', 'Buku Tulis isi 100', 'buku', 'buku', 0, 5, 0.00, 'Satuan Standar: buku | BMN: 1010301005 (Buku Tulis) | Kode Rincian: 10.05.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(88, 10, '10.05.1', '1010301005', 'Buku Tulis', 'Buku Tulis isi 500', 'buku', 'buku', 0, 5, 0.00, 'Satuan Standar: buku | BMN: 1010301005 (Buku Tulis) | Kode Rincian: 10.05.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(89, 11, '10.06.1', '1010301006', 'Ordner Dan Map', 'Ordner / Lever Arch File Uk.Folio (5 cm)', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301006 (Ordner Dan Map) | Kode Rincian: 10.06.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(90, 11, '10.06.1', '1010301006', 'Ordner Dan Map', 'Ordner / Lever Arch File Uk.Folio (7 cm)', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301006 (Ordner Dan Map) | Kode Rincian: 10.06.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(91, 11, '10.06.1', '1010301006', 'Ordner Dan Map', 'Ordner / Lever Arch File Uk.A4(5 cm)', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301006 (Ordner Dan Map) | Kode Rincian: 10.06.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(92, 11, '10.06.1', '1010301006', 'Ordner Dan Map', 'Ordner / Lever Arch File Uk.A4(7 cm)', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301006 (Ordner Dan Map) | Kode Rincian: 10.06.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(93, 12, '10.06.2', '1010301006', 'Ordner Dan Map', 'Clear Holder Uk. Folio isi 40 lembar', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301006 (Ordner Dan Map) | Kode Rincian: 10.06.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(94, 12, '10.06.2', '1010301006', 'Ordner Dan Map', 'Clear Holder Folio 20 lembar', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301006 (Ordner Dan Map) | Kode Rincian: 10.06.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(95, 12, '10.06.2', '1010301006', 'Ordner Dan Map', 'Clear Holder Folio A4 60 lembar', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301006 (Ordner Dan Map) | Kode Rincian: 10.06.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(96, 13, '10.06.3', '1010301006', 'Ordner Dan Map', 'Box File Karton Uk. Folio (Lebar 7 cm)', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301006 (Ordner Dan Map) | Kode Rincian: 10.06.3', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(97, 13, '10.06.3', '1010301006', 'Ordner Dan Map', 'Box File Karton Uk. Folio (Lebar 10 cm)', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301006 (Ordner Dan Map) | Kode Rincian: 10.06.3', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(98, 13, '10.06.3', '1010301006', 'Ordner Dan Map', 'Box File Karton Uk. A4 (Lebar 7 cm)', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301006 (Ordner Dan Map) | Kode Rincian: 10.06.3', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(99, 13, '10.06.3', '1010301006', 'Ordner Dan Map', 'Box File Karton Uk. A4 (Lebar 10 cm)', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301006 (Ordner Dan Map) | Kode Rincian: 10.06.3', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(100, 13, '10.06.3', '1010301006', 'Ordner Dan Map', 'Box File Plastik Uk. Folio (Lebar 7 cm)', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301006 (Ordner Dan Map) | Kode Rincian: 10.06.3', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(101, 13, '10.06.3', '1010301006', 'Ordner Dan Map', 'Box File Plastik Uk. Folio (Lebar 10 cm)', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301006 (Ordner Dan Map) | Kode Rincian: 10.06.3', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(102, 13, '10.06.3', '1010301006', 'Ordner Dan Map', 'Box File Plastik Uk. A4  (Lebar 7 cm)', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301006 (Ordner Dan Map) | Kode Rincian: 10.06.3', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(103, 13, '10.06.3', '1010301006', 'Ordner Dan Map', 'Box File Plastik Uk. A4  (Lebar 10 cm)', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301006 (Ordner Dan Map) | Kode Rincian: 10.06.3', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(104, 14, '10.06.4', '1010301006', 'Ordner Dan Map', 'Sheet Protector Folio', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010301006 (Ordner Dan Map) | Kode Rincian: 10.06.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(105, 14, '10.06.4', '1010301006', 'Ordner Dan Map', 'Sheet Protector Ordner (isi 10)', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010301006 (Ordner Dan Map) | Kode Rincian: 10.06.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(106, 14, '10.06.4', '1010301006', 'Ordner Dan Map', 'PP Pocket A4 5220 20 S', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010301006 (Ordner Dan Map) | Kode Rincian: 10.06.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(107, 14, '10.06.4', '1010301006', 'Ordner Dan Map', 'Sheet Protector Bambi A4', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010301006 (Ordner Dan Map) | Kode Rincian: 10.06.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(108, 15, '10.06.5', '1010301006', 'Ordner Dan Map', 'Map L Plastik Bening / Clear Sleeves Uk. A4', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301006 (Ordner Dan Map) | Kode Rincian: 10.06.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(109, 15, '10.06.5', '1010301006', 'Ordner Dan Map', 'Map L Plastik Bening / Clear Sleeves Uk. F4', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301006 (Ordner Dan Map) | Kode Rincian: 10.06.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(110, 15, '10.06.5', '1010301006', 'Ordner Dan Map', 'Map Stopmap Karton', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301006 (Ordner Dan Map) | Kode Rincian: 10.06.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(111, 15, '10.06.5', '1010301006', 'Ordner Dan Map', 'Map Snelhechter Karton', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301006 (Ordner Dan Map) | Kode Rincian: 10.06.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(112, 15, '10.06.5', '1010301006', 'Ordner Dan Map', 'Map Plastik Kancing', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301006 (Ordner Dan Map) | Kode Rincian: 10.06.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(113, 15, '10.06.5', '1010301006', 'Ordner Dan Map', 'Map Plastik Zipper Uk. Folio', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301006 (Ordner Dan Map) | Kode Rincian: 10.06.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(114, 15, '10.06.5', '1010301006', 'Ordner Dan Map', 'Map Batik Kiky (isi 10)', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301006 (Ordner Dan Map) | Kode Rincian: 10.06.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(115, 15, '10.06.5', '1010301006', 'Ordner Dan Map', 'Snell Plastik Jepit', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301006 (Ordner Dan Map) | Kode Rincian: 10.06.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(116, 15, '10.06.5', '1010301006', 'Ordner Dan Map', 'Snell Plastik Per (Spring File)', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301006 (Ordner Dan Map) | Kode Rincian: 10.06.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(117, 15, '10.06.5', '1010301006', 'Ordner Dan Map', 'Map Snelhelter Kabita', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301006 (Ordner Dan Map) | Kode Rincian: 10.06.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(118, 15, '10.06.5', '1010301006', 'Ordner Dan Map', 'Map Snelhelter Plastik', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301006 (Ordner Dan Map) | Kode Rincian: 10.06.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(119, 15, '10.06.5', '1010301006', 'Ordner Dan Map', 'Map Tali Plastik Eagle', 'Lusin', 'Lusin', 0, 5, 0.00, 'Satuan Standar: Lusin | BMN: 1010301006 (Ordner Dan Map) | Kode Rincian: 10.06.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(120, 15, '10.06.5', '1010301006', 'Ordner Dan Map', 'Map Buffalo Biasa 5001', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301006 (Ordner Dan Map) | Kode Rincian: 10.06.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(121, 15, '10.06.5', '1010301006', 'Ordner Dan Map', 'Map Buffalo Biasa 5002', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301006 (Ordner Dan Map) | Kode Rincian: 10.06.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(122, 16, '10.06.6', '1010301006', 'Ordner Dan Map', 'Business File Inter X Uk. F4', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301006 (Ordner Dan Map) | Kode Rincian: 10.06.6', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(123, 16, '10.06.6', '1010301006', 'Ordner Dan Map', 'Business File Inter X Uk. A4', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301006 (Ordner Dan Map) | Kode Rincian: 10.06.6', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(124, 16, '10.06.6', '1010301006', 'Ordner Dan Map', 'Business File A4 Daiichi', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301006 (Ordner Dan Map) | Kode Rincian: 10.06.6', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(125, 16, '10.06.6', '1010301006', 'Ordner Dan Map', 'Business File F4 Daiichi', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301006 (Ordner Dan Map) | Kode Rincian: 10.06.6', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(126, 17, '10.07.1', '1010301007', 'Penggaris', 'Penggaris Besi 30 cm', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301007 (Penggaris) | Kode Rincian: 10.07.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(127, 17, '10.07.1', '1010301007', 'Penggaris', 'Penggaris Plastik 30 cm', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301007 (Penggaris) | Kode Rincian: 10.07.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(128, 17, '10.07.1', '1010301007', 'Penggaris', 'Penggaris Besi 20 cm', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301007 (Penggaris) | Kode Rincian: 10.07.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(129, 17, '10.07.1', '1010301007', 'Penggaris', 'Penggaris Besi 50 cm', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301007 (Penggaris) | Kode Rincian: 10.07.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(130, 18, '10.08.1', '1010301008', 'Cutter (Alat Tulis Kantor)', 'Cutter Kecil (Tipe A-100/A-300)', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301008 (Cutter (Alat Tulis Kantor)) | Kode Rincian: 10.08.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(131, 18, '10.08.1', '1010301008', 'Cutter (Alat Tulis Kantor)', 'Cutter Besar (Tipe L-150/L500)', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301008 (Cutter (Alat Tulis Kantor)) | Kode Rincian: 10.08.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(132, 18, '10.08.1', '1010301008', 'Cutter (Alat Tulis Kantor)', 'Isi Cutter Kecil(Tipe A-100/A-300) per box isi 12 tube', 'box', 'box', 0, 5, 0.00, 'Satuan Standar: box | BMN: 1010301008 (Cutter (Alat Tulis Kantor)) | Kode Rincian: 10.08.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(133, 18, '10.08.1', '1010301008', 'Cutter (Alat Tulis Kantor)', 'Isi Cutter Kecil(Tipe A-100/A-300)', 'tube', 'tube', 0, 5, 0.00, 'Satuan Standar: tube | BMN: 1010301008 (Cutter (Alat Tulis Kantor)) | Kode Rincian: 10.08.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(134, 18, '10.08.1', '1010301008', 'Cutter (Alat Tulis Kantor)', 'Isi Cutter Kecil (Tipe L-150/L500) per box isi 12 tube', 'box', 'box', 0, 5, 0.00, 'Satuan Standar: box | BMN: 1010301008 (Cutter (Alat Tulis Kantor)) | Kode Rincian: 10.08.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(135, 18, '10.08.1', '1010301008', 'Cutter (Alat Tulis Kantor)', 'Isi Cutter Kecil (Tipe L-150/L500)', 'tube', 'tube', 0, 5, 0.00, 'Satuan Standar: tube | BMN: 1010301008 (Cutter (Alat Tulis Kantor)) | Kode Rincian: 10.08.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(136, 19, '10.10.1', '1010301010', 'Alat Perekat', 'Lem FOX 1 Kg', 'Tube', 'Tube', 0, 5, 0.00, 'Satuan Standar: Tube | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(137, 19, '10.10.1', '1010301010', 'Alat Perekat', 'Lem FOX 500 Gr', 'Tube', 'Tube', 0, 5, 0.00, 'Satuan Standar: Tube | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(138, 19, '10.10.1', '1010301010', 'Alat Perekat', 'Lem FOX 150 Gr', 'Tube', 'Tube', 0, 5, 0.00, 'Satuan Standar: Tube | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(139, 19, '10.10.1', '1010301010', 'Alat Perekat', 'Lem Kertas Botol Besar', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(140, 19, '10.10.1', '1010301010', 'Alat Perekat', 'Lem Kertas Botol Kecil', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(141, 19, '10.10.1', '1010301010', 'Alat Perekat', 'Lem O Glue Lokal', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(142, 19, '10.10.1', '1010301010', 'Alat Perekat', 'Lem O Glue Original', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(143, 19, '10.10.1', '1010301010', 'Alat Perekat', 'Glue Stick Kenko 8 Gr', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(144, 19, '10.10.1', '1010301010', 'Alat Perekat', 'Glue Stick Kenko 15 Gr', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(145, 19, '10.10.1', '1010301010', 'Alat Perekat', 'Glue Stick Kenko 25Gr', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(146, 19, '10.10.1', '1010301010', 'Alat Perekat', 'Glue Stick Kenko 20 Gr', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(147, 19, '10.10.1', '1010301010', 'Alat Perekat', 'Glue Stick Kenko 30 Gr', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(148, 19, '10.10.1', '1010301010', 'Alat Perekat', 'Glue Stick Kenko 40 Gr', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(149, 19, '10.10.1', '1010301010', 'Alat Perekat', 'Lem Stick Kenko 20 Gr', 'Buah', 'Buah', 0, 5, 0.00, 'Satuan Standar: Buah | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(150, 20, '10.10.2', '1010301010', 'Alat Perekat', 'Double Tape 1/4 Inch (6 mm)', 'slop', 'slop', 0, 5, 0.00, 'Satuan Standar: slop | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(151, 20, '10.10.2', '1010301010', 'Alat Perekat', 'Double Tape 1/2 Inch (12 mm)', 'slop', 'slop', 0, 5, 0.00, 'Satuan Standar: slop | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(152, 20, '10.10.2', '1010301010', 'Alat Perekat', 'Double Tape  1 Inch (24 mm)', 'slop', 'slop', 0, 5, 0.00, 'Satuan Standar: slop | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(153, 20, '10.10.2', '1010301010', 'Alat Perekat', 'DoubleTape  2 Inch (48 mm)', 'slop', 'slop', 0, 5, 0.00, 'Satuan Standar: slop | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(154, 21, '10.10.3', '1010301010', 'Alat Perekat', 'Selotip kecil 1/2 inc', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.3', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(155, 21, '10.10.3', '1010301010', 'Alat Perekat', 'Selotip besar 1 inch', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.3', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(156, 21, '10.10.3', '1010301010', 'Alat Perekat', 'Selotip 24 mm x 72 m Nachi Tape', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.3', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(157, 21, '10.10.3', '1010301010', 'Alat Perekat', 'Selotip 1/2 Inch Panfic', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.3', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(158, 21, '10.10.3', '1010301010', 'Alat Perekat', 'Selotip 36 mm Kenko', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.3', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(159, 21, '10.10.3', '1010301010', 'Alat Perekat', 'Selotip 48 mm x 100 Daimaru', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.3', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(160, 21, '10.10.3', '1010301010', 'Alat Perekat', 'Selotip 12 mm x 10 m Nachi Tape isi 12 buah', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.3', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(161, 21, '10.10.3', '1010301010', 'Alat Perekat', 'Isolasi Kecil', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.3', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(162, 21, '10.10.3', '1010301010', 'Alat Perekat', 'Isolasi 1 x 72', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.3', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(163, 22, '10.10.4', '1010301010', 'Alat Perekat', 'Lakban Hitam 1 Inch', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(164, 22, '10.10.4', '1010301010', 'Alat Perekat', 'Lakban Hitam 1,5 Inch', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(165, 22, '10.10.4', '1010301010', 'Alat Perekat', 'Lakban Hitam 2 Inch', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(166, 22, '10.10.4', '1010301010', 'Alat Perekat', 'Lakban 1 cm', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(167, 22, '10.10.4', '1010301010', 'Alat Perekat', 'Lakban 2 cm', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(168, 22, '10.10.4', '1010301010', 'Alat Perekat', 'Lakban 3 cm', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(169, 22, '10.10.4', '1010301010', 'Alat Perekat', 'Lakban Kain 3 cm (Daimaru)', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(170, 22, '10.10.4', '1010301010', 'Alat Perekat', 'Lakban 5 cm', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(171, 23, '10.10.5', '1010301010', 'Alat Perekat', 'Label Sticker No. 100', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(172, 23, '10.10.5', '1010301010', 'Alat Perekat', 'Label Sticker No. 101', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(173, 23, '10.10.5', '1010301010', 'Alat Perekat', 'Label Sticker No. 102', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(174, 23, '10.10.5', '1010301010', 'Alat Perekat', 'Label Sticker No. 103', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(175, 23, '10.10.5', '1010301010', 'Alat Perekat', 'Label Sticker No. 104', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(176, 23, '10.10.5', '1010301010', 'Alat Perekat', 'Label Sticker No. 114', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(177, 23, '10.10.5', '1010301010', 'Alat Perekat', 'Label Sticker No. 117', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(178, 23, '10.10.5', '1010301010', 'Alat Perekat', 'Label Sticker No. 121', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(179, 23, '10.10.5', '1010301010', 'Alat Perekat', 'Label Sticker No. 123', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(180, 23, '10.10.5', '1010301010', 'Alat Perekat', 'Label Sticker No. 127', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(181, 24, '10.10.6', '1010301010', 'Alat Perekat', 'Post It 3M 653', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.6', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(182, 24, '10.10.6', '1010301010', 'Alat Perekat', 'Post It 3M 654', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.6', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(183, 24, '10.10.6', '1010301010', 'Alat Perekat', 'Post It 3M 654 5UC', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.6', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(184, 24, '10.10.6', '1010301010', 'Alat Perekat', 'Post It 3M 655', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.6', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(185, 24, '10.10.6', '1010301010', 'Alat Perekat', 'Post It 3M 656', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.6', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(186, 24, '10.10.6', '1010301010', 'Alat Perekat', 'Post It 657', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.6', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(187, 24, '10.10.6', '1010301010', 'Alat Perekat', 'Post It 670', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.6', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(188, 24, '10.10.6', '1010301010', 'Alat Perekat', 'Post It 670-an (500 lembar)', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.6', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(189, 24, '10.10.6', '1010301010', 'Alat Perekat', 'Post It 671', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.6', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(190, 24, '10.10.6', '1010301010', 'Alat Perekat', '3M Post It Note Page Marker 670', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.6', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(191, 24, '10.10.6', '1010301010', 'Alat Perekat', 'Post It Pronto Notes', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.6', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(192, 24, '10.10.6', '1010301010', 'Alat Perekat', 'Post It  Sign Here Warna-warni TJ44-SH', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.6', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(193, 24, '10.10.6', '1010301010', 'Alat Perekat', 'Post It Sign Here Warna-warni TJ44-P', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.6', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(194, 24, '10.10.6', '1010301010', 'Alat Perekat', 'Post It Sign Here 3M', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.6', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(195, 24, '10.10.6', '1010301010', 'Alat Perekat', 'Post It Note Page Marker', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.6', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(196, 24, '10.10.6', '1010301010', 'Alat Perekat', 'Sign Here Plastik merek Pronto', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.6', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(197, 24, '10.10.6', '1010301010', 'Alat Perekat', 'Sign Here Plastik merek Joyko', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010301010 (Alat Perekat) | Kode Rincian: 10.10.6', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(198, 25, '10.12.1', '1010301012', 'Staples', 'Stapler Max No. 3', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301012 (Staples) | Kode Rincian: 10.12.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(199, 25, '10.12.1', '1010301012', 'Staples', 'Stapler Max No. 10', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301012 (Staples) | Kode Rincian: 10.12.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(200, 25, '10.12.1', '1010301012', 'Staples', 'Stapler Kenko No. 3', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301012 (Staples) | Kode Rincian: 10.12.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(201, 25, '10.12.1', '1010301012', 'Staples', 'Stapler Kenko No. 10', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301012 (Staples) | Kode Rincian: 10.12.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(202, 25, '10.12.1', '1010301012', 'Staples', 'Stapler Joyko No. 3', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301012 (Staples) | Kode Rincian: 10.12.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(203, 25, '10.12.1', '1010301012', 'Staples', 'Stapler Joyko No. 10', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301012 (Staples) | Kode Rincian: 10.12.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(204, 25, '10.12.1', '1010301012', 'Staples', 'Stapler Etona No. 3', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301012 (Staples) | Kode Rincian: 10.12.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(205, 25, '10.12.1', '1010301012', 'Staples', 'Stapler Etona No. 10', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301012 (Staples) | Kode Rincian: 10.12.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(206, 25, '10.12.1', '1010301012', 'Staples', 'Stapler Kangaro No. 3', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301012 (Staples) | Kode Rincian: 10.12.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(207, 25, '10.12.1', '1010301012', 'Staples', 'Stapler Kangaro No. 10', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301012 (Staples) | Kode Rincian: 10.12.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(208, 25, '10.12.1', '1010301012', 'Staples', 'Stapler Kecil', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301012 (Staples) | Kode Rincian: 10.12.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(209, 25, '10.12.1', '1010301012', 'Staples', 'Stapler Max HD No. 10D', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301012 (Staples) | Kode Rincian: 10.12.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00');
INSERT INTO `products` (`id`, `category_id`, `code`, `bmn_code`, `bmn_group`, `name`, `unit`, `available_units`, `stock`, `minimum_stock`, `purchase_price`, `description`, `image`, `created_at`, `updated_at`) VALUES
(210, 25, '10.12.1', '1010301012', 'Staples', 'Stapler Max HD No. 11', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301012 (Staples) | Kode Rincian: 10.12.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(211, 25, '10.12.1', '1010301012', 'Staples', 'Stapler Max HD No. 12', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301012 (Staples) | Kode Rincian: 10.12.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(212, 26, '10.13.1', '1010301013', 'Isi Staples', 'Isi Stapler Max No. 3', 'pak/slop', 'pak/slop', 0, 5, 0.00, 'Satuan Standar: pak/slop | BMN: 1010301013 (Isi Staples) | Kode Rincian: 10.13.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(213, 26, '10.13.1', '1010301013', 'Isi Staples', 'Isi Stapler Max No.10', 'pak/slop', 'pak/slop', 0, 5, 0.00, 'Satuan Standar: pak/slop | BMN: 1010301013 (Isi Staples) | Kode Rincian: 10.13.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(214, 26, '10.13.1', '1010301013', 'Isi Staples', 'Isi Stapler Kenko No. 3', 'pak/slop', 'pak/slop', 0, 5, 0.00, 'Satuan Standar: pak/slop | BMN: 1010301013 (Isi Staples) | Kode Rincian: 10.13.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(215, 26, '10.13.1', '1010301013', 'Isi Staples', 'Isi Stapler Kenko No. 10', 'pak/slop', 'pak/slop', 0, 5, 0.00, 'Satuan Standar: pak/slop | BMN: 1010301013 (Isi Staples) | Kode Rincian: 10.13.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(216, 26, '10.13.1', '1010301013', 'Isi Staples', 'Isi Stapler Joyko No. 3', 'pak/slop', 'pak/slop', 0, 5, 0.00, 'Satuan Standar: pak/slop | BMN: 1010301013 (Isi Staples) | Kode Rincian: 10.13.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(217, 26, '10.13.1', '1010301013', 'Isi Staples', 'Isi Stapler Joyko No. 10', 'pak/slop', 'pak/slop', 0, 5, 0.00, 'Satuan Standar: pak/slop | BMN: 1010301013 (Isi Staples) | Kode Rincian: 10.13.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(218, 26, '10.13.1', '1010301013', 'Isi Staples', 'Isi Stapler Etona No. 3', 'pak/slop', 'pak/slop', 0, 5, 0.00, 'Satuan Standar: pak/slop | BMN: 1010301013 (Isi Staples) | Kode Rincian: 10.13.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(219, 26, '10.13.1', '1010301013', 'Isi Staples', 'Isi Stapler Etona No. 10', 'pak/slop', 'pak/slop', 0, 5, 0.00, 'Satuan Standar: pak/slop | BMN: 1010301013 (Isi Staples) | Kode Rincian: 10.13.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(220, 26, '10.13.1', '1010301013', 'Isi Staples', 'Isi Stapler Kangaro No. 3', 'pak/slop', 'pak/slop', 0, 5, 0.00, 'Satuan Standar: pak/slop | BMN: 1010301013 (Isi Staples) | Kode Rincian: 10.13.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(221, 26, '10.13.1', '1010301013', 'Isi Staples', 'Isi Stapler Kangaro No. 10', 'pak/slop', 'pak/slop', 0, 5, 0.00, 'Satuan Standar: pak/slop | BMN: 1010301013 (Isi Staples) | Kode Rincian: 10.13.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(222, 27, '19.99.1', '1010301999', 'Alat Tulis Kantor Lainnya', 'Elevated Tray 4 susun', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010301999 (Alat Tulis Kantor Lainnya) | Kode Rincian: 19.99.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(223, 27, '19.99.1', '1010301999', 'Alat Tulis Kantor Lainnya', 'Elevated Tray 3 susun', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010301999 (Alat Tulis Kantor Lainnya) | Kode Rincian: 19.99.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(224, 27, '19.99.1', '1010301999', 'Alat Tulis Kantor Lainnya', 'Elevated Tray 2 susun', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010301999 (Alat Tulis Kantor Lainnya) | Kode Rincian: 19.99.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(225, 28, '19.99.2', '1010301999', 'Alat Tulis Kantor Lainnya', 'Gunting Kecil', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301999 (Alat Tulis Kantor Lainnya) | Kode Rincian: 19.99.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(226, 28, '19.99.2', '1010301999', 'Alat Tulis Kantor Lainnya', 'Gunting Sedang', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301999 (Alat Tulis Kantor Lainnya) | Kode Rincian: 19.99.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(227, 28, '19.99.2', '1010301999', 'Alat Tulis Kantor Lainnya', 'Gunting Besar', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301999 (Alat Tulis Kantor Lainnya) | Kode Rincian: 19.99.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(228, 29, '19.99.3', '1010301999', 'Alat Tulis Kantor Lainnya', 'Kalkulator Citizen SDC-868L', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301999 (Alat Tulis Kantor Lainnya) | Kode Rincian: 19.99.3', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(229, 30, '19.99.4', '1010301999', 'Alat Tulis Kantor Lainnya', 'Pembolong Kertas Kenko No. 40', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301999 (Alat Tulis Kantor Lainnya) | Kode Rincian: 19.99.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(230, 30, '19.99.4', '1010301999', 'Alat Tulis Kantor Lainnya', 'Pembolong Kertas No. 30', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301999 (Alat Tulis Kantor Lainnya) | Kode Rincian: 19.99.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(231, 30, '19.99.4', '1010301999', 'Alat Tulis Kantor Lainnya', 'Pembolong Kertas No. 85', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301999 (Alat Tulis Kantor Lainnya) | Kode Rincian: 19.99.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(232, 30, '19.99.4', '1010301999', 'Alat Tulis Kantor Lainnya', 'Pembolong Kertas No. 138', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301999 (Alat Tulis Kantor Lainnya) | Kode Rincian: 19.99.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(233, 31, '19.99.5', '1010301999', 'Alat Tulis Kantor Lainnya', 'Rautan Pensil Kecil', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301999 (Alat Tulis Kantor Lainnya) | Kode Rincian: 19.99.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(234, 31, '19.99.5', '1010301999', 'Alat Tulis Kantor Lainnya', 'Rautan Kaca', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301999 (Alat Tulis Kantor Lainnya) | Kode Rincian: 19.99.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(235, 31, '19.99.5', '1010301999', 'Alat Tulis Kantor Lainnya', 'Rautan Meja Angel No. 5', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301999 (Alat Tulis Kantor Lainnya) | Kode Rincian: 19.99.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(236, 32, '19.99.6', '1010301999', 'Alat Tulis Kantor Lainnya', 'Tape Cutter / Pemotong Lakban Besar', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301999 (Alat Tulis Kantor Lainnya) | Kode Rincian: 19.99.6', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(237, 33, '19.99.7', '1010301999', 'Alat Tulis Kantor Lainnya', 'Tape Dispenser Kecil', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301999 (Alat Tulis Kantor Lainnya) | Kode Rincian: 19.99.7', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(238, 33, '19.99.7', '1010301999', 'Alat Tulis Kantor Lainnya', 'Dispenser Tape SDI', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301999 (Alat Tulis Kantor Lainnya) | Kode Rincian: 19.99.7', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(239, 33, '19.99.7', '1010301999', 'Alat Tulis Kantor Lainnya', 'Dispenser Tape Besar', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301999 (Alat Tulis Kantor Lainnya) | Kode Rincian: 19.99.7', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(240, 33, '19.99.7', '1010301999', 'Alat Tulis Kantor Lainnya', 'Dispenser Lakban', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301999 (Alat Tulis Kantor Lainnya) | Kode Rincian: 19.99.7', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(241, 34, '19.99.8', '1010301999', 'Alat Tulis Kantor Lainnya', 'Joyko LPR104 Green laser pointer', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301999 (Alat Tulis Kantor Lainnya) | Kode Rincian: 19.99.8', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(242, 34, '19.99.8', '1010301999', 'Alat Tulis Kantor Lainnya', 'Logitech Wireless Laser Pointer Presentation R400', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301999 (Alat Tulis Kantor Lainnya) | Kode Rincian: 19.99.8', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(243, 34, '19.99.8', '1010301999', 'Alat Tulis Kantor Lainnya', 'Logitech Wireless Laser Pointer Presentation  R500', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301999 (Alat Tulis Kantor Lainnya) | Kode Rincian: 19.99.8', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(244, 34, '19.99.8', '1010301999', 'Alat Tulis Kantor Lainnya', 'JOYKO LPR-107 Wireless Presenter', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301999 (Alat Tulis Kantor Lainnya) | Kode Rincian: 19.99.8', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(245, 35, '19.99.9', '1010301999', 'Alat Tulis Kantor Lainnya', 'Bak Stempel Besar', 'buah', 'buah', 0, 5, 50000.00, 'Satuan Standar: buah | BMN: 1010301999 (Alat Tulis Kantor Lainnya) | Kode Rincian: 19.99.9', NULL, '2026-01-01 01:00:00', '2026-10-27 02:04:54'),
(246, 35, '19.99.9', '1010301999', 'Alat Tulis Kantor Lainnya', 'Bak Stempel Kecil', 'buah', 'buah', 1, 5, 60000.00, 'Satuan Standar: buah | BMN: 1010301999 (Alat Tulis Kantor Lainnya) | Kode Rincian: 19.99.9', NULL, '2026-01-01 01:00:00', '2026-10-27 02:04:54'),
(247, 36, '19.99.10', '1010301999', 'Alat Tulis Kantor Lainnya', 'Book Stand/Penahan Buku', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010301999 (Alat Tulis Kantor Lainnya) | Kode Rincian: 19.99.10', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(248, 37, '19.99.11', '1010301999', 'Alat Tulis Kantor Lainnya', 'V-Tec Desk Organizer Besi (VT-9058/Black)', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010301999 (Alat Tulis Kantor Lainnya) | Kode Rincian: 19.99.11', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(249, 38, '19.99.12', '1010301999', 'Alat Tulis Kantor Lainnya', 'V-Tech Round Pencil Cup (Besi)', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010301999 (Alat Tulis Kantor Lainnya) | Kode Rincian: 19.99.12', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(250, 39, '20.01.1', '1010302001', 'Kertas HVS', 'Kertas HVS Uk.A5 70 Gsm', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010302001 (Kertas HVS) | Kode Rincian: 20.01.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(251, 39, '20.01.1', '1010302001', 'Kertas HVS', 'Kertas HVS Uk. A4 70 Gsm Paper One', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010302001 (Kertas HVS) | Kode Rincian: 20.01.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(252, 39, '20.01.1', '1010302001', 'Kertas HVS', 'Kertas HVS Uk. A4 70 Gsm Paper One  (warna)', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010302001 (Kertas HVS) | Kode Rincian: 20.01.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(253, 39, '20.01.1', '1010302001', 'Kertas HVS', 'Kertas HVS Uk.A4 75 Gsm Paper One', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010302001 (Kertas HVS) | Kode Rincian: 20.01.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(254, 39, '20.01.1', '1010302001', 'Kertas HVS', 'Kertas HVS Uk. A4 80 Gsm Paper One', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010302001 (Kertas HVS) | Kode Rincian: 20.01.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(255, 39, '20.01.1', '1010302001', 'Kertas HVS', 'Kertas HVS Uk.F4 70 Gsm Paper One', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010302001 (Kertas HVS) | Kode Rincian: 20.01.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(256, 39, '20.01.1', '1010302001', 'Kertas HVS', 'Kertas HVS Uk.F4 75 Gsm Paper One', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010302001 (Kertas HVS) | Kode Rincian: 20.01.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(257, 39, '20.01.1', '1010302001', 'Kertas HVS', 'Kertas HVS Uk.F4 80 Gsm Paper One', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010302001 (Kertas HVS) | Kode Rincian: 20.01.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(258, 39, '20.01.1', '1010302001', 'Kertas HVS', 'Kertas HVS Uk.Quarto 70 Gsm Paper One', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010302001 (Kertas HVS) | Kode Rincian: 20.01.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(259, 39, '20.01.1', '1010302001', 'Kertas HVS', 'Kertas HVS Uk. Qarto 75 Gsm Paper One', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010302001 (Kertas HVS) | Kode Rincian: 20.01.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(260, 39, '20.01.1', '1010302001', 'Kertas HVS', 'Kertas HVS Uk. Qarto 80 Gr Paper One', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010302001 (Kertas HVS) | Kode Rincian: 20.01.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(261, 39, '20.01.1', '1010302001', 'Kertas HVS', 'Kertas HVS Uk. A3 75 Gsm Paper One', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010302001 (Kertas HVS) | Kode Rincian: 20.01.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(262, 39, '20.01.1', '1010302001', 'Kertas HVS', 'Kertas HVS Uk. A3 80 Gr Paper One', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010302001 (Kertas HVS) | Kode Rincian: 20.01.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(263, 39, '20.01.1', '1010302001', 'Kertas HVS', 'Kertas HVS Ukuran A4 70 Gr Bola Dunia', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010302001 (Kertas HVS) | Kode Rincian: 20.01.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(264, 39, '20.01.1', '1010302001', 'Kertas HVS', 'Kertas HVS Ukuran A4 70 Gr Bola Dunia (warna)', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010302001 (Kertas HVS) | Kode Rincian: 20.01.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(265, 39, '20.01.1', '1010302001', 'Kertas HVS', 'Kertas HVS Ukuran A4 75 Gr Bola Dunia', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010302001 (Kertas HVS) | Kode Rincian: 20.01.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(266, 39, '20.01.1', '1010302001', 'Kertas HVS', 'Kertas HVS Ukuran A4 80 Gr Bola Dunia', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010302001 (Kertas HVS) | Kode Rincian: 20.01.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(267, 39, '20.01.1', '1010302001', 'Kertas HVS', 'Kertas HVS Ukuran F4 70 Gr Bola Dunia', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010302001 (Kertas HVS) | Kode Rincian: 20.01.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(268, 39, '20.01.1', '1010302001', 'Kertas HVS', 'Kertas HVS Ukuran F4 75 Gr Bola Dunia', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010302001 (Kertas HVS) | Kode Rincian: 20.01.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(269, 39, '20.01.1', '1010302001', 'Kertas HVS', 'Kertas HVS Ukuran F4 80 Gr Bola Dunia', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010302001 (Kertas HVS) | Kode Rincian: 20.01.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(270, 39, '20.01.1', '1010302001', 'Kertas HVS', 'Kertas HVS Uk. A3 75 Gr Bola Dunia', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010302001 (Kertas HVS) | Kode Rincian: 20.01.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(271, 39, '20.01.1', '1010302001', 'Kertas HVS', 'Kertas HVS Uk. A3 80 Gr Bola Dunia', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010302001 (Kertas HVS) | Kode Rincian: 20.01.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(272, 39, '20.01.1', '1010302001', 'Kertas HVS', 'Kertas Double Folio Bergaris (isi 100 lembar)', 'pak', 'pak', 0, 5, 0.00, 'Satuan Standar: pak | BMN: 1010302001 (Kertas HVS) | Kode Rincian: 20.01.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(273, 39, '20.01.1', '1010302001', 'Kertas HVS', 'Kertas Concord Uk. A4 90 Gr (10 Lembar)', 'pak', 'pak', 0, 5, 0.00, 'Satuan Standar: pak | BMN: 1010302001 (Kertas HVS) | Kode Rincian: 20.01.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(274, 39, '20.01.1', '1010302001', 'Kertas HVS', 'Kertas Concord Uk. A4 160 Gr (10 Lembar)', 'pak', 'pak', 0, 5, 0.00, 'Satuan Standar: pak | BMN: 1010302001 (Kertas HVS) | Kode Rincian: 20.01.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(275, 39, '20.01.1', '1010302001', 'Kertas HVS', 'Kertas Concord Uk. A4 220 Gr (10 Lembar)', 'pak', 'pak', 0, 5, 0.00, 'Satuan Standar: pak | BMN: 1010302001 (Kertas HVS) | Kode Rincian: 20.01.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(276, 39, '20.01.1', '1010302001', 'Kertas HVS', 'Kertas Corcode Uk. Folio/F4 90 Gr (10 Lembar)', 'pak', 'pak', 0, 5, 0.00, 'Satuan Standar: pak | BMN: 1010302001 (Kertas HVS) | Kode Rincian: 20.01.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(277, 39, '20.01.1', '1010302001', 'Kertas HVS', 'Kertas Corcode Uk. Folio/F4 160 Gr (10 Lembar)', 'pak', 'pak', 0, 5, 0.00, 'Satuan Standar: pak | BMN: 1010302001 (Kertas HVS) | Kode Rincian: 20.01.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(278, 39, '20.01.1', '1010302001', 'Kertas HVS', 'Kertas Corcode Uk. Folio/F4 220 Gr (10 Lembar)', 'pak', 'pak', 0, 5, 0.00, 'Satuan Standar: pak | BMN: 1010302001 (Kertas HVS) | Kode Rincian: 20.01.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(279, 39, '20.01.1', '1010302001', 'Kertas HVS', 'Kertas Cover Buffalo Uk. A4 160 Gsm (isi 100 lembar)', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010302001 (Kertas HVS) | Kode Rincian: 20.01.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(280, 39, '20.01.1', '1010302001', 'Kertas HVS', 'Kertas Cover Buffalo Uk. A4 260 Gsm (isi 100 lembar)', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010302001 (Kertas HVS) | Kode Rincian: 20.01.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(281, 39, '20.01.1', '1010302001', 'Kertas HVS', 'Kertas Cover Buffalo Uk. F4 160 Gsm (isi 100 lembar)', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010302001 (Kertas HVS) | Kode Rincian: 20.01.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(282, 39, '20.01.1', '1010302001', 'Kertas HVS', 'Kertas Cover Buffalo Uk. F4 260 Gsm (isi 100 lembar)', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010302001 (Kertas HVS) | Kode Rincian: 20.01.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(283, 39, '20.01.1', '1010302001', 'Kertas HVS', 'Kertas Photo Glossy A4 120 gsm', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010302001 (Kertas HVS) | Kode Rincian: 20.01.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(284, 39, '20.01.1', '1010302001', 'Kertas HVS', 'Kertas Photo Glossy A4 210 gsm', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010302001 (Kertas HVS) | Kode Rincian: 20.01.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(285, 39, '20.01.1', '1010302001', 'Kertas HVS', 'Kertas Photo Doff/Matte A4 120 gsm', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010302001 (Kertas HVS) | Kode Rincian: 20.01.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(286, 39, '20.01.1', '1010302001', 'Kertas HVS', 'Kertas Photo Doff/Matte A4 210 gsm', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010302001 (Kertas HVS) | Kode Rincian: 20.01.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(287, 39, '20.01.1', '1010302001', 'Kertas HVS', 'Kertas Flipchart 60 x 90 cm', 'roll', 'roll', 0, 5, 0.00, 'Satuan Standar: roll | BMN: 1010302001 (Kertas HVS) | Kode Rincian: 20.01.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(288, 40, '20.04.1', '1010302004', 'Amplop', 'Amplop Coklat Uk. Folio (345 x 240 mm)', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010302004 (Amplop) | Kode Rincian: 20.04.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(289, 40, '20.04.1', '1010302004', 'Amplop', 'Amplop Coklat Tali Uk. F4', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010302004 (Amplop) | Kode Rincian: 20.04.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(290, 40, '20.04.1', '1010302004', 'Amplop', 'Amplop Coklat Ukuran B4', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010302004 (Amplop) | Kode Rincian: 20.04.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(291, 40, '20.04.1', '1010302004', 'Amplop', 'Amplop Coklat Tali 304 Kiky (isi 10 buah)', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010302004 (Amplop) | Kode Rincian: 20.04.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(292, 40, '20.04.1', '1010302004', 'Amplop', 'Amplop Coklat Lem 3030 Kiky (isi 10)', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010302004 (Amplop) | Kode Rincian: 20.04.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(293, 40, '20.04.1', '1010302004', 'Amplop', 'Amplop Coklat A4 Air Mail (isi 10)', 'Pak', 'Pak', 0, 5, 250000.00, 'Satuan Standar: Pak | BMN: 1010302004 (Amplop) | Kode Rincian: 20.04.1', NULL, '2026-01-01 01:00:00', '2026-10-27 02:04:54'),
(294, 40, '20.04.1', '1010302004', 'Amplop', 'Amplop Coklat Kwarto', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010302004 (Amplop) | Kode Rincian: 20.04.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(295, 40, '20.04.1', '1010302004', 'Amplop', 'Amplop Plastik Tali IMCO A4/Folio', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010302004 (Amplop) | Kode Rincian: 20.04.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(296, 40, '20.04.1', '1010302004', 'Amplop', 'Amplop Putih Polos Berlem Paperline', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010302004 (Amplop) | Kode Rincian: 20.04.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(297, 40, '20.04.1', '1010302004', 'Amplop', 'Amplop Putih Kiky Merpati 90 - 80', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010302004 (Amplop) | Kode Rincian: 20.04.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(298, 40, '20.04.1', '1010302004', 'Amplop', 'Amplop Putih Ukuran 110 Royal', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010302004 (Amplop) | Kode Rincian: 20.04.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(299, 40, '20.04.1', '1010302004', 'Amplop', 'Amplop Putih No. 90 Paperline', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010302004 (Amplop) | Kode Rincian: 20.04.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(300, 40, '20.04.1', '1010302004', 'Amplop', 'Amplop Putih Ukuran A4', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010302004 (Amplop) | Kode Rincian: 20.04.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(301, 41, '30.01.1', '1010303001', 'Transparant Sheet', 'Transparant Sheet HP 51630 G', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010303001 (Transparant Sheet) | Kode Rincian: 30.01.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(302, 41, '30.01.1', '1010303001', 'Transparant Sheet', 'Transparant Sheet C 3835 A', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010303001 (Transparant Sheet) | Kode Rincian: 30.01.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(303, 41, '30.01.1', '1010303001', 'Transparant Sheet', 'Transparant Sheet Yashika Laser Jet A4/Folio', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010303001 (Transparant Sheet) | Kode Rincian: 30.01.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(304, 41, '30.01.1', '1010303001', 'Transparant Sheet', 'Transparant Sheet Film Laser Jet A4', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010303001 (Transparant Sheet) | Kode Rincian: 30.01.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(305, 41, '30.01.1', '1010303001', 'Transparant Sheet', 'Tranparancy Film HP Premium A4', 'Pak', 'Pak', 0, 5, 0.00, 'Satuan Standar: Pak | BMN: 1010303001 (Transparant Sheet) | Kode Rincian: 30.01.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(306, 42, '40.03.1', '1010304003', 'Pita Printer', 'Pita Brother 1030', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304003 (Pita Printer) | Kode Rincian: 40.03.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(307, 42, '40.03.1', '1010304003', 'Pita Printer', 'Pita Mesin Ketik Brother', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010304003 (Pita Printer) | Kode Rincian: 40.03.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(308, 42, '40.03.1', '1010304003', 'Pita Printer', 'Pita Mesin Ketik Manual Swalow', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010304003 (Pita Printer) | Kode Rincian: 40.03.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(309, 42, '40.03.1', '1010304003', 'Pita Printer', 'Ribbon Cetridge Epson LX 300', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304003 (Pita Printer) | Kode Rincian: 40.03.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(310, 43, '40.04.1', '1010304004', 'Tinta/Toner Printer', 'Tinta Brother BT5000 Warna', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(311, 43, '40.04.1', '1010304004', 'Tinta/Toner Printer', 'Tinta Brother MFC T810W BTD60 Black, Cyan, Magenta, Yellow', 'Botol', 'Botol', 0, 5, 0.00, 'Satuan Standar: Botol | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(312, 43, '40.04.1', '1010304004', 'Tinta/Toner Printer', 'Tinta Brother TN-2356', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(313, 44, '40.04.2', '1010304004', 'Tinta/Toner Printer', 'Canon Pixma Catridge PG-745 (Black)', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(314, 44, '40.04.2', '1010304004', 'Tinta/Toner Printer', 'Canon Pixma Catridge CL-746 (Color)', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(315, 44, '40.04.2', '1010304004', 'Tinta/Toner Printer', 'Canon Black Ink PGI 35', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(316, 44, '40.04.2', '1010304004', 'Tinta/Toner Printer', 'Canon Color Ink CLI 36', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(317, 44, '40.04.2', '1010304004', 'Tinta/Toner Printer', 'Canon Black Ink Cartridge Pg-810', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(318, 44, '40.04.2', '1010304004', 'Tinta/Toner Printer', 'Canon Black Ink Cartridge Cl-811', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(319, 44, '40.04.2', '1010304004', 'Tinta/Toner Printer', 'Tinta Canon 325', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(320, 44, '40.04.2', '1010304004', 'Tinta/Toner Printer', 'Tinta Canon 726 BK', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(321, 44, '40.04.2', '1010304004', 'Tinta/Toner Printer', 'Tinta Canon 726 C', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(322, 44, '40.04.2', '1010304004', 'Tinta/Toner Printer', 'Tinta Canon 726 M', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(323, 44, '40.04.2', '1010304004', 'Tinta/Toner Printer', 'Tinta Canon 726 PGBK', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(324, 44, '40.04.2', '1010304004', 'Tinta/Toner Printer', 'Tinta Canon 726 Y', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(325, 44, '40.04.2', '1010304004', 'Tinta/Toner Printer', 'Tinta Canon 745', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(326, 44, '40.04.2', '1010304004', 'Tinta/Toner Printer', 'Tinta Canon 746', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(327, 44, '40.04.2', '1010304004', 'Tinta/Toner Printer', 'Tinta Canon 40', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(328, 44, '40.04.2', '1010304004', 'Tinta/Toner Printer', 'Tinta Canon 41', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(329, 45, '40.04.3', '1010304004', 'Tinta/Toner Printer', 'Epson Ink Printer Refill 003 (Black)', 'Botol', 'Botol', 0, 5, 0.00, 'Satuan Standar: Botol | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.3', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(330, 45, '40.04.3', '1010304004', 'Tinta/Toner Printer', 'Epson Ink Printer Refill 003 (C,Y,M)', 'Botol', 'Botol', 0, 5, 0.00, 'Satuan Standar: Botol | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.3', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(331, 45, '40.04.3', '1010304004', 'Tinta/Toner Printer', 'Epson AL-M320DN', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.3', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(332, 45, '40.04.3', '1010304004', 'Tinta/Toner Printer', 'Tinta Epson Stylus T 13', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.3', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(333, 45, '40.04.3', '1010304004', 'Tinta/Toner Printer', 'Tinta Epson L.310-664 BK', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.3', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(334, 45, '40.04.3', '1010304004', 'Tinta/Toner Printer', 'Tinta Epson L.310-664 C', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.3', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(335, 45, '40.04.3', '1010304004', 'Tinta/Toner Printer', 'Tinta Epson L.310-664 M', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.3', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(336, 45, '40.04.3', '1010304004', 'Tinta/Toner Printer', 'Tinta Epson L.310-664 Y', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.3', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(337, 45, '40.04.3', '1010304004', 'Tinta/Toner Printer', 'Tinta Epson L1800 Black (T6731)', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.3', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(338, 45, '40.04.3', '1010304004', 'Tinta/Toner Printer', 'Tinta Epson L1800 Cyan (T6732)', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.3', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(339, 45, '40.04.3', '1010304004', 'Tinta/Toner Printer', 'Tinta Epson L1800 Light Cyan(T6735)', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.3', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(340, 45, '40.04.3', '1010304004', 'Tinta/Toner Printer', 'Tinta Epson L1800 Light Magenta(T6736)', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.3', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(341, 45, '40.04.3', '1010304004', 'Tinta/Toner Printer', 'Tinta Epson L1800 Magenta(T6733)', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.3', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(342, 45, '40.04.3', '1010304004', 'Tinta/Toner Printer', 'Tinta Epson L1800 Yellow(T6734)', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.3', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(343, 45, '40.04.3', '1010304004', 'Tinta/Toner Printer', 'Tinta Epson L3210_003 (Black, Cyan, Magenta, Yellow)', 'Botol', 'Botol', 0, 5, 0.00, 'Satuan Standar: Botol | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.3', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(344, 45, '40.04.3', '1010304004', 'Tinta/Toner Printer', 'Tinta Epson L15150 008 Black', 'Botol', 'Botol', 0, 5, 0.00, 'Satuan Standar: Botol | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.3', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(345, 45, '40.04.3', '1010304004', 'Tinta/Toner Printer', 'Tinta Epson L15150 008 Warna', 'Botol', 'Botol', 0, 5, 0.00, 'Satuan Standar: Botol | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.3', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(346, 46, '40.04.4', '1010304004', 'Tinta/Toner Printer', 'HP Smart Tank 750 Ink Printer Refill GT 53 (Black)', 'Botol', 'Botol', 0, 5, 0.00, 'Satuan Standar: Botol | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(347, 46, '40.04.4', '1010304004', 'Tinta/Toner Printer', 'HP Smart Tank 750 Ink Printer Refill GT 52 (C, Y, M)', 'Botol', 'Botol', 0, 5, 0.00, 'Satuan Standar: Botol | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(348, 46, '40.04.4', '1010304004', 'Tinta/Toner Printer', 'Tinta HP Wireless 415 (Black, Cyan, Magenta, Yellow)', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(349, 46, '40.04.4', '1010304004', 'Tinta/Toner Printer', 'Tinta HP GT 53 Hitam', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(350, 46, '40.04.4', '1010304004', 'Tinta/Toner Printer', 'Tinta HP GT 52 Colour', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(351, 46, '40.04.4', '1010304004', 'Tinta/Toner Printer', 'Tinta HP 678 Hitam', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(352, 46, '40.04.4', '1010304004', 'Tinta/Toner Printer', 'Tinta HP 678 Colour', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(353, 46, '40.04.4', '1010304004', 'Tinta/Toner Printer', 'Tinta HP 682 Hitam', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(354, 46, '40.04.4', '1010304004', 'Tinta/Toner Printer', 'Tinta HP 682 Colour', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(355, 46, '40.04.4', '1010304004', 'Tinta/Toner Printer', 'Tinta HP 920 XL Biru', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(356, 46, '40.04.4', '1010304004', 'Tinta/Toner Printer', 'Tinta HP 920 XL Hitam', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(357, 46, '40.04.4', '1010304004', 'Tinta/Toner Printer', 'Tinta HP 920 XL Kuning', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(358, 46, '40.04.4', '1010304004', 'Tinta/Toner Printer', 'Tinta HP 920 XL Merah', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(359, 46, '40.04.4', '1010304004', 'Tinta/Toner Printer', 'Tinta HP 98 (Hitam)', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(360, 46, '40.04.4', '1010304004', 'Tinta/Toner Printer', 'HP Deskjet 1050 802 Black', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(361, 46, '40.04.4', '1010304004', 'Tinta/Toner Printer', 'HP Deskjet 1050 802 Color', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(362, 46, '40.04.4', '1010304004', 'Tinta/Toner Printer', 'HP Deskjet 1280 45 Black', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(363, 46, '40.04.4', '1010304004', 'Tinta/Toner Printer', 'HP Deskjet 1280 78 Color', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(364, 46, '40.04.4', '1010304004', 'Tinta/Toner Printer', 'Tinta HP Deskjet 46 Black', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(365, 46, '40.04.4', '1010304004', 'Tinta/Toner Printer', 'Tinta HP Deskjet 46 Color', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(366, 46, '40.04.4', '1010304004', 'Tinta/Toner Printer', 'Tinta HP Officejet 7110-932/CN57A', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(367, 46, '40.04.4', '1010304004', 'Tinta/Toner Printer', 'Tinta HP Officejet 7110-932/CN58A', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(368, 46, '40.04.4', '1010304004', 'Tinta/Toner Printer', 'Tinta HP Officejet 7110-932/CN59A', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(369, 46, '40.04.4', '1010304004', 'Tinta/Toner Printer', 'Tinta HP Officejet 7110-932/CN60A', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(370, 46, '40.04.4', '1010304004', 'Tinta/Toner Printer', 'Tinta Officejet 200- 62A Black', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(371, 46, '40.04.4', '1010304004', 'Tinta/Toner Printer', 'Tinta Officejet 200- 62A Color', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(372, 46, '40.04.4', '1010304004', 'Tinta/Toner Printer', 'Tinta Printer Canon IP 100 CLI 35 (Black)', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(373, 46, '40.04.4', '1010304004', 'Tinta/Toner Printer', 'Tinta Printer Canon IP 100 CLI 36 (color)', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(374, 46, '40.04.4', '1010304004', 'Tinta/Toner Printer', 'Tinta Printer Color Laserjet Pro M 452 DN CF 410A', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(375, 46, '40.04.4', '1010304004', 'Tinta/Toner Printer', 'Tinta Printer Color Laserjet Pro M 452 DN CF 411A', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(376, 46, '40.04.4', '1010304004', 'Tinta/Toner Printer', 'Tinta Printer HP Officejet 7210 All In One HP 96 (black)', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(377, 46, '40.04.4', '1010304004', 'Tinta/Toner Printer', 'Tinta Printer HP Officejet 7210 All In One HP 97 (color)', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(378, 46, '40.04.4', '1010304004', 'Tinta/Toner Printer', 'Tinta Toner Laserjet 49A', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(379, 46, '40.04.4', '1010304004', 'Tinta/Toner Printer', 'HP 970 XL Black Ink Cartridge', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(380, 46, '40.04.4', '1010304004', 'Tinta/Toner Printer', 'HP 971 XL Cyan INK Cartridge', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(381, 46, '40.04.4', '1010304004', 'Tinta/Toner Printer', 'HP 971 XL Magenta', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(382, 46, '40.04.4', '1010304004', 'Tinta/Toner Printer', 'HP 971 XL Yellow', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(383, 46, '40.04.4', '1010304004', 'Tinta/Toner Printer', 'HP 975 XL Black Ink Cartridge', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(384, 46, '40.04.4', '1010304004', 'Tinta/Toner Printer', 'HP 975 XL Cyan INK Cartridge', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(385, 46, '40.04.4', '1010304004', 'Tinta/Toner Printer', 'HP 975 XL Yellow INK Cartridge', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(386, 46, '40.04.4', '1010304004', 'Tinta/Toner Printer', 'HP 975 XL Magenta INK Cartridge', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(387, 46, '40.04.4', '1010304004', 'Tinta/Toner Printer', 'Toner Printer HP Deskjet 703CD887A Black', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(388, 46, '40.04.4', '1010304004', 'Tinta/Toner Printer', 'Toner Printer HP Deskjet 703CD888A Color', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.4', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(389, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner Cartride HP Laser Jet P1566 (CF 278A/78A)', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(390, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner Cartride HP Laser Jet Pro M12W (CF 279A/79A)', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(391, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner Hewlewtt Packard 85 A', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(392, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP LaseJet 79 A', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(393, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP LaserJet 321', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(394, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'TonerHP LaserJet 322', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(395, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP LaserJet 323', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(396, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner Hewlewtt Packard 21 Black', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(397, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner Hewlewtt Packard 22 Colour', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(398, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner Hewlewtt Packard 126 Black', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(399, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner Hewlewtt Packard CB 542 A (4 buah)', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(400, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner Hewlewtt Packard P 205dn', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(401, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner Hewlewtt Packard CP 5225 (4 buah)', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(402, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner Hewlewtt Packard 305 A Black', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(403, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner Hewlewtt Packard 305A Colour', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(404, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner Hewlewtt Packard 970 Black', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(405, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner Hewlewtt Packard 971 Colour Y/C/M', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(406, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP LaserJet CP 5525', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00');
INSERT INTO `products` (`id`, `category_id`, `code`, `bmn_code`, `bmn_group`, `name`, `unit`, `available_units`, `stock`, `minimum_stock`, `purchase_price`, `description`, `image`, `created_at`, `updated_at`) VALUES
(407, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP Seri 17 A', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(408, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP LaserJet CP 1020 (4 buah)', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(409, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP LaserJet CP 4525 Black 649 X (CE 260 X)', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(410, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP LaserJet CP 5225 Colour', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(411, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP LaserJet CP 5225 CE 740 Black', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(412, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner LaserJet 1102 dan 1022 Black', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(413, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner LaserJet HP 1020', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(414, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP 107 A', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(415, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP 19 A', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(416, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP 79 A', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(417, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'HP Black Toner 05 A', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(418, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'HP Black Toner 125 A (CB 540A)', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(419, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP 12A', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(420, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'HP Black Toner 12A (Q2612A)', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(421, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'HP Black Toner 35A', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(422, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'HP Black Toner 51A', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(423, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'HP Cyan Toner 125 A (CB 541A)', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(424, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Hp Laserjet 1320 49A/5949A', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(425, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'HP Laserjet 202A CF500A BK', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(426, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'HP Laserjet 202A CF501A C', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(427, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'HP Laserjet 202A CF502A M', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(428, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'HP Laserjet 202A CF503A Y', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(429, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'HP Laserjet 307A Black', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(430, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'HP Laserjet 307A Magenta', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(431, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'HP Laserjet 307A Cyan', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(432, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'HP Laserjet 307A Yellow', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(433, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'HP Laserjet 119 A Black', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(434, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'HP Laserjet 119 A Magenta', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(435, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'HP Laserjet 119 A Cyan', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(436, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'HP Laserjet 119 A Yellow', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(437, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'HP Laserjet 305A Magenta', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(438, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'HP Laserjet 305A Cyan', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(439, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'HP Laserjet 305A Yellow', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(440, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'HP Laserjet CP 1025 126 A BK', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(441, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'HP Laserjet CP 1025 126 A C', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(442, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'HP Laserjet CP 1025 126 A M', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(443, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'HP Laserjet CP 1025 126 A Y', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(444, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'HP Laserjet CP 1515N 125A/CB540', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(445, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'HP Laserjet M108a / Toner 110 A', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(446, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'HP Laserjet M102a_HP a7A (Black)', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(447, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'HP Laserjet P1006NCB 435', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(448, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'HP Laserjet P3015', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(449, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'HP Laserjet Pro M452nw 410A BK', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(450, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'HP Laserjet Pro M452nw 410A C', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(451, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'HP Laserjet Pro M452nw 410A M', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(452, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'HP Laserjet Pro M452nw 410A Y', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(453, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'HP Laserjet Pro M454nw / 416 A Black', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(454, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'HP Laserjet Pro M454nw / 416 A Cyan', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(455, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'HP Laserjet Pro M454nw / 416 A Magenta', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(456, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'HP Laserjet Pro M454nw / 416 A Yellow', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(457, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'HP Magenta Toner 125 A (CB 543A)', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(458, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'HP Officerjet 150A 94 Black', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(459, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'HP Officerjet 150A 95 Color', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(460, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'HP Yellow Toner 125 A (CB 542A)', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(461, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'HP Yellow Toner CE 262A', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(462, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Hp 508A Black Toner Cartridge [Cf360A]', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(463, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Hp 508A Cyan Toner Cartridge [Cf361A]', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(464, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Hp 508A Yellow Toner Cartridge [Cf362A]', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(465, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Hp 508A Magenta Toner Cartridge [Cf363A]', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(466, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Tinta Toner Laserjet 49A', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(467, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner 130 A Hitam', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(468, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner 130 A Warna', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(469, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'TONER COLOR LASERJET ENTERPRISE M552 - CF 360 A BLACK', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(470, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'TONER COLOR LASERJET ENTERPRISE M552 - CF 361 A BLUE', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(471, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'TONER COLOR LASERJET ENTERPRISE M552 - CF 362 A YELLOW', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(472, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'TONER COLOR LASERJET ENTERPRISE M552 - CF 363 A MAGENTA', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(473, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP 21 C9351A', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(474, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP 21 C9352A', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(475, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP 26A', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(476, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP 36A', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(477, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP 654A CP 331', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(478, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP 654A CP 332', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(479, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP 95 BK', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(480, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP 95 Color', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(481, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner Hp Cyan CE 261A', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(482, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP Lasejet CB 540A', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(483, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP Lasejet CB 541A', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(484, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP Lasejet CB 543A', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(485, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP Laserjet 53A', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(486, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP Laserjet CE 255 A', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(487, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP Laserjet CE 285 A', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(488, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP Laserjet CE 310A', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(489, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP Laserjet CE 311A', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(490, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP Laserjet CE 312A', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(491, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP Laserjet CE 313A', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(492, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP Laserjet CF 131A', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(493, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP Laserjet CF 131A Cyan', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(494, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP Laserjet CF 131A Yellow', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(495, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP Laserjet CF 131A Magenta', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(496, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP Laserjet CE 410', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(497, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP Laserjet CE 411', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(498, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP Laserjet CE 412', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(499, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP Laserjet CE 413', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(500, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP Laserjet CE 505 A', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(501, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP Laserjet CF 330', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(502, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP Laserjet CF 333', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(503, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP Laserjet CF 410', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(504, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP Laserjet CF 411', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(505, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP Laserjet CF 412', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(506, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP Laserjet CF 413', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(507, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP Laserjet CP2025 CC530 A', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(508, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP Laserjet CP2025 CC531 A', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(509, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP Laserjet CP2025 CC532 A', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(510, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP Laserjet CP2025 CC533 A', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(511, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP Laserjet P1 102-85A', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(512, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner Hp Magenta CE 263A', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(513, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner Laserjet 125A/Black', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(514, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner Laserjet 125A/Blue', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(515, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner Laserjet 125A/Yellow', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(516, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner Laserjet 80A', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(517, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP 651 A Black', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(518, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP 651 A Colour', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(519, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP 215 A Black', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(520, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP 215 A Colour', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(521, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP Laser CP 5225 - HP CE 741A Cyan', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(522, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP Laser CP 5225 - HP CE 742A Yellow', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(523, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP Laser CP 5225 - HP CE 743A Magenta', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(524, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP Laser Pro M252n-HP 201A CF400A Black', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(525, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP Laser Pro M252n-HP 201A CF401A Cyan', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(526, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP Laser Pro M252n-HP 201A CF402A Yellow', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(527, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP Laser Pro M252n-HP 201A CF403A Magenta', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(528, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner HP Laser 83 A', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(529, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner 660 C660', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(530, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner 660 K660', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(531, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner 660 M660', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(532, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Toner 660 Y660', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(533, 47, '40.04.5', '1010304004', 'Tinta/Toner Printer', 'Canon Cartridge 328', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010304004 (Tinta/Toner Printer) | Kode Rincian: 40.04.5', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(534, 48, '40.06.1', '1010304006', 'USB/Flash Disk', 'Converter Type C to HDTV 8 in 1', 'set', 'set', 0, 5, 0.00, 'Satuan Standar: set | BMN: 1010304006 (USB/Flash Disk) | Kode Rincian: 40.06.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(535, 48, '40.06.1', '1010304006', 'USB/Flash Disk', 'USB HUB Converter (Type C Port)', 'set', 'set', 0, 5, 0.00, 'Satuan Standar: set | BMN: 1010304006 (USB/Flash Disk) | Kode Rincian: 40.06.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(536, 49, '40.06.2', '1010304006', 'USB/Flash Disk', 'Sandisk Extreme USB 3.Cz80 16 Gb', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010304006 (USB/Flash Disk) | Kode Rincian: 40.06.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(537, 49, '40.06.2', '1010304006', 'USB/Flash Disk', 'USB 32 GB', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010304006 (USB/Flash Disk) | Kode Rincian: 40.06.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(538, 50, '40.06.3', '1010304006', 'USB/Flash Disk', 'Flashdisk 64 GB', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010304006 (USB/Flash Disk) | Kode Rincian: 40.06.3', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(539, 50, '40.06.3', '1010304006', 'USB/Flash Disk', 'Flashdisk 32 GB', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010304006 (USB/Flash Disk) | Kode Rincian: 40.06.3', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(540, 50, '40.06.3', '1010304006', 'USB/Flash Disk', 'Flashdisk 8 GB', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010304006 (USB/Flash Disk) | Kode Rincian: 40.06.3', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(541, 50, '40.06.3', '1010304006', 'USB/Flash Disk', 'Flashdisk 4 GB', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010304006 (USB/Flash Disk) | Kode Rincian: 40.06.3', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(542, 51, '40.10.1', '1010304010', 'Mouse', 'Mouse Logitech B170', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010304010 (Mouse) | Kode Rincian: 40.10.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(543, 51, '40.10.1', '1010304010', 'Mouse', 'Mouse Logitech M-181', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010304010 (Mouse) | Kode Rincian: 40.10.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(544, 51, '40.10.1', '1010304010', 'Mouse', 'Mouse Logitech M-185', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010304010 (Mouse) | Kode Rincian: 40.10.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(545, 51, '40.10.1', '1010304010', 'Mouse', 'Mouse Logitech M-187', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010304010 (Mouse) | Kode Rincian: 40.10.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(546, 51, '40.10.1', '1010304010', 'Mouse', 'Mouse Logitech M-331', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010304010 (Mouse) | Kode Rincian: 40.10.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(547, 52, '49.99.1', '1010304999', 'Bahan Komputer Lainnya', 'Hardisk 1 TB', 'set', 'set', 0, 5, 0.00, 'Satuan Standar: set | BMN: 1010304999 (Bahan Komputer Lainnya) | Kode Rincian: 49.99.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(548, 52, '49.99.1', '1010304999', 'Bahan Komputer Lainnya', 'Hardisk 2 TB', 'set', 'set', 0, 5, 0.00, 'Satuan Standar: set | BMN: 1010304999 (Bahan Komputer Lainnya) | Kode Rincian: 49.99.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(549, 53, '49.99.2', '1010304999', 'Bahan Komputer Lainnya', 'Corded Keyboard Logitech', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010304999 (Bahan Komputer Lainnya) | Kode Rincian: 49.99.2', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(550, 54, '60.10.1', '1010306010', 'Batu Baterai', 'Baterai AA (isi 8)', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010306010 (Batu Baterai) | Kode Rincian: 60.10.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(551, 54, '60.10.1', '1010306010', 'Batu Baterai', 'Baterai AAA (isi 8)', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010306010 (Batu Baterai) | Kode Rincian: 60.10.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(552, 54, '60.10.1', '1010306010', 'Batu Baterai', 'Baterai AA (isi 4)', 'Set', 'Set', 1, 5, 50000.00, 'Satuan Standar: Set | BMN: 1010306010 (Batu Baterai) | Kode Rincian: 60.10.1', NULL, '2026-01-01 01:00:00', '2026-10-27 02:04:54'),
(553, 54, '60.10.1', '1010306010', 'Batu Baterai', 'Baterai AAA (isi 4)', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010306010 (Batu Baterai) | Kode Rincian: 60.10.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(554, 54, '60.10.1', '1010306010', 'Batu Baterai', 'Baterai AA (isi 2)', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010306010 (Batu Baterai) | Kode Rincian: 60.10.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(555, 54, '60.10.1', '1010306010', 'Batu Baterai', 'Baterai AAA (isi 2)', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010306010 (Batu Baterai) | Kode Rincian: 60.10.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(556, 54, '60.10.1', '1010306010', 'Batu Baterai', 'Baterai Alkaline Size D Besar (2 buah)', 'Set', 'Set', 0, 5, 0.00, 'Satuan Standar: Set | BMN: 1010306010 (Batu Baterai) | Kode Rincian: 60.10.1', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(557, 55, '19.99.13', '1010301999', 'Alat Tulis Kantor Lainnya', 'Recorder Sony', 'buah', 'buah', 0, 5, 0.00, 'Satuan Standar: buah | BMN: 1010301999 (Alat Tulis Kantor Lainnya) | Kode Rincian: 19.99.13', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(558, 56, '19.99.14', '1010301999', 'Alat Tulis Kantor Lainnya', 'Ring O-Bin A4 Ukuran 3/16', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301999 (Alat Tulis Kantor Lainnya) | Kode Rincian: 19.99.14', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(559, 56, '19.99.14', '1010301999', 'Alat Tulis Kantor Lainnya', 'Ring O-Bin A4 Ukuran 4/16', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301999 (Alat Tulis Kantor Lainnya) | Kode Rincian: 19.99.14', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(560, 56, '19.99.14', '1010301999', 'Alat Tulis Kantor Lainnya', 'Ring O-Bin A4 Ukuran 5/16', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301999 (Alat Tulis Kantor Lainnya) | Kode Rincian: 19.99.14', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(561, 56, '19.99.14', '1010301999', 'Alat Tulis Kantor Lainnya', 'Ring O-Bin A4 Ukuran 6/16', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301999 (Alat Tulis Kantor Lainnya) | Kode Rincian: 19.99.14', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(562, 56, '19.99.14', '1010301999', 'Alat Tulis Kantor Lainnya', 'Ring O-Bin A4 Ukuran 7/16', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301999 (Alat Tulis Kantor Lainnya) | Kode Rincian: 19.99.14', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(563, 56, '19.99.14', '1010301999', 'Alat Tulis Kantor Lainnya', 'Ring O-Bin A4 Ukuran 8/16', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301999 (Alat Tulis Kantor Lainnya) | Kode Rincian: 19.99.14', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(564, 56, '19.99.14', '1010301999', 'Alat Tulis Kantor Lainnya', 'Ring O-Bin A4 Ukuran 9/16', 'Dus', 'Dus', 0, 5, 0.00, 'Satuan Standar: Dus | BMN: 1010301999 (Alat Tulis Kantor Lainnya) | Kode Rincian: 19.99.14', NULL, '2026-01-01 01:00:00', '2026-01-01 01:00:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `requests`
--

CREATE TABLE `requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `request_code` varchar(255) NOT NULL,
  `type` enum('individu','subdit') NOT NULL,
  `is_procurement` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(30) NOT NULL DEFAULT 'pending',
  `verified_at` timestamp NULL DEFAULT NULL,
  `verified_by` bigint(20) UNSIGNED DEFAULT NULL,
  `verification_notes` text DEFAULT NULL,
  `purchase_invoice` varchar(255) DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `purchase_price` decimal(15,2) DEFAULT NULL,
  `purchase_notes` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `distributed_at` timestamp NULL DEFAULT NULL,
  `distributed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `receiver_name` varchar(255) DEFAULT NULL,
  `distribution_notes` text DEFAULT NULL,
  `distribution_proof` varchar(255) DEFAULT NULL,
  `signed_proof` varchar(255) DEFAULT NULL,
  `signed_at` timestamp NULL DEFAULT NULL,
  `signed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `requests`
--

INSERT INTO `requests` (`id`, `user_id`, `request_code`, `type`, `is_procurement`, `status`, `verified_at`, `verified_by`, `verification_notes`, `purchase_invoice`, `purchase_date`, `purchase_price`, `purchase_notes`, `notes`, `rejection_reason`, `distributed_at`, `distributed_by`, `receiver_name`, `distribution_notes`, `distribution_proof`, `signed_proof`, `signed_at`, `signed_by`, `created_at`, `updated_at`) VALUES
(34, 263, 'REQ-BUY-20261026-F1HV', 'individu', 1, 'approved', '2026-10-26 02:07:44', 1, 'Seluruh barang telah ditambahkan ke Master Data dan kini tersedia di Katalog ATK.', '123/FK', '2026-10-26', 130000.00, 'Cv. Milia Pertama', 'Keperluan pribadi', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-26 01:31:01', '2026-10-26 02:58:21'),
(120, 263, 'REQ-20261026-QTIA', 'individu', 0, 'distributed', '2026-10-26 03:31:02', 1, NULL, NULL, NULL, NULL, NULL, 'Keperluan mandiri', NULL, '2026-10-26 03:32:36', 261, 'Bagus Sadewo', NULL, 'distribution_proofs/proof_REQ-20261026-QTIA_PQ83ddoA7iAIts2f.webp', NULL, NULL, NULL, '2026-10-26 03:25:20', '2026-10-26 03:32:36'),
(129, 262, 'REQ-BUY-20261026-T4SM', 'subdit', 1, 'approved', '2026-10-26 04:05:16', 1, 'Seluruh barang telah ditambahkan ke Master Data dan kini tersedia di Katalog ATK.', '125/FK', '2026-10-26', 310000.00, 'Cv. Milia Pertama', 'Kebutuhan Subdirektorat SDK', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-26 04:01:41', '2026-10-26 04:06:29'),
(130, 262, 'REQ-20261026-1VF5', 'subdit', 0, 'distributed', '2026-10-26 04:10:53', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-26 04:11:39', 261, 'Fairuz Armina Nur Azizah', NULL, 'distribution_proofs/proof_REQ-20261026-1VF5_ZxFz4obVry2ZV9YS.png', NULL, NULL, NULL, '2026-10-26 04:08:16', '2026-10-26 04:11:39'),
(131, 262, 'REQ-BUY-20261026-I8AB', 'subdit', 1, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'butuh', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-26 07:27:33', '2026-10-26 07:27:33');

-- --------------------------------------------------------

--
-- Struktur dari tabel `request_details`
--

CREATE TABLE `request_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `request_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED DEFAULT NULL,
  `custom_product_name` varchar(255) DEFAULT NULL,
  `custom_unit` varchar(255) DEFAULT NULL,
  `specification` varchar(255) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `approved_quantity` int(11) DEFAULT NULL,
  `verification_note` varchar(255) DEFAULT NULL,
  `is_fulfilled` tinyint(1) NOT NULL DEFAULT 0,
  `fulfilled_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `request_details`
--

INSERT INTO `request_details` (`id`, `request_id`, `product_id`, `custom_product_name`, `custom_unit`, `specification`, `quantity`, `approved_quantity`, `verification_note`, `is_fulfilled`, `fulfilled_at`, `created_at`, `updated_at`) VALUES
(19, 34, 293, 'Amplop Coklat A4 Air Mail (isi 10)', 'Pak', NULL, 1, 1, NULL, 1, '2026-10-26 02:58:21', '2026-10-26 01:31:01', '2026-10-26 02:58:21'),
(20, 34, 552, 'Baterai AA (isi 4)', 'Set', NULL, 1, 1, NULL, 1, '2026-10-26 02:57:52', '2026-10-26 01:31:01', '2026-10-26 02:57:52'),
(21, 34, 245, 'Bak Stempel Besar', 'buah', NULL, 1, 1, NULL, 1, '2026-10-26 02:54:08', '2026-10-26 01:31:01', '2026-10-26 02:54:08'),
(92, 120, 245, 'Bak Stempel Besar', 'buah', 'Barang Katalog (19.99.9)', 10, 10, NULL, 0, NULL, '2026-10-26 03:25:20', '2026-10-26 03:31:02'),
(98, 129, 293, 'Amplop Coklat A4 Air Mail (isi 10)', 'Pak', NULL, 1, 1, NULL, 1, '2026-10-26 04:06:29', '2026-10-26 04:01:41', '2026-10-26 04:06:29'),
(99, 129, 246, 'Bak Stempel Kecil', 'buah', NULL, 1, 1, NULL, 1, '2026-10-26 04:06:00', '2026-10-26 04:01:41', '2026-10-26 04:06:00'),
(100, 130, 293, 'Amplop Coklat A4 Air Mail (isi 10)', 'Pak', 'Barang Katalog (20.04.1)', 1, 1, NULL, 0, NULL, '2026-10-26 04:08:16', '2026-10-26 04:10:53'),
(101, 131, 293, 'Amplop Coklat A4 Air Mail (isi 10)', 'Pak', 'Barang Katalog (20.04.1)', 2, NULL, NULL, 0, NULL, '2026-10-26 07:27:33', '2026-10-26 07:27:33');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sessions`
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
-- Dumping data untuk tabel `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('7LUO5bEeEKLIySMy8QUxcboGCYzXBDrKqY40X744', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiUmUxT2t0SnFxV2p1bWxrS292T1lYRGpuOWkxRXpOQjFpck9maEl4RSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9tYXN0ZXItZGF0YS9kYWZ0YXItYmFyYW5nIjtzOjU6InJvdXRlIjtzOjMxOiJtYXN0ZXItZGF0YS5kYWZ0YXItYmFyYW5nLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1793064843);

-- --------------------------------------------------------

--
-- Struktur dari tabel `stock_transactions`
--

CREATE TABLE `stock_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('masuk','keluar') NOT NULL,
  `recipient_type` varchar(20) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `satuan_beli` varchar(50) DEFAULT NULL,
  `jumlah_beli` int(11) DEFAULT NULL,
  `isi_per_kemasan` int(11) DEFAULT NULL,
  `stock_after` int(11) NOT NULL DEFAULT 0,
  `transaction_date` date NOT NULL,
  `no_faktur` varchar(255) DEFAULT NULL,
  `uraian` varchar(255) DEFAULT NULL,
  `kondisi` varchar(255) NOT NULL DEFAULT 'Baru',
  `harga_perolehan` decimal(15,2) NOT NULL DEFAULT 0.00,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `stock_transactions`
--

INSERT INTO `stock_transactions` (`id`, `product_id`, `user_id`, `type`, `recipient_type`, `quantity`, `satuan_beli`, `jumlah_beli`, `isi_per_kemasan`, `stock_after`, `transaction_date`, `no_faktur`, `uraian`, `kondisi`, `harga_perolehan`, `note`, `created_at`, `updated_at`) VALUES
(29, 245, 260, 'masuk', NULL, 10, NULL, NULL, NULL, 10, '2026-10-26', '121/FK', 'Cv. Milia Pertama', 'Baru', 50000.00, 'Pembelian Bak Stempel Besar', '2026-10-26 02:54:08', '2026-10-26 02:54:08'),
(30, 552, 260, 'masuk', NULL, 1, NULL, NULL, NULL, 1, '2026-10-26', '122/FK', 'Cv. Milia Pertama', 'Baru', 50000.00, 'Pembelian Baterai AA (isi 4)', '2026-10-26 02:57:52', '2026-10-26 02:57:52'),
(31, 293, 260, 'masuk', NULL, 1, NULL, NULL, NULL, 1, '2026-10-26', '125/FK', 'Cv. Milia Pertama', 'Baru', 250000.00, 'Pembelian Amplop Coklat A4 Air Mail (isi 10)', '2026-10-26 02:58:21', '2026-10-26 04:06:29'),
(96, 245, 1, 'keluar', 'individu', 10, NULL, NULL, NULL, 0, '2026-10-26', 'REQ-20261026-QTIA', 'Bagus Sadewo (Asisten Penyusunan Data & Pelaporan)', 'Baik', 50000.00, 'Pengeluaran Bak Stempel Besar Untuk Bagus Sadewo', '2026-10-26 03:31:02', '2026-10-26 03:31:02'),
(109, 246, 260, 'masuk', NULL, 1, NULL, NULL, NULL, 1, '2026-10-26', '124/FK', 'Cv. Milia Pertama', 'Baru', 60000.00, 'Pembelian Bak Stempel Kecil', '2026-10-26 04:06:00', '2026-10-26 04:06:00'),
(110, 293, 1, 'keluar', 'subdit', 1, NULL, NULL, NULL, 0, '2026-10-26', 'REQ-20261026-1VF5', 'Fairuz Armina Nur Azizah (PIC Distribusi Subdirektorat SDK)', 'Baik', 250000.00, 'Pengeluaran Amplop Coklat A4 Air Mail (isi 10) Untuk Fairuz Armina Nur Azizah', '2026-10-26 04:10:53', '2026-10-26 04:10:53');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) DEFAULT NULL,
  `is_distributor` tinyint(1) NOT NULL DEFAULT 0,
  `is_report_examiner` tinyint(1) NOT NULL DEFAULT 0,
  `position` varchar(255) DEFAULT NULL,
  `nip` varchar(255) DEFAULT NULL,
  `subdit_name` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `title`, `email`, `email_verified_at`, `password`, `role`, `is_distributor`, `is_report_examiner`, `position`, `nip`, `subdit_name`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Utami M', NULL, 'utami@gmail.com', NULL, '$2y$12$.X6p2ZZQLiIdAmLIWfAJP.vmWn0yRc2ENaYFRtR0aQqr.rI0Q3uu.', 'kasubag_tu', 0, 0, 'Kasubbag TU', '19850612 201001 2 003', 'Subbagian Tata Usaha', NULL, '2026-10-26 00:34:53', '2026-10-26 00:34:53'),
(260, 'Irvan', 'S.K.', 'irvan@pkp.com', NULL, '$2y$12$9HL1l6gJTueR5mngCayUQejqzz0ZnTtNqzZT8upwd.6GHGpqc2UaW', 'pegawai_gudang', 0, 1, 'Pegawai Gudang', '123455678910', 'Gudang Persediaan ATK', NULL, '2026-10-26 01:13:30', '2026-10-26 01:13:30'),
(261, 'Devi ismiyantati', 'S.T.', 'devi@pkp.com', NULL, '$2y$12$6gQuSHOWWs8JGptLy/44O.qcT3RBnXgCk7v1p5IzgQzb5gzQEkZO.', 'pegawai_gudang', 1, 0, 'Petugas Distribusi Gudang ATK', '12345678911', 'Gudang Persediaan ATK', NULL, '2026-10-26 01:14:33', '2026-10-26 01:14:33'),
(262, 'Fairuz Armina Nur Azizah', 'S.T.', 'fairuz@pkp.com', NULL, '$2y$12$uGuX2hFn835CNl8Rpc/0J.hIlCoinT.YYcttovq1EVGziCQA7vZKK', 'pic_distribusi', 0, 0, 'PIC Distribusi Subdirektorat SDK', '12345678912', 'Subdirektorat Sumber Daya Konstruksi Perumahan dan Kawasan Permukiman', NULL, '2026-10-26 01:15:37', '2026-10-26 01:15:37'),
(263, 'Bagus Sadewo', 'S.Kom,', 'bgsdewo@pkp.com', NULL, '$2y$12$69DvXWNR7gg7nK9imo3oVuiIj5dwizSlSrPe7vz.CnE4W6aHXRMm2', 'pegawai', 0, 0, 'Asisten Penyusunan Data & Pelaporan', '12345678913', 'Pegawai Perorangan', NULL, '2026-10-26 01:27:28', '2026-10-26 01:27:28');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_notifications`
--

CREATE TABLE `user_notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'procurement_available',
  `icon` varchar(50) NOT NULL DEFAULT 'box',
  `link` varchar(255) DEFAULT NULL,
  `request_id` bigint(20) UNSIGNED DEFAULT NULL,
  `product_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `user_notifications`
--

INSERT INTO `user_notifications` (`id`, `user_id`, `title`, `message`, `type`, `icon`, `link`, `request_id`, `product_id`, `is_read`, `read_at`, `data`, `created_at`, `updated_at`) VALUES
(2, 263, 'Usulan Pengadaan Disetujui', 'Usulan pengadaan \"Amplop Coklat A4 Air Mail (isi 10)\" telah disetujui Kasubbag TU sebanyak 1 Pak. Barang sedang dalam proses pembelian oleh Subbag TU & Gudang.', 'procurement_approved', 'shopping-bag', 'http://127.0.0.1:8000/pengajuan', 34, 293, 1, '2026-10-26 02:56:42', '{\"product_name\":\"Amplop Coklat A4 Air Mail (isi 10)\",\"request_code\":\"REQ-BUY-20261026-F1HV\",\"approved_quantity\":1}', '2026-10-26 02:07:44', '2026-10-26 02:56:42'),
(3, 263, 'Usulan Pengadaan Disetujui', 'Usulan pengadaan \"Baterai AA (isi 4)\" telah disetujui Kasubbag TU sebanyak 1 Set. Barang sedang dalam proses pembelian oleh Subbag TU & Gudang.', 'procurement_approved', 'shopping-bag', 'http://127.0.0.1:8000/pengajuan', 34, 552, 1, '2026-10-26 02:56:42', '{\"product_name\":\"Baterai AA (isi 4)\",\"request_code\":\"REQ-BUY-20261026-F1HV\",\"approved_quantity\":1}', '2026-10-26 02:07:44', '2026-10-26 02:56:42'),
(4, 263, 'Usulan Pengadaan Disetujui', 'Usulan pengadaan \"Bak Stempel Besar\" telah disetujui Kasubbag TU sebanyak 1 buah. Barang sedang dalam proses pembelian oleh Subbag TU & Gudang.', 'procurement_approved', 'shopping-bag', 'http://127.0.0.1:8000/pengajuan', 34, 245, 1, '2026-10-26 02:56:42', '{\"product_name\":\"Bak Stempel Besar\",\"request_code\":\"REQ-BUY-20261026-F1HV\",\"approved_quantity\":1}', '2026-10-26 02:07:44', '2026-10-26 02:56:42'),
(15, 263, 'Barang yang Diminta Sudah Tersedia!', 'Barang \"Bak Stempel Besar\" pada pengajuan #REQ-BUY-20261026-F1HV telah ditambahkan di Master Data dan kini stoknya sudah tersedia di Katalog ATK.', 'procurement_available', 'box', 'http://127.0.0.1:8000/katalog-atk?search=19.99.9', 34, 245, 1, '2026-10-26 02:56:03', '{\"product_name\":\"Bak Stempel Besar\",\"request_code\":\"REQ-BUY-20261026-F1HV\",\"invoice\":\"121\\/FK\"}', '2026-10-26 02:54:08', '2026-10-26 02:56:03'),
(16, 263, 'Barang yang Diminta Sudah Tersedia!', 'Barang \"Baterai AA (isi 4)\" pada pengajuan #REQ-BUY-20261026-F1HV telah ditambahkan di Master Data dan kini stoknya sudah tersedia di Katalog ATK.', 'procurement_available', 'box', 'http://127.0.0.1:8000/katalog-atk?search=60.10.1', 34, 552, 1, '2026-10-26 03:22:47', '{\"product_name\":\"Baterai AA (isi 4)\",\"request_code\":\"REQ-BUY-20261026-F1HV\",\"invoice\":\"122\\/FK\"}', '2026-10-26 02:57:52', '2026-10-26 03:22:47'),
(17, 263, 'Barang yang Diminta Sudah Tersedia!', 'Barang \"Amplop Coklat A4 Air Mail (isi 10)\" pada pengajuan #REQ-BUY-20261026-F1HV telah ditambahkan di Master Data dan kini stoknya sudah tersedia di Katalog ATK.', 'procurement_available', 'box', 'http://127.0.0.1:8000/katalog-atk?search=20.04.1', 34, 293, 1, '2026-10-26 03:22:40', '{\"product_name\":\"Amplop Coklat A4 Air Mail (isi 10)\",\"request_code\":\"REQ-BUY-20261026-F1HV\",\"invoice\":\"123\\/FK\"}', '2026-10-26 02:58:21', '2026-10-26 03:22:40'),
(33, 262, 'Usulan Pengadaan Disetujui', 'Usulan pengadaan \"Amplop Coklat A4 Air Mail (isi 10)\" telah disetujui Kasubbag TU sebanyak 1 Pak. Barang sedang dalam proses pembelian oleh Subbag TU & Gudang.', 'procurement_approved', 'shopping-bag', 'http://127.0.0.1:8000/pengajuan', 129, 293, 1, '2026-10-26 04:08:24', '{\"product_name\":\"Amplop Coklat A4 Air Mail (isi 10)\",\"request_code\":\"REQ-BUY-20261026-T4SM\",\"approved_quantity\":1}', '2026-10-26 04:05:16', '2026-10-26 04:08:24'),
(34, 262, 'Usulan Pengadaan Disetujui', 'Usulan pengadaan \"Bak Stempel Kecil\" telah disetujui Kasubbag TU sebanyak 1 buah. Barang sedang dalam proses pembelian oleh Subbag TU & Gudang.', 'procurement_approved', 'shopping-bag', 'http://127.0.0.1:8000/pengajuan', 129, 246, 1, '2026-10-26 04:08:24', '{\"product_name\":\"Bak Stempel Kecil\",\"request_code\":\"REQ-BUY-20261026-T4SM\",\"approved_quantity\":1}', '2026-10-26 04:05:16', '2026-10-26 04:08:24'),
(35, 260, 'Usulan Pengadaan Disetujui Kasubbag TU', 'Kasubbag TU telah menyetujui usulan pengadaan #REQ-BUY-20261026-T4SM dari Fairuz Armina Nur Azizah, S.T. (Amplop Coklat A4 Air Mail (isi 10) (1 Pak), Bak Stempel Kecil (1 buah)). Silakan lakukan penambahan stok barang di Master Data.', 'procurement_approved_examiner', 'box', 'http://127.0.0.1:8000/master-data/daftar-barang#pengadaan-section', 129, 293, 1, '2026-10-26 04:06:10', '{\"request_code\":\"REQ-BUY-20261026-T4SM\",\"requester\":\"Fairuz Armina Nur Azizah, S.T.\",\"unit_name\":\"PIC Distribusi Subdirektorat SDK\",\"item_summary\":\"Amplop Coklat A4 Air Mail (isi 10) (1 Pak), Bak Stempel Kecil (1 buah)\",\"total_items\":2}', '2026-10-26 04:05:16', '2026-10-26 04:06:10'),
(36, 262, 'Barang yang Diminta Sudah Tersedia!', 'Barang \"Bak Stempel Kecil\" pada pengajuan #REQ-BUY-20261026-T4SM telah ditambahkan di Master Data dan kini stoknya sudah tersedia di Katalog ATK.', 'procurement_available', 'box', 'http://127.0.0.1:8000/katalog-atk?search=19.99.9', 129, 246, 1, '2026-10-26 04:08:24', '{\"product_name\":\"Bak Stempel Kecil\",\"request_code\":\"REQ-BUY-20261026-T4SM\",\"invoice\":\"124\\/FK\"}', '2026-10-26 04:06:00', '2026-10-26 04:08:24'),
(37, 262, 'Barang yang Diminta Sudah Tersedia!', 'Barang \"Amplop Coklat A4 Air Mail (isi 10)\" pada pengajuan #REQ-BUY-20261026-T4SM telah ditambahkan di Master Data dan kini stoknya sudah tersedia di Katalog ATK.', 'procurement_available', 'box', 'http://127.0.0.1:8000/katalog-atk?search=20.04.1', 129, 293, 1, '2026-10-26 04:07:44', '{\"product_name\":\"Amplop Coklat A4 Air Mail (isi 10)\",\"request_code\":\"REQ-BUY-20261026-T4SM\",\"invoice\":\"125\\/FK\"}', '2026-10-26 04:06:29', '2026-10-26 04:07:44');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indeks untuk tabel `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indeks untuk tabel `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indeks untuk tabel `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indeks untuk tabel `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indeks untuk tabel `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `products_category_id_foreign` (`category_id`);

--
-- Indeks untuk tabel `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `requests_request_code_unique` (`request_code`),
  ADD KEY `requests_user_id_foreign` (`user_id`),
  ADD KEY `requests_distributed_by_foreign` (`distributed_by`),
  ADD KEY `requests_verified_by_foreign` (`verified_by`),
  ADD KEY `requests_signed_by_foreign` (`signed_by`);

--
-- Indeks untuk tabel `request_details`
--
ALTER TABLE `request_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `request_details_request_id_foreign` (`request_id`),
  ADD KEY `request_details_product_id_foreign` (`product_id`);

--
-- Indeks untuk tabel `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indeks untuk tabel `stock_transactions`
--
ALTER TABLE `stock_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_transactions_product_id_foreign` (`product_id`),
  ADD KEY `stock_transactions_user_id_foreign` (`user_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indeks untuk tabel `user_notifications`
--
ALTER TABLE `user_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_notifications_request_id_foreign` (`request_id`),
  ADD KEY `user_notifications_product_id_foreign` (`product_id`),
  ADD KEY `user_notifications_user_id_is_read_index` (`user_id`,`is_read`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=219;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT untuk tabel `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=887;

--
-- AUTO_INCREMENT untuk tabel `requests`
--
ALTER TABLE `requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=132;

--
-- AUTO_INCREMENT untuk tabel `request_details`
--
ALTER TABLE `request_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT untuk tabel `stock_transactions`
--
ALTER TABLE `stock_transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=971;

--
-- AUTO_INCREMENT untuk tabel `user_notifications`
--
ALTER TABLE `user_notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `requests`
--
ALTER TABLE `requests`
  ADD CONSTRAINT `requests_distributed_by_foreign` FOREIGN KEY (`distributed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `requests_signed_by_foreign` FOREIGN KEY (`signed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `requests_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `request_details`
--
ALTER TABLE `request_details`
  ADD CONSTRAINT `request_details_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `request_details_request_id_foreign` FOREIGN KEY (`request_id`) REFERENCES `requests` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `stock_transactions`
--
ALTER TABLE `stock_transactions`
  ADD CONSTRAINT `stock_transactions_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `user_notifications`
--
ALTER TABLE `user_notifications`
  ADD CONSTRAINT `user_notifications_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `user_notifications_request_id_foreign` FOREIGN KEY (`request_id`) REFERENCES `requests` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `user_notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
