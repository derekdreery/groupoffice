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

require_once ("../../Group-Office.php");

load_basic_controls();
load_control('htmleditor');

//authenticate the user
$GO_SECURITY->authenticate();

//see if the user has access to this module
//for this to work there must be a module named 'example'
$GO_MODULES->authenticate('cms');

require_once ($GO_MODULES->class_path.'cms.class.inc');
$cms = new cms();

//get the language file
require_once ($GO_LANGUAGE->get_language_file('cms'));

$task = isset ($_REQUEST['task']) ? $_REQUEST['task'] : '';
$file_id = isset ($_REQUEST['file_id']) ? $_REQUEST['file_id'] : 0;
$folder_id = isset ($_REQUEST['folder_id']) ? $_REQUEST['folder_id'] : 0;
$link_back = 'edit.php?file_id='.$file_id.'&folder_id='.$folder_id;


if(isset($_REQUEST['site_id']))
{
	$_SESSION['site_id']=smart_stripslashes($_REQUEST['site_id']);
}

if ($folder_id == 0 || $_SESSION['site_id'] == 0) {
	//no folder or site given so back off cowardly
	header('Location: index.php');
	exit ();
}

switch($task)
{
	case 'save':
		$name = smart_addslashes(trim($_POST['name']));
		$hot_item = isset ($_POST['hot_item']) ? '1' : '0';
		//fix for inserted iframes
		$content = preg_replace("'<iframe([^>]*)/>'si", "<iframe$1></iframe>", smart_addslashes($_POST['content']));
	
		$title = (isset($_POST['title']) && $_POST['title'] != '' && !isset($_POST['auto_meta'])) ? 
				smart_addslashes($_POST['title']) : 
				addslashes($cms->get_title_from_html(smart_stripslashes($content), smart_stripslashes($_POST['title'])));
								
		$description = (isset($_POST['description']) && $_POST['description'] != '' && !isset($_POST['auto_meta'])) ? 
				smart_addslashes($_POST['description']) : 
				addslashes($cms->get_description_from_html(smart_stripslashes($content), smart_stripslashes($_POST['description'])));
				
		$keywords = (isset($_POST['keywords']) && $_POST['keywords'] != '' && !isset($_POST['auto_meta'])) ? 
				smart_addslashes($_POST['keywords']) : 
				addslashes($cms->get_keywords_from_html(smart_stripslashes($content), smart_stripslashes($_POST['keywords'])));
		
		$auto_meta = isset($_POST['auto_meta']) ? '1' : '0';
			
					
		if ($file_id > 0) {		
			if ($name == '') {
				$feedback = $error_missing_field;
			} else {
				$name .= '.html';
				$existing_id = $cms->file_exists($folder_id, $name);
				if ($existing_id && ($_POST['file_id'] != $existing_id)) {
					$feedback = $fbNameExists;
				} else {				
					
					$old_file = $cms->get_file($file_id);
					if (isset($_POST['go_auth']))
					{
						if ($old_file['acl'] == 0)
						{
							if (!$acl = $GO_SECURITY->get_new_acl())
							{
								die($strAclError);
							}
						}else
						{
							$acl = $old_file['acl'];
						}
					}else
					{
						$acl = 0;
						if($old_file['acl'] > 0)
						{
							$GO_SECURITY->delete_acl($old_file['acl']);
						}
					}
				
					$cms->update_file(
						$file_id, 
						$name, 
						smart_addslashes($content), 
						$auto_meta, 
						$title, 
						$description, 
						$keywords, 
						$hot_item, 
						$_POST['template_item_id'],
						$acl);
				}
			}
		} else {
	
			if ($name == '') {
				$feedback =$error_missing_field;
			} else {
				$filename = $name.'.html';
				
	
				if ($cms->file_exists($folder_id, $filename)) {
					$feedback = $fbNameExists;
				}else
				{
					if (isset($_POST['go_auth']))
					{
						$acl = $GO_SECURITY->get_new_acl();
					}else
					{
						$acl=0;
					}
					if (!$file_id = $cms->add_file(
						$folder_id, 
						$filename, 
						smart_addslashes($_POST['content']), 
						$title, 
						$description, 
						$keywords, 
						$_POST['template_item_id'],
						$auto_meta,
						$hot_item,
						$acl))
					{
						$feedback = $strSaveError;
					}
				}
			}
		}
	break;
	
	case 'email':
	
		$tmpfile = $cms->get_file($file_id);
		$mail_to = array();
		$users = $GO_SECURITY->get_authorized_users_in_acl($tmpfile['acl']);
	
		foreach($users as $user_id)
		{
			$user = $GO_USERS->get_user($user_id);
			$mail_to[] = $user['email'];		
		}
		
		$mail_to = implode(',', $mail_to);
		
		$GO_HEADER['body_arguments'] = 'onload="'.
				'popup(\''.$GO_MODULES->modules['email']['url'].'send.php?mail_to='.urlencode($mail_to).'\',\''.
				$GO_CONFIG->composer_width.'\',\''.$GO_CONFIG->composer_height.'\');"';
		break;
	
	case 'save_hot_item_text':
		$file['id']=$file_id;
		$file['hot_item_text']=smart_addslashes($_POST['hot_item_text']);
		$cms->__update_file($file);
	break;
}

