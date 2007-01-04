<?php
/*
Copyright Intermesh 2003
Author: Merijn Schering <mschering@intermesh.nl>
Version: 1.0 Release date: 08 July 2003

This program is free software; you can redistribute it and/or modify it
under the terms of the GNU General Public License as published by the
Free Software Foundation; either version 2 of the License, or (at your
option) any later version.
*/
require_once($GO_MODULES->modules['cms']['class_path'].'cms.class.inc');

load_basic_controls();
load_control('date_picker');

class cms_site extends cms {
	var $site;
	var $template;
	var $publish_url;
	var $publish_path;
	var $publish_static = false;
	var $template_file_replacements = array ();

	var $folder_id;
	var $file_id;
	var $task;
	var $cms_module;

	var $return_to;
	
	var $previous_file_id=null;
	var $next_file_id=null;

	var $tags = array (
	'php', //important as first tag so it can produce tags that will be processed
	'template',
	'home',
	'folders',
	'rootfolders',
	'files',
	'path',
	'icon',
	'login',
	'back',
	'active_doc',
	'document',
	'folderlist',
	'hot_items',
	'languages',
	'items',
	'menu',
	'treeview',
	'filename',
	'admin',
	'logout',
	'nextfile',
	'previousfile'
	);

	function cms_site() {
		global $GO_CONFIG, $GO_LANGUAGE, $GO_MODULES;
		$this->db();

		$this->folder_id = isset ($_REQUEST['folder_id']) ? $_REQUEST['folder_id'] : 0;
		$this->file_id = isset ($_REQUEST['file_id']) ? $_REQUEST['file_id'] : 0;
		$this->task = isset ($_REQUEST['task']) ? $_REQUEST['task'] : '';


		$this->cms_module = $GO_MODULES->get_module('cms');


		if($this->task=='logout')
		{
			global $GO_SECURITY;
			$GO_SECURITY->logout();
		}



		$site_id = isset ($_REQUEST['site_id']) ? $_REQUEST['site_id'] : 0;

		//If a site_id was passed then get it
		if ($site_id > 0) {
			$this->site = $this->get_site($site_id);
		}else
		{
			/*
			if no site_id was passed then we are not in Group-Office but somebody is
			requesting the site through a domain. Try to get the site by domain.

			Get the domain name without this page eg.
			http://www.test.com/test/index.php?file_id=0&folder_id=9 becomes:
			www.test.com/test
			*/
			$domain = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
			if (strpos($domain, basename($_SERVER['PHP_SELF'])) !== false) {
				//chop off filename
				$pos = strrpos($domain, '/');
				if ($pos !== false) {
					$domain = substr($domain, 0, $pos);
				}
			}
			if (!$this->site = $this->get_site_by_domain($domain, true)) {
				exit();
			}
		}

		$this->template = $this->get_template($this->site['template_id']);

		if(isset($_SESSION['GO_SESSION']['cms']['return_to']))
		{
			$this->return_to = $_SESSION['GO_SESSION']['cms']['return_to'];
		}
		//$_SESSION['GO_SESSION']['cms']['return_to']=$_SERVER['REQUEST_URI'];
		$_SESSION['GO_SESSION']['cms']['return_to']=$this->get_uri();


	}

	function get_uri()
	{
		$uri = $_SERVER['PHP_SELF'].'?file_id='.$this->file_id.'&amp;folder_id='.$this->folder_id;
		return $uri;
	}

	function get_login_uri($goto_url='')
	{
		$uri = add_params_to_url($this->get_uri(), 'task=login&amp;goto_url='.urlencode($goto_url));
		return $uri;
	}


	function find_page($folder_id)
	{
		global $GO_SECURITY;
		
		if($folder_id==0)
		{
			$folder_id=$this->site['root_folder_id'];
		}
		if(!$files = $this->get_authorized_files($folder_id, $GO_SECURITY->user_id))
		{
			$folder = $this->get_folder($folder_id);
			if(!$folder)
			{
				$folder_id=$this->site['root_folder_id'];
			}else {
				if($folder['parent_id']>0)
				{
					return $this->find_page($folder['parent_id']);
				}
			}
		}
		
		foreach($files as $file)
		{
			if (eregi('htm', $file['extension'])) {
				return $file['id'];
			}
		}
		$folders = $this->get_authorized_folders($folder_id, $GO_SECURITY->user_id);
		foreach($folders as $folder)
		{
			return $this->find_page($folder['id']);
		}
		return false;
	}

	function generate_header($doctype='', $title = '', $keywords = '', $description = '', $header_arguments='', $body_arguments='') {
		global $GO_CONFIG, $charset;

		$header = $doctype."\r\n".
		'<html>'."\r\n".
		'<head>'."\r\n".
		'<meta http-equiv="Content-Type" content="text/html; charset='.$charset.'" />'."\r\n".
		'<meta name="robots" content="all,index,follow" />'."\r\n".
		'<meta name="revisit-after" content="7 days" />'."\r\n".
		'<meta name="keywords" content="'.$keywords.'" />'."\r\n".
		'<meta name="description" content="'.$description.'" />'."\r\n".
		'<meta name="Generator" content="Group-Office '.$GO_CONFIG->version.'" />'."\r\n".
		'<title>'.$title.'</title>'."\r\n".
		'<link href="'.$this->cms_module['url'].'stylesheet.php?template_id='.$this->site['template_id'].'" rel="stylesheet" type="text/css" />'."\r\n".
		'<link href="'.$this->cms_module['url'].'stylesheet.php?print=true&amp;template_id='.$this->site['template_id'].'" rel="stylesheet" type="text/css" media="print" />'."\r\n";

		$header .= $header_arguments;

		$header .= '<script type="text/javascript" src="'.$GO_CONFIG->host.'javascript/common.js"></script>'."\r\n".
		'</head>'."\r\n".
		'<body '.$body_arguments.'>'."\r\n".
		"\r\n<!-- End of generated header --> \r\n\r\n";

		return $header;
	}

	function generate_footer() {

		return "\r\n\r\n<!-- Start of generated Footer --> \r\n\r\n".
		'</body>'."\r\n".
		'</html>'."\r\n";
	}

	function access_denied()
	{
		global $GO_CONFIG;
		header('Location: '.$GO_CONFIG->host.'error_docs/403.php');
		exit();
	}


	function authenticate($goto_url=null)
	{
		global $GO_SECURITY;

		if(!$GO_SECURITY->logged_in())
		{
			if(!isset($goto_url))
			{
				$goto_url=$_SERVER['REQUEST_URI'];
			}
			if($this->template['login_template_item_id']>0)
			{
				header('Location: '.$_SERVER['PHP_SELF'].'?task=login&goto_url='.urlencode($goto_url).'&return_to='.urlencode($this->return_to));
			}else {
				$GO_SECURITY->authenticate(false,false);
			}
			exit();
		}
	}

