<?php
/*if(isset($argv[1]))
{
	define('CONFIG_FILE', $argv[1]);
}*/


require('../../www/Group-Office.php');


$s = file_get_contents($argv[1]);

//$s = substr($s, $i, 2000);
//echo $s;
echo String::clean_utf8($s);

exit();
while($i<strlen($s)) {
	$valid = String::valid_utf8_chr($s,$i,$bytes);

	$chr = substr($s,$i,$bytes);
	if($valid){		
		$news .= $chr;
	}else
	{
		$news .= '?';
	}

	echo $valid.':'.$bytes.':'.$chr."\n";

	//$valid = String::utf8_is_valid($chr) ? '1' : '0';
	//echo $ord.':'.$valid.':'.$chr."\n";
	#if($ord){
	#$news.=substr($s,$i,$bytes);
	#}
	$i+=$bytes;
}

//echo $news;
?>
