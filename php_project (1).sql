-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 12, 2025 at 01:21 PM
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
-- Database: `php_project`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `admin_name` varchar(50) NOT NULL,
  `admin_email` varchar(100) NOT NULL,
  `admin_password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `admin_name`, `admin_email`, `admin_password`) VALUES
(1, 'Super Admin', 'admin@example.com', '<hashed_password_here>'),
(2, 'bilal', 'admin@gmail.com', '$2y$10$gvjH5xgGbcNLbfzhiQ0VY.A/8AxRJbyLnUuBFwwExph5O832ONJn.');

-- --------------------------------------------------------

--
-- Table structure for table `blogs`
--

CREATE TABLE `blogs` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `shop_link` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blogs`
--

INSERT INTO `blogs` (`id`, `title`, `description`, `image`, `shop_link`) VALUES
(1, 'the jdhasdhsaddks lkdjlask alksalksa  alksnalksnA SASNLAKNS', 'JHSDJKSHDJKSHDJKSHDJKSH DHASDDSADHASDHSKJDHASKJDASJHDASKJ DHASJKHDASJDHSKJD ASHDJASKHDAS JDHPadhskl asdhpADHPSD HASD SAOP[DJSKL D', 'girl1 (11).jpg', 'shop.php?product_id=25'),
(2, 'Elegant Floral Cotton Kurta', 'Perfect for casual outings or festive gatherings.', 'girl1 (11).jpg', 'shop.php?product_id=25'),
(3, 'Designer Silk Kurta for Parties', 'Stand out in elegant silk with modern embroidery.', 'kurta2.jpg', 'shop.php?product_id=2'),
(4, 'Casual Lawn Kurta for Summer', 'Lightweight lawn kurtas for everyday wear.', 'kurta3.jpg', 'shop.php?product_id=3'),
(5, 'Embroidered Kurta with Chiffon Dupatta', 'Intricate embroidery with light chiffon dupatta.', 'kurta4.jpg', 'shop.php?product_id=4');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `order_cost` decimal(10,2) NOT NULL,
  `order_status` varchar(100) NOT NULL DEFAULT 'on_hold',
  `user_id` int(11) NOT NULL,
  `user_phone` varchar(20) NOT NULL,
  `user_city` varchar(255) NOT NULL,
  `user_address` varchar(255) NOT NULL,
  `order_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `order_cost`, `order_status`, `user_id`, `user_phone`, `user_city`, `user_address`, `order_date`) VALUES
(1, 465.00, 'not paid', 1, '1234', 'guj', 'newyork', '2025-08-22 17:56:08'),
(2, 465.00, 'not paid', 1, '1234', 'guj', 'newyork', '2025-08-22 17:58:04'),
(3, 465.00, 'not paid', 1, '1234', 'guj', 'newyork', '2025-08-22 18:04:16'),
(4, 465.00, 'not paid', 1, '1234', 'guj', 'newyork', '2025-08-22 18:06:31'),
(11, 465.00, 'pending', 1, '03316551524', 'gujranwala', 'city', '2025-08-24 14:03:14'),
(12, 310.00, 'pending', 1, '03316551524', 'gujranwala', 'city', '2025-08-24 14:04:15'),
(14, 155.00, 'pending', 1, '03316551524', 'gujranwala', 'city', '2025-08-24 14:05:24'),
(16, 465.00, 'pending', 1, '03316551524', 'gujranwala', 'city', '2025-08-24 17:28:53'),
(17, 155.00, 'pending', 1, '03316551524', 'gujranwala', 'city', '2025-08-24 18:08:31'),
(18, 155.00, 'pending', 1, '55666', ' ggg', 'qqqq', '2025-08-25 14:03:56'),
(19, 620.00, 'not paid', 3, '1234', 'guj', 'newyork', '2025-08-25 11:19:19'),
(22, 155.00, 'pending', 1, '03316551524', 'guj', 'newyork', '2025-08-29 09:28:30'),
(24, 22155.00, 'pending', 1, '03316551524', 'gujranwala', 'city', '2025-08-29 10:07:40'),
(28, 31996.00, 'pending', 1, '03316551524', 'gujranwala', 'city', '2025-08-29 19:22:04');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_image` varchar(255) NOT NULL,
  `product_price` int(11) NOT NULL,
  `product_quantity` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`item_id`, `order_id`, `product_id`, `product_name`, `product_image`, `product_price`, `product_quantity`, `user_id`, `order_date`) VALUES
