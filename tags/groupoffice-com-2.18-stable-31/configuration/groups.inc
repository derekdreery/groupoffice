<?php
$tabstrip->innerHTML .= $groups_you_are_in.': <br />';

$GO_GROUPS->get_groups($GO_SECURITY->user_id);

while($GO_GROUPS->next_record())
{
	$link = new hyperlink("javascript:popup('".$GO_CONFIG->control_url."group.php?group_id=".$GO_GROUPS->f('id')."','300','300');", $GO_GROUPS->f('name'));	
	$link->set_attribute('class','normal');
	$link->set_attribute('style','display:block');
	
	$tabstrip->add_html_element($link);
}