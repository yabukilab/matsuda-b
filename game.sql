-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- ホスト: 127.0.0.1
-- 生成日時: 2024-06-28 11:43:43
-- サーバのバージョン： 10.4.32-MariaDB
-- PHP のバージョン: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- データベース: `game`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `comments`
--

CREATE TABLE `comments` (
  `ID` int(11) NOT NULL,
  `GameID` int(11) NOT NULL,
  `Comment` text NOT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- テーブルのデータのダンプ `comments`
--

INSERT INTO `comments` (`ID`, `GameID`, `Comment`, `CreatedAt`) VALUES
(1, 7, 'こんにちは\r\n', '2024-06-28 07:41:24'),
(2, 7, 'こんにちは\r\n', '2024-06-28 07:41:27'),
(3, 7, 'こちらこそ', '2024-06-28 07:41:37'),
(4, 7, 'こちらこそ\r\n', '2024-06-28 07:41:54'),
(5, 7, 'こちらこそ\r\n', '2024-06-28 07:49:42');

-- --------------------------------------------------------

--
-- テーブルの構造 `games`
--

CREATE TABLE `games` (
  `GameID` int(11) NOT NULL,
  `Title` varchar(255) NOT NULL,
  `gaiyou` int(11) DEFAULT NULL,
  `Genre` varchar(100) NOT NULL,
  `ReleaseDate` date NOT NULL,
  `CoverImageURL` varchar(255) NOT NULL,
  `Link` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- テーブルのデータのダンプ `games`
--

INSERT INTO `games` (`GameID`, `Title`, `gaiyou`, `Genre`, `ReleaseDate`, `CoverImageURL`, `Link`) VALUES
(1, 'ゲーム1', NULL, 'アクションRPG', '2022-08-11', 'https://img.gamewith.jp/img/5a27fe1b0d4509f4a75fd31be7281f26.png', 'game1.php'),
(2, 'ゲーム２', NULL, '', '0000-00-00', '', 'game2.php'),
(3, 'ゲーム3', NULL, '', '0000-00-00', '', 'game3.php'),
(4, 'ゲーム4', NULL, '', '0000-00-00', '', 'game4.php'),
(5, 'ゲーム5\r\n', NULL, '', '0000-00-00', '', 'game5.php'),
(6, 'ゲーム6', NULL, '', '0000-00-00', '', 'game6.php'),
(7, 'ゲーム7', NULL, '', '0000-00-00', '', 'game7.php'),
(9, 'ゲーム9', NULL, '', '0000-00-00', '', 'game9.php'),
(10, 'ゲーム10', NULL, '', '0000-00-00', '', 'game10.php');

-- --------------------------------------------------------

--
-- テーブルの構造 `valuation`
--

CREATE TABLE `valuation` (
  `ReviewID` int(11) NOT NULL,
  `GameID` int(11) NOT NULL,
  `Rating` int(11) NOT NULL,
  `ReviewText` text NOT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- テーブルのデータのダンプ `valuation`
--

INSERT INTO `valuation` (`ReviewID`, `GameID`, `Rating`, `ReviewText`, `CreatedAt`, `UpdatedAt`) VALUES
(1, 1, 5, 'おもろかった。あああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああ', '2024-06-07 08:19:22', '2024-06-07 08:19:22'),
(2, 1, 4, '', '2024-06-14 05:50:16', '2024-06-14 05:50:16'),
(3, 2, 3, '', '2024-06-14 05:50:16', '2024-06-14 05:50:16'),
(4, 2, 1, '', '2024-06-14 05:50:47', '2024-06-14 05:50:47'),
(5, 1, 3, '', '2024-06-14 05:50:47', '2024-06-14 05:50:47'),
(6, 3, 4, '', '2024-06-14 08:36:07', '2024-06-14 08:36:07'),
(7, 3, 4, '', '2024-06-14 08:36:15', '2024-06-14 08:36:15'),
(8, 3, 1, '', '2024-06-14 08:37:20', '2024-06-14 08:37:20'),
(9, 3, 1, '', '2024-06-14 08:37:35', '2024-06-14 08:37:35'),
(10, 3, 4, '', '2024-06-14 08:45:50', '2024-06-14 08:45:50'),
(11, 3, 4, '', '2024-06-14 09:32:12', '2024-06-14 09:32:12'),
(12, 3, 4, '', '2024-06-14 09:34:16', '2024-06-14 09:34:16'),
(13, 3, 1, '', '2024-06-14 09:34:43', '2024-06-14 09:34:43'),
(14, 3, 5, '', '2024-06-14 10:03:29', '2024-06-14 10:03:29'),
(15, 3, 4, '', '2024-06-14 10:15:44', '2024-06-14 10:15:44'),
(16, 8, 3, '', '2024-06-14 10:49:57', '2024-06-14 10:49:57'),
(17, 5, 1, '', '2024-06-28 05:39:22', '2024-06-28 05:39:22'),
(18, 5, 3, '', '2024-06-28 05:39:34', '2024-06-28 05:39:34');

-- --------------------------------------------------------

--
-- テーブルの構造 `wiki_content`
--

CREATE TABLE `wiki_content` (
  `ID` int(11) NOT NULL,
  `GameID` int(11) NOT NULL,
  `Section` varchar(255) NOT NULL,
  `Content` text NOT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- テーブルのデータのダンプ `wiki_content`
--

INSERT INTO `wiki_content` (`ID`, `GameID`, `Section`, `Content`, `CreatedAt`, `UpdatedAt`) VALUES
(1, 7, 'セクション\r\n', 'コンテンツ２', '2024-06-28 07:49:30', '2024-06-28 08:46:22');

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `GameID` (`GameID`);

--
-- テーブルのインデックス `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`GameID`);

--
-- テーブルのインデックス `valuation`
--
ALTER TABLE `valuation`
  ADD PRIMARY KEY (`ReviewID`);

--
-- テーブルのインデックス `wiki_content`
--
ALTER TABLE `wiki_content`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `unique_game_section` (`GameID`,`Section`);

--
-- ダンプしたテーブルの AUTO_INCREMENT
--

--
-- テーブルの AUTO_INCREMENT `comments`
--
ALTER TABLE `comments`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- テーブルの AUTO_INCREMENT `games`
--
ALTER TABLE `games`
  MODIFY `GameID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- テーブルの AUTO_INCREMENT `valuation`
--
ALTER TABLE `valuation`
  MODIFY `ReviewID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- テーブルの AUTO_INCREMENT `wiki_content`
--
ALTER TABLE `wiki_content`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`GameID`) REFERENCES `games` (`GameID`);

--
-- テーブルの制約 `wiki_content`
--
ALTER TABLE `wiki_content`
  ADD CONSTRAINT `wiki_content_ibfk_1` FOREIGN KEY (`GameID`) REFERENCES `games` (`GameID`);
COMMIT;
