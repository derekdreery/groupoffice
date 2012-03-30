<?php

require('../../Group-Office.php');

session_write_close();

ini_set('max_execution_time',360);

switch($_REQUEST['task']){

	case 'load_settings':

		// Triggered when saving
		if(isset($_REQUEST['save']) && $_REQUEST['save'] == 'true')
		{

			$text = $_POST['login_screen_text'];

			if(preg_match("/^<br[^>]*>$/", $text))
				$text="";

			$GO_CONFIG->save_setting('login_screen_text', $text);
			$GO_CONFIG->save_setting('login_screen_text_title', $_POST['login_screen_text_title']);
			
			$GO_CONFIG->save_setting('login_screen_text_enabled', !empty($_POST['login_screen_text_enabled']) ? '1' : '0');

			$GO_EVENTS->fire_event('save_global_settings', array(&$response));
			$response['saved'] = true;
		}

		$response['data']=array();
		
		$t = $GO_CONFIG->get_setting('login_screen_text_enabled');
		$response['data']['login_screen_text_enabled']=!empty($t);

		$t = $GO_CONFIG->get_setting('login_screen_text');
		$response['data']['login_screen_text']=$t ? $t : '';

		$t = $GO_CONFIG->get_setting('login_screen_text_title');
		$response['data']['login_screen_text_title']=$t ? $t : '';

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