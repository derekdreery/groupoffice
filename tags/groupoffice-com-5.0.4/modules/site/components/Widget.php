<?php
abstract class GO_Site_Components_Widget extends GO_Base_Object {
	
	/**
	 * @var integer the counter for generating implicit IDs.
	 */
	private static $_counter=0;
	/**
	 * Id will be set automaticaly is setId() is never called
	 * @var string id of the widget.
	 */
	private $_id;
	
	public function __construct($config=array()) {
		$ref = new ReflectionClass($this);
		foreach($config as $key => $value)
			if($ref->getProperty ($key)->isPublic())
				$this->{$key}=$value;
				
		$this->init();
	}
	
	/**
	 * Overwrite this for initial widget setup before rendering anything
	 * Do not overwrite the constructor because it will lose it functionality
	 * to set the default option as a config array
	 */
	protected function init() {
		
	}
	
	/**
	 * Returns the ID of the widget or generates a new one if requested.
	 * @param boolean $autoGenerate whether to generate an ID if it is not set previously
	 * @return string id of the widget.
	 */
	public function getId()
	{
		if($this->_id!==null)
			return $this->_id;
		return $this->_id='go'.self::$_counter++;
	}
	
	/**
	 * Sets the ID of the widget.
	 * @param string $value id of the widget.
	 */
	public function setId($value)
	{
		$this->_id=$value;
	}
	
	/**
	 * The render function to render this widget 
	 */
	abstract public function render();
	
	public static function getAjaxResponse($params){
		return true;
	}
	
}