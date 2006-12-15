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




function view_profile($uname){
return "";
}



#*********************************
#old functions.php
#get list of agents


//classes



class Ticket{
	var $ticket_nr;
        var $issuer;
        var $subject;
        var $description;
        var $due_date;
        var $end_date;
        var $complete;
        var $post_date;
        var $stage_id;
        var $category_id;
        var $priority_id;
        var $change_date;
        var $assigned_id;
        var $notify_priv_msg;
        var $notify_email;
        var $comments;
        var $status_id;
        var $project_id;
	var $acl_read;
	var $acl_write;
        //error variables;
        var $err_nr;
        var $err_message;
	function Ticket(){
		$this->status_id='1';
		$this->assigned_id='1';
		$this->issuer='1';
		$this->stage_id='1';
		$this->category_id='1';
		$this->priority_id='1';
		$this->subject=' [No subject] ';
		$this->description= ' [No Description] ';
		$this->post_date=$this->change_date=$this->end_date=$this->due_date=time();
		$this->complete='0';
		$this->notify_email=$this->notify_priv_msg='';
		$this->project_id='1';
		return TRUE;
	}
	//add here gets and sets;
	function set_issuer($uid){
		// verify existance	
		$this->issuer=$uid;
	}
	
	function set_assigned($uid){
                // verify existance     
                $this->assigned_id=$uid;
        }
	
	function sql_query($ticket_id){
		global $tts,$user,$name,$prefix,$hlpdsk_prefix;
		//load values from db
		$query="select `ticket_number`, 
		`t_assigned`,
                `t_from`,
                `t_stage`,
                `t_category`,
                `t_priority`,
                `t_subject`,
                `t_description`,
                `t_comments`,
                `post_date`,
                `complete`,
                `due_date`,
                `end_date`,
                `change_date`,
                `t_status`,
                `t_sms`,
                `t_email`

		where 
                 Ticket_Number='{$this->ticket_nr}'";
                if ($result = sql_query ($query)){
			list(
			$this->ticket_nr,
                        $this->assigned_id,
                        $this->issuer,
                        $this->stage_id,
                        $this->category_id,
                        $this->priority_id ,
                        $this->subject,
                        $this->description,
                        $this->comments,
                        $this->post_date,
                        $this->complete,
                        $this->due_date,
                        $this->end_date,
                        $this->change_date,
                        $this->status_id,
                        $this->notify_priv_msg,
                	$this->notify_email)=sql_fetch_row($result);
		}
		return $result;
	}

	function sql_insert(){
		global $tts,$user,$name,$prefix,$hlpdsk_prefix;
		//save values to db
		$transac_id=$this->issuer . time();
		$query="insert into {$prefix}{$hlpdsk_prefix}_tickets 
		(`ticket_number`, 
		`t_assigned`,
                `t_from`,
                `t_stage`,
                `t_category`,
                `t_priority`,
                `t_subject`,
                `t_description`,
                `t_comments`,
                `post_date`,
                `complete`,
                `due_date`,
                `end_date`,
                `change_date`,
                `t_status`,
                `t_sms`,
                `t_email`,
                `transac_id`,
                `project_id`,
		`acl_read`,
		`acl_write`
		)
                 values (
                '',
                '{$this->assigned_id}',
                '{$this->issuer}',
                '{$this->stage_id}',
                '{$this->category_id}',
                '{$this->priority_id}' ,
                '{$this->subject}',
                '{$this->description}',
                '{$this->comments}',
                '{$this->post_date}',
                '{$this->complete}',
                '{$this->due_date}',
                '{$this->end_date}',
                '{$this->change_date}',
                '{$this->status_id}',
                '{$this->notify_priv_msg}',
                '{$this->notify_email}',
                '$transac_id',
                '{$this->project_id}',
		'{$this->acl_read}',
		'{$this->acl_write}'
		);";

		if ($tts->query($query)){
			$query="select ticket_number from {$prefix}{$hlpdsk_prefix}_tickets
				where transac_id='$transac_id'";
			$tts->query($query);
			if ($tts->next_record()){
				$this->ticket_nr=$tts->f('ticket_number');
				return TRUE;
			}
		}
		return FALSE;
	}
	function sql_update(){
                global $tts,$user,$name,$prefix,$hlpdsk_prefix;
                //save values to db
		$querytext="update  {$prefix}{$hlpdsk_prefix}_tickets set 
	       	 t_status='{$this->status_id}',
        	 t_assigned='{$this->assigned_id}',
        	 t_from='{$this->issuer}',
        	 t_stage='{$this->stage_id}',
        	 t_category='{$this->category_id}',
        	 t_priority='{$this->priority_id}' , 
	         t_subject='{$this->subject}',
        	 t_description='{$this->description}',
	         t_comments='{$this->comments}',
	         change_date='{$this->change_date}',
	         due_date='{$this->due_date}',
	         post_date='{$this->post_date}',
	         t_sms='{$this->notify_priv_msg}',
	         t_email='{$this->notify_email}'
	        where 
	         Ticket_Number='{$this->ticket_nr}'";
		return  sql_query ($querytext);
			
	}
	function change_status($Ticket_Number,$status_id){
                global $tts,$user,$name,$prefix,$hlpdsk_prefix;
                $status_id=Security::sqlsecure($status_id);
                $Ticket_Number=Security::sqlsecure($Ticket_Number);
                $query="update {$prefix}{$hlpdsk_prefix}_tickets set t_status='$status_id' where ticket_number='$Ticket_Number'";
                if ($tts->query($query)){
                        return TRUE;
                }else{
                        return FALSE;
                }
        }
	
