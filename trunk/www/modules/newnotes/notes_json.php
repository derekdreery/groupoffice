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
$GO_MODULES->authenticate('newnotes');

//load contact management class
require_once($GO_MODULES->class_path."notes.class.inc");
$notes = new notes();

$count = $notes->get_notes($GO_SECURITY->user_id,false,'name','ASC', $_REQUEST['start'], $_REQUEST['limit']);

$records=array();
while($notes->next_record())
{
	$records[]=array('name'=>$notes->f('name'), 'mtime'=>$notes->f('mtime'));
}

echo '({"total":"'.$count.'","results":'.json_encode($records).'})'; 