<?php
require_once($GO_MODULES->modules['files']['class_path'].'files.class.inc.php');
$files = new files();
$folder = $files->check_share('bookmarks/icons/'.$_SESSION['GO_SESSION']['username'],$GO_SECURITY->user_id, $GO_MODULES->modules['bookmarks']['acl_id']);
$GO_SCRIPTS_JS .= 'GO.bookmarks.iconsFolderId='.$folder['id'].';';
?>
