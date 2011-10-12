{php}
global $cms, $GO_CONFIG;

function send_mail_to_webmaster($subject,$body) {
	global $co, $GO_CONFIG;
	require_once($GO_CONFIG->class_path.'mail/GoSwift.class.inc.php');
	$swift = new GoSwift('admin@intermesh.dev', $subject);
	//$swift = new GoSwift($co->site['webmaster'], $subject);
	$swift->set_body('<div style="font:12px arial;">'.$body.'</div>');
	$swift->set_from($co->site['webmaster'], $co->site['name']);
	$swift->sendmail();
}

$table = '';

global $GO_MODULES;
require_once($GO_MODULES->modules['addressbook']['class_path'].'addressbook.class.inc.php');
require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
$ab = new addressbook();
$cf = new customfields();

// GET CUSTOMFIELD DATANAMES
$cf_cat = $cf->get_category_by_name(3, $this->_tpl_vars['item']['option_values']['cf_category']);

if (empty($cf_cat)) {
	send_mail_to_webmaster(
		'Ongeldige extra veld categorie op pagina '.$this->_tpl_vars['item']['name'],
		'Zorgt u er a.u.b. voor dat deze pagina een extra veld categorie heeft zodat de deelnemers correct getoond kunnen worden.');
}

$cf->get_fields($cf_cat['id']);
$cf_ids = array();
while ($field = $cf->next_record()) {
	switch ($field['id']) {
		case substr($this->_tpl_vars['item']['option_values']['type_cf'],4):
			$cf_ids['type_cf'] = 'col_'.$field['id'];
			break;
		case substr($this->_tpl_vars['item']['option_values']['logo_cf'],4):
			$cf_ids['logo_cf'] = 'col_'.$field['id'];
			break;
		case substr($this->_tpl_vars['item']['option_values']['subtext_cf'],4):
			$cf_ids['subtext_cf'] = 'col_'.$field['id'];
			break;
	}
}

// GET COMPANIES FROM SELECTED ADDRESSBOOK
$addressbook = $ab->get_addressbook_by_name($this->_tpl_vars['item']['option_values']['addressbook']);
$ab->get_companies($addressbook['id']);

while ($company = $ab->next_record()) {
	$fields = $cf->get_fields_with_values(1, 3, $company['id']);
	foreach ($fields as $field) {
		switch ($field['dataname']) {
			case $cf_ids['type_cf']:
				$company['type_cf'] = $field['value'];
				break;
			case $cf_ids['logo_cf']:
				$company['logo_cf'] = $field['value'];
				break;
			case $cf_ids['subtext_cf']:
				$company['subtext_cf'] = $field['value'];
				break;
		}
	}
	if (!empty($company['logo_cf']))
		$companies[] = $company;
}

$the_company = $companies[rand(0,count($companies)-1)];

$download_path_prefix = $GO_CONFIG->orig_full_url.'controls/thumb.php?src=';
$this->assign('comp_img','<img src="'.$download_path_prefix.$the_company['logo_cf'].'&w=122&h=105&zc=1" alt="'.$the_company['name'].'" />');
{/php}

<div id="partner-panel">
	{$comp_img}
	<div id="partner-panel-bar">PARTNERS</div>
</div>