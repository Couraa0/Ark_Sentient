-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 01 Sep 2025 pada 10.49
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
-- Database: `e1`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `livestock_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `livestock_id`, `quantity`, `added_at`) VALUES
(2, 3, 3, 1, '2025-07-21 06:19:38');

-- --------------------------------------------------------

--
-- Struktur dari tabel `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `categories`
--

INSERT INTO `categories` (`id`, `name`, `icon`, `created_at`) VALUES
(1, 'Cow', 'fa-cow', '2025-07-21 06:19:38'),
(2, 'Goat', 'fa-goat', '2025-07-21 06:19:38'),
(3, 'Chicken', 'fa-chicken', '2025-07-21 06:19:38'),
(4, 'Seafood', 'fa-fish', '2025-07-21 06:19:38');

-- --------------------------------------------------------

--
-- Struktur dari tabel `conversations`
--

CREATE TABLE `conversations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `conversations`
--

INSERT INTO `conversations` (`id`, `user_id`, `title`, `created_at`) VALUES
(15, 7, 'Halo', '2025-07-29 12:48:22'),
(16, 7, 'Ternak saya gatal', '2025-07-29 12:51:16'),
(17, 7, 'Elanoy', '2025-07-29 12:53:31'),
(18, 7, 'Halo', '2025-08-03 12:36:24'),
(19, 7, 'Halo', '2025-08-06 13:13:16'),
(20, 1, 'Haloo beri saya jadwal ternak', '2025-08-09 07:16:47'),
(21, 7, 'Kambing saya sakit', '2025-08-09 14:27:29'),
(22, 1, 'Halo', '2025-09-01 08:06:35'),
(23, 1, 'Halo', '2025-09-01 08:07:12');

-- --------------------------------------------------------

--
-- Struktur dari tabel `livestock`
--

CREATE TABLE `livestock` (
  `id` int(11) NOT NULL,
  `farmer_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `breed` varchar(100) DEFAULT NULL,
  `age_months` int(11) NOT NULL,
  `weight_kg` int(11) NOT NULL,
  `gender` enum('male','female') NOT NULL,
  `location` varchar(100) NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `health_status` varchar(100) DEFAULT 'Healthy',
  `vaccination_status` varchar(100) DEFAULT 'Up to date',
  `status` enum('available','sold','reserved') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `livestock`
--

