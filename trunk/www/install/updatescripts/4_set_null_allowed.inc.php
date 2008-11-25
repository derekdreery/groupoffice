<?php
require_once(dirname(__FILE__).'/../../Group-Office.php');

$db = new db();
$db2 = new db();

$db->query("SHOW TABLES");
while($record=$db->next_record(DB_BOTH))
{
	$tables[]=$record[0];
}

foreach($tables as $table)
{
	$sql = "SHOW FIELDS FROM `$table`";
	$db->query($sql);
	while($record = $db->next_record())
	{
		if($record['Null']='NO' && (eregi('varchar', $record['Type']) || eregi('text', $record['Type'])))
		{
			$sql = "ALTER TABLE `$table` CHANGE `".$record['Field']."` `".$record['Field']."` ".$record['Type']." CHARACTER SET utf8 COLLATE utf8_general_ci NULL";
			$db2->query($sql); 
		}
	}
}

?>