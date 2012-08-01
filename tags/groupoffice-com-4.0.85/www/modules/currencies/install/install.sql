
--
-- Tabelstructuur voor tabel `cu_currencies`
--

DROP TABLE IF EXISTS `cu_currencies`;
CREATE TABLE IF NOT EXISTS `cu_currencies` (
  `code` char(3) NOT NULL,
  `symbol` varchar(10) NOT NULL,
  `value` double NOT NULL,
  PRIMARY KEY  (`code`),
  KEY `value` (`value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `cu_currencies`
--

INSERT INTO `cu_currencies` (`code`, `symbol`, `value`) VALUES
('EUR', '€', 1),
('USD', '$', 1.4);
