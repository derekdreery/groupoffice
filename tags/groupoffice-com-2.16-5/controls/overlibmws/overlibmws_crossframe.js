/*
 overlibmws_crossframe.js plug-in module - Copyright Foteos Macrides 2003-2004
   For support of the FRAME feature.
   Initial: August 3, 2003 - Last Revised: September 15, 2004
 See the Change History and Command Reference for overlibmws via:

	http://www.macridesweb.com/oltest/

 Published under an open source license: http://www.macridesweb.com/oltest/license.html
*/

// PRE-INIT
OLloaded=0;
OLregCmds('frame');

// INIT
// For commandline parser.
function OLparseCrossframe(pf,i,ar){
var k=i,v;
if(k<ar.length){
if(ar[k]==FRAME){v=ar[++k];if(pf=='ol_'&&OLcompatibleFrame(v))ol_frame=v;
else OLoptFRAME(v);return k;}}
return -1;
}

/////////
// FRAME SUPPORT FUNCTIONS
/////////
function OLgetFrameRef(thisFrame,ofrm){
var i,v,retVal='';
for(i=0;i<thisFrame.length;i++){if((((thisFrame[i].length>0)))&&(((OLns4))||
((OLie4)&&(v=thisFrame[i].document.all.tags('iframe'))!=null&&v.length==0)||
((OLns6)&&(v=thisFrame[i].document.getElementsByTagName('iframe'))!=null&&v.length==0))){
retVal=OLgetFrameRef(thisFrame[i],ofrm);if(retVal=='')continue;}
else if(thisFrame[i]!=ofrm)continue;
retVal='['+i+']'+retVal;break;}
return retVal;
}

// Makes sure target frame has overDiv
function OLcompatibleFrame(frameid){
return OLgetRefById('overDiv',frameid.document)?1:0
}

// Defines which frame we should point to.
function OLoptFRAME(frm){
o3_frame=OLcompatibleFrame(frm)?frm:ol_frame;
if(o3_frame!=ol_frame){
var l,tFrm=OLgetFrameRef(top.frames,o3_frame),sFrm=OLgetFrameRef(top.frames,ol_frame);
if(sFrm.length==tFrm.length){
l=tFrm.lastIndexOf('[');
if(l){
while(sFrm.substring(0,l)!=tFrm.substring(0,l))l=tFrm.lastIndexOf('[',l-1);
tFrm=tFrm.substr(l);
sFrm=sFrm.substr(l);}}
var i,k,cnt=0,p='',str=tFrm;
while((k=str.lastIndexOf('['))!= -1){cnt++;str=str.substring(0,k);}
for(i=0;i<cnt;i++)p=p+'parent.';
OLfnRef=p+'frames'+sFrm+'.';}
over=OLgetRefById('overDiv',o3_frame.document);
return 0;
}

////////
// PLUGIN REGISTRATIONS
////////
OLregCmdLineFunc(OLparseCrossframe);

OLcrossframePI=1;
OLloaded=1;
