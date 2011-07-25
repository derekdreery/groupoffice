<?php

/**
 * TODO
 * 
 * The whole init process of Group-Office has to be remodelled.
 * The default_scripts.inc.php file is ugly and bad design. Instead all init
 * views in modules should register client scripts and css files.
 */
class GO_Controller_Core extends GO_Base_Controller_AbstractController {

	protected $defaultAction = 'Init';

	protected function actionInit() {

		GO_Base_Observable::cacheListeners();

		if (GO::security()->logged_in())
			GO::modules()->callModuleMethod('initUser', array(GO::security()->user_id));

		//$config_file = $GO_CONFIG->get_config_file();
		if (empty(GO::config()->db_user)) {
			header('Location: install/');
			exit();
		}

		//Redirect to correct login url if a force_login_url is set. Useful to force ssl
		if (GO::config()->force_login_url && strpos(GO::config()->full_url, GO::config()->force_login_url) === false) {
			unset(GO::session()->full_url);
			header('Location: ' . GO::config()->force_login_url);
			exit();
		}

		$mtime = GO::config()->get_setting('upgrade_mtime');

		if ($mtime != GO::config()->mtime) {
			
			global $lang;
			
			if (GO::security()->logged_in())
				GO::security()->logout();

			echo '<html><head><style>body{font-family:arial;}</style></head><body>';
			echo '<h1>' . $lang['common']['running_sys_upgrade'] . '</h1><p>' . $lang['common']['sys_upgrade_text'] . '</p>';
			require($GO_CONFIG->root_path . 'install/upgrade.php');
			echo '<a href="#" onclick="document.location.reload();">' . $lang['common']['click_here_to_contine'] . '</a>';
			echo '</body></html>';
			exit();
		}


//will do autologin here before theme is loaded.
		try {
			GO::security()->logged_in();
		} catch (Exception $e) {
			
		}

		$this->render('init');
	}

	protected function actionLogout() {
		GO::security()->logout();
		if (isset($_COOKIE['GO_FULLSCREEN']) && $_COOKIE['GO_FULLSCREEN'] == '1') {
			?>
			<script type="text/javascript">
				window.close();
			</script>
			<?php

			exit();
		} else {
			header('Location: ' . GO::config()->host);
			exit();
		}
	}

	/**
	 * Todo replace compress.php with this action
	 */
	protected function actionCompress() {
		
	}

	private $clientScripts = array();

	protected function registerClientScript($url, $type='url') {
		$this->clientScripts[] = array($type, $url);
	}

	private function replaceUrl($css, $baseurl) {
		return preg_replace('/url[\s]*\(([^\)]*)\)/ieU', "self::replaceUrlCallback('$1', \$baseurl)", $css);
	}

	public static function replaceUrlCallback($url, $baseurl) {
		return 'url(' . $baseurl . trim(stripslashes($url), '\'" ') . ')';
	}

	function loadModuleStylesheets($derrived_theme=false) {
		global $GO_MODULES;

		foreach (GO::modules()->getAll() as $module) {
			if (file_exists($module->path . 'themes/Default/style.css')) {
				$this->registerCssFile($module->path . 'themes/Default/style.css');
			}

			if (GO::view() != 'Default') {

				//todo
				if ($derrived_theme && file_exists($module['path'] . 'themes/' . $derrived_theme . '/style.css')) {
					$this->registerCssFile($module['path'] . 'themes/' . $derrived_theme . '/style.css');
				}
				if (file_exists($module['path'] . 'themes/' . $this->theme . '/style.css')) {
					$this->registerCssFile('themes/' . $this->theme . '/style.css');
				}
			}
		}
	}

	protected function registerCssFile($path) {

		//echo '<!-- '.$path.' -->'."\n";

		GO::debug('Adding stylesheet: ' . $path);

		$this->stylesheets[] = $path;
	}

//	function loadModuleStylesheets($derrived_theme=false) {
//		global $GO_MODULES;
//
//		foreach (GO::modules()->modules as $module) {
//			if (file_exists($module['path'] . 'themes/Default/style.css')) {
//				$this->add_stylesheet($module['path'] . 'themes/Default/style.css');
//			}
//
//			if ($this->theme != 'Default') {
//				if ($derrived_theme && file_exists($module['path'] . 'themes/' . $derrived_theme . '/style.css')) {
//					$this->add_stylesheet($module['path'] . 'themes/' . $derrived_theme . '/style.css');
//				}
//				if (file_exists($module['path'] . 'themes/' . $this->theme . '/style.css')) {
//					$this->add_stylesheet($module['path'] . 'themes/' . $this->theme . '/style.css');
//				}
//			}
//		}
//	}

	public function getCachedCss() {
		global $GO_CONFIG, $GO_SECURITY, $GO_MODULES;

		$mods = '';
		foreach (GO::modules()->modules as $module) {
			$mods.=$module['id'];
		}

		$hash = md5(GO::config()->file_storage_path . GO::config()->host . GO::config()->mtime . $mods);

		$relpath = 'cache/' . $hash . '-' . GO::view() . '-style.css';
		$cssfile = GO::config()->file_storage_path . $relpath;

		if (!file_exists($cssfile) || GO::config()->debug) {

			File::mkdir(GO::config()->file_storage_path . 'cache');

			$fp = fopen($cssfile, 'w+');
			foreach ($this->stylesheets as $s) {

				$baseurl = str_replace(GO::config()->root_path, GO::config()->host, dirname($s)) . '/';

				fputs($fp, $this->replaceUrl(file_get_contents($s), $baseurl));
			}
			fclose($fp);
		}

		$cssurl = GO::config()->host . 'compress.php?file=' . basename($relpath);
		echo '<link href="' . $cssurl . '" type="text/css" rel="stylesheet" />';
	}

}