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
		if(!empty($params['freq']))
		{
			$rRule = new GO_Base_Util_Icalendar_Rrule();
			$rRule->readJsonArray($params);		
			$model->rrule = $rRule->createRrule();
		}
		
		if(isset($params['reminder_value']) && isset($params['reminder_multiplier'])) // Check for a setted reminder TODO: This lines are copied from Tasks and are not working correctly
			$model->reminder= $params['reminder_value']*$params['reminder_multiplier'];
		else 
			$model->reminder = 0;
		
		return parent::beforeSubmit($response, $model, $params);
	
	}
	
	
	protected function afterSubmit(&$response, &$model, &$params, $modifiedAttributes) {
		
		if(!empty($params['exception_for_event_id'])){
						
			$oldmodel = GO_Calendar_Model_Event::model()->findByPk($params['exception_for_event_id']);
			
			
			$params['exception_date']; // TODO: set this one too
			
		}
		
		return parent::afterSubmit($response, $model, $params, $modifiedAttributes);
	}
	
	public function afterLoad(&$response, &$model, &$params) {
		
		$response['data']['subject'] = $response['data']['name'];
		
		
		// TODO: This lines are copied from Tasks and are not working correctly
		if(isset($response['data']['rrule']) && !empty($response['data']['rrule'])) {
			$rRule = new GO_Base_Util_Icalendar_Rrule();
			$rRule->readIcalendarRruleString($model->start_time, $model->rrule);
			$createdRule = $rRule->createJSONOutput();

			$response['data'] = array_merge($response['data'],$createdRule);
		}
		
		$response = $this->_reminderSecondsToForm($response);
		
		
		$response['data']['start_time'] = date(GO::user()->time_format, $model->start_time);
		$response['data']['end_time'] = date(GO::user()->time_format, $model->end_time);
		
		if(!empty($params['recurrenceExceptionDate'])){
			//is a unixtimestamp. We should return this event with an empty id and the exception date.
			
			$response['data']['start_date'] = GO_Base_Util_Date::get_timestamp($params['recurrenceExceptionDate']);
			$response['data']['end_date'] = GO_Base_Util_Date::date_add($params['recurrenceExceptionDate'], $model->getDiff()->d);
			$response['data']['id']=0;
			$response['data']['exception_for_event_id']=$model->id;
		}else
		{
			
			$response['data']['start_date'] = GO_Base_Util_Date::get_timestamp($model->start_time, false);
			$response['data']['end_date'] = GO_Base_Util_Date::get_timestamp($model->end_time, false);

		}
		
		return parent::beforeDisplay($response, $model, $params);
	}
	
	protected function remoteComboFields(){
		return array(
			//	'category_id'=>'$model->category->name',
				'calendar_id'=>'$model->calendar->name'
				);
	}
	
	
	private function _reminderSecondsToForm($response) {
		$multipliers[] = 604800;
		$multipliers[] = 86400;
		$multipliers[] = 3600;
		$multipliers[] = 60;

		$response['data']['reminder_multiplier'] = 60;
		$response['data']['reminder_value'] = 0;

		if(!empty($response['data']['reminder'])) {
			for ($i = 0; $i < count($multipliers); $i ++) {
				$devided = $response['data']['reminder'] / $multipliers[$i];
				$match = (int) $devided;
				if ($match == $devided) {
					$response['data']['reminder_multiplier'] = $multipliers[$i];
					$response['data']['reminder_value'] = $devided;
					break;
				}
			}
		}
		return $response;
	}
	
}