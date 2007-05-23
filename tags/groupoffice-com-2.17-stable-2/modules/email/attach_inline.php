<?php
/*
Copyright Intermesh 2003
Author: Merijn Schering <mschering@intermesh.nl>
Version: 1.0 Release date: 08 July 2003

This program is free software; you can redistribute it and/or modify it
under the terms of the GNU General Public License as published by the
Free Software Foundation; either version 2 of the License, or (at your
option) any later version.
*/

require_once("../../Group-Office.php");
$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('filesystem');
require_once($GO_LANGUAGE->get_language_file('email'));

$email_module = $GO_MODULES->get_module('email');
if (!$email_module)
{
	die($strDataError);
}

//load file management class
require_once($GO_CONFIG->class_path."filesystem.class.inc");
require_once($email_module['class_path'].'email.class.inc');
$email = new email();
$fs = new filesystem();

$path = stripslashes($_REQUEST['path']);

if (!$fs->has_read_permission($GO_SECURITY->user_id, $path))
{
	header('Location: '.$GO_CONFIG->host.'error_docs/401.php');
	exit();
}

$attachments_size = 0;

if (isset($_SESSION['attach_array']))
{
	for($i=0;$i<sizeof($_SESSION['attach_array']);$i++)
	{
		$attachments_size += $_SESSION['attach_array'][$i]->file_size;
	}
}
$filesize = filesize($path);
$attachments_size += $filesize;

$charset = isset($charset) ? $charset : 'UTF-8';
header('Content-Type: text/html; charset='.$charset);

if ($attachments_size < $GO_CONFIG->max_attachment_size)
{
	$GO_URL = $GO_MODULES->url.'download.php?path='.urlencode($path);
	$filename = basename($path);
	$content_id = md5(uniqid(time())).'@groupoffice';

	$tmp_file = $GO_CONFIG->tmpdir.md5(uniqid(time())).'.'.strip_extension($filename);
	if (copy($path, $tmp_file))
	{
		$email->register_attachment($tmp_file, $filename, $filesize, mime_content_type($path), 'inline', $content_id);
		//$email->register_attachment($tmp_file, $filename, $filesize, 'application/octet-stream', 'inline', $content_id);
		

		$url_replacement['id'] = $content_id;
		$url_replacement['url'] = $GO_URL;
		$_SESSION['url_replacements'][] = $url_replacement;
	}else
	{
		die($strDataError);
	}
?>

	<html>
	<body>
	<script type="text/javascript">
    window.opener.SetUrl( "<?php echo stripslashes($GO_URL); ?>", "", "", "<?php echo $filename; ?>");
  		window.close();
	</script>
	</body>
	</html>
<?php
}else
{
?>
	<html>
	<body>
	<script type="text/javascript">
			alert("<?php echo $ml_file_too_big.format_size($GO_CONFIG->max_attachment_size)." (".number_format($GO_CONFIG->max_attachment_size, 0, $_SESSION['GO_SESSION']['decimal_seperator'], $_SESSION['GO_SESSION']['thousands_seperator'])." bytes)."; ?>");
			window.close();
	</script>
	</body>
	</html>
<?php
}
