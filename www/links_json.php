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

require_once("Group-Office.php");
$GO_SECURITY->authenticate();

require_once($GO_CONFIG->class_path.'/base/search.class.inc');
$search = new search();

$links = $GO_LINKS->get_links($_REQUEST['link_id']);

$count = $search->global_search($GO_SECURITY->user_id, '', $_REQUEST['sort'],$_REQUEST['dir'], $_REQUEST['start'], $_REQUEST['limit'],$links);

$records=array();
while($search->next_record())
{
	$records[]=array(
		'link_id'=>$search->f('id'),
		'name'=>$search->f('name'), 
		'description'=>$search->f('description'), 
		'url'=>$search->f('url'),
		'mtime'=>get_timestamp($notes->f('mtime'))
		);
}

echo '({"total":"'.$count.'","results":'.json_encode($records).'})'; 
