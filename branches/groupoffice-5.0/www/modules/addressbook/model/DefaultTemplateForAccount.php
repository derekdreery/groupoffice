<?php
class GO_Addressbook_Model_DefaultTemplateForAccount extends GO_Base_Db_ActiveRecord {
	
	public static function model($className=__CLASS__)
	{	
		return parent::model($className);
	}
	
	public function tableName() {
		return 'ab_default_email_account_templates';
	}
	
	public function primaryKey() {
		return 'account_id';
	}
	
	public function relations(){
		return array(
			'emailTemplate' => array('type'=>self::BELONGS_TO, 'model'=>'GO_Addressbook_Model_Template', 'field'=>'template_id'),
			'emailAccount' => array('type'=>self::BELONGS_TO, 'model'=>'GO_Email_Model_Account', 'field'=>'account_id')
		);
	}

	
	protected function defaultAttributes() {
		$attr = parent::defaultAttributes();
		
		$findParams = GO_Base_Db_FindParams::newInstance()->limit(1);
		$stmt = GO_Addressbook_Model_Template::model()->find($findParams);
		
		if($template=$stmt->fetch())
		{
			$attr['template_id']=$template->id;
		}
		
		return $attr;
	}
}