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


//Backward compatibilty functions.
function menu($highligth,$uname){
	Opentts::menu($highligth);
}
function DrawTr(){
	Opentts::DrawTr();
}
// Classes
class Opentts{
	
	function welcome(){
		include('themes/Aqua/welcome.html');
		echo Opentts::loadvar("welcome_message");
	return TRUE;
	}
	
	function loadvar($var_name){
		global $tts,$prefix,$hlpdsk_prefix;
		return Common::get_cross_value("{$prefix}{$hlpdsk_prefix}_config","definition", " where varname='$var_name'");
	}
	
	function menu_values(){
		global $tts,$name,$prefix,$hlpdsk_prefix,$tts;
		$query="select * from {$prefix}{$hlpdsk_prefix}_menu";
		$row=FALSE;
		if ($result=$tts->query($query)){
			#while($row[]=sql_fetch_object($result,$tts)){ ; }
			$i=0;
			while($tts->next_record()){
				$row[$i]->menu_id=$tts->f('menu_id');
				$row[$i]->title=$tts->f('title');
				$row[$i]->file=$tts->f('file');
				$row[$i]->image=$tts->f('image');
                                $row[$i++]->link=$tts->f('link');
			}
		}
		return $row;
	}
	
	function get_fullname($uid){
		global $name,$prefix,$hlpdsk_prefix;
			$tts= new db();
			$query="select first_name,middle_name,last_name from users where id='$uid'";
			if ($tts->query($query)){
				$tts->next_record();
				$fullname=$tts->f('first_name').' ';
				if ($tts->f('middle_name')<>'') $fullname.=$tts->f('middle_name').' ';
				$fullname.=$tts->f('last_name');
				return $fullname;
			}
		return '';
	}

	function menu($menu_selected,$uid=FALSE){
		global $hlpdsk_theme,$name,$prefix,$file;
		$rows=Opentts::menu_values();
		$_BUTTONS="";
		foreach ($rows as $line){
			if ($line->file==$menu_selected) $selected=TRUE; else $selected=FALSE;
			$_BUTTONS.=draw_button($line->link,$line->title,$line->image,$selected);
			$_EXTRA_BUTTONS=show_hidden() . draw_extra_button();
		}
		$textfile="themes/$hlpdsk_theme/menu.html";
		#$custom_title=get_cross_value("{$prefix}_modules","custom_title"," where title='$name'");
		$custom_title=$name;
		$text=addslashes (implode("",file($textfile)));
		eval("\$content=\"$text\";");
		echo $content;
		return TRUE;
	
	}
	function test($uid=FALSE){
		if ($uid) 
			return eval("echo \"success $uid\";");
		else
			return eval ("echo \"success".Security::get_uid()."\";");
	}

	function status_stat(){
		global $tts,$prefix,$hlpdsk_prefix;
		$select="SELECT status_name,count(status_name) as t_count FROM {$prefix}{$hlpdsk_prefix}_status,{$prefix}{$hlpdsk_prefix}_tickets where {$prefix}{$hlpdsk_prefix}_tickets.t_status={$prefix}{$hlpdsk_prefix}_status.status_id GROUP BY status_name"; 
		$tts->query($select,$tts);
		while($tts->next_record()){
			$myarray[$tts->f('status_name')]=$tts->f('t_count');
		}
		if (isset($myarray)){
		return Statistics::display_xy($myarray,"Ticket status distribution","Status","Quant.");
		}
	}
	
	function cat_status_stat(){
                global $tts,$prefix,$hlpdsk_prefix;
                $select="SELECT category_name,status_name,count(*) as cat_count 
	FROM {$prefix}{$hlpdsk_prefix}_categories,
	{$prefix}{$hlpdsk_prefix}_tickets,
	{$prefix}{$hlpdsk_prefix}_status
	 where {$prefix}{$hlpdsk_prefix}_tickets.t_category={$prefix}{$hlpdsk_prefix}_categories.category_id
and {$prefix}{$hlpdsk_prefix}_tickets.t_status={$prefix}{$hlpdsk_prefix}_status.status_id
 GROUP BY category_name,status_name";
		#echo $select;exit();
                $tts->query($select,$tts);
                while($tts->next_record()){
                        $myarray[$tts->f('category_name')][$tts->f('status_name')]=$tts->f('cat_count');
                }
		if (isset($myarray)){
                return Statistics::display_xyz($myarray,"Ticket categories distribution","Categories","Quant.","Status");
		
		}
        }


