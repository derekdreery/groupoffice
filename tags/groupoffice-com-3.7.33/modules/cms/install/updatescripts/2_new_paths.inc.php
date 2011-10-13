<?php


require_once($GO_MODULES->modules['cms']['class_path'].'cms.class.inc.php');
$cms = new cms();

$cms->get_sites();
while($site = $cms->next_record())
{
    $find = 'public%2Fcms%2F'.$site['id'];
    $replace = 'public%2Fcms%2F'.$site['name'];

    $sql = "UPDATE cms_files SET content = REPLACE(content, ?, ?);";
    $db->query($sql, 'ss',array($find, $replace));

		$find = 'public/cms/'.$site['id'];
    $replace = 'public/cms/'.$site['name'];

    $sql = "UPDATE cms_files SET content = REPLACE(content, ?, ?);";
    $db->query($sql, 'ss',array($find, $replace));
}

?>
