<?php
require_once(dirname(__FILE__).'/../../Group-Office.php');

$db1 = new db();
$db2 = new db();

$db1->query("SHOW TABLES");
while($record=$db1->next_record(DB_BOTH))
{
	$tables[]=$record[0];
}

if(in_array('ab_contacts', $tables))
{
	$db1->query("UPDATE `ab_contacts` SET birthday = '2010-10-10' WHERE birthday = '0000-00-00';");
	$db1->query("ALTER TABLE `ab_contacts` CHANGE `birthday` `birthday` DATE NULL");
	$db1->query("UPDATE `ab_contacts` SET birthday = NULL WHERE birthday = '2010-10-10';");  
}

$db1->query("UPDATE `go_users` SET birthday = '2010-10-10' WHERE birthday = '0000-00-00';");
$db1->query("ALTER TABLE `go_users` CHANGE `birthday` `birthday` DATE NULL");
$db1->query("UPDATE `go_users` SET birthday = NULL WHERE birthday = '2010-10-10';");  


foreach($tables as $table)
{
	$sql = "SHOW FIELDS FROM `$table`";
	$db1->query($sql);
	while($record = $db1->next_record())
	{
		if($record['Null']='NO' && (stripos($record['Type'],'varchar')!==false || stripos($record['Type'],'text'))!==false)
		{
			$sql = "ALTER TABLE `$table` CHANGE `".$record['Field']."` `".$record['Field']."` ".$record['Type']." CHARACTER SET utf8 COLLATE utf8_general_ci NULL";
			$db2->query($sql); 
		}
	}
}

?>