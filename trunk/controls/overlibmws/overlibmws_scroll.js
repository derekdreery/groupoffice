/*
 overlibmws_scroll.js plug-in module - Copyright Foteos Macrides 2002-2004.
  For support of the SCROLL feature.
  Initial: October 20, 2002 - Last Revised: September 27, 2004
 See the Change History and Command Reference for overlibmws via:

	http://www.macridesweb.com/oltest/

 Published under an open source license: http://www.macridesweb.com/oltest/license.html
*/

// PRE-INIT
OLloaded=0;
OLregCmds('scroll');

/////////
// DEFAULT CONFIGURATION
if(typeof ol_scroll=='undefined')var ol_scroll=0;
// END CONFIGURATION
////////

// INIT
var o3_scroll=0,OLscrollRefresh=100;

// Loads runtime variable defaults.
function OLloadScroll(){
o3_scroll=ol_scroll;
}

// For commandline parser.
function OLparseScroll(pf,i,ar){
var k=i;
if(k<ar.length){
if(ar[k]==SCROLL){eval(pf+'scroll=('+pf+'scroll==0)?1:0');return k;}
if(ar[k]==-SCROLL){eval(pf+'scroll=0');return k;}}
return -1;
}

////////
// SCROLL SUPPORT FUNCTIONS
////////
// Sanity check, Init scroll timer
function OLchkScroll(X,Y){
if(o3_scroll){if(!o3_showingsticky||(OLdraggablePI&&o3_draggable&&o3_frame==self)||
(o3_relx==null&&o3_midx==null)||(o3_rely==null&&o3_midy==null))o3_scroll=0;
else if(typeof over.scroll=='undefined'||over.scroll.canScroll)
over.scroll=new OLsetScroll(X,Y,OLscrollRefresh);}
}

// Set, Clear scroll timer
function OLsetScroll(X,Y,refresh){
if(o3_scroll){this.canScroll=0;this.refresh=refresh;this.x=X;this.y=Y;
this.timer=setTimeout("OLscrollReposition()",this.refresh);}
}
function OLclearScroll(){
if(o3_scroll){if(typeof over.scroll=='undefined'){o3_scroll=0;return;}
over.scroll.canScroll=1;if(over.scroll.timer){
clearTimeout(over.scroll.timer);over.scroll.timer=null;}}
}

// Repositions the layer if needed
function OLscrollReposition() {
if(o3_scroll&&over&&over==OLgetRefById('overDiv',o3_frame.document)){
var X,Y,pgLeft,pgTop;
pgLeft=(OLie4)?OLfd().scrollLeft:o3_frame.pageXOffset;
pgTop=(OLie4)?OLfd().scrollTop:o3_frame.pageYOffset;
X=(over.pageX?over.pageX:over.style.left?over.style.left:0)-pgLeft;
Y=(over.pageY?over.pageY:over.style.top?over.style.top:0)-pgTop;
if(X!=over.scroll.x||Y!=over.scroll.y){
OLrepositionTo(over,pgLeft+over.scroll.x,pgTop+over.scroll.y);
if(OLshadowPI)OLrepositionShadow(pgLeft+over.scroll.x,pgTop+over.scroll.y);
if(OLiframePI)OLrepositionIfShim(pgLeft+over.scroll.x,pgTop+over.scroll.y);
if(OLhidePI)OLhideUtil(0,1,1,0,0,0);}
over.scroll.timer=setTimeout("OLscrollReposition()",over.scroll.refresh);}
}

////////
// PLUGIN REGISTRATIONS
////////
OLregRunTimeFunc(OLloadScroll);
OLregCmdLineFunc(OLparseScroll);

OLscrollPI=1;
OLloaded=1;
