<?php
class modules {


	public function __on_load_listeners($events) {
		global $GO_CONFIG;

		$config_file = $GO_CONFIG->get_config_file();

		$config_dir = dirname($config_file);

		if(file_exists($config_dir.'/local_listeners.php'))
			require_once $config_dir.'/local_listeners.php';

	}
}