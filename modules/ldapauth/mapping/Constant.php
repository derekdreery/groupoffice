<?php
class GO_Ldapauth_Mapping_Constant {

	private $_value;

	/**
	 * LDAP Mapping object for functions or constants
	 * 
	 * @param mixed $function Name of function or array('className','function') or contstant value.
	 */
	function __construct($value) {
		$this->_value = $value;
	}

	function getValue(GO_Base_Ldap_Record $record) {
		return $this->_value;
	}

}