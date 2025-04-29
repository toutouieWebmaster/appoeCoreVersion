Info: Using unique option prefix 'no-tablespace' is error-prone and can break in the future. Please use the full name 'no-tablespaces' instead.
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: appoe
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
-- Table structure for table `appoe_categories`
--

DROP TABLE IF EXISTS `appoe_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appoe_categories` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(150) NOT NULL,
  `name` varchar(250) NOT NULL,
  `parentId` int(11) unsigned NOT NULL,
  `position` int(11) NOT NULL DEFAULT 999,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `type` (`type`,`name`,`parentId`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appoe_categories`
--

LOCK TABLES `appoe_categories` WRITE;
/*!40000 ALTER TABLE `appoe_categories` DISABLE KEYS */;
INSERT INTO `appoe_categories` VALUES (11,'CMS','Cuisine',10,999,1,'2025-04-23 08:13:37');
/*!40000 ALTER TABLE `appoe_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appoe_categoryrelations`
--

DROP TABLE IF EXISTS `appoe_categoryrelations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appoe_categoryrelations` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(150) NOT NULL,
  `typeId` int(11) unsigned NOT NULL,
  `categoryId` int(11) unsigned NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `type` (`type`,`typeId`,`categoryId`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appoe_categoryrelations`
--

LOCK TABLES `appoe_categoryrelations` WRITE;
/*!40000 ALTER TABLE `appoe_categoryrelations` DISABLE KEYS */;
/*!40000 ALTER TABLE `appoe_categoryrelations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appoe_files`
--

DROP TABLE IF EXISTS `appoe_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appoe_files` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(11) unsigned NOT NULL,
  `type` varchar(55) NOT NULL,
  `typeId` int(11) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `position` int(11) NOT NULL DEFAULT 999,
  `options` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `type` (`type`,`typeId`,`name`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appoe_files`
--

LOCK TABLES `appoe_files` WRITE;
/*!40000 ALTER TABLE `appoe_files` DISABLE KEYS */;
/*!40000 ALTER TABLE `appoe_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appoe_filescontent`
--

DROP TABLE IF EXISTS `appoe_filescontent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appoe_filescontent` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fileId` int(11) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `lang` varchar(10) NOT NULL,
  `userId` int(11) unsigned NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `fileId` (`fileId`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appoe_filescontent`
--

LOCK TABLES `appoe_filescontent` WRITE;
/*!40000 ALTER TABLE `appoe_filescontent` DISABLE KEYS */;
/*!40000 ALTER TABLE `appoe_filescontent` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appoe_maillogger`
--

DROP TABLE IF EXISTS `appoe_maillogger`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appoe_maillogger` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `object` varchar(70) NOT NULL,
  `toEmail` varchar(70) NOT NULL,
  `toName` varchar(70) NOT NULL,
  `fromEmail` varchar(70) NOT NULL,
  `fromName` varchar(70) NOT NULL,
  `message` text DEFAULT NULL,
  `ip` varchar(50) DEFAULT NULL,
  `source` varchar(255) DEFAULT NULL,
  `sent` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `date` (`date`,`object`,`fromEmail`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appoe_maillogger`
--

LOCK TABLES `appoe_maillogger` WRITE;
/*!40000 ALTER TABLE `appoe_maillogger` DISABLE KEYS */;
/*!40000 ALTER TABLE `appoe_maillogger` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appoe_menu`
--

DROP TABLE IF EXISTS `appoe_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appoe_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slug` varchar(40) NOT NULL,
  `name` varchar(50) NOT NULL,
  `min_role_id` int(11) NOT NULL,
  `statut` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `order_menu` int(11) DEFAULT NULL,
  `pluginName` varchar(200) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`,`parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=801 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appoe_menu`
--

LOCK TABLES `appoe_menu` WRITE;
/*!40000 ALTER TABLE `appoe_menu` DISABLE KEYS */;
INSERT INTO `appoe_menu` VALUES (11,'index','Tableau de bord',1,1,10,1,NULL,'2025-04-22 12:00:53'),(12,'users','Utilisateurs',1,1,10,99999,NULL,'2025-04-22 12:00:53'),(13,'setting','Réglages',11,0,10,13,NULL,'2025-04-22 12:00:53'),(14,'updateCategories','Catégories',11,1,10,2,NULL,'2025-04-22 12:00:53'),(15,'updateMedia','Média',1,1,10,3,NULL,'2025-04-22 12:00:53'),(16,'updatePermissions','Permissions',11,0,10,16,NULL,'2025-04-22 12:00:53'),(20,'allUsers','Tous les utilisateurs',1,1,12,20,NULL,'2025-04-22 12:00:53'),(21,'addUser','Nouvel utilisateur',2,1,12,21,NULL,'2025-04-22 12:00:53'),(22,'updateUser','Mise à jour de l\'utilisateur',1,0,12,22,NULL,'2025-04-22 12:00:53'),(23,'tools','Outils',3,0,10,23,NULL,'2025-04-22 12:00:53'),(70,'people','Personnes',4,1,10,70,'people','2025-04-23 08:35:44'),(71,'allPeople','Toutes les personnes',4,1,70,71,'people','2025-04-23 08:35:44'),(72,'addPerson','Nouvelle personne',4,1,70,72,'people','2025-04-23 08:35:44'),(73,'updatePerson','Fiche de la personne',4,0,70,73,'people','2025-04-23 08:35:44'),(74,'peopleArchives','Archives',4,1,70,74,'people','2025-04-23 08:35:44'),(80,'interactiveMap','Carte Interactive',1,1,10,80,'interactiveMap','2025-04-23 08:35:34'),(81,'allInterMaps','Toutes les cartes',1,1,80,81,'interactiveMap','2025-04-23 08:35:34'),(82,'addInterMap','Ajouter une carte',1,1,80,82,'interactiveMap','2025-04-23 08:35:34'),(83,'updateInterMap','Modifier la carte',1,0,80,83,'interactiveMap','2025-04-23 08:35:34'),(84,'updateInterMapContent','Éditer la carte',1,0,80,84,'interactiveMap','2025-04-23 08:35:34'),(100,'allRating','Évaluations',1,1,10,100,'rating','2025-04-23 08:35:44'),(110,'events','Évènements',1,1,10,110,'eventManagement','2025-04-23 08:33:09'),(111,'allEvents','Tous les Évènements',1,1,110,111,'eventManagement','2025-04-23 08:33:09'),(112,'event','Évènement',1,0,110,112,'eventManagement','2025-04-23 08:33:09'),(113,'addEvent','Nouvel évènement',4,1,110,113,'eventManagement','2025-04-23 08:33:09'),(114,'updateEvent','Mise à jour de l\'évènement',4,0,110,114,'eventManagement','2025-04-23 08:33:09'),(115,'allAuteurs','Tous les auteurs',1,1,110,115,'eventManagement','2025-04-23 08:33:09'),(116,'addAuteur','Nouvel Auteur',1,1,110,116,'eventManagement','2025-04-23 08:33:09'),(117,'updateAuteur','Mise à jour de l\'auteur',1,0,110,117,'eventManagement','2025-04-23 08:33:09'),(200,'cms','Pages',1,1,10,3,'cms','2025-04-23 08:32:20'),(201,'allPages','Toutes les pages',1,1,200,201,'cms','2025-04-23 08:32:20'),(202,'addPage','Nouvelle page',4,1,200,202,'cms','2025-04-23 08:32:20'),(204,'updatePageContent','Contenu de la page',1,0,200,204,'cms','2025-04-23 08:32:20'),(205,'updateMenu','Tous les menus',4,1,200,205,'cms','2025-04-23 08:32:20'),(206,'archives','Archives',1,1,200,206,'cms','2025-04-23 08:32:20'),(300,'agendas','Agendas',3,1,10,300,'appointment','2025-04-23 08:32:33'),(301,'updateAgendaManager','Agenda Manager',3,0,300,301,'appointment','2025-04-23 08:32:33'),(500,'messages','Messag\'In',1,0,10,500,'messagIn','2025-04-23 08:35:44'),(501,'allMessages','Tous les messages',1,0,500,501,'messagIn','2025-04-23 08:35:44'),(502,'addMessage','Nouveau message',1,0,500,502,'messagIn','2025-04-23 08:35:44'),(530,'updateTraduction','Traduction',4,1,10,530,'traduction','2025-04-23 08:35:48'),(600,'itemGlue','Articles',1,1,10,4,'itemGlue','2025-04-23 08:34:13'),(601,'allArticles','Tous les articles',1,1,600,601,'itemGlue','2025-04-23 08:34:13'),(602,'addArticle','Nouvel article',1,1,600,602,'itemGlue','2025-04-23 08:34:13'),(603,'updateArticleContent','Contenu de l\'article',1,0,600,603,'itemGlue','2025-04-23 08:34:13'),(604,'articlesArchives','Archives',1,1,600,604,'itemGlue','2025-04-23 08:34:13'),(700,'shop','Boutique',1,1,10,700,'shop','2025-04-23 08:35:44'),(701,'commandes','Commandes',1,1,700,701,'shop','2025-04-23 08:35:44'),(702,'products','Produits',1,1,700,702,'shop','2025-04-23 08:35:44'),(703,'addProduct','Nouveau produit',1,1,700,703,'shop','2025-04-23 08:35:44'),(704,'stock','Stock',1,1,700,704,'shop','2025-04-23 08:35:44'),(705,'addStock','Nouveau Stock',1,1,700,705,'shop','2025-04-23 08:35:44'),(706,'updateProduct','Mise à jour du produit',1,0,700,706,'shop','2025-04-23 08:35:44'),(707,'updateStock','Mise à jour du Stock',1,0,700,707,'shop','2025-04-23 08:35:44'),(708,'updateProductData','Détails du produit',1,0,700,708,'shop','2025-04-23 08:35:44'),(709,'shopArchives','Archives',1,1,700,709,'shop','2025-04-23 08:35:44'),(800,'updateCards','Les cartes',2,1,10,800,'glueCard','2025-04-23 08:35:44');
/*!40000 ALTER TABLE `appoe_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appoe_options`
--

DROP TABLE IF EXISTS `appoe_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appoe_options` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `key` varchar(255) NOT NULL,
  `val` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `type` (`type`,`key`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appoe_options`
--

LOCK TABLES `appoe_options` WRITE;
/*!40000 ALTER TABLE `appoe_options` DISABLE KEYS */;
INSERT INTO `appoe_options` VALUES (1,'PREFERENCE','Mode maintenance','maintenance','false','2025-04-22 12:00:53'),(2,'PREFERENCE','Forcer le site en HTTPS','forceHTTPS','false','2025-04-22 12:00:53'),(3,'PREFERENCE','Autoriser la mise en cache des fichiers','cacheProcess','false','2025-04-22 12:00:53'),(4,'PREFERENCE','Autoriser le travail sur la même page','sharingWork','false','2025-04-22 12:00:53'),(5,'PREFERENCE','Autoriser l\'API','allowApi','false','2025-04-22 12:00:53'),(6,'DATA','Clé API','apiToken','','2025-04-22 12:00:53'),(7,'DATA','Adresse Email par défaut','defaultEmail','','2025-04-22 12:00:53'),(8,'THEME','','--colorPrimary','#3eb293','2025-04-22 12:00:53'),(9,'THEME','','--colorPrimaryOpacity','rgba(62, 178, 147, 0.7)','2025-04-22 12:00:53'),(10,'THEME','','--textBgColorPrimary','#FFF','2025-04-22 12:00:53'),(11,'THEME','','--colorSecondary','#FF9373','2025-04-22 12:00:53'),(12,'THEME','','--colorSecondaryOpacity','rgba(255, 147, 117, 0.7)','2025-04-22 12:00:53'),(13,'THEME','','--textBgColorSecondary','#FFF','2025-04-22 12:00:53'),(14,'THEME','','--colorTertiary','#3eb293','2025-04-22 12:00:53'),(15,'THEME','','--colorTertiaryOpacity','rgba(62, 178, 147, 0.7)','2025-04-22 12:00:53'),(16,'THEME','','--textBgColorTertiary','#FFF','2025-04-22 12:00:53');
/*!40000 ALTER TABLE `appoe_options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appoe_plugin_appointment_agendas`
--

DROP TABLE IF EXISTS `appoe_plugin_appointment_agendas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appoe_plugin_appointment_agendas` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appoe_plugin_appointment_agendas`
--

LOCK TABLES `appoe_plugin_appointment_agendas` WRITE;
/*!40000 ALTER TABLE `appoe_plugin_appointment_agendas` DISABLE KEYS */;
/*!40000 ALTER TABLE `appoe_plugin_appointment_agendas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appoe_plugin_appointment_agendasmetas`
--

DROP TABLE IF EXISTS `appoe_plugin_appointment_agendasmetas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appoe_plugin_appointment_agendasmetas` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `idAgenda` tinyint(3) unsigned NOT NULL,
  `metaKey` varchar(255) NOT NULL,
  `metaValue` text NOT NULL,
  `position` tinyint(3) unsigned NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `idAgenda` (`idAgenda`,`metaKey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appoe_plugin_appointment_agendasmetas`
--

LOCK TABLES `appoe_plugin_appointment_agendasmetas` WRITE;
/*!40000 ALTER TABLE `appoe_plugin_appointment_agendasmetas` DISABLE KEYS */;
/*!40000 ALTER TABLE `appoe_plugin_appointment_agendasmetas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appoe_plugin_appointment_availabilities`
--

DROP TABLE IF EXISTS `appoe_plugin_appointment_availabilities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appoe_plugin_appointment_availabilities` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `idAgenda` tinyint(3) unsigned NOT NULL,
  `day` tinyint(3) unsigned NOT NULL,
  `start` smallint(5) unsigned NOT NULL,
  `end` smallint(5) unsigned NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `idAgenda` (`idAgenda`,`day`,`start`,`end`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appoe_plugin_appointment_availabilities`
--

LOCK TABLES `appoe_plugin_appointment_availabilities` WRITE;
/*!40000 ALTER TABLE `appoe_plugin_appointment_availabilities` DISABLE KEYS */;
/*!40000 ALTER TABLE `appoe_plugin_appointment_availabilities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appoe_plugin_appointment_clients`
--

DROP TABLE IF EXISTS `appoe_plugin_appointment_clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appoe_plugin_appointment_clients` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `lastName` varchar(100) NOT NULL,
  `firstName` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `tel` varchar(30) NOT NULL,
  `options` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appoe_plugin_appointment_clients`
--

LOCK TABLES `appoe_plugin_appointment_clients` WRITE;
/*!40000 ALTER TABLE `appoe_plugin_appointment_clients` DISABLE KEYS */;
/*!40000 ALTER TABLE `appoe_plugin_appointment_clients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appoe_plugin_appointment_exceptions`
--

DROP TABLE IF EXISTS `appoe_plugin_appointment_exceptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appoe_plugin_appointment_exceptions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `idAgenda` tinyint(3) unsigned NOT NULL,
  `date` date NOT NULL,
  `endDate` date DEFAULT NULL,
  `start` smallint(5) unsigned NOT NULL,
  `end` smallint(5) unsigned NOT NULL,
  `availability` varchar(50) NOT NULL DEFAULT 'UNAVAILABLE',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `idAgenda` (`idAgenda`,`date`,`start`,`end`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appoe_plugin_appointment_exceptions`
--

LOCK TABLES `appoe_plugin_appointment_exceptions` WRITE;
/*!40000 ALTER TABLE `appoe_plugin_appointment_exceptions` DISABLE KEYS */;
/*!40000 ALTER TABLE `appoe_plugin_appointment_exceptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appoe_plugin_appointment_rdv`
--

DROP TABLE IF EXISTS `appoe_plugin_appointment_rdv`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appoe_plugin_appointment_rdv` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `idAgenda` tinyint(3) unsigned NOT NULL,
  `idClient` int(11) unsigned NOT NULL,
  `idTypeRdv` tinyint(3) unsigned NOT NULL,
  `date` date NOT NULL,
  `start` smallint(5) unsigned NOT NULL,
  `end` smallint(5) unsigned NOT NULL,
  `options` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `createdAt` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `idAgenda` (`idAgenda`,`idClient`,`idTypeRdv`,`date`,`start`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appoe_plugin_appointment_rdv`
--

LOCK TABLES `appoe_plugin_appointment_rdv` WRITE;
/*!40000 ALTER TABLE `appoe_plugin_appointment_rdv` DISABLE KEYS */;
/*!40000 ALTER TABLE `appoe_plugin_appointment_rdv` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appoe_plugin_appointment_rdvtypes`
--

DROP TABLE IF EXISTS `appoe_plugin_appointment_rdvtypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appoe_plugin_appointment_rdvtypes` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `idAgenda` tinyint(3) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `duration` smallint(5) unsigned NOT NULL,
  `information` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `idAgenda` (`idAgenda`,`name`,`duration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appoe_plugin_appointment_rdvtypes`
--

LOCK TABLES `appoe_plugin_appointment_rdvtypes` WRITE;
/*!40000 ALTER TABLE `appoe_plugin_appointment_rdvtypes` DISABLE KEYS */;
/*!40000 ALTER TABLE `appoe_plugin_appointment_rdvtypes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appoe_plugin_appointment_rdvtypesform`
--

DROP TABLE IF EXISTS `appoe_plugin_appointment_rdvtypesform`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appoe_plugin_appointment_rdvtypesform` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `idAgenda` tinyint(3) unsigned NOT NULL,
  `idRdvType` tinyint(3) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `type` varchar(100) NOT NULL,
  `placeholder` varchar(250) DEFAULT NULL,
  `required` tinyint(1) NOT NULL DEFAULT 1,
  `position` tinyint(3) unsigned NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `idAgenda` (`idAgenda`,`idRdvType`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appoe_plugin_appointment_rdvtypesform`
--

LOCK TABLES `appoe_plugin_appointment_rdvtypesform` WRITE;
/*!40000 ALTER TABLE `appoe_plugin_appointment_rdvtypesform` DISABLE KEYS */;
/*!40000 ALTER TABLE `appoe_plugin_appointment_rdvtypesform` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appoe_plugin_cms`
--

DROP TABLE IF EXISTS `appoe_plugin_cms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appoe_plugin_cms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(100) NOT NULL DEFAULT 'PAGE',
  `filename` varchar(255) NOT NULL,
  `statut` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` date NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `type` (`type`,`filename`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appoe_plugin_cms`
--

LOCK TABLES `appoe_plugin_cms` WRITE;
/*!40000 ALTER TABLE `appoe_plugin_cms` DISABLE KEYS */;
INSERT INTO `appoe_plugin_cms` VALUES (11,'PAGE','index',1,'2025-04-23','2025-04-23 08:32:20'),(12,'PAGE','mentions-legales',1,'2025-04-23','2025-04-23 08:32:20'),(13,'PAGE','contact',1,'2025-04-23','2025-04-23 08:32:20');
/*!40000 ALTER TABLE `appoe_plugin_cms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appoe_plugin_cms_content`
--

DROP TABLE IF EXISTS `appoe_plugin_cms_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appoe_plugin_cms_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idCms` int(11) NOT NULL,
  `type` varchar(25) NOT NULL DEFAULT 'BODY',
  `metaKey` varchar(255) NOT NULL,
  `metaValue` text NOT NULL,
  `lang` varchar(10) NOT NULL DEFAULT 'fr',
  `created_at` date NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `idCms` (`idCms`,`type`,`metaKey`,`lang`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appoe_plugin_cms_content`
--

LOCK TABLES `appoe_plugin_cms_content` WRITE;
/*!40000 ALTER TABLE `appoe_plugin_cms_content` DISABLE KEYS */;
INSERT INTO `appoe_plugin_cms_content` VALUES (1,11,'HEADER','name','Home','fr','2025-04-23','2025-04-23 08:32:20'),(2,11,'HEADER','description','Home','fr','2025-04-23','2025-04-23 08:32:20'),(3,11,'HEADER','slug','home','fr','2025-04-23','2025-04-23 08:32:20'),(4,11,'HEADER','menuName','Home','fr','2025-04-23','2025-04-23 08:32:20'),(5,11,'HEADER','name','Home','en','2025-04-23','2025-04-23 08:32:20'),(6,11,'HEADER','description','Home','en','2025-04-23','2025-04-23 08:32:20'),(7,11,'HEADER','slug','home','en','2025-04-23','2025-04-23 08:32:20'),(8,11,'HEADER','menuName','Home','en','2025-04-23','2025-04-23 08:32:20'),(9,12,'HEADER','name','Mentions Légales','fr','2025-04-23','2025-04-23 08:32:20'),(10,12,'HEADER','description','Mentions Légales','fr','2025-04-23','2025-04-23 08:32:20'),(11,12,'HEADER','slug','mentions-legales','fr','2025-04-23','2025-04-23 08:32:20'),(12,12,'HEADER','menuName','Mentions Légales','fr','2025-04-23','2025-04-23 08:32:20'),(13,12,'HEADER','name','Mentions Légales','en','2025-04-23','2025-04-23 08:32:20'),(14,12,'HEADER','description','Mentions Légales','en','2025-04-23','2025-04-23 08:32:20'),(15,12,'HEADER','slug','mentions-legales','en','2025-04-23','2025-04-23 08:32:20'),(16,12,'HEADER','menuName','Mentions Légales','en','2025-04-23','2025-04-23 08:32:20'),(17,13,'HEADER','name','Contact','fr','2025-04-23','2025-04-23 08:32:20'),(18,13,'HEADER','description','Contact','fr','2025-04-23','2025-04-23 08:32:20'),(19,13,'HEADER','slug','contact','fr','2025-04-23','2025-04-23 08:32:20'),(20,13,'HEADER','menuName','Contact','fr','2025-04-23','2025-04-23 08:32:20'),(21,13,'HEADER','name','Contact','en','2025-04-23','2025-04-23 08:32:20'),(22,13,'HEADER','description','Contact','en','2025-04-23','2025-04-23 08:32:20'),(23,13,'HEADER','slug','contact','en','2025-04-23','2025-04-23 08:32:20'),(24,13,'HEADER','menuName','Contact','en','2025-04-23','2025-04-23 08:32:20');
/*!40000 ALTER TABLE `appoe_plugin_cms_content` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appoe_plugin_cms_menu`
--

DROP TABLE IF EXISTS `appoe_plugin_cms_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appoe_plugin_cms_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idCms` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `parentId` int(11) NOT NULL,
  `position` int(11) DEFAULT NULL,
  `location` int(11) NOT NULL DEFAULT 1,
  `statut` tinyint(1) NOT NULL DEFAULT 1,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `idCms` (`idCms`,`name`,`parentId`,`location`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appoe_plugin_cms_menu`
--

LOCK TABLES `appoe_plugin_cms_menu` WRITE;
/*!40000 ALTER TABLE `appoe_plugin_cms_menu` DISABLE KEYS */;
INSERT INTO `appoe_plugin_cms_menu` VALUES (11,'11',NULL,10,1,1,1,'2025-04-23 08:32:20');
/*!40000 ALTER TABLE `appoe_plugin_cms_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appoe_plugin_eventmanagement`
--

DROP TABLE IF EXISTS `appoe_plugin_eventmanagement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appoe_plugin_eventmanagement` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `auteurId` int(11) unsigned NOT NULL,
  `titre` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `pitch` varchar(255) DEFAULT NULL,
  `participation` text DEFAULT NULL,
  `duree` varchar(4) DEFAULT NULL,
  `spectacleType` smallint(1) unsigned NOT NULL DEFAULT 1,
  `indoor` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `image` varchar(255) DEFAULT NULL,
  `statut` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` date NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appoe_plugin_eventmanagement`
--

LOCK TABLES `appoe_plugin_eventmanagement` WRITE;
/*!40000 ALTER TABLE `appoe_plugin_eventmanagement` DISABLE KEYS */;
/*!40000 ALTER TABLE `appoe_plugin_eventmanagement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appoe_plugin_eventmanagement_dates`
--

DROP TABLE IF EXISTS `appoe_plugin_eventmanagement_dates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appoe_plugin_eventmanagement_dates` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `eventId` int(11) unsigned NOT NULL,
  `dateDebut` datetime NOT NULL,
  `dateFin` datetime NOT NULL,
  `localisation` varchar(5) DEFAULT NULL,
  `created_at` date NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appoe_plugin_eventmanagement_dates`
--

LOCK TABLES `appoe_plugin_eventmanagement_dates` WRITE;
/*!40000 ALTER TABLE `appoe_plugin_eventmanagement_dates` DISABLE KEYS */;
/*!40000 ALTER TABLE `appoe_plugin_eventmanagement_dates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appoe_plugin_gluecard_contents`
--

DROP TABLE IF EXISTS `appoe_plugin_gluecard_contents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appoe_plugin_gluecard_contents` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_handle` int(11) unsigned NOT NULL,
  `id_plan` int(11) unsigned NOT NULL,
  `id_item` int(11) unsigned NOT NULL,
  `text` text DEFAULT NULL,
  `lang` varchar(50) NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_handle` (`id_handle`,`id_plan`,`id_item`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appoe_plugin_gluecard_contents`
--

LOCK TABLES `appoe_plugin_gluecard_contents` WRITE;
/*!40000 ALTER TABLE `appoe_plugin_gluecard_contents` DISABLE KEYS */;
/*!40000 ALTER TABLE `appoe_plugin_gluecard_contents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appoe_plugin_gluecard_handles`
--

DROP TABLE IF EXISTS `appoe_plugin_gluecard_handles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appoe_plugin_gluecard_handles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` date NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appoe_plugin_gluecard_handles`
--

LOCK TABLES `appoe_plugin_gluecard_handles` WRITE;
/*!40000 ALTER TABLE `appoe_plugin_gluecard_handles` DISABLE KEYS */;
/*!40000 ALTER TABLE `appoe_plugin_gluecard_handles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appoe_plugin_gluecard_items`
--

DROP TABLE IF EXISTS `appoe_plugin_gluecard_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appoe_plugin_gluecard_items` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_handle` int(11) unsigned NOT NULL,
  `order` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` date NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appoe_plugin_gluecard_items`
--

LOCK TABLES `appoe_plugin_gluecard_items` WRITE;
/*!40000 ALTER TABLE `appoe_plugin_gluecard_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `appoe_plugin_gluecard_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appoe_plugin_gluecard_plans`
--

DROP TABLE IF EXISTS `appoe_plugin_gluecard_plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appoe_plugin_gluecard_plans` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_handle` int(11) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `order` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_handle` (`id_handle`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appoe_plugin_gluecard_plans`
--

LOCK TABLES `appoe_plugin_gluecard_plans` WRITE;
/*!40000 ALTER TABLE `appoe_plugin_gluecard_plans` DISABLE KEYS */;
/*!40000 ALTER TABLE `appoe_plugin_gluecard_plans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appoe_plugin_interactivemap`
--

DROP TABLE IF EXISTS `appoe_plugin_interactivemap`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appoe_plugin_interactivemap` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL,
  `data` mediumtext NOT NULL,
  `options` text DEFAULT NULL,
  `width` smallint(6) NOT NULL DEFAULT 0,
  `height` smallint(6) NOT NULL DEFAULT 0,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appoe_plugin_interactivemap`
--

LOCK TABLES `appoe_plugin_interactivemap` WRITE;
/*!40000 ALTER TABLE `appoe_plugin_interactivemap` DISABLE KEYS */;
/*!40000 ALTER TABLE `appoe_plugin_interactivemap` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appoe_plugin_itemglue_articles`
--

DROP TABLE IF EXISTS `appoe_plugin_itemglue_articles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appoe_plugin_itemglue_articles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `statut` tinyint(1) NOT NULL DEFAULT 1,
  `userId` int(11) NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appoe_plugin_itemglue_articles`
--

LOCK TABLES `appoe_plugin_itemglue_articles` WRITE;
/*!40000 ALTER TABLE `appoe_plugin_itemglue_articles` DISABLE KEYS */;
/*!40000 ALTER TABLE `appoe_plugin_itemglue_articles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appoe_plugin_itemglue_articles_content`
--

DROP TABLE IF EXISTS `appoe_plugin_itemglue_articles_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appoe_plugin_itemglue_articles_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idArticle` int(11) NOT NULL,
  `type` varchar(25) NOT NULL DEFAULT 'BODY',
  `content` text NOT NULL,
  `lang` varchar(10) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `idArticle` (`idArticle`,`type`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appoe_plugin_itemglue_articles_content`
--

LOCK TABLES `appoe_plugin_itemglue_articles_content` WRITE;
/*!40000 ALTER TABLE `appoe_plugin_itemglue_articles_content` DISABLE KEYS */;
/*!40000 ALTER TABLE `appoe_plugin_itemglue_articles_content` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appoe_plugin_itemglue_articles_meta`
--

DROP TABLE IF EXISTS `appoe_plugin_itemglue_articles_meta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appoe_plugin_itemglue_articles_meta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idArticle` int(11) NOT NULL,
  `metaKey` varchar(150) NOT NULL,
  `metaValue` text NOT NULL,
  `lang` varchar(10) NOT NULL DEFAULT 'fr',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `idArticle` (`idArticle`,`metaKey`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appoe_plugin_itemglue_articles_meta`
--

LOCK TABLES `appoe_plugin_itemglue_articles_meta` WRITE;
/*!40000 ALTER TABLE `appoe_plugin_itemglue_articles_meta` DISABLE KEYS */;
/*!40000 ALTER TABLE `appoe_plugin_itemglue_articles_meta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appoe_plugin_itemglue_articles_relations`
--

DROP TABLE IF EXISTS `appoe_plugin_itemglue_articles_relations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appoe_plugin_itemglue_articles_relations` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(250) NOT NULL,
  `typeId` int(11) unsigned NOT NULL,
  `articleId` int(11) unsigned NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `type` (`type`,`typeId`,`articleId`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appoe_plugin_itemglue_articles_relations`
--

LOCK TABLES `appoe_plugin_itemglue_articles_relations` WRITE;
/*!40000 ALTER TABLE `appoe_plugin_itemglue_articles_relations` DISABLE KEYS */;
/*!40000 ALTER TABLE `appoe_plugin_itemglue_articles_relations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appoe_plugin_messagin`
--

DROP TABLE IF EXISTS `appoe_plugin_messagin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appoe_plugin_messagin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fromUser` int(11) unsigned NOT NULL,
  `toUser` int(11) unsigned NOT NULL,
  `text` text NOT NULL,
  `statut` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` date NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appoe_plugin_messagin`
--

LOCK TABLES `appoe_plugin_messagin` WRITE;
/*!40000 ALTER TABLE `appoe_plugin_messagin` DISABLE KEYS */;
/*!40000 ALTER TABLE `appoe_plugin_messagin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appoe_plugin_people`
--

DROP TABLE IF EXISTS `appoe_plugin_people`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appoe_plugin_people` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(150) NOT NULL,
  `nature` varchar(150) DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `firstName` varchar(150) DEFAULT NULL,
  `entitled` varchar(350) DEFAULT NULL,
  `birthDate` date DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `tel` varchar(15) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `zip` varchar(7) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `idUser` int(11) DEFAULT NULL,
  `options` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `createdAt` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `type` (`type`,`name`,`firstName`,`email`,`address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appoe_plugin_people`
--

LOCK TABLES `appoe_plugin_people` WRITE;
/*!40000 ALTER TABLE `appoe_plugin_people` DISABLE KEYS */;
/*!40000 ALTER TABLE `appoe_plugin_people` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appoe_plugin_rating`
--

DROP TABLE IF EXISTS `appoe_plugin_rating`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appoe_plugin_rating` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(150) NOT NULL,
  `typeId` int(11) unsigned NOT NULL,
  `user` varchar(255) NOT NULL,
  `score` tinyint(2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `type` (`type`,`typeId`,`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appoe_plugin_rating`
--

LOCK TABLES `appoe_plugin_rating` WRITE;
/*!40000 ALTER TABLE `appoe_plugin_rating` DISABLE KEYS */;
/*!40000 ALTER TABLE `appoe_plugin_rating` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appoe_plugin_shop_commandes`
--

DROP TABLE IF EXISTS `appoe_plugin_shop_commandes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appoe_plugin_shop_commandes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `total_transport` decimal(6,2) DEFAULT NULL,
  `deliveryState` smallint(1) NOT NULL DEFAULT 2,
  `orderState` smallint(1) NOT NULL DEFAULT 1,
  `preBilling` varchar(2) NOT NULL,
  `billing` int(11) unsigned NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appoe_plugin_shop_commandes`
--

LOCK TABLES `appoe_plugin_shop_commandes` WRITE;
/*!40000 ALTER TABLE `appoe_plugin_shop_commandes` DISABLE KEYS */;
/*!40000 ALTER TABLE `appoe_plugin_shop_commandes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appoe_plugin_shop_commandes_details`
--

DROP TABLE IF EXISTS `appoe_plugin_shop_commandes_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appoe_plugin_shop_commandes_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `commandeId` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `poids` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `commandeId` (`commandeId`,`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appoe_plugin_shop_commandes_details`
--

LOCK TABLES `appoe_plugin_shop_commandes_details` WRITE;
/*!40000 ALTER TABLE `appoe_plugin_shop_commandes_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `appoe_plugin_shop_commandes_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appoe_plugin_shop_products`
--

DROP TABLE IF EXISTS `appoe_plugin_shop_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appoe_plugin_shop_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(150) NOT NULL,
  `name` varchar(150) NOT NULL,
  `slug` varchar(100) DEFAULT NULL,
  `price` decimal(7,2) NOT NULL,
  `poids` int(11) DEFAULT NULL,
  `dimension` int(11) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `type` (`type`,`name`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appoe_plugin_shop_products`
--

LOCK TABLES `appoe_plugin_shop_products` WRITE;
/*!40000 ALTER TABLE `appoe_plugin_shop_products` DISABLE KEYS */;
/*!40000 ALTER TABLE `appoe_plugin_shop_products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appoe_plugin_shop_products_content`
--

DROP TABLE IF EXISTS `appoe_plugin_shop_products_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appoe_plugin_shop_products_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `resume` varchar(255) DEFAULT NULL,
  `content` text NOT NULL,
  `lang` varchar(10) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_id` (`product_id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appoe_plugin_shop_products_content`
--

LOCK TABLES `appoe_plugin_shop_products_content` WRITE;
/*!40000 ALTER TABLE `appoe_plugin_shop_products_content` DISABLE KEYS */;
/*!40000 ALTER TABLE `appoe_plugin_shop_products_content` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appoe_plugin_shop_products_meta`
--

DROP TABLE IF EXISTS `appoe_plugin_shop_products_meta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appoe_plugin_shop_products_meta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `meta_key` varchar(150) NOT NULL,
  `meta_value` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_id` (`product_id`,`meta_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appoe_plugin_shop_products_meta`
--

LOCK TABLES `appoe_plugin_shop_products_meta` WRITE;
/*!40000 ALTER TABLE `appoe_plugin_shop_products_meta` DISABLE KEYS */;
/*!40000 ALTER TABLE `appoe_plugin_shop_products_meta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appoe_plugin_shop_stock`
--

DROP TABLE IF EXISTS `appoe_plugin_shop_stock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appoe_plugin_shop_stock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `limit_quantity` int(11) DEFAULT NULL,
  `date_limit` date DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appoe_plugin_shop_stock`
--

LOCK TABLES `appoe_plugin_shop_stock` WRITE;
/*!40000 ALTER TABLE `appoe_plugin_shop_stock` DISABLE KEYS */;
/*!40000 ALTER TABLE `appoe_plugin_shop_stock` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appoe_plugin_tracker`
--

DROP TABLE IF EXISTS `appoe_plugin_tracker`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appoe_plugin_tracker` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `ip` varchar(100) NOT NULL,
  `pageId` int(11) unsigned NOT NULL,
  `pageType` varchar(50) NOT NULL,
  `pageName` varchar(100) NOT NULL,
  `pageSlug` varchar(100) NOT NULL,
  `referer` varchar(255) DEFAULT NULL,
  `device` varchar(50) DEFAULT NULL,
  `browserName` varchar(100) DEFAULT NULL,
  `browserVersion` varchar(50) DEFAULT NULL,
  `osName` varchar(50) DEFAULT NULL,
  `osVersion` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appoe_plugin_tracker`
--

LOCK TABLES `appoe_plugin_tracker` WRITE;
/*!40000 ALTER TABLE `appoe_plugin_tracker` DISABLE KEYS */;
/*!40000 ALTER TABLE `appoe_plugin_tracker` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appoe_plugin_traduction`
--

DROP TABLE IF EXISTS `appoe_plugin_traduction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appoe_plugin_traduction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `metaKey` varchar(250) NOT NULL,
  `metaValue` text NOT NULL,
  `lang` varchar(10) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `metaKey` (`metaKey`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appoe_plugin_traduction`
--

LOCK TABLES `appoe_plugin_traduction` WRITE;
/*!40000 ALTER TABLE `appoe_plugin_traduction` DISABLE KEYS */;
/*!40000 ALTER TABLE `appoe_plugin_traduction` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appoe_users`
--

DROP TABLE IF EXISTS `appoe_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appoe_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(70) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `email` varchar(200) DEFAULT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `options` text DEFAULT NULL,
  `statut` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` date DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`)
) ENGINE=InnoDB AUTO_INCREMENT=15793 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appoe_users`
--

LOCK TABLES `appoe_users` WRITE;
/*!40000 ALTER TABLE `appoe_users` DISABLE KEYS */;
INSERT INTO `appoe_users` VALUES (15792,'Esther','$2y$10$qHRcwzxx/97nahQGCXmEIeeJ3sZDV0r1z/BfnKEjq6ptBiCjDQTuu','gNRkp','esther@pp-communication.fr','D','Esther',NULL,1,'2025-04-22','2025-04-22 12:00:53');
/*!40000 ALTER TABLE `appoe_users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-04-23 12:06:53
