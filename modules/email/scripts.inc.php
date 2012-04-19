<?php
$GO_SCRIPTS_JS .='GO.email.defaultSmtpHost="'.$GLOBALS['GO_CONFIG']->smtp_server.'";
GO.email.useHtmlMarkup=';

$use_plain_text_markup = $GLOBALS['GO_CONFIG']->get_setting('email_use_plain_text_markup', $GLOBALS['GO_SECURITY']->user_id);
if(!empty($use_plain_text_markup))
	$GO_SCRIPTS_JS .= 'false;';
else
	$GO_SCRIPTS_JS .= 'true;';

$GO_SCRIPTS_JS .= 'GO.email.skipUnknownRecipients=';
$skip_unknown_recipients = $GLOBALS['GO_CONFIG']->get_setting('email_skip_unknown_recipients', $GLOBALS['GO_SECURITY']->user_id);
if(empty($skip_unknown_recipients))
	$GO_SCRIPTS_JS .= 'false;';
else
	$GO_SCRIPTS_JS .= 'true;';

$GO_SCRIPTS_JS .= 'GO.email.alwaysRequestNotification=';
$always_request_notification = $GLOBALS['GO_CONFIG']->get_setting('email_always_request_notification', $GLOBALS['GO_SECURITY']->user_id);
if(empty($always_request_notification))
	$GO_SCRIPTS_JS .= 'false;';
else
	$GO_SCRIPTS_JS .= 'true;';

$GO_SCRIPTS_JS .= 'GO.email.alwaysRespondToNotifications=';
$always_respond_to_notifications = $GLOBALS['GO_CONFIG']->get_setting('email_always_respond_to_notifications', $GLOBALS['GO_SECURITY']->user_id);
if(empty($always_respond_to_notifications))
	$GO_SCRIPTS_JS .= 'false;';
else
	$GO_SCRIPTS_JS .= 'true;';


$font_size = $GLOBALS['GO_CONFIG']->get_setting('email_font_size', $GLOBALS['GO_SECURITY']->user_id);
if(empty($font_size))
	$GO_SCRIPTS_JS .= 'GO.email.fontSize="12px";';
else
	$GO_SCRIPTS_JS .= 'GO.email.fontSize="'.$font_size.'";';

if(isset($_GET['mail_to']))
{
	//$qs=strtolower(str_replace('mailto:','mail_to=', $_GET['mail_to']));
	//$qs=str_replace('?subject','&subject', $qs);

        $qs=strtolower(str_replace('mailto:','', urldecode($_SERVER['QUERY_STRING'])));
        $qs=str_replace('?subject','&subject', $qs);
	
	parse_str($qs, $vars);
	//var_dump($vars);
	
	$vars['to']=isset($vars['mail_to']) ? $vars['mail_to'] : '';
	unset($vars['mail_to']);
		
	if(!isset($vars['subject']))
		$vars['subject']='';
		
	if(!isset($vars['body']))
		$vars['body']='';
	
	$js = json_encode($vars);
	?>
	<script type="text/javascript">
	GO.mainLayout.onReady(function(){
		GO.email.showComposer({
			values: <?php echo $js; ?>
		});
	});
	</script>
	<?php
}

$email_show_cc = $GLOBALS['GO_CONFIG']->get_setting('email_show_cc', $GLOBALS['GO_SECURITY']->user_id);
$email_show_bcc = $GLOBALS['GO_CONFIG']->get_setting('email_show_bcc', $GLOBALS['GO_SECURITY']->user_id);

$GO_SCRIPTS_JS .='GO.email.showCCfield="'.$email_show_cc.'";'
		. 'GO.email.showBCCfield="'.$email_show_bcc.'";';


$GO_SCRIPTS_JS .= "GO.email.pspellSupport=";

if(function_exists('pspell_new'))
	$GO_SCRIPTS_JS .= 'true;';
else
	$GO_SCRIPTS_JS .= 'false;';

?>