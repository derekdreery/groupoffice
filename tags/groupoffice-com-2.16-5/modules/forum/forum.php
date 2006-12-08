<?php

require("../../Group-Office.php");
$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('forum');
require($GO_LANGUAGE->get_language_file('forum'));


require($GO_MODULES->path.'classes/forum.class.inc');
$forum = new forum();
$button = new button();

$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
$msg = ''; $msg_class = 'Success';
$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';
$return_to = (isset($_REQUEST['return_to']) && $_REQUEST['return_to'] != '') ? $_REQUEST['return_to'] : $_SERVER['HTTP_REFERER'];
$link_back = (isset($_REQUEST['link_back']) && $_REQUEST['link_back'] != '') ? $_REQUEST['link_back'] : $_SERVER['REQUEST_URI'];
$close = (isset($_REQUEST['close'])) ? $_REQUEST['close'] : 'false';
	
$name = (isset($_REQUEST['name'])) ? $_REQUEST['name'] : '';

$desc = (isset($_REQUEST['desc'])) ? $_REQUEST['desc'] : '';
$drop_type = (isset($_REQUEST['drop_type'])) ? $_REQUEST['drop_type'] : 1;
$mailing_list = (isset($_REQUEST['mailing_list'])) ? $_REQUEST['mailing_list'] : '';

$custom1 = (isset($_REQUEST['custom1'])) ? $_REQUEST['custom1'] : '';
$custom2 = (isset($_REQUEST['custom2'])) ? $_REQUEST['custom2'] : '';
$custom3 = (isset($_REQUEST['custom3'])) ? $_REQUEST['custom3'] : '';

$csv_sep = (isset($_REQUEST['csv_sep'])) ? $_REQUEST['csv_sep'] : ',';
$add_count = 0;$count_str='';

$import_date = isset($_REQUEST['import_date']) ? $_REQUEST['import_date'] : 'all';
$date1 = isset($_REQUEST['date1']) ? $_REQUEST['date1'] : '';
$date2 = isset($_REQUEST['date2']) ? $_REQUEST['date2'] : '';

$date_picker = new date_picker();
$GO_HEADER['head'] = $date_picker->get_header();
			
$checkbox = new checkbox();

$chk_erase = isset($_REQUEST['chk_erase']) ? true : false;

//echo $mailing_list;

$btn = '';

$user_id = $GO_SECURITY->user_id;
	
	switch($task)
	{
		case 'save':
			if ( ($name != '') && ($desc != '') )
			{
				if ( $id == 0 && ($forum->forum_exist($name)==0) )
				{
					
					$acl_read = $GO_SECURITY->get_new_acl('Forum : read',$user_id);
					$acl_write = $GO_SECURITY->get_new_acl('Forum : write',$user_id);
					$new_id = $forum->add_forum($name,$desc,$user_id,$mailing_list,$drop_type,$custom1,$custom2,$custom3);
					if ( $new_id > 0 )
					{
						$id = $new_id;
						$msg_class = 'Success'; $msg = $frm_added;
						
						mkdir($GO_CONFIG->file_storage_path.'forum/'.$id);
						//mkdir('/home/groupoffice/forum')
					}else
					{
						$msg_class = 'Error'; $msg = $frm_not_saved;
					}
					
				}
				else if ( $id > 0 && ($forum->forum_exist($name)==$id || $forum->forum_exist($name)==0) )
				{
					if ( $forum->update_forum($id,$name,$desc,$mailing_list,$drop_type,$custom1,$custom2,$custom3) )
					{
						$msg_class = 'Success'; $msg = $frm_updated;
					}else
					{
						$msg = 'Error'; $msg = $frm_not_saved;
					}
				}
				else
				{
					$new_id = 0;
					$msg_class = 'Error';
					$msg = $frm_already_exists; 
				}
				
			}else
			{
				$msg_class = 'Error';$msg = 'Veuillez saisir tous les champs'; 
			}
		
		break;
		
		case 'new':
		
		break;
		
		case 'load':
		
		break;
		
		case 'delete':
		
		break;
		
		case 'upload':
			//echo $date1.' '.$date2.' ';
			//echo date_to_unixtime($date1);
			//echo $import_date;
			if ( $_FILES['import_file']['size'] > 0 )
			{
				$temp = $_FILES['import_file']['tmp_name'];
				if ( $import_date == 'selected' )
				{
					$add_count = $forum->add_csv_to_forum($temp,$id,$csv_sep,$chk_erase,$date1,$date2);
				}else
				{
					$add_count = $forum->add_csv_to_forum($temp,$id,$csv_sep,$chk_erase);
				}
				
				
			}
			if ( $add_count > 0 )
			{
				if ( $add_count == 1)
				{
					
					$count_str = $add_count.' '.$frm_entry_added;
				}else
				{
					$count_str = $add_count.' '.$frm_entries_added;
				}
				$forum->send_import_mail($id,$add_count,$GO_SECURITY->user_id);
			}
		break;
	}
