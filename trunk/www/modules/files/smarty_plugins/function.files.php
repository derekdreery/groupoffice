<?php
function smarty_function_files($params, &$smarty)
{
	global $co, $GO_CONFIG;

	if(empty($params['path']))
	{
		return 'No path specified in files function!';
	}

	if(empty($params['template']))
	{
		return 'No template specified in files function!';
	}

	$fs = new filesystem();

	$files = $fs->get_files_sorted($GO_CONFIG->file_storage_path.$params['path']);

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