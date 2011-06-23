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
 */

abstract class GO_Base_Db_ActiveRecord extends GO_Base_Observable{
	
	const BELONGS_TO=1;	
	const HAS_MANY=2;
	
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
	public $linkType=0;
	
	
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
	public $aclField=false;
	
	private $_relatedCache;
	
	
	private static $_models=array();			// class name => model
	
	
	private $_attributes=array();
	
	private $_debugSql=true;

	/**
	 *
	 * @return <type> Call $model->aclFieldJoin to check if the aclfield is joined.
	 */
	private function getAclFieldJoin (){
		return strpos($this->aclField,'.')!==false;
	}
	
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
	
	private $_new=true;

	/**
	 * Constructor for the model
	 * 
	 * @param int $primaryKey integer The primary key of the database table
	 */
	public function __construct(){			
		
		$this->setIsNew(empty($this->pk));
		$this->init();
	}
	
	
	/**
	 * Returns the static model of the specified AR class.
	 * @return Course the static model class
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
	
	protected function init(){}
	
	/**
	 * Get's the primary key value. Can also be accessed with $model->pk.
	 * 
	 * @return mixed The primary key value 
	 */
	public function getPk(){
		
		$ret = null;
		
		if(is_array($this->primaryKey)){
			foreach($this->primaryKey as $field){
				if(isset($this->_attributes[$field])){
					$ret[$field]=$this->_attributes[$field];
				}
			}
		}elseif(isset($this->_attributes[$this->primaryKey]))
			$ret =  $this->_attributes[$this->primaryKey];
		
		return $ret;
	}
	
	public function getIsNew(){
		
		return $this->_new;
	}
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
	