	function generate_page() {
		global
		$GO_CONFIG,
		$GO_LANGUAGE,
		$GO_SECURITY,
		$GO_MODULES,
		$cms_empty;

		switch($this->task)
		{
			case 'logout':

				$template_item=$this->get_template_item($this->template['login_template_item_id']);

				$html = $this->generate_header($this->template['doctype']);

				$template_content = $this->parse_template($template_item['content']);

				$p = new html_element('p', $GLOBALS['cms_logged_out']);
				$p->set_attribute('style','margin-top:10px;margin-bottom:10px;');

				if(!empty($_REQUEST['goto_url']))
				{
					$input = new input('button','continue',$GLOBALS['cmdContinue']);
					$input->set_attribute('onclick',"javascript:document.location='".smart_stripslashes($_REQUEST['goto_url'])."';");


					$content = $p->get_html().$input->get_html();
				}else {

					$content = $p->get_html();
				}

				$html .= str_replace($this->get_tag('content', $template_content), $content, $template_content);

				$html .= $this->generate_footer();
				return $html;
				break;

			case 'login':

				require($GO_LANGUAGE->get_base_language_file('login'));
				if(isset($GO_MODULES->modules['webshop']))
				{
					require($GO_LANGUAGE->get_language_file('webshop'));
				}

				$login_task = isset($_REQUEST['login_task']) ? $_REQUEST['login_task'] : '';
				$goto_url = !empty($_REQUEST['goto_url']) ? smart_stripslashes($_REQUEST['goto_url']) : $this->return_to;
				$return_to = isset($_REQUEST['return_to']) ? $_REQUEST['return_to'] : $_SERVER['HTTP_REFERER'];
				if($login_task == 'login')
				{
					$username = smart_addslashes($_POST['username']);
					$password = smart_addslashes($_POST['password']);



					if (!$GLOBALS['GO_AUTH']->login($username, $password, $_SESSION['auth_source']))
					{
						$feedback = $login_bad_login;
					}else {

						header('Location: '.$goto_url);
						exit();
					}
				}



				$form = new form('login_form');
				$form->add_html_element(new input('hidden','login_task', 'login', false));
				$form->add_html_element(new input('hidden','task', 'login'));
				$form->add_html_element(new input('hidden','goto_url', $goto_url));
				$form->add_html_element(new input('hidden','return_to', $return_to));
				$form->add_html_element(new input('hidden','file_id', $this->file_id));
				$form->add_html_element(new input('hidden','folder_id', $this->folder_id));




				if($GO_CONFIG->allow_registration)
				{
					$h2=new html_element('h2', $login_no_account);
					$form->add_html_element($h2);
					
					$p = new html_element('p', $login_no_account_text);
					$form->add_html_element($p);


					$href = 'javascript:document.login_form.task.value=\'register\';document.login_form.submit();';
					$hyperlink = new hyperlink($href, sprintf($login_register, $GO_CONFIG->title));
					$form->add_html_element($hyperlink);

					$h2 = new html_element('h2',sprintf($login_to, $GO_CONFIG->title));
					$h2->set_attribute('style','margin-top:20px;');
					$form->add_html_element($h2);

				}else {
					$h2 = new html_element('h2',sprintf($login_to, $GO_CONFIG->title));
					$form->add_html_element($h2);
				}

				$p = new html_element('p',$login_text);

				/*if($GO_CONFIG->allow_registration)
				{
				$href = 'javascript:document.login_form.task.value=\'register\';document.login_form.submit();';
				$hyperlink = new hyperlink($href, $login_here);

				$p->innerHTML .= ' '.sprintf($login_register, $hyperlink->get_html());
				}*/

				$table = new table();

				$row = new table_row();


				$cell = new table_cell($p->get_html());
				$cell->set_attribute('colspan','2');

				if (isset($feedback))
				{
					$p = new html_element('p', $feedback);
					$p->set_attribute('class', 'error');
					$cell->add_html_element($p);
				}

				$row->add_cell($cell);
				$table->add_row($row);


				$row = new table_row();
				$cell = new table_cell($login_username.':');
				$cell->set_attribute('style', 'whitespace:nowrap;');
				$row->add_cell($cell);

				$cell = new table_cell();
				$username = isset($_REQUEST['username']) ? $_REQUEST['username'] : '';
				$input = new input('text', 'username', $username);
				$input->set_attribute('style','width:200px');
				$cell->innerHTML .= $input->get_html();

				if (count($GLOBALS['auth_sources']) > 1)
				{
					$cookie_key = isset($_COOKIE['GO_AUTH_SOURCE_KEY']) ? $_COOKIE['GO_AUTH_SOURCE_KEY'] : '0';
					$auth_source_key = isset($_REQUEST['auth_source_key']) ? $_REQUEST['auth_source_key'] : $cookie_key;

					$select = new select('auth_source_key', $auth_source_key);
					for($i=0;$i<count($GLOBALS['auth_sources']); $i++)
					{
						$select->add_value($i, $GLOBALS['auth_sources'][$i]['name']);
					}

					$cell->innerHTML .= '@'.$select->get_html();

				}else
				{
					$input = new input('hidden', 'auth_source_key','0');
					$cell->innerHTML .= $input->get_html();
				}

				$row->add_cell($cell);
				$table->add_row($row);

				$row = new table_row();
				$cell = new table_cell($login_password.':');
				$cell->set_attribute('style', 'whitespace:nowrap;');
				$row->add_cell($cell);

				$cell = new table_cell();
				$input = new input('password', 'password');
				$input->set_attribute('style','width:200px');

				$cell->innerHTML .= $input->get_html();

				$row->add_cell($cell);
				$table->add_row($row);

				$row = new table_row();

				$cell = new table_cell();
				$cell->set_attribute('colspan','2');

				$input = new input('submit','cmdOk',$GLOBALS['cmdLogin']);
				//$input->set_attribute('onclick',"javascript:document.login_form.login_task.value='login';document.login_form.submit();");
				$cell->add_html_element($input);

				$input = new input('button','cmdCancel',$GLOBALS['cmdCancel']);
				$input->set_attribute('onclick',"javascript:document.location='".$return_to."';");
				$cell->add_html_element($input);

				$row->add_cell($cell);
				$table->add_row($row);

				$row = new table_row();
				$cell = new table_cell();
				$cell->set_attribute('colspan','2');
				$cell->set_attribute('style','padding-top:20px;');
				$hyperlink = new hyperlink('javascript:document.login_form.task.value=\'lost_password\';document.login_form.submit();', $login_lost_password);

				$cell->add_html_element($hyperlink);
				$row->add_cell($cell);
				$table->add_row($row);

				$form->add_html_element($table);


				$template_item=$this->get_template_item($this->template['login_template_item_id']);

				$html = $this->generate_header($this->template['doctype'],'','','','','onload="document.login_form.username.focus();"');
				$template_content = $this->parse_template($template_item['content']);
				$html .= str_replace($this->get_tag('content', $template_content), $form->get_html(), $template_content);
				$html .= $this->generate_footer();
				return $html;
				break;

			case 'lost_password':

				require($GO_LANGUAGE->get_base_language_file('login'));

				$login_task = isset($_REQUEST['login_task']) ? $_REQUEST['login_task'] : '';
				$goto_url = isset($_REQUEST['goto_url']) ? smart_stripslashes($_REQUEST['goto_url']) : $_SERVER['PHP_SELF'];
				$return_to = isset($_REQUEST['return_to']) ? $_REQUEST['return_to'] : $_SERVER['HTTP_REFERER'];

				$form = new form('login_form');
				$form->add_html_element(new input('hidden','login_task', 'send_password', false));
				$form->add_html_element(new input('hidden','task', 'lost_password'));
				$form->add_html_element(new input('hidden','goto_url', $goto_url));
				$form->add_html_element(new input('hidden','return_to', $return_to));
				$form->add_html_element(new input('hidden','file_id', $this->file_id));
				$form->add_html_element(new input('hidden','folder_id', $this->folder_id));
				
				if($login_task == 'send_password')
				{
					global $GO_USERS;

					if($_POST['email'] == '')
					{
						$feedback = $GLOBALS['error_missing_field'];
					}elseif($user = $GO_USERS->get_user_by_email(smart_addslashes($_POST['email'])))
					{


						$new_password = $GO_USERS->random_password();
						$GO_USERS->update_password($user['id'],$new_password);

						$mail_body = sprintf($login_lost_password_mail_body, $GO_CONFIG->title, $GO_CONFIG->full_url, $user['username'],$new_password);

						sendmail(smart_stripslashes($_POST['email']),
						$GO_CONFIG->webmaster_email,
						$GO_CONFIG->title,
						$login_new_password,
						$mail_body);

						$form->add_html_element(new html_element('p',sprintf($login_lost_password_success, smart_stripslashes($_POST['email']))));

						$input = new input('button','cmdContinue',$GLOBALS['cmdContinue']);
						$input->set_attribute('onclick',"javascript:document.login_form.task.value='login';document.login_form.submit();");
						$form->add_html_element($input);

						$template_item=$this->get_template_item($this->template['login_template_item_id']);

						$html = $this->generate_header($this->template['doctype'],'','','','','onload="document.login_form.email.focus();"');
						$template_content = $this->parse_template($template_item['content']);
						$html .= str_replace($this->get_tag('content', $template_content), $form->get_html(), $template_content);
						$html .= $this->generate_footer();
						return $html;
						exit();
					}else
					{
						$feedback = sprintf($login_lost_password_failed, smart_stripslashes($_POST['email']));
					}
				}


				$form->add_html_element(new html_element('h1',$login_lost_password_title));
				$form->add_html_element(new html_element('p', $login_lost_password_text));

				if (isset($feedback))
				{
					$p = new html_element('p', $feedback);
					$p->set_attribute('class', 'error');
					$form->add_html_element($p);
				}

				$input = new input('text', 'email');
				$input->set_attribute('style','width:300px;');
				$form->add_html_element(new html_element('p',$GLOBALS['strEmail'].': '.$input->get_html()));



				$input = new input('submit','cmdOk',$GLOBALS['cmdOk']);
				//$input->set_attribute('onclick',"javascript:document.login_form.login_task.value='send_password';document.login_form.submit();");
				$form->add_html_element($input);

				$input = new input('button','cmdCancel',$GLOBALS['cmdCancel']);
				$input->set_attribute('onclick',"javascript:document.login_form.task.value='login';document.login_form.submit();");
				$form->add_html_element($input);

				$template_item=$this->get_template_item($this->template['login_template_item_id']);

				$html = $this->generate_header($this->template['doctype'],'','','','','onload="document.login_form.email.focus();"');
				$template_content = $this->parse_template($template_item['content']);
				$html .= str_replace($this->get_tag('content', $template_content), $form->get_html(), $template_content);
				$html .= $this->generate_footer();
				return $html;

				break;


			case 'register':

				global $GO_USERS, $GO_GROUPS, $GO_MODULES;

				function check_fields($required_fields)
				{

					foreach($required_fields as $field)
					{
						if(!empty($field) && empty($_POST[$field]))
						{
							return false;
						}
					}

					return true;
				}

				require($GO_LANGUAGE->get_language_file('users'));

				$login_task = isset($_REQUEST['login_task']) ? $_REQUEST['login_task'] : '';
				$goto_url = isset($_REQUEST['goto_url']) ? smart_stripslashes($_REQUEST['goto_url']) : $_SERVER['PHP_SELF'];
				$return_to = isset($_REQUEST['return_to']) ? $_REQUEST['return_to'] : $_SERVER['HTTP_REFERER'];

				$fields = explode(',', $GO_CONFIG->registration_fields);

				$form = new form('login_form');
				$form->add_html_element(new input('hidden','login_task', 'register', false));
				$form->add_html_element(new input('hidden','task', 'register'));
				$form->add_html_element(new input('hidden','goto_url', $goto_url));
				$form->add_html_element(new input('hidden','return_to', $return_to));
				$form->add_html_element(new input('hidden','file_id', $this->file_id));
				$form->add_html_element(new input('hidden','folder_id', $this->folder_id));
				
				$modules_read = array_map('trim', explode(',',$GO_CONFIG->register_modules_read));
				$modules_write = array_map('trim', explode(',',$GO_CONFIG->register_modules_write));

				//user groups the user will be added to.
				$user_groups = $GO_GROUPS->groupnames_to_ids(array_map('trim',explode(',',$GO_CONFIG->register_user_groups)));

				//user groups that this user will be visible to
				$visible_user_groups = $GO_GROUPS->groupnames_to_ids(array_map('trim',explode(',',$GO_CONFIG->register_visible_user_groups)));



				if(!$GO_CONFIG->allow_registration)
				{
					header('Location: '.$GO_CONFIG->host.'error_docs/403.php');
					exit();
				}


				$user['first_name'] = isset($_POST['first_name']) ?  smart_stripslashes(trim($_POST['first_name'])) : '';
				$user['middle_name'] = isset($_POST['middle_name']) ?  smart_stripslashes(trim($_POST['middle_name'])) : '';
				$user['last_name'] = isset($_POST['last_name']) ?  smart_stripslashes(trim($_POST['last_name'])) : '';

				if(in_array('title_initials', $fields))
				{
					$user['initials'] = isset($_POST['initials']) ? smart_stripslashes($_POST["initials"]) : '';
					$user['title'] = isset($_POST['title']) ? smart_stripslashes($_POST["title"]) : '';
				}
				if(in_array('birthday', $fields))
				{
					$user['birthday'] = isset($_POST['birthday']) ? smart_stripslashes($_POST["birthday"]) : '';
				}
				$user['email'] = isset($_POST['email']) ? smart_stripslashes($_POST["email"]) : '';
				if(in_array('home_phone', $fields))
				{
					$user['home_phone'] = isset($_POST['home_phone']) ? smart_stripslashes($_POST["home_phone"]) : '';
				}
				if(in_array('work_phone', $fields))
				{
					$user['work_phone'] = isset($_POST['work_phone']) ? smart_stripslashes($_POST["work_phone"]) : '';
				}
				if(in_array('fax', $fields))
				{
					$user['fax'] = isset($_POST['fax']) ? smart_stripslashes($_POST["fax"]) : '';
				}
				if(in_array('work_fax', $fields))
				{
					$user['work_fax'] = isset($_POST['work_fax']) ? smart_stripslashes($_POST["work_fax"]) : '';
				}
				if(in_array('cellular', $fields))
				{
					$user['cellular'] = isset($_POST['cellular']) ? smart_stripslashes($_POST["cellular"]) : '';
				}
				if(in_array('address', $fields))
				{
					$user['country_id'] = isset($_POST['country_id']) ? smart_addslashes($_POST["country_id"]) : $GO_CONFIG->default_country_id;
					$user['state'] = isset($_POST['state']) ? smart_stripslashes($_POST["state"]) : '';
					$user['city'] = isset($_POST['city']) ? smart_stripslashes($_POST["city"]) : '';
					$user['zip'] = isset($_POST['zip']) ? smart_stripslashes($_POST["zip"]) : '';
					$user['address'] = isset($_POST['address']) ? smart_stripslashes($_POST["address"]) : '';
					$user['address_no'] = isset($_POST['address_no']) ? smart_stripslashes($_POST["address_no"]) : '';
				}

				if(in_array('work_address', $fields))
				{
					$user['work_country_id'] = isset($_POST['work_country_id']) ? smart_addslashes($_POST["work_country_id"]) : $GO_CONFIG->default_country_id;
					$user['work_state'] = isset($_POST['work_state']) ? smart_stripslashes($_POST["work_state"]) : '';
					$user['work_city'] = isset($_POST['work_city']) ? smart_stripslashes($_POST["work_city"]) : '';
					$user['work_zip'] = isset($_POST['work_zip']) ? smart_stripslashes($_POST["work_zip"]) : '';
					$user['work_address'] = isset($_POST['work_address']) ? smart_stripslashes($_POST["work_address"]) : '';
					$user['work_address_no'] = isset($_POST['work_address_no']) ? smart_stripslashes($_POST["work_address_no"]) : '';
				}

				if(in_array('company', $fields))
				{
					$user['company'] = isset($_POST['company']) ? smart_stripslashes($_POST["company"]) : '';
				}
				if(in_array('department', $fields))
				{
					$user['department'] =  isset($_POST['department']) ? smart_stripslashes($_POST["department"]) : '';
				}
				if(in_array('function', $fields))
				{
					$user['function'] =  isset($_POST['function']) ? smart_stripslashes($_POST["function"]) : '';
				}
				if(in_array('sex', $fields))
				{
					$user['sex'] = isset($_POST['sex']) ? smart_stripslashes($_POST["sex"]) : 'M';
				}

				if(in_array('homepage', $fields))
				{
					$user['homepage'] = isset($_POST['homepage']) ? smart_stripslashes($_POST["homepage"]) : '';
				}

				$user['language'] = isset($_POST['SET_LANGUAGE']) ? $_POST['SET_LANGUAGE'] : $GO_LANGUAGE->language['code'];

				$user['theme'] = $GO_CONFIG->theme;
				$user['username'] = isset($_POST['username']) ? smart_stripslashes($_POST['username']) : '';
				$user['enabled'] = $GO_CONFIG->auto_activate_accounts ? '1' : '0';


				$login_task = isset($_REQUEST['login_task']) ? $_REQUEST['login_task'] : '';
				$goto_url = isset($_REQUEST['goto_url']) ? smart_stripslashes($_REQUEST['goto_url']) : $_SERVER['PHP_SELF'];

				$birthday = isset($_REQUEST['birthday']) ? $_REQUEST['birthday'] : '';
				$birthday_picker = new date_picker('birthday', $_SESSION['GO_SESSION']['date_format'], $birthday);



				$required_registration_fields = str_replace('address', 'address,address_no,zip,city,state,country_id', $GO_CONFIG->required_registration_fields);
				$required_registration_fields = str_replace('work_address', 'work_address,work_address_no,work_zip,work_city,work_state,work_country_id', $required_registration_fields);
				$required_registration_fields = str_replace('title_initials', 'title,initioals', $required_registration_fields);
				$required_fields = explode(',',$required_registration_fields);
				$required_fields[]='username';
				$required_fields[]='email';
				$required_fields[]='first_name';
				$required_fields[]='last_name';
				
				if ($login_task == "register")
				{

					if($GO_CONFIG->auto_activate_accounts)
					{
						$pass1 = smart_stripslashes($_POST["pass1"]);
						$pass2 = smart_stripslashes($_POST["pass2"]);
						$user['password'] = smart_stripslashes($_POST["pass1"]);
					}else {
						$user['password']='';
					}

					$user = array_map('addslashes',$user);

					if (!check_fields($required_fields) || ($GO_CONFIG->auto_activate_accounts && (empty($pass1) || empty ($pass2))))
					{
						$feedback = $GLOBALS['error_missing_field'];
					}elseif(!preg_match('/^[a-z0-9_-]*$/', $user['username']))
					{
						$feedback = $GLOBALS['error_username'];
					}elseif(!validate_email($user['email']))
					{
						$feedback = $GLOBALS['error_email'];
					}elseif($GO_USERS->get_user_by_username($user['username']))
					{
						$feedback = $GLOBALS['error_username_exists'];
					}elseif($GO_USERS->email_exists($user['email']))
					{
						$feedback = $GLOBALS['error_email_exists'];
					}elseif($GO_CONFIG->auto_activate_accounts && $pass1 != $pass2)
					{
						$feedback = $GLOBALS['error_match_pass'];
					}else
					{
						if(isset($_POST['birthday']))
						{
							$user['birthday'] = date_to_db_date($_POST['birthday']);
						}

						if ($new_user_id = $GO_USERS->add_user($user, $user_groups, $visible_user_groups, $modules_read, $modules_write	))
						{

							if($user['enabled']=='1')
							{
								$registration_mail_body = $GO_CONFIG->get_setting('registration_confirmation');
								$registration_mail_subject = $GO_CONFIG->get_setting('registration_confirmation_subject');
							}else {
								$registration_mail_body = $GO_CONFIG->get_setting('registration_unconfirmed');
								$registration_mail_subject = $GO_CONFIG->get_setting('registration_unconfirmed_subject');
							}

							if(!empty($registration_mail_body) && !empty($registration_mail_subject))
							{
								//send email to the user with password
								if(isset($_POST['sex']))
								{
									$registration_mail_body = str_replace("%beginning%", $GLOBALS['sir_madam'][$_POST['sex']], $registration_mail_body);
								}else {
									$registration_mail_body = str_replace("%beginning%", $GLOBALS['sir_madam']['M'].'/'.$GLOBALS['sir_madam']['F'], $registration_mail_body);
								}
								// If $title is not set, then use $sex (sir_madam) instead for $title.
								if(isset($_POST['title']))
								{
									if($user['title']=='')
									{
										if(isset($_POST['sex']))
										{
											$title = $GLOBALS['sir_madam'][$_POST['sex']];
										}else {
											$title = $GLOBALS['sir_madam']['M'].'/'.$GLOBALS['sir_madam']['F'];
										}
									}else {
										$title = $user['title'];
									}
									$registration_mail_body = str_replace("%title%", $title, $registration_mail_body);
								}
								$registration_mail_body = str_replace("%last_name%", smart_stripslashes($_POST['last_name']), $registration_mail_body);
								$registration_mail_body = str_replace("%middle_name%", smart_stripslashes($_POST['middle_name']), $registration_mail_body);
								$registration_mail_body = str_replace("%first_name%", smart_stripslashes($_POST['first_name']), $registration_mail_body);
								$registration_mail_body = str_replace("%username%",smart_stripslashes($_POST['username']), $registration_mail_body);
								$registration_mail_body = str_replace("%password%",smart_stripslashes($user['password']), $registration_mail_body);
								$registration_mail_body = str_replace("%full_url%",'<a href="'.$GO_CONFIG->full_url.'">'.$GO_CONFIG->full_url.'</a>', $registration_mail_body);

								sendmail($user['email'], $GO_CONFIG->webmaster_email, $GO_CONFIG->title, $registration_mail_subject, $registration_mail_body,'3','text/HTML');
							}

							if($GO_CONFIG->notify_admin_of_registration)
							{
								$body = $admin_new_user_body."\r\n\r\n".$GO_CONFIG->full_url.'?return_to='.urlencode($GO_MODULES->modules['users']['url'].'user.php?user_id='.$new_user_id.'&return_to=index.php');
								$subject = $user['enabled'] == '1' ? $admin_new_user_subject : $admin_new_user_subject_activate;

								sendmail($GO_CONFIG->webmaster_email, $GO_CONFIG->webmaster_email, $GO_CONFIG->title, $subject, $body);
							}

							//create Group-Office home directory
							$old_umask = umask(000);
							mkdir($GO_CONFIG->file_storage_path.stripslashes($user['username']), $GO_CONFIG->create_mode);
							umask($old_umask);

							//confirm registration to the user and exit the script so the form won't load

							$link = "javascript:document.login_form.task.value='login';document.login_form.submit();";
							if($user['enabled']=='1')
							{								
								$h1 = new html_element('h1',  sprintf($registration_self_success,$GO_CONFIG->title));
								$form->add_html_element($h1);
								$p = new html_element('p',  $registration_self_success_text);
								$form->add_html_element($p);
								
								
								if($GLOBALS['GO_AUTH']->login($user['username'], $user['password'], $_SESSION['auth_source']))
								{						
										$link = "javascript:document.location='$goto_url'";
								}
							}else {
								$p = new html_element('p',  $registration_success_activate);
								$form->add_html_element($p);
							}

							$input = new input('button','cmdContinue',$GLOBALS['cmdContinue']);
							$input->set_attribute('onclick',$link);
							$form->add_html_element($input);

							$template_item=$this->get_template_item($this->template['login_template_item_id']);

							$html = $this->generate_header($this->template['doctype']);
							$template_content = $this->parse_template($template_item['content']);
							$html .= str_replace($this->get_tag('content', $template_content), $form->get_html(), $template_content);
							$html .= $this->generate_footer();
							return $html;
							exit();

							exit();
						}else
						{
							$error = $registration_failure;
						}
					}
				}





				$form->add_html_element(new html_element('h1', str_replace("%groupoffice_title%",$GO_CONFIG->title, $registration_title)));
				$form->add_html_element(new html_element('p', $registration_text));

				if (isset($feedback))
				{
					$p = new html_element('p', $feedback);
					$p->set_attribute('class','error');
					$form->add_html_element($p);
				}

				$table = new table();
				$row = new table_row();


				$row->add_cell(new table_cell($GLOBALS['strFirstName'].'*:'));
				$input = new input('text','first_name',$user['first_name']);
				$input->set_attribute('style','width:280px');
				$input->set_attribute('maxlength','50');
				$row->add_cell(new table_cell($input->get_html()));
				$table->add_row($row);

				$row = new table_row();
				$row->add_cell(new table_cell($GLOBALS['strMiddleName'].':'));
				$input = new input('text','middle_name',$user['middle_name']);
				$input->set_attribute('style','width:280px');
				$input->set_attribute('maxlength','50');
				$row->add_cell(new table_cell($input->get_html()));
				$table->add_row($row);


				$row = new table_row();
				$row->add_cell(new table_cell($GLOBALS['strLastName'].'*:'));
				$input = new input('text','last_name', $user['last_name']);
				$input->set_attribute('style','width:280px');
				$input->set_attribute('maxlength','50');
				$row->add_cell(new table_cell($input->get_html()));
				$table->add_row($row);



				if(in_array('title_initials',$fields))
				{
					$row = new table_row();


					$end=in_array('inititals_title',$required_fields)? '*:' : ':';
					$row->add_cell(new table_cell($GLOBALS['strTitle'].' / '.$GLOBALS['strInitials'].$end));

					$input1 = new input('text','title', $user['title']);
					$input1->set_attribute('style','width:135px');
					$input1->set_attribute('maxlength','12');

					$span = new html_element('span', ' / ');
					$span->set_attribute('style', 'width: 20px;text-align:center;');

					$input2 = new input('text','initials', $user['initials']);
					$input2->set_attribute('style','width:135px');
					$input2->set_attribute('maxlength','50');

					$row->add_cell(new table_cell($input1->get_html().$span->get_html().$input2->get_html()));
					$table->add_row($row);
				}



				if(in_array('sex',$fields))
				{
					$row = new table_row();
					$end=in_array('sex',$required_fields)? '*:' : ':';
					$row->add_cell(new table_cell($GLOBALS['strSex'].$end));
					$radiogroup = new radiogroup('sex', $user['sex']);
					$male_button = new radiobutton('sex_m', 'M');
					$female_button = new radiobutton('sex_f', 'F');

					$row->add_cell(new table_cell($radiogroup->get_option($male_button, $GLOBALS['strSexes']['M']).$radiogroup->get_option($female_button, $GLOBALS['strSexes']['F'])));
					$table->add_row($row);
				}

				if(in_array('birthday',$fields))
				{
					$end=in_array('birthday',$required_fields)? '*:' : ':';
					$row = new table_row();
					$row->add_cell(new table_cell($GLOBALS['strBirthday'].$end));
					$row->add_cell(new table_cell($birthday_picker->get_html()));
					$table->add_row($row);
				}

				$row = new table_row();
				$row->add_cell(new table_cell($GLOBALS['strEmail'].'*:'));
				$input = new input('text','email', $user['email']);
				$input->set_attribute('style','width:280px');
				$input->set_attribute('maxlength','50');
				$row->add_cell(new table_cell($input->get_html()));
				$table->add_row($row);

				$row = new table_row();
				$cell = new table_cell('&nbsp;');
				$cell->set_attribute('colspan','2');
				$row->add_cell($cell);
				$table->add_row($row);

				if(in_array('address',$fields))
				{
					$end=in_array('address',$required_fields)? '*:' : ':';

					$row = new table_row();
					$row->add_cell(new table_cell($GLOBALS['strAddress'].$end));
					$input = new input('text','address', $user['address']);
					$input->set_attribute('style','width:230px');
					$input->set_attribute('maxlength','50');

					$input1 = new input('text','address_no', $user['address_no']);
					$input1->set_attribute('style','width:47px');
					$input1->set_attribute('maxlength','10');

					$row->add_cell(new table_cell($input->get_html().$input1->get_html()));
					$table->add_row($row);

					$end=in_array('zip',$required_fields)? '*:' : ':';
					$row = new table_row();
					$row->add_cell(new table_cell($GLOBALS['strZip'].$end));
					$input = new input('text','zip', $user['zip']);
					$input->set_attribute('style','width:280px');
					$input->set_attribute('maxlength','20');
					$row->add_cell(new table_cell($input->get_html()));
					$table->add_row($row);

					$end=in_array('city',$required_fields)? '*:' : ':';
					$row = new table_row();
					$row->add_cell(new table_cell($GLOBALS['strCity'].$end));
					$input = new input('text','city', $user['city']);
					$input->set_attribute('style','width:280px');
					$input->set_attribute('maxlength','50');
					$row->add_cell(new table_cell($input->get_html()));
					$table->add_row($row);

					$end=in_array('state',$required_fields)? '*:' : ':';
					$row = new table_row();
					$row->add_cell(new table_cell($GLOBALS['strState'].$end));
					$input = new input('text','state', $user['state']);
					$input->set_attribute('style','width:280px');
					$input->set_attribute('maxlength','30');
					$row->add_cell(new table_cell($input->get_html()));
					$table->add_row($row);

					$end=in_array('country_id',$required_fields)? '*:' : ':';
					$row = new table_row();
					$row->add_cell(new table_cell($GLOBALS['strCountry'].$end));
					$select = new select('country_id', $user['country_id']);
					$select->add_value('0', $GLOBALS['cmdPleaseSelect']);
					$GO_USERS->get_countries();
					while($GO_USERS->next_record())
					{
						$select->add_value($GO_USERS->f('id'), $GO_USERS->f('name'));
					}
					$row->add_cell(new table_cell($select->get_html()));
					$table->add_row($row);

					$row = new table_row();
					$cell = new table_cell('&nbsp;');
					$cell->set_attribute('colspan','2');
					$row->add_cell($cell);
					$table->add_row($row);
				}

				$break=false;
				if(in_array('home_phone',$fields))
				{
					$end=in_array('home_phone',$required_fields)? '*:' : ':';
					$row = new table_row();
					$row->add_cell(new table_cell($GLOBALS['strPhone'].$end));
					$input = new input('text','home_phone', $user['home_phone']);
					$input->set_attribute('style','width:280px');
					$input->set_attribute('maxlength','20');
					$row->add_cell(new table_cell($input->get_html()));
					$table->add_row($row);
					$break=true;
				}

				if(in_array('fax',$fields))
				{
					$end=in_array('fax',$required_fields)? '*:' : ':';
					$row = new table_row();
					$row->add_cell(new table_cell($GLOBALS['strFax'].$end));
					$input = new input('text','fax', $user['fax']);
					$input->set_attribute('style','width:280px');
					$input->set_attribute('maxlength','20');
					$row->add_cell(new table_cell($input->get_html()));
					$table->add_row($row);
					$break=true;
				}
				if(in_array('cellular',$fields))
				{
					$end=in_array('cellular',$required_fields)? '*:' : ':';
					$row = new table_row();
					$row->add_cell(new table_cell($GLOBALS['strCellular'].$end));
					$input = new input('text','cellular', $user['cellular']);
					$input->set_attribute('style','width:280px');
					$input->set_attribute('maxlength','20');
					$row->add_cell(new table_cell($input->get_html()));
					$table->add_row($row);
					$break=true;
				}

				if($break)
				{
					$row = new table_row();
					$cell = new table_cell('&nbsp;');
					$cell->set_attribute('colspan','2');
					$row->add_cell($cell);
					$table->add_row($row);
					$break=false;
				}



				$break = false;
				if(in_array('company',$fields))
				{
					$end=in_array('company',$required_fields)? '*:' : ':';
					$row = new table_row();
					$row->add_cell(new table_cell($GLOBALS['strCompany'].$end));
					$input = new input('text','company', $user['company']);
					$input->set_attribute('style','width:280px');
					$input->set_attribute('maxlength','50');
					$row->add_cell(new table_cell($input->get_html()));
					$table->add_row($row);
					$break=true;
				}

				if(in_array('department',$fields))
				{
					$end=in_array('department',$required_fields)? '*:' : ':';
					$row = new table_row();
					$row->add_cell(new table_cell($GLOBALS['strDepartment'].$end));
					$input = new input('text','department', $user['department']);
					$input->set_attribute('style','width:280px');
					$input->set_attribute('maxlength','50');
					$row->add_cell(new table_cell($input->get_html()));
					$table->add_row($row);
					$break=true;
				}

				if(in_array('function',$fields))
				{
					$end=in_array('function',$required_fields)? '*:' : ':';
					$row = new table_row();
					$row->add_cell(new table_cell($GLOBALS['strFunction'].$end));
					$input = new input('text','function', $user['function']);
					$input->set_attribute('style','width:280px');
					$input->set_attribute('maxlength','50');
					$row->add_cell(new table_cell($input->get_html()));
					$table->add_row($row);
					$break=true;
				}

				if($break)
				{
					$row = new table_row();
					$cell = new table_cell('&nbsp;');
					$cell->set_attribute('colspan','2');
					$row->add_cell($cell);
					$table->add_row($row);
					$break=false;
				}
				if(in_array('work_address',$fields))
				{
					$break=true;
					$end=in_array('work_address',$required_fields)? '*:' : ':';
					$row = new table_row();
					$row->add_cell(new table_cell($GLOBALS['strWorkAddress'].$end));
					$input = new input('text','work_address', $user['work_address']);
					$input->set_attribute('style','width:230px');
					$input->set_attribute('maxlength','50');

					$input1 = new input('text','work_address_no', $user['work_address_no']);
					$input1->set_attribute('style','width:47px');
					$input1->set_attribute('maxlength','10');

					$row->add_cell(new table_cell($input->get_html().$input1->get_html()));
					$table->add_row($row);

					$end=in_array('work_zip',$required_fields)? '*:' : ':';
					$row = new table_row();
					$row->add_cell(new table_cell($GLOBALS['strWorkZip'].$end));
					$input = new input('text','work_zip', $user['work_zip']);
					$input->set_attribute('style','width:280px');
					$input->set_attribute('maxlength','20');
					$row->add_cell(new table_cell($input->get_html()));
					$table->add_row($row);

					$end=in_array('work_city',$required_fields)? '*:' : ':';
					$row = new table_row();
					$row->add_cell(new table_cell($GLOBALS['strWorkCity'].$end));
					$input = new input('text','work_city', $user['work_city']);
					$input->set_attribute('style','width:280px');
					$input->set_attribute('maxlength','50');
					$row->add_cell(new table_cell($input->get_html()));
					$table->add_row($row);

					$end=in_array('work_state',$required_fields)? '*:' : ':';
					$row = new table_row();
					$row->add_cell(new table_cell($GLOBALS['strWorkState'].$end));
					$input = new input('text','work_state', $user['work_state']);
					$input->set_attribute('style','width:280px');
					$input->set_attribute('maxlength','30');
					$row->add_cell(new table_cell($input->get_html()));
					$table->add_row($row);

					$end=in_array('inititals_title',$required_fields)? '*:' : ':';
					$row = new table_row();
					$row->add_cell(new table_cell($GLOBALS['strCountry'].$end));


					$select = new select('work_country_id', $user['work_country_id']);
					$select->add_value('0', $GLOBALS['cmdPleaseSelect']);
					$GO_USERS->get_countries();
					while($GO_USERS->next_record())
					{
						$select->add_value($GO_USERS->f('id'), $GO_USERS->f('name'));
					}
					$row->add_cell(new table_cell($select->get_html()));
					$table->add_row($row);

					$row = new table_row();
					$cell = new table_cell('&nbsp;');
					$cell->set_attribute('colspan','2');
					$row->add_cell($cell);
					$table->add_row($row);
				}

				if(in_array('work_phone',$fields))
				{
					$break=true;
					$end=in_array('work_phone',$required_fields)? '*:' : ':';
					$row = new table_row();
					$row->add_cell(new table_cell($GLOBALS['strWorkphone'].$end));
					$input = new input('text','work_phone', $user['work_phone']);
					$input->set_attribute('style','width:280px');
					$input->set_attribute('maxlength','20');
					$row->add_cell(new table_cell($input->get_html()));
					$table->add_row($row);
				}

				if(in_array('work_fax',$fields))
				{
					$break=true;
					$end=in_array('work_fax',$required_fields)? '*:' : ':';
					$row = new table_row();
					$row->add_cell(new table_cell($GLOBALS['strWorkFax'].$end));
					$input = new input('text','work_fax', $user['work_fax']);
					$input->set_attribute('style','width:280px');
					$input->set_attribute('maxlength','20');
					$row->add_cell(new table_cell($input->get_html()));
					$table->add_row($row);
				}

				if(in_array('homepage',$fields))
				{
					$break=true;
					$end=in_array('homepage',$required_fields)? '*:' : ':';
					$row = new table_row();
					$row->add_cell(new table_cell($GLOBALS['strHomepage'].$end));
					$input = new input('text','homepage', $user['homepage']);
					$input->set_attribute('style','width:280px');
					$input->set_attribute('maxlength','20');
					$row->add_cell(new table_cell($input->get_html()));
					$table->add_row($row);
				}


				if($break)
				{
					$row = new table_row();
					$cell = new table_cell('&nbsp;');
					$cell->set_attribute('colspan','2');
					$row->add_cell($cell);
					$table->add_row($row);
					$break=false;
				}
				$row = new table_row();
				$row->add_cell(new table_cell($GLOBALS['strUsername'].'*:'));
				$input = new input('text', 'username');
				$input->set_attribute('style','width:200px');
				$row->add_cell(new table_cell($input->get_html()));
				$table->add_row($row);

				if($GO_CONFIG->auto_activate_accounts)
				{
					$row = new table_row();
					$row->add_cell(new table_cell($admin_password.'*:'));
					$input = new input('password', 'pass1');
					$input->set_attribute('style','width:200px');
					$row->add_cell(new table_cell($input->get_html()));
					$table->add_row($row);

					$row = new table_row();
					$row->add_cell(new table_cell($admin_confirm_password.'*:'));
					$input = new input('password', 'pass2');
					$input->set_attribute('style','width:200px');
					$row->add_cell(new table_cell($input->get_html()));
					$table->add_row($row);
				}

				$form->add_html_element($table);

				$input = new input('submit','cmdOk',$GLOBALS['cmdOk']);
				//$input->set_attribute('onclick',"javascript:document.login_form.login_task.value='register';document.login_form.submit();");
				$form->add_html_element($input);

				$input = new input('button','cmdReset',$GLOBALS['cmdReset']);
				$input->set_attribute('onclick',"javascript:document.login_form.reset();");
				$form->add_html_element($input);

				$input = new input('button','cmdCancel',$GLOBALS['cmdCancel']);
				$input->set_attribute('onclick',"javascript:document.login_form.task.value='login';document.login_form.submit();");
				$form->add_html_element($input);


				$template_item=$this->get_template_item($this->template['login_template_item_id']);

				$html = $this->generate_header($this->template['doctype'],'','','',$birthday_picker->get_header(),'onload="document.login_form.first_name.focus();"');
				$template_content = $this->parse_template($template_item['content']);
				$html .= str_replace($this->get_tag('content', $template_content), $form->get_html(), $template_content);
				$html .= $this->generate_footer();
				return $html;
				break;

			default:

				if($this->folder_id==0 && $this->file_id==0)
				{
					$this->folder_id = $this->site['root_folder_id'];
					if($this->site['start_file_id']>0)
					{
						$this->file_id=$this->site['start_file_id'];
					}
				}

				if($this->file_id==0)
				{
					$this->file_id = $this->find_page($this->folder_id);
				}

				require_once ($GO_LANGUAGE->get_language_file('cms'));



				if ($this->file_id > 0) {

					if(!$file = $this->get_file($this->file_id))
					{
						$this->file_id=$this->find_page($this->site['root_folder_id']);
						$file = $this->get_file($this->file_id);
					}

					if($file['folder_id'] != $this->folder_id)
					{
						$this->folder_id = $file['folder_id'];
					}

					if (!eregi('htm', $file['extension'])) {
						return $file['content'];
					}
				} else {
					$file['acl']=0;
					$file['title'] = '';
					$meta_title = '';
					$file['keywords'] = '';
					$file['description'] = '';
				}


				$folder = $this->get_folder($this->folder_id);

				if ($this->task == '' && ($folder['multipage'] == '1')) {
					$this->task = 'multipage_display';
				}


				if($file['acl']>0)
				{
					$this->authenticate();
					if(!$GO_SECURITY->has_permission($GO_SECURITY->user_id, $file['acl']))
					{
						$this->access_denied();
					}
				}elseif($folder['acl']>0)
				{
					$this->authenticate();
					if(!$GO_SECURITY->has_permission($GO_SECURITY->user_id, $folder['acl']))
					{
						$this->access_denied();
					}
				}

				$template_item_id = $this->file_id > 0 && $file['template_item_id'] > 0 ? $file['template_item_id'] : $folder['template_item_id'];
				$main_template_item = $this->get_template_item($template_item_id);

				$max_length = 300;
				$print_field = '';
				$date_format = 'd-m-Y';
				$title_class = '';
				$class = '';
				$read_more_class = '';
				$read_more_text = '';
				$template = '';
				$item_template = '';
				$hide_hot_items = 'false';

				if ($main_template_item) {

					$main_template_content = $this->parse_template($main_template_item['content']);

					$content_tag = $this->get_tag('content', $main_template_content);
					$attributes = $this->get_attributes($content_tag);

					//max_length for multipage display
					$max_length = isset ($attributes['max_length']) ? $attributes['max_length'] : $max_length;
					$class = isset ($attributes['class']) ? $attributes['class'] : $class;
					$print_field = isset ($attributes['print_field']) ? $attributes['print_field'] : $print_field;
					$date_format = isset ($attributes['date_format']) ? $attributes['date_format'] : $date_format;
					$title_class = isset ($attributes['title_class']) ? $attributes['title_class'] : $title_class;
					$read_more_class = isset ($attributes['read_more_class']) ? $attributes['read_more_class'] : $read_more_class;
					$read_more_text = isset ($attributes['read_more_text']) ? $attributes['read_more_text'] : $read_more_text;
					$template = isset ($attributes['template']) ? $attributes['template'] : $template;
					$item_template = isset ($attributes['item_template']) ? $attributes['item_template'] : $item_template;
					$hide_hot_items = isset ($attributes['hide_hot_items ']) ? $attributes['hide_hot_items'] : $hide_hot_items;
				}

				//$template = $this->get_template($this->site['template_id']);

				$page = $this->generate_header($this->template['doctype'], $file['title'], $file['keywords'], $file['description'],date_picker::get_header());

				if($this->task == 'multipage_display')
				{

					$content = '';
					if ($this->file_id > 0) {
						$content .= $file['content'];
					}

					$template = $this->get_template_item_by_name($this->site['template_id'], $template);
					if ($template) {
						$template_content = $this->parse_template($template['content']);

					} else {
						$template_content = false;
					}

					$multipage = $this->build_multipage($this->folder_id, $max_length, $template, $class, $print_field, $date_format, $title_class, $read_more_class, $read_more_text, $hide_hot_items);
					if ($template_content) {
						$multipage = str_replace($this->get_tag('content', $template_content), $multipage, $template_content);
					}

					$content .= $multipage;

					$content = str_replace($this->cms_module['full_url'].'view.php', $_SERVER['PHP_SELF'], $content);

					if (isset ($content_tag)) {
						$page .= str_replace($content_tag, $content, $main_template_content);
					} else {
						$page .= $content;
					}
				}else {
					require($GO_LANGUAGE->get_language_file('cms'));
					if ($this->file_id > 0) {
						$content = '';

						$content .= $file['content'];

						$content = $this->print_plugins($content);
						$content = str_replace($this->cms_module['full_url'].'view.php', $_SERVER['PHP_SELF'], $content);

						if (isset ($main_template_content)) {
							$page .= str_replace($this->get_tag('content', $main_template_content), $content, $main_template_content);
						} else {
							$page .= $content;
						}



					} else {
						//get the language file

						if (isset ($main_template_content)) {
							$page .= str_replace($this->get_tag('content', $main_template_content), $cms_empty, $main_template_content);
						} else {
							$page .= $cms_empty;
						}
					}
				}
				$page .= $this->generate_footer();
				break;
		}

		return $page;
	}



