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
$GO_MODULES->authenticate('email');
load_basic_controls();
require_once($GO_LANGUAGE->get_language_file('email'));
require_once ($GO_MODULES->class_path."email.class.inc");
$email = new email();
$account = $email->get_account(0);
$mailbox = $email->get_folder($account['id'], 'INBOX');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php
//$GO_THEME->load_module_theme('email');

require($GO_CONFIG->root_path.'default_head.inc');
require($GO_CONFIG->root_path.'default_scripts.inc');
echo $GO_THEME->get_stylesheet('email');
?>
<script type="text/javascript" src="language/en.js"></script>
<script type="text/javascript" src="email.js"></script>
<script type="text/javascript" src="<?php echo $GO_CONFIG->control_url; ?>selectuser.js"></script>
<script type="text/javascript">

Ext.EventManager.onDocumentReady(
function(){
	email.init(<?php echo $account['id']; ?>, <?php echo $mailbox['id']; ?>, 'INBOX');
	account.init();
}, email, true);
</script>
</head>
<body>
<div id="north">
	<div id="emailtb"></div>
</div>
<div id="west">
	<div id="email-tree"></div>
</div>
<div id="inner-layout">
	<div id="email-grid"></div>
	<div id="preview" style="background-color:#c3daf9;height:100%"></div>
</div>


<div id="accounts-dialog" style="visibility:hidden;">
    <div class="x-dlg-hd"><?php echo $ml_accounts; ?></div>
    <div class="x-dlg-bd">
        <div id="accounts-toolbar"></div>
		<div id="accounts-grid"></div>
    </div>
</div>


<div id="account-dialog" style="position:fixed;visibility:hidden;">
	<div class="x-dlg-hd"><?php echo 'Account'; ?></div>	
	    <div id="box-bd" class="x-dlg-bd">	   
		    <div id="properties" class="x-dlg-tab"></div>
			<div id="folders" class="x-dlg-tab">
				<div id="folders-toolbar"></div>
				<div class="innerTab">
					<div id="folders-tree"></div>
					<div id="folders-form-div"></div>
				</div>			
			</div>
			<div id="filters" class="x-dlg-tab">
			<?php
			$select=new select('field');
			$select->add_value('sender',$ml_email_is);
			$select->add_value('subject',$ml_subject_is);
			$select->add_value('to',$ml_to_is);
			$select->add_value('cc',$ml_cc_is);
			
			echo $select->get_html();
			
			
			$select=new select('folder');
			$select->add_value('',$ml_choose_action.'&nbsp;&nbsp;&nbsp;');
			$email->get_subscribed($account['id']);
			while ($email->next_record())
			{
			  if (!($email->f('attributes')&LATT_NOSELECT) && $email->f('name') != 'INBOX')
			  {
			    $select->add_value($email->f('name'), str_replace('INBOX'.$email->f('delimiter'), '', $email->f('name')));
			  }
			}
			echo $select->get_html();
			
			
			
			?>
			<div id="filters-grid"></div>
			
			</div>
			<div id="autoreply" class="x-dlg-tab"></div>
	    </div>
	</div>
</div>

</body>
</html>
