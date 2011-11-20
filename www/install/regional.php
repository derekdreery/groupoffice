<?php
require('header.php');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {	
	if(GO_Base_Html_Input::checkRequired()){
		foreach($_POST as $key=>$value)
			GO::config()->$key=$value;
		
		GO::config()->save();
		redirect("database.php");
	}
}


printHead();
?>
<h1>Regional settings</h1>

<?php
GO_Base_Html_Select::render(array(
		"required" => true,
		'label' => 'Language',
		'value' => GO::config()->language,
		'name' => "language",
		'options' => GO_Base_Language::getLanguages()
));

$tz = array();
foreach (DateTimeZone::listIdentifiers() as $id)
	$tz[$id] = $id;

GO_Base_Html_Select::render(array(
		"required" => true,
		'label' => 'Timezone',
		'value' => GO::config()->default_timezone,
		'name' => "default_timezone",
		'options' => $tz
));

$dateFormats = array();
foreach (GO::config()->date_formats as $format)	
	$dateFormats[$format] = str_replace(array('Y','m','d'),array('Year ','Month ','Day '), $format);

GO_Base_Html_Select::render(array(
		"required" => true,
		'label' => 'Date format',
		'value' => GO::config()->default_date_format,
		'name' => "default_date_format",
		'options' => $dateFormats
));


$timeFormats = array();
foreach (GO::config()->time_formats as $format)	
	$timeFormats[$format] = trim(str_replace(array('G','g','a','i',':'),array('24h ','12h ','',''), $format));

GO_Base_Html_Select::render(array(
		"required" => true,
		'label' => 'Time format',
		'value' => GO::config()->default_time_format,
		'name' => "default_time_format",
		'options' => $timeFormats
));


GO_Base_Html_Input::render(array(
		"required" => true,
		"label" => "Currency",
		"name" => "currency",
		"value" => GO::config()->default_currency
));

GO_Base_Html_Input::render(array(
		"required" => true,
		"label" => "Decimal point",
		"name" => "default_decimal_separator",
		"value" => GO::config()->default_decimal_separator
));


GO_Base_Html_Input::render(array(
		"required" => true,
		"label" => "Thousands separator",
		"name" => "default_thousands_separator",
		"value" => GO::config()->default_thousands_separator
));

continueButton();
printFoot();

