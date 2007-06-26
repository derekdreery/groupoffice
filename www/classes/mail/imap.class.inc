<?php
/**
* @copyright Intermesh 2004
* @author Merijn Schering <mschering@intermesh.nl>
* @version $Revision: 1.34 $ $Date: 2006/10/31 12:31:22 $
*  
* This program is free software; you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the License, or (at your
* option) any later version.
*/

/**
* A class to handle IMAP and POP-3 connections.
*
* @package Framework
* @subpackage Mail
* @author   Merijn Schering <mschering@intermesh.nl>
* @since    Group-Office 1.0
*/

require_once ($GO_CONFIG->class_path.'mail/RFC822.class.inc');
class imap {
	/**
	* The recource ID that points to the IMAP stream
	*
	* @var     		Recource ID
	* @access  private
	*/
	var $conn;

	/**
	* The number of messages in the current mailbox
	*
	* @var     		Int
	* @access  private
	*/
	var $count;

	/**
	* The number of unseen messages in the current mailbox
	*
	* @var     		Int
	* @access  private
	*/
	var $unseen;

	/**
	* Array of messages that were fetched with imap_fetch_overview()
	*
	* @var     		Array
	* @access  private
	*/
	var $messages = array ();

	/**
	* Current message that is get with get_message();
	*
	* @var     		Array
	* @access  private
	*/
	var $message;

	/**
	* The type of the mailserver. IMAP or POP-3
	*
	* @var     		String
	* @access  private
	*/
	var $servertype;

	/**
	* The string to connect to the mailserver with imap_open();
	*
	* @var     		String
	* @access  private
	*/
	var $connectstring;

	/**
	* Array of mailboxes fetched with get_mailboxes();
	*
	* @var     		Array
	* @access  private
	*/
	var $mailboxes;

	/**
	* Currently opened mailbox
	*
	* @var     		String
	* @access  private
	*/
	var $mailbox;

	/**
	* Sorted message uid's returned by imap_sort();
	*
	* @var     		Array
	* @access  private
	*/
	var $sort;

	/**
	* Array with mailbox info fetched with imap_mailboxinfo()
	*
	* @var     		Array
	* @access  private
	*/
	var $mailbox_info;

	/**
	* Class to handle formatting of e-mail address strings.
	*
	* @var     		Object
	* @access  private
	*/
	var $RFC822;

