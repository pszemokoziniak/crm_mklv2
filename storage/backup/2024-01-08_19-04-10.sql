-- MySQL dump 10.13  Distrib 8.0.35, for Linux (x86_64)
--
-- Host: localhost    Database: crmMklDB
-- ------------------------------------------------------
-- Server version	8.0.35-0ubuntu0.22.04.1

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
-- Table structure for table `accounts`
--

DROP TABLE IF EXISTS `accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `accounts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accounts`
--

LOCK TABLES `accounts` WRITE;
/*!40000 ALTER TABLE `accounts` DISABLE KEYS */;
INSERT INTO `accounts` VALUES (1,'Acme Corporation','2024-01-02 10:12:03','2024-01-02 10:12:03');
/*!40000 ALTER TABLE `accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `activity_logs`
--

DROP TABLE IF EXISTS `activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_logs` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `link_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `link_action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `changes` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `activity_logs_client_id_foreign` (`client_id`),
  KEY `activity_logs_user_id_foreign` (`user_id`),
  CONSTRAINT `activity_logs_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`),
  CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_logs`
--

LOCK TABLES `activity_logs` WRITE;
/*!40000 ALTER TABLE `activity_logs` DISABLE KEYS */;
INSERT INTO `activity_logs` VALUES ('04776fff-edc8-4aaa-8bdf-d96c2857c213','Poprawiono klienta','2fe5a608-fdaf-4405-8947-0736806619e1','2fe5a608-fdaf-4405-8947-0736806619e1','clients','zmiany','f2e49a5c-aa87-4174-9493-c2125403d1ee',NULL,'2024-01-03 17:53:53','2024-01-03 17:53:53'),('1e75122a-304d-467f-8396-030aba1a4baf','Nowa oferta','87ae0f54-8c26-460d-bb7b-8f6cae317e8e','6c262aef-fe2a-4a63-93d5-4d7df4b927fe','oferta','zmiany','f2e49a5c-aa87-4174-9493-c2125403d1ee',NULL,'2024-01-02 14:20:28','2024-01-02 14:20:28'),('590165b3-7196-48dd-b15a-0bb4da51319b','Dodoano przyszły projekt','1d170dd9-6746-4da5-a0f7-33621e73c470','f369fd36-6271-4104-913f-c4253faa88fb','futureproject','zmiany','f2e49a5c-aa87-4174-9493-c2125403d1ee',NULL,'2024-01-03 17:59:11','2024-01-03 17:59:11'),('660ce472-c350-4ef8-bfa9-e0960ef084be','Poprawiono ofertę','dc181a9e-44e8-4289-a3b9-622425e8454c','6c262aef-fe2a-4a63-93d5-4d7df4b927fe','oferta','zmiany','f2e49a5c-aa87-4174-9493-c2125403d1ee',NULL,'2024-01-02 17:12:13','2024-01-02 17:12:13'),('7f0e0edc-2b52-4288-9af8-ff9d94d01ef6','Nowe zapytanie','7369162c-baf4-4e06-a284-603a77310416','6c262aef-fe2a-4a63-93d5-4d7df4b927fe','zapytania','zmiany','f2e49a5c-aa87-4174-9493-c2125403d1ee',NULL,'2024-01-02 10:56:16','2024-01-02 10:56:16'),('91d843f3-5573-4087-bdac-7f9f6a5dce51','Poprawiono zapytanie','7369162c-baf4-4e06-a284-603a77310416','6c262aef-fe2a-4a63-93d5-4d7df4b927fe','zapytania','zmiany','f2e49a5c-aa87-4174-9493-c2125403d1ee',NULL,'2024-01-02 13:24:16','2024-01-02 13:24:16'),('96efb745-3992-4523-9534-182db6f028f4','Nowa oferta','bf5de62e-22c1-4add-8156-76cdd0dc3136','6c262aef-fe2a-4a63-93d5-4d7df4b927fe','oferta','zmiany','f2e49a5c-aa87-4174-9493-c2125403d1ee',NULL,'2024-01-02 14:37:40','2024-01-02 14:37:40'),('97f43aeb-b9a7-4451-b7c4-cbcc5ae8e147','Dodano klienta','2fe5a608-fdaf-4405-8947-0736806619e1','2fe5a608-fdaf-4405-8947-0736806619e1','clients','zmiany','f2e49a5c-aa87-4174-9493-c2125403d1ee',NULL,'2024-01-03 17:39:33','2024-01-03 17:39:33'),('b67d3c7a-c51f-40db-97c9-92d5a2753e34','Nowa oferta','3c2aa488-0c09-4fa0-b0d9-41662988604d','6c262aef-fe2a-4a63-93d5-4d7df4b927fe','oferta','zmiany','f2e49a5c-aa87-4174-9493-c2125403d1ee',NULL,'2024-01-02 17:05:01','2024-01-02 17:05:01'),('eb9174a4-a848-4d99-a410-fc8bc04cab05','Nowa oferta','a4406e5a-64ec-47a2-bde5-b3ee43f4473c','6c262aef-fe2a-4a63-93d5-4d7df4b927fe','oferta','zmiany','f2e49a5c-aa87-4174-9493-c2125403d1ee',NULL,'2024-01-02 14:27:53','2024-01-02 14:27:53');
/*!40000 ALTER TABLE `activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `branzas`
--

DROP TABLE IF EXISTS `branzas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `branzas` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `branzas`
--

LOCK TABLES `branzas` WRITE;
/*!40000 ALTER TABLE `branzas` DISABLE KEYS */;
INSERT INTO `branzas` VALUES ('69d45caa-5b35-418f-9aa1-a472b26ea411','Branza2',NULL,NULL,NULL),('d58f9f46-b5ba-493c-a04f-eb4fcb9c0ff3','Branza1',NULL,NULL,NULL);
/*!40000 ALTER TABLE `branzas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clients`
--

DROP TABLE IF EXISTS `clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clients` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nazwa` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ulica` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `miasto` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `www` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `linkedIn` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `user_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kraj_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `branza_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clients`
--

