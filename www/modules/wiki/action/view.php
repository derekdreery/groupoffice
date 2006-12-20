<?php
// $Id: view.php,v 1.3 2005/02/16 11:42:54 mschering Exp $

require_once('parse/main.php');
require_once('parse/macros.php');
require_once('parse/html.php');
require_once(TemplateDir . '/view.php');
require_once('lib/headers.php');

// Parse and display a page.
function action_view()
{
  global $page, $pagestore, $ParseEngine, $version;

  $pg = $pagestore->page($page);
  if($version != '')
    { $pg->version = $version; }
  $pg->read();

  gen_headers($pg->time);
  template_view(array('page'      => $page,
                      'html'      => parseText($pg->text, $ParseEngine, $page),
                      'editable'  => $pg->mutable,
                      'timestamp' => $pg->time,
                      'archive'   => $version != '',
                      'version'   => $pg->version));
}
