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
//The JSCalendar control requires a header too:
$datepicker = new date_picker();
$GO_HEADER['head'] = $datepicker->get_header();
$hours = array("00","01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23");
$mins = array("00","05","10","15","20","25","30","35","40","45","50","55");
$javascript=<<<EOF
<script type="text/javascript" language="javascript">

		function update_end_hour(start_hour)
		{
		  if (start_hour == 24)
		  {
		    document.change_status.end_date_h.value='01';
		  }else
		  {
		    start_hour = parseInt(start_hour)+1
		      if (start_hour < 10)
		      {
			start_hour = "0"+start_hour;
		      }
		    document.change_status.end_date_h.value= start_hour;
		  }
		}

</script>

EOF;

//set the page title for the header file
$page_title = "Opentts";
require_once($GO_THEME->theme_path."header.inc");
require_once("classes.php");

$tts= new db();
$Ticket_Number=isset($_GET['Ticket_Number']) ?  Security::htmlsecure($_GET['Ticket_Number']) : 0;
$Ticket_Number=isset($_POST['Ticket_Number']) ? Security::htmlsecure($_POST['Ticket_Number']) : $Ticket_Number;
echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'" name="ticket_form">';
echo '<input type="hidden" name="Ticket_Number" value="'.$Ticket_Number.'" />';

global $name;
$textmenu=menu("Show_Tickets",'');
eval($textmenu);
//
// take ownership
if (isset($_GET['take_ownership'])){
	$query="select * from acl_items  where id='".Security::sqlsecure($_GET['acl_write'])."' and description='ticket write'";
	$mydb= new db($query);
	if ($mydb->next_record()){
		$query="select * from acl where acl_id='".Security::sqlsecure($_GET['acl_write'])."' and user_id='".whoami()."'";
		$mydb= new db($query);
		if (!$mydb->next_record()){
			$query="insert into acl (acl_id,user_id) values ('".Security::sqlsecure($_GET['acl_write'])."','".whoami()."')";
			$mydb= new db($query);
		}
	}

}
//
$return_to = isset($_REQUEST['return_to']) ? $_REQUEST['return_to'] : $_SERVER['HTTP_REFERER'];
$link_back = isset($_REQUEST['link_back']) ? $_REQUEST['link_back'] : $_SERVER['REQUEST_URI'];
$ticket['acl_read']= get_cross_value("{$prefix}{$hlpdsk_prefix}_tickets","acl_read"," where Ticket_Number='".Security::sqlsecure($Ticket_Number)."'");
$ticket['acl_write']= get_cross_value("{$prefix}{$hlpdsk_prefix}_tickets","acl_write"," where Ticket_Number='".Security::sqlsecure($Ticket_Number)."'");
$acl_read=$ticket['acl_read'];
$acl_write=$ticket['acl_write'];
  $tabtable = new tabtable('ticket_tabtable',$tts_lang_tickets_details , '100%', '400', '120', '', true);
if($acl_read>0 and $acl_write>0){
  $tabtable->add_tab('properties', $strProperties);
  $tabtable->add_tab('new_task', $helpdesk_add_comment);
  $tabtable->add_tab('show_tasks', $tts_lang_comments);
  $tabtable->add_tab('read_permissions', $strReadRights);
  $tabtable->add_tab('write_permissions', $strWriteRights);
}
$tabtable->print_head();
echo '</form>';
echo '<TABLE border=1 cellPadding=1 cellSpacing=1 width="100%">
        <TBODY>
<tr><td bgcolor="#def1f9" colspan=100%><H2 align=center>'.$tts_lang_ticket_number.':'. $Ticket_Number.'</H2></td></tr></table>';