INSERT INTO `livestock` (`id`, `farmer_id`, `category_id`, `name`, `breed`, `age_months`, `weight_kg`, `gender`, `location`, `price`, `description`, `image_url`, `health_status`, `vaccination_status`, `status`, `created_at`, `updated_at`) VALUES
(3, 2, 1, 'Limousin Cattle', 'Limousin', 36, 700, 'male', 'Bogor', 30000000.00, 'Sapi Limousin jantan dengan berat optimal', '../Asset/img/Cow1.jpeg', 'Healthy', 'Up to date', 'available', '2025-07-21 06:19:38', '2025-08-08 16:14:44'),
(4, 2, 1, 'Simmental Cattle', 'Simmental', 28, 620, 'female', 'Bandung', 26000000.00, 'Sapi Simmental betina berkualitas premium', '../Asset/img/Cow2.jpeg', 'Healthy', 'Up to date', 'available', '2025-07-21 06:19:38', '2025-08-08 16:14:59'),
(17, 2, 2, 'Kambing Etawa', 'Etawa', 18, 55, 'male', 'Yogyakarta', 3500000.00, 'Kambing Etawa jantan sehat dan siap kawin', '../Asset/img/Goat1.jpeg', 'Healthy', 'Up to date', 'available', '2025-08-08 13:21:46', '2025-08-08 15:34:08'),
(18, 2, 2, 'Kambing Kacang', 'Kacang', 12, 30, 'female', 'Magelang', 1500000.00, 'Kambing Kacang betina sehat dan produktif', '../Asset/img/Goat2.jpeg', 'Healthy', 'Up to date', 'available', '2025-08-08 13:21:46', '2025-08-08 15:38:31'),
(19, 2, 2, 'Kambing Boer', 'Boer', 20, 60, 'male', 'Sleman', 4500000.00, 'Kambing Boer unggul untuk pembibitan', '../Asset/img/Goat3.jpeg', 'Healthy', 'Up to date', 'available', '2025-08-08 13:21:46', '2025-08-08 15:39:14'),
(20, 2, 2, 'Kambing PE', 'Peranakan Etawa', 16, 50, 'female', 'Bantul', 3000000.00, 'Kambing PE betina berkualitas tinggi', '../Asset/img/Goat2.jpeg', 'Healthy', 'Up to date', 'available', '2025-08-08 13:21:46', '2025-08-08 15:40:40'),
(21, 2, 2, 'Kambing Jawarandu', 'Jawarandu', 14, 45, 'male', 'Kulon Progo', 2500000.00, 'Kambing Jawarandu jantan siap potong', '../Asset/img/Goat5.jpeg', 'Healthy', 'Up to date', 'available', '2025-08-08 13:21:46', '2025-08-08 15:46:56'),
(22, 2, 3, 'Ayam Broiler', 'Broiler', 2, 2, 'male', 'Cianjur', 50000.00, 'Ayam Broiler jantan sehat siap panen', '../Asset/img/Chc1.jpg', 'Healthy', 'Up to date', 'available', '2025-08-08 13:21:46', '2025-08-08 16:16:12'),
(23, 2, 3, 'Ayam Kampung', 'Kampung', 6, 1, 'female', 'Garut', 75000.00, 'Ayam Kampung betina untuk produksi telur', '../Asset/img/Chc2.jpeg', 'Healthy', 'Up to date', 'available', '2025-08-08 13:21:46', '2025-08-08 16:16:30'),
(27, 2, 4, 'Ikan Nila', 'Nila Merah', 6, 1, 'male', 'Depok', 20000.00, 'Ikan Nila Merah segar', '../Asset/img/Sf1.jpeg', 'Healthy', 'Up to date', 'available', '2025-08-08 13:21:46', '2025-08-09 00:43:16'),
(28, 2, 4, 'Udang Vaname', 'Vannamei', 4, 0, 'male', 'Bekasi', 40000.00, 'Udang Vaname kualitas ekspor', '../Asset/img/Udang1.jpeg', 'Healthy', 'Up to date', 'available', '2025-08-08 13:21:46', '2025-08-08 16:17:35');

-- --------------------------------------------------------

--
-- Struktur dari tabel `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `conversation_id` int(11) NOT NULL,
  `sender` enum('user','ai') NOT NULL,
  `text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `messages`
--