(1, 4, 1, 'Brown shirt', 'product_4.png', 155, 3, 1, '2025-08-22 18:06:31'),
(2, 5, 2, 'Gray Bag', 'BG1.jpeg', 155, 1, 2, '2025-08-23 15:11:27'),
(3, 5, 1, 'White Shoes', 'sho.webp', 155, 1, 2, '2025-08-23 15:11:27'),
(4, 5, 3, 'Black Gray Bag', 'BG2.jpeg', 155, 1, 2, '2025-08-23 15:11:27'),
(5, 6, 3, 'Black Gray Bag', 'BG2.jpeg', 155, 1, 2, '2025-08-23 15:13:40'),
(6, 6, 5, 'Brown shirt', 'product_4.png', 155, 1, 2, '2025-08-23 15:13:40'),
(7, 7, 3, 'Black Gray Bag', 'BG2.jpeg', 155, 1, 2, '2025-08-23 15:34:50'),
(8, 8, 2, 'Gray Bag', 'BG1.jpeg', 155, 1, 2, '2025-08-23 15:37:56'),
(9, 9, 2, 'Gray Bag', 'BG1.jpeg', 155, 3, 2, '2025-08-23 15:38:20'),
(10, 10, 2, 'Gray Bag', 'BG1.jpeg', 155, 1, 2, '2025-08-23 15:45:55'),
(11, 13, 3, 'Black Gray Bag', 'BG2.jpeg', 155, 1, 2, '2025-08-24 10:34:56'),
(12, 15, 4, 'Black bag', 'BG3.jpg', 155, 1, 2, '2025-08-24 10:50:25'),
(13, 19, 2, 'Gray Bag', 'BG1.jpeg', 155, 1, 3, '2025-08-25 11:19:19'),
(14, 19, 4, 'Black bag', 'BG3.jpg', 155, 1, 3, '2025-08-25 11:19:19'),
(15, 19, 3, 'Black Gray Bag', 'BG2.jpeg', 155, 1, 3, '2025-08-25 11:19:19'),
(16, 19, 1, 'White Shoes', 'sho.webp', 155, 1, 3, '2025-08-25 11:19:19'),
(17, 20, 3, 'Black Gray Bag', 'BG2.jpeg', 155, 1, 2, '2025-08-28 15:08:48'),
(18, 21, 3, 'Black Gray Bag', 'BG2.jpeg', 155, 1, 2, '2025-08-29 06:28:08'),
(19, 23, 30, 'bagi pent with shirt', 'girl1 (20).jpg', 5500, 4, 2, '2025-08-29 07:06:55'),
(20, 23, 3, 'Black Gray Bag', 'BG2.jpeg', 155, 1, 2, '2025-08-29 07:06:55'),
(21, 25, 36, 'plates shirts', 'girl1 (26).jpg', 5999, 1, 2, '2025-08-29 08:10:29'),
(22, 26, 30, 'bagi pent with shirt', 'girl1 (20).jpg', 5500, 2, 2, '2025-08-29 16:15:47'),
(23, 26, 29, 'Arabic style kurta', 'girl1 (18).jpg', 4500, 2, 2, '2025-08-29 16:15:47'),
(24, 27, 22, 'jacket style', 'g2 (1).jpg', 7999, 4, 2, '2025-08-29 16:18:06'),
(25, 29, 23, 'Air line style shirt', 'g2 (2).jpg', 4999, 4, 2, '2025-08-29 16:31:10'),
(26, 29, 24, 'Long Frock', 'g2 (4).jpg', 3999, 3, 2, '2025-08-29 16:31:10'),
(27, 30, 26, 'Long Shirt', 'girl1 (15).jpg', 4999, 3, 2, '2025-08-29 16:36:03');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `mobile_no` varchar(20) DEFAULT NULL,
  `pin` varchar(10) DEFAULT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `account_no` varchar(50) DEFAULT NULL,
  `reference` varchar(255) DEFAULT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `order_id`, `payment_method`, `mobile_no`, `pin`, `bank_name`, `account_no`, `reference`, `payment_date`) VALUES