switch ($tabtable->get_active_tab_id())
{
  case 'read_permissions':
    if ($GO_SECURITY->user_in_acl(whoami(),$ticket['acl_read']) or $GO_SECURITY->user_in_acl(whoami(),$ticket['acl_write'])){
    print_acl($ticket['acl_read']);
    echo '<br />';
    $button = new button($cmdClose, "javascript:document.location='".htmlspecialchars($return_to)."';");
    }
    break;

  case 'write_permissions':
    if ($GO_SECURITY->user_in_acl(whoami(),$ticket['acl_write'])){
    print_acl($ticket['acl_write']);
    echo '<br />';
    $button = new button($cmdClose, "javascript:document.location='".htmlspecialchars($return_to)."';");
    }
    $cmdTakeOwnership='Take Ownership';
     if (Security::is_action_allowed('take_ownership')){
     	$button = new button($cmdTakeOwnership,"javascript:document.location='".htmlspecialchars($return_to)."&take_ownership=yes&acl_write=$acl_write';");
	}

    break;
  case 'show_tasks':
	if($acl_read>0 and $acl_write>0) showtasks();
  break;
  case 'new_task':
	if($acl_read>0 and $acl_write>0) show_new_task($Ticket_Number);
  break;
  default:
	if($acl_read>0 and $acl_write>0) showrecords();
  break;
}
$tabtable->print_foot();

//functions

function showtasks($query_condition=''){
	global $Ticket_Number,$name,$tts,$prefix,$hlpdsk_prefix,$hlpdsk_theme,$acl_read,$acl_write,$GO_LANGUAGE;
	require_once($GO_LANGUAGE->get_language_file('opentts'));
	
	if (Security::is_action_allowed("view_tasks",$acl_read,$acl_write)){ 
	$query="select * from {$prefix}{$hlpdsk_prefix}_tasks where ticket_id='$Ticket_Number' $query_condition order by task_id asc";
	if ($tts->query($query)){
		$file="themes/$hlpdsk_theme/showline_task.html";
		$file=addslashes (implode("",file($file)));
		$_MIDDLE='';
		while($tts->next_record()){
		$POST_DATE="<tr><td class=textbox><font class=content>".date($_SESSION['GO_SESSION']['date_format'],$tts->f('post_date'))."<br> ".date("H:i",$tts->f('post_date'))."</td>";
			$SENDER="<td class=textbox><font class=content>".opentts::get_fullname($tts->f('sender_id'))."</td>";
			$comment=nl2br(Security::htmlsecure($tts->f('comment')));
			$COMMENT="<td class=textbox><font class=content>{$comment}</td>";
			$_ACTION="";
			$tts_lang_mail_this="";
			$issuer_email=$tts->f('task_id');
			 $issuer_email=$tts->f('email_issuer');
                        $email_assigned=$tts->f('email_agent');
			if ($issuer_email==0) $issuer_email="--"; else $issuer_email=SECURITY::get_uname($issuer_email);
                        if ($email_assigned==0) $email_assigned="--"; else $email_assigned=SECURITY::get_uname($email_assigned);
		$t_email_issuer="<TD align=center class=textbox>$issuer_email</TD>";
                        $t_email_assigned="<TD align=center class=textbox>$email_assigned</TD></tr>";
                        $_MIDDLE.=$POST_DATE.$SENDER.$COMMENT.$t_email_issuer.$t_email_assigned;

		}
		eval("\$content=stripslashes(\"$file\");");
		echo $content;
	}
	}
}

function show_new_task($ticket_number){
	global	$name,$hlpdsk_theme,$cmdOk,$acl_read,$acl_write,$GO_LANGUAGE,$nuke_user_table,$nuke_user_last_name_fieldname,$nuke_user_id_fieldname,$nuke_user_first_name_fieldname;
	require_once($GO_LANGUAGE->get_language_file('opentts'));
	if (Security::is_action_allowed("enter_new_task",$acl_read,$acl_write)){

	$tts_lang_author_value=opentts::get_fullname(whoami());
	$tts_lang_email.=" $tts_lang_issuer <input type=checkbox name=t_email_issuer value=1><br>$tts_lang_email $tts_lang_assigned <input type=checkbox name=t_email_agent value=1>";
	$tts_lang_comment_value="<textarea name=comment cols=80 rows=12 class=textbox></textarea>";
	$_START_POST="<form name=new_task id=new_task  method=POST action=\"change_ticket.php?func=add_task&Ticket_Number=$ticket_number\">";
	$button = new button();
        $_END_POST=$button->get_button($cmdOk, "javascript:this.form.submit()")."</form>";

	$tts_lang_post_date_value=date("{$_SESSION['GO_SESSION']['date_format']} H:i",time());
	$tts_lang_mail_this='';
	$file="themes/$hlpdsk_theme/showline_new_task.html";
	$file=addslashes (implode("",file($file)));		
	eval("\$file=stripslashes(\"$file\");");
	echo $file;
	}
} 