	function build_multipage($folder_id, $max_length, $template_name, $class, $print_field, $date_format='d-m-Y', $title_class, $read_more_class, $read_more_text, $hide_hot_items, $hot_items = false) {

		global $GO_SECURITY;

		$template = $this->get_template_item_by_name($this->site['template_id'], $template_name);
		if ($template) {
			$template_content = $this->parse_template($template['content']);
		} else {
			$template_content = false;
		}

		$show = $this->file_id > 0 ? false : true;
		$multipage = '';
		if ($folder = $this->get_folder($folder_id)) {

			$files = $this->get_authorized_files($folder_id, $GO_SECURITY->user_id, $hot_items);

			foreach($files as $file)
			{
				if ((($show && ($hide_hot_items == 'false' || $file['hot_item'] != '1')) || $hot_items) && eregi('htm', $file['extension'])) {
					$multipage_item = '<div class="'.$class.'">';


					if(empty($file['hot_item_text']))
					{
						if (!empty($print_field)) {
							switch($print_field)
							{
								case 'mtime':
									$multipage_item .= '<h1 class="'.$title_class.'">'.date($date_format, $file['mtime']).'</h1>';
									break;

								case 'ctime':
									$multipage_item .= '<h1 class="'.$title_class.'">'.date($date_format, $file['ctime']).'</h1>';
									break;

								case 'name':
									$multipage_item .= '<h1 class="'.$title_class.'">'.strip_extension($file['name']).'</h1>';
									break;
							}
						}
						if ($max_length > 0) {
							$stripped_content = $this->strip_for_multipage($file['content']);

							$content = cut_string($stripped_content, $max_length);
							$multipage_item .= $content;

							if (strlen($file['content']) != strlen($content)) {
								$multipage_item .= '<br /><div style="text-align:right;"><a class="'.$read_more_class.'" href="'.$this->create_url($file['folder_id'], $file['id']).'">'.$read_more_text.'</a></div>';
							}
						}
					}else {
						$multipage_item .= $file['hot_item_text'];
						$multipage_item .= '<br /><div style="text-align:right;"><a class="'.$read_more_class.'" href="'.$this->create_url($file['folder_id'], $file['id']).'">'.$read_more_text.'</a></div>';
					}
					$multipage_item .= '</div>';

					if ($template_content) {
						$multipage .= str_replace($this->get_tag('content', $template_content), $multipage_item, $template_content);
					} else {
						$multipage .= $multipage_item;
					}
				}
				if ($this->file_id == $file['id']) {
					$show = true;
				}
			}
			return $this->build_multipage_recursive($folder_id, $max_length, $template_content, $class, $print_field, $date_format, $title_class, $read_more_class, $read_more_text, $hide_hot_items, $hot_items, $multipage);
		} else {
			return false;
		}
	}

