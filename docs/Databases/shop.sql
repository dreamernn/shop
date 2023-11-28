/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 50739
 Source Host           : localhost:3306
 Source Schema         : shop

 Target Server Type    : MySQL
 Target Server Version : 50739
 File Encoding         : 65001

 Date: 28/11/2023 15:00:28
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for carts
-- ----------------------------
DROP TABLE IF EXISTS `carts`;
CREATE TABLE `carts` (
  `cart_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Cart ID',
  `user_id` int(11) NOT NULL COMMENT 'User ID',
  `product_id` int(11) NOT NULL COMMENT 'Product ID',
  `quantity` int(11) NOT NULL COMMENT 'Quantity',
  `sku` varchar(50) NOT NULL COMMENT 'SKU',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Status: 0 - Pending, 1 - Completed',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Delete 0 - No 1 - Yes',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Created Time',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Updated Time',
  PRIMARY KEY (`cart_id`) USING BTREE,
  KEY `user_id` (`user_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`user_id`),
  CONSTRAINT `carts_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `Products` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COMMENT='Table for storing cart information and details';

-- ----------------------------
-- Table structure for orders
-- ----------------------------
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'order ID',
  `user_id` int(11) NOT NULL COMMENT 'User ID',
  `cart_detail` text NOT NULL COMMENT 'Cart Info',
  `first_name` varchar(50) NOT NULL COMMENT 'first_name',
  `last_name` varchar(50) NOT NULL COMMENT 'last_name',
  `email` varchar(50) NOT NULL COMMENT 'email',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT 'Status: 0 - UnPay, 1 - Paid, etc.',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Created Time',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Updated Time',
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for products
-- ----------------------------
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `product_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Product ID',
  `sku` varchar(50) NOT NULL COMMENT 'SKU',
  `name` varchar(100) NOT NULL COMMENT 'Product Name',
  `description` text COMMENT 'Product Description',
  `price` decimal(10,2) NOT NULL COMMENT 'Price',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Created Time',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Updated Time',
  PRIMARY KEY (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COMMENT='Table for storing product information';

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'User ID',
  `username` varchar(50) NOT NULL COMMENT 'Username',
  `password` varchar(100) NOT NULL COMMENT 'Password',
  `role` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Role: 0 - Customer, 1 - Admin',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Created Time',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Updated Time',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COMMENT='Table for storing user information';

SET FOREIGN_KEY_CHECKS = 1;

INSERT INTO `users` (`user_id`, `username`, `password`, `role`, `created_at`, `updated_at`) VALUES (1, 'user1', 'e10adc3949ba59abbe56e057f20f883e', 0, '2023-11-25 13:30:29', '2023-11-25 13:30:29');
INSERT INTO `users` (`user_id`, `username`, `password`, `role`, `created_at`, `updated_at`) VALUES (2, 'admin1', 'e10adc3949ba59abbe56e057f20f883e', 1, '2023-11-25 13:40:27', '2023-11-25 13:40:27');
INSERT INTO `products` (`product_id`, `sku`, `name`, `description`, `price`, `created_at`, `updated_at`) VALUES (1, 'XL', 'test1_name', 'test description', 100.00, '2023-11-25 14:40:08', '2023-11-28 15:01:51');
INSERT INTO `products` (`product_id`, `sku`, `name`, `description`, `price`, `created_at`, `updated_at`) VALUES (2, 'M,M1,M2', 'test2_name', 'test2_desc', 50.00, '2023-11-25 14:40:37', '2023-11-27 11:44:22');
INSERT INTO `products` (`product_id`, `sku`, `name`, `description`, `price`, `created_at`, `updated_at`) VALUES (3, 'XL,XL1,XL2', 'test3_name', 'test3_desc', 20.00, '2023-11-25 14:41:03', '2023-11-27 12:27:26');