-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: siben
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
-- Table structure for table `actualizacion_reciente`
--

DROP TABLE IF EXISTS `actualizacion_reciente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `actualizacion_reciente` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_persona` int(11) NOT NULL,
  `curp` varchar(18) NOT NULL,
  `id_tipo` int(11) NOT NULL,
  `nombre` longtext NOT NULL,
  `horas` int(11) NOT NULL,
  `fecha` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=89196 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `alumnos`
--

DROP TABLE IF EXISTS `alumnos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alumnos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero_identificador` varchar(10) NOT NULL DEFAULT '',
  `identificados` int(9) NOT NULL DEFAULT 0,
  `apellido_paterno` varchar(60) NOT NULL DEFAULT '',
  `apellido_materno` varchar(60) NOT NULL DEFAULT '',
  `nombre` varchar(60) NOT NULL DEFAULT '',
  `curp` varchar(20) NOT NULL DEFAULT '',
  `edad` tinyint(4) NOT NULL DEFAULT 0,
  `genero` char(1) NOT NULL DEFAULT '',
  `correo` varchar(60) NOT NULL,
  `telefono` varchar(11) NOT NULL,
  `fecha_nacimiento` date NOT NULL DEFAULT '0000-00-00',
  `entidad_nacimiento` tinyint(4) NOT NULL DEFAULT 0,
  `municipio_nacimiento` int(11) NOT NULL,
  `nacionalidad` char(1) NOT NULL DEFAULT '',
  `id_documento` tinyint(4) NOT NULL DEFAULT 0,
  `discapacidad` char(1) NOT NULL DEFAULT '',
  `tipo_discapacidad` int(4) NOT NULL DEFAULT 0,
  `fecha_anterior` date DEFAULT NULL,
  `pais` int(11) NOT NULL,
  `entidad_nal` int(11) DEFAULT NULL,
  `cp` varchar(6) NOT NULL,
  `municipio_domicilio` int(11) DEFAULT NULL,
  `entidad_domicilio` int(11) DEFAULT NULL,
  `pais_procedencia` int(11) NOT NULL,
  `entidad_procedencia` int(11) NOT NULL,
  `segunda_lengua` int(11) NOT NULL,
  `antecedente` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `origen` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `clave_unica` (`curp`),
  UNIQUE KEY `nid` (`numero_identificador`),
  KEY `curp` (`curp`),
  KEY `numero` (`numero_identificador`),
  KEY `n_alumno` (`nombre`),
  KEY `ap_alumno` (`apellido_paterno`),
  KEY `am_alumno` (`apellido_materno`)
) ENGINE=MyISAM AUTO_INCREMENT=744204 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `alumnos_bachillerato`
--

DROP TABLE IF EXISTS `alumnos_bachillerato`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alumnos_bachillerato` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nid` varchar(8) NOT NULL,
  `id_entidad` int(11) NOT NULL,
  `id_municipio` int(11) NOT NULL,
  `id_bachillerato` int(11) NOT NULL,
  `promedio` float NOT NULL,
  `otro` varchar(256) NOT NULL DEFAULT 'n',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nid_2` (`nid`),
  KEY `nid` (`nid`)
) ENGINE=MyISAM AUTO_INCREMENT=393257 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `alumnos_ceneval`
--

DROP TABLE IF EXISTS `alumnos_ceneval`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alumnos_ceneval` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` varchar(12) NOT NULL DEFAULT '0',
  `id_egc` int(11) NOT NULL DEFAULT 0,
  `contrasenia` varchar(255) NOT NULL DEFAULT '',
  `apellido_paterno` varchar(60) NOT NULL DEFAULT '',
  `apellido_materno` varchar(60) NOT NULL DEFAULT '',
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `ingreso` int(11) NOT NULL DEFAULT 0,
  `solicitud` int(11) NOT NULL DEFAULT 0,
  `correo` varchar(256) NOT NULL,
  `registro` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `nid` (`id_usuario`)
) ENGINE=MyISAM AUTO_INCREMENT=40278 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `alumnos_ceneval_bkp`
--

DROP TABLE IF EXISTS `alumnos_ceneval_bkp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alumnos_ceneval_bkp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` varchar(12) NOT NULL DEFAULT '0',
  `id_egc` int(11) NOT NULL DEFAULT 0,
  `contrasenia` varchar(255) NOT NULL DEFAULT '',
  `apellido_paterno` varchar(60) NOT NULL DEFAULT '',
  `apellido_materno` varchar(60) NOT NULL DEFAULT '',
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `ingreso` int(11) NOT NULL DEFAULT 0,
  `solicitud` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `nid` (`id_usuario`)
) ENGINE=MyISAM AUTO_INCREMENT=23935 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `alumnos_curp`
--

DROP TABLE IF EXISTS `alumnos_curp`;
/*!50001 DROP VIEW IF EXISTS `alumnos_curp`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `alumnos_curp` AS SELECT
 1 AS `numero_identificador`,
  1 AS `apellido_paterno`,
  1 AS `apellido_materno`,
  1 AS `nombre`,
  1 AS `curp`,
  1 AS `edad`,
  1 AS `genero`,
  1 AS `fecha_nacimiento`,
  1 AS `entidad_nacimiento`,
  1 AS `id_escuela`,
  1 AS `primer_apellido`,
  1 AS `segundo_apellido`,
  1 AS `nombre_c`,
  1 AS `curp_valido`,
  1 AS `genero_c`,
  1 AS `fecha_nacimiento_c`,
  1 AS `entidad_nacimiento_c`,
  1 AS `entidad`,
  1 AS `status_curp`,
  1 AS `status_comparacion` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `alumnos_posgrado`
--

DROP TABLE IF EXISTS `alumnos_posgrado`;
/*!50001 DROP VIEW IF EXISTS `alumnos_posgrado`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `alumnos_posgrado` AS SELECT
 1 AS `id_persona`,
  1 AS `id_escuela`,
  1 AS `id_posgrado`,
  1 AS `posgrado`,
  1 AS `periodo`,
  1 AS `tipo_periodo` */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `alumnos_practica`
--

DROP TABLE IF EXISTS `alumnos_practica`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alumnos_practica` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_alumno` int(11) NOT NULL DEFAULT 0,
  `id_registro` int(11) NOT NULL DEFAULT 0,
  `id_escuela_practica` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idalumno` (`id_alumno`),
  KEY `idescuela` (`id_escuela_practica`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `asignaturas_docentes`
--

DROP TABLE IF EXISTS `asignaturas_docentes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asignaturas_docentes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_asignatura` int(11) NOT NULL DEFAULT 0,
  `id_licenciatura` tinyint(4) NOT NULL DEFAULT 0,
  `id_especialidad` tinyint(4) NOT NULL DEFAULT 0,
  `id_docente` int(11) NOT NULL DEFAULT 0,
  `grupo` varchar(50) NOT NULL DEFAULT '',
  `turno` char(1) NOT NULL DEFAULT 'm',
  `horas` tinyint(4) NOT NULL DEFAULT 0,
  `semestre` tinyint(4) NOT NULL DEFAULT 0,
  `modalidad` char(1) NOT NULL DEFAULT 'e',
  `curricular` char(1) NOT NULL DEFAULT 's',
  PRIMARY KEY (`id`),
  KEY `asignatura` (`id_asignatura`),
  KEY `licenciatura` (`id_licenciatura`),
  KEY `especialidad` (`id_especialidad`),
  KEY `docente` (`id_docente`)
) ENGINE=MyISAM AUTO_INCREMENT=53642 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `asignaturas_posgrados`
--

DROP TABLE IF EXISTS `asignaturas_posgrados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asignaturas_posgrados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_docente` int(11) NOT NULL DEFAULT 0,
  `id_educacion_adicional` int(11) NOT NULL DEFAULT 0,
  `asignatura` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=333 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `aut_autorizaciones`
--

DROP TABLE IF EXISTS `aut_autorizaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aut_autorizaciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_entidad` int(11) NOT NULL,
  `id_escuela` int(11) NOT NULL,
  `id_licenciatura` int(11) NOT NULL,
  `id_especialidad` int(11) NOT NULL,
  `autorizar` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `periodo1` int(11) NOT NULL,
  `periodo2` int(11) NOT NULL,
  `modalidad` char(1) NOT NULL,
  `oficio` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unico` (`id_entidad`,`id_escuela`,`id_licenciatura`,`id_especialidad`,`modalidad`,`oficio`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `aut_periodos_aut`
--

DROP TABLE IF EXISTS `aut_periodos_aut`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aut_periodos_aut` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_autorizacion` int(11) NOT NULL,
  `inicio` year(4) NOT NULL,
  `fin` year(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=96 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `aut_usuarios`
--

DROP TABLE IF EXISTS `aut_usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aut_usuarios` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `avisos`
--

DROP TABLE IF EXISTS `avisos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `avisos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `aviso` text DEFAULT NULL,
  `fecha_alta` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` char(1) NOT NULL DEFAULT 'a',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bajas`
--

DROP TABLE IF EXISTS `bajas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bajas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_alumno` int(11) NOT NULL DEFAULT 0,
  `nid` varchar(10) NOT NULL DEFAULT '',
  `id_motivo_baja` int(11) NOT NULL DEFAULT 0,
  `baja` text NOT NULL,
  `id_escuela` int(11) NOT NULL DEFAULT 0,
  `fecha_baja` timestamp NOT NULL DEFAULT current_timestamp(),
  `captura` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_contactos` (`nid`,`id_escuela`,`captura`,`id_motivo_baja`),
  KEY `nid` (`nid`),
  KEY `capturabaja` (`captura`),
  KEY `idalumno2` (`id_alumno`),
  KEY `idmotivobaja` (`id_motivo_baja`),
  KEY `idescuela` (`id_escuela`)
) ENGINE=MyISAM AUTO_INCREMENT=168600 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `becas_practica`
--

