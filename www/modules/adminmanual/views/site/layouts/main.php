<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
	
		<title>Group Office - <?php echo Site::controller()->getPageTitle(); ?></title>
		<link rel="shortcut icon" type="image/x-icon" href="<?php echo Site::template()->getUrl(); ?>favicon.ico">
		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">

		<!-- Optional theme -->
		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css">
		
		<link rel="stylesheet" href="<?php echo Site::template()->getUrl(); ?>css/site.css">
		
		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <!-- Latest compiled and minified JavaScript -->
		<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
		
		
		
		<script src="http://lokeshdhakar.com/projects/lightbox2/js/lightbox-2.6.min.js"></script>
		<link href="http://lokeshdhakar.com/projects/lightbox2/css/lightbox.css" rel="stylesheet" />
	</head>

	<body>
		<div class="container">
			
			<div class="page-header">
				<h1>Group-Office manual <small>for adminusrators</small></h1>
			</div>
			
			<?php echo $content; ?>
			
		</div>
		
	</body> 
</html>