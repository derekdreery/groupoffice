
<script type="text/javascript" langugae="javascript">
<!--
function upload()
{
	document.forms[0].task.value="upload";
	document.forms[0].submit();
}


-->
</script>

<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $GO_CONFIG->max_file_size; ?>" />

<table border="0" cellpadding="3">
<tr>
	<td class="Success"><?php echo $count_str; ?></td>
</tr>

<tr>
	<td colspan="2">
	<?php echo $frm_import_string; ?>
	<br /><br />
	<?php
	if (isset($feedback)) echo $feedback;
	echo $frm_import_text;
	?>
	
	
	</td>
</tr>

<tr>
	<td><?php echo $frm_import_file; ?>:</td>
	<td><input type="file" name="import_file" maxlength="100" size="60" class="textbox" /></td>
</tr>
<tr>
	<td><?php echo $frm_choose_sep; ?>:</td>
	<td><input type="text" name="csv_sep" size="1" maxlength="1" value="<?php echo $csv_sep; ?>" class="textbox" /></td>
</tr>
<?php
	$chk_erase = $checkbox->get_checkbox('chk_erase','1',$frm_erase_before_import,$chk_erase);
?>
<tr>
	<td><?php echo $chk_erase; ?></td>
</tr>

<tr>
	<td><?php 	$import_date = new radio_list('import_date',$import_date); 
				$import_date->add_option('all',$frm_import_all);
				
				
		?>
	</td>
	<td>
		<?php $import_date->add_option('selected',$frm_import_selected); ?>
	</td>
</tr>
<?php 
	$date1 = $date_picker->get_date_picker('date1',$_SESSION['GO_SESSION']['date_format'],$date1);
	$date2 = $date_picker->get_date_picker('date2',$_SESSION['GO_SESSION']['date_format'],$date2);
?>
<tr>
	<td></td>
	<td><?php echo $frm_from.' '.$date1.' '.$frm_to.' '.$date2; ?></td>
</tr>

<tr>
	<td id="status" class="Success" colspan="2">&nbsp;</td>
</tr>
<tr>
	<td colspan="2">
	<br />
	<?php
	$button = new button($cmdOk, 'javascript:upload();');
	echo '&nbsp;&nbsp;';
	$button = new button($cmdClose, "javascript:document.location='".$return_to."'");
	?>
	</td>
</tr>
</table>
