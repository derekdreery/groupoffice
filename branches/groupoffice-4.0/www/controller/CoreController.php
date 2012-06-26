<?php

/**
 * TODO
 * 
 * The whole init process of Group-Office has to be remodelled.
 * The default_scripts.inc.php file is ugly and bad design. Instead all init
 * views in modules should register client scripts and css files.
 */
class GO_Core_Controller_Core extends GO_Base_Controller_AbstractController {
	
	protected function allowGuests() {
		return array('plupload');
	}
	
	protected function saveSetting($params){
		$response['success']=GO::config()->save_setting($params['name'], $params['value'], $params['user_id']);
		
		return $response;
	}
	
	protected function actionLink($params) {

		$fromLinks = json_decode($params['fromLinks'], true);
		$toLinks = json_decode($params['toLinks'], true);
		$from_folder_id = isset($params['from_folder_id']) ? $params['from_folder_id'] : 0;
		$to_folder_id = isset($params['to_folder_id']) ? $params['to_folder_id'] : 0;

		foreach ($fromLinks as $fromLink) {
			$fromModel = GO::getModel($fromLink['model_name'])->findByPk($fromLink['model_id']);

			foreach ($toLinks as $toLink) {
				$model = GO::getModel($toLink['model_name'])->findByPk($toLink['model_id']);
				$fromModel->link($model, $params['description'], $from_folder_id, $to_folder_id);
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
	protected function actionUsers($params) {

		$store = GO_Base_Data_Store::newInstance(GO_Base_Model_User::model());
		$store->setDefaultSortOrder('name', 'ASC');

		$store->getColumnModel()->formatColumn('name', '$model->name', array(), array('first_name', 'last_name'));
		$store->getColumnModel()->formatColumn('cf', '$model->id.":".$model->name'); //special field used by custom fields. They need an id an value in one.

		$store->setStatement (GO_Base_Model_User::model()->find($store->getDefaultParams($params)));

		return $store->getData();
	}

	/**
	 * Get user groups
	 * 
	 */
	protected function actionGroups($params) {
		$store = GO_Base_Data_Store::newInstance(GO_Base_Model_Group::model());
		$store->setDefaultSortOrder('name', 'ASC');
		
		$findParams = $store->getDefaultParams($params);
		
		if(empty($params['manage'])){
			
			//permissions are handled differently. Users may use all groups they are member of.
			$findParams->ignoreAcl();
			
			if(!GO::user()->isAdmin()){
				$findParams->getCriteria()
								->addCondition('admin_only', 1,'!=')
								->addCondition('user_id', GO::user()->id,'=','ug');
				
				$findParams->joinModel(array(
						'model'=>"GO_Base_Model_UserGroup",
						'localTableAlias'=>'t', //defaults to "t"	  
						'foreignField'=>'group_id', //defaults to primary key of the remote model
						'tableAlias'=>'ug', //Optional table alias
	 			));
			}
			
		}
		
		
		$store->setStatement (GO_Base_Model_Group::model()->find($findParams));
		return $store->getData();
	}

	/**
	 * Todo replace compress.php with this action
	 */
	protected function actionCompress() {
		
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


	protected function actionThumb($params) {

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



		$cacheDir = new GO_Base_Fs_Folder(GO::config()->orig_tmpdir . 'thumbcache');
		$cacheDir->create();


		$cacheFilename = str_replace(array('/', '\\'), '_', $file->parent()->path() . '_' . $w . '_' . $h . '_' . $lw . '_' . $ph. '_' . '_' . $pw . '_' . $lw);
		if ($zc) {
			$cacheFilename .= '_zc';
		}
//$cache_filename .= '_'.filesize($full_path);
		$cacheFilename .= $file->name();

		$readfile = $cacheDir->path() . '/' . $cacheFilename;
		$thumbExists = file_exists($cacheDir->path() . '/' . $cacheFilename);
		$thumbMtime = $thumbExists ? filemtime($cacheDir->path() . '/' . $cacheFilename) : 0;
		
		GO::debug("Thumb mtime: ".$thumbMtime." (".$cacheFilename.")");

		if (!empty($params['nocache']) || !$thumbExists || $thumbMtime < $file->mtime() || $thumbMtime < $file->ctime()) {
			
			GO::debug("Resizing image");
			$image = new GO_Base_Util_Image($file->path());
			if (!$image->load_success) {
				GO::debug("Failed to load image for thumbnailing");
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
					
					GO::debug($w."x".$h);

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

				header("Expires: " . date("D, j M Y G:i:s ", time() + (86400 * 365)) . 'GMT'); //expires in 1 year
				header('Cache-Control: cache');
				header('Pragma: cache');
				header('Content-Type: ' . $file->mimeType());
				header('Content-Disposition: inline; filename="' . $cacheFilename . '"');
				header('Content-Transfer-Encoding: binary');

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
	protected function actionDownloadTempfile($params){		
		$file = new GO_Base_Fs_File(GO::config()->tmpdir.$params['path']);
		GO_Base_Util_Http::outputDownloadHeaders($file, false, !empty($params['cache']));
		$file->output();		
	}
	
	/**
	 * Public files are files stored in GO::config()->file_storage_path.'public'
	 * They are publicly accessible.
	 * Public files are cached
	 * 
	 * @param String $path 
	 */
	protected function actionDownloadPublicFile($params){
		$file = new GO_Base_Fs_File(GO::config()->file_storage_path.'public/'.$params['path']);
		GO_Base_Util_Http::outputDownloadHeaders($file,false,!empty($params['cache']));
		$file->output();		
	}
	
	
	protected function actionMultiRequest($params){	  
			echo "{\n";
			
			//$router = new GO_Base_Router();

			$requests = json_decode($params['requests'], true);
			if(is_array($requests)){
				foreach($requests as $responseIndex=>$requestParams){
					ob_start();				
					GO::router()->runController($requestParams);
					echo "\n".'"'.$responseIndex.'" : '.ob_get_clean().",\n";
				}
			}
			echo "success:true\n}\n";	
	}
	
	
//	protected function actionModelAttributes($params){
//		
//		$response['results']=array();
//		
//		$model = GO::getModel($params['modelName']);
//		$labels = $model->attributeLabels();
//		
//		$columns = $model->getColumns();
//		foreach($columns as $name=>$attr){
//			if($name!='id' && $name!='user_id' && $name!='acl_id'){
//				$attr['name']=$name;
//				$attr['label']=$model->getAttributeLabel($name);
//				$response['results'][]=$attr;
//			}
//		}
//		
//		if($model->customfieldsRecord){
//			$columns = $model->customfieldsRecord->getColumns();
//			foreach($columns as $name=>$attr){
//				if($name != 'model_id'){
//					$attr['name']=$name;
//					$attr['label']=$model->customfieldsRecord->getAttributeLabel($name);
//					$response['results'][]=$attr;
//				}
//			}
//		}
//		
//		return $response;		
//	}
	
	protected function actionUpload($params) {

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
	
	
	protected function actionPlupload($params) {
		
		if(!GO::user() && !GO_Base_Authorized_Actions::isAuthorized('plupload')){
			throw new GO_Base_Exception_AccessDenied();
		}
		
		$tmpFolder = new GO_Base_Fs_Folder(GO::config()->tmpdir . 'uploadqueue');
		//$tmpFolder->delete();
		$tmpFolder->create();

//		$files = GO_Base_Fs_File::moveUploadedFiles($_FILES['attachments'], $tmpFolder);
//		GO::session()->values['files']['uploadqueue'] = array();
//		foreach ($files as $file) {
//			GO::session()->values['files']['uploadqueue'][] = $file->path();
//		}

		if(!isset(GO::session()->values['files']['uploadqueue']))
			GO::session()->values['files']['uploadqueue'] = array();	

		$targetDir=$tmpFolder->path();

		// Get parameters
		$chunk = isset($params["chunk"]) ? $params["chunk"] : 0;
		$chunks = isset($params["chunks"]) ? $params["chunks"] : 0;
		$fileName = isset($params["name"]) ? $params["name"] : '';

// Clean the fileName for security reasons
		$fileName = GO_Base_Fs_File::stripInvalidChars($fileName);
		
// Make sure the fileName is unique but only if chunking is disabled
		if ($chunks < 2 && file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName)) {
			$ext = strrpos($fileName, '.');
			$fileName_a = substr($fileName, 0, $ext);
			$fileName_b = substr($fileName, $ext);

			$count = 1;
			while (file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName_a . '_' . $count . $fileName_b))
				$count++;

			$fileName = $fileName_a . '_' . $count . $fileName_b;
		}


// Look for the content type header
		if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
			$contentType = $_SERVER["HTTP_CONTENT_TYPE"];

		if (isset($_SERVER["CONTENT_TYPE"]))
			$contentType = $_SERVER["CONTENT_TYPE"];
		
		if(!in_array($targetDir . DIRECTORY_SEPARATOR . $fileName, GO::session()->values['files']['uploadqueue']))
				GO::session()->values['files']['uploadqueue'][]=$targetDir . DIRECTORY_SEPARATOR . $fileName;
		
		$file = new GO_Base_Fs_File($targetDir . DIRECTORY_SEPARATOR . $fileName);
		if ($file->exists() && $file->size() > GO::config()->max_file_size)
			throw new Exception("File too large");

// Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
		if (strpos($contentType, "multipart") !== false) {
			
			if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
				
				// Open temp file
				$out = fopen($targetDir . DIRECTORY_SEPARATOR . $fileName, $chunk == 0 ? "wb" : "ab");
				if ($out) {
					// Read binary input stream and append it to temp file
					$in = fopen($_FILES['file']['tmp_name'], "rb");

					if ($in) {
						while ($buff = fread($in, 4096))
							fwrite($out, $buff);
					} else
						die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
					fclose($in);
					fclose($out);
					@unlink($_FILES['file']['tmp_name']);
				} else
					die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
			} else
				die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
		} else {
			// Open temp file
			$out = fopen($targetDir . DIRECTORY_SEPARATOR . $fileName, $chunk == 0 ? "wb" : "ab");
			if ($out) {
				// Read binary input stream and append it to temp file
				$in = fopen("php://input", "rb");

				if ($in) {
					while ($buff = fread($in, 4096))
						fwrite($out, $buff);
				} else
					die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');

				fclose($in);
				fclose($out);
			} else
				die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
		}

// Return JSON-RPC response
		die('{"jsonrpc" : "2.0", "result": null, "success":true, "id" : "id"}');

		//return array('success' => true);
	}
	
	protected function actionPluploads($params){
		
		if(isset($params['addFileStorageFiles'])){
			$files = json_decode($params['addFileStorageFiles'],true);
			foreach($files as $filepath)
				GO::session()->values['files']['uploadqueue'][]=GO::config()->file_storage_path.$filepath;
		}
		
		$response['results']=array();
		
		if(!empty(GO::session()->values['files']['uploadqueue'])){
			foreach(GO::session()->values['files']['uploadqueue'] as $path){
				
				$file = new GO_Base_Fs_File($path);
				
				$result = array(						
						'human_size'=>$file->humanSize(),
						'extension'=>$file->extension(),
						'size'=>$file->size(),
						'type'=>$file->mimeType(),
						'name'=>$file->name()
				);
				if($file->isTempFile())
				{
					$result['from_file_storage']=false;
					$result['tmp_file']=$file->stripTempPath();
				}else
				{
					$result['from_file_storage']=true;
					$result['tmp_file']=$file->stripFileStoragePath();
				}
				
				$response['results'][]=$result;
			}
		}
		$response['total']=count($response['results']);
		
		unset(GO::session()->values['files']['uploadqueue']);
		
		return $response;
	}
	
	protected function actionSpellCheck($params) {
		
		if (!isset($params['lang']))
			$params['lang'] = GO::session()->values['language'];

		if (   !isset($params['tocheck'])
			|| empty($params['tocheck'])
			|| !function_exists('pspell_new')
		) {
			$response['errorcount'] = 0;
			$response['text'] = '';
		} else {

			$mispeltwords = GO_Base_Util_SpellChecker::check($params['tocheck'], $params['lang']);
			if (!empty($mispeltwords)) {
				$response['errorcount'] = count($mispeltwords);
				$response['text'] = GO_Base_Util_SpellChecker::replaceMisspeltWords($mispeltwords, $params['tocheck']);
			} else {
				$response['errorcount'] = 0;
				$response['text'] = $params['tocheck'];
			}
		}

		return $response;
	}
}