        function cat_stat(){
                global $tts,$prefix,$hlpdsk_prefix;
                $select="SELECT category_name,count(category_name) as t_count FROM {$prefix}{$hlpdsk_prefix}_categories,{$prefix}{$hlpdsk_prefix}_tickets where {$prefix}{$hlpdsk_prefix}_tickets.t_category={$prefix}{$hlpdsk_prefix}_categories.category_id GROUP BY category_name";
                $tts->query($select,$tts);
                while($tts->next_record()){
                        $myarray[$tts->f('category_name')]=$tts->f('t_count');
                }
		if (isset($myarray)){
                return Statistics::display_xy($myarray,"Ticket categories distribution","Categories","Quant.");
		}
        }
	
	function DrawTr(){
		global $ThemeSel;
		$theme=$ThemeSel;
		 echo "</td>
    <td background=\"themes/$theme/images/right2.gif\">&nbsp;</td></tr><tr>
    <td width=\"15\" height=\"15\"><img src=\"themes/$theme/images/middle-left.gif\" alt=\"\" border=\"0\"></td>
    <td background=\"themes/$theme/images/middle.gif\" align=\"center\" height=\"15\">&nbsp;</td>
    <td><img src=\"themes/$theme/images/middle-right.gif\" width=\"15\" height=\"15\" alt=\"\" border=\"0\"></td></tr><tr><td background=\"themes/$theme/images/left2.gif\">&nbsp;</td><td>";
	}
	

	function form_show_options($choices,$post_url){
		$ret_value="<form method=POST action='$post_url'><select name=func>";
		foreach ($choices as $key=>$value){
			$ret_value.="<option value='$value'>$value</option>";
		}
		$ret_value.='</select><input name=submit type=submit value="Submit"></form>';
		return $ret_value;
	}
	
	function edit_table($db_table,$db_field,$post_url,$ret_func,$db_order_field=""){
		global $tts,$name,$prefix,$hlpdsk_prefix;
		// we load all the status in a textarea, one per line status_id, status_name
		// then we pase it back to load the database.
		$query="select * from {$db_table} $db_order_field ";
		$tts->query($query,$tts);
		$ret_value="<h2>$db_table</h2><br>";
		$ret_value.="<form method=POST action='$post_url&func=$ret_func'>";
		 $ret_value.="<textarea name=update_array cols=40 rows=10>";
		while($tts->next_record()){
                        #$myarray[$tts->f("$key")]=$tts->f("$value");

                        #$ret_value.=$myrow->$db_field;
                        $ret_value.=$tts->f($db_field)."\n";
		}
		$ret_value.="</textarea>";
		 $ret_value.='<input name=submit type=submit value="Submit"></form>';   
                return $ret_value;                             
        }                         

        function update_table($db_table,$db_field,$text_array,$key_field){
                global $tts;
                // we load all the status in a textarea, one per line status_id, status_name    
                // then we pase it back to load the database.
		#echo nl2br($text_array);exit ();
		$text_array=str_replace("\r","",$text_array);
		$text_array=str_replace("\n","<br />",$text_array);
		$lines=split("<br />",$text_array);
                $query="delete from {$db_table}";     
                if($tts->query($query,$tts)){
			foreach($lines as $key=>$value){
				#$value=Security::sqlsecure($value);
				$insert_key=$key+1;
				$query="insert into  {$db_table}"
				." ($key_field,$db_field) values ('$insert_key','$value')";
				if ($value<>''){
					$tts->query($query,$tts);
				}	
			}
		return TRUE;
		}
		return FALSE;
	}
	function mod_gid($mod_uid=FALSE,$mod_gid=FALSE,$mod_action="show_form"){
                global $prefix,$hlpdsk_prefix,$tts,$name;
                $mod_uid=Security::get_uid($mod_uid);
                switch($mod_action) {
                        case "update":
                        if ($mod_uid){
                                $mod_uid=Security::sqlsecure($mod_uid,'int');
                                $mod_gid=Security::sqlsecure($mod_gid);
                                $query="delete from  {$prefix}{$hlpdsk_prefix}_groups_members where  gid='$mod_gid' and  uid='$mod_uid'";
                                if ($tts->query($query,$tts)) return TRUE; 
                        }
                        return FALSE;
                        break;
                        
                        case "insert":
                        if ($mod_uid){
                                $uid=Security::sqlsecure($uid,'int');
                                $gid_array=Security::sqlsecure($gid_array);
                                $query="insert into {$prefix}{$hlpdsk_prefix}_groups_members  (uid,gid) values ($mod_uid,'$mod_gid')";
                                if ($tts->query($query,$tts)) return TRUE;                        }
                        return FALSE;
                        break;
                        
                         case "delete":
                        if ($mod_uid){
                                $mod_uid=Security::sqlsecure($mod_uid,'int');
                                $mod_gid=Security::sqlsecure($mod_gid);
                                $query="delete from {$prefix}{$hlpdsk_prefix}_groups_members  where uid='$mod_uid' ";
                                if ($tts->query($query,$tts)) return TRUE;                        }
                        return FALSE;
                        break;
                }       
                $response="<form action=\"modules.php?name=$name&file=admin&func=mod_gid\" method=POST>";
                $response.="<table border=1><tr>";
                $response.="<td><font class=content>uname<input name=mod_uid type=text></td>";
                $response.="<td><font class=content>gid_array<input name=mod_gid type=text></td>".
                "<td><font class=content>delete<input name=mod_action type=radio value=\"delete\"></td>".
                "<td><font class=content>add<input name=mod_action type=radio value=\"insert\" checked></td>".
                "<td><font class=content>modify<input name=mod_action type=radio value=\"update\" ></td>";
                $response.="</tr></table>";
                $response.="<input type=submit name=\"submit\"></form>";
                return $response;
        }

