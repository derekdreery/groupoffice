<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.19 $ $Date: 2006/11/21 16:25:38 $

 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */

require_once("../../Group-Office.php");
$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('email');

load_basic_controls();
load_control('tabtable');
load_control('dropbox');

require_once($GO_MODULES->class_path."email.class.inc");
require_once($GO_LANGUAGE->get_language_file('email'));
$email = new email();

$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';
$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
$return_to = (isset($_REQUEST['return_to']) && $_REQUEST['return_to'] != '') ? $_REQUEST['return_to'] : $_SERVER['HTTP_REFERER'];

$filter_id = isset($_REQUEST['filter_id']) ? $_REQUEST['filter_id'] : 0;

switch($task){
  case 'save_filter': 
    if ($_POST['keyword'] != "" && $_POST['folder'] != "")
    {
    	$mark_as_read= isset($_POST['mark_as_read']) ? '1' : '0';
      if ($email->add_filter($id, smart_addslashes($_POST['field']),
		    smart_addslashes($_POST['keyword']),
		    smart_addslashes($_POST['folder']),
		    $mark_as_read))
      {
        header('Location: '.$return_to);
        exit();
      }else
      {
        $feedback = '<p class="Error">'.$strSaveError.'</p>';
       }
    }else
    {
      $feedback = '<p class="Error">'.$error_missing_field.'</p>';
    }
    break;
  case 'update_filter': 
    if ($_POST['keyword'] != "")
    {
    	 $mark_as_read= isset($_POST['mark_as_read']) ? '1' : '0';
		  $email->update_filter($filter_id,
		  smart_addslashes($_POST['field']),
		  smart_addslashes($_POST['keyword']),
		  smart_addslashes($_POST['folder']),
		 $mark_as_read);
		  header('Location: '.$return_to);
		  exit();
    }else
    {
      $feedback = '<p class="Error">'.$error_missing_field.'</p>';
    }
	break;
}

if ($filter_id > 0)
{
  $filter = $email->get_filter($filter_id);
  $field = $filter["field"];
  $keyword = $filter["keyword"];
  $folder = $filter["folder"];
  $mark_as_read = ($filter['mark_as_read'] == '1');
  $title = $ml_edit_filter;
}else
{
  $field = isset($_POST['field']) ? $_POST['field'] : '';
  $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : '';
  $folder = isset($_POST['folder']) ? $_POST['folder'] : '';
  $priority = isset($_POST['priority']) ? $_POST['priority'] : '1';
  $mark_as_read = isset($_POST['mark_as_read']);
  $title = $ml_new_filter;
}

require_once($GO_THEME->theme_path."header.inc");
$tabtable = new tabtable('filters_list', $title, '100%', '300', '100', '', true);
$tabtable->print_head(htmlspecialchars($return_to));
?>
<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="email_client">
<input type="hidden" name="id" value="<?php echo $id; ?>" />
<input type="hidden" name="return_to" value="<?php echo htmlspecialchars($return_to); ?>" />
<input type="hidden" name="task" />
<input type="hidden" name="filter_id" value="<?php echo $filter_id; ?>" />

<table border="0" cellpadding="4" cellspacing="0">
<tr><td colspan="2"></td></tr>
<tr><td><?php echo $ml_choose_action; ?></td><td><?php echo $ml_search_criteria; ?>:</td></tr>
<tr><td>

<?php
$dropbox=new dropbox();
$dropbox->add_value('sender',$ml_email_is);
$dropbox->add_value('subject',$ml_subject_is);
$dropbox->add_value('to',$ml_to_is);
$dropbox->add_value('cc',$ml_cc_is);
$dropbox->print_dropbox('field',$field);
?>
</td>
<td><input type="text" name="keyword" size="37" class="textbox" value="<?php echo $keyword; ?>" /></td></tr>
<tr><td colspan="2"><?php echo $ml_move_to; ?></td></tr>
<tr><td>
<?php
$dropbox=new dropbox();
$dropbox->add_value('',$ml_choose_action.'&nbsp;&nbsp;&nbsp;');
$email->get_subscribed($id);
while ($email->next_record())
{
  if (!($email->f('attributes')&LATT_NOSELECT))
  {
    $dropbox->add_value($email->f('name'), str_replace('INBOX'.$email->f('delimiter'), '', $email->f('name')));
  }
}
$dropbox->print_dropbox('folder',$folder).'</td></tr>';

$checkbox = new checkbox('mark_as_read','mark_as_read','1', $ml_mark_as_read, $mark_as_read);

$row = new table_row();
$cell = new table_cell($checkbox->get_html());
$cell->set_attribute('colspan','2');
$row->add_cell($cell);
echo $row->get_html();

?>
<tr><td colspan="2"><br />
<?php
if ($filter_id > 0)
{
	$button = new button($cmdOk, 'javascript:update_filter()');
	echo $button->get_html();
}else
{
	$button = new button($cmdOk, 'javascript:save_filter()');
	echo $button->get_html();
}
$button = new button($cmdCancel,'javascript:document.location=\''.htmlspecialchars($return_to).'\'');
echo $button->get_html();
echo '</td></tr></table>';

if (isset($feedback)) echo $feedback;
?>

<script type="text/javascript">
  function save_filter()
  {
    document.forms[0].task.value='save_filter';
    document.forms[0].submit();
  }

  function update_filter()
  {
    document.forms[0].task.value='update_filter';
	document.forms[0].submit();
  }
</script>
</form>
<?php
$tabtable->print_foot();
require_once($GO_THEME->theme_path.'footer.inc');