//get the site template
if ($site = $cms->get_site($_SESSION['site_id'])) {
	//$language = $cms->get_language($_SESSION['language_id']);
	$template = $cms->get_template($site['template_id']);
}

if ($file_id > 0) {
	$file = $cms->get_file($file_id);	
} else {

	$file['name'] = isset($_POST['name']) ? smart_stripslashes($_POST['name']) : '';	
	$file['hot_item'] = isset ($_POST['hot_item']) ? '1' : '0';
	//fix for inserted iframes
	$file['content'] = isset($_POST['content']) ? preg_replace("'<iframe([^>]*)/>'si", "<iframe$1></iframe>", smart_stripslashes($_POST['content'])) : '';	
	$file['title'] = isset($_POST['title']) ? smart_stripslashes($_POST['title']) : '';	
	$file['description'] = isset($_POST['description']) ? smart_stripslashes($_POST['description']) : '';	
	$file['keywords'] = isset($_POST['keywords']) ? smart_stripslashes($_POST['keywords']) : '';	
	if($task == 'save')
	{
			$file['auto_meta'] = isset($_POST['auto_meta']) ? '1' : '0';
	}else
	{
		$file['auto_meta'] = '1';
	}
	$file['folder_id'] = $folder_id;
	
	if(isset($_REQUEST['template_item_id']))
	{
		$file['template_item_id']=$_REQUEST['template_item_id'];
	}else
	{
		$folder = $cms->get_folder($file['folder_id']);
		$file['template_item_id']=$folder['template_item_id'];
	}
	$file['acl']=0;
}




$form = new form('editor');
$form->set_attribute('style','height:99%');
$form->add_html_element(new input('hidden', 'file_id', $file_id, false));
$form->add_html_element(new input('hidden', 'folder_id', $file['folder_id']));
$form->add_html_element(new input('hidden', 'unedited', ''));
$form->add_html_element(new input('hidden', 'task', '', false));

$table = new table();
$table->set_attribute('style','height:100%;width:100%');

if (isset ($feedback))
{
	$cell = new table_cell($feedback);
	$cell->set_attribute('class','Error');
	$cell->set_attribute('colspan','2');
	$row =new table_row();
	$row->add_cell($cell);
	
	$table->add_row($row);
}


$tabstrip = new tabstrip('cms_properties', $fbProperties, 160);
$tabstrip->set_attribute('style','width:100%;height:100%');
if($file['acl']>0 || $file['hot_item']=='1')
{
	$tabstrip->add_tab('properties', $fbProperties, $table);
	
	if($cms->get_comments($file_id))
	{
		$tabstrip->add_tab('comments', $cms_comments);
	}
	
	if($file['hot_item']=='1')
	{
		$tabstrip->add_tab('hot_item_text', $cms_hot_item_text);
	}
	if($file['acl']>0)
	{
		$aclspan = new html_element('span', get_acl($file['acl']));
		$tabstrip->add_tab('acl', $strPermissions);	
	}
}

$menu = new button_menu();

