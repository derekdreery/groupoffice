<?php
//============================================================+
// File name   : barcodes.php
// Begin       : 2008-06-09
// Last Update : 2009-04-15
// Version     : 1.0.008
// License     : GNU LGPL (http://www.gnu.org/copyleft/lesser.html)
// 	----------------------------------------------------------------------------
//  Copyright (C) 2008-2009 Nicola Asuni - Tecnick.com S.r.l.
// 	
// 	This program is free software: you can redistribute it and/or modify
// 	it under the terms of the GNU Lesser General Public License as published by
// 	the Free Software Foundation, either version 2.1 of the License, or
// 	(at your option) any later version.
// 	
// 	This program is distributed in the hope that it will be useful,
// 	but WITHOUT ANY WARRANTY; without even the implied warranty of
// 	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// 	GNU Lesser General Public License for more details.
// 	
// 	You should have received a copy of the GNU Lesser General Public License
// 	along with this program.  If not, see <http://www.gnu.org/licenses/>.
// 	
// 	See LICENSE.TXT file for more information.
//  ----------------------------------------------------------------------------
//
// Description : PHP class to creates array representations for 
//               common 1D barcodes to be used with TCPDF.
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com S.r.l.
//               Via della Pace, 11
//               09044 Quartucciu (CA)
//               ITALY
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**
 * PHP class to creates array representations for common 1D barcodes to be used with TCPDF.
 * @package com.tecnick.tcpdf
 * @abstract Functions for generating string representation of common 1D barcodes.
 * @author Nicola Asuni
 * @copyright 2008-2009 Nicola Asuni - Tecnick.com S.r.l (www.tecnick.com) Via Della Pace, 11 - 09044 - Quartucciu (CA) - ITALY - www.tecnick.com - info@tecnick.com
 * @link http://www.tcpdf.org
 * @license http://www.gnu.org/copyleft/lesser.html LGPL
 * @version 1.0.008
 */

	/**
	* PHP class to creates array representations for common 1D barcodes to be used with TCPDF (http://www.tcpdf.org).<br>
	* @name TCPDFBarcode
	* @package com.tecnick.tcpdf
	* @version 1.0.008
	* @author Nicola Asuni
	* @link http://www.tcpdf.org
	* @license http://www.gnu.org/copyleft/lesser.html LGPL
	*/
class TCPDFBarcode {
	
	/**
	 * @var array representation of barcode.
	 * @access protected
	 */
	protected $barcode_array;
		
	/**
	 * This is the class constructor. 
	 * Return an array representations for common 1D barcodes:<ul>
	 * <li>$arrcode['code'] code to be printed on text label</li>
	 * <li>$arrcode['maxh'] max bar height</li>
	 * <li>$arrcode['maxw'] max bar width</li>
	 * <li>$arrcode['bcode'][$k] single bar or space in $k position</li>
	 * <li>$arrcode['bcode'][$k]['t'] bar type: true = bar, false = space.</li>
	 * <li>$arrcode['bcode'][$k]['w'] bar width in units.</li>
	 * <li>$arrcode['bcode'][$k]['h'] bar height in units.</li>
	 * <li>$arrcode['bcode'][$k]['p'] bar top position (0 = top, 1 = middle)</li></ul>
	 * @param string $code code to print
 	 * @param string $type type of barcode: <ul><li>C39 : CODE 39 - ANSI MH10.8M-1983 - USD-3 - 3 of 9.</li><li>C39+ : CODE 39 with checksum</li><li>C39E : CODE 39 EXTENDED</li><li>C39E+ : CODE 39 EXTENDED + CHECKSUM</li><li>C93 : CODE 93 - USS-93</li><li>S25 : Standard 2 of 5</li><li>S25+ : Standard 2 of 5 + CHECKSUM</li><li>I25 : Interleaved 2 of 5</li><li>I25+ : Interleaved 2 of 5 + CHECKSUM</li><li>C128A : CODE 128 A</li><li>C128B : CODE 128 B</li><li>C128C : CODE 128 C</li><li>EAN2 : 2-Digits UPC-Based Extention</li><li>EAN5 : 5-Digits UPC-Based Extention</li><li>EAN8 : EAN 8</li><li>EAN13 : EAN 13</li><li>UPCA : UPC-A</li><li>UPCE : UPC-E</li><li>MSI : MSI (Variation of Plessey code)</li><li>MSI+ : MSI + CHECKSUM (modulo 11)</li><li>POSTNET : POSTNET</li><li>PLANET : PLANET</li><li>RMS4CC : RMS4CC (Royal Mail 4-state Customer Code) - CBC (Customer Bar Code)</li><li>KIX : KIX (Klant index - Customer index)</li><li>IMB: Intelligent Mail Barcode - Onecode - USPS-B-3200</li><li>CODABAR : CODABAR</li><li>CODE11 : CODE 11</li><li>PHARMA : PHARMACODE</li><li>PHARMA2T : PHARMACODE TWO-TRACKS</li></ul>
	 */
	public function __construct($code, $type) {
		$this->setBarcode($code, $type);
	}
	
	/** 
	 * Return an array representations of barcode.
 	 * @return array
	 */
	public function getBarcodeArray() {
		return $this->barcode_array;
	}
	
	/** 
	 * Set the barcode.
	 * @param string $code code to print
 	 * @param string $type type of barcode: <ul><li>C39 : CODE 39 - ANSI MH10.8M-1983 - USD-3 - 3 of 9.</li><li>C39+ : CODE 39 with checksum</li><li>C39E : CODE 39 EXTENDED</li><li>C39E+ : CODE 39 EXTENDED + CHECKSUM</li><li>C93 : CODE 93 - USS-93</li><li>S25 : Standard 2 of 5</li><li>S25+ : Standard 2 of 5 + CHECKSUM</li><li>I25 : Interleaved 2 of 5</li><li>I25+ : Interleaved 2 of 5 + CHECKSUM</li><li>C128A : CODE 128 A</li><li>C128B : CODE 128 B</li><li>C128C : CODE 128 C</li><li>EAN2 : 2-Digits UPC-Based Extention</li><li>EAN5 : 5-Digits UPC-Based Extention</li><li>EAN8 : EAN 8</li><li>EAN13 : EAN 13</li><li>UPCA : UPC-A</li><li>UPCE : UPC-E</li><li>MSI : MSI (Variation of Plessey code)</li><li>MSI+ : MSI + CHECKSUM (modulo 11)</li><li>POSTNET : POSTNET</li><li>PLANET : PLANET</li><li>RMS4CC : RMS4CC (Royal Mail 4-state Customer Code) - CBC (Customer Bar Code)</li><li>KIX : KIX (Klant index - Customer index)</li><li>IMB: Intelligent Mail Barcode - Onecode - USPS-B-3200</li><li>CODABAR : CODABAR</li><li>CODE11 : CODE 11</li><li>PHARMA : PHARMACODE</li><li>PHARMA2T : PHARMACODE TWO-TRACKS</li></ul>
 	 * @return array
	 */
	public function setBarcode($code, $type) {
		switch (strtoupper($type)) {
			case 'C39': { // CODE 39 - ANSI MH10.8M-1983 - USD-3 - 3 of 9.
				$arrcode = $this->barcode_code39($code, false, false);
				break;
			}
			case 'C39+': { // CODE 39 with checksum
				$arrcode = $this->barcode_code39($code, false, true);
				break;
			}
			case 'C39E': { // CODE 39 EXTENDED
				$arrcode = $this->barcode_code39($code, true, false);
				break;
			}
			case 'C39E+': { // CODE 39 EXTENDED + CHECKSUM
				$arrcode = $this->barcode_code39($code, true, true);
				break;
			}
			case 'C93': { // CODE 93 - USS-93
				$arrcode = $this->barcode_code93($code);
				break;
			}
			case 'S25': { // Standard 2 of 5
				$arrcode = $this->barcode_s25($code, false);
				break;
			}
			case 'S25+': { // Standard 2 of 5 + CHECKSUM
				$arrcode = $this->barcode_s25($code, true);
				break;
			}
			case 'I25': { // Interleaved 2 of 5
				$arrcode = $this->barcode_i25($code, false);
				break;
			}
			case 'I25+': { // Interleaved 2 of 5 + CHECKSUM
				$arrcode = $this->barcode_i25($code, true);
				break;
			}
			case 'C128A': { // CODE 128 A
				$arrcode = $this->barcode_c128($code, 'A');
				break;
			}
			case 'C128B': { // CODE 128 B
				$arrcode = $this->barcode_c128($code, 'B');
				break;
			}
			case 'C128C': { // CODE 128 C
				$arrcode = $this->barcode_c128($code, 'C');
				break;
			}
			case 'EAN2': { // 2-Digits UPC-Based Extention
				$arrcode = $this->barcode_eanext($code, 2);
				break;
			}
			case 'EAN5': { // 5-Digits UPC-Based Extention
				$arrcode = $this->barcode_eanext($code, 5);
				break;
			}
			case 'EAN8': { // EAN 8
				$arrcode = $this->barcode_eanupc($code, 8);
				break;
			}
			case 'EAN13': { // EAN 13
				$arrcode = $this->barcode_eanupc($code, 13);
				break;
			}
			case 'UPCA': { // UPC-A
				$arrcode = $this->barcode_eanupc($code, 12);
				break;
			}
			case 'UPCE': { // UPC-E
				$arrcode = $this->barcode_eanupc($code, 6);
				break;
			}
			case 'MSI': { // MSI (Variation of Plessey code)
				$arrcode = $this->barcode_msi($code, false);
				break;
			}
			case 'MSI+': { // MSI + CHECKSUM (modulo 11)
				$arrcode = $this->barcode_msi($code, true);
				break;
			}
			case 'POSTNET': { // POSTNET
				$arrcode = $this->barcode_postnet($code, false);
				break;
			}
			case 'PLANET': { // PLANET
				$arrcode = $this->barcode_postnet($code, true);
				break;
			}
			case 'RMS4CC': { // RMS4CC (Royal Mail 4-state Customer Code) - CBC (Customer Bar Code)
				$arrcode = $this->barcode_rms4cc($code, false);
				break;
			}
			case 'KIX': { // KIX (Klant index - Customer index)
				$arrcode = $this->barcode_rms4cc($code, true);
				break;
			}
			case 'IMB': { // IMB - Intelligent Mail Barcode - Onecode - USPS-B-3200
				$arrcode = $this->barcode_imb($code);
				break;
			}
			case 'CODABAR': { // CODABAR
				$arrcode = $this->barcode_codabar($code);
				break;
			}
			case 'CODE11': { // CODE 11
				$arrcode = $this->barcode_code11($code);
				break;
			}
			case 'PHARMA': { // PHARMACODE
				$arrcode = $this->barcode_pharmacode($code);
				break;
			}
			case 'PHARMA2T': { // PHARMACODE TWO-TRACKS
				$arrcode = $this->barcode_pharmacode2t($code);
				break;
			}
			default: {
				$this->barcode_array = false;
			}
		}
		$this->barcode_array = $arrcode;
	}
	
