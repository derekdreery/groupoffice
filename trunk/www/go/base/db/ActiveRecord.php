<?php

class GO_Base_Db_ActiveRecord extends GO_Base_Observable{
	
	const BELONGS_TO=1;	
	const HAS_MANY=2;
	
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
	 * @var array relational rules.
	 */
	protected $relations;
	
	/**
	 * 
	 * @var string The database table name
	 */
	
	public $tableName;
	
	/**
	 * 
	 * @var int ACL to check for permissions.
	 */
	protected $aclField=false;
	
	protected $aclFieldJoin=false;
	
	private $_relatedCache;
	
	
	private $_attributes=array();
	
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
	public $primaryKey='id'; //TODO can also be array('user_id','group_id') for example.

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
	}
	
	/**
	 * Get's the primary key value. Can also be accessed with $model->pk.
	 * 
	 * @return mixed The primary key value 
	 */
	public function getPk(){
		if(isset($this->_attributes[$this->primaryKey]))
			return $this->_attributes[$this->primaryKey];
		else
			return null;
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
	
	private function _joinAclTable(){
		$arr = explode('.',$this->aclField);
		if(count($arr)==2){
			//we need to join a table for the acl field
			
			$model = new $this->relations[$arr[0]][1];
			
			$ret['relation']=$arr[0];
			$ret['aclField']=$arr[1];
			$ret['join']='INNER JOIN `'.$model->tableName.'` '.$ret['relation'].' ON ('.$ret['relation'].'.`'.$model->primaryKey.'`=t.`'.$this->relations[$arr[0]][2].'`) ';
			$ret['fields']='';
			
			$cols = $model->getColumns();
			
			foreach($cols as $field=>$props){
				$ret['fields'].=', '.$ret['relation'].'.`'.$field.'` AS `'.$ret['relation'].'@'.$field.'`';
			}
			$ret['table']=$ret['relation'];
			
		}else
		{
			return false;
		}
		
		return $ret;
	}
	
	private $_permissionLevel;
	
	
	/**
	 * Returns the permission level if an aclField is defined in the model. Otherwise
	 * it returns -1;
	 * 
	 * @return int GO_SECURITY::*_PERMISSION 
	 */
	
	public function getPermissionLevel(){
		
		if(empty($this->aclField))
			return -1;	
	
		if(!isset($this->_permissionLevel)){
			$arr = explode('.',$this->aclField);
			if(count($arr)==2){
				$relation = $arr[0];
				$aclField = $arr[1];
				$acl_id = $this->$relation->$aclField;
			}else
			{
				$acl_id = $this->{$this->aclField};
			}
			$this->_permissionLevel=GO::security()->hasPermission($acl_id);
		}
		return $this->_permissionLevel;
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
		
		$aclJoin['relation']='';
		$aclJoin['aclField']=$this->aclField;
		$aclJoin['table']='t';
		$aclJoin['join']='';
		$aclJoin['fields']='';
		
		if($this->aclField && empty($params['ignoreAcl'])){
			$ret = $this->_joinAclTable();
			if($ret)
				$aclJoin=$ret;
		}
		
		$sql = "SELECT t.*".$aclJoin['fields']." FROM `".$this->tableName."` t ".$aclJoin['join'];
		
		if($this->aclField && empty($params['ignoreAcl'])){			
			
			$sql .= "INNER JOIN go_acl ON (`".$aclJoin['table']."`.`".$aclJoin['aclField']."` = go_acl.acl_id";
			if(isset($params['permissionLevel']) && $params['permissionLevel']>GO_SECURITY::READ_PERMISSION){
				$sql .= " AND go_acl.level>=".intval($params['permissionLevel']);
			}
			$sql .= " AND (go_acl.user_id=".intval($params['userId'])." OR go_acl.group_id IN (".implode(',',GO::security()->get_user_group_ids($params['userId']))."))) ";
		}
		
		if(!empty($params['criteriaSql']))
			$sql .= $params['criteriaSql'];
		
		if(!empty($params['by'])){
			foreach($params['by'] as $arr){
				
				$field = $arr[0];
				$value= $arr[1];
				$op=isset($arr[2]) ? $arr[2] : '=';
								
				$sql .= "AND `$field` $op ".$this->getDbConnection()->quote($value)." ";
			}
		}

		
		if($this->aclField && empty($params['ignoreAcl'])){
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
		
		go_debug("AR:load($primaryKey)");
		
		$sql = "SELECT * FROM `".$this->tableName."` WHERE `".$this->primaryKey.'`='.intval($primaryKey);
		$result = $this->getDbConnection()->query($sql);
		
		$result->setFetchMode(PDO::FETCH_ASSOC);
				
		$this->setAttributes($result->fetch(), false);
		
		$this->isNew=false;
		
		$this->afterLoad();
		
		/**
		 * Useful event for modules. For example custom fields can be loaded or a files folder.
		 */
		GO::events()->fire_event('loadactiverecord',array(&$this));
	}
	
	/**
	 * May be overriden to do stuff after the model was loaded from the database
	 */
	protected function afterLoad(){
			//go_debug($this);
	}
	
		
	protected function getRelated($name){
		 //$name::findByPk($hit-s)
		if(!isset($this->relations[$name])){
			return false;			
		}
		
		$model = $this->relations[$name][1];
		
		/**
		 * Related stuff can be put in the relatedCache array for when a relation is
		 * accessed multiple times.
		 * 
		 * Related stuff can also be joined in a query and be passed to the __set 
		 * function as relation@relation_attribute. This array will be used here to
		 * construct the related model.
		 */
		if(isset($this->_relatedCache[$name])){			
			
			if(is_array($this->_relatedCache[$name])){
				$attr = $this->_relatedCache[$name];
				
				$this->_relatedCache[$name]=new $model;
				$this->_relatedCache[$name]->setAttributes($attr, false);				
			}
			
		}else
		{
			$joinAttribute = $this->relations[$name][2];
			$this->_relatedCache[$name]= new $model($this->_attributes[$joinAttribute]);
		}
		
		return $this->_relatedCache[$name];
	}
	
	private function _formatInputValues($attributes){
		$formatted = array();
		foreach($attributes as $key=>$value){
			if(!isset($this->_columns[$key])){
				//don't process unknown columns.
				continue;
			}
			if(!isset($this->_columns[$key]['gotype'])){
				$this->_columns[$key]['gotype']='string';
			}
			switch($this->_columns[$key]['gotype']){
				case 'unixtimestamp':
					$formatted[$key] = Date::to_unixtime($value);
					break;			

				default:
					$formatted[$key] = $value;
					break;
			}
			
		}
		return $formatted;
	}
	
	private function _formatOutputValues($attributes, $html=false){
		
		$formatted = array();
		foreach($attributes as $key=>$value){
			if(!isset($this->_columns[$key]['gotype'])){
				$this->_columns[$key]['gotype']='string';
			}
			switch($this->_columns[$key]['gotype']){
				case 'unixtimestamp':
					$formatted[$key] = Date::get_timestamp($value);
					break;	

				case 'textarea':
					if($html){
						$formatted[$key] = String::text_to_html($value);
					}else
					{
						$formatted[$key] = $value;
					}
					break;
				default:
					$formatted[$key] = $value;
					break;
			}
		}
		
		return $formatted;
	}
	
	/**
	 * This function is used to set attributes of this model from a controller.
	 * Input may be in regional format and the model will translate it to the
	 * database format.
	 * 
	 * @param array $attributes attributes to set on this object
	 */
	
	public function setAttributes($attributes, $format=true){		
		$related=array();
		
		if($format)
			$attributes = $this->_formatInputValues($attributes);
		
		foreach($attributes as $key=>$value){
			if(isset($this->_columns[$key])){
				$this->$key=$value;
			}		
		}		
	}
	
	/**
	 * Returns all column attribute values.
	 * Note, related objects are not returned.
	 * @param string $outputType Can be 
	 * 
	 * raw: return values as they are stored in the db
	 * formatted: return the values formatted for an input form
	 * html: Return the values formatted for HTML display
	 * 
	 * @return array attribute values indexed by attribute names.
	 */
	public function getAttributes($outputType='formatted')
	{
		if($outputType=='raw')
			return $this->_attributes;
		
		return $this->_formatOutputValues($this->_attributes, $outputType=='html');		
	}
	
	/**
	 * Returns all column attribute values.
	 * Note, related objects are not returned.
	 * @param string $outputType Can be 
	 * 
	 * raw: return values as they are stored in the db
	 * formatted: return the values formatted for an input form
	 * html: Return the values formatted for HTML display
	 * 
	 * @return array attribute values indexed by attribute names.
	 */
	public function getColumns()
	{
		return $this->_columns;
	}
	
	/**
	 * Checks all the permissions
	 * 
	 * @return boolean 
	 */
	private function _checkPermissionLevel($level){
		if(empty($this->aclField))
			return true;
		
		return $this->getPermissionLevel()>=$level;
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
		
		if(!$this->_checkPermissionLevel(GO_SECURITY::WRITE_PERMISSION))
			throw new AccessDeniedException();
		
		if($this->beforeSave() && $this->validate()){		
		
			/*
			 * Set some common column values
			*/
			
			if(isset($this->_columns['mtime']))
				$this->mtime=time();
			if(isset($this->_columns['ctime']) && !isset($this->ctime)){
				$this->ctime=time();
			}
			
			if(isset($this->_columns['user_id']) && !isset($this->user_id)){
				$this->user_id=GO::security()->user_id;
			}
			
			if($this->isNew){				
				
				if(strpos($this->aclField, '.')===false){
					//generate acl id
					$this->{$this->aclField}=GO::security()->get_new_acl($this->tableName);
				}				

				$this->_attributes[$this->primaryKey] = $this->_dbInsert();
				
				if(!$this->_attributes[$this->primaryKey])
					return false;
			}else
			{
				if(!$this->_dbUpdate())
					return false;
			}
			
			if(!$this->afterSave())
							return false;
			
			/**
			 * Useful event for modules. For example custom fields can be loaded or a files folder.
			 */
			$this->fireEvent('save',array(&$this));
			
			return true;
			
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
		if(isset($this->_attributes[$name])){
			return $this->_attributes[$name];
		}else{
			
			$getter = 'get'.ucfirst($name);
			
			if(method_exists($this,$getter)){
				return $this->$getter();
			}elseif(isset($this->relations[$name]))
			{
				return $this->getRelated($name);
			}
		}
			
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
		elseif(isset($this->_columns[$name]))
			$this->_attributes[$name]=$value;		
		else{
			$arr = explode('@',$name);
			if(count($arr)>1)
				$this->_relatedCache[$arr[0]][$arr[1]]=$value;				
		}	

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