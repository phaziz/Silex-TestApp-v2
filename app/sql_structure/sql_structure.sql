CREATE TABLE `doctrinetest` (
  `id` int(250) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) DEFAULT NULL,
  `val` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM;