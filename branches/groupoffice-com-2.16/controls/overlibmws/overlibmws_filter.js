/*
 overlibmws_filter.js plug-in module - Copyright Foteos Macrides and Essam Gamal 2003-2004.
  For support of the FILTER feature.
  Initial: November 27, 2003 - Last Revised: September 10, 2004
 See the Change History and Command Reference for overlibmws via:

	http://www.macridesweb.com/oltest/

 Published under an open source license: http://www.macridesweb.com/oltest/license.html
*/

// PRE-INIT
OLloaded=0;
OLregCmds('filter,fadein,fadeout,fadetime,filteropacity,filtershadow,filtershadowcolor');

/////////
// DEFAULT CONFIGURATION
if(typeof ol_filter=='undefined')var ol_filter=0;
if(typeof ol_fadein=='undefined')var ol_fadein=52;
if(typeof ol_fadeout=='undefined')var ol_fadeout=52;
if(typeof ol_fadetime=='undefined')var ol_fadetime=800;
if(typeof ol_filteropacity=='undefined')var ol_filteropacity=100;
if(typeof ol_filtershadow=='undefined')var ol_filtershadow=0;
if(typeof ol_filtershadowcolor=='undefined')var ol_filtershadowcolor="#cccccc";
// END CONFIGURATION
////////

// INIT
var o3_filter=0,o3_fadein=52,o3_fadeout=52,o3_fadetime=800,o3_filteropacity=100;
var o3_filtershadow=0,o3_filtershadowcolor="#cccccc",OLfiIdx;

// Loads runtime variable defaults.
function OLloadFilter(){
o3_filter=ol_filter;o3_fadein=ol_fadein;o3_fadeout=ol_fadeout;o3_fadetime=ol_fadetime;
o3_filteropacity=ol_filteropacity;
o3_filtershadow=ol_filtershadow;o3_filtershadowcolor=ol_filtershadowcolor;
}

// For commandline parser.
function OLparseFilter(pf,i,ar){
var k=i;
if(k<ar.length){
if(ar[k]==FILTER){eval(pf+'filter=('+pf+'filter==0)?1:0');return k;}
if(ar[k]==-FILTER){eval(pf+'filter=0');return k;}
if(ar[k]==FADEIN){eval(pf+'fadein='+ar[++k]);return k;}
if(ar[k]==FADEOUT){eval(pf+'fadeout='+ar[++k]);return k;}
if(ar[k]==FADETIME){eval(pf+'fadetime='+ar[++k]);return k;}
if(ar[k]==FILTEROPACITY){eval(pf+'filteropacity='+ar[++k]);return k;}
if(ar[k]==FILTERSHADOW){eval(pf+'filtershadow='+ar[++k]);return k;}
if(ar[k]==FILTERSHADOWCOLOR){eval(pf+"filtershadowcolor='"+OLescSglQt(ar[++k])+"'");return k;}}
return -1;
}

////////
// FILTER SUPPORT FUNCTIONS
////////
// Set up MS filter array for IE5.5+ browsers
function OLinitFilterLyr(){
o3_fadein-=1;o3_fadeout-=1;OLfiIdx= -1;
if((o3_fadein<0||o3_fadein>51)&&(o3_fadeout<0||o3_fadeout>51)){o3_filter=0;return;}
if(OLie55&&over.style.filter){
var p,s,ob=over.filters[28];for(p=28;p<31;p++){over.filters[p].enabled=0;}
for(s=0;s<28;s++){if(over.filters[s].status)over.filters[s].stop();over.filters[s].enabled=0;}
ob.enabled=0;ob.opacity=ol_filteropacity;return;}
if(!OLie55||!o3_filter||(OLshadowPI&&o3_shadow))return;
var d=" progid:DXImageTransform.Microsoft.";
over.style.filter="revealTrans()"
+d+"Fade(Overlap=1.00 enabled=0)"+d+"Inset(enabled=0)"
+d+"Iris(irisstyle=PLUS,motion=in enabled=0)"+d+"Iris(irisstyle=PLUS,motion=out enabled=0)"
+d+"Iris(irisstyle=DIAMOND,motion=in enabled=0)"+d+"Iris(irisstyle=DIAMOND,motion=out enabled=0)"
+d+"Iris(irisstyle=CROSS,motion=in enabled=0)"+d+"Iris(irisstyle=CROSS,motion=out enabled=0)"
+d+"Iris(irisstyle=STAR,motion=in enabled=0)"+d+"Iris(irisstyle=STAR,motion=out enabled=0)"
+d+"RadialWipe(wipestyle=CLOCK enabled=0)"+d+"RadialWipe(wipestyle=WEDGE enabled=0)"
+d+"RadialWipe(wipestyle=RADIAL enabled=0)"+d+"Pixelate(MaxSquare=35,enabled=0)"
+d+"Slide(slidestyle=HIDE,Bands=25 enabled=0)"+d+"Slide(slidestyle=PUSH,Bands=25 enabled=0)"
+d+"Slide(slidestyle=SWAP,Bands=25 enabled=0)"+d+"Spiral(GridSizeX=16,GridSizeY=16 enabled=0)"
+d+"Stretch(stretchstyle=HIDE enabled=0)"+d+"Stretch(stretchstyle=PUSH enabled=0)"
+d+"Stretch(stretchstyle=SPIN enabled=0)"+d+"Wheel(spokes=16 enabled=0)"
+d+"GradientWipe(GradientSize=1.00,wipestyle=0,motion=forward enabled=0)"
+d+"GradientWipe(GradientSize=1.00,wipestyle=0,motion=reverse enabled=0)"
+d+"GradientWipe(GradientSize=1.00,wipestyle=1,motion=forward enabled=0)"
+d+"GradientWipe(GradientSize=1.00,wipestyle=1,motion=reverse enabled=0)"
+d+"Zigzag(GridSizeX=8,GridSizeY=8 enabled=0)"+d+"Alpha(enabled=0)"
+d+"Dropshadow(OffX=5,OffY=5,Positive=true,enabled=0)"
+d+"Shadow(strength=5,direction=135,enabled=0)";
}

