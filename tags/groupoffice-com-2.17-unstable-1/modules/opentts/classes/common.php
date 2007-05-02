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




function locale_date($date_format,$gmt_diff){
	 return Common::locale_date($date_format,$gmt_diff);
}

function locale_time($gmt_diff){
         return Common::locale_time($gmt_diff);
}



function fill_select($select_name,$db_table,$field_index,$field_value,$condition="",$order=""){
	return Common::fill_select($select_name,$db_table,$field_index,$field_value,$condition);
}

function select_option($value,$select){
return Common::select_option($value,$select);
}

function get_cross_value($db_table,$field,$condition="",$order=""){
	return Common::get_cross_value($db_table,$field,$condition,$order);
}


function priv_msg($sender,$rcpt,$subject,$description){
	return Common::priv_msg($sender,$rcpt,$subject,$description);	
}

#*********************************
#old functions.php
#get list of agents


//classes

class Common{
	
	//methods
	function locale_date($date_format,$gmt_diff){
        	 return date("$date_format",mktime (gmdate("H")+($gmt_diff),gmdate("i"),gmdate("s"),gmdate("m"),gmdate("d"),gmdate("Y") ));
	}

	function locale_time($gmt_diff){
        	 return date("H:i:s",mktime (gmdate("H")+($gmt_diff),gmdate("i"),gmdate("s"),gmdate("m"),gmdate("d"),gmdate("Y") ));
	}

	function priv_msg($sender,$rcpt,$subject,$description){
		global $name,$tts,$prefix;
		$msg_time=date("Y-m-d");
		$querytext="insert into {$prefix}_priv_msgs (from_userid,to_userid,subject,msg_text,msg_time) values ('$sender','$rcpt','$subject','$description','$msg_time')";
		$tts->query($querytext);
	}	
	function get_cross_value($db_table,$field,$condition="",$order=""){
		global $prefix, $tts, $user_prefix,$tts;
		$query ="select $field from $db_table  $condition $order";
		if ($tts->query($query)){
			$tts->next_record();
			$my_return=$tts->f("$field");
			return $my_return;
		}else{
			return FALSE;
		}
	}
	function array_fill_options($array_table){
                global $prefix, $dbi, $user_prefix;
			$my_return='';
                foreach ($array_table as $key=>$value){
                        $my_return .="<option value=\"$key\">".htmlspecialchars($value)."</option>\n";
                }
                return $my_return;
        }

	function fill_options($db_table,$field_index,$field_value,$condition="",$order=""){
                global $prefix, $tts, $user_prefix;
                $query="select $field_index,$field_value from $db_table $condition $order";
                $tts->query($query);
                $my_return="\n";
                while($tts->next_record()){
			$field_index_value=$tts->f("$field_index");
			$field_value_value=$tts->f("$field_value");
                        $my_return .="<option value=\"$field_index_value\">".htmlspecialchars($field_value_value)."</option>\n";
                }
                return $my_return;
        }

	function fill_select($select_name,$db_table,$field_index,$field_value,$condition="",$order=""){
		global $prefix, $tts, $user_prefix;
		$query="select $field_index,$field_value from $db_table $condition $order";
		$result=$tts->query($query);
		$my_return="<select name=\"$select_name\" class=textbox >";
		while($tts->next_record()){
			$field_index_value=$tts->f("$field_index");
			$field_value_value=$tts->f("$field_value");
	        	$my_return .="<option value=\"$field_index_value\">".htmlspecialchars($field_value_value)."</option>";
		}
		$my_return .="</select>";
		return $my_return;
	}

	function select_option($value,$select){
		$select=str_replace("value='$value'", "value='$value' selected", $select);
		return  str_replace("value=\"$value\"", "value=\"$value\" selected", $select);
	}
	
