<?php

class Task {

	//
	function Task(){
		return TRUE;
	}
	//
	function get_task_issuer($ticket_number){
		global $prefix,$hlpdsk_prefix;
		$ticket_number=Security::sqlsecure($ticket_number);
		return get_cross_value("{$prefix}{$hlpdsk_prefix}_tickets","t_from", " where ticket_number='$ticket_number'");
	}

	function get_task_assigned($ticket_number){
                global $prefix,$hlpdsk_prefix;
                $ticket_number=Security::sqlsecure($ticket_number);
                return get_cross_value("{$prefix}{$hlpdsk_prefix}_tickets","t_assigned", " where ticket_number='$ticket_number'");
        }

	function insert_new_task($ticket_number,$sender,$comment=""){
		global $tts,$prefix,$hlpdsk_prefix;
		$comment=addslashes($comment);
		$date=time();
		$query="insert into {$prefix}{$hlpdsk_prefix}_tasks (ticket_id,sender_id,comment,post_date) values ('$ticket_number','$sender','$comment','$date')";
		$tts->query($query);
		if (TRUE){
			$query="update {$prefix}{$hlpdsk_prefix}_tickets set change_date='$date' where ticket_number='$ticket_number'";
			$tts->query($query);
		}
		return true;
	}
}