// Enable any wanted filters.
function OLchkFilter(obj){
if(!o3_filter||obj!=over.style||(OLshadowPI&&o3_shadow))return false;
if(!OLie55){var op=o3_filteropacity;if(op>0&&op<100)OLopOv(op);return false;}
var fi=o3_fadein,fo=o3_fadeout,fp=1,ft=o3_fadetime/1000
if(fi<0||fi>51){fi=fo;fp=0;}
if(fi==51)fi=parseInt(Math.random()*50);
var at=fi>-1&&fi<24&&ft>0,af=fi>23&&fi<51&&ft>0;
OLfiIdx=(af?fi-23:0);
var p,s,e,ob,t=over.filters[OLfiIdx];
for(p=28;p<31;p++){over.filters[p].enabled=0;}
for(s=0;s<28;s++){if(over.filters[s].status)over.filters[s].stop();over.filters[s].enabled=0;}
for(e=1;e<3;e++){if(o3_filtershadowcolor&&o3_filtershadow==e){
ob=over.filters[28+e];ob.enabled=1;ob.color=o3_filtershadowcolor;}}
if(o3_filteropacity>0&&o3_filteropacity<100){ob=over.filters[28];
ob.enabled=1;ob.opacity=o3_filteropacity;}
if(fp&&(at||af)){if(at)over.filters[0].transition=fi;
t.duration=ft;t.apply();obj.visibility='visible';t.play();return true;}
return false;
}

// For non-IE55 browsers
function OLopOv(op){
var obj=over.style;
if(OLie4&&typeof obj.filter=='string')obj.filter='Alpha(opacity='+op+')';
else if(typeof obj.opacity!='undefined')obj.opacity=op/100;
else if(typeof obj.MozOpacity!='undefined')obj.MozOpacity=op/100;
else if(typeof obj.KhtmlOpacity!='undefined')obj.KhtmlOpacity=op/100;
}

// Disable any current filters.
function OLcleanupFilter(obj){
if(!o3_filter||!over||obj!=over||(OLshadowPI&&o3_shadow))return;
if(!OLie55){var op=o3_filteropacity;if(op>0&&op<100)OLopOv(ol_filteropacity);return;}
if(typeof over.filters!='object')return;
var ovs=over.style,fi=o3_fadein,fo=o3_fadeout;
if(fi>=0&&fi<=51&&fo==fi){if(OLfiIdx<0)return;var t=over.filters[OLfiIdx];
if(t.status)t.stop();ovs.visibility='visible';t.apply();
ovs.visibility='hidden';t.play();
}else{if(fo>=0&&fo<=51){fi=fo;if(fi==51)fi=parseInt(Math.random()*50);
var ft=o3_fadetime;var at=fi>-1&&fi<24&&ft>0; var af=fi>23&&fi<51&&ft>0;
OLfiIdx=(af?fi-23:0);t=over.filters[OLfiIdx];if(at||af){
if(at)over.filters[0].transition=fi;if(t.status)t.stop();
ovs.visibility='visible';t.apply();ovs.visibility='hidden';t.play();}}}
OLfiIdx=-1;
}

////////
// PLUGIN REGISTRATIONS
////////
OLregRunTimeFunc(OLloadFilter);
OLregCmdLineFunc(OLparseFilter);

OLfilterPI=1;
OLloaded=1;
