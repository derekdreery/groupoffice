<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.23 $ $Date: 2006/11/27 09:59:49 $

 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */


require_once("../../Group-Office.php");

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('email');

load_basic_controls();
load_control('datatable');
load_control('tooltip');

require_once($GO_CONFIG->class_path."mail/imap.class.inc");
require_once($GO_MODULES->class_path."email.class.inc");
require_once($GO_LANGUAGE->get_language_file('email'));
$mail = new imap();
$email = new email();

$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';

$em_settings = $email->get_settings($GO_SECURITY->user_id);

switch($task)
{
		case 'delete':
		// Rebuilding the attachments array with only the files the user wants to keep
		$tmp_array = array();
	
		for ($i=$j=0;$i<count($_SESSION['attach_array']);$i++)
		{
			if ($i != $_POST['delete_attachment_id'])
			{
				$tmp_array[$j]['file_name'] = $_SESSION['attach_array'][$i]['file_name'];
				$tmp_array[$j]['tmp_file'] = $_SESSION['attach_array'][$i]['tmp_file'];
				$tmp_array[$j]['file_size'] = $_SESSION['attach_array'][$i]['file_size'];
				$tmp_array[$j]['file_mime'] = $_SESSION['attach_array'][$i]['file_mime'];
				$tmp_array[$j]['content_id'] = $_SESSION['attach_array'][$i]['content_id'];
				$tmp_array[$j]['disposition'] = $_SESSION['attach_array'][$i]['disposition'];
				$j++;
			}else
			{
				@unlink($_SESSION['attach_array'][$i]['tmp_file']);
			}
		}

		// Removing the attachments array from the current session
		$_SESSION['attach_array'] = $tmp_array;
		break;
		
		case 'add':
		//Adding the new file to the array
		for ($n = 0; $n < count($_FILES['file']['tmp_name']); $n ++) 
		{
			if (is_uploaded_file($_FILES['file']['tmp_name'][$n]))
			{
				$attachments_size = 0;
				// Counting the attachments number in the array
				if (isset($_SESSION['attach_array']))
				{
					for($i=1;$i<count($_SESSION['attach_array']);$i++)
					{
						$attachments_size += $_SESSION['attach_array'][$i]['file_size'];
					}
				}
				$attachments_size += $_FILES['file']['size'][$n];
				if ($attachments_size < $GO_CONFIG->max_attachment_size)
				{
					$tmp_file = $GO_CONFIG->tmpdir.md5(uniqid(time()));
					move_uploaded_file($_FILES['file']['tmp_name'][$n], $tmp_file);
					$email->register_attachment($tmp_file, $_FILES['file']['name'][$n], $_FILES['file']['size'][$n], $_FILES['file']['type'][$n]);
				}else
				{
					$feedback =  '<p class="Error">1'.$ml_file_too_big.
						format_size($GO_CONFIG->max_attachment_size).' ('.
						number_format($GO_CONFIG->max_attachment_size, 0, 
						$_SESSION['GO_SESSION']['decimal_seperator'], 
						$_SESSION['GO_SESSION']['thousands_seperator']).' bytes)</p>';
					$task = 'local_files';
				}
			}
		}
		break;
}



