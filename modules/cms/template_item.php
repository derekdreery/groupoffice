<?php
/**
 * @copyright Intermesh 2005
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.2 $ $Date: 2006/11/22 09:35:38 $

 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */
 
//load Group-Office
require_once("../../Group-Office.php");

load_basic_controls();

//authenticate the user
$GO_SECURITY->authenticate();

//see if the user has access to this module
//for this to work there must be a module named 'example'
$GO_MODULES->authenticate('cms', true);

//load the CMS module class library
require_once($GO_MODULES->class_path.'cms.class.inc');
$cms = new cms();


//get the language file
require_once($GO_LANGUAGE->get_language_file('cms'));

$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';
$template_item_id = isset($_REQUEST['template_item_id']) ? $_REQUEST['template_item_id'] : 0;
$template_id = isset($_REQUEST['template_id']) ? $_REQUEST['template_id'] : 0;
$return_to = 'template.php?template_id='.$template_id;
switch($task)
{
	case 'save_template_item':

		$name = trim($_POST['name']);
		$page = isset($_POST['page']) ? '1' : '0';
		if ($name == '')
		{
			$feedback= $error_missing_field;
		}else
		{
			if ($template_item_id > 0)
			{
				if ($template_item = $cms->get_template_by_name($template_id, $name) && $template_item['id'] != $template_item_id)
				{
					$feedback = $fbNameExists;
				}else
				{
					if (!$cms->update_template_item($template_item_id, $name, smart_addslashes($_POST['content']), $page))
					{
						$feedback = $strSaveError;
					}
				}
			}else
			{
				if ($cms->get_template_item_by_name($template_id, $name))
				{
					$feedback = $fbNameExists;
				}else
				{
					if(!$template_item_id = $cms->add_template_item($template_id, $name, smart_addslashes($_POST['content']), $page))
					{
						$feedback = $strSaveError;
					}
				}
			}
			if ($_POST['close'] == 'true')
			{
				header('location: '.$return_to);
				exit();
			}
		}
	break;
}

if ($template_item_id > 0 && $task != 'save_template_item')
{
  $template_item = $cms->get_template_item($template_item_id);
	$template_id=$template_item['template_id'];
	$return_to = 'template.php?template_id='.$template_id;
}else
{
  $template_item['name'] = isset($_POST['name']) ? smart_stripslashes($_POST['name']) : '';
  $template_item['content'] = isset($_POST['content']) ? smart_stripslashes($_POST['content']) : '';
  if($task == 'save_template_item')
  {
  	$template_item['page'] = isset($_POST['page']) ? '1' : '0';
  }else
  {
  	$template_item['page'] = '1';
  }
 
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
$form->add_html_element(new input('hidden', 'template_item_id', $template_item_id, false));
$form->add_html_element(new input('hidden', 'template_id', $template_id, false));
$form->add_html_element(new input('hidden', 'task', '', false));
$form->add_html_element(new input('hidden', 'close', '', false));

$tabstrip = new tabstrip('template_tabstrip',$cms_templates);
$tabstrip->set_attribute('style','width:100%;height:100%');
$tabstrip->set_return_to($return_to);


if($task == 'replace_template_files')
{
	$template_item['content'] = $cms->replace_template_files($template_id, smart_stripslashes($_POST['content']));
}	
$table = new table();
$table->set_attribute('style', 'width:100%;height:100%');

$row = new table_row();
$cell = new table_cell();
$cell->set_attribute('colspan','2');

$subtable = new table();
$subrow = new table_row();
$subrow->add_cell(new table_cell($strName.':'));
$input = new input('text', 'name', $template_item['name']);
$input->set_attribute('style', 'width:300px');
$subrow->add_cell(new table_cell($input->get_html()));

$checkbox = new checkbox('page', 'page', '1', $cms_page_template, ($template_item['page']=='1'));
$subrow->add_cell(new table_cell($checkbox->get_html()));
$subtable->add_row($subrow);
		
$cell->add_html_element($subtable);

$row->add_cell($cell);
$table->add_row($row);

$row = new table_row();		

$textarea = new textarea('content', $template_item['content']);
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
  
  $link = new hyperlink('javascript:paste_url(\''.addslashes($GO_CONFIG->local_url.'cms/templates/'.$template_id.'/'.$file['name']).'\', \'content\');', $short_name);
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
$cell->add_html_element(new button($cmdOk, "javascript:save('save_template_item', 'true');"));
$cell->add_html_element(new button($cmdSave, "javascript:save('save_template_item', 'false');"));
$cell->add_html_element(new button($cmdClose, "javascript:document.location='".$return_to."';"));
$row->add_cell($cell);
$table->add_row($row);
$tabstrip->add_html_element($table);



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
