<?php
define('GO_NO_SESSION',true);
require('../GO.php');

function redirect($url){
	header('Location: '.$url);
	exit();
}

function printHead()
{
	echo '<html><head>'.
	'<meta content="text/html; charset=UTF-8" http-equiv="Content-Type" />'.
	'<link href="install.css" rel="stylesheet" type="text/css" />'.
	'<title>'.GO::config()->product_name.' Installation</title>'.
	'</head>'.
	'<body style="font-family: Arial,Helvetica;background-color:#f1f1f1">';
	echo '<form method="post">';
	echo '<div style="width:600px;padding:20px;margin:10px auto;background-color:white">';
	echo '<img src="logo.gif" border="0" align="middle" style="margin:10px" />';
	
}

function printFoot()
{
	echo '</div></form></body></html>';
}

function errorMessage($msg){
	echo '<p class="error">'.$msg.'</p>';
}

function continueButton(){
	echo '<br /><div align="right"><input type="submit" value="Continue" /></div>';
}