-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- ホスト: 127.0.0.1
-- 生成日時: 2021-11-03 10:32:03
-- サーバのバージョン： 10.4.20-MariaDB
-- PHP のバージョン: 8.0.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- データベース: `test`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `mst_prefecture`
--

CREATE TABLE `mst_prefecture` (
  `prefecture_cd` char(2) NOT NULL COMMENT '都道府県コード',
  `prefecture_name` varchar(5) NOT NULL COMMENT '都道府県名'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `mst_prefecture`
--

INSERT INTO `mst_prefecture` (`prefecture_cd`, `prefecture_name`) VALUES
('01', '愛媛'),
('02', '秋田'),
('03', '岩手'),
('04', '福岡'),
('05', '山梨'),
('06', '山形'),
('07', '福島'),
('08', '長野'),
('09', '埼玉'),
('10', '大阪府'),
('11', '京都府'),
('12', '宮崎'),
('13', '青森'),
('14', '東京'),
('15', '島根'),
('16', '鹿児島'),
('17', '千葉'),
('23', '鳥取'),
('26', '岡山'),
('28', '沖縄');

-- --------------------------------------------------------

--
-- テーブルの構造 `mst_staff`
--

CREATE TABLE `mst_staff` (
  `staff_id` varchar(20) NOT NULL COMMENT '社員ID',
  `staff_name` varchar(50) NOT NULL COMMENT '社員名',
  `sex_div` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '性別区分',
  `staff_tel` varchar(11) NOT NULL COMMENT '電話番号',
  `prefecture_cd` char(2) NOT NULL COMMENT '都道府県コード',
  `staff_address` varchar(255) NOT NULL COMMENT '住所',
  `staff_birthday` date NOT NULL COMMENT '生年月日',
  `staff_password` varchar(255) NOT NULL COMMENT 'パスワード',
  `staff_note` varchar(255) DEFAULT NULL COMMENT '備考',
  `insert_at` datetime NOT NULL COMMENT '登録日時',
  `update_at` datetime DEFAULT NULL COMMENT '更新日時',
  `delete_flg` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '削除フラグ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `mst_staff`
--

INSERT INTO `mst_staff` (`staff_id`, `staff_name`, `sex_div`, `staff_tel`, `prefecture_cd`, `staff_address`, `staff_birthday`, `staff_password`, `staff_note`, `insert_at`, `update_at`, `delete_flg`) VALUES
('aaaa', 'テスト', 1, '06058458595', '28', 'hsuhuh', '2001-12-31', 'abcdefgh', NULL, '2021-11-03 15:27:11', '2021-11-03 16:12:02', 1),

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `mst_prefecture`
--
ALTER TABLE `mst_prefecture`
  ADD PRIMARY KEY (`prefecture_cd`);

--
-- テーブルのインデックス `mst_staff`
--
ALTER TABLE `mst_staff`
  ADD PRIMARY KEY (`staff_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
