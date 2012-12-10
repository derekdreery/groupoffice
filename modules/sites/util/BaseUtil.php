<?php

class GO_Sites_Util_BaseUtil {
	
	public $site_id;
	
	public function __construct($site_id){
		$this->site_id = $site_id;
	}
	
	public function getWebshop(){
		return GO_Webshop_Model_Webshop::model()->findSingleByAttribute('site_id', $this->site_id);
	}
}