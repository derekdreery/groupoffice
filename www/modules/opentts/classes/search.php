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
function DateDiff
($interval,$date1,$date2) {
    // get the number of seconds between the two dates
$timedifference = $date2 - $date1;

    switch ($interval) {
        case 'w':
            $retval = bcdiv($timedifference,604800);
            break;
        case 'd':
            $retval = bcdiv($timedifference,86400);
            break;
        case 'h':
            $retval =bcdiv($timedifference,3600);
            break;
        case 'n':
            $retval = bcdiv($timedifference,60);
            break;
        case 's':
            $retval = $timedifference;
            break;

    }
    return $retval;
}



//Classes
class Search{
	var $table;
	var $string_to_search;
	var $on_field;
	var $return_fields;
	var $condition;
	var $retrieve_num_rows;
	var $limit;
	var $order;
	function Search(){
		return TRUE;
	}
	function  show_search(){
		global $name,$hlpdsk_prefix,$tts,$prefix,$strtosearch,$limit,$limit_rows,$search_status,$submit,$GO_LANGUAGE;
                require_once($GO_LANGUAGE->get_language_file('opentts'));
		if ($submit=="prev" and $limit>=$limit_rows)$limit -=$limit_rows;
		if ($submit=="next")$limit +=$limit_rows;
		if ($submit=="search")$limit =0;
		if ($submit and $limit_rows==-1)$limit_rows =10;
		if (!$limit) $limit=0;
		if(!$limit_rows) $limit_rows=-1;
		$response= " <DIV align=center>"
		."<TABLE  BORDER=0 CELLSPACING=1 CELLPADDING=1 class=boxcontent>"
		."<TR><TD>"
		."<FORM action=\"queries.php\" method=POST id=frmsearch name=frmsearch>"
		."<STRONG><font class=content>$tts_lang_search</STRONG><BR>"
		."<INPUT type=\"text\" name=strtosearch value=\"".Security::htmlsecure($strtosearch)."\" size=60 class=textbox><br>";
		$response.= "</td></tr>";
		$response.= "<tr><td>";
		$query="select status_id,status_name,show_by_default from {$prefix}{$hlpdsk_prefix}_status";
		$tts->query($query);
		$colbreak=0;
		$response.= "<table border=1 width=100%>";
		while($tts->next_record()){
			$status_id=$tts->f('status_id');
			$status_name=$tts->f('status_name');
			$show_by_default=$tts->f('show_by_default');
        		if ($colbreak==0)$response.= "<tr>";
	        	if (!$search_status){
        	        	if ($show_by_default==1)  $checked="checked";
                	        	else  $checked="";
	                }else{
        	                if($search_status[$status_id]=="on") $checked="checked";else $checked="";
                	}
		        $response.= "<td><font class=content>$status_name:<input name=search_status[$status_id] type=checkbox $checked></td>";
        		$colbreak+=1;
	        	if (2==$colbreak){$response.= "</tr>";$colbreak=0;}
		}
		$response.=  "</table>";
		
		$response.= "</td></tr>";
		$response.= "<tr><td>";
		$response.= "<INPUT  type=\"hidden\" value=\"search\"  name=\"action\">";
		$button = new button();
		$response.= $button->get_button($cmdSearch,'javascript:document.frmsearch.submit();');
		if ($submit){
		        $response.= "<input  type=\"submit\" value=\"prev\"  name=submit><input  type=\"submit\" value=\"next\"  name=submit>  ";
		}
		$response.= "</FORM></TD>";
		$response.= "  </TR></TABLE>";
	return $response;
	}
	function printdb($extra_condition=""){
		global $agent,$t_showall,$search,$strtosearch,$orderby, 
			$name,$tts,$prefix,$hlpdsk_prefix,$limit,$limit_rows,
			$search_status,$submit,$temptime,$hlpdsk_theme,$field,
			$filter,$shadow_dark,$shadow_light,$show_hidden,$filter_field,
			$filter_value,$show_hidden,$GO_LANGUAGE,
			$nuke_user_table,$nuke_user_last_name_fieldname,$nuke_username_fieldname,
			$nuke_user_id_fieldname,$nuke_user_first_name_fieldname,$_SESSION;
		require_once($GO_LANGUAGE->get_language_file('opentts'));
		if ($filter==1){
                        $alert_note="FILTER ON -- <a href='my_tickets.php?submit=clear_filters'>CLEAR FILTER</a><br>";
			if (isset($filter_field)){
        	                foreach ($filter_field as $key=>$value){
                	                $alert_note .= " {$value}='{$filter_value[$key]}'<br> ";
                        	}
                	}	
		}
		$response_raw= unserialize ( Search::querydb($extra_condition));	
#
		$response= "<BR><DIV align=center>";
		if(!isset($alert_note)){$alert_note='';}
		$response.="<center><TABLE  BORDER=1 CELLSPACING=1 CELLPADDING=1  bordercolor=Black><TR>"
		."<TD valign=top>$tts_lang_query<br>$alert_note</TD>"
		."<TD valign=top>";
		$querytotal= $response_raw[0]['querytotal'];

		$querytext= $response_raw[0]['querytext'];
		if (Security::is_action_allowed('view_query')) $response.= Security::htmlsecure($querytext)."</TD>"; else $response.= Security::htmlsecure($strtosearch)."</TD>";

		$color="ffeeee";
		$recordcount=$response_raw[0]['recordcount'];
		$row=0;
		$prev=$limit-10;
		$next=$limit+10;
		if ($prev<0)$prev=0;

		$response.= "
                </TR>
                <TR>
                <TD>".$tts_lang_total_records_found."
                </TD>
                <TD>$recordcount/$querytotal
                </TD>
                </TR>
		</TABLE>";
		$response.= "<br>";
		if ($recordcount==0){ return $response;}
		$response_row="";
		if (Security::is_action_allowed("view_priority")){
		        $file="themes/$hlpdsk_theme/my_tickets.html";
		        $file=addslashes (implode("",file($file)));
		}else{
		        $file="themes/$hlpdsk_theme/my_tickets.html";
		        $file=addslashes (implode("",file($file)));
		}
		$color="#FFFFFF";
		#get format of row
		if (Security::is_action_allowed("view_priority")){
		        $tts_row="themes/$hlpdsk_theme/my_tickets_row.html";
		        $tts_row=addslashes (implode("",file($tts_row)));
		}else{
		        $tts_row="themes/$hlpdsk_theme/my_tickets_row.html";
		        $tts_row=addslashes (implode("",file($tts_row)));
		}

		#
		// Building hashes
		$status_hash=SQL::build_hash("{$prefix}{$hlpdsk_prefix}_status","status_id","status_name");
		$stage_hash=SQL::build_hash("{$prefix}{$hlpdsk_prefix}_stages","stage_id","stage_name");
		$category_hash=SQL::build_hash("{$prefix}{$hlpdsk_prefix}_categories","category_id","category_name");
		$priority_hash=SQL::build_hash("{$prefix}{$hlpdsk_prefix}_priorities","priority_id","priority_name");
		$bck_clr_hash=SQL::build_hash("{$prefix}{$hlpdsk_prefix}_colors_tables", "clr_tbl_id","bck_clr");
		$fnt_clr_hash=SQL::build_hash("{$prefix}{$hlpdsk_prefix}_colors_tables", "clr_tbl_id","fnt_clr");
		$project_name_hash=SQL::build_hash("{$prefix}{$hlpdsk_prefix}_projects", "project_id","project_name");
		$start_eiler_date=time();
		while (list($key,$value)=each($response_raw)){
			if ($key==0) continue;
			$ticket_number=Security::sqlsecure($response_raw[$key]['ticket_number']);
                        $t_status=Security::sqlsecure($response_raw[$key]['t_status']);
                        $t_priority=Security::sqlsecure($response_raw[$key]['t_priority']);
                        $t_from=Security::sqlsecure($response_raw[$key]['t_from']);
                        $t_subject=Security::htmlsecure($response_raw[$key]['t_subject']);
                        $t_assigned=Security::sqlsecure($response_raw[$key]['t_assigned']);
                        $t_stage=Security::sqlsecure($response_raw[$key]['t_stage']);
                        $t_category=Security::sqlsecure($response_raw[$key]['t_category']);
                        $post_date=Security::sqlsecure($response_raw[$key]['post_date']);
                        $due_date=Security::sqlsecure($response_raw[$key]['due_date']);
                        $end_date=Security::sqlsecure($response_raw[$key]['end_date']);
                        $complete=Security::sqlsecure($response_raw[$key]['complete']);
                        $change_date=Security::sqlsecure($response_raw[$key]['change_date']);
                        $activity_id=Security::sqlsecure($response_raw[$key]['activity_id']);
                        $project_id=Security::sqlsecure($response_raw[$key]['project_id']);
			$is_hidden=$t_stage;
                        if (!$t_subject) $t_subject="&nbsp;";
                        $print_complete="$complete%";
                        $due_diff=$due_date-time();
                        $change_diff=$temptime-$change_date;
                        if (date("Y/m/d",$due_date)==date("Y/m/d")){
                                $print_due_date=date("{$_SESSION['GO_SESSION']['date_format']} H:i",$due_date);
                        }else{
                                $print_due_date=date("{$_SESSION['GO_SESSION']['date_format']} H:i",$due_date);
                        }
                        $print_end_date=date("{$_SESSION['GO_SESSION']['date_format']} H:i",$end_date);
                        $print_change_date=date("{$_SESSION['GO_SESSION']['date_format']} H:i",$change_date);
                        $print_last_visit=date("{$_SESSION['GO_SESSION']['date_format']} H:i",$temptime);

                        if ($due_diff<0) {
                                $due_color_start="<font color=red>";
                                $due_color_stop="</font>";
                        }else{
                                $due_color_start="<font color=black>";
                                $due_color_stop="</font>";
                        }

                        if ($change_diff<0) {
                                $change_color_start="<font color=red><img src='icons/red_folder.gif' alt='Changed'>";
                                $change_color_stop="</font>";
                        }else{
                                $change_color_start="<font color=black><img src='icons/folder.gif' alt=''>";
                                $change_color_stop="</font>";
                        }
			$status_name=Security::htmlsecure($status_hash[$t_status]);
			$bck_clr=$bck_clr_hash[0];
			$fnt_clr=$fnt_clr_hash[0];


	                $priority_name=Security::htmlsecure($priority_hash[$t_priority]);
                        $project_name=Security::htmlsecure($project_name_hash[$project_id]);
			$fullname=get_cross_value("{$nuke_user_table}","$nuke_user_last_name_fieldname"," where $nuke_user_id_fieldname='$t_assigned'");
                        $fullname.=", ".get_cross_value("{$nuke_user_table}","$nuke_user_first_name_fieldname"," where $nuke_user_id_fieldname='$t_assigned'");
                        $t_assigned_name=$fullname;
                        $category_name=Security::htmlsecure($category_hash[$t_category]);
                        $stage_name=Security::htmlsecure($stage_hash[$t_stage]);
			$fullname=get_cross_value("{$nuke_user_table}","$nuke_user_last_name_fieldname"," where $nuke_user_id_fieldname='$t_from'");
			$fullname.=", ".get_cross_value("{$nuke_user_table}","$nuke_user_first_name_fieldname"," where $nuke_user_id_fieldname='$t_from'");
                        $issuer_name=Security::htmlsecure($fullname);
			if ($is_hidden==2){
				$color=$shadow_hidden;
			}elseif (!strcmp($color,$shadow_dark)) {
                             $color=$shadow_light;
                        }else {
                             $color=$shadow_dark;
                        }
                        if ($activity_id==1){ $color="00ff00";}

                        eval("\$content_row=\"$tts_row\";");
                        $response_row.= $content_row;

                        #$response_row.= "</tr> ";
                        $row++;

		}
			$start_project_date=date("Ymd",$start_eiler_date)."T000000Z";
		        eval("\$content_total=stripslashes(\"$file\");");
	return ($response . $content_total);
	}

