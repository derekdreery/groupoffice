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

class Agents{
        var $a_agents;
        //error variables;
        var $err_nr;
        var $err_message;
        //constructor
        function Agents(){
                return TRUE;
        }
        function sql_fetch_array(){
                global $dbi,$user,$name,$prefix,$hlpdsk_prefix,$nuke_username_fieldname,$nuke_user_id_fieldname;
		$gid_array=array();
		$gid=2;
		$gid_array=Security::fetch_members($gid);
		foreach ($gid_array  as $key=>$uid){
			$this->a_agents[$uid]=Security::get_uname("$uid");
               	}
		return TRUE;
        }
        function build_select(){
                $options="";
                foreach($this->a_agents as $key=>$value){
			$fullname=Security::whats_his_name($key);
                        $options.= "<option value='$key'>$fullname : $value</option>";
                }
                return "$options";
        }       
        function fill_select($select_name='t_assigned'){
                $this->sql_fetch_array();                                                                                      
                $select="<select name='$select_name'>";                                                                        
                $select.=$this->build_select();
                $select.="</select>";
                return "$select";
        }
        function fetch_uname($uid){
                return $this->a_agents[$uid];
        }
        function fetch_uid($uname){
                return array_search("$uname",$this->a_agents);
        }
}
?>
