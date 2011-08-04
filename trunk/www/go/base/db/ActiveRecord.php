<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: File.class.inc.php 7607 2011-06-15 09:17:42Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */


/**
 * 
 * All Group-Office models should extend this ActiveRecord class.
 * 
 * @property bool $joinAclField
 */

abstract class GO_Base_Db_ActiveRecord extends GO_Base_Observable{
	
	/**
	 * This relation is used when the remote model's primary key is stored in a 
	 * local attribute.
	 * 
	 * Addressbook->user() for example
	 */
	const BELONGS_TO=1;	
	
	/**
	 * This relation type is used when this model has many related models. 
	 * 
	 * Addressbook->contacts() for example.
	 */
	const HAS_MANY=2;
	
	/**
	 * This relation type means that the relation is single and this model's primary
	 * key can be found in the remote model.
	 * 
	 * User->Addressbook for example where user_id is in the addressbook table.
	 */
	const HAS_ONE=3;
	
	/**
	 * The database connection of this record
	 * 
	 * @var PDO  
	 */
	public static $db;
	
	/**
	 *
	 * @var int Link type of this Model used for the link system. See also the linkTo function
	 */
	public function linkType(){
		return false;
	}
	
	
	/**
	 * 
	 * Define the relations for the model.
	 * 
	 * Example return value:
	 * array(
				'contacts' => array('type'=>self::HAS_MANY, 'model'=>'GO_Addressbook_Model_Contact', 'field'=>'addressbook_id', 'delete'=>true //with this enabled the relation will be deleted along with the model),
				'companies' => array('type'=>self::HAS_MANY, 'model'=>'GO_Addressbook_Model_Company', 'field'=>'addressbook_id', 'delete'=>true),
				'user' => array('type'=>self::BELONGS_TO, 'model'=>'GO_Base_Model_User', 'field'=>'user_id')
		);
	 * 
	 * The relations can be accessed as functions:
	 * 
	 * Model->contacts() for example. They always return a PDO statement
	 * 
	 * If you have a "user_id" field, an automatic relation model->user() is created that 
	 * returns a GO_Base_Model_User.
	 * 
	 * @return array relational rules.
	 */
	public function relations(){
		return array();
	}
	
	/**
	 * This is defined as a function because it's a only property that can be set
	 * by child classes.
	 * 
	 * @var string The database table name
	 */
	
	public function tableName(){
		return false;
	}
	
	/**
	 * 
	 * @return int ACL to check for permissions.
	 */
	public function aclField(){
		return false;
	}
	
		
	/**
	 * 
	 * Returns the primary key of the database table of this model
	 * 
	 * @var mixed Primary key of database table. Can be a field name string or an array of fieldnames
	 */
		
	public function primaryKey()
	{
		return 'id';
	}
	
	private $_relatedCache;
	
	
	private static $_models=array();			// class name => model
	
	
	private $_attributes=array();
	
	private $_oldAttributes=array();
	
	private $_debugSql=true;
	
	
	/**
	 * Set to true to enable a files module folder for this item. A files_folder_id
	 * column in the database is required. You will probably 
	 * need to override buildFilesPath() to make it work properly.
	 * 
	 * @return bool 
	 */
	public function hasFiles(){return false;}
	
	/**
	 * Set to a model to enabled custom fields. A relation customfieldsRecord will be
	 * created automatically and saving and deleting custom fields will be handled.
	 * 
	 * @return bool 
	 */
	public function customfieldsModel(){return false;}

	/**
	 *
	 * @return <type> Call $model->joinAclField to check if the aclfield is joined.
	 */
	private function getJoinAclField (){
		return strpos($this->aclField(),'.')!==false;
	}
	
	/**
	 * The columns array is loaded automatically. Validator rules can be added by
	 * overriding the init() method.
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
	protected $columns=array(
				'id'=>array('type'=>PDO::PARAM_INT,'required'=>true,'length'=>null, 'validator'=>null)
			);	
	
	private $_new=true;

	/**
	 * Constructor for the model
	 * 
	 * @param int $primaryKey integer The primary key of the database table
	 */
	public function __construct(){			
		
		$pk = $this->pk;
		
		$this->_setOldAttributes();		
		
		$this->_loadColumns();
		$this->setIsNew(empty($pk));
		$this->init();
	}
	
