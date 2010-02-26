<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @copyright Copyright Intermesh
 * @version $Id$
 * @author Merijn Schering <mschering@intermesh.nl>
 */


$GO_EVENTS->fire_event('before_scripts_load');

$root_uri = $GO_CONFIG->debug ? $GO_CONFIG->host : $GO_CONFIG->root_path;

$scripts=array();
$scripts[]=$root_uri.'ext/adapter/ext/ext-base.js';

if($GO_CONFIG->debug) {
	$scripts[]=$root_uri.'ext/ext-all-debug.js';
}else {
	$scripts[]=$root_uri.'ext/ext-all.js';
}

$scripts[]=$root_uri.'javascript/namespaces.js';
?>
<script type="text/javascript">
	var BaseHref = '<?php echo $GO_CONFIG->host; ?>';

	GO = {};
	GO.settings=<?php echo json_encode($GO_CONFIG->get_client_settings()); ?>;

<?php
if(isset($_REQUEST['after_login_url'])) {
	$after_login_url = $_REQUEST['after_login_url'];
}else {
	$after_login_url = $_SERVER['PHP_SELF'];

	$params = array();
	foreach($_GET as $key=>$value) {
		if($key!='task' || $value!='logout') {
			$params[] =$key.'='.urlencode($value);
		}
	}

	if(count($params)) {
		$after_login_url .= '?'.implode('&', $params);
	}
}
echo 'GO.afterLoginUrl="'.$after_login_url.'";';

$fullscreen = isset($_COOKIE['GO_FULLSCREEN']) && $_COOKIE['GO_FULLSCREEN']=='1' ? 'true' : 'false';
echo 'GO.fullscreen='.$fullscreen.';';

if($fullscreen=='false') {
	echo 'window.name="groupoffice";';
}

/*
 * If fullscreen mode is enabled and the user is already logged in we set $popup_groupoffice with the URL to load Group-Office
 * in.
 *
 * In themes/Default/layout.inc.php we handle this var.
 */
if($GO_SECURITY->logged_in() && $fullscreen=='true' && !isset($_REQUEST['fullscreen_loaded'])) {
	$popup_groupoffice = isset($_REQUEST['after_login_url']) ? smart_stripslashes($_REQUEST['after_login_url']) : $GO_CONFIG->host;
	$popup_groupoffice = String::add_params_to_url($popup_groupoffice, 'fullscreen_loaded=true');
}
?>
</script>
<?php
if(!isset($lang['common']['extjs_lang'])) $lang['common']['extjs_lang'] = $GO_LANGUAGE->language;

$file = 'base-'.md5($GO_LANGUAGE->language.$GO_CONFIG->mtime).'.js';
$path = $GO_CONFIG->file_storage_path.'cache/'.$file;
$url = $GO_CONFIG->host.'compress.php?file='.$file;

if($GO_CONFIG->debug || !file_exists($path)) {
	
	//cleanup old cache
	$fs = new filesystem();
	$files = $fs->get_files_sorted($GO_CONFIG->file_storage_path.'cache');
	while($file=array_shift($files)) {
		if(substr($file['name'],0, 7)=='base-'.$GO_LANGUAGE->language) {
			unlink($file['path']);
		}
	}
	echo "\n<!-- regenerated script -->\n";

	$scripts[]=$root_uri.'language/common/en.js';
	$scripts[]=$root_uri.'modules/users/language/en.js';

	if($GO_LANGUAGE->language!='en') {
		if(file_exists($GO_CONFIG->root_path.'language/common/'.$GO_LANGUAGE->language.'.js')) {
			$scripts[]=$root_uri.'language/common/'.$GO_LANGUAGE->language.'.js';
		}

		if(file_exists($GO_CONFIG->root_path.'ext/build/locale/ext-lang-'.$lang['common']['extjs_lang'].'.js')) {
			$scripts[]=$root_uri.'ext/build/locale/ext-lang-'.$lang['common']['extjs_lang'].'.js';
		}

		if(file_exists($GO_CONFIG->root_path.'modules/users/language/'.$GO_LANGUAGE->language.'.js')) {
			$scripts[]=$root_uri.'modules/users/language/'.$GO_LANGUAGE->language.'.js';
		}
	}

	$dynamic_debug_scripts=array();

	require($GO_CONFIG->root_path.'language/languages.inc.php');
	$fp=fopen($GO_CONFIG->file_storage_path.'cache/languages.js','w');
	fwrite($fp, "GO.Languages=[];\n");
	foreach($languages as $code=>$language) {
		fwrite($fp,'GO.Languages.push(["'.$code.'","'.$language.'"]);');
	}
	fclose($fp);
	if(!$GO_CONFIG->debug){
		$scripts[]=$GO_CONFIG->file_storage_path.'cache/languages.js';
	}else
	{
		$dynamic_debug_script=$GO_CONFIG->file_storage_path.'cache/languages.js';
		$scripts[]=$GO_CONFIG->host.'compress.php?file=languages.js&mtime='.filemtime($dynamic_debug_script);
	}

	include($GO_LANGUAGE->get_base_language_file('countries'));
	//array_multisort($countries);
	$fp=fopen($GO_CONFIG->file_storage_path.'cache/countries.js','w');

	foreach($countries as $key=>$country) {
		fwrite($fp,'GO.lang.countries["'.$key.'"] = "'.$country.'";');
	}
	fclose($fp);
	if(!$GO_CONFIG->debug){
		$scripts[]=$GO_CONFIG->file_storage_path.'cache/countries.js';
	}else
	{
		$dynamic_debug_script=$GO_CONFIG->file_storage_path.'cache/countries.js';
		$scripts[]=$GO_CONFIG->host.'compress.php?file=countries.js&mtime='.filemtime($dynamic_debug_script);
	}

	if($GO_CONFIG->debug) {
		$data = file_get_contents($GO_CONFIG->root_path.'/javascript/scripts.txt');
		$lines = explode("\n", $data);
		foreach($lines as $line) {
			if(!empty($line)) {
				$scripts[]=$root_uri.$line;
			}
		}
	}else {
		$scripts[]=$root_uri.'javascript/go-all-min';
	}

	

	if(!$GO_CONFIG->debug) {
		foreach($scripts as $script) {
			file_put_contents($path,"\n\n".file_get_contents($script),FILE_APPEND);
		}
	}
}

