<?php

$category = GO_Notes_NotesModule::getDefaultNoteCategory(GO::user()->id);


if($category){
	$GO_SCRIPTS_JS .= 'GO.notes.defaultCategory = {id: '.$category->id.', name: "'.$category->name.'"};';

	$GLOBALS['GO_CONFIG']->save_setting('notes_categories_filter',$category->id, GO::session()->values['user_id']);
}