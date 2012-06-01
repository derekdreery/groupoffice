<?php
/**
 * This module is intended for developers as an example for basic CRUD functions 
 */
class GO_Presidents_PresidentsModule extends GO_Base_Module{
	
	public function autoInstall() {
		return false;
	}
	
	public function author() {
		return 'Merijn Schering';
	}
	
	public function authorEmail() {
		return 'mschering@intermesh.nl';
	}
}

?>
