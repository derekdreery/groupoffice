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
 * @property int $user_id
 * @property int $mo_work_hours
 * @property int $tu_work_hours
 * @property int $we_work_hours
 * @property int $th_work_hours
 * @property int $fr_work_hours
 * @property int $sa_work_hours
 * @property int $su_work_hours
 */

class GO_Base_Model_WorkingWeek extends GO_Base_Db_ActiveRecord {
	
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}
	
	public function primaryKey() {
		return 'user_id';
	}
	
	protected function getLocalizedName() {
		return GO::t('workingWeek');
	}

	public function tableName() {
		return 'go_working_weeks';
	}

}