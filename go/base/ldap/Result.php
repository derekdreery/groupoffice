<?php
class GO_Base_Ldap_Result{
	
	/**
	 * The LDAP connection
	 * 
	 * @var GO_Base_Ldap_Connection 
	 */
	private $_ldapConn;
	private $_searchId;
	
	private $_numEntry;
	
	private $_entryId;
	
	public $fetchClass = 'GO_Base_Ldap_Record';
	
	public function __construct(GO_Base_Ldap_Connection $ldapConn, $searchId) {
		$this->_searchId=$searchId;
		$this->_ldapConn=$ldapConn;
	}
	
	/**
	 * Fetch the next record or return false if there's none.
	 * 
	 * @return GO_Base_Ldap_Record 
	 */
	public function fetch(){
		if(!isset($this->_numEntry)){
			$this->_numEntry=0;
			$this->_entryId = ldap_first_entry( $this->_ldapConn->getLink(), $this->_searchId);
		}else
		{
			$this->_numEntry++;
			$this->_entryId = ldap_next_entry( $this->_ldapConn->getLink(),$this->_entryId);
		}
		
		if(!$this->_entryId)
			return false;
		
		$record = new $this->fetchClass($this->_ldapConn, $this->_entryId);
		if(!is_a($record, 'GO_Base_Ldap_Record'))
			throw new Exception($this->fetchClass.' is not a GO_Base_Ldap_Record subclass');
		return $record;
	}
	
	/**
	 * Count number of results.
	 * 
	 * @return int 
	 */
	public function rowCount(){
		return ldap_count_entries( $this->_ldapConn->getLink(), $this->_searchId);
	}
}