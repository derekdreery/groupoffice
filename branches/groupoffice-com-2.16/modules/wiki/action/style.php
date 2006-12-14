<?php
// $Id: style.php,v 1.2 2005/02/16 11:42:54 mschering Exp $

// This function emits the current template's stylesheet.

function action_style()
{
  header("Content-type: text/css");

  ob_start();

  require_once(TemplateDir . '/wiki.css');

  $size = ob_get_length();
  header("Content-Length: $size");
  ob_end_flush();
}
?>