	function build_multipage_recursive($folder_id, $max_length, $template_content, $class, $print_field, $date_format, $title_class, $read_more_class, $read_more_text, $hide_hot_items, $hot_items = false, $multipage = '') {

		global $GO_SECURITY;

		$folders = $this->get_authorized_folders($folder_id, $GO_SECURITY->user_id);
		if (count($folders)) {
			foreach($folders as $folder)
			{
				if ($folder['disabled'] != '1') {

					$files = $this->get_authorized_files($folder['id'], $GO_SECURITY->user_id, $hot_items);

					foreach($files as $file)
					{
						if ((($this->file_id != $file['id'] && ($hide_hot_items == 'false' || $file['hot_item'] != '1')) || $hot_items) && eregi('htm', $file['extension'])) {
							$multipage_item = '<div class="'.$class.'">';

							if(empty($file['hot_item_text']))
							{
								if (!empty($print_field)) {
									switch($print_field)
									{
										case 'mtime':
											$multipage_item .= '<h1 class="'.$title_class.'">'.date($date_format, $file['mtime']).'</h1>';
											break;

										case 'ctime':
											$multipage_item .= '<h1 class="'.$title_class.'">'.date($date_format, $file['ctime']).'</h1>';
											break;

										case 'name':
											$multipage_item .= '<h1 class="'.$title_class.'">'.strip_extension($file['name']).'</h1>';
											break;
									}
								}
								if ($max_length > 0) {
									$stripped_content = $this->strip_for_multipage($file['content']);

									$content = cut_string($stripped_content, $max_length);
									$multipage_item .= $content;

									if (strlen($file['content']) != strlen($content)) {
										$multipage_item .= '<br /><div style="text-align:right;"><a class="'.$read_more_class.'" href="'.$this->create_url($file['folder_id'], $file['id']).'">'.$read_more_text.'</a></div>';
									}

								}
							}else {
								$multipage_item .= $file['hot_item_text'];
								$multipage_item .= '<br /><div style="text-align:right;"><a class="'.$read_more_class.'" href="'.$this->create_url($file['folder_id'], $file['id']).'">'.$read_more_text.'</a></div>';
							}
							$multipage_item .= '</div>';
							if ($template_content) {
								$multipage .= str_replace($this->get_tag('content', $template_content), $multipage_item, $template_content);
							} else {
								$multipage .= $multipage_item;
							}

						}
						if (!$hot_items) {
							break;
						}
					}
					$multipage .= $this->build_multipage_recursive($folder['id'], $max_length, $template_content, $class, $print_field, $date_format, $title_class, $read_more_class, $read_more_text, $hide_hot_items, $hot_items);
				}
			}
		}
		return $multipage;
	}

