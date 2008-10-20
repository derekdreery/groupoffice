<?php
require_once("../www/Group-Office.php");

$modules = $GO_MODULES->get_modules_with_locations();
$lang1 = (isset($_GET['lang1']))? $_GET['lang1'] : 'en';
$lang2 = (isset($_GET['lang2']))? $_GET['lang2'] : 'nl';

echo '<html><head><title>Language Checker</title></head><body><font face="arial", size="2">';

function get_contents($filename)
{
	if(!file_exists($filename))
		return false;
	else
	{
		$lines = file($filename);
		foreach($lines as $line)
		{
			$first_equal = strpos($line,'=');
			if($first_equal != 0)
			{
				$key = trim(substr($line, 0, $first_equal));
				$contents[$key] = trim(substr($line, $first_equal, strlen($line)-1));
			}
		}
	}
	return $contents;
}
function compare_arrays($array1, $array2, $file)
{
	$diffs = array_diff_key($array1, $array2);
	if(!empty($diffs))
	{
		foreach($diffs as $key=>$diff)
		{
			if(!strpos($diff, '{}'))
				$output[] = $key.$diff;
		}
		if(!empty($output))
		{
			echo '<i>Missing in '.$file.':</i><br />';
			foreach($output as $out)
				echo htmlentities($out,ENT_QUOTES,'UTF-8').'<br />';
			echo '<br />';
		}
	}
}

function check_encoding($file)
{
	global $GO_CONFIG;
	
	if(function_exists('mb_detect_encoding'))
	{
		$str = file_get_contents($file);
		$enc = mb_detect_encoding($str, "ASCII,JIS,UTF-8,ISO-8859-1,ISO-8859-15,EUC-JP,SJIS");
		if($enc!='UTF-8' && $enc!='ASCII')
		{
			if(is_writable($file))
			{
				echo '<p style="color:red">Warning, corrected encoding of '.str_replace($GO_CONFIG->root_path, '', $file).' from '.$enc.' to UTF-8</p>';
				
				$str = mb_convert_encoding($str,'UTF-8', $enc);
				file_put_contents($file, $str);
			}else
			{
				echo '<p style="color:red">Warning, encoding of '.str_replace($GO_CONFIG->root_path, '', $file).' is '.$enc.' and should be UTF-8. Make the file writable to let this script correct it.</p>';
			}
		}
	}
}

function compare_files($file1, $file2, $type)
{
	global $GO_CONFIG;
	
	$content1 = get_contents($file1);
	$content2 = get_contents($file2);
	
	
	
	if($content1 && !$content2)
		echo '<i><font color="red">Could not compare '.str_replace($GO_CONFIG->root_path, '', $file1).', because the translation doesn\'t exist!</font></i><br />';
	
	if($content1 && $content2)
	{
		check_encoding($file1);
		check_encoding($file2);
	
		compare_arrays($content1, $content2, $file2);
		compare_arrays($content2, $content1, $file1);
	}
}



echo '<h3>COMMON FILES</h3>';
$common = $GO_CONFIG->root_path.'language/common/';

compare_files($common.$lang1.'.inc.php', $common.$lang2.'.inc.php', 'php');
compare_files($common.$lang1.'.js', $common.$lang2.'.js', 'js');
echo '<hr>';

foreach($modules as $module)
{
	echo '<h3>MODULE: '.$module['id'].'</h3>';

	compare_files($module['path'].'language/'.$lang1.'.inc.php', $module['path'].'language/'.$lang2.'.inc.php', 'php');
	compare_files($module['path'].'language/'.$lang1.'.js', $module['path'].'language/'.$lang2.'.js', 'js');
	
	echo '<hr>';
}
echo '</font></body></html>';
?>
