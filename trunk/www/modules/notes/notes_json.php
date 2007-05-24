<?php
/*
   Copyright Intermesh 2003
   Author: Merijn Schering <mschering@intermesh.nl>
   Version: 1.0 Release date: 08 July 2003

   This program is free software; you can redistribute it and/or modify it
   under the terms of the GNU General Public License as published by the
   Free Software Foundation; either version 2 of the License, or (at your
   option) any later version.
 */

require_once("../../Group-Office.php");
$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('notes');

//load contact management class
require_once($GO_MODULES->class_path."notes.class.inc");
$notes = new notes();

if(isset($_REQUEST['note_id']) && $_REQUEST['note_id']>0)
{
	$note = $notes->get_note(smart_addslashes($_REQUEST['note_id']));
	
	echo '({"note":['.json_encode($note).']})';
}else {

	$count = $notes->get_notes($GO_SECURITY->user_id,false,$_REQUEST['sort'],$_REQUEST['dir'], $_REQUEST['start'], $_REQUEST['limit']);
	
	$records=array();
	while($notes->next_record())
	{
		$records[]=array('id'=>$notes->f('id'),'link_id'=>$notes->f('link_id'), 'link_type'=> 4, 'name'=>$notes->f('name'), 'mtime'=>get_timestamp($notes->f('mtime')));
	}
	
	echo '({"total":"'.$count.'","results":'.json_encode($records).'})'; 
}