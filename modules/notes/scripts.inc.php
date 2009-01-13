<?php
require($GO_LANGUAGE->get_language_file('notes'));

if(isset($GO_MODULES->modules['customfields']))
{
	require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
	$cf = new customfields();
	echo $cf->get_javascript(4, $lang['notes']['notes']);
}

require_once($GO_MODULES->modules['notes']['class_path'].'notes.class.inc.php');
$notes = new notes();

$category = $notes->get_category();
?>
<script type="text/javascript">
GO.notes.defaultCategory = {id: <?php echo $category['id']; ?>, name: "<?php echo $category['name']; ?>"};
</script>