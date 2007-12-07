<?php
require_once('../../Group-Office.php');
$onunload = isset($_REQUEST['onunload']) ? $_REQUEST['onunload'] : '';

$afterUploadURL = isset($_REQUEST['afterUploadURL']) ? $_REQUEST['afterUploadURL'] : '';

//DefaultUploadPolicy", "FileByFileUploadPolicy", "PictureUploadPolicy""

$uploadPolicy = isset($_REQUEST['uploadPolicy']) ? $_REQUEST['uploadPolicy'] : 'DefaultUploadPolicy';

$maxPicWidth = isset($_REQUEST['maxPicWidth']) ? number_to_phpnumber($_REQUEST['maxPicWidth']) : '0';
$maxPicHeight = isset($_REQUEST['maxPicHeight']) ? number_to_phpnumber($_REQUEST['maxPicHeight']) : '0';

require($GO_THEME->theme_path.'header.inc');
?>
<form name="jupload_form">
<input type="hidden" name="post_url" value="<?php echo $_REQUEST['post_url']; ?>" />
<input type="hidden" name="afterUploadURL" value="<?php echo $afterUploadURL; ?>" />
<input type="hidden" name="uploadPolicy" value="<?php echo $uploadPolicy; ?>" />

<table style="height:100%;width:100%">
<tr>
	<td><?php echo $jupload_text; ?><br />
	<?php 
	load_basic_controls();
	$button = new button($cmdCancel, 'document.location=\''.$afterUploadURL.'\'');
	echo $button->get_html();
	?>
	</td>
</tr>
<?php
if($uploadPolicy=='PictureUploadPolicy')
{
?>
	<tr>
		<td>
		<table>
		<tr>
			<td>
				<?php echo $jupload_max_size; ?>:
			</td>
			<td>
				<?php echo $jupload_width; ?>:
			</td>
			<td>
				<?php
				$input = new input('text', 'maxPicWidth', format_number($maxPicWidth,0), false);
				$input->set_attribute('onblur', "javascript:this.value=number_format(this.value, 0, '".$_SESSION['GO_SESSION']['decimal_seperator']."', '".$_SESSION['GO_SESSION']['thousands_seperator']."');");
				$input->set_attribute('onfocus','this.select();');
				echo $input->get_html();
				?>
			</td>
			<td>
				<?php echo $jupload_height; ?>:
			</td>
			<td>
				<?php
				$input = new input('text', 'maxPicHeight', format_number($maxPicHeight,0), false);
				$input->set_attribute('onblur', "javascript:this.value=number_format(this.value, 0, '".$_SESSION['GO_SESSION']['decimal_seperator']."', '".$_SESSION['GO_SESSION']['thousands_seperator']."');");
				$input->set_attribute('onfocus','this.select();');
				echo $input->get_html();
				?>
			</td>
			<td>
				<?php
				$button = new button($cmdApply, 'javascript:document.jupload_form.submit();');
				echo $button->get_html();
				?>
			</td>
		</tr>
		</table>
		</td>
	</tr>
<?php
}
?>
<tr height="100%">
	<td>
	<applet code="wjhk.jupload2.JUploadApplet" name="JUpload"
	archive="wjhk.jupload.jar" width="100%" height="100%"
	mayscript alt="">
	<param name="CODE" value="wjhk.jupload2.JUploadApplet" />
	<param name="ARCHIVE" value="wjhk.jupload.jar" />
	<param name="type" value="application/x-java-applet;version=1.5" />
	<param name="scriptable" value="false" />
	<param name="postURL" value="<?php echo $_REQUEST['post_url']; ?>" />
	<param name="uploadPolicy" value="<?php echo $uploadPolicy; ?>" />
	<param name="afterUploadURL" value="<?php echo $afterUploadURL; ?>" />
	<param name="maxFileSize" value="<?php echo $GO_CONFIG->max_file_size; ?>" />
	<param name="maxPicWidth" value="<?php echo $maxPicWidth; ?>" />
	<param name="maxPicHeight" value="<?php echo $maxPicHeight; ?>" />
	<param name="nbFilesPerRequest" value="1" />
	Java 1.5 or higher plugin required. </applet>
	</td>
</tr>
</table>
</form>
<?php
require($GO_THEME->theme_path.'footer.inc');
?>