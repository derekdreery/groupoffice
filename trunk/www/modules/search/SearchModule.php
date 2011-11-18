<?php
class GO_Search_SearchModule extends GO_Base_Module{
	
	public function autoInstall() {
		return true;
	}
	
	public function install() {		
		GO::modules()->search->acl->addGroup(GO::config()->group_everyone);
	}
}