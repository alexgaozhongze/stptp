-- MySQL dump 10.13  Distrib 5.5.58, for Linux (x86_64)
--
-- Host: localhost    Database: steam
-- ------------------------------------------------------
-- Server version	5.5.58-log

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
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `category` (
  `p_id` int(10) DEFAULT NULL COMMENT '平台id',
  `c_id` varchar(50) NOT NULL DEFAULT '' COMMENT '分类id',
  `c_name` varchar(50) NOT NULL DEFAULT '' COMMENT '分类名称',
  `c_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '数据记录状态，0为废弃，1为有效',
  KEY `p_id` (`p_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='分类';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category`
--

LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
INSERT INTO `category` VALUES (1,'1793pv37tad5','pubg',0),(1,'1793nldoml6o','csgo',1),(1,'17gbn7in0p27','h1z1',0);
/*!40000 ALTER TABLE `category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `good`
--

DROP TABLE IF EXISTS `good`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `good` (
  `p_id` int(10) DEFAULT NULL COMMENT '平台id',
  `g_id` bigint(11) DEFAULT NULL COMMENT '商品id',
  `g_keyword` varchar(50) NOT NULL DEFAULT '' COMMENT '关键词',
  `g_buy_mdiff_price` decimal(10,2) DEFAULT '0.00' COMMENT '销售求购最低差价',
  `g_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '数据记录状态，0为废弃，1为有效',
  KEY `g_id` (`g_id`),
  KEY `g_status` (`g_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `good`
--

LOCK TABLES `good` WRITE;
/*!40000 ALTER TABLE `good` DISABLE KEYS */;
INSERT INTO `good` VALUES (1,2151568372,'九头蛇大行动',0.30,1);
/*!40000 ALTER TABLE `good` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `platform`
--

DROP TABLE IF EXISTS `platform`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `platform` (
  `p_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '平台id',
  `p_name` varchar(50) NOT NULL DEFAULT '' COMMENT '平台名称',
  `p_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '数据记录状态，0为废弃，1为有效',
  PRIMARY KEY (`p_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='steam第三方交易平台';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `platform`
--

LOCK TABLES `platform` WRITE;
/*!40000 ALTER TABLE `platform` DISABLE KEYS */;
INSERT INTO `platform` VALUES (1,'stmbuy',1);
/*!40000 ALTER TABLE `platform` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product`
--

DROP TABLE IF EXISTS `product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product` (
  `p_id` int(10) NOT NULL DEFAULT '0' COMMENT '平台id',
  `g_id` bigint(11) NOT NULL DEFAULT '0' COMMENT '商品id',
  `pdt_id` bigint(11) NOT NULL DEFAULT '0' COMMENT '货品id',
  `pdt_cost_price` decimal(10,2) DEFAULT NULL COMMENT '成本价',
  `pdt_sale_price` decimal(10,2) DEFAULT NULL COMMENT '销售价',
  `pdt_sale_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '销售状态, 0:未售; 1:销售中; 2:已售出',
  `pdt_data_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '数据记录状态，0:废弃，1:有效',
  PRIMARY KEY (`p_id`,`g_id`,`pdt_id`),
  KEY `pdt_sale_status` (`pdt_sale_status`),
  KEY `pdt_data_status` (`pdt_data_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='货品';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product`
--

LOCK TABLES `product` WRITE;
/*!40000 ALTER TABLE `product` DISABLE KEYS */;
/*!40000 ALTER TABLE `product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'steam'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-05-11 11:51:41
