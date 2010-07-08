<?php
//start session
require('../../Group-Office.php');

$url = $GO_CONFIG->get_setting('wp_url');
if(!$url){
	?>
	The Wordpress URL is not set yet. Please click at 'Settings' and set the URL. Then click at the button 'Wordpress admin' to load Wordpress.
	<?php
}else
{
	header('Location: '.$GO_CONFIG->get_setting('wp_url').'?GO_SID='.session_id());
}
?>