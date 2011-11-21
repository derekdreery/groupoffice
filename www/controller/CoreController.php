<?php

/**
 * TODO
 * 
 * The whole init process of Group-Office has to be remodelled.
 * The default_scripts.inc.php file is ugly and bad design. Instead all init
 * views in modules should register client scripts and css files.
 */
class GO_Core_Controller_Core extends GO_Base_Controller_AbstractController {
	
	public function actionGetNewAcl($params){
		$acl = new GO_Base_Model_Acl();
		$acl->user_id=isset($params['user_id']) ? $params['user_id'] : GO::user()->id;
		$acl->save();
		
		echo $acl->id;
	}

	public function actionLink($params) {

		$fromLinks = json_decode($_POST['fromLinks'], true);
		$toLinks = json_decode($_POST['toLinks'], true);
		$from_folder_id = isset($_POST['from_folder_id']) ? $_POST['from_folder_id'] : 0;
		$to_folder_id = isset($_POST['to_folder_id']) ? $_POST['to_folder_id'] : 0;

		foreach ($fromLinks as $fromLink) {
			$fromModel = call_user_func(array($fromLink['model_name'], 'model'))->findByPk($fromLink['model_id']);

			foreach ($toLinks as $toLink) {
				$model = call_user_func(array($toLink['model_name'], 'model'))->findByPk($toLink['model_id']);
				$fromModel->link($model, $_POST['description'], $from_folder_id, $to_folder_id);
			}
		}

		$response['success'] = true;

		return $response;
	}

	/**
	 * Get users
	 * 
	 * @param array $params @see GO_Base_Data_Store::getDefaultParams()
	 * @return  
	 */
	public function actionUsers($params) {

		$store = GO_Base_Data_Store::newInstance(GO_Base_Model_User::model());
		$store->setDefaultSortOrder('name', 'ASC');

		$store->getColumnModel()->formatColumn('name', '$model->name', array(), array('first_name', 'last_name'));
		$store->getColumnModel()->formatColumn('cf', '$model->id.":".$model->name'); //special field used by custom fields. They need an id an value in one.

		$store->setStatement (GO_Base_Model_User::model()->find($store->getDefaultParams()));

		return $store->getData();
	}

