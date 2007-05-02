# phpMyAdmin SQL Dump
# version 2.5.3-rc3
# http://www.phpmyadmin.net
#
# Host: localhost
# Generation Time: Jul 26, 2004 at 12:18 AM
# Server version: 3.23.58
# PHP Version: 4.3.6
# 
# Database : `groupoffice`
# 

# --------------------------------------------------------

#
# Table structure for table `tts_activities`
#

DROP TABLE IF EXISTS `tts_activities`;
CREATE TABLE `tts_activities` (
  `activity_id` tinyint(3) NOT NULL default '0',
  `activity_name` varchar(50) NOT NULL default ''
) TYPE=MyISAM;

#
# Dumping data for table `tts_activities`
#

INSERT INTO `tts_activities` VALUES (0, 'Inactive');
INSERT INTO `tts_activities` VALUES (1, 'Active');

# --------------------------------------------------------

#
# Table structure for table `tts_categories`
#

DROP TABLE IF EXISTS `tts_categories`;
CREATE TABLE `tts_categories` (
  `category_id` int(11) NOT NULL default '0',
  `category_name` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`category_id`)
) TYPE=MyISAM;

#
# Dumping data for table `tts_categories`
#

INSERT INTO `tts_categories` VALUES (1, 'Hardware');
INSERT INTO `tts_categories` VALUES (2, 'Software');
INSERT INTO `tts_categories` VALUES (3, 'Network');

# --------------------------------------------------------

#
# Table structure for table `tts_colors_tables`
#

DROP TABLE IF EXISTS `tts_colors_tables`;
CREATE TABLE `tts_colors_tables` (
  `clr_tbl_id` int(11) NOT NULL default '0',
  `fnt_clr` varchar(20) NOT NULL default '',
  `bck_clr` varchar(20) NOT NULL default ''
) TYPE=MyISAM;

#
# Dumping data for table `tts_colors_tables`
#

INSERT INTO `tts_colors_tables` VALUES (0, 'black', 'white');
INSERT INTO `tts_colors_tables` VALUES (1, 'white', 'black');
INSERT INTO `tts_colors_tables` VALUES (2, 'red', 'yellow');
INSERT INTO `tts_colors_tables` VALUES (4, 'blue', 'white');
INSERT INTO `tts_colors_tables` VALUES (5, 'green', 'white');

# --------------------------------------------------------

#
# Table structure for table `tts_config`
#

DROP TABLE IF EXISTS `tts_config`;
CREATE TABLE `tts_config` (
  `varname` varchar(128) NOT NULL default '',
  `definition` text,
  UNIQUE KEY `varname` (`varname`)
) TYPE=MyISAM;

#
# Dumping data for table `tts_config`
#

INSERT INTO `tts_config` VALUES ('tts_title', 'GROUPOFFICE HELP DESK');
INSERT INTO `tts_config` VALUES ('powered_by_riunx', '@ powered by <a href="www.riunx.com">RIUNX</a>');
INSERT INTO `tts_config` VALUES ('welcome_message', '<h2>Group Office HELPDESK</h2>');
INSERT INTO `tts_config` VALUES ('hlpdsk_theme', 'Default');
INSERT INTO `tts_config` VALUES ('timezone', '+2');

# --------------------------------------------------------

#
# Table structure for table `tts_groups`
#

DROP TABLE IF EXISTS `tts_groups`;
CREATE TABLE `tts_groups` (
  `gid` int(11) NOT NULL default '0',
  `group_name` varchar(127) NOT NULL default '',
  `description` varchar(255) default NULL,
  `permissions` varchar(255) default NULL,
  UNIQUE KEY `gid` (`gid`,`group_name`)
) TYPE=MyISAM;

