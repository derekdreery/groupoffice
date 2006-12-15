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
//Class sql: (static) deals with permission to run methods.
class SQL{
	var $err_nr;
	var $err_message;

	function SQL(){
		return TRUE;
	}
	
	function build_hash($db_table,$index,$value,$condition=""){
		global $prefix,$tts;
		$query="select $index,$value from $db_table where 1 $condition";
		$tts->query($query);
		if (strpos($index,'.')){
			$short_index=substr(strstr($index,'.'),1);
		}else{
			$short_index=$index;
		}
		$list= array();
		while($tts->next_record()){
			$h_index=$tts->f("$short_index");
                        $list[$h_index]=$tts->f("$value");
                }
		return $list;
	}

	function build_array($db_table,$value,$condition=""){
                global $prefix,$tts;
                $query="select $value from $db_table where 1 $condition";
                $tts->query($query); 
                while($tts->next_record()){
                        $list[]=$tts->f("$value");
                }
                return $list;
        }



}
?>
