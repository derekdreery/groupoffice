<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: json.php 5426 2009-08-04 15:01:52Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */
require('../../Group-Office.php');

GO::security()->json_authenticate('mediawiki');

require_once(GO::modules()->modules['mediawiki']['class_path'].'mediawiki.class.inc.php');
$mw = new mediawiki();

$task=isset($_REQUEST['task']) ? ($_REQUEST['task']) : '';

try{
	switch($task)
	{

		case 'load_settings':

			$response['data'] = array();
			$response['data']['title'] = GO::config()->get_setting('mediawiki_title');
				if (empty($response['data']['title'])) $response['data']['title'] = 'Mediawiki';
			$response['data']['external_url'] = GO::config()->get_setting('mediawiki_external_url');
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