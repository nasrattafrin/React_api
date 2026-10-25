-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 25, 2026 at 05:10 AM
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
-- Database: `courier_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

CREATE TABLE `addresses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` tinyint(4) DEFAULT 0 COMMENT '0=sender, 1=receiver',
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `addresses`
--

INSERT INTO `addresses` (`id`, `user_id`, `type`, `name`, `phone`, `address`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `image`) VALUES
(24, 1, 1, 'Azam Ali675', '01890000', 'yuijfyut', '2026-10-12 13:06:35', NULL, '2026-10-12 04:56:10', NULL, NULL, 'uploads/1398140430_logo1.jpg'),
(25, 1, 1, 'iyi77y', '123456', 'O', '2026-10-12 13:29:49', NULL, '2026-10-12 04:29:49', NULL, NULL, 'uploads/856018030_logo1.jpg'),
(26, 1, 1, 'Azam Ali', '123456', 'RGTER', '2026-10-12 13:34:16', NULL, '2026-10-12 04:34:16', NULL, NULL, 'uploads/718284960_logo1.jpg'),
(27, 1, 1, 'FCS', 'ETRE4YT', 'RE5YH5RYU', '2026-10-12 13:48:39', NULL, '2026-10-12 04:48:39', NULL, NULL, 'uploads/1873254522_logo1.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` int(11) NOT NULL,
  `branch_name` varchar(100) NOT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `branch_name`, `address`, `phone`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`) VALUES
(1, 'Dhaka', 'Mirpur 10, Dhaka', '01711111111', '2026-10-13 04:30:23', NULL, '2026-10-13 07:42:51', 1, NULL),
(2, 'Chittagong', 'Agrabad, Chittagong', '01822222222', '2026-10-13 04:30:23', NULL, NULL, NULL, NULL),
(3, 'Sylhet', 'Zindabazar, Sylhet', '01933333333', '2026-10-13 04:30:23', NULL, NULL, NULL, NULL),
(4, 'Khulna', 'Sonadanga, Khulna', '01644444444', '2026-10-13 04:30:23', NULL, NULL, NULL, '2026-10-13 08:15:02'),
(5, 'Rajshahi', 'Shaheb Bazar, Rajshahi', '01755555555', '2026-10-13 04:30:23', NULL, NULL, NULL, '2026-10-13 07:42:58'),
(6, 'Rajshahi', 'Rupnagar,rajshahi', '01890000', '2026-10-13 15:17:26', 1, '2026-10-13 08:23:47', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cod_settlements`
--

