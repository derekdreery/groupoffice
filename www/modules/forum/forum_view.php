<?php
require("../../Group-Office.php");
$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('forum');
require($GO_LANGUAGE->get_language_file('forum'));

require($GO_MODULES->path.'classes/forum.class.inc');
$forum = new forum();
$button = new button();


$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;

if ( $id > 0 )
{
	$obj = $forum->get_forum($id);
}else
{
	header('Location: index.php');
}

if ( !$GO_SECURITY->has_permission($GO_SECURITY->user_id,$obj['acl_read']) && !$GO_SECURITY->has_permission($GO_SECURITY->user_id,$obj['acl_write']) )
{
	header('Location: '.$GO_CONFIG->host.'error_docs/401.php');
	exit();
}
$message_id = isset($_REQUEST['message_id']) ? $_REQUEST['message_id'] : 0;
$body = isset($_REQUEST['body']) ? $_REQUEST['body'] : '';
$title = isset($_REQUEST['title']) ? $_REQUEST['title'] : '';
$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';
$xid = isset($_REQUEST['xid']) ? $_REQUEST['xid'] : 0;
$drop_display = isset($_REQUEST['drop_display']) ? $_REQUEST['drop_display'] : 0;
$drop_sort = isset($_REQUEST['drop_sort']) ? $_REQUEST['drop_sort'] : 'm.ctime';
$drop_order = isset($_REQUEST['drop_order']) ? $_REQUEST['drop_order'] : 'DESC';

if ( $drop_display == 0 )
{
	// have to load settings
	$settings = $forum->get_settings($GO_SECURITY->user_id,$id);
	if ( !$settings )
	{
		$forum->add_settings($GO_SECURITY->user_id,$id);
		$settings = $forum->get_settings($GO_SECURITY->user_id,$id);
	}
	$drop_display = $settings['display'];
	$drop_sort = $settings['sort'];
	$drop_order = $settings['order'];
	
	
}else
{
	$forum->set_settings($GO_SECURITY->user_id,$id,$drop_display,$drop_sort,$drop_order);
	// have to save...

}


switch($task)
{
	case 'expand':
		$forum->user_saw_message($GO_SECURITY->user_id,$xid);
		if ($xid>0)
		{
			$_SESSION['expanded_forums'][$xid]=1;
		}
		
	break;
	
	case 'unexpand':
		if($xid > 0)
		{
			if ( isset($_SESSION['expanded_forums'][$xid] ) )
			{
				unset($_SESSION['expanded_forums'][$xid] );
			}
		}
	break;
	
	case 'change_forum_sort':
		if (isset($_SESSION['forum_sort']))
				{
					unset($_SESSION['forum_sort']);
				}else
				{
					$_SESSION['forum_sort'] = 1;
				}
	break;
	
	case 'empty_forum':
		$forum->delete_messages_from_forum($id);
	break;
	
}

if ( $xid > 0 )
{
	$GO_HEADER['body_arguments'] = 'onload="document.location=\'#'.$xid.'\'"';
}



$loc = 'index.php';
if ( $message_id > 0 )
{
	$forum->user_saw_message($GO_SECURITY->user_id,$message_id);
	$post_title = $frm_post_answer;
	$msg_obj= $forum->get_message($message_id);
	$loc = "forum_view.php?id=$id";
	$title = 'Re: '.$msg_obj['title'];
} 
else $post_title = $frm_post_new_message;

$btn_save = $button->get_button($cmdSave,"save();");

$btn_close = $button->get_button($cmdClose,"document.location='$loc';");
$btn_post_up = $button->get_button($post_title,"popup('post.php?forum_id=$id',500,350)");
$btn_post_down = $button->get_button($post_title,"save();");
$btn_expand_all = $button->get_button($frm_expand_all,"expand_all();");
$btn_collapse_all = $button->get_button($frm_collapse_all,"collapse_all();");


$msg='';$msg_class='Success';

$GO_HEADER['head'] = '<link rel="stylesheet" href="forum.css" />';


require($GO_THEME->theme_path."header.inc");

$obj = $forum->get_forum($id);
$titulu = '<a href="javascript:change_forum_sort();" >'.$obj['name'].'</a>';
$tabtable = new tabtable('fview', $obj['name'], '100%', '400','120','', true);
$tabtable->print_head();
?>
<form name="frm_view" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" ENCTYPE="multipart/form-data">
<?php


$dp = new dropbox();
$dp->add_value('1',$frm_display_1);
$dp->add_value('2',$frm_display_2);
$dd = $drop_display;
$drop_display = $dp->get_dropbox('drop_display',$drop_display,'onChange="javascript:refresh();"');

