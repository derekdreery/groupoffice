<?php

require_once($GLOBALS['GO_CONFIG']->class_path.'mail/imap.class.inc.php');
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

	/*
	 * You can disable the cache for debugging.
	 * If enabled the message will be converted to safe HTML only once.
	 */
	var $disable_message_cache=false;


	function __construct()
	{
		$this->email = new email();
		//parent::__construct();
	}

	function set_account($account, $mailbox='INBOX'){
		$this->account = $account;

		if(!$this->folder || $this->folder['name']!=$mailbox){
			$this->folder = $this->email->get_folder($this->account['id'],$mailbox);

			if($this->folder)
				$this->folder_sort_cache=unserialize($this->folder['sort']);
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


		$conn = parent::connect($account['host'], $account['port'], $account['username'], $account['password'], $account['use_ssl']);

		$this->select_mailbox($mailbox);
		
			

		//$end_time = getmicrotime();
		//go_debug('IMAP connect took '.($end_time-$start_time).'s');

		return $conn;
	}

	function set_message_flag($uid_array, $flags, $clear=false) {
		if(!$this->handle){
			$this->set_account($this->account, $this->selected_mailbox['name']);
			$this->open($this->account, $this->selected_mailbox['name']);
		}
		return parent::set_message_flag($uid_array, $flags, $clear);
	}

	function select_mailbox($mailbox = "INBOX") {
		if(parent::select_mailbox($mailbox)){
			//update $this->folder with the db cache
			$this->set_account($this->account, $mailbox);
			return true;
		}else
		{
			return false;
		}
	}

	/**
	 * Sort message UID's into $this->sort (see imap_sort() PHP docs)
	 *
	 * @param	int	$sort_type	The column
	 * @param	string $reverse Reverse sorting (0 or 1)
	 * @param	string $search Search query
	 * @access public
	 * @return int	 Number of sorted messages
	
	function sort_mailbox($sort_type = SORTDATE, $reverse = "1", $filter = 'ALL') {

		$this->sort_type=$sort_type;
		$this->sort_reverse=$reverse;

		if ($query != '') {
			parent::sort($sort_type, $reverse, $filter);
		} else {
			if($this->folder['msgcount']!=$this->count || $this->folder['unseen']!=$this->unseen)
			{
				go_debug('Cleared sort cache');
				$this->folder_sort_cache=array();
			}
				
			if(isset($this->folder_sort_cache[$sort_type.'_'.$reverse]))
			{
				go_debug('Used cached sort info');
				$this->sort = $this->folder_sort_cache[$sort_type.'_'.$reverse];
			}else
			{
				go_debug('Got sort from IMAP server: '.$this->folder['msgcount'].' = '.$this->count.' && '.$this->folder['unseen'].' = '.$this->unseen);
				$this->sort = imap_sort($this->conn, $sort_type, $reverse, SE_UID+SE_NOPREFETCH);
				$this->folder_sort_cache[$sort_type.'_'.$reverse]=$this->sort;

				$up_folder['id'] = $this->folder['id'];
				$up_folder['sort']=serialize($this->folder_sort_cache);
				$up_folder['unseen']=$this->unseen;
				$up_folder['msgcount']=$this->count;

				$this->email->__update_folder($up_folder);
			}
		}
	} */



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
	function move($uids, $mailbox) {
		if(count($messages))
		{
			if(parent::move($uids, $mailbox))
			{
				$this->delete_cached_messages($uids);
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

			/*
			 * this doesn't work because we don't know how many unseen messages have
			 * been deleted.

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
							go_debug('Removed '.$uid.' from sort cache '.$key);
						}
					}
				}
			}
			if(isset($this->sort_type))
			{
				go_debug('Updated sort');
				$this->sort=$this->folder_sort_cache[$this->sort_type.'_'.$this->sort_reverse];
			}
				
			$up_folder['id'] = $this->folder['id'];
			$up_folder['sort']=serialize($this->folder_sort_cache);


			$status = $this->status($this->mailbox, SA_UNSEEN+SA_MESSAGES);
			if($status)
			{
				$this->unseen = $status->unseen;
				$this->count = $status->messages;
			}else
			{
				$this->unseen = $this->count = 0;
			}
			$this->folder['unseen']=$up_folder['unseen']=$this->unseen;
			$this->folder['msgcount']=$up_folder['msgcount']=$this->count;
			*/

			$up_folder['id'] = $this->folder['id'];
			$up_folder['sort']='';
				
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


	function get_message_part($uid, $message_part, $peek=false) {
		
		if(!$this->handle){
			if(!$this->open($this->account, $this->folder['name'])){
				throw new Exception(sprintf($lang['email']['feedbackCannotConnect'], $this->account['host'],  $this->last_error(), $this->account['port']));
			}
		}

		return parent::get_message_part($uid, $message_part, $peek);
	}



	function get_message_with_body($uid, $create_temporary_attachment_files=false, $create_temporary_inline_attachment_files=false, $peek=false) {
		global $GO_CONFIG, $GO_MODULES, $GO_SECURITY, $GO_LANGUAGE, $lang;

		require_once($GO_LANGUAGE->get_language_file('email'));


		/*
		 * Check cache
		 */
		$this->get_cached_messages($this->folder['id'], array($uid), true);
		$values=$this->email->next_record();
		if(!$this->disable_message_cache && !empty($values['serialized_message_object'])){
			$message =  unserialize($values['serialized_message_object']);
			$message['from_cache']=true;
			$message['new']=$values['new'];
			if($create_temporary_attachment_files) {
				for ($i = 0; $i < count($message['attachments']); $i ++) {
					$tmp_file = $GO_CONFIG->tmpdir.$message['attachments'][$i]['name'];
					$data = $this->view_part($uid, $message['attachments'][$i]['number'], $message['attachments'][$i]['transfer'], '', $peek);
					if($data && file_put_contents($tmp_file, $data)) {
						$message['attachments'][$i]['tmp_file']=$tmp_file;
					}
				}
			}
			if($create_temporary_inline_attachment_files) {
				for ($i = 0; $i < count($message['url_replacements']); $i ++) {
					$attachment = $message['url_replacements'][$i]['attachment'];
					
					$tmp_file = $GO_CONFIG->tmpdir.$attachment['name'];
					$data = $this->view_part($uid, $attachment['number'], $attachment['transfer'], '', $peek);
					
					if($data && file_put_contents($tmp_file, $data)) {
						$message['url_replacements'][$i]['tmp_file']=$tmp_file;
					}
				}
			}
			//go_debug($message);
			return $message;
		}

		if(!$this->handle){
			if(!$this->open($this->account, $this->folder['name'])){
				throw new Exception(sprintf($lang['email']['feedbackCannotConnect'], $this->account['host'],  $this->last_error(), $this->account['port']));
			}
		}
		
		//$message = $this->get_message($uid);
		if(!$values){
			$headers = parent::get_message_headers(array($uid));
			$message=$this->imap_message_to_cache($headers[0]);
		}else
		{
			$message=$values;
		}

		if(!$message){
			throw new Exception($lang['email']['errorGettingMessage']);
		}

		$RFC822 = new RFC822();
		$address = $RFC822->parse_address_list($message['from']);

		$message['full_from']=$message['from'];

		$message['sender']=isset($address[0]['email']) ? $address[0]['email'] : '';
		$message['from']=isset($address[0]['personal']) ? $address[0]['personal'] : '';

		$message['to_string']='';
		$to=array();
		if(!empty($message['to']))
		{
			$addresses = $RFC822->parse_address_list($message['to']);
			foreach($addresses as $address)
			{
				$message['to_string'].= $RFC822->write_address($address['personal'], $address['email']).', ';
				
				$to[] = array('email'=>$address['email'],
				'name'=>$address['personal']);
			}
			$message['to_string']=substr($message['to_string'],0,-2);			
		}else
		{
			$to[]=array('email'=>'', 'name'=> $lang['common']['none']);
		}
		$message['to']=$to;


		$message['cc_string']='';
		$cc=array();
		if(!empty($message['cc']))
		{
			$addresses = $RFC822->parse_address_list($message['cc']);
			foreach($addresses as $address)
			{
				$message['cc_string'].= $RFC822->write_address($address['personal'], $address['email']).', ';

				$cc[] = array('email'=>$address['email'],
				'name'=>$address['personal']);
			}
			$message['cc_string']=substr($message['cc_string'],0,-2);
		}
		$message['cc']=$cc;


		//TODO get bcc from IMAP server
		$message['bcc_string']='';
		$bcc=array();
		if(!empty($message['bcc']))
		{
			$addresses = $RFC822->parse_address_list($message['bcc']);
			foreach($addresses as $address)
			{
				$message['bcc_string'].= $RFC822->write_address($address['personal'], $address['email']).', ';

				$bcc[] = array('email'=>$address['email'],
				'name'=>$address['personal']);
			}
			$message['bcc_string']=substr($message['bcc_string'],0,-2);
		}
		$message['bcc']=$bcc;


		if(empty($message["subject"]))
		{
			$message['subject']= $lang['email']['no_subject'];
		}
		//$message['subject']= htmlspecialchars($message['subject'], ENT_COMPAT, 'UTF-8');


		$struct = $this->get_message_structure($uid);
			
		$plain_part = $this->find_message_part($struct,0,'text', 'plain');
		$html_part = $this->find_message_part($struct,0,'text', 'html');

		//use this array later to find attachments. The body parts will be skipped.
		$body_ids=array();
		if($plain_part){
			$body_ids[]=$plain_part['imap_id'];
			$message['plain_body']=$this->get_message_part_decoded($uid,$plain_part['imap_id'],$plain_part['encoding'], $plain_part['charset']);
		}
		if($html_part){
			$body_ids[]=$html_part['imap_id'];
			$message['html_body']=$this->get_message_part_decoded($uid,$html_part['imap_id'],$html_part['encoding'], $html_part['charset']);
		}

		
		if(empty($message['html_body'])){
			$message['html_body']=String::text_to_html($message['plain_body']);
		}else
		{
			$message['html_body']=String::convert_html($message['html_body']);
		}

		if(empty($message['plain_body'])){
			$message['plain_body']=String::html_to_text($message['html_body']);
		}

		$message['attachments']=$this->find_message_attachments($struct, $body_ids);
		for($i=0,$max=count($message['attachments']);$i<$max;$i++){
			$message['attachments'][$i]['extension']=File::get_extension($message['attachments'][$i]['name']);
			$message['attachments'][$i]['human_size']=Number::format_size($message['attachments'][$i]['size']);
		}
		go_debug($struct);
		//go_debug($message);
		return $message;
		

		$attachments=array();

				
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
											(stripos($mime,'html')!==false) ||
											(stripos($mime,'plain')!==false || $mime == "text/enriched" || $mime == "unknown/unknown"))
							) {

				
				
				$plain_part = '';
				$html_part = '';

				switch($mime) {
					case 'unknown/unknown':
					case 'text/plain':
						$plain_part = $this->view_part($uid, $part["number"], $part["transfer"], $part["charset"], $peek);

						$uuencoded_attachments = $this->extract_uuencoded_attachments($plain_part);

						if(!$html_alternative || (strtolower($part['type'])!='related' && strtolower($part['type'])!='alternative')){
							$html_part = String::text_to_html($plain_part);							
						}
						

						for($i=0;$i<count($uuencoded_attachments);$i++) {
							$attachment = $uuencoded_attachments[$i];
							$attachment['number']=$part['number'];
							unset($attachment['data']);
							$attachment['uuencoded_partnumber']=$i+1;

							$attachments[]=$attachment;
						}

						break;

					case 'text/html':
						$html_part = String::convert_html($this->view_part($uid, $part["number"], $part["transfer"], $part["charset"], $peek));

						if(!$plain_alternative || (strtolower($part['type'])!='related' && strtolower($part['type'])!='alternative')) {
							$plain_part = String::html_to_text($html_part);
						}
						break;

					case 'text/enriched':
						$html_part = String::enriched_to_html($this->view_part($uid, $part["number"], $part["transfer"], $part["charset"], $peek));
						if(!$plain_alternative || (strtolower($part['type'])!='related' && strtolower($part['type'])!='alternative')){
							$plain_part = String::html_to_text($html_part);
						}

						
						break;					
				}

				if(!empty($part['name']))
					$attachments[]=$part;


				if(!empty($message['html_body']) && !empty($html_part)){
					$message['html_body'].='<hr style="margin:20px 0" />';
				}
				$message['html_body'] .= $html_part;
				if(!empty($message['plain_body']) && !empty($plain_part)){
					$message['plain_body'].="\n\n-----\n\n";
				}
				$message['plain_body'] .= $plain_part;
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
					$data = $this->view_part($uid, $attachments[$i]['number'], $attachments[$i]['transfer'], '', $peek);
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

				if(strpos($message['html_body'], $id)) {
					$message['html_body'] = str_replace($id, $url, $message['html_body']);
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
		if(strlen($message['html_body'])>512000){
			$message['html_body']=String::cut_string($message['html_body'], 521000, false);
		}
		if(strlen($message['plain_body'])>512000){
			$message['plain_body']=String::cut_string($message['plain_body'], 521000, false);
		}

		$cached_message['uid']=$uid;
		$cached_message['folder_id']=$this->folder['id'];
		$cached_message['serialized_message_object']=serialize($message);
		$this->update_cached_message($cached_message);

		//go_debug($message);

		return $message;
	}

	function get_message_headers($start, $limit, $sort_field , $reverse=false, $query='')
	{
		//$uids = $this->get_message_uids($start, $limit, $sort_field , $sort_order, $query);

		$uids = $this->sort_mailbox($sort_field, $reverse);
		$uids=array_slice($uids,$start, $limit);

		//go_debug($uids);
		$sorted_messages=array();
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
					//go_debug($message);
					
					//trim values for mysql insertion
					$message = $this->imap_message_to_cache($message);
					$this->add_cached_message($message);
				}
			}

			
			foreach($uids as $uid){
				if(isset($messages[$uid]))//message might have been moved by filter
				{
					$sorted_messages[]=$messages[$uid];
				}
			}

			//go_debug('Got '.count($uncached_uids).' from IMAP server');

			if(count($this->filtered))
			{
				//go_debug('Filtered messages:'.count($this->filtered));

				$newstart = count($messages);
				$newlimit = $newstart+count($this->filtered);

				$sorted_messages = array_merge($sorted_messages, $this->get_message_headers($newstart, $newlimit, $sort_field , $sort_order, $query));
				/*foreach($extra_messages as $uid=>$message)
				{
					$messages[$uid]=$message;
				}*/
				$this->filtered=array();
			}
		}
		return $sorted_messages;
	}


	function imap_message_to_cache($message){
		$message['to']=substr($message['to'],0, 255);
		$message['subject']=substr($message['subject'],0,100);
		$message['from']=substr($message['from'],0,100);
		if(isset($message['reply-to']))
			$message['reply_to']=substr($message['reply-to'],0,100);
		$message['udate']=intval($message['internal_udate']);
		if(isset($message['disposition-notification-to']))
			$message['notification']=$message['disposition-notification-to'];

		$message['new']=empty($message['seen']) || !empty($message['recent']);
		$message['content_type']=empty($message['content-type']);
		$message['priority']=isset($message['x-priority']) ? intval($message['x-priority']) : 3;

		unset(
					$message['seen'],
					$message['recent'],
					$message['disposition-notification-to'],
					$message['reply-to'],
					$message['date'],
					$message['internal_date'],
					$message['internal_udate'],
					$message['content-type'],
					$message['x-priority'],
					$message['charset'],
					$message['cc']
					);


		$messages[$message['uid']]=$message;
		$messages[$message['uid']]['cached']=false;

		$message['folder_id']=$this->folder['id'];
		$message['account_id']=$this->account['id'];

		return $message;
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
		if(strtoupper($this->selected_mailbox['name'])!='INBOX')
		{
			return $new_messages;
		}

		foreach($new_messages as $message)
		{
			if(empty($message['seen']))
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