	/**
	 * CODE 39 - ANSI MH10.8M-1983 - USD-3 - 3 of 9.
	 * General-purpose code in very wide use world-wide
	 * @param string $code code to represent.
	 * @param boolean $checksum if true add a checksum to the code
	 * @return array barcode representation.
	 * @access protected
	 */
	protected function barcode_code39($code, $extended=false, $checksum=false) {
		$chr['0'] = '111221211';
		$chr['1'] = '211211112';
		$chr['2'] = '112211112';
		$chr['3'] = '212211111';
		$chr['4'] = '111221112';
		$chr['5'] = '211221111';
		$chr['6'] = '112221111';
		$chr['7'] = '111211212';
		$chr['8'] = '211211211';
		$chr['9'] = '112211211';
		$chr['A'] = '211112112';
		$chr['B'] = '112112112';
		$chr['C'] = '212112111';
		$chr['D'] = '111122112';
		$chr['E'] = '211122111';
		$chr['F'] = '112122111';
		$chr['G'] = '111112212';
		$chr['H'] = '211112211';
		$chr['I'] = '112112211';
		$chr['J'] = '111122211';
		$chr['K'] = '211111122';
		$chr['L'] = '112111122';
		$chr['M'] = '212111121';
		$chr['N'] = '111121122';
		$chr['O'] = '211121121';
		$chr['P'] = '112121121';
		$chr['Q'] = '111111222';
		$chr['R'] = '211111221';
		$chr['S'] = '112111221';
		$chr['T'] = '111121221';
		$chr['U'] = '221111112';
		$chr['V'] = '122111112';
		$chr['W'] = '222111111';
		$chr['X'] = '121121112';
		$chr['Y'] = '221121111';
		$chr['Z'] = '122121111';
		$chr['-'] = '121111212';
		$chr['.'] = '221111211';
		$chr[' '] = '122111211';
		$chr['$'] = '121212111';
		$chr['/'] = '121211121';
		$chr['+'] = '121112121';
		$chr['%'] = '111212121';
		$chr['*'] = '121121211';
		
		$code = strtoupper($code);
		if ($extended) {
			// extended mode
			$code = $this->encode_code39_ext($code);
		}
		if ($code === false) {
			return false;
		}
		if ($checksum) {
			// checksum
			$code .= $this->checksum_code39($code);
		}
		// add start and stop codes
		$code = '*'.$code.'*';
		
		$bararray = array('code' => $code, 'maxw' => 0, 'maxh' => 1, 'bcode' => array());
		$k = 0;
		$clen = strlen($code);
		for ($i = 0; $i < $clen; ++$i) {
			$char = $code{$i};
			if(!isset($chr[$char])) {
				// invalid character
				return false;
			}
			for ($j = 0; $j < 9; ++$j) {
				if (($j % 2) == 0) {
					$t = true; // bar
				} else {
					$t = false; // space
				}
				$w = $chr[$char]{$j};
				$bararray['bcode'][$k] = array('t' => $t, 'w' => $w, 'h' => 1, 'p' => 0);
				$bararray['maxw'] += $w;
				++$k;
			}
			$bararray['bcode'][$k] = array('t' => false, 'w' => 1, 'h' => 1, 'p' => 0);
			$bararray['maxw'] += 1;
			++$k;
		}
		return $bararray;
	}
	
	/**
	 * Encode a string to be used for CODE 39 Extended mode.
	 * @param string $code code to represent.
	 * @return encoded string.
	 * @access protected
	 */
	protected function encode_code39_ext($code) {
		$encode = array(
			chr(0) => '%U', chr(1) => '$A', chr(2) => '$B', chr(3) => '$C',
			chr(4) => '$D', chr(5) => '$E', chr(6) => '$F', chr(7) => '$G',
			chr(8) => '$H', chr(9) => '$I', chr(10) => '$J', chr(11) => '£K',
			chr(12) => '$L', chr(13) => '$M', chr(14) => '$N', chr(15) => '$O',
			chr(16) => '$P', chr(17) => '$Q', chr(18) => '$R', chr(19) => '$S',
			chr(20) => '$T', chr(21) => '$U', chr(22) => '$V', chr(23) => '$W',
			chr(24) => '$X', chr(25) => '$Y', chr(26) => '$Z', chr(27) => '%A',
			chr(28) => '%B', chr(29) => '%C', chr(30) => '%D', chr(31) => '%E',
			chr(32) => ' ', chr(33) => '/A', chr(34) => '/B', chr(35) => '/C',
			chr(36) => '/D', chr(37) => '/E', chr(38) => '/F', chr(39) => '/G',
			chr(40) => '/H', chr(41) => '/I', chr(42) => '/J', chr(43) => '/K',
			chr(44) => '/L', chr(45) => '-', chr(46) => '.', chr(47) => '/O',
			chr(48) => '0', chr(49) => '1', chr(50) => '2', chr(51) => '3',
			chr(52) => '4', chr(53) => '5', chr(54) => '6', chr(55) => '7',
			chr(56) => '8', chr(57) => '9', chr(58) => '/Z', chr(59) => '%F',
			chr(60) => '%G', chr(61) => '%H', chr(62) => '%I', chr(63) => '%J',
			chr(64) => '%V', chr(65) => 'A', chr(66) => 'B', chr(67) => 'C',
			chr(68) => 'D', chr(69) => 'E', chr(70) => 'F', chr(71) => 'G',
			chr(72) => 'H', chr(73) => 'I', chr(74) => 'J', chr(75) => 'K',
			chr(76) => 'L', chr(77) => 'M', chr(78) => 'N', chr(79) => 'O',
			chr(80) => 'P', chr(81) => 'Q', chr(82) => 'R', chr(83) => 'S',
			chr(84) => 'T', chr(85) => 'U', chr(86) => 'V', chr(87) => 'W',
			chr(88) => 'X', chr(89) => 'Y', chr(90) => 'Z', chr(91) => '%K',
			chr(92) => '%L', chr(93) => '%M', chr(94) => '%N', chr(95) => '%O',
			chr(96) => '%W', chr(97) => '+A', chr(98) => '+B', chr(99) => '+C',
			chr(100) => '+D', chr(101) => '+E', chr(102) => '+F', chr(103) => '+G',
			chr(104) => '+H', chr(105) => '+I', chr(106) => '+J', chr(107) => '+K',
			chr(108) => '+L', chr(109) => '+M', chr(110) => '+N', chr(111) => '+O',
			chr(112) => '+P', chr(113) => '+Q', chr(114) => '+R', chr(115) => '+S',
			chr(116) => '+T', chr(117) => '+U', chr(118) => '+V', chr(119) => '+W',
			chr(120) => '+X', chr(121) => '+Y', chr(122) => '+Z', chr(123) => '%P',
			chr(124) => '%Q', chr(125) => '%R', chr(126) => '%S', chr(127) => '%T');
		$code_ext = '';
		$clen = strlen($code);
		for ($i = 0 ; $i < $clen; ++$i) {
			if (ord($code{$i}) > 127) {
				return false;
			}
			$code_ext .= $encode[$code{$i}];
		}
		return $code_ext;
	}
	
	/**
	 * Calculate CODE 39 checksum (modulo 43).
	 * @param string $code code to represent.
	 * @return char checksum.
	 * @access protected
	 */
	protected function checksum_code39($code) {
		$chars = array(
			'0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
			'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K',
			'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V',
			'W', 'X', 'Y', 'Z', '-', '.', ' ', '$', '/', '+', '%');
		$sum = 0;
		$clen = strlen($code);
		for ($i = 0 ; $i < $clen; ++$i) {
			$k = array_keys($chars, $code{$i});
			$sum += $k[0];
		}
		$j = ($sum % 43);
		return $chars[$j];
	}
	
