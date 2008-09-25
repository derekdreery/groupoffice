<?php
/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @version $Id: state.php 2952 2008-09-03 09:47:49Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 */

require_once("Group-Office.php");
$GO_SECURITY->json_authenticate();



$response['success']= $GO_CONFIG->save_state($GO_SECURITY->user_id, 
	smart_addslashes($_POST['index']),
	smart_addslashes($_POST['name']),
	smart_addslashes($_POST['value'])
	);
	
echo json_encode($response);