$form = new form('attachments_form');
if($task != 'local_files')
{	
	$form->add_html_element(new html_element('h1', $ml_attachments));
	$form->add_html_element(new input('hidden','task','viewing', false));
	$form->add_html_element(new input('hidden','delete_attachment_id'));
	
	$menu = new button_menu();
	$menu->add_button('ml_local_files', $ml_local_files, $_SERVER['PHP_SELF'].'?task=local_files');
	if (isset($GO_MODULES->modules['filesystem']) && $GO_MODULES->modules['filesystem']['read_permission'])
	{
		$menu->add_button('filesystem', $ml_online_files, "javascript:popup('select_file.php','800','500');");
	}

	$form->add_html_element($menu);
	
	$tooltip = '';
	$totalsize = 0;
	$count = 0;
	
	$datatable = new datatable('attachments_table');
	$datatable->set_attribute('style','width:100%');
	
	$datatable->add_column(new table_heading($strName));
	$datatable->add_column(new table_heading($ml_type));
	$datatable->add_column(new table_heading($ml_size));
	$datatable->add_column(new table_heading('&nbsp;'));
	
			
	if (isset($_SESSION['attach_array']) && count($_SESSION['attach_array']) > 0)
	{  
	  for ($i=0;$i<count($_SESSION['attach_array']);$i++)
	  {
	    if ($_SESSION['attach_array'][$i]['disposition'] == 'attachment')
	    {
	      $count++;
	      $totalsize += $_SESSION['attach_array'][$i]['file_size'];
	      
	      $row = new table_row();
	      
	      $extension = get_extension($_SESSION['attach_array'][$i]['file_name']);	      
	      $img = new image('', get_filetype_image($extension));
	      $img->set_attribute('style','width:16px;height:16px;border:0px;margin-right:5px;');
	      $img->set_attribute('align','absmiddle');
	      
	      $row->add_cell(new table_cell($img->get_html().$_SESSION['attach_array'][$i]['file_name']));
	      $row->add_cell(new table_cell(get_filetype_description($extension)));
	      $row->add_cell(new table_cell(format_size($_SESSION['attach_array'][$i]['file_size'])));   
	      
 	      $div = new html_element('div',$img->get_html().$_SESSION['attach_array'][$i]['file_name']);	      
	      $tooltip .= $div->get_html();	      
	      
	      $img = new image('delete');
	      $img->set_attribute('style','width:16px;height:16px;border:0px;margin-right:5px;');
	      $img->set_attribute('align','absmiddle');
	            
	      $hyperlink = new hyperlink("javascript:delete_attachment($i,'".htmlspecialchars($strDeletePrefix."\'".$_SESSION['attach_array'][$i]['file_name']."\'".$strDeleteSuffix,ENT_QUOTES)."');", $img->get_html(), htmlspecialchars($strDeleteItem." '".$_SESSION['attach_array'][$i]['file_name']."'"));	      
	      $row->add_cell(new table_cell($hyperlink->get_html()));
	      $datatable->add_row($row);
	      		
	    }
	  }
	}
	if ($count > 0)
	{
		$cell = new table_cell($ml_total_size.' : '.format_size($totalsize));
		$cell->set_attribute('colspan','99');
		$cell->set_attribute('class','small');		
		$row = new table_row();
		$row->add_cell($cell);
		$datatable->add_footer($row);
	}else
	{
		$cell = new table_cell($ml_no_attachments);
		$cell->set_attribute('colspan','99');
		$row = new table_row();
		$row->add_cell($cell);
		$datatable->add_row($row);
	}
	
	$form->add_html_element($datatable);
	
	$text = '';
	if($tooltip != '')
	{
		$hyperlink = new hyperlink('javascript:open_attachments();','');
		$hyperlink->set_tooltip(new tooltip($tooltip, $ml_attachments));
	}else
	{
		$hyperlink = new html_element('span');
	}
	
	if($count == 1)
	{
		$hyperlink->innerHTML .=  '1 '.$ml_attachment_added;
	}else
	{
		$hyperlink->innerHTML .=  $count.' '.$ml_attachments_added;
	}	
	$GO_HEADER['head'] = '<script type="text/javascript">
	var attachTextDiv = opener.document.getElementById("attachments_text");
	attachTextDiv.innerHTML="'.addslashes($hyperlink->get_html()).'";</script>';
}else
{
	$GO_HEADER['head'] = '<script type="text/javascript" language="javascript" src="'.$GO_CONFIG->host.'javascript/multifile.js"></script>';
	$GO_HEADER['head'] .= '<style>.deleteButton{background-image:url(\''.$GO_THEME->images['delete'].'\');width:16px;height:16px;cursor:default;display:inline;background-repeat:no-repeat;margin-left:5px;</style>';
	
	$form->set_attribute('enctype','multipart/form-data');
	$form->add_html_element(new input('hidden','task'));
	
	$form->add_html_element(new html_element('h1', $ml_local_files));
	
	if (isset($feedback))
	{
	  $p = new html_element('p', $feedback);
	  $p->set_attribute('class','Error');
	  $form->add_html_element($p);
	}

	$table = new table();
	$row = new table_row();
	
	$input = new input('file','file[]');
	$input->set_attribute('id', 'file_element');
	$input->set_attribute('size','50');
	
	
	$br = new html_element('br');
	$cell = new table_cell($ml_select.':'.$br->get_html().$input->get_html());
	if($GO_CONFIG->use_jupload)
	{
		$cell->add_html_element(new button($ml_multiple_files , 'javascript:openPopup(\'upload\',\''.
			$GO_CONFIG->control_url.'JUpload/jupload.php?post_url='.
			urlencode($GO_MODULES->full_url.'upload.php?sid='.session_id()).'&onunload=opener.cancel()\',\'640\',\'400\');', '120'));
	}
	
	$row->add_cell($cell);
	$table->add_row($row);
	
	$row = new table_row();
	$row->add_cell(new table_cell($ml_max_attachment_size.": ".format_size($GO_CONFIG->max_attachment_size).
			" (".number_format($GO_CONFIG->max_attachment_size, 0,
				 $_SESSION['GO_SESSION']['decimal_seperator'], 
				 $_SESSION['GO_SESSION']['thousands_seperator'])." bytes)"));
	$table->add_row($row);
	
	$row = new table_row();
	$cell = new table_cell();
	$cell->set_attribute('id', 'status');
	$row->add_cell($cell);
	$table->add_row($row);
	
	$form->add_html_element($table);
	
	
	
	$form->add_html_element(new button($cmdOk, "javascript:upload()"));
	$form->add_html_element(new button($cmdCancel, "javascript:cancel();"));
	
	$div = new html_element('div', ' ');
	$div->set_attribute('id', 'files_list');
	$div->set_attribute('style', 'margin-top:10px;');
	$form->add_html_element($div);
	
	$form->innerHTML .= "<script>
	<!-- Create an instance of the multiSelector class, pass it the output target and the max number of files -->
	var multi_selector = new MultiSelector( document.getElementById( 'files_list' ), 0 );
	<!-- Pass in the file element -->
	multi_selector.addElement( document.getElementById( 'file_element' ) );
	</script>";
}
require_once($GO_THEME->theme_path."header.inc");
echo $form->get_html();
?>
<script type="text/javascript" language="javascript">
function delete_attachment(attachment_id, message)
{
	if (confirm(message))
	{
		document.forms[0].task.value='delete';
		document.forms[0].delete_attachment_id.value = attachment_id;
		document.forms[0].submit();
	}
}
function cancel()
{
	document.location='<?php echo $_SERVER['PHP_SELF']; ?>';
}
function upload()
{
	var status = null;
	if (status = get_object("status"))
	{
		status.innerHTML = "<?php echo $ml_please_wait; ?>";
	}
	document.attachments_form.task.value='add';
	document.forms[0].submit();
}


</script>
<?php
require_once($GO_THEME->theme_path."footer.inc");
?>