INSERT INTO `messages` (`id`, `conversation_id`, `sender`, `text`, `created_at`) VALUES
(35, 15, 'user', 'Halo', '2025-07-29 12:48:22'),
(36, 16, 'user', 'Ternak saya gatal', '2025-07-29 12:51:16'),
(37, 16, 'ai', 'Maaf mendengar ternak Anda mengalami gatal.  Untuk membantu saya memberikan saran yang tepat, saya perlu informasi lebih lanjut.  Bisakah Anda memberi tahu saya:\n\n* **Jenis ternak apa yang Anda miliki?** (misalnya, sapi, kambing, ayam, dll.)\n* **Seberapa parah gatalnya?** (misalnya, sedikit gatal, gatal parah, menggaruk terus-menerus)\n* **Apakah ada bagian tubuh tertentu yang lebih gatal?**\n* **Apakah ada tanda-tanda lain, seperti ruam, luka, atau perubahan perilaku?** (misalnya, kehilangan nafsu makan, lesu)\n* **Apakah Anda telah mengubah sesuatu baru-baru ini, seperti makanan, lingkungan, atau perawatan ternak?**\n\nInformasi ini akan membantu saya menentukan kemungkinan penyebab gatal dan menyarankan langkah-langkah selanjutnya.  Jika Anda memiliki foto, itu juga akan sangat membantu.\n\nSebagai catatan, saya bukan pengganti saran dari dokter hewan.  Jika gatalnya parah atau Anda khawatir, sebaiknya segera konsultasikan dengan dokter hewan untuk diagnosis dan perawatan yang tepat.\n', '2025-07-29 12:52:37'),
(38, 17, 'user', 'Ternak saya mati gimana', '2025-07-29 12:53:31'),
(39, 17, 'ai', 'Saya turut berduka cita atas kehilangan ternak Anda.  Sayang sekali mendengarnya.  Untuk membantu saya memahami situasi dan memberi Anda saran yang tepat, bisakah Anda memberi tahu saya jenis ternak apa yang mati dan apa yang mungkin menjadi penyebab kematiannya? Informasi ini akan membantu saya mengarahkan Anda ke sumber daya yang tepat, seperti panduan perawatan ternak atau mungkin menghubungi ahli ternak. Apakah Anda menggunakan fitur Pemeriksa Ternak di ARK Sentient untuk memantau kesehatan ternak Anda?  Fitur ini dapat membantu mendeteksi masalah kesehatan lebih awal.\n', '2025-07-29 12:54:28'),
(40, 17, 'user', 'Badan di hewan ternak saya banyak ruam merah, kira2 kenapa ya?', '2025-07-29 13:10:56'),
(41, 16, 'user', 'Gatalnya parahh', '2025-07-29 13:22:47'),
(42, 16, 'ai', 'Maaf mendengar ternak Anda mengalami gatal yang parah.  Karena saya tidak bisa mendiagnosis masalah ternak Anda, sangat penting untuk segera menghubungi dokter hewan.  Gatal yang parah bisa menjadi indikasi berbagai masalah kesehatan yang serius, dan penanganan segera sangat penting untuk kenyamanan dan kesejahteraan ternak Anda.\n\nSementara Anda menunggu untuk menghubungi atau mengunjungi dokter hewan, Anda dapat mencoba untuk:\n\n* **Mengisolasi ternak yang terkena:** Ini akan mencegah penyebaran penyakit jika itu menular.\n* **Memberikan lingkungan yang tenang dan nyaman:**  Kurangi stres sebanyak mungkin.\n* **Mencatat gejala:** Catat setiap perubahan yang Anda lihat, seperti perubahan perilaku, nafsu makan, atau lokasi gatal. Ini akan membantu dokter hewan dalam diagnosis.\n\nIngat, informasi yang saya berikan di sini bukanlah pengganti nasihat profesional dari dokter hewan.  Segera hubungi dokter hewan untuk mendapatkan diagnosis dan perawatan yang tepat.\n', '2025-07-29 13:22:50'),
(43, 18, 'user', 'Halo', '2025-08-03 12:36:24'),
(44, 18, 'ai', 'Halo! Ada yang bisa saya bantu mengenai ternak atau fitur ARK Sentient, seperti Marketplace Ternak, Pemeriksa Ternak, atau Sejarah?\n', '2025-08-03 12:36:24'),
(45, 19, 'user', 'Halo', '2025-08-06 13:13:17'),
(46, 19, 'ai', 'Hai! Ada yang bisa saya bantu mengenai ternak atau fitur ARK Sentient, seperti Marketplace Ternak, Pemeriksa Ternak, atau Sejarah?\n', '2025-08-06 13:13:18'),
(47, 19, 'user', 'Sapi saya sakit, berikan solusi', '2025-08-06 13:13:34'),
(48, 19, 'ai', 'Maaf mendengar sapi Anda sakit.  Saya bukan dokter hewan, jadi saya tidak bisa memberikan diagnosis atau perawatan medis.  Untuk mendapatkan bantuan yang tepat, saya sangat menyarankan Anda untuk menghubungi dokter hewan setempat sesegera mungkin.  Mereka dapat memeriksa sapi Anda, menentukan penyebab penyakitnya, dan memberikan pengobatan yang tepat.\n\nSementara itu, Anda bisa mencoba hal-hal berikut untuk memberikan kenyamanan pada sapi Anda:\n\n* **Isolasi:** Pisahkan sapi yang sakit dari sapi lainnya untuk mencegah penyebaran penyakit.\n* **Perhatikan tanda-tanda vital:** Pantau suhu tubuh, nafsu makan, dan perilaku sapi Anda. Catat perubahan apa pun yang Anda amati dan laporkan kepada dokter hewan.\n* **Pastikan akses ke water bersih:** Pastikan sapi Anda selalu memiliki akses ke air bersih dan segar.\n* **Berikan makanan yang mudah dicerna:**  Jika sapi Anda kesulitan makan, berikan makanan yang lembut dan mudah dicerna.\n\nIngat, ini hanya langkah-langkah sementara.  **Konsultasi dengan dokter hewan sangat penting untuk perawatan yang tepat dan efektif.**  Semakin cepat Anda mendapatkan bantuan profesional, semakin baik peluang sapi Anda untuk pulih.\n', '2025-08-06 13:14:03'),
(49, 20, 'user', 'Haloo beri saya jadwal ternak', '2025-08-09 07:16:47'),
(50, 20, 'ai', 'Hai!  Aku senang bisa membantumu dengan jadwal ternakmu.  Namun, aku perlu informasi lebih lanjut untuk memberikan jadwal yang tepat.  \n\nBisakah kamu ceritakan:\n\n* **Jenis ternak apa yang kamu miliki?** (misalnya, Dodo, Parasaur, Ankylosaurus, dll.)\n* **Apa tujuan dari jadwal ini?** (misalnya, untuk memberi makan, mengumpulkan sumber daya, pembiakan, atau lainnya?)\n* **Apakah ada informasi khusus yang ingin kamu sertakan dalam jadwal?** (misalnya, waktu istirahat, perawatan khusus, dll.)\n\nSemakin banyak informasi yang kamu berikan, semakin akurat dan bermanfaat jadwalku untukmu.\n', '2025-08-09 07:16:49'),
(51, 21, 'user', 'Kambing saya sakit', '2025-08-09 14:27:29'),
(52, 21, 'ai', 'Maaf mendengar kambing Anda sakit. Saya tidak bisa memberikan saran medis, tetapi saya dapat membantu Anda menemukan sumber daya yang dapat membantu.  Apakah Anda ingin saya membantu Anda menemukan informasi tentang gejala yang ditunjukkan kambing Anda, atau mungkin mencari dokter hewan di daerah Anda?  Atau, apakah Anda ingin saya jelaskan fitur Pemeriksa Ternak di ARK Sentient yang mungkin bisa membantu Anda menganalisis kondisi kambing Anda berdasarkan gejala-gejalanya?\n', '2025-08-09 14:27:30'),
(53, 22, 'user', 'Halo', '2025-09-01 08:06:35'),
(54, 22, 'ai', 'Hai! Ada yang bisa saya bantu mengenai ternak Anda atau fitur ARK Sentient, seperti Marketplace Ternak, Pemeriksa Ternak, atau Sejarah?\n', '2025-09-01 08:06:36'),
(55, 23, 'user', 'Halo', '2025-09-01 08:07:12'),
(56, 23, 'ai', 'Hai! Ada yang bisa saya bantu mengenai ternak atau fitur ARK Sentient seperti Marketplace Ternak, Pemeriksa Ternak, atau Sejarah?\n', '2025-09-01 08:07:13');

