<?php
/**
* @copyright Intermesh 2004
* @author Merijn Schering <mschering@intermesh.nl>
* @version $Revision: 1.3 $ $Date: 2005/03/27 22:27:18 $
*
* This program is free software; you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the License, or (at your
* option) any later version.
**/

/**
* This class is used to parse and produce RFC822 formatted E-mail strings.
* It was written because imap_rfc822_parse_addr_list() does a terrible job.
*
* @package Framework
* @subpackage Mail
* @author   Merijn Schering <mschering@intermesh.nl>
* @since    Group-Office 2.10
*/

class RFC822
{
	/**
	* The array of parsed addresses
	*
	* @var     array
	* @access  private
	*/
	var $addresses = array();
	
	/**
	* Temporary storage of personal info of an e-mail address
	*
	* @var     string
	* @access  private
	*/
	var $personal = false;
	
	/**
	* Temporary storage
	*
	* @var     string
	* @access  private
	*/
	var $buffer = '';
	
	/**
	* Bool to check if a string is quoted or not
	*
	* @var     bool
	* @access  private
	*/
	var $quote = false;
	
	/**
	* Parses an RFC822 formatted string 
	* (eg. "Merijn Schering" <mschering@intermesh.nl>)
	*
	* @param	string	$address_list	The address list to parse
	* @access public
	* @return array 	With addresses containing 'personal' and 'email'
	*/
	function parse_address_list($address_list)
	{
		//initiate addresses array
		$this->addresses = array();
		
		for($i=0;$i<strlen($address_list);$i++)
		{
			$char = $address_list[$i];	
			
			switch($char)
			{
				case '"':
					$this->handle_quote($char);
				break;
				
				case "'":
					$this->handle_quote($char);
				break;
				
				case '<':
					$this->personal = trim($this->buffer);
					$this->buffer = '';
				break;
				
				case '>':
					//do nothing		
				break;							
				
				case ',':			
					if($this->quote)
					{
						$this->buffer .= $char;				
					}else
					{
						$this->add_address();
					}
				break;
				
				case ';':			
					if($this->quote)
					{
						$this->buffer .= $char;				
					}else
					{
						$this->add_address();
					}
				break;
			

				default:
					$this->buffer .= $char;
				break;
			}			
		}
		$this->add_address();
		
		return $this->addresses;	
	}
	
	/**
	* Explodes an RFC822 formatted string 
	* (eg. "Merijn Schering" <mschering@intermesh.nl>) into an array.
	*
	* @param	string	$address_list	The address list to parse
	* @access public
	* @return array 	With addresses containing the full RFC822 formatted email address.
	*/
	function explode_address_list($address_list)
	{
		$full_addresses = array();
		
		$addresses = $this->parse_address_list($address_list);
		foreach($addresses as $address)
		{
			$full_addresses[] = $this->write_address($address['personal'], $address['email']);
		}
		return $full_addresses;
	}
	
	/**
	* Produces an RFC822 formatted string 
	* (eg. "Merijn Schering" <mschering@intermesh.nl>).
	*
	* @param	string	$personal	The personal part
	* @param	string	$email	The email address
	* @access public
	* @return string  RFC822 formatted address
	*/
	
	function write_address($personal, $email)
	{
		if($email != '')
		{
			if($personal == '')
			{
				return trim($email);
			}else
			{
				return '"'.str_replace('"', '\"', $personal).'" <'.$email.'>';
			}
		}else
		{
			return false;
		}
	}
	
	/**
	* Tidy an address string
	* (eg. "Merijn Schering" <mschering@intermesh.nl>).
	*
	* @param	string	$address_list	The address list
	* @access public
	* @return string  RFC822 formatted address list
	*/
	function reformat_address_list($address_list)
	{
		$addresses = $this->parse_address_list($address_list);
		$formatted = array();
		foreach ($addresses as $address)
		{     	
			$formatted[] = $this->write_address($address['personal'], $address['email']);	
		}
		return implode(',', $formatted);
	}
	
	/**
	* Adds the current buffers to the addresses array
	*
	* @access private
	* @return void
	*/
	function add_address()
	{
		$address['personal'] = ($this->personal && (trim($this->personal) != '')) ? trim($this->personal) : trim($this->buffer);
		$address['email'] = trim($this->buffer);
		
		if($address['email'] != '')
		{
			$this->addresses[] = $address;
		}
		$this->buffer = '';
		$this->personal = false;
	}
	
	/**
	* Hanldes a quote character (' or ")
	*
	* @access private
	* @return void
	*/
	function handle_quote($char)
	{
		if(!$this->quote)
		{
			$this->quote = $char;
		}elseif($char == $this->quote)
		{
			$this->quote = false;
		}else
		{
			$this->buffer .= $char;			
		}
	}
}
