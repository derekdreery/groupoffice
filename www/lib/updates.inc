<?php
/*
Update queries for each version from 1.95

From version 2.06 and up put module updates in the module's sql directory!

*/
$updates[219] = array();

$updates[218][]="
CREATE TABLE `se_cache` (
  `link_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `table` varchar(50) NOT NULL,
  `id` int(11) NOT NULL,
  `module` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) NOT NULL,
  `url` varchar(100) NOT NULL,
  `link_type` int(11) NOT NULL,
  `type` varchar(20) NOT NULL,
  `keywords` text NOT NULL,
  PRIMARY KEY  (`link_id`,`user_id`),
  KEY `name` (`name`)
) TYPE=MyISAM;";

$updates[218][]="
CREATE TABLE `se_last_sync_times` (
  `user_id` int(11) NOT NULL,
  `module` varchar(50) NOT NULL,
  `last_sync_time` int(11) NOT NULL,
  PRIMARY KEY  (`user_id`,`module`)
) TYPE=MyISAM;
";

$updates[217][]="UPDATE ab_contacts SET email2 = '' WHERE email = email2;";
$updates[217][]="UPDATE ab_contacts SET email3 = '' WHERE email = email3;";

$updates[216][]="ALTER TABLE `sync_settings` ADD `max_days_old` TINYINT NOT NULL ,
ADD `delete_old_events` ENUM( '0', '1' ) NOT NULL  ;";

$updates[216][]= "ALTER TABLE `sync_events_maps` ADD `todo` ENUM( '0', '1' ) NOT NULL ";

$updates[215][]="ALTER TABLE `users` ADD `link_id` INT NOT NULL ;";

$updates[215][]="ALTER TABLE `users` ADD INDEX ( `link_id` ) ;";

$updates[215][]="INSERT INTO `settings` VALUES (0, 'registration_unconfirmed_subject', 'Your account still needs activation');";
$updates[215][]="INSERT INTO `settings` VALUES (0, 'registration_confirmation', '<html>\r\n    <head>\r\n    </head>\r\n    <body>\r\n        <font size=\"3\" face=\"Verdana\">Dear&nbsp;%beginning%&nbsp;%middle_name%%last_name%,<br />\r\n        <br />\r\n        Welcome to Group-Office! You can login at:<br />\r\n        <br />\r\n        %full_url%<br />\r\n        <br />\r\n        With:<br />\r\n        <br />\r\n        Username: %username%<br />\r\n        Password: %password%<br />\r\n        <br />\r\n        With kind regards,<br />\r\n        <br />\r\n        The Group-Office administrator</font>\r\n    </body>\r\n</html>');";
$updates[215][]="INSERT INTO `settings` VALUES (0, 'registration_unconfirmed', '<html>\r\n    <head>\r\n    </head>\r\n    <body>\r\n        <font size=\"3\" face=\"Verdana\">Dear&nbsp;%beginning% %middle_name%%last_name%,<br />\r\n        <br />\r\n        Thank you for your registration at Group-Office. You can login when an administrator activates your account. You will recieve an e-mail with login instructions at that time.<br />\r\n        <br />\r\n        With kind regards,<br />\r\n        <br />\r\n        The Group-Office administrator</font>\r\n    </body>\r\n</html>');";
$updates[215][]="INSERT INTO `settings` VALUES (0, 'registration_confirmation_subject', 'Welcome to Group-Office!');";


$updates[215][]="ALTER TABLE `users` ADD `address_no` VARCHAR( 10 ) NOT NULL AFTER `address` ;";
$updates[215][]="ALTER TABLE `users` ADD `work_address_no` VARCHAR( 10 ) NOT NULL AFTER `work_address` ;";


$updates[215][] = "ALTER TABLE `db_sequence` CHANGE `seq_name` `seq_name` VARCHAR( 50 ) NOT NULL";

$updates[214][]="CREATE TABLE `sync_todos_syncs` (
  `device_id` int(11) NOT NULL default '0',
  `local_last_anchor` int(11) NOT NULL default '0',
  `remote_last_anchor` char(32) NOT NULL default '',
  PRIMARY KEY  (`device_id`)
) TYPE=MyISAM;";

$updates[214][]="UPDATE users SET max_rows_list=100 WHERE max_rows_list=0;";
$updates[214][] = "CREATE TABLE `links` (
  `type1` tinyint(4) NOT NULL default '0',
  `link_id1` int(11) NOT NULL default '0',
  `type2` tinyint(4) NOT NULL default '0',
  `link_id2` int(11) NOT NULL default '0',
  KEY `type1` (`type1`),
  KEY `type2` (`type2`),
  KEY `link_id1` (`link_id1`),
  KEY `link_id2` (`link_id2`)
) TYPE=MyISAM;";


$updates[214][] = "ALTER TABLE `users` ADD `country_id` INT NOT NULL;";
$updates[214][] = "ALTER TABLE `users` ADD `work_country_id` INT NOT NULL;";



$updates['214'][] = "CREATE TABLE `countries` (
  `id` int(11) NOT NULL default '0',
  `name` varchar(64) NOT NULL default '',
  `iso_code_2` char(2) NOT NULL default '',
  `iso_code_3` char(3) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;";



$updates['214'][] = "INSERT INTO `countries` VALUES (1, 'Afghanistan', 'AF', 'AFG');";
$updates['214'][] = "INSERT INTO `countries` VALUES (2, 'Albania', 'AL', 'ALB');";
$updates['214'][] = "INSERT INTO `countries` VALUES (3, 'Algeria', 'DZ', 'DZA');";
$updates['214'][] = "INSERT INTO `countries` VALUES (4, 'American Samoa', 'AS', 'ASM');";
$updates['214'][] = "INSERT INTO `countries` VALUES (5, 'Andorra', 'AD', 'AND');";
$updates['214'][] = "INSERT INTO `countries` VALUES (6, 'Angola', 'AO', 'AGO');";
$updates['214'][] = "INSERT INTO `countries` VALUES (7, 'Anguilla', 'AI', 'AIA');";
$updates['214'][] = "INSERT INTO `countries` VALUES (8, 'Antarctica', 'AQ', 'ATA');";
$updates['214'][] = "INSERT INTO `countries` VALUES (9, 'Antigua and Barbuda', 'AG', 'ATG');";
$updates['214'][] = "INSERT INTO `countries` VALUES (10, 'Argentina', 'AR', 'ARG');";
$updates['214'][] = "INSERT INTO `countries` VALUES (11, 'Armenia', 'AM', 'ARM');";
$updates['214'][] = "INSERT INTO `countries` VALUES (12, 'Aruba', 'AW', 'ABW');";
$updates['214'][] = "INSERT INTO `countries` VALUES (13, 'Australia', 'AU', 'AUS');";
$updates['214'][] = "INSERT INTO `countries` VALUES (14, 'Austria', 'AT', 'AUT');";
$updates['214'][] = "INSERT INTO `countries` VALUES (15, 'Azerbaijan', 'AZ', 'AZE');";
$updates['214'][] = "INSERT INTO `countries` VALUES (16, 'Bahamas', 'BS', 'BHS');";
$updates['214'][] = "INSERT INTO `countries` VALUES (17, 'Bahrain', 'BH', 'BHR');";
$updates['214'][] = "INSERT INTO `countries` VALUES (18, 'Bangladesh', 'BD', 'BGD');";
$updates['214'][] = "INSERT INTO `countries` VALUES (19, 'Barbados', 'BB', 'BRB');";
$updates['214'][] = "INSERT INTO `countries` VALUES (20, 'Belarus', 'BY', 'BLR');";
$updates['214'][] = "INSERT INTO `countries` VALUES (21, 'Belgium', 'BE', 'BEL');";
$updates['214'][] = "INSERT INTO `countries` VALUES (22, 'Belize', 'BZ', 'BLZ');";
$updates['214'][] = "INSERT INTO `countries` VALUES (23, 'Benin', 'BJ', 'BEN');";
$updates['214'][] = "INSERT INTO `countries` VALUES (24, 'Bermuda', 'BM', 'BMU');";
$updates['214'][] = "INSERT INTO `countries` VALUES (25, 'Bhutan', 'BT', 'BTN');";
$updates['214'][] = "INSERT INTO `countries` VALUES (26, 'Bolivia', 'BO', 'BOL');";
$updates['214'][] = "INSERT INTO `countries` VALUES (27, 'Bosnia and Herzegowina', 'BA', 'BIH');";
$updates['214'][] = "INSERT INTO `countries` VALUES (28, 'Botswana', 'BW', 'BWA');";
$updates['214'][] = "INSERT INTO `countries` VALUES (29, 'Bouvet Island', 'BV', 'BVT');";
$updates['214'][] = "INSERT INTO `countries` VALUES (30, 'Brazil', 'BR', 'BRA');";
$updates['214'][] = "INSERT INTO `countries` VALUES (31, 'British Indian Ocean Territory', 'IO', 'IOT');";
$updates['214'][] = "INSERT INTO `countries` VALUES (32, 'Brunei Darussalam', 'BN', 'BRN');";
$updates['214'][] = "INSERT INTO `countries` VALUES (33, 'Bulgaria', 'BG', 'BGR');";
$updates['214'][] = "INSERT INTO `countries` VALUES (34, 'Burkina Faso', 'BF', 'BFA');";
$updates['214'][] = "INSERT INTO `countries` VALUES (35, 'Burundi', 'BI', 'BDI');";
$updates['214'][] = "INSERT INTO `countries` VALUES (36, 'Cambodia', 'KH', 'KHM');";
$updates['214'][] = "INSERT INTO `countries` VALUES (37, 'Cameroon', 'CM', 'CMR');";
$updates['214'][] = "INSERT INTO `countries` VALUES (38, 'Canada', 'CA', 'CAN');";
$updates['214'][] = "INSERT INTO `countries` VALUES (39, 'Cape Verde', 'CV', 'CPV');";
$updates['214'][] = "INSERT INTO `countries` VALUES (40, 'Cayman Islands', 'KY', 'CYM');";
$updates['214'][] = "INSERT INTO `countries` VALUES (41, 'Central African Republic', 'CF', 'CAF');";
$updates['214'][] = "INSERT INTO `countries` VALUES (42, 'Chad', 'TD', 'TCD');";
$updates['214'][] = "INSERT INTO `countries` VALUES (43, 'Chile', 'CL', 'CHL');";
$updates['214'][] = "INSERT INTO `countries` VALUES (45, 'Christmas Island', 'CX', 'CXR');";
$updates['214'][] = "INSERT INTO `countries` VALUES (46, 'Cocos (Keeling) Islands', 'CC', 'CCK');";
$updates['214'][] = "INSERT INTO `countries` VALUES (47, 'Colombia', 'CO', 'COL');";
$updates['214'][] = "INSERT INTO `countries` VALUES (48, 'Comoros', 'KM', 'COM');";
$updates['214'][] = "INSERT INTO `countries` VALUES (49, 'Congo', 'CG', 'COG');";
$updates['214'][] = "INSERT INTO `countries` VALUES (50, 'Cook Islands', 'CK', 'COK');";
$updates['214'][] = "INSERT INTO `countries` VALUES (51, 'Costa Rica', 'CR', 'CRI');";
$updates['214'][] = "INSERT INTO `countries` VALUES (52, 'Cote D''Ivoire', 'CI', 'CIV');";
$updates['214'][] = "INSERT INTO `countries` VALUES (53, 'Croatia', 'HR', 'HRV');";
$updates['214'][] = "INSERT INTO `countries` VALUES (54, 'Cuba', 'CU', 'CUB');";
$updates['214'][] = "INSERT INTO `countries` VALUES (55, 'Cyprus', 'CY', 'CYP');";
$updates['214'][] = "INSERT INTO `countries` VALUES (56, 'Czech Republic', 'CZ', 'CZE');";
$updates['214'][] = "INSERT INTO `countries` VALUES (57, 'Denmark', 'DK', 'DNK');";
$updates['214'][] = "INSERT INTO `countries` VALUES (58, 'Djibouti', 'DJ', 'DJI');";
$updates['214'][] = "INSERT INTO `countries` VALUES (59, 'Dominica', 'DM', 'DMA');";
$updates['214'][] = "INSERT INTO `countries` VALUES (60, 'Dominican Republic', 'DO', 'DOM');";
$updates['214'][] = "INSERT INTO `countries` VALUES (61, 'East Timor', 'TP', 'TMP');";
$updates['214'][] = "INSERT INTO `countries` VALUES (62, 'Ecuador', 'EC', 'ECU');";
$updates['214'][] = "INSERT INTO `countries` VALUES (63, 'Egypt', 'EG', 'EGY');";
$updates['214'][] = "INSERT INTO `countries` VALUES (64, 'El Salvador', 'SV', 'SLV');";
$updates['214'][] = "INSERT INTO `countries` VALUES (65, 'Equatorial Guinea', 'GQ', 'GNQ');";
$updates['214'][] = "INSERT INTO `countries` VALUES (66, 'Eritrea', 'ER', 'ERI');";
$updates['214'][] = "INSERT INTO `countries` VALUES (67, 'Estonia', 'EE', 'EST');";
$updates['214'][] = "INSERT INTO `countries` VALUES (68, 'Ethiopia', 'ET', 'ETH');";
$updates['214'][] = "INSERT INTO `countries` VALUES (69, 'Falkland Islands (Malvinas)', 'FK', 'FLK');";
$updates['214'][] = "INSERT INTO `countries` VALUES (70, 'Faroe Islands', 'FO', 'FRO');";
$updates['214'][] = "INSERT INTO `countries` VALUES (71, 'Fiji', 'FJ', 'FJI');";
$updates['214'][] = "INSERT INTO `countries` VALUES (72, 'Finland', 'FI', 'FIN');";
$updates['214'][] = "INSERT INTO `countries` VALUES (73, 'France', 'FR', 'FRA');";
$updates['214'][] = "INSERT INTO `countries` VALUES (74, 'France, Metropolitan', 'FX', 'FXX');";
$updates['214'][] = "INSERT INTO `countries` VALUES (75, 'French Guiana', 'GF', 'GUF');";
$updates['214'][] = "INSERT INTO `countries` VALUES (76, 'French Polynesia', 'PF', 'PYF');";
$updates['214'][] = "INSERT INTO `countries` VALUES (77, 'French Southern Territories', 'TF', 'ATF');";
$updates['214'][] = "INSERT INTO `countries` VALUES (78, 'Gabon', 'GA', 'GAB');";
$updates['214'][] = "INSERT INTO `countries` VALUES (79, 'Gambia', 'GM', 'GMB');";
$updates['214'][] = "INSERT INTO `countries` VALUES (80, 'Georgia', 'GE', 'GEO');";
$updates['214'][] = "INSERT INTO `countries` VALUES (81, 'Germany', 'DE', 'DEU');";
$updates['214'][] = "INSERT INTO `countries` VALUES (82, 'Ghana', 'GH', 'GHA');";
$updates['214'][] = "INSERT INTO `countries` VALUES (83, 'Gibraltar', 'GI', 'GIB');";
$updates['214'][] = "INSERT INTO `countries` VALUES (84, 'Greece', 'GR', 'GRC');";
$updates['214'][] = "INSERT INTO `countries` VALUES (85, 'Greenland', 'GL', 'GRL');";
$updates['214'][] = "INSERT INTO `countries` VALUES (86, 'Grenada', 'GD', 'GRD');";
$updates['214'][] = "INSERT INTO `countries` VALUES (87, 'Guadeloupe', 'GP', 'GLP');";
$updates['214'][] = "INSERT INTO `countries` VALUES (88, 'Guam', 'GU', 'GUM');";
$updates['214'][] = "INSERT INTO `countries` VALUES (89, 'Guatemala', 'GT', 'GTM');";
$updates['214'][] = "INSERT INTO `countries` VALUES (90, 'Guinea', 'GN', 'GIN');";
$updates['214'][] = "INSERT INTO `countries` VALUES (91, 'Guinea-bissau', 'GW', 'GNB');";
$updates['214'][] = "INSERT INTO `countries` VALUES (92, 'Guyana', 'GY', 'GUY');";
$updates['214'][] = "INSERT INTO `countries` VALUES (93, 'Haiti', 'HT', 'HTI');";
$updates['214'][] = "INSERT INTO `countries` VALUES (94, 'Heard and Mc Donald Islands', 'HM', 'HMD');";
$updates['214'][] = "INSERT INTO `countries` VALUES (95, 'Honduras', 'HN', 'HND');";
$updates['214'][] = "INSERT INTO `countries` VALUES (96, 'Hong Kong', 'HK', 'HKG');";
$updates['214'][] = "INSERT INTO `countries` VALUES (97, 'Hungary', 'HU', 'HUN');";
$updates['214'][] = "INSERT INTO `countries` VALUES (98, 'Iceland', 'IS', 'ISL');";
$updates['214'][] = "INSERT INTO `countries` VALUES (99, 'India', 'IN', 'IND');";
$updates['214'][] = "INSERT INTO `countries` VALUES (100, 'Indonesia', 'ID', 'IDN');";
$updates['214'][] = "INSERT INTO `countries` VALUES (103, 'Ireland', 'IE', 'IRL');";
$updates['214'][] = "INSERT INTO `countries` VALUES (104, 'Israel', 'IL', 'ISR');";
$updates['214'][] = "INSERT INTO `countries` VALUES (105, 'Italy', 'IT', 'ITA');";
$updates['214'][] = "INSERT INTO `countries` VALUES (106, 'Jamaica', 'JM', 'JAM');";
$updates['214'][] = "INSERT INTO `countries` VALUES (107, 'Japan', 'JP', 'JPN');";
$updates['214'][] = "INSERT INTO `countries` VALUES (108, 'Jordan', 'JO', 'JOR');";
$updates['214'][] = "INSERT INTO `countries` VALUES (109, 'Kazakhstan', 'KZ', 'KAZ');";
$updates['214'][] = "INSERT INTO `countries` VALUES (110, 'Kenya', 'KE', 'KEN');";
$updates['214'][] = "INSERT INTO `countries` VALUES (111, 'Kiribati', 'KI', 'KIR');";
$updates['214'][] = "INSERT INTO `countries` VALUES (114, 'Kuwait', 'KW', 'KWT');";
$updates['214'][] = "INSERT INTO `countries` VALUES (115, 'Kyrgyzstan', 'KG', 'KGZ');";
$updates['214'][] = "INSERT INTO `countries` VALUES (116, 'Lao People''s Democratic Republic', 'LA', 'LAO');";
$updates['214'][] = "INSERT INTO `countries` VALUES (117, 'Latvia', 'LV', 'LVA');";
$updates['214'][] = "INSERT INTO `countries` VALUES (118, 'Lebanon', 'LB', 'LBN');";
$updates['214'][] = "INSERT INTO `countries` VALUES (119, 'Lesotho', 'LS', 'LSO');";
$updates['214'][] = "INSERT INTO `countries` VALUES (120, 'Liberia', 'LR', 'LBR');";
$updates['214'][] = "INSERT INTO `countries` VALUES (121, 'Libyan Arab Jamahiriya', 'LY', 'LBY');";
$updates['214'][] = "INSERT INTO `countries` VALUES (122, 'Liechtenstein', 'LI', 'LIE');";
$updates['214'][] = "INSERT INTO `countries` VALUES (123, 'Lithuania', 'LT', 'LTU');";
$updates['214'][] = "INSERT INTO `countries` VALUES (124, 'Luxembourg', 'LU', 'LUX');";
$updates['214'][] = "INSERT INTO `countries` VALUES (125, 'Macau', 'MO', 'MAC');";
$updates['214'][] = "INSERT INTO `countries` VALUES (126, 'Macedonia, The Former Yugoslav Republic of', 'MK', 'MKD');";
$updates['214'][] = "INSERT INTO `countries` VALUES (127, 'Madagascar', 'MG', 'MDG');";
$updates['214'][] = "INSERT INTO `countries` VALUES (128, 'Malawi', 'MW', 'MWI');";
$updates['214'][] = "INSERT INTO `countries` VALUES (130, 'Maldives', 'MV', 'MDV');";
$updates['214'][] = "INSERT INTO `countries` VALUES (131, 'Mali', 'ML', 'MLI');";
$updates['214'][] = "INSERT INTO `countries` VALUES (132, 'Malta', 'MT', 'MLT');";
$updates['214'][] = "INSERT INTO `countries` VALUES (133, 'Marshall Islands', 'MH', 'MHL');";
$updates['214'][] = "INSERT INTO `countries` VALUES (134, 'Martinique', 'MQ', 'MTQ');";
$updates['214'][] = "INSERT INTO `countries` VALUES (135, 'Mauritania', 'MR', 'MRT');";
$updates['214'][] = "INSERT INTO `countries` VALUES (136, 'Mauritius', 'MU', 'MUS');";
$updates['214'][] = "INSERT INTO `countries` VALUES (137, 'Mayotte', 'YT', 'MYT');";
$updates['214'][] = "INSERT INTO `countries` VALUES (138, 'Mexico', 'MX', 'MEX');";
$updates['214'][] = "INSERT INTO `countries` VALUES (139, 'Micronesia, Federated States of', 'FM', 'FSM');";
$updates['214'][] = "INSERT INTO `countries` VALUES (140, 'Moldova, Republic of', 'MD', 'MDA');";
$updates['214'][] = "INSERT INTO `countries` VALUES (141, 'Monaco', 'MC', 'MCO');";
$updates['214'][] = "INSERT INTO `countries` VALUES (142, 'Mongolia', 'MN', 'MNG');";
$updates['214'][] = "INSERT INTO `countries` VALUES (143, 'Montserrat', 'MS', 'MSR');";
$updates['214'][] = "INSERT INTO `countries` VALUES (144, 'Morocco', 'MA', 'MAR');";
$updates['214'][] = "INSERT INTO `countries` VALUES (145, 'Mozambique', 'MZ', 'MOZ');";
$updates['214'][] = "INSERT INTO `countries` VALUES (146, 'Myanmar', 'MM', 'MMR');";
$updates['214'][] = "INSERT INTO `countries` VALUES (147, 'Namibia', 'NA', 'NAM');";
$updates['214'][] = "INSERT INTO `countries` VALUES (148, 'Nauru', 'NR', 'NRU');";
$updates['214'][] = "INSERT INTO `countries` VALUES (149, 'Nepal', 'NP', 'NPL');";
$updates['214'][] = "INSERT INTO `countries` VALUES (150, 'Netherlands', 'NL', 'NLD');";
$updates['214'][] = "INSERT INTO `countries` VALUES (151, 'Netherlands Antilles', 'AN', 'ANT');";
$updates['214'][] = "INSERT INTO `countries` VALUES (152, 'New Caledonia', 'NC', 'NCL');";
$updates['214'][] = "INSERT INTO `countries` VALUES (153, 'New Zealand', 'NZ', 'NZL');";
$updates['214'][] = "INSERT INTO `countries` VALUES (154, 'Nicaragua', 'NI', 'NIC');";
$updates['214'][] = "INSERT INTO `countries` VALUES (155, 'Niger', 'NE', 'NER');";
$updates['214'][] = "INSERT INTO `countries` VALUES (156, 'Nigeria', 'NG', 'NGA');";
$updates['214'][] = "INSERT INTO `countries` VALUES (157, 'Niue', 'NU', 'NIU');";
$updates['214'][] = "INSERT INTO `countries` VALUES (158, 'Norfolk Island', 'NF', 'NFK');";
$updates['214'][] = "INSERT INTO `countries` VALUES (159, 'Northern Mariana Islands', 'MP', 'MNP');";
$updates['214'][] = "INSERT INTO `countries` VALUES (160, 'Norway', 'NO', 'NOR');";
$updates['214'][] = "INSERT INTO `countries` VALUES (161, 'Oman', 'OM', 'OMN');";
$updates['214'][] = "INSERT INTO `countries` VALUES (163, 'Palau', 'PW', 'PLW');";
$updates['214'][] = "INSERT INTO `countries` VALUES (164, 'Panama', 'PA', 'PAN');";
$updates['214'][] = "INSERT INTO `countries` VALUES (165, 'Papua New Guinea', 'PG', 'PNG');";
$updates['214'][] = "INSERT INTO `countries` VALUES (166, 'Paraguay', 'PY', 'PRY');";
$updates['214'][] = "INSERT INTO `countries` VALUES (167, 'Peru', 'PE', 'PER');";
$updates['214'][] = "INSERT INTO `countries` VALUES (168, 'Philippines', 'PH', 'PHL');";
$updates['214'][] = "INSERT INTO `countries` VALUES (169, 'Pitcairn', 'PN', 'PCN');";
$updates['214'][] = "INSERT INTO `countries` VALUES (170, 'Poland', 'PL', 'POL');";
$updates['214'][] = "INSERT INTO `countries` VALUES (171, 'Portugal', 'PT', 'PRT');";
$updates['214'][] = "INSERT INTO `countries` VALUES (172, 'Puerto Rico', 'PR', 'PRI');";
$updates['214'][] = "INSERT INTO `countries` VALUES (173, 'Qatar', 'QA', 'QAT');";
$updates['214'][] = "INSERT INTO `countries` VALUES (174, 'Reunion', 'RE', 'REU');";
$updates['214'][] = "INSERT INTO `countries` VALUES (175, 'Romania', 'RO', 'ROM');";
$updates['214'][] = "INSERT INTO `countries` VALUES (176, 'Russian Federation', 'RU', 'RUS');";
$updates['214'][] = "INSERT INTO `countries` VALUES (177, 'Rwanda', 'RW', 'RWA');";
$updates['214'][] = "INSERT INTO `countries` VALUES (178, 'Saint Kitts and Nevis', 'KN', 'KNA');";
$updates['214'][] = "INSERT INTO `countries` VALUES (179, 'Saint Lucia', 'LC', 'LCA');";
$updates['214'][] = "INSERT INTO `countries` VALUES (180, 'Saint Vincent and the Grenadines', 'VC', 'VCT');";
$updates['214'][] = "INSERT INTO `countries` VALUES (181, 'Samoa', 'WS', 'WSM');";
$updates['214'][] = "INSERT INTO `countries` VALUES (182, 'San Marino', 'SM', 'SMR');";
$updates['214'][] = "INSERT INTO `countries` VALUES (183, 'Sao Tome and Principe', 'ST', 'STP');";
$updates['214'][] = "INSERT INTO `countries` VALUES (185, 'Senegal', 'SN', 'SEN');";
$updates['214'][] = "INSERT INTO `countries` VALUES (186, 'Seychelles', 'SC', 'SYC');";
$updates['214'][] = "INSERT INTO `countries` VALUES (187, 'Sierra Leone', 'SL', 'SLE');";
$updates['214'][] = "INSERT INTO `countries` VALUES (189, 'Slovakia (Slovak Republic)', 'SK', 'SVK');";
$updates['214'][] = "INSERT INTO `countries` VALUES (190, 'Slovenia', 'SI', 'SVN');";
$updates['214'][] = "INSERT INTO `countries` VALUES (191, 'Solomon Islands', 'SB', 'SLB');";
$updates['214'][] = "INSERT INTO `countries` VALUES (192, 'Somalia', 'SO', 'SOM');";
$updates['214'][] = "INSERT INTO `countries` VALUES (193, 'South Africa', 'ZA', 'ZAF');";
$updates['214'][] = "INSERT INTO `countries` VALUES (194, 'South Georgia and the South Sandwich Islands', 'GS', 'SGS');";
$updates['214'][] = "INSERT INTO `countries` VALUES (195, 'Spain', 'ES', 'ESP');";
$updates['214'][] = "INSERT INTO `countries` VALUES (196, 'Sri Lanka', 'LK', 'LKA');";
$updates['214'][] = "INSERT INTO `countries` VALUES (197, 'St. Helena', 'SH', 'SHN');";
$updates['214'][] = "INSERT INTO `countries` VALUES (198, 'St. Pierre and Miquelon', 'PM', 'SPM');";
$updates['214'][] = "INSERT INTO `countries` VALUES (199, 'Sudan', 'SD', 'SDN');";
$updates['214'][] = "INSERT INTO `countries` VALUES (200, 'Suriname', 'SR', 'SUR');";
$updates['214'][] = "INSERT INTO `countries` VALUES (201, 'Svalbard and Jan Mayen Islands', 'SJ', 'SJM');";
$updates['214'][] = "INSERT INTO `countries` VALUES (202, 'Swaziland', 'SZ', 'SWZ');";
$updates['214'][] = "INSERT INTO `countries` VALUES (203, 'Sweden', 'SE', 'SWE');";
$updates['214'][] = "INSERT INTO `countries` VALUES (204, 'Switzerland', 'CH', 'CHE');";
$updates['214'][] = "INSERT INTO `countries` VALUES (205, 'Syrian Arab Republic', 'SY', 'SYR');";
$updates['214'][] = "INSERT INTO `countries` VALUES (206, 'Taiwan', 'TW', 'TWN');";
$updates['214'][] = "INSERT INTO `countries` VALUES (207, 'Tajikistan', 'TJ', 'TJK');";
$updates['214'][] = "INSERT INTO `countries` VALUES (208, 'Tanzania, United Republic of', 'TZ', 'TZA');";
$updates['214'][] = "INSERT INTO `countries` VALUES (210, 'Togo', 'TG', 'TGO');";
$updates['214'][] = "INSERT INTO `countries` VALUES (211, 'Tokelau', 'TK', 'TKL');";
$updates['214'][] = "INSERT INTO `countries` VALUES (212, 'Tonga', 'TO', 'TON');";
$updates['214'][] = "INSERT INTO `countries` VALUES (213, 'Trinidad and Tobago', 'TT', 'TTO');";
$updates['214'][] = "INSERT INTO `countries` VALUES (214, 'Tunisia', 'TN', 'TUN');";
$updates['214'][] = "INSERT INTO `countries` VALUES (215, 'Turkey', 'TR', 'TUR');";
$updates['214'][] = "INSERT INTO `countries` VALUES (216, 'Turkmenistan', 'TM', 'TKM');";
$updates['214'][] = "INSERT INTO `countries` VALUES (217, 'Turks and Caicos Islands', 'TC', 'TCA');";
$updates['214'][] = "INSERT INTO `countries` VALUES (218, 'Tuvalu', 'TV', 'TUV');";
$updates['214'][] = "INSERT INTO `countries` VALUES (219, 'Uganda', 'UG', 'UGA');";
$updates['214'][] = "INSERT INTO `countries` VALUES (220, 'Ukraine', 'UA', 'UKR');";
$updates['214'][] = "INSERT INTO `countries` VALUES (222, 'United Kingdom', 'GB', 'GBR');";
$updates['214'][] = "INSERT INTO `countries` VALUES (223, 'United States', 'US', 'USA');";
$updates['214'][] = "INSERT INTO `countries` VALUES (224, 'United States Minor Outlying Islands', 'UM', 'UMI');";
$updates['214'][] = "INSERT INTO `countries` VALUES (225, 'Uruguay', 'UY', 'URY');";
$updates['214'][] = "INSERT INTO `countries` VALUES (226, 'Uzbekistan', 'UZ', 'UZB');";
$updates['214'][] = "INSERT INTO `countries` VALUES (227, 'Vanuatu', 'VU', 'VUT');";
$updates['214'][] = "INSERT INTO `countries` VALUES (228, 'Vatican City State (Holy See)', 'VA', 'VAT');";
$updates['214'][] = "INSERT INTO `countries` VALUES (229, 'Venezuela', 'VE', 'VEN');";
$updates['214'][] = "INSERT INTO `countries` VALUES (231, 'Virgin Islands (British)', 'VG', 'VGB');";
$updates['214'][] = "INSERT INTO `countries` VALUES (232, 'Virgin Islands (U.S.)', 'VI', 'VIR');";
$updates['214'][] = "INSERT INTO `countries` VALUES (233, 'Wallis and Futuna Islands', 'WF', 'WLF');";
$updates['214'][] = "INSERT INTO `countries` VALUES (234, 'Western Sahara', 'EH', 'ESH');";
$updates['214'][] = "INSERT INTO `countries` VALUES (235, 'Yemen', 'YE', 'YEM');";
$updates['214'][] = "INSERT INTO `countries` VALUES (236, 'Yugoslavia', 'YU', 'YUG');";
$updates['214'][] = "INSERT INTO `countries` VALUES (237, 'Zaire', 'ZR', 'ZAR');";
$updates['214'][] = "INSERT INTO `countries` VALUES (238, 'Zambia', 'ZM', 'ZMB');";
$updates['214'][] = "INSERT INTO `countries` VALUES (239, 'Zimbabwe', 'ZW', 'ZWE');";

//RC2
$updates[214][] = "ALTER TABLE `users` ADD `date_seperator` CHAR( 1 ) DEFAULT '-' NOT NULL AFTER `date_format` ;";
$updates[214][] = "UPDATE users SET date_format='dmY' WHERE date_format='d-m-Y';";
$updates[214][] = "UPDATE users SET date_format='mdY' WHERE date_format='m-d-Y';";

$updates[214][] = "ALTER TABLE `users` DROP INDEX `id`";
$updates[214][] = "ALTER TABLE `sync_contacts_syncs` ADD PRIMARY KEY ( `device_id` ) ";
$updates[214][] = "ALTER TABLE `sync_contacts_maps` ADD PRIMARY KEY ( `device_id` , `contact_id` , `remote_id` ) ;";
$updates[214][] = "ALTER TABLE `sync_events_syncs` ADD PRIMARY KEY ( `device_id` ) ";
$updates[214][] = "ALTER TABLE `sync_events_maps` ADD PRIMARY KEY ( `device_id` , `event_id` , `remote_id` ) ;";
$updates[214][] = "ALTER TABLE `modules` DROP INDEX `id`";
$updates[214][] = "ALTER TABLE `db_sequence` DROP INDEX `seq_name`";
$updates[214][] = "ALTER TABLE `acl_items` DROP INDEX `id`";
$updates[214][] = "CREATE TABLE acl_temp(acl_id INT, user_id INT, group_id INT);";
$updates[214][] = "INSERT INTO acl_temp(acl_id,user_id,group_id) SELECT DISTINCT acl_id,user_id,group_id FROM acl;";
$updates[214][] = "DELETE FROM acl;";
$updates[214][] = "INSERT INTO acl(acl_id,user_id,group_id) SELECT acl_id,user_id,group_id FROM acl_temp;";
$updates[214][] = "DROP TABLE acl_temp;";
$updates[214][] = "ALTER TABLE `acl` ADD PRIMARY KEY ( `acl_id` , `user_id`, group_id ) ;";
$updates[214][] = "ALTER TABLE `acl` DROP INDEX `acl_id` ;";
$updates[214][] = "ALTER TABLE `acl` DROP INDEX `user_id` ;";
$updates[214][] = "ALTER TABLE `acl` DROP INDEX `group_id` ;";

$updates[214][] = "CREATE TABLE users_groups_temp(user_id INT, group_id INT);";
$updates[214][] = "INSERT INTO users_groups_temp(user_id,group_id) SELECT DISTINCT user_id,group_id FROM users_groups;";
$updates[214][] = "DELETE FROM users_groups;";
$updates[214][] = "INSERT INTO users_groups(user_id,group_id) SELECT user_id,group_id FROM users_groups_temp;";
$updates[214][] = "DROP TABLE users_groups_temp;";
$updates[214][] = "ALTER TABLE `groups` DROP INDEX `id`";
$updates[214][] = "ALTER TABLE `users_groups` DROP INDEX `user_id`";
$updates[214][] = "ALTER TABLE `users_groups` DROP INDEX `group_id`";
$updates[214][] = "ALTER TABLE `users_groups` ADD PRIMARY KEY ( `group_id` , `user_id` ) ;";

$updates[214][] = "ALTER TABLE `users` ADD `bank` VARCHAR( 50 ) NOT NULL ,
ADD `bank_no` VARCHAR( 50 ) NOT NULL ;";

$updates[213][] = "ALTER TABLE `sync_contacts_maps` ADD INDEX ( `device_id` ) ";
$updates[213][] = "ALTER TABLE `sync_contacts_syncs` ADD PRIMARY KEY ( `device_id` ) ";
$updates[213][] = "ALTER TABLE `sync_events_maps` ADD INDEX ( `device_id` ) ";
$updates[213][] = "ALTER TABLE `sync_events_syncs` ADD PRIMARY KEY ( `device_id` )";
$updates[213][] = "ALTER TABLE `cal_participants` DROP INDEX `id`";
$updates[213][] = "ALTER TABLE `modules` CHANGE `id` `id` VARCHAR( 50 ) NOT NULL";
$updates[213][] = "DROP TABLE `admin_settings`";
$updates[213][] = "ALTER TABLE `settings` DROP PRIMARY KEY";
$updates[213][] = "ALTER TABLE `settings` ADD `user_id` INT NOT NULL FIRST ;";
$updates[213][] = "ALTER TABLE `settings` ADD PRIMARY KEY ( `user_id` , `name` )";
$updates[213][] = "ALTER TABLE `users` ADD `use_checkbox_select` ENUM( '0', '1' ) NOT NULL ;";
$updates[213][] = "ALTER TABLE `settings` CHANGE `name` `name` VARCHAR( 50 ) NOT NULL ";
$updates[213][] = "ALTER TABLE `users` ADD `enabled` ENUM( '0', '1' ) DEFAULT '1' NOT NULL AFTER `password`";
$updates[213][] = "ALTER TABLE `users` DROP `auth_source`;";
$updates[213][] = "ALTER TABLE `sync_devices` ADD `manufacturer` VARCHAR( 50 ) NOT NULL AFTER `id` ,
ADD `model` VARCHAR( 50 ) NOT NULL AFTER `manufacturer` ,
ADD `software_version` VARCHAR( 50 ) NOT NULL AFTER `model` ;";


$updates[212][] = "ALTER TABLE `modules` ADD `admin_menu` ENUM( '0', '1' ) NOT NULL AFTER `sort_order` ;";
$updates[211][] = "UPDATE cal_events SET end_time=start_time+86340 WHERE all_day_event='1';";
$updates[211][] = "UPDATE users SET theme='Annie' WHERE theme='annie';";
$updates[211][] = "UPDATE emAccounts SET password='';";

$updates[210][] = "UPDATE users SET theme='Default' WHERE theme='Professional' OR theme='Professional 2'";

$updates[209] = array();
$updates[208] = array();

$updates[207][] = "ALTER TABLE `modules` ADD `sort_order` INT NOT NULL AFTER `path`;";
$updates[207][] = "ALTER TABLE `users` ADD `sort_name` VARCHAR(20) NOT NULL DEFAULT 'first_name';";
$updates[207][] = "DROP TABLE filetypes";

$updates[206] = array();

$updates[205][] = "CREATE TABLE `admin_settings` (
  `user_id` int(11) NOT NULL default '0',
  `sort_field` varchar(20) NOT NULL default '',
  `sort_order` varchar(4) NOT NULL default '',
  PRIMARY KEY  (`user_id`)
) TYPE=MyISAM;";

/******************************************************************************
UPDATES FOR 2.04
*******************************************************************************/
$updates[204][] = "ALTER TABLE `cms_sites` ADD `domain` VARCHAR( 100 ) NOT NULL AFTER `name`";
$updates[204][] = "ALTER TABLE `users` ADD `DST` ENUM( '0', '1', '2' ) NOT NULL AFTER `timezone`";

$updates[204][] = "CREATE TABLE `cms_settings` (
  `user_id` int(11) NOT NULL default '0',
  `sort_field` varchar(20) NOT NULL default '',
  `sort_order` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`user_id`)
) TYPE=MyISAM;";

$updates[204][] = "CREATE TABLE `fs_settings` (
  `user_id` int(11) NOT NULL default '0',
  `sort_field` varchar(20) NOT NULL default '',
  `sort_order` varchar(4) NOT NULL default '',
  PRIMARY KEY  (`user_id`)
) TYPE=MyISAM;";

$updates[204][] = "CREATE TABLE `no_settings` (
  `user_id` int(11) NOT NULL default '0',
  `sort_field` varchar(20) NOT NULL default '',
  `sort_order` varchar(20) NOT NULL default '',
  `show_notes` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`user_id`)
) TYPE=MyISAM;";

$updates[204][] = "CREATE TABLE `pm_settings` (
  `user_id` int(11) NOT NULL default '0',
  `sort_field` varchar(20) NOT NULL default '',
  `sort_order` varchar(20) NOT NULL default '',
  `show_projects` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`user_id`)
) TYPE=MyISAM;";

$updates[204][] = "CREATE TABLE `ab_settings` (
  `user_id` int(11) NOT NULL default '0',
  `sort_contacts_field` varchar(20) NOT NULL default '',
  `sort_contacts_order` varchar(4) NOT NULL default '',
  `sort_companies_field` varchar(20) NOT NULL default '',
  `sort_companies_order` varchar(4) NOT NULL default '',
  `sort_users_field` varchar(20) NOT NULL default '',
  `sort_users_order` varchar(4) NOT NULL default '',
  `search_type` varchar(10) NOT NULL default '',
  `search_contacts_field` varchar(30) NOT NULL default '',
  `search_companies_field` varchar(30) NOT NULL default '',
  `search_users_field` varchar(30) NOT NULL default '',
  `search_addressbook_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`user_id`)
) TYPE=MyISAM;";

