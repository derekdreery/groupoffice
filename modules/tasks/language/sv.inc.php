<?php
/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @version $Id: en.inc.php 1616 2008-12-17 16:16:28Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 */

//Uncomment this line in new translations!
require($GO_LANGUAGE->get_fallback_language_file('tasks'));

$lang['tasks']['name']= 'Uppgifter';
$lang['tasks']['description']= 'Sätt en beskrivning här';

$lang['link_type'][12]=$lang['tasks']['task']= 'Uppgift';
$lang['tasks']['status']= 'Status';


$lang['tasks']['scheduled_call']= 'Planerat samtal vid %s';

$lang['tasks']['statuses']['NEEDS-ACTION'] = 'Åtgärd krävs';
$lang['tasks']['statuses']['ACCEPTED'] = 'Accepterad';
$lang['tasks']['statuses']['DECLINED'] = 'Avvisad';
$lang['tasks']['statuses']['TENTATIVE'] = 'Tveksam';
$lang['tasks']['statuses']['DELEGATED'] = 'Delegerad';
$lang['tasks']['statuses']['COMPLETED'] = 'Avslutad';
$lang['tasks']['statuses']['IN-PROCESS'] = 'I process';

$lang['tasks']['import_success']= '%s uppgifter importerades';

$lang['tasks']['call']= 'Ring';
?>