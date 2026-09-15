-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: la_ranita_admin
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
-- Table structure for table `account_movements`
--

DROP TABLE IF EXISTS `account_movements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `account_movements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `functional_unit_id` bigint(20) unsigned NOT NULL,
  `type` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `balance_after` decimal(15,2) NOT NULL,
  `description` varchar(255) NOT NULL,
  `related_model_type` varchar(255) DEFAULT NULL,
  `related_model_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `account_movements_functional_unit_id_foreign` (`functional_unit_id`),
  KEY `account_movements_related_model_type_related_model_id_index` (`related_model_type`,`related_model_id`),
  CONSTRAINT `account_movements_functional_unit_id_foreign` FOREIGN KEY (`functional_unit_id`) REFERENCES `functional_units` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `account_movements`
--

LOCK TABLES `account_movements` WRITE;
/*!40000 ALTER TABLE `account_movements` DISABLE KEYS */;
/*!40000 ALTER TABLE `account_movements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  `old_values` longtext DEFAULT NULL,
  `new_values` longtext DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `audit_logs_user_id_foreign` (`user_id`),
  KEY `audit_logs_model_type_model_id_index` (`model_type`,`model_id`),
  CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `billing_periods`
--

DROP TABLE IF EXISTS `billing_periods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `billing_periods` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `period` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `billing_periods_period_unique` (`period`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `billing_periods`
--

LOCK TABLES `billing_periods` WRITE;
/*!40000 ALTER TABLE `billing_periods` DISABLE KEYS */;
INSERT INTO `billing_periods` VALUES (1,'2026-08','2026-08-01','2026-08-31','draft','2026-08-08 19:35:19','2026-08-08 19:35:19'),(2,'2026-09','2026-09-01','2026-09-30','draft','2026-09-15 20:34:57','2026-09-15 20:34:57');
/*!40000 ALTER TABLE `billing_periods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('la-ranita-admin-cache-admin@laranita.com|127.0.0.1','i:2;',1787935113),('la-ranita-admin-cache-admin@laranita.com|127.0.0.1:timer','i:1787935113;',1787935113);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `common_areas`
--

DROP TABLE IF EXISTS `common_areas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `common_areas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `capacity` int(11) NOT NULL DEFAULT 20,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `requires_approval` tinyint(1) NOT NULL DEFAULT 0,
  `rules` text DEFAULT NULL,
  `schedule_start` time NOT NULL DEFAULT '08:00:00',
  `schedule_end` time NOT NULL DEFAULT '22:00:00',
  `duration_minutes` int(11) NOT NULL DEFAULT 120,
  `maintenance_blocked_days` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`maintenance_blocked_days`)),
  `photos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`photos`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `common_areas`
--

LOCK TABLES `common_areas` WRITE;
/*!40000 ALTER TABLE `common_areas` DISABLE KEYS */;
INSERT INTO `common_areas` VALUES (1,'Quincho','quincho princopal',50,1,0.00,0,NULL,'08:00:00','02:00:00',360,NULL,'[\"img\\/common_area_placeholder.jpg\"]','2026-08-10 15:49:31','2026-08-10 20:55:13');
/*!40000 ALTER TABLE `common_areas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `communication_deliveries`
--

DROP TABLE IF EXISTS `communication_deliveries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `communication_deliveries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `communication_recipient_id` bigint(20) unsigned NOT NULL,
  `channel` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `provider_message_id` varchar(255) DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `error_message` text DEFAULT NULL,
  `retry_count` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `communication_deliveries_communication_recipient_id_foreign` (`communication_recipient_id`),
  CONSTRAINT `communication_deliveries_communication_recipient_id_foreign` FOREIGN KEY (`communication_recipient_id`) REFERENCES `communication_recipients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `communication_deliveries`
--

LOCK TABLES `communication_deliveries` WRITE;
/*!40000 ALTER TABLE `communication_deliveries` DISABLE KEYS */;
/*!40000 ALTER TABLE `communication_deliveries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `communication_recipients`
--

DROP TABLE IF EXISTS `communication_recipients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `communication_recipients` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `communication_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `lot_id` bigint(20) unsigned DEFAULT NULL,
  `preferred_channel` varchar(255) NOT NULL DEFAULT 'email',
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `communication_recipients_communication_id_foreign` (`communication_id`),
  KEY `communication_recipients_user_id_foreign` (`user_id`),
  KEY `communication_recipients_lot_id_foreign` (`lot_id`),
  CONSTRAINT `communication_recipients_communication_id_foreign` FOREIGN KEY (`communication_id`) REFERENCES `communications` (`id`) ON DELETE CASCADE,
  CONSTRAINT `communication_recipients_lot_id_foreign` FOREIGN KEY (`lot_id`) REFERENCES `lots` (`id`) ON DELETE CASCADE,
  CONSTRAINT `communication_recipients_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `communication_recipients`
--

LOCK TABLES `communication_recipients` WRITE;
/*!40000 ALTER TABLE `communication_recipients` DISABLE KEYS */;
/*!40000 ALTER TABLE `communication_recipients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `communication_templates`
--

DROP TABLE IF EXISTS `communication_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `communication_templates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `body` text NOT NULL,
  `channels` varchar(255) NOT NULL DEFAULT 'email',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `communication_templates_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `communication_templates`
--

LOCK TABLES `communication_templates` WRITE;
/*!40000 ALTER TABLE `communication_templates` DISABLE KEYS */;
/*!40000 ALTER TABLE `communication_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `communications`
--

