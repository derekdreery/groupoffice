<?php
class cached_imap extends imap{

	/**
	 * E-mail module object to connect to the database
	 *
	 * @var unknown_type
	 */
	var $email;
	
	/**
	 * The opened folder in the database cache
	 *
	 * @var unknown_type
	 */
	var $folder;
	
	/**
	 * E-mail account record
	 *
	 * @var unknown_type
	 */
	var $account;
	
	function __construct()
	{
		$this->email = new email();
		parent::__construct();
	}
	
	/**
	 * Opens a connection to server
	 *
	 * @param	string	$host					The hostname of the mailserver
	 * @param	string	$type					The type of the mailserver (IMAP or POP-3)
	 * @param	int 			$port 					The port to connect to
	 * @param	string	$username	The username
	 * @param	string	$password		The password
	 * @param	string	$mailbox			The mailbox to open
	 * @param	string	$flags					Connection flags (See PHP docs imap_open()
	 * @param	bool		$ssl						Connect in SSL mode or not
	 * @param	bool		$novalidate_cert						Don't validate SSL certificate
	 * @access public
	 * @return mixed	The recource ID on success or false on failure
	 */
	function open($account, $mailbox='INBOX') {
		
		$this->account = $account;
		
		$conn = parent::open($account['host'], $account['type'], $account['port'], $account['username'], $account['password'], $mailbox, null, $account['use_ssl'], $account['novalidate_cert']);
		
		$this->folder = $this->email->get_folder($this->account['id'],$mailbox);
		
		return $conn;
	}
	

	
	/**
		* Delete messages from the IMAP server
		*
		* @param Array $messages An array of message UID's
		* @access public
		* @return void
		*/

	function delete($messages) {
		if(count($messages))
		{
			if(parent::delete($messages))
			{
				$this->delete_cached_messages($messages);
				return true;
			}
		}
		return false;
	}
	
	/**
		* Move messages to another mailbox
		*
		* @param String $folder The mailbox where the messages need to go
		* @param Array $messages An array of message UID's to move
		* @access public
		* @return bool True on success
		*/
	function move($folder, $messages) {
		if(count($messages))
		{
			if(parent::move($folder, $messages))
			{
				$this->delete_cached_messages($messages);
				return true;
			}
		}
		return false;
	}
	
	function delete_cached_messages($uids)
	{
		$sql = "DELETE FROM em_messages_cache WHERE folder_id=".$this->email->escape($this->folder['id'])." AND uid IN(".$this->email->escape(implode(',',$uids)).")";
		return $this->email->query($sql);
	}
	

	function get_message_headers($uids)
	{
		$messages=array();
		
		if(count($uids))
		{
			$this->get_cached_messages($this->folder['id'], $uids);
			
			//get messages from cache
			while($message = $this->email->next_record())
			{
				$messages[$message['uid']]=$message;
			}		
			
			$uncached_uids=array();
			for($i=0;$i<count($uids);$i++)
			{
				if(!isset($messages[$uids[$i]]))
				{
					$uncached_uids[]=$uids[$i];
				}
			}			
			
			if(count($uncached_uids))
			{
				$new_messages = parent::get_message_headers($uncached_uids);
				foreach($new_messages as $message)
				{
					$messages[$message['uid']]=$message;
					$message['folder_id']=$this->folder['id'];
					$this->add_cached_message($message);
				}
			}
		}
		return $messages;
	}
	
	
	
	/**
	 * Add a Cached message
	 *
	 * @param Array $cached_message Associative array of record fields
	 *
	 * @access public
	 * @return int New record ID created
	 */

	function add_cached_message($cached_message)
	{
		return $this->email->insert_row('em_messages_cache', $cached_message);
	}

	/**
	 * Update a Cached message
	 *
	 * @param Array $cached_message Associative array of record fields
	 *
	 * @access public
	 * @return bool True on success
	 */

	function update_cached_message($cached_message)
	{
		return $this->email->update_row('em_messages_cache', 'id', $cached_message);
	}


	/**
	 * Delete a Cached message
	 *
	 * @param Int $cached_message_id ID of the cached_message
	 *
	 * @access public
	 * @return bool True on success
	 */

	function delete_cached_message($folder_id, $uid)
	{
		return $this->email->query("DELETE FROM em_messages_cache WHERE uid=? AND folder_id=?", 'ii', array($uid, $folder_id));
	}


	/**
	 * Gets a Cached message record
	 *
	 * @param Int $cached_message_id ID of the cached_message
	 *
	 * @access public
	 * @return Array Record properties
	 */

	function get_cached_messages($folder_id, $uids)
	{
		$this->email->query("SELECT * FROM em_messages_cache WHERE folder_id=".$this->email->escape($folder_id)." AND uid IN (".$this->email->escape(implode(',',$uids)).")");
	}

}