-- MySQL dump 10.13  Distrib 8.0.40, for Win64 (x86_64)
--
-- Host: localhost    Database: telehealthdb
-- ------------------------------------------------------
-- Server version	8.0.40

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
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin` (
  `AdminID` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `FName` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  `DOB` date NOT NULL,
  `Gender` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `NatID_PP` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `PhoneNum` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Email` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `CreatedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`AdminID`),
  CONSTRAINT `admin_ibfk_1` FOREIGN KEY (`AdminID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin`
--

LOCK TABLES `admin` WRITE;
/*!40000 ALTER TABLE `admin` DISABLE KEYS */;
INSERT INTO `admin` VALUES ('ADM001','Esther Onyango','1990-04-27','Female','AID0001','0701000001','eonyango@telehealth.test','2025-12-24 21:01:00','2026-03-04 20:32:00'),('ADM002','Jane Ochieng','1989-10-18','Female','AID0002','0701000002','jochieng@telehealth.test','2025-12-04 23:45:00','2026-03-12 18:31:00'),('ADM003','Francis Kamau','1988-07-10','Male','AID0003','0701000003','fkamau@telehealth.test','2025-12-02 21:53:00','2026-03-07 23:29:00'),('ADM004','Catherine Kamau','1987-02-11','Female','AID0004','0701000004','ckamau@telehealth.test','2025-12-14 12:10:00','2026-02-27 14:19:00'),('ADM005','Janet Sang','1986-11-17','Female','AID0005','0701000005','jsang@telehealth.test','2025-11-08 20:08:00','2026-03-06 08:08:00'),('ADM006','Dennis Korir','1985-03-23','Male','AID0006','0701000006','dkorir@telehealth.test','2025-11-05 16:44:00','2026-02-18 22:43:00'),('ADM007','Ruth Wekesa','1984-09-17','Female','AID0007','0701000007','rwekesa@telehealth.test','2025-11-04 13:28:00','2026-02-16 15:21:00'),('ADM008','Mary Wairimu','1983-04-04','Female','AID0008','0701000008','mwairimu@telehealth.test','2025-12-17 16:37:00','2026-03-11 14:11:00'),('ADM009','Caleb Maina','1982-11-04','Male','AID0009','0701000009','cmaina@telehealth.test','2025-12-03 20:39:00','2026-02-26 04:17:00'),('ADM010','Michael Karanja','1991-06-09','Male','AID0010','0701000010','mkaranja@telehealth.test','2025-11-21 13:29:00','2026-03-09 19:11:00'),('ADM011','Samuel Kamau','1990-04-06','Male','AID0011','0701000011','skamau@telehealth.test','2025-11-20 04:52:00','2026-03-10 11:00:00'),('ADM012','Michael Mumo','1989-08-24','Male','AID0012','0701000012','mmumo@telehealth.test','2025-11-24 09:45:00','2026-03-07 06:07:00'),('ADM013','David Mumo','1988-03-20','Male','AID0013','0701000013','dmumo@telehealth.test','2025-12-19 11:36:00','2026-03-15 09:49:00'),('ADM014','Stella Wanjiru','1987-09-01','Female','AID0014','0701000014','swanjiru@telehealth.test','2025-11-04 07:19:00','2026-02-14 18:13:00'),('ADM015','Grace Maina','1986-03-24','Female','AID0015','0701000015','gmaina@telehealth.test','2025-12-20 10:08:00','2026-03-09 20:34:00'),('ADM016','Emmanuel Ochieng','1985-10-01','Male','AID0016','0701000016','eochieng@telehealth.test','2025-11-08 14:55:00','2026-02-22 17:36:00'),('ADM017','Victor Mutiso','1984-01-27','Male','AID0017','0701000017','vmutiso@telehealth.test','2025-10-27 13:50:00','2026-02-23 00:45:00'),('ADM018','Joseph Otieno','1983-07-11','Male','AID0018','0701000018','jotieno@telehealth.test','2025-11-01 03:46:00','2026-02-25 11:10:00'),('ADM019','Veronica Kamau','1982-02-21','Female','AID0019','0701000019','vkamau@telehealth.test','2025-12-18 05:08:00','2026-03-07 13:32:00'),('ADM020','Naomi Mwangi','1991-07-15','Female','AID0020','0701000020','nmwangi@telehealth.test','2025-11-29 16:06:00','2026-02-28 01:12:00');
/*!40000 ALTER TABLE `admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appointments`
--

DROP TABLE IF EXISTS `appointments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `appointments` (
  `AppID` int NOT NULL AUTO_INCREMENT,
  `PatientID` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `DoctorID` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `NurseID` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `AppointmentDate` datetime NOT NULL,
  `DurationMinutes` int NOT NULL DEFAULT '20',
  `ReasonCategory` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ReasonText` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `DoctorRejectedAt` datetime DEFAULT NULL,
  `DoctorRejectionReason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Status` enum('Pending','AwaitingPatientApproval','Confirmed','Cancelled','Completed') COLLATE utf8mb4_unicode_ci DEFAULT 'Pending',
  `Notes` text COLLATE utf8mb4_unicode_ci,
  `CreatedAt` datetime DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`AppID`),
  KEY `idx_appt_patient_time` (`PatientID`,`AppointmentDate`),
  KEY `idx_appt_doctor_time` (`DoctorID`,`AppointmentDate`),
  KEY `idx_appt_nurse_time` (`NurseID`,`AppointmentDate`),
  KEY `idx_appt_status` (`Status`),
  CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`PatientID`) REFERENCES `patients` (`PatientID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`DoctorID`) REFERENCES `doctors` (`DoctorID`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `appointments_ibfk_3` FOREIGN KEY (`NurseID`) REFERENCES `nurses` (`NurseID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appointments`
--

LOCK TABLES `appointments` WRITE;
/*!40000 ALTER TABLE `appointments` DISABLE KEYS */;
INSERT INTO `appointments` VALUES (1,'PT001','DR001','NR001','2026-02-03 11:40:00',20,'General Checkup','Routine follow-up',NULL,NULL,'Completed','Vitals stable','2026-01-30 05:48:00','2026-03-02 23:17:00'),(2,'PT002','DR002','NR002','2026-02-18 15:00:00',20,'General Checkup','Routine follow-up',NULL,NULL,'Completed','Vitals stable','2026-01-22 18:57:00','2026-03-07 16:20:00'),(3,'PT003','DR003','NR003','2026-01-29 16:10:00',20,'General Checkup','Routine follow-up',NULL,NULL,'Completed','Vitals stable','2026-01-26 10:19:00','2026-03-11 07:28:00'),(4,'PT004','DR004','NR004','2026-02-02 11:20:00',20,'General Checkup','Routine follow-up',NULL,NULL,'Completed','Vitals stable','2026-02-25 09:36:00','2026-03-14 10:08:00'),(5,'PT005','DR005','NR005','2026-02-09 11:20:00',20,'General Checkup','Routine follow-up',NULL,NULL,'Completed','Vitals stable','2026-02-07 16:52:00','2026-03-04 13:37:00'),(6,'PT006','DR006','NR006','2026-02-12 09:20:00',20,'General Checkup','Routine follow-up',NULL,NULL,'Completed','Vitals stable','2026-03-06 17:10:00','2026-03-04 20:01:00'),(7,'PT007','DR007','NR007','2026-02-28 10:00:00',20,'General Checkup','Routine follow-up',NULL,NULL,'Completed','Vitals stable','2026-02-12 05:07:00','2026-03-05 01:13:00'),(8,'PT008','DR008','NR008','2026-02-12 16:20:00',20,'General Checkup','Routine follow-up',NULL,NULL,'Completed','Vitals stable','2026-02-06 06:54:00','2026-03-10 07:15:00'),(9,'PT009','DR009','NR009','2026-02-28 15:20:00',20,'General Checkup','Routine follow-up',NULL,NULL,'Completed','Vitals stable','2026-02-28 15:08:00','2026-03-04 12:56:00'),(10,'PT010','DR010','NR010','2026-02-25 14:30:00',20,'General Checkup','Routine follow-up',NULL,NULL,'Completed','Vitals stable','2026-02-10 07:15:00','2026-03-12 08:42:00'),(11,'PT011','DR011','NR011','2026-02-27 09:30:00',20,'General Checkup','Routine follow-up','2026-03-09 11:46:00','Schedule conflict','Cancelled','Vitals stable','2026-02-01 08:33:00','2026-03-13 17:31:00'),(12,'PT012','DR012','NR012','2026-02-11 12:10:00',20,'General Checkup','Routine follow-up','2026-03-13 11:34:00','Schedule conflict','Cancelled','Vitals stable','2026-02-27 13:36:00','2026-03-15 04:12:00'),(13,'PT013','DR013','NR013','2026-03-05 13:20:00',20,'General Checkup','Routine follow-up','2026-03-14 05:54:00','Schedule conflict','Cancelled','Vitals stable','2026-02-22 17:32:00','2026-03-14 01:04:00'),(14,'PT014','DR014','NR014','2026-02-15 14:10:00',20,'General Checkup','Routine follow-up','2026-03-10 10:33:00','Schedule conflict','Cancelled','Vitals stable','2026-03-05 03:02:00','2026-03-09 16:11:00'),(15,'PT015','DR015','NR015','2026-02-13 09:30:00',20,'General Checkup','Routine follow-up','2026-03-11 16:46:00','Schedule conflict','Cancelled','Vitals stable','2026-02-06 14:09:00','2026-03-07 01:24:00'),(16,'PT016','DR016','NR016','2026-01-24 14:00:00',20,'General Checkup','Routine follow-up','2026-03-13 00:47:00','Schedule conflict','Cancelled','Vitals stable','2026-02-25 15:14:00','2026-03-14 02:42:00'),(17,'PT017','DR017','NR017','2026-01-16 15:10:00',20,'General Checkup','Routine follow-up','2026-03-14 03:14:00','Schedule conflict','Cancelled','Vitals stable','2026-02-17 11:58:00','2026-03-05 08:21:00'),(18,'PT018','DR018','NR018','2026-02-06 09:20:00',20,'General Checkup','Routine follow-up','2026-03-11 20:09:00','Schedule conflict','Cancelled','Vitals stable','2026-01-22 09:41:00','2026-03-02 13:46:00'),(19,'PT019','DR019','NR019','2026-03-20 13:20:00',20,'General Checkup','Routine follow-up',NULL,NULL,'Confirmed','Vitals stable','2026-02-23 12:20:00','2026-03-06 08:27:00'),(20,'PT020','DR020','NR020','2026-03-19 12:40:00',20,'General Checkup','Routine follow-up',NULL,NULL,'Confirmed','Vitals stable','2026-02-14 20:34:00','2026-03-10 19:18:00'),(21,'PT001','DR001','NR001','2026-04-06 08:30:00',20,'General Checkup','Routine follow-up',NULL,NULL,'Confirmed','Vitals stable','2026-02-15 07:04:00','2026-03-12 13:11:00'),(22,'PT002','DR002','NR002','2026-04-10 12:00:00',20,'General Checkup','Routine follow-up',NULL,NULL,'Confirmed','Vitals stable','2026-03-02 17:48:00','2026-03-10 15:52:00'),(23,'PT003','DR003','NR003','2026-04-06 14:20:00',20,'General Checkup','Routine follow-up',NULL,NULL,'Confirmed','Vitals stable','2026-02-26 05:25:00','2026-03-08 02:20:00'),(24,'PT004','DR004','NR004','2026-06-09 15:10:00',20,'General Checkup','Routine follow-up',NULL,NULL,'Confirmed','Vitals stable','2026-01-19 20:51:00','2026-03-09 08:41:00'),(25,'PT005','DR005','NR005','2026-05-20 16:20:00',20,'General Checkup','Routine follow-up',NULL,NULL,'Confirmed','Vitals stable','2026-02-18 10:26:00','2026-03-06 22:44:00'),(26,'PT006','DR006','NR006','2026-04-10 16:00:00',20,'General Checkup','Routine follow-up',NULL,NULL,'Confirmed','Vitals stable','2026-02-23 01:05:00','2026-03-07 12:40:00'),(27,'PT007','DR007','NR007','2026-03-28 09:00:00',20,'General Checkup','Routine follow-up',NULL,NULL,'Confirmed','Vitals stable','2026-02-05 17:05:00','2026-03-06 13:14:00'),(28,'PT008','DR008','NR008','2026-05-30 13:40:00',20,'General Checkup','Routine follow-up',NULL,NULL,'Confirmed','Vitals stable','2026-01-23 13:47:00','2026-03-04 09:44:00'),(29,'PT009','DR009','NR009','2026-06-15 15:00:00',20,'General Checkup','Routine follow-up',NULL,NULL,'Pending','Vitals stable','2026-03-04 04:48:00','2026-03-01 05:19:00'),(30,'PT010','DR010','NR010','2026-06-12 15:10:00',20,'General Checkup','Routine follow-up',NULL,NULL,'Pending','Vitals stable','2026-01-22 02:13:00','2026-03-01 15:28:00'),(31,'PT011','DR011','NR011','2026-06-13 10:10:00',20,'General Checkup','Routine follow-up',NULL,NULL,'Pending','Vitals stable','2026-02-28 00:50:00','2026-03-10 17:10:00'),(32,'PT012','DR012','NR012','2026-05-27 15:10:00',20,'General Checkup','Routine follow-up',NULL,NULL,'Pending','Vitals stable','2026-01-20 08:45:00','2026-03-15 00:37:00'),(33,'PT013','DR013','NR013','2026-03-28 10:20:00',20,'General Checkup','Routine follow-up',NULL,NULL,'Pending','Vitals stable','2026-01-30 22:04:00','2026-03-07 03:42:00'),(34,'PT014','DR014','NR014','2026-04-30 14:00:00',20,'General Checkup','Routine follow-up',NULL,NULL,'Pending','Vitals stable','2026-02-07 13:47:00','2026-03-11 09:45:00'),(35,'PT015','DR015','NR015','2026-03-25 16:40:00',20,'General Checkup','Routine follow-up',NULL,NULL,'AwaitingPatientApproval','Vitals stable','2026-03-01 11:39:00','2026-03-01 07:47:00'),(36,'PT016','DR016','NR016','2026-06-14 13:30:00',20,'General Checkup','Routine follow-up',NULL,NULL,'AwaitingPatientApproval','Vitals stable','2026-02-01 02:53:00','2026-03-09 21:15:00'),(37,'PT017','DR017','NR017','2026-03-25 11:20:00',20,'General Checkup','Routine follow-up',NULL,NULL,'AwaitingPatientApproval','Vitals stable','2026-01-25 16:00:00','2026-03-06 03:19:00'),(38,'PT018','DR018','NR018','2026-06-12 13:00:00',20,'General Checkup','Routine follow-up',NULL,NULL,'AwaitingPatientApproval','Vitals stable','2026-01-29 11:05:00','2026-03-08 09:51:00'),(39,'PT019','DR019','NR019','2026-04-22 10:20:00',20,'General Checkup','Routine follow-up',NULL,NULL,'AwaitingPatientApproval','Vitals stable','2026-03-02 12:55:00','2026-03-14 06:27:00'),(40,'PT020','DR020','NR020','2026-05-16 11:30:00',20,'General Checkup','Routine follow-up',NULL,NULL,'AwaitingPatientApproval','Vitals stable','2026-01-23 12:01:00','2026-03-01 13:16:00'),(41,'PT001',NULL,'NR001','2026-03-25 08:30:00',30,'General','headache','2026-03-20 20:00:01','Patient requested reassignment.','Pending',NULL,'2026-03-20 19:37:38','2026-03-20 20:00:01');
/*!40000 ALTER TABLE `appointments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `caregiver_messages`
--

DROP TABLE IF EXISTS `caregiver_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `caregiver_messages` (
  `MessageID` int NOT NULL AUTO_INCREMENT,
  `AppID` int NOT NULL,
  `SenderRole` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `SenderID` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `MsgDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`MessageID`),
  KEY `idx_caregiver_msg_app` (`AppID`),
  KEY `idx_caregiver_msg_sender` (`SenderID`),
  CONSTRAINT `caregiver_messages_ibfk_1` FOREIGN KEY (`AppID`) REFERENCES `appointments` (`AppID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `caregiver_messages`
--

LOCK TABLES `caregiver_messages` WRITE;
/*!40000 ALTER TABLE `caregiver_messages` DISABLE KEYS */;
INSERT INTO `caregiver_messages` VALUES (1,1,'Caregiver','CG001','Patient needs assistance for transport.','2026-02-25 17:46:00'),(2,2,'Caregiver','CG002','Patient needs assistance for transport.','2026-02-20 05:52:00'),(3,3,'Caregiver','CG003','Patient needs assistance for transport.','2026-03-14 06:01:00'),(4,4,'Caregiver','CG004','Patient needs assistance for transport.','2026-03-03 15:03:00'),(5,5,'Caregiver','CG005','Patient needs assistance for transport.','2026-03-13 18:29:00'),(6,6,'Caregiver','CG006','Patient needs assistance for transport.','2026-02-27 04:31:00'),(7,7,'Caregiver','CG007','Patient needs assistance for transport.','2026-02-25 16:56:00'),(8,8,'Caregiver','CG008','Patient needs assistance for transport.','2026-02-27 15:24:00'),(9,9,'Caregiver','CG009','Patient needs assistance for transport.','2026-03-10 12:19:00'),(10,10,'Caregiver','CG010','Patient needs assistance for transport.','2026-03-07 12:06:00'),(11,11,'Caregiver','CG011','Patient needs assistance for transport.','2026-02-27 03:16:00'),(12,12,'Caregiver','CG012','Patient needs assistance for transport.','2026-03-04 12:14:00'),(13,13,'Caregiver','CG013','Patient needs assistance for transport.','2026-03-11 01:49:00'),(14,14,'Caregiver','CG014','Patient needs assistance for transport.','2026-02-23 09:40:00'),(15,15,'Caregiver','CG015','Patient needs assistance for transport.','2026-03-03 03:51:00'),(16,16,'Caregiver','CG016','Patient needs assistance for transport.','2026-02-24 23:57:00'),(17,17,'Caregiver','CG017','Patient needs assistance for transport.','2026-03-08 09:16:00'),(18,18,'Caregiver','CG018','Patient needs assistance for transport.','2026-03-02 23:16:00'),(19,19,'Caregiver','CG019','Patient needs assistance for transport.','2026-03-11 03:44:00'),(20,20,'Caregiver','CG020','Patient needs assistance for transport.','2026-02-26 10:55:00');
/*!40000 ALTER TABLE `caregiver_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `caregiver_patients`
--

DROP TABLE IF EXISTS `caregiver_patients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `caregiver_patients` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `CaregiverID` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `PatientID` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Status` enum('Pending','Accepted','Rejected') COLLATE utf8mb4_unicode_ci DEFAULT 'Pending',
  `RequestDate` datetime DEFAULT CURRENT_TIMESTAMP,
  `ResponseDate` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `CaregiverID` (`CaregiverID`,`PatientID`),
  KEY `idx_caregiver_patients` (`CaregiverID`,`PatientID`),
  KEY `PatientID` (`PatientID`),
  CONSTRAINT `caregiver_patients_ibfk_1` FOREIGN KEY (`CaregiverID`) REFERENCES `caregivers` (`CaregiverID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `caregiver_patients_ibfk_2` FOREIGN KEY (`PatientID`) REFERENCES `patients` (`PatientID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `caregiver_patients`
--

LOCK TABLES `caregiver_patients` WRITE;
/*!40000 ALTER TABLE `caregiver_patients` DISABLE KEYS */;
INSERT INTO `caregiver_patients` VALUES (1,'CG001','PT003','Pending','2026-02-20 23:40:00',NULL),(2,'CG002','PT004','Pending','2026-03-09 12:13:00',NULL),(3,'CG003','PT005','Pending','2026-02-11 03:53:00',NULL),(4,'CG004','PT006','Pending','2026-02-04 02:28:00',NULL),(5,'CG005','PT007','Pending','2026-02-25 22:10:00',NULL),(6,'CG006','PT008','Pending','2026-01-29 00:08:00',NULL),(7,'CG007','PT009','Accepted','2026-02-20 14:15:00','2026-02-25 06:57:00'),(8,'CG008','PT010','Accepted','2026-02-25 07:50:00','2026-03-12 19:41:00'),(9,'CG009','PT011','Accepted','2026-01-17 06:41:00','2026-02-25 14:35:00'),(10,'CG010','PT012','Accepted','2026-02-07 01:33:00','2026-03-12 20:41:00'),(11,'CG011','PT013','Accepted','2026-03-06 21:55:00','2026-03-12 00:45:00'),(12,'CG012','PT014','Accepted','2026-02-12 01:58:00','2026-03-02 20:37:00'),(13,'CG013','PT015','Rejected','2026-02-08 16:05:00','2026-02-25 21:16:00'),(14,'CG014','PT016','Rejected','2026-03-03 16:06:00','2026-03-11 00:20:00'),(15,'CG015','PT017','Rejected','2026-01-17 00:35:00','2026-03-09 11:24:00'),(16,'CG016','PT018','Rejected','2026-02-08 01:53:00','2026-02-24 16:07:00'),(17,'CG017','PT019','Rejected','2026-02-15 21:51:00','2026-02-25 10:49:00'),(18,'CG018','PT020','Rejected','2026-03-08 23:50:00','2026-03-15 23:31:00'),(19,'CG019','PT001','Rejected','2026-02-14 01:22:00','2026-03-06 01:20:00'),(20,'CG020','PT002','Rejected','2026-03-08 01:09:00','2026-03-01 09:31:00'),(21,'CG011','PT017','Pending','2026-03-20 19:40:07',NULL),(22,'CG011','PT014','Pending','2026-03-20 19:40:42',NULL);
/*!40000 ALTER TABLE `caregiver_patients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `caregivers`
--

DROP TABLE IF EXISTS `caregivers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `caregivers` (
  `CaregiverID` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `FName` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  `DOB` date NOT NULL,
  `Gender` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `NatID_PP` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `PhoneNum` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Email` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `WorkHours` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `CreatedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`CaregiverID`),
  CONSTRAINT `caregivers_ibfk_1` FOREIGN KEY (`CaregiverID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `caregivers`
--

LOCK TABLES `caregivers` WRITE;
/*!40000 ALTER TABLE `caregivers` DISABLE KEYS */;
INSERT INTO `caregivers` VALUES ('CG001','Alice Githinji','1997-03-06','Female','CID0001','0712000001','agithinji@telehealth.test','Weekdays 8am-5pm','2026-01-22 20:02:00','2026-02-28 15:40:00'),('CG002','Joy Korir','1996-12-15','Female','CID0002','0712000002','jkorir2@telehealth.test','Weekdays 8am-5pm','2025-12-22 00:46:00','2026-03-13 06:26:00'),('CG003','Emmanuel Cheruiyot','1995-11-07','Male','CID0003','0712000003','echeruiyot@telehealth.test','Weekdays 8am-5pm','2025-12-12 01:50:00','2026-03-04 07:35:00'),('CG004','Francis Kariuki','1994-10-28','Male','CID0004','0712000004','fkariuki@telehealth.test','Weekdays 8am-5pm','2026-01-21 17:25:00','2026-03-01 15:54:00'),('CG005','David Nduta','1993-09-22','Male','CID0005','0712000005','dnduta@telehealth.test','Weekdays 8am-5pm','2026-01-07 01:01:00','2026-03-10 12:43:00'),('CG006','Helen Maina','1992-12-10','Female','CID0006','0712000006','hmaina@telehealth.test','Weekdays 8am-5pm','2025-12-28 07:10:00','2026-03-09 00:30:00'),('CG007','Sarah Mutiso','1991-09-02','Female','CID0007','0712000007','smutiso@telehealth.test','Weekdays 8am-5pm','2026-01-20 11:47:00','2026-03-15 04:42:00'),('CG008','Ann Sang','1990-01-10','Female','CID0008','0712000008','asang@telehealth.test','Weekdays 8am-5pm','2025-12-09 20:18:00','2026-02-28 16:31:00'),('CG009','Veronica Githinji','1989-01-13','Female','CID0009','0712000009','vgithinji@telehealth.test','Weekdays 8am-5pm','2025-12-15 05:34:00','2026-02-25 01:13:00'),('CG010','Stella Nduta','1988-04-28','Female','CID0010','0712000010','snduta@telehealth.test','Weekdays 8am-5pm','2025-12-21 23:30:00','2026-03-15 01:55:00'),('CG011','Peter Onyango','1987-03-19','Male','CID0011','0712000011','ponyango@telehealth.test','Weekdays 8am-5pm','2026-01-01 01:06:00','2026-03-15 11:48:00'),('CG012','David Kariuki','1986-05-10','Male','CID0012','0712000012','dkariuki@telehealth.test','Weekdays 8am-5pm','2026-01-16 15:33:00','2026-02-22 02:10:00'),('CG013','Paul Njoroge','1985-10-26','Male','CID0013','0712000013','pnjoroge2@telehealth.test','Weekdays 8am-5pm','2025-12-31 07:32:00','2026-02-26 04:28:00'),('CG014','Mary Muthoni','1984-06-23','Female','CID0014','0712000014','mmuthoni@telehealth.test','Weekdays 8am-5pm','2025-12-06 13:15:00','2026-03-01 04:22:00'),('CG015','Beatrice Githinji','1998-02-19','Female','CID0015','0712000015','bgithinji@telehealth.test','Weekdays 8am-5pm','2025-12-23 22:34:00','2026-03-08 08:54:00'),('CG016','Kevin Sang','1997-01-24','Male','CID0016','0712000016','ksang@telehealth.test','Weekdays 8am-5pm','2025-12-20 20:58:00','2026-03-04 23:54:00'),('CG017','Irene Barasa','1996-10-16','Female','CID0017','0712000017','ibarasa@telehealth.test','Weekdays 8am-5pm','2025-12-20 00:56:00','2026-03-14 09:49:00'),('CG018','Naomi Njoroge','1995-09-15','Female','CID0018','0712000018','nnjoroge@telehealth.test','Weekdays 8am-5pm','2026-01-21 18:58:00','2026-03-14 01:29:00'),('CG019','Beatrice Mumo','1994-04-02','Female','CID0019','0712000019','bmumo@telehealth.test','Weekdays 8am-5pm','2025-12-29 07:15:00','2026-03-15 15:12:00'),('CG020','Mark Njoroge','1993-12-01','Male','CID0020','0712000020','mnjoroge@telehealth.test','Weekdays 8am-5pm','2026-02-02 06:05:00','2026-03-12 08:38:00');
/*!40000 ALTER TABLE `caregivers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chat_reads`
--

DROP TABLE IF EXISTS `chat_reads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_reads` (
  `ChatType` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ChatID` int NOT NULL,
  `UserID` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Role` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `LastReadAt` datetime NOT NULL,
  PRIMARY KEY (`ChatType`,`ChatID`,`UserID`),
  KEY `idx_chat_reads_time` (`LastReadAt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chat_reads`
--

LOCK TABLES `chat_reads` WRITE;
/*!40000 ALTER TABLE `chat_reads` DISABLE KEYS */;
INSERT INTO `chat_reads` VALUES ('Appointment',3,'ADM004','Labtech','2026-03-15 23:56:00'),('Appointment',6,'ADM007','Admin','2026-03-15 00:03:00'),('Appointment',9,'ADM010','Labtech','2026-03-13 00:38:00'),('Appointment',12,'ADM013','Admin','2026-03-06 08:57:00'),('Appointment',15,'ADM016','Labtech','2026-03-08 04:32:00'),('appointment',15,'DR015','Doctor','2026-03-20 19:50:18'),('Appointment',18,'ADM019','Admin','2026-03-12 06:08:00'),('appointment',41,'PT001','Patient','2026-03-20 19:57:28'),('Caregiver',1,'ADM002','Doctor','2026-03-11 03:49:00'),('Caregiver',4,'ADM005','Patient','2026-03-06 22:19:00'),('Caregiver',7,'ADM008','Doctor','2026-03-10 04:03:00'),('Caregiver',10,'ADM011','Patient','2026-03-14 03:51:00'),('Caregiver',13,'ADM014','Doctor','2026-03-08 06:46:00'),('Caregiver',16,'ADM017','Patient','2026-03-09 07:42:00'),('Caregiver',19,'ADM020','Doctor','2026-03-12 05:56:00'),('LabTest',2,'ADM003','Nurse','2026-03-08 18:08:00'),('LabTest',5,'ADM006','Caregiver','2026-03-14 06:06:00'),('LabTest',8,'ADM009','Nurse','2026-03-08 22:58:00'),('labtest',8,'LT008','Labtech','2026-03-20 20:31:17'),('LabTest',11,'ADM012','Caregiver','2026-03-14 11:44:00'),('LabTest',14,'ADM015','Nurse','2026-03-10 02:32:00'),('LabTest',17,'ADM018','Caregiver','2026-03-12 10:12:00'),('LabTest',20,'DR001','Nurse','2026-03-13 17:30:00');
/*!40000 ALTER TABLE `chat_reads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doctor_specializations`
--

DROP TABLE IF EXISTS `doctor_specializations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `doctor_specializations` (
  `DoctorSpecID` int NOT NULL AUTO_INCREMENT,
  `DoctorSpecName` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`DoctorSpecID`),
  UNIQUE KEY `DoctorSpecName` (`DoctorSpecName`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doctor_specializations`
--

LOCK TABLES `doctor_specializations` WRITE;
/*!40000 ALTER TABLE `doctor_specializations` DISABLE KEYS */;
INSERT INTO `doctor_specializations` VALUES (8,'Cardiology'),(7,'Dermatology'),(15,'Endocrinology'),(11,'ENT'),(2,'Family Medicine'),(16,'Gastroenterology'),(1,'General Medicine'),(3,'Internal Medicine'),(17,'Nephrology'),(9,'Neurology'),(5,'Obstetrics'),(14,'Oncology'),(12,'Ophthalmology'),(6,'Orthopedics'),(4,'Pediatrics'),(10,'Psychiatry'),(18,'Pulmonology'),(20,'Radiology'),(19,'Rheumatology'),(13,'Urology');
/*!40000 ALTER TABLE `doctor_specializations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doctors`
--

DROP TABLE IF EXISTS `doctors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `doctors` (
  `DoctorID` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `HospitalID` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `FName` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  `DOB` date NOT NULL,
  `Gender` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `NatID_PP` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `PhoneNum` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Email` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `LicenseNum` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Specialization` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ExperienceYears` int DEFAULT NULL,
  `CreatedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`DoctorID`),
  UNIQUE KEY `NatID_PP` (`NatID_PP`),
  UNIQUE KEY `PhoneNum` (`PhoneNum`),
  UNIQUE KEY `Email` (`Email`),
  UNIQUE KEY `LicenseNum` (`LicenseNum`),
  KEY `idx_doctors_hospital` (`HospitalID`),
  KEY `Specialization` (`Specialization`),
  CONSTRAINT `doctors_ibfk_1` FOREIGN KEY (`DoctorID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `doctors_ibfk_2` FOREIGN KEY (`HospitalID`) REFERENCES `hospitals` (`HospitalID`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `doctors_ibfk_3` FOREIGN KEY (`Specialization`) REFERENCES `doctor_specializations` (`DoctorSpecName`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doctors`
--

LOCK TABLES `doctors` WRITE;
/*!40000 ALTER TABLE `doctors` DISABLE KEYS */;
INSERT INTO `doctors` VALUES ('DR001','HOS001','Dr. Agnes Barasa','1995-09-20','Female','DID0001','0744000001','abarasa@telehealth.test','LIC00001','General Medicine',4,'2025-11-25 19:19:00','2026-03-15 05:28:00'),('DR002','HOS002','Dr. Jane Korir','1994-01-21','Female','DID0002','0744000002','jkorir@telehealth.test','LIC00002','General Medicine',5,'2025-11-10 08:38:00','2026-03-02 07:32:00'),('DR003','HOS003','Dr. Felix Muthoni','1993-02-26','Male','DID0003','0744000003','fmuthoni@telehealth.test','LIC00003','General Medicine',6,'2025-11-05 05:34:00','2026-03-04 21:42:00'),('DR004','HOS004','Dr. Caleb Moraa','1992-12-24','Male','DID0004','0744000004','cmoraa@telehealth.test','LIC00004','General Medicine',7,'2025-12-13 08:44:00','2026-03-09 12:36:00'),('DR005','HOS005','Dr. Dennis Muthoni','1991-04-27','Male','DID0005','0744000005','dmuthoni@telehealth.test','LIC00005','General Medicine',8,'2025-11-25 09:01:00','2026-03-04 08:01:00'),('DR006','HOS006','Dr. Helen Moraa','1990-03-16','Female','DID0006','0744000006','hmoraa@telehealth.test','LIC00006','General Medicine',9,'2025-11-01 03:11:00','2026-03-03 18:31:00'),('DR007','HOS007','Dr. Paul Barasa','1989-05-17','Male','DID0007','0744000007','pbarasa@telehealth.test','LIC00007','General Medicine',10,'2025-12-10 00:46:00','2026-02-22 16:33:00'),('DR008','HOS008','Dr. Stella Barasa','1988-12-19','Female','DID0008','0744000008','sbarasa@telehealth.test','LIC00008','Family Medicine',11,'2025-11-30 18:24:00','2026-03-10 06:11:00'),('DR009','HOS009','Dr. Felix Nduta','1987-02-16','Male','DID0009','0744000009','fnduta@telehealth.test','LIC00009','Family Medicine',12,'2025-11-14 11:36:00','2026-03-02 04:41:00'),('DR010','HOS010','Dr. James Wanjiru','1986-08-11','Male','DID0010','0744000010','jwanjiru@telehealth.test','LIC00010','Family Medicine',13,'2025-11-08 02:55:00','2026-03-09 20:36:00'),('DR011','HOS011','Dr. Peter Njoroge','1985-06-17','Male','DID0011','0744000011','pnjoroge@telehealth.test','LIC00011','Family Medicine',14,'2026-01-08 11:36:00','2026-02-27 10:57:00'),('DR012','HOS012','Dr. Esther Mwangi','1984-06-22','Female','DID0012','0744000012','emwangi@telehealth.test','LIC00012','Family Medicine',15,'2025-12-31 00:26:00','2026-02-20 16:04:00'),('DR013','HOS013','Dr. Michael Kamau','1983-10-27','Male','DID0013','0744000013','mkamau@telehealth.test','LIC00013','Internal Medicine',16,'2025-10-31 02:35:00','2026-03-10 14:08:00'),('DR014','HOS014','Dr. Patrick Moraa','1982-11-17','Male','DID0014','0744000014','pmoraa@telehealth.test','LIC00014','Pediatrics',17,'2026-01-09 03:21:00','2026-03-09 10:57:00'),('DR015','HOS015','Dr. Beatrice Cheruiyot','1996-05-13','Female','DID0015','0744000015','bcheruiyot@telehealth.test','LIC00015','Obstetrics',3,'2025-12-14 19:27:00','2026-03-11 19:20:00'),('DR016','HOS016','Dr. John Njoroge','1995-01-21','Male','DID0016','0744000016','jnjoroge@telehealth.test','LIC00016','Orthopedics',4,'2025-12-07 11:47:00','2026-03-09 00:02:00'),('DR017','HOS017','Dr. Timothy Omondi','1994-09-24','Male','DID0017','0744000017','tomondi@telehealth.test','LIC00017','Dermatology',5,'2026-01-07 16:24:00','2026-03-11 01:37:00'),('DR018','HOS018','Dr. Felix Wairimu','1993-06-08','Male','DID0018','0744000018','fwairimu@telehealth.test','LIC00018','Cardiology',6,'2025-12-13 08:17:00','2026-02-20 02:51:00'),('DR019','HOS019','Dr. Irene Ochieng','1992-10-21','Female','DID0019','0744000019','iochieng@telehealth.test','LIC00019','Neurology',7,'2025-11-24 08:43:00','2026-03-07 18:54:00'),('DR020','HOS020','Dr. Helen Muthoni','1991-03-20','Female','DID0020','0744000020','hmuthoni@telehealth.test','LIC00020','Psychiatry',8,'2025-11-08 10:43:00','2026-02-20 13:38:00');
/*!40000 ALTER TABLE `doctors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `documents`
--

DROP TABLE IF EXISTS `documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `documents` (
  `DocID` int NOT NULL AUTO_INCREMENT,
  `PatientID` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `CaregiverID` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `UploaderRole` enum('Patient','Caregiver') COLLATE utf8mb4_unicode_ci NOT NULL,
  `FileName` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `FilePath` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `UploadedAt` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`DocID`),
  KEY `idx_documents_patient` (`PatientID`),
  KEY `idx_documents_caregiver` (`CaregiverID`),
  CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`PatientID`) REFERENCES `patients` (`PatientID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `documents_ibfk_2` FOREIGN KEY (`CaregiverID`) REFERENCES `caregivers` (`CaregiverID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documents`
--

LOCK TABLES `documents` WRITE;
/*!40000 ALTER TABLE `documents` DISABLE KEYS */;
INSERT INTO `documents` VALUES (1,'PT001',NULL,'Patient','PT001_medical_summary_2026-05-04.pdf','../uploads/medical_docs/PT001_medical_summary_2026-05-04.pdf','2026-02-14 01:57:00'),(2,'PT002',NULL,'Patient','PT002_medical_summary_2026-05-04.pdf','../uploads/medical_docs/PT002_medical_summary_2026-05-04.pdf','2026-02-19 14:05:00'),(3,'PT003','CG003','Caregiver','PT003_caregiver_note_2026-05-04.pdf','../uploads/caregiver_docs/PT003_caregiver_note_2026-05-04.pdf','2026-03-02 01:49:00'),(4,'PT004',NULL,'Patient','PT004_medical_summary_2026-05-04.pdf','../uploads/medical_docs/PT004_medical_summary_2026-05-04.pdf','2026-03-02 05:51:00'),(5,'PT005',NULL,'Patient','PT005_medical_summary_2026-05-04.pdf','../uploads/medical_docs/PT005_medical_summary_2026-05-04.pdf','2026-03-05 10:23:00'),(6,'PT006','CG006','Caregiver','PT006_caregiver_note_2026-05-04.pdf','../uploads/caregiver_docs/PT006_caregiver_note_2026-05-04.pdf','2026-02-15 03:10:00'),(7,'PT007',NULL,'Patient','PT007_medical_summary_2026-05-04.pdf','../uploads/medical_docs/PT007_medical_summary_2026-05-04.pdf','2026-03-06 21:59:00'),(8,'PT008',NULL,'Patient','PT008_medical_summary_2026-05-04.pdf','../uploads/medical_docs/PT008_medical_summary_2026-05-04.pdf','2026-02-23 20:05:00'),(9,'PT009','CG009','Caregiver','PT009_caregiver_note_2026-05-04.pdf','../uploads/caregiver_docs/PT009_caregiver_note_2026-05-04.pdf','2026-03-11 05:02:00'),(10,'PT010',NULL,'Patient','PT010_medical_summary_2026-05-04.pdf','../uploads/medical_docs/PT010_medical_summary_2026-05-04.pdf','2026-03-03 03:20:00'),(11,'PT011',NULL,'Patient','PT011_medical_summary_2026-05-04.pdf','../uploads/medical_docs/PT011_medical_summary_2026-05-04.pdf','2026-03-07 03:08:00'),(12,'PT012','CG012','Caregiver','PT012_caregiver_note_2026-05-04.pdf','../uploads/caregiver_docs/PT012_caregiver_note_2026-05-04.pdf','2026-03-01 19:26:00'),(13,'PT013',NULL,'Patient','PT013_medical_summary_2026-05-04.pdf','../uploads/medical_docs/PT013_medical_summary_2026-05-04.pdf','2026-03-06 20:18:00'),(14,'PT014',NULL,'Patient','PT014_medical_summary_2026-05-04.pdf','../uploads/medical_docs/PT014_medical_summary_2026-05-04.pdf','2026-03-06 12:47:00'),(15,'PT015','CG015','Caregiver','PT015_caregiver_note_2026-05-04.pdf','../uploads/caregiver_docs/PT015_caregiver_note_2026-05-04.pdf','2026-02-18 22:34:00'),(16,'PT016',NULL,'Patient','PT016_medical_summary_2026-05-04.pdf','../uploads/medical_docs/PT016_medical_summary_2026-05-04.pdf','2026-02-24 21:28:00'),(17,'PT017',NULL,'Patient','PT017_medical_summary_2026-05-04.pdf','../uploads/medical_docs/PT017_medical_summary_2026-05-04.pdf','2026-03-04 14:10:00'),(18,'PT018','CG018','Caregiver','PT018_caregiver_note_2026-05-04.pdf','../uploads/caregiver_docs/PT018_caregiver_note_2026-05-04.pdf','2026-02-28 10:08:00'),(19,'PT019',NULL,'Patient','PT019_medical_summary_2026-05-04.pdf','../uploads/medical_docs/PT019_medical_summary_2026-05-04.pdf','2026-02-25 03:07:00'),(20,'PT020',NULL,'Patient','PT020_medical_summary_2026-05-04.pdf','../uploads/medical_docs/PT020_medical_summary_2026-05-04.pdf','2026-02-19 12:16:00');
/*!40000 ALTER TABLE `documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `feedback`
--

DROP TABLE IF EXISTS `feedback`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `feedback` (
  `FeedbackID` int NOT NULL AUTO_INCREMENT,
  `UserID` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Feedback` text COLLATE utf8mb4_unicode_ci,
  `FBDate` datetime DEFAULT CURRENT_TIMESTAMP,
  `Reply` text COLLATE utf8mb4_unicode_ci,
  `ReplyDate` datetime DEFAULT NULL,
  PRIMARY KEY (`FeedbackID`),
  KEY `idx_feedback_user` (`UserID`),
  CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `feedback`
--

LOCK TABLES `feedback` WRITE;
/*!40000 ALTER TABLE `feedback` DISABLE KEYS */;
INSERT INTO `feedback` VALUES (1,'PT001','Service was good.','2026-03-01 20:58:00','Thank you for your feedback','2026-03-07 18:08:00'),(2,'PT002','Service was good.','2026-02-27 22:44:00','Thank you for your feedback','2026-03-07 07:54:00'),(3,'PT003','Service was good.','2026-02-23 09:32:00',NULL,NULL),(4,'PT004','Service was good.','2026-02-16 10:53:00','Thank you for your feedback','2026-03-03 22:39:00'),(5,'PT005','Service was good.','2026-02-18 12:59:00','Thank you for your feedback','2026-03-08 03:37:00'),(6,'PT006','Service was good.','2026-02-07 20:57:00',NULL,NULL),(7,'PT007','Service was good.','2026-02-25 19:34:00','Thank you for your feedback','2026-03-02 00:34:00'),(8,'PT008','Service was good.','2026-02-12 10:51:00','Thank you for your feedback','2026-03-09 22:22:00'),(9,'PT009','Service was good.','2026-03-02 17:35:00',NULL,NULL),(10,'PT010','Service was good.','2026-02-25 08:25:00','Thank you for your feedback','2026-03-08 12:36:00'),(11,'PT011','Service was good.','2026-02-25 20:21:00','Thank you for your feedback','2026-03-14 13:43:00'),(12,'PT012','Service was good.','2026-02-11 11:57:00',NULL,NULL),(13,'PT013','Service was good.','2026-03-02 21:28:00','Thank you for your feedback','2026-03-10 03:19:00'),(14,'PT014','Service was good.','2026-02-17 14:09:00','Thank you for your feedback','2026-03-03 09:44:00'),(15,'PT015','Service was good.','2026-02-10 15:06:00',NULL,NULL),(16,'PT016','Service was good.','2026-02-10 16:20:00','Thank you for your feedback','2026-03-12 12:15:00'),(17,'PT017','Service was good.','2026-02-06 09:14:00','Thank you for your feedback','2026-03-06 01:48:00'),(18,'PT018','Service was good.','2026-03-09 17:27:00','thanks','2026-03-20 20:55:05'),(19,'PT019','Service was good.','2026-02-11 00:21:00','Thank you for your feedback','2026-03-03 02:51:00'),(20,'PT020','Service was good.','2026-03-04 13:33:00','Thank you for your feedback','2026-03-14 00:36:00');
/*!40000 ALTER TABLE `feedback` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `history`
--

DROP TABLE IF EXISTS `history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `history` (
  `HistoryID` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `PatientID` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Allergens` text COLLATE utf8mb4_unicode_ci,
  `MajorSurgeries` text COLLATE utf8mb4_unicode_ci,
  `ChronicConditions` text COLLATE utf8mb4_unicode_ci,
  `LongTermMedications` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`HistoryID`),
  KEY `idx_history_patient` (`PatientID`),
  CONSTRAINT `history_ibfk_1` FOREIGN KEY (`PatientID`) REFERENCES `patients` (`PatientID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `history`
--

LOCK TABLES `history` WRITE;
/*!40000 ALTER TABLE `history` DISABLE KEYS */;
INSERT INTO `history` VALUES ('HIS001','PT001','Dust','Appendectomy','Diabetes','Metformin'),('HIS002','PT002','None','Appendectomy','Asthma','Salbutamol'),('HIS003','PT003','Penicillin','Appendectomy','None','None'),('HIS004','PT004','Peanuts','Appendectomy','Arthritis','Ibuprofen'),('HIS005','PT005','Pollen','Appendectomy','Hypertension','Amlodipine'),('HIS006','PT006','Dust','Appendectomy','Diabetes','Metformin'),('HIS007','PT007','None','Appendectomy','Asthma','Salbutamol'),('HIS008','PT008','Penicillin','Appendectomy','None','None'),('HIS009','PT009','Peanuts','Appendectomy','Arthritis','Ibuprofen'),('HIS010','PT010','Pollen','Appendectomy','Hypertension','Amlodipine'),('HIS011','PT011','Dust','Appendectomy','Diabetes','Metformin'),('HIS012','PT012','None','Appendectomy','Asthma','Salbutamol'),('HIS013','PT013','Penicillin','Appendectomy','None','None'),('HIS014','PT014','Peanuts','Appendectomy','Arthritis','Ibuprofen'),('HIS015','PT015','Pollen','Appendectomy','Hypertension','Amlodipine'),('HIS016','PT016','Dust','Appendectomy','Diabetes','Metformin'),('HIS017','PT017','None','Appendectomy','Asthma','Salbutamol'),('HIS018','PT018','Penicillin','Appendectomy','None','None'),('HIS019','PT019','Peanuts','Appendectomy','Arthritis','Ibuprofen'),('HIS020','PT020','Pollen','Appendectomy','Hypertension','Amlodipine');
/*!40000 ALTER TABLE `history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hospitals`
--

DROP TABLE IF EXISTS `hospitals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hospitals` (
  `HospitalID` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Location` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  `HospitalName` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `KMPDCLicense` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `CreatedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`HospitalID`),
  UNIQUE KEY `HospitalName` (`HospitalName`),
  UNIQUE KEY `KMPDCLicense` (`KMPDCLicense`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hospitals`
--

LOCK TABLES `hospitals` WRITE;
/*!40000 ALTER TABLE `hospitals` DISABLE KEYS */;
INSERT INTO `hospitals` VALUES ('HOS001','Nairobi County','Nairobi General Hospital','KMPDC00001','2024-03-04 11:25:00','2026-02-08 11:01:00'),('HOS002','Mombasa County','Mombasa General Hospital','KMPDC00002','2025-01-15 18:17:00','2026-03-10 04:43:00'),('HOS003','Kisumu County','Kisumu General Hospital','KMPDC00003','2024-10-14 19:45:00','2026-01-24 23:56:00'),('HOS004','Nakuru County','Nakuru General Hospital','KMPDC00004','2024-01-25 22:56:00','2026-02-22 09:45:00'),('HOS005','Eldoret County','Eldoret General Hospital','KMPDC00005','2024-10-27 22:17:00','2026-02-05 16:14:00'),('HOS006','Thika County','Thika General Hospital','KMPDC00006','2025-01-31 12:18:00','2026-01-19 03:19:00'),('HOS007','Machakos County','Machakos General Hospital','KMPDC00007','2025-04-28 23:10:00','2026-02-20 01:11:00'),('HOS008','Kisii County','Kisii General Hospital','KMPDC00008','2023-12-02 23:17:00','2026-01-24 05:40:00'),('HOS009','Nyeri County','Nyeri General Hospital','KMPDC00009','2024-03-30 17:53:00','2026-02-04 00:16:00'),('HOS010','Meru County','Meru General Hospital','KMPDC00010','2024-02-26 05:57:00','2026-03-07 17:28:00'),('HOS011','Naivasha County','Naivasha General Hospital','KMPDC00011','2024-02-06 02:49:00','2026-01-25 18:10:00'),('HOS012','Kericho County','Kericho General Hospital','KMPDC00012','2024-03-11 07:36:00','2026-01-27 02:23:00'),('HOS013','Malindi County','Malindi General Hospital','KMPDC00013','2024-10-22 08:28:00','2026-02-15 20:06:00'),('HOS014','Kitale County','Kitale General Hospital','KMPDC00014','2025-05-05 16:07:00','2026-03-15 06:35:00'),('HOS015','Garissa County','Garissa General Hospital','KMPDC00015','2024-02-27 14:26:00','2026-03-09 06:11:00'),('HOS016','Embu County','Embu General Hospital','KMPDC00016','2024-04-03 14:24:00','2026-02-28 09:07:00'),('HOS017','Bungoma County','Bungoma General Hospital','KMPDC00017','2024-06-06 07:32:00','2026-03-10 10:28:00'),('HOS018','Kakamega County','Kakamega General Hospital','KMPDC00018','2024-05-21 01:30:00','2026-03-11 03:38:00'),('HOS019','Voi County','Voi General Hospital','KMPDC00019','2025-02-19 18:01:00','2026-01-24 03:57:00'),('HOS020','Narok County','Narok General Hospital','KMPDC00020','2024-12-15 21:40:00','2026-02-27 12:29:00');
/*!40000 ALTER TABLE `hospitals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lab_test_messages`
--

DROP TABLE IF EXISTS `lab_test_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lab_test_messages` (
  `MessageID` int NOT NULL AUTO_INCREMENT,
  `LabTestID` int NOT NULL,
  `SenderRole` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `SenderID` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `MsgDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`MessageID`),
  KEY `idx_labtest_msg_test` (`LabTestID`),
  KEY `idx_labtest_msg_sender` (`SenderID`),
  CONSTRAINT `lab_test_messages_ibfk_1` FOREIGN KEY (`LabTestID`) REFERENCES `lab_tests` (`LabTestID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lab_test_messages`
--

LOCK TABLES `lab_test_messages` WRITE;
/*!40000 ALTER TABLE `lab_test_messages` DISABLE KEYS */;
INSERT INTO `lab_test_messages` VALUES (1,1,'Labtech','LT001','Sample received and processing.','2026-02-24 00:44:00'),(2,2,'Labtech','LT002','Sample received and processing.','2026-03-07 02:08:00'),(3,3,'Labtech','LT003','Sample received and processing.','2026-03-07 02:33:00'),(4,4,'Labtech','LT004','Sample received and processing.','2026-02-27 20:09:00'),(5,5,'Labtech','LT005','Sample received and processing.','2026-03-06 17:05:00'),(6,6,'Labtech','LT006','Sample received and processing.','2026-03-13 09:31:00'),(7,7,'Labtech','LT007','Sample received and processing.','2026-03-07 08:43:00'),(8,8,'Labtech','LT008','Sample received and processing.','2026-03-13 11:35:00'),(9,9,'Labtech','LT009','Sample received and processing.','2026-03-01 20:47:00'),(10,10,'Labtech','LT010','Sample received and processing.','2026-02-27 05:08:00'),(11,11,'Labtech','LT011','Sample received and processing.','2026-03-12 00:54:00'),(12,12,'Labtech','LT012','Sample received and processing.','2026-03-02 17:39:00'),(13,13,'Labtech','LT013','Sample received and processing.','2026-02-28 05:16:00'),(14,14,'Labtech','LT014','Sample received and processing.','2026-03-10 21:12:00'),(15,15,'Labtech','LT015','Sample received and processing.','2026-03-04 00:11:00'),(16,16,'Labtech','LT016','Sample received and processing.','2026-03-04 06:30:00'),(17,17,'Labtech','LT017','Sample received and processing.','2026-03-10 03:42:00'),(18,18,'Labtech','LT018','Sample received and processing.','2026-03-04 13:11:00'),(19,19,'Labtech','LT019','Sample received and processing.','2026-02-25 23:24:00'),(20,20,'Labtech','LT020','Sample received and processing.','2026-03-15 14:49:00');
/*!40000 ALTER TABLE `lab_test_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lab_tests`
--

DROP TABLE IF EXISTS `lab_tests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lab_tests` (
  `LabTestID` int NOT NULL AUTO_INCREMENT,
  `PatientID` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `LabID` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `TestName` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Status` enum('Pending','Processing','Completed') COLLATE utf8mb4_unicode_ci DEFAULT 'Pending',
  `Result` text COLLATE utf8mb4_unicode_ci,
  `TestDate` datetime DEFAULT CURRENT_TIMESTAMP,
  `ResultDate` datetime DEFAULT NULL,
  `PatientApprovalStatus` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Accepted',
  `RequestedByDoctorID` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `RequestNote` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`LabTestID`),
  KEY `idx_lab_tests_patient` (`PatientID`),
  KEY `idx_lab_tests_lab` (`LabID`),
  CONSTRAINT `lab_tests_ibfk_1` FOREIGN KEY (`PatientID`) REFERENCES `patients` (`PatientID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `lab_tests_ibfk_2` FOREIGN KEY (`LabID`) REFERENCES `laboratories` (`LabID`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lab_tests`
--

LOCK TABLES `lab_tests` WRITE;
/*!40000 ALTER TABLE `lab_tests` DISABLE KEYS */;
INSERT INTO `lab_tests` VALUES (1,'PT001','LABS001','Urinanalysis','Completed','../uploads/lab_results/LT001_urinanalysis_report_2026-05-04.pdf','2026-02-15 14:52:00','2026-03-06 00:55:00','Accepted',NULL,NULL),(2,'PT002','LABS002','Lipid Panel','Completed','../uploads/lab_results/LT002_lipid_panel_report_2026-05-04.pdf','2026-03-09 02:29:00','2026-03-07 01:15:00','Accepted',NULL,NULL),(3,'PT003','LABS003','HbA1c','Completed','../uploads/lab_results/LT003_hba1c_report_2026-05-04.pdf','2026-03-06 13:57:00','2026-03-07 21:50:00','Accepted',NULL,NULL),(4,'PT004','LABS004','Malaria Smear','Completed','../uploads/lab_results/LT004_malaria_smear_report_2026-05-04.pdf','2026-02-08 19:20:00','2026-03-08 20:17:00','Accepted',NULL,NULL),(5,'PT005','LABS005','HIV Rapid Test','Completed','../uploads/lab_results/LT005_hiv_rapid_test_report_2026-05-04.pdf','2026-02-22 03:26:00','2026-03-09 00:14:00','Accepted',NULL,NULL),(6,'PT006','LABS006','Liver Function Test','Completed','../uploads/lab_results/LT006_liver_function_test_report_2026-05-04.pdf','2026-02-09 15:22:00','2026-03-10 14:26:00','Accepted',NULL,NULL),(7,'PT007','LABS007','Renal Panel','Completed','../uploads/lab_results/LT007_renal_panel_report_2026-05-04.pdf','2026-03-08 09:13:00','2026-03-07 03:04:00','Accepted',NULL,NULL),(8,'PT008','LABS008','Thyroid Panel','Completed','../uploads/lab_results/LT008_thyroid_panel_report_2026-05-04.pdf','2026-02-24 12:41:00','2026-03-07 20:45:00','Accepted',NULL,NULL),(9,'PT009','LABS009','Stool Analysis','Processing',NULL,'2026-02-23 15:16:00',NULL,'Accepted',NULL,NULL),(10,'PT010','LABS010','Full Blood Count','Processing',NULL,'2026-02-27 21:14:00',NULL,'Accepted',NULL,NULL),(11,'PT011','LABS011','Urinanalysis','Processing',NULL,'2026-03-09 10:31:00',NULL,'Accepted',NULL,NULL),(12,'PT012','LABS012','Lipid Panel','Processing',NULL,'2026-03-10 14:10:00',NULL,'Accepted',NULL,NULL),(13,'PT013','LABS013','HbA1c','Processing',NULL,'2026-03-03 02:52:00',NULL,'Accepted',NULL,NULL),(14,'PT014','LABS014','Malaria Smear','Processing',NULL,'2026-02-20 17:33:00',NULL,'Accepted',NULL,NULL),(15,'PT015','LABS015','HIV Rapid Test','Pending',NULL,'2026-02-11 03:30:00',NULL,'Accepted',NULL,NULL),(16,'PT016','LABS016','Liver Function Test','Pending',NULL,'2026-02-07 17:28:00',NULL,'Accepted',NULL,NULL),(17,'PT017','LABS017','Renal Panel','Pending',NULL,'2026-03-10 06:46:00',NULL,'Accepted',NULL,NULL),(18,'PT018','LABS018','Thyroid Panel','Pending',NULL,'2026-03-09 00:41:00',NULL,'Accepted',NULL,NULL),(19,'PT019','LABS019','Stool Analysis','Pending',NULL,'2026-02-17 13:07:00',NULL,'Accepted',NULL,NULL),(20,'PT020','LABS020','Full Blood Count','Pending',NULL,'2026-03-01 18:55:00',NULL,'Accepted',NULL,NULL);
/*!40000 ALTER TABLE `lab_tests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `laboratories`
--

DROP TABLE IF EXISTS `laboratories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `laboratories` (
  `LabID` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `LabName` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `LabLocation` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `CreatedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`LabID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `laboratories`
--

LOCK TABLES `laboratories` WRITE;
/*!40000 ALTER TABLE `laboratories` DISABLE KEYS */;
INSERT INTO `laboratories` VALUES ('LABS001','Machakos Diagnostic Lab','Machakos CBD','2024-09-24 18:58:00','2026-02-24 08:09:00'),('LABS002','Kisii Diagnostic Lab','Kisii CBD','2024-05-27 07:34:00','2026-02-23 17:50:00'),('LABS003','Nyeri Diagnostic Lab','Nyeri CBD','2024-04-29 23:28:00','2026-02-25 00:12:00'),('LABS004','Meru Diagnostic Lab','Meru CBD','2024-05-02 10:23:00','2026-02-03 09:43:00'),('LABS005','Naivasha Diagnostic Lab','Naivasha CBD','2024-11-08 06:40:00','2026-02-24 22:17:00'),('LABS006','Kericho Diagnostic Lab','Kericho CBD','2024-05-31 09:43:00','2026-03-01 01:24:00'),('LABS007','Malindi Diagnostic Lab','Malindi CBD','2025-03-28 09:59:00','2026-03-13 22:13:00'),('LABS008','Kitale Diagnostic Lab','Kitale CBD','2025-07-31 07:43:00','2026-02-24 06:26:00'),('LABS009','Garissa Diagnostic Lab','Garissa CBD','2024-12-30 12:01:00','2026-02-16 18:55:00'),('LABS010','Embu Diagnostic Lab','Embu CBD','2024-11-24 14:22:00','2026-02-12 15:57:00'),('LABS011','Bungoma Diagnostic Lab','Bungoma CBD','2024-06-15 05:30:00','2026-01-18 16:24:00'),('LABS012','Kakamega Diagnostic Lab','Kakamega CBD','2025-03-18 19:51:00','2026-02-28 18:35:00'),('LABS013','Voi Diagnostic Lab','Voi CBD','2025-01-13 01:38:00','2026-02-21 01:13:00'),('LABS014','Narok Diagnostic Lab','Narok CBD','2025-01-08 22:26:00','2026-01-19 05:02:00'),('LABS015','Nairobi Diagnostic Lab','Nairobi CBD','2024-08-29 06:50:00','2026-02-08 21:23:00'),('LABS016','Mombasa Diagnostic Lab','Mombasa CBD','2024-06-29 22:30:00','2026-02-23 13:29:00'),('LABS017','Kisumu Diagnostic Lab','Kisumu CBD','2025-08-24 18:30:00','2026-01-19 09:49:00'),('LABS018','Nakuru Diagnostic Lab','Nakuru CBD','2025-08-09 18:59:00','2026-02-15 07:25:00'),('LABS019','Eldoret Diagnostic Lab','Eldoret CBD','2024-09-10 23:24:00','2026-03-14 20:45:00'),('LABS020','Thika Diagnostic Lab','Thika CBD','2025-08-18 08:45:00','2026-03-04 20:54:00');
/*!40000 ALTER TABLE `laboratories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `labtechs`
--

DROP TABLE IF EXISTS `labtechs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `labtechs` (
  `LabTechID` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `LabID` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `FName` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  `DOB` date NOT NULL,
  `Gender` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `NatID_PP` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `PhoneNum` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Email` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `KMLTTB_License` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `CreatedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`LabTechID`),
  UNIQUE KEY `NatID_PP` (`NatID_PP`),
  UNIQUE KEY `PhoneNum` (`PhoneNum`),
  UNIQUE KEY `Email` (`Email`),
  UNIQUE KEY `KMLTTB_License` (`KMLTTB_License`),
  KEY `idx_labtechs_lab` (`LabID`),
  CONSTRAINT `labtechs_ibfk_1` FOREIGN KEY (`LabTechID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `labtechs_ibfk_2` FOREIGN KEY (`LabID`) REFERENCES `laboratories` (`LabID`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `labtechs`
--

LOCK TABLES `labtechs` WRITE;
/*!40000 ALTER TABLE `labtechs` DISABLE KEYS */;
INSERT INTO `labtechs` VALUES ('LT001','LABS001','Helen Kiptoo','2000-05-26','Female','LID0001','0766000001','hkiptoo@telehealth.test','KML00001','2025-12-20 13:22:00','2026-03-13 23:03:00'),('LT002','LABS002','Irene Wanjiru','1999-12-24','Female','LID0002','0766000002','iwanjiru@telehealth.test','KML00002','2025-12-12 19:22:00','2026-02-27 17:40:00'),('LT003','LABS003','Timothy Muthoni','1998-10-17','Male','LID0003','0766000003','tmuthoni@telehealth.test','KML00003','2025-11-28 06:20:00','2026-03-10 01:39:00'),('LT004','LABS004','Evan Muthoni','1997-01-17','Male','LID0004','0766000004','emuthoni@telehealth.test','KML00004','2026-01-21 08:00:00','2026-03-03 20:05:00'),('LT005','LABS005','Wendy Mwangi','1996-11-01','Female','LID0005','0766000005','wmwangi@telehealth.test','KML00005','2026-01-10 06:27:00','2026-03-14 03:12:00'),('LT006','LABS006','Felix Barasa','1995-02-11','Male','LID0006','0766000006','fbarasa@telehealth.test','KML00006','2025-12-12 01:47:00','2026-02-24 07:22:00'),('LT007','LABS007','Faith Mwangi','1994-08-01','Female','LID0007','0766000007','fmwangi@telehealth.test','KML00007','2025-11-26 17:57:00','2026-02-26 22:45:00'),('LT008','LABS008','Lilian Barasa','1993-01-14','Female','LID0008','0766000008','lbarasa@telehealth.test','KML00008','2026-01-07 00:52:00','2026-02-26 03:29:00'),('LT009','LABS009','Ruth Githinji','1992-11-03','Female','LID0009','0766000009','rgithinji@telehealth.test','KML00009','2026-01-18 04:55:00','2026-02-26 23:09:00'),('LT010','LABS010','Victor Wairimu','2001-10-11','Male','LID0010','0766000010','vwairimu@telehealth.test','KML00010','2026-01-23 23:04:00','2026-03-03 17:57:00'),('LT011','LABS011','Brian Wairimu','2000-11-18','Male','LID0011','0766000011','bwairimu@telehealth.test','KML00011','2025-12-07 17:06:00','2026-02-19 15:13:00'),('LT012','LABS012','Timothy Korir','1999-06-20','Male','LID0012','0766000012','tkorir@telehealth.test','KML00012','2025-12-02 11:47:00','2026-02-27 04:41:00'),('LT013','LABS013','Esther Kamau','1998-06-16','Female','LID0013','0766000013','ekamau@telehealth.test','KML00013','2026-01-11 10:21:00','2026-03-14 15:00:00'),('LT014','LABS014','Jane Kariuki','1997-03-10','Female','LID0014','0766000014','jkariuki@telehealth.test','KML00014','2026-01-19 00:21:00','2026-03-03 02:59:00'),('LT015','LABS015','Naomi Kiptoo','1996-01-21','Female','LID0015','0766000015','nkiptoo@telehealth.test','KML00015','2025-12-31 22:34:00','2026-03-12 04:39:00'),('LT016','LABS016','Alice Kamau','1995-05-11','Female','LID0016','0766000016','akamau@telehealth.test','KML00016','2026-01-22 15:34:00','2026-03-07 05:31:00'),('LT017','LABS017','Francis Njoroge','1994-09-08','Male','LID0017','0766000017','fnjoroge@telehealth.test','KML00017','2026-01-08 00:42:00','2026-03-07 20:33:00'),('LT018','LABS018','Ruth Kariuki','1993-08-12','Female','LID0018','0766000018','rkariuki@telehealth.test','KML00018','2026-01-15 14:03:00','2026-02-28 02:04:00'),('LT019','LABS019','Timothy Kariuki','1992-10-15','Male','LID0019','0766000019','tkariuki@telehealth.test','KML00019','2026-01-10 09:21:00','2026-02-23 22:00:00'),('LT020','LABS020','Ian Mwangi','2001-10-18','Male','LID0020','0766000020','imwangi@telehealth.test','KML00020','2025-12-26 17:03:00','2026-02-26 04:56:00');
/*!40000 ALTER TABLE `labtechs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `login_logs`
--

DROP TABLE IF EXISTS `login_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `login_logs` (
  `LogID` int NOT NULL AUTO_INCREMENT,
  `UserID` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Role` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `SessionID` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `LoginAt` datetime NOT NULL,
  `LogoutAt` datetime DEFAULT NULL,
  `SessionMinutes` int DEFAULT NULL,
  `IPAddress` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`LogID`),
  KEY `idx_login_logs_user` (`UserID`),
  KEY `idx_login_logs_session` (`SessionID`),
  KEY `idx_login_logs_login` (`LoginAt`),
  CONSTRAINT `login_logs_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login_logs`
--

LOCK TABLES `login_logs` WRITE;
/*!40000 ALTER TABLE `login_logs` DISABLE KEYS */;
INSERT INTO `login_logs` VALUES (1,'ADM002','Doctor','SID00001','2026-02-25 23:30:00','2026-03-13 08:11:00',31,'192.168.1.1'),(2,'ADM003','Nurse','SID00002','2026-03-09 23:26:00','2026-03-15 09:34:00',32,'192.168.1.2'),(3,'ADM004','Labtech','SID00003','2026-03-11 13:30:00','2026-03-16 17:57:00',33,'192.168.1.3'),(4,'ADM005','Patient','SID00004','2026-03-06 08:03:00',NULL,34,'192.168.1.4'),(5,'ADM006','Caregiver','SID00005','2026-03-10 23:49:00','2026-03-15 03:48:00',35,'192.168.1.5'),(6,'ADM007','Admin','SID00006','2026-03-05 02:31:00','2026-03-16 15:15:00',36,'192.168.1.6'),(7,'ADM008','Doctor','SID00007','2026-02-24 08:22:00','2026-03-13 11:47:00',37,'192.168.1.7'),(8,'ADM009','Nurse','SID00008','2026-03-04 13:48:00',NULL,38,'192.168.1.8'),(9,'ADM010','Labtech','SID00009','2026-03-05 13:09:00','2026-03-12 22:01:00',39,'192.168.1.9'),(10,'ADM011','Patient','SID00010','2026-03-13 08:28:00','2026-03-12 20:29:00',40,'192.168.1.10'),(11,'ADM012','Caregiver','SID00011','2026-03-15 07:31:00','2026-03-16 00:21:00',41,'192.168.1.11'),(12,'ADM013','Admin','SID00012','2026-02-25 15:30:00',NULL,42,'192.168.1.12'),(13,'ADM014','Doctor','SID00013','2026-03-04 01:46:00','2026-03-15 23:21:00',43,'192.168.1.13'),(14,'ADM015','Nurse','SID00014','2026-03-06 05:25:00','2026-03-14 05:46:00',44,'192.168.1.14'),(15,'ADM016','Labtech','SID00015','2026-03-10 21:55:00','2026-03-12 22:06:00',45,'192.168.1.15'),(16,'ADM017','Patient','SID00016','2026-02-26 19:59:00',NULL,46,'192.168.1.16'),(17,'ADM018','Caregiver','SID00017','2026-03-09 07:49:00','2026-03-12 02:54:00',47,'192.168.1.17'),(18,'ADM019','Admin','SID00018','2026-03-06 15:01:00','2026-03-12 18:21:00',48,'192.168.1.18'),(19,'ADM020','Doctor','SID00019','2026-03-15 15:28:00','2026-03-14 12:55:00',49,'192.168.1.19'),(20,'DR001','Nurse','SID00020','2026-03-13 22:04:00',NULL,50,'192.168.1.20'),(21,'PT021','Patient','lo99qkv76ns8kp3p579u1ini04','2026-03-20 19:05:40','2026-03-20 19:35:46',30,'::1'),(22,'PT001','Patient','lo99qkv76ns8kp3p579u1ini04','2026-03-20 19:36:23','2026-03-20 19:39:13',2,'::1'),(23,'CG011','Caregiver','lo99qkv76ns8kp3p579u1ini04','2026-03-20 19:39:41','2026-03-20 19:43:05',3,'::1'),(24,'DR015','Doctor','lo99qkv76ns8kp3p579u1ini04','2026-03-20 19:43:31','2026-03-20 19:53:57',10,'::1'),(25,'DR015','Doctor','lo99qkv76ns8kp3p579u1ini04','2026-03-20 19:54:12','2026-03-20 19:56:28',2,'::1'),(26,'PT001','Patient','lo99qkv76ns8kp3p579u1ini04','2026-03-20 19:57:01','2026-03-20 19:57:49',0,'::1'),(27,'NR001','Nurse','lo99qkv76ns8kp3p579u1ini04','2026-03-20 19:58:00','2026-03-20 19:59:06',1,'::1'),(28,'DR009','Doctor','lo99qkv76ns8kp3p579u1ini04','2026-03-20 19:59:16','2026-03-20 19:59:31',0,'::1'),(29,'PT001','Patient','lo99qkv76ns8kp3p579u1ini04','2026-03-20 19:59:52','2026-03-20 20:00:16',0,'::1'),(30,'DR009','Doctor','lo99qkv76ns8kp3p579u1ini04','2026-03-20 20:00:27','2026-03-20 20:02:44',2,'::1'),(31,'DR009','Doctor','lo99qkv76ns8kp3p579u1ini04','2026-03-20 20:02:56','2026-03-20 20:10:38',7,'::1'),(32,'NR007','Nurse','lo99qkv76ns8kp3p579u1ini04','2026-03-20 20:11:09','2026-03-20 20:30:11',19,'::1'),(33,'LT008','Labtech','lo99qkv76ns8kp3p579u1ini04','2026-03-20 20:30:40','2026-03-20 20:31:45',1,'::1'),(34,'ADM001','Admin','lo99qkv76ns8kp3p579u1ini04','2026-03-20 20:32:18','2026-03-25 19:29:22',7137,'::1'),(35,'ADM001','Admin','lo99qkv76ns8kp3p579u1ini04','2026-06-10 20:41:02','2026-06-10 20:45:42',4,'::1'),(36,'ADM001','Admin','lo99qkv76ns8kp3p579u1ini04','2026-06-15 16:27:29',NULL,NULL,'::1');
/*!40000 ALTER TABLE `login_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `messages` (
  `MessageID` int NOT NULL AUTO_INCREMENT,
  `AppID` int NOT NULL,
  `SenderRole` enum('Patient','Doctor','Nurse') COLLATE utf8mb4_unicode_ci NOT NULL,
  `SenderID` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `MsgDate` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`MessageID`),
  KEY `idx_messages_app` (`AppID`,`MsgDate`),
  CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`AppID`) REFERENCES `appointments` (`AppID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
INSERT INTO `messages` VALUES (1,1,'Patient','PT001','Hello doctor, I have a question about my visit.','2026-03-09 01:17:00'),(2,2,'Patient','PT002','Hello doctor, I have a question about my visit.','2026-02-25 22:57:00'),(3,3,'Patient','PT003','Hello doctor, I have a question about my visit.','2026-02-26 01:09:00'),(4,4,'Patient','PT004','Hello doctor, I have a question about my visit.','2026-03-08 11:23:00'),(5,5,'Patient','PT005','Hello doctor, I have a question about my visit.','2026-03-09 02:01:00'),(6,6,'Patient','PT006','Hello doctor, I have a question about my visit.','2026-03-10 05:01:00'),(7,7,'Patient','PT007','Hello doctor, I have a question about my visit.','2026-02-25 23:25:00'),(8,8,'Patient','PT008','Hello doctor, I have a question about my visit.','2026-03-11 20:49:00'),(9,9,'Patient','PT009','Hello doctor, I have a question about my visit.','2026-03-06 12:56:00'),(10,10,'Patient','PT010','Hello doctor, I have a question about my visit.','2026-03-09 05:47:00'),(11,11,'Patient','PT011','Hello doctor, I have a question about my visit.','2026-03-13 19:40:00'),(12,12,'Patient','PT012','Hello doctor, I have a question about my visit.','2026-02-27 03:48:00'),(13,13,'Patient','PT013','Hello doctor, I have a question about my visit.','2026-03-11 21:17:00'),(14,14,'Patient','PT014','Hello doctor, I have a question about my visit.','2026-03-15 00:17:00'),(15,15,'Patient','PT015','Hello doctor, I have a question about my visit.','2026-03-14 17:33:00'),(16,16,'Patient','PT016','Hello doctor, I have a question about my visit.','2026-03-05 18:26:00'),(17,17,'Patient','PT017','Hello doctor, I have a question about my visit.','2026-02-24 17:51:00'),(18,18,'Patient','PT018','Hello doctor, I have a question about my visit.','2026-03-07 06:32:00'),(19,19,'Patient','PT019','Hello doctor, I have a question about my visit.','2026-03-08 06:57:00'),(20,20,'Patient','PT020','Hello doctor, I have a question about my visit.','2026-02-27 01:04:00');
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `NotificationID` int NOT NULL AUTO_INCREMENT,
  `UserID` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Role` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Message` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `IsRead` tinyint(1) NOT NULL DEFAULT '0',
  `CreatedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`NotificationID`),
  KEY `idx_notifications_user` (`UserID`,`Role`,`IsRead`,`CreatedAt`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (1,'ADM002','Doctor','System Update','You have a new notification','/notifications.php',0,'2026-03-14 17:34:00'),(2,'ADM003','Nurse','System Update','You have a new notification','/notifications.php',0,'2026-02-25 14:16:00'),(3,'ADM004','Labtech','System Update','You have a new notification','/notifications.php',1,'2026-02-27 22:23:00'),(4,'ADM005','Patient','System Update','You have a new notification','/notifications.php',0,'2026-02-01 20:45:00'),(5,'ADM006','Caregiver','System Update','You have a new notification','/notifications.php',0,'2026-03-07 15:42:00'),(6,'ADM007','Admin','System Update','You have a new notification','/notifications.php',1,'2026-03-02 02:31:00'),(7,'ADM008','Doctor','System Update','You have a new notification','/notifications.php',0,'2026-03-13 20:00:00'),(8,'ADM009','Nurse','System Update','You have a new notification','/notifications.php',0,'2026-02-28 14:52:00'),(9,'ADM010','Labtech','System Update','You have a new notification','/notifications.php',1,'2026-03-08 23:27:00'),(10,'ADM011','Patient','System Update','You have a new notification','/notifications.php',0,'2026-03-14 01:29:00'),(11,'ADM012','Caregiver','System Update','You have a new notification','/notifications.php',0,'2026-02-14 23:54:00'),(12,'ADM013','Admin','System Update','You have a new notification','/notifications.php',1,'2026-02-22 08:52:00'),(13,'ADM014','Doctor','System Update','You have a new notification','/notifications.php',0,'2026-02-08 00:03:00'),(14,'ADM015','Nurse','System Update','You have a new notification','/notifications.php',0,'2026-02-22 10:07:00'),(15,'ADM016','Labtech','System Update','You have a new notification','/notifications.php',1,'2026-02-27 08:15:00'),(16,'ADM017','Patient','System Update','You have a new notification','/notifications.php',0,'2026-03-02 04:59:00'),(17,'ADM018','Caregiver','System Update','You have a new notification','/notifications.php',0,'2026-02-27 17:52:00'),(18,'ADM019','Admin','System Update','You have a new notification','/notifications.php',1,'2026-03-15 16:43:00'),(19,'ADM020','Doctor','System Update','You have a new notification','/notifications.php',0,'2026-02-26 16:27:00'),(20,'DR001','Nurse','System Update','You have a new notification','/notifications.php',0,'2026-02-01 16:28:00'),(21,'NR001','Nurse','New Appointment Request','A patient requested an appointment for 25-03-2026 08:30 (30 min).','/Telehealth_system/nurse/nurse_assign_doctor.php',0,'2026-03-20 19:37:38'),(22,'DR009','Doctor','New Appointment Assigned','You have a new appointment scheduled for 25-03-2026 08:30 (30 min).','/Telehealth_system/doctor/doctor_appointments.php',1,'2026-03-20 19:58:31'),(23,'PT001','Patient','Doctor Assigned','Your appointment is scheduled for 25-03-2026 08:30 (30 min).','/Telehealth_system/patient/patient_my_appointments.php',0,'2026-03-20 19:58:32'),(24,'DR009','Doctor','New Appointment Assigned','You have a new appointment scheduled for 25-03-2026 08:30 (30 min).','/Telehealth_system/doctor/doctor_appointments.php',1,'2026-03-20 19:58:41'),(25,'PT001','Patient','Doctor Assigned','Your appointment is scheduled for 25-03-2026 08:30 (30 min).','/Telehealth_system/patient/patient_my_appointments.php',0,'2026-03-20 19:58:41'),(26,'PT001','Patient','Reassignment Requested','Your appointment scheduled for 25-03-2026 08:30 has been sent for reassignment.','/Telehealth_system/patient/patient_my_appointments.php',0,'2026-03-20 20:00:01'),(27,'NR001','Nurse','Appointment Needs Reassignment','A patient requested reassignment for an appointment scheduled for 25-03-2026 08:30.','/Telehealth_system/nurse/nurse_assign_doctor.php',0,'2026-03-20 20:00:01'),(28,'DR009','Doctor','Appointment Reassigned','A patient requested reassignment for an appointment scheduled for 25-03-2026 08:30.','/Telehealth_system/doctor/doctor_appointments.php',1,'2026-03-20 20:00:01');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nurses`
--

DROP TABLE IF EXISTS `nurses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nurses` (
  `NurseID` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `HospitalID` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `FName` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  `DOB` date NOT NULL,
  `Gender` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `NatID_PP` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `PhoneNum` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Email` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `LicenseNum` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Specialization` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `CreatedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`NurseID`),
  UNIQUE KEY `NatID_PP` (`NatID_PP`),
  UNIQUE KEY `PhoneNum` (`PhoneNum`),
  UNIQUE KEY `Email` (`Email`),
  UNIQUE KEY `LicenseNum` (`LicenseNum`),
  KEY `idx_nurses_hospital` (`HospitalID`),
  CONSTRAINT `nurses_ibfk_1` FOREIGN KEY (`NurseID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `nurses_ibfk_2` FOREIGN KEY (`HospitalID`) REFERENCES `hospitals` (`HospitalID`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nurses`
--

LOCK TABLES `nurses` WRITE;
/*!40000 ALTER TABLE `nurses` DISABLE KEYS */;
INSERT INTO `nurses` VALUES ('NR001','HOS001','George Maina','2001-02-07','Male','NID0001','0755000001','gmaina2@telehealth.test','NLIC00001','General Nursing','2025-12-10 20:36:00','2026-03-10 12:03:00'),('NR002','HOS002','Francis Maina','2000-07-17','Male','NID0002','0755000002','fmaina@telehealth.test','NLIC00002','General Nursing','2026-01-25 12:22:00','2026-03-02 23:32:00'),('NR003','HOS003','Helen Sang','1999-08-25','Female','NID0003','0755000003','hsang@telehealth.test','NLIC00003','General Nursing','2025-12-20 07:33:00','2026-03-02 15:17:00'),('NR004','HOS004','George Omondi','1998-01-08','Male','NID0004','0755000004','gomondi@telehealth.test','NLIC00004','General Nursing','2025-11-27 11:55:00','2026-03-07 15:54:00'),('NR005','HOS005','Carol Mumo','1997-03-06','Female','NID0005','0755000005','cmumo@telehealth.test','NLIC00005','General Nursing','2025-12-11 08:11:00','2026-02-26 01:13:00'),('NR006','HOS006','Kevin Muthoni','1996-05-22','Male','NID0006','0755000006','kmuthoni@telehealth.test','NLIC00006','General Nursing','2026-01-05 01:34:00','2026-03-05 23:08:00'),('NR007','HOS007','Faith Omondi','1995-06-17','Female','NID0007','0755000007','fomondi@telehealth.test','NLIC00007','General Nursing','2025-11-30 17:15:00','2026-03-09 11:35:00'),('NR008','HOS008','Michael Barasa','1994-03-06','Male','NID0008','0755000008','mbarasa@telehealth.test','NLIC00008','General Nursing','2025-11-28 23:22:00','2026-02-23 15:11:00'),('NR009','HOS009','Kevin Kiptoo','1993-08-10','Male','NID0009','0755000009','kkiptoo@telehealth.test','NLIC00009','General Nursing','2026-01-16 11:16:00','2026-02-24 20:20:00'),('NR010','HOS010','Francis Mutiso','1992-09-10','Male','NID0010','0755000010','fmutiso@telehealth.test','NLIC00010','General Nursing','2025-12-05 19:35:00','2026-02-20 06:32:00'),('NR011','HOS011','David Mwangi','1991-08-02','Male','NID0011','0755000011','dmwangi@telehealth.test','NLIC00011','General Nursing','2025-12-09 17:18:00','2026-02-23 00:16:00'),('NR012','HOS012','Leonard Kilonzo','2002-06-25','Male','NID0012','0755000012','lkilonzo@telehealth.test','NLIC00012','General Nursing','2025-12-29 08:00:00','2026-02-24 15:05:00'),('NR013','HOS013','Peter Karanja','2001-03-05','Male','NID0013','0755000013','pkaranja@telehealth.test','NLIC00013','General Nursing','2026-01-01 00:30:00','2026-03-05 06:16:00'),('NR014','HOS014','Mary Onyango','2000-03-01','Female','NID0014','0755000014','monyango@telehealth.test','NLIC00014','General Nursing','2025-12-05 20:54:00','2026-03-08 11:06:00'),('NR015','HOS015','Peter Kilonzo','1999-06-01','Male','NID0015','0755000015','pkilonzo@telehealth.test','NLIC00015','General Nursing','2025-12-29 11:20:00','2026-03-10 01:45:00'),('NR016','HOS016','John Otieno','1998-01-22','Male','NID0016','0755000016','jotieno2@telehealth.test','NLIC00016','General Nursing','2025-12-13 10:39:00','2026-03-10 00:26:00'),('NR017','HOS017','Eric Kariuki','1997-08-07','Male','NID0017','0755000017','ekariuki@telehealth.test','NLIC00017','General Nursing','2026-01-09 08:17:00','2026-02-28 04:49:00'),('NR018','HOS018','Esther Sang','1996-04-10','Female','NID0018','0755000018','esang@telehealth.test','NLIC00018','General Nursing','2025-11-25 15:21:00','2026-03-11 18:30:00'),('NR019','HOS019','Evan Mwangi','1995-09-03','Male','NID0019','0755000019','emwangi2@telehealth.test','NLIC00019','General Nursing','2025-11-17 07:21:00','2026-03-03 09:05:00'),('NR020','HOS020','David Githinji','1994-03-21','Male','NID0020','0755000020','dgithinji@telehealth.test','NLIC00020','General Nursing','2025-11-25 20:06:00','2026-03-12 06:53:00');
/*!40000 ALTER TABLE `nurses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `patient_vitals`
--

DROP TABLE IF EXISTS `patient_vitals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `patient_vitals` (
  `VitalID` int NOT NULL AUTO_INCREMENT,
  `PatientID` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `UploadedByID` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `UploadedByRole` enum('Nurse','Patient','Caregiver') COLLATE utf8mb4_unicode_ci NOT NULL,
  `AppID` int NOT NULL,
  `Temperature` decimal(4,1) DEFAULT NULL,
  `BloodPressure` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `HeartRate` int DEFAULT NULL,
  `RespiratoryRate` int DEFAULT NULL,
  `OxygenSaturation` int DEFAULT NULL,
  `Notes` text COLLATE utf8mb4_unicode_ci,
  `RecordedAt` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`VitalID`),
  KEY `idx_vitals_app` (`AppID`,`PatientID`,`UploadedByRole`,`UploadedByID`),
  KEY `PatientID` (`PatientID`),
  CONSTRAINT `patient_vitals_ibfk_1` FOREIGN KEY (`PatientID`) REFERENCES `patients` (`PatientID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `patient_vitals_ibfk_2` FOREIGN KEY (`AppID`) REFERENCES `appointments` (`AppID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `patient_vitals`
--

LOCK TABLES `patient_vitals` WRITE;
/*!40000 ALTER TABLE `patient_vitals` DISABLE KEYS */;
INSERT INTO `patient_vitals` VALUES (1,'PT001','NR001','Nurse',1,36.6,'120/80',71,16,98,'Stable','2026-03-14 23:48:00'),(2,'PT002','NR002','Nurse',2,36.7,'120/80',72,16,98,'Stable','2026-03-15 13:26:00'),(3,'PT003','NR003','Nurse',3,36.8,'120/80',73,16,98,'Stable','2026-03-10 11:14:00'),(4,'PT004','NR004','Nurse',4,36.5,'120/80',74,16,98,'Stable','2026-03-10 23:35:00'),(5,'PT005','NR005','Nurse',5,36.6,'120/80',75,16,98,'Stable','2026-03-11 10:45:00'),(6,'PT006','NR006','Nurse',6,36.7,'120/80',76,16,98,'Stable','2026-03-07 17:47:00'),(7,'PT007','NR007','Nurse',7,36.8,'120/80',77,16,98,'Stable','2026-03-01 16:36:00'),(8,'PT008','NR008','Nurse',8,36.5,'120/80',78,16,98,'Stable','2026-03-05 12:56:00'),(9,'PT009','NR009','Nurse',9,36.6,'120/80',79,16,98,'Stable','2026-03-04 13:19:00'),(10,'PT010','NR010','Nurse',10,36.7,'120/80',70,16,98,'Stable','2026-03-07 06:27:00'),(11,'PT011','NR011','Nurse',11,36.8,'120/80',71,16,98,'Stable','2026-03-04 13:18:00'),(12,'PT012','NR012','Nurse',12,36.5,'120/80',72,16,98,'Stable','2026-03-04 12:44:00'),(13,'PT013','NR013','Nurse',13,36.6,'120/80',73,16,98,'Stable','2026-03-14 02:37:00'),(14,'PT014','NR014','Nurse',14,36.7,'120/80',74,16,98,'Stable','2026-03-02 18:10:00'),(15,'PT015','NR015','Nurse',15,36.8,'120/80',75,16,98,'Stable','2026-03-05 10:19:00'),(16,'PT016','NR016','Nurse',16,36.5,'120/80',76,16,98,'Stable','2026-03-13 09:33:00'),(17,'PT017','NR017','Nurse',17,36.6,'120/80',77,16,98,'Stable','2026-03-07 05:16:00'),(18,'PT018','NR018','Nurse',18,36.7,'120/80',78,16,98,'Stable','2026-03-14 21:05:00'),(19,'PT019','NR019','Nurse',19,36.8,'120/80',79,16,98,'Stable','2026-03-09 15:54:00'),(20,'PT020','NR020','Nurse',20,36.5,'120/80',70,16,98,'Stable','2026-03-05 09:07:00');
/*!40000 ALTER TABLE `patient_vitals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `patients`
--

DROP TABLE IF EXISTS `patients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `patients` (
  `PatientID` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `FName` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  `DOB` date NOT NULL,
  `Gender` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `NatID_PP` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `PhoneNum` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Email` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `NOKName` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `NOKPhoneNum` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `NOKEmail` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `SHAnum` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `InsuranceProvider` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `PolicyNum` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `CreatedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`PatientID`),
  UNIQUE KEY `PolicyNum` (`PolicyNum`),
  CONSTRAINT `patients_ibfk_1` FOREIGN KEY (`PatientID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `patients`
--

LOCK TABLES `patients` WRITE;
/*!40000 ALTER TABLE `patients` DISABLE KEYS */;
INSERT INTO `patients` VALUES ('PT001','Timothy Karanja','2006-10-09','Male','PID0001','0722000001','tkaranja@telehealth.test','Joy Omondi','0733000001','nok1@telehealth.test','SHA00001','AfyaCover','POL00001','2026-02-01 19:39:00','2026-03-02 07:37:00'),('PT002','Daniel Mumo','2005-11-15','Male','PID0002','0722000002','dmumo2@telehealth.test','Ian Wekesa','0733000002','nok2@telehealth.test','SHA00002','AfyaCover','POL00002','2026-02-09 12:26:00','2026-02-25 16:51:00'),('PT003','Leonard Barasa','2004-12-03','Male','PID0003','0722000003','lbarasa2@telehealth.test','Ruth Karanja','0733000003','nok3@telehealth.test','SHA00003','AfyaCover','POL00003','2025-12-31 16:43:00','2026-02-26 21:07:00'),('PT004','Mercy Onyango','2003-03-20','Female','PID0004','0722000004','monyango2@telehealth.test','Irene Njoroge','0733000004','nok4@telehealth.test','SHA00004','AfyaCover','POL00004','2025-12-18 04:48:00','2026-03-08 22:30:00'),('PT005','Carol Njoroge','2002-01-06','Female','PID0005','0722000005','cnjoroge@telehealth.test','Francis Nduta','0733000005','nok5@telehealth.test','SHA00005','AfyaCover','POL00005','2025-12-20 06:58:00','2026-03-10 06:11:00'),('PT006','Irene Kamau','2001-11-15','Female','PID0006','0722000006','ikamau@telehealth.test','Mercy Korir','0733000006','nok6@telehealth.test','SHA00006','AfyaCover','POL00006','2026-01-12 16:34:00','2026-03-02 03:56:00'),('PT007','Emmanuel Mumo','2000-02-19','Male','PID0007','0722000007','emumo@telehealth.test','Paul Mutiso','0733000007','nok7@telehealth.test','SHA00007','AfyaCover','POL00007','2026-01-27 13:30:00','2026-03-15 10:42:00'),('PT008','Kevin Otieno','1999-05-11','Male','PID0008','0722000008','kotieno@telehealth.test','Evan Githinji','0733000008','nok8@telehealth.test','SHA00008','AfyaCover','POL00008','2025-12-06 16:42:00','2026-03-10 04:57:00'),('PT009','Joy Kamau','1998-10-17','Female','PID0009','0722000009','jkamau@telehealth.test','Catherine Ochieng','0733000009','nok9@telehealth.test','SHA00009','AfyaCover','POL00009','2026-02-01 00:34:00','2026-02-24 02:42:00'),('PT010','Eric Korir','1997-11-18','Male','PID0010','0722000010','ekorir@telehealth.test','Emmanuel Korir','0733000010','nok10@telehealth.test','SHA00010','AfyaCover','POL00010','2025-12-18 02:38:00','2026-02-24 16:35:00'),('PT011','Emmanuel Barasa','1996-04-21','Male','PID0011','0722000011','ebarasa@telehealth.test','Francis Ochieng','0733000011','nok11@telehealth.test','SHA00011','AfyaCover','POL00011','2026-02-08 15:08:00','2026-03-12 09:34:00'),('PT012','Daniel Cheruiyot','1995-11-28','Male','PID0012','0722000012','dcheruiyot@telehealth.test','James Sang','0733000012','nok12@telehealth.test','SHA00012','AfyaCover','POL00012','2026-02-14 16:48:00','2026-02-28 20:01:00'),('PT013','Emmanuel Githinji','1994-10-24','Male','PID0013','0722000013','egithinji@telehealth.test','Ruth Otieno','0733000013','nok13@telehealth.test','SHA00013','AfyaCover','POL00013','2026-01-16 12:27:00','2026-02-28 09:04:00'),('PT014','Mercy Ochieng','1993-07-06','Female','PID0014','0722000014','mochieng@telehealth.test','Carol Githinji','0733000014','nok14@telehealth.test','SHA00014','AfyaCover','POL00014','2026-01-30 03:53:00','2026-03-09 21:16:00'),('PT015','Catherine Moraa','1992-11-08','Female','PID0015','0722000015','cmoraa2@telehealth.test','Lilian Omondi','0733000015','nok15@telehealth.test','SHA00015','AfyaCover','POL00015','2026-01-19 03:43:00','2026-03-08 09:36:00'),('PT016','Mercy Kamau','1991-05-10','Female','PID0016','0722000016','mkamau2@telehealth.test','Michael Muthoni','0733000016','nok16@telehealth.test','SHA00016','AfyaCover','POL00016','2026-01-23 18:55:00','2026-03-15 22:46:00'),('PT017','Esther Korir','1990-05-02','Female','PID0017','0722000017','ekorir2@telehealth.test','Victor Barasa','0733000017','nok17@telehealth.test','SHA00017','AfyaCover','POL00017','2026-01-24 12:05:00','2026-03-04 19:41:00'),('PT018','Ian Ochieng','1989-02-05','Male','PID0018','0722000018','iochieng2@telehealth.test','Catherine Wairimu','0733000018','nok18@telehealth.test','SHA00018','AfyaCover','POL00018','2025-12-31 05:57:00','2026-03-12 07:51:00'),('PT019','Faith Onyango','1988-04-15','Female','PID0019','0722000019','fonyango@telehealth.test','Ian Githinji','0733000019','nok19@telehealth.test','SHA00019','AfyaCover','POL00019','2025-12-14 05:25:00','2026-03-09 10:26:00'),('PT020','Timothy Githinji','1987-01-06','Male','PID0020','0722000020','tgithinji@telehealth.test','Patrick Otieno','0733000020','nok20@telehealth.test','SHA00020','AfyaCover','POL00020','2026-01-19 13:28:00','2026-03-15 11:25:00');
/*!40000 ALTER TABLE `patients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ratings`
--

DROP TABLE IF EXISTS `ratings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ratings` (
  `RatingID` int NOT NULL AUTO_INCREMENT,
  `UserID` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `EntityType` enum('Doctor','Hospital','Lab') COLLATE utf8mb4_unicode_ci NOT NULL,
  `EntityID` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `RatingValue` decimal(2,1) NOT NULL,
  `RatingDate` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`RatingID`),
  KEY `idx_ratings_user` (`UserID`),
  KEY `idx_ratings_entity` (`EntityType`,`EntityID`),
  CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ratings`
--

LOCK TABLES `ratings` WRITE;
/*!40000 ALTER TABLE `ratings` DISABLE KEYS */;
INSERT INTO `ratings` VALUES (1,'PT001','Hospital','HOS001',4.0,'2026-03-06 04:38:00'),(2,'PT002','Lab','LABS002',3.5,'2026-03-14 20:35:00'),(3,'PT003','Doctor','DR003',4.0,'2026-02-16 10:47:00'),(4,'PT004','Hospital','HOS004',3.5,'2026-03-03 06:43:00'),(5,'PT005','Lab','LABS005',4.0,'2026-02-26 14:12:00'),(6,'PT006','Doctor','DR006',3.5,'2026-02-21 21:15:00'),(7,'PT007','Hospital','HOS007',4.0,'2026-03-13 22:22:00'),(8,'PT008','Lab','LABS008',3.5,'2026-02-21 06:34:00'),(9,'PT009','Doctor','DR009',4.0,'2026-02-27 00:45:00'),(10,'PT010','Hospital','HOS010',3.5,'2026-02-24 12:40:00'),(11,'PT011','Lab','LABS011',4.0,'2026-02-22 17:22:00'),(12,'PT012','Doctor','DR012',3.5,'2026-02-28 03:28:00'),(13,'PT013','Hospital','HOS013',4.0,'2026-03-15 12:17:00'),(14,'PT014','Lab','LABS014',3.5,'2026-03-10 11:26:00'),(15,'PT015','Doctor','DR015',4.0,'2026-02-21 07:41:00'),(16,'PT016','Hospital','HOS016',3.5,'2026-02-19 03:14:00'),(17,'PT017','Lab','LABS017',4.0,'2026-02-21 12:33:00'),(18,'PT018','Doctor','DR018',3.5,'2026-03-12 23:26:00'),(19,'PT019','Hospital','HOS019',4.0,'2026-03-04 16:39:00'),(20,'PT020','Lab','LABS020',3.5,'2026-02-27 18:53:00');
/*!40000 ALTER TABLE `ratings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `SessionID` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `PatientID` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `DoctorID` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `NurseID` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `AppID` int DEFAULT NULL,
  `Diagnosis` text COLLATE utf8mb4_unicode_ci,
  `Prescription` text COLLATE utf8mb4_unicode_ci,
  `FollowupDate` date DEFAULT NULL,
  `SpecialistRecommended` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `FutureCare` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`SessionID`),
  KEY `idx_sessions_app` (`AppID`),
  KEY `PatientID` (`PatientID`),
  KEY `DoctorID` (`DoctorID`),
  KEY `NurseID` (`NurseID`),
  CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`PatientID`) REFERENCES `patients` (`PatientID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sessions_ibfk_2` FOREIGN KEY (`DoctorID`) REFERENCES `doctors` (`DoctorID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sessions_ibfk_3` FOREIGN KEY (`NurseID`) REFERENCES `nurses` (`NurseID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sessions_ibfk_4` FOREIGN KEY (`AppID`) REFERENCES `appointments` (`AppID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('SES000000000001','PT001','DR001','NR001',1,'Acute URTI','Paracetamol 500mg','2026-04-10','None','Hydration and rest'),('SES000000000002','PT002','DR002','NR002',2,'Acute URTI','Paracetamol 500mg','2026-04-11','None','Hydration and rest'),('SES000000000003','PT003','DR003','NR003',3,'Acute URTI','Paracetamol 500mg','2026-03-28','None','Hydration and rest'),('SES000000000004','PT004','DR004','NR004',4,'Acute URTI','Paracetamol 500mg','2026-03-27','None','Hydration and rest'),('SES000000000005','PT005','DR005','NR005',5,'Acute URTI','Paracetamol 500mg','2026-03-29','None','Hydration and rest'),('SES000000000006','PT006','DR006','NR006',6,'Acute URTI','Paracetamol 500mg','2026-03-24','None','Hydration and rest'),('SES000000000007','PT007','DR007','NR007',7,'Acute URTI','Paracetamol 500mg','2026-04-07','None','Hydration and rest'),('SES000000000008','PT008','DR008','NR008',8,'Acute URTI','Paracetamol 500mg','2026-04-14','None','Hydration and rest'),('SES000000000009','PT009','DR009','NR009',9,'Acute URTI','Paracetamol 500mg','2026-04-14','None','Hydration and rest'),('SES000000000010','PT010','DR010','NR010',10,'Acute URTI','Paracetamol 500mg','2026-04-15','None','Hydration and rest'),('SES000000000011','PT011','DR011','NR011',11,'Acute URTI','Paracetamol 500mg','2026-03-31','None','Hydration and rest'),('SES000000000012','PT012','DR012','NR012',12,'Acute URTI','Paracetamol 500mg','2026-04-08','None','Hydration and rest'),('SES000000000013','PT013','DR013','NR013',13,'Acute URTI','Paracetamol 500mg','2026-03-29','None','Hydration and rest'),('SES000000000014','PT014','DR014','NR014',14,'Acute URTI','Paracetamol 500mg','2026-04-02','None','Hydration and rest'),('SES000000000015','PT015','DR015','NR015',15,'Acute URTI','Paracetamol 500mg','2026-03-29','None','Hydration and rest'),('SES000000000016','PT016','DR016','NR016',16,'Acute URTI','Paracetamol 500mg','2026-03-31','None','Hydration and rest'),('SES000000000017','PT017','DR017','NR017',17,'Acute URTI','Paracetamol 500mg','2026-04-12','None','Hydration and rest'),('SES000000000018','PT018','DR018','NR018',18,'Acute URTI','Paracetamol 500mg','2026-04-09','None','Hydration and rest'),('SES000000000019','PT019','DR019','NR019',19,'Acute URTI','Paracetamol 500mg','2026-04-16','None','Hydration and rest'),('SES000000000020','PT020','DR020','NR020',20,'Acute URTI','Paracetamol 500mg','2026-03-31','None','Hydration and rest');
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_security_questions`
--

DROP TABLE IF EXISTS `user_security_questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_security_questions` (
  `UserID` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `QuestionKey` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `AnswerHash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `CreatedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`UserID`,`QuestionKey`),
  KEY `idx_user_security_q_user` (`UserID`),
  CONSTRAINT `user_security_questions_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_security_questions`
--

LOCK TABLES `user_security_questions` WRITE;
/*!40000 ALTER TABLE `user_security_questions` DISABLE KEYS */;
INSERT INTO `user_security_questions` VALUES ('ADM001','Q1','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','2026-01-28 17:17:00','2026-03-10 07:48:00'),('ADM002','Q2','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','2026-01-24 07:37:00','2026-03-04 10:31:00'),('ADM003','Q3','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','2026-01-20 17:34:00','2026-03-10 06:22:00'),('ADM004','Q4','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','2026-01-23 08:42:00','2026-03-10 07:48:00'),('ADM005','Q1','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','2026-01-28 21:40:00','2026-02-26 12:12:00'),('ADM006','Q2','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','2026-02-02 06:17:00','2026-03-01 03:23:00'),('ADM007','Q3','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','2026-02-04 15:50:00','2026-03-07 01:14:00'),('ADM008','Q4','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','2025-12-30 02:44:00','2026-03-05 10:29:00'),('ADM009','Q1','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','2026-01-19 19:07:00','2026-02-26 21:26:00'),('ADM010','Q2','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','2025-12-20 07:43:00','2026-02-27 19:19:00'),('ADM011','Q3','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','2025-12-19 05:20:00','2026-03-03 02:18:00'),('ADM012','Q4','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','2026-01-12 13:36:00','2026-03-14 17:21:00'),('ADM013','Q1','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','2025-12-20 11:51:00','2026-03-15 12:36:00'),('ADM014','Q2','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','2025-12-27 00:04:00','2026-03-11 02:07:00'),('ADM015','Q3','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','2025-12-25 22:39:00','2026-02-28 19:41:00'),('ADM016','Q4','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','2026-01-15 16:33:00','2026-03-01 21:32:00'),('ADM017','Q1','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','2025-12-22 17:05:00','2026-03-08 22:00:00'),('ADM018','Q2','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','2026-01-12 17:51:00','2026-03-06 12:25:00'),('ADM019','Q3','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','2026-01-22 09:31:00','2026-03-08 06:38:00'),('ADM020','Q4','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','2025-12-16 16:19:00','2026-03-09 03:14:00'),('ADM021','q1','$2y$10$c5gPp/YrYlnY4oJXQ6ZhM.PxzyDKmpG4NMadhy8ufyldARY1XU4su','2026-03-20 19:01:05','2026-03-20 19:01:05'),('ADM021','q2','$2y$10$LQeEgr6vKFLJaT4YrMmg/enIPKyuPF0Z3iuVq9mvx9wmzU7Enf29C','2026-03-20 19:01:05','2026-03-20 19:01:05'),('ADM021','q3','$2y$10$mLN4VEcf1Xfvz4rPfNNjFuHYBv.8NbUb7imzpsdo9mo0MOvAlAZ4K','2026-03-20 19:01:05','2026-03-20 19:01:05'),('ADM021','q4','$2y$10$E2SLSmzlsGJJJQXFu.AvdeSV.GvpBbprIPiOA01jTjNhg8GSC8EsC','2026-03-20 19:01:05','2026-03-20 19:01:05'),('ADM021','q5','$2y$10$7qFSs8YZ50dw85lK3VGiMu56l5tM2IRoNudWuQPOozuZ14G71YWe.','2026-03-20 19:01:05','2026-03-20 19:01:05'),('PT021','q1','$2y$10$PhLp8y8FHa/1zH/cxddCJeB6DAVmYuf9jbtfdPL.kft74I6LFTir6','2026-03-20 19:05:21','2026-03-20 19:05:21'),('PT021','q2','$2y$10$hTOouAcqNLdXvhEaMKd8tuWKkyN/HrG0GQ8SwSXXczW4O7fAUg6t2','2026-03-20 19:05:22','2026-03-20 19:05:22'),('PT021','q3','$2y$10$4Ob5bqZu6p7cvbhtsQZDMeEHprA1QSKCmkGdgloBh2n0X/eK.Pf6.','2026-03-20 19:05:22','2026-03-20 19:05:22'),('PT021','q4','$2y$10$GuEnpvM4csbkIoxSyJO7meggIbGqivCS1ppFSK8sYrbmYwn42LzFi','2026-03-20 19:05:22','2026-03-20 19:05:22'),('PT021','q5','$2y$10$2XAS4zZMD7mMfiAz20YgVeKPrfFBuDoP.UUIPix4XEEtz8KDq2Uvi','2026-03-20 19:05:22','2026-03-20 19:05:22'),('PT022','q1','$2y$10$KncFFHNN6aEl97XrgqDq4OpNVtEtsneI8gnsVYWjW0PutSZE81GUi','2026-06-15 16:26:52','2026-06-15 16:26:52'),('PT022','q2','$2y$10$L4OCK0qo8D7BIpZ6aT1IseBDSWyWWJDpICaJjb8qAVz2K0roU7NoC','2026-06-15 16:26:53','2026-06-15 16:26:53'),('PT022','q3','$2y$10$eYam.Eelg2d.63TJHvIrEuqrsgIS0w3g4m.M5.RTsneLasU4oPg7y','2026-06-15 16:26:53','2026-06-15 16:26:53'),('PT022','q4','$2y$10$yLtL0uTaoBrX8hrh1Lg9a.lMHNnJzgeXN2zWS2TxAZraqrQ6TFqU.','2026-06-15 16:26:53','2026-06-15 16:26:53'),('PT022','q5','$2y$10$kHwWdsZOBc5ae0uYi6Cie.W2zPSmYm4f09mxak1sAD5LaSnvK4Dcu','2026-06-15 16:26:53','2026-06-15 16:26:53');
/*!40000 ALTER TABLE `user_security_questions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `UserID` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Uname` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Passwd` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Role` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `RegDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `AprovDate` datetime DEFAULT NULL,
  `ApprovedBy` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Status` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ProfileComplete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`UserID`),
  UNIQUE KEY `Uname` (`Uname`),
  KEY `idx_users_role` (`Role`),
  KEY `idx_users_status` (`Status`),
  KEY `idx_users_approved_by` (`ApprovedBy`),
  CONSTRAINT `fk_users_approved_by` FOREIGN KEY (`ApprovedBy`) REFERENCES `admin` (`AdminID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES ('ADM001','eonyango','$2y$10$q8Q4wx3a.hdUpdcZefpxMexr75N215xt7CQgexL8PKjSHjO7LKXfm','Admin','2025-12-08 18:14:00','2025-12-30 08:06:00','ADM001','Active',1),('ADM002','jochieng','$2y$10$q8Q4wx3a.hdUpdcZefpxMexr75N215xt7CQgexL8PKjSHjO7LKXfm','Admin','2025-10-02 03:07:00','2026-01-08 14:57:00','ADM001','Active',1),('ADM003','fkamau','$2y$10$q8Q4wx3a.hdUpdcZefpxMexr75N215xt7CQgexL8PKjSHjO7LKXfm','Admin','2025-12-16 04:44:00','2025-12-20 03:31:00','ADM001','Active',1),('ADM004','ckamau','$2y$10$q8Q4wx3a.hdUpdcZefpxMexr75N215xt7CQgexL8PKjSHjO7LKXfm','Admin','2025-11-29 00:57:00','2025-12-27 08:04:00','ADM001','Active',1),('ADM005','jsang','$2y$10$q8Q4wx3a.hdUpdcZefpxMexr75N215xt7CQgexL8PKjSHjO7LKXfm','Admin','2025-10-26 07:52:00','2025-12-28 21:08:00','ADM001','Active',1),('ADM006','dkorir','$2y$10$q8Q4wx3a.hdUpdcZefpxMexr75N215xt7CQgexL8PKjSHjO7LKXfm','Admin','2025-11-12 13:27:00','2025-12-28 07:06:00','ADM001','Active',1),('ADM007','rwekesa','$2y$10$q8Q4wx3a.hdUpdcZefpxMexr75N215xt7CQgexL8PKjSHjO7LKXfm','Admin','2025-09-24 10:32:00','2025-11-23 02:41:00','ADM001','Active',1),('ADM008','mwairimu','$2y$10$q8Q4wx3a.hdUpdcZefpxMexr75N215xt7CQgexL8PKjSHjO7LKXfm','Admin','2025-12-11 07:26:00','2025-11-16 04:45:00','ADM001','Active',1),('ADM009','cmaina','$2y$10$q8Q4wx3a.hdUpdcZefpxMexr75N215xt7CQgexL8PKjSHjO7LKXfm','Admin','2025-11-12 08:05:00','2026-01-02 12:42:00','ADM001','Active',1),('ADM010','mkaranja','$2y$10$q8Q4wx3a.hdUpdcZefpxMexr75N215xt7CQgexL8PKjSHjO7LKXfm','Admin','2025-10-20 20:53:00','2025-12-13 07:40:00','ADM001','Active',1),('ADM011','skamau','$2y$10$q8Q4wx3a.hdUpdcZefpxMexr75N215xt7CQgexL8PKjSHjO7LKXfm','Admin','2025-12-16 05:27:00','2026-01-08 17:34:00','ADM001','Active',1),('ADM012','mmumo','$2y$10$q8Q4wx3a.hdUpdcZefpxMexr75N215xt7CQgexL8PKjSHjO7LKXfm','Admin','2025-10-27 09:46:00','2025-11-21 02:07:00','ADM001','Active',1),('ADM013','dmumo','$2y$10$q8Q4wx3a.hdUpdcZefpxMexr75N215xt7CQgexL8PKjSHjO7LKXfm','Admin','2025-11-29 07:27:00','2025-12-03 09:42:00','ADM001','Active',1),('ADM014','swanjiru','$2y$10$q8Q4wx3a.hdUpdcZefpxMexr75N215xt7CQgexL8PKjSHjO7LKXfm','Admin','2025-10-11 00:02:00','2025-11-22 23:57:00','ADM001','Active',1),('ADM015','gmaina','$2y$10$q8Q4wx3a.hdUpdcZefpxMexr75N215xt7CQgexL8PKjSHjO7LKXfm','Admin','2025-10-01 07:18:00','2025-12-28 01:59:00','ADM001','Active',1),('ADM016','eochieng','$2y$10$q8Q4wx3a.hdUpdcZefpxMexr75N215xt7CQgexL8PKjSHjO7LKXfm','Admin','2025-11-21 11:02:00','2025-11-29 00:50:00','ADM001','Active',1),('ADM017','vmutiso','$2y$10$q8Q4wx3a.hdUpdcZefpxMexr75N215xt7CQgexL8PKjSHjO7LKXfm','Admin','2025-11-11 17:37:00','2025-12-15 05:00:00','ADM001','Active',1),('ADM018','jotieno','$2y$10$q8Q4wx3a.hdUpdcZefpxMexr75N215xt7CQgexL8PKjSHjO7LKXfm','Admin','2025-10-25 11:08:00','2026-01-01 16:32:00','ADM001','Active',1),('ADM019','vkamau','$2y$10$q8Q4wx3a.hdUpdcZefpxMexr75N215xt7CQgexL8PKjSHjO7LKXfm','Admin','2025-12-06 09:28:00','2025-12-27 19:42:00','ADM001','Active',1),('ADM020','nmwangi','$2y$10$q8Q4wx3a.hdUpdcZefpxMexr75N215xt7CQgexL8PKjSHjO7LKXfm','Admin','2025-12-10 04:43:00','2025-11-27 21:39:00','ADM001','Active',1),('ADM021','danny','$2y$10$cbmfHEugG8YVXBWXp1CP1uNmh/mJ8udX.t.QQnYXF1OT2YqRht2M.','Admin','2026-03-20 19:01:05',NULL,NULL,'Pending',0),('CG001','agithinji','$2y$10$6q1FBPbUniUlz8cDw0FxK.17oVzXLGB3atQVL8oQzRQN2Hah/B9rK','Caregiver','2025-11-27 15:24:00','2025-12-25 03:48:00','ADM006','Active',1),('CG002','jkorir2','$2y$10$6q1FBPbUniUlz8cDw0FxK.17oVzXLGB3atQVL8oQzRQN2Hah/B9rK','Caregiver','2026-01-02 14:47:00','2025-12-27 23:21:00','ADM006','Active',1),('CG003','echeruiyot','$2y$10$6q1FBPbUniUlz8cDw0FxK.17oVzXLGB3atQVL8oQzRQN2Hah/B9rK','Caregiver','2025-11-28 09:27:00','2026-01-31 08:10:00','ADM006','Active',1),('CG004','fkariuki','$2y$10$6q1FBPbUniUlz8cDw0FxK.17oVzXLGB3atQVL8oQzRQN2Hah/B9rK','Caregiver','2026-01-10 10:37:00','2026-01-20 01:40:00','ADM006','Active',1),('CG005','dnduta','$2y$10$6q1FBPbUniUlz8cDw0FxK.17oVzXLGB3atQVL8oQzRQN2Hah/B9rK','Caregiver','2026-01-18 18:55:00','2025-12-31 02:12:00','ADM006','Active',1),('CG006','hmaina','$2y$10$6q1FBPbUniUlz8cDw0FxK.17oVzXLGB3atQVL8oQzRQN2Hah/B9rK','Caregiver','2025-10-27 03:07:00','2025-12-29 00:07:00','ADM006','Active',1),('CG007','smutiso','$2y$10$6q1FBPbUniUlz8cDw0FxK.17oVzXLGB3atQVL8oQzRQN2Hah/B9rK','Caregiver','2026-01-08 18:42:00','2026-01-12 09:25:00','ADM006','Active',1),('CG008','asang','$2y$10$6q1FBPbUniUlz8cDw0FxK.17oVzXLGB3atQVL8oQzRQN2Hah/B9rK','Caregiver','2025-10-27 18:31:00','2026-02-08 21:17:00','ADM006','Active',1),('CG009','vgithinji','$2y$10$6q1FBPbUniUlz8cDw0FxK.17oVzXLGB3atQVL8oQzRQN2Hah/B9rK','Caregiver','2025-12-08 20:35:00','2025-12-19 14:46:00','ADM006','Active',1),('CG010','snduta','$2y$10$6q1FBPbUniUlz8cDw0FxK.17oVzXLGB3atQVL8oQzRQN2Hah/B9rK','Caregiver','2026-01-11 05:16:00','2026-01-25 02:21:00','ADM006','Active',1),('CG011','ponyango','$2y$10$6q1FBPbUniUlz8cDw0FxK.17oVzXLGB3atQVL8oQzRQN2Hah/B9rK','Caregiver','2025-12-12 13:17:00','2026-01-08 06:04:00','ADM006','Active',1),('CG012','dkariuki','$2y$10$6q1FBPbUniUlz8cDw0FxK.17oVzXLGB3atQVL8oQzRQN2Hah/B9rK','Caregiver','2025-12-09 01:17:00','2026-01-18 14:54:00','ADM006','Active',1),('CG013','pnjoroge2','$2y$10$6q1FBPbUniUlz8cDw0FxK.17oVzXLGB3atQVL8oQzRQN2Hah/B9rK','Caregiver','2025-12-06 19:07:00','2025-12-24 05:10:00','ADM006','Active',1),('CG014','mmuthoni','$2y$10$6q1FBPbUniUlz8cDw0FxK.17oVzXLGB3atQVL8oQzRQN2Hah/B9rK','Caregiver','2025-11-12 14:19:00','2026-01-15 23:52:00','ADM006','Active',1),('CG015','bgithinji','$2y$10$6q1FBPbUniUlz8cDw0FxK.17oVzXLGB3atQVL8oQzRQN2Hah/B9rK','Caregiver','2025-12-03 07:41:00','2026-02-02 22:34:00','ADM006','Active',1),('CG016','ksang','$2y$10$6q1FBPbUniUlz8cDw0FxK.17oVzXLGB3atQVL8oQzRQN2Hah/B9rK','Caregiver','2026-01-14 09:22:00','2026-01-15 18:48:00','ADM006','Active',1),('CG017','ibarasa','$2y$10$6q1FBPbUniUlz8cDw0FxK.17oVzXLGB3atQVL8oQzRQN2Hah/B9rK','Caregiver','2025-11-28 19:04:00','2025-12-23 22:25:00','ADM006','Active',1),('CG018','nnjoroge','$2y$10$6q1FBPbUniUlz8cDw0FxK.17oVzXLGB3atQVL8oQzRQN2Hah/B9rK','Caregiver','2025-12-13 05:53:00','2025-12-25 13:35:00','ADM006','Active',1),('CG019','bmumo','$2y$10$6q1FBPbUniUlz8cDw0FxK.17oVzXLGB3atQVL8oQzRQN2Hah/B9rK','Caregiver','2025-11-22 22:25:00','2026-01-07 19:55:00','ADM006','Active',1),('CG020','mnjoroge','$2y$10$6q1FBPbUniUlz8cDw0FxK.17oVzXLGB3atQVL8oQzRQN2Hah/B9rK','Caregiver','2025-12-24 16:04:00','2026-01-03 19:31:00','ADM006','Active',1),('DR001','abarasa','$2y$10$.5zEctX2NZKOwhfIiqb5oukhExXb0JXAa8vUmqI1iBqsrL.vSiohm','Doctor','2025-12-21 08:05:00','2026-01-03 11:45:00','ADM002','Active',1),('DR002','jkorir','$2y$10$.5zEctX2NZKOwhfIiqb5oukhExXb0JXAa8vUmqI1iBqsrL.vSiohm','Doctor','2025-12-08 19:49:00','2025-12-15 01:37:00','ADM002','Active',1),('DR003','fmuthoni','$2y$10$.5zEctX2NZKOwhfIiqb5oukhExXb0JXAa8vUmqI1iBqsrL.vSiohm','Doctor','2025-11-10 04:32:00','2025-12-28 06:48:00','ADM002','Active',1),('DR004','cmoraa','$2y$10$.5zEctX2NZKOwhfIiqb5oukhExXb0JXAa8vUmqI1iBqsrL.vSiohm','Doctor','2025-10-24 03:27:00','2025-12-25 18:38:00','ADM002','Active',1),('DR005','dmuthoni','$2y$10$.5zEctX2NZKOwhfIiqb5oukhExXb0JXAa8vUmqI1iBqsrL.vSiohm','Doctor','2025-10-29 17:22:00','2025-12-22 21:41:00','ADM002','Active',1),('DR006','hmoraa','$2y$10$.5zEctX2NZKOwhfIiqb5oukhExXb0JXAa8vUmqI1iBqsrL.vSiohm','Doctor','2025-11-12 20:34:00','2025-12-27 21:06:00','ADM002','Active',1),('DR007','pbarasa','$2y$10$.5zEctX2NZKOwhfIiqb5oukhExXb0JXAa8vUmqI1iBqsrL.vSiohm','Doctor','2025-10-11 23:23:00','2025-11-28 17:29:00','ADM002','Active',1),('DR008','sbarasa','$2y$10$.5zEctX2NZKOwhfIiqb5oukhExXb0JXAa8vUmqI1iBqsrL.vSiohm','Doctor','2025-11-05 08:32:00','2025-12-17 04:13:00','ADM002','Active',1),('DR009','fnduta','$2y$10$.5zEctX2NZKOwhfIiqb5oukhExXb0JXAa8vUmqI1iBqsrL.vSiohm','Doctor','2025-12-22 03:57:00','2025-11-27 04:49:00','ADM002','Active',1),('DR010','jwanjiru','$2y$10$.5zEctX2NZKOwhfIiqb5oukhExXb0JXAa8vUmqI1iBqsrL.vSiohm','Doctor','2025-11-16 19:35:00','2025-12-13 10:23:00','ADM002','Active',1),('DR011','pnjoroge','$2y$10$.5zEctX2NZKOwhfIiqb5oukhExXb0JXAa8vUmqI1iBqsrL.vSiohm','Doctor','2025-11-29 11:41:00','2026-01-02 14:13:00','ADM002','Active',1),('DR012','emwangi','$2y$10$.5zEctX2NZKOwhfIiqb5oukhExXb0JXAa8vUmqI1iBqsrL.vSiohm','Doctor','2025-12-22 15:37:00','2026-01-02 17:03:00','ADM002','Active',1),('DR013','mkamau','$2y$10$.5zEctX2NZKOwhfIiqb5oukhExXb0JXAa8vUmqI1iBqsrL.vSiohm','Doctor','2025-10-22 02:19:00','2026-01-07 23:40:00','ADM002','Active',1),('DR014','pmoraa','$2y$10$.5zEctX2NZKOwhfIiqb5oukhExXb0JXAa8vUmqI1iBqsrL.vSiohm','Doctor','2025-12-02 07:58:00','2025-12-08 02:20:00','ADM002','Active',1),('DR015','bcheruiyot','$2y$10$.5zEctX2NZKOwhfIiqb5oukhExXb0JXAa8vUmqI1iBqsrL.vSiohm','Doctor','2025-11-16 00:25:00','2025-12-16 17:54:00','ADM002','Active',1),('DR016','jnjoroge','$2y$10$.5zEctX2NZKOwhfIiqb5oukhExXb0JXAa8vUmqI1iBqsrL.vSiohm','Doctor','2025-11-29 04:39:00','2025-12-12 01:59:00','ADM002','Active',1),('DR017','tomondi','$2y$10$.5zEctX2NZKOwhfIiqb5oukhExXb0JXAa8vUmqI1iBqsrL.vSiohm','Doctor','2025-12-11 17:31:00','2025-12-23 05:46:00','ADM002','Active',1),('DR018','fwairimu','$2y$10$.5zEctX2NZKOwhfIiqb5oukhExXb0JXAa8vUmqI1iBqsrL.vSiohm','Doctor','2025-10-26 18:40:00','2025-12-23 12:06:00','ADM002','Active',1),('DR019','iochieng','$2y$10$.5zEctX2NZKOwhfIiqb5oukhExXb0JXAa8vUmqI1iBqsrL.vSiohm','Doctor','2025-10-03 05:02:00','2025-12-19 19:01:00','ADM002','Active',1),('DR020','hmuthoni','$2y$10$.5zEctX2NZKOwhfIiqb5oukhExXb0JXAa8vUmqI1iBqsrL.vSiohm','Doctor','2025-11-13 07:37:00','2026-01-07 10:47:00','ADM002','Active',1),('LT001','hkiptoo','$2y$10$nPQrajb09KlW3iKOyg5X9ed1hUpYuT6hnurCFKdDXAaELROCKbHsy','Labtech','2025-11-10 22:20:00','2025-12-07 23:23:00','ADM004','Active',1),('LT002','iwanjiru','$2y$10$nPQrajb09KlW3iKOyg5X9ed1hUpYuT6hnurCFKdDXAaELROCKbHsy','Labtech','2025-12-09 11:02:00','2025-12-20 10:29:00','ADM004','Active',1),('LT003','tmuthoni','$2y$10$nPQrajb09KlW3iKOyg5X9ed1hUpYuT6hnurCFKdDXAaELROCKbHsy','Labtech','2025-11-28 04:00:00','2025-12-13 10:27:00','ADM004','Active',1),('LT004','emuthoni','$2y$10$nPQrajb09KlW3iKOyg5X9ed1hUpYuT6hnurCFKdDXAaELROCKbHsy','Labtech','2025-11-10 04:27:00','2026-01-29 05:54:00','ADM004','Active',1),('LT005','wmwangi','$2y$10$nPQrajb09KlW3iKOyg5X9ed1hUpYuT6hnurCFKdDXAaELROCKbHsy','Labtech','2025-12-09 05:38:00','2026-01-29 06:10:00','ADM004','Active',1),('LT006','fbarasa','$2y$10$nPQrajb09KlW3iKOyg5X9ed1hUpYuT6hnurCFKdDXAaELROCKbHsy','Labtech','2025-12-18 01:20:00','2025-12-19 15:50:00','ADM004','Active',1),('LT007','fmwangi','$2y$10$nPQrajb09KlW3iKOyg5X9ed1hUpYuT6hnurCFKdDXAaELROCKbHsy','Labtech','2025-10-27 14:52:00','2026-01-17 00:54:00','ADM004','Active',1),('LT008','lbarasa','$2y$10$nPQrajb09KlW3iKOyg5X9ed1hUpYuT6hnurCFKdDXAaELROCKbHsy','Labtech','2025-10-17 22:13:00','2026-01-28 23:45:00','ADM004','Active',1),('LT009','rgithinji','$2y$10$nPQrajb09KlW3iKOyg5X9ed1hUpYuT6hnurCFKdDXAaELROCKbHsy','Labtech','2025-12-14 00:52:00','2026-01-08 16:34:00','ADM004','Active',1),('LT010','vwairimu','$2y$10$nPQrajb09KlW3iKOyg5X9ed1hUpYuT6hnurCFKdDXAaELROCKbHsy','Labtech','2025-11-27 21:48:00','2025-12-16 12:51:00','ADM004','Active',1),('LT011','bwairimu','$2y$10$nPQrajb09KlW3iKOyg5X9ed1hUpYuT6hnurCFKdDXAaELROCKbHsy','Labtech','2025-11-03 10:02:00','2026-02-03 10:26:00','ADM004','Active',1),('LT012','tkorir','$2y$10$nPQrajb09KlW3iKOyg5X9ed1hUpYuT6hnurCFKdDXAaELROCKbHsy','Labtech','2025-12-15 08:17:00','2026-01-30 16:20:00','ADM004','Active',1),('LT013','ekamau','$2y$10$nPQrajb09KlW3iKOyg5X9ed1hUpYuT6hnurCFKdDXAaELROCKbHsy','Labtech','2025-12-11 07:35:00','2025-12-22 06:01:00','ADM004','Active',1),('LT014','jkariuki','$2y$10$nPQrajb09KlW3iKOyg5X9ed1hUpYuT6hnurCFKdDXAaELROCKbHsy','Labtech','2025-11-30 11:18:00','2025-12-06 08:44:00','ADM004','Active',1),('LT015','nkiptoo','$2y$10$nPQrajb09KlW3iKOyg5X9ed1hUpYuT6hnurCFKdDXAaELROCKbHsy','Labtech','2025-11-30 07:04:00','2025-12-28 02:50:00','ADM004','Active',1),('LT016','akamau','$2y$10$nPQrajb09KlW3iKOyg5X9ed1hUpYuT6hnurCFKdDXAaELROCKbHsy','Labtech','2025-12-04 21:10:00','2025-12-12 06:59:00','ADM004','Active',1),('LT017','fnjoroge','$2y$10$nPQrajb09KlW3iKOyg5X9ed1hUpYuT6hnurCFKdDXAaELROCKbHsy','Labtech','2025-11-04 18:54:00','2026-01-17 02:46:00','ADM004','Active',1),('LT018','rkariuki','$2y$10$nPQrajb09KlW3iKOyg5X9ed1hUpYuT6hnurCFKdDXAaELROCKbHsy','Labtech','2025-12-21 11:55:00','2026-01-05 04:00:00','ADM004','Active',1),('LT019','tkariuki','$2y$10$nPQrajb09KlW3iKOyg5X9ed1hUpYuT6hnurCFKdDXAaELROCKbHsy','Labtech','2026-01-06 19:31:00','2026-01-20 03:34:00','ADM004','Active',1),('LT020','imwangi','$2y$10$nPQrajb09KlW3iKOyg5X9ed1hUpYuT6hnurCFKdDXAaELROCKbHsy','Labtech','2025-10-21 19:52:00','2025-12-15 22:46:00','ADM004','Active',1),('NR001','gmaina2','$2y$10$iYaZQo78gKrBlOY4FR6ObuGuTqB/JYUtH17iWJibv9CVpxmgg2n.O','Nurse','2025-11-01 08:00:00','2026-01-29 00:03:00','ADM003','Active',1),('NR002','fmaina','$2y$10$iYaZQo78gKrBlOY4FR6ObuGuTqB/JYUtH17iWJibv9CVpxmgg2n.O','Nurse','2025-10-30 00:20:00','2025-12-05 14:14:00','ADM003','Active',1),('NR003','hsang','$2y$10$iYaZQo78gKrBlOY4FR6ObuGuTqB/JYUtH17iWJibv9CVpxmgg2n.O','Nurse','2025-11-25 04:34:00','2025-12-06 11:20:00','ADM003','Active',1),('NR004','gomondi','$2y$10$iYaZQo78gKrBlOY4FR6ObuGuTqB/JYUtH17iWJibv9CVpxmgg2n.O','Nurse','2025-11-08 00:06:00','2025-12-18 21:23:00','ADM003','Active',1),('NR005','cmumo','$2y$10$iYaZQo78gKrBlOY4FR6ObuGuTqB/JYUtH17iWJibv9CVpxmgg2n.O','Nurse','2025-11-11 19:06:00','2025-12-30 03:32:00','ADM003','Active',1),('NR006','kmuthoni','$2y$10$iYaZQo78gKrBlOY4FR6ObuGuTqB/JYUtH17iWJibv9CVpxmgg2n.O','Nurse','2025-11-05 02:06:00','2026-01-28 09:00:00','ADM003','Active',1),('NR007','fomondi','$2y$10$iYaZQo78gKrBlOY4FR6ObuGuTqB/JYUtH17iWJibv9CVpxmgg2n.O','Nurse','2025-11-14 17:46:00','2026-01-09 15:53:00','ADM003','Active',1),('NR008','mbarasa','$2y$10$iYaZQo78gKrBlOY4FR6ObuGuTqB/JYUtH17iWJibv9CVpxmgg2n.O','Nurse','2025-12-30 00:08:00','2026-01-30 05:41:00','ADM003','Active',1),('NR009','kkiptoo','$2y$10$iYaZQo78gKrBlOY4FR6ObuGuTqB/JYUtH17iWJibv9CVpxmgg2n.O','Nurse','2025-12-01 13:15:00','2026-01-28 19:20:00','ADM003','Active',1),('NR010','fmutiso','$2y$10$iYaZQo78gKrBlOY4FR6ObuGuTqB/JYUtH17iWJibv9CVpxmgg2n.O','Nurse','2025-12-28 17:50:00','2026-01-16 20:42:00','ADM003','Active',1),('NR011','dmwangi','$2y$10$iYaZQo78gKrBlOY4FR6ObuGuTqB/JYUtH17iWJibv9CVpxmgg2n.O','Nurse','2025-12-10 20:42:00','2026-01-28 19:23:00','ADM003','Active',1),('NR012','lkilonzo','$2y$10$iYaZQo78gKrBlOY4FR6ObuGuTqB/JYUtH17iWJibv9CVpxmgg2n.O','Nurse','2025-12-21 12:46:00','2025-12-12 08:01:00','ADM003','Active',1),('NR013','pkaranja','$2y$10$iYaZQo78gKrBlOY4FR6ObuGuTqB/JYUtH17iWJibv9CVpxmgg2n.O','Nurse','2025-10-24 01:54:00','2025-12-04 12:45:00','ADM003','Active',1),('NR014','monyango','$2y$10$iYaZQo78gKrBlOY4FR6ObuGuTqB/JYUtH17iWJibv9CVpxmgg2n.O','Nurse','2026-01-03 18:52:00','2025-12-16 13:47:00','ADM003','Active',1),('NR015','pkilonzo','$2y$10$iYaZQo78gKrBlOY4FR6ObuGuTqB/JYUtH17iWJibv9CVpxmgg2n.O','Nurse','2025-12-25 08:41:00','2026-01-16 03:12:00','ADM003','Active',1),('NR016','jotieno2','$2y$10$iYaZQo78gKrBlOY4FR6ObuGuTqB/JYUtH17iWJibv9CVpxmgg2n.O','Nurse','2025-12-11 18:18:00','2025-12-22 03:09:00','ADM003','Active',1),('NR017','ekariuki','$2y$10$iYaZQo78gKrBlOY4FR6ObuGuTqB/JYUtH17iWJibv9CVpxmgg2n.O','Nurse','2025-10-19 10:27:00','2025-12-29 03:59:00','ADM003','Active',1),('NR018','esang','$2y$10$iYaZQo78gKrBlOY4FR6ObuGuTqB/JYUtH17iWJibv9CVpxmgg2n.O','Nurse','2025-12-03 09:39:00','2026-01-21 16:27:00','ADM003','Active',1),('NR019','emwangi2','$2y$10$iYaZQo78gKrBlOY4FR6ObuGuTqB/JYUtH17iWJibv9CVpxmgg2n.O','Nurse','2025-10-27 22:24:00','2026-01-15 11:15:00','ADM003','Active',1),('NR020','dgithinji','$2y$10$iYaZQo78gKrBlOY4FR6ObuGuTqB/JYUtH17iWJibv9CVpxmgg2n.O','Nurse','2025-11-14 04:35:00','2025-12-15 18:40:00','ADM003','Active',1),('PT001','tkaranja','$2y$10$cHkPbfRZ02v54IVnrn9QoOpOeubh9Fkac6b9rBMR4ELJDYvqTLHyG','Patient','2026-01-05 17:39:00','2026-01-27 20:16:00','ADM005','Active',1),('PT002','dmumo2','$2y$10$cHkPbfRZ02v54IVnrn9QoOpOeubh9Fkac6b9rBMR4ELJDYvqTLHyG','Patient','2025-12-09 03:58:00','2026-02-05 15:44:00','ADM005','Active',1),('PT003','lbarasa2','$2y$10$cHkPbfRZ02v54IVnrn9QoOpOeubh9Fkac6b9rBMR4ELJDYvqTLHyG','Patient','2025-12-27 18:18:00','2026-01-08 08:41:00','ADM005','Active',1),('PT004','monyango2','$2y$10$cHkPbfRZ02v54IVnrn9QoOpOeubh9Fkac6b9rBMR4ELJDYvqTLHyG','Patient','2025-12-01 18:18:00','2026-02-09 13:26:00','ADM005','Active',1),('PT005','cnjoroge','$2y$10$cHkPbfRZ02v54IVnrn9QoOpOeubh9Fkac6b9rBMR4ELJDYvqTLHyG','Patient','2025-12-28 04:45:00','2026-01-13 10:24:00','ADM005','Active',1),('PT006','ikamau','$2y$10$cHkPbfRZ02v54IVnrn9QoOpOeubh9Fkac6b9rBMR4ELJDYvqTLHyG','Patient','2025-10-29 20:16:00','2026-01-15 00:34:00','ADM005','Active',1),('PT007','emumo','$2y$10$cHkPbfRZ02v54IVnrn9QoOpOeubh9Fkac6b9rBMR4ELJDYvqTLHyG','Patient','2025-12-13 23:34:00','2026-01-31 03:02:00','ADM005','Active',1),('PT008','kotieno','$2y$10$cHkPbfRZ02v54IVnrn9QoOpOeubh9Fkac6b9rBMR4ELJDYvqTLHyG','Patient','2025-11-14 00:54:00','2025-12-20 23:13:00','ADM005','Active',1),('PT009','jkamau','$2y$10$cHkPbfRZ02v54IVnrn9QoOpOeubh9Fkac6b9rBMR4ELJDYvqTLHyG','Patient','2025-10-29 11:35:00','2026-01-20 11:38:00','ADM005','Active',1),('PT010','ekorir','$2y$10$cHkPbfRZ02v54IVnrn9QoOpOeubh9Fkac6b9rBMR4ELJDYvqTLHyG','Patient','2025-11-15 15:46:00','2026-01-19 13:22:00','ADM005','Active',1),('PT011','ebarasa','$2y$10$cHkPbfRZ02v54IVnrn9QoOpOeubh9Fkac6b9rBMR4ELJDYvqTLHyG','Patient','2025-11-21 02:28:00','2026-01-21 19:25:00','ADM005','Active',1),('PT012','dcheruiyot','$2y$10$cHkPbfRZ02v54IVnrn9QoOpOeubh9Fkac6b9rBMR4ELJDYvqTLHyG','Patient','2025-11-04 21:43:00','2026-01-03 02:47:00','ADM005','Active',1),('PT013','egithinji','$2y$10$cHkPbfRZ02v54IVnrn9QoOpOeubh9Fkac6b9rBMR4ELJDYvqTLHyG','Patient','2025-11-14 09:22:00','2026-02-09 09:34:00','ADM005','Active',1),('PT014','mochieng','$2y$10$cHkPbfRZ02v54IVnrn9QoOpOeubh9Fkac6b9rBMR4ELJDYvqTLHyG','Patient','2025-12-09 17:19:00','2026-01-08 06:08:00','ADM005','Active',1),('PT015','cmoraa2','$2y$10$cHkPbfRZ02v54IVnrn9QoOpOeubh9Fkac6b9rBMR4ELJDYvqTLHyG','Patient','2025-11-02 23:25:00','2026-02-13 18:12:00','ADM005','Active',1),('PT016','mkamau2','$2y$10$cHkPbfRZ02v54IVnrn9QoOpOeubh9Fkac6b9rBMR4ELJDYvqTLHyG','Patient','2026-01-03 09:44:00','2026-02-13 01:41:00','ADM005','Active',1),('PT017','ekorir2','$2y$10$cHkPbfRZ02v54IVnrn9QoOpOeubh9Fkac6b9rBMR4ELJDYvqTLHyG','Patient','2026-01-18 13:10:00','2026-01-11 02:54:00','ADM005','Active',1),('PT018','iochieng2','$2y$10$cHkPbfRZ02v54IVnrn9QoOpOeubh9Fkac6b9rBMR4ELJDYvqTLHyG','Patient','2026-01-05 22:49:00','2026-02-08 14:11:00','ADM005','Active',1),('PT019','fonyango','$2y$10$cHkPbfRZ02v54IVnrn9QoOpOeubh9Fkac6b9rBMR4ELJDYvqTLHyG','Patient','2025-11-14 13:42:00','2026-01-14 04:10:00','ADM005','Active',1),('PT020','tgithinji','$2y$10$cHkPbfRZ02v54IVnrn9QoOpOeubh9Fkac6b9rBMR4ELJDYvqTLHyG','Patient','2025-12-14 20:23:00','2026-01-26 19:24:00','ADM005','Active',1),('PT021','patientjim','$2y$10$UbIyKU212LEnUneGkAhTi.6Viv2n6C1vwIMoCy7BtSuJEu7TqXwW.','Patient','2026-03-20 19:05:21',NULL,NULL,'Active',0),('PT022','WilliamMirugi','$2y$10$XTm8Npwvu6dGuZhpadNVm.0n1EkRPjyr.IYIB77x2c.c/1CGlIxUe','Patient','2026-06-15 16:26:52',NULL,NULL,'Active',0);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-23 20:17:32