if(!$GO_CONFIG->debug) {
	$scripts=array();
	$scripts[]=$url;
}

foreach($scripts as $script) {
	echo '<script type="text/javascript" src="'.$script.'"></script>'."\n";
}
?>
<script type="text/javascript">
	if(typeof(Ext)=='undefined')
	{
		alert('The Group-Office javascripts were not loaded. Check the host property in config.php and see if the file_storage_path folder and it\'s contents are writable');
	}
</script>
<?php

foreach($GO_MODULES->modules as $module) {
	if(file_exists($module['path'].'logged_off_scripts.inc.php')) {
		require($module['path'].'logged_off_scripts.inc.php');
	}
}

$scripts=array();


if($GO_SECURITY->logged_in()) {
	//load language first so it can be overridden
	foreach($GO_MODULES->modules as $module) {
		if($module['read_permission']) {

			$module_uri = $GO_CONFIG->debug ? $module['url'] : $module['path'];

			if(file_exists($module['path'].'language/en.js')) {
				$scripts[]=$module_uri.'language/en.js';
			}

			if($GO_LANGUAGE->language!='en' && file_exists($module['path'].'language/'.$GO_LANGUAGE->language.'.js')) {
				$scripts[]=$module_uri.'language/'.$GO_LANGUAGE->language.'.js';
			}
		}
	}

	$scripts[]=$root_uri.'javascript/LanguageLoaded.js';


	foreach($GO_MODULES->modules as $module) {
		if($module['read_permission']) {
			if(file_exists($module['path'].'prescripts.inc.php')) {
				require($module['path'].'prescripts.inc.php');
			}
		}
	}


	$modules=array();
	foreach($GO_MODULES->modules as $module) {
		if($module['read_permission']) {

			$module_uri = $GO_CONFIG->debug ? $module['url'] : $module['path'];

			if(file_exists($module['path'].'scripts.txt') && $GO_CONFIG->debug) {
				$data = file_get_contents($module['path'].'scripts.txt');
				$lines = explode("\n", $data);
				foreach($lines as $line) {
					if(!empty($line)) {
						$scripts[]=$root_uri.$line;
					}
				}
			}else {
				if(file_exists($module['path'].'all-module-scripts-min')) {
					$scripts[]=$module_uri.'all-module-scripts-min';
				}
			}

			$modules[]=$module['id'].$module['write_permission'];
		}
	}

	//two modules may include the same script
	$scripts=array_unique($scripts);

	//include config file location because in some cases different URL's point to
	//the same database and this can break things if the settings are cached.
	$file = $GO_SECURITY->user_id.'-'.md5($GO_CONFIG->mtime.$GO_CONFIG->get_config_file().filemtime($GO_CONFIG->root_path.'javascript/go-all-min').':'.$GO_LANGUAGE->language.':'.implode(':', $modules)).'.js';
	$path = $GO_CONFIG->file_storage_path.'cache/'.$file;
	$url = $GO_CONFIG->host.'compress.php?file='.$file;
	
	if(!$GO_CONFIG->debug) {
		if(!file_exists($path)) {
		
			//cleanup old cache
			$fs = new filesystem();
			$files = $fs->get_files_sorted($GO_CONFIG->file_storage_path.'cache');
			while($file=array_shift($files)) {
				if(substr($file['name'],0, 1)==$GO_SECURITY->user_id) {
					unlink($file['path']);
				}
			}

			file_put_contents($GO_CONFIG->file_storage_path.'cache/'.$GO_SECURITY->user_id.'-modules.js', 'GO.settings.modules = Ext.decode("'.addslashes(json_encode($GO_MODULES->modules)).'");');
			array_unshift($scripts, $GO_CONFIG->file_storage_path.'cache/'.$GO_SECURITY->user_id.'-modules.js');


			foreach($scripts as $script) {
				file_put_contents($path,"\n\n".file_get_contents($script),FILE_APPEND);
			}
		}

		$scripts=array($url);

	}else
	{
		file_put_contents($GO_CONFIG->file_storage_path.'cache/'.$GO_SECURITY->user_id.'-modules.js', 'GO.settings.modules = Ext.decode("'.addslashes(json_encode($GO_MODULES->modules)).'");');
		array_unshift($scripts, $GO_CONFIG->host.'compress.php?file='.$GO_SECURITY->user_id.'-modules.js&mtime='.filemtime($GO_CONFIG->file_storage_path.'cache/'.$GO_SECURITY->user_id.'-modules.js'));
	}
	
	foreach($scripts as $script) {
		echo '<script type="text/javascript" src="'.$script.'"></script>'."\n";
	}

	/*
	 * The GO_SCRIPTS_JS variable can be filled with javascript code and will be
	 * executed when Group-Office loads for the first time.
	 * Modules can add stuff in their scripts.inc.php files.
	 */

	$GO_SCRIPTS_JS='';

	foreach($GO_MODULES->modules as $module) {
		if($lang_file = $GO_LANGUAGE->get_language_file($module['id'])) {
			$GO_LANGUAGE->require_language_file($module['id']);
		}
	}

	//The checked values is used in the SearchPanel.js for the filter
	$types = $GO_CONFIG->get_setting('link_type_filter', $GO_SECURITY->user_id);
	$types = empty($types) ? array() : explode(',', $types);

	$link_types=array();
	asort($lang['link_type']);
	foreach($lang['link_type'] as $id=>$name) {
		$type['id']=$id;
		$type['name']=$name;
		$type['checked']=in_array($id, $types);
		$link_types[] = $type;
	}

	$GO_SCRIPTS_JS .= 'GO.linkTypes='.json_encode($link_types).';';

	require_once($GO_CONFIG->class_path.'export/export_query.class.inc.php');
	$eq = new export_query();

	$GO_SCRIPTS_JS.=$eq->find_custom_exports();

	
	foreach($GO_MODULES->modules as $module) {
		if($module['read_permission']) {
			if(file_exists($module['path'].'scripts.inc.php')) {
				require($module['path'].'scripts.inc.php');
			}
		}
	}


	
	

	$filename = $GO_SECURITY->user_id.'-scripts.js';
	$path = $GO_CONFIG->file_storage_path.'cache/'.$filename;

	if($GO_SCRIPTS_JS!=@file_get_contents($path)){
		file_put_contents($path, $GO_SCRIPTS_JS);
	}
	if(file_exists($path)){

		$url = $GO_CONFIG->host.'compress.php?file='.$filename.'&mtime='.filemtime($path);
		echo '<script type="text/javascript" src="'.$url.'"></script>'."\n";
	}
}
?>
<script type="text/javascript">
	Ext.BLANK_IMAGE_URL = '<?php echo $GO_CONFIG->host; ?>ext/resources/images/default/s.gif';

	Ext.state.Manager.setProvider(new GO.state.HttpProvider({url: BaseHref+'state.php'}));
</script>
<?php
if(file_exists($GO_THEME->theme_path.'MainLayout.js')) {
	echo '<script src="'.$GO_THEME->theme_url.'MainLayout.js" type="text/javascript"></script>';
	echo "\n";
}
?>
<script type="text/javascript">
<?php
if(isset($_GET['module']))
{
    $module = isset($_GET['module']) ? $_GET['module'] : false;
    $function = isset($_GET['function']) ? $_GET['function'] : false;
    $params = isset($_GET['params']) ? ($_GET['params']) : false;

    if($module && $function && $params)
    {
    ?>
    if(GO.<?php echo $module; ?>)
    {
        GO.mainLayout.onReady(function(){
            GO.<?php echo $module; ?>.<?php echo $function; ?>({
                values: <?php echo $params; ?>
            });
        });
    }
    <?php
    }
}
?>
</script>