	/**
	* Constructor. Initialises RFC822 class
	*
	* @access public
	* @return void
	*/
	function imap() {
		$this->RFC822 = new RFC822();
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
	function open($host, $type, $port, $username, $password, $mailbox = "INBOX", $flags = 0, $ssl = false, $novalidate_cert = false) {
		global $GO_CONFIG;

		$this->servertype = strtolower($type);
		$this->mailbox = $mailbox;
		$this->connectstring = $host.":".$port."/".$this->servertype.$GO_CONFIG->email_connectstring_options;

		if ($ssl) {
			$this->connectstring .= '/ssl';
		}

		if ($novalidate_cert) {
			$this->connectstring .= '/novalidate-cert';
		}

		if ($flags != 0) {
			$this->conn = @ imap_open("{".$this->connectstring."}".$this->mailbox, $username, $password, $flags);
		} else {
			$this->conn = @ imap_open("{".$this->connectstring."}".$this->mailbox, $username, $password);
		}
		return $this->conn;
	}

	/**
	* Close the connection to the mailserver
	*
	* @access public
	* @return void
	*/
	function close() {
		unset ($this->messages);
		unset ($this->count);
		unset ($this->unseen);
		unset ($this->next_message_id);
		@ imap_close($this->conn);
		unset ($this->conn);
	}

	/**
	* Count total and new messages on server
	*
	* @param	string	$mailbox	The mailbox to get status of
	* @access public
	* @return void
	*/
	function status($mailbox = false, $options=SA_UNSEEN) {
		if (!$mailbox)
			$mailbox = $this->mailbox;

		$status = imap_status($this->conn, "{".$this->connectstring."}".$mailbox, $options);
		return $status;
	}
	
	/**
	* Count total and new messages on server
	*
	* @param	string	$mailbox	The mailbox to get status of
	* @access public
	* @return void
	*/
	function get_unseen($mailbox = false, $recursive=false) {
		if (!$mailbox)
			$mailbox = $this->mailbox;

		
		if(!$recursive)
		{
			$status = imap_status($this->conn, "{".$this->connectstring."}".$mailbox, SA_UNSEEN);
			return $status->unseen;
		}else
		{			
			$unseen =0;
			//Sometimes INBOX is subscribed, sometimes not. So check it manually and skip it
			//if it's subscribed.
			if($mailbox == 'INBOX')
			{
				$status = imap_status($this->conn, "{".$this->connectstring."}INBOX", SA_UNSEEN);
				$unseen+=$status->unseen;	
			}
			$folders = $this->get_subscribed($mailbox);			
			foreach($folders as $folder)
			{
				if($folder['name'] != 'INBOX')
				{
					$status = imap_status($this->conn, "{".$this->connectstring."}".$folder['name'], SA_UNSEEN);
					$unseen+=$status->unseen;	
				}
			}
			return $unseen;	
		}		
	}

	/**
	* Check if this is an IMAP server
	*
	* @param	string	$mailbox	The mailbox to get status of
	* @access public
	* @return bool	 True if this is an IMAP server
	*/

	function is_imap() {
		if ($this->servertype == "imap") {
			return true;
		} else {
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
	*/
	function sort($sort_type = SORTDATE, $reverse = "1", $query = '') {
		if ($query != '') {
			$this->sort = imap_sort($this->conn, $sort_type, $reverse, SE_UID, trim($query));
		} else {
			$this->sort = imap_sort($this->conn, $sort_type, $reverse, SE_UID);
		}
		if ($this->sort) {
			$this->count = count($this->sort);
		} else {
			$this->sort = array ();
			$this->count = 0;
		}
		return $this->count;
	}

	function build_search_query($subject = '', $from = '', $to = '', $cc = '', $body = '', $before = '', $since = '', $before = '', $since = '', $flagged = '', $answered = '', $seen='') {
		$query = '';
		if ($subject != '') {
			$query .= 'SUBJECT "'.$subject.'" ';
		}
		if ($from != '') {
			$query .= 'FROM "'.$from.'" ';
		}
		if ($to != '') {
			$query .= 'TO "'.$to.'" ';
		}
		if ($cc != '') {
			$query .= 'CC "'.$cc.'" ';
		}
		if ($body != '') {
			$query .= 'BODY "'.$body.'" ';
		}

		if ($before != '') {
			$unix_before = date_to_unixtime($before);
			$query .= 'BEFORE "'.date('d-M-Y', $unix_before).'" ';
		}

		if ($since != '') {
			$unix_since = date_to_unixtime($since);
			$query .= 'SINCE "'.date('d-M-Y', $unix_since).'" ';
		}

		if ($flagged != '') {
			$query .= $flagged.' ';
		}

		if ($answered != '') {
			$query .= $answered.' ';
		}
		
		if ($seen != '') {
			$query .= $seen.' ';
		}
		
		return $query;
	}

	/**
	* Fetches the sorted messages and puts them into $this->messages
	*
	* @param	int	$first The first message to fetch
	* @param	int	$offset The number of messages to fetch
	* @access public
	* @return void
	*/

	function get_messages($first, $offset) {
		//Use imap_fetch_overview and sort them correctly
		//for performance.

		if (!isset($this->sort)) {
			$this->sort();
		}

		$last = $first + $offset;
		if ($offset == 0 || ($last > $this->count)) {
			$last = $this->count;
		}
		$get = '';
		$index = 0;
		$sorted_uids = array ();
		for ($i = $first; $i < $last; $i ++) {
			if ($i != $first) {
				$get .= ',';
			}
			$get .= $this->sort[$i];
			$sorted_uids[] = $this->sort[$i];
			$index ++;
		}

		$tmp = $sorted_uids;
		sort($tmp); //this is the way they are returned by imap_fetch_overview

		$uid_map = array ();
		$count = count($tmp);
		for ($i = 0; $i < $count; $i ++) {
			$uid_map[$tmp[$i]] = $i;
		}

		$tmp_overviews = imap_fetch_overview($this->conn, $get, FT_UID);

		while ($message_overview = array_shift($tmp) && $uid = array_shift($sorted_uids)) {
			$this->messages[] = $tmp_overviews[$uid_map[$uid]];
		}
	}
	
	function _next_message()
	{
		if (!isset ($this->sort)) {
			$this->sort();
		}

		if($uid = array_shift($this->sort))
		{
			return $this->get_message($uid);
		}
		return false;
	}

	/**
	* Loop trough all messages after calling get_messages()
	*
	* @access public
	* @return array The E-mail message elements
	*/
	function next_message($examine_header=false) {
		if (!isset ($this->messages)) {
			die('Call get_messages() first');
		}

		if ($overview = array_shift($this->messages)) {
			unset($this->message);
			$this->message["uid"] = $overview->uid;
			$this->message["msgno"] = $overview->msgno;

			//$this->message['udate'] = strtotime($overview->date);
			$this->message["date"] = $overview->date;
			$this->message['subject'] = isset ($overview->subject) ? enc_utf8($overview->subject) : '';

			$from = $this->RFC822->parse_address_list(enc_utf8($overview->from));
			if (isset ($from[0])) {
				$email = $from[0]['email'];
			}
			$personal = isset ($from[0]['personal']) ? $from[0]['personal'] : '';

			$this->message["from"] = $personal == '' ? $email : $personal;
			$this->message["sender"] = $email;
			$this->message["flagged"] = $overview->flagged;
			$this->message["size"] = $overview->size;
			$this->message["answered"] = $overview->answered;
			//$this->message['new'] = (($overview->recent==1 || $overview->seen==0) && $this->is_imap()) ? true : false;
			$this->message['new'] = ($overview->seen == 0 && $this->is_imap()) ? true : false;
			
			
			$headerinfo = imap_headerinfo($this->conn, imap_msgno($this->conn,$overview->uid));
			$this->message['udate'] = $headerinfo->udate;
			
			if (isset ($headerinfo->to)) {
				$tmp = $headerinfo->to;

				for ($x = 0; $x < count($tmp); $x ++) {
					$email = '';
					if (isset ($tmp[$x]->mailbox)) {
						$host = isset ($tmp[$x]->host) ? '@'.$tmp[$x]->host : '';
						$email = $tmp[$x]->mailbox.$host;
					}
					$personal = isset ($tmp[$x]->personal) ? enc_utf8($tmp[$x]->personal) : '';
					$this->message['to'][$x] = $this->RFC822->write_address($personal, $email);
				}
			}

			
			if($examine_header)
			{
				
				//much slower but people want the attachment icon!
				$this->message["attachments"]=false;
				$header = imap_fetchheader($this->conn, imap_msgno($this->conn,$overview->uid));
				$this->message["priority"] = $this->get_header_value('X-Priority:', $header);		
				/*$RFC822 = new RFC822();
				$this->message["to"] = $RFC822->explode_address_list(enc_utf8($this->get_header_value('To:', $header)));*/
				
				$content_type = $this->get_header_value('Content-Type:', $header);
				if (!empty($content_type) && eregi("([^/]*)/([^ ;\n\t]*)", $content_type, $regs)) {
					$mtype = strtolower($regs[1]);
					$stype = strtolower($regs[2]);
				}else
				{
					$mtype = '';
					$stype = '';
				}
				if ($mtype != 'text' && $stype != 'alternative' && $stype != 'related' && !empty($mtype) && !empty($stype))
				{
					$this->message["attachments"] = true;
				}
			}
			
			
		

			return $this->message;
		} else {
			return false;
		}
	}
	
	function get_source($uid)
	{
		return imap_fetchheader($this->conn,$uid, FT_UID).imap_body($this->conn, $uid, FT_UID);
	}

	/**
	* Get one message with the structure
	*
	* @param int $uid The unique identifier of the
	* @param string $preferred_type Preferred body type to get html or text
	* @param string $part Get a specific part of a message
	* @access public
	* @return array The E-mail message elements
	*/
	function get_message($uid, $preferred_type = "html", $part = "") {
		if ($this->conn) {
			unset ($this->message);
			
			

			//determine next and previous message
			if (!isset ($this->sort)) {
				$this->sort();
			}

			$this->message['uid'] = $uid;
			$this->message['number'] = imap_msgno($this->conn, $uid);
			
			if(!@$headerinfo = imap_headerinfo($this->conn, $this->message['number']))
			{
				//message doesn't exist
				return false;
			}
			//var_dump($headerinfo);

			for ($i = 0; $i < sizeof($this->sort); $i ++) {
				if ($this->message['uid'] == $this->sort[$i]) {
					$this->message["next"] = ($i -1 >= 0) ? $this->sort[$i -1] : 0;
					$this->message["previous"] = ($i +1 < sizeof($this->sort)) ? $this->sort[$i +1] : 0;
					break;
				}
			}			
			
			
			
			//$this->message['udate'] = $headerinfo->udate;
			$this->message["udate"] = strtotime($headerinfo->date);

			$this->message['new'] = ($headerinfo->Unseen == 'U' && $this->is_imap()) ? true : false;

			$tmp = $headerinfo->from;
			$this->message["sender"] = $tmp[0]->mailbox.'@'.$tmp[0]->host;
			$this->message["from"] = isset ($tmp[0]->personal) ? enc_utf8($tmp[0]->personal) : $this->message["sender"];

			$this->message["reply_to"] = $this->RFC822->write_address($this->message['from'], $this->message["sender"]);

			if (isset ($headerinfo->reply_to)) {
				$tmp = $headerinfo->reply_to;
				$this->message["reply_to"] = $this->RFC822->write_address($this->message['from'], $tmp[0]->mailbox.'@'.$tmp[0]->host);
			}

			$this->message["subject"] = isset ($headerinfo->Subject) ? enc_utf8($headerinfo->Subject) : '';

			$this->message["to"] = array();
			if (isset($headerinfo->to) && is_array ($headerinfo->to)) {
				$tmp = $headerinfo->to;

				for ($x = 0; $x < sizeof($tmp); $x ++) {
					$email = '';
					if (isset ($tmp[$x]->mailbox)) {
						$host = isset ($tmp[$x]->host) ? '@'.$tmp[$x]->host : '';
						$email = $tmp[$x]->mailbox.$host;
					}

					$personal = isset ($tmp[$x]->personal) ? enc_utf8($tmp[$x]->personal) : '';
					$this->message["to"][$x] = $this->RFC822->write_address($personal, $email);
				}
			}
			
			$this->message["cc"] = array();
			if (isset($headerinfo->cc) && is_array ($headerinfo->cc)) {
				$tmp = $headerinfo->cc;
				for ($x = 0; $x < sizeof($tmp); $x ++) {
					$email = '';
					if (isset ($tmp[$x]->mailbox)) {
						$host = isset ($tmp[$x]->host) ? '@'.$tmp[$x]->host : '';
						$email = $tmp[$x]->mailbox.$host;
					}

					$personal = isset ($tmp[$x]->personal) ? enc_utf8($tmp[$x]->personal) : '';
					$this->message["cc"][$x] = $this->RFC822->write_address($personal, $email);
				}
			}

			$this->message["bcc"] = array();
			if (isset($headerinfo->bcc) && is_array ($headerinfo->bcc)) {
				$tmp = $headerinfo->bcc;
				for ($x = 0; $x < sizeof($tmp); $x ++) {
					$email = '';
					if (isset ($tmp[$x]->mailbox)) {
						$host = isset ($tmp[$x]->host) ? '@'.$tmp[$x]->host : '';
						$email = $tmp[$x]->mailbox.$host;
					}

					$personal = isset ($tmp[$x]->personal) ? enc_utf8($tmp[$x]->personal) : '';
					$this->message["bcc"][$x] = $this->RFC822->write_address($personal, $email);
				}
			}
			
			

			$this->message["parts"] = array ();

			if ($part == '') {
				$structure = imap_fetchstructure($this->conn, $uid, FT_UID);
			} else {
				$structure = imap_bodystruct($this->conn, $this->message['number'], $part);
			}

			$this->mail["parts"] = array ();
			$this->get_parts($structure, $preferred_type);

			// $this->print_structure($structure);

			$header = imap_fetchheader($this->conn, $uid, FT_UID);
			$this->message["priority"] = $this->get_header_value("X-Priority:", $header);
			$this->message["notification"] = $this->get_header_value("Disposition-Notification-To:", $header);
			$this->message["header"] = $header;

			$overview = imap_fetch_overview($this->conn, $uid, FT_UID);
			$this->message["flagged"] = $overview[0]->flagged;
			$this->message["size"] = $overview[0]->size;
			$this->message["answered"] = $overview[0]->answered;

			return $this->message;

		} else {
			return false;
		}
	}

	/**
	* Get structured parts of a message
	*
	* @param array $mimeobj An array returned by imap_fetch_structure()
	* @param string $preferred_type Preferred body type to get html or text
	* @param string $section The current section of the message
	* @access private
	* @return void
	*/
	function get_parts($mimeobj, $preferred_type = "html", $section = 0) {
		if (isset ($mimeobj->type)) {
			$type = $this->get_mime_type($mimeobj->type);
		} else {
			$type = 'text';
		}

		$full_mime_type = $type."/".$mimeobj->subtype;
		//echo $full_mime_type.'<br />';
		$encoding = $this->get_encoding($mimeobj->encoding);

		if (isset ($mimeobj->parameters)) {
			$params = $mimeobj->parameters;
			for ($x = 0; $x < count($params); $x ++) {
				if(is_object($params) && isset($params->$x))
				{
					$param = $params->$x;
				}elseif(is_array($params) && isset($params[$x]))
				{
					$param = $params[$x];
				}else
				{
					$param = null;
				}
				
				if(isset($param))
				{
					if ((strtolower($param->attribute) == 'name' || strtolower($param->attribute) == 'name*') && $param->value != '') {
						$name = enc_utf8($param->value);
						break;
					}
				}
			}
		}
		$name = isset ($name) ? $name : '';

		if ((!isset ($name) || $name == "") && isset ($mimeobj->dparameters)) {
			$params = $mimeobj->dparameters;
			for ($x = 0; $x < count($params); $x ++) {
				if(is_object($params) && isset($params->$x))
				{
					$param = $params->$x;
				}elseif(is_array($params) && isset($params[$x]))
				{
					$param = $params[$x];
				}else
				{
					$param = null;
				}
				
				if(isset($param))
				{
					if ((strtolower($param->attribute) == 'filename' || strtolower($param->attribute) == 'filename*') && $param->value != '') {
						//$name = enc_utf8(quoted_printable_decode($param->value));
						$name = enc_utf8($param->value);
						break;
					}
				}
			}
		}

		$x = 0;
		if (isset ($mimeobj->parts)) {
			for ($x = 0; $x < count($mimeobj->parts); $x ++) {
			
				if ($mimeobj->subtype == "ALTERNATIVE" && $preferred_type == "html") {
					if (isset ($mimeobj->parts[$x +1]) && eregi('html', $mimeobj->parts[$x +1]->subtype)) {
						$x ++;
					} else {
						$preferred_type = 'text';
					}
				}

				// If we are in the root of the object increment by whole integers

				if ($section == 0) {
					/*if(strtolower($mimeobj->subtype)!='related' && strtolower($mimeobj->subtype)!='mixed' && strtolower($mimeobj->subtype)!='alternative')
					{
						$nsection = $x +1;
					}else {
						//$nsection=0;
					}*/
					$nsection = $x +1;
				} else
					if (($pos = strrpos($section, ".")) && ($mimeobj->parts[0]->type != TYPEMULTIPART || $mimeobj->parts[0]->subtype != 'RELATED')) {
						$subsection = (int) substr($section, $pos +1) + $x;
						if ($subsection == '') {
							$subsection = '0';
						}
						$nsection = substr($section, 0, $pos).".". ($subsection +1);
					} else {
						$nsection = $section;
					}

				// If there are more parts to the part about to be processed reference it as a header with ".0"
				// but only if the child of this child isn't MULTIPART

				if (isset ($mimeobj->parts[$x]->parts) && count($mimeobj->parts[$x]->parts) && strtolower($mimeobj->parts[$x]->subtype) != 'rfc822') {
					// Funny really, if a mime section is a inline message that has a multipart body you reference the message
					// mime section with "2" the inline message header with "2.0" and the subsections with	"2.x"
					// However if the mime section is a inline message with only 1 part then you reference the
					// mime section in the message with 2.0 and the	inline message body with 2.1

					/*	//echo $mimeobj->parts[$x]->parts[0]->type.' '.TYPEMULTIPART.'<br />';
					if (!($mimeobj->parts[$x]->type == TYPEMESSAGE && $mimeobj->parts[$x]->parts[0]->type == TYPEMULTIPART)) {
						$nsection .= ".0";
					} else {
						$nsection .= "";
					}*/
					
					$nsection .= ".0";
				}

				if(strtolower($full_mime_type) != 'message/rfc822')
				{
					$this->get_parts($mimeobj->parts[$x], $preferred_type, $nsection);
				}else {
			
					if (strtolower($mimeobj->parts[$x]->subtype) == 'mixed') {					
						$section .= ".2";
					}
				}
				

				//if ($mimeobj->subtype == "ALTERNATIVE" && $preferred_type == "plain")
					//$x ++;

			}
		}

		// If after processing the entire MIME object the $x variable is still zero then we didn't
		// process a multipart mime message.

		if ($x == 0 && $section == 0) {
			$section = "1";
		}

		if ($type != "multipart" && $full_mime_type) {
			/*if (eregi('message', $full_mime_type)) {
				$section ++;
			}*/

			$part_charset = 'ISO-8859-15';
			if ($mimeobj->ifparameters) {
				for ($x = 0; $x < count($mimeobj->parameters); $x ++) {
					$param = $mimeobj->parameters[$x];
					if ((strtolower($param->attribute) == 'charset') && ($param->value != '')) {
						$part_charset = $param->value;
						break;
					}
				}
			}
//echo $section.': '.$full_mime_type.' '.$name.'<br>';
			$bytes = isset ($mimeobj->bytes) ? $mimeobj->bytes : 0;
			$tmp = Array ('number' => $section, 'id' => $mimeobj->ifid ? $mimeobj->id : 0, 'name' => $name, 'mime' => $full_mime_type, 'transfer' => $encoding, 'charset' => $part_charset, 'disposition' => $mimeobj->ifdisposition ? $mimeobj->disposition : '', 'size' => $bytes);

			array_unshift($this->message["parts"], $tmp);
		}
	}
	/*
		function print_structure($mimeobj, $depth = 0, $section = 0)
		{
			for($y = 0; $y < $depth; $y++)
			{
				echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
			}
			echo($this->get_mime_type($mimeobj->type) . "/{$mimeobj->subtype},");
			echo($this->get_encoding($mimeobj->encoding) . "(<B>$section</B>)<br>");
	
			$x=0;
			if (isset($mimeobj->parts))
			{
	
				for($x = 0; $x < count($mimeobj->parts); $x++)
				{
					// If we are in the root of the object increment by whole	integers
	
					if($section == 0)
					{
						$nsection = $x + 1;
						$subsection = 0;
						// If we are in the object and the first sub-object of our	object isn't multipart
						// then increment the postfix by ".1" otherwise	we are multipart or a message
						// and leave the section id alone to be handled by the next	code block
	
						//else if(($pos = strrpos($section, ".")) && sizeof($mimeobj->parts) > 1)
					}else if(($pos = strrpos($section, ".")) && ($mimeobj->parts[0]->type != TYPEMULTIPART || $mimeobj->parts[0]->subtype != 'RELATED'))
						//}elseif($pos = strrpos($section, "."))
					{
						$subsection = (int) substr($section, $pos+1)+$x;
						if ($subsection == '')
						{
							$subsection = '0';
						}
						$nsection = substr($section, 0, $pos) .	"." . ($subsection + 1);
					}else
					{
						$nsection = $section;
	
					}
	
					// If there are more parts to the part about to be processed reference it as a header with ".0"
					// but only if the child of this child isn't MULTIPART
	
					if(isset($mimeobj->parts[$x]->parts) && count($mimeobj->parts[$x]->parts))
					{
						// Funny really, if a mime section is a inline message that has a multipart body you reference the message
						// mime section with "2" the inline	message header with "2.0" and the subsections with	"2.x"
						// However if the mime section is a inline message with only 1 part then you reference the
						// mime section in the message with 2.0 and the	inline message body with 2.1
	
						if(!($mimeobj->parts[$x]->type == TYPEMESSAGE && $mimeobj->parts[$x]->parts[0]->type ==	TYPEMULTIPART))
						{
							$nsection .= ".0";
						}else
						{
							$nsection .= "";
						}
					}
	
					$this->print_structure($mimeobj->parts[$x], $depth + 1, $nsection);
			}
		}
	
		// If after processing the entire MIME object the $x variable is still zero then we didn't
		// process a multipart mime message, it's just normal email so say so here.
	
		if($x == 0 && $section == 0)
		{
			echo($this->get_mime_type($mimeobj->type) . "/{$mimeobj->subtype}, ");
			echo($this->get_encoding($mimeobj->encoding) . "(<B>1</B>) (<B>NOT MIME MULTIPART</B>)<br>");
		}
	}
	*/

	/**
		* Get the encoding of a message part in text
		*
		* @param int $encoding The encoding type as ut comes from imap_fetch_structure()
		* @access private
		* @return String The Encoding
		*/

	function get_encoding($encoding) {
		switch ($encoding) {
			case 0 :
				$encoding = '7BIT';
				break;
			case 1 :
				$encoding = '8BIT';
				break;
			case 2 :
				$encoding = 'BINARY';
				break;
			case 3 :
				$encoding = 'BASE64';
				break;
			case 4 :
				$encoding = 'QUOTED-PRINTABLE';
				break;
			case 5 :
				$encoding = 'OTHER';
				break;
			default :
				$encoding = 'none';
				break;
		}

		return $encoding;
	}

	/**
		* Get the mime type of a message part in text
		*
		* @param int $type The mime type as ut comes from imap_fetch_structure()
		* @access private
		* @return String The mime type
		*/

	function get_mime_type($type) {
		switch ($type) {
			case 0 :
				$mime_type = 'text';
				break;
			case 1 :
				$mime_type = 'multipart';
				break;
			case 2 :
				$mime_type = 'message';
				break;
			case 3 :
				$mime_type = 'application';
				break;
			case 4 :
				$mime_type = 'audio';
				break;
			case 5 :
				$mime_type = 'image';
				break;
			case 6 :
				$mime_type = 'video';
				break;
			case 7 :
				$mime_type = 'model';
				break;
			default :
				$mime_type = 'unknown';
		}
		return $mime_type;
	}

	/**
		* Search a plain text header for a value
		*
		* @param String $fieldname The name of the field
		* @param String $header The plain text header to search through
		* @access private
		* @return string The value of the fieldname if found.
		*/

	function get_header_value($fieldname, $header) {
		$resu = '';
		$header = eregi_replace("\t", " ", $header);
		$results = array ();
		if (eregi("$fieldname (.*)", $header, $results)) {
			$fieldval = $results[1];
			for ($b = 0; $b <= strlen($fieldval); $b ++) {
				$curr = substr($fieldval, $b, 1);
				$next = substr($fieldval, $b +1, 1);
				if ($curr == "\n" && $next != " ") {
					break;
				}
				if ($curr == "\t") {
					$curr = " ";
				}
				if ($curr == "\n") {
					$curr = "";
				}
				$resu .= $curr;
			}
		}
		$resu = eregi_replace("\([^\)]*\)", "", $resu);
		return trim($resu);
	}

	/**
		* Delete messages from the IMAP server
		*
		* @param Array $messages An array of message UID's
		* @access public
		* @return void
		*/

	function delete($messages) {
		for ($i = 0; $i < count($messages); $i ++) {
			@ imap_delete($this->conn, $messages[$i], FT_UID);
		}
		return @ imap_expunge($this->conn);
	}

	/**
		* Return a message part
		*
		* @param int $uid The message UID
		* @param float $part_no The message part identifier
		* @param String $transfer The transfer-encoding
		* @param String $part_charset The character set of the part if applicable
		* @access public
		* @return string Message part data
		*/

	function view_part($uid, $part_no, $transfer, $part_charset = '') {
		global $GO_CONFIG, $charset;

		$str = imap_fetchbody($this->conn, $uid, $part_no, FT_UID);
		if ($transfer == 'BASE64')
		{
			$str = imap_base64($str);
		}elseif ($transfer == 'QUOTED-PRINTABLE') 
		{
			$str = quoted_printable_decode($str);
			
			/*
			 * work around for some characters.
			 * They seem only to occur when transfer-encoding is quoted-printable.
			 * 
			 * $pos = strpos($str,'20.000')-2;		
			 * 	echo $str[$pos].' = '.ord($str[$pos]);
					
			$str = str_replace(chr(128), "&euro;", $str);
			$str = str_replace(chr(133), ".", $str);
			$str = str_replace(chr(146), "'", $str);			
			$str = str_replace(chr(147), '"', $str);
			$str = str_replace(chr(148), '"', $str);		 */	
		}
		if (function_exists('iconv') && $part_charset != '' && $part_charset != $charset) {
	
			if(strtolower($part_charset)=='us-ascii')
			{
				$part_charset='ISO-8859-1';
			}
			if($converted = @iconv($part_charset, $charset.'//IGNORE', $str))
			{			
				return str_replace($part_charset, $charset, $converted);
			}
		}
		return ($str);
	}

	function f($name) {
		$value = isset ($this->message[$name]) ? $this->message[$name] : false;
		return $value;
	}

	/**
		* Get the delimiter that is used to delimit Mailbox names
		*
		* @access public
		* @return mixed The delimiter or false on failure
		*/

	function get_mailbox_delimiter() {
		$list = imap_getmailboxes($this->conn, "{".$this->connectstring."}", '%');
		if (is_array($list)) {
			$folder = array_shift($list);
			if (strlen($folder->delimiter) > 0) {
				return $folder->delimiter;
			}
		}
		return false;
	}

	/**
		* Check if the given mailbox root is valid and return it with the correct delimiter 
		*
		* @param $mbroot The Mailbox root. (eg. INBOX/)
		* @access public
		* @return mixed Mailbox root with delimiter or false on failure
		*/

	function check_mbroot($mbroot) {
		$mbroot = trim($mbroot);

		$list = imap_getmailboxes($this->conn, "{".$this->connectstring."}", '%');
		if (is_array($list)) {
			while ($folder = array_shift($list)) {
				if (!isset ($delimiter) && strlen($folder->delimiter) > 0) {
					$delimiter = $folder->delimiter;
					if (substr($mbroot, -1) == $delimiter) {
						$mbroot = substr($mbroot, 0, -1);
					}
				}

				if (str_replace("{".$this->connectstring."}", "", $folder->name) == $mbroot) {
					return $mbroot.$delimiter;
				}
			}
		}
		return false;
	}

	/**
		* Return all mailboxes in an array with name, delimiter and attributes
		*
		* @param String $mailbox_root The mailbox root
		* @param bool $name_only Return only the mailbox names in the result
		* @access public
		* @return array The mailboxes
		*/
	function get_mailboxes($mailbox_root = '', $name_only = false) {
		
		//echo $mailbox_root.' -- > ';
		
		//echo '>"'.$mailbox_root.'"   ->   "'.$this->utf7_encode($mailbox_root).'"<hr />';
		
		$this->mailboxes = array ();
		$list = imap_getmailboxes($this->conn, "{".$this->connectstring."}", $mailbox_root.'%');
		if (is_array($list)) {
			foreach ($list as $value) {
				if (substr($value->name, -1) != $value->delimiter && strlen($value->delimiter) > 0) {
						if ($name_only) {
						$this->mailboxes[] = str_replace("{".$this->connectstring."}", "", $value->name);
					} else {
						
						$mailbox['name'] = str_replace("{".$this->connectstring."}", "", $value->name);
						$mailbox['delimiter'] = $value->delimiter;
						$mailbox['attributes'] = $value->attributes;
						$this->mailboxes[] = $mailbox;
						if (!($mailbox['attributes'] & LATT_NOINFERIORS)) {
							$this->mailboxes = array_merge($this->mailboxes, $this->get_mailboxes($mailbox['name'].$mailbox['delimiter']));
						}
					}
				}
			}
		}

		return $this->mailboxes;
	}

	/**
		* Return all subscribed mailboxes in an array with name, delimiter and attributes
		*
		* @param String $mailbox_root The mailbox root
		* @param bool $name_only Return only the mailbox names in the result
		* @access public
		* @return array The subscribed mailboxes
		*/
	function get_subscribed($mailbox_root = '', $name_only = false) {
		$this->mailboxes = array ();
		$list = imap_getsubscribed($this->conn, "{".$this->connectstring."}", $mailbox_root.'%');
		if (is_array($list)) {
			foreach ($list as $value) {
				if (substr($value->name, -1) != $value->delimiter && strlen($value->delimiter) > 0) {
				$mailbox['name'] = str_replace("{".$this->connectstring."}", "",$value->name);
				
					$mailbox['delimiter'] = $value->delimiter;
					$mailbox['attributes'] = $value->attributes;
						
					if ($name_only) {
						$this->mailboxes[] = $mailbox['name'];
					} else {						
						$this->mailboxes[] = $mailbox;						
					}
					
					if (!($mailbox['attributes'] & LATT_NOINFERIORS)) {
						$this->mailboxes = array_merge($this->mailboxes, $this->get_subscribed($mailbox['name'].$mailbox['delimiter'], $name_only));
					}
				}
			}
		}

		return $this->mailboxes;
	}

	/**
		* Check if a mailbox is subscribed
		*
		* @param String $name The name of the mailbox
		* @param String $mailbox_root The mailbox root
		* @access public
		* @return bool
		*/
	function is_subscribed($name, $mailbox_root) {
		$this->get_subscribed($mailbox_root);
		for ($i = 0; $i < count($this->mailboxes); $i ++) {
			if ($this->mailboxes[$i]['name'] == $name) {
				return true;
			}
		}
		return false;
	}

	/**
		* Subscribe a mailbox
		*
		* @param String $name The name of the mailbox
		* @access public
		* @return bool True on success
		*/
	function subscribe($name) {
		return imap_subscribe($this->conn, "{".$this->connectstring."}".$name);
	}

	/**
		* Unsubscribe a mailbox
		*
		* @param String $name The name of the mailbox
		* @access public
		* @return bool True on success
		*/
	function unsubscribe($name) {
		return imap_unsubscribe($this->conn, "{".$this->connectstring."}".$name);
	}

	/**
		* Delete a mailbox
		*
		* @param String $name The name of the mailbox
		* @param String $mailbox_root The mailbox root
		* @access public
		* @return bool True on success
		*/
	function delete_folder($name, $mailbox_root) {
		if ($this->is_subscribed($name, $mailbox_root)) {
			if ($this->unsubscribe($name)) {
				return imap_deletemailbox($this->conn, "{".$this->connectstring."}".$name);
			}
			return false;
		} else {
			return imap_deletemailbox($this->conn, "{".$this->connectstring."}".$name);
		}
	}

	/**
		* Create a mailbox
		*
		* @param String $name The name of the mailbox
		* @access public
		* @return bool True on success
		*/
	function create_folder($name, $delimiter='.') {
	
		//echo imap_utf7_encode(utf8_decode($name)).' -> '.$this->utf7_encode($name);
		
		if (imap_createmailbox($this->conn, "{".$this->connectstring."}".$name)) {
			return $this->subscribe($name);
		}
	}

	/**
		* Rename a mailbox
		*
		* @param String $old_name The current name of the mailbox
		* @param String $new_name The new name of the mailbox
		* @access public
		* @return bool True on success
		*/
	function rename_folder($old_name, $new_name) {
				
		$children = $this->get_mailboxes($old_name);		
		if(imap_renamemailbox($this->conn, "{".$this->connectstring."}".$old_name, "{".$this->connectstring."}".$new_name))
		{
			foreach($children as $old_child)
			{
				$old_child = $old_child['name'];
				$pos = strpos($old_child, $old_name);
				$new_child = substr_replace($old_child, $new_name, $pos, strlen($old_name));
				//echo 'Renaming: '.$old_child.' to: '.$new_child.' <br />';
				if(!$this->unsubscribe($old_child))
				{
					return false;
				}elseif(!$this->subscribe($new_child))
				{
					if(imap_renamemailbox($this->conn, "{".$this->connectstring."}".$old_child, "{".$this->connectstring."}".$new_child))
					{
						if(!$this->subscribe($new_child))
						{
							return false;
						}
					}
				}
			}
		}
		return true;		
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
		$messageset = implode(",", $messages);
		if (imap_mail_move($this->conn, $messageset, $folder, CP_UID)) {
			return imap_expunge($this->conn);
		}
		return false;
	}

	/**
		* Append a message to a maibox
		*
		* @param String $mailbox The mailbox where the message needs to go
		* @param String $body The message body
		* @param Int $flags The message flags (Unseen, Replied, flagged)
		* @access public
		* @return bool True on success
		*/
	function append_message($mailbox, $body, $flags = "") {
		if (@ imap_append($this->conn, "{".$this->connectstring."}".$mailbox, $body, $flags)) {
			return true;
		} else {
			return false;
		}
	}

	/**
		* Set message flags
		*
		* @param String $mailbox The mailbox where the message is
		* @param String $uid_array An array of message UID's
		* @param Int $flags The message flags (Unseen, Replied, flagged)
		* @param String $action If action is reset the given flags will be cleared
		* @access public
		* @return bool True on success
		*/
	function set_message_flag($mailbox = "INBOX", $uid_array, $flags, $action = "") {
		if ($mailbox == $this->mailbox || $this->reopen($mailbox)) {
			$msgno_set = implode(",", $uid_array);

			if ($action == "reset") {
				if (imap_clearflag_full($this->conn, $msgno_set, $flags, ST_UID)) {
					return true;
				} else {
					return false;
				}
			} else {
				if (imap_setflag_full($this->conn, $msgno_set, $flags, ST_UID)) {
					return true;
				} else {
					return false;
				}
			}
		} else {
			return false;
		}
	}

	/**
		* Re-open a mailbox
		*
		* @param String $mailbox The mailbox to open
		* @param Int $flags Connection flags see imap_open() PHP docs.
		* @access public
		* @return bool True on success
		*/
	function reopen($mailbox = "INBOX", $flags = "") {
		if (imap_reopen($this->conn, "{".$this->connectstring."}".$mailbox, $flags)) {
			$this->mailbox = $mailbox;
			return true;
		} else {
			return false;
		}
	}

	/**
		* Get mailbox message info and put it in $this->mailbox_info.
		*
		* @access public
		* @return bool True on success
		*/
	function mailbox_info() {
		$info = imap_mailboxmsginfo($this->conn);
		if ($info) {
			$this->mailbox_info = array ();
			$this->mailbox_info["date"] = $info->Date;
			$this->mailbox_info["driver"] = $info->Driver;
			$this->mailbox_info["mailbox"] = $info->Mailbox;
			$this->mailbox_info["nmsgs"] = $info->Nmsgs;
			$this->mailbox_info["recent"] = $info->Recent;
			$this->mailbox_info["size"] = $info->Size;
			return true;
		} else {
			return false;
		}
	}
	


}
