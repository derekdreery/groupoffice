<?php
class GO_Base_Cron_CronGroup extends GO_Base_Db_ActiveRecord {

	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}
	
	public function primaryKey() {
		return array('cronjob_id','group_id');
	}
	
	public function tableName() {
		return 'go_cron_groups';
	}
	
	public function relations(){
		return array(	
			'cronjob' => array('type'=>self::MANY_MANY, 'model'=>'GO_Base_Cron_CronJob', 'field'=>'group_id', 'linkModel' => 'GO_Base_Model_Group'),
    );
	}
}