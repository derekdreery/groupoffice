<?php
// $Id: save.php,v 1.4 2005/02/16 11:42:54 mschering Exp $

// The save template is passed an associative array with the following
// elements:
//
//   page      => A string containing the name of the wiki page being saved.
//   text      => A string containing the wiki markup for the given page.

require_once(TemplateDir . '/view.php');
require_once('lib/headers.php');

function template_save($args)
{
  // You might use this to put up some sort of "thank-you" page like Ward
  //   does in WikiWiki, or to display a list of words that fail spell-check.
  // For now, we simply redirect to the view action for this page.
  // Removed since headers are sent by Group-Office Framework
  //  header('Location: ' . viewURL($args['page']));

  global $pagestore, $ParseEngine;
  $pg = $pagestore->page( $args['page'] );
  $pg->read();

  gen_headers( $pg->time );

  template_common_prologue(
      array('norobots' => $pg->version != '',
	'title'    => $args['page'],
	'heading'  => '',
	'headlink' => $args['page'],
	'headsufx' => '',
	'toolbar'  => 1));

  echo '<div id="body" align="left">';
  print parseText( $pg->text, $ParseEngine, $args['page'] );
  echo '</div>';

  template_common_epilogue(
      array('twin'      => '',
	'edit'      => $args['page'],
	'editver'   => '',
	'history'   => $args['page'],
	'timestamp' => $pg->time,
	'nosearch'  => 0));

}
