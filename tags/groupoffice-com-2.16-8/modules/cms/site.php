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


//load Group-Office
require_once("../../Group-Office.php");

load_basic_controls();

//authenticate the user
$GO_SECURITY->authenticate();

//see if the user has access to this module
//for this to work there must be a module named 'example'
$GO_MODULES->authenticate('cms');

//load the CMS module class library
require_once($GO_MODULES->class_path.'cms.class.inc');
$cms = new cms();

//get the language file
require_once($GO_LANGUAGE->get_language_file('cms'));

$site_id = isset($_REQUEST['site_id']) ? $_REQUEST['site_id'] : 0;
$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';

$search_word_id = isset($_REQUEST['search_word_id']) ? $_REQUEST['search_word_id'] : 0;

$return_to = isset($_REQUEST['return_to']) ? $_REQUEST['return_to'] : 'index.php';
$sites = isset($_POST['sites']) ? '&sites='.$_POST['sites'] : '';
$link_back = $_SERVER['PHP_SELF'].'?site_id='.$site_id.$sites.'&return_to='.urlencode($return_to);

switch($task)
{
	case 'save_site':
	
		$domain = $cms->prepare_domain(smart_addslashes(trim($_POST['domain'])));
		$webmaster = isset($_POST['webmaster']) ? smart_addslashes($_POST['webmaster']) : '';
		if ($domain == '' || $webmaster == '')
		{
			$feedback= $error_missing_field;
		}else
		{
			if($site_id>0)
			{
				if(!$site = $cms->get_site($site_id))
				{
					$feedback = $strSaveError;
				}else
				{
					$existing_site = $cms->get_site_by_domain($domain);
					if ($existing_site && $existing_site['id'] != $_POST['site_id'])
					{
						$feedback = $cms_site_exists;
					}else
					{
						
						if (!$cms->update_site(
									$site_id,
									$domain,
									$webmaster,
									$_POST['template_id'],
									$_POST['start_file_id']))
						{
							$feedback = $strSaveError;
						}else
						{
							if($_POST['close'] == 'true')
							{
								header('Location: '.$return_to);
								exit();
							}
						}
					}
				}
				
			}else
			{
				if (!$cms->get_site_by_domain($domain))
				{
					if (!$acl_write = $GO_SECURITY->get_new_acl('cms write: '.$domain))
					{
						$GO_SECURITY->delete_acl($acl_read);
						die($strAclError);
					}

					if (!$GO_SECURITY->add_user_to_acl($GO_SECURITY->user_id, $acl_write))
					{
						$GO_SECURITY->delete_acl($acl_read);
						$GO_SECURITY->delete_acl($acl_write);
						die($strAclError);
					}

					if($site_id = $cms->add_site(
								$GO_SECURITY->user_id,
								$domain,
								$webmaster,
								$acl_write,
								$_POST['template_id']))
					{
						if($_POST['close'] == 'true')
						{
							header('Location: '.$return_to);
							exit();
						}
					}else
					{
						$GO_SECURITY->delete_acl($acl_read);
						$GO_SECURITY->delete_acl($acl_write);
						$feedback = $strSaveError;

					}
				}else
				{
					$feedback = $cms_site_exists;
				}
			}
		}
	break;
}

if($site_id>0)
{
	$site = $cms->get_site($site_id);
	if (!$GO_SECURITY->has_permission($GO_SECURITY->user_id, $site['acl_write']))
	{
		header('Location: '.$GO_CONFIG->host.'error_docs/403.php');
		exit();
	}

	$tabstrip = new tabstrip('sites', $site['domain']);
	

	if($site['user_id'] == $GO_SECURITY->user_id || $site['allow_properties'] == '1')
	{
		$tabstrip->add_tab('properties', $strProperties);
	}

	if($site['user_id'] == $GO_SECURITY->user_id)
	{
		$tabstrip->add_tab('write_permissions', $strWriteRights);
	}
}else
{
	$tabstrip = new tabstrip('sites', $cms_new_site);
}
$tabstrip->set_attribute('style', 'width:100%');
$tabstrip->set_return_to(htmlspecialchars($return_to));

if($site_id>0 && $task != 'save_site')
{
	$template_id = $site['template_id'];
	$domain = $site['domain'];
	$webmaster = $site['webmaster'];
	$start_file_id  = $site['start_file_id'];
}else
{
	$template_id = isset($_POST['template_id']) ? $_POST['template_id'] : '0';
	$domain = isset($_POST['domain']) ? $cms->prepare_domain(smart_addslashes(trim($_POST['domain']))) : '';
	$webmaster = isset($_POST['webmaster']) ? smart_addslashes($_POST['webmaster']) : '';
	$start_file_id  = isset($_POST['start_file_id']) ? smart_addslashes($_POST['start_file_id']) : '0';
}


