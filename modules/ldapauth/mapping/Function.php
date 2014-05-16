<?php
class GO_Ldapauth_Mapping_Function {

	private $_function;

	/**
	 * LDAP Mapping object for functions.
	 * 
	 * @param mixed $function Name of function or array('className','function'). It will be called with the GO_Base_Ldap_Record $record parameter.
	 */
	function __construct($function) {
		$this->_function = $function;
	}

	function getValue(GO_Base_Ldap_Record $record) {
		return call_user_func($this->_function, $record);		
	}

}
