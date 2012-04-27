<?php
/**
 * Component to keep track of the state of an import.
 * It keeps track on hou many items are imported and which items went wrong. 
 */
class GO_Base_Component_SummaryLog {
	
	/**
	 * The total items count
	 * 
	 * @var int 
	 */
	private $_total = 0;
	
	/**
	 * List of items that went wrong
	 * 
	 * @var array 
	 */
	private $_errors = array();
	
	/**
	 * Add count to totals
	 * 
	 * @param int $count 
	 */
	public function add($count=1){
		$this->_total = $this->_total+$count;
	}
	
	/**
	 * Get the totals
	 * 
	 * @return int
	 */
	public function getTotal(){
		return $this->_total;
	}
	
	/**
	 * Add an element with error to the error array
	 * 
	 * @param mixed $itemIdentifier
	 * @param string $message 
	 */
	public function addError($itemIdentifier, $message){
		$this->_errors[]= array('name'=>$itemIdentifier,'message'=>$message);
	}
	
	/**
	 * Get the error array
	 * 
	 * @return array 
	 */
	public function getErrors(){
		return $this->_errors;
	}
	
	/**
	 * Get the error array for the Json response
	 * 
	 * @return array 
	 */
	public function getErrorsJson(){
		$response = array();
		$response['summarylog']['total']=$this->_total;
		$response['summarylog']['errorCount']=count($this->_errors);
		$response['summarylog']['errors']=$this->_errors;
		return $response;
	}
}
?>
