<?php

require("../../Group-Office.php");
$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('forum');
require($GO_LANGUAGE->get_language_file('forum'));

require($GO_MODULES->path.'classes/forum.class.inc');

$forum = new forum();
$del_id = isset($_POST['delete_id']) ? $_POST['delete_id'] : 0;
if ( $del_id > 0 )
{
	$forum->delete_forum($del_id);
	$forum->delete_messages_from_forum($del_id);
}

$button = new button();

$btn = '';

$GO_HEADER['head'] = '<link rel="stylesheet" href="forum.css" />';
require($GO_THEME->theme_path."header.inc");


echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'" />';
echo '<input type="hidden" name="delete_id" />';
echo '</form>';

if($GO_MODULES->write_permission)
{
	$btn .= $button->get_button($frm_new_forum,"javascript:document.location='forum.php?task=new'");
	echo $btn.'<br /><br />';
}
if ( $forum->get_authorised_forums($GO_SECURITY->user_id) > 0 )
{
	
	?>
	<table class="go_table" width="100%">
	<tr height="20">
		<th nowrap><?php echo $frm_forum; ?></th>
		<th width="100px" nowrap><?php echo $frm_created_by; ?></th>
		<th width="120px" nowrap><?php echo $frm_ctime; ?></th> 
		<th width="120px" nowrap><?php echo $frm_mtime; ?></th>
		<th width="50px" nowrap><?php echo $frm_subjects_total; ?></th>
		<th width="50px" nowrap><?php echo $frm_messages_total; ?></th>
		<!-- <th width="100px" nowrap><?php echo $frm_unread_messages_total; ?></th>
		<th width="100px" nowrap><?php echo $frm_unread_subjects_total; ?></th>
		!-->
		<th width="25px;" nowrap>&nbsp;</th>
	</tr>
	<?php
	
	while ( $forum->next_record() )
	{
		$obj = $forum->Record;
		$owner= $GO_USERS->get_user($obj['owner_id']);// forum cell printing
		$datec = date($_SESSION['GO_SESSION']['date_format'].' G:i', $obj['ctime']+get_timezone_offset()*3600);
		
		$last = $forum->get_last_message($obj['id']);
		$datem = date($_SESSION['GO_SESSION']['date_format'].' G:i', $last['ctime']+get_timezone_offset()*3600);
		
		
		
		$mt = $forum->get_messages_total($obj['id']);
		$st = $forum->get_subjects_total($obj['id']);
		$mu = $mt - $forum->get_read_count($GO_SECURITY->user_id,$obj['id']);
		$su = $st - $forum->get_read_count($GO_SECURITY->user_id,$obj['id'],true);
		$str_mu = '';
		if ( $mu > 0 )
		{
			$str_mu = '&nbsp<span class="unread_msg">('.$mu.')</span>';
		}
		$str_su = '';
		if ( $su > 0 )
		{
			$str_su = '&nbsp<span class="unread_msg">('.$su.')</span>';
		}
		
		$title_class = 'normal';
		if ( $su > 0 || $mu > 0 )
		{
			$title_class='highlight';
		}
		echo '<tr height="25">';
		echo '<td nowrap><a class="'.$title_class.'" title="'.$obj['description'].'" href="forum_view.php?id='.$obj['id'].'">'.$obj['name'].'</a></td>';
		echo '<td nowrap>'.$owner['first_name'].' '.$owner['last_name'].'</td>';
		echo '<td nowrap>'.$datec.'</td>';
		echo '<td nowrap>'.$datem.'</td>';
		echo '<td nowrap style="text-align:center;" >'.$st.$str_su.'</td>';
		echo '<td nowrap style="text-align:center;" >'.$mt.$str_mu.'</td>';
		/*echo '<td nowrap style="text-align:center;" >'.$mu.'</td>';
		echo '<td nowrap style="text-align:center;" >'.$su.'</td>'*/;
		
		/*$umt = $mt - $forum->get_read_messages_total($GO_SECURITY->user_id,$obj['id']);
		$ust = $st - $forum->get_read_subjects_total($GO_SECURITY->user_id,$obj['id']);
		echo '<td nowrap style="text-align:center;" >'.$umt.'</td>';
		echo '<td nowrap style="text-align:center;" >'.$ust.'</td>';*/
		
		
		
		if ( $obj['owner_id'] == $GO_SECURITY->user_id )
		{
			echo '<td><a href="forum.php?id='.$obj['id'].'" ><img src="'.$GO_THEME->images['edit'].'" border="0" /></a>&nbsp;';
			echo '<a href="javascript:confirm_delete(\''.$obj['name'].'\',\''.$obj['id'].'\');" ><img src="'.$GO_THEME->images['delete'].'" border="0"/></a></td>';
		}
		echo '</tr>';
	}
	echo '</table>';

}else
{
	echo '<p>'.$frm_no_forum.'</p>';
}

require($GO_THEME->theme_path."footer.inc");

?>

<script language="javascript">
	function confirm_delete(name,id)
	{
		if (confirm("<?php echo $frm_confirm_delete; ?> '" + name + "' ?"))
      	{
			document.forms[0].delete_id.value=id;
			document.forms[0].submit();
	   	}
	}
</script>