	function build_form($form_field,$form_type="TEXT",$title="",$form_value="",$form_style="",$readonly=""){
		switch ($form_type){
			case "TEXT":
				return "$title<INPUT type=$form_type name='$form_field' value='$form_value' $form_style $readonly>";
			break;
			case "HIDDEN":
                                return "<INPUT type=$form_type name='$form_field' value='$form_value'  $readonly>";
                        break;
			case "CHECKBOX":
				return "$title<INPUT type=$form_type name='$form_field' value='$form_value' $form_style $readonly>";
			 break;
                        case "TEXTAREA":
                                return "$title<$form_type name='$form_field' $form_style $readonly>". htmlspecialchars($form_value). "</$form_type>";
			break;
                        case "RADIO":
                                return "$title<INPUT type=$form_type name='$form_field' value='$form_value' $form_style $readonly>";
                         break;
			break;
                        case "SUBMIT":
                                return "$title<INPUT type=$form_type name='$form_field' value='$form_value' >";
                         break;
			break;
                        case "RESET":
                                return "$title<INPUT type=$form_type name='$form_field' value='$form_value' >";
                         break;
		}
		return FALSE;
	}
}



class User{
	var $uid;
	var $uname;
	var $email;
	function User(){
		return TRUE;
	}
	function fetch_uname_by_uid($uid){
		global $tts,$user,$name,$prefix,$hlpdsk_prefix,$nuke_user_id_fieldname,$nuke_username_filedname;
		$query="select $nuke_username_filedname from {$prefix}_users where $nuke_user_id_fieldname='$uid'";
		$tts->query('$query');
		$uname=$tts->f($nuke_username_filedname);
		return $uname;
	}
	function fetch_email_by_uid($uid){
                global $tts,$user,$name,$prefix,$hlpdsk_prefix,$nuke_user_email_fieldname, $nuke_user_id_fieldname;
                $query="select $nuke_user_email_fieldname from {$prefix}_users where $nuke_user_id_fieldname='$uid'";
                $tts->query('$query');
		$email=$tts->f($nuke_user_email_fieldname);
                return $email;
        }
	function fetch_uid_by_uname($uname){
                global $tts,$user,$name,$prefix,$hlpdsk_prefix, $nuke_user_id_fieldname, $nuke_usename_fieldname;
                $query="select $nuke_user_id_fieldname from {$prefix}_users where $nuke_username_fieldname='$uname'";
                $tts->query('$query');
		$uname=$tts->f($nuke_user_id_fieldname);
                return $uname;
        }
}

class Tts_sql{
	//
	//constructor
	function Tts_sql(){
		return TRUE;
	}
	//methods 
	function get_csv($tbl_name,$field,$condition=""){
		global $tts,$user,$name,$prefix,$hlpdsk_prefix;
		$query =" select $field from $tbl_name $condition";
		if ($tts->query('$query')){
			$values="";
			#while($row=sql_fetch_object($result')){
			while($tts->next_record()){
				$values.="'".$tts->f($field)."',";
			}
			$values=trim($values,",");
			return $values;
		}
		return FALSE;
	}

	function show_query($fields,$db_tables,$condition="",$title="",$show_query=FALSE){
		global $tts;
		$query="select $fields from $db_tables $condition";
		if ($tts->query($query)){
			$content= "Query title: $title<br>\n";
			if ($show_query) $content.= "$query<br>\n";
                        $content.= "<table border=1>\n";
			$field_array=split(",",$fields);
                        while($tts->next_record()){
				$content.="<tr>\n";
				foreach($field_array as $key=>$value){
					$content.="<td><font class=content>".htmlspecialchars($tts->f($value))."</font></td>";
				}
				$content.="</tr>\n";
			}
			$content.="</table>\n";
			return $content;
		}
		return FALSE;
	}
	
	function show_table($db_table,$edit=0,$condition="",$sort="",$limit=""){
		global $tts;
		$query="SHOW COLUMNS FROM $db_table";
		if ($tts->query('$query')){
			$content= "Table: $db_table<br>\n";
                        $content.= "<table border=1>\n";
                        $content.="<tr>\n";
                        while($tts->next_record()){
                                //show row
                                $content.="<td><font class=content>".$tts->f('Field')."</font></td>\n";
                        }
                        $content.="</tr>\n";
		}	
		$query="select * from $db_table where 1 $condition $sort $limit";
		if ($tts->query($query)){
			while($tts->next_record()){
				//show row
				$content.="<tr>\n";
				foreach($myrow as $key=>$value){
                                        $content.="<td><font class=content>$tts->f($value)</font></td>\n";
                                }
				$content.="</tr>\n";
			}
		$content.="</table>\n";
		}
	return $content;
	}
}
