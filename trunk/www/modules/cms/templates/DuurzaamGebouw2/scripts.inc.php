<?php
$this->assign('site_url','http://office.intermesh.nl:81/groupoffice-3.7-demo/www/modules/cms/run.php?site_id=13');
$this->assign('page_base_url','http://office.intermesh.nl:81/groupoffice-3.7-demo/www/modules/cms/run.php?site_id=13&path=');
$_SESSION['GO_SESSION']['DG2']['site_url'] = $this->_tpl_vars['site_url'];
$_SESSION['GO_SESSION']['DG2']['page_base_url'] = $this->_tpl_vars['page_base_url'];

function update_get_parameter($name,$value) {
	echo $get_string;
}

?>
