<?php
// $Id: find.php,v 1.1 2004/01/12 22:14:05 comsubvie Exp $

require_once(TemplateDir . '/common.php');

// The find template is passed an associative array with the following
// elements:
//
//   find      => A string containing the text that was searched for.
//   pages     => A string containing the XHTML markup for the list of pages
//                found containing the given text.

function template_find($args)
{
  template_common_prologue(array('norobots' => 1,
                                 'title'    => TMPL_Find .' '. $args['find'],
                                 'heading'  => TMPL_Find .' '. $args['find'],
                                 'headlink' => '',
                                 'headsufx' => '',
                                 'toolbar'  => 1));
?>
<div id="body">
<?php print $args['pages']; ?>
</div>
<?php
  template_common_epilogue(array('twin'      => '',
                                 'edit'      => '',
                                 'editver'   => 0,
                                 'history'   => '',
                                 'timestamp' => '',
                                 'nosearch'  => 0));
}
?>
