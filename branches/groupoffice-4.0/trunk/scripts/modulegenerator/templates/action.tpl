<?php
/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: action.tpl 1858 2008-04-29 14:09:19Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

require_once("../../Group-Office.php");
$GLOBALS['GO_SECURITY']->json_authenticate('{module}');

require_once ($GLOBALS['GO_MODULES']->modules['{module}']['class_path']."{module}.class.inc.php");
//require_once ($GLOBALS['GO_LANGUAGE']->get_language_file('{module}'));

${module} = new {module}();


try{

	switch($_REQUEST['task'])
	{
/* {TASKSWITCH} */
	}
}catch(Exception $e)
{
	$response['feedback']=$e->getMessage();
	$response['success']=false;
}

echo json_encode($response);