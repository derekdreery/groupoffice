<?php
// $Id: edit.php,v 1.1 2004/01/12 22:14:05 comsubvie Exp $

require_once(TemplateDir . '/common.php');

// The edit template is passed an associative array with the following
// elements:
//
//   page      => A string containing the name of the wiki page being edited.
//   text      => A string containing the wiki markup of the wiki page.
//   timestamp => Timestamp of last edit to page.
//   nextver   => An integer; the expected version of this document when saved.
//   archive   => An integer.  Will be nonzero if this is not the most recent
//                version of the page.

function template_edit($args)
{
  global $EditRows, $EditCols, $UserName, $PrefsScript;

  template_common_prologue(array('norobots' => 1,
                                 'title'    => TMPL_Editing .' '. $args['page'],
                                 'heading'  => TMPL_Editing .' ',
                                 'headlink' => $args['page'],
                                 'headsufx' => '',
                                 'toolbar'  => 1));
?>
<div id="body">
<form method="post" action="<?php print saveURL($args['page']); ?>">
<div class="form">
<input type="submit" name="Save" value="<?php echo TMPL_ButtonSave; ?>" />
<input type="submit" name="Preview" value="<?php echo TMPL_ButtonPreview; ?>" />
<?php
  if($UserName != '')
    { print TMPL_YourUsername .' '. html_ref($UserName, $UserName); }
  else
  { echo TMPL_VisitPrefs . "\n"; }
 ?><br />
  <input type="hidden" name="nextver" value="<?php print $args['nextver']; ?>" />
<?php  if($args['archive'])
    {?>
  <input type="hidden" name="archive" value="1" />
<?php  }?>
  <textarea name="document" rows="<?php
    print $EditRows; ?>" cols="<?php
    print $EditCols; ?>" wrap="virtual"><?php
  print str_replace('<', '&lt;', str_replace('&', '&amp;', $args['text']));
?></textarea><br />
  <?php echo TMPL_SummaryOfChange; ?>
  <input type="text" name="comment" size="40" value="" /><br />
  <?php echo TMPL_AddToCategory; ?>
  <input type="text" name="categories" size="40" value="" />
</div>
</form>
</div>
<?php
  template_common_epilogue(array('twin'      => $args['page'],
                                 'edit'      => '',
                                 'editver'   => '',
                                 'history'   => $args['page'],
                                 'timestamp' => $args['timestamp'],
                                 'nosearch'  => 0));
}
?>
