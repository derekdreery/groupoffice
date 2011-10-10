<?php
class GO_Base_Mail_EmailRecipients{
	public function __construct(String $emailRecipientList){
		$this->addString($recipientListString);
	}
	
	public function addRecipient($email, $personal=''){
		$this->_addresses[trim($email)]=trim($personal);
	}	
	
	public function getAddresses(){
		return $this->_addresses;
	}
	
	public function __toString() {
		$str = '';
		foreach($this->_addresses as $email=>$personal){
			if(!empty($personal)){
				$str .= '"'.$personal.'" <'.$email.'>, ';
			}else
			{
				$str .= $email.', ';
			}
		}
		
		return rtrim($str,', ');
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
	* Parses an RFC822 formatted string 
	* (eg. "Merijn Schering" <mschering@intermesh.nl>)
	*
	* @param	string	$recipientListString	The address list to parse
	* @access public
	* @return array 	With addresses containing 'personal' and 'email'
	*/
	public function addString($recipientListString)
	{
		//initiate addresses array
		//$this->_addresses = array();
		
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
						$this->buffer .= $char;				
					}else
					{
						$this->_addBuffer();
					}
				break;
			

				default:					
					$this->buffer .= $char;
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
}