<?php
/**
 * @copyright Intermesh 2007
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.0 $ $Date: 2007/04/10 $

 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */
require_once ("../../Group-Office.php");

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('email');

load_basic_controls();

$qs=str_replace('mailto:','', $_SERVER['QUERY_STRING']);
$qs=str_replace('?subject','&subject', $qs);

parse_str($qs, $vars);
//var_dump($vars);

if(!isset($vars['mail_to']))
	$vars['mail_to']='';
	
if(!isset($vars['subject']))
	$vars['subject']='';
	
if(!isset($vars['body']))
	$vars['body']='';

$GO_HEADER['body_arguments'] = 'onload="javascript:composer(\''.smart_stripslashes($vars['mail_to']).'\',\''.smart_stripslashes($vars['subject']).'\',\''.smart_stripslashes($vars['body']).'\');"';



require_once ($GO_THEME->theme_path."header.inc");

require_once ($GO_LANGUAGE->get_language_file('email'));


$p = new html_element('p', sprintf($ml_mail_to, $vars['mail_to']));
echo $p->get_html();

$button = new button($ml_inbox, "javascript:parent.location='".$GO_CONFIG->host."index.php?return_to=".$GO_MODULES->modules['email']['url']."';");
echo $button->get_html();


?>
<script type="text/javascript">
function composer(mail_to,subject,body)
{
	if(typeof(mail_to) == "undefined")
	{
		mail_to='';
	}
	if(typeof(subject) == "undefined")
	{
		subject='';
	}
	if(typeof(body) == "undefined")
	{
		body='';
	}

	var url = 'send.php?mail_to='+mail_to+'&mail_subject='+subject+'&mail_body='+escape(body);
	
	var height='<?php echo $GO_CONFIG->composer_height; ?>';
	var width='<?php echo $GO_CONFIG->composer_width; ?>';
	var centered;
	
	x = (screen.availWidth - width) / 2;
	y = (screen.availHeight - height) / 2;
	centered =',width=' + width + ',height=' + height + ',left=' + x + ',top=' + y + ',scrollbars=yes,resizable=yes,status=yes';

	var popup = window.open(url, '_blank', centered);
  if (!popup.opener) popup.opener = self;
	popup.focus();
}
</script>
<?php
require_once ($GO_THEME->theme_path."footer.inc");