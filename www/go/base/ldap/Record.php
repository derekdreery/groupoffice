<?php
class GO_Base_Ldap_Record{
	/**
	 * The LDAP connection
	 * 
	 * @var GO_Base_Ldap_Connection 
	 */
	private $_ldapConn;
	
	private $_entryId;
	
	private $_attributes;
	
	public function __construct(GO_Base_Ldap_Connection $ldapConn, $entryId) {
		$this->_entryId=$entryId;
		$this->_ldapConn=$ldapConn;
	}
	
	/**
	 * Get all attributes with values in a key value array
	 * 
	 * @return array 
	 */
	public function getAttributes(){
		if(!isset($_attributes)){
			$attributes = ldap_get_attributes($this->_ldapConn->getLink(), $this->_entryId);
			//var_dump($attributes);
			for($i=0;$i<$attributes['count'];$i++){
				//echo $attributes[$i]." : ".$attributes[$attributes[$i]][0]."\n";
				$this->_attributes[$attributes[$i]]=$attributes[$attributes[$i]];
			}
		}
		
		return $attributes;
	}
	
	/**
	 * Get the DN of this record.
	 * 
	 * @return string 
	 */
	public function getDn(){
		return ldap_get_dn($this->_ldapConn->getLink(),$this->_entryId);
	}
	
	public function __get($name){
		$this->getAttributes();
		return $this->_attributes[$name][0];
	}
}