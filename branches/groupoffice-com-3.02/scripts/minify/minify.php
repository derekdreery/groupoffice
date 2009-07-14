#!/usr/bin/php
<?php
if(!isset($argv[1]))
{
	exit('usage ./minify.php /path/to/group-office');
}

$delete = isset($argv[2]) && $argv[2]=='delete';

$full_path = dirname(__FILE__);
$compressor="java -jar $full_path/yuicompressor-2.3.5/build/yuicompressor-2.3.5.jar";

//chdir($full_path);

if(!@chdir($argv[1]))
{
	exit('Could not change directory to '.$argv[1]);
}
echo getcwd()."\n";
echo "Processing main scripts\n";

if(file_exists('javascript/go-all.js'))
{
	unlink('javascript/go-all.js');
}

$scripts_fp = fopen('javascript/scripts.txt', 'r');
while($script = fgets($scripts_fp))
{
	$script = trim($script);
	
	if(!empty($script))
	{		
		$contents = file_get_contents($script);	
		if(!$contents)
		{
			exit('Could not get contents from '.$script);
		}	
		file_put_contents('javascript/go-all.js', $contents.';', FILE_APPEND);
	
		if($delete)
		{
			unlink($script);
		}
	}
}
fclose($scripts_fp);

exec($compressor.' javascript/go-all.js -o javascript/go-all-min.js');
unlink('javascript/go-all.js');
if($delete)
{
	unlink('javascript/scripts.txt');
}

if($dir = @opendir('modules'))
{
	while($module=readdir($dir))
	{
		//echo $module."\n\n";
		if(is_dir('modules/'.$module) && $module[0] != '.' && file_exists('modules/'.$module.'/scripts.txt'))
		{
			echo "Processing $module\n";

			$all_scripts_file = 'modules/'.$module.'/all-module-scripts.js';

			if(file_exists($all_scripts_file))
			{
				unlink($all_scripts_file);
			}
			touch($all_scripts_file);

			$scripts_fp = fopen('modules/'.$module.'/scripts.txt', 'r');
			while($script = fgets($scripts_fp))
			{
				$script = trim($script);
				//echo $script."\n";
				
				if(!empty($script))
				{
					$contents = file_get_contents($script);
					if(!$contents)
					{
						exit('Could not get contents from '.$script);
					}
					file_put_contents($all_scripts_file, $contents.';', FILE_APPEND);
	
					if($delete)
					{
						unlink($script);
					}
				}
			}
			fclose($scripts_fp);
			
			if($delete)
			{
				unlink('modules/'.$module.'/scripts.txt');
			}


			exec($compressor.' '.$all_scripts_file.' -o modules/'.$module.'/all-module-scripts-min.js');
			unlink($all_scripts_file);
		}
	}
}

echo "Finished!\n";
?>
