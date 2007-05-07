<?php

require("../../Group-Office.php");
$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('forum');
require($GO_LANGUAGE->get_language_file('forum'));

require($GO_MODULES->class_path.'forum.class.inc');
$forum = new forum();
$button = new button();

$forum_id = isset($_REQUEST['forum_id']) ? $_REQUEST['forum_id'] : 0;
$message_id = isset($_REQUEST['message_id']) ? $_REQUEST['message_id'] : 0;
$reply_to = isset($_REQUEST['reply_to']) ? $_REQUEST['reply_to'] : 0;
$edit = isset($_REQUEST['edit']) ? $_REQUEST['edit'] : 0;

$body = isset($_REQUEST['body']) ? $_REQUEST['body'] : '';
$title = isset($_REQUEST['title']) ? $_REQUEST['title'] : '';
$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';
$xid = isset($_REQUEST['xid']) ? $_REQUEST['xid'] : 0;

$cf1_body = isset($_REQUEST['cf1_body']) ? $_REQUEST['cf1_body'] : '';
$cf2_body = isset($_REQUEST['cf2_body']) ? $_REQUEST['cf2_body'] : '';
$cf3_body = isset($_REQUEST['cf3_body']) ? $_REQUEST['cf3_body'] : '';


$msg ='';
$msg_class='';
if ($reply_to > 0)
{
	$obj_rep = $forum->get_message($reply_to);
	$post_title = $frm_reply_to.' "'.$obj_rep['title'].'" ';
}else
{
	$post_title = $frm_post_new_message;
}

//$GO_HEADER['body_arguments'] = 'onLoad=opener.location=\'forum_view.php?id='.$forum_id.'\';';

$button = new button();
$btn_save = $button->get_button($cmdSave,"save();");
$btn_close = $button->get_button($cmdClose,"window.close();opener.focus();");


switch($task)
{
	case 'save';
		
		$body = mysql_escape_string($body);
		$title = mysql_escape_string($title);
		
		if ( $title == '' || $body == '' )
		{
			$msg_class = 'Error';
			$msg = $frm_fields_missing;
		}else
		if ( $edit > 0 )
		{
			if ( $forum->update_message($edit,$title,$body,$cf1_body,$cf2_body,$cf3_body ))
			{
				$rep = $GO_CONFIG->file_storage_path.'forum/'.$forum_id.'/'.$edit.'/';
				
				for ( $i=1;$i<4;$i++)
				{
				if ( $_FILES["userfile$i"]['size'] > 0 )
				{
					$savefile = $rep.$_FILES["userfile$i"]['name'];
					$temp = $_FILES["userfile$i"]['tmp_name'];
					echo $rep;
					if ( move_uploaded_file($temp,$savefile))
					{
						$ext = get_extension($savefile);
						$image = get_filetype_image($ext);
						//echo '<img src="'.$image.'" />';
					}
				
				}
				}
				$GO_HEADER['body_arguments'] = 'onLoad=opener.location=\'forum_view.php?id='.$forum_id.'\';';
				$GO_HEADER['body_arguments'] .= 'opener.focus();window.close();';
			}
			
		}else
		{
			$new_id = $forum->add_message($GO_SECURITY->user_id,$title,$body,$forum_id,$reply_to,$cf1_body,$cf2_body,$cf3_body);
			if ( $new_id > 0 )
			{
				$msg_class = "Success";
				$msg = $frm_msg_posted;
				$rep = $GO_CONFIG->file_storage_path.'forum/'.$forum_id.'/'.$new_id.'/';
				mkdir($rep);
				$GO_HEADER['body_arguments'] = 'onLoad=opener.location=\'forum_view.php?id='.$forum_id.'\';';
				$GO_HEADER['body_arguments'] .= 'opener.focus();window.close();';
	
				
				$f = new forum();
				$f->send_new_message_ad($new_id);
				
				$title = '';
				$body = '';	
				
				for ( $i=1;$i<4;$i++)
				{
				if ( $_FILES["userfile$i"]['size'] > 0 )
				{
					$savefile = $rep.$_FILES["userfile$i"]['name'];
					$temp = $_FILES["userfile$i"]['tmp_name'];
					
					if ( move_uploaded_file($temp,$savefile))
					{
						$ext = get_extension($savefile);
						$image = get_filetype_image($ext);
						//echo '<img src="'.$image.'" />';
					}
				
				}
				
				
			
			}		
			}else
			{
			
			}
		}
		
	break;


}

if ($edit > 0 )
{
	if ( $obj = $forum->get_message($edit) )
	{
		$body = $obj['message'];
		$title = $obj['title'];
		$cf1_body = $obj['custom1'];
		$cf2_body = $obj['custom2'];
		$cf3_body = $obj['custom3'];
		
	}
}

require($GO_THEME->theme_path."header.inc");

$tabtable = new tabtable('post_tab', $post_title, '100%');
$tabtable->print_head();

require('post.inc');

$tabtable->print_foot();

require($GO_THEME->theme_path."footer.inc");
