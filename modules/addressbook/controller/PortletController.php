<?php
/*
 * Copyright Intermesh BV.
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 */

/**
 * The GO_Addressbook_Controller_Portlet controller
 *
 * @package GO.modules.Addressbook.controller
 * @version $Id$
 * @copyright Copyright Intermesh BV.
 * @author Michael de Hart <mdhart@intermesh.nl>
 *

class GO_Addressbook_Controller_Portlet extends GO_Base_Controller_AbstractJsonController {
	
	/**
	 * Get the 10 latest bithdays from the contact in the addressbook
	 * 
	 * @param integer $addressbook_id id of addressbook to select birthdays from
	 *
	protected function actionBirthdays() {

		$yesterday = GO_Base_Util_Date::date_add(mktime(0,0,0),-1);
		$next_month = GO_Base_Util_Date::date_add(mktime(0,0,0),30);
		
		$start = date('Y-m-d',strtotime($yesterday));
		$end = date('Y-m-d',strtotime($next_month));

		$select = "t.id, birthday, first_name, middle_name, last_name, "
			."IF (STR_TO_DATE(CONCAT(YEAR('$start'),'/',MONTH(birthday),'/',DAY(birthday)),'%Y/%c/%e') >= '$start', "
			."STR_TO_DATE(CONCAT(YEAR('$start'),'/',MONTH(birthday),'/',DAY(birthday)),'%Y/%c/%e') , "
			."STR_TO_DATE(CONCAT(YEAR('$start')+1,'/',MONTH(birthday),'/',DAY(birthday)),'%Y/%c/%e')) "
			."as upcoming ";
		
		$findCriteria = GO_Base_Db_FindCriteria::newInstance()
						->addCondition('birthday', '0000-00-00', '!=')
						->addRawCondition('birthday', 'NULL', 'IS NOT');
		
		$settings = GO_Addressbook_Model_BirthdayPortletSetting::model()->findByAttribute('user_id', GO::user()->id);
		
		if(count($settings)) {
			$abooks=array_map(function($value) {
				return $value->addressbook_id;
			}, $settings);
			$findCriteria->addInCondition('addressbook_id', $abooks);
		}
		
		$having = "upcoming BETWEEN '$start' AND '$end'";
		
		$findParams = GO_Base_Db_FindParams::newInstance()
			->distinct()
			->select($select)
			->criteria($findCriteria)
			->having($having)
			->order('upcoming');
		
		$columnModel = new GO_Base_Data_ColumnModel('GO_Addressbook_Model_Contact');
		
		$store = new GO_Base_Data_DbStore('GO_Addressbook_Model_Contact', $columnModel, $_POST, $findParams);
		
		echo $this->renderStore($store);
		
	}
	
	/**
	 * Get all selected addressbooks
	 * If post request delete all buy user_id and add selected
	 *
	protected function actionBirthdaysSettings() {
		
		if(GO_Base_Util_Http::isPostRequest() && isset($_POST['addressbook_ids'])) {
			
			GO_Addressbook_Model_BirthdayPortletSetting::model()->deleteByAttribute('user_id', GO::user()->id);
			
			foreach($_POST['addressbook_ids'] as $addressbook_id) {
				$setting = new GO_Addressbook_Model_BirthdayPortletSetting();
				$setting->addressbook_id = $addressbook_id;
				$setting->user_id = Go::user()->id;
				$setting->save();
			}
		}
		
		$settings = GO_Addressbook_Model_BirthdayPortletSetting::model()->findByAttribute('user_id', GO::user()->id);
		$abooks=array_map(function($value) {
			return $value->addressbook_id;
		}, $settings);
		
		echo $this->renderJson($abooks);
	}
	
}
*/
?>
<?php
/*
 * Copyright Intermesh BV.
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 */

/**
 * The GO_Addressbook_Controller_Portlet controller
 *
 * @package GO.modules.Addressbook.controller
 * @version $Id$
 * @copyright Copyright Intermesh BV.
 * @author Michael de Hart <mdhart@intermesh.nl>
 */
class GO_Addressbook_Controller_Portlet extends GO_Base_Controller_AbstractMultiSelectModelController {
	
	/**
	 * The name of the model from where the MANY_MANY relation is called
	 * @return String 
	 */
	public function modelName() {
		return 'GO_Addressbook_Model_Addressbook';
	}
	
	/**
	 * Returns the name of the model that handles the MANY_MANY relation.
	 * @return String 
	 */
	public function linkModelName() {
		return 'GO_Addressbook_Model_BirthdaysPortletSetting';
	}
	
	/**
	 * The name of the field in the linkModel where the key of the current model is defined.
	 * @return String
	 */
	public function linkModelField() {
		return 'addressbook_id';
	}
	
	/**
	 * Get the data for the grid that shows all the tasks from the selected tasklists.
	 * 
	 * @param Array $params
	 * @return Array The array with the data for the grid. 
	 */
	protected function actionBirthdays($params) {
		
		$today = mktime(0,0,0);
		$next_month = GO_Base_Util_Date::date_add(mktime(0,0,0),30);
		//GO::debug($yesterday);
		
		$start = date('Y-m-d',$today);
		$end = date('Y-m-d',$next_month);
		//GO::debug($start);
		
		$select = "t.id, birthday, first_name, middle_name, last_name, addressbook_id, photo, "
			."IF (STR_TO_DATE(CONCAT(YEAR('$start'),'/',MONTH(birthday),'/',DAY(birthday)),'%Y/%c/%e') >= '$start', "
			."STR_TO_DATE(CONCAT(YEAR('$start'),'/',MONTH(birthday),'/',DAY(birthday)),'%Y/%c/%e') , "
			."STR_TO_DATE(CONCAT(YEAR('$start')+1,'/',MONTH(birthday),'/',DAY(birthday)),'%Y/%c/%e')) "
			."as upcoming ";
		
		$findCriteria = GO_Base_Db_FindCriteria::newInstance()
						->addCondition('birthday', '0000-00-00', '!=')
						->addRawCondition('birthday', 'NULL', 'IS NOT');
		
		$settings = GO_Addressbook_Model_BirthdaysPortletSetting::model()->findByAttribute('user_id', GO::user()->id);
		
		if(count($settings)) {
			$abooks=array_map(function($value) {
				return $value->addressbook_id;
			}, $settings->fetchAll());
			$findCriteria->addInCondition('addressbook_id', $abooks);
		}
		
		$having = "upcoming BETWEEN '$start' AND '$end'";
		
		$findParams = GO_Base_Db_FindParams::newInstance()
			->distinct()
			->select($select)
			->criteria($findCriteria)
			->having($having)
			->order('upcoming');
		
		
		//$response['data']['original_photo_url']=$model->photoURL;
		$columnModel = new GO_Base_Data_ColumnModel('GO_Addressbook_Model_Contact');
		$columnModel->formatColumn('addressbook_id', '$model->addressbook->name');
		$columnModel->formatColumn('photo_url', '$model->getPhotoThumbURL()');
		$columnModel->formatColumn('age', '($model->upcoming != date("Y-m-d")) ? $model->age+1 : $model->age');
		
		$store = new GO_Base_Data_DbStore('GO_Addressbook_Model_Contact', $columnModel, $_POST, $findParams);
		
		return $store->getData();
		
	}
	
}