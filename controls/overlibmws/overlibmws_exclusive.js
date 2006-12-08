/*
 overlibmws_exclusive.js plug-in module - Copyright Foteos Macrides 2003-2004
   For support of the EXCLUSIVE feature.
   Initial: November 7, 2003 - Last Revised: September 12, 2004
 See the Change History and Command Reference for overlibmws via:

	http://www.macridesweb.com/oltest/

 Published under an open source license: http://www.macridesweb.com/oltest/license.html
*/

// PRE-INIT
OLloaded=0;
OLregCmds('exclusive,exclusivestatus,exclusiveoverride');

/////////
// DEFAULT CONFIGURATION
if(typeof ol_exclusive=='undefined')var ol_exclusive=0;
if(typeof ol_exclusivestatus=='undefined')var ol_exclusivestatus=
'Please act on or close the open popup.';
if(typeof ol_exclusiveoverride=='undefined')var ol_exclusiveoverride=0;
// END CONFIGURATION
/////////

// INIT
var o3_exclusive=0,o3_exclusivestatus='',o3_exclusiveoverride=0;

// Loads runtime variable defaults.
function OLloadExclusive(){
o3_exclusive=ol_exclusive;o3_exclusivestatus=ol_exclusivestatus;
o3_exclusiveoverride=ol_exclusiveoverride;
}

// For commandline parser.
function OLparseExclusive(pf,i,ar){
var k=i;
if(k<ar.length){
if(ar[k]==EXCLUSIVE){eval(pf+'exclusive=('+pf+'exclusive==0)?1:0');return k;}
if(ar[k]==-EXCLUSIVE){eval(pf+'exclusive=0');return k;}
if(ar[k]==EXCLUSIVESTATUS){eval(pf+"exclusivestatus='"+OLescSglQt(ar[++k])+"'");return k;}
if(ar[k]==EXCLUSIVEOVERRIDE){eval(pf+'exclusiveoverride=('+pf+'exclusiveoverride==0)?1:0');
return k;}
if(ar[k]==-EXCLUSIVEOVERRIDE){eval(pf+'exclusiveoverride=0');return k;}}
return -1;
}

/////////
// EXCLUSIVE SUPPORT FUNCTIONS
/////////
// indicate whether popup is exclusive and set status message if so. 
function OLisExclusive(args){
if((args!=null)&&OLhasOverRide(args))o3_exclusiveoverride=(ol_exclusiveoverride==0)?1:0;
else o3_exclusiveoverride=ol_exclusiveoverride;
var rtnVal=(o3_exclusive&&!o3_exclusiveoverride&&o3_showingsticky&&
over==OLgetRefById('overDiv',o3_frame.document));
if(rtnVal)self.status=o3_exclusivestatus;
return rtnVal;
}

// check the overlib arguments for the EXCLUSIVEOVERRIDE command
function OLhasOverRide(args){
var rtnFlag=0;
for(var i=0;i<args.length;i++){
if(typeof args[i]=='number'&&args[i]==EXCLUSIVEOVERRIDE){
rtnFlag=1;break;}}
return rtnFlag;
}

////////
// PLUGIN REGISTRATIONS
////////
OLregRunTimeFunc(OLloadExclusive);
OLregCmdLineFunc(OLparseExclusive);

OLexclusivePI=1;
OLloaded=1;
