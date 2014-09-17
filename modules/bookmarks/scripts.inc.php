<?php
$findParams = \GO\Base\Db\FindParams::newInstance()->criteria(\GO\Base\Db\FindCriteria::newInstance()->addCondition('behave_as_module', 1));

$stmt = \GO\Bookmarks\Model\Bookmark::model()->find($findParams);

while($bookmark = $stmt->fetch()){
	if (strlen($bookmark->name) > 30) {
		$name = substr($bookmark->name, 0, 28) . '..';
	} else {
		$name = $bookmark->name;
	}
	$GO_SCRIPTS_JS .= 'GO.moduleManager.addModule(\'bookmarks-id-' . $bookmark->id . '\', GO.panel.IFrameComponent, {title : \'' . \GO\Base\Util\String::escape_javascript($name) . '\', url : \'' . \GO\Base\Util\String::escape_javascript($bookmark->content) . '\',iconCls: \'go-tab-icon-bookmarks\'});';
}