if($tabstrip->get_active_tab_id()=='properties' || $tabstrip->get_active_tab_id()=='')
{
	$menu->add_button('save_big', $cmdSave, "javascript:_save('save');");
	if ($file_id > 0) {
		$menu->add_button('magnifier_big', $cms_preview, 'view.php?site_id='.
			$_SESSION['site_id'].'&folder_id='.$file['folder_id'].'&file_id='.$file['id'], array('target'=>'_blank'));
	}
	$menu->add_button('components', $cms_insert_plugin, "javascript:popup('select_plugin.php', '400','400');");
	
}elseif($tabstrip->get_active_tab_id()=='hot_item_text')
{
	$menu->add_button('save_big', $cmdSave, "javascript:_save('save_hot_item_text');");
}elseif($tabstrip->get_active_tab_id()=='comments')
{
	
	$datatable = new datatable('cms_comments');
	
	if($datatable->task=='delete')
	{
		foreach($datatable->selected as $comment_id)
		{
			$cms->delete_comment($comment_id);
		}
	}
	$GO_HEADER['head'] = $datatable->get_header();
	$menu->add_button('delete_big', $cmdDelete, $datatable->get_delete_handler());
}
$menu->add_button('close', $cmdClose, 'javascript:confirm_close(\''.$GO_MODULES->url.'browse.php?folder_id='.$file['folder_id'].'\');');

$cell = new table_cell($menu->get_html());
$cell->set_attribute('colspan','2');
$row =new table_row();
$row->add_cell($cell);
$table->add_row($row);


$htmleditor = new htmleditor('content');
$htmleditor->setImagePath('cms/'.$_SESSION['site_id'].'/images/');
$htmleditor->SetConfig('CustomConfigurationsPath', $GO_MODULES->url.'fckconfig.js');
$htmleditor->SetConfig('EditorAreaCSS', $GO_MODULES->url.'stylesheet.php?template_id='.$site['template_id'].'&editor=true');
$htmleditor->Value		=  $file['content'];
$htmleditor->ToolbarSet='cms';

$htmleditor->SetConfig('LinkBrowser',true);
$htmleditor->SetConfig('LinkBrowserURL', $GO_MODULES->modules['cms']['url'].'select.php');



$editor_table = new table();
$editor_table->set_attribute('style','width:100%;height:100%');

$editor_row = new table_row();
$editor_cell = new table_cell($htmleditor->CreateHtml());
$editor_cell->set_attribute('style','height:100%;');
$editor_row->add_cell($editor_cell);


$editor_cell = new table_cell();
$editor_cell->set_attribute('valign','top');
$editor_cell->set_attribute('style','width:400px;');






$subtable = new table();
$subtable->set_attribute('style','width:100%;');
$subrow = new table_row();
$subrow->add_cell(new table_cell($strName.':'));
$input = new input('text','name', strip_extension($file['name']), false);
$input->set_attribute('style','width:100%');
$subrow->add_cell(new table_cell($input->get_html()));
$subtable->add_row($subrow);

$subrow = new table_row();
$subrow->add_cell(new table_cell($cms_template_item.':'));

$select = new select("template_item_id", $file['template_item_id']);
$cms->get_template_items($site['template_id'], true);
while($cms->next_record())
{
	$select->add_value($cms->f('id'), $cms->f('name'));
}
$subcell = new table_cell($select->get_html());
$subrow->add_cell($subcell);
$subtable->add_row($subrow);

$subrow = new table_row();
$checkbox = new checkbox('hot_item','hot_item', '1', $cms_hot_item, ($file['hot_item'] == '1'));
$subcell = new table_cell($checkbox->get_html());
$subcell->set_attribute('colspan','2');
$subrow->add_cell($subcell);
$subtable->add_row($subrow);

$subrow = new table_row();
$checkbox = new checkbox('go_auth','go_auth', '1', $cms_go_auth, ($file['acl']>0));
$subcell = new table_cell($checkbox->get_html());

$subcell->set_attribute('colspan','2');
$subrow->add_cell($subcell);
$subtable->add_row($subrow);


$subrow = new table_row();
$checkbox = new checkbox('auto_meta','auto_meta','1', $cms_autogenerate_searchengine_info, ($file['auto_meta'] == '1'));
$subcell = new table_cell($checkbox->get_html());
$subcell->set_attribute('colspan','2');
$subrow->add_cell($subcell);
$subtable->add_row($subrow);

$editor_cell->add_html_element($subtable);

$subtable = new table();
$subtable->set_attribute('style','width:100%;');