$ds = $drop_sort;
if ( $dd == 2 )
{
$dp = new dropbox();
$dp->add_value('m.title',$frm_sort_1);
$dp->add_value('m.ctime',$frm_sort_2);
$dp->add_value('u.first_name',$frm_sort_3);
$dp->add_value('u.last_name',$frm_sort_4);
for ($i = 1;$i<=3;$i++)
{
	if ( $obj["custom$i"] != "" )
	{
		$dp->add_value("m.custom$i",$obj["custom$i"]);
	}
}
$ds = $drop_sort;
$drop_sort = $dp->get_dropbox('drop_sort',$drop_sort,'onChange="javascript:refresh();"');

$dp = new dropbox();
$dp->add_value('ASC',$frm_ASC);
$dp->add_value('DESC',$frm_DESC);
$do = $drop_order;
$drop_order = $dp->get_dropbox('drop_order',$drop_order,'onChange="javascript:refresh();"');

}

$msg_ids = $forum->get_msg_ids($id);


?>
<div class="impair" style="float:right;margin-top:-16px;" >
<table>
	<tr>
		<td><?php echo $frm_display_type; ?></td><td><?php echo $drop_display;?></td>
	</tr>
	<?php if ( $dd == 2 ) {
		echo "<tr><td>$frm_display_sort</td><td>$drop_sort</td></tr>";
		echo "<tr><td>$frm_order</td><td>$drop_order</td></tr>";
		 } ?>
</table>
</div>

<?php
echo '<p>'.$obj['description'].'</p>';
//echo '<p class="title"><span class="name">'..'</span><br /><span class="desc">'.$obj['description'].'</span></p>';
$forum->get_messages($id);
if ( $forum->user_can_post($GO_SECURITY->user_id,$id)) echo $btn_post_up.' ';
echo $btn_close.' '.$btn_expand_all.' '.$btn_collapse_all.' ';
$the_forum = $forum->get_forum($id);
if ( $the_forum['owner_id'] == $GO_SECURITY->user_id )
{
	
echo $button->get_button($frm_empty_forum,"empty_forum();");$tree = '';
}

echo '<br /><br />';

switch($dd)
{
	// Javascript remote scripting view
	case 1:
		$tree = $forum->get_jsrs_tree($message_id,$id);
	break;
	
	// PHP-BB like view with sort
	case 2:
		$tree = $forum->get_list($id,$ds,$do);
	break;
}


$user_id = $GO_SECURITY->user_id;
$user = $GO_USERS->get_user($user_id);

echo '<br />';
if ( trim($tree) == '')
{
	echo '<br />';
	if ( $message_id > 0)
	{
		echo $frm_no_reponses;
	}else
	{
		echo '<i>'.$frm_no_message.'</i>';
	}
	echo '<br /><br />';
	
}else
{
	echo $tree;
}

$tabtable->print_foot();
?>

<input type="hidden" name="task" />
<input type="hidden" name="xid" />
<input type="hidden" name="id" value="<?php echo $id; ?>" />
<input type="hidden" name="message_id" value="<?php echo $message_id; ?>" />

