<?php
require_once("../Group-Office.php");
load_basic_controls();
$page_title=$http_not_found;
require_once($GO_THEME->theme_path."header.inc");
?>
<table border="0" class="TableInside" cellpadding="10">
<tr>
	<td>
	<h1><?php echo $http_not_found; ?></h1>
	<?php echo $http_not_found_text; ?>
	</td>
</tr>

<tr>
	<td>
	<?php
	$button = new button($cmdBack, "javascript:window.history.go(-1)");
	echo '&nbsp;&nbsp;';
	$button = new button($cmdClose, "javascript:window.close()");
	?>
    </td>
</tr>
</table>
<?php
require_once($GO_THEME->theme_path."footer.inc");
