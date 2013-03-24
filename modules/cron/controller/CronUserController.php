<?php

class GO_Cron_Controller_CronUser extends GO_Base_Controller_AbstractMultiSelectModelController {
	
	/**
	 * The name of the model from where the MANY_MANY relation is called
	 * @return String 
	 */
	public function modelName() {
		return 'GO_Base_Model_User';
	}
	
	/**
	 * Returns the name of the model that handles the MANY_MANY relation.
	 * @return String 
	 */
	public function linkModelName() {
		return 'GO_Base_Cron_CronUser';
	}
	
	/**
	 * The name of the field in the linkModel where the key of the current model is defined.
	 * @return String
	 */
	public function linkModelField() {
		return 'user_id';
	}	
}