<?php
/************************************************************************/
/* TTS: Ticket tracking system                                          */
/* ============================================                         */
/*                                                                      */
/* Copyright (c) 2002 by Meir Michanie                                  */
/* http://www.riunx.com                                                 */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
require_once("../../Group-Office.php");

//authenticate the user
//if $GO_SECURITY->authenticate(true); is used the user needs admin permissons

$GO_SECURITY->authenticate();
require_once($GO_LANGUAGE->get_language_file('opentts'));

//see if the user has access to this module
//for this to work there must be a module named 'example'
$GO_MODULES->authenticate('opentts');

//set the page title for the header file
$page_title = "Opentts";

require_once($GO_THEME->theme_path."header.inc");

$tts= new db();
require_once("classes.php");


if (!Security::is_action_allowed("set_money")){$money='0.00';}
$my_ticket= new Ticket();

$t_status="1";
$t_stage="1";
$post_date=time();
$change_date=$post_date;
if (!isset($_POST['due_date_d_m_y'])) $due_date=$change_date;
	else {
		$due_date_d_m_y=$_POST['due_date_d_m_y'];
		$due_date_h=$_POST['due_date_h'];
		$due_date_i=$_POST['due_date_i'];
		$due_date=date_to_unixtime("$due_date_d_m_y $due_date_h:$due_date_i:00");
	}
if (!isset($_POST['end_date_d_m_y'])) $end_date=$change_date;
        else {
		$end_date_d_m_y=$_POST['end_date_d_m_y'];
                $end_date_h=$_POST['end_date_h'];
                $end_date_i=$_POST['end_date_i'];
		$end_date=date_to_unixtime("$end_date_d_m_y $end_date_h:$end_date_i:00");
	}


# NEEDS TO BE FIXED
$t_subject='';
if (Security::is_action_allowed("imperson") and isset($_POST['t_from']) ) {
	$t_from=Security::sqlsecure($_POST['t_from']);
	if ($t_from<>whoami()){
		$t_subject="(submitted by ".whatsmyname(whoami()).") : ";
		}
}else{
	$t_from= $GO_SECURITY->user_id;
}
$my_ticket->issuer=$t_from;

if (!isset($_POST['t_assigned'])) $t_assigned=whoami();
if (!isset($_POST['t_priority'])) $t_priority=1;
if(!isset($_POST['project_id'])) $project_id=1;
if(isset($_POST['t_status'])) $my_ticket->status_id=Security::sqlsecure($_POST['t_status']);

if(Security::is_action_allowed("set_assigned") and isset($_POST['t_assigned'])){
	 $my_ticket->assigned_id=Security::sqlsecure($_POST['t_assigned']);
}else{
	$my_ticket->assigned_id=$t_from;
}
if(isset($_POST['stage_id'])) $my_ticket->stage_id=Security::sqlsecure($_POST['t_stage']);
if(isset($_POST['t_category'])) $my_ticket->category_id=Security::sqlsecure($_POST['t_category']);
if(isset($_POST['t_priority'])) $my_ticket->priority_id=Security::sqlsecure($_POST['t_priority']);
if(isset($_POST['t_subject']) && $_POST['t_subject']) $my_ticket->subject="$t_subject".Security::sqlsecure($_POST['t_subject']);
if(isset($_POST['t_description']) && $_POST['t_description']) $my_ticket->description=Security::sqlsecure($_POST['t_description']);
$my_ticket->due_date=$due_date;
$my_ticket->end_date=$end_date;
$my_ticket->post_date=$post_date;
$my_ticket->change_date=$change_date;

if(isset($_POST['complete'])) $my_ticket->complete=Security::sqlsecure($_POST['complete']);
if(isset($_POST['t_priv_msg'])) $my_ticket->notify_priv_msg=Security::sqlsecure($_POST['t_priv_msg']);
if(isset($_POST['t_email'])) $my_ticket->notify_email=Security::sqlsecure($_POST['t_email']);
if(isset($_POST['project_id'])) $my_ticket->project_id=Security::sqlsecure($_POST['project_id']);
$acl_read = $GO_SECURITY->get_new_acl('ticket read');
$acl_write = $GO_SECURITY->get_new_acl('ticket write');
$my_ticket->acl_read = $acl_read;
$my_ticket->acl_write = $acl_write;
$GO_SECURITY->add_user_to_acl($GO_SECURITY->user_id, $acl_read);
$GO_SECURITY->add_user_to_acl($my_ticket->assigned_id, $acl_write);

$my_ticket->sql_insert();
$Ticket_Number= $my_ticket->ticket_nr;
$textmenu=menu("Show_Tickets",'');
eval($textmenu);
$tabtable = new tabtable('newticket_tabtable', 'Adding new ticket ...', '100%', '400');
$tabtable->print_head();
$statusbar = new statusbar();
$statusbar->info_text = "Adding new ticket";
$statusbar->turn_red_point = 90;
echo "<center><br><br>".$statusbar->print_bar(95, 100);

echo "<script language=\"Javascript\" type=\"text/javascript\">
        <!--
        function gotoThread(){
        window.location.href=\"showline.php?Ticket_Number=$Ticket_Number\";
        }
        window.setTimeout(\"gotoThread()\", 3000);
        //-->
        </script>

";
$tabtable->print_foot();
?>
