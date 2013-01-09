<?php

$basedir = dirname(dirname(__FILE__));

require_once($basedir.'/vendor/linkedin/linkedin_3.2.0.class.php');

class GO_Linkedin_Util_Linkedin extends LinkedIn {
	
	public function retrieveTokenRequestForNetwork() {
    $parameters = array(
      'oauth_callback' => $this->getCallbackUrl()
    );
    $response = $this->fetch(self::_METHOD_TOKENS, self::_URL_REQUEST.'?scope=r_network', NULL, $parameters);
    parse_str($response['linkedin'], $response['linkedin']);
    
    /**
	   * Check for successful request (a 200 response from LinkedIn server) 
	   * per the documentation linked in method comments above.
	   */
    if(($response['info']['http_code'] == 200) && (array_key_exists('oauth_callback_confirmed', $response['linkedin'])) && ($response['linkedin']['oauth_callback_confirmed'] == 'true')) {
      // tokens retrieved
      $this->setToken($response['linkedin']);
      
      // set the response
      $return_data            = $response;
      $return_data['success'] = TRUE;        
    } else {
      // error getting the request tokens
      $this->setToken(NULL);
      
      // set the response
      $return_data = $response;
      if((array_key_exists('oauth_callback_confirmed', $response['linkedin'])) && ($response['linkedin']['oauth_callback_confirmed'] == 'true')) {
        $return_data['error'] = 'HTTP response from LinkedIn end-point was not code 200';
      } else {
        $return_data['error'] = 'OAuth callback URL was not confirmed by the LinkedIn end-point';
      }
      $return_data['success'] = FALSE;
    }
    return $return_data;
	}
	
}

?>