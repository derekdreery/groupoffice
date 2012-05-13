<?php
$module = $this->get_module('summary');

global $GO_LANGUAGE, $GO_CONFIG;

require_once($GO_CONFIG->class_path.'base/users.class.inc.php');
$GO_USERS = new GO_USERS();

require($GO_LANGUAGE->get_language_file('summary'));

require_once ($module['class_path']."summary.class.inc.php");

$sum = new summary();
$GO_USERS->get_users();
while($GO_USERS->next_record())
{
	$feed['user_id']=$GO_USERS->f('id');
	$feed['url']=$lang['summary']['default_rss_url'];
	$feed['title']=$lang['summary']['default_rss_title'];
	$feed['summary']=1;
	$sum->add_feed($feed);
}
?>