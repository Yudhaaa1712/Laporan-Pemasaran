-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 15, 2025 at 07:39 AM
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
-- Database: `freedom_cofe`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Minuman Kopi', 'Kalau ngantuk ya minum kopi', '2025-05-14 12:06:32', '2025-05-14 12:07:45'),
(2, 'Minuman Non-Kopi', 'Red velvet enak loh', '2025-05-14 12:06:32', '2025-05-14 12:08:01'),
(3, 'Makanan Pendamping', 'Makanan yang enak di gabung dengan makanan berat', '2025-05-14 12:06:32', '2025-05-14 12:07:10'),
(4, 'Merchandise', 'yang suka sama freedom coffe yok beli merch nya', '2025-05-14 12:06:32', '2025-05-14 12:08:58'),
(5, 'Makanan', 'Makana enak sekali saat lapar', '2025-05-14 12:06:45', '2025-05-14 12:06:45');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `category_id`, `price`, `stock`, `description`, `image`, `created_at`) VALUES
(1, 'Espresso', 1, 20000.00, 50, 'Kopi espresso dengan rasa yang kuat dan pekat', NULL, '2025-05-07 08:10:22'),
(2, 'Cappuccino', 1, 25000.00, 40, 'Cappuccino dengan busa susu yang lembut dan creamy', NULL, '2025-05-07 08:10:22'),
(3, 'Latte', 1, 22000.00, 60, 'Kopi latte dengan perpaduan espresso dan susu panas', NULL, '2025-05-07 08:10:22'),
(4, 'Americano', 1, 18000.00, 69, 'Kopi americano yang pekat dan menyegarkan', NULL, '2025-05-07 08:10:22'),
(5, 'Mocha', 1, 28000.00, 16, 'Mocha yang manis dengan campuran coklat dan kopi', NULL, '2025-05-07 08:10:22'),
(6, 'Teh Tarik', 2, 15000.00, 50, 'Teh tarik dengan rasa manis dan aroma khas', NULL, '2025-05-07 08:10:22'),
(7, 'Jus Jeruk Segar', 2, 18000.00, 80, 'Jus jeruk segar yang menyegarkan di siang hari', NULL, '2025-05-07 08:10:22'),
(8, 'Brownies', 3, 15000.00, 100, 'Brownies coklat lezat untuk pendamping kopi', NULL, '2025-05-07 08:10:22'),
(9, 'Croissant', 3, 20000.00, 120, 'Croissant lembut dengan isi coklat di dalamnya', NULL, '2025-05-07 08:10:22'),
(10, 'Sandwich Keju', 3, 25000.00, 16, 'Sandwich keju dengan roti yang empuk dan lezat', NULL, '2025-05-07 08:10:22'),
(11, 'Mug Kopi', 4, 30000.00, 150, 'Mug kopi berkualitas tinggi untuk menikmati kopi di rumah', NULL, '2025-05-07 08:10:22'),
(12, 'Teko Teh', 4, 35000.00, 100, 'Teko teh untuk menyeduh teh dengan cita rasa terbaik', NULL, '2025-05-07 08:10:22'),
(13, 'Teko Kopi', 4, 40000.00, 80, 'Teko kopi untuk menyeduh kopi dengan cita rasa maksimal', NULL, '2025-05-07 08:10:22'),
(14, 'Nasi goreng', 3, 10000.00, 20, 'Nasi goreng enak sekali ', '', '2025-05-14 11:13:16'),
(15, 'Mie Goreng', 3, 17000.00, 60, 'Mie Goreng Enak nikmat ', '', '2025-05-14 11:58:41'),
(16, 'Tumbler', 4, 100000.00, 9, 'AYOK BELI MERCH NYA ', '', '2025-05-14 12:09:43'),
(17, 'Ayam Geprek', 5, 15000.00, 29, 'ayam geprek enak sekali', '', '2025-05-15 05:27:45');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `sale_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `product_id`, `quantity`, `total_price`, `sale_date`) VALUES
(1, 1, 5, 100000.00, '2025-05-07 08:10:22'),
(2, 2, 3, 75000.00, '2025-05-07 08:10:22'),
(3, 3, 7, 154000.00, '2025-05-07 08:10:22'),
(4, 4, 10, 180000.00, '2025-05-07 08:10:22'),
(5, 5, 2, 56000.00, '2025-05-07 08:10:22'),
(6, 6, 6, 90000.00, '2025-05-07 08:10:22'),
(7, 7, 8, 144000.00, '2025-05-07 08:10:22'),
(8, 8, 4, 60000.00, '2025-05-07 08:10:22'),
(9, 9, 6, 120000.00, '2025-05-07 08:10:22'),
(10, 10, 5, 125000.00, '2025-05-07 08:10:22'),
(11, 11, 3, 90000.00, '2025-05-07 08:10:22'),
(12, 12, 2, 70000.00, '2025-05-07 08:10:22'),
(13, 13, 1, 40000.00, '2025-05-07 08:10:22'),
(14, 4, 1, 18000.00, '2025-05-10 17:00:00'),
(15, 14, 30, 300000.00, '2025-05-13 17:00:00'),
(16, 5, 14, 392000.00, '2025-05-13 17:00:00'),
(17, 10, 4, 100000.00, '2025-05-13 17:00:00'),
(18, 16, 2, 200000.00, '2025-05-13 17:00:00'),
(19, 16, 4, 400000.00, '2025-05-14 17:00:00'),
(20, 10, 70, 1750000.00, '2025-05-14 17:00:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
