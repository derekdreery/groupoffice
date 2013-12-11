<?php

class GO_Site_SiteModule extends \GO\Base\Module{
	
	public function autoInstall() {
		return false;
	}
	
	public function author() {
		return 'Wesley Smits';
	}
	
	public function depends() {
		return array('files');
	}

	public function authorEmail() {
		return 'wsmits@intermesh.nl';
	}
}