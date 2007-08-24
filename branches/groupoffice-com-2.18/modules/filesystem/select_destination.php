<?php
require('../../Group-Office.php');

load_basic_controls();

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('filesystem');
require_once ($GO_LANGUAGE->get_language_file('filesystem'));

//var_dump($_POST);

$GO_CONFIG->set_help_url($fs_help_url);

$return_to_path = isset($_REQUEST['return_to_path']) ? smart_stripslashes($_REQUEST['return_to_path']) : '';
$task = isset($_REQUEST['task']) ? smart_stripslashes($_REQUEST['task']) : '';
$return_to = $GO_MODULES->url.'index.php?path='.urlencode($return_to_path);
if(isset($_POST['fs_list']['selected']))
{
	$selected = $_POST['fs_list']['selected'];
}else
{
	$selected = $_POST['selected'];
}

$path = isset($_REQUEST['path']) ? smart_stripslashes($_REQUEST['path']) : $return_to_path;

require($GO_MODULES->modules['filesystem']['class_path'].'filesystem_treeview.class.inc');
$ftv = new filesystem_treeview('id', $path);

$fs = new filesystem();

if($ftv->change_folder || isset($_POST['overwrite']))
{
	$overwrite_destination_path = isset ($_POST['overwrite_destination_path']) ? smart_stripslashes($_POST['overwrite_destination_path']) : '';
	$overwrite_all = (isset($_POST['overwrite_all']) && $_POST['overwrite_all'] == 'true') ? true : false;
	$overwrite = (isset($_POST['overwrite']) && $_POST['overwrite']=='true') ? true : $overwrite_all;
	
	//folder was clicked so we are going to move/copy files
	while($selected_path = array_shift($selected))
	{
		$destination = $ftv->path.'/'.basename($selected_path);
		

			//echo $ftv->path.'<br />';
			//echo $selected_path.'<br />';
			if(
				($task == 'copy' && 
					(!$fs->has_read_permission($GO_SECURITY->user_id, $selected_path) || 
					!$fs->has_write_permission($GO_SECURITY->user_id, $ftv->path)))
			||
				($task == 'cut' && 
					(!$fs->has_write_permission($GO_SECURITY->user_id, $selected_path) || 
					!$fs->has_write_permission($GO_SECURITY->user_id, $ftv->path)))
				)
			{
				$feedback = $strAccessDenied;
			}elseif($overwrite_destination_path==$destination && !$overwrite)
			{
				//do nothing
			}elseif(file_exists($destination) && !$overwrite_all && $overwrite_destination_path!=$destination)
			{
				$overwrite_destination_path=$destination;
				$confirm_overwrite = true;
				array_unshift($selected, $selected_path);
				break;				
			}else
			{
				//echo 'Copy '.$selected_path.' -> '.$destination.'<br />';
				
				if($task=='copy')
				{
					$result=$fs->copy($selected_path, $destination);
				}else
				{
					$result=$fs->move($selected_path, $destination);
				}
				if(!$result)
				{
					if($fs->action_result=='recursion')
					{
						$feedback = $fs_recursion;
					}else
					{
						$feedback = $fs_inssufficient_diskspace;
					}
					
					array_unshift($selected, $selected_path);
					break;
				}
			}
		
	}
	
	if(!isset($confirm_overwrite) && !isset($feedback))
	{
		header('Location: '.$return_to);
		exit();
	}
}






$form = new form('select_destination_form');
$form->add_html_element(new input('hidden', 'return_to_path', $return_to_path));
$form->add_html_element(new input('hidden', 'task', $task));


foreach($selected as $selected_path)
{
	$form->add_html_element(new input('hidden', 'selected[]', smart_stripslashes($selected_path)));	
}

if(isset($confirm_overwrite))
{
	require('overwrite.inc');
	
}else
{

	
	$h1 = new html_element('h1', $fs_select_destination);
	$form->add_html_element($h1);
	
	if(isset($feedback))
	{
		$p = new html_element('p', $feedback);
		$p->set_attribute('class','Error');
		$form->add_html_element($p);
		
	}
	
	$form->add_html_element($ftv);
	
	$button = new button($cmdCancel, "javascript:document.location='$return_to';");
	$form->add_html_element($button);
}
require($GO_THEME->theme_path.'header.inc');
echo $form->get_html();
require($GO_THEME->theme_path.'footer.inc');