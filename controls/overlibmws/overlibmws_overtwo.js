/*
 overlibmws_overtwo.js plug-in module - Copyright Foteos Macrides 2003-2004
   For support of the popups-within-a-popup feature.
   Initial: July 14, 2003 - Last Revised: September 11, 2004
 See the Change History and Command Reference for overlibmws via:

	http://www.macridesweb.com/oltest/

 Published under an open source license: http://www.macridesweb.com/oltest/license.html
*/

// PRE-INIT
OLloaded=false;

// INIT
var OLzIndex,over2=null,OLp1=null,OLp1Drop=null,OLp1Relx=null,OLp1Rely=null,OLp1Midx=null;
var OLp1Midy=null,OLp1Ref=null,OLp1Refc=null,OLp1Refp=null,OLp1Refx=null,OLp1Refy=null;
var OLp1Drag=0,OLp1Scroll=0,OLp1X=0,OLp1Y=0,OLp1If=null,OLp1IfShadow=null,OLp1Status="";
var OLp1Autostatus=0,OLp1Bubble=0,OLp1Hover=0,OLp1Width=0;

/////////
// PUBLIC FUNCTIONS
/////////
function overlib2(){
var args=overlib2.arguments;
if(!o3_showingsticky||args.length==0)return;
if(o3_timerid>0){clearTimeout(o3_timerid);o3_timerid=0;OLoptMOUSEOFF(1);}
if(typeof over.onmouseover!='undefined'&&over.onmouseover!=null){
OLp1Hover=1;OLhover=0;over.onmouseover=null;}
if(OLdraggablePI&&o3_draggable){OLp1Drag=1;o3_draggable=0;}
if((OLshadowPI)&&bkdrop){OLp1Drop=bkdrop;bkdrop=null;}
if((OLiframePI)&&OLifShim){OLp1If=OLifShim;OLifShim=null;
if(OLifShimShadow){OLp1IfShadow=OLifShimShadow;OLifShimShadow=null;}}
if(OLscrollPI&&o3_scroll){OLp1X=over.scroll.x;OLp1Y=over.scroll.y;OLclearScroll();
OLp1Scroll=1;o3_scroll=0;}OLp1Width=o3_width;
OLp1Relx=o3_relx;OLp1Rely=o3_rely;OLp1Midx=o3_midx;OLp1Midy=o3_midy;
OLp1Ref=o3_ref;OLp1Refc=o3_refc;OLp1Refp=o3_refp;OLp1Refx=o3_refx;
OLp1Refy=o3_refy;OLp1Status=o3_status;OLp1Autostatus=o3_autostatus;
OLloadP1or2();OLp1=null;if(OLfunctionPI)o3_function=ol_function;
OLp1Bubble=(OLbubblePI&&o3_bubble)?1:0;
OLparseTokens('o3_',args);if(OLbubblePI)o3_bubble=0;
if(o3_autostatus==2&&o3_cap!="")o3_status=o3_cap;
else if(o3_autostatus==1&&o3_text!="")o3_status=o3_text;
if(o3_delay==0)OLdispP2();else o3_delayid=setTimeout("OLdispP2()",o3_delay);
if(o3_status!=""){self.status=o3_status;return true;}
else if(!(OLop7&&event&&event.type=='mouseover'))return false;
}

