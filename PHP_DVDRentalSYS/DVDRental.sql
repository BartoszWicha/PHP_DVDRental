-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: dvdrental
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
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `CatCode` tinyint(3) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `Description` varchar(30) NOT NULL,
  `Rates` decimal(5,2) unsigned NOT NULL,
  PRIMARY KEY (`CatCode`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (001,'New Release',10.20),(002,'Old Release',9.00);
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dvds`
--

DROP TABLE IF EXISTS `dvds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dvds` (
  `DVDID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Title` varchar(30) NOT NULL,
  `Status` enum('A','R','U') NOT NULL,
  `CatCode` tinyint(3) unsigned zerofill NOT NULL,
  PRIMARY KEY (`DVDID`),
  KEY `CatCode` (`CatCode`),
  CONSTRAINT `dvds_ibfk_1` FOREIGN KEY (`CatCode`) REFERENCES `categories` (`CatCode`)
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dvds`
--

LOCK TABLES `dvds` WRITE;
/*!40000 ALTER TABLE `dvds` DISABLE KEYS */;
INSERT INTO `dvds` VALUES (1,'Beyond the Horizon','R',001),(2,'Finding nemo','A',002),(3,'Harry Potter','A',001),(4,'Shrek','A',002);
/*!40000 ALTER TABLE `dvds` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `members`
--

DROP TABLE IF EXISTS `members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `members` (
  `MemberID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `FirstName` varchar(20) NOT NULL,
  `SurName` varchar(20) NOT NULL,
  `PhoneNum` varchar(15) NOT NULL,
  `status` enum('A','C') NOT NULL,
  PRIMARY KEY (`MemberID`),
  UNIQUE KEY `PhoneNum` (`PhoneNum`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `members`
--

LOCK TABLES `members` WRITE;
/*!40000 ALTER TABLE `members` DISABLE KEYS */;
INSERT INTO `members` VALUES (1,'John','Doe','086342786','A'),(2,'John','Murphy','853472253','C'),(3,'Bart','Wicha','089238472','A');
/*!40000 ALTER TABLE `members` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rentals`
--

DROP TABLE IF EXISTS `rentals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rentals` (
  `RentID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `DateRented` date NOT NULL DEFAULT current_timestamp(),
  `DueDate` date NOT NULL DEFAULT (current_timestamp() + interval 7 day),
  `Status` enum('F','U') NOT NULL,
  `MemberID` smallint(5) unsigned NOT NULL,
  `Cost` decimal(6,2) unsigned NOT NULL,
  PRIMARY KEY (`RentID`),
  KEY `MemberID` (`MemberID`),
  CONSTRAINT `rentals_ibfk_1` FOREIGN KEY (`MemberID`) REFERENCES `members` (`MemberID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rentals`
--

LOCK TABLES `rentals` WRITE;
/*!40000 ALTER TABLE `rentals` DISABLE KEYS */;
INSERT INTO `rentals` VALUES (1,'2024-02-19','2024-02-26','U',1,0.00);
/*!40000 ALTER TABLE `rentals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `renteditems`
--

DROP TABLE IF EXISTS `renteditems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `renteditems` (
  `RentID` mediumint(8) unsigned NOT NULL,
  `DVDID` smallint(5) unsigned NOT NULL,
  `Cost` decimal(5,2) unsigned NOT NULL,
  `DateReturned` date DEFAULT NULL,
  PRIMARY KEY (`RentID`,`DVDID`),
  KEY `DVDID` (`DVDID`),
  CONSTRAINT `renteditems_ibfk_1` FOREIGN KEY (`DVDID`) REFERENCES `dvds` (`DVDID`),
  CONSTRAINT `renteditems_ibfk_2` FOREIGN KEY (`RentID`) REFERENCES `rentals` (`RentID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `renteditems`
--

LOCK TABLES `renteditems` WRITE;
/*!40000 ALTER TABLE `renteditems` DISABLE KEYS */;
INSERT INTO `renteditems` VALUES (1,1,0.00,NULL);
/*!40000 ALTER TABLE `renteditems` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-04-28  6:06:47
