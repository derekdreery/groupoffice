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


//Backward compatibility
function verify_agent($uid){
	return Security::get_uid();
} 
function whatsmyname(){
	return Security::whatsmyname();
}
function whoami(){
	return Security::get_uid();
}

//Class Security: (static) deals with permission to run methods.
class Security{
	var $err_nr;
	var $err_message;

	function Security(){
		return TRUE;
	}
	
	function doorman($condition){
		exec("\$response=$condition;");
		return $response; 
	}
	function htmlsecure($query,$format="str"){
			return str_replace('<','&lt;',$query);
			#return smart_addslashes($query);
	}
	
	function sqlsecure($query,$format="str"){
			#return str_replace('<','&lt;',$query);
			return smart_addslashes($query);
	}
	function get_uid($uname=FALSE){
		global $prefix,$nuke_username_fieldname,$nuke_user_id_fieldname;
		if (!$uname)
			return Security::whatsmyuid();
		else
			return get_cross_value("users","$nuke_user_id_fieldname"," where $nuke_username_fieldname='".Security::sqlsecure($uname)."'");
	}

	function get_uname($uid=FALSE){
		global $GO_SECURITY,$GO_USERS,$nuke_username_fieldname;
		if($uid){
			$user = $GO_USERS->get_user($uid);
		}else{
			$user = $GO_USERS->get_user($GO_SECURITY->user_id);
		}
		return $user[$nuke_username_fieldname];	
	}
	function whats_his_name($uid){
                global $nuke_user_table,$nuke_username_fieldname,$nuke_user_id_fieldname,$nuke_user_first_name_fieldname,$nuke_user_last_name_fieldname;
                $fullname= get_cross_value("{$nuke_user_table}","$nuke_user_last_name_fieldname"," where $nuke_user_id_fieldname='".Security::sqlsecure($uid)."'");
                $fullname.=", ". get_cross_value("{$nuke_user_table}","$nuke_user_first_name_fieldname"," where $nuke_user_id_fieldname='".Security::sqlsecure($uid)."'");
		return $fullname;
        }


	function fetch_members($gid){
	global $prefix,$nuke_username_fieldname,$nuke_user_id_fieldname,$hlpdsk_prefix;
	return Sql::build_array("{$prefix}{$hlpdsk_prefix}_groups_members","uid","and  gid=$gid");

	}
	
	function whatsmyuid(){
		global $GO_SECURITY;
		return $GO_SECURITY->user_id;
		
        }

	function whatsmyname($uid=FALSE){
		return Security::get_uname($uid);
	}

	 function get_gid_array($uid=FALSE){
                if (!$uid) $uid=Security::get_uid();
                global $tts,$prefix,$hlpdsk_prefix;
		$query="select gid from {$prefix}{$hlpdsk_prefix}_groups_members where uid='$uid'";
		$tts->query($query);
		$gid_array =  array();
		while ($tts->next_record()){$gid_array[]=$tts->f('gid');}
		// dirty patch to allow everyone get regular users permissions
		$gid_array[]='1';
		
		return	join(":",$gid_array); 

        }
	
	function add_gid($gid,$uid=FALSE){
                if (!$uid) $uid=Security::get_uid();
                global $tts,$prefix,$hlpdsk_prefix;
                $query="insert into {$prefix}{$hlpdsk_prefix}_groups_members (uid,gid) values('$uid','$gid')";
                if ($tts->query($query)) return TRUE; else return FALSE;
        }



  function rem_gid($gid,$uid=FALSE){
                if (!$uid) $uid=Security::get_uid();
                global $tts,$prefix,$hlpdsk_prefix;
                $query="delete from  {$prefix}{$hlpdsk_prefix}_groups_members where gid='$gid' and uid=$uid";
                if ($tts->query($query)) return TRUE; else return FALSE;
        }

