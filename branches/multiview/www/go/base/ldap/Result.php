<?php
namespace GO\Base\Ldap;

class Result{
	
	/**
	 * The LDAP connection
	 * 
	 * @var \GO\Base\Ldap\Connection 
	 */
	private $_ldapConn;
	private $_searchId;
	
	private $_numEntry;
	
	private $_entryId;
	
	public function __construct(\GO\Base\Ldap\Connection $ldapConn, $searchId) {
		$this->_searchId=$searchId;
		$this->_ldapConn=$ldapConn;
	}
	
	/**
	 * Fetch the next record or return false if there's none.
	 * 
	 * @return \GO\Base\Ldap\Record 
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
		
		return new \GO\Base\Ldap\Record($this->_ldapConn, $this->_entryId);
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