	/**
	 * CODE 93 - USS-93
	 * Compact code similar to Code 39
	 * @param string $code code to represent.
	 * @param boolean $checksum if true add a checksum to the code
	 * @return array barcode representation.
	 * @access protected
	 */
	protected function barcode_code93($code) {
		$chr['0'] = '131112';
		$chr['1'] = '111213';
		$chr['2'] = '111312';
		$chr['3'] = '111411';
		$chr['4'] = '121113';
		$chr['5'] = '121212';
		$chr['6'] = '121311';
		$chr['7'] = '111114';
		$chr['8'] = '131211';
		$chr['9'] = '141111';
		$chr['A'] = '211113';
		$chr['B'] = '211212';
		$chr['C'] = '211311';
		$chr['D'] = '221112';
		$chr['E'] = '221211';
		$chr['F'] = '231111';
		$chr['G'] = '112113';
		$chr['H'] = '112212';
		$chr['I'] = '112311';
		$chr['J'] = '122112';
		$chr['K'] = '132111';
		$chr['L'] = '111123';
		$chr['M'] = '111222';
		$chr['N'] = '111321';
		$chr['O'] = '121122';
		$chr['P'] = '131121';
		$chr['Q'] = '212112';
		$chr['R'] = '212211';
		$chr['S'] = '211122';
		$chr['T'] = '211221';
		$chr['U'] = '221121';
		$chr['V'] = '222111';
		$chr['W'] = '112122';
		$chr['X'] = '112221';
		$chr['Y'] = '122121';
		$chr['Z'] = '123111';
		$chr['-'] = '121131';
		$chr['.'] = '311112';
		$chr[' '] = '311211';
		$chr['$'] = '321111';
		$chr['/'] = '112131';
		$chr['+'] = '113121';
		$chr['%'] = '211131';
		$chr[128] = '121221'; // ($)
		$chr[129] = '311121'; // (/)
		$chr[130] = '122211'; // (+)
		$chr[131] = '312111'; // (%)
		$chr['*'] = '111141';
		$code = strtoupper($code);
		$encode = array(
			chr(0) => chr(131).'U', chr(1) => chr(128).'A', chr(2) => chr(128).'B', chr(3) => chr(128).'C',
			chr(4) => chr(128).'D', chr(5) => chr(128).'E', chr(6) => chr(128).'F', chr(7) => chr(128).'G',
			chr(8) => chr(128).'H', chr(9) => chr(128).'I', chr(10) => chr(128).'J', chr(11) => '£K',
			chr(12) => chr(128).'L', chr(13) => chr(128).'M', chr(14) => chr(128).'N', chr(15) => chr(128).'O',
			chr(16) => chr(128).'P', chr(17) => chr(128).'Q', chr(18) => chr(128).'R', chr(19) => chr(128).'S',
			chr(20) => chr(128).'T', chr(21) => chr(128).'U', chr(22) => chr(128).'V', chr(23) => chr(128).'W',
			chr(24) => chr(128).'X', chr(25) => chr(128).'Y', chr(26) => chr(128).'Z', chr(27) => chr(131).'A',
			chr(28) => chr(131).'B', chr(29) => chr(131).'C', chr(30) => chr(131).'D', chr(31) => chr(131).'E',
			chr(32) => ' ', chr(33) => chr(129).'A', chr(34) => chr(129).'B', chr(35) => chr(129).'C',
			chr(36) => chr(129).'D', chr(37) => chr(129).'E', chr(38) => chr(129).'F', chr(39) => chr(129).'G',
			chr(40) => chr(129).'H', chr(41) => chr(129).'I', chr(42) => chr(129).'J', chr(43) => chr(129).'K',
			chr(44) => chr(129).'L', chr(45) => '-', chr(46) => '.', chr(47) => chr(129).'O',
			chr(48) => '0', chr(49) => '1', chr(50) => '2', chr(51) => '3',
			chr(52) => '4', chr(53) => '5', chr(54) => '6', chr(55) => '7',
			chr(56) => '8', chr(57) => '9', chr(58) => chr(129).'Z', chr(59) => chr(131).'F',
			chr(60) => chr(131).'G', chr(61) => chr(131).'H', chr(62) => chr(131).'I', chr(63) => chr(131).'J',
			chr(64) => chr(131).'V', chr(65) => 'A', chr(66) => 'B', chr(67) => 'C',
			chr(68) => 'D', chr(69) => 'E', chr(70) => 'F', chr(71) => 'G',
			chr(72) => 'H', chr(73) => 'I', chr(74) => 'J', chr(75) => 'K',
			chr(76) => 'L', chr(77) => 'M', chr(78) => 'N', chr(79) => 'O',
			chr(80) => 'P', chr(81) => 'Q', chr(82) => 'R', chr(83) => 'S',
			chr(84) => 'T', chr(85) => 'U', chr(86) => 'V', chr(87) => 'W',
			chr(88) => 'X', chr(89) => 'Y', chr(90) => 'Z', chr(91) => chr(131).'K',
			chr(92) => chr(131).'L', chr(93) => chr(131).'M', chr(94) => chr(131).'N', chr(95) => chr(131).'O',
			chr(96) => chr(131).'W', chr(97) => chr(130).'A', chr(98) => chr(130).'B', chr(99) => chr(130).'C',
			chr(100) => chr(130).'D', chr(101) => chr(130).'E', chr(102) => chr(130).'F', chr(103) => chr(130).'G',
			chr(104) => chr(130).'H', chr(105) => chr(130).'I', chr(106) => chr(130).'J', chr(107) => chr(130).'K',
			chr(108) => chr(130).'L', chr(109) => chr(130).'M', chr(110) => chr(130).'N', chr(111) => chr(130).'O',
			chr(112) => chr(130).'P', chr(113) => chr(130).'Q', chr(114) => chr(130).'R', chr(115) => chr(130).'S',
			chr(116) => chr(130).'T', chr(117) => chr(130).'U', chr(118) => chr(130).'V', chr(119) => chr(130).'W',
			chr(120) => chr(130).'X', chr(121) => chr(130).'Y', chr(122) => chr(130).'Z', chr(123) => chr(131).'P',
			chr(124) => chr(131).'Q', chr(125) => chr(131).'R', chr(126) => chr(131).'S', chr(127) => chr(131).'T');
		$code_ext = '';
		$clen = strlen($code);
		for ($i = 0 ; $i < $clen; ++$i) {
			if (ord($code{$i}) > 127) {
				return false;
			}
			$code_ext .= $encode[$code{$i}];
		}
		// checksum
		$code .= $this->checksum_code93($code);
		// add start and stop codes
		$code = '*'.$code.'*';
		$bararray = array('code' => $code, 'maxw' => 0, 'maxh' => 1, 'bcode' => array());
		$k = 0;
		$clen = strlen($code);
		for ($i = 0; $i < $clen; ++$i) {
			$char = $code{$i};
			if(!isset($chr[$char])) {
				// invalid character
				return false;
			}
			for ($j = 0; $j < 6; ++$j) {
				if (($j % 2) == 0) {
					$t = true; // bar
				} else {
					$t = false; // space
				}
				$w = $chr[$char]{$j};
				$bararray['bcode'][$k] = array('t' => $t, 'w' => $w, 'h' => 1, 'p' => 0);
				$bararray['maxw'] += $w;
				++$k;
			}
		}
		$bararray['bcode'][$k] = array('t' => true, 'w' => 1, 'h' => 1, 'p' => 0);
		$bararray['maxw'] += 1;
		++$k;		
		return $bararray;
	}
	
	/**
	 * Calculate CODE 93 checksum (modulo 47).
	 * @param string $code code to represent.
	 * @return string checksum code.
	 * @access protected
	 */
	protected function checksum_code93($code) {
		$chars = array(
			'0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
			'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K',
			'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V',
			'W', 'X', 'Y', 'Z', '-', '.', ' ', '$', '/', '+', '%');
		// translate special characters
		$code = strtr($code, chr(128).chr(129).chr(130).chr(131), '$/+%');	
		$len = strlen($code);
		// calculate check digit C
		$p = 1;
		$check = 0;
		for ($i = ($len - 1); $i >= 0; --$i) {
			$k = array_keys($chars, $code{$i});
			$check += ($k[0] * $p);
			++$p;
			if ($p > 20) {
				$p = 1;
			}
		}
		$check %= 47;
		$c = $chars[$check];
		$code .= $c;
		// calculate check digit K
		$p = 1;
		$check = 0;
		for ($i = $len; $i >= 0; --$i) {
			$k = array_keys($chars, $code{$i});
			$check += ($k[0] * $p);
			++$p;
			if ($p > 15) {
				$p = 1;
			}
		}
		$check %= 47;
		$k = $chars[$check];
		return $c.$k;
	}
	
	/**
	 * Checksum for standard 2 of 5 barcodes.
	 * @param string $code code to process.
	 * @return int checksum.
	 * @access protected
	 */
	protected function checksum_s25($code) {
		$len = strlen($code);
		$sum = 0;
		for ($i = 0; $i < $len; $i+=2) {
			$sum += $code{$i};
		}
		$sum *= 3;
		for ($i = 1; $i < $len; $i+=2) {
			$sum += ($code{$i});
		}
		$r = $sum % 10;
		if($r > 0) {
			$r = (10 - $r);
		}
		return $r;
	}
	
