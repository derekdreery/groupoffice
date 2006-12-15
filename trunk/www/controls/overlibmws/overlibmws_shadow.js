/*
 overlibmws_shadow.js plug-in module - Copyright Foteos Macrides 2003-2004
   For support of the SHADOW feature.
   Initial: July 14, 2003 - Last Revised: September 10, 2004
 See the Change History and Command Reference for overlibmws via:

	http://www.macridesweb.com/oltest/

 Published under an open source license: http://www.macridesweb.com/oltest/license.html
*/

// PRE-INIT
OLloaded=0;
OLregCmds('shadow,shadowx,shadowy,shadowcolor,shadowimage,shadowopacity');

/////////
// DEFAULT CONFIGURATION
if(typeof ol_shadow=='undefined')var ol_shadow=0;
if(typeof ol_shadowx=='undefined')var ol_shadowx=5;
if(typeof ol_shadowy=='undefined')var ol_shadowy=5;
if(typeof ol_shadowcolor=='undefined')var ol_shadowcolor="#666666";
if(typeof ol_shadowimage=='undefined')var ol_shadowimage="";
if(typeof ol_shadowopacity=='undefined')var ol_shadowopacity=60;
// END CONFIGURATION
////////

// INIT
var o3_shadow=0,o3_shadowx=5,o3_shadowy=5,o3_shadowcolor="#666666",o3_shadowimage="";
var o3_shadowopacity=60,bkdrop=null;

// Loads runtime variable defaults.
function OLloadShadow(){
o3_shadow=ol_shadow;o3_shadowx=ol_shadowx;o3_shadowy=ol_shadowy;o3_shadowcolor=ol_shadowcolor;
o3_shadowimage=ol_shadowimage;o3_shadowopacity=ol_shadowopacity;
}

// For commandline parser.
function OLparseShadow(pf,i,ar){
var k=i;
if(k<ar.length){
if(ar[k]==SHADOW){eval(pf+'shadow=('+pf+'shadow==0)?1:0');return k;}
if(ar[k]==-SHADOW){eval(pf+'shadow=0');return k;}
if(ar[k]==SHADOWX){eval(pf+'shadowx='+ar[++k]);return k;}
if(ar[k]==SHADOWY){eval(pf+'shadowy='+ar[++k]);return k;}
if(ar[k]==SHADOWCOLOR){eval(pf+"shadowcolor='"+OLescSglQt(ar[++k])+"'");return k;}
if(ar[k]==SHADOWIMAGE){eval(pf+"shadowimage='"+OLescSglQt(ar[++k])+"'");return k;}
if(ar[k]==SHADOWOPACITY){eval(pf+'shadowopacity='+ar[++k]);return k;}}
return -1;
}

////////
// DROPSHADOW SUPPORT FUNCTIONS
////////
function OLdispShadow(){
if(o3_shadow){OLgetShadowLyrRef();if(bkdrop)OLgenerateShadowLyr();}
}

function OLinitShadow(){
if(OLie55&&OLfilterPI&&o3_filtershadow){o3_shadow=0;return}
var theObj=OLgetRefById('backdrop',o3_frame.document);
if(typeof theObj=='undefined'||!theObj){
if(OLns4&&document.classes){theObj=o3_frame.document.layers['backdrop']=new Layer(1024,o3_frame);
}else if(OLie4||OLns6){var body=(OLie4?o3_frame.document.all.tags('body')[0]:
o3_frame.document.getElementsByTagName('body')[0]);
theObj=o3_frame.document.createElement('div');theObj.id='backdrop';
theObj.style.position='absolute';theObj.style.visibility='hidden';
body.appendChild(theObj);}}
if(typeof theObj=='undefined'||!theObj||bkdrop!=theObj){
bkdrop=null;OLgetShadowLyrRef();}
}

function OLgetShadowLyrRef(){
if(bkdrop||!o3_shadow)return;
bkdrop=OLgetRefById('backdrop',o3_frame.document);
if(typeof bkdrop=='undefined'||!bkdrop){o3_shadow=0;bkdrop=null;}
}

function OLgenerateShadowLyr(){
var wd=(OLns4?over.clip.width:over.offsetWidth),hgt=(OLns4?over.clip.height:over.offsetHeight);
if(OLns4){bkdrop.clip.width=wd;bkdrop.clip.height=hgt;
if(o3_shadowimage)bkdrop.background.src=o3_shadowimage;
else{bkdrop.bgColor=o3_shadowcolor;bkdrop.zIndex=over.zIndex -1;}
}else{var obj=bkdrop.style;obj.width=wd+'px';obj.height=hgt+'px';
if(o3_shadowimage)obj.backgroundImage="url("+o3_shadowimage+")";
else obj.backgroundColor=o3_shadowcolor;
obj.clip='rect(0px '+wd+'px '+hgt+'px 0px)';obj.zIndex=over.style.zIndex -1;
if(o3_shadowopacity){var op=o3_shadowopacity;op=(op<=100&&op>0?op:100);
if(OLie4&&!OLieM&&typeof obj.filter=='string'){
obj.filter='Alpha(opacity='+op+')';if(OLie55)bkdrop.filters.alpha.enabled=1;}
else{op=op/100;OLopBk(op);}}}
}

function OLopBk(op){
var obj=bkdrop.style;
if(typeof obj.opacity!='undefined')obj.opacity=op;
else if(typeof obj.MozOpacity!='undefined')obj.MozOpacity=op;
else if(typeof obj.KhtmlOpacity!='undefined')obj.KhtmlOpacity=op;
}

function OLcleanUpShadow(){
if(!bkdrop)return;
if(OLns4){
bkdrop.bgColor=null;bkdrop.background.src=null;
}else{
var obj=bkdrop.style;
obj.backgroundColor='transparent';obj.backgroundImage='none';
if(OLie4&&!OLieM&&typeof obj.filter=='string'){
obj.filter='Alpha(opacity=100)';if(OLie55&&typeof bkdrop.filters=='object')
bkdrop.filters.alpha.enabled=0;}else OLopBk(1.0);
if(OLns6){obj.width=1+'px';obj.height=1+'px';
OLrepositionTo(bkdrop,o3_frame.pageXOffset,o3_frame.pageYOffset);}}
}

function OLshowShadow(){
if(bkdrop&&o3_shadow){
var theObj=(OLns4?bkdrop:bkdrop.style);
theObj.visibility="visible";}
}

function OLhideShadow(){
if(bkdrop&&o3_shadow){
var obj=OLgetRefById('backdrop',o3_frame.document);
if(obj&&obj==bkdrop){var bks=(OLns4?bkdrop:bkdrop.style);
if(typeof bks.visibility=='string')bks.visibility="hidden";OLcleanUpShadow();}}
}

function OLrepositionShadow(X,Y){
if(bkdrop&&o3_shadow)OLrepositionTo(bkdrop,X+o3_shadowx,Y+o3_shadowy);
}

////////
// PLUGIN REGISTRATIONS
////////
OLregRunTimeFunc(OLloadShadow);
OLregCmdLineFunc(OLparseShadow);

OLshadowPI=1;
OLloaded=1;
