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
<script language="javascript" type="text/javascript" src="<?php echo $GO_CONFIG->host; ?>javascript/common.js"></script>
<title><?php echo $GO_CONFIG->title; ?>
</title>
<link href="<?php echo $GO_THEME->theme_url.'css/common.css'; ?>" rel="stylesheet" type="text/css" />
<?php require($GO_CONFIG->control_path.'fixpng.inc'); ?>
<link rel="shortcut icon" href="<?php echo $GO_CONFIG->host; ?>lib/favicon.ico" />
</head>
<body style="padding:0px;margin:0px;" dir="<?php echo $htmldirection; ?>" onblur="document.search_form.reset();">
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="23">
<tr>
	<td class="HeaderBar" style="color:white;text-align:left;padding-left:5px;vertical-align:top;padding-top:4px;">
	<?php echo $strLoggedInAs.' '.htmlspecialchars($_SESSION['GO_SESSION']['name']); ?>
	</td>
    <td class="HeaderBar" style="padding-top:2px;">
    
    <?php
    load_basic_controls();
    $form = new form('search_form','post',$GO_CONFIG->control_url.'/select/global_select.php');
    $form->set_attribute('style','margin:0px;');
    $form->set_attribute('target','main');
    $input = new input('text','query',$cmdSearch.'...');
    $input->set_attribute('onfocus',"javascript:this.value='';");
    // $input->set_attribute('onblur',"javascript:this.value='".$cmdSearch."...';");
    $input->set_attribute('style','background-color:#22437f;border:1px solid white;color:white;padding-left:1px;');

    $img = new image('magnifier');
    $img->set_attribute('style','border:0px;margin-right:3px;');
    $img->set_attribute('align','absmiddle');

    $form->add_html_element($img);
    $form->add_html_element($input);

    $img = new image('configuration');
    $img->set_attribute('style','border:0px;margin-right:3px;');
    $img->set_attribute('align','absmiddle');

    $link = new hyperlink($GO_CONFIG->host.'configuration/',$img->get_html().$menu_configuration);
    $link->set_attribute('target','main');
    $link->set_attribute('class','HeaderBar');

    $form->add_html_element($link);

    $img = new image('help');
    $img->set_attribute('style','border:0px;margin-right:3px;');
    $img->set_attribute('align','absmiddle');

    $link = new hyperlink($GO_CONFIG->host.'doc/index.php',$img->get_html().$menu_help);
    $link->set_attribute('target','main');
    $link->set_attribute('class','HeaderBar');

    $form->add_html_element($link);

    $img = new image('logout');
    $img->set_attribute('style','border:0px;margin-right:3px;');
    $img->set_attribute('align','absmiddle');

    $link = new hyperlink($GO_CONFIG->host.'index.php?task=logout',$img->get_html().$menu_logout);
    $link->set_attribute('class','HeaderBar');
    $link->set_attribute('target','_top');

    $form->add_html_element($link);

    echo $form->get_html();
    ?>
    </td>
</tr>
</table>
</body>
</html>
