<?php
/**
* @copyright Intermesh 2007
* @author Merijn Schering <mschering@intermesh.nl>
* @version $Revision: 1.13 $ $Date: 2006/10/20 12:36:43 $3
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */
 

require_once("Group-Office.php");
$GO_SECURITY->authenticate();



$result =array();
switch($_REQUEST['task'])
{
    case 'link':
        var_dump($_POST);
        
        $fromLinks = json_decode(smart_stripslashes($_POST['fromLinks']));
        $toLinks = json_decode(smart_stripslashes($_POST['toLinks']));
        
        var_dump($fromLinks);
        var_dump($toLinks);
        
        foreach($fromLinks as $fromLink)
        {
        	foreach($toLinks as $toLink)
	        {
	        	$GO_LINKS->add_link($fromLink->link_id, $fromLink->link_type, $toLink->link_id, $toLink->link_type);
	        }
        }

        $result['success']=true;
        $result['errors']='Items linked successfully';
        
        break;
}

echo json_encode($result);