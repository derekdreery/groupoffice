<?php
/* 
This holds all languages of Group-Office. If you find your language to be commented
then it's not up to date.
*/

//DO NOT CHANGE THE DEFAULT
//CHANGE IT AT THE SPECIFIC LANGUAGE
$europe_default['currency'] = '$';
$europe_default['decimal_seperator'] = ',';
$europe_default['thousands_seperator'] = '.';
$europe_default['date_format'] = 'dmY';
$europe_default['date_seperator'] = '-';
$europe_default['time_format'] = 'G:i';
$europe_default['first_weekday'] = 1;
$europe_default['timezone'] = 1;
$europe_default['DST'] = 1;

$languages['nl'] = $europe_default;
$languages['nl']['description'] = 'Nederlands';
$languages['nl']['language_file'] = 'nl';
$languages['nl-be'] = $europe_default;
$languages['nl-be']['language_file'] = 'nl';
$languages['nl-NL'] = $europe_default;

$languages['da'] = $europe_default;
$languages['da']['language_file'] = 'da';
$languages['da']['description'] = 'Dansk';
$languages['da']['currency'] = 'DKK';
$languages['da-dk'] = $europe_default;
$languages['da-dk']['language_file'] = 'da';
$languages['da-dk']['description'] = 'Dansk/Danmark';
$languages['da-dk']['currency'] = 'DKK';

$languages['fr'] = $europe_default;
$languages['fr']['language_file'] = 'fr';
$languages['fr']['description'] = 'Français';
$languages['fr-be'] = $europe_default;
$languages['fr-be']['language_file'] = 'fr';
$languages['fr-ca'] = $europe_default;
$languages['fr-ca']['language_file'] = 'fr';
$languages['fr-lu'] = $europe_default;
$languages['fr-lu']['language_file'] = 'fr';
$languages['fr-mc'] = $europe_default;
$languages['fr-mc']['language_file'] = 'fr';
$languages['fr-ch'] = $europe_default;
$languages['fr-ch']['language_file'] = 'fr';

$languages['de'] = $europe_default;
$languages['de']['language_file'] = 'de';
$languages['de']['description'] = 'Deutsch';
$languages['de-at'] = $europe_default;
$languages['de-at']['language_file'] = 'de';
$languages['de-at']['description'] = 'Deutsch/Austria';
$languages['de-li'] = $europe_default;
$languages['de-li']['language_file'] = 'de';
$languages['de-lu'] = $europe_default;
$languages['de-lu']['language_file'] = 'de';
$languages['de-ch'] = $europe_default;
$languages['de-ch']['language_file'] = 'de';
$languages['de-ch']['description'] = 'Deutsch/Switzerland';

$languages['en'] = $europe_default;
$languages['en']['language_file'] = 'en';
$languages['en']['description'] = 'English';
$languages['en']['decimal_seperator'] = '.';
$languages['en']['thousands_seperator'] = ',';
$languages['en']['first_weekday'] = 1;
$languages['en']['timezone'] = 0;
$languages['en']['DST'] = 0;

$languages['en-us'] = $languages['en'];
$languages['en-us']['description'] = 'English/United States';
$languages['en-us']['currency'] = '$';
$languages['en-us']['date_format'] = 'mdY';
$languages['en-us']['date_seperator'] = '-';
$languages['en-us']['time_format'] = 'g:i a';
$languages['en-us']['timezone'] = 4;
$languages['en-us']['DST'] = 1;

$languages['en-gb'] = $languages['en'];
$languages['en-gb']['description'] = 'English/Great Britain';
$languages['en-gb']['currency'] = 'GBP';
$languages['en-gb']['timezone'] = 0;

$languages['en-au'] = $languages['en'];
$languages['en-au']['description'] = 'English/Australia';
$languages['en-au']['currency'] = '$';
$languages['en-au']['timezone'] = 10;
$languages['en-au']['DST'] = 1;

$languages['en-ie'] = $languages['en'];
$languages['en-ie']['description'] = null;

$languages['en-jm'] = $languages['en'];
$languages['en-jm']['description'] = null;

$languages['en-nz'] = $languages['en'];
$languages['en-nz']['description'] = 'English/New Zealand';
$languages['en-nz']['currency'] = '$';
$languages['en-nz']['first_weekday'] = 1;
$languages['en-nz']['timezone'] = 12;
$languages['en-nz']['DST'] = 1;

$languages['en-ph'] = $languages['en'];
$languages['en-ph']['description'] = null;

$languages['en-za'] = $languages['en'];
$languages['en-za']['description'] = null;

$languages['en-zw'] = $languages['en'];
$languages['en-zw']['description'] = null;

$languages['it'] = $europe_default;
$languages['it']['description'] = 'Italiano';
$languages['it']['language_file'] = 'it';

$languages['sv'] = $europe_default;
$languages['sv']['description'] = 'Svenska';
$languages['sv']['language_file'] = 'sv';
$languages['sv-fi'] = $europe_default;
$languages['sv-fi']['language_file'] = 'sv';

