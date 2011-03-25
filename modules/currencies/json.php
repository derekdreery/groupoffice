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
require_once('../../Group-Office.php');
$GO_SECURITY->json_authenticate('currencies');
require_once ($GO_MODULES->modules['currencies']['class_path'].'currencies.class.inc.php');
$currencies = new currencies();
$task=isset($_REQUEST['task']) ? $_REQUEST['task'] : '';
try{
	switch($task)
	{
		case 'currencies':
			if(isset($_POST['delete_keys']))
			{
				try{
					$response['deleteSuccess']=true;
					$delete_currencies = json_decode($_POST['delete_keys']);
					foreach($delete_currencies as $currency_id)
					{
						$currencies->delete_currency(addslashes($currency_id));
					}
				}catch(Exception $e)
				{
					$response['deleteSuccess']=false;
					$response['deleteFeedback']=$e->getMessage();
				}
			}
			$sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'code';
			$dir = isset($_REQUEST['dir']) ? $_REQUEST['dir'] : 'DESC';
			$start = isset($_REQUEST['start']) ? $_REQUEST['start'] : '0';
			$limit = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : '0';
			$query = !empty($_REQUEST['query']) ? '%'.$_REQUEST['query'].'%' : '';
			$response['total'] = $currencies->get_currencies( $query, $sort, $dir, $start, $limit);
			$response['results']=array();
			while($currency = $currencies->next_record())
			{
				$currency['value']=Number::format($currency['value']);
				$response['results'][] = $currency;
			}
			break;
/* {TASKSWITCH} */
	}
}catch(Exception $e)
{
	$response['feedback']=$e->getMessage();
	$response['success']=false;
}
echo json_encode($response);
