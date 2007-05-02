# phpMyAdmin SQL Dump
# version 2.5.5-pl1
# http://www.phpmyadmin.net
#
# Host: localhost
# Generatie Tijd: 05 Apr 2004 om 11:17
# Server versie: 3.23.58
# PHP Versie: 4.3.4
# 
# Database : `groupoffice`
# 

# --------------------------------------------------------

#
# Tabel structuur voor tabel `wiki_interwiki`
#

DROP TABLE IF EXISTS `wiki_interwiki`;
CREATE TABLE `wiki_interwiki` (
  `prefix` varchar(80) NOT NULL default '',
  `where_defined` varchar(80) NOT NULL default '',
  `url` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`prefix`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Tabel structuur voor tabel `wiki_links`
#

DROP TABLE IF EXISTS `wiki_links`;
CREATE TABLE `wiki_links` (
  `page` varchar(80) NOT NULL default '',
  `link` varchar(80) NOT NULL default '',
  `count` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`page`,`link`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Tabel structuur voor tabel `wiki_pages`
#

DROP TABLE IF EXISTS `wiki_pages`;
CREATE TABLE `wiki_pages` (
  `title` varchar(80) NOT NULL default '',
  `version` int(10) unsigned NOT NULL default '1',
  `time` timestamp(14) NOT NULL,
  `supercede` timestamp(14) NOT NULL,
  `mutable` set('off','on') NOT NULL default 'on',
  `username` varchar(80) default NULL,
  `author` varchar(80) NOT NULL default '',
  `comment` varchar(80) NOT NULL default '',
  `body` text,
  PRIMARY KEY  (`title`,`version`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Tabel structuur voor tabel `wiki_rate`
#

DROP TABLE IF EXISTS `wiki_rate`;
CREATE TABLE `wiki_rate` (
  `ip` char(20) NOT NULL default '',
  `time` timestamp(14) NOT NULL,
  `viewLimit` smallint(5) unsigned default NULL,
  `searchLimit` smallint(5) unsigned default NULL,
  `editLimit` smallint(5) unsigned default NULL,
  PRIMARY KEY  (`ip`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Tabel structuur voor tabel `wiki_remote_pages`
#

DROP TABLE IF EXISTS `wiki_remote_pages`;
CREATE TABLE `wiki_remote_pages` (
  `page` varchar(80) NOT NULL default '',
  `site` varchar(80) NOT NULL default '',
  PRIMARY KEY  (`page`,`site`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Tabel structuur voor tabel `wiki_sisterwiki`
#

DROP TABLE IF EXISTS `wiki_sisterwiki`;
CREATE TABLE `wiki_sisterwiki` (
  `prefix` varchar(80) NOT NULL default '',
  `where_defined` varchar(80) NOT NULL default '',
  `url` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`prefix`)
) TYPE=MyISAM;
