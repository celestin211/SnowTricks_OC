-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 10 mars 2022 à 15:09
-- Version du serveur : 5.7.36
-- Version de PHP : 7.4.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;



-- --------------------------------------------------------




CREATE TABLE IF NOT EXISTS `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



INSERT INTO `category` (`id`, `title`, `description`) VALUES
(1, 'Grabs', 'Un grab consiste à attraper la planche avec la main pendant le saut.'),
(6, 'Rotations', 'Le principe est d\'effectuer une rotation horizontale pendant le saut, puis d\'attérir en position switch ou normal. La nomenclature se base sur le nombre de degrés de rotation effectués.'),
(7, 'Flips', 'Un flip est une rotation verticale. On distingue les front flips, rotations en avant, et les back flips, rotations en arrière.'),
(8, 'Rotations désaxées', 'Une rotation désaxée est une rotation initialement horizontale mais lancée avec un mouvement des épaules particulier qui désaxe la rotation.'),
(9, 'Slides', 'Un slide consiste à glisser sur une barre de slide. Le slide se fait soit avec la planche dans l\'axe de la barre, soit perpendiculaire, soit plus ou moins désaxé.');

-- --------------------------------------------------------



CREATE TABLE IF NOT EXISTS `comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trick_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_9474526CB281BE2E` (`trick_id`),
  KEY `IDX_9474526CA76ED395` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



INSERT INTO `comment` (`id`, `trick_id`, `user_id`, `content`, `created_at`) VALUES
(1, 15, 1, 'C\'est simple mais efficace', '2022-03-09 15:14:47'),
(2, 16, 1, 'J\'aime bien', '2022-03-09 17:03:53'),
(3, 16, 2, 'C\'est cool ça', '2022-03-09 17:14:29');

-- --------------------------------------------------------



CREATE TABLE IF NOT EXISTS `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20220309074427', '2022-03-09 07:44:42', 1159);

-- --------------------------------------------------------



CREATE TABLE IF NOT EXISTS `image` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trick_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_C53D045FB281BE2E` (`trick_id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



INSERT INTO `image` (`id`, `trick_id`, `name`) VALUES
(4, 12, 'stalefish-1.jpg'),
(5, 12, 'stalefish-2.jpg'),
(7, 14, '1080-1.jpg'),
(8, 16, 'backflip-1.jpg'),
(9, 16, 'backflip-2.jpg'),
(10, 15, 'frontflip-1.jpg'),
(11, 15, 'frontflip-2.jpg'),
(12, 18, 'rodeo-1.jpg'),
(13, 17, 'cork-1.jpg'),
(33, 20, 'b63d5647aa08ed0fabed45967085f2a4.jpeg'),
(34, 13, '0638ae1be04ee1d3520ee520c79a39ea.jpeg'),
(35, 13, '850bbf2474c7cb2c5edb99a52b22ee4d.jpeg'),
(36, 22, '67b8bcb316ca0cbf698d6ffa7c11d475.jpg');

-- --------------------------------------------------------




