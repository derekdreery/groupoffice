/*
 overlibmws_debug.js plug-in module - Copyright Foteos Macrides 2003-2004
   For support of the OLshowProperties() debugging function.
   Initial: July 26, 2003 - Last Revised: October 3, 2004
 See the Change History and Command Reference for overlibmws via:

	http://www.macridesweb.com/oltest/

 Published under an open source license: http://www.macridesweb.com/oltest/license.html
*/

// PRE-INIT
OLloaded=0;
var OLzIndex;
OLregCmds('allowdebug');

/////////
// DEFAULT CONFIGURATION
if(typeof ol_allowdebug=='undefined')var ol_allowdebug="";
// END CONFIGURATION
/////////

// INIT
var o3_allowdebug="";

// Loads runtime variable defaults.
function OLloadDebug(){
o3_allowdebug=ol_allowdebug;
}

// For commandline parser.
function OLparseDebug(pf,i,ar){
var k=i;
if(ar[k]==ALLOWDEBUG){
if(k<(ar.length-1)&&typeof ar[k+1]=='string')eval(pf+"allowdebug='"+OLescSglQt(ar[++k])+"'");
return k;}
return -1;
}

/////////
// DEBUG SUPPORT FUNCTIONS
/////////
function OLshowProperties(){
var args=OLshowProperties.arguments,sho,shoObj,vis,lvl=0,istrt=0,theDiv='showProps',txt='';
var fac='Verdana,Arial,Helvetica',siz=(OLns4?'1':'67%');
var fon='><font color="#000000" face="'+fac+'" size="'+siz;
var stl=' style="font-family:'+fac+';font-size:'+siz+';';
var sty=stl+'color:#000000;';
var clo=(OLns4?'</font>':'');
if(args.length==0)return;
if(args.length%2&&typeof args[0]=='string'){
istrt=1;
theDiv=args[0];}
if(OLns4){
sho=self.document.layers[theDiv];
if((typeof sho=='undefined'||!sho)&&document.classes)
sho=self.document.layers[theDiv]=new Layer(1024,self);
if(typeof sho=='undefined'||!sho)return;
shoObj=sho;
lvl=OLgetLayerLevel(theDiv);
}else{
lvl=OLgetLayerLevel(theDiv);
sho=OLie4?self.document.all[theDiv]:self.document.getElementById(theDiv);
if(sho==null){
var body=(OLie4?self.document.all.tags('body')[0]:
self.document.getElementsByTagName('body')[0]);
sho=self.document.createElement("div");
sho.id=theDiv;sho.style.position='absolute';
body.appendChild(sho);}
shoObj=sho.style;}
if(typeof sho.position=='undefined'){
sho.position=new OLpageLocDebug(10+lvl*20,10,1);
if(typeof OLzIndex=='undefined')OLzIndex=OLgetDivZindex();
shoObj.zIndex=OLzIndex+1+lvl;if(0||!OLns4){sho.style.backgroundColor='#ffffcc';
}}
txt='<table cellpadding="1" cellspacing="0" border="0" bgcolor="#000000"><tr><td>'
+'<table cellpadding="5" border="0" cellspacing="0" bgcolor="#ffffcc">'
+'<tr><td><strong><a href="javascript:OLmoveToBack(\''+theDiv+'\');" title="Move to back"'
+(OLns4?fon:stl)+'">'+theDiv+clo
+'</a></strong></td><td align="right"><strong><a href="javascript:OLcloseLayer(\''+theDiv
+'\');" title="Close Layer"'+(OLns4?fon:stl
+'background-color:#cccccc;border:1px #333369 outset;padding:0px;')+'">X'+clo
+'</a></strong></td></tr><tr><td'+(OLns4?fon:sty)+'">'+'<strong><em>Item</em></strong>'
+clo+'</td><td'+(OLns4?fon:sty)+'">'+'<strong><em>Value</em></strong>'+clo+'</td></tr>';
for(var i=istrt;i<args.length-1;i++)
txt+='<tr><td align="right"'+(OLns4?fon:sty)+'">'+'<strong>'+args[i]+':&nbsp;</strong>'
+clo+'</td><td'+(OLns4?fon:sty)+'">'+args[++i]+clo+'</td></tr>';
txt+='</table></td></tr></table>';
if(OLns4){sho.document.open();sho.document.write(txt);sho.document.close();
}else{if(OLie4&&OLieM)sho.innerHTML='';sho.innerHTML=txt;}
OLshowAllVisibleLayers();
}

function OLgetLayerLevel(lyr){
var i=0;
if(typeof document.popups=='undefined'){
document.popups=new Array(lyr);
}else{
var l=document.popups;
for(i=0;i<l.length;i++)if(lyr==l[i])break;
if(i==l.length)l[l.length++]=lyr;}
return i;
}

function OLgetDivZindex(id){
if(id==''||id==null)id='overDiv';
var obj=OLgetRefById(id,self.document);
if(obj){obj=OLns4?obj:obj.style;return obj.zIndex;}
else return 1000;
}

function OLsetDebugCanShow(debugID){
if(typeof debugID!='string')return;
var i,lyr,pLyr=debugID.replace(/[ ]/ig,'').split(',');
for(i=0;i<pLyr.length;i++){
lyr=OLgetRefById(pLyr[i],self.document);
if(lyr&&typeof lyr.position!='undefined')lyr.position.canShow=1;}
}

function OLpageLocDebug(x,y,canShow){
this.x=x;this.y=y;this.canShow=(canShow==null)?0:canShow;
}

function OLshowAllVisibleLayers(){
var i,lyr,lyrObj,l=document.popups;
for(i=0;i<l.length;i++){
lyr=OLgetRefById(l[i],self.document);
if(lyr){lyrObj=OLns4?lyr:lyr.style;
if(lyr.position.canShow){
OLpositionLayer(lyrObj,lyr.position.x,lyr.position.y);
lyrObj.visibility='visible';}}}
}

function OLpositionLayer(Obj,x,y){
Obj.left=x+(OLie4?OLfd(self).scrollLeft:self.pageXOffset)+(OLns4?0:'px');
Obj.top=y+(OLie4?OLfd(self).scrollTop:self.pageYOffset)+(OLns4?0:'px');
}

function OLcloseLayer(lyrID){
var lyr=OLgetRefById(lyrID,self.document);
if(lyr){lyr.position.canShow=0;
lyr=OLns4?lyr:lyr.style;
lyr.visibility='hidden';}
}

function OLmoveToBack(layer){
var l=document.popups,lyr,obj,i,x=10,dx=20,z=OLzIndex+1;
if(l.length==1)return;
lyr=OLgetRefById(layer,self.document);
if(lyr){lyr.position.x=x;
obj=OLns4?lyr:lyr.style;
obj.zIndex=z;
for(i=0;i<l.length;i++){
if(layer==l[i])continue;
lyr=OLgetRefById(l[i],self.document);
if(!lyr||lyr.position.canShow==0)continue;
obj=OLns4?lyr:lyr.style;
obj.zIndex+=1;
lyr.position.x+=dx;}
OLshowAllVisibleLayers();}
}

////////
// PLUGIN REGISTRATIONS
////////
OLregRunTimeFunc(OLloadDebug);
OLregCmdLineFunc(OLparseDebug);

OLdebugPI=1;
OLloaded=1;
