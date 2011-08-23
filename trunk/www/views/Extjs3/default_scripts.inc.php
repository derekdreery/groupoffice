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

$root_uri = $GLOBALS['GO_CONFIG']->debug ? $GLOBALS['GO_CONFIG']->host : $GLOBALS['GO_CONFIG']->root_path;
$view_root_uri = $root_uri.'views/Extjs3/';

$scripts=array();
//important to load focus first
$scripts[]=$view_root_uri.'javascript/focus.js';


//if($GLOBALS['GO_CONFIG']->debug) {
	$scripts[]=$view_root_uri.'ext/adapter/ext/ext-base-debug.js';
	$scripts[]=$view_root_uri.'ext/ext-all-debug.js';
//}else {
//	$scripts[]=$root_uri.'ext/adapter/ext/ext-base.js';
//	$scripts[]=$root_uri.'ext/ext-all.js';
//}

$scripts[]=$view_root_uri.'javascript/namespaces.js';
?>
<script type="text/javascript">
	var BaseHref = '<?php echo $GLOBALS['GO_CONFIG']->host; ?>';

	GO = {};
	GO.settings=<?php echo json_encode($GLOBALS['GO_CONFIG']->get_client_settings()); ?>;
	GO.calltoTemplate = '<?php echo $GLOBALS['GO_CONFIG']->callto_template; ?>';
	
	
	
	GO.permissionLevels={
		read:1,
		write:2,
		writeAndDelete:3,
		manage:4		
	};

<?php

//some functions require extra security

if(isset($_SESSION['GO_SESSION']['security_token'])){	
	echo 'GO.securityToken="'.$_SESSION['GO_SESSION']['security_token'].'";';
}

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

if(isset($_REQUEST['SET_LANGUAGE']))
	echo 'GO.loginSelectedLanguage="'.$_REQUEST['SET_LANGUAGE'].'";';

echo 'GO.afterLoginUrl="'.String::xss_clean($after_login_url).'";';

$fullscreen = isset($_COOKIE['GO_FULLSCREEN']) && $_COOKIE['GO_FULLSCREEN']=='1' ? 'true' : 'false';
echo 'GO.fullscreen='.$fullscreen.';';

/*
 * If fullscreen mode is enabled and the user is already logged in we set $popup_groupoffice with the URL to load Group-Office
 * in.
 *
 * In themes/Default/layout.inc.php we handle this var.
 */
if($GLOBALS['GO_SECURITY']->logged_in() && $fullscreen=='true' && !isset($_REQUEST['fullscreen_loaded'])) {
	$popup_groupoffice = isset($_REQUEST['after_login_url']) ? smart_stripslashes($_REQUEST['after_login_url']) : $GLOBALS['GO_CONFIG']->host;
	$popup_groupoffice = String::add_params_to_url($popup_groupoffice, 'fullscreen_loaded=true');
}

if($GLOBALS['GO_SECURITY']->logged_in() && !isset($popup_groupoffice)) {
	echo 'window.name="groupoffice";';
}else
{
	echo 'window.name="groupoffice-login";';
}

?>
</script>
<?php
if(!isset($lang['common']['extjs_lang'])) $lang['common']['extjs_lang'] = $GLOBALS['GO_LANGUAGE']->language;

$file = 'base-'.md5($GLOBALS['GO_LANGUAGE']->language.$GLOBALS['GO_CONFIG']->mtime).'.js';
$path = $GLOBALS['GO_CONFIG']->file_storage_path.'cache/'.$file;
$url = $GLOBALS['GO_CONFIG']->host.'compress.php?file='.$file;