	function change_project_id($Ticket_Number,$project_id){
                global $tts,$user,$name,$prefix,$hlpdsk_prefix;
                $project_id=Security::sqlsecure($project_id);
                $Ticket_Number=Security::sqlsecure($Ticket_Number);
                $query="update {$prefix}{$hlpdsk_prefix}_tickets set project_id='$project_id' where ticket_number='$Ticket_Number'";
                if ($tts->query($query)){
                        return TRUE;
                }else{  
                        return FALSE;
                }
        }


        function change_subject($Ticket_Number,$t_subject){
                global $tts,$user,$name,$prefix,$hlpdsk_prefix;
                $t_subject=Security::sqlsecure($t_subject);
                $Ticket_Number=Security::sqlsecure($Ticket_Number);
                $query="update {$prefix}{$hlpdsk_prefix}_tickets set t_subject='$t_subject' where ticket_number='$Ticket_Number'";
                if ($tts->query($query)){
                        return TRUE;
                }else{
                        return FALSE;
                }
        }

        function change_due_date($Ticket_Number,$due_date){
                global $tts,$user,$name,$prefix,$hlpdsk_prefix;
                $due_date=Security::sqlsecure($due_date);
                $Ticket_Number=Security::sqlsecure($Ticket_Number);
                $query="update {$prefix}{$hlpdsk_prefix}_tickets set due_date='$due_date' where ticket_number='$Ticket_Number'";
                if ($tts->query($query)){
                        return TRUE;
                }else{
                        return FALSE;
                }
        }

        function change_end_date($Ticket_Number,$end_date){
                global $tts,$user,$name,$prefix,$hlpdsk_prefix;
                $end_date=Security::sqlsecure($end_date);
                $Ticket_Number=Security::sqlsecure($Ticket_Number);
                $query="update {$prefix}{$hlpdsk_prefix}_tickets set end_date='$end_date' where ticket_number='$Ticket_Number'";
                if ($tts->query($query)){
                        return TRUE;
                }else{
                        return FALSE;
                }
        }


        function change_priority($Ticket_Number,$status_id){
                global $tts,$user,$name,$prefix,$hlpdsk_prefix;
                $status_id=Security::sqlsecure($status_id);
                $Ticket_Number=Security::sqlsecure($Ticket_Number);
                $query="update {$prefix}{$hlpdsk_prefix}_tickets set t_priority='$status_id' where ticket_number='$Ticket_Number'";
                if ($tts->query($query)){
                        return TRUE;
                }else{
                        return FALSE;
                }
        }

        function change_stage($Ticket_Number,$stage_id){
                global $tts,$user,$name,$prefix,$hlpdsk_prefix;
                $stage_id=Security::sqlsecure($stage_id);
                $Ticket_Number=Security::sqlsecure($Ticket_Number);
                $query="update {$prefix}{$hlpdsk_prefix}_tickets set t_stage='$stage_id' where ticket_number='$Ticket_Number'";
                if ($tts->query($query)){
                        return TRUE;
                }else{  
                        return FALSE;
                }
        }


