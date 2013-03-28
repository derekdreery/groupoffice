<?php

class GO_Site_SiteModule extends GO_Base_Module{
	
	public function autoInstall() {
		return false;
	}
	
	public function author() {
		return 'Wesley Smits';
	}
	
	public function authorEmail() {
		return 'wsmits@intermesh.nl';
	}
}