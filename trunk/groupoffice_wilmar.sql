-- phpMyAdmin SQL Dump
-- version 3.1.2deb1ubuntu0.1
-- http://www.phpmyadmin.net
--
-- Machine: localhost
-- Genereertijd: 22 Oct 2009 om 09:47
-- Serverversie: 5.0.75
-- PHP-Versie: 5.2.6-3ubuntu4.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `groupofficecom_wilmar`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `ab_addressbooks`
--

CREATE TABLE IF NOT EXISTS `ab_addressbooks` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `name` varchar(50) default NULL,
  `acl_read` int(11) NOT NULL default '0',
  `acl_write` int(11) NOT NULL default '0',
  `shared_acl` tinyint(1) NOT NULL,
  `default_iso_address_format` varchar(2) NOT NULL,
  `default_salutation` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `ab_addressbooks`
--

INSERT INTO `ab_addressbooks` (`id`, `user_id`, `name`, `acl_read`, `acl_write`, `shared_acl`, `default_iso_address_format`, `default_salutation`) VALUES
(1, 1, 'Potentiële klanten', 15, 16, 0, 'NL', 'Geachte [heer/mevrouw] {middle_name} {last_name}'),
(2, 1, 'Leveranciers', 17, 18, 0, 'NL', 'Geachte [heer/mevrouw] {middle_name} {last_name}'),
(3, 1, 'Klanten', 19, 20, 0, 'NL', 'Geachte [heer/mevrouw] {middle_name} {last_name}');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `ab_companies`
--

