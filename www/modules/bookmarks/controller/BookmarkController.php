<?php

class GO_Bookmarks_Controller_Bookmark extends GO_Base_Controller_AbstractModelController {

	protected $model = 'GO_Bookmarks_Model_Bookmark';

	public function actionDescription($params) {

		$response = array();

		if (function_exists('curl_init')) {
			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, $params['url']);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			//pretend to be ff4
			$useragent = "Mozilla/5.0 (X11; Linux x86_64; rv:2.0) Gecko/20100101 Firefox/4.0";
			curl_setopt($ch, CURLOPT_USERAGENT, $useragent);

			//for self-signed certificates
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

			$html = curl_exec($ch);
		} else {
			$html = @file_get_contents($params['url']);
		}

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

		$contents = @file_get_contents($params['url'] . '/favicon.ico');

		if (!empty($contents)) {
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
			$storeParams['where'] = 'category_id = ' . $params['category'];
		}
		
		return $storeParams;
	}

	protected function prepareStore($store) {
		$store->formatColumn('category_name', '$model->category_name');
		$store->formatColumn('thumb', '$model->thumbURL');
		$store->formatColumn('permissionLevel', '$model->permissionLevel');
	}

	protected function remoteComboFields() {
		return array('category_id' => '$model->category->name');
	}

	public function actionThumbs() {

		
		$response['results'] = array();
		
		$folder = new GO_Base_Fs_Folder(GO::modules()->bookmarks->path."icons");
		
		$filesystemObjects = $folder->ls();
		foreach($filesystemObjects as $imgObject) {
			
			$response['results'][] = array('filename' => $imgObject->name());
			
		}
		//var_dump($filesystemObjects);
		
		
		
		
		// relative path, URL 
		
//
//		while ($file = readdir($handler)) {
//			if ($file != '.' && $file != '..') {
//				$response['results'][] = array('filename' => $file);
//			}
//		}
//		closedir($handler);
//		
		$response['success']=true;

		return $response;
	}
	
	
	protected function afterLoad(&$response, &$model, &$params) {
		
		return parent::afterLoad($response, $model, $params);
	}
	
	

}