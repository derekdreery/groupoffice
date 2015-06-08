<?php
$findParams = GO_Base_Db_FindParams::newInstance()->criteria(GO_Base_Db_FindCriteria::newInstance()->addCondition('behave_as_module', 1));

$stmt = GO_Bookmarks_Model_Bookmark::model()->find($findParams);

while($bookmark = $stmt->fetch()){
	if (strlen($bookmark->name) > 30) {
		$name = substr($bookmark->name, 0, 28) . '..';
	} else {
		$name = $bookmark->name;
	}
	$GO_SCRIPTS_JS .= 'GO.moduleManager.addModule(\'bookmarks-id-' . $bookmark->id . '\', GO.panel.IFrameComponent, {title : \'' . GO_Base_Util_String::escape_javascript($name) . '\', url : \'' . GO_Base_Util_String::escape_javascript($bookmark->content) . '\',iconCls: \'go-tab-icon-bookmarks\'});';
}

// Load the bookmark categories for the start menu
$categoryFindParams = GO_Base_Db_FindParams::newInstance()->criteria(GO_Base_Db_FindCriteria::newInstance()->addCondition('show_in_startmenu', 1));
$categoryStmt = GO_Bookmarks_Model_Category::model()->find($categoryFindParams);

while($category = $categoryStmt->fetch()){
	
	if (strlen($category->name) > 30) {
		$categoryName = substr($category->name, 0, 28) . '..';
	} else {
		$categoryName = $category->name;
	}
	
	$bookmarks = $category->bookmarks;
	
	while($bookmark = $bookmarks->fetch()){
		
		if (strlen($bookmark->name) > 30) {
			$name = substr($bookmark->name, 0, 28) . '..';
		} else {
			$name = $bookmark->name;
		}
		
		$GO_SCRIPTS_JS .= 'GO.moduleManager.addModule(\'bookmarks-id-' . $bookmark->id . '\', GO.panel.IFrameComponent, {title : \'' . GO_Base_Util_String::escape_javascript($name) . '\', url : \'' . GO_Base_Util_String::escape_javascript($bookmark->content) . '\',iconCls: \'go-tab-icon-bookmarks\'},{title:\''.GO_Base_Util_String::escape_javascript($categoryName).'\',iconCls: \'go-menu-icon-bookmarks\'});';
	}

}