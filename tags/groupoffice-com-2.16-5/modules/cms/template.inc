<?php
if ($template_id > 0 && $task != 'replace_template_files')
{
	$restrict_check = (isset($template) && $template['restrict_editor'] == '1') ? true : false;
	$frontpage_check  = (isset($template) && $template['activate_frontpage'] == '1') ? true : false;
	$name = $template['name'];
	$style = $template['style'];
	$additional_style= $template['additional_style'];
	$print_style= $template['print_style'];
}else
{
	$restrict_check = isset($_POST['restrict_editor']) ? true : false;
	$frontpage_check = isset($_POST['activate_frontpage']) ? true : false;
	$name = isset($_POST['name']) ? smart_stripslashes($_POST['name']) : '';
	$additional_style = isset($_POST['additional_style']) ? smart_stripslashes($_POST['additional_style']) : "
body{
}
hr{
}
a:link, a:visited, a:active, a:hover  {
}
p {
}
h1 {
}
h2{
}
h3{
}
";
	$print_style = isset($_POST['print_style']) ? smart_stripslashes($_POST['print_style']) : "
body{
}
hr{
}
a:link, a:visited, a:active, a:hover  {
}
p {
}
h1 {
}
h2{
}
h3{
}
";

	$style = isset($_POST['style']) ? smart_stripslashes($_POST['style']) : "
body{
}
hr{
}
a:link, a:visited, a:active, a:hover  {
}
p {
}
h1 {
}
h2{
}
h3{
}
";
}

if($task == 'replace_template_files')
{
  $style = $cms->replace_template_files($template_id, $style);
  $additional_style = $cms->replace_template_files($template_id, $additional_style);  
  $print_style = $cms->replace_template_files($template_id, $print_style);
}

?>
<input type="hidden" name="task" />
<input type="hidden" name="template_id" value="<?php echo $template_id; ?>" />
<input type="hidden" name="close" value="false" />

<?php
if(isset($feedback)) echo $feedback;
?>
<table border="0" cellpadding="0" cellspacing="0">
<tr>
	<td valign="top">
	<table border="0">
	<tr>
		<td><?php echo $strName; ?>:</td>
		<td><input type="text" value="<?php echo $name; ?>" name="name" class="textbox" size="30" /></d>
	</tr>
	<tr>
		<td colspan="2">
		<?php $checkbox = new checkbox('restrict_editor','restrict_editor', 'true', $cms_restrict_editor, $restrict_check); 
		echo $checkbox->get_html();
		?></td>
	</tr>
	<tr>
		<td colspan="2">
		<?php 
		$checkbox = new checkbox('activate_frontpage', 'activate_frontpage',  'true', $cms_activate_frontpage, $frontpage_check); 
		echo $checkbox->get_html();
		?></td>
	</tr>
	</table>
	</td>
	<td valign="bottom" align="right">
	<?php
	$button = new button($cms_replace_filenames,'javascript:replace_template_files()', 140);
	echo $button->get_html();
	?>	
	</td>
</tr>
<tr>
	<td colspan="2">
	<table border="0">
	<tr>
		<td colspan="2">
		<table border="0">
		<tr><td><?php echo $cms_style; ?>:</td></tr>
		<tr><td><a href="javascript:popup('template_file_selector.php?template_id=<?php echo $template_id; ?>&SET_FIELD=style','250','300')" class="normal"><?php echo $cms_insert_file_url; ?></a>
		<br />
		<input type="file" class="textbox" name="style_file" /></td></tr>
		<tr><td>
		<textarea class="textbox" name="style" style="width: 200px;height:300px;"><?php echo htmlspecialchars($style); ?></textarea>
		</td></tr></table>
		</td>
		<td>
		<table border="0">
		<tr><td><?php echo $cms_additional_style; ?>:</p></td></tr>
		<tr><td><a href="javascript:popup('template_file_selector.php?template_id=<?php echo $template_id; ?>&SET_FIELD=additional_style','250','300')" class="normal"><?php echo $cms_insert_file_url; ?></a></td></tr>
		<tr><td><input type="file" class="textbox" name="additional_style_file" /></td></tr>		
		<tr><td><textarea class="textbox" name="additional_style" style="width: 200px;height:300px;"><?php echo htmlspecialchars($additional_style); ?></textarea></td></tr>
		</table>
		</td>
		<td>
		<table border="0">
		<tr><td><?php echo $cms_print_style; ?>:</p></td></tr>
		<tr><td><a href="javascript:popup('template_file_selector.php?template_id=<?php echo $template_id; ?>&SET_FIELD=print_style','250','300')" class="normal"><?php echo $cms_insert_file_url; ?></a></td></tr>
		<tr><td><input type="file" class="textbox" name="print_style_file" /></td></tr>		
		<tr><td><textarea class="textbox" name="print_style" style="width: 200px;height:300px;"><?php echo htmlspecialchars($print_style); ?></textarea></td></tr>
		</table>
		</td>
	</tr>
	</table>
	<br />
	<?php
	$button = new button($cmdOk, "javascript:save_close_template_item()");
	echo $button->get_html();
	$button = new button($cmdSave, "javascript:save_template_item()");
	echo $button->get_html();
	$button = new button($cmdClose, "javascript:document.location='index.php?tabindex=templates.inc'");
	echo $button->get_html();
	?>
	</td>
</tr>
</table>

<script type="text/javascript">
function save_template_item()
{
	document.forms[0].task.value='save_template';
	document.forms[0].submit();
}
function save_close_template_item()
{
	document.forms[0].close.value='true';
	document.forms[0].task.value='save_template';
	document.forms[0].submit();
}
function replace_template_files()
{
	document.forms[0].task.value = 'replace_template_files';
	document.forms[0].submit();
}
</script>
