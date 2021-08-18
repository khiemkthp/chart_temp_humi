-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th8 10, 2021 lúc 06:33 PM
-- Phiên bản máy phục vụ: 10.4.20-MariaDB
-- Phiên bản PHP: 8.0.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `iot_database`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `present`
--

CREATE TABLE `present` (
  `STT` int(6) NOT NULL,
  `pre_temp` float NOT NULL,
  `pre_count` int(6) NOT NULL,
  `pre_limit` int(6) NOT NULL,
  `pre_num` int(6) NOT NULL,
  `pre_medi` float NOT NULL,
  `pre_min` float NOT NULL,
  `pre_max` float NOT NULL,
  `pre_type` int(6) NOT NULL,
  `pre_warn` int(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Đang đổ dữ liệu cho bảng `present`
--

INSERT INTO `present` (`STT`, `pre_temp`, `pre_count`, `pre_limit`, `pre_num`, `pre_medi`, `pre_min`, `pre_max`, `pre_type`, `pre_warn`) VALUES
(1, 0.5, 1, 1, 1, 5, 35, 50, 2, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `senser`
--

CREATE TABLE `senser` (
  `STT` int(6) NOT NULL,
  `temp` float NOT NULL,
  `humi` float NOT NULL,
  `time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Đang đổ dữ liệu cho bảng `senser`
--

INSERT INTO `senser` (`STT`, `temp`, `humi`, `time`) VALUES
(1, 30.4, 80, '0000-00-00 00:00:00'),
(2, 30.9, 85, NULL),
(3, 32, 85, NULL),
(4, 35, 80, NULL),
(5, 34, 80, NULL);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `present`
--
ALTER TABLE `present`
  ADD PRIMARY KEY (`STT`);

--
-- Chỉ mục cho bảng `senser`
--
ALTER TABLE `senser`
  ADD PRIMARY KEY (`STT`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `senser`
--
ALTER TABLE `senser`
  MODIFY `STT` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
