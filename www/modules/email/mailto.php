<?php
header('Content-Type: text/html; charset=UTF-8');

require('../../GO.php');


$qs=strtolower(str_replace('mailto:','', urldecode($_SERVER['QUERY_STRING'])));
$qs=str_replace('?subject','&subject', $qs);

parse_str($qs, $vars);

$vars['to']=isset($vars['mail_to']) ? $vars['mail_to'] : '';
unset($vars['mail_to']);
	
if(!isset($vars['subject']))
	$vars['subject']='';
	
if(!isset($vars['body']))
	$vars['body']='';
//
//var_dump($vars);
//exit();

header('Location: '.GO::createExternalUrl('email', 'showComposer', array('values'=>$vars)));

