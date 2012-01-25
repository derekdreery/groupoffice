<?php
class GO_Base_Html_Component {
	/**
	 * The component id
	 * 
	 * @var string 
	 */
	protected $_id;
	
	/**
	 * 
	 * 
	 * @var GO_Sites_Model_Page 
	 */
	protected $_page;
	
	/**
	 * Default constructor for Html_Components
	 * 
	 * @param string $id 
	 */
	protected function __construct($id,GO_Sites_Model_Page $page){
		$this->_id = $id;
		$this->_page = $page;
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
	
	
}