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
-- Temporary view structure for view `vw_medicoes_resumo`
--

DROP TABLE IF EXISTS `vw_medicoes_resumo`;
/*!50001 DROP VIEW IF EXISTS `vw_medicoes_resumo`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vw_medicoes_resumo` AS SELECT 
 1 AS `id_medicao`,
 1 AS `contrato_numero`,
 1 AS `mes_referencia`,
 1 AS `total_pf`,
 1 AS `valor_unitario_pf`,
 1 AS `valor_total`,
 1 AS `contratada`,
 1 AS `gestor`*/;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `vw_medicoes_resumo`
--

/*!50001 DROP VIEW IF EXISTS `vw_medicoes_resumo`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_medicoes_resumo` AS select `m`.`id_medicao` AS `id_medicao`,`c`.`numero` AS `contrato_numero`,`m`.`mes_referencia` AS `mes_referencia`,`m`.`total_pf` AS `total_pf`,`m`.`valor_unitario_pf` AS `valor_unitario_pf`,`m`.`valor_total` AS `valor_total`,`e`.`razao_social` AS `contratada`,`u`.`nome` AS `gestor` from (((`medicao` `m` join `contratos` `c` on(`c`.`id_contrato` = `m`.`contrato_id`)) left join `empresas` `e` on(`e`.`id_empresa` = `c`.`contratada_id`)) left join `usuarios` `u` on(`u`.`id_usuario` = `c`.`gestor_id`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-24 12:05:10
