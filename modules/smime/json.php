<?php
/**
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * @version $Id: source.php 7354 2011-05-03 06:46:51Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 * @since Group-Office 1.0
 */
require_once("../../Group-Office.php");

$GO_SECURITY->json_authenticate('smime');

require_once($GO_MODULES->modules['smime']['class_path'].'smime.class.inc.php');
$smime = new smime();

$start = isset($_REQUEST['start']) ? ($_REQUEST['start']) : '0';
$limit = isset($_REQUEST['limit']) ? ($_REQUEST['limit']) : '0';

$query = !empty($_REQUEST['query']) ? '%'.($_REQUEST['query']).'%' : '';


if(isset($_POST['delete_keys']))
{
	try
	{
		$response['deleteSuccess']=true;
		$ids = json_decode($_POST['delete_keys']);
		foreach($ids as $id)
		{
			$smime->delete_public_certificate($id);
		}
	}catch(Exception $e)
	{
		$response['deleteSuccess']=false;
		$response['deleteFeedback']=$e->getMessage();
	}
}

$response['count']=$smime->get_public_certificates($GO_SECURITY->user_id, $query, $start, $limit);
$response['results']=array();

while($r=$smime->next_record()){
	$response['results'][]=$r;
}

echo json_encode($response);