-- --------------------------------------------------------

--
-- Struktur dari tabel `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `total_amount` decimal(15,2) NOT NULL,
  `shipping_cost` decimal(15,2) DEFAULT 0.00,
  `tax_amount` decimal(15,2) DEFAULT 0.00,
  `final_amount` decimal(15,2) NOT NULL,
  `payment_method` enum('qris','bank_transfer','cash') DEFAULT 'bank_transfer',
  `payment_status` enum('pending','paid','failed','refunded') DEFAULT 'pending',
  `order_status` enum('pending','confirmed','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `shipping_address` text NOT NULL,
  `notes` text DEFAULT NULL,
  `snap_token` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `order_number`, `total_amount`, `shipping_cost`, `tax_amount`, `final_amount`, `payment_method`, `payment_status`, `order_status`, `shipping_address`, `notes`, `snap_token`, `created_at`, `updated_at`) VALUES
(1, 7, 'ORD-20250809023032-3883', 1500000.00, 200000.00, 150000.00, 1850000.00, 'qris', 'pending', '', '', '', NULL, '2025-08-09 00:30:32', '2025-08-09 00:30:33'),
(2, 7, 'ORD-20250809023430-4190', 1500000.00, 200000.00, 150000.00, 1850000.00, 'qris', 'pending', 'pending', '', '', 'c0516c00-861f-4a05-8d2d-24bd5125f3cb', '2025-08-09 00:34:30', '2025-08-09 00:34:31'),
(3, 7, 'ORD-20250809023702-2009', 30000.00, 200000.00, 3000.00, 233000.00, 'qris', 'pending', 'pending', '', '', '951507f1-a61d-47c1-b612-7bf4626419b3', '2025-08-09 00:37:02', '2025-08-09 00:37:03'),
(4, 7, 'ORD-20250809023915-6078', 30000.00, 200000.00, 3000.00, 233000.00, 'qris', 'pending', '', '', '', NULL, '2025-08-09 00:39:15', '2025-08-09 00:39:16'),
(5, 7, 'ORD-20250809024108-0810', 30000.00, 200000.00, 3000.00, 233000.00, 'qris', 'pending', 'pending', '', '', '7d992c29-6895-4f8d-ad11-b8b60336b6d8', '2025-08-09 00:41:08', '2025-08-09 00:41:08'),
(6, 7, 'ORD-20250809024158-1575', 0.00, 200000.00, 0.00, 200000.00, 'qris', 'pending', 'pending', '', '', NULL, '2025-08-09 00:41:58', '2025-08-09 00:41:58'),
(7, 7, 'ORD-20250809024328-0740', 20000.00, 200000.00, 2000.00, 222000.00, 'qris', 'pending', 'pending', '', '', '42d5de18-2c09-43e0-8ffb-f84c13b894eb', '2025-08-09 00:43:28', '2025-08-09 00:43:28'),
(8, 7, 'ORD-20250809040726-9346', 3500000.00, 200000.00, 350000.00, 4050000.00, 'qris', 'pending', 'pending', '', '', '4ee3d62e-46bd-4f05-a340-e6b42b07642e', '2025-08-09 02:07:26', '2025-08-09 02:07:28'),
(9, 7, 'ORD-20250809041053-0248', 20000.00, 200000.00, 2000.00, 222000.00, 'qris', 'pending', 'pending', '', '', '1e027c65-6c14-4223-9717-8a3597c06afc', '2025-08-09 02:10:53', '2025-08-09 02:10:54'),
(10, 7, 'ORD-20250809045319-4802', 3500000.00, 200000.00, 350000.00, 4050000.00, 'qris', 'pending', 'pending', '', '', '17c01132-ffaf-4237-8c90-6c1ab7dd94ef', '2025-08-09 02:53:19', '2025-08-09 02:53:20'),
(11, 7, 'ORD-20250809045640-9400', 20000.00, 200000.00, 2000.00, 222000.00, 'qris', 'pending', 'pending', '', '', '471a7ecf-74a8-44f2-9db2-8c0137b288c5', '2025-08-09 02:56:40', '2025-08-09 02:56:41'),
(12, 7, 'ORD-20250809045802-6701', 3500000.00, 200000.00, 350000.00, 4050000.00, 'qris', 'pending', 'pending', '', '', '97c49569-557a-4daf-9ef5-62209bb5de52', '2025-08-09 02:58:02', '2025-08-09 02:58:03'),
(13, 7, 'ORD-20250809045903-5834', 20000.00, 200000.00, 2000.00, 222000.00, 'qris', 'pending', 'pending', '', '', '70c8c32f-efa5-4a62-a11d-444e8198f612', '2025-08-09 02:59:03', '2025-08-09 02:59:04'),
(14, 7, 'ORD-20250809045952-7803', 20000.00, 200000.00, 2000.00, 222000.00, 'qris', 'pending', 'pending', '', '', '95b65c05-d964-4648-9677-9e8ec5bfeb9a', '2025-08-09 02:59:52', '2025-08-09 02:59:53'),
(15, 1, 'ORD-20250809091542-3535', 3500000.00, 200000.00, 350000.00, 4050000.00, 'qris', 'pending', 'pending', '', '', 'd5267a7d-89d6-4305-b4a1-0b03c9050087', '2025-08-09 07:15:42', '2025-08-09 07:15:44'),
(16, 1, 'ORD-20250901101638-9504', 3500000.00, 200000.00, 350000.00, 4050000.00, 'qris', 'pending', 'pending', '', '', '67a30fb4-fe3f-4c51-9049-c7fa22bb308c', '2025-09-01 08:16:38', '2025-09-01 08:16:38');

-- --------------------------------------------------------

--
-- Struktur dari tabel `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `livestock_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(15,2) NOT NULL,
  `total_price` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `livestock_id`, `quantity`, `unit_price`, `total_price`) VALUES
(1, 1, 18, 1, 1500000.00, 1500000.00),
(2, 2, 18, 1, 1500000.00, 1500000.00),
(3, 3, 27, 1, 30000.00, 30000.00),
(4, 4, 27, 1, 30000.00, 30000.00),
(5, 5, 27, 1, 30000.00, 30000.00),
(6, 6, 27, 1, 0.00, 0.00),
(7, 7, 27, 1, 20000.00, 20000.00),
(8, 8, 17, 1, 3500000.00, 3500000.00),
(9, 9, 27, 1, 20000.00, 20000.00),
(10, 10, 17, 1, 3500000.00, 3500000.00),
(11, 11, 27, 1, 20000.00, 20000.00),
(12, 12, 17, 1, 3500000.00, 3500000.00),
(13, 13, 27, 1, 20000.00, 20000.00),
(14, 14, 27, 1, 20000.00, 20000.00),
(15, 15, 17, 1, 3500000.00, 3500000.00),
(16, 16, 17, 1, 3500000.00, 3500000.00);

-- --------------------------------------------------------

--
-- Struktur dari tabel `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `payment_status` enum('pending','completed','failed','cancelled') DEFAULT 'pending',
  `payment_date` timestamp NULL DEFAULT NULL,
  `snap_token` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `payment_method`, `amount`, `transaction_id`, `payment_status`, `payment_date`, `snap_token`, `created_at`) VALUES
