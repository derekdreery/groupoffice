<input type="hidden" name="task" value="add_folder" />
<input type="hidden" name="folder_id" value="<?php echo $folder_id; ?>" />
<?php
load_control('tabtable');
$tabtable = new tabtable('file_tab',$fbNewFolder, '100%', '300');
$tabtable->print_head($_SERVER['PHP_SELF']."?folder_id=".$folder_id);
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
<?php
$subrow = new table_row();
$subrow->add_cell(new table_cell($cms_template_item.':'));

$parent_folder = $cms->get_folder($folder_id);

$template_item_id = isset($_POST['template_item_id']) ? $_POST['template_item_id'] : $parent_folder['template_item_id'];

$select = new select("template_item_id", $template_item_id);
$cms->get_template_items($site['template_id'], true);
while($cms->next_record())
{
	$select->add_value($cms->f('id'), $cms->f('name'));
}
$subcell = new table_cell($select->get_html());
$subrow->add_cell($subcell);

echo $subrow->get_html();


?>
<tr>
  <td colspan="2">
<?php
$checkbox = new checkbox('disabled', 'disabled','1', $cms_hide_folder, isset($_POST['disabled']));
echo $checkbox->get_html();
?>
</td>
</tr>
<tr>
	<td colspan="2">
		<?php
		$button = new button($cmdOk, "javascript:document.forms[0].submit()");
		echo $button->get_html();
		$button = new button($cmdCancel, "javascript:document.location='".$_SERVER['PHP_SELF']."?folder_id=".$folder_id."';");
		echo $button->get_html();
		?>
	</td>
</tr>
</table>
<?php
$tabtable->print_foot();
?>
