<script type="text/javascript">
GO.email.defaultSmtpHost="<?php echo $GO_CONFIG->smtp_server; ?>";


<?php 

if(isset($_GET['mail_to']))
{
	$qs=strtolower(str_replace('mailto:','mail_to=', $_GET['mail_to']));
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

	GO.mainLayout.onReady(function(){
		GO.email.showComposer({
			values: <?php echo $js; ?>
		});
	});
	<?php
}
?>

</script>