function nd2(){
if(OLp1Drop){bkdrop=OLp1Drop;OLp1Drop=null;}
if(OLp1Drag){o3_draggable=1;OLp1Drag=0;}
OLhideObjectP2(over2);
if(OLp1){over=OLp1;OLp1=null;}
if((OLiframePI)&&OLp1If){OLifShim=OLp1If;OLp1If=null;
if(OLp1IfShadow){OLifShimShadow=OLp1IfShadow;OLp1IfShadow=null;}}
o3_relx=OLp1Relx;o3_rely=OLp1Rely;o3_midx=OLp1Midx;o3_midy=OLp1Midy;
o3_ref=OLp1Ref;o3_refc=OLp1Refc;o3_refp=OLp1Refp;o3_refx=OLp1Refx;o3_refy=OLp1Refy;
if(OLp1Bubble){o3_bubble=1;OLp1Bubble=0;}if(OLp1Width){o3_width=OLp1Width;OLp1Width=0;}
if(OLp1Scroll){o3_scroll=1;OLp1Scroll=0;OLchkScroll(OLp1X,OLp1Y);}
if(OLhidePI)OLhideUtil(0,0,0);
o3_status=OLp1Status;OLp1Status="";o3_autostatus=OLp1Autostatus;OLp1Autostatus=0;
if(o3_autostatus==2&&o3_cap!="")o3_status=o3_cap;
else if(o3_autostatus==1&&o3_text!="")o3_status=o3_text;
if(OLp1Hover){OLoptMOUSEOFF(1);OLp1Hover=0;OLhover=1}
if(o3_status!=""){self.status=o3_status;return true;}else return false;
}

/////////
// SUPPORT FUNCTIONS
/////////
function OLdispP2(){
o3_delay=0;
over2=OLgetRefById('overDiv2',o3_frame.document);
if(typeof over2=='undefined'||!over2){
if(OLns4&&document.classes){over2=o3_frame.document.layers['overDiv2']=new Layer(1024,o3_frame);
}else if(OLie4||OLns6){var body=OLie4?o3_frame.document.all.tags('body')[0]:
o3_frame.document.getElementsByTagName('body')[0];
over2=o3_frame.document.createElement('div');over2.id='overDiv2';
over2.style.position='absolute';over2.style.visibility='hidden';
body.appendChild(over2);}}
if(typeof over2=='undefined'||!over2){over2=null;return;}
if(typeof OLzIndex=='undefined')OLzIndex=OLgetDIVzIndex();
if(OLie4||OLns6)over2.style.zIndex=OLzIndex+2;
else if(OLns4)over2.zIndex=OLzIndex+2;
OLp1=over;over=over2;o3_sticky=0;OLdoLyr();o3_sticky=1;
if(o3_timeout>0){if(o3_timerid>0)clearTimeout(o3_timerid);
o3_timerid=setTimeout("nd2()",o3_timeout);o3_timeout=0;}
if(o3_ref){OLrefXY=OLgetRefXY(o3_ref);if(OLrefXY[0]==null){o3_ref='';o3_midx=0;o3_midy=0;}}
if(OLiframePI){OLinitIframe();OLdispIfShim();}
OLplaceLayer();
o3_showid=setTimeout("OLshowObjectP2(over2)",1);
o3_allowmove=(o3_nofollow?0:1);
}

function OLgetDIVzIndex(id){
var obj;
if(id==''||id==null)id='overDiv';
obj=OLgetRefById(id,o3_frame.document);
obj=OLns4?obj:obj.style;
return obj.zIndex;
}

function OLshowObjectP2(obj){
o3_showid=0;
obj=(OLns4?obj:obj.style);
obj.visibility='visible';
if((OLiframePI)&&OLifShimOvertwo)OLifShimOvertwo.style.visibility="visible";
if(OLhidePI)OLhideUtil(1,1,0);
}

function OLhideObjectP2(obj){
if(o3_showid>0){clearTimeout(o3_showid);o3_showid=0;}
if(o3_timerid>0){clearTimeout(o3_timerid);o3_timerid=0;}o3_timeout=0;
if(o3_delayid>0){clearTimeout(o3_delayid);o3_delayid=0;}o3_delay=0;
if((OLiframePI)&&OLifShimOvertwo)OLifShimOvertwo.style.visibility="hidden";
if(obj){obj=(OLns4?obj:obj.style);obj.visibility='hidden';}
o3_allowmove=o3_nofollow=0;
}

OLovertwoPI=1;
OLloaded=1;
