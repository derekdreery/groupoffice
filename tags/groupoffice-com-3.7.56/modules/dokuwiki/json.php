<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: json.php 5426 2011-07-06 15:01:52Z wsmits $
 * @copyright Copyright Intermesh
 * @author Wesley Smits <wsmits@intermesh.nl>
 */
require('../../Group-Office.php');

$GO_SECURITY->json_authenticate('dokuwiki');

//require_once($GO_MODULES->modules['dokuwiki']['class_path'].'dokuwiki.class.inc.php');
//$dw = new dokuwiki();

$task=isset($_REQUEST['task']) ? ($_REQUEST['task']) : '';

try{
	switch($task)
	{

		case 'load_settings':

			$response['data'] = array();
			$response['data']['title'] = $GO_CONFIG->get_setting('dokuwiki_title');
				if (empty($response['data']['title'])) $response['data']['title'] = 'Dokuwiki';
			$response['data']['external_url'] = $GO_CONFIG->get_setting('dokuwiki_external_url');
				if (empty($response['data']['external_url'])) $response['data']['external_url'] = '';
			$response['success'] = true;

			break;

/* {TASKSWITCH} */
	}
}catch(Exception $e)
{
	$response['feedback']=$e->getMessage();
	$response['success']=false;
}
echo json_encode($response);