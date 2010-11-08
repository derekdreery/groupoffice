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
$GO_SECURITY->html_authenticate();
?>
<div style="padding:10px;line-height:16px">
	<div class="go-about-logo"></div>
	<?php	
	echo String::text_to_html(sprintf($lang['common']['about'], $GO_CONFIG->version, date('Y'), $GO_CONFIG->webmaster_email));
	
	$usage_date = $GO_CONFIG->get_setting('usage_date');
	if($usage_date){
		$mailbox_usage = floatval($GO_CONFIG->get_setting('mailbox_usage'));
		$file_storage_usage = floatval($GO_CONFIG->get_setting('file_storage_usage'));
		$database_usage = floatval($GO_CONFIG->get_setting('database_usage'));	
	?>
		<h1 style="margin-top:10px;"><?php echo sprintf($lang['common']['usage_stats'], Date::get_timestamp($usage_date)); ?></h1>	
		<p style="margin-top:5px;margin-bottom:5px;"><?php echo $lang['common']['usage_text']; ?>:</p>
		<table class="about" cellspacing=0>
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