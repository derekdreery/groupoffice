<?php
require_once($GLOBALS['GO_CONFIG']->class_path.'mail/imap_base.class.inc.php');
class imap extends imap_bodystruct {

	var $handle=false;

	var $ssl=false;
	var $server='';
	var $port=143;
	var $username='';
	var $password='';

	var $starttls=false;

	var $auth='PLAIN';

	var $selected_mailbox=false;

	var $touched_folders =array();

	var $delimiter=false;

	public function __construct(){
		if(isset($_SESSION['imap_delimiter'])){
			$this->delimiter=$_SESSION['imap_delimiter'];
		}
	}

	public function __destruct() {
		$this->disconnect();
	}

	public function last_error(){
		return 'TODO record errors';
	}


	/**
	 * Connects to the IMAP server and authenticates the user
	 *
	 * @param <type> $server
	 * @param <type> $port
	 * @param <type> $username
	 * @param <type> $password
	 * @param <type> $ssl
	 * @param <type> $starttls
	 * @return <type>
	 */

	public function connect($server, $port, $username, $password, $ssl=false, $starttls=false) {

		$this->ssl = $ssl;
		$this->starttls = $starttls;
		$this->server=$server;
		$this->port=$port;
		$this->username=$username;
		$this->password=$password;

		if ($this->ssl) {
			$this->server = 'tls://'.$this->server;
		}

		$this->handle = fsockopen($this->server, $this->port, $errorno, $errorstr, 30);
		if (!is_resource($this->handle)) {
			throw new Exception('#'.$errorno.'. '.$errorstr);
		}

		return $this->authenticate($username, $password);
	}



	/**
	 * Disconnect from the IMAP server
	 *
	 * @return <type>
	 */

	public function disconnect() {
		if (is_resource($this->handle)) {
			$command = "LOGOUT\r\n";
			$this->send_command($command);
			$this->state = 'disconnected';
			$result = $this->get_response();


			//go_debug($this->commands);

			//go_debug($this->responses);

			return fclose($this->handle);
		}else {
			return false;
		}
	}