        function mod_permissions($mod_action_name=FALSE,$mod_gid=FALSE,$mod_action="show_form"){
                global $prefix,$hlpdsk_prefix,$tts,$name;
	        $mod_action_name=Security::sqlsecure($mod_action_name);
		$mod_action_id = get_cross_value("{$prefix}{$hlpdsk_prefix}_actions","action_id"," where action_name='$mod_action_name'");
               	$mod_gid=Security::sqlsecure($mod_gid);
                switch($mod_action) {

                        case "insert":
                        if ($mod_action_id and $mod_gid){
                                $query="insert into {$prefix}{$hlpdsk_prefix}_permissions (action_id,gid) values ('$mod_action_id','$mod_gid')";
                                if ($tts->query($query,$tts)) return TRUE;                        }
                        return FALSE;
                        break;

                         case "delete":
                        if ($mod_action_id and $mod_gid){
                                $query="delete from {$prefix}{$hlpdsk_prefix}_permissions  where gid='$mod_gid' and action_id='$mod_action_id'";
                                if ($tts->query($query,$tts)) return TRUE;                        }
                        return FALSE;
                        break;
                }
		// show_form
                $response="<form action=\"modules.php?name=$name&file=admin&func=mod_permissions\" method=POST>";
                $response.="<table border=1><tr>";
                $response.="<td><input name=mod_action_name type=text></td>";
                $response.="<td><input name=mod_gid type=text></td>".
                "<td><font class=content>delete<input name=mod_action type=radio value=\"delete\"></td>".
                "<td><font class=content>add<input name=mod_action type=radio value=\"insert\"></td>";
                $response.="</tr></table>";
                $response.="<input type=submit name=\"submit\"></form>";
                return $response;
        }

	function mod_globals($mod_varname=FALSE,$mod_definition=FALSE,$mod_action="show_form"){
		global $prefix,$hlpdsk_prefix,$tts,$name,$cmdOk,$admin_tabtable;
		switch($mod_action) {
			case "update":
			if ($mod_varname){
				$mod_varname=Security::sqlsecure($mod_varname);
				$mod_definition=Security::sqlsecure($mod_definition);
				$query="update {$prefix}{$hlpdsk_prefix}_config set definition='$mod_definition' where varname='$mod_varname'";
				if ($tts->query($query,$tts)) return TRUE; 
			}
			return FALSE;
	    		break;
			
			case "insert":
			if ($mod_varname){
				$mod_varname=Security::sqlsecure($mod_varname);
                                $mod_definition=Security::sqlsecure($mod_definition);
                                $query="insert into {$prefix}{$hlpdsk_prefix}_config (varname,definition) values ('$mod_varname','$mod_definition')";
				#echo "$query";exit();
                                if ($tts->query($query,$tts)) return TRUE; 			}
			return FALSE;
                        break;
			
			 case "delete":
                        if ($mod_varname){
				$mod_varname=Security::sqlsecure($mod_varname);
                                $mod_definition=Security::sqlsecure($mod_definition);
                                $query="delete from {$prefix}{$hlpdsk_prefix}_config  where varname='$mod_varname'";
                                if ($tts->query($query,$tts)) return TRUE; 			}
			return FALSE;
                        break;
		}	
		$response="<form action=\"admin.php?admin_tabtable=$admin_tabtable&func=mod_globals\" method=POST>";
		$response.="<table border=1><tr>";
		$response.="<td><font class=content>varname<input name=mod_varname type=text></td>";
		$response.="<td><font class=content>definition<textarea name=mod_definition style=\"HEIGHT: 145px; WIDTH: 500px\"></textarea></td>".
		"<td><font class=content>delete<input name=mod_action type=radio value=\"delete\"></td>".
		"<td><font class=content>add<input name=mod_action type=radio value=\"insert\"></td>".
		"<td><font class=content>modify<input name=mod_action type=radio value=\"update\" checked></td>";
		$response.="</tr></table>";
		$button=new button();
		$button_submit=$button->get_button($cmdOk,"javascript:this.form.submit();");
		$response.=$button_submit."</form>";
		return $response;
	}
}
?>
