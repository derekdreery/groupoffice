<?php
/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @version $Id$
 * @author Merijn Schering <mschering@intermesh.nl>
 */

//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('tools'));

$lang['tools']['name']= 'Verktyg';
$lang['tools']['description']= 'En modul för att utföra administrativa uppgifter, bl a databasunderhåll.';

$lang['tools']['dbcheck']= 'Databaskontroll';
$lang['tools']['rm_duplicates']= 'Ta bort dubbla kontakter och händelser';

$lang['tools']['backupdb']= 'Backup av databas';
$lang['tools']['index_files']='Indexera alla filer';

$lang['tools']['buildsearchcache']='Skapa sökindex';
$lang['tools']['checkmodules']='Kontrollera moduler';
$lang['tools']['resetState']='Nollställ fönsterpositioner, rutsystem etc.';
?>