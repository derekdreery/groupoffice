<?php
require($GO_LANGUAGE->get_language_file('summary'));

$sql = "UPDATE su_rss_feeds SET title = \"".$lang['summary']['default_rss_title']."\"";
$db->query($sql);