<?php
require_once("../Group-Office.php");

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
			echo '<i>Ontbrekend in '.$file.':</i><br />';
			foreach($output as $out)
				echo htmlentities($out).'<br />';
			echo '<br />';
		}
	}
}

function compare_files($file1, $file2, $type)
{
	$content1 = get_contents($file1);
	$content2 = get_contents($file2);
	
	if(!$content1 || !$content2)
		echo '<i><font color="red">Kan '.$type.' bestanden niet vergelijken, want een van de vertalingen bestaat niet!</font></i><br />';
	else
	{
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