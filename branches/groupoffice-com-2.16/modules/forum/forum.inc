<div id="div_forum">

<table class="TableInside" width="500">
<tr>
	<td colspan=3><span class="<?php echo $msg_class; ?>"><?php echo $msg; ?></span></td>
</tr>

<tr>
	<td><?php echo $frm_name; ?></td>
	<td><input type="text" class="textbox" size="50" name="name" value="<?php echo $name; ?>"</td>
</tr>

<tr>
	<td><?php echo $frm_desc; ?></td>
	<td><textarea rows="3" cols="47" name="desc" class="textbox"><?php echo $desc; ?></textarea></td>
</tr>

<tr>
	<td><?php echo $frm_mailing_list; ?></td>
	<td><textarea rows="9" cols="47" name="mailing_list" class="textbox"><?php echo $mailing_list; ?></textarea></td>
</tr>
<tr>
	<td><?php echo $frm_custom; ?> 1</td>
	<td><input type="text" class="textbox" size="50" name="custom1" value="<?php echo $custom1; ?>"</td>
</tr>
<tr>
	<td><?php echo $frm_custom; ?> 2</td>
	<td><input type="text" class="textbox" size="50" name="custom2" value="<?php echo $custom2; ?>"</td>
</tr>
<tr>
	<td><?php echo $frm_custom; ?> 3</td>
	<td><input type="text" class="textbox" size="50" name="custom3" value="<?php echo $custom3; ?>"</td>
</tr>


<tr>
	<td><?php echo $frm_ad_type; ?></td>
	<td><?php echo $drop_type; ?></td>
</table>
<br /><br />

<?php echo $btn; ?>
	

</div>

<script language="javascript">
	function save()
	{
		document.forms[0].close.value='true';
		document.forms[0].task.value='save';
		document.forms[0].submit();
	}
	function apply()
	{
		document.forms[0].close.value='false';
		document.forms[0].task.value='save';
		document.forms[0].submit();
	}
	
</script>
