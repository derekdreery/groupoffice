<?php
/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @version $Id$
 * @author Merijn Schering <mschering@intermesh.nl>
 */

/**
 * This class contains functions that perform operations on numbers. It 
 * formats numbers according to the user preferences.
 *  
 * @copyright Copyright Intermesh
 * @version $Id$
 * @author Merijn Schering <mschering@intermesh.nl>
 * @package go.utils
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
		return number_format(floatval($number), $decimals, GO::user()->decimal_separator, GO::user()->thousands_separator);
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
		$number = str_replace(GO::user()->thousands_separator,'', $number);
		return floatval(str_replace(GO::user()->decimal_separator,'.',$number));
	}

	/**
	 * Format a size to a human readable format.
	 *
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
				$size .= " GB";
				break;

			case ($size > 1048576) :
				$size = self::localize($size / 1048576, $decimals);
				$size .= " MB";
				break;

			case ($size > 1024) :
				$size = self::localize($size / 1024, $decimals);
				$size .= " KB";
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