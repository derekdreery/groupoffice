<?php
require(GO::language()->get_language_file('notes'));

if(isset(GO::modules()->modules['customfields']))
{
	require_once(GO::modules()->modules['customfields']['class_path'].'customfields.class.inc.php');
	$cf = new customfields();
	$GO_SCRIPTS_JS .= $cf->get_javascript(4, $lang['notes']['notes']);
}

//require_once(GO::modules()->modules['notes']['class_path'].'notes.class.inc.php');
//$notes = new notes();
//
//$category = $notes->get_category();
$category = GO_Notes_Model_Category::model()->findSingleByAttribute('user_id', GO::security()->user_id);
if($category){
	$GO_SCRIPTS_JS .= 'GO.notes.defaultCategory = {id: '.$category->id.', name: "'.$category->name.'"};';

	GO::config()->save_setting('notes_categories_filter',$category->id, GO::security()->user_id);
}