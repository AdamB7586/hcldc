CREATE TABLE IF NOT EXISTS `highway_code_section` (
  `sec_no` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(1000) NOT NULL,
  PRIMARY KEY (`sec_no`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `highway_code` (
  `hcno` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `hcrule` longtext,
  `hctitle` varchar(255) DEFAULT NULL,
  `imagetitle1` varchar(31) DEFAULT NULL,
  `imagetitle2` varchar(31) DEFAULT NULL,
  `imagetitle3` varchar(31) DEFAULT NULL,
  `imagefooter1` text,
  `pub` varchar(1) DEFAULT NULL,
  `pubsec` smallint(6) UNSIGNED DEFAULT NULL,
  `pubsubsec` smallint(6) UNSIGNED DEFAULT NULL,
  `largetext` longtext,
  `module` smallint(6) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`hcno`),
  UNIQUE KEY `hcno` (`hcno`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;