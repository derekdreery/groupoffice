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


$settings['state_index'] = 'go';

$settings['language']=GO::language()->getLanguage();

$user_id = GO::user() ? GO::user()->id : 0;

$load_modules = GO::modules()->getAllModules(true);

//done in AuthController already
//if(isset($_REQUEST["SET_LANGUAGE"]))
//	$settings['language']=$_REQUEST["SET_LANGUAGE"];

$settings['state']=array();
if(GO::user()) {
	//state for Ext components
	$settings['html_editor_font']=GO::config()->html_editor_font;
	$settings['state'] = GO_Base_Model_State::model()->getFullClientState($user_id);
	$settings['user_id']=$user_id;	
	$settings['has_admin_permission']=GO::user()->isAdmin();	
	$settings['username'] = GO::user()->username;
	$settings['name'] = GO::user()->name;
	
	$settings['email'] = GO::user()->email;
	$settings['thousands_separator'] = GO::user()->thousands_separator;
	$settings['decimal_separator'] = GO::user()->decimal_separator;
	$settings['date_format'] = GO::user()->completeDateFormat;
	$settings['date_separator'] = GO::user()->date_separator;
	$settings['time_format'] = GO::user()->time_format;
	$settings['currency'] = GO::user()->currency;
	$settings['lastlogin'] = GO::user()->lastlogin;
	$settings['max_rows_list'] = GO::user()->max_rows_list;
	$settings['timezone'] = GO::user()->timezone;
	$settings['start_module'] = GO::user()->start_module;
	$settings['theme'] = GO::user()->theme;
	$settings['mute_sound'] = GO::user()->mute_sound;
	$settings['mute_reminder_sound'] = GO::user()->mute_reminder_sound;
	$settings['mute_new_mail_sound'] = GO::user()->mute_new_mail_sound;
	$settings['popup_reminders'] = GO::user()->popup_reminders;
	$settings['show_smilies'] = GO::user()->show_smilies;
	$settings['first_weekday'] = GO::user()->first_weekday;
	$settings['sort_name'] = GO::user()->sort_name;
	$settings['list_separator'] = GO::user()->list_separator;
	$settings['text_separator'] = GO::user()->text_separator;
	
}
//
//require_once(GO::config()->root_path.'classes/base/theme.class.inc.php');
//$GLOBALS['GO_THEME'] = new GO_THEME();

//$settings['modules']=$GLOBALS['GO_MODULES']->modules;
//$settings['config']['theme_url']=GO::user()->theme;
$settings['config']['theme']=GO::config()->theme;
$settings['config']['product_name']=GO::config()->product_name;
$settings['config']['product_version']=GO::config()->version;
$settings['config']['host']=GO::config()->host;
$settings['config']['title']=GO::config()->title;
$settings['config']['webmaster_email']=GO::config()->webmaster_email;

$settings['config']['allow_password_change']=GO::config()->allow_password_change;
$settings['config']['allow_themes']=GO::config()->allow_themes;
$settings['config']['allow_profile_edit']=GO::config()->allow_profile_edit;

$settings['config']['max_users']=GO::config()->max_users;

$settings['config']['debug']=GO::config()->debug;
$settings['config']['max_attachment_size']=GO::config()->max_attachment_size;
$settings['config']['max_file_size']=GO::config()->max_file_size;
$settings['config']['help_link']=GO::config()->help_link;
$settings['config']['nav_page_size']=intval(GO::config()->nav_page_size);

$settings['config']['default_country'] = GO::config()->default_country;




$root_uri = GO::config()->debug ? GO::config()->host : GO::config()->root_path;
$view_root_uri = $root_uri.'views/Extjs3/';
$view_root_path = GO::config()->root_path.'views/Extjs3/';

$scripts=array();
//important to load focus first
$scripts[]=$view_root_uri.'javascript/focus.js';


//if(GO::config()->debug) {
	$scripts[]=$view_root_uri.'ext/adapter/ext/ext-base-debug.js';
	$scripts[]=$view_root_uri.'ext/ext-all-debug.js';
