-- phpMyAdmin SQL Dump
-- version 3.2.2.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 04, 2010 at 04:16 PM
-- Server version: 5.1.37
-- PHP Version: 5.2.10-2ubuntu6.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `propack`
--

-- --------------------------------------------------------

--
-- Table structure for table `sh_charges`
--

DROP TABLE IF EXISTS `sh_charges`;
CREATE TABLE IF NOT EXISTS `sh_charges` (
  `id` int(11) NOT NULL DEFAULT '0',
  `invoice_id` int(11) NOT NULL DEFAULT '0',
  `container_id` int(11) NOT NULL,
  `amount` double NOT NULL DEFAULT '0',
  `description` text NOT NULL,
  `price` double NOT NULL DEFAULT '0',
  `cost_code_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_id` (`invoice_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sh_containers`
--

DROP TABLE IF EXISTS `sh_containers`;
CREATE TABLE IF NOT EXISTS `sh_containers` (
  `id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `container_no` varchar(50) NOT NULL DEFAULT '',
  `seal_no` varchar(50) NOT NULL DEFAULT '',
  `type` varchar(50) NOT NULL DEFAULT '',
  `shipment_id` int(11) NOT NULL DEFAULT '0',
  `link_id` int(11) NOT NULL DEFAULT '0',
  `ctime` int(11) NOT NULL DEFAULT '0',
  `mtime` int(11) NOT NULL DEFAULT '0',
  `shipping_method` enum('air','sea','road') NOT NULL DEFAULT 'air',
  `invoice_id` int(11) NOT NULL DEFAULT '0',
  `charge_load_fee` enum('0','1') NOT NULL DEFAULT '0',
  `group_id` int(11) NOT NULL DEFAULT '0',
  `package_count` int(11) NOT NULL,
  `invoiced` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sh_cost_codes`
--

DROP TABLE IF EXISTS `sh_cost_codes`;
CREATE TABLE IF NOT EXISTS `sh_cost_codes` (
  `id` int(11) NOT NULL,
  `code` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sh_destinations`
--

DROP TABLE IF EXISTS `sh_destinations`;
CREATE TABLE IF NOT EXISTS `sh_destinations` (
  `id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sh_groups`
--

DROP TABLE IF EXISTS `sh_groups`;
CREATE TABLE IF NOT EXISTS `sh_groups` (
  `group_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sh_invoices`
--

DROP TABLE IF EXISTS `sh_invoices`;
CREATE TABLE IF NOT EXISTS `sh_invoices` (
  `id` int(11) NOT NULL DEFAULT '0',
  `text_order_id` varchar(20) NOT NULL,
  `customer_name` varchar(100) NOT NULL DEFAULT '',
  `customer_address` text NOT NULL,
  `itime` int(11) NOT NULL DEFAULT '0',
  `ptime` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `ctime` int(11) NOT NULL DEFAULT '0',
  `mtime` int(11) NOT NULL DEFAULT '0',
  `customer_order_no` varchar(50) NOT NULL DEFAULT '',
  `propack_reference` varchar(50) NOT NULL DEFAULT '',
  `jcb_supplier_reference` varchar(50) NOT NULL DEFAULT '',
  `customer_contact_name` varchar(100) NOT NULL DEFAULT '',
  `total` float NOT NULL,
  `link_id` int(11) NOT NULL,
  `supplier` varchar(100) NOT NULL,
  `status_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `cost_code_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`),
  KEY `status_id` (`status_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sh_invoice_statuses`
--

DROP TABLE IF EXISTS `sh_invoice_statuses`;
CREATE TABLE IF NOT EXISTS `sh_invoice_statuses` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sh_jobs`
--

DROP TABLE IF EXISTS `sh_jobs`;
CREATE TABLE IF NOT EXISTS `sh_jobs` (
  `id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `order_no` varchar(50) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `ctime` int(11) NOT NULL DEFAULT '0',
  `mtime` int(11) NOT NULL DEFAULT '0',
  `items` int(11) NOT NULL DEFAULT '0',
  `packed` int(11) NOT NULL DEFAULT '0',
  `customer` varchar(100) NOT NULL DEFAULT '',
  `supplier` varchar(100) NOT NULL DEFAULT '',
  `destination` varchar(100) NOT NULL DEFAULT '',
  `order_by` varchar(50) NOT NULL DEFAULT '',
  `shipping_method` enum('air','sea','road') NOT NULL DEFAULT 'air',
  `link_id` int(11) NOT NULL DEFAULT '0',
  `priority` enum('0','1') NOT NULL DEFAULT '0',
  `group_id` int(11) NOT NULL DEFAULT '0',
  `completed` enum('0','1') NOT NULL DEFAULT '0',
  `archived` enum('0','1') NOT NULL DEFAULT '0',
  `jcb_po_no` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`),
  KEY `destination` (`destination`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sh_job_items`
--

DROP TABLE IF EXISTS `sh_job_items`;
CREATE TABLE IF NOT EXISTS `sh_job_items` (
  `id` int(11) NOT NULL DEFAULT '0',
  `job_id` int(11) NOT NULL DEFAULT '0',
  `part_id` int(11) NOT NULL DEFAULT '0',
  `description` text NOT NULL,
  `serial_no` varchar(100) NOT NULL DEFAULT '',
  `amount` double NOT NULL DEFAULT '0',
  `vat` double NOT NULL DEFAULT '0',
  `discount` double NOT NULL DEFAULT '0',
  `packed` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `order_id` (`job_id`),
  KEY `product_id` (`part_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sh_materials`
--

DROP TABLE IF EXISTS `sh_materials`;
CREATE TABLE IF NOT EXISTS `sh_materials` (
  `id` int(11) NOT NULL,
  `supplier` varchar(100) NOT NULL,
  `number` varchar(20) NOT NULL,
  `description` varchar(100) DEFAULT NULL,
  `unit` varchar(10) NOT NULL,
  `price` double NOT NULL,
  `comments` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `supplier` (`supplier`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sh_packages`
--

DROP TABLE IF EXISTS `sh_packages`;
CREATE TABLE IF NOT EXISTS `sh_packages` (
  `id` int(11) NOT NULL DEFAULT '0',
  `job_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `package_no` varchar(20) NOT NULL DEFAULT '',
  `ctime` int(11) NOT NULL DEFAULT '0',
  `mtime` int(11) NOT NULL DEFAULT '0',
  `description` varchar(255) NOT NULL DEFAULT '',
  `weight` double NOT NULL DEFAULT '0',
  `width` int(10) NOT NULL DEFAULT '0',
  `height` int(11) NOT NULL DEFAULT '0',
  `length` int(11) NOT NULL DEFAULT '0',
  `price` double NOT NULL DEFAULT '0',
  `standard_package_id` int(11) NOT NULL DEFAULT '0',
  `container_id` int(11) NOT NULL DEFAULT '0',
  `link_id` int(11) NOT NULL DEFAULT '0',
  `packer_user_id` int(11) NOT NULL DEFAULT '0',
  `group_id` int(11) NOT NULL DEFAULT '0',
  `invoice_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`),
  KEY `job_id` (`job_id`),
  KEY `container_id` (`container_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sh_package_items`
--

DROP TABLE IF EXISTS `sh_package_items`;
CREATE TABLE IF NOT EXISTS `sh_package_items` (
  `id` int(11) NOT NULL DEFAULT '0',
  `job_item_id` int(11) NOT NULL DEFAULT '0',
  `package_id` int(11) NOT NULL DEFAULT '0',
  `amount` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `job_item_id` (`job_item_id`),
  KEY `package_id` (`package_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sh_package_types`
--

DROP TABLE IF EXISTS `sh_package_types`;
CREATE TABLE IF NOT EXISTS `sh_package_types` (
  `id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sh_parts`
--

DROP TABLE IF EXISTS `sh_parts`;
CREATE TABLE IF NOT EXISTS `sh_parts` (
  `id` int(11) NOT NULL DEFAULT '0',
  `category_id` int(11) NOT NULL DEFAULT '0',
  `image` varchar(255) NOT NULL DEFAULT '',
  `part_no` varchar(50) NOT NULL DEFAULT '',
  `name` varchar(50) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `weight` double NOT NULL DEFAULT '0',
  `unit_of_measure` varchar(20) NOT NULL DEFAULT '',
  `part_type_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sh_parts_groups`
--

DROP TABLE IF EXISTS `sh_parts_groups`;
CREATE TABLE IF NOT EXISTS `sh_parts_groups` (
  `part_id` int(11) NOT NULL DEFAULT '0',
  `group_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`part_id`,`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sh_part_categories`
--

DROP TABLE IF EXISTS `sh_part_categories`;
CREATE TABLE IF NOT EXISTS `sh_part_categories` (
  `id` int(11) NOT NULL DEFAULT '0',
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sh_part_types`
--

DROP TABLE IF EXISTS `sh_part_types`;
CREATE TABLE IF NOT EXISTS `sh_part_types` (
  `id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sh_pi_charges`
--

DROP TABLE IF EXISTS `sh_pi_charges`;
CREATE TABLE IF NOT EXISTS `sh_pi_charges` (
  `id` int(11) NOT NULL DEFAULT '0',
  `purchase_invoice_id` int(11) NOT NULL DEFAULT '0',
  `amount` double NOT NULL DEFAULT '0',
  `description` text NOT NULL,
  `price` double NOT NULL DEFAULT '0',
  `unit` varchar(20) NOT NULL,
  `purchase_code_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_invoice_id` (`purchase_invoice_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sh_pi_statuses`
--

DROP TABLE IF EXISTS `sh_pi_statuses`;
CREATE TABLE IF NOT EXISTS `sh_pi_statuses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `sh_po_charges`
--

DROP TABLE IF EXISTS `sh_po_charges`;
CREATE TABLE IF NOT EXISTS `sh_po_charges` (
  `id` int(11) NOT NULL DEFAULT '0',
  `purchase_order_id` int(11) NOT NULL DEFAULT '0',
  `standard_package_id` int(11) NOT NULL,
  `amount` double NOT NULL DEFAULT '0',
  `delivered_amount` double NOT NULL,
  `description` text NOT NULL,
  `price` double NOT NULL DEFAULT '0',
  `unit` varchar(20) NOT NULL,
  `purchase_code_id` int(11) NOT NULL,
  `vat_applicable` enum('0','1') NOT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_id` (`purchase_order_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sh_purchase_codes`
--

DROP TABLE IF EXISTS `sh_purchase_codes`;
CREATE TABLE IF NOT EXISTS `sh_purchase_codes` (
  `id` int(11) NOT NULL,
  `code` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sh_purchase_invoices`
--

DROP TABLE IF EXISTS `sh_purchase_invoices`;
CREATE TABLE IF NOT EXISTS `sh_purchase_invoices` (
  `id` int(11) NOT NULL DEFAULT '0',
  `text_order_id` varchar(20) NOT NULL,
  `customer_name` varchar(100) NOT NULL DEFAULT '',
  `customer_address` text NOT NULL,
  `itime` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `ctime` int(11) NOT NULL DEFAULT '0',
  `mtime` int(11) NOT NULL DEFAULT '0',
  `customer_order_no` varchar(50) NOT NULL DEFAULT '',
  `propack_reference` int(11) DEFAULT NULL,
  `jcb_supplier_reference` varchar(50) NOT NULL DEFAULT '',
  `customer_contact_name` varchar(100) NOT NULL DEFAULT '',
  `supplier` varchar(100) NOT NULL,
  `status_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `total` float NOT NULL,
  `vat` float NOT NULL,
  `purchase_code_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sh_purchase_orders`
--

DROP TABLE IF EXISTS `sh_purchase_orders`;
CREATE TABLE IF NOT EXISTS `sh_purchase_orders` (
  `id` int(11) NOT NULL,
  `supplier_name` varchar(100) NOT NULL,
  `supplier_address` text NOT NULL,
  `delivery_name` varchar(100) NOT NULL,
  `delivery_address` text NOT NULL,
  `number` varchar(20) NOT NULL,
  `supplier_number` varchar(50) NOT NULL,
  `ctime` int(11) NOT NULL,
  `mtime` int(11) NOT NULL,
  `btime` int(11) NOT NULL,
  `total` varchar(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `link_id` int(11) NOT NULL,
  `purchase_code_id` int(11) NOT NULL,
  `items` int(11) NOT NULL,
  `delivered` int(11) NOT NULL,
  `special_instructions` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sh_shipments`
--

DROP TABLE IF EXISTS `sh_shipments`;
CREATE TABLE IF NOT EXISTS `sh_shipments` (
  `id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `exporter` varchar(100) NOT NULL DEFAULT '',
  `exporter_address` varchar(100) NOT NULL DEFAULT '',
  `exporter_address_no` varchar(10) NOT NULL DEFAULT '',
  `exporter_zip` varchar(20) NOT NULL DEFAULT '',
  `exporter_city` varchar(100) NOT NULL DEFAULT '',
  `exporter_state` varchar(100) NOT NULL DEFAULT '',
  `exporter_country` varchar(100) NOT NULL DEFAULT '',
  `customs_reference` varchar(50) NOT NULL DEFAULT '',
  `customer` varchar(100) NOT NULL DEFAULT '',
  `customer_address` varchar(100) NOT NULL DEFAULT '',
  `customer_address_no` varchar(10) NOT NULL DEFAULT '',
  `customer_zip` varchar(20) NOT NULL DEFAULT '',
  `customer_city` varchar(100) NOT NULL DEFAULT '',
  `customer_state` varchar(100) NOT NULL DEFAULT '',
  `customer_country` varchar(100) NOT NULL DEFAULT '',
  `freight_forwarder` varchar(100) NOT NULL DEFAULT '',
  `freight_forwarder_address` varchar(100) NOT NULL DEFAULT '',
  `freight_forwarder_address_no` varchar(10) NOT NULL DEFAULT '',
  `freight_forwarder_zip` varchar(20) NOT NULL DEFAULT '',
  `freight_forwarder_city` varchar(100) NOT NULL DEFAULT '',
  `freight_forwarder_state` varchar(100) NOT NULL DEFAULT '',
  `freight_forwarder_country` varchar(100) NOT NULL DEFAULT '',
  `booking_number` varchar(50) NOT NULL DEFAULT '',
  `exporters_reference` varchar(50) NOT NULL DEFAULT '',
  `forwareders_reference` varchar(50) NOT NULL DEFAULT '',
  `international_carrier` varchar(100) NOT NULL DEFAULT '',
  `ets` int(11) NOT NULL DEFAULT '0',
  `eta` int(11) NOT NULL DEFAULT '0',
  `vessel_name` varchar(100) NOT NULL DEFAULT '',
  `port_of_loading` varchar(100) NOT NULL DEFAULT '',
  `port_of_discharge` varchar(100) NOT NULL DEFAULT '',
  `destination` varchar(100) NOT NULL DEFAULT '',
  `tare_wieght` double NOT NULL DEFAULT '0',
  `weight` double NOT NULL DEFAULT '0',
  `haulier` varchar(100) NOT NULL DEFAULT '',
  `vehicle_reg_no` varchar(100) NOT NULL DEFAULT '',
  `author_company` varchar(100) NOT NULL DEFAULT '',
  `author_company_address` varchar(100) NOT NULL DEFAULT '',
  `author_company_address_no` varchar(10) NOT NULL DEFAULT '',
  `author_company_zip` varchar(20) NOT NULL DEFAULT '',
  `author_company_city` varchar(100) NOT NULL DEFAULT '',
  `author_company_state` varchar(100) NOT NULL DEFAULT '',
  `author_company_country` varchar(100) NOT NULL DEFAULT '',
  `author_name` varchar(100) NOT NULL DEFAULT '',
  `author_telephone` varchar(20) NOT NULL DEFAULT '',
  `ctime` int(11) NOT NULL DEFAULT '0',
  `mtime` int(11) NOT NULL DEFAULT '0',
  `link_id` int(11) NOT NULL DEFAULT '0',
  `group_id` int(11) NOT NULL DEFAULT '0',
  `special_instructions` text NOT NULL,
  `container_contents` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`),
  KEY `mtime` (`mtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sh_standard_packages`
--

DROP TABLE IF EXISTS `sh_standard_packages`;
CREATE TABLE IF NOT EXISTS `sh_standard_packages` (
  `id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  `width` int(11) NOT NULL DEFAULT '0',
  `height` int(11) NOT NULL DEFAULT '0',
  `length` int(11) NOT NULL DEFAULT '0',
  `price` double NOT NULL DEFAULT '0',
  `weight` double NOT NULL DEFAULT '0',
  `softwood` double NOT NULL,
  `plywood` double NOT NULL,
  `corrugated_paper` double NOT NULL,
  `plastics` double NOT NULL,
  `metals` double NOT NULL,
  `description` text NOT NULL,
  `package_type_id` int(11) NOT NULL DEFAULT '0',
  `cost` double NOT NULL,
  `stock` int(11) NOT NULL,
  `supplier_company_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sh_standard_packages_groups`
--

DROP TABLE IF EXISTS `sh_standard_packages_groups`;
CREATE TABLE IF NOT EXISTS `sh_standard_packages_groups` (
  `standard_package_id` int(11) NOT NULL DEFAULT '0',
  `group_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`standard_package_id`,`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabel structuur voor tabel `se_cache`
--

DROP TABLE IF EXISTS `se_cache`;
CREATE TABLE `se_cache` (
  `link_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `table` varchar(50) NOT NULL,
  `id` int(11) NOT NULL,
  `module` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `link_type` int(11) NOT NULL,
  `type` varchar(20) NOT NULL,
  `keywords` text NOT NULL,
  `mtime` int(11) NOT NULL,
  PRIMARY KEY  (`link_id`,`user_id`),
  KEY `name` (`name`)
) TYPE=MyISAM;