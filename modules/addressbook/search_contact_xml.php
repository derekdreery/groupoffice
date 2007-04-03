<?php
require('../../Group-Office.php');

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('addressbook');


require_once ($GO_MODULES->class_path."addressbook.class.inc");

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


header('Content-Type: text/xml; charset=UTF-8');

echo "<?xml version=\"1.0\" charset=\"UTF-8\" ?>\r\n";
echo '<contacts>';
$ab = new addressbook();
$ab->search_contacts($GO_SECURITY->user_id, '%'.smart_addslashes($_REQUEST['query']).'%','name',0,0,0,10);
while($ab->next_record(MYSQL_ASSOC))
{
	$name = format_name($ab->f('last_name'),$ab->f('first_name'),$ab->f('middle_name'),'first_name');
	$salutation = $ab->f('middle_name')=='' ? $ab->f('last_name') : $ab->f('middle_name').' '.$ab->f('last_name');
	echo '<contact>';
	echo '<salutation>'.htmlspecialchars($sir_madam[$ab->f('sex')].' '.$salutation).'</salutation>';
	echo '<name>'.htmlspecialchars($name).'</name>';
	foreach($ab->Record as $key=>$value)
	{
		echo '<'.$key.'>'.htmlspecialchars($value).'</'.$key.'>';
	}
	echo '</contact>';
}
echo '</contacts>';
