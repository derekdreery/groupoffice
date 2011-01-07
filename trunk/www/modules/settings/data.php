<?php

require('../../Group-Office.php');

switch($_REQUEST['task']){

	case 'load_settings':

		// Triggered when saving
		if(isset($_REQUEST['save']) && $_REQUEST['save'] == 'true')
		{
			$GO_EVENTS->fire_event('save_global_settings', array(&$response));
			$response['saved'] = true;
		}

		$response['data']=array();

		$GO_EVENTS->fire_event('load_global_settings',array(&$response));

		$response['success']=true;
		break;


/*
	case 'save_settings':

		$response=array();

		$GO_EVENTS->fire_event('save_global_settings', array(&$response));

		$response['success']=true;

		break; 
 */
} 

echo json_encode($response);