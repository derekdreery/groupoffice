<?php
/**
 * @copyright Copyright Intermesh 2006
 * @version $Revision: 1.47 $ $Date: 2006/11/21 16:25:40 $
 * 
 * @author Merijn Schering <mschering@intermesh.nl>

   This file is part of Group-Office.

   Group-Office is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   Group-Office is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with Group-Office; if not, write to the Free Software
   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
      
 * @package Notes
 * @category Notes
 */
require_once("../../Group-Office.php");

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('notes');
require_once($GO_LANGUAGE->get_language_file('notes'));

load_basic_controls();

$page_title=$lang_modules['notes'];
require_once($GO_MODULES->class_path."notes.class.inc");
$notes = new notes();

$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';
$note_id = isset($_REQUEST['note_id']) ? $_REQUEST['note_id'] : 0;

$return_to = isset($_REQUEST['return_to']) ? $_REQUEST['return_to'] : $_SERVER['HTTP_REFERER'];
$link_back = isset($_REQUEST['link_back']) ? $_REQUEST['link_back'] : $_SERVER['REQUEST_URI'];


switch ($task) {

	case 'save_note' :
		$note['name'] = smart_addslashes(trim($_POST['name']));
		$note['content'] = smart_addslashes($_POST['content']);
		if ($note_id > 0)
		{
			$note['id'] = $note_id;
			if ($note['name'] == '')
			{
				$feedback = $error_missing_field;
			}elseif(!$notes->update_note($note))
			{
				$feedback = $strSaveError;
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
			$note['user_id'] = $GO_SECURITY->user_id;
			$note['link_id'] = $GO_LINKS->get_link_id();

			if ($note['name'] == '')
			{
				$feedback = $error_missing_field.'</p>';
			}elseif (!$note_id = $notes->add_note($note))
			{
				$feedback = $strSaveError;
			}else
			{
				if(isset($_POST['link']['link_id']) && $_POST['link']['link_id']>0)
				{
					$GO_LINKS->add_link($_POST['link']['link_id'],$_POST['link']['link_type'], $note['link_id'], 4);
				}

				if ($_POST['close'] == 'true')
				{
					header('Location: '.$return_to);
					exit();
				}else {

					$link_back = add_params_to_url($link_back, 'note_id='.$note_id);
				}
			}
		}
		break;
}

if ($note_id > 0)
{

	$note = $notes->get_note($note_id);
	$tabstrip = new tabstrip('note_tab', $note['name']);


}else
{

	$tabstrip = new tabstrip('note_tab', $no_new_note);
	$note = false;
}
$tabstrip->set_attribute('style','width:100%;height:300px');
$tabstrip->set_return_to(htmlspecialchars($return_to));

if ($note && $task != 'save_note')
{
	$name = $note['name'];
	$user_id = $note['user_id'];
	$file_path = $note['file_path'];
	$content = $note['content'];
	$ctime = date($_SESSION['GO_SESSION']['date_format'].' '.$_SESSION['GO_SESSION']['time_format'], gmt_to_local_time($note['ctime']));
	$mtime = date($_SESSION['GO_SESSION']['date_format'].' '.$_SESSION['GO_SESSION']['time_format'], gmt_to_local_time($note['mtime']));
}else
{
	$name = isset($_REQUEST['name']) ? smart_stripslashes($_REQUEST['name']) : '';
	$content = isset($_REQUEST['content']) ? smart_stripslashes($_REQUEST['content']) : '';
	$user_id = isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : $GO_SECURITY->user_id;
	$due_date = isset($_REQUEST['due_date']) ? $_REQUEST['due_date'] : '';
	$ctime = date($_SESSION['GO_SESSION']['date_format'], get_time());
	$mtime = date($_SESSION['GO_SESSION']['date_format'], get_time());
}

//check permissions of parent objects
$write_permission = true;
$read_permission = true;

if((!$write_permission && $note_id == 0) || !$read_permission)
{
	header('Location: '.$GO_CONFIG->host.'error_docs/403.php');
	exit();
}



$form = new form('note_form');
$form->add_html_element(new input('hidden','close','false'));
$form->add_html_element(new input('hidden','note_id',$note_id,false));
$form->add_html_element(new input('hidden','task','save',false));


$maintable = new table();
$maintable->set_attribute('style','width:100%;height:100%;');


$row = new table_row();
$row->add_cell(new table_cell($strName.':*'));
if ($write_permission)
{
	$input = new input('text','name',$name);
	$input->set_attribute('maxlength','50');
	$input->set_attribute('style','width: 100%;');

	$row->add_cell(new table_cell($input->get_html()));
}else
{
	$row->add_cell(new table_cell(htmlspecialchars($note['name'])));
}
$maintable->add_row($row);

if($note_id>0)
{
	$row = new table_row();
	$row->add_cell(new table_cell($strOwner.':'));
	$row->add_cell(new table_cell(show_profile($user_id, '', 'normal', $link_back)));
	
	$row = new table_row();
	$cell = new table_cell($strCreatedAt.':');
	$cell->set_attribute('style','white-space:nowrap');
	$row->add_cell($cell);
	$row->add_cell(new table_cell($ctime));
	$maintable->add_row($row);
	
	$row = new table_row();
	$cell = new table_cell($strModifiedAt.':');
	$cell->set_attribute('style','white-space:nowrap');
	$row->add_cell($cell);
	$row->add_cell(new table_cell($mtime));
	$maintable->add_row($row);

}else {
	load_control('select_link');

	$link_id=isset($_REQUEST['link_id']) ? $_REQUEST['link_id'] : 0;
	$link_type=isset($_REQUEST['link_type']) ? $_REQUEST['link_type'] : 0;
	$link_text=isset($_REQUEST['link_text']) ? $_REQUEST['link_text'] : '';
	$sl = new select_link('link',$link_type,$link_id,$link_text,'note_form');

	$row = new table_row();
	$link = $sl->get_link($strCreateLink);
	$cell = new table_cell($link->get_html().':');
	$cell->set_attribute('style','width:250px;white-space:nowrap');
	$row->add_cell($cell);
	$field=$sl->get_field('100%');
	$cell = new table_cell($field->get_html());
	$cell->set_attribute('style','width:250px;');
	$row->add_cell($cell);
	$table->add_row($row);
	
	
}
$maintable->add_row($row);



$mainrow = new table_row();
$maincell= new table_cell();
$maincell->set_attribute('colspan','2');
$maincell->set_attribute('style','width:100%;height:100%;');
$textarea = new textarea('content', $content);
$textarea->set_attribute('style','width:100%;height:100%;');
$maincell->add_html_element($textarea);
$mainrow->add_cell($maincell);
$maintable->add_row($mainrow);


$form->add_html_element($maintable);

echo $form->get_html();



