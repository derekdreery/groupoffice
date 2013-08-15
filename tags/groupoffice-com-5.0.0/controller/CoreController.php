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
		return array('compress','cron');
	}
	
	protected function ignoreAclPermissions() {
		return array('cron');
	}
	
	protected function actionSaveSetting($params){
		$response['success']=GO::config()->save_setting($params['name'], $params['value'], $params['user_id']);
		
		return $response;
	}
	
	protected function actionDebug($params){
		
		if(empty(GO::session()->values['debug'])){
//			if(!GO::user()->isAdmin())
//				throw new GO_Base_Exception_AccessDenied("Debugging can only be enabled by an admin. Tip: You can enable it as admin and switch to any user with the 'Switch user' module.");
		
			GO::session()->values['debug']=true;
		}
		
		GO::session()->values['debugSql']=!empty($params['debugSql']);
		
		
		$debugFile = new GO_Base_Fs_File(GO::config()->file_storage_path.'log/debug.log');
		if(!$debugFile->exists())
			$debugFile->touch(true);
		
		$errorFile = new GO_Base_Fs_File(GO::config()->file_storage_path.'log/error.log');
		if(!$errorFile->exists())
			$errorFile->touch(true);
		
		$debugLog = nl2br(str_replace('['.GO::user()->username.'] ','',$debugFile->tail(300)));
		$debugLog = str_replace('--------------------','<hr />', $debugLog);
		
		return array(
				'success'=>true, 
				'debugLog'=>$debugLog,
				'errorLog'=>str_replace('----------------','<hr />', nl2br($errorFile->tail(300)))
				);
	}
	
	protected function actionInfo($params){
		
		if(empty(GO::session()->values['debug'])){
			throw new GO_Base_Exception_AccessDenied("Debugging can only be enabled by an admin");
		}
			
		$response = array('success'=>true, 'info'=>'');
		
		$info['username']=GO::user()->username;
		$info['config']=GO::config()->get_config_file();
		$info['database']=GO::config()->db_name;
		
		$modules = GO::modules()->getAllModules();		
		foreach($modules as $module){
			if(!isset($info['modules']))
				$info['modules']=$module->id;
			else
				$info['modules'].=', '.$module->id;
		}
		
		$info = array_merge($info,$_SERVER);
		
		
		$response['info']='<table>';
		
		foreach($info as $key=>$value)
			$response['info'] .= '<tr><td>'.$key.':</td><td>'.$value.'</td></tr>';
		
		$response['info'].='</table>';
		
		ob_start();
		phpinfo();
		$phpinfo = ob_get_contents();
		ob_get_clean();
		
		$response['info'].= GO_Base_Util_String::sanitizeHtml($phpinfo);
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
	
	protected function actionUnlink($params){
		$linkedModel1 = GO::getModel($params['model_name1'])->findByPk($params['id1']);				
		$linkedModel2 = GO::getModel($params['model_name2'])->findByPk($params['id2']);			
		$linkedModel1->unlink($linkedModel2);	
		
		return array('success'=>true);
	}
	
	protected function actionUpdateLink($params){
		$model1 = GO::getModel($params['model_name1'])->findByPk($params['model_id1']);
		$model2 = GO::getModel($params['model_name2'])->findByPk($params['model_id2']);
		$model1->updateLink($model2, array('description'=>$params['description']));
		$model2->updateLink($model1, array('description'=>$params['description']));
		
		return array('success'=>true);
	}

	/**
	 * Get users
	 * 
	 * @param array $params @see GO_Base_Data_Store::getDefaultParams()
	 * @return  
	 */
	protected function actionUsers($params) {
		
		if(GO::user()->isAdmin())
			GO::config()->limit_usersearch=0;
		
//		GO::config()->limit_usersearch=10;
		
//		if(empty($params['query']) && !empty($params['queryRequired'])){
//			return array(
////					'emptyText'=>"Enter queSry",
//					'success'=>true,
//					'results'=>array()
//			);
//		}
		
		if(!isset($params['limit']))
			$params['limit']=0;
		
		if(!isset($params['start']))
			$params['start']=0;
		
		// Check for the value "limit_usersearch" in the group-office config file and then add the limit.
		if(!empty(GO::config()->limit_usersearch)){
			if($params['limit']>GO::config()->limit_usersearch)
				$params['limit'] = GO::config()->limit_usersearch;			
			
			if($params['start']+$params['limit']>GO::config()->limit_usersearch)
				$params['start']=0;
		}
		
		$store = GO_Base_Data_Store::newInstance(GO_Base_Model_User::model());
		$store->setDefaultSortOrder('name', 'ASC');

		$store->getColumnModel()->formatColumn('name', '$model->name', array(), array('first_name', 'last_name'));
		$store->getColumnModel()->formatColumn('cf', '$model->id.":".$model->name'); //special field used by custom fields. They need an id an value in one.
		
		//only get users that are enabled
		$enabledParam = GO_Base_Db_FindParams::newInstance();
						//->criteria(GO_Base_Db_FindCriteria::newInstance()->addCondition('enabled', true));
		
		$store->setStatement (GO_Base_Model_User::model()->find($store->getDefaultParams($params, $enabledParam)));
		$response = $store->getData();
		
		if(!empty(GO::config()->limit_usersearch) && $response['total']>GO::config()->limit_usersearch)
			$response['total']=GO::config()->limit_usersearch;	
		
		return $response;
	}

	/**
	 * Get user groups
	 * 
	 */
	protected function actionGroups($params) {
		$store = GO_Base_Data_Store::newInstance(GO_Base_Model_Group::model());
		$store->setDefaultSortOrder('name', 'ASC');
		
		$findParams = $store->getDefaultParams($params);
		
//		if(empty($params['manage'])){
//			
//			//permissions are handled differently. Users may use all groups they are member of.
//			$findParams->ignoreAcl();
//			
//			if(!GO::user()->isAdmin()){
//				$findParams->getCriteria()
//								->addCondition('admin_only', 1,'!=')
//								->addCondition('user_id', GO::user()->id,'=','ug');
//				
//				$findParams->joinModel(array(
//						'model'=>"GO_Base_Model_UserGroup",
//						'localTableAlias'=>'t', //defaults to "t"	  
//						'foreignField'=>'group_id', //defaults to primary key of the remote model
//						'tableAlias'=>'ug', //Optional table alias
//	 			));
//			}
//			
//		}
		
		
		$store->setStatement (GO_Base_Model_Group::model()->find($findParams));
		return $store->getData();
	}
	
	/**
	 * Get the holidayfiles that are available groups
	 */
	protected function actionHolidays($params) {
		$available = GO_Base_Model_Holiday::getAvailableHolidayFiles();
		
		$store = new GO_Base_Data_ArrayStore();
		$store->setRecords($available);
		return $store->getData();
	}

	/**
	 * Todo replace compress.php with this action
	 */
	protected function actionCompress($params) {
		
		GO::session()->closeWriting();
		
		$this->checkRequiredParameters(array('file'), $params);
	
		$file = GO::config()->getCacheFolder()->child(basename($params['file']));

//		$file = new GO_Base_Fs_File(GO::config()->file_storage_path.'cache/'.basename($params['file']));

		$ext = $file->extension();

		$type = $ext =='js' ? 'text/javascript' : 'text/css';

		$use_compression = GO::config()->use_zlib_compression();

		if($use_compression){
			ob_start();
			ob_start('ob_gzhandler');
		}
		$offset = 30*24*60*60;
		header ("Content-Type: $type; charset: UTF-8");
		header("Expires: " . date("D, j M Y G:i:s ", time()+$offset) . 'GMT');
		header('Cache-Control: cache');
		header('Pragma: cache');
		if(!$use_compression){
			header("Content-Length: ".$file->size());
		}
		readfile($file->path());

		if($use_compression){
			ob_end_flush();  // The ob_gzhandler one

			header("Content-Length: ".ob_get_length());

			ob_end_flush();  // The main one
		}
	}

	protected function actionThumb($params) {

		GO::session()->closeWriting();

		$dir = GO::config()->root_path . 'views/Extjs3/themes/Default/images/128x128/filetypes/';
		$url = GO::config()->host . 'views/Extjs3/themes/Default/images/128x128/filetypes/';
		$file = new GO_Base_Fs_File(GO::config()->file_storage_path . $params['src']);
		
		if (is_dir(GO::config()->file_storage_path . $params['src'])) {
			$src = $dir . 'folder.png';
		} else {

			switch (strtolower($file->extension())) {

				case 'svg':
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
		
		$inline = !isset($params['inline']) || !empty($params['inline']);
		
		$file = new GO_Base_Fs_File(GO::config()->tmpdir.$params['path']);
		GO_Base_Util_Http::outputDownloadHeaders($file, $inline, !empty($params['cache']));
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
			
			$this->checkRequiredParameters(array('requests'), $params);

			$requests = json_decode($params['requests'], true);
			if(is_array($requests)){
				foreach($requests as $responseIndex=>$requestParams){
					ob_start();				
					GO::router()->runController($requestParams);
					echo "\n".'"'.$responseIndex.'" : '.ob_get_clean().",\n";
				}
			}
			echo '"success":true}';	
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
		
		
		GO_Base_Component_Plupload::handleUpload();

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
						'extension'=>strtolower($file->extension()),
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
	
	
	
	protected function actionSaveState($params){
		//close writing to session so other concurrent requests won't be locked out.
		GO::session()->closeWriting();
		
		if(isset($params['values'])){
			$values = json_decode($params['values'], true);

			if(!is_array($values)){
				trigger_error ("Invalid value for GO_Core_Controller_Core::actionSaveState: ".var_export($params, true), E_USER_NOTICE);
			}else
			{
				foreach($values as $name=>$value){

					$state = GO_Base_Model_State::model()->findByPk(array('name'=>$name,'user_id'=>GO::user()->id));

					if(!$state){
						$state = new GO_Base_Model_State();
						$state->name=$name;
					}

					$state->value=$value;
					$state->save();
				}
			}
		}
		$response['success']=true;
		echo json_encode($response);
	}
	
	
	protected function actionAbout($params){	
		$response['data']['about']=GO::t('about');
		
		if(GO::config()->product_name=='Group-Office')
			$response['data']['about']=str_replace('{company_name}', 'Intermesh B.V.', $response['data']['about']);
		else
			$response['data']['about']=str_replace('{company_name}', GO::config()->product_name, $response['data']['about']);
		
		$response['data']['about']=str_replace('{version}', GO::config()->version, $response['data']['about']);
		$response['data']['about']=str_replace('{current_year}', date('Y'), $response['data']['about']);
		$response['data']['about']=str_replace('{product_name}', GO::config()->product_name, $response['data']['about']);

		
		$response['data']['mailbox_usage']=GO::config()->get_setting('mailbox_usage');
		$response['data']['file_storage_usage']=GO::config()->get_setting('file_storage_usage');
		$response['data']['database_usage']=GO::config()->get_setting('database_usage');
		$response['data']['total_usage']=$response['data']['database_usage']+$response['data']['file_storage_usage']+$response['data']['mailbox_usage'];
		$response['data']['has_usage']=$response['data']['total_usage']>0;
		foreach($response['data'] as $key=>$value){
			if($key!='has_usage' && $key!='about')
				$response['data'][$key]=  GO_Base_Util_Number::formatSize($value);
		}
		
		$response['success']=true;
		
		return $response;
	}
	
	
 /* MOVED TO CRONFILE IN Email/Cron/EmailReminders.php
  * 
  * Run a cron job every 5 minutes. Add this to /etc/cron.d/groupoffice :
  *
  STAR/5 * * * * root php /usr/share/groupoffice/groupofficecli.php -c=/path/to/config.php -r=core/cron
  *
  * Replace STAR with a *.
	*
	* @DEPRECATED
  */
//	protected function actionCron($params){		
//		
//		$this->requireCli();
//		GO::session()->runAsRoot();
//		
//		$this->_emailReminders();
//		
//		$this->fireEvent("cron");
//	}
	
// 	/**
// 	 * MOVED TO CRONFILE IN Email/Cron/EmailReminders.php
// 	 *
// 	 *
// 	 *  @DEPRECATED
// 	 */
//	private function _emailReminders(){
//		$usersStmt = GO_Base_Model_User::model()->find();
//		while ($userModel = $usersStmt->fetch()) {
//			if ($userModel->mail_reminders==1) {
//				$remindersStmt = GO_Base_Model_Reminder::model()->find(
//					GO_Base_Db_FindParams::newInstance()
//						->joinModel(array(
//							'model' => 'GO_Base_Model_ReminderUser',
//							'localTableAlias' => 't',
//							'localField' => 'id',
//							'foreignField' => 'reminder_id',
//							'tableAlias' => 'ru'								
//						))
//						->criteria(
//							GO_Base_Db_FindCriteria::newInstance()
//								->addCondition('user_id', $userModel->id, '=', 'ru')
//								->addCondition('time', time(), '<', 'ru')
//								->addCondition('mail_sent', '0', '=', 'ru')
//						)
//				);
//
//				while ($reminderModel = $remindersStmt->fetch()) {
////					$relatedModel = $reminderModel->getRelatedModel();
//					
////					var_dump($relatedModel->name);
//					
////					$modelName = $relatedModel ? $relatedModel->localizedName : GO::t('unknown');
//					$subject = GO::t('reminder').': '.$reminderModel->name;
//
//					$time = !empty($reminderModel->vtime) ? $reminderModel->vtime : $reminderModel->time;
//			
//					date_default_timezone_set($userModel->timezone);
//					
//					$body = GO::t('time').': '.date($userModel->completeDateFormat.' '.$userModel->time_format,$time)."\n";
//					$body .= GO::t('name').': '.str_replace('<br />',',',$reminderModel->name)."\n";
//			
////					date_default_timezone_set(GO::user()->timezone);
//					
//					$message = GO_Base_Mail_Message::newInstance($subject, $body);
//					$message->addFrom(GO::config()->webmaster_email,GO::config()->title);
//					$message->addTo($userModel->email,$userModel->name);
//					GO_Base_Mail_Mailer::newGoInstance()->send($message);
//					
//					$reminderUserModelSend = GO_Base_Model_ReminderUser::model()
//						->findSingleByAttributes(array(
//							'user_id' => $userModel->id,
//							'reminder_id' => $reminderModel->id
//						));
//					$reminderUserModelSend->mail_sent = 1;
//					$reminderUserModelSend->save();
//				}
//				
//				date_default_timezone_set(GO::user()->timezone);
//			}
//		}
//	}
	
	protected function actionThemes($params){
		$store = new GO_Base_Data_ArrayStore();
		
		$view = new GO_Base_View_Extjs3();
		$themes = $view->getThemeNames();
		
		foreach($themes as $theme){
			$store->addRecord(array('theme'=>$theme));
		}
		
		return $store->getData();
	}
	
	protected function actionModules($params){
		$store = new GO_Base_Data_ArrayStore();
		
		$modules = GO::modules()->getAllModules(true);
		
		foreach($modules as $module){
			$store->addRecord(array('id'=>$module->id,'name'=>$module->moduleManager->name()));
		}
		
		return $store->getData();
	}
}
