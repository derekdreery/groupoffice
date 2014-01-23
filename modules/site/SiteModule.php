<?php

class GO_Site_SiteModule extends GO_Base_Module{
	
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
	
	/**
	 * Extract a treenode ID to an array
	 * 
	 * @param string $nodeId Examples "1_content_2" or "1_menu_4" or "1_content"
	 * @return mixed array/false
	 */
	public static function extractTreeNode($nodeId){
		$siteId = false;
		$type = false;
		$modelId = false;
		
		$parts = explode('_',$nodeId);
		
		if(is_array($parts)){
			
			if($parts[0] === 'root'){
				return array('siteId'=>false,'type'=>$parts[0],'modelId'=>false);
			} else {
			
				$siteId = $parts[0];
				$type = $parts[1];

				if(isset($parts[2]))
					$modelId = $parts[2];

				return array('siteId'=>$siteId,'type'=>$type,'modelId'=>$modelId);
			}
		}
		
		return false;
	}
	
	public static function getModelNameFromTreeNodeType($nodeType){
		
		switch($nodeType){
			
			case 'content':
				return 'GO_Site_Model_Content';
				break;
			case 'site':
				return 'GO_Site_Model_Site';
				break;
			case 'menu':
				return 'GO_Site_Model_Menu';
				break;
			case 'menuitem':
				return 'GO_Site_Model_MenuItem';
				break;
		}
		
		return false;
		
		
	} 
	
	
}