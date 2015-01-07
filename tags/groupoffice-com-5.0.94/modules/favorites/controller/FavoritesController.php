<?php

class GO_Favorites_Controller_Favorites extends GO_Base_Controller_AbstractJsonController {
	
	protected function actionCalendarStore(){
		$colModel = new GO_Base_Data_ColumnModel(GO_Favorites_Model_Calendar::model());
		$colModel->setColumnsFromModel(GO_Calendar_Model_Calendar::model());

		$findParams = new GO_Base_Db_FindParams();
		$findParams->getCriteria()->addCondition('user_id', GO::user()->id, '=','cal');
		$findParams->order('name');
		$findParams->joinModel(
			array(
				'model'=>'GO_Favorites_Model_Calendar',					
				'localTableAlias'=>'t', //defaults to "t"
				'localField'=>'id', //defaults to "id"
				'foreignField'=>'calendar_id', //defaults to primary key of the remote model
				'tableAlias'=>'cal', //Optional table alias
				'type'=>'INNER' //defaults to INNER,
			)
		);
				
		$store = new GO_Base_Data_DbStore('GO_Calendar_Model_Calendar',$colModel , $_POST, $findParams);
		
		$store->defaultSort = array('name');
		$store->multiSelectable('calendars');
		
		echo $this->renderStore($store);	
	}
	
	protected function actionTasklistStore(){
		$colModel = new GO_Base_Data_ColumnModel(GO_Favorites_Model_Tasklist::model());
//		$colModel->formatColumn('type', '$model->customfieldtype->name()');
		
		$findParams = new GO_Base_Db_FindParams();
		$findParams->getCriteria()->addCondition('user_id', GO::user()->id, '=','tal');
		$findParams->order('name');
		$findParams->joinModel(
			array(
				'model'=>'GO_Favorites_Model_Tasklist',					
				'localTableAlias'=>'t', //defaults to "t"
				'localField'=>'id', //defaults to "id"
				'foreignField'=>'tasklist_id', //defaults to primary key of the remote model
				'tableAlias'=>'tal', //Optional table alias
				'type'=>'INNER' //defaults to INNER,
			)
		);
		$store = new GO_Base_Data_DbStore('GO_Tasks_Model_Tasklist',$colModel , $_POST, $findParams);
//		$store->defaultSort = array('sort','name');
		$store->multiSelectable('ta-taskslists');
		
		echo $this->renderStore($store);	
	}
	
	protected function actionAddressbookStore(){
		$colModel = new GO_Base_Data_ColumnModel(GO_Addressbook_Model_Addressbook::model());
//		$colModel->formatColumn('type', '$model->customfieldtype->name()');
		
		$findParams = new GO_Base_Db_FindParams();
		$findParams->ignoreAcl();
		$findParams->getCriteria()->addCondition('user_id', GO::user()->id, '=','f');
		$findParams->order('name');
		$findParams->joinModel(
			array(
				'model'=>'GO_Favorites_Model_Addressbook',					
				'localTableAlias'=>'t', //defaults to "t"
				'localField'=>'id', //defaults to "id"
				'foreignField'=>'addressbook_id', //defaults to primary key of the remote model
				'tableAlias'=>'f', //Optional table alias
				'type'=>'INNER' //defaults to INNER,
			)
		);	
		
		$store = new GO_Base_Data_DbStore('GO_Addressbook_Model_Addressbook',$colModel , $_POST, $findParams);
		$store->multiSelectable('books');
//		$store->defaultSort = array('sort','name');
		
		echo $this->renderStore($store);	
	}
}