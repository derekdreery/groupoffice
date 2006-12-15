
<form name="frm_post" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" ENCTYPE="multipart/form-data">

<INPUT TYPE=HIDDEN NAME=MAX_FILE_SIZE  VALUE=50000000>

<a name="post_message">
<input type="hidden" name="task" value="<?php echo $task; ?>" />

<input type="hidden" name="forum_id" value="<?php echo $forum_id; ?>" />
<input type="hidden" name="reply_to" value="<?php echo $reply_to; ?>" />
<input type="hidden" name="message_id" value="<?php echo $message_id; ?>" />
<input type="hidden" name="edit" value="<?php echo $edit; ?>" />

<table>

<tr>
	<td colspan="99" class="<?php echo $msg_class; ?>"><?php echo $msg; ?></td>
</tr>

<tr>
	<td><?php echo $frm_title; ?></td>
	<td><input type="text" class="textbox" size="50" name="title" value="<?php echo $title; ?>"</td>
</tr>

<tr>
	<td><?php echo $frm_body; ?></td>
	<td><textarea rows="9" cols="47" name="body" class="textbox"><?php echo $body; ?></textarea></td>
</tr>
<?php require('custom_fields.inc'); ?>
<?php require('attach_files.inc'); ?>
<tr>
	<td colspan="2"><?php echo $btn_save.' '.$btn_close; ?></td>
</tr>
</table>

</form>


<script language="javascript">
	
	function save()
	{
		document.forms[0].task.value='save';
		document.forms[0].submit();
	}

</script>