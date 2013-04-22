<?php
abstract class GO_Site_Components_Widget {
	
	/**
	 * @var integer the counter for generating implicit IDs.
	 */
	private static $_counter=0;
	/**
	 * @var string id of the widget.
	 */
	private $_id;
	
	public function __get($name) {
		$getter = 'get'.ucfirst($name);
			
		if(method_exists($this,$getter))
			return $this->$getter();
		else
			throw new Exception('Call for unexisting property: '. $name);
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
	public function render($return = false){
		
	}
}