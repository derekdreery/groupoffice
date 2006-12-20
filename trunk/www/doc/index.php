<?php
/*
   Copyright Intermesh 2003
   Author: Merijn Schering <mschering@intermesh.nl>
   Version: 1.0 Release date: 08 July 2003

   This program is free software; you can redistribute it and/or modify it
   under the terms of the GNU General Public License as published by the
   Free Software Foundation; either version 2 of the License, or (at your
   option) any later version.
 */

require_once("../Group-Office.php");
$GO_SECURITY->authenticate();

require_once($GO_LANGUAGE->get_fallback_base_language_file('preferences'));

if (!file_exists($GO_CONFIG->root_path.'doc'.$GO_CONFIG->slash.$GO_LANGUAGE->language['language_file'].'.pdf'))
{
  require_once($GO_CONFIG->class_path."filesystem.class.inc");
  $fs = new filesystem();

  require_once($GO_CONFIG->root_path.'/language/languages.inc');

  $page_title = $menu_manual;
  require_once($GO_THEME->theme_path."header.inc");
  echo '<table border="0" width="100%" cellpadding="10"><tr><td align="center"><table border="0">';
  echo '<tr><td><h2>'.$pref_language.':</h2><br /></td></tr>';
  $files = $fs->get_files_sorted($GO_CONFIG->root_path.'doc', 'basename', 'asc');
  while($file = array_shift($files))
  {
    if ($file['name'] != 'index.php')
    {
    	$language_code = strip_extension($file['name']);
    	$language = isset($languages[$language_code]) ? $languages[$language_code]['description'] : $language_code;
      echo '<tr><td><a href="'.$GO_CONFIG->host.'doc'.$GO_CONFIG->slash.$file['name'].'">'.$language.'</a></td></tr>';
    }
  }
  echo '</table></td></tr></table>';
  require_once($GO_THEME->theme_path."footer.inc");

}else
{
  header('Location: '.$GO_CONFIG->host.'doc'.'/'.$GO_LANGUAGE->language['language_file'].'.pdf');
}
?>
