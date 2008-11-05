<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.83 $ $Date: 2006/11/28 11:39:19 $

 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */

if (isset($_REQUEST['handler_base64_encoded']))
{
	$_REQUEST['GO_HANDLER'] = base64_decode($_REQUEST['GO_HANDLER']);
}
require_once("../../Group-Office.php");
$GO_SECURITY->authenticate();

load_basic_controls();
load_control('datatable');


require_once($GO_LANGUAGE->get_language_file('addressbook'));

$GO_FIELD = isset($_REQUEST['GO_FIELD']) ? smart_stripslashes($_REQUEST['GO_FIELD']) : '';
$GO_HANDLER = isset($_REQUEST['GO_HANDLER']) ? smart_stripslashes($_REQUEST['GO_HANDLER']) : '';

$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';
$mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : '';

$pass_value = isset($_REQUEST['pass_value']) ? $_REQUEST['pass_value'] : 'email';
$multiselect = isset($_REQUEST['multiselect']) ? $_REQUEST['multiselect'] :  'false';
$require_email_address = isset($_REQUEST['require_email_address']) ? $_REQUEST['require_email_address'] : 'false';

$tabstrip = new tabstrip('search_type',$cmdSearch);	
$tabstrip->set_attribute('style','width:100%');


if(isset($_REQUEST['show_contacts']) &&  $_REQUEST['show_contacts'] == 'true' 
	&& isset($GO_MODULES->modules['addressbook']) 
	&& $GO_MODULES->modules['addressbook']['read_permission'])
{
	$tabstrip->add_tab('contact',$contacts_contacts);
	$types_used[] = 'contact';
	$show_contacts='true';
}else
{
	$show_contacts='false';
}
if(isset($_REQUEST['show_companies']) &&  $_REQUEST['show_companies'] == 'true'
	&& isset($GO_MODULES->modules['addressbook']) 
	&& $GO_MODULES->modules['addressbook']['read_permission'])
{
	$types_used[] = 'company';
	$show_companies='true';
	$tabstrip->add_tab('company',$ab_companies);
}else
{
	$show_companies='false';
}
if(isset($_REQUEST['show_users']) &&  $_REQUEST['show_users'] == 'true')
{
	$tabstrip->add_tab('user',$contacts_members);
	$types_used[] = 'user';
	$show_users='true';
}else
{
	$show_users='false';
}
if(isset($_REQUEST['show_projects']) &&  $_REQUEST['show_projects'] == 'true'
	&& isset($GO_MODULES->modules['projects']) 
	&& $GO_MODULES->modules['projects']['read_permission'])
{
	$types_used[] = 'project';
	$show_projects='true';
	$tabstrip->add_tab('project', $strProjects);
}else
{
	$show_projects='false';
}
if(isset($_REQUEST['show_files']) &&  $_REQUEST['show_files'] == 'true'
		&& isset($GO_MODULES->modules['filesystem']) 
		&& $GO_MODULES->modules['filesystem']['read_permission'])
{
	$tabstrip->add_tab('file', $strFiles);
	$types_used[] = 'file';
	$show_files='true';
}else
{
	$show_files='false';
}
$show_mailings = isset($_REQUEST['show_mailings']) && $_REQUEST['show_mailings']=='true' ? true : false;


$search_type = isset($_REQUEST['search_type']) ? $_REQUEST['search_type'] : 'contact';

if (isset($GO_MODULES->modules['addressbook']) && $GO_MODULES->modules['addressbook']['read_permission'])
{
	$GO_MODULES->authenticate('addressbook');
	
	require_once($GO_MODULES->modules['addressbook']['class_path'].'addressbook.class.inc');
	$ab = new addressbook();
	
	
	$ab_settings = $ab->get_settings($GO_SECURITY->user_id);
	if($search_type == '')
	{
		$search_type = $ab_settings['search_type'];
		$tabstrip->set_active_tab($ab_settings['search_type']);
	}else
	{
		 $ab_settings['search_type'] = $search_type;
	}
	
	$addressbook_id = isset($_REQUEST['addressbook_id']) ? $_REQUEST['addressbook_id'] : $ab_settings['addressbook_id'];
	if($addressbook_id == 0)
	{
		$addressbook = $ab->get_addressbook();
		$addressbook_id = $addressbook['id'];
	}
}
$GO_HEADER['nomessages']=true;
$GO_HEADER['head'] = datatable::get_header();
$GO_HEADER['body_arguments'] = 'onkeypress="javascript:executeOnEnter(event, \'search();\');"';
$GO_HEADER['body_arguments'] .= 'onload="javascript:document.select_form.query.focus();"';