	/**
	 * Get user groups
	 * 
	 */
	public function actionGroups($params) {
		$store = GO_Base_Data_Store::newInstance(GO_Base_Model_Group::model());
		$store->setDefaultSortOrder('name', 'ASC');
		$store->setStatement (GO_Base_Model_Group::model()->find($store->getDefaultParams()));
		return $store->getData();
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


	public function actionThumb($params) {

		GO::session()->closeWriting();

		$dir = GO::config()->root_path . 'views/Extjs3/themes/Default/images/128x128/filetypes/';
		$url = GO::config()->host . 'views/Extjs3/themes/Default/images/128x128/filetypes/';
		$file = new GO_Base_Fs_File(GO::config()->file_storage_path . $params['src']);
		
		if (is_dir(GO::config()->file_storage_path . $params['src'])) {
			$src = $dir . 'folder.png';
		} else {

			switch ($file->extension()) {

				case 'ico':
				case 'jpg':
				case 'jpeg':
				case 'png':
				case 'gif':
				case 'xmind':
					$src = GO::config()->file_storage_path . $params['src'];
					break;


				case 'tar':
				case 'tgz':
				case 'gz':
				case 'bz2':
				case 'zip':
					$src = $dir . 'zip.png';
					break;
				case 'odt':
				case 'docx':
				case 'doc':
				case 'htm':
				case 'html':
					$src = $dir . 'doc.png';

					break;

				case 'odc':
				case 'ods':
				case 'xls':
				case 'xlsx':
					$src = $dir . 'spreadsheet.png';
					break;

				case 'odp':
				case 'pps':
				case 'pptx':
				case 'ppt':
					$src = $dir . 'pps.png';
					break;
				case 'eml':
					$src = $dir . 'message.png';
					break;


				case 'log':
					$src = $dir . 'txt.png';
					break;
				default:
					if (file_exists($dir . $file->extension() . '.png')) {
						$src = $dir . $file->extension() . '.png';
					} else {
						$src = $dir . 'unknown.png';
					}
					break;
			}
		}

		$file = new GO_Base_Fs_File($src);
		

		$w = isset($params['w']) ? intval($params['w']) : 0;
		$h = isset($params['h']) ? intval($params['h']) : 0;
		$zc = !empty($params['zc']) && !empty($w) && !empty($h);

		$lw = isset($params['lw']) ? intval($params['lw']) : 0;
		$lh = isset($params['lh']) ? intval($params['lh']) : 0;

		$pw = isset($params['pw']) ? intval($params['pw']) : 0;
		$ph = isset($params['ph']) ? intval($params['ph']) : 0;

		if ($file->extension() == 'xmind') {

//			$filename = $file->nameWithoutExtension().'.jpeg';
//
//			if (!file_exists($GLOBALS['GO_CONFIG']->file_storage_path . 'thumbcache/' . $filename) || filectime($GLOBALS['GO_CONFIG']->file_storage_path . 'thumbcache/' . $filename) < filectime($GLOBALS['GO_CONFIG']->file_storage_path . $path)) {
//				$zipfile = zip_open($GLOBALS['GO_CONFIG']->file_storage_path . $path);
//
//				while ($entry = zip_read($zipfile)) {
//					if (zip_entry_name($entry) == 'Thumbnails/thumbnail.jpg') {
//						require_once($GLOBALS['GO_CONFIG']->class_path . 'filesystem.class.inc');
//						zip_entry_open($zipfile, $entry, 'r');
//						file_put_contents($GLOBALS['GO_CONFIG']->file_storage_path . 'thumbcache/' . $filename, zip_entry_read($entry, zip_entry_filesize($entry)));
//						zip_entry_close($entry);
//						break;
//					}
//				}
//				zip_close($zipfile);
//			}
//			$path = 'thumbcache/' . $filename;
		}



		$cacheDir = new GO_Base_Fs_Folder(GO::config()->file_storage_path . 'thumbcache');
		$cacheDir->create();


		$cacheFilename = str_replace(array('/', '\\'), '_', $file->parent()->path() . '_' . $w . '_' . $h . '_' . $lw . '_' . $lh . '_' . $pw . '_' . $lw);
		if ($zc) {
			$cacheFilename .= '_zc';
		}
//$cache_filename .= '_'.filesize($full_path);
		$cacheFilename .= $file->name();

		$readfile = $cacheDir->path() . '/' . $cacheFilename;
		$thumbExists = file_exists($cacheDir->path() . '/' . $cacheFilename);
		$thumbMtime = $thumbExists ? filemtime($cacheDir->path() . '/' . $cacheFilename) : 0;

		if (!empty($params['nocache']) || !$thumbExists || $thumbMtime < $file->mtime() || $thumbMtime < $file->ctime()) {
			$image = new GO_Base_Util_Image($file->path());
			if (!$image->load_success) {
				//failed. Stream original image
				$readfile = $file->path();
			} else {


				if ($zc) {
					$image->zoomcrop($w, $h);
				} else {
					if ($lw || $lh || $pw || $lw) {
						//treat landscape and portrait differently
						$landscape = $image->landscape();
						if ($landscape) {
							$w = $lw;
							$h = $lh;
						} else {
							$w = $pw;
							$h = $ph;
						}
					}

					if ($w && $h) {
						$image->resize($w, $h);
					} elseif ($w) {
						$image->resizeToWidth($w);
					} else {
						$image->resizeToHeight($h);
					}
				}
				$image->save($cacheDir->path() . '/' . $cacheFilename);
			}
		}

//				header("Expires: " . date("D, j M Y G:i:s ", time() + (86400 * 365)) . 'GMT'); //expires in 1 year
//				header('Cache-Control: cache');
//				header('Pragma: cache');
//				header('Content-Type: ' . $file->mimeType());
//				header('Content-Disposition: inline; filename="' . $cacheFilename . '"');
//				header('Content-Transfer-Encoding: binary');

		readfile($readfile);


//			case 'pdf':
//				$this->redirect($url . 'pdf.png');
//				break;
//
//			case 'tar':
//			case 'tgz':
//			case 'gz':
//			case 'bz2':
//			case 'zip':
//				$this->redirect( $url . 'zip.png');
//				break;
//			case 'odt':
//			case 'docx':
//			case 'doc':
//				$this->redirect( $url . 'doc.png');
//				break;
//
//			case 'odc':
//			case 'ods':
//			case 'xls':
//			case 'xlsx':
//				$this->redirect( $url . 'spreadsheet.png');
//				break;
//
//			case 'odp':
//			case 'pps':
//			case 'pptx':
//			case 'ppt':
//				$this->redirect( $url . 'pps.png');
//				break;
//			case 'eml':
//				$this->redirect( $url . 'message.png');
//				break;
//
//			case 'htm':
//				$this->redirect( $url . 'doc.png');
//				break;
//
//			case 'log':
//				$this->redirect( $url . 'txt.png');
//				break;
//
//			default:
//				if (file_exists($dir . $file->extension() . '.png')) {
//					$this->redirect( $url . $file->extension() . '.png');
//				} else {
//					$this->redirect( $url . 'unknown.png');
//				}
//				break;
	}
	
	
	/**
	 * Download file from GO::config()->tmpdir/user_id/$path
	 * Because download is restricted from <user_id> subfolder this is secure.
	 * The user_id is appended in the config class.
	 * 
	 * 
	 */
	public function actionDownloadTempfile($params){		
		$file = new GO_Base_Fs_File(GO::config()->tmpdir.$params['path']);
		GO_Base_Util_Common::outputDownloadHeaders($file, false, !empty($params['cache']));
		$file->output();		
	}
	
	/**
	 * Public files are files stored in GO::config()->file_storage_path.'public'
	 * They are publicly accessible.
	 * Public files are cached
	 * 
	 * @param String $path 
	 */
	public function actionDownloadPublicFile($params){
		$file = new GO_Base_Fs_File(GO::config()->file_storage_path.'public/'.$params['path']);
		GO_Base_Util_Common::outputDownloadHeaders($file,false,!empty($params['cache']));
		$file->output();		
	}
	
	
	public function actionMultiRequest($params){		
			echo "{\n";
			
			$router = new GO_Base_Router();

			$requests = json_decode($params['requests'], true);
			foreach($requests as $responseIndex=>$requestParams){
				ob_start();				
				$router->runController($requestParams);
				echo "\n".'"'.$responseIndex.'" : '.ob_get_clean().",\n";
			}
			echo "success:true\n}\n";	
	}
	
	
	public function actionModelAttributes($params){
		
		$response['results']=array();
		
		$model = GO::getModel($params['modelName']);
		$labels = $model->attributeLabels();
		
		$columns = $model->getColumns();
		foreach($columns as $name=>$attr){
			if($name!='id' && $name!='user_id' && $name!='acl_id'){
				$attr['name']=$name;
				$attr['label']=$model->getAttributeLabel($name);
				$response['results'][]=$attr;
			}
		}
		
		if($model->customfieldsRecord){
			$columns = $model->customfieldsRecord->getColumns();
			foreach($columns as $name=>$attr){
				if($name != 'model_id'){
					$attr['name']=$name;
					$attr['label']=$model->customfieldsRecord->getAttributeLabel($name);
					$response['results'][]=$attr;
				}
			}
		}
		
		return $response;		
	}
	
	public function actionUpload($params) {

		$tmpFolder = new GO_Base_Fs_Folder(GO::config()->tmpdir . 'uploadqueue');
//		$tmpFolder->delete();
		$tmpFolder->create();

		$files = GO_Base_Fs_File::moveUploadedFiles($_FILES['attachments'], $tmpFolder);

		$relativeFiles = array();
		foreach ($files as $file) {
			$relativeFiles[]=str_replace(GO::config()->tmpdir, '', $file->path());
		}

		return array('success' => true, 'files'=>$relativeFiles);
	}

}