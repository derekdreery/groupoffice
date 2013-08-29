<?php
//$this->render('externalHeader');
?>

<p>Enter your regional settings and click on continue to complete the trial:</p>

<?php
//GO_Base_Html_Form::renderBegin('servermanager/trial/createtrial', 'createtrial', true);
GO_Base_Html_Form::renderBegin(false, 'createtrial', true);


GO_Base_Html_Select::render(array(
		"required" => true,
		'label' => 'Country',
		'value' => GO::config()->default_country,
		'name' => "default_country",
		'options' => GO::language()->getCountries()
));

GO_Base_Html_Select::render(array(
		"required" => true,
		'label' => 'Language',
		'value' => GO::config()->language,
		'name' => "language",
		'options' => GO::language()->getLanguages()
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

GO_Base_Html_Input::render(array(
		"required" => true,
		"label" => "Date separator",
		"name" => "default_date_separator",
		"value" => GO::config()->default_date_separator
));

$timeFormats = array();
foreach (GO::config()->time_formats as $format)
	$timeFormats[$format] = trim(str_replace(array('h','H','a','i',':'),array('24h ','12h ','','',''), $format));

GO_Base_Html_Select::render(array(
		"required" => true,
		'label' => 'Time format',
		'value' => GO::config()->default_time_format,
		'name' => "default_time_format",
		'options' => $timeFormats
));

GO_Base_Html_Select::render(array(
		"required" => true,
		'label' => 'First weekday',
		'value' => GO::config()->default_first_weekday,
		'name' => "first_weekday",
		'options' => array('0'=>'Sunday','1'=>'Monday')
));

GO_Base_Html_Input::render(array(
		"required" => true,
		"label" => "Currency",
		"name" => "default_currency",
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


GO_Base_Html_Submit::render(array(
		"name" => "submit",
		"value" => "Continue"
));

GO_Base_Html_Form::renderEnd();

//$this->render('externalFooter');