	/**
	 * MSI.
	 * Variation of Plessey code, with similar applications 
	 * Contains digits (0 to 9) and encodes the data only in the width of bars.
	 * @param string $code code to represent.
	 * @param boolean $checksum if true add a checksum to the code (modulo 11)
	 * @return array barcode representation.
	 * @access protected
	 */
	protected function barcode_msi($code, $checksum=false) {
		$chr['0'] = '100100100100';
		$chr['1'] = '100100100110';
		$chr['2'] = '100100110100';
		$chr['3'] = '100100110110';
		$chr['4'] = '100110100100';
		$chr['5'] = '100110100110';
		$chr['6'] = '100110110100';
		$chr['7'] = '100110110110';
		$chr['8'] = '110100100100';
		$chr['9'] = '110100100110';
		$chr['A'] = '110100110100';
		$chr['B'] = '110100110110';
		$chr['C'] = '110110100100';
		$chr['D'] = '110110100110';
		$chr['E'] = '110110110100';
		$chr['F'] = '110110110110';
		if ($checksum) {
			// add checksum
			$clen = strlen($code);
			$p = 2;
			$check = 0;
			for ($i = ($clen - 1); $i >= 0; --$i) {
				$check += (hexdec($code{$i}) * $p);
				++$p;
				if ($p > 7) {
					$p = 2;
				}
			}
			$check %= 11;
			if ($check > 0) {
				$check = 11 - $check;
			}
			$code .= $check;
		}
		$seq = '110'; // left guard
		$clen = strlen($code);
		for ($i = 0; $i < $clen; ++$i) {
			$digit = $code{$i};
			if (!isset($chr[$digit])) {
				// invalid character
				return false;
			}
			$seq .= $chr[$digit];
		}		
		$seq .= '1001'; // right guard
		$bararray = array('code' => $code, 'maxw' => 0, 'maxh' => 1, 'bcode' => array());
		return $this->binseq_to_array($seq, $bararray);
	}
	
	/**
	 * Standard 2 of 5 barcodes.
	 * Used in airline ticket marking, photofinishing
	 * Contains digits (0 to 9) and encodes the data only in the width of bars.
	 * @param string $code code to represent.
	 * @param boolean $checksum if true add a checksum to the code
	 * @return array barcode representation.
	 * @access protected
	 */
	protected function barcode_s25($code, $checksum=false) {
		$chr['0'] = '10101110111010';
		$chr['1'] = '11101010101110';
		$chr['2'] = '10111010101110';
		$chr['3'] = '11101110101010';
		$chr['4'] = '10101110101110';
		$chr['5'] = '11101011101010';
		$chr['6'] = '10111011101010';
		$chr['7'] = '10101011101110';
		$chr['8'] = '10101110111010';
		$chr['9'] = '10111010111010';
		if ($checksum) {
			// add checksum
			$code .= $this->checksum_s25($code);
		}
		if((strlen($code) % 2) != 0) {
			// add leading zero if code-length is odd
			$code = '0'.$code;
		}
		$seq = '11011010';
		$clen = strlen($code);
		for ($i = 0; $i < $clen; ++$i) {
			$digit = $code{$i};
			if (!isset($chr[$digit])) {
				// invalid character
				return false;
			}
			$seq .= $chr[$digit];
		}		
		$seq .= '1101011';
		$bararray = array('code' => $code, 'maxw' => 0, 'maxh' => 1, 'bcode' => array());
		return $this->binseq_to_array($seq, $bararray);
	}
	
	/**
	 * Convert binary barcode sequence to TCPDF barcode array
	 * @param string $seq barcode as binary sequence
	 * òparam array $bararray TCPDF barcode array to fill up
	 * @return array barcode representation.
	 * @access protected
	 */
	protected function binseq_to_array($seq, $bararray) {
		$len = strlen($seq);
		$w = 0;
		$k = 0;
		for ($i = 0; $i < $len; ++$i) {
			$w += 1;
			if (($i == ($len - 1)) OR (($i < ($len - 1)) AND ($seq{$i} != $seq{($i+1)}))) {
				if ($seq{$i} == '1') {
					$t = true; // bar
				} else {
					$t = false; // space
				}
				$bararray['bcode'][$k] = array('t' => $t, 'w' => $w, 'h' => 1, 'p' => 0);
				$bararray['maxw'] += $w;
				++$k;
				$w = 0;
			}
		}
		return $bararray;
	}
	
	/**
	 * Interleaved 2 of 5 barcodes.
	 * Compact numeric code, widely used in industry, air cargo
	 * Contains digits (0 to 9) and encodes the data in the width of both bars and spaces.
	 * @param string $code code to represent.
	 * @param boolean $checksum if true add a checksum to the code
	 * @return array barcode representation.
	 * @access protected
	 */
	protected function barcode_i25($code, $checksum=false) {
		$chr['0'] = '11221';
		$chr['1'] = '21112';
		$chr['2'] = '12112';
		$chr['3'] = '22111';
		$chr['4'] = '11212';
		$chr['5'] = '21211';
		$chr['6'] = '12211';
		$chr['7'] = '11122';
		$chr['8'] = '21121';
		$chr['9'] = '12121';
		$chr['A'] = '11';
		$chr['Z'] = '21';
		if ($checksum) {
			// add checksum
			$code .= $this->checksum_s25($code);
		}
		if((strlen($code) % 2) != 0) {
			// add leading zero if code-length is odd
			$code = '0'.$code;
		}
		// add start and stop codes
		$code = 'AA'.strtolower($code).'ZA';
			
		$bararray = array('code' => $code, 'maxw' => 0, 'maxh' => 1, 'bcode' => array());
		$k = 0;
		$clen = strlen($code);
		for ($i = 0; $i < $clen; $i = ($i + 2)) {
			$char_bar = $code{$i};
			$char_space = $code{$i+1};
			if((!isset($chr[$char_bar])) OR (!isset($chr[$char_space]))) {
				// invalid character
				return false;
			}
			// create a bar-space sequence
			$seq = '';
			$chrlen = strlen($chr[$char_bar]);
			for ($s = 0; $s < $chrlen; $s++){
				$seq .= $chr[$char_bar]{$s} . $chr[$char_space]{$s};
			}
			$seqlen = strlen($seq);
			for ($j = 0; $j < $seqlen; ++$j) {
				if (($j % 2) == 0) {
					$t = true; // bar
				} else {
					$t = false; // space
				}
				$w = $seq{$j};
				$bararray['bcode'][$k] = array('t' => $t, 'w' => $w, 'h' => 1, 'p' => 0);
				$bararray['maxw'] += $w;
				++$k;
			}
		}
		return $bararray;
	}
	
	/**
	 * C128 barcodes. 
	 * Very capable code, excellent density, high reliability; in very wide use world-wide
	 * @param string $code code to represent.
	 * @param string $type barcode type: A, B or C
	 * @return array barcode representation.
	 * @access protected
	 */
	protected function barcode_c128($code, $type='B') {
		$chr = array(
			'212222', /* 00 */
			'222122', /* 01 */
			'222221', /* 02 */
			'121223', /* 03 */
			'121322', /* 04 */
			'131222', /* 05 */
			'122213', /* 06 */
			'122312', /* 07 */
			'132212', /* 08 */
			'221213', /* 09 */
			'221312', /* 10 */
			'231212', /* 11 */
			'112232', /* 12 */
			'122132', /* 13 */
			'122231', /* 14 */
			'113222', /* 15 */
			'123122', /* 16 */
			'123221', /* 17 */
			'223211', /* 18 */
			'221132', /* 19 */
			'221231', /* 20 */
			'213212', /* 21 */
			'223112', /* 22 */
			'312131', /* 23 */
			'311222', /* 24 */
			'321122', /* 25 */
			'321221', /* 26 */
			'312212', /* 27 */
			'322112', /* 28 */
			'322211', /* 29 */
			'212123', /* 30 */
			'212321', /* 31 */
			'232121', /* 32 */
			'111323', /* 33 */
			'131123', /* 34 */
			'131321', /* 35 */
			'112313', /* 36 */
			'132113', /* 37 */
			'132311', /* 38 */
			'211313', /* 39 */
			'231113', /* 40 */
			'231311', /* 41 */
			'112133', /* 42 */
			'112331', /* 43 */
			'132131', /* 44 */
			'113123', /* 45 */
			'113321', /* 46 */
			'133121', /* 47 */
			'313121', /* 48 */
			'211331', /* 49 */
			'231131', /* 50 */
			'213113', /* 51 */
			'213311', /* 52 */
			'213131', /* 53 */
			'311123', /* 54 */
			'311321', /* 55 */
			'331121', /* 56 */
			'312113', /* 57 */
			'312311', /* 58 */
			'332111', /* 59 */
			'314111', /* 60 */
			'221411', /* 61 */
			'431111', /* 62 */
			'111224', /* 63 */
			'111422', /* 64 */
			'121124', /* 65 */
			'121421', /* 66 */
			'141122', /* 67 */
			'141221', /* 68 */
			'112214', /* 69 */
			'112412', /* 70 */
			'122114', /* 71 */
			'122411', /* 72 */
			'142112', /* 73 */
			'142211', /* 74 */
			'241211', /* 75 */
			'221114', /* 76 */
			'413111', /* 77 */
			'241112', /* 78 */
			'134111', /* 79 */
			'111242', /* 80 */
			'121142', /* 81 */
			'121241', /* 82 */
			'114212', /* 83 */
			'124112', /* 84 */
			'124211', /* 85 */
			'411212', /* 86 */
			'421112', /* 87 */
			'421211', /* 88 */
			'212141', /* 89 */
			'214121', /* 90 */
			'412121', /* 91 */
			'111143', /* 92 */
			'111341', /* 93 */
			'131141', /* 94 */
			'114113', /* 95 */
			'114311', /* 96 */
			'411113', /* 97 */
			'411311', /* 98 */
			'113141', /* 99 */
			'114131', /* 100 */
			'311141', /* 101 */
			'411131', /* 102 */
			'211412', /* 103 START A */
			'211214', /* 104 START B  */
			'211232', /* 105 START C  */
			'233111', /* STOP */
			'200000'  /* END */
		);
		$keys = '';
		switch(strtoupper($type)) {
			case 'A': {
				$startid = 103;
				$keys = ' !"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\\]^_';
				for ($i = 0; $i < 32; ++$i) {
					$keys .= chr($i);
				}
				break;
			}
			case 'B': {
				$startid = 104;
				$keys = ' !"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\\]^_`abcdefghijklmnopqrstuvwxyz{|}~'.chr(127);
				break;
			}
			case 'C': {
				$startid = 105;
				$keys = '';
				if ((strlen($code) % 2) != 0) {
					// The length of barcode value must be even ($code). You must pad the number with zeros
					return false;
				}
				for ($i = 0; $i <= 99; ++$i) {
					$keys .= chr($i);
				}
				$new_code = '';
				$hclen = (strlen($code) / 2);
				for ($i = 0; $i < $hclen; ++$i) {
					$new_code .= chr(intval($code{(2 * $i)}.$code{(2 * $i + 1)}));
				}
				$code = $new_code;
				break;
			}
			default: {
				return false;
			}
		}
		// calculate check character
		$sum = $startid;
		$clen = strlen($code);
		for ($i = 0; $i < $clen; ++$i) {
			$sum +=  (strpos($keys, $code{$i}) * ($i+1));
		}
		$check = ($sum % 103);
		// add start, check and stop codes
		$code = chr($startid).$code.chr($check).chr(106).chr(107);
		$bararray = array('code' => $code, 'maxw' => 0, 'maxh' => 1, 'bcode' => array());
		$k = 0;
		$len = strlen($code);
		for ($i = 0; $i < $len; ++$i) {
			$ck = strpos($keys, $code{$i});
			if (($i == 0) OR ($i > ($len-4))) {
				$char_num = ord($code{$i});
				$seq = $chr[$char_num];
			} elseif(($ck >= 0) AND isset($chr[$ck])) {
					$seq = $chr[$ck];
			} else {
				// invalid character
				return false;
			}
			for ($j = 0; $j < 6; ++$j) {
				if (($j % 2) == 0) {
					$t = true; // bar
				} else {
					$t = false; // space
				}
				$w = $seq{$j};
				$bararray['bcode'][$k] = array('t' => $t, 'w' => $w, 'h' => 1, 'p' => 0);
				$bararray['maxw'] += $w;
				++$k;
			}
		}
		return $bararray;		
	}
	
