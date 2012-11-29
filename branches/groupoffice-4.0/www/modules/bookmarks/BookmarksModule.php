<?php
class GO_Bookmarks_BookmarksModule extends GO_Base_Module{
	public function autoInstall() {
		return true;
	}
	
	public static function initListeners() {
		$c = new GO_Core_Controller_Auth();
		$c->addListener('head', "GO_Bookmarks_BookmarksModule", "head");
	}
	
	public function head(){
		echo '<style>';

		$findParams = GO_Base_Db_FindParams::newInstance()->criteria(GO_Base_Db_FindCriteria::newInstance()->addCondition('behave_as_module', 1));

		$stmt = GO_Bookmarks_Model_Bookmark::model()->find($findParams);
		while ($bookmark = $stmt->fetch()) {			
			echo '.go-menu-icon-bookmarks-id-'.$bookmark->id.'{background-image:url('.$bookmark->thumbUrl.')}';			
		}

		echo '</style>';
	}
}