CREATE TABLE IF NOT EXISTS `trick` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `lead` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_D8F0A91E12469DE2` (`category_id`),
  KEY `IDX_D8F0A91EA76ED395` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



INSERT INTO `trick` (`id`, `category_id`, `user_id`, `title`, `image`, `content`, `created_at`, `lead`, `updated_at`) VALUES
(12, 1, 1, 'Stalefish', 'stalefish.jpg', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus est diam, tincidunt ac lorem sed, vehicula lobortis ligula. Nullam id scelerisque magna, et cursus arcu. Proin eleifend lacus sit amet bibendum imperdiet. Fusce a iaculis ex, eu euismod nisl. Proin ut fringilla nunc, ac suscipit magna. Curabitur mollis in ipsum vitae ullamcorper. In quis diam ut eros faucibus accumsan in ut est. In nec finibus ex. Etiam vulputate aliquet tortor ac consectetur. Etiam ac tortor aliquet, aliquam lacus efficitur, blandit nisl. Vestibulum in orci ut massa semper ultrices ut et nibh. Sed laoreet cursus libero eget maximus. Integer non lacus cursus tellus porttitor placerat ut sit amet magna.\r\n\r\nPellentesque sagittis fringilla massa eget bibendum. Etiam vestibulum pretium risus, in sollicitudin nisl mollis nec. Integer quis turpis vulputate, volutpat ligula nec, dignissim dolor. Suspendisse potenti. Sed eget nunc vitae urna sodales euismod. Vestibulum mollis, metus at gravida malesuada, enim leo pharetra eros, at volutpat sem odio a lacus. Donec rutrum nisl in metus vehicula, in bibendum sapien vestibulum. Donec porttitor nisi risus, ut dignissim sem iaculis eget. Suspendisse condimentum congue ex, sed placerat nibh vehicula vel. Vestibulum aliquam lobortis dapibus. Donec pharetra posuere sapien a fermentum. Duis turpis nibh, viverra vel risus vel, varius blandit dolor. Praesent fringilla, lectus malesuada dapibus pharetra, risus lorem interdum turpis, id efficitur elit quam a eros. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Integer eu convallis libero.\r\n\r\nAliquam sed enim lorem. Nunc ullamcorper bibendum mauris molestie ullamcorper. In euismod egestas felis. Vivamus interdum iaculis eros eget pretium. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Fusce non metus non urna hendrerit bibendum. Phasellus et viverra ex, ut maximus urna. Sed non justo id dolor mattis placerat eget commodo velit. Donec ut metus lacus. Proin eget eros eget elit bibendum blandit. Cras volutpat accumsan nunc in molestie. Nunc ultrices nibh ut ornare vulputate. Morbi sed efficitur sem.', '2022-03-09 18:09:46', 'Saisie de la carre backside de la planche entre les deux pieds avec la main arrière.', NULL),
(13, 6, 1, 'FS 720', '720.jpg', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus est diam, tincidunt ac lorem sed, vehicula lobortis ligula. Nullam id scelerisque magna, et cursus arcu. Proin eleifend lacus sit amet bibendum imperdiet. Fusce a iaculis ex, eu euismod nisl. Proin ut fringilla nunc, ac suscipit magna. Curabitur mollis in ipsum vitae ullamcorper. In quis diam ut eros faucibus accumsan in ut est. In nec finibus ex. Etiam vulputate aliquet tortor ac consectetur. Etiam ac tortor aliquet, aliquam lacus efficitur, blandit nisl. Vestibulum in orci ut massa semper ultrices ut et nibh. Sed laoreet cursus libero eget maximus. Integer non lacus cursus tellus porttitor placerat ut sit amet magna.\r\n\r\nPellentesque sagittis fringilla massa eget bibendum. Etiam vestibulum pretium risus, in sollicitudin nisl mollis nec. Integer quis turpis vulputate, volutpat ligula nec, dignissim dolor. Suspendisse potenti. Sed eget nunc vitae urna sodales euismod. Vestibulum mollis, metus at gravida malesuada, enim leo pharetra eros, at volutpat sem odio a lacus. Donec rutrum nisl in metus vehicula, in bibendum sapien vestibulum. Donec porttitor nisi risus, ut dignissim sem iaculis eget. Suspendisse condimentum congue ex, sed placerat nibh vehicula vel. Vestibulum aliquam lobortis dapibus. Donec pharetra posuere sapien a fermentum. Duis turpis nibh, viverra vel risus vel, varius blandit dolor. Praesent fringilla, lectus malesuada dapibus pharetra, risus lorem interdum turpis, id efficitur elit quam a eros. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Integer eu convallis libero.\r\n\r\nAliquam sed enim lorem. Nunc ullamcorper bibendum mauris molestie ullamcorper. In euismod egestas felis. Vivamus interdum iaculis eros eget pretium. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Fusce non metus non urna hendrerit bibendum. Phasellus et viverra ex, ut maximus urna. Sed non justo id dolor mattis placerat eget commodo velit. Donec ut metus lacus. Proin eget eros eget elit bibendum blandit. Cras volutpat accumsan nunc in molestie. Nunc ultrices nibh ut ornare vulputate. Morbi sed efficitur sem.', '2022-03-08 18:10:05', 'Deux tours complets en front-side.', '2019-04-11 08:07:29'),
(14, 6, 1, 'Backside Rodeo 1080', '1080.jpg', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus est diam, tincidunt ac lorem sed, vehicula lobortis ligula. Nullam id scelerisque magna, et cursus arcu. Proin eleifend lacus sit amet bibendum imperdiet. Fusce a iaculis ex, eu euismod nisl. Proin ut fringilla nunc, ac suscipit magna. Curabitur mollis in ipsum vitae ullamcorper. In quis diam ut eros faucibus accumsan in ut est. In nec finibus ex. Etiam vulputate aliquet tortor ac consectetur. Etiam ac tortor aliquet, aliquam lacus efficitur, blandit nisl. Vestibulum in orci ut massa semper ultrices ut et nibh. Sed laoreet cursus libero eget maximus. Integer non lacus cursus tellus porttitor placerat ut sit amet magna.\r\n\r\nPellentesque sagittis fringilla massa eget bibendum. Etiam vestibulum pretium risus, in sollicitudin nisl mollis nec. Integer quis turpis vulputate, volutpat ligula nec, dignissim dolor. Suspendisse potenti. Sed eget nunc vitae urna sodales euismod. Vestibulum mollis, metus at gravida malesuada, enim leo pharetra eros, at volutpat sem odio a lacus. Donec rutrum nisl in metus vehicula, in bibendum sapien vestibulum. Donec porttitor nisi risus, ut dignissim sem iaculis eget. Suspendisse condimentum congue ex, sed placerat nibh vehicula vel. Vestibulum aliquam lobortis dapibus. Donec pharetra posuere sapien a fermentum. Duis turpis nibh, viverra vel risus vel, varius blandit dolor. Praesent fringilla, lectus malesuada dapibus pharetra, risus lorem interdum turpis, id efficitur elit quam a eros. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Integer eu convallis libero.\r\n\r\nAliquam sed enim lorem. Nunc ullamcorper bibendum mauris molestie ullamcorper. In euismod egestas felis. Vivamus interdum iaculis eros eget pretium. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Fusce non metus non urna hendrerit bibendum. Phasellus et viverra ex, ut maximus urna. Sed non justo id dolor mattis placerat eget commodo velit. Donec ut metus lacus. Proin eget eros eget elit bibendum blandit. Cras volutpat accumsan nunc in molestie. Nunc ultrices nibh ut ornare vulputate. Morbi sed efficitur sem.', '2022-03-06 18:10:28', 'Trois tours avec une rotation désaxée (Rodeo).', NULL),
(15, 7, 1, 'Frontflip', 'frontflip.jpg', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus est diam, tincidunt ac lorem sed, vehicula lobortis ligula. Nullam id scelerisque magna, et cursus arcu. Proin eleifend lacus sit amet bibendum imperdiet. Fusce a iaculis ex, eu euismod nisl. Proin ut fringilla nunc, ac suscipit magna. Curabitur mollis in ipsum vitae ullamcorper. In quis diam ut eros faucibus accumsan in ut est. In nec finibus ex. Etiam vulputate aliquet tortor ac consectetur. Etiam ac tortor aliquet, aliquam lacus efficitur, blandit nisl. Vestibulum in orci ut massa semper ultrices ut et nibh. Sed laoreet cursus libero eget maximus. Integer non lacus cursus tellus porttitor placerat ut sit amet magna.\r\n\r\nPellentesque sagittis fringilla massa eget bibendum. Etiam vestibulum pretium risus, in sollicitudin nisl mollis nec. Integer quis turpis vulputate, volutpat ligula nec, dignissim dolor. Suspendisse potenti. Sed eget nunc vitae urna sodales euismod. Vestibulum mollis, metus at gravida malesuada, enim leo pharetra eros, at volutpat sem odio a lacus. Donec rutrum nisl in metus vehicula, in bibendum sapien vestibulum. Donec porttitor nisi risus, ut dignissim sem iaculis eget. Suspendisse condimentum congue ex, sed placerat nibh vehicula vel. Vestibulum aliquam lobortis dapibus. Donec pharetra posuere sapien a fermentum. Duis turpis nibh, viverra vel risus vel, varius blandit dolor. Praesent fringilla, lectus malesuada dapibus pharetra, risus lorem interdum turpis, id efficitur elit quam a eros. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Integer eu convallis libero.\r\n\r\nAliquam sed enim lorem. Nunc ullamcorper bibendum mauris molestie ullamcorper. In euismod egestas felis. Vivamus interdum iaculis eros eget pretium. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Fusce non metus non urna hendrerit bibendum. Phasellus et viverra ex, ut maximus urna. Sed non justo id dolor mattis placerat eget commodo velit. Donec ut metus lacus. Proin eget eros eget elit bibendum blandit. Cras volutpat accumsan nunc in molestie. Nunc ultrices nibh ut ornare vulputate. Morbi sed efficitur sem.', '2022-03-06 18:10:37', 'Rotation en avant.', NULL),
(16, 9, 1, 'Backflip a', 'aee17de9397e507f348b4408de28d3f0.jpg', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus est diam, tincidunt ac lorem sed, vehicula lobortis ligula. Nullam id scelerisque magna, et cursus arcu. Proin eleifend lacus sit amet bibendum imperdiet. Fusce a iaculis ex, eu euismod nisl. Proin ut fringilla nunc, ac suscipit magna. Curabitur mollis in ipsum vitae ullamcorper. In quis diam ut eros faucibus accumsan in ut est. In nec finibus ex. Etiam vulputate aliquet tortor ac consectetur. Etiam ac tortor aliquet, aliquam lacus efficitur, blandit nisl. Vestibulum in orci ut massa semper ultrices ut et nibh. Sed laoreet cursus libero eget maximus. Integer non lacus cursus tellus porttitor placerat ut sit amet magna.\r\n\r\nPellentesque sagittis fringilla massa eget bibendum. Etiam vestibulum pretium risus, in sollicitudin nisl mollis nec. Integer quis turpis vulputate, volutpat ligula nec, dignissim dolor. Suspendisse potenti. Sed eget nunc vitae urna sodales euismod. Vestibulum mollis, metus at gravida malesuada, enim leo pharetra eros, at volutpat sem odio a lacus. Donec rutrum nisl in metus vehicula, in bibendum sapien vestibulum. Donec porttitor nisi risus, ut dignissim sem iaculis eget. Suspendisse condimentum congue ex, sed placerat nibh vehicula vel. Vestibulum aliquam lobortis dapibus. Donec pharetra posuere sapien a fermentum. Duis turpis nibh, viverra vel risus vel, varius blandit dolor. Praesent fringilla, lectus malesuada dapibus pharetra, risus lorem interdum turpis, id efficitur elit quam a eros. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Integer eu convallis libero.\r\n\r\nAliquam sed enim lorem. Nunc ullamcorper bibendum mauris molestie ullamcorper. In euismod egestas felis. Vivamus interdum iaculis eros eget pretium. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Fusce non metus non urna hendrerit bibendum. Phasellus et viverra ex, ut maximus urna. Sed non justo id dolor mattis placerat eget commodo velit. Donec ut metus lacus. Proin eget eros eget elit bibendum blandit. Cras volutpat accumsan nunc in molestie. Nunc ultrices nibh ut ornare vulputate. Morbi sed efficitur sem.\r\nC\'est difficile', '2022-03-06 18:10:47', 'Rotation en arrière.', '2022-03-09 19:31:28'),
(17, 8, 1, 'Cork', 'cork.jpg', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus est diam, tincidunt ac lorem sed, vehicula lobortis ligula. Nullam id scelerisque magna, et cursus arcu. Proin eleifend lacus sit amet bibendum imperdiet. Fusce a iaculis ex, eu euismod nisl. Proin ut fringilla nunc, ac suscipit magna. Curabitur mollis in ipsum vitae ullamcorper. In quis diam ut eros faucibus accumsan in ut est. In nec finibus ex. Etiam vulputate aliquet tortor ac consectetur. Etiam ac tortor aliquet, aliquam lacus efficitur, blandit nisl. Vestibulum in orci ut massa semper ultrices ut et nibh. Sed laoreet cursus libero eget maximus. Integer non lacus cursus tellus porttitor placerat ut sit amet magna.\r\n\r\nPellentesque sagittis fringilla massa eget bibendum. Etiam vestibulum pretium risus, in sollicitudin nisl mollis nec. Integer quis turpis vulputate, volutpat ligula nec, dignissim dolor. Suspendisse potenti. Sed eget nunc vitae urna sodales euismod. Vestibulum mollis, metus at gravida malesuada, enim leo pharetra eros, at volutpat sem odio a lacus. Donec rutrum nisl in metus vehicula, in bibendum sapien vestibulum. Donec porttitor nisi risus, ut dignissim sem iaculis eget. Suspendisse condimentum congue ex, sed placerat nibh vehicula vel. Vestibulum aliquam lobortis dapibus. Donec pharetra posuere sapien a fermentum. Duis turpis nibh, viverra vel risus vel, varius blandit dolor. Praesent fringilla, lectus malesuada dapibus pharetra, risus lorem interdum turpis, id efficitur elit quam a eros. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Integer eu convallis libero.\r\n\r\nAliquam sed enim lorem. Nunc ullamcorper bibendum mauris molestie ullamcorper. In euismod egestas felis. Vivamus interdum iaculis eros eget pretium. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Fusce non metus non urna hendrerit bibendum. Phasellus et viverra ex, ut maximus urna. Sed non justo id dolor mattis placerat eget commodo velit. Donec ut metus lacus. Proin eget eros eget elit bibendum blandit. Cras volutpat accumsan nunc in molestie. Nunc ultrices nibh ut ornare vulputate. Morbi sed efficitur sem.', '2022-03-05 18:10:53', 'Un cork est une rotation horizontale plus ou moins désaxée, selon un mouvement d\'épaules effectué juste au moment du saut.', NULL),
(18, 8, 1, 'Rodeo', 'rodeo.jpg', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus est diam, tincidunt ac lorem sed, vehicula lobortis ligula. Nullam id scelerisque magna, et cursus arcu. Proin eleifend lacus sit amet bibendum imperdiet. Fusce a iaculis ex, eu euismod nisl. Proin ut fringilla nunc, ac suscipit magna. Curabitur mollis in ipsum vitae ullamcorper. In quis diam ut eros faucibus accumsan in ut est. In nec finibus ex. Etiam vulputate aliquet tortor ac consectetur. Etiam ac tortor aliquet, aliquam lacus efficitur, blandit nisl. Vestibulum in orci ut massa semper ultrices ut et nibh. Sed laoreet cursus libero eget maximus. Integer non lacus cursus tellus porttitor placerat ut sit amet magna.\r\n\r\nPellentesque sagittis fringilla massa eget bibendum. Etiam vestibulum pretium risus, in sollicitudin nisl mollis nec. Integer quis turpis vulputate, volutpat ligula nec, dignissim dolor. Suspendisse potenti. Sed eget nunc vitae urna sodales euismod. Vestibulum mollis, metus at gravida malesuada, enim leo pharetra eros, at volutpat sem odio a lacus. Donec rutrum nisl in metus vehicula, in bibendum sapien vestibulum. Donec porttitor nisi risus, ut dignissim sem iaculis eget. Suspendisse condimentum congue ex, sed placerat nibh vehicula vel. Vestibulum aliquam lobortis dapibus. Donec pharetra posuere sapien a fermentum. Duis turpis nibh, viverra vel risus vel, varius blandit dolor. Praesent fringilla, lectus malesuada dapibus pharetra, risus lorem interdum turpis, id efficitur elit quam a eros. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Integer eu convallis libero.\r\n\r\nAliquam sed enim lorem. Nunc ullamcorper bibendum mauris molestie ullamcorper. In euismod egestas felis. Vivamus interdum iaculis eros eget pretium. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Fusce non metus non urna hendrerit bibendum. Phasellus et viverra ex, ut maximus urna. Sed non justo id dolor mattis placerat eget commodo velit. Donec ut metus lacus. Proin eget eros eget elit bibendum blandit. Cras volutpat accumsan nunc in molestie. Nunc ultrices nibh ut ornare vulputate. Morbi sed efficitur sem.', '2022-03-05 18:11:00', 'Le rodeo est une rotation désaxée, qui se reconnaît par son aspect vrillé.', NULL),
(19, 9, 1, 'Nose Slide', 'noseslide.jpg', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus est diam, tincidunt ac lorem sed, vehicula lobortis ligula. Nullam id scelerisque magna, et cursus arcu. Proin eleifend lacus sit amet bibendum imperdiet. Fusce a iaculis ex, eu euismod nisl. Proin ut fringilla nunc, ac suscipit magna. Curabitur mollis in ipsum vitae ullamcorper. In quis diam ut eros faucibus accumsan in ut est. In nec finibus ex. Etiam vulputate aliquet tortor ac consectetur. Etiam ac tortor aliquet, aliquam lacus efficitur, blandit nisl. Vestibulum in orci ut massa semper ultrices ut et nibh. Sed laoreet cursus libero eget maximus. Integer non lacus cursus tellus porttitor placerat ut sit amet magna.\r\n\r\nPellentesque sagittis fringilla massa eget bibendum. Etiam vestibulum pretium risus, in sollicitudin nisl mollis nec. Integer quis turpis vulputate, volutpat ligula nec, dignissim dolor. Suspendisse potenti. Sed eget nunc vitae urna sodales euismod. Vestibulum mollis, metus at gravida malesuada, enim leo pharetra eros, at volutpat sem odio a lacus. Donec rutrum nisl in metus vehicula, in bibendum sapien vestibulum. Donec porttitor nisi risus, ut dignissim sem iaculis eget. Suspendisse condimentum congue ex, sed placerat nibh vehicula vel. Vestibulum aliquam lobortis dapibus. Donec pharetra posuere sapien a fermentum. Duis turpis nibh, viverra vel risus vel, varius blandit dolor. Praesent fringilla, lectus malesuada dapibus pharetra, risus lorem interdum turpis, id efficitur elit quam a eros. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Integer eu convallis libero.\r\n\r\nAliquam sed enim lorem. Nunc ullamcorper bibendum mauris molestie ullamcorper. In euismod egestas felis. Vivamus interdum iaculis eros eget pretium. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Fusce non metus non urna hendrerit bibendum. Phasellus et viverra ex, ut maximus urna. Sed non justo id dolor mattis placerat eget commodo velit. Donec ut metus lacus. Proin eget eros eget elit bibendum blandit. Cras volutpat accumsan nunc in molestie. Nunc ultrices nibh ut ornare vulputate. Morbi sed efficitur sem.', '2022-03-04 21:11:06', 'Un nose slide consiste à glisser sur une barre de slide avec l\'avant de la planche sur la barre.', NULL),
(20, 9, 1, 'Tail Slide', 'tailslide.jpg', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus est diam, tincidunt ac lorem sed, vehicula lobortis ligula. Nullam id scelerisque magna, et cursus arcu. Proin eleifend lacus sit amet bibendum imperdiet. Fusce a iaculis ex, eu euismod nisl. Proin ut fringilla nunc, ac suscipit magna. Curabitur mollis in ipsum vitae ullamcorper. In quis diam ut eros faucibus accumsan in ut est. In nec finibus ex. Etiam vulputate aliquet tortor ac consectetur. Etiam ac tortor aliquet, aliquam lacus efficitur, blandit nisl. Vestibulum in orci ut massa semper ultrices ut et nibh. Sed laoreet cursus libero eget maximus. Integer non lacus cursus tellus porttitor placerat ut sit amet magna.\r\n\r\nPellentesque sagittis fringilla massa eget bibendum. Etiam vestibulum pretium risus, in sollicitudin nisl mollis nec. Integer quis turpis vulputate, volutpat ligula nec, dignissim dolor. Suspendisse potenti. Sed eget nunc vitae urna sodales euismod. Vestibulum mollis, metus at gravida malesuada, enim leo pharetra eros, at volutpat sem odio a lacus. Donec rutrum nisl in metus vehicula, in bibendum sapien vestibulum. Donec porttitor nisi risus, ut dignissim sem iaculis eget. Suspendisse condimentum congue ex, sed placerat nibh vehicula vel. Vestibulum aliquam lobortis dapibus. Donec pharetra posuere sapien a fermentum. Duis turpis nibh, viverra vel risus vel, varius blandit dolor. Praesent fringilla, lectus malesuada dapibus pharetra, risus lorem interdum turpis, id efficitur elit quam a eros. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Integer eu convallis libero.\r\n\r\nAliquam sed enim lorem. Nunc ullamcorper bibendum mauris molestie ullamcorper. In euismod egestas felis. Vivamus interdum iaculis eros eget pretium. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Fusce non metus non urna hendrerit bibendum. Phasellus et viverra ex, ut maximus urna. Sed non justo id dolor mattis placerat eget commodo velit. Donec ut metus lacus. Proin eget eros eget elit bibendum blandit. Cras volutpat accumsan nunc in molestie. Nunc ultrices nibh ut ornare vulputate. Morbi sed efficitur sem.', '2022-03-01 15:11:26', 'Un tail slide consiste à glisser sur une barre de slide avec l\'arrière de la planche sur la barre.', '2019-04-11 08:04:53'),
(22, 9, 1, 'Flibs', 'e39b9d13700246bb0dfd3f474095cd4b.jpg', 'Le lorem ipsum est, en imprimerie, une suite de mots sans signification utilisée à titre provisoire pour calibrer une mise en page, le texte définitif venant remplacer le faux-texte dès qu\'il est prêt ou que la mise en page est achevée. Généralement, on utilise un texte en faux latin, le Lorem ipsum ou Lipsum.', '2022-03-09 19:29:37', 'flibs', NULL);

-- --------------------------------------------------------


CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL,
  `confirmed` tinyint(1) NOT NULL,
  `confirmation_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



