-- MySQL dump 10.13  Distrib 8.0.38, for Win64 (x86_64)
--
-- Host: localhost    Database: shop
-- ------------------------------------------------------
-- Server version	8.0.39

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `audit_trail`
--

DROP TABLE IF EXISTS `audit_trail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_trail` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `user_email_snapshot` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Email of the user at the time of action, if applicable',
  `activity_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'e.g., USER_LOGIN, PRODUCT_UPDATE, ORDER_PLACED',
  `target_entity` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'e.g., product, user, order',
  `target_id` int DEFAULT NULL COMMENT 'ID of the entity affected',
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Detailed description of the activity',
  `details_before` text COLLATE utf8mb4_unicode_ci COMMENT 'JSON snapshot of data before change',
  `details_after` text COLLATE utf8mb4_unicode_ci COMMENT 'JSON snapshot of data after change',
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `audit_trail_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_trail`
--

LOCK TABLES `audit_trail` WRITE;
/*!40000 ALTER TABLE `audit_trail` DISABLE KEYS */;
INSERT INTO `audit_trail` VALUES (1,NULL,'rjasoncapuno@gmail.com','USER_REGISTRATION','user',1,'User \'rjasoncapuno@gmail.com\' registered successfully.',NULL,NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0','2025-05-15 13:02:04'),(2,NULL,'rjasoncapuno@gmail.com','USER_LOGIN_FAILURE','user',1,'Failed login attempt for email \'rjasoncapuno@gmail.com\'. Reason: Incorrect password.',NULL,NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0','2025-05-15 13:02:23'),(3,NULL,'rjasoncapuno@gmail.com','USER_LOGIN_FAILURE','user',1,'Failed login attempt for email \'rjasoncapuno@gmail.com\'. Reason: Incorrect password.',NULL,NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0','2025-05-15 13:02:35'),(4,2,'ken@gmail.com','USER_REGISTRATION','user',2,'User \'ken@gmail.com\' registered successfully.',NULL,NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0','2025-05-15 13:03:08'),(5,2,'ken@gmail.com','USER_LOGIN_SUCCESS','user',2,'User \'ken@gmail.com\' logged in successfully.',NULL,NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0','2025-05-15 13:03:15'),(6,2,'ken@gmail.com','USER_LOGIN_SUCCESS','user',2,'User \'ken@gmail.com\' logged in successfully.',NULL,NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0','2025-05-15 13:03:48'),(7,3,'ronald@gmail.com','USER_REGISTRATION','user',3,'User \'ronald@gmail.com\' registered successfully.',NULL,NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0','2025-05-15 13:07:53'),(8,3,'ronald@gmail.com','USER_LOGIN_SUCCESS','user',3,'User \'ronald@gmail.com\' logged in successfully.',NULL,NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0','2025-05-15 13:08:00'),(9,2,'ken@gmail.com','USER_LOGIN_SUCCESS','user',2,'User \'ken@gmail.com\' logged in successfully.',NULL,NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0','2025-05-15 13:08:37'),(10,3,'ronald@gmail.com','USER_LOGIN_SUCCESS','user',3,'User \'ronald@gmail.com\' logged in successfully.',NULL,NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0','2025-05-15 13:16:32'),(11,3,'ronald@gmail.com','USER_LOGIN_SUCCESS','user',3,'User \'ronald@gmail.com\' logged in successfully.',NULL,NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0','2025-05-15 13:38:15'),(12,3,'ronald@gmail.com','USER_LOGIN_SUCCESS','user',3,'User \'ronald@gmail.com\' logged in successfully.',NULL,NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0','2025-05-15 14:24:01'),(13,3,'ronald@gmail.com','USER_LOGIN_SUCCESS','user',3,'User \'ronald@gmail.com\' logged in successfully.',NULL,NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0','2025-05-15 14:24:52'),(14,3,'ronald@gmail.com','USER_LOGIN_FAILURE','user',3,'Failed login attempt for email \'ronald@gmail.com\'. Reason: Incorrect password.',NULL,NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0','2025-05-15 14:29:33'),(15,3,'ronald@gmail.com','USER_LOGIN_SUCCESS','user',3,'User \'ronald@gmail.com\' logged in successfully.',NULL,NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0','2025-05-15 14:29:38'),(16,3,'ronald@gmail.com','USER_LOGIN_FAILURE','user',3,'Failed login attempt for email \'ronald@gmail.com\'. Reason: Incorrect password.',NULL,NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0','2025-05-15 15:06:11'),(17,3,'ronald@gmail.com','USER_LOGIN_SUCCESS','user',3,'User \'ronald@gmail.com\' logged in successfully.',NULL,NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0','2025-05-15 15:06:15'),(18,3,'ronald@gmail.com','USER_LOGIN_SUCCESS','user',3,'User \'ronald@gmail.com\' logged in successfully.',NULL,NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0','2025-05-15 15:06:39'),(19,NULL,'ronald@gmal.com','USER_LOGIN_FAILURE','user',NULL,'Failed login attempt for email \'ronald@gmal.com\'. Reason: User not found.',NULL,NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0','2025-05-15 15:17:18'),(20,NULL,'ronald@gmal.com','USER_LOGIN_FAILURE','user',NULL,'Failed login attempt for email \'ronald@gmal.com\'. Reason: User not found.',NULL,NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0','2025-05-15 15:17:23'),(21,3,'ronald@gmail.com','USER_LOGIN_SUCCESS','user',3,'User \'ronald@gmail.com\' logged in successfully.',NULL,NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0','2025-05-15 15:17:29'),(22,3,'ronald@gmail.com','USER_LOGIN_FAILURE','user',3,'Failed login attempt for email \'ronald@gmail.com\'. Reason: Incorrect password.',NULL,NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0','2025-05-15 15:52:55'),(23,3,'ronald@gmail.com','USER_LOGIN_SUCCESS','user',3,'User \'ronald@gmail.com\' logged in successfully.',NULL,NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0','2025-05-15 15:52:58'),(24,2,'ken@gmail.com','USER_LOGIN_SUCCESS','user',2,'User \'ken@gmail.com\' logged in successfully.',NULL,NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:138.0) Gecko/20100101 Firefox/138.0','2025-05-15 16:01:17');
/*!40000 ALTER TABLE `audit_trail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cart_items`
--

DROP TABLE IF EXISTS `cart_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cart_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL COMMENT 'Null if guest cart',
  `session_id` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'For guest carts, using PHP session ID or similar',
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `date_added` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_user_product` (`user_id`,`product_id`),
  UNIQUE KEY `uq_session_product` (`session_id`,`product_id`),
  KEY `product_id` (`product_id`),
  KEY `idx_cart_session_id` (`session_id`),
  CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart_items`
