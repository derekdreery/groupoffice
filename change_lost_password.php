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

header('Content-Type: text/html; charset=UTF-8');
require('Group-Office.php');

require_once($GLOBALS['GO_CONFIG']->class_path.'base/users.class.inc.php');
$GO_USERS = new GO_USERS();

$user = $GO_USERS->get_user_by_username($_REQUEST['username']);

if(!$user || $_REQUEST['code1']!=md5($user['password']) || $_REQUEST['code2']!=md5($user['lastlogin'].$user['registration_time']))
{
	die('Invalid request');
}


require_once($GLOBALS['GO_CONFIG']->class_path.'smarty/Smarty.class.php');

require($GLOBALS['GO_LANGUAGE']->get_base_language_file('lostpassword'));

require_once($GLOBALS['GO_CONFIG']->class_path.'base/theme.class.inc.php');
$GO_THEME = new GO_THEME();

$theme = is_dir($GLOBALS['GO_THEME']->theme_path.'smarty') ? $GLOBALS['GO_CONFIG']->theme : 'Default';

$smarty = new Smarty();
$smarty->template_dir=$GLOBALS['GO_CONFIG']->root_path.'themes/'.$theme.'/smarty';
$smarty->compile_dir=$GLOBALS['GO_CONFIG']->tmpdir.'templates_c';
if(!is_dir($smarty->compile_dir))
	mkdir($smarty->compile_dir,0755, true);

$smarty->assign('title', $GLOBALS['GO_CONFIG']->title);

$smarty->assign('subtitle', $lang['lostpassword']['lost_password']);

$smarty->assign('go_url', $GLOBALS['GO_CONFIG']->host);
$smarty->assign('theme_url', $GLOBALS['GO_CONFIG']->host.'themes/'.$theme.'/');
$smarty->assign('lang', $lang);



if($_SERVER['REQUEST_METHOD']=='POST')
{
	if(empty($_POST['pass1']))
	{
		$smarty->assign('feedback', $lang['common']['missingField']);
	}elseif($_POST['pass1']!=$_POST['pass2']){
		$smarty->assign('feedback', $lang['common']['passwordMatchError']);
	}else
	{
		$GO_USERS->update_profile(array('id'=>$user['id'], 'password'=>$_POST['pass1']));
		$smarty->assign('password_changed', true);
	}
}


$smarty->display('lost_password.tpl');
