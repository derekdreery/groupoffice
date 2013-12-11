<?php
class GO_Customfields_Customfieldtype_EncryptedText extends \GO_Customfields_Customfieldtype_Abstract{
	
	public function name(){
		return 'Encrypted text';
	}
	
	public function includeInSearches() {
		return false;
	}
	
	public function formatFormInput($key, &$attributes, GO_Customfields_Model_AbstractCustomFieldsRecord $model) {
		return \GO\Base\Util\Crypt::encrypt($attributes[$key]);
	}
	
	public function formatFormOutput($key, &$attributes, GO_Customfields_Model_AbstractCustomFieldsRecord $model) {
		$decrypted = !empty($attributes[$key]) ? \GO\Base\Util\Crypt::decrypt($attributes[$key]) : '';
		return $decrypted;
	}
	
	public function formatDisplay($key, &$attributes, GO_Customfields_Model_AbstractCustomFieldsRecord $model) {
		if(\GO_Customfields_Model_AbstractCustomFieldsRecord::$formatForExport){
			return \GO\Base\Util\Crypt::decrypt($attributes[$key]);
		}
		$decrypted = !empty($attributes[$key]) ? '<div ext:qtip="'.htmlspecialchars(\GO\Base\Util\Crypt::decrypt($attributes[$key]),ENT_COMPAT, 'utf-8').'">'.\GO::t('pointForText').'</div>' : '';
		return $decrypted;
	}
}