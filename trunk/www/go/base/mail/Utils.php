<?php

class GO_Base_Mail_Utils{
	public static function mimeHeaderDecode($string, $defaultCharset='UTF-8') {

		/*
		 * (=?ISO-8859-1?Q?a?= =?ISO-8859-1?Q?b?=)     (ab)
		 *  White space between adjacent 'encoded-word's is not displayed.
		 *
		 *  http://www.faqs.org/rfcs/rfc2047.html
		 */
		$string = preg_replace("/\?=[\s]*=\?/","?==?", $string);

		if (preg_match_all("/(=\?[^\?]+\?(q|b)\?[^\?]+\?=)/i", $string, $matches)) {
			foreach ($matches[1] as $v) {
				$fld = substr($v, 2, -2);
				$charset = strtolower(substr($fld, 0, strpos($fld, '?')));
				$fld = substr($fld, (strlen($charset) + 1));
				$encoding = $fld{0};
				$fld = substr($fld, (strpos($fld, '?') + 1));
				$fld = str_replace('_', '=20', $fld);
				if (strtoupper($encoding) == 'B') {
					$fld = base64_decode($fld);
				}
				elseif (strtoupper($encoding) == 'Q') {
					$fld = quoted_printable_decode($fld);
				}
				$fld = GO_Base_Util_String::to_utf8($fld, $charset);

				$string = str_replace($v, $fld, $string);
			}
		}else
		{			
			$string=GO_Base_Util_String::to_utf8($string, $defaultCharset);
		}
		$string=GO_Base_Util_String::clean_utf8($string);
		return str_replace(array('\\\\', '\\(', '\\)'), array('\\','(', ')'), $string);
	}
}
