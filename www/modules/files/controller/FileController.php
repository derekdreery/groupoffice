<?php

class GO_Files_Controller_File extends GO_Base_Controller_AbstractModelController {

	protected $model = 'GO_Files_Model_File';
	
	protected function allowGuests() {
		return array('download'); //permissions will be checked manually in that action
	}

	protected function afterDisplay(&$response, &$model, &$params) {

		$response['data']['path'] = $model->path;
		$response['data']['size'] = GO_Base_Util_Number::formatSize($model->fsFile->size());
		$response['data']['extension'] = $model->fsFile->extension();
		$response['data']['type'] = GO::t($model->fsFile->extension(), 'base', 'filetypes');

		if (!empty($model->random_code) && time() < $model->expire_time) {
			$response['data']['expire_time'] = $model->getAttribute('expire_time', 'formatted');
			$response['data']['download_link'] = $model->emailDownloadURL;
		} else {
			$response['data']['expire_time'] = "";
			$response['data']['download_link'] = "";
		}

		if ($model->fsFile->isImage())
			$response['data']['thumbnail_url'] = $model->thumbURL;
		else
			$response['data']['thumbnail_url'] = "";

		if(GO::modules()->filesearch){
			$filesearch = GO_Filesearch_Model_Filesearch::model()->findByPk($model->id);
			if(!$filesearch){
				$filesearch = GO_Filesearch_Model_Filesearch::model()->createFromFile($model);
			}
					
			$response['data']=array_merge($response['data'],$filesearch->getAttributes('formatted'));
			
			if (!empty($params['query_params'])) {
				$qp = json_decode($params['query_params'], true);
				if (isset($qp['content_all'])){
					
					$c = new GO_Filesearch_Controller_Filesearch();
					
					$response['data']['text'] = $c->highlightSearchParams($qp, $response['data']['text']);
				}
			}
		}

		return parent::afterDisplay($response, $model, $params);
	}

	protected function afterLoad(&$response, &$model, &$params) {

		$response['data']['path'] = $model->path;
		$response['data']['size'] = GO_Base_Util_Number::formatSize($model->fsFile->size());
		$response['data']['extension'] = $model->fsFile->extension();
		$response['data']['type'] = GO::t($model->fsFile->extension(), 'base', 'filetypes');
		
		if (GO::modules()->customfields)
			$response['customfields'] = GO_Customfields_Controller_Category::getEnabledCategoryData("GO_Files_Model_File", $model->folder_id);

		return parent::afterLoad($response, $model, $params);
	}

	protected function actionDownload($params) {
		GO::session()->closeWriting();
		
		if(isset($params['path'])){
			$folder = GO_Files_Model_Folder::model()->findByPath(dirname($params['path']));
			$file = $folder->hasFile(GO_Base_Fs_File::utf8Basename($params['path']));
		}else
		{
			$file = GO_Files_Model_File::model()->findByPk($params['id'], false, true);
		}
		
		if(!empty($params['random_code'])){
			if($file->random_code!=$params['random_code'])
				throw new Exception("Invalid download link");
			
			if(time()>$file->expire_time)
				throw new Exception("Sorry, the download link has expired");				
		}else
		{
			if(!$file->checkPermissionLevel(GO_Base_Model_Acl::READ_PERMISSION))
				throw new GO_Base_Exception_AccessDenied();
		}

		
		GO_Base_Util_Http::outputDownloadHeaders($file->fsFile, false, !empty($params['cache']));
		$file->fsFile->output();
	}

	
	/**
	 *
	 * @param type $params 
	 * @todo
	 */
	protected function actionEmailDownloadLink($params){
		
		$file = GO_Files_Model_File::model()->findByPk($params['id']);
		$file->random_code=GO_Base_Util_String::randomPassword(11);
		$file->expire_time = $params['expire_time'];
		$file->save();
				
		$html=$params['content_type']=='html';
		$bodyindex = $html ? 'htmlbody' : 'plainbody';
		
		$url = GO::url('files/file/download',array('id'=>$file->id,'random_code'=>$file->random_code),false, $html);
		
		if($html){
			$url = '<a href="'.$url.'">'.$file->name.'</a>';
			$lb='<br />';
		}else
		{
			$lb = "\n";
		}
		
		$text = $url.' ('.GO::t('possibleUntil','files').' '.GO_Base_Util_Date::get_timestamp($file->expire_time, false).')'.$lb.$lb;

		
		if($params['template_id'] && ($template = GO_Addressbook_Model_Template::model()->findByPk($params['template_id']))){
			$message = GO_Email_Model_SavedMessage::model()->createFromMimeData($template->content);
	
			$response['data']=$message->toOutputArray($html, true);
			if(strpos($response['data'][$bodyindex],'{body}'))
				$response['data'][$bodyindex] = GO_Addressbook_Model_Template::model()->replaceCustomTags($response['data'][$bodyindex], array('body'=>$text));			
			else
				$response['data'][$bodyindex] = $text.$response['data'][$bodyindex];
			
		}else
		{
			$response['data'][$bodyindex]=$text;	
		}
				
		$response['data']['subject'] = GO::t('downloadLink','files').' '.$file->name;
		$response['success']=true;
		
		return $response;
	}
	

}

