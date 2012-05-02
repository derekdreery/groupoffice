<?php
/**
 * Group-Office
 * 
 * Copyright Intermesh BV. 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @license AGPL/Proprietary http://www.group-office.com/LICENSE.TXT
 * @link http://www.group-office.com
 * @copyright Copyright Intermesh BV
 * @version $Id: Number.php 7962 2011-08-24 14:48:45Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 * @package GO.base.db
 */

/**
 * All Group-Office models should extend this ActiveRecord class.
 *
 * @package GO.base.db
 * @version $Id: File.class.inc.php 7607 2011-06-15 09:17:42Z mschering $
 * @copyright Copyright Intermesh BV.
 * @author Merijn Schering <mschering@intermesh.nl> 
 * @abstract
 * 
 * @property GO_Base_Model_User $user If this model has a user_id field it will automatically create this property
 * @property GO_Base_Model_Acl $acl If this model has an acl ID configured. See GO_Base_Db_ActiveRecord::aclId it will automatically create this property.
 * @property bool $joinAclField
 * @property int/array $pk Primary key value(s) for the model
 * @property string $module Name of the module this model belongs to
 * @property boolean $isNew Is the model new and not inserted in the database yet.
 * @property GO_Customfields_Model_AbstractCustomFieldsRecord $customfieldsRecord The custom fields model with all custom attributes.
 * @property String $localizedName The localized human friendly name of this model.
 * @property int $permissionLevel @see GO_Base_Model_Acl for available levels. Returns -1 if no aclField() is set in the model.
 * 
 * @property GO_Files_Model_Folder $filesFolder The folder model that belongs to this model if hasFiles is true.
 */

abstract class GO_Base_Db_ActiveRecord extends GO_Base_Model{
	
	/**
	 * The mode for this model on how to output the attribute data.
	 * Can be "raw", "formatted" or "html";
	 * 
	 * @var string 
	 */
	public static $attributeOutputMode='raw';
	
	/**
	 * This relation is used when the remote model's primary key is stored in a 
	 * local attribute.
	 * 
	 * Addressbook->user() for example
	 */
	const BELONGS_TO=1;	// n:1
	
	/**
	 * This relation type is used when this model has many related models. 
	 * 
	 * Addressbook->contacts() for example.
	 */
	const HAS_MANY=2; // 1:n
	
	/**
	 * This relation type means that the relation is single and this model's primary
	 * key can be found in the remote model.
	 * 
	 * User->Addressbook for example where user_id is in the addressbook table.
	 */
	const HAS_ONE=3; // 1:1
	
  /*
   * This relation type is used when this model has many related models.
   * The relation makes use of a linked table that has a combined key of the related model and this model.
   * 
   * Example use in the model class relationship array: 'users' => array('type'=>self::MANY_MANY, 'model'=>'GO_Base_Model_User', 'linkModel'=>'GO_Base_Model_UserGroups', 'field'=>'group_id', 'remoteField'=>'user_id'),
   * 
   */
  const MANY_MANY=4;	// n:n
  
//	/**
//	 * The database connection of this record
//	 * 
//	 * @var PDO  
//	 */
//	private static $db;
	
	private $_validationErrors = array();

	private $_attributeLabels;	
	
	/**
	 * Force this activeRecord to save itself 
	 * 
	 * @var boolean 
	 */
	private $_forceSave = false;
	
	/**
	 * See http://dev.mysql.com/doc/refman/5.1/en/insert-delayed.html
	 * 
	 * @var boolean 
	 */
	protected $insertDelayed=false;
	
	/**
	 *
	 * @var int Link type of this Model used for the link system. See also the linkTo function
	 */
	public function modelTypeId(){		
		return GO_Base_Model_ModelType::model()->findByModelName($this->className());		
	}
	
	/**
	 * Get the localized human friendly name of this model.
	 * This function must be overriden.
	 * 
	 * @return String 
	 */
	protected function getLocalizedName(){
		
		$parts = explode('_',$this->className());
		$lastPart = array_pop($parts);
		
		$module = strtolower($parts[1]);
		
		return GO::t($lastPart, $module);
	}
	
	
	/**
	 * 
	 * Define the relations for the model.
	 * 
	 * Example return value:
	 * array(
				'contacts' => array('type'=>self::HAS_MANY, 'model'=>'GO_Addressbook_Model_Contact', 'field'=>'addressbook_id', 'delete'=>true //with this enabled the relation will be deleted along with the model),
				'companies' => array('type'=>self::HAS_MANY, 'model'=>'GO_Addressbook_Model_Company', 'field'=>'addressbook_id', 'delete'=>true),
				'addressbook' => array('type'=>self::BELONGS_TO, 'model'=>'GO_Addressbook_Model_Addressbook', 'field'=>'addressbook_id')
				'users' => array('type'=>self::MANY_MANY, 'model'=>'GO_Base_Model_User', 'field'=>'group_id', 'linkModel' => 'GO_Base_Model_UserGroup'), // The "field" property is the key of the current model that is defined in the linkModel
		);
	 * 
	 * The relations can be accessed as functions:
	 * 
	 * Model->contacts() for example. They always return a PDO statement. 
	 * You can supply GO_Base_Db_FindParams as an optional parameter to narrow down the results.
	 * 
	 * Note: relational queries do not check permissions!
	 * 
	 * If you have a "user_id" field, an automatic relation model->user() is created that 
	 * returns a GO_Base_Model_User.
	 * 
	 * "delete"=>true will automatically delete the relation along with the model. delete flags on BELONGS_TO relations are invalid and will be ignored.
	 * 
	 * 
	 * You can also select find parameters that will be applied to the relational query. eg.:
	 * 
	 * findParams=>GO_Base_Db_FindParams::newInstance()->order('sort_index');
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
	
	protected $_attributes=array();
	
	private $_modifiedAttributes=array();
	
	private $_debugSql=false;
	
	
	/**
	 * Set to true to enable a files module folder for this item. A files_folder_id
	 * column in the database is required. You will probably 
	 * need to override buildFilesPath() to make it work properly.
	 * 
	 * @return bool 
	 */
	public function hasFiles(){return false;}
	
	/**
	 * Set to true to enable links for this model. A table go_links_$this->tableName() must be created
	 * with columns: id, model_id, model_type_id
	 * 
	 * @return bool 
	 */
	public function hasLinks(){return false;}
	
	
	private $_filesFolder;
	
