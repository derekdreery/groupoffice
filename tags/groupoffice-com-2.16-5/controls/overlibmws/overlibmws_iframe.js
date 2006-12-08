/*
 overlibmws_iframe.js plug-in module - Copyright Foteos Macrides 2003-2004
   Masks system controls to prevent obscuring of popops for IE v5.5 or higher.
   Initial: October 19, 2003 - Last Revised: September 13, 2004
 See the Change History and Command Reference for overlibmws via:

	http://www.macridesweb.com/oltest/

 Published under an open source license: http://www.macridesweb.com/oltest/license.html
*/

// PRE-INIT
OLloaded=0;

// INIT
var OLifShim=null,OLifShimShadow=null,OLifShimOvertwo=null;

////////
// IFRAME SHIM SUPPORT FUNCTIONS
////////
function OLinitIframe(){
if(!OLie55)return;
var theObj;if((OLovertwoPI)&&over2&&over==over2){
theObj=o3_frame.document.all['overIframeOvertwo'];
if(theObj=='undefined'||!theObj||OLifShimOvertwo!=theObj){
OLifShimOvertwo=null;OLgetIfShimOvertwoRef();}return;}
theObj=o3_frame.document.all['overIframe'];
if(theObj=='undefined'||!theObj||OLifShim!=theObj){
OLifShim=null;OLgetIfShimRef();}
if((OLshadowPI)&&o3_shadow){
theObj=o3_frame.document.all['overIframeShadow'];
if(theObj=='undefined'||!theObj||OLifShimShadow!=theObj){
OLifShimShadow=null;OLgetIfShimShadowRef();}}
}

function OLsetIfShimRef(obj,i,z){
obj.id=i;obj.src='javascript:false;';obj.scrolling='no';var ob=obj.style;
ob.position='absolute';ob.top=0;ob.left=0;ob.width=1;ob.height=1;ob.visibility='hidden';
ob.zIndex=over.style.zIndex-z;ob.filter='Alpha(style=0,opacity=0)';
}

function OLgetIfShimRef(){
if(OLifShim||!OLie55)return;
var body=o3_frame.document.all.tags('body')[0];
OLifShim=o3_frame.document.createElement('iframe');
if(typeof OLifShim.style.filter!='string'){if(typeof OLifShim!='undefined')
OLifShim.style.display='none';OLifShim=null;OLie55=false;return;}
OLsetIfShimRef(OLifShim,'overIframe',2);
body.insertBefore(OLifShim,body.firstChild);
}

function OLgetIfShimShadowRef(){
if(OLifShimShadow||!OLie55)return;
var body=o3_frame.document.all.tags('body')[0];
OLifShimShadow=o3_frame.document.createElement('iframe');
if(typeof OLifShimShadow.style.filter!='string'){if(typeof OLifShimShadow!='undefined')
OLifShimShadow.style.display='none';OLifShimShadow=null;OLie55=false;return;}
OLsetIfShimRef(OLifShimShadow,'overIframeShadow',3);
body.insertBefore(OLifShimShadow,body.firstChild);
}

function OLgetIfShimOvertwoRef(){
if(OLifShimOvertwo||!OLie55)return;
var body=o3_frame.document.all.tags('body')[0];
OLifShimOvertwo=o3_frame.document.createElement('iframe');
if(typeof OLifShimOvertwo.style.filter!='string'){if(typeof OLifShimOvertwo!='undefined')
OLifShimOvertwo.style.display='none';OLifShimOvertwo=null;OLie55=false;return;}
OLsetIfShimRef(OLifShimOvertwo,'overIframeOvertwo',1);
body.insertBefore(OLifShimOvertwo,body.firstChild);
}

function OLsetDispIfShim(obj,w,h){
obj.style.width=w+'px';obj.style.height=h+'px';obj.style.clip='rect(0px '+w+'px '+h+'px 0px)';
obj.filters.alpha.enabled=true;
}

function OLdispIfShim(){
if(!OLie55)return;
var wd=over.offsetWidth,ht=over.offsetHeight;
if(OLfilterPI&&o3_filtershadow){wd+=5;ht+=5;}
if((OLovertwoPI)&&over2&&over==over2){
if(!OLifShimOvertwo)return;
OLsetDispIfShim(OLifShimOvertwo,wd,ht);return;}
if(!OLifShim)return;
OLsetDispIfShim(OLifShim,wd,ht);
if((!OLshadowPI)||!o3_shadow||!OLifShimShadow)return;
OLsetDispIfShim(OLifShimShadow,wd,ht);
}

function OLshowIfShim(){
if(OLifShim){OLifShim.style.visibility="visible";
if((OLshadowPI)&&o3_shadow&&OLifShimShadow)OLifShimShadow.style.visibility="visible";}
}

function OLhideIfShim(obj){
if(!OLie55||obj!=over)return;
if(OLifShim)OLifShim.style.visibility="hidden";
if((OLshadowPI)&&o3_shadow&&OLifShimShadow)OLifShimShadow.style.visibility="hidden";
}

function OLrepositionIfShim(X,Y){
if(OLie55){if((OLovertwoPI)&&over2&&over==over2){
if(OLifShimOvertwo)OLrepositionTo(OLifShimOvertwo,X,Y);}
else{if(OLifShim){OLrepositionTo(OLifShim,X,Y);if((OLshadowPI)&&o3_shadow&&OLifShimShadow)
OLrepositionTo(OLifShimShadow,X+o3_shadowx,Y+o3_shadowy);}}}
}

OLiframePI=1;
OLloaded=1;
