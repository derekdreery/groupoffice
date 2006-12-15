/*
 overlibmws_draggable.js plug-in module - Copyright Foteos Macrides 2002=2004
   For support of the DRAGGABLE feature.
   Initial: August 24, 2002 - Last Revised: July 23, 2004
 See the Change History and Command Reference for overlibmws via:

	http://www.macridesweb.com/oltest/

 Published under an open source license: http://www.macridesweb.com/oltest/license.html
*/

// PRE-INIT
OLloaded=0;
OLregCmds('draggable');

/////////
// DEFAULT CONFIGURATION
if(typeof ol_draggable=='undefined')var ol_draggable=0;
// END CONFIGURATION
/////////

// INIT
var o3_draggable=0,o3_dragging=0,OLmMv;

// Loads runtime variable defaults.
function OLloadDraggable(){
o3_draggable=ol_draggable;
}

// For commandline parser.
function OLparseDraggable(pf,i,ar){
var k=i;
if(k<ar.length){
if(ar[k]==DRAGGABLE){eval(pf+'draggable=('+pf+'draggable==0)?1:0');return k;}
if(ar[k]==-DRAGGABLE){eval(pf+'draggable=0');return k;}}
return -1;
}

/////////
// DRAGGABLE SUPPORT FUNCTIONS
/////////
// Act on DRAGGABLE if sticky and in our frame, otherwise, make sure dragging is off.
function OLcheckDrag(){
if(o3_draggable&&o3_sticky&&(o3_frame==self)){if(!o3_dragging)OLinitDrag();}
else{if(o3_dragging)OLclearDrag();}
}

function OLinitDrag(){
OLmMv=OLcapExtent.onmousemove;
if(OLns4){
document.captureEvents(Event.MOUSEDOWN|Event.CLICK);
document.onmousedown=OLgrabEl;
document.onclick=function(e){return routeEvent(e);}
}else{
over.onmousedown=OLgrabEl;
if(OLie4&&over.onselectstart!='undefined')over.onselectstart=function(){return false;}
over.style.cursor="move";}
o3_dragging=1;
return true;
}

function OLgrabEl(e){
var e=(e)?e:event;
var cKy=(OLns4?e.modifiers&Event.ALT_MASK:(!OLop7)?e.altKey:e.ctrlKey);
if(cKy){
if(OLie4&&over.onselectstart!='undefined')over.onselectstart=null;
if(!OLns4)over.style.cursor="auto";
document.onmouseup=function(){
if(!OLns4){
if(OLie4&&over.onselectstart!='undefined') over.onselectstart=function(){return false;}
if(!OLns4)over.style.cursor="move";}}
return(OLns4?routeEvent(e):true);}
OLmMv(e);
if(OLns4){
cX=e.pageX;cY=e.pageY;
}else{
cX=o3_x-(parseInt(over.style.left));cY=o3_y-(parseInt(over.style.top));
if((OLshadowPI)&&bkdrop&&o3_shadow){
cbX=o3_x-(parseInt(bkdrop.style.left));cbY=o3_y-(parseInt(bkdrop.style.top));}}
if(OLns4)document.captureEvents(Event.MOUSEMOVE|Event.MOUSEUP);
OLcapExtent.onmousemove=OLmoveEl;
document.onmouseup=function(){OLcapExtent.onmousemove=OLmMv;document.onmouseup=null;}
return (OLns4?routeEvent(e):false);
}

function OLmoveEl(e){
OLmMv(e);
if(OLns4){
newX=e.pageX;newY=e.pageY;
over.moveBy(newX-cX,newY-cY);
if((OLshadowPI)&&bkdrop&&o3_shadow)bkdrop.moveBy(newX-cX,newY-cY);
cX=newX;cY=newY;
}else{
OLrepositionTo(over,o3_x-cX,o3_y-cY);
if((OLiframePI)&&OLie55&&OLifShim)OLrepositionTo(OLifShim,o3_x-cX,o3_y-cY);
if((OLshadowPI)&&bkdrop&&o3_shadow){
OLrepositionTo(bkdrop,o3_x-cbX,o3_y-cbY);
if((OLiframePI)&&OLie55&&OLifShimShadow)OLrepositionTo(OLifShimShadow,o3_x-cbX,o3_y-cbY);}}
if(OLhidePI)OLhideUtil(0,1,1,0,0,0);
return false;
}

function OLclearDrag(){
if(OLns4){
document.releaseEvents(Event.MOUSEDOWN|Event.MOUSEUP|Event.CLICK);
document.onmousedown=document.onmouseup=document.onclick=null;
}else if(OLie4||OLns6){
over.onmousedown=null;
if(OLie4&&over.onselectstart!='undefined')over.onselectstart=null;
over.style.cursor="auto";}
o3_dragging=0;
}

////////
// PLUGIN REGISTRATIONS
////////
OLregRunTimeFunc(OLloadDraggable);
OLregCmdLineFunc(OLparseDraggable);

OLdraggablePI=1;
OLloaded=1;
