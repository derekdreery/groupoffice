<?php

class GO_Web_Cache {

	private function replaceUrl($css, $baseurl) {
		return preg_replace('/url[\s]*\(([^\)]*)\)/ieU', "GO_Base_View_Web::replaceUrlCallback('$1', \$baseurl)", $css);
	}

	public static function replaceUrlCallback($url, $baseurl) {
		return 'url(' . $baseurl . trim(stripslashes($url), '\'" ') . ')';
	}

	function addStylesheet($path) {

		//echo '<!-- '.$path.' -->'."\n";

		go_debug('Adding stylesheet: ' . $path);

		$this->stylesheets[] = $path;
	}

	function loadModuleStylesheets($derrived_theme=false) {
		global $GO_MODULES;

		foreach (GO::modules()->modules as $module) {
			if (file_exists($module['path'] . 'themes/Default/style.css')) {
				$this->add_stylesheet($module['path'] . 'themes/Default/style.css');
			}

			if ($this->theme != 'Default') {
				if ($derrived_theme && file_exists($module['path'] . 'themes/' . $derrived_theme . '/style.css')) {
					$this->add_stylesheet($module['path'] . 'themes/' . $derrived_theme . '/style.css');
				}
				if (file_exists($module['path'] . 'themes/' . $this->theme . '/style.css')) {
					$this->add_stylesheet($module['path'] . 'themes/' . $this->theme . '/style.css');
				}
			}
		}
	}

	function get_cached_css() {
		global $GO_CONFIG, $GO_SECURITY, $GO_MODULES;

		$mods = '';
		foreach (GO::modules()->modules as $module) {
			$mods.=$module['id'];
		}

		$hash = md5(GO::config()->file_storage_path . GO::config()->host . GO::config()->mtime . $mods);

		$relpath = 'cache/' . $hash . '-' . $this->theme . '-style.css';
		$cssfile = GO::config()->file_storage_path . $relpath;

		if (!file_exists($cssfile) || GO::config()->debug) {

			File::mkdir(GO::config()->file_storage_path . 'cache');

			$fp = fopen($cssfile, 'w+');
			foreach ($this->stylesheets as $s) {

				$baseurl = str_replace(GO::config()->root_path, GO::config()->host, dirname($s)) . '/';

				fputs($fp, $this->replace_url(file_get_contents($s), $baseurl));
			}
			fclose($fp);
		}

		$cssurl = GO::config()->host . 'compress.php?file=' . basename($relpath);
		echo '<link href="' . $cssurl . '" type="text/css" rel="stylesheet" />';
	}

}