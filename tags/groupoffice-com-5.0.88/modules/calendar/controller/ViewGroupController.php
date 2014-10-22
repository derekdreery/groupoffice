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
 * The GO_Calendar_Controller_View controller
 *
 * @package GO.modules.Calendar
 * @version $Id$
 * @copyright Copyright Intermesh BV.
 * @author Michael de Hart mdhart@intermesh.nl
 */

class GO_Calendar_Controller_ViewGroup extends GO_Base_Controller_AbstractMultiSelectModelController {

	//protected $model = 'GO_Calendar_Model_ViewGroup';
	
	//protected function formatColumns(GO_Base_Data_ColumnModel $columnModel) {
	//	$columnModel->formatColumn('group_name','$model->group->name',array(),'group_id');
	//	return parent::formatColumns($columnModel);
	//}

  public function linkModelField() {
    return 'group_id';
  }

  public function linkModelName() {
    return 'GO_Calendar_Model_ViewGroup';
  }

  public function modelName() {
    return 'GO_Base_Model_Group';
  }
	
}