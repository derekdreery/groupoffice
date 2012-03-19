<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<link href="<?php echo GO::config()->host; ?>views/Extjs3/themes/Default/external.css" type="text/css" rel="stylesheet" />
		<?php
		
		$theme = GO::user() ? GO::user()->theme : GO::config()->theme;
		
		if($theme!='Default'){
			?>
			<link href="<?php echo GO::config()->host; ?>views/Extjs3/themes/<?php echo $theme; ?>/external.css" type="text/css" rel="stylesheet" />
			<?php
		}
		if(!empty($GLOBALS['GO_CONFIG']->custom_css_url))
			echo '<link href="'.$GLOBALS['GO_CONFIG']->custom_css_url.'" type="text/css" rel="stylesheet" />';
		?>
		<title><?php echo GO::config()->title; ?></title>
	</head>
<body>
	<div id="container">