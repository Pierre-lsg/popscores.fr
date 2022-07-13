-- MariaDB dump 10.19  Distrib 10.7.3-MariaDB, for debian-linux-gnu (aarch64)
--
-- Host: localhost    Database: popscores
-- ------------------------------------------------------
-- Server version	10.7.3-MariaDB-1:10.7.3+maria~focal

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
-- Table structure for table `arbitre`
--

DROP TABLE IF EXISTS `arbitre`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `arbitre` (
  `id_arbitre` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(128) NOT NULL,
  `photo` varchar(128) DEFAULT NULL,
  `code` int(11) NOT NULL,
  `id_champ` int(11) NOT NULL,
  UNIQUE KEY `id_arbitre` (`id_arbitre`) USING BTREE,
  KEY `id_champ` (`id_champ`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `arbitre_competition`
--

DROP TABLE IF EXISTS `arbitre_competition`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `arbitre_competition` (
  `id_comp` int(11) NOT NULL,
  `id_arbitre` int(11) NOT NULL,
  KEY `id_comp` (`id_comp`),
  KEY `id_arbitre` (`id_arbitre`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `championnat`
--

DROP TABLE IF EXISTS `championnat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `championnat` (
  `id_champ` int(11) NOT NULL AUTO_INCREMENT,
  `interne` tinyint(1) NOT NULL DEFAULT 0,
  `animation` tinyint(4) NOT NULL DEFAULT 0,
  `calculPoints` varchar(32) NOT NULL,
  `nbCompCalcul` tinyint(4) NOT NULL DEFAULT 0,
  `nom` varchar(128) NOT NULL,
  `saison` varchar(32) NOT NULL,
  PRIMARY KEY (`id_champ`),
  UNIQUE KEY `id_champ` (`id_champ`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `classement_champ`
--

DROP TABLE IF EXISTS `classement_champ`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `classement_champ` (
  `id_champ` int(11) NOT NULL,
  `id_catClass` int(11) NOT NULL,
  `id_cat` int(11) NOT NULL,
  `score` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `classement_comp`
--

DROP TABLE IF EXISTS `classement_comp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `classement_comp` (
  `id_comp` int(11) NOT NULL,
  `id_catClass` int(11) NOT NULL,
  `id_cat` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `scoreChamp` int(11) NOT NULL,
  `resultat` int(11) NOT NULL,
  `nbCoups` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `club`
--

DROP TABLE IF EXISTS `club`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `club` (
  `id_club` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(128) NOT NULL,
  `logo` varchar(128) DEFAULT NULL,
  `descriptif` text NOT NULL,
  PRIMARY KEY (`id_club`),
  UNIQUE KEY `id_club` (`id_club`)
) ENGINE=MyISAM AUTO_INCREMENT=38 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `competition`
--

DROP TABLE IF EXISTS `competition`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `competition` (
  `id_comp` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(128) NOT NULL,
  `etape` int(11) NOT NULL,
  `dateC` date NOT NULL,
  `logo` varchar(128) DEFAULT NULL,
  `plan` varchar(128) DEFAULT NULL,
  `regles_img` varchar(128) DEFAULT NULL,
  `regles_txt` text DEFAULT NULL,
  `nbJouParEqp` int(11) NOT NULL,
  `nbEqpParFly` int(11) NOT NULL,
  `nbTrou` int(11) NOT NULL DEFAULT 9,
  `id_champ` int(11) NOT NULL,
  `pourChampionnat` tinyint(4) NOT NULL DEFAULT 1,
  `dateResultat` date NOT NULL,
  PRIMARY KEY (`id_comp`),
  UNIQUE KEY `id_comp` (`id_comp`),
  KEY `id_champ` (`id_champ`)
) ENGINE=MyISAM AUTO_INCREMENT=62 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `equipe`
--

DROP TABLE IF EXISTS `equipe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `equipe` (
  `id_equipe` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(128) NOT NULL,
  `logo` varchar(128) DEFAULT NULL,
  `rdmCalcFly` int(11) DEFAULT NULL,
  `id_club` int(11) NOT NULL,
  `estCalculChampionnat` tinyint(1) NOT NULL COMMENT 'Pris en compte dans le résultat du club',
  PRIMARY KEY (`id_equipe`),
  UNIQUE KEY `id_equipe` (`id_equipe`),
  KEY `id_club` (`id_club`)
) ENGINE=MyISAM AUTO_INCREMENT=163 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `flight`
--

DROP TABLE IF EXISTS `flight`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `flight` (
  `id_fly` int(11) NOT NULL AUTO_INCREMENT,
  `numero` int(11) NOT NULL,
  `id_comp` int(11) NOT NULL,
  `id_equipe` int(11) NOT NULL,
  `id_joueur` int(11) NOT NULL,
  PRIMARY KEY (`id_fly`),
  UNIQUE KEY `id_fly` (`id_fly`),
  KEY `id_fly_2` (`id_fly`),
  KEY `id_equipe` (`id_equipe`),
  KEY `id_comp` (`id_comp`)
) ENGINE=MyISAM AUTO_INCREMENT=12907622 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `joueur`
--

DROP TABLE IF EXISTS `joueur`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `joueur` (
  `id_joueur` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(128) DEFAULT NULL,
  `prenom` varchar(128) DEFAULT NULL,
  `pseudo` varchar(128) DEFAULT NULL,
  `photo` varchar(128) DEFAULT NULL,
  `estCalculChampionnat` tinyint(4) NOT NULL DEFAULT 0,
  `id_club` int(11) NOT NULL,
  `id_equipe` int(11) NOT NULL,
  `id_champ` int(11) NOT NULL,
  PRIMARY KEY (`id_joueur`),
  UNIQUE KEY `id_joueur` (`id_joueur`),
  KEY `id_equipe` (`id_equipe`),
  KEY `id_club` (`id_club`)
) ENGINE=MyISAM AUTO_INCREMENT=1386 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `joueur_comp`
--

DROP TABLE IF EXISTS `joueur_comp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `joueur_comp` (
  `id_comp` int(11) NOT NULL,
  `id_joueur` int(11) NOT NULL,
  `id_equipe` int(11) NOT NULL,
  KEY `id_joueur` (`id_joueur`),
  KEY `id_comp` (`id_comp`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `organisateur`
--

DROP TABLE IF EXISTS `organisateur`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `organisateur` (
  `id_org` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(128) NOT NULL,
  `prenom` varchar(128) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `pseudo` varchar(128) NOT NULL,
  `mdp` varchar(128) NOT NULL,
  `id_club` int(11) NOT NULL,
  `estAdmin` tinyint(4) NOT NULL,
  PRIMARY KEY (`id_org`),
  UNIQUE KEY `id_org` (`id_org`),
  KEY `id_club` (`id_club`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `organisation_comp`
--
 
DROP TABLE IF EXISTS `organisation_comp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `organisation_comp` (
  `id_comp` int(11) NOT NULL,
  `id_club` int(11) NOT NULL,
  `id_org` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `param`
--

DROP TABLE IF EXISTS `param`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `param` (
  `param` varchar(256) NOT NULL,
  `val` varchar(256) NOT NULL,
  PRIMARY KEY (`param`),
  UNIQUE KEY `param` (`param`),
  KEY `param_2` (`param`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ref_arbitre`
--

DROP TABLE IF EXISTS `ref_arbitre`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ref_arbitre` (
  `id_typearb` int(11) NOT NULL AUTO_INCREMENT,
  `type` char(128) NOT NULL,
  `descriptif` text NOT NULL,
  PRIMARY KEY (`id_typearb`),
  UNIQUE KEY `id_typearb` (`id_typearb`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ref_catclassement`
--

DROP TABLE IF EXISTS `ref_catclassement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ref_catclassement` (
  `id_catClass` int(11) NOT NULL AUTO_INCREMENT,
  `nomCatClass` varchar(128) NOT NULL,
  PRIMARY KEY (`id_catClass`),
  UNIQUE KEY `id_catClass` (`id_catClass`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ref_classement`
--

DROP TABLE IF EXISTS `ref_classement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ref_classement` (
  `id_champ` int(11) NOT NULL,
  `id_catClass` int(11) NOT NULL,
  `classement` int(11) NOT NULL,
  `points` int(11) NOT NULL,
  KEY `id_champ` (`id_champ`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ref_formulejeu`
--

DROP TABLE IF EXISTS `ref_formulejeu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ref_formulejeu` (
  `id_formjeu` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(128) NOT NULL,
  `nomabr` varchar(11) NOT NULL,
  `explication` text NOT NULL,
  PRIMARY KEY (`id_formjeu`),
  UNIQUE KEY `id_formjeu` (`id_formjeu`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `resultat`
--

DROP TABLE IF EXISTS `resultat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `resultat` (
  `id_trou` int(11) NOT NULL,
  `id_comp` int(11) NOT NULL,
  `id_joueur` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  UNIQUE KEY `id_comp` (`id_comp`,`id_joueur`,`id_trou`),
  KEY `id_trou` (`id_trou`),
  KEY `id_joueur` (`id_joueur`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `surveillance_fly`
--

DROP TABLE IF EXISTS `surveillance_fly`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `surveillance_fly` (
  `id_comp` int(11) NOT NULL,
  `id_fly` int(11) NOT NULL,
  `id_arbitre` int(11) NOT NULL,
  UNIQUE KEY `id_fly` (`id_fly`),
  UNIQUE KEY `id_arbitre` (`id_arbitre`),
  KEY `id_comp` (`id_comp`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `surveillance_trou`
--

DROP TABLE IF EXISTS `surveillance_trou`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `surveillance_trou` (
  `id_comp` int(11) NOT NULL,
  `id_arbitre` int(11) NOT NULL,
  `id_trou` int(11) NOT NULL,
  KEY `id_comp` (`id_comp`),
  KEY `id_arbitre` (`id_arbitre`),
  KEY `id_trou` (`id_trou`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `trou`
--

DROP TABLE IF EXISTS `trou`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `trou` (
  `id_trou` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(128) NOT NULL,
  `numero` int(11) NOT NULL,
  `id_formjeu` int(11) NOT NULL,
  `regle_spec` text NOT NULL,
  `depart` varchar(128) NOT NULL,
  `depart_img` varchar(128) DEFAULT NULL,
  `cible` varchar(128) NOT NULL,
  `cible_img` varchar(128) DEFAULT NULL,
  `trajet` varchar(128) DEFAULT NULL,
  `par` int(11) NOT NULL,
  `distance` int(11) NOT NULL,
  `id_comp` int(11) NOT NULL,
  PRIMARY KEY (`id_trou`),
  UNIQUE KEY `id_trou` (`id_trou`),
  KEY `id_comp` (`id_comp`)
) ENGINE=MyISAM AUTO_INCREMENT=1049 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-04-30  7:55:39
