<?php
/**
 * @copyright Intermesh 2005
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.35 $ $Date: 2006/11/22 09:35:38 $

 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */
 
//load Group-Office
require_once("../../Group-Office.php");


//authenticate the user
$GO_SECURITY->authenticate();

load_basic_controls();

//see if the user has access to this module
//for this to work there must be a module named 'example'
$GO_MODULES->authenticate('cms', true);

//load the CMS module class library
require_once($GO_MODULES->class_path.'cms.class.inc');
$cms = new cms();


//get the language file
require_once($GO_LANGUAGE->get_language_file('cms'));

$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';
$template_id = isset($_REQUEST['template_id']) ? $_REQUEST['template_id'] : 0;


$return_to = 'templates.php';
$link_back = $_SERVER['PHP_SELF'].'?template_id='.$template_id;

switch($task)
{
	case 'save_properties':
		$template['name'] = trim(smart_addslashes($_POST['name']));
		$template['doctype'] = trim(smart_addslashes($_POST['doctype']));
		$template['restrict_editor'] = isset($_POST['restrict_editor']) ? '1' : '0';
		
		if ($template['name'] == '')
		{
			$feedback= $error_missing_field;
		}else
		{
			if ($template_id > 0)
			{
				$template['id'] = $template_id;
				
				$existing_template = $cms->get_template_by_name($GO_SECURITY->user_id, $template['name']);
				if ($existing_template && $existing_template['id'] != $template_id)
				{
					$feedback = $fbNameExists;
				}else
				{
					if (!$cms->update_template($template))
					{
						$feedback = $strSaveError;
					}
				}
			}else
			{
				if ($cms->get_template_by_name($GO_SECURITY->user_id, $template['name']))
				{
					$feedback = $fbNameExists;
				}else
				{
					if (!$template['acl_read'] = $GO_SECURITY->get_new_acl())
					{
						die($strAclError);
					}

					if (!$template['acl_write'] = $GO_SECURITY->get_new_acl())
					{
						$GO_SECURITY->delete_acl($template['acl_read']);
						die($strAclError);
					}

					if (!$GO_SECURITY->add_user_to_acl($GO_SECURITY->user_id, $template['acl_write']))
					{
						$GO_SECURITY->delete_acl($template['acl_read']);
						$GO_SECURITY->delete_acl($template['acl_write']);
						die($strAclError);
					}
					$template['user_id'] = $GO_SECURITY->user_id;
					
					if(!$template_id = $cms->add_template($template))
					{
						$GO_SECURITY->delete_acl($template['acl_read']);
						$GO_SECURITY->delete_acl($template['acl_write']);
						$feedback = $strSaveError;
					}
				}
			}
			if ($_POST['close'] == 'true')
			{
				header('Location: '.$return_to);
				exit();
			}
		}
	
	break;
	
	case 'save_style':
		$template['id'] = $template_id;
		$template['style'] = smart_addslashes($_POST['style']);
		
		$cms->update_template($template);
	break;

	case 'save_additional_style':
		$template['id'] = $template_id;
		$template['additional_style'] = smart_addslashes($_POST['additional_style']);
		
		$cms->update_template($template);
	break;

	case 'save_print_style':
		$template['id'] = $template_id;
		$template['print_style'] = smart_addslashes($_POST['print_style']);
		
		$cms->update_template($template);
	break;
	
	case 'save_fckeditor_styles':
		$template['id'] = $template_id;
		$template['fckeditor_styles'] = smart_addslashes($_POST['fckeditor_styles']);
		
		$cms->update_template($template);
	break;
}

if ($template_id > 0)
{
	$template = $cms->get_template($template_id);

	if (!$GO_SECURITY->has_permission($GO_SECURITY->user_id, $template['acl_write']))
	{
		header('Location: '.$GO_CONFIG->host.'error_docs/403.php');
		exit();
	}
}

$form = new form('template_form');
$form->add_html_element(new input('hidden', 'template_id', $template_id, false));
$form->add_html_element(new input('hidden', 'task', '', false));
$form->add_html_element(new input('hidden', 'close', '', false));