//}else {
//	$scripts[]=$root_uri.'ext/adapter/ext/ext-base.js';
//	$scripts[]=$root_uri.'ext/ext-all.js';
//}

$scripts[]=$view_root_uri.'javascript/namespaces.js';
?>
<script type="text/javascript">
	var BaseHref = '<?php echo GO::config()->host; ?>';

	GO = {};
	GO.settings=<?php echo json_encode($settings); ?>;
	GO.calltoTemplate = '<?php echo GO::config()->callto_template; ?>';
	
	
	
	GO.permissionLevels={
		read:10,
		create:20,
		write:30,
		writeAndDelete:40,
		manage:50		
	};

<?php

//some functions require extra security

if(isset(GO::session()->values['security_token'])){	
	echo 'GO.securityToken="'.GO::session()->values['security_token'].'";';
}

//if(isset($_REQUEST['after_login_url'])) {
//	$after_login_url = $_REQUEST['after_login_url'];
//	
//}else {
//	$after_login_url = $_SERVER['PHP_SELF'];
//
//	$params = array();
//	foreach($_GET as $key=>$value) {
//		if($key!='task' || $value!='logout') {
//			$params[] =$key.'='.urlencode($value);
//		}
//	}
//
//	if(count($params)) {
//		$after_login_url .= '?'.implode('&', $params);
//	}
//}

//$_SESSION['GO_SESSION']['after_login_url']=$after_login_url;

if(isset($_REQUEST['SET_LANGUAGE']) && preg_match('/[a-z_]/', $_REQUEST['SET_LANGUAGE']))
	echo 'GO.loginSelectedLanguage="'.$_REQUEST['SET_LANGUAGE'].'";';

//echo 'GO.afterLoginUrl="'.$after_login_url.'";';

//$fullscreen = isset($_COOKIE['GO_FULLSCREEN']) && $_COOKIE['GO_FULLSCREEN']=='1' ? 'true' : 'false';
//echo 'GO.fullscreen='.$fullscreen.';';

/*
 * If fullscreen mode is enabled and the user is already logged in we set $popup_groupoffice with the URL to load Group-Office
 * in.
 *
 * In themes/Default/layout.inc.php we handle this var.
 */
//if(GO::user() && $fullscreen=='true' && !isset($_REQUEST['fullscreen_loaded'])) {
//	//$popup_groupoffice = isset($_REQUEST['after_login_url']) ? smart_stripslashes($_REQUEST['after_login_url']) : GO::config()->host;
//	$popup_groupoffice = String::add_params_to_url($popup_groupoffice, 'fullscreen_loaded=true');
//}

if(GO::user()) {
	echo 'window.name="'.GO::getId().'";';
	
	//echo 'window.name="groupoffice";';
}else
{
	echo 'window.name="groupoffice-login";';
}

?>
</script>
<?php
$extjsLang = GO::t('extjs_lang');
if($extjsLang=='extjs_lang')
	$extjsLang = GO::language()->getLanguage();

$file = 'base-'.md5($settings['language'].GO::config()->mtime).'.js';
$path = GO::config()->file_storage_path.'cache/'.$file;