	function querydb($extra_condition=""){
		global $agent,$t_showall,$search,$strtosearch,$orderby, $name,$tts,$prefix,$hlpdsk_prefix,$limit,$limit_rows,$search_status,$submit,$temptime,$hlpdsk_theme,$field,$filter_field,$filter_value,$show_hidden,$hidden_check,$GO_SECURITY;
		$querytext="select  *  from {$prefix}{$hlpdsk_prefix}_tickets ";
		if ($limit=="") $limit=0;
		if ($limit_rows=="") $limit_rows=-1;
		$query_limit=" limit $limit,$limit_rows ";
		$query_condition="where 1 $extra_condition";
		$strtosearch=Security::sqlsecure($strtosearch);
		if (isset($submit)){
		        $search=1;
			if ($field){
				$field=Security::sqlsecure($field);
			        $query_condition .= " and  (  $field='$strtosearch')";
			}else{
				$array_to_search=split(" ",$strtosearch);
				$query_condition .= " and ( ";
				foreach ($array_to_search as $to_search){ 
				        $query_condition .= " (t_subject like '%$to_search%' or t_description like '%$to_search%') and";
			}
			$query_condition =substr($query_condition,0,-3);
			$query_condition .=" ) ";
			}
		}
		if (is_array($search_status)){
				$search_condition ='';
		        foreach (array_keys($search_status) as  $check_key){
                		$search_condition .= "t_status={$check_key} ";
		        }
			# show only open tickets
			if(trim($search_condition)<>"")
				$query_condition .=" and (". str_replace(" "," or ",trim ($search_condition)).")";
		}
		if ($show_hidden=='on') { ; } else $query_condition .=" and t_stage=1 ";
			
		if ($strtosearch){ 
			$search_uid=Security::get_uid("$strtosearch");
		}else{ 
			$search_uid="";
		}
		if ($search_uid<>""){
			$query_condition = "where 1  and (t_assigned='$search_uid' or  t_from='$search_uid') ";
		}
		# FILTERS
		if (isset($filter_field)){
		        foreach ($filter_field as $key=>$value){
                		$query_condition .= " and ( {$value}='{$filter_value[$key]}') ";
		        }
		}



	# END OF SEARCH CONDITIONS

		if (strcmp($orderby,"")) {
		        $query_order_by= " $orderby DESC";
		}else{
			$query_order_by= " order by Ticket_Number DESC";
 		}

#
		$response_raw[0]['querytext']= $querytext . $query_condition . $query_order_by .$query_limit;
	        $tts->query($response_raw[0]['querytext']);
		$response_raw[0]['recordcount']=$tts->num_rows();
		$response_raw[0]['querytotal']= $response_raw[0]['recordcount'];
		if ($response_raw[0]['recordcount']==0){ return serialize( $response_raw);}
		$row=1;
		if (Security::is_action_allowed("view_all_tickets")){
	        $tts->query($response_raw[0]['querytext']);
			while ($tts->next_record()){
                                $response_raw[$row]['ticket_number']=$tts->f('ticket_number');
                                $response_raw[$row]['t_status']=$tts->f('t_status');
                                $response_raw[$row]['t_priority']=$tts->f('t_priority');
                                $response_raw[$row]['t_from']=$tts->f('t_from');
                                $response_raw[$row]['t_subject']=$tts->f('t_subject');
                                $response_raw[$row]['t_assigned']=$tts->f('t_assigned');
                                $response_raw[$row]['t_stage']=$tts->f('t_stage');
                                $response_raw[$row]['t_category']=$tts->f('t_category');
                                $response_raw[$row]['post_date']=$tts->f('post_date');
                                $response_raw[$row]['due_date']=$tts->f('due_date');
                                $response_raw[$row]['end_date']=$tts->f('end_date');
                                $response_raw[$row]['complete']=$tts->f('complete');
                                $response_raw[$row]['change_date']=$tts->f('change_date');
                                $response_raw[$row]['activity_id']=$tts->f('activity_id');
                                $response_raw[$row]['project_id']=$tts->f('project_id');
                                 $row++;
                         }
		}else{
	        $tts->query($response_raw[0]['querytext']);
			while ($tts->next_record()){
				if ($GO_SECURITY->user_in_acl(whoami(),$tts->f('acl_read')) or $GO_SECURITY->user_in_acl(whoami(),$tts->f('acl_write')) ){	
				$response_raw[$row]['ticket_number']=$tts->f('ticket_number');
				$response_raw[$row]['t_status']=$tts->f('t_status');
				$response_raw[$row]['t_priority']=$tts->f('t_priority');
				$response_raw[$row]['t_from']=$tts->f('t_from');
				$response_raw[$row]['t_subject']=$tts->f('t_subject');
				$response_raw[$row]['t_assigned']=$tts->f('t_assigned');
				$response_raw[$row]['t_stage']=$tts->f('t_stage');
				$response_raw[$row]['t_category']=$tts->f('t_category');
				$response_raw[$row]['post_date']=$tts->f('post_date');
				$response_raw[$row]['due_date']=$tts->f('due_date');
				$response_raw[$row]['end_date']=$tts->f('end_date');
				$response_raw[$row]['complete']=$tts->f('complete');
				$response_raw[$row]['change_date']=$tts->f('change_date');
				$response_raw[$row]['activity_id']=$tts->f('activity_id');
				$response_raw[$row]['project_id']=$tts->f('project_id');
				$row++;
				}else{
				continue;
		 		}
			}
		}
		$response_raw[0]['recordcount']=--$row;
		$response_raw[0]['querytotal']= $response_raw[0]['recordcount'];
	return serialize ( $response_raw);
	}
		
}
?>
