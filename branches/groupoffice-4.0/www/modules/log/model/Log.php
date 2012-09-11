<?php
/**
 * Group-Office
 * 
 * Copyright Intermesh BV. 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @license AGPL/Proprietary http://www.group-office.com/LICENSE.TXT
 * @link http://www.group-office.com
 * @package GO.modules.log.model
 * @version $Id: example.php 7607 20120101Z <<USERNAME>> $
 * @copyright Copyright Intermesh BV.
 * @author <<FIRST_NAME>> <<LAST_NAME>> <<EMAIL>>@intermesh.nl
 */
 
/**
 * The GO_Log_Model_Log model
 *
 * @package GO.modules.log.model
 * @property int $id
 * @property int $user_id
 * @property string $username
 * @property string $model_id
 * @property int $ctime
 * @property string $user_agent
 * @property string $ip
 * @property string $controller_route
 * @property string $action
 * @property string $message
 */

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
	
	public function validate() {
		
		$this->cutAttributeLengths();
			
		return parent::validate();
	}
	
	public function defaultAttributes() {
		$attr = parent::defaultAttributes();
		if(PHP_SAPI=='cli')
			$attr['user_agent']='cli';
		else
			$attr['user_agent']= isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'unknown';
		$attr['ip']=isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
		$attr['controller_route']=GO::router()->getControllerRoute();
		$attr['username']=GO::user() ? GO::user()->username : 'notloggedin';
		return $attr;
	}
	
	/**
	 * Log a custom message
	 * 
	 * @param string $action eg update, save
	 * @param string $message 
	 */
	public static function create($action, $message){
		$log = new GO_Log_Model_Log();
		
		$log->model_id=0;

		$log->action=$action;
		$log->model="";			
		$log->message = $message;
		$log->save();
	}
}