(1, 1, 'qris', 1850000.00, NULL, 'pending', NULL, NULL, '2025-08-09 00:30:32'),
(2, 2, 'qris', 1850000.00, NULL, 'pending', NULL, NULL, '2025-08-09 00:34:30'),
(3, 3, 'qris', 233000.00, NULL, 'pending', NULL, '951507f1-a61d-47c1-b612-7bf4626419b3', '2025-08-09 00:37:02'),
(4, 4, 'qris', 233000.00, NULL, 'pending', NULL, NULL, '2025-08-09 00:39:15'),
(5, 5, 'qris', 233000.00, NULL, 'pending', NULL, '7d992c29-6895-4f8d-ad11-b8b60336b6d8', '2025-08-09 00:41:08'),
(6, 6, 'qris', 200000.00, NULL, 'pending', NULL, NULL, '2025-08-09 00:41:58'),
(7, 7, 'qris', 222000.00, NULL, 'pending', NULL, '42d5de18-2c09-43e0-8ffb-f84c13b894eb', '2025-08-09 00:43:28'),
(8, 8, 'qris', 4050000.00, NULL, 'pending', NULL, '4ee3d62e-46bd-4f05-a340-e6b42b07642e', '2025-08-09 02:07:26'),
(9, 9, 'qris', 222000.00, NULL, 'pending', NULL, '1e027c65-6c14-4223-9717-8a3597c06afc', '2025-08-09 02:10:53'),
(10, 10, 'qris', 4050000.00, NULL, 'pending', NULL, '17c01132-ffaf-4237-8c90-6c1ab7dd94ef', '2025-08-09 02:53:19'),
(11, 11, 'qris', 222000.00, NULL, 'pending', NULL, '471a7ecf-74a8-44f2-9db2-8c0137b288c5', '2025-08-09 02:56:40'),
(12, 12, 'qris', 4050000.00, NULL, 'pending', NULL, '97c49569-557a-4daf-9ef5-62209bb5de52', '2025-08-09 02:58:02'),
(13, 13, 'qris', 222000.00, NULL, 'pending', NULL, '70c8c32f-efa5-4a62-a11d-444e8198f612', '2025-08-09 02:59:03'),
(14, 14, 'qris', 222000.00, NULL, 'pending', NULL, '95b65c05-d964-4648-9677-9e8ec5bfeb9a', '2025-08-09 02:59:52'),
(15, 15, 'qris', 4050000.00, NULL, 'pending', NULL, 'd5267a7d-89d6-4305-b4a1-0b03c9050087', '2025-08-09 07:15:42'),
(16, 16, 'qris', 4050000.00, NULL, 'pending', NULL, '67a30fb4-fe3f-4c51-9049-c7fa22bb308c', '2025-09-01 08:16:38');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role` enum('customer','farmer','admin') DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `phone`, `address`, `role`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@arksentient.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', '081234567890', 'Jakarta', 'admin', '2025-07-21 06:19:38', '2025-07-21 06:19:38'),
(2, 'farmer1', 'farmer@arksentient.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Farmer', '081234567891', 'Bogor, Jawa Barat', 'farmer', '2025-07-21 06:19:38', '2025-07-21 06:19:38'),
(3, 'customer1', 'customer@arksentient.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jane Customer', '081234567892', 'Jakarta Selatan', 'customer', '2025-07-21 06:19:38', '2025-07-21 06:19:38'),
(6, 'Flyaway', 'mraflyyydwiii@gmail.com', '$2y$12$ul7milCuR2XbGfuAHoYh0etC2O.vw0zA0Opm5yIsWmKzWbXJVVKRW', 'PlyPlaPlo', '0852-8952-1862', 'DD House, California', 'farmer', '2025-07-23 10:08:45', '2025-07-23 10:08:45'),
(7, 'Rakha', 'rakha@gmail.com', '$2y$10$11w9lwb8YnRtQVtrJ2rw4u6LSIWORdtf9bxBBpE4jlgZa/2mWku6u', 'M Rakha Syamputra', '0878-7131-0560', 'Jakarta', 'farmer', '2025-07-29 11:46:20', '2025-07-29 11:46:20');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `livestock_id` (`livestock_id`);

--
-- Indeks untuk tabel `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `livestock`
--
ALTER TABLE `livestock`
  ADD PRIMARY KEY (`id`),
  ADD KEY `farmer_id` (`farmer_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indeks untuk tabel `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `conversation_id` (`conversation_id`);

--
-- Indeks untuk tabel `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `livestock_id` (`livestock_id`);

--
-- Indeks untuk tabel `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT untuk tabel `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `conversations`
--
ALTER TABLE `conversations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT untuk tabel `livestock`
--
ALTER TABLE `livestock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT untuk tabel `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT untuk tabel `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT untuk tabel `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT untuk tabel `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`livestock_id`) REFERENCES `livestock` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `livestock`
--
ALTER TABLE `livestock`
  ADD CONSTRAINT `livestock_ibfk_1` FOREIGN KEY (`farmer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `livestock_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`livestock_id`) REFERENCES `livestock` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
