CREATE DATABASE  IF NOT EXISTS `db_fiscalizer` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `db_fiscalizer`;
-- MySQL dump 10.13  Distrib 8.0.42, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: db_fiscalizer
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

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
-- Table structure for table `medicao_itens`
--

DROP TABLE IF EXISTS `medicao_itens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `medicao_itens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `medicao_id` bigint(20) unsigned NOT NULL,
  `projeto_id` bigint(20) unsigned DEFAULT NULL,
  `descricao` varchar(255) NOT NULL,
  `pontos_funcao` decimal(10,2) NOT NULL DEFAULT 0.00,
  `ust` decimal(10,2) NOT NULL DEFAULT 0.00,
  `valor_unitario_pf` decimal(10,2) NOT NULL DEFAULT 0.00,
  `valor_unitario_ust` decimal(10,2) NOT NULL DEFAULT 0.00,
  `valor_total` decimal(14,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `medicao_itens_projeto_id_foreign` (`projeto_id`),
  KEY `medicao_itens_created_by_foreign` (`created_by`),
  KEY `medicao_itens_updated_by_foreign` (`updated_by`),
  KEY `medicao_itens_medicao_id_projeto_id_index` (`medicao_id`,`projeto_id`),
  CONSTRAINT `medicao_itens_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `medicao_itens_medicao_id_foreign` FOREIGN KEY (`medicao_id`) REFERENCES `medicoes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `medicao_itens_projeto_id_foreign` FOREIGN KEY (`projeto_id`) REFERENCES `projetos` (`id`) ON DELETE SET NULL,
  CONSTRAINT `medicao_itens_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `medicao_itens`
--

LOCK TABLES `medicao_itens` WRITE;
/*!40000 ALTER TABLE `medicao_itens` DISABLE KEYS */;
/*!40000 ALTER TABLE `medicao_itens` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-29  1:37:45
