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

	var $folder_sort_cache = array();

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
		$start_time = getmicrotime();

		$this->account = $account;

		//cache DNS in session. Seems to be faster with gmail somehow.
		/*if(empty($_SESSION['cached_imap'][$account['host']]))
		{
		$_SESSION['cached_imap'][$account['host']]=gethostbyname($account['host']);
		}*/


		$conn = parent::open($account['host'], $account['type'], $account['port'], $account['username'], $account['password'], $mailbox, null, $account['use_ssl'], $account['novalidate_cert']);

		$this->folder = $this->email->get_folder($this->account['id'],$mailbox);

		if($this->folder)
		$this->folder_sort_cache=json_decode($this->folder['sort'], true);
			

		$end_time = getmicrotime();
		//go_debug('IMAP connect took '.($end_time-$start_time).'s');

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
			if($this->folder['msgcount']!=$this->count || $this->folder['unseen']!=$this->unseen)
			{
				//go_debug('Cleared sort cache');
				$this->folder_sort_cache=array();
			}
				
			if(isset($this->folder_sort_cache[$sort_type.'_'.$reverse]))
			{
				//go_debug('Used cached sort info');
				$this->sort = $this->folder_sort_cache[$sort_type.'_'.$reverse];
			}else
			{
				//go_debug('Got sort from IMAP server: '.$this->folder['msgcount'].' = '.$this->count.' && '.$this->folder['unseen'].' = '.$this->unseen);
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
	function move($folder, $messages, $expunge=true) {
		if(count($messages))
		{
			if(parent::move($folder, $messages, $expunge))
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

		if(!empty($this->folder['id']))
		{
			$sql = "DELETE FROM em_messages_cache WHERE folder_id=".$this->email->escape($this->folder['id'])." AND uid IN(".$this->email->escape(implode(',',$uids)).")";
			$this->email->query($sql);
			//go_debug('Deleted '.implode(',', $uids).' from cache');
			if(is_array($this->folder_sort_cache))
			{
				foreach($this->folder_sort_cache as $key=>$sort)
				{
					$this->folder_sort_cache[$key]=array();
					$removed=0;
					$total = count($uids);
					foreach($sort as $uid)
					{
						if($total==$removed || !in_array($uid, $uids))
						{
							$this->folder_sort_cache[$key][]=$uid;

						}else
						{
							$removed++;
							//go_debug('Removed '.$uid.' from sort cache '.$key);
						}
					}
				}
			}
			if(isset($this->sort_type))
			{
				//go_debug('Updated sort');
				$this->sort=$this->folder_sort_cache[$this->sort_type.'_'.$this->sort_reverse];
			}
				
			$up_folder['id'] = $this->folder['id'];
			$up_folder['sort']=json_encode($this->folder_sort_cache);

			//test
			$this->folder['unseen']=$up_folder['unseen']=$this->unseen;
			$this->folder['msgcount']=$up_folder['msgcount']=$this->count;


				
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
			//go_debug('Adding '.$operator.$affected_rows.' unseen');
		}

		return $affected_rows;
	}

	function set_flagged_cache($uids, $flagged)
	{
		$new_val = $flagged ? '1' : '0';

		$sql = "UPDATE em_messages_cache SET flagged='".$new_val."' WHERE folder_id=".$this->email->escape($this->folder['id'])." AND uid IN(".$this->email->escape(implode(',',$uids)).")";
		$this->email->query($sql);
	}

	function get_message_uids($first, $offset, $sort_type = SORTDATE, $reverse = "1", $query = '')
	{
		//get the unseen and total messages

		//if(imap_num_recent($this->conn))
		//{
		$status = $this->status($this->mailbox, SA_UNSEEN+SA_MESSAGES);
		if($status)
		{
			$this->unseen = $status->unseen;
			$this->count = $status->messages;
		}else
		{
			$this->unseen = $this->count = 0;
		}
		/*}else
		 {
			$this->unseen = $this->folder['unseen'];
			$this->count = $this->folder['msgcount'];
			go_debug('Used cached folder status');
			}*/
		$this->query = $query;
		$this->first = $first;
		$this->offset = $offset;

		//sort the uid's
		$this->sort($sort_type, $reverse, $query);

		return $this->get_uids_subset($first, $offset);
	}

	/**
	 * Get one message with the structure
	 *
	 * @param int $uid The unique identifier of the
	 * @param string $part Get a specific part of a message
	 * @access public
	 * @return array The E-mail message elements
	 */
	function get_message($uid, $fetchstructure=true, $nocache=false) {

		parent::get_message($uid, $fetchstructure);
		if($nocache)
		{
			return $this->message;
		}
		if ($this->message) {

			if(is_object($uid))
			{
				$uids = array($uid->uid);
			}else
			{
				$uids = array($uid);
			}
			$this->get_cached_messages($this->folder['id'], $uids);
			$values=$this->email->next_record();

			if($values)
			{
				$this->message['new']=$values['new'];
				$this->message['answered']=$values['answered'];
				$this->message['flagged']=$values['flagged'];
				$this->message['priority']=$values['priority'];
			}
		}
		return $this->message;
	}

	function get_message_headers($start, $limit, $sort_field , $sort_order, $query)
	{
		$uids = $this->get_message_uids($start, $limit, $sort_field , $sort_order, $query);

		//go_debug($uids);

		$messages=array();
		$this->filtered=array();

		if(count($uids))
		{
			$this->get_cached_messages($this->folder['id'], $uids);

			//get messages from cache
			while($message = $this->email->next_record())
			{
				$message['cached']=true;
				$messages[$message['uid']]=$message;
			}

			//go_debug('Got '.count($messages).' from cache');

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
					//trim values for mysql insertion
					$message['to']=substr($message['to'],0, 255);
					$message['subject']=substr($message['subject'],0,100);
					$message['from']=substr($message['from'],0,100);
					$message['reply_to']=substr($message['reply_to'],0,100);
					$message['udate']=intval($message['udate']);

					$messages[$message['uid']]=$message;
					$messages[$message['uid']]['cached']=false;

					$message['folder_id']=$this->folder['id'];
					$message['account_id']=$this->account['id'];
					$this->add_cached_message($message);
				}
			}
			//go_debug('Got '.count($uncached_uids).' from IMAP server');

			if(count($this->filtered))
			{
				//go_debug('Filtered messages:'.count($this->filtered));

				$newstart = count($messages);
				$newlimit = $newstart+count($this->filtered);

				$extra_messages = $this->get_message_headers($newstart, $newlimit, $sort_field , $sort_order, $query);
				foreach($extra_messages as $uid=>$message)
				{
					$messages[$uid]=$message;
				}
				$this->filtered=array();
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
		
		$this->filtered=array();
		for ($i=0;$i<sizeof($this->filters);$i++)
		{
			$this->filters[$i]['uids']=array();
		}

		$new_messages = parent::get_message_headers($uids);
		if(strtoupper($this->mailbox)!='INBOX')
		{
			return $new_messages;
		}

		foreach($new_messages as $message)
		{
			if($message['new']=='1')
			{
				$continue=false;

				for ($i=0;$i<sizeof($this->filters);$i++)
				{
					$field = $message[$this->filters[$i]["field"]];

					if (stripos($field,$this->filters[$i]["keyword"])!==false)// ('/'.preg_quote($this->filters[$i]["keyword"]).'/i', $field))
					{
						$this->filters[$i]['uids'][]=$message['uid'];
						$continue=true;
						break;
					}
				}
				if ($continue)
				{
					//message was filtered so dont't add it
					continue;
				}
			}			
		}

		for ($i=0;$i<sizeof($this->filters);$i++)
		{
			if(isset($this->filters[$i]['uids']) && count($this->filters[$i]['uids']))
			{
				if($this->filters[$i]['mark_as_read'])
				{
					$ret = $this->set_message_flag($this->mailbox, $this->filters[$i]['uids'], "\\Seen");
				}
				if(parent::move($this->utf7_imap_encode($this->filters[$i]["folder"]), $this->filters[$i]['uids'],false))
				{
					foreach($this->filters[$i]['uids'] as $uid)
					{
						$this->filtered[]=$uid;
					}
				}
			}
		}
		if(count($this->filtered))
		{
			$this->expunge();

			$this->unseen-=count($this->filtered);
			$this->count-=count($this->filtered);
				

			$this->delete_cached_messages($this->filtered);
		}


		if(count($this->filtered)){
			$messages=array();
			while($message = array_shift($new_messages)){
				if(!in_array($message['uid'], $this->filtered)){
					$messages[]=$message;
				}				
			}
			return $messages;
		}else
		{
			return $new_messages;
		}
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
		return $this->email->update_row('em_messages_cache', array('uid', 'folder_id'), $cached_message);
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