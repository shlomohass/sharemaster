-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Aug 20, 2016 at 08:25 PM
-- Server version: 5.7.9-log
-- PHP Version: 5.6.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dbsharemaster`
--

-- --------------------------------------------------------

--
-- Table structure for table `db_error_log`
--

CREATE TABLE `db_error_log` (
  `id` int(11) NOT NULL,
  `when` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `page` varchar(250) NOT NULL,
  `user_ip` varchar(250) DEFAULT NULL,
  `proxy` varchar(500) DEFAULT NULL,
  `host` varchar(500) DEFAULT NULL,
  `sql_message` text,
  `query_used` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tokens_error_log`
--

CREATE TABLE `tokens_error_log` (
  `id` int(11) NOT NULL,
  `page` varchar(500) NOT NULL,
  `token_used` varchar(250) NOT NULL,
  `sender_ip` varchar(250) NOT NULL,
  `proxy` varchar(500) NOT NULL,
  `host` varchar(500) NOT NULL,
  `when` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'when occured'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(32) NOT NULL,
  `session` varchar(32) NOT NULL,
  `sip` varchar(50) NOT NULL,
  `seen` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_seen` datetime DEFAULT NULL,
  `email` varchar(250) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Allowed user access';

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `session`, `sip`, `seen`, `created`, `last_seen`, `email`, `status`) VALUES
(1, 'shlomi', 'dd53b34a8b833392c764333acd0b61f7', '', '::1', 3, '2015-10-26 22:48:21', '2016-08-20 23:17:55', 'shlomohassid@gmail.com', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `db_error_log`
--
ALTER TABLE `db_error_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `when` (`when`);

--
-- Indexes for table `tokens_error_log`
--
ALTER TABLE `tokens_error_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `db_error_log`
--
ALTER TABLE `db_error_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tokens_error_log`
--
ALTER TABLE `tokens_error_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
