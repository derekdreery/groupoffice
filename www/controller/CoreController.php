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

		if ($GLOBALS['GO_SECURITY']->logged_in())
			$GLOBALS['GO_MODULES']->callModuleMethod('initUser', array($GLOBALS['GO_SECURITY']->user_id));

		//$config_file = $GO_CONFIG->get_config_file();
		if (empty($GLOBALS['GO_CONFIG']->db_user)) {
			header('Location: install/');
			exit();
		}

		//Redirect to correct login url if a force_login_url is set. Useful to force ssl
		if ($GLOBALS['GO_CONFIG']->force_login_url && strpos($GLOBALS['GO_CONFIG']->full_url, $GLOBALS['GO_CONFIG']->force_login_url) === false) {
			unset(GO::session()->values['full_url']);
			header('Location: ' . $GLOBALS['GO_CONFIG']->force_login_url);
			exit();
		}

		$mtime = $GLOBALS['GO_CONFIG']->get_setting('upgrade_mtime');

		if ($mtime != $GLOBALS['GO_CONFIG']->mtime) {
			
			global $lang;
			
			if ($GLOBALS['GO_SECURITY']->logged_in())
				$GLOBALS['GO_SECURITY']->logout();

			echo '<html><head><style>body{font-family:arial;}</style></head><body>';
			echo '<h1>' . $lang['common']['running_sys_upgrade'] . '</h1><p>' . $lang['common']['sys_upgrade_text'] . '</p>';
			require($GLOBALS['GO_CONFIG']->root_path . 'install/upgrade.php');
			echo '<a href="#" onclick="document.location.reload();">' . $lang['common']['click_here_to_contine'] . '</a>';
			echo '</body></html>';
			exit();
		}


//will do autologin here before theme is loaded.
		try {
			$GLOBALS['GO_SECURITY']->logged_in();
		} catch (Exception $e) {
			
		}

		$this->render('init');
	}

	protected function actionLogout() {
		$GLOBALS['GO_SECURITY']->logout();
		if (isset($_COOKIE['GO_FULLSCREEN']) && $_COOKIE['GO_FULLSCREEN'] == '1') {
			?>
			<script type="text/javascript">
				window.close();
			</script>
			<?php

			exit();
		} else {
			header('Location: ' . $GLOBALS['GO_CONFIG']->host);
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

		foreach ($GLOBALS['GO_MODULES']->getAll() as $module) {
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

		go_debug('Adding stylesheet: ' . $path);

		$this->stylesheets[] = $path;
	}

//	function loadModuleStylesheets($derrived_theme=false) {
//		global $GO_MODULES;
//
//		foreach ($GLOBALS['GO_MODULES']->modules as $module) {
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
		foreach ($GLOBALS['GO_MODULES']->modules as $module) {
			$mods.=$module['id'];
		}

		$hash = md5($GLOBALS['GO_CONFIG']->file_storage_path . $GLOBALS['GO_CONFIG']->host . $GLOBALS['GO_CONFIG']->mtime . $mods);

		$relpath = 'cache/' . $hash . '-' . GO::view() . '-style.css';
		$cssfile = $GLOBALS['GO_CONFIG']->file_storage_path . $relpath;

		if (!file_exists($cssfile) || $GLOBALS['GO_CONFIG']->debug) {

			File::mkdir($GLOBALS['GO_CONFIG']->file_storage_path . 'cache');

			$fp = fopen($cssfile, 'w+');
			foreach ($this->stylesheets as $s) {

				$baseurl = str_replace($GLOBALS['GO_CONFIG']->root_path, $GLOBALS['GO_CONFIG']->host, dirname($s)) . '/';

				fputs($fp, $this->replaceUrl(file_get_contents($s), $baseurl));
			}
			fclose($fp);
		}

		$cssurl = $GLOBALS['GO_CONFIG']->host . 'compress.php?file=' . basename($relpath);
		echo '<link href="' . $cssurl . '" type="text/css" rel="stylesheet" />';
	}

}