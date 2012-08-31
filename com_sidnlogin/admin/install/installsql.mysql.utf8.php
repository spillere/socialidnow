CREATE TABLE IF NOT EXISTS `#__sidn_login` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `screen_name` varchar(255) DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `social_network_id` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `sidn_id` varchar(30) NOT NULL,
  `picture_url` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;