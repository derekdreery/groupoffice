<?php
/*
Copyright Intermesh 2003
Author: Merijn Schering <mschering@intermesh.nl>
Version: 1.0 Release date: 08 July 2003

This program is free software; you can redistribute it and/or modify it
under the terms of the GNU General Public License as published by the
Free Software Foundation; either version 2 of the License, or (at your
option) any later version.
*/
require_once("../../Group-Office.php");
$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('search');
require_once($GO_LANGUAGE->get_language_file('search'));

load_basic_controls();

$page_title = $search_title;
require_once($GO_THEME->theme_path."header.inc");

?>
<table border="0" cellspacing="0" cellpadding="15">
<tr>
	<td>
        <h1><?php echo $search_title; ?></h1>
        <?php echo $search_text; ?><br />
        <form method="get" action="http://www.google.com/search">
        <table border="0" cellpadding="0" cellspacing="3">
        <tr>
                <td>
                <input type="text" name="q" size="31" maxlength="255" class="textbox" value="">
                </td>
                <td align="right">
                &nbsp;&nbsp;
                <?php
                $button = new button($cmdSearch, 'javascript:document.forms[0].submit()');
                ?>
                </td>
        </tr>
        <tr>
        	<td colspan="2" >
        	<br /><br />
        	<a href="http://www.google.com/" target="_blank" title="Google"><img src="<?php echo $GO_THEME->images['google']; ?>" border="0" width="143" height="53" /></a>
        	</td>
        </tr>
        </table>
        </form>

	</td>
</tr>
</table>
<script type="text/javascript">
document.forms[0].q.focus();
</script>
<?php
require_once($GO_THEME->theme_path."footer.inc");
?>
