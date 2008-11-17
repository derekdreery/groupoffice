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
	
	var $folder_sort_cache;
	
	/**
	 * E-mail account record
	 *
	 * @var unknown_type
	 */
	var $account;
	
	var $filters=array();
	
	var $filtered=0;

	
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
		
		$this->folder_sort_cache=json_decode($this->folder['sort'], true);
		
		return $conn;
	}
	
	/**
	 * Sort message UID's into $this->sort (see imap_sort() PHP docs)
	 *
	 * @param	int	$sort_type	The column
	 * @param	string $reverse Reverse sorting (0 or 1)
	 * @param	string $search Search query
	 * @access public
	 * @return int	 Number of sorted messages
	 */
	function sort($sort_type = SORTDATE, $reverse = "1", $query = '') {
		
		$this->sort_type=$sort_type;
		$this->sort_reverse=$reverse;

		if ($query != '') {
			parent::sort($sort_type, $reverse, $query);
		} else {
			if($this->folder['msgcount']==$this->count && $this->folder['unseen']==$this->unseen && isset($this->folder_sort_cache[$sort_type.'_'.$reverse]))
			{
				debug('Used cached sort info');
				$this->sort = $this->folder_sort_cache[$sort_type.'_'.$reverse];
			}else
			{
				$this->sort = imap_sort($this->conn, $sort_type, $reverse, SE_UID+SE_NOPREFETCH);				
				$this->folder_sort_cache[$sort_type.'_'.$reverse]=$this->sort;
				
				$up_folder['id'] = $this->folder['id'];
				$up_folder['sort']=json_encode($this->folder_sort_cache);
				$up_folder['unseen']=$this->unseen;
				$up_folder['msgcount']=$this->count;
				
				$this->email->__update_folder($up_folder);
			}
		}
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
		/*$sql = "SELECT count(*) AS count FROM em_messages_cache WHERE new='1' AND folder_id=".$this->email->escape($this->folder['id'])." AND uid IN(".$this->email->escape(implode(',',$uids)).")";
		$this->query($sql);
		$record = $this->next_record();*/
		
		$sql = "DELETE FROM em_messages_cache WHERE folder_id=".$this->email->escape($this->folder['id'])." AND uid IN(".$this->email->escape(implode(',',$uids)).")";
		$this->email->query($sql);
		debug('Deleted '.implode(',', $uids).' from cache');
		if(isset($this->sort_reverse))
		{
			debug('Removed '.implode(',', $uids).' from sort');
			//remove uids from cached sort
			$sort = $this->sort;		
			$this->sort=array();
			foreach($sort as $uid)
			{
				if(!in_array($uid, $uids))
				{
					$this->sort[]=$uid;
				}
			}
			$this->folder_sort_cache[$this->sort_type.'_'.$this->sort_reverse]=$this->sort;
			
			$up_folder['id'] = $this->folder['id'];
			$up_folder['sort']=json_encode($this->folder_sort_cache);
			$up_folder['unseen']=$this->unseen;
			$up_folder['msgcount']=$this->count;
			
			$this->email->__update_folder($up_folder);
		}
	}
	
	function set_unseen_cache($uids, $new)
	{
		$new_val = $new ? '1' : '0';
		
		$sql = "UPDATE em_messages_cache SET new='".$new_val."' WHERE folder_id=".$this->email->escape($this->folder['id'])." AND uid IN(".$this->email->escape(implode(',',$uids)).")";
		$this->email->query($sql);
		
		$affected_rows = $this->email->affected_rows();
		
		if($affected_rows>0)
		{
			$operator = $new ? '+' : '-';
			
			$sql = "UPDATE em_folders SET unseen=unseen$operator? WHERE id=?";
			$this->email->query($sql, 'ii', array($affected_rows, $this->folder['id']));
			debug('Adding '.$operator.$affected_rows.' unseen');		
		}
	}
	
	function set_flagged_cache($uids, $flagged)
	{
		$new_val = $flagged ? '1' : '0';
		
		$sql = "UPDATE em_messages_cache SET flagged='".$new_val."' WHERE folder_id=".$this->email->escape($this->folder['id'])." AND uid IN(".$this->email->escape(implode(',',$uids)).")";
		$this->email->query($sql);
	}

	function get_message_headers($start, $limit, $sort_field , $sort_order, $query)
	{
		$uids = $this->get_message_uids($start, $limit, $sort_field , $sort_order, $query);
		
		$messages=array();
		$this->filtered=0;
		
		if(count($uids))
		{
			$this->get_cached_messages($this->folder['id'], $uids);
			
			//get messages from cache
			while($message = $this->email->next_record())
			{
				$messages[$message['uid']]=$message;
			}

			debug('Got '.count($messages).' from cache');
			
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
				$new_messages = $this->get_filtered_message_headers($uncached_uids);
				foreach($new_messages as $message)
				{
					$messages[$message['uid']]=$message;
					$message['folder_id']=$this->folder['id'];
					$message['account_id']=$this->account['id'];
					$this->add_cached_message($message);
				}
			}
			debug('Got '.count($uncached_uids).' from IMAP server');
			
			if($this->filtered>0)
			{
				debug('Extra messages start '.($this->first+$this->offset-$this->filtered));
				debug('Extra messages offset '.$this->filtered);
					
				$extra_messages = $this->get_message_headers($this->first+$this->offset-$this->filtered, $this->filtered, $sort_field , $sort_order, $query);
				foreach($extra_messages as $uid=>$message)
				{
					$messages[$uid]=$message;
				}
			}
		}
		return $messages;
	}
	
	function set_filters($filters)
	{
		$this->filters=$filters;
	}
	
	function get_filtered_message_headers($uids)
	{
		$messages=array();
		$this->filtered=0;
		
		$new_messages = parent::get_message_headers($uids);
		if(strtoupper($this->mailbox)!='INBOX')
		{
			return $new_messages;
		}
		
		while($message = array_shift($new_messages))
		{			
			if($message['new']=='1')
			{
				$continue=false;
				
				for ($i=0;$i<sizeof($this->filters);$i++)
				{
					$field = $message[$this->filters[$i]["field"]];

					if (stristr($field, $this->filters[$i]["keyword"]))
					{
						$move_messages = array($message['uid']);

						if($this->filters[$i]['mark_as_read'])
						{
							$ret = $this->set_message_flag($this->mailbox, $move_messages, "\\Seen");
						}
						
						//moving uses unseen and count for a cache update
						$this->unseen--;
						$this->count--;
						if ($this->move($this->filters[$i]["folder"], $move_messages))
						{							
							debug('Filtered:');
							debug($message);
							$this->filtered++;
							$continue=true;
							break;
						}else
						{
							$this->unseen++;
							$this->count++;
						}
					}												
				}
				if ($continue)
				{					
					//message was filtered so dont't add it						
					continue;
				}											
			}
			$messages[]=$message;
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
	
	
	function clear_cache($folder_id=0){
		if($folder_id==0)
		{
			$sql = "DELETE FROM em_messages_cache WHERE account_id=?";
			$this->email->query($sql, 'i', $this->account['id']);
			
			$sql = "UPDATE em_folders SET sort='' WHERE account_id=?";
			$this->email->query($sql, 'i', $this->account['id']);
		}else
		{
			$sql = "DELETE FROM em_messages_cache WHERE folder_id=?";
			$this->email->query($sql, 'i', $folder_id);
			
			$sql = "UPDATE em_folders SET sort='' WHERE id=?";
			$this->email->query($sql, 'i', $folder_id);
		}
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
	 * Gets a Cached message record
	 *
	 * @param Int $cached_message_id ID of the cached_message
	 *
	 * @access public
	 * @return Array Record properties
	 */

	function get_cached_messages($folder_id, $uids)
	{
		//TODO dont select all fields
		$this->email->query("SELECT * FROM em_messages_cache WHERE folder_id=".$this->email->escape($folder_id)." AND uid IN (".$this->email->escape(implode(',',$uids)).")");
	}

}