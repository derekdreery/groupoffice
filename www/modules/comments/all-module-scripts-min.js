GO.comments.CommentDialog=function(B){if(!B){B={}}this.buildForm();var A=function(){this.formPanel.items.items[0].focus()};B.collapsible=true;B.layout="fit";B.modal=false;B.resizable=false;B.width=600;B.autoHeight=true;B.closeAction="hide";B.title=GO.comments.lang.comment;B.items=this.formPanel;B.focus=A.createDelegate(this);B.buttons=[{text:GO.lang.cmdOk,handler:function(){this.submitForm(true)},scope:this},{text:GO.lang.cmdClose,handler:function(){this.hide()},scope:this}];GO.comments.CommentDialog.superclass.constructor.call(this,B);this.addEvents({save:true})};Ext.extend(GO.comments.CommentDialog,Ext.Window,{show:function(B,A){if(!this.rendered){this.render(Ext.getBody())}if(!B){B=0}this.setCommentId(B);delete this.link_config;if(this.comment_id>0){this.formPanel.load({url:GO.settings.modules.comments.url+"json.php",waitMsg:GO.lang.waitMsgLoad,success:function(C,D){GO.comments.CommentDialog.superclass.show.call(this)},failure:function(C,D){Ext.Msg.alert(GO.lang.strError,D.result.feedback)},scope:this})}else{this.formPanel.form.reset();GO.comments.CommentDialog.superclass.show.call(this)}if(A&&A.link_config){this.link_config=A.link_config;this.formPanel.baseParams.link_id=A.link_config.id;this.formPanel.baseParams.link_type=A.link_config.type}},setCommentId:function(A){this.formPanel.form.baseParams.comment_id=A;this.comment_id=A},submitForm:function(A){this.formPanel.form.submit({url:GO.settings.modules.comments.url+"action.php",params:{task:"save_comment"},waitMsg:GO.lang.waitMsgSave,success:function(B,C){this.fireEvent("save",this);if(A){this.hide()}else{if(C.result.comment_id){this.setCommentId(C.result.comment_id)}}if(this.link_config&&this.link_config.callback){this.link_config.callback.call(this)}},failure:function(B,C){if(C.failureType=="client"){Ext.MessageBox.alert(GO.lang.strError,GO.lang.strErrorsInForm)}else{Ext.MessageBox.alert(GO.lang.strError,C.result.feedback)}},scope:this})},buildForm:function(){this.formPanel=new Ext.form.FormPanel({waitMsgTarget:true,url:GO.settings.modules.comments.url+"action.php",border:false,autoHeight:true,cls:"go-form-panel",baseParams:{task:"comment"},items:[{xtype:"textarea",name:"comments",anchor:"100%",height:200,hideLabel:true}]})}});GO.mainLayout.onReady(function(){GO.comments.commentDialog=new GO.comments.CommentDialog()});GO.comments.browseComments=function(A,B){if(!GO.comments.commentsBrowser){GO.comments.commentsBrowser=new GO.comments.CommentsBrowser()}GO.comments.commentsBrowser.show({link_id:A,link_type:B})};GO.newMenuItems.push({text:GO.comments.lang.comment,iconCls:"go-menu-icon-comments",handler:function(A,B){GO.comments.commentDialog.show(0,{link_config:A.parentMenu.link_config})}});GO.comments.CommentsGrid=function(A){if(!A){A={}}A.layout="fit";A.autoScroll=true;A.split=true;A.border=false;A.store=new GO.data.JsonStore({url:GO.settings.modules.comments.url+"json.php",baseParams:{task:"comments"},root:"results",id:"id",totalProperty:"total",fields:["id","link_id","link_type","user_name","ctime","mtime","comments"],remoteSort:true});A.store.on("load",function(){this.setWritePermission(this.store.reader.jsonData.write_permission)},this);A.paging=true;var B=new Ext.grid.ColumnModel([{header:GO.lang.strOwner,dataIndex:"user_name",sortable:false,renderer:function(C){return"<i>"+C+"</i>"}},{header:GO.lang.strCtime,dataIndex:"ctime",align:"right",renderer:function(C){return"<b>"+C+"</b>"}}]);B.defaultSortable=true;A.cm=B;A.viewConfig={forceFit:true,enableRowBody:true,showPreview:true,getRowClass:this.applyRowClass};A.disabled=true;A.sm=new Ext.grid.RowSelectionModel();A.loadMask=true;A.tbar=[{iconCls:"btn-add",text:GO.lang.cmdAdd,cls:"x-btn-text-icon",handler:function(){GO.comments.commentDialog.formPanel.baseParams.link_id=this.store.baseParams.link_id;GO.comments.commentDialog.formPanel.baseParams.link_type=this.store.baseParams.link_type;GO.comments.commentDialog.show()},scope:this},{iconCls:"btn-delete",text:GO.lang.cmdDelete,cls:"x-btn-text-icon",handler:function(){this.deleteSelected()},scope:this}];GO.comments.CommentsGrid.superclass.constructor.call(this,A);this.on("rowdblclick",function(D,E){if(this.writePermission){var C=D.getStore().getAt(E);GO.comments.commentDialog.show(C.data.id)}},this)};Ext.extend(GO.comments.CommentsGrid,GO.grid.GridPanel,{writePermission:false,setWritePermission:function(A){this.writePermission=A;this.getTopToolbar().setDisabled(!A)},afterRender:function(){GO.comments.commentDialog.on("save",function(){this.store.reload()},this);GO.comments.CommentsGrid.superclass.afterRender.call(this)},applyRowClass:function(A,D,C,B){if(this.showPreview){C.body='<p class="description">'+A.data.comments+"</p>";return"x-grid3-row-expanded"}return"x-grid3-row-collapsed"},setLinkId:function(A,B){this.store.baseParams.link_id=A;this.store.baseParams.link_type=B;this.store.loaded=false;this.setDisabled(A<1)},onShow:function(){GO.grid.LinksPanel.superclass.onShow.call(this);if(!this.store.loaded){this.store.load()}}});GO.comments.displayPanelTemplate='<tpl if="comments.length"><table cellpadding="0" cellspacing="0" border="0" class="display-panel"><tr><td class="display-panel-heading" colspan="2">'+GO.comments.lang.fiveLatestComments+' (<a href="#" onclick="GO.comments.browseComments({id}, {link_type});" class="normal-link">'+GO.comments.lang.browseComments+'</a>)</td></tr><tpl for="comments"><tr><td><i>{user_name}</i></td><td style="text-align:right"><b>{ctime}</b></td></tr><tr><td colspan="2" style="padding-left:5px">{comments}<hr /></td></tr></tpl></tpl></table></tpl>';GO.comments.CommentsBrowser=function(A){Ext.apply(this,A);this.commentsGrid=new GO.comments.CommentsGrid();GO.comments.CommentsBrowser.superclass.constructor.call(this,{layout:"fit",modal:false,minWidth:300,minHeight:300,height:500,width:700,plain:true,maximizable:true,closeAction:"hide",title:GO.comments.lang.browseComments,items:this.commentsGrid,buttons:[{text:GO.lang.cmdClose,handler:function(){this.hide()},scope:this}]});this.addEvents({link:true})};Ext.extend(GO.comments.CommentsBrowser,Ext.Window,{show:function(A){this.commentsGrid.setLinkId(A.link_id,A.link_type);this.commentsGrid.store.load();GO.comments.CommentsBrowser.superclass.show.call(this)}});