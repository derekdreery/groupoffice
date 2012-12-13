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

		return $isInternal && GO_Base_Util_Validate::ip($ip);
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
	
	/**
	 * TODO: CREATE THE HOSTNAME FUNCTION
	 * @return boolean 
	 */
	public static function hostname(){
		return true;
	}
	
	/**
	 * Check for the given country if it is an EU country or not.
	 * 
	 * @param string $country eg. "NL" or "BE"
	 * @return boolean  
	 */
	public static function isEUCountry($country){
		return in_array(strtoupper($country), array(
			'AT',
			'BE',
			'BG',
			'CY',
			'CZ',
			'DK',
			'EE',
			'FI',
			'FR',
			//'FX', ??
			'DE',
			'GR',
			'HU',
			'IE',
			'IT',
			'LV',
			'LT',
			'LU',
			'MT',
			'NL',
			'PL',
			'PT',
			'RO',
			'SK',
			'SI',
			'ES',
			'SE',
			'GB'
		));
	}
	
	/**
	 * Check if a customer needs to pay VAT.
	 * 
	 * @param string $customerCountry eg. NL Country 
	 * @param boolean $hasVatNo Customer has a valid vat number
	 * @param string $merchantCountry eg. NL This is the country the merchant lives in. If the customer comes from the same country he should always pay VAT.
	 */
	public static function vatApplicable($customerCountry, $hasVatNo, $merchantCountry){
		return strtolower($customerCountry)==strtolower($merchantCountry) || 
						(GO_Base_Util_Validate::isEUCountry($customerCountry) && !$hasVatNo);
	}
	
	/**
	 * Check if a vat number is correct.
	 * 
	 * @param string $countryCode The country code: eg. "NL" or "BE"
	 * @param string $vat The vat number
	 * @return boolean true
	 */
	public static function checkVat($countryCode, $vat) {
		
		//remove unwanted characters
		$vat = preg_replace('/[^a-z0-9]/i','',$vat);
		
		//strip country if included
		if(substr($vat,0,2)==$countryCode)
			$vat = trim(substr($vat,2));
		
		//$wsdl = 'http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl';
		$wsdl = GO::config()->root_path.'go/vendor/wsdl/checkVatService.wsdl';

		$vies = new SoapClient($wsdl);

		/**
			var_dump($vies->__getFunctions());
			var_dump($vies->__getTypes());
		*/
		
		$message = new stdClass();
		$message->countryCode = $countryCode;
		$message->vatNumber = $vat;

		try {
			$ret = $vies->checkVat($message);
		} catch (SoapFault $e) {
			$ret = $e->faultstring;
			$regex = '/\{ \'([A-Z_]*)\' \}/';
			$n = preg_match($regex, $ret, $matches);
			if(isset($matches[1])){
				$ret = $matches[1];
				$faults = array
						(
						'INVALID_INPUT' => 'The provided CountryCode is invalid or the VAT number is empty',
						'SERVICE_UNAVAILABLE' => 'The SOAP service is unavailable, try again later',
						'MS_UNAVAILABLE' => 'The VAT Member State service is unavailable, try again later or with another Member State',
						'TIMEOUT' => 'The Member State service could not be reached in time, try again later or with another Member State',
						'SERVER_BUSY' => 'The service cannot process your request. Try again later.'
				);
				$msg=$faults[$ret];
			}else
			{
				$msg=$ret;
			}
			
			if($ret!="INVALID_INPUT")
				throw new GO_Base_Exception_ViesDown();
			
			throw new Exception("Could not check VAT number: ".$msg);
		}

		return $ret->valid;
	}
	
}