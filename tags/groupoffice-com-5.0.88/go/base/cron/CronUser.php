<?php
class GO_Base_Cron_CronUser extends GO_Base_Db_ActiveRecord {

	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}
	
	public function primaryKey() {
		return array('cronjob_id','user_id');
	}
	
	public function tableName() {
		return 'go_cron_users';
	}
	
	public function relations(){
		return array(	
			'cronjob' => array('type'=>self::MANY_MANY, 'model'=>'GO_Base_Cron_CronJob', 'field'=>'user_id', 'linkModel' => 'GO_Base_Model_User'),
    );
	}
}