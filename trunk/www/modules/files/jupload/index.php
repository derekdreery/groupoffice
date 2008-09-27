<?php
require('../../../Group-Office.php');
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" style="height:100%">
<head>
<title>JUpload - File Upload Applet</title>
<meta content="text/html; charset=UTF-8" http-equiv="Content-Type" />
</head>
<body style="margin:0;padding:0;height:100%">
<?php echo $_REQUEST['path']; ?>
        <applet
	            code="wjhk.jupload2.JUploadApplet"
	            name="JUpload"
	            archive="<?php echo $GO_CONFIG->control_url; ?>wjhk.jupload.jar"
	            width="100%"
	            height="100%"
	            mayscript 
	            alt="The java pugin must be installed.">
            <param name="postURL" value="upload.php?path=<?php echo urlencode(smart_stripslashes($_REQUEST['path'])); ?>&local_path=<?php echo $_REQUEST['local_path']; ?>" />
            <param name="afterUploadURL" value="javascript:opener.GO.currentFilesStore.reload();if(%success%){window.close();}" />
            <param name="lookAndFeel" value="system" />
            <param name="showLogWindow" value="true" />
            <param name="maxChunkSize" value="1048576" />    
            <param name="maxFileSize" value="<?php echo intval($GO_CONFIG->max_file_size); ?>" />
                     
            Java 1.5 or higher plugin required. 
        </applet>
</body>
</html>