	function print_hot_items($attributes) {
		$class = isset ($attributes['class']) ? $attributes['class'] : '';
		$max_length = isset ($attributes['max_length']) ? $attributes['max_length'] : '0';
		$print_title = isset ($attributes['print_title']) ? $attributes['print_title'] : 'false';

		$print_field = isset($attributes['print_field']) ? $attributes['print_field'] : '';
		if($print_title=='true')
		{
			$print_field='name';
		}
		$date_format = isset($attributes['date_format']) ? $attributes['date_format'] : 'd-m-Y';

		$title_class = isset ($attributes['title_class']) ? $attributes['title_class'] : '';
		$read_more_class = isset ($attributes['read_more_class']) ? $attributes['read_more_class'] : '';
		$read_more_text = isset ($attributes['read_more_text']) ? $attributes['read_more_text'] : 'Read more';
		$template_name = isset ($attributes['template']) ? $attributes['template'] : '';
		$item_template = isset ($attributes['item_template']) ? $attributes['item_template'] : '';
		$print_root_only = isset ($attributes['print_root_only']) ? $attributes['print_root_only'] : 'false';

		$template = $this->get_template_item_by_name($this->site['template_id'], $template_name);
		if ($template) {
			$template_content = $this->parse_template($template['content']);
		} else {
			$template_content = false;
		}

		if ($print_root_only == 'true' && $this->folder_id != $this->site['root_folder_id']) {
			return '';
		} else {
			$multipage = $this->build_multipage($this->site['root_folder_id'], $max_length, $item_template, $class, $print_field, $date_format, $title_class, $read_more_class, $read_more_text, false, true);
			if ($template_content) {
				$multipage = str_replace($this->get_tag('content', $template_content), $multipage, $template_content);
			}
			return $multipage;
		}
	}

