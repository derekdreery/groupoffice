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
$GLOBALS['GO_SECURITY']->json_authenticate('currencies');
require_once ($GLOBALS['GO_MODULES']->modules['currencies']['class_path']."currencies.class.inc.php");
//require_once ($GLOBALS['GO_LANGUAGE']->get_language_file('currencies'));
$currencies = new currencies();
try {
	switch($_REQUEST['task']) {

		case 'save_currencies':
		//expenses
			$ids = array();

			$cs= json_decode($_POST['currencies'], true);
			for($i=0;$i<count($cs);$i++) {
				$c = $cs[$i];
				
				$c['code']=strtoupper($c['code']);
				$c['value']=Number::to_phpnumber($c['value']);
				$ids[]=$c['code'];

				//var_dump($c);
				$currencies->replace_currency($c);
				
			}
			$currencies->delete_other_currencies($ids);

			$response['success']=true;
			break;

/* {TASKSWITCH} */
	}
}
catch(Exception $e) {
	$response['feedback']=$e->getMessage();
	$response['success']=false;
}
echo json_encode($response);
