<?php
/**
 * @copyright Copyright Intermesh 2006
 * @version $Revision: 1.4 $ $Date: 2006/09/26 13:47:13 $
 * 
 * @author Merijn Schering <mschering@intermesh.nl>

   This program is protected by copyright law and the Group-Office Professional license.

   You should have received a copy of the Group-Office Proffessional license
   along with Group-Office; if not, write to:
   
   Intermesh
   Reitscheweg 37
   5232BX Den Bosch
   The Netherlands   
   
   info@intermesh.nl
   
 * @package billing
 * @category billing
 */

require_once("../../Group-Office.php");

load_basic_controls();

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('filesystem');
require_once ($GO_LANGUAGE->get_language_file('filesystem'));


$task = isset($_POST['task']) ? $_POST['task'] : '';
$return_to = isset($_REQUEST['return_to']) ? $_REQUEST['return_to'] : $_SERVER['HTTP_REFERER'];
$path = $GO_CONFIG->file_storage_path.smart_stripslashes($_REQUEST['path']);
if(substr($path,-1,1)!='/')
{
	$path .= '/';
}
$link_id = smart_stripslashes($_REQUEST['link_id']);
$link_type = smart_stripslashes($_REQUEST['link_type']);

require_once($GO_CONFIG->class_path.'filesystem.class.inc');
$fs = new filesystem();

if(!is_dir($path))
{
	mkdir_recursive($path);
}


switch($task)
{
	case 'upload':
		for ($i = 0; $i < count($_FILES['file']['tmp_name']); $i ++) {
			if (is_uploaded_file($_FILES['file']['tmp_name'][$i])) {
				$destination =$path.$_FILES['file']['name'][$i];
				move_uploaded_file($_FILES['file']['tmp_name'][$i], $destination);

				$file_link_id = $fs->get_link_id($destination);
				$GO_LINKS->add_link($link_id, $link_type, $file_link_id, 6);
			}
		}
		header('Location: '.$return_to);
		exit();
		break;
}



$form = new form('billing_form');

$form->add_html_element(new input('hidden', 'path',smart_stripslashes($_REQUEST['path']),false));
$form->add_html_element(new input('hidden', 'link_id',$link_id,false));
$form->add_html_element(new input('hidden', 'link_type',$link_type,false));
$form->add_html_element(new input('hidden', 'task','',false));
$form->add_html_element(new input('hidden', 'close','false',false));
$form->add_html_element(new input('hidden', 'return_to',$return_to,false));


$GO_HEADER['head'] = '<script type="text/javascript" language="javascript" src="'.$GO_CONFIG->host.'javascript/multifile.js"></script>';
$GO_HEADER['head'] .= '<style>.deleteButton{background-image:url(\''.$GO_THEME->images['delete'].'\');width:16px;height:16px;cursor:default;display:inline;background-repeat:no-repeat;margin-left:5px;</style>';

$form->add_html_element(new input('hidden','MAX_FILE_SIZE', $GO_CONFIG->max_file_size));
$form->set_attribute('enctype', 'multipart/form-data');
$tabstrip = new tabstrip('upload_tab', $fbUpload);
$tabstrip->set_return_to(htmlspecialchars($return_to));
$tabstrip->set_attribute('style','width:100%');

if (isset($feedback))
{
	$p = new html_element('p', $feedback);
	$p->set_attribute('class','Error');
	$tabstrip->add_html_element($p);
}

$tabstrip->add_html_element(new html_element('p', $fbSelect.':'));

$input = new input('file','file[]');
$input->set_attribute('id', 'file_element');
$input->set_attribute('size','50');

$tabstrip->add_html_element($input);
/*if($GO_CONFIG->use_jupload)
{
$tabstrip->add_html_element(new button($fbMultipleFiles, 'javascript:openPopup(\'upload\',\''.
$GO_CONFIG->control_url.'JUpload/jupload.php?post_url='.
urlencode($GO_MODULES->modules['billing']['full_url'].'jupload.php?sid='.session_id()).'&onunload=opener.upload()\',\'640\',\'400\');', '120'));
}	*/

$p = new html_element('p',
$fbMaxUploadSize.": ".format_size($GO_CONFIG->max_file_size)." (".
number_format($GO_CONFIG->max_file_size, 0,
$_SESSION['GO_SESSION']['decimal_seperator'],
$_SESSION['GO_SESSION']['thousands_seperator'])." bytes)");


$tabstrip->add_html_element($p);

$p = new html_element('p');
$p->set_attribute('id', 'status');
$p->set_attribute('class', 'Success');


$tabstrip->add_html_element($p);


$tabstrip->add_html_element(new button($cmdOk, "javascript:upload()"));
$tabstrip->add_html_element(new button($cmdCancel, "javascript:document.location='".htmlspecialchars($return_to)."';"));

$div = new html_element('div', ' ');
$div->set_attribute('id', 'files_list');
$div->set_attribute('style', 'margin-top:10px;');
$tabstrip->add_html_element($div);

$form->add_html_element($tabstrip);

$form->innerHTML .= "<script>
<!-- Create an instance of the multiSelector class, pass it the output target and the max number of files -->
var multi_selector = new MultiSelector( document.getElementById( 'files_list' ), 0 );
<!-- Pass in the file element -->
multi_selector.addElement( document.getElementById( 'file_element' ) );
</script>";







require_once($GO_THEME->theme_path.'header.inc');
echo $form->get_html();
?>
<script type="text/javascript">

function upload()
{
	var status = null;
	if (status = get_object("status"))
	{
		status.innerHTML = "<?php echo $fbPleaseWait; ?>";
	}
	document.forms[0].task.value='upload';
	document.forms[0].submit();
}

</script>
<?php
require_once($GO_THEME->theme_path.'footer.inc');
