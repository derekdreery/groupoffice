<?php
/*
   Copyright Intermesh 2003
   Author: Merijn Schering <mschering@intermesh.nl>
   Version: 1.0 Release date: 08 July 2003

   This program is free software; you can redistribute it and/or modify it
   under the terms of the GNU General Public License as published by the
   Free Software Foundation; either version 2 of the License, or (at your
   option) any later version.
 */

require_once("../../Group-Office.php");
$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('calendar');
require_once($GO_LANGUAGE->get_language_file('calendar'));

$GO_CONFIG->set_help_url($cal_help_url);


require_once($GO_MODULES->class_path.'calendar.class.inc');
$cal = new calendar();


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php
//$GO_THEME->load_module_theme('email');

require($GO_CONFIG->root_path.'default_head.inc');
require($GO_CONFIG->root_path.'default_scripts.inc');
echo $GO_THEME->get_stylesheet('calendar');
?>
<script type="text/javascript" src="language/en.js"></script>
<style>
#calendar-grid{
width:100%;
}

.evenRow{
	border-top:1px solid #DDDDDD;
	border-left:1px solid #DDDDDD;
	height:20px;
}
.unevenRow{
	border-top:1px dotted #DDDDDD;
	border-left:1px solid #DDDDDD;
	height:20px;
}
.timeHead{
	background-color:#f1f1f1;
	height:41px;
	width:100%;
	border-top:1px solid #DDDDDD;
	text-align:right;
}
</style>
</head>
<body>



<table border="0" style="table-layout: fixed; width: 100%;">
<tr>
	<td style="width:50px;vertical-align:top">
	<?php
	for($i=0;$i<24;$i++)
	{
		echo '<div id="head'.$i.'" class="timeHead">'.date($_SESSION['GO_SESSION']['time_format'], mktime($i,0)).'</div>';
	}
	?></td>
	<td style="width:auto;vertical-align:top">
	<div id="col0">
	<?php
	for($i=0;$i<24;$i++)
	{
		echo '<div id="row'.$i.'" class="evenRow" onmousedown="selector.startSelection(\'row'.$i.'\',\'col0\');"></div>'.
			'<div id="row'.$i.'" class="unevenRow" onmousedown="selector.startSelection(\'row'.$i.'\',\'col0\');"></div>';
	}
	?>
	</div>
	</td>
</tr>
</table>

<div id="selector" style="background-color:#ffffcc;position:absolute;visibility:hidden;z-index:10000"></div>

<script type="text/javascript">

selector = function(){ 
	
	return {
		startSelection : function (row, col){
		
			var startRow = Ext.get(row);
			
			var position = startRow.getXY();
			var size=startRow.getSize();
			this.snapSize=size['height'];
			
			if(!this.el)
			{
				this.el = Ext.get("selector");
				
			}
			this.el.setOpacity(.4);
			this.el.setVisible(true,false);
			this.el.setXY(position);
			this.el.setSize(size['width'], size['height']);
		
			if(!this.overlay){
                this.overlay = this.el.createProxy({tag: "div", cls: "x-resizable-overlay", html: "&#160;"});
                this.overlay.unselectable();
                this.overlay.enableDisplayMode("block");
                this.overlay.on("mousemove", this.onMouseMove, this);
                this.overlay.on("mouseup", this.onMouseUp, this);
            }
            
            this.overlay.setSize(Ext.lib.Dom.getViewWidth(true), Ext.lib.Dom.getViewHeight(true));
            this.overlay.show();
			
			
	
		},	
		onMouseMove : function (e){

			var eventPos = e.getXY();
					
			var shadowPos = this.el.getXY();
			
			var height = this.el.getHeight();
			
			var increment = this.snap(eventPos[1]-shadowPos[1],this.snapSize, 0);
			this.el.setHeight(increment);		
		},	
		onMouseUp : function (e){
			//this.el.setSize(0,0);		
			this.overlay.hide();	
			this.el.setVisible(false,true);	
		},
	
	    // private
	    snap : function(value, inc, min){
	        if(!inc || !value) return value;
	        var newValue = value;
	        var m = value % inc;
	        if(m > 0){
	            if(m > (inc/2)){
	                newValue = value + (inc-m);
	            }else{
	                newValue = value - m;
	            }
	        }
	        return Math.max(min, newValue);
	    }
	}
};

var selector = new selector();
Ext.EventManager.onDocumentReady(function(){
	
});
</script>

</body>
</html>

