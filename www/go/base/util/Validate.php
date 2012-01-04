<?php
class GO_Base_Util_Validate {
	
/**
	* Checks the given Ip is valid (ipv4 and ipv6).
	* * Needs PHP 5.2 or higher
	* 
	* @param string $ip
	* @return boolean $isValid
	*/
	public static function ip($ip){
		$isValid = false;
		 
		if(filter_var($ip, FILTER_VALIDATE_IP)) {
			$isValid = true;
		}

		return $isValid;
	}
	
	
/**
	* Checks the given Ip if it is an internal one or not (ipv4 and ipv6).
	* * Needs PHP 5.2 or higher
	* 
	* @param string $ip
	* @return boolean $isInternal
	*/
	public static function internalIp($ip){
		$isInternal = false;

		if(!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
				$isInternal = true;
		}

		return $isInternal;
	}
	
/**
	* Checks the given Ip is an ipV6 address.
	* * Needs PHP 5.2 or higher
	* 
	* @param string $ip
	* @return boolean $isIpV6
	*/
	public static function ipV6($ip){
		$isIpV6 = false;
		 
		if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
			$isIpV6 = true;
		}

		return $isIpV6;
	}
	
/**
	* Checks the given Ip is an ipV4 address.
	* * Needs PHP 5.2 or higher
	* 
	* @param string $ip
	* @return boolean $isIpV4
	*/
	public static function ipV4($ip){
		$isIpV4 = false;
		 
		if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
			$isIpV4 = true;
		}

		return $isIpV4;
	}	
	
	public static function hostname(){
		return true;
	}
	
}