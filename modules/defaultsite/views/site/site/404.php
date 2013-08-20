<?php
header("HTTP/1.0 404 Not Found");
header("Status: 404 Not Found");
?>
<div class="404-page page">
	<div class="wrapper">
		<h2>404 Not found</h2>
		<p><?php echo isset($error)?$error:'Niet gevonden'; ?></p>
	</div>
</div>