//require the header file. This will draw the logo's and the menu
require_once($GO_THEME->theme_path."header.inc");

$form = new form('site_form');
$form->add_html_element(new input('hidden', 'site_id', $site_id, false));
$form->add_html_element(new input('hidden', 'close', 'false', false));
$form->add_html_element(new input('hidden', 'return_to', $return_to, false));
$form->add_html_element(new input('hidden', 'task', '', false));


switch($tabstrip->get_active_tab_id())
{
	
	case 'write_permissions':
		$tabstrip->innerHTML .= get_acl($site["acl_write"]);
		$tabstrip->add_html_element(new button($cmdClose, "javascript:document.location='".htmlspecialchars($return_to)."';"));

		break;

	case 'read_permissions':
		$tabstrip->innerHTML .= get_acl($site["acl_read"]);
		$tabstrip->add_html_element(new button($cmdClose, "javascript:document.location='".htmlspecialchars($return_to)."';"));
		break;

	default:
		$table = new table();
		
		if (isset ($feedback))
		{
			$cell = new table_cell($feedback);
			$cell->set_attribute('class','Error');
			$cell->set_attribute('colspan','2');
			$row =new table_row();
			
			$table->add_row($row);
		}
		
		$row = new table_row();		
		$cell = new table_cell($cms_domain.':*');
		$row->add_cell($cell);		
		$input = new input('text', 'domain', $domain);
		$input->set_attribute('style', 'width:250px;');
		$cell = new table_cell($input->get_html());
		$row->add_cell($cell);
		$table->add_row($row);
		
		$row = new table_row();		
		$cell = new table_cell($cms_webmaster.':*');
		$row->add_cell($cell);		
		$input = new input('text', 'webmaster', $webmaster);
		$input->set_attribute('style', 'width:250px;');
		$cell = new table_cell($input->get_html());
		$row->add_cell($cell);
		$table->add_row($row);
		
		$row = new table_row();		
		$cell = new table_cell($cms_theme.':*');
		$row->add_cell($cell);		
		
		$select = new select('template_id', $template_id);	
		//$select->set_attribute('onchange','document.forms[0].submit();');	
		$cms->get_authorized_templates($GO_SECURITY->user_id);
		while($cms->next_record())
		{
			$select->add_value($cms->f('id'), $cms->f('name'));
		}

		
		$cell = new table_cell($select->get_html());
		$row->add_cell($cell);
		$table->add_row($row);
		
		if($site_id>0)
		{		
			$row = new table_row();		
			$cell = new table_cell($cms_start_page.':');
			$row->add_cell($cell);		
			
			$select = new select('start_file_id', $start_file_id);		
			
			$select->add_value('0', ' ');
			$path = '/';
			
			function buildTree($folder_id, $select, $path='/')
			{
				$cms = new cms();
				$cms->get_files($folder_id);
				
				while($cms->next_record())
				{
					$select->add_value($cms->f('id'), $path.$cms->f('name'));
				}
			
				$cms->get_folders($folder_id);
				while($cms->next_record())
				{
					$select = buildTree($cms->f('id'), $select, $path.$cms->f('name').'/');
				}
				return $select;
			}
			
			$select = buildTree($site['root_folder_id'], $select);
			
			$cell = new table_cell($select->get_html());
			$row->add_cell($cell);
			$table->add_row($row);
		}
		
		
		
		


		$row = new table_row();		
		
		
		$cell = new table_cell();
		$cell->set_attribute('colspan','2');
		
		$cell->add_html_element(new button($cmdOk, "javascript:save_close_site()"));
		$cell->add_html_element(new button($cmdApply, "javascript:save_site()"));
		$cell->add_html_element(new button($cmdClose, "javascript:document.location='".htmlspecialchars($return_to)."';"));
		$row->add_cell($cell);
		$table->add_row($row);

		$tabstrip->add_html_element($table);
		?>
		<script type="text/javascript">
		function save_close_site()
		{
			document.forms[0].close.value='true';
			document.forms[0].task.value='save_site';
			document.forms[0].submit();
		}

		function save_site()
		{
		document.forms[0].task.value='save_site';
		document.forms[0].submit();
		}
		document.forms[0].domain.focus();
		</script>
		<?php

		
		break;
}
$form->add_html_element($tabstrip);
echo $form->get_html();

require_once($GO_THEME->theme_path."footer.inc");
