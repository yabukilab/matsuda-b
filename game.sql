-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- ホスト: 127.0.0.1
-- 生成日時: 2024-06-07 10:37:35
-- サーバのバージョン： 10.4.32-MariaDB
-- PHP のバージョン: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- データベース: `game`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `games`
--

CREATE TABLE `games` (
  `GameID` int(11) NOT NULL,
  `Title` varchar(255) NOT NULL,
  `Genre` varchar(100) NOT NULL,
  `ReleaseDate` date NOT NULL,
  `Description` text NOT NULL,
  `CoverImageURL` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- テーブルのデータのダンプ `games`
--

INSERT INTO `games` (`GameID`, `Title`, `Genre`, `ReleaseDate`, `Description`, `CoverImageURL`) VALUES
(1, 'ダダサバイバー', 'アクションRPG', '2022-08-11', 'ゾンビなどの敵を武器で倒して時間がくるまで耐えるメインチャプターや毎日内容が変わるデイリーチャレンジ、一週間合計14回のプレイでダメージ数を稼いでランキングで報酬や上のグループへの昇進がある末世反響、コインが稼げるデイリーステージ、基本課金キャラの解放やキャラの星アップなどに使うキャラのかけらを買う特別行動コインをゲットできる特別行動、既にクリアしたメインチャプターのステージをさらに難しくプレイし報酬をゲットするメインチャレンジ、既にクリアしたメインチャプターのステージの内5の倍数のステージをさらに強化したメガチャレンジ、その他期間限定のイベントなどがある', 'https://img.gamewith.jp/img/5a27fe1b0d4509f4a75fd31be7281f26.png');

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
(1, 1, 5, 'おもろかった。あああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああ', '2024-06-07 08:19:22', '2024-06-07 08:19:22');

--
-- ダンプしたテーブルのインデックス
--

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
-- ダンプしたテーブルの AUTO_INCREMENT
--

--
-- テーブルの AUTO_INCREMENT `games`
--
ALTER TABLE `games`
  MODIFY `GameID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- テーブルの AUTO_INCREMENT `valuation`
--
ALTER TABLE `valuation`
  MODIFY `ReviewID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