	private function authenticate($username, $pass) {

		if ($this->starttls) {

			$command = "STARTTLS\r\n";
			$this->send_command($command);
			$response = $this->get_response();
			if (!empty($response)) {
				$end = array_pop($response);
				if (substr($end, 0, strlen('A'.$this->command_count.' OK')) == 'A'.$this->command_count.' OK') {
					stream_socket_enable_crypto($this->handle, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
				}
			}
		}
		switch (strtolower($this->auth)) {
			case 'cram-md5':
				$this->banner = fgets($this->handle, 1024);
				$cram1 = 'A'.$this->command_number().' AUTHENTICATE CRAM-MD5'."\r\n";
				fputs ($this->handle, $cram1);
				$this->commands[trim($cram1)] = microtime();
				$response = fgets($this->handle, 1024);
				$this->responses[] = $response;
				$challenge = base64_decode(substr(trim($response), 1));
				$pass .= str_repeat(chr(0x00), (64-strlen($pass)));
				$ipad = str_repeat(chr(0x36), 64);
				$opad = str_repeat(chr(0x5c), 64);
				$digest = bin2hex(pack("H*", md5(($pass ^ $opad).pack("H*", md5(($pass ^ $ipad).$challenge)))));
				$challenge_response = base64_encode($username.' '.$digest);
				$this->commands[trim($challenge_response)] = microtime();
				fputs($this->handle, $challenge_response."\r\n");
				break;
			default:
				$login = 'A'.$this->command_number().' LOGIN "'.str_replace('"', '\"', $username).'" "'.str_replace('"', '\"', $pass). "\"\r\n";
				$this->commands[trim(str_replace($pass, 'xxxx', $login))] = microtime();
				fputs($this->handle, $login);
				break;
		}
		$res = $this->get_response();
		$authed = false;
		if (is_array($res) && !empty($res)) {
			$response = array_pop($res);
			$this->short_responses[$response] = microtime();
			if (!$this->auth) {
				if (isset($res[1])) {
					$this->banner = $res[1];
				}
				if (isset($res[0])) {
					$this->banner = $res[0];
				}
			}
			if (stristr($response, 'A'.$this->command_count.' OK')) {
				$authed = true;
				$this->state = 'authed';
			}
		}
		return $authed;
	}

	private function get_capability() {
		if (isset($_SESSION['GO_IMAP'][$this->server]['imap_capability'])) {
			$this->capability=$_SESSION['GO_IMAP'][$this->server]['imap_capability'];
		}else {
			$command = "CAPABILITY\r\n";
			$this->send_command($command);
			$response = $this->get_response();
			$this->capability = $_SESSION['GO_IMAP'][$this->server]['imap_capability'] = implode(' ', $response);

			go_debug($this->capability);
		}

		return $this->capability;
	}

	/**
		* Get the delimiter that is used to delimit Mailbox names
		*
		* @access public
		* @return mixed The delimiter or false on failure
		*/

	function get_mailbox_delimiter() {
		if(!$this->delimiter){
			$this->get_folders();
		}
		return $this->delimiter;
	}


	/**
	 * Get's the mailboxes
	 *
	 * @param <type> $namespace
	 * @param <type> $subscribed
	 * @return <type>
	 */

	public function get_folders($namespace='', $subscribed=false) {

		$this->get_capability();

		if ($subscribed) {
			$imap_command = 'LSUB';
		}
		else {
			$imap_command = 'LIST';
		}
		$excluded = array();
		$parents = array();
		$delim = false;

		$command = $imap_command.' "'.$namespace."\" \"*\"\r\n";
		$this->send_command($command);
		$result = $this->get_response($this->folder_max, true);
		$folders = array();
		foreach ($result as $vals) {
			if (!isset($vals[0])) {
				continue;
			}
			if ($vals[0] == 'A'.$this->command_count) {
				continue;
			}
			$flags = false;
			$count = count($vals);
			$folder = $this->utf7_decode($vals[($count - 1)]);
			$flag = false;
			$delim_flag = false;
			$parent = '';
			$base_name = '';
			$folder_parts = array();
			$no_select = false;
			$can_have_kids = false;
			$has_kids = false;
			$marked = false;
			$special = false;
			$hidden = false;
			$folder_sort_by = 'ARRIVAL';
			$check_for_new = false;
			foreach ($vals as $v) {
				if ($v == '(') {
					$flag = true;
				}
				elseif ($v == ')') {
					$flag = false;
					$delim_flag = true;
				}
				else {
					if ($flag) {
						$flags .= ' '.$v;
					}
					if ($delim_flag && !$delim) {
						$delim = $v;
						$delim_flag = false;
					}
				}
			}

			if (!$this->delimiter) {
				$this->delimiter = $delim;
				$_SESSION['imap_delimiter'] = $this->delimiter;
			}
			if ($delim && strstr($folder, $delim)) {
				$temp_parts = explode($delim, $folder);
				$folder_parts = array();
				foreach ($temp_parts as $g) {
					if (trim($g)) {
						$folder_parts[] = $g;
					}
				}
			}
			if (isset($folder_parts[(count($folder_parts) - 1)])) {
				$base_name = $folder_parts[(count($folder_parts) - 1)];
			}
			else {
				$base_name = $folder;
			}

			if (stristr($flags, 'marked')) {
				$marked = true;
			}
			if (!stristr($flags, 'noinferiors')) {
				$can_have_kids = true;
			}
			if (($folder == $namespace && $namespace) || stristr($flags, 'haschildren')) {
				$has_kids = true;
			}
			if ($folder != 'INBOX' && $folder != $namespace && stristr($flags, 'noselect')) {
				$no_select = true;
			}

			if (!isset($folders[$folder]) && $folder) {
				$folders[$folder] = array(
								'delim' => $delim,
								'name' => $folder,
								'name_parts' => $folder_parts,
								'basename' => $base_name,
								'realname' => $folder,
								'namespace' => $namespace,
								'marked' => $marked,
								'noselect' => $no_select,
								'can_have_kids' => $can_have_kids,
								'has_kids' => $has_kids,
								'special' => $special
				);
			}
		}

		return $folders;
	}


	/**
	 * Before getting message a mailbox must be selected
	 *
	 * @param <type> $mailbox_name
	 * @return <type>
	 *
	 */

	public function select_mailbox($mailbox_name) {

		if($this->selected_mailbox && $this->selected_mailbox['name']==$mailbox_name)
			return true;

		if(!in_array($mailbox_name, $this->touched_folders))
			$this->touched_folders[]=$mailbox_name;


		$box = $this->utf7_encode(str_replace('"', '\"', $mailbox_name));
		$this->clean($box, 'mailbox');

		$command = "SELECT \"$box\"\r\n";

		$this->send_command($command);
		$res = $this->get_response(false, true);
		$status = $this->check_response($res, true);
		$uidvalidity = 0;
		$exists = 0;
		$uidnext = 0;
		$flags = array();
		$pflags = array();
		foreach ($res as $vals) {
			if (in_array('UIDNEXT', $vals)) {
				foreach ($vals as $i => $v) {
					if (intval($v) && isset($vals[($i - 1)]) && $vals[($i - 1)] == 'UIDNEXT') {
						$uidnext = $v;
					}
				}
			}
			if (in_array('UIDVALIDITY', $vals)) {
				foreach ($vals as $i => $v) {
					if (intval($v) && isset($vals[($i - 1)]) && $vals[($i - 1)] == 'UIDVALIDITY') {
						$uidvalidity = $v;
					}
				}
			}
			if (in_array('PERMANENTFLAGS', $vals)) {
				$collect_flags = false;
				foreach ($vals as $i => $v) {
					if ($v == ')') {
						$collect_flags = false;
					}
					if ($collect_flags) {
						$pflags[] = $v;
					}
					if ($v == '(') {
						$collect_flags = true;
					}
				}
			}
			if (in_array('FLAGS', $vals)) {
				$collect_flags = false;
				foreach ($vals as $i => $v) {
					if ($v == ')') {
						$collect_flags = false;
					}
					if ($collect_flags) {
						$flags[] = $v;
					}
					if ($v == '(') {
						$collect_flags = true;
					}
				}
			}
			if (in_array('EXISTS', $vals)) {
				foreach ($vals as $i => $v) {
					if (intval($v) && isset($vals[($i + 1)]) && $vals[($i + 1)] == 'EXISTS') {
						$exists = $v;
					}
				}
			}
		}

		$mailbox=false;
		if ($status) {
			$mailbox['name']=$mailbox_name;
			$mailbox['uidnext'] = $uidnext;
			$mailbox['uidvalidity'] = $uidvalidity;
			$mailbox['messages'] = $exists;
			$mailbox['flags'] = $flags;
			$mailbox['permanentflags'] = $pflags;

			$this->selected_mailbox=$mailbox;
		}
		return $mailbox;
	}

	/**
	 * Get's the number and UID's of unseen messages of a mailbox
	 *
	 * @param <type> $folder
	 * @return <type>
	 */

	public function get_unseen($mailbox=false) {

		if($mailbox)
			$this->select_mailbox($mailbox);

		$command = "UID SEARCH (UNSEEN) ALL\r\n";
		$this->send_command($command);
		$res = $this->get_response(false, true);
		$status = $this->check_response($res, true);
		$unseen = 0;
		$uids = array();
		if ($status) {
			array_pop($res);
			foreach ($res as $vals) {
				foreach ($vals as $v) {

					if (is_numeric($v)) {
						$unseen++;
						$uids[] = $v;
					}
				}
			}
		}

		//go_debug($unseen);
		return array('count'=>$unseen, 'uids'=>$uids);
	}


	/**
	 * Returns a sorted list of mailbox UID's
	 *
	 * @param <type> $sort
	 * @param <type> $reverse
	 * @param <type> $filter
	 * @return <type>
	 */
	public function sort_mailbox($sort, $reverse=false, $filter='ALL') {

		if(!$this->selected_mailbox)
			throw new Exception('No mailbox selected');

		$this->get_capability();

		if (($sort == 'THREAD_R' || $sort == 'THREAD_O')) {
			if ($sort == 'THREAD_O') {
				if (stristr($this->capability, 'ORDEREDSUBJECT')) {
					return $this->thread_sort($sort, $filter);
				}
				else {
					return $this->server_side_sort('ARRIVAL', false, $filter);
				}
			}
			if ($sort == 'THREAD_R') {
				if (stristr($this->capability, 'THREAD')) {
					return $this->thread_sort($sort, $filter);
				}
				else {
					return $this->server_side_sort('ARRIVAL', false, $filter);
				}
			}
		}
		elseif (stristr($this->capability, 'SORT')) {
			return $this->server_side_sort($sort, $reverse, $filter);
		}
		else {
			return $this->client_side_sort($sort, $reverse, $filter);
		}
	}

	private function server_side_sort($sort, $reverse, $filter) {
		go_debug("server_side_sort($sort, $reverse, $filter");

		$this->clean($sort, 'keyword');
		$this->clean($filter, 'keyword');
		$command = 'UID SORT ('.$sort.') US-ASCII '.$filter."\r\n";
		$this->send_command($command);
		/*if ($this->disable_sort_speedup) {
			$speedup = false;
		}
		else {*/
		$speedup = true;
		//}
		$res = $this->get_response(false, true, 8192, $speedup);
		$status = $this->check_response($res, true);
		$uids = array();
		foreach ($res as $vals) {
			if ($vals[0] == '*' && strtoupper($vals[1]) == 'SORT') {
				array_shift($vals);
				array_shift($vals);
				$uids = array_merge($uids, $vals);
			}
			else {
				if (preg_match("/^(\d)+$/", $vals[0])) {
					$uids = array_merge($uids, $vals);
				}
			}
		}
		unset($res);
		if ($reverse) {
			$uids = array_reverse($uids);
		}
		return $status ? $uids : false;
	}
	/* use the FETCH command to manually sort the mailbox */
	private function client_side_sort($sort, $reverse) {
		//$this->clean($mailbox, 'mailbox');
		$this->clean($sort, 'keyword');
		$command1 = 'UID FETCH 1:* ';
		switch ($sort) {
			case 'DATE':
			case 'R_DATE':
				$command2 = "BODY.PEEK[HEADER.FIELDS (DATE)]\r\n";
				$key = "BODY[HEADER.FIELDS";
				break;
			case 'SIZE':
			case 'R_SIZE':
				$command2 = "RFC822.SIZE\r\n";
				$key = "RFC822.SIZE";
				break;
			case 'ARRIVAL':
				$command2 = "INTERNALDATE\r\n";
				$key = "INTERNALDATE";
				break;
			case 'R_ARRIVAL':
				$command2 = "INTERNALDATE\r\n";
				$key = "INTERNALDATE";
				break;
			case 'FROM':
			case 'R_FROM':
				$command2 = "BODY.PEEK[HEADER.FIELDS (FROM)]\r\n";
				$key = "BODY[HEADER.FIELDS";
				break;
			case 'SUBJECT':
			case 'R_SUBJECT':
				$command2 = "BODY.PEEK[HEADER.FIELDS (SUBJECT)]\r\n";
				$key = "BODY[HEADER.FIELDS";
				break;
			default:
				$command2 = "INTERNALDATE\r\n";
				$key = "INTERNALDATE";
				break;
		}
		$command = $command1.$command2;
		$this->send_command($command);
		$res = $this->get_response(false, true);
		$status = $this->check_response($res, true);
		$uids = array();
		$sort_keys = array();
		foreach ($res as $vals) {
			if (!isset($vals[0]) || $vals[0] != '*') {
				continue;
			}
			$uid = 0;
			$sort_key = 0;
			$body = false;
			foreach ($vals as $i => $v) {
				if ($body) {
					if ($v == ']' && isset($vals[$i + 1])) {
						if ($command2 == "BODY.PEEK[HEADER.FIELDS (DATE)]\r\n") {
							$sort_key = strtotime(trim(substr($vals[$i + 1], 5)));
						}
						else {
							$sort_key = $vals[$i + 1];
						}
						$body = false;
					}
				}
				if (strtoupper($v) == 'UID') {
					if (isset($vals[($i + 1)])) {
						$uid = $vals[$i + 1];
						$uids[] = $uid;
					}
				}
				if ($key == strtoupper($v)) {
					if (substr($key, 0, 4) == 'BODY') {
						$body = 1;
					}
					elseif (isset($vals[($i + 1)])) {
						if ($key == "INTERNALDATE") {
							$sort_key = strtotime($vals[$i + 1]);
						}
						else {
							$sort_key = $vals[$i + 1];
						}
					}
				}
			}
			if ($sort_key && $uid) {
				$sort_keys[$uid] = $sort_key;
			}
		}
		if (count($sort_keys) != count($uids)) {
			echo 'BUG: Client side sort array mismatch';
			exit;
		}
		unset($res);
		natcasesort($sort_keys);
		$uids = array_keys($sort_keys);
		if ($reverse) {
			$uids = array_reverse($uids);
		}
		return $status ? $uids : false;
	}
	/* use the THREAD extension to get the sorted UID list and thread data */
	private function thread_sort($sort ,$filter) {
		$this->clean($filter, 'keyword');
		if (substr($sort, 7) == 'R') {
			$method = 'REFERENCES';
		}
		else {
			$method = 'ORDEREDSUBJECT';
		}
		$command = 'UID THREAD '.$method.' US-ASCII '.$filter."\r\n";
		$this->send_command($command);
		$res = $this->get_response();
		$status = $this->check_response($res);
		$uid_string = '';
		foreach ($res as $val) {
			if (strtoupper(substr($val, 0, 8)) == '* THREAD') {
				$uid_string .= ' '.substr($val, 8);
			}
		}
		unset($res);
		$uids = array();
		$thread_data = array();
		$uid_string = str_replace(array(' )', ' ) ', ')', ' (', ' ( ', '( '), array(')', ')', ')', '(', '(', '('), $uid_string);
		$branches = array();
		$level = 0;
		$thread = 0;
		$last_id = 0;
		$offset = 0;
		$parents = array();
		while($uid_string) {
			switch ($uid_string{0}) {
				case ' ':
					$level++;
					$offset++;
					$parents[$level] = $last_id;
					$uid_string = substr($uid_string, 1);
					break;
				case '(':
					$level++;
					if ($level == 2) {
						$parents[$level] = $thread;
					}
					$uid_string = substr($uid_string, 1);
					break;
				case ')':
					$uid_string = substr($uid_string, 1);
					if ($offset) {
						$level -= $offset;
						$offset = 0;
					}
					$level--;
					break;
				default:
					if (preg_match("/^(\d+)/", $uid_string, $matches)) {
						if ($level == 1) {
							$thread = $matches[1];
							$parents = array(1 => 0);
						}
						if (!isset($parents[$level])) {
							if (isset($parents[$level - 1])) {
								$parents[$level] = $parents[$level - 1];
							}
							else {
								$parents[$level] = 0;
							}
						}
						$thread_data[$thread][$matches[1]] = array('parent' => $parents[$level], 'level' => $level, 'thread' => $thread);
						$parents[$level] = $thread;
						$last_id = $matches[1];
						$uid_string = substr($uid_string, strlen($matches[1]));
					}
					else {
						echo 'BUG'.$uid_string."\r\n";
						;
						$uid_string = substr($uid_string, 1);
					}
			}
		}
		$thread_data = array_reverse($thread_data);
		$new_thread_data = array();
		$threads = array();
		foreach ($thread_data as $vals) {
			foreach ($vals as $i => $v) {
				$uids[] = $i;
				if ($v['parent'] && isset($new_thread_data[$v['parent']])) {
					if (isset($new_thread_data[$v['thread']]['reply_count'])) {
						$new_thread_data[$v['thread']]['reply_count']++;
					}
					else {
						$new_thread_data[$v['thread']]['reply_count'] = 1;
					}
				}
				else {
					$threads[] = $i;
				}
				$new_thread_data[$i] = $v;
			}
		}
		return array('uids' => $uids, 'total' => count($uids), 'thread_data' => $new_thread_data,
						'sort' => $sort, 'filter' => $filter, 'timestamp' => time(), 'threads' => $threads);

	}



	/**
	 * Get's all message headers from an array of UID's
	 *
	 * @param <type> $uids
	 * @return <type>
	 */
	public function get_message_headers($uids) {

		$sorted_string = implode(',', $uids);
		$this->clean($sorted_string, 'uid_list');
		$command = 'UID FETCH '.$sorted_string.' (FLAGS INTERNALDATE RFC822.SIZE BODY.PEEK[HEADER.FIELDS (SUBJECT FROM '.
						"DATE CONTENT-TYPE X-PRIORITY TO CC BCC REPLY-TO DISPOSITION-NOTIFICATION-TO)])\r\n";
		$this->send_command($command);
		$res = $this->get_response(false, true);

		$status = $this->check_response($res, true);
		$tags = array('UID' => 'uid', 'FLAGS' => 'flags', 'RFC822.SIZE' => 'size', 'INTERNALDATE' => 'internal_date');
		$junk = array('SUBJECT', 'FROM', 'CONTENT-TYPE', 'TO', 'CC','BCC', '(', ')', ']', 'X-PRIORITY', 'DATE','REPLY-TO','DISPOSITION-NOTIFICATION-TO');
		//$flds = array('uid','flags','size','internal_date','answered','seen','','reply-to', 'content-type','x-priority','disposition-notification-to');
		$headers = array();
		foreach ($res as $n => $vals) {
			if (isset($vals[0]) && $vals[0] == '*') {
				$message=array(
					'to'=>'',
					'cc'=>'',
					'bcc'=>'',
					'from'=>'',
					'subject'=>'',
					'uid'=>'',
					'size'=>'',
					'internal_date'=>'',
					'date'=>'',
					'udate'=>'',
					'internal_udate'=>'',
					'x-priority'=>3,
					'reply-to'=>'',
					'disposition-notification-to'=>'',
				);

				$count = count($vals);
				for ($i=0;$i<$count;$i++) {

					//go_debug($vals[$i]);

					if ($vals[$i] == 'BODY[HEADER.FIELDS') {
						$i++;
						while(isset($vals[$i]) && in_array($vals[$i], $junk)) {
							$i++;
						}

						$header = str_replace("\r\n", "\n", $vals[$i]);
						$header = preg_replace("/\n\s/", " ", $header);
						//go_debug($header);
						$lines = explode("\n", $header);
						foreach ($lines as $line) {
							if(!empty($line)) {
								$header = trim(strtolower(substr($line, 0, strpos($line, ':'))));	

								if (!$header) {
									$message[$last_header] .= "\n".trim($line);
								}else {
									$message[$header] = trim(substr($line, (strpos($line, ':') + 1)));
									$last_header = $header;
								}
							}
						}
					}
					elseif (isset($tags[strtoupper($vals[$i])])) {
						if (isset($vals[($i + 1)])) {
							if ($tags[strtoupper($vals[$i])] == 'flags' && $vals[$i + 1] == '(') {
								$n = 2;
								while (isset($vals[$i + $n]) && $vals[$i + $n] != ')') {
									$message[strtolower(substr($vals[$i + $n],1))]=true;
									$n++;
								}
								$i += $n;
							}
							else {
								$message[$tags[strtoupper($vals[$i])]] = trim($vals[($i + 1)]);
								$i++;
							}
						}
					}
				}
				if ($message['uid']) {
					if(isset($message['content_type'])) {
						if (stristr($message['content_type'], 'charset=')) {
							if (preg_match("/charset\=([^\s]+)/", $message['content-type'], $matches)) {
								$message['charset'] = trim(strtolower(str_replace(array('"', "'", ';'), '', $matches[1])));
							}
						}
					}
					$message['udate']=empty($message['date']) ? 0 : strtotime($message['date']);
					$message['internal_udate']=strtotime($message['internal_date']);

					$message['subject']=$this->mime_header_decode($message['subject']);
					$message['from']=$this->mime_header_decode($message['from']);
					$message['to']=$this->mime_header_decode($message['to']);
					if(isset($message['cc']))
						$message['cc']=$this->mime_header_decode($message['cc']);
					$headers[$message['uid']] = $message;
				}
			}
		}
		$final_headers = array();
		foreach ($uids as $v) {
			if (isset($headers[$v])) {
				$final_headers[$v] = $headers[$v];
			}
		}
		return $final_headers;
	}

	//todo
	public function get_quota() {

	}

	/**
	 * Get the structure of a message
	 *
	 * @param <type> $uid
	 * @return <type>
	 */
	public function get_message_structure($uid) {
		$this->clean($uid, 'uid');
		$part_num = 1;
		$struct = array();
		$command = "UID FETCH $uid BODYSTRUCTURE\r\n";
		$this->send_command($command);
		$result = $this->get_response(false, true);

		while (isset($result[0][0]) && isset($result[0][1]) && $result[0][0] == '*' && strtoupper($result[0][1]) == 'OK') {
			array_shift($result);
		}
		$status = $this->check_response($result, true);
		$response = array();
		if (!isset($result[0][4])) {
			$status = false;
		}
		if ($status) {
			if (strtoupper($result[0][4]) == 'UID') {
				$response = array_slice($result[0], 7, -1);
			}
			else {
				$response = array_slice($result[0], 5, -1);
			}
			$response = $this->split_toplevel_result($response);
			if (count($response) > 1) {
				$struct = $this->parse_multi_part($response, 1, 1);
			}
			else {
				$struct[1] = $this->parse_single_part($response);
			}
		}

		return $struct;
	}

	function find_message_part($struct, $part, $type='text', $subtype=false) {
		$res = array();
		if (!is_array($struct) || empty($struct)) {
			return $res;
		}
		foreach ($struct as $id => $vals) {
			if ($part && $id == $part) {
				$vals['imap_id'] = $id;
				$res = $vals;
			}
			elseif (!$part && isset($vals['type']) && $vals['type'] == $type) {
				if ($subtype) {
					if ($subtype == $vals['subtype']) {
						$vals['imap_id'] = $id;
						$res = $vals;
					}
				}
				else {
					$vals['imap_id'] = $id;
					$res = $vals;
				}
			}
			if (empty($res) && isset($vals['subs'])) {
				$res =  $this->find_message_part($vals['subs'], $part, $type, $subtype);
			}
			if (!empty($res)) {
				break;
			}
		}
		return $res;
	}
	/**
	 * Find all attachment parts
	 *
	 * @param <type> $struct
	 * @param <type> $skip_ids Skip thise ID's
	 * @param <type> $attachments
	 * @return <type>
	 */

	function find_message_attachments($struct, $skip_ids=array(), $attachments=array()) {
		if (!is_array($struct) || empty($struct)) {
			return $attachments;
		}

		foreach ($struct as $id => $vals) {
			if(!is_array($vals) || in_array($id, $skip_ids))
				continue;

			if(isset($vals['type'])){
				$vals['imap_id'] = $id;
				$attachments[]=$vals;
			}elseif(isset($vals['subs'])) {
				$attachments = $this->find_message_attachments($vals['subs'],$skip_ids,	$attachments);
			}
		}
		return $attachments;
	}

	function decode_message_part($str, $encoding, $charset) {

		switch($encoding) {
			case 'base64':
				$str = base64_decode($str);
				break;
			case 'quoted-printable':
				$str =  quoted_printable_decode($str);
				break;
		}

		if($charset){
			$str = String::clean_utf8($str, $charset);
			if($charset != 'utf-8') {
				$str = str_replace($charset, 'utf-8', $str);
			}
		}
		return $str;
	}

	public function get_message_part_decoded($uid, $part_no, $encoding, $charset, $peek=false) {
		go_debug("get_message_part_decode($uid, $part_no, $encoding, $charset)");
		return $this->decode_message_part(
						$this->get_message_part($uid, $part_no, $peek),
						$encoding,
						$charset
		);
	}


	/**
	 * Get the full body of a message part. Obtain the partnumbers with get_message_structure.
	 *
	 * @param <type> $uid
	 * @param <type> $message_part omit if you want the full message
	 * @param <type> $raw
	 * @param <type> $max
	 * @return <type>
	 */
	public function get_message_part($uid, $message_part=-1, $peek=false) {
		$this->clean($uid, 'uid');
		if ($message_part==-1) {
			$command = "UID FETCH $uid BODY[]\r\n";
		}
		else {
			$this->clean($message_part, 'msg_part');
			$command = "UID FETCH $uid BODY[$message_part]\r\n";
		}
		$this->send_command($command);
		$result = $this->get_response(false, true);
		$status = $this->check_response($result, true);
		$res = '';
		foreach ($result as $vals) {
			if ($vals[0] != '*') {
				continue;
			}
			$search = true;
			foreach ($vals as $v) {
				if ($v != ']' && !$search) {
					$res = trim(preg_replace("/\s*\)$/", '', $v));
					break 2;
				}
				if (stristr(strtoupper($v), 'BODY')) {
					$search = false;
				}
			}
		}
		return $res;
	}

	/**
	 * Start getting a message part for reading it line by line
	 *
	 * @param <type> $uid
	 * @param <type> $message_part
	 * @return <type>
	 */
	function get_message_part_start($uid, $message_part) {
		$this->clean($uid, 'uid');
		if ($message_part == 0) {
			$command = "UID FETCH $uid BODY[]\r\n";
		}
		else {
			$this->clean($message_part, 'msg_part');
			$command = "UID FETCH $uid BODY[$message_part]\r\n";
		}
		$this->send_command($command);
		$result = fgets($this->handle, 1024);
		$size = false;
		if (preg_match("/\{(\d+)\}\r\n/", $result, $matches)) {
			$size = $matches[1];
		}
		return $size;
	}
	/**
	 * Read message part line. get_message_part_start must be called first
	 *
	 * @return <type>
	 */
	function get_message_part_line() {
		$res = fgets($this->handle, 1024);
		while(substr($res, -2) != "\r\n") {
			$res .= fgets($this->handle, 1024);
		}
		if ($this->check_response(array($res))) {
			$res = false;
		}
		return $res;
	}


	function set_message_flag($uids, $flags, $clear=false) {
		$status=false;
		$uid_strings = array();
		if (!empty($uids)) {
			if (count($uids) > 1000) {
				while (count($uids) > 1000) {
					$uid_strings[] = implode(',', array_splice($uids, 0, 1000));
				}
				if (count($uids)) {
					$uid_strings[] = implode(',', $uids);
				}
			}
			else {
				$uid_strings[] = implode(',', $uids);
			}
		}
		else {
			$uid_strings[] = $uid_str;
		}
		foreach ($uid_strings as $uid_string) {
			if ($uid_string) {
				$this->clean($uid_string, 'uid_list');
			}

			if($clear) {
				$command = "UID STORE $uid_string -FLAGS ($flags)\r\n";
			}else {
				$command = "UID STORE $uid_string +FLAGS ($flags)\r\n";
			}

			go_debug($command);

			$this->send_command($command);
			$res = $this->get_response();
			go_debug($res);
			$status = $this->check_response($res);
			if (!$status) {
				return $status;
			}
		}
		return $status;
	}

	public function copy($uids, $mailbox) {
		$this->clean($mailbox, 'mailbox');

		$uid_string = implode(',',$uids);

		$command = "UID COPY $uid_string \"".$this->utf7_encode($mailbox)."\"\r\n";
		$this->send_command($command);
		$res = $this->get_response();
		return $this->check_response($res);
	}

	public function move($uids, $mailbox) {

		if(!in_array($mailbox, $this->touched_folders)) {
			$this->touched_folders[]=$mailbox;
		}

		if(!$this->copy($uids, $mailbox))
			return false;

		return $this->delete($uids);
	}


	public function delete($uids) {
		$this->set_message_flag($uids, '\Deleted \Seen');
		return $this->expunge();
	}

	public function expunge() {
		$this->send_command("EXPUNGE\r\n");
		$res = $this->get_response();
		return $this->check_response($res);
	}


	private function prep_folder_name($mailbox, $prefix='', $parent=false, $subs=false) {
		if ($prefix) {
			$prefix = rtrim($prefix, $_SESSION['imap_delimiter']);
		}
		if ($parent) {
			$mailbox = $parent.$_SESSION['imap_delimiter'].$mailbox;
			$prefix = false;
		}
		if ($prefix) {
			if (strtoupper(substr($mailbox, 0, (strlen($prefix) + 1))) != strtoupper($prefix.$_SESSION['imap_delimiter'])) {
				$new_box_name = str_replace(array('"'), array('\"'), $prefix.$_SESSION['imap_delimiter'].$mailbox);
			}
			else {
				$new_box_name = str_replace('"', '\"', $mailbox);
			}
		}
		else {
			$new_box_name = str_replace('"', '\"', $mailbox);
		}
		if ($subs) {
			$new_box_name .= $_SESSION['imap_delimiter'];
		}
		return $new_box_name;
	}
	function delete_folder($mailbox) {
		$this->clean($mailbox, 'mailbox');

		$command = 'DELETE "'.str_replace('"', '\"', $this->utf7_encode($mailbox))."\"\r\n";
		$this->send_command($command);
		$result = $this->get_response(false);
		$status = $this->check_response($result, false);
		if ($status) {
			return false;
		}
		else {
			return str_replace('A'.$this->command_count, '', $result[0]);
		}
	}
	function rename_folder($prefix, $mailbox, $new_mailbox) {
		$this->clean($mailbox, 'mailbox');
		$this->clean($new_mailbox, 'mailbox');

		$command = 'RENAME "'.$this->prep_folder_name($this->utf7_encode($mailbox, $prefix)).'" "'.
						$this->prep_folder_name($this->utf7_encode($new_mailbox, $prefix)).'"'."\r\n";
		$this->send_command($command);
		$result = $this->get_response(false);
		$status = $this->check_response($result, false);
		if ($status) {
			return false;
		}
		else {
			return str_replace('A'.$this->command_count, '', $result[0]);
		}
	}
	function create_folder($prefix, $mailbox, $parent) {
		$this->clean($mailbox, 'mailbox');
		if ($parent) {
			$this->clean($parent, 'mailbox');
		}

		$command = 'CREATE "'.$this->prep_folder_name($this->utf7_encode($mailbox), $prefix, $parent).'"'."\r\n";
		$this->send_command($command);
		$result = $this->get_response(false);
		$status = $this->check_response($result, false);
		if ($status) {
			return false;
		}
		else {
			return str_replace('A'.$this->command_count, '', $result[0]);
		}
	}
	function full_search($terms) {
		$this->clean($this->search_charset, 'charset');
		$this->clean($terms, 'search_str');
		if ($this->search_charset) {
			$charset = 'CHARSET '.strtoupper($this->search_charset).' ';
		}
		else {
			$charset = '';
		}
		$command = 'UID SEARCH '.$charset.$terms."\r\n";
		$this->send_command($command);
		$result = $this->get_response(false, true);
		$status = $this->check_response($result, true);
		$res = array();
		if ($status) {
			array_pop($result);
			foreach ($result as $vals) {
				foreach ($vals as $v) {
					if (preg_match("/^\d+$/", $v)) {
						$res[] = $v;
					}
				}
			}
		}
		return $res;
	}
	function append_end() {
		$result = $this->get_response(false, true);
		$status = $this->check_response($result, true);
		return $status;
	}
	function append_feed($string) {
		return fwrite($this->handle, $string."\r\n");
	}
	function append_start($mailbox, $size, $flags = "") {
		$this->clean($mailbox, 'mailbox');
		$this->clean($size, 'uid');
		$command = 'APPEND "'.$this->utf7_encode($mailbox).'" ('.$flags.') {'.$size."}\r\n";
		$this->send_command($command);
		$result = fgets($this->handle);
		if (substr($result, 0, 1) == '+') {
			return true;
		}
		else {
			return false;
		}
	}

	function append_message($mailbox, $data, $flags=""){

		go_debug("imap::append_message($mailbox, data, $flags);");

		if(!$this->append_start($mailbox, strlen($data), $flags))
			return false;

		if(!$this->append_feed($data))
			return false;

		return $this->append_end();
	}
}
