<?php
/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @version $Id$
 * @author Merijn Schering <mschering@intermesh.nl>
 */
require('Group-Office.php');
?>
<div style="padding:5px;">
	<img src="<?php echo $GO_THEME->theme_url; ?>images/groupoffice.gif" /><br />
	<br />	
	Version: <?php echo $GO_CONFIG->version ?>
	<br /><br />
	Copyright (c) 2003-2008, Intermesh<br />
	All rights reserved.<br />
	<br />
	This program is protected by copyright law and the Group-Office license.<br />
	<br />
	For more information about Group-Office visit:<br />
	<br />
	<a class="normal-link" href="http://www.group-office.com" target="_blank">http://www.group-office.com</a><br />
	<br />
	Group-Office is created by Intermesh. For more information about Intermesh visit:<br />
	<br />
	<a class="normal-link" href="http://www.intermesh.nl" target="_blank">http://www.intermesh.nl</a>
	
	<?php
	$usage_date = $GO_CONFIG->get_setting('usage_date');
	if($usage_date){
		$mailbox_usage = floatval($GO_CONFIG->get_setting('mailbox_usage'));
		$file_storage_usage = floatval($GO_CONFIG->get_setting('file_storage_usage'));
		$database_usage = floatval($GO_CONFIG->get_setting('database_usage'));	
	?>	
		<br /><br />	
		<h1><?php echo sprintf($lang['common']['usage_stats'], Date::get_timestamp($usage_date)); ?></h1>	
		<br />
		<p><?php echo $lang['common']['usage_text']; ?>:</p>
		<br />
		<table class="about">
		<tr>
			<td><?php echo $lang['common']['database']; ?>:</td>
			<td><?php echo Number::format_size($database_usage*1024); ?></td>
		</tr>
		<tr>
			<td><?php echo $lang['common']['files']; ?>:</td>
			<td><?php echo Number::format_size($file_storage_usage*1024); ?></td>
		</tr>
		<tr>
			<td><?php echo $lang['common']['email']; ?>:</td>
			<td><?php echo Number::format_size($mailbox_usage*1024); ?></td>
		</tr>
		<tr>
			<td><b><?php echo $lang['common']['total']; ?>:</b></td>
			<td><b><?php echo Number::format_size(($mailbox_usage+$file_storage_usage+$database_usage)*1024); ?></b></td>
		</table>
	<?php } ?>
</div>