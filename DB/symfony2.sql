-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 01, 2026 at 02:08 PM
-- Server version: 8.4.3
-- PHP Version: 8.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `symfony2`
--

-- --------------------------------------------------------

--
-- Table structure for table `cat`
--

CREATE TABLE `cat` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `des` longtext,
  `dess` longtext,
  `img` varchar(255) DEFAULT NULL,
  `img2` varchar(255) DEFAULT NULL,
  `filer` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cat`
--

INSERT INTO `cat` (`id`, `name`, `des`, `dess`, `img`, `img2`, `filer`) VALUES
(3, 'cat1', 'ggfd', '<p>gfgfdd</p>', 'uploads/cats/img/bench-product-categories-1-1771780105_699b3809cf884.png', 'uploads/cats/img/thumb/bench-product-categories-1-1771780105_699b3809cf884.png', 'uploads/cats/files/cat_doc_699b380a56f52.docx'),
(5, 'cat4', NULL, NULL, 'uploads/cats/img/what-is-a-product-category-clothes-768x512-1771781170_699b3c32e45f4.jpg', 'uploads/cats/img/thumb/what-is-a-product-category-clothes-768x512-1771781170_699b3c32e45f4.jpg', NULL),
(6, 'cat5', 'jkjk', '<p>kjkjh</p>', NULL, NULL, 'uploads/cats/files/cat_doc_699b3c227aec2.docx'),
(7, 'cat3', 'hg', '<p>hghg</p>', 'uploads/cats/img/bench-product-categories-1-1771781139_699b3c137bc95.png', 'uploads/cats/img/thumb/bench-product-categories-1-1771781139_699b3c137bc95.png', 'uploads/cats/files/cat_doc_699b3c13c0a4f.docx'),
(8, 'cat2', NULL, NULL, NULL, NULL, NULL),
(9, 'cat6', 'uyyuuyyu  iiiuiy', '<p>iuiiuui</p>', NULL, NULL, NULL),
(11, 'cat888', 'kjkh', NULL, 'uploads/cats/img/bench-product-categories-1-1771871090_699c9b7238a6f.png', 'uploads/cats/img/thumb/bench-product-categories-1-1771871090_699c9b7238a6f.png', NULL),
(17, 'new dhfddgffg', 'hghgfh', '<p>hghfghf</p>', NULL, NULL, NULL),
(18, 'newcat111', 'hhghf', '<p>hhghhgfh</p>', NULL, NULL, NULL),
(19, 'anotherrrupdateddf', 'ghghg', NULL, 'uploads/cats/img/anotherrrupdateddf-1772304140_69a3370c28ac2.webp', 'uploads/cats/img/thumb/anotherrrupdateddf-1772304140_69a3370c28ac2.webp', 'uploads/cats/files/anotherrrupdateddf_07513970.docx'),
(20, 'neforfile', 'ghfgh', '<p>gfhfg</p>', 'uploads/cats/img/bench-product-categories-1-1772303967_69a3365f611c3.png', 'uploads/cats/img/thumb/bench-product-categories-1-1772303967_69a3365f611c3.png', 'uploads/cats/files/cat_doc_69a3365fad206.docx'),
(21, 'new4Forfile', 'ghgfhg', '<p>hghgf</p>', 'uploads/cats/img/new4Forfile-1772304038_69a336a6f10b9.png', 'uploads/cats/img/thumb/new4Forfile-1772304038_69a336a6f10b9.png', 'uploads/cats/files/new4Forfile_84256922.docx'),
(22, 'number5678', 'hjhj', '<p>jhjh</p>', 'uploads/cats/img/number5678-1772369681_69a4371157d1a.jpg', 'uploads/cats/img/thumb/number5678-1772369681_69a4371157d1a.jpg', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20260219184802', '2026-02-19 18:48:18', 35),
('DoctrineMigrations\\Version20260219185629', '2026-02-19 18:56:42', 30),
('DoctrineMigrations\\Version20260221154323', '2026-02-21 15:43:46', 43),
('DoctrineMigrations\\Version20260227182847', '2026-02-27 18:29:08', 109);

-- --------------------------------------------------------

--
-- Table structure for table `messenger_messages`
--

CREATE TABLE `messenger_messages` (
  `id` bigint NOT NULL,
  `body` longtext NOT NULL,
  `headers` longtext NOT NULL,
  `queue_name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subcat`
--

CREATE TABLE `subcat` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `des` longtext,
  `dess` longtext,
  `img` varchar(255) DEFAULT NULL,
  `img2` varchar(255) DEFAULT NULL,
  `filer` varchar(255) DEFAULT NULL,
  `catid` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `subcat`
--

INSERT INTO `subcat` (`id`, `name`, `des`, `dess`, `img`, `img2`, `filer`, `catid`) VALUES
(4, 'ytytry', 'ytyyty', '<p>tyttt</p>', NULL, NULL, NULL, 3),
(5, 'YHTYTYT', 'YTTYfddfsfdgff hjhj', '<p><strong>jhjh jhj jhjhj</strong></p>', 'uploads/subcats/img/YHTYTYT-1772370812_69a43b7cedfc0.png', 'uploads/subcats/img/thumb/YHTYTYT-1772370812_69a43b7cedfc0.png', 'uploads/subcats/files/YHTYTYT_06221308.docx', 7),
(6, 'subcatgeiry566', 'hjhjhjhj', NULL, NULL, NULL, NULL, 21),
(7, 'sub4', 'hghghg', NULL, NULL, NULL, NULL, 21),
(8, 'ud1111', 'bnbnbn', NULL, NULL, NULL, NULL, 3),
(9, 'ud1111', 'hhgh', NULL, NULL, NULL, NULL, 8);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `email` varchar(180) NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `email`, `roles`, `password`) VALUES
(1, 'super@admin.com', '[\"ROLE_SUPER_ADMIN\"]', '$2y$13$3VZOMA88b95pX2II.uecz.D9HQ0/aKWeXwMwJ/0zHFGIMGkJXvYy.'),
(2, 'admin@admin.com', '[\"ROLE_ADMIN\"]', '$2y$13$nkn.4PLKnel.qSKEnhux8.VdDENvWPamjGeM04rKi37SX2T0pGIYC');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cat`
--
ALTER TABLE `cat`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_9E5E43A85E237E06` (`name`);

--
-- Indexes for table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Indexes for table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750` (`queue_name`,`available_at`,`delivered_at`,`id`);

--
-- Indexes for table `subcat`
--
ALTER TABLE `subcat`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_subcat_cat_name` (`catid`,`name`),
  ADD KEY `IDX_FD7614413632DFC5` (`catid`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_IDENTIFIER_EMAIL` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cat`
--
ALTER TABLE `cat`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subcat`
--
ALTER TABLE `subcat`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `subcat`
--
ALTER TABLE `subcat`
  ADD CONSTRAINT `FK_FD7614413632DFC5` FOREIGN KEY (`catid`) REFERENCES `cat` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
