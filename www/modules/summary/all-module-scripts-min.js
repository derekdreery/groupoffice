GO.summary.AnnouncementDialog=function(B){if(!B){B={}}this.buildForm();var A=function(){this.formPanel.items.items[0].focus()};B.collapsible=true;B.maximizable=true;B.layout="fit";B.modal=false;B.resizable=false;B.width=700;B.height=500;B.closeAction="hide";B.title=GO.summary.lang.announcement;B.items=this.formPanel;B.focus=A.createDelegate(this);B.buttons=[{text:GO.lang.cmdOk,handler:function(){this.submitForm(true)},scope:this},{text:GO.lang.cmdApply,handler:function(){this.submitForm()},scope:this},{text:GO.lang.cmdClose,handler:function(){this.hide()},scope:this}];GO.summary.AnnouncementDialog.superclass.constructor.call(this,B);this.addEvents({save:true})};Ext.extend(GO.summary.AnnouncementDialog,Ext.Window,{show:function(A,B){if(!this.rendered){this.render(Ext.getBody())}if(!A){A=0}this.setAnnouncementId(A);if(this.announcement_id>0){this.formPanel.load({url:GO.settings.modules.summary.url+"json.php",waitMsg:GO.lang.waitMsgLoad,success:function(C,D){GO.summary.AnnouncementDialog.superclass.show.call(this)},failure:function(C,D){Ext.Msg.alert(GO.lang.strError,D.result.feedback)},scope:this})}else{this.formPanel.form.reset();GO.summary.AnnouncementDialog.superclass.show.call(this)}},setAnnouncementId:function(A){this.formPanel.form.baseParams.announcement_id=A;this.announcement_id=A},submitForm:function(A){this.formPanel.form.submit({url:GO.settings.modules.summary.url+"action.php",params:{task:"save_announcement"},waitMsg:GO.lang.waitMsgSave,success:function(B,C){this.fireEvent("save",this);if(A){this.hide()}else{if(C.result.announcement_id){this.setAnnouncementId(C.result.announcement_id)}}},failure:function(B,C){if(C.failureType=="client"){Ext.MessageBox.alert(GO.lang.strError,GO.lang.strErrorsInForm)}else{Ext.MessageBox.alert(GO.lang.strError,C.result.feedback)}},scope:this})},buildForm:function(){this.formPanel=new Ext.form.FormPanel({waitMsgTarget:true,url:GO.settings.modules.summary.url+"action.php",border:false,baseParams:{task:"announcement"},cls:"go-form-panel",items:[{xtype:"datefield",name:"due_time",anchor:"-20",format:GO.settings.date_format,fieldLabel:GO.summary.lang.dueTime},{xtype:"textfield",name:"title",anchor:"-20",fieldLabel:GO.summary.lang.title},{xtype:"htmleditor",name:"content",anchor:"-20 -60",hideLabel:true}]})}});GO.summary.AnnouncementsGrid=function(A){if(!A){A={}}A.border=false;A.layout="fit";A.autoScroll=true;A.split=true;A.store=new GO.data.JsonStore({url:GO.settings.modules.summary.url+"json.php",baseParams:{task:"announcements"},root:"results",id:"id",totalProperty:"total",fields:["id","user_name","due_time","ctime","mtime","title"],remoteSort:true});A.paging=true;var B=new Ext.grid.ColumnModel([{header:GO.summary.lang.title,dataIndex:"title"},{header:GO.lang.strOwner,dataIndex:"user_name",sortable:false},{header:GO.summary.lang.dueTime,dataIndex:"due_time"},{header:GO.lang.strCtime,dataIndex:"ctime"},{header:GO.lang.strMtime,dataIndex:"mtime"}]);B.defaultSortable=true;A.cm=B;A.view=new Ext.grid.GridView({autoFill:true,forceFit:true,emptyText:GO.lang.strNoItems});A.sm=new Ext.grid.RowSelectionModel();A.loadMask=true;this.announcementDialog=new GO.summary.AnnouncementDialog();this.announcementDialog.on("save",function(){this.store.reload()},this);A.tbar=[{iconCls:"btn-add",text:GO.lang.cmdAdd,cls:"x-btn-text-icon",handler:function(){this.announcementDialog.show()},scope:this},{iconCls:"btn-delete",text:GO.lang.cmdDelete,cls:"x-btn-text-icon",handler:function(){this.deleteSelected()},scope:this}];GO.summary.AnnouncementsGrid.superclass.constructor.call(this,A);this.on("rowdblclick",function(D,E){var C=D.getStore().getAt(E);this.announcementDialog.show(C.data.id)},this)};Ext.extend(GO.summary.AnnouncementsGrid,GO.grid.GridPanel,{});GO.summary.AnnouncementsViewGrid=function(A){if(!A){A={}}A.cls="go-grid3-hide-headers go-html-formatted";A.border=false;A.autoHeight=true;A.autoScroll=true;A.split=true;A.store=new GO.data.JsonStore({url:GO.settings.modules.summary.url+"json.php",baseParams:{task:"announcements",active:"true"},root:"results",id:"id",totalProperty:"total",fields:["id","user_name","due_time","ctime","mtime","title","content"],remoteSort:true});var B=new Ext.grid.ColumnModel([{header:"",dataIndex:"title",sortable:false,renderer:function(D,E,C){return"<b>"+D+"</b>"}}]);B.defaultSortable=true;A.cm=B;A.view=new Ext.grid.GridView({enableRowBody:true,showPreview:true,forceFit:true,autoFill:true,getRowClass:function(C,F,E,D){if(this.showPreview){E.body='<div class="description">'+C.data.content+"</div>";return"x-grid3-row-expanded"}return"x-grid3-row-collapsed"},emptyText:GO.lang.strNoItems});A.loadMask=true;A.disableSelection=true;GO.summary.AnnouncementsViewGrid.superclass.constructor.call(this,A)};Ext.extend(GO.summary.AnnouncementsViewGrid,GO.grid.GridPanel,{afterRender:function(){GO.summary.AnnouncementsViewGrid.superclass.afterRender.call(this);Ext.TaskMgr.start({run:this.store.load,scope:this.store,interval:180000})}});GO.summary.PortalColumn=Ext.extend(Ext.Container,{layout:"anchor",autoEl:"div",defaultType:"portlet",cls:"x-portal-column"});Ext.reg("portalcolumn",GO.summary.PortalColumn);GO.summary.Portlet=Ext.extend(Ext.Panel,{anchor:"100%",frame:true,collapsible:true,draggable:true,cls:"x-portlet",stateful:false,initComponent:function(){this.addEvents({remove:true});GO.summary.Portlet.superclass.initComponent.call(this)},saveState:function(){},removePortlet:function(){this.fireEvent("remove",this)}});Ext.reg("portlet",GO.summary.Portlet);GO.summary.Portal=Ext.extend(Ext.Panel,{layout:"column",autoScroll:true,cls:"x-portal",defaultType:"portalcolumn",initComponent:function(){GO.summary.Portal.superclass.initComponent.call(this);this.addEvents({validatedrop:true,beforedragover:true,dragover:true,beforedrop:true,drop:true})},initEvents:function(){GO.summary.Portal.superclass.initEvents.call(this);this.dd=new GO.summary.Portal.DropZone(this,this.dropConfig)}});Ext.reg("portal",GO.summary.Portal);GO.summary.Portal.DropZone=function(A,B){this.portal=A;Ext.dd.ScrollManager.register(A.body);GO.summary.Portal.DropZone.superclass.constructor.call(this,A.bwrap.dom,B);A.body.ddScrollConfig=this.ddScrollConfig};Ext.extend(GO.summary.Portal.DropZone,Ext.dd.DropTarget,{ddScrollConfig:{vthresh:50,hthresh:-1,animate:true,increment:200},createEvent:function(A,D,C,B,F,E){return{portal:this.portal,panel:C.panel,columnIndex:B,column:F,position:E,data:C,source:A,rawEvent:D,status:this.dropAllowed}},notifyOver:function(Q,O,R){var D=O.getXY(),A=this.portal,J=Q.proxy;if(!this.grid){this.grid=this.getGrid()}var B=A.body.dom.clientWidth;if(!this.lastCW){this.lastCW=B}else{if(this.lastCW!=B){this.lastCW=B;A.doLayout();this.grid=this.getGrid()}}var C=0,H=this.grid.columnX,I=false;for(var M=H.length;C<M;C++){if(D[0]<(H[C].x+H[C].w)){I=true;break}}if(!I){C--}var L,G=false,F=0,P=A.items.itemAt(C);if(!P.items){P.initItems()}var K=P.items.items;for(var M=K.length;F<M;F++){L=K[F];var N=L.el.getHeight();if(N!==0&&(L.el.getY()+(N/2))>D[1]){G=true;break}}var E=this.createEvent(Q,O,R,C,P,G&&L?F:P.items.getCount());if(A.fireEvent("validatedrop",E)!==false&&A.fireEvent("beforedragover",E)!==false){J.getProxy().setWidth("auto");if(L){J.moveProxy(L.el.dom.parentNode,G?L.el.dom:null)}else{J.moveProxy(P.el.dom,null)}this.lastPos={c:P,col:C,p:G&&L?F:false};this.scrollPos=A.body.getScroll();A.fireEvent("dragover",E);return E.status}else{return E.status}},notifyOut:function(){delete this.grid},notifyDrop:function(H,D,C){delete this.grid;if(!this.lastPos){return }var F=this.lastPos.c,B=this.lastPos.col,G=this.lastPos.p;var A=this.createEvent(H,D,C,B,F,G!==false?G:F.items.getCount());if(this.portal.fireEvent("validatedrop",A)!==false&&this.portal.fireEvent("beforedrop",A)!==false){H.proxy.getProxy().remove();H.panel.el.dom.parentNode.removeChild(H.panel.el.dom);if(G!==false){F.insert(G,H.panel)}else{F.add(H.panel)}F.doLayout();this.portal.fireEvent("drop",A);var I=this.scrollPos.top;if(I){var E=this.portal.body.dom;setTimeout(function(){E.scrollTop=I},10)}}delete this.lastPos},getGrid:function(){var A=this.portal.bwrap.getBox();A.columnX=[];this.portal.items.each(function(B){A.columnX.push({x:B.el.getX(),w:B.el.getWidth()})});return A}});GO.summary.MainPanel=function(A){if(!A){A={}}var E=Ext.state.Manager.get("summary-active-portlets");if(E){E=Ext.decode(E);if(!E[0]||E[0].col=="undefined"){E=false}}if(!E){this.activePortlets=["portlet-announcements","portlet-tasks","portlet-calendar","portlet-note"];E=[{id:"portlet-announcements",col:0},{id:"portlet-tasks",col:0},{id:"portlet-calendar",col:1},{id:"portlet-note",col:1}]}this.activePortlets=[];for(var B=0;B<E.length;B++){this.activePortlets.push(E[B].id)}this.columns=[{columnWidth:0.5,style:"padding:10px 0 10px 10px",border:false},{columnWidth:0.5,style:"padding:10px 10px 10px 10px",border:false}];for(var F=0;F<E.length;F++){if(GO.summary.portlets[E[F].id]){var D=this.columns[E[F].col];if(!D.items){D.items=[GO.summary.portlets[E[F].id]]}else{D.items.push(GO.summary.portlets[E[F].id])}}}this.availablePortletsStore=new Ext.data.JsonStore({id:"id",root:"portlets",fields:["id","title","iconCls"]});for(var F in GO.summary.portlets){if(typeof (GO.summary.portlets[F])=="object"){GO.summary.portlets[F].on("remove",function(G){G.ownerCt.remove(G,false);G.hide();this.saveActivePortlets()},this);var C=this.activePortlets.indexOf(F);if(C==-1){this.availablePortlets.push(GO.summary.portlets[F])}}}this.availablePortletsStore.loadData({portlets:this.availablePortlets});A.items=this.columns;if(!A.items){A.html=GO.summary.lang.noItems}this.tbar=[{text:GO.lang.cmdAdd,iconCls:"btn-add",handler:this.showAvailablePortlets,scope:this}];if(GO.settings.modules.summary.write_permission){this.tbar.push({text:GO.summary.lang.manageAnnouncements,iconCls:"btn-settings",handler:function(){if(!this.manageAnnouncementsWindow){this.manageAnnouncementsWindow=new Ext.Window({layout:"fit",items:this.announcementsGrid=new GO.summary.AnnouncementsGrid(),width:700,height:400,title:GO.summary.lang.announcements,closeAction:"hide",buttons:[{text:GO.lang.cmdClose,handler:function(){this.manageAnnouncementsWindow.hide()},scope:this}],listeners:{show:function(){if(!this.announcementsGrid.store.loaded){this.announcementsGrid.store.load()}},scope:this}});this.announcementsGrid.store.on("load",function(){this.announcementsGrid.store.on("load",function(){GO.summary.announcementsPanel.store.load()},this)},this)}this.manageAnnouncementsWindow.show()},scope:this})}GO.summary.MainPanel.superclass.constructor.call(this,A);this.on("drop",this.saveActivePortlets,this)};Ext.extend(GO.summary.MainPanel,GO.summary.Portal,{activePortlets:Array(),availablePortlets:Array(),saveActivePortlets:function(){this.activePortlets=[];var B=[];for(var E=0;E<this.items.items.length;E++){var A=this.items.items[E];for(var C=0;C<A.items.items.length;C++){var D=A.items.items[C].id;this.activePortlets.push(D);B.push({id:D,col:E})}}this.availablePortlets=[];for(var C in GO.summary.portlets){if(typeof (GO.summary.portlets[C])=="object"&&this.activePortlets.indexOf(C)==-1){this.availablePortlets.push(GO.summary.portlets[C])}}this.availablePortletsStore.loadData({portlets:this.availablePortlets});Ext.state.Manager.set("summary-active-portlets",Ext.encode(B))},showAvailablePortlets:function(){if(!this.portletsWindow){var A='<tpl for="."><div class="go-item-wrap">{title}</div></tpl>';var B=new GO.grid.SimpleSelectList({store:this.availablePortletsStore,tpl:A});B.on("click",function(C,D){var E=C.store.data.items[D].data.id;this.items.items[0].add(GO.summary.portlets[E]);GO.summary.portlets[E].show();this.items.items[0].doLayout();this.saveActivePortlets(true);B.clearSelections();this.portletsWindow.hide()},this);this.portletsWindow=new Ext.Window({title:GO.summary.lang.selectPortlet,layout:"fit",modal:false,height:400,width:600,closable:true,closeAction:"hide",items:new Ext.Panel({items:B,cls:"go-form-panel"})})}this.portletsWindow.show()}});GO.moduleManager.addModule("summary",GO.summary.MainPanel,{title:GO.summary.lang.summary,iconCls:"go-tab-icon-summary"});GO.portlets.rssFeedPortlet=function(A){Ext.apply(this,A);if(!this.feed){this.feed="http://www.nu.nl/deeplink_rss2/index.jsp?r=Algemeen"}this.store=new Ext.data.Store({proxy:new Ext.data.HttpProxy({url:GO.settings.modules.summary.url+"feed_proxy.php"}),baseParams:{feed:this.feed},reader:new Ext.data.XmlReader({record:"item"},["title","author",{name:"pubDate",type:"date"},"link","description","content"])});this.store.setDefaultSort("pubDate","DESC");this.columns=[{id:"title",header:GO.lang.strTitle,dataIndex:"title",sortable:true,width:420,renderer:this.formatTitle},{header:GO.lang.author,dataIndex:"author",width:100,hidden:true,sortable:true},{id:"last",header:GO.lang.strDate,dataIndex:"pubDate",width:150,renderer:this.formatDate,sortable:true}];GO.portlets.rssFeedPortlet.superclass.constructor.call(this,{loadMask:{msg:GO.summary.lang.loadingFeed},sm:new Ext.grid.RowSelectionModel({singleSelect:true}),viewConfig:{forceFit:true,enableRowBody:true,showPreview:true,getRowClass:this.applyRowClass}});this.on("rowcontextmenu",this.onContextClick,this)};Ext.extend(GO.portlets.rssFeedPortlet,Ext.grid.GridPanel,{afterRender:function(){GO.portlets.rssFeedPortlet.superclass.afterRender.call(this);this.on("rowDblClick",this.rowDoubleClick,this)},rowDoubleClick:function(C,B,D){var A=this.store.getAt(B);window.open(A.data.link)},onContextClick:function(B,A,C){if(!this.menu){this.menu=new Ext.menu.Menu({id:"grid-ctx",items:[{iconCls:"new-win",text:GO.summary.lang.goToPost,scope:this,handler:function(){window.open(this.ctxRecord.data.link)}},"-",{iconCls:"refresh-icon",text:GO.lang.cmdRefresh,scope:this,handler:function(){this.ctxRow=null;this.store.reload()}}]});this.menu.on("hide",this.onContextHide,this)}C.stopEvent();if(this.ctxRow){Ext.fly(this.ctxRow).removeClass("x-node-ctx");this.ctxRow=null}this.ctxRow=this.view.getRow(A);this.ctxRecord=this.store.getAt(A);Ext.fly(this.ctxRow).addClass("x-node-ctx");this.menu.showAt(C.getXY())},onContextHide:function(){if(this.ctxRow){Ext.fly(this.ctxRow).removeClass("x-node-ctx");this.ctxRow=null}},loadFeed:function(A){this.store.baseParams={feed:A};Ext.TaskMgr.start({run:this.store.load,scope:this.store,interval:1800000})},applyRowClass:function(A,D,C,B){if(this.showPreview){C.body='<p class="description">'+A.data.description+"</p>";return"x-grid3-row-expanded"}return"x-grid3-row-collapsed"},formatDate:function(B){if(!B){return""}var A=new Date();var D=A.clearTime(true);var C=B.clearTime(true).getTime();if(C==D.getTime()){return GO.summary.lang.today+B.dateFormat("g:i a")}D=D.add("d",-6);if(D.getTime()<=C){return B.dateFormat("D g:i a")}return B.dateFormat("n/j g:i a")},formatTitle:function(B,C,A){return'<div class="topic"><b>'+B+"</b></div>"}});GO.summary.portlets=[];GO.mainLayout.onReady(function(){var A=new GO.portlets.rssFeedPortlet();GO.summary.portlets["portlet-rss-reader"]=new GO.summary.Portlet({id:"portlet-rss-reader",title:GO.summary.lang.hotTopics,layout:"fit",tools:[{id:"gear",handler:function(){Ext.Msg.prompt(GO.lang.url,GO.summary.lang.enterRssFeed,function(D,E){if(D=="ok"){A.loadFeed(E);Ext.Ajax.request({url:GO.settings.modules.summary.url+"action.php",params:{task:"save_rss_url",url:E},waitMsg:GO.lang.waitMsgSave,waitMsgTarget:"portlet-rss-reader"})}})}},{id:"close",handler:function(F,E,D){D.removePortlet()}}],items:A,height:300});A.on("render",function(){Ext.Ajax.request({url:GO.settings.modules.summary.url+"json.php",params:{task:"feed"},waitMsg:GO.lang.waitMsgLoad,waitMsgTarget:"portlet-rss-reader",callback:function(E,G,D){if(!G){Ext.MessageBox.alert(GO.lang.strError,GO.lang.strRequestError)}else{var F=Ext.decode(D.responseText);if(F.data.url&&F.data.url!=""){A.loadFeed(F.data.url)}else{A.loadFeed("http://newsrss.bbc.co.uk/rss/newsonline_world_edition/front_page/rss.xml")}}}})});var C=new Ext.form.TextArea({hideLabel:true,name:"text",anchor:"100% 100%"});C.on("change",function(){B.form.submit({url:GO.settings.modules.summary.url+"action.php",params:{task:"save_note"},waitMsg:GO.lang.waitMsgSave})});var B=new Ext.form.FormPanel({items:C,waitMsgTarget:true});B.on("render",function(){B.load({url:GO.settings.modules.summary.url+"json.php",params:{task:"note"},waitMsg:GO.lang.waitMsgLoad})});GO.summary.portlets["portlet-note"]=new GO.summary.Portlet({id:"portlet-note",title:GO.summary.lang.notes,layout:"fit",tools:[{id:"close",handler:function(F,E,D){D.removePortlet()}}],items:B,height:300});GO.summary.announcementsPanel=new GO.summary.AnnouncementsViewGrid();GO.summary.announcementsPanel.store.on("load",function(){if(GO.summary.announcementsPanel.store.getCount()&&!GO.summary.portlets["portlet-announcements"].isVisible()){GO.summary.portlets["portlet-announcements"].show();GO.summary.portlets["portlet-announcements"].doLayout()}else{GO.summary.portlets["portlet-announcements"].hide()}},this);GO.summary.portlets["portlet-announcements"]=new GO.summary.Portlet({id:"portlet-announcements",title:GO.summary.lang.announcements,layout:"fit",items:GO.summary.announcementsPanel,autoHeight:true});GO.summary.portlets["portlet-announcements"].hide()});