<?php
class GO_Base_Mail_EmailRecipients{
	/**
	 * Pass a e-mail string like:
	 * 
	 * "Merijn Schering" <mschering@intermesh.nl>,someone@somedomain.com,Pete <pete@pete.com
	 * 
	 * @param string $emailRecipientList 
	 */
	public function __construct($emailRecipientList=''){
		$this->addString($emailRecipientList);
	}
	
	/**
	 * Create a list for a sinlge recipient.
	 * 
	 * Useful to format a single addres like this:
	 * 
	 * (string) GO_Base_Mail_EmailRecipients::createSingle("john@example.com", "John Smith");
	 * 
	 * @param string $email
	 * @param string $personal
	 * @return GO_Base_Mail_EmailRecipients 
	 */
	public static function createSingle($email, $personal){
		$l = new GO_Base_Mail_EmailRecipients();
		$l->addRecipient($email, $personal);
		return $l;
	}
	
	/**
	 * Add a recipient to the list.
	 * 
	 * @param string $email
	 * @param string $personal 
	 */
	public function addRecipient($email, $personal=null){
		//echo $email.' '.$personal.'<br />';
		if(empty($email))
			return false;

		$this->_addresses[trim($email)]=!empty($personal) ? trim($personal) : null;
	}
	
	/**
	 * Check if an e-mail address is in this list
	 * 
	 * @param string $email
	 * @return boolean 
	 */
	public function hasRecipient($email){
//		return isset($this->_addresses[$email]);
		return array_key_exists($email, $this->_addresses);
	}

	
	/**
	 * Count the addresses
	 * 
	 * @return int 
	 */
	public function count(){
		return count($this->_addresses);
	}
	
	/**
	 * Remove a recipient from the list.
	 * 
	 * @param string $email 
	 */
	public function removeRecipient($email){
		unset($this->_addresses[trim($email)]);
	}
	/**
	 * Get the addresses in an array('email@address.com'=>'Personal name'))
	 * 
	 * @return array  
	 */
	public function getAddresses(){
		return $this->_addresses;
	}
	
	/**
	 * Get the next address as array('email'=>'example@domain.com','personal'=>'Example name');
	 * 
	 * @return array  
	 */
	public function getAddress(){
		reset($this->_addresses);
		$each = each($this->_addresses);
		return $each ? array('email'=>$each['key'], 'personal'=>empty($each['value']) ? $each['key'] : $each['value']) : false;
	}
	
//	public function getEmail($index=0){
//		return isset($this->_addresses[$index]['email']) ? $this->_addresses[$index]['email'] : '';
//	}
//	
//	public function getPersonal($index=0){
//		return isset($this->_addresses[$index]['personal']) ? $this->_addresses[$index]['personal'] : '';
//	}
	
	public function __toString() {
		$str = '';
		foreach($this->_addresses as $email=>$personal){			
			$str .= $this->_formatAddress($email, $personal).', ';			
		}		
		return rtrim($str,', ');
	}
	
	/**
	 * Get an array of formatted address:
	 * 
	 * $a[] = '"John Doe" <john@domain.com>';
	 * 
	 * @return array
	 */
	public function getArray(){
		$array = array();
		foreach($this->_addresses as $email=>$personal){			
			$array[] = $this->_formatAddress($email, $personal);			
		}		
		return $array;
	}
	
	
	private function _formatAddress($email, $personal){
		if(!empty($personal)){
			return '"'.$personal.'" <'.$email.'>';
		}else
		{
			return $email;
		}
	}
	
	/**
	* The array of parsed addresses
	*
	* @var     array
	* @access  private
	*/
	private $_addresses = array();
	
	/**
	* Temporary storage of personal info of an e-mail address
	*
	* @var     string
	* @access  private
	*/
	private $_personal = false;
	
	/**
	* Temporary storage
	*
	* @var     string
	* @access  private
	*/
	private $_buffer = '';
	
	/**
	* Bool to check if a string is quoted or not
	*
	* @var     bool
	* @access  private
	*/
	private $_quote = false;
	
	/**
	* Bool to check if we found an e-mail address
	*
	* @var     bool
	* @access  private
	*/

	private $_emailFound=false;
	

	/**
	 * Pass a e-mail string like:
	 * 
	 * "Merijn Schering" <mschering@intermesh.nl>,someone@somedomain.com,Pete <pete@pete.com
	 * 
	 * @param string $emailRecipientList 
	 */
	public function addString($recipientListString)
	{
		//initiate addresses array
		//$this->_addresses = array();

		$recipientListString = trim($recipientListString,',; ');
		
		
		
		for($i=0;$i<strlen($recipientListString);$i++)
		{
			$char = $recipientListString[$i];	
			
			switch($char)
			{
				case '"':
					$this->_handleQuote($char);
				break;
				
				case "'":
					$this->_handleQuote($char);
				break;
				
				case '<':
					$this->_personal = trim($this->_buffer);
					$this->_buffer = '';
					$this->_emailFound=true;
				break;
				
				case '>':
					//do nothing		
				break;							
				
				case ',':
				case ';':
					if($this->_quote || (!$this->_emailFound && !GO_Base_Util_String::validate_email(trim($this->_buffer))))
					{
						$this->_buffer .= $char;				
					}else
					{
						$this->_addBuffer();
					}
				break;
			

				default:					
					$this->_buffer .= $char;
				break;
			}			
		}
		$this->_addBuffer();
		
		return $this->_addresses;	
	}

	
	/**
	* Adds the current buffers to the addresses array
	*
	* @access private
	* @return void
	*/
	private function _addBuffer()
	{
		if(!empty($this->_personal) && empty($this->_buffer)){
			$this->_buffer = 'noaddress';
		}
		if(!empty($this->_buffer))
		{
			$this->addRecipient($this->_buffer, $this->_personal);
		}
		$this->_buffer = '';
		$this->_personal = false;
		$this->_emailFound=false;
		$this->_quote=false;
	}
	
	/**
	* Hanldes a quote character (' or ")
	*
	* @access private
	* @return void
	*/
	private function _handleQuote($char)
	{
		if(!$this->_quote && trim($this->_buffer)=="")
		{
			$this->_quote = $char;
		}elseif($char == $this->_quote)
		{
			$this->_quote = false;
		}else
		{
			$this->_buffer .= $char;			
		}
	}
	
	/**
	 * Merge two address strings
	 * 
	 * @param GO_Base_Mail_EmailRecipients $recipients
	 * @return GO_Base_Mail_EmailRecipients 
	 */
	public function mergeWith(GO_Base_Mail_EmailRecipients $recipients){
		$this->_addresses = array_merge($this->_addresses, $recipients->getAddresses());
		
		return $this;
	}
}
