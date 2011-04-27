<?php
require('../../../Group-Office.php');

if(!$GO_SECURITY->logged_in())
{
	die();
}

if(!isset($_SESSION['GO_SESSION']['just_uploaded_attachments']))
	$_SESSION['GO_SESSION']['just_uploaded_attachments']=array();

$path = $GO_CONFIG->tmpdir.'/attachments';
if(!is_dir($path))
{
	mkdir($path, 0755, true);
}


$count=0;
while($file = array_shift($_FILES))
{

	if (is_uploaded_file($file['tmp_name']))
	{
		if(isset($_POST['jupart']))
		{
				
				
			$dir = $GO_CONFIG->tmpdir.'chunked_upload/';
			$filepath = $dir.$file['name'].'.part'.$_POST['jupart'];
				
				
			if($_POST['jupart']==1)
			{
				$_SESSION['GO_SESSION']['chunked_upload_size']=0;
			}
				
			$_SESSION['GO_SESSION']['chunked_upload_size']+=$file['size'];
				
			if($_SESSION['GO_SESSION']['chunked_upload_size']>$GO_CONFIG->max_attachment_size)
			{
				for($i=1;$i<$_POST['jupart'];$i++)
				{
					$part = $dir.$file['name'].'.part'.$i;
					unlink($part);
				}
				go_debug('Uploaded file too big: '.$_SESSION['GO_SESSION']['chunked_upload_size'].' -> '.$GO_CONFIG->max_attachment_size);
				exit('ERROR: File is too big');
			}

			if(!empty($_POST['jufinal']))
			{
				$_SESSION['GO_SESSION']['chunked_upload_size']=0;

				move_uploaded_file($file['tmp_name'], $filepath);


				$complete_dir = $path.'/'.($_POST['relpathinfo'][$count]).'/';
				$filepath = File::checkfilename($complete_dir.File::strip_invalid_chars($file['name']));


				$fp = fopen($filepath, 'a+');

				for($i=1;$i<=$_POST['jupart'];$i++)
				{
					$part = $dir.$file['name'].'.part'.$i;
					fwrite($fp, file_get_contents($part));
					unlink($part);
				}
				fclose($fp);
				$_SESSION['GO_SESSION']['just_uploaded_attachments'][]=$filepath;
				
				continue;
			}
		}else
		{
			$dir = $path.'/'.($_POST['relpathinfo'][$count]).'/';
			$filepath = $dir.$file['name'];
			
		}
			
		if(!is_dir($dir))
		{
			mkdir($dir,0755,true);
		}

		if(!isset($_POST['jupart']))
		{
			$filepath = File::checkfilename($filepath);
			$_SESSION['GO_SESSION']['just_uploaded_attachments'][]=$filepath;
		}
		
		

		move_uploaded_file($file['tmp_name'], $filepath);
	}
	$count++;
}
echo "SUCCESS\n";