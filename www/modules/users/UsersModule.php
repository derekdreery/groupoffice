<?php
class GO_Users_UsersModule extends GO_Base_Module{	
	public function author() {
		return 'Merijn Schering';
	}
	
	public function authorEmail() {
		return 'mschering@intermesh.nl';
	}
	public function autoInstall() {
		return true;
	}
	
	public function adminModule() {
		return true;
	}
}