<?php
/**
 * 
 * Copyright Intermesh BV.
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @package GO.base.db
 * @version $Id: File.class.inc.php 7607 2011-06-15 09:17:42Z mschering $
 * @copyright Copyright Intermesh BV.
 * @author Merijn Schering <mschering@intermesh.nl> 
 */

/**
 * The parameters for GO_Base_Db_ActiveRecord::find() can be constructed with this class
 *
 * @package GO.base.db
 * @version $Id: File.class.inc.php 7607 2011-06-15 09:17:42Z mschering $
 * @copyright Copyright Intermesh BV.
 * @author Merijn Schering <mschering@intermesh.nl>  * 
 */

class GO_Base_Db_FindParams{
	
	private $_params=array();
	
	/**
	 * Get all the parameters in an array.
	 * 
	 * @return array  
	 */
	public function getParams(){
		return $this->_params;
	}
	
	/**
	 * Create a new instance of GO_Base_Db_FindParams
	 * 
	 * @return GO_Base_Db_FindParams 
	 */
	public static function newInstance(){
		return new self;
	}
	
	/**
	 * Merge this with another findParams object.
	 * 
	 * @param GO_Base_Db_FindParams $findParams 
	 * @return GO_Base_Db_FindParams 
	 */
	public function mergeWith($findParams){
		if(!is_array($findParams))
			$findParams = $findParams->getParams();
		
		
		if(isset($this->_params['criteriaObject']) && isset($findParams['criteriaObject'])){
			$this->_params['criteriaObject']->mergeWith($findParams['criteriaObject']);
			unset($findParams['criteriaObject']);
		}
		
		$this->_params = array_merge($this->_params, $findParams);
		return $this;
	}
	
	/**
	 * Set to true if you want to ignore ACL permissions.
	 * 
	 * @param boolean $value
	 * @return GO_Base_Db_FindParams 
	 */
	public function ignoreAcl($value=true){
		$this->_params['ignoreAcl']=$value;
		return $this;
	}
	
	/**
	 * Select other fields in combination with 'join'. 
	 * Remember the model table is aliased with 't'.  
	 * if you supply this and the fields do not contain t.* then 
	 * the system can't return full objects. Arrays will be fetched instead.
	 * 
	 * @param string $fields
	 * @return GO_Base_Db_FindParams 
	 */					
	public function select($fields='t.*'){
		$this->_params['fields']=$fields;
		return $this;
	}
	
	/**
	 * Insert a plain join SQL string
	 * 
	 * @param string $join
	 * @return GO_Base_Db_FindParams 
	 */
	public function join($join){
		$this->_params['join']=$fields;
		return $this;
	}
	
	/**
	 * Add a find criteria object to add where conditions
	 * 
	 * @param GO_Base_Db_FindCriteria $criteria
	 * @return GO_Base_Db_FindParams 
	 */
	public function criteria(GO_Base_Db_FindCriteria $criteria){
		$this->_params['criteriaObject']=$fields;
		return $this;
	}
	
	/**
	 * Make this query available for exports to CSV, PDF etc.
	 * It will be stored in the session so that 
	 * GO_Base_Controller_AbstractModelController can reuise the params.
	 * 
	 * @param boolean $value
	 * @return GO_Base_Db_FindParams 
	 */
	public function export($value=true){
		$this->_params['export']=$fields;
		return $this;
	}
	
	/**
	 * Execute a simple search query
	 * 
	 * @param string $query
	 * @param array $fields When you ommit this it will search all text fields
	 * @return GO_Base_Db_FindParams 
	 */
	public function searchQuery($query, $fields=array()){
		$this->_params['searchQuery']=$query;
		$this->_params['searchQueryFields']=$fields;
		
		return $this;
	}
	
	/**
	 * Join the custom fields table if it's available for the model.
	 * 
	 * @param boolean $value
	 * @return GO_Base_Db_FindParams 
	 */
	public function joinCustomFields($value=true){
		$this->_params['joinCustomFields']=$value;
		return $this;
	}
	
	/**
	 * Set tot true to return the number of foundRows in the statement (See class GO_Base_Db_ActiveStatement 
	 * 
	 * @param boolean $value
	 * @return GO_Base_Db_FindParams 
	 */
	public function calcFoundRows($value=true){
		$this->_params['calcFoundRows']=$value;
		return $this;
	}
	
	/**
	 * Set sort order
	 * 
	 * @param string/array $field or array('field1','field2') for multiple values
	 * @param string/array $direction 'ASC' or array('ASC','DESC') for multiple values
	 * @return GO_Base_Db_FindParams 
	 */
	public function order($field, $direction='ASC'){
		$this->_params['order']=$field;
		$this->_params['orderDirection']=$direction;
		
		return $this;
	}
	
	/**
	 * Adds a group by clause
	 * 
	 * @param string $field
	 * @return GO_Base_Db_FindParams 
	 */
	public function group($field){
		$this->_params['group']=$field;
		return $this;
	}
	
	/**
	 * Adds a having clause
	 * 
	 * @param string $field
	 * @return GO_Base_Db_FindParams 
	 */
	public function having($field){
		$this->_params['having']=$field;
		return $this;
	}
	
	/**
	 * Set to true to return a single model instead of a statement.
	 * 
	 * @param boolean $value
	 * @return GO_Base_Db_FindParams 
	 */
	public function single($value=true){
		$this->_params['single']=$value;
		return $this;
	}
	
	/**
	 * Join a model table on the query
	 * 
	 * @param array $config
	 * 
	 * array(
	 *			'model'=>'GO_Billing_Model_OrderStatusLanguage',					
	 *			'foreignField'=>'status_id', //defaults to primary key of the remote model
	 *			'localField'=>'id', //defaults to primary key of the model
	 *			'tableAlias'=>'l', //Optional table alias
	 *			'type'=>'INNER' //defaults to INNER
	 *			)
	 * 
	 * @return GO_Base_Db_FindParams 
	 */
	public function joinModel($config){
		$this->_params['joinModel']=$config;
		return $this;
	}
	
	
	/**
	 * Skip this number of items
	 * 
	 * @param int $start
	 * @return GO_Base_Db_FindParams 
	 */
	public function start($start=0){
		$this->_params['start']=$start;
		return $this;
	}
	
	/**
	 * Limit the number of models returned
	 * 
	 * @param int $limit
	 * @return GO_Base_Db_FindParams 
	 */
	public function limit($limit=0){
		$this->_params['limit']=$limit;
		return $this;
	}
	
	/**
	 * Only return rows that the user has this level of access to.
	 * 
	 * Note: this is ignored when you use ignoreAcl()
	 * 
	 * @param int $level See GO_Base_Model_Acl constants for available levels. It defaults to GO_Base_Model_Acl::READ_PERMISSION
	 * @param int $user_id Defaults to the currently logged in user
	 * @return GO_Base_Db_FindParams 
	 */
	public function permissionLevel($level, $user_id=false){
		$this->_params['permissionLevel']=$level;
		$this->_params['userId']=$user_id;
		
		return $this;
	}
	
	
}