//create a tab window
$tabstrip = new tabstrip('template_tabstrip',$cms_theme, '140', 'template_form', 'vertical');
$tabstrip->set_attribute('style','width:100%;height:100%');
$tabstrip->set_return_to($return_to);

if ($template_id > 0)
{
	$tabstrip->add_tab('properties', $strProperties);
	$tabstrip->add_tab('style', $cms_style);
	$tabstrip->add_tab('additional_style', $cms_additional_style);
	$tabstrip->add_tab('fckeditor_styles', $cms_fckeditor_styles);
	$tabstrip->add_tab('print_style', $cms_print_style);
	$tabstrip->add_tab('template_items', $cms_templates);
	$tabstrip->add_tab('template_files', $cms_files);

	$tabstrip->add_tab('read_permissions', $strReadRights);
	$tabstrip->add_tab('write_permissions', $strWriteRights);
	if(isset($active_tab))
	{
		$tabstrip->set_active_tab($active_tab);
	}
}


if (isset ($feedback))
{
	$p = new html_element('p', $feedback);
	$p->set_attribute('class','Error');
	$tabstrip->add_html_element($p);
}
	
switch($tabstrip->get_active_tab_id())
{
	case 'template_files':
		$template_file_path = $GO_CONFIG->local_path.'cms/templates/'.$template_id.'/';		
		if(!is_dir($template_file_path))
		{
			require_once($GO_CONFIG->class_path.'filesystem.class.inc');
			$fs = new filesystem();
			mkdir_recursive($template_file_path);	
			$fs->add_share($template['user_id'], $template_file_path, 'template', $template['acl_read'], $template['acl_write']);
		}
		require($GO_MODULES->modules['filesystem']['class_path'].'filesystem_list.class.inc');


		$fl = new filesystem_list('cms_templatefiles_list', $template_file_path);
		$GO_HEADER['head'] = $fl->get_header();
		$menu = new button_menu();
		$menu->add_button(
				'upload', 
				$strUpload, 
				$GO_MODULES->modules['filesystem']['url'].
					'index.php?task=upload&path='.urlencode($template_file_path).'&return_to='.
						urlencode($link_back));
		$menu->add_button(
			'delete_big', 
			$cmdDelete, 
			$fl->get_delete_handler());
			
	 $form->add_html_element($menu);
	 $tabstrip->add_html_element($fl);
	 $tabstrip->add_html_element(new button($cmdClose, "javascript:document.location='".$return_to."';"));
	break;
	
	case 'write_permissions':
		$tabstrip->innerHTML .= get_acl($template["acl_write"]);
		$tabstrip->add_html_element(new button($cmdClose, "javascript:document.location='".$return_to."';"));

		break;
		
	case 'style':
	
		if($task == 'replace_template_files')
		{
			$template['style'] = $cms->replace_template_files($template_id, smart_stripslashes($_POST['style']));
		}	
		$table = new table();
		$table->set_attribute('style', 'width:100%;height:100%');
		
		$row = new table_row();		
		
		$textarea = new textarea('style', $template['style']);
		$textarea->set_attribute('style', 'width:100%;height:100%');
		$cell = new table_cell($textarea->get_html());
		$cell->set_attribute('style', 'width:100%;height:100%');
		$row->add_cell($cell);
		
		$cell = new table_cell();
		$cell->set_attribute('style','width:300px;vertical-align:top');
		
		$filestab = new tabstrip('filestab', $cms_files);
		$filestab->set_attribute('style','width:300px;');
		
		require_once($GO_CONFIG->class_path.'filesystem.class.inc');
		$fs = new filesystem();

		$template_file_path = $GO_CONFIG->local_path.'cms/templates/'.$template_id.'/';		

		$files = $fs->get_files($template_file_path);
		foreach($files as $file)
		{
		  $short_name = cut_string($file['name'], 30);
		  
		  $link = new hyperlink('javascript:paste_url(\''.addslashes($GO_CONFIG->local_url.'cms/templates/'.$template_id.'/'.$file['name']).'\', \'style\');', $short_name);
			$link->set_attribute('class', 'selectableItem');
			$filestab->add_html_element($link);
		}
		
		$button = new button($cms_replace_filenames,'javascript:save(\'replace_template_files\', \'false\');', 140);
		$filestab->add_html_element($button);
		
		$cell->add_html_element($filestab);
		
		$row->add_cell($cell);
		$table->add_row($row);
		
		$row = new table_row();
		$cell = new table_cell();
		$cell->set_attribute('colspan','2');
		$cell->add_html_element(new button($cmdOk, "javascript:save('save_style', 'true');"));
		$cell->add_html_element(new button($cmdSave, "javascript:save('save_style', 'false');"));
		$cell->add_html_element(new button($cmdClose, "javascript:document.location='".$return_to."';"));
		$row->add_cell($cell);
		$table->add_row($row);
		$tabstrip->add_html_element($table);
	break;

	case 'additional_style':
	
		if($task == 'replace_template_files')
		{
			$template['additional_style'] = $cms->replace_template_files($template_id, smart_stripslashes($_POST['additional_style']));
		}	
		$table = new table();
		$table->set_attribute('style', 'width:100%;height:100%');
		
		$row = new table_row();		
		
		$textarea = new textarea('additional_style', $template['additional_style']);
		$textarea->set_attribute('style', 'width:100%;height:100%');
		$cell = new table_cell($textarea->get_html());
		$cell->set_attribute('style', 'width:100%;height:100%');
		$row->add_cell($cell);
		
		$cell = new table_cell();
		$cell->set_attribute('style','width:300px;vertical-align:top');
		
		$filestab = new tabstrip('filestab', $cms_files);
		$filestab->set_attribute('style','width:300px;');
		
		require_once($GO_CONFIG->class_path.'filesystem.class.inc');
		$fs = new filesystem();

		$template_file_path = $GO_CONFIG->local_path.'cms/templates/'.$template_id.'/';		

		$files = $fs->get_files($template_file_path);
		foreach($files as $file)
		{
		  $short_name = cut_string($file['name'], 30);
		  
		  $link = new hyperlink('javascript:paste_url(\''.addslashes($GO_CONFIG->local_url.'cms/templates/'.$template_id.'/'.$file['name']).'\', \'additional_style\');', $short_name);
			$link->set_attribute('class', 'selectableItem');
			$filestab->add_html_element($link);
		}
		
		$button = new button($cms_replace_filenames,'javascript:save(\'replace_template_files\', \'false\');', 140);
		$filestab->add_html_element($button);
		
		$cell->add_html_element($filestab);
		
		$row->add_cell($cell);
		$table->add_row($row);
		
		$row = new table_row();
		$cell = new table_cell();
		$cell->set_attribute('colspan','2');
		$cell->add_html_element(new button($cmdOk, "javascript:save('save_additional_style', 'true');"));
		$cell->add_html_element(new button($cmdSave, "javascript:save('save_additional_style', 'false');"));
		$cell->add_html_element(new button($cmdClose, "javascript:document.location='".$return_to."';"));
		$row->add_cell($cell);
		$table->add_row($row);
		$tabstrip->add_html_element($table);
	break;
	
	
	case 'fckeditor_styles':
	

		$table = new table();
		$table->set_attribute('style', 'width:100%;height:100%');
		
		$row = new table_row();
		$cell = new table_cell();
		$link = new hyperlink('http://wiki.fckeditor.net/Developer%27s_Guide/Configuration/Styles', $cms_fckeditor_help);
		$link->set_attribute('class','normal');
		$link->set_attribute('target','_blank');
		$cell->add_html_element($link);
		$row->add_cell($cell);
		$table->add_row($row);
		
		
		$row = new table_row();		
		
		$textarea = new textarea('fckeditor_styles', $template['fckeditor_styles']);
		$textarea->set_attribute('style', 'width:100%;height:100%');
		$cell = new table_cell($textarea->get_html());
		$cell->set_attribute('style', 'width:100%;height:100%');
		$row->add_cell($cell);
		
		$table->add_row($row);
		
		$row = new table_row();
		$cell = new table_cell();
		$cell->set_attribute('colspan','2');
		$cell->add_html_element(new button($cmdOk, "javascript:save('save_fckeditor_styles', 'true');"));
		$cell->add_html_element(new button($cmdSave, "javascript:save('save_fckeditor_styles', 'false');"));
		$cell->add_html_element(new button($cmdClose, "javascript:document.location='".$return_to."';"));
		$row->add_cell($cell);
		$table->add_row($row);
		$tabstrip->add_html_element($table);
	break;

	case 'print_style':
	
		if($task == 'replace_template_files')
		{
			$template['print_style'] = $cms->replace_template_files($template_id, smart_stripslashes($_POST['print_style']));
		}	
		$table = new table();
		$table->set_attribute('style', 'width:100%;height:100%');
		
		$row = new table_row();		
		
		$textarea = new textarea('print_style', $template['print_style']);
		$textarea->set_attribute('style', 'width:100%;height:100%');
		$cell = new table_cell($textarea->get_html());
		$cell->set_attribute('style', 'width:100%;height:100%');
		$row->add_cell($cell);
		
		$cell = new table_cell();
		$cell->set_attribute('style','width:300px;vertical-align:top');
		
		$filestab = new tabstrip('filestab', $cms_files);
		$filestab->set_attribute('style','width:300px;');
		
		require_once($GO_CONFIG->class_path.'filesystem.class.inc');
		$fs = new filesystem();

		$template_file_path = $GO_CONFIG->local_path.'cms/templates/'.$template_id.'/';		

		$files = $fs->get_files($template_file_path);
		foreach($files as $file)
		{
		  $short_name = cut_string($file['name'], 30);
		  
		  $link = new hyperlink('javascript:paste_url(\''.addslashes($GO_CONFIG->local_url.'cms/templates/'.$template_id.'/'.$file['name']).'\', \'print_style\');', $short_name);
			$link->set_attribute('class', 'selectableItem');
			$filestab->add_html_element($link);
		}
		
		$button = new button($cms_replace_filenames,'javascript:save(\'replace_template_files\', \'false\');', 140);
		$filestab->add_html_element($button);
		
		$cell->add_html_element($filestab);
		
		$row->add_cell($cell);
		$table->add_row($row);
		
		$row = new table_row();
		$cell = new table_cell();
		$cell->set_attribute('colspan','2');
		$cell->add_html_element(new button($cmdOk, "javascript:save('save_print_style', 'true');"));
		$cell->add_html_element(new button($cmdSave, "javascript:save('save_print_style', 'false');"));
		$cell->add_html_element(new button($cmdClose, "javascript:document.location='".$return_to."';"));
		$row->add_cell($cell);
		$table->add_row($row);
		$tabstrip->add_html_element($table);
	break;


	case 'read_permissions':
		$tabstrip->innerHTML .= get_acl($template["acl_read"]);
		$tabstrip->add_html_element(new button($cmdClose, "javascript:document.location='".$return_to."';"));
		break;
		
	case 'template_items':	
	
		if ($task == 'save_templates')
		{
			$template['login_template_item_id'] = $update_template['login_template_item_id'] = smart_addslashes($_POST['login_template_item_id']);
		}
		
		if($task=='save_templates')
		{
			$update_template['id']=$template_id;
			$cms->update_template($update_template);
			
		}
	
		
		
		$table = new table();
		$row = new table_row();		
		$cell = new table_cell($cms_login_template_item.':');
		$row->add_cell($cell);		
		
		$select = new select('login_template_item_id', $template['login_template_item_id']);	
		$select->set_attribute('onchange','javascript:document.forms[0].task.value=\'save_templates\';document.forms[0].submit();');
		$select->add_value('',$cms_default_go_login);
			
		$cms->get_template_items($template_id);
		while($cms->next_record())
		{
			if($cms->f('page')=='1')
			{
				$select->add_value($cms->f('id'), $cms->f('name'));
			}
		}
		
		$cell = new table_cell($select->get_html());
		$row->add_cell($cell);
		$table->add_row($row);
		
		$tabstrip->add_html_element($table);
		
		load_control('datatable');
		$datatable = new datatable('template_items');
		if($datatable->task=='delete')
		{
			foreach($datatable->selected as $template_item_id)
			{
				$cms->delete_template_item($template_item_id);
			}
		}
		$datatable->add_column(new table_heading($strName));
		$GO_HEADER['head'] = $datatable->get_header();
	
		$menu = new button_menu();
		$menu->add_button(
				'add', 
				$cmdAdd, 
				$GO_MODULES->modules['cms']['url'].
					'template_item.php?template_id='.$template_id);
		$menu->add_button(
			'delete_big', 
			$cmdDelete, 
			$datatable->get_delete_handler());

		$form->add_html_element($menu);
		
		
		$count = $cms->get_template_items($template_id);
	  while($cms->next_record())
	  {
	  	$row = new table_row($cms->f('id'));
	  	$row->set_attribute('ondblclick', "javascript:document.location='template_item.php?template_item_id=".$cms->f('id')."';");
	  	$row->add_cell(new table_cell(htmlspecialchars($cms->f('name'))));
	  	$datatable->add_row($row);
	  }
	  
	  $tabstrip->add_html_element($datatable);
		$tabstrip->add_html_element(new button($cmdClose, "javascript:document.location='".$return_to."';"));
	break;
		
	default:
		if ($template_id > 0 && $task != 'save_properties')
		{
			$restrict_check = (isset($template) && $template['restrict_editor'] == '1') ? true : false;
			$name = $template['name'];
			$doctype = $template['doctype'];
		}else
		{
			$restrict_check = isset($_POST['restrict_editor']) ? true : false;
			$name = isset($_POST['name']) ? smart_stripslashes($_POST['name']) : '';		
			$doctype = isset($_POST['doctype']) ? smart_stripslashes($_POST['doctype']) : '';
		}
		
		$table = new table();
		$row = new table_row();
		$row->add_cell(new table_cell($strName.':'));
		$input = new input('text', 'name', $name);
		$input->set_attribute('style', 'width:300px');
		$row->add_cell(new table_cell($input->get_html()));
		$table->add_row($row);

		$row = new table_row();
		$row->add_cell(new table_cell('DOCTYPE:'));
		$textarea = new textarea('doctype', $doctype);
		$textarea->set_attribute('style', 'width:600px;height:100px;');
		$row->add_cell(new table_cell($textarea->get_html()));
		$table->add_row($row);
		
		$row = new table_row();
		$cell = new table_cell();
		$cell->set_attribute('colspan','2');
		$cell->add_html_element(new checkbox('restrict_editor','restrict_editor', 'true', $cms_restrict_editor, $restrict_check));
		$row->add_cell($cell);
		$table->add_row($row);
				
		$tabstrip->add_html_element($table);
		
		$tabstrip->add_html_element(new button($cmdOk, "javascript:save('save_properties', 'true');"));
		$tabstrip->add_html_element(new button($cmdSave, "javascript:save('save_properties', 'false');"));
		$tabstrip->add_html_element(new button($cmdClose, "javascript:document.location='".$return_to."'"));
	break;
}
//require the content file. This will draw the logo's and the menu
require_once($GO_THEME->theme_path."header.inc");
$form->add_html_element($tabstrip);
echo $form->get_html();
?>
<script type="text/javascript">
function save(task, close)
{
	document.template_form.close.value=close;
	document.template_form.task.value=task;
	document.template_form.submit();
}

function paste_url(url, field)
{
  var textarea = document.forms[0].elements[field];

  if (document.all)
  {
    textarea.value=url+"\r\n"+textarea.value
  }else
  {
    textarea.value=textarea.value.substring(0,textarea.selectionStart)+url+textarea.value.substring(textarea.selectionEnd,textarea.value.length);
  }
}
</script>
<?php
require_once($GO_THEME->theme_path."footer.inc");
