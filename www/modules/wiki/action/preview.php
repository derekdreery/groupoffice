<?php
// $Id: preview.php,v 1.2 2005/02/16 11:42:54 mschering Exp $

require_once(TemplateDir . '/preview.php');

// Preview what a page will look like when it is saved.
function action_preview()
{
  global $ParseEngine, $archive;
  global $page, $document, $nextver, $pagestore;

  $document = str_replace("\r", "", $document);
  $pg = $pagestore->page($page);
  $pg->read();

  template_preview(array('page'      => $page,
                         'text'      => $document,
                         'html'      => parseText($document,
                                                  $ParseEngine, $page),
                         'timestamp' => $pg->time,
                         'nextver'   => $nextver,
                         'archive'   => $archive));
}
?>