	private $_acl_id;
	
	
	public function findAclId() {
		if (empty($this->aclField))
			return false;
		
		if(!isset($this->_acl_id)){
			$arr = explode('.', $this->aclField);
			if (count($arr) == 2) {
				$relation = $arr[0];
				$aclField = $arr[1];
				$this->_acl_id = $this->$relation->$aclField;
			} else {
				$this->_acl_id = $this->{$this->aclField};
			}
		}
		
		return $this->_acl_id;		
	}

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
			$this->_permissionLevel=GO::security()->hasPermission($this->findAclId());
		}
		return $this->_permissionLevel;
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
	 *  searchQuery=>"String"
	 * };
	 * 
	 * @param array $params
	 * @return PDOStatement
	 */
	public function find($params=array(), &$foundRows=false){
		
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
		
		$sql = "SELECT ";
		
		if($foundRows!==false && !empty($params['limit']) && empty($params['start'])){
			
			//TODO: This is MySQL only code
			
			$sql .= "SQL_CALC_FOUND_ROWS ";
		}
		
		$sql .= "t.*".$aclJoin['fields']." FROM `".$this->tableName."` t ".$aclJoin['join'];
		
		if($this->aclField && empty($params['ignoreAcl'])){			
			
			$sql .= "INNER JOIN go_acl ON (`".$aclJoin['table']."`.`".$aclJoin['aclField']."` = go_acl.acl_id";
			if(isset($params['permissionLevel']) && $params['permissionLevel']>GO_SECURITY::READ_PERMISSION){
				$sql .= " AND go_acl.level>=".intval($params['permissionLevel']);
			}
			$sql .= " AND (go_acl.user_id=".intval($params['userId'])." OR go_acl.group_id IN (".implode(',',GO::security()->get_user_group_ids($params['userId']))."))) ";
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
						$value[$i]=$this->getDbConnection()->quote($value[$i], $this->_columns[$field]['type']);
					
					$sql .= "`$field` $comparator (".implode(',',$value).") ";
				}else
				{							
					$sql .= "`$field` $comparator ".$this->getDbConnection()->quote($value, $this->_columns[$field]['type'])." ";
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
		
		
		if($this->aclField && empty($params['ignoreAcl'])){
			
			$pk = is_array($this->primaryKey) ? $this->primaryKey : array($this->primaryKey);
			
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
				go_debug($sql);

		//$sql .= "WHERE `".$this->primaryKey.'`='.intval($primaryKey);
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
					$foundRows = $_SESSION['GO_SESSION'][$queryUid]=intval($record['found']);	
				}else
				{
					$foundRows=$_SESSION['GO_SESSION'][$queryUid];
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
	 * Override this method to support a searchQuery argument in the find function.
	 * 
	 * @return array Field names that should be used for the search query.
	 */
	protected function getFindSearchQueryParamFields(){
		throw new Exception('Error: you supplied a searchQuery parameter to find but getFindSearchQueryParamFields() should be overriden in '.$this->className());
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
		
					
		if(is_array($this->primaryKey)){
			$first = true;
			foreach($primaryKey as $field=>$value){
				$this->$field=$value;
				if(!$first)
					$sql .= ' AND ';
				else
					$first=false;
				
				$sql .= "`".$field.'`='.$this->getDbConnection()->quote($value, $this->_columns[$field]['type']);
			}
		}else
		{
			$this->{$this->primaryKey}=$primaryKey;
			
			$sql .= "`".$this->primaryKey.'`='.intval($primaryKey);
		}
		return $sql;
	}
	
	/**
	 * Loads the model attributes from the database
	 * 
	 * @param int $primaryKey 
	 */
	
	public function findByPk($primaryKey){
		
		//Use cache so identical findByPk calls are only executed once per script request
		$cachedModel = GO::modelCache()->get($this->className(), $primaryKey);
		if($cachedModel)
			return $cachedModel;
		
		$sql = "SELECT * FROM `".$this->tableName."` WHERE ";
		
		$sql = $this->_appendPkSQL($sql, $primaryKey);
		
		if($this->_debugSql)
				go_debug($sql);
		
		//go_debug($sql);
			
		$result = $this->getDbConnection()->query($sql);
		
		$result->setFetchMode(PDO::FETCH_CLASS, $this->className());
		
		$model =  $result->fetch();
		
		GO::modelCache()->add($this->className(), $model);
		
		return $model;
		
		/**
		 * Useful event for modules. For example custom fields can be loaded or a files folder.
		 */
		//GO::events()->fire_event('loadactiverecord',array(&$this));
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
			$this->_relatedCache[$name]= $model::model()->findByPk($this->_attributes[$joinAttribute]);
		}
		
		return $this->_relatedCache[$name];
	}
	
	protected function formatInputValues($attributes){
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
	
	protected function formatOutputValues($attributes, $html=false){
		
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
			$attributes = $this->formatInputValues($attributes);
		
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
			
			
			/**
			 * Useful event for modules. For example custom fields can be loaded or a files folder.
			 */
			$this->fireEvent('beforesave',array(&$this));
			
			if($this->isNew){				
				
				if($this->aclField && !$this->joinAclField){
					//generate acl id
					$this->{$this->aclField}=GO::security()->get_new_acl($this->tableName);
				}				

				$this->_dbInsert();
				
				if(!is_array($this->primaryKey))
					$this->{$this->primaryKey} = $this->getDbConnection()->lastInsertId();
				
				if(!$this->pk)
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
			
			
			$this->_cacheSearchRecord();
			
			return true;
			
		}else
		{
			return false;
		}
	}
	
	private function getModule(){
		$arr = explode('_', $this->className());
		
		return strtolower($arr[1]);
	}
	
	private function _cacheSearchRecord(){
		
		$attr = $this->getCacheAttributes();
		
		if($attr){

			$model =GO_Base_Model_SearchCacheRecord::model()->findByPk(array('id'=>$this->pk,'link_type'=>$this->linkType));
			if(!$model)
				$model = GO_Base_Model_SearchCacheRecord::model();
			
			//go_debug($model);

			$autoAttr = array(
				'id'=>$this->pk,
				'link_type'=>$this->linkType,
				'user_id'=>isset($this->user_id) ? $this->user_id : $_SESSION['GO_SESSION']['user_id'],
				'module'=>$this->module,
				'name' => '',
				'link_type'=>$this->linkType,
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
	 * @return array cache parameters with at least 'name', 'description' and 'type'. All are strings.
	 */
	protected function getCacheAttributes(){
		return false;
	}
	
	private function _getSearchCacheKeywords(){
		$keywords=array();

		foreach($this->_columns as $key=>$attr)
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
		
		$fieldNames = array_keys($this->_columns);
		
		$sql = "INSERT INTO `{$this->tableName}` (`".implode('`,`', $fieldNames)."`) VALUES ".
					"(:".implode(',:', $fieldNames).")";

		if($this->_debugSql)
			go_debug($sql);
		
		$stmt = $this->getDbConnection()->prepare($sql);
		
		foreach($fieldNames as  $field){
			
			$attr = $this->_columns[$field];
			
			$stmt->bindParam(':'.$field, $this->_attributes[$field], $attr['type'], empty($attr['length']) ? null : $attr['length']);
		}
		return $stmt->execute();
		
		
		
	}
	
	private function _dbUpdate(){
		
		$updates=array();
		
		$pks = is_array($this->primaryKey) ? $this->primaryKey : array($this->primaryKey);
		foreach($this->_columns as $field => $value)
		{
			if(!in_array($field,$pks))
			{
				$updates[] = "`$field`=:".$field;
			}
		}
		
		if(!count($updates))
			return true;
		
		$sql = "UPDATE `{$this->tableName}` SET ".implode(',',$updates)." WHERE ";
		
		if(is_array($this->primaryKey)){
			
			$first=true;
			foreach($this->primaryKey as $field){
				if(!$first)
					$sql .= ' AND ';
				else
					$first=false;
			
				$sql .= "`".$field."`=:".$field;
			}
			
		}else
			$sql .= "`".$this->primaryKey."`=:".$this->primaryKey;
		
		if($this->_debugSql)
			go_debug($sql);

		$stmt = $this->getDbConnection()->prepare($sql);
		
		foreach($this->_columns as $field => $value){
			
			$attr = $this->_columns[$field];
			
			$stmt->bindParam(':'.$field, $this->_attributes[$field], $attr['type'], empty($attr['length']) ? null : $attr['length']);
		}
		return $stmt->execute();
		
	}
	
	/**
	 * Delete's the model from the database
	 * @return PDOStatement 
	 */
	public function delete(){
		
		if(!$this->_checkPermissionLevel(GO_SECURITY::DELETE_PERMISSION))
						throw new AccessDeniedException ();
		
		$sql = "DELETE FROM `".$this->tableName."` WHERE ";
		$sql = $this->_appendPkSQL($sql);
		
		
		if($this->_debugSql)
			go_debug($sql);
		
		$success = $this->getDbConnection()->query($sql);		
		
		$attr = $this->getCacheAttributes();
		
		if($attr){
			$model = GO_Base_Model_SearchCacheRecord::model()->findByPk(array('id'=>$this->pk,'link_type'=>$this->linkType));
			$model->delete();
		}
		
		return $success;			
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
		
		return parent::__call($name,$parameters);
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
		}elseif(isset($this->_columns[$name])){
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
	 * @param mixed $model 
	 */
	
	public function linkTo($model){
		
	}

}