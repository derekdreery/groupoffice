<?php
function smarty_function_files($params, &$smarty)
{
	global $co, $GO_CONFIG;

	if(empty($params['path']))
	{
		$images_path = $smarty->_tpl_vars['images_path'];
		
		$path = $images_path.$co->build_path($co->folder['id'], $co->site['root_folder_id']).$co->file['name'];
		
		if(!is_dir($path))
		{
			return 'Could not find path: '.$path;
		}
		
	}else
	{
		$path = $GO_CONFIG->file_storage_path.$params['path'];
	}

	if(empty($params['template']))
	{
		return 'No template specified in files function!';
	}

	$fs = new filesystem();

	$files = $fs->get_files_sorted($path);

	//var_dump($files);
	$html = '';

	$uneven=true;
	$s = new cms_smarty($co);
	for($i=0;$i<count($files);$i++)
	{
		
		$s->assign('file', $files[$i]);
		$s->assign('even', $uneven ? 'uneven' : 'even');

		$html .= $s->fetch($params['template']);
		
		$uneven=!$uneven;
	}
	
	return $html;

}
?>