(1, 11, 'jazzcash', '3521783578', '5555', NULL, NULL, NULL, '2025-08-24 08:33:14'),
(2, 12, 'easypaisa', '126735473543', '22222', NULL, NULL, NULL, '2025-08-24 08:34:15'),
(3, 14, 'easypaisa', '126735473543', '22222', NULL, NULL, NULL, '2025-08-24 08:35:24'),
(4, 16, 'easypaisa', '126735473543', '111111', NULL, NULL, NULL, '2025-08-24 12:28:53'),
(5, 17, 'jazzcash', '3521783578', '5555', NULL, NULL, NULL, '2025-08-24 13:08:31'),
(6, 18, 'easypaisa', '126735473543', '11111', NULL, NULL, NULL, '2025-08-25 08:33:56'),
(7, 22, 'easypaisa', 'Pakistan', 'Pakistan', NULL, NULL, NULL, '2025-08-29 04:28:30'),
(8, 24, 'bank', NULL, '2222', '33', '2222', NULL, '2025-08-29 05:07:40'),
(9, 28, 'jazzcash', '3521783578', '5555', NULL, NULL, NULL, '2025-08-29 14:22:04');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `product_category` varchar(100) NOT NULL,
  `product_description` varchar(255) NOT NULL,
  `product_image` varchar(255) NOT NULL,
  `product_image2` varchar(255) NOT NULL,
  `product_image3` varchar(255) NOT NULL,
  `product_image4` varchar(255) NOT NULL,
  `product_price` decimal(10,2) UNSIGNED NOT NULL,
  `product_special_offer` int(2) NOT NULL,
  `product_color` varchar(100) NOT NULL
) ;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `product_category`, `product_description`, `product_image`, `product_image2`, `product_image3`, `product_image4`, `product_price`, `product_special_offer`, `product_color`) VALUES
(1, 'White Shoes', 'sho', 'awesome white shoes', 'sho.webp', 'BG1.jpeg', 'BG2.jpeg', 'BG3.jpg', 3500.00, 0, 'white'),
(2, 'Gray Bag', 'bags', 'awesome gray bags', 'BG1.jpeg', 'sho.webp', 'BG2.jpeg', 'BG3.jpg', 39990.00, 0, 'gray'),
(3, 'Black Gray Bag', 'bags', 'awesome bag', 'BG2.jpeg', 'BG1.jpeg', 'sho.webp', 'BG3.jpg', 45000.00, 0, 'black gray'),
(4, 'Black bag', 'bag', 'awesome bag', 'BG3.jpg', 'BG1.jpeg', 'BG2.jpeg', 'sho.webp', 3500.00, 0, 'black'),
(5, 'Brown shirt', 'dresses', 'Brown shirt for women', 'product_4.png', 'product_5.png', 'product_6.png', 'product_7.png', 3999.00, 0, 'Brown'),
(6, 'Purple shirt', 'dresses', 'Purple shirt for women', 'product_5.png', 'product_4.png', 'product_6.png', 'product_7.png', 7999.00, 0, 'Purple'),
(7, 'Shirt with Hijab', 'dresses', ' shirt with hijab for women', 'product_6.png', 'product_4.png', 'product_5.png', 'product_7.png', 5700.00, 0, 'camel color'),
(8, 'White Neck shirt', 'dresses', 'Neck shirt for women', 'product_7.png', 'product_4.png', 'product_5.png', 'product_6.png', 6999.00, 0, 'white'),
(13, 'Smart watch', 'watches', 'white strap smart watch', 'watches1.webp', 'watches2.jpeg', 'watches3.jpg', 'watches4.jpeg', 5000.00, 0, 'white'),
(14, 'Rolex watch', 'watches', 'Leather Strap Rolex watch', 'watches2.jpeg', 'watches1.webp', 'watches3.jpg', 'watches4.jpeg', 7000.00, 0, 'Black'),
(15, '3D smart watch', 'watches', '3D smart watch black', 'watches3.jpg', 'watches1.webp', 'watches2.jpeg', 'watches4.jpeg', 6000.00, 0, 'Black'),
(16, 'Braclet Watch', 'watches', 'Braclet Watch in black', 'watches4.jpeg', 'watches1.webp', 'watches2.jpeg', 'watches3.jpg', 6500.00, 0, 'Black golden'),
(17, 'Black Shoes', 'shoes', 'Black shoes', 'shoes1.jpeg', 'shoes2.jpeg', 'shoes3.jpeg', 'shoes4.jpeg', 3000.00, 0, 'Black'),
(18, 'Blue Solid Men running', 'shoes', 'Nevy Blue shoes', 'shoes2.jpeg', 'shoes3.jpeg', 'shoes4.jpeg', 'shoes1.jpeg', 5000.00, 0, 'Black'),
(19, 'Sky sneaker', 'shoes', 'sky sneaker shoes', 'shoes3.jpeg', 'shoes2.jpeg', 'shoes1.jpeg', 'shoes4.jpeg', 3999.00, 0, 'Sky'),
(20, 'Sports shoes', 'shoes', 'sports shoes', 'shoes4.jpeg', 'shoes1.jpeg', 'shoes2.jpeg', 'shoes3.jpeg', 4500.00, 0, 'Black Sports'),
(21, 'Short Kurti ', 'kurta', 'Short Kurti with Plazo All item is available in related to this', 'g1.jpg', 'girl1 (8).jpg', 'girl1 (13).jpg', 'girl1 (37).jpg', 4599.00, 0, 'All color available'),
(22, 'jacket style', 'kurta', 'all styles and color is availabe ', 'g2 (1).jpg', 'g2 (2).jpg', 'g2 (3).jpg', 'g2 (4).jpg', 7999.00, 0, ''),
(23, 'Air line style shirt', 'kurta', 'all style and color  is available', 'g2 (2).jpg', 'g2 (3).jpg', 'g2 (4).jpg', 'g2 (5).jpg', 4999.00, 0, 'black'),
(24, 'Long Frock', 'kurta', 'all style and color are available', 'g2 (4).jpg', 'g2 (5).jpg', 'g1 (7).jpg', 'g1 (8).jpg', 3999.00, 0, 'white'),
(25, 'Long Shirt', 'kurta', 'All style and color is available', 'girl1 (11).jpg', 'girl1 (12).jpg', 'girl1 (13).jpg', 'girl1 (14).jpg', 4000.00, 0, ''),
(26, 'Long Shirt', 'kurta', 'All style and color is available', 'girl1 (15).jpg', 'girl1 (12).jpg', 'girl1 (13).jpg', 'girl1 (14).jpg', 4999.00, 0, 'white'),
(27, 'smosa style ', 'kurta', 'All style and color are available', 'girl1 (17).jpg', 'girl1 (18).jpg', 'girl1 (19).jpg', 'girl1 (21).jpg', 3500.00, 0, 'lemon'),
(28, 'long shirt', 'kurta', 'All colors are available', 'girl1 (16).jpg', 'girl1 (17).jpg', 'girl1 (18).jpg', 'girl1 (19).jpg', 4000.00, 0, 'all'),
(29, 'Arabic style kurta', 'kurta', 'All colors are available', 'girl1 (18).jpg', 'girl1 (19).jpg', 'girl1 (20).jpg', 'girl1 (21).jpg', 4500.00, 0, 'multi color'),
(30, 'bagi pent with shirt', 'kurta', 'All colors are available', 'girl1 (20).jpg', 'girl1 (21).jpg', 'girl1 (22).jpg', 'girl1 (23).jpg', 5500.00, 0, 'All'),
(31, 'long kmeez', 'kurta', 'All colors are available ', 'girl1 (22).jpg', 'girl1 (23).jpg', 'girl1 (24).jpg', 'girl1 (25).jpg', 4999.00, 0, 'All'),
(32, 'Gone style kurta', 'kurta', 'All colors are available', 'girl1 (21).jpg', 'girl1 (22).jpg', 'girl1 (23).jpg', 'girl1 (24).jpg', 5999.00, 0, 'All'),
(33, 'long kmeez', 'kurta', 'All colors are available', 'girl1 (24).jpg', 'girl1 (25).jpg', 'girl1 (26).jpg', 'girl1 (23).jpg', 7999.00, 0, 'All'),
(34, 'kurta pajama', 'kurta', 'All colors are available', 'girl1 (23).jpg', 'girl1 (24).jpg', 'girl1 (25).jpg', 'girl1 (26).jpg', 5500.00, 0, 'all'),
(35, 'gol gaira shirt style', 'kurta', 'All colors are available ', 'girl1 (25).jpg', 'girl1 (26).jpg', 'girl1 (27).jpg', 'girl1 (28).jpg', 4000.00, 0, 'All'),
(36, 'plates shirts', 'kurta', 'All colors are available', 'girl1 (26).jpg', 'girl1 (27).jpg', 'girl1 (28).jpg', 'girl1 (29).jpg', 5999.00, 0, 'All'),
(37, 'kurta pajama', 'kurta', 'All colors are available', 'girl1 (27).jpg', 'girl1 (28).jpg', 'girl1 (29).jpg', 'girl1 (30).jpg', 7999.00, 0, 'All'),
(38, 'Long kmeez', 'kurta', 'All colors are available ', 'girl1 (28).jpg', 'girl1 (29).jpg', 'girl1 (30).jpg', 'girl1 (31).jpg', 5500.00, 0, ''),
(39, 'short kurta', 'kurta', 'All colors are available', 'girl1 (29).jpg', 'girl1 (30).jpg', 'girl1 (31).jpg', 'girl1 (32).jpg', 3500.00, 0, 'Black'),
(40, 'Long kmeez', 'kurta', 'All colors are available', 'girl1 (30).jpg', 'girl1 (31).jpg', 'girl1 (32).jpg', 'girl1 (33).jpg', 4500.00, 0, 'All'),
(41, 'Farshi shalwar with short kurta', 'kurta', 'All colors are available ', 'girl1 (32).jpg', 'girl1 (31).jpg', 'girl1 (33).jpg', 'girl1 (34).jpg', 7999.00, 0, 'All'),
(42, 'gol ghaira kmeez', 'kurta', 'All colors are available ', 'girl1 (33).jpg', 'girl1 (34).jpg', 'girl1 (35).jpg', 'girl1 (36).jpg', 3500.00, 0, 'All'),
(43, 'Gol Ktaon wala ghaira', 'kurta', 'All colors are available ', 'girl1 (34).jpg', 'girl1 (35).jpg', 'girl1 (36).jpg', 'girl1 (37).jpg', 3500.00, 0, 'All'),
(44, 'Short kmeez', 'kurta', 'All colors are available', 'girl1 (36).jpg', 'girl1 (37).jpg', 'girl1 (38).jpg', 'girl1 (39).jpg', 7000.00, 0, 'All'),
(45, 'Short kurta with jacket', 'kurta', 'All colors are available', 'girl1 (35).jpg', 'girl1 (36).jpg', 'girl1 (37).jpg', 'girl1 (38).jpg', 5000.00, 0, 'All'),
(46, 'Umbrella kurti', 'kurta', 'All colors are available', 'girl1 (37).jpg', 'girl1 (38).jpg', 'girl1 (39).jpg', 'girl1 (40).jpg', 5500.00, 0, 'All'),
(47, 'Shirt with bagi trouser', 'kurta', 'All colors are available', 'girl1 (38).jpg', 'girl1 (39).jpg', 'girl1 (40).jpg', 'girl1 (41).jpg', 3500.00, 0, 'All'),
(48, 'Kurta with  farshi trouser', 'kurta', 'All colors are available', 'girl1 (39).jpg', 'girl1 (40).jpg', 'girl1 (41).jpg', 'girl1 (42).jpg', 3500.00, 0, 'All '),
(49, 'Long frock', 'kurta', 'All colors are available', 'girl1 (40).jpg', 'girl1 (41).jpg', 'girl1 (42).jpg', 'girl1 (43).jpg', 5999.00, 0, 'All'),
(50, 'Short kmeez', 'kurta', 'All colors are available', 'girl1 (42).jpg', 'girl1 (42).jpg', 'girl1 (43).jpg', 'girl1 (44).jpg', 4000.00, 0, 'All'),
(51, 'Check style kurta', 'kurta', 'All colors are available', 'girl1 (43).jpg', 'girl1 (44).jpg', 'girl1 (45).jpg', 'girl1 (46).jpg', 4000.00, 0, 'All'),
(52, 'Short kurti with long gone', 'kurti', 'All colors are available', 'girl1 (48).jpg', 'girl1 (47).jpg', 'girl1 (46).jpg', 'girl1 (45).jpg', 5000.00, 0, 'All'),
(53, 'V gla kurti', 'kurta', 'All colors are available', 'girl1 (49).jpg', 'girl1 (50).jpg', 'girl1 (51).jpg', 'girl1 (52).jpg', 9000.00, 0, 'All'),
(54, 'Short Frock', 'kurta', 'All colors are available', 'girl1 (51).jpg', 'girl1 (52).jpg', 'girl1 (53).jpg', 'girl1 (54).jpg', 4000.00, 0, 'Black'),
(55, 'Kurta pajama', 'kurta', 'All colors are available', 'girl1 (53).jpg', 'girl1 (54).jpg', 'girl1 (55).jpg', 'girl1 (56).jpg', 3500.00, 0, 'All'),
(56, 'Long Frock', 'kurta', 'All colors are available ', 'girl1 (55).jpg', 'girl1 (56).jpg', 'girl1 (57).jpg', 'girl1 (58).jpg', 5999.00, 0, 'All'),
(57, 'Kurta pajama', 'kurta', 'All colors are available', 'girl1 (54).jpg', 'girl1 (55).jpg', 'girl1 (56).jpg', 'girl1 (57).jpg', 7000.00, 0, 'All'),
(58, 'Belt frock ', 'kurta', 'All colors are available', 'girl1 (52).jpg', 'girl1 (53).jpg', 'girl1 (54).jpg', 'girl1 (55).jpg', 4000.00, 0, 'All'),
(59, 'Jacket style kurta', 'kurta', 'All colors are available', 'girl1 (56).jpg', 'girl1 (57).jpg', 'girl1 (58).jpg', 'girl1 (59).jpg', 8000.00, 0, 'All'),
(60, 'V style kurta', 'kurta', 'All colors are available', 'girl1 (57).jpg', 'girl1 (58).jpg', 'girl1 (59).jpg', 'girl1 (60).jpg', 3500.00, 0, 'All'),
(61, 'Kurta pajama', 'kurta', 'All colors are available', 'girl1 (58).jpg', 'girl1 (59).jpg', 'girl1 (60).jpg', 'girl1 (61).jpg', 3500.00, 0, 'All'),
(62, 'V style frock', 'kurta', 'All colors are available', 'girl1 (59).jpg', 'girl1 (60).jpg', 'girl1 (61).jpg', 'girl1 (62).jpg', 6000.00, 0, 'All'),
(63, 'Short kurta', 'kurta', 'All colors are available', 'girl1 (60).jpg', 'girl1 (62).jpg', 'girl1 (61).jpg', 'girl1 (63).jpg', 4000.00, 0, 'All'),
(72, 'Kurta pajama', 'kurta', 'all color available', '1756470170_5390.jpg', '', '', '', 78999.00, 0, ''),
(74, 'Kurta pajama', 'kurta', 'all', 'girl1 (52).jpg', '', '', '', 6666.00, 0, ''),
(75, 'kurta', 'kurta', 'all', 'girl1 (44).jpg', '', '', '', 6666.00, 0, ''),
(76, 'Kurta pajama', 'kurta', 'all', '1756837424_6098.jpg', '', '', '', 99999.00, 0, '');

--
-- Triggers `products`
--
DELIMITER $$
CREATE TRIGGER `prevent_negative_price_update` BEFORE UPDATE ON `products` FOR EACH ROW BEGIN
    IF NEW.product_price < 0 THEN
        SET NEW.product_price = OLD.product_price;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `user_password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_name`, `user_email`, `user_password`) VALUES
(2, 'qq', 'qq@gmail.com', '$2y$10$6fQBNrpilSUIg5dqsFwxeuBJt5Ix8GWQO8pCBus5T3BgPI2J7BNni'),
(4, 'Admin', 'admin@example.com', '$2y$10$T8p2QnVez2vJ6gG4G3wQ0u0k1zE6s4fOe8U4nOxI1v9w4HqYpP8yG'),
(5, 'Yy', 'yy@Gmail.com', '$2y$10$MebPz3yyky7nGeQhmOSAQOZdrAi6lwwS3DRtMj.aifV2A0nj7UejS');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `admin_email` (`admin_email`);

--
-- Indexes for table `blogs`
--
ALTER TABLE `blogs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `fk_payment_order` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `UX_Constraint` (`user_email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `blogs`
--
ALTER TABLE `blogs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payment_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
