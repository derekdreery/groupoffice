<?php
// $Id: find.php,v 1.2 2005/02/16 11:42:54 mschering Exp $

require_once('parse/html.php');
require_once(TemplateDir . '/find.php');

// Find a string in the database.
function action_find()
{
  global $pagestore, $find, $style, $SeparateLinkWords;

  $list = $pagestore->find($find);

  switch ($style) {
    case 'meta':
      $SeparateLinkWords = 0;
      break;
  }

  $text = '';
  foreach($list as $page)
    { $text = $text . html_ref($page, $page) . html_newline(); }

  template_find(array('find'  => $find,
                      'pages' => $text));
}
?>
