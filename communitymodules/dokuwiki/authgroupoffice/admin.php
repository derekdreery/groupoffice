<?php
class admin_plugin_authgroupoffice extends admin_plugin_acl {
	
	function getMenuText($language) {
		return 'Extended ACL Management';
	}
	
	 /**
     * Get current ACL settings as multidim array
     *
     * @author WilmarVB <info@intermesh.nl>
     */
	function handle(){
		parent::handle();
		$this->usersgroups = $this->_loadGOGroups();
	}

	private function _loadGOGroups() {
		$groups = $this->usersgroups;
		$groupNames= array();
		$allGroupNames = array();
		foreach($groups as $line){
			$line = trim(preg_replace('/#.*$/','',$line)); //ignore comments
			if(!$line) continue;

			$acl = preg_split('/[ \t]+/',$line);
			//0 is pagename, 1 is user, 2 is acl

			$acl[1] = rawurldecode($acl[1]);
			$acl_config[$acl[0]][$acl[1]] = $acl[2];

			// store non-special users and groups for later selection dialog
			$ug = $acl[1];
			$allGroupNames[] = $ug;
			if (substr($ug,0,1)=='@') $ug = substr($ug,1);
			$groupNames[] = $ug;
		}
		
		$goGroupsStmt = GO_Base_Model_Group::model()->find();
		
		foreach ($goGroupsStmt as $goGroupModel) {
			if (!in_array($goGroupModel->name,$groupNames)) {
				$groupNames[] = $goGroupModel->name;
				$allGroupNames[] = '@'.$goGroupModel->name;
			}
		}
		
		return $allGroupNames;
	}
		
}
?>
