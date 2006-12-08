<table border="0" class="ErrorBox" cellpadding="10">
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
	echo '&nbsp;&nbsp;';
	$button = new button($cmdClose, "javascript:window.close()");
	?>
    </td>
</tr>
</table>
