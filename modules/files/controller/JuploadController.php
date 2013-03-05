<?php

class GO_Files_Controller_Jupload extends GO_Base_Controller_AbstractController {

//	protected function allowGuests(){ // TODO: REMOVE THIS AND FIX THE ACCESS TO THE ACTIONS
//		return array('*');
//	}
//	
	protected function actionRenderJupload($params) {

		//$cookieParams = session_get_cookie_params();

		$sessionCookie = 'Cookie: ' . session_name() . '=' . session_id();

		//TODO: Check if code below is necessary
		
//		if(!empty($cookieParams['domain']))
//			$sessionCookie .= '; Domain='.$cookieParams['domain'];
//		
//		$sessionCookie .= '; Path='.$cookieParams['path'];
//		
//		if(!empty($cookieParams['lifetime']))
//			$sessionCookie .= '; Expires='.gmdate('D, d M Y H:i:s \G\M\T', time()+$cookieParams['lifetime']);
//		
//		if($cookieParams['secure'])
//			$sessionCookie .= '; Secure';
//		
//		if($cookieParams['httponly'])
//			$sessionCookie .= '; HttpOnly';

		
		$afterUploadScript = '
			<script type="text/javascript">
				function afterUpload(success){
					
					opener.GO.files.juploadFileBrowser.sendOverwrite({upload:true});	

					if(success){
						setTimeout("self.close();", 1000);
					}
				}
			</script>			
		';

		$appletCode = '
			<applet
				code="wjhk.jupload2.JUploadApplet"
				name="JUpload"
				archive="' . GO::config()->full_url . 'go/vendor/jupload/wjhk.jupload.jar' . '"
				width="640"
				height="480"
				mayscript="true"
				alt="The java pugin must be installed.">
				<param name="lang" value="' . GO::user()->language . '" />
				<param name="readCookieFromNavigator" value="false" />
				<!--<param name="lookAndFeel" value="system" />-->
				<param name="postURL" value="' . GO::url('files/jupload/handleUploads') . '" />
				<param name="afterUploadURL" value="javascript:afterUpload(%success%);" />
				<param name="showLogWindow" value="false" />
				<param name="maxChunkSize" value="1048576" />    
				<param name="specificHeaders" value="' . $sessionCookie . '" />
				<param name="maxFileSize" value="' . intval(GO::config()->max_file_size) . '" />
				<param name="nbFilesPerRequest" value="5" />
				<!--<param name="debugLevel" value="99" />-->
				Java 1.5 or higher plugin required. 
			</applet>';

		$this->render('jupload', array('applet' => $appletCode, 'afterUploadScript' => $afterUploadScript));
	}

	protected function actionHandleUploads($params) {

		if (!isset(GO::session()->values['files']['uploadqueue']))
			GO::session()->values['files']['uploadqueue'] = array();

		try {
			$chunkTmpFolder = new GO_Base_Fs_Folder(GO::config()->tmpdir . 'juploadqueue/chunks');
			$tmpFolder = new GO_Base_Fs_Folder(GO::config()->tmpdir . 'juploadqueue');

			$tmpFolder->create();
			$chunkTmpFolder->create();

			$count = 0;
			while ($uploadedFile = array_shift($_FILES)) {
				
				if (isset($params['jupart'])) {
					$originalFileName = $uploadedFile['name'];
					$uploadedFile['name'] = $uploadedFile['name'] . '.part' . $params['jupart'];
					$chunkTmpFolder->create();
					GO_Base_Fs_File::moveUploadedFiles($uploadedFile, $chunkTmpFolder);
					if (!empty($params['jufinal'])) {
						$file = new GO_Base_Fs_File($tmpFolder . '/' . $originalFileName);

						$fp = fopen($file->path(), 'w+');
						for ($i = 1; $i <= $params['jupart']; $i++) {
							$part = new GO_Base_Fs_File($chunkTmpFolder . '/' . $originalFileName . '.part' . $i);
							fwrite($fp, $part->contents());
							$part->delete();
						}
						fclose($fp);

						$chunkTmpFolder->delete();
					} else {
						echo "SUCCESS\n";
						return;
					}
				} else {
					$files = GO_Base_Fs_File::moveUploadedFiles($uploadedFile, $tmpFolder);
					$file = $files[0];
				}
				$subdir = false;
				if ((!empty($params['relpathinfo' . $count]) && !isset($params['jupart'])) ||
								(!empty($params['relpathinfo' . $count]) && isset($params['jupart']) && !empty($params['jufinal']))) {
					$fullpath = GO::config()->tmpdir . 'juploadqueue' . '/' . str_replace('\\','/',$params['relpathinfo'.$count]);

					$dir = new GO_Base_Fs_Folder($fullpath);
					$dir->create();
					$subdir = true;
					$file->move($dir);
				}
				$count++;
		
				if ($subdir) {
						
					$parent = $this->_findHighestParent($dir);
					
					go::debug($parent);
					if (!in_array($parent->path(), GO::session()->values['files']['uploadqueue']))
						GO::session()->values['files']['uploadqueue'][] = $parent->path();
				} else {
					GO::session()->values['files']['uploadqueue'][] = $file->path();
				}
			}
		} catch (Exception $e) {
			echo 'WARNING: ' . $e->getMessage() . "\n";
		}
		echo "SUCCESS\n";
	}
	
	private function _findHighestParent(GO_Base_Fs_Folder $dir){
		$parent = $dir;

		while($parent->parent()->name()!="juploadqueue"){
			go::debug($parent->name());
			$parent=$parent->parent();
		}

		return $parent;
		
	}

}
