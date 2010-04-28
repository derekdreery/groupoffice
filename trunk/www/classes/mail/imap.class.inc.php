<?php

class imap_base {

	var $max_read=false;

	var $imap_search_charsets = array(
					'UTF-8',
					'US-ASCII',
					'');

	var $imap_keywords = array(
					'ARRIVAL',    'DATE',    'FROM',      'SUBJECT',
					'CC',         'TO',      'SIZE',      'UNSEEN',
					'SEEN',       'FLAGGED', 'UNFLAGGED', 'ANSWERED',
					'UNANSWERED', 'DELETED', 'UNDELETED', 'TEXT',
					'ALL'
	);


	function input_validate($val, $type) {
		//global $imap_search_charsets;
		//global $imap_keywords;
		$valid = false;
		switch ($type) {
			case 'search_str':
				if (preg_match("/^[^\r\n]+$/", $val)) {
					$valid = true;
				}
				break;
			case 'msg_part':
				if (preg_match("/^[\d\.]+$/", $val)) {
					$valid = true;
				}
				break;
			case 'charset':
				if (!$val || in_array(strtoupper($val), $this->imap_search_charsets)) {
					$valid = true;
				}
				break;
			case 'uid':
				if (preg_match("/^\d+$/", $val)) {
					$valid = true;
				}
				break;
			case 'uid_list';
				if (preg_match("/^(\d+\s*,*\s*|(\d+|\*):(\d+|\*))+$/", $val)) {
					$valid = true;
				}
				break;
			case 'mailbox';
				if (preg_match("/^[^\r\n]+$/", $val)) {
					$valid = true;
				}
				break;
			case 'keyword';
				if (in_array(strtoupper($val), $this->imap_keywords)) {
					$valid = true;
				}
				break;
		}
		return $valid;
	}
	function clean($val, $type) {
		if (!$this->input_validate($val, $type)) {
			go_debug("INVALID IMAP INPUT DETECTED: ".$type.': '.$val);
			exit;
		}
	}


