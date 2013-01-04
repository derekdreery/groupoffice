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

//close writing to session so other concurrent requests won't be locked out.
session_write_close();


$values = json_decode($_POST['values'], true);

foreach($values as $name=>$value){

	$GO_CONFIG->save_state($GO_SECURITY->user_id,
		$name,
		$value
	);
}
$response['success']=true;
echo json_encode($response);