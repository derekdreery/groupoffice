<?php
class GO_Customfields_Controller_BlockField extends \GO\Base\Controller\AbstractJsonController{

	protected function actionSelectStore($params) {
		
		$columnModel = new \GO\Base\Data\ColumnModel(\GO_Customfields_Model_Field::model());
		$columnModel->formatColumn('extends_model', '$model->category->extends_model', array(), 'category_id');
		$columnModel->formatColumn('full_info','"[".\GO::t($model->category->extends_model,"customfields")."] ".$model->category->name." : ".$model->name." (col_".$model->id.")"', array(), 'category_id');
		
		$findParams = \GO\Base\Db\FindParams::newInstance()
			->joinModel(array(
				'model'=>'GO_Customfields_Model_Category',
				'localTableAlias'=>'t',
				'localField'=>'category_id',
				'foreignField'=>'id',
				'tableAlias'=>'c'
			))
			->criteria(
				\GO\Base\Db\FindCriteria::newInstance()
					->addInCondition(
						'extends_model',
						array(
							'GO_Addressbook_Model_Contact',
							'GO_Addressbook_Model_Company',
							'GO_Projects_Model_Project',
							'\GO\Base\Model\User'
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
		
		$store = new \GO\Base\Data\DbStore('GO_Customfields_Model_Field', $columnModel, $params, $findParams);

		echo $this->renderStore($store);
		
	}
	
}