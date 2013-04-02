<?php

class GO_Site_Controller_Error extends GO_Site_Controller_Abstract {
	protected function action404($params){
		$this->setPageTitle("404 Not found");
		
		$this->render('404');
	}
}