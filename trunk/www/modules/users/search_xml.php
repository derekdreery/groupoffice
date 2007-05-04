<?php
require('../../Group-Office.php');

$GO_SECURITY->authenticate();

/*
header('Content-Type: text/xml; charset: UTF-8');
// create a new XML document
$doc = new DomDocument('1.0');

// create root node
$root = $doc->createElement('forms');
$root = $doc->appendChild($root);

$forms = new forms();
$forms->search_forms(smart_addslashes($_REQUEST['query']));

while($forms->next_record(MYSQL_ASSOC))
{
	$folderNode = $doc->createElement('form');
	$folderNode = $root->appendChild($folderNode);
	
	$fieldNode = $doc->createElement('id');
	$fieldNode = $folderNode->appendChild($fieldNode);
	
	$value = $doc->createTextNode($forms->f('id'));
	$value = $fieldNode->appendChild($value);

	
	$fieldNode = $doc->createElement('name');
	$fieldNode = $folderNode->appendChild($fieldNode);
	
	$value = $doc->createTextNode($forms->f('name'));
	$value = $fieldNode->appendChild($value);

}

echo $doc->saveXML();*/

header('Content-Type: text/xml; charset: UTF-8');

echo "<?xml version=\"1.0\" ?>\r\n";
echo '<users>';



$GO_USERS->search('%'.smart_addslashes($_REQUEST['query']).'%','name',$GO_SECURITY->user_id, 0,10);

while($GO_USERS->next_record(MYSQL_ASSOC))
{
	$salutation = $GO_USERS->f('middle_name')=='' ? $GO_USERS->f('last_name') : $GO_USERS->f('middle_name').' '.$GO_USERS->f('last_name');
	echo '<user><id>'.$GO_USERS->f('id').'</id>'.
	'<salutation>'.htmlspecialchars($sir_madam[$GO_USERS->f('sex')].' '.$salutation).'</salutation>'.
	'<name>'.htmlspecialchars(format_name($GO_USERS->f('last_name'), $GO_USERS->f('first_name'),$GO_USERS->f('middle_name'),'first_name')).'</name>';
	
	
	foreach($GO_USERS->Record as $key=>$value)
	{
		echo '<'.$key.'>'.$value.'</'.$key.'>';
	}
	echo '</user>';
}
echo '</users>';
