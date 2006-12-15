<?php
/*
#usage example:
 php -q cmdtts.php 2 2 "first try from command line" 1 1 1 "nothing much here"


*/
$database="nuke68";
$dbhostname="localhost";
$dbuname="root";
$dbpasswd="";
$prefix="nuke";
$hlpdsk_prefix="_opentts";
if (!$dbi=mysql_connect($dbhostname,$dbuname,$dbpasswd)){
        die("2");
}
if (!mysql_select_db($database)){
        die("3");
}

if ($argv[1]=="--help" or $argc==1)
{
echo "Opentts GPL Licensed\n";
echo "Developed by Meir Michanie\n";
	echo "Usage:";
	echo "php -q cmdtts.php <issuer> <assigned> <subject> <category> <priority> <status> <description> ";
}else{
	$issuer=$argv[1];
	$assigned=$argv[2];
	$subject=$argv[3];
	$stage=$argv[4];
	$priority=$argv[5];
	$category=$argv[6];
	$status=$argv[7];
	$description=$argv[8];
	$post_date=$due_date=$change_date=time();
	
$query="insert into {$prefix}{$hlpdsk_prefix}_tickets (t_assigned,t_from,t_stage,t_priority,t_category,t_subject,t_description,post_date,due_date,change_date,t_status,transac_id) "
." values ( '$assigned','$issuer','$stage','$priority','$category','$subject','$description','$post_date','$due_date','$change_date','$status','$transac_id')";
$res=mysql_query($query,$dbi);
if ($res==1){
	echo "0";
	}else{
	echo "4";
	}
}
?>