	/**
	 * Loads the column information from the database
	 * 
	 * @todo cache this
	 */
	private function _loadColumns() {
		if($this->tableName()){
			$sql = "SHOW COLUMNS FROM `" . $this->tableName() . "`;";
			$stmt = $this->getDbConnection()->query($sql);
			while ($field = $stmt->fetch()) {
				preg_match('/([a-zA-Z].*)\(([1-9].*)\)/', $field['Type'], $matches);
				if ($matches) {
					$length = $matches[2];
					$type = $matches[1];
				} else {
					$type = $field['Type'];
					$length = 0;
				}

				$gotype = 'textfield';
				$required=false;

				$pdoType = PDO::PARAM_STR;
				switch ($type) {
					case 'int':
					case 'tinyint':
					case 'bigint':
						$pdoType = PDO::PARAM_INT;
						$length = 0;
						$gotype = '';
						break;

					case 'text':
						$gotype = 'textarea';
						break;
				}

				switch($field['Field']){
					case 'ctime':
					case 'mtime':
						$gotype = 'unixtimestamp';			
						break;
					case 'name':
						$required=true;
						break;
				}

				$this->columns[$field['Field']]=array(
						'type'=>$pdoType,
						'required'=>$required,
						'length'=>$length,
						'gotype'=>$gotype
				);
			}
		}
	}
	
	/**
	 * Save original attributes so we can check the modifications later.
	 */
	private function _setOldAttributes(){
		$this->_oldAttributes = $this->_attributes;
	}
	
	
	/**
	 * Get's the attribute value of this model as it is saved in the database.
	 * 
	 * @param string $name Attribute name
	 * @return string Database value
	 */
	public function getOldAttribute($name){
		return $this->_oldAttributes[$name];
	}
	
	
	/**
	 * Returns the static model of the specified AR class.
	 * @return GO_Base_Db_ActiveRecord the static model class
	 */
	public static function model()
	{
		$className=  get_called_class();
		
		if(isset(self::$_models[$className]))
			return self::$_models[$className];
		else
		{
			$model=self::$_models[$className]=new $className(null);
			return $model;
		}
	}
	
	/**
	 * Can be overriden to initialize the model. Useful for setting attribute
	 * validators in the columns property for example.
	 */
	protected function init(){}
	
	/**
	 * Get's the primary key value. Can also be accessed with $model->pk.
	 * 
	 * @return mixed The primary key value 
	 */
	public function getPk(){
		
		$ret = null;
		
		if(is_array($this->primaryKey())){
			foreach($this->primaryKey() as $field){
				if(isset($this->_attributes[$field])){
					$ret[$field]=$this->_attributes[$field];
				}
			}
		}elseif(isset($this->_attributes[$this->primaryKey()]))
			$ret =  $this->_attributes[$this->primaryKey()];
		
		return $ret;
	}
	
	/**
	 * Check if this model is new and not stored in the database yet.
	 * 
	 * @return bool 
	 */
	public function getIsNew(){
		
		return $this->_new;
	}
	
	/**
	 * Set if this model is new and not stored in the database yet.
	 * 
	 * @param bool $new 
	 */
	public function setIsNew($new){
		
		$this->_new=$new;
	}

	
	/**
	 * Returns the database connection used by active record.
	 * By default, the "db" application component is used as the database connection.
	 * You may override this method if you want to use a different database connection.
	 * @return PDO the database connection used by active record.
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
		$arr = explode('.',$this->aclField());
		if(count($arr)==2){
			//we need to join a table for the acl field
			$r= $this->relations();
			$model = new $r[$arr[0]]['model'];
			
			$ret['relation']=$arr[0];
			$ret['aclField']=$arr[1];
			$ret['join']='INNER JOIN `'.$model->tableName.'` '.$ret['relation'].' ON ('.$ret['relation'].'.`'.$model->primaryKey.'`=t.`'.$r[$arr[0]]['field'].'`) ';
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
	
	/**
	 * Makes an attribute unique in the table by adding a number behind the name.
	 * eg. Name becomes Name (1) if it already exists.
	 * 
	 * @param String $attributeName 
	 */
	public function makeAttributeUnique($attributeName){
		$x = 1;
		
		$value = $this->$attributeName;
		
		while($this->findSingleByAttribute($attributeName, $value))
		{			
			$value = $value.' ('.$x.')';
			$x++;
		}
		$this->$attributeName=$value;
	}
	
	private $_permissionLevel;
	
	private $_acl_id;
	
