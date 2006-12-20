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

//////////////////////////////////////////////////////////////////////
////////////// PLEASE LEAVE THIS ABOUT PAGE INTACT ///////////////////
//////////////////////////////////////////////////////////////////////

require_once("Group-Office.php");
$GO_SECURITY->authenticate();
require_once($GO_LANGUAGE->get_base_language_file('about'));
$page_title = $menu_about;
require_once($GO_THEME->theme_path."header.inc");

if(file_exists('LICENSE.GPL'))
{
  $license = 'LICENSE.GPL';
}else
{
  $license = 'LICENSE.PRO';
}
?>
<table border="0" cellspacing="0" cellpadding="10">
<tr>
<td>
<h1>Group-Office <?php echo $GO_CONFIG->version; ?></h1>
<?php echo $about_text; ?>
<br />
<?php echo $about_support; ?>:
<ul style="list-style: square;">
<li><a target="_blank" class="normal" href="http://www.intermesh.nl"><?php echo $about_pro_support; ?></a></li>
<li><a target="_blank" class="normal" href="http://www.group-office.com/forum/"><?php echo $about_com_support; ?></a></li>
<li><a class="normal" href="javascript:popup('<?php echo $GO_CONFIG->host; ?>doc/index.php', 750, 500);"><?php echo $about_help_files; ?></a></li>
</ul>

<table border="0">
<tr>
<td><a href="http://www.intermesh.nl" target="_blank"><img src="<?php echo $GO_THEME->images['Intermesh']; ?>" border="0" /></td>
<td><a href="http://www.apache.org" target="_blank"><img src="<?php echo $GO_THEME->images['apache_logo']; ?>" border="0"></a></td>
<td><a href="http://www.php.net" target="_blank"><img src="<?php echo $GO_THEME->images['php_logo']; ?>" border="0"></a></td>
<td><a href="http://www.mysql.com" target="_blank"><img src="<?php echo $GO_THEME->images['mysql_logo']; ?>" border="0"></a></td>
</tr>
</table>
<iframe style="width: 700; height: 250;" src="<?php echo $license; ?>"></iframe>
<br /><br /><b>Copyright Intermesh 2003 - 2005</b><br /><br />
<?php
//$button = new button($cmdClose, 'javascript:window.history.go(-1);');
?>
</td>
</tr>
</table>
<?php
require_once($GO_THEME->theme_path."footer.inc");