DROP TABLE IF EXISTS `communications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `communications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `attachment_path` varchar(255) DEFAULT NULL,
  `channels` varchar(255) NOT NULL DEFAULT 'portal',
  `target_type` varchar(255) NOT NULL DEFAULT 'all',
  `sent_by` bigint(20) unsigned DEFAULT NULL,
  `scheduled_at` timestamp NULL DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `communications_sent_by_foreign` (`sent_by`),
  CONSTRAINT `communications_sent_by_foreign` FOREIGN KEY (`sent_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `communications`
--

LOCK TABLES `communications` WRITE;
/*!40000 ALTER TABLE `communications` DISABLE KEYS */;
/*!40000 ALTER TABLE `communications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `document_categories`
--

DROP TABLE IF EXISTS `document_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `document_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `document_categories_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `document_categories`
--

LOCK TABLES `document_categories` WRITE;
/*!40000 ALTER TABLE `document_categories` DISABLE KEYS */;
INSERT INTO `document_categories` VALUES (1,'regulations','Reglamentos e Internas','2026-08-08 19:20:12','2026-08-08 19:20:12'),(2,'minutes','Actas de Asambleas','2026-08-08 19:20:12','2026-08-08 19:20:12'),(3,'expenses_liq','Liquidación de Expensas','2026-08-08 19:20:12','2026-08-08 19:20:12'),(4,'forms','Formularios y Autorizaciones','2026-08-08 19:20:12','2026-08-08 19:20:12'),(5,'legal','Información Legal','2026-08-08 19:20:12','2026-08-08 19:20:12');
/*!40000 ALTER TABLE `document_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `document_versions`
--

DROP TABLE IF EXISTS `document_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `document_versions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `document_id` bigint(20) unsigned NOT NULL,
  `version` varchar(255) NOT NULL DEFAULT '1.0',
  `file_path` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_size` int(11) DEFAULT NULL,
  `uploaded_by` bigint(20) unsigned NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `document_versions_document_id_foreign` (`document_id`),
  KEY `document_versions_uploaded_by_foreign` (`uploaded_by`),
  CONSTRAINT `document_versions_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `document_versions_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `document_versions`
--

LOCK TABLES `document_versions` WRITE;
/*!40000 ALTER TABLE `document_versions` DISABLE KEYS */;
/*!40000 ALTER TABLE `document_versions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `documents`
--

DROP TABLE IF EXISTS `documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `documents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `visibility` varchar(255) NOT NULL DEFAULT 'public',
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `documents_category_id_foreign` (`category_id`),
  CONSTRAINT `documents_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `document_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documents`
--

LOCK TABLES `documents` WRITE;
/*!40000 ALTER TABLE `documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `email_settings`
--

DROP TABLE IF EXISTS `email_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sender_name` varchar(255) DEFAULT NULL,
  `sender_email` varchar(255) DEFAULT NULL,
  `reply_to` varchar(255) DEFAULT NULL,
  `provider` varchar(255) NOT NULL DEFAULT 'smtp',
  `host` varchar(255) DEFAULT NULL,
  `port` int(11) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` text DEFAULT NULL,
  `encryption` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `test_connection_status` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_settings`
--

LOCK TABLES `email_settings` WRITE;
/*!40000 ALTER TABLE `email_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `email_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expense_items`
--

DROP TABLE IF EXISTS `expense_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `expense_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `expense_id` bigint(20) unsigned NOT NULL,
  `concept` varchar(255) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `category` varchar(255) NOT NULL DEFAULT 'general',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `expense_items_expense_id_foreign` (`expense_id`),
  CONSTRAINT `expense_items_expense_id_foreign` FOREIGN KEY (`expense_id`) REFERENCES `expenses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expense_items`
--

LOCK TABLES `expense_items` WRITE;
/*!40000 ALTER TABLE `expense_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `expense_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expenses`
--

DROP TABLE IF EXISTS `expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `expenses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `billing_period_id` bigint(20) unsigned NOT NULL,
  `functional_unit_id` bigint(20) unsigned NOT NULL,
  `issue_date` date NOT NULL,
  `due_date` date NOT NULL,
  `second_due_date` date DEFAULT NULL,
  `previous_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `capital_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `interest_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `adjustments_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` varchar(255) NOT NULL DEFAULT 'draft',
  `attachment_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `expenses_billing_period_id_foreign` (`billing_period_id`),
  KEY `expenses_functional_unit_id_foreign` (`functional_unit_id`),
  CONSTRAINT `expenses_billing_period_id_foreign` FOREIGN KEY (`billing_period_id`) REFERENCES `billing_periods` (`id`) ON DELETE CASCADE,
  CONSTRAINT `expenses_functional_unit_id_foreign` FOREIGN KEY (`functional_unit_id`) REFERENCES `functional_units` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expenses`
--

LOCK TABLES `expenses` WRITE;
/*!40000 ALTER TABLE `expenses` DISABLE KEYS */;
/*!40000 ALTER TABLE `expenses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `functional_units`
--

DROP TABLE IF EXISTS `functional_units`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `functional_units` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `lot_id` bigint(20) unsigned NOT NULL,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `functional_units_code_unique` (`code`),
  KEY `functional_units_lot_id_foreign` (`lot_id`),
  CONSTRAINT `functional_units_lot_id_foreign` FOREIGN KEY (`lot_id`) REFERENCES `lots` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=132 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `functional_units`
--

LOCK TABLES `functional_units` WRITE;
/*!40000 ALTER TABLE `functional_units` DISABLE KEYS */;
INSERT INTO `functional_units` VALUES (1,1,'LOT-001-UF','UF Lote 1','Unidad funcional del Lote 1',0.00,'2026-08-08 19:20:12','2026-08-08 19:20:12',NULL),(2,2,'LOT-002-UF','UF Lote 2','Unidad funcional del Lote 2',0.00,'2026-08-08 19:20:13','2026-08-08 19:20:13',NULL),(3,3,'LOT-003-UF','UF Lote 3','Unidad funcional del Lote 3',0.00,'2026-08-08 19:20:13','2026-08-08 19:20:13',NULL),(4,4,'LOT-004-UF','UF Lote 4','Unidad funcional del Lote 4',0.00,'2026-08-08 19:20:14','2026-08-08 19:20:14',NULL),(5,5,'LOT-005-UF','UF Lote 5','Unidad funcional del Lote 5',0.00,'2026-08-08 19:20:14','2026-08-08 19:20:14',NULL),(6,6,'LOT-006-UF','UF Lote 6','Unidad funcional del Lote 6',0.00,'2026-08-08 19:20:15','2026-08-08 19:20:15',NULL),(7,7,'LOT-007-UF','UF Lote 7','Unidad funcional del Lote 7',0.00,'2026-08-08 19:20:15','2026-08-08 19:20:15',NULL),(8,8,'LOT-008-UF','UF Lote 8','Unidad funcional del Lote 8',0.00,'2026-08-08 19:20:16','2026-08-08 19:20:16',NULL),(9,9,'LOT-009-UF','UF Lote 9','Unidad funcional del Lote 9',0.00,'2026-08-08 19:20:16','2026-08-08 19:20:16',NULL),(10,10,'LOT-010-UF','UF Lote 10','Unidad funcional del Lote 10',0.00,'2026-08-08 19:20:17','2026-08-08 19:20:17',NULL),(11,11,'LOT-011-UF','UF Lote 11','Unidad funcional del Lote 11',0.00,'2026-08-08 19:20:17','2026-08-08 19:20:17',NULL),(12,12,'LOT-012-UF','UF Lote 12','Unidad funcional del Lote 12',0.00,'2026-08-08 19:20:18','2026-08-08 19:20:18',NULL),(13,13,'LOT-013-UF','UF Lote 13','Unidad funcional del Lote 13',0.00,'2026-08-08 19:20:18','2026-08-08 19:20:18',NULL),(14,14,'LOT-014-UF','UF Lote 14','Unidad funcional del Lote 14',0.00,'2026-08-08 19:20:19','2026-08-08 19:20:19',NULL),(15,15,'LOT-015-UF','UF Lote 15','Unidad funcional del Lote 15',0.00,'2026-08-08 19:20:19','2026-08-08 19:20:19',NULL),(16,16,'LOT-016-UF','UF Lote 16','Unidad funcional del Lote 16',0.00,'2026-08-08 19:20:20','2026-08-08 19:20:20',NULL),(17,17,'LOT-017-UF','UF Lote 17','Unidad funcional del Lote 17',0.00,'2026-08-08 19:20:20','2026-08-08 19:20:20',NULL),(18,18,'LOT-018-UF','UF Lote 18','Unidad funcional del Lote 18',0.00,'2026-08-08 19:20:21','2026-08-08 19:20:21',NULL),(19,19,'LOT-019-UF','UF Lote 19','Unidad funcional del Lote 19',0.00,'2026-08-08 19:20:21','2026-08-08 19:20:21',NULL),(20,20,'LOT-020-UF','UF Lote 20','Unidad funcional del Lote 20',0.00,'2026-08-08 19:20:22','2026-08-08 19:20:22',NULL),(21,21,'LOT-021-UF','UF Lote 21','Unidad funcional del Lote 21',0.00,'2026-08-08 19:20:22','2026-08-08 19:20:22',NULL),(22,22,'LOT-022-UF','UF Lote 22','Unidad funcional del Lote 22',0.00,'2026-08-08 19:20:23','2026-08-08 19:20:23',NULL),(23,23,'LOT-023-UF','UF Lote 23','Unidad funcional del Lote 23',0.00,'2026-08-08 19:20:23','2026-08-08 19:20:23',NULL),(24,24,'LOT-024-UF','UF Lote 24','Unidad funcional del Lote 24',0.00,'2026-08-08 19:20:24','2026-08-08 19:20:24',NULL),(25,25,'LOT-025-UF','UF Lote 25','Unidad funcional del Lote 25',0.00,'2026-08-08 19:20:24','2026-08-08 19:20:24',NULL),(26,26,'LOT-026-UF','UF Lote 26','Unidad funcional del Lote 26',0.00,'2026-08-08 19:20:25','2026-08-08 19:20:25',NULL),(27,27,'LOT-027-UF','UF Lote 27','Unidad funcional del Lote 27',0.00,'2026-08-08 19:20:25','2026-08-08 19:20:25',NULL),(28,28,'LOT-028-UF','UF Lote 28','Unidad funcional del Lote 28',0.00,'2026-08-08 19:20:26','2026-08-08 19:20:26',NULL),(29,29,'LOT-029-UF','UF Lote 29','Unidad funcional del Lote 29',0.00,'2026-08-08 19:20:26','2026-08-08 19:20:26',NULL),(30,30,'LOT-030-UF','UF Lote 30','Unidad funcional del Lote 30',0.00,'2026-08-08 19:20:27','2026-08-08 19:20:27',NULL),(31,31,'LOT-031-UF','UF Lote 31','Unidad funcional del Lote 31',0.00,'2026-08-08 19:20:27','2026-08-08 19:20:27',NULL),(32,32,'LOT-032-UF','UF Lote 32','Unidad funcional del Lote 32',0.00,'2026-08-08 19:20:28','2026-08-08 19:20:28',NULL),(33,33,'LOT-033-UF','UF Lote 33','Unidad funcional del Lote 33',0.00,'2026-08-08 19:20:28','2026-08-08 19:20:28',NULL),(34,34,'LOT-034-UF','UF Lote 34','Unidad funcional del Lote 34',0.00,'2026-08-08 19:20:29','2026-08-08 19:20:29',NULL),(35,35,'LOT-035-UF','UF Lote 35','Unidad funcional del Lote 35',0.00,'2026-08-08 19:20:29','2026-08-08 19:20:29',NULL),(36,36,'LOT-036-UF','UF Lote 36','Unidad funcional del Lote 36',0.00,'2026-08-08 19:20:30','2026-08-08 19:20:30',NULL),(37,37,'LOT-037-UF','UF Lote 37','Unidad funcional del Lote 37',0.00,'2026-08-08 19:20:30','2026-08-08 19:20:30',NULL),(38,38,'LOT-038-UF','UF Lote 38','Unidad funcional del Lote 38',0.00,'2026-08-08 19:20:31','2026-08-08 19:20:31',NULL),(39,39,'LOT-039-UF','UF Lote 39','Unidad funcional del Lote 39',0.00,'2026-08-08 19:20:31','2026-08-08 19:20:31',NULL),(40,40,'LOT-040-UF','UF Lote 40','Unidad funcional del Lote 40',0.00,'2026-08-08 19:20:32','2026-08-08 19:20:32',NULL),(41,41,'LOT-041-UF','UF Lote 41','Unidad funcional del Lote 41',0.00,'2026-08-08 19:20:32','2026-08-08 19:20:32',NULL),(42,42,'LOT-042-UF','UF Lote 42','Unidad funcional del Lote 42',0.00,'2026-08-08 19:20:33','2026-08-08 19:20:33',NULL),(43,43,'LOT-043-UF','UF Lote 43','Unidad funcional del Lote 43',0.00,'2026-08-08 19:20:33','2026-08-08 19:20:33',NULL),(44,44,'LOT-044-UF','UF Lote 44','Unidad funcional del Lote 44',0.00,'2026-08-08 19:20:34','2026-08-08 19:20:34',NULL),(45,45,'LOT-045-UF','UF Lote 45','Unidad funcional del Lote 45',0.00,'2026-08-08 19:20:34','2026-08-08 19:20:34',NULL),(46,46,'LOT-046-UF','UF Lote 46','Unidad funcional del Lote 46',0.00,'2026-08-08 19:20:35','2026-08-08 19:20:35',NULL),(47,47,'LOT-047-UF','UF Lote 47','Unidad funcional del Lote 47',0.00,'2026-08-08 19:20:35','2026-08-08 19:20:35',NULL),(48,48,'LOT-048-UF','UF Lote 48','Unidad funcional del Lote 48',0.00,'2026-08-08 19:20:36','2026-08-08 19:20:36',NULL),(49,49,'LOT-049-UF','UF Lote 49','Unidad funcional del Lote 49',0.00,'2026-08-08 19:20:36','2026-08-08 19:20:36',NULL),(50,50,'LOT-050-UF','UF Lote 50','Unidad funcional del Lote 50',0.00,'2026-08-08 19:20:37','2026-08-08 19:20:37',NULL),(51,51,'LOT-051-UF','UF Lote 51','Unidad funcional del Lote 51',0.00,'2026-08-08 19:20:37','2026-08-08 19:20:37',NULL),(52,52,'LOT-052-UF','UF Lote 52','Unidad funcional del Lote 52',0.00,'2026-08-08 19:20:37','2026-08-08 19:20:37',NULL),(53,53,'LOT-053-UF','UF Lote 53','Unidad funcional del Lote 53',0.00,'2026-08-08 19:20:38','2026-08-08 19:20:38',NULL),(54,54,'LOT-054-UF','UF Lote 54','Unidad funcional del Lote 54',0.00,'2026-08-08 19:20:38','2026-08-08 19:20:38',NULL),(55,55,'LOT-055-UF','UF Lote 55','Unidad funcional del Lote 55',0.00,'2026-08-08 19:20:39','2026-08-08 19:20:39',NULL),(56,56,'LOT-056-UF','UF Lote 56','Unidad funcional del Lote 56',0.00,'2026-08-08 19:20:39','2026-08-08 19:20:39',NULL),(57,57,'LOT-057-UF','UF Lote 57','Unidad funcional del Lote 57',0.00,'2026-08-08 19:20:40','2026-08-08 19:20:40',NULL),(58,58,'LOT-058-UF','UF Lote 58','Unidad funcional del Lote 58',0.00,'2026-08-08 19:20:40','2026-08-08 19:20:40',NULL),(59,59,'LOT-059-UF','UF Lote 59','Unidad funcional del Lote 59',0.00,'2026-08-08 19:20:41','2026-08-08 19:20:41',NULL),(60,60,'LOT-060-UF','UF Lote 60','Unidad funcional del Lote 60',0.00,'2026-08-08 19:20:41','2026-08-08 19:20:41',NULL),(61,61,'LOT-061-UF','UF Lote 61','Unidad funcional del Lote 61',0.00,'2026-08-08 19:20:42','2026-08-08 19:20:42',NULL),(62,62,'LOT-062-UF','UF Lote 62','Unidad funcional del Lote 62',0.00,'2026-08-08 19:20:42','2026-08-08 19:20:42',NULL),(63,63,'LOT-063-UF','UF Lote 63','Unidad funcional del Lote 63',0.00,'2026-08-08 19:20:43','2026-08-08 19:20:43',NULL),(64,64,'LOT-064-UF','UF Lote 64','Unidad funcional del Lote 64',0.00,'2026-08-08 19:20:43','2026-08-08 19:20:43',NULL),(65,65,'LOT-065-UF','UF Lote 65','Unidad funcional del Lote 65',0.00,'2026-08-08 19:20:43','2026-08-08 19:20:43',NULL),(66,66,'LOT-066-UF','UF Lote 66','Unidad funcional del Lote 66',0.00,'2026-08-08 19:20:44','2026-08-08 19:20:44',NULL),(67,67,'LOT-067-UF','UF Lote 67','Unidad funcional del Lote 67',0.00,'2026-08-08 19:20:44','2026-08-08 19:20:44',NULL),(68,68,'LOT-068-UF','UF Lote 68','Unidad funcional del Lote 68',0.00,'2026-08-08 19:20:45','2026-08-08 19:20:45',NULL),(69,69,'LOT-069-UF','UF Lote 69','Unidad funcional del Lote 69',0.00,'2026-08-08 19:20:45','2026-08-08 19:20:45',NULL),(70,70,'LOT-070-UF','UF Lote 70','Unidad funcional del Lote 70',0.00,'2026-08-08 19:20:45','2026-08-08 19:20:45',NULL),(71,71,'LOT-071-UF','UF Lote 71','Unidad funcional del Lote 71',0.00,'2026-08-08 19:20:46','2026-08-08 19:20:46',NULL),(72,72,'LOT-072-UF','UF Lote 72','Unidad funcional del Lote 72',0.00,'2026-08-08 19:20:46','2026-08-08 19:20:46',NULL),(73,73,'LOT-073-UF','UF Lote 73','Unidad funcional del Lote 73',0.00,'2026-08-08 19:20:46','2026-08-08 19:20:46',NULL),(74,74,'LOT-074-UF','UF Lote 74','Unidad funcional del Lote 74',0.00,'2026-08-08 19:20:46','2026-08-08 19:20:46',NULL),(75,75,'LOT-075-UF','UF Lote 75','Unidad funcional del Lote 75',0.00,'2026-08-08 19:20:47','2026-08-08 19:20:47',NULL),(76,76,'LOT-076-UF','UF Lote 76','Unidad funcional del Lote 76',0.00,'2026-08-08 19:20:47','2026-08-08 19:20:47',NULL),(77,77,'LOT-077-UF','UF Lote 77','Unidad funcional del Lote 77',0.00,'2026-08-08 19:20:48','2026-08-08 19:20:48',NULL),(78,78,'LOT-078-UF','UF Lote 78','Unidad funcional del Lote 78',0.00,'2026-08-08 19:20:48','2026-08-08 19:20:48',NULL),(79,79,'LOT-079-UF','UF Lote 79','Unidad funcional del Lote 79',0.00,'2026-08-08 19:20:49','2026-08-08 19:20:49',NULL),(80,80,'LOT-080-UF','UF Lote 80','Unidad funcional del Lote 80',0.00,'2026-08-08 19:20:49','2026-08-08 19:20:49',NULL),(81,81,'LOT-081-UF','UF Lote 81','Unidad funcional del Lote 81',0.00,'2026-08-08 19:20:50','2026-08-08 19:20:50',NULL),(82,82,'LOT-082-UF','UF Lote 82','Unidad funcional del Lote 82',0.00,'2026-08-08 19:20:50','2026-08-08 19:20:50',NULL),(83,83,'LOT-083-UF','UF Lote 83','Unidad funcional del Lote 83',0.00,'2026-08-08 19:20:51','2026-08-08 19:20:51',NULL),(84,84,'LOT-084-UF','UF Lote 84','Unidad funcional del Lote 84',0.00,'2026-08-08 19:20:51','2026-08-08 19:20:51',NULL),(85,85,'LOT-085-UF','UF Lote 85','Unidad funcional del Lote 85',0.00,'2026-08-08 19:20:52','2026-08-08 19:20:52',NULL),(86,86,'LOT-086-UF','UF Lote 86','Unidad funcional del Lote 86',0.00,'2026-08-08 19:20:52','2026-08-08 19:20:52',NULL),(87,87,'LOT-087-UF','UF Lote 87','Unidad funcional del Lote 87',0.00,'2026-08-08 19:20:53','2026-08-08 19:20:53',NULL),(88,88,'LOT-088-UF','UF Lote 88','Unidad funcional del Lote 88',0.00,'2026-08-08 19:20:53','2026-08-08 19:20:53',NULL),(89,89,'LOT-089-UF','UF Lote 89','Unidad funcional del Lote 89',0.00,'2026-08-08 19:20:53','2026-08-08 19:20:53',NULL),(90,90,'LOT-090-UF','UF Lote 90','Unidad funcional del Lote 90',0.00,'2026-08-08 19:20:54','2026-08-08 19:20:54',NULL),(91,91,'LOT-091-UF','UF Lote 91','Unidad funcional del Lote 91',0.00,'2026-08-08 19:20:54','2026-08-08 19:20:54',NULL),(92,92,'LOT-092-UF','UF Lote 92','Unidad funcional del Lote 92',0.00,'2026-08-08 19:20:54','2026-08-08 19:20:54',NULL),(93,93,'LOT-093-UF','UF Lote 93','Unidad funcional del Lote 93',0.00,'2026-08-08 19:20:55','2026-08-08 19:20:55',NULL),(94,94,'LOT-094-UF','UF Lote 94','Unidad funcional del Lote 94',0.00,'2026-08-08 19:20:56','2026-08-08 19:20:56',NULL),(95,95,'LOT-095-UF','UF Lote 95','Unidad funcional del Lote 95',0.00,'2026-08-08 19:20:56','2026-08-08 19:20:56',NULL),(96,96,'LOT-096-UF','UF Lote 96','Unidad funcional del Lote 96',0.00,'2026-08-08 19:20:56','2026-08-08 19:20:56',NULL),(97,97,'LOT-097-UF','UF Lote 97','Unidad funcional del Lote 97',0.00,'2026-08-08 19:20:57','2026-08-08 19:20:57',NULL),(98,98,'LOT-098-UF','UF Lote 98','Unidad funcional del Lote 98',0.00,'2026-08-08 19:20:57','2026-08-08 19:20:57',NULL),(99,99,'LOT-099-UF','UF Lote 99','Unidad funcional del Lote 99',0.00,'2026-08-08 19:20:58','2026-08-08 19:20:58',NULL),(100,100,'LOT-100-UF','UF Lote 100','Unidad funcional del Lote 100',0.00,'2026-08-08 19:20:58','2026-08-08 19:20:58',NULL),(101,101,'LOT-101-UF','UF Lote 101','Unidad funcional del Lote 101',0.00,'2026-08-08 19:20:58','2026-08-08 19:20:58',NULL),(102,102,'LOT-102-UF','UF Lote 102','Unidad funcional del Lote 102',0.00,'2026-08-08 19:20:59','2026-08-08 19:20:59',NULL),(103,103,'LOT-103-UF','UF Lote 103','Unidad funcional del Lote 103',0.00,'2026-08-08 19:20:59','2026-08-08 19:20:59',NULL),(104,104,'LOT-104-UF','UF Lote 104','Unidad funcional del Lote 104',0.00,'2026-08-08 19:21:00','2026-08-08 19:21:00',NULL),(105,105,'LOT-105-UF','UF Lote 105','Unidad funcional del Lote 105',0.00,'2026-08-08 19:21:01','2026-08-08 19:21:01',NULL),(106,106,'LOT-106-UF','UF Lote 106','Unidad funcional del Lote 106',0.00,'2026-08-08 19:21:01','2026-08-08 19:21:01',NULL),(107,107,'LOT-107-UF','UF Lote 107','Unidad funcional del Lote 107',0.00,'2026-08-08 19:21:01','2026-08-08 19:21:01',NULL),(108,108,'LOT-108-UF','UF Lote 108','Unidad funcional del Lote 108',0.00,'2026-08-08 19:21:02','2026-08-08 19:21:02',NULL),(109,109,'LOT-109-UF','UF Lote 109','Unidad funcional del Lote 109',0.00,'2026-08-08 19:21:02','2026-08-08 19:21:02',NULL),(110,110,'LOT-110-UF','UF Lote 110','Unidad funcional del Lote 110',0.00,'2026-08-08 19:21:03','2026-08-08 19:21:03',NULL),(111,111,'LOT-111-UF','UF Lote 111','Unidad funcional del Lote 111',0.00,'2026-08-08 19:21:03','2026-08-08 19:21:03',NULL),(112,112,'LOT-112-UF','UF Lote 112','Unidad funcional del Lote 112',0.00,'2026-08-08 19:21:04','2026-08-08 19:21:04',NULL),(113,113,'LOT-113-UF','UF Lote 113','Unidad funcional del Lote 113',0.00,'2026-08-08 19:21:04','2026-08-08 19:21:04',NULL),(114,114,'LOT-114-UF','UF Lote 114','Unidad funcional del Lote 114',0.00,'2026-08-08 19:21:05','2026-08-08 19:21:05',NULL),(115,115,'LOT-115-UF','UF Lote 115','Unidad funcional del Lote 115',0.00,'2026-08-08 19:21:05','2026-08-08 19:21:05',NULL),(116,116,'LOT-116-UF','UF Lote 116','Unidad funcional del Lote 116',0.00,'2026-08-08 19:21:06','2026-08-08 19:21:06',NULL),(117,117,'LOT-117-UF','UF Lote 117','Unidad funcional del Lote 117',0.00,'2026-08-08 19:21:06','2026-08-08 19:21:06',NULL),(118,118,'LOT-118-UF','UF Lote 118','Unidad funcional del Lote 118',0.00,'2026-08-08 19:21:07','2026-08-08 19:21:07',NULL),(119,119,'LOT-119-UF','UF Lote 119','Unidad funcional del Lote 119',0.00,'2026-08-08 19:21:07','2026-08-08 19:21:07',NULL),(120,120,'LOT-120-UF','UF Lote 120','Unidad funcional del Lote 120',0.00,'2026-08-08 19:21:08','2026-08-08 19:21:08',NULL),(121,121,'LOT-121-UF','UF Lote 121','Unidad funcional del Lote 121',0.00,'2026-08-08 19:21:08','2026-08-08 19:21:08',NULL),(122,122,'LOT-122-UF','UF Lote 122','Unidad funcional del Lote 122',0.00,'2026-08-08 19:21:09','2026-08-08 19:21:09',NULL),(123,123,'LOT-123-UF','UF Lote 123','Unidad funcional del Lote 123',0.00,'2026-08-08 19:21:09','2026-08-08 19:21:09',NULL),(124,124,'LOT-124-UF','UF Lote 124','Unidad funcional del Lote 124',0.00,'2026-08-08 19:21:09','2026-08-08 19:21:09',NULL),(125,125,'LOT-125-UF','UF Lote 125','Unidad funcional del Lote 125',0.00,'2026-08-08 19:21:10','2026-08-08 19:21:10',NULL),(126,126,'LOT-126-UF','UF Lote 126','Unidad funcional del Lote 126',0.00,'2026-08-08 19:21:10','2026-08-08 19:21:10',NULL),(127,127,'LOT-127-UF','UF Lote 127','Unidad funcional del Lote 127',0.00,'2026-08-08 19:21:11','2026-08-08 19:21:11',NULL),(128,128,'LOT-128-UF','UF Lote 128','Unidad funcional del Lote 128',0.00,'2026-08-08 19:21:11','2026-08-08 19:21:11',NULL),(129,129,'LOT-129-UF','UF Lote 129','Unidad funcional del Lote 129',0.00,'2026-08-08 19:21:12','2026-08-08 19:21:12',NULL),(130,130,'LOT-130-UF','UF Lote 130','Unidad funcional del Lote 130',0.00,'2026-08-08 19:21:12','2026-08-08 19:21:12',NULL),(131,131,'LOT-131-UF','UF Lote 131','Unidad funcional del Lote 131',0.00,'2026-08-08 19:21:13','2026-08-08 19:21:13',NULL);
/*!40000 ALTER TABLE `functional_units` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `guest_authorizations`
--

DROP TABLE IF EXISTS `guest_authorizations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `guest_authorizations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `lot_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `type` enum('individual','list','frequent') NOT NULL DEFAULT 'individual',
  `name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `dni` varchar(255) DEFAULT NULL,
  `license_plate` varchar(255) DEFAULT NULL,
  `visit_date` date DEFAULT NULL,
  `visit_time` time DEFAULT NULL,
  `status` enum('pending','active','used','expired') NOT NULL DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `qr_code` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `guest_authorizations_lot_id_foreign` (`lot_id`),
  KEY `guest_authorizations_user_id_foreign` (`user_id`),
  CONSTRAINT `guest_authorizations_lot_id_foreign` FOREIGN KEY (`lot_id`) REFERENCES `lots` (`id`) ON DELETE CASCADE,
  CONSTRAINT `guest_authorizations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `guest_authorizations`
--

LOCK TABLES `guest_authorizations` WRITE;
/*!40000 ALTER TABLE `guest_authorizations` DISABLE KEYS */;
INSERT INTO `guest_authorizations` VALUES (1,62,66,'individual','Nicole','Nunes','12121212',NULL,'2026-08-31','14:00:00','active',NULL,'RANITA-8WRIQJMKO862','2026-08-31 17:57:14','2026-08-31 17:57:14'),(2,62,66,'individual','Juan','Salaverri','131313131',NULL,'2026-08-31',NULL,'active',NULL,'RANITA-FQR9XVAYSNJN','2026-08-31 18:54:53','2026-08-31 18:54:53');
/*!40000 ALTER TABLE `guest_authorizations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `import_rows`
--

DROP TABLE IF EXISTS `import_rows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `import_rows` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `import_id` bigint(20) unsigned NOT NULL,
  `row_number` int(11) NOT NULL,
  `data` longtext NOT NULL,
  `errors` longtext DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `import_rows_import_id_foreign` (`import_id`),
  CONSTRAINT `import_rows_import_id_foreign` FOREIGN KEY (`import_id`) REFERENCES `imports` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `import_rows`
--

LOCK TABLES `import_rows` WRITE;
/*!40000 ALTER TABLE `import_rows` DISABLE KEYS */;
/*!40000 ALTER TABLE `import_rows` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `imports`
--

DROP TABLE IF EXISTS `imports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `imports` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `total_rows` int(11) NOT NULL DEFAULT 0,
  `valid_rows` int(11) NOT NULL DEFAULT 0,
  `invalid_rows` int(11) NOT NULL DEFAULT 0,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `imports_user_id_foreign` (`user_id`),
  CONSTRAINT `imports_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `imports`
--

LOCK TABLES `imports` WRITE;
/*!40000 ALTER TABLE `imports` DISABLE KEYS */;
/*!40000 ALTER TABLE `imports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `integration_logs`
--

DROP TABLE IF EXISTS `integration_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `integration_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `service_name` varchar(255) NOT NULL,
  `request_data` text DEFAULT NULL,
  `response_data` text DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'success',
  `error_message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `integration_logs`
--

LOCK TABLES `integration_logs` WRITE;
/*!40000 ALTER TABLE `integration_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `integration_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `login_logs`
--

DROP TABLE IF EXISTS `login_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `login_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'success',
  `failed_attempts` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `login_logs_user_id_foreign` (`user_id`),
  CONSTRAINT `login_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login_logs`
--

LOCK TABLES `login_logs` WRITE;
/*!40000 ALTER TABLE `login_logs` DISABLE KEYS */;
INSERT INTO `login_logs` VALUES (1,1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','success',0,'2026-08-08 21:50:26'),(2,1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:153.0) Gecko/20100101 Firefox/153.0','success',0,'2026-08-09 21:25:20'),(3,1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:153.0) Gecko/20100101 Firefox/153.0','success',0,'2026-08-10 12:37:40'),(4,6,'192.168.2.136','Mozilla/5.0 (iPhone; CPU iPhone OS 26_5_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/151.0.7922.57 Mobile/15E148 Safari/604.1','success',0,'2026-08-10 13:25:09'),(5,6,'192.168.2.136','Mozilla/5.0 (iPhone; CPU iPhone OS 26_5_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/151.0.7922.57 Mobile/15E148 Safari/604.1','success',0,'2026-08-10 15:55:54'),(6,1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:153.0) Gecko/20100101 Firefox/153.0','success',0,'2026-08-10 21:10:58'),(7,NULL,'192.168.68.109','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:153.0) Gecko/20100101 Firefox/153.0','failed',1,'2026-08-15 11:57:49'),(8,NULL,'192.168.68.109','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:153.0) Gecko/20100101 Firefox/153.0','failed',1,'2026-08-15 11:57:59'),(9,1,'192.168.68.109','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:153.0) Gecko/20100101 Firefox/153.0','success',0,'2026-08-15 11:58:10'),(10,1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:153.0) Gecko/20100101 Firefox/153.0','success',0,'2026-08-15 21:44:13'),(11,NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:154.0) Gecko/20100101 Firefox/154.0','failed',1,'2026-08-28 16:37:33'),(12,NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:154.0) Gecko/20100101 Firefox/154.0','failed',1,'2026-08-28 16:37:45'),(13,1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:154.0) Gecko/20100101 Firefox/154.0','success',0,'2026-08-28 16:38:34'),(14,1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:154.0) Gecko/20100101 Firefox/154.0','success',0,'2026-08-31 14:45:18'),(15,66,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:154.0) Gecko/20100101 Firefox/154.0','success',0,'2026-08-31 14:48:09'),(16,66,'192.168.2.136','Mozilla/5.0 (iPhone; CPU iPhone OS 26_6_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/152.0.7977.64 Mobile/15E148 Safari/604.1','success',0,'2026-08-31 15:54:16');
/*!40000 ALTER TABLE `login_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lot_follow_ups`
--

DROP TABLE IF EXISTS `lot_follow_ups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lot_follow_ups` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `lot_history_event_id` bigint(20) unsigned DEFAULT NULL,
  `lot_id` bigint(20) unsigned NOT NULL,
  `reason` varchar(255) NOT NULL,
  `assignee_id` bigint(20) unsigned DEFAULT NULL,
  `due_date` date NOT NULL,
  `priority` varchar(255) NOT NULL DEFAULT 'medium',
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `reminder_sent` tinyint(1) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lot_follow_ups_lot_history_event_id_foreign` (`lot_history_event_id`),
  KEY `lot_follow_ups_lot_id_foreign` (`lot_id`),
  KEY `lot_follow_ups_assignee_id_foreign` (`assignee_id`),
  CONSTRAINT `lot_follow_ups_assignee_id_foreign` FOREIGN KEY (`assignee_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `lot_follow_ups_lot_history_event_id_foreign` FOREIGN KEY (`lot_history_event_id`) REFERENCES `lot_history_events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lot_follow_ups_lot_id_foreign` FOREIGN KEY (`lot_id`) REFERENCES `lots` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lot_follow_ups`
--

LOCK TABLES `lot_follow_ups` WRITE;
/*!40000 ALTER TABLE `lot_follow_ups` DISABLE KEYS */;
/*!40000 ALTER TABLE `lot_follow_ups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lot_history_attachments`
--

DROP TABLE IF EXISTS `lot_history_attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lot_history_attachments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `lot_history_event_id` bigint(20) unsigned NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lot_history_attachments_lot_history_event_id_foreign` (`lot_history_event_id`),
  CONSTRAINT `lot_history_attachments_lot_history_event_id_foreign` FOREIGN KEY (`lot_history_event_id`) REFERENCES `lot_history_events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lot_history_attachments`
--

LOCK TABLES `lot_history_attachments` WRITE;
/*!40000 ALTER TABLE `lot_history_attachments` DISABLE KEYS */;
/*!40000 ALTER TABLE `lot_history_attachments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lot_history_categories`
--

DROP TABLE IF EXISTS `lot_history_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lot_history_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lot_history_categories_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lot_history_categories`
--

LOCK TABLES `lot_history_categories` WRITE;
/*!40000 ALTER TABLE `lot_history_categories` DISABLE KEYS */;
INSERT INTO `lot_history_categories` VALUES (1,'admin','Administrativo','2026-08-08 19:20:12','2026-08-08 19:20:12'),(2,'finance','Financiero','2026-08-08 19:20:12','2026-08-08 19:20:12'),(3,'security','Seguridad','2026-08-08 19:20:12','2026-08-08 19:20:12'),(4,'maintenance','Mantenimiento','2026-08-08 19:20:12','2026-08-08 19:20:12'),(5,'inspections','Inspecciones y Sanciones','2026-08-08 19:20:12','2026-08-08 19:20:12'),(6,'comms','Comunicaciones','2026-08-08 19:20:12','2026-08-08 19:20:12');
/*!40000 ALTER TABLE `lot_history_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lot_history_event_types`
--

DROP TABLE IF EXISTS `lot_history_event_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lot_history_event_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lot_history_event_types_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lot_history_event_types`
--

LOCK TABLES `lot_history_event_types` WRITE;
/*!40000 ALTER TABLE `lot_history_event_types` DISABLE KEYS */;
INSERT INTO `lot_history_event_types` VALUES (1,'lot_created','Alta de Lote','2026-08-08 19:20:12','2026-08-08 19:20:12'),(2,'owner_changed','Cambio de Propietario','2026-08-08 19:20:12','2026-08-08 19:20:12'),(3,'tenant_changed','Cambio de Inquilino','2026-08-08 19:20:12','2026-08-08 19:20:12'),(4,'user_associated','Usuario Asociado','2026-08-08 19:20:12','2026-08-08 19:20:12'),(5,'user_dissociated','Usuario Desasociado','2026-08-08 19:20:12','2026-08-08 19:20:12'),(6,'expense_generated','Expensa Generada','2026-08-08 19:20:12','2026-08-08 19:20:12'),(7,'expense_published','Expensa Publicada','2026-08-08 19:20:12','2026-08-08 19:20:12'),(8,'payment_reported','Pago Informado','2026-08-08 19:20:12','2026-08-08 19:20:12'),(9,'payment_approved','Pago Aprobado','2026-08-08 19:20:12','2026-08-08 19:20:12'),(10,'payment_rejected','Pago Rechazado','2026-08-08 19:20:12','2026-08-08 19:20:12'),(11,'ticket_created','Reclamo Creado','2026-08-08 19:20:12','2026-08-08 19:20:12'),(12,'ticket_answered','Reclamo Respondido','2026-08-08 19:20:12','2026-08-08 19:20:12'),(13,'ticket_closed','Reclamo Cerrado','2026-08-08 19:20:12','2026-08-08 19:20:12'),(14,'note_added','Nota Administrativa','2026-08-08 19:20:12','2026-08-08 19:20:12'),(15,'incident_logged','Incidente de Seguridad','2026-08-08 19:20:12','2026-08-08 19:20:12'),(16,'sanction_applied','Sanción Aplicada','2026-08-08 19:20:12','2026-08-08 19:20:12'),(17,'inspection_logged','Inspección Realizada','2026-08-08 19:20:12','2026-08-08 19:20:12'),(18,'comm_sent','Comunicación Enviada','2026-08-08 19:20:12','2026-08-08 19:20:12');
/*!40000 ALTER TABLE `lot_history_event_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lot_history_events`
--

DROP TABLE IF EXISTS `lot_history_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lot_history_events` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `lot_id` bigint(20) unsigned NOT NULL,
  `functional_unit_id` bigint(20) unsigned DEFAULT NULL,
  `event_type_id` bigint(20) unsigned NOT NULL,
  `category_id` bigint(20) unsigned NOT NULL,
  `related_model_type` varchar(255) DEFAULT NULL,
  `related_model_id` bigint(20) unsigned DEFAULT NULL,
  `owner_id` bigint(20) unsigned DEFAULT NULL,
  `tenant_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `event_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` varchar(255) DEFAULT NULL,
  `priority` varchar(255) NOT NULL DEFAULT 'medium',
  `source_channel` varchar(255) NOT NULL DEFAULT 'portal',
  `visibility` varchar(255) NOT NULL DEFAULT 'internal',
  `is_confidential` tinyint(1) NOT NULL DEFAULT 0,
  `metadata` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lot_history_events_lot_id_foreign` (`lot_id`),
  KEY `lot_history_events_functional_unit_id_foreign` (`functional_unit_id`),
  KEY `lot_history_events_event_type_id_foreign` (`event_type_id`),
  KEY `lot_history_events_category_id_foreign` (`category_id`),
  KEY `lot_history_events_owner_id_foreign` (`owner_id`),
  KEY `lot_history_events_tenant_id_foreign` (`tenant_id`),
  KEY `lot_history_events_user_id_foreign` (`user_id`),
  KEY `lot_history_events_related_model_type_related_model_id_index` (`related_model_type`,`related_model_id`),
  CONSTRAINT `lot_history_events_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `lot_history_categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lot_history_events_event_type_id_foreign` FOREIGN KEY (`event_type_id`) REFERENCES `lot_history_event_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lot_history_events_functional_unit_id_foreign` FOREIGN KEY (`functional_unit_id`) REFERENCES `functional_units` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lot_history_events_lot_id_foreign` FOREIGN KEY (`lot_id`) REFERENCES `lots` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lot_history_events_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `owners` (`id`) ON DELETE SET NULL,
  CONSTRAINT `lot_history_events_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE SET NULL,
  CONSTRAINT `lot_history_events_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lot_history_events`
--

LOCK TABLES `lot_history_events` WRITE;
/*!40000 ALTER TABLE `lot_history_events` DISABLE KEYS */;
/*!40000 ALTER TABLE `lot_history_events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lot_residents`
--

DROP TABLE IF EXISTS `lot_residents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lot_residents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `lot_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `dni` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `relationship` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lot_residents_dni_unique` (`dni`),
  KEY `lot_residents_lot_id_foreign` (`lot_id`),
  CONSTRAINT `lot_residents_lot_id_foreign` FOREIGN KEY (`lot_id`) REFERENCES `lots` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lot_residents`
--

LOCK TABLES `lot_residents` WRITE;
/*!40000 ALTER TABLE `lot_residents` DISABLE KEYS */;
/*!40000 ALTER TABLE `lot_residents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lot_vehicles`
--

DROP TABLE IF EXISTS `lot_vehicles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lot_vehicles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `lot_id` bigint(20) unsigned NOT NULL,
  `brand` varchar(255) DEFAULT NULL,
  `model` varchar(255) DEFAULT NULL,
  `color` varchar(255) DEFAULT NULL,
  `license_plate` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lot_vehicles_lot_id_foreign` (`lot_id`),
  CONSTRAINT `lot_vehicles_lot_id_foreign` FOREIGN KEY (`lot_id`) REFERENCES `lots` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lot_vehicles`
--

LOCK TABLES `lot_vehicles` WRITE;
/*!40000 ALTER TABLE `lot_vehicles` DISABLE KEYS */;
/*!40000 ALTER TABLE `lot_vehicles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lots`
--

DROP TABLE IF EXISTS `lots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lots` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `number` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `internal_address` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `current_owner_id` bigint(20) unsigned DEFAULT NULL,
  `current_tenant_id` bigint(20) unsigned DEFAULT NULL,
  `balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lots_code_unique` (`code`),
  UNIQUE KEY `lots_number_unique` (`number`),
  KEY `lots_current_owner_id_foreign` (`current_owner_id`),
  KEY `lots_current_tenant_id_foreign` (`current_tenant_id`),
  CONSTRAINT `lots_current_owner_id_foreign` FOREIGN KEY (`current_owner_id`) REFERENCES `owners` (`id`) ON DELETE SET NULL,
  CONSTRAINT `lots_current_tenant_id_foreign` FOREIGN KEY (`current_tenant_id`) REFERENCES `tenants` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=132 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lots`
--

LOCK TABLES `lots` WRITE;
/*!40000 ALTER TABLE `lots` DISABLE KEYS */;
INSERT INTO `lots` VALUES (1,'LOT-001','1','Lote 1','Calle Principal Lote 1','active',1,NULL,0.00,NULL,'2026-08-08 19:20:12','2026-08-08 19:20:12',NULL),(2,'LOT-002','2','Lote 2','Calle Principal Lote 2','active',2,NULL,0.00,NULL,'2026-08-08 19:20:13','2026-08-08 19:20:13',NULL),(3,'LOT-003','3','Lote 3','Calle Principal Lote 3','active',3,NULL,0.00,NULL,'2026-08-08 19:20:13','2026-08-08 19:20:13',NULL),(4,'LOT-004','4','Lote 4','Calle Principal Lote 4','active',4,NULL,0.00,NULL,'2026-08-08 19:20:14','2026-08-08 19:20:14',NULL),(5,'LOT-005','5','Lote 5','Calle Principal Lote 5','active',5,NULL,0.00,NULL,'2026-08-08 19:20:14','2026-08-08 19:20:14',NULL),(6,'LOT-006','6','Lote 6','Calle Principal Lote 6','active',6,NULL,0.00,NULL,'2026-08-08 19:20:15','2026-08-08 19:20:15',NULL),(7,'LOT-007','7','Lote 7','Calle Principal Lote 7','active',7,NULL,0.00,NULL,'2026-08-08 19:20:15','2026-08-08 19:20:15',NULL),(8,'LOT-008','8','Lote 8','Calle Principal Lote 8','active',8,NULL,0.00,NULL,'2026-08-08 19:20:16','2026-08-08 19:20:16',NULL),(9,'LOT-009','9','Lote 9','Calle Principal Lote 9','active',9,NULL,0.00,NULL,'2026-08-08 19:20:16','2026-08-08 19:20:16',NULL),(10,'LOT-010','10','Lote 10','Calle Principal Lote 10','active',10,NULL,0.00,NULL,'2026-08-08 19:20:17','2026-08-08 19:20:17',NULL),(11,'LOT-011','11','Lote 11','Calle Principal Lote 11','active',11,NULL,0.00,NULL,'2026-08-08 19:20:17','2026-08-08 19:20:17',NULL),(12,'LOT-012','12','Lote 12','Calle Principal Lote 12','active',12,NULL,0.00,NULL,'2026-08-08 19:20:18','2026-08-08 19:20:18',NULL),(13,'LOT-013','13','Lote 13','Calle Principal Lote 13','active',13,NULL,0.00,NULL,'2026-08-08 19:20:18','2026-08-08 19:20:18',NULL),(14,'LOT-014','14','Lote 14','Calle Principal Lote 14','active',14,NULL,0.00,NULL,'2026-08-08 19:20:19','2026-08-08 19:20:19',NULL),(15,'LOT-015','15','Lote 15','Calle Principal Lote 15','active',15,NULL,0.00,NULL,'2026-08-08 19:20:19','2026-08-08 19:20:19',NULL),(16,'LOT-016','16','Lote 16','Calle Principal Lote 16','active',16,NULL,0.00,NULL,'2026-08-08 19:20:20','2026-08-08 19:20:20',NULL),(17,'LOT-017','17','Lote 17','Calle Principal Lote 17','active',17,NULL,0.00,NULL,'2026-08-08 19:20:20','2026-08-08 19:20:20',NULL),(18,'LOT-018','18','Lote 18','Calle Principal Lote 18','active',18,NULL,0.00,NULL,'2026-08-08 19:20:21','2026-08-08 19:20:21',NULL),(19,'LOT-019','19','Lote 19','Calle Principal Lote 19','active',19,NULL,0.00,NULL,'2026-08-08 19:20:21','2026-08-08 19:20:21',NULL),(20,'LOT-020','20','Lote 20','Calle Principal Lote 20','active',20,NULL,0.00,NULL,'2026-08-08 19:20:22','2026-08-08 19:20:22',NULL),(21,'LOT-021','21','Lote 21','Calle Principal Lote 21','active',21,NULL,0.00,NULL,'2026-08-08 19:20:22','2026-08-08 19:20:22',NULL),(22,'LOT-022','22','Lote 22','Calle Principal Lote 22','active',22,NULL,0.00,NULL,'2026-08-08 19:20:23','2026-08-08 19:20:23',NULL),(23,'LOT-023','23','Lote 23','Calle Principal Lote 23','active',23,NULL,0.00,NULL,'2026-08-08 19:20:23','2026-08-08 19:20:23',NULL),(24,'LOT-024','24','Lote 24','Calle Principal Lote 24','active',24,NULL,0.00,NULL,'2026-08-08 19:20:24','2026-08-08 19:20:24',NULL),(25,'LOT-025','25','Lote 25','Calle Principal Lote 25','active',25,NULL,0.00,NULL,'2026-08-08 19:20:24','2026-08-08 19:20:24',NULL),(26,'LOT-026','26','Lote 26','Calle Principal Lote 26','active',26,NULL,0.00,NULL,'2026-08-08 19:20:25','2026-08-08 19:20:25',NULL),(27,'LOT-027','27','Lote 27','Calle Principal Lote 27','active',27,NULL,0.00,NULL,'2026-08-08 19:20:25','2026-08-08 19:20:25',NULL),(28,'LOT-028','28','Lote 28','Calle Principal Lote 28','active',28,NULL,0.00,NULL,'2026-08-08 19:20:26','2026-08-08 19:20:26',NULL),(29,'LOT-029','29','Lote 29','Calle Principal Lote 29','active',29,NULL,0.00,NULL,'2026-08-08 19:20:26','2026-08-08 19:20:26',NULL),(30,'LOT-030','30','Lote 30','Calle Principal Lote 30','active',30,NULL,0.00,NULL,'2026-08-08 19:20:27','2026-08-08 19:20:27',NULL),(31,'LOT-031','31','Lote 31','Calle Principal Lote 31','active',31,NULL,0.00,NULL,'2026-08-08 19:20:27','2026-08-08 19:20:27',NULL),(32,'LOT-032','32','Lote 32','Calle Principal Lote 32','active',32,NULL,0.00,NULL,'2026-08-08 19:20:28','2026-08-08 19:20:28',NULL),(33,'LOT-033','33','Lote 33','Calle Principal Lote 33','active',33,NULL,0.00,NULL,'2026-08-08 19:20:28','2026-08-08 19:20:28',NULL),(34,'LOT-034','34','Lote 34','Calle Principal Lote 34','active',34,NULL,0.00,NULL,'2026-08-08 19:20:29','2026-08-08 19:20:29',NULL),(35,'LOT-035','35','Lote 35','Calle Principal Lote 35','active',35,NULL,0.00,NULL,'2026-08-08 19:20:29','2026-08-08 19:20:29',NULL),(36,'LOT-036','36','Lote 36','Calle Principal Lote 36','active',36,NULL,0.00,NULL,'2026-08-08 19:20:30','2026-08-08 19:20:30',NULL),(37,'LOT-037','37','Lote 37','Calle Principal Lote 37','active',37,NULL,0.00,NULL,'2026-08-08 19:20:30','2026-08-08 19:20:30',NULL),(38,'LOT-038','38','Lote 38','Calle Principal Lote 38','active',38,NULL,0.00,NULL,'2026-08-08 19:20:31','2026-08-08 19:20:31',NULL),(39,'LOT-039','39','Lote 39','Calle Principal Lote 39','active',39,NULL,0.00,NULL,'2026-08-08 19:20:31','2026-08-08 19:20:31',NULL),(40,'LOT-040','40','Lote 40','Calle Principal Lote 40','active',40,NULL,0.00,NULL,'2026-08-08 19:20:32','2026-08-08 19:20:32',NULL),(41,'LOT-041','41','Lote 41','Calle Principal Lote 41','active',41,NULL,0.00,NULL,'2026-08-08 19:20:32','2026-08-08 19:20:32',NULL),(42,'LOT-042','42','Lote 42','Calle Principal Lote 42','active',42,NULL,0.00,NULL,'2026-08-08 19:20:33','2026-08-08 19:20:33',NULL),(43,'LOT-043','43','Lote 43','Calle Principal Lote 43','active',43,NULL,0.00,NULL,'2026-08-08 19:20:33','2026-08-08 19:20:33',NULL),(44,'LOT-044','44','Lote 44','Calle Principal Lote 44','active',44,NULL,0.00,NULL,'2026-08-08 19:20:34','2026-08-08 19:20:34',NULL),(45,'LOT-045','45','Lote 45','Calle Principal Lote 45','active',45,NULL,0.00,NULL,'2026-08-08 19:20:34','2026-08-08 19:20:34',NULL),(46,'LOT-046','46','Lote 46','Calle Principal Lote 46','active',46,NULL,0.00,NULL,'2026-08-08 19:20:35','2026-08-08 19:20:35',NULL),(47,'LOT-047','47','Lote 47','Calle Principal Lote 47','active',47,NULL,0.00,NULL,'2026-08-08 19:20:35','2026-08-08 19:20:35',NULL),(48,'LOT-048','48','Lote 48','Calle Principal Lote 48','active',48,NULL,0.00,NULL,'2026-08-08 19:20:36','2026-08-08 19:20:36',NULL),(49,'LOT-049','49','Lote 49','Calle Principal Lote 49','active',49,NULL,0.00,NULL,'2026-08-08 19:20:36','2026-08-08 19:20:36',NULL),(50,'LOT-050','50','Lote 50','Calle Principal Lote 50','active',50,NULL,0.00,NULL,'2026-08-08 19:20:37','2026-08-08 19:20:37',NULL),(51,'LOT-051','51','Lote 51','Calle Principal Lote 51','active',31,NULL,0.00,NULL,'2026-08-08 19:20:37','2026-08-08 19:20:37',NULL),(52,'LOT-052','52','Lote 52','Calle Principal Lote 52','active',51,NULL,0.00,NULL,'2026-08-08 19:20:37','2026-08-08 19:20:37',NULL),(53,'LOT-053','53','Lote 53','Calle Principal Lote 53','active',52,NULL,0.00,NULL,'2026-08-08 19:20:38','2026-08-08 19:20:38',NULL),(54,'LOT-054','54','Lote 54','Calle Principal Lote 54','active',53,NULL,0.00,NULL,'2026-08-08 19:20:38','2026-08-08 19:20:38',NULL),(55,'LOT-055','55','Lote 55','Calle Principal Lote 55','active',54,NULL,0.00,NULL,'2026-08-08 19:20:39','2026-08-08 19:20:39',NULL),(56,'LOT-056','56','Lote 56','Calle Principal Lote 56','active',55,NULL,0.00,NULL,'2026-08-08 19:20:39','2026-08-08 19:20:39',NULL),(57,'LOT-057','57','Lote 57','Calle Principal Lote 57','active',56,NULL,0.00,NULL,'2026-08-08 19:20:40','2026-08-08 19:20:40',NULL),(58,'LOT-058','58','Lote 58','Calle Principal Lote 58','active',57,NULL,0.00,NULL,'2026-08-08 19:20:40','2026-08-08 19:20:40',NULL),(59,'LOT-059','59','Lote 59','Calle Principal Lote 59','active',58,NULL,0.00,NULL,'2026-08-08 19:20:41','2026-08-08 19:20:41',NULL),(60,'LOT-060','60','Lote 60','Calle Principal Lote 60','active',59,NULL,0.00,NULL,'2026-08-08 19:20:41','2026-08-08 19:20:41',NULL),(61,'LOT-061','61','Lote 61','Calle Principal Lote 61','active',60,NULL,0.00,NULL,'2026-08-08 19:20:42','2026-08-08 19:20:42',NULL),(62,'LOT-062','62','Lote 62','Calle Principal Lote 62','active',61,NULL,0.00,NULL,'2026-08-08 19:20:42','2026-08-08 19:20:42',NULL),(63,'LOT-063','63','Lote 63','Calle Principal Lote 63','active',62,NULL,0.00,NULL,'2026-08-08 19:20:43','2026-08-08 19:20:43',NULL),(64,'LOT-064','64','Lote 64','Calle Principal Lote 64','active',63,NULL,0.00,NULL,'2026-08-08 19:20:43','2026-08-08 19:20:43',NULL),(65,'LOT-065','65','Lote 65','Calle Principal Lote 65','active',64,NULL,0.00,NULL,'2026-08-08 19:20:43','2026-08-08 19:20:43',NULL),(66,'LOT-066','66','Lote 66','Calle Principal Lote 66','active',65,NULL,0.00,NULL,'2026-08-08 19:20:44','2026-08-08 19:20:44',NULL),(67,'LOT-067','67','Lote 67','Calle Principal Lote 67','active',66,NULL,0.00,NULL,'2026-08-08 19:20:44','2026-08-08 19:20:44',NULL),(68,'LOT-068','68','Lote 68','Calle Principal Lote 68','active',66,NULL,0.00,NULL,'2026-08-08 19:20:45','2026-08-08 19:20:45',NULL),(69,'LOT-069','69','Lote 69','Calle Principal Lote 69','active',67,NULL,0.00,NULL,'2026-08-08 19:20:45','2026-08-08 19:20:45',NULL),(70,'LOT-070','70','Lote 70','Calle Principal Lote 70','active',68,NULL,0.00,NULL,'2026-08-08 19:20:45','2026-08-08 19:20:45',NULL),(71,'LOT-071','71','Lote 71','Calle Principal Lote 71','active',69,NULL,0.00,NULL,'2026-08-08 19:20:46','2026-08-08 19:20:46',NULL),(72,'LOT-072','72','Lote 72','Calle Principal Lote 72','active',69,NULL,0.00,NULL,'2026-08-08 19:20:46','2026-08-08 19:20:46',NULL),(73,'LOT-073','73','Lote 73','Calle Principal Lote 73','active',31,NULL,0.00,NULL,'2026-08-08 19:20:46','2026-08-08 19:20:46',NULL),(74,'LOT-074','74','Lote 74','Calle Principal Lote 74','active',70,NULL,0.00,NULL,'2026-08-08 19:20:46','2026-08-08 19:20:46',NULL),(75,'LOT-075','75','Lote 75','Calle Principal Lote 75','active',71,NULL,0.00,NULL,'2026-08-08 19:20:47','2026-08-08 19:20:47',NULL),(76,'LOT-076','76','Lote 76','Calle Principal Lote 76','active',72,NULL,0.00,NULL,'2026-08-08 19:20:47','2026-08-08 19:20:47',NULL),(77,'LOT-077','77','Lote 77','Calle Principal Lote 77','active',73,NULL,0.00,NULL,'2026-08-08 19:20:48','2026-08-08 19:20:48',NULL),(78,'LOT-078','78','Lote 78','Calle Principal Lote 78','active',74,NULL,0.00,NULL,'2026-08-08 19:20:48','2026-08-08 19:20:48',NULL),(79,'LOT-079','79','Lote 79','Calle Principal Lote 79','active',75,NULL,0.00,NULL,'2026-08-08 19:20:49','2026-08-08 19:20:49',NULL),(80,'LOT-080','80','Lote 80','Calle Principal Lote 80','active',76,NULL,0.00,NULL,'2026-08-08 19:20:49','2026-08-08 19:20:49',NULL),(81,'LOT-081','81','Lote 81','Calle Principal Lote 81','active',77,NULL,0.00,NULL,'2026-08-08 19:20:50','2026-08-08 19:20:50',NULL),(82,'LOT-082','82','Lote 82','Calle Principal Lote 82','active',78,NULL,0.00,NULL,'2026-08-08 19:20:50','2026-08-08 19:20:50',NULL),(83,'LOT-083','83','Lote 83','Calle Principal Lote 83','active',79,NULL,0.00,NULL,'2026-08-08 19:20:51','2026-08-08 19:20:51',NULL),(84,'LOT-084','84','Lote 84','Calle Principal Lote 84','active',80,NULL,0.00,NULL,'2026-08-08 19:20:51','2026-08-08 19:20:51',NULL),(85,'LOT-085','85','Lote 85','Calle Principal Lote 85','active',81,NULL,0.00,NULL,'2026-08-08 19:20:52','2026-08-08 19:20:52',NULL),(86,'LOT-086','86','Lote 86','Calle Principal Lote 86','active',81,NULL,0.00,NULL,'2026-08-08 19:20:52','2026-08-08 19:20:52',NULL),(87,'LOT-087','87','Lote 87','Calle Principal Lote 87','active',82,NULL,0.00,NULL,'2026-08-08 19:20:52','2026-08-08 19:20:52',NULL),(88,'LOT-088','88','Lote 88','Calle Principal Lote 88','active',83,NULL,0.00,NULL,'2026-08-08 19:20:53','2026-08-08 19:20:53',NULL),(89,'LOT-089','89','Lote 89','Calle Principal Lote 89','active',84,NULL,0.00,NULL,'2026-08-08 19:20:53','2026-08-08 19:20:53',NULL),(90,'LOT-090','90','Lote 90','Calle Principal Lote 90','active',85,NULL,0.00,NULL,'2026-08-08 19:20:54','2026-08-08 19:20:54',NULL),(91,'LOT-091','91','Lote 91','Calle Principal Lote 91','active',31,NULL,0.00,NULL,'2026-08-08 19:20:54','2026-08-08 19:20:54',NULL),(92,'LOT-092','92','Lote 92','Calle Principal Lote 92','active',86,NULL,0.00,NULL,'2026-08-08 19:20:54','2026-08-08 19:20:54',NULL),(93,'LOT-093','93','Lote 93','Calle Principal Lote 93','active',87,NULL,0.00,NULL,'2026-08-08 19:20:55','2026-08-08 19:20:55',NULL),(94,'LOT-094','94','Lote 94','Calle Principal Lote 94','active',88,NULL,0.00,NULL,'2026-08-08 19:20:56','2026-08-08 19:20:56',NULL),(95,'LOT-095','95','Lote 95','Calle Principal Lote 95','active',89,NULL,0.00,NULL,'2026-08-08 19:20:56','2026-08-08 19:20:56',NULL),(96,'LOT-096','96','Lote 96','Calle Principal Lote 96','active',89,NULL,0.00,NULL,'2026-08-08 19:20:56','2026-08-08 19:20:56',NULL),(97,'LOT-097','97','Lote 97','Calle Principal Lote 97','active',90,NULL,0.00,NULL,'2026-08-08 19:20:57','2026-08-08 19:20:57',NULL),(98,'LOT-098','98','Lote 98','Calle Principal Lote 98','active',91,NULL,0.00,NULL,'2026-08-08 19:20:57','2026-08-08 19:20:57',NULL),(99,'LOT-099','99','Lote 99','Calle Principal Lote 99','active',92,NULL,0.00,NULL,'2026-08-08 19:20:58','2026-08-08 19:20:58',NULL),(100,'LOT-100','100','Lote 100','Calle Principal Lote 100','active',93,NULL,0.00,NULL,'2026-08-08 19:20:58','2026-08-08 19:20:58',NULL),(101,'LOT-101','101','Lote 101','Calle Principal Lote 101','active',94,NULL,0.00,NULL,'2026-08-08 19:20:58','2026-08-08 19:20:58',NULL),(102,'LOT-102','102','Lote 102','Calle Principal Lote 102','active',95,NULL,0.00,NULL,'2026-08-08 19:20:59','2026-08-08 19:20:59',NULL),(103,'LOT-103','103','Lote 103','Calle Principal Lote 103','active',96,NULL,0.00,NULL,'2026-08-08 19:20:59','2026-08-08 19:20:59',NULL),(104,'LOT-104','104','Lote 104','Calle Principal Lote 104','active',97,NULL,0.00,NULL,'2026-08-08 19:21:00','2026-08-08 19:21:00',NULL),(105,'LOT-105','105','Lote 105','Calle Principal Lote 105','active',98,NULL,0.00,NULL,'2026-08-08 19:21:01','2026-08-08 19:21:01',NULL),(106,'LOT-106','106','Lote 106','Calle Principal Lote 106','active',99,NULL,0.00,NULL,'2026-08-08 19:21:01','2026-08-08 19:21:01',NULL),(107,'LOT-107','107','Lote 107','Calle Principal Lote 107','active',100,NULL,0.00,NULL,'2026-08-08 19:21:01','2026-08-08 19:21:01',NULL),(108,'LOT-108','108','Lote 108','Calle Principal Lote 108','active',101,NULL,0.00,NULL,'2026-08-08 19:21:02','2026-08-08 19:21:02',NULL),(109,'LOT-109','109','Lote 109','Calle Principal Lote 109','active',102,NULL,0.00,NULL,'2026-08-08 19:21:02','2026-08-08 19:21:02',NULL),(110,'LOT-110','110','Lote 110','Calle Principal Lote 110','active',103,NULL,0.00,NULL,'2026-08-08 19:21:03','2026-08-08 19:21:03',NULL),(111,'LOT-111','111','Lote 111','Calle Principal Lote 111','active',104,NULL,0.00,NULL,'2026-08-08 19:21:03','2026-08-08 19:21:03',NULL),(112,'LOT-112','112','Lote 112','Calle Principal Lote 112','active',105,NULL,0.00,NULL,'2026-08-08 19:21:04','2026-08-08 19:21:04',NULL),(113,'LOT-113','113','Lote 113','Calle Principal Lote 113','active',106,NULL,0.00,NULL,'2026-08-08 19:21:04','2026-08-08 19:21:04',NULL),(114,'LOT-114','114','Lote 114','Calle Principal Lote 114','active',107,NULL,0.00,NULL,'2026-08-08 19:21:05','2026-08-08 19:21:05',NULL),(115,'LOT-115','115','Lote 115','Calle Principal Lote 115','active',108,NULL,0.00,NULL,'2026-08-08 19:21:05','2026-08-08 19:21:05',NULL),(116,'LOT-116','116','Lote 116','Calle Principal Lote 116','active',109,NULL,0.00,NULL,'2026-08-08 19:21:06','2026-08-08 19:21:06',NULL),(117,'LOT-117','117','Lote 117','Calle Principal Lote 117','active',110,NULL,0.00,NULL,'2026-08-08 19:21:06','2026-08-08 19:21:06',NULL),(118,'LOT-118','118','Lote 118','Calle Principal Lote 118','active',111,NULL,0.00,NULL,'2026-08-08 19:21:07','2026-08-08 19:21:07',NULL),(119,'LOT-119','119','Lote 119','Calle Principal Lote 119','active',112,NULL,0.00,NULL,'2026-08-08 19:21:07','2026-08-08 19:21:07',NULL),(120,'LOT-120','120','Lote 120','Calle Principal Lote 120','active',113,NULL,0.00,NULL,'2026-08-08 19:21:08','2026-08-08 19:21:08',NULL),(121,'LOT-121','121','Lote 121','Calle Principal Lote 121','active',114,NULL,0.00,NULL,'2026-08-08 19:21:08','2026-08-08 19:21:08',NULL),(122,'LOT-122','122','Lote 122','Calle Principal Lote 122','active',115,NULL,0.00,NULL,'2026-08-08 19:21:09','2026-08-08 19:21:09',NULL),(123,'LOT-123','123','Lote 123','Calle Principal Lote 123','active',115,NULL,0.00,NULL,'2026-08-08 19:21:09','2026-08-08 19:21:09',NULL),(124,'LOT-124','124','Lote 124','Calle Principal Lote 124','active',116,NULL,0.00,NULL,'2026-08-08 19:21:09','2026-08-08 19:21:09',NULL),(125,'LOT-125','125','Lote 125','Calle Principal Lote 125','active',117,NULL,0.00,NULL,'2026-08-08 19:21:10','2026-08-08 19:21:10',NULL),(126,'LOT-126','126','Lote 126','Calle Principal Lote 126','active',118,NULL,0.00,NULL,'2026-08-08 19:21:10','2026-08-08 19:21:10',NULL),(127,'LOT-127','127','Lote 127','Calle Principal Lote 127','active',119,NULL,0.00,NULL,'2026-08-08 19:21:11','2026-08-08 19:21:11',NULL),(128,'LOT-128','128','Lote 128','Calle Principal Lote 128','active',120,NULL,0.00,NULL,'2026-08-08 19:21:11','2026-08-08 19:21:11',NULL),(129,'LOT-129','129','Lote 129','Calle Principal Lote 129','active',121,NULL,0.00,NULL,'2026-08-08 19:21:12','2026-08-08 19:21:12',NULL),(130,'LOT-130','130','Lote 130','Calle Principal Lote 130','active',122,NULL,0.00,NULL,'2026-08-08 19:21:12','2026-08-08 19:21:12',NULL),(131,'LOT-131','131','Lote 131','Calle Principal Lote 131','active',123,NULL,0.00,NULL,'2026-08-08 19:21:13','2026-08-08 19:21:13',NULL);
/*!40000 ALTER TABLE `lots` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_08_05_000001_create_roles_and_permissions_tables',1),(5,'2026_08_05_000002_create_owners_tenants_lots_tables',1),(6,'2026_08_05_000003_create_billing_and_finance_tables',1),(7,'2026_08_05_000004_create_tickets_and_reclamos_tables',1),(8,'2026_08_05_000005_create_news_documents_communications_tables',1),(9,'2026_08_05_000006_create_settings_logs_and_audits_tables',1),(10,'2026_08_05_000007_create_lot_history_and_followups_tables',1),(11,'2026_08_07_212526_create_portal_features_tables',1),(12,'2026_08_08_000001_create_suppliers_and_invoices_tables',1),(13,'2026_08_09_000001_create_notifications_table',2),(14,'2026_08_10_135709_update_payments_and_allocations_for_reconciliation',3),(15,'2026_08_10_140052_add_reversion_fields_to_payment_allocations',4),(16,'2026_08_10_144003_add_is_exclusive_to_reservations_table',5);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `news`
--

DROP TABLE IF EXISTS `news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `news` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `summary` varchar(255) DEFAULT NULL,
  `content` text NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'draft',
  `visibility` varchar(255) NOT NULL DEFAULT 'public',
  `recipients_type` varchar(255) NOT NULL DEFAULT 'all',
  `publish_date` timestamp NULL DEFAULT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `channels` varchar(255) NOT NULL DEFAULT 'portal',
  `is_published` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `news`
--

LOCK TABLES `news` WRITE;
/*!40000 ALTER TABLE `news` DISABLE KEYS */;
/*!40000 ALTER TABLE `news` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'general',
  `link` varchar(255) DEFAULT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_user_id_foreign` (`user_id`),
  CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (1,1,'Nueva Reserva Registrada','Lote 1 reservó Quincho para el 11/08/2026 (14:00 hs)','reservation','http://192.168.2.145:8000/admin/reservations','2026-08-10 20:55:23','2026-08-10 18:56:49','2026-08-10 20:55:23'),(2,2,'Nueva Reserva Registrada','Lote 1 reservó Quincho para el 11/08/2026 (14:00 hs)','reservation','http://192.168.2.145:8000/admin/reservations',NULL,'2026-08-10 18:56:49','2026-08-10 18:56:49'),(3,3,'Nueva Reserva Registrada','Lote 1 reservó Quincho para el 11/08/2026 (14:00 hs)','reservation','http://192.168.2.145:8000/admin/reservations',NULL,'2026-08-10 18:56:49','2026-08-10 18:56:49'),(4,5,'Nueva Reserva Registrada','Lote 1 reservó Quincho para el 11/08/2026 (14:00 hs)','reservation','http://192.168.2.145:8000/admin/reservations',NULL,'2026-08-10 18:56:49','2026-08-10 18:56:49');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `owner_functional_unit`
--

DROP TABLE IF EXISTS `owner_functional_unit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `owner_functional_unit` (
  `owner_id` bigint(20) unsigned NOT NULL,
  `functional_unit_id` bigint(20) unsigned NOT NULL,
  `share_percentage` decimal(5,2) NOT NULL DEFAULT 100.00,
  PRIMARY KEY (`owner_id`,`functional_unit_id`),
  KEY `owner_functional_unit_functional_unit_id_foreign` (`functional_unit_id`),
  CONSTRAINT `owner_functional_unit_functional_unit_id_foreign` FOREIGN KEY (`functional_unit_id`) REFERENCES `functional_units` (`id`) ON DELETE CASCADE,
  CONSTRAINT `owner_functional_unit_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `owners` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `owner_functional_unit`
--

LOCK TABLES `owner_functional_unit` WRITE;
/*!40000 ALTER TABLE `owner_functional_unit` DISABLE KEYS */;
INSERT INTO `owner_functional_unit` VALUES (1,1,100.00),(2,2,100.00),(3,3,100.00),(4,4,100.00),(5,5,100.00),(6,6,100.00),(7,7,100.00),(8,8,100.00),(9,9,100.00),(10,10,100.00),(11,11,100.00),(12,12,100.00),(13,13,100.00),(14,14,100.00),(15,15,100.00),(16,16,100.00),(17,17,100.00),(18,18,100.00),(19,19,100.00),(20,20,100.00),(21,21,100.00),(22,22,100.00),(23,23,100.00),(24,24,100.00),(25,25,100.00),(26,26,100.00),(27,27,100.00),(28,28,100.00),(29,29,100.00),(30,30,100.00),(31,31,100.00),(31,51,100.00),(31,73,100.00),(31,91,100.00),(32,32,100.00),(33,33,100.00),(34,34,100.00),(35,35,100.00),(36,36,100.00),(37,37,100.00),(38,38,100.00),(39,39,100.00),(40,40,100.00),(41,41,100.00),(42,42,100.00),(43,43,100.00),(44,44,100.00),(45,45,100.00),(46,46,100.00),(47,47,100.00),(48,48,100.00),(49,49,100.00),(50,50,100.00),(51,52,100.00),(52,53,100.00),(53,54,100.00),(54,55,100.00),(55,56,100.00),(56,57,100.00),(57,58,100.00),(58,59,100.00),(59,60,100.00),(60,61,100.00),(61,62,100.00),(62,63,100.00),(63,64,100.00),(64,65,100.00),(65,66,100.00),(66,67,100.00),(66,68,100.00),(67,69,100.00),(68,70,100.00),(69,71,100.00),(69,72,100.00),(70,74,100.00),(71,75,100.00),(72,76,100.00),(73,77,100.00),(74,78,100.00),(75,79,100.00),(76,80,100.00),(77,81,100.00),(78,82,100.00),(79,83,100.00),(80,84,100.00),(81,85,100.00),(81,86,100.00),(82,87,100.00),(83,88,100.00),(84,89,100.00),(85,90,100.00),(86,92,100.00),(87,93,100.00),(88,94,100.00),(89,95,100.00),(89,96,100.00),(90,97,100.00),(91,98,100.00),(92,99,100.00),(93,100,100.00),(94,101,100.00),(95,102,100.00),(96,103,100.00),(97,104,100.00),(98,105,100.00),(99,106,100.00),(100,107,100.00),(101,108,100.00),(102,109,100.00),(103,110,100.00),(104,111,100.00),(105,112,100.00),(106,113,100.00),(107,114,100.00),(108,115,100.00),(109,116,100.00),(110,117,100.00),(111,118,100.00),(112,119,100.00),(113,120,100.00),(114,121,100.00),(115,122,100.00),(115,123,100.00),(116,124,100.00),(117,125,100.00),(118,126,100.00),(119,127,100.00),(120,128,100.00),(121,129,100.00),(122,130,100.00),(123,131,100.00);
/*!40000 ALTER TABLE `owner_functional_unit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `owners`
--

DROP TABLE IF EXISTS `owners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `owners` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `business_name` varchar(255) DEFAULT NULL,
  `dni` varchar(255) DEFAULT NULL,
  `cuit` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `email_alternate` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `phone_alternate` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `preferred_channel` varchar(255) NOT NULL DEFAULT 'email',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `owners_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=124 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `owners`
--

LOCK TABLES `owners` WRITE;
/*!40000 ALTER TABLE `owners` DISABLE KEYS */;
INSERT INTO `owners` VALUES (1,'OLGA','GARCIA',NULL,'22466042','20-22466042-3','o.garcia@laranita.com',NULL,NULL,NULL,'Lote 1','active',NULL,'email','2026-08-08 19:20:12','2026-08-08 19:20:12',NULL),(2,'SILVERA','AGUSTIN',NULL,'37597336','20-37597336-3','s.agustin@laranita.com',NULL,NULL,NULL,'Lote 2','active',NULL,'email','2026-08-08 19:20:13','2026-08-08 19:20:13',NULL),(3,'Alejandro','Moliné',NULL,'37097918','20-37097918-2','a.moline@laranita.com',NULL,NULL,NULL,'Lote 3','active',NULL,'email','2026-08-08 19:20:13','2026-08-08 19:20:13',NULL),(4,'GISELE - En Legales','LUCERO',NULL,'45265781','20-45265781-3','g.lucero@laranita.com',NULL,NULL,NULL,'Lote 4','active',NULL,'email','2026-08-08 19:20:14','2026-08-08 19:20:14',NULL),(5,'MANGONE','NESTOR',NULL,'29442500','20-29442500-0','m.nestor@laranita.com',NULL,NULL,NULL,'Lote 5','active',NULL,'email','2026-08-08 19:20:14','2026-08-08 19:20:14',NULL),(6,'ALEJANDRA','MARANDOLA',NULL,'16266816','20-16266816-2','a.marandola@laranita.com',NULL,NULL,NULL,'Lote 6','active',NULL,'email','2026-08-08 19:20:15','2026-08-08 19:20:15',NULL),(7,'GRACIELA En legales','SANTANA',NULL,'38511326','20-38511326-7','g.santana@laranita.com',NULL,NULL,NULL,'Lote 7','active',NULL,'email','2026-08-08 19:20:15','2026-08-08 19:20:15',NULL),(8,'FERNANDO','CLARO',NULL,'38364413','20-38364413-4','f.claro@laranita.com',NULL,NULL,NULL,'Lote 8','active',NULL,'email','2026-08-08 19:20:16','2026-08-08 19:20:16',NULL),(9,'ARANDA','HERNAN',NULL,'29630048','20-29630048-8','a.hernan@laranita.com',NULL,NULL,NULL,'Lote 9','active',NULL,'email','2026-08-08 19:20:16','2026-08-08 19:20:16',NULL),(10,'RUBEN','CANTELMI',NULL,'36575858','20-36575858-6','r.cantelmi@laranita.com',NULL,NULL,NULL,'Lote 10','inactive',NULL,'email','2026-08-08 19:20:17','2026-08-08 19:20:17',NULL),(11,'EDGAR','ROGGENBAU',NULL,'33817502','20-33817502-3','e.roggenbau@laranita.com',NULL,NULL,NULL,'Lote 11','active',NULL,'email','2026-08-08 19:20:17','2026-08-08 19:20:17',NULL),(12,'RIBERA','JOAQUIN',NULL,'37282817','20-37282817-3','r.joaquin@laranita.com',NULL,NULL,NULL,'Lote 12','active',NULL,'email','2026-08-08 19:20:18','2026-08-08 19:20:18',NULL),(13,'BENETTI','NORMA - Legales',NULL,'27796819','20-27796819-2','b.normalegales@laranita.com',NULL,NULL,NULL,'Lote 13','active',NULL,'email','2026-08-08 19:20:18','2026-08-08 19:20:18',NULL),(14,'SANTORA','ANA',NULL,'27302232','20-27302232-5','s.ana@laranita.com',NULL,NULL,NULL,'Lote 14','active',NULL,'email','2026-08-08 19:20:19','2026-08-08 19:20:19',NULL),(15,'ARIAS','STELLA MARIS',NULL,'31073266','20-31073266-4','a.stellamaris@laranita.com',NULL,NULL,NULL,'Lote 15','active',NULL,'email','2026-08-08 19:20:19','2026-08-08 19:20:19',NULL),(16,'DIEGO','GANTUS',NULL,'36957351','20-36957351-1','d.gantus@laranita.com',NULL,NULL,NULL,'Lote 16','active',NULL,'email','2026-08-08 19:20:20','2026-08-08 19:20:20',NULL),(17,'GUILLERMO','ARMENTANO',NULL,'12822748','20-12822748-2','g.armentano@laranita.com',NULL,NULL,NULL,'Lote 17','active',NULL,'email','2026-08-08 19:20:20','2026-08-08 19:20:20',NULL),(18,'MONICA','FARAH',NULL,'24686173','20-24686173-5','m.farah@laranita.com',NULL,NULL,NULL,'Lote 18','active',NULL,'email','2026-08-08 19:20:21','2026-08-08 19:20:21',NULL),(19,'FOSINI','MATIAS',NULL,'18334049','20-18334049-4','f.matias@laranita.com',NULL,NULL,NULL,'Lote 19','active',NULL,'email','2026-08-08 19:20:21','2026-08-08 19:20:21',NULL),(20,'OMAR','GRANDE',NULL,'25668193','20-25668193-2','o.grande@laranita.com',NULL,NULL,NULL,'Lote 20','active',NULL,'email','2026-08-08 19:20:22','2026-08-08 19:20:22',NULL),(21,'Ma Belén/MADEO, Lucas Nicolas','CANO CODECÁ',NULL,'30307136','20-30307136-9','m.canocodeca@laranita.com',NULL,'11 2650 5916',NULL,'Lote 21','active',NULL,'both','2026-08-08 19:20:22','2026-08-08 19:20:22',NULL),(22,'SEMBEROIZ','PABLO',NULL,'10341054','20-10341054-7','s.pablo@laranita.com',NULL,NULL,NULL,'Lote 22','active',NULL,'email','2026-08-08 19:20:23','2026-08-08 19:20:23',NULL),(23,'MARTIN','BERNAL / DOLORES CASAL',NULL,'13520961','20-13520961-1','m.bernaldolorescasal@laranita.com',NULL,NULL,NULL,'Lote 23','active',NULL,'email','2026-08-08 19:20:23','2026-08-08 19:20:23',NULL),(24,'BENVENUTO','Jorge / BASUALDO María',NULL,'45819015','20-45819015-3','b.jorgebasualdomaria@laranita.com',NULL,NULL,NULL,'Lote 24','active',NULL,'email','2026-08-08 19:20:24','2026-08-08 19:20:24',NULL),(25,'MARCELA','PRADELLS',NULL,'45331679','20-45331679-7','m.pradells@laranita.com',NULL,NULL,NULL,'Lote 25','active',NULL,'email','2026-08-08 19:20:24','2026-08-08 19:20:24',NULL),(26,'GERMAN','PLESSEN',NULL,'38254160','20-38254160-1','g.plessen@laranita.com',NULL,NULL,NULL,'Lote 26','active',NULL,'email','2026-08-08 19:20:25','2026-08-08 19:20:25',NULL),(27,'VANESA','JUAREZ',NULL,'10127373','20-10127373-2','v.juarez@laranita.com',NULL,NULL,NULL,'Lote 27','active',NULL,'email','2026-08-08 19:20:25','2026-08-08 19:20:25',NULL),(28,'LEONARDO','MAGLIOCCO',NULL,'41720192','20-41720192-9','l.magliocco@laranita.com',NULL,NULL,NULL,'Lote 28','active',NULL,'email','2026-08-08 19:20:26','2026-08-08 19:20:26',NULL),(29,'FERNANDO','PRATI',NULL,'30554940','20-30554940-6','f.prati@laranita.com',NULL,NULL,NULL,'Lote 29','active',NULL,'email','2026-08-08 19:20:26','2026-08-08 19:20:26',NULL),(30,'DIEGO ANDRES','SEFERCHEOGLOU',NULL,'39783204','20-39783204-9','d.sefercheoglou@laranita.com',NULL,NULL,NULL,'Lote 30','active',NULL,'email','2026-08-08 19:20:27','2026-08-08 19:20:27',NULL),(31,'MARIO En Juicio','ESPARRICA',NULL,'18590202','20-18590202-9','m.esparrica@laranita.com',NULL,NULL,NULL,'Lote 31','active',NULL,'email','2026-08-08 19:20:27','2026-08-08 19:20:27',NULL),(32,'Agustín','PIEDRABUENA',NULL,'26990458','20-26990458-5','a.piedrabuena@laranita.com',NULL,NULL,NULL,'Lote 32','active',NULL,'email','2026-08-08 19:20:28','2026-08-08 19:20:28',NULL),(33,'MARIA L - Acuerdo','MONACCI',NULL,'33182763','20-33182763-7','m.monacci@laranita.com',NULL,NULL,NULL,'Lote 33','active',NULL,'email','2026-08-08 19:20:28','2026-08-08 19:20:28',NULL),(34,'EZEQUIEL/ MONTORO, LUCAS Legales','MONTORO',NULL,'35945294','20-35945294-7','e.montoro@laranita.com',NULL,NULL,NULL,'Lote 34','active',NULL,'email','2026-08-08 19:20:29','2026-08-08 19:20:29',NULL),(35,'BAEZ','FLORENCIA',NULL,'28976095','20-28976095-9','b.florencia@laranita.com',NULL,NULL,NULL,'Lote 35','active',NULL,'email','2026-08-08 19:20:29','2026-08-08 19:20:29',NULL),(36,'JUAN CARLOS','MENDOZA',NULL,'30397781','20-30397781-5','j.mendoza@laranita.com',NULL,NULL,NULL,'Lote 36','active',NULL,'email','2026-08-08 19:20:30','2026-08-08 19:20:30',NULL),(37,'LEONARDO','CANARIO',NULL,'13249589','20-13249589-2','l.canario@laranita.com',NULL,NULL,NULL,'Lote 37','active',NULL,'email','2026-08-08 19:20:30','2026-08-08 19:20:30',NULL),(38,'CARINA Y SATRIANO','VIETA',NULL,'13535681','20-13535681-2','c.vieta@laranita.com',NULL,NULL,NULL,'Lote 38','active',NULL,'email','2026-08-08 19:20:31','2026-08-08 19:20:31',NULL),(39,'RAMIRO','DEL MORAL',NULL,'31190826','20-31190826-2','r.delmoral@laranita.com',NULL,NULL,NULL,'Lote 39','active',NULL,'email','2026-08-08 19:20:31','2026-08-08 19:20:31',NULL),(40,'ALFREDO','ROSSI',NULL,'43241420','20-43241420-1','a.rossi@laranita.com',NULL,NULL,NULL,'Lote 40','active',NULL,'email','2026-08-08 19:20:32','2026-08-08 19:20:32',NULL),(41,'CLAUDIO M.','FERNANDEZ',NULL,'41537537','20-41537537-6','c.fernandez@laranita.com',NULL,NULL,NULL,'Lote 41','active',NULL,'email','2026-08-08 19:20:32','2026-08-08 19:20:32',NULL),(42,'DANIELA ANA','PETKOVSEK',NULL,'33232960','20-33232960-0','d.petkovsek@laranita.com',NULL,NULL,NULL,'Lote 42','active',NULL,'email','2026-08-08 19:20:33','2026-08-08 19:20:33',NULL),(43,'SEBASTIAN','MARCHESI',NULL,'12116724','20-12116724-0','s.marchesi@laranita.com',NULL,'(011) 15 4447 0155',NULL,'Lote 43','active',NULL,'both','2026-08-08 19:20:33','2026-08-08 19:20:33',NULL),(44,'MARIA','GONZALEZ STEGEMANN',NULL,'40060489','20-40060489-1','m.gonzalezstegemann@laranita.com',NULL,NULL,NULL,'Lote 44','active',NULL,'email','2026-08-08 19:20:34','2026-08-08 19:20:34',NULL),(45,'SANDRA','PINZON GARZON',NULL,'19797105','20-19797105-7','s.pinzongarzon@laranita.com',NULL,NULL,NULL,'Lote 45','active',NULL,'email','2026-08-08 19:20:34','2026-08-08 19:20:34',NULL),(46,'FERNANDEZ','ANSELMI MELINA',NULL,'27346841','20-27346841-7','f.anselmimelina@laranita.com',NULL,NULL,NULL,'Lote 46','active',NULL,'email','2026-08-08 19:20:35','2026-08-08 19:20:35',NULL),(47,'ALFREDO','ORDUÑA',NULL,'25189002','20-25189002-3','a.orduna@laranita.com',NULL,NULL,NULL,'Lote 47','active',NULL,'email','2026-08-08 19:20:35','2026-08-08 19:20:35',NULL),(48,'AUDISIO','IGNACIO',NULL,'37486417','20-37486417-0','a.ignacio@laranita.com',NULL,NULL,NULL,'Lote 48','active',NULL,'email','2026-08-08 19:20:36','2026-08-08 19:20:36',NULL),(49,'WILLIAM','PHILPOTT',NULL,'25315364','20-25315364-9','w.philpott@laranita.com',NULL,NULL,NULL,'Lote 49','active',NULL,'email','2026-08-08 19:20:36','2026-08-08 19:20:36',NULL),(50,'GERARDO','RUZO',NULL,'27622258','20-27622258-3','g.ruzo@laranita.com',NULL,NULL,NULL,'Lote 50','active',NULL,'email','2026-08-08 19:20:37','2026-08-08 19:20:37',NULL),(51,'Gagliardini','Facundo',NULL,'30523593','20-30523593-3','g.facundo@laranita.com',NULL,NULL,NULL,'Lote 52','active',NULL,'email','2026-08-08 19:20:37','2026-08-08 19:20:37',NULL),(52,'FOGLIA','FRANCISCO',NULL,'38332377','20-38332377-1','f.francisco@laranita.com',NULL,NULL,NULL,'Lote 53','active',NULL,'email','2026-08-08 19:20:38','2026-08-08 19:20:38',NULL),(53,'ALFREDO','COBOS',NULL,'23750420','20-23750420-3','a.cobos@laranita.com',NULL,NULL,NULL,'Lote 54','active',NULL,'email','2026-08-08 19:20:38','2026-08-08 19:20:38',NULL),(54,'OSCAR MARIO','PANGARO',NULL,'30632978','20-30632978-7','o.pangaro@laranita.com',NULL,'(011) 15 6159 0624',NULL,'Lote 55','active',NULL,'both','2026-08-08 19:20:39','2026-08-08 19:20:39',NULL),(55,'DIEGO','FUSTER',NULL,'22563766','20-22563766-7','d.fuster@laranita.com',NULL,NULL,NULL,'Lote 56','active',NULL,'email','2026-08-08 19:20:39','2026-08-08 19:20:39',NULL),(56,'MARTIN','SUSBIELLES',NULL,'22372689','20-22372689-0','m.susbielles@laranita.com',NULL,NULL,NULL,'Lote 57','active',NULL,'email','2026-08-08 19:20:40','2026-08-08 19:20:40',NULL),(57,'WALTER','LABONIA',NULL,'17883717','20-17883717-6','w.labonia@laranita.com',NULL,NULL,NULL,'Lote 58','active',NULL,'email','2026-08-08 19:20:40','2026-08-08 19:20:40',NULL),(58,'JOSE','BERTOTTI',NULL,'45263873','20-45263873-5','j.bertotti@laranita.com',NULL,NULL,NULL,'Lote 59','active',NULL,'email','2026-08-08 19:20:41','2026-08-08 19:20:41',NULL),(59,'SILVINA','TORELLA',NULL,'15362927','20-15362927-8','s.torella@laranita.com',NULL,NULL,NULL,'Lote 60','active',NULL,'email','2026-08-08 19:20:41','2026-08-08 19:20:41',NULL),(60,'PABLO','VERNENGO',NULL,'21639411','20-21639411-5','p.vernengo@laranita.com',NULL,NULL,NULL,'Lote 61','active',NULL,'email','2026-08-08 19:20:42','2026-08-08 19:20:42',NULL),(61,'OTERO','/ ALBORNOZ',NULL,'34793659','20-34793659-8','o.albornoz@laranita.com',NULL,NULL,NULL,'Lote 62','active',NULL,'email','2026-08-08 19:20:42','2026-08-08 19:20:42',NULL),(62,'MARCELO En Juicio','LEONARDI',NULL,'38660660','20-38660660-1','m.leonardi@laranita.com',NULL,NULL,NULL,'Lote 63','active',NULL,'email','2026-08-08 19:20:43','2026-08-08 19:20:43',NULL),(63,'J.PABLO','LASSERRE',NULL,'45041672','20-45041672-8','j.lasserre@laranita.com',NULL,NULL,NULL,'Lote 64','active',NULL,'email','2026-08-08 19:20:43','2026-08-08 19:20:43',NULL),(64,'CARLOS','BOVERI',NULL,'41444948','20-41444948-3','c.boveri@laranita.com',NULL,NULL,NULL,'Lote 65','active',NULL,'email','2026-08-08 19:20:43','2026-08-08 19:20:43',NULL),(65,'PABLO Y LAREO','SAAVEDRA',NULL,'13033017','20-13033017-4','p.saavedra@laranita.com',NULL,NULL,NULL,'Lote 66','active',NULL,'email','2026-08-08 19:20:44','2026-08-08 19:20:44',NULL),(66,'MARIO EDUARDO','ACKERMAN',NULL,'45854257','20-45854257-8','m.ackerman@laranita.com',NULL,NULL,NULL,'Lote 67','active',NULL,'email','2026-08-08 19:20:44','2026-08-08 19:20:44',NULL),(67,'MARCELO','GARCIA VARA',NULL,'14260437','20-14260437-2','m.garciavara@laranita.com',NULL,NULL,NULL,'Lote 69','active',NULL,'email','2026-08-08 19:20:45','2026-08-08 19:20:45',NULL),(68,'NICOLAS','BURIS',NULL,'26814340','20-26814340-6','n.buris@laranita.com',NULL,NULL,NULL,'Lote 70','active',NULL,'email','2026-08-08 19:20:45','2026-08-08 19:20:45',NULL),(69,'PABLO','PENELA',NULL,'14708532','20-14708532-4','p.penela@laranita.com',NULL,NULL,NULL,'Lote 71','active',NULL,'email','2026-08-08 19:20:46','2026-08-08 19:20:46',NULL),(70,'AMAYA','ANDRES',NULL,'16140771','20-16140771-9','a.andres@laranita.com',NULL,NULL,NULL,'Lote 74','active',NULL,'email','2026-08-08 19:20:46','2026-08-08 19:20:46',NULL),(71,'DANIELA','REY',NULL,'12548228','20-12548228-2','d.rey@laranita.com',NULL,NULL,NULL,'Lote 75','active',NULL,'email','2026-08-08 19:20:47','2026-08-08 19:20:47',NULL),(72,'GARCIA','SILVIO SIXTO',NULL,'37329785','20-37329785-9','g.silviosixto@laranita.com',NULL,NULL,NULL,'Lote 76','active',NULL,'email','2026-08-08 19:20:47','2026-08-08 19:20:47',NULL),(73,'VALERIA','SALADINO',NULL,'15573074','20-15573074-0','v.saladino@laranita.com',NULL,NULL,NULL,'Lote 77','active',NULL,'email','2026-08-08 19:20:48','2026-08-08 19:20:48',NULL),(74,'CASTRO','PAULA',NULL,'28620952','20-28620952-6','c.paula@laranita.com',NULL,NULL,NULL,'Lote 78','active',NULL,'email','2026-08-08 19:20:48','2026-08-08 19:20:48',NULL),(75,'FEDERICO','RUBISTEIN',NULL,'19283628','20-19283628-1','f.rubistein@laranita.com',NULL,NULL,NULL,'Lote 79','active',NULL,'email','2026-08-08 19:20:49','2026-08-08 19:20:49',NULL),(76,'WALTER','GADEA',NULL,'42153864','20-42153864-4','w.gadea@laranita.com',NULL,NULL,NULL,'Lote 80','active',NULL,'email','2026-08-08 19:20:49','2026-08-08 19:20:49',NULL),(77,'EUGENIO','CAMBACERES',NULL,'33986780','20-33986780-3','e.cambaceres@laranita.com',NULL,NULL,NULL,'Lote 81','active',NULL,'email','2026-08-08 19:20:50','2026-08-08 19:20:50',NULL),(78,'CARLOS','D´ONOFRIO',NULL,'45052288','20-45052288-6','c.donofrio@laranita.com',NULL,NULL,NULL,'Lote 82','active',NULL,'email','2026-08-08 19:20:50','2026-08-08 19:20:50',NULL),(79,'MARCELO','GRECCO',NULL,'15341572','20-15341572-8','m.grecco@laranita.com',NULL,NULL,NULL,'Lote 83','active',NULL,'email','2026-08-08 19:20:51','2026-08-08 19:20:51',NULL),(80,'GOMEZ','MURIEGA YANINA',NULL,'41683892','20-41683892-3','g.muriegayanina@laranita.com',NULL,NULL,NULL,'Lote 84','active',NULL,'email','2026-08-08 19:20:51','2026-08-08 19:20:51',NULL),(81,'PAULO FRANCISCO','BELLUSCHI',NULL,'33577369','20-33577369-1','p.belluschi@laranita.com',NULL,NULL,NULL,'Lote 85','active',NULL,'email','2026-08-08 19:20:52','2026-08-08 19:20:52',NULL),(82,'TESTA','MARCELO',NULL,'22967431','20-22967431-3','t.marcelo@laranita.com',NULL,NULL,NULL,'Lote 87','active',NULL,'email','2026-08-08 19:20:52','2026-08-08 19:20:52',NULL),(83,'LUCIANA','PROL',NULL,'30268144','20-30268144-1','l.prol@laranita.com',NULL,NULL,NULL,'Lote 88','active',NULL,'email','2026-08-08 19:20:53','2026-08-08 19:20:53',NULL),(84,'MACCIO','ALEJANDRA/ CANE GONZALO',NULL,'42359515','20-42359515-1','m.alejandracanegonzalo@laranita.com',NULL,NULL,NULL,'Lote 89','active',NULL,'email','2026-08-08 19:20:53','2026-08-08 19:20:53',NULL),(85,'VELACION GILBERTO','SILVA',NULL,'32110915','20-32110915-6','v.silva@laranita.com',NULL,NULL,NULL,'Lote 90','active',NULL,'email','2026-08-08 19:20:54','2026-08-08 19:20:54',NULL),(86,'CANDIANO','PEDRO',NULL,'26256438','20-26256438-8','c.pedro@laranita.com',NULL,NULL,NULL,'Lote 92','active',NULL,'email','2026-08-08 19:20:54','2026-08-08 19:20:54',NULL),(87,'MARIO','VALLEDOR LASTRA',NULL,'23538840','20-23538840-8','m.valledorlastra@laranita.com',NULL,'(011) 15 4473 0985',NULL,'Lote 93','active',NULL,'both','2026-08-08 19:20:55','2026-08-08 19:20:55',NULL),(88,'POMPOSIELLO','JUAN I.',NULL,'42577437','20-42577437-4','p.juani@laranita.com',NULL,NULL,NULL,'Lote 94','active',NULL,'email','2026-08-08 19:20:56','2026-08-08 19:20:56',NULL),(89,'MARTÍN','CAROSSINO',NULL,'42398315','20-42398315-4','m.carossino@laranita.com',NULL,NULL,NULL,'Lote 95','active',NULL,'email','2026-08-08 19:20:56','2026-08-08 19:20:56',NULL),(90,'GABRIELA','CASANOVAS',NULL,'28233381','20-28233381-4','g.casanovas@laranita.com',NULL,NULL,NULL,'Lote 97','active',NULL,'email','2026-08-08 19:20:57','2026-08-08 19:20:57',NULL),(91,'UKI','GOÑI',NULL,'42356309','20-42356309-7','u.goni@laranita.com',NULL,NULL,NULL,'Lote 98','active',NULL,'email','2026-08-08 19:20:57','2026-08-08 19:20:57',NULL),(92,'TOMAS - Rosario','BISCEGLIA',NULL,'14632617','20-14632617-9','t.bisceglia@laranita.com',NULL,'11 6462 0201 (Rosario, esposa)',NULL,'Lote 99','active',NULL,'both','2026-08-08 19:20:58','2026-08-08 19:20:58',NULL),(93,'LUIS','MAYORGA',NULL,'10717486','20-10717486-3','l.mayorga@laranita.com',NULL,NULL,NULL,'Lote 100','active',NULL,'email','2026-08-08 19:20:58','2026-08-08 19:20:58',NULL),(94,'CANGIANO','COSENTINO',NULL,'37445299','20-37445299-1','c.cosentino@laranita.com',NULL,NULL,NULL,'Lote 101','active',NULL,'email','2026-08-08 19:20:58','2026-08-08 19:20:58',NULL),(95,'ENRIQUE','DUHART',NULL,'35238175','20-35238175-7','e.duhart@laranita.com',NULL,NULL,NULL,'Lote 102','active',NULL,'email','2026-08-08 19:20:59','2026-08-08 19:20:59',NULL),(96,'LUJAN HORACIO MARIA','ABRAM',NULL,'27644227','20-27644227-6','l.abram@laranita.com',NULL,NULL,NULL,'Lote 103','active',NULL,'email','2026-08-08 19:20:59','2026-08-08 19:20:59',NULL),(97,'PAULO','MOYANO',NULL,'24006713','20-24006713-5','p.moyano@laranita.com',NULL,NULL,NULL,'Lote 104','active',NULL,'email','2026-08-08 19:21:00','2026-08-08 19:21:00',NULL),(98,'RODRIGUEZ','WALTER',NULL,'43822235','20-43822235-5','r.walter@laranita.com',NULL,NULL,NULL,'Lote 105','active',NULL,'email','2026-08-08 19:21:00','2026-08-08 19:21:00',NULL),(99,'JOSE G.','DIAZ',NULL,'16515384','20-16515384-1','j.diaz@laranita.com',NULL,NULL,NULL,'Lote 106','active',NULL,'email','2026-08-08 19:21:01','2026-08-08 19:21:01',NULL),(100,'RICARDO','CORREIA',NULL,'45422334','20-45422334-2','r.correia@laranita.com',NULL,NULL,NULL,'Lote 107','active',NULL,'email','2026-08-08 19:21:01','2026-08-08 19:21:01',NULL),(101,'ADRIAN','BESUSCHIO',NULL,'23350918','20-23350918-9','a.besuschio@laranita.com',NULL,NULL,NULL,'Lote 108','active',NULL,'email','2026-08-08 19:21:02','2026-08-08 19:21:02',NULL),(102,'HERRERA','MARIA PAZ',NULL,'21646805','20-21646805-0','h.mariapaz@laranita.com',NULL,NULL,NULL,'Lote 109','active',NULL,'email','2026-08-08 19:21:02','2026-08-08 19:21:02',NULL),(103,'ENRIQUE RUBEN','SANTOS',NULL,'31361241','20-31361241-9','e.santos@laranita.com',NULL,NULL,NULL,'Lote 110','active',NULL,'email','2026-08-08 19:21:03','2026-08-08 19:21:03',NULL),(104,'RICARDO','FEDERICO',NULL,'45892329','20-45892329-1','r.federico@laranita.com',NULL,NULL,NULL,'Lote 111','active',NULL,'email','2026-08-08 19:21:03','2026-08-08 19:21:03',NULL),(105,'NJM','SA',NULL,'43540504','20-43540504-9','n.sa@laranita.com',NULL,NULL,NULL,'Lote 112','active',NULL,'email','2026-08-08 19:21:04','2026-08-08 19:21:04',NULL),(106,'I.P.S.A','Propietario',NULL,'38809158','20-38809158-1','i.propietario@laranita.com',NULL,NULL,NULL,'Lote 113','active',NULL,'email','2026-08-08 19:21:04','2026-08-08 19:21:04',NULL),(107,'THIAGO','ALDAVE',NULL,'41649806','20-41649806-7','t.aldave@laranita.com',NULL,NULL,NULL,'Lote 114','active',NULL,'email','2026-08-08 19:21:05','2026-08-08 19:21:05',NULL),(108,'CERVIÑO','SANTIAGO',NULL,'26833074','20-26833074-8','c.santiago@laranita.com',NULL,NULL,NULL,'Lote 115','active',NULL,'email','2026-08-08 19:21:05','2026-08-08 19:21:05',NULL),(109,'VICENTE','NIGRO',NULL,'45157094','20-45157094-1','v.nigro@laranita.com',NULL,NULL,NULL,'Lote 116','active',NULL,'email','2026-08-08 19:21:06','2026-08-08 19:21:06',NULL),(110,'ADRIAN','COSENTINO',NULL,'42110520','20-42110520-6','a.cosentino@laranita.com',NULL,NULL,NULL,'Lote 117','active',NULL,'email','2026-08-08 19:21:06','2026-08-08 19:21:06',NULL),(111,'HOY','WEST GUILLERMO',NULL,'34188322','20-34188322-7','h.westguillermo@laranita.com',NULL,NULL,NULL,'Lote 118','active',NULL,'email','2026-08-08 19:21:07','2026-08-08 19:21:07',NULL),(112,'LEONEL','EZEQUIEL BUHAY',NULL,'37317109','20-37317109-8','l.ezequielbuhay@laranita.com',NULL,NULL,NULL,'Lote 119','active',NULL,'email','2026-08-08 19:21:07','2026-08-08 19:21:07',NULL),(113,'DANIEL','HIGA',NULL,'38229051','20-38229051-7','d.higa@laranita.com',NULL,NULL,NULL,'Lote 120','active',NULL,'email','2026-08-08 19:21:08','2026-08-08 19:21:08',NULL),(114,'Francine - Juicio Iniciado','NUSBAUM',NULL,'32256719','20-32256719-8','f.nusbaum@laranita.com',NULL,NULL,NULL,'Lote 121','active',NULL,'email','2026-08-08 19:21:08','2026-08-08 19:21:08',NULL),(115,'Bema','Cosntrucciones - En Legales',NULL,'27324085','20-27324085-3','b.cosntruccionesenlegales@laranita.com',NULL,NULL,NULL,'Lote 122','active',NULL,'email','2026-08-08 19:21:09','2026-08-08 19:21:09',NULL),(116,'GALARRAGA','IÑAQUI',NULL,'17746402','20-17746402-6','g.inaqui@laranita.com',NULL,NULL,NULL,'Lote 124','active',NULL,'email','2026-08-08 19:21:09','2026-08-08 19:21:09',NULL),(117,'Sebastián','Bracco',NULL,'26707471','20-26707471-0','s.bracco@laranita.com',NULL,NULL,NULL,'Lote 125','active',NULL,'email','2026-08-08 19:21:10','2026-08-08 19:21:10',NULL),(118,'MARINA','MORERO',NULL,'31082672','20-31082672-2','m.morero@laranita.com',NULL,NULL,NULL,'Lote 126','active',NULL,'email','2026-08-08 19:21:10','2026-08-08 19:21:10',NULL),(119,'PIDRE','GABRIEL',NULL,'12515408','20-12515408-6','p.gabriel@laranita.com',NULL,NULL,NULL,'Lote 127','active',NULL,'email','2026-08-08 19:21:11','2026-08-08 19:21:11',NULL),(120,'PABLO MARIANO','FERNANDEZ',NULL,'21280607','20-21280607-1','p.fernandez@laranita.com',NULL,NULL,NULL,'Lote 128','active',NULL,'email','2026-08-08 19:21:11','2026-08-08 19:21:11',NULL),(121,'Paula - MOURELOS, Gabriel','NOTRICA',NULL,'14508158','20-14508158-7','p.notrica@laranita.com',NULL,NULL,NULL,'Lote 129','active',NULL,'email','2026-08-08 19:21:12','2026-08-08 19:21:12',NULL),(122,'ALEJANDRO','TRANI',NULL,'20597184','20-20597184-2','a.trani@laranita.com',NULL,NULL,NULL,'Lote 130','active',NULL,'email','2026-08-08 19:21:12','2026-08-08 19:21:12',NULL),(123,'FERREYRA','VERONICA',NULL,'33715541','20-33715541-9','f.veronica@laranita.com',NULL,NULL,NULL,'Lote 131','active',NULL,'email','2026-08-08 19:21:13','2026-08-08 19:21:13',NULL);
/*!40000 ALTER TABLE `owners` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ownership_history`
--

DROP TABLE IF EXISTS `ownership_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ownership_history` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `lot_id` bigint(20) unsigned NOT NULL,
  `owner_id` bigint(20) unsigned NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `documents` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ownership_history_lot_id_foreign` (`lot_id`),
  KEY `ownership_history_owner_id_foreign` (`owner_id`),
  KEY `ownership_history_user_id_foreign` (`user_id`),
  CONSTRAINT `ownership_history_lot_id_foreign` FOREIGN KEY (`lot_id`) REFERENCES `lots` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ownership_history_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `owners` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ownership_history_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ownership_history`
--

LOCK TABLES `ownership_history` WRITE;
/*!40000 ALTER TABLE `ownership_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `ownership_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_allocations`
--

DROP TABLE IF EXISTS `payment_allocations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_allocations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `payment_id` bigint(20) unsigned NOT NULL,
  `account_movement_id` bigint(20) unsigned NOT NULL,
  `allocated_amount` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `method` varchar(255) DEFAULT NULL,
  `previous_balance` decimal(15,2) DEFAULT NULL,
  `posterior_balance` decimal(15,2) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `reverted_at` timestamp NULL DEFAULT NULL,
  `reverted_by` bigint(20) unsigned DEFAULT NULL,
  `reversion_reason` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_allocations_payment_id_foreign` (`payment_id`),
  KEY `payment_allocations_account_movement_id_foreign` (`account_movement_id`),
  KEY `payment_allocations_user_id_foreign` (`user_id`),
  KEY `payment_allocations_reverted_by_foreign` (`reverted_by`),
  CONSTRAINT `payment_allocations_account_movement_id_foreign` FOREIGN KEY (`account_movement_id`) REFERENCES `account_movements` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payment_allocations_payment_id_foreign` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payment_allocations_reverted_by_foreign` FOREIGN KEY (`reverted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payment_allocations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_allocations`
--

LOCK TABLES `payment_allocations` WRITE;
/*!40000 ALTER TABLE `payment_allocations` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment_allocations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_receipts`
--

DROP TABLE IF EXISTS `payment_receipts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_receipts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `payment_id` bigint(20) unsigned NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_size` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_receipts_payment_id_foreign` (`payment_id`),
  CONSTRAINT `payment_receipts_payment_id_foreign` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_receipts`
--

LOCK TABLES `payment_receipts` WRITE;
/*!40000 ALTER TABLE `payment_receipts` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment_receipts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `owner_id` bigint(20) unsigned DEFAULT NULL,
  `lot_id` bigint(20) unsigned DEFAULT NULL,
  `functional_unit_id` bigint(20) unsigned DEFAULT NULL,
  `payment_date` date NOT NULL,
  `import_date` date DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `bank` varchar(255) DEFAULT NULL,
  `payment_method` varchar(255) NOT NULL DEFAULT 'transfer',
  `operation_number` varchar(255) DEFAULT NULL,
  `receipt_path` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `source_channel` varchar(255) NOT NULL DEFAULT 'portal',
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `matching_score` int(11) DEFAULT NULL,
  `matched_debit_id` bigint(20) unsigned DEFAULT NULL,
  `reconciliation_method` varchar(255) DEFAULT NULL,
  `reconciled_at` timestamp NULL DEFAULT NULL,
  `reverted_at` timestamp NULL DEFAULT NULL,
  `reverted_by` bigint(20) unsigned DEFAULT NULL,
  `reversion_reason` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payments_owner_id_foreign` (`owner_id`),
  KEY `payments_lot_id_foreign` (`lot_id`),
  KEY `payments_functional_unit_id_foreign` (`functional_unit_id`),
  KEY `payments_user_id_foreign` (`user_id`),
  KEY `payments_reverted_by_foreign` (`reverted_by`),
  CONSTRAINT `payments_functional_unit_id_foreign` FOREIGN KEY (`functional_unit_id`) REFERENCES `functional_units` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_lot_id_foreign` FOREIGN KEY (`lot_id`) REFERENCES `lots` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `owners` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_reverted_by_foreign` FOREIGN KEY (`reverted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permission_role`
--

DROP TABLE IF EXISTS `permission_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permission_role` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `permission_role_role_id_foreign` (`role_id`),
  CONSTRAINT `permission_role_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `permission_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permission_role`
--

LOCK TABLES `permission_role` WRITE;
/*!40000 ALTER TABLE `permission_role` DISABLE KEYS */;
INSERT INTO `permission_role` VALUES (1,1),(1,2),(2,1),(2,2),(2,3),(3,1),(3,2),(3,4),(4,1),(4,2),(4,4),(5,1),(5,2),(5,3),(6,1),(6,2),(6,3),(7,1),(7,2),(7,4),(7,7),(8,1),(9,1),(9,3),(9,5),(9,6),(9,7);
/*!40000 ALTER TABLE `permission_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'manage-users','Gestionar Usuarios','Permiso para Gestionar Usuarios','2026-08-08 19:20:09','2026-08-08 19:20:09'),(2,'manage-lots','Gestionar Lotes y Unidades','Permiso para Gestionar Lotes y Unidades','2026-08-08 19:20:09','2026-08-08 19:20:09'),(3,'manage-finances','Gestionar Finanzas y Expensas','Permiso para Gestionar Finanzas y Expensas','2026-08-08 19:20:09','2026-08-08 19:20:09'),(4,'manage-payments','Conciliar y Aprobar Pagos','Permiso para Conciliar y Aprobar Pagos','2026-08-08 19:20:09','2026-08-08 19:20:09'),(5,'manage-tickets','Responder Reclamos','Permiso para Responder Reclamos','2026-08-08 19:20:09','2026-08-08 19:20:09'),(6,'manage-communications','Enviar Comunicados','Permiso para Enviar Comunicados','2026-08-08 19:20:09','2026-08-08 19:20:09'),(7,'view-reports','Ver Reportes y Métricas','Permiso para Ver Reportes y Métricas','2026-08-08 19:20:09','2026-08-08 19:20:09'),(8,'view-audit','Consultar Logs y Auditoría','Permiso para Consultar Logs y Auditoría','2026-08-08 19:20:09','2026-08-08 19:20:09'),(9,'use-portal','Acceso al Portal del Propietario','Permiso para Acceso al Portal del Propietario','2026-08-08 19:20:09','2026-08-08 19:20:09');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reservations`
--

DROP TABLE IF EXISTS `reservations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reservations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `common_area_id` bigint(20) unsigned NOT NULL,
  `lot_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `reservation_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `charge_to_expenses` tinyint(1) NOT NULL DEFAULT 1,
  `is_exclusive` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('pending','confirmed','rejected','canceled','completed') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reservations_common_area_id_foreign` (`common_area_id`),
  KEY `reservations_lot_id_foreign` (`lot_id`),
  KEY `reservations_user_id_foreign` (`user_id`),
  CONSTRAINT `reservations_common_area_id_foreign` FOREIGN KEY (`common_area_id`) REFERENCES `common_areas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reservations_lot_id_foreign` FOREIGN KEY (`lot_id`) REFERENCES `lots` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reservations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservations`
--

LOCK TABLES `reservations` WRITE;
/*!40000 ALTER TABLE `reservations` DISABLE KEYS */;
INSERT INTO `reservations` VALUES (1,1,1,6,'2026-08-11','14:00:00','20:00:00',0.00,0,0,'completed',NULL,'2026-08-10 18:56:49','2026-08-10 19:40:23');
/*!40000 ALTER TABLE `reservations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_user`
--

DROP TABLE IF EXISTS `role_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_user` (
  `role_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`user_id`),
  KEY `role_user_user_id_foreign` (`user_id`),
  CONSTRAINT `role_user_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_user`
--

LOCK TABLES `role_user` WRITE;
/*!40000 ALTER TABLE `role_user` DISABLE KEYS */;
INSERT INTO `role_user` VALUES (1,1),(2,2),(2,3),(3,5),(4,4),(5,6),(5,7),(5,8),(5,9),(5,10),(5,11),(5,12),(5,13),(5,14),(5,15),(5,16),(5,17),(5,18),(5,19),(5,20),(5,21),(5,22),(5,23),(5,24),(5,25),(5,26),(5,27),(5,28),(5,29),(5,30),(5,31),(5,32),(5,33),(5,34),(5,35),(5,36),(5,37),(5,38),(5,39),(5,40),(5,41),(5,42),(5,43),(5,44),(5,45),(5,46),(5,47),(5,48),(5,49),(5,50),(5,51),(5,52),(5,53),(5,54),(5,55),(5,56),(5,57),(5,58),(5,59),(5,60),(5,61),(5,62),(5,63),(5,64),(5,65),(5,66),(5,67),(5,68),(5,69),(5,70),(5,71),(5,72),(5,73),(5,74),(5,75),(5,76),(5,77),(5,78),(5,79),(5,80),(5,81),(5,82),(5,83),(5,84),(5,85),(5,86),(5,87),(5,88),(5,89),(5,90),(5,91),(5,92),(5,93),(5,94),(5,95),(5,96),(5,97),(5,98),(5,99),(5,100),(5,101),(5,102),(5,103),(5,104),(5,105),(5,106),(5,107),(5,108),(5,109),(5,110),(5,111),(5,112),(5,113),(5,114),(5,115),(5,116),(5,117),(5,118),(5,119),(5,120),(5,121),(5,122),(5,123),(5,124),(5,125),(5,126),(5,127),(5,128);
/*!40000 ALTER TABLE `role_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'superadmin','Super Administrador','Rol de Super Administrador','2026-08-08 19:20:09','2026-08-08 19:20:09'),(2,'admin','Administrador','Rol de Administrador','2026-08-08 19:20:09','2026-08-08 19:20:09'),(3,'operator','Operador Administrativo','Rol de Operador Administrativo','2026-08-08 19:20:09','2026-08-08 19:20:09'),(4,'accounting','Contabilidad','Rol de Contabilidad','2026-08-08 19:20:09','2026-08-08 19:20:09'),(5,'owner','Propietario','Rol de Propietario','2026-08-08 19:20:09','2026-08-08 19:20:09'),(6,'tenant','Inquilino','Rol de Inquilino','2026-08-08 19:20:09','2026-08-08 19:20:09'),(7,'board','Consejo o Directorio','Rol de Consejo o Directorio','2026-08-08 19:20:09','2026-08-08 19:20:09');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `scheduled_jobs`
--

DROP TABLE IF EXISTS `scheduled_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `scheduled_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `cron` varchar(255) DEFAULT NULL,
  `last_run_at` timestamp NULL DEFAULT NULL,
  `next_run_at` timestamp NULL DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `output` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `scheduled_jobs`
--

LOCK TABLES `scheduled_jobs` WRITE;
/*!40000 ALTER TABLE `scheduled_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `scheduled_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `supplier_invoices`
--

DROP TABLE IF EXISTS `supplier_invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `supplier_invoices` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` bigint(20) unsigned NOT NULL,
  `invoice_number` varchar(255) NOT NULL,
  `concept` varchar(255) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `issue_date` date NOT NULL,
  `due_date` date NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `supplier_invoices_supplier_id_foreign` (`supplier_id`),
  CONSTRAINT `supplier_invoices_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supplier_invoices`
--

LOCK TABLES `supplier_invoices` WRITE;
/*!40000 ALTER TABLE `supplier_invoices` DISABLE KEYS */;
INSERT INTO `supplier_invoices` VALUES (1,2,'0003-00012489','Abono Limpieza Quincena 1 Agosto',125000.00,'2026-07-24','2026-08-07',NULL,'pending','Factura pendiente de aprobación final por gerencia.','2026-08-08 19:21:13','2026-08-08 19:21:13',NULL),(2,1,'0001-00054231','Abono Servicio Seguridad Agosto',250000.00,'2026-08-06','2026-08-12',NULL,'scheduled','Pago programado para el día viernes.','2026-08-08 19:21:13','2026-08-08 19:21:13',NULL),(3,3,'0002-00004123','Servicio de poda y mantenimiento de cerco perimetral',85000.00,'2026-08-03','2026-08-18',NULL,'pending',NULL,'2026-08-08 19:21:13','2026-08-08 19:21:13',NULL),(4,4,'0012-99887766','Factura de luz espacios comunes y portería',198000.00,'2026-08-07','2026-09-02',NULL,'pending',NULL,'2026-08-08 19:21:13','2026-08-08 19:21:13',NULL),(5,1,'0001-00053912','Abono Servicio Seguridad Julio',250000.00,'2026-07-07','2026-08-03',NULL,'paid','Pagado con transferencia de cuenta Banco Provincia.','2026-08-08 19:21:13','2026-08-08 19:21:13',NULL);
/*!40000 ALTER TABLE `supplier_invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `suppliers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cuit` varchar(255) NOT NULL,
  `business_name` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL DEFAULT 'Servicios',
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `cbu_alias` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `suppliers_cuit_unique` (`cuit`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers`
--

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
INSERT INTO `suppliers` VALUES (1,'30-55444333-8','Seguridad y Monitoreo del Norte S.A.','Seguridad','administracion@seguridadnorte.com','+5491155667788','Av. General Paz 4500, CABA','Banco de la Nación Argentina','seguridad.norte.cbu','active',NULL,'2026-08-08 19:21:13','2026-08-08 19:21:13',NULL),(2,'30-66777888-2','Servicios Generales Limpieza S.R.L.','Mantenimiento y Limpieza','proveedores@limpiezasrl.com','+5491144332211','Sarmiento 1200, Pilar','Banco Santander Río','limpieza.laranita.alias','active',NULL,'2026-08-08 19:21:13','2026-08-08 19:21:13',NULL),(3,'20-18456123-5','Jardines y Parquizaciones Verdes','Jardinería','contacto@jardinesverdes.com','+5491133225566','Ruta 8 Km 54, Pilar','Banco Galicia','jardines.galicia.alias','active',NULL,'2026-08-08 19:21:13','2026-08-08 19:21:13',NULL),(4,'30-70722243-5','Edesur S.A.','Electricidad','facturacion@edesur.com.ar','0810-222-0200','San José 140, CABA','Banco BBVA Argentina','edesur.pago.cbu','active',NULL,'2026-08-08 19:21:13','2026-08-08 19:21:13',NULL);
/*!40000 ALTER TABLE `suppliers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_settings`
--

DROP TABLE IF EXISTS `system_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `system_settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_settings`
--

LOCK TABLES `system_settings` WRITE;
/*!40000 ALTER TABLE `system_settings` DISABLE KEYS */;
INSERT INTO `system_settings` VALUES (1,'neighborhood_name','Barrio Privado La Ranita','Nombre comercial de la urbanización','2026-08-08 19:20:12','2026-08-08 19:20:12'),(2,'cuit','30-71234567-9','CUIT institucional del consorcio','2026-08-08 19:20:12','2026-08-08 19:20:12'),(3,'address','Ruta 25 Km 4.5, Pilar, Buenos Aires','Dirección física del barrio','2026-08-08 19:20:12','2026-08-08 19:20:12'),(4,'interest_rate_monthly','3.5','Porcentaje de interés mensual por mora (en %)','2026-08-08 19:20:12','2026-08-08 19:20:12'),(5,'due_day','10','Día del mes para el primer vencimiento de expensas','2026-08-08 19:20:12','2026-08-08 19:20:12'),(6,'second_due_day','20','Día del mes para el segundo vencimiento de expensas','2026-08-08 19:20:12','2026-08-08 19:20:12'),(7,'interest_type','daily','Tipo de cálculo de interés: daily (diario) o monthly (mensual completo)','2026-08-08 19:20:12','2026-08-08 19:20:12'),(8,'notify_reservation_email','1','Enviar notificación por email de nuevas reservas al administrador (1: Si, 0: No)','2026-08-10 00:04:11','2026-08-28 19:40:54'),(9,'notify_reservation_system','1','Mostrar alertas en la campana de notificaciones de nuevas reservas (1: Si, 0: No)','2026-08-10 00:04:11','2026-08-28 19:40:54'),(10,'notify_reservation_owner_email','1','Enviar confirmación/estado por email al propietario (1: Si, 0: No)','2026-08-10 00:04:11','2026-08-28 19:40:54');
/*!40000 ALTER TABLE `system_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tenancy_history`
--

DROP TABLE IF EXISTS `tenancy_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tenancy_history` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `lot_id` bigint(20) unsigned NOT NULL,
  `tenant_id` bigint(20) unsigned NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `owner_id` bigint(20) unsigned NOT NULL,
  `documents` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tenancy_history_lot_id_foreign` (`lot_id`),
  KEY `tenancy_history_tenant_id_foreign` (`tenant_id`),
  KEY `tenancy_history_owner_id_foreign` (`owner_id`),
  CONSTRAINT `tenancy_history_lot_id_foreign` FOREIGN KEY (`lot_id`) REFERENCES `lots` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tenancy_history_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `owners` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tenancy_history_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tenancy_history`
--

LOCK TABLES `tenancy_history` WRITE;
/*!40000 ALTER TABLE `tenancy_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `tenancy_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tenants`
--

DROP TABLE IF EXISTS `tenants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tenants` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `dni` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tenants_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tenants`
--

LOCK TABLES `tenants` WRITE;
/*!40000 ALTER TABLE `tenants` DISABLE KEYS */;
/*!40000 ALTER TABLE `tenants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ticket_attachments`
--

DROP TABLE IF EXISTS `ticket_attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket_attachments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` bigint(20) unsigned NOT NULL,
  `ticket_message_id` bigint(20) unsigned DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_attachments_ticket_id_foreign` (`ticket_id`),
  KEY `ticket_attachments_ticket_message_id_foreign` (`ticket_message_id`),
  CONSTRAINT `ticket_attachments_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ticket_attachments_ticket_message_id_foreign` FOREIGN KEY (`ticket_message_id`) REFERENCES `ticket_messages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_attachments`
--

LOCK TABLES `ticket_attachments` WRITE;
/*!40000 ALTER TABLE `ticket_attachments` DISABLE KEYS */;
/*!40000 ALTER TABLE `ticket_attachments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ticket_categories`
--

DROP TABLE IF EXISTS `ticket_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ticket_categories_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_categories`
--

LOCK TABLES `ticket_categories` WRITE;
/*!40000 ALTER TABLE `ticket_categories` DISABLE KEYS */;
INSERT INTO `ticket_categories` VALUES (1,'admin','Administración','2026-08-08 19:20:12','2026-08-08 19:20:12'),(2,'expenses','Expensas y Cuentas','2026-08-08 19:20:12','2026-08-08 19:20:12'),(3,'security','Seguridad','2026-08-08 19:20:12','2026-08-08 19:20:12'),(4,'maintenance','Mantenimiento e Infraestructura','2026-08-08 19:20:12','2026-08-08 19:20:12'),(5,'poda','Poda y Jardinería','2026-08-08 19:20:12','2026-08-08 19:20:12'),(6,'pets','Mascotas','2026-08-08 19:20:12','2026-08-08 19:20:12'),(7,'documents','Documentación','2026-08-08 19:20:12','2026-08-08 19:20:12'),(8,'suggestions','Sugerencias','2026-08-08 19:20:12','2026-08-08 19:20:12');
/*!40000 ALTER TABLE `ticket_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ticket_internal_notes`
--

DROP TABLE IF EXISTS `ticket_internal_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket_internal_notes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `note` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_internal_notes_ticket_id_foreign` (`ticket_id`),
  KEY `ticket_internal_notes_user_id_foreign` (`user_id`),
  CONSTRAINT `ticket_internal_notes_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ticket_internal_notes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_internal_notes`
--

LOCK TABLES `ticket_internal_notes` WRITE;
/*!40000 ALTER TABLE `ticket_internal_notes` DISABLE KEYS */;
/*!40000 ALTER TABLE `ticket_internal_notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ticket_messages`
--

DROP TABLE IF EXISTS `ticket_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket_messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `message` text NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_messages_ticket_id_foreign` (`ticket_id`),
  KEY `ticket_messages_user_id_foreign` (`user_id`),
  CONSTRAINT `ticket_messages_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ticket_messages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_messages`
--

LOCK TABLES `ticket_messages` WRITE;
/*!40000 ALTER TABLE `ticket_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `ticket_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tickets`
--

DROP TABLE IF EXISTS `tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tickets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `lot_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `category_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'new',
  `priority` varchar(255) NOT NULL DEFAULT 'medium',
  `assigned_to` bigint(20) unsigned DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `rating_comments` text DEFAULT NULL,
  `source_channel` varchar(255) NOT NULL DEFAULT 'portal',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tickets_lot_id_foreign` (`lot_id`),
  KEY `tickets_user_id_foreign` (`user_id`),
  KEY `tickets_category_id_foreign` (`category_id`),
  KEY `tickets_assigned_to_foreign` (`assigned_to`),
  CONSTRAINT `tickets_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tickets_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `ticket_categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tickets_lot_id_foreign` FOREIGN KEY (`lot_id`) REFERENCES `lots` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tickets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tickets`
--

LOCK TABLES `tickets` WRITE;
/*!40000 ALTER TABLE `tickets` DISABLE KEYS */;
/*!40000 ALTER TABLE `tickets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_activity_logs`
--

DROP TABLE IF EXISTS `user_activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_activity_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `action` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_activity_logs_user_id_foreign` (`user_id`),
  CONSTRAINT `user_activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_activity_logs`
--

LOCK TABLES `user_activity_logs` WRITE;
/*!40000 ALTER TABLE `user_activity_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_functional_unit`
--

DROP TABLE IF EXISTS `user_functional_unit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_functional_unit` (
  `user_id` bigint(20) unsigned NOT NULL,
  `functional_unit_id` bigint(20) unsigned NOT NULL,
  `relationship_type` varchar(255) NOT NULL DEFAULT 'owner',
  PRIMARY KEY (`user_id`,`functional_unit_id`),
  KEY `user_functional_unit_functional_unit_id_foreign` (`functional_unit_id`),
  CONSTRAINT `user_functional_unit_functional_unit_id_foreign` FOREIGN KEY (`functional_unit_id`) REFERENCES `functional_units` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_functional_unit_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_functional_unit`
--

LOCK TABLES `user_functional_unit` WRITE;
/*!40000 ALTER TABLE `user_functional_unit` DISABLE KEYS */;
INSERT INTO `user_functional_unit` VALUES (6,1,'owner'),(7,2,'owner'),(8,3,'owner'),(9,4,'owner'),(10,5,'owner'),(11,6,'owner'),(12,7,'owner'),(13,8,'owner'),(14,9,'owner'),(15,10,'owner'),(16,11,'owner'),(17,12,'owner'),(18,13,'owner'),(19,14,'owner'),(20,15,'owner'),(21,16,'owner'),(22,17,'owner'),(23,18,'owner'),(24,19,'owner'),(25,20,'owner'),(26,21,'owner'),(27,22,'owner'),(28,23,'owner'),(29,24,'owner'),(30,25,'owner'),(31,26,'owner'),(32,27,'owner'),(33,28,'owner'),(34,29,'owner'),(35,30,'owner'),(36,31,'owner'),(36,51,'owner'),(36,73,'owner'),(36,91,'owner'),(37,32,'owner'),(38,33,'owner'),(39,34,'owner'),(40,35,'owner'),(41,36,'owner'),(42,37,'owner'),(43,38,'owner'),(44,39,'owner'),(45,40,'owner'),(46,41,'owner'),(47,42,'owner'),(48,43,'owner'),(49,44,'owner'),(50,45,'owner'),(51,46,'owner'),(52,47,'owner'),(53,48,'owner'),(54,49,'owner'),(55,50,'owner'),(56,52,'owner'),(57,53,'owner'),(58,54,'owner'),(59,55,'owner'),(60,56,'owner'),(61,57,'owner'),(62,58,'owner'),(63,59,'owner'),(64,60,'owner'),(65,61,'owner'),(66,62,'owner'),(67,63,'owner'),(68,64,'owner'),(69,65,'owner'),(70,66,'owner'),(71,67,'owner'),(71,68,'owner'),(72,69,'owner'),(73,70,'owner'),(74,71,'owner'),(74,72,'owner'),(75,74,'owner'),(76,75,'owner'),(77,76,'owner'),(78,77,'owner'),(79,78,'owner'),(80,79,'owner'),(81,80,'owner'),(82,81,'owner'),(83,82,'owner'),(84,83,'owner'),(85,84,'owner'),(86,85,'owner'),(86,86,'owner'),(87,87,'owner'),(88,88,'owner'),(89,89,'owner'),(90,90,'owner'),(91,92,'owner'),(92,93,'owner'),(93,94,'owner'),(94,95,'owner'),(94,96,'owner'),(95,97,'owner'),(96,98,'owner'),(97,99,'owner'),(98,100,'owner'),(99,101,'owner'),(100,102,'owner'),(101,103,'owner'),(102,104,'owner'),(103,105,'owner'),(104,106,'owner'),(105,107,'owner'),(106,108,'owner'),(107,109,'owner'),(108,110,'owner'),(109,111,'owner'),(110,112,'owner'),(111,113,'owner'),(112,114,'owner'),(113,115,'owner'),(114,116,'owner'),(115,117,'owner'),(116,118,'owner'),(117,119,'owner'),(118,120,'owner'),(119,121,'owner'),(120,122,'owner'),(120,123,'owner'),(121,124,'owner'),(122,125,'owner'),(123,126,'owner'),(124,127,'owner'),(125,128,'owner'),(126,129,'owner'),(127,130,'owner'),(128,131,'owner');
/*!40000 ALTER TABLE `user_functional_unit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_preferences`
--

DROP TABLE IF EXISTS `user_preferences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_preferences` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `theme` varchar(255) NOT NULL DEFAULT 'auto',
  `notifications_email` tinyint(1) NOT NULL DEFAULT 1,
  `notifications_whatsapp` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_preferences_user_id_foreign` (`user_id`),
  CONSTRAINT `user_preferences_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=125 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_preferences`
--

LOCK TABLES `user_preferences` WRITE;
/*!40000 ALTER TABLE `user_preferences` DISABLE KEYS */;
INSERT INTO `user_preferences` VALUES (1,6,'auto',1,0,'2026-08-08 19:20:13','2026-08-08 19:20:13'),(2,7,'auto',1,0,'2026-08-08 19:20:13','2026-08-08 19:20:13'),(3,8,'auto',1,0,'2026-08-08 19:20:14','2026-08-08 19:20:14'),(4,9,'auto',1,0,'2026-08-08 19:20:14','2026-08-08 19:20:14'),(5,10,'auto',1,0,'2026-08-08 19:20:15','2026-08-08 19:20:15'),(6,11,'auto',1,0,'2026-08-08 19:20:15','2026-08-08 19:20:15'),(7,12,'auto',1,0,'2026-08-08 19:20:16','2026-08-08 19:20:16'),(8,13,'auto',1,0,'2026-08-08 19:20:16','2026-08-08 19:20:16'),(9,14,'auto',1,0,'2026-08-08 19:20:17','2026-08-08 19:20:17'),(10,15,'auto',1,0,'2026-08-08 19:20:17','2026-08-08 19:20:17'),(11,16,'auto',1,0,'2026-08-08 19:20:18','2026-08-08 19:20:18'),(12,17,'auto',1,0,'2026-08-08 19:20:18','2026-08-08 19:20:18'),(13,18,'auto',1,0,'2026-08-08 19:20:19','2026-08-08 19:20:19'),(14,19,'auto',1,0,'2026-08-08 19:20:19','2026-08-08 19:20:19'),(15,20,'auto',1,0,'2026-08-08 19:20:20','2026-08-08 19:20:20'),(16,21,'auto',1,0,'2026-08-08 19:20:20','2026-08-08 19:20:20'),(17,22,'auto',1,0,'2026-08-08 19:20:21','2026-08-08 19:20:21'),(18,23,'auto',1,0,'2026-08-08 19:20:21','2026-08-08 19:20:21'),(19,24,'auto',1,0,'2026-08-08 19:20:22','2026-08-08 19:20:22'),(20,25,'auto',1,0,'2026-08-08 19:20:22','2026-08-08 19:20:22'),(21,26,'auto',1,1,'2026-08-08 19:20:23','2026-08-08 19:20:23'),(22,27,'auto',1,0,'2026-08-08 19:20:23','2026-08-08 19:20:23'),(23,28,'auto',1,0,'2026-08-08 19:20:24','2026-08-08 19:20:24'),(24,29,'auto',1,0,'2026-08-08 19:20:24','2026-08-08 19:20:24'),(25,30,'auto',1,0,'2026-08-08 19:20:25','2026-08-08 19:20:25'),(26,31,'auto',1,0,'2026-08-08 19:20:25','2026-08-08 19:20:25'),(27,32,'auto',1,0,'2026-08-08 19:20:26','2026-08-08 19:20:26'),(28,33,'auto',1,0,'2026-08-08 19:20:26','2026-08-08 19:20:26'),(29,34,'auto',1,0,'2026-08-08 19:20:27','2026-08-08 19:20:27'),(30,35,'auto',1,0,'2026-08-08 19:20:27','2026-08-08 19:20:27'),(31,36,'auto',1,0,'2026-08-08 19:20:28','2026-08-08 19:20:28'),(32,37,'auto',1,0,'2026-08-08 19:20:28','2026-08-08 19:20:28'),(33,38,'auto',1,0,'2026-08-08 19:20:29','2026-08-08 19:20:29'),(34,39,'auto',1,0,'2026-08-08 19:20:29','2026-08-08 19:20:29'),(35,40,'auto',1,0,'2026-08-08 19:20:30','2026-08-08 19:20:30'),(36,41,'auto',1,0,'2026-08-08 19:20:30','2026-08-08 19:20:30'),(37,42,'auto',1,0,'2026-08-08 19:20:31','2026-08-08 19:20:31'),(38,43,'auto',1,0,'2026-08-08 19:20:31','2026-08-08 19:20:31'),(39,44,'auto',1,0,'2026-08-08 19:20:32','2026-08-08 19:20:32'),(40,45,'auto',1,0,'2026-08-08 19:20:32','2026-08-08 19:20:32'),(41,46,'auto',1,0,'2026-08-08 19:20:33','2026-08-08 19:20:33'),(42,47,'auto',1,0,'2026-08-08 19:20:33','2026-08-08 19:20:33'),(43,48,'auto',1,1,'2026-08-08 19:20:34','2026-08-08 19:20:34'),(44,49,'auto',1,0,'2026-08-08 19:20:34','2026-08-08 19:20:34'),(45,50,'auto',1,0,'2026-08-08 19:20:35','2026-08-08 19:20:35'),(46,51,'auto',1,0,'2026-08-08 19:20:35','2026-08-08 19:20:35'),(47,52,'auto',1,0,'2026-08-08 19:20:36','2026-08-08 19:20:36'),(48,53,'auto',1,0,'2026-08-08 19:20:36','2026-08-08 19:20:36'),(49,54,'auto',1,0,'2026-08-08 19:20:37','2026-08-08 19:20:37'),(50,55,'auto',1,0,'2026-08-08 19:20:37','2026-08-08 19:20:37'),(51,56,'auto',1,0,'2026-08-08 19:20:38','2026-08-08 19:20:38'),(52,57,'auto',1,0,'2026-08-08 19:20:38','2026-08-08 19:20:38'),(53,58,'auto',1,0,'2026-08-08 19:20:39','2026-08-08 19:20:39'),(54,59,'auto',1,1,'2026-08-08 19:20:39','2026-08-08 19:20:39'),(55,60,'auto',1,0,'2026-08-08 19:20:40','2026-08-08 19:20:40'),(56,61,'auto',1,0,'2026-08-08 19:20:40','2026-08-08 19:20:40'),(57,62,'auto',1,0,'2026-08-08 19:20:41','2026-08-08 19:20:41'),(58,63,'auto',1,0,'2026-08-08 19:20:41','2026-08-08 19:20:41'),(59,64,'auto',1,0,'2026-08-08 19:20:42','2026-08-08 19:20:42'),(60,65,'auto',1,0,'2026-08-08 19:20:42','2026-08-08 19:20:42'),(61,66,'auto',1,0,'2026-08-08 19:20:43','2026-08-08 19:20:43'),(62,67,'auto',1,0,'2026-08-08 19:20:43','2026-08-08 19:20:43'),(63,68,'auto',1,0,'2026-08-08 19:20:43','2026-08-08 19:20:43'),(64,69,'auto',1,0,'2026-08-08 19:20:44','2026-08-08 19:20:44'),(65,70,'auto',1,0,'2026-08-08 19:20:44','2026-08-08 19:20:44'),(66,71,'auto',1,0,'2026-08-08 19:20:45','2026-08-08 19:20:45'),(67,72,'auto',1,0,'2026-08-08 19:20:45','2026-08-08 19:20:45'),(68,73,'auto',1,0,'2026-08-08 19:20:46','2026-08-08 19:20:46'),(69,74,'auto',1,0,'2026-08-08 19:20:46','2026-08-08 19:20:46'),(70,75,'auto',1,0,'2026-08-08 19:20:47','2026-08-08 19:20:47'),(71,76,'auto',1,0,'2026-08-08 19:20:47','2026-08-08 19:20:47'),(72,77,'auto',1,0,'2026-08-08 19:20:48','2026-08-08 19:20:48'),(73,78,'auto',1,0,'2026-08-08 19:20:48','2026-08-08 19:20:48'),(74,79,'auto',1,0,'2026-08-08 19:20:49','2026-08-08 19:20:49'),(75,80,'auto',1,0,'2026-08-08 19:20:49','2026-08-08 19:20:49'),(76,81,'auto',1,0,'2026-08-08 19:20:50','2026-08-08 19:20:50'),(77,82,'auto',1,0,'2026-08-08 19:20:50','2026-08-08 19:20:50'),(78,83,'auto',1,0,'2026-08-08 19:20:51','2026-08-08 19:20:51'),(79,84,'auto',1,0,'2026-08-08 19:20:51','2026-08-08 19:20:51'),(80,85,'auto',1,0,'2026-08-08 19:20:52','2026-08-08 19:20:52'),(81,86,'auto',1,0,'2026-08-08 19:20:52','2026-08-08 19:20:52'),(82,87,'auto',1,0,'2026-08-08 19:20:53','2026-08-08 19:20:53'),(83,88,'auto',1,0,'2026-08-08 19:20:53','2026-08-08 19:20:53'),(84,89,'auto',1,0,'2026-08-08 19:20:54','2026-08-08 19:20:54'),(85,90,'auto',1,0,'2026-08-08 19:20:54','2026-08-08 19:20:54'),(86,91,'auto',1,0,'2026-08-08 19:20:55','2026-08-08 19:20:55'),(87,92,'auto',1,1,'2026-08-08 19:20:56','2026-08-08 19:20:56'),(88,93,'auto',1,0,'2026-08-08 19:20:56','2026-08-08 19:20:56'),(89,94,'auto',1,0,'2026-08-08 19:20:56','2026-08-08 19:20:56'),(90,95,'auto',1,0,'2026-08-08 19:20:57','2026-08-08 19:20:57'),(91,96,'auto',1,0,'2026-08-08 19:20:58','2026-08-08 19:20:58'),(92,97,'auto',1,1,'2026-08-08 19:20:58','2026-08-08 19:20:58'),(93,98,'auto',1,0,'2026-08-08 19:20:58','2026-08-08 19:20:58'),(94,99,'auto',1,0,'2026-08-08 19:20:59','2026-08-08 19:20:59'),(95,100,'auto',1,0,'2026-08-08 19:20:59','2026-08-08 19:20:59'),(96,101,'auto',1,0,'2026-08-08 19:21:00','2026-08-08 19:21:00'),(97,102,'auto',1,0,'2026-08-08 19:21:00','2026-08-08 19:21:00'),(98,103,'auto',1,0,'2026-08-08 19:21:01','2026-08-08 19:21:01'),(99,104,'auto',1,0,'2026-08-08 19:21:01','2026-08-08 19:21:01'),(100,105,'auto',1,0,'2026-08-08 19:21:02','2026-08-08 19:21:02'),(101,106,'auto',1,0,'2026-08-08 19:21:02','2026-08-08 19:21:02'),(102,107,'auto',1,0,'2026-08-08 19:21:03','2026-08-08 19:21:03'),(103,108,'auto',1,0,'2026-08-08 19:21:03','2026-08-08 19:21:03'),(104,109,'auto',1,0,'2026-08-08 19:21:04','2026-08-08 19:21:04'),(105,110,'auto',1,0,'2026-08-08 19:21:04','2026-08-08 19:21:04'),(106,111,'auto',1,0,'2026-08-08 19:21:05','2026-08-08 19:21:05'),(107,112,'auto',1,0,'2026-08-08 19:21:05','2026-08-08 19:21:05'),(108,113,'auto',1,0,'2026-08-08 19:21:06','2026-08-08 19:21:06'),(109,114,'auto',1,0,'2026-08-08 19:21:06','2026-08-08 19:21:06'),(110,115,'auto',1,0,'2026-08-08 19:21:07','2026-08-08 19:21:07'),(111,116,'auto',1,0,'2026-08-08 19:21:07','2026-08-08 19:21:07'),(112,117,'auto',1,0,'2026-08-08 19:21:08','2026-08-08 19:21:08'),(113,118,'auto',1,0,'2026-08-08 19:21:08','2026-08-08 19:21:08'),(114,119,'auto',1,0,'2026-08-08 19:21:09','2026-08-08 19:21:09'),(115,120,'auto',1,0,'2026-08-08 19:21:09','2026-08-08 19:21:09'),(116,121,'auto',1,0,'2026-08-08 19:21:10','2026-08-08 19:21:10'),(117,122,'auto',1,0,'2026-08-08 19:21:10','2026-08-08 19:21:10'),(118,123,'auto',1,0,'2026-08-08 19:21:11','2026-08-08 19:21:11'),(119,124,'auto',1,0,'2026-08-08 19:21:11','2026-08-08 19:21:11'),(120,125,'auto',1,0,'2026-08-08 19:21:12','2026-08-08 19:21:12'),(121,126,'auto',1,0,'2026-08-08 19:21:12','2026-08-08 19:21:12'),(122,127,'auto',1,0,'2026-08-08 19:21:13','2026-08-08 19:21:13'),(123,128,'auto',1,0,'2026-08-08 19:21:13','2026-08-08 19:21:13'),(124,1,'dark',1,1,'2026-08-08 19:27:15','2026-08-10 16:50:09');
/*!40000 ALTER TABLE `user_preferences` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `dni` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending_invite',
  `relationship_type` varchar(255) NOT NULL DEFAULT 'owner',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `hire_date` timestamp NULL DEFAULT NULL,
  `resignation_date` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `first_login_at` timestamp NULL DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `login_count` int(11) NOT NULL DEFAULT 0,
  `last_login_ip` varchar(255) DEFAULT NULL,
  `last_login_agent` varchar(255) DEFAULT NULL,
  `terms_accepted_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=129 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Alejandro','Lo Presti','superadmin@laranita.com','+5491133334444','11222333','active','superadmin','2026-08-08 19:20:10','$2y$12$RaP.naM.erAFp5PcDT0Yy.sV9z3yNEO96JcgbQHgb1y3se4dzqLq.',NULL,NULL,NULL,'2026-08-08 19:20:10','2026-08-31 17:45:18',9,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:154.0) Gecko/20100101 Firefox/154.0','2026-08-08 19:20:10',NULL,'2026-08-08 19:20:10','2026-08-31 17:45:18',NULL),(2,'María Marta','Fernández','admin1@laranita.com','+5491144445555','22333444','active','admin','2026-08-08 19:20:10','$2y$12$QxxoXuGbHG4yK.tQf5cMwOMm0.c2HDjnklRKmJJNIW3ggXn20hHEG',NULL,NULL,NULL,'2026-08-08 19:20:10','2026-08-08 19:20:10',1,NULL,NULL,'2026-08-08 19:20:10',NULL,'2026-08-08 19:20:10','2026-08-08 19:20:10',NULL),(3,'Juan Carlos','Pérez','admin2@laranita.com','+5491155556666','33444555','active','admin','2026-08-08 19:20:11','$2y$12$f0vKDowTFPG1pXeKBRtL1.mbm90JXgLWzfZLM4zeBr5H0dhAxx8ZO',NULL,NULL,NULL,'2026-08-08 19:20:11','2026-08-08 19:20:11',1,NULL,NULL,'2026-08-08 19:20:11',NULL,'2026-08-08 19:20:11','2026-08-08 19:20:11',NULL),(4,'Esteban','Gómez','contabilidad@laranita.com','+5491166667777','44555666','active','accounting','2026-08-08 19:20:11','$2y$12$xUzrDVcffjHJtKODcXVX5e.GvDgBXEllLFqGYYZOZg7H8FELHDf9S',NULL,NULL,NULL,'2026-08-08 19:20:11','2026-08-08 19:20:11',1,NULL,NULL,'2026-08-08 19:20:11',NULL,'2026-08-08 19:20:11','2026-08-08 19:20:11',NULL),(5,'Ramiro','López','operador1@laranita.com','+5491177778888','55666777','active','operator','2026-08-08 19:20:12','$2y$12$7gr9/hpJjSVImrHeIE4S1OmgPVimaP.GEe7DjwLfnBIHNGILLyiRO',NULL,NULL,NULL,'2026-08-08 19:20:12','2026-08-08 19:20:12',1,NULL,NULL,'2026-08-08 19:20:12',NULL,'2026-08-08 19:20:12','2026-08-08 19:20:12',NULL),(6,'OLGA','GARCIA','o.garcia@laranita.com',NULL,'22466042','active','owner',NULL,'$2y$12$b.xEAzpal5ByK9v0hNXikuoMvvv5vS0w7x0yv0Sbxfx0rH90HZ5Je',NULL,NULL,NULL,'2026-08-10 16:25:09','2026-08-10 18:55:54',2,'192.168.2.136','Mozilla/5.0 (iPhone; CPU iPhone OS 26_5_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/151.0.7922.57 Mobile/15E148 Safari/604.1','2026-08-10 18:56:10',NULL,'2026-08-08 19:20:13','2026-08-10 18:56:10',NULL),(7,'SILVERA','AGUSTIN','s.agustin@laranita.com',NULL,'37597336','active','owner',NULL,'$2y$12$UaBb08YlExvDf82ffok0Be/plPYI4sN7bYvoQVpCCK5DNzD6ncTae',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:13','2026-08-08 19:20:13',NULL),(8,'Alejandro','Moliné','a.moline@laranita.com',NULL,'37097918','active','owner',NULL,'$2y$12$vZrXIq5MPvKAlglNpK3ayutcBasQ35Jz4rZ9z/hRNEVO1jrTmkv7q',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:14','2026-08-08 19:20:14',NULL),(9,'GISELE - En Legales','LUCERO','g.lucero@laranita.com',NULL,'45265781','active','owner',NULL,'$2y$12$EMsFe40FP3lmMyFeB0cAGeja57QRPccy2K70chII0a.4fP4t1au8m',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:14','2026-08-08 19:20:14',NULL),(10,'MANGONE','NESTOR','m.nestor@laranita.com',NULL,'29442500','active','owner',NULL,'$2y$12$ijv8NXzXuiPMJh0WC3gcfezO4ANYXdv3Lsk17ZFH4IQepibJ5zkJS',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:15','2026-08-08 19:20:15',NULL),(11,'ALEJANDRA','MARANDOLA','a.marandola@laranita.com',NULL,'16266816','active','owner',NULL,'$2y$12$ZYFiMKSovaKQFhLO.GnOWuE34Ixx1q.kSh1LVYvx0jI7CUe0q7bXm',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:15','2026-08-08 19:20:15',NULL),(12,'GRACIELA En legales','SANTANA','g.santana@laranita.com',NULL,'38511326','active','owner',NULL,'$2y$12$X/UA3Pu7xsbIZKQs9WGgGO/UCc.K7q8xvrpSkg1KyEc9zMrFeFV16',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:16','2026-08-08 19:20:16',NULL),(13,'FERNANDO','CLARO','f.claro@laranita.com',NULL,'38364413','active','owner',NULL,'$2y$12$7ShH.RRODP4owqzD2rRsKuZdFFgSL3dQw8S0hu3xRaPw2NELpDIiW',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:16','2026-08-08 19:20:16',NULL),(14,'ARANDA','HERNAN','a.hernan@laranita.com',NULL,'29630048','active','owner',NULL,'$2y$12$s4mdK7Nu9CRbGnpBvpR7XeQPVsFZWtUZ5A6ZpAZl19q0zbPWMNYYC',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:17','2026-08-08 19:20:17',NULL),(15,'RUBEN','CANTELMI','r.cantelmi@laranita.com',NULL,'36575858','inactive','owner',NULL,'$2y$12$YwLmZiQa9aayoAlP6Yk.5.666C21Qx9AeNUGeoRQA0CIrZYu3KHwu',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:17','2026-08-08 19:20:17',NULL),(16,'EDGAR','ROGGENBAU','e.roggenbau@laranita.com',NULL,'33817502','active','owner',NULL,'$2y$12$KH9D3CD8ykedtTCgqhxDXeveAseOZwtTHnv0Q4Fx77hm3fQQ3oJFK',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:18','2026-08-08 19:20:18',NULL),(17,'RIBERA','JOAQUIN','r.joaquin@laranita.com',NULL,'37282817','active','owner',NULL,'$2y$12$2NJywughEBtSkSGdTP6XceP4Fs20MuWmSZu/6PJRYh.oYQ0casszu',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:18','2026-08-08 19:20:18',NULL),(18,'BENETTI','NORMA - Legales','b.normalegales@laranita.com',NULL,'27796819','active','owner',NULL,'$2y$12$8rhA/LmWoAhfYSlLS2YwjOUsPECv7byFQLRcnki9OjMSMGrK1ELK2',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:19','2026-08-08 19:20:19',NULL),(19,'SANTORA','ANA','s.ana@laranita.com',NULL,'27302232','active','owner',NULL,'$2y$12$zlTPptG7Mp2D6qvgJc6YB./6wj6WLUtBXU3m.DrTLniQDS20NeBRe',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:19','2026-08-08 19:20:19',NULL),(20,'ARIAS','STELLA MARIS','a.stellamaris@laranita.com',NULL,'31073266','active','owner',NULL,'$2y$12$Fr7sankH1b.6wqzPExV1Lu5/mB5UcX8K8FJWUR/lOrvsGE8ux.L8m',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:20','2026-08-08 19:20:20',NULL),(21,'DIEGO','GANTUS','d.gantus@laranita.com',NULL,'36957351','active','owner',NULL,'$2y$12$Td6C3hYPcZe3iYtURaOQTujpWLGNk5bWzK8Qt6VAreyKziAE3ITQS',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:20','2026-08-08 19:20:20',NULL),(22,'GUILLERMO','ARMENTANO','g.armentano@laranita.com',NULL,'12822748','active','owner',NULL,'$2y$12$cFpANivhhwwnNCxhVgUaneoqQz3J7E6p31apBx6HS0M.HTTCooCDu',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:21','2026-08-08 19:20:21',NULL),(23,'MONICA','FARAH','m.farah@laranita.com',NULL,'24686173','active','owner',NULL,'$2y$12$QRFp7q63RkNuvuQ5kIxo7OGX2QKEgYhNz8ITUd.I28D6zyBKEFaOK',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:21','2026-08-08 19:20:21',NULL),(24,'FOSINI','MATIAS','f.matias@laranita.com',NULL,'18334049','active','owner',NULL,'$2y$12$hCXE.OitR2dqvRLVCHT1Kegof90WrlfV5wFMQYSpflZcgpomq1SF6',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:22','2026-08-08 19:20:22',NULL),(25,'OMAR','GRANDE','o.grande@laranita.com',NULL,'25668193','active','owner',NULL,'$2y$12$73XPgNaN4uDya4YfFVhLouw7ZX7BgpT8kWiwo1yfQwV65kfXzh4Li',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:22','2026-08-08 19:20:22',NULL),(26,'Ma Belén/MADEO, Lucas Nicolas','CANO CODECÁ','m.canocodeca@laranita.com','11 2650 5916','30307136','active','owner',NULL,'$2y$12$AKas3Inn7sq46rC9Gm.mQuZ7dgC9PReVVvikvZyWsDLkGTotJFM4.',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:23','2026-08-08 19:20:23',NULL),(27,'SEMBEROIZ','PABLO','s.pablo@laranita.com',NULL,'10341054','active','owner',NULL,'$2y$12$Bl3gofUnrAAtxci0fDWbu.YQp9UhnhM3sbGXyP57wyhKzpdFzLeA.',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:23','2026-08-08 19:20:23',NULL),(28,'MARTIN','BERNAL / DOLORES CASAL','m.bernaldolorescasal@laranita.com',NULL,'13520961','active','owner',NULL,'$2y$12$WL0g5iL7qRB3pjcnWbZ0kuLyHCZ3.mEiCIm92y0Rc/Y1VVsdHX24W',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:24','2026-08-08 19:20:24',NULL),(29,'BENVENUTO','Jorge / BASUALDO María','b.jorgebasualdomaria@laranita.com',NULL,'45819015','active','owner',NULL,'$2y$12$TkzkwhE4o0vfVL6r6r2gDOipQXDjZ/WVhTTSB2CR7zzSCnwR7kzm.',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:24','2026-08-08 19:20:24',NULL),(30,'MARCELA','PRADELLS','m.pradells@laranita.com',NULL,'45331679','active','owner',NULL,'$2y$12$dNF1qYCFzArGaT6bS4w.Aesf.CympHk3046BW0Nwu1dTnC13J9JQu',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:25','2026-08-08 19:20:25',NULL),(31,'GERMAN','PLESSEN','g.plessen@laranita.com',NULL,'38254160','active','owner',NULL,'$2y$12$XiJhm4B8FZ2jkjPgCi0ZCu8hE2IYr8mnmerr918I.4TFcrFY3iY/y',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:25','2026-08-08 19:20:25',NULL),(32,'VANESA','JUAREZ','v.juarez@laranita.com',NULL,'10127373','active','owner',NULL,'$2y$12$UhnMNZPxjVgRJ5QuCwbHMeH1RAT2ChMjMrXwg0RpRrkZ41ns/W2LC',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:26','2026-08-08 19:20:26',NULL),(33,'LEONARDO','MAGLIOCCO','l.magliocco@laranita.com',NULL,'41720192','active','owner',NULL,'$2y$12$7GVdJbFDUL18daNquLWNT.gxC4ArhjaSNsFDCo.T1xa.Qznww8YKG',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:26','2026-08-08 19:20:26',NULL),(34,'FERNANDO','PRATI','f.prati@laranita.com',NULL,'30554940','active','owner',NULL,'$2y$12$9iP9lHxjbqAqyIzY1TZFquG1eDRNBnmbBgp1yRoa1pBI9CHVkuDMG',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:27','2026-08-08 19:20:27',NULL),(35,'DIEGO ANDRES','SEFERCHEOGLOU','d.sefercheoglou@laranita.com',NULL,'39783204','active','owner',NULL,'$2y$12$xaNOdxx.i36yIM2/xLRHZOR5ciEXEBq46IgQfshR2V0h1vXSpbQNy',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:27','2026-08-08 19:20:27',NULL),(36,'MARIO En Juicio','ESPARRICA','m.esparrica@laranita.com',NULL,'18590202','active','owner',NULL,'$2y$12$l9tOtY9FlFyqZ4cYg7QBgeQ8VUkAG4UetkAp.l9DvGVQXoEBsWtSu',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:28','2026-08-08 19:20:28',NULL),(37,'Agustín','PIEDRABUENA','a.piedrabuena@laranita.com',NULL,'26990458','active','owner',NULL,'$2y$12$MksJYsCXjJEajvvCrdJvFeUEouYI5BSjwU9YBnzU57o8i0MbVnjza',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:28','2026-08-08 19:20:28',NULL),(38,'MARIA L - Acuerdo','MONACCI','m.monacci@laranita.com',NULL,'33182763','active','owner',NULL,'$2y$12$IX/yEH28Mbjrh6uLJqnjEO228Y43/8kn4n6nTlxb8ctGor0ISxufq',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:29','2026-08-08 19:20:29',NULL),(39,'EZEQUIEL/ MONTORO, LUCAS Legales','MONTORO','e.montoro@laranita.com',NULL,'35945294','active','owner',NULL,'$2y$12$H3MQSzWOpFZhq.7AiLts.OrLbAhuAY4KR5P6Jh4.8ju50FoAEd3qi',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:29','2026-08-08 19:20:29',NULL),(40,'BAEZ','FLORENCIA','b.florencia@laranita.com',NULL,'28976095','active','owner',NULL,'$2y$12$stontM6TdbUSJlhdpUbJ5uil.0AzUTqhe.46MGxoMqiFS5WbmUZvm',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:30','2026-08-08 19:20:30',NULL),(41,'JUAN CARLOS','MENDOZA','j.mendoza@laranita.com',NULL,'30397781','active','owner',NULL,'$2y$12$deCm1J8F5Y4QysYah7oK3.gB9fBuh7lPk7qV4F/pPVg8/xCzlGh5G',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:30','2026-08-08 19:20:30',NULL),(42,'LEONARDO','CANARIO','l.canario@laranita.com',NULL,'13249589','active','owner',NULL,'$2y$12$MNaUua4RPSrYEWzQQtEDQudtwArslgPYVUfR3b6cdNSFMcbCrvRzi',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:31','2026-08-08 19:20:31',NULL),(43,'CARINA Y SATRIANO','VIETA','c.vieta@laranita.com',NULL,'13535681','active','owner',NULL,'$2y$12$7.Samw0DBC9klBp6PZlxjerRyvJOsCSvPZsaqG97kdbJiYXH65YWO',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:31','2026-08-08 19:20:31',NULL),(44,'RAMIRO','DEL MORAL','r.delmoral@laranita.com',NULL,'31190826','active','owner',NULL,'$2y$12$eHhNor02onIDxqeKi97xF..1A5jKztuvdLxqZ91drWP/8n.wa35de',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:32','2026-08-08 19:20:32',NULL),(45,'ALFREDO','ROSSI','a.rossi@laranita.com',NULL,'43241420','active','owner',NULL,'$2y$12$36DE2LaN7Qk4cyC9S6BMQ.TjPqZGyiZveEILXkun8XvtL6l.PUKUS',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:32','2026-08-08 19:20:32',NULL),(46,'CLAUDIO M.','FERNANDEZ','c.fernandez@laranita.com',NULL,'41537537','active','owner',NULL,'$2y$12$d12foVBzOoW1dztR0LA5nu243d0wZBVMX0UUlNJlwnRQ9R0D/n0Xa',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:33','2026-08-08 19:20:33',NULL),(47,'DANIELA ANA','PETKOVSEK','d.petkovsek@laranita.com',NULL,'33232960','active','owner',NULL,'$2y$12$hqGn1AnnrTZChOARnZgSi.W3acAtatgXvHQ/GN7zRZmkrLPM6QH8e',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:33','2026-08-08 19:20:33',NULL),(48,'SEBASTIAN','MARCHESI','s.marchesi@laranita.com','(011) 15 4447 0155','12116724','active','owner',NULL,'$2y$12$SbDn03HfdhM65MvOOS2H4epxCTcRQcx8KnEQ1ILEMMWYRZQIez0/a',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:34','2026-08-08 19:20:34',NULL),(49,'MARIA','GONZALEZ STEGEMANN','m.gonzalezstegemann@laranita.com',NULL,'40060489','active','owner',NULL,'$2y$12$BIi7rsIQm66Dh1jqWKEFDOAscPT6VdHr8DsGoconBXNA7Y0wucLGO',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:34','2026-08-08 19:20:34',NULL),(50,'SANDRA','PINZON GARZON','s.pinzongarzon@laranita.com',NULL,'19797105','active','owner',NULL,'$2y$12$TKd/ZDLjna5cENTgCj5BS.EmVjiMUSQrugEU/GGeJr1DDAU7hBx66',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:35','2026-08-08 19:20:35',NULL),(51,'FERNANDEZ','ANSELMI MELINA','f.anselmimelina@laranita.com',NULL,'27346841','active','owner',NULL,'$2y$12$axsO.7q7HjcCbagMAzAP8.P9jTy9ccjJUABNpLU7UqOArHA1dPaGO',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:35','2026-08-08 19:20:35',NULL),(52,'ALFREDO','ORDUÑA','a.orduna@laranita.com',NULL,'25189002','active','owner',NULL,'$2y$12$8Fy/HIuyE/LCaKh1yp9Bu.UdCMm4bG4N37QMgGjpaAB/yd/Xo.S7K',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:36','2026-08-08 19:20:36',NULL),(53,'AUDISIO','IGNACIO','a.ignacio@laranita.com',NULL,'37486417','active','owner',NULL,'$2y$12$spax8MtXReNbmXXAWKnPXOoRIS/PjB74mPvfCwr6FTU68ejMJXjhu',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:36','2026-08-08 19:20:36',NULL),(54,'WILLIAM','PHILPOTT','w.philpott@laranita.com',NULL,'25315364','active','owner',NULL,'$2y$12$9JD3qF9rpdakmFUWTuJ.2utSZq6cXtIOigCw6hZudh62gQa6/MN42',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:37','2026-08-08 19:20:37',NULL),(55,'GERARDO','RUZO','g.ruzo@laranita.com',NULL,'27622258','active','owner',NULL,'$2y$12$1KIsDDPWp8MMkPnajqwoaeKYejC.X8gFc7XgZeNXjCwJN9MBcSAP2',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:37','2026-08-08 19:20:37',NULL),(56,'Gagliardini','Facundo','g.facundo@laranita.com',NULL,'30523593','active','owner',NULL,'$2y$12$DSg6A.PTw7cRtjdnieiMdO1QDUp66b/HM6FVJXEb7lDeB1OD6YR/K',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:38','2026-08-08 19:20:38',NULL),(57,'FOGLIA','FRANCISCO','f.francisco@laranita.com',NULL,'38332377','active','owner',NULL,'$2y$12$71AcWhTHbdlfKXIff0QFhO8udUGp8RRk6dGmCRqKxbk/vbFaYO5f6',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:38','2026-08-08 19:20:38',NULL),(58,'ALFREDO','COBOS','a.cobos@laranita.com',NULL,'23750420','active','owner',NULL,'$2y$12$HAGuNcldr7xpeAomD1y7hO.HOiZ5N97CpSfBWYEt0qkIFomB9iRra',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:39','2026-08-08 19:20:39',NULL),(59,'OSCAR MARIO','PANGARO','o.pangaro@laranita.com','(011) 15 6159 0624','30632978','active','owner',NULL,'$2y$12$yGMblZ3zT4RTC0GZTf2oxOjBTmFA/bXZk7k8lTsKcA.hmTaLA3kgu',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:39','2026-08-08 19:20:39',NULL),(60,'DIEGO','FUSTER','d.fuster@laranita.com',NULL,'22563766','active','owner',NULL,'$2y$12$/sxQSEuq5BEbmVxtwSj36.xoA8lh6ZIqqLOIVtLrzV4kPjDmyQcOm',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:40','2026-08-08 19:20:40',NULL),(61,'MARTIN','SUSBIELLES','m.susbielles@laranita.com',NULL,'22372689','active','owner',NULL,'$2y$12$4WN1O5R8eCHe402AWnrnzebqxHHWv45DG6uDyCUzst6IX8uQXGDqe',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:40','2026-08-08 19:20:40',NULL),(62,'WALTER','LABONIA','w.labonia@laranita.com',NULL,'17883717','active','owner',NULL,'$2y$12$Kk4V9W7PC7oc9FUfWQfR1utyXRSAz8bsPvDFeXp8XXpwCRNtNPKUm',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:41','2026-08-08 19:20:41',NULL),(63,'JOSE','BERTOTTI','j.bertotti@laranita.com',NULL,'45263873','active','owner',NULL,'$2y$12$iXWjBddAgAAtJbKmnKx.Mu3oqAkgO5hAqZoAeqK/ZpNeL.K1x6zvC',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:41','2026-08-08 19:20:41',NULL),(64,'SILVINA','TORELLA','s.torella@laranita.com',NULL,'15362927','active','owner',NULL,'$2y$12$bcXBwllX7Pl/OCgo8JDThufEF6ohYrnZZ6yQnSKQYzwrfaT62o872',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:42','2026-08-08 19:20:42',NULL),(65,'PABLO','VERNENGO','p.vernengo@laranita.com',NULL,'21639411','active','owner',NULL,'$2y$12$o9O/pen6Rtb6kFxjdR3fHuSkruVOAi3N42GCVpUAyZtwVi6Ro7jvC',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:42','2026-08-08 19:20:42',NULL),(66,'OTERO','/ ALBORNOZ','o.albornoz@laranita.com',NULL,'34793659','active','owner',NULL,'$2y$12$1jetQYtFXHxnO1U06rDmmuyMi80LlMWSrgfWqpCyWvUYgdlJYwPau',NULL,NULL,NULL,'2026-08-31 17:48:09','2026-08-31 18:54:16',2,'192.168.2.136','Mozilla/5.0 (iPhone; CPU iPhone OS 26_6_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/152.0.7977.64 Mobile/15E148 Safari/604.1','2026-08-31 17:48:34',NULL,'2026-08-08 19:20:43','2026-08-31 18:54:16',NULL),(67,'MARCELO En Juicio','LEONARDI','m.leonardi@laranita.com',NULL,'38660660','active','owner',NULL,'$2y$12$HSpmt/fzL4ovKwMHOcjgJOy/FkOVAu9tT3JdoafbbFRshtZSIhgLK',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:43','2026-08-08 19:20:43',NULL),(68,'J.PABLO','LASSERRE','j.lasserre@laranita.com',NULL,'45041672','active','owner',NULL,'$2y$12$XEsf06gHJr77wgvD4TvQbO2i5LHstBxqxBb6ALUcbIG7XzKhIW5Sy',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:43','2026-08-08 19:20:43',NULL),(69,'CARLOS','BOVERI','c.boveri@laranita.com',NULL,'41444948','active','owner',NULL,'$2y$12$b7NP9WEFM9Oou5Jbt2gL.uE9gHvCX/Ug/M4hVR0yFrC8nqJuR5Z8a',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:44','2026-08-08 19:20:44',NULL),(70,'PABLO Y LAREO','SAAVEDRA','p.saavedra@laranita.com',NULL,'13033017','active','owner',NULL,'$2y$12$TrDtbOFVTbXBQwF0FolCpunKeh4Go.m9No9bP2eYL5jS03Q.yR49O',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:44','2026-08-08 19:20:44',NULL),(71,'MARIO EDUARDO','ACKERMAN','m.ackerman@laranita.com',NULL,'45854257','active','owner',NULL,'$2y$12$NaPagG9McUdYXoHvknyKKujFWiVFYmSISHcP.wkx0PBltbLu5onTq',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:45','2026-08-08 19:20:45',NULL),(72,'MARCELO','GARCIA VARA','m.garciavara@laranita.com',NULL,'14260437','active','owner',NULL,'$2y$12$roHNi4WV2tr5RPOIq9GmjOy5ut5bl73YpAE4bCL.biUjLIqEUdz0C',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:45','2026-08-08 19:20:45',NULL),(73,'NICOLAS','BURIS','n.buris@laranita.com',NULL,'26814340','active','owner',NULL,'$2y$12$8ScjnF/rjJFLU.dOUBICEeVA.u8K5nLt836CkQwP.p6Yu0NcNmJDW',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:46','2026-08-08 19:20:46',NULL),(74,'PABLO','PENELA','p.penela@laranita.com',NULL,'14708532','active','owner',NULL,'$2y$12$YHncV09/tMBQpVKGZCv/5.LOnvkWyKJxd6d3FIB6a7d32xFULry2a',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:46','2026-08-08 19:20:46',NULL),(75,'AMAYA','ANDRES','a.andres@laranita.com',NULL,'16140771','active','owner',NULL,'$2y$12$GI0hf34laAGkKLCcGPNqReTP6IvKlH8qgIf2j7r5rsZ1bZDG7USvu',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:47','2026-08-08 19:20:47',NULL),(76,'DANIELA','REY','d.rey@laranita.com',NULL,'12548228','active','owner',NULL,'$2y$12$UqLJ915lN9VcuW144eGDTurJd/g5vXbwTfrcAAo3shuD00z5hHe.q',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:47','2026-08-08 19:20:47',NULL),(77,'GARCIA','SILVIO SIXTO','g.silviosixto@laranita.com',NULL,'37329785','active','owner',NULL,'$2y$12$ZKWeYu8rZW63Y8MVb4JaKep2KP/PSJvUMaByRpdKmtdMHfklDQoz6',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:48','2026-08-08 19:20:48',NULL),(78,'VALERIA','SALADINO','v.saladino@laranita.com',NULL,'15573074','active','owner',NULL,'$2y$12$8HUkPwbuwl/TtwJZn84nZOlJwQKEwz1pSSLdALOfrKOGBGBZIwA/y',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:48','2026-08-08 19:20:48',NULL),(79,'CASTRO','PAULA','c.paula@laranita.com',NULL,'28620952','active','owner',NULL,'$2y$12$5XkcKL4F8y8S/.oKkB3jyOp3b52cea4TGfEvfJt8IhuciNfiJxxWy',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:49','2026-08-08 19:20:49',NULL),(80,'FEDERICO','RUBISTEIN','f.rubistein@laranita.com',NULL,'19283628','active','owner',NULL,'$2y$12$HBX6QIWxIdBJpm2kaoytS.Vy1SbmMi5yjPCNf.erqy8p5.Jlh2ptW',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:49','2026-08-08 19:20:49',NULL),(81,'WALTER','GADEA','w.gadea@laranita.com',NULL,'42153864','active','owner',NULL,'$2y$12$HrQJvw2In7h2i5Y/n40m4ey2t7dFe8W3L6u3aHU0ydNQNh0ygDscu',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:50','2026-08-08 19:20:50',NULL),(82,'EUGENIO','CAMBACERES','e.cambaceres@laranita.com',NULL,'33986780','active','owner',NULL,'$2y$12$E28vb/Kx0IS6wp2M.5d93eZd.hI0BnygECTQWrsyb6NXDq9DLxopS',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:50','2026-08-08 19:20:50',NULL),(83,'CARLOS','D´ONOFRIO','c.donofrio@laranita.com',NULL,'45052288','active','owner',NULL,'$2y$12$P3U6MSiEv.aejnBW0nSN7OXUmZYZ3psArx2Lnt8qbocBFhEOHzqPa',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:51','2026-08-08 19:20:51',NULL),(84,'MARCELO','GRECCO','m.grecco@laranita.com',NULL,'15341572','active','owner',NULL,'$2y$12$fGEWouhcp3mQQCri3yRCIe/wOnDtNzkJWH5jlRVFbyUH19NZDd1Ba',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:51','2026-08-08 19:20:51',NULL),(85,'GOMEZ','MURIEGA YANINA','g.muriegayanina@laranita.com',NULL,'41683892','active','owner',NULL,'$2y$12$ProoTW/IdQZ4uRpQKCqOCOUATfN60QxpUgOH59q2JbNwT4d5tNegW',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:52','2026-08-08 19:20:52',NULL),(86,'PAULO FRANCISCO','BELLUSCHI','p.belluschi@laranita.com',NULL,'33577369','active','owner',NULL,'$2y$12$qHQb4gG4NRw2WtCr.5zQweljU77GgkQl4UACaOhR91A.zlSKTO0/a',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:52','2026-08-08 19:20:52',NULL),(87,'TESTA','MARCELO','t.marcelo@laranita.com',NULL,'22967431','active','owner',NULL,'$2y$12$7U5jU7g.c/gnRDnDqM4QkOFOPaEdMjdyl2tiA.KJAauRXRsteF67.',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:53','2026-08-08 19:20:53',NULL),(88,'LUCIANA','PROL','l.prol@laranita.com',NULL,'30268144','active','owner',NULL,'$2y$12$KdRirGG8nK0RckTk/sCioe79DyYM68Th5XTphqGQb6agk1nKtIbri',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:53','2026-08-08 19:20:53',NULL),(89,'MACCIO','ALEJANDRA/ CANE GONZALO','m.alejandracanegonzalo@laranita.com',NULL,'42359515','active','owner',NULL,'$2y$12$iCS38e0j0RzgxejndMs0aOFi97xfEL4dydewtGfjkKdBgsVKSthUq',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:54','2026-08-08 19:20:54',NULL),(90,'VELACION GILBERTO','SILVA','v.silva@laranita.com',NULL,'32110915','active','owner',NULL,'$2y$12$E4ZyQyNfQIiqmZ6F35WLdOUKQGHeqOvQBjt6JiN8gONGMeEVVpY1G',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:54','2026-08-08 19:20:54',NULL),(91,'CANDIANO','PEDRO','c.pedro@laranita.com',NULL,'26256438','active','owner',NULL,'$2y$12$etD5rglJ8d7IJnbxhZqGDehQ23oCz8CYRp4fm5jzEGjdLdk.i58pq',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:55','2026-08-08 19:20:55',NULL),(92,'MARIO','VALLEDOR LASTRA','m.valledorlastra@laranita.com','(011) 15 4473 0985','23538840','active','owner',NULL,'$2y$12$sYe5IsYvhhnsHrZR7n15KO2Hg6QXycEIqHG.C0HUXzfbGXqkP/Vm6',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:56','2026-08-08 19:20:56',NULL),(93,'POMPOSIELLO','JUAN I.','p.juani@laranita.com',NULL,'42577437','active','owner',NULL,'$2y$12$78xliNxZj9tE6MziBdhIl.VCBelJDEg2VUJzp0TUz1TqkDhpJb9L2',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:56','2026-08-08 19:20:56',NULL),(94,'MARTÍN','CAROSSINO','m.carossino@laranita.com',NULL,'42398315','active','owner',NULL,'$2y$12$KD7yGunjKC4RuEPvP6eqp.VjGzcq9CJqyn710mgRKqe4xAMMYkTmu',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:56','2026-08-08 19:20:56',NULL),(95,'GABRIELA','CASANOVAS','g.casanovas@laranita.com',NULL,'28233381','active','owner',NULL,'$2y$12$B7.le90Kij9g7sEcOjCR7OD78JAEE4v1qW2DCF1pE5re/FpciyJE2',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:57','2026-08-08 19:20:57',NULL),(96,'UKI','GOÑI','u.goni@laranita.com',NULL,'42356309','active','owner',NULL,'$2y$12$LvMRQ8XHbxPkQm1TnWHPwusqhKKdpE2TOo638MTvztcqgW5u4pUW.',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:58','2026-08-08 19:20:58',NULL),(97,'TOMAS - Rosario','BISCEGLIA','t.bisceglia@laranita.com','11 6462 0201 (Rosario, esposa)','14632617','active','owner',NULL,'$2y$12$pee5KCCjsSrvQnE0IuNBlO.ZFpmH2fyl6fYGpuURMf8l0ZYgWdaXK',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:58','2026-08-08 19:20:58',NULL),(98,'LUIS','MAYORGA','l.mayorga@laranita.com',NULL,'10717486','active','owner',NULL,'$2y$12$rwkAf3wGGAx32JTYkvEOQuwEymbwVEps8bJTp7gLJ8bOwF7sK1PSW',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:58','2026-08-08 19:20:58',NULL),(99,'CANGIANO','COSENTINO','c.cosentino@laranita.com',NULL,'37445299','active','owner',NULL,'$2y$12$ygwudQ7G9tULpFn3eOxyEuH/6/7hZvh2jSDHVbzQ3ZEBEHpwUW95S',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:59','2026-08-08 19:20:59',NULL),(100,'ENRIQUE','DUHART','e.duhart@laranita.com',NULL,'35238175','active','owner',NULL,'$2y$12$RecC8sM3DXD/30x9Wl76uuJpQBqfhTCgpCb6ZoeLWQRmyYYmEHbDq',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:20:59','2026-08-08 19:20:59',NULL),(101,'LUJAN HORACIO MARIA','ABRAM','l.abram@laranita.com',NULL,'27644227','active','owner',NULL,'$2y$12$Y/W.0DsXe7CWmJPog2PCq.i3mJmLfz3c6izMXZFLoj/hwvw8kNYXe',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:21:00','2026-08-08 19:21:00',NULL),(102,'PAULO','MOYANO','p.moyano@laranita.com',NULL,'24006713','active','owner',NULL,'$2y$12$o71S9IDgVIaRbtC1.vzxFeuth/DeRBw8wm/HBz4reiAuSSBcEd1vq',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:21:00','2026-08-08 19:21:00',NULL),(103,'RODRIGUEZ','WALTER','r.walter@laranita.com',NULL,'43822235','active','owner',NULL,'$2y$12$Q67ODnB65RcqKAuLyDI0SO0MTFvYJDpn8pj9mwHYWULgkYTWuIajO',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:21:01','2026-08-08 19:21:01',NULL),(104,'JOSE G.','DIAZ','j.diaz@laranita.com',NULL,'16515384','active','owner',NULL,'$2y$12$Pv.kIy7nbNA9PcyVHSspDe9OF1WikhEQzp0mhN.vT3O5J8UpNGDeS',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:21:01','2026-08-08 19:21:01',NULL),(105,'RICARDO','CORREIA','r.correia@laranita.com',NULL,'45422334','active','owner',NULL,'$2y$12$Fn86mrOhPzF9yOsspo18UuWgqCW3Vn2TvQZjIgxriCgFGEt7VI/VS',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:21:02','2026-08-08 19:21:02',NULL),(106,'ADRIAN','BESUSCHIO','a.besuschio@laranita.com',NULL,'23350918','active','owner',NULL,'$2y$12$71.Z4QRd7iiciZxUJArtcOKrFSItuakK9BCKiC6O/YAkZfPptW8NC',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:21:02','2026-08-08 19:21:02',NULL),(107,'HERRERA','MARIA PAZ','h.mariapaz@laranita.com',NULL,'21646805','active','owner',NULL,'$2y$12$BGEfE2nME.O5hMKejOBWxeBU1HLdETNt3VTK4BtIsWWLIL1xfyg36',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:21:03','2026-08-08 19:21:03',NULL),(108,'ENRIQUE RUBEN','SANTOS','e.santos@laranita.com',NULL,'31361241','active','owner',NULL,'$2y$12$t/K7XVrZgTsnh8EfCfZ9SuSlDuhplG1GMAfStevfPDjPPg0Bq5nz6',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:21:03','2026-08-08 19:21:03',NULL),(109,'RICARDO','FEDERICO','r.federico@laranita.com',NULL,'45892329','active','owner',NULL,'$2y$12$.pN8bx6GLJX4lscZQmKVPuyvHJRh87jpGMm.YuuZtgG/wwcmW6Dcu',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:21:04','2026-08-08 19:21:04',NULL),(110,'NJM','SA','n.sa@laranita.com',NULL,'43540504','active','owner',NULL,'$2y$12$Tbewp7krILYQx8WPmFS9LugSAiDWXzl2Swn/eP3cdsE7aySgIHedS',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:21:04','2026-08-08 19:21:04',NULL),(111,'I.P.S.A','Propietario','i.propietario@laranita.com',NULL,'38809158','active','owner',NULL,'$2y$12$j6zrpxEoGFCxNAxEuIogAOe8ssfCyMofH/YgtcStG8EwmFhR.TeTC',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:21:05','2026-08-08 19:21:05',NULL),(112,'THIAGO','ALDAVE','t.aldave@laranita.com',NULL,'41649806','active','owner',NULL,'$2y$12$eP5jS8iOb1Khz1EpcDvqyuZrxkSCKV6jq1kte9SSlziz0kry8JieW',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:21:05','2026-08-08 19:21:05',NULL),(113,'CERVIÑO','SANTIAGO','c.santiago@laranita.com',NULL,'26833074','active','owner',NULL,'$2y$12$JvtdMd99BCf9UbRwosfozuOrpoF0sAqISe7UulirFfD3ID0ggKFMO',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:21:06','2026-08-08 19:21:06',NULL),(114,'VICENTE','NIGRO','v.nigro@laranita.com',NULL,'45157094','active','owner',NULL,'$2y$12$RQpZK0hURQGkr6dI1mRZhu4FVOzaV7.K8KblPHz47YHg1RhmdmCA.',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:21:06','2026-08-08 19:21:06',NULL),(115,'ADRIAN','COSENTINO','a.cosentino@laranita.com',NULL,'42110520','active','owner',NULL,'$2y$12$5e9.BmgaFSLd05hiGMaFq.gIS7sjjIuQUG0hr7V/Xxe/D3JjwTz3e',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:21:07','2026-08-08 19:21:07',NULL),(116,'HOY','WEST GUILLERMO','h.westguillermo@laranita.com',NULL,'34188322','active','owner',NULL,'$2y$12$txduVeA12CbcjIchzTB8..I6HeQmedhq0v3Gvs3SifVi52GhVwO1.',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:21:07','2026-08-08 19:21:07',NULL),(117,'LEONEL','EZEQUIEL BUHAY','l.ezequielbuhay@laranita.com',NULL,'37317109','active','owner',NULL,'$2y$12$C6mmyEbQpe9oFVxNpPHD9eJnDZyruKMv.PoL9Si2IT8liZ9gO5zVK',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:21:08','2026-08-08 19:21:08',NULL),(118,'DANIEL','HIGA','d.higa@laranita.com',NULL,'38229051','active','owner',NULL,'$2y$12$lwdHOSZt6dI2KrZ4hhozQOcwEj0ECAcQ/iGNTiR6AUowo780UALjC',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:21:08','2026-08-08 19:21:08',NULL),(119,'Francine - Juicio Iniciado','NUSBAUM','f.nusbaum@laranita.com',NULL,'32256719','active','owner',NULL,'$2y$12$WRFXHlFeq6yKGk6uUXps1O/fGgL5UIGegWaaywX7NKooceTV2VtRe',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:21:09','2026-08-08 19:21:09',NULL),(120,'Bema','Cosntrucciones - En Legales','b.cosntruccionesenlegales@laranita.com',NULL,'27324085','active','owner',NULL,'$2y$12$aqFPa3UdRXJaDAsDuWF9pOboZnE9othAtpaDDgjVlw048iF9QWo62',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:21:09','2026-08-08 19:21:09',NULL),(121,'GALARRAGA','IÑAQUI','g.inaqui@laranita.com',NULL,'17746402','active','owner',NULL,'$2y$12$W3VgA0XuxTexocX5aex4Me0q32aHONlnRvm6vgQVrrgtvEteYCTnm',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:21:10','2026-08-08 19:21:10',NULL),(122,'Sebastián','Bracco','s.bracco@laranita.com',NULL,'26707471','active','owner',NULL,'$2y$12$UAosrWCFqt4gvLvq6ifYJeqcuJXAfFdX0R4H5CmzUX4XHqk45Cia.',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:21:10','2026-08-08 19:21:10',NULL),(123,'MARINA','MORERO','m.morero@laranita.com',NULL,'31082672','active','owner',NULL,'$2y$12$Q5fukipnYYUwp8M4vH.GJer/b2PsgOuTsy001HapmF85jvmbHkOJO',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:21:11','2026-08-08 19:21:11',NULL),(124,'PIDRE','GABRIEL','p.gabriel@laranita.com',NULL,'12515408','active','owner',NULL,'$2y$12$oN.OxVgkhmUSccEiSiHGdeqzf2lMQMCZ3FyAldpIIQCHDW43jwp.q',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:21:11','2026-08-08 19:21:11',NULL),(125,'PABLO MARIANO','FERNANDEZ','p.fernandez@laranita.com',NULL,'21280607','active','owner',NULL,'$2y$12$2nta8qm4iBpIZGWILGPQxuFaYoIHfHeYwsLFXT/Fvtdb4.gnkQpju',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:21:12','2026-08-08 19:21:12',NULL),(126,'Paula - MOURELOS, Gabriel','NOTRICA','p.notrica@laranita.com',NULL,'14508158','active','owner',NULL,'$2y$12$W9lUnpSG3j7c2uvBqsiw.eR2hXI.qQKcAh.IVnf1zcqr2rV5XdGUi',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:21:12','2026-08-08 19:21:12',NULL),(127,'ALEJANDRO','TRANI','a.trani@laranita.com',NULL,'20597184','active','owner',NULL,'$2y$12$8NB6tEcNIrHD61DO/rOGHOlrkj47coehJU/pyD/B6nXuCyW3bPCAC',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:21:13','2026-08-08 19:21:13',NULL),(128,'FERREYRA','VERONICA','f.veronica@laranita.com',NULL,'33715541','active','owner',NULL,'$2y$12$0XAVQwUM6gcT/ZUastq.q.li.w5EgeWlk8e45dNXOVzrHKZESmNEW',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'2026-08-08 19:21:13','2026-08-08 19:21:13',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `whatsapp_settings`
--

DROP TABLE IF EXISTS `whatsapp_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `whatsapp_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `provider` varchar(255) NOT NULL DEFAULT 'meta',
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `phone_number` varchar(255) DEFAULT NULL,
  `phone_number_id` varchar(255) DEFAULT NULL,
  `business_account_id` varchar(255) DEFAULT NULL,
  `token` text DEFAULT NULL,
  `secret` varchar(255) DEFAULT NULL,
  `webhook_url` varchar(255) DEFAULT NULL,
  `display_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `whatsapp_settings`
--

LOCK TABLES `whatsapp_settings` WRITE;
/*!40000 ALTER TABLE `whatsapp_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `whatsapp_settings` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-15 14:35:11
