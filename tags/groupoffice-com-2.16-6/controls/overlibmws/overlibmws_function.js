/*
 overlibmws_function.js plug-in module - Copyright Foteos Macrides 2002-2004
   For support of the FUNCTION feature.
   Initial: August 18, 2002 - Last Revised: July 23, 2004
 See the Change History and Command Reference for overlibmws via:

	http://www.macridesweb.com/oltest/

 Published under an open source license: http://www.macridesweb.com/oltest/license.html
*/

// PRE-INIT
OLloaded=0;
OLregCmds('function');

/////////
// DEFAULT CONFIGURATION
if(typeof ol_function=='undefined')var ol_function=null;
// END CONFIGURATION
/////////

// INIT
var o3_function=null;

// Loads runtime variable defaults.
function OLloadFunction(){
o3_function=ol_function;
}

// For commandline parser.
function OLparseFunction(pf,i,ar){
var k=i,v=null;;
if(k<ar.length){
if(ar[k]==FUNCTION){if(pf=='ol_'){if(typeof ar[k+1]!='number'){v=ar[++k];
ol_function=(typeof v=='function'?v:null);}}
else{OLudf=0;v=null;if(typeof ar[k+1]!='number')v=ar[++k];OLoptFUNCTION(v);}return k;}}
return -1;
}

/////////
// FUNCTION SUPPORT FUNCTIONS
/////////
// Calls an external function
function OLoptFUNCTION(callme){
o3_text=(callme?(typeof callme=='string'?(/.+\(.*\)/.test(callme)?eval(callme):
callme):callme()):(o3_function?o3_function():'No Function'));
return 0;
}

////////
// PLUGIN REGISTRATIONS
////////
OLregRunTimeFunc(OLloadFunction);
OLregCmdLineFunc(OLparseFunction);

OLfunctionPI=1;
OLloaded=1;