CREATE TABLE IF NOT EXISTS `ab_companies` (
  `id` int(11) NOT NULL default '0',
  `link_id` int(11) default NULL,
  `user_id` int(11) NOT NULL default '0',
  `addressbook_id` int(11) NOT NULL default '0',
  `name` varchar(100) default NULL,
  `address` varchar(100) default NULL,
  `address_no` varchar(10) default NULL,
  `zip` varchar(10) default NULL,
  `city` varchar(50) default NULL,
  `state` varchar(50) default NULL,
  `country` varchar(50) default NULL,
  `iso_address_format` varchar(2) NOT NULL,
  `post_address` varchar(100) default NULL,
  `post_address_no` varchar(10) default NULL,
  `post_city` varchar(50) default NULL,
  `post_state` varchar(50) default NULL,
  `post_country` varchar(50) default NULL,
  `post_zip` varchar(10) default NULL,
  `post_iso_address_format` varchar(2) NOT NULL,
  `phone` varchar(30) default NULL,
  `fax` varchar(30) default NULL,
  `email` varchar(75) default NULL,
  `homepage` varchar(100) default NULL,
  `comment` text,
  `bank_no` varchar(50) default NULL,
  `vat_no` varchar(30) default NULL,
  `ctime` int(11) NOT NULL default '0',
  `mtime` int(11) NOT NULL default '0',
  `email_allowed` enum('0','1') NOT NULL,
  `files_folder_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `addressbook_id` (`addressbook_id`),
  KEY `addressbook_id_2` (`addressbook_id`),
  KEY `link_id` (`link_id`),
  KEY `link_id_2` (`link_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `ab_companies`
--


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `ab_contacts`
--

CREATE TABLE IF NOT EXISTS `ab_contacts` (
  `id` int(11) NOT NULL default '0',
  `link_id` int(11) default NULL,
  `user_id` int(11) NOT NULL default '0',
  `addressbook_id` int(11) NOT NULL default '0',
  `source_id` int(11) NOT NULL default '0',
  `first_name` varchar(50) default '',
  `middle_name` varchar(50) default NULL,
  `last_name` varchar(50) default NULL,
  `initials` varchar(10) default NULL,
  `title` varchar(10) default NULL,
  `sex` enum('M','F') NOT NULL default 'M',
  `birthday` date default NULL,
  `email` varchar(100) default NULL,
  `email2` varchar(100) default NULL,
  `email3` varchar(100) default NULL,
  `company_id` int(11) NOT NULL default '0',
  `department` varchar(50) default NULL,
  `function` varchar(50) default NULL,
  `home_phone` varchar(30) default NULL,
  `work_phone` varchar(30) default NULL,
  `fax` varchar(30) default NULL,
  `work_fax` varchar(30) default NULL,
  `cellular` varchar(30) default NULL,
  `country` varchar(50) default NULL,
  `state` varchar(50) default NULL,
  `city` varchar(50) default NULL,
  `zip` varchar(10) default NULL,
  `address` varchar(100) default NULL,
  `address_no` varchar(10) default NULL,
  `comment` text,
  `iso_address_format` varchar(2) NOT NULL,
  `color` varchar(6) default NULL,
  `sid` varchar(32) default NULL,
  `ctime` int(11) NOT NULL default '0',
  `mtime` int(11) NOT NULL default '0',
  `salutation` varchar(50) default NULL,
  `email_allowed` enum('0','1') NOT NULL,
  `files_folder_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `link_id` (`link_id`),
  KEY `addressbook_id` (`addressbook_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `ab_contacts`
--


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `bs_batchjobs`
--

CREATE TABLE IF NOT EXISTS `bs_batchjobs` (
  `id` int(11) NOT NULL default '0',
  `book_id` int(11) NOT NULL default '0',
  `time` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `from_status_id` int(11) NOT NULL default '0',
  `to_status_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `book_id` (`book_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `bs_batchjobs`
--


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `bs_batchjob_orders`
--

CREATE TABLE IF NOT EXISTS `bs_batchjob_orders` (
  `batchjob_id` int(11) NOT NULL default '0',
  `order_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`batchjob_id`,`order_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `bs_batchjob_orders`
--


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `bs_books`
--

CREATE TABLE IF NOT EXISTS `bs_books` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `name` varchar(100) default NULL,
  `acl_read` int(11) NOT NULL default '0',
  `acl_write` int(11) NOT NULL default '0',
  `order_id_prefix` varchar(10) default NULL,
  `show_statuses` varchar(100) default NULL,
  `next_id` int(11) NOT NULL default '0',
  `default_vat` double NOT NULL default '0',
  `currency` varchar(10) default NULL,
  `order_csv_template` text,
  `item_csv_template` text,
  `country` char(2) NOT NULL,
  `bcc` varchar(100) default NULL,
  `call_after_days` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `bs_books`
--

INSERT INTO `bs_books` (`id`, `user_id`, `name`, `acl_read`, `acl_write`, `order_id_prefix`, `show_statuses`, `next_id`, `default_vat`, `currency`, `order_csv_template`, `item_csv_template`, `country`, `bcc`, `call_after_days`) VALUES
(1, 1, 'Offertes', 49, 50, 'Q%y', NULL, 0, 19, '€', '', '', 'NL', NULL, 3),
(2, 1, 'Orders', 51, 52, 'O%y', NULL, 4, 19, '€', '', '', 'NL', NULL, 0),
(3, 1, 'Facturen', 53, 54, 'I%y', NULL, 14, 19, '€', '', '', 'NL', NULL, 0),
(9, 1, 'Webshop Orders', 606, 607, '%y-', '', 8, 19, '€', '', '', 'NL', '', 0),
(10, 1, 'Webshop Orders', 606, 607, '%y-', '', 0, 19, '€', '', '', 'NL', '', 0),
(11, 1, 'Webshop Orders', 615, 616, '%y-', NULL, 60, 19, '€', '', '', 'NL', '', 0);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `bs_category_languages`
--

CREATE TABLE IF NOT EXISTS `bs_category_languages` (
  `language_id` int(11) NOT NULL default '0',
  `category_id` int(11) NOT NULL default '0',
  `name` varchar(50) default NULL,
  PRIMARY KEY  (`language_id`,`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `bs_category_languages`
--

INSERT INTO `bs_category_languages` (`language_id`, `category_id`, `name`) VALUES
(1, 160, 'Niet-vliegende vogels'),
(1, 159, 'Vliegende vogels'),
(1, 158, 'Gevogelte'),
(1, 157, 'Computers'),
(1, 156, 'Electronica'),
(1, 155, 'Webshop-demo');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `bs_expenses`
--

CREATE TABLE IF NOT EXISTS `bs_expenses` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `expense_book_id` int(11) NOT NULL default '0',
  `category_id` int(11) NOT NULL default '0',
  `supplier` varchar(100) default NULL,
  `invoice_no` varchar(50) default NULL,
  `ctime` int(11) NOT NULL default '0',
  `mtime` int(11) NOT NULL default '0',
  `btime` int(11) default '0',
  `ptime` int(11) default NULL,
  `subtotal` double NOT NULL default '0',
  `vat` double NOT NULL default '0',
  `invoice_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `book_id` (`expense_book_id`,`category_id`),
  KEY `invoice_id` (`invoice_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `bs_expenses`
--


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `bs_expense_books`
--

CREATE TABLE IF NOT EXISTS `bs_expense_books` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `acl_read` int(11) NOT NULL default '0',
  `acl_write` int(11) NOT NULL default '0',
  `name` varchar(50) default NULL,
  `currency` varchar(10) default NULL,
  `vat` double NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `bs_expense_books`
--

INSERT INTO `bs_expense_books` (`id`, `user_id`, `acl_read`, `acl_write`, `name`, `currency`, `vat`) VALUES
(1, 0, 55, 56, 'Kosten', '€', 19);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `bs_expense_categories`
--

CREATE TABLE IF NOT EXISTS `bs_expense_categories` (
  `id` int(11) NOT NULL default '0',
  `expense_book_id` int(11) NOT NULL default '0',
  `name` varchar(100) default NULL,
  PRIMARY KEY  (`id`),
  KEY `book_id` (`expense_book_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `bs_expense_categories`
--

INSERT INTO `bs_expense_categories` (`id`, `expense_book_id`, `name`) VALUES
(1, 1, 'Kantoor spullen'),
(2, 1, 'Auto''s'),
(3, 1, 'Internet'),
(4, 1, 'Overige');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `bs_items`
--

CREATE TABLE IF NOT EXISTS `bs_items` (
  `id` int(11) NOT NULL default '0',
  `order_id` int(11) NOT NULL default '0',
  `product_id` int(11) NOT NULL default '0',
  `description` text,
  `unit_cost` double NOT NULL default '0',
  `unit_price` double NOT NULL default '0',
  `unit_list` double NOT NULL default '0',
  `unit_total` double NOT NULL default '0',
  `amount` double NOT NULL default '0',
  `vat` double NOT NULL default '0',
  `discount` double NOT NULL default '0',
  `sort_order` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `bs_items`
--

INSERT INTO `bs_items` (`id`, `order_id`, `product_id`, `description`, `unit_cost`, `unit_price`, `unit_list`, `unit_total`, `amount`, `vat`, `discount`, `sort_order`) VALUES
(1, 1, 271, 'De bejaarde postduif Frans heeft ons jaren trouwe\ndienst bewezen en verdient nu een goede oude duivendag. Voor de echte\nliefhebber.', 50, 200, 200, 238, 1, 19, 0, 0),
(2, 2, 271, 'De bejaarde postduif Frans heeft ons jaren trouwe\ndienst bewezen en verdient nu een goede oude duivendag. Voor de echte\nliefhebber.', 50, 200, 200, 238, 1, 19, 0, 0),
(3, 3, 271, 'De bejaarde postduif Frans heeft ons jaren trouwe\ndienst bewezen en verdient nu een goede oude duivendag. Voor de echte\nliefhebber.', 50, 200, 200, 238, 1, 19, 0, 0),
(4, 4, 272, 'Vanwege de sloop van onze kinderboerderij verkopen\nwij deze struisvogel Barry. Hij is 5 jaar oud en al die tijd een publiekslieveling\ngeweest.', 50, 900, 900, 1071, 3, 19, 0, 0),
(5, 5, 272, 'Vanwege de sloop van onze kinderboerderij verkopen\nwij deze struisvogel Barry. Hij is 5 jaar oud en al die tijd een publiekslieveling\ngeweest.', 5, 9, 9, 10.71, 2, 19, 0, 0),
(6, 6, 272, 'Vanwege de sloop van onze kinderboerderij verkopen\nwij deze struisvogel Barry. Hij is 5 jaar oud en al die tijd een publiekslieveling\ngeweest.', 5, 9, 9, 10.71, 2, 19, 0, 0),
(7, 7, 272, 'Vanwege de sloop van onze kinderboerderij verkopen\nwij deze struisvogel Barry. Hij is 5 jaar oud en al die tijd een publiekslieveling\ngeweest.', 5, 9, 9, 10.71, 2, 19, 0, 0),
(8, 8, 278, 'De bejaarde postduif Frans heeft ons jaren trouwe\ndienst bewezen en verdient nu een goede oude duivendag. Voor de echte\nliefhebber.', 5, 20, 20, 23.8, 1, 19, 0, 0),
(9, 9, 280, 'Wij hebben een 3 jaar oude legkip. Elke maand legt\nzij circa 10 eieren, lekker voor bij het ontbijt. Vanwege verhuizing willen wij\nhaar verkopen.', 1, 5, 5, 5.95, 3, 19, 0, 0),
(10, 9, 282, 'Functionerende radio uit 1933 van bakeliet. Onbeschadigd\nen in prima werkende staat. U kunt uw favoriete zenders met een charmante jaren ''30\ngeluid beluisteren.', 2, 5, 5, 5.95, 1, 19, 0, 0),
(11, 9, 284, 'Authentieke transformator. Stamt nog uit de tijd dat\nwisselstroom 220V was en niet 230V.', 1.5, 3, 3, 3.57, 1, 19, 0, 0),
(12, 10, 288, 'Deze zwart-wit televisie is zeer robuust. Na 35 jaar\ndoet deze retro televisie het nog uitstekend.', 1.5, 4.5, 4.5, 5.36, 2, 19, 0, 0),
(13, 10, 291, 'Authentieke transformator. Stamt nog uit de tijd dat\nwisselstroom 220V was en niet 230V.', 1.5, 3, 3, 3.57, 1, 19, 0, 0),
(14, 11, 288, 'Deze zwart-wit televisie is zeer robuust. Na 35 jaar\ndoet deze retro televisie het nog uitstekend.', 1.5, 4.5, 4.5, 5.36, 1, 19, 0, 0),
(15, 13, 291, 'Authentieke transformator. Stamt nog uit de tijd dat\nwisselstroom 220V was en niet 230V.', 1.5, 3, 3, 3.57, 1, 19, 0, 0),
(16, 14, 291, 'Authentieke transformator. Stamt nog uit de tijd dat\nwisselstroom 220V was en niet 230V.', 1.5, 3, 3, 3.57, 1, 19, 0, 0),
(17, 15, 291, 'Authentieke transformator. Stamt nog uit de tijd dat\nwisselstroom 220V was en niet 230V.', 1.5, 3, 3, 3.57, 1, 19, 0, 0),
(18, 17, 291, 'Authentieke transformator. Stamt nog uit de tijd dat\nwisselstroom 220V was en niet 230V.', 1.5, 3, 3, 3.57, 1, 19, 0, 0),
(19, 19, 288, 'Deze zwart-wit televisie is zeer robuust. Na 35 jaar\ndoet deze retro televisie het nog uitstekend.', 1.5, 4.5, 4.5, 5.36, 1, 19, 0, 0),
(20, 20, 288, 'Deze zwart-wit televisie is zeer robuust. Na 35 jaar\ndoet deze retro televisie het nog uitstekend.', 1.5, 4.5, 4.5, 5.36, 1, 19, 0, 0),
(21, 21, 288, 'Deze zwart-wit televisie is zeer robuust. Na 35 jaar\ndoet deze retro televisie het nog uitstekend.', 1.5, 4.5, 4.5, 5.36, 1, 19, 0, 0),
(22, 22, 291, 'Authentieke transformator. Stamt nog uit de tijd dat\nwisselstroom 220V was en niet 230V.', 1.5, 3, 3, 3.57, 1, 19, 0, 0),
(23, 23, 288, 'Deze zwart-wit televisie is zeer robuust. Na 35 jaar\ndoet deze retro televisie het nog uitstekend.', 1.5, 4.5, 4.5, 5.36, 1, 19, 0, 0),
(24, 24, 291, 'Authentieke transformator. Stamt nog uit de tijd dat\nwisselstroom 220V was en niet 230V.', 1.5, 3, 3, 3.57, 1, 19, 0, 0),
(25, 26, 291, 'Authentieke transformator. Stamt nog uit de tijd dat\nwisselstroom 220V was en niet 230V.', 1.5, 3, 3, 3.57, 1, 19, 0, 0),
(26, 27, 291, 'Authentieke transformator. Stamt nog uit de tijd dat\nwisselstroom 220V was en niet 230V.', 1.5, 3, 3, 3.57, 1, 19, 0, 0),
(27, 28, 291, 'Authentieke transformator. Stamt nog uit de tijd dat\nwisselstroom 220V was en niet 230V.', 1.5, 3, 3, 3.57, 1, 19, 0, 0),
(28, 29, 291, 'Authentieke transformator. Stamt nog uit de tijd dat\nwisselstroom 220V was en niet 230V.', 1.5, 3, 3, 3.57, 1, 19, 0, 0),
(29, 30, 291, 'Authentieke transformator. Stamt nog uit de tijd dat\nwisselstroom 220V was en niet 230V.', 1.5, 3, 3, 3.57, 1, 19, 0, 0),
(30, 31, 291, 'Authentieke transformator. Stamt nog uit de tijd dat\nwisselstroom 220V was en niet 230V.', 1.5, 3, 3, 3.57, 1, 19, 0, 0),
(31, 32, 291, 'Authentieke transformator. Stamt nog uit de tijd dat\nwisselstroom 220V was en niet 230V.', 1.5, 3, 3, 3.57, 1, 19, 0, 0),
(32, 33, 291, 'Authentieke transformator. Stamt nog uit de tijd dat\nwisselstroom 220V was en niet 230V.', 1.5, 3, 3, 3.57, 1, 19, 0, 0),
(33, 34, 291, 'Authentieke transformator. Stamt nog uit de tijd dat\nwisselstroom 220V was en niet 230V.', 1.5, 3, 3, 3.57, 1, 19, 0, 0),
(34, 35, 291, 'Authentieke transformator. Stamt nog uit de tijd dat\nwisselstroom 220V was en niet 230V.', 1.5, 3, 3, 3.57, 1, 19, 0, 0),
(35, 36, 291, 'Authentieke transformator. Stamt nog uit de tijd dat\nwisselstroom 220V was en niet 230V.', 1.5, 3, 3, 3.57, 1, 19, 0, 0),
(36, 37, 291, 'Authentieke transformator. Stamt nog uit de tijd dat\nwisselstroom 220V was en niet 230V.', 1.5, 3, 3, 3.57, 1, 19, 0, 0),
(37, 38, 291, 'Authentieke transformator. Stamt nog uit de tijd dat\nwisselstroom 220V was en niet 230V.', 1.5, 3, 3, 3.57, 1, 19, 0, 0),
(38, 39, 291, 'Authentieke transformator. Stamt nog uit de tijd dat\nwisselstroom 220V was en niet 230V.', 1.5, 3, 3, 3.57, 1, 19, 0, 0),
(39, 40, 291, 'Authentieke transformator. Stamt nog uit de tijd dat\nwisselstroom 220V was en niet 230V.', 1.5, 3, 3, 3.57, 1, 19, 0, 0),
(40, 41, 291, 'Authentieke transformator. Stamt nog uit de tijd dat\nwisselstroom 220V was en niet 230V.', 1.5, 3, 3, 3.57, 1, 19, 0, 0),
(41, 42, 291, 'Authentieke transformator. Stamt nog uit de tijd dat\nwisselstroom 220V was en niet 230V.', 1.5, 3, 3, 3.57, 1, 19, 0, 0),
(42, 43, 291, 'Authentieke transformator. Stamt nog uit de tijd dat\nwisselstroom 220V was en niet 230V.', 1.5, 3, 3, 3.57, 1, 19, 0, 0),
(43, 44, 291, 'Authentieke transformator. Stamt nog uit de tijd dat\nwisselstroom 220V was en niet 230V.', 1.5, 3, 3, 3.57, 1, 19, 0, 0),
(44, 45, 291, 'Authentieke transformator. Stamt nog uit de tijd dat\nwisselstroom 220V was en niet 230V.', 1.5, 3, 3, 3.57, 1, 19, 0, 0),
(45, 46, 288, 'Deze zwart-wit televisie is zeer robuust. Na 35 jaar\ndoet deze retro televisie het nog uitstekend.', 1.5, 4.5, 4.5, 5.36, 1, 19, 0, 0),
(46, 47, 287, 'Wij hebben een 3 jaar oude legkip. Elke maand legt\nzij circa 10 eieren, lekker voor bij het ontbijt. Vanwege verhuizing willen wij\nhaar verkopen.', 1, 5, 5, 5.95, 2, 19, 0, 0),
(47, 48, 287, 'Wij hebben een 3 jaar oude legkip. Elke maand legt\nzij circa 10 eieren, lekker voor bij het ontbijt. Vanwege verhuizing willen wij\nhaar verkopen.', 1, 5, 5, 5.95, 1, 19, 0, 0),
(48, 48, 291, 'Authentieke transformator. Stamt nog uit de tijd dat\nwisselstroom 220V was en niet 230V.', 1.5, 3, 3, 3.57, 1, 19, 0, 0),
(49, 49, 287, 'Wij hebben een 3 jaar oude legkip. Elke maand legt\nzij circa 10 eieren, lekker voor bij het ontbijt. Vanwege verhuizing willen wij\nhaar verkopen.', 1, 5, 5, 5.95, 1, 19, 0, 0),
(50, 49, 291, 'Authentieke transformator. Stamt nog uit de tijd dat\nwisselstroom 220V was en niet 230V.', 1.5, 3, 3, 3.57, 1, 19, 0, 0),
(51, 50, 291, 'Authentieke transformator. Stamt nog uit de tijd dat\nwisselstroom 220V was en niet 230V.', 1.5, 3, 3, 3.57, 3, 19, 0, 0),
(52, 51, 291, 'Authentieke transformator. Stamt nog uit de tijd dat\nwisselstroom 220V was en niet 230V.', 1.5, 3, 3, 3.57, 3, 19, 0, 0),
(53, 52, 291, 'Authentieke transformator. Stamt nog uit de tijd dat\nwisselstroom 220V was en niet 230V.', 1.5, 3, 3, 3.57, 3, 19, 0, 0),
(54, 53, 291, 'Authentieke transformator. Stamt nog uit de tijd dat\nwisselstroom 220V was en niet 230V.', 1.5, 3, 3, 3.57, 3, 19, 0, 0),
(55, 55, 291, 'Authentieke transformator. Stamt nog uit de tijd dat\nwisselstroom 220V was en niet 230V.', 1.5, 3, 3, 3.57, 1, 19, 0, 0),
(56, 56, 291, 'Authentieke transformator. Stamt nog uit de tijd dat\nwisselstroom 220V was en niet 230V.', 1.5, 3, 3, 3.57, 1, 19, 0, 0),
(57, 57, 291, 'Authentieke transformator. Stamt nog uit de tijd dat\nwisselstroom 220V was en niet 230V.', 1.5, 3, 3, 3.57, 3, 19, 0, 0),
(58, 58, 291, 'Authentieke transformator. Stamt nog uit de tijd dat\nwisselstroom 220V was en niet 230V.', 1.5, 3, 3, 3.57, 3, 19, 0, 0),
(59, 59, 291, 'Authentieke transformator. Stamt nog uit de tijd dat\nwisselstroom 220V was en niet 230V.', 1.5, 3, 3, 3.57, 1, 19, 0, 0),
(60, 60, 291, 'Authentieke transformator. Stamt nog uit de tijd dat\nwisselstroom 220V was en niet 230V.', 1.5, 3, 3, 3.57, 1, 19, 0, 0),
(61, 61, 291, 'Authentieke transformator. Stamt nog uit de tijd dat\nwisselstroom 220V was en niet 230V.', 1.5, 3, 3, 3.57, 1, 19, 0, 0),
(62, 62, 291, 'Authentieke transformator. Stamt nog uit de tijd dat\nwisselstroom 220V was en niet 230V.', 1.5, 3, 3, 3.57, 1, 19, 0, 0),
(63, 63, 291, 'Authentieke transformator. Stamt nog uit de tijd dat\nwisselstroom 220V was en niet 230V.', 1.5, 3, 3, 3.57, 2, 19, 0, 0),
(64, 64, 291, 'Authentieke transformator. Stamt nog uit de tijd dat\nwisselstroom 220V was en niet 230V.', 1.5, 3, 3, 3.57, 2, 19, 0, 0),
(65, 65, 286, 'Vanwege de sloop van onze kinderboerderij verkopen\nwij deze struisvogel Barry. Hij is 5 jaar oud en al die tijd een publiekslieveling\ngeweest.', 5, 9, 9, 10.71, 1, 19, 0, 0),
(66, 66, 286, 'Vanwege de sloop van onze kinderboerderij verkopen\nwij deze struisvogel Barry. Hij is 5 jaar oud en al die tijd een publiekslieveling\ngeweest.', 5, 9, 9, 10.71, 1, 19, 0, 0),
(67, 67, 286, 'Vanwege de sloop van onze kinderboerderij verkopen\nwij deze struisvogel Barry. Hij is 5 jaar oud en al die tijd een publiekslieveling\ngeweest.', 5, 9, 9, 10.71, 1, 19, 0, 0),
(68, 68, 291, 'Authentieke transformator. Stamt nog uit de tijd dat\nwisselstroom 220V was en niet 230V.', 1.5, 3, 3, 3.57, 1, 19, 0, 0),
(69, 69, 291, 'Authentieke transformator. Stamt nog uit de tijd dat\nwisselstroom 220V was en niet 230V.', 1.5, 3, 3, 3.57, 1, 19, 0, 0),
(70, 70, 285, 'De bejaarde postduif Frans heeft ons jaren trouwe\ndienst bewezen en verdient nu een goede oude duivendag. Voor de echte\nliefhebber.', 5, 20, 20, 23.8, 1, 19, 0, 0),
(71, 71, 285, 'De bejaarde postduif Frans heeft ons jaren trouwe\ndienst bewezen en verdient nu een goede oude duivendag. Voor de echte\nliefhebber.', 5, 20, 20, 23.8, 1, 19, 0, 0),
(72, 72, 285, 'De bejaarde postduif Frans heeft ons jaren trouwe\ndienst bewezen en verdient nu een goede oude duivendag. Voor de echte\nliefhebber.', 5, 20, 20, 23.8, 1, 19, 0, 0),
(73, 73, 285, 'De bejaarde postduif Frans heeft ons jaren trouwe\ndienst bewezen en verdient nu een goede oude duivendag. Voor de echte\nliefhebber.', 5, 20, 20, 23.8, 1, 19, 0, 0),
(74, 74, 285, 'De bejaarde postduif Frans heeft ons jaren trouwe\ndienst bewezen en verdient nu een goede oude duivendag. Voor de echte\nliefhebber.', 5, 20, 20, 23.8, 1, 19, 0, 0),
(75, 76, 285, 'De bejaarde postduif Frans heeft ons jaren trouwe\ndienst bewezen en verdient nu een goede oude duivendag. Voor de echte\nliefhebber.', 5, 20, 20, 23.8, 1, 19, 0, 0),
(76, 77, 285, 'De bejaarde postduif Frans heeft ons jaren trouwe\ndienst bewezen en verdient nu een goede oude duivendag. Voor de echte\nliefhebber.', 5, 20, 20, 23.8, 1, 19, 0, 0),
(77, 78, 285, 'De bejaarde postduif Frans heeft ons jaren trouwe\ndienst bewezen en verdient nu een goede oude duivendag. Voor de echte\nliefhebber.', 5, 20, 20, 23.8, 1, 19, 0, 0),
(78, 79, 285, 'De bejaarde postduif Frans heeft ons jaren trouwe\ndienst bewezen en verdient nu een goede oude duivendag. Voor de echte\nliefhebber.', 5, 20, 20, 23.8, 1, 19, 0, 0),
(79, 80, 285, 'De bejaarde postduif Frans heeft ons jaren trouwe\ndienst bewezen en verdient nu een goede oude duivendag. Voor de echte\nliefhebber.', 5, 20, 20, 23.8, 1, 19, 0, 0),
(80, 81, 287, 'Wij hebben een 3 jaar oude legkip. Elke maand legt\nzij circa 10 eieren, lekker voor bij het ontbijt. Vanwege verhuizing willen wij\nhaar verkopen.', 1, 5, 5, 5.95, 2, 19, 0, 0),
(81, 82, 287, 'Wij hebben een 3 jaar oude legkip. Elke maand legt\nzij circa 10 eieren, lekker voor bij het ontbijt. Vanwege verhuizing willen wij\nhaar verkopen.', 1, 5, 5, 5.95, 2, 19, 0, 0),
(82, 83, 285, 'De bejaarde postduif Frans heeft ons jaren trouwe\ndienst bewezen en verdient nu een goede oude duivendag. Voor de echte\nliefhebber.', 5, 20, 20, 23.8, 1, 19, 0, 0),
(83, 84, 285, 'De bejaarde postduif Frans heeft ons jaren trouwe\ndienst bewezen en verdient nu een goede oude duivendag. Voor de echte\nliefhebber.', 5, 20, 20, 23.8, 1, 19, 0, 0),
(84, 85, 286, 'Vanwege de sloop van onze kinderboerderij verkopen\nwij deze struisvogel Barry. Hij is 5 jaar oud en al die tijd een publiekslieveling\ngeweest.', 5, 9, 9, 10.71, 1, 19, 0, 0),
(85, 86, 286, 'Vanwege de sloop van onze kinderboerderij verkopen\nwij deze struisvogel Barry. Hij is 5 jaar oud en al die tijd een publiekslieveling\ngeweest.', 5, 9, 9, 10.71, 1, 19, 0, 0),
(86, 87, 288, 'Deze zwart-wit televisie is zeer robuust. Na 35 jaar\ndoet deze retro televisie het nog uitstekend.', 1.5, 4.5, 4.5, 5.36, 1, 19, 0, 0),
(87, 88, 288, 'Deze zwart-wit televisie is zeer robuust. Na 35 jaar\ndoet deze retro televisie het nog uitstekend.', 1.5, 4.5, 4.5, 5.36, 1, 19, 0, 0);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `bs_languages`
--

CREATE TABLE IF NOT EXISTS `bs_languages` (
  `id` int(11) NOT NULL default '0',
  `language` varchar(10) default NULL,
  `name` varchar(50) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `bs_languages`
--

INSERT INTO `bs_languages` (`id`, `language`, `name`) VALUES
(1, 'nl', 'Standaard');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `bs_numbers`
--

CREATE TABLE IF NOT EXISTS `bs_numbers` (
  `book_id` int(11) NOT NULL default '0',
  `type` tinyint(4) NOT NULL default '0',
  `next_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`book_id`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `bs_numbers`
--


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `bs_orders`
--

CREATE TABLE IF NOT EXISTS `bs_orders` (
  `id` int(11) NOT NULL default '0',
  `status_id` int(11) NOT NULL default '0',
  `book_id` int(11) NOT NULL default '0',
  `language_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `order_id` varchar(20) NOT NULL,
  `po_id` varchar(50) NOT NULL,
  `company_id` int(11) NOT NULL default '0',
  `contact_id` int(11) NOT NULL default '0',
  `ctime` int(11) NOT NULL default '0',
  `mtime` int(11) NOT NULL default '0',
  `btime` int(11) NOT NULL default '0',
  `ptime` int(11) NOT NULL,
  `costs` double NOT NULL default '0',
  `subtotal` double NOT NULL default '0',
  `vat` double NOT NULL default '0',
  `total` double NOT NULL default '0',
  `authcode` varchar(50) default NULL,
  `frontpage_text` text NOT NULL,
  `customer_name` varchar(50) default NULL,
  `customer_salutation` varchar(100) default NULL,
  `customer_contact_name` varchar(50) default NULL,
  `customer_address` varchar(50) default NULL,
  `customer_address_no` varchar(10) default NULL,
  `customer_zip` varchar(20) default NULL,
  `customer_city` varchar(50) default NULL,
  `customer_state` varchar(50) default NULL,
  `customer_country` char(2) NOT NULL,
  `customer_vat_no` varchar(50) default NULL,
  `customer_email` varchar(100) default NULL,
  `customer_extra` varchar(255) NOT NULL,
  `webshop_id` int(11) NOT NULL default '0',
  `recur_type` varchar(10) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `recurred_order_id` int(11) NOT NULL default '0',
  `reference` varchar(100) NOT NULL,
  `order_bonus_points` int(11) default NULL,
  `pagebreak` enum('0','1') NOT NULL,
  `files_folder_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `company_id` (`company_id`),
  KEY `book_id` (`book_id`),
  KEY `status_id` (`status_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `bs_orders`
--

INSERT INTO `bs_orders` (`id`, `status_id`, `book_id`, `language_id`, `user_id`, `order_id`, `po_id`, `company_id`, `contact_id`, `ctime`, `mtime`, `btime`, `ptime`, `costs`, `subtotal`, `vat`, `total`, `authcode`, `frontpage_text`, `customer_name`, `customer_salutation`, `customer_contact_name`, `customer_address`, `customer_address_no`, `customer_zip`, `customer_city`, `customer_state`, `customer_country`, `customer_vat_no`, `customer_email`, `customer_extra`, `webshop_id`, `recur_type`, `payment_method`, `recurred_order_id`, `reference`, `order_bonus_points`, `pagebreak`, `files_folder_id`) VALUES
(1, 44, 2, 1, 1, 'O2009000004', '', 0, 0, 1255610005, 1256123868, 1255610005, 1256123868, 0, 200, 38, 238, '8f54851aba87a5d820f0f83318b4323f', '', 'Group-Office Beheerder', 'Geachte heer Beheerder', NULL, 'fcncv', 'vnv', 'vnv', 'vnc', 'vnnvc', 'NL', '', 'webmaster@example.com', '', 71, '', '', 0, '', NULL, '0', 130),
(2, 4, 2, 1, 1, 'O2009000001', '', 0, 0, 1255610311, 1255610362, 1255610310, 1255610311, 0, 200, 38, 238, '2075924bc240d03a4785b23b5e3e118b', '', 'Group-Office Beheerder', 'Geachte heer Beheerder', NULL, 'fcncv', 'vnv', 'vnv', 'vnc', 'vnnvc', 'NL', '', 'webmaster@example.com', '', 71, '', 'Teletik Safepay Card', 0, '', NULL, '0', 131),
(3, 9, 3, 1, 1, 'I2009000001', '', 0, 0, 1255610362, 1255610362, 1255610310, 1255610311, 0, 200, 38, 238, 'd7e33fe674962f03e577dfb263eeb6eb', '', 'Group-Office Beheerder', 'Geachte heer Beheerder', '', 'fcncv', 'vnv', 'vnv', 'vnc', 'vnnvc', 'NL', '', 'webmaster@example.com', '', 71, '', 'Teletik Safepay Card', 0, '', 0, '0', 133),
(4, 5, 2, 1, 1, 'O2009000002', '', 0, 0, 1255610733, 1255610733, 1255610733, 1255610733, 0, 2700, 513, 3213, '4c5cf28df179e2f7ece522380d554133', '', 'Group-Office Beheerder', 'Geachte heer Beheerder', NULL, 'fcncv', 'vnv', 'vnv', 'vnc', 'vnnvc', 'NL', '', 'webmaster@example.com', '', 71, '', '', 0, '', NULL, '0', 134),
(5, 32, 2, 1, 1, 'O2009000003', '', 0, 0, 1255611055, 1255675623, 1255611055, 1255611055, 0, 18, 3.42, 21.42, 'f3eb69ffe248e9df2d4a89432ec2c889', '', 'Group-Office Beheerder', 'Geachte heer Beheerder', NULL, 'fcncv', 'vnv', 'vnv', 'vnc', 'vnnvc', 'NL', '', 'webmaster@example.com', '', 71, '', 'Teletik Safepay Card', 0, '', NULL, '0', 135),
(6, 9, 3, 1, 1, 'I2009000002', '', 0, 0, 1255611070, 1255611070, 1255611055, 1255611055, 0, 18, 3.42, 21.42, 'ed03bd78284739b3bdd69f4207d5f305', '', 'Group-Office Beheerder', 'Geachte heer Beheerder', '', 'fcncv', 'vnv', 'vnv', 'vnc', 'vnnvc', 'NL', '', 'webmaster@example.com', '', 71, '', 'Teletik Safepay Card', 0, '', 0, '0', 136),
(7, 9, 3, 1, 1, 'I2009000003', '', 0, 0, 1255675623, 1255675623, 1255611055, 1255611055, 0, 18, 3.42, 21.42, '7b734917e682a280ab121b5c6ccf8e90', '', 'Group-Office Beheerder', 'Geachte heer Beheerder', '', 'fcncv', 'vnv', 'vnv', 'vnc', 'vnnvc', 'NL', '', 'webmaster@example.com', '', 71, '', 'Teletik Safepay Card', 0, '', 0, '0', 139),
(8, 33, 9, 1, 1, '2009-000001', '', 0, 0, 1255958630, 1255958630, 1255958630, 1255958630, 0, 20, 3.8, 23.8, '16d0e718d9f29ca78e46a21f1b41a3fb', '', 'Group-Office Beheerder', 'Geachte heer Beheerder', NULL, 'fcncv', 'vnv', 'vnv', 'vnc', 'vnnvc', 'NL', '', 'webmaster@example.com', '', 72, '', '', 0, '', NULL, '0', 145),
(9, 33, 9, 1, 1, '2009-000002', '', 0, 0, 1255959006, 1255959006, 1255959006, 1255959006, 0, 23, 4.37, 27.37, '4806786c50b847874c7688f879d52f83', '', 'Group-Office Beheerder', 'Geachte heer Beheerder', NULL, 'fcncv', 'vnv', 'vnv', 'vnc', 'vnnvc', 'NL', '', 'webmaster@example.com', '', 72, '', '', 0, '', NULL, '0', 146),
(10, 41, 11, 1, 1, '2009-000001', '', 0, 0, 1256036740, 1256036740, 1256036740, 1256036740, 0, 12, 2.29, 14.29, '5069b5a8bcde708dfe1fb4771e69c44b', '', 'Group-Office Beheerder', 'Geachte heer Beheerder', NULL, 'fcncv', 'vnv', 'vnv', 'vnc', 'vnnvc', 'NL', '', 'webmaster@example.com', '', 73, '', '', 0, '', NULL, '0', 149),
(11, 41, 11, 1, 1, '2009-000002', '', 0, 0, 1256036966, 1256036966, 1256036966, 1256036966, 0, 4.5, 0.86, 5.36, '92c761a3dbc2a26d397636e1d43bffe3', '', 'Group-Office Beheerder', 'Geachte heer Beheerder', NULL, 'fcncv', 'vnv', 'vnv', 'vnc', 'vnnvc', 'NL', '', 'webmaster@example.com', '', 73, '', '', 0, '', NULL, '0', 150),
(12, 41, 11, 1, 1, '2009-000003', '', 0, 0, 1256037664, 1256037664, 1256037664, 1256037664, 0, 0, 0, 0, 'd48938e9f1cad2218feb964efe70edfc', '', 'Group-Office Beheerder', 'Geachte heer Beheerder', NULL, 'fcncv', 'vnv', 'vnv', 'vnc', 'vnnvc', 'NL', '', 'webmaster@example.com', '', 73, '', '', 0, '', NULL, '0', 151),
(13, 41, 11, 1, 1, '2009-000004', '', 0, 0, 1256037706, 1256037706, 1256037706, 1256037706, 0, 3, 0.57, 3.57, '7a60bd4521a8b8f24c6e098f07a1ae75', '', 'Group-Office Beheerder', 'Geachte heer Beheerder', NULL, 'fcncv', 'vnv', 'vnv', 'vnc', 'vnnvc', 'NL', '', 'webmaster@example.com', '', 73, '', '', 0, '', NULL, '0', 152),
(14, 41, 11, 1, 1, '2009-000005', '', 0, 0, 1256044841, 1256044841, 1256044841, 1256044841, 0, 3, 0.57, 3.57, 'cd544eb5b2185f590f8eaf7d2c51efc2', '', 'Group-Office Beheerder', 'Geachte heer Beheerder', NULL, 'fcncv', 'vnv', 'vnv', 'vnc', 'vnnvc', 'NL', '', 'webmaster@example.com', '', 73, '', '', 0, '', NULL, '0', 153),
(15, 41, 11, 1, 1, '2009-000006', '', 0, 0, 1256045109, 1256045109, 1256045109, 1256045109, 0, 3, 0.57, 3.57, '6d4a716fd6b7fec25c302371f61b3598', '', 'Group-Office Beheerder', 'Geachte heer Beheerder', NULL, 'fcncv', 'vnv', 'vnv', 'vnc', 'vnnvc', 'NL', '', 'webmaster@example.com', '', 73, '', '', 0, '', NULL, '0', 154),
(16, 41, 11, 1, 1, '2009-000007', '', 0, 0, 1256045123, 1256045123, 1256045123, 1256045123, 0, 0, 0, 0, 'f29c20e14ff8c844333b073d72c6e13d', '', 'Group-Office Beheerder', 'Geachte heer Beheerder', NULL, 'fcncv', 'vnv', 'vnv', 'vnc', 'vnnvc', 'NL', '', 'webmaster@example.com', '', 73, '', '', 0, '', NULL, '0', 155),
(17, 41, 11, 1, 1, '2009-000008', '', 0, 0, 1256045424, 1256045424, 1256045424, 1256045424, 0, 3, 0.57, 3.57, 'd3715b384b3d2f510ab08c0df6c301b1', '', 'Group-Office Beheerder', 'Geachte heer Beheerder', NULL, 'fcncv', 'vnv', 'vnv', 'vnc', 'vnnvc', 'NL', '', 'webmaster@example.com', '', 73, '', '', 0, '', NULL, '0', 156),
(18, 41, 11, 1, 1, '2009-000009', '', 0, 0, 1256045806, 1256045806, 1256045806, 1256045806, 0, 0, 0, 0, '1b79639487ae61e0f785d64ecd10d1c5', '', 'Group-Office Beheerder', 'Geachte heer Beheerder', NULL, 'fcncv', 'vnv', 'vnv', 'vnc', 'vnnvc', 'NL', '', 'webmaster@example.com', '', 73, '', '', 0, '', NULL, '0', 157),
(19, 41, 11, 1, 1, '2009-000010', '', 0, 0, 1256045833, 1256045833, 1256045833, 1256045833, 0, 4.5, 0.86, 5.36, 'c77e42832895493b5dbc22261d622798', '', 'Group-Office Beheerder', 'Geachte heer Beheerder', NULL, 'fcncv', 'vnv', 'vnv', 'vnc', 'vnnvc', 'NL', '', 'webmaster@example.com', '', 73, '', '', 0, '', NULL, '0', 158),
(20, 41, 11, 1, 1, '2009-000011', '', 0, 0, 1256045892, 1256045892, 1256045892, 1256045892, 0, 4.5, 0.86, 5.36, '788f5884abccdee1cebbb4d9976957ae', '', 'Group-Office Beheerder', 'Geachte heer Beheerder', NULL, 'fcncv', 'vnv', 'vnv', 'vnc', 'vnnvc', 'NL', '', 'webmaster@example.com', '', 73, '', '', 0, '', NULL, '0', 159),
(21, 41, 11, 1, 1, '2009-000012', '', 0, 0, 1256045928, 1256045928, 1256045928, 1256045928, 0, 4.5, 0.86, 5.36, '4c0d5742a15c331032b14fc197d60148', '', 'Group-Office Beheerder', 'Geachte heer Beheerder', NULL, 'fcncv', 'vnv', 'vnv', 'vnc', 'vnnvc', 'NL', '', 'webmaster@example.com', '', 73, '', '', 0, '', NULL, '0', 160),
(22, 41, 11, 1, 1, '2009-000013', '', 0, 0, 1256048092, 1256048092, 1256048092, 1256048092, 0, 3, 0.57, 3.57, 'b55595e174ded5f7d57f68ab563a19dc', '', 'Group-Office Beheerder', 'Geachte heer Beheerder', NULL, 'fcncv', 'vnv', 'vnv', 'vnc', 'vnnvc', 'NL', '', 'webmaster@example.com', '', 73, '', '', 0, '', NULL, '0', 161),
(23, 41, 11, 1, 1, '2009-000014', '', 0, 0, 1256049007, 1256049007, 1256049007, 1256049007, 0, 4.5, 0.86, 5.36, '3e42e4c61553eaa5bd446111fac6bb22', '', 'Group-Office Beheerder', 'Geachte heer Beheerder', NULL, 'fcncv', 'vnv', 'vnv', 'vnc', 'vnnvc', 'NL', '', 'webmaster@example.com', '', 73, '', '', 0, '', NULL, '0', 162),
(24, 41, 11, 1, 1, '2009-000015', '', 0, 0, 1256050481, 1256050481, 1256050481, 1256050481, 0, 3, 0.57, 3.57, '4a5bc7591408d052fe6924bcd8110ed4', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', NULL, 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'info@intermesh.nl', '', 73, '', '', 0, '', NULL, '0', 163),
(25, 41, 11, 1, 1, '2009-000016', '', 0, 0, 1256050700, 1256050700, 1256050700, 1256050700, 0, 0, 0, 0, 'd4f75e545d8153f068c488339f416f54', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', NULL, 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'info@intermesh.nl', '', 73, '', '', 0, '', NULL, '0', 164),
(26, 41, 11, 1, 1, '2009-000017', '', 0, 0, 1256050720, 1256050720, 1256050720, 1256050720, 0, 3, 0.57, 3.57, 'bf4ad7351c91531091a60dd1825ffdeb', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', NULL, 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'info@intermesh.nl', '', 73, '', '', 0, '', NULL, '0', 165),
(27, 41, 11, 1, 1, '2009-000018', '', 0, 0, 1256050880, 1256050880, 1256050880, 1256050880, 0, 3, 0.57, 3.57, '4c3c793deb9945c0a20f1bd1ee377111', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', NULL, 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'info@intermesh.nl', '', 73, '', '', 0, '', NULL, '0', 166),
(28, 41, 11, 1, 1, '2009-000019', '', 0, 0, 1256050965, 1256050966, 1256050965, 1256050966, 0, 3, 0.57, 3.57, '313d620771b72a873b5724f531c1b539', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', NULL, 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'info@intermesh.nl', '', 73, '', '', 0, '', NULL, '0', 167),
(29, 41, 11, 1, 1, '2009-000020', '', 0, 0, 1256051082, 1256051082, 1256051082, 1256051082, 0, 3, 0.57, 3.57, '7027d5cc3a02d7d3ccd708193cbc9dfa', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', NULL, 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'info@intermesh.nl', '', 73, '', '', 0, '', NULL, '0', 168),
(30, 41, 11, 1, 1, '2009-000021', '', 0, 0, 1256106325, 1256106325, 1256106325, 1256106325, 0, 3, 0.57, 3.57, 'c0e36a0c70fc46df3eba1d337a048ca7', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', NULL, 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'info@intermesh.nl', '', 73, '', '', 0, '', NULL, '0', 169),
(31, 41, 11, 1, 1, '2009-000022', '', 0, 0, 1256106436, 1256106436, 1256106436, 1256106436, 0, 3, 0.57, 3.57, '1bbab56c297e51f618badb320ee5bc60', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', NULL, 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'info@intermesh.nl', '', 73, '', '', 0, '', NULL, '0', 170),
(32, 41, 11, 1, 1, '2009-000023', '', 0, 0, 1256106655, 1256106655, 1256106655, 1256106655, 0, 3, 0.57, 3.57, 'ee98fca67343496fc21885792e458252', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', NULL, 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'info@intermesh.nl', '', 73, '', '', 0, '', NULL, '0', 171),
(33, 41, 11, 1, 1, '2009-000024', '', 0, 0, 1256106727, 1256106727, 1256106727, 1256106727, 0, 3, 0.57, 3.57, '4288af4c60ea5f54f1ef26c0489875c5', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', NULL, 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'info@intermesh.nl', '', 73, '', '', 0, '', NULL, '0', 172),
(34, 41, 11, 1, 1, '2009-000025', '', 0, 0, 1256106778, 1256106778, 1256106778, 1256106778, 0, 3, 0.57, 3.57, 'a6cf279ded932f552d1d609503871b0a', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', NULL, 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'info@intermesh.nl', '', 73, '', '', 0, '', NULL, '0', 173),
(35, 41, 11, 1, 1, '2009-000026', '', 0, 0, 1256106827, 1256106827, 1256106827, 1256106827, 0, 3, 0.57, 3.57, 'c6c5663d63aed176cf446276e0362071', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', NULL, 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'info@intermesh.nl', '', 73, '', '', 0, '', NULL, '0', 174),
(36, 41, 11, 1, 1, '2009-000027', '', 0, 0, 1256107034, 1256107034, 1256107034, 1256107034, 0, 3, 0.57, 3.57, '0fb3df54234881a364f93ece78e9127b', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', NULL, 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'info@intermesh.nl', '', 73, '', '', 0, '', NULL, '0', 175),
(37, 41, 11, 1, 1, '2009-000028', '', 0, 0, 1256107076, 1256107076, 1256107076, 1256107076, 0, 3, 0.57, 3.57, 'a21de070e7e5b5ee2686f163e998fce9', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', NULL, 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'info@intermesh.nl', '', 73, '', '', 0, '', NULL, '0', 176),
(38, 41, 11, 1, 1, '2009-000029', '', 0, 0, 1256111276, 1256111276, 1256111276, 1256111276, 0, 3, 0.57, 3.57, '113f5483f16cba1068a4444f701feb14', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', NULL, 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'info@intermesh.nl', '', 73, '', '', 0, '', NULL, '0', 177),
(39, 44, 11, 1, 1, '2009-000030', '', 0, 0, 1256113255, 1256113280, 1256113255, 1256113255, 0, 3, 0.57, 3.57, '27e24b3680a84ad878323a1b0582493e', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', NULL, 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'info@intermesh.nl', '', 73, '', 'Teletik Safepay Card', 0, '', NULL, '0', 178),
(40, 9, 3, 1, 1, 'I2009000004', '', 0, 0, 1256113280, 1256113280, 1256113255, 1256113255, 0, 3, 0.57, 3.57, '9b92c9633d1a1ee2347b62a7978684b7', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', '', 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'info@intermesh.nl', '', 73, '', 'Teletik Safepay Card', 0, '', 0, '0', 179),
(41, 41, 11, 1, 1, '2009-000031', '', 0, 0, 1256116366, 1256116366, 1256116366, 1256116366, 0, 3, 0.57, 3.57, '57de3c8b18f7821d482912726c4d1c09', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', NULL, 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'info@intermesh.nl', '', 73, '', '', 0, '', NULL, '0', 180),
(42, 41, 11, 1, 1, '2009-000032', '', 0, 0, 1256118245, 1256118245, 1256118245, 1256118245, 0, 3, 0.57, 3.57, '5b4387de95491b5b2bf617de3fe5b37d', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', NULL, 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'info@intermesh.nl', '', 73, '', '', 0, '', NULL, '0', 181),
(43, 41, 11, 1, 1, '2009-000033', '', 0, 0, 1256118707, 1256118707, 1256118707, 1256118707, 0, 3, 0.57, 3.57, '996a7ba2729862f291345feb7446b1e7', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', NULL, 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'info@intermesh.nl', '', 73, '', '', 0, '', NULL, '0', 182),
(44, 41, 11, 1, 1, '2009-000034', '', 0, 0, 1256119281, 1256119281, 1256119281, 1256119281, 0, 3, 0.57, 3.57, '0cfb32110c48ebb713b0f2c5add2ddfa', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', NULL, 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'info@intermesh.nl', '', 73, '', '', 0, '', NULL, '0', 183),
(45, 41, 11, 1, 1, '2009-000035', '', 0, 0, 1256120853, 1256120853, 1256120853, 1256120853, 0, 3, 0.57, 3.57, 'd2645c9c887e66e42100ac316b9ebac4', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', NULL, 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'info@intermesh.nl', '', 73, '', '', 0, '', NULL, '0', 184),
(46, 41, 11, 1, 1, '2009-000036', '', 0, 0, 1256120913, 1256120913, 1256120913, 1256120913, 0, 4.5, 0.86, 5.36, '02ed5523d14a50fcf83b80703dcbdeb7', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', NULL, 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'info@intermesh.nl', '', 73, '', '', 0, '', NULL, '0', 185),
(47, 41, 11, 1, 1, '2009-000037', '', 0, 0, 1256121009, 1256121009, 1256121009, 1256121009, 0, 10, 1.9, 11.9, '64fe6c5f837a69db639ae839f5dc3b59', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', NULL, 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'info@intermesh.nl', '', 73, '', '', 0, '', NULL, '0', 186),
(48, 44, 11, 1, 1, '2009-000038', '', 0, 0, 1256123294, 1256123303, 1256123294, 1256123294, 0, 8, 1.52, 9.52, 'd3839c861b1041535e98f23dd0b8ee5e', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', NULL, 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'info@intermesh.nl', '', 73, '', 'Teletik Safepay Card', 0, '', NULL, '0', 187),
(49, 9, 3, 1, 1, 'I2009000005', '', 0, 0, 1256123303, 1256123303, 1256123294, 1256123294, 0, 8, 1.52, 9.52, '508078e110f77c124aab3da2b96adeca', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', '', 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'info@intermesh.nl', '', 73, '', 'Teletik Safepay Card', 0, '', 0, '0', 188),
(50, 44, 11, 1, 1, '2009-000039', '', 0, 0, 1256123852, 1256124394, 1256123852, 1256123852, 0, 9, 1.71, 10.71, '952c9b5cd95381a3e8af1b99ac98c530', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', NULL, 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'info@intermesh.nl', '', 73, '', '', 0, '', NULL, '0', 189),
(51, 9, 3, 1, 1, 'I2009000006', '', 0, 0, 1256124394, 1256124394, 1256123852, 1256123852, 0, 9, 1.71, 10.71, 'e8c35bbaadc80781431d1e807c49857f', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', '', 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'info@intermesh.nl', '', 73, '', '', 0, '', 0, '0', 190),
(52, 44, 11, 1, 1, '2009-000040', '', 0, 0, 1256124815, 1256124829, 1256124815, 1256124815, 0, 9, 1.71, 10.71, '4f8775352f3936951011848ae47b3f19', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', NULL, 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'info@intermesh.nl', '', 73, '', '', 0, '', NULL, '0', 191),
(53, 9, 3, 1, 1, 'I2009000007', '', 0, 0, 1256124829, 1256124829, 1256124815, 1256124815, 0, 9, 1.71, 10.71, '93e01da22462e5fbe2629c1fc7c43945', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', '', 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'info@intermesh.nl', '', 73, '', '', 0, '', 0, '0', 192),
(54, 41, 11, 1, 1, '2009-000041', '', 0, 0, 1256125172, 1256125172, 1256125172, 1256125172, 0, 0, 0, 0, '0da6eb818c0b37db5ac9449e3dad1c35', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', NULL, 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'w.vanbeusekom@gmail.com', '', 73, '', '', 0, '', NULL, '0', 193),
(55, 44, 11, 1, 1, '2009-000042', '', 0, 0, 1256125191, 1256125220, 1256125191, 1256125191, 0, 3, 0.57, 3.57, '1c493888d49eb266b2ec22c709691a55', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', NULL, 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'w.vanbeusekom@gmail.com', '', 73, '', '', 0, '', NULL, '0', 194),
(56, 9, 3, 1, 1, 'I2009000008', '', 0, 0, 1256125220, 1256125220, 1256125191, 1256125191, 0, 3, 0.57, 3.57, '300d19c43f5909f982b9c2a75f2fd460', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', '', 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'w.vanbeusekom@gmail.com', '', 73, '', '', 0, '', 0, '0', 195),
(57, 44, 11, 1, 1, '2009-000043', '', 0, 0, 1256126402, 1256126562, 1256126402, 1256126402, 0, 9, 1.71, 10.71, 'd26247c82f9df6f75df5512f5cde0aeb', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', NULL, 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'w.vanbeusekom@gmail.com', '', 73, '', '', 0, '', NULL, '0', 196),
(58, 9, 3, 1, 1, 'I2009000009', '', 0, 0, 1256126562, 1256126562, 1256126402, 1256126402, 0, 9, 1.71, 10.71, '94f8e88f8d8bc71126ad4f9365363256', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', '', 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'w.vanbeusekom@gmail.com', '', 73, '', '', 0, '', 0, '0', 197),
(59, 44, 11, 1, 1, '2009-000044', '', 0, 0, 1256126628, 1256126659, 1256126628, 1256126628, 0, 3, 0.57, 3.57, '8f8215a94cfc79f268e408a43e1f7f7e', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', NULL, 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'w.vanbeusekom@gmail.com', '', 73, '', '', 0, '', NULL, '0', 198),
(60, 9, 3, 1, 1, 'I2009000010', '', 0, 0, 1256126659, 1256126659, 1256126628, 1256126628, 0, 3, 0.57, 3.57, 'b97efd1bab8ee6a3f1c9dfe247b21add', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', '', 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'w.vanbeusekom@gmail.com', '', 73, '', '', 0, '', 0, '0', 199),
(61, 44, 11, 1, 1, '2009-000045', '', 0, 0, 1256127055, 1256127084, 1256127055, 1256127055, 0, 3, 0.57, 3.57, 'cbc272bf37afff571020002d63cc7d01', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', NULL, 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'w.vanbeusekom@gmail.com', '', 73, '', 'Teletik Safepay Card', 0, '', NULL, '0', 200),
(62, 9, 3, 1, 1, 'I2009000011', '', 0, 0, 1256127084, 1256127084, 1256127055, 1256127055, 0, 3, 0.57, 3.57, '3d959b9123db06fa4a2f488ef9d2bcc1', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', '', 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'w.vanbeusekom@gmail.com', '', 73, '', 'Teletik Safepay Card', 0, '', 0, '0', 201),
(63, 44, 11, 1, 1, '2009-000046', '', 0, 0, 1256127721, 1256127736, 1256127721, 1256127721, 0, 6, 1.14, 7.14, 'dd82df2a8b8ec0a16fdaab97dc94fdb4', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', NULL, 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'w.vanbeusekom@gmail.com', '', 73, '', 'Rabobank InternetKassa', 0, '', NULL, '0', 202),
(64, 9, 3, 1, 1, 'I2009000012', '', 0, 0, 1256127736, 1256127736, 1256127721, 1256127721, 0, 6, 1.14, 7.14, '0949e0e01d79bbc763495368606d2e17', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', '', 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'w.vanbeusekom@gmail.com', '', 73, '', '', 0, '', 0, '0', 203),
(65, 44, 11, 1, 1, '2009-000047', '', 0, 0, 1256130713, 1256130732, 1256130713, 1256130713, 0, 9, 1.71, 10.71, '083c4e5911ff65d4560fceff524afa56', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', NULL, 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'w.vanbeusekom@gmail.com', '', 73, '', 'Rabobank InternetKassa', 0, '', NULL, '0', 204),
(66, 9, 3, 1, 1, 'I2009000013', '', 0, 0, 1256130732, 1256130732, 1256130713, 1256130713, 0, 9, 1.71, 10.71, '0c8d532911e139019b462371e2e90764', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', '', 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'w.vanbeusekom@gmail.com', '', 73, '', '', 0, '', 0, '0', 205),
(67, 0, 11, 1, 1, '', '', 0, 0, 1256130773, 1256130773, 1256130713, 1256130713, 0, 9, 1.71, 10.71, '56e2e269117d46846f8573c103b0fb27', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', '', 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'w.vanbeusekom@gmail.com', '', 73, '', '', 0, 'Hack attempt detected: orderID=2009-000047,currency=EUR,amount=10.71,PM=iDEAL,ACCEPTANCE=0000000000,', 0, '0', 206),
(68, 44, 11, 1, 1, '2009-000048', '', 0, 0, 1256133235, 1256133496, 1256133235, 1256133235, 0, 3, 0.57, 3.57, '36f86ae170e77d14b3020daa22d8accd', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', NULL, 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'w.vanbeusekom@gmail.com', '', 73, '', 'Rabobank InternetKassa', 0, '', NULL, '0', 207),
(69, 9, 3, 1, 1, 'I2009000014', '', 0, 0, 1256133496, 1256133496, 1256133235, 1256133235, 0, 3, 0.57, 3.57, '0529d13306069a2770db43071811084b', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', '', 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'w.vanbeusekom@gmail.com', '', 73, '', '', 0, '', 0, '0', 208),
(70, 41, 11, 1, 1, '2009-000049', '', 0, 0, 1256133657, 1256133657, 1256133657, 1256133657, 0, 20, 3.8, 23.8, '282661db23c83ec89a077483141f36a2', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', NULL, 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'w.vanbeusekom@gmail.com', '', 73, '', '', 0, '', NULL, '0', 209),
(71, 41, 11, 1, 1, '2009-000050', '', 0, 0, 1256134025, 1256134025, 1256134025, 1256134025, 0, 20, 3.8, 23.8, '13fdc8653a424a8518990df1b0a9534f', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', NULL, 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'w.vanbeusekom@gmail.com', '', 73, '', '', 0, '', NULL, '0', 210),
(72, 41, 11, 1, 1, '2009-000051', '', 0, 0, 1256136267, 1256136267, 1256136267, 1256136267, 0, 20, 3.8, 23.8, '9ff375c6a2f269e714a5ea0a12d820f6', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', NULL, 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'w.vanbeusekom@gmail.com', '', 73, '', '', 0, '', NULL, '0', 211),
(73, 41, 11, 1, 1, '2009-000052', '', 0, 0, 1256136294, 1256136294, 1256136294, 1256136294, 0, 20, 3.8, 23.8, '755ffa397f3fe5ff84bfe7fe601e3e20', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', NULL, 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'w.vanbeusekom@gmail.com', '', 73, '', '', 0, '', NULL, '0', 212),
(74, 46, 11, 1, 1, '2009-000053', '', 0, 0, 1256136692, 1256136704, 1256136692, 1256136692, 0, 20, 3.8, 23.8, '5ce162203e28fd9321ff142a131cbc9d', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', NULL, 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'w.vanbeusekom@gmail.com', '', 73, '', 'Rabobank InternetKassa', 0, '', NULL, '0', 213),
(76, 46, 11, 1, 1, '2009-000054', '', 0, 0, 1256136832, 1256136859, 1256136832, 1256136832, 0, 20, 3.8, 23.8, '13eb94015d50513ef011c6c7c1d56898', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', NULL, 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'w.vanbeusekom@gmail.com', '', 73, '', 'Rabobank InternetKassa', 0, '', NULL, '0', 214),
(77, 9, 9, 1, 1, '2009-000003', '', 0, 0, 1256136859, 1256136859, 1256136832, 1256136832, 0, 20, 3.8, 23.8, 'e714cfeab2393cd958eb5d34a7a99d76', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', '', 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'w.vanbeusekom@gmail.com', '', 73, '', '', 0, '', 0, '0', 215),
(78, 46, 11, 1, 1, '2009-000055', '', 0, 0, 1256136928, 1256136952, 1256136928, 1256136928, 0, 20, 3.8, 23.8, '6d7eb8f96a516dce8f252fdd33aaf05b', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', NULL, 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'w.vanbeusekom@gmail.com', '', 73, '', 'Rabobank InternetKassa', 0, '', NULL, '0', 216),
(79, 9, 9, 1, 1, '2009-000004', '', 0, 0, 1256136952, 1256136952, 1256136928, 1256136928, 0, 20, 3.8, 23.8, '1785d8cec3f5f3154b25c153de6bc037', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', '', 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'w.vanbeusekom@gmail.com', '', 73, '', '', 0, '', 0, '0', 217),
(80, 41, 11, 1, 1, '2009-000056', '', 0, 0, 1256137002, 1256137002, 1256137002, 1256137002, 0, 20, 3.8, 23.8, '2b240bcff83a844d1b09213666cac0e8', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', NULL, 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'w.vanbeusekom@gmail.com', '', 73, '', '', 0, '', NULL, '0', 218),
(81, 44, 11, 1, 1, '2009-000057', '', 0, 0, 1256193361, 1256193377, 1256193361, 1256193362, 0, 10, 1.9, 11.9, '4fd4868f771f80b1ea58daf53525cba9', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', NULL, 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'w.vanbeusekom@gmail.com', '', 73, '', 'Rabobank InternetKassa', 0, '', NULL, '0', 219),
(82, 9, 9, 1, 1, '2009-000005', '', 0, 0, 1256193377, 1256193377, 1256193361, 1256193362, 0, 10, 1.9, 11.9, 'fe8b6491c888b01474287e2dabf62162', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', '', 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'w.vanbeusekom@gmail.com', '', 73, '', '', 0, '', 0, '0', 220),
(83, 47, 11, 1, 1, '2009-000058', '', 0, 0, 1256193526, 1256193542, 1256193526, 1256193526, 0, 20, 3.8, 23.8, '8583c394fe2f71984d74e7bd81cd7c26', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', NULL, 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'w.vanbeusekom@gmail.com', '', 73, '', 'Rabobank InternetKassa', 0, '', NULL, '0', 221),
(84, 9, 9, 1, 1, '2009-000006', '', 0, 0, 1256193542, 1256193542, 1256193526, 1256193526, 0, 20, 3.8, 23.8, 'cce10e58abe87183ed56a6d70246b39e', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', '', 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'w.vanbeusekom@gmail.com', '', 73, '', '', 0, '', 0, '0', 222),
(85, 46, 11, 1, 1, '2009-000059', '', 0, 0, 1256196160, 1256196218, 1256196160, 1256196160, 0, 9, 1.71, 10.71, 'f2fe04a50be08edbc55799eec535fb34', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', NULL, 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'w.vanbeusekom@gmail.com', '', 73, '', 'Rabobank InternetKassa', 0, '', NULL, '0', 223),
(86, 9, 9, 1, 1, '2009-000007', '', 0, 0, 1256196218, 1256196218, 1256196160, 1256196160, 0, 9, 1.71, 10.71, '13d18d9a39998909551d67a2d090760c', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', '', 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'w.vanbeusekom@gmail.com', '', 73, '', '', 0, '', 0, '0', 224),
(87, 46, 11, 1, 1, '2009-000060', '', 0, 0, 1256196291, 1256196345, 1256196291, 1256196291, 0, 4.5, 0.86, 5.36, '38db36156e879703c69601e68018ad23', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', NULL, 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'w.vanbeusekom@gmail.com', '', 73, '', 'Rabobank InternetKassa', 0, '', NULL, '0', 225),
(88, 9, 9, 1, 1, '2009-000008', '', 0, 0, 1256196345, 1256196345, 1256196291, 1256196291, 0, 4.5, 0.86, 5.36, 'e6b2f6149e911592a87a8b16c1ec820d', '', 'Waldemar de Beursekond', 'Geachte heer Beheerder', '', 'Reitscheweg', '37', '5933BX', 'Den Bosch', 'Gelderland', 'NL', '', 'w.vanbeusekom@gmail.com', '', 73, '', '', 0, '', 0, '0', 226);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `bs_order_statuses`
--

CREATE TABLE IF NOT EXISTS `bs_order_statuses` (
  `id` int(11) NOT NULL default '0',
  `book_id` int(11) NOT NULL default '0',
  `max_age` int(11) NOT NULL default '0',
  `payment_required` enum('0','1') NOT NULL default '0',
  `remove_from_stock` enum('0','1') NOT NULL,
  `read_only` enum('0','1') NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `book_id` (`book_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `bs_order_statuses`
--

INSERT INTO `bs_order_statuses` (`id`, `book_id`, `max_age`, `payment_required`, `remove_from_stock`, `read_only`) VALUES
(1, 1, 0, '0', '0', '0'),
(2, 1, 0, '0', '0', '0'),
(3, 1, 0, '0', '0', '0'),
(4, 2, 0, '0', '0', '0'),
(5, 2, 0, '0', '0', '0'),
(6, 2, 0, '0', '0', '0'),
(7, 3, 0, '0', '0', '0'),
(8, 3, 0, '0', '0', '0'),
(9, 3, 0, '0', '0', '0'),
(10, 3, 0, '0', '0', '0'),
(11, 2, 0, '0', '0', '0'),
(12, 2, 0, '0', '0', '0'),
(13, 4, 0, '0', '0', '0'),
(14, 4, 0, '0', '0', '0'),
(15, 4, 0, '0', '0', '0'),
(16, 4, 0, '0', '0', '0'),
(17, 5, 0, '0', '0', '0'),
(18, 5, 0, '0', '0', '0'),
(19, 5, 0, '0', '0', '0'),
(20, 5, 0, '0', '0', '0'),
(21, 6, 0, '0', '0', '0'),
(22, 6, 0, '0', '0', '0'),
(23, 6, 0, '0', '0', '0'),
(24, 6, 0, '0', '0', '0'),
(37, 10, 0, '0', '0', '0'),
(38, 10, 0, '0', '0', '0'),
(39, 10, 0, '0', '0', '0'),
(40, 10, 0, '0', '0', '0'),
(33, 9, 0, '0', '0', '0'),
(34, 9, 0, '0', '0', '0'),
(35, 9, 0, '0', '0', '0'),
(36, 9, 0, '0', '0', '0'),
(41, 11, 0, '0', '0', '0'),
(42, 11, 0, '0', '0', '0'),
(43, 11, 0, '0', '0', '0'),
(44, 11, 0, '0', '0', '0'),
(45, 11, 0, '0', '0', '0'),
(46, 11, 0, '0', '0', '0'),
(47, 11, 0, '0', '0', '0'),
(48, 11, 0, '0', '0', '0');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `bs_order_status_history`
--

CREATE TABLE IF NOT EXISTS `bs_order_status_history` (
  `id` int(11) NOT NULL default '0',
  `order_id` int(11) NOT NULL default '0',
  `status_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `ctime` int(11) NOT NULL default '0',
  `notified` enum('0','1') NOT NULL default '0',
  `notification_email` varchar(255) default NULL,
  `comments` text,
  PRIMARY KEY  (`id`),
  KEY `order_id` (`order_id`,`status_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `bs_order_status_history`
--

INSERT INTO `bs_order_status_history` (`id`, `order_id`, `status_id`, `user_id`, `ctime`, `notified`, `notification_email`, `comments`) VALUES
(1, 2, 5, 1, 1255610311, '0', 'billing/notifications/2/2/1.eml', NULL),
(2, 2, 4, 1, 1255610362, '0', 'billing/notifications/2/2/2.eml', NULL),
(3, 4, 5, 1, 1255610733, '0', 'billing/notifications/2/4/3.eml', NULL),
(4, 5, 5, 1, 1255611055, '0', 'billing/notifications/2/5/4.eml', NULL),
(5, 5, 4, 1, 1255611070, '0', 'billing/notifications/2/5/5.eml', NULL),
(6, 5, 32, 1, 1255675623, '0', 'billing/notifications/2/5/6.eml', NULL),
(7, 8, 33, 1, 1255958630, '0', 'billing/notifications/9/8/7.eml', NULL),
(8, 9, 33, 1, 1255959006, '0', 'billing/notifications/9/9/8.eml', NULL),
(9, 10, 41, 1, 1256036740, '0', 'billing/notifications/11/10/9.eml', NULL),
(10, 11, 41, 1, 1256036966, '0', 'billing/notifications/11/11/10.eml', NULL),
(11, 12, 41, 1, 1256037664, '0', 'billing/notifications/11/12/11.eml', NULL),
(12, 13, 41, 1, 1256037706, '0', 'billing/notifications/11/13/12.eml', NULL),
(13, 14, 41, 1, 1256044841, '0', 'billing/notifications/11/14/13.eml', NULL),
(14, 15, 41, 1, 1256045109, '0', 'billing/notifications/11/15/14.eml', NULL),
(15, 16, 41, 1, 1256045123, '0', 'billing/notifications/11/16/15.eml', NULL),
(16, 17, 41, 1, 1256045424, '0', 'billing/notifications/11/17/16.eml', NULL),
(17, 18, 41, 1, 1256045806, '0', 'billing/notifications/11/18/17.eml', NULL),
(18, 19, 41, 1, 1256045833, '0', 'billing/notifications/11/19/18.eml', NULL),
(19, 20, 41, 1, 1256045892, '0', 'billing/notifications/11/20/19.eml', NULL),
(20, 21, 41, 1, 1256045928, '0', 'billing/notifications/11/21/20.eml', NULL),
(21, 22, 41, 1, 1256048092, '0', 'billing/notifications/11/22/21.eml', NULL),
(22, 23, 41, 1, 1256049007, '0', 'billing/notifications/11/23/22.eml', NULL),
(23, 24, 41, 1, 1256050481, '0', 'billing/notifications/11/24/23.eml', NULL),
(24, 25, 41, 1, 1256050700, '0', 'billing/notifications/11/25/24.eml', NULL),
(25, 26, 41, 1, 1256050720, '0', 'billing/notifications/11/26/25.eml', NULL),
(26, 27, 41, 1, 1256050880, '0', 'billing/notifications/11/27/26.eml', NULL),
(27, 28, 41, 1, 1256050966, '0', 'billing/notifications/11/28/27.eml', NULL),
(28, 29, 41, 1, 1256051082, '0', 'billing/notifications/11/29/28.eml', NULL),
(29, 30, 41, 1, 1256106325, '0', 'billing/notifications/11/30/29.eml', NULL),
(30, 31, 41, 1, 1256106436, '0', 'billing/notifications/11/31/30.eml', NULL),
(31, 32, 41, 1, 1256106655, '0', 'billing/notifications/11/32/31.eml', NULL),
(32, 33, 41, 1, 1256106727, '0', 'billing/notifications/11/33/32.eml', NULL),
(33, 34, 41, 1, 1256106778, '0', 'billing/notifications/11/34/33.eml', NULL),
(34, 35, 41, 1, 1256106827, '0', 'billing/notifications/11/35/34.eml', NULL),
(35, 36, 41, 1, 1256107034, '0', 'billing/notifications/11/36/35.eml', NULL),
(36, 37, 41, 1, 1256107076, '0', 'billing/notifications/11/37/36.eml', NULL),
(37, 38, 41, 1, 1256111276, '0', 'billing/notifications/11/38/37.eml', NULL),
(38, 39, 41, 1, 1256113255, '0', 'billing/notifications/11/39/38.eml', NULL),
(39, 39, 44, 1, 1256113280, '0', 'billing/notifications/11/39/39.eml', NULL),
(40, 41, 41, 1, 1256116366, '0', 'billing/notifications/11/41/40.eml', NULL),
(41, 42, 41, 1, 1256118245, '0', 'billing/notifications/11/42/41.eml', NULL),
(42, 43, 41, 1, 1256118707, '0', 'billing/notifications/11/43/42.eml', NULL),
(43, 44, 41, 1, 1256119281, '0', 'billing/notifications/11/44/43.eml', NULL),
(44, 45, 41, 1, 1256120853, '0', 'billing/notifications/11/45/44.eml', NULL),
(45, 46, 41, 1, 1256120913, '0', 'billing/notifications/11/46/45.eml', NULL),
(46, 47, 41, 1, 1256121009, '0', 'billing/notifications/11/47/46.eml', NULL),
(47, 48, 41, 1, 1256123294, '0', 'billing/notifications/11/48/47.eml', NULL),
(48, 48, 44, 1, 1256123303, '0', 'billing/notifications/11/48/48.eml', NULL),
(49, 50, 41, 1, 1256123852, '0', 'billing/notifications/11/50/49.eml', NULL),
(50, 50, 44, 1, 1256124394, '0', 'billing/notifications/11/50/50.eml', NULL),
(51, 52, 41, 1, 1256124815, '0', 'billing/notifications/11/52/51.eml', NULL),
(52, 52, 44, 1, 1256124829, '0', 'billing/notifications/11/52/52.eml', NULL),
(53, 54, 41, 1, 1256125172, '0', 'billing/notifications/11/54/53.eml', NULL),
(54, 55, 41, 1, 1256125191, '0', 'billing/notifications/11/55/54.eml', NULL),
(55, 55, 44, 1, 1256125220, '0', 'billing/notifications/11/55/55.eml', NULL),
(56, 57, 41, 1, 1256126402, '0', 'billing/notifications/11/57/56.eml', NULL),
(57, 57, 44, 1, 1256126562, '0', 'billing/notifications/11/57/57.eml', NULL),
(58, 59, 41, 1, 1256126628, '0', 'billing/notifications/11/59/58.eml', NULL),
(59, 59, 44, 1, 1256126659, '0', 'billing/notifications/11/59/59.eml', NULL),
(60, 61, 41, 1, 1256127055, '0', 'billing/notifications/11/61/60.eml', NULL),
(61, 61, 44, 1, 1256127084, '0', 'billing/notifications/11/61/61.eml', NULL),
(62, 63, 41, 1, 1256127721, '0', 'billing/notifications/11/63/62.eml', NULL),
(63, 63, 44, 1, 1256127736, '0', 'billing/notifications/11/63/63.eml', NULL),
(64, 65, 41, 1, 1256130713, '0', 'billing/notifications/11/65/64.eml', NULL),
(65, 65, 44, 1, 1256130732, '0', 'billing/notifications/11/65/65.eml', NULL),
(66, 68, 41, 1, 1256133235, '0', 'billing/notifications/11/68/66.eml', NULL),
(67, 68, 44, 1, 1256133496, '0', 'billing/notifications/11/68/67.eml', NULL),
(68, 70, 41, 1, 1256133657, '0', 'billing/notifications/11/70/68.eml', NULL),
(69, 71, 41, 1, 1256134025, '0', 'billing/notifications/11/71/69.eml', NULL),
(70, 72, 41, 1, 1256136267, '0', 'billing/notifications/11/72/70.eml', NULL),
(71, 73, 41, 1, 1256136294, '0', 'billing/notifications/11/73/71.eml', NULL),
(72, 74, 41, 1, 1256136692, '0', 'billing/notifications/11/74/72.eml', NULL),
(73, 74, 46, 1, 1256136704, '0', 'billing/notifications/11/74/73.eml', NULL),
(74, 76, 41, 1, 1256136832, '0', 'billing/notifications/11/76/74.eml', NULL),
(75, 76, 46, 1, 1256136859, '0', 'billing/notifications/11/76/75.eml', NULL),
(76, 78, 41, 1, 1256136928, '0', 'billing/notifications/11/78/76.eml', NULL),
(77, 78, 46, 1, 1256136952, '0', 'billing/notifications/11/78/77.eml', NULL),
(78, 80, 41, 1, 1256137002, '0', 'billing/notifications/11/80/78.eml', NULL),
(79, 81, 41, 1, 1256193362, '0', 'billing/notifications/11/81/79.eml', NULL),
(80, 81, 44, 1, 1256193377, '0', 'billing/notifications/11/81/80.eml', NULL),
(81, 83, 41, 1, 1256193526, '0', 'billing/notifications/11/83/81.eml', NULL),
(82, 83, 47, 1, 1256193542, '0', 'billing/notifications/11/83/82.eml', NULL),
(83, 85, 41, 1, 1256196160, '0', 'billing/notifications/11/85/83.eml', NULL),
(84, 85, 46, 1, 1256196218, '0', 'billing/notifications/11/85/84.eml', NULL),
(85, 87, 41, 1, 1256196291, '0', 'billing/notifications/11/87/85.eml', NULL),
(86, 87, 46, 1, 1256196345, '0', 'billing/notifications/11/87/86.eml', NULL);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `bs_products`
--

CREATE TABLE IF NOT EXISTS `bs_products` (
  `id` int(11) NOT NULL default '0',
  `sort_order` int(11) NOT NULL default '0',
  `category_id` int(11) NOT NULL default '0',
  `image` varchar(255) default NULL,
  `cost_price` double NOT NULL default '0',
  `list_price` double NOT NULL default '0',
  `vat` double NOT NULL default '0',
  `total_price` double NOT NULL default '0',
  `supplier_company_id` int(11) NOT NULL default '0',
  `supplier_product_id` varchar(50) default NULL,
  `allow_bonus_points` enum('0','1') NOT NULL default '0',
  `special` enum('0','1') NOT NULL default '0',
  `special_list_price` double NOT NULL default '0',
  `special_total_price` double NOT NULL default '0',
  `charge_shipping_costs` enum('0','1') NOT NULL default '0',
  `stock` int(11) NOT NULL default '0',
  `bonus_points` int(11) NOT NULL default '0',
  `required_products` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  KEY `category_id` (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `bs_products`
--

INSERT INTO `bs_products` (`id`, `sort_order`, `category_id`, `image`, `cost_price`, `list_price`, `vat`, `total_price`, `supplier_company_id`, `supplier_product_id`, `allow_bonus_points`, `special`, `special_list_price`, `special_total_price`, `charge_shipping_costs`, `stock`, `bonus_points`, `required_products`) VALUES
(291, 0, 156, 'NULL', 1.5, 3, 19, 3.57, 0, 'NULL', '0', '0', 0, 0, '0', 0, 0, 'NULL'),
(290, 0, 156, 'NULL', 3, 8, 19, 9.52, 0, 'NULL', '0', '0', 0, 0, '0', 0, 0, 'NULL'),
(289, 0, 156, 'NULL', 2, 5, 19, 5.95, 0, 'NULL', '0', '0', 0, 0, '0', 0, 0, 'NULL'),
(288, 0, 156, 'NULL', 1.5, 4.5, 19, 5.36, 0, 'NULL', '0', '0', 0, 0, '0', 0, 0, 'NULL'),
(287, 0, 160, 'NULL', 1, 5, 19, 5.95, 0, 'NULL', '0', '0', 0, 0, '0', 0, 0, 'NULL'),
(286, 0, 160, 'NULL', 5, 9, 19, 10.71, 0, 'NULL', '0', '0', 0, 0, '0', 0, 0, 'NULL'),
(285, 0, 159, 'NULL', 5, 20, 19, 23.8, 0, 'NULL', '0', '0', 0, 0, '0', 0, 0, 'NULL');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `bs_product_categories`
--

CREATE TABLE IF NOT EXISTS `bs_product_categories` (
  `id` int(11) NOT NULL default '0',
  `sort_order` int(11) NOT NULL default '0',
  `parent_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `bs_product_categories`
--

INSERT INTO `bs_product_categories` (`id`, `sort_order`, `parent_id`) VALUES
(160, 0, 158),
(159, 0, 158),
(158, 0, 155),
(157, 0, 156),
(156, 0, 155),
(155, 0, 0);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `bs_product_languages`
--

CREATE TABLE IF NOT EXISTS `bs_product_languages` (
  `language_id` int(11) NOT NULL default '0',
  `product_id` int(11) NOT NULL default '0',
  `name` varchar(100) default NULL,
  `description` text,
  `short_description` varchar(255) default NULL,
  PRIMARY KEY  (`language_id`,`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `bs_product_languages`
--

INSERT INTO `bs_product_languages` (`language_id`, `product_id`, `name`, `description`, `short_description`) VALUES
(1, 291, 'Transformator', 'Authentieke transformator. Stamt nog uit de tijd dat\nwisselstroom 220V was en niet 230V.', 'Authentieke transformator.'),
(1, 290, 'Telefoon', 'Prachtige antieke telefoon van koper en onyx. Zoals\nte zien is op de foto, zijn er wat beschadigingen. De telefoon werkt echter\nprima.', 'Antieke telefoon van koper en onyx.'),
(1, 289, 'Radio', 'Functionerende radio uit 1933 van bakeliet. Onbeschadigd\nen in prima werkende staat. U kunt uw favoriete zenders met een charmante jaren ''30\ngeluid beluisteren.', 'Functionerende radio uit 1933 van bakeliet.'),
(1, 288, 'Televisie', 'Deze zwart-wit televisie is zeer robuust. Na 35 jaar\ndoet deze retro televisie het nog uitstekend.', 'Retro televisie.'),
(1, 285, 'Postduif', 'De bejaarde postduif Frans heeft ons jaren trouwe\ndienst bewezen en verdient nu een goede oude duivendag. Voor de echte\nliefhebber.', 'Bejaarde postduif.'),
(1, 286, 'Struisvogel', 'Vanwege de sloop van onze kinderboerderij verkopen\nwij deze struisvogel Barry. Hij is 5 jaar oud en al die tijd een publiekslieveling\ngeweest.', 'Prachtige struisvogel.'),
(1, 287, 'Kip', 'Wij hebben een 3 jaar oude legkip. Elke maand legt\nzij circa 10 eieren, lekker voor bij het ontbijt. Vanwege verhuizing willen wij\nhaar verkopen.', 'Drie jaar oude legkip.');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `bs_status_languages`
--

CREATE TABLE IF NOT EXISTS `bs_status_languages` (
  `language_id` int(11) NOT NULL default '0',
  `status_id` int(11) NOT NULL default '0',
  `name` varchar(50) default NULL,
  `email_subject` varchar(100) default NULL,
  `email_template` longtext,
  `screen_template` text,
  `pdf_template_id` int(11) NOT NULL,
  PRIMARY KEY  (`language_id`,`status_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `bs_status_languages`
--

INSERT INTO `bs_status_languages` (`language_id`, `status_id`, `name`, `email_subject`, `email_template`, `screen_template`, `pdf_template_id`) VALUES
(1, 1, 'Verzonden', 'Uw Offerte heeft status Verzonden', 'Message-ID: <1253802195.4abb80d30f69c@localhost>\r\nDate: Thu, 24 Sep 2009 16:23:15 +0200\r\nFrom: \r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=utf-8\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n\r\n%customer_salutation%,<br />\r\n<br />\r\nUw Offerte is in status=\r\n Verzonden.<br />\r\n<br />\r\nMet vriendelijke groet,<br />\r\n<br /=\r\n>\r\nEen bedrijf', '%customer_salutation%,<br />\n<br />\nUw Offerte is in status Verzonden.<br />\n<br />\nMet vriendelijke groet,<br />\n<br />\nEen bedrijf', 2),
(1, 2, 'Geaccepteerd', 'Uw Offerte heeft status Geaccepteerd', 'Message-ID: <1253802195.4abb80d315921@localhost>\r\nDate: Thu, 24 Sep 2009 16:23:15 +0200\r\nFrom: \r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=utf-8\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n\r\n%customer_salutation%,<br />\r\n<br />\r\nUw Offerte is in status=\r\n Geaccepteerd.<br />\r\n<br />\r\nMet vriendelijke groet,<br />=\r\n\r\n<br />\r\nEen bedrijf', '%customer_salutation%,<br />\n<br />\nUw Offerte is in status Geaccepteerd.<br />\n<br />\nMet vriendelijke groet,<br />\n<br />\nEen bedrijf', 2),
(1, 3, 'Verloren', 'Uw Offerte heeft status Verloren', 'Message-ID: <1253802195.4abb80d31aed5@localhost>\r\nDate: Thu, 24 Sep 2009 16:23:15 +0200\r\nFrom: \r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=utf-8\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n\r\n%customer_salutation%,<br />\r\n<br />\r\nUw Offerte is in status=\r\n Verloren.<br />\r\n<br />\r\nMet vriendelijke groet,<br />\r\n<br=\r\n />\r\nEen bedrijf', '%customer_salutation%,<br />\n<br />\nUw Offerte is in status Verloren.<br />\n<br />\nMet vriendelijke groet,<br />\n<br />\nEen bedrijf', 2),
(1, 4, 'In rekening gebracht', 'Uw Order heeft status Bezig', 'Message-ID: <1253802195.4abb80d31d9e6@localhost>\r\nDate: Thu, 24 Sep 2009 16:23:15 +0200\r\nFrom: \r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=utf-8\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n\r\n%customer_salutation%,<br />\r\n<br />\r\nUw Order is in status B=\r\nezig.<br />\r\n<br />\r\nMet vriendelijke groet,<br />\r\n<br /=\r\n>\r\nEen bedrijf', '%customer_salutation%,<br />\n<br />\nUw Order is in status Bezig.<br />\n<br />\nMet vriendelijke groet,<br />\n<br />\nEen bedrijf', 0),
(1, 5, 'Pending', 'Uw Order heeft status Afgeleverd', 'Message-ID: <1253802195.4abb80d31f080@localhost>\r\nDate: Thu, 24 Sep 2009 16:23:15 +0200\r\nFrom: \r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=utf-8\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n\r\n%customer_salutation%,<br />\r\n<br />\r\nUw Order is in status A=\r\nfgeleverd.<br />\r\n<br />\r\nMet vriendelijke groet,<br />\r\n<br=\r\n />\r\nEen bedrijf', '%customer_salutation%,<br />\n<br />\nUw Order is in status Afgeleverd.<br />\n<br />\nMet vriendelijke groet,<br />\n<br />\nEen bedrijf', 3),
(1, 6, 'Wachtend op betaling', 'Uw Order heeft status In rekening gebracht', 'Message-ID: <1253802195.4abb80d3207e0@localhost>\r\nDate: Thu, 24 Sep 2009 16:23:15 +0200\r\nFrom: \r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=utf-8\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n\r\n%customer_salutation%,<br />\r\n<br />\r\nUw Order is in status I=\r\nn rekening gebracht.<br />\r\n<br />\r\nMet vriendelijke groet,<br=\r\n />\r\n<br />\r\nEen bedrijf', '%customer_salutation%,<br />\n<br />\nUw Order is in status In rekening gebracht.<br />\n<br />\nMet vriendelijke groet,<br />\n<br />\nEen bedrijf', 3),
(1, 7, 'Wacht op betaling', 'Uw Factuur heeft status Wacht op betaling', 'Message-ID: <1253802195.4abb80d3220fc@localhost>\r\nDate: Thu, 24 Sep 2009 16:23:15 +0200\r\nFrom: \r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=utf-8\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n\r\n%customer_salutation%,<br />\r\n<br />\r\nUw Factuur is in status=\r\n Wacht op betaling.<br />\r\n<br />\r\nMet vriendelijke groet,<br =\r\n/>\r\n<br />\r\nEen bedrijf', '%customer_salutation%,<br />\n<br />\nUw Order is in status In rekening gebracht.<br />\n<br />\nMet vriendelijke groet,<br />\n<br />\nEen bedrijf', 1),
(1, 8, 'Herinnering verzonden', 'Uw Factuur heeft status Herinnering verzonden', 'Message-ID: <1253802195.4abb80d3237be@localhost>\r\nDate: Thu, 24 Sep 2009 16:23:15 +0200\r\nFrom: \r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=utf-8\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n\r\n%customer_salutation%,<br />\r\n<br />\r\nUw Factuur is in status=\r\n Herinnering verzonden.<br />\r\n<br />\r\nMet vriendelijke groet,<br =\r\n/>\r\n<br />\r\nEen bedrijf', '%customer_salutation%,<br />\n<br />\nUw Order is in status In rekening gebracht.<br />\n<br />\nMet vriendelijke groet,<br />\n<br />\nEen bedrijf', 1),
(1, 9, 'Betaald', 'Uw Factuur heeft status Betaald', 'Message-ID: <1253802195.4abb80d32634d@localhost>\r\nDate: Thu, 24 Sep 2009 16:23:15 +0200\r\nFrom: \r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=utf-8\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n\r\n%customer_salutation%,<br />\r\n<br />\r\nUw Factuur is in status=\r\n Betaald.<br />\r\n<br />\r\nMet vriendelijke groet,<br />\r\n<br /=\r\n>\r\nEen bedrijf', '%customer_salutation%,<br />\n<br />\nUw Order is in status In rekening gebracht.<br />\n<br />\nMet vriendelijke groet,<br />\n<br />\nEen bedrijf', 1),
(1, 10, 'Creditnota', 'Uw Factuur heeft status Creditnota', 'Message-ID: <1253802195.4abb80d327b3d@localhost>\r\nDate: Thu, 24 Sep 2009 16:23:15 +0200\r\nFrom: \r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=utf-8\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n\r\n%customer_salutation%,<br />\r\n<br />\r\nUw Factuur is in status=\r\n Creditnota.<br />\r\n<br />\r\nMet vriendelijke groet,<br />\r\n<=\r\nbr />\r\nEen bedrijf', '%customer_salutation%,<br />\n<br />\nUw Order is in status In rekening gebracht.<br />\n<br />\nMet vriendelijke groet,<br />\n<br />\nEen bedrijf', 1),
(1, 11, 'Betaling wordt geverifieerd', NULL, NULL, NULL, 3),
(1, 12, 'Betaling mislukt', NULL, NULL, NULL, 3),
(1, 25, 'Wacht op betaling', 'Uw Factuur heeft status Wacht op betaling', 'Message-ID: <1255615742.4ad72cfe29fbd@localhost>\r\nDate: Thu, 15 Oct 2009 16:09:02 +0200\r\nFrom: \r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=utf-8\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n\r\n%customer_salutation%,<br />\r\n<br />\r\nUw Factuur is in status=\r\n Wacht op betaling.<br />\r\n<br />\r\nMet vriendelijke groet,<br =\r\n/>\r\n<br />\r\nEen bedrijf', NULL, 7),
(1, 27, 'Herinnering verzonden', 'Uw Factuur heeft status Herinnering verzonden', 'Message-ID: <1255615742.4ad72cfe2c996@localhost>\r\nDate: Thu, 15 Oct 2009 16:09:02 +0200\r\nFrom: \r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=utf-8\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n\r\n%customer_salutation%,<br />\r\n<br />\r\nUw Factuur is in status=\r\n Herinnering verzonden.<br />\r\n<br />\r\nMet vriendelijke groet,<br =\r\n/>\r\n<br />\r\nEen bedrijf', NULL, 7),
(1, 28, 'Betaald', 'Uw Factuur heeft status Betaald', 'Message-ID: <1255615742.4ad72cfe2de9e@localhost>\r\nDate: Thu, 15 Oct 2009 16:09:02 +0200\r\nFrom: \r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=utf-8\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n\r\n%customer_salutation%,<br />\r\n<br />\r\nUw Factuur is in status=\r\n Betaald.<br />\r\n<br />\r\nMet vriendelijke groet,<br />\r\n<br /=\r\n>\r\nEen bedrijf', NULL, 7),
(1, 26, 'Creditnota', 'Uw Factuur heeft status Creditnota', 'Message-ID: <1255615742.4ad72cfe2ffd8@localhost>\r\nDate: Thu, 15 Oct 2009 16:09:02 +0200\r\nFrom: \r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=utf-8\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n\r\n%customer_salutation%,<br />\r\n<br />\r\nUw Factuur is in status=\r\n Creditnota.<br />\r\n<br />\r\nMet vriendelijke groet,<br />\r\n<=\r\nbr />\r\nEen bedrijf', NULL, 7),
(1, 29, 'Wacht op betaling', 'Uw Factuur heeft status Wacht op betaling', 'Message-ID: <1255615772.4ad72d1c13b3d@localhost>\r\nDate: Thu, 15 Oct 2009 16:09:32 +0200\r\nFrom: \r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=utf-8\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n\r\n%customer_salutation%,<br />\r\n<br />\r\nUw Factuur is in status=\r\n Wacht op betaling.<br />\r\n<br />\r\nMet vriendelijke groet,<br =\r\n/>\r\n<br />\r\nEen bedrijf', NULL, 8),
(1, 31, 'Herinnering verzonden', 'Uw Factuur heeft status Herinnering verzonden', 'Message-ID: <1255615772.4ad72d1c16a01@localhost>\r\nDate: Thu, 15 Oct 2009 16:09:32 +0200\r\nFrom: \r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=utf-8\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n\r\n%customer_salutation%,<br />\r\n<br />\r\nUw Factuur is in status=\r\n Herinnering verzonden.<br />\r\n<br />\r\nMet vriendelijke groet,<br =\r\n/>\r\n<br />\r\nEen bedrijf', NULL, 8),
(1, 32, 'Betaald', 'Uw Factuur heeft status Betaald', 'Message-ID: <1255615772.4ad72d1c1c91d@localhost>\r\nDate: Thu, 15 Oct 2009 16:09:32 +0200\r\nFrom: \r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=utf-8\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n\r\n%customer_salutation%,<br />\r\n<br />\r\nUw Factuur is in status=\r\n Betaald.<br />\r\n<br />\r\nMet vriendelijke groet,<br />\r\n<br /=\r\n>\r\nEen bedrijf', NULL, 8),
(1, 30, 'Creditnota', 'Uw Factuur heeft status Creditnota', 'Message-ID: <1255615772.4ad72d1c1e1f0@localhost>\r\nDate: Thu, 15 Oct 2009 16:09:32 +0200\r\nFrom: \r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=utf-8\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n\r\n%customer_salutation%,<br />\r\n<br />\r\nUw Factuur is in status=\r\n Creditnota.<br />\r\n<br />\r\nMet vriendelijke groet,<br />\r\n<=\r\nbr />\r\nEen bedrijf', NULL, 8),
(1, 33, 'Wacht op betaling', 'Uw Factuur heeft status Wacht op betaling', 'Message-ID: <1255680944.4ad82bb0a1b3d@localhost>\r\nDate: Fri, 16 Oct 2009 10:15:44 +0200\r\nFrom: \r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=utf-8\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n\r\n%customer_salutation%,<br />\r\n<br />\r\nUw Factuur is in status=\r\n Wacht op betaling.<br />\r\n<br />\r\nMet vriendelijke groet,<br =\r\n/>\r\n<br />\r\nEen bedrijf', NULL, 9),
(1, 35, 'Herinnering verzonden', 'Uw Factuur heeft status Herinnering verzonden', 'Message-ID: <1255680944.4ad82bb0a6fff@localhost>\r\nDate: Fri, 16 Oct 2009 10:15:44 +0200\r\nFrom: \r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=utf-8\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n\r\n%customer_salutation%,<br />\r\n<br />\r\nUw Factuur is in status=\r\n Herinnering verzonden.<br />\r\n<br />\r\nMet vriendelijke groet,<br =\r\n/>\r\n<br />\r\nEen bedrijf', NULL, 9),
(1, 36, 'Betaald', 'Uw Factuur heeft status Betaald', 'Message-ID: <1255680944.4ad82bb0a8671@localhost>\r\nDate: Fri, 16 Oct 2009 10:15:44 +0200\r\nFrom: \r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=utf-8\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n\r\n%customer_salutation%,<br />\r\n<br />\r\nUw Factuur is in status=\r\n Betaald.<br />\r\n<br />\r\nMet vriendelijke groet,<br />\r\n<br /=\r\n>\r\nEen bedrijf', NULL, 9),
(1, 34, 'Creditnota', 'Uw Factuur heeft status Creditnota', 'Message-ID: <1255680944.4ad82bb0a9a12@localhost>\r\nDate: Fri, 16 Oct 2009 10:15:44 +0200\r\nFrom: \r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=utf-8\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n\r\n%customer_salutation%,<br />\r\n<br />\r\nUw Factuur is in status=\r\n Creditnota.<br />\r\n<br />\r\nMet vriendelijke groet,<br />\r\n<=\r\nbr />\r\nEen bedrijf', NULL, 9),
(1, 37, 'Wacht op betaling', 'Uw Factuur heeft status Wacht op betaling', 'Message-ID: <1255959348.4adc6b341fd2d@localhost>\r\nDate: Mon, 19 Oct 2009 15:35:48 +0200\r\nFrom: \r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=utf-8\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n\r\n%customer_salutation%,<br />\r\n<br />\r\nUw Factuur is in status=\r\n Wacht op betaling.<br />\r\n<br />\r\nMet vriendelijke groet,<br =\r\n/>\r\n<br />\r\nEen bedrijf', NULL, 10),
(1, 39, 'Herinnering verzonden', 'Uw Factuur heeft status Herinnering verzonden', 'Message-ID: <1255959348.4adc6b3423257@localhost>\r\nDate: Mon, 19 Oct 2009 15:35:48 +0200\r\nFrom: \r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=utf-8\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n\r\n%customer_salutation%,<br />\r\n<br />\r\nUw Factuur is in status=\r\n Herinnering verzonden.<br />\r\n<br />\r\nMet vriendelijke groet,<br =\r\n/>\r\n<br />\r\nEen bedrijf', NULL, 10),
(1, 40, 'Betaald', 'Uw Factuur heeft status Betaald', 'Message-ID: <1255959348.4adc6b34249ba@localhost>\r\nDate: Mon, 19 Oct 2009 15:35:48 +0200\r\nFrom: \r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=utf-8\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n\r\n%customer_salutation%,<br />\r\n<br />\r\nUw Factuur is in status=\r\n Betaald.<br />\r\n<br />\r\nMet vriendelijke groet,<br />\r\n<br /=\r\n>\r\nEen bedrijf', NULL, 10),
(1, 38, 'Creditnota', 'Uw Factuur heeft status Creditnota', 'Message-ID: <1255959348.4adc6b3425d63@localhost>\r\nDate: Mon, 19 Oct 2009 15:35:48 +0200\r\nFrom: \r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=utf-8\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n\r\n%customer_salutation%,<br />\r\n<br />\r\nUw Factuur is in status=\r\n Creditnota.<br />\r\n<br />\r\nMet vriendelijke groet,<br />\r\n<=\r\nbr />\r\nEen bedrijf', NULL, 10),
(1, 41, 'Wacht op betaling', 'Uw Factuur heeft status Wacht op betaling', 'Message-ID: <1255960277.4adc6ed55f7bb@localhost>\r\nDate: Mon, 19 Oct 2009 15:51:17 +0200\r\nFrom: \r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=utf-8\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n\r\n%customer_salutation%,<br />\r\n<br />\r\nUw Factuur is in status=\r\n Wacht op betaling.<br />\r\n<br />\r\nMet vriendelijke groet,<br =\r\n/>\r\n<br />\r\nEen bedrijf', NULL, 11),
(1, 43, 'Herinnering verzonden', 'Uw Factuur heeft status Herinnering verzonden', 'Message-ID: <1255960277.4adc6ed561f9d@localhost>\r\nDate: Mon, 19 Oct 2009 15:51:17 +0200\r\nFrom: \r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=utf-8\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n\r\n%customer_salutation%,<br />\r\n<br />\r\nUw Factuur is in status=\r\n Herinnering verzonden.<br />\r\n<br />\r\nMet vriendelijke groet,<br =\r\n/>\r\n<br />\r\nEen bedrijf', NULL, 11),
(1, 44, 'Betaald', 'Uw Factuur heeft status Betaald', 'Message-ID: <1255960277.4adc6ed56488f@localhost>\r\nDate: Mon, 19 Oct 2009 15:51:17 +0200\r\nFrom: \r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=utf-8\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n\r\n%customer_salutation%,<br />\r\n<br />\r\nUw Factuur is in status=\r\n Betaald.<br />\r\n<br />\r\nMet vriendelijke groet,<br />\r\n<br /=\r\n>\r\nEen bedrijf', '%customer_salutation%,<br>\n<br>\nUw orderbetaling is geaccepteerd.<br>\n<br>\nMet vriendelijke groet,<br>\n<br>\nEen bedrijf', 11),
(1, 42, 'Creditnota', 'Uw Factuur heeft status Creditnota', 'Message-ID: <1255960277.4adc6ed565ed4@localhost>\r\nDate: Mon, 19 Oct 2009 15:51:17 +0200\r\nFrom: \r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=utf-8\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n\r\n%customer_salutation%,<br />\r\n<br />\r\nUw Factuur is in status=\r\n Creditnota.<br />\r\n<br />\r\nMet vriendelijke groet,<br />\r\n<=\r\nbr />\r\nEen bedrijf', NULL, 11),
(1, 45, 'Afgewezen', 'Uw Factuur is in status Afgewezen', 'Message-ID: <1256136021.4adf1d5587f11@localhost>\r\nDate: Wed, 21 Oct 2009 16:40:21 +0200\r\nFrom: \r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=utf-8\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n\r\n%customer_salutation%,<br>\r\n<br>\r\nUw Factuur is in status A=\r\nfgewezen.<br>\r\n<br>\r\nMet vriendelijke groet,<br>\r\n<br>\r\nE=\r\nen bedrijf', '%customer_salutation%,<br>\n<br>\nUw orderbetaling is afgewezen, omdat de maximaal mogelijke hoeveelheid betaalpogingen is overschreden.<br>\n<br>\nMet vriendelijke groet,<br>\n<br>\nEen bedrijf', 0),
(1, 46, 'Geannuleerd', 'Uw Factuur is in status Geannuleerd', 'Message-ID: <1256135939.4adf1d037a70c@localhost>\r\nDate: Wed, 21 Oct 2009 16:38:59 +0200\r\nFrom: \r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=utf-8\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n\r\n%customer_salutation%,<br>\r\n\r\n<br>\r\n\r\nUw Factuur is=\r\n in status Geannuleerd.<br>\r\n<br>\r\n\r\nMet vriendelijke groet,<b=\r\nr>\r\n\r\n<br>\r\n\r\nEen bedrijf', '%customer_salutation%,<br>\n<br>\nUw orderbetaling is geannuleerd.<br>\n<br>\nMet vriendelijke groet,<br>\n<br>\nEen bedrijf', 0),
(1, 47, 'Exception', 'Uw Factuur is in status Onbekend', 'Message-ID: <1256135972.4adf1d24b0c01@localhost>\r\nDate: Wed, 21 Oct 2009 16:39:32 +0200\r\nFrom: \r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=utf-8\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n\r\n%customer_salutation%,<br>\r\n\r\n<br>\r\n\r\nVanwege een o=\r\nnbekende fout is de status van uw orderbetaling onbekend.\r\nNeem a.u.b=\r\n. contact op met uw bank om vast te stellen of de\r\norderbetaling gebo=\r\nekt staat.<br>\r\n<br>\r\n\r\nMet vriendelijke groet,<b=\r\nr>\r\n\r\n<br>\r\n\r\nEen bedrijf', '%customer_salutation%,<br>\n<br>Vanwege een onbekende fout is de status van uw orderbetaling onbekend. Neem a.u.b. contact op met uw bank om vast te stellen of de orderbetaling geboekt staat.<br>\n<br>\nMet vriendelijke groet,<br>\n<br>\nEen bedrijf', 0),
(1, 48, 'Mislukt', 'Uw Factuur is in status Mislukt', 'Message-ID: <1256135910.4adf1ce60691a@localhost>\r\nDate: Wed, 21 Oct 2009 16:38:30 +0200\r\nFrom: \r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=utf-8\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n\r\n%customer_salutation%,<br>\r\n<br>\r\nUw Factuur is in status M=\r\nislukt.<br><br>\r\nMet vriendelijke groet,<br>\r\n<br>\r\nEen bedrijf', '%customer_salutation%,<br>\n<br>\nUw orderbetaling is mislukt.<br>\n<br>\nMet vriendelijke groet,<br>\n<br>\nEen bedrijf', 0);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `bs_templates`
--

CREATE TABLE IF NOT EXISTS `bs_templates` (
  `id` int(11) NOT NULL,
  `name` varchar(100) default NULL,
  `title` varchar(50) default NULL,
  `right_col` text,
  `left_col` text,
  `margin_top` int(11) NOT NULL,
  `margin_bottom` int(11) NOT NULL,
  `margin_left` int(11) NOT NULL,
  `margin_right` int(11) NOT NULL,
  `page_format` varchar(20) default NULL,
  `footer` text,
  `closing` text,
  `number_name` varchar(30) default NULL,
  `reference_name` varchar(30) default NULL,
  `date_name` varchar(30) default NULL,
  `logo` varchar(255) default NULL,
  `logo_width` int(11) NOT NULL,
  `logo_height` int(11) NOT NULL,
  `show_supplier_product_id` enum('0','1') NOT NULL,
  `show_prod_prices` enum('0','1') NOT NULL,
  `show_unit_prices` enum('0','1') NOT NULL,
  `show_total_prices` enum('0','1') NOT NULL,
  `show_tax` enum('0','1') NOT NULL,
  `book_id` int(11) NOT NULL,
  `logo_top` int(11) NOT NULL,
  `logo_left` int(11) NOT NULL,
  `left_col_top` int(11) NOT NULL,
  `left_col_left` int(11) NOT NULL,
  `right_col_top` int(11) NOT NULL,
  `right_col_left` int(11) NOT NULL,
  `show_amounts` int(11) default NULL,
  `show_vat` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `book_id` (`book_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `bs_templates`
--

INSERT INTO `bs_templates` (`id`, `name`, `title`, `right_col`, `left_col`, `margin_top`, `margin_bottom`, `margin_left`, `margin_right`, `page_format`, `footer`, `closing`, `number_name`, `reference_name`, `date_name`, `logo`, `logo_width`, `logo_height`, `show_supplier_product_id`, `show_prod_prices`, `show_unit_prices`, `show_total_prices`, `show_tax`, `book_id`, `logo_top`, `logo_left`, `left_col_top`, `left_col_left`, `right_col_top`, `right_col_left`, `show_amounts`, `show_vat`) VALUES
(1, 'Factuur', 'Factuur', 'Intermesh\nReitscheweg 37\n5232BX ''s-Hertogenbosch\n\ntel. +31 73 6445508\nfax. +31 84 7380370\nemail: info@intermesh.nl\nurl: http://www.intermesh.nl\n\nKvK: 17156675\nBTW Nr.: NL 1502.03.871.B01', '%customer_name%\n%customer_address% %customer_address_no%\n%customer_zip% %customer_city%\n%customer_country%\n%customer_vat_no%\n%customer_extra%', 30, 30, 30, 30, 'A4', 'Footer text', '<b>Terms</b><br />Put your terms here.', 'Factuur nr.', 'Referentie', 'Factuurdatum', '', 0, 0, '0', '1', '0', '1', '0', 3, 0, 0, 30, 30, 30, 365, NULL, NULL),
(2, 'Offerte', 'Offerte', 'Intermesh\nReitscheweg 37\n5232BX ''s-Hertogenbosch\n\ntel. +31 73 6445508\nfax. +31 84 7380370\nemail: info@intermesh.nl\nurl: http://www.intermesh.nl\n\nKvK: 17156675\nBTW Nr.: NL 1502.03.871.B01', '%customer_name%\n%customer_address% %customer_address_no%\n%customer_zip% %customer_city%\n%customer_country%\n%customer_vat_no%\n%customer_extra%', 30, 30, 30, 30, 'A4', 'Footer text', '<b>Terms</b><br />Put your terms here.', 'Offerte nummer', 'Referentie', 'Offerte datum', '', 0, 0, '0', '1', '0', '1', '0', 1, 0, 0, 30, 30, 30, 365, NULL, NULL),
(3, 'Order', 'Order', 'Intermesh\nReitscheweg 37\n5232BX ''s-Hertogenbosch\n\ntel. +31 73 6445508\nfax. +31 84 7380370\nemail: info@intermesh.nl\nurl: http://www.intermesh.nl\n\nKvK: 17156675\nBTW Nr.: NL 1502.03.871.B01', '%customer_name%\n%customer_address% %customer_address_no%\n%customer_zip% %customer_city%\n%customer_country%\n%customer_vat_no%\n%customer_extra%', 30, 30, 30, 30, 'A4', 'Footer text', '<b>Terms</b><br />Put your terms here.', 'Order nummer', 'Referentie', 'Order datum', '', 0, 0, '0', '1', '0', '1', '0', 2, 0, 0, 30, 30, 30, 365, NULL, NULL),
(10, 'Factuur', 'Factuur', 'Intermesh\nReitscheweg 37\n5232BX ''s-Hertogenbosch\n\ntel. +31 73 6445508\nfax. +31 84 7380370\nemail: info@intermesh.nl\nurl: http://www.intermesh.nl\n\nKvK: 17156675\nBTW Nr.: NL 1502.03.871.B01', '%customer_name%\n%formatted_address%\n%customer_vat_no%\n%customer_extra%', 30, 30, 30, 30, 'A4', 'Footer text', '<b>Terms</b><br />Put your terms here.', 'Factuur nr.', 'Referentie', 'Factuurdatum', '', 0, 0, '0', '1', '1', '1', '0', 10, 0, 0, 30, 30, 30, 365, 1, 0),
(9, 'Factuur', 'Factuur', 'Intermesh\nReitscheweg 37\n5232BX ''s-Hertogenbosch\n\ntel. +31 73 6445508\nfax. +31 84 7380370\nemail: info@intermesh.nl\nurl: http://www.intermesh.nl\n\nKvK: 17156675\nBTW Nr.: NL 1502.03.871.B01', '%customer_name%\n%formatted_address%\n%customer_vat_no%\n%customer_extra%', 30, 30, 30, 30, 'A4', 'Footer text', '<b>Terms</b><br />Put your terms here.', 'Factuur nr.', 'Referentie', 'Factuurdatum', '', 0, 0, '0', '1', '1', '1', '0', 9, 0, 0, 30, 30, 30, 365, 1, 0),
(11, 'Factuur', 'Factuur', 'Intermesh\nReitscheweg 37\n5232BX ''s-Hertogenbosch\n\ntel. +31 73 6445508\nfax. +31 84 7380370\nemail: info@intermesh.nl\nurl: http://www.intermesh.nl\n\nKvK: 17156675\nBTW Nr.: NL 1502.03.871.B01', '%customer_name%\n%formatted_address%\n%customer_vat_no%\n%customer_extra%', 30, 30, 30, 30, 'A4', 'Footer text', '<b>Terms</b><br />Put your terms here.', 'Factuur nr.', 'Referentie', 'Factuurdatum', '', 0, 0, '0', '1', '1', '1', '0', 11, 0, 0, 30, 30, 30, 365, 1, 0);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `cal_calendars`
--

CREATE TABLE IF NOT EXISTS `cal_calendars` (
  `id` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '1',
  `user_id` int(11) NOT NULL default '0',
  `acl_read` int(11) NOT NULL default '0',
  `acl_write` int(11) NOT NULL default '0',
  `name` varchar(100) default NULL,
  `start_hour` tinyint(4) NOT NULL default '0',
  `end_hour` tinyint(4) NOT NULL default '0',
  `background` varchar(6) default NULL,
  `time_interval` int(11) NOT NULL default '1800',
  `public` enum('0','1') NOT NULL,
  `shared_acl` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `group_id` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `cal_calendars`
--

INSERT INTO `cal_calendars` (`id`, `group_id`, `user_id`, `acl_read`, `acl_write`, `name`, `start_hour`, `end_hour`, `background`, `time_interval`, `public`, `shared_acl`) VALUES
(1, 1, 1, 62, 63, 'Beheerder, Group-Office', 0, 0, NULL, 1800, '0', 0);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `cal_events`
--

CREATE TABLE IF NOT EXISTS `cal_events` (
  `id` int(11) NOT NULL default '0',
  `calendar_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `start_time` int(11) NOT NULL default '0',
  `end_time` int(11) NOT NULL default '0',
  `all_day_event` enum('0','1') NOT NULL default '0',
  `name` varchar(100) default NULL,
  `description` text,
  `location` varchar(100) default NULL,
  `repeat_end_time` int(11) NOT NULL default '0',
  `reminder` int(11) NOT NULL default '0',
  `ctime` int(11) NOT NULL default '0',
  `mtime` int(11) NOT NULL default '0',
  `busy` enum('0','1') NOT NULL default '0',
  `status` varchar(20) default NULL,
  `participants_event_id` int(11) NOT NULL,
  `private` enum('0','1') NOT NULL,
  `rrule` varchar(100) NOT NULL,
  `background` char(6) NOT NULL default 'ebf1e2',
  `files_folder_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `start_time` (`start_time`),
  KEY `end_time` (`end_time`),
  KEY `repeat_end_time` (`repeat_end_time`),
  KEY `event_id` (`event_id`),
  KEY `rrule` (`rrule`),
  KEY `participants_event_id` (`participants_event_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `cal_events`
--


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `cal_exceptions`
--

CREATE TABLE IF NOT EXISTS `cal_exceptions` (
  `id` int(11) NOT NULL default '0',
  `event_id` int(11) NOT NULL default '0',
  `time` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `event_id` (`event_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `cal_exceptions`
--


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `cal_groups`
--

CREATE TABLE IF NOT EXISTS `cal_groups` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `name` varchar(50) default NULL,
  `acl_admin` int(11) NOT NULL,
  `fields` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `cal_groups`
--

INSERT INTO `cal_groups` (`id`, `user_id`, `name`, `acl_admin`, `fields`) VALUES
(1, 1, 'Agenda''s', 10, '');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `cal_participants`
--

CREATE TABLE IF NOT EXISTS `cal_participants` (
  `id` int(11) NOT NULL default '0',
  `event_id` int(11) NOT NULL default '0',
  `name` varchar(50) default NULL,
  `email` varchar(100) default NULL,
  `user_id` int(11) NOT NULL default '0',
  `status` enum('0','1','2') NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `cal_participants`
--


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `cal_settings`
--

CREATE TABLE IF NOT EXISTS `cal_settings` (
  `user_id` int(11) NOT NULL,
  `reminder` int(11) NOT NULL,
  `background` char(6) NOT NULL,
  `calendar_id` int(11) NOT NULL,
  PRIMARY KEY  (`user_id`),
  KEY `calendar_id` (`calendar_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `cal_settings`
--

INSERT INTO `cal_settings` (`user_id`, `reminder`, `background`, `calendar_id`) VALUES
(1, 0, 'EBF1E2', 0);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `cal_views`
--

CREATE TABLE IF NOT EXISTS `cal_views` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `name` varchar(50) default NULL,
  `start_hour` tinyint(4) NOT NULL default '0',
  `end_hour` tinyint(4) NOT NULL default '0',
  `event_colors_override` enum('0','1') NOT NULL default '0',
  `time_interval` int(11) NOT NULL default '1800',
  `acl_read` int(11) NOT NULL default '0',
  `acl_write` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `cal_views`
--

INSERT INTO `cal_views` (`id`, `user_id`, `name`, `start_hour`, `end_hour`, `event_colors_override`, `time_interval`, `acl_read`, `acl_write`) VALUES
(1, 1, 'Groepsoverzicht', 0, 0, '0', 1800, 8, 9);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `cal_views_calendars`
--

CREATE TABLE IF NOT EXISTS `cal_views_calendars` (
  `view_id` int(11) NOT NULL default '0',
  `calendar_id` int(11) NOT NULL default '0',
  `background` char(6) NOT NULL default 'CCFFCC',
  PRIMARY KEY  (`view_id`,`calendar_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `cal_views_calendars`
--


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `cf_1`
--

CREATE TABLE IF NOT EXISTS `cf_1` (
  `link_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`link_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `cf_1`
--


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `cf_2`
--

CREATE TABLE IF NOT EXISTS `cf_2` (
  `link_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`link_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `cf_2`
--


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `cf_3`
--

CREATE TABLE IF NOT EXISTS `cf_3` (
  `link_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`link_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `cf_3`
--


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `cf_4`
--

CREATE TABLE IF NOT EXISTS `cf_4` (
  `link_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`link_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `cf_4`
--


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `cf_6`
--

CREATE TABLE IF NOT EXISTS `cf_6` (
  `link_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`link_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `cf_6`
--


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `cf_7`
--

CREATE TABLE IF NOT EXISTS `cf_7` (
  `link_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`link_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `cf_7`
--


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `cf_8`
--

CREATE TABLE IF NOT EXISTS `cf_8` (
  `link_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`link_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `cf_8`
--


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `cms_files`
--

CREATE TABLE IF NOT EXISTS `cms_files` (
  `id` int(11) NOT NULL default '0',
  `folder_id` int(11) NOT NULL default '0',
  `size` int(11) NOT NULL default '0',
  `ctime` int(11) NOT NULL default '0',
  `mtime` int(11) NOT NULL default '0',
  `name` varchar(255) default NULL,
  `content` longtext,
  `auto_meta` enum('0','1') NOT NULL default '1',
  `title` varchar(100) default NULL,
  `description` text,
  `keywords` text,
  `priority` int(11) NOT NULL default '0',
  `option_values` text,
  `plugin` varchar(20) default NULL,
  `type` varchar(100) NOT NULL,
  `files_folder_id` int(11) NOT NULL,
  `show_until` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `folder_id` (`folder_id`),
  KEY `name` (`name`),
  KEY `show_until` (`show_until`),
  FULLTEXT KEY `content` (`content`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `cms_files`
--

INSERT INTO `cms_files` (`id`, `folder_id`, `size`, `ctime`, `mtime`, `name`, `content`, `auto_meta`, `title`, `description`, `keywords`, `priority`, `option_values`, `plugin`, `type`, `files_folder_id`, `show_until`) VALUES
(666, 396, 0, 1255006536, 1255006536, 'Home', '<h1>Demo site<br /></h1>\n<p>Just showing off some features here!</p>\n<p>&nbsp;</p>', '1', NULL, NULL, NULL, 0, NULL, NULL, 'default', 18, 0),
(667, 396, 0, 1255006536, 1255006536, 'Contact', '', '1', NULL, NULL, NULL, 1, '<?xml version="1.0"?>\n<template_options><option name="addressbook" value="Klanten"/></template_options>', NULL, 'contact', 19, 0),
(668, 397, 0, 1255006536, 1255006536, 'Photoalbum', '', '1', NULL, NULL, NULL, 0, '', NULL, 'photoalbum', 21, 0),
(669, 397, 0, 1255006536, 1255006536, 'Guestbook', '', '1', NULL, NULL, NULL, 1, NULL, NULL, 'guestbook', 22, 0),
(670, 396, 0, 1255006536, 1255006536, 'Portfolio', '<h1>Portfolio</h1>\n<p>This page demonstrates how it can sum up other pages and use some custom fields with that.</p>', '1', NULL, NULL, NULL, 3, NULL, NULL, 'portfolio', 23, 0),
(671, 398, 0, 1255006536, 1255006536, 'formSuccess', '<h1>Thank you!</h1>\n<p>Your information was recieved.</p>\n<p>&nbsp;</p>', '1', NULL, NULL, NULL, 0, NULL, NULL, 'default', 25, 0),
(672, 400, 0, 1255006536, 1255006536, 'Calendar', '<p>In a corporate environment a calendar can''t be missed. This calendar allows you to plan all sorts of recurring events and set reminders for them. The easy to use interface will never let you miss an event. It''s easy to set up multiple calendars and share them with other users. The calendar supports the import and export of the popular iCalendar standard. This makes it possible to synchronise the Group-Office calendar with other calendar software that support the iCalendar protocol.</p>', '1', NULL, NULL, NULL, 0, '<?xml version="1.0"?>\n<template_options><option name="image" value="public/cms/Example website/data/portfolio/Calendar/calendar.jpg"/></template_options>', NULL, 'portfolio', 27, 0),
(673, 400, 0, 1255006536, 1255006536, 'E-mail', '<p>The flexible e-mail module integrates in all other modules. You can access your e-mail everywhere in the world. With the templates you can create professional signatures and send newsletters to keep your customers up-to-date with your latest news!</p>', '1', NULL, NULL, NULL, 1, '<?xml version="1.0"?>\n<template_options><option name="image" value="public/cms/Example website/data/portfolio/E-mail/email.jpg"/></template_options>', NULL, 'portfolio', 28, 0),
(674, 400, 0, 1255006536, 1255006536, 'CRM', '<p>Keep in touch with your prospects and customers in an easy way. The addressbook keeps track of all the customers related notes, e-mail, files etc. With the ticket system you will be reminded of important events so you will never forget a customer.</p>', '1', NULL, NULL, NULL, 2, '<?xml version="1.0"?>\n<template_options><option name="image" value="public/cms/Example website/data/portfolio/CRM/crm.jpg"/></template_options>', NULL, 'portfolio', 29, 0),
(686, 408, 0, 1255959347, 1255959347, 'Home', '<h1>Welcome to the Group-Office demo webshop!<br /></h1>\n<p>Go to the Catalog section to see some example products. The products\nin the catalog can be added with the Billing module. The catalog is linked\nto the website with the Webshop dialog in the Website module.</p>\n<p>&nbsp;</p>', '1', NULL, NULL, NULL, 0, NULL, NULL, 'default', 127, 0),
(687, 408, 0, 1255959347, 1255959347, 'Catalog', '<h1>Catalog<br /></h1>\n<p>We accept only good quality and working products for your second-hand use.\nFeel free to browse thru our catalog. We make the catalog up-to-date every week day.\nUnable to find what you are looking for today? It may be here tomorrow!', '1', NULL, NULL, NULL, 1, NULL, NULL, 'products', 128, 0);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `cms_folders`
--

CREATE TABLE IF NOT EXISTS `cms_folders` (
  `id` int(11) NOT NULL default '0',
  `parent_id` int(11) NOT NULL default '0',
  `ctime` int(11) NOT NULL default '0',
  `mtime` int(11) NOT NULL default '0',
  `name` char(255) NOT NULL default '',
  `disabled` enum('0','1') NOT NULL default '0',
  `priority` int(11) NOT NULL default '0',
  `acl` int(11) NOT NULL default '0',
  `site_id` int(11) NOT NULL,
  `option_values` text,
  `default_template` varchar(100) NOT NULL default '',
  `type` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `site_id` (`site_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `cms_folders`
--

INSERT INTO `cms_folders` (`id`, `parent_id`, `ctime`, `mtime`, `name`, `disabled`, `priority`, `acl`, `site_id`, `option_values`, `default_template`, `type`) VALUES
(396, 0, 1255006536, 1255006536, 'Root', '0', 0, 0, 159, NULL, '', ''),
(397, 396, 1255006536, 1255006536, 'Member area', '0', 2, 0, 159, NULL, '', 'default'),
(398, 396, 1255006536, 1255006536, 'data', '1', 4, 0, 159, NULL, '', 'default'),
(399, 398, 1255006536, 1255006536, 'guestbook', '1', 1, 0, 159, NULL, '', 'default'),
(400, 398, 1255006536, 1255006536, 'portfolio', '1', 2, 0, 159, NULL, '', 'portfolio'),
(406, 0, 1255680944, 1255680944, 'Root', '0', 1, 0, 164, NULL, '', ''),
(408, 0, 1255959347, 1255959347, 'Root', '0', 2, 0, 166, NULL, '', '');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `cms_sites`
--

CREATE TABLE IF NOT EXISTS `cms_sites` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `acl_write` int(11) NOT NULL default '0',
  `domain` varchar(100) default NULL,
  `webmaster` varchar(100) default NULL,
  `root_folder_id` int(11) NOT NULL default '0',
  `start_file_id` int(11) NOT NULL default '0',
  `language` varchar(10) default NULL,
  `name` varchar(100) default NULL,
  `template` varchar(50) default NULL,
  `files_folder_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `cms_sites`
--

INSERT INTO `cms_sites` (`id`, `user_id`, `acl_write`, `domain`, `webmaster`, `root_folder_id`, `start_file_id`, `language`, `name`, `template`, `files_folder_id`) VALUES
(159, 1, 583, 'example.com', 'webmaster@example.com', 396, 0, 'nl', 'Example website', 'Example', 17),
(166, 1, 614, 'webshop-demo.com', 'webmaster@example.com', 408, 0, 'nl', 'Webshop_Demo_Site', 'webshop-demo', 126);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `co_comments`
--

CREATE TABLE IF NOT EXISTS `co_comments` (
  `id` int(11) NOT NULL,
  `link_id` int(11) NOT NULL,
  `link_type` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ctime` int(11) NOT NULL,
  `mtime` int(11) NOT NULL,
  `comments` text,
  PRIMARY KEY  (`id`),
  KEY `link_id` (`link_id`,`link_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `co_comments`
--


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `em_accounts`
--

CREATE TABLE IF NOT EXISTS `em_accounts` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `type` varchar(4) default NULL,
  `host` varchar(100) default NULL,
  `port` int(11) NOT NULL default '0',
  `use_ssl` enum('0','1') NOT NULL default '0',
  `novalidate_cert` enum('0','1') NOT NULL default '0',
  `username` varchar(50) default NULL,
  `password` varchar(64) default NULL,
  `signature` text,
  `standard` tinyint(4) NOT NULL default '0',
  `mbroot` varchar(30) default NULL,
  `sent` varchar(100) default NULL,
  `drafts` varchar(100) default NULL,
  `trash` varchar(100) default NULL,
  `spam` varchar(100) default NULL,
  `spamtag` varchar(20) default NULL,
  `examine_headers` enum('0','1') NOT NULL default '0',
  `auto_check` enum('0','1') NOT NULL default '0',
  `forward_enabled` enum('0','1') NOT NULL,
  `forward_to` varchar(255) default NULL,
  `forward_local_copy` enum('0','1') NOT NULL,
  `smtp_host` varchar(100) default NULL,
  `smtp_port` int(11) NOT NULL,
  `smtp_encryption` char(3) NOT NULL,
  `smtp_username` varchar(50) default NULL,
  `smtp_password` varchar(50) default NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `em_accounts`
--


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `em_aliases`
--

CREATE TABLE IF NOT EXISTS `em_aliases` (
  `id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `signature` text NOT NULL,
  `default` enum('0','1') NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `account_id` (`account_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `em_aliases`
--


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `em_filters`
--

CREATE TABLE IF NOT EXISTS `em_filters` (
  `id` int(11) NOT NULL default '0',
  `account_id` int(11) NOT NULL default '0',
  `field` varchar(20) default NULL,
  `keyword` varchar(100) default NULL,
  `folder` varchar(100) default NULL,
  `priority` int(11) NOT NULL default '0',
  `mark_as_read` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `em_filters`
--


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `em_folders`
--

CREATE TABLE IF NOT EXISTS `em_folders` (
  `id` int(11) NOT NULL default '0',
  `account_id` int(11) NOT NULL default '0',
  `name` varchar(100) default NULL,
  `subscribed` enum('0','1') NOT NULL default '0',
  `parent_id` int(11) NOT NULL default '0',
  `delimiter` char(1) NOT NULL default '',
  `attributes` int(11) NOT NULL default '0',
  `sort_order` tinyint(4) NOT NULL default '0',
  `msgcount` int(11) NOT NULL default '0',
  `unseen` int(11) NOT NULL default '0',
  `auto_check` enum('0','1') NOT NULL default '0',
  `sort` longtext,
  PRIMARY KEY  (`id`),
  KEY `account_id` (`account_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `em_folders`
--


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `em_links`
--

CREATE TABLE IF NOT EXISTS `em_links` (
  `link_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `from` varchar(255) default NULL,
  `to` text,
  `subject` varchar(255) default NULL,
  `time` int(11) NOT NULL default '0',
  `path` varchar(255) default NULL,
  `ctime` int(11) NOT NULL,
  `acl_read` int(11) NOT NULL,
  `acl_write` int(11) NOT NULL,
  PRIMARY KEY  (`link_id`),
  KEY `account_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `em_links`
--


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `em_messages_cache`
--

CREATE TABLE IF NOT EXISTS `em_messages_cache` (
  `folder_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `new` enum('0','1') NOT NULL,
  `subject` varchar(100) default NULL,
  `from` varchar(100) default NULL,
  `reply_to` varchar(100) default NULL,
  `size` int(11) NOT NULL,
  `udate` int(11) NOT NULL,
  `attachments` enum('0','1') NOT NULL,
  `flagged` enum('0','1') NOT NULL,
  `answered` enum('0','1') NOT NULL,
  `priority` tinyint(4) NOT NULL,
  `to` varchar(255) default NULL,
  `notification` varchar(100) NOT NULL,
  `content_type` varchar(100) NOT NULL,
  `content_transfer_encoding` varchar(50) NOT NULL,
  PRIMARY KEY  (`folder_id`,`uid`),
  KEY `account_id` (`account_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `em_messages_cache`
--


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `fs_files`
--

CREATE TABLE IF NOT EXISTS `fs_files` (
  `id` int(11) NOT NULL,
  `folder_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL default '',
  `locked_user_id` int(11) NOT NULL default '0',
  `status_id` int(11) NOT NULL default '0',
  `ctime` int(11) NOT NULL default '0',
  `mtime` int(11) NOT NULL default '0',
  `size` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comments` text,
  `extension` varchar(4) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `folder_id` (`folder_id`),
  KEY `name` (`name`),
  KEY `extension` (`extension`),
  KEY `path` (`path`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `fs_files`
--


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `fs_folders`
--

CREATE TABLE IF NOT EXISTS `fs_folders` (
  `user_id` int(11) NOT NULL default '0',
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `visible` enum('0','1') NOT NULL,
  `acl_read` int(11) NOT NULL default '0',
  `acl_write` int(11) NOT NULL default '0',
  `comments` text,
  `thumbs` enum('0','1') NOT NULL default '0',
  `ctime` int(11) NOT NULL,
  `mtime` int(11) NOT NULL,
  `readonly` enum('0','1') NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `name` (`name`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `fs_folders`
--

INSERT INTO `fs_folders` (`user_id`, `id`, `parent_id`, `name`, `path`, `visible`, `acl_read`, `acl_write`, `comments`, `thumbs`, `ctime`, `mtime`, `readonly`) VALUES
(0, 1, 0, 'notes', '', '0', 0, 0, NULL, '0', 1253707127, 1252927055, '1'),
(1, 2, 1, 'General', '', '0', 31, 32, NULL, '0', 1252927055, 1253609212, '1'),
(0, 3, 0, 'billing', '', '0', 0, 0, NULL, '0', 1250671910, 1255959347, '1'),
(1, 4, 3, 'Offertes', '', '0', 49, 50, NULL, '0', 1250671910, 1250671910, '1'),
(1, 5, 3, 'Orders', '', '0', 51, 52, NULL, '0', 1251962945, 1253719047, '1'),
(1, 6, 3, 'Facturen', '', '0', 53, 54, NULL, '0', 1250691923, 1253197742, '1'),
(0, 7, 0, 'users', '', '0', 0, 0, NULL, '0', 1253707127, 1250065914, '1'),
(0, 8, 0, 'adminusers', '', '0', 0, 0, NULL, '0', 1253707127, 1249039775, '1'),
(1, 9, 7, 'admin', '', '1', 58, 59, NULL, '0', 1250065914, 1250065914, '1'),
(1, 10, 8, 'admin', '', '0', 60, 61, NULL, '0', 1249039775, 1249039775, '1'),
(0, 11, 0, 'events', '', '0', 0, 0, NULL, '0', 1253707127, 1251445312, '1'),
(1, 12, 11, 'Beheerder, Group-Office', '', '0', 62, 63, NULL, '0', 1251445312, 1251445312, '1'),
(0, 13, 0, 'tasks', '', '0', 0, 0, NULL, '0', 1253707127, 1251372326, '1'),
(1, 14, 13, 'Beheerder, Group-Office', '', '0', 64, 65, NULL, '0', 1251372326, 1251372326, '1'),
(0, 15, 0, 'public', '', '0', 0, 0, NULL, '0', 1253198152, 1253801929, '1'),
(0, 16, 15, 'cms', '', '0', 0, 0, NULL, '0', 1253801929, 1255684236, '1'),
(1, 17, 16, 'Example website', '', '0', 583, 583, NULL, '0', 1253274791, 1253274791, '1'),
(0, 18, 17, 'Home', '', '0', 0, 0, NULL, '0', 1253196114, 1253196114, '1'),
(0, 19, 17, 'Contact', '', '0', 0, 0, NULL, '0', 1253196114, 1253196114, '1'),
(0, 20, 17, 'Member area', '', '0', 0, 0, NULL, '0', 1253196114, 1253196114, '1'),
(0, 21, 20, 'Photoalbum', '', '0', 0, 0, NULL, '0', 1253196114, 1253196114, '1'),
(0, 22, 20, 'Guestbook', '', '0', 0, 0, NULL, '0', 1253196114, 1253196114, '1'),
(0, 23, 17, 'Portfolio', '', '0', 0, 0, NULL, '0', 1253196114, 1253196114, '1'),
(0, 24, 17, 'data', '', '0', 0, 0, NULL, '0', 1253196114, 1253196114, '1'),
(0, 25, 24, 'formSuccess', '', '0', 0, 0, NULL, '0', 1253196114, 1253196114, '1'),
(0, 26, 24, 'portfolio', '', '0', 0, 0, NULL, '0', 1253196114, 1253196115, '1'),
(0, 27, 26, 'Calendar', '', '0', 0, 0, NULL, '0', 1253196114, 1253196114, '1'),
(0, 28, 26, 'E-mail', '', '0', 0, 0, NULL, '0', 1253196115, 1253196115, '1'),
(0, 29, 26, 'CRM', '', '0', 0, 0, NULL, '0', 1253196115, 1253196115, '1'),
(1, 36, 17, 'Test (1)', '', '0', 0, 0, NULL, '0', 1253274791, 1253274791, '0'),
(1, 37, 17, 'Products', '', '0', 0, 0, NULL, '0', 1253198025, 1253198025, '0'),
(1, 38, 17, 'Test', '', '0', 0, 0, NULL, '0', 1253274791, 1253274791, '0'),
(1, 126, 16, 'Webshop_Demo_Site', '', '0', 614, 614, NULL, '0', 1255526417, 1255526417, '1'),
(0, 41, 16, 'Home', '', '0', 0, 0, NULL, '0', 1254817715, 1254817715, '1'),
(0, 127, 126, 'Home', '', '0', 0, 0, NULL, '0', 1255526417, 1255526417, '1'),
(0, 128, 126, 'Catalog', '', '0', 0, 0, NULL, '1', 1255526417, 1255526417, '1'),
(0, 53, 0, 'log', '', '0', 0, 0, NULL, '0', 1253705563, 1253705563, '1'),
(0, 129, 5, '2009', '', '0', 0, 0, NULL, '0', 1253719047, 1255611055, '1'),
(0, 130, 129, '1 Group-Office Beheerder', '', '0', 0, 0, NULL, '0', 1255610005, 1255610005, '1'),
(0, 131, 129, '2 Group-Office Beheerder', '', '0', 0, 0, NULL, '0', 1255610311, 1255610311, '1'),
(0, 132, 6, '2009', '', '0', 0, 0, NULL, '0', 1253197742, 1256133496, '1'),
(0, 133, 132, '3 Group-Office Beheerder', '', '0', 0, 0, NULL, '0', 1255610362, 1255610362, '1'),
(0, 134, 129, '4 Group-Office Beheerder', '', '0', 0, 0, NULL, '0', 1255610733, 1255610733, '1'),
(0, 135, 129, '5 Group-Office Beheerder', '', '0', 0, 0, NULL, '0', 1255611055, 1255611055, '1'),
(0, 136, 132, '6 Group-Office Beheerder', '', '0', 0, 0, NULL, '0', 1255611070, 1255611070, '1'),
(1, 147, 3, 'Webshop Orders', '', '0', 615, 616, NULL, '0', 1255959347, 1256036740, '1'),
(0, 139, 132, '7 Group-Office Beheerder', '', '0', 0, 0, NULL, '0', 1255675624, 1255675624, '1'),
(0, 148, 147, '2009', '', '0', 0, 0, NULL, '0', 1256036740, 1256196345, '1'),
(0, 141, 2, '2009', '', '0', 0, 0, NULL, '0', 1253609212, 1255937721, '1'),
(0, 151, 148, '12 Group-Office Beheerder', '', '0', 0, 0, NULL, '0', 1256037664, 1256037664, '1'),
(0, 150, 148, '11 Group-Office Beheerder', '', '0', 0, 0, NULL, '0', 1256036966, 1256036966, '1'),
(0, 149, 148, '10 Group-Office Beheerder', '', '0', 0, 0, NULL, '0', 1256036740, 1256036740, '1'),
(0, 152, 148, '13 Group-Office Beheerder', '', '0', 0, 0, NULL, '0', 1256037706, 1256037706, '1'),
(0, 153, 148, '14 Group-Office Beheerder', '', '0', 0, 0, NULL, '0', 1256044841, 1256044841, '1'),
(0, 154, 148, '15 Group-Office Beheerder', '', '0', 0, 0, NULL, '0', 1256045109, 1256045109, '1'),
(0, 155, 148, '16 Group-Office Beheerder', '', '0', 0, 0, NULL, '0', 1256045123, 1256045123, '1'),
(0, 156, 148, '17 Group-Office Beheerder', '', '0', 0, 0, NULL, '0', 1256045424, 1256045424, '1'),
(0, 157, 148, '18 Group-Office Beheerder', '', '0', 0, 0, NULL, '0', 1256045806, 1256045806, '1'),
(0, 158, 148, '19 Group-Office Beheerder', '', '0', 0, 0, NULL, '0', 1256045833, 1256045833, '1'),
(0, 159, 148, '20 Group-Office Beheerder', '', '0', 0, 0, NULL, '0', 1256045892, 1256045892, '1'),
(0, 160, 148, '21 Group-Office Beheerder', '', '0', 0, 0, NULL, '0', 1256045928, 1256045928, '1'),
(0, 161, 148, '22 Group-Office Beheerder', '', '0', 0, 0, NULL, '0', 1256048092, 1256048092, '1'),
(0, 162, 148, '23 Group-Office Beheerder', '', '0', 0, 0, NULL, '0', 1256049007, 1256049007, '1'),
(0, 163, 148, '24 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256050481, 1256050481, '1'),
(0, 164, 148, '25 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256050700, 1256050700, '1'),
(0, 165, 148, '26 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256050720, 1256050720, '1'),
(0, 166, 148, '27 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256050880, 1256050880, '1'),
(0, 167, 148, '28 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256050965, 1256050965, '1'),
(0, 168, 148, '29 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256051082, 1256051082, '1'),
(0, 169, 148, '30 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256106325, 1256106325, '1'),
(0, 170, 148, '31 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256106436, 1256106436, '1'),
(0, 171, 148, '32 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256106655, 1256106655, '1'),
(0, 172, 148, '33 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256106727, 1256106727, '1'),
(0, 173, 148, '34 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256106778, 1256106778, '1'),
(0, 174, 148, '35 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256106827, 1256106827, '1'),
(0, 175, 148, '36 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256107034, 1256107034, '1'),
(0, 176, 148, '37 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256107076, 1256107076, '1'),
(0, 177, 148, '38 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256111276, 1256111276, '1'),
(0, 178, 148, '39 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256113255, 1256113255, '1'),
(0, 179, 132, '40 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256113280, 1256113280, '1'),
(0, 180, 148, '41 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256116366, 1256116366, '1'),
(0, 181, 148, '42 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256118245, 1256118245, '1'),
(0, 182, 148, '43 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256118707, 1256118707, '1'),
(0, 183, 148, '44 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256119281, 1256119281, '1'),
(0, 184, 148, '45 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256120853, 1256120853, '1'),
(0, 185, 148, '46 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256120913, 1256120913, '1'),
(0, 186, 148, '47 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256121009, 1256121009, '1'),
(0, 187, 148, '48 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256123294, 1256123294, '1'),
(0, 188, 132, '49 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256123303, 1256123303, '1'),
(0, 189, 148, '50 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256123852, 1256123852, '1'),
(0, 190, 132, '51 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256124394, 1256124394, '1'),
(0, 191, 148, '52 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256124815, 1256124815, '1'),
(0, 192, 132, '53 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256124829, 1256124829, '1'),
(0, 193, 148, '54 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256125172, 1256125172, '1'),
(0, 194, 148, '55 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256125191, 1256125191, '1'),
(0, 195, 132, '56 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256125220, 1256125220, '1'),
(0, 196, 148, '57 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256126402, 1256126402, '1'),
(0, 197, 132, '58 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256126562, 1256126562, '1'),
(0, 198, 148, '59 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256126628, 1256126628, '1'),
(0, 199, 132, '60 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256126659, 1256126659, '1'),
(0, 200, 148, '61 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256127055, 1256127055, '1'),
(0, 201, 132, '62 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256127084, 1256127084, '1'),
(0, 202, 148, '63 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256127721, 1256127721, '1'),
(0, 203, 132, '64 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256127736, 1256127736, '1'),
(0, 204, 148, '65 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256130713, 1256130713, '1'),
(0, 205, 132, '66 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256130732, 1256130732, '1'),
(0, 206, 148, '67 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256130773, 1256130773, '1'),
(0, 207, 148, '68 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256133235, 1256133235, '1'),
(0, 208, 132, '69 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256133496, 1256133496, '1'),
(0, 209, 148, '70 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256133657, 1256133657, '1'),
(0, 210, 148, '71 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256134025, 1256134025, '1'),
(0, 211, 148, '72 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256136267, 1256136267, '1'),
(0, 212, 148, '73 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256136294, 1256136294, '1'),
(0, 213, 148, '74 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256136692, 1256136692, '1'),
(0, 214, 148, '76 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256136832, 1256136832, '1'),
(0, 215, 148, '77 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256136859, 1256136859, '1'),
(0, 216, 148, '78 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256136928, 1256136928, '1'),
(0, 217, 148, '79 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256136952, 1256136952, '1'),
(0, 218, 148, '80 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256137002, 1256137002, '1'),
(0, 219, 148, '81 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256193362, 1256193362, '1'),
(0, 220, 148, '82 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256193377, 1256193377, '1'),
(0, 221, 148, '83 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256193526, 1256193526, '1'),
(0, 222, 148, '84 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256193542, 1256193542, '1'),
(0, 223, 148, '85 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256196160, 1256196160, '1'),
(0, 224, 148, '86 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256196218, 1256196218, '1'),
(0, 225, 148, '87 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256196291, 1256196291, '1'),
(0, 226, 148, '88 Waldemar de Beursekond', '', '0', 0, 0, NULL, '0', 1256196345, 1256196345, '1');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `fs_new_files`
--

CREATE TABLE IF NOT EXISTS `fs_new_files` (
  `file_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  KEY `file_id` (`file_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `fs_new_files`
--


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `fs_notifications`
--

CREATE TABLE IF NOT EXISTS `fs_notifications` (
  `folder_id` int(11) NOT NULL,
  `path` varchar(255) NOT NULL default '',
  `user_id` int(11) NOT NULL,
  PRIMARY KEY  (`folder_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `fs_notifications`
--


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `fs_statuses`
--

CREATE TABLE IF NOT EXISTS `fs_statuses` (
  `id` int(11) NOT NULL default '0',
  `name` varchar(50) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `fs_statuses`
--


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `fs_status_history`
--

CREATE TABLE IF NOT EXISTS `fs_status_history` (
  `id` int(11) NOT NULL default '0',
  `link_id` int(11) NOT NULL default '0',
  `status_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `ctime` int(11) NOT NULL default '0',
  `comments` text,
  PRIMARY KEY  (`id`),
  KEY `link_id` (`link_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `fs_status_history`
--


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `fs_templates`
--

CREATE TABLE IF NOT EXISTS `fs_templates` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(50) default NULL,
  `acl_read` int(11) NOT NULL,
  `acl_write` int(11) NOT NULL,
  `content` mediumblob NOT NULL,
  `extension` char(4) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `fs_templates`
--

INSERT INTO `fs_templates` (`id`, `user_id`, `name`, `acl_read`, `acl_write`, `content`, `extension`) VALUES
(1, 1, 'Open-Office Text document', 23, 24, 0x504b03041400000000004b3b1a395ec6320c2700000027000000080000006d696d65747970656170706c69636174696f6e2f766e642e6f617369732e6f70656e646f63756d656e742e74657874504b03041400000000004b3b1a390000000000000000000000001a000000436f6e66696775726174696f6e73322f7374617475736261722f504b03041400080008004b3b1a3900000000000000000000000027000000436f6e66696775726174696f6e73322f616363656c657261746f722f63757272656e742e786d6c0300504b0708000000000200000000000000504b03041400000000004b3b1a3900000000000000000000000018000000436f6e66696775726174696f6e73322f666c6f617465722f504b03041400000000004b3b1a390000000000000000000000001a000000436f6e66696775726174696f6e73322f706f7075706d656e752f504b03041400000000004b3b1a390000000000000000000000001c000000436f6e66696775726174696f6e73322f70726f67726573736261722f504b03041400000000004b3b1a3900000000000000000000000018000000436f6e66696775726174696f6e73322f6d656e756261722f504b03041400000000004b3b1a3900000000000000000000000018000000436f6e66696775726174696f6e73322f746f6f6c6261722f504b03041400000000004b3b1a390000000000000000000000001f000000436f6e66696775726174696f6e73322f696d616765732f4269746d6170732f504b03041400080008004b3b1a390000000000000000000000000b000000636f6e74656e742e786d6ca556cb6e1b2114ddf72b462cba1b63275d24538fa34a51a54a49174d5a754b80b169794c81f1d87f5f1e9e314e32099237b6b99c73b99c73012f6f7682175baa0d53b2068bd91c14546245985cd7e0e7e3d7f20adcac3e2c55d3304c2ba27027a8b42556d2baefc2b1a5a9e26c0d3a2d2b850c339544829acae24ab5540eac2a455761ad183176cfb3e9019cb22dddd95cb2c79e70d153feca019cb289467d2ed9639da829bd51b9e49de165a39ceaa245963dab62c799fc5b838db56d0561dff7b3fe72a6f41a2eaeafaf61981d0bc623aeed340f288221e5d42f66e062b6800356508b72ebf3d8b424d98927aab3a54116bd70d56cd7d91db15d4f4883374867f746009fda7b49f2edbd24295720bb99f0e40adebbc9f0717f77ec052d72d7f2d813a9b0666df636233ae52ba5c6523d211ed050eec57cfe09c67182eedf84f79a59aa13387e138e11c7a3e24abc269ac32da0439474ebdb746c7c2f8499205cc0383d820d994cfdfbfeee016fa84047307b1f5c32692c9247651a46f9d030e3460f5ed05d4b35f33620ee34f19b51a471299c54aaad9204d1990339b9991760355cc3d14203c740e3aee3b241989684626e56cb789cc67011c7be941a3cba324cf19df6c50f259004853b3f035430beafc147d42af3f9192e06417192dae3cb35956e6fce657dc87744b4cc62770eb648337f7902f876695f1c8cbf52d0109f5edaf4cc987396bea57fd0afae7840d24c2a926032d4307b63a978af263865e1218e3aeb54b50c97214f62fa9322fb71e01fb6d5323c6f86feebdc1b3e267a192c428830d372b42f5567dd1b414bee4e17af816bbe301d65f9c67967ac760528e9cb3d2bd9e3b0e9f3b2b89f6727b98dcf7170605ab5365282f265243eb8234f9026a977517c78e20b9cf8cbb4fa0f504b0708d5003d409002000073090000504b03041400080008004b3b1a390000000000000000000000000a0000007374796c65732e786d6ccd594b8fdb3610bef757182ada1b6dcbbb9baeddec06458ba205921c9ab4d78096688b09450a2465d9f9f51992a244cb9257fb48e13d2c20ce70e6e33cc9f1eb37fb9c4d76442a2af85d144fe7d184f044a4946fefa27f3ffe896ea337f73fbc169b0d4dc82a15499913ae91d20746d4043673b572c4bba8947c25b0a26ac5714ed44a272b5110ee37ad42ee9555e556acb0b1db2d73b85b93bd1ebbd9f01eedc5ebf19a2d73b83b95b81abbd9f0824dc3ed1b3176f35e31b4112811798135eda0d833cabfdc4599d6c56a36abaa6a5a5d4d85dccee2e57239b3d40670d2f015a564962b4d668411a34ccde2693cf3bc39d1782c3ec31b42e265be2672b469b0c6275e55bbede888d86d074c9364588e8e0dcb7cecdeab74bc7bafd2706f8e7536e093dbd93b20da7fefdeb6b120f3b1ba0cef91a912498bd1c774dce17e214403d56c70096ae12ee6f3eb99fb0eb8abb3ec95a49ac8803d39cb9e6096341617799fd1802f9e0107223b13a6d1a42e2141d98aa37b5fa33602ead3062704a52461eafeb58bad6679e2be8d8deea28f142c35794faac93f22c73c9a403079d69cb2c35df4332e84fab5c3e716a3c99168c38fb6841349e1c8b296d77214542710143b2ca9a924d1ec3cb4df808df500f2ebc3aa5545957a8eea3fc867fc5f39f980b91ab448c033c21aeaa034c91fc2341b7261bdeeba8ec79e920d2e59dd8bbce41ae356e222a349e479eb6f54488841a92938d354e495ca702a2a04f215d1687f17cda75709e0ec211e3a440de50241752548153881da8e3221e957808e99615ddc9e65de1918c9292b24ec58a927ac3d326bb33038474575865cb7dc60a6822828b0c4d642a17d1cc9f0235c6a61744068d09408c78a599161afc0c2584b82a113290d2ed79e62ca81c1968b14b63389f4fa280c284f89a952e656111ec683f418a1fb82a745a14c9c0cc36ed80dee93d3948a8019b8f1aa559e0826a04f6959427ddb088748d1af80345e14daae31ccb725dec2126776211125d712c2e1fddbe6f84443cd435f88e416ba13189cd2c844509bb1a956f3e94dd1d8c78bf7d4affbbd27d58a3c850bde23d4743e46f61e70476843ed11dbd0ace0d6ac47793526d91a3f4467030a4c971d8a8c7068d4822386d3146c66d1180ce0fd9c362718197745c9135d3a811590a17dc0d1c1070f07a60f289452484f6e94c4d3c54ddc66cd71e81660cf36659e105f81e7069acff78c40a3d247d2717dffce516a1537b17656f54bc572186ac417926efc499263ca91b9f6f9205c9c3015a5ca3a2ccf4814fb82082b1a23610cb907c65a48931726e8a0904304315c2813d1cf558ca4a83aca61a593a15f082990165ba23373833719f890e250a10bec0f904f299669345828bcfb18560ae04132b5a9752aef2f82d320a707c5c142f33845fd50b8c9db90e1232c7c5acc3fad457ae883f55049cbb1847a03262b4cd7bd5ed8aedbaeaf85d6e6560b0d395ed4246b63db8db9edc69855f8a01eaa2d41e1a8af809d7271dde6ccd332be57c06333d7086903e68140e9333e94e382e143e09e49487e8ef39fecd7f33e1d7ddcb7d0699e728e3331ca8cc8118173e4e8c5637cf43b2e4c297c41fb034a891f99586df60c25969dedb8c983bd48aaa6f05b8abd08f8c1c47c38d9067ab05b73832a0a7771785b9c35ef11d19aa4a60e6deeed7dc1f6865e0b18edbfbfe16ebd7f41ef512befacf75ec8158f0a5f2b5994dabd217ad618d91156774707c32c80b8a635943932b3150c25b9399fc9af7a6bf780393597d83550da1b24bc106fe3ba240c691f896b71a1b8ae2e14d7f585e2bab9505caf2e14d72f178aebf642712d2f14573cffff811d9342b45c68a2a089f20ddd96d23eee260d01d5ad6d238436df7dc0e3ba79b951de0eb3d2a0aa17fd46850aa1a8b643693b2f08f7b88e67060a469e1fcb9b138d4748783a0490f603f4e28d455a047d6a06fbb51b80dae7cd72d98e47faac530b69adc0c846d734ca13697f6832353198f65a69ed90d73c3941264d9027f89bc616bc8d0fe0dda3fb4d91c7510f4fe74e6729154dcdcf328bf974e90ee20919a1dbccbcee97d357678e582b010b6a242485a3e0dad742c29596eaa87b711db8b476968d914e16650d69681ed519a5ba004439de37a731cf9676d05f3328527871ce1af3e93cbe6d95f894436b0227b7fc86279ec73d3c7863864e7d2c38fd5c2aedbced62c0ad4b4856ef869b9fda318e9d93fd38b77f5138e2ed73a83f5446b01995d88f5978d260f154501b79a7a1561372ac1a198db67ad1483a3bed0831b7211a047c47faacffc7f5fb6f504b0708ea8445d17d0600009c1f0000504b03041400000000004b3b1a3991678ab20e0400000e040000080000006d6574612e786d6c3c3f786d6c2076657273696f6e3d22312e302220656e636f64696e673d225554462d38223f3e0a3c6f66666963653a646f63756d656e742d6d65746120786d6c6e733a6f66666963653d2275726e3a6f617369733a6e616d65733a74633a6f70656e646f63756d656e743a786d6c6e733a6f66666963653a312e302220786d6c6e733a786c696e6b3d22687474703a2f2f7777772e77332e6f72672f313939392f786c696e6b2220786d6c6e733a64633d22687474703a2f2f7075726c2e6f72672f64632f656c656d656e74732f312e312f2220786d6c6e733a6d6574613d2275726e3a6f617369733a6e616d65733a74633a6f70656e646f63756d656e743a786d6c6e733a6d6574613a312e302220786d6c6e733a6f6f6f3d22687474703a2f2f6f70656e6f66666963652e6f72672f323030342f6f666669636522206f66666963653a76657273696f6e3d22312e31223e3c6f66666963653a6d6574613e3c6d6574613a67656e657261746f723e4f70656e4f66666963652e6f72672f322e34244c696e7578204f70656e4f66666963652e6f72675f70726f6a6563742f3638306d3137244275696c642d393331303c2f6d6574613a67656e657261746f723e3c6d6574613a696e697469616c2d63726561746f723e4d6572696a6e205363686572696e673c2f6d6574613a696e697469616c2d63726561746f723e3c6d6574613a6372656174696f6e2d646174653e323030382d30382d32365430393a32363a30323c2f6d6574613a6372656174696f6e2d646174653e3c6d6574613a65646974696e672d6379636c65733e303c2f6d6574613a65646974696e672d6379636c65733e3c6d6574613a65646974696e672d6475726174696f6e3e505430533c2f6d6574613a65646974696e672d6475726174696f6e3e3c6d6574613a757365722d646566696e6564206d6574613a6e616d653d22496e666f2031222f3e3c6d6574613a757365722d646566696e6564206d6574613a6e616d653d22496e666f2032222f3e3c6d6574613a757365722d646566696e6564206d6574613a6e616d653d22496e666f2033222f3e3c6d6574613a757365722d646566696e6564206d6574613a6e616d653d22496e666f2034222f3e3c6d6574613a646f63756d656e742d737461746973746963206d6574613a7461626c652d636f756e743d223022206d6574613a696d6167652d636f756e743d223022206d6574613a6f626a6563742d636f756e743d223022206d6574613a706167652d636f756e743d223122206d6574613a7061726167726170682d636f756e743d223022206d6574613a776f72642d636f756e743d223022206d6574613a6368617261637465722d636f756e743d2230222f3e3c2f6f66666963653a6d6574613e3c2f6f66666963653a646f63756d656e742d6d6574613e504b03041400080008004b3b1a39000000000000000000000000180000005468756d626e61696c732f7468756d626e61696c2e706e67eb0cf073e7e592e2626060e0f5f4700902d25b1918181938d880acafcae6b7191898f67bba388654cc797b6923278301cf810d7c3fff3ffdd2e974d043bce2c39b778d937eca32ac9f394b2678727256918f6fdfca9386a696cbd48e5f79326354705470c005c5cfe5b2fd330aff2a0b4cd10c9eae7e2eeb9c129a00504b070884d783a37c000000f8020000504b03041400080008004b3b1a390000000000000000000000000c00000073657474696e67732e786d6cb5595173e2380c7ebf5fd1c93b054a6fefcab4ec04baecb1a58501ba9ddb379308c8d5b132b653e0df9fec844e0ba14b097ea24d6cc992a5ef9394ebafab989fbd8054118a1baf7e5ef3ce40041846627ee33d4eba95bfbdafad3fae71368b02688618a431085d51a0352d5167b45da866f6fac64ba5682253916a0a16836aeaa0890988cdb6e6dbd54dab2c7bb2e29178bef1165a27cd6a75b95c9e2f1be728e7d5fad5d555d5bedd2c0d50cca2f9a1aab2d56f5521e2ab22b3213b8c557651ab5d56b3ffbdb3fc906f5c53f75a1b3f6ccc6f5de70ab29f4aa42136be39cb1f9ba3dd78a4b2f912c1f2d56b5ed1bef77b7ed27a5f029b60e26ddee875426f22a1bd56edbaba2be170a97d98e922b1957aad5efbab9ceca728d48b22e1978d8bc69fe564ff03d17c5178f28b7afdea48e1e3052e471052904167c1c41cd4968229220726bc9696291ca7a327da12970aee31847dd2678cab83c5576296542211c20ac25d5f154798dd43b921d78779bc176e1d556949e1ebb54c305f1c7f93fba2af51ab9590ba2753ca0855d194839b5cb1a24f9ddc56e8685f8a341af58b2fa544b7516b8cf7a55fe338d9bf10e30949da8eb505cae37d61847659a051168badd78e14dc5363e0106808bb921e1c91cb050fdf26e6bed779ae172f2062399c8ab207a9649a88ed339ce487e190493661140ae38405060b4e8e944382183d02c3bbb08d3fc741e57bf983541ba4ef9306fe6f043c540f693c05f9813125b4f5a972794c42a68b907f138b251cf52d4ef47ac8dc70d65ca0846e24952633a0470128744fb87417f1afa4ac05d9c13891a04ce975726cb08e1b93011c7ee074afe34a9891dd785712b0419c70fadb519e0c5902d2e819834eb7b9e4149650ca5b9c1bcc6604142e7c65ed301ceb288af3ca6e2851136c533cddc17a5b0b53f0e5b21d0926d75ef5c0235b147470defca05d94db447baa04d37eaa310b514761d941e232e44ebc43e2411686fb276f318337ba46e58bb0cd997856e47483741dc683945b7a7415f0be10a8ad86fdbc7024bcf591852360210abe13e8a741045304e4fc3f419b071de02e6e9b747d5bd1750bc6fb6491937ac34f12be7e54206f9966a717df352586cb841bb317f8990d2906a2c3519da6bfdc55f29de394f1db7cbe620a1b1774d0537774dfbe8a9818a622d0a9ab34f479341714bc638dc91055f4919ae36fa7c3a3c457af658d2f02ca6d089f242d955dbeb6e4eac28f9d544aba2613d40618cdef185319ec604dd6d21f8097b7f880bac3129d4ab8956c3998fea706c2b0b683e35b5419e1f20ec0455993d3c8c36e0777b83fac8c2e5b9512424ca9a210e40456fa49b26420c8ad148e2e8a5205031ee696df031d317056cfd91984abb644f5d9145e81c88da74cf905b618f84da35b9e49dfb6d3be1e6b2ad04eefb45793368196b8b1c982c408a85730a6f95a4b535451cbd845275795754112554235b32bf9dfc95f8b0fb2a544f1c1392ead0eea443b4c04c01d70d07b2aa0d63a66222c68abcb0c1fedc5ff48958e666b9336ea29d28b7b2652c6db12d8b31b8e50233075fa0b4c301b4f38cc1c02668ab14da9e0267d727436b6986f59a6ce626b4cb7cddacce11704b315090a796ace5486175c4e75f251c51cda2c789e4b4cc5dec1dea9a3bc1cbb9b32c7ce46ddf4b1d47406cf06189dd0bd5851dc06f00b245213f59bfee993f36a3b6faeee7c0cadeefb4cdcfa1f504b0708749187f0db040000681e0000504b03041400080008004b3b1a39000000000000000000000000150000004d4554412d494e462f6d616e69666573742e786d6cb5954b6ac3301040f73d85d1de56db553171022df404e90126f2d811e887661492db570ee4d33694a6583b09a4f74623cd68b1da5b53ed3092f6ae134fcda3a8d029df6b3776e263fd5ebf88d5f26161c1e90189dbd3a0cafb1c9da79d48d1b51e4853ebc022b5ac5a1fd0f55e258b8edbafebdbc9b47ca82ee0411bacf3c278a82e32ec35d47c08d80908c168059ce3943bd737475773ad6818f72c2ebb87644c1d80b79d9042de25bb4d79f36ed0638ac720e859120327da402c8307a5d0609efa28558a713a62ce62715711c1603c301682071f52c84f2015c2473f46a472373d855e0ccede9b62706d614492af9a2d042aeab893fdbd5f507253f5344937ea5af0b718ee944f9d484eb57a139efdfcbf62fe9d4b7c3048b3632d32ccd678d6db64370eb421c9a76113dc38377cdec42273fe10cfa95dc81fffe1f213504b07083562d7393e0100004a070000504b010214001400000000004b3b1a395ec6320c27000000270000000800000000000000000000000000000000006d696d6574797065504b010214001400000000004b3b1a390000000000000000000000001a000000000000000000000000004d000000436f6e66696775726174696f6e73322f7374617475736261722f504b010214001400080008004b3b1a39000000000200000000000000270000000000000000000000000085000000436f6e66696775726174696f6e73322f616363656c657261746f722f63757272656e742e786d6c504b010214001400000000004b3b1a390000000000000000000000001800000000000000000000000000dc000000436f6e66696775726174696f6e73322f666c6f617465722f504b010214001400000000004b3b1a390000000000000000000000001a0000000000000000000000000012010000436f6e66696775726174696f6e73322f706f7075706d656e752f504b010214001400000000004b3b1a390000000000000000000000001c000000000000000000000000004a010000436f6e66696775726174696f6e73322f70726f67726573736261722f504b010214001400000000004b3b1a39000000000000000000000000180000000000000000000000000084010000436f6e66696775726174696f6e73322f6d656e756261722f504b010214001400000000004b3b1a390000000000000000000000001800000000000000000000000000ba010000436f6e66696775726174696f6e73322f746f6f6c6261722f504b010214001400000000004b3b1a390000000000000000000000001f00000000000000000000000000f0010000436f6e66696775726174696f6e73322f696d616765732f4269746d6170732f504b010214001400080008004b3b1a39d5003d4090020000730900000b000000000000000000000000002d020000636f6e74656e742e786d6c504b010214001400080008004b3b1a39ea8445d17d0600009c1f00000a00000000000000000000000000f60400007374796c65732e786d6c504b010214001400000000004b3b1a3991678ab20e0400000e0400000800000000000000000000000000ab0b00006d6574612e786d6c504b010214001400080008004b3b1a3984d783a37c000000f80200001800000000000000000000000000df0f00005468756d626e61696c732f7468756d626e61696c2e706e67504b010214001400080008004b3b1a39749187f0db040000681e00000c00000000000000000000000000a110000073657474696e67732e786d6c504b010214001400080008004b3b1a393562d7393e0100004a0700001500000000000000000000000000b61500004d4554412d494e462f6d616e69666573742e786d6c504b0506000000000f000f00ee030000371700000000, 'odt');
INSERT INTO `fs_templates` (`id`, `user_id`, `name`, `acl_read`, `acl_write`, `content`, `extension`) VALUES
(2, 1, 'Microsoft Word document', 25, 26, 0xd0cf11e0a1b11ae1000000000000000000000000000000003b000300feff090006000000000000000000000002000000b800000000000000001000000200000001000000feffffff000000000000000080000000fffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffdfffffffffffffffeffffff040000000500000006000000b300000008000000090000000a0000000b0000000c0000000d0000000e0000000f000000100000001100000012000000130000001400000015000000160000001700000018000000190000001a0000001b0000001c0000001d0000001e0000001f000000200000002100000022000000230000002400000025000000260000002700000028000000290000002a0000002b0000002c0000002d0000002e0000002f000000300000003100000032000000330000003400000035000000360000003700000038000000390000003a0000003b0000003c0000003d0000003e0000003f000000400000004100000042000000430000004400000045000000460000004700000048000000490000004a0000004b0000004c0000004d0000004e0000004f000000500000005100000052000000530000005400000055000000560000005700000058000000590000005a0000005b0000005c0000005d0000005e0000005f000000600000006100000062000000630000006400000065000000660000006700000068000000690000006a0000006b0000006c0000006d0000006e0000006f000000700000007100000072000000730000007400000075000000760000007700000078000000790000007a0000007b0000007c0000007d0000007e0000007f0000008100000052006f006f007400200045006e00740072007900000000000000000000000000000000000000000000000000000000000000000000000000000000000000000016000500ffffffffffffffffffffffff000000000000000000000000000000000000000000000000000000000000000000000000feffffff00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000ffffffffffffffffffffffff000000000000000000000000000000000000000000000000000000000000000000000000feffffff00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000ffffffffffffffffffffffff000000000000000000000000000000000000000000000000000000000000000000000000feffffff00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000ffffffffffffffffffffffff000000000000000000000000000000000000000000000000000000000000000000000000feffffff000000000000000001000000fefffffffeffffff0400000005000000060000000700000008000000090000000a0000000b0000000c0000000d0000000e0000000f00000010000000110000001200000013000000140000001500000016000000170000001800000019000000feffffff1b0000001c0000001d0000001e0000001f000000200000002100000022000000230000002400000025000000260000002700000028000000290000002a0000002b0000002c0000002d0000002e0000002f000000300000003100000032000000330000003400000035000000360000003700000038000000390000003a0000003b0000003c0000003d0000003e0000003f000000400000004100000042000000feffffff44000000feffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff0100feff030a0000ffffffff0609020000000000c000000000000046180000004d6963726f736f667420576f72642d446f6b756d656e74000a0000004d53576f7264446f630010000000576f72642e446f63756d656e742e3800f439b2710000000000000000000000000000000000000000000000000000000000000000000001000002000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000120014000a0001005b000f00020000000000000060000010f1ff02006000000009005300740061006e00640061006100720064000000080000003124002a24013300422a004f4a0000514a0000434a18006d481304734813044b480100504a03006e48ff005e4a0300614a18005f48ff007448ff000000000000000000000000000000000000000042004140f2ffa100420000001900410062007300610074007a002d005300740061006e00640061007200640073006300680072006900660074006100720074000000000000000000000000003e00fe1f010002013e00000003004b006f00700000000d000f0013a4f00014a478000624010018004f4a0200514a0200434a1c00504a03005e4a0300614a1c002e004210010002012e0000000900540065006b007300740062006c006f006b0000000a00100013a4000014a4780000001e002f10010112011e00000005004c0069006a007300740000000200110000004200fe1f01002201420000000a00420069006a00730063006800720069006600740000000d00120013a4780014a478000c2401000e00434a1800360801614a18005d08012200fe1f0100320122000000050049006e006400650078000000050013000c240100000000000000010000000400000a00000000ffffffff000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000004000002040000030000000004000002040000040000000000000001000000000000000210000000000000000100000050000004000000000400000047169001000000000000000000000000000000000000000000000000000000000000000000000000540069006d006500730020004e0065007700200052006f006d0061006e00000035169001020000000000000000000000000000000000000000000000000000000000000000000000530079006d0062006f006c0000003326900100000000000000000000000000000000000000000000000000000000000000000000000041007200690061006c0000003f069001000000000000000000000000000000000000000000000000000000000000000000000000440065006a006100560075002000530061006e00730000004200040001088d180000c50200006801000000005ad2c84600000000000000000100000000000000000000000000010000000000040083900000000000000000000000000100000000000000000000000000270300000000000000040000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000001230000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000200000000000000000000000000000000000400000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000800000000000000000000000000000000000000000000000000000000000000000000000000000eca5c2004d20090400000012bf000000000000300000000000040000020400000e0043616f6c616e383000000000000000000000000000000000000009041600240a000000000000000000000100000000000000000000000000000000000000000000000000000000000000ffff0f000300000001000000ffff0f000400000001000000ffff0f00000000000000000000000000000000006c0000000000cc01000000000000cc0100000000000000000000000000000000000000000000000000000000000000000000cc010000140000000000000000000000000000000000000000000000000000000000000000000000e001000034000000140200000c000000200200000c00000000000000000000004d020000f6000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000002c02000000000000000000000000000000000000000000000000000000000000000000000000feff0000010002000000000000000000000000000000000001000000e0859ff2f94f6810ab9108002b27b3d930000000bc540100080000000100000048000000040000005000000009000000680000000a000000740000000b000000800000000c0000008c0000000d0000009800000011000000a400000002000000e9fd00001e000000100000004d6572696a6e205363686572696e67001e00000002000000300000004000000000000000000000004000000000000000000000004000000000612cfe4c07c9014000000000000000000000004700000010540100ffffffff080000002800000071000000a0000000010018000000000080d4000000000000000000000000000000000000ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000fdffffff82000000830000008400000085000000860000008700000088000000890000008a0000008b0000008c0000008d0000008e0000008f000000900000009100000092000000930000009400000095000000960000009700000098000000990000009a0000009b0000009c0000009d0000009e0000009f000000a0000000a1000000a2000000a3000000a4000000a5000000a6000000a7000000a8000000a9000000aa000000ab000000ac000000ad000000ae000000af000000b0000000b1000000b2000000feffffffb4000000b5000000b6000000b7000000feffffffb9000000feffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000430300005802000000000000000000003802000015000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000002c0200000c000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000200d90000000d0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000040000020400000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000010004000002040000fd000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000010000000122001fb0812e20b0c54121b06e0422b06e0423906e0424906e04335000002832000e300000000000000000000000000000000000000000000000000000000000feff000001000200000000000000000000000000000000000200000002d5cdd59c2e1b10939708002b2cf9ae4400000005d5cdd59c2e1b10939708002b2cf9ae5c0000001800000001000000010000001000000002000000e9fd00001800000001000000010000001000000002000000e9fd000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000052006f006f007400200045006e00740072007900000000000000000000000000000000000000000000000000000000000000000000000000000000000000000016000500ffffffffffffffff010000000609020000000000c0000000000000460000000000000000000000000000000000000000030000004011000000000000010043006f006d0070004f0062006a00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000120002000200000004000000ffffffff000000000000000000000000000000000000000000000000000000000000000000000000000000006a0000000000000001004f006c00650000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000a000200ffffffff03000000ffffffff00000000000000000000000000000000000000000000000000000000000000000000000002000000140000000000000031005400610062006c006500000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000e000200ffffffffffffffffffffffff000000000000000000000000000000000000000000000000000000000000000000000000030000009b050000000000000500530075006d006d0061007200790049006e0066006f0072006d006100740069006f006e000000000000000000000000000000000000000000000000000000280002000500000006000000ffffffff00000000000000000000000000000000000000000000000000000000000000000000000007000000ec5401000000000057006f007200640044006f00630075006d0065006e007400000000000000000000000000000000000000000000000000000000000000000000000000000000001a000200ffffffffffffffffffffffff0000000000000000000000000000000000000000000000000000000000000000000000001a000000240a000000000000050044006f00630075006d0065006e007400530075006d006d0061007200790049006e0066006f0072006d006100740069006f006e000000000000000000000038000200ffffffffffffffffffffffff0000000000000000000000000000000000000000000000000000000000000000000000004300000074000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000ffffffffffffffffffffffff000000000000000000000000000000000000000000000000000000000000000000000000feffffff0000000000000000, 'doc');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_acl`
--

CREATE TABLE IF NOT EXISTS `go_acl` (
  `acl_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`acl_id`,`user_id`,`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `go_acl`
--

INSERT INTO `go_acl` (`acl_id`, `user_id`, `group_id`) VALUES
(1, 0, 0),
(1, 0, 1),
(2, 0, 0),
(2, 0, 1),
(3, 0, 0),
(3, 0, 1),
(4, 0, 0),
(4, 0, 1),
(5, 0, 0),
(5, 0, 1),
(6, 0, 0),
(6, 0, 1),
(7, 0, 1),
(7, 1, 0),
(8, 0, 1),
(8, 1, 0),
(9, 0, 1),
(9, 0, 3),
(9, 1, 0),
(10, 0, 0),
(10, 0, 1),
(11, 0, 0),
(11, 0, 1),
(12, 0, 0),
(12, 0, 1),
(13, 0, 0),
(13, 0, 1),
(14, 0, 0),
(14, 0, 1),
(15, 0, 1),
(15, 1, 0),
(16, 0, 1),
(16, 0, 3),
(16, 1, 0),
(17, 0, 1),
(17, 1, 0),
(18, 0, 1),
(18, 0, 3),
(18, 1, 0),
(19, 0, 1),
(19, 1, 0),
(20, 0, 1),
(20, 0, 3),
(20, 1, 0),
(21, 0, 0),
(21, 0, 1),
(22, 0, 0),
(22, 0, 1),
(23, 0, 0),
(23, 0, 1),
(23, 0, 3),
(24, 0, 0),
(24, 0, 1),
(25, 0, 0),
(25, 0, 1),
(25, 0, 3),
(26, 0, 0),
(26, 0, 1),
(27, 0, 1),
(27, 1, 0),
(28, 0, 1),
(28, 0, 3),
(28, 1, 0),
(29, 0, 0),
(29, 0, 1),
(30, 0, 0),
(30, 0, 1),
(31, 0, 1),
(31, 1, 0),
(32, 0, 1),
(32, 0, 2),
(32, 1, 0),
(33, 0, 0),
(33, 0, 1),
(34, 0, 0),
(34, 0, 1),
(35, 0, 0),
(35, 0, 1),
(36, 0, 0),
(36, 0, 1),
(37, 0, 0),
(37, 0, 1),
(38, 0, 0),
(38, 0, 1),
(39, 0, 0),
(39, 0, 1),
(40, 0, 0),
(40, 0, 1),
(41, 0, 0),
(41, 0, 1),
(42, 0, 0),
(42, 0, 1),
(43, 0, 0),
(43, 0, 1),
(44, 0, 0),
(44, 0, 1),
(45, 0, 0),
(45, 0, 1),
(46, 0, 0),
(46, 0, 1),
(47, 0, 0),
(47, 0, 1),
(48, 0, 0),
(48, 0, 1),
(49, 0, 0),
(49, 0, 1),
(50, 0, 0),
(50, 0, 1),
(50, 0, 3),
(51, 0, 0),
(51, 0, 1),
(52, 0, 0),
(52, 0, 1),
(52, 0, 3),
(53, 0, 0),
(53, 0, 1),
(54, 0, 0),
(54, 0, 1),
(54, 0, 3),
(55, 0, 0),
(55, 0, 1),
(56, 0, 0),
(56, 0, 1),
(56, 0, 3),
(57, 0, 0),
(57, 0, 1),
(57, 0, 2),
(58, 0, 1),
(58, 1, 0),
(59, 0, 1),
(59, 1, 0),
(60, 0, 1),
(60, 1, 0),
(61, 0, 1),
(61, 1, 0),
(62, 0, 1),
(62, 1, 0),
(63, 0, 1),
(63, 1, 0),
(64, 0, 1),
(64, 1, 0),
(65, 0, 1),
(65, 1, 0),
(68, 0, 1),
(68, 1, 0),
(77, 0, 1),
(77, 1, 0),
(83, 0, 1),
(83, 1, 0),
(88, 0, 1),
(88, 1, 0),
(91, 0, 1),
(91, 1, 0),
(97, 0, 1),
(97, 1, 0),
(100, 0, 1),
(100, 1, 0),
(103, 0, 1),
(103, 1, 0),
(106, 0, 1),
(106, 1, 0),
(109, 0, 1),
(109, 1, 0),
(121, 0, 1),
(121, 1, 0),
(130, 0, 1),
(130, 1, 0),
(133, 0, 1),
(133, 1, 0),
(138, 0, 1),
(138, 1, 0),
(143, 0, 1),
(143, 1, 0),
(146, 0, 1),
(146, 1, 0),
(149, 0, 1),
(149, 1, 0),
(154, 0, 1),
(154, 1, 0),
(159, 0, 1),
(159, 1, 0),
(164, 0, 1),
(164, 1, 0),
(165, 0, 1),
(165, 1, 0),
(166, 0, 1),
(166, 1, 0),
(171, 0, 1),
(171, 1, 0),
(176, 0, 1),
(176, 1, 0),
(181, 0, 1),
(181, 1, 0),
(186, 0, 1),
(186, 1, 0),
(191, 0, 1),
(191, 1, 0),
(196, 0, 1),
(196, 1, 0),
(201, 0, 1),
(201, 1, 0),
(204, 0, 1),
(204, 1, 0),
(209, 0, 1),
(209, 1, 0),
(210, 0, 1),
(210, 1, 0),
(211, 0, 1),
(211, 1, 0),
(216, 0, 1),
(216, 1, 0),
(219, 0, 1),
(219, 1, 0),
(222, 0, 1),
(222, 1, 0),
(249, 0, 1),
(249, 1, 0),
(255, 0, 1),
(255, 1, 0),
(258, 0, 1),
(258, 1, 0),
(261, 0, 1),
(261, 1, 0),
(264, 0, 1),
(264, 1, 0),
(267, 0, 1),
(267, 1, 0),
(270, 0, 1),
(270, 1, 0),
(275, 0, 1),
(275, 1, 0),
(276, 0, 1),
(276, 1, 0),
(277, 0, 1),
(277, 1, 0),
(282, 0, 1),
(282, 1, 0),
(285, 0, 1),
(285, 1, 0),
(288, 0, 1),
(288, 1, 0),
(291, 0, 1),
(291, 1, 0),
(294, 0, 1),
(294, 1, 0),
(297, 0, 1),
(297, 1, 0),
(300, 0, 1),
(300, 1, 0),
(303, 0, 1),
(303, 1, 0),
(306, 0, 1),
(306, 1, 0),
(309, 0, 1),
(309, 1, 0),
(312, 0, 1),
(312, 1, 0),
(315, 0, 1),
(315, 1, 0),
(318, 0, 1),
(318, 1, 0),
(321, 0, 1),
(321, 1, 0),
(324, 0, 1),
(324, 1, 0),
(327, 0, 1),
(327, 1, 0),
(330, 0, 1),
(330, 1, 0),
(333, 0, 1),
(333, 1, 0),
(336, 0, 1),
(336, 1, 0),
(339, 0, 1),
(339, 1, 0),
(349, 0, 1),
(349, 1, 0),
(350, 0, 1),
(350, 1, 0),
(355, 0, 1),
(355, 1, 0),
(360, 0, 1),
(360, 1, 0),
(363, 0, 1),
(363, 1, 0),
(366, 0, 1),
(366, 1, 0),
(369, 0, 1),
(369, 1, 0),
(372, 0, 1),
(372, 1, 0),
(375, 0, 1),
(375, 1, 0),
(378, 0, 1),
(378, 1, 0),
(381, 0, 1),
(381, 1, 0),
(384, 0, 1),
(384, 1, 0),
(387, 0, 1),
(387, 1, 0),
(390, 0, 1),
(390, 1, 0),
(393, 0, 1),
(393, 1, 0),
(396, 0, 1),
(396, 1, 0),
(399, 0, 1),
(399, 1, 0),
(402, 0, 1),
(402, 1, 0),
(405, 0, 1),
(405, 1, 0),
(408, 0, 1),
(408, 1, 0),
(411, 0, 1),
(411, 1, 0),
(414, 0, 1),
(414, 1, 0),
(417, 0, 1),
(417, 1, 0),
(420, 0, 1),
(420, 1, 0),
(423, 0, 1),
(423, 1, 0),
(426, 0, 1),
(426, 1, 0),
(429, 0, 1),
(429, 1, 0),
(432, 0, 1),
(432, 1, 0),
(435, 0, 1),
(435, 1, 0),
(438, 0, 1),
(438, 1, 0),
(441, 0, 1),
(441, 1, 0),
(444, 0, 1),
(444, 1, 0),
(447, 0, 1),
(447, 1, 0),
(450, 0, 1),
(450, 1, 0),
(453, 0, 1),
(453, 1, 0),
(456, 0, 1),
(456, 1, 0),
(459, 0, 1),
(459, 1, 0),
(462, 0, 1),
(462, 1, 0),
(465, 0, 1),
(465, 1, 0),
(468, 0, 1),
(468, 1, 0),
(471, 0, 1),
(471, 1, 0),
(474, 0, 1),
(474, 1, 0),
(477, 0, 1),
(477, 1, 0),
(480, 0, 1),
(480, 1, 0),
(483, 0, 1),
(483, 1, 0),
(486, 0, 1),
(486, 1, 0),
(489, 0, 1),
(489, 1, 0),
(492, 0, 1),
(492, 1, 0),
(495, 0, 1),
(495, 1, 0),
(498, 0, 1),
(498, 1, 0),
(501, 0, 1),
(501, 1, 0),
(504, 0, 1),
(504, 1, 0),
(538, 0, 1),
(538, 1, 0),
(541, 0, 1),
(541, 1, 0),
(544, 0, 1),
(544, 1, 0),
(547, 0, 1),
(547, 1, 0),
(550, 0, 1),
(550, 1, 0),
(553, 0, 1),
(553, 1, 0),
(556, 0, 1),
(556, 1, 0),
(559, 0, 1),
(559, 1, 0),
(562, 0, 1),
(562, 1, 0),
(565, 0, 1),
(565, 1, 0),
(568, 0, 1),
(568, 1, 0),
(571, 0, 1),
(571, 1, 0),
(574, 0, 1),
(574, 1, 0),
(577, 0, 1),
(577, 1, 0),
(580, 0, 1),
(580, 1, 0),
(581, 0, 1),
(581, 1, 0),
(582, 0, 1),
(582, 1, 0),
(583, 0, 1),
(583, 1, 0),
(586, 0, 1),
(586, 1, 0),
(589, 0, 1),
(589, 1, 0),
(597, 0, 1),
(597, 1, 0),
(598, 0, 1),
(598, 1, 0),
(599, 0, 1),
(599, 1, 0),
(600, 0, 1),
(600, 1, 0),
(601, 0, 1),
(601, 1, 0),
(610, 0, 1),
(610, 1, 0),
(612, 0, 1),
(612, 1, 0),
(613, 0, 1),
(613, 1, 0),
(614, 0, 1),
(614, 1, 0),
(615, 0, 1),
(615, 1, 0),
(616, 0, 1),
(616, 1, 0);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_acl_items`
--

CREATE TABLE IF NOT EXISTS `go_acl_items` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `description` varchar(50) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `go_acl_items`
--

INSERT INTO `go_acl_items` (`id`, `user_id`, `description`) VALUES
(1, 0, ''),
(2, 0, ''),
(3, 0, ''),
(4, 0, ''),
(5, 0, ''),
(6, 0, ''),
(7, 1, 'resource_group'),
(8, 1, 'view'),
(9, 1, 'view'),
(10, 0, 'resource_group'),
(11, 0, ''),
(12, 0, ''),
(13, 0, ''),
(14, 0, ''),
(15, 1, 'addressbook'),
(16, 1, 'addressbook'),
(17, 1, 'addressbook'),
(18, 1, 'addressbook'),
(19, 1, 'addressbook'),
(20, 1, 'addressbook'),
(21, 0, ''),
(22, 0, ''),
(23, 0, 'files'),
(24, 0, 'files'),
(25, 0, 'files'),
(26, 0, 'files'),
(27, 1, 'files'),
(28, 1, 'files'),
(29, 0, ''),
(30, 0, ''),
(31, 1, 'category'),
(32, 1, 'category'),
(33, 0, ''),
(34, 0, ''),
(35, 0, ''),
(36, 0, ''),
(37, 0, ''),
(38, 0, ''),
(39, 0, ''),
(40, 0, ''),
(41, 0, ''),
(42, 0, ''),
(43, 0, ''),
(44, 0, ''),
(45, 0, ''),
(46, 0, ''),
(47, 0, ''),
(48, 0, ''),
(49, 0, ''),
(50, 0, ''),
(51, 0, ''),
(52, 0, ''),
(53, 0, ''),
(54, 0, ''),
(55, 0, ''),
(56, 0, ''),
(57, 1, 'webmaster@example.com'),
(58, 1, 'files'),
(59, 1, 'files'),
(60, 1, 'files'),
(61, 1, 'files'),
(62, 1, ''),
(63, 1, ''),
(64, 1, ''),
(65, 1, ''),
(121, 1, 'site'),
(83, 1, 'site'),
(68, 1, 'site'),
(103, 1, 'site'),
(91, 1, 'site'),
(77, 1, 'resource_group'),
(97, 1, 'site'),
(88, 1, 'site'),
(133, 1, 'site'),
(109, 1, 'site'),
(100, 1, 'site'),
(143, 1, 'site'),
(106, 1, 'site'),
(130, 1, 'site'),
(149, 1, 'site'),
(138, 1, 'site'),
(154, 1, 'site'),
(146, 1, 'site'),
(159, 1, 'site'),
(164, 1, 'site'),
(171, 1, 'site'),
(165, 1, ''),
(166, 1, ''),
(176, 1, 'site'),
(181, 1, 'site'),
(186, 1, 'site'),
(191, 1, 'site'),
(196, 1, 'site'),
(201, 1, 'site'),
(210, 1, ''),
(209, 1, 'site'),
(204, 1, 'site'),
(216, 1, 'site'),
(211, 1, ''),
(222, 1, 'site'),
(261, 1, 'site'),
(219, 1, 'site'),
(300, 1, 'site'),
(249, 1, 'site'),
(275, 1, 'site'),
(267, 1, 'site'),
(255, 1, 'site'),
(258, 1, 'site'),
(276, 1, ''),
(264, 1, 'site'),
(282, 1, 'site'),
(270, 1, 'site'),
(277, 1, ''),
(355, 1, 'site'),
(339, 1, 'site'),
(285, 1, 'site'),
(288, 1, 'site'),
(291, 1, 'site'),
(294, 1, 'site'),
(297, 1, 'site'),
(360, 1, 'site'),
(303, 1, 'site'),
(306, 1, 'site'),
(372, 1, 'site'),
(309, 1, 'site'),
(312, 1, 'site'),
(315, 1, 'site'),
(318, 1, 'site'),
(321, 1, 'site'),
(324, 1, 'site'),
(327, 1, 'site'),
(330, 1, 'site'),
(333, 1, 'site'),
(336, 1, 'site'),
(378, 1, 'site'),
(366, 1, 'site'),
(349, 1, ''),
(350, 1, ''),
(384, 1, 'site'),
(363, 1, 'site'),
(390, 1, 'site'),
(369, 1, 'site'),
(396, 1, 'site'),
(375, 1, 'site'),
(402, 1, 'site'),
(381, 1, 'site'),
(408, 1, 'site'),
(387, 1, 'site'),
(414, 1, 'site'),
(393, 1, 'site'),
(420, 1, 'site'),
(399, 1, 'site'),
(426, 1, 'site'),
(405, 1, 'site'),
(432, 1, 'site'),
(411, 1, 'site'),
(438, 1, 'site'),
(417, 1, 'site'),
(444, 1, 'site'),
(423, 1, 'site'),
(450, 1, 'site'),
(429, 1, 'site'),
(456, 1, 'site'),
(435, 1, 'site'),
(462, 1, 'site'),
(441, 1, 'site'),
(468, 1, 'site'),
(447, 1, 'site'),
(474, 1, 'site'),
(453, 1, 'site'),
(480, 1, 'site'),
(459, 1, 'site'),
(583, 1, 'site'),
(465, 1, 'site'),
(582, 1, ''),
(471, 1, 'site'),
(492, 1, 'site'),
(477, 1, 'site'),
(486, 1, 'site'),
(483, 1, 'site'),
(581, 1, ''),
(489, 1, 'site'),
(598, 1, 'book'),
(495, 1, 'site'),
(498, 1, 'site'),
(501, 1, 'site'),
(504, 1, 'site'),
(538, 1, 'site'),
(541, 1, 'site'),
(544, 1, 'site'),
(547, 1, 'site'),
(550, 1, 'site'),
(553, 1, 'site'),
(556, 1, 'site'),
(559, 1, 'site'),
(562, 1, 'site'),
(565, 1, 'site'),
(568, 1, 'site'),
(571, 1, 'site'),
(574, 1, 'site'),
(577, 1, 'site'),
(580, 1, 'site'),
(597, 1, 'site'),
(586, 1, 'site'),
(589, 1, 'site'),
(613, 1, ''),
(612, 1, ''),
(599, 1, 'book'),
(600, 1, 'book'),
(601, 1, 'book'),
(615, 1, 'book'),
(610, 1, 'site'),
(614, 1, 'site'),
(616, 1, 'book');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_address_format`
--

CREATE TABLE IF NOT EXISTS `go_address_format` (
  `id` int(11) NOT NULL,
  `format` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `go_address_format`
--

INSERT INTO `go_address_format` (`id`, `format`) VALUES
(1, '{address} {address_no}\r\n{zip} {city}\r\n{state}\r\n{country}'),
(2, '{address_no} {address}\r\n{city}, {state} {zip}\r\n{country}'),
(3, '{address}, {address_no}\r\n{zip} {city}\r\n{state} {country}'),
(4, '{address_no} {address}\r\n{city} {zip}\r\n{state} {country}'),
(5, '{address_no} {address}\r\n{zip} {city}\r\n{state} {country}'),
(6, '{address_no} {address}\r\n{city}\r\n{zip}\r\n{country}'),
(7, '{address_no} {address}\r\n{zip} {city} {state}\r\n{country}'),
(8, '{address_no} {address}, {city}\r\n{zip} {state}\r\n{country}'),
(9, '{address} {address_no} {zip} {city} {state} {country}');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_cache`
--

CREATE TABLE IF NOT EXISTS `go_cache` (
  `user_id` int(11) NOT NULL,
  `key` varchar(255) NOT NULL default '',
  `content` longtext,
  `mtime` int(11) NOT NULL,
  PRIMARY KEY  (`user_id`,`key`),
  KEY `mtime` (`mtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `go_cache`
--


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_countries`
--

CREATE TABLE IF NOT EXISTS `go_countries` (
  `id` int(11) NOT NULL default '0',
  `name` varchar(64) default NULL,
  `iso_code_2` char(2) NOT NULL default '',
  `iso_code_3` char(3) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `go_countries`
--


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_db_sequence`
--

CREATE TABLE IF NOT EXISTS `go_db_sequence` (
  `seq_name` varchar(50) NOT NULL default '',
  `nextid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`seq_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `go_db_sequence`
--

INSERT INTO `go_db_sequence` (`seq_name`, `nextid`) VALUES
('go_users', 1),
('go_groups', 3),
('go_acl_items', 616),
('cal_views', 1),
('ab_addressbooks', 3),
('fs_templates', 2),
('no_categories', 1),
('fs_folders', 226),
('bs_languages', 1),
('bs_books', 11),
('bs_templates', 11),
('bs_order_statuses', 48),
('bs_expense_books', 1),
('bs_expense_categories', 4),
('go_log', 252),
('cal_calendars', 1),
('ta_lists', 1),
('cms_sites', 166),
('cms_folders', 408),
('cms_files', 687),
('bs_product_categories', 160),
('bs_products', 291),
('ws_webshops', 73),
('ws_payments', 5),
('bs_orders', 88),
('bs_items', 87),
('bs_order_status_history', 86),
('no_notes', 2);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_groups`
--

CREATE TABLE IF NOT EXISTS `go_groups` (
  `id` int(11) NOT NULL default '0',
  `name` varchar(50) default NULL,
  `user_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `go_groups`
--

INSERT INTO `go_groups` (`id`, `name`, `user_id`) VALUES
(1, 'Beheerders', 1),
(2, 'Iedereen', 1),
(3, 'Intern', 1);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_iso_address_format`
--

CREATE TABLE IF NOT EXISTS `go_iso_address_format` (
  `iso` varchar(2) NOT NULL,
  `address_format_id` int(11) NOT NULL,
  PRIMARY KEY  (`iso`,`address_format_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `go_iso_address_format`
--

INSERT INTO `go_iso_address_format` (`iso`, `address_format_id`) VALUES
('AD', 1),
('AE', 1),
('AF', 1),
('AG', 1),
('AI', 1),
('AL', 1),
('AM', 1),
('AN', 1),
('AO', 1),
('AQ', 1),
('AR', 1),
('AS', 1),
('AT', 1),
('AU', 1),
('AW', 1),
('AX', 1),
('AZ', 1),
('BA', 1),
('BB', 1),
('BD', 1),
('BE', 3),
('BF', 1),
('BG', 1),
('BH', 1),
('BI', 1),
('BJ', 1),
('BM', 1),
('BN', 1),
('BO', 1),
('BR', 1),
('BS', 1),
('BT', 1),
('BV', 1),
('BW', 1),
('BY', 1),
('BZ', 1),
('CA', 1),
('CC', 1),
('CD', 1),
('CF', 1),
('CG', 1),
('CH', 1),
('CI', 1),
('CK', 1),
('CL', 1),
('CM', 1),
('CN', 8),
('CO', 1),
('CR', 1),
('CU', 1),
('CV', 1),
('CX', 1),
('CY', 1),
('CZ', 1),
('DE', 1),
('DJ', 1),
('DK', 1),
('DM', 1),
('DO', 1),
('DZ', 1),
('EC', 1),
('EE', 1),
('EG', 1),
('EH', 1),
('ER', 1),
('ES', 3),
('ET', 1),
('FI', 1),
('FJ', 1),
('FK', 1),
('FM', 1),
('FO', 1),
('FR', 5),
('FX', 1),
('GA', 1),
('GB', 6),
('GD', 1),
('GE', 1),
('GF', 1),
('GG', 1),
('GH', 1),
('GI', 1),
('GL', 1),
('GM', 1),
('GN', 1),
('GP', 1),
('GQ', 1),
('GR', 1),
('GS', 1),
('GT', 1),
('GU', 1),
('GW', 1),
('GY', 1),
('HK', 1),
('HM', 1),
('HN', 1),
('HR', 1),
('HT', 1),
('HU', 1),
('ID', 1),
('IE', 1),
('IL', 1),
('IN', 1),
('IO', 1),
('IQ', 1),
('IR', 1),
('IS', 1),
('IT', 7),
('JM', 1),
('JO', 1),
('JP', 1),
('KE', 1),
('KG', 1),
('KH', 1),
('KI', 1),
('KM', 1),
('KN', 1),
('KW', 1),
('KY', 1),
('KZ', 1),
('LA', 1),
('LB', 1),
('LC', 1),
('LI', 1),
('LK', 1),
('LR', 1),
('LS', 1),
('LT', 1),
('LU', 1),
('LV', 1),
('LY', 1),
('MA', 1),
('MC', 1),
('MD', 1),
('MG', 1),
('MH', 1),
('MK', 1),
('ML', 1),
('MM', 1),
('MN', 1),
('MO', 1),
('MP', 1),
('MQ', 1),
('MR', 1),
('MS', 1),
('MT', 1),
('MU', 1),
('MV', 1),
('MW', 1),
('MX', 1),
('MY', 1),
('MZ', 1),
('NA', 1),
('NC', 1),
('NE', 1),
('NF', 1),
('NG', 1),
('NI', 1),
('NL', 1),
('NO', 1),
('NP', 1),
('NR', 1),
('NU', 1),
('NZ', 1),
('OM', 1),
('PA', 1),
('PE', 1),
('PF', 1),
('PG', 1),
('PH', 1),
('PL', 1),
('PM', 1),
('PN', 1),
('PR', 1),
('PT', 1),
('PW', 1),
('PY', 1),
('QA', 1),
('RE', 1),
('RO', 1),
('RU', 1),
('RW', 1),
('SA', 1),
('SB', 1),
('SC', 1),
('SD', 1),
('SE', 1),
('SG', 4),
('SH', 1),
('SI', 1),
('SJ', 1),
('SK', 1),
('SL', 1),
('SM', 1),
('SN', 1),
('SO', 1),
('SR', 1),
('ST', 1),
('SV', 1),
('SY', 1),
('SZ', 1),
('TC', 1),
('TD', 1),
('TF', 1),
('TG', 1),
('TH', 1),
('TJ', 1),
('TK', 1),
('TM', 1),
('TN', 1),
('TO', 1),
('TP', 1),
('TR', 1),
('TT', 1),
('TV', 1),
('TW', 1),
('TZ', 1),
('UA', 1),
('UG', 1),
('UM', 1),
('US', 2),
('UY', 1),
('UZ', 1),
('VA', 1),
('VC', 1),
('VE', 1),
('VG', 1),
('VI', 1),
('VU', 1),
('WF', 1),
('WS', 1),
('YE', 1),
('YT', 1),
('YU', 1),
('ZA', 1),
('ZM', 1),
('ZW', 1);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_links_1`
--

CREATE TABLE IF NOT EXISTS `go_links_1` (
  `id` int(11) NOT NULL,
  `folder_id` int(11) NOT NULL,
  `link_id` int(11) NOT NULL,
  `link_type` int(11) NOT NULL,
  `description` varchar(100) default NULL,
  `ctime` int(11) NOT NULL,
  KEY `link_id` (`link_id`,`link_type`),
  KEY `id` (`id`,`folder_id`),
  KEY `ctime` (`ctime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `go_links_1`
--


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_links_2`
--

CREATE TABLE IF NOT EXISTS `go_links_2` (
  `id` int(11) NOT NULL,
  `folder_id` int(11) NOT NULL,
  `link_id` int(11) NOT NULL,
  `link_type` int(11) NOT NULL,
  `description` varchar(100) default NULL,
  `ctime` int(11) NOT NULL,
  KEY `link_id` (`link_id`,`link_type`),
  KEY `id` (`id`,`folder_id`),
  KEY `ctime` (`ctime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `go_links_2`
--


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_links_3`
--

CREATE TABLE IF NOT EXISTS `go_links_3` (
  `id` int(11) NOT NULL,
  `folder_id` int(11) NOT NULL,
  `link_id` int(11) NOT NULL,
  `link_type` int(11) NOT NULL,
  `description` varchar(100) default NULL,
  `ctime` int(11) NOT NULL,
  KEY `link_id` (`link_id`,`link_type`),
  KEY `id` (`id`,`folder_id`),
  KEY `ctime` (`ctime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `go_links_3`
--


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_links_4`
--

CREATE TABLE IF NOT EXISTS `go_links_4` (
  `id` int(11) NOT NULL,
  `folder_id` int(11) NOT NULL,
  `link_id` int(11) NOT NULL,
  `link_type` int(11) NOT NULL,
  `description` varchar(100) default NULL,
  `ctime` int(11) NOT NULL,
  KEY `link_id` (`link_id`,`link_type`),
  KEY `id` (`id`,`folder_id`),
  KEY `ctime` (`ctime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `go_links_4`
--


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_links_6`
--

CREATE TABLE IF NOT EXISTS `go_links_6` (
  `id` int(11) NOT NULL,
  `folder_id` int(11) NOT NULL,
  `link_id` int(11) NOT NULL,
  `link_type` int(11) NOT NULL,
  `description` varchar(100) default NULL,
  `ctime` int(11) NOT NULL,
  KEY `link_id` (`link_id`,`link_type`),
  KEY `id` (`id`,`folder_id`),
  KEY `ctime` (`ctime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `go_links_6`
--


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_links_7`
--

CREATE TABLE IF NOT EXISTS `go_links_7` (
  `id` int(11) NOT NULL,
  `folder_id` int(11) NOT NULL,
  `link_id` int(11) NOT NULL,
  `link_type` int(11) NOT NULL,
  `description` varchar(100) default NULL,
  `ctime` int(11) NOT NULL,
  KEY `link_id` (`link_id`,`link_type`),
  KEY `id` (`id`,`folder_id`),
  KEY `ctime` (`ctime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `go_links_7`
--

INSERT INTO `go_links_7` (`id`, `folder_id`, `link_id`, `link_type`, `description`, `ctime`) VALUES
(1, 0, 1, 8, '', 1255610005),
(2, 0, 1, 8, '', 1255610311),
(2, 0, 3, 7, '', 1255610362),
(3, 0, 2, 7, '', 1255610362),
(4, 0, 1, 8, '', 1255610733),
(5, 0, 1, 8, '', 1255611055),
(5, 0, 6, 7, '', 1255611070),
(6, 0, 5, 7, '', 1255611070),
(5, 0, 7, 7, '', 1255675624),
(7, 0, 5, 7, '', 1255675624),
(8, 0, 1, 8, '', 1255958630),
(9, 0, 1, 8, '', 1255959006),
(10, 0, 1, 8, '', 1256036740),
(11, 0, 1, 8, '', 1256036966),
(12, 0, 1, 8, '', 1256037664),
(13, 0, 1, 8, '', 1256037706),
(14, 0, 1, 8, '', 1256044841),
(15, 0, 1, 8, '', 1256045109),
(16, 0, 1, 8, '', 1256045123),
(17, 0, 1, 8, '', 1256045424),
(18, 0, 1, 8, '', 1256045806),
(19, 0, 1, 8, '', 1256045833),
(20, 0, 1, 8, '', 1256045892),
(21, 0, 1, 8, '', 1256045928),
(22, 0, 1, 8, '', 1256048092),
(23, 0, 1, 8, '', 1256049007),
(24, 0, 1, 8, '', 1256050481),
(25, 0, 1, 8, '', 1256050700),
(26, 0, 1, 8, '', 1256050720),
(27, 0, 1, 8, '', 1256050880),
(28, 0, 1, 8, '', 1256050965),
(29, 0, 1, 8, '', 1256051082),
(30, 0, 1, 8, '', 1256106325),
(31, 0, 1, 8, '', 1256106436),
(32, 0, 1, 8, '', 1256106655),
(33, 0, 1, 8, '', 1256106727),
(34, 0, 1, 8, '', 1256106778),
(35, 0, 1, 8, '', 1256106827),
(36, 0, 1, 8, '', 1256107034),
(37, 0, 1, 8, '', 1256107076),
(38, 0, 1, 8, '', 1256111276),
(39, 0, 1, 8, '', 1256113255),
(39, 0, 40, 7, '', 1256113280),
(40, 0, 39, 7, '', 1256113280),
(41, 0, 1, 8, '', 1256116366),
(42, 0, 1, 8, '', 1256118245),
(43, 0, 1, 8, '', 1256118707),
(44, 0, 1, 8, '', 1256119281),
(45, 0, 1, 8, '', 1256120853),
(46, 0, 1, 8, '', 1256120913),
(47, 0, 1, 8, '', 1256121009),
(48, 0, 1, 8, '', 1256123294),
(48, 0, 49, 7, '', 1256123303),
(49, 0, 48, 7, '', 1256123303),
(50, 0, 1, 8, '', 1256123852),
(50, 0, 51, 7, '', 1256124394),
(51, 0, 50, 7, '', 1256124394),
(52, 0, 1, 8, '', 1256124815),
(52, 0, 53, 7, '', 1256124829),
(53, 0, 52, 7, '', 1256124829),
(54, 0, 1, 8, '', 1256125172),
(55, 0, 1, 8, '', 1256125191),
(55, 0, 56, 7, '', 1256125220),
(56, 0, 55, 7, '', 1256125220),
(57, 0, 1, 8, '', 1256126402),
(57, 0, 58, 7, '', 1256126562),
(58, 0, 57, 7, '', 1256126562),
(59, 0, 1, 8, '', 1256126628),
(59, 0, 60, 7, '', 1256126659),
(60, 0, 59, 7, '', 1256126659),
(61, 0, 1, 8, '', 1256127055),
(61, 0, 62, 7, '', 1256127084),
(62, 0, 61, 7, '', 1256127084),
(63, 0, 1, 8, '', 1256127721),
(63, 0, 64, 7, '', 1256127736),
(64, 0, 63, 7, '', 1256127736),
(65, 0, 1, 8, '', 1256130713),
(65, 0, 66, 7, '', 1256130732),
(66, 0, 65, 7, '', 1256130732),
(65, 0, 67, 7, '', 1256130773),
(67, 0, 65, 7, '', 1256130773),
(68, 0, 1, 8, '', 1256133235),
(68, 0, 69, 7, '', 1256133496),
(69, 0, 68, 7, '', 1256133496),
(70, 0, 1, 8, '', 1256133657),
(71, 0, 1, 8, '', 1256134025),
(72, 0, 1, 8, '', 1256136267),
(73, 0, 1, 8, '', 1256136294),
(74, 0, 1, 8, '', 1256136692),
(76, 0, 1, 8, '', 1256136832),
(76, 0, 77, 7, '', 1256136859),
(77, 0, 76, 7, '', 1256136859),
(78, 0, 1, 8, '', 1256136928),
(78, 0, 79, 7, '', 1256136952),
(79, 0, 78, 7, '', 1256136952),
(80, 0, 1, 8, '', 1256137002),
(81, 0, 1, 8, '', 1256193362),
(81, 0, 82, 7, '', 1256193377),
(82, 0, 81, 7, '', 1256193377),
(83, 0, 1, 8, '', 1256193526),
(83, 0, 84, 7, '', 1256193542),
(84, 0, 83, 7, '', 1256193542),
(85, 0, 1, 8, '', 1256196160),
(85, 0, 86, 7, '', 1256196218),
(86, 0, 85, 7, '', 1256196218),
(87, 0, 1, 8, '', 1256196291),
(87, 0, 88, 7, '', 1256196345),
(88, 0, 87, 7, '', 1256196345);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_links_8`
--

CREATE TABLE IF NOT EXISTS `go_links_8` (
  `id` int(11) NOT NULL,
  `folder_id` int(11) NOT NULL,
  `link_id` int(11) NOT NULL,
  `link_type` int(11) NOT NULL,
  `description` varchar(100) NOT NULL,
  `ctime` int(11) NOT NULL,
  KEY `link_id` (`link_id`,`link_type`),
  KEY `id` (`id`,`folder_id`),
  KEY `ctime` (`ctime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `go_links_8`
--

INSERT INTO `go_links_8` (`id`, `folder_id`, `link_id`, `link_type`, `description`, `ctime`) VALUES
(1, 0, 1, 7, '', 1255610005),
(1, 0, 2, 7, '', 1255610311),
(1, 0, 4, 7, '', 1255610733),
(1, 0, 5, 7, '', 1255611055),
(1, 0, 8, 7, '', 1255958630),
(1, 0, 9, 7, '', 1255959006),
(1, 0, 10, 7, '', 1256036740),
(1, 0, 11, 7, '', 1256036966),
(1, 0, 12, 7, '', 1256037664),
(1, 0, 13, 7, '', 1256037706),
(1, 0, 14, 7, '', 1256044841),
(1, 0, 15, 7, '', 1256045109),
(1, 0, 16, 7, '', 1256045123),
(1, 0, 17, 7, '', 1256045424),
(1, 0, 18, 7, '', 1256045806),
(1, 0, 19, 7, '', 1256045833),
(1, 0, 20, 7, '', 1256045892),
(1, 0, 21, 7, '', 1256045928),
(1, 0, 22, 7, '', 1256048092),
(1, 0, 23, 7, '', 1256049007),
(1, 0, 24, 7, '', 1256050481),
(1, 0, 25, 7, '', 1256050700),
(1, 0, 26, 7, '', 1256050720),
(1, 0, 27, 7, '', 1256050880),
(1, 0, 28, 7, '', 1256050965),
(1, 0, 29, 7, '', 1256051082),
(1, 0, 30, 7, '', 1256106325),
(1, 0, 31, 7, '', 1256106436),
(1, 0, 32, 7, '', 1256106655),
(1, 0, 33, 7, '', 1256106727),
(1, 0, 34, 7, '', 1256106778),
(1, 0, 35, 7, '', 1256106827),
(1, 0, 36, 7, '', 1256107034),
(1, 0, 37, 7, '', 1256107076),
(1, 0, 38, 7, '', 1256111276),
(1, 0, 39, 7, '', 1256113255),
(1, 0, 41, 7, '', 1256116366),
(1, 0, 42, 7, '', 1256118245),
(1, 0, 43, 7, '', 1256118707),
(1, 0, 44, 7, '', 1256119281),
(1, 0, 45, 7, '', 1256120853),
(1, 0, 46, 7, '', 1256120913),
(1, 0, 47, 7, '', 1256121009),
(1, 0, 48, 7, '', 1256123294),
(1, 0, 50, 7, '', 1256123852),
(1, 0, 52, 7, '', 1256124815),
(1, 0, 54, 7, '', 1256125172),
(1, 0, 55, 7, '', 1256125191),
(1, 0, 57, 7, '', 1256126402),
(1, 0, 59, 7, '', 1256126628),
(1, 0, 61, 7, '', 1256127055),
(1, 0, 63, 7, '', 1256127721),
(1, 0, 65, 7, '', 1256130713),
(1, 0, 68, 7, '', 1256133235),
(1, 0, 70, 7, '', 1256133657),
(1, 0, 71, 7, '', 1256134025),
(1, 0, 72, 7, '', 1256136267),
(1, 0, 73, 7, '', 1256136294),
(1, 0, 74, 7, '', 1256136692),
(1, 0, 76, 7, '', 1256136832),
(1, 0, 78, 7, '', 1256136928),
(1, 0, 80, 7, '', 1256137002),
(1, 0, 81, 7, '', 1256193362),
(1, 0, 83, 7, '', 1256193526),
(1, 0, 85, 7, '', 1256196160),
(1, 0, 87, 7, '', 1256196291);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_links_9`
--

CREATE TABLE IF NOT EXISTS `go_links_9` (
  `id` int(11) NOT NULL,
  `folder_id` int(11) NOT NULL,
  `link_id` int(11) NOT NULL,
  `link_type` int(11) NOT NULL,
  `description` varchar(100) default NULL,
  `ctime` int(11) NOT NULL,
  KEY `link_id` (`link_id`,`link_type`),
  KEY `id` (`id`,`folder_id`),
  KEY `ctime` (`ctime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `go_links_9`
--


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_links_12`
--

CREATE TABLE IF NOT EXISTS `go_links_12` (
  `id` int(11) NOT NULL,
  `folder_id` int(11) NOT NULL,
  `link_id` int(11) NOT NULL,
  `link_type` int(11) NOT NULL,
  `description` varchar(100) default NULL,
  `ctime` int(11) NOT NULL,
  KEY `link_id` (`link_id`,`link_type`),
  KEY `id` (`id`,`folder_id`),
  KEY `ctime` (`ctime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `go_links_12`
--


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_link_descriptions`
--

CREATE TABLE IF NOT EXISTS `go_link_descriptions` (
  `id` int(11) NOT NULL,
  `description` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `go_link_descriptions`
--


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_link_folders`
--

CREATE TABLE IF NOT EXISTS `go_link_folders` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `link_id` int(11) NOT NULL,
  `link_type` int(11) NOT NULL,
  `name` varchar(50) default NULL,
  PRIMARY KEY  (`id`),
  KEY `link_id` (`link_id`,`link_type`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `go_link_folders`
--


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_log`
--

CREATE TABLE IF NOT EXISTS `go_log` (
  `id` int(11) NOT NULL,
  `link_id` int(11) NOT NULL,
  `link_type` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `text` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `link_type` (`link_type`,`time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `go_log`
--

INSERT INTO `go_log` (`id`, `link_id`, `link_type`, `time`, `user_id`, `text`) VALUES
(1, 1, 8, 1253802195, 0, 'Added Group-Office Beheerder'),
(2, 0, 0, 1253802223, 1, 'Logged in'),
(3, 0, 0, 1254811465, 1, 'Logged in'),
(4, 0, 0, 1254818500, 1, 'Logged in'),
(5, 0, 0, 1254983171, 1, 'Logged in'),
(6, 0, 0, 1255329633, 1, 'Logged in'),
(7, 1, 8, 1255610005, 1, 'Updated Group-Office Beheerder'),
(8, 1, 7, 1255610005, 1, 'Added  (Group-Office Beheerder)'),
(9, 1, 8, 1255610310, 1, 'Updated Group-Office Beheerder'),
(10, 2, 7, 1255610311, 1, 'Added  (Group-Office Beheerder)'),
(11, 2, 7, 1255610311, 1, 'Updated O2009000001 (Group-Office Beheerder)'),
(12, 2, 7, 1255610362, 1, 'Updated O2009000001 (Group-Office Beheerder)'),
(13, 3, 7, 1255610362, 1, 'Added  (Group-Office Beheerder)'),
(14, 0, 0, 1255610437, 1, 'Logged in'),
(15, 1, 8, 1255610733, 1, 'Updated Group-Office Beheerder'),
(16, 4, 7, 1255610733, 1, 'Added  (Group-Office Beheerder)'),
(17, 4, 7, 1255610733, 1, 'Updated O2009000002 (Group-Office Beheerder)'),
(18, 1, 8, 1255611055, 1, 'Updated Group-Office Beheerder'),
(19, 5, 7, 1255611055, 1, 'Added  (Group-Office Beheerder)'),
(20, 5, 7, 1255611055, 1, 'Updated O2009000003 (Group-Office Beheerder)'),
(21, 5, 7, 1255611070, 1, 'Updated O2009000003 (Group-Office Beheerder)'),
(22, 6, 7, 1255611070, 1, 'Added  (Group-Office Beheerder)'),
(23, 5, 7, 1255675623, 1, 'Updated O2009000003 (Group-Office Beheerder)'),
(24, 7, 7, 1255675624, 1, 'Added  (Group-Office Beheerder)'),
(25, 1, 4, 1255937467, 1, 'Added Aan het werk!'),
(26, 2, 4, 1255937721, 1, 'Added Recursie'),
(27, 1, 8, 1255958630, 1, 'Updated Group-Office Beheerder'),
(28, 8, 7, 1255958630, 1, 'Added  (Group-Office Beheerder)'),
(29, 8, 7, 1255958630, 1, 'Updated 2009-000001 (Group-Office Beheerder)'),
(30, 1, 8, 1255959006, 1, 'Updated Group-Office Beheerder'),
(31, 9, 7, 1255959006, 1, 'Added  (Group-Office Beheerder)'),
(32, 9, 7, 1255959006, 1, 'Updated 2009-000002 (Group-Office Beheerder)'),
(33, 2, 4, 1255959174, 1, 'Deleted Recursie'),
(34, 1, 4, 1255959176, 1, 'Deleted Aan het werk!'),
(35, 0, 0, 1255964118, 1, 'Logged in'),
(36, 1, 8, 1256036740, 1, 'Updated Group-Office Beheerder'),
(37, 10, 7, 1256036740, 1, 'Added  (Group-Office Beheerder)'),
(38, 10, 7, 1256036740, 1, 'Updated 2009-000001 (Group-Office Beheerder)'),
(39, 1, 8, 1256036966, 1, 'Updated Group-Office Beheerder'),
(40, 11, 7, 1256036966, 1, 'Added  (Group-Office Beheerder)'),
(41, 11, 7, 1256036966, 1, 'Updated 2009-000002 (Group-Office Beheerder)'),
(42, 1, 8, 1256037664, 1, 'Updated Group-Office Beheerder'),
(43, 12, 7, 1256037664, 1, 'Added  (Group-Office Beheerder)'),
(44, 12, 7, 1256037664, 1, 'Updated 2009-000003 (Group-Office Beheerder)'),
(45, 1, 8, 1256037706, 1, 'Updated Group-Office Beheerder'),
(46, 13, 7, 1256037706, 1, 'Added  (Group-Office Beheerder)'),
(47, 13, 7, 1256037706, 1, 'Updated 2009-000004 (Group-Office Beheerder)'),
(48, 1, 8, 1256044841, 1, 'Updated Group-Office Beheerder'),
(49, 14, 7, 1256044841, 1, 'Added  (Group-Office Beheerder)'),
(50, 14, 7, 1256044841, 1, 'Updated 2009-000005 (Group-Office Beheerder)'),
(51, 1, 8, 1256045109, 1, 'Updated Group-Office Beheerder'),
(52, 15, 7, 1256045109, 1, 'Added  (Group-Office Beheerder)'),
(53, 15, 7, 1256045109, 1, 'Updated 2009-000006 (Group-Office Beheerder)'),
(54, 1, 8, 1256045123, 1, 'Updated Group-Office Beheerder'),
(55, 16, 7, 1256045123, 1, 'Added  (Group-Office Beheerder)'),
(56, 16, 7, 1256045123, 1, 'Updated 2009-000007 (Group-Office Beheerder)'),
(57, 1, 8, 1256045424, 1, 'Updated Group-Office Beheerder'),
(58, 17, 7, 1256045424, 1, 'Added  (Group-Office Beheerder)'),
(59, 17, 7, 1256045424, 1, 'Updated 2009-000008 (Group-Office Beheerder)'),
(60, 1, 8, 1256045806, 1, 'Updated Group-Office Beheerder'),
(61, 18, 7, 1256045806, 1, 'Added  (Group-Office Beheerder)'),
(62, 18, 7, 1256045806, 1, 'Updated 2009-000009 (Group-Office Beheerder)'),
(63, 1, 8, 1256045833, 1, 'Updated Group-Office Beheerder'),
(64, 19, 7, 1256045833, 1, 'Added  (Group-Office Beheerder)'),
(65, 19, 7, 1256045833, 1, 'Updated 2009-000010 (Group-Office Beheerder)'),
(66, 1, 8, 1256045892, 1, 'Updated Group-Office Beheerder'),
(67, 20, 7, 1256045892, 1, 'Added  (Group-Office Beheerder)'),
(68, 20, 7, 1256045892, 1, 'Updated 2009-000011 (Group-Office Beheerder)'),
(69, 1, 8, 1256045928, 1, 'Updated Group-Office Beheerder'),
(70, 21, 7, 1256045928, 1, 'Added  (Group-Office Beheerder)'),
(71, 21, 7, 1256045928, 1, 'Updated 2009-000012 (Group-Office Beheerder)'),
(72, 1, 8, 1256048092, 1, 'Updated Group-Office Beheerder'),
(73, 22, 7, 1256048092, 1, 'Added  (Group-Office Beheerder)'),
(74, 22, 7, 1256048092, 1, 'Updated 2009-000013 (Group-Office Beheerder)'),
(75, 1, 8, 1256049007, 1, 'Updated Group-Office Beheerder'),
(76, 23, 7, 1256049007, 1, 'Added  (Group-Office Beheerder)'),
(77, 23, 7, 1256049007, 1, 'Updated 2009-000014 (Group-Office Beheerder)'),
(78, 1, 8, 1256050481, 1, 'Updated Group-Office Beheerder'),
(79, 24, 7, 1256050481, 1, 'Added  (Waldemar de Beursekond)'),
(80, 24, 7, 1256050481, 1, 'Updated 2009-000015 (Waldemar de Beursekond)'),
(81, 1, 8, 1256050700, 1, 'Updated Group-Office Beheerder'),
(82, 25, 7, 1256050700, 1, 'Added  (Waldemar de Beursekond)'),
(83, 25, 7, 1256050700, 1, 'Updated 2009-000016 (Waldemar de Beursekond)'),
(84, 1, 8, 1256050720, 1, 'Updated Group-Office Beheerder'),
(85, 26, 7, 1256050720, 1, 'Added  (Waldemar de Beursekond)'),
(86, 26, 7, 1256050720, 1, 'Updated 2009-000017 (Waldemar de Beursekond)'),
(87, 1, 8, 1256050880, 1, 'Updated Group-Office Beheerder'),
(88, 27, 7, 1256050880, 1, 'Added  (Waldemar de Beursekond)'),
(89, 27, 7, 1256050880, 1, 'Updated 2009-000018 (Waldemar de Beursekond)'),
(90, 1, 8, 1256050965, 1, 'Updated Group-Office Beheerder'),
(91, 28, 7, 1256050965, 1, 'Added  (Waldemar de Beursekond)'),
(92, 28, 7, 1256050966, 1, 'Updated 2009-000019 (Waldemar de Beursekond)'),
(93, 1, 8, 1256051082, 1, 'Updated Group-Office Beheerder'),
(94, 29, 7, 1256051082, 1, 'Added  (Waldemar de Beursekond)'),
(95, 29, 7, 1256051082, 1, 'Updated 2009-000020 (Waldemar de Beursekond)'),
(96, 1, 8, 1256106325, 1, 'Updated Group-Office Beheerder'),
(97, 30, 7, 1256106325, 1, 'Added  (Waldemar de Beursekond)'),
(98, 30, 7, 1256106325, 1, 'Updated 2009-000021 (Waldemar de Beursekond)'),
(99, 1, 8, 1256106436, 1, 'Updated Group-Office Beheerder'),
(100, 31, 7, 1256106436, 1, 'Added  (Waldemar de Beursekond)'),
(101, 31, 7, 1256106436, 1, 'Updated 2009-000022 (Waldemar de Beursekond)'),
(102, 1, 8, 1256106655, 1, 'Updated Group-Office Beheerder'),
(103, 32, 7, 1256106655, 1, 'Added  (Waldemar de Beursekond)'),
(104, 32, 7, 1256106655, 1, 'Updated 2009-000023 (Waldemar de Beursekond)'),
(105, 1, 8, 1256106727, 1, 'Updated Group-Office Beheerder'),
(106, 33, 7, 1256106727, 1, 'Added  (Waldemar de Beursekond)'),
(107, 33, 7, 1256106727, 1, 'Updated 2009-000024 (Waldemar de Beursekond)'),
(108, 1, 8, 1256106778, 1, 'Updated Group-Office Beheerder'),
(109, 34, 7, 1256106778, 1, 'Added  (Waldemar de Beursekond)'),
(110, 34, 7, 1256106778, 1, 'Updated 2009-000025 (Waldemar de Beursekond)'),
(111, 1, 8, 1256106827, 1, 'Updated Group-Office Beheerder'),
(112, 35, 7, 1256106827, 1, 'Added  (Waldemar de Beursekond)'),
(113, 35, 7, 1256106827, 1, 'Updated 2009-000026 (Waldemar de Beursekond)'),
(114, 1, 8, 1256107034, 1, 'Updated Group-Office Beheerder'),
(115, 36, 7, 1256107034, 1, 'Added  (Waldemar de Beursekond)'),
(116, 36, 7, 1256107034, 1, 'Updated 2009-000027 (Waldemar de Beursekond)'),
(117, 1, 8, 1256107076, 1, 'Updated Group-Office Beheerder'),
(118, 37, 7, 1256107076, 1, 'Added  (Waldemar de Beursekond)'),
(119, 37, 7, 1256107076, 1, 'Updated 2009-000028 (Waldemar de Beursekond)'),
(120, 1, 8, 1256111276, 1, 'Updated Group-Office Beheerder'),
(121, 38, 7, 1256111276, 1, 'Added  (Waldemar de Beursekond)'),
(122, 38, 7, 1256111276, 1, 'Updated 2009-000029 (Waldemar de Beursekond)'),
(123, 1, 8, 1256113255, 1, 'Updated Group-Office Beheerder'),
(124, 39, 7, 1256113255, 1, 'Added  (Waldemar de Beursekond)'),
(125, 39, 7, 1256113255, 1, 'Updated 2009-000030 (Waldemar de Beursekond)'),
(126, 39, 7, 1256113280, 1, 'Updated 2009-000030 (Waldemar de Beursekond)'),
(127, 40, 7, 1256113280, 1, 'Added  (Waldemar de Beursekond)'),
(128, 1, 8, 1256116366, 1, 'Updated Group-Office Beheerder'),
(129, 41, 7, 1256116366, 1, 'Added  (Waldemar de Beursekond)'),
(130, 41, 7, 1256116366, 1, 'Updated 2009-000031 (Waldemar de Beursekond)'),
(131, 1, 8, 1256118245, 1, 'Updated Group-Office Beheerder'),
(132, 42, 7, 1256118245, 1, 'Added  (Waldemar de Beursekond)'),
(133, 42, 7, 1256118245, 1, 'Updated 2009-000032 (Waldemar de Beursekond)'),
(134, 1, 8, 1256118707, 1, 'Updated Group-Office Beheerder'),
(135, 43, 7, 1256118707, 1, 'Added  (Waldemar de Beursekond)'),
(136, 43, 7, 1256118707, 1, 'Updated 2009-000033 (Waldemar de Beursekond)'),
(137, 1, 8, 1256119281, 1, 'Updated Group-Office Beheerder'),
(138, 44, 7, 1256119281, 1, 'Added  (Waldemar de Beursekond)'),
(139, 44, 7, 1256119281, 1, 'Updated 2009-000034 (Waldemar de Beursekond)'),
(140, 1, 8, 1256120853, 1, 'Updated Group-Office Beheerder'),
(141, 45, 7, 1256120853, 1, 'Added  (Waldemar de Beursekond)'),
(142, 45, 7, 1256120853, 1, 'Updated 2009-000035 (Waldemar de Beursekond)'),
(143, 1, 8, 1256120913, 1, 'Updated Group-Office Beheerder'),
(144, 46, 7, 1256120913, 1, 'Added  (Waldemar de Beursekond)'),
(145, 46, 7, 1256120913, 1, 'Updated 2009-000036 (Waldemar de Beursekond)'),
(146, 1, 8, 1256121009, 1, 'Updated Group-Office Beheerder'),
(147, 47, 7, 1256121009, 1, 'Added  (Waldemar de Beursekond)'),
(148, 47, 7, 1256121009, 1, 'Updated 2009-000037 (Waldemar de Beursekond)'),
(149, 1, 8, 1256123294, 1, 'Updated Group-Office Beheerder'),
(150, 48, 7, 1256123294, 1, 'Added  (Waldemar de Beursekond)'),
(151, 48, 7, 1256123294, 1, 'Updated 2009-000038 (Waldemar de Beursekond)'),
(152, 48, 7, 1256123303, 1, 'Updated 2009-000038 (Waldemar de Beursekond)'),
(153, 49, 7, 1256123303, 1, 'Added  (Waldemar de Beursekond)'),
(154, 1, 8, 1256123852, 1, 'Updated Group-Office Beheerder'),
(155, 50, 7, 1256123852, 1, 'Added  (Waldemar de Beursekond)'),
(156, 50, 7, 1256123852, 1, 'Updated 2009-000039 (Waldemar de Beursekond)'),
(157, 1, 7, 1256123868, 1, 'Updated O2009000004 (Group-Office Beheerder)'),
(158, 50, 7, 1256124394, 1, 'Updated 2009-000039 (Waldemar de Beursekond)'),
(159, 51, 7, 1256124394, 1, 'Added  (Waldemar de Beursekond)'),
(160, 1, 8, 1256124815, 1, 'Updated Group-Office Beheerder'),
(161, 52, 7, 1256124815, 1, 'Added  (Waldemar de Beursekond)'),
(162, 52, 7, 1256124815, 1, 'Updated 2009-000040 (Waldemar de Beursekond)'),
(163, 52, 7, 1256124829, 1, 'Updated 2009-000040 (Waldemar de Beursekond)'),
(164, 53, 7, 1256124829, 1, 'Added  (Waldemar de Beursekond)'),
(165, 1, 8, 1256125172, 1, 'Updated Group-Office Beheerder'),
(166, 54, 7, 1256125172, 1, 'Added  (Waldemar de Beursekond)'),
(167, 54, 7, 1256125172, 1, 'Updated 2009-000041 (Waldemar de Beursekond)'),
(168, 1, 8, 1256125191, 1, 'Updated Group-Office Beheerder'),
(169, 55, 7, 1256125191, 1, 'Added  (Waldemar de Beursekond)'),
(170, 55, 7, 1256125191, 1, 'Updated 2009-000042 (Waldemar de Beursekond)'),
(171, 55, 7, 1256125220, 1, 'Updated 2009-000042 (Waldemar de Beursekond)'),
(172, 56, 7, 1256125220, 1, 'Added  (Waldemar de Beursekond)'),
(173, 1, 8, 1256126402, 1, 'Updated Group-Office Beheerder'),
(174, 57, 7, 1256126402, 1, 'Added  (Waldemar de Beursekond)'),
(175, 57, 7, 1256126402, 1, 'Updated 2009-000043 (Waldemar de Beursekond)'),
(176, 57, 7, 1256126562, 1, 'Updated 2009-000043 (Waldemar de Beursekond)'),
(177, 58, 7, 1256126562, 1, 'Added  (Waldemar de Beursekond)'),
(178, 1, 8, 1256126628, 1, 'Updated Group-Office Beheerder'),
(179, 59, 7, 1256126628, 1, 'Added  (Waldemar de Beursekond)'),
(180, 59, 7, 1256126628, 1, 'Updated 2009-000044 (Waldemar de Beursekond)'),
(181, 59, 7, 1256126659, 1, 'Updated 2009-000044 (Waldemar de Beursekond)'),
(182, 60, 7, 1256126659, 1, 'Added  (Waldemar de Beursekond)'),
(183, 1, 8, 1256127055, 1, 'Updated Group-Office Beheerder'),
(184, 61, 7, 1256127055, 1, 'Added  (Waldemar de Beursekond)'),
(185, 61, 7, 1256127055, 1, 'Updated 2009-000045 (Waldemar de Beursekond)'),
(186, 61, 7, 1256127084, 1, 'Updated 2009-000045 (Waldemar de Beursekond)'),
(187, 62, 7, 1256127084, 1, 'Added  (Waldemar de Beursekond)'),
(188, 1, 8, 1256127721, 1, 'Updated Group-Office Beheerder'),
(189, 63, 7, 1256127721, 1, 'Added  (Waldemar de Beursekond)'),
(190, 63, 7, 1256127721, 1, 'Updated 2009-000046 (Waldemar de Beursekond)'),
(191, 63, 7, 1256127736, 1, 'Updated 2009-000046 (Waldemar de Beursekond)'),
(192, 64, 7, 1256127736, 1, 'Added  (Waldemar de Beursekond)'),
(193, 1, 8, 1256130713, 1, 'Updated Group-Office Beheerder'),
(194, 65, 7, 1256130713, 1, 'Added  (Waldemar de Beursekond)'),
(195, 65, 7, 1256130713, 1, 'Updated 2009-000047 (Waldemar de Beursekond)'),
(196, 65, 7, 1256130732, 1, 'Updated 2009-000047 (Waldemar de Beursekond)'),
(197, 66, 7, 1256130732, 1, 'Added  (Waldemar de Beursekond)'),
(198, 67, 7, 1256130773, 1, 'Added  (Waldemar de Beursekond)'),
(199, 1, 8, 1256133235, 1, 'Updated Group-Office Beheerder'),
(200, 68, 7, 1256133235, 1, 'Added  (Waldemar de Beursekond)'),
(201, 68, 7, 1256133235, 1, 'Updated 2009-000048 (Waldemar de Beursekond)'),
(202, 68, 7, 1256133496, 1, 'Updated 2009-000048 (Waldemar de Beursekond)'),
(203, 69, 7, 1256133496, 1, 'Added  (Waldemar de Beursekond)'),
(204, 1, 8, 1256133657, 1, 'Updated Group-Office Beheerder'),
(205, 70, 7, 1256133657, 1, 'Added  (Waldemar de Beursekond)'),
(206, 70, 7, 1256133657, 1, 'Updated 2009-000049 (Waldemar de Beursekond)'),
(207, 1, 8, 1256134025, 1, 'Updated Group-Office Beheerder'),
(208, 71, 7, 1256134025, 1, 'Added  (Waldemar de Beursekond)'),
(209, 71, 7, 1256134025, 1, 'Updated 2009-000050 (Waldemar de Beursekond)'),
(210, 1, 8, 1256136267, 1, 'Updated Group-Office Beheerder'),
(211, 72, 7, 1256136267, 1, 'Added  (Waldemar de Beursekond)'),
(212, 72, 7, 1256136267, 1, 'Updated 2009-000051 (Waldemar de Beursekond)'),
(213, 1, 8, 1256136294, 1, 'Updated Group-Office Beheerder'),
(214, 73, 7, 1256136294, 1, 'Added  (Waldemar de Beursekond)'),
(215, 73, 7, 1256136294, 1, 'Updated 2009-000052 (Waldemar de Beursekond)'),
(216, 1, 8, 1256136692, 1, 'Updated Group-Office Beheerder'),
(217, 74, 7, 1256136692, 1, 'Added  (Waldemar de Beursekond)'),
(218, 74, 7, 1256136692, 1, 'Updated 2009-000053 (Waldemar de Beursekond)'),
(219, 74, 7, 1256136704, 1, 'Updated 2009-000053 (Waldemar de Beursekond)'),
(220, 1, 8, 1256136832, 1, 'Updated Group-Office Beheerder'),
(221, 76, 7, 1256136832, 1, 'Added  (Waldemar de Beursekond)'),
(222, 76, 7, 1256136832, 1, 'Updated 2009-000054 (Waldemar de Beursekond)'),
(223, 76, 7, 1256136859, 1, 'Updated 2009-000054 (Waldemar de Beursekond)'),
(224, 77, 7, 1256136859, 1, 'Added  (Waldemar de Beursekond)'),
(225, 1, 8, 1256136928, 1, 'Updated Group-Office Beheerder'),
(226, 78, 7, 1256136928, 1, 'Added  (Waldemar de Beursekond)'),
(227, 78, 7, 1256136928, 1, 'Updated 2009-000055 (Waldemar de Beursekond)'),
(228, 78, 7, 1256136952, 1, 'Updated 2009-000055 (Waldemar de Beursekond)'),
(229, 79, 7, 1256136952, 1, 'Added  (Waldemar de Beursekond)'),
(230, 1, 8, 1256137002, 1, 'Updated Group-Office Beheerder'),
(231, 80, 7, 1256137002, 1, 'Added  (Waldemar de Beursekond)'),
(232, 80, 7, 1256137002, 1, 'Updated 2009-000056 (Waldemar de Beursekond)'),
(233, 1, 8, 1256193361, 1, 'Updated Group-Office Beheerder'),
(234, 81, 7, 1256193362, 1, 'Added  (Waldemar de Beursekond)'),
(235, 81, 7, 1256193362, 1, 'Updated 2009-000057 (Waldemar de Beursekond)'),
(236, 81, 7, 1256193377, 1, 'Updated 2009-000057 (Waldemar de Beursekond)'),
(237, 82, 7, 1256193377, 1, 'Added  (Waldemar de Beursekond)'),
(238, 1, 8, 1256193526, 1, 'Updated Group-Office Beheerder'),
(239, 83, 7, 1256193526, 1, 'Added  (Waldemar de Beursekond)'),
(240, 83, 7, 1256193526, 1, 'Updated 2009-000058 (Waldemar de Beursekond)'),
(241, 83, 7, 1256193542, 1, 'Updated 2009-000058 (Waldemar de Beursekond)'),
(242, 84, 7, 1256193542, 1, 'Added  (Waldemar de Beursekond)'),
(243, 1, 8, 1256196160, 1, 'Updated Group-Office Beheerder'),
(244, 85, 7, 1256196160, 1, 'Added  (Waldemar de Beursekond)'),
(245, 85, 7, 1256196160, 1, 'Updated 2009-000059 (Waldemar de Beursekond)'),
(246, 85, 7, 1256196218, 1, 'Updated 2009-000059 (Waldemar de Beursekond)'),
(247, 86, 7, 1256196218, 1, 'Added  (Waldemar de Beursekond)'),
(248, 1, 8, 1256196291, 1, 'Updated Group-Office Beheerder'),
(249, 87, 7, 1256196291, 1, 'Added  (Waldemar de Beursekond)'),
(250, 87, 7, 1256196291, 1, 'Updated 2009-000060 (Waldemar de Beursekond)'),
(251, 87, 7, 1256196345, 1, 'Updated 2009-000060 (Waldemar de Beursekond)'),
(252, 88, 7, 1256196345, 1, 'Added  (Waldemar de Beursekond)');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_mail_counter`
--

CREATE TABLE IF NOT EXISTS `go_mail_counter` (
  `host` varchar(100) NOT NULL default '',
  `date` date NOT NULL,
  `count` int(11) NOT NULL,
  PRIMARY KEY  (`host`),
  KEY `date` (`date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `go_mail_counter`
--


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_modules`
--

CREATE TABLE IF NOT EXISTS `go_modules` (
  `id` varchar(20) NOT NULL default '',
  `version` int(11) NOT NULL,
  `sort_order` int(11) NOT NULL default '0',
  `admin_menu` enum('0','1') NOT NULL default '0',
  `acl_read` int(11) NOT NULL default '0',
  `acl_write` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `go_modules`
--

INSERT INTO `go_modules` (`id`, `version`, `sort_order`, `admin_menu`, `acl_read`, `acl_write`) VALUES
('summary', 16, 1, '0', 1, 2),
('email', 16, 2, '0', 3, 4),
('calendar', 14, 3, '0', 5, 6),
('tasks', 8, 4, '0', 11, 12),
('addressbook', 38, 5, '0', 13, 14),
('files', 40, 6, '0', 21, 22),
('notes', 3, 7, '0', 29, 30),
('links', 0, 8, '0', 33, 34),
('users', 0, 9, '0', 35, 36),
('comments', 0, 10, '0', 37, 38),
('groups', 0, 11, '0', 39, 40),
('tools', 0, 12, '0', 41, 42),
('modules', 0, 13, '0', 43, 44),
('log', 0, 14, '0', 45, 46),
('billing', 38, 15, '0', 47, 48),
('webshop', 1, 17, '0', 612, 613),
('cms', 9, 16, '0', 581, 582);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_reminders`
--

CREATE TABLE IF NOT EXISTS `go_reminders` (
  `id` int(11) NOT NULL default '0',
  `link_id` int(11) NOT NULL default '0',
  `link_type` int(11) NOT NULL,
  `user_id` int(11) NOT NULL default '0',
  `name` varchar(100) default NULL,
  `time` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `link_id` (`link_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `go_reminders`
--


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_search_cache`
--

CREATE TABLE IF NOT EXISTS `go_search_cache` (
  `user_id` int(11) NOT NULL default '0',
  `table` varchar(50) default NULL,
  `id` int(11) NOT NULL default '0',
  `module` varchar(50) default NULL,
  `name` varchar(100) default NULL,
  `description` varchar(255) default NULL,
  `url` varchar(255) default NULL,
  `link_type` int(11) NOT NULL default '0',
  `type` varchar(20) default NULL,
  `keywords` text,
  `mtime` int(11) NOT NULL default '0',
  `acl_read` int(11) NOT NULL,
  `acl_write` int(11) NOT NULL,
  PRIMARY KEY  (`id`,`link_type`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `go_search_cache`
--

INSERT INTO `go_search_cache` (`user_id`, `table`, `id`, `module`, `name`, `description`, `url`, `link_type`, `type`, `keywords`, `mtime`, `acl_read`, `acl_write`) VALUES
(1, NULL, 1, 'users', 'Group-Office Beheerder', '', NULL, 8, 'Gebruiker', 'admin,21232f297a57a5a743894a0e4a801fc3,d2abaa37a7c3db1137d385e1d8c15fd2,Group-Office,Beheerder,M,w.vanbeusekom@gmail.com,Waldemar de Beursekond,NL,Gelderland,Den Bosch,5933BX,Reitscheweg,dmY,-,G:i,.,,,€,Europe/Amsterdam,summary,nl,Default,;,",Gebruiker', 1256196291, 35, 36),
(1, NULL, 1, 'billing', 'O2009000004 (Group-Office Beheerder)', '', NULL, 7, 'Order', 'O2009000004,8f54851aba87a5d820f0f83318b4323f,Group-Office Beheerder,Geachte heer Beheerder,fcncv,vnv,vnc,vnnvc,NL,webmaster@example.com,Order', 1256123868, 51, 52),
(1, NULL, 2, 'billing', 'O2009000001 (Group-Office Beheerder)', '', NULL, 7, 'Order', 'O2009000001,2075924bc240d03a4785b23b5e3e118b,Group-Office Beheerder,Geachte heer Beheerder,fcncv,vnv,vnc,vnnvc,NL,webmaster@example.com,Teletik Safepay Card,Order', 1255610362, 51, 52),
(1, NULL, 3, 'billing', '(Group-Office Beheerder)', '', NULL, 7, 'Order', 'd7e33fe674962f03e577dfb263eeb6eb,Group-Office Beheerder,Geachte heer Beheerder,fcncv,vnv,vnc,vnnvc,NL,webmaster@example.com,Teletik Safepay Card,Order', 1255610362, 53, 54),
(1, NULL, 4, 'billing', 'O2009000002 (Group-Office Beheerder)', '', NULL, 7, 'Order', 'O2009000002,4c5cf28df179e2f7ece522380d554133,Group-Office Beheerder,Geachte heer Beheerder,fcncv,vnv,vnc,vnnvc,NL,webmaster@example.com,Order', 1255610733, 51, 52),
(1, NULL, 5, 'billing', 'O2009000003 (Group-Office Beheerder)', '', NULL, 7, 'Order', 'O2009000003,f3eb69ffe248e9df2d4a89432ec2c889,Group-Office Beheerder,Geachte heer Beheerder,fcncv,vnv,vnc,vnnvc,NL,webmaster@example.com,Teletik Safepay Card,Order', 1255675623, 51, 52),
(1, NULL, 6, 'billing', '(Group-Office Beheerder)', '', NULL, 7, 'Order', 'ed03bd78284739b3bdd69f4207d5f305,Group-Office Beheerder,Geachte heer Beheerder,fcncv,vnv,vnc,vnnvc,NL,webmaster@example.com,Teletik Safepay Card,Order', 1255611070, 53, 54),
(1, NULL, 7, 'billing', '(Group-Office Beheerder)', '', NULL, 7, 'Order', '7b734917e682a280ab121b5c6ccf8e90,Group-Office Beheerder,Geachte heer Beheerder,fcncv,vnv,vnc,vnnvc,NL,webmaster@example.com,Teletik Safepay Card,Order', 1255675623, 53, 54),
(1, NULL, 10, 'billing', '2009-000001 (Group-Office Beheerder)', '', NULL, 7, 'Order', '2009-000001,5069b5a8bcde708dfe1fb4771e69c44b,Group-Office Beheerder,Geachte heer Beheerder,fcncv,vnv,vnc,vnnvc,NL,webmaster@example.com,Order', 1256036740, 615, 616),
(1, NULL, 8, 'billing', '2009-000001 (Group-Office Beheerder)', '', NULL, 7, 'Order', '2009-000001,16d0e718d9f29ca78e46a21f1b41a3fb,Group-Office Beheerder,Geachte heer Beheerder,fcncv,vnv,vnc,vnnvc,NL,webmaster@example.com,Order', 1255958630, 606, 607),
(1, NULL, 9, 'billing', '2009-000002 (Group-Office Beheerder)', '', NULL, 7, 'Order', '2009-000002,4806786c50b847874c7688f879d52f83,Group-Office Beheerder,Geachte heer Beheerder,fcncv,vnv,vnc,vnnvc,NL,webmaster@example.com,Order', 1255959006, 606, 607),
(1, NULL, 11, 'billing', '2009-000002 (Group-Office Beheerder)', '', NULL, 7, 'Order', '2009-000002,92c761a3dbc2a26d397636e1d43bffe3,Group-Office Beheerder,Geachte heer Beheerder,fcncv,vnv,vnc,vnnvc,NL,webmaster@example.com,Order', 1256036966, 615, 616),
(1, NULL, 12, 'billing', '2009-000003 (Group-Office Beheerder)', '', NULL, 7, 'Order', '2009-000003,d48938e9f1cad2218feb964efe70edfc,Group-Office Beheerder,Geachte heer Beheerder,fcncv,vnv,vnc,vnnvc,NL,webmaster@example.com,Order', 1256037664, 615, 616),
(1, NULL, 13, 'billing', '2009-000004 (Group-Office Beheerder)', '', NULL, 7, 'Order', '2009-000004,7a60bd4521a8b8f24c6e098f07a1ae75,Group-Office Beheerder,Geachte heer Beheerder,fcncv,vnv,vnc,vnnvc,NL,webmaster@example.com,Order', 1256037706, 615, 616),
(1, NULL, 14, 'billing', '2009-000005 (Group-Office Beheerder)', '', NULL, 7, 'Order', '2009-000005,cd544eb5b2185f590f8eaf7d2c51efc2,Group-Office Beheerder,Geachte heer Beheerder,fcncv,vnv,vnc,vnnvc,NL,webmaster@example.com,Order', 1256044841, 615, 616),
(1, NULL, 15, 'billing', '2009-000006 (Group-Office Beheerder)', '', NULL, 7, 'Order', '2009-000006,6d4a716fd6b7fec25c302371f61b3598,Group-Office Beheerder,Geachte heer Beheerder,fcncv,vnv,vnc,vnnvc,NL,webmaster@example.com,Order', 1256045109, 615, 616),
(1, NULL, 16, 'billing', '2009-000007 (Group-Office Beheerder)', '', NULL, 7, 'Order', '2009-000007,f29c20e14ff8c844333b073d72c6e13d,Group-Office Beheerder,Geachte heer Beheerder,fcncv,vnv,vnc,vnnvc,NL,webmaster@example.com,Order', 1256045123, 615, 616),
(1, NULL, 17, 'billing', '2009-000008 (Group-Office Beheerder)', '', NULL, 7, 'Order', '2009-000008,d3715b384b3d2f510ab08c0df6c301b1,Group-Office Beheerder,Geachte heer Beheerder,fcncv,vnv,vnc,vnnvc,NL,webmaster@example.com,Order', 1256045424, 615, 616),
(1, NULL, 18, 'billing', '2009-000009 (Group-Office Beheerder)', '', NULL, 7, 'Order', '2009-000009,1b79639487ae61e0f785d64ecd10d1c5,Group-Office Beheerder,Geachte heer Beheerder,fcncv,vnv,vnc,vnnvc,NL,webmaster@example.com,Order', 1256045806, 615, 616),
(1, NULL, 19, 'billing', '2009-000010 (Group-Office Beheerder)', '', NULL, 7, 'Order', '2009-000010,c77e42832895493b5dbc22261d622798,Group-Office Beheerder,Geachte heer Beheerder,fcncv,vnv,vnc,vnnvc,NL,webmaster@example.com,Order', 1256045833, 615, 616),
(1, NULL, 20, 'billing', '2009-000011 (Group-Office Beheerder)', '', NULL, 7, 'Order', '2009-000011,788f5884abccdee1cebbb4d9976957ae,Group-Office Beheerder,Geachte heer Beheerder,fcncv,vnv,vnc,vnnvc,NL,webmaster@example.com,Order', 1256045892, 615, 616),
(1, NULL, 21, 'billing', '2009-000012 (Group-Office Beheerder)', '', NULL, 7, 'Order', '2009-000012,4c0d5742a15c331032b14fc197d60148,Group-Office Beheerder,Geachte heer Beheerder,fcncv,vnv,vnc,vnnvc,NL,webmaster@example.com,Order', 1256045928, 615, 616),
(1, NULL, 22, 'billing', '2009-000013 (Group-Office Beheerder)', '', NULL, 7, 'Order', '2009-000013,b55595e174ded5f7d57f68ab563a19dc,Group-Office Beheerder,Geachte heer Beheerder,fcncv,vnv,vnc,vnnvc,NL,webmaster@example.com,Order', 1256048092, 615, 616),
(1, NULL, 23, 'billing', '2009-000014 (Group-Office Beheerder)', '', NULL, 7, 'Order', '2009-000014,3e42e4c61553eaa5bd446111fac6bb22,Group-Office Beheerder,Geachte heer Beheerder,fcncv,vnv,vnc,vnnvc,NL,webmaster@example.com,Order', 1256049007, 615, 616),
(1, NULL, 24, 'billing', '2009-000015 (Waldemar de Beursekond)', '', NULL, 7, 'Order', '2009-000015,4a5bc7591408d052fe6924bcd8110ed4,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,info@intermesh.nl,Order', 1256050481, 615, 616),
(1, NULL, 25, 'billing', '2009-000016 (Waldemar de Beursekond)', '', NULL, 7, 'Order', '2009-000016,d4f75e545d8153f068c488339f416f54,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,info@intermesh.nl,Order', 1256050700, 615, 616),
(1, NULL, 26, 'billing', '2009-000017 (Waldemar de Beursekond)', '', NULL, 7, 'Order', '2009-000017,bf4ad7351c91531091a60dd1825ffdeb,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,info@intermesh.nl,Order', 1256050720, 615, 616),
(1, NULL, 27, 'billing', '2009-000018 (Waldemar de Beursekond)', '', NULL, 7, 'Order', '2009-000018,4c3c793deb9945c0a20f1bd1ee377111,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,info@intermesh.nl,Order', 1256050880, 615, 616),
(1, NULL, 28, 'billing', '2009-000019 (Waldemar de Beursekond)', '', NULL, 7, 'Order', '2009-000019,313d620771b72a873b5724f531c1b539,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,info@intermesh.nl,Order', 1256050966, 615, 616),
(1, NULL, 29, 'billing', '2009-000020 (Waldemar de Beursekond)', '', NULL, 7, 'Order', '2009-000020,7027d5cc3a02d7d3ccd708193cbc9dfa,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,info@intermesh.nl,Order', 1256051082, 615, 616),
(1, NULL, 30, 'billing', '2009-000021 (Waldemar de Beursekond)', '', NULL, 7, 'Order', '2009-000021,c0e36a0c70fc46df3eba1d337a048ca7,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,info@intermesh.nl,Order', 1256106325, 615, 616),
(1, NULL, 31, 'billing', '2009-000022 (Waldemar de Beursekond)', '', NULL, 7, 'Order', '2009-000022,1bbab56c297e51f618badb320ee5bc60,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,info@intermesh.nl,Order', 1256106436, 615, 616),
(1, NULL, 32, 'billing', '2009-000023 (Waldemar de Beursekond)', '', NULL, 7, 'Order', '2009-000023,ee98fca67343496fc21885792e458252,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,info@intermesh.nl,Order', 1256106655, 615, 616),
(1, NULL, 33, 'billing', '2009-000024 (Waldemar de Beursekond)', '', NULL, 7, 'Order', '2009-000024,4288af4c60ea5f54f1ef26c0489875c5,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,info@intermesh.nl,Order', 1256106727, 615, 616),
(1, NULL, 34, 'billing', '2009-000025 (Waldemar de Beursekond)', '', NULL, 7, 'Order', '2009-000025,a6cf279ded932f552d1d609503871b0a,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,info@intermesh.nl,Order', 1256106778, 615, 616),
(1, NULL, 35, 'billing', '2009-000026 (Waldemar de Beursekond)', '', NULL, 7, 'Order', '2009-000026,c6c5663d63aed176cf446276e0362071,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,info@intermesh.nl,Order', 1256106827, 615, 616),
(1, NULL, 36, 'billing', '2009-000027 (Waldemar de Beursekond)', '', NULL, 7, 'Order', '2009-000027,0fb3df54234881a364f93ece78e9127b,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,info@intermesh.nl,Order', 1256107034, 615, 616),
(1, NULL, 37, 'billing', '2009-000028 (Waldemar de Beursekond)', '', NULL, 7, 'Order', '2009-000028,a21de070e7e5b5ee2686f163e998fce9,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,info@intermesh.nl,Order', 1256107076, 615, 616),
(1, NULL, 38, 'billing', '2009-000029 (Waldemar de Beursekond)', '', NULL, 7, 'Order', '2009-000029,113f5483f16cba1068a4444f701feb14,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,info@intermesh.nl,Order', 1256111276, 615, 616),
(1, NULL, 39, 'billing', '2009-000030 (Waldemar de Beursekond)', '', NULL, 7, 'Order', '2009-000030,27e24b3680a84ad878323a1b0582493e,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,info@intermesh.nl,Teletik Safepay Card,Order', 1256113280, 615, 616),
(1, NULL, 40, 'billing', '(Waldemar de Beursekond)', '', NULL, 7, 'Order', '9b92c9633d1a1ee2347b62a7978684b7,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,info@intermesh.nl,Teletik Safepay Card,Order', 1256113280, 53, 54),
(1, NULL, 41, 'billing', '2009-000031 (Waldemar de Beursekond)', '', NULL, 7, 'Order', '2009-000031,57de3c8b18f7821d482912726c4d1c09,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,info@intermesh.nl,Order', 1256116366, 615, 616),
(1, NULL, 42, 'billing', '2009-000032 (Waldemar de Beursekond)', '', NULL, 7, 'Order', '2009-000032,5b4387de95491b5b2bf617de3fe5b37d,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,info@intermesh.nl,Order', 1256118245, 615, 616),
(1, NULL, 43, 'billing', '2009-000033 (Waldemar de Beursekond)', '', NULL, 7, 'Order', '2009-000033,996a7ba2729862f291345feb7446b1e7,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,info@intermesh.nl,Order', 1256118707, 615, 616),
(1, NULL, 44, 'billing', '2009-000034 (Waldemar de Beursekond)', '', NULL, 7, 'Order', '2009-000034,0cfb32110c48ebb713b0f2c5add2ddfa,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,info@intermesh.nl,Order', 1256119281, 615, 616),
(1, NULL, 45, 'billing', '2009-000035 (Waldemar de Beursekond)', '', NULL, 7, 'Order', '2009-000035,d2645c9c887e66e42100ac316b9ebac4,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,info@intermesh.nl,Order', 1256120853, 615, 616),
(1, NULL, 46, 'billing', '2009-000036 (Waldemar de Beursekond)', '', NULL, 7, 'Order', '2009-000036,02ed5523d14a50fcf83b80703dcbdeb7,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,info@intermesh.nl,Order', 1256120913, 615, 616),
(1, NULL, 47, 'billing', '2009-000037 (Waldemar de Beursekond)', '', NULL, 7, 'Order', '2009-000037,64fe6c5f837a69db639ae839f5dc3b59,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,info@intermesh.nl,Order', 1256121009, 615, 616),
(1, NULL, 48, 'billing', '2009-000038 (Waldemar de Beursekond)', '', NULL, 7, 'Order', '2009-000038,d3839c861b1041535e98f23dd0b8ee5e,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,info@intermesh.nl,Teletik Safepay Card,Order', 1256123303, 615, 616),
(1, NULL, 49, 'billing', '(Waldemar de Beursekond)', '', NULL, 7, 'Order', '508078e110f77c124aab3da2b96adeca,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,info@intermesh.nl,Teletik Safepay Card,Order', 1256123303, 53, 54),
(1, NULL, 50, 'billing', '2009-000039 (Waldemar de Beursekond)', '', NULL, 7, 'Order', '2009-000039,952c9b5cd95381a3e8af1b99ac98c530,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,info@intermesh.nl,Order', 1256124394, 615, 616),
(1, NULL, 51, 'billing', '(Waldemar de Beursekond)', '', NULL, 7, 'Order', 'e8c35bbaadc80781431d1e807c49857f,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,info@intermesh.nl,Order', 1256124394, 53, 54),
(1, NULL, 52, 'billing', '2009-000040 (Waldemar de Beursekond)', '', NULL, 7, 'Order', '2009-000040,4f8775352f3936951011848ae47b3f19,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,info@intermesh.nl,Order', 1256124829, 615, 616),
(1, NULL, 53, 'billing', '(Waldemar de Beursekond)', '', NULL, 7, 'Order', '93e01da22462e5fbe2629c1fc7c43945,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,info@intermesh.nl,Order', 1256124829, 53, 54),
(1, NULL, 54, 'billing', '2009-000041 (Waldemar de Beursekond)', '', NULL, 7, 'Order', '2009-000041,0da6eb818c0b37db5ac9449e3dad1c35,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,w.vanbeusekom@gmail.com,Order', 1256125172, 615, 616),
(1, NULL, 55, 'billing', '2009-000042 (Waldemar de Beursekond)', '', NULL, 7, 'Order', '2009-000042,1c493888d49eb266b2ec22c709691a55,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,w.vanbeusekom@gmail.com,Order', 1256125220, 615, 616),
(1, NULL, 56, 'billing', '(Waldemar de Beursekond)', '', NULL, 7, 'Order', '300d19c43f5909f982b9c2a75f2fd460,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,w.vanbeusekom@gmail.com,Order', 1256125220, 53, 54),
(1, NULL, 57, 'billing', '2009-000043 (Waldemar de Beursekond)', '', NULL, 7, 'Order', '2009-000043,d26247c82f9df6f75df5512f5cde0aeb,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,w.vanbeusekom@gmail.com,Order', 1256126562, 615, 616),
(1, NULL, 58, 'billing', '(Waldemar de Beursekond)', '', NULL, 7, 'Order', '94f8e88f8d8bc71126ad4f9365363256,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,w.vanbeusekom@gmail.com,Order', 1256126562, 53, 54),
(1, NULL, 59, 'billing', '2009-000044 (Waldemar de Beursekond)', '', NULL, 7, 'Order', '2009-000044,8f8215a94cfc79f268e408a43e1f7f7e,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,w.vanbeusekom@gmail.com,Order', 1256126659, 615, 616),
(1, NULL, 60, 'billing', '(Waldemar de Beursekond)', '', NULL, 7, 'Order', 'b97efd1bab8ee6a3f1c9dfe247b21add,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,w.vanbeusekom@gmail.com,Order', 1256126659, 53, 54),
(1, NULL, 61, 'billing', '2009-000045 (Waldemar de Beursekond)', '', NULL, 7, 'Order', '2009-000045,cbc272bf37afff571020002d63cc7d01,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,w.vanbeusekom@gmail.com,Teletik Safepay Card,Order', 1256127084, 615, 616),
(1, NULL, 62, 'billing', '(Waldemar de Beursekond)', '', NULL, 7, 'Order', '3d959b9123db06fa4a2f488ef9d2bcc1,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,w.vanbeusekom@gmail.com,Teletik Safepay Card,Order', 1256127084, 53, 54),
(1, NULL, 63, 'billing', '2009-000046 (Waldemar de Beursekond)', '', NULL, 7, 'Order', '2009-000046,dd82df2a8b8ec0a16fdaab97dc94fdb4,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,w.vanbeusekom@gmail.com,Rabobank InternetKassa,Order', 1256127736, 615, 616),
(1, NULL, 64, 'billing', '(Waldemar de Beursekond)', '', NULL, 7, 'Order', '0949e0e01d79bbc763495368606d2e17,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,w.vanbeusekom@gmail.com,Order', 1256127736, 53, 54),
(1, NULL, 65, 'billing', '2009-000047 (Waldemar de Beursekond)', '', NULL, 7, 'Order', '2009-000047,083c4e5911ff65d4560fceff524afa56,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,w.vanbeusekom@gmail.com,Rabobank InternetKassa,Order', 1256130732, 615, 616),
(1, NULL, 66, 'billing', '(Waldemar de Beursekond)', '', NULL, 7, 'Order', '0c8d532911e139019b462371e2e90764,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,w.vanbeusekom@gmail.com,Order', 1256130732, 53, 54),
(1, NULL, 67, 'billing', '(Waldemar de Beursekond)', '', NULL, 7, 'Order', '56e2e269117d46846f8573c103b0fb27,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,w.vanbeusekom@gmail.com,Hack attempt detected: orderID=2009-000047,currency=EUR,amount=10.71,PM=iDEAL,ACCEPTANCE=0000000000,,Order', 1256130773, 615, 616),
(1, NULL, 68, 'billing', '2009-000048 (Waldemar de Beursekond)', '', NULL, 7, 'Order', '2009-000048,36f86ae170e77d14b3020daa22d8accd,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,w.vanbeusekom@gmail.com,Rabobank InternetKassa,Order', 1256133496, 615, 616),
(1, NULL, 69, 'billing', '(Waldemar de Beursekond)', '', NULL, 7, 'Order', '0529d13306069a2770db43071811084b,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,w.vanbeusekom@gmail.com,Order', 1256133496, 53, 54),
(1, NULL, 70, 'billing', '2009-000049 (Waldemar de Beursekond)', '', NULL, 7, 'Order', '2009-000049,282661db23c83ec89a077483141f36a2,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,w.vanbeusekom@gmail.com,Order', 1256133657, 615, 616),
(1, NULL, 71, 'billing', '2009-000050 (Waldemar de Beursekond)', '', NULL, 7, 'Order', '2009-000050,13fdc8653a424a8518990df1b0a9534f,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,w.vanbeusekom@gmail.com,Order', 1256134025, 615, 616),
(1, NULL, 72, 'billing', '2009-000051 (Waldemar de Beursekond)', '', NULL, 7, 'Order', '2009-000051,9ff375c6a2f269e714a5ea0a12d820f6,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,w.vanbeusekom@gmail.com,Order', 1256136267, 615, 616),
(1, NULL, 73, 'billing', '2009-000052 (Waldemar de Beursekond)', '', NULL, 7, 'Order', '2009-000052,755ffa397f3fe5ff84bfe7fe601e3e20,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,w.vanbeusekom@gmail.com,Order', 1256136294, 615, 616),
(1, NULL, 74, 'billing', '2009-000053 (Waldemar de Beursekond)', '', NULL, 7, 'Order', '2009-000053,5ce162203e28fd9321ff142a131cbc9d,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,w.vanbeusekom@gmail.com,Rabobank InternetKassa,Order', 1256136704, 615, 616),
(1, NULL, 76, 'billing', '2009-000054 (Waldemar de Beursekond)', '', NULL, 7, 'Order', '2009-000054,13eb94015d50513ef011c6c7c1d56898,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,w.vanbeusekom@gmail.com,Rabobank InternetKassa,Order', 1256136859, 615, 616),
(1, NULL, 77, 'billing', '(Waldemar de Beursekond)', '', NULL, 7, 'Order', 'e714cfeab2393cd958eb5d34a7a99d76,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,w.vanbeusekom@gmail.com,Order', 1256136859, 606, 607),
(1, NULL, 78, 'billing', '2009-000055 (Waldemar de Beursekond)', '', NULL, 7, 'Order', '2009-000055,6d7eb8f96a516dce8f252fdd33aaf05b,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,w.vanbeusekom@gmail.com,Rabobank InternetKassa,Order', 1256136952, 615, 616),
(1, NULL, 79, 'billing', '(Waldemar de Beursekond)', '', NULL, 7, 'Order', '1785d8cec3f5f3154b25c153de6bc037,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,w.vanbeusekom@gmail.com,Order', 1256136952, 606, 607),
(1, NULL, 80, 'billing', '2009-000056 (Waldemar de Beursekond)', '', NULL, 7, 'Order', '2009-000056,2b240bcff83a844d1b09213666cac0e8,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,w.vanbeusekom@gmail.com,Order', 1256137002, 615, 616),
(1, NULL, 81, 'billing', '2009-000057 (Waldemar de Beursekond)', '', NULL, 7, 'Order', '2009-000057,4fd4868f771f80b1ea58daf53525cba9,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,w.vanbeusekom@gmail.com,Rabobank InternetKassa,Order', 1256193377, 615, 616),
(1, NULL, 82, 'billing', '(Waldemar de Beursekond)', '', NULL, 7, 'Order', 'fe8b6491c888b01474287e2dabf62162,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,w.vanbeusekom@gmail.com,Order', 1256193377, 606, 607),
(1, NULL, 83, 'billing', '2009-000058 (Waldemar de Beursekond)', '', NULL, 7, 'Order', '2009-000058,8583c394fe2f71984d74e7bd81cd7c26,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,w.vanbeusekom@gmail.com,Rabobank InternetKassa,Order', 1256193542, 615, 616),
(1, NULL, 84, 'billing', '(Waldemar de Beursekond)', '', NULL, 7, 'Order', 'cce10e58abe87183ed56a6d70246b39e,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,w.vanbeusekom@gmail.com,Order', 1256193542, 606, 607),
(1, NULL, 85, 'billing', '2009-000059 (Waldemar de Beursekond)', '', NULL, 7, 'Order', '2009-000059,f2fe04a50be08edbc55799eec535fb34,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,w.vanbeusekom@gmail.com,Rabobank InternetKassa,Order', 1256196218, 615, 616),
(1, NULL, 86, 'billing', '(Waldemar de Beursekond)', '', NULL, 7, 'Order', '13d18d9a39998909551d67a2d090760c,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,w.vanbeusekom@gmail.com,Order', 1256196218, 606, 607),
(1, NULL, 87, 'billing', '2009-000060 (Waldemar de Beursekond)', '', NULL, 7, 'Order', '2009-000060,38db36156e879703c69601e68018ad23,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,w.vanbeusekom@gmail.com,Rabobank InternetKassa,Order', 1256196345, 615, 616),
(1, NULL, 88, 'billing', '(Waldemar de Beursekond)', '', NULL, 7, 'Order', 'e6b2f6149e911592a87a8b16c1ec820d,Waldemar de Beursekond,Geachte heer Beheerder,Reitscheweg,5933BX,Den Bosch,Gelderland,NL,w.vanbeusekom@gmail.com,Order', 1256196345, 606, 607);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_search_sync`
--

CREATE TABLE IF NOT EXISTS `go_search_sync` (
  `user_id` int(11) NOT NULL default '0',
  `module` varchar(50) NOT NULL default '',
  `last_sync_time` int(11) NOT NULL default '0',
  PRIMARY KEY  (`user_id`,`module`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `go_search_sync`
--


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_settings`
--

CREATE TABLE IF NOT EXISTS `go_settings` (
  `user_id` int(11) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `value` text,
  PRIMARY KEY  (`user_id`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `go_settings`
--

INSERT INTO `go_settings` (`user_id`, `name`, `value`) VALUES
(0, 'version', '32'),
(1, 'billing_report_books', '3,1,2'),
(1, 'billing_report_expense_books', '1');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_state`
--

CREATE TABLE IF NOT EXISTS `go_state` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL default '',
  `value` text,
  PRIMARY KEY  (`user_id`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `go_state`
--

INSERT INTO `go_state` (`user_id`, `name`, `value`) VALUES
(1, 'summary-calendar-grid', 'o%3Acolumns%3Da%253Ao%25253Aid%25253Dn%2525253A0%25255Ehidden%25253Db%2525253A1%255Eo%25253Aid%25253Dn%2525253A1%25255Ewidth%25253Dn%2525253A100%255Eo%25253Aid%25253Ds%2525253Asummary-calendar-name-heading%25255Ewidth%25253Dn%2525253A356%255Eo%25253Aid%25253Dn%2525253A3%25255Ewidth%25253Dn%2525253A140%5Esort%3Do%253Afield%253Ds%25253Astart_time%255Edirection%253Ds%25253AASC'),
(1, 'open-modules', 's%3A%5B%22summary%22%2C%22email%22%2C%22calendar%22%2C%22tasks%22%2C%22addressbook%22%2C%22files%22%2C%22notes%22%2C%22modules%22%2C%22billing%22%2C%22cms%22%2C%22tenders%22%5D'),
(1, 'bs-orders-grid', 'o%3Acolumns%3Da%253Ao%25253Aid%25253Dn%2525253A0%25255Ewidth%25253Dn%2525253A106%255Eo%25253Aid%25253Dn%2525253A1%25255Ewidth%25253Dn%2525253A69%255Eo%25253Aid%25253Dn%2525253A2%25255Ewidth%25253Dn%2525253A68%255Eo%25253Aid%25253Dn%2525253A3%25255Ewidth%25253Dn%2525253A68%255Eo%25253Aid%25253Dn%2525253A4%25255Ewidth%25253Dn%2525253A68%255Eo%25253Aid%25253Dn%2525253A5%25255Ewidth%25253Dn%2525253A68%255Eo%25253Aid%25253Dn%2525253A6%25255Ehidden%25253Db%2525253A1%255Eo%25253Aid%25253Dn%2525253A7%25255Ehidden%25253Db%2525253A1%255Eo%25253Aid%25253Dn%2525253A8%25255Ewidth%25253Dn%2525253A68%255Eo%25253Aid%25253Dn%2525253A9%25255Ehidden%25253Db%2525253A1%255Eo%25253Aid%25253Dn%2525253A10%25255Ewidth%25253Dn%2525253A68%255Eo%25253Aid%25253Dn%2525253A11%25255Ehidden%25253Db%2525253A1%255Eo%25253Aid%25253Dn%2525253A12%25255Ehidden%25253Db%2525253A1%255Eo%25253Aid%25253Dn%2525253A13%25255Ehidden%25253Db%2525253A1%255Eo%25253Aid%25253Dn%2525253A14%25255Ehidden%25253Db%2525253A1%255Eo%25253Aid%25253Dn%2525253A15%25255Ehidden%25253Db%2525253A1%255Eo%25253Aid%25253Dn%2525253A16%25255Ehidden%25253Db%2525253A1%255Eo%25253Aid%25253Dn%2525253A17%25255Ehidden%25253Db%2525253A1%255Eo%25253Aid%25253Dn%2525253A18%25255Ehidden%25253Db%2525253A1%255Eo%25253Aid%25253Dn%2525253A19%25255Ehidden%25253Db%2525253A1%255Eo%25253Aid%25253Dn%2525253A20%25255Ehidden%25253Db%2525253A1%255Eo%25253Aid%25253Dn%2525253A21%25255Ehidden%25253Db%2525253A1%255Eo%25253Aid%25253Dn%2525253A22%25255Ehidden%25253Db%2525253A1%5Esort%3Do%253Afield%253Ds%25253Acustomer_name%255Edirection%253Ds%25253AASC'),
(1, 'list-grid', 'o%3Acolumns%3Da%253Ao%25253Aid%25253Dn%2525253A0%25255Ehidden%25253Db%2525253A1%255Eo%25253Aid%25253Dn%2525253A1%25255Ewidth%25253Dn%2525253A80%255Eo%25253Aid%25253Ds%2525253Alistview-calendar-name-heading%25255Ewidth%25253Dn%2525253A51%5Esort%3Do%253Afield%253Ds%25253Astart_time%255Edirection%253Ds%25253AASC'),
(1, 'acc_resources', 'o%3Acolumns%3Da%253Ao%25253Aid%25253Ds%2525253Aname%25255Ewidth%25253Dn%2525253A95%255Eo%25253Aid%25253Ds%2525253Agroup_name%25255Ewidth%25253Dn%2525253A95%25255Ehidden%25253Db%2525253A1'),
(1, 'category-0-panel', 'o%3Acolumns%3Da%253Ao%25253Aid%25253Dn%2525253A0%25255Ewidth%25253Dn%2525253A238%255Eo%25253Aid%25253Dn%2525253A1%25255Ewidth%25253Dn%2525253A238%255Eo%25253Aid%25253Dn%2525253A2%25255Ewidth%25253Dn%2525253A238%5Esort%3Do%253Afield%253Ds%25253Aname%255Edirection%253Ds%25253AASC'),
(1, 'calendar-state', 's%3A%7B%22displayType%22%3A%22days%22%2C%22calendar_id%22%3A1%2C%22days%22%3A7%7D');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_users`
--

CREATE TABLE IF NOT EXISTS `go_users` (
  `id` int(11) NOT NULL default '0',
  `username` varchar(50) default NULL,
  `password` varchar(64) default NULL,
  `auth_md5_pass` varchar(100) default NULL,
  `enabled` enum('0','1') NOT NULL default '1',
  `first_name` varchar(50) default NULL,
  `middle_name` varchar(50) default NULL,
  `last_name` varchar(100) default NULL,
  `initials` varchar(10) default NULL,
  `title` varchar(10) default NULL,
  `sex` enum('M','F') NOT NULL default 'M',
  `birthday` date default NULL,
  `email` varchar(100) default NULL,
  `company` varchar(50) default NULL,
  `department` varchar(50) default NULL,
  `function` varchar(50) default NULL,
  `home_phone` varchar(20) default NULL,
  `work_phone` varchar(20) default NULL,
  `fax` varchar(20) default NULL,
  `cellular` varchar(20) default NULL,
  `country` char(2) NOT NULL,
  `state` varchar(50) default NULL,
  `city` varchar(50) default NULL,
  `zip` varchar(10) default NULL,
  `address` varchar(100) default NULL,
  `address_no` varchar(10) default NULL,
  `homepage` varchar(100) default NULL,
  `work_address` varchar(100) default NULL,
  `work_address_no` varchar(10) default NULL,
  `work_zip` varchar(10) default NULL,
  `work_country` char(2) NOT NULL,
  `work_state` varchar(50) default NULL,
  `work_city` varchar(50) default NULL,
  `work_fax` varchar(20) default NULL,
  `acl_id` int(11) NOT NULL default '0',
  `date_format` varchar(20) default NULL,
  `date_separator` char(1) NOT NULL default '-',
  `time_format` varchar(10) default NULL,
  `thousands_separator` char(1) NOT NULL default '.',
  `decimal_separator` char(1) NOT NULL default ',',
  `currency` char(3) NOT NULL default '',
  `logins` int(11) NOT NULL default '0',
  `lastlogin` int(11) NOT NULL default '0',
  `registration_time` int(11) NOT NULL default '0',
  `max_rows_list` tinyint(4) NOT NULL default '15',
  `timezone` varchar(50) default NULL,
  `start_module` varchar(50) default NULL,
  `language` varchar(20) default NULL,
  `theme` varchar(20) default NULL,
  `first_weekday` tinyint(4) NOT NULL default '0',
  `sort_name` varchar(20) default NULL,
  `bank` varchar(50) default NULL,
  `bank_no` varchar(50) default NULL,
  `mtime` int(11) NOT NULL default '0',
  `mute_sound` enum('0','1') NOT NULL,
  `list_separator` char(3) NOT NULL default ';',
  `text_separator` char(3) NOT NULL default '"',
  `files_folder_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `go_users`
--

INSERT INTO `go_users` (`id`, `username`, `password`, `auth_md5_pass`, `enabled`, `first_name`, `middle_name`, `last_name`, `initials`, `title`, `sex`, `birthday`, `email`, `company`, `department`, `function`, `home_phone`, `work_phone`, `fax`, `cellular`, `country`, `state`, `city`, `zip`, `address`, `address_no`, `homepage`, `work_address`, `work_address_no`, `work_zip`, `work_country`, `work_state`, `work_city`, `work_fax`, `acl_id`, `date_format`, `date_separator`, `time_format`, `thousands_separator`, `decimal_separator`, `currency`, `logins`, `lastlogin`, `registration_time`, `max_rows_list`, `timezone`, `start_module`, `language`, `theme`, `first_weekday`, `sort_name`, `bank`, `bank_no`, `mtime`, `mute_sound`, `list_separator`, `text_separator`, `files_folder_id`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'd2abaa37a7c3db1137d385e1d8c15fd2', '1', 'Group-Office', '', 'Beheerder', NULL, NULL, 'M', NULL, 'w.vanbeusekom@gmail.com', 'Waldemar de Beursekond', NULL, NULL, NULL, NULL, NULL, NULL, 'NL', 'Gelderland', 'Den Bosch', '5933BX', 'Reitscheweg', '37', NULL, NULL, NULL, NULL, 'NL', NULL, NULL, NULL, 57, 'dmY', '-', 'G:i', '.', ',', '€', 7, 1255964117, 1253802195, 30, 'Europe/Amsterdam', 'summary', 'nl', 'Default', 1, NULL, NULL, NULL, 1256196291, '0', ';', '"', 10);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `go_users_groups`
--

CREATE TABLE IF NOT EXISTS `go_users_groups` (
  `group_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`group_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `go_users_groups`
--

INSERT INTO `go_users_groups` (`group_id`, `user_id`) VALUES
(1, 1),
(2, 1),
(3, 1);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `no_categories`
--

CREATE TABLE IF NOT EXISTS `no_categories` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `acl_read` int(11) NOT NULL,
  `acl_write` int(11) NOT NULL,
  `name` varchar(50) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `no_categories`
--

INSERT INTO `no_categories` (`id`, `user_id`, `acl_read`, `acl_write`, `name`) VALUES
(1, 1, 31, 32, 'General');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `no_notes`
--

CREATE TABLE IF NOT EXISTS `no_notes` (
  `id` int(11) NOT NULL default '0',
  `category_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL default '0',
  `ctime` int(11) NOT NULL default '0',
  `mtime` int(11) NOT NULL default '0',
  `name` varchar(100) default NULL,
  `content` text,
  `files_folder_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`),
  KEY `category_id` (`category_id`),
  FULLTEXT KEY `content` (`content`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `no_notes`
--


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `su_announcements`
--

CREATE TABLE IF NOT EXISTS `su_announcements` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `due_time` int(11) NOT NULL default '0',
  `ctime` int(11) NOT NULL default '0',
  `mtime` int(11) NOT NULL default '0',
  `title` varchar(50) default NULL,
  `content` text,
  PRIMARY KEY  (`id`),
  KEY `due_time` (`due_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `su_announcements`
--


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `su_notes`
--

CREATE TABLE IF NOT EXISTS `su_notes` (
  `user_id` int(11) NOT NULL,
  `text` text,
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `su_notes`
--

INSERT INTO `su_notes` (`user_id`, `text`) VALUES
(1, NULL);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `su_rss_feeds`
--

CREATE TABLE IF NOT EXISTS `su_rss_feeds` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `url` varchar(255) default NULL,
  `summary` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Gegevens worden uitgevoerd voor tabel `su_rss_feeds`
--


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `su_visible_calendars`
--

CREATE TABLE IF NOT EXISTS `su_visible_calendars` (
  `user_id` int(11) NOT NULL,
  `calendar_id` int(11) NOT NULL,
  PRIMARY KEY  (`user_id`,`calendar_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `su_visible_calendars`
--


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `su_visible_lists`
--

CREATE TABLE IF NOT EXISTS `su_visible_lists` (
  `user_id` int(11) NOT NULL,
  `tasklist_id` int(11) NOT NULL,
  PRIMARY KEY  (`user_id`,`tasklist_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `su_visible_lists`
--


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `ta_lists`
--

CREATE TABLE IF NOT EXISTS `ta_lists` (
  `id` int(11) NOT NULL,
  `name` varchar(100) default NULL,
  `user_id` int(11) NOT NULL,
  `acl_read` int(11) NOT NULL,
  `acl_write` int(11) NOT NULL,
  `shared_acl` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `ta_lists`
--

INSERT INTO `ta_lists` (`id`, `name`, `user_id`, `acl_read`, `acl_write`, `shared_acl`) VALUES
(1, 'Beheerder, Group-Office', 1, 64, 65, 0);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `ta_settings`
--

CREATE TABLE IF NOT EXISTS `ta_settings` (
  `user_id` int(11) NOT NULL,
  `reminder_days` int(11) NOT NULL,
  `reminder_time` varchar(10) NOT NULL,
  `remind` enum('0','1') NOT NULL,
  `default_tasklist_id` int(11) NOT NULL,
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `ta_settings`
--

INSERT INTO `ta_settings` (`user_id`, `reminder_days`, `reminder_time`, `remind`, `default_tasklist_id`) VALUES
(1, 0, '8:00', '0', 0);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `ta_tasks`
--

CREATE TABLE IF NOT EXISTS `ta_tasks` (
  `id` int(11) NOT NULL,
  `tasklist_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ctime` int(11) NOT NULL,
  `mtime` int(11) NOT NULL,
  `start_time` int(11) NOT NULL,
  `due_time` int(11) NOT NULL,
  `completion_time` int(11) NOT NULL default '0',
  `name` varchar(100) default NULL,
  `description` text,
  `status` varchar(20) default NULL,
  `repeat_end_time` int(11) NOT NULL,
  `reminder` int(11) NOT NULL,
  `rrule` varchar(50) NOT NULL,
  `files_folder_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `list_id` (`tasklist_id`),
  KEY `rrule` (`rrule`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `ta_tasks`
--


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `ws_bonus_products`
--

CREATE TABLE IF NOT EXISTS `ws_bonus_products` (
  `id` int(11) NOT NULL default '0',
  `webshop_id` int(11) NOT NULL default '0',
  `product_id` int(11) NOT NULL default '0',
  `amount` double NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `webshop_id` (`webshop_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `ws_bonus_products`
--


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `ws_demo_categories`
--

CREATE TABLE IF NOT EXISTS `ws_demo_categories` (
  `id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `ws_demo_categories`
--

INSERT INTO `ws_demo_categories` (`id`) VALUES
(155),
(156),
(157),
(158),
(159),
(160);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `ws_demo_identifier`
--

CREATE TABLE IF NOT EXISTS `ws_demo_identifier` (
  `id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `ws_demo_identifier`
--

INSERT INTO `ws_demo_identifier` (`id`) VALUES
(166);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `ws_demo_products`
--

CREATE TABLE IF NOT EXISTS `ws_demo_products` (
  `id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `ws_demo_products`
--

INSERT INTO `ws_demo_products` (`id`) VALUES
(285),
(286),
(287),
(288),
(289),
(290),
(291);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `ws_payments`
--

CREATE TABLE IF NOT EXISTS `ws_payments` (
  `id` int(11) NOT NULL default '0',
  `webshop_id` int(1) NOT NULL default '0',
  `name` varchar(100) default NULL,
  `image` varchar(100) default NULL,
  `pending_status_id` int(11) NOT NULL default '0',
  `failed_status_id` int(11) NOT NULL default '0',
  `options` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `ws_payments`
--

INSERT INTO `ws_payments` (`id`, `webshop_id`, `name`, `image`, `pending_status_id`, `failed_status_id`, `options`) VALUES
(1, 73, 'multipay', 'multipay/multipay.gif', 17, 14, '<options><mpSeller_ID>909</mpSeller_ID>\r\n<mpAdministration_ID>Intermesh Software Shop</mpAdministration_ID>\r\n<mpCountry>EN</mpCountry>\r\n<mpLanguage>EN</mpLanguage>\r\n<mpCurrency>EUR</mpCurrency>\r\n<mpAllow>T</mpAllow>\r\n<mpHeaderFooterid></mpHeaderFooterid>\r\n</options>'),
(4, 73, 'rik', 'rik/ideal.jpeg', 41, 45, '<options><PSPID>Wilmar</PSPID>\n<language>EN</language>\n<currency>EUR</currency>\n<SHA1in_key>Je moeder is een geit.</SHA1in_key>\n<SHA1out_key>I sense a disturbance in the force.</SHA1out_key>\n</options>'),
(5, 73, 'abnamro_ik', 'abnamro_ik/ideal.jpeg', 41, 45, '<options><PSPID>Wilmar</PSPID>\n<language>EN</language>\n<currency>EUR</currency>\n<SHA1in_key>Je moeder is een geit.</SHA1in_key>\n<SHA1out_key>I sense a disturbance in the force.</SHA1out_key>\n</options>');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `ws_product_webshops`
--

CREATE TABLE IF NOT EXISTS `ws_product_webshops` (
  `product_id` int(11) NOT NULL,
  `webshop_id` int(11) NOT NULL,
  PRIMARY KEY  (`product_id`,`webshop_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `ws_product_webshops`
--


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `ws_users`
--

CREATE TABLE IF NOT EXISTS `ws_users` (
  `webshop_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `bonus_points` int(11) NOT NULL default '0',
  `vat_no` varchar(50) default NULL,
  `reseller_discount` tinyint(100) NOT NULL,
  PRIMARY KEY  (`webshop_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `ws_users`
--

INSERT INTO `ws_users` (`webshop_id`, `user_id`, `bonus_points`, `vat_no`, `reseller_discount`) VALUES
(73, 1, 0, NULL, 0);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `ws_webshops`
--

CREATE TABLE IF NOT EXISTS `ws_webshops` (
  `id` int(11) NOT NULL default '0',
  `currency` varchar(10) default NULL,
  `language_id` int(11) NOT NULL default '0',
  `name` varchar(50) default NULL,
  `user_id` int(11) NOT NULL default '0',
  `order_book_id` int(11) NOT NULL default '0',
  `invoice_book_id` int(11) NOT NULL default '0',
  `root_category_id` int(11) default '0',
  `site_id` int(11) NOT NULL default '0',
  `payment_template_item_id` int(11) NOT NULL default '0',
  `acl_id` int(11) NOT NULL default '0',
  `bonus_point_value` double NOT NULL default '1',
  `order_success_status_id` int(11) NOT NULL default '0',
  `invoice_success_status_id` int(11) NOT NULL default '0',
  `shipping_costs` double NOT NULL default '0',
  `free_shipping_treshold` double NOT NULL default '0',
  `full_go_url` varchar(255) default NULL,
  `order_failed_status_id` int(11) NOT NULL default '0',
  `order_pending_status_id` int(11) NOT NULL default '0',
  `order_exception_status_id` int(11) default NULL,
  `order_declined_status_id` int(11) default NULL,
  `order_canceled_status_id` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Gegevens worden uitgevoerd voor tabel `ws_webshops`
--

INSERT INTO `ws_webshops` (`id`, `currency`, `language_id`, `name`, `user_id`, `order_book_id`, `invoice_book_id`, `root_category_id`, `site_id`, `payment_template_item_id`, `acl_id`, `bonus_point_value`, `order_success_status_id`, `invoice_success_status_id`, `shipping_costs`, `free_shipping_treshold`, `full_go_url`, `order_failed_status_id`, `order_pending_status_id`, `order_exception_status_id`, `order_declined_status_id`, `order_canceled_status_id`) VALUES
(73, NULL, 1, 'My first Group-Office webshop', 1, 11, 3, 155, 166, 0, 0, 0, 44, 9, 0, 0, 'http://www.groupoffice.nl', 45, 41, 47, 45, 46);
