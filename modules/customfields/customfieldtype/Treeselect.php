<?php
class GO_Customfields_Customfieldtype_Treeselect extends GO_Customfields_Customfieldtype_Select{
	
	public function name(){
		return 'Treeselect';
	}
	
	public function fieldSql(){
		//needs to be text for multiselect field
		if($this->field->multiselect)
			return "TEXT NULL";		
		else
			return parent::fieldSql ();
	}
	
	public function includeInSearches() {
		return true;
	}
	

	
	public function formatDisplay($key, &$attributes, GO_Customfields_Model_AbstractCustomFieldsRecord $model) {
		
		if(!empty($attributes[$key])) {

			//multiselect is only valid for the last treeselect_slave
			if(!empty($this->field->multiselect)){

				$value_arr=array();
				$id_value_arr = explode('|', $attributes[$this->field->dataname]);
				foreach($id_value_arr as $value){
					$id_value = explode(':', $value);
					if(isset($id_value[1])){
						array_shift($id_value);
						$value_arr[]=implode(':', $id_value);
					}
				}

				$attributes[$key] = implode(', ', $value_arr);
			}else {
				$value = explode(':', $attributes[$key]);			
				//var_dump(GO_Customfields_Model_AbstractCustomFieldsRecord::$formatForExport);
				if(isset($value[1])){
					$attributes[$key] = $value[1];
				}
			}
		}
		
		return $attributes[$key];
	}
    
    public function formatFormOutput($key, &$attributes, GO_Customfields_Model_AbstractCustomFieldsRecord $model) {
	
      if(!GO_Customfields_Model_AbstractCustomFieldsRecord::$formatForExport){
          return parent::formatFormOutput($key, $attributes, $model);
      }else
      {
          return $this->getName($attributes[$key]);
      }		
	}	
	
	

}