	/**
	 * Get the folder model belonging to this model if it supports it.
	 * If the folder doesn't exist yet it will create it.
	 * 
	 * @return GO_Files_Model_Folder
	 */
	public function getFilesFolder(){
	
		if(!$this->hasFiles())
			return false;
		
		if(!isset($this->_filesFolder)){		
			$c = new GO_Files_Controller_Folder();
			$folder_id = $c->checkModelFolder($this, true, true);

			$this->_filesFolder=GO_Files_Model_Folder::model()->findByPk($folder_id);
			if(!$this->_filesFolder)
				throw new Exception("Could not create files folder for ".$this->className()." ".$this->pk);
		}
		return $this->_filesFolder;		
	}

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
	 * eg: 'id'=>array(
	 * 'type'=>PDO::PARAM_INT, //Autodetected
	 * 'required'=>true, //Will be true automatically if field in database may not be null and doesn't have a default value
	 * 'length'=><max length of the value>, //Autodetected from db
	 * 'validator'=><a function to call to validate the value>,
	 * 'gotype'=>'number|textfield|textarea|unixtimestamp|unixdate|user', //Autodetected from db as far as possible. See loadColumns()
	 * 'decimals'=>2//only for gotype=number)
	 * 'regex'=>'A preg_match expression for validation',
	 * 'dbtype'=>'varchar' //mysql database type
	 * 'unique'=>false //true to enforce a unique value
	 * 
	 * The validator looks like this:
	 * 
	 * function validate ($value){
			return true;
		}
	 */
	protected $columns=array(
				'id'=>array('type'=>PDO::PARAM_INT,'required'=>true,'length'=>null, 'validator'=>null,)
			);	
	
	private $_new=true;

	/**
	 * Constructor for the model
	 * 
	 * @param int $primaryKey integer The primary key of the database table
	 */
	public function __construct($newRecord=true){			
		
		//$pk = $this->pk;

		$this->loadColumns();
		$this->setIsNew($newRecord);
		
		if($this->isNew) 
			$this->setAttributes($this->_getDefaultAttributes(),false);
//		else
//			$this->_cacheRelatedAttributes();
				
		$this->init();
		
		$this->_modifiedAttributes=array();
	}
	
	/**
	 * When a model is joined on a find action and we need it for permissions, We 
	 * select all the model attributes so we don't have to query it seperately later.
	 * eg. $contact->addressbook will work from the cache when it was already joined. 
	 */
	private function _cacheRelatedAttributes(){
		foreach($this->_attributes as $name=>$value){
			$arr = explode('@',$name);
			if(count($arr)>1)
				$this->_relatedCache[$arr[0]][$arr[1]]=$value;							
		}
	}
	
	/**
	 * Returns localized attribute labels for each column.
	 * 
	 * The default language variable name is modelColumn.
	 * 
	 * eg.: GO_Tasks_Model_Task column 'name' will look for:
	 * 
	 * $l['taskName']
	 * 
	 * 'due_time' will be
	 * 
	 * $l['taskDue_time']
	 * 
	 * If you don't like this you may also override this function in your model.
	 * 
	 * @return array
	 * 
	 * A key value array eg. array('name'=>'Name', 'due_time'=>'Due time')
	 * 
	 */
	public function attributeLabels(){
		if(!isset($this->_attributeLabels)){
			$this->_attributeLabels = array();

			$classParts = explode('_',$this->className());
			$prefix = strtolower(array_pop($classParts));

			foreach($this->columns as $columnName=>$columnData){
				$this->_attributeLabels[$columnName] = GO::t($prefix.ucfirst($columnName), $this->getModule(),'',$found);
				if(!$found) {
						switch($columnName){
							case 'user_id':
								$this->_attributeLabels[$columnName] = GO::t('strUser');
								break;

							case 'ctime':
								$this->_attributeLabels[$columnName] = GO::t('strCtime');
								break;

							case 'mtime':
								$this->_attributeLabels[$columnName] = GO::t('strMtime');
								break;
							case 'name':
								$this->_attributeLabels[$columnName] = GO::t('strName');
								break;	
						}
					}				
				}
		}
		return $this->_attributeLabels;
	}
	
		
		
	/**
	 * Get the label of the asked attribute
	 * 
	 * This function can be overridden in the model.
	 * 
	 * @return String The label of the asked attribute
	 */
	public function getAttributeLabel($attribute) {
		
		$labels = $this->attributeLabels();
		
		return isset($labels[$attribute]) ? $labels[$attribute] : $attribute;
	}
	
	/**
	 * Set the label of an attribute
	 * 
	 * This function can be overridden in the model.
	 * 
	 * @param type $attribute
	 * @param type $label 
	 */
	public function setAttributeLabel($attribute,$label) {
			$this->columns[$attribute]['label'] = $label;
	}
	
	
	/**
	 * Loads the column information from the database
	 * 
	 */
	protected function loadColumns() {
		if($this->tableName()){
			
			$this->columns=GO::cache()->get('modelColumns_'.$this->tableName());
			
			if(!$this->columns){
			
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

					$required=false;
					$gotype = 'textfield';
	
					$pdoType = PDO::PARAM_STR;
					switch ($type) {
						case 'int':
						case 'tinyint':
						case 'bigint':
						
							$pdoType = PDO::PARAM_INT;
							if($length==1 && $type=='tinyint')
								$gotype='boolean';
							else
								$gotype = '';
							
							$length = 0;
							
							break;		
						
						case 'float':
						case 'double':
						case 'decimal':
							$pdoType = PDO::PARAM_STR;
							$length = 0;
							$gotype = 'number';
							break;
						
						case 'mediumtext':
						case 'longtext':
						case 'text':
							$gotype = 'textarea';
							break;
						
						case 'mediumblob':
						case 'longblob':
						case 'blob':
							$gotype = 'blob';
							break;
						
						case 'date':
							$gotype='date';
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
						case 'user_id':
							$gotype = 'user';
							break;
					}
					
					$default = $field['Default'];
					if($field['Null']=='NO' && is_null($default) && strpos($field['Extra'],'auto_increment')===false)
						$default='';
					
					//$required = is_null($default) && $field['Null']=='NO' && strpos($field['Extra'],'auto_increment')===false;

					$this->columns[$field['Field']]=array(
							'type'=>$pdoType,
							'required'=>$required,
							'length'=>$length,
							'gotype'=>$gotype,
							'default'=>$default,
							'dbtype'=>$type
					);
					
				}

				GO::cache()->set('modelColumns_'.$this->tableName(), $this->columns);
			}
		}
	}
	
	

	
	
//	/**
//	 * Returns the static model of the specified AR class.
//	 * Every child of this class must override it.
//	 * 
//	 * @return GO_Base_Db_ActiveRecord the static model class
//	 */
//	public static function model($className=__CLASS__)
//	{		
////	    if ($className=='GO_Base_Db_ActiveRecord') throw new Exception($className);
//		if(isset(self::$_models[$className]))
//			return self::$_models[$className];
//		else
//		{
//			$model=self::$_models[$className]=new $className();
//			return $model;
//		}
//	}
	
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
				}else
				{
					$ret[$field]=null;
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
	 * Note: this function is generally only used by the framework internally.
	 * You don't need to set this boolean. The framework takes care of that.
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
		return GO::getDbConnection();
		
//		if(self::$db!==null)
//			return self::$db;
//		else
//		{
//			self::$db=GO::getDbConnection();			
//		}
//		return self::$db;
	}
	
	private function _joinAclTable(){
		$arr = explode('.',$this->aclField());
		if(count($arr)==2){
			//we need to join a table for the acl field
			$r= $this->relations();
			$model = new $r[$arr[0]]['model'];
			
			$ret['relation']=$arr[0];
			$ret['aclField']=$arr[1];
			$ret['join']="\nINNER JOIN `".$model->tableName().'` '.$ret['relation'].' ON ('.$ret['relation'].'.`'.$model->primaryKey().'`=t.`'.$r[$arr[0]]['field'].'`) ';
			$ret['fields']='';
			
//			$cols = $model->getColumns();
			
//			foreach($cols as $field=>$props){
//				$ret['fields'].=', '.$ret['relation'].'.`'.$field.'` AS `'.$ret['relation'].'@'.$field.'`';
//			}
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
		
		$origValue = $value =  $this->$attributeName;

		while($existing = $this->findSingle(array(
				'criteriaObject'=>  GO_Base_Db_FindCriteria::newInstance()
					->addModel(GO::getModel($this->className()))
					->addCondition($attributeName, $value)
					->addCondition($this->primaryKey(), $this->pk, '!=')
		)))
		{			
			
			$value = $origValue.' ('.$x.')';
			$x++;
		}
		$this->$attributeName=$value;
	}
	
	private $_permissionLevel;
	
	private $_acl_id;
	
	/**
	 * Find the model that controls permissions for this model.
	 * 
	 * @return GO_Base_Db_ActiveRecord
	 * @throws Exception 
	 */
	public function findRelatedAclModel(){
		
		if (!$this->aclField())
			return false;
	
		
	
		$arr = explode('.', $this->aclField());
		if (count($arr) > 1) {
			$relation = $arr[0];

			//not really used. We use findAclId() of the model.
			$aclField = array_pop($arr);
			$modelWithAcl=$this;
			
			while($relation = array_shift($arr)){
				if(!$modelWithAcl->$relation)
					throw new Exception("Could not find relational ACL: ".$this->aclField()." ($relation) in ".$this->className()." with pk: ".$this->pk);
				else
					$modelWithAcl=$modelWithAcl->$relation;
			}	
			return $modelWithAcl;
		}else
		{
			return false;
		}
	}
	
	/**
	 * Find the acl_id integer value that applies to this model.
	 * 
	 * @return int ACL id from go_acl_items table. 
	 */
	public function findAclId() {
		if (!$this->aclField())
			return false;
		
		//removed caching of _acl_id because the relation is cached already and when the relation changes the wrong acl_id is returned,
		////this happened when moving contacts from one acl to another.
		//if(!isset($this->_acl_id)){
			//ACL is mapped to a relation. eg. $contact->addressbook->acl_id is defined as "addressbook.acl_id" in the contact model.
			$modelWithAcl = $this->findRelatedAclModel();
			if($modelWithAcl){
				$this->_acl_id = $modelWithAcl->findAclId();
			} else {
				$this->_acl_id = $this->{$this->aclField()};
			}
		//}
		
		return $this->_acl_id;		
	}
	
	/**
	 * Returns the permission level for the current user when this model is new 
	 * and does not have an ACL yet. This function can be overridden if you don't 
	 * like the default action.
	 * By default it only allows new models by module admins.
	 * 
	 * @return int 
	 */
	protected function getPermissionLevelForNewModel(){
		//the new model has it's own ACL but it's not created yet.
		//In this case we will check the module permissions.
		$module = $this->getModule();
		if ($module == 'base') {
			return GO::user()->isAdmin() ? GO_Base_Model_Acl::MANAGE_PERMISSION : false;
		}else
			return GO::modules()->$module->permissionLevel;
	}

	/**
	 * Returns the permission level if an aclField is defined in the model. Otherwise
	 * it returns -1;
	 * 
	 * @return int GO_Base_Model_Acl::*_PERMISSION 
	 */
	
	public function getPermissionLevel(){
		
		if(GO::$ignoreAclPermissions)
			return GO_Base_Model_Acl::MANAGE_PERMISSION;
		
		if(!$this->aclField())
			return -1;	
		
		if(!GO::user())
			return false;
		
		//if($this->isNew && !$this->joinAclField){
		if(empty($this->{$this->aclField()}) && !$this->joinAclField){
			return $this->getPermissionLevelForNewModel();
		}else
		{		
			if(!isset($this->_permissionLevel)){

				$acl_id = $this->findAclId();
				if(!$acl_id){
					throw new Exception("Could not find ACL for ".$this->className()." with pk: ".$this->pk);
				}

				$this->_permissionLevel=GO_Base_Model_Acl::getUserPermissionLevel($acl_id);// model()->findByPk($acl_id)->getUserPermissionLevel();
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
		
		unset($params['start'], $params['orderDirection'], $params['order']);
		if(isset($params['criteriaObject'])){
			$params['criteriaParams']=$params['criteriaObject']->getParams();
			$params['criteriaParams']=$params['criteriaObject']->getCondition();
			unset($params['criteriaObject']);
		}
		//GO::debug($params);
		return md5(serialize($params).$this->className());
	}
	
	/**
	 * Finds a single model by an attribute name and value.
	 * This function does NOT check permissions! Use find() if you need that.
	 * 
	 * @param string $attributeName
	 * @param mixed $value
	 * @param GO_Base_Db_FindParams $findParams Extra parameters to send to the find function.
	 * @return GO_Base_Db_ActiveRecord 
	 */
	public function findByAttribute($attributeName, $value, $findParams=false){		
		return $this->findByAttributes(array($attributeName=>$value), $findParams);
	}
	
	/**
	 * Finds models by an attribute=>value array.
	 * This function does NOT check permissions! Use find() if you need that.
	 * 
	 * @param array $attributes
	 * @param GO_Base_Db_FindParams $findParams
	 * @return GO_Base_Db_ActiveStatement 
	 */
	public function findByAttributes($attributes, $findParams=false){
		$newParams = GO_Base_Db_FindParams::newInstance();
		$criteria = $newParams->getCriteria()->addModel($this);
		
		foreach($attributes as $attributeName=>$value)
			$criteria->addCondition($attributeName, $value);
		
		if($findParams)
			$newParams->mergeWith ($findParams);
		
		$newParams->ignoreAcl();
				
		return $this->find($newParams);
	}
	
	/**
	 * Finds a single model by an attribute name and value.
	 * 
	 * @param string $attributeName
	 * @param mixed $value
	 * @param GO_Base_Db_FindParams $findParams Extra parameters to send to the find function.
	 * @return GO_Base_Db_ActiveRecord 
	 */
	public function findSingleByAttribute($attributeName, $value, $findParams=false){		
		return $this->findSingleByAttributes(array($attributeName=>$value), $findParams);
	}
	
	
	/**
	 * Finds a single model by an attribute=>value array.
	 * 
	 * @param string $attributeName
	 * @param mixed $value
	 * @param array $findParams Extra parameters to send to the find function.
	 * @return GO_Base_Db_ActiveRecord 
	 */
	public function findSingleByAttributes($attributes, $findParams=false){
		
		$cacheKey = md5(serialize($attributes));
		//Use cache so identical findByPk calls are only executed once per script request
		$cachedModel =  GO::modelCache()->get($this->className(), $cacheKey);
		if($cachedModel)
			return $cachedModel;
		
		$newParams = GO_Base_Db_FindParams::newInstance();
		$criteria = $newParams->getCriteria()->addModel($this);
		
		foreach($attributes as $attributeName=>$value)
			$criteria->addCondition($attributeName, $value);
		
		if($findParams)
			$newParams->mergeWith ($findParams);
		
		$newParams->ignoreAcl()->limit(1);
				
		$stmt = $this->find($newParams);
		
		$model = $stmt->fetch();
		
		GO::modelCache()->add($this->className(), $model, $cacheKey);
		
		return $model;		
	}
	
	/**
	 * Finds a single model by an attribute name and value.
	 * 
	 * @todo FindSingleByAttributes should use this function when this one uses the FindParams object too.
	 * 
	 * @param string $attributeName
	 * @param mixed $value
	 * @param GO_Base_Db_FindParams $findParams Extra parameters to send to the find function.
	 * @return GO_Base_Db_ActiveRecord 
	 */
	public function findSingle($findParams=array()){
		
		if(!is_array($findParams))
			$findParams = $findParams->getParams();
		
		$defaultParams=array('limit'=>1,'ignoreAcl'=>true);
		$params = array_merge($defaultParams, $findParams);
		
		$cacheKey = md5(serialize($params));
		//Use cache so identical findByPk calls are only executed once per script request
		$cachedModel =  GO::modelCache()->get($this->className(), $cacheKey);
		if($cachedModel)
			return $cachedModel;
				
		$stmt = $this->find($params);		
		$models = $stmt->fetchAll();
		
		$model = isset($models[0]) ? $models[0] : false;
		
		GO::modelCache()->add($this->className(), $model, $cacheKey);
		
		return $model;		
	}
	
	private function _getDefaultFindSelectFields($single=false){
		
		if($single)
			return 't.*';
		
		foreach($this->columns as $name=>$attr){
			if($attr['gotype']!='blob' && $attr['gotype']!='textarea')
				$fields[]=$name;
		}
		
		return "`t`.`".implode('`, `t`.`', $fields)."`";
	}
	

	/**
	 * Find models
	 * 
	 * Example usage:
	 * 
	 * <code>
	 * //create new find params object
	 * $params = GO_Base_Db_FindParams::newInstance()
	 *   ->joinCustomFields()
	 *   ->order('due_time','ASC');
	 * 
	 * //select all from tasklist id = 1
	 * $params->getCriteria()->addCondition('tasklist_id,1);
	 * 
	 * //find the tasks
	 * $stmt = GO_Tasks_Model_Task::model()->find($params);
	 * 
	 * //print the names
	 * while($task = $stmt->fetch()){
	 *	echo $task->name.'&lt;br&gt;';
	 * }
	 * </code>
	 * 
	 * 
	 * @param GO_Base_Db_FindParams $params
	 * @return GO_Base_Db_ActiveStatement
	 */
	public function find($params=array()){
	
		if(!is_array($params))
		{
			if(!($params instanceof GO_Base_Db_FindParams))
				throw new Exception('$params parameter for find() must be instance of GO_Base_Db_FindParams');
			
			if($params->getParam("export")){
				GO::session()->values[$params->getParam("export")]=array('name'=>$params->getParam("export"), 'model'=>$this->className(), 'findParams'=>$params);
			}
			
			//it must be a GO_Base_Db_FindParams object
			$params = $params->getParams();
		}
		
		if(!empty($params['single'])){
			unset($params['single']);
			return $this->findSingle($params);
		}
				
		if(!empty($params['debugSql'])){
			$this->_debugSql=true;
			//GO::debug($params);
		}
		
		
		if(GO::$ignoreAclPermissions)
			$params['ignoreAcl']=true;
		
		if(empty($params['userId'])){			
			$params['userId']=!empty(GO::session()->values['user_id']) ? GO::session()->values['user_id'] : 1;
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
		
		if(!empty($params['distinct']))
			$sql .= "DISTINCT ";
		
		if(!empty($params['calcFoundRows']) && !empty($params['limit']) && empty($params['start'])){
			
			//TODO: This is MySQL only code			
			$sql .= "SQL_CALC_FOUND_ROWS ";
		}
		
		
		if(empty($params['fields'])){
			$params['fields']=$this->_getDefaultFindSelectFields(isset($params['limit']) && $params['limit']==1);
			$fetchObject= true;
		}else
		{
			$fetchObject = strpos($params['fields'],'t.*')!==false || strpos($params['fields'],'t.id')!==false;
		}
		
		$sql .= $params['fields'].$aclJoin['fields'].' ';
		

		$joinCf = !empty($params['joinCustomFields']) && $this->customfieldsModel() && GO::modules()->customfields && GO::modules()->customfields->permissionLevel;
		
		if($joinCf){
			
			$cfModel = GO::getModel($this->customfieldsModel());
			
			$sql .= ",cf.* ";
		}
		
		
		$sql .= "\nFROM `".$this->tableName()."` t ".$aclJoin['join'];
		
		
		if (!empty($params['linkModel'])) { //passed in case of a MANY_MANY relation query
      $linkModel = new $params['linkModel'];
      $primaryKeys = $linkModel->primaryKey();
			
			if(!is_array($primaryKeys))
				throw new Exception ("Fatal error: Primary key of linkModel '".$params['linkModel']."' in relation '".$params['relation']."' should be an array.");
			
      $remoteField = $primaryKeys[0]==$params['linkModelLocalField'] ? $primaryKeys[1] : $primaryKeys[0];
      $sql .= "\nINNER JOIN `".$linkModel->tableName()."` link_t ON t.`".$this->primaryKey()."`= link_t.".$remoteField.' ';
    }
    
		
		if($joinCf)			
			$sql .= "\nLEFT JOIN `".$cfModel->tableName()."` cf ON cf.model_id=t.id ";	
		  
		if($this->aclField() && empty($params['ignoreAcl']))
			$sql .= $this->_appendAclJoin($params, $aclJoin);
			
		if(isset($params['join']))
			$sql .= "\n".$params['join'];
		
		//quick and dirty way to use and in next sql build blocks
		$sql .= "\nWHERE 1 ";

    
		if(isset($params['criteriaObject'])){
			$conditionSql = $params['criteriaObject']->getCondition();
			if(!empty($conditionSql))
				$sql .= "\nAND".$conditionSql;
		}
		
//		if(!empty($params['criteriaSql']))
//			$sql .= $params['criteriaSql'];
		
		$sql = self::_appendByParamsToSQL($sql, $params);
		
		if(isset($params['where']))
			$sql .= "\nAND ".$params['where'];
    
    if(isset($linkModel)){
      //$primaryKeys = $linkModel->primaryKey();
      //$remoteField = $primaryKeys[0]==$params['linkModelLocalField'] ? $primaryKeys[1] : $primaryKeys[0];
      $sql .= " \nAND link_t.`".$params['linkModelLocalField']."` = ".intval($params['linkModelLocalPk'])." ";
    }
		
		if(!empty($params['searchQuery'])){
			$sql .= " \nAND (";
			
			if(empty($params['searchQueryFields']))
				$fields = $this->getFindSearchQueryParamFields('t',$joinCf);
			else
				$fields = $params['searchQueryFields'];
			
			
			if(empty($fields))
				throw new Exception("No automatic search fields defined for ".$this->className().". Maybe this model has no varchar fields? You can override function getFindSearchQueryParamFields() or you can supply them with GO_Base_Db_FindParams::searchFields()");
			
			//`name` LIKE "test" OR `content` LIKE "test"
			
			$first = true;
			foreach($fields as $field){
				if($first){
					$first=false;
				}else
				{
					$sql .= ' OR ';
				}
				$sql .= $field.' LIKE '.$this->getDbConnection()->quote($params['searchQuery'], PDO::PARAM_STR);
			}			
			
			
			$sql .= ') ';
		}
		
		
		if($this->aclField() && empty($params['ignoreAcl'])){
			
			$pk = is_array($this->primaryKey()) ? $this->primaryKey() : array($this->primaryKey());
			
			$sql .= "\nGROUP BY t.`".implode('`,t.`', $pk)."` ";			
			if(isset($params['group']))
				$sql .= ", ";
			
							
		}elseif(isset($params['group'])){
			$sql .= "\nGROUP BY ";
		}
		
		if(isset($params['group'])){
			if(!is_array($params['group']))
				$params['group']=array($params['group']);
			
			for($i=0;$i<count($params['group']);$i++){
				if($i>0)
					$sql .= ', ';
				
				$sql .= $this->_quoteColumnName($params['group'][$i]).' ';
			}
		}
		
		if(isset($params['having']))
			$sql.="\nHAVING ".$params['having'];
		
		if(!empty($params['order'])){
			$sql .= "\nORDER BY ";
			
			if(!is_array($params['order']))
				$params['order']=array($params['order']);
			
			if(!isset($params['orderDirection'])){
				$params['orderDirection']=array('ASC');
			}elseif(!is_array($params['orderDirection'])){
				$params['orderDirection']=array($params['orderDirection']);
			}
			
			for($i=0;$i<count($params['order']);$i++){
				if($i>0)
					$sql .= ',';
				
				$sql .= $this->_quoteColumnName($params['order'][$i]).' ';
				if(isset($params['orderDirection'][$i])){
					$sql .= strtoupper($params['orderDirection'][$i])=='ASC' ? 'ASC ' : 'DESC ';
				}
			}
		}
		
		if(!empty($params['limit'])){
			if(!isset($params['start']))
				$params['start']=0;
			
			$sql .= "\nLIMIT ".intval($params['start']).','.intval($params['limit']);
		}
		
		if($this->_debugSql)
			$this->_debugSql($params, $sql);
		

		try{
			
			$result = $this->getDbConnection()->prepare($sql);
			
			if(isset($params['criteriaObject'])){
				$criteriaObjectParams = $params['criteriaObject']->getParams();
				
				foreach($criteriaObjectParams as $param=>$value)
					$result->bindValue($param, $value[0], $value[1]);
				
				$result->execute();
			}elseif(isset($params['bindParams'])){			
				$result = $this->getDbConnection()->prepare($sql);				
				$result->execute($params['bindParams']);
			}else
			{
				$result = $this->getDbConnection()->query($sql);
			}
		}catch(Exception $e){
			$msg = $e->getMessage();
						
			if(GO::config()->debug){
				$msg .= "\n\nFull SQL Query: ".$sql;

				if(isset($params['bindParams'])){	
					$msg .= "\nbBind params: ".var_export($params['bindParams'], true);
				}

				if(isset($criteriaObjectParams)){
					$msg .= "\nbBind params: ".var_export($criteriaObjectParams, true);
				}

				$msg .= "\n\n".$e->getTraceAsString();

				GO::debug($msg);
			}
			throw new Exception($msg);
		}

		
		if(!empty($params['calcFoundRows'])){
			if(!empty($params['limit'])){
				
				$queryUid = $this->_getFindQueryUid($params);
				
				//Total numbers are cached in session when browsing through pages.
				if(empty($params['start']) || !isset(GO::session()->values[$queryUid])){
					//TODO: This is MySQL only code
					$sql = "SELECT FOUND_ROWS() as found;";			
					$r2 = $this->getDbConnection()->query($sql);
					$record = $r2->fetch(PDO::FETCH_ASSOC);
					//$foundRows = intval($record['found']);
					$foundRows = GO::session()->values[$queryUid]=intval($record['found']);	
				}
				else
				{
					$foundRows=GO::session()->values[$queryUid];
				}
						
			}else
			{
				$foundRows = $result->rowCount();       
      }	
      $result->foundRows=$foundRows;
		}
		
		//$result->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $this->className());
		if($fetchObject)
			$result->setFetchMode(PDO::FETCH_CLASS, $this->className(),array(false));
		else
			$result->setFetchMode (PDO::FETCH_ASSOC);
    
    //TODO these values should be set on findByPk too.
    $result->model=$this;
    $result->findParams=$params;
    if(isset($params['relation']))
      $result->relation=$params['relation'];    

    return $result;		
	}
	
	private function _debugSql($params, $sql){
		
		if(isset($params['criteriaObject'])){
			$criteriaObjectParams = $params['criteriaObject']->getParams();	
				
			//sort so that :param1 does not replace :param11 first.
			arsort($criteriaObjectParams);	
			
			foreach($criteriaObjectParams as $param=>$value)
				$sql = str_replace($param, '"'.$value[0].'"', $sql);									
		}
		
		if(isset($params['bindParams'])){		
			
			//sort so that :param1 does not replace :param11 first.
			arsort($params['bindParams']);			
			
			foreach($params['bindParams'] as $key=>$value)
				$sql = str_replace(':'.$key, '"'.$value.'"', $sql);
		}
		
		GO::debug($sql);				
	}
	
	private function _appendAclJoin($findParams, $aclJoin){		
		
		$sql = "\nINNER JOIN go_acl ON (`".$aclJoin['table']."`.`".$aclJoin['aclField']."` = go_acl.acl_id";
		if(isset($findParams['permissionLevel']) && $findParams['permissionLevel']>GO_Base_Model_Acl::READ_PERMISSION){
			$sql .= " AND go_acl.level>=".intval($findParams['permissionLevel']);
		}
		
		$groupIds = GO_Base_Model_User::getGroupIds($findParams['userId']);
		
		if(!empty($findParams['ignoreAdminGroup'])){
			$key = array_search(GO::config()->group_root, $groupIds);
			if($key!==false)
				unset($groupIds[$key]);
		}
		
		
		$sql .= " AND (go_acl.user_id=".intval($findParams['userId'])." OR go_acl.group_id IN (".implode(',',$groupIds)."))) ";		
		
		return $sql;
	}
	
	private function _quoteColumnName($name){
	
		//disallow \ ` and \00  : http://stackoverflow.com/questions/1542627/escaping-field-names-in-pdo-statements
		if(preg_match("/[`\\\\\\000\(\),]/", $name))
			throw new Exception("Invalid characters found in column name: ".$name);
		
		$arr = explode('.',$name);
		
//		for($i=0,$max=count($arr);$i<$max;$i++)
//			$arr[$i]=$this->getDbConnection ()->quote($arr[$i], PDO::PARAM_STR);
		
		return '`'.implode('`.`',$arr).'`';
	}
	
	private function _appendByParamsToSQL($sql, $params){
		if(!empty($params['by'])){

			if(!isset($params['byOperator']))
				$params['byOperator']='AND';

			$first=true;
			$sql .= "\nAND (";
			foreach($params['by'] as $arr){
				
				$field = $arr[0];
				$value= $arr[1];
				$comparator=isset($arr[2]) ? strtoupper($arr[2]) : '=';

				if($first)
				{
					$first=false;
				}else
				{
					$sql .= $params['byOperator'].' ';
				}
				
				if($comparator=='IN' || $comparator=='NOT IN'){
					
					//prevent sql error on empty value
					if(!count($value))
						$value=array(0);
					
					for($i=0;$i<count($value);$i++)
						$value[$i]=$this->getDbConnection()->quote($value[$i], $this->columns[$field]['type']);

					$sql .= "t.`$field` $comparator (".implode(',',$value).") ";
					
						
				}else
				{
					if(!isset($this->columns[$field]['type']))
						throw new Exception($field.' not found in columns for model '.$this->className());
					
          $sql .= "t.`$field` $comparator ".$this->getDbConnection()->quote($value, $this->columns[$field]['type'])." ";
				}
			}

			$sql .= ') ';
		}
		return $sql;
	}
	
	/**
	 * Override this method to supply the fields that the searchQuery argument 
	 * will usein the find function.
	 * 
	 * By default all fields with type PDO::PARAM_STR are returned
	 * 
	 * @return array Field names that should be used for the search query.
	 */
	public function getFindSearchQueryParamFields($prefixTable='t', $withCustomFields=true){
		//throw new Exception('Error: you supplied a searchQuery parameter to find but getFindSearchQueryParamFields() should be overriden in '.$this->className());
		$fields = array();
		foreach($this->columns as $field=>$attributes){
//			if(isset($attributes['gotype']) && ($attributes['gotype']=='textfield' || $attributes['gotype']=='textarea')){
//				$fields[]='`'.$prefixTable.'`.`'.$field.'`';
//			}
			if($field == 'keywords')
				$fields[]='`'.$prefixTable.'`.`'.$field.'`';
		}
		
		if($withCustomFields && $this->customfieldsRecord)
		{
			$fields = array_merge($fields, $this->customfieldsRecord->getFindSearchQueryParamFields('cf'));
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
			
			if(!is_array($primaryKey)){
				throw new Exception('Primary key should be an array for the model '.$this->className());
			}
			
			$first = true;
			foreach($primaryKey as $field=>$value){
				$this->$field=$value;
				if(!$first)
					$sql .= ' AND ';
				else
					$first=false;
				
				if(!isset($this->columns[$field])){
					throw new Exception($field.' not found in columns of '.$this->className());
				}
				
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
	 * Loads the model attributes from the database. It also automatically checks
	 * read permission for the current user.
	 * 
	 * @param int $primaryKey
	 * @return GO_Base_Db_ActiveRecord 
	 */
	
	public function findByPk($primaryKey, $findParams=false, $ignoreAcl=false, $noCache=false){		
		
		//GO::debug($this->className()."::findByPk($primaryKey)");
		if(empty($primaryKey))
			return false;
		
		//Use cache so identical findByPk calls are only executed once per script request
		if(!$noCache){
			$cachedModel =  GO::modelCache()->get($this->className(), $primaryKey);
			if($cachedModel)
				return $cachedModel;
		}
		
		$sql = "SELECT * FROM `".$this->tableName()."` WHERE ";
		
		$sql = $this->_appendPkSQL($sql, $primaryKey);
	
		
		if($this->_debugSql)
				GO::debug($sql);
		
		//try{
			$result = $this->getDbConnection()->query($sql);
			$result->model=$this;
			$result->findParams=$findParams;

			$result->setFetchMode(PDO::FETCH_CLASS, $this->className(),array(false));

			$models =  $result->fetchAll();
			$model = isset($models[0]) ? $models[0] : false;
		
			//todo check read permissions
			if($model && !$ignoreAcl && !$model->checkPermissionLevel(GO_Base_Model_Acl::READ_PERMISSION)){
				$msg = GO::config()->debug ? $this->className().' pk: '.var_export($this->pk, true) : '';
				throw new GO_Base_Exception_AccessDenied($msg);
			}

			if($model)
				GO::modelCache()->add($this->className(), $model);

			return $model;
//		}
//		Can't do this because an access denied exception must remain an accessdenied exception.
//		catch(Exception $e){
//			$msg = $e->getMessage()."\n\nFull SQL Query: ".$sql;			
//		
//			throw new Exception($msg);
//		}
		
		/**
		 * Useful event for modules. For example custom fields can be loaded or a files folder.
		 */
		//$GLOBALS['GO_EVENTS']->fire_event('loadactiverecord',array(&$this));
	}
	
	/**
	 * Return the number of model records in the database.
	 * 
	 * @return int  
	 */
	public function count(){
		$stmt = $this->getDbConnection()->query("SELECT count(*) AS count FROM `".$this->tableName()."`");
		$record = $stmt->fetch();
		return $record['count'];		
	}
	
	/**
	 * May be overriden to do stuff after the model was loaded from the database
	 */
	protected function afterLoad(){
			//GO::debug($this);
	}
	
	private function _relationExists($name){
		$r= $this->relations();
		
		return isset($r[$name]);		
	}
	
	protected function getRelation($name){
		$r= $this->relations();
		
		if(!isset($r[$name]))
			return false;
		
		return $r[$name];
	}
		
	private function _checkRelations($r){
		if(GO::config()->debug){
			foreach($r as $name => $attr){
				if(!isset($attr['model']))
					throw new Exception('model not set in relation '.$name.' '.var_export($attr, true));
		
				if(isset($this->columns[$name]))
					throw new Exception("Relation $name conflicts with column attribute in ".$this->className());
				
				$method = 'get'.ucfirst($name);
				if(method_exists($this, $method))
					throw new Exception("Relation $name conflicts with getter function $method in ".$this->className());
				
				if($attr['type']==self::BELONGS_TO && !empty($attr['delete'])){
					throw new Exception("BELONGS_TO Relation $name may not have a delete flag in ".$this->className());
				}				
			}
		}
	}
	
	private function _getRelated($name, $extraFindParams=array()){
		 //$name::findByPk($hit-s)
		$r= $this->relations();
		
		$this->_checkRelations($r);
		
		if(!isset($r[$name]))
			return false;
				
		$model = $r[$name]['model'];
		
		if(!isset($r[$name]['findParams']))
			$r[$name]['findParams']=array();
		
		if($r[$name]['type']==self::BELONGS_TO){
		
			$joinAttribute = $r[$name]['field'];
			
			/**
			 * Related stuff can be put in the relatedCache array for when a relation is
			 * accessed multiple times.
			 * 
			 * Related stuff can also be joined in a query and be passed to the __set 
			 * function as relation@relation_attribute. This array will be used here to
			 * construct the related model.
			 */
			
			//append join attribute so cache is void automatically when this attribute changes.
			$cacheKey = $name.':'.(isset($this->_attributes[$joinAttribute]) ? $this->_attributes[$joinAttribute] : 0);
				
			if(isset($this->_relatedCache[$cacheKey])){			

				if(is_array($this->_relatedCache[$cacheKey])){
					$attr = $this->_relatedCache[$cacheKey];

					$this->_relatedCache[$cacheKey]=new $model;
					$this->_relatedCache[$cacheKey]->setAttributes($attr, false);					
				}				
				
			}else
			{	
				//In a belongs to relationship the primary key of the remote model is stored in this model in the attribute "field".
				$this->_relatedCache[$cacheKey] = !empty($this->_attributes[$joinAttribute]) ? GO::getModel($model)->findByPk($this->_attributes[$joinAttribute], array('relation'=>$name), true) : false;
			}
			return $this->_relatedCache[$cacheKey];
			
		}elseif($r[$name]['type']==self::HAS_ONE){			
			//We can't put this in the related cache because there's no reliable way to check if the situation has changed.
		
			//In a has one to relation ship the primary key of this model is stored in the "field" attribute of the related model.					
			return empty($this->pk) ? false : GO::getModel($model)->findSingleByAttribute($r[$name]['field'], $this->pk, array('relation'=>$name,'debugSql'=>true));			
		}elseif($r[$name]['type']==self::HAS_MANY)
		{									
			$remoteFieldThatHoldsMyPk = $r[$name]['field'];
			
			$findParams = GO_Base_Db_FindParams::newInstance()
					->mergeWith($r[$name]['findParams'])
					->mergeWith($extraFindParams)					
					->ignoreAcl()
					->relation($name);
					
			$findParams->getCriteria()							
							->addModel(GO::getModel($model))
							->addCondition($remoteFieldThatHoldsMyPk, $this->pk);
			
//			$findParams = array_merge($extraFindParams,$r[$name]['findParams'],array(
//					"by"=>array(array($remoteFieldThatHoldsMyPk,$this->pk,'=')),
//					"ignoreAcl"=>true,
//          "relation"=>$name
//			));
//				
			$stmt = GO::getModel($model)->find($findParams);
			return $stmt;		
		}elseif($r[$name]['type']==self::MANY_MANY)
		{							
			$localPkField = $r[$name]['field'];
      $linkModelName = $r[$name]['linkModel']; // name where the local id is linked to the ids of the records in the remote table
			
			$findParams = GO_Base_Db_FindParams::newInstance()
					->mergeWith($extraFindParams)
					->mergeWith($r[$name]['findParams'])
					->ignoreAcl()
					->relation($name)
					->linkModel($r[$name]['linkModel'], $r[$name]['field'], $this->pk);
				
			$stmt = GO::getModel($model)->find($findParams); // pakt alle records waarvan de ids via de koppeltabel gelinked zijn aan de local id
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
			$formatted[$key]=$this->formatInput($key, $value);			
		}
		return $formatted;
	}
	
	/**
	 * Formats user input for the database.
	 * 
	 * @param string $column
	 * @param mixed $value
	 * @return array 
	 */
	public function formatInput($column, $value){
			if(!isset($this->columns[$column]['gotype'])){
				//don't process unknown columns. But keep them for flexibility.
				return $value;				
			}

			switch($this->columns[$column]['gotype']){
				case 'unixdate':
				case 'unixtimestamp':
					return  GO_Base_Util_Date::to_unixtime($value);
					break;			
				case 'number':
					return  GO_Base_Util_Number::unlocalize($value);
					break;
				case 'boolean':
					return  empty($value) ? 0 : 1; 
					break;				
				case 'date':
					return  GO_Base_Util_Date::to_db_date($value);
					break;		
				case 'textfield':
					return trim($value);
					break;
				default:
					if($this->columns[$column]['type']==PDO::PARAM_INT)
						$value = intval($value);
					
					return  $value;
					break;
			}
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
		foreach($attributes as $attributeName=>$value){			
			$formatted[$attributeName]=$this->formatAttribute($attributeName, $value, $html);
		}
		
		return $formatted;
	}	
	
	public function formatAttribute($attributeName, $value, $html=false){
		if(!isset($this->columns[$attributeName]['gotype'])){			

			if(false && $this->customfieldsModel() && substr($attributeName,0,4)=='col_'){
				//if it's a custom field then we create a dummy customfields model.
				$cfModel = $this->_createCustomFieldsRecordFromAttributes();				
				return $cfModel->formatAttribute($attributeName, $value);
			}else	{
				return $value;
			}
		}

		switch($this->columns[$attributeName]['gotype']){
				
			case 'unixdate':
				return GO_Base_Util_Date::get_timestamp($value, false);
				break;	

			case 'unixtimestamp':
				return GO_Base_Util_Date::get_timestamp($value);
				break;	

			case 'textarea':
				if($html){
					return GO_Base_Util_String::text_to_html($value);
				}else
				{
					return $value;
				}
				break;

			case 'date':
				//strtotime hangs a while on parsing 0000-00-00 from the database. There shouldn't be such a date in it but 
				//the old system stored dates like this.
				return $value != '0000-00-00' ? GO_Base_Util_Date::get_timestamp(strtotime($value),false) : '';
				break;

			case 'number':
				$decimals = isset($this->columns[$attributeName]['decimals']) ? $this->columns[$attributeName]['decimals'] : 2;
				return GO_Base_Util_Number::localize($value, $decimals);
				break;
			
			case 'boolean':
					return !empty($value);				
				break;
			
			case 'html':
				return $value;
				break;
			default:
				return $html ? htmlspecialchars($value, ENT_COMPAT,'UTF-8') : $value;
				break;
		}		
	}
	
	
	private function _hasCustomfieldValue($attributes){
		foreach($attributes as $key=>$value)
		{
			if(substr($key,0,4)=='col_'){
				return true;
			}
		}
		return false;
	}
	
	/**
	 * This function is used to set attributes of this model from a controller.
	 * Input may be in regional format and the model will translate it to the
	 * database format.
	 * 
	 * All attributes will be set even if the attributes don't exist in the model.
	 * The only exception if for relations. You can't set an attribute named 
	 * "someRelation" if it exists in the relations.
	 * 
	 * The attributes array may also contain custom fields. They will be saved
	 * automatically.
	 * 
	 * @param array $attributes attributes to set on this object
	 */
	
	public function setAttributes($attributes, $format=true){		
		
		//GO::debug($this->className().'::setAttributes(); '.$this->pk);
		
		if($this->_hasCustomfieldValue($attributes) && $this->customfieldsRecord)
			$this->customfieldsRecord->setAttributes($attributes, $format);
		
		//$related=array();
		
		if($format)
			$attributes = $this->formatInputValues($attributes);
		
		$relations = $this->relations();
		
		foreach($attributes as $key=>$value){
			//don't set a value for a relation. Otherwise getting the relation won't
			//work anymore.
			if(!isset($relations[$key]))
				$this->$key=$value;			
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
			$att=$this->_attributes;
		else
			$att=$this->formatOutputValues($this->_attributes, $outputType=='html');		

		foreach($this->_getMagicAttributeNames() as $attName){
			$att[$attName]=$this->$attName;
		}
		
		return $att;
	}
	
	private $_magicAttributeNames;
	
	private function _getMagicAttributeNames(){
		if(!isset($this->_magicAttributeNames)){
			$this->_magicAttributeNames=array();
			$r = new ReflectionObject($this);
			$publicProperties = $r->getProperties(ReflectionProperty::IS_PUBLIC);
			foreach($publicProperties as $prop){
				//$att[$prop->getName()]=$prop->getValue($this);
				//$prop = new ReflectionProperty();
				if(!$prop->isStatic())
					$this->_magicAttributeNames[]=$prop->getName();
			}
			
//			$methods = $r->getMethods();
//			
//			foreach($methods as $method){
//				$methodName = $method->getName();
//				if(substr($methodName,0,3)=='get' && !$method->getNumberOfParameters()){
//					
//					echo $propName = strtolower(substr($methodName,3,1)).substr($methodName,4);
//					
//					$this->_magicAttributeNames[]=$propName;
//				}
//			}
//			
		}
		return $this->_magicAttributeNames;
	}
	
	/**
	 * Returns all columns 
	 * 
	 * @see GO_Base_Db_ActiveRecord::$columns	
	 * @return array
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
	public function checkPermissionLevel($level){

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
				
		//foreach($this->columns as $field=>$attributes){
		
		$fieldsToCheck = $this->isNew ? array_keys($this->columns) : array_keys($this->getModifiedAttributes());
		
		foreach($fieldsToCheck as $field){
			
			$attributes=$this->columns[$field];
			
			if(!empty($attributes['required']) && empty($this->_attributes[$field])){				
				$this->setValidationError($field, sprintf(GO::t('attributeRequired'),$this->getAttributeLabel($field)));				
			}elseif(!empty($attributes['length']) && !empty($this->_attributes[$field]) && GO_Base_Util_String::length($this->_attributes[$field])>$attributes['length'])
			{
				$this->setValidationError($field, sprintf(GO::t('attributeTooLong'),$this->getAttributeLabel($field),$attributes['length']));
			}elseif(!empty($attributes['regex']) && !empty($this->_attributes[$field]) && !preg_match($attributes['regex'], $this->_attributes[$field]))
			{
				$this->setValidationError($field, sprintf(GO::t('attributeIncorrectFormat'),$this->getAttributeLabel($field)));
			}elseif(!empty($attributes['validator']) && !empty($this->_attributes[$field]) && !call_user_func($attributes['validator'], $this->_attributes[$field]))
			{
				$this->setValidationError($field, sprintf(GO::t('attributeInvalid'),$this->getAttributeLabel($field)));
			}
		}
		
		$this->_validateUniqueColumns();
		
		$errors = $this->getValidationErrors();
		if(!empty($errors)){
			return false;
		}
		
		return true;
	}
	
	private function _validateUniqueColumns(){
		foreach($this->columns as $field=>$attributes){
		
			if(!empty($attributes['unique']) && !empty($this->_attributes[$field])){
				
				$relatedAttributes = array($field);
				if(is_array($attributes['unique']))
					$relatedAttributes = array_merge($relatedAttributes,$attributes['unique']);
				
				$modified = false;
				foreach($relatedAttributes as $relatedAttribute){
					if($this->isModified($relatedAttribute))
						$modified=true;
				}
				
				if($modified){
					$criteria = GO_Base_Db_FindCriteria::newInstance()
								->addModel(GO::getModel($this->className()))
								->addCondition($field, $this->_attributes[$field]);

					if(is_array($attributes['unique'])){
						foreach($attributes['unique'] as $f)
							$criteria->addCondition($f, $this->_attributes[$f]);
					}

					if(!$this->isNew)
						$criteria->addCondition($this->primaryKey(), $this->pk, '!=');

					$existing = $this->findSingle(GO_Base_Db_FindParams::newInstance()
									->ignoreAcl()
									->criteria($criteria)
					);

					if($existing)
						$this->setValidationError($field, sprintf(GO::t('alreadyExists'),$this->localizedName, $this->_attributes[$field]));
				}
			}
		}
	}
	
	/**
	 * Return all validation errors of this model
	 * 
	 * @return type 
	 */
	public function getValidationErrors(){
		return $this->_validationErrors;
	}
	
	/**
	 * Get the validationError for the given attribute
	 * If the attribute has no error then fals will be returned
	 * 
	 * @param string $attribute
	 * @return mixed 
	 */
	public function getValidationError($attribute){
		if(!empty($this->_validationErrors[$attribute]))
			return $this->_validationErrors[$attribute];
		else
			return false;
	}
	
	/**
	 * Set a validation error for the given field.
	 * 
	 * @param string $attribute Set to 'form' for a general form error.
	 * @param string $message 
	 */
	protected function setValidationError($attribute,$message) {
		$this->_validationErrors[$attribute] = $message;
	}
	
	
//	public function getFilesFolder(){
//		if(!$this->hasFiles())
//			throw new Exception("getFilesFolder() called on ".$this->className()." but hasFiles() is false for this model.");
//		
//		if($this->files_folder_id==0)
//			return false;
//		
//		return GO_Files_Model_Folder::model()->findByPk($this->files_folder_id);
//		
//	}
	
	/**
	 * Get the column name of the field this model sorts on.
	 * It will automatically give the highest number to new models.
	 * Useful in combination with GO_Base_Controller_AbstractModelController::actionSubmitMultiple().
	 * Drag and drop actions will save the sort order in that action.
	 * 
	 * @return string 
	 */
	public function getSortOrderColumn(){
		return false;
	}


	/**
	 * Saves the model to the database
	 * 
	 * @return boolean 
	 */
	
	public function save(){
			
		//GO::debug('save'.$this->className());
			
		if(!$this->checkPermissionLevel($this->isNew?GO_Base_Model_Acl::CREATE_PERMISSION:GO_Base_Model_Acl::WRITE_PERMISSION)){
			$msg = GO::config()->debug ? $this->className().' pk: '.var_export($this->pk, true) : '';
			throw new GO_Base_Exception_AccessDenied($msg);
		}
		
		if(!$this->validate()){
			$errors = $this->getValidationErrors();
			throw new GO_Base_Exception_Validation(sprintf(GO::t('validationErrorsFound'),strtolower($this->localizedName))."\n\n".implode("\n", $errors)."\n");			
		}
		
		//Don't do anything if nothing has been modified.
		if(!$this->_forceSave && !$this->isNew && !$this->isModified() && (!$this->customfieldsRecord || !$this->customfieldsRecord->isModified()))
			return true;


		/*
		 * Set some common column values
		*/
//GO::debug($this->mtime);
		if(isset($this->columns['mtime']) && (!$this->isModified('mtime') || empty($this->mtime)))//Don't update if mtime was manually set.
			$this->mtime=time();
		if(isset($this->columns['ctime']) && empty($this->ctime)){
			$this->ctime=time();
		}

		//user id is set by defaultAttributes now.
		//do not use empty() here for checking the user id because some times it must be 0. eg. go_acl
//		if(isset($this->columns['user_id']) && !isset($this->user_id)){
//			$this->user_id=GO::user() ? GO::user()->id : 1;
//		}


		/**
		 * Useful event for modules. For example custom fields can be loaded or a files folder.
		 */
		$this->fireEvent('beforesave',array(&$this));




		if($this->isNew){		
			
			//automatically set sort order column
			if($this->getSortOrderColumn())
				$this->{$this->getSortOrderColumn()}=$this->count();

			$wasNew=true;

			if($this->aclField() && !$this->joinAclField && empty($this->{$this->aclField()})){
				//generate acl id				

				$this->setNewAcl(!empty($this->user_id) ? $this->user_id : 1);
			}				
			
			if ($this->hasFiles() && GO::modules()->isInstalled('files')) {
				//ACL must be generated here.
				$fc = new GO_Files_Controller_Folder();
				$this->files_folder_id = $fc->checkModelFolder($this);
			}

			if(!$this->beforeSave()){
				GO::debug("WARNING: ".$this->className()."::beforeSave returned false or no value");
				return false;				
			}

			$this->_dbInsert();
			
			if(!is_array($this->primaryKey()) && empty($this->pk))
				$this->{$this->primaryKey()} = $this->getDbConnection()->lastInsertId();

			if(!$this->pk)
				return false;

			$this->setIsNew(false);
			
			if($this->afterDbInsert()){
				$this->_dbUpdate();
			}
		}else
		{
			$wasNew=false;
			
			
			if ($this->hasFiles() && GO::modules()->isInstalled('files')) {
				//ACL must be generated here.
				$fc = new GO_Files_Controller_Folder();
				$this->files_folder_id = $fc->checkModelFolder($this);
			}

			if(!$this->beforeSave()){
				GO::debug("WARNING: ".$this->className()."::beforeSave returned false or no value");
				return false;				
			}


			if(!$this->_dbUpdate())
				return false;
		}


		if ($this->customfieldsRecord){
			//id is not set if this is a new record so we make sure it's set here.
			$this->customfieldsRecord->model_id=$this->id;

			$this->customfieldsRecord->save();
		}
		
		$this->_log($wasNew ? GO_Log_Model_Log::ACTION_ADD : GO_Log_Model_Log::ACTION_UPDATE);
		
		

		if(!$this->afterSave($wasNew)){
			GO::debug("WARNING: ".$this->className()."::afterSave returned false or no value");
			return false;
		}
		
		if(!$wasNew)
			$this->_fixLinkedEmailAcls();

		/**
		 * Useful event for modules. For example custom fields can be loaded or a files folder.
		 */
		$this->fireEvent('save',array(&$this,$wasNew));


		$this->cacheSearchRecord();

		$this->_modifiedAttributes = array();

		return true;
	}
	
	/**
	 * Get the message for the log module. Returns the contents of the first text column by default.
	 * 
	 * @return string 
	 */
	public function getLogMessage($action){
		
		$attr = $this->getCacheAttributes();
		if($attr){
			$msg = $attr['name'];
			if(isset($attr['description']))
				$msg.="\n".$attr['description'];
			return substr($msg,0,255);
		}else
			return false;
	}
	
	private function _log($action){
	
		$message = $this->getLogMessage($action);
		if($message && GO::modules()->isInstalled('log')){			
			$log = new GO_Log_Model_Log();
			
			$pk = $this->pk;
			$log->model_id=is_array($pk) ? var_export($pk, true) : $pk;
			
			$log->action=$action;
			$log->model=$this->className();			
			$log->message = $message;
			$log->save();
		}
	}
	
	/**
	 * Acl id's of linked emails are copies from the model they are linked too. 
	 * For example an e-mail linked to a contact will get the acl id of the addressbook.
	 * When you move a contact to another contact all the acl id's must change. 
	 */
	private function _fixLinkedEmailAcls(){
		if($this->hasLinks() && GO::modules()->isInstalled('savemailas')){
			$arr = explode('.', $this->aclField());
			if (count($arr) > 1) {
				
				$relation = $this->getRelation($arr[0]);
				
				if($relation && $this->isModified($relation['field'])){
					//acl relation changed. We must update linked emails
					
					GO::debug("Fixing linked e-mail acl's because relation ".$relation['name']." changed.");
					
					$stmt = GO_Savemailas_Model_LinkedEmail::model()->findLinks($this);
					while($linkedEmail = $stmt->fetch()){
						
						GO::debug("Updating ".$linkedEmail->subject);
						
						$linkedEmail->acl_id=$this->findAclId();
						$linkedEmail->save();
					}
				}
			}
		}
	}
	
	
	/**
	 * Sometimes you need the auto incremented primary key to generate another
	 * property. Like the UUID of an event or task.
	 * Or in a project number for example where you want to generate a number 
	 * like PR00023 where 23 is the id for example.
	 * 
	 * @return boolean NOTE: Only return true if a database update is needed.
	 */
	protected function afterDbInsert(){
		return false;
	}
	
	
	/**
	 * Get a key value array of modified attribute names with their old values 
	 * that are not saved to the database yet.
	 * 
	 * e. array('attributeName'=>'Old value')
	 * 
	 * @return array 
	 */
	public function getModifiedAttributes(){
		return $this->_modifiedAttributes;
	}
	
	public function setNewAcl($user_id=0){
		
		if(!$user_id)
			$user_id = GO::user() ? GO::user()->id : 1;
		
		$acl = new GO_Base_Model_Acl();
		$acl->description=$this->tableName().'.'.$this->aclField();
		$acl->user_id=$user_id;
		$acl->save();

		$this->{$this->aclField()}=$acl->id;
		
		return $acl->id;
	}
	
	/**
	 * Check is this model or model attribute name has modifications not saved to
	 * the database yet.
	 * 
	 * @param type $attributeName
	 * @return boolean 
	 */
	public function isModified($attributeName=false){
		if(!$attributeName){
			return count($this->_modifiedAttributes)>0;
		}else
		{
			return isset($this->_modifiedAttributes[$attributeName]);
		}
	}

	/**
	 * Get the old value for a modified attribute.
	 * 
	 * @param String $attributeName
	 * @return mixed 
	 */
	public function getOldAttributeValue($attributeName){
		return isset($this->_modifiedAttributes[$attributeName]) ? $this->_modifiedAttributes[$attributeName] : false;
	}
	
	/**
	 * The files module will use this function. To create a files folder.
	 * Override it if you don't like the default path. Make sure this path is unique! Appending the (<id>) would be wise.
	 */
	public function buildFilesPath() {

		return isset($this->name) ? $this->getModule().'/' . GO_Base_Fs_Base::stripInvalidChars($this->name) : false;
	}
	
	/**
	 * Get the name of the module that this model belongs to.
	 * 
	 * @return string 
	 */
	public function getModule(){
		$arr = explode('_', $this->className());
		
		return strtolower($arr[1]);
	}
	
	/**
	 * Put this model in the go_search_cache table as a GO_Base_Model_SearchCacheRecord so it's searchable and linkable.
	 * Generally you don't need to do this. It's called from the save function automatically when getCacheAttributes is overridden.
	 * This method is only public so that the maintenance script can access it to rebuid the search cache.
	 * 
	 * @return boolean 
	 */
	public function cacheSearchRecord(){
		
		//don't do this on datbase checks.
		if(GO::router()->getControllerAction()=='checkdatabase')
			return;
		
		$attr = $this->getCacheAttributes();
		
		//GO::debug($attr);
		
		if($attr){

			$model = GO_Base_Model_SearchCacheRecord::model()->findByPk(array('model_id'=>$this->pk, 'model_type_id'=>$this->modelTypeId()),false,true);
			
			if(!$model)
				$model = new GO_Base_Model_SearchCacheRecord();
			
			$acl_id =$this->findAclId();
			
			//if model doesn't have an acl we use the acl of the module it belongs to.
			if(!$acl_id)
				$acl_id = GO::modules()->{$this->getModule ()}->acl_id;
			
			//GO::debug($model);
			$autoAttr = array(
				'model_id'=>$this->pk,
				'model_type_id'=>$this->modelTypeId(),
				'user_id'=>isset($this->user_id) ? $this->user_id : GO::session()->values['user_id'],
				'module'=>$this->module,
				'model_name'=>$this->className(),
				'name' => '',
				//'link_type'=>$this->modelTypeId(),
				'description'=>'',		
				'type'=>$this->localizedName, //deprecated, for backwards compatibilty
				'keywords'=>$this->getSearchCacheKeywords($this->localizedName),
				'mtime'=>$this->mtime,
				'ctime'=>$this->ctime,
				'acl_id'=>$acl_id
			);
			
			$attr = array_merge($autoAttr, $attr);
			
			//make sure these attributes are not too long
			if(GO_Base_Util_String::length($attr['name'])>100)
				$attr['name']=substr($attr['name'], 0, 100);
			
			if(GO_Base_Util_String::length($attr['description'])>255)
				$attr['description']=substr($attr['description'], 0, 255);
			
			//GO::debug($attr);

			$model->setAttributes($attr, false);
			$model->save();
			return $model;
			
		}
		return false;
	}
	
	public function getCachedSearchRecord(){
		$model = GO_Base_Model_SearchCacheRecord::model()->findByPk(array('model_id'=>$this->pk, 'model_type_id'=>$this->modelTypeId()));
		if($model)
			return $model;
		else
			return $this->cacheSearchRecord ();
	}
	
	/**
	 * Override this function if you want to put your model in the search cache.
	 * 
	 * @return array cache parameters with at least 'name', 'description' and 'type'. All are strings. See GO_Base_Model_SearchCacheRecord for more info.
	 */
	protected function getCacheAttributes(){
		return false;
	}
	
	/**
	 * Get keywords this model should be found on.
	 * Returns all String properties in a concatenated string.
	 * 
	 * @param String $prepend
	 * @return String 
	 */
	public function getSearchCacheKeywords($prepend=''){
		$keywords=array();

		foreach($this->columns as $key=>$attr)
		{
			if(isset($this->$key)){
				$value = $this->$key;
				if(($attr['gotype']=='textfield' || $attr['gotype']=='customfield' || $attr['gotype']=='textarea') && !in_array($value,$keywords)){
					if(!empty($value))
						$keywords[]=$value;
				}
			}
		}
		
		$keywords = $prepend.','.implode(',',$keywords);
		
		if($this->customfieldsRecord){
			$keywords .= ','.$this->customfieldsRecord->getSearchCacheKeywords();
		}
		
		return substr($keywords,0,255);
	}
	
	protected function beforeSave(){
		
		return true;
	}
	
	/**
	 * May be overridden to do stuff after save
	 * 
	 * @var bool $wasNew True if the model was new before saving
	 * @return boolean 
	 */
	protected function afterSave($wasNew){
		return true;
	}
	
	/**
	 * Inserts the model into the database
	 * 
	 * @return boolean 
	 */
	private function _dbInsert(){		

		$fieldNames = array();
		
		//Build an array of fields that are set in the object. Unset columns will
		//not be in the SQL query so default values from the database are respected.
		foreach($this->columns as $field=>$col){
			if(isset($this->_attributes[$field])){
				$fieldNames[]=$field;
			}
		}

		
		$sql = "INSERT ";
		
		if($this->insertDelayed)
			$sql .= "DELAYED ";
		
		$sql .= "INTO `{$this->tableName()}` (`".implode('`,`', $fieldNames)."`) VALUES ".
					"(:".implode(',:', $fieldNames).")";

		if($this->_debugSql)			
			$this->_debugSql(array('bindParams'=>$this->_attributes), $sql);		
		
		try{
			$stmt = $this->getDbConnection()->prepare($sql);

			foreach($fieldNames as  $field){

				$attr = $this->columns[$field];

				$stmt->bindParam(':'.$field, $this->_attributes[$field], $attr['type'], empty($attr['length']) ? null : $attr['length']);
			}
			$ret =  $stmt->execute();
		}catch(Exception $e){
			
			$msg = $e->getMessage();
						
			if(GO::config()->debug){
				$msg .= "\n\nFull SQL Query: ".$sql."\n\nParams:\n".var_export($this->_attributes, true);

				$msg .= "\n\n".$e->getTraceAsString();

				GO::debug($msg);
			}
			throw new Exception($msg);
		}
		
		return $ret;
	}
	

	private function _dbUpdate(){
		
		$updates=array();
		
		//$pks = is_array($this->primaryKey()) ? $this->primaryKey() : array($this->primaryKey());
//		foreach($this->columns as $field => $value)
//		{
//			if(!in_array($field,$pks))
//			{
//				$updates[] = "`$field`=:".$field;
//			}
//		}
//		
		foreach($this->_modifiedAttributes as $field=>$oldValue)
			$updates[] = "`$field`=:".$field;		
		
		
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
			$this->_debugSql(array('bindParams'=>$this->_attributes), $sql);

		try{
			$stmt = $this->getDbConnection()->prepare($sql);

			$pks = is_array($this->primaryKey()) ? $this->primaryKey() : array($this->primaryKey());

			foreach($this->columns as $field => $attr){

				if($this->isModified($field) || in_array($field, $pks))
					$stmt->bindParam(':'.$field, $this->_attributes[$field], $attr['type'], empty($attr['length']) ? null : $attr['length']);
			}
			$ret = $stmt->execute();
		}catch(Exception $e){
			$msg = $e->getMessage();
						
			if(GO::config()->debug){
				$msg .= "\n\nFull SQL Query: ".$sql."\n\nParams:\n".var_export($this->_attributes, true);

				$msg .= "\n\n".$e->getTraceAsString();

				GO::debug($msg);
			}
			throw new Exception($msg);			
		}	
		return $ret;		
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
	public function delete($ignoreAcl=false){
		
		//GO::debug("Delete ".$this->className()." pk: ".$this->pk);
		
		if($this->isNew)
			return true;
		
		if(!$ignoreAcl && !$this->checkPermissionLevel(GO_Base_Model_Acl::DELETE_PERMISSION)){
			$msg = GO::config()->debug ? $this->className().' pk: '.var_export($this->pk, true) : '';
			throw new GO_Base_Exception_AccessDenied ($msg);
		}
		
		
		if(!$this->beforeDelete())
				return false;
		
		$r= $this->relations();
		
		foreach($r as $name => $attr){
			
			if(!empty($attr['delete']) && $attr['type']!=self::BELONGS_TO){

				$result = $this->$name;
				
				if($result instanceof GO_Base_Db_ActiveStatement){	
					//has_many relations result in a statement.
					while($child = $result->fetch()){				
						$child->delete();
					}
				}elseif($result)
				{
					//single relations return a model.
					$result->delete();
				}
			}
			
			//clean up link models for many_many relations
			if($attr['type']==self::MANY_MANY){
				$stmt = GO::getModel($attr['linkModel'])->find(
				 GO_Base_Db_FindParams::newInstance()							
								->criteria(GO_Base_Db_FindCriteria::newInstance()
												->addModel(GO::getModel($attr['linkModel']))
												->addCondition($attr['field'], $this->pk)
												)											
								);
				$stmt->callOnEach('delete');
			}
		}
		
		//Set the foreign fields of the deleted relations to 0 because the relation doesn't exist anymore.
		//We do this in a separate loop because relations that should be deleted should be processed first.
		//Consider these relation definitions:
		//
		// 'messagesCustomer' => array('type'=>self::HAS_MANY, 'model'=>'GO_Tickets_Model_Message', 'field'=>'ticket_id', 'findParams'=>GO_Base_Db_FindParams::newInstance()->order('id','DESC')->select('t.*')->criteria(GO_Base_Db_FindCriteria::newInstance()->addCondition('is_note', 0))),
		// 'messagesNotes' => array('type'=>self::HAS_MANY, 'model'=>'GO_Tickets_Model_Message', 'field'=>'ticket_id', 'findParams'=>GO_Base_Db_FindParams::newInstance()->order('id','DESC')->select('t.*')->criteria(GO_Base_Db_FindCriteria::newInstance()->addCondition('is_note', 0))),
		// 'messages' => array('type'=>self::HAS_MANY, 'model'=>'GO_Tickets_Model_Message', 'field'=>'ticket_id','delete'=>true, 'findParams'=>GO_Base_Db_FindParams::newInstance()->order('id','DESC')->select('t.*')),
		//
		// messagesCustomer and messagesNotes are just subsets of the messages 
		// relation that must all be deleted anyway. We don't want to clear foreign keys first and then fail to delete them.
	
		foreach($r as $name => $attr){
			if(empty($attr['delete'])){
				if($attr['type']==self::HAS_ONE){
					//set the foreign field to 0. Because it doesn't exist anymore.
					$model = $this->$name;
					if($model){
						$model->{$attr['field']}=0;
						$model->save();
					}
				}elseif($attr['type']==self::HAS_MANY){
					//set the foreign field to 0 because it doesn't exist anymore.
					$stmt = $this->$name;
					while($model = $stmt->fetch()){
						$model->{$attr['field']}=0;
						$model->save();
					}
				}
			}
		}
		
		$sql = "DELETE FROM `".$this->tableName()."` WHERE ";
		$sql = $this->_appendPkSQL($sql);
		
		//remove cached models
		GO::modelCache()->remove($this->className());
		
		
		if($this->_debugSql)
			GO::debug($sql);

		$success = $this->getDbConnection()->query($sql);		
		if(!$success)
			throw new Exception("Could not delete from database");
		
		$this->_log(GO_Log_Model_Log::ACTION_DELETE);
		
		$attr = $this->getCacheAttributes();
		
		if($attr){
			$model = GO_Base_Model_SearchCacheRecord::model()->findByPk(array('model_id'=>$this->pk, 'model_type_id'=>$this->modelTypeId()),false,true);
			if($model)
				$model->delete(true);
		}
		
		if($this->hasFiles() && $this->files_folder_id > 0 && GO::modules()->isInstalled('files')){
			$folder = GO_Files_Model_Folder::model()->findByPk($this->files_folder_id,false,true);
			if($folder)
				$folder->delete(true);
		}		
		
		if($this->aclField() && !$this->joinAclField){			
			//echo 'Deleting acl '.$this->{$this->aclField()}.' '.$this->aclField().'<br />';
			
			$acl = GO_Base_Model_Acl::model()->findByPk($this->{$this->aclField()});			
			$acl->delete();
		}	
		
		$this->_deleteLinks();

		if(!$this->afterDelete())
			return false;
		
		$this->fireEvent('delete', array(&$this));
		
		return true;
	}
	
	
	private function _deleteLinks(){
		//cleanup links
		if($this->hasLinks()){
			$stmt = GO_Base_Model_ModelType::model()->find();
			while($modelType = $stmt->fetch()){
				
				$model = GO::getModel($modelType->model_name);
				if($model->hasLinks()){

					$linksTable = "go_links_".$model->tableName();

					$sql = "DELETE FROM $linksTable WHERE model_type_id=".intval($this->modelTypeId()).' AND model_id='.intval($this->pk);
					$this->getDbConnection()->query($sql);

					$linksTable = "go_links_".$this->tableName();

					$sql = "DELETE FROM $linksTable WHERE id=".intval($this->pk);
					$this->getDbConnection()->query($sql);			
				}
			}
		}
	}
	
//	/**
//	 * Set the output mode for this model. The default value can be set globally 
//	 * too with GO_Base_Db_ActiveRecord::$attributeOutputMode.
//	 * It can be 'raw', 'formatted' or 'html'.
//	 * 
//	 * @param type $mode 
//	 */
//	public function setAttributeOutputMode($mode){
//		if($mode!='raw' && $mode!='formatted' && $mode!='html')
//			throw new Exception("Invalid mode ".$mode." supplied to setAttributeOutputMode in ".$this->className());
//
//		$this->_attributeOutputMode=$mode;
//	}
	
//	/**
//	 *Get the current attributeOutputmode
//	 * 
//	 * @return string 
//	 */
//	public function getAttributeOutputMode(){
//		
//		return $this->_attributeOutputMode;
//	}
	/**
	 * PHP getter magic method.
	 * This method is overridden so that AR attributes can be accessed like properties.
	 * @param string $name property name
	 * @return mixed property value
	 * @see getAttribute
	 */
	public function __get($name)
	{
		return $this->_getMagicAttribute($name);
	}
	
	private function _getMagicAttribute($name, $triggerError=true){
		if(isset($this->_attributes[$name])){
			return $this->getAttribute($name, self::$attributeOutputMode);
		}else{
			
			$getter = 'get'.ucfirst($name);
			
			if(method_exists($this,$getter)){
				return $this->$getter();
			}else
			{
				$r = $this->relations();
				if(isset($r[$name]))	
					return $this->_getRelated($name);
				elseif($triggerError){
					if(!isset($this->columns[$name]))
						trigger_error ("Access to undefined property $name in ".$this->className());
					else 
						GO::debug("Column $name is NULL in ".$this->className());				
				}
			}
		}		
	}
	/**
	 * Get a single attibute raw like in the database or formatted using the \
	 * Group-Office user preferences.
	 * 
	 * @param String $attributeName
	 * @param String $outputType raw, formatted or html
	 * @return mixed 
	 */
	public function getAttribute($attributeName, $outputType='raw'){
		if(!isset($this->_attributes[$attributeName]))						
			return false;
		
		return $outputType=='raw' ?  $this->_attributes[$attributeName] : $this->formatAttribute($attributeName, $this->_attributes[$attributeName],$outputType=='html');
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

    $extraFindParams=isset($parameters[0]) ?$parameters[0] : array();
		if($this->_relationExists($name))
			return $this->_getRelated($name,$extraFindParams);
		else
			throw new Exception("function {$this->className()}:$name does not exist");
		//return parent::__call($name,$parameters);
	}

	/**
	 * PHP setter magic method.
	 * This method is overridden so that AR attributes can be accessed like properties.
	 * 
	 * @param string $name property name
	 * @param mixed $value property value
	 */
	public function __set($name,$value)
	{
		$this->setAttribute($name,$value);
	}
	
	public function __isset($name){
		$var = $this->_getMagicAttribute($name, false);
		return isset($var);
	}
	
	/**
	 * Sets a component property to be null.
	 * This method overrides the parent implementation by clearing
	 * the specified attribute value.
	 * 
	 * @param string $name the property name
	 */
	public function __unset($name)
	{		
		unset($this->_attributes[$name]);		
	}
	
	
	/**
	 * Sets the named attribute value.
	 * You may also use $this->AttributeName to set the attribute value.
	 * @param string $name the attribute name
	 * @param mixed $value the attribute value.
	 * @return boolean whether the attribute exists and the assignment is conducted successfully
	 * @see hasAttribute
	 */
	public function setAttribute($name,$value, $format=false)
	{	
		if($format)
			$value = $this->formatInput($name, $value);
		
		if(isset($this->columns[$name])){
			
			if(GO::config()->debug){
				if(is_object($value) || is_array($value))
					throw new Exception($this->className()."::setAttribute : Invalid attribute value for ".$name.". Type was: ".gettype($value));
			}
			if((!isset($this->_attributes[$name]) || $this->_attributes[$name]!=$value) && !$this->isModified($name))
				$this->_modifiedAttributes[$name]=isset($this->_attributes[$name]) ? $this->_attributes[$name] : false;
			
			$this->_attributes[$name]=$value;

		}else{			
			$setter = 'set'.$name;
			
			if(method_exists($this,$setter)){
				return $this->$setter($value);
			}else
			{				
				$this->_attributes[$name]=$value;				
			}
		}

		return true;
	}
	
	
	/**
	 * Pass another model to this function and they will be linked with the
	 * Group-Office link system.
	 * 
	 * @param mixed $model 
	 */
	
	public function link($model, $description='', $this_folder_id=0, $model_folder_id=0, $linkBack=true){
		
		$isSearchCacheModel = ($this instanceof GO_Base_Model_SearchCacheRecord);
		
		if(!$this->hasLinks() && !$isSearchCacheModel)
			throw new Exception("Links not supported by ".$this->className ());
		
		if($this->_linkExists($model))
			return true;
		
		if($model instanceof GO_Base_Model_SearchCacheRecord){
			$model_id = $model->model_id;
			$model_type_id = $model->model_type_id;			
		}else
		{
			$model_id = $model->id;
			$model_type_id = $model->modelTypeId();			
		}
		
		$table = $isSearchCacheModel ? GO::getModel($this->model_name)->model()->tableName() : $this->tableName();
		
		$id = $isSearchCacheModel ? $this->model_id : $this->id;
		
		$fieldNames = array(
				'id',
				'folder_id',
				'model_type_id',
				'model_id', 
				'description',
				'ctime');
		
		$sql = "INSERT INTO `go_links_$table` ".
					"(`".implode('`,`', $fieldNames)."`) VALUES ".
					"(:".implode(',:', $fieldNames).")";
		
		$values = array(
				':id'=>$id,
				':folder_id'=>$this_folder_id,
				':model_type_id'=>$model_type_id,
				':model_id'=>$model_id,
				':description'=>$description,
				':ctime'=>time()
		);
		
		if($this->_debugSql){
			GO::debug($sql);
			GO::debug($values);
		}

		$result = $this->getDbConnection()->prepare($sql);
		$success = $result->execute($values);
		
		if($success)
			return !$linkBack || $model->link($this, $description, $model_folder_id, $this_folder_id, false);
	}
	
	private function _linkExists($model){		
		if($model->className()=="GO_Base_Model_SearchCacheRecord"){
			$model_id = $model->model_id;
			$model_type_id = $model->model_type_id;
		}else
		{
			$model_id = $model->id;
			$model_type_id = $model->modelTypeId();
		}
		
		$table = $this->className()=="GO_Base_Model_SearchCacheRecord" ? GO::getModel($this->model_name)->model()->tableName() : $this->tableName();		
		$this_id = $this->className()=="GO_Base_Model_SearchCacheRecord" ? $this->model_id : $this->id;
		
		$sql = "SELECT count(*) FROM `go_links_$table` WHERE ".
			"`id`=".intval($this_id)." AND model_type_id=".$model_type_id." AND `model_id`=".$model_id;
		$stmt = $this->getDbConnection()->query($sql);
		return $stmt->fetchColumn(0) > 0;		
	}
	
	/**
	 * Unlink a model from this model
	 * 
	 * @param GO_Base_Db_ActiveRecord $model
	 * @param boolean $unlinkBack For private use only
	 * @return boolean 
	 */
	public function unlink($model, $unlinkBack=true){
		$sql = "DELETE FROM `go_links_{$this->tableName()}` WHERE id=:id AND model_type_id=:model_type_id AND model_id=:model_id";
		
		$values=array(
				':id'=>$this->id,
				':model_type_id'=>$model->modelTypeId(),
				':model_id'=>$model->id
		);
		
		$result = $this->getDbConnection()->prepare($sql);
		$success = $result->execute($values);
		
		if($success)
			return !$unlinkBack || $model->unlink($this, false);
		
	}
	
	/**
	 * Get the number of links this model has to other models.
	 * 
	 * @param int $model_id
	 * @return int
	 */
	public function countLinks($model_id=0){
		if($model_id==0)
			$model_id=$this->id;
		$sql = "SELECT count(*) FROM `go_links_{$this->tableName()}` WHERE id=".intval($model_id);
		$stmt = $this->getDbConnection()->query($sql);
		return intval($stmt->fetchColumn(0));	
	}
	
	/**
	 * Find links of this model type to a given model. 
	 * 
	 * eg.:
	 * 
	 * GO_Addressbook_Model_Contact::model()->findLinks($noteModel);
	 * 
	 * selects all contacts linked to the $noteModel
	 * 
	 * @param GO_Base_Db_ActiveRecord $model
	 * @param GO_Base_Db_FindParams $findParams
	 * @return GO_Base_Db_ActiveStatement 
	 */
	public function findLinks($model, $findParams=false){
		
		if(!$findParams)
			$findParams = GO_Base_Db_FindParams::newInstance ();
		
		$findParams->select('t.*,l.description AS link_description');
		
		$joinCriteria = GO_Base_Db_FindCriteria::newInstance()
						->addCondition('id', $model->id,'=','l')
						->addRawCondition("t.id", "l.model_id")
						->addCondition('model_type_id', $this->modelTypeId(),'=','l');
		
		$findParams->join("go_links_{$model->tableName()}", $joinCriteria, 'l');
		
		return $this->find($findParams);
	}
	
	
	/**
	 * Copy links from this model to the target model.
	 * 
	 * @param GO_Base_Db_ActiveRecord $targetModel 
	 */
	public function copyLinks(GO_Base_Db_ActiveRecord $targetModel){
		if(!$this->hasLinks() || !$targetModel->hasLinks())
			return false;
			
		$stmt = GO_Base_Model_SearchCacheRecord::model()->findLinks($this);
		while($searchCacheModel = $stmt->fetch()){
			$targetModel->link($searchCacheModel, $searchCacheModel->link_description);
		}
		return true;
	}	
	
	private $_customfieldsRecord;
	
	/**
	 *
	 * @return GO_Customfields_Model_AbstractCustomFieldsRecord 
	 */
	private function _createCustomFieldsRecordFromAttributes(){
		$model = $this->customfieldsModel();
		
		if(!isset($this->_customfieldsRecord)){
			
			$customattr = $this->attributes;
			$customattr['model_id']=$this->id;

			$this->_customfieldsRecord = new $model;
			$this->_customfieldsRecord->setAttributes($customattr,false);
		}
		
		
		return $this->_customfieldsRecord;		
	}
	
	/**
	 * Returns the customfields record if module is installed and this model
	 * supports it (See GO_Base_Db_ActiveRecord::customFieldsModel())
	 * 
	 * @return GO_Customfields_Model_AbstractCustomFieldsRecord 
	 */
	public function getCustomfieldsRecord(){
		
		if($this->customfieldsModel() && GO::modules()->customfields){			
			if(!isset($this->_customfieldsRecord)){// && !empty($this->pk)){
				$customFieldModelName=$this->customfieldsModel();
				$this->_customfieldsRecord = GO::getModel($customFieldModelName)->findByPk($this->pk);
				if(!$this->_customfieldsRecord){
					//doesn't exist yet. Return a new one
					$this->_customfieldsRecord = new $customFieldModelName;
					$this->_customfieldsRecord->model_id=$this->pk;
				}
			}
			return $this->_customfieldsRecord;
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
	public function getUser(){
		
		if(!empty($this->user_id)){
			return GO_Base_Model_User::model()->findByPk($this->user_id, array(), true);
		}else
		{
			return false;
		}
	}
	
	/**
	 * Get's the Acces Control List for this model if it has one.
	 * 
	 * @return GO_Base_Model_Acl 
	 */
	public function getAcl(){
		$aclId = $this->findAclId();
		if($aclId)
			return GO_Base_Model_Acl::model()->findByPk($aclId);
		else
			return false;
	}
	
	/**
	 * A function that checks the consistency with the database.
	 * Generally this is called by r=maintenance/checkDabase
	 */
	public function checkDatabase(){		
		//$this->save();		
		
		echo "Checking ".(is_array($this->pk)?implode(',',$this->pk):$this->pk)." ".$this->className()."\n";
		flush();

		if($this->aclField() && !$this->joinAclField){

			$acl = $this->acl;
			if(!$acl)
				$this->setNewAcl();
			else
			{
				$user_id = empty($this->user_id) ? 1 : $this->user_id;				
				$acl->user_id=$user_id;
				$acl->description=$this->tableName().'.'.$this->aclField();
				$acl->save();
			}
		}
		
		if ($this->hasFiles() && GO::modules()->isInstalled('files')) {
			//ACL must be generated here.
			$fc = new GO_Files_Controller_Folder();
			$this->files_folder_id = $fc->checkModelFolder($this);
		}
		
		$this->save();		
	}
	
	
	public function rebuildSearchCache(){
		$attr = $this->getCacheAttributes();
		
		if($attr){			
			$stmt = $this->find(GO_Base_Db_FindParams::newInstance()->ignoreAcl()->select('t.*'));			
			$stmt->callOnEach('cacheSearchRecord', true);			
		}
	}

	
	/**
	 * Duplicates the current activerecord to a new one.
	 * 
	 * @param array $params Array of parameters that need to be set in the newly created activerecord as KEY => VALUE. Like: $params = array('attribute1'=>1,'attribute2'=>'Hello');
	 *
	 * @return Object The newly created object
	 * 
	 * @todo Copy the linked items too.  Use __clone() ??
	 * 
	 */
	public function duplicate($params = array(), $save=true) {
		
		//$copy = new GO_Base_Db_ActiveRecord(true);
		$copy = clone $this;
		
		unset($copy->ctime);
		
		//unset the files folder
		if($this->hasFiles())
			$copy->files_folder_id = 0;
		
		$pkField = $this->primaryKey();
		if(is_array($pkField)) {
			foreach($pkField as $key)
				unset($copy->$key);
		}
		else {
			unset($copy->$pkField);
		}
		
		if(!$this->beforeDuplicate($copy)){
			$copy->delete();
			return false;
		}
		

		foreach($params as $key=>$value) {
			$copy->$key = $value;
		}
		
		//Generate new acl for this model
		if($this->aclField() && !$this->joinAclField){
			$copy->setNewAcl($this->user_id);
		}
		
		$copy->setIsNew(true);
		
		if($this->customFieldsRecord){
			$copy->customFieldsRecord->setAttributes($this->customFieldsRecord->getAttributes('raw'), false);
		}
		
		
		
		if($save)
			$copy->save();
		
		if(!$this->afterDuplicate($copy)){
			$copy->delete();
			return false;
		}

		return $copy;
	}
	
	protected function beforeDuplicate(&$duplicate){
		return true;	
	}
	protected function afterDuplicate(&$duplicate){
		return true;	
	}
	
	/**
	 * Duplicate related items to another model.
	 * 
	 * @param string $relationName
	 * @param GO_Base_Db_ActiveRecord $duplicate
	 * @return boolean
	 * @throws Exception 
	 */
	public function duplicateRelation($relationName, $duplicate, array $attributes=array(), $findParams=false){
		
		$r= $this->relations();
		
		if(!isset($r[$relationName]))
			throw new Exception("Relation $relationName not found");
		
		if($r[$relationName]['type']!=self::HAS_MANY){
			throw new Exception("Only HAS_MANY relations are supported in duplicateRelation");
		}
		
		$field = $r[$relationName]['field'];
		
		if(!$findParams)
			$findParams=  GO_Base_Db_FindParams::newInstance ();
		
		$findParams->select('t.*');		
		
		$stmt = $this->_getRelated($relationName, $findParams);
		while($model = $stmt->fetch()){
			
			//set new foreign key
			$attributes[$field]=$duplicate->pk;
			
			$duplicateRelatedModel = $model->duplicate($attributes);
			$this->afterDuplicateRelation($relationName, $model, $duplicateRelatedModel);
		}
		
		return true;
	}
	
	protected function afterDuplicateRelation($relationName, GO_Base_Db_ActiveRecord $relatedModel, GO_Base_Db_ActiveRecord $duplicatedRelatedModel){
		return true;
	}
	
	/**
	 * Lock the database table
	 *
	 * @param string $mode Modes are: "read", "read local", "write", "low priority write"
	 * @return boolean
	 */
	public function lockTable($mode="WRITE"){
		$sql = "LOCK TABLES `".$this->tableName()."` AS t $mode";
		$this->getDbConnection()->query($sql);
		
		if($this->hasFiles() && GO::modules()->isInstalled('files')){
			$sql = "LOCK TABLES `fs_folders` AS t $mode";
			$this->getDbConnection()->query($sql);
		}
		
		return true;
	}
	/**
	 * Unlock tables
	 *
	 * @return bool True on success
	 */

	public function unlockTable(){
		$sql = "UNLOCK TABLES;";
		return $this->getDbConnection()->query($sql);
	}
	
	
	private function _getDefaultAttributes(){
		$attr=array();
		foreach($this->getColumns() as $field => $colAttr)
			$attr[$field]=$colAttr['default'];
		
		if(isset($this->columns['user_id']))
			$attr['user_id']=GO::user() ? GO::user()->id : 1;
		
		return array_merge($attr, $this->defaultAttributes());
	}
	
	/**
	 * Array of default attributes to set
	 * 
	 * This function can be overridden in the model.
	 * 
	 * @return Array An empty array.
	 */
	public function defaultAttributes() {
		return array();
	}
	
	
	
	/**
	 * Delete all reminders linked to this midel.
	 */
	public function deleteReminders(){
		
		$stmt = GO_Base_Model_Reminder::model()->findByModel($this->className(), $this->pk);
		$stmt->callOnEach("delete");
	}
	
	/**
	 * Add a reminder linked to this model
	 * 
	 * @param string $name The name of the reminder
	 * @param int $time This needs to be an unixtimestamp
	 * @param int $user_id The user where this reminder belongs to.
	 * @return GO_Base_Model_Reminder 
	 */
	public function addReminder($name, $time, $user_id){	
	
		$reminder = GO_Base_Model_Reminder::newInstance($name, $time, $this->className(), $this->pk);
		$reminder->setForUser($user_id);
		
		return $reminder;
					
	}
		
	/**
	 * Add a record to the given MANY_MANY relation
	 * 
	 * @param String $relationName
	 * @param int $foreignPk
	 * @param array $extraAttributes
	 * @return boolean Saved
	 */
	public function addManyMany($relationName, $foreignPk, $extraAttributes=array()){
		
		if(empty($foreignPk))
			return false;
		
		if(!$this->hasManyMany($relationName, $foreignPk)){
			
			$r = $this->getRelation($relationName);
			
			if($this->isNew)
				throw new Exception("Can't add manymany relation to a new model. Call save() first.");
			
			if(!$r)
				throw new Exception("Relation '$relationName' not found in GO_Base_Db_ActiveRecord::addManyMany()");
			
			$linkModel = new $r['linkModel'];
			$linkModel->{$r['field']} = $this->pk;
			
			$keys = $linkModel->primaryKey();
			
			$foreignField = $keys[0]==$r['field'] ? $keys[1] : $keys[0];
			
			$linkModel->$foreignField = $foreignPk;
			
			$linkModel->setAttributes($extraAttributes);
			
			return $linkModel->save();
		}else
		{
			return true;
		}
  }
	
	/**
	 * Remove a record from the given MANY_MANY relation
	 * 
	 * @param String $relationName
	 * @param int $foreignPk
	 * 
	 * @return GO_Base_Db_ActiveRecord or false 
	 */
	public function removeManyMany($relationName, $foreignPk){		
		$linkModel = $this->hasManyMany($relationName, $foreignPk);
		
		if($linkModel)
			return $linkModel->delete();
		else
			return true;
	}

	public function removeAllManyMany($relationName){
		$r = $this->getRelation($relationName);
		if(!$r)
			throw new Exception("Relation '$relationName' not found in GO_Base_Db_ActiveRecord::hasManyMany()");
		$linkModel = GO::getModel($r['linkModel']);
		
		$linkModel->deleteByAttribute($r['field'],$this->pk);
	}
  
  /**
   * Check for records in the given MANY_MANY relation
   * 
   * @param String $relationName
	 * @param int $foreignPk
	 * 
   * @return GO_Base_Db_ActiveRecord or false 
   */
  public function hasManyMany($relationName, $foreignPk){
		$r = $this->getRelation($relationName);
		if(!$r)
			throw new Exception("Relation '$relationName' not found in GO_Base_Db_ActiveRecord::hasManyMany()");
		
		if($this->isNew)
			throw new Exception("You can't call hasManyMany on a new model. Call save() first.");
		
		$linkModel = GO::getModel($r['linkModel']);
		$keys = $linkModel->primaryKey();	
		if(count($keys)!=2){
			throw new Exception("Primary key of many many linkModel ".$r['linkModel']." must be an array of two fields");
		}
		$foreignField = $keys[0]==$r['field'] ? $keys[1] : $keys[0];
		
		$primaryKey = array($r['field']=>$this->pk, $foreignField=>$foreignPk);
		
    return $linkModel->findByPk($primaryKey);
  }
	
	/**
	 * Quickly delete all records by attribute. This function does NOT check the ACL.
	 * 
	 * @param string $name
	 * @param mixed $value 
	 */
	public function deleteByAttribute($name, $value){
		$stmt = $this->find(GO_Base_Db_FindParams::newInstance()->ignoreAcl()->criteria(GO_Base_Db_FindCriteria::newInstance()->addCondition($name, $value)));		
		$stmt->callOnEach('delete');	
	}
	
	/**
	 * Add a comment to the model. If the comments module is not installed this
	 * function will return false.
	 * 
	 * @param string $text
	 * @return boolean 
	 */
	public function addComment($text){
		if(!GO::modules()->isInstalled('comments') || !GO::modules()->isInstalled('comments') && !$this->hasLinks())
			return false;
		
		$comment = new GO_Comments_Model_Comment();
		$comment->model_id=$this->id;
		$comment->model_type_id=$this->modelTypeId();
		$comment->comments=$text;
		return $comment->save();
		
	}
	
	/**
	 * Merge this model with another one of the same type.
	 * 
	 * All attributes of the given model will be applied to this model if they are empty. Textarea's will be concatenated.
	 * All links will be moved to this model.
	 * Finally the given model will be deleted.
	 * 
	 * @param GO_Base_Db_ActiveRecord $model 
	 */
	public function mergeWith(GO_Base_Db_ActiveRecord $model, $mergeAttributes=true, $deleteModel=true){
		
		if($model->id==$this->id && $this->className()==$model->className())
			return false;
				
		//copy attributes if models are of the same type.
		if($mergeAttributes){
			$attributes = $model->getAttributes('raw');

			//don't copy primary key
			if(is_array($this->primaryKey())){
				foreach($this->primaryKey() as $field)
					unset($attributes[$field]);			
			}else			
				unset($attributes[$this->primaryKey()]);

			unset($attributes['files_folder_id']);

			foreach($attributes as $name=>$value){
				$isset = isset($this->columns[$name]);

				if($isset && !empty($value)){
					if($this->columns[$name]['gotype']=='textarea'){
						$this->$name .= "\n\n-- merge --\n\n".$value;
					}elseif(empty($this->$name))
						$this->$name=$value;
				}
			}		
			$this->save();				
		
			//copy custom fields
			if($model->customfieldsRecord)
				$this->customfieldsRecord->mergeWith($model->customfieldsRecord);
		}
		
		$model->copyLinks($this);
		
		//move files.
		$this->_moveFiles($model);
		
		$this->_moveComments($model);
		
		$this->afterMergeWith($model);
		
		if($deleteModel)
			$model->delete();				
	}
	
	private function _moveComments(GO_Base_Db_ActiveRecord $sourceModel){
		if(GO::modules()->isInstalled('comments') && $this->hasLinks()){
			$findParams = GO_Base_Db_FindParams::newInstance()
						->ignoreAcl()	
						->order('id','DESC')
						->criteria(
										GO_Base_Db_FindCriteria::newInstance()
											->addCondition('model_id', $sourceModel->id)
											->addCondition('model_type_id', $sourceModel->modelTypeId())										
										);
			
			$stmt = GO_Comments_Model_Comment::model()->find($findParams);
			while($comment = $stmt->fetch()){
				$comment->model_type_id=$this->modelTypeId();
				$comment->model_id=$this->id;
				$comment->save();
			}
		}
	}
	
	private function _moveFiles(GO_Base_Db_ActiveRecord $sourceModel){
		if(!$this->hasFiles())
			return false;
		
		$sourceFolder = GO_Files_Model_Folder::model()->findByPk($sourceModel->files_folder_id);
		if(!$sourceFolder)
			return false;
		
		$this->filesFolder->moveContentsFrom($sourceFolder);		
	}
	
	/**
	 * This function forces this activeRecord to save itself.
	 */
	public function forceSave(){
		
		$this->_forceSave=true;
	}	
	
	/**
	 * Override this if you need to do extra stuff after merging.
	 * Move relations for example.
	 */
	protected function afterMergeWith(GO_Base_Db_ActiveRecord $model){}

}