	/**
	 * Find the acl_id integer value that applies to this model.
	 * 
	 * @return int ACL id from go_acl_items table. 
	 */
	public function findAclId() {
		if (!$this->aclField())
			return false;
		
		if(!isset($this->_acl_id)){
			$arr = explode('.', $this->aclField());
			if (count($arr) == 2) {
				$relation = $arr[0];
				$aclField = $arr[1];
				$this->_acl_id = $this->$relation->$aclField;
			} else {
				$this->_acl_id = $this->{$this->aclField()};
			}
		}
		
		return $this->_acl_id;		
	}

	/**
	 * Returns the permission level if an aclField is defined in the model. Otherwise
	 * it returns -1;
	 * 
	 * @return int GO_Base_Model_Acl::*_PERMISSION 
	 */
	
	public function getPermissionLevel(){
		
		if(!$this->aclField())
			return -1;	
		
		if($this->isNew && !$this->joinAclField){
			//the new model has it's own ACL but it's not created yet.
			//In this case we will check the module permissions.
			$module = $this->getModule();
			if($module=='base')
				return GO::user()->isAdmin() ? GO_Base_Model_Acl::MANAGE_PERMISSION : false;
			else
				return GO::modules()->$module->permissionLevel;
			 
		}else
		{		
			if(!isset($this->_permissionLevel)){

				$acl_id = $this->findAclId();
				if(!$acl_id){
					throw new Exception("Could not find ACL for ".$this->className()." with pk: ".$this->pk);
				}

				$this->_permissionLevel=GO_Base_Model_Acl::model()->findByPk($acl_id)->getUserPermissionLevel();
			}
			return $this->_permissionLevel;
		}
		
	}
	
	/**
	 * Returns an unique ID string for a find query. That is used to store the 
	 * total number of rows in session. This way we don't need to calculate the 
	 * total on each pagination page when limit 0,n is used.
	 * 
	 * @param array $params
	 * @return string  
	 */
	private function _getFindQueryUid($params){
		//create unique query id
		unset($params['start']);
		return md5(serialize($params).$this->className());
	}
	
	/**
	 * Finds a single model by an attribute name and value.
	 * 
	 * @param string $attributeName
	 * @param mixed $value
	 * @param array $findParams Extra parameters to send to the find function.
	 * @return GO_Base_Db_ActiveRecord 
	 */
	public function findSingleByAttribute($attributeName, $value, $findParams=array()){
		
		$params = array_merge(array(
				"by"=>array(array($attributeName,$value,'=')),
				"ignoreAcl"=>true,
				"limit"=>1
		), $findParams);		
				
		$stmt = $this->find($params);
		
		$model = $stmt->fetch();
		
		return $model;		
	}
	

