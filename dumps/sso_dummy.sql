-- Adminer 4.2.3 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `domains`;
CREATE TABLE `domains` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `domains` (`id`, `name`) VALUES
  (1,	'domain1.local'),
  (2,	'domain2.local'),
  (3,	'sub1.domain1.local');

DROP TABLE IF EXISTS `tokens`;
CREATE TABLE `tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `value` varchar(1024) COLLATE utf8_bin DEFAULT NULL,
  `used` tinyint(1) DEFAULT NULL,
  `expires` int(11) DEFAULT NULL,
  `domain_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` text COLLATE utf8_bin,
  `password` varchar(512) COLLATE utf8_bin DEFAULT NULL,
  `first_name` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `last_name` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `cookie` varchar(256) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `users` (`id`, `email`, `password`, `first_name`, `last_name`, `cookie`) VALUES
  (1,	'joe@example.com',	'$1$k84.dI3.$9/qnahwUbk3047whNEojD/',	'Joe',	'Satriani',	'eb33a4d17ef9950b72ee56bf39b24aeb:97905ef86554ad06b8d0f34c9498effe'),
  (2,	'bob@example.com',	'$1$Js2.eI3.$HFmO/0rNJp9Yts/HU99YQ1',	'Bob',	'Jackson',	'296292edf0553ca52e51f2fb284cf731:d0e7c070617b13fcdfe8340479d72437'),
  (3,	'testsso@wgz.cz',	'',	'',	'',	'e659afb8dff710c3ba7cfe665453cc21:baa52ff7190baa9f574ff802778e0ddf');

DROP TABLE IF EXISTS `user_login_facebook`;
CREATE TABLE `user_login_facebook` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `facebook_id` varchar(64) COLLATE utf8_bin NOT NULL,
  `created` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `user_login_facebook` (`id`, `user_id`, `facebook_id`, `created`) VALUES
  (1,	3,	'107712179619654',	1456008427);

DROP TABLE IF EXISTS `user_login_google`;
CREATE TABLE `user_login_google` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `google_id` varchar(64) COLLATE utf8_bin NOT NULL,
  `created` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `user_login_google` (`id`, `user_id`, `google_id`, `created`) VALUES
  (1,	3,	'106440411057598425368',	1456008298);

-- 2016-04-25 20:53:18