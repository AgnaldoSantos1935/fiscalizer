-- MySQL dump 10.13  Distrib 8.0.36, for Win64 (x86_64)
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
-- Table structure for table `parametros_apf`
--

DROP TABLE IF EXISTS `parametros_apf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `parametros_apf` (
  `id_parametro` int(11) NOT NULL AUTO_INCREMENT,
  `tipo_funcao` enum('EE','SE','CE','ALI','AIE') NOT NULL,
  `complexidade` enum('baixa','media','alta') NOT NULL,
  `pontos` decimal(5,2) NOT NULL,
  PRIMARY KEY (`id_parametro`),
  UNIQUE KEY `uk_tipo_complex` (`tipo_funcao`,`complexidade`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parametros_apf`
--

LOCK TABLES `parametros_apf` WRITE;
/*!40000 ALTER TABLE `parametros_apf` DISABLE KEYS */;
INSERT INTO `parametros_apf` VALUES (1,'EE','baixa',3.00),(2,'EE','media',4.00),(3,'EE','alta',6.00),(4,'SE','baixa',4.00),(5,'SE','media',5.00),(6,'SE','alta',7.00),(7,'CE','baixa',3.00),(8,'CE','media',4.00),(9,'CE','alta',6.00),(10,'ALI','baixa',7.00),(11,'ALI','media',10.00),(12,'ALI','alta',15.00),(13,'AIE','baixa',5.00),(14,'AIE','media',7.00),(15,'AIE','alta',10.00);
/*!40000 ALTER TABLE `parametros_apf` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-24 12:05:09