#
# Dumping data for table `tts_groups`
#
INSERT INTO `tts_groups` VALUES (0, 'anonymous', NULL, NULL);
INSERT INTO `tts_groups` VALUES (1, 'clients', NULL, NULL);
INSERT INTO `tts_groups` VALUES (2, 'agents', NULL, NULL);
INSERT INTO `tts_groups` VALUES (3, 'manager', NULL, NULL);
INSERT INTO `tts_groups` VALUES (4, 'auditor', NULL, NULL);
INSERT INTO `tts_groups` VALUES (5, 'administrators', NULL, NULL);

# --------------------------------------------------------

#
# Table structure for table `tts_groups_members`
#

DROP TABLE IF EXISTS `tts_groups_members`;
CREATE TABLE `tts_groups_members` (
  `uid` int(11) NOT NULL default '0',
  `gid` int(11) NOT NULL default '0',
  `uid_default` smallint(3) NOT NULL default '0',
  PRIMARY KEY  (`uid`,`gid`)
) TYPE=MyISAM;

#
# Dumping data for table `tts_groups_members`
#

INSERT INTO `tts_groups_members` VALUES (1, 3, 0);
INSERT INTO `tts_groups_members` VALUES (1, 2, 0);
INSERT INTO `tts_groups_members` VALUES (1, 5, 0);

# --------------------------------------------------------

#
# Table structure for table `tts_menu`
#

DROP TABLE IF EXISTS `tts_menu`;
CREATE TABLE `tts_menu` (
  `menu_id` int(11) NOT NULL auto_increment,
  `title` varchar(30) NOT NULL default '',
  `file` varchar(30) NOT NULL default '',
  `image` varchar(30) NOT NULL default '',
  `link` varchar(30) NOT NULL default '',
  UNIQUE KEY `menu_id` (`menu_id`)
) TYPE=MyISAM AUTO_INCREMENT=7 ;

#
# Dumping data for table `tts_menu`
#

INSERT INTO `tts_menu` VALUES (1, 'helpdesk_title_my_tickets', 'my_tickets.php','cal_list.png', 'my_tickets.php');
INSERT INTO `tts_menu` VALUES (2, 'helpdesk_title_queries', 'queries.php' ,'hlp_ab_search.png','queries.php');
INSERT INTO `tts_menu` VALUES (3, 'helpdesk_title_entry', 'entry.php','hlp_new_folder.png', 'entry.php');
INSERT INTO `tts_menu` VALUES (4, 'helpdesk_title_admin', 'admin.php','hlp_preferences.png', 'admin.php');
#INSERT INTO `tts_menu` VALUES (5, 'helpdesk_title_start', 'index.php','hlp_fs_refresh.png', 'index.php');
# INSERT INTO `tts_menu` VALUES (6, 'helpdesk_title_stat', 'statistics.php','hlp_properties.png', 'statistics.php');

# --------------------------------------------------------

#
# Table structure for table `tts_permissions`
#

DROP TABLE IF EXISTS `tts_permissions`;
CREATE TABLE `tts_permissions` (
  `gid` bigint(11) NOT NULL default '0',
  `action_id` int(11) NOT NULL default '0',
  `description` varchar(255) NOT NULL default ''
) TYPE=MyISAM;

#
# Dumping data for table `tts_permissions`
#

