
--
-- Tabel structuur voor tabel `su_announcements`
--

DROP TABLE IF EXISTS `su_announcements`;
CREATE TABLE IF NOT EXISTS `su_announcements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL default '0',
  `due_time` int(11) NOT NULL default '0',
  `ctime` int(11) NOT NULL default '0',
  `mtime` int(11) NOT NULL default '0',
  `title` varchar(50) default NULL,
  `content` text,
  PRIMARY KEY  (`id`),
  KEY `due_time` (`due_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `su_notes`
--

DROP TABLE IF EXISTS `su_notes`;
CREATE TABLE IF NOT EXISTS `su_notes` (
  `user_id` int(11) NOT NULL,
  `text` text,
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `su_rss_feeds`
--

DROP TABLE IF EXISTS `su_rss_feeds`;
CREATE TABLE IF NOT EXISTS `su_rss_feeds` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `url` varchar(255) default NULL,
  `summary` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `su_visible_lists`
--

CREATE TABLE IF NOT EXISTS `su_visible_lists` (
  `user_id` int(11) NOT NULL,
  `tasklist_id` int(11) NOT NULL,
  PRIMARY KEY  (`user_id`,`tasklist_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `su_visible_calendars`
--

CREATE TABLE IF NOT EXISTS `su_visible_calendars` (
  `user_id` int(11) NOT NULL,
  `calendar_id` int(11) NOT NULL,
  PRIMARY KEY  (`user_id`,`calendar_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
