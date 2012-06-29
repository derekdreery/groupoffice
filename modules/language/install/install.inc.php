<?php
if(!is_dir($GO_CONFIG->file_storage_path.'users/admin/language')){
	mkdir($GO_CONFIG->file_storage_path.'users/admin/language', 0755, true);
	copy(dirname(__FILE__).'/en.inc.php', $GO_CONFIG->file_storage_path.'users/admin/language/en.inc.php');
	copy(dirname(__FILE__).'/en.js', $GO_CONFIG->file_storage_path.'users/admin/language/en.js');
}
copy(dirname(__FILE__).'/en.zip', $GO_CONFIG->file_storage_path.'users/admin/language/Language strings.zip');

