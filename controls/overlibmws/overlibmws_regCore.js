/*
 overlibmws_regCore.js plug-in module - Copyright Foteos Macrides 2003-2004
   Import this file to declare the core command constants in frame documents which do not
   import the core module, overlibmws.js, but point to the overlib() and nd() commands in
   another document of the frameset, e.g. parent.scene.overlib(...) and parent.scene.nd()
   in a frame document named "scene" which does import the core module.  You can then use
   this file's OLregisterPlugins() function via a script block to declare pre-existing
   plugin modules, or it's OLregisterCommands() function for new plugins whose
   commands are not yet defined as string variables in this file.  The order of
   entries in those functions should parallel the order of importing the plugins.
 Initial: August 3, 2003 - Last Revised: July 23, 2004
 See the Change History and Command Reference for overlibmws via:

	http://www.macridesweb.com/oltest/

 Published under an open source license: http://www.macridesweb.com/oltest/license.html
*/

/////////
// CONSTANTS
/////////
var OLloaded=0,OLpmCnt=1,OLpMtr=new Array();
OLregisterCommands(
 'inarray,caparray,caption,sticky,nofollow,background,noclose,mouseoff,offdelay,right,left,'
+'center,offsetx,offsety,fgcolor,bgcolor,cgcolor,textcolor,capcolor,closecolor,width,wrap,'
+'wrapmax,height,border,base,status,autostatus,autostatuscap,snapx,snapy,fixx,fixy,relx,rely,'
+'midx,midy,ref,refc,refp,refx,refy,fgbackground,bgbackground,cgbackground,padx,pady,fullhtml,'
+'below,above,vcenter,capicon,textfont,captionfont,closefont,textsize,captionsize,closesize,'
+'timeout,delay,hauto,vauto,nojustx,nojusty,closetext,closeclick,closetitle,fgclass,bgclass,'
+'cgclass,capbelow,textpadding,textfontclass,captionpadding,captionfontclass,closefontclass,'
+'donothing');
var CSSOFF=DONOTHING,CSSCLASS=DONOTHING;
var OLpluginBUBBLE='bubble,bubbletype,adjbubble';
var OLpluginCROSSFRAME='frame';
var OLpluginDEBUG='allowdebug';
var OLpluginDRAGGABLE='draggable';
var OLpluginEXCLUSIVE='exclusive,exclusivestatus,exclusiveoverride';
var OLpluginFILTER='filter,fadein,fadeout,fadetime,filteropacity,filtershadow,filtershadowcolor';
var OLpluginFUNCTION='function';
var OLpluginHIDE='hideselectboxes,hidebyid,hidebyidall,hidebyidns4';
var OLpluginSCROLL='scroll';
var OLpluginSHADOW='shadow,shadowx,shadowy,shadowcolor,shadowimage,shadowopacity';

/////////
// PUBLIC FUNCTIONS
/////////
function OLregisterCommands(cmdStr){
if(typeof cmdStr!='string')return;
var pM=cmdStr.split(',');
OLpMtr=OLpMtr.concat(pM);
for(var i=0;i<pM.length;i++)
eval(pM[i].toUpperCase()+'='+OLpmCnt++);
}

function OLregisterPlugins(){
var ar=arguments;
for(var i=0;i<ar.length;i++){
if(ar[i].toUpperCase()=='BUBBLE'){OLregisterCommands(OLpluginBUBBLE);continue;}
if(ar[i].toUpperCase()=='CROSSFRAME'){OLregisterCommands(OLpluginCROSSFRAME);continue;}
if(ar[i].toUpperCase()=='DEBUG'){OLregisterCommands(OLpluginDEBUG);continue;}
if(ar[i].toUpperCase()=='DRAGGABLE'){OLregisterCommands(OLpluginDRAGGABLE);continue;}
if(ar[i].toUpperCase()=='EXCLUSIVE'){OLregisterCommands(OLpluginEXCLUSIVE);continue;}
if(ar[i].toUpperCase()=='FILTER'){OLregisterCommands(OLpluginFILTER);continue;}
if(ar[i].toUpperCase()=='FUNCTION'){OLregisterCommands(OLpluginFUNCTION);continue;}
if(ar[i].toUpperCase()=='HIDE'){OLregisterCommands(OLpluginHIDE);continue;}
if(ar[i].toUpperCase()=='IFRAME')continue;
if(ar[i].toUpperCase()=='OVERTWO')continue;
if(ar[i].toUpperCase()=='SCROLL'){OLregisterCommands(OLpluginSCROLL);continue;}
if(ar[i].toUpperCase()=='SHADOW'){OLregisterCommands(OLpluginSHADOW);continue;}}
}

OLloaded=1;
