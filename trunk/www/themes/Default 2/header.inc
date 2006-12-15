<?php
$charset = isset($charset) ? $charset : 'UTF-8';
header('Content-Type: text/html; charset='.$charset);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $charset; ?>">
<?php if (isset($GO_HEADER['head'])) echo $GO_HEADER['head']; ?>
<?php require($GO_CONFIG->control_path.'fixpng.inc'); ?>
<script language="javascript" type="text/javascript" src="<?php echo $GO_CONFIG->host; ?>javascript/common.js"></script>
<link rel="shortcut icon" href="<?php echo $GO_CONFIG->host; ?>lib/favicon.ico" />
<link href="<?php echo $GO_THEME->theme_url.'css/common.css'; ?>" rel="stylesheet" type="text/css" />
<?php
if(file_exists($GO_THEME->theme_path.'css/'.$GO_MODULES->id.'.css'))
{
	echo '<link href="'.$GO_THEME->theme_url.'css/'.$GO_MODULES->id.'.css" type="text/css" rel="stylesheet" />';
}
?>
<title>
<?php
echo $GO_CONFIG->title;
if (isset($page_title))
{
 	echo " - ".$page_title;
}

echo '</title>';
if(isset($GO_HEADER['auto_refresh']))
{	
	$GO_HEADER['auto_refresh']['interval'] = isset($GO_HEADER['auto_refresh']['interval']) ? $GO_HEADER['auto_refresh']['interval'] : $GO_CONFIG->refresh_rate;
	$GO_HEADER['auto_refresh']['action'] = isset($GO_HEADER['auto_refresh']['action']) ? $GO_HEADER['auto_refresh']['action'] : "document.location='".$_SERVER['PHP_SELF']."';";	
	echo '<script type="text/javascript">'.
		'setTimeout("'.$GO_HEADER['auto_refresh']['action'].'", '.($GO_HEADER['auto_refresh']['interval']*1000).');'.	
		'</script>';
}
echo "</head>\r\n";
echo '<body marginwidth="10" marginheight="10" leftmargin="10" topmargin="10"';
//echo ' style="background-image: url('.$GO_THEME->theme_url.'images/background.gif); background-position: center center;background-repeat: no-repeat;"';
if (isset($GO_HEADER['body_arguments'])) echo ' '.$GO_HEADER['body_arguments'];
echo '><div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>';
$active_link = $GO_LINKS->get_active_link();
if($active_link && !isset($GO_HEADER['nomessages']))
{	
	$img = new image('info');
	$img->set_attribute('align','middle');
	$img->set_attribute('style','margin:5px;');
	
	$table = new table();
	$table->set_attribute('class','ErrorBox');
	$table->set_attribute('id','active_link_div');
	$row = new table_row();	
	$row->add_cell(new table_cell($img->get_html()));
	$row->add_cell(new table_cell(sprintf($strLinkText, $_SESSION['GO_SESSION']['link_description'])));
	$table->add_row($row);
	
	$row = new table_row();
	$button = new button($strCancelLink, 'parent.checker.location=\''.$GO_CONFIG->host.'checker.php?task=deactivate_linking\';document.location=\''.$active_link['return_to'].'\';');
	$button->set_attribute('style','margin:0px;');
	$cell = new table_cell($button->get_html());
	
	
	$link =  "javascript:popup('".$GO_CONFIG->control_url."select/select.php?".
		"search_type=contact&multiselect=true&GO_HANDLER=".base64_encode($GO_CONFIG->control_url."select/link.php")."&pass_value=id&".
		"handler_base64_encoded=true&require_email_address=false".
		"&show_contacts=true&show_companies=true&show_projects=true".
		"&show_files=true','620','400');";

	$button = new button ($cmdSearch, $link);
	$cell->add_html_element($button);
	$cell->set_attribute('colspan','2');
	$row->add_cell($cell);	
	$table->add_row($row);	
	echo $table->get_html();	
}
?>
<!--Einde header-->
