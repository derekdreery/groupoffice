<?php
class Cal_Event_Replacements implements Swift_Plugins_Decorator_Replacements {
	function getReplacementsFor($address) {
		return array('%email%'=>$address);
	}
}