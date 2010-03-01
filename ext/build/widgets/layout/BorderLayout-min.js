/*
 * Ext JS Library 2.3.0
 * Copyright(c) 2006-2009, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 */


Ext.layout.BorderLayout=Ext.extend(Ext.layout.ContainerLayout,{monitorResize:true,rendered:false,onLayout:function(ct,target){var collapsed;if(!this.rendered){target.addClass('x-border-layout-ct');var items=ct.items.items;collapsed=[];for(var i=0,len=items.length;i<len;i++){var c=items[i];var pos=c.region;if(c.collapsed){collapsed.push(c);}
c.collapsed=false;if(!c.rendered){c.cls=c.cls?c.cls+' x-border-panel':'x-border-panel';c.render(target,i);}
this[pos]=pos!='center'&&c.split?new Ext.layout.BorderLayout.SplitRegion(this,c.initialConfig,pos):new Ext.layout.BorderLayout.Region(this,c.initialConfig,pos);this[pos].render(target,c);}
this.rendered=true;}
var size=target.getViewSize();if(size.width<20||size.height<20){if(collapsed){this.restoreCollapsed=collapsed;}
return;}else if(this.restoreCollapsed){collapsed=this.restoreCollapsed;delete this.restoreCollapsed;}
var w=size.width,h=size.height;var centerW=w,centerH=h,centerY=0,centerX=0;var n=this.north,s=this.south,west=this.west,e=this.east,c=this.center;if(!c&&Ext.layout.BorderLayout.WARN!==false){throw'No center region defined in BorderLayout '+ct.id;}
if(n&&n.isVisible()){var b=n.getSize();var m=n.getMargins();b.width=w-(m.left+m.right);b.x=m.left;b.y=m.top;centerY=b.height+b.y+m.bottom;centerH-=centerY;n.applyLayout(b);}
if(s&&s.isVisible()){var b=s.getSize();var m=s.getMargins();b.width=w-(m.left+m.right);b.x=m.left;var totalHeight=(b.height+m.top+m.bottom);b.y=h-totalHeight+m.top;centerH-=totalHeight;s.applyLayout(b);}
if(west&&west.isVisible()){var b=west.getSize();var m=west.getMargins();b.height=centerH-(m.top+m.bottom);b.x=m.left;b.y=centerY+m.top;var totalWidth=(b.width+m.left+m.right);centerX+=totalWidth;centerW-=totalWidth;west.applyLayout(b);}
if(e&&e.isVisible()){var b=e.getSize();var m=e.getMargins();b.height=centerH-(m.top+m.bottom);var totalWidth=(b.width+m.left+m.right);b.x=w-totalWidth+m.left;b.y=centerY+m.top;centerW-=totalWidth;e.applyLayout(b);}
if(c){var m=c.getMargins();var centerBox={x:centerX+m.left,y:centerY+m.top,width:centerW-(m.left+m.right),height:centerH-(m.top+m.bottom)};c.applyLayout(centerBox);}
if(collapsed){for(var i=0,len=collapsed.length;i<len;i++){collapsed[i].collapse(false);}}
if(Ext.isIE&&Ext.isStrict){target.repaint();}},destroy:function(){var r=['north','south','east','west'];for(var i=0;i<r.length;i++){var region=this[r[i]];if(region){if(region.destroy){region.destroy();}else if(region.split){region.split.destroy(true);}}}
Ext.layout.BorderLayout.superclass.destroy.call(this);}});Ext.layout.BorderLayout.Region=function(layout,config,pos){Ext.apply(this,config);this.layout=layout;this.position=pos;this.state={};if(typeof this.margins=='string'){this.margins=this.layout.parseMargins(this.margins);}
this.margins=Ext.applyIf(this.margins||{},this.defaultMargins);if(this.collapsible){if(typeof this.cmargins=='string'){this.cmargins=this.layout.parseMargins(this.cmargins);}
if(this.collapseMode=='mini'&&!this.cmargins){this.cmargins={left:0,top:0,right:0,bottom:0};}else{this.cmargins=Ext.applyIf(this.cmargins||{},pos=='north'||pos=='south'?this.defaultNSCMargins:this.defaultEWCMargins);}}};Ext.layout.BorderLayout.Region.prototype={collapsible:false,split:false,floatable:true,minWidth:50,minHeight:50,defaultMargins:{left:0,top:0,right:0,bottom:0},defaultNSCMargins:{left:5,top:5,right:5,bottom:5},defaultEWCMargins:{left:5,top:0,right:5,bottom:0},isCollapsed:false,render:function(ct,p){this.panel=p;p.el.enableDisplayMode();this.targetEl=ct;this.el=p.el;var gs=p.getState,ps=this.position;p.getState=function(){return Ext.apply(gs.call(p)||{},this.state);}.createDelegate(this);if(ps!='center'){p.allowQueuedExpand=false;p.on({beforecollapse:this.beforeCollapse,collapse:this.onCollapse,beforeexpand:this.beforeExpand,expand:this.onExpand,hide:this.onHide,show:this.onShow,scope:this});if(this.collapsible||this.floatable){p.collapseEl='el';p.slideAnchor=this.getSlideAnchor();}
if(p.tools&&p.tools.toggle){p.tools.toggle.addClass('x-tool-collapse-'+ps);p.tools.toggle.addClassOnOver('x-tool-collapse-'+ps+'-over');}}},getCollapsedEl:function(){if(!this.collapsedEl){if(!this.toolTemplate){var tt=new Ext.Template('<div class="x-tool x-tool-{id}">&#160;</div>');tt.disableFormats=true;tt.compile();Ext.layout.BorderLayout.Region.prototype.toolTemplate=tt;}
this.collapsedEl=this.targetEl.createChild({cls:"x-layout-collapsed x-layout-collapsed-"+this.position,id:this.panel.id+'-xcollapsed'});this.collapsedEl.enableDisplayMode('block');if(this.collapseMode=='mini'){this.collapsedEl.addClass('x-layout-cmini-'+this.position);this.miniCollapsedEl=this.collapsedEl.createChild({cls:"x-layout-mini x-layout-mini-"+this.position,html:"&#160;"});this.miniCollapsedEl.addClassOnOver('x-layout-mini-over');this.collapsedEl.addClassOnOver("x-layout-collapsed-over");this.collapsedEl.on('click',this.onExpandClick,this,{stopEvent:true});}else{if(this.collapsible!==false&&!this.hideCollapseTool){var t=this.toolTemplate.append(this.collapsedEl.dom,{id:'expand-'+this.position},true);t.addClassOnOver('x-tool-expand-'+this.position+'-over');t.on('click',this.onExpandClick,this,{stopEvent:true});}
if(this.floatable!==false||this.titleCollapse){this.collapsedEl.addClassOnOver("x-layout-collapsed-over");this.collapsedEl.on("click",this[this.floatable?'collapseClick':'onExpandClick'],this);}}}
return this.collapsedEl;},onExpandClick:function(e){if(this.isSlid){this.afterSlideIn();this.panel.expand(false);}else{this.panel.expand();}},onCollapseClick:function(e){this.panel.collapse();},beforeCollapse:function(p,animate){this.lastAnim=animate;if(this.splitEl){this.splitEl.hide();}
this.getCollapsedEl().show();this.panel.el.setStyle('z-index',100);this.isCollapsed=true;this.layout.layout();},onCollapse:function(animate){this.panel.el.setStyle('z-index',1);if(this.lastAnim===false||this.panel.animCollapse===false){this.getCollapsedEl().dom.style.visibility='visible';}else{this.getCollapsedEl().slideIn(this.panel.slideAnchor,{duration:.2});}
this.state.collapsed=true;this.panel.saveState();},beforeExpand:function(animate){var c=this.getCollapsedEl();this.el.show();if(this.position=='east'||this.position=='west'){this.panel.setSize(undefined,c.getHeight());}else{this.panel.setSize(c.getWidth(),undefined);}
c.hide();c.dom.style.visibility='hidden';this.panel.el.setStyle('z-index',100);},onExpand:function(){this.isCollapsed=false;if(this.splitEl){this.splitEl.show();}
this.layout.layout();this.panel.el.setStyle('z-index',1);this.state.collapsed=false;this.panel.saveState();},collapseClick:function(e){if(this.isSlid){e.stopPropagation();this.slideIn();}else{e.stopPropagation();this.slideOut();}},onHide:function(){if(this.isCollapsed){this.getCollapsedEl().hide();}else if(this.splitEl){this.splitEl.hide();}},onShow:function(){if(this.isCollapsed){this.getCollapsedEl().show();}else if(this.splitEl){this.splitEl.show();}},isVisible:function(){return!this.panel.hidden;},getMargins:function(){return this.isCollapsed&&this.cmargins?this.cmargins:this.margins;},getSize:function(){return this.isCollapsed?this.getCollapsedEl().getSize():this.panel.getSize();},setPanel:function(panel){this.panel=panel;},getMinWidth:function(){return this.minWidth;},getMinHeight:function(){return this.minHeight;},applyLayoutCollapsed:function(box){var ce=this.getCollapsedEl();ce.setLeftTop(box.x,box.y);ce.setSize(box.width,box.height);},applyLayout:function(box){if(this.isCollapsed){this.applyLayoutCollapsed(box);}else{this.panel.setPosition(box.x,box.y);this.panel.setSize(box.width,box.height);}},beforeSlide:function(){this.panel.beforeEffect();},afterSlide:function(){this.panel.afterEffect();},initAutoHide:function(){if(this.autoHide!==false){if(!this.autoHideHd){var st=new Ext.util.DelayedTask(this.slideIn,this);this.autoHideHd={"mouseout":function(e){if(!e.within(this.el,true)){st.delay(500);}},"mouseover":function(e){st.cancel();},scope:this};}
this.el.on(this.autoHideHd);}},clearAutoHide:function(){if(this.autoHide!==false){this.el.un("mouseout",this.autoHideHd.mouseout);this.el.un("mouseover",this.autoHideHd.mouseover);}},clearMonitor:function(){Ext.getDoc().un("click",this.slideInIf,this);},slideOut:function(){if(this.isSlid||this.el.hasActiveFx()){return;}
this.isSlid=true;var ts=this.panel.tools;if(ts&&ts.toggle){ts.toggle.hide();}
this.el.show();if(this.position=='east'||this.position=='west'){this.panel.setSize(undefined,this.collapsedEl.getHeight());}else{this.panel.setSize(this.collapsedEl.getWidth(),undefined);}
this.restoreLT=[this.el.dom.style.left,this.el.dom.style.top];this.el.alignTo(this.collapsedEl,this.getCollapseAnchor());this.el.setStyle("z-index",102);this.panel.el.replaceClass('x-panel-collapsed','x-panel-floating');if(this.animFloat!==false){this.beforeSlide();this.el.slideIn(this.getSlideAnchor(),{callback:function(){this.afterSlide();this.initAutoHide();Ext.getDoc().on("click",this.slideInIf,this);},scope:this,block:true});}else{this.initAutoHide();Ext.getDoc().on("click",this.slideInIf,this);}},afterSlideIn:function(){this.clearAutoHide();this.isSlid=false;this.clearMonitor();this.el.setStyle("z-index","");this.panel.el.replaceClass('x-panel-floating','x-panel-collapsed');this.el.dom.style.left=this.restoreLT[0];this.el.dom.style.top=this.restoreLT[1];var ts=this.panel.tools;if(ts&&ts.toggle){ts.toggle.show();}},slideIn:function(cb){if(!this.isSlid||this.el.hasActiveFx()){Ext.callback(cb);return;}
this.isSlid=false;if(this.animFloat!==false){this.beforeSlide();this.el.slideOut(this.getSlideAnchor(),{callback:function(){this.el.hide();this.afterSlide();this.afterSlideIn();Ext.callback(cb);},scope:this,block:true});}else{this.el.hide();this.afterSlideIn();}},slideInIf:function(e){if(!e.within(this.el)){this.slideIn();}},anchors:{"west":"left","east":"right","north":"top","south":"bottom"},sanchors:{"west":"l","east":"r","north":"t","south":"b"},canchors:{"west":"tl-tr","east":"tr-tl","north":"tl-bl","south":"bl-tl"},getAnchor:function(){return this.anchors[this.position];},getCollapseAnchor:function(){return this.canchors[this.position];},getSlideAnchor:function(){return this.sanchors[this.position];},getAlignAdj:function(){var cm=this.cmargins;switch(this.position){case"west":return[0,0];break;case"east":return[0,0];break;case"north":return[0,0];break;case"south":return[0,0];break;}},getExpandAdj:function(){var c=this.collapsedEl,cm=this.cmargins;switch(this.position){case"west":return[-(cm.right+c.getWidth()+cm.left),0];break;case"east":return[cm.right+c.getWidth()+cm.left,0];break;case"north":return[0,-(cm.top+cm.bottom+c.getHeight())];break;case"south":return[0,cm.top+cm.bottom+c.getHeight()];break;}}};Ext.layout.BorderLayout.SplitRegion=function(layout,config,pos){Ext.layout.BorderLayout.SplitRegion.superclass.constructor.call(this,layout,config,pos);this.applyLayout=this.applyFns[pos];};Ext.extend(Ext.layout.BorderLayout.SplitRegion,Ext.layout.BorderLayout.Region,{splitTip:"Drag to resize.",collapsibleSplitTip:"Drag to resize. Double click to hide.",useSplitTips:false,splitSettings:{north:{orientation:Ext.SplitBar.VERTICAL,placement:Ext.SplitBar.TOP,maxFn:'getVMaxSize',minProp:'minHeight',maxProp:'maxHeight'},south:{orientation:Ext.SplitBar.VERTICAL,placement:Ext.SplitBar.BOTTOM,maxFn:'getVMaxSize',minProp:'minHeight',maxProp:'maxHeight'},east:{orientation:Ext.SplitBar.HORIZONTAL,placement:Ext.SplitBar.RIGHT,maxFn:'getHMaxSize',minProp:'minWidth',maxProp:'maxWidth'},west:{orientation:Ext.SplitBar.HORIZONTAL,placement:Ext.SplitBar.LEFT,maxFn:'getHMaxSize',minProp:'minWidth',maxProp:'maxWidth'}},applyFns:{west:function(box){if(this.isCollapsed){return this.applyLayoutCollapsed(box);}
var sd=this.splitEl.dom,s=sd.style;this.panel.setPosition(box.x,box.y);var sw=sd.offsetWidth;s.left=(box.x+box.width-sw)+'px';s.top=(box.y)+'px';s.height=Math.max(0,box.height)+'px';this.panel.setSize(box.width-sw,box.height);},east:function(box){if(this.isCollapsed){return this.applyLayoutCollapsed(box);}
var sd=this.splitEl.dom,s=sd.style;var sw=sd.offsetWidth;this.panel.setPosition(box.x+sw,box.y);s.left=(box.x)+'px';s.top=(box.y)+'px';s.height=Math.max(0,box.height)+'px';this.panel.setSize(box.width-sw,box.height);},north:function(box){if(this.isCollapsed){return this.applyLayoutCollapsed(box);}
var sd=this.splitEl.dom,s=sd.style;var sh=sd.offsetHeight;this.panel.setPosition(box.x,box.y);s.left=(box.x)+'px';s.top=(box.y+box.height-sh)+'px';s.width=Math.max(0,box.width)+'px';this.panel.setSize(box.width,box.height-sh);},south:function(box){if(this.isCollapsed){return this.applyLayoutCollapsed(box);}
var sd=this.splitEl.dom,s=sd.style;var sh=sd.offsetHeight;this.panel.setPosition(box.x,box.y+sh);s.left=(box.x)+'px';s.top=(box.y)+'px';s.width=Math.max(0,box.width)+'px';this.panel.setSize(box.width,box.height-sh);}},render:function(ct,p){Ext.layout.BorderLayout.SplitRegion.superclass.render.call(this,ct,p);var ps=this.position;this.splitEl=ct.createChild({cls:"x-layout-split x-layout-split-"+ps,html:"&#160;",id:this.panel.id+'-xsplit'});if(this.collapseMode=='mini'){this.miniSplitEl=this.splitEl.createChild({cls:"x-layout-mini x-layout-mini-"+ps,html:"&#160;"});this.miniSplitEl.addClassOnOver('x-layout-mini-over');this.miniSplitEl.on('click',this.onCollapseClick,this,{stopEvent:true});}
var s=this.splitSettings[ps];this.split=new Ext.SplitBar(this.splitEl.dom,p.el,s.orientation);this.split.placement=s.placement;this.split.getMaximumSize=this[s.maxFn].createDelegate(this);this.split.minSize=this.minSize||this[s.minProp];this.split.on("beforeapply",this.onSplitMove,this);this.split.useShim=this.useShim===true;this.maxSize=this.maxSize||this[s.maxProp];if(p.hidden){this.splitEl.hide();}
if(this.useSplitTips){this.splitEl.dom.title=this.collapsible?this.collapsibleSplitTip:this.splitTip;}
if(this.collapsible){this.splitEl.on("dblclick",this.onCollapseClick,this);}},getSize:function(){if(this.isCollapsed){return this.collapsedEl.getSize();}
var s=this.panel.getSize();if(this.position=='north'||this.position=='south'){s.height+=this.splitEl.dom.offsetHeight;}else{s.width+=this.splitEl.dom.offsetWidth;}
return s;},getHMaxSize:function(){var cmax=this.maxSize||10000;var center=this.layout.center;return Math.min(cmax,(this.el.getWidth()+center.el.getWidth())-center.getMinWidth());},getVMaxSize:function(){var cmax=this.maxSize||10000;var center=this.layout.center;return Math.min(cmax,(this.el.getHeight()+center.el.getHeight())-center.getMinHeight());},onSplitMove:function(split,newSize){var s=this.panel.getSize();this.lastSplitSize=newSize;if(this.position=='north'||this.position=='south'){this.panel.setSize(s.width,newSize);this.state.height=newSize;}else{this.panel.setSize(newSize,s.height);this.state.width=newSize;}
this.layout.layout();this.panel.saveState();return false;},getSplitBar:function(){return this.split;},destroy:function(){Ext.destroy(this.miniSplitEl,this.split,this.splitEl);}});Ext.Container.LAYOUTS['border']=Ext.layout.BorderLayout;