$updates[204][] = "CREATE TABLE `em_settings` (
  `user_id` int(11) NOT NULL default '0',
  `sort_field` tinyint(4) NOT NULL default '0',
  `sort_order` enum('0','1') NOT NULL default '0',
  `send_format` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`user_id`)
) TYPE=MyISAM;";

$updates[204][] = "CREATE TABLE `cal_views` (
  `id` int(11) NOT NULL default '0',
  `standard` enum('0','1') NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `start_hour` tinyint(4) NOT NULL default '0',
  `end_hour` tinyint(4) NOT NULL default '0',
  `type` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) TYPE=MyISAM;";

$updates[204][] = "CREATE TABLE `cal_views_calendars` (
  `view_id` int(11) NOT NULL default '0',
  `calendar_id` int(11) NOT NULL default '0'
) TYPE=MyISAM;";

$updates[204][] = "CREATE TABLE `cal_settings` (
  `user_id` int(11) NOT NULL default '0',
  `default_cal_id` int(11) NOT NULL default '0',
  `default_view_id` int(11) NOT NULL default '0',
  `show_days` tinyint(4) NOT NULL default '0',
  `hide_completed_todos` enum('0','1') NOT NULL default '0',
  `show_todos` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`user_id`)
) TYPE=MyISAM;";
$updates[204][] = "ALTER TABLE `users` DROP `samba_user`;";
$updates[204][] = "ALTER TABLE `modules` ADD `version` VARCHAR( 5 ) NOT NULL AFTER `id`;";

