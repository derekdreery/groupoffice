<?php
if(!is_dir(GO::config()->file_storage_path.'users/admin/language')){
	mkdir(GO::config()->file_storage_path.'users/admin/language', 0755, true);
	copy(dirname(__FILE__).'/en.inc.php', GO::config()->file_storage_path.'users/admin/language/en.inc.php');
	copy(dirname(__FILE__).'/en.js', GO::config()->file_storage_path.'users/admin/language/en.js');
}
copy(dirname(__FILE__).'/en.zip', GO::config()->file_storage_path.'users/admin/language/Language strings.zip');

