<?php
// $Id: save.php,v 1.2 2005/02/16 11:42:54 mschering Exp $

require_once(TemplateDir . '/save.php');
require_once('lib/category.php');
require_once('parse/save.php');

// Commit an edit to the database.
function action_save()
{
  global $pagestore, $comment, $categories, $archive;
  global $Save, $page, $document, $nextver, $REMOTE_ADDR;
  global $MaxPostLen, $UserName, $SaveMacroEngine;

  if(empty($Save))                      // Didn't click the save button.
  {
    include('action/preview.php');
    action_preview();
    return;
  }

  $pagestore->lock();                   // Ensure atomicity.

  $pg = $pagestore->page($page);
  $pg->read();

  if(!$pg->mutable)                     // Edit disallowed.
    { die(ACTION_ErrorPageLocked); }

  if($pg->exists()                      // Page already exists.
     && $pg->version >= $nextver        // Someone has changed it.
     && $pg->hostname != gethostbyaddr($REMOTE_ADDR)  // Wasn't us.
     && !$archive)                      // Not editing an archive version.
  {
    $pagestore->unlock();
    include('action/conflict.php');
    action_conflict();
    return;
  }

  // Silently trim string to $MaxPostLen chars.

  $document = substr($document, 0, $MaxPostLen);
  $document = str_replace("\r", "", $document);

  $esc_doc = str_replace("\\", "\\\\", $document);
  $esc_doc = str_replace("'", "\\'", $esc_doc);

  $comment = str_replace("\\", "\\\\", $comment);
  $comment = str_replace("'", "\\'", $comment);

  $pg->text = $esc_doc;
  $pg->hostname = gethostbyaddr($REMOTE_ADDR);
  $pg->username = $UserName;
  $pg->comment  = $comment;

  if($pg->exists)
    { $pg->version++; }
  else
    { $pg->version = 1; }
  $pg->write();

  if(!empty($categories))               // Editor asked page to be added to
  {                                     //   a category or categories.
    add_to_category($page, $categories);
  }

  template_save(array('page' => $page,
                      'text' => $document));

  // Process save macros (e.g., to define interwiki entries).
  parseText($document, $SaveMacroEngine, $page);

  $pagestore->unlock();                 // End "transaction".

}
?>
