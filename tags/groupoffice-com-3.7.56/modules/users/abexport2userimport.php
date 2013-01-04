#!/usr/bin/php
<?php
require('../../Group-Office.php');
$list_sep = ',';
$text_sep = '"';


$cols[]='username';
$cols[]='password';
$cols[]='enabled';
$cols[]='first_name';
$cols[]='middle_name';
$cols[]='last_name';
$cols[]='initials';
$cols[]='title';
$cols[]='sex';
$cols[]='birthday';
$cols[]='email';
$cols[]='company';
$cols[]='department';
$cols[]='function';
$cols[]='home_phone';
$cols[]='work_phone';
$cols[]='fax';
$cols[]='cellular';
$cols[]='country';
$cols[]='state';
$cols[]='city';
$cols[]='zip';
$cols[]='address';
$cols[]='address_no';
$cols[]='homepage';
$cols[]='work_address';
$cols[]='work_address_no';
$cols[]='work_zip';
$cols[]='work_country';
$cols[]='work_state';
$cols[]='work_city';
$cols[]='work_fax';



$type = $argv[1];

$fp = fopen($argv[2], "r");
if(!$fp)
{
	throw new Exception('Could not open uploaded file');
}

$mapping['contacts']=array(
7,
'random',
'1',
1,
2,
3,
4,
0,
5,
6,
7,
21,
22,
23,
16,
17,
18,
20,
10,
11,
12,
13,
14,
15,
'',
'',
'',
'',
'',
'',
'',
19,
'Gastouders',
'',
'hoursapproval',
'',
'1'
);


$mapping['companies']=array(
13,
'pass',
'1',
'Gastouder',
'',
0,
'',
'',
'M',
'',
13,
0,
'',
'',
14,
14,
15,
'',
7,
8,
9,
10,
11,
12,
'',
'',
'',
'',
'',
'',
'',
15,
'Gastouders',
'',
'timeregistration',
'',
'1'
);

if(!isset($mapping[$type]))
{
	exit('abexport2userimport.php contacts contactexport.csv');
}

require_once($GO_CONFIG->class_path.'base/users.class.inc.php');
$GO_USERS = new GO_USERS();

$record = fgetcsv($fp, 4096, $list_sep, $text_sep);
echo $text_sep.implode($text_sep.$list_sep.$text_sep, $cols).$text_sep."\n";
while($record = fgetcsv($fp, 4096, $list_sep, $text_sep)){
	$newcsv_record=array();

	foreach($mapping[$type] as $index){
		if(is_string($index)){
			if($index=='pass')
			{
				$newcsv_record[]=$GO_USERS->random_password();
			}else
			{
				$newcsv_record[]=$index;
			}
		}else
		{
			$newcsv_record[]=$record[$index];
		}
	}
	echo $text_sep.implode($text_sep.$list_sep.$text_sep, $newcsv_record).$text_sep."\n";

}



?>