	/**
	 * Finds model objects
	 *
	 *
	 * params=array(
	 *	"by"=> array(array('field','value','=')),
	 *  "byOperator"=>[AND / OR]
	 *
	 *
	 * TODO:
	 *	"byGroups"=> array(
	 *			array('operator=>'AND','criteria'=>array('field','value','=')),
	 *			array('operator=>'OR','criteria'=>array('field','value','='))
	 *	)
	 *  "ignoreAcl"=>true,
	 * 
	 *  searchQuery=>"String",
	 *  joinCustomFields=>false
	 * };
	 * 
	 * 
	 * @param array $params
	 * @return PDOStatement
	 */
	public function find($params=array(), &$foundRows=false){
		
		//todo joins acl tables and finds stuff by parameters
		
		
		
		if(!isset($params['userId'])){			
			$params['userId']=GO::user() ? GO::user()->id : 1;
		}
		
		$aclJoin['relation']='';
		$aclJoin['aclField']=$this->aclField();
		$aclJoin['table']='t';
		$aclJoin['join']='';
		$aclJoin['fields']='';
		
		if($this->aclField() && empty($params['ignoreAcl'])){
			$ret = $this->_joinAclTable();
			if($ret)
				$aclJoin=$ret;
		}
		
		$sql = "SELECT ";
		
		if($foundRows!==false && !empty($params['limit']) && empty($params['start'])){
			
			//TODO: This is MySQL only code
			
			$sql .= "SQL_CALC_FOUND_ROWS ";
		}
		
		$sql .= "t.*".$aclJoin['fields'].' ';
		
		
		$joinCf = !empty($params['joinCustomFields']) && $this->linkType()>0 && GO::modules()->customfields->permissionLevel;
		
		if($joinCf)			
			$sql .= ",cf_".$this->linkType().".* ";
		
		
		$sql .= "FROM `".$this->tableName()."` t ".$aclJoin['join'];
		
		if($joinCf)			
			$sql .= "LEFT JOIN cf_".$this->linkType()." ON cf_".$this->linkType().".link_id=t.id ";
		
		if($this->aclField() && empty($params['ignoreAcl'])){			
			
			$sql .= "INNER JOIN go_acl ON (`".$aclJoin['table']."`.`".$aclJoin['aclField']."` = go_acl.acl_id";
			if(isset($params['permissionLevel']) && $params['permissionLevel']>GO_Base_Model_Acl::READ_PERMISSION){
				$sql .= " AND go_acl.level>=".intval($params['permissionLevel']);
			}
			$sql .= " AND (go_acl.user_id=".intval($params['userId'])." OR go_acl.group_id IN (".implode(',',GO_Base_Model_User::getGroupIds($params['userId']))."))) ";
		}  else {
			//quick and dirty way to use and in next sql build blocks
			$sql .= 'WHERE 1 ';
		}
		
		if(!empty($params['criteriaSql']))
			$sql .= $params['criteriaSql'];
		
		if(!empty($params['by'])){

			if(!isset($params['byOperator']))
				$params['byOperator']='AND';

			$first=true;
			$sql .= 'AND (';
			foreach($params['by'] as $arr){
				
				$field = $arr[0];
				$value= $arr[1];
				$comparator=isset($arr[2]) ? $arr[2] : '=';

				if($first)
				{
					$first=false;
				}else
				{
					$sql .= $params['byOperator'].' ';
				}
				
				if($comparator=='IN'){
					for($i=0;$i<count($value);$i++)
						$value[$i]=$this->getDbConnection()->quote($value[$i], $this->columns[$field]['type']);
					
					$sql .= "`$field` $comparator (".implode(',',$value).") ";
				}else
				{							
					$sql .= "`$field` $comparator ".$this->getDbConnection()->quote($value, $this->columns[$field]['type'])." ";
				}
			}

			$sql .= ') ';
		}

		
		if(!empty($params['searchQuery'])){
			$sql .= ' AND (';
			
			$fields = $this->getFindSearchQueryParamFields();
			
			//`name` LIKE "test" OR `content` LIKE "test"
			
			$first = true;
			foreach($fields as $field){
				if($first){
					$first=false;
				}else
				{
					$sql .= ' OR ';
				}
				$sql .= 't.`'.$field.'` LIKE '.$this->getDbConnection()->quote($params['searchQuery'], PDO::PARAM_STR);
			}			
			
			
			$sql .= ') ';
		}
		
		
		if($this->aclField() && empty($params['ignoreAcl'])){
			
			$pk = is_array($this->primaryKey()) ? $this->primaryKey() : array($this->primaryKey());
			
			$sql .= "GROUP BY `".implode('`,`', $pk)."` ";
		}
		
		if(!empty($params['orderField'])){
			$sql .= 'ORDER BY `'.$params['orderField'].'`' ;
			if(!empty($params['orderDirection'])){
				$sql .= $params['orderDirection'].' ';
			}
		}
		
		if(!empty($params['limit'])){
			if(!isset($params['start']))
				$params['start']=0;
			
			$sql .= 'LIMIT '.intval($params['start']).','.intval($params['limit']);
		}
		
		if($this->_debugSql)
				GO::debug($sql);

		//$sql .= "WHERE `".$this->primaryKey().'`='.intval($primaryKey);
		$result = $this->getDbConnection()->query($sql);
		
		
		if($foundRows!==false){			
			if(!empty($params['limit'])){
				
				$queryUid = $this->_getFindQueryUid($params);
				
				//Total numbers are cached in session when browsing through pages.
				if(empty($params['start'])){
					//TODO: This is MySQL only code
					$sql = "SELECT FOUND_ROWS() as found;";			
					$r2 = $this->getDbConnection()->query($sql);
					$record = $r2->fetch(PDO::FETCH_ASSOC);
					$foundRows = GO::session()->values[$queryUid]=intval($record['found']);	
				}else
				{
					$foundRows=GO::session()->values[$queryUid];
				}
						
			}else
			{
				$foundRows = $result->rowCount();
			}			
		}
		
		
		//$result->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $this->className());
		$result->setFetchMode(PDO::FETCH_CLASS, $this->className());
		
		return $result;
		
	}
	
