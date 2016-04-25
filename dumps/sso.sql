-- Adminer 4.2.3 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `domains`;
CREATE TABLE `domains` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `domains` (`id`, `name`) VALUES
  (1,	'domain1.local'),
  (2,	'domain2.local');

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
  (1,	'joe@example.com',	'$1$k84.dI3.$9/qnahwUbk3047whNEojD/',	'Joe',	'Satriani',	'47329d3b94f0dc13e09c6c1a4caeddc8:7e7a9bb979baf355267cef9b0f46076a'),
  (2,	'bob@example.com',	'$1$Js2.eI3.$HFmO/0rNJp9Yts/HU99YQ1',	'Bob',	'Jackson',	'c8845bd95bfae01c01eaa15e26b48bc9:8adb439a3de000adc7b80b0ebd2d8c26');

DROP TABLE IF EXISTS `user_login_facebook`;
CREATE TABLE `user_login_facebook` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `facebook_id` decimal(30,0) DEFAULT NULL,
  `created` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


DROP TABLE IF EXISTS `user_login_google`;
CREATE TABLE `user_login_google` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `google_id` decimal(30,0) DEFAULT NULL,
  `created` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


-- 2016-04-25 20:54:50