--

LOCK TABLES `cart_items` WRITE;
/*!40000 ALTER TABLE `cart_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `cart_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'URL-friendly identifier',
  `description` text COLLATE utf8mb4_unicode_ci,
  `parent_category_id` int DEFAULT NULL,
  `image_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `display_order` int DEFAULT '0',
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `slug` (`slug`),
  KEY `parent_category_id` (`parent_category_id`),
  CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Smartphone','','',NULL,NULL,1,0,'2025-05-15 13:09:02','2025-05-15 13:09:02');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `homepage_brands`
--

DROP TABLE IF EXISTS `homepage_brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `homepage_brands` (
  `id` int NOT NULL AUTO_INCREMENT,
  `logo_url` varchar(255) DEFAULT NULL,
  `alt_text` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `homepage_brands`
--

LOCK TABLES `homepage_brands` WRITE;
/*!40000 ALTER TABLE `homepage_brands` DISABLE KEYS */;
/*!40000 ALTER TABLE `homepage_brands` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `homepage_hero`
--

DROP TABLE IF EXISTS `homepage_hero`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `homepage_hero` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `button_text` varchar(100) DEFAULT NULL,
  `button_link` varchar(255) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `last_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `homepage_hero`
--

LOCK TABLES `homepage_hero` WRITE;
/*!40000 ALTER TABLE `homepage_hero` DISABLE KEYS */;
INSERT INTO `homepage_hero` VALUES (1,'Premium 2nd Hand Phones, Unbeatable Prices!','Get top-brand smartphones in excellent condition with full warranty.','Shop Now','/second_hand_phone_shop/shop.php','asset/images/upload/hero_1747286654_4750.jpg','2025-05-15 05:24:14');
/*!40000 ALTER TABLE `homepage_hero` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `homepage_reviews`
--

DROP TABLE IF EXISTS `homepage_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `homepage_reviews` (
  `id` int NOT NULL AUTO_INCREMENT,
  `reviewer_name` varchar(100) DEFAULT NULL,
  `avatar_url` varchar(255) DEFAULT NULL,
  `review_text` text,
  `rating` int DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `homepage_reviews`
--

LOCK TABLES `homepage_reviews` WRITE;
/*!40000 ALTER TABLE `homepage_reviews` DISABLE KEYS */;
/*!40000 ALTER TABLE `homepage_reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `product_id` int DEFAULT NULL COMMENT 'Allow NULL if product is deleted but order history needs to be maintained',
  `product_name_snapshot` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Name of product at time of purchase',
  `product_sku_snapshot` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'SKU of product at time of purchase',
  `quantity` int NOT NULL,
  `price_at_purchase` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `order_number` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Publicly visible order identifier',
  `shipping_address_id` int NOT NULL,
  `billing_address_id` int NOT NULL,
  `subtotal_amount` decimal(10,2) NOT NULL,
  `shipping_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_status` enum('pending','paid','failed','refunded','partially_refunded') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `transaction_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_status` enum('pending_payment','processing','shipped','delivered','cancelled','completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending_payment',
  `customer_notes` text COLLATE utf8mb4_unicode_ci,
  `admin_notes` text COLLATE utf8mb4_unicode_ci,
  `shipping_carrier` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tracking_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_ordered` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_shipped` datetime DEFAULT NULL,
  `date_delivered` datetime DEFAULT NULL,
  `date_updated` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_number` (`order_number`),
  UNIQUE KEY `transaction_id` (`transaction_id`),
  KEY `user_id` (`user_id`),
  KEY `shipping_address_id` (`shipping_address_id`),
  KEY `billing_address_id` (`billing_address_id`),
  KEY `idx_orders_status` (`order_status`),
  KEY `idx_orders_payment_status` (`payment_status`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`shipping_address_id`) REFERENCES `user_addresses` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`billing_address_id`) REFERENCES `user_addresses` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_images`
--

DROP TABLE IF EXISTS `product_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_images` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `image_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alt_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_thumbnail` tinyint(1) DEFAULT '0',
  `display_order` int DEFAULT '0',
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_images`
--

LOCK TABLES `product_images` WRITE;
/*!40000 ALTER TABLE `product_images` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_reviews`
--

DROP TABLE IF EXISTS `product_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_reviews` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `user_id` int NOT NULL,
  `rating` tinyint NOT NULL COMMENT 'Rating from 1 to 5',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `is_approved` tinyint(1) DEFAULT '0' COMMENT 'For moderation by admin',
  `approved_by_user_id` int DEFAULT NULL,
  `date_reviewed` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `user_id` (`user_id`),
  KEY `approved_by_user_id` (`approved_by_user_id`),
  CONSTRAINT `product_reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `product_reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `product_reviews_ibfk_3` FOREIGN KEY (`approved_by_user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `product_reviews_chk_1` CHECK (((`rating` >= 1) and (`rating` <= 5)))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_reviews`
--

LOCK TABLES `product_reviews` WRITE;
/*!40000 ALTER TABLE `product_reviews` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category_id` int DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(280) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'URL-friendly identifier',
  `sku` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Stock Keeping Unit',
  `description` text COLLATE utf8mb4_unicode_ci,
  `brand` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `condition` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'e.g., New, Used - Like New',
  `price` decimal(10,2) NOT NULL,
  `sale_price` decimal(10,2) DEFAULT NULL,
  `stock_quantity` int NOT NULL DEFAULT '0',
  `is_featured` tinyint(1) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1' COMMENT 'Whether the product is visible in the shop',
  `main_image_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  UNIQUE KEY `sku` (`sku`),
  KEY `category_id` (`category_id`),
  KEY `idx_products_name` (`name`),
  KEY `idx_products_is_featured` (`is_featured`),
  KEY `idx_products_is_active` (`is_active`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,1,'iPhone 16 Pro Max','iphone-16-pro-max',NULL,'',NULL,NULL,NULL,55234.00,NULL,10,1,1,'asset/images/upload/product_1747296723_8952.jpg','2025-05-15 13:09:20','2025-05-15 16:12:03'),(2,1,'Samsung S25','samsung-s25',NULL,'',NULL,NULL,NULL,78123.00,NULL,5,1,1,'asset/images/upload/product_1747296637_3109.jpg','2025-05-15 13:22:07','2025-05-15 16:10:37');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `site_info`
--

DROP TABLE IF EXISTS `site_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `site_info` (
  `id` int NOT NULL AUTO_INCREMENT,
  `mission` text,
  `vision` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `site_info`
--

LOCK TABLES `site_info` WRITE;
/*!40000 ALTER TABLE `site_info` DISABLE KEYS */;
INSERT INTO `site_info` VALUES (1,'To provide affordable, high-quality pre-owned smartphones while promoting sustainable consumption and reducing electronic waste. We are committed to offering thoroughly tested devices with transparent pricing and exceptional customer service.','To become the most trusted marketplace for pre-owned smartphones, leading the way in sustainable technology consumption and setting new standards for quality assurance and customer satisfaction in the second-hand mobile industry.');
/*!40000 ALTER TABLE `site_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `site_settings`
--

DROP TABLE IF EXISTS `site_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `site_settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` text COLLATE utf8mb4_unicode_ci,
  `setting_group` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `setting_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
  `setting_label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_description` text COLLATE utf8mb4_unicode_ci,
  `is_public` tinyint(1) DEFAULT '0',
  `date_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `site_settings`
--

LOCK TABLES `site_settings` WRITE;
/*!40000 ALTER TABLE `site_settings` DISABLE KEYS */;
INSERT INTO `site_settings` VALUES (1,'site_name','just in case - 2nd Hand Phone Shop','general','text','Site Name','The name of your website',1,'2025-05-15 06:37:19'),(2,'site_description','Your trusted source for quality second-hand phones','general','textarea','Site Description','A brief description of your website',1,'2025-05-15 06:18:13'),(3,'contact_email','cindypacaldo2@gmail.com','general','email','Contact Email','Primary contact email address',1,'2025-05-15 08:36:25'),(4,'contact_phone','09668636061','general','text','Contact Phone','Primary contact phone number',1,'2025-05-15 08:36:25'),(5,'address','Unit 108 HRPV Building Rizal Avenue St. 5300 Puerto Princesa, Philippines','general','textarea','Business Address','Physical business address',1,'2025-05-15 08:33:07'),(6,'facebook_url','','social','url','Facebook URL','Your Facebook page URL',1,'2025-05-15 07:38:14'),(7,'instagram_url','','social','url','Instagram URL','Your Instagram profile URL',1,'2025-05-15 06:18:13'),(8,'twitter_url','','social','url','Twitter URL','Your Twitter profile URL',1,'2025-05-15 06:18:13'),(9,'currency_symbol','₱','shop','text','Currency Symbol','Currency symbol to display with prices',1,'2025-05-15 06:18:13'),(10,'tax_rate','12','shop','number','Tax Rate (%)','Default tax rate for products',0,'2025-05-15 06:18:13'),(11,'shipping_fee','100','shop','number','Default Shipping Fee','Default shipping fee for orders',0,'2025-05-15 06:18:13'),(12,'min_order_amount','500','shop','number','Minimum Order Amount','Minimum amount required for orders',0,'2025-05-15 06:18:13'),(13,'meta_title','2nd Phone Shop - Quality Second-hand Phones','seo','text','Meta Title','Default meta title for pages',1,'2025-05-15 06:18:13'),(14,'meta_description','Find quality second-hand phones at great prices','seo','textarea','Meta Description','Default meta description for pages',1,'2025-05-15 06:18:13'),(15,'meta_keywords','second hand phones, used phones, refurbished phones','seo','text','Meta Keywords','Default meta keywords for pages',1,'2025-05-15 06:18:13'),(16,'maintenance_mode','0','maintenance','boolean','Maintenance Mode','Enable/disable maintenance mode',0,'2025-05-15 06:19:28'),(17,'maintenance_message','We are currently performing maintenance. Please check back soon.','maintenance','textarea','Maintenance Message','Message to display during maintenance',1,'2025-05-15 06:18:13'),(18,'contact_address','PPRP+66Q, Puerto Princesa, Palawan','contact','textarea','Business Address','Enter your business address',0,'2025-05-15 07:34:28'),(19,'business_hours','{\"Monday\":\"9:00 AM - 5:00 PM\",\"Tuesday\":\"9:00 AM - 5:00 PM\",\"Wednesday\":\"9:00 AM - 5:00 PM\",\"Thursday\":\"9:00 AM - 5:00 PM\",\"Friday\":\"9:00 AM - 5:00 PM\",\"Saturday\":\"10:00 AM - 2:00 PM\",\"Sunday\":\"Closed\"}','general','textarea','Business Hours','Enter your business hours in JSON format',0,'2025-05-15 08:26:49'),(20,'contact_map','','contact','textarea','Google Maps Embed Code','Enter your Google Maps embed code',0,'2025-05-15 07:34:28'),(25,'contact_form_email','contact@example.com','contact','email','Contact Form Email','Email address where contact form submissions will be sent',0,'2025-05-15 07:30:57');
/*!40000 ALTER TABLE `site_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `middle_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Stored hashed password',
  `role` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'customer' COMMENT 'e.g., customer, admin',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `email_verified_at` datetime DEFAULT NULL,
  `last_login_at` datetime DEFAULT NULL,
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `mobile_number` (`mobile_number`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (2,'Ken','','Domingo','ken@gmail.com','09123456789','$2y$10$2Ux/qU26ecdqFI1kfyIj5OAsd7HU5OtFRUukR4nAiFM4ulf8QO5Xm','admin',1,NULL,'2025-05-15 16:01:17','2025-05-15 13:03:08','2025-05-15 16:01:17'),(3,'Ronald','','Capuno','ronald@gmail.com','09501057091','$2y$10$jYkkgwhhtESHumozVfWxreKStBmCVBODgJJYCB1BS0cHgmkB6N832','customer',1,NULL,'2025-05-15 15:52:58','2025-05-15 13:07:53','2025-05-15 15:52:58');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_addresses`
--

DROP TABLE IF EXISTS `user_addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_addresses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `address_type` enum('shipping','billing') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'shipping',
  `is_default` tinyint(1) DEFAULT '0',
  `recipient_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_line1` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address_line2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `state_province_region` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `postal_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_code` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ISO 3166-1 alpha-3 country code',
  `phone_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_instructions` text COLLATE utf8mb4_unicode_ci,
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_addresses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_addresses`
--

LOCK TABLES `user_addresses` WRITE;
/*!40000 ALTER TABLE `user_addresses` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_addresses` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-05-16 18:34:45
