# phpMyAdmin SQL Dump
# version 2.5.7-pl1
# http://www.phpmyadmin.net
#
# Serveur: localhost
# Généré le : Mardi 22 Mars 2005 à 16:47
# Version du serveur: 3.23.58
# Version de PHP: 4.3.10
# 
# Base de données: `groupoffice210`
# 

# --------------------------------------------------------

#
# Structure de la table `frm_forums`
#

CREATE TABLE `frm_forums` (
  `id` int(11) NOT NULL default '0',
  `name` varchar(200) NOT NULL default '',
  `description` varchar(200) NOT NULL default '',
  `owner_id` int(11) NOT NULL default '0',
  `mailing_list` longtext NOT NULL,
  `acl_read` int(11) NOT NULL default '0',
  `acl_write` int(11) NOT NULL default '0',
  `ctime` int(11) NOT NULL default '0',
  `mtime` int(11) NOT NULL default '0',
  `ad_type` int(11) NOT NULL default '0',
  `custom1` varchar(100) NOT NULL default '',
  `custom2` varchar(100) NOT NULL default '',
  `custom3` varchar(100) NOT NULL default '',
  `custom1_values` longtext NOT NULL,
  `custom2_values` longtext NOT NULL,
  `custom3_values` longtext NOT NULL,
  `date_access` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Structure de la table `frm_messages`
#

CREATE TABLE `frm_messages` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `parent_id` int(11) NOT NULL default '0',
  `forum_id` int(11) NOT NULL default '0',
  `title` varchar(200) NOT NULL default '',
  `message` longtext NOT NULL,
  `ctime` int(11) NOT NULL default '0',
  `closed` enum('y','n') NOT NULL default 'n',
  `custom1` varchar(200) NOT NULL default '',
  `custom2` varchar(200) NOT NULL default '',
  `custom3` varchar(200) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Structure de la table `frm_msg_views`
#

CREATE TABLE `frm_msg_views` (
  `message_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `ctime` int(11) NOT NULL default '0'
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Structure de la table `frm_settings`
#

CREATE TABLE `frm_settings` (
  `user_id` int(11) NOT NULL default '0',
  `forum_id` int(11) NOT NULL default '0',
  `display` int(11) NOT NULL default '1',
  `sort` varchar(30) NOT NULL default '1',
  `theme` int(11) NOT NULL default '1',
  `order` varchar(30) NOT NULL default ''
) TYPE=MyISAM;
