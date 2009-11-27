<?php
header('Content-Type: text/html; charset=UTF-8');
if(file_exists('Group-Office.php'))
{
	require_once("Group-Office.php");
}elseif(file_exists('../www/Group-Office.php'))
{
	require_once("../www/Group-Office.php");	
}else
{
	exit('Could not find Group-Office.php! Put this script in the root of Group-Office.');
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>Language checker</title>
<meta content="text/html; charset=UTF-8" http-equiv="Content-Type" />
</head>
<body>
<?php

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

	$save=false;
	$str = file_get_contents($file);
	if(function_exists('mb_detect_encoding'))
	{		
		$enc = mb_detect_encoding($str, "ASCII,JIS,UTF-8,ISO-8859-1,ISO-8859-15,EUC-JP,SJIS");
		if($enc!='UTF-8' && $enc!='ASCII')
		{
			if(is_writable($file))
			{
				$save=true;
				echo '<p style="color:red">Warning, corrected encoding of '.str_replace($GO_CONFIG->root_path, '', $file).' from '.$enc.' to UTF-8</p>';
				
				$str = iconv($enc, 'UTF-8', $str);//mb_convert_encoding($str,'UTF-8', $enc);
				
			}else
			{
				echo '<p style="color:red">Warning, encoding of '.str_replace($GO_CONFIG->root_path, '', $file).' is '.$enc.' and should be UTF-8. Make the file writable to let this script correct it.</p>';
			}
		}
	}

	if(strpos($str, "\xEF\xBB\xBF")!==false){
		$save=true;
		echo '<p style="color:red">Replacing BOM character</p>';
		$str = str_replace("\xEF\xBB\xBF", '', $str);
	}
	if($save && is_writable($file)){
		echo 'Saving: '.$file.'<br />';
		file_put_contents($file, $str);
	}
}

function check_namespace($file)
{
	global $GO_CONFIG;
	
	$invalid_regs=array(
		'/GO\.[\w]+\.lang\s*=\s*\{.*\};/',
		'/Ext.namespace\s*\([^\)]*\);/'
	);
	
	$allmatches = array();
	$str = file_get_contents($file);
	foreach($invalid_regs as $reg)
	{	
		if(preg_match_all($reg,$str,$matches))
		{
			foreach($matches[0] as $match)
				$allmatches[]=$match;
		}
	}
	
	if(is_writable($file))
	{
		$changed=false;
		if(count($allmatches))
		{
			$changed = true;
			echo '<p style="color:red">Warning, removed invalid lines from '.str_replace($GO_CONFIG->root_path, '', $file).': <br /><br /> '.implode('<br />',$allmatches).'</p>';
			
			foreach($allmatches as $match)
			{
				$str = str_replace($match, '', $str);
			}
		}
		
		if(strstr($str, '//require'))
		{
			$changed = true;
			
			$str = str_replace('//require', 'require', $str);			
			echo '<p style="color:red">Warning, uncommented //require($GO_LANGUAGE->get_fallback_language_file(\'module\'));</p>';
		}
		if($changed)
		{
			file_put_contents($file, $str);
		}
	}else
	{
		if(count($allmatches))
		{
			echo '<p style="color:red">Warning, '.str_replace($GO_CONFIG->root_path, '', $file).' contains: <br /><br /> '.implode('<br />',$allmatches).'<br /><br /> These lines must be removed in translations. Make the language files writable to automatically remove them.</p>';
		}
		
		if(strstr($str, '//require'))
		{
				echo '<p style="color:red">Warning, you must uncomment //require($GO_LANGUAGE->get_fallback_language_file(\'module\')); make the language files writable to let this script correct it.</p>';
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
		
		check_namespace($file2);
	
		compare_arrays($content1, $content2, $file2);
		compare_arrays($content2, $content1, $file1);
	}
}



require_once($GO_CONFIG->class_path.'filesystem.class.inc');
$fs = new filesystem();
$commons = $fs->get_folders($GO_CONFIG->root_path.'language');

foreach($commons as $common){
	echo '<h3>'.$common['path'].'</h3>';
	compare_files($common['path'].'/'.$lang1.'.inc.php', $common['path'].'/'.$lang2.'.inc.php', 'php');
	compare_files($common['path'].'/'.$lang1.'.js', $common['path'].'/'.$lang2.'.js', 'js');
	echo '<hr>';
}

foreach($modules as $module)
{
	echo '<h3>MODULE: '.$module['id'].'</h3>';

	compare_files($module['path'].'language/'.$lang1.'.inc.php', $module['path'].'language/'.$lang2.'.inc.php', 'php');
	compare_files($module['path'].'language/'.$lang1.'.js', $module['path'].'language/'.$lang2.'.js', 'js');
	
	echo '<hr>';
}
echo '</font></body></html>';
?>
</body>
</html>