        function change_category($Ticket_Number,$category_id){
                global $tts,$user,$name,$prefix,$hlpdsk_prefix;
                $category_id=Security::sqlsecure($category_id);
                $Ticket_Number=Security::sqlsecure($Ticket_Number);
                $query="update {$prefix}{$hlpdsk_prefix}_tickets set t_category='$category_id' where ticket_number='$Ticket_Number'";
                if ($tts->query($query)){
                        return TRUE;
                }else{
                        return FALSE;
                }
        }
	function change_complete($Ticket_Number,$complete){
                global $tts,$user,$name,$prefix,$hlpdsk_prefix;
                $complete=Security::sqlsecure($complete);
                $Ticket_Number=Security::sqlsecure($Ticket_Number);
                $query="update {$prefix}{$hlpdsk_prefix}_tickets set complete='$complete' where ticket_number='$Ticket_Number'";
                if ($tts->query($query)){
                        return TRUE;
                }else{
                        return FALSE;
                }
        }



        function change_assigned($Ticket_Number,$t_assigned){
                global $tts,$user,$name,$prefix,$hlpdsk_prefix;
                $t_assigned=Security::sqlsecure($t_assigned);
                $Ticket_Number=Security::sqlsecure($Ticket_Number);
                $query="update {$prefix}{$hlpdsk_prefix}_tickets set t_assigned='$t_assigned' where ticket_number='$Ticket_Number'";
                if ($tts->query($query)){
                        return TRUE;
                }else{
                        return FALSE;
                }
        }

        function change_issuer($Ticket_Number,$issuer){
                global $tts,$user,$name,$prefix,$hlpdsk_prefix;
                $issuer=Security::sqlsecure($issuer);
                $Ticket_Number=Security::sqlsecure($Ticket_Number);
                $query="update {$prefix}{$hlpdsk_prefix}_tickets set t_from='$issuer' where ticket_number='$Ticket_Number'";
                if ($tts->query($query)){
                        return TRUE;
                }else{
                        return FALSE;
                }
        }

        function get_ticket_issuer_uid($Ticket_Number){
                global $prefix,$hlpdsk_prefix;
                $Ticket_Number=Security::sqlsecure($Ticket_Number);
                return get_cross_value("{$prefix}{$hlpdsk_prefix}_tickets","t_from"," where  Ticket_Number='$Ticket_Number' ");
        }

        function get_ticket_subject($Ticket_Number){
                global $prefix,$hlpdsk_prefix;
                $Ticket_Number=Security::sqlsecure($Ticket_Number);
                return get_cross_value("{$prefix}{$hlpdsk_prefix}_tickets","t_subject"," where  Ticket_Number='$Ticket_Number' ");
        }

        function get_ticket_category_id($Ticket_Number){
                global $prefix,$hlpdsk_prefix;
                $Ticket_Number=Security::sqlsecure($Ticket_Number);
                return get_cross_value("{$prefix}{$hlpdsk_prefix}_tickets","t_category"," where  Ticket_Number='$Ticket_Number' ");
        }

        function get_ticket_stage_id($Ticket_Number){
                global $prefix,$hlpdsk_prefix;
                $Ticket_Number=Security::sqlsecure($Ticket_Number);
                return get_cross_value("{$prefix}{$hlpdsk_prefix}_tickets","t_stage"," where  Ticket_Number='$Ticket_Number' ");
        }




	function get_ticket_status_id($Ticket_Number){
                global $prefix,$hlpdsk_prefix;
                $Ticket_Number=Security::sqlsecure($Ticket_Number);
                return get_cross_value("{$prefix}{$hlpdsk_prefix}_tickets","t_status"," where  Ticket_Number='$Ticket_Number' ");
        }
	
	function get_ticket_project_id($Ticket_Number){
                global $prefix,$hlpdsk_prefix;
                $Ticket_Number=Security::sqlsecure($Ticket_Number);
                return get_cross_value("{$prefix}{$hlpdsk_prefix}_tickets","project_id"," where  Ticket_Number='$Ticket_Number' ");
        }
	function get_ticket_complete($Ticket_Number){
                global $prefix,$hlpdsk_prefix;
                $Ticket_Number=Security::sqlsecure($Ticket_Number);
                return get_cross_value("{$prefix}{$hlpdsk_prefix}_tickets","complete"," where  Ticket_Number='$Ticket_Number' ");
        }


