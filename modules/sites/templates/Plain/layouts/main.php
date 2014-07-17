<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
		<meta name="robots" content="all,index,follow" />
		<meta name="description" content="<?php echo $this->description; ?>" />
		<title><?php echo $this->getPageTitle() . " - " . GOS::site()->getName(); ?></title>
		<link href="<?php echo $this->getTemplateUrl(); ?>css/stylesheet.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo $this->getTemplateUrl(); ?>css/notifications.css" rel="stylesheet" type="text/css" />

	</head>

	<body>
		<div class="main-container">
					<?php echo $content; ?>					
		</div>
</body>
</html>