	/**
	 * Override this method to supply the fields that the searchQuery argument 
	 * will usein the find function.
	 * 
	 * By default all fields with type PDO::PARAM_STR are returned
	 * 
	 * @todo implement custom fields
	 * @return array Field names that should be used for the search query.
	 */
	protected function getFindSearchQueryParamFields(){
		//throw new Exception('Error: you supplied a searchQuery parameter to find but getFindSearchQueryParamFields() should be overriden in '.$this->className());
		$fields = array();
		foreach($this->columns as $field=>$attributes){
			if($attributes['type']==PDO::PARAM_STR){
				$fields[]=$field;
			}
		}
		return $fields;		
	}
	
	/**
	 * Returns the name of this ActiveRecord derrived class
	 * 
	 * @return string Name
	 */
	public function className(){
		return get_class($this);
	}
	
	private function _appendPkSQL($sql, $primaryKey=false){
		
		if(!$primaryKey)
			$primaryKey=$this->pk;
		
					
		if(is_array($this->primaryKey())){
			$first = true;
			foreach($primaryKey as $field=>$value){
				$this->$field=$value;
				if(!$first)
					$sql .= ' AND ';
				else
					$first=false;
				
				$sql .= "`".$field.'`='.$this->getDbConnection()->quote($value, $this->columns[$field]['type']);
			}
		}else
		{
			$this->{$this->primaryKey()}=$primaryKey;
			
			$sql .= "`".$this->primaryKey().'`='.$this->getDbConnection()->quote($primaryKey, $this->columns[$this->primaryKey()]['type']);
		}
		return $sql;
	}
	
	/**
	 * Loads the model attributes from the database
	 * 
	 * @param int $primaryKey
	 * @return GO_Base_Db_ActiveRecord 
	 */
	
	public function findByPk($primaryKey){
		
		//Use cache so identical findByPk calls are only executed once per script request
		$cachedModel = !is_array($primaryKey) ? GO::modelCache()->get($this->className(), $primaryKey) : false;
		if($cachedModel)
			return $cachedModel;
		
		$sql = "SELECT * FROM `".$this->tableName()."` WHERE ";
		
		$sql = $this->_appendPkSQL($sql, $primaryKey);
	
		
		if($this->_debugSql)
				GO::debug($sql);
		
		//GO::debug($sql);
			
		$result = $this->getDbConnection()->query($sql);
		
		$result->setFetchMode(PDO::FETCH_CLASS, $this->className());
		
		$model =  $result->fetch();
		
		if(!is_array($primaryKey) && $model)
			GO::modelCache()->add($this->className(), $model);
		
		return $model;
		
		/**
		 * Useful event for modules. For example custom fields can be loaded or a files folder.
		 */
		//$GLOBALS['GO_EVENTS']->fire_event('loadactiverecord',array(&$this));
	}
	
	/**
	 * May be overriden to do stuff after the model was loaded from the database
	 */
	protected function afterLoad(){
			//GO::debug($this);
	}
	

