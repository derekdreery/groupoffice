<?php
require("../../Group-Office.php");
$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('forum');
require($GO_LANGUAGE->get_language_file('forum'));
if($GO_MODULES->write_permissions){}

require($GO_MODULES->path.'classes/forum.class.inc');
$forum = new forum();
$button = new button();

$forum_id = isset($_GET['forum_id']) ? $_GET['forum_id'] : 0;
$msg_id = isset($_GET['msg_id']) ? $_GET['msg_id'] : 0;
$task = isset($_GET['task']) ? $_GET['task'] : '';
$user_id = $GO_SECURITY->user_id;
$output ='';
echo $task;
echo $msg_id;
switch( $task )
{
	case 'getNew':
	
	break;
	
	case 'asRead':
		if ($msg_id>0)
		{
			$forum->user_saw_message($user_id,$msg_id);
		}
	break;
	
	case 'delete':
		$msg = $forum->get_message($msg_id);
		$frm = $forum->get_forum($msg['forum_id']);
		if ( $msg['user_id'] == $GO_SECURITY->user_id || $frm['owner_id'] == $GO_SECURITY->user_id || $GO_SECURITY->has_admin_permission($GO_SECURITY->user_id) )
		{
			if ($forum->delete_message($msg_id,true))
			{
				echo '<script language="javascript">window.parent.killdiv('.$msg_id.');</script>';
			}
		}
	break;
	
	case 'close_topic':
		if ($forum->close_topic($msg_id))
		{
			echo '<script language="javascript">window.parent.refresh();</script>';
		}
	break;
	
	case 'reopen_topic':
		if ($forum->reopen_topic($msg_id))
		{
			echo '<script language="javascript">window.parent.refresh();</script>';
		}
	break;
}
?>