	function print_template($attributes)
	{
		if(!empty($attributes['name']))
		{
			if($template = $this->get_template_item_by_name($this->site['template_id'], $attributes['name']))
			{
				return $this->parse_template($template['content']);
			}
		}
		return '';
	}

	function create_url($folder_id, $file_id = 0) {
		return $_SERVER['PHP_SELF'].'?folder_id='.$folder_id.'&amp;file_id='.$file_id;
	}

	function print_icon() {
		global $extension, $GO_CONFIG;
		return '<img border="0" align="absmiddle" src="'.get_filetype_image($extension).'" />';
	}

	function print_home($attributes) {
		$text = isset ($attributes['text']) ? $attributes['text'] : '';
		$template_name = isset ($attributes['template']) ? $attributes['template'] : '';
		$active_template_name = isset ($attributes['active_template']) ? $attributes['active_template'] : $template_name;
		$class = isset ($attributes['class']) ? $attributes['class'] : '';
		$active_class = isset ($attributes['active_class']) ? $attributes['active_class'] : $class;
		$print_at_home = isset ($attributes['print_at_home']) ? $attributes['print_at_home'] : 'true';

		if ($this->folder_id == $this->site['root_folder_id'] ) {
			if($print_at_home != 'true') {
				return '';
			}
			$template = $this->get_template_item_by_name($this->site['template_id'], $active_template_name);
			$class =  $active_class ;
		} else {
			$template = $this->get_template_item_by_name($this->site['template_id'], $template_name);
		}
		$home = '<a class="'.$class.'" href="'.$_SERVER['PHP_SELF'].'?folder_id='.$this->site['root_folder_id'].'">'.$text.'</a>';
		if ($template) {
			$template_content = $this->parse_template($template['content']);
			$home = str_replace($this->get_tag('content', $template_content), $home, $template_content);
		}

		return $home;
	}

	function print_path($attributes) {
		$template_name = isset ($attributes['template']) ? $attributes['template'] : '';
		$item_template_name = isset ($attributes['item_template']) ? $attributes['item_template'] : '';
		$active_item_template_name = isset ($attributes['active_item_template']) ? $attributes['active_item_template'] : $item_template_name;
		$class = isset ($attributes['class']) ? $attributes['class'] : '';
		$active_class = isset ($attributes['active_class']) ? $attributes['active_class'] : $class;

		$path = '';
		$id = $this->folder_id;

		$template = $this->get_template_item_by_name($this->site['template_id'], $template_name);
		$item_template_passive = $this->get_template_item_by_name($this->site['template_id'], $item_template_name);
		$item_template_active = $this->get_template_item_by_name($this->site['template_id'], $active_item_template_name);

		$folder = $this->get_folder($id);

		while ($folder['parent_id'] != 0) {
			$item_template = ($this->folder_id == $id) ? $item_template_active : $item_template_passive;
			$_class = ($this->folder_id == $id) ? $active_class : $class;
			if ($item_template) {
				$item_template_content = $this->parse_template($item_template['content']);
				$path = str_replace($this->get_tag('content', $item_template_content), '<a class="'.$class.'" href="'.$this->create_url($folder['id']).'">'.$folder['name'].'</a>', $item_template_content).$path;
			} else {
				$path = '<a class="'.$_class.'" href="'.$this->create_url($folder['id']).'">'.$folder['name'].'</a>'.$path;
			}

			$id = $folder['parent_id'];
			$folder = $this->get_folder($id);
		}

		if ($template) {
			$template_content = $this->parse_template($template['content']);
			$path = str_replace($this->get_tag('content', $template_content), $path, $template_content);
		}
		return $path;
	}

	function print_active_doc($attributes) {
		global $extension;

		$template_name = isset ($attributes['template']) ? $attributes['template'] : '';
		$class = isset ($attributes['class']) ? $attributes['class'] : '';

		$current_file = '';

		if (isset ($this->file_id) && $file = $this->get_file($this->file_id)) {
			$extension = get_extension($file['name']);
			$title = ($file['title'] == '') ? strip_extension($file['name']) : $file['title'];

			if ($template = $this->get_template_item_by_name($this->site['template_id'], $template_name)) {
				$current_file = $this->parse_template($template['content']);
			}

			$current_file = str_replace($this->get_tag('content', $current_file), '<span class="'.$class.'">'.$title.'</span>', $current_file);
		}
		return $current_file;
	}


	function print_folders($attributes) {
		$template_name = isset ($attributes['template']) ? $attributes['template'] : '';
		$item_template_name = isset ($attributes['item_template']) ? $attributes['item_template'] : '';
		$class = isset ($attributes['class']) ? $attributes['class'] : '';
		$print_rootfolders = isset ($attributes['print_rootfolders']) ? $attributes['print_rootfolders'] : 'true';

		$folders = '';
		$item_template = $this->get_template_item_by_name($this->site['template_id'], $item_template_name);
		$template = $this->get_template_item_by_name($this->site['template_id'], $template_name);

		if ($print_rootfolders == 'true' || $this->folder_id != $this->site['root_folder_id']) {
			$count = $this->get_visible_folders($this->folder_id);
			if ($count > 0) {
				while ($this->next_record()) {
					if ($item_template) {
						$item_template_content = $this->parse_template($item_template['content']);
						$folders .= str_replace($this->get_tag('content', $item_template_content), '<a class="'.$class.'" href="'.$this->create_url($this->f('id')).'" title="'.$this->f('name').'">'.$this->f('name').'</a>', $item_template_content);
					} else {
						$folders .= '<a class="'.$class.'" title="'.$this->f('name').'" href="'.$this->create_url($this->f('id')).'">'.$this->f('name').'</a>';
					}
				}
				if ($template) {
					$template_content = $this->parse_template($template['content']);
					$folders = str_replace($this->get_tag('content', $template_content), $folders, $template_content);
				}
			}
		}
		return $folders;
	}

	function print_back($attributes) {
		$template_name = isset ($attributes['template']) ? $attributes['template'] : '';
		$class = isset ($attributes['class']) ? $attributes['class'] : '';
		$text = isset ($attributes['text']) ? $attributes['text'] : '';
		$print_rootfolders = isset ($attributes['print_rootfolders']) ? $attributes['print_rootfolders'] : 'true';

		$back = '';
		$template = $this->get_template_item_by_name($this->site['template_id'], $template_name);

		$folder = $this->get_folder($this->folder_id);

		if ($folder['parent_id'] != 0 && ($print_rootfolders == 'true' || $folder['parent_id'] != $this->site['root_folder_id'])) {
			$back = '<a class="'.$class.'" href="'.$this->create_url($folder['parent_id']).'">'.$text.'</a>';
			if ($template) {
				$template_content = $this->parse_template($template['content']);
				$back = str_replace($this->get_tag('content', $template_content), $back, $template_content);
			}
		}
		return $back;
	}
	
	function get_next_previous_files()
	{
		global $GO_SECURITY;
		
		$this->previous_file_id=0;
		$this->next_file_id=0;

		$files = $this->get_authorized_files($this->folder_id, $GO_SECURITY->user_id);		
		for($i=0;$i<count($files);$i++)
		{
			if($files[$i]['id']==$this->file_id)
			{
				if(isset($files[$i-1]))
				{
					$this->previous_file_id=$files[$i-1]['id'];
				}
				if(isset($files[$i+1]))
				{
					$this->next_file_id=$files[$i+1]['id'];
				}
				break;				
			}
	
		}
	}
	
	
	function print_nextfile($attributes) {
		$template_name = isset ($attributes['template']) ? $attributes['template'] : '';
		$class = isset ($attributes['class']) ? $attributes['class'] : '';
		$text = isset ($attributes['text']) ? $attributes['text'] : '';
		$print_rootfolders = isset ($attributes['print_rootfolders']) ? $attributes['print_rootfolders'] : 'true';

		$nextpage = '';
		$template = $this->get_template_item_by_name($this->site['template_id'], $template_name);
		
		if(!isset($this->next_file_id))
		{
			$this->get_next_previous_files();
		}

		if ($this->next_file_id>0) {
			
			$nextpage = '<a class="'.$class.'" href="'.$this->create_url($this->folder_id, $this->next_file_id).'">'.$text.'</a>';
			if ($template) {
				$template_content = $this->parse_template($template['content']);
				$nextpage = str_replace($this->get_tag('content', $template_content), $nextpage, $template_content);
			}
		}
		return $nextpage;
	}
	
	function print_previousfile($attributes) {
		$template_name = isset ($attributes['template']) ? $attributes['template'] : '';
		$class = isset ($attributes['class']) ? $attributes['class'] : '';
		$text = isset ($attributes['text']) ? $attributes['text'] : '';
		$print_rootfolders = isset ($attributes['print_rootfolders']) ? $attributes['print_rootfolders'] : 'true';

		$previouspage = '';
		$template = $this->get_template_item_by_name($this->site['template_id'], $template_name);
		
		if(!isset($this->previous_file_id))
		{
			$this->get_next_previous_files();
		}

		if ($this->previous_file_id>0) {
			$previouspage = '<a class="'.$class.'" href="'.$this->create_url($this->folder_id, $this->previous_file_id).'">'.$text.'</a>';
			if ($template) {
				$template_content = $this->parse_template($template['content']);
				$previouspage = str_replace($this->get_tag('content', $template_content), $previouspage, $template_content);
			}
		}
		return $previouspage;
	}