CREATE TABLE `cod_settlements` (
  `id` int(11) NOT NULL,
  `parcel_id` int(11) NOT NULL,
  `deliveryman_id` int(11) NOT NULL,
  `merchant_id` int(11) NOT NULL,
  `cod_amount` decimal(10,2) NOT NULL,
  `service_charge` decimal(10,2) NOT NULL DEFAULT 0.00,
  `net_amount` decimal(10,2) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0=collected,1=received,2=settled',
  `settled_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cod_settlements`
--

INSERT INTO `cod_settlements` (`id`, `parcel_id`, `deliveryman_id`, `merchant_id`, `cod_amount`, `service_charge`, `net_amount`, `status`, `settled_at`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`) VALUES
(1, 5, 5, 1, 500.00, 25.00, 475.00, 2, '2026-10-24 00:18:16', '2026-10-19 06:02:59', 1, '2026-10-24 04:36:37', 1, '2026-10-24 04:36:37'),
(2, 6, 5, 1, 3000.00, 150.00, 2850.00, 0, NULL, '2026-10-25 03:22:29', 1, '2026-10-25 03:45:40', 1, '2026-10-25 03:45:40'),
(3, 7, 6, 1, 1197.00, 59.85, 1137.15, 2, '2026-10-24 22:51:30', '2026-10-25 03:46:37', 1, '2026-10-25 03:51:30', 1, NULL),
(4, 8, 6, 7, 1499.00, 74.95, 1424.05, 0, NULL, '2026-10-25 04:03:00', 1, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `parcels`
--

CREATE TABLE `parcels` (
  `id` int(11) NOT NULL,
  `tracking_id` varchar(50) NOT NULL,
  `sender_id` int(10) UNSIGNED NOT NULL,
  `receiver_name` varchar(100) NOT NULL,
  `receiver_phone` varchar(20) NOT NULL,
  `pickup_address` text NOT NULL,
  `delivery_address` text NOT NULL,
  `weight` decimal(5,2) NOT NULL,
  `zone_id` int(11) NOT NULL,
  `delivery_charge` decimal(10,2) NOT NULL DEFAULT 0.00,
  `cod_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0=pending,1=picked,2=in_transit,3=delivered,4=cancelled',
  `payment_status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0=unpaid,1=collected,2=settled,3=withdrawn',
  `assigned_to` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parcels`
--

INSERT INTO `parcels` (`id`, `tracking_id`, `sender_id`, `receiver_name`, `receiver_phone`, `pickup_address`, `delivery_address`, `weight`, `zone_id`, `delivery_charge`, `cod_amount`, `status`, `payment_status`, `assigned_to`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`) VALUES
(1, 'TRK-20261017-365CFE', 1, 'Md Azam Ali', '018196112', 'dfqwr', 'asgat', 0.50, 1, 60.00, 12000.00, 4, 0, 5, '2026-10-17 04:54:37', 1, '2026-10-18 03:11:22', 1, NULL),
(2, 'TRK-20261018-FA695B', 1, 'Shamsun Nahar', '01858322', 'cda', 'cda', 0.80, 1, 60.00, 1200.00, 3, 1, 5, '2026-10-18 04:00:02', 1, '2026-10-18 04:06:34', 1, NULL),
(6, 'TRK-20261025-F75AE8', 1, 'Sadman', '015625461', 'nasirbad', 'nasirabad', 0.80, 2, 130.00, 3000.00, 3, 1, 5, '2026-10-25 03:11:29', 1, '2026-10-25 03:22:29', 1, NULL),
(7, 'TRK-20261025-21F012', 1, 'Safat', '012836278', 'fouzdarhat', 'fouzdarhat', 0.60, 1, 60.00, 1197.00, 3, 1, 6, '2026-10-25 03:13:58', 1, '2026-10-25 03:46:37', 1, NULL),
(8, 'TRK-20261025-409E8C', 7, 'Nasrat', '012723', 'cda', 'cda', 0.50, 1, 60.00, 1499.00, 3, 1, 6, '2026-10-25 04:02:10', 1, '2026-10-25 04:03:00', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `parcel_logs`
--

CREATE TABLE `parcel_logs` (
  `id` int(11) NOT NULL,
  `parcel_id` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL COMMENT '0=pending,1=picked,2=in_transit,3=delivered,4=cancelled',
  `note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parcel_logs`
--

INSERT INTO `parcel_logs` (`id`, `parcel_id`, `status`, `note`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`) VALUES
(1, 1, 0, 'Parcel created', '2026-10-17 04:54:37', 1, NULL, NULL, NULL),
(2, 1, 0, 'Assigned to deliveryman #5', '2026-10-17 06:33:55', 1, NULL, NULL, NULL),
(3, 1, 4, 'Cancelled from list', '2026-10-18 03:11:22', 1, NULL, NULL, NULL),
(4, 2, 0, 'Parcel created', '2026-10-18 04:00:02', 1, NULL, NULL, NULL),
(5, 2, 0, 'Assigned to deliveryman #5', '2026-10-18 04:00:09', 1, NULL, NULL, NULL),
(6, 2, 1, 'Status updated', '2026-10-18 04:00:14', 1, NULL, NULL, NULL),
(7, 2, 2, 'Status updated', '2026-10-18 04:06:30', 1, NULL, NULL, NULL),
(8, 2, 3, 'Status updated', '2026-10-18 04:06:34', 1, NULL, NULL, NULL),
(9, 3, 0, 'Parcel created', '2026-10-18 05:45:09', 1, NULL, NULL, NULL),
(10, 3, 0, 'Assigned to deliveryman #5', '2026-10-18 05:45:56', 1, NULL, NULL, NULL),
(11, 3, 1, 'Status updated', '2026-10-18 05:45:59', 1, NULL, NULL, NULL),
(12, 3, 2, 'Status updated', '2026-10-18 05:46:02', 1, NULL, NULL, NULL),
(13, 3, 3, 'Status updated', '2026-10-18 05:46:06', 1, NULL, NULL, NULL),
(14, 4, 0, 'Parcel created', '2026-10-18 07:10:07', 1, NULL, NULL, NULL),
(15, 4, 0, 'Assigned to deliveryman #5', '2026-10-18 07:10:12', 1, NULL, NULL, NULL),
(16, 4, 0, 'Assigned to deliveryman #5', '2026-10-18 07:10:15', 1, NULL, NULL, NULL),
(17, 4, 0, 'Assigned to deliveryman #5', '2026-10-18 07:10:19', 1, NULL, NULL, NULL),
(18, 4, 0, 'Assigned to deliveryman #5', '2026-10-18 07:10:24', 1, NULL, NULL, NULL),
(19, 4, 1, 'Status updated', '2026-10-18 07:10:29', 1, NULL, NULL, NULL),
(20, 4, 1, 'Assigned to deliveryman #5', '2026-10-18 07:10:50', 1, NULL, NULL, NULL),
(21, 4, 1, 'Status updated', '2026-10-18 07:10:55', 1, NULL, NULL, NULL),
(22, 4, 1, 'Assigned to deliveryman #5', '2026-10-18 07:11:04', 1, NULL, NULL, NULL),
(23, 4, 2, 'Status updated', '2026-10-18 07:13:09', 1, NULL, NULL, NULL),
(24, 4, 3, 'Status updated', '2026-10-18 07:13:46', 1, NULL, NULL, NULL),
(25, 5, 0, 'Parcel created', '2026-10-19 06:01:48', 1, NULL, NULL, NULL),
(26, 5, 0, 'Assigned to deliveryman #5', '2026-10-19 06:01:53', 1, NULL, NULL, NULL),
(27, 5, 1, 'Status updated', '2026-10-19 06:01:54', 1, NULL, NULL, NULL),
(28, 5, 2, 'Status updated', '2026-10-19 06:01:59', 1, NULL, NULL, NULL),
(29, 5, 2, 'Parcel info updated', '2026-10-19 06:02:44', 1, NULL, NULL, NULL),
(30, 5, 3, 'Status updated', '2026-10-19 06:02:59', 1, NULL, NULL, NULL),
(31, 6, 0, 'Parcel created', '2026-10-25 03:11:29', 1, NULL, NULL, NULL),
(32, 6, 0, 'Assigned to deliveryman #5', '2026-10-25 03:11:34', 1, NULL, NULL, NULL),
(33, 6, 1, 'Status updated', '2026-10-25 03:11:40', 1, NULL, NULL, NULL),
(34, 7, 0, 'Parcel created', '2026-10-25 03:13:58', 1, NULL, NULL, NULL),
(35, 7, 0, 'Assigned to deliveryman #5', '2026-10-25 03:14:05', 1, NULL, NULL, NULL),
(36, 7, 1, 'Status updated', '2026-10-25 03:14:07', 1, NULL, NULL, NULL),
(37, 7, 2, 'Status updated', '2026-10-25 03:14:09', 1, NULL, NULL, NULL),
(38, 6, 1, 'Parcel info updated', '2026-10-25 03:14:40', 1, NULL, NULL, NULL),
(39, 6, 1, 'Parcel info updated', '2026-10-25 03:14:41', 1, NULL, NULL, NULL),
(40, 6, 2, 'Status updated', '2026-10-25 03:22:10', 1, NULL, NULL, NULL),
(41, 6, 3, 'Status updated', '2026-10-25 03:22:29', 1, NULL, NULL, NULL),
(42, 7, 2, 'Assigned to deliveryman #6', '2026-10-25 03:46:12', 1, NULL, NULL, NULL),
(43, 7, 3, 'Status updated', '2026-10-25 03:46:37', 1, NULL, NULL, NULL),
(44, 8, 0, 'Parcel created', '2026-10-25 04:02:10', 1, NULL, NULL, NULL),
(45, 8, 0, 'Assigned to deliveryman #6', '2026-10-25 04:02:14', 1, NULL, NULL, NULL),
(46, 8, 1, 'Status updated', '2026-10-25 04:02:57', 1, NULL, NULL, NULL),
(47, 8, 2, 'Status updated', '2026-10-25 04:02:58', 1, NULL, NULL, NULL),
(48, 8, 3, 'Status updated', '2026-10-25 04:03:00', 1, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `parcel_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `method` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0=bkash,1=nagad,2=card,3=cod',
  `transaction_id` varchar(100) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0=pending,1=success,2=failed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `parcel_id`, `amount`, `method`, `transaction_id`, `status`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`) VALUES
(1, 2, 1200.00, 2, 'bkas56y5ruy', 1, '2026-10-18 04:44:44', 1, '2026-10-18 05:38:08', 1, '2026-10-18 05:38:08'),
(2, 2, 1200.00, 3, '', 1, '2026-10-18 05:38:22', 1, NULL, NULL, NULL),
(3, 3, 1500.00, 3, '111', 1, '2026-10-18 05:46:53', 1, '2026-10-19 04:12:16', 1, '2026-10-19 04:12:16'),
(4, 3, 500.00, 3, '', 1, '2026-10-19 04:12:25', 1, NULL, NULL, NULL),
(5, 5, 500.00, 3, '', 1, '2026-10-19 06:02:56', 1, NULL, NULL, NULL),
(6, 6, 3000.00, 3, '', 1, '2026-10-25 03:22:27', 1, NULL, NULL, NULL),
(7, 7, 1197.00, 3, '', 1, '2026-10-25 03:46:32', 1, NULL, NULL, NULL),
(8, 8, 1499.00, 3, '', 1, '2026-10-25 04:02:52', 1, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pricing`
--

CREATE TABLE `pricing` (
  `id` int(11) NOT NULL,
  `zone_id` int(11) NOT NULL,
  `min_weight` decimal(5,2) NOT NULL,
  `max_weight` decimal(5,2) NOT NULL,
  `charge` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pricing`
--

INSERT INTO `pricing` (`id`, `zone_id`, `min_weight`, `max_weight`, `charge`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`) VALUES
(7, 1, 0.01, 0.88, 60.00, '2026-10-18 03:57:11', 1, NULL, NULL, NULL),
(8, 2, 0.01, 1.01, 130.00, '2026-10-18 03:57:58', 1, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_name`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`) VALUES
(1, 'Admin', '2026-10-13 03:31:57', 1, NULL, NULL, NULL),
(2, 'Merchant', '2026-10-13 03:31:57', 1, NULL, NULL, NULL),
(3, 'Deliveryman', '2026-10-13 03:31:57', 1, NULL, NULL, NULL),
(4, 'Customer', '2026-10-13 13:06:32', 1, NULL, NULL, '2026-10-13 06:07:37');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(100) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `contact_no` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `remember_token` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `contact_no`, `email`, `password`, `role_id`, `branch_id`, `address`, `status`, `remember_token`, `image`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'anika', NULL, 'anika@gmail.com', 'f7c3bc1d808e04732adf679965ccc34ca7ae3441', NULL, NULL, NULL, 1, '43531792817801396701', NULL, NULL, NULL, NULL),
(5, 'anika', '0123435', 'adrtin@gmail.com', 'f7c3bc1d808e04732adf679965ccc34ca7ae3441', 3, 1, '6tyrd57yd5r', 1, NULL, NULL, '2026-10-17 01:16:27', NULL, NULL),
(6, 'Tafrin', '0126382', 'tafrin@gmail.com', 'f7c3bc1d808e04732adf679965ccc34ca7ae3441', 3, 1, 'mirpur', 1, NULL, NULL, '2026-10-24 22:16:04', NULL, NULL),
(7, 'Merchant Farzana', '01711111111', 'rahim@gmail.com', '20eabe5d64b0e216796e834f52d61fd0b70332fc', 2, NULL, NULL, 1, NULL, NULL, '2026-10-25 03:18:47', NULL, NULL),
(8, 'Karim Traders', '01822222222', 'karim@gmail.com', '7c222fb2927d828af22f592134e8932480637c0d', 2, NULL, NULL, 1, NULL, NULL, '2026-10-25 03:20:17', NULL, NULL),
(9, 'Azam Enterprise', '01933333333', 'azam@gmail.com', '7c222fb2927d828af22f592134e8932480637c0d', 2, NULL, NULL, 1, NULL, NULL, '2026-10-25 03:20:17', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wallets`
--

CREATE TABLE `wallets` (
  `id` int(11) NOT NULL,
  `merchant_id` int(11) NOT NULL,
  `balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wallets`
--

INSERT INTO `wallets` (`id`, `merchant_id`, `balance`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`) VALUES
(2, 9, 59322.00, '2026-10-25 03:34:51', 1, '2026-10-25 03:35:53', 1, NULL),
(3, 8, 2000.00, '2026-10-25 03:43:41', 1, NULL, NULL, NULL),
(4, 1, 1137.15, '2026-10-25 03:51:30', 1, '2026-10-25 03:51:30', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wallet_transactions`
--

CREATE TABLE `wallet_transactions` (
  `id` int(11) NOT NULL,
  `wallet_id` int(11) NOT NULL,
  `type` tinyint(4) NOT NULL COMMENT '0=CREDIT, 1=DEBIT',
  `amount` decimal(10,2) NOT NULL,
  `reference` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wallet_transactions`
--

INSERT INTO `wallet_transactions` (`id`, `wallet_id`, `type`, `amount`, `reference`, `description`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`) VALUES
(1, 1, 0, 475.00, 'PARCEL-5', 'COD Settled Payout', '2026-10-24 04:18:16', 1, NULL, NULL, NULL),
(2, 1, 1, 1000.00, 'WITHDRAW-1', 'Withdraw approved (Pending Payment)', '2026-10-24 04:31:00', 1, NULL, NULL, NULL),
(3, 2, 0, 60000.00, 'ADMIN-DEPOSIT', 'Manual balance added by Admin', '2026-10-25 03:34:51', 1, NULL, NULL, NULL),
(4, 2, 1, 678.00, 'WITHDRAW-2', 'Withdraw approved (Pending Payment)', '2026-10-25 03:35:53', 1, NULL, NULL, NULL),
(5, 3, 0, 2000.00, 'ADMIN-DEPOSIT', 'Manual balance added by Admin', '2026-10-25 03:43:41', 1, NULL, NULL, NULL),
(6, 4, 0, 1137.15, 'PARCEL-7', 'COD Settled Payout', '2026-10-25 03:51:30', 1, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `withdraw_requests`
--

CREATE TABLE `withdraw_requests` (
  `id` int(11) NOT NULL,
  `merchant_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `method` tinyint(4) NOT NULL COMMENT '0=bkash,1=nagad,2=bank',
  `account_number` varchar(50) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0=pending,1=approved,2=paid,3=rejected',
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `processed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `withdraw_requests`
--

INSERT INTO `withdraw_requests` (`id`, `merchant_id`, `amount`, `method`, `account_number`, `status`, `requested_at`, `processed_at`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`) VALUES
(1, 1, 1000.00, 0, '01711111111', 1, '2026-10-24 04:20:55', '2026-10-24 00:31:00', '2026-10-24 04:20:55', 1, '2026-10-24 04:31:00', 1, NULL),
(2, 9, 678.00, 0, '01933333333', 1, '2026-10-24 22:35:49', '2026-10-24 22:35:54', '2026-10-25 03:35:49', 1, '2026-10-25 03:35:54', 1, NULL),
(3, 8, 1500.00, 0, '01822222222', 0, '2026-10-24 22:43:56', NULL, '2026-10-25 03:43:56', 1, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `zones`
--

CREATE TABLE `zones` (
  `id` int(11) NOT NULL,
  `zone_name` varchar(100) NOT NULL COMMENT '0=Inside City,1=Outside City',
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by` int(10) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `zones`
--

INSERT INTO `zones` (`id`, `zone_name`, `description`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`) VALUES
(1, 'Inside City', 'Delivery within city limits (e.g. Dhaka city)', '2026-10-17 04:53:02', 1, NULL, NULL, NULL),
(2, 'Outside City', 'Delivery outside city / upazila / district', '2026-10-17 04:53:02', 1, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cod_settlements`
--
ALTER TABLE `cod_settlements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `parcels`
--
ALTER TABLE `parcels`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `parcel_logs`
--
ALTER TABLE `parcel_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pricing`
--
ALTER TABLE `pricing`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `email_2` (`email`);

--
-- Indexes for table `wallets`
--
ALTER TABLE `wallets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `withdraw_requests`
--
ALTER TABLE `withdraw_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `zones`
--
ALTER TABLE `zones`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `cod_settlements`
--
ALTER TABLE `cod_settlements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `parcels`
--
ALTER TABLE `parcels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `parcel_logs`
--
ALTER TABLE `parcel_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `pricing`
--
ALTER TABLE `pricing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `wallets`
--
ALTER TABLE `wallets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `withdraw_requests`
--
ALTER TABLE `withdraw_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `zones`
--
ALTER TABLE `zones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
