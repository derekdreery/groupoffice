<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.12 $ $Date: 2006/11/27 13:07:04 $
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */
 
require_once("../../Group-Office.php");


$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('summary', true);
require_once($GO_LANGUAGE->get_language_file('summary'));

require_once($GO_MODULES->class_path."summary.class.inc");
$summary = new summary();

$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';
$announcement_id = isset($_REQUEST['announcement_id']) ? $_REQUEST['announcement_id'] : 0;

$return_to = 'announcements.php';
$link_back = isset($_REQUEST['link_back']) ? htmlspecialchars($_REQUEST['link_back']) : $_SERVER['REQUEST_URI'];

load_basic_controls();
load_control('htmleditor');
load_control('date_picker');

switch ($task)
{
	case 'save_announcement':
		$due_time = date_to_unixtime($_POST['due_time']);
		$title = smart_addslashes(trim($_POST['title']));
		if ($title == '')
		{
			$feedback = '<p class="Error">'.$error_missing_field.'</p>';
		}else
		{
			if ($announcement_id > 0)
			{
				if(!$summary->update_announcement($_POST['announcement_id'],
																			$title,																			
																			smart_addslashes($_POST['content']),
																			$due_time))
				{
					$feedback = '<p class="Error">'.$strSaveError.'</p>';
				}else
				{					
					if ($_POST['close'] == 'true')
					{
						header('Location: '.$return_to);
						exit();
					}
				}
			}else
			{
				$acl_id = $GO_SECURITY->get_new_acl('announcement');
				if ($acl_id > 0)
				{
					if (!$announcement_id = $summary->add_announcement($_POST['user_id'],
																					$due_time,
																					$title,
																					smart_addslashes($_POST['content']),
																					$acl_id))
					{
						$GO_SECURITY->delete_acl($acl_id);

						$feedback = '<p class="Error">'.$strSaveError.'</p>';
					}else
					{						
						if (!isset($_POST['private']))
						{
							$GO_SECURITY->add_group_to_acl($GO_CONFIG->group_everyone, $acl_id);
						}
											
						if ($_POST['close'] == 'true')
						{
							header('Location: '.$return_to);
							exit();
						}
					}
				}else
				{
					$feedback = '<p class="Error">'.$strSaveError.'</p>';
				}
			}
		}
	break;
}

if ($announcement_id > 0)
{
	$announcement = $summary->get_announcement($announcement_id);
	$tabstrip = new tabstrip('announcement_tab', $announcement['title']);
	$tabstrip->add_tab('properties', $strProperties);
	$tabstrip->add_tab('read_permissions', $strReadRights);
}else
{
	$tabstrip = new tabstrip('announcement_tab', $sum_new_announcement);
	$announcement = false;
}
$tabstrip->set_attribute('style','width:100%;height:99%');

if ($announcement && $task != 'save_announcement')
{
	$title = $announcement['title'];
	$user_id = $announcement['user_id'];
	$content = $announcement['content'];
	$due_time = $announcement['due_time'] > 0 ? date($_SESSION['GO_SESSION']['date_format'], $announcement['due_time']) : '';
	$ctime = date($_SESSION['GO_SESSION']['date_format'].' '.
			$_SESSION['GO_SESSION']['time_format'], $announcement['ctime']+
			(get_timezone_offset($announcement['ctime'])*3600));
			
	$mtime = date($_SESSION['GO_SESSION']['date_format'].' '.
			$_SESSION['GO_SESSION']['time_format'], $announcement['mtime']+
			(get_timezone_offset($announcement['mtime'])*3600));

}else
{
	$title = isset($_REQUEST['title']) ? smart_stripslashes($_REQUEST['title']) : '';
	$content = isset($_REQUEST['content']) ? smart_stripslashes($_REQUEST['content']) : '';
	$user_id = isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : $GO_SECURITY->user_id;
	$due_time = isset($_REQUEST['due_time']) ? $_REQUEST['due_time'] : '';
	$ctime = date($_SESSION['GO_SESSION']['date_format'], get_time());
	$mtime = date($_SESSION['GO_SESSION']['date_format'], get_time());
}

//create htmlarea


if ($tabstrip->get_active_tab_id() != 'read_permissions' && $tabstrip->get_active_tab_id() != 'write_permissions')
{

	$GO_HEADER['head'] = date_picker::get_header();
	$GO_HEADER['body_arguments'] = 'onload="document.announcement_form.title.focus();"';
}

require_once($GO_THEME->theme_path."header.inc");

