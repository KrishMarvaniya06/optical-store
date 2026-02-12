-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 04, 2026 at 06:10 PM
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
-- Database: `optical_store`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password_hash`, `created_at`) VALUES
(1, 'admin', 'admin123', '2025-12-02 09:04:57');

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `main_category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `name`, `main_category_id`) VALUES
(16, 'GUCCI', 7),
(19, 'PRADA', NULL),
(20, 'DIOR', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `carts`
--

INSERT INTO `carts` (`id`, `user_id`, `created_at`) VALUES
(5, 1, '2025-12-04 14:06:35');

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `cart_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `user_name`, `user_email`, `subject`, `message`, `created_at`) VALUES
(2, 'Naman', 'namangovani01@gmail.com', 'abcd', 'demo1', '2025-12-12 12:49:06'),
(3, 'Naman', 'namangovani01@gmail.com', 'abcd', 'your website deserves full marks and you are such a very good student', '2025-12-12 14:41:52'),
(4, 'dhanashree', 'asdfg@gmaol.com', 'swsdfg', 'wergh', '2025-12-15 04:34:53'),
(5, 'dhanashree', 'asdfg@gmaol.com', 'swsdfg', 'wergh', '2025-12-15 04:36:33');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `review` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `user_id`, `order_id`, `product_id`, `rating`, `review`, `created_at`) VALUES
(22, 4, 113, 119, 5, 'good', '2026-01-30 17:17:40'),
(23, 4, 113, 136, 5, 'good', '2026-01-30 17:17:40'),
(24, 4, 113, 137, 5, 'good', '2026-01-30 17:17:40');

-- --------------------------------------------------------

--
-- Table structure for table `genders`
--

CREATE TABLE `genders` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `main_category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `genders`
--

INSERT INTO `genders` (`id`, `name`, `main_category_id`) VALUES
(19, 'Male', 7),
(20, 'Female', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE `images` (
  `path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `main_categories`
--

CREATE TABLE `main_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `main_categories`
--

INSERT INTO `main_categories` (`id`, `name`, `slug`) VALUES
(7, 'Sunglasses', 'sunglasses'),
(11, 'Rimless', 'rimless'),
(12, 'Half Frame', 'half-frame'),
(13, 'Full Frame', 'full-frame');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `shipping_address` text NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp(),
  `payment_method` varchar(50) DEFAULT NULL,
  `delivered_otp` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `name`, `email`, `shipping_address`, `total_amount`, `status`, `created_at`, `payment_method`, `delivered_otp`) VALUES
(114, 4, 'Krish Marvaniya', 'krishmarvaniya531@gmail.com', 'RAJKOT, Rajkot - 360005', 21040.00, 'cancelled', '2026-02-03 12:58:19', 'COD', NULL),
(115, 4, 'krish Marvaniya', 'krishmarvaniya51@gmail.com', 'RAJKOT, Rajkot - 360005', 8440.00, 'cancelled', '2026-02-03 19:34:32', 'COD', NULL),
(116, 4, 'krish Marvaniya', 'krishmarvaniya51@gmail.com', 'RAJKOT, Rajkot - 360005', 3190.00, 'pending', '2026-02-03 19:40:34', 'COD', NULL),
(117, 4, 'krish Marvaniya', 'krishmarvaniya51@gmail.com', 'Ground Floor, 150 Feet Ring Road, Chandra Park, Rajkot,, Rajkot - 360005', 6340.00, 'shipped', '2026-02-03 20:04:05', 'COD', NULL),
(118, 5, 'dhairya Marvaniya', 'dhairyasanghani110@gmail.com', 'Ground Floor, 150 Feet Ring Road, Chandra Park, Rajkot,, Rajkot - 360005', 21040.00, 'shipped', '2026-02-04 14:41:20', 'COD', NULL),
(119, 10, 'marvaniya krish', 'krishmarvaniya08@gmail.com', 'Ground Floor, 150 Feet Ring Road, Chandra Park, Rajkot,, Rajkot - 360005', 3190.00, 'shipped', '2026-02-04 14:44:20', 'COD', NULL),
(120, 4, 'krish Marvaniya', 'krishmarvaniya51@gmail.com', 'Ground Floor, 150 Feet Ring Road, Chandra Park, Rajkot,, Rajkot - 360005', 3190.00, 'pending', '2026-02-04 22:33:57', 'COD', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(141, 114, 119, 1, 20000.00),
(142, 115, 137, 2, 4000.00),
(143, 116, 136, 1, 3000.00),
(144, 117, 107, 1, 6000.00),
(145, 118, 119, 1, 20000.00),
(146, 119, 136, 1, 3000.00),
(147, 120, 136, 1, 3000.00);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `payment_method` enum('COD','CARD','UPI','NET_BANKING') DEFAULT 'COD',
  `amount` decimal(12,2) NOT NULL,
  `payment_status` enum('pending','success','failed') DEFAULT 'pending',
  `transaction_id` varchar(255) DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `upi_id` varchar(100) DEFAULT NULL,
  `card_number` varchar(20) DEFAULT NULL,
  `card_expiry` varchar(10) DEFAULT NULL,
  `card_cvv` varchar(10) DEFAULT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `bank_account` varchar(50) DEFAULT NULL,
  `ifsc_code` varchar(20) DEFAULT NULL,
  `payment_info` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `payment_method`, `amount`, `payment_status`, `transaction_id`, `paid_at`, `upi_id`, `card_number`, `card_expiry`, `card_cvv`, `bank_name`, `bank_account`, `ifsc_code`, `payment_info`) VALUES
(1, 50, 'UPI', 16392.00, 'success', 'TXN693570B3E44C0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'UPI ID: 9104362458@fam'),
(2, 51, 'UPI', 9995.00, 'success', 'TXN69357173A0571', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'UPI ID: 9104362458@fam'),
(3, 52, 'UPI', 4598.00, 'success', 'TXN6936530C9254B', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'UPI ID: 9104362458@fam'),
(4, 53, 'UPI', 1999.00, 'success', 'TXN693655F560E58', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'UPI ID: 9104362458@fam'),
(5, 54, 'COD', 1799.00, 'pending', 'TXN69365C8180C43', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(6, 55, 'UPI', 1799.00, 'success', 'TXN69369102E9AA4', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'UPI ID: 9104362458@fam'),
(7, 56, 'UPI', 2299.00, 'success', 'TXN6936911E63EDE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'UPI ID: 9104362458@fam'),
(8, 57, 'UPI', 2099.00, 'success', 'TXN693692DE14B59', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'UPI ID: 9104362458@fam'),
(9, 58, 'COD', 2299.00, 'pending', 'TXN69369307402CE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(10, 59, 'UPI', 6897.00, 'success', 'TXN693693BBC36C2', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'UPI ID: 9104362458@fam'),
(11, 60, 'COD', 2299.00, 'pending', 'TXN6936970E3DF0B', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(12, 61, 'UPI', 2099.00, 'success', 'TXN6936D7297DDAD', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'UPI ID: 9104362458@fam'),
(13, 62, 'UPI', 2499.00, 'success', 'TXN6936D8DB92755', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'UPI ID: 9104362458@fam'),
(14, 63, 'UPI', 2499.00, 'success', 'TXN6936DB3F996DC', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'UPI ID: 9104362458@fam'),
(15, 64, 'UPI', 2663.95, 'success', 'TXN6936E0C6B54B7', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'UPI: 9104362458@fam'),
(16, 65, 'COD', 5287.90, 'pending', 'TXN6936E5F7BC464', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(17, 66, 'COD', 40.00, 'pending', 'TXN6936E602AA904', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(18, 67, 'COD', 26279.50, 'pending', 'TXN6936E61B749A2', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(19, 68, 'COD', 18407.65, 'pending', 'TXN6936E763E4FD9', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(20, 69, 'COD', 26279.50, 'pending', 'TXN6936E790009C6', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(21, 70, 'UPI', 5287.90, 'success', 'TXN6936F71151C42', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'UPI: '),
(22, 71, 'COD', 2663.95, 'pending', 'TXN6936F7817F8EB', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(23, 72, 'COD', 1090.00, 'pending', 'TXN693CE22A5CB12', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(24, 73, 'COD', 1090.00, 'pending', 'TXN693CE257E9C46', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(25, 74, 'COD', 1090.00, 'pending', 'TXN693CF541AA8D0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(26, 75, 'COD', 1090.00, 'pending', 'TXN693CFB863447E', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(27, 76, 'CARD', 1090.00, 'success', 'TXN693D05F65B21B', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'CARD ****'),
(28, 77, 'COD', 1090.00, 'pending', 'TXN693D113FC7708', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(29, 78, 'COD', 40.00, 'pending', 'TXN693D11C86FEA3', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(30, 79, 'COD', 40.00, 'pending', 'TXN693D11DF20939', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(31, 80, 'COD', 1090.00, 'pending', 'TXN693D13F2D364E', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(32, 81, 'COD', 1090.00, 'pending', 'TXN693D15281156F', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(33, 82, 'COD', 1090.00, 'pending', 'TXN693D29A9E32F2', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(34, 83, 'COD', 1090.00, 'pending', 'TXN693D2A2E08DBF', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(35, 84, 'COD', 40.00, 'pending', 'TXN693D2CA5CE3BB', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(36, 85, 'COD', 1090.00, 'pending', 'TXN693D2CE7964D4', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(37, 86, 'COD', 1090.00, 'pending', 'TXN693D2D7DD7771', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(38, 87, 'COD', 40.00, 'pending', 'TXN693D2E541924F', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(39, 88, 'COD', 1090.00, 'pending', 'TXN693D2E7D0C97B', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(40, 89, 'COD', 1090.00, 'pending', 'TXN693D2ED4584D9', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(41, 90, 'COD', 1090.00, 'pending', 'TXN693D2FE48C136', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(42, 91, 'COD', 1090.00, 'pending', 'TXN693D3016BE0B6', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(43, 92, 'COD', 1090.00, 'pending', 'TXN693D31565C15F', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(44, 93, 'COD', 525040.00, 'pending', 'TXN693F8D697DB38', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(45, 94, 'CARD', 52500040.00, 'success', 'TXN693F938AE04CC', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'CARD ****0000'),
(46, 95, 'COD', 10500040.00, 'pending', 'TXN695DE47C7842A', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(47, 96, 'COD', 50440.00, 'pending', 'TXN695DF108047B3', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(48, 97, 'COD', 3150040.00, 'pending', 'TXN695DF266D1F40', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(49, 98, 'COD', 3190.00, 'pending', 'TXN695E575632729', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(50, 99, 'COD', 21040.00, 'pending', 'TXN695E5CE864800', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(51, 100, 'COD', 6300040.00, 'pending', 'TXN695E5D4423819', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(52, 101, 'COD', 3190.00, 'pending', 'TXN6961EC88719E8', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(53, 102, 'UPI', 21040.00, 'success', 'TXN696F194ADD539', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'UPI: hetasvi'),
(54, 103, 'COD', 3190.00, 'pending', 'TXN696F19B9BD4B3', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(55, 104, 'UPI', 21040.00, 'success', 'TXN697848171C9E3', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'UPI: 9104362458@'),
(56, 105, 'UPI', 21040.00, 'success', 'TXN6978495C5C7FB', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'UPI: krishmarvaniya@fam'),
(57, 106, 'UPI', 21040.00, 'success', 'TXN697AF1B52C2C8', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'UPI: dscsdc@upi'),
(58, 107, 'COD', 31290040.00, 'pending', 'TXN697B29F118EB8', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(59, 108, 'COD', 1050040.00, 'pending', 'TXN697B2A6F40A0C', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(60, 109, 'COD', 105040.00, 'pending', 'TXN697B2ACD33307', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(61, 110, 'COD', 105040.00, 'pending', 'TXN697B2AFCEF397', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(62, 111, 'COD', 105040.00, 'pending', 'TXN697CE4C294C7D', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(63, 112, 'COD', 105040.00, 'pending', 'TXN697CE5FFC3B5D', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(64, 113, 'COD', 2124190.00, 'pending', 'TXN697CE7937AEC7', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(65, 114, 'COD', 21040.00, 'pending', 'TXN6981A393350D7', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(66, 115, 'COD', 8440.00, 'pending', 'TXN69820070B4AC9', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(67, 116, 'COD', 3190.00, 'pending', 'TXN698201DA64A97', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(68, 117, 'COD', 6340.00, 'pending', 'TXN6982075D271BA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(69, 118, 'COD', 21040.00, 'pending', 'TXN69830D3885541', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(70, 119, 'COD', 3190.00, 'pending', 'TXN69830DEC0F0A1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(71, 120, 'COD', 3190.00, 'pending', 'TXN69837BFD9AC5C', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '');

-- --------------------------------------------------------

--
-- Table structure for table `price_categories`
--

CREATE TABLE `price_categories` (
  `id` int(11) NOT NULL,
  `label` varchar(150) NOT NULL,
  `min_price` decimal(12,2) DEFAULT NULL,
  `max_price` decimal(12,2) DEFAULT NULL,
  `main_category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `price_categories`
--

INSERT INTO `price_categories` (`id`, `label`, `min_price`, `max_price`, `main_category_id`) VALUES
(11, 'Luxury', 1000.00, 25000.00, 7),
(12, 'Ultra Luxury', 26000.00, 50000.00, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `main_category_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `gender_id` int(11) DEFAULT NULL,
  `price_category_id` int(11) DEFAULT NULL,
  `price` decimal(12,2) NOT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `main_category_id`, `name`, `brand_id`, `gender_id`, `price_category_id`, `price`, `description`, `image_path`, `created_at`) VALUES
(105, 7, 'DiorSignature S7F', 20, 20, 11, 18000.00, '0', 'assets/images/products/product_1765817679_8284.jpg', '2025-12-15 16:54:39'),
(106, 7, '30 Montaigne B5U', 20, 20, 12, 35000.00, '0', 'assets/images/products/product_1765817854_2350.jpg', '2025-12-15 16:57:34'),
(107, 7, 'Dior Cannage S1U', 20, 20, 11, 6000.00, '0', 'assets/images/products/product_1765818000_5933.jpg', '2025-12-15 17:00:00'),
(108, 7, 'Dior Signature  S10F', 20, 20, 11, 8000.00, '0', 'assets/images/products/product_1765818121_7033.jpg', '2025-12-15 17:02:01'),
(109, 7, 'Dior Signature B1U', 20, 20, 11, 7000.00, '0', 'assets/images/products/product_1765818225_3701.webp', '2025-12-15 17:03:45'),
(110, 7, 'Dior BlackSuit R1', 20, 19, 11, 8000.00, '0', 'assets/images/products/product_1765818347_5820.jpg', '2025-12-15 17:05:47'),
(111, 7, 'NeoDior A1U', 20, 19, 12, 26500.00, '0', 'assets/images/products/product_1765818458_5308.jpg', '2025-12-15 17:07:38'),
(112, 7, 'Dior oblique S4F', 20, 19, 11, 9000.00, '0', 'assets/images/products/product_1765818600_6787.jpg', '2025-12-15 17:10:00'),
(114, 7, 'DiorTailoring S1F', 20, 19, 12, 31000.00, '0', 'assets/images/products/product_1765818920_2551.webp', '2025-12-15 17:15:20'),
(119, 13, 'Gucci GG12850', 16, 20, 11, 20000.00, 'Color:  silver Metallic transparent', 'assets/images/products/product_1765819616_1354.webp', '2025-12-15 17:26:56'),
(121, 7, 'Prada shaded Sunglasses', 19, 20, 12, 30000.00, '0', 'assets/images/products/product_1765820000_4170.jpg', '2025-12-15 17:33:20'),
(124, 7, 'Prada Symbol Sunglasses', 19, 20, 11, 9000.00, '0', 'assets/images/products/product_1765820223_6918.jpg', '2025-12-15 17:37:03'),
(125, 7, 'prada sunglasses with the iconic metal plaque', 19, 20, 12, 46000.00, '0', 'assets/images/products/product_1765820314_2229.jpg', '2025-12-15 17:38:34'),
(126, 7, 'prada runway sunglasess', 19, 20, 12, 45000.00, '0', 'assets/images/products/product_1765820421_9292.jpg', '2025-12-15 17:40:21'),
(127, 7, 'sunglasses with the iconic metal plaque', 19, 20, 11, 8000.00, '0', 'assets/images/products/product_1765820523_7293.jpg', '2025-12-15 17:42:03'),
(128, 7, 'sunglasses with the iconic metal plaque', 19, 19, 12, 44000.00, '0', 'assets/images/products/product_1765820605_3586.jpg', '2025-12-15 17:43:25'),
(129, 7, 'Sunglasses with prada logo', 19, 19, 11, 3000.00, '0', 'assets/images/products/product_1765820691_8996.jpg', '2025-12-15 17:44:51'),
(130, 7, 'Sunglasses with iconic metal plaque', 19, 19, 12, 43000.00, '0', 'assets/images/products/product_1765820763_7896.jpg', '2025-12-15 17:46:03'),
(131, 7, 'sunglasses with prada logo', 19, 19, 12, 42000.00, '0', 'assets/images/products/product_1765820842_6804.jpg', '2025-12-15 17:47:22'),
(132, 12, 'Guuci Half rim frame for women', 16, 20, 12, 41000.00, '0', 'assets/images/products/product_1765868837_3450.jpg', '2025-12-16 07:07:17'),
(133, 7, 'Guuci sunglasses withgucci logo', 16, 20, 12, 40000.00, '0', 'assets/images/products/product_1765868988_4827.jpg', '2025-12-16 07:09:48'),
(134, 7, 'Gucci antique', 16, 20, 11, 6000.00, '0', 'assets/images/products/product_1765869119_5203.jpg', '2025-12-16 07:11:59'),
(135, 7, 'Guuci tricolor frame', 16, 20, 11, 7500.00, '0', 'assets/images/products/product_1765869299_1944.jpg', '2025-12-16 07:14:59'),
(136, 13, 'Gucci Rectangular eyeglasses GG0278o 016', 16, 20, 11, 3000.00, 'Modern transparent eyeglasses with a clean rectangular frame and subtle signature detailing on the temples, offering a refined, contemporary look with everyday comfort.', 'assets/images/products/product_1765869403_6722.jpg', '2025-12-16 07:16:43'),
(137, 13, 'Elegant NEw Gucci GG1172o-004  Eyeglasses', 16, 20, 11, 4000.00, '0', 'assets/images/products/product_1765869533_8814.jpg', '2025-12-16 07:18:53'),
(138, 11, 'Gucci eyewear Rectangular-frame rimless Gold tone for men', 16, 19, 12, 39000.00, '0', 'uploads/products/694109efc8052.jpg', '2025-12-16 07:27:17'),
(139, 13, 'Gucci Square eyeglasses', 16, 19, 11, 5000.00, '0', 'assets/images/products/product_1766120352_2040.jpg', '2025-12-19 04:59:12'),
(140, 13, 'Gucci Iconic Eyewear', 16, 19, 12, 39000.00, '0', 'assets/images/products/product_1766120591_6587.jpg', '2025-12-19 05:03:11'),
(142, 13, 'Dior mens eyewear glasses', 20, 19, 12, 38000.00, '0', 'assets/images/products/product_1766120860_8625.jpg', '2025-12-19 05:07:40'),
(143, 11, 'rimless dior', 20, 19, 11, 6000.00, '0', 'assets/images/products/product_1766121061_1180.jpg', '2025-12-19 05:11:01'),
(144, 12, 'Half rime prada', 19, 19, 11, 7000.00, '0', 'assets/images/products/product_1766121223_1649.jpg', '2025-12-19 05:13:43'),
(145, 12, 'half rime for mens', 19, 20, 11, 8000.00, '0', 'assets/images/products/prod_695e5d058f4da.jpg', '2025-12-19 05:25:19'),
(146, 11, 'Dior RoseGold rimlessFrame', 20, 20, 11, 9000.00, '0', 'assets/images/products/product_1766122133_6882.jpg', '2025-12-19 05:28:53'),
(147, 11, 'Prada rimless with rounded glasses', 19, 20, 12, 48000.00, '0', 'assets/images/products/product_1766122274_1174.jpg', '2025-12-19 05:31:14'),
(148, 11, 'Dior Rimless', 20, 20, 12, 47000.00, 'Color: Gold frame', 'assets/images/products/product_1766122969_5074.jpg', '2025-12-19 05:42:49'),
(149, 11, 'Gucci rimless', 16, 20, 11, 20000.00, 'Color: rosegold', 'assets/images/products/product_1766123228_9664.jpg', '2025-12-19 05:47:08'),
(151, 12, 'Brown Metalic cateye  half frame', 20, 20, 11, 10000.00, 'Color: Brown metallic', 'assets/images/products/product_1766123483_7791.jpg', '2025-12-19 05:51:23'),
(152, 11, 'rimless dior images', 20, 20, 12, 28500.00, '0', 'assets/images/products/product_1766416489_5058.jpg', '2025-12-22 15:14:49'),
(153, 12, 'half frame male dior', 20, 19, 11, 13000.00, '0', 'assets/images/products/product_1766416574_7428.jpg', '2025-12-22 15:16:14'),
(154, 12, 'half frame male gucci', 16, 19, 11, 12000.00, '0', 'assets/images/products/product_1766416654_5592.jpg', '2025-12-22 15:17:34'),
(157, 12, 'half rime prada male', 19, 19, 11, 5600.00, 'half rime prada', 'assets/images/products/product_1766416929_2782.jpg', '2025-12-22 15:22:09'),
(158, 13, 'full frame dior', 20, 20, 12, 28000.00, '0', 'assets/images/products/product_1766417015_2620.jpg', '2025-12-22 15:23:35'),
(159, 13, 'full frame of prada', 19, 19, 11, 11000.00, '0', 'assets/images/products/product_1766417107_4614.jpg', '2025-12-22 15:25:07'),
(160, 13, 'full frame prada', 19, 20, 11, 10000.00, '0', 'assets/images/products/product_1766417180_3704.jpg', '2025-12-22 15:26:20'),
(161, 11, 'rimless prada', 19, 19, 12, 27000.00, '0', 'assets/images/products/product_1766417243_8149.jpg', '2025-12-22 15:27:23'),
(162, 7, 'sunglasses male gucci', 16, 19, 12, 26500.00, '0', 'assets/images/products/product_1766417327_6686.jpg', '2025-12-22 15:28:47');

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'active',
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `email`, `password`, `created_at`, `reset_token`, `reset_expires`, `first_name`, `last_name`, `status`, `phone`, `address`) VALUES
(1, 'naman', 'namangovani01@gmail.com', '$2y$10$1zaboiNUI92e8kst1GxIvO5VsiIoHnCou/yF6E0oMKrRF04q5pz2W', '2025-12-12 17:56:08', '5d62c6a0c1d7f81bd79a1b2e7fb858da77aff8c3c6ee1fe8e2fc769f5ecd6c3a', '2025-12-12 19:56:51', NULL, NULL, 'active', NULL, NULL),
(3, 'dhanashree', 'd@gmail.com', '$2y$10$wehgxifCiYYr/KoOyvD22OpdALzupDqCvsUDr2PFxJIi0hWvxcOxa', '2025-12-15 04:39:17', NULL, NULL, NULL, NULL, 'active', NULL, NULL),
(4, 'krish', 'krishmarvaniya51@gmail.com', '$2y$10$3dX7SLjeDqEfPmk2ecKCb..NEphjt/ucSiU5NYfHzUSAGX6/5AECa', '2026-01-07 04:43:01', NULL, NULL, NULL, NULL, 'active', NULL, NULL),
(5, 'dhairya', 'dhairyasanghani110@gmail.com', '$2y$10$2J0BnsdFJwADBGoV3Q9f2uQ4kCkURbMYP0Lu4BzC2XzsAagqC0die', '2026-02-02 04:38:24', NULL, NULL, NULL, NULL, 'active', NULL, NULL),
(9, 'admin', 'admin@gamil.com', '$2y$10$QbBX4i39jHRjTMqcGPhP.ewPEkzZa5eH0eKi9uNn48SAl1QxX.8SK', '2026-02-03 12:43:51', NULL, NULL, NULL, NULL, 'active', NULL, NULL),
(10, 'marvaniya', 'krishmarvaniya08@gmail.com', '$2y$10$bAT0ZFdBwlpIvWyattohB.gpmKthbJdCskpjr7F0GAz5tBfH1SWpW', '2026-02-04 09:13:07', NULL, NULL, NULL, NULL, 'active', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_brands_main` (`main_category_id`);

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `genders`
--
ALTER TABLE `genders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_genders_main` (`main_category_id`);

--
-- Indexes for table `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`path`);

--
-- Indexes for table `main_categories`
--
ALTER TABLE `main_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `price_categories`
--
ALTER TABLE `price_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_prices_main` (`main_category_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `brand_id` (`brand_id`),
  ADD KEY `gender_id` (`gender_id`),
  ADD KEY `price_category_id` (`price_category_id`),
  ADD KEY `fk_main_category` (`main_category_id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `genders`
--
ALTER TABLE `genders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `main_categories`
--
ALTER TABLE `main_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=148;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `price_categories`
--
ALTER TABLE `price_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=163;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `brands`
--
ALTER TABLE `brands`
  ADD CONSTRAINT `fk_brands_main` FOREIGN KEY (`main_category_id`) REFERENCES `main_categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `feedback_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `genders`
--
ALTER TABLE `genders`
  ADD CONSTRAINT `fk_genders_main` FOREIGN KEY (`main_category_id`) REFERENCES `main_categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `price_categories`
--
ALTER TABLE `price_categories`
  ADD CONSTRAINT `fk_prices_main` FOREIGN KEY (`main_category_id`) REFERENCES `main_categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_main_category` FOREIGN KEY (`main_category_id`) REFERENCES `main_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`gender_id`) REFERENCES `genders` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_ibfk_3` FOREIGN KEY (`price_category_id`) REFERENCES `price_categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_ibfk_4` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`),
  ADD CONSTRAINT `products_ibfk_5` FOREIGN KEY (`gender_id`) REFERENCES `genders` (`id`),
  ADD CONSTRAINT `products_ibfk_6` FOREIGN KEY (`price_category_id`) REFERENCES `price_categories` (`id`);

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
