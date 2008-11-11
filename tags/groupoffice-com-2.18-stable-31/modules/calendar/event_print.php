<?php

require_once ("../../Group-Office.php");

load_basic_controls();
load_control('dynamic_tabstrip');
load_control('date_picker');
load_control('color_selector');
load_control('datatable');

$GO_HEADER['head'] = "<script type=\"text/javascript\">window.print();</script>" ;

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('calendar');
require_once ($GO_LANGUAGE->get_language_file('calendar'));
require_once ($GO_MODULES->class_path.'calendar.class.inc');
$cal = new calendar();

$cal_settings = $cal->get_settings($GO_SECURITY->user_id);

$event_id = isset ($_REQUEST['event_id']) ? $_REQUEST['event_id'] : 0;

require_once ($GO_THEME->theme_path.'header.inc');
echo $cal->event_to_html($cal->get_event($event_id));
require_once ($GO_THEME->theme_path.'footer.inc');
?>