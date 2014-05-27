<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
	
		<title>Group Office - <?php echo \Site::controller()->getPageTitle(); ?></title>
		<link rel="shortcut icon" type="image/x-icon" href="<?php echo \Site::template()->getUrl(); ?>favicon.ico">
		<link rel="stylesheet" href="<?php echo \Site::template()->getUrl(); ?>css/site.css">
		
		<?php if(\Site::fileExists('style.css', false)){ ?>
		<link rel="stylesheet" href="<?php echo \Site::file('style.css', false); ?>">
		<?php } ?>
	
	</head>

	<body>
		<span itemscope itemtype="http://www.schema.org/Organization">
			
			<header id="page-header">
				<div class="wrapper">
					<h1 id="logo-holder">
						<a href="/">
							<img id="logo" src="<?php echo \Site::file('images/logo-244.png'); ?>" alt="Group-Office logo" />
	<!--						<img id="logo-retina" src="<?php echo \Site::file('images/logo-244.png'); ?>" alt="Group-Office logo" />-->
						</a>
					</h1>				
				</div>
			</header>

			<?php echo $content; ?>
				
			<footer id="contact">
				<div class="wrapper">

					<div class="contact-info">
						<?php
						$addressHtml = 'a';

						echo $addressHtml;
						?>
					</div>

					<div class="contact-form">
					</div>
				</div>
			</footer>
			
		</span>
	</body> 
</html>