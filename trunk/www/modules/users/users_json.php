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
$GO_MODULES->authenticate('users');



if(isset($_REQUEST['user_id']) && $_REQUEST['user_id']>0)
{
	$user = $GO_USERS->get_user(smart_addslashes($_REQUEST['user_id']));
	
	echo '({"user":['.json_encode($user).']})';
}else {
	
	$query = isset($_REQUEST['query']) ? '%'.smart_addslashes($_REQUEST['query']).'%' : '';
	$search_field = isset($_REQUEST['search_field']) ? smart_addslashes($_REQUEST['search_field']) : '';


	$count = $GO_USERS->search($query, $search_field, 0, $_REQUEST['start'], $_REQUEST['limit'], $_REQUEST['sort'],$_REQUEST['dir']);
	
	$records=array();
	while($GO_USERS->next_record())
	{
		$name = format_name($GO_USERS->f('last_name'),$GO_USERS->f('first_name'),$GO_USERS->f('middle_name'));
		
		$records[]=array(
			'id'=>$GO_USERS->f('id'),
			'link_id'=>$GO_USERS->f('link_id'), 
			'link_type'=> 8, 
			'name'=>htmlspecialchars($name), 
			'username'=>htmlspecialchars($GO_USERS->f('username')), 
			'email'=>htmlspecialchars($GO_USERS->f('email')), 
			'mtime'=>get_timestamp($GO_USERS->f('mtime')));
	}
	
	echo '({"total":"'.$count.'","results":'.json_encode($records).'})'; 
}