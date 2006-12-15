<?php
// $Id: macro.php,v 1.2 2005/02/16 11:42:54 mschering Exp $

require_once('parse/macros.php');
require_once('parse/html.php');

// Execute a macro directly from the URL.
function action_macro()
{
  global $ViewMacroEngine, $macro, $parms;

  if(!empty($ViewMacroEngine[$macro]))
  {
    print $ViewMacroEngine[$macro]($parms);
  }
}
?>
