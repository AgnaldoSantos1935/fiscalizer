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
-- Table structure for table `empresas`
--

DROP TABLE IF EXISTS `empresas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empresas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `razao_social` varchar(200) NOT NULL,
  `nome_fantasia` varchar(200) DEFAULT NULL,
  `cnpj` varchar(18) NOT NULL,
  `inscricao_estadual` varchar(30) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `uf` varchar(2) DEFAULT NULL,
  `cep` varchar(10) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `empresas_cnpj_unique` (`cnpj`),
  KEY `empresas_created_by_foreign` (`created_by`),
  KEY `empresas_updated_by_foreign` (`updated_by`),
  KEY `empresas_razao_social_index` (`razao_social`),
  KEY `empresas_cnpj_index` (`cnpj`),
  CONSTRAINT `empresas_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `empresas_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empresas`
--

LOCK TABLES `empresas` WRITE;
/*!40000 ALTER TABLE `empresas` DISABLE KEYS */;
INSERT INTO `empresas` VALUES (1,'Montreal Informática Ltda','Montreal TI','12.345.678/0001-90','123456789','contato@montrealti.com.br','(91) 3245-7788','Av. Nazaré, 1234','Belém','PA','66035-170','2025-10-26 23:41:04','2025-10-26 23:41:04',NULL,1,1),(2,'PRODEPA - Companhia de Processamento de Dados do Pará','PRODEPA','04.567.890/0001-12','904567890','suporte@prodepa.pa.gov.br','(91) 3201-8200','Rod. Augusto Montenegro, Km 10','Belém','PA','66635-110','2025-10-26 23:41:04','2025-10-26 23:41:04',NULL,1,1),(3,'Global Tecnologia S.A.','GlobalTech','98.765.432/0001-56','456789123','contato@globaltech.com.br','(91) 3222-5566','Rua dos Mundurucus, 450','Belém','PA','66025-210','2025-10-26 23:41:04','2025-10-26 23:41:04',NULL,1,1),(4,'SoftPlus Soluções Ltda','SoftPlus','11.111.111/0001-11','111222333','vendas@softplus.com.br','(91) 3344-7788','Av. Almirante Barroso, 1555','Belém','PA','66093-020','2025-10-26 23:41:04','2025-10-26 23:41:04',NULL,1,1),(5,'TechAmazon Serviços Digitais EIRELI','TechAmazon','22.222.222/0001-22','444555666','suporte@techamazon.com','(91) 3265-8800','Trav. Humaitá, 890','Ananindeua','PA','67020-120','2025-10-26 23:41:04','2025-10-26 23:41:04',NULL,1,1),(6,'DataControl Consultoria Ltda','DataControl','33.333.333/0001-33','777888999','atendimento@datacontrol.com','(91) 3256-8899','Rua Boaventura da Silva, 820','Belém','PA','66055-090','2025-10-26 23:41:04','2025-10-26 23:41:04',NULL,1,1),(7,'InfoPará Tecnologia e Serviços','InfoPará','44.444.444/0001-44','111999333','contato@infopara.com','(91) 3281-6600','Rua Santo Antônio, 300','Belém','PA','66020-200','2025-10-26 23:41:04','2025-10-26 23:41:04',NULL,1,1),(8,'Norte Digital Ltda','NorteDigital','55.555.555/0001-55','888444555','suporte@nortedigital.com','(91) 3333-7722','Av. Independência, 980','Marabá','PA','68500-200','2025-10-26 23:41:04','2025-10-26 23:41:04',NULL,1,1),(9,'TechBel Soluções Integradas','TechBel','66.666.666/0001-66','444111222','contato@techbel.com','(91) 3221-9900','Av. Gentil Bittencourt, 456','Belém','PA','66035-150','2025-10-26 23:41:04','2025-10-26 23:41:04',NULL,1,1),(10,'Amazônia Sistemas Inteligentes Ltda','AmazôniaSI','77.777.777/0001-77','123999888','adm@amazoniasi.com','(91) 3345-6677','Av. Augusto Montenegro, 4567','Belém','PA','66640-050','2025-10-26 23:41:04','2025-10-26 23:41:04',NULL,1,1),(11,'ParáTech Desenvolvimento e Sistemas','ParáTech','12.111.111/0001-12','101010101','suporte@paratech.com','(91) 3322-1100','Av. Pedro Álvares Cabral, 501','Ananindeua','PA','67033-020','2025-10-26 23:41:04','2025-10-26 23:41:04',NULL,1,1),(12,'BelNet Soluções Digitais','BelNet','13.111.111/0001-13','101010102','contato@belnet.com.br','(91) 3333-2211','Tv. Curuzu, 234','Belém','PA','66060-100','2025-10-26 23:41:04','2025-10-26 23:41:04',NULL,1,1),(13,'Delta Sistemas de Informação','DeltaInfo','14.111.111/0001-14','101010103','info@deltainfo.com.br','(91) 3344-3322','Rua São Pedro, 122','Castanhal','PA','68740-000','2025-10-26 23:41:04','2025-10-26 23:41:04',NULL,1,1),(14,'AmazonData Consultoria','AmazonData','15.111.111/0001-15','101010104','contato@amazondataconsult.com','(91) 3355-4433','Av. Independência, 200','Marabá','PA','68500-001','2025-10-26 23:41:04','2025-10-26 23:41:04',NULL,1,1),(15,'ParaSoft Tecnologia','ParaSoft','16.111.111/0001-16','101010105','vendas@parasoft.com','(91) 3366-5544','Av. João Paulo II, 987','Belém','PA','66085-000','2025-10-26 23:41:04','2025-10-26 23:41:04',NULL,1,1),(16,'InfoMar Soluções','InfoMar','17.111.111/0001-17','101010106','info@infomar.com','(91) 3377-6655','Rua dos Pariquis, 333','Belém','PA','66033-330','2025-10-26 23:41:04','2025-10-26 23:41:04',NULL,1,1),(17,'TecnoCast Sistemas','TecnoCast','18.111.111/0001-18','101010107','atendimento@tecnocast.com','(91) 3388-7766','Av. Barão de Igarapé-Miri, 77','Castanhal','PA','68742-100','2025-10-26 23:41:04','2025-10-26 23:41:04',NULL,1,1),(18,'SoluPará Tecnologia','SoluPará','19.111.111/0001-19','101010108','contato@solupara.com.br','(91) 3399-8877','Trav. Mauriti, 505','Belém','PA','66077-050','2025-10-26 23:41:04','2025-10-26 23:41:04',NULL,1,1),(19,'GeoSoft Amazônia','GeoSoft','20.111.111/0001-20','101010109','suporte@geosoft.com.br','(91) 3400-9988','Av. Gov. José Malcher, 654','Belém','PA','66055-180','2025-10-26 23:41:04','2025-10-26 23:41:04',NULL,1,1),(20,'TechPará Engenharia Digital','TechPará','21.111.111/0001-21','101010110','engenharia@techpara.com.br','(91) 3411-1122','Av. Visconde de Souza Franco, 155','Belém','PA','66053-000','2025-10-26 23:41:04','2025-10-26 23:41:04',NULL,1,1),(21,'North Sistemas','NorthSys','22.111.111/0001-22','101010111','contato@northsys.com','(91) 3422-2233','Rua dos Caripunas, 707','Belém','PA','66033-333','2025-10-26 23:41:04','2025-10-26 23:41:04',NULL,1,1),(22,'Smart Pará Soluções Integradas','SmartPará','23.111.111/0001-23','101010112','suporte@smartpara.com.br','(91) 3433-3344','Av. Augusto Montenegro, 2345','Belém','PA','66645-010','2025-10-26 23:41:04','2025-10-26 23:41:04',NULL,1,1),(23,'WebNet Serviços Digitais','WebNet','24.111.111/0001-24','101010113','vendas@webnet.com','(91) 3444-4455','Tv. Barão do Triunfo, 430','Ananindeua','PA','67020-000','2025-10-26 23:41:04','2025-10-26 23:41:04',NULL,1,1),(24,'ParaTI Gestão e Sistemas','ParaTI','25.111.111/0001-25','101010114','atendimento@parati.com.br','(91) 3455-5566','Av. Independência, 890','Marabá','PA','68501-200','2025-10-26 23:41:04','2025-10-26 23:41:04',NULL,1,1),(25,'DataSmart Sistemas','DataSmart','26.111.111/0001-26','101010115','contato@datasmart.com.br','(91) 3466-6677','Rua São José, 123','Santarém','PA','68005-200','2025-10-26 23:41:04','2025-10-26 23:41:04',NULL,1,1),(26,'NetPower Soluções Digitais','NetPower','27.111.111/0001-27','101010116','info@netpower.com','(91) 3477-7788','Av. Magalhães Barata, 999','Belém','PA','66063-240','2025-10-26 23:41:04','2025-10-26 23:41:04',NULL,1,1),(27,'SmartCode Pará','SmartCode','28.111.111/0001-28','101010117','dev@smartcode.com','(91) 3488-8899','Rua Manoel Barata, 765','Castanhal','PA','68745-020','2025-10-26 23:41:04','2025-10-26 23:41:04',NULL,1,1),(28,'CodeAmazon Desenvolvimento','CodeAmazon','29.111.111/0001-29','101010118','suporte@codeamazon.com.br','(91) 3499-9900','Av. Independência, 1550','Belém','PA','66065-090','2025-10-26 23:41:04','2025-10-26 23:41:04',NULL,1,1),(29,'BelData Informática','BelData','30.111.111/0001-30','101010119','contato@beldata.com','(91) 3500-1010','Rua dos Tamoios, 999','Belém','PA','66065-500','2025-10-26 23:41:04','2025-10-26 23:41:04',NULL,1,1);
/*!40000 ALTER TABLE `empresas` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-29  1:37:48
