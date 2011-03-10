<?php
class language {

	public function __on_load_listeners($events){
		$events->add_listener('require_language_file', __FILE__, 'language', 'require_language_file');
	}
	public static function require_language_file($module, $language){
		global $GO_LANGUAGE, $GO_CONFIG, $lang;

		$file = $GO_CONFIG->file_storage_path.'users/admin/language/'.$language.'.inc.php';
		if(!file_exists($file)){
			$file = $GO_CONFIG->file_storage_path.'users/admin/language/en.inc.php';
		}
		include($file);
	}
}
?>