if($GLOBALS['GO_CONFIG']->debug || !file_exists($path)) {
	
	//cleanup old cache
	require_once($GLOBALS['GO_CONFIG']->class_path.'filesystem.class.inc');
	$fs = new filesystem();
	/*$files = $fs->get_files_sorted($GLOBALS['GO_CONFIG']->file_storage_path.'cache');
	while($file=array_shift($files)) {
		if(substr($file['name'],0, 7)=='base-'.$GLOBALS['GO_LANGUAGE']->language) {
			unlink($file['path']);
		}
	}*/
	echo "\n<!-- regenerated script -->\n";

//	$scripts[]=$root_uri.'language/common/en.js';
//	$scripts[]=$root_uri.'modules/users/language/en.js';
//
//	if($GLOBALS['GO_LANGUAGE']->language!='en') {
//		if(file_exists($GLOBALS['GO_CONFIG']->root_path.'language/common/'.$GLOBALS['GO_LANGUAGE']->language.'.js')) {
//			$scripts[]=$root_uri.'language/common/'.$GLOBALS['GO_LANGUAGE']->language.'.js';
//		}
//
//		if(file_exists($GLOBALS['GO_CONFIG']->root_path.'ext/src/locale/ext-lang-'.$lang['common']['extjs_lang'].'.js')) {
//			$scripts[]=$view_root_uri.'ext/src/locale/ext-lang-'.$lang['common']['extjs_lang'].'.js';
//		}
//
//		if(file_exists($GLOBALS['GO_CONFIG']->root_path.'modules/users/language/'.$GLOBALS['GO_LANGUAGE']->language.'.js')) {
//			$scripts[]=$root_uri.'modules/users/language/'.$GLOBALS['GO_LANGUAGE']->language.'.js';
//		}
//	}

	$dynamic_debug_scripts=array();

	require($GLOBALS['GO_CONFIG']->root_path.'language/languages.inc.php');
	$fp=fopen($GLOBALS['GO_CONFIG']->file_storage_path.'cache/languages.js','w');
	if(!$fp){
		die('Could not write to cache directory');
	}
	fwrite($fp, "GO.Languages=[];\n");
	
	
	//Temporary dirty hack for namespaces
	$stmt = GO::modules()->getAll();
	while($module = $stmt->fetch()){
		fwrite($fp, 'Ext.ns("GO.'.$module->id.'");');
	}
	
	fwrite($fp, 'Ext.ns("GO.portlets");');
	fwrite($fp, 'Ext.ns("GO.customfields.columns");');
	fwrite($fp, 'Ext.ns("GO.customfields.types");');
	

	//fwrite($fp,'GO.Languages.push(["",GO.lang.userSelectedLanguage]);');
	foreach($languages as $code=>$language) {
		fwrite($fp,'GO.Languages.push(["'.$code.'","'.$language.'"]);');
	}
	
	
	//Put all lang vars in js
	$language = new GO_Base_Language();
	$l = $language->getAllLanguage();
	
	fwrite($fp,'GO.lang='.json_encode($l['base']['common']).';');
	fwrite($fp,'GO.lang.countries='.json_encode($l['base']['countries']).';');
	unset($l['base']);
	foreach($l as $module=>$langVars){
		fwrite($fp,'GO.'.$module.'.lang='.json_encode($langVars).';');
	}
	
	
	
	
	fclose($fp);
	if(!$GLOBALS['GO_CONFIG']->debug){
		$scripts[]=$GLOBALS['GO_CONFIG']->file_storage_path.'cache/languages.js';
	}else
	{
		$dynamic_debug_script=$GLOBALS['GO_CONFIG']->file_storage_path.'cache/languages.js';
		$scripts[]=$GLOBALS['GO_CONFIG']->host.'compress.php?file=languages.js&mtime='.filemtime($dynamic_debug_script);
	}

	include($GLOBALS['GO_LANGUAGE']->get_base_language_file('countries'));
	//array_multisort($countries);
	$fp=fopen($GLOBALS['GO_CONFIG']->file_storage_path.'cache/countries.js','w');
	if(!$fp){
		die('Could not write to cache directory');
	}

	foreach($countries as $key=>$country) {
		fwrite($fp,'GO.lang.countries["'.$key.'"] = "'.$country.'";');
	}
	fclose($fp);
	if(!$GLOBALS['GO_CONFIG']->debug){
		$scripts[]=$GLOBALS['GO_CONFIG']->file_storage_path.'cache/countries.js';
	}else
	{
		$dynamic_debug_script=$GLOBALS['GO_CONFIG']->file_storage_path.'cache/countries.js';
		$scripts[]=$GLOBALS['GO_CONFIG']->host.'compress.php?file=countries.js&mtime='.filemtime($dynamic_debug_script);
	}

	if($GLOBALS['GO_CONFIG']->debug) {
		$data = file_get_contents($GLOBALS['GO_CONFIG']->root_path.'views/Extjs3/javascript/scripts.txt');
		$lines = explode("\n", $data);
		foreach($lines as $line) {
			if(!empty($line)) {
				$scripts[]=$root_uri.$line;
			}
		}
	}else {
		$scripts[]=$view_root_uri.'javascript/go-all-min';
	}

	

	if(!$GLOBALS['GO_CONFIG']->debug) {
		foreach($scripts as $script) {
			file_put_contents($path,"\n\n".file_get_contents($script),FILE_APPEND);
		}
	}
}

if(!$GLOBALS['GO_CONFIG']->debug) {
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
		alert('Could not load the application javascripts. Check the "host" property in config.php and see if the "file_storage_path" folder and it\'s contents are writable');
	}
</script>
<?php

foreach($GLOBALS['GO_MODULES']->modules as $module) {
	if(file_exists($module['path'].'logged_off_scripts.inc.php')) {
		require($module['path'].'logged_off_scripts.inc.php');
	}
	if(file_exists($module['path'].'views/Extjs3/logged_off_scripts.inc.php')) {
		require($module['path'].'views/Extjs3/logged_off_scripts.inc.php');
	}
}

