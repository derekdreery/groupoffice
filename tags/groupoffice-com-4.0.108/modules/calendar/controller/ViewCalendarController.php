<?php

class GO_Calendar_Controller_ViewCalendar extends GO_Base_Controller_AbstractMultiSelectModelController {
	
	/**
	 * The name of the model we are showing and adding to the other model.
	 * 
	 * eg. When selecting calendars for a user in the sync settings this is set to GO_Calendar_Model_Calendar
	 */
	public function modelName() {
		return 'GO_Calendar_Model_Calendar';
	}
	
	/**
	 * Returns the name of the model that handles the MANY_MANY relation.
	 * @return String 
	 */
	public function linkModelName() {
		return 'GO_Calendar_Model_ViewCalendar';
	}
	
	/**
	 * The key (from the combined key) of the linkmodel that identifies the model as defined in self::modelName().
	 */
	public function linkModelField() {
		return 'calendar_id';
	}
	
	protected function formatColumns(GO_Base_Data_ColumnModel $cm) {
		$cm->formatColumn('username', '$model->user->username');
		return parent::formatColumns($cm);
	}
}