if(GO::config()->debug || !file_exists($path)) {
	
	//cleanup old cache
//	require_once(GO::config()->class_path.'filesystem.class.inc');
//	$fs = new filesystem();
	/*$files = $fs->get_files_sorted(GO::config()->file_storage_path.'cache');
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
//		if(file_exists(GO::config()->root_path.'language/common/'.$GLOBALS['GO_LANGUAGE']->language.'.js')) {
//			$scripts[]=$root_uri.'language/common/'.$GLOBALS['GO_LANGUAGE']->language.'.js';
//		}
//
		//echo $view_root_uri.'ext/src/locale/ext-lang-'.$extjsLang.'.js';
		if(file_exists($view_root_path.'ext/src/locale/ext-lang-'.$extjsLang.'.js')) {
			$scripts[]=$view_root_uri.'ext/src/locale/ext-lang-'.$extjsLang.'.js';
		}
//
//		if(file_exists(GO::config()->root_path.'modules/users/language/'.$GLOBALS['GO_LANGUAGE']->language.'.js')) {
//			$scripts[]=$root_uri.'modules/users/language/'.$GLOBALS['GO_LANGUAGE']->language.'.js';
//		}
//	}

//	$dynamic_debug_scripts=array();

	require(GO::config()->root_path.'language/languages.inc.php');
	$fp=fopen(GO::config()->file_storage_path.'cache/languages.js','w');
	if(!$fp){
		die('Could not write to cache directory');
	}
	fwrite($fp, "GO.Languages=[];\n");
	
	

	fwrite($fp, 'Ext.ns("GO.portlets");');
	
	

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
	
	
	
	
	
	
	fclose($fp);
	if(!GO::config()->debug){
		$scripts[]=GO::config()->file_storage_path.'cache/languages.js';
	}else
	{
		$dynamic_debug_script=GO::config()->file_storage_path.'cache/languages.js';		
		$scripts[]=GO::url("core/compress", array('file'=>'languages.js', 'mtime'=>filemtime($dynamic_debug_script)));	
	}

	include($GLOBALS['GO_LANGUAGE']->get_base_language_file('countries'));
	//array_multisort($countries);
	$fp=fopen(GO::config()->file_storage_path.'cache/countries.js','w');
	if(!$fp){
		die('Could not write to cache directory');
	}

	foreach($countries as $key=>$country) {
		fwrite($fp,'GO.lang.countries["'.$key.'"] = "'.$country.'";');
	}
	fclose($fp);
	if(!GO::config()->debug){
		$scripts[]=GO::config()->file_storage_path.'cache/countries.js';
	}else
	{
		$dynamic_debug_script=GO::config()->file_storage_path.'cache/countries.js';
		$scripts[]=GO::url("core/compress", array('file'=>'countries.js', 'mtime'=>filemtime($dynamic_debug_script)));			
	}

	
		$data = file_get_contents(GO::config()->root_path.'views/Extjs3/javascript/scripts.txt');
		$lines = explode("\n", $data);
		foreach($lines as $line) {
			if(!empty($line)) {
				$scripts[]=$root_uri.$line;
			}
		}
	

	

	if(!GO::config()->debug) {
		foreach($scripts as $script) {
			file_put_contents($path,"\n\n".file_get_contents($script),FILE_APPEND);
		}
	}
}

if(!GO::config()->debug) {
	$scripts=array();
	$scripts[]=GO::url("core/compress", array('file'=>$file, 'mtime'=>filemtime($path)));	
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

foreach($load_modules as $module) {
	if(file_exists($module->moduleManager->path().'logged_off_scripts.inc.php')) {
		require($module->moduleManager->path().'logged_off_scripts.inc.php');
	}
	if(file_exists($module->moduleManager->path().'views/Extjs3/logged_off_scripts.inc.php')) {
		require($module->moduleManager->path().'views/Extjs3/logged_off_scripts.inc.php');
	}
}

$scripts=array();
//$load_modules=array();
//if(!isset($default_scripts_load_modules)){
//	if($GLOBALS['GO_SECURITY']->logged_in() && !isset($popup_groupoffice)){
//		$load_modules=$GLOBALS['GO_MODULES']->modules;
//	}
//}else
//{
//	foreach($default_scripts_load_modules as $module)
//	{
//		$GLOBALS['GO_MODULES']->modules[$module]['read_permission']=true;
//		$load_modules[]=$GLOBALS['GO_MODULES']->modules[$module];
//	}
//}


//var_dump($load_modules);
$modulesCacheStr = array();
foreach($load_modules as $module)
	if($module->permissionLevel) 
		$modulesCacheStr[]=$module->id.($module->permissionLevel>GO_Base_Model_Acl::READ_PERMISSION ? '1' : '0');
	
$modulesCacheStr=md5(implode('-',$modulesCacheStr));

if(count($load_modules)) {
	
	$modLangPath =GO::config()->file_storage_path.'cache/'.$settings['language'].'-'.$modulesCacheStr.'-module-languages.js';
	if(!file_exists($modLangPath) || GO::config()->debug){
		$fp=fopen($modLangPath,'w');
		if(!$fp){
			die('Could not write to cache directory');
		}

		//Temporary dirty hack for namespaces
		$modules = GO::modules()->getAllModules();

		while ($module=array_shift($modules)) {
			fwrite($fp, 'Ext.ns("GO.'.$module->id.'");');
		}

		//Put all lang vars in js
		$language = new GO_Base_Language();
		$l = $language->getAllLanguage();
		unset($l['base']);

		fwrite($fp, 'if(GO.customfields){Ext.ns("GO.customfields.columns");Ext.ns("GO.customfields.types");}');
		foreach($l as $module=>$langVars){
			fwrite($fp,'GO.'.$module.'.lang='.json_encode($langVars).';');
		}
		fclose($fp);
	}
	//$scripts[]=GO::config()->file_storage_path.'cache/module-languages.js';
	
	if(!GO::config()->debug){
		$scripts[]=$modLangPath;
	}else
	{		
		$scripts[]=GO::url("core/compress", array('file'=>basename($modLangPath), 'mtime'=>filemtime($modLangPath)));
	}
	
	//load language first so it can be overridden
//	foreach($load_modules as $module) {
//		if($module['read_permission']) {
//
//			$module_uri = GO::config()->debug ? $module['url'] : $module['path'];
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
		if($module->permissionLevel) {
			if(file_exists($module->moduleManager->path().'prescripts.inc.php')) {
				require($module->moduleManager->path().'prescripts.inc.php');
			}
			if(file_exists($module->moduleManager->path().'views/Extjs3/prescripts.inc.php')) {
				require($module->moduleManager->path().'views/Extjs3/prescripts.inc.php');
			}
		}
	}


	$modules=array();
	foreach($load_modules as $module) {
		if($module->permissionLevel) {

//			$module_uri = GO::config()->debug ? $module->moduleManager->url() : $module->moduleManager->path();
			
			$scriptsFile = $module->moduleManager->path().'scripts.txt';
			if(!file_exists($scriptsFile))
						$scriptsFile = $module->moduleManager->path().'views/Extjs3/scripts.txt';	

			if(file_exists($scriptsFile)) {
				$data = file_get_contents($scriptsFile);
				$lines = explode("\n", $data);
				foreach($lines as $line) {
					if(!empty($line)) {
						$scripts[]=$root_uri.$line;
					}
				}
			}

			$modules[]=$module->id.$module->permissionLevel;
		}
	}

	//two modules may include the same script
	$scripts=array_unique($scripts);

	//include config file location because in some cases different URL's point to
	//the same database and this can break things if the settings are cached.
	$file = $user_id.'-'.md5(GO::config()->mtime.GO::config()->get_config_file().':'.GO::language()->getLanguage().':'.$modulesCacheStr).'.js';
	$path = GO::config()->file_storage_path.'cache/'.$file;
	
	
	if(!GO::config()->debug) {
		if(!file_exists($path)) {
		
			//cleanup old cache
//			require_once(GO::config()->class_path.'filesystem.class.inc');
//			$fs = new filesystem();
			/*$files = $fs->get_files_sorted(GO::config()->file_storage_path.'cache');
			while($file=array_shift($files)) {
				if(substr($file['name'],0, 1)==$user_id) {
					unlink($file['path']);
				}
			}*/

			file_put_contents(GO::config()->file_storage_path.'cache/'.$user_id.'-modules.js', 'GO.settings.modules = Ext.decode("'.addslashes(json_encode($GLOBALS['GO_MODULES']->modules)).'");');
			array_unshift($scripts, GO::config()->file_storage_path.'cache/'.$user_id.'-modules.js');


			foreach($scripts as $script) {
				file_put_contents($path,"\n\n".file_get_contents($script),FILE_APPEND);
			}
		}
		
		$url=GO::url("core/compress", array('file'=>$file, 'mtime'=>filemtime($path)));

		$scripts=array($url);

	}else
	{
		file_put_contents(GO::config()->file_storage_path.'cache/'.$user_id.'-modules.js', 'GO.settings.modules = Ext.decode("'.addslashes(json_encode($GLOBALS['GO_MODULES']->modules)).'");');
		
		$url=GO::url("core/compress", array('file'=>$user_id.'-modules.js', 'mtime'=>filemtime(GO::config()->file_storage_path.'cache/'.$user_id.'-modules.js')));		
		array_unshift($scripts, $url);
		
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

//	foreach($load_modules as $module) {
//		$GLOBALS['GO_LANGUAGE']->require_language_file($module['id']);
//	}

	//The checked values is used in the SearchPanel.js for the filter
//	$types = GO::config()->get_setting('link_type_filter', $user_id);
//	$types = empty($types) ? array() : explode(',', $types);
//
//	$link_types=array();
//	if(isset($lang['link_type'])){
//		asort($lang['link_type']);
//		foreach($lang['link_type'] as $id=>$name) {
//			$type['id']=$id;
//			$type['name']=$name;
//			$type['checked']=in_array($id, $types);
//			$link_types[] = $type;
//		}
//	}
//
//	$GO_SCRIPTS_JS .= 'GO.linkTypes='.json_encode($link_types).';';
//
//	require_once(GO::config()->class_path.'export/export_query.class.inc.php');
//	$eq = new export_query();
//
//	$GO_SCRIPTS_JS.=$eq->find_custom_exports();

	
	foreach($load_modules as $module) {
		if($module->permissionLevel) {
			if(file_exists($module->moduleManager->path().'scripts.inc.php')) {
				require($module->moduleManager->path().'scripts.inc.php');
			}
			if(file_exists($module->moduleManager->path().'views/Extjs3/scripts.inc.php')) {
				require($module->moduleManager->path().'views/Extjs3/scripts.inc.php');
			}
		}
	}

	$GLOBALS['GO_EVENTS']->fire_event('load_scripts', array(&$GO_SCRIPTS_JS));	

	$filename = $user_id.'-scripts.js';
	$path = GO::config()->file_storage_path.'cache/'.$filename;

	if($GO_SCRIPTS_JS!=@file_get_contents($path)){
		file_put_contents($path, $GO_SCRIPTS_JS);
	}
	if(file_exists($path)){

		$url=GO::url("core/compress", array('file'=>$filename, 'mtime'=>filemtime($path)));		
		echo '<script type="text/javascript" src="'.$url.'"></script>'."\n";
	}
}
?>
<script type="text/javascript">


	<?php $GLOBALS['GO_EVENTS']->fire_event('inline_scripts');	?>

	Ext.BLANK_IMAGE_URL = '<?php echo GO::config()->host; ?>views/Extjs3/ext/resources/images/default/s.gif';

	Ext.state.Manager.setProvider(new GO.state.HttpProvider());
	//Ext.state.Manager.setProvider(new Ext.state.CookieProvider());


	//some functions require extra security
	<?php
	
	
	
	
	if(isset(GO::session()->values['security_token']))		
		echo 'Ext.Ajax.extraParams={security_token:"'.GO::session()->values['security_token'].'"};';
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
if(isset($_REQUEST['f']))
{
	$fp = GO_Base_Util_Crypt::decrypt($_REQUEST['f']);
	

	
	?>
	if(GO.<?php echo $fp['m']; ?>)
	{
		 GO.mainLayout.on("render", function(){
				GO.<?php echo $fp['m']; ?>.<?php echo $fp['f']; ?>.apply(this, <?php echo json_encode($fp['p']); ?>);
		 });
	}
	<?php
	
}
?>
</script>