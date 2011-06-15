<?php
$module = $this->get_module('bookmarks');
global $GO_LANGUAGE, $lang, $GO_CONFIG;
require(GO::language()->get_language_file('bookmarks'));

require_once($module['class_path'].'bookmarks.class.inc.php');
$bookmarks = new bookmarks();

$category['user_id']=1;
$category['name']=$lang['bookmarks']['general'];
$category['acl_id']=GO::security()->get_new_acl('bookmarks');

GO::security()->add_group_to_acl(GO::config()->group_everyone, $category['acl_id'], GO_SECURITY::READ_PERMISSION);

$category_id= $bookmarks->add_category($category);


if(GO::config()->product_name=='Group-Office'){

	$bookmark['user_id']=1;
	$bookmark['category_id']=$category_id;
	$bookmark['name']='Intermesh Web Solutions';
	$bookmark['content']=GO::language()->language=='nl' ? 'http://www.intermesh.nl' : 'http://www.intermesh.nl/en/';
	$bookmark['description']=GO::language()->language=='nl' ?'Intermesh ontwikkelt webapplicaties op maat en maakt elegante en effectieve websites' : 'Intermesh develops tailor-made web applications and designs stylish and effective websites.';
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