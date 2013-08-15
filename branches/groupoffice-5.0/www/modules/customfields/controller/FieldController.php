<?php

class GO_Customfields_Controller_Field extends GO_Base_Controller_AbstractModelController {

	protected $model = 'GO_Customfields_Model_Field';

	protected function actionTypes($params) {

		if(isset($params['extend_model']))
			$response['results'] = GO_Customfields_CustomfieldsModule::getCustomfieldTypes($params['extend_model']);
		else
			$response['results'] = GO_Customfields_CustomfieldsModule::getCustomfieldTypes();
		$response['success']=true;

		return $response;
	}

	protected function afterSubmit(&$response, &$model, &$params, $modifiedAttributes) {

		if ($model->datatype == 'GO_Customfields_Customfieldtype_Select') {

			//select_options
			$ids = array();
			if (isset($params['select_options'])) {
				$select_options = json_decode($_POST['select_options'], true);
				for ($i = 0; $i < count($select_options); $i++) {

					if (!empty($select_options[$i]['id'])) {
						$so = GO_Customfields_Model_FieldSelectOption::model()->findByPk($select_options[$i]['id']);
					} else {
						$so = new GO_Customfields_Model_FieldSelectOption();
					}
					$so->sort_order = $i;
					$so->field_id = $model->id;
					$so->text = $select_options[$i]['text'];
					$so->save();
					if (empty($select_options[$i]['id'])) {
						$response['new_select_options'][$i] = $so->id;
					}
					$ids[] = $so->id;
				}

				//delete all other field options
				$stmt = GO_Customfields_Model_FieldSelectOption::model()->find(array(
						'by' => array(
								array('field_id', $model->id),
								array('id', $ids, 'NOT IN'),
						)
								));
				$stmt->callOnEach('delete');
			}
		}

		return parent::afterSubmit($response, $model, $params, $modifiedAttributes);
	}

	protected function actionSelectOptions($params) {
		
		$findParams = GO_Base_Db_FindParams::newInstance()->order('sort_order');
		$findParams->getCriteria()->addCondition('field_id', $params["field_id"]);
		
		$stmt = GO_Customfields_Model_FieldSelectOption::model()->find($findParams);

		$store = GO_Base_Data_Store::newInstance(GO_Customfields_Model_FieldSelectOption::model());
		$store->setStatement($stmt);
		$store->getColumnModel()->formatColumn('text', 'html_entity_decode($model->text)');
		return $store->getData();
	}

	protected function actionSaveSort($params) {
		$fields = json_decode($params['fields'], true);
		$sort = 0;
		foreach ($fields as $field) {
			$model = GO_Customfields_Model_Field::model()->findByPk($field['id']);
			$model->sort_index = $sort;
			$model->category_id = $field['category_id'];
			$model->save();
			$sort++;
		}

		return array('success' => true);
	}

	protected function getStoreParams($params) {
//		return array(
//				'where' => 'category.extends_model=:extends_model',
//				'bindParams' => array('extends_model' => $params['extends_model']),
//
//		);
		
		return GO_Base_Db_FindParams::newInstance()
						->limit(0)
						->order(array('category.sort_index', 't.sort_index'), array('ASC', 'ASC'))
						->criteria(GO_Base_Db_FindCriteria::newInstance()->addCondition('extends_model', $params['extends_model'],'=','category'));
	}

	protected function formatColumns(GO_Base_Data_ColumnModel $columnModel) {
		$columnModel->formatColumn('category_name', '$model->category->name');
		$columnModel->formatColumn('column_name', '$model->columnName()');
		$columnModel->formatColumn('type', '$model->customfieldtype->name()');
		$columnModel->formatColumn('unique_values', '$model->unique_values ? GO::t("cmdYes") : GO::t("cmdNo")');
		return parent::formatColumns($columnModel);
	}

	protected function actionSaveTreeSelectOption($params) {
		if (empty($params['tree_select_option_id'])) {
			$model = new GO_Customfields_Model_FieldTreeSelectOption();
		} else {
			$model = GO_Customfields_Model_FieldTreeSelectOption::model()->findByPk($params['tree_select_option_id']);
		}

		$model->setAttributes($params);
		$response['success'] = $model->save();

		return $response;
	}

	protected function actionImportSelectOptions($params) {

		$importFile = GO::config()->getTempFolder() . 'selectoptionsimport.csv';
		if (is_uploaded_file($_FILES['importfile']['tmp_name'][0])) {
			move_uploaded_file($_FILES['importfile']['tmp_name'][0], $importFile);
		}

		if (!file_exists($importFile)) {
			throw new Exception('File was not uploaded!');
		}
		$csv = new GO_Base_Fs_CsvFile($importFile);
		$sortOrder = 0;
		while ($record = $csv->getRecord()) {
			$o = new GO_Customfields_Model_FieldSelectOption();
			$o->field_id = $params['field_id'];
			$o->text = $record[0];
			$o->sort_order = $sortOrder++;
			$o->save();
		}

		return array('success' => true);
	}

	protected function actionImportTreeSelectOptions($params) {

		$importFile = GO::config()->getTempFolder() . 'selectoptionsimport.csv';
		
		if (is_uploaded_file($_FILES['importfile']['tmp_name'][0])) {
			move_uploaded_file($_FILES['importfile']['tmp_name'][0], $importFile);
		}

		if (!file_exists($importFile)) {
			throw new Exception('File was not uploaded!');
		}
		
		$field = GO_Customfields_Model_Field::model()->findByPk($params['field_id']);
		
		$sort=1;
		$csv = new GO_Base_Fs_CsvFile($importFile);
		while ($record = $csv->getRecord()) {

			for ($i = 0; $i < count($record); $i++) {
				
				if($i==0)
					$parent_id=0;

				if (!empty($record[$i])) {
					$existingModel = GO_Customfields_Model_FieldTreeSelectOption::model()->findSingleByAttributes(array(							
						'field_id'=>$params['field_id'],
						'parent_id'=>$parent_id,
						'name'=> $record[$i]
					));					
					
					if($existingModel)
						$parent_id=$existingModel->id;
					else{
						$o = new GO_Customfields_Model_FieldTreeSelectOption();
						
						$o->checkSlaves=false;
						
						$o->field_id = $params['field_id'];
						$o->name = $record[$i];
						$o->parent_id=$parent_id;
						$o->sort=$sort;
						$o->save();
						
						$sort++;
						
						$parent_id=$o->id;
					}
				}			
			}
		}
		
		$field->checkTreeSelectSlaves();

		return array('success' => true);
	}

}

