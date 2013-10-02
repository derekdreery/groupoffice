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
 * @property double $mo_work_hours
 * @property double $tu_work_hours
 * @property double $we_work_hours
 * @property double $th_work_hours
 * @property double $fr_work_hours
 * @property double $sa_work_hours
 * @property double $su_work_hours
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
	
	public function getHoursForDay($time){
		
		switch(date('w', $time)){
			case 0:
				return $this->su_work_hours;
				break;
			case 1:
				return $this->mo_work_hours;
				break;
			case 2:
				return $this->tu_work_hours;
				break;
			case 3:
				return $this->we_work_hours;
				break;
			case 4:
				return $this->th_work_hours;
				break;
			case 5:
				return $this->fr_work_hours;
				break;
			case 6:
				return $this->sa_work_hours;
				break;
			
		}
		
	}

}