	function print_rootfolders($attributes) {
		$template_name = isset ($attributes['template']) ? $attributes['template'] : '';
		$item_template_name = isset ($attributes['item_template']) ? $attributes['item_template'] : '';
		$active_item_template_name = isset ($attributes['active_item_template']) ? $attributes['active_item_template'] : $item_template_name;
		$class = isset ($attributes['class']) ? $attributes['class'] : '';
		$active_class = isset ($attributes['active_class']) ? $attributes['active_class'] : $class;

		$folders = '';
		$active_item_template = $this->get_template_item_by_name($this->site['template_id'], $active_item_template_name);
		$passive_item_template = $this->get_template_item_by_name($this->site['template_id'], $item_template_name);
		$template = $this->get_template_item_by_name($this->site['template_id'], $template_name);

		$count = $this->get_visible_folders($this->site['root_folder_id']);
		if ($count > 0) {

			while ($this->next_record()) {
				$item_template = ($this->f('id') == $this->folder_id) ? $active_item_template : $passive_item_template;
				$_class = ($this->f('id') == $this->folder_id) ? $active_class : $class;

				if ($item_template) {
					$item_template_content = $this->parse_template($item_template['content']);
					$folders .= str_replace($this->get_tag('content', $item_template_content), '<a title="'.$this->f('name').'" class="'.$_class.'" href="'.$this->create_url($this->f('id')).'">'.$this->f('name').'</a>', $item_template_content);
				} else {
					$folders .= '<a title="'.$this->f('name').'" class="'.$_class.'" href="'.$this->create_url($this->f('id')).'">'.$this->f('name').'</a>';
				}
			}
			if ($template) {
				$template_content = $this->parse_template($template['content']);
				$folders = str_replace($this->get_tag('content', $template_content), $folders, $template_content);
			}
		}
		return $folders;
	}

	function print_folderlist($attributes) {
		global $GO_CONFIG, $GO_SECURITY;

		$path = isset ($attributes['path']) ? $attributes['path'] : '';
		$id = isset ($attributes['id']) ? $attributes['id'] : '';

		$folders = '';
		if ($resolved_path = $this->resolve_url($path, $this->site['root_folder_id'])) {
			$count = $this->get_visible_folders($resolved_path['folder_id']);
			$folders_arr = $this->get_authorized_folders($resolved_path['folder_id'], $GO_SECURITY->user_id, true);
			if (count($folders_arr)) {
				$folders .= '<ul id="'.$id.'">';
				foreach($folders_arr as $folder)
				{
					if ($folder['id'] == $this->folder_id) {
						$folders .= '<li><strong><a title="'.$folder['name'].'" href="'.$this->create_url($folder['id']).'">'.$folder['name'].'</a></strong>';


						$subfolders_arr = $this->get_authorized_folders($folder['id'], $GO_SECURITY->user_id, true);
						if (count($subfolders_arr)) {
							$folders .= '<ul>';
							foreach($subfolders_arr as $subfolder)
							{
								$folders .= '<li><a title="'.$subfolder['name'].'" href="'.$this->create_url($subfolder['id']).'">'.$subfolder['name'].'</a></li>';
							}
						}

						$file_count = $cms->get_files($this->folder_id, 'priority', 'ASC');

						$files = $this->get_authorized_files($this->folder_id, $GO_SECURITY->user_id);


						if (count($files)) {
							$browser = detect_browser();

							if ($folder_count == 0) {
								$folders .= '<ul>';
							}
							if ($browser['name'] == 'MSIE') {
								$target = '_blank';
							} else {
								$target = '_self';
							}

							foreach($files as $file)
							{
								if ($this->file_id == $file['id']) {
									$folders .= '<li><strong>';
								} else {
									$folders .= '<li>';
								}
								$extension = $file['extension'];

								if ($this->publish_static || eregi('htm', $file['extension'])) {
									$folders .= '<a href="'.$this->create_url($this->folder_id, $file['id']).'">'.strip_extension($file['name']).'</a>';
								} else {
									$folders .= '<a target="'.$target.'" href="'.$this->cms_module['url'].'download.php?site_id='.$this->site['id'].'&amp;file_id='.$file['id'].'">'.strip_extension($file['name']).'</a>';
								}

								if ($this->file_id == $file['id']) {
									$folders .= '</strong></li>';
								} else {
									$folders .= '</li>';
								}
							}
							if ($folder_count > 0 || $file_count > 0) {
								$folders .= '</ul>';
							}
							$folders .= '</li>';
						}
					} else {
						$folders .= '<li><a href="'.$this->create_url($folder['id']).'">'.$folder['name'].'</a></li>';
					}
				}

				$folders .= '</ul>';
			}
			return $folders;
		}
	}

	function print_files($attributes) {
		global $GO_CONFIG, $extension, $GO_SECURITY;

		$template_name = isset ($attributes['template']) ? $attributes['template'] : '';
		$item_template_name = isset ($attributes['item_template']) ? $attributes['item_template'] : '';
		$active_item_template_name = isset ($attributes['active_item_template']) ? $attributes['active_item_template'] : $item_template_name;
		$class = isset ($attributes['class']) ? $attributes['class'] : '';
		$active_class = isset ($attributes['active_class']) ? $attributes['active_class'] : $class;
		$print_single = isset ($attributes['print_single']) ? $attributes['print_single'] : 'true';


		$files = '';
		$template = $this->get_template_item_by_name($this->site['template_id'], $template_name);
		$item_template_passive = $this->get_template_item_by_name($this->site['template_id'], $item_template_name);
		$item_template_active = $this->get_template_item_by_name($this->site['template_id'], $active_item_template_name);

		$files_arr = $this->get_authorized_files($this->folder_id, $GO_SECURITY->user_id);

		$count = count($files_arr);
		$browser = detect_browser();
		if ($browser['name'] == 'MSIE') {
			$target = '_blank';
		} else {
			$target = '_self';
		}

		if ($count > 1 || ($count == 1 && $print_single == 'true')) {
			foreach($files_arr as $file)
			{
				$extension = $file['extension'];

				$item_template = ($this->file_id == $file['id']) ? $item_template_active : $item_template_passive;
				$_class = ($this->file_id == $file['id']) ? $active_class : $class;

				$name = strip_extension($file['name']);
				$title = $file['title'] == '' ? $name : $file['title'];
				if ($this->publish_static || eregi('htm', $file['extension'])) {
					$content = '<a title="'.$title.'" class="'.$_class.'" href="'.$this->create_url($this->folder_id, $file['id']).'">'.$name.'</a>';
				} else {
					$content = '<a title="'.$title.'" target="'.$target.'" class="'.$_class.'" href="'.$this->cms_module['url'].'download.php?site_id='.$this->site['id'].'&amp;file_id='.$file['id'].'">'.$name.'</a>';
				}
				if ($item_template) {
					$item_template_content = $this->parse_template($item_template['content']);
					$files .= str_replace($this->get_tag('content', $item_template_content), $content, $item_template_content);
				} else {
					$files .= $content;
				}
			}

			if ($template) {
				$template_content = $this->parse_template($template['content']);
				$files = str_replace($this->get_tag('content', $template_content), $files, $template_content);
			}
		}
		return $files;
	}

	function print_items($attributes) {
		global $GO_CONFIG, $extension, $GO_SECURITY;

		$folder_id = isset ($attributes['folder_id']) ? $attributes['folder_id'] : 0;
		$template_name = isset ($attributes['template']) ? $attributes['template'] : '';
		$item_template_name = isset ($attributes['item_template']) ? $attributes['item_template'] : '';
		$active_item_template_name = isset ($attributes['active_item_template']) ? $attributes['active_item_template'] : $item_template_name;
		$class = isset ($attributes['class']) ? $attributes['class'] : '';
		$active_class = isset ($attributes['active_class']) ? $attributes['active_class'] : $class;
		$print_root_only = isset ($attributes['print_root_only']) ? $attributes['print_root_only'] : 'false';
		$print_root = isset ($attributes['print_root']) ? $attributes['print_root'] : 'true';
		$menu = isset ($attributes['menu']) ? $attributes['menu'] : '';

		$return = '';
		$template = $this->get_template_item_by_name($this->site['template_id'], $template_name);
		$item_template_passive = $this->get_template_item_by_name($this->site['template_id'], $item_template_name);
		$item_template_active = $this->get_template_item_by_name($this->site['template_id'], $active_item_template_name);

		if($folder_id>0)
		{
			$items = $this->get_authorized_items($folder_id, $GO_SECURITY->user_id, true);
		}else
		{
			if ($print_root_only == 'false') {
				if ($print_root == 'false' && $this->folder_id == $this->site['root_folder_id']) {
					return '';
				}
				$items = $this->get_authorized_items($this->folder_id, $GO_SECURITY->user_id, true);
			} else {
				$items = $this->get_authorized_items($this->site['root_folder_id'], $GO_SECURITY->user_id, true);
			}
		}
		$count = count($items);

		while ($item = array_shift($items)) {
			if (isset ($item['extension'])) {
				$extension = $item['extension'];

				$item_template = ($this->file_id == $item['id']) ? $item_template_active : $item_template_passive;
				$_class = ($this->file_id == $item['id']) ? $active_class : $class;

				$name = strip_extension($item['name']);
				$title = $item['title'] == '' ? $name : $item['title'];
				if ($this->publish_static || eregi('htm', $extension)) {
					$content = '<a title="'.$title.'" class="'.$_class.'" href="'.$this->create_url($item['folder_id'], $item['id']).'">'.$name.'</a>';
				} else {
					$content = '<a title="'.$title.'" target="_blank" class="'.$_class.'" href="'.$this->cms_module['url'].'download.php?site_id='.$this->site['id'].'&amp;file_id='.$item['id'].'">'.$name.'</a>';
				}
				if ($item_template) {
					$item_template_content = $this->parse_template($item_template['content']);
					$return .= str_replace($this->get_tag('content', $item_template_content), $content, $item_template_content);
				} else {
					$return .= $content;
				}
			} else {
				$extension = '';
				$item_template = ($this->folder_id == $item['id']) ? $item_template_active : $item_template_passive;
				$_class = ($this->folder_id == $item['id']) ? $active_class : $class;

				if ($menu != '') {
					$menu_id = 'goMenuID_'.$item['id'];
					$menu_object = 'goMenu_'.$item['id'];
					$link = '<div id="'.$menu_id.'"></div>';
					$link .= $this->generate_menu_object($item, $menu_object);

					$link .= '<script type="text/javascript">cmDraw("'.$menu_id.'", '.$menu_object.' , "hbr", cm'.$menu.', "'.$menu.'");</script>';
				} else {
					$link = '<a class="'.$_class.'" href="'.$this->create_url($item['id']).'" title="'.$item['name'].'">'.$item['name'].'</a>';
				}

				if ($item_template) {
					$item_template_content = $this->parse_template($item_template['content']);
					$return .= str_replace($this->get_tag('content', $item_template_content), $link, $item_template_content);
				} else {
					$return .= $link;
				}
			}
		}

		if ($template) {
			$template_content = $this->parse_template($template['content']);
			$return = str_replace($this->get_tag('content', $template_content), $return, $template_content);
		}

		return $return;
	}


