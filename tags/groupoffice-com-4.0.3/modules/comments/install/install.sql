--
-- Tabelstructuur voor tabel `co_comments`
--

DROP TABLE IF EXISTS `co_comments`;
CREATE TABLE IF NOT EXISTS `co_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model_id` int(11) NOT NULL,
  `model_type_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ctime` int(11) NOT NULL,
  `mtime` int(11) NOT NULL,
  `comments` text,
  PRIMARY KEY (`id`),
  KEY `link_id` (`model_id`,`model_type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------