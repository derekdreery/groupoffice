<?php
require('../../../Group-Office.php');
header('Content-Type: text/html; charset=UTF-8');

if(!$GO_SECURITY->logged_in())
{
	die('Unauthorized');
}

require_once ($GO_MODULES->modules['files']['class_path']."files.class.inc");
$fs = new files();

$path = $GO_CONFIG->file_storage_path.urldecode(($_REQUEST['path']));

if(substr($path,-1,1)=='/')
{
	$path = substr($path, 0, -1);
}

if(!isset($_SESSION['GO_SESSION']['files']['jupload_new_files']))
{
	$_SESSION['GO_SESSION']['files']['jupload_new_files']=array();
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
				
			if($_SESSION['GO_SESSION']['chunked_upload_size']>$GO_CONFIG->max_file_size)
			{
				for($i=1;$i<$_POST['jupart'];$i++)
				{
					$part = $dir.$file['name'].'.part'.$i;
					unlink($part);
				}
			//	debug('Uploaded file too big: '.$_SESSION['GO_SESSION']['chunked_upload_size'].' -> '.$GO_CONFIG->max_file_size);
				exit('ERROR: File is too big');
			}

			if(!empty($_POST['jufinal']))
			{
				$_SESSION['GO_SESSION']['chunked_upload_size']=0;

				move_uploaded_file($file['tmp_name'], $filepath);


				$complete_dir = $path.'/'.($_POST['relpathinfo'][$count]).'/';
				$filepath = File::checkfilename($complete_dir.$file['name']);

				$fp = fopen($filepath, 'a+');

				for($i=1;$i<=$_POST['jupart'];$i++)
				{
					$part = $dir.$file['name'].'.part'.$i;
					fwrite($fp, file_get_contents($part));
					unlink($part);
				}
				
				$fs->add_file($filepath);
				
				$_SESSION['GO_SESSION']['files']['jupload_new_files'][]=($_POST['relpathinfo'][$count]).'/'.utf8_basename($filepath);
				fclose($fp);
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
			
			$_SESSION['GO_SESSION']['files']['jupload_new_files'][]=($_POST['relpathinfo'][$count]).'/'.utf8_basename($filepath);
		}
		
		move_uploaded_file($file['tmp_name'], $filepath);
		$fs->add_file($filepath);
	}
	$count++;
}
echo 'SUCCESS';