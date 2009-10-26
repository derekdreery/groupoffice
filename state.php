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
 * @version $Id$
 * @author Merijn Schering <mschering@intermesh.nl>
 */

require_once("Group-Office.php");
$GO_SECURITY->json_authenticate();



$response['success']= $GO_CONFIG->save_state($GO_SECURITY->user_id,
	$_POST['name'],
	$_POST['value']
	);
	
echo json_encode($response);