	/**
	 * EAN13 and UPC-A barcodes.
	 * EAN13: European Article Numbering international retail product code
	 * UPC-A: Universal product code seen on almost all retail products in the USA and Canada
	 * UPC-E: Short version of UPC symbol
	 * @param string $code code to represent.
	 * @param string $len barcode type: 6 = UPC-E, 8 = EAN8, 13 = EAN13, 12 = UPC-A
	 * @return array barcode representation.
	 * @access protected
	 */
	protected function barcode_eanupc($code, $len=13) {
		$upce = false;
		if ($len == 6) {
			$len = 12; // UPC-A
			$upce = true; // UPC-E mode
		}
		$data_len = $len - 1;
		//Padding
		$code = str_pad($code, $data_len, '0', STR_PAD_LEFT);
		$code_len = strlen($code);
		// calculate check digit
		$sum_a = 0;
		for ($i = 1; $i < $data_len; $i+=2) {
			$sum_a += $code{$i};
		}
		if ($len > 12) {
			$sum_a *= 3;
		}
		$sum_b = 0;
		for ($i = 0; $i < $data_len; $i+=2) {
			$sum_b += ($code{$i});
		}
		if ($len < 13) {
			$sum_b *= 3;
		}
		$r = ($sum_a + $sum_b) % 10;
		if($r > 0) {
			$r = (10 - $r);
		}
		if ($code_len == $data_len) {
			// add check digit
			$code .= $r;
		} elseif ($r !== intval($code{$data_len})) {
			// wrong checkdigit
			return false;
		}
		if ($len == 12) {
			// UPC-A
			$code = '0'.$code;
			++$len;
		}
		if ($upce) {
			// convert UPC-A to UPC-E
			$tmp = substr($code, 4, 3);
			if (($tmp == '000') OR ($tmp == '100') OR ($tmp == '200')) {
				// manufacturer code ends in 000, 100, or 200
				$upce_code = substr($code, 2, 2).substr($code, 9, 3).substr($code, 4, 1);
			} else {
				$tmp = substr($code, 5, 2);
				if ($tmp == '00') {
					// manufacturer code ends in 00
					$upce_code = substr($code, 2, 3).substr($code, 10, 2).'3';
				} else {
					$tmp = substr($code, 6, 1);
					if ($tmp == '0') {
						// manufacturer code ends in 0
						$upce_code = substr($code, 2, 4).substr($code, 11, 1).'4';
					} else {
						// manufacturer code does not end in zero
						$upce_code = substr($code, 2, 5).substr($code, 11, 1);
					}
				}
			}
		}
		//Convert digits to bars
		$codes = array(
			'A'=>array( // left odd parity
				'0'=>'0001101',
				'1'=>'0011001',
				'2'=>'0010011',
				'3'=>'0111101',
				'4'=>'0100011',
				'5'=>'0110001',
				'6'=>'0101111',
				'7'=>'0111011',
				'8'=>'0110111',
				'9'=>'0001011'),
			'B'=>array( // left even parity
				'0'=>'0100111',
				'1'=>'0110011',
				'2'=>'0011011',
				'3'=>'0100001',
				'4'=>'0011101',
				'5'=>'0111001',
				'6'=>'0000101',
				'7'=>'0010001',
				'8'=>'0001001',
				'9'=>'0010111'),
			'C'=>array( // right
				'0'=>'1110010',
				'1'=>'1100110',
				'2'=>'1101100',
				'3'=>'1000010',
				'4'=>'1011100',
				'5'=>'1001110',
				'6'=>'1010000',
				'7'=>'1000100',
				'8'=>'1001000',
				'9'=>'1110100')
		);
		$parities = array(
			'0'=>array('A','A','A','A','A','A'),
			'1'=>array('A','A','B','A','B','B'),
			'2'=>array('A','A','B','B','A','B'),
			'3'=>array('A','A','B','B','B','A'),
			'4'=>array('A','B','A','A','B','B'),
			'5'=>array('A','B','B','A','A','B'),
			'6'=>array('A','B','B','B','A','A'),
			'7'=>array('A','B','A','B','A','B'),
			'8'=>array('A','B','A','B','B','A'),
			'9'=>array('A','B','B','A','B','A')
		);
		$upce_parities = array();
		$upce_parities[0] = array(
			'0'=>array('B','B','B','A','A','A'),
			'1'=>array('B','B','A','B','A','A'),
			'2'=>array('B','B','A','A','B','A'),
			'3'=>array('B','B','A','A','A','B'),
			'4'=>array('B','A','B','B','A','A'),
			'5'=>array('B','A','A','B','B','A'),
			'6'=>array('B','A','A','A','B','B'),
			'7'=>array('B','A','B','A','B','A'),
			'8'=>array('B','A','B','A','A','B'),
			'9'=>array('B','A','A','B','A','B')
		);
		$upce_parities[1] = array(
			'0'=>array('A','A','A','B','B','B'),
			'1'=>array('A','A','B','A','B','B'),
			'2'=>array('A','A','B','B','A','B'),
			'3'=>array('A','A','B','B','B','A'),
			'4'=>array('A','B','A','A','B','B'),
			'5'=>array('A','B','B','A','A','B'),
			'6'=>array('A','B','B','B','A','A'),
			'7'=>array('A','B','A','B','A','B'),
			'8'=>array('A','B','A','B','B','A'),
			'9'=>array('A','B','B','A','B','A')
		);
		$k = 0;
		$seq = '101'; // left guard bar
		if ($upce) {
			$bararray = array('code' => $upce_code, 'maxw' => 0, 'maxh' => 1, 'bcode' => array());
			$p = $upce_parities[$code{1}][$r];
			for ($i = 0; $i < 6; ++$i) {
				$seq .= $codes[$p[$i]][$upce_code{$i}];
			}
			$seq .= '010101'; // right guard bar
		} else {
			$bararray = array('code' => $code, 'maxw' => 0, 'maxh' => 1, 'bcode' => array());
			$half_len = ceil($len / 2);
			if ($len == 8) {
				for ($i = 0; $i < $half_len; ++$i) {
					$seq .= $codes['A'][$code{$i}];
				}
			} else {
				$p = $parities[$code{0}];
				for ($i = 1; $i < $half_len; ++$i) {
					$seq .= $codes[$p[$i-1]][$code{$i}];
				}
			}
			$seq .= '01010'; // center guard bar
			for ($i = $half_len; $i < $len; ++$i) {
				$seq .= $codes['C'][$code{$i}];
			}
			$seq .= '101'; // right guard bar
		}
		$clen = strlen($seq);
		$w = 0;
		for ($i = 0; $i < $clen; ++$i) {
			$w += 1;
			if (($i == ($clen - 1)) OR (($i < ($clen - 1)) AND ($seq{$i} != $seq{($i+1)}))) {
				if ($seq{$i} == '1') {
					$t = true; // bar
				} else {
					$t = false; // space
				}
				$bararray['bcode'][$k] = array('t' => $t, 'w' => $w, 'h' => 1, 'p' => 0);
				$bararray['maxw'] += $w;
				++$k;
				$w = 0;
			}
		}
		return $bararray;
	}
	
