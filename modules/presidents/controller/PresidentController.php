<?php

/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

/**
 * The President controller
 */
class GO_Presidents_Controller_President extends GO_Base_Controller_AbstractModelController {
	
	protected $model = 'GO_Presidents_Model_President';

	/**
	 * Tell the controller to change some column values
	 */
	protected function formatColumns(GO_Base_Data_ColumnModel $columnModel) {
		$columnModel->formatColumn('party_id','$model->party->name');
		$columnModel->formatColumn('income_val','$model->income');
		return parent::formatColumns($columnModel);
	}

	/**
	 * Display corrent value in combobox
	 */
	protected function remoteComboFields(){
		return array('party_id'=>'$model->party->name');
	}
	
	protected function afterDisplay(&$response, &$model, &$params) {
		$response['data']['write_permission'] = true;
		$response['data']['permission_level'] = GO_Base_Model_Acl::MANAGE_PERMISSION;
		$response['data']['partyName'] = $model->party->name;
		return parent::beforeDisplay($response, $model, $params);
	}
}

