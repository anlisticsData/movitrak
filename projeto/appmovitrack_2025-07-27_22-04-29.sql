# ************************************************************
# Antares - SQL Client
# Version 0.7.35
# 
# https://antares-sql.app/
# https://github.com/antares-sql/antares
# 
# Host: 192.168.15.49 ((Ubuntu) 8.0.42)
# Database: appmovitrack
# Generation time: 2025-07-27T22:04:38-03:00
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
SET NAMES utf8mb4;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table cameras
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cameras`;

CREATE TABLE `cameras` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `conexao_type` varchar(40) DEFAULT NULL,
  `conexao` varchar(250) DEFAULT NULL,
  `fk_user` bigint NOT NULL,
  `state` tinyint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cameras_users_FK` (`fk_user`),
  CONSTRAINT `cameras_users_FK` FOREIGN KEY (`fk_user`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb3;

LOCK TABLES `cameras` WRITE;
/*!40000 ALTER TABLE `cameras` DISABLE KEYS */;

INSERT INTO `cameras` (`id`, `name`, `conexao_type`, `conexao`, `fk_user`, `state`) VALUES
	(1, "CAM1-2025", "IP", "rtsp://admin:%40Dev1234@heb08t8gt73.sn.mynetname.net:554/cam/realmonitor?channel=1&subtype=0", 4, 1),
	(12, "CAM-01", "IP", "*", 6, 1),
	(13, "principal", "IP", "*", 7, 1),
	(14, "esqueda", "IP", "*", 7, 1);

/*!40000 ALTER TABLE `cameras` ENABLE KEYS */;
UNLOCK TABLES;



# Dump of table companies
# ------------------------------------------------------------

DROP TABLE IF EXISTS `companies`;

CREATE TABLE `companies` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `isactive` tinyint DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3;

LOCK TABLES `companies` WRITE;
/*!40000 ALTER TABLE `companies` DISABLE KEYS */;

INSERT INTO `companies` (`id`, `name`, `isactive`) VALUES
	(2, "analistics", 1),
	(3, "analistics 2", 1),
	(4, "edi@a.com", 1),
	(5, "Confitec", 1);

/*!40000 ALTER TABLE `companies` ENABLE KEYS */;
UNLOCK TABLES;



# Dump of table moviment_vacancies
# ------------------------------------------------------------

DROP TABLE IF EXISTS `moviment_vacancies`;

CREATE TABLE `moviment_vacancies` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `ip` varchar(100) DEFAULT NULL,
  `fk_camera` bigint DEFAULT NULL,
  `fk_vacancie` bigint DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `state` tinyint DEFAULT NULL,
  `file_path` varchar(250) DEFAULT NULL,
  `placa` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `moviment_vacancies_cameras_FK` (`fk_camera`),
  KEY `moviment_vacancies_vacancies_FK` (`fk_vacancie`),
  CONSTRAINT `moviment_vacancies_cameras_FK` FOREIGN KEY (`fk_camera`) REFERENCES `cameras` (`id`),
  CONSTRAINT `moviment_vacancies_vacancies_FK` FOREIGN KEY (`fk_vacancie`) REFERENCES `vacancies` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=354 DEFAULT CHARSET=utf8mb3;

LOCK TABLES `moviment_vacancies` WRITE;
/*!40000 ALTER TABLE `moviment_vacancies` DISABLE KEYS */;

INSERT INTO `moviment_vacancies` (`id`, `ip`, `fk_camera`, `fk_vacancie`, `created_at`, `state`, `file_path`, `placa`) VALUES
	(85, "192.168.15.1", 1, 12, "2025-07-17 16:56:33", 1, "/uploads/moviments/moviment_1752782193_6879557184e5a.jpg", "**"),
	(86, "192.168.15.1", 1, 13, "2025-07-17 16:56:33", 1, "/uploads/moviments/moviment_1752782193_6879557195ac3.jpg", "**"),
	(87, "192.168.15.1", 1, 14, "2025-07-17 16:56:33", 1, "/uploads/moviments/moviment_1752782193_68795571a61e7.jpg", "**"),
	(88, "192.168.15.1", 1, 14, "2025-07-17 16:58:30", 1, "/uploads/moviments/moviment_1752782310_687955e6cc822.jpg", "**"),
	(89, "192.168.15.1", 1, 14, "2025-07-17 17:01:15", 1, "/uploads/moviments/moviment_1752782475_6879568b692e8.jpg", "**"),
	(90, "192.168.15.1", 1, 12, "2025-07-17 17:01:35", 1, "/uploads/moviments/moviment_1752782495_6879569f8d0d2.jpg", "**"),
	(91, "192.168.15.1", 1, 13, "2025-07-17 17:01:35", 1, "/uploads/moviments/moviment_1752782495_6879569fa087a.jpg", "**"),
	(92, "192.168.15.1", 1, 14, "2025-07-17 17:01:35", 1, "/uploads/moviments/moviment_1752782495_6879569fb938b.jpg", "**"),
	(93, "192.168.15.1", 1, 14, "2025-07-17 17:07:01", 1, "/uploads/moviments/moviment_1752782821_687957e544fa8.jpg", "**"),
	(94, "192.168.15.1", 1, 13, "2025-07-17 17:07:02", 1, "/uploads/moviments/moviment_1752782822_687957e60c916.jpg", "**"),
	(95, "192.168.15.1", 1, 12, "2025-07-17 17:07:02", 1, "/uploads/moviments/moviment_1752782822_687957e64d58a.jpg", "**"),
	(96, "192.168.15.1", 1, 14, "2025-07-17 17:12:59", 1, "/uploads/moviments/moviment_1752783179_6879594b03c0d.jpg", "**"),
	(97, "192.168.15.1", 1, 13, "2025-07-17 17:12:59", 1, "/uploads/moviments/moviment_1752783179_6879594b74154.jpg", "**"),
	(98, "192.168.15.1", 1, 12, "2025-07-17 17:12:59", 1, "/uploads/moviments/moviment_1752783179_6879594b93510.jpg", "**"),
	(99, "192.168.15.1", 12, 12, "2025-07-17 17:26:00", 1, "/uploads/moviments/moviment_1752783960_68795c58ae68f.jpg", "**"),
	(100, "192.168.15.1", 12, 13, "2025-07-17 17:26:00", 1, "/uploads/moviments/moviment_1752783960_68795c58c0a71.jpg", "**"),
	(101, "192.168.15.1", 12, 14, "2025-07-17 17:26:00", 1, "/uploads/moviments/moviment_1752783960_68795c58d2c3e.jpg", "**"),
	(102, "192.168.15.1", 12, 14, "2025-07-17 17:28:51", 1, "/uploads/moviments/moviment_1752784131_68795d03343ea.jpg", "**"),
	(103, "192.168.15.1", 12, 13, "2025-07-17 17:30:22", 0, "/uploads/moviments/moviment_1752784222_68795d5e03b00.jpg", "**"),
	(104, "192.168.15.1", 12, 14, "2025-07-17 17:33:51", 1, "/uploads/moviments/moviment_1752784431_68795e2f6cc6f.jpg", "**"),
	(105, "192.168.15.1", 12, 14, "2025-07-17 17:43:31", 1, "/uploads/moviments/moviment_1752785011_68796073017fe.jpg", "**"),
	(106, "192.168.15.1", 12, 12, "2025-07-17 17:55:54", 1, "/uploads/moviments/moviment_1752785754_6879635a3132f.jpg", NULL),
	(107, "192.168.15.1", 12, 14, "2025-07-17 17:55:54", 1, "/uploads/moviments/moviment_1752785754_6879635af28e3.jpg", NULL),
	(108, "192.168.15.1", 12, 13, "2025-07-17 17:57:30", 0, "/uploads/moviments/moviment_1752785850_687963ba29de4.jpg", "**"),
	(109, "192.168.15.1", 12, 14, "2025-07-17 18:04:06", 1, "/uploads/moviments/moviment_1752786246_687965462c6f1.jpg", NULL),
	(110, "192.168.15.1", 12, 12, "2025-07-17 18:15:25", 1, "/uploads/moviments/moviment_1752786925_687967ed14bf4.jpg", NULL),
	(111, "192.168.15.1", 12, 14, "2025-07-17 18:15:25", 1, "/uploads/moviments/moviment_1752786925_687967edf165a.jpg", NULL),
	(112, "192.168.15.1", 12, 14, "2025-07-17 18:17:59", 1, "/uploads/moviments/moviment_1752787079_687968874a33f.jpg", NULL),
	(113, "192.168.15.1", 12, 14, "2025-07-17 18:23:40", 1, "/uploads/moviments/moviment_1752787420_687969dc7acce.jpg", NULL),
	(114, "192.168.15.1", 12, 12, "2025-07-17 18:23:41", 1, "/uploads/moviments/moviment_1752787421_687969ddbbca7.jpg", NULL),
	(115, "192.168.15.1", 12, 13, "2025-07-17 18:30:31", 1, "/uploads/moviments/moviment_1752787831_68796b77d7637.jpg", NULL),
	(116, "192.168.15.1", 12, 13, "2025-07-17 18:32:32", 0, "/uploads/moviments/moviment_1752787952_68796bf011620.jpg", "**"),
	(117, "192.168.15.1", 12, 12, "2025-07-17 18:37:05", 1, "/uploads/moviments/moviment_1752788225_68796d016997d.jpg", NULL),
	(118, "192.168.15.1", 12, 14, "2025-07-17 18:37:07", 1, "/uploads/moviments/moviment_1752788227_68796d032ca8b.jpg", NULL),
	(119, "192.168.15.1", 12, 13, "2025-07-17 18:38:41", 0, "/uploads/moviments/moviment_1752788321_68796d6179ee1.jpg", "**"),
	(120, "192.168.15.1", 12, 14, "2025-07-17 18:56:28", 1, "/uploads/moviments/moviment_1752789388_6879718c862cc.jpg", NULL),
	(121, "192.168.15.1", 12, 12, "2025-07-17 18:56:31", 1, "/uploads/moviments/moviment_1752789391_6879718f182f5.jpg", NULL),
	(122, "192.168.15.1", 12, 14, "2025-07-17 19:17:24", 1, "/uploads/moviments/moviment_1752790644_68797674e9c3d.jpg", NULL),
	(123, "192.168.15.1", 12, 14, "2025-07-17 19:24:53", 1, "/uploads/moviments/moviment_1752791093_687978354dc46.jpg", NULL),
	(124, "192.168.15.1", 12, 13, "2025-07-17 19:35:52", 1, "/uploads/moviments/moviment_1752791752_68797ac89c957.jpg", NULL),
	(125, "192.168.15.1", 12, 13, "2025-07-17 19:36:16", 1, "/uploads/moviments/moviment_1752791776_68797ae08bfc1.jpg", NULL),
	(126, "192.168.15.1", 12, 14, "2025-07-17 19:36:32", 1, "/uploads/moviments/moviment_1752791792_68797af036284.jpg", NULL),
	(127, "192.168.15.1", 12, 13, "2025-07-17 19:37:58", 0, "/uploads/moviments/moviment_1752791878_68797b4695ede.jpg", "**"),
	(128, "192.168.15.1", 12, 12, "2025-07-17 19:38:06", 0, "/uploads/moviments/moviment_1752791886_68797b4ee62a0.jpg", "**"),
	(129, "192.168.15.1", 12, 14, "2025-07-17 19:41:45", 1, "/uploads/moviments/moviment_1752792105_68797c294b08e.jpg", NULL),
	(130, "192.168.15.1", 12, 14, "2025-07-17 19:56:27", 1, "/uploads/moviments/moviment_1752792987_68797f9bd388c.jpg", NULL),
	(131, "192.168.15.1", 12, 12, "2025-07-17 21:06:48", 1, "/uploads/moviments/moviment_1752797208_687990181e28b.jpg", NULL),
	(132, "192.168.15.1", 12, 12, "2025-07-17 21:08:30", 0, "/uploads/moviments/moviment_1752797310_6879907eb30de.jpg", "**"),
	(133, "192.168.15.1", 12, 13, "2025-07-17 21:46:42", 1, "/uploads/moviments/moviment_1752799602_687999728e2c4.jpg", NULL),
	(134, "192.168.15.1", 12, 12, "2025-07-17 21:46:43", 1, "/uploads/moviments/moviment_1752799603_68799973f0ff0.jpg", NULL),
	(135, "192.168.15.1", 12, 14, "2025-07-17 21:46:44", 1, "/uploads/moviments/moviment_1752799604_68799974d44f9.jpg", NULL),
	(136, "192.168.15.1", 12, 13, "2025-07-17 21:48:23", 0, "/uploads/moviments/moviment_1752799703_687999d7b303a.jpg", "**"),
	(137, "192.168.15.1", 12, 12, "2025-07-17 21:48:24", 0, "/uploads/moviments/moviment_1752799704_687999d84b225.jpg", "**"),
	(138, "179.119.113.114", 12, 14, "2025-07-18 05:09:02", 1, "/uploads/moviments/moviment_1752826142_687a011e1bb23.jpg", NULL),
	(139, "179.119.113.114", 12, 12, "2025-07-18 05:10:39", 0, "/uploads/moviments/moviment_1752826239_687a017f0f737.jpg", "**"),
	(140, "179.119.113.114", 12, 13, "2025-07-18 05:10:39", 0, "/uploads/moviments/moviment_1752826239_687a017f30dee.jpg", "**"),
	(141, "179.119.113.196", 12, 14, "2025-07-18 07:24:23", 1, "/uploads/moviments/moviment_1752834263_687a20d7ba005.jpg", "**"),
	(142, "179.119.113.196", 12, 12, "2025-07-18 07:24:24", 1, "/uploads/moviments/moviment_1752834264_687a20d8c2478.jpg", "**"),
	(143, "179.119.113.196", 12, 13, "2025-07-18 07:24:25", 1, "/uploads/moviments/moviment_1752834265_687a20d99d16b.jpg", "**"),
	(144, "179.119.113.196", 12, 13, "2025-07-18 07:24:49", 1, "/uploads/moviments/moviment_1752834289_687a20f1a4e3b.jpg", "**"),
	(145, "179.119.113.196", 12, 12, "2025-07-18 07:24:53", 1, "/uploads/moviments/moviment_1752834293_687a20f5912a5.jpg", "**"),
	(146, "179.119.113.196", 12, 14, "2025-07-18 07:25:21", 1, "/uploads/moviments/moviment_1752834321_687a211144911.jpg", "**"),
	(147, "179.119.113.196", 12, 13, "2025-07-18 07:25:57", 1, "/uploads/moviments/moviment_1752834357_687a21359f909.jpg", "**"),
	(148, "179.119.113.196", 12, 12, "2025-07-18 07:26:07", 1, "/uploads/moviments/moviment_1752834367_687a213f1fabb.jpg", "EUN3756"),
	(149, "179.119.113.196", 12, 12, "2025-07-18 07:26:36", 1, "/uploads/moviments/moviment_1752834396_687a215c50e7d.jpg", "**"),
	(150, "179.119.113.196", 12, 14, "2025-07-18 07:26:37", 1, "/uploads/moviments/moviment_1752834397_687a215d167da.jpg", "**"),
	(151, "179.119.113.196", 12, 13, "2025-07-18 07:26:38", 1, "/uploads/moviments/moviment_1752834398_687a215e09c6b.jpg", "**"),
	(152, "179.119.113.196", 12, 12, "2025-07-18 07:28:16", 1, "/uploads/moviments/moviment_1752834496_687a21c0ef75e.jpg", "**"),
	(153, "179.119.113.196", 12, 13, "2025-07-18 07:28:40", 1, "/uploads/moviments/moviment_1752834520_687a21d88afd3.jpg", "**"),
	(154, "179.119.113.196", 12, 12, "2025-07-18 07:28:42", 1, "/uploads/moviments/moviment_1752834522_687a21dae701b.jpg", "**"),
	(155, "179.119.113.196", 12, 14, "2025-07-18 07:50:09", 1, "/uploads/moviments/moviment_1752835809_687a26e1111f7.jpg", NULL),
	(156, "179.119.113.196", 12, 14, "2025-07-18 07:56:03", 1, "/uploads/moviments/moviment_1752836163_687a2843840e2.jpg", NULL),
	(157, "179.119.113.196", 12, 14, "2025-07-18 07:58:01", 1, "/uploads/moviments/moviment_1752836281_687a28b99af85.jpg", NULL),
	(158, "179.119.113.196", 12, 12, "2025-07-18 07:58:02", 1, "/uploads/moviments/moviment_1752836282_687a28bab4376.jpg", NULL),
	(159, "179.119.113.196", 12, 13, "2025-07-18 07:58:03", 1, "/uploads/moviments/moviment_1752836283_687a28bb9a2d7.jpg", NULL),
	(160, "179.119.113.196", 12, 13, "2025-07-18 07:58:32", 1, "/uploads/moviments/moviment_1752836312_687a28d8eb4a9.jpg", "SUX0H23"),
	(161, "179.119.113.196", 12, 12, "2025-07-18 07:58:38", 1, "/uploads/moviments/moviment_1752836318_687a28de17d7d.jpg", NULL),
	(162, "179.119.113.196", 12, 14, "2025-07-18 07:59:08", 1, "/uploads/moviments/moviment_1752836348_687a28fc16e6e.jpg", NULL),
	(163, "179.119.113.196", 12, 12, "2025-07-18 07:59:33", 1, "/uploads/moviments/moviment_1752836373_687a2915632f4.jpg", NULL),
	(164, "179.119.113.196", 12, 13, "2025-07-18 07:59:34", 1, "/uploads/moviments/moviment_1752836374_687a2916602f7.jpg", NULL),
	(165, "179.119.113.196", 12, 13, "2025-07-18 07:59:46", 1, "/uploads/moviments/moviment_1752836386_687a292214e17.jpg", NULL),
	(166, "179.119.113.196", 12, 12, "2025-07-18 07:59:54", 1, "/uploads/moviments/moviment_1752836394_687a292aa44e7.jpg", NULL),
	(167, "179.119.113.196", 12, 12, "2025-07-18 08:00:15", 1, "/uploads/moviments/moviment_1752836415_687a293fb1d9e.jpg", NULL),
	(168, "179.119.113.196", 12, 14, "2025-07-18 08:00:16", 1, "/uploads/moviments/moviment_1752836416_687a294087177.jpg", NULL),
	(169, "179.119.113.196", 12, 13, "2025-07-18 08:00:17", 1, "/uploads/moviments/moviment_1752836417_687a29416acdf.jpg", NULL),
	(170, "179.119.113.196", 12, 12, "2025-07-18 08:01:31", 1, "/uploads/moviments/moviment_1752836491_687a298ba61c0.jpg", NULL),
	(171, "179.119.113.196", 12, 13, "2025-07-18 08:01:56", 1, "/uploads/moviments/moviment_1752836516_687a29a43cc04.jpg", NULL),
	(172, "179.119.113.196", 12, 12, "2025-07-18 08:01:58", 1, "/uploads/moviments/moviment_1752836518_687a29a6d0458.jpg", NULL),
	(173, "179.119.113.196", 12, 14, "2025-07-18 08:09:29", 1, "/uploads/moviments/moviment_1752836969_687a2b69657e2.jpg", NULL),
	(174, "179.119.113.196", 12, 12, "2025-07-18 08:09:30", 1, "/uploads/moviments/moviment_1752836970_687a2b6a7052c.jpg", NULL),
	(175, "179.119.113.196", 12, 13, "2025-07-18 08:09:31", 1, "/uploads/moviments/moviment_1752836971_687a2b6b499a1.jpg", NULL),
	(176, "179.119.113.196", 12, 13, "2025-07-18 08:09:53", 1, "/uploads/moviments/moviment_1752836993_687a2b81dd093.jpg", "SVX0H23"),
	(177, "179.119.113.196", 12, 12, "2025-07-18 08:09:58", 1, "/uploads/moviments/moviment_1752836998_687a2b8614a00.jpg", NULL),
	(178, "179.119.113.196", 12, 14, "2025-07-18 08:10:40", 1, "/uploads/moviments/moviment_1752837040_687a2bb0df6b7.jpg", NULL),
	(179, "179.119.113.196", 12, 13, "2025-07-18 08:11:13", 1, "/uploads/moviments/moviment_1752837073_687a2bd11e33e.jpg", NULL),
	(180, "179.119.113.196", 12, 12, "2025-07-18 08:11:16", 1, "/uploads/moviments/moviment_1752837076_687a2bd4d45fd.jpg", NULL),
	(181, "179.119.113.196", 12, 12, "2025-07-18 08:11:28", 1, "/uploads/moviments/moviment_1752837088_687a2be018d2e.jpg", NULL),
	(182, "179.119.113.196", 12, 12, "2025-07-18 08:11:39", 1, "/uploads/moviments/moviment_1752837099_687a2beb33e13.jpg", NULL),
	(183, "179.119.113.196", 12, 12, "2025-07-18 08:12:07", 1, "/uploads/moviments/moviment_1752837127_687a2c07e7276.jpg", NULL),
	(184, "179.119.113.196", 12, 14, "2025-07-18 08:12:08", 1, "/uploads/moviments/moviment_1752837128_687a2c08bf81e.jpg", NULL),
	(185, "179.119.113.196", 12, 13, "2025-07-18 08:12:10", 1, "/uploads/moviments/moviment_1752837130_687a2c0a6df77.jpg", NULL),
	(186, "179.119.113.196", 12, 12, "2025-07-18 08:13:35", 1, "/uploads/moviments/moviment_1752837215_687a2c5f54574.jpg", NULL),
	(187, "179.119.113.196", 12, 13, "2025-07-18 08:13:57", 1, "/uploads/moviments/moviment_1752837237_687a2c755cc32.jpg", NULL),
	(188, "179.119.113.196", 12, 12, "2025-07-18 08:14:00", 1, "/uploads/moviments/moviment_1752837240_687a2c78985b5.jpg", NULL),
	(189, "179.119.113.196", 12, 14, "2025-07-18 08:21:53", 1, "/uploads/moviments/moviment_1752837713_687a2e51af321.jpg", NULL),
	(190, "179.119.113.196", 13, 15, "2025-07-18 08:21:54", 1, "/uploads/moviments/moviment_1752837714_687a2e52ad510.jpg", NULL),
	(191, "179.119.113.196", 12, 13, "2025-07-18 08:21:55", 1, "/uploads/moviments/moviment_1752837715_687a2e53811b0.jpg", NULL),
	(192, "179.119.113.196", 12, 13, "2025-07-18 08:22:17", 1, "/uploads/moviments/moviment_1752837737_687a2e6943ba4.jpg", NULL),
	(193, "179.119.113.196", 13, 15, "2025-07-18 08:22:21", 1, "/uploads/moviments/moviment_1752837741_687a2e6d0f525.jpg", NULL),
	(194, "179.119.113.196", 12, 14, "2025-07-18 08:22:49", 1, "/uploads/moviments/moviment_1752837769_687a2e89d2660.jpg", NULL),
	(195, "179.119.113.196", 13, 15, "2025-07-18 08:23:15", 1, "/uploads/moviments/moviment_1752837795_687a2ea3d5d90.jpg", NULL),
	(196, "179.119.113.196", 12, 13, "2025-07-18 08:23:16", 1, "/uploads/moviments/moviment_1752837796_687a2ea4a92f7.jpg", NULL),
	(197, "179.119.113.196", 13, 15, "2025-07-18 08:23:34", 1, "/uploads/moviments/moviment_1752837814_687a2eb6f06e6.jpg", NULL),
	(198, "179.119.113.196", 13, 15, "2025-07-18 08:23:55", 1, "/uploads/moviments/moviment_1752837835_687a2ecb0fbaa.jpg", NULL),
	(199, "179.119.113.196", 12, 14, "2025-07-18 08:23:55", 1, "/uploads/moviments/moviment_1752837835_687a2ecbd73bb.jpg", NULL),
	(200, "179.119.113.196", 12, 13, "2025-07-18 08:23:57", 1, "/uploads/moviments/moviment_1752837837_687a2ecd0dadd.jpg", NULL),
	(201, "179.119.113.196", 13, 15, "2025-07-18 08:26:17", 1, "/uploads/moviments/moviment_1752837977_687a2f59c67f5.jpg", NULL),
	(202, "179.119.113.196", 13, 15, "2025-07-18 08:26:39", 1, "/uploads/moviments/moviment_1752837999_687a2f6fc2a12.jpg", NULL),
	(203, "179.119.113.196", 13, 15, "2025-07-18 08:27:29", 1, "/uploads/moviments/moviment_1752838049_687a2fa1eb6f3.jpg", NULL),
	(204, "179.119.113.196", 13, 15, "2025-07-18 08:28:04", 1, "/uploads/moviments/moviment_1752838084_687a2fc45ece7.jpg", NULL),
	(205, "179.119.113.196", 13, 15, "2025-07-18 08:29:05", 1, "/uploads/moviments/moviment_1752838145_687a3001cb8e9.jpg", NULL),
	(206, "179.119.113.196", 13, 15, "2025-07-18 08:29:21", 1, "/uploads/moviments/moviment_1752838161_687a30110aedb.jpg", NULL),
	(207, "179.119.113.196", 12, 14, "2025-07-18 08:57:00", 1, "/uploads/moviments/moviment_1752839820_687a368c43cdc.jpg", "**"),
	(208, "179.119.113.196", 12, 14, "2025-07-18 10:00:31", 1, "/uploads/moviments/moviment_1752843631_687a456ff04df.jpg", "**"),
	(209, "179.119.113.196", 12, 13, "2025-07-18 10:00:31", 1, "/uploads/moviments/moviment_1752843631_687a456ff04df.jpg", "DOR5893"),
	(212, "179.119.113.196", 13, 15, "2025-07-18 10:07:06", 1, "/uploads/moviments/moviment_1752844026_687a46fa03b74.jpg", "DQR5893"),
	(213, "179.119.113.196", 13, 15, "2025-07-18 12:04:59", 1, "/uploads/moviments/moviment_1752851099_687a629b696e6.jpg", "**"),
	(214, "179.119.113.196", 13, 15, "2025-07-18 12:54:43", 1, "/uploads/moviments/moviment_1752854083_687a6e43628fa.jpg", "DQR5893"),
	(215, "179.119.113.196", 13, 15, "2025-07-18 13:22:21", 1, "/uploads/moviments/moviment_1752855741_687a74bdb8672.jpg", "DQR5893"),
	(216, "179.119.113.196", 13, 15, "2025-07-18 13:23:27", 1, "/uploads/moviments/moviment_1752855807_687a74ff81039.jpg", "**"),
	(217, "179.119.113.196", 13, 15, "2025-07-18 13:24:50", 1, "/uploads/moviments/moviment_1752855890_687a7552c4cf2.jpg", "OOR5093"),
	(218, "179.119.113.196", 13, 15, "2025-07-18 13:31:02", 1, "/uploads/moviments/moviment_1752856262_687a76c6ad84d.jpg", "DQR5893"),
	(219, "179.119.113.196", 13, 15, "2025-07-18 13:32:35", 1, "/uploads/moviments/moviment_1752856355_687a772343d80.jpg", "**"),
	(220, "179.119.113.196", 13, 15, "2025-07-18 13:33:46", 1, "/uploads/moviments/moviment_1752856426_687a776a6b1b0.jpg", "DQR5893"),
	(221, "179.119.113.196", 13, 15, "2025-07-18 13:36:09", 1, "/uploads/moviments/moviment_1752856569_687a77f9aa4f2.jpg", "**"),
	(222, "179.119.113.196", 13, 15, "2025-07-18 13:36:37", 1, "/uploads/moviments/moviment_1752856597_687a781554671.jpg", "**"),
	(223, "179.119.113.196", 13, 15, "2025-07-18 13:38:04", 1, "/uploads/moviments/moviment_1752856684_687a786ca65bd.jpg", "**"),
	(224, "179.119.113.196", 13, 15, "2025-07-18 13:38:32", 1, "/uploads/moviments/moviment_1752856712_687a78888e17c.jpg", "**"),
	(225, "179.119.113.196", 13, 15, "2025-07-18 13:39:51", 1, "/uploads/moviments/moviment_1752856791_687a78d7e7a31.jpg", "DQR5893"),
	(226, "179.119.113.196", 13, 15, "2025-07-18 13:42:10", 1, "/uploads/moviments/moviment_1752856930_687a796272585.jpg", "**"),
	(227, "179.119.113.196", 13, 15, "2025-07-18 13:42:56", 1, "/uploads/moviments/moviment_1752856976_687a799079354.jpg", "**"),
	(228, "179.119.113.196", 13, 15, "2025-07-18 13:44:25", 1, "/uploads/moviments/moviment_1752857065_687a79e9abd36.jpg", "DQR5893"),
	(229, "179.119.113.196", 13, 15, "2025-07-18 13:46:26", 1, "/uploads/moviments/moviment_1752857186_687a7a62a230b.jpg", "DQR5893"),
	(230, "179.119.113.196", 13, 15, "2025-07-18 13:49:09", 1, "/uploads/moviments/moviment_1752857349_687a7b054c96f.jpg", "**"),
	(231, "179.119.113.196", 13, 15, "2025-07-18 13:50:10", 1, "/uploads/moviments/moviment_1752857410_687a7b4210a1b.jpg", "DQR5893"),
	(234, "179.119.113.196", 13, 15, "2025-07-18 13:54:17", 1, "/uploads/moviments/moviment_1752857657_687a7c3930249.jpg", "**"),
	(235, "179.119.113.196", 13, 15, "2025-07-18 13:55:28", 1, "/uploads/moviments/moviment_1752857728_687a7c80b04c8.jpg", "DQR5893"),
	(236, "179.119.113.196", 13, 15, "2025-07-18 13:56:17", 1, "/uploads/moviments/moviment_1752857777_687a7cb14c0e5.jpg", "DQR5893"),
	(237, "179.119.113.196", 13, 15, "2025-07-18 13:57:27", 1, "/uploads/moviments/moviment_1752857847_687a7cf7232bc.jpg", "DQR5893"),
	(238, "179.119.113.196", 13, 15, "2025-07-18 14:03:51", 1, "/uploads/moviments/moviment_1752858231_687a7e77e4648.jpg", "DQR5893"),
	(239, "179.119.113.196", 13, 15, "2025-07-18 15:22:00", 1, "/uploads/moviments/moviment_1752862920_687a90c8c5b1e.jpg", "**"),
	(240, "179.119.113.196", 13, 15, "2025-07-18 15:53:23", 1, "/uploads/moviments/moviment_1752864803_687a982326430.jpg", "DQR5893"),
	(241, "192.168.15.1", 13, 15, "2025-07-20 15:50:43", 1, "/uploads/moviments/moviment_1753037443_687d3a830ecbd.jpg", "**"),
	(242, "192.168.15.1", 13, 15, "2025-07-20 15:51:08", 1, "/uploads/moviments/moviment_1753037468_687d3a9cd5dcd.jpg", "DSM2D26"),
	(245, "192.168.15.1", 13, 15, "2025-07-27 12:54:12", 1, "/uploads/moviments/moviment_1753631652_68864ba497bc9.jpg", NULL),
	(246, "192.168.15.1", 13, 15, "2025-07-27 13:04:18", 1, "/uploads/moviments/moviment_1753632258_68864e0291859.jpg", "GED4E40"),
	(247, "192.168.15.1", 13, 15, "2025-07-27 13:06:21", 1, "/uploads/moviments/moviment_1753632381_68864e7d868c4.jpg", "GED4E40"),
	(248, "192.168.15.1", 13, 15, "2025-07-27 13:08:54", 1, "/uploads/moviments/moviment_1753632534_68864f1680caf.jpg", "GED4E40"),
	(249, "192.168.15.1", 13, 15, "2025-07-27 13:11:39", 1, "/uploads/moviments/moviment_1753632699_68864fbb23892.jpg", "sem_placa"),
	(250, "192.168.15.1", 13, 15, "2025-07-27 13:12:37", 1, "/uploads/moviments/moviment_1753632757_68864ff56f959.jpg", "GED4E40"),
	(251, "192.168.15.1", 13, 15, "2025-07-27 13:15:14", 1, "/uploads/moviments/moviment_1753632914_6886509231798.jpg", "GED4E40"),
	(252, "192.168.15.1", 13, 15, "2025-07-27 13:15:18", 1, "/uploads/moviments/moviment_1753632918_6886509636694.jpg", "GED4E40"),
	(253, "192.168.15.1", 13, 15, "2025-07-27 13:15:26", 1, "/uploads/moviments/moviment_1753632926_6886509e51922.jpg", "GED4E40"),
	(254, "192.168.15.1", 13, 15, "2025-07-27 13:16:00", 1, "/uploads/moviments/moviment_1753632960_688650c058999.jpg", "sem_placa"),
	(255, "192.168.15.1", 13, 15, "2025-07-27 13:16:11", 1, "/uploads/moviments/moviment_1753632971_688650cbf2319.jpg", "sem_placa"),
	(256, "192.168.15.1", 13, 15, "2025-07-27 13:16:28", 1, "/uploads/moviments/moviment_1753632988_688650dca3afc.jpg", "GED4E40"),
	(257, "192.168.15.1", 13, 15, "2025-07-27 13:18:30", 1, "/uploads/moviments/moviment_1753633110_688651565638a.jpg", "sem_placa"),
	(258, "192.168.15.1", 13, 15, "2025-07-27 13:18:34", 1, "/uploads/moviments/moviment_1753633114_6886515a3918c.jpg", "GED4E40"),
	(259, "192.168.15.1", 13, 15, "2025-07-27 13:18:41", 1, "/uploads/moviments/moviment_1753633121_6886516156f94.jpg", "GED4E40"),
	(260, "192.168.15.1", 13, 15, "2025-07-27 13:18:45", 1, "/uploads/moviments/moviment_1753633125_688651659975a.jpg", "GED4E40"),
	(261, "192.168.15.1", 13, 15, "2025-07-27 13:18:54", 1, "/uploads/moviments/moviment_1753633134_6886516e38b66.jpg", "GED4E40"),
	(262, "192.168.15.1", 13, 15, "2025-07-27 13:19:06", 1, "/uploads/moviments/moviment_1753633146_6886517acdc4c.jpg", "GED4E40"),
	(263, "192.168.15.1", 13, 15, "2025-07-27 13:19:25", 1, "/uploads/moviments/moviment_1753633165_6886518d79284.jpg", "GED4E40"),
	(264, "192.168.15.1", 13, 15, "2025-07-27 13:19:33", 1, "/uploads/moviments/moviment_1753633173_68865195b38b3.jpg", "GED4E40"),
	(265, "192.168.15.1", 13, 15, "2025-07-27 13:19:38", 1, "/uploads/moviments/moviment_1753633178_6886519aa630f.jpg", "sem_placa"),
	(266, "192.168.15.1", 13, 15, "2025-07-27 13:19:43", 1, "/uploads/moviments/moviment_1753633183_6886519ff1893.jpg", "GED4E40"),
	(267, "192.168.15.1", 13, 15, "2025-07-27 13:19:48", 1, "/uploads/moviments/moviment_1753633188_688651a408e24.jpg", "GED4E40"),
	(268, "192.168.15.1", 13, 15, "2025-07-27 13:20:02", 1, "/uploads/moviments/moviment_1753633202_688651b25cb64.jpg", "GED4E40"),
	(269, "192.168.15.1", 13, 15, "2025-07-27 13:20:07", 1, "/uploads/moviments/moviment_1753633207_688651b7f118d.jpg", "GED4E40"),
	(270, "192.168.15.1", 13, 15, "2025-07-27 13:20:19", 1, "/uploads/moviments/moviment_1753633219_688651c3812e9.jpg", "GED4E40"),
	(271, "192.168.15.1", 13, 15, "2025-07-27 13:20:24", 1, "/uploads/moviments/moviment_1753633224_688651c8b2504.jpg", "GED4E40"),
	(272, "192.168.15.1", 13, 15, "2025-07-27 13:20:27", 1, "/uploads/moviments/moviment_1753633227_688651cbec3c7.jpg", "GED4E40"),
	(273, "192.168.15.1", 13, 15, "2025-07-27 13:20:39", 1, "/uploads/moviments/moviment_1753633239_688651d7bd9ec.jpg", "GED4E40"),
	(274, "192.168.15.1", 13, 15, "2025-07-27 13:20:51", 1, "/uploads/moviments/moviment_1753633251_688651e343926.jpg", "GED4E40"),
	(275, "192.168.15.1", 13, 15, "2025-07-27 13:20:58", 1, "/uploads/moviments/moviment_1753633258_688651eaec383.jpg", "sem_placa"),
	(276, "192.168.15.1", 13, 15, "2025-07-27 13:21:02", 1, "/uploads/moviments/moviment_1753633262_688651eed124e.jpg", "GED4E40"),
	(277, "192.168.15.1", 13, 15, "2025-07-27 13:21:07", 1, "/uploads/moviments/moviment_1753633267_688651f379758.jpg", "GED4E40"),
	(278, "192.168.15.1", 13, 15, "2025-07-27 13:21:16", 1, "/uploads/moviments/moviment_1753633276_688651fc29b56.jpg", "GED4E40"),
	(279, "192.168.15.1", 13, 15, "2025-07-27 13:21:29", 1, "/uploads/moviments/moviment_1753633289_688652093e0eb.jpg", "GED4E40"),
	(280, "192.168.15.1", 13, 15, "2025-07-27 13:21:32", 1, "/uploads/moviments/moviment_1753633292_6886520c9045a.jpg", "GED4E40"),
	(281, "192.168.15.1", 13, 15, "2025-07-27 13:21:35", 1, "/uploads/moviments/moviment_1753633295_6886520f51cbc.jpg", "GED4E40"),
	(282, "192.168.15.1", 13, 15, "2025-07-27 13:21:40", 1, "/uploads/moviments/moviment_1753633300_688652143c870.jpg", "GED4E40"),
	(283, "192.168.15.1", 13, 15, "2025-07-27 13:21:55", 1, "/uploads/moviments/moviment_1753633315_68865223bf891.jpg", "GED4E40"),
	(284, "192.168.15.1", 13, 15, "2025-07-27 13:22:17", 1, "/uploads/moviments/moviment_1753633337_68865239cd582.jpg", "GED4E40"),
	(285, "192.168.15.1", 13, 15, "2025-07-27 13:22:29", 1, "/uploads/moviments/moviment_1753633349_688652452b46d.jpg", "GED4E40"),
	(286, "192.168.15.1", 13, 15, "2025-07-27 13:22:35", 1, "/uploads/moviments/moviment_1753633355_6886524bbfff3.jpg", "sem_placa"),
	(287, "192.168.15.1", 13, 15, "2025-07-27 13:22:41", 1, "/uploads/moviments/moviment_1753633361_68865251424a0.jpg", "GED4E40"),
	(288, "192.168.15.1", 13, 15, "2025-07-27 13:22:45", 1, "/uploads/moviments/moviment_1753633365_688652550dc18.jpg", "GED4E40"),
	(289, "192.168.15.1", 13, 15, "2025-07-27 13:22:48", 1, "/uploads/moviments/moviment_1753633368_68865258b29d1.jpg", "GED4E40"),
	(290, "192.168.15.1", 13, 15, "2025-07-27 13:22:50", 1, "/uploads/moviments/moviment_1753633370_6886525acfe80.jpg", "GED4E40"),
	(291, "192.168.15.1", 13, 15, "2025-07-27 13:22:55", 1, "/uploads/moviments/moviment_1753633375_6886525f8d53e.jpg", "GED4E40"),
	(292, "192.168.15.1", 13, 15, "2025-07-27 13:23:01", 1, "/uploads/moviments/moviment_1753633381_68865265359cb.jpg", "sem_placa"),
	(293, "192.168.15.1", 13, 15, "2025-07-27 13:23:05", 1, "/uploads/moviments/moviment_1753633385_68865269c5212.jpg", "GED4E40"),
	(294, "192.168.15.1", 13, 15, "2025-07-27 13:23:11", 1, "/uploads/moviments/moviment_1753633391_6886526f8673c.jpg", "GED4E40"),
	(295, "192.168.15.1", 13, 15, "2025-07-27 13:23:18", 1, "/uploads/moviments/moviment_1753633398_68865276c4ba1.jpg", "GED4E40"),
	(296, "192.168.15.1", 13, 15, "2025-07-27 13:23:25", 1, "/uploads/moviments/moviment_1753633405_6886527d8b160.jpg", "GED4E40"),
	(297, "192.168.15.1", 13, 15, "2025-07-27 13:23:31", 1, "/uploads/moviments/moviment_1753633411_688652836188c.jpg", "GED4E40"),
	(298, "192.168.15.1", 13, 15, "2025-07-27 13:23:45", 1, "/uploads/moviments/moviment_1753633425_688652915be39.jpg", "GED4E40"),
	(299, "192.168.15.1", 13, 15, "2025-07-27 13:23:49", 1, "/uploads/moviments/moviment_1753633429_6886529540929.jpg", "GED4E40"),
	(300, "192.168.15.1", 13, 15, "2025-07-27 13:23:54", 1, "/uploads/moviments/moviment_1753633434_6886529a6f3a3.jpg", "GED4E40"),
	(301, "192.168.15.1", 13, 15, "2025-07-27 13:24:07", 1, "/uploads/moviments/moviment_1753633447_688652a781a2b.jpg", "GED4E40"),
	(302, "192.168.15.1", 13, 15, "2025-07-27 13:24:16", 1, "/uploads/moviments/moviment_1753633456_688652b01ebc2.jpg", "GED4E40"),
	(303, "192.168.15.1", 13, 15, "2025-07-27 13:24:22", 1, "/uploads/moviments/moviment_1753633462_688652b6db659.jpg", "sem_placa"),
	(304, "192.168.15.1", 13, 15, "2025-07-27 13:24:39", 1, "/uploads/moviments/moviment_1753633479_688652c7b1b44.jpg", "GED4E40"),
	(305, "192.168.15.1", 13, 15, "2025-07-27 13:24:46", 1, "/uploads/moviments/moviment_1753633486_688652ce716da.jpg", "GED4E40"),
	(306, "192.168.15.1", 13, 15, "2025-07-27 13:24:52", 1, "/uploads/moviments/moviment_1753633492_688652d4dd022.jpg", "GED4E40"),
	(307, "192.168.15.1", 13, 15, "2025-07-27 13:24:55", 1, "/uploads/moviments/moviment_1753633495_688652d715faf.jpg", "GED4E40"),
	(308, "192.168.15.1", 13, 15, "2025-07-27 13:24:59", 1, "/uploads/moviments/moviment_1753633499_688652dbe5f17.jpg", "GED4E40"),
	(309, "192.168.15.1", 13, 15, "2025-07-27 13:25:06", 1, "/uploads/moviments/moviment_1753633506_688652e22e309.jpg", "sem_placa"),
	(310, "192.168.15.1", 13, 15, "2025-07-27 13:25:08", 1, "/uploads/moviments/moviment_1753633508_688652e4e3fd9.jpg", "GED4E40"),
	(311, "192.168.15.1", 13, 15, "2025-07-27 13:25:20", 1, "/uploads/moviments/moviment_1753633520_688652f09c5cb.jpg", "GED4E40"),
	(312, "192.168.15.1", 13, 15, "2025-07-27 13:25:25", 1, "/uploads/moviments/moviment_1753633525_688652f5542c4.jpg", "GED4E40"),
	(313, "192.168.15.1", 13, 15, "2025-07-27 13:25:27", 1, "/uploads/moviments/moviment_1753633527_688652f79df55.jpg", "GED4E40"),
	(314, "192.168.15.1", 13, 15, "2025-07-27 13:25:32", 1, "/uploads/moviments/moviment_1753633532_688652fc77267.jpg", "sem_placa"),
	(315, "192.168.15.1", 13, 15, "2025-07-27 13:25:51", 1, "/uploads/moviments/moviment_1753633551_6886530fd9ea5.jpg", "GED4E40"),
	(316, "192.168.15.1", 13, 15, "2025-07-27 13:37:10", 1, "/uploads/moviments/moviment_1753634230_688655b69254a.jpg", "GED4E40"),
	(317, "192.168.15.1", 13, 15, "2025-07-27 13:41:02", 1, "/uploads/moviments/moviment_1753634462_6886569e988ee.jpg", "GED4E40"),
	(318, "192.168.15.1", 13, 15, "2025-07-27 13:43:22", 1, "/uploads/moviments/moviment_1753634602_6886572a06498.jpg", "GED4E40"),
	(319, "192.168.15.1", 13, 15, "2025-07-27 14:01:18", 1, "/uploads/moviments/moviment_1753635678_68865b5e9a0e2.jpg", "GED4E40"),
	(320, "192.168.15.1", 13, 15, "2025-07-27 14:20:35", 1, "/uploads/moviments/moviment_1753636835_68865fe31006f.jpg", "sem_placa"),
	(321, "192.168.15.1", 14, 16, "2025-07-27 14:43:24", 1, "/uploads/moviments/moviment_1753638204_6886653c1c7f0.jpg", "OPP9H59"),
	(322, "192.168.15.1", 13, 15, "2025-07-27 14:43:40", 1, "/uploads/moviments/moviment_1753638220_6886654c1db4e.jpg", "sem_placa"),
	(323, "192.168.15.1", 13, 15, "2025-07-27 14:44:09", 1, "/uploads/moviments/moviment_1753638249_688665698143b.jpg", "sem_placa"),
	(324, "192.168.15.1", 14, 16, "2025-07-27 14:55:08", 1, "/uploads/moviments/moviment_1753638908_688667fc03776.jpg", "sem_placa"),
	(325, "192.168.15.1", 13, 15, "2025-07-27 14:55:12", 1, "/uploads/moviments/moviment_1753638912_68866800b65cb.jpg", "sem_placa"),
	(326, "192.168.15.1", 14, 16, "2025-07-27 14:56:31", 1, "/uploads/moviments/moviment_1753638991_6886684f5e5e4.jpg", "sem_placa"),
	(327, "192.168.15.1", 13, 15, "2025-07-27 14:56:46", 1, "/uploads/moviments/moviment_1753639006_6886685e8ae3b.jpg", "sem_placa"),
	(328, "192.168.15.1", 13, 15, "2025-07-27 15:00:24", 1, "/uploads/moviments/moviment_1753639224_6886693822a19.jpg", "sem_placa"),
	(329, "192.168.15.1", 13, 15, "2025-07-27 15:01:02", 1, "/uploads/moviments/moviment_1753639262_6886695e64cf9.jpg", "sem_placa"),
	(330, "192.168.15.1", 13, 15, "2025-07-27 15:04:32", 1, "/uploads/moviments/moviment_1753639472_68866a30e8e75.jpg", "sem_placa"),
	(331, "192.168.15.1", 14, 16, "2025-07-27 15:04:46", 1, "/uploads/moviments/moviment_1753639486_68866a3e88009.jpg", "sem_placa"),
	(332, "192.168.15.1", 13, 15, "2025-07-27 15:07:25", 1, "/uploads/moviments/moviment_1753639645_68866add3e243.jpg", "sem_placa"),
	(333, "192.168.15.1", 14, 16, "2025-07-27 15:07:33", 1, "/uploads/moviments/moviment_1753639653_68866ae511e87.jpg", "sem_placa"),
	(334, "192.168.15.1", 13, 15, "2025-07-27 15:08:12", 1, "/uploads/moviments/moviment_1753639692_68866b0cc9a4c.jpg", "sem_placa"),
	(335, "192.168.15.1", 14, 16, "2025-07-27 15:08:20", 1, "/uploads/moviments/moviment_1753639700_68866b14969f1.jpg", "sem_placa"),
	(336, "192.168.15.1", 13, 15, "2025-07-27 15:09:22", 1, "/uploads/moviments/moviment_1753639762_68866b52a3a17.jpg", "sem_placa"),
	(337, "192.168.15.1", 14, 16, "2025-07-27 15:09:32", 1, "/uploads/moviments/moviment_1753639772_68866b5c163bc.jpg", "sem_placa"),
	(338, "192.168.15.1", 13, 15, "2025-07-27 15:13:36", 1, "/uploads/moviments/moviment_1753640016_68866c5036962.jpg", "sem_placa"),
	(339, "192.168.15.1", 14, 16, "2025-07-27 15:13:55", 1, "/uploads/moviments/moviment_1753640035_68866c63879dc.jpg", "sem_placa"),
	(340, "192.168.15.1", 14, 16, "2025-07-27 15:17:47", 1, "/uploads/moviments/moviment_1753640267_68866d4bd9da2.jpg", "sem_placa"),
	(341, "192.168.15.1", 13, 15, "2025-07-27 15:18:04", 1, "/uploads/moviments/moviment_1753640284_68866d5cb1a81.jpg", "sem_placa"),
	(342, "192.168.15.1", 14, 16, "2025-07-27 18:09:05", 1, "/uploads/moviments/moviment_1753650545_68869571def73.jpg", "sem_placa"),
	(343, "192.168.15.1", 13, 15, "2025-07-27 18:09:27", 1, "/uploads/moviments/moviment_1753650567_68869587ae686.jpg", "sem_placa"),
	(344, "192.168.15.1", 14, 16, "2025-07-27 18:18:18", 1, "/uploads/moviments/moviment_1753651098_6886979a2723d.jpg", "sem_placa"),
	(345, "192.168.15.1", 13, 15, "2025-07-27 18:18:45", 1, "/uploads/moviments/moviment_1753651125_688697b5f2a41.jpg", "sem_placa"),
	(346, "192.168.15.1", 13, 15, "2025-07-27 18:27:52", 1, "/uploads/moviments/moviment_1753651672_688699d8bd70d.jpg", "sem_placa"),
	(347, "192.168.15.1", 14, 16, "2025-07-27 18:27:59", 1, "/uploads/moviments/moviment_1753651679_688699df1cb91.jpg", "sem_placa"),
	(348, "192.168.15.1", 13, 15, "2025-07-27 19:27:40", 1, "/uploads/moviments/moviment_1753655260_6886a7dc16eb3.jpg", "sem_placa"),
	(349, "192.168.15.1", 13, 15, "2025-07-27 19:29:20", 1, "/uploads/moviments/moviment_1753655360_6886a8401b43f.jpg", "sem_placa"),
	(350, "192.168.15.1", 13, 15, "2025-07-27 19:35:40", 1, "/uploads/moviments/moviment_1753655740_6886a9bc727df.jpg", "sem_placa"),
	(351, "192.168.15.1", 14, 16, "2025-07-27 19:51:25", 1, "/uploads/moviments/moviment_1753656685_6886ad6dd64b7.jpg", "sem_placa"),
	(352, "192.168.15.1", 13, 15, "2025-07-27 20:25:34", 1, "/uploads/moviments/moviment_1753658734_6886b56e6374f.jpg", "sem_placa"),
	(353, "192.168.15.1", 13, 15, "2025-07-27 21:32:53", 1, "/uploads/moviments/moviment_1753662773_6886c535219f6.jpg", "GED4E40");

/*!40000 ALTER TABLE `moviment_vacancies` ENABLE KEYS */;
UNLOCK TABLES;



# Dump of table user_tokens
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_tokens`;

CREATE TABLE `user_tokens` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` bigint NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `token` (`token`)
) ENGINE=InnoDB AUTO_INCREMENT=120 DEFAULT CHARSET=utf8mb3;

