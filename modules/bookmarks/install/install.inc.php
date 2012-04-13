<?php
$module = $this->get_module('bookmarks');
global $GO_LANGUAGE, $lang, $GO_CONFIG;
require($GLOBALS['GO_LANGUAGE']->get_language_file('bookmarks'));

require_once($module['class_path'].'bookmarks.class.inc.php');
$bookmarks = new bookmarks();

$category['user_id']=1;
$category['name']=$lang['bookmarks']['general'];
$category['acl_id']=$GLOBALS['GO_SECURITY']->get_new_acl('bookmarks');

$GLOBALS['GO_SECURITY']->add_group_to_acl($GLOBALS['GO_CONFIG']->group_everyone, $category['acl_id'], GO_SECURITY::READ_PERMISSION);

$category_id= $bookmarks->add_category($category);


if($GLOBALS['GO_CONFIG']->product_name=='Group-Office'){

	$bookmark['user_id']=1;
	$bookmark['category_id']=$category_id;
	$bookmark['name']='Intermesh Web Solutions';
	$bookmark['content']=$GLOBALS['GO_LANGUAGE']->language=='nl' ? 'http://www.intermesh.nl' : 'http://www.intermesh.nl/en/';
	$bookmark['description']=$GLOBALS['GO_LANGUAGE']->language=='nl' ?'Intermesh ontwikkelt webapplicaties op maat en maakt elegante en effectieve websites' : 'Intermesh develops tailor-made web applications and designs stylish and effective websites.';
	$bookmark['open_extern']='0';
	$bookmark['logo']='icons/intermesh.png';
	$bookmark['public_icon']='1';

	$bookmark_id= $bookmarks->add_bookmark($bookmark, $category);

	$bookmark['user_id']=1;
	$bookmark['category_id']=$category_id;
	$bookmark['name']='Group-Office groupware';
	$bookmark['content']='http://www.group-office.com';
	$bookmark['description']="Take your office online\nShare projects, calendars, files and e-mail online with co-workers and clients. Easy to use and fully customizable, Group-Office takes online collaboration to the next level.";
	$bookmark['open_extern']='0';
	$bookmark['logo']='icons/groupoffice.png';
	$bookmark['public_icon']='1';

	$bookmark_id= $bookmarks->add_bookmark($bookmark, $category);

}

	$bookmark['user_id']=1;
	$bookmark['category_id']=$category_id;
	$bookmark['name']='Google';
	$bookmark['content']='http://www.google.com';
	$bookmark['description']=$lang['bookmarks']['googleDescription'];
	$bookmark['open_extern']='1';
	$bookmark['logo']='icons/viewmag.png';
	$bookmark['public_icon']='1';

	$bookmark_id= $bookmarks->add_bookmark($bookmark, $category);