$subrow = new table_row();
$input = new input('text','title', strip_extension($file['title']), false);
$input->set_attribute('style','width:100%');
$subrow->add_cell(new table_cell('<b>'.$strTitle.':</b><br />'.$input->get_html()));
$subtable->add_row($subrow);


$subrow = new table_row();
$input = new textarea('description', $file['description']);
$input->set_attribute('style','width:100%;height:60px;');
$subcell = new table_cell('<b>'.$strDescription.':</b><br />'.$input->get_html());
$subcell->set_attribute('style','width:100%;');
$subrow->add_cell($subcell);
$subtable->add_row($subrow);

$subrow = new table_row();
$input = new textarea('keywords', $file['keywords']);
$input->set_attribute('style','width:100%;height:60px;');
$subcell = new table_cell('<b>'.$cms_keywords.':</b><br />'.$input->get_html());
$subcell->set_attribute('style','width:100%;');
$subrow->add_cell($subcell);
$subtable->add_row($subrow);


$editor_cell->add_html_element($subtable);

if(isset($GO_MODULES->modules['email']) && $GO_MODULES->modules['email']['read_permission'] && $file['acl']>0)
{
	$link = new hyperlink('javascript:_save(\'email\');', 'e-mail gebruikers met toegang');
	$link->set_attribute('class', 'normal');
	$editor_cell->innerHTML .= '<br />'.$link->get_html();
}


$editor_row->add_cell($editor_cell);
$editor_table->add_row($editor_row);





switch($tabstrip->get_active_tab_id())
{
	case 'hot_item_text':
		
		$htmleditor = new htmleditor('hot_item_text');
		$htmleditor->setImagePath('cms/'.$_SESSION['site_id'].'/images/');
		$htmleditor->SetConfig('CustomConfigurationsPath', $GO_MODULES->url.'fckconfig.js');
		$htmleditor->SetConfig('EditorAreaCSS', $GO_MODULES->url.'stylesheet.php?template_id='.$site['template_id'].'&editor=true');
		$htmleditor->Value		=  $file['hot_item_text'];
		$htmleditor->ToolbarSet='cms';
		
		$htmleditor->SetConfig('LinkBrowser',true);
		$htmleditor->SetConfig('LinkBrowserURL', $GO_MODULES->modules['cms']['url'].'select.php');
		
		$tabstrip->innerHTML .= $htmleditor->CreateHtml();
		
	break;
	
	case 'comments':
		
		
		
		$datatable->add_column(new table_heading($strName));
		$datatable->add_column(new table_heading($cms_comments));
		
		if($cms->get_comments($file_id))
		{
			while($cms->next_record())
			{
				$subrow = new table_row($cms->f('id'));
				$subrow->add_cell(new table_cell(htmlspecialchars($cms->f('name'))));
				$subrow->add_cell(new table_cell(htmlspecialchars($cms->f('comments'))));
				$datatable->add_row($subrow);				
			}
			
		}else {
			$subrow = new table_row();
			$subcell = new table_cell($strNoItems);
			$subcell->set_attribute('colspan','2');
			$subrow->add_cell($subcell);
			$datatable->add_row($subrow);			
		}
		
		$tabstrip->add_html_element($datatable);	
		
	break;
	
	case'acl':
		$tabstrip->innerHTML .= get_acl($file['acl']);
	break;
		
	default:
		$tabstrip->add_html_element($editor_table);		
	break;
}

/*
else
{
	$form->add_html_element($table);
}
*/

//$cell->add_html_element($tabstrip);

//$row->add_cell($cell);
//$table->add_row($row);

$cell = new table_cell($tabstrip->get_html());
$cell->set_attribute('colspan','2');
$cell->set_attribute('style','height:100%');
$row =new table_row();
$row->add_cell($cell);
$table->add_row($row);


$form->add_html_element($table);

//require the header file. This will draw the logo's and the menu
require_once ($GO_THEME->theme_path."header.inc");
echo $form->get_html();
?>
  <script type="text/javascript">

  function confirm_close(URL)
  {
  	//TODO: detect if content has been changed
  	//if (confirm('<?php echo $cms_confirm_close; ?>'))
  	//{
  	document.location=URL;
  	//}
  }

  
  function _save(task)
  {
  	document.editor.task.value=task;
  	document.editor.submit();
  }
</script>

<?php
require_once ($GO_THEME->theme_path."footer.inc");
?>
