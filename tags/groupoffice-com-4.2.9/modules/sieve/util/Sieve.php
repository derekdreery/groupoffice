<?php

/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: sieve.class.inc.php 0000 2010-12-15 08:33:19Z wsmits $
 * @copyright Copyright Intermesh
 * @author Wesley Smits <wsmits@intermesh.nl>
 */
// make sure path_separator is defined
if (!defined('PATH_SEPARATOR')) {
	define('PATH_SEPARATOR', (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') ? ';' : ':');
}

$include_path = GO::config()->root_path . 'go/vendor/pear/' . PATH_SEPARATOR;
$include_path.= ini_get('include_path');

if (set_include_path($include_path) === false) {
	die("Fatal error: ini_set/set_include_path does not work.");
}

require_once 'Net/Sieve.php';


define('SIEVE_ERROR_CONNECTION', 1);
define('SIEVE_ERROR_LOGIN', 2);
define('SIEVE_ERROR_NOT_EXISTS', 3);	// script not exists
define('SIEVE_ERROR_INSTALL', 4);	   // script installation
define('SIEVE_ERROR_ACTIVATE', 5);	  // script activation
define('SIEVE_ERROR_DELETE', 6);		// script deletion
define('SIEVE_ERROR_INTERNAL', 7);	  // internal error
define('SIEVE_ERROR_DEACTIVATE', 8);	// script activation
define('SIEVE_ERROR_OTHER', 255);	   // other/unknown error

class GO_Sieve_Util_Sieve {

	private $sieve;				 // Net_Sieve object
	private $error = false;		 // error flag
	private $list = array();		// scripts list
	public $script;				 // go_sieve_script object
	public $current;				// name of currently loaded script
	private $disabled;			  // array of disabled extensions
	
	private $_PEAR;

	/**
	 * Object constructor
	 *
	 * @param string  Username (for managesieve login)
	 * @param string  Password (for managesieve login)
	 * @param string  Managesieve server hostname/address
	 * @param string  Managesieve server port number
	 * @param string  Managesieve authentication method
	 * @param boolean Enable/disable TLS use
	 * @param array   Disabled extensions
	 * @param boolean Enable/disable debugging
	 */

	public function __construct() {
		$this->sieve = new Net_Sieve();
		$this->sieve->setDebug(true, array($this, "debug_handler"));
		$this->_PEAR = new PEAR();
	}

	private function rewrite_host($host) {
		if (isset(GO::config()->sieve_rewrite_hosts)) {

			$maps = explode(',', GO::config()->sieve_rewrite_hosts);

			foreach ($maps as $map) {
				$pair = explode('=', $map);

				if ($pair[0] == $host && isset($pair[1]))
					return $pair[1];
			}
		}

		return $host;
	}

	public function connect($username, $password='', $host='localhost', $port=2000, $auth_type=null, $usetls=true, $disabled=array(), $debug=false) {

		$host = $this->rewrite_host($host);

		GO::debug("sieve::connect($username, ***, $host, $port, $auth_type, $usetls)");
		
		if ($this->_PEAR->isError($this->sieve->connect($host, $port, NULL, $usetls))) {
			return $this->_set_error(SIEVE_ERROR_CONNECTION);
		}

		if ($this->_PEAR->isError($this->sieve->login($username, $password,
								$auth_type ? strtoupper($auth_type) : null))
		) {
			return $this->_set_error(SIEVE_ERROR_LOGIN);
		}
		$this->disabled = $disabled;

		return true;
	}

	public function __destruct() {
		$this->sieve->disconnect();
	}

	/**
	 * Getter for error code
	 */
	public function error() {
		return $this->error ? $this->error : false;
	}

	/**
	 * Saves current script into server
	 */
	public function save($name = null) {
		if (!$this->sieve)
			return $this->_set_error(SIEVE_ERROR_INTERNAL);

		if (!$this->script)
			return $this->_set_error(SIEVE_ERROR_INTERNAL);

		if (!$name)
			$name = $this->current;

		$script = $this->script->as_text();
		
		if (!$script)
			$script = '/* empty script */';

		$res = $this->sieve->installScript($name, $script, true);
		if ($this->_PEAR->isError($res)){
			
			GO::debug("ERROR: ".$res);
			
			return $this->_set_error(SIEVE_ERROR_INSTALL);
		}

		return true;
	}

	/**
	 * Saves text script into server
	 */
	public function save_script($name, $content = null) {
		if (!$this->sieve)
			return $this->_set_error(SIEVE_ERROR_INTERNAL);

		if (!$content)
			$content = '/* empty script */';
		
		$res = $this->sieve->installScript($name, $content);
		if ($this->_PEAR->isError($res)) {
			return $this->_set_error(SIEVE_ERROR_INSTALL);
		}

		return true;
	}

	/**
	 * Creates a script for the spam filter
	 */
	public function createSpamScript($test) {
		$test['test'] = 'X-Spam-Flag';
		$test['not'] = 'false';
		$test['type'] = 'contains';
		$test['arg'] = 'YES';
		$test['arg1'] = '';
		$test['arg2'] = '';

		return $test;
	}

	/**
	 * Activates specified script
	 */
	public function activate($name = null) {
		if (!$this->sieve)
			return $this->_set_error(SIEVE_ERROR_INTERNAL);

		if (!$name)
			$name = $this->current;

		if ($this->_PEAR->isError($this->sieve->setActive($name)))
			return $this->_set_error(SIEVE_ERROR_ACTIVATE);

		return true;
	}

	/**
	 * De-activates specified script
	 */
	public function deactivate() {
		if (!$this->sieve)
			return $this->_set_error(SIEVE_ERROR_INTERNAL);

		if ($this->_PEAR->isError($this->sieve->setActive('')))
			return $this->_set_error(SIEVE_ERROR_DEACTIVATE);

		return true;
	}

	/**
	 * Removes specified script
	 */
	public function remove($name = null) {
		if (!$this->sieve)
			return $this->_set_error(SIEVE_ERROR_INTERNAL);

		if (!$name)
			$name = $this->current;

		// script must be deactivated first
		if ($name == $this->sieve->getActive())
			if ($this->_PEAR->isError($this->sieve->setActive('')))
				return $this->_set_error(SIEVE_ERROR_DELETE);

		if ($this->_PEAR->isError($this->sieve->removeScript($name)))
			return $this->_set_error(SIEVE_ERROR_DELETE);

		if ($name == $this->current)
			$this->current = null;

		return true;
	}

	/**
	 * Gets list of supported by server Sieve extensions
	 */
	public function get_extensions() {
		if (!$this->sieve)
			return $this->_set_error(SIEVE_ERROR_INTERNAL);

		$ext = $this->sieve->getExtensions();
		// we're working on lower-cased names
		$ext = array_map('strtolower', (array) $ext);

		if ($this->script) {
			$supported = $this->script->get_extensions();
			foreach ($ext as $idx => $ext_name)
				if (!in_array($ext_name, $supported))
					unset($ext[$idx]);
		}

		return array_values($ext);
	}

	/**
	 * Gets list of scripts from server
	 */
	public function get_scripts() {
		if (!$this->list) {

			if (!$this->sieve)
				return $this->_set_error(SIEVE_ERROR_INTERNAL);

			$this->list = $this->sieve->listScripts();

			if ($this->_PEAR->isError($this->list))
				return $this->_set_error(SIEVE_ERROR_OTHER);
		}

		return $this->list;
	}

	/**
	 * Returns active script name
	 */
	public function get_active($accountId) {
		$aliasEmails = array();
		$aliasesStmt = GO_Email_Model_Alias::model()->findByAttribute('account_id',$accountId);
		while ($aliasModel = $aliasesStmt->fetch())
			$aliasEmails[] = $aliasModel->email;
		
		if (!$this->sieve)
			return $this->_set_error(SIEVE_ERROR_INTERNAL);

		$active = $this->sieve->getActive();
		if (!$active) {

			$content = "require [\"vacation\",\"fileinto\"];
# rule:[".GO::t('standardvacation','sieve')."]
if false # anyof (true)
{
\tvacation :days 3 :addresses [\"".implode('","',$aliasEmails)."\"] \"".GO::t('standardvacationmessage','sieve')."\";
\tstop;
}
# rule:[Spam]
if anyof (header :contains \"X-Spam-Flag\" \"YES\")
{
	fileinto \"Spam\";
}";
			$this->save_script('default', $content);
			$this->activate('default');
			$active = 'default';
		}
		return $active;
	}

	/**
	 * Loads script by name
	 */
	public function load($name) {
		if (!$this->sieve)
			return $this->_set_error(SIEVE_ERROR_INTERNAL);

		if ($this->current == $name)
			return true;

		$script = $this->sieve->getScript($name);
		
		if ($this->_PEAR->isError($script))
			return $this->_set_error(SIEVE_ERROR_OTHER);

		// try to parse from Roundcube format
		$this->script = $this->_parse($script);

		$this->current = $name;

		return true;
	}

	/**
	 * Loads script from text content
	 */
	public function load_script($script) {
		if (!$this->sieve)
			return $this->_set_error(SIEVE_ERROR_INTERNAL);

		// try to parse from Roundcube format
		$this->script = $this->_parse($script);
	}

	/**
	 * Creates go_sieve_script object from text script
	 */
	private function _parse($txt) {
		// try to parse from Roundcube format
		$script = new go_sieve_script($txt, $this->disabled);

		// ... else try to import from different formats
		if (empty($script->content)) {
			$script = $this->_import_rules($txt);
			$script = new go_sieve_script($script, $this->disabled);
		}

		// replace all elsif with if+stop, we support only ifs
    //
    // Stop rule is inserted client side now
    // 
//		foreach ($script->content as $idx => $rule) {
//			if (!isset($script->content[$idx + 1])
//					|| preg_match('/^else|elsif$/', $script->content[$idx + 1]['type'])) {
//				// 'stop' not found?
//				if (!preg_match('/^(stop|vacation)$/', $rule['actions'][count($rule['actions']) - 1]['type'])) {
//					$script->content[$idx]['actions'][] = array(
//						'type' => 'stop'
//					);
//				}
//			}
//		}

		return $script;
	}

	/**
	 * Gets specified script as text
	 */
	public function get_script($name) {
		if (!$this->sieve)
			return $this->_set_error(SIEVE_ERROR_INTERNAL);

		$content = $this->sieve->getScript($name);

		//var_dump($content);

		if ($this->_PEAR->isError($content))
			return $this->_set_error(SIEVE_ERROR_OTHER);

		return $content;
	}

	/**
	 * Creates empty script or copy of other script
	 */
	public function copy($name, $copy) {
		if (!$this->sieve)
			return $this->_set_error(SIEVE_ERROR_INTERNAL);

		if ($copy) {
			$content = $this->sieve->getScript($copy);

			if ($this->_PEAR->isError($content))
				return $this->_set_error(SIEVE_ERROR_OTHER);
		}

		return $this->save_script($name, $content);
	}

	private function _import_rules($script) {
		$i = 0;
		$name = array();
		$content = '';

		// Squirrelmail (Avelsieve)
		if ($tokens = preg_split('/(#START_SIEVE_RULE.*END_SIEVE_RULE)\n/', $script, -1, PREG_SPLIT_DELIM_CAPTURE)) {
			foreach ($tokens as $token) {
				if (preg_match('/^#START_SIEVE_RULE.*/', $token, $matches)) {
					$name[$i] = "unnamed rule " . ($i + 1);
					$content .= "# rule:[" . $name[$i] . "]\n";
				} elseif (isset($name[$i])) {
					// This preg_replace is added because I've found some Avelsieve scripts
					// with rules containing "if" here. I'm not sure it was working
					// before without this or not.
					$token = preg_replace('/^if\s+/', '', trim($token));
					$content .= "if $token\n";
					$i++;
				}
			}
		}
		// Horde (INGO)
		else if ($tokens = preg_split('/(# .+)\r?\n/i', $script, -1, PREG_SPLIT_DELIM_CAPTURE)) {
			foreach ($tokens as $token) {
				if (preg_match('/^# (.+)/i', $token, $matches)) {
					$name[$i] = $matches[1];
					$content .= "# rule:[" . $name[$i] . "]\n";
				} elseif (isset($name[$i])) {
					$token = str_replace(":comparator \"i;ascii-casemap\" ", "", $token);
					$content .= $token . "\n";
					$i++;
				}
			}
		}

		return $content;
	}

	private function _set_error($error) {
		$this->error = $error;
		GO::debug("SIEVE ERROR: ".$error);
		return false;
	}

	/**
	 * This is our own debug handler for connection
	 */
	public function debug_handler(&$sieve, $message) {
		//write_log('sieve', preg_replace('/\r\n$/', '', $message));
//		if (isset(GO::config()))
			GO::debug("SIEVE DEBUG: ".$message);
	}

}

class go_sieve_script {

	public $content = array();	  // script rules array
	private $supported = array(// extensions supported by class
		'fileinto',
		'reject',
		'ereject',
		'copy', // RFC3894
		'vacation', // RFC5230
		'date',
		'setflag'
			// TODO: (most wanted first) body, imapflags, notify, regex
	);

	/**
	 * Object constructor
	 *
	 * @param  string  Script's text content
	 * @param  array   Disabled extensions
	 */
	public function __construct($script, $disabled=NULL) {
		if (!empty($disabled))
			foreach ($disabled as $ext)
				if (($idx = array_search($ext, $this->supported)) !== false)
					unset($this->supported[$idx]);

		$this->content = $this->_parse_text($script);
	}

	/**
	 * Adds script contents as text to the script array (at the end)
	 *
	 * @param    string    Text script contents
	 */
	public function add_text($script) {
		$content = $this->_parse_text($script);
		$result = false;

		// check existsing script rules names
		foreach ($this->content as $idx => $elem) {
			$names[$elem['name']] = $idx;
		}

		foreach ($content as $elem) {
			if (!isset($names[$elem['name']])) {
				array_push($this->content, $elem);
				$result = true;
			}
		}

		return $result;
	}

	/**
	 * Adds rule to the script (at the end)
	 *
	 * @param string Rule name
	 * @param array  Rule content (as array)
	 */
	public function add_rule($content) {
		// TODO: check this->supported
		array_push($this->content, $content);
		return sizeof($this->content) - 1;
	}

	public function delete_rule($index) {
		if (isset($this->content[$index])) {
			unset($this->content[$index]);
			return true;
		}
		return false;
	}

	public function size() {
		return sizeof($this->content);
	}

	public function update_rule($index, $content) {
		// TODO: check this->supported
		if ($this->content[$index]) {
			$this->content[$index] = $content;
			return $index;
		}
		return false;
	}

	/**
	 * Returns script as text
	 */
	public function as_text() {
		$script = '';
		$exts = array();
		$idx = 0;

		// rules
		foreach ($this->content as $rule) {
			$extension = '';
			$tests = array();
			$i = 0;

			// header
			$script .= '# rule:[' . $rule['name'] . "]\n";

			// constraints expressions
			foreach ($rule['tests'] as $test) {
				$tests[$i] = '';
				switch ($test['test']) {
					case 'size':
						$tests[$i] .= ( $test['not'] ? 'not ' : '');
						$tests[$i] .= 'size :' . ($test['type'] == 'under' ? 'under ' : 'over ') . $test['arg'];
						break;
					case 'true':
						$tests[$i] .= ( $test['not'] ? 'not true' : 'true');
						break;
					case 'exists':
						$tests[$i] .= ( $test['not'] ? 'not ' : '');
						if (is_array($test['arg']))
							$tests[$i] .= 'exists ["' . implode('", "', $this->_escape_string($test['arg'])) . '"]';
						else
							$tests[$i] .= 'exists "' . $this->_escape_string($test['arg']) . '"';
						break;
					case 'header':
						$tests[$i] .= ( $test['not'] ? 'not ' : '');
						$tests[$i] .= 'header :' . $test['type'];
						if (is_array($test['arg1']))
							$tests[$i] .= ' ["' . implode('", "', $this->_escape_string($test['arg1'])) . '"]';
						else
							$tests[$i] .= ' "' . $this->_escape_string($test['arg1']) . '"';
						if (is_array($test['arg2']))
							$tests[$i] .= ' ["' . implode('", "', $this->_escape_string($test['arg2'])) . '"]';
						else
							$tests[$i] .= ' "' . $this->_escape_string($test['arg2']) . '"';
						break;
				}
				$i++;
			}

//          $script .= ($idx>0 ? 'els' : '').($rule['join'] ? 'if allof (' : 'if anyof (');
			// disabled rule: if false #....
			$script .= 'if' . ($rule['disabled'] ? ' false #' : '');
			$script .= $rule['join'] ? ' allof (' : ' anyof (';
			if (sizeof($tests) > 1)
				$script .= implode(", ", $tests);
			else if (sizeof($tests))
				$script .= $tests[0];
			else
				$script .= 'true';
			$script .= ")\n{\n";

			// action(s)
			foreach ($rule['actions'] as $action) {
				switch ($action['type']) {
					case 'set_read':
						$script .= "\tsetflag \"\\\\seen\";\n";
						array_push($exts, 'imap4flags');
						break;
					case 'fileinto':
						array_push($exts, 'fileinto');
						$script .= "\tfileinto ";
						if ($action['copy']) {
							$script .= ':copy ';
							array_push($exts, 'copy');
						}
						$script .= "\"" . $this->_escape_string($action['target']) . "\";\n";
						break;
					case 'redirect':
						$script .= "\tredirect ";
						if (!empty($action['copy'])) {
							$script .= ':copy ';
							array_push($exts, 'copy');
						}
						$script .= "\"" . $this->_escape_string($action['target']) . "\";\n";
						break;
					case 'reject':
					case 'ereject':
						array_push($exts, $action['type']);
						if (strpos($action['target'], "\n") !== false)
							$script .= "\t" . $action['type'] . " text:\n" . $action['target'] . "\n.\n;\n";
						else
							$script .= "\t" . $action['type'] . " \"" . $this->_escape_string($action['target']) . "\";\n";
						break;
					case 'keep':
					case 'discard':
					case 'stop':
						$script .= "\t" . $action['type'] . ";\n";
						break;
					case 'vacation':
						array_push($exts, 'vacation');
											
						$script .= "\tvacation";
						if (!empty($action['days']))
							$script .= " :days " . $action['days'];
						if (!empty($action['addresses']))
							$script .= " :addresses " . $this->_print_list($action['addresses']);
						if (!empty($action['subject']))
							$script .= " :subject \"" . $this->_escape_string($action['subject']) . "\"";
						if (!empty($action['handle']))
							$script .= " :handle \"" . $this->_escape_string($action['handle']) . "\"";
						if (!empty($action['from']))
							$script .= " :from \"" . $this->_escape_string($action['from']) . "\"";
						if (!empty($action['mime']))
							$script .= " :mime";
						
						if (strpos($action['reason'], "\n") !== false)
							$script .= " text:\n" . $action['reason'] . "\n.\n;\n";
						else
							$script .= " \"" . $this->_escape_string($action['reason']) . "\";\n";
						
						break;
				}
			}

			$script .= "}\n";
			$idx++;
		}

		// requires
		if (!empty($exts))
			$script = 'require ["' . implode('","', array_unique($exts)) . "\"];\n" . $script;

		return $script;
	}

	/**
	 * Returns script object
	 *
	 */
	public function as_array() {
		return $this->content;
	}

	/**
	 * Returns array of supported extensions
	 *
	 */
	public function get_extensions() {
		return array_values($this->supported);
	}

	/**
	 * Converts text script to rules array
	 *
	 * @param string Text script
	 */
	private function _parse_text($script) {
		$i = 0;
		$content = array();

		// remove C comments
		$script = preg_replace('|/\*.*?\*/|sm', '', $script);

		// tokenize rules
		if ($tokens = preg_split('/(# rule:\[.*\])\r?\n/', $script, -1, PREG_SPLIT_DELIM_CAPTURE)) {
			
			foreach ($tokens as $token) {
				if (preg_match('/^# rule:\[(.*)\]/', $token, $matches)) {
					$content[$i]['name'] = $matches[1];
				} else if (isset($content[$i]['name']) && sizeof($content[$i]) == 1) {
					
									
					if ($rule = $this->_tokenize_rule($token)) {
						$content[$i] = array_merge($content[$i], $rule);
						$i++;
					}
					else // unknown rule format
						unset($content[$i]);
				}
			}
		}

		return $content;
	}

	/**
	 * Convert text script fragment to rule object
	 *
	 * @param string Text rule
	 */
	private function _tokenize_rule($content) {
		$result = NULL;

		if (preg_match('/^(if|elsif|else)\s+((true|false|not\s+true|allof|anyof|exists|header|not|size)(.*))\s+\{(.*)\}$/sm',
						trim($content), $matches)) {

			$tests = trim($matches[2]);
GO::debug($tests);
			// disabled rule (false + comment): if false #.....
			if ($matches[3] == 'false') {
				$tests = preg_replace('/^false\s+#\s+/', '', $tests);
				$disabled = true;
			}
			else
				$disabled = false;

			list($tests, $join) = $this->_parse_tests($tests);
			$actions = $this->_parse_actions(trim($matches[5]));

			if ($tests && $actions)
				$result = array(
					'type' => $matches[1],
					'tests' => $tests,
					'actions' => $actions,
					'join' => $join,
					'disabled' => $disabled,
				);
		}

		return $result;
	}

	/**
	 * Parse body of actions section
	 *
	 * @param string Text body
	 * @return array Array of parsed action type/target pairs
	 */
	private function _parse_actions($content) {
		$result = NULL;

		// supported actions
		$patterns[] = '^\s*discard;';
		$patterns[] = '^\s*keep;';
		$patterns[] = '^\s*stop;';
		$patterns[] = '^\s*redirect\s+(.*?[^\\\]);';
		if (in_array('fileinto', $this->supported))
			$patterns[] = '^\s*fileinto\s+(.*?[^\\\]);';
		if (in_array('reject', $this->supported)) {
			$patterns[] = '^\s*reject\s+text:(.*)\n\.\n;';
			$patterns[] = '^\s*reject\s+(.*?[^\\\]);';
			$patterns[] = '^\s*ereject\s+text:(.*)\n\.\n;';
			$patterns[] = '^\s*ereject\s+(.*?[^\\\]);';
		}
		if (in_array('vacation', $this->supported))
			$patterns[] = '^\s*vacation\s+(.*?[^\\\]);';
		
		if (in_array('setflag', $this->supported))
			$patterns[] = '^\s*setflag\s+(.*?[^\\\]);';

		$pattern = '/(' . implode('$)|(', $patterns) . '$)/ms';

		// parse actions body
		if (preg_match_all($pattern, $content, $mm, PREG_SET_ORDER)) {			
			foreach ($mm as $m) {
				$content = trim($m[0]);
				
				if (preg_match('/^(discard|keep|stop)/', $content, $matches)) {
					$result[] = array('type' => $matches[1]);
				} else if (preg_match('/^fileinto/', $content)) {
					$target = $m[sizeof($m) - 1];
					$copy = false;
					if (preg_match('/^:copy\s+/', $target)) {
						$target = preg_replace('/^:copy\s+/', '', $target);
						$copy = true;
					}
					$result[] = array('type' => 'fileinto', 'copy' => $copy,
						'target' => $this->_parse_string($target));
				} else if (preg_match('/^redirect/', $content)) {
					$target = $m[sizeof($m) - 1];
					$copy = false;
					if (preg_match('/^:copy\s+/', $target)) {
						$target = preg_replace('/^:copy\s+/', '', $target);
						$copy = true;
					}
					$result[] = array('type' => 'redirect', 'copy' => $copy,
						'target' => $this->_parse_string($target));
				} else if (preg_match('/^(reject|ereject)\s+(.*);$/sm', $content, $matches)) {
					$result[] = array('type' => $matches[1], 'target' => $this->_parse_string($matches[2]));
				} else if (preg_match('/^vacation\s+(.*);$/sm', $content, $matches)) {
					$vacation = array('type' => 'vacation');

					if (preg_match('/:days\s+([0-9]+)/', $content, $vm)) {
						$vacation['days'] = $vm[1];
						$content = preg_replace('/:days\s+([0-9]+)/', '', $content);
					}
					if (preg_match('/:subject\s+"(.*?[^\\\])"/', $content, $vm)) {
						$vacation['subject'] = $vm[1];
						$content = preg_replace('/:subject\s+"(.*?[^\\\])"/', '', $content);
					}
					if (preg_match('/:addresses\s+\[(.*?[^\\\])\]/', $content, $vm)) {
						$vacation['addresses'] = $this->_parse_list($vm[1]);
						$content = preg_replace('/:addresses\s+\[(.*?[^\\\])\]/', '', $content);
					}
					if (preg_match('/:handle\s+"(.*?[^\\\])"/', $content, $vm)) {
						$vacation['handle'] = $vm[1];
						$content = preg_replace('/:handle\s+"(.*?[^\\\])"/', '', $content);
					}
					if (preg_match('/:from\s+"(.*?[^\\\])"/', $content, $vm)) {
						$vacation['from'] = $vm[1];
						$content = preg_replace('/:from\s+"(.*?[^\\\])"/', '', $content);
					}

					$content = preg_replace('/^vacation/', '', $content);
					$content = preg_replace('/;$/', '', $content);
					$content = trim($content);

					if (preg_match('/^:mime/', $content, $vm)) {
						$vacation['mime'] = true;
						$content = preg_replace('/^:mime/', '', $content);
					}

					$vacation['reason'] = $this->_parse_string($content);

					$result[] = $vacation;
				} else if (preg_match('/^setflag\s+(.*);$/sm', $content, $matches)) {			
					if (strtolower($matches[1])=='"\\\\seen"')
						$result[] = array('type' => 'set_read');
				}
			}
		}

		return $result;
	}

	/**
	 * Parse test/conditions section
	 *
	 * @param string Text
	 */
	private function _parse_tests($content) {
		$result = NULL;

		// lists
		if (preg_match('/^(allof|anyof)\s+\((.*)\)$/sm', $content, $matches)) {
			$content = $matches[2];
			$join = $matches[1] == 'allof' ? true : false;
		}
		else
			$join = false;

		// supported tests regular expressions
		// TODO: comparators, envelope
		$patterns[] = '(not\s+)?(exists)\s+\[(.*?[^\\\])\]';
		$patterns[] = '(not\s+)?(exists)\s+(".*?[^\\\]")';
		$patterns[] = '(not\s+)?(true)';
		$patterns[] = '(not\s+)?(size)\s+:(under|over)\s+([0-9]+[KGM]{0,1})';
		$patterns[] = '(not\s+)?(header)\s+:(contains|is|matches)\s+\[(.*?[^\\\]")\]\s+\[(.*?[^\\\]")\]';
		$patterns[] = '(not\s+)?(header)\s+:(contains|is|matches)\s+(".*?[^\\\]")\s+(".*?[^\\\]")';
		$patterns[] = '(not\s+)?(header)\s+:(contains|is|matches)\s+\[(.*?[^\\\]")\]\s+(".*?[^\\\]")';
		$patterns[] = '(not\s+)?(header)\s+:(contains|is|matches)\s+(".*?[^\\\]")\s+\[(.*?[^\\\]")\]';

		// join patterns...
		$pattern = '/(' . implode(')|(', $patterns) . ')/';

		// ...and parse tests list
		if (preg_match_all($pattern, $content, $matches, PREG_SET_ORDER)) {
			foreach ($matches as $match) {
				$size = sizeof($match);

				if (preg_match('/^(not\s+)?size/', $match[0])) {
					$result[] = array(
						'test' => 'size',
						'not' => $match[$size - 4] ? true : false,
						'type' => $match[$size - 2], // under/over
						'arg' => $match[$size - 1], // value
					);
				} else if (preg_match('/^(not\s+)?header/', $match[0])) {
					$result[] = array(
						'test' => 'header',
						'not' => $match[$size - 5] ? true : false,
						'type' => $match[$size - 3], // is/contains/matches
						'arg1' => $this->_parse_list($match[$size - 2]), // header(s)
						'arg2' => $this->_parse_list($match[$size - 1]), // string(s)
					);
				} else if (preg_match('/^(not\s+)?exists/', $match[0])) {
					$result[] = array(
						'test' => 'exists',
						'not' => $match[$size - 3] ? true : false,
						'arg' => $this->_parse_list($match[$size - 1]), // header(s)
					);
				} else if (preg_match('/^(not\s+)?true/', $match[0])) {
					$result[] = array(
						'test' => 'true',
						'not' => $match[$size - 2] ? true : false,
					);
				}
			}
		}

		return array($result, $join);
	}

	/**
	 * Parse string value
	 *
	 * @param string Text
	 */
	private function _parse_string($content) {
		$text = '';
		$content = trim($content);

		if (preg_match('/^text:(.*)\.$/sm', $content, $matches))
			$text = trim($matches[1]);
		else if (preg_match('/^"(.*)"$/', $content, $matches))
			$text = str_replace('\"', '"', $matches[1]);

		return $text;
	}

	/**
	 * Escape special chars in string value
	 *
	 * @param string Text
	 */
	private function _escape_string($content) {
		$replace['/"/'] = '\\"';

		if (is_array($content)) {
			for ($x = 0, $y = sizeof($content); $x < $y; $x++)
				$content[$x] = preg_replace(array_keys($replace),
								array_values($replace), $content[$x]);

			return $content;
		}
		else
			return preg_replace(array_keys($replace), array_values($replace), $content);
	}

	/**
	 * Parse string or list of strings to string or array of strings
	 *
	 * @param string Text
	 */
	private function _parse_list($content) {
		$result = array();

		for ($x = 0, $len = strlen($content); $x < $len; $x++) {
			switch ($content[$x]) {
				case '\\':
					$str .= $content[++$x];
					break;
				case '"':
					if (isset($str)) {
						$result[] = $str;
						unset($str);
					}
					else
						$str = '';
					break;
				default:
					if (isset($str))
						$str .= $content[$x];
					break;
			}
		}

		if (sizeof($result) > 1)
			return $result;
		else if (sizeof($result) == 1)
			return $result[0];
		else
			return NULL;
	}

	/**
	 * Convert array of elements to list of strings
	 *
	 * @param string Text
	 */
	private function _print_list($list) {
		$list = (array) $list;
		foreach ($list as $idx => $val)
			$list[$idx] = $this->_escape_string($val);

		return '["' . implode('","', $list) . '"]';
	}

}

?>