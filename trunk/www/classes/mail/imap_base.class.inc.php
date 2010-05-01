<?php
class imap_base {

	var $touched_folders =array();

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

		//go_debug($result);
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

		//go_debug($command);
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

	function mime_header_decode($str) {
		//some mail clients create encoded strings such: =?iso-8859-1?Q? "Andr=E9=20Mc=20Intyre" ?=
		//containing space values inside, but they mustn't. The space values have to be removed before
		//they are going to be converted to utf8.

		//go_debug($str);

		if (strpos($str, '=?') !== false) {

			$obj = imap_mime_header_decode($str);
			//go_debug($obj);
			$decoded='';
			for ($i=0; $i<count($obj); $i++) {
				if($obj[$i]->charset!='UTF-8' && $obj[$i]->charset!='default') {
					$decoded.=iconv($obj[$i]->charset, 'UTF-8', $obj[$i]->text);
				}else {
					$decoded.=$obj[$i]->text;
				}
			}
			//go_debug($decoded);
			return $decoded;

			/*$decoded = preg_replace_callback(
        '/=\?.*\?=/U',
        create_function(
            // single quotes are essential here,
            // or alternative escape all $ as \$
            '$matches',
            'go_debug($matches[0]);
	$decoded= iconv_mime_decode($matches[0],ICONV_MIME_DECODE_CONTINUE_ON_ERROR,\'UTF-8\');

			if($decoded){
				return $decoded;
			}else
			{
				return imap_utf8($matches[0]);
			}
	'
        ),
        $str
			);

			//$str = str_replace(" ", "", $str);
			//return imap_utf8($str);
			//$decoded = iconv_mime_decode($str,ICONV_MIME_DECODE_CONTINUE_ON_ERROR,'UTF-8');
			//go_debug($decoded);
			if($decoded){
				return $decoded;
			}else
			{
				return imap_utf8($str);
			}*/
		}else {
			if (function_exists('iconv')) {
				if($converted = @iconv('ISO-8859-15', 'UTF-8//IGNORE', $str)) {
					return $converted;
				}
			}
		}
		return $str;

		/*
		 $text = '';
		 if($elements = imap_mime_header_decode($str))
		 {
		 foreach($elements as $element)
		 {
			$text .= $element->text;
			}
			return utf8_encode($text);
			}	*/
	}
}



/* parsing routines for the imap bodstructure response */
class imap_bodystruct extends imap_base {
	function update_part_num($part) {
		if (!strstr($part, '.')) {
			$part++;
		}
		else {
			$parts = explode('.', $part);
			$parts[(count($parts) - 1)]++;
			$part = implode('.', $parts);
		}
		return $part;
	}
	function parse_single_part($array) {
		$vals = $array[0];
		array_shift($vals);
		array_pop($vals);
		$atts = array('name', 'filename', 'type', 'subtype', 'charset', 'id', 'description', 'encoding',
						'size', 'lines', 'md5', 'disposition', 'language', 'location');
		$res = array();
		if (count($vals) > 7) {
			$res['type'] = strtolower(trim(array_shift($vals)));
			$res['subtype'] = strtolower(trim(array_shift($vals)));
			if ($vals[0] == '(') {
				array_shift($vals);
				while($vals[0] != ')') {
					if (isset($vals[0]) && isset($vals[1])) {
						$res[strtolower($vals[0])] = $vals[1];
						$vals = array_splice($vals, 2);
					}
				}
				array_shift($vals);
			}
			else {
				array_shift($vals);
			}
			$res['id'] = array_shift($vals);
			$res['description'] = array_shift($vals);
			$res['encoding'] = strtolower(array_shift($vals));
			$res['size'] = array_shift($vals);
			if ($res['type'] == 'text' && isset($vals[0])) {
				$res['lines'] = array_shift($vals);
			}
			if (isset($vals[0]) && $vals[0] != ')') {
				$res['md5'] = array_shift($vals);
			}
			if (isset($vals[0]) && $vals[0] == '(') {
				array_shift($vals);
			}
			if (isset($vals[0]) && $vals[0] != ')') {
				$res['disposition'] = array_shift($vals);
				if (strtolower($res['disposition']) == 'attachment' && $vals[0] == '(') {
					array_shift($vals);
					if (isset($vals[0]) && strtolower($vals[0]) == 'filename' && isset($vals[1]) && $vals[1] != ')') {
						array_shift($vals);
						$res['name'] = array_shift($vals);
						if ($vals[0] == ')') {
							array_shift($vals);
						}
					}
				}
			}
			if (isset($vals[0]) && $vals[0] != ')') {
				$res['language'] = array_shift($vals);
			}
			if (isset($vals[0]) && $vals[0] != ')') {
				$res['location'] = array_shift($vals);
			}
			foreach ($atts as $v) {
				if (!isset($res[$v]) || trim(strtoupper($res[$v])) == 'NIL') {
					$res[$v] = false;
				}
				else {
					if ($v == 'charset') {
						$res[$v] = strtolower(trim($res[$v]));
					}
					else {
						$res[$v] = trim($res[$v]);
					}
				}
			}
			if (!isset($res['name'])) {
				$res['name'] = 'message';
			}
		}
		return $res;
	}



