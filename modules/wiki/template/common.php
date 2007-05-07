<?php
// $Id: common.php,v 1.2 2004/02/26 14:44:51 comsubvie Exp $

// This function generates the common prologue and header
// for the various templates.
//
// Its parameters are passed as an associative array with the following
// members:
//
//   'norobots' => An integer; if nonzero, robots will be forbidden to
//                 index the page or follow links from the page.
//   'title'    => A string containing the page title.  This function
//                 will append ' - WikiName' to the title.
//   'heading'  => A string containing the page's heading.
//   'headlink' => A string.  If not empty, it will be appended to the
//                 page's heading as a link to find the contents of the
//                 string in the database.
//   'headsufx' => A string containing the heading suffix.  If not
//                 empty, it will be printed after the heading link.
//   'toolbar'  => An integer; if nonzero, the toolbar will be displayed.

function template_common_prologue($args)
{
  global $WikiName, $HomePage, $WikiLogo, $MetaKeywords, $MetaDescription;
  global $StyleScript, $SeparateTitleWords, $SeparateHeaderWords;

  $keywords = ' ' . html_split_name($args['headlink']);
  $keywords = str_replace('"', '&quot;', $keywords);

  preg_match("/^(.*)\//", $args['headlink'], $path); // Find startpath of page
  ob_start();                           // Start buffering output.

  if($SeparateTitleWords)
    { $args['title'] = html_split_name($args['title']); }
?>
<!DOCTYPE html
          PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
          "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html   xmlns   = "http://www.w3.org/1999/xhtml">
<head>
<meta name="KEYWORDS" content="<?php print $MetaKeywords . $keywords; ?>" />
<meta name="DESCRIPTION" content="<?php print $MetaDescription; ?>" />
<?php
  if($args['norobots'])
  {
?>
<meta name="ROBOTS" content="NOINDEX, NOFOLLOW" />
<?php
  }
?>
<link rel="STYLESHEET" href="<?php print $StyleScript; ?>" type="text/css" />
<title><?php print $args['title'] . ' - ' . $WikiName; ?></title>
</head>
<body>
<div id="header">
  <div class="logo">
  <a href="<?php print viewURL($HomePage); ?>"><img
    src="<?php print $WikiLogo; ?>" alt="[Home]" /></a>
  </div>
  <h1>
<?php
    print $args['heading'];
    if($args['headlink'] != '')
    {
?>
    <a class="title" href="<?php print findURL($args['headlink']); ?>">
<?php
    if($SeparateHeaderWords)
      { print html_split_name($args['headlink']); }
    else
      { print $args['headlink']; }
?></a>
<?php
    }
    print $args['headsufx'];
?>
  </h1>
  <div id="toolbar-top">
<?php
  if($args['toolbar'])
  {
    if ( isset( $path[1] ) )
    {
      print html_toolbar_top($path[1]);
    } else {
      print html_toolbar_top("");
    }
  }
?>

</div>
<hr />
</div>
<?php
}

// This function generates the common prologue and header
// for the various templates.
//
// Its parameters are passed as an associative array with the following
// members:
//
//   'twin'      => A string containing the page's name; if not empty,
//                  twin pages will be sought and printed.
//   'edit'      => A string containing the page's name; if not empty,
//                  an edit link will be printed.
//   'editver'   => An integer containing the page's version; if not
//                  zero, the edit link will be directed at the given
//                  version.  If it is -1, the page cannot be edited,
//                  and a message to that effect will be printed.
//   'history'   => A string containing the page's name; if not empty,
//                  a history link will be printed.
//   'timestamp' => Timestamp for the page.  If not empty, a 'document
//                  last modified' note will be printed.
//   'nosearch'  => An integer; if nonzero, the search form will not appear.

function template_common_epilogue($args)
{
  global $FindScript, $pagestore;

?>
<div id="footer">
<hr />
<div id="toolbar-bottom">
<?php
  if($args['edit'])
  {
    if($args['editver'] == 0)
    {
      echo '<a href="'. editUrl($args['edit']) . '">'.TMPL_EditDocument.'</a>'; 
    }
    else if($args['editver'] == -1)
    {
      echo TMPL_NoEditDocument;
    }
    else
    {
      echo '<a href="' . editUrl($args['edit'], $args['editver']) . '">'.
           TMPL_EditArchiveVersion . '</a>';
    }

    if($args['history'])
      { print ' | '; }
  }
  if($args['history'])
  {
    echo '<a href="'. historyURL($args['history']). '">'.
         TMPL_ViewDocHistory . '</a><br />';
  }
  if($args['timestamp'])
  {
    echo "\n</div>\n". TMPL_DocLastModified . ' '. 
         html_time($args['timestamp']) . '<br />';
  }
  if($args['twin'] != '')
  {
    if(count($twin = $pagestore->twinpages($args['twin'])))
    {
      echo TMPL_TwinPages . ' ';
      for($i = 0; $i < count($twin); $i++)
        { print html_twin($twin[$i][0], $twin[$i][1]) . ' <br />'; } 
    }
  }
  if(!$args['nosearch'])
  {
?>
<form method="get" action="<?php print $FindScript; ?>">
<div class="form">
  <input type="hidden" name="action" value="find" />
  <?php print TMPL_Search; ?> <input type="text" name="find" size="20" />
</div>
</form>
<?php
  }
?>
</div>
<!-- Commented out since this is done via Group-Office framework
</body>
</html>
-->
<?php
// Following three lines are commented out since this is done via Group-Office framework
//  $size = ob_get_length();
//  header("Content-Length: $size");
//  ob_end_flush();
}