LOCK TABLES `user_tokens` WRITE;
/*!40000 ALTER TABLE `user_tokens` DISABLE KEYS */;

INSERT INTO `user_tokens` (`id`, `user_id`, `token`, `expires_at`, `created_at`) VALUES
	(17, 6, "9c3a4d3ae1372c993113909f6255d22bce0853092f80ff7d5fecc211410085e3", "2025-07-18 16:56:08", "2025-07-17 16:56:08"),
	(18, 6, "6ad421886189f7d73f095e4c5429335d458210d49f9951816a7b2cfdf081f471", "2025-07-18 17:25:35", "2025-07-17 17:25:35"),
	(19, 6, "4ff3b225bb9b8ac621a5bb5ca8b14e199cf8262ef210408179bae91393ecebf7", "2025-07-18 17:55:28", "2025-07-17 17:55:28"),
	(20, 6, "e09637085b3ff7d0d5814df248c5a5ca07a9985575fe67aa4c6971bd34f50c4b", "2025-07-18 18:36:37", "2025-07-17 18:36:37"),
	(21, 6, "fc807fad9a160d27e5eeae8a2b17a5352cd80346a2cda446ec1243c4e97abd99", "2025-07-19 05:08:34", "2025-07-18 05:08:34"),
	(22, 6, "d5cf8f21fd6225ce176dcad4c7a4edb3e94511a9783b40f10a22d435d6d10f5d", "2025-07-19 05:47:27", "2025-07-18 05:47:27"),
	(23, 6, "1234097cf34e596187ac34a3aefb3a09bb8ab69b8b231edce66ffb12170271f0", "2025-07-19 05:54:54", "2025-07-18 05:54:54"),
	(24, 6, "b4e7df37de8251deaf14cff0c7f2b5fee1050b23a16676ef52b5af095d97312c", "2025-07-19 06:11:55", "2025-07-18 06:11:55"),
	(25, 6, "687d909d677a4709261b1b23d90b897eaa2edcb0127d6d41c0e4a543567f3b12", "2025-07-19 06:23:02", "2025-07-18 06:23:02"),
	(26, 6, "391fd055c3e50f7a212e90dcdc10c6d45bc2c4bc8c115c4b7d978108da2ec389", "2025-07-19 06:39:13", "2025-07-18 06:39:13"),
	(27, 6, "bca36b6670345abfa7bf9dfcc5255d184a9ee59e7cff845a303cf92ca5b31bdc", "2025-07-19 06:43:41", "2025-07-18 06:43:41"),
	(28, 6, "b59fdf5aad750221532fdf9260e4b19c11c0d381af7e9f90fe388e9083298e29", "2025-07-19 06:44:48", "2025-07-18 06:44:48"),
	(29, 6, "e2078e24afa139dc977665e1420d9e1062b9cb9c836661ac8c40a6ab0be2879b", "2025-07-19 06:47:19", "2025-07-18 06:47:19"),
	(30, 6, "261bc6496a72681bcaf811617245c1ac4893bfa7fb4338a1c8a14c356e61a9af", "2025-07-19 06:49:01", "2025-07-18 06:49:01"),
	(31, 6, "53edb900ae2f3874fe7ded5364bc5c1c7f17e90afe58f5d0a8803063e9c75441", "2025-07-19 06:56:14", "2025-07-18 06:56:14"),
	(32, 6, "643614ddc8f755ac731d122e97a5dfa09c7aa1cc6d6e35c1db2a69e152741ca4", "2025-07-19 06:57:35", "2025-07-18 06:57:35"),
	(33, 6, "fe0502065eb0fafe87ea48b337ddb4a7775947b511ba32659785c90799e7ffbd", "2025-07-19 06:58:35", "2025-07-18 06:58:35"),
	(34, 6, "5de465495b1bd315582a27f8309a4c830239e9d4b4870450fd00ebdde4ddfe0d", "2025-07-19 06:59:29", "2025-07-18 06:59:29"),
	(35, 6, "14b88aa98cf8d7d275d65790258a2ce7ceb02076c81e8d9d909cb9ec25188b2c", "2025-07-19 07:00:34", "2025-07-18 07:00:34"),
	(36, 6, "1002f841abc7855ee562f175fc689e08ba92057d554ce5f4b17e93a14e5e9c13", "2025-07-19 07:05:10", "2025-07-18 07:05:10"),
	(37, 6, "183b4bda337e5842ad728f2c771a0f5ecaeb69d5808a4e2886dc43c37a3aa0f9", "2025-07-19 07:08:18", "2025-07-18 07:08:18"),
	(38, 6, "23135e9ecfeeb5524eae01575141edd5b9bcf330449ea9a53b737accf9c2c4ca", "2025-07-19 07:09:28", "2025-07-18 07:09:28"),
	(39, 6, "ed553acaa519b61dfb342c71be3118b2d70f0645d7a526b1066e6a42e2c16cd4", "2025-07-19 07:12:47", "2025-07-18 07:12:47"),
	(40, 6, "a9f400adf3ab56d190ee9d488992af60a05c9f4ea756917e7a23ff66be9a5f0b", "2025-07-19 07:23:59", "2025-07-18 07:23:59"),
	(41, 6, "f037a3900e5b644e52d5c3182f3e9ba6f686401313f2d60826c84995d928d5a7", "2025-07-19 07:38:37", "2025-07-18 07:38:37"),
	(42, 6, "cf56b21fae87e2dfd96da7548c9933860b73d7ee56d844d479a297f488436107", "2025-07-19 07:43:40", "2025-07-18 07:43:40"),
	(43, 6, "d4fc086fca76223f0c3cab45ee3b6ce4b88e7fa4db7d06041d658b47830e9849", "2025-07-19 07:48:38", "2025-07-18 07:48:38"),
	(44, 6, "3c1ee9d6a06194f05231dffaee1aa0c13aad4a153c1d073a3a9a39e2422b3817", "2025-07-19 07:49:42", "2025-07-18 07:49:42"),
	(45, 6, "d4bf9a00ba5419f68ec8df655107f3faf97c9071f572bf71b48c5fdbd46f10fa", "2025-07-19 07:55:47", "2025-07-18 07:55:47"),
	(46, 6, "b88030619d75ad012f36a3e1e075df8ef2cb3798c5f1b8f2fbd67da35720c230", "2025-07-19 07:57:46", "2025-07-18 07:57:46"),
	(47, 6, "17f29e4e86d0029c0920460e085cb4ca56412a60b74326e8fca3e7a882bc50c3", "2025-07-19 08:09:15", "2025-07-18 08:09:15"),
	(48, 6, "9af9394055bab7685ed905e0aba3ea4d3b5baf1a6d3d728bb575943eb71d8e61", "2025-07-19 08:21:39", "2025-07-18 08:21:39"),
	(49, 6, "46402cab480873c3fb9a2b3567eb38c733ff6b7c05da2871bcf9d2465ddd8cd3", "2025-07-19 08:26:01", "2025-07-18 08:26:01"),
	(50, 6, "aa4baf226983b50dd1709e2c5efe217c8d45b69069684910f91d63989c9be724", "2025-07-19 08:34:11", "2025-07-18 08:34:11"),
	(51, 6, "8219a7d3fa9f237c8607bfc7eef57ddabc96fcb29077a5cabb0518323ced5073", "2025-07-19 08:56:34", "2025-07-18 08:56:34"),
	(52, 6, "a708392547474a9a7cfdfe7e17825aa79d96063a6d69e9b8b9126723145333b5", "2025-07-19 10:00:04", "2025-07-18 10:00:04"),
	(53, 6, "6e2ce589c73a9e04de9f95c0748fed777c58e65eb7a6eb4eb1a025ce4fdbed0f", "2025-07-19 10:02:25", "2025-07-18 10:02:25"),
	(54, 6, "4ab7e404ca10114c057e565e0a003c74ce293464a1374d6bda286d2618e31a64", "2025-07-19 10:05:19", "2025-07-18 10:05:19"),
	(55, 6, "14fa1244a9cb74d21d1dc5d7bdfa2638a742d9c708701657feb244f4a8415467", "2025-07-19 10:06:40", "2025-07-18 10:06:40"),
	(56, 6, "65b9070bd4b5bb2c644b93db0bdc5a5d97b67e779428853f3ab438e8ca619e39", "2025-07-19 11:35:13", "2025-07-18 11:35:13"),
	(57, 6, "209f66eee0abb2e3dadb371a3bbd6e7300cbc857af62eda7e42dbe42a5599bc4", "2025-07-19 12:04:32", "2025-07-18 12:04:32"),
	(58, 6, "3bb9e2cfd2ddfce315d3d4c3c0f90405d7c4b3a85187e22ca19935597b377c62", "2025-07-19 12:54:16", "2025-07-18 12:54:16"),
	(59, 6, "cb1c120bd1a80d5f06342ee303c8dcb9385e52bfc4d873948d9d15252711bfcb", "2025-07-19 13:04:36", "2025-07-18 13:04:36"),
	(60, 6, "838e76b492e360c3ee164010091e51b6c585d820f2e16e8fb8b9d6d0cc579774", "2025-07-19 13:21:44", "2025-07-18 13:21:44"),
	(61, 6, "859613a8828f514f055f3e766c54961bf788dc2e34d913c8cdcbbde003d0a4f8", "2025-07-19 13:23:02", "2025-07-18 13:23:02"),
	(62, 6, "c2f8fb1549bc4eb692a61092eccd76ddc4e9ba8e39fe3993c4ed6931d51b3a5c", "2025-07-19 13:24:24", "2025-07-18 13:24:24"),
	(63, 6, "b8204edbe79805c67962071339fc5fbce574191fbd9c66b9a8ac8c6b17dd8ab9", "2025-07-19 13:30:36", "2025-07-18 13:30:36"),
	(64, 6, "8fd4eee554c7c62037bb7433f77b7aed68e852b0fe735f97e3e1af9b62346839", "2025-07-19 13:32:09", "2025-07-18 13:32:09"),
	(65, 6, "32e279492870db57ce5fa9bdfa490c44d2fafee6243beee26cfb5cc010e05838", "2025-07-19 13:33:21", "2025-07-18 13:33:21"),
	(66, 6, "8673d760934be35b89278e8230667e5c29a4236c76d7219b26dcb4866d03356e", "2025-07-19 13:35:39", "2025-07-18 13:35:39"),
	(67, 6, "a6fa636544d24e5a1dc18d4bddc582d81f1f2207a73f36b0a545996e7a7b4d8d", "2025-07-19 13:37:35", "2025-07-18 13:37:35"),
	(68, 6, "1b4dd0c97c8063bc5738f7f80700d9d2cecbf79f388ae79ffc08d2dad9e6befc", "2025-07-19 13:39:26", "2025-07-18 13:39:26"),
	(69, 6, "4698f2be09f04b307e26fbb9e920325bf5610fb7c3dc80f4b33b984eed2c9aa7", "2025-07-19 13:41:45", "2025-07-18 13:41:45"),
	(70, 6, "fa879f9d904cfabc6e77086fbb63609d8a1b0e11d230137d35bc55f142335901", "2025-07-19 13:43:42", "2025-07-18 13:43:42"),
	(71, 6, "c4441ef65d4018d8eeda1b0b22a8d37bb8e4075d5e70b780740db14c6f421f96", "2025-07-19 13:46:01", "2025-07-18 13:46:01"),
	(72, 6, "f5cdc940702d214f371e753e70ee2c00390938164bad97dc50794cf862013e54", "2025-07-19 13:48:44", "2025-07-18 13:48:44"),
	(73, 6, "769df3576591845f095adc3b3cdb22c58bfd76f5f8a241695aec44e974b6012c", "2025-07-19 13:49:38", "2025-07-18 13:49:38"),
	(74, 6, "bd0beb29691aefd027782ea90f80172b31e4bd9cb9cf476fcc60809d805d2a97", "2025-07-19 13:51:03", "2025-07-18 13:51:03"),
	(75, 6, "b9f88199a886e6d8aafb19c0053ccf4eebe7a8e442648e4b4185878873a4a7ae", "2025-07-19 13:52:39", "2025-07-18 13:52:39"),
	(76, 6, "76f76c1035badb3edf7523fa86d58d6b9a7473393d13410e891f40b0325101b6", "2025-07-19 13:53:52", "2025-07-18 13:53:52"),
	(77, 6, "5404831b86f2e09dc58affaf15cd1f91e73bce4d922fd377f0e5a8c5b07a1261", "2025-07-19 13:54:45", "2025-07-18 13:54:45"),
	(78, 6, "39dbb7d117b12bb04e3765dbb10a125a73724c1a6336f8aeee1ebf1d6b9c4fd3", "2025-07-19 14:00:39", "2025-07-18 14:00:39"),
	(79, 6, "692dd7b23e6b85b53a21fe1274b897f286394426a884ae48afc8adda232ccb05", "2025-07-19 14:01:34", "2025-07-18 14:01:34"),
	(80, 6, "0f2311d422fc84cd91473397feb447ebca2dbffc6c5e9177968f23494911c3c6", "2025-07-19 14:02:14", "2025-07-18 14:02:14"),
	(81, 6, "f80ef36d3c2c2a11c871693fe97e5eb20f90dd6686c9818d40d17c88dfc03062", "2025-07-19 14:03:26", "2025-07-18 14:03:26"),
	(82, 6, "7cb2ec6a1919c7909947bad4428e113f0f0f239cea95900e070437a8bcbe62ca", "2025-07-19 15:21:24", "2025-07-18 15:21:24"),
	(83, 6, "ebf98c3d7bd6c7468615fab940c6cbc08c7eaf6f432d1c172f43b878702f478e", "2025-07-19 15:49:05", "2025-07-18 15:49:05"),
	(84, 7, "97a39ae5c1c2e58109b71777aa00db29a7e3dddce874edc24603a00c6d88a06e", "2025-07-19 15:51:57", "2025-07-18 15:51:57"),
	(85, 6, "3aeaf07f3c056eddd4427387fa17fade3a3d49eac408e84a25fd9a6b5f9ec66d", "2025-07-19 15:52:42", "2025-07-18 15:52:42"),
	(86, 6, "0d12dd869ac807ff5b4575f89719ef1e2d528c6ffb6c7378f1e4d78e1166bc5e", "2025-07-19 15:52:57", "2025-07-18 15:52:57"),
	(87, 6, "f6780d64b11ccf1e45393652e0c9c2b8a71f4134dd08c6455c265effd5005630", "2025-07-21 09:24:34", "2025-07-20 09:24:34"),
	(88, 7, "4a596d621635461a168fc760c4777c062963bed7b84f9e8529baa6eee7940967", "2025-07-21 13:21:47", "2025-07-20 13:21:47"),
	(89, 6, "872aa1193cd69b184b6ee03490b734b0c188e026169715ea100c4b45bf10e0e1", "2025-07-21 14:21:46", "2025-07-20 14:21:46"),
	(90, 6, "b781a94369405b7651c4856c7234f44f755d0a012d7db6b99a6be4dc01031893", "2025-07-21 14:22:39", "2025-07-20 14:22:39"),
	(91, 6, "d9ae79d9a7784175882c7d45359c2cb8b0fae1fe2e23c52196d24d40020d5a33", "2025-07-21 15:50:16", "2025-07-20 15:50:16"),
	(92, 6, "a28429795d36a34b7cb593e8737145f887c18c95594926dde57176cef4bff68f", "2025-07-21 15:57:46", "2025-07-20 15:57:46"),
	(93, 6, "23e1ea57e4056cf2f6d02ad5c9d9470107eb537105f6b233b01307ffec10ab07", "2025-07-21 16:00:19", "2025-07-20 16:00:19"),
	(94, 6, "1f7704832ffa5c124103619fda16c8a5267b8d8976146b124e3f5751446b21d5", "2025-07-28 12:36:12", "2025-07-27 12:36:12"),
	(95, 6, "63c0cbf28a37240c1311b625a91878e3d2419eb855ae25e95504de7f3d61f5a0", "2025-07-28 12:38:32", "2025-07-27 12:38:32"),
	(96, 6, "0f8103d91402d7a0e18a8fcee3b567dde876b3825bd0547861a1c2366a458ae6", "2025-07-28 12:43:00", "2025-07-27 12:43:00"),
	(97, 6, "b9582d15629e1e5c324b425d07853d15de51a7ec17fd7c5674a7e00f2409a4e7", "2025-07-28 12:48:56", "2025-07-27 12:48:56"),
	(98, 6, "2329406e415fb6b473150450995a03a13f8ceaa2c54260bfb3905173012b4da6", "2025-07-28 12:50:33", "2025-07-27 12:50:33"),
	(99, 7, "9767dcd165d5b19b5bb7f981374aba378344c792225f64f3c9eeb10b69416389", "2025-07-28 12:54:00", "2025-07-27 12:54:00"),
	(100, 7, "f160e7dc38423191b5deac3ed55c73b45d3f77d3502d06e692db8c4b7a11c845", "2025-07-28 13:02:28", "2025-07-27 13:02:28"),
	(101, 7, "4b13d617f9601a7f2834324c1aa48dfa9b0b240ecaf8a338d3f8d2937945505e", "2025-07-28 13:04:10", "2025-07-27 13:04:10"),
	(102, 7, "4e7e02de4aa3e7959727de86a62947eee35fe51f3d20c3ef2f6392dd85ace773", "2025-07-28 13:06:13", "2025-07-27 13:06:13"),
	(103, 7, "b3140705fe4ee1560980cd51cd961d8eea4ad066bda417de69da005b0d2142b1", "2025-07-28 13:08:35", "2025-07-27 13:08:35"),
	(104, 7, "cc6905f64f2abd0ad3730c779a2eef162e163985cc20f1ae6c97917035d00a02", "2025-07-28 13:11:24", "2025-07-27 13:11:24"),
	(105, 7, "426fbfa2435ec7d29bd1e37d984aff9a722085f8d1b4a0f89e810ecec45a630f", "2025-07-28 13:12:16", "2025-07-27 13:12:16"),
	(106, 7, "606c30981ee31bafc991e820790ac70b5f7fba9a5a652780a2903f9a64a43423", "2025-07-28 13:14:51", "2025-07-27 13:14:51"),
	(107, 7, "53b78f7eb63f35436335eb16a01a06ddbcab3b7909b8bec73354b2508f16092d", "2025-07-28 13:18:20", "2025-07-27 13:18:20"),
	(108, 7, "54e86549da1a92b158451aa5305ccf810daaa8d81d4e3e486614e978d87538a2", "2025-07-28 13:25:44", "2025-07-27 13:25:44"),
	(109, 7, "83209f8a74c63c75ae198787305138775304ce514907591ccf23fbcd8b260136", "2025-07-28 13:36:24", "2025-07-27 13:36:24"),
	(110, 7, "3e422140a67b4e895eba493f344296c12170e1de9d9156f6012c2f90cbb28468", "2025-07-28 13:40:46", "2025-07-27 13:40:46"),
	(111, 7, "a50e7890d2e23339215ea2a62cff40ba2fdb66248b2065357adc0ede25d2a337", "2025-07-28 13:42:56", "2025-07-27 13:42:56"),
	(112, 7, "c1bdcdbfe01bd6571a3401a5a97c063f0713a236f5a68319539eb4fbc2d4edf8", "2025-07-28 13:43:08", "2025-07-27 13:43:08"),
	(113, 7, "395b47496f51db181feba8e829d21e55ecd5bb623f32f141fbf9ac908c268f06", "2025-07-28 14:39:46", "2025-07-27 14:39:46"),
	(114, 7, "716d86fd5dea0157f0a243d5d99eea890eb0b08271bf5333852f1ae7d5514ab4", "2025-07-28 14:42:59", "2025-07-27 14:42:59"),
	(115, 7, "1a5c4f3875806bbb8538ae296ffeec116ad675b76538f4e5aa91d16632c9b91c", "2025-07-28 14:54:20", "2025-07-27 14:54:20"),
	(116, 7, "0d20b84fcaf8be093f0081189577a129ef363ca7f8e6f2b9c48519095469d99f", "2025-07-28 14:56:09", "2025-07-27 14:56:09"),
	(117, 7, "2c3e634fd1193c79598465e9f1a853d4430e3189c1b2f5a6c759181fd89daf45", "2025-07-28 15:17:19", "2025-07-27 15:17:19"),
	(118, 7, "e0a75253935dad641d3cedb8b4136cceb526de94c3f3750bdbdcb61ee8d1e426", "2025-07-28 18:08:43", "2025-07-27 18:08:43"),
	(119, 7, "2facdf90c5bbe2ab4c57b94311633234ab3dbc161fa82bea736d9c0db263e998", "2025-07-28 21:32:30", "2025-07-27 21:32:30");

