<?php
 class GO_Base_Model_Module extends GO_Base_Db_ActiveRecord{
	
	/**
	 *
	 * @var String The absolute filesystem path to this module 
	 */
	public $path;
	
	/**
	 *
	 * @var String The absolute URL to this module. This is autodetected or manually set in config.php.
	 */
	public $full_url;
	
	/**
	 *
	 * @var string The relative URL to this module. 
	 */
	public $url;
	
	/**
	 * @deprecated
	 * @var String the absolute path to the classes folder of this module.
	 */
	public $class_path;
	
	
		
	 
	public $aclField='acl_id';
	
	public $tableName='go_modules';

	protected $_columns=array(
		'id'=>array('type'=>PDO::PARAM_STR),
		'name'=>array('type'=>PDO::PARAM_STR,'required'=>true,'length'=>100),
		'version'=>array('type'=>PDO::PARAM_INT),
		'sort_order'=>array('type'=>PDO::PARAM_INT),
		'acl_id'=>array('type'=>PDO::PARAM_INT)
	);	
	
	protected function init() {
		parent::init();
		
		$this->path = $GLOBALS['GO_CONFIG']->root_path.'modules/'.$this->id.'/';
		$this->full_url = $GLOBALS['GO_CONFIG']->full_url.'modules/'.$this->id.'/';
		$this->url = $GLOBALS['GO_CONFIG']->host.'modules/'.$this->id.'/';
		$this->class_path = $this->path.'classes/';		
	}
	
	protected function getHumanName(){
		global $lang;
		
		$file = $GLOBALS['GO_LANGUAGE']->get_language_file($this->id);
		if($file)
			require($file);
		
		return isset($lang[$this->id]['name']) ? $lang[$this->id]['name'] : $this->id;
	}
	
	protected function getDescription(){
		global $lang;
		
		$file = $GLOBALS['GO_LANGUAGE']->get_language_file($this->id);
		if($file)
			require($file);
		
		return isset($lang[$this->id]['description']) ? $lang[$this->id]['description'] : "";
	}
	
	
	
	/**
	 * Installs the module's tables etc
	 */
	protected function afterSave(){
		return parent::afterSave();
	}
	
	/**
	 * Delete's the module's tables etc.
	 */	
	public function afterDelete(){
		return parent::afterDelete();
	}
	
	/**
	 * This class can be overriden by a module class to add listeners to objects
	 * that extend the GO_Base_Observable class.
	 */
	public function initListeners(){
		
	}
	
	/**
	 * 
	 * When a user is created, updated or logs in this function will be called.
	 * The function can check if the default calendar, addressbook, notebook etc.
	 * is created for this user.
	 * 
	 */
	public function initUser($userId){
		
	}
}