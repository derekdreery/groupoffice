GO.summary.PortalColumn=Ext.extend(Ext.Container,{layout:"anchor",autoEl:"div",defaultType:"portlet",cls:"x-portal-column"});Ext.reg("portalcolumn",GO.summary.PortalColumn);GO.summary.PortalColumn=Ext.extend(Ext.Container,{layout:"anchor",autoEl:"div",defaultType:"portlet",cls:"x-portal-column"});Ext.reg("portalcolumn",GO.summary.PortalColumn);GO.summary.Portlet=Ext.extend(Ext.Panel,{anchor:"100%",frame:true,collapsible:true,draggable:true,cls:"x-portlet"});Ext.reg("portlet",GO.summary.Portlet);GO.summary.Portal=Ext.extend(Ext.Panel,{layout:"column",autoScroll:true,cls:"x-portal",defaultType:"portalcolumn",initComponent:function(){GO.summary.Portal.superclass.initComponent.call(this);this.addEvents({validatedrop:true,beforedragover:true,dragover:true,beforedrop:true,drop:true})},initEvents:function(){GO.summary.Portal.superclass.initEvents.call(this);this.dd=new GO.summary.Portal.DropZone(this,this.dropConfig)}});Ext.reg("portal",GO.summary.Portal);GO.summary.Portal.DropZone=function(A,B){this.portal=A;Ext.dd.ScrollManager.register(A.body);GO.summary.Portal.DropZone.superclass.constructor.call(this,A.bwrap.dom,B);A.body.ddScrollConfig=this.ddScrollConfig};Ext.extend(GO.summary.Portal.DropZone,Ext.dd.DropTarget,{ddScrollConfig:{vthresh:50,hthresh:-1,animate:true,increment:200},createEvent:function(A,D,C,B,F,E){return{portal:this.portal,panel:C.panel,columnIndex:B,column:F,position:E,data:C,source:A,rawEvent:D,status:this.dropAllowed}},notifyOver:function(Q,O,R){var D=O.getXY(),A=this.portal,J=Q.proxy;if(!this.grid){this.grid=this.getGrid()}var B=A.body.dom.clientWidth;if(!this.lastCW){this.lastCW=B}else{if(this.lastCW!=B){this.lastCW=B;A.doLayout();this.grid=this.getGrid()}}var C=0,H=this.grid.columnX,I=false;for(var M=H.length;C<M;C++){if(D[0]<(H[C].x+H[C].w)){I=true;break}}if(!I){C--}var L,G=false,F=0,P=A.items.itemAt(C);if(!P.items){P.initItems()}var K=P.items.items;for(var M=K.length;F<M;F++){L=K[F];var N=L.el.getHeight();if(N!==0&&(L.el.getY()+(N/2))>D[1]){G=true;break}}var E=this.createEvent(Q,O,R,C,P,G&&L?F:P.items.getCount());if(A.fireEvent("validatedrop",E)!==false&&A.fireEvent("beforedragover",E)!==false){J.getProxy().setWidth("auto");if(L){J.moveProxy(L.el.dom.parentNode,G?L.el.dom:null)}else{J.moveProxy(P.el.dom,null)}this.lastPos={c:P,col:C,p:G&&L?F:false};this.scrollPos=A.body.getScroll();A.fireEvent("dragover",E);return E.status}else{return E.status}},notifyOut:function(){delete this.grid},notifyDrop:function(H,D,C){delete this.grid;if(!this.lastPos){return }var F=this.lastPos.c,B=this.lastPos.col,G=this.lastPos.p;var A=this.createEvent(H,D,C,B,F,G!==false?G:F.items.getCount());if(this.portal.fireEvent("validatedrop",A)!==false&&this.portal.fireEvent("beforedrop",A)!==false){H.proxy.getProxy().remove();H.panel.el.dom.parentNode.removeChild(H.panel.el.dom);if(G!==false){F.insert(G,H.panel)}else{F.add(H.panel)}F.doLayout();this.portal.fireEvent("drop",A);var I=this.scrollPos.top;if(I){var E=this.portal.body.dom;setTimeout(function(){E.scrollTop=I},10)}}delete this.lastPos},getGrid:function(){var A=this.portal.bwrap.getBox();A.columnX=[];this.portal.items.each(function(B){A.columnX.push({x:B.el.getX(),w:B.el.getWidth()})});return A}});GO.summary.MainPanel=function(C){if(!C){C={}}this.activePortletsIds=Ext.state.Manager.get("active-portlets");if(!this.activePortletsIds){this.activePortletsIds=["portlet-rss-reader","portlet-tasks","portlet-calendar","portlet-note"]}this.columns=[{columnWidth:0.5,style:"padding:10px 0 10px 10px",border:false},{columnWidth:0.5,style:"padding:10px 0 10px 10px",border:false}];var A=Math.ceil(this.activePortletsIds.length/this.columns.length);for(var E=0;E<this.activePortletsIds.length;E++){if(GO.summary.portlets[this.activePortletsIds[E]]){this.activePortlets.push(this.activePortletsIds[E]);var B=Math.ceil((E+1)/A)-1;var D=this.columns[B];if(!D.items){D.items=[GO.summary.portlets[this.activePortletsIds[E]]]}else{D.items.push(GO.summary.portlets[this.activePortletsIds[E]])}}}C.items=this.columns;for(var E in GO.summary.portlets){this.availablePortlets.push(GO.summary.portlets[E])}if(!C.items){C.html=GO.summary.lang.noItems}GO.summary.MainPanel.superclass.constructor.call(this,C)};Ext.extend(GO.summary.MainPanel,GO.summary.Portal,{activePortlets:Array(),availablePortlets:Array(),showAvailablePortlets:function(){var E={portlets:this.availablePortlets};var A=new Ext.data.JsonStore({id:"id",root:"portlets",fields:["id","title","iconCls"]});A.loadData(E);var B='<tpl for="."><div class="go-item-wrap">{title}</div></tpl>';var D=new GO.grid.SimpleSelectList({store:A,tpl:B});D.on("click",function(F,H){var J=F.store.data.items[H].data.id;var G=0;for(var I=0;I<this.columns.length;I++){if(!this.columns[I].items||this.columns[I].items.length==0||G>this.columns[I].items.length){break}G=this.columns[I].items.length}this.columns[I].add(GO.summary.portlets[J]);this.columns[I].doLayout();D.clearSelections();C.close()},this);var C=new Ext.Window({title:GO.summary.lang.selectPortlet,layout:"fit",modal:false,height:400,width:600,closable:true,closeAction:"hide",items:new Ext.Panel({items:D,cls:"go-form-panel"})});C.show()}});GO.moduleManager.addModule("summary",GO.summary.MainPanel,{title:GO.summary.lang.summary,iconCls:"go-tab-icon-summary"});GO.portlets.rssFeedPortlet=function(A){Ext.apply(this,A);if(!this.feed){this.feed="http://www.nu.nl/deeplink_rss2/index.jsp?r=Algemeen"}this.store=new Ext.data.Store({proxy:new Ext.data.HttpProxy({url:GO.settings.modules.summary.url+"feed_proxy.php"}),baseParams:{feed:this.feed},reader:new Ext.data.XmlReader({record:"item"},["title","author",{name:"pubDate",type:"date"},"link","description","content"])});this.store.setDefaultSort("pubDate","DESC");this.columns=[{id:"title",header:GO.lang.strTitle,dataIndex:"title",sortable:true,width:420,renderer:this.formatTitle},{header:GO.lang.author,dataIndex:"author",width:100,hidden:true,sortable:true},{id:"last",header:GO.lang.strDate,dataIndex:"pubDate",width:150,renderer:this.formatDate,sortable:true}];GO.portlets.rssFeedPortlet.superclass.constructor.call(this,{loadMask:{msg:GO.summary.lang.loadingFeed},sm:new Ext.grid.RowSelectionModel({singleSelect:true}),viewConfig:{forceFit:true,enableRowBody:true,showPreview:true,getRowClass:this.applyRowClass}});this.on("rowcontextmenu",this.onContextClick,this)};Ext.extend(GO.portlets.rssFeedPortlet,Ext.grid.GridPanel,{afterRender:function(){GO.portlets.rssFeedPortlet.superclass.afterRender.call(this);this.on("rowDblClick",this.rowDoubleClick,this)},rowDoubleClick:function(C,B,D){var A=this.store.getAt(B);window.open(A.data.link)},onContextClick:function(B,A,C){if(!this.menu){this.menu=new Ext.menu.Menu({id:"grid-ctx",items:[{iconCls:"new-win",text:GO.summary.lang.goToPost,scope:this,handler:function(){window.open(this.ctxRecord.data.link)}},"-",{iconCls:"refresh-icon",text:GO.lang.cmdRefresh,scope:this,handler:function(){this.ctxRow=null;this.store.reload()}}]});this.menu.on("hide",this.onContextHide,this)}C.stopEvent();if(this.ctxRow){Ext.fly(this.ctxRow).removeClass("x-node-ctx");this.ctxRow=null}this.ctxRow=this.view.getRow(A);this.ctxRecord=this.store.getAt(A);Ext.fly(this.ctxRow).addClass("x-node-ctx");this.menu.showAt(C.getXY())},onContextHide:function(){if(this.ctxRow){Ext.fly(this.ctxRow).removeClass("x-node-ctx");this.ctxRow=null}},loadFeed:function(A){this.store.baseParams={feed:A};this.store.load()},applyRowClass:function(A,D,C,B){if(this.showPreview){C.body='<p class="description">'+A.data.description+"</p>";return"x-grid3-row-expanded"}return"x-grid3-row-collapsed"},formatDate:function(B){if(!B){return""}var A=new Date();var D=A.clearTime(true);var C=B.clearTime(true).getTime();if(C==D.getTime()){return GO.summary.lang.today+B.dateFormat("g:i a")}D=D.add("d",-6);if(D.getTime()<=C){return B.dateFormat("D g:i a")}return B.dateFormat("n/j g:i a")},formatTitle:function(B,C,A){return'<div class="topic"><b>'+B+"</b></div>"}});GO.summary.portlets=[];GO.mainLayout.onReady(function(){var A=new GO.portlets.rssFeedPortlet();GO.summary.portlets["portlet-rss-reader"]=new GO.summary.Portlet({id:"portlet-rss-reader",title:GO.summary.lang.hotTopics,layout:"fit",tools:[{handler:function(){Ext.Msg.prompt(GO.lang.url,GO.summary.lang.enterRssFeed,function(D,E){if(D=="ok"){A.loadFeed(E);Ext.Ajax.request({url:GO.settings.modules.summary.url+"action.php",params:{task:"save_rss_url",url:E},waitMsg:GO.lang.waitMsgSave,waitMsgTarget:"portlet-rss-reader"})}})}}],items:A,height:300});A.on("render",function(){Ext.Ajax.request({url:GO.settings.modules.summary.url+"json.php",params:{task:"feed"},waitMsg:GO.lang.waitMsgLoad,waitMsgTarget:"portlet-rss-reader",callback:function(E,G,D){if(!G){Ext.MessageBox.alert(GO.lang.strError,GO.lang.strRequestError)}else{var F=Ext.decode(D.responseText);if(F.data.url&&F.data.url!=""){A.loadFeed(F.data.url)}else{A.loadFeed("http://newsrss.bbc.co.uk/rss/newsonline_world_edition/front_page/rss.xml")}}}})});var C=new Ext.form.TextArea({hideLabel:true,name:"text",anchor:"100% 100%"});C.on("change",function(){B.form.submit({url:GO.settings.modules.summary.url+"action.php",params:{task:"save_note"},waitMsg:GO.lang.waitMsgSave})});var B=new Ext.form.FormPanel({items:C,waitMsgTarget:true});B.on("render",function(){B.load({url:GO.settings.modules.summary.url+"json.php",params:{task:"note"},waitMsg:GO.lang.waitMsgLoad})});GO.summary.portlets["portlet-note"]=new GO.summary.Portlet({id:"portlet-note",title:GO.summary.lang.notes,layout:"fit",items:B,height:300})});