	/**
	 * UPC-Based Extentions
	 * 2-Digit Ext.: Used to indicate magazines and newspaper issue numbers
	 * 5-Digit Ext.: Used to mark suggested retail price of books
	 * @param string $code code to represent.
	 * @param string $len barcode type: 2 = 2-Digit, 5 = 5-Digit
	 * @return array barcode representation.
	 * @access protected
	 */
	protected function barcode_eanext($code, $len=5) {
		//Padding
		$code = str_pad($code, $len, '0', STR_PAD_LEFT);
		// calculate check digit
		if ($len == 2) {
			$r = $code % 4;
		} elseif ($len == 5) {
			$r = (3 * ($code{0} + $code{2} + $code{4})) + (9 * ($code{1} + $code{3}));
			$r %= 10;
		} else {
			return false;
		}
		//Convert digits to bars
		$codes = array(
			'A'=>array( // left odd parity
				'0'=>'0001101',
				'1'=>'0011001',
				'2'=>'0010011',
				'3'=>'0111101',
				'4'=>'0100011',
				'5'=>'0110001',
				'6'=>'0101111',
				'7'=>'0111011',
				'8'=>'0110111',
				'9'=>'0001011'),
			'B'=>array( // left even parity
				'0'=>'0100111',
				'1'=>'0110011',
				'2'=>'0011011',
				'3'=>'0100001',
				'4'=>'0011101',
				'5'=>'0111001',
				'6'=>'0000101',
				'7'=>'0010001',
				'8'=>'0001001',
				'9'=>'0010111')
		);
		$parities = array();
		$parities[2] = array(
			'0'=>array('A','A'),
			'1'=>array('A','B'),
			'2'=>array('B','A'),
			'3'=>array('B','B')
		);
		$parities[5] = array(
			'0'=>array('B','B','A','A','A'),
			'1'=>array('B','A','B','A','A'),
			'2'=>array('B','A','A','B','A'),
			'3'=>array('B','A','A','A','B'),
			'4'=>array('A','B','B','A','A'),
			'5'=>array('A','A','B','B','A'),
			'6'=>array('A','A','A','B','B'),
			'7'=>array('A','B','A','B','A'),
			'8'=>array('A','B','A','A','B'),
			'9'=>array('A','A','B','A','B')
		);	
		$p = $parities[$len][$r];
		$seq = '1011'; // left guard bar
		$seq .= $codes[$p[0]][$code{0}];
		for ($i = 1; $i < $len; ++$i) {
			$seq .= '01'; // separator
			$seq .= $codes[$p[$i]][$code{$i}];
		}
		$bararray = array('code' => $code, 'maxw' => 0, 'maxh' => 1, 'bcode' => array());
		return $this->binseq_to_array($seq, $bararray);
	}
	
	/**
	 * POSTNET and PLANET barcodes.
	 * Used by U.S. Postal Service for automated mail sorting
	 * @param string $code zip code to represent. Must be a string containing a zip code of the form DDDDD or DDDDD-DDDD.
	 * @param boolean $planet if true print the PLANET barcode, otherwise print POSTNET
	 * @return array barcode representation.
	 * @access protected
	 */
	protected function barcode_postnet($code, $planet=false) {
		// bar lenght
		if ($planet) {
			$barlen = Array(
				0 => Array(1,1,2,2,2),
				1 => Array(2,2,2,1,1),
				2 => Array(2,2,1,2,1),
				3 => Array(2,2,1,1,2),
				4 => Array(2,1,2,2,1),
				5 => Array(2,1,2,1,2),
				6 => Array(2,1,1,2,2),
				7 => Array(1,2,2,2,1),
				8 => Array(1,2,2,1,2),
				9 => Array(1,2,1,2,2)
			);
		} else {
			$barlen = Array(
				0 => Array(2,2,1,1,1),
				1 => Array(1,1,1,2,2),
				2 => Array(1,1,2,1,2),
				3 => Array(1,1,2,2,1),
				4 => Array(1,2,1,1,2),
				5 => Array(1,2,1,2,1),
				6 => Array(1,2,2,1,1),
				7 => Array(2,1,1,1,2),
				8 => Array(2,1,1,2,1),
				9 => Array(2,1,2,1,1)
			);
		}
		$bararray = array('code' => $code, 'maxw' => 0, 'maxh' => 2, 'bcode' => array());
		$k = 0;
		$code = str_replace('-', '', $code);
		$code = str_replace(' ', '', $code);
		$len = strlen($code);
		// calculate checksum
		$sum = 0;
		for ($i = 0; $i < $len; ++$i) {
			$sum += intval($code{$i});
		}
		$chkd = ($sum % 10);
		if($chkd > 0) {
			$chkd = (10 - $chkd);
		}
		$code .= $chkd;
		$len = strlen($code);
		// start bar
		$bararray['bcode'][$k++] = array('t' => 1, 'w' => 1, 'h' => 2, 'p' => 0);
		$bararray['bcode'][$k++] = array('t' => 0, 'w' => 1, 'h' => 2, 'p' => 0);
		$bararray['maxw'] += 2;
		for ($i = 0; $i < $len; ++$i) {
			for ($j = 0; $j < 5; ++$j) {
				$h = $barlen[$code{$i}][$j];
				$p = floor(1 / $h);
				$bararray['bcode'][$k++] = array('t' => 1, 'w' => 1, 'h' => $h, 'p' => $p);
				$bararray['bcode'][$k++] = array('t' => 0, 'w' => 1, 'h' => 2, 'p' => 0);
				$bararray['maxw'] += 2;
			}
		}
		// end bar
		$bararray['bcode'][$k++] = array('t' => 1, 'w' => 1, 'h' => 2, 'p' => 0);
		$bararray['maxw'] += 1;
		return $bararray;
	}
	
	/**
	 * RMS4CC - CBC - KIX
	 * RMS4CC (Royal Mail 4-state Customer Code) - CBC (Customer Bar Code) - KIX (Klant index - Customer index)
	 * RM4SCC is the name of the barcode symbology used by the Royal Mail for its Cleanmail service.
	 * @param string $code code to print
	 * @param boolean $kix if true prints the KIX variation (doesn't use the start and end symbols, and the checksum) - in this case the house number must be sufficed with an X and placed at the end of the code.
	 * @return array barcode representation.
	 * @access protected
	 */
	protected function barcode_rms4cc($code, $kix=false) {
		$notkix = !$kix;
		// bar mode
		// 1 = pos 1, length 2
		// 2 = pos 1, length 3
		// 3 = pos 2, length 1
		// 4 = pos 2, length 2
		$barmode = array(
			'0' => array(3,3,2,2),
			'1' => array(3,4,1,2),
			'2' => array(3,4,2,1),
			'3' => array(4,3,1,2),
			'4' => array(4,3,2,1),
			'5' => array(4,4,1,1),
			'6' => array(3,1,4,2),
			'7' => array(3,2,3,2),
			'8' => array(3,2,4,1),
			'9' => array(4,1,3,2),
			'A' => array(4,1,4,1),
			'B' => array(4,2,3,1),
			'C' => array(3,1,2,4),
			'D' => array(3,2,1,4),
			'E' => array(3,2,2,3),
			'F' => array(4,1,1,4),
			'G' => array(4,1,2,3),
			'H' => array(4,2,1,3),
			'I' => array(1,3,4,2),
			'J' => array(1,4,3,2),
			'K' => array(1,4,4,1),
			'L' => array(2,3,3,2),
			'M' => array(2,3,4,1),
			'N' => array(2,4,3,1),
			'O' => array(1,3,2,4),
			'P' => array(1,4,1,4),
			'Q' => array(1,4,2,3),
			'R' => array(2,3,1,4),
			'S' => array(2,3,2,3),
			'T' => array(2,4,1,3),
			'U' => array(1,1,4,4),
			'V' => array(1,2,3,4),
			'W' => array(1,2,4,3),
			'X' => array(2,1,3,4),
			'Y' => array(2,1,4,3),
			'Z' => array(2,2,3,3)		
		);
		$code = strtoupper($code);
		$len = strlen($code);
		$bararray = array('code' => $code, 'maxw' => 0, 'maxh' => 3, 'bcode' => array());
		if ($notkix) {
			// table for checksum calculation (row,col)
			$checktable = array(
				'0' => array(1,1),
				'1' => array(1,2),
				'2' => array(1,3),
				'3' => array(1,4),
				'4' => array(1,5),
				'5' => array(1,0),
				'6' => array(2,1),
				'7' => array(2,2),
				'8' => array(2,3),
				'9' => array(2,4),
				'A' => array(2,5),
				'B' => array(2,0),
				'C' => array(3,1),
				'D' => array(3,2),
				'E' => array(3,3),
				'F' => array(3,4),
				'G' => array(3,5),
				'H' => array(3,0),
				'I' => array(4,1),
				'J' => array(4,2),
				'K' => array(4,3),
				'L' => array(4,4),
				'M' => array(4,5),
				'N' => array(4,0),
				'O' => array(5,1),
				'P' => array(5,2),
				'Q' => array(5,3),
				'R' => array(5,4),
				'S' => array(5,5),
				'T' => array(5,0),
				'U' => array(0,1),
				'V' => array(0,2),
				'W' => array(0,3),
				'X' => array(0,4),
				'Y' => array(0,5),
				'Z' => array(0,0)
			);
			$row = 0;
			$col = 0;
			for ($i = 0; $i < $len; ++$i) {
				$row += $checktable[$code{$i}][0];
				$col += $checktable[$code{$i}][1];
			}
			$row %= 6;
			$col %= 6;
			$chk = array_keys($checktable, array($row,$col));
			$code .= $chk[0];
			++$len;
		}
		$k = 0;
		if ($notkix) {
			// start bar
			$bararray['bcode'][$k++] = array('t' => 1, 'w' => 1, 'h' => 2, 'p' => 0);
			$bararray['bcode'][$k++] = array('t' => 0, 'w' => 1, 'h' => 2, 'p' => 0);
			$bararray['maxw'] += 2;
		}
		for ($i = 0; $i < $len; ++$i) {
			for ($j = 0; $j < 4; ++$j) {
				switch ($barmode[$code{$i}][$j]) {
					case 1: {
						$p = 0;
						$h = 2;
						break;
					}
					case 2: {
						$p = 0;
						$h = 3;
						break;
					}
					case 3: {
						$p = 1;
						$h = 1;
						break;
					}
					case 4: {
						$p = 1;
						$h = 2;
						break;
					}
				}
				$bararray['bcode'][$k++] = array('t' => 1, 'w' => 1, 'h' => $h, 'p' => $p);
				$bararray['bcode'][$k++] = array('t' => 0, 'w' => 1, 'h' => 2, 'p' => 0);
				$bararray['maxw'] += 2;
			}
		}
		if ($notkix) {
			// stop bar
			$bararray['bcode'][$k++] = array('t' => 1, 'w' => 1, 'h' => 3, 'p' => 0);
			$bararray['maxw'] += 1;
		}
		return $bararray;
	}
	
