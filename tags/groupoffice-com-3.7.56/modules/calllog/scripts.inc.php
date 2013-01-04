<?php
require($GO_LANGUAGE->get_language_file('calllog'));

require_once($GO_MODULES->modules['calllog']['class_path'].'calllog.class.inc.php');

if(isset($GO_MODULES->modules['customfields']))
{
	require_once($GO_MODULES->modules['customfields']['class_path'].'customfields.class.inc.php');
	$cf = new customfields();
	$GO_SCRIPTS_JS .= $cf->get_javascript(18, $lang['calllog']['name']);
}

/*
$tickets = new tickets();
$type = $tickets->get_default_type($GO_SECURITY->user_id);

//$autoreply_template = $tickets->get_template_auto_reply();
//$art_id=$autoreply_template ? $autoreply_template['id'] : 0;
$ticket_created_for_client_template = $tickets->get_template_ticket_created_for_client();
$tcfc_id=$ticket_created_for_client_template ? $ticket_created_for_client_template['id'] : 0;


$tickets_bill_item_template = $GO_CONFIG->get_setting('tickets_bill_item_template');
if (!$tickets_bill_item_template) {
	$tickets_bill_item_template = $lang['tickets']['bill_item_template'];
	$GO_CONFIG->save_setting('tickets_bill_item_template',$lang['tickets']['bill_item_template']);
}
$GO_SCRIPTS_JS .= 'GO.tickets.bill_item_template="'.String::escape_javascript($tickets_bill_item_template).'";';

$GO_SCRIPTS_JS .= 'GO.tickets.defaultType = {id:'.$type['id'].', name: "'.String::escape_javascript($type['name']).'", acl_id:'.$type['acl_id'].'};'.

	'GO.tickets.ticketCreatedforClientTemplateID='.$tcfc_id.';';;

$settings = $tickets->get_settings();
$GO_SCRIPTS_JS .= 'GO.tickets.notify_contact="'.$settings['notify_contact'].'";';

*/