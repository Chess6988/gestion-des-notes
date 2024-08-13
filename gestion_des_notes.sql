-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mar. 13 août 2024 à 16:18
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `gestion_des_notes`
--

-- --------------------------------------------------------

--
-- Structure de la table `admins`
--

CREATE TABLE `admins` (
  `id_admin` int(11) NOT NULL,
  `firstName_admin` varchar(50) NOT NULL,
  `lastName_admin` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `admins`
--

INSERT INTO `admins` (`id_admin`, `firstName_admin`, `lastName_admin`, `password`, `date_creation`) VALUES
(1, 'pam', 'pam', '$2y$10$rxH.Gx1UduYroY8mAjoMC.YHsGUli5ga/qVJ97jCbGbIj5Nl6EZIC', '2024-07-31 08:10:09'),
(2, 'bobo', 'bobo', '$2y$10$FBp8asMJzimT3mAf/Z.ef.W/kA6hsaB6gbY4UlsXRKq1WpBPlivKi', '2024-07-31 10:49:06'),
(3, 'valdes', 'fred', '$2y$10$QSaCdCBirq1VBv5ncrlMyup3AXnPFMGlVSFFlZMhfCabrgndAQR/q', '2024-08-01 11:25:57'),
(4, 'nba', 'nba', '$2y$10$V5.mNmtjzlVniaHgQ7LR/.3zyf5hX/1lcEZ42xQ6lUlfTluRGyWh.', '2024-08-01 11:38:26'),
(5, 'alex', 'alex', '$2y$10$2tmHzm0T6lt/MpYPql3DP.mYmfKfC9mu3vI2oKHqoA3c./Qylnf5u', '2024-08-03 05:06:56'),
(6, 'Jonias', 'Jonias', '$2y$10$pW5nUdbKH51zXFXVXa0SP.mL9T3w1UsNER0sghmRTffavWdN..SIe', '2024-08-09 13:54:29'),
(7, 'Rock', 'Rock', '$2y$10$vts1n2unu5saiPmotuYoseXDgWb1GU2J3V4xZr/oHo5LEGZ/9CE3m', '2024-08-09 14:15:22'),
(8, 'fon', 'fon', '$2y$10$8puW4wfJSg.gZ3ixfBMoPexHVbcpcLm6W4q.l.radHCddCNNyRv4u', '2024-08-12 07:31:43'),
(9, 'nbaa', 'nbaa', '$2y$10$O.w0UPpuB4R2byAKCDyQRu4vQu7G8m/y6T9B.JYAt.ygWKNQSv3hm', '2024-08-12 07:32:38'),
(10, 'go', 'go', '$2y$10$KDVn6DsJkrbtPU2p66Eflea9eumDcoi8nN1EXKHWUuT7xTFTBdFVu', '2024-08-12 07:41:05'),
(11, 'k', 'k', '$2y$10$.wFTBXa/6NiGn94idX3F3elE.clBjZu/31YyS55zYgW6evAMPInfW', '2024-08-12 07:47:20');

-- --------------------------------------------------------

--
-- Structure de la table `admins_annees`
--

CREATE TABLE `admins_annees` (
  `id_admin` int(11) NOT NULL,
  `id_annee` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déclencheurs `admins_annees`
--
DELIMITER $$
CREATE TRIGGER `check_admin_annee` BEFORE INSERT ON `admins_annees` FOR EACH ROW BEGIN
    IF NOT EXISTS (SELECT 1 FROM admins WHERE id_admin = NEW.id_admin) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Admin ID does not exist.';
    END IF;
    IF NOT EXISTS (SELECT 1 FROM annees WHERE id_annee = NEW.id_annee) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Annee ID does not exist.';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `admins_enseignants_matieres`
--

CREATE TABLE `admins_enseignants_matieres` (
  `id_admin` int(11) NOT NULL,
  `id_enseignant` int(11) NOT NULL,
  `id_matiere` int(11) NOT NULL,
  `id_matiere_commune` int(11) NOT NULL,
  `date_attribution` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `admins_filieres`
--

CREATE TABLE `admins_filieres` (
  `id_admin` int(11) NOT NULL,
  `id_filiere` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déclencheurs `admins_filieres`
--
DELIMITER $$
CREATE TRIGGER `check_admin_filiere` BEFORE INSERT ON `admins_filieres` FOR EACH ROW BEGIN
    IF NOT EXISTS (SELECT 1 FROM admins WHERE id_admin = NEW.id_admin) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Admin ID does not exist.';
    END IF;
    IF NOT EXISTS (SELECT 1 FROM filieres WHERE id_filiere = NEW.id_filiere) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Filiere ID does not exist.';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `admins_semestres`
--

CREATE TABLE `admins_semestres` (
  `id_admin` int(11) NOT NULL,
  `id_semestre` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déclencheurs `admins_semestres`
--
DELIMITER $$
CREATE TRIGGER `check_admin_semestre` BEFORE INSERT ON `admins_semestres` FOR EACH ROW BEGIN
    IF NOT EXISTS (SELECT 1 FROM admins WHERE id_admin = NEW.id_admin) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Admin ID does not exist.';
    END IF;
    IF NOT EXISTS (SELECT 1 FROM semestres WHERE id_semestre = NEW.id_semestre) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Semestre ID does not exist.';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `annees`
--

CREATE TABLE `annees` (
  `id_annee` int(11) NOT NULL,
  `annee` varchar(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `annees`
--

INSERT INTO `annees` (`id_annee`, `annee`) VALUES
(1, '2023-2024'),
(2, '2024-2025'),
(3, '2025-2026');

-- --------------------------------------------------------

--
-- Structure de la table `annees_niveaux`
--

CREATE TABLE `annees_niveaux` (
  `id_annee` int(11) NOT NULL,
  `id_niveau` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `annees_notes`
--

CREATE TABLE `annees_notes` (
  `id_annee` int(11) NOT NULL,
  `id_note` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `annees_semestres`
--

CREATE TABLE `annees_semestres` (
  `id_annee` int(11) NOT NULL,
  `id_semestre` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `enseignants`
--

CREATE TABLE `enseignants` (
  `id_enseignant` int(11) NOT NULL,
  `firstName_enseignant` varchar(50) NOT NULL,
  `lastName_enseignant` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `enseignants`
--

INSERT INTO `enseignants` (`id_enseignant`, `firstName_enseignant`, `lastName_enseignant`, `password`, `date_creation`) VALUES
(25, 'tracy', 'tracy', '$2y$10$AvJgL6Gs7e4W.ltXljuQkuQ7kKvKD4/9J2ufMPt/7fjTtEWpHx8dm', '2024-08-10 17:23:03'),
(26, 'alex', 'alex', '$2y$10$HZZIae1u.jk2DlZbsgVToOz6bSwPmDb6CzaS51Sjd6xeHURYAyShy', '2024-08-12 07:58:35'),
(27, 'Nono', 'Nono', '$2y$10$nn39D7sOySFyneM3TYUvJu.AUUkjk2Sqa5gVwQUmim5kyZGZizEsa', '2024-08-13 07:30:34'),
(28, 'John', 'John', '$2y$10$FiLmUCm45DbJN59W32ZpnebA2wtrzs0KU6bVkZOvdboi1LY8ukUJK', '2024-08-13 07:42:33');

-- --------------------------------------------------------

--
-- Structure de la table `enseignants_annees`
--

CREATE TABLE `enseignants_annees` (
  `id_enseignant` int(11) NOT NULL,
  `id_annee` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `enseignants_annees`
--

INSERT INTO `enseignants_annees` (`id_enseignant`, `id_annee`) VALUES
(1, 1),
(6, 1),
(8, 1),
(8, 3),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(14, 1);

-- --------------------------------------------------------

--
-- Structure de la table `enseignants_filieres`
--

CREATE TABLE `enseignants_filieres` (
  `id_enseignant` int(11) NOT NULL,
  `id_filiere` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `enseignants_matieres`
--

CREATE TABLE `enseignants_matieres` (
  `id_enseignant` int(11) NOT NULL,
  `id_matiere` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `enseignants_matieres_communes`
--

CREATE TABLE `enseignants_matieres_communes` (
  `id_enseignant` int(11) NOT NULL,
  `id_matiere_commune` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `enseignants_niveaux`
--

CREATE TABLE `enseignants_niveaux` (
  `id_enseignant` int(11) NOT NULL,
  `id_niveau` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `enseignants_semestres`
--

CREATE TABLE `enseignants_semestres` (
  `id_enseignant` int(11) NOT NULL,
  `id_semestre` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `etudiants`
--

CREATE TABLE `etudiants` (
  `id_etudiant` int(11) NOT NULL,
  `firstName_etudiant` varchar(50) NOT NULL,
  `lastName_etudiant` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_filiere` int(11) DEFAULT NULL,
  `id_niveau` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `etudiants`
--

INSERT INTO `etudiants` (`id_etudiant`, `firstName_etudiant`, `lastName_etudiant`, `password`, `date_creation`, `id_filiere`, `id_niveau`) VALUES
(26, 'nbaa', 'nbaa', '$2y$10$wCGKl6iu/LH1MsxYHaGONuXmVqNBJ/IQdWV14PaDoL4SZcEGopiu.', '2024-08-10 16:39:04', 1, 1),
(27, 'jonias', 'jonias', '$2y$10$DHc73v70a7MSsVfmGYcqTeeAeU.BppBIXf3r4T.4BXw5hfLFrl9ha', '2024-08-10 16:39:58', 1, 1),
(28, 'valdes', 'valdes', '$2y$10$EJWNoDO/y.5GlhyG.65WEureKtpim051VVFgb6BFCqoPJ4v9WfMai', '2024-08-10 16:56:05', 1, 1),
(29, 'Boy', 'Boy', '$2y$10$5aLhHQYotybBmevnvA2rDuqD8V21o/da85Q1SF9RuTxsfThMhNKCm', '2024-08-10 17:19:34', 1, 1),
(30, 'Kuete ', 'Valdes', '$2y$10$m2U1vhFfX/XYqWD9hbGHKueXOWGVhD0GXdOtU4EJ.sZcG9pWAMzrm', '2024-08-13 07:27:19', 1, 1),
(31, 'Rock', 'Rock', '$2y$10$PfM0g6uuttp2KvAMuFNW/OFsxTZXN6KmKj/Rc4NV7eI6Un8z2mtRq', '2024-08-13 07:39:05', 1, 1);

--
-- Déclencheurs `etudiants`
--
DELIMITER $$
CREATE TRIGGER `before_etudiants_insert` BEFORE INSERT ON `etudiants` FOR EACH ROW BEGIN
    SET NEW.date_creation = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `etudiants_annees`
--

CREATE TABLE `etudiants_annees` (
  `id_etudiant` int(11) NOT NULL,
  `id_annee` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `etudiants_annees`
--

INSERT INTO `etudiants_annees` (`id_etudiant`, `id_annee`) VALUES
(1, 2),
(2, 1),
(3, 3),
(4, 3),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 2),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 2),
(17, 1),
(18, 1),
(21, 1),
(22, 3),
(23, 1),
(24, 1),
(26, 1),
(27, 1),
(28, 1),
(29, 1),
(30, 2),
(31, 2);

-- --------------------------------------------------------

--
-- Structure de la table `etudiants_notes`
--

CREATE TABLE `etudiants_notes` (
  `id_etudiant` int(11) NOT NULL,
  `id_note` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `etudiants_semestres`
--

CREATE TABLE `etudiants_semestres` (
  `id_etudiant` int(11) NOT NULL,
  `id_semestre` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `etudiants_semestres`
--

INSERT INTO `etudiants_semestres` (`id_etudiant`, `id_semestre`) VALUES
(1, 2),
(2, 1),
(3, 2),
(4, 2),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 2),
(17, 1),
(18, 1),
(21, 2),
(22, 1),
(23, 1),
(24, 1),
(26, 1),
(27, 1),
(28, 1),
(29, 1),
(30, 1),
(31, 1);

-- --------------------------------------------------------

--
-- Structure de la table `filieres`
--

CREATE TABLE `filieres` (
  `id_filiere` int(11) NOT NULL,
  `nom_filiere` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `filieres`
--

INSERT INTO `filieres` (`id_filiere`, `nom_filiere`) VALUES
(1, 'Prepa'),
(2, 'GLT'),
(3, 'MCV');

-- --------------------------------------------------------

--
-- Structure de la table `filieres_semestres`
--

CREATE TABLE `filieres_semestres` (
  `id_filiere` int(11) NOT NULL,
  `id_semestre` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `matieres`
--

CREATE TABLE `matieres` (
  `id_matiere` int(11) NOT NULL,
  `nom_matiere` varchar(100) NOT NULL,
  `courseCode` varchar(52) NOT NULL,
  `id_filiere` int(11) DEFAULT NULL,
  `id_semestre` int(11) DEFAULT NULL,
  `id_niveau` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `matieres`
--

INSERT INTO `matieres` (`id_matiere`, `nom_matiere`, `courseCode`, `id_filiere`, `id_semestre`, `id_niveau`) VALUES
(35, 'Language C', 'LANG1101_prep', 1, 1, 1),
(36, 'Architecture des ordinateurs', 'ARCH1104_prep', 1, 1, 1),
(37, 'Algebre Linear', 'ALGEB1103_prep', 1, 1, 1),
(38, 'Analyse 1', 'ANALY1104_prep', 1, 1, 1),
(39, 'Algorithm', 'ALGOR1105_prep', 1, 1, 1),
(40, 'EMO (environment et micro-ordinateur)', 'EMO1106_prep', 1, 1, 1),
(41, 'Math General', 'MATHG1101_glt', 2, 1, 1),
(42, 'Statistique 1', 'STATI1102_glt', 2, 1, 1),
(43, 'Economie generale', 'ECONO1103_glt', 2, 1, 1),
(44, 'Transport Maritime', 'TRANS1104_glt', 2, 1, 1),
(45, 'Math financier I', 'MATHF1105_glt', 2, 1, 1),
(46, 'Math general I', 'MATHG1101_mcv', 3, 1, 1),
(47, 'EBL', 'EBL1102_mcv', 3, 1, 1),
(48, 'Statistique', 'STATI1103_mcv', 3, 1, 1),
(49, 'Math financier', 'MATHF1104_mcv', 3, 1, 1),
(50, 'Politique de distribution', 'POLIT1105_mcv', 3, 1, 1),
(51, 'Algebre de Bool', 'ALGEB1201_prep', 1, 2, 1),
(52, 'Outil Bureautique', 'OUTIL1202_prep', 1, 2, 1),
(53, 'Infographie', 'INFOG1203_prep', 1, 2, 1),
(54, 'Programmation web', 'PROGR1204_prep', 1, 2, 1),
(55, 'Mecanique et Electriciter', 'MECAN1205_prep', 1, 2, 1),
(56, 'System d\'exploitation', 'SYSTE1206_prep', 1, 2, 1),
(57, 'Math financier II', 'MATHF1201_glt', 2, 2, 1),
(58, 'Transport routier', 'TRANS1202_glt', 2, 2, 1),
(59, 'Math financier II', 'MATHF1201_mcv', 3, 2, 1),
(60, 'Transport routier', 'TRANS1202_mcv', 3, 2, 1),
(61, 'Advanced Language C', 'ADVLA2101_prep', 1, 1, 2),
(62, 'Computer Networks', 'COMPU2102_prep', 1, 1, 2),
(63, 'Advanced Algebra', 'ADVAL2103_prep', 1, 1, 2),
(64, 'Data Structures', 'DATAS2104_prep', 1, 1, 2),
(65, 'Advanced Math', 'ADVM2101_glt', 2, 1, 2),
(66, 'Operational Research', 'OPERA2102_glt', 2, 1, 2),
(67, 'Logistics Management', 'LOGIS2103_glt', 2, 1, 2),
(68, 'Financial Analysis', 'FINAN2104_glt', 2, 1, 2),
(69, 'Advanced Math General II', 'ADVM2101_mcv', 3, 1, 2),
(70, 'Business Law', 'BUSIN2102_mcv', 3, 1, 2),
(71, 'Advanced Statistics', 'ADVST2103_mcv', 3, 1, 2),
(72, 'Marketing Strategy', 'MARKS2104_mcv', 3, 1, 2),
(73, 'Software Engineering', 'SOFTW2201_prep', 1, 2, 2),
(74, 'Operating Systems', 'OPERA2202_prep', 1, 2, 2),
(75, 'Database Systems', 'DATAB2203_prep', 1, 2, 2),
(76, 'Compiler Design', 'COMPI2204_prep', 1, 2, 2),
(77, 'Supply Chain Management', 'SUPPL2201_glt', 2, 2, 2),
(78, 'Advanced Transport Logistics', 'ADVTR2202_glt', 2, 2, 2),
(79, 'Warehouse Management', 'WAREH2203_glt', 2, 2, 2),
(80, 'International Trade', 'INTER2204_glt', 2, 2, 2),
(81, 'Market Research', 'MARKR2201_mcv', 3, 2, 2),
(82, 'Consumer Behavior', 'CONSU2202_mcv', 3, 2, 2),
(83, 'Sales Management', 'SALES2203_mcv', 3, 2, 2),
(84, 'Brand Management', 'BRAND2204_mcv', 3, 2, 2);

-- --------------------------------------------------------

--
-- Structure de la table `matieres_communes`
--

CREATE TABLE `matieres_communes` (
  `id_matiere_commune` int(11) NOT NULL,
  `nom_matiere_commune` varchar(100) NOT NULL,
  `courseCode` varchar(52) NOT NULL,
  `id_filiere` int(11) DEFAULT NULL,
  `id_semestre` int(11) DEFAULT NULL,
  `id_niveau` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `matieres_communes`
--

INSERT INTO `matieres_communes` (`id_matiere_commune`, `nom_matiere_commune`, `courseCode`, `id_filiere`, `id_semestre`, `id_niveau`) VALUES
(1, 'Economie General I', 'ECONG1101_com', NULL, 1, 1),
(2, 'Droit Commercial I', 'DROIT1102_com', NULL, 1, 1),
(3, 'Francais I', 'FRANC1103_com', NULL, 1, 1),
(4, 'Comptabilite I', 'COMPT1104_com', NULL, 1, 1),
(5, 'Economie General II', 'ECONG1201_com', NULL, 2, 1),
(6, 'Droit Commercial II', 'DROIT1202_com', NULL, 2, 1),
(7, 'Francais II', 'FRANC1203_com', NULL, 2, 1),
(8, 'Comptabilite II', 'COMPT1204_com', NULL, 2, 1),
(9, 'Droit civil II', 'DROIT1205_com', NULL, 2, 1),
(10, 'Economie et organisation des entreprises II', 'ECONO1206_com', NULL, 2, 1),
(11, 'Economie General III', 'ECONG2101_com', NULL, 1, 2),
(12, 'Droit Commercial III', 'DROIT2102_com', NULL, 1, 2),
(13, 'Francais III', 'FRANC2103_com', NULL, 1, 2),
(14, 'Comptabilite III', 'COMPT2104_com', NULL, 1, 2),
(15, 'Droit civil III', 'DROIT2105_com', NULL, 1, 2),
(16, 'Economie et organisation des entreprises III', 'ECONO2106_com', NULL, 1, 2),
(17, 'Economie General IV', 'ECONG2201_com', NULL, 2, 2),
(18, 'Droit Commercial IV', 'DROIT2202_com', NULL, 2, 2),
(19, 'Francais IV', 'FRANC2203_com', NULL, 2, 2),
(20, 'Comptabilite IV', 'COMPT2204_com', NULL, 2, 2),
(21, 'Droit civil IV', 'DROIT2205_com', NULL, 2, 2),
(22, 'Economie et organisation des entreprises IV', 'ECONO2206_com', NULL, 2, 2);

-- --------------------------------------------------------

--
-- Structure de la table `matieres_communes_etudiants`
--

CREATE TABLE `matieres_communes_etudiants` (
  `id_matiere_commune` int(11) NOT NULL,
  `id_etudiant` int(11) NOT NULL,
  `id_annee` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `matieres_communes_etudiants`
--

INSERT INTO `matieres_communes_etudiants` (`id_matiere_commune`, `id_etudiant`, `id_annee`) VALUES
(1, 26, 1),
(1, 27, 1),
(1, 28, 1),
(1, 29, 1),
(2, 26, 1),
(2, 27, 1),
(2, 28, 1),
(2, 29, 1),
(3, 26, 1),
(3, 27, 1),
(3, 28, 1),
(3, 29, 1),
(4, 26, 1),
(4, 27, 1),
(4, 28, 1),
(4, 29, 1),
(1, 30, 2),
(1, 31, 2),
(2, 30, 2),
(2, 31, 2),
(3, 30, 2),
(3, 31, 2),
(4, 30, 2),
(4, 31, 2);

-- --------------------------------------------------------

--
-- Structure de la table `matieres_etudiants`
--

CREATE TABLE `matieres_etudiants` (
  `id_matiere` int(11) NOT NULL,
  `id_etudiant` int(11) NOT NULL,
  `id_annee` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `matieres_etudiants`
--

INSERT INTO `matieres_etudiants` (`id_matiere`, `id_etudiant`, `id_annee`) VALUES
(35, 26, 1),
(35, 27, 1),
(35, 28, 1),
(35, 29, 1),
(36, 26, 1),
(36, 27, 1),
(36, 28, 1),
(36, 29, 1),
(37, 26, 1),
(37, 27, 1),
(37, 28, 1),
(37, 29, 1),
(38, 26, 1),
(38, 27, 1),
(38, 28, 1),
(38, 29, 1),
(39, 26, 1),
(39, 27, 1),
(39, 28, 1),
(39, 29, 1),
(40, 26, 1),
(40, 27, 1),
(40, 28, 1),
(40, 29, 1),
(35, 30, 2),
(35, 31, 2),
(36, 30, 2),
(36, 31, 2),
(37, 30, 2),
(37, 31, 2),
(38, 30, 2),
(38, 31, 2),
(39, 30, 2),
(39, 31, 2),
(40, 30, 2),
(40, 31, 2);

-- --------------------------------------------------------

--
-- Structure de la table `niveaux`
--

CREATE TABLE `niveaux` (
  `id_niveau` int(11) NOT NULL,
  `nom_niveau` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `niveaux`
--

INSERT INTO `niveaux` (`id_niveau`, `nom_niveau`) VALUES
(1, 'Niveau I'),
(2, 'Niveau II');

-- --------------------------------------------------------

--
-- Structure de la table `niveaux_matieres`
--

CREATE TABLE `niveaux_matieres` (
  `id_niveau` int(11) NOT NULL,
  `id_matiere` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `niveaux_matieres`
--

INSERT INTO `niveaux_matieres` (`id_niveau`, `id_matiere`) VALUES
(1, 35),
(1, 36),
(1, 37),
(1, 38),
(1, 39),
(1, 40),
(2, 77),
(2, 78),
(2, 79),
(2, 80);

-- --------------------------------------------------------

--
-- Structure de la table `niveaux_semestres`
--

CREATE TABLE `niveaux_semestres` (
  `id_niveau` int(11) NOT NULL,
  `id_semestre` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `niveaux_semestres`
--

INSERT INTO `niveaux_semestres` (`id_niveau`, `id_semestre`) VALUES
(1, 1),
(2, 2);

-- --------------------------------------------------------

--
-- Structure de la table `notes`
--

CREATE TABLE `notes` (
  `id` int(11) NOT NULL,
  `id_etudiant` int(11) NOT NULL,
  `id_matiere` int(11) DEFAULT NULL,
  `id_matiere_commune` int(11) DEFAULT NULL,
  `cc_note` float NOT NULL,
  `normal_note` float NOT NULL,
  `note_final` float NOT NULL,
  `id_annee` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `notes`
--

INSERT INTO `notes` (`id`, `id_etudiant`, `id_matiere`, `id_matiere_commune`, `cc_note`, `normal_note`, `note_final`, `id_annee`) VALUES
(37, 6, 61, NULL, 14, 14, 14, 1),
(38, 6, NULL, 11, 14, 14, 14, 1),
(39, 11, 35, NULL, 14, 12, 12, 1),
(40, 13, 35, NULL, 14, 14, 14, 1),
(41, 11, 35, NULL, 0, 16, 11, 1),
(42, 13, 35, NULL, 0, 16, 11, 1),
(43, 15, NULL, 2, 1, 10, 7.3, 1),
(44, 15, NULL, 2, 1, 10, 7.3, 1),
(48, 14, NULL, 12, 1, 1, 1, 1),
(49, 15, NULL, 1, 1, 10, 7.3, 1),
(102, 17, NULL, 1, 12, 15, 14.1, 1),
(103, 15, 45, NULL, 1, 16, 8.5, 1),
(104, 17, 45, NULL, 18, 19, 18.5, 1),
(105, 16, 80, NULL, 20, 10, 15, 2),
(106, 16, NULL, 22, 0, 0, 0, 2),
(107, 14, 64, NULL, 0, 0, 0, 1),
(108, 18, 64, NULL, 14, 1, 4.9, 1),
(109, 14, 62, NULL, 0, 0, 0, 1),
(110, 18, 62, NULL, 14, 1, 4.9, 1),
(111, 15, NULL, 4, 1, 10, 7.3, 1),
(112, 17, NULL, 4, 12, 15, 14.1, 1),
(113, 18, NULL, 12, 4, 20, 15.2, 1),
(114, 21, 80, NULL, 1, 19, 13.6, 1),
(115, 15, 42, NULL, 1, 10, 7, 1),
(116, 17, 42, NULL, 12, 15, 14, 1),
(117, 14, NULL, 11, 11, 5, 6, 1),
(118, 18, NULL, 11, 1, 10, 7, 1),
(119, 14, NULL, 13, 14, 0, 4.2, 1),
(120, 18, NULL, 13, 14, 20, 18.2, 1),
(121, 14, NULL, 14, 10, 14, 12, 1),
(122, 18, NULL, 14, 20, 1, 6, 1),
(123, 14, NULL, 15, 0, 0, 0, 1),
(124, 18, NULL, 15, 14, 1, 4, 1),
(125, 14, NULL, 16, 4, 14, 10, 1),
(126, 18, NULL, 16, 1, 2, 1, 1),
(127, 14, 61, NULL, 1, 1, 1, 1),
(128, 18, 61, NULL, 10, 15, 13, 1),
(129, 14, 63, NULL, 10, 10, 10, 1),
(130, 18, 63, NULL, 10, 10, 10, 1),
(131, 16, 77, NULL, 14, 12, 12.6, 2),
(132, 16, 78, NULL, 11, 11, 11, 2),
(133, 16, 79, NULL, 0, 0, 0, 2),
(134, 26, 35, NULL, 12, 20, 17.6, 1),
(135, 27, 35, NULL, 2, 20, 14.6, 1),
(136, 28, 35, NULL, 20, 10, 13, 1),
(137, 29, 35, NULL, 10, 1, 3.7, 1),
(139, 26, 36, NULL, 12, 20, 17.6, 1),
(140, 27, 36, NULL, 2, 20, 14.6, 1),
(141, 28, 36, NULL, 20, 10, 13, 1),
(142, 29, 36, NULL, 10, 1, 3.7, 1),
(143, 26, 38, NULL, 12, 20, 17.6, 1),
(144, 27, 38, NULL, 2, 20, 14.6, 1),
(145, 28, 38, NULL, 20, 10, 13, 1),
(146, 29, 38, NULL, 10, 1, 3.7, 1),
(147, 26, 39, NULL, 12, 20, 17.6, 1),
(148, 27, 39, NULL, 2, 20, 14.6, 1),
(149, 28, 39, NULL, 20, 10, 13, 1),
(150, 29, 39, NULL, 10, 1, 3.7, 1),
(157, 26, 40, NULL, 12, 20, 17.6, 1),
(158, 27, 40, NULL, 2, 20, 14.6, 1),
(159, 28, 40, NULL, 20, 10, 13, 1),
(160, 29, 40, NULL, 10, 1, 3.7, 1),
(161, 26, NULL, 1, 12, 20, 17.6, 1),
(162, 27, NULL, 1, 2, 20, 14.6, 1),
(163, 28, NULL, 1, 20, 10, 13, 1),
(164, 29, NULL, 1, 10, 1, 3.7, 1),
(165, 26, NULL, 2, 20, 20, 20, 1),
(166, 27, NULL, 2, 20, 20, 20, 1),
(167, 28, NULL, 2, 12, 19, 16.9, 1),
(168, 29, NULL, 2, 19, 12, 14.1, 1),
(169, 26, NULL, 3, 4, 4, 4, 1),
(170, 27, NULL, 3, 5, 9, 7, 1),
(171, 28, NULL, 3, 7, 6, 6, 1),
(172, 29, NULL, 3, 5, 6, 5, 1),
(173, 26, NULL, 4, 10, 20, 17, 1),
(174, 27, NULL, 4, 5, 20, 15, 1),
(175, 28, NULL, 4, 20, 20, 20, 1),
(176, 29, NULL, 4, 20, 10, 13, 1),
(179, 30, NULL, 1, 18, 0, 5, 2),
(180, 31, NULL, 1, 15, 15, 15, 2),
(181, 30, NULL, 2, 0, 10, 7, 2),
(182, 31, NULL, 2, 14, 15, 14, 2),
(183, 30, NULL, 3, 14, 14, 14, 2),
(184, 31, NULL, 3, 0, 20, 14, 2),
(185, 30, NULL, 4, 9, 18, 15, 2),
(186, 31, NULL, 4, 15, 14, 14, 2),
(187, 30, 35, NULL, 5, 3, 3, 2),
(188, 31, 35, NULL, 5, 7, 6, 2),
(189, 30, 36, NULL, 12, 8, 9, 2),
(190, 31, 36, NULL, 4, 13, 10, 2),
(191, 30, 38, NULL, 20, 9, 12, 2),
(192, 31, 38, NULL, 5, 3, 3, 2),
(193, 30, 37, NULL, 12, 12, 11, 2),
(194, 31, 37, NULL, 14, 0, 4, 2),
(195, 30, 39, NULL, 10, 10, 10, 2),
(196, 31, 39, NULL, 12, 12, 11, 2),
(197, 30, 40, NULL, 15, 15, 15, 2),
(198, 31, 40, NULL, 15, 15, 15, 2);

-- --------------------------------------------------------

--
-- Structure de la table `notes_log_new`
--

CREATE TABLE `notes_log_new` (
  `id` int(11) NOT NULL,
  `id_etudiant` int(11) NOT NULL,
  `id_matiere` int(11) DEFAULT NULL,
  `id_matiere_commune` int(11) DEFAULT NULL,
  `id_annee` int(11) NOT NULL,
  `old_cc_note` float DEFAULT NULL,
  `old_normal_note` float DEFAULT NULL,
  `old_note_final` float DEFAULT NULL,
  `new_cc_note` float DEFAULT NULL,
  `new_normal_note` float DEFAULT NULL,
  `new_note_final` float DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `profile_admin`
--

CREATE TABLE `profile_admin` (
  `id_profile_admin` int(11) NOT NULL,
  `id_admin` int(11) DEFAULT NULL,
  `id_filiere` int(11) DEFAULT NULL,
  `id_niveau` int(11) DEFAULT NULL,
  `id_annee` int(11) DEFAULT NULL,
  `id_semestre` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déclencheurs `profile_admin`
--
DELIMITER $$
CREATE TRIGGER `check_profile_admin` BEFORE INSERT ON `profile_admin` FOR EACH ROW BEGIN
    IF NOT EXISTS (SELECT 1 FROM admins WHERE id_admin = NEW.id_admin) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Admin ID does not exist.';
    END IF;
    IF NOT EXISTS (SELECT 1 FROM filieres WHERE id_filiere = NEW.id_filiere) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Filiere ID does not exist.';
    END IF;
    IF NOT EXISTS (SELECT 1 FROM niveaux WHERE id_niveau = NEW.id_niveau) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Niveau ID does not exist.';
    END IF;
    IF NOT EXISTS (SELECT 1 FROM annees WHERE id_annee = NEW.id_annee) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Annee ID does not exist.';
    END IF;
    IF NOT EXISTS (SELECT 1 FROM semestres WHERE id_semestre = NEW.id_semestre) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Semestre ID does not exist.';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `profile_enseignant`
--

CREATE TABLE `profile_enseignant` (
  `id_profile_enseignant` int(11) NOT NULL,
  `id_enseignant` int(11) DEFAULT NULL,
  `id_annee` int(11) DEFAULT NULL,
  `id_matiere` int(11) DEFAULT NULL,
  `id_matiere_commune` int(11) DEFAULT NULL,
  `validated` tinyint(1) DEFAULT 0,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp(),
  `new_entry` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `profile_enseignant`
--

INSERT INTO `profile_enseignant` (`id_profile_enseignant`, `id_enseignant`, `id_annee`, `id_matiere`, `id_matiere_commune`, `validated`, `date_creation`, `new_entry`) VALUES
(175, 25, 1, 35, NULL, 1, '2024-08-10 17:35:37', 0),
(176, 25, 1, 36, NULL, 1, '2024-08-10 17:35:37', 0),
(178, 25, 1, 38, NULL, 1, '2024-08-10 17:35:38', 0),
(179, 25, 1, 39, NULL, 1, '2024-08-10 17:35:38', 0),
(180, 25, 1, 40, NULL, 1, '2024-08-10 17:35:38', 0),
(181, 25, 1, NULL, 1, 1, '2024-08-10 17:35:38', 0),
(182, 25, 1, NULL, 2, 1, '2024-08-10 17:35:38', 0),
(183, 25, 1, NULL, 3, 1, '2024-08-10 17:35:38', 0),
(184, 25, 1, NULL, 4, 1, '2024-08-10 17:35:38', 0),
(186, 27, 2, 36, NULL, 0, '2024-08-13 07:31:47', 0),
(195, 28, 2, 35, NULL, 1, '2024-08-13 07:43:38', 0),
(196, 28, 2, 36, NULL, 1, '2024-08-13 07:43:38', 0),
(197, 28, 2, 37, NULL, 1, '2024-08-13 07:43:38', 0),
(198, 28, 2, 38, NULL, 1, '2024-08-13 07:43:38', 0),
(199, 28, 2, 39, NULL, 1, '2024-08-13 07:43:38', 0),
(200, 28, 2, 40, NULL, 1, '2024-08-13 07:43:38', 0),
(201, 28, 2, NULL, 1, 1, '2024-08-13 07:43:38', 0),
(202, 28, 2, NULL, 2, 1, '2024-08-13 07:43:38', 0),
(203, 28, 2, NULL, 3, 1, '2024-08-13 07:43:38', 0),
(204, 28, 2, NULL, 4, 1, '2024-08-13 07:43:38', 0);

-- --------------------------------------------------------

--
-- Structure de la table `profile_etudiant`
--

CREATE TABLE `profile_etudiant` (
  `id_profile` int(11) NOT NULL,
  `id_etudiant` int(11) DEFAULT NULL,
  `id_filiere` int(11) DEFAULT NULL,
  `id_matiere` int(11) DEFAULT NULL,
  `id_semestre` int(11) DEFAULT NULL,
  `id_annee` int(11) DEFAULT NULL,
  `id_niveau` int(11) DEFAULT NULL,
  `id_matiere_commune` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `profile_etudiant`
--

INSERT INTO `profile_etudiant` (`id_profile`, `id_etudiant`, `id_filiere`, `id_matiere`, `id_semestre`, `id_annee`, `id_niveau`, `id_matiere_commune`) VALUES
(965, 26, 1, 35, 1, 1, 1, 1),
(1109, 27, 1, 35, 1, 1, 1, 1),
(1061, 28, 1, 35, 1, 1, 1, 1),
(1085, 29, 1, 35, 1, 1, 1, 1),
(1157, 30, 1, 35, 1, 2, 1, 1),
(1205, 31, 1, 35, 1, 2, 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `roles`
--

CREATE TABLE `roles` (
  `id_role` int(11) NOT NULL,
  `role` varchar(50) NOT NULL,
  `rolePassword` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `roles`
--

INSERT INTO `roles` (`id_role`, `role`, `rolePassword`) VALUES
(9, 'student', 'student'),
(10, 'teacher', 'teacher'),
(11, 'admin', 'admin'),
(12, 'superadmin', 'superadmin');

-- --------------------------------------------------------

--
-- Structure de la table `semestres`
--

CREATE TABLE `semestres` (
  `id_semestre` int(11) NOT NULL,
  `nom_semestre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `semestres`
--

INSERT INTO `semestres` (`id_semestre`, `nom_semestre`) VALUES
(1, 'Semestre I'),
(2, 'Semestre II'),
(3, 'Rattrapage');

-- --------------------------------------------------------

--
-- Structure de la table `validation_matieres`
--

CREATE TABLE `validation_matieres` (
  `id_admin` int(11) NOT NULL,
  `id_matiere` int(11) NOT NULL,
  `id_semestre` int(11) NOT NULL,
  `date_validation` datetime DEFAULT current_timestamp(),
  `valide` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `validation_matieres_communes`
--

CREATE TABLE `validation_matieres_communes` (
  `id_admin` int(11) NOT NULL,
  `id_matiere_commune` int(11) NOT NULL,
  `id_semestre` int(11) NOT NULL,
  `date_validation` datetime DEFAULT current_timestamp(),
  `valide` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `validation_notes`
--

CREATE TABLE `validation_notes` (
  `id_admin` int(11) NOT NULL,
  `id_note` int(11) NOT NULL,
  `date_validation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `id_admin` (`id_admin`);

--
-- Index pour la table `admins_annees`
--
ALTER TABLE `admins_annees`
  ADD PRIMARY KEY (`id_admin`,`id_annee`),
  ADD KEY `id_annee` (`id_annee`);

--
-- Index pour la table `admins_enseignants_matieres`
--
ALTER TABLE `admins_enseignants_matieres`
  ADD PRIMARY KEY (`id_admin`,`id_enseignant`,`id_matiere`,`id_matiere_commune`),
  ADD KEY `id_enseignant` (`id_enseignant`),
  ADD KEY `id_matiere` (`id_matiere`),
  ADD KEY `id_matiere_commune` (`id_matiere_commune`);

--
-- Index pour la table `admins_filieres`
--
ALTER TABLE `admins_filieres`
  ADD PRIMARY KEY (`id_admin`,`id_filiere`),
  ADD KEY `id_filiere` (`id_filiere`);

--
-- Index pour la table `admins_semestres`
--
ALTER TABLE `admins_semestres`
  ADD PRIMARY KEY (`id_admin`,`id_semestre`),
  ADD KEY `id_semestre` (`id_semestre`);

--
-- Index pour la table `annees`
--
ALTER TABLE `annees`
  ADD PRIMARY KEY (`id_annee`);

--
-- Index pour la table `annees_niveaux`
--
ALTER TABLE `annees_niveaux`
  ADD PRIMARY KEY (`id_annee`,`id_niveau`),
  ADD KEY `id_niveau` (`id_niveau`);

--
-- Index pour la table `annees_notes`
--
ALTER TABLE `annees_notes`
  ADD PRIMARY KEY (`id_annee`,`id_note`),
  ADD KEY `id_note` (`id_note`);

--
-- Index pour la table `annees_semestres`
--
ALTER TABLE `annees_semestres`
  ADD PRIMARY KEY (`id_annee`,`id_semestre`),
  ADD KEY `id_semestre` (`id_semestre`);

--
-- Index pour la table `enseignants`
--
ALTER TABLE `enseignants`
  ADD PRIMARY KEY (`id_enseignant`),
  ADD UNIQUE KEY `id_enseignant` (`id_enseignant`);

--
-- Index pour la table `enseignants_annees`
--
ALTER TABLE `enseignants_annees`
  ADD PRIMARY KEY (`id_enseignant`,`id_annee`),
  ADD KEY `id_annee` (`id_annee`);

--
-- Index pour la table `enseignants_filieres`
--
ALTER TABLE `enseignants_filieres`
  ADD PRIMARY KEY (`id_enseignant`,`id_filiere`),
  ADD KEY `id_filiere` (`id_filiere`);

--
-- Index pour la table `enseignants_matieres`
--
ALTER TABLE `enseignants_matieres`
  ADD PRIMARY KEY (`id_enseignant`,`id_matiere`),
  ADD KEY `id_matiere` (`id_matiere`);

--
-- Index pour la table `enseignants_matieres_communes`
--
ALTER TABLE `enseignants_matieres_communes`
  ADD PRIMARY KEY (`id_enseignant`,`id_matiere_commune`),
  ADD KEY `id_matiere_commune` (`id_matiere_commune`);

--
-- Index pour la table `enseignants_niveaux`
--
ALTER TABLE `enseignants_niveaux`
  ADD PRIMARY KEY (`id_enseignant`,`id_niveau`),
  ADD KEY `id_niveau` (`id_niveau`);

--
-- Index pour la table `enseignants_semestres`
--
ALTER TABLE `enseignants_semestres`
  ADD PRIMARY KEY (`id_enseignant`,`id_semestre`),
  ADD KEY `id_semestre` (`id_semestre`);

--
-- Index pour la table `etudiants`
--
ALTER TABLE `etudiants`
  ADD PRIMARY KEY (`id_etudiant`),
  ADD UNIQUE KEY `id_etudiant` (`id_etudiant`),
  ADD KEY `id_filiere` (`id_filiere`),
  ADD KEY `id_niveau` (`id_niveau`);

--
-- Index pour la table `etudiants_annees`
--
ALTER TABLE `etudiants_annees`
  ADD PRIMARY KEY (`id_etudiant`,`id_annee`),
  ADD KEY `id_annee` (`id_annee`);

--
-- Index pour la table `etudiants_notes`
--
ALTER TABLE `etudiants_notes`
  ADD PRIMARY KEY (`id_etudiant`,`id_note`),
  ADD KEY `id_note` (`id_note`);

--
-- Index pour la table `etudiants_semestres`
--
ALTER TABLE `etudiants_semestres`
  ADD PRIMARY KEY (`id_etudiant`,`id_semestre`),
  ADD KEY `id_semestre` (`id_semestre`);

--
-- Index pour la table `filieres`
--
ALTER TABLE `filieres`
  ADD PRIMARY KEY (`id_filiere`),
  ADD UNIQUE KEY `unique_id_filiere` (`id_filiere`);

--
-- Index pour la table `filieres_semestres`
--
ALTER TABLE `filieres_semestres`
  ADD PRIMARY KEY (`id_filiere`,`id_semestre`),
  ADD KEY `id_semestre` (`id_semestre`);

--
-- Index pour la table `matieres`
--
ALTER TABLE `matieres`
  ADD PRIMARY KEY (`id_matiere`),
  ADD UNIQUE KEY `courseCode` (`courseCode`),
  ADD UNIQUE KEY `unique_courseCode_matieres` (`courseCode`),
  ADD KEY `id_filiere` (`id_filiere`),
  ADD KEY `id_semestre` (`id_semestre`),
  ADD KEY `id_niveau` (`id_niveau`);

--
-- Index pour la table `matieres_communes`
--
ALTER TABLE `matieres_communes`
  ADD PRIMARY KEY (`id_matiere_commune`),
  ADD UNIQUE KEY `courseCode` (`courseCode`),
  ADD UNIQUE KEY `unique_courseCode_matieres_communes` (`courseCode`),
  ADD KEY `id_filiere` (`id_filiere`),
  ADD KEY `id_semestre` (`id_semestre`),
  ADD KEY `id_niveau` (`id_niveau`);

--
-- Index pour la table `matieres_communes_etudiants`
--
ALTER TABLE `matieres_communes_etudiants`
  ADD PRIMARY KEY (`id_matiere_commune`,`id_etudiant`),
  ADD KEY `id_etudiant` (`id_etudiant`),
  ADD KEY `id_annee` (`id_annee`);

--
-- Index pour la table `matieres_etudiants`
--
ALTER TABLE `matieres_etudiants`
  ADD PRIMARY KEY (`id_matiere`,`id_etudiant`),
  ADD KEY `id_etudiant` (`id_etudiant`),
  ADD KEY `id_annee` (`id_annee`);

--
-- Index pour la table `niveaux`
--
ALTER TABLE `niveaux`
  ADD PRIMARY KEY (`id_niveau`);

--
-- Index pour la table `niveaux_matieres`
--
ALTER TABLE `niveaux_matieres`
  ADD PRIMARY KEY (`id_niveau`,`id_matiere`),
  ADD KEY `id_matiere` (`id_matiere`);

--
-- Index pour la table `niveaux_semestres`
--
ALTER TABLE `niveaux_semestres`
  ADD PRIMARY KEY (`id_niveau`,`id_semestre`),
  ADD KEY `id_semestre` (`id_semestre`);

--
-- Index pour la table `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_note` (`id_etudiant`,`id_matiere`,`id_matiere_commune`,`id_annee`),
  ADD UNIQUE KEY `unique_notes` (`id_etudiant`,`id_matiere`,`id_matiere_commune`,`id_annee`);

--
-- Index pour la table `notes_log_new`
--
ALTER TABLE `notes_log_new`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `profile_admin`
--
ALTER TABLE `profile_admin`
  ADD PRIMARY KEY (`id_profile_admin`),
  ADD KEY `id_admin` (`id_admin`),
  ADD KEY `id_filiere` (`id_filiere`),
  ADD KEY `id_niveau` (`id_niveau`),
  ADD KEY `id_annee` (`id_annee`),
  ADD KEY `id_semestre` (`id_semestre`);

--
-- Index pour la table `profile_enseignant`
--
ALTER TABLE `profile_enseignant`
  ADD PRIMARY KEY (`id_profile_enseignant`),
  ADD UNIQUE KEY `id_enseignant` (`id_enseignant`,`id_annee`,`id_matiere`,`id_matiere_commune`),
  ADD UNIQUE KEY `id_enseignant_2` (`id_enseignant`,`id_annee`,`id_matiere`,`id_matiere_commune`),
  ADD KEY `id_annee` (`id_annee`),
  ADD KEY `id_matiere` (`id_matiere`),
  ADD KEY `id_matiere_commune` (`id_matiere_commune`);

--
-- Index pour la table `profile_etudiant`
--
ALTER TABLE `profile_etudiant`
  ADD PRIMARY KEY (`id_profile`),
  ADD UNIQUE KEY `unique_profile` (`id_profile`),
  ADD UNIQUE KEY `unique_etudiant` (`id_etudiant`),
  ADD UNIQUE KEY `unique_student_profile` (`id_etudiant`,`id_filiere`,`id_matiere`,`id_semestre`,`id_annee`,`id_niveau`,`id_matiere_commune`),
  ADD KEY `id_filiere` (`id_filiere`),
  ADD KEY `id_matiere` (`id_matiere`),
  ADD KEY `id_semestre` (`id_semestre`),
  ADD KEY `id_annee` (`id_annee`),
  ADD KEY `id_niveau` (`id_niveau`),
  ADD KEY `id_matiere_commune` (`id_matiere_commune`);

--
-- Index pour la table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_role`),
  ADD UNIQUE KEY `role` (`role`);

--
-- Index pour la table `semestres`
--
ALTER TABLE `semestres`
  ADD PRIMARY KEY (`id_semestre`);

--
-- Index pour la table `validation_matieres`
--
ALTER TABLE `validation_matieres`
  ADD PRIMARY KEY (`id_admin`,`id_matiere`,`id_semestre`),
  ADD KEY `id_matiere` (`id_matiere`),
  ADD KEY `id_semestre` (`id_semestre`);

--
-- Index pour la table `validation_matieres_communes`
--
ALTER TABLE `validation_matieres_communes`
  ADD PRIMARY KEY (`id_admin`,`id_matiere_commune`,`id_semestre`),
  ADD KEY `id_matiere_commune` (`id_matiere_commune`),
  ADD KEY `id_semestre` (`id_semestre`);

--
-- Index pour la table `validation_notes`
--
ALTER TABLE `validation_notes`
  ADD PRIMARY KEY (`id_admin`,`id_note`),
  ADD KEY `id_note` (`id_note`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `admins`
--
ALTER TABLE `admins`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `annees`
--
ALTER TABLE `annees`
  MODIFY `id_annee` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `enseignants`
--
ALTER TABLE `enseignants`
  MODIFY `id_enseignant` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT pour la table `etudiants`
--
ALTER TABLE `etudiants`
  MODIFY `id_etudiant` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT pour la table `filieres`
--
ALTER TABLE `filieres`
  MODIFY `id_filiere` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `matieres`
--
ALTER TABLE `matieres`
  MODIFY `id_matiere` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT pour la table `matieres_communes`
--
ALTER TABLE `matieres_communes`
  MODIFY `id_matiere_commune` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT pour la table `niveaux`
--
ALTER TABLE `niveaux`
  MODIFY `id_niveau` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `notes`
--
ALTER TABLE `notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=199;

--
-- AUTO_INCREMENT pour la table `notes_log_new`
--
ALTER TABLE `notes_log_new`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `profile_admin`
--
ALTER TABLE `profile_admin`
  MODIFY `id_profile_admin` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `profile_enseignant`
--
ALTER TABLE `profile_enseignant`
  MODIFY `id_profile_enseignant` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=205;

--
-- AUTO_INCREMENT pour la table `profile_etudiant`
--
ALTER TABLE `profile_etudiant`
  MODIFY `id_profile` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1229;

--
-- AUTO_INCREMENT pour la table `roles`
--
ALTER TABLE `roles`
  MODIFY `id_role` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `semestres`
--
ALTER TABLE `semestres`
  MODIFY `id_semestre` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `admins_annees`
--
ALTER TABLE `admins_annees`
  ADD CONSTRAINT `admins_annees_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `admins` (`id_admin`),
  ADD CONSTRAINT `admins_annees_ibfk_2` FOREIGN KEY (`id_annee`) REFERENCES `annees` (`id_annee`);

--
-- Contraintes pour la table `admins_enseignants_matieres`
--
ALTER TABLE `admins_enseignants_matieres`
  ADD CONSTRAINT `admins_enseignants_matieres_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `admins` (`id_admin`),
  ADD CONSTRAINT `admins_enseignants_matieres_ibfk_2` FOREIGN KEY (`id_enseignant`) REFERENCES `enseignants` (`id_enseignant`),
  ADD CONSTRAINT `admins_enseignants_matieres_ibfk_3` FOREIGN KEY (`id_matiere`) REFERENCES `matieres` (`id_matiere`),
  ADD CONSTRAINT `admins_enseignants_matieres_ibfk_4` FOREIGN KEY (`id_matiere_commune`) REFERENCES `matieres_communes` (`id_matiere_commune`);

--
-- Contraintes pour la table `admins_filieres`
--
ALTER TABLE `admins_filieres`
  ADD CONSTRAINT `admins_filieres_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `admins` (`id_admin`),
  ADD CONSTRAINT `admins_filieres_ibfk_2` FOREIGN KEY (`id_filiere`) REFERENCES `filieres` (`id_filiere`);

--
-- Contraintes pour la table `admins_semestres`
--
ALTER TABLE `admins_semestres`
  ADD CONSTRAINT `admins_semestres_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `admins` (`id_admin`),
  ADD CONSTRAINT `admins_semestres_ibfk_2` FOREIGN KEY (`id_semestre`) REFERENCES `semestres` (`id_semestre`);

--
-- Contraintes pour la table `annees_niveaux`
--
ALTER TABLE `annees_niveaux`
  ADD CONSTRAINT `annees_niveaux_ibfk_1` FOREIGN KEY (`id_annee`) REFERENCES `annees` (`id_annee`),
  ADD CONSTRAINT `annees_niveaux_ibfk_2` FOREIGN KEY (`id_niveau`) REFERENCES `niveaux` (`id_niveau`);

--
-- Contraintes pour la table `annees_notes`
--
ALTER TABLE `annees_notes`
  ADD CONSTRAINT `annees_notes_ibfk_1` FOREIGN KEY (`id_annee`) REFERENCES `annees` (`id_annee`);

--
-- Contraintes pour la table `annees_semestres`
--
ALTER TABLE `annees_semestres`
  ADD CONSTRAINT `annees_semestres_ibfk_1` FOREIGN KEY (`id_annee`) REFERENCES `annees` (`id_annee`),
  ADD CONSTRAINT `annees_semestres_ibfk_2` FOREIGN KEY (`id_semestre`) REFERENCES `semestres` (`id_semestre`);

--
-- Contraintes pour la table `enseignants_annees`
--
ALTER TABLE `enseignants_annees`
  ADD CONSTRAINT `enseignants_annees_ibfk_1` FOREIGN KEY (`id_enseignant`) REFERENCES `enseignants` (`id_enseignant`),
  ADD CONSTRAINT `enseignants_annees_ibfk_2` FOREIGN KEY (`id_annee`) REFERENCES `annees` (`id_annee`);

--
-- Contraintes pour la table `enseignants_filieres`
--
ALTER TABLE `enseignants_filieres`
  ADD CONSTRAINT `enseignants_filieres_ibfk_1` FOREIGN KEY (`id_enseignant`) REFERENCES `enseignants` (`id_enseignant`),
  ADD CONSTRAINT `enseignants_filieres_ibfk_2` FOREIGN KEY (`id_filiere`) REFERENCES `filieres` (`id_filiere`);

--
-- Contraintes pour la table `enseignants_matieres`
--
ALTER TABLE `enseignants_matieres`
  ADD CONSTRAINT `enseignants_matieres_ibfk_1` FOREIGN KEY (`id_enseignant`) REFERENCES `enseignants` (`id_enseignant`),
  ADD CONSTRAINT `enseignants_matieres_ibfk_2` FOREIGN KEY (`id_matiere`) REFERENCES `matieres` (`id_matiere`);

--
-- Contraintes pour la table `enseignants_matieres_communes`
--
ALTER TABLE `enseignants_matieres_communes`
  ADD CONSTRAINT `enseignants_matieres_communes_ibfk_1` FOREIGN KEY (`id_enseignant`) REFERENCES `enseignants` (`id_enseignant`),
  ADD CONSTRAINT `enseignants_matieres_communes_ibfk_2` FOREIGN KEY (`id_matiere_commune`) REFERENCES `matieres_communes` (`id_matiere_commune`);

--
-- Contraintes pour la table `enseignants_niveaux`
--
ALTER TABLE `enseignants_niveaux`
  ADD CONSTRAINT `enseignants_niveaux_ibfk_1` FOREIGN KEY (`id_enseignant`) REFERENCES `enseignants` (`id_enseignant`),
  ADD CONSTRAINT `enseignants_niveaux_ibfk_2` FOREIGN KEY (`id_niveau`) REFERENCES `niveaux` (`id_niveau`);

--
-- Contraintes pour la table `enseignants_semestres`
--
ALTER TABLE `enseignants_semestres`
  ADD CONSTRAINT `enseignants_semestres_ibfk_1` FOREIGN KEY (`id_enseignant`) REFERENCES `enseignants` (`id_enseignant`),
  ADD CONSTRAINT `enseignants_semestres_ibfk_2` FOREIGN KEY (`id_semestre`) REFERENCES `semestres` (`id_semestre`);

--
-- Contraintes pour la table `etudiants`
--
ALTER TABLE `etudiants`
  ADD CONSTRAINT `etudiants_ibfk_1` FOREIGN KEY (`id_filiere`) REFERENCES `filieres` (`id_filiere`),
  ADD CONSTRAINT `etudiants_ibfk_2` FOREIGN KEY (`id_niveau`) REFERENCES `niveaux` (`id_niveau`);

--
-- Contraintes pour la table `etudiants_annees`
--
ALTER TABLE `etudiants_annees`
  ADD CONSTRAINT `etudiants_annees_ibfk_1` FOREIGN KEY (`id_etudiant`) REFERENCES `etudiants` (`id_etudiant`),
  ADD CONSTRAINT `etudiants_annees_ibfk_2` FOREIGN KEY (`id_annee`) REFERENCES `annees` (`id_annee`);

--
-- Contraintes pour la table `etudiants_notes`
--
ALTER TABLE `etudiants_notes`
  ADD CONSTRAINT `etudiants_notes_ibfk_1` FOREIGN KEY (`id_etudiant`) REFERENCES `etudiants` (`id_etudiant`);

--
-- Contraintes pour la table `etudiants_semestres`
--
ALTER TABLE `etudiants_semestres`
  ADD CONSTRAINT `etudiants_semestres_ibfk_1` FOREIGN KEY (`id_etudiant`) REFERENCES `etudiants` (`id_etudiant`),
  ADD CONSTRAINT `etudiants_semestres_ibfk_2` FOREIGN KEY (`id_semestre`) REFERENCES `semestres` (`id_semestre`);

--
-- Contraintes pour la table `filieres_semestres`
--
ALTER TABLE `filieres_semestres`
  ADD CONSTRAINT `filieres_semestres_ibfk_1` FOREIGN KEY (`id_filiere`) REFERENCES `filieres` (`id_filiere`),
  ADD CONSTRAINT `filieres_semestres_ibfk_2` FOREIGN KEY (`id_semestre`) REFERENCES `semestres` (`id_semestre`);

--
-- Contraintes pour la table `matieres`
--
ALTER TABLE `matieres`
  ADD CONSTRAINT `matieres_ibfk_1` FOREIGN KEY (`id_filiere`) REFERENCES `filieres` (`id_filiere`),
  ADD CONSTRAINT `matieres_ibfk_2` FOREIGN KEY (`id_semestre`) REFERENCES `semestres` (`id_semestre`),
  ADD CONSTRAINT `matieres_ibfk_4` FOREIGN KEY (`id_niveau`) REFERENCES `niveaux` (`id_niveau`);

--
-- Contraintes pour la table `matieres_communes`
--
ALTER TABLE `matieres_communes`
  ADD CONSTRAINT `matieres_communes_ibfk_1` FOREIGN KEY (`id_filiere`) REFERENCES `filieres` (`id_filiere`),
  ADD CONSTRAINT `matieres_communes_ibfk_2` FOREIGN KEY (`id_semestre`) REFERENCES `semestres` (`id_semestre`),
  ADD CONSTRAINT `matieres_communes_ibfk_3` FOREIGN KEY (`id_niveau`) REFERENCES `niveaux` (`id_niveau`);

--
-- Contraintes pour la table `matieres_communes_etudiants`
--
ALTER TABLE `matieres_communes_etudiants`
  ADD CONSTRAINT `matieres_communes_etudiants_ibfk_1` FOREIGN KEY (`id_matiere_commune`) REFERENCES `matieres_communes` (`id_matiere_commune`),
  ADD CONSTRAINT `matieres_communes_etudiants_ibfk_2` FOREIGN KEY (`id_etudiant`) REFERENCES `etudiants` (`id_etudiant`),
  ADD CONSTRAINT `matieres_communes_etudiants_ibfk_3` FOREIGN KEY (`id_annee`) REFERENCES `annees` (`id_annee`);

--
-- Contraintes pour la table `matieres_etudiants`
--
ALTER TABLE `matieres_etudiants`
  ADD CONSTRAINT `matieres_etudiants_ibfk_1` FOREIGN KEY (`id_matiere`) REFERENCES `matieres` (`id_matiere`),
  ADD CONSTRAINT `matieres_etudiants_ibfk_2` FOREIGN KEY (`id_etudiant`) REFERENCES `etudiants` (`id_etudiant`),
  ADD CONSTRAINT `matieres_etudiants_ibfk_3` FOREIGN KEY (`id_annee`) REFERENCES `annees` (`id_annee`);

--
-- Contraintes pour la table `niveaux_matieres`
--
ALTER TABLE `niveaux_matieres`
  ADD CONSTRAINT `niveaux_matieres_ibfk_1` FOREIGN KEY (`id_niveau`) REFERENCES `niveaux` (`id_niveau`),
  ADD CONSTRAINT `niveaux_matieres_ibfk_2` FOREIGN KEY (`id_matiere`) REFERENCES `matieres` (`id_matiere`);

--
-- Contraintes pour la table `niveaux_semestres`
--
ALTER TABLE `niveaux_semestres`
  ADD CONSTRAINT `niveaux_semestres_ibfk_1` FOREIGN KEY (`id_niveau`) REFERENCES `niveaux` (`id_niveau`),
  ADD CONSTRAINT `niveaux_semestres_ibfk_2` FOREIGN KEY (`id_semestre`) REFERENCES `semestres` (`id_semestre`);

--
-- Contraintes pour la table `profile_admin`
--
ALTER TABLE `profile_admin`
  ADD CONSTRAINT `profile_admin_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `admins` (`id_admin`),
  ADD CONSTRAINT `profile_admin_ibfk_2` FOREIGN KEY (`id_filiere`) REFERENCES `filieres` (`id_filiere`),
  ADD CONSTRAINT `profile_admin_ibfk_3` FOREIGN KEY (`id_niveau`) REFERENCES `niveaux` (`id_niveau`),
  ADD CONSTRAINT `profile_admin_ibfk_4` FOREIGN KEY (`id_annee`) REFERENCES `annees` (`id_annee`),
  ADD CONSTRAINT `profile_admin_ibfk_5` FOREIGN KEY (`id_semestre`) REFERENCES `semestres` (`id_semestre`);

--
-- Contraintes pour la table `profile_enseignant`
--
ALTER TABLE `profile_enseignant`
  ADD CONSTRAINT `profile_enseignant_ibfk_1` FOREIGN KEY (`id_enseignant`) REFERENCES `enseignants` (`id_enseignant`),
  ADD CONSTRAINT `profile_enseignant_ibfk_2` FOREIGN KEY (`id_annee`) REFERENCES `annees` (`id_annee`),
  ADD CONSTRAINT `profile_enseignant_ibfk_3` FOREIGN KEY (`id_matiere`) REFERENCES `matieres` (`id_matiere`),
  ADD CONSTRAINT `profile_enseignant_ibfk_4` FOREIGN KEY (`id_matiere_commune`) REFERENCES `matieres_communes` (`id_matiere_commune`);

--
-- Contraintes pour la table `profile_etudiant`
--
ALTER TABLE `profile_etudiant`
  ADD CONSTRAINT `profile_etudiant_ibfk_1` FOREIGN KEY (`id_etudiant`) REFERENCES `etudiants` (`id_etudiant`),
  ADD CONSTRAINT `profile_etudiant_ibfk_2` FOREIGN KEY (`id_filiere`) REFERENCES `filieres` (`id_filiere`),
  ADD CONSTRAINT `profile_etudiant_ibfk_3` FOREIGN KEY (`id_matiere`) REFERENCES `matieres` (`id_matiere`),
  ADD CONSTRAINT `profile_etudiant_ibfk_4` FOREIGN KEY (`id_semestre`) REFERENCES `semestres` (`id_semestre`),
  ADD CONSTRAINT `profile_etudiant_ibfk_5` FOREIGN KEY (`id_annee`) REFERENCES `annees` (`id_annee`),
  ADD CONSTRAINT `profile_etudiant_ibfk_6` FOREIGN KEY (`id_niveau`) REFERENCES `niveaux` (`id_niveau`),
  ADD CONSTRAINT `profile_etudiant_ibfk_7` FOREIGN KEY (`id_matiere_commune`) REFERENCES `matieres_communes` (`id_matiere_commune`);

--
-- Contraintes pour la table `validation_matieres`
--
ALTER TABLE `validation_matieres`
  ADD CONSTRAINT `validation_matieres_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `admins` (`id_admin`),
  ADD CONSTRAINT `validation_matieres_ibfk_2` FOREIGN KEY (`id_matiere`) REFERENCES `matieres` (`id_matiere`),
  ADD CONSTRAINT `validation_matieres_ibfk_3` FOREIGN KEY (`id_semestre`) REFERENCES `semestres` (`id_semestre`);

--
-- Contraintes pour la table `validation_matieres_communes`
--
ALTER TABLE `validation_matieres_communes`
  ADD CONSTRAINT `validation_matieres_communes_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `admins` (`id_admin`),
  ADD CONSTRAINT `validation_matieres_communes_ibfk_2` FOREIGN KEY (`id_matiere_commune`) REFERENCES `matieres_communes` (`id_matiere_commune`),
  ADD CONSTRAINT `validation_matieres_communes_ibfk_3` FOREIGN KEY (`id_semestre`) REFERENCES `semestres` (`id_semestre`);

--
-- Contraintes pour la table `validation_notes`
--
ALTER TABLE `validation_notes`
  ADD CONSTRAINT `validation_notes_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `admins` (`id_admin`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
