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
require_once ($GO_MODULES->modules['bookmarks']['class_path'].'bookmarks.class.inc.php');
$bookmarks = new bookmarks();

$task=isset($_REQUEST['task']) ? $_REQUEST['task'] : '';

class thumbimage {
	var $filename;
}

try {

	switch($task) {


		case 'thumbdir': //

			$thumbs = $bookmarks->thumbdir_images();

			$response['results']=$thumbs;
			$response['success']=true;

			break;

		case 'get_one_bookmark':

			$bookmark = $bookmarks->get_one_bookmark(($_REQUEST['thumb_id']));
			$response['results']=$bookmark;
			$response['success']=true;

			break;


		case 'get_bookmarks':

			$start = isset($_REQUEST['start']) ? ($_REQUEST['start']) : '0';
			$limit = isset($_REQUEST['limit']) ? ($_REQUEST['limit']) : '0';
			$category = isset($_REQUEST['category']) ? ($_REQUEST['category']) : '0';
			$query = isset($_REQUEST['query']) ? '%'.($_REQUEST['query']).'%' : '';

			$bookmarks->get_authorized_bookmarks($GO_SECURITY->user_id,$query,$start,$limit,$category);

			$response['results']=array();
			$response['total'] = $bookmarks->found_rows(); // paging
			$index=0;
			while($bookmark = $bookmarks->next_record()) {

				if ($bookmark['acl_id']==0) {
					$bookmark['write_permission']=true;
				}
				else {
					$bookmark['write_permission']=$GO_SECURITY->has_permission($GO_SECURITY->user_id, $bookmark['acl_id'])>GO_SECURITY::READ_PERMISSION;
				}

				$bookmark['thumb']=empty($bookmark['logo']) ? '' : $bookmarks->bm_thumb_url($bookmark['logo'],32,32,0);
				$bookmark['index']=$index;
				$index++;

				$response['results'][] = $bookmark;


			}


			break;


		case 'category':
			$category = $bookmarks->get_category(($_REQUEST['category_id']));
			$user = $GO_USERS->get_user($category['user_id']);
			$category['user_name']=String::format_name($user);
			$category['write_permission']=$GO_SECURITY->has_permission($GO_SECURITY->user_id, $category['acl_id'])>GO_SECURITY::READ_PERMISSION;
			$response['data']=$category;
			$response['success']=true;
			break;



		case 'categories':
			$auth_type = isset($_POST['auth_type']) ? ($_POST['auth_type']) : 'write';

			if(isset($_POST['delete_keys'])) {
				try {
					$response['deleteSuccess']=true;

					$delete_categories = json_decode($_POST['delete_keys']);
					foreach($delete_categories as $category_id) {

						$cat = $bookmarks->get_category($category_id);
						$usr = $GO_USERS->get_user($cat['user_id']);

						if ($usr==$GO_SECURITY->user_id) {
							throw new AccessDeniedException();
						}


						$bookmarks->delete_category($category_id);
					}

				}catch(Exception $e) {
					$response['deleteSuccess']=false;
					$response['deleteFeedback']=$e->getMessage();
				}
			}


			$sort = isset($_REQUEST['sort']) ? ($_REQUEST['sort']) : 'name';
			$dir = isset($_REQUEST['dir']) ? ($_REQUEST['dir']) : 'ASC';
			$start = isset($_REQUEST['start']) ? ($_REQUEST['start']) : '0';
			$limit = isset($_REQUEST['limit']) ? ($_REQUEST['limit']) : '0';
			$combo = isset($_REQUEST['combo']) ? ($_REQUEST['combo']) : '0';


			$query = '';//isset($_REQUEST['query']) ? '%'.($_REQUEST['query']).'%' : '';

			$response['total'] = $bookmarks->get_authorized_categories($auth_type, $GO_SECURITY->user_id, $query, $sort, $dir, $start, $limit);
			if(!$response['total']) {

				$response['total'] = $bookmarks->get_authorized_categories($auth_type, $GO_SECURITY->user_id, $query, $sort, $dir, $start, $limit);
			}


			$response['results']=array();

			while($bookmarks->next_record()) {

				$category = $bookmarks->record;

				$user = $GO_USERS->get_user($category['user_id']);
				$category['user_name']=String::format_name($user);
				$response['results'][] = $category;

			}



			break;




	}

















} catch(Exception $e) {

}

echo json_encode($response);




?>