if (isset($_POST['close']) && $_POST['close'] == 'true')
{
	header('Location: index.php');
	exit();
}
	
if ( $id == 0 )
{
	$str_title = $frm_new_forum;
	$owner_id = 0;
}	
else {
	$obj = $forum->get_forum($id);
	$str_title = 'Forum : '.$obj['name'];
	$name = $obj['name'];
	$drop_type = $obj['ad_type'];
	$desc = $obj['description'];
	$mailing_list = $obj['mailing_list'];
	$owner_id = $obj['owner_id'];
	$custom1 = $obj['custom1'];
	$custom2 = $obj['custom2'];
	$custom3 = $obj['custom3'];
}
$read_only = ( $user_id == $owner_id ) ? false : true;

if ( $id==0 || ( $id >0 && !$read_only ) ) {
	$btn .= $button->get_button($cmdOk,'save();').'&nbsp';
	$btn .= $button->get_button($cmdApply,'apply();').'&nbsp'; 
}
$btn .= $button->get_button($cmdClose, "javascript:document.location='".$GO_MODULES->url."'");
$dp = new dropbox();
$dp->add_value('1',$frm_ad_1);
$dp->add_value('2',$frm_ad_2);
$dp->add_value('3',$frm_ad_3);

$drop_type = $dp->get_dropbox('drop_type',$drop_type);
$tabtable = new tabtable('forum', $str_title, '100%', '400','120','', true);

$tabtable->add_tab('properties', $frm_properties);
if ( $id > 0)
{
	$tabtable->add_tab('acl_read',$frm_read_perm);
	$tabtable->add_tab('acl_write',$frm_write_perm);
	$tabtable->add_tab('import',$frm_import);
}

require($GO_THEME->theme_path."header.inc");

echo '<form name="frm_forum" method="post" action="'.$_SERVER['PHP_SELF'].'" ENCTYPE="multipart/form-data" >';
echo '<input type="hidden" name="id" value="'.$id.'" />';
echo '<input type="hidden" name="task" value="" />';
echo '<input type="hidden" name="close" value="false" />';
echo '<input type="hidden" name="return_to" value="'.htmlspecialchars($return_to).'" />';


$tabtable->print_head();

switch($tabtable->get_active_tab_id())
{
  case 'properties':
	
	$f = new forum();
	$fusers=$f->get_users_from_forum($id);
	foreach($fusers as $value)
	{
		//echo "$value,";
	}
	echo '<br />';
	$uusers=$f->check_unemailed_users($id);
	//echo $f->get_mailing_list_from_array($uusers).'<br />';
	foreach($uusers as $value)
	{
		echo "$value,";
	}
	
	
	require($GO_MODULES->path.'forum.inc');
	
  
  break;
  
  case 'import':
  	require('import.inc');
  break;
  
  case 'acl_read':
  	echo '<p>'.$frm_acl_read.'</p>';
  	print_acl($obj['acl_read'],$read_only);
  break; 
  
  case 'acl_write':
  	echo '<p>'.$frm_acl_write.'</p>';
  	print_acl($obj['acl_write'],$read_only);
  break;
  
 }
$tabtable->print_foot();

//Always require the footer file after you're done outputting to the 
//client and you have included the header.inc file.
echo '</form>';
require($GO_THEME->theme_path."footer.inc");
?>
