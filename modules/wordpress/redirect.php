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
	//http://localhost/wordpress/wp-admin/post.php?post=710&action=edit

	$redirect_url = $GO_CONFIG->get_setting('wp_url').'?GO_SID='.session_id();

	if(isset($_REQUEST['link_id'])){
		$redirect_url .= '&link_id='.intval($_REQUEST['link_id']).'&link_type='.intval($_REQUEST['link_type']);
	}

	header('Location: '.$redirect_url);
}
?>