INSERT INTO `tts_permissions` VALUES (1, 36, '');
INSERT INTO `tts_permissions` VALUES (2, 35, '');
INSERT INTO `tts_permissions` VALUES (2, 34, '');
INSERT INTO `tts_permissions` VALUES (1, 28, '');
INSERT INTO `tts_permissions` VALUES (2, 33, '');
INSERT INTO `tts_permissions` VALUES (2, 32, '');
INSERT INTO `tts_permissions` VALUES (2, 28, '');
INSERT INTO `tts_permissions` VALUES (1, 35, '');
INSERT INTO `tts_permissions` VALUES (1, 34, '');
INSERT INTO `tts_permissions` VALUES (2, 23, '');
INSERT INTO `tts_permissions` VALUES (2, 21, '');
INSERT INTO `tts_permissions` VALUES (2, 20, '');
INSERT INTO `tts_permissions` VALUES (3, 10, '');
INSERT INTO `tts_permissions` VALUES (5, 2, '');
INSERT INTO `tts_permissions` VALUES (2, 19, '');
INSERT INTO `tts_permissions` VALUES (2, 12, '');
INSERT INTO `tts_permissions` VALUES (2, 11, '');
INSERT INTO `tts_permissions` VALUES (2, 6, '');
INSERT INTO `tts_permissions` VALUES (3, 9, '');
INSERT INTO `tts_permissions` VALUES (0, 1, '');
INSERT INTO `tts_permissions` VALUES (1, 33, '');
INSERT INTO `tts_permissions` VALUES (1, 32, '');
INSERT INTO `tts_permissions` VALUES (1, 31, '');
INSERT INTO `tts_permissions` VALUES (1, 29, '');
INSERT INTO `tts_permissions` VALUES (1, 27, '');
INSERT INTO `tts_permissions` VALUES (1, 26, '');
INSERT INTO `tts_permissions` VALUES (1, 23, '');
INSERT INTO `tts_permissions` VALUES (1, 21, '');
INSERT INTO `tts_permissions` VALUES (1, 20, '');
INSERT INTO `tts_permissions` VALUES (1, 19, '');
INSERT INTO `tts_permissions` VALUES (1, 18, '');
INSERT INTO `tts_permissions` VALUES (1, 16, '');
INSERT INTO `tts_permissions` VALUES (1, 14, '');
INSERT INTO `tts_permissions` VALUES (1, 13, '');
INSERT INTO `tts_permissions` VALUES (1, 12, '');
INSERT INTO `tts_permissions` VALUES (1, 11, '');
INSERT INTO `tts_permissions` VALUES (1, 8, '');
INSERT INTO `tts_permissions` VALUES (1, 6, '');
INSERT INTO `tts_permissions` VALUES (1, 4, '');
INSERT INTO `tts_permissions` VALUES (1, 3, '');
INSERT INTO `tts_permissions` VALUES (1, 1, '');

# --------------------------------------------------------

#
# Table structure for table `tts_permissions_users`
#

DROP TABLE IF EXISTS `tts_permissions_users`;
CREATE TABLE `tts_permissions_users` (
  `uid` int(11) NOT NULL default '0',
  `action_id` int(11) NOT NULL default '0'
) TYPE=MyISAM;

#
# Dumping data for table `tts_permissions_users`
#


# --------------------------------------------------------

#
# Table structure for table `tts_priorities`
#

DROP TABLE IF EXISTS `tts_priorities`;
CREATE TABLE `tts_priorities` (
  `priority_id` tinyint(4) NOT NULL default '0',
  `priority_name` varchar(100) NOT NULL default '',
  `select_id` tinyint(4) NOT NULL default '0'
) TYPE=MyISAM;

#
# Dumping data for table `tts_priorities`
#

INSERT INTO `tts_priorities` VALUES (1, 'std', 0);
INSERT INTO `tts_priorities` VALUES (2, 'low', 0);
INSERT INTO `tts_priorities` VALUES (3, 'high', 0);
INSERT INTO `tts_priorities` VALUES (4, 'urgent', 0);

# --------------------------------------------------------

#
# Table structure for table `tts_projects`
#

DROP TABLE IF EXISTS `tts_projects`;
CREATE TABLE `tts_projects` (
  `project_id` int(11) unsigned NOT NULL auto_increment,
  `project_name` varchar(100) NOT NULL default '',
  `project_description` varchar(255) default NULL,
  `privacy` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`project_id`)
) TYPE=MyISAM AUTO_INCREMENT=5 ;

#
# Dumping data for table `tts_projects`
#