INSERT INTO `user` (`id`, `username`, `email`, `password`, `avatar`, `website`, `description`, `created_at`, `confirmed`, `confirmation_token`, `role`) VALUES
(1, 'boso', 'celestin.mombela@yahoo.fr', '$argon2i$v=19$m=65536,t=4,p=1$QWpsMHZsT0hYUVhzRGZCYw$ft1OLMhCxxEuW3LdshdjLHdacAfMqI1EdwBvGpNQYqg', 'c1030aceb4f30fbb45e82bc0270df9f6.jpg', 'http://celestinservices.fr', 'Célestin Bosongo développeur web Full Stack', '2022-03-09 08:12:57', 1, '3a5a8851dad8ff35cec94b99ca5ad797', 'ROLE_ADMIN'),
(2, 'soniachoytun', 'soniachoytun@yahoo.fr', '$argon2i$v=19$m=65536,t=4,p=1$Q0ozSUJnYUIwaVBMRFJ6WQ$+P8S3Iy5eMOt3aHyg7l/kLAbhjBO83iLrZ+M4vFDmVc', 'default-avatar.jpg', NULL, NULL, '2022-03-09 15:19:07', 1, '1b164e98ef4a54b5dc981fd849e21152', 'ROLE_USER');

-- --------------------------------------------------------




