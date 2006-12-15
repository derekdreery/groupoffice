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


class Statistics{
	//vars

	//constructor
	function Statistics(){
		return TRUE;
	}
	//methods
	//draw a chart of x/y 
	function display_xy($x_y_array,$chart_title="Chart Title",$x_display_name="X values",$y_display_name="Y values"){
		$col_total=count($x_y_array)+1;
		$response="<h2><font class=content>$chart_title</h2><br>";
		$response.="<table border=1><tr><td valign=center><font class=content>$y_display_name</td><td>";
		$response.="<table border=1><tr>";
		if ($col_total==1) {
			$response.="<td><font class=content> No Data available </td>";
		}else{
			foreach($x_y_array as $x=>$y){
				$response.="<td><font class=content>$y</td>";
			}
			$response.="</tr><tr>";
			foreach($x_y_array as $x=>$y){
                                $response.="<td><font class=content>$x</td>";
                        }
		}
		$response.="</tr></table>";
		$response.="</td></tr><tr><td colspan=$col_total align=center><font class=content>$x_display_name</td></tr></table>";
		return $response;
	}
	
	 function display_xyz($x_y_z_array,$chart_title="Chart Title",$x_display_name="X values",$y_display_name="Y values",$z_display_name="Z values"){
                $col_total=count($x_y_z_array)+1;
                $response="<h2><font class=content>$chart_title</h2><br>";
                $response.="<table border=1>";
                if ($col_total==1) {
                        $response.="<tr><td><font class=content> No Data available </td></tr>";
                }else{
			foreach($x_y_z_array as $xkey=>$xvalue){
				$response.="<tr><td colspan=2>&nbsp;&nbsp;</td></tr><tr><td colspan=2 bgcolor=grey>$xkey </td></tr>";
				foreach($x_y_z_array[$xkey]
						as $ykey=>$yvalue){
					$response.= "<tr><td>&nbsp;&nbsp;</td><td><font class=content>$xkey: $ykey -> ".$x_y_z_array[$xkey][$ykey]." </td></tr> ";
				}
			}
                }
                $response.="</table>";
                return $response;
        }

}
