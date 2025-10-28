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
-- Table structure for table `ocorrencias_fiscalizacao`
--

DROP TABLE IF EXISTS `ocorrencias_fiscalizacao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ocorrencias_fiscalizacao` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `contrato_id` bigint(20) unsigned NOT NULL,
  `responsavel_id` bigint(20) unsigned DEFAULT NULL,
  `data_ocorrencia` date DEFAULT NULL,
  `tipo` enum('advertencia','glosa','nao_conformidade','outros') NOT NULL DEFAULT 'outros',
  `gravidade` enum('baixa','media','alta') NOT NULL DEFAULT 'baixa',
  `descricao` text DEFAULT NULL,
  `providencias` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ocorrencias_fiscalizacao_responsavel_id_foreign` (`responsavel_id`),
  KEY `ocorrencias_fiscalizacao_created_by_foreign` (`created_by`),
  KEY `ocorrencias_fiscalizacao_updated_by_foreign` (`updated_by`),
  KEY `ocorrencias_fiscalizacao_contrato_id_tipo_index` (`contrato_id`,`tipo`),
  KEY `ocorrencias_fiscalizacao_data_ocorrencia_index` (`data_ocorrencia`),
  CONSTRAINT `ocorrencias_fiscalizacao_contrato_id_foreign` FOREIGN KEY (`contrato_id`) REFERENCES `contratos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ocorrencias_fiscalizacao_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ocorrencias_fiscalizacao_responsavel_id_foreign` FOREIGN KEY (`responsavel_id`) REFERENCES `pessoas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ocorrencias_fiscalizacao_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ocorrencias_fiscalizacao`
--

LOCK TABLES `ocorrencias_fiscalizacao` WRITE;
/*!40000 ALTER TABLE `ocorrencias_fiscalizacao` DISABLE KEYS */;
/*!40000 ALTER TABLE `ocorrencias_fiscalizacao` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-27 23:48:52
