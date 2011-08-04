<?php
class GO_Addressbook_Utils{
	
	public static function getIndexChar($string) {
		$char = '';
		if (!empty($string)) {
			if (function_exists('mb_substr')) {
				$char = strtoupper(mb_substr(GO_Base_Util_File::strip_invalid_chars($string),0,1,'UTF-8'));
			} else {
				$char = strtoupper(substr(GO_Base_Util_File::strip_invalid_chars($string),0,1));
			}
		}

		return $char;
	}
	
}