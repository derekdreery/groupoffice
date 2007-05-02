<form name="add_file" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<input type="hidden" name="title" value="" />
<input type="hidden" name="description" value="" />
<input type="hidden" name="keywords" value="" />
<input type="hidden" name="task" value="save" />
<input type="hidden" name="content" value="" />
<input type="hidden" name="folder_id" value="<?php echo $folder_id; ?>" />
<?php
$tabtable = new tabtable('file_tab', $cms_new_page, '100%', '300');
$tabtable->print_head('browse.php?folder_id='.$folder_id);
if(isset($feedback)) echo $feedback;
?>
<table border="0" cellpadding="4" cellspacing="0">
<tr>
	<td>
	<?php echo $strName; ?>:
	</td>
	<td>
	<?php
	$name = isset($_POST['name']) ? smart_stripslashes(htmlspecialchars($_POST['name'])) : '';
	?>
	<input type="text" class="textbox" name="name" value="<?php echo $name; ?>" maxlength="100" size="30" />
	</td>
</tr>
<tr>
	<td colspan="2">
		<br />
		<?php
		$button = new button($cmdOk, "javascript:document.forms[0].submit()");
		echo $button->get_html();
		$button = new button($cmdCancel, "javascript:document.location='browse.php?folder_id=".$folder_id."';");
		echo $button->get_html();
		?>
	</td>
</tr>
</table>
<?php
$tabtable->print_foot();
?>
</form>
<script type="text/javascript">
document.forms[0].name.focus();
</script>