/*!40000 ALTER TABLE `user_tokens` ENABLE KEYS */;
UNLOCK TABLES;



# Dump of table users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `fk_companie` bigint DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `login` varchar(100) DEFAULT NULL,
  `password` varchar(250) DEFAULT NULL,
  `role` varchar(100) DEFAULT NULL,
  `link_site` varchar(200) DEFAULT NULL,
  `file_logo` varchar(250) DEFAULT NULL,
  `state` tinyint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `users_companies_FK` (`fk_companie`),
  CONSTRAINT `users_companies_FK` FOREIGN KEY (`fk_companie`) REFERENCES `companies` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;

INSERT INTO `users` (`id`, `fk_companie`, `name`, `login`, `password`, `role`, `link_site`, `file_logo`, `state`) VALUES
	(4, 2, "Edilson Claudino da Silva", "use@a.com", "$2y$10$leQceI0Ey.a7D/U51rsoMewPdCThd/pcmTdyRJKT7bKwDT5p.He7O", "user", "http://hml-analitics.duckdns.org:26080/analisticsdata/", "../uploads/Screenshot_2025-04-22_12-42-07.png", 1),
	(5, 3, "maria", "m@a.com", "$2y$10$Oap5Xxphcrt2nEpKIOyQlOw/jhdPPMradrirtFzNQXvS2UszoSswO", "user", "", NULL, 1),
	(6, 4, "edi", "edi@a.com", "$2y$10$b87v6QXH7drbZpym6bT6q.91I8lqXrLu7A6HewSzy/OTYFHMX2l6q", "user", "", "../uploads/testimony-2x-6977acfa5102a08c106be173a13473aa6ac88f0989c6a6ea965fd8aad43a5c5f.png", 1),
	(7, 5, "Remato", "recon", "$2y$10$eDUxTzNkFVAGBGFVcd7z9.ZYbGpgPZkMtYvxPZXCRrNetSUFo7bRe", "user", "123456", "../uploads/aguia.jpeg", 1);