$page_title = $contacts_select;
require_once($GO_THEME->theme_path."header.inc");


$form = new form('select_form');
$form->add_html_element(new input('hidden','multiselect', $multiselect));
$form->add_html_element(new input('hidden','require_email_address', $require_email_address));
$form->add_html_element(new input('hidden','show_users', $show_users));
$form->add_html_element(new input('hidden','show_contacts', $show_contacts));
$form->add_html_element(new input('hidden','show_companies', $show_companies));
$form->add_html_element(new input('hidden','show_projects', $show_projects));
$form->add_html_element(new input('hidden','show_files', $show_files));
$form->add_html_element(new input('hidden','pass_value', $pass_value));
$form->add_html_element(new input('hidden','task', $task, false));
$form->add_html_element(new input('hidden','mode', $mode, false));
$form->add_html_element(new input('hidden','GO_FIELD', $GO_FIELD));
$form->add_html_element(new input('hidden','GO_HANDLER', $GO_HANDLER));
$form->add_html_element(new input('hidden','clicked_letter'));
$form->add_html_element(new input('hidden','select_single'));


if ($show_contacts == 'true' || $show_companies == 'true')
{
	$menu = new button_menu();
	if($show_contacts)
	{
		$menu->add_button('add_contact', $ab_new_contact, "javascript:popup('".$GO_MODULES->modules['addressbook']['url'].
			"contact.php?addressbook_id=".$addressbook_id.
			"&popup=true','770','500');");	
	}
			
	if($show_companies)
	{
		$menu->add_button('ab_add_company', $ab_new_company, "javascript:popup('".
			$GO_MODULES->modules['addressbook']['url']."company.php?addressbook_id=".
			$addressbook_id."&popup=true','770','500');");
	}
	$tp_plugin=$GO_MODULES->get_plugin('templates', 'addressbook');

	
	
	if($show_mailings)
	{
		if( isset($GO_MODULES->modules['reports']) && $GO_MODULES->modules['reports']['read_permission'])
		{
			$GO_THEME->load_module_theme('reports');
			require($GO_LANGUAGE->get_language_file('reports'));
			$menu->add_button('reports', $lang_modules['reports'], "javascript:change_mode('reports');");
		}
		
		if($tp_plugin)
		{
			$menu->add_button('mailings', $ab_mailings, "javascript:change_mode('mailings');");
		}
	}
	
	
	$form->add_html_element($menu);
}

require_once($GO_CONFIG->class_path.'mail/RFC822.class.inc');
$RFC822 = new RFC822();

$addresses=array();
if (isset($_REQUEST['address_string']))
{
	$addresses = explode(',',smart_stripslashes($_REQUEST['address_string']));

}

$datatable = new datatable('select_table');
$datatable->set_multiselect(($multiselect == 'true'));

