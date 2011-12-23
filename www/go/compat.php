<?php
if(!function_exists("quoted_printable_encode")){
	function quoted_printable_encode($string) {
		return preg_replace('/[^\r\n]{73}[^=\r\n]{2}/', "$0=\n", str_replace(array('%20', '%0D%0A', '%'), array(' ', "\r\n", '='), rawurlencode($string)));
	}
}