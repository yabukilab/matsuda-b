-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: game
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
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comments` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `GameID` int(11) NOT NULL,
  `Comment` text NOT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ID`),
  KEY `GameID` (`GameID`),
  CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`GameID`) REFERENCES `games` (`GameID`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
INSERT INTO `comments` VALUES (1,7,'こんにちは\r\n','2024-06-28 07:41:24'),(2,7,'こんにちは\r\n','2024-06-28 07:41:27'),(3,7,'こちらこそ','2024-06-28 07:41:37'),(4,7,'こちらこそ\r\n','2024-06-28 07:41:54'),(5,7,'こちらこそ\r\n','2024-06-28 07:49:42');
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `games`
--

DROP TABLE IF EXISTS `games`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `games` (
  `GameID` int(11) NOT NULL AUTO_INCREMENT,
  `Title` varchar(255) NOT NULL,
  `gaiyou` int(11) DEFAULT NULL,
  `Genre` varchar(100) NOT NULL,
  `ReleaseDate` date NOT NULL,
  `CoverImageURL` varchar(255) NOT NULL,
  `Link` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`GameID`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `games`
--

LOCK TABLES `games` WRITE;
/*!40000 ALTER TABLE `games` DISABLE KEYS */;
INSERT INTO `games` VALUES (1,'ゲーム1',NULL,'アクションRPG','2022-08-11','https://img.gamewith.jp/img/5a27fe1b0d4509f4a75fd31be7281f26.png','game1.php'),(2,'ゲーム２',NULL,'','0000-00-00','','game2.php'),(3,'ゲーム3',NULL,'','0000-00-00','','game3.php'),(4,'ゲーム4',NULL,'','0000-00-00','','game4.php'),(5,'ゲーム5\r\n',NULL,'','0000-00-00','','game5.php'),(6,'ゲーム6',NULL,'','0000-00-00','','game6.php'),(7,'ゲーム7',NULL,'','0000-00-00','','game7.php'),(9,'ゲーム9',NULL,'','0000-00-00','','game9.php'),(10,'ゲーム10',NULL,'','0000-00-00','','game10.php');
/*!40000 ALTER TABLE `games` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `valuation`
--

DROP TABLE IF EXISTS `valuation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `valuation` (
  `ReviewID` int(11) NOT NULL AUTO_INCREMENT,
  `GameID` int(11) NOT NULL,
  `Rating` int(11) NOT NULL,
  `ReviewText` text NOT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ReviewID`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `valuation`
--

LOCK TABLES `valuation` WRITE;
/*!40000 ALTER TABLE `valuation` DISABLE KEYS */;
INSERT INTO `valuation` VALUES (1,1,5,'おもろかった。あああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああ','2024-06-07 08:19:22','2024-06-07 08:19:22'),(2,1,4,'','2024-06-14 05:50:16','2024-06-14 05:50:16'),(3,2,3,'','2024-06-14 05:50:16','2024-06-14 05:50:16'),(4,2,1,'','2024-06-14 05:50:47','2024-06-14 05:50:47'),(5,1,3,'','2024-06-14 05:50:47','2024-06-14 05:50:47'),(6,3,4,'','2024-06-14 08:36:07','2024-06-14 08:36:07'),(7,3,4,'','2024-06-14 08:36:15','2024-06-14 08:36:15'),(8,3,1,'','2024-06-14 08:37:20','2024-06-14 08:37:20'),(9,3,1,'','2024-06-14 08:37:35','2024-06-14 08:37:35'),(10,3,4,'','2024-06-14 08:45:50','2024-06-14 08:45:50'),(11,3,4,'','2024-06-14 09:32:12','2024-06-14 09:32:12'),(12,3,4,'','2024-06-14 09:34:16','2024-06-14 09:34:16'),(13,3,1,'','2024-06-14 09:34:43','2024-06-14 09:34:43'),(14,3,5,'','2024-06-14 10:03:29','2024-06-14 10:03:29'),(15,3,4,'','2024-06-14 10:15:44','2024-06-14 10:15:44'),(16,8,3,'','2024-06-14 10:49:57','2024-06-14 10:49:57'),(17,5,1,'','2024-06-28 05:39:22','2024-06-28 05:39:22'),(18,5,3,'','2024-06-28 05:39:34','2024-06-28 05:39:34');
/*!40000 ALTER TABLE `valuation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wiki_content`
--

DROP TABLE IF EXISTS `wiki_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wiki_content` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `GameID` int(11) NOT NULL,
  `Section` varchar(255) NOT NULL,
  `Content` text NOT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`ID`),
  UNIQUE KEY `unique_game_section` (`GameID`,`Section`),
  CONSTRAINT `wiki_content_ibfk_1` FOREIGN KEY (`GameID`) REFERENCES `games` (`GameID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wiki_content`
--

LOCK TABLES `wiki_content` WRITE;
/*!40000 ALTER TABLE `wiki_content` DISABLE KEYS */;
INSERT INTO `wiki_content` VALUES (1,7,'セクション\r\n','コンテンツ２','2024-06-28 07:49:30','2024-06-28 08:46:22');
/*!40000 ALTER TABLE `wiki_content` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-06-28 19:01:15
