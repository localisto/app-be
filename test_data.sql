-- MySQL dump 10.13  Distrib 5.1.51, for apple-darwin10.3.0 (i386)
--
-- Host: localhost    Database: localisto
-- ------------------------------------------------------
-- Server version	5.1.51

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `agency`
--

LOCK TABLES `agency` WRITE;
/*!40000 ALTER TABLE `agency` DISABLE KEYS */;
INSERT INTO `agency` VALUES (1,'Feet First',1,1,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(2,'Waterfront Seattle.org',1,2,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(3,'City of Seattle',1,3,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(4,'Sound Transit',1,4,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(5,'Seattle Public Schools',0,5,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(6,'City of Bellevue',0,6,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(7,'City of Kirkland',0,7,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(8,'City of Mercer Island',0,8,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(9,'City of Shoreline',0,9,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(10,'Community Associations Institute',0,10,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(11,'Greenways Coalition',0,11,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(12,'Seattle City Lights',0,12,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(13,'Washington State Homeowners Association',0,13,'0000-00-00 00:00:00','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `agency` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `project`
--

LOCK TABLES `project` WRITE;
/*!40000 ALTER TABLE `project` DISABLE KEYS */;
INSERT INTO `project` VALUES (1,1,'Walk and Talk - U District','','1305 NE 43rd Street, Seattle, WA 98105','47.659746,-122.314009','2012-10-24 11:00:00',NULL,'Tue Oct 18, 3:30-6:30 pm',NULL,0,NULL,NULL,NULL,NULL,NULL,'http://localisto.org/somepage','0000-00-00 00:00:00','0000-00-00 00:00:00'),(2,1,'Waterfront Seattle Ped and Bike Design','','314 First Avenue South Seattle WA 98104 USA','47.599584,-122.33401','2012-08-24 00:00:00','2012-08-20 07:00:00','Oct 20, 5:00-8:00 pm',NULL,0,'1.2.gif',NULL,NULL,NULL,NULL,NULL,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(3,1,'Walking Kids to School Safely at NE 50th','','5031 University Way Northeast, Seattle, WA 98105, United States','47.665721,-122.313366',NULL,'2012-11-01 05:00:00','Nov 1, 4:00-6:00 pm',NULL,0,'1.3.gif',NULL,NULL,NULL,NULL,NULL,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(4,2,'Toward a Great Waterfront','','1119 8th AvenueÂ Â Seattle, WA 98101, United States','47.609027,-122.329953',NULL,'2013-05-19 08:30:00','May 19th, 6:30 pm',NULL,0,'2.1.gif',NULL,NULL,NULL,NULL,NULL,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(5,2,'Clean & Fishable Waters Day','','1951 Alaskan Way, Seattle, Washington, United States','47.609034,-122.344648',NULL,'2013-06-21 00:00:00','June 21, 10:00-5:00 pm',NULL,0,'2.2.gif',NULL,NULL,NULL,NULL,NULL,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(6,2,'Waterfront into Focus','','1119 8th AvenueÂ Â Seattle, WA 98101, United States','47.609027,-122.329953',NULL,'2013-07-12 07:30:00','July 12, 5:30 pm',NULL,0,'2.3.gif',NULL,NULL,NULL,NULL,NULL,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(7,3,'Design Review Meeting - South East','',NULL,'47.636383,-122.357988',NULL,'2012-09-28 06:30:00','Aug 28, 6:30 pm',NULL,0,'3.1.gif',NULL,NULL,NULL,NULL,NULL,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(8,3,'Elliot Bay Seawall \"Pier Peer\" People of Puget Sound','','1951 Alaskan Way, Seattle, Washington, United States','47.609034,-122.344648',NULL,'2012-09-26 07:30:00',NULL,NULL,0,'3.2.gif',NULL,NULL,NULL,NULL,NULL,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(9,3,'First Hill Street Car','',NULL,NULL,'2012-10-01 12:00:00',NULL,NULL,NULL,0,'3.3.gif',NULL,NULL,NULL,NULL,NULL,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(10,3,'Elliot Bay Seawall Stakeholders Group Meeting #12','','600 Fourth Avenue, Seattle, WA 98104','47.603828,-122.330056',NULL,'2012-11-15 06:15:00','Nov 15, 5:15-7:15 pm',NULL,0,'3.4.gif',NULL,NULL,NULL,NULL,NULL,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(11,4,'Tacoma Link Expansion','','1602 Martin Luther King Jr Way,Tacoma, WA 98405, USA','47.246292,-122.45087',NULL,'2012-08-22 06:00:00','Aug 22, 4:00-7:00 pm',NULL,0,'4.1.gif',NULL,NULL,NULL,NULL,NULL,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(12,4,'University Link Expansion','','1833 Broadway, Seattle, WA, United States','47.618563,-122.321022','2012-09-05 06:00:00',NULL,'Sept 5, 4:00-7:30 pm',NULL,0,'4.2.gif',NULL,NULL,NULL,NULL,NULL,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(13,4,'East Link Expansion','East Link is moving forward\n\nWith help from you and the Eastside community, in the last year Sound Transit has reached several significant milestones on the East Link Project. This included selecting the project to be built as shown on map above and furthering our partnerships with local governments, stakeholders and residents so that the project can move forward into final design.\nStay informed! You can receive, notices of upcoming meetings and updates on this project using our free e-mail at soundtransit.org/eastlink.\n\nOn November 15, 2011 the City of Bellevue and Sound Transit executed a Memorandum of Understanding (MOU) which establishes a collaborative framework for Sound Transit and the City to share the additional cost of a tunnel in downtown Bellevue. The MOU establishes a funding commitment from the City of Bellevue for up to $160 million, identifies the Cityâ€™s preferred design for the alignment along 112th Avenue Southeast, and commits Sound Transit to review and consider cost- saving design changes.\n           \nThe MOU charged Sound Transit and the City of Bellevue with identifying project cost-savings. Sound Transit and City staff worked collaboratively with an Independent Expert Review Panel to develop cost-savings ideas. This document highlights the public involvement opportunities during the cost-savings process and summarizes public feedback on the following cost- savings ideas with potential changes to the MOU project description.\n                       \nOverview\n                       \nSound Transit and the City of Bellevue co-hosted two open houses and over a dozen stakeholder briefings to inform the public of the cost-savings concepts and engage stakeholders in the decision-making process.\n                       \nPublic comments were accepted in-person at the open houses and stakeholder briefings, and by mail and email. Sound Transit and the City of Bellevue received approximately 370 comments throughout the cost-savings process, all of which have been forwarded to the Sound Transit Board of Directors and Bellevue City Council. The Sound Transit Board and Bellevue\nCity Council will weigh public feedback along with environmental considerations and engineering findings to determine which ideas to advance for further development. All meeting materials and graphics are available on Sound Transitâ€™s East Link project website www.soundtransit.org/eastlink and linked from the Cityâ€™s website as well.\n                       \nNotification\n                       \nSound Transit and the City of Bellevue collaborated to spread the word about the cost-savings process and opportunities for public involvement. For each open house there was broad notification through the following channels:\n\n    Display advertisements in the Bellevue Reporter, Seattle Transit Blog, La Raza, Seattle Chinese Post, Publicola.net, and BellevuePatch.com\n    Postcards mailed to 31,201 eastside residents and businesses\n    A press release to local papers and blogs\n    Email notification to 5,041 subscribers of the East Link project listserv, subscribers of the Bellevue Gov Alert, neighborhood newsletter, and other agency or community group listservs\n    Announcements on the City of Bellevue and Sound Transit project web pages\n    Sandwich boards displayed at key locations in Bellevue (Prior to the first open house)\n    Posters distributed to community locations','450 110th Avenue Northeast  Bellevue, WA 98004, United States','47.614351,-122.191873',NULL,'2012-10-19 06:00:00','Oct 19, 5:00-7:00 pm',NULL,0,'4.3.gif',NULL,NULL,NULL,NULL,'http://localisto.org/civic-topics/east-link-project','0000-00-00 00:00:00','0000-00-00 00:00:00'),(14,5,'Regular Board Meeting','','2445 3rd Ave. S., Seattle, WA 98124-1165','47.580388,-122.330037',NULL,'2012-09-04 07:00:00','Sept 4, 5:00 pm',NULL,0,'5.1.gif',NULL,NULL,NULL,NULL,NULL,'0000-00-00 00:00:00','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `project` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `project_image`
--

LOCK TABLES `project_image` WRITE;
/*!40000 ALTER TABLE `project_image` DISABLE KEYS */;
INSERT INTO `project_image` VALUES (1,13,'1.gif','','','','1.gif',2),(2,13,'2.gif','','','','2.gif',1);
/*!40000 ALTER TABLE `project_image` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `question`
--

LOCK TABLES `question` WRITE;
/*!40000 ALTER TABLE `question` DISABLE KEYS */;
INSERT INTO `question` VALUES (20,'2',13,NULL,NULL,NULL,NULL,'','Choose the design that you like best for the downtown Bellevue station.',NULL,1),(21,'1',13,NULL,NULL,NULL,NULL,'4.png','Choose the station name for this location.',NULL,2),(22,'3',13,NULL,NULL,NULL,NULL,'','What is your biggest concern with the East Link Light Rail Project?',4,3);
/*!40000 ALTER TABLE `question` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `answer`
--

LOCK TABLES `answer` WRITE;
/*!40000 ALTER TABLE `answer` DISABLE KEYS */;
INSERT INTO `answer` VALUES (1,20,NULL,'1.jpg',NULL,1,NULL,NULL,NULL,'2012-09-06 04:56:43','0000-00-00 00:00:00'),(2,20,NULL,'2.jpg',NULL,2,NULL,NULL,NULL,'2012-09-06 04:56:43','0000-00-00 00:00:00'),(3,20,NULL,'3.jpg',NULL,3,NULL,NULL,NULL,'2012-09-06 04:56:43','0000-00-00 00:00:00'),(4,21,NULL,NULL,'Mercer Slough Station',1,NULL,NULL,NULL,'2012-09-06 04:59:12','0000-00-00 00:00:00'),(5,21,NULL,NULL,'South Bellevue Station',2,NULL,NULL,NULL,'2012-09-06 04:59:12','0000-00-00 00:00:00'),(6,21,NULL,NULL,'Beaux Arts Station',3,NULL,NULL,NULL,'2012-09-06 04:59:12','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `answer` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-09-06 15:10:13