</form>
<script language="javascript">
	var min_node = new Image();
	min_node.src = 'images/min_node.gif';
	var plus_node = new Image();
	plus_node.src = 'images/plus_node.gif';

	function change_msg(id)
	{
		document.forms[0].message_id.value=id;
		document.forms[0].submit();
	}
	function change__msg(id)
	{
		var pbox;
		pbox = document.getElementById(id);
		if ( pbox.className == 'ub_hidden' )
		{
			pbox.className='ub_visible';
		}else
		{
			pbox.className = 'ub_hidden';
		}
	}
	function expand(id)
	{
		document.forms[0].task.value='expand';
		document.forms[0].xid.value=id;
		document.forms[0].submit();
	}

	function unexpand(id)
	{
		document.forms[0].task.value='unexpand';
		document.forms[0].xid.value=id;
		document.forms[0].submit();
	}
	function refresh()
	{
		document.forms[0].task.value='refresh';
		document.forms[0].submit();
	}
	function toggleDiv(id)
	{
		var mydiv=document.getElementById('children_of_'+id);
		if ( mydiv.style.display == "none" )
		{
			mydiv.style.display = "block";
			img  = 'image_of_'+id;
			document.images[img].src  = min_node.src;
			
		}else
		{
			mydiv.style.display = "none";
			img = 'image_of_'+id;
			document.images[img].src = plus_node.src;
		}
	}
	function toggleText(id)
	{
		var maindiv=document.getElementById(id);
		if ( maindiv.className == "new_pair" || maindiv.className == "new_impair" )
		{
			asRead(id);
			AskAsRead(id);
		}
		var mydiv=document.getElementById('text_of_'+id);
		if ( mydiv.style.display == "none" )
		{
			mydiv.style.display = "block";	
		}else
		{
			mydiv.style.display = "none";			
		}
	}
	
	function asNew(id)
	{
		var mydiv=document.getElementById(id);
		if ( mydiv.className == "pair" )	mydiv.className = "new_pair";
		if ( mydiv.className == "impair" )	mydiv.className = "new_impair";
	}
	
	function AskAsRead(id)
	{
		document.magic.task.value='asRead';
		document.magic.msg_id.value=id;
		qs = buildQueryString('magic');
		RSIFrame.location.replace('server.php' + qs);
	}
	function delete_message(id)
	{
		if (confirm("<?php echo $frm_confirm_msg_delete; ?>"))
      	{
			document.magic.task.value='delete';
			document.magic.msg_id.value=id;
			qs = buildQueryString('magic');
			RSIFrame.location.replace('server.php' + qs);
		}
		
	}	
	function asRead(id)
	{
		var mydiv=document.getElementById(id);
		if ( mydiv.className == "new_pair" )	mydiv.className = "pair";
		if ( mydiv.className == "new_impair" )	mydiv.className = "impair";
	}
	
	
	function save()
	{
		document.forms[0].task.value='save';
		document.forms[0].submit();
	}
	function buildQueryString(theFormName) {
  		theForm = document.forms[theFormName];
  		var qs = ''
  		for (e=0;e<theForm.elements.length;e++) {
    		if (theForm.elements[e].name!='') {
      		qs+=(qs=='')?'?':'&'
      		qs+=theForm.elements[e].name+'='+escape(theForm.elements[e].value)
      		}
    	}
  		return qs;
	}
	function collapse(id)
	{
		var mydiv=document.getElementById('text_of_'+id);
			mydiv.style.display = "none";	mydiv=document.getElementById('children_of_'+id);
		if ( mydiv != null )
		{
			mydiv.style.display = "none";
			img = 'image_of_'+id;
			if (document.images[img] != null)
			{
				document.images[img].src = plus_node.src;
			}
			
			
		}
		
		
	}
	function expand(id)
	{
		var mydiv=document.getElementById('text_of_'+id);
			mydiv.style.display = "block";	mydiv=document.getElementById('children_of_'+id);
		if ( mydiv != null )
		{ 
			mydiv.style.display = "block";
			img  = 'image_of_'+id;
			if (document.images[img] != null)
			{
				document.images[img].src  = min_node.src;
			}
			
		}
	}
	function collapse_all()
	{
		
		var c = divArray.length;
		for (i=0;i<c;i++)
		{
			collapse(divArray[i]);	
		}
		
	}
	function expand_all()
	{
		var c = divArray.length;
		for (i=0;i<c;i++)
		{
			expand(divArray[i]);	
		}
	}
	function killdiv(id)
	{
		mydiv=document.getElementById(id);
		if ( mydiv != null )
		{
			mydiv.style.display = "none";
		}
	}
	function empty_forum()
	{
		if (confirm("<?php echo $frm_confirm_empty_forum; ?>"))
		{
			document.forms[0].task.value='empty_forum';
			document.forms[0].submit();
		}
	}
	function close_topic(id)
	{
		if (confirm("<?php echo $frm_confirm_close_topic; ?>"))
      	{
			document.magic.task.value='close_topic';
			document.magic.msg_id.value=id;
			qs = buildQueryString('magic');
			RSIFrame.location.replace('server.php' + qs);
		}
	}
	function reopen_topic(id)
	{
		if (confirm("<?php echo $frm_confirm_reopen_topic; ?>"))
      	{
			document.magic.task.value='reopen_topic';
			document.magic.msg_id.value=id;
			qs = buildQueryString('magic');
			RSIFrame.location.replace('server.php' + qs);
		}
	}
	var divArray = new Array();
	divArray.push(<?php echo $msg_ids; ?>);
	
</script>
<form name="magic" action="server.php" target="RSIFrame" method="POST">
<input type="hidden" name="task" />
<input type="hidden" name="forum_id" />
<input type="hidden" name="msg_id" />
</form>

<iframe id="RSIFrame"
  name="RSIFrame"
  style="width:0px; height:0px; border: 2px"
  src="blank.html">
</iframe>



<?php
	require($GO_THEME->theme_path."footer.inc");
?>