switch($mode)
{
	case 'mailings':
		if(isset($_POST['addresses']))
		{
			$addresses = array_map('smart_stripslashes', $_POST['addresses']);
		}elseif(isset($_POST['select_table']['selected']))
		{
			$addresses = array_map('smart_stripslashes', $_POST['select_table']['selected']);
		}else
		{
			$addresses = array();
		}
		foreach($addresses as $address)
		{
			$form->add_html_element(new input('hidden', 'addresses[]', $address));
		}
		$datatable->add_column(new table_heading($strName));
		require($tp_plugin['class_path'].'templates.class.inc');
		$tp = new templates();
		
		$count = $tp->get_mailing_groups($GO_SECURITY->user_id);
		
		while($tp->next_record())
		{
			$row = new table_row($tp->f('id'));
			$row->set_attribute('ondblclick', "javascript:_select_mailing();");
			
			$row->add_cell(new table_cell($tp->f('name')));			
			$datatable->add_row($row);
		}
	break;
	
	case 'reports':
		if(isset($_POST['addresses']))
		{
			$addresses = array_map('smart_stripslashes', $_POST['addresses']);
		}elseif(isset($_POST['select_table']['selected']))
		{
			$addresses = array_map('smart_stripslashes', $_POST['select_table']['selected']);
		}else
		{
			$addresses = array();
		}
		foreach($addresses as $address)
		{
			$form->add_html_element(new input('hidden', 'addresses[]', $address));
		}
		$datatable->add_column(new table_heading($strName));
		require($GO_MODULES->modules['reports']['class_path'].'reports.class.inc');
		$reports = new reports();
		
		$count = $reports->get_authorized_reports($GO_SECURITY->user_id);
		
		while($reports->next_record())
		{
			$row = new table_row($reports->f('id'));
			$row->set_attribute('ondblclick', "javascript:_select_report();");
			
			$row->add_cell(new table_cell($reports->f('name')));			
			$datatable->add_row($row);
		}
	break;
	
	default:

		$datatable->set_remind_selected(true);

		if ($pass_value == 'email')
		{
			$datatable->selected = array_merge($addresses, $datatable->selected);
		}

		$count = 0;
		
		$table = new table();
		$row = new table_row();			

		$h2 = new html_element('h1');
		$alphabet = explode(',',$alphabet);
		for($i=0;$i<count($alphabet);$i++)
		{
			$hyperlink = new hyperlink("javascript:letter_click('".$alphabet[$i]."')",$alphabet[$i]);
			$hyperlink->set_attribute('style', 'margin-right:10px;');
			$h2->add_html_element($hyperlink);
		}

		$tabstrip->add_html_element($h2);
		
		$table = new table();
		$row = new table_row();

		$search_fields = new select('search_field');		

		switch ($search_type)
		{
			case 'company':
				$search_fields->add_value('', $strSearchAll);
				$search_fields->add_value('ab_companies.id', $ab_id);
				$search_fields->add_value('ab_companies.name', $strName);
				$search_fields->add_value('ab_companies.email', $strEmail);
				$search_fields->add_value('ab_companies.address',$strAddress);
				$search_fields->add_value('ab_companies.city', $strCity);
				$search_fields->add_value('ab_companies.zip',$strZip);
				$search_fields->add_value('ab_companies.state',$strState);
				$search_fields->add_value('ab_companies.country', $strCountry);


				$search_field = $ab_settings['search_companies_field'] ;
				if(isset($_REQUEST['search_field']))
				{
					if($search_fields->is_in_select($_REQUEST['search_field']))
					{
						$search_field = $_REQUEST['search_field'];
						if($task != 'show_letter')
						{
							$ab_settings['search_companies_field'] = $_REQUEST['search_field'];
						}
					}
				}		  
				$search_fields->value=$ab_settings['search_companies_field'];
			break;

			case 'user':
				$search_fields->add_value('', $strSearchAll);	
						
				$search_fields->add_value('users.first_name', $strFirstName);
				$search_fields->add_value('users.last_name', $strLastName);
				$search_fields->add_value('users.email', $strEmail);
				$search_fields->add_value('users.department',$strDepartment);
				$search_fields->add_value('users.function',$strFunction);
				$search_fields->add_value('users.address',$strAddress);
				$search_fields->add_value('users.city', $strCity);
				$search_fields->add_value('users.zip',$strZip);
				$search_fields->add_value('users.state',$strState);
				$search_fields->add_value('users.country', $strCountry);
				$search_fields->add_value('users.company', $strCompany);
				
				$search_field = isset($ab_settings['search_users_field']) ? $ab_settings['search_users_field'] : '';
				if(isset($_REQUEST['search_field']))
				{
					if($search_fields->is_in_select($_REQUEST['search_field']))
					{
						$search_field = $_REQUEST['search_field'];
						$ab_settings['search_users_field'] = $_REQUEST['search_field'];
					}
				}		  
				$search_fields->value=$ab_settings['search_users_field'];
			break;
			
			case 'project':
				$search_field = $search_fields->value = 'pmProjects.name';
				$search_fields->add_value('pmProjects.name', $strName);
			break;
			
			case 'file':			
				$home_path = $GO_CONFIG->file_storage_path.'users/'.$_SESSION['GO_SESSION']['username'];
				
				
				$search_field = $search_fields->value = 
					(isset($_POST['search_field']) && is_dir(smart_stripslashes($_POST['search_field']))) ? 
					smart_stripslashes($_POST['search_field']) : $home_path;

				$search_fields->add_value($home_path, 'home');
				
				require_once($GO_LANGUAGE->get_language_file('filesystem'));
				require_once($GO_CONFIG->class_path.'filesystem.class.inc');
				$fs = new filesystem();
				$fs2 = new filesystem();
			  $fs2->get_my_shares($GO_SECURITY->user_id);
				
			  while ($fs2->next_record())
			  {
			    $shares = array();
			    $share_count = $fs->get_shares($fs2->f('user_id'));
			    while ($fs->next_record())
			    {
			      if ($GO_SECURITY->has_permission($GO_SECURITY->user_id, $fs->f('acl_read')) || 
								$GO_SECURITY->has_permission($GO_SECURITY->user_id, $fs->f('acl_write')))
			      {
							$shares[] = $fs->f('path');
			      }
			    }
			    $share_count = count($shares) ;
			    if ($share_count > 0)
			    {
			      if ($user = $GO_USERS->get_user($fs2->f('user_id')))
			      {
							$search_fields->add_optgroup($user['username']);
							for ($i=0;$i<$share_count;$i++)
							{
							  $search_fields->add_value($shares[$i], utf8_basename($shares[$i]));
							}
			      }
			    }
			  }		
			break;
			
			default:
				//$search_fields->value = $ab_settings['search_contacts_field'];
				$search_fields->add_value('', $strSearchAll);
				$search_fields->add_value('ab_contacts.id', $ab_id);
				$search_fields->add_value('ab_contacts.first_name', $strFirstName);
				$search_fields->add_value('ab_contacts.last_name', $strLastName);
				$search_fields->add_value('ab_contacts.email', $strEmail);
				$search_fields->add_value('ab_contacts.department',$strDepartment);
				$search_fields->add_value('ab_contacts.function',$strFunction);
				$search_fields->add_value('ab_contacts.address',$strAddress);
				$search_fields->add_value('ab_contacts.city', $strCity);
				$search_fields->add_value('ab_contacts.zip',$strZip);
				$search_fields->add_value('ab_contacts.state',$strState);
				$search_fields->add_value('ab_contacts.country', $strCountry);
				$search_fields->add_value('ab_contacts.comment', $ab_comment);
			
				$search_field = $ab_settings['search_contacts_field'] ;
				if(isset($_REQUEST['search_field']))
				{
					if($search_fields->is_in_select($_REQUEST['search_field']))
					{
						$search_field = $_REQUEST['search_field'];
						$ab_settings['search_contacts_field'] = $_REQUEST['search_field'];
					}
				}
				$search_fields->value=$ab_settings['search_contacts_field'];
			break;
		}
		if(isset($_REQUEST['search_addressbook_id']))
		{
			$ab_settings['search_addressbook_id'] = $_REQUEST['search_addressbook_id'];
		}
		$form->add_html_element(new input('hidden', 'search_addressbook_id', $ab_settings['search_addressbook_id'], false));
		
		if($search_type != 'project' && $search_type!='file' && isset($ab))
		{
			$ab->update_settings($ab_settings);
		}

		if (($search_type == 'contact' || $search_type == 'company'))
		{
			if($ab_settings['search_addressbook_id'] == 0)
			{
				$link_text = $ab_all_your_addressbooks;
			}else
			{
				$addressbook = $ab->get_addressbook($ab_settings['search_addressbook_id']);
				$link_text = $addressbook['name'];
			}
			$link = new hyperlink("javascript:popup('".$GO_MODULES->modules['addressbook']['url']."select_addressbook.php?callback=select_search_addressbook&add_null=true', '300','400');",$link_text);
			$link->set_attribute('style','margin:3px;');
			$link->set_attribute('class','normal');
			$row->add_cell(new table_cell($ab_search_in.':'));
			$row->add_cell(new table_cell($link->get_html().' '.$ab_search_on.': '.$search_fields->get_html()));
		}elseif(isset($ab))
		{
			$search_addressbook_id = $ab_settings['search_addressbook_id'];
			
			
			$row->add_cell(new table_cell($ab_search_on_users.':'));
			$row->add_cell(new table_cell($search_fields->get_html()));
		}
		
		$table->add_row($row);
		$row = new table_row();
		
		$row->add_cell(new table_cell($strKeyword.':'));
		$query = (isset($_POST['query']) && $task != 'show_letter') ? smart_stripslashes($_POST['query']) : '';
		$input = new input('text', 'query', $query, false);
		$input->set_attribute('style', 'width:300px;');
		$cell = new table_cell($input->get_html());
		$cell->add_html_element(new button($cmdSearch, 'javascript:search();'));		
		$row->add_cell($cell);
		
		$table->add_row($row);
		
		$tabstrip->add_html_element($table);
		
		$form->add_html_element($tabstrip);

	if ($task == 'search' || $task == 'show_letter')
	{
		if ($task != 'show_letter')
		{
			$query = '%'.smart_addslashes($_POST['query']).'%';
		}else
		{
			switch ($ab_settings['search_type'])
			{
				case 'company':
					$search_field='ab_companies.name';
				break;
				
				case 'contact':
					$search_field='ab_contacts.'.$_SESSION['GO_SESSION']['sort_name'];
				break;
				
				case 'user':
					$search_field='users.'.$_SESSION['GO_SESSION']['sort_name'];
				break;
			}

			$query = smart_addslashes($_POST['clicked_letter']).'%';
		}

		if ($search_type == 'contact' || $search_type == 'user')
		{
			if ($search_type == 'user')
			{
				$ab = new $go_users_class();
				$count = $ab->search($query, $search_field, $GO_SECURITY->user_id, $datatable->start, $datatable->offset);
			}else
			{
				$count = $ab->search_contacts(
					$GO_SECURITY->user_id, 
					$query, 
					$search_field, 
					$ab_settings['search_addressbook_id'], 
					0, 
					$datatable->start, 
					$datatable->offset, 
					($require_email_address=='true'));
			}
			$datatable->set_pagination($count);

			while ($ab->next_record())
			{			
				$mail_name = format_name($ab->f('last_name'), $ab->f('first_name'), $ab->f('middle_name'), 'first_name');
				$full_email = $RFC822->write_address($mail_name, $ab->f('email'));				
				$name = format_name($ab->f('last_name'), $ab->f('first_name'), $ab->f('middle_name'));


				
				$value = $pass_value == 'email' ?  $full_email : $ab->f($pass_value);
				
				$row = new table_row($ab->f('id'), $value);		
				$row->set_attribute('ondblclick', "javascript:_select();");
				
				$cell = new table_cell($name);
				if ($search_type != 'user' && $ab->f('color') != '')
				{
					$cell->set_attribute('style','color: '.$ab->f('color'));
				}
				
				$row->add_cell($cell);
				
				$row->add_cell(new table_cell($ab->f('email')));
				$datatable->add_row($row);				
			}
			
			
			$result_str =  ($count == 1) ? $count.' '.$contacts_result : $count.' '.$contacts_results;
			$h2 = new html_element('h2', $result_str);
			$form->add_html_element($h2);

			if ($count > 0)
			{
				$datatable->add_column(new table_heading($strName));
				$datatable->add_column(new table_heading($strEmail));
				
				
			}
		}elseif($search_type=='company')
		{
			$count = $ab->search_companies(
				$GO_SECURITY->user_id, 
				$query, 
				$search_field, 
				$ab_settings['search_addressbook_id'], 
				$datatable->start, 
				$datatable->offset, 
				($require_email_address=='true'));

			while ($ab->next_record())
			{				
				$full_email = $RFC822->write_address($ab->f('name'), $ab->f('email'));	
				$value = $pass_value == 'email' ?  $full_email : $ab->f($pass_value);
				
				$row = new table_row($ab->f('id'),$value);
				$row->set_attribute('ondblclick', "javascript:_select();");
				$row->add_cell(new table_cell($ab->f('name')));
				$row->add_cell(new table_cell(empty_to_stripe($ab->f('email'))));
				$datatable->add_row($row);
			}			
			$result_str =  ($count == 1) ? $count.' '.$contacts_result : $count.' '.$contacts_results;
			$h2 = new html_element('h2', $result_str);
			$form->add_html_element($h2);
			if ($count > 0)
			{
				$datatable->set_pagination($count);
				$datatable->add_column(new table_heading($strName));
				$datatable->add_column(new table_heading($strEmail));
				

			}
		}elseif($search_type == 'project')
		{
			require_once($GO_MODULES->modules['projects']['class_path'].'projects.class.inc');
			$projects = new projects();
			
			$count = $projects->get_authorized_projects($GO_SECURITY->user_id, false, 
								$search_field, 'ASC', $datatable->start, $datatable->offset, '', $search_field,$query);
			
			$result_str =  ($count == 1) ? $count.' '.$contacts_result : $count.' '.$contacts_results;
			$h2 = new html_element('h2', $result_str);
			$form->add_html_element($h2);
			$datatable->set_pagination($count);
			if($count > 0)
			{				
				$datatable->add_column(new table_heading($strName));
				$datatable->add_column(new table_heading($strOwner));

				while($projects->next_record())
				{
					$row = new table_row($projects->f('id'));
					$row->set_attribute('ondblclick', "javascript:_select();");				
					
					$project_name = $projects->f('description') == '' ? $projects->f('name') : $projects->f('name').' ('.$projects->f('description').')';
					$row->add_cell(new table_cell($project_name));
					$row->add_cell(new table_cell(show_profile($projects->f('user_id'))));			
					
					$datatable->add_row($row);							
				}


			}
		}elseif($search_type == 'file')
		{
			if ($_POST['query'] == '') 
			{
				$_POST['query'] = '.';
			}
						
			$results = $fs->search($search_field, smart_stripslashes($_POST['query']));
			$count = count($results);
			
			$datatable->add_column(new table_heading($strName));
			$datatable->add_column(new table_heading($fbLocation));

			while($result = array_shift($results))
			{
				$row = new table_row($result['path']);
				
				$row->set_attribute('ondblclick', "javascript:_select();");			
					
				if (!is_dir($result['path']))
				{
					$extension = get_extension($result['name']);					
					$img = new image('', get_filetype_image($extension));
					
				}else
				{
					$img = new image('folder');
				}
				$img->set_attribute('align', 'absmiddle');
				$img->set_attribute('style', 'border:0px;width:16px;height:16px;margin-right:5px;');
				
				$row->add_cell(new table_cell($img->get_html().$result['name']));
				
				$location = dirname($result['path']);
				
				$row->add_cell(new table_cell(str_replace($GO_CONFIG->file_storage_path,$GO_CONFIG->slash,$location)));
				$datatable->add_row($row);
			}
			$cell = new table_cell($count.' '.$fbItems);
			$cell->set_attribute('colspan','99');
			$cell->set_attribute('class','small');
			
			$datatable->add_footer($cell);
		}		
	}
}

