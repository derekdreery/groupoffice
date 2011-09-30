<?php

/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Twan Verhofstad
 */
require_once('../../Group-Office.php');

//Authenticate the user
$GO_SECURITY->json_authenticate('bookmarks');

//Require the module class
require_once ($GO_MODULES->modules['bookmarks']['class_path'] . 'bookmarks.class.inc.php');
$bookmarks = new bookmarks();

$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';
try {

	switch ($task) {

		case 'description':

			$response=array();

			if (function_exists('curl_init')) {
				$ch = curl_init();

				curl_setopt($ch, CURLOPT_URL, $_POST['url']);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

				//pretend to be ff4
				$useragent="Mozilla/5.0 (X11; Linux x86_64; rv:2.0) Gecko/20100101 Firefox/4.0";
				curl_setopt($ch, CURLOPT_USERAGENT, $useragent);

				//for self-signed certificates
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
				@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

				$html = curl_exec($ch);
			} else {
				$html = @file_get_contents($_POST['url']);
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
				if(isset($match[1])){

					$charset = strtolower(trim($match[1]));
					if($charset!='utf-8')
						$html = String::to_utf8($html, $charset);
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

			$contents = @file_get_contents($_POST['url'].'/favicon.ico');

			if(!empty($contents)){
				$relpath = 'public/bookmarks/';
				$path = $GO_CONFIG->file_storage_path.$relpath;
				if(!is_dir($path))
					mkdir($path,0755, true);

				$filename = str_replace('.','_',preg_replace('/^https?:\/\//','', $_POST['url'])).'.ico';
        $filename = rtrim(str_replace('/','_',$filename),'_ ');

        //var_dump($filename);

				file_put_contents($path.$filename, $contents);

				$response['logo']=$relpath.$filename;
			}

			break;

		case 'thumbdir':
			$thumbs = $bookmarks->thumbdir_images();

			$response['results'] = $thumbs;
			$response['success'] = true;
			break;

		case 'get_one_bookmark':

			$bookmark = $bookmarks->get_one_bookmark(($_REQUEST['thumb_id']));
			$response['results'] = $bookmark;
			$response['success'] = true;
			break;

		case 'get_bookmarks':

			$start = isset($_REQUEST['start']) ? ($_REQUEST['start']) : '0';
			$limit = isset($_REQUEST['limit']) ? ($_REQUEST['limit']) : '0';
			$category = isset($_REQUEST['category']) ? ($_REQUEST['category']) : '0';
			$query = !empty($_REQUEST['query']) ? '%' . ($_REQUEST['query']) . '%' : '';

			$bookmarks->get_authorized_bookmarks($GO_SECURITY->user_id, $query, $start, $limit, $category);

			$response['results'] = array();
			$response['total'] = $bookmarks->found_rows(); // paging
			$index = 0;
			while ($bookmark = $bookmarks->next_record()) {

				if ($bookmark['acl_id'] == 0) {
					$bookmark['write_permission'] = true;
				} else {
					$bookmark['write_permission'] = $GO_SECURITY->has_permission($GO_SECURITY->user_id, $bookmark['acl_id']) > GO_SECURITY::READ_PERMISSION;
				}

				//$bookmark['description']=nl2br($bookmark['description']);
				$bookmark['thumb'] = '';
				if (!empty($bookmark['logo'])) {
					if ($bookmark['public_icon'] == '1') {
						$bookmark['thumb'] = $GO_MODULES->modules['bookmarks']['url'] . $bookmark['logo'];
					} else {
						$bookmark['thumb'] = get_thumb_url($bookmark['logo'], 16, 16, 0);
					}
				}
				$bookmark['index'] = $index;
				$index++;

				$response['results'][] = $bookmark;
			}
			break;

		case 'category':
			$category = $bookmarks->get_category(($_REQUEST['category_id']));
			require_once($GO_CONFIG->class_path.'base/users.class.inc.php');
			$GO_USERS = new GO_USERS();

			$category['user_name']=$GO_USERS->get_user_realname($category['user_id']);
			$category['public'] = $category['acl_id'] > 0 ? '1' : '0';
			$category['write_permission'] = $GO_SECURITY->has_permission($GO_SECURITY->user_id, $category['acl_id']) > GO_SECURITY::READ_PERMISSION;
			$response['data'] = $category;
			$response['success'] = true;
			break;



		case 'categories':
			$auth_type = isset($_POST['auth_type']) ? ($_POST['auth_type']) : 'write';

			if (isset($_POST['delete_keys'])) {
				try {
					$response['deleteSuccess'] = true;

					$delete_categories = json_decode($_POST['delete_keys']);
					foreach ($delete_categories as $category_id) {
						$bookmarks->delete_category($category_id);
					}
				} catch (Exception $e) {
					$response['deleteSuccess'] = false;
					$response['deleteFeedback'] = $e->getMessage();
				}
			}

			$sort = isset($_REQUEST['sort']) ? ($_REQUEST['sort']) : 'name';
			$dir = isset($_REQUEST['dir']) ? ($_REQUEST['dir']) : 'ASC';
			$start = isset($_REQUEST['start']) ? ($_REQUEST['start']) : '0';
			$limit = isset($_REQUEST['limit']) ? ($_REQUEST['limit']) : '0';
			$combo = isset($_REQUEST['combo']) ? ($_REQUEST['combo']) : '0';


			$query = ''; //isset($_REQUEST['query']) ? '%'.($_REQUEST['query']).'%' : '';

			$response['total'] = $bookmarks->get_authorized_categories($auth_type, $GO_SECURITY->user_id, $query, $sort, $dir, $start, $limit);
			if (!$response['total']) {

				$response['total'] = $bookmarks->get_authorized_categories($auth_type, $GO_SECURITY->user_id, $query, $sort, $dir, $start, $limit);
			}

			$response['results'] = array();

			require_once($GO_CONFIG->class_path.'base/users.class.inc.php');
			$GO_USERS = new GO_USERS();

			while ($category =$bookmarks->next_record()){
				$category['user_name']=$GO_USERS->get_user_realname($category['user_id']);
				$response['results'][] = $category;
			}

			break;
	}
} catch (Exception $e) {
	$response['feedback'] = $e->getMessage();
	$response['success'] = false;
}

echo json_encode($response);