	function print_treeview($attributes, $tag=null, $folder_id=0) {
		global $GO_CONFIG, $extension,$GO_SECURITY;

		$start_folder_id = isset ($attributes['folder_id']) ? $attributes['folder_id'] : $this->site['root_folder_id'];
		$template_name = isset ($attributes['template']) ? $attributes['template'] : '';
		$item_template_name = isset ($attributes['item_template']) ? $attributes['item_template'] : '';
		$active_item_template_name = isset ($attributes['active_item_template']) ? $attributes['active_item_template'] : $item_template_name;
		$class = isset ($attributes['class']) ? $attributes['class'] : '';
		$item_class = isset ($attributes['item_class']) ? $attributes['item_class'] : '';
		$active_item_class = isset ($attributes['item_active_class']) ? $attributes['item_active_class'] : $item_class;
		$menu = isset ($attributes['menu']) ? $attributes['menu'] : '';


		if($folder_id==0)
		{
			$return = '<div class="'.$class.'">';
		}else
		{
			$return = '<div>';
		}
		$template = $this->get_template_item_by_name($this->site['template_id'], $template_name);
		$item_template_passive = $this->get_template_item_by_name($this->site['template_id'], $item_template_name);
		$item_template_active = $this->get_template_item_by_name($this->site['template_id'], $active_item_template_name);

		if($folder_id==0)
		{
			$folder_id = $start_folder_id;
		}

		$folder = $this->get_folder($folder_id);

		if($folder['multipage']=='1')
		{
			$items = $this->get_authorized_folders($folder_id, $GO_SECURITY->user_id, true);
		}else {
			$items = $this->get_authorized_items($folder_id, $GO_SECURITY->user_id, true);
		}


		$count = count($items);

		while ($item = array_shift($items)) {
			if (isset ($item['extension'])) {
				$extension = $item['extension'];

				$item_template = ($this->file_id == $item['id']) ? $item_template_active : $item_template_passive;
				$_class = ($this->file_id == $item['id']) ? $active_item_class : $item_class;

				$name = strip_extension($item['name']);
				$title = $item['title'] == '' ? $name : $item['title'];
				if ($this->publish_static || eregi('htm', $extension)) {
					$content = '<a title="'.$title.'" class="'.$_class.'" href="'.$this->create_url($item['folder_id'], $item['id']).'">'.$name.'</a>';
				} else {
					$content = '<a title="'.$title.'" target="_blank" class="'.$_class.'" href="'.$this->cms_module['url'].'download.php?site_id='.$this->site['id'].'&amp;file_id='.$item['id'].'">'.$name.'</a>';
				}
				if ($item_template) {
					$item_template_content = $this->parse_template($item_template['content']);
					$return .= str_replace($this->get_tag('content', $item_template_content), $content, $item_template_content);
				} else {
					$return .= $content;
				}
			} else {
				$extension = '';
				$item_template = ($this->folder_id == $item['id']) ? $item_template_active : $item_template_passive;
				$_class = ($this->folder_id == $item['id']) ? $active_item_class : $item_class;

				$link = '<a class="'.$_class.'" href="'.$this->create_url($item['id']).'" title="'.$item['name'].'">'.$item['name'].'</a>';

				if ($item_template) {
					$item_template_content = $this->parse_template($item_template['content']);
					$return .= str_replace($this->get_tag('content', $item_template_content), $link, $item_template_content);
				} else {
					$return .= $link;
				}

				if($this->is_in_path($item['id'],$this->folder_id))
				{
					$return .= $this->print_treeview($attributes, $tag, $item['id']);
				}

			}
		}

		if ($template) {
			$template_content = $this->parse_template($template['content']);
			$return = str_replace($this->get_tag('content', $template_content), $return, $template_content);
		}

		$return .= '</div>';
		return $return;
	}


	function print_login($attributes) {
		global $GO_CONFIG, $cms_login, $GO_SECURITY;

		if(!$GO_SECURITY->logged_in())
		{
			$template_name = isset ($attributes['template']) ? $attributes['template'] : '';
			$class = isset ($attributes['class']) ? $attributes['class'] : '';
			$text = isset ($attributes['text']) ? $attributes['text'] : $cms_login;
			$goto_url = isset ($attributes['goto_url']) ? $attributes['goto_url'] : '';

			$template = $this->get_template_item_by_name($this->site['template_id'], $template_name);
			$login = '<a class="'.$class.'" href="'.$_SERVER['PHP_SELF'].'?task=login&amp;goto_url='.urlencode($goto_url).'">'.$text.'</a>';
			if ($template) {
				$template_content = $this->parse_template($template['content']);
				$login = str_replace($this->get_tag('content', $template_content), $login, $template_content);
			}
			return $login;
		}
	}

	function print_logout($attributes) {
		global $GO_CONFIG, $cms_logout, $GO_SECURITY;

		if($GO_SECURITY->logged_in())
		{
			$template_name = isset ($attributes['template']) ? $attributes['template'] : '';
			$class = isset ($attributes['class']) ? $attributes['class'] : '';
			$text = isset ($attributes['text']) ? $attributes['text'] : $cms_logout;
			$main_template = isset ($attributes['main_template']) ? $attributes['main_template'] : '';
			$goto_url = isset ($attributes['goto_url']) ? $attributes['goto_url'] : '';


			$template = $this->get_template_item_by_name($this->site['template_id'], $template_name);
			$logout = '<a class="'.$class.'" href="'.$_SERVER['PHP_SELF'].'?task=logout&amp;goto_url='.urlencode($goto_url).'">'.$text.'</a>';
			if ($template) {
				$template_content = $this->parse_template($template['content']);
				$logout = str_replace($this->get_tag('content', $template_content), $logout, $template_content);
			}
			return $logout;
		}
	}

	function print_admin($attributes)
	{
		global $GO_SECURITY, $GO_CONFIG, $GO_MODULES;

		if($GO_SECURITY->logged_in())
		{
			if($GO_SECURITY->has_permission($GO_SECURITY->user_id, $this->site['acl_write']))
			{
				$template_name = isset ($attributes['template']) ? $attributes['template'] : '';
				$class = isset ($attributes['class']) ? $attributes['class'] : '';
				$text = isset ($attributes['text']) ? $attributes['text'] : 'Admin';

				$template = $this->get_template_item_by_name($this->site['template_id'], $template_name);
				$admin = '<a class="'.$class.'" href="javascript:popup(\''.$GO_CONFIG->host.'?return_to='.urlencode(urlencode($GO_MODULES->modules['cms']['url'].'edit.php?site_id='.$this->site['id'].'&file_id='.$this->file_id.'&folder_id='.$this->folder_id)).'\',\'\',\'\');">'.$text.'</a>';
				if ($template) {
					$template_content = $this->parse_template($template['content']);
					$admin = str_replace($this->get_tag('content', $template_content), $admin, $template_content);
				}
				return $admin;
			}
		}
	}


	/*function print_plugin($attributes)
	{
	global $GO_CONFIG;

	$name = isset ($attributes['name']) ? $attributes['name'] : '';
	$template_name = isset ($attributes['template']) ? $attributes['template'] : '';
	$class = isset ($attributes['class']) ? $attributes['class'] : '';
	$text = isset ($attributes['text']) ? $attributes['text'] : 'Noname';
	$plugin_template = isset ($attributes['plugin_template']) ? $attributes['plugin_template'] : '';
	$plugin_class = isset ($attributes['plugin_class']) ? $attributes['plugin_class'] : '';

	$url = add_params_to_url($_SERVER['PHP_SELF'],'plugin='.$name.'&amp;plugin_class='.$plugin_class.'&amp;plugin_template='.$plugin_template.'&amp;plugin_text='.urlencode($text));
	if(isset($attributes['params']))
	{
	$url = add_params_to_url($url, $attributes['params']);
	}

	$template = $this->get_template_item_by_name($this->site['template_id'], $template_name);
	$login = '<a class="'.$class.'" href="'.$url.'">'.$text.'</a>';
	if ($template) {
	$template_content = $this->parse_template($template['content']);
	$login = str_replace($this->get_tag('content', $template_content), $login, $template_content);
	}
	return $login;
	}*/



	function print_document($attributes) {
		global $GO_CONFIG;

		$path = isset ($attributes['path']) ? $attributes['path'] : '';

		if ($resolved_path = $this->resolve_url($path, $this->site['id'])) {
			if ($resolved_path['file_id'] > 0) {
				$file = $this->get_file($resolved_path['file_id']);
				$content = $this->parse_template($file['content']);
				$content = str_replace($this->cms_module['full_url'].'view.php', $_SERVER['PHP_SELF'], $content);
				return $content;
			} else {
				return "<p><b>Error: Could not resolve file from '$path'</b></p>";
			}

		} else {
			return "<p><b>Error: Could not resolve url to '$path'</b></p>";
		}
	}

	function print_filename($attributes)
	{
		if($file = $this->get_file($this->file_id))
		{
			return strip_extension($file['name']);
		}
		return '';
	}

	function get_tag($tag, $content) {
		$start_pos = strpos($content, '<'.$tag);
		if ($start_pos !== false) {
			$end_pos = strpos($content, '</'.$tag.'>', $start_pos);
			if($end_pos === false)
			{
				$end_pos = strpos($content, '/>', $start_pos);
				if($end_pos === false)
				{
					return false;
				}else
				{
					$end_pos+=2;
				}
			}else
			{
				$end_pos+=strlen('</'.$tag.'>');
			}
			$tag_length = $end_pos-$start_pos;
			return substr($content, $start_pos, $tag_length);
		}
		return false;
	}

	function print_plugins($content)
	{
		global $GO_MODULES;

		while($tag = $this->get_tag('cms:plugin', $content))
		{
			$attributes = $this->get_attributes($tag);

			if(!empty($attributes['plugin_id']))
			{
				require_once($GO_MODULES->modules['cms']['path'].'plugins/'.$attributes['plugin_id'].'.class.inc');
				$plugin_class='cms_'.$attributes['plugin_id'];
				$plugin = new $plugin_class($attributes, &$this);
				$content = str_replace($tag, $plugin->get_html(), $content);
			}else {
				$error = 'Invalid plugin tag!<br />'.
				nl2br(htmlspecialchars(var_export($tag,true)).
				htmlspecialchars(var_export($attributes,true)));

				$content = str_replace($tag, $error, $content);
			}
		}

		return $content;
	}

	function print_php($attributes, $tag)
	{
		global $GO_CONFIG;

		$tag = preg_replace("'<php[^>]*>'si",'', $tag);
		$tag = preg_replace("'</php>'si",'', $tag);

		ob_start();
		eval($tag);
		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}


	function get_attributes($tag) {
		$attributes = array ();
		$in_value = false;
		$in_name = false;
		$name = '';
		$value = '';
		$length = strlen($tag);
		for ($i = 0; $i < $length; $i ++) {
			$char = $tag[$i];
			switch ($char) {
				case '"' :
					if ($in_value) {
						$in_value = false;

						$attributes[trim($name)] = trim($value);
						$name = '';
						$value = '';
					} else {
						$in_value = true;
					}

					break;

				case ' ' :
					if (!$in_value) {
						$in_name = true;
					} else {
						$value .= $char;
					}
					break;

				case '=' :
					$in_name = false;
					if ($in_value) {
						$value .= $char;
					}
					break;

				default :
					if ($in_name) {
						$name .= $char;
					}

					if ($in_value) {
						$value .= $char;
					}
					break;
			}
		}
		return $attributes;
	}

	function parse_template($content) {
		for ($i = 0; $i < count($this->tags); $i ++) {
			while ($tag = $this->get_tag($this->tags[$i], $content)) {
				$function = 'print_'.$this->tags[$i];

				$attributes = $this->get_attributes($tag);
				$content = str_replace($tag, $this-> $function ($attributes, $tag), $content);
			}
		}
		return $content;
	}
}