$scripts=array();
$load_modules=array();
if(!isset($default_scripts_load_modules)){
	if($GLOBALS['GO_SECURITY']->logged_in() && !isset($popup_groupoffice)){
		$load_modules=$GLOBALS['GO_MODULES']->modules;
	}
}else
{
	foreach($default_scripts_load_modules as $module)
	{
		$GLOBALS['GO_MODULES']->modules[$module]['read_permission']=true;
		$load_modules[]=$GLOBALS['GO_MODULES']->modules[$module];
	}
}


//var_dump($load_modules);


if(count($load_modules)) {
	//load language first so it can be overridden
//	foreach($load_modules as $module) {
//		if($module['read_permission']) {
//
//			$module_uri = $GLOBALS['GO_CONFIG']->debug ? $module['url'] : $module['path'];
//
//			if(file_exists($module['path'].'language/en.js')) {
//				$scripts[]=$module_uri.'language/en.js';
//			}
//
//			if($GLOBALS['GO_LANGUAGE']->language!='en' && file_exists($module['path'].'language/'.$GLOBALS['GO_LANGUAGE']->language.'.js')) {
//				$scripts[]=$module_uri.'language/'.$GLOBALS['GO_LANGUAGE']->language.'.js';
//			}
//		}
//	}
//
//	$scripts[]=$root_uri.'javascript/LanguageLoaded.js';


	foreach($load_modules as $module) {
		if($module['read_permission']) {
			if(file_exists($module['path'].'prescripts.inc.php')) {
				require($module['path'].'prescripts.inc.php');
			}
			if(file_exists($module['path'].'views/Extjs3/prescripts.inc.php')) {
				require($module['path'].'views/Extjs3/prescripts.inc.php');
			}
		}
	}


	$modules=array();
	foreach($load_modules as $module) {
		if($module['read_permission']) {

			$module_uri = $GLOBALS['GO_CONFIG']->debug ? $module['url'] : $module['path'];
			
			$scriptsFile = $module['path'].'scripts.txt';
			if(!file_exists($scriptsFile))
						$scriptsFile = $module['path'].'views/Extjs3/scripts.txt';	

			if(file_exists($scriptsFile) && $GLOBALS['GO_CONFIG']->debug) {
				$data = file_get_contents($scriptsFile);
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
				if(file_exists($module['path'].'views/Extjs3/all-module-scripts-min')) {
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
	$file = $GLOBALS['GO_SECURITY']->user_id.'-'.md5($GLOBALS['GO_CONFIG']->mtime.$GLOBALS['GO_CONFIG']->get_config_file().filemtime($GLOBALS['GO_CONFIG']->root_path.'views/Extjs3/javascript/go-all-min').':'.$GLOBALS['GO_LANGUAGE']->language.':'.implode(':', $modules)).'.js';
	$path = $GLOBALS['GO_CONFIG']->file_storage_path.'cache/'.$file;
	$url = $GLOBALS['GO_CONFIG']->host.'compress.php?file='.$file;
	
	if(!$GLOBALS['GO_CONFIG']->debug) {
		if(!file_exists($path)) {
		
			//cleanup old cache
			require_once($GLOBALS['GO_CONFIG']->class_path.'filesystem.class.inc');
			$fs = new filesystem();
			/*$files = $fs->get_files_sorted($GLOBALS['GO_CONFIG']->file_storage_path.'cache');
			while($file=array_shift($files)) {
				if(substr($file['name'],0, 1)==$GLOBALS['GO_SECURITY']->user_id) {
					unlink($file['path']);
				}
			}*/

			file_put_contents($GLOBALS['GO_CONFIG']->file_storage_path.'cache/'.$GLOBALS['GO_SECURITY']->user_id.'-modules.js', 'GO.settings.modules = Ext.decode("'.addslashes(json_encode($GLOBALS['GO_MODULES']->modules)).'");');
			array_unshift($scripts, $GLOBALS['GO_CONFIG']->file_storage_path.'cache/'.$GLOBALS['GO_SECURITY']->user_id.'-modules.js');


			foreach($scripts as $script) {
				file_put_contents($path,"\n\n".file_get_contents($script),FILE_APPEND);
			}
		}

		$scripts=array($url);

	}else
	{
		file_put_contents($GLOBALS['GO_CONFIG']->file_storage_path.'cache/'.$GLOBALS['GO_SECURITY']->user_id.'-modules.js', 'GO.settings.modules = Ext.decode("'.addslashes(json_encode($GLOBALS['GO_MODULES']->modules)).'");');
		array_unshift($scripts, $GLOBALS['GO_CONFIG']->host.'compress.php?file='.$GLOBALS['GO_SECURITY']->user_id.'-modules.js&mtime='.filemtime($GLOBALS['GO_CONFIG']->file_storage_path.'cache/'.$GLOBALS['GO_SECURITY']->user_id.'-modules.js'));
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

	foreach($load_modules as $module) {
		$GLOBALS['GO_LANGUAGE']->require_language_file($module['id']);
	}

	//The checked values is used in the SearchPanel.js for the filter
	$types = $GLOBALS['GO_CONFIG']->get_setting('link_type_filter', $GLOBALS['GO_SECURITY']->user_id);
	$types = empty($types) ? array() : explode(',', $types);

	$link_types=array();
	if(isset($lang['link_type'])){
		asort($lang['link_type']);
		foreach($lang['link_type'] as $id=>$name) {
			$type['id']=$id;
			$type['name']=$name;
			$type['checked']=in_array($id, $types);
			$link_types[] = $type;
		}
	}

	$GO_SCRIPTS_JS .= 'GO.linkTypes='.json_encode($link_types).';';

	require_once($GLOBALS['GO_CONFIG']->class_path.'export/export_query.class.inc.php');
	$eq = new export_query();

	$GO_SCRIPTS_JS.=$eq->find_custom_exports();

	
	foreach($load_modules as $module) {
		if($module['read_permission']) {
			if(file_exists($module['path'].'scripts.inc.php')) {
				require($module['path'].'scripts.inc.php');
			}
			if(file_exists($module['path'].'views/Extjs3/scripts.inc.php')) {
				require($module['path'].'views/Extjs3/scripts.inc.php');
			}
		}
	}

	$GLOBALS['GO_EVENTS']->fire_event('load_scripts', array(&$GO_SCRIPTS_JS));	

	$filename = $GLOBALS['GO_SECURITY']->user_id.'-scripts.js';
	$path = $GLOBALS['GO_CONFIG']->file_storage_path.'cache/'.$filename;

	if($GO_SCRIPTS_JS!=@file_get_contents($path)){
		file_put_contents($path, $GO_SCRIPTS_JS);
	}
	if(file_exists($path)){

		$url = $GLOBALS['GO_CONFIG']->host.'compress.php?file='.$filename.'&mtime='.filemtime($path);
		echo '<script type="text/javascript" src="'.$url.'"></script>'."\n";
	}
}
?>
<script type="text/javascript">


	<?php $GLOBALS['GO_EVENTS']->fire_event('inline_scripts');	?>

	Ext.BLANK_IMAGE_URL = '<?php echo $GLOBALS['GO_CONFIG']->host; ?>views/Extjs3/ext/resources/images/default/s.gif';

	Ext.state.Manager.setProvider(new GO.state.HttpProvider({url: BaseHref+'state.php'}));
	//Ext.state.Manager.setProvider(new Ext.state.CookieProvider());


	//some functions require extra security
	<?php
	
	
	
	
	if(isset($_SESSION['GO_SESSION']['security_token']))		
		echo 'Ext.Ajax.extraParams={security_token:"'.$_SESSION['GO_SESSION']['security_token'].'"};';
	?>
</script>
<?php
if(file_exists($GLOBALS['GO_THEME']->theme_path.'MainLayout.js')) {
	echo '<script src="'.$GLOBALS['GO_THEME']->theme_url.'MainLayout.js" type="text/javascript"></script>';
	echo "\n";
}
?>
<script type="text/javascript">
<?php
//these parameter are passed by dialog.php. These are used to directly link to
//a dialog.
if(isset($_REQUEST['m']) || isset($_REQUEST['module']))
{
	//old names where long
	if(!isset($_REQUEST['m']) && isset($_REQUEST['module']))
		$_REQUEST['m']=$_REQUEST['module'];

	if(!isset($_REQUEST['e']) && isset($_REQUEST['loadevent']))
		$_REQUEST['e']=$_REQUEST['loadevent'];

	if(!isset($_REQUEST['f']) && isset($_REQUEST['function']))
		$_REQUEST['f']=$_REQUEST['function'];

	if(!isset($_REQUEST['p']) && isset($_REQUEST['params']))
		$_REQUEST['p']=$_REQUEST['params'];

	$module = isset($_REQUEST['m']) ? $_REQUEST['m'] : false;
	$function = isset($_REQUEST['f']) ? $_REQUEST['f'] : false;
	$params = isset($_REQUEST['p']) ? ($_REQUEST['p']) : false;
	$loadevent = isset($_REQUEST['e']) ? $_REQUEST['e'] : 'render';

	if($module && $function && $params)
	{
	?>
	if(GO.<?php echo $module; ?>)
	{

		 <?php if(!empty($loadevent)) echo 'GO.mainLayout.on("'.$loadevent.'",function(){'; ?>

					GO.<?php echo $module; ?>.<?php echo $function; ?>.apply(this, <?php echo base64_decode($params); ?>);

		 <?php if(!empty($loadevent)) echo '});'; ?>
	}
	<?php
	}
}
?>
</script>