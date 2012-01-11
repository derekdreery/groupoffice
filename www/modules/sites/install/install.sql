-- phpMyAdmin SQL Dump
-- version 3.4.5deb1
-- http://www.phpmyadmin.net
--
-- Machine: localhost
-- Genereertijd: 07 dec 2011 om 14:15
-- Serverversie: 5.1.58
-- PHP-Versie: 5.3.6-13ubuntu3.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `intermesh`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `si_pages`
--
CREATE TABLE IF NOT EXISTS `si_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `site_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ctime` int(11) NOT NULL,
  `mtime` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(255) NOT NULL,
  `keywords` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `template` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `hidden` tinyint(1) NOT NULL DEFAULT '0',
  `sort` int(11) NOT NULL DEFAULT '0',
  `login_required` tinyint(1) NOT NULL DEFAULT '0',
  `controller` varchar(80) NOT NULL DEFAULT 'GO_Sites_Controller_Site',
  `controller_action` varchar(80) NOT NULL DEFAULT 'index',
  PRIMARY KEY (`id`),
  KEY `path` (`path`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

--
-- Gegevens worden uitgevoerd voor tabel `si_pages`
--

INSERT INTO `si_pages` (`id`, `parent_id`, `site_id`, `user_id`, `ctime`, `mtime`, `name`, `title`, `description`, `keywords`, `path`, `template`, `content`, `hidden`, `sort`, `login_required`, `controller`, `controller_action`) VALUES
(1, 0, 1, 1, 0, 0, 'Products', 'Products', '', '', 'products', 'products', '', 0, 0, 0, 'GO_Webshop_Controller_Site', 'products'),
(2, 0, 1, 1, 0, 0, 'Shopping cart', '', '', '', 'cart', 'cart', '', 1, 0, 0, 'GO_Webshop_Controller_Site', 'cart'),
(3, 0, 1, 1, 0, 0, 'Login', 'Login', '', '', 'login', 'login', '', 1, 0, 0, 'GO_Sites_Controller_User', 'login'),
(4, 0, 1, 1, 0, 0, 'Invoices', 'Invoices', '', '', 'invoices', 'invoices', '', 0, 0, 1, 'GO_Billing_Controller_Site', 'invoices'),
(5, 0, 1, 1, 0, 0, 'Download', 'Download', '', '', 'download', 'licenselist', '', 0, 0, 1, 'GO_Licenses_Controller_Site', 'licenseList'),
(6, 0, 1, 1, 0, 0, 'Requirements', 'Requirements', '', '', 'requirements', '', '	\r\n\r\n<h1>Group-Office Professional System requirements</h1>\r\n<p>Read this before you purchase your Group-Office Professional license.<br> <br> To test if your server meets the system requirements for Group-Office and to find out which host and IP you need to fill in for your license we created a test script. Download the Group-Office test script here:<br> <br><a href="http://group-office.svn.sourceforge.net/viewvc/*checkout*/group-office/branches/groupoffice-3.7/www/install/gotest.php">http://group-office.svn.sourceforge.net/viewvc/*checkout*/group-office/branches/groupoffice-3.7/www/install/gotest.php</a><br> <br> Upload it to the server where you want to install Group-Office Professional and open it in your browser. If everything is Ok then you can continue.<br> <br> Group-Office Professional is partly encoded by Ioncube encoder. This requires the free loader to be installed on your server.&nbsp; You can download the loader with installation instructions here:<br> <br> <a href="http://www.ioncube.com/loaders.php" target="_blank">http://www.ioncube.com/loaders.php</a><br> <br> If this works then you are all set!<br> <br> Enjoy Group-Office!</p>							\r\n					', 0, 0, 0, 'GO_Sites_Controller_Site', 'index'),
(7, 0, 1, 1, 0, 0, 'Support', 'Support', '', '', 'support', 'ticketlist', 'Users of the Professional version get premium support through our ticket system. Login to the ticket system <a href="" target="_blank">here</a> with your webshop username and password and post your question. A technician will respond within 8 working hours but probably much faster. If you don''t have an account yet please click at Register at the top of this page.', 0, 0, 1, 'GO_Tickets_Controller_Site', 'ticketList'),
(8, 0, 1, 1, 0, 0, 'Register', 'Register', '', '', 'register', 'register', '', 1, 0, 0, 'GO_Sites_Controller_Site', 'index'),
(9, 0, 1, 1, 0, 0, 'Recover password', 'Recover password', '', '', 'lostpass', 'recoverpassword', '', 1, 0, 0, 'GO_Sites_Controller_Site', 'index'),
(11, 0, 1, 1, 0, 0, 'Checkout', 'Checkout', '', '', 'checkout', 'checkout', '', 1, 0, 1, 'GO_Webshop_Controller_Site', 'checkout'),
(12, 0, 1, 1, 0, 0, 'Pay for your order', 'Pay for your order', '', '', 'payment', 'payment', '', 1, 0, 1, 'GO_Webshop_Controller_Site', 'payment'),
(13, 0, 1, 1, 0, 0, 'paymentreturn', 'paymentreturn', 'paymentreturn', '', 'paymentreturn', 'paymentreturn', '', 1, 0, 1, 'GO_Webshop_Controller_Site', 'paymentReturn'),
(14, 0, 1, 1, 0, 0, 'Set license details', 'Set license details', '', '', 'setlicense', 'setlicense', '<p>Fill in the form with the data that is provided in the gotest.php file.</p>\r\n<p>Download the gotest.php file <a href="">here</a></p>', 1, 0, 1, 'GO_Licenses_Controller_Site', 'setlicense'),
(15, 0, 1, 1, 0, 0, 'License details', 'License details', '', '', 'viewlicense', 'viewlicense', '', 1, 0, 1, 'GO_Licenses_Controller_Site', 'viewlicense'),
(16, 0, 1, 1, 0, 0, 'Logout', 'Logout', '', '', 'logout', '', '', 1, 0, 1, 'GO_Sites_Controller_User', 'logout');


-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `si_sites`
--

CREATE TABLE IF NOT EXISTS `si_sites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `user_id` int(11) NOT NULL,
  `mtime` int(11) NOT NULL,
  `ctime` int(11) NOT NULL,
  `domain` varchar(100) NOT NULL,
  `template` varchar(100) NOT NULL,
  `login_page` varchar(255) NOT NULL DEFAULT 'login',
  `ssl` tinyint(1) NOT NULL DEFAULT '0',
  `mod_rewrite` tinyint(1) NOT NULL DEFAULT '0',
  `mod_rewrite_base_path` varchar(50) NOT NULL DEFAULT '/',
  PRIMARY KEY (`id`),
  KEY `domain` (`domain`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;# MySQL gaf een lege resultaat set terug (0 rijen).


--
-- Gegevens worden uitgevoerd voor tabel `si_sites`
--

INSERT INTO `si_sites` (`id`, `name`, `user_id`, `mtime`, `ctime`, `domain`, `template`, `login_page`, `ssl`, `mod_rewrite`, `mod_rewrite_base_path`) VALUES
(1, 'Intermesh software shop', 1, 1320058048, 1320058048, 'testshop.group-office.com', 'Example', 'login', 0, 1, '/');# 1 rij bijgewerkt.

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;