	/**
	 * CODABAR barcodes.
	 * Older code often used in library systems, sometimes in blood banks
	 * @param string $code code to represent.
	 * @return array barcode representation.
	 * @access protected
	 */
	protected function barcode_codabar($code) {
		$chr = array(
			'0' => '11111221',
			'1' => '11112211',
			'2' => '11121121',
			'3' => '22111111',
			'4' => '11211211',
			'5' => '21111211',
			'6' => '12111121',
			'7' => '12112111',
			'8' => '12211111',
			'9' => '21121111',
			'-' => '11122111',
			'$' => '11221111',
			':' => '21112121',
			'/' => '21211121',
			'.' => '21212111',
			'+' => '11222221',
			'A' => '11221211',
			'B' => '12121121',
			'C' => '11121221',
			'D' => '11122211'
		);
		$bararray = array('code' => $code, 'maxw' => 0, 'maxh' => 1, 'bcode' => array());
		$k = 0;
		$w = 0;
		$seq = '';
		$code = 'A'.strtoupper($code).'A';
		$len = strlen($code);
		for ($i = 0; $i < $len; ++$i) {
			if (!isset($chr[$code{$i}])) {
				return false;
			}
			$seq = $chr[$code{$i}];
			for ($j = 0; $j < 8; ++$j) {
				if (($j % 2) == 0) {
					$t = true; // bar
				} else {
					$t = false; // space
				}
				$w = $seq{$j};
				$bararray['bcode'][$k] = array('t' => $t, 'w' => $w, 'h' => 1, 'p' => 0);
				$bararray['maxw'] += $w;
				++$k;
			}
		}
		return $bararray;
	}
	
	/**
	 * CODE11 barcodes.
	 * Used primarily for labeling telecommunications equipment
	 * @param string $code code to represent.
	 * @return array barcode representation.
	 * @access protected
	 */
	protected function barcode_code11($code) {
		$chr = array(
			'0' => '111121',
			'1' => '211121',
			'2' => '121121',
			'3' => '221111',
			'4' => '112121',
			'5' => '212111',
			'6' => '122111',
			'7' => '111221',
			'8' => '211211',
			'9' => '211111',
			'-' => '112111',
			'S' => '112211'
		);
		
		$bararray = array('code' => $code, 'maxw' => 0, 'maxh' => 1, 'bcode' => array());
		$k = 0;
		$w = 0;
		$seq = '';
		$len = strlen($code);
		// calculate check digit C
		$p = 1;
		$check = 0;
		for ($i = ($len - 1); $i >= 0; --$i) {
			$digit = $code{$i};
			if ($digit == '-') {
				$dval = 10;
			} else {
				$dval = intval($digit);
			}
			$check += ($dval * $p);
			++$p;
			if ($p > 10) {
				$p = 1;
			}
		}
		$check %= 11;
		if ($check == 10) {
			$check = '-';
		} 
		$code .= $check;
		if ($len > 10) {
			// calculate check digit K
			$p = 1;
			$check = 0;
			for ($i = $len; $i >= 0; --$i) {
				$digit = $code{$i};
				if ($digit == '-') {
					$dval = 10;
				} else {
					$dval = intval($digit);
				}
				$check += ($dval * $p);
				++$p;
				if ($p > 9) {
					$p = 1;
				}
			}
			$check %= 11;
			$code .= $check;
			++$len;
		}
		$code = 'S'.$code.'S';
		$len += 3;
		for ($i = 0; $i < $len; ++$i) {
			if (!isset($chr[$code{$i}])) {
				return false;
			}
			$seq = $chr[$code{$i}];
			for ($j = 0; $j < 6; ++$j) {
				if (($j % 2) == 0) {
					$t = true; // bar
				} else {
					$t = false; // space
				}
				$w = $seq{$j};
				$bararray['bcode'][$k] = array('t' => $t, 'w' => $w, 'h' => 1, 'p' => 0);
				$bararray['maxw'] += $w;
				++$k;
			}
		}
		return $bararray;
	}
	
	/**
	 * Pharmacode
	 * Contains digits (0 to 9)
	 * @param string $code code to represent.
	 * @return array barcode representation.
	 * @access protected
	 */
	protected function barcode_pharmacode($code) {
		$seq = '';
		$code = intval($code);
		while ($code > 0) {
			if (($code % 2) == 0) {
				$seq .= '11100';
				$code -= 2;
			} else {
				$seq .= '100';
				$code -= 1;
			}
			$code /= 2;
		}
		$seq = substr($seq, 0, -2);
		$seq = strrev($seq);
		$bararray = array('code' => $code, 'maxw' => 0, 'maxh' => 1, 'bcode' => array());
		return $this->binseq_to_array($seq, $bararray);
	}
	