$form->add_html_element($datatable);				

$div = new html_element('div');
$div->set_attribute('style','text-align:center');

if($multiselect == 'true' && $count > 0)
{
	if($mode=='reports')
	{
		$div->add_html_element(new button($cmdAdd,'javascript:_select_report();'));
	}elseif($mode=='mailings')
	{
		$div->add_html_element(new button($cmdAdd,'javascript:_select_mailing();'));
	}else
	{
		$div->add_html_element(new button($cmdAdd,'javascript:_select();'));
	}
}
$div->add_html_element(new button($cmdCancel,'javascript:window.close();'));

$form->add_html_element($div);


echo $form->get_html();
?>
<script type="text/javascript" language="javascript">

function search()
{
	<?php echo $datatable->set_page_one(); ?>
	document.select_form.task.value = 'search';
	document.select_form.submit();
}

function expand_group(group_id)
{
	document.select_form.expand_id.value = group_id;
	document.select_form.task.value = "expand";
	document.select_form.submit();
}

function select_group(group_id, check)
{
	var add = false;

	for (var i = 0; i < document.select_form.elements.length; i++)
	{
		if (document.select_form.elements[i].name == 'group_start_'+group_id)
		{
			add = true;
		}

		if (document.select_form.elements[i].name == 'group_end_'+group_id)
		{
			add = false;
		}

		if(document.select_form.elements[i].type == 'checkbox' && document.select_form.elements[i].name != 'dummy' && add==true)
		{
			document.select_form.elements[i].checked = check;
			document.select_form.elements[i].onclick();
		}
	}
}

function change_mode(mode)
{
	document.select_form.mode.value=mode;
	document.select_form.submit();
}

function letter_click(letter)
{
	<?php echo $datatable->set_page_one(); 	?>
	document.select_form.task.value='show_letter';
	document.select_form.clicked_letter.value=letter;
	document.select_form.submit();
}

function _select()
{
	document.select_form.action = "<?php echo $GO_HANDLER; ?>";
	document.select_form.submit();
}

<?php if(isset($tp_plugin) && $tp_plugin){?>
function _select_mailing()
{
	document.select_form.action = "<?php echo $tp_plugin['url'].'add_mailing_group.php'; ?>";
	document.select_form.submit();
}
<?php } ?>

<?php if(isset($GO_MODULES->modules['reports']) && $GO_MODULES->modules['reports']['read_permission']){?>
function _select_report()
{
	document.select_form.action = "<?php echo $GO_MODULES->modules['reports']['url'].'select_report_for_email.php'; ?>";
	document.select_form.submit();
}
<?php } ?>
function select_search_addressbook(addressbook_id)
{
	document.forms[0].search_addressbook_id.value=addressbook_id;
	document.forms[0].submit();
}
</script>
<?php
require_once($GO_THEME->theme_path."footer.inc");
