<?php
function smarty_function_cms_href($params, &$smarty)
{
	global $co, $GO_MODULES;

	if(!isset($params['path']) && isset($params['folder_id'])){
		$params['path']=$co->build_path($params['folder_id'], $co->site['root_folder_id']);
	}

	if(!isset($params['path']))
	{
		$params['path']='';
	}
	
	$co->build_path($folder_id);
	
	$site_id = $smarty->_tpl_vars['site']['id'];
	
		if($co->basehref!=$GO_MODULES->modules['cms']['url'])
		{
			//we do rewriting
			$url = $co->basehref;
		}else
		{
			//we use the ugly URL
			$url = $GO_MODULES->modules['cms']['url'].'run.php?basehref='.urlencode($GO_MODULES->modules['cms']['url']).'&amp;site_id='.$site_id.'&amp;path=';
		}
	
	$url .= $params['path'];
	if(!empty($params['params']))
	{
		$url .= '&amp;'.$params['params'];
	}
	return $url;
}
