<?php
class GO_ActiveRecord {
	
	public static $db;
	
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
		return array(
				'pages' => array(self::BELONGS_TO, 'Course', 'course_id')
		);
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
	public $aclId;
	
	/**
	 * 
	 * 
	 * @var mixed Primary key of database table. Can be a field name string or an array of fieldnames
	 */
	protected $primaryKey='id'; //TODO can also be array('user_id','group_id') for example.
	
	/**
	 * Constructor for the model
	 * 
	 * @param int $primaryKey integer The primary key of the database table
	 */
	public function __construct($primaryKey=0){
		
		if($primaryKey!=0)
			$this->load($primaryKey);
	}
	
	/**
	 * Loads the model attributes from the database
	 * 
	 * @param int $primaryKey 
	 */
	
	protected function load($primaryKey){
		$sql = "SELECT * FROM `".$this->tableName."` WHERE `".$this->primaryKey.'`='.intval($primaryKey);
		$this->db->query($sql);
		$atrributes = $this->db->next_record();
		
		$this->setAttributes($attributes);
		
		$this->isNew=false;
		
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('country, name', 'required'),
			array('country', 'length', 'max'=>2),
			array('name', 'length', 'max'=>50),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, country, name', 'safe', 'on'=>'search'),
		);
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
	public function getAttributes($names=true)
	{
		$attributes=$this->_attributes;
		foreach($this->getMetaData()->columns as $name=>$column)
		{
			if(property_exists($this,$name))
				$attributes[$name]=$this->$name;
			else if($names===true && !isset($attributes[$name]))
				$attributes[$name]=null;
		}
		if(is_array($names))
		{
			$attrs=array();
			foreach($names as $name)
			{
				if(property_exists($this,$name))
					$attrs[$name]=$this->$name;
				else
					$attrs[$name]=isset($attributes[$name])?$attributes[$name]:null;
			}
			return $attrs;
		}
		else
			return $attributes;
	}
	
	private function _checkPermissions(){
		
	}
	
	public function save(){
		
		if($this->checkPermissions()){		
			if($this->isNew){

				return $this->db->insert_row($this->tableName, $attributes);
			}else
			{
				return $this->db->update_row($this->tableName, $this->primaryKey, $attributes);
			}
		}else
		{
			return false;
		}
	}
	
	public function delete(){
		
	}
	
	public function __get($name){
		
	}

}