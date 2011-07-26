<?php
require($GLOBALS['GO_LANGUAGE']->get_language_file('notes'));

if(isset($GLOBALS['GO_MODULES']->modules['customfields']))
{
	require_once($GLOBALS['GO_MODULES']->modules['customfields']['class_path'].'customfields.class.inc.php');
	$cf = new customfields();
	$GO_SCRIPTS_JS .= $cf->get_javascript(4, $lang['notes']['notes']);
}

//require_once($GLOBALS['GO_MODULES']->modules['notes']['class_path'].'notes.class.inc.php');
//$notes = new notes();
//
//$category = $notes->get_category();

require_once('GO.php');

$category = GO_Notes_Model_Category::model()->findSingleByAttribute('user_id', $GLOBALS['GO_SECURITY']->user_id);
if($category){
	$GO_SCRIPTS_JS .= 'GO.notes.defaultCategory = {id: '.$category->id.', name: "'.$category->name.'"};';

	$GLOBALS['GO_CONFIG']->save_setting('notes_categories_filter',$category->id, $GLOBALS['GO_SECURITY']->user_id);
}