DROP TABLE IF EXISTS `becas_practica`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `becas_practica` (
  `id_alumno` int(11) NOT NULL DEFAULT 0,
  `id_registro` int(11) NOT NULL DEFAULT 0,
  `situacion` char(1) NOT NULL DEFAULT 'n',
  `fecha_cobro` date NOT NULL DEFAULT '0000-00-00',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_escuela_practica` int(11) NOT NULL DEFAULT 0,
  `nid` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idalumno` (`id_alumno`),
  KEY `registro` (`id_registro`),
  KEY `escuela_practica` (`id_escuela_practica`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `becas_practica_bkp`
--

DROP TABLE IF EXISTS `becas_practica_bkp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `becas_practica_bkp` (
  `id_alumno` int(11) NOT NULL DEFAULT 0,
  `id_registro` int(11) NOT NULL DEFAULT 0,
  `situacion` char(1) NOT NULL DEFAULT 'n',
  `fecha_cobro` date NOT NULL DEFAULT '0000-00-00',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_escuela_practica` int(11) NOT NULL DEFAULT 0,
  `nid` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idalumno` (`id_alumno`),
  KEY `registro` (`id_registro`),
  KEY `escuela_practica` (`id_escuela_practica`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `becas_practica_bkp2`
--

DROP TABLE IF EXISTS `becas_practica_bkp2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `becas_practica_bkp2` (
  `id_alumno` int(11) NOT NULL DEFAULT 0,
  `id_registro` int(11) NOT NULL DEFAULT 0,
  `situacion` char(1) NOT NULL DEFAULT 'n',
  `fecha_cobro` date NOT NULL DEFAULT '0000-00-00',
  `id` int(11) NOT NULL DEFAULT 0,
  `id_escuela_practica` int(11) NOT NULL DEFAULT 0,
  `nid` varchar(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `caracteristicas_escuelas`
--

DROP TABLE IF EXISTS `caracteristicas_escuelas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `caracteristicas_escuelas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_escuela` int(11) NOT NULL DEFAULT 0,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `propiedad` int(11) NOT NULL DEFAULT 11,
  `estructura` int(11) NOT NULL DEFAULT 21,
  `aulas` tinyint(4) NOT NULL DEFAULT 0,
  `bibliotecas` tinyint(4) NOT NULL DEFAULT 0,
  `aulas_computo` tinyint(4) NOT NULL DEFAULT 0,
  `auditorios` tinyint(4) NOT NULL DEFAULT 0,
  `idiomas` tinyint(4) NOT NULL DEFAULT 0,
  `albercas` tinyint(4) NOT NULL DEFAULT 0,
  `gimnasios` tinyint(4) NOT NULL DEFAULT 0,
  `canchas` tinyint(4) NOT NULL DEFAULT 0,
  `pistas` tinyint(4) NOT NULL DEFAULT 0,
  `salas_maestros` tinyint(4) NOT NULL DEFAULT 0,
  `cubiculos_maestros` tinyint(4) NOT NULL DEFAULT 0,
  `salas_multiples` tinyint(4) NOT NULL DEFAULT 0,
  `laboratorios` tinyint(4) NOT NULL DEFAULT 0,
  `talleres` tinyint(4) NOT NULL DEFAULT 0,
  `computo_alumnos` int(11) NOT NULL DEFAULT 0,
  `computo_docentes` int(11) NOT NULL DEFAULT 0,
  `computo_administrativo` int(11) NOT NULL DEFAULT 0,
  `internet_alumnos` int(11) NOT NULL DEFAULT 0,
  `internet_docentes` int(11) NOT NULL DEFAULT 0,
  `internet_administrativo` int(11) NOT NULL DEFAULT 0,
  `internet` char(1) NOT NULL DEFAULT 'n',
  `internet_banda_ancha` char(1) NOT NULL DEFAULT 'n',
  `restriccion_internet` char(1) NOT NULL DEFAULT 'n',
  `siden` char(1) NOT NULL DEFAULT 'n',
  `edusat` char(1) NOT NULL DEFAULT 'n',
  `d_rampas` tinyint(4) NOT NULL DEFAULT 0,
  `d_elevadores` tinyint(4) NOT NULL DEFAULT 0,
  `d_computadoras` tinyint(4) NOT NULL DEFAULT 0,
  `d_software` char(1) NOT NULL DEFAULT '',
  `d_banios` tinyint(4) NOT NULL DEFAULT 0,
  `d_libros` tinyint(4) NOT NULL DEFAULT 0,
  `d_guias` char(1) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `escuela` (`id_escuela`)
) ENGINE=MyISAM AUTO_INCREMENT=448 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cat_adicional`
--

DROP TABLE IF EXISTS `cat_adicional`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat_adicional` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cat_apoyos`
--

DROP TABLE IF EXISTS `cat_apoyos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat_apoyos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `apoyo` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cat_aptitud`
--

DROP TABLE IF EXISTS `cat_aptitud`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat_aptitud` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `aptitud` varchar(100) NOT NULL,
  `abreviatura` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cat_areas_conocimiento`
--

DROP TABLE IF EXISTS `cat_areas_conocimiento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat_areas_conocimiento` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_campo` char(1) NOT NULL DEFAULT '',
  `campo` varchar(100) NOT NULL DEFAULT '',
  `disciplina` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=52 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cat_asentamiento`
--

DROP TABLE IF EXISTS `cat_asentamiento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat_asentamiento` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asentamiento` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=42 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cat_asignaturas`
--

DROP TABLE IF EXISTS `cat_asignaturas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat_asignaturas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asignatura` varchar(255) NOT NULL DEFAULT '',
  `horas` tinyint(4) NOT NULL DEFAULT 1,
  `creditos` float NOT NULL DEFAULT 10,
  `semestre` tinyint(4) NOT NULL DEFAULT 0,
  `id_licenciatura` tinyint(4) NOT NULL DEFAULT 0,
  `id_especialidad` tinyint(4) NOT NULL DEFAULT 0,
  `modalidad` char(1) NOT NULL DEFAULT 'e',
  `curricular` char(1) NOT NULL DEFAULT 's',
  `evento` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4478 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cat_asignaturas_bkp`
--

DROP TABLE IF EXISTS `cat_asignaturas_bkp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat_asignaturas_bkp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asignatura` varchar(255) NOT NULL DEFAULT '',
  `horas` tinyint(4) NOT NULL DEFAULT 1,
  `creditos` float NOT NULL DEFAULT 10,
  `semestre` tinyint(4) NOT NULL DEFAULT 0,
  `id_licenciatura` tinyint(4) NOT NULL DEFAULT 0,
  `id_especialidad` tinyint(4) NOT NULL DEFAULT 0,
  `modalidad` char(1) NOT NULL DEFAULT 'e',
  `curricular` char(1) NOT NULL DEFAULT 's',
  `evento` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2400 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cat_asignaturas_copia`
--

DROP TABLE IF EXISTS `cat_asignaturas_copia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat_asignaturas_copia` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asignatura` varchar(255) NOT NULL DEFAULT '',
  `horas` tinyint(4) NOT NULL DEFAULT 1,
  `creditos` float NOT NULL DEFAULT 10,
  `semestre` tinyint(4) NOT NULL DEFAULT 0,
  `id_licenciatura` tinyint(4) NOT NULL DEFAULT 0,
  `id_especialidad` tinyint(4) NOT NULL DEFAULT 0,
  `modalidad` char(1) NOT NULL DEFAULT 'e',
  `curricular` char(1) NOT NULL DEFAULT 's',
  `evento` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3466 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cat_bachilleratos`
--

DROP TABLE IF EXISTS `cat_bachilleratos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat_bachilleratos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` text NOT NULL,
  `clave` varchar(10) NOT NULL,
  `sostenimiento` varchar(1) NOT NULL,
  `ambito` varchar(1) NOT NULL,
  `cp` varchar(5) NOT NULL,
  `id_entidad` int(11) NOT NULL,
  `id_municipio` int(11) NOT NULL,
  `id_municipio2` int(11) NOT NULL,
  `id_localidad` int(11) NOT NULL,
  `servicio` varchar(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17446 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cat_campos`
--

DROP TABLE IF EXISTS `cat_campos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat_campos` (
  `id` varchar(1) NOT NULL DEFAULT '',
  `campo` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cat_categorias`
--

DROP TABLE IF EXISTS `cat_categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat_categorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `categoria` varchar(30) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=502 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cat_construcciones`
--

DROP TABLE IF EXISTS `cat_construcciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat_construcciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `construccion` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cat_contrataciones`
--

DROP TABLE IF EXISTS `cat_contrataciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat_contrataciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contratacion` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cat_cursos`
--

DROP TABLE IF EXISTS `cat_cursos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat_cursos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=44 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cat_discapacidad2`
--

DROP TABLE IF EXISTS `cat_discapacidad2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat_discapacidad2` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(60) NOT NULL,
  `tipo` varchar(100) NOT NULL,
  `abreviatura` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cat_discapacidades`
--

DROP TABLE IF EXISTS `cat_discapacidades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat_discapacidades` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `discapacidad` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cat_discapacidades2`
--

DROP TABLE IF EXISTS `cat_discapacidades2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat_discapacidades2` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(60) NOT NULL,
  `tipo` varchar(100) NOT NULL,
  `abreviatura` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cat_documentos`
--

DROP TABLE IF EXISTS `cat_documentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat_documentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `documento` varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cat_egc`
--

DROP TABLE IF EXISTS `cat_egc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat_egc` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `egc` varchar(255) NOT NULL DEFAULT '',
  `clave` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cat_entidades`
--

DROP TABLE IF EXISTS `cat_entidades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat_entidades` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entidad` varchar(100) NOT NULL,
  `id_renapo` varchar(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cat_entidades_nal`
--

DROP TABLE IF EXISTS `cat_entidades_nal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat_entidades_nal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_pais` int(11) NOT NULL,
  `clave_ent` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `clave` varchar(10) NOT NULL,
  `pais` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=347 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cat_especialidades`
--

DROP TABLE IF EXISTS `cat_especialidades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat_especialidades` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `especialidad` varchar(255) NOT NULL DEFAULT '',
  `id_licenciatura` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=85 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cat_eventos`
--

DROP TABLE IF EXISTS `cat_eventos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat_eventos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cat_funciones`
--

DROP TABLE IF EXISTS `cat_funciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat_funciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `funcion` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cat_funciones_directorio`
--

DROP TABLE IF EXISTS `cat_funciones_directorio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat_funciones_directorio` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `funcion` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cat_lenguas`
--

DROP TABLE IF EXISTS `cat_lenguas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat_lenguas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lengua` varchar(200) NOT NULL,
  `familia` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=70 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cat_lenguas2`
--

DROP TABLE IF EXISTS `cat_lenguas2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat_lenguas2` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clave` varchar(10) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `LOI` varchar(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=164 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cat_lenguas_variantes`
--

DROP TABLE IF EXISTS `cat_lenguas_variantes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat_lenguas_variantes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_lengua` int(11) NOT NULL,
  `lengua` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=347 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cat_licenciaturas`
--

DROP TABLE IF EXISTS `cat_licenciaturas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat_licenciaturas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `licenciatura` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=98 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cat_licenciaturas2`
--

DROP TABLE IF EXISTS `cat_licenciaturas2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat_licenciaturas2` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `licenciatura` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cat_licenciaturas_bkp`
--

DROP TABLE IF EXISTS `cat_licenciaturas_bkp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat_licenciaturas_bkp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `licenciatura` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cat_localidades`
--

DROP TABLE IF EXISTS `cat_localidades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat_localidades` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_entidad` int(11) NOT NULL DEFAULT 0,
  `entidad` varchar(255) NOT NULL DEFAULT '',
  `id_municipio` int(11) NOT NULL DEFAULT 0,
  `id_municipio2` int(11) NOT NULL DEFAULT 0,
  `municipio` varchar(255) NOT NULL DEFAULT '',
  `id_localidad` int(11) NOT NULL DEFAULT 0,
  `localidad` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `municipio` (`id_municipio`),
  KEY `entidad` (`id_entidad`),
  KEY `localidad` (`id_localidad`)
) ENGINE=MyISAM AUTO_INCREMENT=368479 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cat_localidades_bkp2`
--

DROP TABLE IF EXISTS `cat_localidades_bkp2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat_localidades_bkp2` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_entidad` int(11) NOT NULL DEFAULT 0,
  `entidad` varchar(255) NOT NULL DEFAULT '',
  `id_municipio` int(11) NOT NULL DEFAULT 0,
  `id_municipio2` int(11) NOT NULL DEFAULT 0,
  `municipio` varchar(255) NOT NULL DEFAULT '',
  `id_localidad` int(11) NOT NULL DEFAULT 0,
  `localidad` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `municipio` (`id_municipio`),
  KEY `entidad` (`id_entidad`),
  KEY `localidad` (`id_localidad`)
) ENGINE=MyISAM AUTO_INCREMENT=328433 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cat_modalidades`
--

DROP TABLE IF EXISTS `cat_modalidades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat_modalidades` (
  `id` char(1) NOT NULL DEFAULT '',
  `modalidad` varchar(20) NOT NULL DEFAULT '',
  KEY `modalidad` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cat_motivos_bajas`
--

DROP TABLE IF EXISTS `cat_motivos_bajas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat_motivos_bajas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `motivo` varchar(255) NOT NULL DEFAULT '',
  `tipo_baja` char(1) NOT NULL DEFAULT 't',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cat_municipios`
--

DROP TABLE IF EXISTS `cat_municipios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat_municipios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_entidad` tinyint(4) NOT NULL DEFAULT 0,
  `id_municipio` int(11) NOT NULL DEFAULT 0,
  `municipio` varchar(255) NOT NULL DEFAULT '',
  `cabecera` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `entidad` (`id_entidad`),
  KEY `municipio` (`id_municipio`)
) ENGINE=MyISAM AUTO_INCREMENT=2459 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cat_nacionalidad`
--

DROP TABLE IF EXISTS `cat_nacionalidad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat_nacionalidad` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nacionalidad` varchar(60) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cat_necesidades`
--

DROP TABLE IF EXISTS `cat_necesidades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat_necesidades` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clave` varchar(10) NOT NULL,
  `necesidad` varchar(60) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cat_nivel_estudios`
--

DROP TABLE IF EXISTS `cat_nivel_estudios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat_nivel_estudios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nivel_estudio` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cat_nivel_sni`
--

DROP TABLE IF EXISTS `cat_nivel_sni`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat_nivel_sni` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nivel_sni` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cat_origen`
--

DROP TABLE IF EXISTS `cat_origen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat_origen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `origen` varchar(60) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cat_paises`
--

DROP TABLE IF EXISTS `cat_paises`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat_paises` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clave` int(11) NOT NULL,
  `pais` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=347 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cat_seguridad_social`
--

DROP TABLE IF EXISTS `cat_seguridad_social`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat_seguridad_social` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `regimen` varchar(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cat_turnos`
--

DROP TABLE IF EXISTS `cat_turnos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat_turnos` (
  `id` char(1) NOT NULL DEFAULT '',
  `turno` varchar(20) NOT NULL DEFAULT '',
  KEY `turno` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cat_vialidades`
--

DROP TABLE IF EXISTS `cat_vialidades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cat_vialidades` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom_vialidad` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cedula_adecuaciones`
--

DROP TABLE IF EXISTS `cedula_adecuaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cedula_adecuaciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_escuela` int(11) NOT NULL,
  `id_tipoadecuaciones` int(11) NOT NULL,
  `descripcion_adecuaciones` text NOT NULL,
  `monto_adecuaciones` float NOT NULL,
  `anio_adecuaciones` int(11) NOT NULL,
  `procedencia_recursos` int(11) NOT NULL,
  `procedencia_recursos_otros` varchar(255) NOT NULL,
  `adjunto` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1058 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cedula_barda`
--

DROP TABLE IF EXISTS `cedula_barda`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cedula_barda` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_escuela` int(11) NOT NULL,
  `id_tipo_material` int(11) NOT NULL,
  `material_barda` varchar(255) NOT NULL,
  `longitud` float NOT NULL,
  `altura` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=463 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cedula_cat_accesibilidad`
--

DROP TABLE IF EXISTS `cedula_cat_accesibilidad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cedula_cat_accesibilidad` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `accesibilidad` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cedula_cat_areadeterioro`
--

DROP TABLE IF EXISTS `cedula_cat_areadeterioro`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cedula_cat_areadeterioro` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `area` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cedula_cat_calidad`
--

DROP TABLE IF EXISTS `cedula_cat_calidad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cedula_cat_calidad` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `calidad` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cedula_cat_datosesp`
--

DROP TABLE IF EXISTS `cedula_cat_datosesp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cedula_cat_datosesp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_tipouso` int(11) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=52 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cedula_cat_documentacion`
--

DROP TABLE IF EXISTS `cedula_cat_documentacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cedula_cat_documentacion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `documento` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cedula_cat_edades`
--

DROP TABLE IF EXISTS `cedula_cat_edades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cedula_cat_edades` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `edad` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cedula_cat_funcionamiento`
--

DROP TABLE IF EXISTS `cedula_cat_funcionamiento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cedula_cat_funcionamiento` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `funcionamiento` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cedula_cat_inventario`
--

DROP TABLE IF EXISTS `cedula_cat_inventario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cedula_cat_inventario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inventario` varchar(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cedula_cat_laboratorio`
--

DROP TABLE IF EXISTS `cedula_cat_laboratorio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cedula_cat_laboratorio` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` varchar(80) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cedula_cat_patrimonio`
--

DROP TABLE IF EXISTS `cedula_cat_patrimonio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cedula_cat_patrimonio` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `edificio` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cedula_cat_recursos`
--

DROP TABLE IF EXISTS `cedula_cat_recursos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cedula_cat_recursos` (
  `id` int(11) NOT NULL,
  `recurso` varchar(30) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cedula_cat_salas`
--

DROP TABLE IF EXISTS `cedula_cat_salas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cedula_cat_salas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` varchar(80) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cedula_cat_sostenimientos`
--

DROP TABLE IF EXISTS `cedula_cat_sostenimientos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cedula_cat_sostenimientos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sostenimiento` varchar(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cedula_cat_subareas`
--

DROP TABLE IF EXISTS `cedula_cat_subareas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cedula_cat_subareas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` varchar(80) NOT NULL,
  `id_datosesp` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cedula_cat_tipoadecuaciones`
--

DROP TABLE IF EXISTS `cedula_cat_tipoadecuaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cedula_cat_tipoadecuaciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cedula_cat_tipobarda`
--

DROP TABLE IF EXISTS `cedula_cat_tipobarda`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cedula_cat_tipobarda` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `material` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cedula_cat_tipocontrato`
--

DROP TABLE IF EXISTS `cedula_cat_tipocontrato`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cedula_cat_tipocontrato` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contrato` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cedula_cat_tipoescuela`
--

DROP TABLE IF EXISTS `cedula_cat_tipoescuela`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cedula_cat_tipoescuela` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cedula_cat_tipopropiedad`
--

DROP TABLE IF EXISTS `cedula_cat_tipopropiedad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cedula_cat_tipopropiedad` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `propiedad` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cedula_cat_tipouso`
--

DROP TABLE IF EXISTS `cedula_cat_tipouso`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cedula_cat_tipouso` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uso` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cedula_cat_turnos`
--

DROP TABLE IF EXISTS `cedula_cat_turnos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cedula_cat_turnos` (
  `id` int(11) NOT NULL,
  `turno` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cedula_cat_uso`
--

DROP TABLE IF EXISTS `cedula_cat_uso`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cedula_cat_uso` (
  `id` int(11) NOT NULL DEFAULT 0,
  `frecuencia` varchar(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cedula_conectividad`
--

DROP TABLE IF EXISTS `cedula_conectividad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cedula_conectividad` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_escuela` int(11) NOT NULL,
  `id_tipo` int(11) NOT NULL,
  `tipo_otros` varchar(255) NOT NULL,
  `salidas` int(11) NOT NULL,
  `id_tipocontrato` int(11) NOT NULL,
  `fibra` int(11) NOT NULL,
  `telefonica` int(11) NOT NULL,
  `banda_ancha` int(11) NOT NULL,
  `cable` int(11) NOT NULL,
  `alcance` int(11) NOT NULL,
  `otro` varchar(255) NOT NULL,
  `observaciones` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=521 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cedula_construccion`
--

DROP TABLE IF EXISTS `cedula_construccion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cedula_construccion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_escuela` int(11) NOT NULL,
  `edad` int(11) NOT NULL,
  `m2_construidos` float NOT NULL,
  `num_niveles` int(11) NOT NULL,
  `tipo_uso` int(11) NOT NULL,
  `uso_otros` varchar(255) NOT NULL,
  `accesibilidad` int(11) NOT NULL,
  `accesibilidad_otros` varchar(255) NOT NULL,
  `observaciones` text NOT NULL,
  `mantenimiento` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1491 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cedula_cuenta_con`
--

DROP TABLE IF EXISTS `cedula_cuenta_con`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cedula_cuenta_con` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_escuela` int(11) NOT NULL,
  `agua` int(11) NOT NULL,
  `luz` int(11) NOT NULL,
  `fosa` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=328 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cedula_deportiva`
--

DROP TABLE IF EXISTS `cedula_deportiva`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cedula_deportiva` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_escuela` int(11) NOT NULL,
  `area` int(11) NOT NULL,
  `area_otros` varchar(255) NOT NULL,
  `id_tipouso` int(11) NOT NULL,
  `numero` int(11) NOT NULL,
  `num_usuarios` int(11) NOT NULL,
  `capacidad` int(11) NOT NULL,
  `superficie` float NOT NULL,
  `frecuencia_uso` int(11) NOT NULL,
  `aire` int(11) NOT NULL,
  `inventario_aire` int(11) NOT NULL,
  `voz` int(11) NOT NULL,
  `inventario_voz` int(11) NOT NULL,
  `datos` int(11) NOT NULL,
  `inventario_datos` int(11) NOT NULL,
  `ventiladores` int(11) NOT NULL,
  `inventario_ventiladores` int(11) NOT NULL,
  `ie_otros` varchar(255) NOT NULL,
  `inventario_ie_otros` int(11) NOT NULL,
  `computadora` int(11) NOT NULL,
  `inventario_computadora` int(11) NOT NULL,
  `proyector` int(11) NOT NULL,
  `inventario_proyector` int(11) NOT NULL,
  `audio` int(11) NOT NULL,
  `inventario_audio` int(11) NOT NULL,
  `pizarron` int(11) NOT NULL,
  `inventario_pizarron` int(11) NOT NULL,
  `e_otros` varchar(255) NOT NULL,
  `inventario_e_otros` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=505 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cedula_deterioro`
--

DROP TABLE IF EXISTS `cedula_deterioro`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cedula_deterioro` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_escuela` int(11) NOT NULL,
  `id_areadeterioro` int(11) NOT NULL,
  `id_tipodeterioro` int(11) NOT NULL,
  `descripcion_deterioro` text NOT NULL,
  `monto_deterioro` float NOT NULL,
  `adjunto_deterioro` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=686 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cedula_equipo_computo`
--

DROP TABLE IF EXISTS `cedula_equipo_computo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cedula_equipo_computo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_escuela` int(11) NOT NULL,
  `id_tipo` int(11) NOT NULL,
  `tipo_otros` varchar(255) NOT NULL,
  `directivos` int(11) NOT NULL,
  `docentes` int(11) NOT NULL,
  `administrativos` int(11) NOT NULL,
  `biblioteca` int(11) NOT NULL,
  `aulas` int(11) NOT NULL,
  `otro` varchar(255) NOT NULL,
  `uso` int(11) NOT NULL,
  `condiciones` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=795 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cedula_info_areas`
--

DROP TABLE IF EXISTS `cedula_info_areas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cedula_info_areas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_escuela` int(11) NOT NULL,
  `id_tipo` int(11) NOT NULL,
  `id_subtipo` int(11) NOT NULL,
  `tipo_otros` varchar(255) NOT NULL,
  `id_tipouso` int(11) NOT NULL,
  `numero` int(11) NOT NULL,
  `num_usuarios` int(11) NOT NULL,
  `capacidad` int(11) NOT NULL,
  `superficie` float NOT NULL,
  `frecuencia_uso` int(11) NOT NULL,
  `aire` int(11) NOT NULL,
  `inventario_aire` int(11) NOT NULL,
  `voz` int(11) NOT NULL,
  `inventario_voz` int(11) NOT NULL,
  `datos` int(11) NOT NULL,
  `inventario_datos` int(11) NOT NULL,
  `ventiladores` int(11) NOT NULL,
  `inventario_ventiladores` int(11) NOT NULL,
  `ie_otros` varchar(255) NOT NULL,
  `inventario_ie_otros` int(11) NOT NULL,
  `computadora` int(11) NOT NULL,
  `inventario_computadora` int(11) NOT NULL,
  `proyector` int(11) NOT NULL,
  `inventario_proyector` int(11) NOT NULL,
  `audio` int(11) NOT NULL,
  `inventario_audio` int(11) NOT NULL,
  `pizarron` int(11) NOT NULL,
  `inventario_pizarron` int(11) NOT NULL,
  `e_otros` varchar(255) NOT NULL,
  `inventario_e_otros` int(11) NOT NULL,
  `mesa` int(11) NOT NULL,
  `inventario_mesa` int(11) NOT NULL,
  `silla` int(11) NOT NULL,
  `inventario_silla` int(11) NOT NULL,
  `anaquel` int(11) NOT NULL,
  `inventario_anaquel` int(11) NOT NULL,
  `librero` int(11) NOT NULL,
  `inventario_librero` int(11) NOT NULL,
  `archivero` int(11) NOT NULL,
  `inventario_archivero` int(11) NOT NULL,
  `escritorio` int(11) NOT NULL,
  `inventario_escritorio` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5548 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cedula_info_escuela`
--

DROP TABLE IF EXISTS `cedula_info_escuela`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cedula_info_escuela` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_escuela` int(11) NOT NULL,
  `id_estado` int(11) NOT NULL,
  `id_municipio` int(11) NOT NULL,
  `id_turno` int(11) NOT NULL,
  `id_localidad` int(11) NOT NULL,
  `fecha_captura` date NOT NULL,
  `clave_siben` varchar(20) NOT NULL,
  `id_tipo_escuela` int(11) NOT NULL,
  `id_sostenimiento` int(11) NOT NULL,
  `id_tipo_propiedad` int(11) NOT NULL,
  `id_documentacion` int(11) NOT NULL,
  `otro_documentacion` varchar(255) NOT NULL,
  `id_edificio_patrimonial` int(11) NOT NULL,
  `otro_edificio_patrimonial` varchar(255) NOT NULL,
  `clave_ct` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unico id_escuela` (`id_escuela`)
) ENGINE=InnoDB AUTO_INCREMENT=378 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cedula_licenciaturas`
--

DROP TABLE IF EXISTS `cedula_licenciaturas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cedula_licenciaturas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_escuela` int(11) NOT NULL,
  `id_escuela_licenciatura` int(11) NOT NULL,
  `id_licenciatura` int(11) NOT NULL,
  `id_especialidad` int(11) NOT NULL,
  `modalidad` char(1) NOT NULL,
  `turno` char(1) NOT NULL,
  `grupo_1` int(11) NOT NULL,
  `hombres_1` int(11) NOT NULL,
  `mujeres_1` int(11) NOT NULL,
  `grupo_2` int(11) NOT NULL,
  `hombres_2` int(11) NOT NULL,
  `mujeres_2` int(11) NOT NULL,
  `grupo_3` int(11) NOT NULL,
  `hombres_3` int(11) NOT NULL,
  `mujeres_3` int(11) NOT NULL,
  `grupo_4` int(11) NOT NULL,
  `hombres_4` int(11) NOT NULL,
  `mujeres_4` int(11) NOT NULL,
  `grupo_5` int(11) NOT NULL,
  `hombres_5` int(11) NOT NULL,
  `mujeres_5` int(11) NOT NULL,
  `grupo_6` int(11) NOT NULL,
  `hombres_6` int(11) NOT NULL,
  `mujeres_6` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `escuela-lic-esp-modalidad-turno` (`id_escuela`,`id_licenciatura`,`id_especialidad`,`modalidad`,`turno`)
) ENGINE=InnoDB AUTO_INCREMENT=1481 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cedula_monitor`
--

DROP TABLE IF EXISTS `cedula_monitor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cedula_monitor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_entidad` int(11) NOT NULL,
  `id_escuela` int(11) NOT NULL,
  `datos_generales` int(11) NOT NULL DEFAULT 0,
  `terreno` int(11) NOT NULL DEFAULT 0,
  `construccion` int(11) NOT NULL DEFAULT 0,
  `area_academica` int(11) NOT NULL DEFAULT 0,
  `area_administrativa` int(11) NOT NULL DEFAULT 0,
  `area_deportiva` int(11) NOT NULL DEFAULT 0,
  `servicios_sanitarios` int(11) NOT NULL DEFAULT 0,
  `otros_servicios` int(11) NOT NULL DEFAULT 0,
  `equipo_de_computo` int(11) NOT NULL DEFAULT 0,
  `conectividad` int(11) NOT NULL DEFAULT 0,
  `areas_de_servicio` int(11) NOT NULL DEFAULT 0,
  `cuenta_con` int(11) NOT NULL DEFAULT 0,
  `adecuaciones` int(11) NOT NULL DEFAULT 0,
  `deterioro` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_escuela` (`id_escuela`)
) ENGINE=InnoDB AUTO_INCREMENT=446 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cedula_otros`
--

DROP TABLE IF EXISTS `cedula_otros`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cedula_otros` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_escuela` int(11) NOT NULL,
  `id_tipo` int(11) NOT NULL,
  `tipo_otros` varchar(255) NOT NULL,
  `numero` int(11) NOT NULL,
  `superficie` int(11) NOT NULL,
  `capacidad` int(11) NOT NULL,
  `mesas` int(11) NOT NULL,
  `inventario_mesas` int(11) NOT NULL,
  `sillas` int(11) NOT NULL,
  `inventario_sillas` int(11) NOT NULL,
  `bancas` int(11) NOT NULL,
  `inventario_bancas` int(11) NOT NULL,
  `m_otros` varchar(255) NOT NULL,
  `inventario_m_otros` int(11) NOT NULL,
  `aire` int(11) NOT NULL,
  `inventario_aire` int(11) NOT NULL,
  `voz` int(11) NOT NULL,
  `inventario_voz` int(11) NOT NULL,
  `datos` int(11) NOT NULL,
  `inventario_datos` int(11) NOT NULL,
  `ventiladores` int(11) NOT NULL,
  `inventario_ventiladores` int(11) NOT NULL,
  `ie_otros` varchar(255) NOT NULL,
  `inventario_ie_otros` int(11) NOT NULL,
  `proyector` int(11) NOT NULL,
  `inventario_proyector` int(11) NOT NULL,
  `audio` int(11) NOT NULL,
  `inventario_audio` int(11) NOT NULL,
  `pizarrones` int(11) NOT NULL,
  `inventario_pizarrones` int(11) NOT NULL,
  `e_otros` varchar(255) NOT NULL,
  `inventario_e_otros` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=426 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cedula_sanitarios`
--

DROP TABLE IF EXISTS `cedula_sanitarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cedula_sanitarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_escuela` int(11) NOT NULL,
  `id_tipo` int(11) NOT NULL,
  `nucleos` int(11) NOT NULL,
  `superficie` float NOT NULL,
  `lavabos` int(11) NOT NULL,
  `inodoro` int(11) NOT NULL,
  `inodoro2` int(11) NOT NULL,
  `migitorio` int(11) NOT NULL,
  `migitorio2` int(11) NOT NULL,
  `espacios_discapacitados` int(11) NOT NULL,
  `regaderas` int(11) NOT NULL,
  `vestidores` int(11) NOT NULL,
  `tarjas` int(11) NOT NULL,
  `calidad` int(11) NOT NULL,
  `bebederos` int(11) NOT NULL,
  `condiciones` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1305 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cedula_servicios`
--

DROP TABLE IF EXISTS `cedula_servicios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cedula_servicios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_escuela` int(11) NOT NULL,
  `id_tipo` int(11) NOT NULL,
  `tipo_otros` varchar(255) NOT NULL,
  `numero` int(11) NOT NULL,
  `dimension` float NOT NULL,
  `capacidad` int(11) NOT NULL,
  `documentacion` varchar(11) NOT NULL,
  `mantenimiento` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=834 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cedula_terreno`
--

DROP TABLE IF EXISTS `cedula_terreno`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cedula_terreno` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_escuela` int(11) NOT NULL,
  `superficie` float NOT NULL,
  `superficie_construida` float NOT NULL,
  `superficie_disponible` float NOT NULL,
  `superficie_areas_verdes` float NOT NULL,
  `superficie_estacionamiento` float NOT NULL,
  `estacionamiento_personal` float NOT NULL,
  `estacionamiento_alumnos` float NOT NULL,
  `otros_espacios_abiertos` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=345 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cedula_usuarios`
--

DROP TABLE IF EXISTS `cedula_usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cedula_usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(20) NOT NULL DEFAULT '',
  `password` varchar(100) NOT NULL DEFAULT '',
  `id_entidad` int(11) NOT NULL,
  `id_escuela` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `cedula_view_entidadesnoaparecen`
--

DROP TABLE IF EXISTS `cedula_view_entidadesnoaparecen`;
/*!50001 DROP VIEW IF EXISTS `cedula_view_entidadesnoaparecen`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `cedula_view_entidadesnoaparecen` AS SELECT
 1 AS `id_entidad` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `cedula_view_escuelasagregadas`
--

DROP TABLE IF EXISTS `cedula_view_escuelasagregadas`;
/*!50001 DROP VIEW IF EXISTS `cedula_view_escuelasagregadas`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `cedula_view_escuelasagregadas` AS SELECT
 1 AS `id`,
  1 AS `entidad`,
  1 AS `total` */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `cedula_view_estadosinfoescuela`
--

DROP TABLE IF EXISTS `cedula_view_estadosinfoescuela`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cedula_view_estadosinfoescuela` (
  `id_estado` tinyint(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cedula_view_idescuelasfaltantes`
--

DROP TABLE IF EXISTS `cedula_view_idescuelasfaltantes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cedula_view_idescuelasfaltantes` (
  `id_escuela` tinyint(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cedula_view_mon1`
--

DROP TABLE IF EXISTS `cedula_view_mon1`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cedula_view_mon1` (
  `id_entidad` tinyint(4) NOT NULL,
  `id_escuela` tinyint(4) NOT NULL,
  `entidad` tinyint(4) NOT NULL,
  `escuela` tinyint(4) NOT NULL,
  `datos_generales` tinyint(4) NOT NULL,
  `licenciaturas` tinyint(4) NOT NULL,
  `terreno` tinyint(4) NOT NULL,
  `barda` tinyint(4) NOT NULL,
  `construccion` tinyint(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `conflictos`
--

DROP TABLE IF EXISTS `conflictos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `conflictos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_registro` int(11) NOT NULL DEFAULT 0,
  `id_escuela` int(11) NOT NULL DEFAULT 0,
  `id_licenciatura` int(11) NOT NULL DEFAULT 0,
  `id_especialidad` int(11) NOT NULL DEFAULT 0,
  `modalidad` char(1) NOT NULL DEFAULT '',
  `semestre` int(11) NOT NULL DEFAULT 0,
  `status` char(1) NOT NULL DEFAULT 'n',
  PRIMARY KEY (`id`),
  KEY `escuela` (`id_escuela`),
  KEY `licenciatura` (`id_licenciatura`),
  KEY `especialidad` (`id_especialidad`),
  KEY `registro` (`id_registro`)
) ENGINE=MyISAM AUTO_INCREMENT=2512 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contrataciones_docentes`
--

DROP TABLE IF EXISTS `contrataciones_docentes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contrataciones_docentes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_personal` int(11) NOT NULL DEFAULT 0,
  `forma_contratacion` int(11) NOT NULL DEFAULT 0,
  `tiempo_contratacion` int(11) NOT NULL DEFAULT 0,
  `categoria` varchar(50) NOT NULL DEFAULT '',
  `horas_grupo` int(11) NOT NULL DEFAULT 0,
  `clave_contratacion` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idpersonal` (`id_personal`),
  KEY `forma` (`forma_contratacion`),
  KEY `tiempo` (`tiempo_contratacion`),
  KEY `clave` (`clave_contratacion`)
) ENGINE=MyISAM AUTO_INCREMENT=191614 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cuestionario_herramientas`
--

DROP TABLE IF EXISTS `cuestionario_herramientas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cuestionario_herramientas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(18) NOT NULL,
  `p1a` varchar(100) NOT NULL,
  `p1b` varchar(100) NOT NULL,
  `p2` varchar(100) NOT NULL,
  `p2a` varchar(256) NOT NULL,
  `p31` varchar(256) NOT NULL,
  `p32` varchar(256) NOT NULL,
  `p33` varchar(256) NOT NULL,
  `p34` varchar(256) NOT NULL,
  `p41` text NOT NULL,
  `p42` text NOT NULL,
  `p43` text NOT NULL,
  `p44` text NOT NULL,
  `p5` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `id_2` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cuestionarios_handicapeados`
--

DROP TABLE IF EXISTS `cuestionarios_handicapeados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cuestionarios_handicapeados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_pregunta` int(11) NOT NULL DEFAULT 0,
  `id_escuela` int(11) NOT NULL DEFAULT 0,
  `respuesta` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8150 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cupo_cursos`
--

DROP TABLE IF EXISTS `cupo_cursos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cupo_cursos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_entidad` int(11) NOT NULL,
  `id_escuela` int(11) NOT NULL,
  `cupo` int(11) NOT NULL,
  `id_licenciatura` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=305 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `curp_renapo`
--

DROP TABLE IF EXISTS `curp_renapo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `curp_renapo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nid` varchar(10) NOT NULL,
  `cad_valida` varchar(22) NOT NULL,
  `status_comparacion` int(11) NOT NULL,
  `curp_valido` varchar(20) NOT NULL,
  `primer_apellido` varchar(60) NOT NULL,
  `segundo_apellido` varchar(60) NOT NULL,
  `nombre_c` varchar(60) NOT NULL,
  `genero_c` char(1) NOT NULL,
  `fecha_nacimiento_c` date NOT NULL,
  `entidad_nacimiento_c` tinyint(4) NOT NULL,
  `entidad` varchar(50) NOT NULL,
  `id_escuela` int(11) NOT NULL,
  `status_curp` varchar(1) NOT NULL,
  `mensaje` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=302720 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `curp_renapoper`
--

DROP TABLE IF EXISTS `curp_renapoper`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `curp_renapoper` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_persona` int(11) NOT NULL,
  `cad_valida` varchar(22) NOT NULL,
  `status_comparacion` int(11) NOT NULL,
  `curp_valido` varchar(20) NOT NULL,
  `primer_apellido` varchar(60) NOT NULL,
  `segundo_apellido` varchar(60) NOT NULL,
  `nombre` varchar(60) NOT NULL,
  `genero` char(1) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `entidad_nacimiento` varchar(2) NOT NULL,
  `entidad` varchar(50) NOT NULL,
  `id_escuela` int(11) NOT NULL,
  `status_curp` varchar(1) NOT NULL,
  `mensaje` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=39308 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `curso_cat_sedes`
--

DROP TABLE IF EXISTS `curso_cat_sedes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `curso_cat_sedes` (
  `id_sede` int(11) NOT NULL,
  `nombre_sede` varchar(180) NOT NULL,
  `id_entidad` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_sede`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `curso_modalidad_titulacion`
--

DROP TABLE IF EXISTS `curso_modalidad_titulacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `curso_modalidad_titulacion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `modalidad_titulacion` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `curso_nivelacion`
--

DROP TABLE IF EXISTS `curso_nivelacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `curso_nivelacion` (
  `id_nivelacion` int(11) NOT NULL AUTO_INCREMENT,
  `id_participante` int(11) NOT NULL,
  `asiste` int(11) NOT NULL,
  `asistencia_mod1` int(11) NOT NULL,
  `actividades_mod1` int(11) NOT NULL,
  `asistencia_mod2` int(11) NOT NULL,
  `actividades_mod2` int(11) NOT NULL,
  `asistencia_mod3` int(11) NOT NULL,
  `actividades_mod3` int(11) NOT NULL,
  `asistencia_mod4` int(11) NOT NULL,
  `actividades_mod4` int(11) NOT NULL,
  `asistencia_mod5` int(11) NOT NULL,
  `actividades_mod5` int(11) NOT NULL,
  `modalidad_titulacion` int(11) NOT NULL,
  `total` float NOT NULL,
  `presento_examen` char(1) NOT NULL,
  `resultado` char(1) NOT NULL,
  PRIMARY KEY (`id_nivelacion`),
  UNIQUE KEY `id_participante_unico` (`id_participante`)
) ENGINE=InnoDB AUTO_INCREMENT=16083 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `curso_participantes`
--

DROP TABLE IF EXISTS `curso_participantes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `curso_participantes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `curp` varchar(20) NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `id_sede` int(11) NOT NULL,
  `grupo` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29211 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `curso_usuarios`
--

DROP TABLE IF EXISTS `curso_usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `curso_usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(30) NOT NULL,
  `password` varchar(30) NOT NULL,
  `id_sede` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=513 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `datos_curso`
--

DROP TABLE IF EXISTS `datos_curso`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `datos_curso` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_evento` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL,
  `id_licenciatura` int(11) NOT NULL,
  `semestre` int(11) NOT NULL,
  `fecha` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=108 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `datos_lenguas`
--

DROP TABLE IF EXISTS `datos_lenguas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `datos_lenguas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_escuela` int(11) NOT NULL,
  `numero` int(11) NOT NULL,
  `id_lengua` int(11) NOT NULL,
  `id_lengua_variante` int(11) NOT NULL,
  `tipo` varchar(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=89 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `directores`
--

DROP TABLE IF EXISTS `directores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `directores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_escuela` int(11) NOT NULL,
  `id_personal` int(11) NOT NULL,
  `curp` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_escuela` (`id_escuela`),
  KEY `id_personal` (`id_personal`)
) ENGINE=InnoDB AUTO_INCREMENT=500 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `directorio`
--

DROP TABLE IF EXISTS `directorio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `directorio` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL DEFAULT '',
  `apellido_paterno` varchar(50) NOT NULL DEFAULT '',
  `apellido_materno` varchar(50) NOT NULL DEFAULT '',
  `titulo` varchar(20) NOT NULL DEFAULT '',
  `id_entidad` int(11) NOT NULL DEFAULT 0,
  `cargo` varchar(255) NOT NULL DEFAULT '',
  `id_funcion` int(11) NOT NULL DEFAULT 0,
  `correo_electronico` varchar(128) NOT NULL DEFAULT '',
  `direccion` varchar(255) NOT NULL DEFAULT '',
  `lada` varchar(5) NOT NULL DEFAULT '',
  `telefono` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=302 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `docentes`
--

DROP TABLE IF EXISTS `docentes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `docentes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_personal` int(11) NOT NULL DEFAULT 0,
  `id_nivel_estudio` tinyint(4) NOT NULL DEFAULT 0,
  `titulo` char(1) NOT NULL DEFAULT 's',
  `escuela_egreso` varchar(255) NOT NULL DEFAULT '',
  `forma_contratacion` tinyint(4) NOT NULL DEFAULT 0,
  `tiempo_contratacion` tinyint(4) NOT NULL DEFAULT 0,
  `fecha_titulacion` date NOT NULL DEFAULT '0000-00-00',
  `tema_titulacion` varchar(255) NOT NULL DEFAULT '',
  `lengua_indigena` varchar(100) NOT NULL DEFAULT '',
  `antiguedad_escuela` year(4) NOT NULL DEFAULT 0000,
  `antiguedad_docente` year(4) NOT NULL DEFAULT 0000,
  `grupo_investigacion` varchar(128) NOT NULL DEFAULT '',
  `proyecto_investigacion` varchar(128) NOT NULL DEFAULT '',
  `estancia_investigacion` char(1) NOT NULL DEFAULT 'n',
  `id_area_conocimiento` int(11) NOT NULL DEFAULT 0,
  `id_nivel_sni` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idpersonal` (`id_personal`),
  KEY `estudio` (`id_nivel_estudio`),
  KEY `area` (`id_area_conocimiento`)
) ENGINE=MyISAM AUTO_INCREMENT=53611 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `docentes_estudios`
--

DROP TABLE IF EXISTS `docentes_estudios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `docentes_estudios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_personal` int(11) NOT NULL,
  `curp` varchar(18) NOT NULL,
  `id_nivel` int(11) NOT NULL,
  `id_campo` varchar(1) NOT NULL,
  `id_area` int(11) NOT NULL,
  `id_licenciatura` int(11) NOT NULL,
  `otra` varchar(80) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=30244 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `docentes_inscritos`
--

DROP TABLE IF EXISTS `docentes_inscritos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `docentes_inscritos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_personal` int(11) NOT NULL,
  `id_datos_curso` int(11) NOT NULL,
  `semestre` int(11) NOT NULL,
  `id_licenciatura` int(11) NOT NULL,
  `id_escuela` int(11) NOT NULL,
  `asistencia` int(11) NOT NULL,
  `oficio` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `observacion` varchar(256) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2980 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `docentes_inscritos_backup`
--

DROP TABLE IF EXISTS `docentes_inscritos_backup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `docentes_inscritos_backup` (
  `id` int(11) NOT NULL DEFAULT 0,
  `id_personal` int(11) NOT NULL,
  `id_datos_curso` int(11) NOT NULL,
  `semestre` int(11) NOT NULL,
  `id_licenciatura` int(11) NOT NULL,
  `id_escuela` int(11) NOT NULL,
  `asistencia` int(11) NOT NULL,
  `oficio` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `observacion` varchar(256) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `domicilio`
--

DROP TABLE IF EXISTS `domicilio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `domicilio` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero_identificador` varchar(10) NOT NULL,
  `tipovial` varchar(255) NOT NULL,
  `nomvial` varchar(255) NOT NULL,
  `numextnum1` varchar(255) NOT NULL,
  `numextnum2` varchar(255) NOT NULL,
  `numintnum` varchar(5) NOT NULL,
  `tipoasen` varchar(255) NOT NULL,
  `nomasen` varchar(255) NOT NULL,
  `cp` varchar(5) NOT NULL,
  `cve_loc` varchar(255) NOT NULL,
  `cve_mun` int(5) NOT NULL,
  `cve_ent` varchar(255) NOT NULL,
  `tiporef1` varchar(255) NOT NULL,
  `nomref1` varchar(255) NOT NULL,
  `tiporef2` varchar(255) NOT NULL,
  `nomref2` varchar(255) NOT NULL,
  `tiporef3` varchar(255) NOT NULL,
  `nomref3` varchar(255) NOT NULL,
  `descrubic` varchar(255) NOT NULL,
  `ingresos` int(11) NOT NULL,
  `folio` varchar(20) DEFAULT NULL,
  `envio_folio` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=70088 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `domicilio_becarios`
--

DROP TABLE IF EXISTS `domicilio_becarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `domicilio_becarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero_identificador` varchar(10) NOT NULL,
  `tipovial` varchar(255) NOT NULL,
  `nomvial` varchar(255) NOT NULL,
  `numextnum1` varchar(255) NOT NULL,
  `numextnum2` varchar(255) NOT NULL,
  `numintnum` varchar(5) NOT NULL,
  `tipoasen` varchar(255) NOT NULL,
  `nomasen` varchar(255) NOT NULL,
  `cp` varchar(5) NOT NULL,
  `cve_loc` varchar(255) NOT NULL,
  `cve_mun` int(5) NOT NULL,
  `cve_ent` varchar(255) NOT NULL,
  `tiporef1` varchar(255) NOT NULL,
  `nomref1` varchar(255) NOT NULL,
  `tiporef2` varchar(255) NOT NULL,
  `nomref2` varchar(255) NOT NULL,
  `tiporef3` varchar(255) NOT NULL,
  `nomref3` varchar(255) NOT NULL,
  `descrubic` varchar(255) NOT NULL,
  `ingresos` int(11) NOT NULL,
  `folio` varchar(20) DEFAULT NULL,
  `fecha` date NOT NULL,
  `fecha_actualizacion` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10000 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `domicilio_escuela`
--

DROP TABLE IF EXISTS `domicilio_escuela`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `domicilio_escuela` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_escuela` varchar(10) NOT NULL,
  `tipovial` varchar(255) DEFAULT NULL,
  `nomvial` varchar(255) DEFAULT NULL,
  `carretera` varchar(255) DEFAULT NULL,
  `camino` varchar(255) DEFAULT NULL,
  `numextnum1` varchar(255) DEFAULT NULL,
  `numextnum2` varchar(255) DEFAULT NULL,
  `numextalf1` varchar(35) DEFAULT NULL,
  `numintnum` varchar(5) DEFAULT NULL,
  `numintalf` varchar(35) DEFAULT NULL,
  `tipoasen` varchar(255) DEFAULT NULL,
  `nomasen` varchar(255) DEFAULT NULL,
  `cp` varchar(5) DEFAULT NULL,
  `nom_loc` varchar(255) NOT NULL,
  `cve_loc` varchar(4) NOT NULL,
  `nom_mun` varchar(255) NOT NULL,
  `cve_mun` int(5) NOT NULL,
  `nom_ent` varchar(255) NOT NULL,
  `cve_ent` varchar(255) NOT NULL,
  `tiporef1` varchar(255) DEFAULT NULL,
  `nomref1` varchar(255) DEFAULT NULL,
  `tiporef2` varchar(255) DEFAULT NULL,
  `nomref2` varchar(255) DEFAULT NULL,
  `tiporef3` varchar(255) DEFAULT NULL,
  `nomref3` varchar(255) DEFAULT NULL,
  `descrubic` varchar(255) DEFAULT NULL,
  `latitud` double NOT NULL,
  `longitud` double NOT NULL,
  `id_seguridad_social` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=620 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `educacion_adicional`
--

DROP TABLE IF EXISTS `educacion_adicional`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `educacion_adicional` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_escuela` int(11) NOT NULL DEFAULT 0,
  `tipo` char(1) NOT NULL DEFAULT '',
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `matricula` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=131 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `egresados`
--

DROP TABLE IF EXISTS `egresados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `egresados` (
  `id_alumno` int(11) NOT NULL DEFAULT 0,
  `nid` varchar(10) NOT NULL,
  `ciclo_escolar` int(4) NOT NULL DEFAULT 0,
  `fecha_titulacion` date NOT NULL DEFAULT '0000-00-00',
  `tema_titulacion` varchar(255) NOT NULL DEFAULT '',
  `vinculacion` varchar(255) NOT NULL DEFAULT '',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_escuela` int(11) NOT NULL DEFAULT 0,
  `captura` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `alumno` (`id_alumno`),
  KEY `nid` (`nid`),
  KEY `cicloescolar` (`ciclo_escolar`),
  KEY `idescuela` (`id_escuela`)
) ENGINE=MyISAM AUTO_INCREMENT=503254 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `encuesta_docentes`
--

DROP TABLE IF EXISTS `encuesta_docentes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `encuesta_docentes` (
  `nombre` tinyint(4) NOT NULL,
  `apellido_paterno` tinyint(4) NOT NULL,
  `apellido_materno` tinyint(4) NOT NULL,
  `id` tinyint(4) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `id_escuela` tinyint(4) NOT NULL,
  `curp` tinyint(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `encuesta_docentes2`
--

DROP TABLE IF EXISTS `encuesta_docentes2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `encuesta_docentes2` (
  `nombre` tinyint(4) NOT NULL,
  `apellido_paterno` tinyint(4) NOT NULL,
  `apellido_materno` tinyint(4) NOT NULL,
  `id` tinyint(4) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `id_escuela` tinyint(4) NOT NULL,
  `curp` tinyint(4) NOT NULL,
  `folio` tinyint(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `encuestas2`
--

DROP TABLE IF EXISTS `encuestas2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `encuestas2` (
  `nid` tinyint(4) NOT NULL,
  `apellido_paterno` tinyint(4) NOT NULL,
  `apellido_materno` tinyint(4) NOT NULL,
  `nombre` tinyint(4) NOT NULL,
  `semestre` tinyint(4) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `captura` tinyint(4) NOT NULL,
  `id_escuela` tinyint(4) NOT NULL,
  `id_licenciatura` tinyint(4) NOT NULL,
  `id_especialidad` tinyint(4) NOT NULL,
  `ingresos` tinyint(4) NOT NULL,
  `folio` tinyint(4) NOT NULL,
  `modalidad` tinyint(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `escuelas`
--

DROP TABLE IF EXISTS `escuelas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `escuelas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `escuela_key` char(10) NOT NULL DEFAULT '',
  `id_entidad` tinyint(4) NOT NULL DEFAULT 0,
  `escuela` varchar(255) NOT NULL DEFAULT '',
  `sostenimiento` char(1) NOT NULL DEFAULT 'p',
  `calle` varchar(100) NOT NULL DEFAULT '',
  `colonia` varchar(100) NOT NULL DEFAULT '',
  `id_municipio` int(11) NOT NULL DEFAULT 0,
  `id_localidad` int(11) NOT NULL DEFAULT 0,
  `cp` int(5) unsigned zerofill NOT NULL DEFAULT 00000,
  `telefono1` varchar(10) NOT NULL DEFAULT '',
  `telefono2` varchar(10) NOT NULL DEFAULT '',
  `telefono3` varchar(10) NOT NULL DEFAULT '',
  `fax` varchar(10) NOT NULL DEFAULT '',
  `lada` varchar(5) NOT NULL DEFAULT '',
  `correo_electronico` varchar(128) NOT NULL DEFAULT '',
  `pagina_web` varchar(128) NOT NULL DEFAULT '',
  `unica` tinyint(4) NOT NULL DEFAULT 1,
  `clave_escuela` varchar(15) NOT NULL DEFAULT '',
  `clave_institucion` varchar(15) NOT NULL DEFAULT '',
  `fecha_autorizacion` date NOT NULL DEFAULT '0000-00-00',
  `fecha_inicio` date NOT NULL DEFAULT '0000-00-00',
  `investigacion` char(1) NOT NULL DEFAULT 'n',
  `turno` char(1) NOT NULL DEFAULT 'm',
  `origen` varchar(80) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `claves` (`escuela_key`),
  KEY `entidad` (`id_entidad`),
  KEY `municipio` (`id_municipio`)
) ENGINE=MyISAM AUTO_INCREMENT=669 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `escuelas2`
--

DROP TABLE IF EXISTS `escuelas2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `escuelas2` (
  `id` tinyint(4) NOT NULL,
  `escuela_key` tinyint(4) NOT NULL,
  `id_entidad` tinyint(4) NOT NULL,
  `escuela` tinyint(4) NOT NULL,
  `sostenimiento` tinyint(4) NOT NULL,
  `calle` tinyint(4) NOT NULL,
  `colonia` tinyint(4) NOT NULL,
  `id_municipio` tinyint(4) NOT NULL,
  `cp` tinyint(4) NOT NULL,
  `telefono1` tinyint(4) NOT NULL,
  `telefono2` tinyint(4) NOT NULL,
  `telefono3` tinyint(4) NOT NULL,
  `fax` tinyint(4) NOT NULL,
  `lada` tinyint(4) NOT NULL,
  `correo_electronico` tinyint(4) NOT NULL,
  `pagina_web` tinyint(4) NOT NULL,
  `unica` tinyint(4) NOT NULL,
  `clave_institucion` tinyint(4) NOT NULL,
  `clave_escuela` tinyint(4) NOT NULL,
  `investigacion` tinyint(4) NOT NULL,
  `turno` tinyint(4) NOT NULL,
  `modulo` tinyint(4) NOT NULL,
  `cantidad_alumnos` tinyint(4) NOT NULL,
  `cantidad_personal` tinyint(4) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `captura` tinyint(4) NOT NULL,
  `estructura` tinyint(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `escuelas_eventos`
--

DROP TABLE IF EXISTS `escuelas_eventos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `escuelas_eventos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_entidad` int(11) NOT NULL,
  `id_escuela` int(11) NOT NULL,
  `id_evento` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=126 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `escuelas_licenciaturas`
--

DROP TABLE IF EXISTS `escuelas_licenciaturas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `escuelas_licenciaturas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_escuela` int(11) NOT NULL DEFAULT 0,
  `id_licenciatura` tinyint(4) NOT NULL DEFAULT 0,
  `id_especialidad` tinyint(4) NOT NULL DEFAULT 0,
  `modalidad` char(1) NOT NULL DEFAULT 'e',
  `turno` char(1) NOT NULL DEFAULT 'm',
  `clave` varchar(30) NOT NULL DEFAULT '',
  `id_personal` int(11) NOT NULL DEFAULT 0,
  `clave_carrera` varchar(40) NOT NULL,
  `acuedo_rvoe` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `escuela` (`id_escuela`),
  KEY `licenciatura` (`id_licenciatura`),
  KEY `personal` (`id_personal`),
  KEY `especialidad` (`id_especialidad`)
) ENGINE=MyISAM AUTO_INCREMENT=6716 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `escuelas_licenciaturas2`
--

DROP TABLE IF EXISTS `escuelas_licenciaturas2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `escuelas_licenciaturas2` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_escuela` int(11) NOT NULL DEFAULT 0,
  `id_licenciatura` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `escuela` (`id_escuela`),
  KEY `licenciatura` (`id_licenciatura`)
) ENGINE=MyISAM AUTO_INCREMENT=494 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `escuelas_practica`
--

DROP TABLE IF EXISTS `escuelas_practica`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `escuelas_practica` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `escuela_practica` varchar(255) NOT NULL DEFAULT '',
  `clave_ct` varchar(10) NOT NULL DEFAULT '',
  `id_entidad` int(11) NOT NULL DEFAULT 0,
  `id_municipio` int(11) NOT NULL DEFAULT 0,
  `localidad` varchar(100) NOT NULL DEFAULT '',
  `direccion` varchar(100) NOT NULL DEFAULT '',
  `cp` char(5) NOT NULL DEFAULT '',
  `telefono1` varchar(10) NOT NULL DEFAULT '',
  `telefono2` varchar(10) NOT NULL DEFAULT '',
  `telefono3` varchar(10) NOT NULL DEFAULT '',
  `lada` varchar(5) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `clave` (`clave_ct`),
  KEY `municipio` (`id_municipio`),
  KEY `entidad` (`id_entidad`)
) ENGINE=MyISAM AUTO_INCREMENT=28152 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `escuelas_status`
--

DROP TABLE IF EXISTS `escuelas_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `escuelas_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_escuela` int(11) NOT NULL DEFAULT 0,
  `modulo` char(1) NOT NULL DEFAULT 'l',
  `cantidad_alumnos` int(11) NOT NULL DEFAULT 0,
  `cantidad_personal` int(11) NOT NULL DEFAULT 0,
  `status` char(1) NOT NULL DEFAULT 'x',
  `captura` char(1) NOT NULL DEFAULT '0',
  `estructura` char(1) NOT NULL DEFAULT '0',
  `oficializar` char(1) NOT NULL DEFAULT '0',
  `oficializar_fecha` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_escuela` (`id_escuela`)
) ENGINE=MyISAM AUTO_INCREMENT=1287 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `falta_enc`
--

DROP TABLE IF EXISTS `falta_enc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `falta_enc` (
  `nid` tinyint(4) NOT NULL,
  `semestre` tinyint(4) NOT NULL,
  `id_escuela` tinyint(4) NOT NULL,
  `id_licenciatura` tinyint(4) NOT NULL,
  `id_especialidad` tinyint(4) NOT NULL,
  `nombre` tinyint(4) NOT NULL,
  `apellido_paterno` tinyint(4) NOT NULL,
  `apellido_materno` tinyint(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `historia`
--

DROP TABLE IF EXISTS `historia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `historia` (
  `id` tinyint(4) NOT NULL,
  `nid` tinyint(4) NOT NULL,
  `apellido_paterno` tinyint(4) NOT NULL,
  `apellido_materno` tinyint(4) NOT NULL,
  `nombre` tinyint(4) NOT NULL,
  `curp` tinyint(4) NOT NULL,
  `edad` tinyint(4) NOT NULL,
  `genero` tinyint(4) NOT NULL,
  `fecha_nacimiento` tinyint(4) NOT NULL,
  `entidad_nacimiento` tinyint(4) NOT NULL,
  `municipio_nacimiento` tinyint(4) NOT NULL,
  `nacionalidad` tinyint(4) NOT NULL,
  `id_documento` tinyint(4) NOT NULL,
  `discapacidad` tinyint(4) NOT NULL,
  `tipo_discapacidad` tinyint(4) NOT NULL,
  `id_registro` tinyint(4) NOT NULL,
  `id_escuela` tinyint(4) NOT NULL,
  `id_licenciatura` tinyint(4) NOT NULL,
  `id_especialidad` tinyint(4) NOT NULL,
  `modalidad` tinyint(4) NOT NULL,
  `turno` tinyint(4) NOT NULL,
  `semestre` tinyint(4) NOT NULL,
  `grupo` tinyint(4) NOT NULL,
  `matricula` tinyint(4) NOT NULL,
  `sepa_ingles` tinyint(4) NOT NULL,
  `apoyos` tinyint(4) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `fecha_alta` tinyint(4) NOT NULL,
  `fecha_actualizacion` tinyint(4) NOT NULL,
  `captura` tinyint(4) NOT NULL,
  `id_conflicto` tinyint(4) NOT NULL,
  `id_escuela_origen` tinyint(4) NOT NULL,
  `id_licenciatura_destino` tinyint(4) NOT NULL,
  `id_especialidad_destino` tinyint(4) NOT NULL,
  `modalidad_destino` tinyint(4) NOT NULL,
  `semestre_destino` tinyint(4) NOT NULL,
  `status_conflictos` tinyint(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `jubilados`
--

DROP TABLE IF EXISTS `jubilados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jubilados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_personal` int(11) NOT NULL DEFAULT 0,
  `fecha_jubilacion` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=19933 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lengua_persona`
--

DROP TABLE IF EXISTS `lengua_persona`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lengua_persona` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_escuela` int(11) NOT NULL,
  `docentes` int(11) NOT NULL,
  `alumnos` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lenguas_docentes`
--

DROP TABLE IF EXISTS `lenguas_docentes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lenguas_docentes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(18) NOT NULL,
  `id_lengua` varchar(10) NOT NULL,
  `id_variante` varchar(10) DEFAULT NULL,
  `nativo` varchar(3) DEFAULT NULL,
  `habla` varchar(3) DEFAULT NULL,
  `entiende` varchar(3) DEFAULT NULL,
  `escribe` varchar(3) DEFAULT NULL,
  `formacion` varchar(3) DEFAULT NULL,
  `formacion_nivel` varchar(255) DEFAULT NULL,
  `formacion_int` varchar(255) DEFAULT NULL,
  `cursos_impartidos` varchar(3) DEFAULT NULL,
  `cursos_cuales` varchar(255) DEFAULT NULL,
  `cursos_cuando` varchar(255) DEFAULT NULL,
  `cursos_donde` varchar(255) DEFAULT NULL,
  `impartira_curso` varchar(3) DEFAULT NULL,
  `otra_lengua` varchar(3) DEFAULT NULL,
  `lengua2` varchar(25) DEFAULT NULL,
  `porcentaje` varchar(4) DEFAULT NULL,
  `bibliografia` longtext DEFAULT NULL,
  `comentarios` longtext DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `listas_becas`
--

DROP TABLE IF EXISTS `listas_becas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `listas_becas` (
  `id_alumno` int(11) DEFAULT NULL,
  `curp` varchar(20) DEFAULT NULL,
  `apellido_paterno` varchar(60) DEFAULT NULL,
  `apellido_materno` varchar(60) DEFAULT NULL,
  `nombre` varchar(60) DEFAULT NULL,
  `edad` tinyint(4) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `genero` char(1) DEFAULT NULL,
  `entidad_nacimiento` tinyint(4) DEFAULT NULL,
  `nacionalidad` char(1) DEFAULT NULL,
  `id_licenciatura` int(11) DEFAULT NULL,
  `matricula` char(10) DEFAULT NULL,
  `id_especialidad` int(11) DEFAULT NULL,
  `id_escuela` int(11) DEFAULT NULL,
  `semestre` int(11) DEFAULT NULL,
  `captura` int(11) DEFAULT NULL,
  `escuela_practica` varchar(255) DEFAULT NULL,
  `clave_ct` varchar(10) DEFAULT NULL,
  `id_municipio` int(11) DEFAULT NULL,
  `localidad` varchar(100) DEFAULT NULL,
  `direccion` varchar(100) DEFAULT NULL,
  `cp` char(5) DEFAULT NULL,
  `telefono1` varchar(10) DEFAULT NULL,
  `telefono2` varchar(10) DEFAULT NULL,
  `telefono3` varchar(10) DEFAULT NULL,
  `situacion` char(1) DEFAULT NULL,
  `fecha_cobro` date DEFAULT NULL,
  `escuela` varchar(255) DEFAULT NULL,
  `id_entidad` tinyint(4) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `log_correos_ceneval`
--

DROP TABLE IF EXISTS `log_correos_ceneval`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log_correos_ceneval` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_alumno_ceneval` int(11) NOT NULL DEFAULT 0,
  `correo` varchar(255) NOT NULL DEFAULT '',
  `fecha_alta` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3439 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `log_correos_ceneval_bkp`
--

DROP TABLE IF EXISTS `log_correos_ceneval_bkp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log_correos_ceneval_bkp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_alumno_ceneval` int(11) NOT NULL DEFAULT 0,
  `correo` varchar(255) NOT NULL DEFAULT '',
  `fecha_alta` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=616 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `login`
--

DROP TABLE IF EXISTS `login`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `login` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL DEFAULT 0,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` char(1) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=98402 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mensajes`
--

DROP TABLE IF EXISTS `mensajes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mensajes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mensaje` text NOT NULL,
  `fecha_alta` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mensajes_destinos`
--

DROP TABLE IF EXISTS `mensajes_destinos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mensajes_destinos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_mensaje` int(11) NOT NULL DEFAULT 0,
  `id_tipo` int(11) NOT NULL DEFAULT 0,
  `id_destino` int(11) NOT NULL DEFAULT 0,
  `modulo` char(1) NOT NULL DEFAULT 'l',
  `fecha_alta` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` char(1) NOT NULL DEFAULT 'a',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `opciones_preguntas`
--

DROP TABLE IF EXISTS `opciones_preguntas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `opciones_preguntas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_pregunta` int(11) NOT NULL DEFAULT 0,
  `opcion` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `p_alumnos`
--

DROP TABLE IF EXISTS `p_alumnos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `p_alumnos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_persona` int(11) NOT NULL DEFAULT 0,
  `id_escuela` int(11) NOT NULL DEFAULT 0,
  `id_posgrado` int(11) NOT NULL DEFAULT 0,
  `entidad_radica` int(11) NOT NULL DEFAULT 0,
  `periodo` int(11) NOT NULL DEFAULT 0,
  `generacion` varchar(12) NOT NULL DEFAULT '0000-0000',
  `status` char(1) NOT NULL DEFAULT 'x',
  `fecha_alta` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `persona_alumno` (`id_persona`)
) ENGINE=MyISAM AUTO_INCREMENT=36375 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `p_autorizaciones`
--

DROP TABLE IF EXISTS `p_autorizaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `p_autorizaciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_posgrado` int(11) NOT NULL,
  `id_autorizacion` int(11) NOT NULL,
  `fecha_autorizacion` date NOT NULL,
  `no_oficio` varchar(50) NOT NULL,
  `generacion` varchar(12) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=629 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `p_bajas`
--

DROP TABLE IF EXISTS `p_bajas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `p_bajas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_persona` int(11) NOT NULL,
  `id_tipo` int(11) NOT NULL,
  `id_motivo` int(11) NOT NULL,
  `fecha` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=511 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `p_cat_autorizaciones`
--

DROP TABLE IF EXISTS `p_cat_autorizaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `p_cat_autorizaciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `autorizacion` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `p_cat_estudios`
--

DROP TABLE IF EXISTS `p_cat_estudios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `p_cat_estudios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `estudios` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `p_cat_extrangera_indigena`
--

DROP TABLE IF EXISTS `p_cat_extrangera_indigena`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `p_cat_extrangera_indigena` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lengua` varchar(200) NOT NULL,
  `tipo` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=95 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `p_cat_modos_titulacion`
--

DROP TABLE IF EXISTS `p_cat_modos_titulacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `p_cat_modos_titulacion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo_titulacion` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `p_cat_preguntas`
--

DROP TABLE IF EXISTS `p_cat_preguntas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `p_cat_preguntas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pregunta` varchar(255) NOT NULL,
  `identificador` char(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `p_docente_posgrado`
--

DROP TABLE IF EXISTS `p_docente_posgrado`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `p_docente_posgrado` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_docente` int(11) NOT NULL DEFAULT 0,
  `id_posgrado` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `fk_p_docente_posgrado_1` (`id_docente`),
  KEY `fk_p_docente_posgrado_2` (`id_posgrado`)
) ENGINE=MyISAM AUTO_INCREMENT=2125 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `p_docentes`
--

DROP TABLE IF EXISTS `p_docentes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `p_docentes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_persona` int(11) NOT NULL DEFAULT 0,
  `id_escuela` int(11) NOT NULL DEFAULT 0,
  `estado_civil` char(1) NOT NULL DEFAULT '',
  `antiguedad_docente` int(11) NOT NULL DEFAULT 0,
  `antiguedad_institucion` int(11) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'x',
  `fecha_alta` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `persona_docente` (`id_persona`)
) ENGINE=MyISAM AUTO_INCREMENT=3233 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `p_docentes_contratacion`
--

DROP TABLE IF EXISTS `p_docentes_contratacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `p_docentes_contratacion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_docente` int(11) NOT NULL DEFAULT 0,
  `id_contratacion` int(11) NOT NULL DEFAULT 0,
  `id_tiempo_dedicacion` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `fk_p_docentes_contratacion_1` (`id_docente`)
) ENGINE=MyISAM AUTO_INCREMENT=1653 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `p_estudios_realizados`
--

DROP TABLE IF EXISTS `p_estudios_realizados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `p_estudios_realizados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_persona` int(11) NOT NULL DEFAULT 0,
  `id_modo_titulacion` int(11) NOT NULL DEFAULT 0,
  `id_estudio` int(11) NOT NULL DEFAULT 0,
  `titulo` char(1) NOT NULL DEFAULT '',
  `cedula_profecional` varchar(20) NOT NULL DEFAULT '',
  `fecha_titulacion` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=35288 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `p_experiencia_docente`
--

DROP TABLE IF EXISTS `p_experiencia_docente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `p_experiencia_docente` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_docente` int(11) NOT NULL,
  `id_nivel` int(11) NOT NULL,
  `anios` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_nivel` (`id_nivel`),
  KEY `fk_p_experiencia_docente_1` (`id_docente`)
) ENGINE=MyISAM AUTO_INCREMENT=4429 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `p_generaciones`
--

DROP TABLE IF EXISTS `p_generaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `p_generaciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_posgrado` int(11) NOT NULL DEFAULT 0,
  `generacion` varchar(12) NOT NULL DEFAULT '0000-0000',
  `ano_inicio` int(4) NOT NULL DEFAULT 0,
  `anio_conclusion` int(4) NOT NULL DEFAULT 0,
  `total_alumnos` int(11) NOT NULL,
  `no_egresados` int(11) NOT NULL DEFAULT 0,
  `no_titulados` int(11) NOT NULL DEFAULT 0,
  `termino` char(1) NOT NULL DEFAULT 't',
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_generaciones_posgrados_1` (`id_posgrado`)
) ENGINE=MyISAM AUTO_INCREMENT=1919 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `p_lengua_habilidades`
--

DROP TABLE IF EXISTS `p_lengua_habilidades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `p_lengua_habilidades` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_persona` int(11) NOT NULL DEFAULT 0,
  `id_extrangera_indigena` int(11) NOT NULL DEFAULT 0,
  `comprension_lectora` int(11) NOT NULL,
  `escritura` int(11) NOT NULL,
  `expresion_oral` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=29092 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `p_niveles_educacion`
--

DROP TABLE IF EXISTS `p_niveles_educacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `p_niveles_educacion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_nivel` char(1) NOT NULL DEFAULT '',
  `nivel` varchar(200) NOT NULL DEFAULT '',
  `sub_nivel` varchar(200) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `p_personas`
--

DROP TABLE IF EXISTS `p_personas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `p_personas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `apellido_paterno` varchar(255) NOT NULL DEFAULT '',
  `apellido_materno` varchar(255) NOT NULL DEFAULT '',
  `curp` varchar(20) NOT NULL DEFAULT '',
  `genero` char(1) NOT NULL DEFAULT '',
  `edad` int(11) NOT NULL,
  `entidad_nacimiento` int(11) NOT NULL DEFAULT 0,
  `fecha_nacimiento` date NOT NULL DEFAULT '0000-00-00',
  `fecha_alta` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=37650 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `p_posgrados`
--

DROP TABLE IF EXISTS `p_posgrados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `p_posgrados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_escuela` int(11) NOT NULL DEFAULT 0,
  `posgrado` varchar(255) NOT NULL DEFAULT '',
  `modalidad` char(1) NOT NULL DEFAULT '',
  `horas` int(11) NOT NULL DEFAULT 0,
  `modulos` int(11) DEFAULT 0,
  `total_creditos` int(11) NOT NULL DEFAULT 0,
  `nivel_posgrado` int(11) NOT NULL DEFAULT 0,
  `fecha_inicio_operacion` date NOT NULL DEFAULT '0000-00-00',
  `tipo_periodo` int(11) NOT NULL DEFAULT 0,
  `num_periodos` int(11) NOT NULL DEFAULT 0,
  `orientacion` int(11) NOT NULL,
  `costo` int(11) NOT NULL,
  `nivel_ciees` int(11) NOT NULL,
  `fecha_alta` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_p_posgrados_1` (`id_escuela`)
) ENGINE=MyISAM AUTO_INCREMENT=669 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `p_preguntas_alumnos_docentes`
--

DROP TABLE IF EXISTS `p_preguntas_alumnos_docentes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `p_preguntas_alumnos_docentes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_persona` int(11) NOT NULL DEFAULT 0,
  `confirmacion` char(1) NOT NULL DEFAULT 'n',
  `id_pregunta` int(11) NOT NULL DEFAULT 0,
  `respuesta` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=65940 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `personal`
--

DROP TABLE IF EXISTS `personal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personal` (
  `id` tinyint(4) NOT NULL,
  `curp` tinyint(4) NOT NULL,
  `edad` tinyint(4) NOT NULL,
  `genero` tinyint(4) NOT NULL,
  `apellido_paterno` tinyint(4) NOT NULL,
  `apellido_materno` tinyint(4) NOT NULL,
  `nombre` tinyint(4) NOT NULL,
  `id_escuela` tinyint(4) NOT NULL,
  `id_funcion` tinyint(4) NOT NULL,
  `correo_electronico` tinyint(4) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `fecha_alta` tinyint(4) NOT NULL,
  `fecha_actualizacion` tinyint(4) NOT NULL,
  `did` tinyint(4) NOT NULL,
  `id_nivel_estudio` tinyint(4) NOT NULL,
  `titulo` tinyint(4) NOT NULL,
  `escuela_egreso` tinyint(4) NOT NULL,
  `fecha_titulacion` tinyint(4) NOT NULL,
  `tema_titulacion` tinyint(4) NOT NULL,
  `lengua_indigena` tinyint(4) NOT NULL,
  `antiguedad_escuela` tinyint(4) NOT NULL,
  `antiguedad_docente` tinyint(4) NOT NULL,
  `grupo_investigacion` tinyint(4) NOT NULL,
  `proyecto_investigacion` tinyint(4) NOT NULL,
  `estancia_investigacion` tinyint(4) NOT NULL,
  `id_area_conocimiento` tinyint(4) NOT NULL,
  `cid` tinyint(4) NOT NULL,
  `forma_contratacion` tinyint(4) NOT NULL,
  `tiempo_contratacion` tinyint(4) NOT NULL,
  `categoria` tinyint(4) NOT NULL,
  `horas_grupo` tinyint(4) NOT NULL,
  `clave_contratacion` tinyint(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `personal2`
--

DROP TABLE IF EXISTS `personal2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personal2` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `curp` varchar(20) NOT NULL DEFAULT '',
  `edad` tinyint(4) NOT NULL DEFAULT 0,
  `genero` char(1) NOT NULL DEFAULT 'm',
  `apellido_paterno` varchar(50) NOT NULL DEFAULT '',
  `apellido_materno` varchar(50) NOT NULL DEFAULT '',
  `nombre` varchar(50) NOT NULL DEFAULT '',
  `id_escuela` char(15) NOT NULL DEFAULT '',
  `id_funcion` tinyint(4) NOT NULL DEFAULT 0,
  `correo_electronico` varchar(128) NOT NULL DEFAULT '',
  `status` char(1) NOT NULL DEFAULT 'x',
  `fecha_alta` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `discapacidad` char(1) NOT NULL DEFAULT '',
  `tipo_discapacidad` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `curp` (`curp`)
) ENGINE=MyISAM AUTO_INCREMENT=72895 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `personal_curp`
--

DROP TABLE IF EXISTS `personal_curp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personal_curp` (
  `id` tinyint(4) NOT NULL,
  `apellido_paterno` tinyint(4) NOT NULL,
  `apellido_materno` tinyint(4) NOT NULL,
  `nombre` tinyint(4) NOT NULL,
  `curp` tinyint(4) NOT NULL,
  `genero` tinyint(4) NOT NULL,
  `id_escuela` tinyint(4) NOT NULL,
  `primer_apellido` tinyint(4) NOT NULL,
  `segundo_apellido` tinyint(4) NOT NULL,
  `nombre_c` tinyint(4) NOT NULL,
  `curp_valido` tinyint(4) NOT NULL,
  `genero_c` tinyint(4) NOT NULL,
  `fecha_nacimiento` tinyint(4) NOT NULL,
  `entidad_nacimiento` tinyint(4) NOT NULL,
  `entidad` tinyint(4) NOT NULL,
  `status_curp` tinyint(4) NOT NULL,
  `status_comparacion` tinyint(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `preguntas_handicapeados`
--

DROP TABLE IF EXISTS `preguntas_handicapeados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `preguntas_handicapeados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pregunta` varchar(255) NOT NULL DEFAULT '',
  `tipo` char(1) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `registro_egresados`
--

DROP TABLE IF EXISTS `registro_egresados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `registro_egresados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nid` varchar(10) NOT NULL,
  `id_alumno` int(11) NOT NULL DEFAULT 0,
  `id_escuela` int(11) NOT NULL DEFAULT 0,
  `id_licenciatura` int(11) NOT NULL DEFAULT 0,
  `id_especialidad` int(11) NOT NULL DEFAULT 0,
  `modalidad` char(1) NOT NULL DEFAULT '',
  `turno` char(1) NOT NULL DEFAULT '',
  `semestre` int(11) NOT NULL DEFAULT 0,
  `grupo` varchar(20) NOT NULL DEFAULT '',
  `matricula` char(12) NOT NULL DEFAULT '',
  `sepa_ingles` char(1) NOT NULL DEFAULT '',
  `apoyos` varchar(14) NOT NULL DEFAULT '',
  `status` char(1) NOT NULL DEFAULT '',
  `fecha_alta` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` date NOT NULL DEFAULT '0000-00-00',
  `captura` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `id_alumno` (`id_alumno`),
  KEY `captura` (`captura`),
  KEY `nid` (`nid`),
  KEY `licenciatura` (`id_licenciatura`),
  KEY `especialidad` (`id_especialidad`),
  KEY `matricula` (`matricula`),
  KEY `idescuela` (`id_escuela`)
) ENGINE=MyISAM AUTO_INCREMENT=805086 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `registro_egresados_tmp`
--

DROP TABLE IF EXISTS `registro_egresados_tmp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `registro_egresados_tmp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nid` varchar(10) NOT NULL,
  `id_alumno` int(11) NOT NULL DEFAULT 0,
  `id_escuela` int(11) NOT NULL DEFAULT 0,
  `id_licenciatura` int(11) NOT NULL DEFAULT 0,
  `id_especialidad` int(11) NOT NULL DEFAULT 0,
  `modalidad` char(1) NOT NULL DEFAULT '',
  `turno` char(1) NOT NULL DEFAULT '',
  `semestre` int(11) NOT NULL DEFAULT 0,
  `grupo` varchar(20) NOT NULL DEFAULT '',
  `matricula` char(10) NOT NULL DEFAULT '',
  `sepa_ingles` char(1) NOT NULL DEFAULT '',
  `apoyos` varchar(14) NOT NULL DEFAULT '',
  `status` char(1) NOT NULL DEFAULT '',
  `fecha_alta` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` date NOT NULL DEFAULT '0000-00-00',
  `captura` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `id_alumno` (`id_alumno`),
  KEY `captura` (`captura`),
  KEY `nid` (`nid`),
  KEY `licenciatura` (`id_licenciatura`),
  KEY `especialidad` (`id_especialidad`),
  KEY `matricula` (`matricula`),
  KEY `idescuela` (`id_escuela`)
) ENGINE=MyISAM AUTO_INCREMENT=293260 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `registro_egresados_tmp2`
--

DROP TABLE IF EXISTS `registro_egresados_tmp2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `registro_egresados_tmp2` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nid` varchar(10) NOT NULL,
  `id_alumno` int(11) NOT NULL DEFAULT 0,
  `id_escuela` int(11) NOT NULL DEFAULT 0,
  `id_licenciatura` int(11) NOT NULL DEFAULT 0,
  `id_especialidad` int(11) NOT NULL DEFAULT 0,
  `modalidad` char(1) NOT NULL DEFAULT '',
  `turno` char(1) NOT NULL DEFAULT '',
  `semestre` int(11) NOT NULL DEFAULT 0,
  `grupo` varchar(20) NOT NULL DEFAULT '',
  `matricula` char(12) NOT NULL DEFAULT '',
  `sepa_ingles` char(1) NOT NULL DEFAULT '',
  `apoyos` varchar(14) NOT NULL DEFAULT '',
  `status` char(1) NOT NULL DEFAULT '',
  `fecha_alta` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` date NOT NULL DEFAULT '0000-00-00',
  `captura` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `id_alumno` (`id_alumno`),
  KEY `captura` (`captura`),
  KEY `nid` (`nid`),
  KEY `licenciatura` (`id_licenciatura`),
  KEY `especialidad` (`id_especialidad`),
  KEY `matricula` (`matricula`),
  KEY `idescuela` (`id_escuela`)
) ENGINE=MyISAM AUTO_INCREMENT=290963 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `registros`
--

DROP TABLE IF EXISTS `registros`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `registros` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nid` varchar(10) NOT NULL,
  `id_alumno` int(11) NOT NULL DEFAULT 0,
  `id_escuela` int(11) NOT NULL DEFAULT 0,
  `id_licenciatura` int(11) NOT NULL DEFAULT 0,
  `id_especialidad` int(11) NOT NULL DEFAULT 0,
  `modalidad` char(1) NOT NULL DEFAULT '',
  `turno` char(1) NOT NULL DEFAULT '',
  `semestre` int(11) NOT NULL DEFAULT 0,
  `grupo` varchar(20) NOT NULL DEFAULT '',
  `matricula` varchar(14) DEFAULT NULL,
  `sepa_ingles` char(1) NOT NULL DEFAULT '',
  `apoyos` varchar(20) DEFAULT '',
  `status` char(1) NOT NULL DEFAULT '',
  `fecha_alta` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` date NOT NULL DEFAULT '0000-00-00',
  `captura` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `id_alumno` (`id_alumno`),
  KEY `captura` (`captura`),
  KEY `nid` (`nid`),
  KEY `licenciatura` (`id_licenciatura`),
  KEY `especialidad` (`id_especialidad`),
  KEY `matricula` (`matricula`),
  KEY `idescuela` (`id_escuela`)
) ENGINE=MyISAM AUTO_INCREMENT=8112496 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sedes`
--

DROP TABLE IF EXISTS `sedes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sedes` (
  `id_escuela` int(11) NOT NULL DEFAULT 0,
  `id_sede` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` varchar(20) NOT NULL DEFAULT '',
  `contrasenia` varchar(100) NOT NULL DEFAULT '',
  `id_renapo` char(2) NOT NULL DEFAULT '',
  `id_escuela` int(11) NOT NULL DEFAULT 0,
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `nivel` tinyint(4) NOT NULL DEFAULT 0,
  `correo_electronico` varchar(128) NOT NULL DEFAULT '',
  `status` char(1) NOT NULL DEFAULT 'x',
  `modulo` char(1) NOT NULL DEFAULT 'l',
  PRIMARY KEY (`id`),
  KEY `escuela` (`id_escuela`),
  KEY `renapo` (`id_renapo`),
  KEY `nivel` (`nivel`),
  KEY `usuario` (`id_usuario`)
) ENGINE=MyISAM AUTO_INCREMENT=2436 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usuarios_becarios`
--

DROP TABLE IF EXISTS `usuarios_becarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios_becarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(20) NOT NULL,
  `password` varchar(10) NOT NULL,
  `email` varchar(45) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `fecha` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=204 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usuarios_cursos`
--

DROP TABLE IF EXISTS `usuarios_cursos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios_cursos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(20) NOT NULL DEFAULT '',
  `contrasenia` varchar(100) NOT NULL DEFAULT '',
  `contrasenia_2` int(80) NOT NULL,
  `id_entidad` char(2) NOT NULL DEFAULT '',
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `correo_electronico` varchar(128) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usuarios_docentes`
--

DROP TABLE IF EXISTS `usuarios_docentes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios_docentes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(18) NOT NULL,
  `password` varchar(30) NOT NULL,
  `email` varchar(45) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `fecha` date NOT NULL,
  `folio` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=18399 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usuarios_herramientas`
--

DROP TABLE IF EXISTS `usuarios_herramientas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios_herramientas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(20) NOT NULL DEFAULT '',
  `contrasenia` varchar(100) NOT NULL DEFAULT '',
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `correo_electronico` varchar(128) NOT NULL DEFAULT '',
  `fecha` date NOT NULL,
  `telefono` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usuarios_lenguas`
--

DROP TABLE IF EXISTS `usuarios_lenguas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios_lenguas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(20) NOT NULL DEFAULT '',
  `contrasenia` varchar(100) NOT NULL DEFAULT '',
  `id_entidad` char(2) NOT NULL DEFAULT '',
  `nombre` varchar(255) NOT NULL DEFAULT '',
  `correo_electronico` varchar(128) NOT NULL DEFAULT '',
  `fecha` date NOT NULL,
  `telefono` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=39 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Final view structure for view `alumnos_curp`
--

/*!50001 DROP VIEW IF EXISTS `alumnos_curp`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`normalista`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `alumnos_curp` AS select `a`.`numero_identificador` AS `numero_identificador`,`a`.`apellido_paterno` AS `apellido_paterno`,`a`.`apellido_materno` AS `apellido_materno`,`a`.`nombre` AS `nombre`,`a`.`curp` AS `curp`,`a`.`edad` AS `edad`,`a`.`genero` AS `genero`,`a`.`fecha_nacimiento` AS `fecha_nacimiento`,`a`.`entidad_nacimiento` AS `entidad_nacimiento`,`b`.`id_escuela` AS `id_escuela`,`b`.`primer_apellido` AS `primer_apellido`,`b`.`segundo_apellido` AS `segundo_apellido`,`b`.`nombre_c` AS `nombre_c`,`b`.`curp_valido` AS `curp_valido`,`b`.`genero_c` AS `genero_c`,`b`.`fecha_nacimiento_c` AS `fecha_nacimiento_c`,`b`.`entidad_nacimiento_c` AS `entidad_nacimiento_c`,`b`.`entidad` AS `entidad`,`b`.`status_curp` AS `status_curp`,`b`.`status_comparacion` AS `status_comparacion` from (`alumnos` `a` join `curp_renapo` `b`) where `a`.`numero_identificador` = `b`.`nid` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `alumnos_posgrado`
--

/*!50001 DROP VIEW IF EXISTS `alumnos_posgrado`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`normalista`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `alumnos_posgrado` AS select `p_alumnos`.`id_persona` AS `id_persona`,`p_posgrados`.`id_escuela` AS `id_escuela`,`p_alumnos`.`id_posgrado` AS `id_posgrado`,`p_posgrados`.`posgrado` AS `posgrado`,`p_alumnos`.`periodo` AS `periodo`,`p_posgrados`.`tipo_periodo` AS `tipo_periodo` from (`p_alumnos` join `p_posgrados` on(`p_alumnos`.`id_posgrado` = `p_posgrados`.`id`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `cedula_view_entidadesnoaparecen`
--

/*!50001 DROP VIEW IF EXISTS `cedula_view_entidadesnoaparecen`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`normalista`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `cedula_view_entidadesnoaparecen` AS select `e`.`id_entidad` AS `id_entidad` from (`escuelas` `e` join `escuelas_status` `es` on(`e`.`id` = `es`.`id_escuela`)) where !(`e`.`id` in (select `cedula_view_idescuelasfaltantes`.`id_escuela` AS `id_escuela` from `cedula_view_idescuelasfaltantes`)) and `es`.`modulo` = _utf8'l' and `es`.`status` = _utf8'a' and `e`.`unica` <> 2 */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `cedula_view_escuelasagregadas`
--

/*!50001 DROP VIEW IF EXISTS `cedula_view_escuelasagregadas`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`normalista`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `cedula_view_escuelasagregadas` AS select `cent`.`id` AS `id`,`cent`.`entidad` AS `entidad`,count(`ce`.`id_estado`) AS `total` from (`cat_entidades` `cent` left join `cedula_view_estadosinfoescuela` `ce` on(`cent`.`id` = `ce`.`id_estado`)) group by `cent`.`id` order by `cent`.`id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-17 10:42:19
