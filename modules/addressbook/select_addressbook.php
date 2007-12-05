<?php
/**
 * @copyright Intermesh 2006
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.4 $ $Date: 2006/11/21 16:25:36 $
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */
 
require_once("../../Group-Office.php");
$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('addressbook');
require_once($GO_LANGUAGE->get_language_file('addressbook'));

load_basic_controls();

require_once($GO_MODULES->class_path.'addressbook.class.inc');
$ab = new addressbook();

$GO_HEADER['nomessages']=true;
require_once($GO_THEME->theme_path."header.inc");


$form = new form('select_form');

$form->add_html_element(new html_element('h1', $ab_select_addressbook));

if(isset($_REQUEST['writable_only']) && $_REQUEST['writable_only']==true)
{
	$ab->get_writable_addressbooks($GO_SECURITY->user_id);
}else
{
	$ab->get_user_addressbooks($GO_SECURITY->user_id);
}

if(isset($_REQUEST['add_null']) && $_REQUEST['add_null']=='true')
{
	$link = new hyperlink('javascript:opener.'.$_REQUEST['callback'].'(0);window.close();', $ab_all_your_addressbooks);
	$link->set_attribute('style','display:block');
	$link->set_attribute('class','normal');
	$form->add_html_element($link);
}

while ($ab->next_record())
{
	$link = new hyperlink('javascript:opener.'.$_REQUEST['callback'].'('.$ab->f('id').');window.close();', $ab->f('name'));
	$link->set_attribute('style','display:block');
	$link->set_attribute('class','normal');
	$form->add_html_element($link);
}
echo $form->get_html();
require_once($GO_THEME->theme_path."footer.inc");
