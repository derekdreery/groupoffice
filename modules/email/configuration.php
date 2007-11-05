<?php
/**
 * @copyright Intermesh 2003
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.11 $ $Date: 2006/11/21 16:25:38 $

 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */

require_once("../../Group-Office.php");

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('email');

load_basic_controls();
load_control('tabtable');

require_once($GO_CONFIG->class_path."mail/imap.class.inc");
require_once($GO_MODULES->class_path."email.class.inc");
require_once($GO_LANGUAGE->get_language_file('email'));
$mail = new imap();
$email = new email();


$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';
$return_to = (isset($_REQUEST['return_to']) && $_REQUEST['return_to'] != '') ? $_REQUEST['return_to'] : $_SERVER['HTTP_REFERER'];
$link_back = (isset($_REQUEST['link_back']) && $_REQUEST['link_back'] != '') ? $_REQUEST['link_back'] : $_SERVER['REQUEST_URI'];

if ($task == 'save')
{
	$disable_accounts = isset($_POST['disable_accounts']) ? $_POST['disable_accounts'] : 'false';
	$GO_CONFIG->save_setting('em_disable_accounts', $disable_accounts);
	if ($_POST['close'] == 'true')
	{
		header('Location: '.$return_to);
		exit();
	}
}


require_once($GO_THEME->theme_path."header.inc");

echo '<form method="POST" action="'.$_SERVER['PHP_SELF'].'" name="email_client">';
echo '<input type="hidden" name="task" value="" />';
echo '<input type="hidden" name="close" value="false" />';
echo '<input type="hidden" name="return_to" value="'.htmlspecialchars($return_to).'" />';
echo '<input type="hidden" name="link_back" value="'.$link_back.'" />';

$cfg_tab = new tabtable('configuration', $menu_configuration, '100%','300');

$cfg_tab->print_head(htmlspecialchars($return_to));
$disable_accounts_check = ($GO_CONFIG->get_setting('em_disable_accounts') == 'true') ? true : false;
echo '<br />';
$checkbox = new checkbox('disable_accounts', 'disable_accounts', 'true', $ml_disable_accounts, $disable_accounts_check);
echo $checkbox->get_html();
echo '<br /><br />';

$button = new button($cmdOk, "javascript:_save('true')");
echo $button->get_html();
$button = new button($cmdApply, "javascript:_save('false')");
echo $button->get_html();
$button = new button($cmdCancel, "javascript:document.location='".htmlspecialchars($return_to)."';");
echo $button->get_html();
$cfg_tab->print_foot();
?>
<script type="text/javascript">
function _save(close)
{
	document.forms[0].close.value=close;
	document.forms[0].task.value='save';
	javascript:document.forms[0].submit();
}
</script>
<?php
require_once($GO_THEME->theme_path."footer.inc");
