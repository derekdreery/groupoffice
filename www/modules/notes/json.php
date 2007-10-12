<?php
/**
 * @copyright Copyright Intermesh 2007
 * @author Merijn Schering <mschering@intermesh.nl>
 * 
 * This file is part of Group-Office.
 * 
 * Group-Office is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 * 
 * See file /LICENSE.GPL
 */
require_once("../../Group-Office.php");

$GO_SECURITY->json_authenticate('notes');


//load contact management class
require_once($GO_MODULES->modules['notes']['class_path']."notes.class.inc");
$notes = new notes();

if(isset($_REQUEST['note_id']) && $_REQUEST['note_id']>0)
{
	$note = $notes->get_note(smart_addslashes($_REQUEST['note_id']));
	
	echo '({"note":['.json_encode($note).']})';
}else {

	$sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'mtime';
	$dir = isset($_REQUEST['dir']) ? $_REQUEST['dir'] : 'DESC';
	$start = isset($_REQUEST['start']) ? $_REQUEST['start'] : '0';
	$limit = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : '0';
	
	$count = $notes->get_notes($GO_SECURITY->user_id,false,$sort,$dir, $start, $limit);
	
	$records=array();
	while($notes->next_record())
	{
		$records[]=array('id'=>$notes->f('id'),'link_id'=>$notes->f('link_id'), 'link_type'=> 4, 'name'=>$notes->f('name'), 'mtime'=>get_timestamp($notes->f('mtime')));
	}
	
	
	$response['total']=$count;
	$response['results']=$records;
	
	echo json_encode($response);
	//echo '({"success":false," total":"'.$count.'","results":'.json_encode($records).'})'; 
}