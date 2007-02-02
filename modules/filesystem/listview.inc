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



$urlencoded_path = urlencode($fv->path);


$form->add_html_element(new html_element('h1', $fbPath.': '.str_replace($GO_CONFIG->file_storage_path.'users','', $fv->path)));

if (isset($feedback))
{
  $p = new html_element('p', $feedback);
  $p->set_attribute('class','Error');
  $form->add_html_element($p);
}

$menu = new button_menu();

if ($parent_path = dirname($fv->path)) {
	if ($fs->has_read_permission($GO_SECURITY->user_id, $parent_path)) {
		$menu->add_button('uplvl_big', $fbUpLevel, $fv->fsl->get_folder_handler($parent_path));		
	}
}
$menu->add_button('properties', $fbProperties, "javascript:properties('".htmlspecialchars(addslashes($fs_no_multi_select))."');");		



if ($write_permission) {
	$menu->add_button('new_folder', $fbNewFolder, $_SERVER['PHP_SELF'].'?task=new_folder&path='.$urlencoded_path);
	$menu->add_button('upload', $fbUpload, $_SERVER['PHP_SELF'].'?task=upload&path='.$urlencoded_path);
	$menu->add_button('delete_big', $fbDelete, $fv->fsl->get_delete_handler());
}
$menu->add_button('cut', $fbCut, "javascript:cut_items('".htmlspecialchars(addslashes($fbNoSelect))."');");
$menu->add_button('copy', $fbCopy, "javascript:copy_items('".htmlspecialchars(addslashes($fbNoSelect))."');");

if ($write_permission) {
	if ((count($_SESSION['cut_files']) + count($_SESSION['copy_files'])) > 0) {
		$menu->add_button('paste', $fbPaste, "javascript:paste_items();");
	}
	$menu->add_button('new_doc', $fs_new_doc, 'edit.php?path='.$urlencoded_path);
}

if ($GO_CONFIG->window_mode == 'normal') {
	if (isset($GO_MODULES->modules['email']) && $GO_MODULES->modules['email']['read_permission']) {
		$menu->add_button('fs_email', $lang_modules['email'],"javascript:mail_files('".htmlentities(addslashes($fbNoSelect),ENT_QUOTES)."')");
	}
//	$menu->add_button('sharing', $fbSharing,$_SERVER['PHP_SELF'].'?task=shares&path='.$urlencoded_path);	
	$menu->add_button('fs_search', $fs_search,$_SERVER['PHP_SELF'].'?task=search&path='.$urlencoded_path);

	if ($write_permission && 
	(is_executable($GO_CONFIG->cmd_zip) || is_executable($GO_CONFIG->cmd_tar))
	) {
		$menu->add_button('compress', $fb_compress,'javascript:create_archive();');
		$menu->add_button('decompress', $fb_decompress,'javascript:extract();');
	}
}
$form->add_html_element($menu);
if ($GO_CONFIG->window_mode == 'popup') {
	
	$table = new table();
	if ($mode == 'save') {
		$row = new table_row();
		$row->add_cell(new table_cell($fbFilename.':*'));
		$input = new input('text', 'filename', $filename);
		$input->set_attribute('size','40');
		$row->add_cell(new table_cell($input->get_html()));
		$table->add_row($row);
	}
	$form->add_html_element($table);
	
	
	/*
	$location = isset ($_POST['location']) ? $_POST['location'] : $home_path;
	$select = new select('location', $fv->fsl->path);
	$select->set_attribute('onchange', $fv->fsl->get_folder_handler());
	
	$select->add_value($home_path, $fs_personal);
	

	$fs2 = new filesystem();
	$fs2->get_authorized_sharers($GO_SECURITY->user_id);

	while ($fs2->next_record()) {
		$shares = array ();
		$share_count = $fs->get_shares($fs2->f('user_id'));
		while ($fs->next_record()) {
			if ($GO_SECURITY->has_permission($GO_SECURITY->user_id, $fs->f('acl_read')) || 
				$GO_SECURITY->has_permission($GO_SECURITY->user_id, $fs->f('acl_write'))) {
				if (strpos($fv->path, $fs->f('path')) !== false) {
					$location = $fs->f('path');
				}
				$shares[] = $fs->f('path');
			}
		}
		$share_count = count($shares);
		if ($share_count > 0) {
			if ($user = $GO_USERS->get_user($fs2->f('user_id'))) {
				
				$middle_name = $GO_USERS->f('middle_name') == '' ? '' : $GO_USERS->f('middle_name').' ';
				$user['name'] = $GO_USERS->f('first_name').' '.$middle_name.$GO_USERS->f('last_name');
				
				$select->add_optgroup($user['name']);
				for ($i = 0; $i < $share_count; $i ++) {
					$select->add_value($shares[$i], basename($shares[$i]));
				}
			}
		}
	}
	$row = new table_row();
	$row->add_cell(new table_cell($strLookIn.':'));
	$row->add_cell(new table_cell($select->get_html()));
	$table->add_row($row);
	*/
}

$form->add_html_element($fv);

if ($GO_CONFIG->window_mode == 'popup') {
	$div = new html_element('div');
	$div->set_attribute('style', 'text-align:center');

	$div->add_html_element(new button($cmdOk, 'javascript:submit_to_handler()'));
	$div->add_html_element(new button($cmdCancel, 'javascript:window.close()'));
	$form->add_html_element($div);
}

?>

<script type="text/javascript" language="javascript">


function submit_to_handler()
{
  <?php


if ($GO_MULTI_SELECT == false) {
?>
	var count = 0;
  var path = new String;

  for (var i=0;i<document.forms[0].elements.length;i++)
  {
			if(document.forms[0].elements[i].type == 'checkbox' && document.forms[0].elements[i].name != 'dummy')
			{
			  if (document.forms[0].elements[i].checked == true)
			  {
			    document.forms[0].path.value = document.forms[0].elements[i].value;
			    count++;
			  }
			}
  }
  if (count > 1)
  {
		alert("<?php echo $fs_no_multi_select; ?>");
		return false;
  }
  <?php


}
?>
    document.forms[0].task.value = 'GO_HANDLER';
  document.forms[0].action = "<?php echo $GO_HANDLER; ?>";
  document.forms[0].submit();
}

function create_archive()
{
  var count = 0;

  for (var i=0;i<document.forms[0].elements.length;i++)
  {
    if(document.forms[0].elements[i].type == 'checkbox' && document.forms[0].elements[i].name != 'dummy')
    {
      if (document.forms[0].elements[i].checked == true)
      {
	count++;
      }
    }
  }
  if (count == 0)
  {
    alert("<?php echo $fbNoSelect; ?>");
  }else
  {
    document.forms[0].task.value = 'create_archive';
    document.forms[0].submit();
  }
}

function extract()
{
  var count = 0;

  for (var i=0;i<document.forms[0].elements.length;i++)
  {
    if(document.forms[0].elements[i].type == 'checkbox' && document.forms[0].elements[i].name != 'dummy')
    {
      if (document.forms[0].elements[i].checked == true)
      {
	count++;
      }
    }
  }
  if (count == 0)
  {
    alert("<?php echo $fbNoSelect; ?>");
  }else
  {
    document.forms[0].task.value = 'extract';
    document.forms[0].submit();
  }
}
</script>
