<?php
abstract class GO_Site_Components_Widget extends GO_Base_Object {
	
	/**
	 * @var integer the counter for generating implicit IDs.
	 */
	private static $_counter=0;
	/**
	 * @var string id of the widget.
	 */
	private $_id;
	
	public function __construct($config=array()) {
		$ref = new ReflectionClass(get_called_class());
		foreach($config as $key => $value)
			if($ref->getProperty ($key)->isPublic())
				$this->{$key}=$value;
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
	public function render(){
		
	}
	
	public static function getAjaxResponse($params){
		return true;
	}
	
}