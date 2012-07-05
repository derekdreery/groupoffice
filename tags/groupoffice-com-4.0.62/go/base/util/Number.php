<?php
/*
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 */

/**
 * This class contains functions that perform operations on numbers. It 
 * formats numbers according to the user preferences.
 *  
 * @copyright Copyright Intermesh
 * @version $Id$
 * @author Merijn Schering <mschering@intermesh.nl>
 * @package GO.base.util
 * @since Group-Office 3.0
 */

class GO_Base_Util_Number {

	/**
	 * Format a number by using the user preferences
	 *
	 * @param	int $number The number
	 * @param	int $decimals Number of decimals to display
	 * @access public
	 * @return string
	 */

	public static function localize($number, $decimals=2)
	{		
		$ts = GO::user() ? GO::user()->thousands_separator : GO::config()->default_thousands_separator;
		$ds = GO::user() ? GO::user()->decimal_separator : GO::config()->default_decimal_separator;
		return number_format(floatval($number), $decimals, $ds, $ts);
	}

	/**
	 * Conver a number formatted by using the user preferences to a number understood by PHP
	 *
	 * @param	int $number The number
	 * @param	int $decimals Number of decimals to display
	 * @access public
	 * @return string
	 */

	public static function unlocalize($number)
	{	
		$ts = GO::user() ? GO::user()->thousands_separator : GO::config()->default_thousands_separator;
		$ds = GO::user() ? GO::user()->decimal_separator : GO::config()->default_decimal_separator;
		$number = str_replace($ts,'', $number);
		$number = str_replace($ds,'.',$number);
		
		if(!empty($number) && !is_numeric($number))
			return false;
		
		return floatval($number);
		//return str_replace($ds,'.',$number);
	}

	/**
	 * Format a size to a human readable format.
	 *
	 * @deprecated
	 * @param	int $size The size in bytes
	 * @param	int $decimals Number of decimals to display
	 * @access public
	 * @return string
	 */

	public static function formatSize($size, $decimals = 1) {
		
		if($size==0)
			return 0;
		
		switch ($size) {
			case ($size > 1073741824) :
				$size = self::localize($size / 1073741824, $decimals);
				$size .= " G";
				break;

			case ($size > 1048576) :
				$size = self::localize($size / 1048576, $decimals);
				$size .= " M";
				break;

			case ($size > 1024) :
				$size = self::localize($size / 1024, $decimals);
				$size .= " K";
				break;

			default :
				$size = self::localize($size, $decimals);
				$size .= " bytes";
				break;
		}
		return $size;
	}
}
?>