<?php
require_once(GO::config()->class_path.'smarty/Smarty.class.php');

class cms_smarty extends Smarty{

	var $cms_output;
	
	function __construct(){
		parent::Smarty();
		
		global $GO_MODULES, $GO_CONFIG, $co, $GO_LANGUAGE;
		
		//$this->plugins_dir[] = GO::modules()->modules['cms']['path'].'smarty_plugins';
		
		foreach(GO::modules()->modules as $module)
		{
			if(is_dir($module['path'].'smarty_plugins'))
			{
				$this->plugins_dir[] = $module['path'].'smarty_plugins';
			}
		}
		
		//var_dump($this->plugins_dir);
		
		$this->template_dir=GO::modules()->modules['cms']['path'].'templates/'.$co->site['template'];
		$this->compile_dir=GO::config()->orig_tmpdir.'cms/'.$co->site['id'].'/templates_c';
		if(!is_dir($this->compile_dir))
			mkdir($this->compile_dir,0755, true);

		$this->cms_output=$co;
		
		$this->assign('site', $co->site);
		$this->assign('session', $_SESSION['GO_SESSION']);
		$this->assign('cms_url', GO::modules()->modules['cms']['url']);
		$this->assign('template_path', GO::modules()->modules['cms']['path'].'templates/'.$co->site['template'].'/');
		$this->assign('template_url', GO::modules()->modules['cms']['url'].'templates/'.$co->site['template'].'/');
		$this->assign('go_url', GO::config()->host);
		$this->assign('go_root_path', GO::config()->root_path);
		$this->assign('file_storage_path', GO::config()->file_storage_path);
		$this->assign('modules', GO::modules()->modules);
		$this->assign('images_url', GO::modules()->modules['files']['url'].'download.php?path='.urlencode('public/cms/'.File::strip_invalid_chars($co->site['name']).'/'));
		$this->assign('images_path', GO::config()->file_storage_path.'public/cms/'.File::strip_invalid_chars($co->site['name']).'/');
		
		if(isset($co->folder))
		{
			$co->folder['safename']=preg_replace($co->safe_regex, '', $co->folder['name']);
				
			$this->assign('folder', $co->folder);
		}
		
		if(isset($co->file))
		{
			$co->file['safename']=preg_replace($co->safe_regex, '', $co->file['name']);
			$this->load_plugins();
			$this->assign('file', $co->file);
		}

		$this->assign('head', $co->head);
		
		//process scripts
		if(file_exists(GO::modules()->modules['cms']['path'].'templates/'.$co->site['template'].'/scripts.inc.php'))
		{
			require_once(GO::modules()->modules['cms']['path'].'templates/'.$co->site['template'].'/scripts.inc.php');
		}
		
		$this->assign('lang', GO::language()->language);				
	}

	function load_plugins(){
		global $GO_MODULES, $co;
		$tp = new TagParser();

		$tags = $tp->parseTag('cms:plugin', $co->file['content']);

		foreach($tags as $tag){

			require_once(GO::modules()->modules[$tag['attributes']['module']]['path'].'smarty_plugins/function.'.$tag['attributes']['type'].'.php');

			$function = 'smarty_function_'.$tag['attributes']['type'];

			$co->file['content']= substr($co->file['content'],0, $tag['startPos']).$function($tag['attributes'], $this).substr($co->file['content'],$tag['startPos']+strlen($tag['tag']));

		}
	}

}
?>