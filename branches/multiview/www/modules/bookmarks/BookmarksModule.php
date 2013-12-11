<?php
class GO_Bookmarks_BookmarksModule extends \GO\Base\Module{
	public function autoInstall() {
		return true;
	}
	
	public static function initListeners() {
		$c = new \GO_Core_Controller_Auth();
		$c->addListener('head', "GO_Bookmarks_BookmarksModule", "head");
	}
	
	public static function head(){
		echo '<style>';

		$findParams = \GO\Base\Db\FindParams::newInstance()->criteria(\GO\Base\Db\FindCriteria::newInstance()->addCondition('behave_as_module', 1));

		$stmt = \GO_Bookmarks_Model_Bookmark::model()->find($findParams);
		while ($bookmark = $stmt->fetch()) {			
			echo '.go-menu-icon-bookmarks-id-'.$bookmark->id.'{background-image:url('.$bookmark->thumbUrl.')}';			
		}

		echo '</style>';
	}
	
	public function install() {
		parent::install();
		
		$category = new \GO_Bookmarks_Model_Category();
		$category->name=\GO::t('general','bookmarks');		
		$category->save();
		$category->acl->addGroup(\GO::config()->group_internal, \GO\Base\Model\Acl::READ_PERMISSION);
	}
}
