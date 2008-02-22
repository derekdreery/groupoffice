<?php
/**
 * @copyright Intermesh 2006
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.12 $ $Date: 2006/11/27 09:45:03 $
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */
 
 require_once("../../Group-Office.php");
$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('calendar');
require_once($GO_LANGUAGE->get_language_file('calendar'));


load_basic_controls();
load_control('treeview');

require_once($GO_MODULES->class_path.'calendar.class.inc');
$cal = new calendar();

$GO_HEADER['nomessages']=true;
require_once($GO_THEME->theme_path."header.inc");


$form = new form('select_form');

$form->add_html_element(new html_element('h1', $cal_select_calendar));

$tv = new treeview('calendar_select_tv'); 
$first_opened = (count($tv->nodeState) == 0);
if($first_opened)
{
	$tv->setOpen('cal_views');
	$tv->setOpen('cal_my_cals');	
}

$catNode = new treenode($tv, 'cal_my_cals', $cal_my_cals);
$catNode->childnodesNotLoaded=true;

if($catNode->open)
{
	if($cal->get_user_calendars($GO_SECURITY->user_id,0))
	{
		while($cal->next_record())
		{
			$link = new hyperlink('javascript:select_calendar('.$cal->f('id').');', $cal->f('name'));
			$link->set_attribute('class','normal');
			
			$calNode = new treenode($tv, 'bm_'.$cal->f('id'), $link->get_html());
			$catNode->addNode($calNode);	
		}
	}
}
$tv->addRootNode($catNode);

if(!isset($_REQUEST['noviews']))
{
	$catNode = new treenode($tv, 'cal_views', $cal_views);
	$catNode->childnodesNotLoaded=true;
	if($catNode->open)
	{
		if($cal->get_authorized_views($GO_SECURITY->user_id))
		{
			 while($cal->next_record())
		  {
		  	$link = new hyperlink('javascript:select_view('.$cal->f('id').');', $cal->f('name'));
				$link->set_attribute('class','normal');
				
				$calNode = new treenode($tv, 'bm_'.$cal->f('id'), $link->get_html());
				$catNode->addNode($calNode);	
		  }		    
		}
	}
	$tv->addRootNode($catNode);
}else
{
	$form->add_html_element(new input('hidden', 'noviews', '1'));
}

$cal2 = new calendar();


$resourceNode = new treenode($tv, 'cal_resources', $cal_resources);
$resourceNode->childnodesNotLoaded=true;
if($resourceNode->open)
{
	$cal2->get_resource_groups();
	while($cal2->next_record())
	{
		$catNode = new treenode($tv, 'cal_groups_'.$cal2->f('id'), $cal2->f('name'));
		$catNode->childnodesNotLoaded=true;
		if($cal->get_authorized_calendars($GO_SECURITY->user_id, $cal2->f('id')))
		{			
			if($catNode->open)
			{
				while($cal->next_record())
				{
					//if($cal->f('user_id') != $GO_SECURITY->user_id)
					//{
						$link = new hyperlink('javascript:select_calendar('.$cal->f('id').');', $cal->f('name'));
						$link->set_attribute('class','normal');
						$calNode = new treenode($tv, 'cal_calendars_'.$cal->f('id'), $link->get_html());
						$catNode->addNode($calNode);
					//}
				}
			}					
		}
		$resourceNode->addNode($catNode);
	}
}

$tv->addRootNode($resourceNode);


$catNode = new treenode($tv, 'cal_groups_0', $cal_shared_calendars);
$catNode->childnodesNotLoaded=true;
if($catNode->open)
{
	if($cal->get_authorized_calendars($GO_SECURITY->user_id, 0))
	{
		while($cal->next_record())
		{
			if($cal->f('user_id') != $GO_SECURITY->user_id)
			{
				$link = new hyperlink('javascript:select_calendar('.$cal->f('id').');', $cal->f('name'));
				$link->set_attribute('class','normal');
				$calNode = new treenode($tv, 'cal_calendars_'.$cal->f('id'), $link->get_html());
				$catNode->addNode($calNode);
			}
		}
	}
}
$tv->addRootNode($catNode);

$form->innerHTML .= $tv->getTreeview();
echo $form->get_html();
?>
<script type="text/javascript">
function select_calendar(calendar_id)
{
	opener.document.forms[0].view_id.value=0;
	opener.document.forms[0].calendar_id.value=calendar_id;
	opener.document.forms[0].submit();
	window.close();
}

function select_view(view_id)
{
	opener.document.forms[0].calendar_id.value=0;
	opener.document.forms[0].view_id.value=view_id;
	opener.document.forms[0].submit();
	window.close();
}
</script>
<?php
require_once($GO_THEME->theme_path."footer.inc");
