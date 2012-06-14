<?php
class GO_Ldapauth_Mapping_Function {

	private $_function;

	/**
	 * LDAP Mapping object for functions or constants
	 * 
	 * @param mixed $function Name of function or array('className','function') or contstant value.
	 */
	function __construct($function) {
		$this->_function = $function;
	}

	function getValue(GO_Base_Ldap_Record $record) {
		return call_user_func($this->_function, $record);		
	}

}