	/**
	 * Pharmacode two-track
	 * Contains digits (0 to 9)
	 * @param string $code code to represent.
	 * @return array barcode representation.
	 * @access protected
	 */
	protected function barcode_pharmacode2t($code) {
		$seq = '';
		$code = intval($code);
		do {
			switch ($code % 3) {
				case 0: {
					$seq .= '3';
					$code = ($code - 3) / 3;
					break;
				}
				case 1: {
					$seq .= '1';
					$code = ($code - 1) / 3;
					break;
				}
				case 2: {
					$seq .= '2';
					$code = ($code - 2) / 3;
					break;
				}
			}
		} while($code != 0);
		$seq = strrev($seq);
		$k = 0;
		$bararray = array('code' => $code, 'maxw' => 0, 'maxh' => 2, 'bcode' => array());
		$len = strlen($seq);
		for ($i = 0; $i < $len; ++$i) {
			switch ($seq{$i}) {
				case '1': {
					$p = 1;
					$h = 1;
					break;
				}
				case '2': {
					$p = 0;
					$h = 1;
					break;
				}
				case '3': {
					$p = 0;
					$h = 2;
					break;
				}
			}
			$bararray['bcode'][$k++] = array('t' => 1, 'w' => 1, 'h' => $h, 'p' => $p);
			$bararray['bcode'][$k++] = array('t' => 0, 'w' => 1, 'h' => 2, 'p' => 0);
			$bararray['maxw'] += 2;
		}
		unset($bararray['bcode'][($k - 1)]);
		--$bararray['maxw'];
		return $bararray;
	}
	
	
	/**
	 * IMB - Intelligent Mail Barcode - Onecode - USPS-B-3200
	 * (requires PHP bcmath extension) 
	 * Intelligent Mail barcode is a 65-bar code for use on mail in the United States.
	 * The fields are described as follows:<ul><li>The Barcode Identifier shall be assigned by USPS to encode the presort identification that is currently printed in human readable form on the optional endorsement line (OEL) as well as for future USPS use. This shall be two digits, with the second digit in the range of 0–4. The allowable encoding ranges shall be 00–04, 10–14, 20–24, 30–34, 40–44, 50–54, 60–64, 70–74, 80–84, and 90–94.</li><li>The Service Type Identifier shall be assigned by USPS for any combination of services requested on the mailpiece. The allowable encoding range shall be 000http://it2.php.net/manual/en/function.dechex.php–999. Each 3-digit value shall correspond to a particular mail class with a particular combination of service(s). Each service program, such as OneCode Confirm and OneCode ACS, shall provide the list of Service Type Identifier values.</li><li>The Mailer or Customer Identifier shall be assigned by USPS as a unique, 6 or 9 digit number that identifies a business entity. The allowable encoding range for the 6 digit Mailer ID shall be 000000- 899999, while the allowable encoding range for the 9 digit Mailer ID shall be 900000000-999999999.</li><li>The Serial or Sequence Number shall be assigned by the mailer for uniquely identifying and tracking mailpieces. The allowable encoding range shall be 000000000–999999999 when used with a 6 digit Mailer ID and 000000-999999 when used with a 9 digit Mailer ID. e. The Delivery Point ZIP Code shall be assigned by the mailer for routing the mailpiece. This shall replace POSTNET for routing the mailpiece to its final delivery point. The length may be 0, 5, 9, or 11 digits. The allowable encoding ranges shall be no ZIP Code, 00000–99999,  000000000–999999999, and 00000000000–99999999999.</li></ul>
	 * @param string $code code to print, separate the ZIP (routing code) from the rest using a minus char '-' (BarcodeID_ServiceTypeID_MailerID_SerialNumber-RoutingCode)
	 * @return array barcode representation.
	 * @access protected
	 */
	protected function barcode_imb($code) {
		$asc_chr = array(4,0,2,6,3,5,1,9,8,7,1,2,0,6,4,8,2,9,5,3,0,1,3,7,4,6,8,9,2,0,5,1,9,4,3,8,6,7,1,2,4,3,9,5,7,8,3,0,2,1,4,0,9,1,7,0,2,4,6,3,7,1,9,5,8);
		$dsc_chr = array(7,1,9,5,8,0,2,4,6,3,5,8,9,7,3,0,6,1,7,4,6,8,9,2,5,1,7,5,4,3,8,7,6,0,2,5,4,9,3,0,1,6,8,2,0,4,5,9,6,7,5,2,6,3,8,5,1,9,8,7,4,0,2,6,3);
		$asc_pos = array(3,0,8,11,1,12,8,11,10,6,4,12,2,7,9,6,7,9,2,8,4,0,12,7,10,9,0,7,10,5,7,9,6,8,2,12,1,4,2,0,1,5,4,6,12,1,0,9,4,7,5,10,2,6,9,11,2,12,6,7,5,11,0,3,2);
		$dsc_pos = array(2,10,12,5,9,1,5,4,3,9,11,5,10,1,6,3,4,1,10,0,2,11,8,6,1,12,3,8,6,4,4,11,0,6,1,9,11,5,3,7,3,10,7,11,8,2,10,3,5,8,0,3,12,11,8,4,5,1,3,0,7,12,9,8,10);
		$code_arr = explode('-', $code);
		$tracking_number = $code_arr[0];
		if (isset($code_arr[1])) {
			$routing_code = $code_arr[1];
		} else {
			$routing_code = '';
		}
		// Conversion of Routing Code
		switch (strlen($routing_code)) {
			case 0: {
				$binary_code = 0;
				break;
			}
			case 5: {
				$binary_code = bcadd($routing_code, '1');
				break;
			}
			case 9: {
				$binary_code = bcadd($routing_code, '100001');
				break;
			}
			case 11: {
				$binary_code = bcadd($routing_code, '1000100001');
				break;
			}
			default: {
				return false;
				break;
			}
		}
		$binary_code = bcmul($binary_code, 10);
		$binary_code = bcadd($binary_code, $tracking_number{0});
		$binary_code = bcmul($binary_code, 5);
		$binary_code = bcadd($binary_code, $tracking_number{1});
		$binary_code .= substr($tracking_number, 2, 18);
		// convert to hexadecimal
		$binary_code = $this->dec_to_hex($binary_code);
		// pad to get 13 bytes
		$binary_code = str_pad($binary_code, 26, '0', STR_PAD_LEFT);
		// convert string to array of bytes
		$binary_code_arr = chunk_split($binary_code, 2, "\r");
		$binary_code_arr = substr($binary_code_arr, 0, -1);
		$binary_code_arr = explode("\r", $binary_code_arr);
		// calculate frame check sequence
		$fcs = $this->imb_crc11fcs($binary_code_arr);
		// exclude first 2 bits from first byte
		$first_byte = sprintf('%2s', dechex((hexdec($binary_code_arr[0]) << 2) >> 2));
		$binary_code_102bit = $first_byte.substr($binary_code, 2);
		// convert binary data to codewords
		$codewords = array();
		$data = $this->hex_to_dec($binary_code_102bit);
		$codewords[0] = bcmod($data, 636) * 2;
		$data = bcdiv($data, 636);
		for ($i = 1; $i < 9; ++$i) {
			$codewords[$i] = bcmod($data, 1365);
			$data = bcdiv($data, 1365);
		}
		$codewords[9] = $data;
		if (($fcs >> 10) == 1) {
			$codewords[9] += 659;
		}
		// generate lookup tables
		$table2of13 = $this->imb_tables(2, 78);
		$table5of13 = $this->imb_tables(5, 1287);
		// convert codewords to characters
		$characters = array();
		$bitmask = 512;
		foreach($codewords as $k => $val) {
			if ($val <= 1286) {
				$chrcode = $table5of13[$val];
			} else {
				$chrcode = $table2of13[($val - 1287)];
			}
			if (($fcs & $bitmask) > 0) {
				// bitwise invert
				$chrcode = ((~$chrcode) & 8191);
			}
			$characters[] = $chrcode;
			$bitmask /= 2;
		}
		$characters = array_reverse($characters);
		// build bars
		$k = 0;
		$bararray = array('code' => $code, 'maxw' => 0, 'maxh' => 3, 'bcode' => array());
		for ($i = 0; $i < 65; ++$i) {
			$asc = (($characters[$asc_chr[$i]] & pow(2, $asc_pos[$i])) > 0);
			$dsc = (($characters[$dsc_chr[$i]] & pow(2, $dsc_pos[$i])) > 0);
			if ($asc AND $dsc) {
				// full bar (F)
				$p = 0;
				$h = 3;
			} elseif ($asc) {
				// ascender (A)
				$p = 0;
				$h = 2;
			} elseif ($dsc) {
				// descender (D)
				$p = 1;
				$h = 2;
			} else {
				// tracker (T)
				$p = 1;
				$h = 1;
			}
			$bararray['bcode'][$k++] = array('t' => 1, 'w' => 1, 'h' => $h, 'p' => $p);
			$bararray['bcode'][$k++] = array('t' => 0, 'w' => 1, 'h' => 2, 'p' => 0);
			$bararray['maxw'] += 2;
		}
		unset($bararray['bcode'][($k - 1)]);
		--$bararray['maxw'];
		return $bararray;
	}
	
	/**
	 * Convert large integer number to hexadecimal representation.
	 * (requires PHP bcmath extension) 
	 * @param string $number number to convert specified as a string
	 * @return string hexadecimal representation
	 */
	public function dec_to_hex($number) {
		$i = 0;
		$hex = array();
		if($number == 0) {
			return '00';
		}
		while($number > 0) {
			if($number == 0) {
				array_push($hex, '0');
			} else {
				array_push($hex, strtoupper(dechex(bcmod($number, '16'))));
				$number = bcdiv($number, '16', 0);
			}
		}
		$hex = array_reverse($hex);
		return implode($hex);
	}
	
	/**
	 * Convert large hexadecimal number to decimal representation (string).
	 * (requires PHP bcmath extension) 
	 * @param string $hex hexadecimal number to convert specified as a string
	 * @return string hexadecimal representation
	 */
	public function hex_to_dec($hex) {
		$dec = 0;
		$bitval = 1;
		$len = strlen($hex);
		for($pos = ($len - 1); $pos >= 0; --$pos) {
			$dec = bcadd($dec, bcmul(hexdec($hex{$pos}), $bitval));
			$bitval = bcmul($bitval, 16);
		}
		return $dec;
	}	
	
	/**
	 * Intelligent Mail Barcode calculation of Frame Check Sequence
	 * @param string $code_arr array of hexadecimal values (13 bytes holding 102 bits right justified).
	 * @return int 11 bit Frame Check Sequence as integer (decimal base)
	 * @access protected
	 */
	protected function imb_crc11fcs($code_arr) {
		$genpoly = 0x0F35; // generator polynomial
		$fcs = 0x07FF; // Frame Check Sequence
		// do most significant byte skipping the 2 most significant bits
		$data = hexdec($code_arr[0]) << 5;
		for ($bit = 2; $bit < 8; ++$bit) {
			if (($fcs ^ $data) & 0x400) {
				$fcs = ($fcs << 1) ^ $genpoly;
			} else {
				$fcs = ($fcs << 1);
			}
			$fcs &= 0x7FF;
			$data <<= 1;
		}
		// do rest of bytes
		for ($byte = 1; $byte < 13; ++$byte) {
			$data = hexdec($code_arr[$byte]) << 3;
			for ($bit = 0; $bit < 8; ++$bit) {
				if (($fcs ^ $data) & 0x400) {
					$fcs = ($fcs << 1) ^ $genpoly;
				} else {
					$fcs = ($fcs << 1);
				}
				$fcs &= 0x7FF;
				$data <<= 1;
			}
		}
		return $fcs;		
	}
	
	/**
	 * Reverse unsigned short value
	 * @param int $num value to reversr
	 * @return int reversed value
	 * @access protected
	 */
	protected function imb_reverse_us($num) {
		$rev = 0;
		for ($i = 0; $i < 16; ++$i) {
			$rev <<= 1;
			$rev |= ($num & 1);
			$num >>= 1;
		}
		return $rev;
	}
	
	/**
	 * generate Nof13 tables used for Intelligent Mail Barcode
	 * @param int $n is the type of table: 2 for 2of13 table, 5 for 5of13table
	 * @param int $size size of table (78 for n=2 and 1287 for n=5)
	 * @return array requested table
	 * @access protected
	 */
	protected function imb_tables($n, $size) {
		$table = array();
		$lli = 0; // LUT lower index
		$lui = $size - 1; // LUT upper index
		for ($count = 0; $count < 8192; ++$count) {
			$bit_count = 0;
			for ($bit_index = 0; $bit_index < 13; ++$bit_index) {
				$bit_count += intval(($count & (1 << $bit_index)) != 0);
			}
			// if we don't have the right number of bits on, go on to the next value
			if ($bit_count == $n) {
				$reverse = ($this->imb_reverse_us($count) >> 3);
				// if the reverse is less than count, we have already visited this pair before
				if ($reverse >= $count) {
					// If count is symmetric, place it at the first free slot from the end of the list.
					// Otherwise, place it at the first free slot from the beginning of the list AND place $reverse ath the next free slot from the beginning of the list
					if ($reverse == $count) {
						$table[$lui] = $count;
						--$lui;
					} else {
						$table[$lli] = $count;
						++$lli;
						$table[$lli] = $reverse;
						++$lli;
					}
				}
			}
		}
		return $table;
	}
	
} // end of class

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