/*
Needs UTF-8 Conversion
$languages['zh_TW_big5'] = $europe_default;
$languages['zh_TW_big5']['description'] = 'Chinese';
$languages['zh_TW_big5']['language_file'] = 'zh_TW_big5';


$languages['pt'] = $europe_default;
$languages['pt']['description'] = 'Português';
$languages['pt']['language_file'] = 'pt';
$languages['pt-br']['description'] = 'Português Brasileiro';
$languages['pt-br']['currency'] = 'R$';
$languages['pt-br']['date_format'] = 'dmY';
$languages['pt-br']['date_seperator'] = '/';
$languages['pt-br']['time_format'] = 'G:i';
$languages['pt-br']['timezone'] = -3;
$languages['pt-br']['DST'] = 1;
$languages['pt-br']['decimal_seperator'] = ',';
$languages['pt-br']['thousands_seperator'] = '.';
$languages['pt-br']['first_weekday'] = 1;



Needs UTF-8 Conversion
$languages['pt-pt'] = $europe_default;
$languages['pt-pt']['language_file'] = 'pt';
*/


$languages['pt-br']['language_file'] = 'pt-br';
$languages['pt-br']['description'] = 'Português Brasileiro';
$languages['pt-br']['currency'] = 'R$';
$languages['pt-br']['date_format'] = 'dmY';
$languages['pt-br']['date_seperator'] = '-';
$languages['pt-br']['time_format'] = 'G:i';
$languages['pt-br']['timezone'] = -3;
$languages['pt-br']['DST'] = 1;
$languages['pt-br']['decimal_seperator'] = ',';
$languages['pt-br']['thousands_seperator'] = '.';
$languages['pt-br']['first_weekday'] = 1;



$languages['es'] = $europe_default;
$languages['es'] = $languages['pt-br'];
$languages['es']['language_file'] = 'es';
$languages['es']['description'] = 'Spanish';


$languages['ca'] = $europe_default;
$languages['ca']['language_file'] = 'ca';
$languages['ca']['description'] = 'Catalan';
$languages['ca-ad'] = $europe_default;
$languages['ca-ad']['language_file'] = 'ca';


/*
Needs UTF-8 conversion
$languages['no'] = $europe_default;
$languages['no']['language_file'] = 'no';
$languages['no']['description'] = 'Norsk';
*/
$languages['bg'] = $europe_default;
$languages['bg']['language_file'] = 'bg';
$languages['bg']['description'] = 'Bulgaria';

/*
Needs UTF-8 Conversion
$languages['sl']['currency'] = 'SIT';
$languages['sl']['decimal_seperator'] = ',';
$languages['sl']['thousands_seperator'] = '.';
$languages['sl']['date_format'] = 'dmY';
$languages['sl']['date_seperator'] = '-';
$languages['sl']['time_format'] = 'G:i';
$languages['sl']['first_weekday'] = 1;
$languages['sl']['timezone'] = 1;
$languages['sl']['DST'] = 1;
$languages['sl']['language_file'] = 'sl';
$languages['sl']['description'] = 'Slovenski';
*/

$languages['he'] = $europe_default;
$languages['he']['language_file'] = 'he';
$languages['he']['description'] = 'Hebrew';

$languages['th'] = $europe_default;
$languages['th']['language_file'] = 'th';
$languages['th']['description'] = 'Thai';

/*
Needs UTF-8 Conversion
$languages['ru'] = $europe_default;
$languages['ru']['language_file'] = 'ru';
$languages['ru']['description'] = 'Russian';
*/

$languages['cs'] = $europe_default;
$languages['cs']['language_file'] = 'cs';
$languages['cs']['description'] = 'Czech';
$languages['cs']['currency'] = 'KÄ';

$languages['ja']['decimal_seperator'] = ',';
$languages['ja']['thousands_seperator'] = '.';
$languages['ja']['description'] = 'Japanese';
$languages['ja']['language_file'] = 'ja';
$languages['ja']['currency'] = 'Y';
$languages['ja']['date_format'] = 'dmY';
$languages['ja']['date_seperator'] = '-';
$languages['ja']['time_format'] = 'g:i a';
$languages['ja']['timezone'] = 9;
$languages['ja']['DST'] = 1;

//Swedish - Finland  -> all texts and desc. as 'sv' but own calendar like 'fi'
$languages['sv-fi'] = $europe_default;
$languages['sv-fi']['description'] = 'Finnish (svenska)';
$languages['sv-fi']['language_file'] = 'sv';
$languages['sv-fi']['thousands_seperator'] = ' ';
$languages['sv-fi']['timezone'] = 2;
$languages['sv-fi']['DST'] = 1;

$languages['fi'] = $europe_default;
$languages['fi']['description'] = 'Finnish';
$languages['fi']['language_file'] = 'fi';
$languages['fi']['thousands_seperator'] = ' ';
$languages['fi']['timezone'] = 2;
$languages['fi']['DST'] = 1;

$languages['hu'] = $europe_default;
$languages['hu']['description'] = 'Hungarian';
$languages['hu']['language_file'] = 'hu';
$languages['hu']['timezone'] = 1;
$languages['hu']['DST'] = 1;

$languages['tr'] = $europe_default;
$languages['tr']['description'] = 'Turkish';
$languages['tr']['language_file'] = 'tr';
$languages['tr']['timezone'] = 1;
$languages['tr']['DST'] = 1;

$languages['si'] = $europe_default;
$languages['si']['description'] = 'Slovenian';
$languages['si']['language_file'] = 'si';
$languages['si']['timezone'] = 1;
$languages['si']['DST'] = 1;


$languages['ar'] = $europe_default;
$languages['ar']['description'] = 'Arabic';
$languages['ar']['language_file'] = 'ar';
$languages['ar']['timezone'] = 1;
$languages['ar']['DST'] = 1;
