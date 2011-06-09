<?php

class GO_Base_Db_ActiveRecord {
	
	/**
	 * The database connection of this record
	 * 
	 * @var GO_Database  
	 */
	public static $db;
	
	/**
	 *
	 * @var int Link type of this Model used for the link system. See also the linkTo function
	 */
	protected $link_type=0;
	
	/**
	 *
	 * @var boolean Is this model new? 
	 */
	public $isNew = true;
	
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
//		return array(
//				'category' => array(self::BELONGS_TO, 'Category', 'category_id')
//		);
		
		return array();
	}
	
	/**
	 * 
	 * @var string The database table name
	 */
	
	protected $tableName;
	
	/**
	 * 
	 * @var int ACL to check for permissions.
	 */
	protected $aclField=false;
	
	
	private $_attributes;
	
	/**
	 *
	 * @var array Holds all the column properties indexed by the field name.
	 * 
	 * eg: 'id'=>array('type'=>PDO::PARAM_INT,'required'=>true,'length'=><max length of the value>, 'validator'=><a function to call to validate the value>)
	 * 
	 * The validator looks like this:
	 * 
	 * function validate ($value){
			return true;
		}
	 */
	protected $_columns=array(
				'id'=>array('type'=>PDO::PARAM_INT,'required'=>true,'length'=>null, 'validator'=>null)
			);
	
	/**
	 * 
	 * 
	 * @var mixed Primary key of database table. Can be a field name string or an array of fieldnames
	 */
	protected $primaryKey='id'; //TODO can also be array('user_id','group_id') for example.
	
	public $pk;
	
	/**
	 * Constructor for the model
	 * 
	 * @param int $primaryKey integer The primary key of the database table
	 */
	public function __construct($primaryKey=0){			
		if($primaryKey!=0)
			$this->load($primaryKey);
		else
			$this->afterLoad();
		
		if(isset($this->_attributes[$this->primaryKey]))
				$this->pk=$this->_attributes[$this->primaryKey];
	}

	
	/**
	 * Returns the database connection used by active record.
	 * By default, the "db" application component is used as the database connection.
	 * You may override this method if you want to use a different database connection.
	 * @return CDbConnection the database connection used by active record.
	 */
	public function getDbConnection()
	{
		if(self::$db!==null)
			return self::$db;
		else
		{
			self::$db=GO::getDbConnection();			
		}
		return self::$db;
	}

	
	public static function findByAttribute($attribute, $value){
		$sql = "SELECT * FROM `".$this->tableName."` WHERE `".$attribute.'`='.$this->db->escape($value);
		$this->_db->query($sql);
		
		return new GO_Model_Iterator($this);
	}

	/**
	 * Finds model objects 
	 * 
	 * @param array $params
	 * @return PDOStatement
	 */
	public function find($params=array()){
		
		//todo joins acl tables and finds stuff by parameters
		
		if(!isset($params['userId'])){
			$params['userId']=GO::security()->user_id;
		}
		
		$sql = "SELECT t.* FROM `".$this->tableName."` t ";
		
		if($this->aclField){
			$sql .= "INNER JOIN go_acl ON (t.`".$this->aclField."` = go_acl.acl_id";
			if(isset($params['permissionLevel']) && $params['permissionLevel']>GO_SECURITY::READ_PERMISSION){
				$sql .= " AND go_acl.level>=".intval($params['permissionLevel']);
			}
			$sql .= " AND (go_acl.user_id=".intval($params['userId'])." OR go_acl.group_id IN (".implode(',',GO::security()->get_user_group_ids($params['userId']))."))) ";
		}
		
		if($this->aclField){
			$sql .= "GROUP BY `".$this->primaryKey."` ";
		}
		
		if(isset($params['orderField'])){
			$sql .= 'ORDER BY `'.$params['orderField'].'`' ;
			if(isset($params['orderDirection'])){
				$sql .= $params['orderDirection'].' ';
			}
		}
		
		//$sql .= "WHERE `".$this->primaryKey.'`='.intval($primaryKey);
		$result = $this->getDbConnection()->query($sql);
		
		$result->setFetchMode(PDO::FETCH_CLASS, $this->className());
		
		return $result;
		
	}
	
	/**
	 * Returns the name of this ActiveRecord derrived class
	 * 
	 * @return string Name
	 */
	public function className(){
		return get_class($this);
	}
	
	/**
	 * Loads the model attributes from the database
	 * 
	 * @param int $primaryKey 
	 */
	
	protected function load($primaryKey){
		$sql = "SELECT * FROM `".$this->tableName."` WHERE `".$this->primaryKey.'`='.intval($primaryKey);
		$result = $this->getDbConnection()->query($sql);
		
		$result->setFetchMode(PDO::FETCH_ASSOC);
				
		$this->setAttributes($result->fetch());
		
		$this->isNew=false;
		
		$this->afterLoad();
		
	}
	
	/**
	 * May be overriden to do stuff after the model was loaded from the database
	 */
	protected function afterLoad(){
		
	}
	
		
	protected function getRelated($name){
		 //$name::findByPk($hit-s)
	}
	
	public function setAttributes($attr){
		foreach($attr as $key=>$value){
			$this->$key=$value;
		}
	}
	
	/**
	 * Returns all column attribute values.
	 * Note, related objects are not returned.
	 * @param mixed $names names of attributes whose value needs to be returned.
	 * If this is true (default), then all attribute values will be returned, including
	 * those that are not loaded from DB (null will be returned for those attributes).
	 * If this is null, all attributes except those that are not loaded from DB will be returned.
	 * @return array attribute values indexed by attribute names.
	 */
	public function getAttributes()
	{
		return $this->_attributes;		
	}
	
	/**
	 * Checks all the permissions
	 * 
	 * @return boolean 
	 */
	private function _checkPermissions(){
		return true;
	}
	
	
	
	public function validate(){
		foreach($this->_columns as $field=>$attributes){
			if(!empty($attributes['required']) && !isset($this->_attributes[$field])){
				throw new Exception($field.' is required');
			}elseif(!empty($attributes['length']) && strlen($this->_attributes[$field])>$attributes['length'])
			{
				throw new Exception($field.' too long');
			}elseif(!empty($attributes['validator']) && !call_user_func($attributes['validator'], $this->_attributes[$field]))
			{
				throw new Exception($field.' did not validate');
			}
		}
		
		return true;
	}
	
	
	/**
	 * Saves the model to the database
	 * 
	 * @return boolean 
	 */
	
	public function save(){
		
		if($this->_checkPermissions() && $this->beforeSave() && $this->validate()){		
		
			/*
			 * Set some common column values
			*/
			
			if(isset($this->_columns['mtime']))
				$this->mtime=time();
			if(isset($this->_columns['ctime']) && !isset($this->ctime)){
				$this->ctime=time();
			}
			
			if($this->isNew){

				$this->_attributes[$this->primaryKey] = $this->_dbInsert();
				
				if(!$this->_attributes[$this->primaryKey])
					return false;
			}else
			{
				if(!$this->_dbUpdate())
					return false;
			}
			
			return $this->afterSave();
			
		}else
		{
			return false;
		}
	}
	
	protected function beforeSave(){
		
		return true;
	}
	
	/**
	 * May be overridden to do stuff after save
	 * 
	 * @return boolean 
	 */
	protected function afterSave(){
		return true;
	}
	
	/**
	 * Inserts the model into the database
	 * 
	 * @return boolean 
	 */
	private function _dbInsert(){		
		
		$fieldNames = array_keys($this->_columns);
		
		$sql = "INSERT INTO `{$this->tableName}` (`".implode('`,`', $fieldNames)."`) VALUES ".
					"(:".implode(',:', $fieldNames).")";
		
		$stmt = $this->getDbConnection()->prepare($sql);
		
		foreach($fieldNames as  $field){
			
			$attr = $this->_columns[$field];
			
			$stmt->bindParam(':'.$field, $this->_attributes[$field], $attr['type'], empty($attr['length']) ? null : $attr['length']);
		}
		$stmt->execute();
		
		return $this->getDbConnection()->lastInsertId();
		
	}
	
	private function _dbUpdate(){
		
		$updates=array();
		foreach($this->_columns as $field => $value)
		{
			if($field!=$this->primaryKey)
			{
				$updates[] = "`$field`=:".$field;
			}
		}
		
		$sql = "UPDATE `{$this->tableName}` SET ".implode(',',$updates)." WHERE `".$this->primaryKey."`=:id";	  		

		
		$stmt = $this->getDbConnection()->prepare($sql);
		
		foreach($this->_columns as $field => $value){
			
			$attr = $this->_columns[$field];
			
			$stmt->bindParam(':'.$field, $this->_attributes[$field], $attr['type'], empty($attr['length']) ? null : $attr['length']);
		}
		return $stmt->execute();
		
	}
	
	public function delete(){
		
	}
	
	/**
	 * PHP getter magic method.
	 * This method is overridden so that AR attributes can be accessed like properties.
	 * @param string $name property name
	 * @return mixed property value
	 * @see getAttribute
	 */
	public function __get($name)
	{
		return $this->_attributes[$name];
	}

	/**
	 * PHP setter magic method.
	 * This method is overridden so that AR attributes can be accessed like properties.
	 * @param string $name property name
	 * @param mixed $value property value
	 */
	public function __set($name,$value)
	{
		$this->setAttribute($name,$value);
	}
	
	/**
	 * Sets the named attribute value.
	 * You may also use $this->AttributeName to set the attribute value.
	 * @param string $name the attribute name
	 * @param mixed $value the attribute value.
	 * @return boolean whether the attribute exists and the assignment is conducted successfully
	 * @see hasAttribute
	 */
	public function setAttribute($name,$value)
	{
		if(property_exists($this,$name))
			$this->$name=$value;
		else
			$this->_attributes[$name]=$value;

		return true;
	}
	
	
	/**
	 * Pass another model to this function and they will be linked with the
	 * Group-Office link system.
	 * 
	 * @param mixed $model 
	 */
	
	public function linkTo($model){
		
	}

}