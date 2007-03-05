<?php
require_once('../../Group-Office.php');
$GO_SECURITY->authenticate();
$charset = isset($charset) ? $charset : 'UTF-8';
$htmldirection= isset($htmldirection) ? $htmldirection : 'ltr';
header('Content-Type: text/html; charset='.$charset);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $charset; ?>">
<link href="<?php echo $GO_THEME->theme_url.'css/common.css'; ?>" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="<?php echo $GO_CONFIG->host; ?>javascript/common.js"></script>
<?php require($GO_CONFIG->control_path.'fixpng.inc'); ?>
<title><?php echo $GO_CONFIG->title; ?></title>
<link rel="shortcut icon" href="<?php echo $GO_CONFIG->host; ?>lib/favicon.ico" />  
</head>
<body style="padding:0px;margin:0px;" dir="<?php echo $htmldirection; ?>" onblur="document.search_form.reset();">
<?php
load_basic_controls();

$form = new form('search_form','post',$GO_CONFIG->control_url.'/select/global_select.php');
$form->set_attribute('target','main');


$table = new table();
$table->set_attribute('id','headerTable');

$row = new table_row();
$cell = new table_cell($strLoggedInAs.' '.htmlspecialchars($_SESSION['GO_SESSION']['name']));
$cell->set_attribute('style', 'width:15%');
$row->add_cell($cell);

$iframe = new html_element('iframe',' ');
$iframe->set_attribute('style','height:20px;width:100%;border:0;');
$iframe->set_attribute('frameborder','0');
$iframe->set_attribute('scrolling','no');
$iframe->set_attribute('name','checker');
$iframe->set_attribute('src',$GO_CONFIG->host.'checker.php');

$cell = new table_cell($iframe->get_html());
$cell->set_attribute('style', 'text-align:right;width:70%');
$row->add_cell($cell);

$cell = new table_cell();
$cell->set_attribute('style', 'text-align:right;width:15%');

$input = new input('text','query',$cmdSearch.'...');
$input->set_attribute('onfocus',"javascript:this.value='';");
$input->set_attribute('onblur',"javascript:document.search_form.reset();");

$img = new image('magnifier');
$img->set_attribute('style','border:0px;margin-left:10px;margin-right:3px;');
$img->set_attribute('align','absmiddle');

$cell->add_html_element($img);
$cell->add_html_element($input);

$img = new image('configuration');
$img->set_attribute('style','border:0px;margin-right:3px;');
$img->set_attribute('align','absmiddle');

$link = new hyperlink($GO_CONFIG->host.'configuration/',$img->get_html().$menu_configuration);
$link->set_attribute('target','main');

$cell->add_html_element($link);

$img = new image('help');
$img->set_attribute('style','border:0px;margin-right:3px;');
$img->set_attribute('align','absmiddle');

$link = new hyperlink($GO_CONFIG->host.'doc/index.php',$img->get_html().$menu_help);
$link->set_attribute('target','main');

$cell->add_html_element($link);

$img = new image('logout');
$img->set_attribute('style','border:0px;margin-right:3px;');
$img->set_attribute('align','absmiddle');

$link = new hyperlink($GO_CONFIG->host.'index.php?task=logout',$img->get_html().$menu_logout);
$link->set_attribute('target','_top');

$cell->add_html_element($link);

$row->add_cell($cell);
$table->add_row($row);

$form->add_html_element($table);

echo $form->get_html();
?>
</body>
</html>
