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
-- Table structure for table `contratos`
--

DROP TABLE IF EXISTS `contratos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contratos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `numero` varchar(30) NOT NULL,
  `objeto` text NOT NULL,
  `contratada_id` bigint(20) unsigned NOT NULL,
  `fiscal_tecnico_id` bigint(20) unsigned DEFAULT NULL,
  `fiscal_administrativo_id` bigint(20) unsigned DEFAULT NULL,
  `gestor_id` bigint(20) unsigned DEFAULT NULL,
  `valor_global` decimal(14,2) NOT NULL DEFAULT 0.00,
  `data_inicio` date DEFAULT NULL,
  `data_fim` date DEFAULT NULL,
  `situacao` enum('vigente','encerrado','rescindido','suspenso') NOT NULL DEFAULT 'vigente',
  `tipo` enum('TI','Serviço','Obra','Material') NOT NULL DEFAULT 'TI',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `contratos_numero_unique` (`numero`),
  KEY `contratos_contratada_id_foreign` (`contratada_id`),
  KEY `contratos_fiscal_tecnico_id_foreign` (`fiscal_tecnico_id`),
  KEY `contratos_fiscal_administrativo_id_foreign` (`fiscal_administrativo_id`),
  KEY `contratos_gestor_id_foreign` (`gestor_id`),
  KEY `contratos_created_by_foreign` (`created_by`),
  KEY `contratos_updated_by_foreign` (`updated_by`),
  KEY `contratos_numero_index` (`numero`),
  KEY `contratos_situacao_index` (`situacao`),
  KEY `contratos_tipo_index` (`tipo`),
  CONSTRAINT `contratos_contratada_id_foreign` FOREIGN KEY (`contratada_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `contratos_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `contratos_fiscal_administrativo_id_foreign` FOREIGN KEY (`fiscal_administrativo_id`) REFERENCES `pessoas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `contratos_fiscal_tecnico_id_foreign` FOREIGN KEY (`fiscal_tecnico_id`) REFERENCES `pessoas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `contratos_gestor_id_foreign` FOREIGN KEY (`gestor_id`) REFERENCES `pessoas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `contratos_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contratos`
--

LOCK TABLES `contratos` WRITE;
/*!40000 ALTER TABLE `contratos` DISABLE KEYS */;
/*!40000 ALTER TABLE `contratos` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-27 23:48:53
