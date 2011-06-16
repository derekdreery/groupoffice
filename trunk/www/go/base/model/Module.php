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
	
	protected function afterLoad() {
		parent::afterLoad();
		
		$this->path = GO::config()->root_path.'modules/'.$this->id.'/';
		$this->full_url = GO::config()->full_url.'modules/'.$this->id.'/';
		$this->url = GO::config()->host.'modules/'.$this->id.'/';
		$this->class_path = $this->path.'classes/';
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
}