	function filter_alternatives($struct, $filter, $parent_type=false, $cnt=0) {
		$filtered = array();
		if (!is_array($struct) || empty($struct)) {
			return array($filtered, $cnt);
		}
		if (!$parent_type) {
			if (isset($struct['subtype'])) {
				$parent_type = $struct['subtype'];
			}
		}
		foreach ($struct as $index => $value) {
			if ($parent_type == 'alternative' && isset($value['subtype']) && $value['subtype'] != $filter) {
				$cnt += 1;
			}
			else {
				$filtered[$index] = $value;
			}
			if (isset($value['subs']) && is_array($value['subs'])) {
				if (isset($struct['subtype'])) {
					$parent_type = $struct['subtype'];
				}
				else {
					$parent_type = false;
				}
				list($filtered[$index]['subs'], $cnt) = $this->filter_alternatives($value['subs'], $filter, $parent_type, $cnt);
			}
		}
		return array($filtered, $cnt);
	}
	function parse_multi_part($array, $part_num, $run_num) {
		$struct = array();
		$index = 0;
		foreach ($array as $vals) {
			if ($vals[0] != '(') {
				break;
			}
			$type = strtolower($vals[1]);
			$sub = strtolower($vals[2]);
			$part_type = 1;
			switch ($type) {
				case 'message':
					switch ($sub) {
						case 'delivery-status':
						case 'external-body':
						case 'disposition-notification':
						case 'rfc822-headers':
							break;
						default:
							$part_type = 2;
							break;
					}
					break;
			}
			if ($vals[0] == '(' && $vals[1] == '(') {
				$part_type = 3;
			}
			if ($part_type == 1) {
				$struct[$part_num] = $this->parse_single_part(array($vals));
				$part_num = $this->update_part_num($part_num);
			}
			elseif ($part_type == 2) {
				$parts = $this->split_toplevel_result($vals);
				$struct[$part_num] = $this->parse_rfc822($parts[0], $part_num);
				$part_num = $this->update_part_num($part_num);
			}
			else {
				$parts = $this->split_toplevel_result($vals);
				$struct[$part_num]['subs'] = $this->parse_multi_part($parts, $part_num.'.1', $part_num);
				$part_num = $this->update_part_num($part_num);
			}
			$index++;
		}
		if (isset($array[$index][0])) {
			$struct['type'] = 'message';
			$struct['subtype'] = $array[$index][0];
		}
		return $struct;
	}
	function parse_rfc822($array, $part_num) {
		$res = array();
		array_shift($array);
		$res['type'] = strtolower(trim(array_shift($array)));
		$res['subtype'] = strtolower(trim(array_shift($array)));
		if ($array[0] == '(') {
			array_shift($array);
			while($array[0] != ')') {
				if (isset($array[0]) && isset($array[1])) {
					$res[strtolower($array[0])] = $array[1];
					$array = array_splice($array, 2);
				}
			}
			array_shift($array);
		}
		else {
			array_shift($array);
		}
		$res['id'] = array_shift($array);
		$res['description'] = array_shift($array);
		$res['encoding'] = strtolower(array_shift($array));
		$res['size'] = array_shift($array);
		$envelope = array();
		if ($array[0] == '(') {
			array_shift($array);
			$index = 0;
			$level = 1;
			foreach ($array as $i => $v) {
				if ($level == 0) {
					$index = $i;
					break;
				}
				$envelope[] = $v;
				if ($v == '(') {
					$level++;
				}
				if ($v == ')') {
					$level--;
				}
			}
			if ($index) {
				$array = array_splice($array, $index);
			}
		}
		$res = $this->parse_envelope($envelope, $res);
		$parts = $this->split_toplevel_result($array);
		$res['subs'] = $this->parse_multi_part($parts, $part_num.'.1', $part_num);
		return $res;
	}
	function split_toplevel_result($array) {
		if (empty($array) || $array[1] != '(') {
			return array($array);
		}
		$level = 0;
		$i = 0;
		$res = array();
		foreach ($array as $val) {
			if ($val == '(') {
				$level++;
			}
			$res[$i][] = $val;
			if ($val == ')') {
				$level--;
			}
			if ($level == 1) {
				$i++;
			}
		}
		return array_splice($res, 1, -1);
	}
	function parse_envelope_address($array) {
		$count = count($array) - 1;
		$string = '';
		$name = false;
		$mail = false;
		$domain = false;
		for ($i = 0;$i<$count;$i+= 6) {
			if (isset($array[$i + 1])) {
				$name = $array[$i + 1];
			}
			if (isset($array[$i + 3])) {
				$mail = $array[$i + 3];
			}
			if (isset($array[$i + 4])) {
				$domain = $array[$i + 4];
			}
			if ($name && strtoupper($name) != 'NIL') {
				$name = str_replace(array('"', "'"), '', $name);
				if ($string != '') {
					$string .= ', ';
				}
				if ($name != $mail.'@'.$domain) {
					$string .= '"'.$name.'" ';
				}
				if ($mail && $domain) {
					$string .= $mail.'@'.$domain;
				}
			}
			if ($mail && $domain) {
				$string .= $mail.'@'.$domain;
			}
			$name = false;
			$mail = false;
			$domain = false;
		}
		return $string;
	}
	function parse_envelope($array, $res) {
		$flds = array('date', 'subject', 'from', 'sender', 'reply-to', 'to', 'cc', 'bcc', 'in-reply-to', 'message_id');
		foreach ($flds as $val) {
			if (strtoupper($array[0]) != 'NIL') {
				if ($array[0] == '(') {
					array_shift($array);
					$parts = array();
					$index = 0;
					$level = 1;
					foreach ($array as $i => $v) {
						if ($level == 0) {
							$index = $i;
							break;
						}
						$parts[] = $v;
						if ($v == '(') {
							$level++;
						}
						if ($v == ')') {
							$level--;
						}
					}
					if ($index) {
						$array = array_splice($array, $index);
						$res[$val] = $this->parse_envelope_address($parts);
					}
				}
				else {
					$res[$val] = array_shift($array);
				}
			}
			else {
				$res[$val] = false;
			}
		}
		return $res;
	}
}


