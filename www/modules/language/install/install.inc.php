<?php
if(!is_dir($GLOBALS['GO_CONFIG']->file_storage_path.'users/admin/language')){
	mkdir($GLOBALS['GO_CONFIG']->file_storage_path.'users/admin/language', 0755, true);
	copy(dirname(__FILE__).'/en.inc.php', $GLOBALS['GO_CONFIG']->file_storage_path.'users/admin/language/en.inc.php');
	copy(dirname(__FILE__).'/en.js', $GLOBALS['GO_CONFIG']->file_storage_path.'users/admin/language/en.js');
}
copy(dirname(__FILE__).'/en.zip', $GLOBALS['GO_CONFIG']->file_storage_path.'users/admin/language/Language strings.zip');

