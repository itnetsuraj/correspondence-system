-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: correspondence
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `correspondence`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `correspondence` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `correspondence`;

--
-- Table structure for table `activity_logs`
--

DROP TABLE IF EXISTS `activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `establishment` varchar(200) DEFAULT NULL,
  `activity` text DEFAULT NULL,
  `log_time` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_logs`
--

LOCK TABLES `activity_logs` WRITE;
/*!40000 ALTER TABLE `activity_logs` DISABLE KEYS */;
INSERT INTO `activity_logs` VALUES (26,'INOUT01','user','District and Sessions Court, Hingoli','User Login','2026-05-26 14:02:15'),(27,'admin','admin','All','User Login','2026-05-26 14:40:05'),(28,'admin','admin','All','Added Inward Entry - -','2026-05-26 14:55:47'),(29,'admin','admin','All','User Login','2026-05-26 15:19:00'),(30,'admin','admin','All','Credit ₹100 Establishment : Civil Court Junior Division, Hingoli','2026-05-26 15:20:24'),(31,'admin','admin','All','Added Inward Entry - 20','2026-05-26 15:20:45'),(32,'admin','admin','All','Added Outward : 20','2026-05-26 15:21:20'),(33,'admin','admin','All','Credit ₹50 Establishment : District and Sessions Court, Hingoli','2026-05-26 15:22:17'),(34,'admin','admin','All','Added Outward : 10','2026-05-26 15:27:18'),(35,'admin','admin','All','Added Outward : 4','2026-05-26 15:27:57'),(36,'admin','admin','All','Added Outward : 13','2026-05-26 15:31:32'),(37,'admin','admin','All','Added Outward : 2','2026-05-26 15:35:03'),(38,'INOUT01','user','District and Sessions Court, Hingoli','User Login','2026-05-26 15:38:06'),(39,'INOUT01','user','District and Sessions Court, Hingoli','User Login','2026-05-26 15:56:50'),(40,'admin','admin','All','User Login','2026-05-26 15:57:04'),(41,'admin','admin','All','Added Inward Entry - -','2026-05-26 15:58:51'),(42,'admin','admin','All','Added Outward : -','2026-05-26 16:08:51'),(43,'admin','admin','All','User Login','2026-05-26 16:29:38'),(44,'admin','admin','All','Added Outward : -','2026-05-26 16:30:43'),(45,'admin','admin','All','Added Outward : 1','2026-05-26 16:31:21'),(46,'admin','admin','All','User Login','2026-05-26 17:12:04'),(47,'admin','admin','All','User Login','2026-05-26 18:11:54'),(48,'admin','admin','All','Added Inward Entry - 15','2026-05-26 18:19:02'),(49,'admin','admin','All','Dispatch : 15','2026-05-26 18:19:27'),(50,'admin','admin','All','Added Inward Entry - 5','2026-05-26 18:21:18'),(51,'admin','admin','All','User Login','2026-05-28 11:14:27');
/*!40000 ALTER TABLE `activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `amount_transactions`
--

DROP TABLE IF EXISTS `amount_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `amount_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `amount` decimal(10,2) DEFAULT NULL,
  `type` varchar(30) DEFAULT NULL,
  `entry_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `establishment` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `amount_transactions`
--

LOCK TABLES `amount_transactions` WRITE;
/*!40000 ALTER TABLE `amount_transactions` DISABLE KEYS */;
INSERT INTO `amount_transactions` VALUES (11,100.00,'Credit','2026-05-26 09:50:24','2026-05-26 09:50:24','Civil Court Junior Division, Hingoli'),(12,50.00,'Credit','2026-05-26 09:52:17','2026-05-26 09:52:17','District and Sessions Court, Hingoli');
/*!40000 ALTER TABLE `amount_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `archive_log`
--

DROP TABLE IF EXISTS `archive_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `archive_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `archived_year` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `archive_log`
--

LOCK TABLES `archive_log` WRITE;
/*!40000 ALTER TABLE `archive_log` DISABLE KEYS */;
INSERT INTO `archive_log` VALUES (1,2025),(2,2026);
/*!40000 ALTER TABLE `archive_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dispatch`
--

DROP TABLE IF EXISTS `dispatch`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dispatch` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inward_id` int(11) DEFAULT NULL,
  `letter_no` varchar(100) DEFAULT NULL,
  `dispatch_qty` int(11) DEFAULT 1,
  `dispatch_date` date DEFAULT NULL,
  `establishment` varchar(100) DEFAULT NULL,
  `language` varchar(10) DEFAULT 'en',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dispatch`
--

LOCK TABLES `dispatch` WRITE;
/*!40000 ALTER TABLE `dispatch` DISABLE KEYS */;
INSERT INTO `dispatch` VALUES (1,101,'15',3,'2026-05-26','Civil Court Junior Division, Hingoli','en');
/*!40000 ALTER TABLE `dispatch` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `establishments`
--

DROP TABLE IF EXISTS `establishments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `establishments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `establishment_name` varchar(200) DEFAULT NULL,
  `inward_start_id` int(11) NOT NULL DEFAULT 1,
  `outward_start_id` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `establishments`
--

LOCK TABLES `establishments` WRITE;
/*!40000 ALTER TABLE `establishments` DISABLE KEYS */;
INSERT INTO `establishments` VALUES (1,'District and Sessions Court, Hingoli',2,202),(3,'Civil Court Senior Divison, Hingoli',1,1),(4,'Civil Court Junior Division, Hingoli',1,2);
/*!40000 ALTER TABLE `establishments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inward_archive`
--

DROP TABLE IF EXISTS `inward_archive`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inward_archive` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `letter_no` varchar(100) DEFAULT NULL,
  `received_from` varchar(255) DEFAULT NULL,
  `department_person` varchar(255) DEFAULT NULL,
  `subject` text DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `received_date` date DEFAULT NULL,
  `language` varchar(10) DEFAULT 'en',
  `status` varchar(20) DEFAULT 'Pending',
  `register_id` int(11) DEFAULT NULL,
  `establishment` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inward_archive`
--

LOCK TABLES `inward_archive` WRITE;
/*!40000 ALTER TABLE `inward_archive` DISABLE KEYS */;
/*!40000 ALTER TABLE `inward_archive` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inward_letters`
--

DROP TABLE IF EXISTS `inward_letters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inward_letters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `letter_no` varchar(100) DEFAULT NULL,
  `received_from` varchar(255) DEFAULT NULL,
  `department_person` varchar(255) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `subject` text DEFAULT NULL,
  `document_type` varchar(100) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `received_date` date DEFAULT NULL,
  `language` varchar(10) DEFAULT 'en',
  `status` varchar(20) DEFAULT 'Pending',
  `register_id` int(11) DEFAULT NULL,
  `establishment` varchar(100) DEFAULT NULL,
  `record_year` int(11) DEFAULT NULL,
  `other_document_type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inward_letters`
--

LOCK TABLES `inward_letters` WRITE;
/*!40000 ALTER TABLE `inward_letters` DISABLE KEYS */;
INSERT INTO `inward_letters` VALUES (4,'-','fg','g',2,'bxqbv','Notice','fds','2026-05-26','en','Pending',1,'District and Sessions Court, Hingoli',NULL,NULL);
/*!40000 ALTER TABLE `inward_letters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `outward_archive`
--

DROP TABLE IF EXISTS `outward_archive`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `outward_archive` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `letter_no` varchar(100) DEFAULT NULL,
  `sent_to` varchar(255) DEFAULT NULL,
  `department_person` varchar(255) DEFAULT NULL,
  `subject` text DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `sent_date` date DEFAULT NULL,
  `language` varchar(10) DEFAULT 'en',
  `status` varchar(20) DEFAULT 'Pending',
  `register_id` int(11) DEFAULT NULL,
  `postage_amount` decimal(10,2) DEFAULT NULL,
  `establishment` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `outward_archive`
--

LOCK TABLES `outward_archive` WRITE;
/*!40000 ALTER TABLE `outward_archive` DISABLE KEYS */;
/*!40000 ALTER TABLE `outward_archive` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `outward_balance`
--

DROP TABLE IF EXISTS `outward_balance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `outward_balance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `establishment` varchar(255) NOT NULL,
  `balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `outward_balance`
--

LOCK TABLES `outward_balance` WRITE;
/*!40000 ALTER TABLE `outward_balance` DISABLE KEYS */;
INSERT INTO `outward_balance` VALUES (36,'District and Sessions Court, Hingoli',10.00),(37,'Civil Court Junior Division, Hingoli',90.00),(38,'Civil Court Junior Division, Hingoli',90.00),(39,'Civil Court Junior Division, Hingoli',90.00),(40,'District and Sessions Court, Hingoli',10.00),(41,'District and Sessions Court, Hingoli',10.00),(42,'District and Sessions Court, Hingoli',10.00),(43,'District and Sessions Court, Hingoli',10.00),(44,'District and Sessions Court, Hingoli',10.00),(45,'District and Sessions Court, Hingoli',10.00),(46,'District and Sessions Court, Hingoli',10.00),(47,'Civil Court Junior Division, Hingoli',0.00),(48,'Civil Court Junior Division, Hingoli',0.00),(49,'Civil Court Junior Division, Hingoli',0.00),(50,'Civil Court Junior Division, Hingoli',0.00),(51,'District and Sessions Court, Hingoli',0.00);
/*!40000 ALTER TABLE `outward_balance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `outward_letters`
--

DROP TABLE IF EXISTS `outward_letters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `outward_letters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `letter_no` varchar(100) DEFAULT NULL,
  `sent_to` varchar(255) DEFAULT NULL,
  `department_person` varchar(255) DEFAULT NULL,
  `subject` text DEFAULT NULL,
  `document_type` varchar(100) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `sent_date` date DEFAULT NULL,
  `language` varchar(10) DEFAULT 'en',
  `status` varchar(20) DEFAULT 'Pending',
  `register_id` int(11) DEFAULT NULL,
  `postage_amount` decimal(10,2) DEFAULT NULL,
  `establishment` varchar(100) DEFAULT NULL,
  `record_year` int(11) DEFAULT NULL,
  `inward_ref` varchar(100) DEFAULT NULL,
  `dispatch` tinyint(1) DEFAULT 0,
  `dispatch_quantity` int(11) DEFAULT 1,
  `other_document_type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `outward_letters`
--

LOCK TABLES `outward_letters` WRITE;
/*!40000 ALTER TABLE `outward_letters` DISABLE KEYS */;
INSERT INTO `outward_letters` VALUES (8,'20','dsg','g','dsg','Confidential','g','2026-05-26','en','Pending',1,10.00,'Civil Court Junior Division, Hingoli',NULL,NULL,0,1,NULL),(9,'10','gs','gsd','gsfg','Notice','g','2026-05-26','en','Pending',1,NULL,'District and Sessions Court, Hingoli',NULL,NULL,0,1,NULL),(10,'4','gsd','ga','gdfsg','Notice','','2026-05-26','en','Pending',2,NULL,'District and Sessions Court, Hingoli',NULL,NULL,0,1,NULL),(11,'13','gs','sg','ga','Notice','','2026-05-26','en','Pending',3,NULL,'District and Sessions Court, Hingoli',NULL,NULL,0,1,NULL),(12,'2','gsdg','gdg','sddg','Notice','','2026-05-26','en','Pending',4,10.00,'District and Sessions Court, Hingoli',NULL,NULL,0,1,NULL),(13,'-','dgfFS','GKLL','NNGFD','Notice','D','2026-05-26','en','Pending',200,0.00,'District and Sessions Court, Hingoli',2026,NULL,0,1,NULL),(14,'1','FSD','FGD','gsd','Applications','','2026-05-26','en','Pending',201,30.00,'District and Sessions Court, Hingoli',2026,NULL,0,1,NULL);
/*!40000 ALTER TABLE `outward_letters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) DEFAULT NULL,
  `setting_value` varchar(100) DEFAULT NULL,
  `establishment` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` varchar(20) DEFAULT NULL,
  `admin_role` varchar(20) NOT NULL DEFAULT 'user',
  `establishment` varchar(100) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (9,'admin','$2y$10$Q.r7w1HCPZeUJTsee6jv3uwQu5Oatq.OSikuRG0vMkGSHmPVhFJl.',NULL,'admin','All','2026-05-28 07:44:27'),(10,'INOUT01','$2y$10$0dqRNIHgibiIY6foFcEHM.NJp601HxMHQQY5EqMDMVB8syEyMKazS',NULL,'user','District and Sessions Court, Hingoli','2026-05-26 12:26:50'),(11,'INOUT02','$2y$10$c5K5s1elp2zYt58ehxX1ouBM2gg9HUPZPDPHtDveU9Cq66O0pk3se',NULL,'user','Civil Court Senior Divison, Hingoli','2026-05-22 11:58:40'),(12,'INOUT03','$2y$10$pYjTefiJhAlkEfDrvAj/aOEQ4SXAKH0mGmE/Q.z59sSkLrPQFNEUi',NULL,'user','Civil Court Junior Division, Hingoli',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'correspondence'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-28 11:46:55