/******************************************************************************
UPDATES FOR 2.03
*******************************************************************************/
$updates[203][] = "CREATE TABLE `cal_todos` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `contact_id` int(11) NOT NULL default '0',
  `ctime` int(11) NOT NULL default '0',
  `mtime` int(11) NOT NULL default '0',
  `start_time` int(11) NOT NULL default '0',
  `due_time` int(11) NOT NULL default '0',
  `completion_time` int(11) NOT NULL default '0',
  `remind_time` int(11) NOT NULL default '0',
  `remind_style` enum('0','1') NOT NULL default '0',
  `status` tinyint(4) NOT NULL default '0',
  `priority` enum('0','1','2') NOT NULL default '0',
  `res_user_id` int(11) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `description` text NOT NULL,
  `location` varchar(50) NOT NULL default '',
  `background` varchar(6) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`,`res_user_id`),
  KEY `remind_time` (`remind_time`)
) TYPE=MyISAM;";

$updates[203][] = "ALTER TABLE `cal_reminders` DROP INDEX `event_id`";
$updates[203][] = "ALTER TABLE `cal_reminders` ADD INDEX ( `remind_time` )";

/******************************************************************************
UPDATES FOR 2.02
*******************************************************************************/
$updates[202][] = "ALTER TABLE `ab_contacts` ADD `user_id` INT NOT NULL AFTER `id`";
$updates[202][] = "ALTER TABLE `ab_companies` ADD `user_id` INT NOT NULL AFTER `id`";
$updates[202][] = "ALTER TABLE `ab_companies` DROP PRIMARY KEY , ADD PRIMARY KEY ( `id` )";
$updates[202][] = "ALTER TABLE `ab_companies` ADD INDEX ( `addressbook_id` )";
$updates[202][] = "CREATE TABLE `bm_catagories` (
									`id` int(11) NOT NULL default '0',
									`user_id` int(11) NOT NULL default '0',
									`name` varchar(50) NOT NULL default '',
									`acl_read` int(11) NOT NULL default '0',
									`acl_write` int(11) NOT NULL default '0',
									PRIMARY KEY  (`id`)
								) TYPE=MyISAM;";
$updates[202][] = "ALTER TABLE `bookmarks` RENAME `bm_bookmarks`";
$updates[202][] = "ALTER TABLE `bm_bookmarks` ADD `catagory_id` INT NOT NULL AFTER `user_id`";
$updates[202][] = "ALTER TABLE `bm_bookmarks` ADD `acl_read` INT NOT NULL AFTER `new_window`";
$updates[202][] = "ALTER TABLE `bm_bookmarks` ADD `acl_write` INT NOT NULL AFTER `acl_read`";
$updates[202][] = "ALTER TABLE `cal_events` ADD `ctime` INT NOT NULL , ADD `mtime` INT NOT NULL";
$updates[202][] = "CREATE TABLE `ab_custom_contact_fields_cats` (
									`id` INT NOT NULL ,
									`name` VARCHAR( 50 ) NOT NULL ,
									`acl_id` INT NOT NULL ,
									PRIMARY KEY ( `id` )
									);";

$updates[202][] = "CREATE TABLE `ab_custom_company_fields_cats` (
									`id` INT NOT NULL ,
									`name` VARCHAR( 50 ) NOT NULL ,
									`acl_id` INT NOT NULL ,
									PRIMARY KEY ( `id` )
									);";
$updates[202][] = "ALTER TABLE `ab_custom_contact_fields_sort` ADD `catagory_id` INT NOT NULL FIRST";
$updates[202][] = "ALTER TABLE `ab_custom_contact_fields_sort` ADD INDEX ( `catagory_id` )";
$updates[202][] = "ALTER TABLE `ab_custom_company_fields_sort` ADD `catagory_id` INT NOT NULL FIRST";
$updates[202][] = "ALTER TABLE `ab_custom_company_fields_sort` ADD INDEX ( `catagory_id` )";

/******************************************************************************
UPDATES FOR 2.01
*******************************************************************************/
$updates[201] = array();

/******************************************************************************
UPDATES FOR 2.0
*******************************************************************************/
$updates[200][] = "ALTER TABLE `users` ADD `first_weekday` TINYINT NOT NULL";

/******************************************************************************
UPDATES FOR 1.98
*******************************************************************************/
$updates[198] = array();

/******************************************************************************
UPDATES FOR 1.97
*******************************************************************************/
$updates[197][] = "ALTER TABLE `users` ADD `language` VARCHAR( 20 ) NOT NULL, ADD `theme` VARCHAR( 20 ) NOT NULL";
$updates[197][] = "CREATE TABLE `tp_mailing_companies` (
  `group_id` int(11) NOT NULL default '0',
  `company_id` int(11) NOT NULL default '0',
  KEY `group_id` (`group_id`)
) TYPE=MyISAM;";
$updates[197][] = "CREATE TABLE `tp_mailing_contacts` (
  `group_id` int(11) NOT NULL default '0',
  `contact_id` int(11) NOT NULL default '0',
  KEY `group_id` (`group_id`)
) TYPE=MyISAM;";
$updates[197][] = "CREATE TABLE `tp_mailing_groups` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `acl_read` int(11) NOT NULL default '0',
  `acl_write` int(11) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;";

$updates[197][] = "CREATE TABLE `tp_subscribed` (
  `user_id` int(11) NOT NULL default '0',
  `template_id` int(11) NOT NULL default '0',
  KEY `user_id` (`user_id`,`template_id`)
) TYPE=MyISAM;";

$updates[197][] = "CREATE TABLE `tp_templates` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `type` tinyint(4) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `content` longblob NOT NULL,
  `acl_read` int(11) NOT NULL default '0',
  `acl_write` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;";

/******************************************************************************
UPDATES FOR 1.96
*******************************************************************************/

$updates[196][] = "CREATE TABLE `ab_custom_company_fields` (
  `id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;";

