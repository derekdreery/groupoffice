<?php
// $Id: edit.php,v 1.2 2005/02/16 11:42:54 mschering Exp $

require_once('parse/html.php');
require_once(TemplateDir . '/edit.php');

// Edit a page (possibly an archive version).
function action_edit()
{
  global $page, $pagestore, $ParseEngine, $version;

  $pg = $pagestore->page($page);
  $pg->read();

  if(!$pg->mutable)
    { die(ACTION_ErrorPageLocked); }

  $archive = 0;
  if($version != '')
  {
    $pg->version = $version;
    $pg->read();
    $archive = 1;
  }

  template_edit(array('page'      => $page,
                      'text'      => $pg->text,
                      'timestamp' => $pg->time,
                      'nextver'   => $pg->version + 1,
                      'archive'   => $archive));
}
