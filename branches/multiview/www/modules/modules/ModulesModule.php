<?php

class GO_Modules_ModulesModule extends \GO\Base\Module{
	
	public function autoInstall() {
		return true;
	}
	public function adminModule() {
		return true;
	}
}