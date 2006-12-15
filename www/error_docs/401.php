<?php
require_once("../Group-Office.php");
$page_title=$strAccessDenied;
require_once($GO_THEME->theme_path."header.inc");
?>
<table cellpadding="10" class="ErrorBox">
<tr>
	<td valign="top"><img src="<?php echo $GO_THEME->images['stop']; ?>" border="0" /></td>
	<td>
	<h1><?php echo $strAccessDenied; ?></h1>
	<?php echo $AccessDenied_text; ?>
	</td>
</tr>
<tr>
	<td colspan="2">
	<?php
	$button = new button($cmdBack, "javascript:window.history.go(-1)");
	?>
    </td>
</tr>
</table>
<?php
require_once($GO_THEME->theme_path."footer.inc");
?>