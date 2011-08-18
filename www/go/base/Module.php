<?php

class GO_Base_Module extends GO_Base_Observable {

	public function id() {
		
		$className = get_class($this);
		
		$arr = explode('_', $className);
		return strtolower($arr[1]);
	}
	
	public function path(){
		return GO::config()->root_path . 'modules/' . $this->id() . '/';
	}

	public function name() {
		return GO::t('name', $this->id());// isset($lang[$this->id]['name']) ? $lang[$this->id]['name'] : $this->id;
	}

	public function description() {
		return GO::t('description', $this->id());
	}
	
	public function author(){
		return '';
	}
	
	public function authorEmail(){
		return 'info@intermesh.nl';
	}
	
	public function copyright(){
		return 'Copyright Intermesh BV';
	}

	/**
	 * Installs the module's tables etc
	 */
	protected function install() {
		return true;
	}

	/**
	 * Delete's the module's tables etc.
	 */
	public function uninstall() {
		return true;
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
		
		echo "Checking database for ".$this->id()."\n";		
				
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
		$folder = new GO_Base_Fs_Folder($this->path().'model');
		if($folder->exists()){
			$items = $folder->ls();
			
			foreach($items as $item){
				if($item instanceof GO_Base_Fs_File){
					$className = 'GO_'.ucfirst($this->id()).'_Model_'.$item->nameWithoutExtension();
					
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