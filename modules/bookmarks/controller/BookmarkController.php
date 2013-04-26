<?php

class GO_Bookmarks_Controller_Bookmark extends GO_Base_Controller_AbstractModelController {

	protected $model = 'GO_Bookmarks_Model_Bookmark';

	protected function actionDescription($params) {

		$response = array();
		$response['title'] = '';
		$response['description'] = '';
				
		if (function_exists('curl_init')) {
			try{

				$c = new GO_Base_Util_HttpClient();
				$c->setCurlOption(CURLOPT_CONNECTTIMEOUT, 2);
				$c->setCurlOption(CURLOPT_TIMEOUT, 5);
				$c->setCurlOption(CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);			

				$html = $c->request($params['url']);

				//go_debug($html);

				$html = str_replace("\r", '', $html);
				$html = str_replace("\n", ' ', $html);

				$html = preg_replace("'</[\s]*([\w]*)[\s]*>'", "</$1>", $html);

				preg_match('/<head>(.*)<\/head>/i', $html, $match);
				if (isset($match[1])) {
					$html = $match[1];
					//go_debug($html);

					preg_match('/charset=([^"\'>]*)/i', $html, $match);
					if (isset($match[1])) {

						$charset = strtolower(trim($match[1]));
						if ($charset != 'utf-8')
							$html = GO_Base_Util_String::to_utf8($html, $charset);
					}

					preg_match_all('/<meta[^>]*>/i', $html, $matches);

					$description = '';
					foreach ($matches[0] as $match) {
						if (stripos($match, 'description')) {
							$name_pos = stripos($match, 'content');
							if ($name_pos) {
								$description = substr($match, $name_pos + 7, -1);
								$description = trim($description, '="\'/ ');
								break;
							}
						}
					}
					//replace double spaces
					$response['description'] = preg_replace('/\s+/', ' ', $description);

					preg_match('/<title>(.*)<\/title>/i', $html, $match);
					$response['title'] = $match ? preg_replace('/\s+/', ' ', trim($match[1])) : '';
				}
			}
			catch(Exception $e){
				
			}

			try{

				$contents = $c->request($params['url'] . '/favicon.ico');

				if (!empty($contents) && $c->getHttpCode()!=404) {
					$relpath = 'public/bookmarks/';
					$path = GO::config()->file_storage_path . $relpath;
					if (!is_dir($path))
						mkdir($path, 0755, true);

					$filename = str_replace('.', '_', preg_replace('/^https?:\/\//', '', $_POST['url'])) . '.ico';
					$filename = rtrim(str_replace('/', '_', $filename), '_ ');

					//var_dump($filename);

					file_put_contents($path . $filename, $contents);

					$response['logo'] = $relpath . $filename;
				}
			}
			catch(Exception $e){
				$response['logo'] = '';
			}
		}
		
		$response['title']=GO_Base_Util_String::cut_string($response['title'], 64, true, "");
		$response['description']=GO_Base_Util_String::cut_string($response['description'], 255, true, "");
		return $response;
	}

	protected function getStoreParams($params) {
		$storeParams = array(
				'order' => array('category_name', 'name'),
				'fields' => 't.*,bm_categories.name AS category_name',
				'join' => 'inner join bm_categories on t.category_id = bm_categories.id',
				
		);
		
		if(!empty($params['category'])){
			// Do something
			$storeParams['where'] = 'category_id = ' . intval($params['category']);
		}
		
		return $storeParams;
	}

	protected function formatColumns(GO_Base_Data_ColumnModel $columnModel) {		
		
		$columnModel->formatColumn('category_name', '$model->category_name');
		$columnModel->formatColumn('thumb', '$model->thumbURL');
		$columnModel->formatColumn('permissionLevel', '$model->permissionLevel');
		$columnModel->formatColumn('content', 'urldecode($model->content)');
	}

	protected function remoteComboFields() {
		return array('category_id' => '$model->category->name');
	}

	protected function actionThumbs() {		
		$response['results'] = array();
		
		$folder = new GO_Base_Fs_Folder(GO::modules()->bookmarks->path."icons");
		
		$filesystemObjects = $folder->ls();
		foreach($filesystemObjects as $imgObject) {			
			$response['results'][] = array('filename' => $imgObject->name());			
		}	
		$response['success']=true;

		return $response;
	}
	
	protected function actionUpload($params) {

	
		$relpath = 'public/bookmarks/';
		
		$folder = new GO_Base_Fs_Folder(GO::config()->file_storage_path.$relpath);
		$folder->create();
		
		
		$files= GO_Base_Fs_File::moveUploadedFiles($_FILES['attachments'], $folder);
		$file= $files[0];
		$file->rename($params['thumb_id'].'.'.$file->extension());
		
		$response['logo'] = $file->stripFileStoragePath();

		$response['success'] = true;
		
		return $response;
	}
}