LOCK TABLES `clients` WRITE;
/*!40000 ALTER TABLE `clients` DISABLE KEYS */;
INSERT INTO `clients` VALUES ('2fe5a608-fdaf-4405-8947-0736806619e1','Totam incididunt molqq','Quo pariatur Except','Ducimus in ea repre','Dolor dicta vero max','Temporibus adipisci','Veniam optio volup','f2e49a5c-aa87-4174-9493-c2125403d1ee','7af71111-fec9-4ca6-ab43-0b8899ac4fd6','69d45caa-5b35-418f-9aa1-a472b26ea411',NULL,'2024-01-03 17:39:33','2024-01-03 17:53:53'),('69d1d115-145b-4fbb-bbca-691d0307a5d4','Et neque labore quia','Laboriosam autem as','Voluptas laboris vol','Molestiae voluptatem','Similique dolorum et','Sit nihil modi volup','f2e49a5c-aa87-4174-9493-c2125403d1ee','7af71111-fec9-4ca6-ab43-0b8899ac4fd6','69d45caa-5b35-418f-9aa1-a472b26ea411',NULL,'2024-01-03 17:19:42','2024-01-03 17:19:42'),('6c262aef-fe2a-4a63-93d5-4d7df4b927fe','Asperiores eligendi','Cupidatat in placeat','Fugiat impedit fac','Tempore et suscipit','Nostrud veritatis mo','Sunt delectus assum','f2e49a5c-aa87-4174-9493-c2125403d1ee','b1cd262d-0730-406a-a54f-5f64d6699bc5','d58f9f46-b5ba-493c-a04f-eb4fcb9c0ff3',NULL,'2024-01-02 10:55:22','2024-01-02 10:55:22'),('7c6eaee3-6584-4489-8132-ea46a1afce74','Est tempora in ut n','Ea ipsa modi quaera','Ut voluptate autem i','Ut consequatur quas','Placeat natus vero','Perspiciatis enim r','f2e49a5c-aa87-4174-9493-c2125403d1ee','b1cd262d-0730-406a-a54f-5f64d6699bc5','69d45caa-5b35-418f-9aa1-a472b26ea411',NULL,'2024-01-03 17:38:16','2024-01-03 17:38:16'),('da5108e2-e030-4760-b61f-0ebd97febb95','Et neque labore quia','Laboriosam autem as','Voluptas laboris vol','Molestiae voluptatem','Similique dolorum et','Sit nihil modi volup','f2e49a5c-aa87-4174-9493-c2125403d1ee','7af71111-fec9-4ca6-ab43-0b8899ac4fd6','69d45caa-5b35-418f-9aa1-a472b26ea411',NULL,'2024-01-03 17:18:43','2024-01-03 17:18:43'),('f369fd36-6271-4104-913f-c4253faa88fb','Facere aute est cons','Laudantium dolores','Illo blanditiis ad e','Incidunt maxime ea','Aliquam non tempore','Ea qui id delectus','f2e49a5c-aa87-4174-9493-c2125403d1ee','7af71111-fec9-4ca6-ab43-0b8899ac4fd6','d58f9f46-b5ba-493c-a04f-eb4fcb9c0ff3',NULL,'2024-01-03 17:23:32','2024-01-03 17:23:32');
/*!40000 ALTER TABLE `clients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contacts`
--

DROP TABLE IF EXISTS `contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contacts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int NOT NULL,
  `organization_id` int DEFAULT NULL,
  `first_name` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `region` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contacts_account_id_index` (`account_id`),
  KEY `contacts_organization_id_index` (`organization_id`)
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contacts`
--

LOCK TABLES `contacts` WRITE;
/*!40000 ALTER TABLE `contacts` DISABLE KEYS */;
INSERT INTO `contacts` VALUES (1,1,38,'Isidro','Kilback','bulah.marvin@example.com','(844) 387-2313','57230 Linnea Junction','Goyetteland','Wyoming','US','73846','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(2,1,70,'Jocelyn','Wiza','zgaylord@example.com','877-488-7678','866 Harber Circle','East Noemie','Florida','US','60342-6493','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(3,1,74,'Osbaldo','Padberg','xgusikowski@example.net','1-888-673-2539','8805 Schowalter Coves','Port Garlandhaven','Colorado','US','32155-3467','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(4,1,100,'Theodore','Metz','tlemke@example.com','877.574.3444','54471 Huels Circles Suite 398','Bruenfort','Rhode Island','US','18903','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(5,1,23,'Cody','Lowe','mraz.tyrese@example.org','(800) 910-8035','506 Walter Shoal Suite 643','Lake Tonymouth','Massachusetts','US','66768','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(6,1,74,'Joany','Wolf','funk.myrtis@example.com','855-346-3724','79187 Afton Streets Apt. 612','Orvalburgh','Nevada','US','61783-3880','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(7,1,18,'Jordy','Wiza','dskiles@example.org','(866) 875-1790','657 Gleason Square Suite 935','New Aldenchester','Iowa','US','12440-5294','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(8,1,100,'Alisa','Will','shad.kreiger@example.com','(866) 212-5135','961 Delia Pass','Kemmerchester','Alabama','US','70997','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(9,1,55,'Angelita','Hoppe','cbalistreri@example.com','888.220.8123','918 Rempel Alley Apt. 405','Kossbury','Tennessee','US','54620','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(10,1,7,'Kyle','Durgan','blaise.wiegand@example.org','866-251-6963','3285 Bartoletti Harbor Apt. 701','West Carolebury','Florida','US','74916-0933','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(11,1,6,'Darien','Cruickshank','nona90@example.com','877-812-1884','80116 Danny Branch','VonRuedenhaven','Missouri','US','12797','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(12,1,84,'Rogelio','O\'Conner','qberge@example.org','877.926.9558','40507 Tracy Plaza Apt. 788','Vernerport','New Mexico','US','94629','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(13,1,64,'Elda','Bruen','rempel.eva@example.net','866-956-1894','93128 Angelina Loop','Faychester','North Dakota','US','30317-4227','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(14,1,71,'Cathy','Klein','uparisian@example.com','877.335.5991','54994 Felicita Freeway Apt. 846','New Flossie','Mississippi','US','77431-6094','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(15,1,33,'Natalia','Willms','akoepp@example.com','1-800-415-6172','24610 Christiansen Trafficway','Lake Denis','New York','US','95465','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(16,1,46,'Alexie','Weber','yhammes@example.com','844-721-2254','639 Schaefer Port','West Nikita','Kansas','US','72298','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(17,1,44,'General','Rau','gino.heaney@example.org','844-393-0740','39091 Meghan Lodge','Rippinside','Florida','US','05216','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(18,1,61,'Alec','Hoeger','ystroman@example.com','1-800-605-1067','8952 Anna Junctions Suite 942','Belleton','Indiana','US','09438','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(19,1,47,'Alyce','Okuneva','kory44@example.org','844-844-2229','439 Pedro Isle Apt. 327','New Fabiola','Hawaii','US','17340-0855','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(20,1,13,'Davion','Ortiz','sharon68@example.net','1-855-435-6656','93299 Hilpert Knolls Suite 861','Pacochastad','Iowa','US','63451-6028','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(21,1,88,'Kian','Mraz','bhamill@example.org','1-888-466-3431','704 Avis Key','Abbeyfort','New Jersey','US','12391','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(22,1,94,'Sadye','McGlynn','paula11@example.org','(855) 318-1427','12894 Harber Passage Suite 148','Gibsonbury','Alaska','US','84048-7036','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(23,1,88,'Jermaine','Wilkinson','qlakin@example.org','1-844-593-9788','81804 Klein Shoal Apt. 412','New Simone','North Dakota','US','43831','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(24,1,92,'Stuart','Windler','hartmann.clifton@example.org','1-877-612-1349','1833 Natalia Mountains','East Claude','Ohio','US','51081-8776','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(25,1,59,'Audreanne','Christiansen','bnitzsche@example.net','1-877-935-3666','8057 Christina Mills Apt. 739','Staceychester','New York','US','09846','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(26,1,65,'Clair','Hansen','iosinski@example.net','800-235-7794','87837 Lavonne Station','Jaleelmouth','New Hampshire','US','05885-1100','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(27,1,15,'Pearl','Mills','kuhlman.ignatius@example.com','844.597.0249','464 Lane Spring','New Maddisonfort','Oklahoma','US','63589','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(28,1,31,'Deshaun','Herzog','christy.gleason@example.com','1-877-615-1033','46102 Ratke Island Apt. 122','North Loma','District of Columbia','US','65620-8025','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(29,1,51,'Myron','Paucek','smith.mariam@example.org','844.277.8145','25324 Sigrid Dam','Gutmannberg','Massachusetts','US','43138-7083','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(30,1,65,'Yadira','Haley','jakubowski.brando@example.org','1-888-716-5602','558 Janie Streets Apt. 433','Anaiston','Alaska','US','89520-3343','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(31,1,31,'Bell','Spencer','angie.rempel@example.net','1-877-725-2456','4349 Treutel Motorway Suite 104','Abshireberg','District of Columbia','US','14702-2698','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(32,1,72,'Abbigail','Quitzon','eugene39@example.org','(866) 757-7853','9342 Teresa Camp','New Marjorieville','Illinois','US','41570-6358','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(33,1,94,'Samanta','Strosin','khowe@example.com','888-909-5456','85624 Lauretta Isle','West Ellenberg','Minnesota','US','60491-2848','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(34,1,5,'Gerhard','Glover','rhianna.brakus@example.net','877-977-6414','91544 Effertz Heights','Romaguerabury','Pennsylvania','US','95722','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(35,1,38,'Tyrese','Schulist','swaniawski.donna@example.org','844-816-1010','83620 McClure Crest','North Lavadabury','New Hampshire','US','29643-0629','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(36,1,96,'Korey','D\'Amore','kreiger.anabelle@example.com','855-563-1652','3899 Braun Alley','South Susannaport','Virginia','US','01107','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(37,1,3,'Margaretta','Heaney','adams.hannah@example.com','1-888-688-6089','8567 Jazlyn Knoll','Hartmannmouth','Connecticut','US','99139','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(38,1,86,'Faye','Fay','vallie36@example.org','(877) 475-3497','6066 Walsh Pine Apt. 427','East Emilyport','North Carolina','US','52772-3904','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(39,1,8,'Carolanne','Kemmer','molly.abernathy@example.com','877-207-2965','274 Mann Trafficway','West Cedrick','Colorado','US','05443-6955','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(40,1,50,'Francesco','Robel','maggio.isobel@example.net','866.891.9407','47927 Zieme Court Apt. 300','Rosinaview','Idaho','US','03547','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(41,1,93,'Percy','Hackett','fkihn@example.org','(866) 212-5363','26868 Doyle Cove','Pfannerstillland','Arizona','US','78229','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(42,1,20,'Catharine','Effertz','nchamplin@example.com','(844) 417-9992','167 Gibson Trail Apt. 806','West Kyleighborough','Texas','US','90604','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(43,1,35,'Evangeline','Ortiz','gleason.holden@example.com','1-888-848-2146','42071 Romaguera Dam','Swifthaven','North Carolina','US','58282','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(44,1,8,'Carolina','Legros','hand.retha@example.org','844.584.1376','65968 Kuhn Ramp','Gorczanytown','South Carolina','US','66538-2079','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(45,1,64,'Nola','Mayert','qgoldner@example.com','855-335-8090','5551 Jerde Parks Suite 521','Port Roxanne','Kentucky','US','28100','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(46,1,20,'Arnulfo','Schmeler','amelie.barrows@example.com','866-509-3258','44996 Alison Ways','Port Clarabelleburgh','Indiana','US','21030-6534','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(47,1,81,'Aaliyah','Sanford','thermann@example.net','800-472-8692','4048 Megane Branch Suite 495','New Garrisonland','Hawaii','US','18569-2067','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(48,1,57,'Ike','Gerlach','eanderson@example.com','877.982.9716','10219 Cara Estate','West Itzel','Florida','US','67078-0441','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(49,1,3,'Dominic','Kessler','rmorar@example.org','877-841-6388','95791 Celine Hills Suite 849','Stoltenbergchester','Georgia','US','97479','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(50,1,23,'Ottilie','Kemmer','gislason.cornell@example.net','1-855-415-0882','93251 Elza Field','Jerdefurt','Georgia','US','25295-7734','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(51,1,38,'Margret','Stiedemann','flangworth@example.org','1-888-322-9975','65031 Liliana Brooks','Medhurstport','Texas','US','58230','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(52,1,72,'Kameron','Heaney','auer.laury@example.net','800.456.8807','2824 Hartmann Road','Port Abdul','Colorado','US','21702','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(53,1,100,'Ilene','Will','percival62@example.com','844.753.5412','96249 Brekke Curve','Kreigerton','Minnesota','US','34089','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(54,1,15,'Uriah','Bode','ktreutel@example.com','1-877-884-6174','655 Collier Plaza Suite 770','Port Bailey','Mississippi','US','02974','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(55,1,6,'Lilian','Satterfield','zackery.terry@example.com','866-803-3222','2572 Krajcik Place','Lake Rogelioberg','Ohio','US','86308-7621','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(56,1,97,'Roosevelt','Gorczany','lucious.hettinger@example.net','(866) 897-9136','216 Garrison Port Suite 334','North Larry','South Dakota','US','68082-3320','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(57,1,38,'Linnie','Sporer','sauer.emiliano@example.org','1-855-659-5305','59036 Schulist Corners','Walkerberg','Oregon','US','57727','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(58,1,9,'Delphine','Ruecker','skonopelski@example.org','855.385.0017','35533 Annabelle Mills','South Rosetta','Maryland','US','38335','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(59,1,25,'Dorothea','Kulas','vhartmann@example.com','1-888-517-2547','81911 Runte Circles','Kilbackland','Texas','US','01984','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(60,1,72,'Harmony','McCullough','graham.ora@example.net','800.958.6347','35947 Jaylan Stream Suite 270','North Abigale','Hawaii','US','39529','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(61,1,96,'Jed','Lang','alexanne.flatley@example.com','(877) 493-3051','108 Ankunding Road Suite 817','Langworthhaven','West Virginia','US','07926-1456','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(62,1,65,'Scarlett','Baumbach','larson.christopher@example.org','1-888-667-4262','4096 Crooks Throughway Apt. 974','Windlerland','Oregon','US','98162-9414','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(63,1,46,'Reymundo','Casper','arielle.jerde@example.com','877.452.8479','335 Merritt Mall','Rohanport','West Virginia','US','42697','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(64,1,34,'Jamar','Skiles','floy83@example.net','(844) 547-6705','796 Oral Knolls Suite 429','Kesslerton','Maryland','US','42818','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(65,1,45,'Rodolfo','Bashirian','maurine46@example.com','877.576.0993','10163 Mack Curve Apt. 545','Lake Rose','Wyoming','US','50329','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(66,1,53,'Adeline','Swaniawski','hill.myrna@example.com','866-883-8735','516 Tania Camp Apt. 293','East Zachariahfurt','Connecticut','US','82352-0074','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(67,1,76,'Marianna','Wiza','wwintheiser@example.com','1-844-917-9985','80443 Tremblay Bypass','Cormierfurt','West Virginia','US','85046-2297','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(68,1,36,'Chelsie','Larkin','sjacobi@example.org','844-230-1057','10467 Cummerata Rapid Suite 126','West Amiyamouth','Wisconsin','US','59033-3702','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(69,1,42,'Armando','Schuster','chandler.wehner@example.net','855-447-0960','90651 Werner Turnpike','Ofeliaborough','Iowa','US','54652-9766','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(70,1,90,'Garfield','Koepp','maud19@example.net','800-389-1431','862 Corwin Fall','Nathanialfort','Maryland','US','18204','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(71,1,22,'Dejon','Feest','yvonne31@example.org','1-800-728-5331','8187 Olson Road Suite 700','Romagueraborough','Rhode Island','US','60136-0465','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(72,1,55,'Anais','Ortiz','kerluke.amie@example.org','844.337.1664','50469 Terry Run Suite 755','Lake Nelda','California','US','20809-3251','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(73,1,56,'Derrick','Satterfield','lilyan.gusikowski@example.org','(888) 315-9667','21909 Chasity Summit Suite 393','East Wadeton','Maryland','US','50707','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(74,1,78,'Alice','Doyle','vrutherford@example.com','1-888-672-0373','2855 Turcotte Heights Suite 932','Vadahaven','West Virginia','US','59876','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(75,1,22,'Dewitt','Wyman','stroman.nettie@example.com','877.529.2003','337 Jerde Drive Apt. 453','Heidenreichside','Hawaii','US','63357','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(76,1,44,'Burnice','Lakin','pollich.matteo@example.net','877-333-6878','2401 Bruen Landing Suite 794','North Connerhaven','South Carolina','US','20687','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(77,1,18,'Gay','Lind','mikel09@example.com','1-888-486-1011','180 Kassulke Harbor','Lednerhaven','Alabama','US','88104','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(78,1,82,'Catalina','Nader','powlowski.randall@example.net','(877) 664-3591','19706 Donnelly Lakes','Port Annabelbury','Arizona','US','65027','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(79,1,13,'Don','Hartmann','crona.blaze@example.org','(800) 213-7361','772 Damion Ports Suite 655','Charlieland','North Carolina','US','12217-3543','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(80,1,14,'Shanel','Bailey','wilmer.witting@example.com','800-671-9107','52166 Cummings Islands','North Bernadine','Minnesota','US','57182','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(81,1,59,'Shayne','Walsh','rprosacco@example.com','877.357.7074','126 Klocko Row','Hanetown','Vermont','US','63203','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(82,1,50,'Hunter','Prohaska','agustina32@example.com','855.330.7415','641 Alexis Canyon','East Wilberside','Idaho','US','32367','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(83,1,36,'Justen','McClure','zcronin@example.com','855-432-7096','3955 Morar Drive Apt. 087','Amelystad','South Dakota','US','27722','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(84,1,64,'Rebekah','Lockman','pat.price@example.net','1-888-224-2165','867 Rohan Junctions','Lake Katelynstad','Virginia','US','94144-0623','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(85,1,56,'Corene','Weissnat','julie.west@example.org','800.830.2726','914 Bruce Gardens','Berniemouth','Colorado','US','66072-5692','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(86,1,78,'Braden','Pfannerstill','kerluke.pinkie@example.net','855-913-9938','43950 Turcotte Neck','Hartmannton','Alabama','US','41245','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(87,1,83,'Devyn','Witting','kertzmann.arnulfo@example.net','1-866-866-6622','273 Greenfelder Landing','New Mortonchester','New Mexico','US','52547-3852','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(88,1,47,'Kirstin','Mertz','gust.legros@example.com','844.585.2008','616 Osinski Land Suite 355','New Felixfort','Pennsylvania','US','18271','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(89,1,49,'Tyler','Veum','rwilderman@example.com','844-559-2440','1748 Dickinson Point Suite 520','Shermanchester','Rhode Island','US','83909-5497','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(90,1,89,'Bethel','Kuhn','dario.balistreri@example.net','888.396.0792','1281 Chance Avenue Suite 656','Sibyltown','Massachusetts','US','23781-1867','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(91,1,8,'Rosalind','Hessel','charber@example.com','(855) 526-5784','1702 Mervin Valley','West Larueland','Arizona','US','32063-6826','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(92,1,35,'Roxanne','Von','janelle26@example.com','(855) 380-7501','8688 Bailey Brooks Suite 757','Kendallmouth','Arizona','US','14187-9034','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(93,1,9,'Ardith','Lehner','eloisa.sporer@example.net','800-535-3987','4857 Colin Extensions','Port Aglae','District of Columbia','US','66404-8587','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(94,1,100,'Anthony','Marks','janis40@example.org','800.483.2848','8467 Sidney Vista Apt. 600','Port Nora','Nevada','US','68132-8576','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(95,1,27,'Adalberto','Willms','karine30@example.net','855.389.0234','46169 Walker Court Apt. 535','Ahmedborough','South Carolina','US','69160-2929','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(96,1,44,'Susie','Zboncak','rowe.fleta@example.net','866-858-1306','12346 Frances Canyon','Port Alessia','Utah','US','62183-4700','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(97,1,72,'Deion','Weber','rau.robert@example.com','1-877-880-4847','331 Hartmann Stream','Daremouth','Arizona','US','62721-2657','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(98,1,64,'Lisa','Quitzon','anastasia39@example.org','(888) 723-5525','4117 Collier Trail','New Bayleeton','Nevada','US','37426','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(99,1,93,'Sunny','Weimann','edythe91@example.net','(855) 743-7740','3377 Douglas Square Apt. 138','Louiehaven','Alaska','US','22931','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(100,1,63,'Remington','Larson','mcruickshank@example.net','844.359.1813','40342 Berry Cove Apt. 920','Port Jerrellberg','New Hampshire','US','68491-8512','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL);
/*!40000 ALTER TABLE `contacts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
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
-- Table structure for table `fazas`
--

DROP TABLE IF EXISTS `fazas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fazas` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fazas`
--

LOCK TABLES `fazas` WRITE;
/*!40000 ALTER TABLE `fazas` DISABLE KEYS */;
INSERT INTO `fazas` VALUES ('74869126-8592-4528-a14a-fa26b65d30b9','Do id ea adipisicing',NULL,'2024-01-03 17:57:16','2024-01-03 17:57:16');
/*!40000 ALTER TABLE `fazas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `future_projects`
--

DROP TABLE IF EXISTS `future_projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `future_projects` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nazwa` text COLLATE utf8mb4_unicode_ci,
  `miasto` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kraj_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `objekt_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `opis` text COLLATE utf8mb4_unicode_ci,
  `start` date DEFAULT NULL,
  `end` date DEFAULT NULL,
  `faza_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `inwestor` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `dane_kontaktowe` text COLLATE utf8mb4_unicode_ci,
  `data_kontakt` date DEFAULT NULL,
  `user_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `arch` tinyint(1) DEFAULT NULL,
  `arch_time` date DEFAULT NULL,
  `arch_user` int DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `future_projects_kraj_id_foreign` (`kraj_id`),
  KEY `future_projects_objekt_id_foreign` (`objekt_id`),
  KEY `future_projects_client_id_foreign` (`client_id`),
  KEY `future_projects_faza_id_foreign` (`faza_id`),
  KEY `future_projects_user_id_foreign` (`user_id`),
  CONSTRAINT `future_projects_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`),
  CONSTRAINT `future_projects_faza_id_foreign` FOREIGN KEY (`faza_id`) REFERENCES `fazas` (`id`),
  CONSTRAINT `future_projects_kraj_id_foreign` FOREIGN KEY (`kraj_id`) REFERENCES `krajs` (`id`),
  CONSTRAINT `future_projects_objekt_id_foreign` FOREIGN KEY (`objekt_id`) REFERENCES `objekts` (`id`),
  CONSTRAINT `future_projects_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `future_projects`
--

LOCK TABLES `future_projects` WRITE;
/*!40000 ALTER TABLE `future_projects` DISABLE KEYS */;
INSERT INTO `future_projects` VALUES ('1d170dd9-6746-4da5-a0f7-33621e73c470','Mollit perferendis s','Voluptatem vel duis','7af71111-fec9-4ca6-ab43-0b8899ac4fd6','b2dc65aa-3c97-4c8e-bba8-7214bb266332','f369fd36-6271-4104-913f-c4253faa88fb','Elit dolore est ut','1977-05-05','1994-11-18','74869126-8592-4528-a14a-fa26b65d30b9','Qui facere commodo n','Aliquid fugiat sunt','1981-09-08','f2e49a5c-aa87-4174-9493-c2125403d1ee',NULL,NULL,NULL,NULL,'2024-01-03 17:59:11','2024-01-03 17:59:11'),('657feee0-e87b-410b-847c-f3175ef70700','Mollit perferendis s','Voluptatem vel duis','7af71111-fec9-4ca6-ab43-0b8899ac4fd6','b2dc65aa-3c97-4c8e-bba8-7214bb266332','f369fd36-6271-4104-913f-c4253faa88fb','Elit dolore est ut','1977-05-05','1994-11-18','74869126-8592-4528-a14a-fa26b65d30b9','Qui facere commodo n','Aliquid fugiat sunt','1981-09-08','f2e49a5c-aa87-4174-9493-c2125403d1ee',NULL,NULL,NULL,NULL,'2024-01-03 17:57:50','2024-01-03 17:57:50'),('8229344d-ab9e-4c0a-8327-00958fbcac7f','Mollit perferendis s','Voluptatem vel duis','7af71111-fec9-4ca6-ab43-0b8899ac4fd6','b2dc65aa-3c97-4c8e-bba8-7214bb266332','f369fd36-6271-4104-913f-c4253faa88fb','Elit dolore est ut','1977-05-05','1994-11-18','74869126-8592-4528-a14a-fa26b65d30b9','Qui facere commodo n','Aliquid fugiat sunt','1981-09-08','f2e49a5c-aa87-4174-9493-c2125403d1ee',NULL,NULL,NULL,NULL,'2024-01-03 17:58:27','2024-01-03 17:58:27');
/*!40000 ALTER TABLE `future_projects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `krajs`
--

DROP TABLE IF EXISTS `krajs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `krajs` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `waluta` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `krajs`
--

LOCK TABLES `krajs` WRITE;
/*!40000 ALTER TABLE `krajs` DISABLE KEYS */;
INSERT INTO `krajs` VALUES ('7af71111-fec9-4ca6-ab43-0b8899ac4fd6','Monako','EUR',NULL,NULL,NULL),('b1cd262d-0730-406a-a54f-5f64d6699bc5','Polska','PLN',NULL,NULL,NULL);
/*!40000 ALTER TABLE `krajs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kursies`
--

DROP TABLE IF EXISTS `kursies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kursies` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `kurs` double(10,4) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kursies`
--

LOCK TABLES `kursies` WRITE;
/*!40000 ALTER TABLE `kursies` DISABLE KEYS */;
INSERT INTO `kursies` VALUES ('2f14b5b7-04dd-4c0e-9df5-af8556799071','PLN',62.0000,NULL,'2024-01-02 10:55:47','2024-01-02 10:55:47'),('707ab328-f876-411c-9902-8a0a8df25cda','EUR',4.1200,NULL,'2024-01-02 10:55:56','2024-01-02 10:55:56');
/*!40000 ALTER TABLE `kursies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `linkedins`
--

DROP TABLE IF EXISTS `linkedins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `linkedins` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `click` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `linkedins`
--

LOCK TABLES `linkedins` WRITE;
/*!40000 ALTER TABLE `linkedins` DISABLE KEYS */;
/*!40000 ALTER TABLE `linkedins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2019_12_14_000001_create_personal_access_tokens_table',1),(2,'2020_01_01_000001_create_password_resets_table',1),(3,'2020_01_01_000002_create_failed_jobs_table',1),(4,'2020_01_01_000003_create_accounts_table',1),(5,'2020_01_01_000004_create_users_table',1),(6,'2020_01_01_000005_create_organizations_table',1),(7,'2020_01_01_000006_create_contacts_table',1),(8,'2023_10_07_211130_create_branzas_table',1),(9,'2023_10_10_221457_create_krajs_table',1),(10,'2023_10_11_200757_create_clients_table',1),(11,'2023_11_10_223827_create_zakres_table',1),(12,'2023_11_10_231041_create_zapytanias_table',1),(13,'2023_11_16_203909_create_oferta_statuses_table',1),(14,'2023_11_16_222103_create_ofertas_table',1),(15,'2023_11_19_171944_create_kursies_table',1),(16,'2023_12_20_211410_create_obiekts_table',1),(17,'2023_12_20_211421_create_fazas_table',1),(18,'2023_12_21_175842_create_linkedins_table',1),(19,'2023_12_21_205419_create_future_projects_table',1),(20,'2023_12_22_112859_create_strony_wwws_table',1),(21,'2024_01_02_101054_create_activity_logs_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `objekts`
--

DROP TABLE IF EXISTS `objekts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `objekts` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `objekts`
--

LOCK TABLES `objekts` WRITE;
/*!40000 ALTER TABLE `objekts` DISABLE KEYS */;
INSERT INTO `objekts` VALUES ('b2dc65aa-3c97-4c8e-bba8-7214bb266332','Sed accusamus volupt',NULL,'2024-01-03 17:57:41','2024-01-03 17:57:41');
/*!40000 ALTER TABLE `objekts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `oferta_statuses`
--

DROP TABLE IF EXISTS `oferta_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `oferta_statuses` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `oferta_statuses`
--

LOCK TABLES `oferta_statuses` WRITE;
/*!40000 ALTER TABLE `oferta_statuses` DISABLE KEYS */;
INSERT INTO `oferta_statuses` VALUES ('548a93ba-4b3e-48f1-8570-3074eca3a318','Status1',NULL,NULL,NULL),('c64fa0ac-fb14-4583-9620-a7996ff0a4e7','Status2',NULL,NULL,NULL);
/*!40000 ALTER TABLE `oferta_statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ofertas`
--

DROP TABLE IF EXISTS `ofertas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ofertas` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `typ` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `zapytania_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_wyslania` date DEFAULT NULL,
  `kwota` bigint DEFAULT NULL,
  `waluta` text COLLATE utf8mb4_unicode_ci,
  `kurs` text COLLATE utf8mb4_unicode_ci,
  `kwotaPLN` text COLLATE utf8mb4_unicode_ci,
  `data_kontakt` date DEFAULT NULL,
  `oferta_status_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `opis` text COLLATE utf8mb4_unicode_ci,
  `arch_user_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `arch_text` text COLLATE utf8mb4_unicode_ci,
  `arch_time` date DEFAULT NULL,
  `user_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ofertas_zapytania_id_foreign` (`zapytania_id`),
  KEY `ofertas_client_id_foreign` (`client_id`),
  KEY `ofertas_oferta_status_id_foreign` (`oferta_status_id`),
  KEY `ofertas_arch_user_id_foreign` (`arch_user_id`),
  KEY `ofertas_user_id_foreign` (`user_id`),
  CONSTRAINT `ofertas_arch_user_id_foreign` FOREIGN KEY (`arch_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `ofertas_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`),
  CONSTRAINT `ofertas_oferta_status_id_foreign` FOREIGN KEY (`oferta_status_id`) REFERENCES `oferta_statuses` (`id`),
  CONSTRAINT `ofertas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `ofertas_zapytania_id_foreign` FOREIGN KEY (`zapytania_id`) REFERENCES `zapytanias` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ofertas`
--

LOCK TABLES `ofertas` WRITE;
/*!40000 ALTER TABLE `ofertas` DISABLE KEYS */;
INSERT INTO `ofertas` VALUES ('1153a9ef-8543-466d-95af-65b6479582cf','Klienta ma kontakt','7369162c-baf4-4e06-a284-603a77310416','6c262aef-fe2a-4a63-93d5-4d7df4b927fe','1985-11-09',20,'PLN','62','1240','2024-11-02','548a93ba-4b3e-48f1-8570-3074eca3a318','Dolor id consequunt',NULL,NULL,NULL,'f2e49a5c-aa87-4174-9493-c2125403d1ee',NULL,'2024-01-02 14:25:37','2024-01-02 14:25:37'),('20d44cda-ed58-4449-a325-176b7605ef87','Klient oferuje','7369162c-baf4-4e06-a284-603a77310416','6c262aef-fe2a-4a63-93d5-4d7df4b927fe','2016-10-16',12,'PLN','62','744','1980-07-12','548a93ba-4b3e-48f1-8570-3074eca3a318','Suscipit in rerum co',NULL,NULL,NULL,'f2e49a5c-aa87-4174-9493-c2125403d1ee',NULL,'2024-01-02 14:21:49','2024-01-02 14:21:49'),('3c2aa488-0c09-4fa0-b0d9-41662988604d','Klienta ma kontakt','7369162c-baf4-4e06-a284-603a77310416','6c262aef-fe2a-4a63-93d5-4d7df4b927fe','1978-02-17',52,'PLN','62','3224','1970-05-02','c64fa0ac-fb14-4583-9620-a7996ff0a4e7','In ratione ut et cor',NULL,NULL,NULL,'f2e49a5c-aa87-4174-9493-c2125403d1ee',NULL,'2024-01-02 17:05:01','2024-01-02 17:05:01'),('3f4ce33e-3f9f-4555-8790-e6586531d68e','Klienta ma kontakt','7369162c-baf4-4e06-a284-603a77310416','6c262aef-fe2a-4a63-93d5-4d7df4b927fe','2006-12-13',9,'PLN','62','558','2013-02-04','c64fa0ac-fb14-4583-9620-a7996ff0a4e7','Ullamco quia quis al',NULL,NULL,NULL,'f2e49a5c-aa87-4174-9493-c2125403d1ee',NULL,'2024-01-02 14:21:28','2024-01-02 14:21:28'),('73a19115-4297-4378-8a12-292883ffaed2','Klienta ma kontakt','7369162c-baf4-4e06-a284-603a77310416','6c262aef-fe2a-4a63-93d5-4d7df4b927fe','2024-10-06',22,'EUR','4.12','90.64','2011-06-07','c64fa0ac-fb14-4583-9620-a7996ff0a4e7','Sed cupiditate corpo',NULL,NULL,NULL,'f2e49a5c-aa87-4174-9493-c2125403d1ee',NULL,'2024-01-02 14:27:03','2024-01-02 14:27:03'),('87ae0f54-8c26-460d-bb7b-8f6cae317e8e','Klient oferuje','7369162c-baf4-4e06-a284-603a77310416','6c262aef-fe2a-4a63-93d5-4d7df4b927fe','1980-05-26',65,'PLN','62','4030','2002-09-20','c64fa0ac-fb14-4583-9620-a7996ff0a4e7','In proident commodi',NULL,NULL,NULL,'f2e49a5c-aa87-4174-9493-c2125403d1ee',NULL,'2024-01-02 14:20:28','2024-01-02 14:20:28'),('9d5975e6-e6f3-4882-8479-5d1e618151d1','Klienta ma kontakt','7369162c-baf4-4e06-a284-603a77310416','6c262aef-fe2a-4a63-93d5-4d7df4b927fe','1985-11-09',20,'PLN','62','1240','2024-11-02','548a93ba-4b3e-48f1-8570-3074eca3a318','Dolor id consequunt',NULL,NULL,NULL,'f2e49a5c-aa87-4174-9493-c2125403d1ee',NULL,'2024-01-02 14:25:43','2024-01-02 14:25:43'),('a4406e5a-64ec-47a2-bde5-b3ee43f4473c','Klient oferuje','7369162c-baf4-4e06-a284-603a77310416','6c262aef-fe2a-4a63-93d5-4d7df4b927fe','2018-07-19',10,'PLN','62','620','1976-10-28','c64fa0ac-fb14-4583-9620-a7996ff0a4e7','Sequi nobis illo rer',NULL,NULL,NULL,'f2e49a5c-aa87-4174-9493-c2125403d1ee',NULL,'2024-01-02 14:27:53','2024-01-02 14:27:53'),('ab066c70-561c-4e9d-aeec-dc7e9e6ed899','Klienta ma kontakt','7369162c-baf4-4e06-a284-603a77310416','6c262aef-fe2a-4a63-93d5-4d7df4b927fe','1985-11-09',20,'PLN','62','1240','2024-11-02','548a93ba-4b3e-48f1-8570-3074eca3a318','Dolor id consequunt',NULL,NULL,NULL,'f2e49a5c-aa87-4174-9493-c2125403d1ee',NULL,'2024-01-02 14:22:15','2024-01-02 14:22:15'),('b2e2790f-22c1-4e7c-9401-1925ff25fd3d','Klienta ma kontakt','7369162c-baf4-4e06-a284-603a77310416','6c262aef-fe2a-4a63-93d5-4d7df4b927fe','1985-11-09',20,'PLN','62','1240','2024-11-02','548a93ba-4b3e-48f1-8570-3074eca3a318','Dolor id consequunt',NULL,NULL,NULL,'f2e49a5c-aa87-4174-9493-c2125403d1ee',NULL,'2024-01-02 14:26:26','2024-01-02 14:26:26'),('bf5de62e-22c1-4add-8156-76cdd0dc3136','Klient oferuje','7369162c-baf4-4e06-a284-603a77310416','6c262aef-fe2a-4a63-93d5-4d7df4b927fe','1975-02-21',30,'PLN','62','1860','2007-05-17','548a93ba-4b3e-48f1-8570-3074eca3a318','Sed aliquam alias as',NULL,NULL,NULL,'f2e49a5c-aa87-4174-9493-c2125403d1ee',NULL,'2024-01-02 14:37:40','2024-01-02 14:37:40'),('c1aeb8e8-c91c-4405-9bc5-04e56f959daa','Klienta ma kontakt','7369162c-baf4-4e06-a284-603a77310416','6c262aef-fe2a-4a63-93d5-4d7df4b927fe','1985-11-09',20,'PLN','62','1240','2024-11-02','548a93ba-4b3e-48f1-8570-3074eca3a318','Dolor id consequunt',NULL,NULL,NULL,'f2e49a5c-aa87-4174-9493-c2125403d1ee',NULL,'2024-01-02 14:26:00','2024-01-02 14:26:00'),('d3b9d9db-79de-43e8-b909-5e02485f3c4f','Klient oferuje','7369162c-baf4-4e06-a284-603a77310416','6c262aef-fe2a-4a63-93d5-4d7df4b927fe','1980-05-26',65,'PLN','62','4030','2002-09-20','c64fa0ac-fb14-4583-9620-a7996ff0a4e7','In proident commodi',NULL,NULL,NULL,'f2e49a5c-aa87-4174-9493-c2125403d1ee',NULL,'2024-01-02 13:30:29','2024-01-02 13:30:29'),('dc181a9e-44e8-4289-a3b9-622425e8454c','Klienta ma kontakt','7369162c-baf4-4e06-a284-603a77310416','6c262aef-fe2a-4a63-93d5-4d7df4b927fe','1988-12-03',22222222222255,'PLN','62','3410','2010-03-26','c64fa0ac-fb14-4583-9620-a7996ff0a4e7','Quae assumenda incid',NULL,NULL,NULL,'f2e49a5c-aa87-4174-9493-c2125403d1ee',NULL,'2024-01-02 13:29:50','2024-01-03 13:52:20');
/*!40000 ALTER TABLE `ofertas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `organizations`
--

DROP TABLE IF EXISTS `organizations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `organizations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `region` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `organizations_account_id_index` (`account_id`)
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `organizations`
--

LOCK TABLES `organizations` WRITE;
/*!40000 ALTER TABLE `organizations` DISABLE KEYS */;
INSERT INTO `organizations` VALUES (1,1,'Senger PLC','dooley.alden@osinski.com','844.492.9466','74178 Rolfson Canyon Apt. 970','West Alexys','New Hampshire','US','08706-2931','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(2,1,'Ruecker Ltd','lakin.rodrigo@bosco.com','1-855-672-3451','14321 Quitzon Skyway Apt. 892','New Alexandreamouth','Virginia','US','37619-5154','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(3,1,'Marquardt, Smith and Hoppe','julianne66@lang.net','(877) 815-2764','893 Dillon Creek Suite 418','Hodkiewiczland','Nebraska','US','62912-9766','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(4,1,'Weissnat-Reilly','jaida27@friesen.com','844-220-2170','5648 Bayer Isle Suite 556','Eastershire','Massachusetts','US','07561-2831','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(5,1,'Legros Inc','xspinka@hand.com','866.999.0482','810 Will Light Apt. 312','Lake Janychester','Wisconsin','US','31415','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(6,1,'Hackett Group','kristina83@wunsch.net','(866) 797-0289','3389 Destinee Knoll','New Isaac','Alaska','US','11620','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(7,1,'Harris Ltd','bartholome.lebsack@harris.info','855-722-5388','34067 Edna Dam Suite 285','New Malvinatown','New Jersey','US','16357-2098','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(8,1,'Davis, Feeney and Miller','ona89@yundt.com','844-326-1150','464 Godfrey Parkway','South Alessandramouth','Louisiana','US','76343-6559','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(9,1,'Boehm-Harris','richmond28@mclaughlin.com','855-717-2853','2841 Mueller Ways','West Ardella','Connecticut','US','73117-0518','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(10,1,'Adams, Huel and Kemmer','qmann@emard.net','877-420-8844','7610 Osinski Stravenue Apt. 658','Evieview','Hawaii','US','61987-1088','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(11,1,'Considine-Rempel','arianna.schumm@glover.net','1-877-803-6490','43045 Dashawn Cape Apt. 958','North Kristoffer','Kentucky','US','01385-6993','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(12,1,'Moen, Gulgowski and Cartwright','blair.wyman@ruecker.info','(888) 690-9105','703 Little Spur','Port Alysabury','Arkansas','US','64505-7316','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(13,1,'Padberg, Effertz and Thiel','gulgowski.miguel@kertzmann.com','1-877-888-0527','3722 Cleve Ports','West Thalia','Utah','US','18513-6117','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(14,1,'Ziemann Inc','jefferey.gleason@bogisich.com','(855) 218-6867','6284 Loyal Junction','Damarisport','Missouri','US','21726-3603','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(15,1,'Grimes, Bergstrom and Kunze','kip.jacobs@nikolaus.info','1-855-875-2261','65819 Thiel Springs','New Hillardburgh','Kentucky','US','09235','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(16,1,'Russel, Dickens and Lakin','tmorar@stoltenberg.com','(855) 816-7665','582 White Place','Goodwinfurt','New York','US','59259','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(17,1,'O\'Kon-Breitenberg','wwolff@doyle.com','855-709-5484','65905 Joelle Garden','North Mckenzie','Utah','US','09408','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(18,1,'Reichert-Altenwerth','herzog.general@emmerich.com','(877) 924-4636','403 Priscilla Parkways','Delfinaview','Wyoming','US','97818-7716','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(19,1,'Gottlieb-Lindgren','patrick.greenholt@stracke.info','1-877-298-9104','4511 Vandervort River','South Jimmiefort','Kentucky','US','05386-5224','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(20,1,'Moen-Lind','huel.estell@cormier.com','1-855-953-2909','227 Eliza Avenue','North Krystinaton','Missouri','US','11776-5992','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(21,1,'Heathcote, Koss and Howe','oreilly.frida@donnelly.com','1-877-382-1072','620 Titus Streets','Caterinastad','Nevada','US','31904-7394','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(22,1,'O\'Connell-Jacobson','zemlak.khalil@roberts.com','866-821-3117','596 Fritsch Harbor Suite 868','Port Carleystad','California','US','00464','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(23,1,'Muller Ltd','estelle.rau@bosco.biz','800.380.3328','8465 Gutmann Crossing','Lake Kellen','Wyoming','US','44508','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(24,1,'Ortiz-Steuber','efeeney@ruecker.com','(888) 513-5470','265 David Inlet Suite 072','Federicoland','Washington','US','80358','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(25,1,'Wolf PLC','ldicki@nikolaus.biz','855-590-1506','858 Satterfield Vista','New Stone','Indiana','US','11302-3232','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(26,1,'Ondricka, Dickinson and Swift','bwyman@frami.info','1-800-828-5373','32249 Cristobal Isle Suite 613','Lake Annamouth','South Carolina','US','69419-5075','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(27,1,'Cormier PLC','schoen.juanita@sawayn.info','(888) 922-5682','77500 Hailey Lakes Apt. 565','North Gregorioborough','Utah','US','42662-7252','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(28,1,'Hackett Group','felicia.gibson@koepp.com','888.797.4581','3833 Jaida Isle Suite 387','Constanceshire','Alabama','US','84626','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(29,1,'Pfannerstill, Hauck and Grant','jaylan.bogisich@erdman.net','1-866-208-2098','42387 Klocko Glen','Ondrickafurt','Montana','US','38570-5068','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(30,1,'Stark PLC','phermiston@mills.org','844.662.2435','47578 Lueilwitz Viaduct Apt. 843','East Tianaland','Kentucky','US','96502-8426','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(31,1,'Lesch, Spinka and Herman','gutmann.rosie@veum.net','844-799-5929','5450 Krajcik Points','Jessiemouth','New Mexico','US','21759','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(32,1,'D\'Amore LLC','mosciski.emile@windler.net','(855) 689-7547','5555 Elza Land','Port Sandra','Iowa','US','00457','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(33,1,'Hoppe, Hyatt and Abshire','bkrajcik@stiedemann.com','877-841-8212','543 Jarrett Parkways Suite 154','East Kennedy','New Jersey','US','94555','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(34,1,'Rippin-Volkman','jermey.koch@eichmann.net','888-803-4850','29815 Rossie Ferry','Heidenreichchester','South Carolina','US','92648-2222','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(35,1,'Hauck, Hodkiewicz and Von','xhomenick@windler.biz','855.360.6675','5794 Johann Overpass Apt. 361','North Jany','Louisiana','US','99853','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(36,1,'Schowalter Group','waelchi.timmothy@hermann.info','844-479-8812','87948 Blair Loop','Cobyside','Alabama','US','22052-8706','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(37,1,'Hermann, Terry and Treutel','claudie46@armstrong.com','866.975.3209','58998 Crist Gateway Suite 698','Corkeryville','Michigan','US','05581-5115','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(38,1,'Schaden Inc','mayert.alexys@bednar.org','877.452.7510','263 Gwen Bridge Suite 048','Crooksland','Louisiana','US','44998-3578','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(39,1,'Kemmer, King and Weber','schultz.douglas@zulauf.com','855.515.7556','5632 Dare Alley','Port Melyssa','Oregon','US','22342','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(40,1,'Ward, Rohan and Bruen','berenice.kiehn@aufderhar.com','1-877-830-6937','393 Isac Stravenue','Dominichaven','West Virginia','US','91677-5125','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(41,1,'Nitzsche LLC','lisette23@yost.com','800.941.7005','26097 Lucy Underpass Apt. 117','Port Connerville','Maine','US','95084','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(42,1,'Wilkinson-Larkin','nthompson@morissette.info','800.912.3182','9233 Pouros Extensions Suite 828','Port Adaline','New Hampshire','US','95490-1157','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(43,1,'Zboncak PLC','destiny.beer@bogan.biz','844-783-2565','856 Renner Springs Apt. 589','Port Marianne','Utah','US','70168-3378','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(44,1,'Turcotte Group','anissa.langworth@powlowski.com','1-844-325-4088','473 Nichole Oval','Chrisburgh','Georgia','US','35248-6026','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(45,1,'Eichmann PLC','gutkowski.kody@bednar.biz','844.810.5078','32482 Beatty View','Lake Joanie','Georgia','US','70524','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(46,1,'Adams Group','jalen.quigley@hagenes.com','877-614-3870','2298 Hayes Passage','Rueckerside','Tennessee','US','40425-7223','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(47,1,'Von, Hermiston and Bayer','lora19@smith.com','844-320-7262','3074 Diana Estate Suite 257','West Kayleighville','Louisiana','US','19886-4662','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(48,1,'Herman, Goyette and Dickens','gaylord.jaycee@white.com','855-850-0584','679 Ledner Loop','New Guiseppe','Illinois','US','55198','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(49,1,'Heidenreich, O\'Connell and Paucek','leora05@glover.biz','855-683-0877','87416 Henriette Pines Apt. 689','Elizaborough','New Jersey','US','89779-0014','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(50,1,'Corwin, Emard and Dickens','katrina82@aufderhar.com','855.839.8409','78937 Frida Ways Apt. 740','North Neilton','California','US','48665','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(51,1,'Hirthe-Auer','hintz.gregorio@cummings.com','855.588.0652','136 Schoen Fork Apt. 657','McGlynnport','Maryland','US','19396','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(52,1,'Romaguera, Schumm and Zulauf','helene58@robel.com','(800) 814-4667','4115 Rosenbaum Manor','Gaylordchester','Pennsylvania','US','23427-9546','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(53,1,'Adams-Strosin','jannie02@rutherford.com','800.772.6937','54274 Kub View Apt. 397','North Felicity','Georgia','US','29123','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(54,1,'Bosco-Walker','lstiedemann@hauck.com','1-888-791-8836','25839 Giuseppe Mountains Apt. 427','Port Benedict','New York','US','63819-9141','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(55,1,'Tillman, Mitchell and Lebsack','morissette.krystina@barton.com','888.594.9774','47347 Benny Loaf','Lake Katlynn','Delaware','US','42845','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(56,1,'Cassin, Breitenberg and Leuschke','lorna.dickens@stanton.com','877.659.9460','87190 Quigley Lights Suite 814','Port Kurtisberg','Missouri','US','32313','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(57,1,'Lubowitz and Sons','dgaylord@emmerich.com','844-530-7256','5582 Greenfelder Crossroad','Lydafurt','Oregon','US','10443-6759','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(58,1,'Streich Group','geo.tromp@dubuque.net','1-877-463-4736','265 Rachelle Forge','Cassinfort','Texas','US','78559','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(59,1,'Stark-Mosciski','strosin.lila@kautzer.info','866.412.4512','28795 Eino Road','Justinastad','Hawaii','US','06942-6880','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(60,1,'Mitchell, Wilkinson and Mertz','aylin.hill@kuhlman.com','855-706-2021','448 Meta Shoals','Myronborough','Massachusetts','US','08912-6515','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(61,1,'Hill-Toy','adolfo.gleason@kuhlman.info','855-433-3150','697 Birdie Summit','East Aliya','Mississippi','US','66239','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(62,1,'Jacobi Inc','jed32@muller.org','888.707.9798','16503 Pete Meadow','Sallyhaven','New Jersey','US','56831','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(63,1,'Rice, Howell and Murazik','gvonrueden@daniel.com','844-804-2384','152 Klocko Glens','Lake Queenie','Maryland','US','84524-7281','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(64,1,'Gerlach-Grimes','ivon@buckridge.com','(844) 923-0070','396 Reichert Ville','New Lennie','Montana','US','33422-8339','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(65,1,'Zboncak, Luettgen and Zboncak','einar.conroy@sporer.com','1-866-958-1051','55968 Garnett Spring Apt. 046','North Laurianeport','Pennsylvania','US','53380-0796','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(66,1,'Koepp, Yost and Borer','icie38@rosenbaum.org','(877) 760-3503','1616 Mayer Mountains','East Elenashire','Missouri','US','69973','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(67,1,'Yost, Collins and Pfeffer','claudia.little@goldner.com','877.389.0416','694 Teagan Spurs Apt. 813','Sydnistad','Florida','US','89437','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(68,1,'Kunze Inc','vhuels@bins.org','888-951-1115','78110 Joany Views Suite 644','Cassinside','Alaska','US','09463','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(69,1,'Wiegand-Flatley','gladyce54@koelpin.com','844-781-2627','471 Flo Forges Suite 783','Lilianehaven','Massachusetts','US','24544','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(70,1,'O\'Kon, O\'Keefe and O\'Reilly','daren.bernier@hoppe.com','1-877-878-0962','8785 Collin Lakes','Breitenbergborough','Georgia','US','39667','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(71,1,'Waters, Lang and Kuhn','jsteuber@kassulke.biz','866-473-1251','88145 O\'Kon Forks Suite 364','New Modesto','Virginia','US','96664','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(72,1,'Hartmann-Brown','qhaag@mosciski.org','(800) 212-3596','550 Nicola Bridge','Rutherfordshire','Oklahoma','US','13944-9055','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(73,1,'Kertzmann-Luettgen','abradtke@abbott.com','855-561-5408','2944 Otto Heights','Santastad','Georgia','US','76501-3235','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(74,1,'Lesch LLC','gino.stracke@ratke.com','(844) 832-5054','450 Lueilwitz Lake Suite 557','Dixiebury','Mississippi','US','72771','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(75,1,'Torp Group','mkovacek@bashirian.info','(877) 266-5335','215 Deanna Mill','Breitenbergberg','Hawaii','US','85832','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(76,1,'Walker-Kozey','pfeffer.jeramy@weimann.com','1-800-935-2581','803 Breitenberg Harbors Suite 892','Tarynview','Nevada','US','44892-1511','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(77,1,'Hirthe, Goodwin and Douglas','myles45@marquardt.biz','844.602.2811','95380 Marquise Lock','Lake Ramirostad','West Virginia','US','99994-9910','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(78,1,'Batz Inc','dietrich.julie@goldner.com','888-269-4871','571 Bode Manors','Lake Myrtishaven','New Hampshire','US','93154-7727','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(79,1,'Reichel Inc','murray.solon@bayer.com','866.407.4118','1372 Tromp Landing','Cordiatown','Delaware','US','50738-5748','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(80,1,'Reynolds-Block','patricia41@grimes.com','866-946-5039','5223 Magnolia Stravenue Suite 405','Krajcikborough','Arizona','US','17473','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(81,1,'Nienow Group','dooley.kira@casper.com','1-866-284-6794','61101 Hammes Bridge','Toychester','Arizona','US','41028','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(82,1,'Mertz Ltd','ndibbert@rau.com','1-888-396-7311','147 Samanta Flat','Faheyville','Delaware','US','60270','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(83,1,'Cormier-Rolfson','ccarroll@swaniawski.com','844-826-1264','83998 Angeline Village','Port Vallietown','Indiana','US','66201-3968','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(84,1,'Dickens PLC','katheryn15@bednar.com','800.785.1892','18581 Nico Field Suite 813','West Colt','Louisiana','US','16186','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(85,1,'Nolan, Kuvalis and Weber','kasey.smith@klein.net','866.685.2268','669 Ladarius Burgs','Bernadetteside','Connecticut','US','07316','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(86,1,'Reilly, Ziemann and Kozey','yratke@dach.com','(855) 958-7204','46109 Rex Views Apt. 434','Nicholausborough','Texas','US','62211-7916','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(87,1,'Morissette Ltd','swaniawski.darrel@casper.info','800-726-8449','8022 Osborne Rapid Apt. 132','North Hosea','Missouri','US','22223-4373','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(88,1,'Spinka Inc','raquel42@sauer.org','(800) 923-9548','4770 Dasia Creek Apt. 711','Fadelmouth','New Mexico','US','86240-2960','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(89,1,'Boehm-Muller','ankunding.kelly@block.biz','800.886.2784','78924 Jamey Road Apt. 447','Schmelerport','Pennsylvania','US','93598-2777','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(90,1,'Howe and Sons','cokuneva@hegmann.biz','(855) 931-4738','2654 Shana Terrace','South Dorothyshire','North Dakota','US','04983-9978','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(91,1,'Haag Inc','kub.marvin@mraz.info','1-800-915-1234','15345 Efren Alley Apt. 524','East Lucious','Idaho','US','63110-9290','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(92,1,'Ratke PLC','fbogisich@kilback.net','1-866-260-7447','9188 Reichel Villages','Corkerychester','New Jersey','US','59593','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(93,1,'Armstrong LLC','reynolds.berta@kunze.net','877-882-4088','51377 Abernathy Island Apt. 615','Botsfordview','New Hampshire','US','08243','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(94,1,'Terry, Maggio and Bode','edwina78@grady.com','1-888-379-6209','350 Ashley Views Suite 308','Port Katelin','Ohio','US','30625-1226','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(95,1,'Hayes, Fisher and Raynor','hadley46@boyle.com','844-244-8584','4095 Rickey Points Apt. 241','Bergeport','Idaho','US','25803-7365','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(96,1,'Smitham, Lakin and Champlin','amber.steuber@casper.com','(855) 440-5411','970 Elian Crest','Jaynechester','Maryland','US','20479','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(97,1,'Kemmer, Gislason and Bashirian','sawayn.abigayle@orn.com','(877) 379-1723','9381 Streich Club','Fridatown','District of Columbia','US','32899-0830','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(98,1,'Welch LLC','mack.pouros@predovic.com','800.451.5750','112 Fabian Ports Suite 698','Monahanfurt','Georgia','US','68689','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(99,1,'Kutch, DuBuque and Runolfsson','cesar.franecki@mertz.com','1-888-916-3910','924 Swift Mill','Bernierton','Louisiana','US','53492','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),(100,1,'Fritsch-Bergnaum','boyer.xander@lemke.com','866-746-5420','2248 Konopelski Dale','Hillsview','Wyoming','US','60347','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL);
/*!40000 ALTER TABLE `organizations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `strony_wwws`
--

DROP TABLE IF EXISTS `strony_wwws`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `strony_wwws` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `click` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `strony_wwws`
--

LOCK TABLES `strony_wwws` WRITE;
/*!40000 ALTER TABLE `strony_wwws` DISABLE KEYS */;
/*!40000 ALTER TABLE `strony_wwws` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_id` int NOT NULL,
  `first_name` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `owner` tinyint(1) NOT NULL DEFAULT '0',
  `photo_path` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_account_id_index` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES ('0194d6f5-eb5d-4182-81e7-83344ebc45e7',1,'Maria','Hahn','carlotta43@example.net','2024-01-02 10:12:03','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',0,NULL,'RqYPXAqa1h','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),('40500ff3-02f5-470a-9d3f-0ccb9a64f462',1,'Nico','Funk','zoila.ankunding@example.org','2024-01-02 10:12:03','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',0,NULL,'sB1qe71Q3m','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),('42e1ad7d-4c8b-42e7-a0b7-da0af8ce9ae7',1,'Regan','Herman','godfrey.bruen@example.org','2024-01-02 10:12:03','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',0,NULL,'Co18wBbYuC','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),('5422a82c-6a67-494d-9e34-ba01c8f693e6',1,'Ralph','Hoeger','ghettinger@example.org','2024-01-02 10:12:03','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',0,NULL,'aUiC96wesx','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),('c6b8c55a-aac7-4785-9412-a6dcaa8c7214',1,'Reyna','Powlowski','jazmyne.okon@example.com','2024-01-02 10:12:03','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',0,NULL,'kXTugdMmwC','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL),('f2e49a5c-aa87-4174-9493-c2125403d1ee',1,'John','Doe','johndoe@example.com','2024-01-02 10:12:03','$2y$10$dqcYq1r3mryNs9QWfF.L1OwJ3Be9zeMQGPFhF8ANlbUgdCSGvqGC2',1,NULL,'tgZJv9tCI5','2024-01-02 10:12:03','2024-01-02 10:12:03',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `zakres`
--

DROP TABLE IF EXISTS `zakres`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `zakres` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `zakres`
--

LOCK TABLES `zakres` WRITE;
/*!40000 ALTER TABLE `zakres` DISABLE KEYS */;
INSERT INTO `zakres` VALUES ('acaa49e5-3464-4e70-a14a-cda3aa680060','Zakres1',NULL,NULL,NULL),('f288bf5e-51a7-4c65-8abd-f3aedc849f55','Zakres2',NULL,NULL,NULL);
/*!40000 ALTER TABLE `zakres` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `zapytanias`
--

DROP TABLE IF EXISTS `zapytanias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `zapytanias` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_zapyt` varchar(18) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_otrzymal_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_otrzymania` date DEFAULT NULL,
  `data_zlozenia` date DEFAULT NULL,
  `client_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nazwa_projektu` text COLLATE utf8mb4_unicode_ci,
  `miejscowosc` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kraj_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `zakres_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_opracowuje_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start` date DEFAULT NULL,
  `end` date DEFAULT NULL,
  `kwota` bigint DEFAULT NULL,
  `waluta` text COLLATE utf8mb4_unicode_ci,
  `opis` text COLLATE utf8mb4_unicode_ci,
  `miasto` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kurs` decimal(8,2) DEFAULT NULL,
  `kwotaPLN` decimal(8,2) DEFAULT NULL,
  `arch` tinyint(1) DEFAULT NULL,
  `arch_time` date DEFAULT NULL,
  `arch_user` int DEFAULT NULL,
  `wznowienie` int DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `zapytanias_user_otrzymal_id_foreign` (`user_otrzymal_id`),
  KEY `zapytanias_kraj_id_foreign` (`kraj_id`),
  KEY `zapytanias_zakres_id_foreign` (`zakres_id`),
  KEY `zapytanias_user_opracowuje_id_foreign` (`user_opracowuje_id`),
  KEY `zapytanias_user_id_foreign` (`user_id`),
  CONSTRAINT `zapytanias_kraj_id_foreign` FOREIGN KEY (`kraj_id`) REFERENCES `krajs` (`id`),
  CONSTRAINT `zapytanias_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `zapytanias_user_opracowuje_id_foreign` FOREIGN KEY (`user_opracowuje_id`) REFERENCES `users` (`id`),
  CONSTRAINT `zapytanias_user_otrzymal_id_foreign` FOREIGN KEY (`user_otrzymal_id`) REFERENCES `users` (`id`),
  CONSTRAINT `zapytanias_zakres_id_foreign` FOREIGN KEY (`zakres_id`) REFERENCES `zakres` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `zapytanias`
--

LOCK TABLES `zapytanias` WRITE;
/*!40000 ALTER TABLE `zapytanias` DISABLE KEYS */;
INSERT INTO `zapytanias` VALUES ('7369162c-baf4-4e06-a284-603a77310416','1/2024','40500ff3-02f5-470a-9d3f-0ccb9a64f462','1974-01-18','2015-03-03','6c262aef-fe2a-4a63-93d5-4d7df4b927fe','Distinctio Soluta nsss3','Debitis ut sunt exc','b1cd262d-0730-406a-a54f-5f64d6699bc5','f288bf5e-51a7-4c65-8abd-f3aedc849f55','42e1ad7d-4c8b-42e7-a0b7-da0af8ce9ae7','1985-10-23','1989-03-14',1,'7af71111-fec9-4ca6-ab43-0b8899ac4fd6','Nisi id reprehenderi',NULL,'f2e49a5c-aa87-4174-9493-c2125403d1ee',4.12,4.12,NULL,NULL,NULL,NULL,NULL,'2024-01-02 10:56:16','2024-01-02 13:24:16');
/*!40000 ALTER TABLE `zapytanias` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-01-08 20:04:10
