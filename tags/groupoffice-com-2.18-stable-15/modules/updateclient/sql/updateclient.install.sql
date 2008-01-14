DROP TABLE IF EXISTS `uc_packages`;
CREATE TABLE `uc_packages` (
  `id` int(11) NOT NULL,
  `package_name` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `version` varchar(10) NOT NULL,
  `date` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;