function showrecords(){
global $Ticket_Number,$name,$tts,$prefix,$hlpdsk_prefix,$hlpdsk_theme,$nuke_user_table,$nuke_user_last_name_fieldname,$nuke_username_fieldname,$hours,$mins,$javascript,
$nuke_user_id_fieldname,$nuke_user_first_name_fieldname,$datepicker,$cmdOk,$cmdReset,$GO_SECURITY,$acl_read,$acl_write,$GO_LANGUAGE;
require_once($GO_LANGUAGE->get_language_file('opentts'));
$acl_read=get_cross_value("{$prefix}{$hlpdsk_prefix}_tickets",'acl_read',"where ticket_number='$Ticket_Number'");
$acl_write=get_cross_value("{$prefix}{$hlpdsk_prefix}_tickets",'acl_write',"where ticket_number='$Ticket_Number'");
if ($GO_SECURITY->user_in_acl(whoami(), $acl_read) or $GO_SECURITY->user_in_acl(whoami(), $acl_write) or Security::is_action_allowed("view_all_tickets")){
$query_condition =" ";
}else{
$query_condition = " and (t_from='".whoami()."' or t_assigned='".whoami()."')"; 
}
$querytext="select * from {$prefix}{$hlpdsk_prefix}_tickets where Ticket_Number='$Ticket_Number' $query_condition ";
$tts->query($querytext); 
$recordcount=$tts->num_rows();
$row=0;
if ($recordcount=0) return 'missing';
while ($tts->next_record()):
		$post_date=$tts->f('post_date');
		$due_date=$tts->f('due_date');
		$end_date=$tts->f('end_date');
		$complete=$tts->f('complete');
		$t_from=$tts->f('t_from');
		$t_stage=$tts->f('t_stage');
		$t_category=$tts->f('t_category');
		$t_priority=$tts->f('t_priority');
		$t_subject=htmlspecialchars($tts->f('t_subject'));
		$t_description=htmlspecialchars($tts->f('t_description'));
		#$t_description=str_replace("\n"," <br> ",$t_description);
		$t_assigned=$tts->f('t_assigned');
		$t_email=$tts->f('t_email');
		$t_sms=$tts->f('t_sms');
		$t_status=$tts->f('t_status');
		$change_date=htmlspecialchars($tts->f('change_date'));
		$activity_id=$tts->f('activity_id');
		$project_id=$tts->f('project_id');
$due_date=date("Y/m/d H:i",$due_date);
$end_date=date("Y/m/d H:i",$end_date);
$action_changes=$javascript;
if ($t_sms=="on") $t_sms=" CHECKED";
if ($t_email=="on") $t_email=" CHECKED";
if ($GO_SECURITY->user_in_acl(whoami(), $acl_write)){
$button = new button();

$action_changes.=$button->get_button($cmdOk, "javascript:document.change_status.submit()");
$action_changes.=$button->get_button($cmdReset, "javascript:document.change_status.reset()");
}else{
$action_changes.='';
}
$action_changes.="</form></center>";
$tts_lang_ticket_number="Ticket Number:";
$tts_lang_post_date_value= date("{$_SESSION['GO_SESSION']['date_format']} H:i",$post_date);
$fullname=opentts::get_fullname($t_from);
if (Security::is_action_allowed("imperson",0,$acl_write)){
$select = new select('user', 'change_status', 't_from',$t_from);
            $tts_lang_issuer=$select->get_link("$tts_lang_issuer");
            $tts_lang_issuer_value=$select->get_field();
}else{
$tts_lang_issuer_value="$fullname";
}



$stage_name=Security::htmlsecure(get_cross_value("{$prefix}{$hlpdsk_prefix}_stages","stage_name"," where stage_id='$t_stage'"));
$category_name=Security::htmlsecure(get_cross_value("{$prefix}{$hlpdsk_prefix}_categories","category_name"," where category_id='$t_category'"));
$project_name=Security::htmlsecure(get_cross_value("{$prefix}{$hlpdsk_prefix}_projects","project_name"," where project_id='$project_id'"));
$select_complete="<select name='complete' class=textbox>"
        ."<option value='0' >0%</option>"
        ."<option value='10'>10%</option>"
        ."<option value='20'>20%</option>"
        ."<option value='30'>30%</option>"
        ."<option value='40'>40%</option>"
        ."<option value='50'>50%</option>"
        ."<option value='60'>60%</option>"
        ."<option value='70'>70%</option>"
        ."<option value='80'>80%</option>"
        ."<option value='90'>90%</option>"
        ."<option value='100'>100%</option>"
        ."</select>";

$t_assigned_name=Security::whatsmyname($t_assigned);
if (Security::is_action_allowed("change_subject",0,$acl_write)){
	$tts_lang_subject_value="<input name=t_subject value=\"".Security::sqlsecure($t_subject)."\" class=textbox max=80 size=80>";	
}else{
	$tts_lang_subject_value="<input name=t_subject value=\"".Security::sqlsecure($t_subject)."\" class=textbox max=80 size=80 readonly>";
}
$tts_lang_description_value="<textarea name=t_description cols=80 rows=12 class=textbox readonly>".Security::sqlsecure($t_description)."</textarea>";
$tts_lang_change_date_VALUE=date("{$_SESSION['GO_SESSION']['date_format']} H:i",$change_date);
$tts_lang_email="Email $tts_lang_issuer: <input type=checkbox name=t_email_issuer value=1><br>Email $tts_lang_assigned <input type=checkbox name=t_email_agent value=1>";


$post_changes="<form name=\"change_status\" method=\"POST\" action=\"change_ticket.php?Ticket_Number=$Ticket_Number&func=change_status\">";
if (Security::is_action_allowed("change_project",0,$acl_write)){
	 $project_name= select_option("$project_id",fill_select("project_id","{$prefix}{$hlpdsk_prefix}_projects","project_id","project_name"," order by project_id"));
	$tts_lang_project_value="$project_name";
}else{
	$project_name=Security::htmlsecure(get_cross_value("{$prefix}{$hlpdsk_prefix}_projects","project_name"," where project_id='$project_id'"));
        $tts_lang_project_value="$project_name";

}
$fullname=opentts::get_fullname($t_assigned);

if (Security::is_action_allowed("change_assigned",0,$acl_write)){
	    $select = new select('user', 'change_status', 't_assigned',$t_assigned);
            $tts_lang_assign_to=$select->get_link("$tts_lang_assign_to");
            $tts_lang_assign_to_value=$select->get_field();
}else{
	
	$tts_lang_assign_to_value="$fullname";
}
if (Security::is_action_allowed("change_end_date",0,$acl_write)){
        $time=strtotime($end_date);
        #$end_date_d_m_y=date("Y/m/d",$time);
        $end_date_h=date("H",$time);
        $end_date_i=date("i",$time);
	$today=date($_SESSION['GO_SESSION']['date_format'],$time);
        $end_date=$datepicker->get_date_picker('end_date_d_m_y',$_SESSION['GO_SESSION']['date_format'], $today);
$dropbox = new dropbox();
$dropbox->add_arrays($hours, $hours);
$end_date.='<td>';
$end_date_h_value=$dropbox->get_dropbox("end_date_h",$end_date_h);
$dropbox = new dropbox();
$dropbox->add_arrays($mins, $mins);
$end_date_i_value= $dropbox->get_dropbox("end_date_i",$end_date_i);
	if ($htmldirection=='rtl'){
		$end_date.="$end_date_i_value:$end_date_h_value";
	}else{
		$end_date.="$end_date_h_value:$end_date_i_value";
	}

        $tts_lang_end_date_value="$end_date&nbsp;";
}else{
        $tts_lang_end_date_value="$end_date";
}
if (Security::is_action_allowed("change_complete",0,$acl_write)){
        $_PERCENTAGE_COMPLETE_VALUE=select_option( "$complete","$select_complete");
}else{
        $_PERCENTAGE_COMPLETE_VALUE="$complete %";
}

if (Security::is_action_allowed("change_due_date",0,$acl_write)){
	$time=strtotime($due_date);
	$due_date_d_m_y=date($_SESSION['GO_SESSION']['date_format'],$time);
	$due_date_h=date("H",$time);
	$due_date_i=date("i",$time);
	$today=date($_SESSION['GO_SESSION']['date_format'],$time);
        $due_date=$datepicker->get_date_picker('due_date_d_m_y',$_SESSION['GO_SESSION']['date_format'], $today, '', '', 'onchange="javascript:document.change_status.end_date_d_m_y.value=this.value;"');
$dropbox = new dropbox();
$dropbox->add_arrays($hours, $hours);
$due_date.='<td>';
$due_date_h_value=$dropbox->get_dropbox("due_date_h",$due_date_h, 'onchange="javascript:update_end_hour(this.value);"');
$dropbox = new dropbox();
$dropbox->add_arrays($mins, $mins);
$due_date_i_value= $dropbox->get_dropbox("due_date_i",$due_date_i, 'onchange="javascript:update_end_min(this.value);"');
	if ($htmldirection=='rtl'){
		$due_date.="$due_date_i_value:$due_date_h_value";
	}else{
		$due_date.="$due_date_h_value:$due_date_i_value";
	}

        $tts_lang_due_date_value="$due_date&nbsp;";

}else{
	$tts_lang_due_date_value="{$tts_lang_due_date}$due_date";
}
if (Security::is_action_allowed("change_activity",0,$acl_write)){
        $activity=select_option("$activity_id",fill_select("activity_id","{$prefix}{$hlpdsk_prefix}_activities","activity_id","activity_name"," "));
        $tts_lang_activity_value="<br>$activity&nbsp;";
}else{
        $tts_lang_activity_value=Security::htmlsecure(get_cross_value("{$prefix}{$hlpdsk_prefix}_activities","activity_name","where activity_id='$activity_id'"));
}

if (Security::is_action_allowed("change_status",0,$acl_write)){
	$t_status_sel=select_option("$t_status",fill_select("t_status","{$prefix}{$hlpdsk_prefix}_status","status_id","status_name"," "));
	$tts_lang_status_value="<br>$t_status_sel";
}else{
	$status_name=Security::htmlsecure(get_cross_value("{$prefix}{$hlpdsk_prefix}_status","status_name"," where status_id='$t_status'"));
	$tts_lang_status_value="$status_name";
}
if (Security::is_action_allowed("change_priority",0,$acl_write)){
	$t_priorities= select_option("$t_priority",fill_select("t_priority","{$prefix}{$hlpdsk_prefix}_priorities","priority_id","priority_name"," "));
$tts_lang_priority_value="$t_priorities<br>";
}else{
	$t_priority_name=Security::htmlsecure(get_cross_value("{$prefix}{$hlpdsk_prefix}_priorities","priority_name"," where priority_id=$t_priority"));
	$tts_lang_priority_value="$t_priority_name";
}
if (Security::is_action_allowed("change_category",0,$acl_write)){
        $t_category= select_option("$t_category",fill_select("t_category","{$prefix}{$hlpdsk_prefix}_categories","category_id","category_name"," "));
	$tts_lang_category_value="$t_category<br>";
}else{
	$tts_lang_category_value="$category_name";
}
if (Security::is_action_allowed("change_stage",0,$acl_write)){
        $t_stage= select_option("$t_stage",fill_select("t_stage","{$prefix}{$hlpdsk_prefix}_stages","stage_id","stage_name"," "));
	$tts_lang_stage_value="<br>$t_stage<br>";
}else{
	$tts_lang_stage_value=Security::htmlsecure("$stage_name");
}

$mailto_subject= "?subject=".addslashes("Ticket Task  $Ticket_Number: "). addslashes($t_subject);
$mailto_body= "&body=".addslashes("Ticket/Task:   $Ticket_Number / "  ).addslashes($t_description);
$mailto= $mailto_subject . $mailto_body;
$tts_lang_mail_this="<a href=\"mailto:$mailto\">Send email</a>";

$tts_lang_estimated_time=$_MONEY='';
$tts_lang_mail_this="";
$file="themes/$hlpdsk_theme/showline_ticket.html";
$file=addslashes (implode("",file($file)));
eval("\$content=stripslashes(\" $file\");");
echo $content;

 $row++;
endwhile;

}
?>
