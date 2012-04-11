<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */
require_once("../../Group-Office.php");
$GLOBALS['GO_SECURITY']->json_authenticate('customcss');

try{	
	file_put_contents($GLOBALS['GO_CONFIG']->file_storage_path.'customcss/style.css', $_POST['css']);
	file_put_contents($GLOBALS['GO_CONFIG']->file_storage_path.'customcss/javascript.js', $_POST['javascript']);

	$response['success']=true;
}catch(Exception $e)
{
	$response['feedback']=$e->getMessage();
	$response['success']=false;
}
echo json_encode($response);