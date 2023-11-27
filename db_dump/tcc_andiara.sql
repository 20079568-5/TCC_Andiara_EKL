-- MySQL dump 10.13  Distrib 8.0.35, for Linux (x86_64)
--
-- Host: localhost    Database: tcc_andiara
-- ------------------------------------------------------
-- Server version	8.0.35-0ubuntu0.23.10.1

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
-- Table structure for table `contrato`
--

DROP TABLE IF EXISTS `contrato`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contrato` (
  `id` int NOT NULL AUTO_INCREMENT,
  `numero` varchar(50) DEFAULT NULL,
  `status` varchar(45) NOT NULL DEFAULT 'Ativo',
  `dt_inicio` date NOT NULL,
  `dt_termino` date DEFAULT NULL,
  `dt_encerramento` date DEFAULT NULL,
  `dt_aditamento` date DEFAULT NULL,
  `orcamento_inicial` double(10,2) NOT NULL DEFAULT '0.00',
  `saldo_orcamento` double(10,2) NOT NULL DEFAULT '0.00',
  `nome_empresa` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contrato`
--

LOCK TABLES `contrato` WRITE;
/*!40000 ALTER TABLE `contrato` DISABLE KEYS */;
INSERT INTO `contrato` VALUES (11,'0001','Inativo','2023-01-01','2024-01-01','2024-04-01','2024-05-01',500000.00,300000.00,'Andiara Empresária');
/*!40000 ALTER TABLE `contrato` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recurso`
--

DROP TABLE IF EXISTS `recurso`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recurso` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `quantidade` int DEFAULT NULL,
  `validade_meses` int NOT NULL DEFAULT '1',
  `status` varchar(45) NOT NULL DEFAULT 'Ativo',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recurso`
--

LOCK TABLES `recurso` WRITE;
/*!40000 ALTER TABLE `recurso` DISABLE KEYS */;
INSERT INTO `recurso` VALUES (3,'Ferramentas de Inspeção','Proprio',8,10,'Ativo'),(4,'Máquina de Corte','Proprio',5,200,'Ativo'),(5,'Escavadeira de Esteira','Alugado',2,4,'Ativo');
/*!40000 ALTER TABLE `recurso` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recursos_contrato`
--

DROP TABLE IF EXISTS `recursos_contrato`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recursos_contrato` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_contrato` int NOT NULL,
  `id_recurso` int NOT NULL,
  `validade_meses` int DEFAULT NULL,
  `status` varchar(45) DEFAULT 'Ativo',
  PRIMARY KEY (`id`),
  KEY `fk_recursos_contrato_id_contrato_idx` (`id_contrato`),
  KEY `fk_recursos_contrato_id_recurso_idx` (`id_recurso`),
  CONSTRAINT `fk_recursos_contrato_id_contrato` FOREIGN KEY (`id_contrato`) REFERENCES `contrato` (`id`),
  CONSTRAINT `fk_recursos_contrato_id_recurso` FOREIGN KEY (`id_recurso`) REFERENCES `recurso` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recursos_contrato`
--

LOCK TABLES `recursos_contrato` WRITE;
/*!40000 ALTER TABLE `recursos_contrato` DISABLE KEYS */;
INSERT INTO `recursos_contrato` VALUES (6,11,4,10,'Ativo');
/*!40000 ALTER TABLE `recursos_contrato` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tarefas`
--

DROP TABLE IF EXISTS `tarefas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tarefas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `validade_meses` int DEFAULT NULL,
  `opcional_obrigatoria` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'null',
  `status` varchar(45) DEFAULT 'Ativo',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tarefas`
--

LOCK TABLES `tarefas` WRITE;
/*!40000 ALTER TABLE `tarefas` DISABLE KEYS */;
INSERT INTO `tarefas` VALUES (3,'Cadastrar Documentos',6,'Obrigatória','Ativo'),(4,'Analisar Riscos',4,'Opcional','Ativo'),(5,'Auditar Resultados',5,'Opcional','Ativo');
/*!40000 ALTER TABLE `tarefas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tarefas_contrato`
--

DROP TABLE IF EXISTS `tarefas_contrato`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tarefas_contrato` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_contrato` int NOT NULL,
  `id_tarefa` int NOT NULL,
  `validade_meses` int DEFAULT NULL,
  `status` varchar(45) DEFAULT 'Ativo',
  PRIMARY KEY (`id`),
  KEY `fk_tarefas_contrato_id_tarefa_idx` (`id_tarefa`),
  KEY `fk_tarefas_contrato_id_contrato_idx` (`id_contrato`),
  CONSTRAINT `fk_tarefas_contrato_id_contrato` FOREIGN KEY (`id_contrato`) REFERENCES `contrato` (`id`),
  CONSTRAINT `fk_tarefas_contrato_id_tarefa` FOREIGN KEY (`id_tarefa`) REFERENCES `tarefas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tarefas_contrato`
--

LOCK TABLES `tarefas_contrato` WRITE;
/*!40000 ALTER TABLE `tarefas_contrato` DISABLE KEYS */;
INSERT INTO `tarefas_contrato` VALUES (6,11,3,NULL,'Ativo'),(7,11,5,NULL,'Ativo'),(8,11,4,10,'Ativo');
/*!40000 ALTER TABLE `tarefas_contrato` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `upload`
--

DROP TABLE IF EXISTS `upload`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `upload` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_tarefa_contrato` int NOT NULL,
  `arquivo` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `nome` varchar(20) DEFAULT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_upload_id_tarefa_contrato_idx` (`id_tarefa_contrato`),
  CONSTRAINT `fk_upload_id_tarefa_contrato` FOREIGN KEY (`id_tarefa_contrato`) REFERENCES `tarefas_contrato` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `upload`
--

LOCK TABLES `upload` WRITE;
/*!40000 ALTER TABLE `upload` DISABLE KEYS */;
/*!40000 ALTER TABLE `upload` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `login` varchar(200) NOT NULL COMMENT 'Utilizado para fazer login',
  `email` varchar(150) NOT NULL,
  `password` varchar(250) NOT NULL,
  `create_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `api_token` varchar(200) DEFAULT NULL COMMENT 'Token para autenticação via API',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_UN_login` (`login`),
  UNIQUE KEY `user_UN_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb3 COMMENT='Cadastro de usuários';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES ('teste','email@exemplo.com','fc94908aacc4ee5f5628b0fda5cb487f','2023-02-15 13:24:35',1,9,'Usuário Teste','69c056d40ca6f4e6d21d4c6ff9d3b7ae');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'tcc_andiara'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-11-21 21:49:41
