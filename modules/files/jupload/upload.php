<?php
require('../../../Group-Office.php');
header('Content-Type: text/html; charset=UTF-8');

if(!$GO_SECURITY->logged_in())
{
	die('Unauthorized');
}

require_once($GO_CONFIG->class_path.'base/quota.class.inc.php');
$quota = new quota();


require_once ($GO_MODULES->modules['files']['class_path']."files.class.inc.php");
$files = new files();

$folder = $files->get_folder($_REQUEST['id']);

$path = $GO_CONFIG->file_storage_path.$files->build_path($folder);


if(!isset($_SESSION['GO_SESSION']['files']['jupload_new_files']))
{
	$_SESSION['GO_SESSION']['files']['jupload_new_files']=array();
}

$count=0;


try{


while($file = array_shift($_FILES))
{

	if (is_uploaded_file($file['tmp_name']))
	{
		if(isset($_POST['jupart']))
		{				
			$dir = $GO_CONFIG->tmpdir.'chunked_upload/';
			$filepath = $dir.$file['name'].'.part'.$_POST['jupart'];

			go_debug('Part '.$_POST['jupart'].': '.$filepath);
				
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
			//	go_debug('Uploaded file too big: '.$_SESSION['GO_SESSION']['chunked_upload_size'].' -> '.$GO_CONFIG->max_file_size);
				exit('ERROR: File is too big');
			}

			if(!empty($_POST['jufinal']))
			{
				$_SESSION['GO_SESSION']['chunked_upload_size']=0;

				move_uploaded_file($file['tmp_name'], $filepath);

				$complete_dir = $path.'/';				
				if(!empty($_POST['relpathinfo'.$count]))
				{
					$complete_dir .= str_replace('\\','/',$_POST['relpathinfo'.$count]).'/';
				}

				File::mkdir($complete_dir);

				$filepath = File::checkfilename($complete_dir.$file['name']);

				go_debug('Final part '.$_POST['jupart'].': '.$filepath);

				$fp = fopen($filepath, 'w+');
				for($i=1;$i<=$_POST['jupart'];$i++)
				{
					$part = $dir.$file['name'].'.part'.$i;
					fwrite($fp, file_get_contents($part));
					unlink($part);
				}
				fclose($fp);


				
				if(!$quota->add_file($filepath))
				{
					unlink($filepath);
					throw new Exception($lang['common']['quotaExceeded']);
				}
				
				$file_id = $files->import_file($filepath);

				if($GO_MODULES->has_module('workflow'))
				{
					require_once($GO_MODULES->modules['workflow']['class_path'].'workflow.class.inc.php');
					$wf = new workflow();

					$wf_folder = $wf->get_folder($folder['id']);
					if(!empty($wf_folder['default_process_id']))
					{
						$wf->enable_workflow_process($file_id, $wf_folder['default_process_id']);
					}
				}
				
				$_SESSION['GO_SESSION']['files']['jupload_new_files'][]=$files->strip_server_path($filepath);
				
				continue;
			}
		}else
		{
			$dir = $path.'/';				
			if(!empty($_POST['relpathinfo'.$count]))
			{
				$dir .= str_replace('\\','/',$_POST['relpathinfo'.$count]).'/';
			}
				
			$filepath = $dir.$file['name'];
		}

		File::mkdir($dir);

		if(!isset($_POST['jupart']))
		{
			$filepath = File::checkfilename($filepath);
		}

	
		if(!$quota->add_file($file['tmp_name']))
		{
			throw new Exception($lang['common']['quotaExceeded']);
		}
        
		move_uploaded_file($file['tmp_name'], $filepath);
		
		if(!isset($_POST['jupart']))
		{
			$relpath = $files->strip_server_path($filepath);
			
			$_SESSION['GO_SESSION']['files']['jupload_new_files'][]=$relpath;
			$file_id = $files->import_file($filepath);

			if($GO_MODULES->has_module('workflow'))
			{
				require_once($GO_MODULES->modules['workflow']['class_path'].'workflow.class.inc.php');
				$wf = new workflow();

				$wf_folder = $wf->get_folder($folder['id']);
				if(!empty($wf_folder['default_process_id']))
				{
					$wf->enable_workflow_process($file_id, $wf_folder['default_process_id']);
				}
			}
		}	
	}
	$count++;
}

}catch(Exception $e){
	echo 'WARNING: ' .$e->getMessage()."\n";
}

echo "SUCCESS\n";