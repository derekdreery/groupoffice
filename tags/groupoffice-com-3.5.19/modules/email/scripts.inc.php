<?php
$GO_SCRIPTS_JS .='GO.email.defaultSmtpHost="'.$GO_CONFIG->smtp_server.'";
GO.email.useHtmlMarkup=';

$use_plain_text_markup = $GO_CONFIG->get_setting('email_use_plain_text_markup', $GO_SECURITY->user_id);
if(!empty($use_plain_text_markup))
	$GO_SCRIPTS_JS .= 'false;';
else
	$GO_SCRIPTS_JS .= 'true;';

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
?>