$form = new form('announcement_form');
$form->add_html_element(new input('hidden', 'close', 'false'));
$form->add_html_element(new input('hidden', 'announcement_id', $announcement_id));
$form->add_html_element(new input('hidden', 'task', '', false));
$form->add_html_element(new input('hidden', 'user_id', $user_id));

switch ($tabstrip->get_active_tab_id())
{
	case 'read_permissions':
		$tabstrip->innerHTML .= get_acl($announcement['acl_id']);

		$tabstrip->add_html_element(new button($cmdClose, "javascript:document.location='".$return_to."';"));
	break;

	default:
		
		
		$table = new table();
		$table->set_attribute('style','width:100%;height:100%');
		
		
		if (isset($feedback))
		{
			$row = new table_row();
		
			$cell = new table_cell($feedback);
			$cell->set_attribute('colspan','99');
		  $cell->set_attribute('class','Error');
		  $row->add_cell($cell);
		  $table->add_row($row);
		}
		
		$row = new table_row();
		
		$cell = new table_cell();
		$cell->set_attribute('valign','top');
		
		$subtable = new table();
		$subtable->set_attribute('style','width:100%;height:100%');
		$subrow = new table_row();
		$subrow->add_cell(new table_cell($strTitle.'*:'));
		
		$input = new input('text', 'title', $title);
		$input->set_attribute('style', 'width:250px;');
		$input->set_attribute('maxlength', '50');
		
		$subrow->add_cell(new table_cell($input->get_html()));
		$subtable->add_row($subrow);
		
		$subrow = new table_row();
		$subrow->add_cell(new table_cell($sum_due_time.':'));
		
		$datepicker = new date_picker('due_time', $_SESSION['GO_SESSION']['date_format'], $due_time);
		$subrow->add_cell(new table_cell($datepicker->get_html()));
		$subtable->add_row($subrow);		

		if ($announcement_id == 0)
		{
			$subrow = new table_row();
			$checkbox = new checkbox('private', 'private', 'true', $sum_private, isset($_POST['private']));
			$subcell = new table_cell($checkbox->get_html());
			$subcell->set_attribute('colspan','99');
			$subrow->add_cell($subcell);
			$subtable->add_row($subrow);		
		}
		$cell->add_html_element($subtable);
		$row->add_cell($cell);
		
		$subtable = new table();
		$subrow = new table_row();
		$subrow->add_cell(new table_cell($strOwner.':'));
		$subrow->add_cell(new table_cell(show_profile($user_id, '', 'normal', $link_back)));
		$subtable->add_row($subrow);
		
		$subrow = new table_row();
		$subrow->add_cell(new table_cell($strCreatedAt.':'));
		$subrow->add_cell(new table_cell($ctime));
		$subtable->add_row($subrow);
		
		$subrow = new table_row();
		$subrow->add_cell(new table_cell($strModifiedAt.':'));
		$subrow->add_cell(new table_cell($mtime));
		$subtable->add_row($subrow);
		
		$cell = new table_cell($subtable->get_html());
		$cell->set_attribute('valign','top');
		$row->add_cell($cell);
		
		$table->add_row($row);
		$htmleditor = new htmleditor('content');
		$htmleditor->Value		= $content;
		$htmleditor->ToolbarSet='email';
		//$htmleditor->SetConfig('ImageBrowser',true);
	//	$htmleditor->SetConfig('ImageBrowserURL', $GO_MODULES->modules['email']['url'].'select_image.php');
		
		$cell = new table_cell($htmleditor->CreateHtml());
		$cell->set_attribute('colspan','99');
		$cell->set_attribute('style','width:100%;height:100%');
		
		$row = new table_row();
		$row->add_cell($cell);
		
		$table->add_row($row);
		
		$row = new table_row();
		
		$cell = new table_cell();
		$cell->set_attribute('colspan','99');
		
		$cell->add_html_element(new button($cmdOk, "javascript:_save('save_announcement', 'true');"));
		$cell->add_html_element(new button($cmdApply, "javascript:_save('save_announcement', 'false')"));
		$cell->add_html_element(new button($cmdClose, "javascript:document.location='".$return_to."';"));
		$row->add_cell($cell);
		$table->add_row($row);
		
		$tabstrip->add_html_element($table);
	break;
}

$form->add_html_element($tabstrip);

echo $form->get_html();
?>
<script type="text/javascript">

function _save(task, close)
{
	document.announcement_form.task.value = task;
	document.announcement_form.close.value = close;
	<?php
	if (isset($htmlarea) && $htmlarea->browser_is_supported())
	{
		echo 'document.announcement_form.onsubmit();';
	}
	?>
	document.announcement_form.submit();
}
</script>
<?php
require_once($GO_THEME->theme_path."footer.inc");
?>
