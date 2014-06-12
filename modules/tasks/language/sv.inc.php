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
 * @version $Id$
 * @author Merijn Schering <mschering@intermesh.nl>
 */

//Uncomment this line in new translations!
require($GLOBALS['GO_LANGUAGE']->get_fallback_language_file('tasks'));

$lang['tasks']['name']= 'Uppgifter';
$lang['tasks']['description']= 'Modul för hantering av uppgifter i en uppgiftslista. Kan ex. kopplas till kalenderhändelser, e-postmeddelanden eller projekt.';

$lang['link_type'][12]=$lang['tasks']['task']= 'Uppgift';
$lang['tasks']['status']= 'Status';


$lang['tasks']['scheduled_call']= 'Planerat in samtal vid %s';

$lang['tasks']['statuses']['NEEDS-ACTION'] = 'Åtgärd krävs';
$lang['tasks']['statuses']['ACCEPTED'] = 'Accepterad';
$lang['tasks']['statuses']['DECLINED'] = 'Avvisad';
$lang['tasks']['statuses']['TENTATIVE'] = 'Preliminär';
$lang['tasks']['statuses']['DELEGATED'] = 'Delegerad';
$lang['tasks']['statuses']['COMPLETED'] = 'Avslutad';
$lang['tasks']['statuses']['IN-PROCESS'] = 'Pågående';

$lang['tasks']['import_success']= '%s uppgifter importerades';

$lang['tasks']['call']= 'Ring';

$lang['tasks']['dueAtdate']='Förfaller vid %s';

$lang['tasks']['list']='Uppgiftslista';
$lang['tasks']['tasklistChanged']="* Uppgiftslista ändrad från '%s' till '%s'";
$lang['tasks']['statusChanged']="* Status ändrad från '%s' till '%s'";
$lang['tasks']['multipleSelected']='Flera uppgiftslistor valda';
$lang['tasks']['incomplete_delete']='Du har inte behörighet att radera alla de valda uppgifterna';