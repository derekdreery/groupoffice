<?php

class GO_Settings_Controller_Setting extends GO_Base_Controller_AbstractController {

	protected function actionLoad($params){
		$response = array();
		
		$response['data']=array();
		
		$t = GO::config()->get_setting('login_screen_text_enabled');
		$response['data']['login_screen_text_enabled']=!empty($t);

		$t = GO::config()->get_setting('login_screen_text');
		$response['data']['login_screen_text']=$t ? $t : '';

		$t = GO::config()->get_setting('login_screen_text_title');
		$response['data']['login_screen_text_title']=$t ? $t : '';

		$response['data']['addressbook_name_template'] = GO_Base_Model_AbstractUserDefaultModel::getNameTemplate("GO_Addressbook_Model_Addressbook");
		$response['data']['task_name_template'] = GO_Base_Model_AbstractUserDefaultModel::getNameTemplate("GO_Tasks_Model_Tasklist");
		$response['data']['calendar_name_template'] = GO_Base_Model_AbstractUserDefaultModel::getNameTemplate("GO_Calendar_Model_Calendar");
		
//		$GLOBALS['GO_EVENTS']->fire_event('load_global_settings',array(&$response));

		$response['success']=true;
		return $response;
	}
	
	protected function actionSubmit($params) {
		
		$text = $params['login_screen_text'];
		$reportFeedback = '';

		if(preg_match("/^<br[^>]*>$/", $text))
			$text="";

		GO::config()->save_setting('login_screen_text', $text);
		GO::config()->save_setting('login_screen_text_title', $_POST['login_screen_text_title']);

		GO::config()->save_setting('login_screen_text_enabled', !empty($_POST['login_screen_text_enabled']) ? '1' : '0');

		if (!empty($params['addressbook_name_template']))
			GO_Base_Model_AbstractUserDefaultModel::setNameTemplate("GO_Addressbook_Model_Addressbook",$params['addressbook_name_template']);
		
		if (!empty($params['task_name_template']))
			GO_Base_Model_AbstractUserDefaultModel::setNameTemplate("GO_Tasks_Model_Tasklist",$params['task_name_template']);
		if (isset($params['GO_Tasks_Model_Tasklist_change_all_names']))
			$this->_updateAllDefaultTasklists($reportFeedback);
		
		if (!empty($params['calendar_name_template']))
			GO_Base_Model_AbstractUserDefaultModel::setNameTemplate("GO_Calendar_Model_Calendar",$params['calendar_name_template']);
		if (isset($params['GO_Calendar_Model_Calendar_change_all_names']))
			$this->_updateAllDefaultCalendars($reportFeedback);	
		
		$response['feedback'] = !empty($reportFeedback) ? $reportFeedback : '';
		$response['success'] = true;
		return $response;
	}
	
	private function _updateAllDefaultTasklists(&$feedback='') {		
		$stmt = GO_Tasks_Model_Tasklist::model()->find(
			GO_Base_Db_FindParams::newInstance()
				->ignoreAcl()
				->joinModel(
					array(
						'model'=>'GO_Tasks_Model_Settings',
						'localTableAlias'=>'t',
						'localField'=>'id',
						'foreignField'=>'default_tasklist_id',
						'tableAlias'=>'sett'
					)
				));
		while ($updateModel = $stmt->fetch()) {
			try{
				$updateModel->setDefaultAttributes(false);
				$updateModel->save();
			}catch(Exception $e){
				$feedback .= $e->getMessage();
			}
		}
	}
	
	private function _updateAllDefaultCalendars(&$feedback=''){
		$stmt = GO_Calendar_Model_Calendar::model()->find(
			GO_Base_Db_FindParams::newInstance()
				->ignoreAcl()
				->joinModel(
					array(
						'model'=>'GO_Calendar_Model_Settings',
						'localTableAlias'=>'t',
						'localField'=>'id',
						'foreignField'=>'calendar_id',
						'tableAlias'=>'sett'
					)
				));
		while ($updateModel = $stmt->fetch()) {
			try{
				$updateModel->setDefaultAttributes(false);
				$updateModel->save();
			}catch(Exception $e){
				$feedback .= $e->getMessage();
			}
		}
	}

}
?>
