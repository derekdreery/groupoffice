<?php
//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('files'));
$lang['files']['name'] = 'Filer';
$lang['files']['description'] = 'Modul för att hantera filer i Group-Office. Filer kan delas mellan användare och grupper. Använd GOTA för att redigera filer från Group-Office lokalt och spara ändringarna till servern.';

$lang['link_type'][6]= 'Fil';
$lang['link_type'][17]='Mapp';

$lang['files']['fileNotFound'] = 'Filen hittades inte';
$lang['files']['folderExists'] = 'Mappen finns redan';
$lang['files']['filenameExists'] = 'Filnamn finns redan';
$lang['files']['uploadedSucces'] = 'Fil uppladdad utan problem';

$lang['files']['ootextdoc']= 'OpenDocument-textdokument';
$lang['files']['wordtextdoc']= 'Microsoft Word-dokument';
$lang['files']['personal']= 'Personlig';
$lang['files']['shared']= 'Delad';

$lang['files']['general']= 'Allmänt';


$lang['files']['folder_modified_subject']= 'Ändringar i Group-Office-mapp';
$lang['files']['folder_modified_body']= 'Du har begärt att bli meddelad när ändringar gjorts i:

%s

Följande ändringar gjordes av %s:

%s
';

$lang['files']['modified']= 'Ändrad';
$lang['files']['new']= 'Ny';
$lang['files']['deleted']= 'Raderad';

$lang['files']['file']= 'Fil';
$lang['files']['folder']= 'Mapp';
$lang['files']['files']='Filer';


$lang['files']['emptyFile']='Tom fil';

$lang['files']['downloadLink'] = 'Länk för nedladdning';
$lang['files']['clickHereToDownload'] = 'Klicka här för att ladda ner filen via en säker länk';
$lang['files']['copyPasteToDownload'] = 'Klicka på den säkrade länken nedan eller kopiera den till adressfältet i din webbläsare för att ladda ner filen.';
$lang['files']['possibleUntil'] = 'möjlig fram till';