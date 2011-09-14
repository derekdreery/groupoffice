<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: GO_Calendar_Controller_Event.php 7607 2011-09-14 10:06:07Z <<USERNAME>> $
 * @copyright Copyright Intermesh
 * @author <<FIRST_NAME>> <<LAST_NAME>> <<EMAIL>>@intermesh.nl
 */  

/**
 * The GO_Calendar_Controller_Event controller
 *
 */

class GO_Calendar_Controller_Event extends GO_Base_Controller_AbstractModelController {

	protected $model = 'GO_Calendar_Model_Event';
	
	function beforeSubmit(&$response, &$model, &$params) {
		
		$params['name'] = $params['subject'];

		if (isset($params['all_day_event'])) {
			$params['all_day_event'] = '1';
			$start_time = "00:00";
			$end_time = '23:59';
		} else {
			$params['all_day_event'] = '0';
			$start_time = $params['start_time'];
			$end_time = $params['end_time'];
		}

		$params['start_time'] = $params['start_date'].' '.$start_time;
		$params['end_time'] = $params['end_date'].' '.$end_time;
		
		// TODO: This lines are copied from Tasks and are not working correctly
		if(isset($params['freq']) && !empty($params['freq']))
		{
			$rRule = new GO_Base_Util_Icalendar_Rrule();
			$rRule->readJsonArray($params);		
			$model->rrule = $rRule->createRrule();
		}
		
		if(isset($params['remind'])) // Check for a setted reminder TODO: This lines are copied from Tasks and are not working correctly
			$model->reminder= GO_Base_Util_Date::to_unixtime($params['remind_date'].' '.$params['remind_time']);	
		else 
			$model->reminder = 0;
		
		return parent::beforeSubmit($response, $model, $params);
	}
	
	public function afterLoad(&$response, &$model, &$params) {
		
		$response['data']['subject'] = $response['data']['name'];
		
		$response['data']['start_time'] = date(GO::user()->time_format, strtotime($response['data']['start_time']));
		$response['data']['start_date'] = date(GO::user()->completeDateFormat, strtotime($response['data']['start_time']));
		
		$response['data']['end_time'] = date(GO::user()->time_format, strtotime($response['data']['end_time']));
		$response['data']['end_date'] = date(GO::user()->completeDateFormat, strtotime($response['data']['end_time']));
		
		// TODO: This lines are copied from Tasks and are not working correctly
		if(isset($params['rrule']) && !empty($params['rrule'])) {
			$rRule = new GO_Base_Util_Icalendar_Rrule();
			$rRule->readIcalendarRruleString($model->start_time, $model->rrule);
			$createdRule = $rRule->createJSONOutput();

			$response['data'] = array_merge($response['data'],$createdRule);
		}
		
		if(isset($response['data']['reminder']) && !empty($response['data']['reminder'])) {			
			$response['data']['remind']=1;
			$response['data']['remind_date']=date(GO::user()->completeDateFormat, strtotime($response['data']['reminder']));
			$response['data']['remind_time']=date(GO::user()->time_format, strtotime($response['data']['reminder']));
		}
		
		return parent::beforeDisplay($response, $model, $params);
	}
	
}