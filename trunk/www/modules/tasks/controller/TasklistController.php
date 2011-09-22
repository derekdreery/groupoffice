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
 * The GO_Tasks_Controller_Tasklist controller
 *
 * @package GO.modules.Tasks
 * @version $Id: GO_Tasks_Controller_Tasklist.php 7607 2011-09-20 10:08:21Z <<USERNAME>> $
 * @copyright Copyright Intermesh BV.
 * @author <<FIRST_NAME>> <<LAST_NAME>> <<EMAIL>>@intermesh.nl
 */

class GO_Tasks_Controller_Tasklist extends GO_Base_Controller_AbstractModelController{
	
	protected $model = 'GO_Tasks_Model_Tasklist';
	
	protected function prepareGrid(GO_Base_Provider_Grid $grid){
    $grid->formatColumn('user_name','$model->user->name');
    return $grid;
  }
	
	protected function remoteComboFields(){
		return array(
				'user_name'=>'$model->user->name'
				);
	}
}