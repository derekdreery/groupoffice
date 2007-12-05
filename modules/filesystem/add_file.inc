<form name="add_file" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<input type="hidden" name="path" value="<?php echo $path; ?>" />
<input type="hidden" name="task" value="save" />


<?php
load_control('tabtable');
$tabtable = new tabtable('file_tab', $fs_new_doc, '100%', '300');
$tabtable->print_head('index.php');
if(isset($feedback)) echo '<p class="Error">'.$feedback.'</p>';
?>

<table border="0">
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
		$button = new button($cmdCancel, "javascript:document.location='index.php';");
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
