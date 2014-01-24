<?php
class GO_Customfields_Controller_BlockField extends GO_Base_Controller_AbstractJsonController{

	protected function actionSelectStore($params) {
		
		$columnModel = new GO_Base_Data_ColumnModel(GO_Customfields_Model_Field::model());
		$columnModel->formatColumn('extends_model', '$model->category->extends_model', array(), 'category_id');
		$columnModel->formatColumn('full_info','"[".GO::t($model->category->extends_model,"customfields")."] ".$model->category->name." : ".$model->name." (col_".$model->id.")"', array(), 'category_id');
		
		$findParams = GO_Base_Db_FindParams::newInstance()
			->joinModel(array(
				'model'=>'GO_Customfields_Model_Category',
				'localTableAlias'=>'t',
				'localField'=>'category_id',
				'foreignField'=>'id',
				'tableAlias'=>'c'
			))
			->criteria(
				GO_Base_Db_FindCriteria::newInstance()
					->addInCondition(
						'extends_model',
						array(
							'GO_Addressbook_Model_Contact',
							'GO_Addressbook_Model_Company',
							'GO_Projects_Model_Project',
							'GO_Base_Model_User'
						),
						'c'
					)
					->addInCondition(
						'datatype',
						array(
							'GO_Addressbook_Customfieldtype_Contact',
							'GO_Addressbook_Customfieldtype_Company'
						),
						't'
					)
			);
		
		$store = new GO_Base_Data_DbStore('GO_Customfields_Model_Field', $columnModel, $params, $findParams);

		echo $this->renderStore($store);
		
	}
	
}