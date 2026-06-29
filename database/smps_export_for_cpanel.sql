-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: localhost    Database: smps_local
-- ------------------------------------------------------
-- Server version	8.4.3

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `addresses`
--

DROP TABLE IF EXISTS `addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `addresses` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `label` varchar(50) DEFAULT 'Home',
  `full_name` varchar(100) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `address_line` varchar(255) NOT NULL,
  `landmark` varchar(150) DEFAULT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) DEFAULT 'Telangana',
  `pincode` varchar(10) NOT NULL,
  `is_default` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  CONSTRAINT `addresses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `addresses`
--

LOCK TABLES `addresses` WRITE;
/*!40000 ALTER TABLE `addresses` DISABLE KEYS */;
/*!40000 ALTER TABLE `addresses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_users`
--

DROP TABLE IF EXISTS `admin_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin_users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('superadmin','manager') DEFAULT 'manager',
  `is_active` tinyint(1) DEFAULT '1',
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_users`
--

LOCK TABLES `admin_users` WRITE;
/*!40000 ALTER TABLE `admin_users` DISABLE KEYS */;
INSERT INTO `admin_users` VALUES (1,'Store Owner','admin@srimanikanta.com','$2y$10$iDKeJPL7p.TE7mZLmOX5huW/3xQkDYVrFQ5WLjDscDN9Alumk1WfO','superadmin',1,'2026-06-20 12:51:17','2026-06-10 11:37:41'),(2,'Sri Manikanta Admin','admin@smps.com','$2y$12$pQuEB029FV27iUXopSKPqefScpDr8Js8v.oc5hX1lcWqJs3BCFxW6','superadmin',1,'2026-06-29 20:02:40','2026-06-18 22:06:55');
/*!40000 ALTER TABLE `admin_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `telugu` varchar(100) DEFAULT NULL,
  `emoji` varchar(10) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `sort_order` int DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'agarbatti','Agarbatti & Incense','à°…à°—à°°à±à°¬à°¤à±à°¤à°¿','ðŸª”','images/cat_agarbatti.png',1,1),(2,'camphor','Camphor (Kapoor)','à°•à°°à±à°ªà±‚à°°à°‚','ðŸ”¥','images/cat_diyas.png',2,1),(3,'kumkum','Kumkum & Haldi','à°•à±à°‚à°•à±à°® à°ªà°¸à±à°ªà±','ðŸŸ¡','images/cat_kumkum.png',3,1),(4,'oils','Pooja Oils & Ghee','à°¨à±‚à°¨à±† & à°¨à±†à°¯à±à°¯à°¿','ðŸ«™','images/cat_oils.png',4,1),(5,'diyas','Diyas & Lamps','à°¦à±€à°ªà°¾à°²à±','ðŸ•¯ï¸','images/cat_diyas.png',5,1),(6,'photos','God Photos & Frames','à°¦à±‡à°µà±à°¡à°¿ à°«à±‹à°Ÿà±‹à°²à±','ðŸ–¼ï¸','images/cat_idols.png',6,1),(7,'idols','Idols (Vigrahas)','à°µà°¿à°—à±à°°à°¹à°¾à°²à±','ðŸº','images/cat_idols.png',7,1),(8,'thali','Puja Thali & Vessels','à°ªà±‚à°œà°¾ à°ªà°¾à°¤à±à°°à°²à±','âš±ï¸','images/cat_thali.png',8,1),(9,'malas','Malas & Rosaries','à°®à°¾à°²à°²à±','ðŸ“¿','images/cat_malas.png',9,1),(10,'havan','Havan Samagri','à°¹à°µà°¨à± à°¸à°¾à°®à°—à±à°°à°¿','ðŸ”±','images/cat_festival.png',10,1),(11,'festivals','Festival Kits','à°ªà°‚à°¡à±à°— à°•à°¿à°Ÿà±à°²à±','ðŸŽ‰','images/cat_festival.png',11,1),(12,'wedding','Wedding Items','à°µà°¿à°µà°¾à°¹ à°¸à°¾à°®à°—à±à°°à°¿','ðŸ’','images/cat_wedding.png',12,1),(14,'dhoop-sticks','Dhoop Sticks','',NULL,NULL,99,1),(15,'more-and-other','More and other','',NULL,NULL,99,1);
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned DEFAULT NULL,
  `type` varchar(60) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text,
  `is_read` tinyint(1) DEFAULT '0',
  `data` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_unread` (`user_id`,`is_read`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (1,1,'order_placed','Order Placed Successfully! Ã°Å¸â„¢Â','Your order SMPS-2026-86216 has been placed. We\'ll confirm it shortly.',1,'{\"order_id\":1,\"order_number\":\"SMPS-2026-86216\"}','2026-06-16 13:40:03'),(2,1,'order_placed','Order Placed Successfully! Ã°Å¸â„¢Â','Your order SMPS-2026-68060 has been placed. We\'ll confirm it shortly.',1,'{\"order_id\":2,\"order_number\":\"SMPS-2026-68060\"}','2026-06-16 13:41:34'),(3,1,'order_placed','Order Placed Successfully! Ã°Å¸â„¢Â','Your order SMPS-2026-56413 has been placed. We\'ll confirm it shortly.',1,'{\"order_id\":3,\"order_number\":\"SMPS-2026-56413\"}','2026-06-16 13:43:02'),(4,1,'order_placed','Order Placed Successfully! Ã°Å¸â„¢Â','Your order SMPS-2026-43339 has been placed. We\'ll confirm it shortly.',1,'{\"order_id\":4,\"order_number\":\"SMPS-2026-43339\"}','2026-06-16 19:52:18'),(5,1,'order_placed','Order Placed Successfully! Ã°Å¸â„¢Â','Your order SMPS-2026-63584 has been placed. We\'ll confirm it shortly.',1,'{\"order_id\":5,\"order_number\":\"SMPS-2026-63584\"}','2026-06-16 19:52:20'),(6,1,'order_status','Order Status: Confirmed','Ã¢Å“â€¦ Great news! Your order SMPS-2026-63584 has been confirmed. We\'re preparing it for dispatch.',1,'{\"order_id\":5,\"order_number\":\"SMPS-2026-63584\",\"status\":\"confirmed\"}','2026-06-16 20:04:10'),(7,1,'order_status','Order Status: Confirmed','Ã¢Å“â€¦ Great news! Your order SMPS-2026-63584 has been confirmed. We\'re preparing it for dispatch.',1,'{\"order_id\":5,\"order_number\":\"SMPS-2026-63584\",\"status\":\"confirmed\"}','2026-06-16 20:04:13'),(8,1,'order_status','Order Status: Confirmed','Ã¢Å“â€¦ Great news! Your order SMPS-2026-63584 has been confirmed. We\'re preparing it for dispatch.',1,'{\"order_id\":5,\"order_number\":\"SMPS-2026-63584\",\"status\":\"confirmed\"}','2026-06-16 20:07:14'),(9,1,'order_status','Order Status: Confirmed','Ã¢Å“â€¦ Great news! Your order SMPS-2026-63584 has been confirmed. We\'re preparing it for dispatch.',1,'{\"order_id\":5,\"order_number\":\"SMPS-2026-63584\",\"status\":\"confirmed\"}','2026-06-16 20:07:16'),(10,1,'order_status','Order Status: Pending','Your order SMPS-2026-43339 status updated to: Pending.',1,'{\"order_id\":4,\"order_number\":\"SMPS-2026-43339\",\"status\":\"pending\"}','2026-06-16 20:16:03'),(11,1,'order_status','Order Status: Pending','Your order SMPS-2026-43339 status updated to: Pending.',1,'{\"order_id\":4,\"order_number\":\"SMPS-2026-43339\",\"status\":\"pending\"}','2026-06-16 20:16:05'),(12,1,'order_status','Order Status: Pending','Your order SMPS-2026-43339 status updated to: Pending.',1,'{\"order_id\":4,\"order_number\":\"SMPS-2026-43339\",\"status\":\"pending\"}','2026-06-16 20:16:07'),(13,1,'order_status','Order Status: Pending','Your order SMPS-2026-43339 status updated to: Pending.',1,'{\"order_id\":4,\"order_number\":\"SMPS-2026-43339\",\"status\":\"pending\"}','2026-06-16 20:16:09'),(14,1,'order_status','Order Status: Pending','Your order SMPS-2026-43339 status updated to: Pending.',1,'{\"order_id\":4,\"order_number\":\"SMPS-2026-43339\",\"status\":\"pending\"}','2026-06-16 20:16:11'),(15,1,'order_status','Order Status: Pending','Your order SMPS-2026-43339 status updated to: Pending.',1,'{\"order_id\":4,\"order_number\":\"SMPS-2026-43339\",\"status\":\"pending\"}','2026-06-16 20:16:13'),(16,1,'order_status','Order Status: Confirmed','Ã¢Å“â€¦ Great news! Your order SMPS-2026-43339 has been confirmed. We\'re preparing it for dispatch.',1,'{\"order_id\":4,\"order_number\":\"SMPS-2026-43339\",\"status\":\"confirmed\"}','2026-06-16 20:16:25'),(17,1,'order_status','Order Status: Confirmed','Ã¢Å“â€¦ Great news! Your order SMPS-2026-43339 has been confirmed. We\'re preparing it for dispatch.',1,'{\"order_id\":4,\"order_number\":\"SMPS-2026-43339\",\"status\":\"confirmed\"}','2026-06-16 20:16:28'),(18,1,'order_status','Order Status: Confirmed','Ã¢Å“â€¦ Great news! Your order SMPS-2026-43339 has been confirmed. We\'re preparing it for dispatch.',1,'{\"order_id\":4,\"order_number\":\"SMPS-2026-43339\",\"status\":\"confirmed\"}','2026-06-16 20:16:30'),(19,1,'order_status','Order Status: Confirmed','Ã¢Å“â€¦ Great news! Your order SMPS-2026-43339 has been confirmed. We\'re preparing it for dispatch.',1,'{\"order_id\":4,\"order_number\":\"SMPS-2026-43339\",\"status\":\"confirmed\"}','2026-06-16 20:16:32'),(20,1,'order_status','Order Status: Confirmed','Ã¢Å“â€¦ Great news! Your order SMPS-2026-43339 has been confirmed. We\'re preparing it for dispatch.',1,'{\"order_id\":4,\"order_number\":\"SMPS-2026-43339\",\"status\":\"confirmed\"}','2026-06-16 20:16:34'),(21,1,'order_status','Order Status: Confirmed','Ã¢Å“â€¦ Great news! Your order SMPS-2026-43339 has been confirmed. We\'re preparing it for dispatch.',1,'{\"order_id\":4,\"order_number\":\"SMPS-2026-43339\",\"status\":\"confirmed\"}','2026-06-16 20:16:36'),(22,1,'order_status','Order Status: Shipped','Ã°Å¸Å¡Å¡ Your order SMPS-2026-63584 has been shipped! You\'ll receive it in 2-3 days.',1,'{\"order_id\":5,\"order_number\":\"SMPS-2026-63584\",\"status\":\"shipped\"}','2026-06-16 20:55:06'),(23,1,'order_status','Order Status: Shipped','Ã°Å¸Å¡Å¡ Your order SMPS-2026-63584 has been shipped! You\'ll receive it in 2-3 days.',1,'{\"order_id\":5,\"order_number\":\"SMPS-2026-63584\",\"status\":\"shipped\"}','2026-06-16 20:55:08'),(24,1,'order_status','Order Status: Shipped','Ã°Å¸Å¡Å¡ Your order SMPS-2026-63584 has been shipped! You\'ll receive it in 2-3 days.',1,'{\"order_id\":5,\"order_number\":\"SMPS-2026-63584\",\"status\":\"shipped\"}','2026-06-16 20:55:11'),(25,1,'order_status','Order Status: Shipped','Ã°Å¸Å¡Å¡ Your order SMPS-2026-63584 has been shipped! You\'ll receive it in 2-3 days.',1,'{\"order_id\":5,\"order_number\":\"SMPS-2026-63584\",\"status\":\"shipped\"}','2026-06-16 20:55:13'),(26,1,'order_status','Order Status: Shipped','Ã°Å¸Å¡Å¡ Your order SMPS-2026-63584 has been shipped! You\'ll receive it in 2-3 days.',1,'{\"order_id\":5,\"order_number\":\"SMPS-2026-63584\",\"status\":\"shipped\"}','2026-06-16 20:55:15'),(27,1,'order_status','Order Status: Shipped','Ã°Å¸Å¡Å¡ Your order SMPS-2026-63584 has been shipped! You\'ll receive it in 2-3 days.',1,'{\"order_id\":5,\"order_number\":\"SMPS-2026-63584\",\"status\":\"shipped\"}','2026-06-16 20:55:17'),(28,1,'order_status','Order Status: Shipped','Ã°Å¸Å¡Å¡ Your order SMPS-2026-63584 has been shipped! You\'ll receive it in 2-3 days.',1,'{\"order_id\":5,\"order_number\":\"SMPS-2026-63584\",\"status\":\"shipped\"}','2026-06-16 20:55:19'),(29,1,'order_status','Order Status: Confirmed','Ã¢Å“â€¦ Great news! Your order SMPS-2026-63584 has been confirmed. We\'re preparing it for dispatch.',1,'{\"order_id\":5,\"order_number\":\"SMPS-2026-63584\",\"status\":\"confirmed\"}','2026-06-16 21:01:00'),(30,1,'order_status','Order Status: Confirmed','Ã¢Å“â€¦ Great news! Your order SMPS-2026-63584 has been confirmed. We\'re preparing it for dispatch.',1,'{\"order_id\":5,\"order_number\":\"SMPS-2026-63584\",\"status\":\"confirmed\"}','2026-06-16 21:01:02'),(31,1,'order_status','Order Status: Confirmed','Ã¢Å“â€¦ Great news! Your order SMPS-2026-63584 has been confirmed. We\'re preparing it for dispatch.',1,'{\"order_id\":5,\"order_number\":\"SMPS-2026-63584\",\"status\":\"confirmed\"}','2026-06-16 21:01:04'),(32,1,'order_status','Order Status: Confirmed','Ã¢Å“â€¦ Great news! Your order SMPS-2026-63584 has been confirmed. We\'re preparing it for dispatch.',1,'{\"order_id\":5,\"order_number\":\"SMPS-2026-63584\",\"status\":\"confirmed\"}','2026-06-16 21:01:06'),(33,1,'order_status','Order Status: Shipped','Ã°Å¸Å¡Å¡ Your order SMPS-2026-63584 has been shipped! You\'ll receive it in 2-3 days.',1,'{\"order_id\":5,\"order_number\":\"SMPS-2026-63584\",\"status\":\"shipped\"}','2026-06-16 21:01:37'),(34,1,'order_status','Order Status: Shipped','Ã°Å¸Å¡Å¡ Your order SMPS-2026-63584 has been shipped! You\'ll receive it in 2-3 days.',1,'{\"order_id\":5,\"order_number\":\"SMPS-2026-63584\",\"status\":\"shipped\"}','2026-06-16 21:01:39'),(35,1,'order_status','Order Status: Shipped','Ã°Å¸Å¡Å¡ Your order SMPS-2026-63584 has been shipped! You\'ll receive it in 2-3 days.',1,'{\"order_id\":5,\"order_number\":\"SMPS-2026-63584\",\"status\":\"shipped\"}','2026-06-16 21:01:41'),(36,1,'order_status','Order Status: Shipped','Ã°Å¸Å¡Å¡ Your order SMPS-2026-63584 has been shipped! You\'ll receive it in 2-3 days.',1,'{\"order_id\":5,\"order_number\":\"SMPS-2026-63584\",\"status\":\"shipped\"}','2026-06-16 21:01:43'),(37,1,'order_status','Order Status: Confirmed','Ã¢Å“â€¦ Great news! Your order SMPS-2026-63584 has been confirmed. We\'re preparing it for dispatch.',1,'{\"order_id\":5,\"order_number\":\"SMPS-2026-63584\",\"status\":\"confirmed\"}','2026-06-16 21:04:07'),(38,1,'order_status','Order Status: Confirmed','Ã¢Å“â€¦ Great news! Your order SMPS-2026-56413 has been confirmed. We\'re preparing it for dispatch.',1,'{\"order_id\":3,\"order_number\":\"SMPS-2026-56413\",\"status\":\"confirmed\"}','2026-06-16 21:06:11'),(39,1,'order_status','Order Status: Shipped','Ã°Å¸Å¡Å¡ Your order SMPS-2026-68060 has been shipped! You\'ll receive it in 2-3 days.',1,'{\"order_id\":2,\"order_number\":\"SMPS-2026-68060\",\"status\":\"shipped\"}','2026-06-16 21:06:29'),(40,1,'order_status','Order Status: Shipped','Ã°Å¸Å¡Å¡ Your order SMPS-2026-68060 has been shipped! You\'ll receive it in 2-3 days.',1,'{\"order_id\":2,\"order_number\":\"SMPS-2026-68060\",\"status\":\"shipped\"}','2026-06-16 21:06:31'),(41,1,'order_status','Order Status: Delivered','Ã°Å¸Å½â€° Your order SMPS-2026-68060 has been delivered. Thank you for shopping with us! Ã°Å¸â„¢Â',1,'{\"order_id\":2,\"order_number\":\"SMPS-2026-68060\",\"status\":\"delivered\"}','2026-06-16 21:06:54'),(42,1,'order_status','Order Status: Delivered','Ã°Å¸Å½â€° Your order SMPS-2026-68060 has been delivered. Thank you for shopping with us! Ã°Å¸â„¢Â',1,'{\"order_id\":2,\"order_number\":\"SMPS-2026-68060\",\"status\":\"delivered\"}','2026-06-16 21:06:57'),(43,1,'order_status','Order Status: Delivered','Ã°Å¸Å½â€° Your order SMPS-2026-63584 has been delivered. Thank you for shopping with us! Ã°Å¸â„¢Â',1,'{\"order_id\":5,\"order_number\":\"SMPS-2026-63584\",\"status\":\"delivered\"}','2026-06-16 21:12:26'),(44,1,'order_status','Order Status: Delivered','Ã°Å¸Å½â€° Your order SMPS-2026-63584 has been delivered. Thank you for shopping with us! Ã°Å¸â„¢Â',1,'{\"order_id\":5,\"order_number\":\"SMPS-2026-63584\",\"status\":\"delivered\"}','2026-06-16 21:12:28'),(45,1,'order_placed','Order Placed Successfully! Ã°Å¸â„¢Â','Your order SMPS-2026-89845 has been placed. We\'ll confirm it shortly.',1,'{\"order_id\":6,\"order_number\":\"SMPS-2026-89845\"}','2026-06-17 20:16:00'),(46,1,'order_status','Order Status: Delivered','Your order SMPS-2026-89845 has been delivered. Thank you for shopping with us!',1,'{\"order_id\":6,\"order_number\":\"SMPS-2026-89845\",\"status\":\"delivered\"}','2026-06-17 21:19:39'),(47,1,'order_status','Order Status: Confirmed','Great news! Your order SMPS-2026-89845 has been confirmed. We\'re preparing it for dispatch.',1,'{\"order_id\":6,\"order_number\":\"SMPS-2026-89845\",\"status\":\"confirmed\"}','2026-06-17 21:25:26'),(48,1,'order_status','Order Status: Confirmed','Great news! Your order SMPS-2026-89845 has been confirmed. We\'re preparing it for dispatch.',1,'{\"order_id\":6,\"order_number\":\"SMPS-2026-89845\",\"status\":\"confirmed\"}','2026-06-17 21:26:21'),(49,1,'order_status','Order Status: Confirmed','Great news! Your order SMPS-2026-89845 has been confirmed. We\'re preparing it for dispatch.',1,'{\"order_id\":6,\"order_number\":\"SMPS-2026-89845\",\"status\":\"confirmed\"}','2026-06-17 21:27:32'),(50,1,'order_status','Order Status: Confirmed','Great news! Your order SMPS-2026-89845 has been confirmed. We\'re preparing it for dispatch.',1,'{\"order_id\":6,\"order_number\":\"SMPS-2026-89845\",\"status\":\"confirmed\"}','2026-06-17 21:28:34'),(51,1,'order_status','Order Status: Confirmed','Great news! Your order SMPS-2026-89845 has been confirmed. We\'re preparing it for dispatch.',1,'{\"order_id\":6,\"order_number\":\"SMPS-2026-89845\",\"status\":\"confirmed\"}','2026-06-17 21:28:48'),(52,1,'order_status','Order Status: Confirmed','Great news! Your order SMPS-2026-89845 has been confirmed. We\'re preparing it for dispatch.',1,'{\"order_id\":6,\"order_number\":\"SMPS-2026-89845\",\"status\":\"confirmed\"}','2026-06-17 21:31:02'),(53,1,'order_status','Order Status: Confirmed','Great news! Your order SMPS-2026-89845 has been confirmed. We\'re preparing it for dispatch.',1,'{\"order_id\":6,\"order_number\":\"SMPS-2026-89845\",\"status\":\"confirmed\"}','2026-06-17 21:34:02'),(54,1,'order_status','Order Status: Confirmed','Great news! Your order SMPS-2026-89845 has been confirmed. We\'re preparing it for dispatch.',1,'{\"order_id\":6,\"order_number\":\"SMPS-2026-89845\",\"status\":\"confirmed\"}','2026-06-18 13:28:10'),(55,1,'order_status','Order Status: Confirmed','Great news! Your order SMPS-2026-89845 has been confirmed. We\'re preparing it for dispatch.',1,'{\"order_id\":6,\"order_number\":\"SMPS-2026-89845\",\"status\":\"confirmed\"}','2026-06-18 13:46:04'),(56,1,'order_status','Order Status: Confirmed','Great news! Your order SMPS-2026-89845 has been confirmed. We\'re preparing it for dispatch.',1,'{\"order_id\":6,\"order_number\":\"SMPS-2026-89845\",\"status\":\"confirmed\"}','2026-06-18 13:50:45'),(57,1,'order_placed','Order Placed Successfully!','Your order SMPS-2026-17104 has been placed. We\'ll confirm it shortly.',1,'{\"order_id\":7,\"order_number\":\"SMPS-2026-17104\"}','2026-06-18 19:33:40'),(58,1,'order_placed','Order Placed Successfully!','Your order SMPS-2026-71922 has been placed. We\'ll confirm it shortly.',1,'{\"order_id\":8,\"order_number\":\"SMPS-2026-71922\"}','2026-06-19 11:07:22'),(59,1,'order_status','Order Status: Confirmed','Great news! Your order SMPS-2026-71922 has been confirmed. We\'re preparing it for dispatch.',1,'{\"order_id\":8,\"order_number\":\"SMPS-2026-71922\",\"status\":\"confirmed\"}','2026-06-19 11:42:13'),(60,1,'order_status','Order Status: Delivered','Your order SMPS-2026-71922 has been delivered. Thank you for shopping with us!',1,'{\"order_id\":8,\"order_number\":\"SMPS-2026-71922\",\"status\":\"delivered\"}','2026-06-19 11:49:27'),(61,1,'order_status','Order Status: Delivered','Your order SMPS-2026-71922 has been delivered. Thank you for shopping with us!',1,'{\"order_id\":8,\"order_number\":\"SMPS-2026-71922\",\"status\":\"delivered\"}','2026-06-19 11:49:28'),(62,1,'order_placed','Order Placed Successfully!','Your order SMPS-2026-04583 has been placed. We\'ll confirm it shortly.',1,'{\"order_id\":9,\"order_number\":\"SMPS-2026-04583\"}','2026-06-19 12:02:45'),(63,1,'order_placed','Order Placed Successfully!','Your order SMPS-2026-97866 has been placed. We\'ll confirm it shortly.',1,'{\"order_id\":10,\"order_number\":\"SMPS-2026-97866\"}','2026-06-19 12:06:40'),(64,1,'order_status','Order Status: Confirmed','Great news! Your order SMPS-2026-97866 has been confirmed. We\'re preparing it for dispatch.',1,'{\"order_id\":10,\"order_number\":\"SMPS-2026-97866\",\"status\":\"confirmed\"}','2026-06-19 12:10:57'),(65,1,'order_status','Order Status: Confirmed','Great news! Your order SMPS-2026-97866 has been confirmed. We\'re preparing it for dispatch.',1,'{\"order_id\":10,\"order_number\":\"SMPS-2026-97866\",\"status\":\"confirmed\"}','2026-06-19 12:17:24'),(66,1,'order_status','Order Status: Confirmed','Great news! Your order SMPS-2026-04583 has been confirmed. We\'re preparing it for dispatch.',1,'{\"order_id\":9,\"order_number\":\"SMPS-2026-04583\",\"status\":\"confirmed\"}','2026-06-19 12:19:11'),(67,1,'order_status','Order Status: Confirmed','Great news! Your order SMPS-2026-89845 has been confirmed. We\'re preparing it for dispatch.',1,'{\"order_id\":6,\"order_number\":\"SMPS-2026-89845\",\"status\":\"confirmed\"}','2026-06-19 12:22:14'),(68,1,'order_status','Order Status: Confirmed','Great news! Your order SMPS-2026-89845 has been confirmed. We\'re preparing it for dispatch.',1,'{\"order_id\":6,\"order_number\":\"SMPS-2026-89845\",\"status\":\"confirmed\"}','2026-06-19 12:22:45'),(69,1,'order_status','Order Status: Delivered','Your order SMPS-2026-97866 has been delivered. Thank you for shopping with us!',1,'{\"order_id\":10,\"order_number\":\"SMPS-2026-97866\",\"status\":\"delivered\"}','2026-06-19 12:26:26'),(70,1,'order_status','Order Status: Delivered','Your order SMPS-2026-04583 has been delivered. Thank you for shopping with us!',1,'{\"order_id\":9,\"order_number\":\"SMPS-2026-04583\",\"status\":\"delivered\"}','2026-06-19 12:26:39'),(71,1,'order_status','Order Status: Confirmed','Great news! Your order SMPS-2026-17104 has been confirmed. We\'re preparing it for dispatch.',1,'{\"order_id\":7,\"order_number\":\"SMPS-2026-17104\",\"status\":\"confirmed\"}','2026-06-19 12:32:11'),(72,1,'order_status','Order Status: Delivered','Your order SMPS-2026-86216 has been delivered. Thank you for shopping with us!',1,'{\"order_id\":1,\"order_number\":\"SMPS-2026-86216\",\"status\":\"delivered\"}','2026-06-19 12:33:33'),(73,1,'order_status','Order Status: Delivered','Your order SMPS-2026-17104 has been delivered. Thank you for shopping with us!',1,'{\"order_id\":7,\"order_number\":\"SMPS-2026-17104\",\"status\":\"delivered\"}','2026-06-19 13:05:15'),(74,1,'order_status','Order Status: Confirmed','Great news! Your order SMPS-2026-89845 has been confirmed. We\'re preparing it for dispatch.',1,'{\"order_id\":6,\"order_number\":\"SMPS-2026-89845\",\"status\":\"confirmed\"}','2026-06-19 19:46:37'),(75,1,'order_placed','Order Placed Successfully!','Your order SMPS-2026-20765 has been placed. We\'ll confirm it shortly.',1,'{\"order_id\":11,\"order_number\":\"SMPS-2026-20765\"}','2026-06-20 12:36:02'),(76,1,'order_status','Order Status: Delivered','Your order SMPS-2026-20765 has been delivered. Thank you for shopping with us!',1,'{\"order_id\":11,\"order_number\":\"SMPS-2026-20765\",\"status\":\"delivered\"}','2026-06-20 12:46:06'),(77,1,'order_placed','Order Placed Successfully!','Your order SMPS-2026-20525 has been placed. We\'ll confirm it shortly.',0,'{\"order_id\":12,\"order_number\":\"SMPS-2026-20525\"}','2026-06-29 19:57:55');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int unsigned NOT NULL,
  `product_id` int unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `variant` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `qty` int NOT NULL DEFAULT '1',
  `image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `idx_order` (`order_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (1,1,36,'Vinayaka Chavithi Complete Kit','Basic',549.00,2,'images/1000090829.jpg'),(2,2,25,'Brass Ganesha Idol (4 inch)','2 inch',799.00,1,'images/1000090828.jpg'),(3,2,21,'Lord Ganesha Photo Frame','4Ã—6 inch',250.00,1,'images/1000090824.jpg'),(4,2,31,'Rudraksha Mala (108 beads)','Small Bead (6mm)',599.00,1,'images/1000090824.jpg'),(5,3,9,'Premium Red Kumkum','25g',25.00,1,'images/1000090832.jpg'),(6,3,36,'Vinayaka Chavithi Complete Kit','Basic',549.00,1,'images/1000090829.jpg'),(7,3,23,'Lord Venkateswara (Balaji) Frame','6Ã—8 inch',280.00,1,'images/1000090826.jpg'),(8,4,3,'Darshan White Stone Incense Sticks','Standard Pack',60.00,1,'images/1000090826.jpg'),(9,4,4,'Darshan Black Stone Incense Sticks','Standard Pack',60.00,1,'images/1000090827.jpg'),(10,4,2,'Balaji 100 Divine Agarbathi 4IN1','Standard Pack',70.00,1,'images/1000090825.jpg'),(11,5,3,'Darshan White Stone Incense Sticks','Standard Pack',60.00,1,'images/1000090826.jpg'),(12,5,4,'Darshan Black Stone Incense Sticks','Standard Pack',60.00,1,'images/1000090827.jpg'),(13,5,2,'Balaji 100 Divine Agarbathi 4IN1','Standard Pack',70.00,1,'images/1000090825.jpg'),(14,6,9,'Premium Red Kumkum','25g',25.00,3,'images/1000090832.jpg'),(15,6,3,'Darshan White Stone Incense Sticks','Standard Pack',60.00,2,'images/1000090826.jpg'),(16,6,5005,'Balaji Bindu Premium Incense Sticks','Standard Pouch',70.00,1,'images/1000090833.jpg'),(17,6,5001,'Ambica Durbar Bathi (Herbal)','Standard Pack',60.00,1,'images/1000090829.jpg'),(18,6,10,'Pure Haldi (Turmeric) Powder','50g',30.00,1,'images/1000090833.jpg'),(19,6,36,'Vinayaka Chavithi Complete Kit','Basic',549.00,1,'images/1000090829.jpg'),(20,6,23,'Lord Venkateswara (Balaji) Frame','6Ã—8 inch',280.00,1,'images/1000090826.jpg'),(21,7,36,'Vinayaka Chavithi Complete Kit','Basic',549.00,1,'images/1000090829.jpg'),(22,7,23,'Lord Venkateswara (Balaji) Frame','6Ã—8 inch',280.00,1,'images/1000090826.jpg'),(23,8,12,'Sindoor (Vermilion)','5g',20.00,1,'images/1000090825.jpg'),(24,8,10,'Pure Haldi (Turmeric) Powder','50g',30.00,1,'images/1000090833.jpg'),(25,8,9,'Premium Red Kumkum','25g',25.00,1,'images/1000090832.jpg'),(26,9,36,'Vinayaka Chavithi Complete Kit','Basic',549.00,1,'images/1000090829.jpg'),(27,9,23,'Lord Venkateswara (Balaji) Frame','6Ã—8 inch',280.00,1,'images/1000090826.jpg'),(28,9,9,'Premium Red Kumkum','25g',25.00,1,'images/1000090832.jpg'),(29,10,36,'Vinayaka Chavithi Complete Kit','Basic',549.00,1,'images/1000090829.jpg'),(30,11,11,'Kumkum & Haldi Combo Pack','50g+50g',55.00,1,'images/1000090824.jpg'),(31,11,25,'Brass Ganesha Idol (4 inch)','2 inch',799.00,1,'images/1000090828.jpg'),(32,12,5005,'Balaji Bindu Premium Incense Sticks','Standard Pouch',70.00,1,'images/1000090833.jpg'),(33,12,6,'Pure Camphor Tablets','10g',30.00,1,'images/1000090829.jpg'),(34,12,9,'Premium Red Kumkum','25g',25.00,3,'images/1000090832.jpg'),(35,12,36,'Vinayaka Chavithi Complete Kit','Basic',549.00,1,'images/1000090829.jpg');
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_number` varchar(25) NOT NULL,
  `user_id` int unsigned NOT NULL,
  `address_id` int unsigned DEFAULT NULL,
  `address_snapshot` text,
  `subtotal` decimal(10,2) NOT NULL,
  `delivery_charge` decimal(10,2) DEFAULT '0.00',
  `discount` decimal(10,2) DEFAULT '0.00',
  `total` decimal(10,2) NOT NULL,
  `payment_method` enum('cod','upi') NOT NULL DEFAULT 'cod',
  `payment_status` enum('pending','pending_verification','verifying','paid','failed','refunded') DEFAULT 'pending',
  `order_status` enum('pending','confirmed','processing','packed','shipped','delivered','cancelled') DEFAULT 'pending',
  `notes` text,
  `admin_notes` text,
  `cancelled_reason` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_number` (`order_number`),
  KEY `address_id` (`address_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_status` (`order_status`),
  KEY `idx_payment_status` (`payment_status`),
  KEY `idx_created` (`created_at`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,'SMPS-2026-86216',1,NULL,'{\"full_name\":\"Laggoni chethan sai\",\"mobile\":\"9110582086\",\"address_line\":\"e;lkn,\",\"landmark\":\"Madhurapuri\",\"city\":\"Hyderabad\",\"state\":\"Telangana\",\"pincode\":\"500659\"}',1098.00,0.00,0.00,1098.00,'cod','paid','delivered','l;mkj','',NULL,'2026-06-16 13:40:03','2026-06-19 12:33:33'),(2,'SMPS-2026-68060',1,NULL,'{\"full_name\":\"Laggoni chethan sai\",\"mobile\":\"9110582086\",\"address_line\":\"ughgxfdghjkl\",\"landmark\":\"Madhurapuri\",\"city\":\"Hyderabad\",\"state\":\"Telangana\",\"pincode\":\"500659\"}',1648.00,0.00,0.00,1648.00,'cod','pending','delivered','','',NULL,'2026-06-16 13:41:34','2026-06-16 21:06:54'),(3,'SMPS-2026-56413',1,NULL,'{\"full_name\":\"Laggoni chethan sai\",\"mobile\":\"9110582086\",\"address_line\":\"llhugy\",\"landmark\":\"Madhurapuri\",\"city\":\"Hyderabad\",\"state\":\"Telangana\",\"pincode\":\"500659\"}',854.00,0.00,0.00,854.00,'cod','pending','confirmed','knj','',NULL,'2026-06-16 13:43:02','2026-06-16 21:06:09'),(4,'SMPS-2026-43339',1,NULL,'{\"full_name\":\"Laggoni chethan sai\",\"mobile\":\"9110582086\",\"address_line\":\"Red Cross Road\",\"landmark\":\"Gaddiannaram\",\"city\":\"Hyderabad\",\"state\":\"Telangana\",\"pincode\":\"500035\"}',190.00,60.00,0.00,250.00,'cod','pending','confirmed','','',NULL,'2026-06-16 19:52:16','2026-06-16 20:16:34'),(5,'SMPS-2026-63584',1,NULL,'{\"full_name\":\"Laggoni chethan sai\",\"mobile\":\"9110582086\",\"address_line\":\"Red Cross Road\",\"landmark\":\"Gaddiannaram\",\"city\":\"Hyderabad\",\"state\":\"Telangana\",\"pincode\":\"500035\"}',190.00,60.00,0.00,250.00,'cod','pending','delivered','','',NULL,'2026-06-16 19:52:18','2026-06-16 21:12:26'),(6,'SMPS-2026-89845',1,NULL,'{\"full_name\":\"Laggoni chethan sai\",\"mobile\":\"9110582086\",\"address_line\":\"Red Cross Road\",\"landmark\":\"Gaddiannaram\",\"city\":\"Hyderabad\",\"state\":\"Telangana\",\"pincode\":\"500035\"}',1184.00,0.00,0.00,1184.00,'upi','paid','confirmed','UTR: 789654236598','',NULL,'2026-06-17 20:16:00','2026-06-19 19:46:37'),(7,'SMPS-2026-17104',1,NULL,'{\"full_name\":\"Laggoni chethan sai\",\"mobile\":\"9110582086\",\"address_line\":\"Red Cross Road\",\"landmark\":\"Gaddiannaram\",\"city\":\"Hyderabad\",\"state\":\"Telangana\",\"pincode\":\"500035\"}',829.00,0.00,0.00,829.00,'cod','paid','delivered','','',NULL,'2026-06-18 19:33:39','2026-06-19 13:05:15'),(8,'SMPS-2026-71922',1,NULL,'{\"full_name\":\"Laggoni chethan sai\",\"mobile\":\"9110582086\",\"address_line\":\"Red Cross Road\",\"landmark\":\"Gaddiannaram\",\"city\":\"Hyderabad\",\"state\":\"Telangana\",\"pincode\":\"500035\"}',75.00,0.00,0.00,75.00,'cod','pending','delivered','','',NULL,'2026-06-19 11:07:22','2026-06-19 11:49:28'),(9,'SMPS-2026-04583',1,NULL,'{\"full_name\":\"Laggoni chethan sai\",\"mobile\":\"9110582086\",\"address_line\":\"Red Cross Road\",\"landmark\":\"Gaddiannaram\",\"city\":\"Hyderabad\",\"state\":\"Telangana\",\"pincode\":\"500035\"}',854.00,0.00,0.00,854.00,'upi','paid','delivered','UTR: 856421256348','',NULL,'2026-06-19 12:02:45','2026-06-19 12:26:39'),(10,'SMPS-2026-97866',1,NULL,'{\"full_name\":\"Laggoni chethan sai\",\"mobile\":\"9110582086\",\"address_line\":\"Ward 23 Gaddiannaram\",\"landmark\":\"Ward 23 Gaddiannaram\",\"city\":\"Hyderabad\",\"state\":\"Telangana\",\"pincode\":\"500035\"}',549.00,0.00,0.00,549.00,'upi','paid','delivered','UTR: 856421256348','',NULL,'2026-06-19 12:06:40','2026-06-19 12:26:26'),(11,'SMPS-2026-20765',1,NULL,'{\"full_name\":\"Laggoni chethan sai\",\"mobile\":\"9110582086\",\"address_line\":\"Red Cross Road\",\"landmark\":\"Gaddiannaram\",\"city\":\"Hyderabad\",\"state\":\"Telangana\",\"pincode\":\"500035\"}',854.00,0.00,0.00,854.00,'cod','paid','delivered','','',NULL,'2026-06-20 12:36:02','2026-06-20 12:46:06'),(12,'SMPS-2026-20525',1,NULL,'{\"full_name\":\"Laggoni chethan sai\",\"mobile\":\"9110582086\",\"address_line\":\"flat mo - 409 ,shylavn shelter apartments  , dilshukh nagar\",\"landmark\":\"Gaddiannaram\",\"city\":\"Hyderabad\",\"state\":\"Telangana\",\"pincode\":\"500035\"}',724.00,0.00,0.00,724.00,'cod','pending','pending','',NULL,NULL,'2026-06-29 19:57:55','2026-06-29 19:57:55');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int unsigned NOT NULL,
  `utr_number` varchar(30) DEFAULT NULL,
  `upi_id_used` varchar(100) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `screenshot_path` varchar(255) DEFAULT NULL,
  `status` enum('pending','verifying','verified','failed') DEFAULT 'pending',
  `verified_by` int unsigned DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `admin_note` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_id` (`order_id`),
  KEY `idx_utr` (`utr_number`),
  KEY `idx_status` (`status`),
  CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES (1,6,'789654236598',NULL,1184.00,'uploads/screenshots/ss_1_1781707560_9ffe97c4.jpg','verified',NULL,NULL,NULL,'2026-06-17 20:16:00','2026-06-17 21:27:04'),(2,9,'856421256348',NULL,854.00,'uploads/screenshots/ss_1_1781850765_f217c104.jpg','verifying',NULL,NULL,NULL,'2026-06-19 12:02:45','2026-06-19 12:02:45'),(3,10,'856421256348',NULL,549.00,'uploads/screenshots/ss_1_1781851000_2a5097b8.jpg','verifying',NULL,NULL,NULL,'2026-06-19 12:06:40','2026-06-19 12:06:40');
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `telugu_name` varchar(255) DEFAULT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL,
  `original_price` decimal(10,2) DEFAULT NULL,
  `stock_qty` int DEFAULT '100',
  `sku` varchar(100) DEFAULT NULL,
  `images` text,
  `sizes` text,
  `tags` text,
  `badge` varchar(20) DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `is_deleted` tinyint(1) DEFAULT '0',
  `rating` decimal(3,1) DEFAULT '0.0',
  `review_count` int DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  UNIQUE KEY `sku` (`sku`),
  KEY `idx_category` (`category_id`),
  KEY `idx_slug` (`slug`),
  KEY `idx_featured` (`is_featured`),
  KEY `idx_active` (`is_active`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5006 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,1,'Zed Black 3-IN-1 Premium Agarbatti','à°œà±†à°¡à± à°¬à±à°²à°¾à°•à± 3-IN-1 à°…à°—à°°à±à°¬à°¤à±à°¤à°¿','-ed-lack-3-1-remium-garbatti','Zed Black Premium 3-IN-1 Agarbatti â€“ a triple fragrance blend in one pack. Long-lasting divine scent for daily puja. Brand ambassador MS Dhoni. Comes with a free matchbox inside.',60.00,80.00,100,NULL,'[\"images\\/1000090824.jpg\"]','[\"Standard Pack\"]','[\"zed black\",\"3-in-1\",\"premium\",\"daily puja\"]','popular',1,1,0,0.0,0,'2026-06-16 13:39:30','2026-06-16 13:39:30'),(2,1,'Balaji 100 Divine Agarbathi 4IN1','à°¬à°¾à°²à°¾à°œà±€ 100 à°¡à°¿à°µà±ˆà°¨à± à°…à°—à°°à±à°¬à°¤à±à°¤à°¿','-alaji-100-ivine-garbathi-4-1','Balaji 100 Divine Agarbathi â€“ Super Strong Premium Fragrance 4IN1 blend. Since 1957, Balaji brings you the finest quality incense. Premium Gold range for a truly divine experience.',70.00,90.00,98,NULL,'[\"images\\/1000090825.jpg\"]','[\"Standard Pack\"]','[\"balaji\",\"4-in-1\",\"super strong\",\"premium gold\"]','popular',1,1,0,0.0,0,'2026-06-16 13:39:30','2026-06-16 19:52:18'),(3,1,'Darshan White Stone Incense Sticks','à°¦à°°à±à°¶à°¨à± à°µà±ˆà°Ÿà± à°¸à±à°Ÿà±‹à°¨à± à°…à°—à°°à±à°¬à°¤à±à°¤à°¿','-arshan-hite-tone-ncense-ticks','Darshan White Stone Incense Sticks â€“ manufactured using enchanting perfumes in combination with natural oils to give power to your prayer and concentration for meditation. Elegant floral-patterned packaging.',60.00,80.00,96,NULL,'[\"images\\/1000090826.jpg\"]','[\"Standard Pack\"]','[\"darshan\",\"white stone\",\"meditation\",\"natural oils\"]','new',1,1,0,0.0,0,'2026-06-16 13:39:30','2026-06-17 20:16:00'),(4,1,'Darshan Black Stone Incense Sticks','à°¦à°°à±à°¶à°¨à± à°¬à±à°²à°¾à°•à± à°¸à±à°Ÿà±‹à°¨à± à°…à°—à°°à±à°¬à°¤à±à°¤à°¿','-arshan-lack-tone-ncense-ticks','Darshan Black Stone Incense Sticks â€“ a unique, unmatched fragrance (Ek Anokhi Khushboo). Dark premium packaging with gold mandala design. Perfect for meditation and evening prayers.',60.00,80.00,98,NULL,'[\"images\\/1000090827.jpg\"]','[\"Standard Pack\"]','[\"darshan\",\"black stone\",\"unique fragrance\",\"meditation\"]',NULL,1,1,0,0.0,0,'2026-06-16 13:39:30','2026-06-16 19:52:18'),(5,1,'Zed Black Luxury Fresh Pineapple Incense Sticks','à°œà±†à°¡à± à°¬à±à°²à°¾à°•à± à°²à°•à±à°œà°°à±€ à°«à±à°°à±†à°·à± à°ªà±ˆà°¨à°¾à°ªà°¿à°²à± à°…à°—à°°à±à°¬à°¤à±à°¤à°¿','-ed-lack-uxury-resh-ineapple-ncense-ticks','Zed Black Luxury Fresh Pineapple Premium Incense Sticks â€“ charcoal-free incense with a refreshing, exotic pineapple fragrance. Premium luxury range for a truly uplifting prayer experience.',70.00,90.00,100,NULL,'[\"images\\/1000090828.jpg\"]','[\"Standard Pack\"]','[\"zed black\",\"luxury\",\"pineapple\",\"charcoal free\"]',NULL,0,1,0,0.0,0,'2026-06-16 13:39:30','2026-06-16 13:39:30'),(6,2,'Pure Camphor Tablets','à°•à°°à±à°ªà±‚à°°à°‚','-ure-amphor-ablets','Pure white camphor tablets for aarti and puja. Burns completely without residue. Highest quality camphor with strong, clean fragrance. Essential for daily worship.',30.00,45.00,99,NULL,'[\"images\\/1000090829.jpg\"]','[\"10g\",\"50g\",\"100g\",\"250g\",\"500g\"]','[\"aarti\",\"daily puja\",\"essential\"]','popular',1,1,0,0.0,0,'2026-06-16 13:39:30','2026-06-29 19:57:55'),(7,2,'Camphor Powder (Loose)','à°•à°°à±à°ªà±‚à°°à°‚ à°ªà±Šà°¡à°¿','-amphor-owder-oose-','Fine camphor powder for havan and special rituals. Dissolves quickly and produces divine fragrance. Used in traditional Telugu and South Indian ceremonies.',55.00,70.00,100,NULL,'[\"images\\/1000090830.jpg\"]','[\"50g\",\"100g\",\"250g\"]','[\"havan\",\"powder\",\"ritual\"]',NULL,0,1,0,0.0,0,'2026-06-16 13:39:30','2026-06-16 13:39:30'),(8,2,'Camphor Aarti Lamp Refill','à°•à°°à±à°ªà±‚à°°à± à°†à°°à°¤à°¿','-amphor-arti-amp-efill','Premium camphor cubes specially shaped for brass aarti lamps. Consistent size, pure grade camphor. Pack includes 24 large cubes for extended puja sessions.',120.00,150.00,100,NULL,'[\"images\\/1000090831.jpg\"]','[\"Pack of 12\",\"Pack of 24\"]','[\"aarti lamp\",\"premium\",\"cubes\"]','new',0,1,0,0.0,0,'2026-06-16 13:39:30','2026-06-16 13:39:30'),(9,3,'Premium Red Kumkum','à°•à±à°‚à°•à±à°®','-remium-ed-umkum','Pure, bright red kumkum made from natural turmeric and lime. Fine texture, vibrant color, long-lasting. Essential for tilak, goddess worship and all religious ceremonies.',25.00,35.00,91,NULL,'[\"images\\/1000090832.jpg\"]','[\"25g\",\"50g\",\"100g\",\"250g\",\"500g\"]','[\"tilak\",\"goddess\",\"daily puja\"]','popular',1,1,0,0.0,0,'2026-06-16 13:39:30','2026-06-29 19:57:55'),(10,3,'Pure Haldi (Turmeric) Powder','à°ªà°¸à±à°ªà±','-ure-aldi-urmeric-owder','Bright yellow turmeric powder of highest purity. Used for tilak, haldi ceremony, warding off evil eye and skin care. Naturally sourced, pure grade quality.',30.00,42.00,98,NULL,'[\"images\\/1000090833.jpg\"]','[\"50g\",\"100g\",\"250g\",\"500g\",\"1kg\"]','[\"haldi ceremony\",\"tilak\",\"pure\"]',NULL,1,1,0,0.0,0,'2026-06-16 13:39:30','2026-06-19 11:07:22'),(11,3,'Kumkum & Haldi Combo Pack','à°•à±à°‚à°•à±à°® à°ªà°¸à±à°ªà± à°•à°¾à°‚à°¬à±‹','-umkum-aldi-ombo-ack','Value combo pack with premium red kumkum and pure haldi turmeric. Perfect for daily rituals, festivals and ceremonies. Sealed airtight for freshness.',55.00,80.00,99,NULL,'[\"images\\/1000090824.jpg\"]','[\"50g+50g\",\"100g+100g\",\"250g+250g\"]','[\"combo\",\"value pack\",\"festival\"]','sale',0,1,0,0.0,0,'2026-06-16 13:39:30','2026-06-20 12:36:02'),(12,3,'Sindoor (Vermilion)','à°¸à°¿à°‚à°§à±‚à°°à±','-indoor-ermilion-','Auspicious deep red sindoor for Goddess Lakshmi and Durga puja. Also used as mangalsutra sindoor. Natural ingredients, vibrant red color.',20.00,30.00,99,NULL,'[\"images\\/1000090825.jpg\"]','[\"5g\",\"10g\",\"25g\",\"50g\"]','[\"sindoor\",\"lakshmi\",\"marriage\"]',NULL,0,1,0,0.0,0,'2026-06-16 13:39:30','2026-06-19 11:07:22'),(13,4,'Pure Sesame (Til) Pooja Oil','à°¨à±à°µà±à°µà±à°² à°¨à±‚à°¨à±†','-ure-esame-il-ooja-il','Cold-pressed pure sesame oil for diya and lamp. Traditional pooja oil used for Lord Shiva and Saturn (Shani) worship. Natural, unrefined, pure quality.',120.00,160.00,100,NULL,'[\"images\\/1000090826.jpg\"]','[\"100ml\",\"250ml\",\"500ml\",\"1L\"]','[\"sesame oil\",\"til oil\",\"shani puja\"]',NULL,0,1,0,0.0,0,'2026-06-16 13:39:30','2026-06-16 13:39:30'),(14,4,'Pure Cow Ghee (Desi)','à°†à°µà± à°¨à±†à°¯à±à°¯à°¿','-ure-ow-hee-esi-','Pure A2 desi cow ghee for havan, lamp and prasad. Made from curd of indigenous cows using traditional Vedic method. Sacred, pure and highly aromatic.',450.00,580.00,100,NULL,'[\"images\\/1000090827.jpg\"]','[\"100g\",\"250g\",\"500g\",\"1kg\"]','[\"cow ghee\",\"havan\",\"prasad\"]','popular',1,1,0,0.0,0,'2026-06-16 13:39:30','2026-06-16 13:39:30'),(15,4,'Coconut Pooja Oil','à°•à±Šà°¬à±à°¬à°°à°¿ à°¨à±‚à°¨à±†','-oconut-ooja-il','Pure cold-pressed coconut oil for lamp and diya. Ideal for South Indian rituals and Vishnu puja. Long-burning, bright flame, pure natural quality.',90.00,120.00,100,NULL,'[\"images\\/1000090828.jpg\"]','[\"100ml\",\"250ml\",\"500ml\",\"1L\"]','[\"coconut oil\",\"vishnu puja\",\"lamp oil\"]',NULL,0,1,0,0.0,0,'2026-06-16 13:39:30','2026-06-16 13:39:30'),(16,4,'Jasmine Pooja Oil (Malli Nune)','à°®à°²à±à°²à°¿ à°¨à±‚à°¨à±†','-asmine-ooja-il-alli-une-','Fragrant jasmine-infused pooja oil used for deity abhishekam and lamp. Sweet natural jasmine fragrance, traditional formulation used in temples.',75.00,100.00,100,NULL,'[\"images\\/1000090829.jpg\"]','[\"50ml\",\"100ml\",\"250ml\"]','[\"jasmine\",\"abhishekam\",\"fragrant\"]',NULL,0,1,0,0.0,0,'2026-06-16 13:39:30','2026-06-16 13:39:30'),(17,5,'Brass Deepam (5-inch)','à°ªà°¿à°¤à°² à°¦à±€à°ªà°‚','-rass-eepam-5-inch-','Traditional 5-inch brass deepam for daily worship. Elegant design, heavy base, holds ghee or oil. Polished brass surface, tarnish resistant. Perfect for home puja room.',299.00,399.00,100,NULL,'[\"images\\/1000090830.jpg\"]','[\"Single\",\"Set of 2\",\"Set of 5\"]','[\"brass diya\",\"deepam\",\"daily puja\"]','popular',1,1,0,0.0,0,'2026-06-16 13:39:30','2026-06-16 13:39:30'),(18,5,'Clay Diyas (Mitti Diya)','à°®à°Ÿà±à°Ÿà°¿ à°¦à±€à°ªà°¾à°²à±','-lay-iyas-itti-iya-','Handmade earthen clay diyas for Diwali and all festivals. Pack of 20 small diyas. Made by traditional artisans. Eco-friendly, natural clay, auspicious orange color.',60.00,80.00,100,NULL,'[\"images\\/1000090831.jpg\"]','[\"Pack of 10\",\"Pack of 20\",\"Pack of 50\",\"Pack of 100\"]','[\"diwali\",\"clay diya\",\"eco-friendly\"]','popular',1,1,0,0.0,0,'2026-06-16 13:39:30','2026-06-16 13:39:30'),(19,5,'Pancha Mukhi Deepam','à°ªà°‚à°šà°®à±à°–à°¿ à°¦à±€à°ªà°‚','-ancha-ukhi-eepam','Five-faced brass deepam for special pujas and navaratri. Each face represents a different deity. Heavy antique-finish brass, premium craftsmanship.',450.00,599.00,100,NULL,'[\"images\\/1000090832.jpg\"]','[\"Medium\",\"Large\"]','[\"pancha mukhi\",\"navaratri\",\"special puja\"]','new',0,1,0,0.0,0,'2026-06-16 13:39:30','2026-06-16 13:39:30'),(20,5,'Electric Diya (LED)','à°Žà°²à±†à°•à±à°Ÿà±à°°à°¿à°•à± à°¦à±€à°ªà°‚','-lectric-iya-','LED electric diya with realistic flame flicker effect. Safe, flameless, energy efficient. Ideal for puja rooms where open flames are not practical.',199.00,299.00,100,NULL,'[\"images\\/1000090833.jpg\"]','[\"Single\",\"Set of 2\"]','[\"electric\",\"LED\",\"safe\"]',NULL,0,0,1,0.0,0,'2026-06-16 13:39:30','2026-06-16 14:27:24'),(21,6,'Lord Ganesha Photo Frame','à°—à°£à±‡à°¶ à°«à±‹à°Ÿà±‹ à°«à±à°°à±‡à°®à±','-ord-anesha-hoto-rame','Beautiful Lord Ganesha photo in premium wooden frame with glass. High-quality print with vibrant colors. Available in multiple sizes. Ideal for puja room, office and gifting.',250.00,350.00,99,NULL,'[\"images\\/1000090824.jpg\"]','[\"4\\u00d76 inch\",\"6\\u00d78 inch\",\"8\\u00d710 inch\",\"12\\u00d715 inch\"]','[\"ganesha\",\"photo frame\",\"gifting\"]','popular',1,1,0,0.0,0,'2026-06-16 13:39:30','2026-06-16 13:41:34'),(22,6,'Goddess Lakshmi Photo Frame','à°²à°•à±à°·à±à°®à±€à°¦à±‡à°µà°¿ à°«à±‹à°Ÿà±‹','-oddess-akshmi-hoto-rame','Auspicious Goddess Lakshmi photo with golden border frame. High-resolution divine image, premium print on canvas board. Brings prosperity and blessings to your home.',250.00,350.00,100,NULL,'[\"images\\/1000090825.jpg\"]','[\"4\\u00d76 inch\",\"6\\u00d78 inch\",\"8\\u00d710 inch\",\"12\\u00d715 inch\"]','[\"lakshmi\",\"prosperity\",\"home blessing\"]',NULL,1,1,0,0.0,0,'2026-06-16 13:39:30','2026-06-16 13:39:30'),(23,6,'Lord Venkateswara (Balaji) Frame','à°µà±‡à°‚à°•à°Ÿà±‡à°¶à±à°µà°° à°¸à±à°µà°¾à°®à°¿','-ord-enkateswara-alaji-rame','Tirupati Balaji Venkateswara Swami photo frame with traditional design. Special Telugu deity, brass-finished border, premium quality print, auspicious gift.',280.00,380.00,96,NULL,'[\"images\\/1000090826.jpg\"]','[\"6\\u00d78 inch\",\"8\\u00d710 inch\",\"12\\u00d715 inch\",\"18\\u00d724 inch\"]','[\"tirupati\",\"balaji\",\"telugu deity\"]','popular',1,1,0,0.0,0,'2026-06-16 13:39:30','2026-06-19 12:02:45'),(24,6,'Navgraha (9 Planets) Frame','à°¨à°µà°—à±à°°à°¹ à°¦à±‡à°µà°¤à°²à±','-avgraha-9-lanets-rame','All nine planet deities in one beautiful frame. Essential for navgraha puja and home altar. Premium print, gold border frame.',350.00,480.00,100,NULL,'[\"images\\/1000090827.jpg\"]','[\"8\\u00d710 inch\",\"12\\u00d715 inch\"]','[\"navgraha\",\"nine planets\",\"special puja\"]',NULL,0,1,0,0.0,0,'2026-06-16 13:39:30','2026-06-16 13:39:30'),(25,7,'Brass Ganesha Idol (4 inch)','à°—à°£à±‡à°¶ à°µà°¿à°—à±à°°à°¹à°‚','-rass-anesha-dol-4-inch-','Hand-crafted pure brass Lord Ganesha idol, 4-inch seated Ganesha. Intricate detailing, smooth finish, auspicious idol for home temple. Ideal for gifting and installation.',799.00,1099.00,98,NULL,'[\"images\\/1000090828.jpg\"]','[\"2 inch\",\"4 inch\",\"6 inch\",\"8 inch\"]','[\"ganesha\",\"brass idol\",\"handcrafted\"]','popular',1,1,0,0.0,0,'2026-06-16 13:39:30','2026-06-20 12:36:02'),(26,7,'Lakshmi Idol (Silver Plated)','à°²à°•à±à°·à±à°®à±€à°¦à±‡à°µà°¿ à°µà°¿à°—à±à°°à°¹à°‚','-akshmi-dol-ilver-lated-','Beautiful silver-plated Goddess Lakshmi idol in standing posture. Exquisite craftsmanship, pure silver coating, elegant finish. Brings divine blessings and prosperity.',1299.00,1699.00,100,NULL,'[\"images\\/1000090829.jpg\"]','[\"3 inch\",\"5 inch\",\"7 inch\"]','[\"lakshmi\",\"silver plated\",\"gifting\"]','new',1,1,0,0.0,0,'2026-06-16 13:39:30','2026-06-16 13:39:30'),(27,7,'Saraswati Brass Idol','à°¸à°°à°¸à±à°µà°¤à°¿ à°µà°¿à°—à±à°°à°¹à°‚','-araswati-rass-dol','Goddess Saraswati brass idol with Veena (musical instrument). Perfect for Saraswati Puja and Navratri. Fine craftsmanship, antique brass finish.',899.00,1200.00,100,NULL,'[\"images\\/1000090830.jpg\"]','[\"4 inch\",\"6 inch\",\"8 inch\"]','[\"saraswati\",\"navratri\",\"education\"]',NULL,0,1,0,0.0,0,'2026-06-16 13:39:30','2026-06-16 13:39:30'),(28,8,'Brass Puja Thali Set','à°ªà±‚à°œà°¾ à°ªà°³à±à°³à±†à°‚ à°¸à±†à°Ÿà±','-rass-uja-hali-et','Complete 7-piece brass puja thali set includes: thali, diya, bell, incense holder, kumkum holder, haldi holder and flower bowl. Premium engraved design, gift-ready packaging.',699.00,950.00,100,NULL,'[\"images\\/1000090831.jpg\"]','[\"Small (7\\\")\",\"Medium (9\\\")\",\"Large (11\\\")\"]','[\"thali set\",\"brass\",\"gifting\",\"complete set\"]','popular',1,1,0,0.0,0,'2026-06-16 13:39:30','2026-06-16 13:39:30'),(29,8,'Brass Kalash (Sacred Pot)','à°•à°²à°¶à°‚','-rass-alash-acred-ot-','Traditional brass kalash for Gruhapravesam, marriage and all major pujas. With coconut holder top, engraved patterns. Pure brass, heavy and durable.',399.00,550.00,100,NULL,'[\"images\\/1000090832.jpg\"]','[\"Small\",\"Medium\",\"Large\"]','[\"kalash\",\"gruhapravesam\",\"wedding\"]',NULL,0,1,0,0.0,0,'2026-06-16 13:39:30','2026-06-16 13:39:30'),(30,8,'Panchapatra & Uddhrani Set','à°ªà°‚à°šà°ªà°¾à°¤à±à°°','-anchapatra-ddhrani-et','Pure brass Panchapatra (water pot) with Uddhrani (spoon) for achamana during puja. Traditional design, easy to hold, polished finish.',299.00,420.00,100,NULL,'[\"images\\/1000090833.jpg\"]','[\"Standard\",\"Large\"]','[\"panchapatra\",\"achamana\",\"ritual\"]',NULL,0,1,0,0.0,0,'2026-06-16 13:39:30','2026-06-16 13:39:30'),(31,9,'Rudraksha Mala (108 beads)','à°°à±à°¦à±à°°à°¾à°•à±à°· à°®à°¾à°²','-udraksha-ala-108-beads-','Authentic 5-mukhi Rudraksha mala with 108 beads for japa and meditation. Energized and blessed. Genuine Indonesian rudraksha, knotted between each bead, silver guru bead.',599.00,850.00,99,NULL,'[\"images\\/1000090824.jpg\"]','[\"Small Bead (6mm)\",\"Medium Bead (8mm)\",\"Large Bead (10mm)\"]','[\"rudraksha\",\"japa\",\"meditation\",\"shiva\"]','popular',1,1,0,0.0,0,'2026-06-16 13:39:30','2026-06-16 13:41:34'),(32,9,'Tulsi Mala (Holy Basil)','à°¤à±à°²à°¸à°¿ à°®à°¾à°²','-ulsi-ala-oly-asil-','Sacred Tulsi mala for Vishnu and Krishna japa. Made from genuine Vrindavan Tulsi beads. 108 + 1 beads, smooth finish, auspicious for daily chanting.',150.00,220.00,100,NULL,'[\"images\\/1000090825.jpg\"]','[\"Standard (108 beads)\"]','[\"tulsi\",\"krishna\",\"vishnu\",\"japa\"]',NULL,0,1,0,0.0,0,'2026-06-16 13:39:30','2026-06-16 13:39:30'),(33,9,'Marigold Flower Garland','à°¬à°‚à°¤à°¿ à°ªà±‚à°² à°®à°¾à°²','-arigold-lower-arland','Artificial marigold flower garland for deity decoration. Premium quality silk-finish flowers, vibrant orange/yellow. Reusable, long-lasting, no maintenance required.',50.00,70.00,100,NULL,'[\"images\\/1000090826.jpg\"]','[\"2 ft\",\"4 ft\",\"6 ft\"]','[\"marigold\",\"decoration\",\"garland\"]',NULL,0,1,0,0.0,0,'2026-06-16 13:39:30','2026-06-16 13:39:30'),(34,10,'Havan Samagri Mix (Premium)','à°¹à°µà°¨à± à°¸à°¾à°®à°—à±à°°à°¿','-avan-amagri-ix-remium-','Premium blend of 51 sacred herbs and materials for havan/homam. Includes sandalwood, ghee-soaked cotton wicks, guggul, camphor, and more. Rich fragrance, pure ingredients.',199.00,280.00,100,NULL,'[\"images\\/1000090827.jpg\"]','[{\"name\":\"100g\",\"price\":199,\"original_price\":280,\"stock\":100,\"image\":\"\"},{\"name\":\"250g\",\"price\":199,\"original_price\":280,\"stock\":100,\"image\":\"\"},{\"name\":\"500g\",\"price\":199,\"original_price\":280,\"stock\":100,\"image\":\"\"},{\"name\":\"1kg\",\"price\":199,\"original_price\":280,\"stock\":100,\"image\":\"\"}]','[]','popular',1,1,0,0.0,0,'2026-06-16 13:39:30','2026-06-19 21:07:29'),(35,10,'Copper Havan Kund','à°¹à°µà°¨à± à°•à±à°‚à°¡à±','-opper-avan-und','Pure copper havan kund for performing Agni puja and homam at home. Traditional pyramid shape, durable copper, easy to clean, comes with stand.',899.00,1299.00,100,NULL,'[\"images\\/1000090828.jpg\"]','[{\"name\":\"Small (6\",\"price\":899,\"original_price\":1299,\"stock\":100,\"image\":\"\"},{\"name\":\"Medium (9\",\"price\":899,\"original_price\":1299,\"stock\":100,\"image\":\"\"},{\"name\":\"Large (12\",\"price\":899,\"original_price\":1299,\"stock\":100,\"image\":\"\"}]','[]',NULL,0,1,0,0.0,0,'2026-06-16 13:39:30','2026-06-19 21:07:39'),(36,11,'Vinayaka Chavithi Complete Kit','à°µà°¿à°¨à°¾à°¯à°• à°šà°µà°¿à°¤à°¿ à°•à°¿à°Ÿà±','-inayaka-havithi-omplete-it','All-in-one Ganesh Chaturthi kit: includes modak plate, 21-durva grass, red thread (janeu), dhoop, incense, kumkum, haldi, flowers, coconut and complete puja booklet in Telugu & English.',549.00,799.00,92,NULL,'[\"images\\/1000090829.jpg\"]','[\"Basic\",\"Premium\",\"Deluxe\"]','[\"ganesh chaturthi\",\"complete kit\",\"festival\"]','popular',1,1,0,0.0,0,'2026-06-16 13:39:30','2026-06-29 19:57:55'),(37,11,'Diwali Puja Complete Kit','à°¦à±€à°ªà°¾à°µà°³à°¿ à°ªà±‚à°œà°¾ à°•à°¿à°Ÿà±','-iwali-uja-omplete-it','Complete Diwali puja kit with: clay diyas (set of 20), camphor, Lakshmi idol, rangoli colors, kumkum, haldi, dhoop, flowers, mauli thread and puja booklet.',649.00,950.00,100,NULL,'[\"images\\/1000090830.jpg\"]','[\"Standard\",\"Premium\"]','[\"diwali\",\"lakshmi puja\",\"complete kit\"]','popular',1,1,0,0.0,0,'2026-06-16 13:39:30','2026-06-16 13:39:30'),(38,11,'Ugadi Puja Kit','à°‰à°—à°¾à°¦à°¿ à°ªà±‚à°œà°¾ à°•à°¿à°Ÿà±','-gadi-uja-it','Complete Telugu New Year (Ugadi) kit with neem flowers, jaggery, mango pieces, tamarind, chilli and all 6 rasa (taste) items plus puja essentials.',349.00,499.00,100,NULL,'[\"images\\/1000090831.jpg\"]','[\"Small\",\"Family Pack\"]','[\"ugadi\",\"telugu new year\",\"festival\"]','new',0,1,0,0.0,0,'2026-06-16 13:39:30','2026-06-16 13:39:30'),(39,12,'Telugu Wedding Samagri Kit','à°µà°¿à°µà°¾à°¹ à°¸à°¾à°®à°—à±à°°à°¿ à°•à°¿à°Ÿà±','-elugu-edding-amagri-it','Complete Telugu Brahmin wedding puja kit. Includes: coconut, beetle leaves & nuts, mango leaves, sacred thread, turmeric pieces, bangles, sacred cloth, kankanam thread, akshat, and all ritual items as per Telugu Shastras.',1499.00,2200.00,100,NULL,'[\"images\\/1000090824.jpg\"]','[\"Basic\",\"Complete\",\"Premium Deluxe\"]','[\"wedding\",\"vivah\",\"complete\",\"telugu\"]','popular',1,1,0,0.0,0,'2026-06-16 13:39:30','2026-06-16 13:39:30'),(40,12,'Kankana Dhara Thread Set','à°•à°‚à°•à°£ à°§à°¾à°°à°£ à°¸à±†à°Ÿà±','-ankana-hara-hread-et','Sacred kankanam wrist thread for bride and groom. Yellow silk thread with turmeric, janeu sacred thread included. Traditional Telugu wedding ritual item.',299.00,420.00,100,NULL,'[\"images\\/1000090825.jpg\"]','[\"Standard Pack (2 sets)\",\"Family Pack (5 sets)\"]','[\"kankanam\",\"wedding\",\"sacred thread\"]',NULL,0,1,0,0.0,0,'2026-06-16 13:39:30','2026-06-16 13:39:30'),(41,12,'Mangalsutra Thread (Dhaarana)','à°®à°‚à°—à°³à°¸à±‚à°¤à±à°°à°‚ à°¦à°¾à°°à°‚','-angalsutra-hread-haarana-','Traditional yellow mangalsutra dhaaranu thread with turmeric powder and kumkum. Pure cotton, sacred yellow thread for wedding and Varalakshmi puja.',150.00,220.00,100,NULL,'[\"images\\/1000090826.jpg\"]','[\"Standard\",\"With Pendant Space\"]','[\"mangalsutra\",\"wedding\",\"auspicious\"]',NULL,0,1,0,0.0,0,'2026-06-16 13:39:30','2026-06-16 13:39:30'),(5001,1,'Ambica Durbar Bathi (Herbal)','à°…à°‚à°¬à°¿à°•à°¾ à°¦à°°à±à°¬à°¾à°°à± à°¬à°¤à±à°¤à°¿','-mbica-urbar-athi-erbal-','Ambica Durbar Bathi â€“ India\'s only Herbal Durbar Bathi. Hand rolled in India using a mix of 63 herbs. By ACP Industries Ltd., Eluru. A traditional herbal incense loved for generations.',60.00,75.00,99,NULL,'[\"images\\/1000090829.jpg\"]','[\"Standard Pack\"]','[\"ambica\",\"durbar\",\"herbal\",\"63 herbs\",\"hand rolled\"]','popular',1,1,0,0.0,0,'2026-06-16 13:39:30','2026-06-17 20:16:00'),(5002,1,'Ambica Durbar Bathi 145g','à°…à°‚à°¬à°¿à°•à°¾ à°¦à°°à±à°¬à°¾à°°à± à°¬à°¤à±à°¤à°¿ 145à°—à±à°°à°¾','-mbica-urbar-athi-145g','Ambica Durbar Bathi 145g â€“ India\'s only Herbal Durbar Bathi in a larger 145g pack. Hand rolled using a traditional blend of 63 herbs for a long-lasting, divine fragrance.',70.00,90.00,100,NULL,'[\"images\\/1000090830.jpg\"]','[\"145g Pack\"]','[\"ambica\",\"durbar\",\"herbal\",\"145g\",\"large pack\"]',NULL,0,1,0,0.0,0,'2026-06-16 13:39:30','2026-06-16 13:39:30'),(5003,1,'Ambica Durbar Bathi 75g','à°…à°‚à°¬à°¿à°•à°¾ à°¦à°°à±à°¬à°¾à°°à± à°¬à°¤à±à°¤à°¿ 75à°—à±à°°à°¾','-mbica-urbar-athi-75g','Ambica Durbar Bathi 75g â€“ India\'s only Herbal Durbar Bathi. Hand rolled with 63 herbs blend. Compact 75g pack ideal for regular daily use.',60.00,75.00,100,NULL,'[\"images\\/1000090831.jpg\"]','[\"75g Pack\"]','[\"ambica\",\"durbar\",\"herbal\",\"75g\"]',NULL,0,1,0,0.0,0,'2026-06-16 13:39:30','2026-06-16 13:39:30'),(5004,1,'Ambica Durbar Bathi New Pack','à°…à°‚à°¬à°¿à°•à°¾ à°¦à°°à±à°¬à°¾à°°à± à°¬à°¤à±à°¤à°¿ à°¨à±à°¯à±‚ à°ªà±à°¯à°¾à°•à±','-mbica-urbar-athi-ew-ack','Ambica Durbar Bathi New Pack â€“ India&#039;s only Herbal Durbar Bathi in a fresh new packaging. Hand rolled blend of 63 herbs. A new look for the same trusted traditional quality.',70.00,85.00,100,NULL,'[\"images\\/1000090832.jpg\"]','[{\"name\":\"New Pack\",\"price\":70,\"original_price\":85,\"stock\":100,\"image\":\"\"}]','[]','new',0,1,0,0.0,0,'2026-06-16 13:39:30','2026-06-19 21:06:41'),(5005,1,'Balaji Bindu Premium Incense Sticks','à°¬à°¾à°²à°¾à°œà±€ à°¬à°¿à°‚à°¦à± à°ªà±à°°à±€à°®à°¿à°¯à°‚ à°…à°—à°°à±à°¬à°¤à±à°¤à°¿','-alaji-indu-remium-ncense-ticks','Balaji Bindu Premium Incense Sticks â€“ by Balaji Since 1957. Zipper-lock pouch for extra freshness. Premium quality incense for a divine, long-lasting fragrance experience during pooja.',70.00,90.00,98,NULL,'[\"images\\/1000090833.jpg\"]','[\"Standard Pouch\"]','[\"balaji\",\"bindu\",\"premium\",\"zipper lock\",\"fresh\"]','popular',1,1,0,0.0,0,'2026-06-16 13:39:30','2026-06-29 19:57:55');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rate_limits`
--

DROP TABLE IF EXISTS `rate_limits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rate_limits` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `identifier` varchar(100) NOT NULL,
  `action` varchar(60) NOT NULL,
  `attempts` int DEFAULT '1',
  `blocked_until` datetime DEFAULT NULL,
  `last_attempt` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_identifier_action` (`identifier`,`action`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rate_limits`
--

LOCK TABLES `rate_limits` WRITE;
/*!40000 ALTER TABLE `rate_limits` DISABLE KEYS */;
INSERT INTO `rate_limits` VALUES (19,'::1:9110582086','forgot_password',1,NULL,'2026-06-29 20:42:46'),(20,'::1:chethansailaggoni@gmail.com','forgot_password',2,NULL,'2026-06-29 20:48:36');
/*!40000 ALTER TABLE `rate_limits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `is_verified` tinyint(1) DEFAULT '0',
  `otp_code` varchar(10) DEFAULT NULL,
  `otp_expires` datetime DEFAULT NULL,
  `otp_attempts` tinyint DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mobile` (`mobile`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_mobile` (`mobile`),
  KEY `idx_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Laggoni chethan sai','9110582086','chethansailaggoni@gmail.com','$2y$12$cHZKzngLS5etEcumnbzLF.mvd1SqeFq//eNzhC04FGYkfApmgVtW.',1,'503129','2026-06-29 15:28:36',0,1,'2026-06-10 20:16:56','2026-06-29 20:48:36'),(2,'Test User','9876543210','test@example.com','$2y$12$9mmuyD/.QhWhc.xehoIBbeIlEMwMa8fXoZT3bpHLuv9.5mm5QxKmW',1,NULL,NULL,0,1,'2026-06-12 23:59:20','2026-06-12 23:59:20');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-29 20:52:30
