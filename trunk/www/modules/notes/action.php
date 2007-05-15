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
 

require_once("../../Group-Office.php");
$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('notes');

//load contact management class
require_once($GO_MODULES->class_path."notes.class.inc");
$notes = new notes();


$result =array();
switch($_REQUEST['task'])
{
    case 'delete':
        
        $selectedRows = json_decode(smart_stripslashes($_POST['selectedRows']));
        foreach($selectedRows as $note_id)
        {
            $notes->delete_note($note_id);            
        }
        $result['success']='true';
        $result['message']='Notes deleted successfully';
        
        break;
}

echo json_encode($result);