	/* break up a "line" response from imap. If we find
       a literal we read ahead on the stream and include it.
	*/
	function parse_line($line, $current_size, $max, $line_length) {
		$line = str_replace(')(', ') (', $line);
		$parts = array();
		$line_cont = false;
		while ($line) {
			$chunk = false;
			switch ($line{0}) {
				case "\r":
				case "\n":
					$line = false;
					break;
				case ' ':
					$line = substr($line, 1);
					break;
				case '*':
				case '[':
				case ']':
				case '(':
				case ')':
					$chunk = $line{0};
					$line = substr($line, 1);
					break;
				case '"':
					if (preg_match("/^(\"[^\"\\\]*(?:\\\.[^\"\\\]*)*\")/", $line, $matches)) {
						$chunk = substr($matches[1], 1, -1);
					}
					$line = substr($line, strlen($chunk) + 2);
					break;
				case '{':
					$end = strpos($line, '}');
					if ($end !== false) {
						$literal_size  = substr($line, 1, ($end - 1));
					}
					$lit_result = $this->read_literal($literal_size, $max, $current_size, $line_length);
					$chunk = $lit_result[0];
					if ($lit_result[1]) {
						$line = str_replace(')', ' )', $lit_result[1]);
					}
					else {
						$line_cont = true;
						$line = false;
					}
					break;
				default:
					if (strpos($line, ' ') !== false) {
						$marker = strpos($line, ' ');
						$marker_adjust = $marker;
						$chunk = substr($line, 0, $marker);
						$char_check = substr($chunk, -1);
						$temp_chunk = $chunk;
						while ($temp_chunk && ($char_check == ')' || $char_check == ']')) {
							$marker_adjust--;
							$temp_chunk = substr($temp_chunk, 0, -1);
							$char_check = substr($temp_chunk, -1);
						}
						if ($marker_adjust != $marker) {
							$marker = $marker_adjust;
						}
						$chunk = substr($line, 0, $marker);
						$line = substr($line, strlen($chunk));
					}
					else {
						$chunk = trim($line);
						$line = false;
						$marker = strlen($chunk);
						$marker_adjust = $marker;
						$temp_chunk = trim($chunk);
						$char_check = substr($temp_chunk, -1);
						while ($temp_chunk && ($char_check == ')' || $char_check == ']')) {
							$marker_adjust--;
							$temp_chunk = substr($temp_chunk, 0, -1);
							$char_check = substr($temp_chunk, -1);
						}
						if ($marker_adjust != $marker) {
							$marker = $marker_adjust;
							$line = $chunk;
							$chunk = substr($line, 0, $marker);
							$line = substr($line, strlen($chunk));
						}
					}
					break;
			}
			if (is_string($chunk)) {
				$parts[] = $chunk;
			}
		}
		return array($line_cont, $parts);
	}
	/* Read literal found during parse_line().
	*/
	function read_literal($size, $max, $current, $line_length) {
		$left_over = false;
		$literal_data = fgets($this->handle, $line_length);
		$current += strlen($literal_data);
		while (strlen($literal_data) < $size) {
			$chunk = fgets($this->handle, $line_length);
			$current += strlen($chunk);
			$literal_data .= $chunk;
			if ($max && $current > $max) {
				$this->max_read = true;
				break;
			}
		}
		if ($size < strlen($literal_data)) {
			$left_over = substr($literal_data, $size);
			$literal_data = substr($literal_data, 0, $size);
		}
		return array($literal_data, $left_over);
	}
	/* loop through "lines" returned from imap and parse
       them with parse_line() and read_literal. it can return
       the lines in a raw format, or parsed into atoms. It also
       supports a maximum number of lines to return, in case we
       did something stupid like list a loaded unix homedir
       in UW
	*/
	function get_response($max=false, $chunked=false, $line_length=8192, $sort=false) {
		$result = array();
		$current_size = 0;
		$chunked_result = array();
		$last_line_cont = false;
		$line_cont = false;
		$c = -1;
		$n = -1;
		do {
			$n++;
			if (!is_resource($this->handle)) {
				break;
			}
			$result[$n] = fgets($this->handle, $line_length);
			$current_size += strlen($result[$n]);
			if ($max && $current_size > $max) {
				$this->max_read = true;
				break;
			}
			while(substr($result[$n], -2) != "\r\n") {
				if (!is_resource($this->handle)) {
					break;
				}
				$result[$n] .= fgets($this->handle, $line_length);
				$current_size += strlen($result[$n]);
				if ($max && $current_size > $max) {
					$this->max_read = true;
					break 2;
				}
			}
			if ($line_cont) {
				$last_line_cont = true;
				$pres = $n - 1;
				if ($chunks) {
					$pchunk = $c;
				}
			}
			if ($sort) {
				$line_cont = false;
				$chunks = explode(' ', trim($result[$n]));
			}
			else {
				list($line_cont, $chunks) = $this->parse_line($result[$n], $current_size, $max, $line_length);
			}
			if ($chunks && !$last_line_cont) {
				$c++;
			}
			if ($last_line_cont) {
				$result[$pres] .= ' '.implode(' ', $chunks);
				if ($chunks) {
					$line_bits = array_merge($chunked_result[$pchunk], $chunks);
					$chunked_result[$pchunk] = $line_bits;
				}
				$last_line_cont = false;
			}
			else {
				$result[$n] = join(' ', $chunks);
				if ($chunked) {
					$chunked_result[$c] = $chunks;
				}
			}
		} while (substr($result[$n], 0, strlen('A'.$this->command_count)) != 'A'.$this->command_count);
		$this->responses[] = $result;
		if ($chunked) {
			$result = $chunked_result;
		}
		return $result;
	}
	/* increment the imap command prefix such that it counts
       up on each command sent. ('A1', 'A2', ...) */
	function command_number() {
		$this->command_count += 1;
		return $this->command_count;
	}
	/* put a prefix on a command and send it to the server */
	function send_command($command, $piped=false) {
		if ($piped) {
			$final_command = '';
			foreach ($command as $v) {
				$final_command .= 'A'.$this->command_number().' '.$v;
			}
			$command = $final_command;
		}
		else {
			$command = 'A'.$this->command_number().' '.$command;
		}
		if (is_resource($this->handle)) {
			fputs($this->handle, $command);
		}
		$this->commands[trim($command)] = microtime();
	}
	/* determine if an imap response returned an "OK", returns
       true or false */
	function check_response($data, $chunked=false) {
		$result = false;
		if ($chunked) {
			if (!empty($data)) {
				$vals = $data[(count($data) - 1)];
				if ($vals[0] == 'A'.$this->command_count) {
					$this->short_responses[implode(' ', $vals)] = microtime();
					if (strtoupper($vals[1]) == 'OK') {
						$result = true;
					}
				}
			}
		}
		else {
			$line = array_pop($data);
			$this->short_responses[$line] = microtime();
			if (preg_match("/^A".$this->command_count." OK/i", $line)) {
				$result = true;
			}
		}
		return $result;
	}

	function utf7_decode($string) {
		$string = mb_convert_encoding($string, "UTF-8", "UTF7-IMAP" );
		return $string;
	}
	function utf7_encode($string) {
		$string = mb_convert_encoding($string, "UTF7-IMAP", "UTF-8" );
		return $string;
	}
}



class imap extends imap_base {

	var $handle=false;

	var $ssl=false;
	var $server='';
	var $port=143;
	var $username='';
	var $password='';

	var $starttls=false;

	var $auth='PLAIN';

	var $selected_mailbox=false;


	public function __destruct() {
		$this->disconnect();
	}

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

