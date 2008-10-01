GO.notes.CategoriesGrid=function(A){if(!A){A={}}A.title=GO.notes.lang.categories;A.layout="fit";A.autoScroll=true;A.split=true;A.store=new GO.data.JsonStore({url:GO.settings.modules.notes.url+"json.php",baseParams:{task:"categories",auth_type:"read"},root:"results",id:"id",totalProperty:"total",fields:["id","user_name","acl_read","acl_write","name"],remoteSort:true});var B=new Ext.grid.ColumnModel([{header:GO.lang.strName,dataIndex:"name"}]);B.defaultSortable=true;A.cm=B;A.view=new Ext.grid.GridView({autoFill:true,forceFit:true,emptyText:GO.lang.strNoItems});A.sm=new Ext.grid.RowSelectionModel();A.loadMask=true;this.categoryDialog=new GO.notes.CategoryDialog();this.categoryDialog.on("save",function(){this.store.reload()},this);GO.notes.CategoriesGrid.superclass.constructor.call(this,A);this.on("rowdblclick",function(D,E){var C=D.getStore().getAt(E);this.categoryDialog.show(C.data.id)},this)};Ext.extend(GO.notes.CategoriesGrid,GO.grid.GridPanel,{loaded:false,afterRender:function(){GO.notes.CategoriesGrid.superclass.afterRender.call(this);if(this.isVisible()){this.onGridShow()}},onGridShow:function(){if(!this.loaded&&this.rendered){this.store.load();this.loaded=true}}});GO.notes.CategoryDialog=function(B){if(!B){B={}}this.buildForm();var A=function(){this.propertiesPanel.items.items[0].focus()};B.maximizable=true;B.layout="fit";B.modal=false;B.resizable=false;B.width=700;B.height=500;B.closeAction="hide";B.title=GO.notes.lang.category;B.items=this.formPanel;B.focus=A.createDelegate(this);B.buttons=[{text:GO.lang.cmdOk,handler:function(){this.submitForm(true)},scope:this},{text:GO.lang.cmdApply,handler:function(){this.submitForm()},scope:this},{text:GO.lang.cmdClose,handler:function(){this.hide()},scope:this}];GO.notes.CategoryDialog.superclass.constructor.call(this,B);this.addEvents({save:true})};Ext.extend(GO.notes.CategoryDialog,Ext.Window,{show:function(A){if(!this.rendered){this.render(Ext.getBody())}this.tabPanel.setActiveTab(0);if(!A){A=0}this.setCategoryId(A);if(this.category_id>0){this.formPanel.load({url:GO.settings.modules.notes.url+"json.php",success:function(B,C){this.setWritePermission(C.result.data.write_permission);this.readPermissionsTab.setAcl(C.result.data.acl_read);this.writePermissionsTab.setAcl(C.result.data.acl_write);this.selectUser.setRemoteText(C.result.data.user_name);GO.notes.CategoryDialog.superclass.show.call(this)},failure:function(B,C){Ext.Msg.alert(GO.lang.strError,C.result.feedback)},scope:this})}else{this.formPanel.form.reset();this.setWritePermission(true);GO.notes.CategoryDialog.superclass.show.call(this)}},setWritePermission:function(A){this.buttons[0].setDisabled(!A);this.buttons[1].setDisabled(!A)},setCategoryId:function(A){this.formPanel.form.baseParams.category_id=A;this.category_id=A},submitForm:function(A){this.formPanel.form.submit({url:GO.settings.modules.notes.url+"action.php",params:{task:"save_category"},waitMsg:GO.lang.waitMsgSave,success:function(B,C){this.fireEvent("save",this);if(A){this.hide()}else{if(C.result.category_id){this.setCategoryId(C.result.category_id);this.readPermissionsTab.setAcl(C.result.acl_read);this.writePermissionsTab.setAcl(C.result.acl_write)}}},failure:function(B,C){if(C.failureType=="client"){Ext.MessageBox.alert(GO.lang.strError,GO.lang.strErrorsInForm)}else{Ext.MessageBox.alert(GO.lang.strError,C.result.feedback)}},scope:this})},buildForm:function(){this.propertiesPanel=new Ext.Panel({url:GO.settings.modules.notes.url+"action.php",border:false,baseParams:{task:"category"},title:GO.lang.strProperties,cls:"go-form-panel",waitMsgTarget:true,layout:"form",autoScroll:true,items:[this.selectUser=new GO.form.SelectUser({fieldLabel:GO.lang.strUser,disabled:!GO.settings.modules.notes["write_permission"],value:GO.settings.user_id,anchor:"100%"}),{xtype:"textfield",name:"name",anchor:"100%",allowBlank:false,fieldLabel:GO.lang.strName}]});var A=[this.propertiesPanel];this.readPermissionsTab=new GO.grid.PermissionsPanel({title:GO.lang.strReadPermissions});this.writePermissionsTab=new GO.grid.PermissionsPanel({title:GO.lang.strWritePermissions});A.push(this.readPermissionsTab);A.push(this.writePermissionsTab);this.tabPanel=new Ext.TabPanel({activeTab:0,deferredRender:false,border:false,items:A,anchor:"100% 100%"});this.formPanel=new Ext.form.FormPanel({waitMsgTarget:true,url:GO.settings.modules.notes.url+"action.php",border:false,baseParams:{task:"category"},items:this.tabPanel})}});GO.notes.NotesGrid=function(A){if(!A){A={}}A.title=GO.notes.lang.notes;A.layout="fit";A.autoScroll=true;A.split=true;A.store=new GO.data.JsonStore({url:GO.settings.modules.notes.url+"json.php",baseParams:{task:"notes",category_id:0},root:"results",id:"id",totalProperty:"total",fields:["id","category_id","user_name","ctime","mtime","name","content"],remoteSort:true});A.paging=true;var B=new Ext.grid.ColumnModel([{header:GO.lang.strName,dataIndex:"name"},{header:GO.lang.strOwner,dataIndex:"user_name",sortable:false},{header:GO.lang.strCtime,dataIndex:"ctime"},{header:GO.lang.strMtime,dataIndex:"mtime"}]);B.defaultSortable=true;A.cm=B;A.view=new Ext.grid.GridView({autoFill:true,forceFit:true,emptyText:GO.lang.strNoItems});A.sm=new Ext.grid.RowSelectionModel();A.loadMask=true;GO.notes.NotesGrid.superclass.constructor.call(this,A)};Ext.extend(GO.notes.NotesGrid,GO.grid.GridPanel,{afterRender:function(){if(!GO.notes.noteDialog.hasListener("save")){GO.notes.noteDialog.on("save",function(){this.store.reload()},this)}GO.notes.NotesGrid.superclass.afterRender.call(this)}});GO.notes.NoteDialog=function(B){if(!B){B={}}this.buildForm();var A=function(){this.propertiesPanel.items.items[0].focus()};B.collapsible=true;B.maximizable=true;B.layout="fit";B.modal=false;B.resizable=true;B.width=700;B.height=500;B.closeAction="hide";B.title=GO.notes.lang.note;B.items=this.formPanel;B.focus=A.createDelegate(this);B.buttons=[{text:GO.lang.cmdOk,handler:function(){this.submitForm(true)},scope:this},{text:GO.lang.cmdApply,handler:function(){this.submitForm()},scope:this},{text:GO.lang.cmdClose,handler:function(){this.hide()},scope:this}];GO.notes.NoteDialog.superclass.constructor.call(this,B);this.addEvents({save:true})};Ext.extend(GO.notes.NoteDialog,Ext.Window,{show:function(B,A){if(!this.rendered){this.render(Ext.getBody())}this.tabPanel.setActiveTab(0);if(!B){B=0}this.setNoteId(B);if(this.note_id>0){this.formPanel.load({url:GO.settings.modules.notes.url+"json.php",success:function(C,D){if(GO.files){this.fileBrowser.setRootPath(D.result.data.files_path);this.fileBrowser.setDisabled(false)}this.selectCategory.setRemoteText(D.result.data.category_name);this.selectUser.setRemoteText(D.result.data.user_name);GO.notes.NoteDialog.superclass.show.call(this)},failure:function(C,D){Ext.Msg.alert(GO.lang.strError,D.result.feedback)},scope:this})}else{this.formPanel.form.reset();if(GO.files){this.fileBrowser.setDisabled(true)}GO.notes.NoteDialog.superclass.show.call(this)}if(A&&A.link_config){this.link_config=A.link_config;if(A.link_config.type_id){this.selectLinkField.setValue(A.link_config.type_id);this.selectLinkField.setRemoteText(A.link_config.text)}}else{delete this.link_config}},setNoteId:function(A){this.formPanel.form.baseParams.note_id=A;this.note_id=A;this.linksPanel.loadLinks(A,4);this.selectLinkField.container.up("div.x-form-item").setDisplayed(A==0)},submitForm:function(A){this.formPanel.form.submit({url:GO.settings.modules.notes.url+"action.php",params:{task:"save_note"},waitMsg:GO.lang.waitMsgSave,success:function(B,C){this.fireEvent("save",this);if(A){this.hide()}else{if(C.result.note_id){this.setNoteId(C.result.note_id);if(GO.files&&C.result.files_path){this.fileBrowser.setRootPath(C.result.files_path);this.fileBrowser.setDisabled(false)}}}if(this.link_config&&this.link_config.callback){this.link_config.callback.call(this)}},failure:function(B,C){if(C.failureType=="client"){Ext.MessageBox.alert(GO.lang.strError,GO.lang.strErrorsInForm)}else{Ext.MessageBox.alert(GO.lang.strError,C.result.feedback)}},scope:this})},buildForm:function(){this.selectLinkField=new GO.form.SelectLink({anchor:"-20"});this.propertiesPanel=new Ext.Panel({url:GO.settings.modules.notes.url+"action.php",border:false,baseParams:{task:"note"},title:GO.lang.strProperties,cls:"go-form-panel",waitMsgTarget:true,layout:"form",autoScroll:true,items:[this.selectLinkField,this.selectCategory=new GO.form.ComboBox({fieldLabel:GO.notes.lang.category_id,hiddenName:"category_id",anchor:"-20",emptyText:GO.lang.strPleaseSelect,store:new GO.data.JsonStore({url:GO.settings.modules.notes.url+"json.php",baseParams:{auth_type:"write",task:"categories"},root:"results",id:"id",totalProperty:"total",fields:["id","name","user_name"],remoteSort:true}),pageSize:parseInt(GO.settings.max_rows_list),valueField:"id",displayField:"name",mode:"remote",triggerAction:"all",editable:true,selectOnFocus:true,forceSelection:true,allowBlank:false}),this.selectUser=new GO.form.SelectUser({fieldLabel:GO.lang.strUser,disabled:!GO.settings.modules.notes["write_permission"],value:GO.settings.user_id,anchor:"-20"}),{xtype:"textfield",name:"name",width:300,anchor:"-20",allowBlank:false,fieldLabel:GO.lang.strName},{xtype:"textarea",name:"content",anchor:"-20 -110",allowBlank:true,fieldLabel:GO.notes.lang.content}]});var A=[this.propertiesPanel];if(GO.files){this.fileBrowser=new GO.files.FileBrowser({title:GO.lang.strFiles,treeRootVisible:true,treeCollapsed:true,loadDelayed:true,disabled:true});A.push(this.fileBrowser)}this.linksPanel=new GO.grid.LinksPanel({title:GO.lang.strLinks});A.push(this.linksPanel);if(GO.customfields&&GO.customfields.types["4"]){for(var B=0;B<GO.customfields.types["4"].panels.length;B++){A.push(GO.customfields.types["4"].panels[B])}}this.tabPanel=new Ext.TabPanel({activeTab:0,deferredRender:false,border:false,items:A,anchor:"100% 100%"});this.formPanel=new Ext.form.FormPanel({waitMsgTarget:true,url:GO.settings.modules.notes.url+"action.php",border:false,baseParams:{task:"note"},items:this.tabPanel})}});GO.notes.NotePanel=function(A){Ext.apply(this,A);this.split=true;this.autoScroll=true;this.title=GO.notes.lang.note;this.newMenuButton=new GO.NewMenuButton();this.tbar=[this.editButton=new Ext.Button({iconCls:"btn-edit",text:GO.lang.cmdEdit,cls:"x-btn-text-icon",handler:function(){if(!GO.notes.noteDialog){GO.notes.noteDialog=new GO.notes.NoteDialog()}GO.notes.noteDialog.show(this.data.id)},scope:this,disabled:true}),{iconCls:"btn-link",cls:"x-btn-text-icon",text:GO.lang.cmdBrowseLinks,handler:function(){GO.linkBrowser.show({link_id:this.data.id,link_type:"4",folder_id:"0"})},scope:this},this.newMenuButton];GO.notes.NotePanel.superclass.constructor.call(this)};Ext.extend(GO.notes.NotePanel,Ext.Panel,{initComponent:function(){var B='<div><table class="display-panel" cellpadding="0" cellspacing="0" border="0"><tr><td colspan="2" class="display-panel-heading">{name}</td></tr><tpl if="content.length"><tr><td colspan="2">{content}</td></tr></tpl></table>';B+=GO.linksTemplate;if(GO.customfields){B+=GO.customfields.displayPanelTemplate}var A={};if(GO.files){Ext.apply(A,GO.files.filesTemplateConfig);B+=GO.files.filesTemplate}Ext.apply(A,GO.linksTemplateConfig);B+="</div>";this.template=new Ext.XTemplate(B,A);GO.notes.NotePanel.superclass.initComponent.call(this)},loadNote:function(A){this.body.mask(GO.lang.waitMsgLoad);Ext.Ajax.request({url:GO.settings.modules.notes.url+"json.php",params:{task:"note_with_items",note_id:A},callback:function(C,E,B){this.body.unmask();if(!E){Ext.MessageBox.alert(GO.lang.strError,GO.lang.strRequestError)}else{var D=Ext.decode(B.responseText);this.setData(D.data)}},scope:this})},setData:function(A){this.data=A;this.editButton.setDisabled(!A.write_permission);if(A.write_permission){this.newMenuButton.setLinkConfig({id:this.data.id,type:4,text:this.data.name,callback:function(){this.loadNote(this.data.id)},scope:this})}this.template.overwrite(this.body,A)}});GO.notes.ManageCategoriesGrid=function(A){if(!A){A={}}A.layout="fit";A.autoScroll=true;A.split=true;A.store=GO.notes.writableCategoriesStore;A.border=false;A.paging=true;var B=new Ext.grid.ColumnModel([{header:GO.lang.strName,dataIndex:"name"},{header:GO.lang.strOwner,dataIndex:"user_name",sortable:false}]);B.defaultSortable=true;A.cm=B;A.view=new Ext.grid.GridView({autoFill:true,forceFit:true,emptyText:GO.lang.strNoItems});A.sm=new Ext.grid.RowSelectionModel();A.loadMask=true;this.categoryDialog=new GO.notes.CategoryDialog();this.categoryDialog.on("save",function(){this.store.reload();this.changed=true},this);A.tbar=[{iconCls:"btn-add",text:GO.lang.cmdAdd,cls:"x-btn-text-icon",handler:function(){this.categoryDialog.show()},scope:this},{iconCls:"btn-delete",text:GO.lang.cmdDelete,cls:"x-btn-text-icon",handler:function(){this.deleteSelected();this.changed=true},scope:this}];GO.notes.ManageCategoriesGrid.superclass.constructor.call(this,A);this.on("rowdblclick",function(D,E){var C=D.getStore().getAt(E);this.categoryDialog.show(C.data.id)},this)};Ext.extend(GO.notes.ManageCategoriesGrid,GO.grid.GridPanel,{changed:false});GO.notes.ManageCategoriesDialog=function(A){if(!A){A={}}this.categoriesGrid=new GO.notes.ManageCategoriesGrid();A.iconCls="no-btn-categories";A.maximizable=true;A.layout="fit";A.modal=false;A.resizable=false;A.width=500;A.height=300;A.closeAction="hide";A.title=GO.notes.lang.manageCategories;A.items=this.categoriesGrid;A.buttons=[{text:GO.lang.cmdClose,handler:function(){if(this.categoriesGrid.changed){this.fireEvent("change");this.categoriesGrid.changed=false}this.hide()},scope:this}];GO.notes.ManageCategoriesDialog.superclass.constructor.call(this,A);this.addEvents({change:true})};Ext.extend(GO.notes.ManageCategoriesDialog,Ext.Window,{});GO.notes.MainPanel=function(A){if(!A){A={}}this.westPanel=new GO.notes.CategoriesGrid({region:"west",title:GO.lang.menu,autoScroll:true,width:150,split:true});this.westPanel.on("rowclick",function(D,E){var C=D.getStore().getAt(E);this.centerPanel.store.baseParams.category_id=C.data.id;this.category_id=C.data.id;this.category_name=C.data.name;this.centerPanel.store.load()},this);this.westPanel.store.on("load",function(){this.westPanel.selModel.selectFirstRow();GO.notes.writableCategoriesStore.load();var C=this.westPanel.selModel.getSelected();if(C){this.centerPanel.store.baseParams.category_id=C.data.id;this.category_id=C.data.id;this.category_name=C.data.name;this.centerPanel.store.load()}},this);this.centerPanel=new GO.notes.NotesGrid({region:"center",border:true});this.centerPanel.on("rowclick",function(D,E){var C=D.getStore().getAt(E);this.eastPanel.loadNote(C.data.id)},this);this.eastPanel=new GO.notes.NotePanel({region:"east",width:400,border:true});var B=new Ext.Panel({region:"north",baseCls:"x-plain",split:true,resizable:false,tbar:new Ext.Toolbar({cls:"go-head-tb",items:[{iconCls:"btn-add",text:GO.lang.cmdAdd,cls:"x-btn-text-icon",handler:function(){GO.notes.noteDialog.show();GO.notes.noteDialog.formPanel.form.setValues({category_id:this.centerPanel.store.baseParams.category_id});GO.notes.noteDialog.selectCategory.setRemoteText(this.category_name)},scope:this},{iconCls:"btn-delete",text:GO.lang.cmdDelete,cls:"x-btn-text-icon",handler:function(){this.centerPanel.deleteSelected()},scope:this},{iconCls:"no-btn-categories",text:GO.notes.lang.manageCategories,cls:"x-btn-text-icon",handler:function(){if(!this.categoriesDialog){this.categoriesDialog=new GO.notes.ManageCategoriesDialog();this.categoriesDialog.on("change",function(){this.westPanel.store.reload()},this)}this.categoriesDialog.show()},scope:this}]})});A.items=[B,this.westPanel,this.centerPanel,this.eastPanel];A.layout="border";GO.notes.MainPanel.superclass.constructor.call(this,A)};Ext.extend(GO.notes.MainPanel,Ext.Panel,{afterRender:function(){GO.notes.MainPanel.superclass.afterRender.call(this)}});GO.notes.writableCategoriesStore=new GO.data.JsonStore({url:GO.settings.modules.notes.url+"json.php",baseParams:{auth_type:"write",task:"categories"},root:"results",id:"id",totalProperty:"total",fields:["id","name","user_name"],remoteSort:true});GO.mainLayout.onReady(function(){GO.notes.noteDialog=new GO.notes.NoteDialog()});GO.moduleManager.addModule("notes",GO.notes.MainPanel,{title:GO.notes.lang.notes,iconCls:"go-tab-icon-notes"});GO.linkHandlers[4]=function(A){if(!GO.notes.noteDialog){GO.notes.noteDialog=new GO.notes.NoteDialog()}GO.notes.noteDialog.show(A)};GO.newMenuItems.push({text:GO.notes.lang.note,iconCls:"go-link-icon-4",handler:function(A,B){GO.notes.noteDialog.show(0,{link_config:A.parentMenu.link_config})}});