/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;



# Dump of table vacancies
# ------------------------------------------------------------

DROP TABLE IF EXISTS `vacancies`;

CREATE TABLE `vacancies` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `fk_camera` bigint DEFAULT NULL,
  `settings` text,
  `setting_areas` text,
  `state` tinyint DEFAULT NULL,
  `plate` varchar(60) DEFAULT NULL,
  `plate_file` varchar(250) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `update_at` datetime DEFAULT NULL,
  `active` tinyint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vacancies_cameras_FK` (`fk_camera`),
  CONSTRAINT `vacancies_cameras_FK` FOREIGN KEY (`fk_camera`) REFERENCES `cameras` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb3;

LOCK TABLES `vacancies` WRITE;
/*!40000 ALTER TABLE `vacancies` DISABLE KEYS */;

INSERT INTO `vacancies` (`id`, `name`, `fk_camera`, `settings`, `setting_areas`, `state`, `plate`, `plate_file`, `created_at`, `update_at`, `active`) VALUES
	(1, "VAGA-1", 1, "{}", "{}", 1, "*", "", "2025-07-16 17:47:03", "2025-07-16 17:47:03", 1),
	(2, "CAM-2", 1, "{}", "{}", 1, "*", "", "2025-07-16 18:06:24", "2025-07-16 18:06:24", 1),
	(11, "Vaga-03", 1, "{}", "{}", 1, "*", "", "2025-07-17 17:50:33", "2025-07-17 17:50:33", 1),
	(12, "VAGA-01", 12, "*", "*", 1, "*", "", "2025-07-17 16:52:08", "2025-07-17 16:52:08", 1),
	(13, "VAGA-02", 12, "*", "*", 1, "*", "", "2025-07-17 16:52:23", "2025-07-17 16:52:23", 1),
	(14, "VAGA-03", 12, "*", "*", 1, "*", "", "2025-07-17 16:52:37", "2025-07-17 16:52:37", 1),
	(15, "rua1", 13, "*", "*", 1, "*", "", "2025-07-18 08:20:34", "2025-07-18 08:20:34", 1),
	(16, "esquerda", 14, "{}", "{}", 1, "*", "", "2025-07-27 14:41:43", "2025-07-27 14:41:43", 1);

/*!40000 ALTER TABLE `vacancies` ENABLE KEYS */;
UNLOCK TABLES;



# Dump of views
# ------------------------------------------------------------

# Creating temporary tables to overcome VIEW dependency errors


/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

# Dump completed on 2025-07-27T22:04:40-03:00
