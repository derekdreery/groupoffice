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


	var $disable_message_cache=false;


	function __construct()
	{
		$this->email = new email();
		parent::__construct();
	}

	function set_account($account, $mailbox='INBOX'){
		$this->account = $account;

		if(!$this->folder || $this->folder['name']!=$mailbox){
			$this->folder = $this->email->get_folder($this->account['id'],$mailbox);

			if($this->folder)
				$this->folder_sort_cache=json_decode($this->folder['sort'], true);
		}
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
		//$start_time = getmicrotime();

		$this->set_account($account, $mailbox);

		//cache DNS in session. Seems to be faster with gmail somehow.
		/*if(empty($_SESSION['cached_imap'][$account['host']]))
		{
		$_SESSION['cached_imap'][$account['host']]=gethostbyname($account['host']);
		}*/


		$conn = parent::open($account['host'], $account['type'], $account['port'], $account['username'], $account['password'], $mailbox, null, $account['use_ssl'], $account['novalidate_cert']);

		
			

		//$end_time = getmicrotime();
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

	function view_part($uid, $part_no, $transfer, $part_charset = '') {
		
		if(!$this->conn){
			if(!$this->open($this->account, $this->folder['name'])){
				throw new Exception(sprintf($lang['email']['feedbackCannotConnect'], $this->account['host'],  $this->last_error(), $this->account['port']));
			}
		}

		return parent::view_part($uid, $part_no, $transfer, $part_charset);
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

	function get_message_with_body($uid, $return_plaintext_body=false, $create_temporary_attachment_files=false) {
		global $GO_CONFIG, $GO_MODULES, $GO_SECURITY, $GO_LANGUAGE, $lang;

		require_once($GO_LANGUAGE->get_language_file('email'));


		/*
		 * Check cache
		 */
		$this->get_cached_messages($this->folder['id'], array($uid), true);
		$values=$this->email->next_record();
		if(!$this->disable_message_cache && !empty($values['serialized_message_object'])){
			$message =  unserialize($values['serialized_message_object']);

			if($create_temporary_attachment_files) {
				for ($i = 0; $i < count($message['attachments']); $i ++) {
					$tmp_file = $GO_CONFIG->tmpdir.$message['attachments'][$i]['name'];
					$data = $this->view_part($uid, $message['attachments'][$i]['number'], $message['attachments'][$i]['transfer']);
					if($data && file_put_contents($tmp_file, $data)) {
						$message['attachments'][$i]['tmp_file']=$tmp_file;
					}
				}

				for ($i = 0; $i < count($message['url_replacements']); $i ++) {
					$attachment = $message['url_replacements'][$i]['attachment'];
					$tmp_file = $GO_CONFIG->tmpdir.$attachment['name'];
					$data = $this->view_part($uid, $attachment['number'], $attachment['transfer']);
					if($data && file_put_contents($tmp_file, $data)) {
						$message['url_replacements'][$i]['tmp_file']=$tmp_file;
					}
				}
			}
			//go_debug($message);
			return $message;
		}

		if(!$this->conn){
			if(!$this->open($this->account, $this->folder['name'])){
				throw new Exception(sprintf($lang['email']['feedbackCannotConnect'], $this->account['host'],  $this->last_error(), $this->account['port']));
			}
		}
		
		$message = $this->get_message($uid);

		if(!$message){
			throw new Exception($lang['email']['errorGettingMessage']);
		}

		$RFC822 = new RFC822();
		$address = $RFC822->parse_address_list($message['from']);

		$message['full_from']=htmlspecialchars($message['from'], ENT_QUOTES, 'UTF-8');

		$message['sender']=isset($address[0]['email']) ? htmlspecialchars($address[0]['email'], ENT_QUOTES, 'UTF-8') : '';
		$message['from']=isset($address[0]['personal']) ? htmlspecialchars($address[0]['personal'], ENT_QUOTES, 'UTF-8') : '';

		if(empty($message["subject"]))
		{
			$message['subject']= $lang['email']['no_subject'];
		}
		$message['subject']= htmlspecialchars($message['subject'], ENT_QUOTES, 'UTF-8');

		

		/*
		 * Sometimes clients send multipart/alternative but there's only a text part. FIrst check if there's
		 * a html alternative to display
		 */
		$html_alternative=false;
		if(!$return_plaintext_body) {
			for($i=0;$i<count($message['parts']);$i++) {
				if(stripos($message['parts'][$i]['mime'],'html')!==false && (strtolower($message['parts'][$i]['type'])=='alternative' || strtolower($message['parts'][$i]['type'])=='related')) {
					$html_alternative=true;
				}
			}
		}

		//go_debug($html_alternative);

		//$message['blocked_images']=0;
		$message['body']='';

		$attachments=array();

		if(stripos($message['content_type'],'html')!==false) {
			$default_mime = 'text/html';
		}else {
			$default_mime = 'text/plain';
		}

		$part_count = count($message['parts']);
		if($part_count==1) {
			//if there's only one part use the message parameters.
			if(stripos($message['parts'][0]['mime'],'plain')!==false)
				$message['parts'][0]['mime']=$default_mime;

			//go_debug($message['content_transfer_encoding']);
			//go_debug($message['parts'][0]['transfer']);

			if(!empty($message['content_transfer_encoding']) && (empty($message['parts'][0]['transfer']) || strtolower($message['parts'][0]['transfer'])=='7bit' || strtolower($message['parts'][0]['transfer'])=='8bit'))
				$message['parts'][0]['transfer']=$message['content_transfer_encoding'];
		}

		//go_debug($message['parts']);

		while($part = array_shift($message['parts'])) {
			$mime = isset($part["mime"]) ? strtolower($part["mime"]) : $default_mime;

			//some clients just send html
			if($mime=='html') {
				$mime = 'text/html';
			}

			/*go_debug($mime);
			go_debug($html_alternative);
			go_debug($part['type']);
			go_debug($part["disposition"]);
			go_debug('-----');*/

			if (/*empty($message['body']) &&*/
							(stripos($part["disposition"],'attachment')===false) &&
							(
							(stripos($mime,'html')!==false && !$return_plaintext_body) ||
											(stripos($mime,'plain')!==false && (!$html_alternative || strtolower($part['type'])!='alternative')) || $mime == "text/enriched" || $mime == "unknown/unknown")) {
				//go_debug('ja');
				
//go_debug($part_body);
//go_debug('######');
				switch($mime) {
					case 'unknown/unknown':
					case 'text/plain':
						$part_body = $this->view_part($uid, $part["number"], $part["transfer"], $part["charset"]);

						$uuencoded_attachments = $this->extract_uuencoded_attachments($part_body);

						$part_body = $return_plaintext_body ? $part_body : String::text_to_html($part_body);

						for($i=0;$i<count($uuencoded_attachments);$i++) {
							$attachment = $uuencoded_attachments[$i];
							$attachment['number']=$part['number'];
							unset($attachment['data']);
							$attachment['uuencoded_partnumber']=$i+1;

							$attachments[]=$attachment;
						}

						break;

					case 'text/html':
						$part_body = $this->view_part($uid, $part["number"], $part["transfer"], $part["charset"]);
						$part_body = $return_plaintext_body ?  String::html_to_text($part_body) : String::convert_html($part_body);
						break;

					case 'text/enriched':
						$part_body = $this->view_part($uid, $part["number"], $part["transfer"], $part["charset"]);
						$part_body = String::enriched_to_html($part_body);
						break;					
				}
				
				if(!empty($message['body']))
					$message['body'].='<hr style="margin:20px 0" />';
				$message['body'] .= trim($part_body);
			}else {
				$attachments[]=$part;
			}
		}

		$message['url_replacements']=array();
		$message['attachments']=array();
		$index=0;
		for ($i = 0; $i < count($attachments); $i ++) {

			if(empty($attachments[$i]['name'])){
				if(stripos($attachments[$i]['mime'],'calendar')!==false) {
					$attachments[$i]['name']=$lang['email']['event'].'.ics';
				}else
				{
					$attachments[$i]['name']=uniqid(time());
				}
			}

			if(strpos($attachments[$i]['name'],'.')===false && !empty($attachments[$i]["mime"]) && strpos($attachments[$i]["mime"], 'text')!==false){
				$attachments[$i]['name'].='.txt';
			}

			if (!empty($attachments[$i]["id"]) || $this->part_is_attachment($attachments[$i])) {
				//When a mail is saved as a task/appointment/etc. the attachments will be saved temporarily
				$attachments[$i]['tmp_file']=false;
				if($create_temporary_attachment_files) {
					$tmp_file = $GO_CONFIG->tmpdir.$attachments[$i]['name'];
					$data = $this->view_part($uid, $attachments[$i]['number'], $attachments[$i]['transfer']);
					if($data && file_put_contents($tmp_file, $data)) {
						$attachments[$i]['tmp_file']=$tmp_file;
					}
				}
			}

			if (!empty($attachments[$i]["id"])) {
				//when an image has an id it belongs somewhere in the text we gathered above so replace the
				//source id with the correct link to display the image.

				$tmp_id = $attachments[$i]["id"];
				if (strpos($tmp_id,'>')) {
					$tmp_id = substr($attachments[$i]["id"], 1,strlen($attachments[$i]["id"])-2);
				}
				$id = "cid:".$tmp_id;

				$url = $GO_MODULES->modules['email']['url']."attachment.php?account_id=".$this->account['id']."&mailbox=".urlencode($this->mailbox)."&amp;uid=".$uid."&amp;part=".$attachments[$i]["number"]."&amp;transfer=".$attachments[$i]["transfer"]."&amp;mime=".$attachments[$i]["mime"]."&amp;filename=".urlencode($attachments[$i]["name"]);

				$url_replacement['id'] = $attachments[$i]["id"];
				$url_replacement['url'] = $url;
				$url_replacement['tmp_file'] = $attachments[$i]['tmp_file'];
				//we need the attachment object later when we're creating temporary
				//attachment files from cache
				$url_replacement['attachment']=$attachments[$i];

				$message['url_replacements'][]=$url_replacement;

				if(strpos($message['body'], $id)) {
					$message['body'] = str_replace($id, $url, $message['body']);
				}else {
					//id was not found in body so add it as attachment later
					unset($attachments[$i]['id']);
				}
			}

			if ($this->part_is_attachment($attachments[$i])) {
				$attachments[$i]['index']=$index;
				$attachments[$i]['extension']=File::get_extension($attachments[$i]["name"]);
				$message['attachments'][]=$attachments[$i];
				$index++;
			}
		}

		// don't send very large texts to the browser because it will hang.
		if(strlen($message['body'])>512000){
			$message['body']=String::cut_string($message['body'], 521000, false);
		}

		$cached_message['uid']=$uid;
		$cached_message['folder_id']=$this->folder['id'];
		$cached_message['serialized_message_object']=serialize($message);
		$this->update_cached_message($cached_message);

		return $message;
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

	function get_cached_messages($folder_id, $uids, $with_full_cached_message=false)
	{
		$sql = "SELECT `folder_id`,`uid`,`account_id`,`new`,`subject`,`from`,".
			"`reply_to`,`size`,`udate`,`attachments`,`flagged`,`answered`,`priority`,".
			"`to`,`notification`,`content_type`,`content_transfer_encoding`";
		if($with_full_cached_message){
			$sql .= ",`serialized_message_object` ";
		}
		$sql .= "FROM em_messages_cache WHERE folder_id=".$this->email->escape($folder_id)." AND uid IN (".$this->email->escape(implode(',',$uids)).")";
		$this->email->query($sql);
	}

}