<?php
/**
 * @property GO_Customfields_Model_Category $category 
 * @property GO_Customfields_Customfieldtype_Text $customfieldtype
 * @property int $height
 * @property boolean $exclude_from_grid
 * @property int $treemaster_field_id
 * @property int $nesting_level
 * @property int $max
 * @property boolean $multiselect
 * @property string $helptext
 * @property string $validation_regex
 * @property boolean $required
 * @property string $function
 * @property int $sort_index
 * @property string $datatype
 * @property string $name
 * @property int $category_id
 * @property int $id
 * @property boolean $unique_values
 * @property int $number_decimals
 */
class GO_Customfields_Model_Field extends GO_Base_Db_ActiveRecord{
	
	private $_datatype;
	
	/**
	 * Returns a static model of itself
	 * 
	 * @param String $className
	 * @return GO_Customfields_Model_Field 
	 */
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}
	
	public function tableName() {
		return 'cf_fields';
	}
	
	public function aclField() {
		return 'category.acl_id';
	}
	
	public function relations() {
		return array(
				'category' => array('type' => self::BELONGS_TO, 'model' => 'GO_Customfields_Model_Category', 'field' => 'category_id'),		
				'treeOptions'=>array('type' => self::HAS_MANY, 'model' => 'GO_Customfields_Model_FieldTreeSelectOption', 'field' => 'field_id','delete'=>true),
				'selectOptions'=>array('type' => self::HAS_MANY, 'model' => 'GO_Customfields_Model_FieldSelectOption', 'field' => 'field_id','delete'=>true)		
			);
	}
	
	/**
	 * Return the column name in the database of this field.
	 * @return String 
	 */
	public function columnName(){
		return 'col_'.$this->id;
	}
	
	protected function init() {
		
//		$this->columns['max']['gotype']='number';
//		$this->columns['height']['gotype']='number';
		
		$this->columns['name']['required']=true;
		
		parent::init();
	}
	
	//Mapping PHP errors to exceptions
	public function exception_error_handler($errno, $errstr, $errfile, $errline ) {
			$this->_regex_has_errors=true;
	}
	
	private $_regex_has_errors;

	
	public function validate() {		
		
		if(!empty($this->validation_regex)){
			$this->_regex_has_errors=false;
			set_error_handler(array($this,"exception_error_handler"));
			preg_match($this->validation_regex, "");
			if($this->_regex_has_errors)
				$this->setValidationError ("validation_regex", GO::t("invalidRegex","customfields"));
			
			restore_error_handler();
		}
		
		return parent::validate();
	}
	
	protected function afterSave($wasNew) {
		
		$this->alterDatabase($wasNew);
		
		return parent::afterSave($wasNew);
	}
		
	public function alterDatabase($wasNew){
			$table=$this->category->customfieldsTableName();
					
		if($wasNew){
			$sql = "ALTER TABLE `".$table."` ADD `".$this->columnName()."` ".$this->customfieldtype->fieldSql().";";
		}else
		{
			$sql = "ALTER TABLE `".$table."` CHANGE `".$this->columnName()."` `".$this->columnName()."` ".$this->customfieldtype->fieldSql();
		}		
		//don't be strict in upgrade process
		GO::getDbConnection()->query("SET sql_mode=''");
		
		if(!$this->getDbConnection()->query($sql))
			throw new Exception("Could not create custom field");
		
//		if ($this->isModified('unique_values')) {
//			
//			if (!empty($this->unique_values))
//				$sqlUnique = "ALTER TABLE `".$table."` ADD UNIQUE INDEX ".$this->columnName()."_unique(".$this->columnName().")";
//			else
//				$sqlUnique = "ALTER TABLE `".$table."` DROP INDEX ".$this->columnName()."_unique";
//			
//			if (!$this->getDbConnection()->query($sqlUnique))
//				throw new Exception("Could not change custom field uniqueness.");
//		}
		
		$this->_clearColumnCache();
	}
	
	/**
	 * GO caches the table schema for performance. We need to clear it 
	 */
	private function _clearColumnCache(){
	  //deleted cached column schema. See GO_Customfields_Model_AbstractCustomFieldsRecord			
		GO_Base_Db_Columns::clearCache(GO::getModel(GO::getModel($this->category->extends_model)->customfieldsModel()));
		GO::cache()->delete('customfields_'.$this->category->extends_model);	
	}
	
	
	protected function getCustomfieldtype(){
		
		if(!isset($this->_datatype)){
			$className = class_exists($this->datatype) ? $this->datatype : "GO_Customfields_Customfieldtype_Text";

			$this->_datatype = new $className($this);
		}
		
		return $this->_datatype;
	}
	
	protected function afterDelete() {
		
		//don't be strict in upgrade process
		GO::getDbConnection()->query("SET sql_mode=''");	
		
		$sql = "ALTER TABLE `".$this->category->customfieldsTableName()."` DROP `".$this->columnName()."`";
		if(!$this->getDbConnection()->query($sql))
			return false;
		
		$this->_clearColumnCache();
		
		return parent::afterDelete();
	}
	
	
	public function getTreeSelectNestingLevel($parentOptionId=0, $nestingLevel=0){
		$stmt= GO_Customfields_Model_FieldTreeSelectOption::model()->find(array(
			'where'=>'parent_id=:parent_id AND field_id=:field_id',
			'bindParams'=>array('parent_id'=>$parentOptionId, 'field_id'=>$this->id),
			'order'=>'sort'
		));		
		$options = $stmt->fetchAll();
		
		$startNestingLevel=$nestingLevel;
		foreach($options as $o){
			$newNestingLevel=$this->getTreeSelectNestingLevel($o->id, $startNestingLevel+1);
			if($newNestingLevel>$nestingLevel){
				$nestingLevel=$newNestingLevel;
			}
		}
		
		return $nestingLevel;
	}
	
	public function checkTreeSelectSlaves(){
		//We need to create a GO_Customfields_Customfieldtype_TreeselectSlave field for all tree levels
		$nestingLevel = $this->getTreeSelectNestingLevel();

		for($i=1;$i<$nestingLevel;$i++){
			$field =GO_Customfields_Model_Field::model()->findSingleByAttributes(array('treemaster_field_id'=>$this->id,'nesting_level'=>$i));

			if(!$field){
				$field = new GO_Customfields_Model_Field();
				$field->name=$this->name.' '.$i;
				$field->datatype='GO_Customfields_Customfieldtype_TreeselectSlave';
				$field->treemaster_field_id=$this->id;
				$field->nesting_level=$i;
				$field->category_id=$this->category_id;
				$field->save();
			}				
		}
	}
	
	protected function beforeSave() {
		if($this->isNew)
			$this->sort_index=$this->count();		
		
		return parent::beforeSave();
	}
	
	/**
	 * Get or create field if not exists
	 * 
	 * @param int $category_id
	 * @param string $fieldName
	 * @return \GO_Customfields_Model_Field 
	 */
	public function createIfNotExists($category_id, $fieldName, $createAttributes=array()){
		$field = GO_Customfields_Model_Field::model()->findSingleByAttributes(array('category_id'=>$category_id,'name'=>$fieldName));
		if(!$field){
			$field = new GO_Customfields_Model_Field();
			$field->setAttributes($createAttributes, false);
			$field->category_id=$category_id;
			$field->name=$fieldName;
			$field->save();
		}
		return $field;
	}
	
	
	public function checkDatabase() {
		
		$this->alterDatabase(false);
		
		return parent::checkDatabase();
	}
	
	
	public function toJsonArray(){
		$arr=$this->getAttributes();
		$arr['dataname']=$this->columnName();
		$arr['customfield_id']=$this->id;

		$arr['validation_modifiers']="";

		if(!empty($arr['validation_regex'])){
			$delimiter = $arr['validation_regex'][0];
			$rpos = strrpos($arr['validation_regex'], $delimiter);
			if($rpos){
				$arr['validation_modifiers']=substr($arr['validation_regex'],$rpos+1);
				$arr['validation_regex']=substr($arr['validation_regex'],1, $rpos-1);
			}else
			{
				$arr['validation_regex']="";
			}
		}
		
		return $arr;
	}
	
	/**
	 * Get all customfield models that are attached to the given model.
	 * 
	 * @param string $modelName
	 * @param int $permissionLevel Set to false to ignore permissions
	 * @return GO_Customfields_Model_Field
	 */
	public function findByModel($modelName, $permissionLevel=  GO_Base_Model_Acl::READ_PERMISSION){
		$findParams = GO_Base_Db_FindParams::newInstance()->joinRelation('category')->order('sort_index');
		
		if($permissionLevel){
			$findParams->permissionLevel($permissionLevel);
		}else
		{
			$findParams->ignoreAcl();
		}
		
		$findParams->getCriteria()->addCondition('extends_model', $modelName,'=','category');
		return $this->find($findParams);
	}
}