$updates[196][] = "CREATE TABLE `ab_custom_company_fields_sort` (
  `field` varchar(50) NOT NULL default '',
  `size` tinyint(4) NOT NULL default '0',
  `sort_index` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`field`)
) TYPE=MyISAM;";

$updates[196][] = "CREATE TABLE `ab_custom_contact_fields` (
  `id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;";

$updates[196][] = "CREATE TABLE `ab_custom_contact_fields_sort` (
  `field` varchar(50) NOT NULL default '',
  `size` tinyint(4) NOT NULL default '0',
  `sort_index` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`field`),
  KEY `sort_index` (`sort_index`)
) TYPE=MyISAM;";

$updates[196][] = "ALTER TABLE `users` ADD `start_module` VARCHAR( 50 ) NOT NULL ;";
$updates[196][] = "ALTER TABLE `emAccounts` ADD `auto_check` ENUM( '0', '1' ) NOT NULL ;";
$updates[196][] = "ALTER TABLE `ab_contacts` DROP `homepage` ,
DROP `work_address` ,
DROP `work_zip` ,
DROP `work_city` ,
DROP `work_state` ,
DROP `work_country`;";

$updates[196][] = "ALTER TABLE `ab_contacts` CHANGE `company` `company_id` INT NOT NULL;";
$updates[196][] = "CREATE TABLE `ab_companies` (
  `id` int(11) NOT NULL default '0',
  `addressbook_id` int(11) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `address` varchar(100) NOT NULL default '',
  `zip` varchar(10) NOT NULL default '',
  `city` varchar(50) NOT NULL default '',
  `state` varchar(50) NOT NULL default '',
  `country` varchar(50) NOT NULL default '',
  `phone` varchar(20) NOT NULL default '',
  `fax` varchar(20) NOT NULL default '',
  `email` varchar(75) NOT NULL default '',
  `homepage` varchar(100) NOT NULL default '',
  `bank_no` varchar(20) NOT NULL default '',
  `vat_no` varchar(30) NOT NULL default '',
  `acl_read` int(11) NOT NULL default '0',
  `acl_write` int(11) NOT NULL default '0'
) TYPE=MyISAM;";

/******************************************************************************
UPDATES FOR 1.95
*******************************************************************************/
$updates[195][] = "ALTER TABLE `ab_contacts` ADD `acl_read` INT NOT NULL;";
$updates[195][] = "ALTER TABLE `ab_contacts` ADD `acl_write` INT NOT NULL;";
$updates[195][] = "ALTER TABLE `acl_items` ADD `user_id` INT NOT NULL;";
$updates[195][] = "ALTER TABLE `pmProjects` ADD `probability` TINYINT NOT NULL ,
ADD `fee_id` INT NOT NULL ,
ADD `budget` INT NOT NULL ,
ADD `description` VARCHAR(50) NOT NULL;";

$updates[195][] = "ALTER TABLE `pmHours` DROP `fee_id`;";

$updates[195][] = "CREATE TABLE `no_notes` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `contact_id` int(11) NOT NULL default '0',
  `project_id` int(11) NOT NULL default '0',
  `file_path` varchar(255) NOT NULL default '0',
  `catagory_id` int(11) NOT NULL default '0',
  `res_user_id` int(11) NOT NULL default '0',
  `due_date` int(11) NOT NULL default '0',
  `ctime` int(11) NOT NULL default '0',
  `mtime` int(11) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `content` text NOT NULL,
  `acl_read` INT NOT NULL,
  `acl_write` INT NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`,`contact_id`,`project_id`),
  KEY `file_path` (`file_path`)
) TYPE=MyISAM;";

$updates[195][] = "CREATE TABLE `no_catagories` (
  `id` int(11) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;";

$updates[195][] = "CREATE TABLE `cal_holidays` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `calendar_id` int(11) NOT NULL default '0',
  `region` varchar ( 4 ) NOT NULL default '',
  `date` int(10) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;";
$updates[195][] = "ALTER TABLE `users` CHANGE `birthday` `birthday` DATE DEFAULT '0000-00-00 00:00:00' NOT NULL;";
$updates[195][] = "ALTER TABLE `ab_contacts` CHANGE `birthday` `birthday` DATE DEFAULT '0000-00-00 00:00:00' NOT NULL;";
$updates[195][] = "ALTER TABLE `users` CHANGE `password` `password` VARCHAR( 64 ) NOT NULL;";
$updates[195][] = "ALTER TABLE `emAccounts` CHANGE `password` `password` VARCHAR( 64 ) NOT NULL;";

/******************************************************************************
OLDER VERSIONS NOT SUPPORTED BY THIS INSTALLER. DOWNLOAD 1.95 IF YOU WANT TO
UPGRADE FROM AN OLDER VERSION THEN 1.95.
*******************************************************************************/
