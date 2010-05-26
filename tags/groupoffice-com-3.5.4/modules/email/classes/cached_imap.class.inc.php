<?php

require_once($GLOBALS['GO_CONFIG']->class_path.'mail/imap.class.inc');
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
	 * If we don't no the encoding of a filename header. Use the last charset found
	 * in a part. mb_detect_encoding doesn't work reliable.
	 */
	var $default_charset='';

	/*
	 * You can disable the cache for debugging.
	 * If enabled the message will be converted to safe HTML only once.
	 */
	var $disable_message_cache=false;


	public function __construct()
	{
		$this->email = new email();
		//parent::__construct();
	}

	public function is_imap(){
		return true;
	}

	function open_account($account_id, $mailbox='INBOX', $halt_on_error=true) {
		global $GO_SECURITY, $lang;

		if (!$account = $this->email->get_account($account_id)) {
			throw new Exception($lang['common']['selectError']);
		}

		if($account['user_id']!=$GO_SECURITY->user_id && !$GO_SECURITY->has_admin_permission($GO_SECURITY->user_id)) {
			throw new AccessDeniedException();
		}
		try {
			if (!$this->open($account, $mailbox)) {
				if(!$halt_on_error)
					return false;
				
				throw new Exception(printf($lang['email']['feedbackCannotConnect'], $account['host'],  $imap->last_error(), $account['port']));

			}
		}
		catch (Exception $e) {
			throw new Exception($this->email->human_connect_error($e->getMessage()));
		}
		return $account;

	}

	public function set_account($account, $mailbox='INBOX'){
		$this->account = $this->email->decrypt_account($account);

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
	public function open($account=false, $mailbox=false) {
		if(!$this->handle){
			if(!$account)
				$account = $this->account;

			if(!$mailbox)
				$mailbox = $this->folder['name'];

			$this->set_account($account, $mailbox);

			parent::connect(
							$this->account['host'],
							$this->account['port'],
							$this->account['username'],
							$this->account['password'],
							$this->account['use_ssl']);

			$this->select_mailbox($mailbox);
		}
		return $this->handle;
	}

	public function set_message_flag($uid_array, $flags, $clear=false) {
		if(!$this->handle){
			$this->set_account($this->account, $this->selected_mailbox['name']);
			$this->open($this->account, $this->selected_mailbox['name']);
		}
		return parent::set_message_flag($uid_array, $flags, $clear);
	}

	public function select_mailbox($mailbox = "INBOX") {
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
	 *
	 * @param <type> $sort_type
	 * @param <type> $reverse
	 * @param <type> $filter 
	 */
	
	public function sort_mailbox($sort_type='ARRIVAL', $reverse=false, $filter='ALL') {

		go_debug("cached_imap::sort_mailbox($sort_type, $reverse, $filter)");

		if(!$this->selected_mailbox){
			throw new Exception('No mailbox selected');
		}

		$this->sort_type=$sort_type;
		$this->sort_reverse=$reverse;

		//we need the unseen value to determine if the cache is valid
		//we also need this value later to pass it to the client.
		if(!isset($this->selected_mailbox['unseen'])){
			$this->get_unseen();
		}

		if ($filter != 'ALL' && !empty($filter)) {
			return parent::sort_mailbox($sort_type, $reverse, $filter);
		} else {
			
			if($this->folder['msgcount']!=$this->selected_mailbox['messages'] || $this->folder['unseen']!=$this->selected_mailbox['unseen'])
			{
				go_debug('Cleared sort cache');
				$this->folder_sort_cache=array();
			}
			
			if(isset($this->folder_sort_cache[$sort_type.'_'.$reverse]))
			{
				go_debug('Used cached sort info');
				return $this->folder_sort_cache[$sort_type.'_'.$reverse];
			}else
			{
				go_debug('Got sort from IMAP server: '.$this->folder['msgcount'].' = '.$this->selected_mailbox['messages'].' && '.$this->folder['unseen'].' = '.$this->selected_mailbox['unseen']);
				$sort = parent::sort_mailbox($sort_type, $reverse, $filter);
				$this->folder_sort_cache[$sort_type.'_'.$reverse]=$sort;

				$up_folder['id'] = $this->folder['id'];
				$up_folder['sort']=serialize($this->folder_sort_cache);
				$up_folder['unseen']=$this->selected_mailbox['unseen'];
				$up_folder['msgcount']=$this->selected_mailbox['messages'];

				$this->email->update_folder($up_folder);

				return $sort;
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

	public function delete($messages) {
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
	public function move($uids, $mailbox) {
		if(count($uids))
		{
			if(parent::move($uids, $mailbox))
			{
				$this->delete_cached_messages($uids);
				return true;
			}
		}
		return false;
	}

	public function delete_cached_messages($uids)
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
				
			$this->email->update_folder($up_folder);
		}
	}

	public function set_unseen_cache($uids, $new)
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

	public function set_flagged_cache($uids, $flagged)
	{
		$new_val = $flagged ? '1' : '0';

		$sql = "UPDATE em_messages_cache SET flagged='".$new_val."' WHERE folder_id=".$this->email->escape($this->folder['id'])." AND uid IN(".$this->email->escape(implode(',',$uids)).")";
		$this->email->query($sql);
	}


	public function get_message_part($uid, $message_part=0, $peek=false) {
		
		go_debug("imap::get_message_part($uid, $message_part, $peek)");

		if(!$this->handle){
			if(!$this->open($this->account, $this->folder['name'])){
				throw new Exception(sprintf($lang['email']['feedbackCannotConnect'], $this->account['host'],  $this->last_error(), $this->account['port']));
			}
		}

		return parent::get_message_part($uid, $message_part, $peek);
	}

	public function get_message_header($uid){
		$this->get_cached_messages($this->folder['id'], array($uid));
		$record = $this->email->next_record();

		if($record)
			return $record;
		else
			return parent::get_message_header($uid);
	}



	public function get_message_with_body($uid, $create_temporary_attachment_files=false, $create_temporary_inline_attachment_files=false, $peek=false, $plain_body_requested=true, $html_body_requested=true) {
		global $GO_CONFIG, $GO_MODULES, $GO_SECURITY, $GO_LANGUAGE, $lang;

		go_debug("cached_imap::get_message_with_body($uid, $create_temporary_attachment_files, $create_temporary_inline_attachment_files, $peek, $plain_body_requested, $html_body_requested)");

		require_once($GO_LANGUAGE->get_language_file('email'));

		if($create_temporary_attachment_files || $create_temporary_inline_attachment_files){
			require_once($GO_CONFIG->class_path.'filesystem.class.inc');
			$fs = new filesystem();
			
			$fs->mkdir_recursive($GO_CONFIG->tmpdir.'attachments');
		}


		/*
		 * Check cache
		 */
		$this->get_cached_messages($this->folder['id'], array($uid), true);
		$values=$this->email->next_record();
		if(!$this->disable_message_cache && !empty($values['serialized_message_object'])){

			go_debug('got cached message');		

			$message =  unserialize($values['serialized_message_object']);

			if($this->get_body_parts($plain_body_requested,$html_body_requested, $struct, $message, $peek))
			{
				//additional body part added
				$cached_message['uid']=$uid;
				$cached_message['folder_id']=$this->folder['id'];
				$cached_message['serialized_message_object']=serialize($message);
				
				$this->update_cached_message($cached_message);
			}


			$message['from_cache']=true;
			$message['new']=$values['new'];

			if($create_temporary_attachment_files) {
				for ($i = 0; $i < count($message['attachments']); $i ++) {
					$tmp_file = File::checkfilename($GO_CONFIG->tmpdir.'attachments/'.$message['attachments'][$i]['name']);
					$data = $this->get_message_part_decoded(
									$uid,
									$message['attachments'][$i]['imap_id'],
									$message['attachments'][$i]['encoding'],
									$message['attachments'][$i]['charset'],
									$peek);

					if($data && file_put_contents($tmp_file, $data)) {
						$message['attachments'][$i]['tmp_file']=$tmp_file;
					}
				}
			}
			if($create_temporary_inline_attachment_files) {
				for ($i = 0; $i < count($message['url_replacements']); $i ++) {
					$tmp_file = File::checkfilename($GO_CONFIG->tmpdir.'attachments/'.$message['url_replacements'][$i]['attachment']['name']);
					$data = $this->get_message_part_decoded(
									$uid,
									$message['url_replacements'][$i]['attachment']['imap_id'],
									$message['url_replacements'][$i]['attachment']['encoding'],
									$message['url_replacements'][$i]['attachment']['charset'],
									$peek);

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
		
		$headers = parent::get_message_header($uid, true);
		$message=$this->imap_message_to_cache($headers, true);
		
		if(!$message){
			throw new Exception($lang['email']['errorGettingMessage']);
		}

		require_once($GO_CONFIG->class_path.'mail/RFC822.class.inc');
		$RFC822 = new RFC822();
		$address = $RFC822->parse_address_list($message['from']);

		$message['full_from']=$message['from'];

		$message['sender']=isset($address[0]['email']) ? $address[0]['email'] : '';
		$message['from']=isset($address[0]['personal']) ? $address[0]['personal'] : '';

		$message['reply-to']=empty($message['reply-to']) ? $message['full_from'] : $RFC822->reformat_address_list($message['reply-to']);

		if(isset($message['disposition-notification-to'])){
			$message['notification']=$RFC822->reformat_address_list($message['disposition-notification-to']);
			unset($message['dispostion-notifcation-to']);
		}
		
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

		$this->get_body_parts($plain_body_requested, $html_body_requested, $struct, $message, $peek);
		

		//URL replacements for inline images
		$message['url_replacements']=array();

		$att=$this->find_message_attachments($struct, $message['body_ids']);
		for($i=0,$max=count($att);$i<$max;$i++){

			//not needed
			unset($att[$i]['filename'], $att[$i]['description']);

			if(empty($att[$i]['name'])){
				if(!empty($att[$i]['subject'])){
					$att[$i]['name']=File::strip_invalid_chars($this->mime_header_decode($att[$i]['subject'])).'.eml';
				}elseif($att[$i]['type']=='message')
				{
					$att[$i]['name']='message.eml';
				}elseif($att[$i]['subtype']=='calendar')
				{
					$att[$i]['name']=$lang['email']['event'].'.ics';
				}
			}else
			{
				$att[$i]['name']=$this->mime_header_decode($att[$i]['name']);
			}
			$att[$i]['extension']=File::get_extension($att[$i]['name']);
			$att[$i]['human_size']=Number::format_size($att[$i]['size']);

			//When a mail is saved as a task/appointment/etc. the attachments will be saved temporarily
			$att[$i]['tmp_file']=false;

			if(($create_temporary_attachment_files && empty($att[$i]['id'])) || ($create_temporary_inline_attachment_files && !empty($att[$i]['id']))) {
				$tmp_file = File::checkfilename($GO_CONFIG->tmpdir.'attachments/'.$att[$i]['name']);
				$data = $this->get_message_part_decoded(
								$uid, 
								$att[$i]['imap_id'],
								$att[$i]['encoding'],
								$att[$i]['charset'],
								$peek);

				if($data && file_put_contents($tmp_file, $data)) {
					$att[$i]['tmp_file']=$tmp_file;
				}
			}


			if (!empty($att[$i]["id"])) {
				//when an image has an id it belongs somewhere in the text we gathered above so replace the
				//source id with the correct link to display the image.

				$tmp_id = $att[$i]["id"];
				if (strpos($tmp_id,'>')) {
					$tmp_id = substr($att[$i]["id"], 1,-1);
				}
				$id = "cid:".$tmp_id;

				$url_replacement['id'] = $att[$i]["id"];
				$url_replacement['url'] = $this->get_attachment_url($uid, $att[$i]);
				$url_replacement['tmp_file'] = $att[$i]['tmp_file'];
				
				//we need the attachment object later when we're creating temporary
				//attachment files from cache
				$url_replacement['attachment']=$att[$i];

				$message['url_replacements'][]=$url_replacement;

				if(isset($message['html_body'])){
					if(strpos($message['html_body'], $id)) {
						$message['html_body'] = str_replace($id, $url_replacement['url'], $message['html_body']);
					}else {
						//id was not found in body so add it as attachment later
						unset($att[$i]['id']);
					}
				}
			}
		}

		$message['attachments']=array();
		while($attachment = array_shift($att)){
			if(empty($attachment['id'])){
				$message['attachments'][]=$attachment;
			}
		}

		$cached_message['uid']=$uid;
		$cached_message['folder_id']=$this->folder['id'];
		$cached_message['serialized_message_object']=serialize($message);		
		$this->update_cached_message($cached_message);

		//go_debug($message);

		return $message;
	}

	private function get_attachment_url($uid, $part){
		global $GO_MODULES;
		
		return  $GO_MODULES->modules['email']['url']."attachment.php?".
			"account_id=".$this->account['id'].
			"&amp;mailbox=".urlencode($this->selected_mailbox['name']).
			"&amp;uid=".$uid.
			"&amp;imap_id=".$part["imap_id"].
			"&amp;encoding=".$part["encoding"].
			"&amp;type=".$part["type"].
			"&amp;subtype=".$part["subtype"].
			"&amp;filename=".urlencode($part["name"]);
	}

	private function get_body_parts($plain_body_requested, $html_body_requested, &$struct, &$message, $peek){
		go_debug("get_body_parts($plain_body_requested,$html_body_requested, struct, message)");


	
		if((isset($message['html_body']) || !$html_body_requested) && (isset($message['plain_body']) || !$plain_body_requested)){
			go_debug('No parts needed');
			return false;
		}

		if(!$this->handle){
			if(!$this->open($this->account, $this->folder['name'])){
				throw new Exception(sprintf($lang['email']['feedbackCannotConnect'], $this->account['host'],  $this->last_error(), $this->account['port']));
			}
		}

		//use this array later to find attachments. The body parts will be skipped.
		$message['body_ids']=array();

		$struct = $this->get_message_structure($message['uid']);

		//go_debug($struct);

		if(count($struct)==1) {
			$header_ct = explode('/', $message['content-type']);

			if(count($header_ct)==2){
				//if there's only one part the IMAP server always seems to return the type as text/plain even though the headers say text/html
				//so use the header's content type.

				go_debug('Overriden part type parameters with header parameters');
				go_debug($message['content-type']);
				go_debug($message['content-transfer-encoding']);
				go_debug($message['charset']);


				if($struct[1]['subtype']=='plain'){
					$struct[1]['type']=$header_ct[0];
					$struct[1]['subtype']=$header_ct[1];
				}

				if(!empty($message['content-transfer-encoding']) &&
					(empty($struct[1]['encoding']) || $struct[1]['encoding']=='7bit' || $struct[1]['encoding']=='8bit')){
					$struct[1]['encoding']=$message['content-transfer-encoding'];
				}

				if(!empty($message['charset']) && $struct[1]['charset']=='us-ascii'){
					$struct[1]['charset']=$message['charset'];
				}
			}
		}

		//get a default charset to decode filenames of attachments that don't have
		//that value
		if(!empty($struct[1]['charset']))
			$this->default_charset = $struct[1]['charset'];

		//it seems better to use windows-1252 because converting from that also
		//works for iso-8859-* strings
		if(stripos($this->default_charset, 'iso-8859')!==false)
			$this->default_charset = 'windows-1252';

		//go_debug($struct);

		$plain_parts = $this->find_body_parts($struct,'text', 'plain');
		foreach($plain_parts['parts'] as $plain_part){
			$message['body_ids'][]=$plain_part['imap_id'];
		}

		$html_parts = $this->find_body_parts($struct,'text', 'html');
		foreach($html_parts['parts'] as $html_part){
			$message['body_ids'][]=$html_part['imap_id'];
		}

		$inline_images=array();

		if(!isset($message['plain_body']) && $plain_parts['text_found'] && ($plain_body_requested || ($html_body_requested && !$html_parts['text_found']))){
			$message['plain_body']='';
			foreach($plain_parts['parts'] as $plain_part){
				if($plain_part['type']=='text'){

					if(!empty($message['plain_body']))
						$message['plain_body'].= "\n";

					$message['plain_body'].=$this->get_message_part_decoded($message['uid'],$plain_part['imap_id'],$plain_part['encoding'], $plain_part['charset'],$peek, 512000);
				}else
				{
					$message['plain_body'].='{inline_'.count($inline_images).'}';
					$inline_images[]='<img alt="'.$plain_part['name'].'" src="'.$this->get_attachment_url($message['uid'], $plain_part).'" style="display:block;margin:10px 0;" />';
				}
			}
			
			$uuencoded_attachments = $this->extract_uuencoded_attachments($message['plain_body']);
			for($i=0;$i<count($uuencoded_attachments);$i++) {
				$attachment = $uuencoded_attachments[$i];
				$attachment['number']=$part['number'];
				unset($attachment['data']);
				$attachment['uuencoded_partnumber']=$i+1;

				$attachments[]=$attachment;
			}
		}
		

		if(!isset($message['html_body']) && $html_parts['text_found'] && ($html_body_requested || ($plain_body_requested && !$plain_parts['text_found']))){
			$message['html_body']='';
			foreach($html_parts['parts'] as $html_part){
				if($html_part['type']=='text'){

					if(!empty($message['html_body']))
						$message['html_body'].= '<br />';

					$message['html_body'].=$this->get_message_part_decoded($message['uid'],$html_part['imap_id'],$html_part['encoding'], $html_part['charset'],$peek,512000);
				}else
				{
					$message['html_body'].='<img alt="'.$html_part['name'].'" src="'.$this->get_attachment_url($message['uid'], $html_part).'" style="display:block;margin:10px 0;" />';
				}
			}
			$message['html_body']=String::convert_html($message['html_body']);
		}
		if($html_body_requested){

			if(empty($message['html_body'])){				
				$message['html_body']=isset($message['plain_body']) ? String::text_to_html($message['plain_body']) : '';
				for($i=0,$max=count($inline_images);$i<$max;$i++){
					$message['html_body']=str_replace('{inline_'.$i.'}', $inline_images[$i], $message['html_body']);
				}
			}
		}
		
		if(!isset($message['plain_body'])){
			if(empty($message['plain_body']) && $plain_body_requested){
				$message['plain_body']=String::html_to_text($message['html_body']);
			}
		}else
		{
			for($i=0,$max=count($inline_images);$i<$max;$i++){
				$message['plain_body']=str_replace('{inline_'.$i.'}', "\n", $message['plain_body']);
			}
		}

		return true;
	}

	
	public function get_message_headers_set($start, $limit, $sort_field , $reverse=false, $query='ALL')
	{
		$uids = $this->sort_mailbox($sort_field, $reverse, $query);

		if($limit>0)
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
					$messages[$message['uid']]=$message;
				}
			}

			
			foreach($uids as $uid){
				if(isset($messages[$uid]))//message might have been moved by filter
				{
					$sorted_messages[]=$messages[$uid];
				}
			}

			//go_debug('Got '.count($uncached_uids).' from IMAP server');
			$count = count($this->filtered);
			if($count)
			{
				$this->selected_mailbox['messages']-=$count;

				if(isset($this->selected_mailbox['unseen']))
					$this->selected_mailbox['unseen']-=$count;
				
				//go_debug('Filtered messages:'.count($this->filtered));

				$newstart = count($messages);
				$newlimit = $newstart+count($this->filtered);

				$sorted_messages = array_merge($sorted_messages, $this->get_message_headers_set($newstart, $newlimit, $sort_field , $reverse, $query));
				/*foreach($extra_messages as $uid=>$message)
				{
					$messages[$uid]=$message;
				}*/
				$this->filtered=array();
			}
		}
		return $sorted_messages;
	}


	public function imap_message_to_cache($message, $keep_full_data=false){
		/*$message['to']=substr($message['to'],0, 255);
		$message['subject']=substr($message['subject'],0,100);
		$message['from']=substr($message['from'],0,100);*/
		$message['udate']=intval($message['internal_udate']);		
		$message['new']=empty($message['seen']);
		$message['priority']=intval($message['x-priority']);
		
		preg_match("'([^/]*)/([^ ;\n\t]*)'i", $message['content-type'], $ct);

		if (isset($ct[2]) && $ct[1] != 'text' && $ct[2] != 'alternative' && $ct[2] != 'related')
		{
			$message["attachments"] = 1;
		}

		if(!$keep_full_data){
			unset(
						$message['seen'],
						$message['recent'],
						$message['disposition-notification-to'],
						$message['content-transfer-encoding'],
						$message['reply-to'],
						$message['date'],
						$message['internal_date'],
						$message['internal_udate'],
						$message['content-type'],
						$message['x-priority'],
						$message['charset'],
						$message['cc'],
						$message['bcc']
						);
		}

		$message['folder_id']=$this->folder['id'];
		$message['account_id']=$this->account['id'];

		return $message;
	}

	public function set_filters($filters)
	{
		$this->filters=$filters;
	}

	public function get_filtered_message_headers($uids)
	{		
		$this->filtered=array();
		for ($i=0;$i<sizeof($this->filters);$i++)
		{
			$this->filters[$i]['uids']=array();
		}

		$new_messages = $this->get_message_headers($uids);
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
					$ret = $this->set_message_flag($this->filters[$i]['uids'], "\Seen");
				}
				if(parent::move($this->filters[$i]['uids'],$this->filters[$i]["folder"], false))
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

	public function add_cached_message($cached_message)
	{
		return $this->email->insert_row('em_messages_cache', $cached_message);
	}


	public function clear_cache($folder_id=0){
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

	public function update_cached_message($cached_message)
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

	public function get_cached_messages($folder_id, $uids, $with_full_cached_message=false)
	{
		$sql = "SELECT `folder_id`,`uid`,`account_id`,`new`,`subject`,`from`,".
			"`size`,`udate`,`attachments`,`flagged`,`answered`,`forwarded`,`priority`,".
			"`to`";
		if($with_full_cached_message){
			$sql .= ",`serialized_message_object` ";
		}
		$sql .= "FROM em_messages_cache WHERE folder_id=".$this->email->escape($folder_id)." AND uid IN (".$this->email->escape(implode(',',$uids)).")";
		$this->email->query($sql);
	}

}
