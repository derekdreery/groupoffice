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

$root_uri = $GO_CONFIG->debug ? $GO_CONFIG->host : $GO_CONFIG->root_path;
$local_uri = $GO_CONFIG->debug ? $GO_CONFIG->local_url : $GO_CONFIG->local_path;

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
$path = $GO_CONFIG->local_path.'cache/'.$file;
$url = $GO_CONFIG->local_url.'cache/'.$file;

if($GO_CONFIG->debug || !file_exists($path)) {
	if(!is_dir($GO_CONFIG->local_path.'cache')) {
		mkdir($GO_CONFIG->local_path.'cache', 0755, true);
	}

	//cleanup old cache
	$fs = new filesystem();
	$files = $fs->get_files_sorted($GO_CONFIG->local_path.'cache');
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

	require($GO_CONFIG->root_path.'language/languages.inc.php');
	$fp=fopen($GO_CONFIG->local_path.'cache/languages.js','w');
	fwrite($fp, "GO.Languages=[];\n");
	foreach($languages as $code=>$language) {
		fwrite($fp,'GO.Languages.push(["'.$code.'","'.$language.'"]);');
	}
	fclose($fp);
	$scripts[]=$local_uri.'cache/languages.js';


	include($GO_LANGUAGE->get_base_language_file('countries'));
	$fp=fopen($GO_CONFIG->local_path.'cache/countries.js','w');

	foreach($countries as $key=>$country) {
		fwrite($fp,'GO.lang.countries["'.$key.'"] = "'.$country.'";');
	}
	fclose($fp);
	$scripts[]=$local_uri.'cache/countries.js';

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
			file_put_contents($path,"\n\n/*".$script."*/\n\n".file_get_contents($script),FILE_APPEND);
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
		alert('The ExtJS javascripts were not loaded. Your host configuration properties are probably configured incorrectly');
	}
</script>
<?php

$scripts=array();

if($GO_SECURITY->logged_in()) {


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

			if(file_exists($module['path'].'language/en.js')) {
				$scripts[]=$module_uri.'language/en.js';
			}

			if($GO_LANGUAGE->language!='en' && file_exists($module['path'].'language/'.$GO_LANGUAGE->language.'.js')) {
				$scripts[]=$module_uri.'language/'.$GO_LANGUAGE->language.'.js';
			}

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

			$modules[]=$module['id'];
		}
	}

	//two modules may include the same script
	$scripts=array_unique($scripts);

	$file = $GO_SECURITY->user_id.'-'.md5($GO_CONFIG->mtime.filemtime($GO_CONFIG->root_path.'javascript/go-all-min').':'.$GO_LANGUAGE->language.':'.implode(':', $modules)).'.js';
	$path = $GO_CONFIG->local_path.'cache/'.$file;
	$url = $GO_CONFIG->local_url.'cache/'.$file;

	if(!$GO_CONFIG->debug) {
		if(!file_exists($path)) {

			file_put_contents($GO_CONFIG->local_path.'cache/modules.js', 'GO.settings.modules = Ext.decode("'.addslashes(json_encode($GO_MODULES->modules)).'");');
			array_unshift($scripts, $local_uri.'cache/modules.js');

			//cleanup old cache
			$fs = new filesystem();
			$files = $fs->get_files_sorted($GO_CONFIG->local_path.'cache');
			while($file=array_shift($files)) {
				if(substr($file['name'],0, 1)==$GO_SECURITY->user_id) {
					unlink($file['path']);
				}
			}
			foreach($scripts as $script) {
				file_put_contents($path,"\n\n/*".$script."*/\n\n".file_get_contents($script),FILE_APPEND);
			}
		}
		$scripts=array($url);
	}else
	{
		file_put_contents($GO_CONFIG->local_path.'cache/modules.js', 'GO.settings.modules = Ext.decode("'.addslashes(json_encode($GO_MODULES->modules)).'");');
		array_unshift($scripts, $local_uri.'cache/modules.js');
	}
	
	foreach($scripts as $script) {
		echo '<script type="text/javascript" src="'.$script.'"></script>'."\n";
	}

	$GO_SCRIPTS_JS='';
	foreach($GO_MODULES->modules as $module) {
		if($module['read_permission']) {
			if(file_exists($module['path'].'scripts.inc.php')) {
				require($module['path'].'scripts.inc.php');
			}
		}
	}


	$filename = $GO_SECURITY->user_id.'-scripts.js';
	$path = $GO_CONFIG->local_path.'cache/'.$filename;

	if($GO_SCRIPTS_JS!=@file_get_contents($path)){
		file_put_contents($path, $GO_SCRIPTS_JS);
	}
	if(file_exists($path)){
		echo '<script type="text/javascript" src="'.$GO_CONFIG->local_url.'cache/'.$filename.'?mtime='.filemtime($path).'"></script>'."\n";
	}
}
?>




<script type="text/javascript">
	Ext.BLANK_IMAGE_URL = '<?php echo $GO_CONFIG->host; ?>ext/resources/images/default/s.gif';

	if(!GO.state.HttpProvider)
	{
		alert('The Group-Office javascripts were not loaded. Your local_url or local_path configuration properties are probably configured incorrectly');
	}

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