CREATE TABLE IF NOT EXISTS `video` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trick_id` int(11) NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_7CC7DA2CB281BE2E` (`trick_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



INSERT INTO `video` (`id`, `trick_id`, `url`) VALUES
(1, 16, 'https://www.youtube.com/embed/tgbNymZ7vqY'),
(3, 12, 'https://www.youtube.com/embed/0Oez89EoE_c'),
(4, 12, 'https://www.youtube.com/embed/f9FjhCt_w2U'),
(6, 14, 'https://www.youtube.com/embed/vquZvxGMJT0'),
(7, 16, 'https://www.youtube.com/embed/SlhGVnFPTDE'),
(8, 16, 'https://www.youtube.com/embed/c6ry31Wc8sI'),
(9, 15, 'https://www.youtube.com/embed/xhvqu2XBvI0'),
(10, 15, 'https://www.youtube.com/embed/eGJ8keB1-JM'),
(11, 17, 'https://www.youtube.com/embed/FMHiSF0rHF8'),
(12, 18, 'https://www.youtube.com/embed/vf9Z05XY79A'),
(14, 22, 'https://www.youtube.com/embed/Ey5elKTrUCk');


ALTER TABLE `comment`
  ADD CONSTRAINT `FK_9474526CA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_9474526CB281BE2E` FOREIGN KEY (`trick_id`) REFERENCES `trick` (`id`);


ALTER TABLE `image`
  ADD CONSTRAINT ` https://127.0.0.1:8000` FOREIGN KEY (`trick_id`) REFERENCES `trick` (`id`);


ALTER TABLE `trick`
  ADD CONSTRAINT `FK_D8F0A91E12469DE2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`),
  ADD CONSTRAINT `FK_D8F0A91EA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);


ALTER TABLE `video`
  ADD CONSTRAINT `FK_7CC7DA2CB281BE2E` FOREIGN KEY (`trick_id`) REFERENCES `trick` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
