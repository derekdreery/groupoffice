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
		if(!empty(GO::config()->custom_css_url))
			echo '<link href="'.GO::config()->custom_css_url.'" type="text/css" rel="stylesheet" />';
		
		if(GO::modules()->isInstalled("customcss") && file_exists(GO::config()->file_storage_path.'customcss/style.css'))
			echo '<style>'.file_get_contents(GO::config()->file_storage_path.'customcss/style.css').'</style>'."\n";
		?>
		<title><?php echo GO::config()->title; ?></title>
	</head>
<body>
	<div id="container">