	public function disconnect() {
		if (is_resource($this->handle)) {
			$command = "LOGOUT\r\n";
			$this->send_command($command);
			$this->state = 'disconnected';
			$result = $this->get_response();

			return fclose($this->handle);
		}else {
			return false;
		}
	}



	function authenticate($username, $pass) {

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
			return $_SESSION['GO_IMAP'][$this->server]['imap_capability'];
		}

		$command = "CAPABILITY\r\n";
		$this->send_command($command);
		$response = $this->get_response();
		$this->capability = $_SESSION['GO_IMAP'][$this->server]['imap_capability'] = implode(' ', $response);

		go_debug($this->capability);

		return $this->capability;
	}


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



	public function select_mailbox($mailbox_name) {

		if($this->selected_mailbox && $this->selected_mailbox['name']==$mailbox_name)
			return true;

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


	function get_mailbox_unseen($folder) {

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
					if (is_int($v)) {
						$unseen++;
						$uids[] = $v;
					}
				}
			}
		}
		return array($unseen, $uids);
	}


	function sort_mailbox($sort, $reverse=false, $filter='ALL') {

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
	/* use the SORT extension to get a sorted UID list */
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



	function get_message_headers($uids) {

		$sorted_string = implode(',', $uids);
		$this->clean($sorted_string, 'uid_list');
		$command = 'UID FETCH '.$sorted_string.' (FLAGS INTERNALDATE RFC822.SIZE BODY.PEEK[HEADER.FIELDS (SUBJECT FROM '.
						"DATE CONTENT-TYPE X-PRIORITY TO)])\r\n";
		$this->send_command($command);
		$res = $this->get_response(false, true);

		$status = $this->check_response($res, true);
		$tags = array('UID' => 'uid', 'FLAGS' => 'flags', 'RFC822.SIZE' => 'size', 'INTERNALDATE' => 'internal_date');
		$junk = array('SUBJECT', 'FROM', 'CONTENT-TYPE', 'TO', '(', ')', ']', 'X-PRIORITY', 'DATE');
		$flds = array('date' => 'date', 'from' => 'from', 'to' => 'to', 'subject' => 'subject', 'content-type' => 'content_type');
		$headers = array();
		foreach ($res as $n => $vals) {
			if (isset($vals[0]) && $vals[0] == '*') {
				$uid = 0;
				$size = 0;
				$subject = '';
				$from = '';
				$date = '';
				$x_priority = 0;
				$content_type = '';
				$to = '';
				$flags = '';
				$internal_date = '';
				$count = count($vals);
				for ($i=0;$i<$count;$i++) {
					if ($vals[$i] == 'BODY[HEADER.FIELDS') {
						$i++;
						while(isset($vals[$i]) && in_array($vals[$i], $junk)) {
							$i++;
						}
						$lines = explode("\r\n", $vals[$i]);
						foreach ($lines as $line) {
							$header = strtolower(substr($line, 0, strpos($line, ':')));
							if (!$header) {
								${$flds[$last_header]} .= "\r\n".$line;
							}
							elseif (isset($flds[$header])) {
								${$flds[$header]} = substr($line, (strpos($line, ':') + 1));
								$last_header = $header;
							}

						}
					}
					elseif (isset($tags[strtoupper($vals[$i])])) {
						if (isset($vals[($i + 1)])) {
							if ($tags[strtoupper($vals[$i])] == 'flags' && $vals[$i + 1] == '(') {
								$n = 2;
								while (isset($vals[$i + $n]) && $vals[$i + $n] != ')') {
									$flags .= ' '.$vals[$i + $n];
									$n++;
								}
								$i += $n;
							}
							else {
								$$tags[strtoupper($vals[$i])] = $vals[($i + 1)];
								$i++;
							}
						}
					}
				}
				if ($uid) {
					$cset = '';
					if (stristr($content_type, 'charset=')) {
						if (preg_match("/charset\=([^\s]+)/", $content_type, $matches)) {
							$cset = trim(strtolower(str_replace(array('"', "'", ';'), '', $matches[1])));
						}
					}
					$headers[$uid] = array(
									'uid' => $uid,
									'flags' => $flags,
									'internal_date' => $internal_date,
									'size' => $size,
									'date' => $date,
									'from' => $from,
									'to' => $to,
									'subject' => $subject,
									'content-type' => $content_type,
									'timestamp' => time(),
									'charset' => $cset);
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

	function get_message_structure($uid) {
		$this->clean($uid, 'uid');
		$part_num = 1;
		$struct = array();
		$command = "UID FETCH $uid BODYSTRUCTURE\r\n";
		$this->send_command($command);
		$result = $this->get_response(false, true);
		var_dump($result);
		while (isset($result[0][0]) && isset($result[0][1]) && $result[0][0] == '*' && strtoupper($result[0][1]) == 'OK') {
			array_shift($result);
		}
		$status = $this->check_response($result, true);

	}
}