INSERT INTO `tts_projects` VALUES (1, 'Helpdesk', NULL, 0);
INSERT INTO `tts_projects` VALUES (2, 'Security', NULL, 0);
INSERT INTO `tts_projects` VALUES (3, 'Networking', NULL, 0);
INSERT INTO `tts_projects` VALUES (4, 'General', NULL, 0);

# --------------------------------------------------------

#
# Table structure for table `tts_stages`
#

DROP TABLE IF EXISTS `tts_stages`;
CREATE TABLE `tts_stages` (
  `stage_id` tinyint(3) NOT NULL default '1',
  `stage_name` varchar(100) NOT NULL default 'undefined',
  PRIMARY KEY  (`stage_id`)
) TYPE=MyISAM;

#
# Dumping data for table `tts_stages`
#

INSERT INTO `tts_stages` VALUES (1, 'No');
INSERT INTO `tts_stages` VALUES (2, 'Yes');

# --------------------------------------------------------

#
# Table structure for table `tts_status`
#

DROP TABLE IF EXISTS `tts_status`;
CREATE TABLE `tts_status` (
  `status_id` int(11) NOT NULL auto_increment,
  `status_name` varchar(100) NOT NULL default '',
  `show_by_default` tinyint(3) unsigned default '1',
  PRIMARY KEY  (`status_id`)
) TYPE=MyISAM AUTO_INCREMENT=4 ;

#
# Dumping data for table `tts_status`
#

INSERT INTO `tts_status` VALUES (1, 'queue', 1);
INSERT INTO `tts_status` VALUES (2, 'open', 1);
INSERT INTO `tts_status` VALUES (3, 'in progress', 1);
INSERT INTO `tts_status` VALUES (4, 'done', 1);
INSERT INTO `tts_status` VALUES (5, 'cancelled', 1);

# --------------------------------------------------------

#
# Table structure for table `tts_tasks`
#

DROP TABLE IF EXISTS `tts_tasks`;
CREATE TABLE `tts_tasks` (
  `task_id` int(11) NOT NULL auto_increment,
  `ticket_id` int(11) NOT NULL default '0',
  `sender_id` int(11) NOT NULL default '0',
  `comment` text,
  `post_date` varchar(25) NOT NULL default '',
  `email_issuer` int(11) NOT NULL default '0',
  `email_agent` int(11) NOT NULL default '0',
  KEY `sender_id` (`sender_id`),
  KEY `task_id` (`task_id`,`ticket_id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


# --------------------------------------------------------

#
# Table structure for table `tts_tickets`
#

DROP TABLE IF EXISTS `tts_tickets`;
CREATE TABLE `tts_tickets` (
  `ticket_number` int(11) NOT NULL auto_increment,
  `t_assigned` varchar(255) NOT NULL default '',
  `t_from` varchar(255) NOT NULL default '',
  `t_stage` int(3) NOT NULL default '0',
  `t_priority` tinyint(3) NOT NULL default '1',
  `t_category` tinyint(3) NOT NULL default '1',
  `t_subject` varchar(255) NOT NULL default '',
  `t_description` text NOT NULL,
  `t_comments` varchar(255) NOT NULL default '',
  `post_date` varchar(12) NOT NULL default '',
  `due_date` varchar(12) NOT NULL default '',
  `change_date` varchar(12) NOT NULL default '',
  `t_status` varchar(255) NOT NULL default '',
  `t_sms` varchar(255) NOT NULL default '',
  `t_email` varchar(255) NOT NULL default '',
  `transac_id` varchar(128) default NULL,
  `activity_id` tinyint(3) NOT NULL default '0',
  `project_id` int(11) default '1',
  `end_date` varchar(12) NOT NULL default '',
  `complete` int(3) NOT NULL default '0',
  `acl_read` int(11) NOT NULL default '0',
  `acl_write` int(11) NOT NULL default '0',
  PRIMARY KEY  (`ticket_number`),
  KEY `t_category` (`t_category`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;


