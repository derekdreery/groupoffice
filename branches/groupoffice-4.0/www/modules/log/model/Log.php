<?php
class GO_Log_Model_Log extends GO_Base_Db_ActiveRecord {
	
	
	const ACTION_ADD='add';
	const ACTION_DELETE='delete';
	const ACTION_UPDATE='update';
	const ACTION_LOGIN='login';
	const ACTION_LOGOUT='logout';
	
	protected $insertDelayed=true;
	
	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Notes_Model_Note 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}
	
	public function tableName(){
		return 'go_log';
	}
	
	protected function init() {
		
		//$this->columns['time']='unixtimestamp';
		
		return parent::init();
	}
	
	public function defaultAttributes() {
		$attr = parent::defaultAttributes();
		$attr['user_agent']=PHP_SAPI=='cli' ? 'CLI' : $_SERVER['HTTP_USER_AGENT'];
		$attr['ip']=isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
		$attr['controller_route']=GO::router()->getControllerRoute();
		$attr['username']=GO::user() ? GO::user()->username : 'notloggedin';
		return $attr;
	}
}