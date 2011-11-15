<?php

class GO_Base_Data_Column {
	
	/**
	 *
	 * @var type 
	 */
	private $_dataindex;
	
	/**
	 *
	 * @var type 
	 */
	private $_label;
	
	/**
	 *
	 * @var type 
	 */
	private $_sortIndex;
	
	/**
	 *
	 * @var type 
	 */
	private $_format;
	
	/**
	 *
	 * @var type 
	 */
	private $_extraVars = array();
	
	/**
	 *
	 * @var type 
	 */
	private $_sortAlias;
	
	/**
	 *
	 * @var type 
	 */
	private $_modelFormatType;
	
	/**
	 *
	 * @param string $dataindex
	 * @param string $label
	 * @param int $sortIndex
	 * @return GO_Base_Data_Column 
	 */
	public static function newInstance($dataindex, $label='', $sortIndex=0){
		return new self($dataindex, $label, $sortIndex);
	}
	
	/**
	 *
	 * @param type $dataindex
	 * @param type $label
	 * @param type $sortIndex 
	 */
	public function __construct($dataindex, $label='', $sortIndex=0){
		$this->_dataindex = $dataindex;
		$this->_label = !empty($label) ? $label : $dataindex;
		$this->_sortIndex = $sortIndex;
	}
	
	/**
	 *
	 * @param type $type 
	 */
	public function setModelFormatType($type){
		$this->_modelFormatType=$type;
		
		
	}
	
	/**
	 *
	 * @return type 
	 */
	public function getSortIndex(){
		return $this->_sortIndex;
	}
	
	/**
	 *
	 * @return type 
	 */
	public function getSortColumn(){
		return isset($this->_sortAlias) ? $this->_sortAlias : $this->getDataIndex();
	}
	
	/**
	 *
	 * @param type $format
	 * @param type $extraVars
	 * @return GO_Base_Data_Column 
	 */
	public function setFormat($format, $extraVars=array()){
		$this->_format = $format;
		return $this;
	}
	
	/**
	 *
	 * @param type $sortAlias
	 * @return GO_Base_Data_Column 
	 */
	public function setSortAlias($sortAlias){
		$this->_sortAlias = $sortAlias;
		return $this;
	}
	
	/**
	 *
	 * @return type 
	 */
	public function getLabel(){
		return $this->_label;
	}
	
	/**
	 *
	 * @return type 
	 */
	public function getDataIndex(){
		return $this->_dataindex;
	}
	
	/**
	 *
	 * @param type $model
	 * @return string 
	 */
	public function render($model) {

		//$array = $model->getAttributes($this->_modelFormatType);

		/**
		 * The extract function makes the array keys available as variables in the current function scope.
		 * we need this for the eval functoion.
		 * 
		 * example $array = array('name'=>'Pete'); becomes $name='Pete';
		 * 
		 * In the column definition we can supply a format like this:
		 * 
		 * 'format'=>'$name'
		 */
		//extract($array);

		extract($this->_extraVars);

		if (isset($this->_format)) {
			$result = '';
			eval('$result=' . $this->_format . ';');
			return $result;
		} elseif (isset($model->{$this->_dataindex})) {
			return $model->getAttribute($this->_dataindex,$this->_modelFormatType);
		} else {
			return "";
		}
	}
	
}