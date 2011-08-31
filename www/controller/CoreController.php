<?php

/**
 * TODO
 * 
 * The whole init process of Group-Office has to be remodelled.
 * The default_scripts.inc.php file is ugly and bad design. Instead all init
 * views in modules should register client scripts and css files.
 */
class GO_Core_Controller_Core extends GO_Base_Controller_AbstractController {

	protected $defaultAction = 'Init';
	
	
	private function loadInit(){
		GO_Base_Observable::cacheListeners();
		
		//when GO initializes modules need to perform their first run actions.
		unset(GO::session()->values['firstRunDone']);
		
		if (GO::user())
			$this->fireEvent('loadapplication', array(&$this));
	}

	public function actionInit() {
		
		$this->loadInit();
		$this->render('index');
	}
	
	public function actionSetView($params){
		GO::setView($params['view']);
		
		$this->redirect();
	}
	

	public function actionLogout() {
		
		GO::session()->logout();
		
		if (isset($_COOKIE['GO_FULLSCREEN']) && $_COOKIE['GO_FULLSCREEN'] == '1') {
			?>
			<script type="text/javascript">
				window.close();
			</script>
			<?php

			exit();
		} else {
			$this->redirect();
		}
	}
	
	public function actionLogin($params){
		
		$user = GO::session()->login($params['username'], $params['password']);
	
		$response['success'] = $user!=false;
		
		if(!$response['success']){
			GO::infolog("LOGIN FAILED for user: \"".$params['username']."\" from IP: ".$_SERVER['REMOTE_ADDR']);
			
			//sleep 3 seconds for slowing down brute force attacks
			sleep(3);
		}else
		{
			GO::infolog("LOGIN SUCCESS for user: \"".$params['username']."\" from IP: ".$_SERVER['REMOTE_ADDR']);		
			
			if(!empty($params['remind'])){
				
				$encUsername = GO_Base_Util_Crypt::encrypt($params['username']);
				if($encUsername)
					$encUsername=$params['username'];
				
				$encPassword = GO_Base_Util_Crypt::encrypt($params['password']);
				if($encPassword)
					$encPassword=$params['password'];
				
				$this->setCookie('GO_UN', $encUsername, 3600*24*30);
				$this->setCookie('GO_PW', $encPassword, 3600*24*30);
			}
		}
		
		if($this->isAjax())
			return $response;		
		else
			$this->redirect();
	}	
	
	
	public function actionSearch($params){
		
		$grid = new GO_Base_Provider_Grid();		    
		$stmt = GO_Base_Model_SearchCacheRecord::model()->find($grid->getDefaultParams());
		$grid->setStatement($stmt);
		
		$grid->formatColumn('iconCls', '"go-model-".$model->mode_name');		
		$grid->formatColumn('type', 'call_user_func(array($model->model_name, "model"))->localizedName');
		
		return $grid->getData();		
	}
	
	
	public function actionModelTypes(){
		$grid = new GO_Base_Provider_Grid();		    
		$stmt = GO_Base_Model_ModelType::model()->find($grid->getDefaultParams());
		$grid->setStatement($stmt);
		$grid->formatColumn('name', 'call_user_func(array($model->model_name,"model"))->localizedName');	
		
		return $grid->getData();		
	}
	

	/**
	 * Todo replace compress.php with this action
	 */
	public function actionCompress() {
		
	}

	private $clientScripts = array();

//	protected function registerClientScript($url, $type='url') {
//		$this->clientScripts[] = array($type, $url);
//	}
//
//	private function replaceUrl($css, $baseurl) {
//		return preg_replace('/url[\s]*\(([^\)]*)\)/ieU', "self::replaceUrlCallback('$1', \$baseurl)", $css);
//	}
//
//	public static function replaceUrlCallback($url, $baseurl) {
//		return 'url(' . $baseurl . trim(stripslashes($url), '\'" ') . ')';
//	}
//
//	function loadModuleStylesheets($derrived_theme=false) {
//		global $GO_MODULES;
//
//		foreach ($GLOBALS['GO_MODULES']->getAll() as $module) {
//			if (file_exists($module->path . 'themes/Default/style.css')) {
//				$this->registerCssFile($module->path . 'themes/Default/style.css');
//			}
//
//			if (GO::view() != 'Default') {
//
//				//todo
//				if ($derrived_theme && file_exists($module['path'] . 'themes/' . $derrived_theme . '/style.css')) {
//					$this->registerCssFile($module['path'] . 'themes/' . $derrived_theme . '/style.css');
//				}
//				if (file_exists($module['path'] . 'themes/' . $this->theme . '/style.css')) {
//					$this->registerCssFile('themes/' . $this->theme . '/style.css');
//				}
//			}
//		}
//	}
//
//	protected function registerCssFile($path) {
//
//		//echo '<!-- '.$path.' -->'."\n";
//
//		go_debug('Adding stylesheet: ' . $path);
//
//		$this->stylesheets[] = $path;
//	}
//
////	function loadModuleStylesheets($derrived_theme=false) {
////		global $GO_MODULES;
////
////		foreach ($GLOBALS['GO_MODULES']->modules as $module) {
////			if (file_exists($module['path'] . 'themes/Default/style.css')) {
////				$this->add_stylesheet($module['path'] . 'themes/Default/style.css');
////			}
////
////			if ($this->theme != 'Default') {
////				if ($derrived_theme && file_exists($module['path'] . 'themes/' . $derrived_theme . '/style.css')) {
////					$this->add_stylesheet($module['path'] . 'themes/' . $derrived_theme . '/style.css');
////				}
////				if (file_exists($module['path'] . 'themes/' . $this->theme . '/style.css')) {
////					$this->add_stylesheet($module['path'] . 'themes/' . $this->theme . '/style.css');
////				}
////			}
////		}
////	}
//
//	public function getCachedCss() {
//		global $GO_CONFIG, $GO_SECURITY, $GO_MODULES;
//
//		$mods = '';
//		foreach ($GLOBALS['GO_MODULES']->modules as $module) {
//			$mods.=$module['id'];
//		}
//
//		$hash = md5($GLOBALS['GO_CONFIG']->file_storage_path . $GLOBALS['GO_CONFIG']->host . $GLOBALS['GO_CONFIG']->mtime . $mods);
//
//		$relpath = 'cache/' . $hash . '-' . GO::view() . '-style.css';
//		$cssfile = $GLOBALS['GO_CONFIG']->file_storage_path . $relpath;
//
//		if (!file_exists($cssfile) || $GLOBALS['GO_CONFIG']->debug) {
//
//			File::mkdir($GLOBALS['GO_CONFIG']->file_storage_path . 'cache');
//
//			$fp = fopen($cssfile, 'w+');
//			foreach ($this->stylesheets as $s) {
//
//				$baseurl = str_replace($GLOBALS['GO_CONFIG']->root_path, $GLOBALS['GO_CONFIG']->host, dirname($s)) . '/';
//
//				fputs($fp, $this->replaceUrl(file_get_contents($s), $baseurl));
//			}
//			fclose($fp);
//		}
//
//		$cssurl = $GLOBALS['GO_CONFIG']->host . 'compress.php?file=' . basename($relpath);
//		echo '<link href="' . $cssurl . '" type="text/css" rel="stylesheet" />';
//	}
}