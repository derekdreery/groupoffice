<?php

class GO_Modules_ModulesModule extends GO_Base_Module{
	
	public function autoInstall() {
		return true;
	}
	public function adminModule() {
		return true;
	}
}