        function get_category_name($category_id){
                global $prefix,$hlpdsk_prefix;
                $category_id=Security::sqlsecure($category_id);
                return get_cross_value("{$prefix}{$hlpdsk_prefix}_categories","category_name", " where category_id='$category_id'");
        }

       function get_stage_name($stage_id){
                global $prefix,$hlpdsk_prefix;
                $stage_id=Security::sqlsecure($stage_id);
                return get_cross_value("{$prefix}{$hlpdsk_prefix}_stages","stage_name", " where stage_id='$stage_id'");
        }




	function get_status_name($status_id){
		global $prefix,$hlpdsk_prefix;
		$status_id=Security::sqlsecure($status_id);
		return get_cross_value("{$prefix}{$hlpdsk_prefix}_status","status_name", " where status_id='$status_id'");
	}

	function get_project_name($project_id){
                global $prefix,$hlpdsk_prefix;
                $project_id=Security::sqlsecure($project_id);
                return get_cross_value("{$prefix}{$hlpdsk_prefix}_projects","project_name", " where project_id='$project_id'");
        }

	function get_due_date($Ticket_Number){
                global $prefix,$hlpdsk_prefix;
                $Ticket_Number=Security::sqlsecure($Ticket_Number);
                return get_cross_value("{$prefix}{$hlpdsk_prefix}_tickets","due_date", " where Ticket_Number='$Ticket_Number' ");
        }
	function get_end_date($Ticket_Number){
                global $prefix,$hlpdsk_prefix;
                $Ticket_Number=Security::sqlsecure($Ticket_Number);
                return get_cross_value("{$prefix}{$hlpdsk_prefix}_tickets","end_date", " where Ticket_Number='$Ticket_Number' ");
        }


	function ticket_related($Ticket_Number){
		global $prefix,$hlpdsk_prefix;
		$uid=whoami();
		return get_cross_value("{$prefix}{$hlpdsk_prefix}_tickets","Ticket_Number"," where (t_from='$uid' or t_assigned='$uid') and Ticket_Number='$Ticket_Number' ");

	}
	
	function get_ticket_assigned_uid($Ticket_Number){
                global $prefix,$hlpdsk_prefix;
		$Ticket_Number=Security::sqlsecure($Ticket_Number);
		return get_cross_value("{$prefix}{$hlpdsk_prefix}_tickets","t_assigned"," where  Ticket_Number='$Ticket_Number' ");
	}

	function get_ticket_priority_id($Ticket_Number){
                global $prefix,$hlpdsk_prefix;
                $Ticket_Number=Security::sqlsecure($Ticket_Number);
                return get_cross_value("{$prefix}{$hlpdsk_prefix}_tickets","t_priority"," where  Ticket_Number='$Ticket_Number' ");
        }
	
	function get_priority_name($priority_id){
                global $prefix,$hlpdsk_prefix;
                $priority_id=Security::sqlsecure($priority_id);
                return get_cross_value("{$prefix}{$hlpdsk_prefix}_priorities","priority_name"," where  priority_id='$priority_id' ");
        }

        function get_ticket_activity_id($Ticket_Number){
                global $prefix,$hlpdsk_prefix;
                $Ticket_Number=Security::sqlsecure($Ticket_Number);
                return get_cross_value("{$prefix}{$hlpdsk_prefix}_tickets","activity_id"," where  Ticket_Number='$Ticket_Number' ");
        }

        function get_activity_name($activity_id){
                global $prefix,$hlpdsk_prefix;
                $activity=Security::sqlsecure($activity_id);
                return get_cross_value("{$prefix}{$hlpdsk_prefix}_activities","activity_name"," where  activity_id='$activity_id' ");
        }

        function change_activity_id($Ticket_Number,$activity_id){
                global $tts,$user,$name,$prefix,$hlpdsk_prefix;
                $activity_id=Security::sqlsecure($activity_id);
                $Ticket_Number=Security::sqlsecure($Ticket_Number);
                $query="update {$prefix}{$hlpdsk_prefix}_tickets set activity_id='$activity_id' where ticket_number='$Ticket_Number'";
                if ($tts->query($query)){
                        return TRUE;
                }else{
                        return FALSE;
                }
        }


}

?>

