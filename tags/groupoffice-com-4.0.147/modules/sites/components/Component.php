<?php
class GO_Sites_Components_Component {
	/**
	 * The component id
	 * 
	 * @var string 
	 */
	protected $_id;
	
	/**
	 * The page to show this component on
	 * 
	 * @var GO_Sites_Model_Page 
	 */
	protected $_page;
	
	/**
	 * The controller that is used
	 * 
	 * @var GO_Sites_Controller_Site 
	 */
	protected $_controller;
	
	
	protected $_params;
	
	
	/**
	 * Default constructor for Html_Components
	 * 
	 * @param string $id 
	 */
	protected function __construct($id,GO_Sites_Controller_Site $controller,$params){
		$this->_id = $id;
		$this->_page = $controller->getPage();
		$this->_controller = $controller;
		$this->_params = $params;
		$this->_init();
	}
	
	/**
	 * The init function that will be called in the constructor. 
	 */
	protected function _init(){
		
	}
	
	
	/**
	 * The render function to render the component. 
	 */
	public function render(){
		
	}
	
	/**
	 * Get the components id
	 * 
	 * @return string  
	 */
	public function getId(){
		return $this->_id;
	}
	
	/**
	 * Set the components id
	 * 
	 * @param string $id 
	 */
	public function setId($id){
		$this->_id = $id;
	}
	
	/**
	 * Get the components page
	 * 
	 * @return string  
	 */
	public function getPage(){
		return $this->_page;
	}
	
	/**
	 * Set the components page
	 * 
	 * @param GO_Sites_Model_Page $page 
	 */
	public function setPage($page){
		$this->_page = $page;
	}
	
	/**
	 * Get the key=>value array of the requestparams that where given to the page.
	 * This function filters out the site_id and the path parameter.
	 * 
	 * @return array 
	 */
	public function getAdditionalParams(){
		if(!empty($this->_params['path']))
			unset($this->_params['path']);
		
		if(!empty($this->_params['site_id']))
			unset($this->_params['site_id']);
		
		// This one is filtered out because of an error in the usage of it. (The required parameter is an array and is not supposed to be an array)
		if(!empty($this->_params['required']))
			unset($this->_params['required']);
				
		return $this->_params;
	}
}