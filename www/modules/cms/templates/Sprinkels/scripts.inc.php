
<?php
if($co->file['type']=='shuffle'){
	global $cms;
	
	function get_items($folder_id){
		global $GO_SECURITY, $co;
		$items = $co->get_authorized_items($folder_id, $GO_SECURITY->user_id, true, false);

		$allitems = array();
		while($item = array_shift($items)){
			$item['option_values']=$co->get_template_values($item['option_values']);
			if($item['fstype']=='file'){
				if($item['type']!='shuffle')
					$allitems[]=$item;
			}else
			{
				$allitems=array_merge($allitems, get_items($item['id']));
			}
		}
		return $allitems;
	}

	global $key;
	
	$key = 'shuffle_'.$co->file['id'];


	if(!isset($_SESSION['GO_SESION']['cms'][$key]) || !isset($_GET['shuffle_index']))
	{
		$_SESSION['GO_SESION']['cms'][$key]=!empty($co->folder['id']) ? get_items($co->folder['id']) : array();
		shuffle($_SESSION['GO_SESION']['cms'][$key]);
	}

	$shuffle_index = isset($_GET['shuffle_index']) ? $_GET['shuffle_index'] : 0;

	function add_shuffle_index($shuffle_index, $increment){
		global $key;


		if(!isset($_SESSION['GO_SESION']['cms'][$key][$shuffle_index+$increment]))
			return false;

		$url = $_SERVER['REQUEST_URI'];
		if(strpos($url, 'shuffle_index')){
			$url = str_replace('shuffle_index='.$shuffle_index, 'shuffle_index='.($shuffle_index+$increment),$url);
		}else
		{
			$url = String::add_params_to_url($url, 'shuffle_index='.($shuffle_index+$increment));
		}
		return $url;
	}


	$shuffle['previous']=add_shuffle_index($shuffle_index, -1);
	$shuffle['next']=add_shuffle_index($shuffle_index, 1);

	//var_dump($shuffle);
	
	if(isset($_SESSION['GO_SESION']['cms'][$key][$shuffle_index])){
		$this->assign('folder', $cms->get_folder($_SESSION['GO_SESION']['cms'][$key][$shuffle_index]['folder_id']));
		$this->assign('file', $_SESSION['GO_SESION']['cms'][$key][$shuffle_index]);
	}
	
	$this->assign('shuffle',$shuffle);
}