	private function _getRelated($name){
		 //$name::findByPk($hit-s)
		$r= $this->relations();
		
		if(!isset($r[$name])){
			return false;			
		}
		
		$model = $r[$name]['model'];
		
		if($r[$name]['type']==self::BELONGS_TO){// || r[$name]['type']==self::HAS_ONE){
		
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
				$joinAttribute = $r[$name]['field'];
				$this->_relatedCache[$name]= $model::model()->findByPk($this->_attributes[$joinAttribute]);
			}

			return $this->_relatedCache[$name];
		}elseif($r[$name]['type']==self::HAS_MANY)
		{							
			$remotePkValue = $this->pk;
			$remotePkField = $r[$name]['field'];
			$findParams = array(
					"by"=>array(array($remotePkField,$remotePkValue,'=')),
					"ignoreAcl"=>true
			);
				
			$stmt = $model::model()->find($findParams);
			return $stmt;		
		}
	}
	
	/**
	 * Formats user input for the database.
	 * 
	 * @param array $attributes
	 * @return array 
	 */
	protected function formatInputValues($attributes){
		$formatted = array();
		foreach($attributes as $key=>$value){
			if(!isset($this->columns[$key])){
				//don't process unknown columns.
				continue;
			}
			if(!isset($this->columns[$key]['gotype'])){
				$this->columns[$key]['gotype']='string';
			}
			switch($this->columns[$key]['gotype']){
				case 'unixtimestamp':
					$formatted[$key] = GO_Base_Util_Date::to_unixtime($value);
					break;			

				default:
					$formatted[$key] = $value;
					break;
			}
			
		}
		return $formatted;
	}
	
	/**
	 * Format database values for display in the user's locale.
	 * 
	 * @param array $attributes
	 * @param bool $html set to true if it's used for html output
	 * @return array 
	 */
	protected function formatOutputValues($attributes, $html=false){
		
		$formatted = array();
		foreach($attributes as $key=>$value){
			if(!isset($this->columns[$key]['gotype'])){
				$this->columns[$key]['gotype']='string';
			}
			switch($this->columns[$key]['gotype']){
				case 'unixtimestamp':
					$formatted[$key] = GO_Base_Util_Date::get_timestamp($value);
					break;	

				case 'textarea':
					if($html){
						$formatted[$key] = GO_Base_Util_String::text_to_html($value);
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
			$attributes = $this->formatInputValues($attributes);
		
		foreach($attributes as $key=>$value){
			if(isset($this->columns[$key])){
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
		
		return $this->formatOutputValues($this->_attributes, $outputType=='html');		
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
		return $this->columns;
	}
	
	/**
	 * Checks all the permissions
	 * 
	 * @todo new item's which don't have ACL should check different ACL for adding new items.
	 * @return boolean 
	 */
	private function _checkPermissionLevel($level){

		if(!$this->aclField())
			return true;

		if($this->getPermissionLevel()==-1)
			return true;

		return $this->getPermissionLevel()>=$level;
	}
	

	/**
	 * Validates all attributes of this model
	 * 
	 * @todo Implement unique fields. eg. Name of addresbook must be unique
	 * @return boolean 
	 */

	public function validate(){
		foreach($this->columns as $field=>$attributes){
			if(!empty($attributes['required']) && empty($this->_attributes[$field])){
				throw new Exception($field.' is required');
			}elseif(!empty($attributes['length']) && !empty($this->_attributes[$field]) && strlen($this->_attributes[$field])>$attributes['length'])
			{
				throw new Exception($field.' too long');
			}elseif(!empty($attributes['validator']) && !empty($this->_attributes[$field]) && !call_user_func($attributes['validator'], $this->_attributes[$field]))
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
		
		if(!$this->_checkPermissionLevel(GO_Base_Model_Acl::WRITE_PERMISSION))
			throw new AccessDeniedException();
				
		if($this->validate()){		
		
			/*
			 * Set some common column values
			*/
			
			if(isset($this->columns['mtime']))
				$this->mtime=time();
			if(isset($this->columns['ctime']) && !isset($this->ctime)){
				$this->ctime=time();
			}
			
			if(isset($this->columns['user_id']) && !isset($this->user_id)){
				$this->user_id=GO::user() ? GO::user()->id : 1;
			}
			
			
			/**
			 * Useful event for modules. For example custom fields can be loaded or a files folder.
			 */
			$this->fireEvent('beforesave',array(&$this));
			
			
			if ($this->hasFiles()) {
				$this->files_folder_id = GO_Files_Controller_Item::itemFilesFolder($this, $this->buildFilesPath());
			}
			
			
			if($this->isNew){				
				
				if($this->aclField() && !$this->joinAclField && empty($this->{$this->aclField()})){
					//generate acl id
					
					$acl = new GO_Base_Model_Acl();
					$acl->description=$this->tableName().'.'.$this->aclField();
					$acl->user_id=GO::user() ? GO::user()->id : 1;
					$acl->save();
					
					$this->{$this->aclField()}=$acl->id;
				}				
				
				if(!$this->beforeSave())
					return false;				

				$this->_dbInsert();
				
				if(!is_array($this->primaryKey()))
					$this->{$this->primaryKey()} = $this->getDbConnection()->lastInsertId();
				
				if(!$this->pk)
					return false;
			}else
			{
				if(!$this->beforeSave())
					return false;
				
				
				if(!$this->_dbUpdate())
					return false;
			}
			
			
			if (isset(GO::modules()->customfields) && $this->customfieldsModel())
				GO_Customfields_Controller_Item::saveCustomFields($this, $this->customfieldsModel());

			
			if(!$this->afterSave())
				return false;
			
			/**
			 * Useful event for modules. For example custom fields can be loaded or a files folder.
			 */
			$this->fireEvent('save',array(&$this));
			
			
			$this->_cacheSearchRecord();
			
			$this->_setOldAttributes();
			
			return true;
			
		}else
		{
			return false;
		}
	}
	
	/**
	 * The files module will use this function. To create a files folder.
	 * Override it if you don't like the default path.
	 */
	protected function buildFilesPath() {

		return isset($this->name) ? $this->getModule().'/' . GO_Base_Util_File::strip_invalid_chars($this->name) : false;
	}
	
	/**
	 * Get the name of the module that this model belongs to.
	 * 
	 * @return string 
	 */
	private function getModule(){
		$arr = explode('_', $this->className());
		
		return strtolower($arr[1]);
	}
	
	private function _cacheSearchRecord(){
		
		$attr = $this->getCacheAttributes();
		
		if($attr){

			$model =GO_Base_Model_SearchCacheRecord::model()->findByPk(array('id'=>$this->pk,'link_type'=>$this->linkType()));
			if(!$model)
				$model = GO_Base_Model_SearchCacheRecord::model();
			
			//GO::debug($model);

			$autoAttr = array(
				'id'=>$this->pk,
				'link_type'=>$this->linkType(),
				'user_id'=>isset($this->user_id) ? $this->user_id : GO::session()->values['user_id'],
				'module'=>$this->module,
				'name' => '',
				'link_type'=>$this->linkType(),
				'description'=>'',		
				'type'=>'',
				'keywords'=>$this->_getSearchCacheKeywords($this->record).','.$attr['type'],
				'mtime'=>$this->mtime,
				'acl_id'=>$this->findAclId()
			);
			
			$attr = array_merge($autoAttr, $attr);

			$model->setAttributes($attr);
			return $model->save();
		}
		return true;
	}
	
	/**
	 * Override this function if you want to put your model in the search cache.
	 * 
	 * @return array cache parameters with at least 'name', 'description' and 'type'. All are strings. See GO_Base_Model_Search_Cache for more info.
	 */
	protected function getCacheAttributes(){
		return false;
	}
	
	private function _getSearchCacheKeywords(){
		$keywords=array();

		foreach($this->columns as $key=>$attr)
		{
			$value = $this->$key;
			if($attr['type']==PDO::PARAM_STR && !in_array($value,$keywords)){
				$keywords[]=$value;
			}
		}
		$keywords =  implode(',',$keywords);
		
		return substr($keywords,0,255);
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
		
		$fieldNames = array_keys($this->columns);
		
		$sql = "INSERT INTO `{$this->tableName()}` (`".implode('`,`', $fieldNames)."`) VALUES ".
					"(:".implode(',:', $fieldNames).")";

		if($this->_debugSql)
			GO::debug($sql);
		
		$stmt = $this->getDbConnection()->prepare($sql);
		
		foreach($fieldNames as  $field){
			
			$attr = $this->columns[$field];
			
			$stmt->bindParam(':'.$field, $this->_attributes[$field], $attr['type'], empty($attr['length']) ? null : $attr['length']);
		}
		return $stmt->execute();
		
		
		
	}
	
	private function _dbUpdate(){
		
		$updates=array();
		
		$pks = is_array($this->primaryKey()) ? $this->primaryKey() : array($this->primaryKey());
		foreach($this->columns as $field => $value)
		{
			if(!in_array($field,$pks))
			{
				$updates[] = "`$field`=:".$field;
			}
		}
		
		if(!count($updates))
			return true;
		
		$sql = "UPDATE `{$this->tableName()}` SET ".implode(',',$updates)." WHERE ";
		
		if(is_array($this->primaryKey())){
			
			$first=true;
			foreach($this->primaryKey() as $field){
				if(!$first)
					$sql .= ' AND ';
				else
					$first=false;
			
				$sql .= "`".$field."`=:".$field;
			}
			
		}else
			$sql .= "`".$this->primaryKey()."`=:".$this->primaryKey();
		
		if($this->_debugSql)
			GO::debug($sql);

		$stmt = $this->getDbConnection()->prepare($sql);
		
		foreach($this->columns as $field => $value){
			
			$attr = $this->columns[$field];
			
			$stmt->bindParam(':'.$field, $this->_attributes[$field], $attr['type'], empty($attr['length']) ? null : $attr['length']);
		}
		return $stmt->execute();
		
	}
	
	protected function beforeDelete(){
		return true;
	}
	protected function afterDelete(){
		return true;
	}
	
	/**
	 * Delete's the model from the database
	 * @return PDOStatement 
	 */
	public function delete(){
		
		if(!$this->_checkPermissionLevel(GO_Base_Model_Acl::DELETE_PERMISSION))
						throw new AccessDeniedException ();
		
		
		if(!$this->beforeDelete())
				return false;
		
		$r= $this->relations();
		
		foreach($r as $name => $attr){
			if(!empty($attr['delete'])){

				$stmt = $this->$name;
				while($child = $stmt->fetch()){

					$child->delete();
				}
			}
		}
		
		$sql = "DELETE FROM `".$this->tableName()."` WHERE ";
		$sql = $this->_appendPkSQL($sql);
		
		
		if($this->_debugSql)
			GO::debug($sql);
		
		$success = $this->getDbConnection()->query($sql);		
		
		$attr = $this->getCacheAttributes();
		
		if($attr){
			$model = GO_Base_Model_SearchCacheRecord::model()->findByPk(array('id'=>$this->pk,'link_type'=>$this->linkType()));
			if($model)
				$model->delete();
		}
		
		
		if($this->aclField() && !$this->joinAclField){			
			//echo 'Deleting acl '.$this->{$this->aclField()}.' '.$this->aclField().'<br />';
			
			$acl = GO_Base_Model_Acl::model()->findByPk($this->{$this->aclField()});			
			$acl->delete();
		}	
		
		
		if(isset(GO::modules()->files) && $this->hasFiles()){
			GO_Files_Controller_Item::deleteFilesFolder($this->files_folder_id);	
		}

		return $this->afterDelete();			
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
			}else
			{
				$r = $this->relations();
				if(isset($r[$name]))			
				{
					return $this->_getRelated($name);
				}
			}
		}
			
	}
	
	
	/**
	 * Calls the named method which is not a class method.
	 * Do not call this method. This is a PHP magic method that we override
	 * to implement the named scope feature.
	 * 
	 * @param string $name the method name
	 * @param array $parameters method parameters
	 * @return mixed the method return value
	 */
	public function __call($name,$parameters)
	{
		//todo find relation
		die('function '.$name.' does not exist');
		//return parent::__call($name,$parameters);
	}

	/**
	 * PHP setter magic method.
	 * This method is overridden so that AR attributes can be accessed like properties.
	 * 
	 * //TODO record changed attributes here for smarter saving.
	 * 
	 * 
	 * @param string $name property name
	 * @param mixed $value property value
	 */
	public function __set($name,$value)
	{
		$this->setAttribute($name,$value);
	}
	
	public function __isset($name){
		$var = $this->$name;
		return isset($var);
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
		if(property_exists($this,$name)){
			$this->$name=$value;
		}elseif(isset($this->columns[$name])){
			$this->_attributes[$name]=$value;		
		}else{
			$arr = explode('@',$name);
			if(count($arr)>1)
				$this->_relatedCache[$arr[0]][$arr[1]]=$value;				
			else		
				$this->_attributes[$name]=$value; //this attribute is unsafe but we may want to use it in the contructor anyway. For example the customfield record doesn't know the columns until after the constructor.
		}

		return true;
	}
	
	
	/**
	 * Pass another model to this function and they will be linked with the
	 * Group-Office link system.
	 * 
	 * @todo
	 * @param mixed $model 
	 */
	
	public function linkTo($model){
		
	}
	
	/**
	 * Returns the customfields record if module is installed and this model
	 * supports it (See GO_Base_Db_ActiveRecord::customFieldsModel())
	 * 
	 * @return GO_Customfields_Model_AbstractCustomFieldsRecord 
	 */
	public function customfieldsRecord(){
		
		if($this->$this->customfieldsModel() && GO::modules()->customfields){
			$customFieldModelName=$this->$this->customfieldsModel();

			return $customFieldModelName::model()->findByPk($this->pk);
		}else
		{
			return false;
		}
	}
	
	/**
	 * Returns the user model if this model has a user_id column.
	 * 
	 * @return GO_Base_Model_User 
	 */
	public function user(){
		
		if(!empty($this->user_id)){
			return GO_Base_Model_User::model()->findByPk($model->user_id);
		}else
		{
			return false;
		}
	}

}