  function belongs_to_gid($gid,$uid=FALSE){
		if (!$uid) $uid=Security::get_uid();
                global $tts,$prefix,$hlpdsk_prefix;
                $query="select * from {$prefix}{$hlpdsk_prefix}_groups_members where uid='$uid' and gid=$gid";
		$tts->query($query);
		return $tts->num_rows();
		
	}
        function is_action_allowed($method,$acl_read=0,$acl_write=0,$uid=FALSE){
		global $dbi,$prefix,$hlpdsk_prefix,$GO_SECURITY;
		$uid= ($uid) ? $uid : whoami();
                $gid_array=Security::get_gid_array($uid);
                $gid_array=str_replace(":",",",$gid_array);
                if(!$gid_array) $gid_array="1";
                $perm=0;
                $perm= get_cross_value("{$prefix}{$hlpdsk_prefix}_permissions",'gid'," where ( action_id='".Security::get_action_id(Security::sqlsecure($method))."') and (gid in ($gid_array))");
                if ($perm) {
			if ($acl_read==0 and $acl_write==0){
	                        return $perm;
			}else{
				if ($acl_read>0 and $GO_SECURITY->user_in_acl($uid, $acl_read)) return $perm;
				if ($acl_write>0 and $GO_SECURITY->user_in_acl($uid, $acl_write)) return $perm; 						    $perm=0;
			}
                }
                return $perm;

        } 

	function get_action_id($action_name){
		global $dbi,$prefix,$hlpdsk_prefix,$name,$action;
                $action_name=Security::sqlsecure($action_name);
                return array_search($action_name,$action);
        }


	function sec_run($method,$params="",$uid=FALSE){
		global $tts,$prefix,$hlpdsk_prefix;
                $gid_array=Security::get_gid_array($uid);
                $class=split("::",$method);
                $class=$class[0];
                $gid_array=str_replace(":"," or gid=",$gid_array);
                if( get_cross_value("{$prefix}{$hlpdsk_prefix}_permissions",'gid'," where ( perm_name='".Security::sqlsecure($method)."' or  perm_name='$class::*') and (gid=$gid_array)")) 
			 eval("\$retval= $method($params);");
		else 
			 eval("\$retval= \"access denied\";");
		return $retval;
	}
	
	function get_default_agent_id(){
		global $tts,$prefix,$hlpdsk_prefix;
		$default_agent=get_cross_value("{$prefix}{$hlpdsk_prefix}_groups_members",'uid'," where uid_default='1'");
		return $default_agent;
	}

	
	function show_default_agent_table(){
		global $tts,$prefix,$hlpdsk_prefix,$name,$admin_tabtable,$cmdOk,$cmdReset;
		$query="select * from {$prefix}{$hlpdsk_prefix}_groups_members where gid=2";
		$content="<br><h2>Change Default agent</h2><br>";
		$content.="<form method=POST action='admin.php?admin_tabtable=$admin_tabtable&func=set_default_agent'>";
		$content.="<table border=1><tr><td>select</td><td>agent</td></tr>";
		if ($tts->query($query, $tts)){
			while($tts->next_record()){
				if ($tts->f('uid_default')==1) {
					$choosen_one="checked"; 
				}else{
					 $choosen_one="";
				}

				$content.="<tr><td><input type=radio name=\"choosen_one\" value=\"".$tts->f('uid')."\" $choosen_one ></td><td>". Security::whatsmyname($tts->f('uid'))."</td></tr>";
			}
		$content.="</table>";
		$button=new button();
		$button_submit= $button->get_button($cmdOk,"javascript:this.form.submit();");
		$button_reset= $button->get_button($cmdReset,"javascript:this.for
m.reset();");
		$content.="$button_submit.$button_reset</form>";
		return $content;
		}
		return FALSE;
	}
	
	function set_default_agent($uid){
		global $tts,$prefix,$hlpdsk_prefix,$name;
		$uid=Security::sqlsecure($uid);
                $query="update {$prefix}{$hlpdsk_prefix}_groups_members set uid_default=0 where gid=2";
		if ($tts->query($query, $tts)){
			$query="update {$prefix}{$hlpdsk_prefix}_groups_members set uid_default=1 where gid=2 and  uid='$uid'";
			if ($tts->query($query, $tts)){
				return TRUE;
			}
		}
		return FALSE;
	}
}
?>
