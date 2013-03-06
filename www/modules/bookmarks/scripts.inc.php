<?php
$findParams = GO_Base_Db_FindParams::newInstance()->criteria(GO_Base_Db_FindCriteria::newInstance()->addCondition('behave_as_module', 1));

$stmt = GO_Bookmarks_Model_Bookmark::model()->find($findParams);

while($bookmark = $stmt->fetch()){
	if (strlen($bookmark->name) > 30) {
		$name = substr($bookmark->name, 0, 28) . '..';
	} else {
		$name = $bookmark->name;
	}
	$GO_SCRIPTS_JS .= 'GO.moduleManager.addModule(\'bookmarks-id-' . $bookmark->id . '\', GO.panel.IFrameComponent, {title : \'' . GO_Base_Util_String::escape_javascript($name) . '\', url : \'' . GO_Base_Util_String::escape_javascript($bookmark->content) . '\'});';
}