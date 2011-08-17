<?php

class GO_Base_Model_Module extends GO_Base_Db_ActiveRecord {

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


	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Base_Model_Module 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}

	public function aclField() {
		return 'acl_id';
	}

	public function tableName() {
		return 'go_modules';
	}
	
	public function sessionCache(){
		return true;
	}

	protected function init() {
		parent::init();

		$this->path = GO::config()->root_path . 'modules/' . $this->id . '/';
		$this->full_url = GO::config()->full_url . 'modules/' . $this->id . '/';
		$this->url = GO::config()->host . 'modules/' . $this->id . '/';
		$this->class_path = $this->path . 'classes/';
	}

	protected function getHumanName() {
		return GO::t('name', $this->id);// isset($lang[$this->id]['name']) ? $lang[$this->id]['name'] : $this->id;
	}

	protected function getDescription() {
		return GO::t('description', $this->id);
	}

	/**
	 * Installs the module's tables etc
	 */
	protected function afterSave($wasNew) {
		return parent::afterSave($wasNew);
	}

	/**
	 * Delete's the module's tables etc.
	 */
	public function afterDelete() {
		return parent::afterDelete();
	}

	/**
	 * This class can be overriden by a module class to add listeners to objects
	 * that extend the GO_Base_Observable class.
	 */
	public static function initListeners() {
		
	}
	
	/**
	 * This function is called when the first request is made to the module.
	 * Useful to check for a default calendar, tasklist etc.
	 */
	public static function firstRun(){
		
	}
	
	/**
	 * This function is called when the search index needs to be rebuilt.
	 * 
	 * You want to use MyModel::model()->rebuildSearchCache();
	 * 
	 * @param array $response Array of output lines
	 */
	public function buildSearchCache(&$response){		
		
		$response[]  = "Building search cache for ".$this->getModule()."\n";		
				
		$models=$this->getModels();
		
		foreach($models as $model){
			echo $response[] = "Processing ".$model."\n";
			$stmt = call_user_func(array($model,'model'))->find(array(
					'ignoreAcl'=>true
			));
			$stmt->callOnEach('rebuildSearchCache');
		}
	}
	
	/**
	 * This function is called when a database check is performed
	 * 
	 * @param array $response Array of output lines
	 */
	public function checkDatabase(&$response){				
		
		//echo "<pre>";
		
		echo "Checking database for ".$this->id."\n";		
				
		$models=$this->getModels();
		
		
		foreach($models as $model){			
			echo "Processing ".$model."\n";
			flush();
			
			$m = call_user_func(array($model,'model'));
			
			$stmt = $m->find(array(
					'ignoreAcl'=>true
			));
			$stmt->callOnEach('save');
			
			
		}
	}
	
	/**
	 * Get all model class names.
	 * 
	 * @return Array Names of all model classes 
	 */
	public function getModels(){		
	
		$models=array();
		$folder = new GO_Base_Fs_Folder($this->path.'model');
		if($folder->exists()){
			$items = $folder->ls();
			
			foreach($items as $item){
				if($item instanceof GO_Base_Fs_File){
					$className = 'GO_'.ucfirst($this->id).'_Model_'.$item->nameWithoutExtension();
					
					$class = new ReflectionClass($className);
					if(!$class->isAbstract()){					
						$models[] = $className;
					}
				}
			}
		}
		
		return $models;
	}
}