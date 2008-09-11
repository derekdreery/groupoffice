GO.files.FilePropertiesDialog=function(A){if(!A){A={}}this.local_path=A.local_path;this.propertiesPanel=new Ext.Panel({layout:"form",title:GO.lang.strProperties,cls:"go-form-panel",labelWidth:70,defaultType:"textfield",items:[{fieldLabel:GO.lang.strName,name:"name",anchor:"100%"},new GO.form.PlainField({type:"plainfield",fieldLabel:"Path",id:"path"}),new GO.form.HtmlComponent({html:"<hr />"}),new GO.form.PlainField({fieldLabel:GO.lang.strCtime,id:"ctime"}),new GO.form.PlainField({fieldLabel:GO.lang.strMtime,id:"mtime"}),new GO.form.PlainField({fieldLabel:GO.lang.Atime,id:"atime"}),new GO.form.HtmlComponent({html:"<hr />"}),new GO.form.PlainField({fieldLabel:GO.lang.strType,id:"type"}),new GO.form.PlainField({fieldLabel:GO.lang.strSize,id:"size"})]});this.commentsPanel=new Ext.Panel({layout:"form",labelWidth:70,title:GO.files.lang.comments,border:false,items:new Ext.form.TextArea({name:"comments",fieldLabel:"",hideLabel:true,anchor:"100% 100%"})});this.tabPanel=new Ext.TabPanel({activeTab:0,deferredRender:false,border:false,anchor:"100% 100%",hideLabel:true,items:[this.propertiesPanel,this.commentsPanel]});this.formPanel=new Ext.form.FormPanel({border:false,defaultType:"textfield",items:this.tabPanel});GO.files.FilePropertiesDialog.superclass.constructor.call(this,{title:GO.lang.strProperties,layout:"fit",width:400,height:400,closeAction:"hide",items:this.formPanel,buttons:[{text:GO.lang.cmdOk,handler:function(){this.save(true)},scope:this},{text:GO.lang.cmdApply,handler:function(){this.save(false)},scope:this},{text:GO.lang.cmdClose,handler:function(){this.hide()},scope:this}]});this.addEvents({rename:true})};Ext.extend(GO.files.FilePropertiesDialog,Ext.Window,{show:function(A){this.path=A;if(!this.rendered){this.render(Ext.getBody())}this.formPanel.form.load({url:GO.settings.modules.files.url+"json.php",params:{path:A,task:"file_properties",local_path:this.local_path},success:function(B,C){this.setWritePermission(C.result.data.write_permission);this.tabPanel.setActiveTab(0);GO.files.FilePropertiesDialog.superclass.show.call(this)},failure:function(B,C){Ext.MessageBox.alert(GO.lang.strError,C.result.feedback)},scope:this})},setWritePermission:function(A){var B=this.formPanel.form;B.findField("name").setDisabled(!A)},save:function(A){this.formPanel.form.submit({url:GO.settings.modules.files.url+"action.php",params:{path:this.path,task:"file_properties",local_path:this.local_path},waitMsg:GO.lang.waitMsgSave,success:function(B,C){if(C.result.path){this.path=C.result.path;this.fireEvent("rename",this)}if(A){this.hide()}},failure:function(C,D){var B="";if(D.failureType=="client"){B=GO.lang.strErrorsInForm}else{B=D.result.feedback}Ext.MessageBox.alert(GO.lang.strError,B)},scope:this})}});GO.files.FolderPropertiesDialog=function(A){if(!A){A={}}this.local_path=A.local_path;this.propertiesPanel=new Ext.Panel({layout:"form",title:GO.lang.strProperties,cls:"go-form-panel",defaultType:"textfield",labelWidth:70,border:false,items:[{fieldLabel:GO.lang.strName,name:"name",anchor:"100%"},new GO.form.PlainField({type:"plainfield",fieldLabel:"Path",id:"path"}),new GO.form.HtmlComponent({html:"<hr />"}),new GO.form.PlainField({fieldLabel:GO.lang.strCtime,id:"ctime"}),new GO.form.PlainField({fieldLabel:GO.lang.strMtime,id:"mtime"}),new GO.form.PlainField({fieldLabel:GO.lang.Atime,id:"atime"}),new GO.form.HtmlComponent({html:"<hr />"}),new GO.form.PlainField({fieldLabel:GO.lang.strType,id:"type"}),new GO.form.PlainField({fieldLabel:GO.lang.strSize,id:"size"}),new Ext.form.Checkbox({boxLabel:GO.files.lang.activateSharing,name:"share",checked:false,hideLabel:true}),new Ext.form.Checkbox({boxLabel:GO.files.lang.notifyChanges,name:"notify",checked:false,hideLabel:true})]});this.readPermissionsTab=new GO.grid.PermissionsPanel({title:GO.lang.strReadPermissions});this.writePermissionsTab=new GO.grid.PermissionsPanel({title:GO.lang.strWritePermissions});this.commentsPanel=new Ext.Panel({layout:"form",labelWidth:70,title:GO.files.lang.comments,border:false,items:new Ext.form.TextArea({name:"comments",fieldLabel:"",hideLabel:true,anchor:"100% 100%"})});this.tabPanel=new Ext.TabPanel({activeTab:0,deferredRender:false,border:false,anchor:"100% 100%",hideLabel:true,items:[this.propertiesPanel,this.commentsPanel,this.readPermissionsTab,this.writePermissionsTab]});this.formPanel=new Ext.form.FormPanel({border:false,defaultType:"textfield",items:this.tabPanel});GO.files.FolderPropertiesDialog.superclass.constructor.call(this,{title:GO.lang.strProperties,layout:"fit",width:400,height:400,closeAction:"hide",items:this.formPanel,buttons:[{text:GO.lang.cmdOk,handler:function(){this.save(true)},scope:this},{text:GO.lang.cmdApply,handler:function(){this.save(false)},scope:this},{text:GO.lang.cmdClose,handler:function(){this.hide()},scope:this}]});this.addEvents({rename:true})};Ext.extend(GO.files.FolderPropertiesDialog,Ext.Window,{show:function(A){this.path=A;if(!this.rendered){this.render(Ext.getBody())}this.formPanel.form.load({url:GO.settings.modules.files.url+"json.php",params:{path:A,task:"folder_properties",local_path:this.local_path},success:function(B,C){this.formPanel.form.findField("share").setValue(C.result.data.acl_read>0);this.readPermissionsTab.setAcl(C.result.data.acl_read);this.writePermissionsTab.setAcl(C.result.data.acl_write);this.setWritePermission(C.result.data.write_permission);this.tabPanel.setActiveTab(0);GO.files.FolderPropertiesDialog.superclass.show.call(this)},failure:function(B,C){Ext.MessageBox.alert(GO.lang.strError,C.result.feedback)},scope:this})},setWritePermission:function(A){var B=this.formPanel.form;B.findField("name").setDisabled(!A);B.findField("share").setDisabled(!A);this.readPermissionsTab.setDisabled(!A||this.readPermissionsTab.acl_id==0);this.writePermissionsTab.setDisabled(!A||this.writePermissionsTab.acl_id==0)},save:function(A){this.formPanel.form.submit({url:GO.settings.modules.files.url+"action.php",params:{path:this.path,task:"folder_properties",local_path:this.local_path},waitMsg:GO.lang.waitMsgSave,success:function(B,C){if(C.result.acl_read){this.readPermissionsTab.setAcl(C.result.acl_read);this.writePermissionsTab.setAcl(C.result.acl_write)}if(C.result.path){this.path=C.result.path;this.fireEvent("rename",this)}if(A){this.hide()}},failure:function(C,D){var B="";if(D.failureType=="client"){B=GO.lang.strErrorsInForm}else{B=D.result.feedback}Ext.MessageBox.alert(GO.lang.strError,B)},scope:this})}});GO.files.FilesContextMenu=function(A){if(!A){A={}}A.shadow="frame";A.minWidth=180;this.downloadButton=new Ext.menu.Item({iconCls:"btn-download",text:GO.lang.download,cls:"x-btn-text-icon",handler:function(){this.fireEvent("download",this,this.clickedAt)},scope:this});this.gotaButton=new Ext.menu.Item({iconCls:"btn-download-gota",text:GO.files.lang.downloadGOTA,cls:"x-btn-text-icon",handler:function(){this.fireEvent("gota",this,this.clickedAt)},scope:this});this.deleteButton=new Ext.menu.Item({iconCls:"btn-delete",text:GO.lang.cmdDelete,cls:"x-btn-text-icon",handler:function(){this.fireEvent("delete",this,this.clickedAt)},scope:this});this.cutButton=new Ext.menu.Item({iconCls:"btn-cut",text:GO.lang.cut,cls:"x-btn-text-icon",handler:function(){this.fireEvent("cut",this,this.clickedAt)},scope:this});this.copyButton=new Ext.menu.Item({iconCls:"btn-copy",text:GO.lang.copy,cls:"x-btn-text-icon",handler:function(){this.fireEvent("copy",this,this.clickedAt)},scope:this});this.compressButton=new Ext.menu.Item({iconCls:"btn-compress",text:GO.lang.compress,cls:"x-btn-text-icon",handler:function(){this.fireEvent("compress",this,this.clickedAt)},scope:this});this.decompressButton=new Ext.menu.Item({iconCls:"btn-decompress",text:GO.lang.decompress,cls:"x-btn-text-icon",handler:function(){this.fireEvent("decompress",this,this.clickedAt)},scope:this});A.items=[this.downloadButton];if(GO.settings.modules.gota){A.items.push(this.gotaButton)}A.items.push({iconCls:"btn-properties",text:GO.lang.strProperties,handler:function(){this.fireEvent("properties",this,this.clickedAt)},scope:this});A.items.push(new Ext.menu.Separator());A.items.push(this.cutButton);A.items.push(this.copyButton);A.items.push(new Ext.menu.Separator());A.items.push(this.deleteButton);A.items.push(this.compressSeparator=new Ext.menu.Separator());A.items.push(this.compressButton);A.items.push(this.decompressButton);GO.files.FilesContextMenu.superclass.constructor.call(this,A);this.addEvents({properties:true,paste:true,cut:true,copy:true,"delete":true,compress:true,decompress:true})};Ext.extend(GO.files.FilesContextMenu,Ext.menu.Menu,{clickedAt:"grid",showAt:function(B,C,A){this.clickedAt=A;switch(C){case"zip":case"tar":case"tgz":case"gz":this.downloadButton.show();this.gotaButton.show();this.decompressButton.show();this.compressButton.hide();break;case"":case"folder":this.downloadButton.hide();this.gotaButton.hide();this.decompressButton.hide();this.compressButton.show();break;default:this.downloadButton.show();this.gotaButton.show();this.compressButton.show();this.decompressButton.hide();break}GO.files.FilesContextMenu.superclass.showAt.call(this,B)}});GO.files.TemplateWindow=function(A){this.gridStore=new GO.data.JsonStore({url:GO.settings.modules.files.url+"json.php",baseParams:{task:"templates",writable_only:"true"},root:"results",totalProperty:"total",id:"id",fields:["id","name","type","grid_display"],remoteSort:true});this.gridStore.on("load",function(){this.firstLoad=false},this,{single:true});this.gridStore.load();this.gridPanel=new GO.grid.GridPanel({region:"center",layout:"fit",split:true,paging:true,store:this.gridStore,columns:[{header:GO.lang.strName,dataIndex:"grid_display",sortable:true},{header:GO.lang.strType,dataIndex:"type",sortable:false}],view:new Ext.grid.GridView({autoFill:true,forceFit:true}),sm:new Ext.grid.RowSelectionModel(),loadMask:true,tbar:[{iconCls:"btn-add",text:GO.lang.cmdAdd,cls:"x-btn-text-icon",scope:this,handler:function(){this.showTemplate()}},{iconCls:"btn-delete",text:GO.lang.cmdDelete,cls:"x-btn-text-icon",scope:this,handler:function(){this.gridPanel.deleteSelected()}}]});this.gridPanel.on("rowdblclick",function(B){this.showTemplate(B.selModel.selections.keys[0])},this);GO.files.TemplateWindow.superclass.constructor.call(this,{title:"Templates",layout:"fit",width:500,height:400,closeAction:"hide",items:this.gridPanel,buttons:[{text:GO.lang.cmdClose,handler:function(){this.hide()},scope:this}]})};Ext.extend(GO.files.TemplateWindow,Ext.Window,{firstLoad:true,showTemplate:function(A){if(!this.templateDialog){this.uploadFile=new GO.form.UploadFile({inputName:"file",max:1});this.downloadButton=new Ext.Button({handler:function(){document.location.href="download_template.php?template_id="+this.template_id},disabled:true,text:GO.files.lang.downloadTemplate,scope:this});this.formPanel=new Ext.form.FormPanel({title:GO.lang.strProperties,cls:"go-form-panel",labelWidth:85,defaultType:"textfield",fileUpload:true,defaults:{allowBlank:false},items:[{fieldLabel:GO.lang.strName,name:"name",id:"template-name",anchor:"100%"},this.selectUser=new GO.form.SelectUser({fieldLabel:GO.lang.strUser,disabled:!GO.settings.modules.email["write_permission"],allowBlank:false,anchor:"100%"}),new GO.form.HtmlComponent({html:"<br />"}),this.uploadFile,new GO.form.HtmlComponent({html:"<br />"}),this.downloadButton,]});var B=[{text:GO.lang.cmdOk,handler:function(){this.saveTemplate(true)},scope:this},{text:GO.lang.cmdApply,handler:function(){this.saveTemplate(false)},scope:this},{text:GO.lang.cmdClose,handler:function(){this.templateDialog.hide()},scope:this}];this.templateDialog=new Ext.Window({layout:"fit",modal:false,height:400,width:400,closeAction:"hide",title:GO.files.lang.template,items:[this.templateTabPanel=new Ext.TabPanel({activeTab:0,border:false,items:[this.formPanel,this.readPermissionsTab=new GO.grid.PermissionsPanel({title:GO.lang.strReadPermissions}),this.writePermissionsTab=new GO.grid.PermissionsPanel({title:GO.lang.strWritePermissions})]})],buttons:B,keys:[{key:Ext.EventObject.ESC,fn:function(){this.savetemplate(true)},scope:this,key:Ext.EventObject.ENTER,fn:function(){this.templateDialog.hide();this.formPanel.remove(this.importfileInput)},scope:this}],focus:function(){Ext.get("template-name").focus()}})}this.template_id=A;this.templateTabPanel.setActiveTab(0);if(this.template_id>0){this.readPermissionsTab.setDisabled(false);this.writePermissionsTab.setDisabled(false);this.loadTemplate()}else{this.formPanel.form.reset();this.readPermissionsTab.setAcl(0);this.writePermissionsTab.setAcl(0);this.downloadButton.setDisabled(true)}this.templateDialog.show()},loadTemplate:function(){this.formPanel.form.load({url:GO.settings.modules.files.url+"json.php",params:{template_id:this.template_id,task:"template"},success:function(A,B){this.selectUser.setRemoteText(B.result.data.user_name);this.readPermissionsTab.setAcl(B.result.data.acl_read);this.writePermissionsTab.setAcl(B.result.data.acl_write);this.downloadButton.setDisabled(false)},scope:this})},saveTemplate:function(A){this.formPanel.form.submit({url:GO.settings.modules.files.url+"action.php",params:{task:"save_template",template_id:this.template_id},success:function(B,C){this.template_id=C.result.template_id;this.gridStore.reload();if(this.template_id&&!A){this.readPermissionsTab.setAcl(C.result.acl_read);this.writePermissionsTab.setAcl(C.result.acl_write)}if(A){this.templateDialog.hide()}},failure:function(B,C){if(C.failureType!="client"){Ext.MessageBox.alert(GO.lang.strError,C.result.feedback)}},scope:this})}});Ext.namespace("GO.files");GO.files.FileBrowser=function(A){if(!A){A={}}if(!A.root){A.root="root"}if(A.local_path){this.local_path=A.local_path}else{this.local_path=""}this.treePanel=new Ext.tree.TreePanel({region:"west",title:GO.lang.locations,layout:"fit",split:true,autoScroll:true,width:200,animate:true,loader:new Ext.tree.TreeLoader({dataUrl:GO.settings.modules.files.url+"json.php",baseParams:{task:"tree",local_path:this.local_path},preloadChildren:true}),collapsed:A.treeCollapsed,containerScroll:true,rootVisible:A.treeRootVisible,collapsible:true,ddAppendOnly:true,containerScroll:true,ddGroup:"FilesDD",enableDD:true});this.rootNode=new Ext.tree.AsyncTreeNode({text:GO.lang.folders,draggable:false,id:A.root,iconCls:"folder-default"});this.treePanel.setRootNode(this.rootNode);this.treePanel.on("click",function(B){this.setPath(B.id,true)},this);this.treePanel.on("contextmenu",function(B,D){D.stopEvent();this.contextTreePath=B.id;var C=D.getXY();this.filesContextMenu.showAt([C[0],C[1]],"folder","tree")},this);this.treePanel.on("beforenodedrop",function(D){if(D.data.selections){var C=D.data.selections}else{var B={};B.data={};B.data.extension="folder";B.data.path=D.data.node.id;var C=[B]}this.paste("cut",D.target.id,C)},this);this.gridStore=new GO.data.JsonStore({url:GO.settings.modules.files.url+"json.php",baseParams:{task:"grid",local_path:this.local_path},root:"results",totalProperty:"total",id:"path",fields:["path","name","type","size","mtime","grid_display","extension"],remoteSort:true});if(A.filesFilter){this.gridStore.baseParams.files_filter=A.filesFilter}this.gridStore.on("load",function(C){this.setWritePermission(C.reader.jsonData.write_permission);var B=this.path.lastIndexOf("/");this.parentPath=this.path.substr(0,B);if(this.parentPath=="users"||this.path==this.rootNode.id){this.upButton.setDisabled(true)}else{this.upButton.setDisabled(false)}},this);this.gridPanel=new GO.grid.GridPanel({region:"center",layout:"fit",split:true,store:this.gridStore,deleteConfig:{scope:this,success:function(){var B=this.treePanel.getNodeById(this.path);if(B){B.reload()}}},columns:[{header:GO.lang.strName,dataIndex:"grid_display",sortable:true},{header:"Type",dataIndex:"type",sortable:true},{header:"Size",dataIndex:"size",sortable:true},{header:"Modified at",dataIndex:"mtime",sortable:true}],view:new Ext.grid.GridView({autoFill:true,forceFit:true}),sm:new Ext.grid.RowSelectionModel(),loadMask:true,enableDragDrop:true,ddGroup:"FilesDD"});this.gridPanel.on("render",function(){var B=new Ext.dd.DropTarget(this.gridPanel.getView().mainBody,{ddGroup:"FilesDD",copy:false,notifyOver:this.onGridNotifyOver,notifyDrop:this.onGridNotifyDrop.createDelegate(this)})},this);this.gridPanel.on("rowdblclick",this.onGridDoubleClick,this);this.filesContextMenu=new GO.files.FilesContextMenu();this.filesContextMenu.on("properties",function(C,B){if(B=="tree"){this.showFolderPropertiesDialog(this.contextTreePath)}else{this.showGridPropertiesDialog()}},this);this.filesContextMenu.on("cut",function(C,B){this.onCutCopy("cut",B)},this);this.filesContextMenu.on("copy",function(C,B){this.onCutCopy("copy",B)},this);this.filesContextMenu.on("delete",function(C,B){this.onDelete(B)},this);this.filesContextMenu.on("compress",function(C,B){this.onCompress(B)},this);this.filesContextMenu.on("decompress",function(C,B){this.onDecompress(B)},this);this.filesContextMenu.on("download",function(){var B=this.gridPanel.getSelectionModel();var C=B.getSelected();window.location.href=GO.settings.modules.files.url+"download.php?mode=download&path="+C.data.path},this);this.filesContextMenu.on("gota",function(){var B=this.gridPanel.getSelectionModel();var C=B.getSelected();if(!deployJava.isWebStartInstalled("1.6.0")){Ext.MessageBox.alert(GO.lang.strError,GO.lang.noJava)}else{window.location.href=GO.settings.modules.gota.url+"jnlp.php?path="+C.data.path}},this);this.gridPanel.on("rowcontextmenu",this.onGridRowContextMenu,this);this.newMenu=new Ext.menu.Menu({id:"new-menu",items:[]});this.newButton=new Ext.Button({text:GO.lang.cmdNew,iconCls:"btn-add",menu:this.newMenu});this.locationTextField=new Ext.form.TextField({fieldLabel:GO.lang.strLocation,name:"files-location",anchor:"100%"});this.locationPanel=new Ext.Panel({region:"north",layout:"form",border:false,autoHeight:true,labelWidth:75,plain:true,cls:"go-files-location-panel",items:this.locationTextField});this.upButton=new Ext.Button({iconCls:"btn-up",text:GO.lang.up,cls:"x-btn-text-icon",handler:function(){this.setPath(this.parentPath)},scope:this,disabled:true});this.pasteButton=new Ext.Button({iconCls:"btn-paste",text:GO.lang.paste,cls:"x-btn-text-icon",handler:this.onPaste,scope:this,disabled:true});this.uploadButton=new Ext.Button({iconCls:"btn-upload",text:GO.lang.upload,cls:"x-btn-text-icon",handler:function(){this.showUploadDialog()},scope:this});this.deleteButton=new Ext.Button({iconCls:"btn-delete",text:GO.lang.cmdDelete,cls:"x-btn-text-icon",handler:function(){this.onDelete("grid")},scope:this});this.cutButton=new Ext.Button({iconCls:"btn-cut",text:GO.lang.cut,cls:"x-btn-text-icon",handler:function(){this.onCutCopy("cut","grid")},scope:this});this.copyButton=new Ext.Button({iconCls:"btn-copy",text:GO.lang.copy,cls:"x-btn-text-icon",handler:function(){this.onCutCopy("copy","grid")},scope:this});A.layout="border";A.tbar=new Ext.Toolbar({cls:"go-head-tb",items:[this.newButton,this.uploadButton,new Ext.Toolbar.Separator(),this.upButton,new Ext.Toolbar.Separator(),this.copyButton,this.cutButton,this.pasteButton,new Ext.Toolbar.Separator(),this.deleteButton]});A.items=[this.locationPanel,this.treePanel,this.gridPanel];GO.files.FileBrowser.superclass.constructor.call(this,A)};Ext.extend(GO.files.FileBrowser,Ext.Panel,{fileClickHandler:false,scope:this,pasteSelections:Array(),pasteMode:"cut",onShow:function(){GO.files.FileBrowser.superclass.onShow.call(this);if(!this.loaded){this.loadFiles()}},afterRender:function(){GO.files.FileBrowser.superclass.afterRender.call(this);this.loadFiles()},loadFiles:function(){this.buildNewMenu();this.setRootNode(this.root);this.loaded=true},setRootPath:function(A,B){this.root=A;this.loaded=false;if(B){this.loadFiles()}},setRootNode:function(A){this.rootNode.id=A;this.rootNode.attributes.id=A;delete this.rootNode.children;this.rootNode.expanded=false;this.rootNode.childrenRendered=false;if(A=="root"){this.rootNode.on("load",function(B){if(B.childNodes[0]){var C=B.childNodes[0];this.setPath(C.id)}},this,{single:true})}else{this.setPath(A)}this.rootNode.reload()},showFolderPropertiesDialog:function(A){if(!this.folderPropertiesDialog){this.folderPropertiesDialog=new GO.files.FolderPropertiesDialog({local_path:this.local_path});this.folderPropertiesDialog.on("rename",function(){this.reload()},this)}this.folderPropertiesDialog.show(A)},showFilePropertiesDialog:function(A){this.filePropertiesDialog=new GO.files.FilePropertiesDialog({local_path:this.local_path});this.filePropertiesDialog.on("rename",function(){this.gridStore.load()},this);this.filePropertiesDialog.show(A)},buildNewMenu:function(){this.newMenu.removeAll();Ext.Ajax.request({url:GO.settings.modules.files.url+"json.php",params:{task:"templates"},callback:function(B,G,A){if(!G){Ext.MessageBox.alert(GO.lang.strError,GO.lang.strRequestError)}else{this.newMenu.add({iconCls:"btn-add-folder",text:GO.lang.folder,cls:"x-btn-text-icon",handler:this.promptNewFolder,scope:this});var F=Ext.decode(A.responseText);if(F.results.length){this.newMenu.add("-");for(var C=0;C<F.results.length;C++){var E=F.results[C];var D=new Ext.menu.Item({iconCls:"filetype filetype-"+E.extension,text:E.name,template_id:E.id,handler:function(H){this.createFileFromTemplate(H.template_id)},scope:this});this.newMenu.add(D)}}if(GO.settings.modules.files.write_permission){this.newMenu.add("-");this.newMenu.add({iconCls:"btn-templates",text:GO.files.lang.manageTemplates,cls:"x-btn-text-icon",handler:function(){if(!this.templatesWindow){this.templatesWindow=new GO.files.TemplateWindow();this.templatesWindow.gridStore.on("datachanged",function(){if(!this.templatesWindow.firstLoad){this.buildNewMenu()}},this)}this.templatesWindow.show()},scope:this})}}},scope:this})},createFileFromTemplate:function(A,B){if(!B||B==""){Ext.Msg.prompt(GO.files.lang.enterName,GO.files.lang.pleaseEnterName,function(D,C){this.createFileFromTemplate(A,C)},this)}else{this.gridStore.baseParams.template_id=A;this.gridStore.baseParams.template_name=B;this.gridStore.load({callback:function(){if(this.gridStore.reader.jsonData.new_path){GO.files.openFile(this.gridStore.reader.jsonData.new_path)}},scope:this});delete this.gridStore.baseParams.template_id;delete this.gridStore.baseParams.template_name}},onDecompress:function(A){var B=this.gridPanel.selModel.selections.keys;if(B.length){this.gridStore.baseParams.decompress_sources=Ext.encode(B);this.gridStore.load({callback:function(){if(!this.gridStore.reader.jsonData.decompress_success){Ext.Msg.alert(GO.lang.strError,this.gridStore.reader.jsonData.decompress_feedback)}},scope:this});delete this.gridStore.baseParams.decompress_sources}},onCompress:function(A,B){var C=this.gridPanel.selModel.selections.keys;if(C.length){if(!B||B==""){Ext.Msg.prompt(GO.files.lang.enterName,GO.files.lang.pleaseEnterNameArchive,function(E,D){this.onCompress(A,D)},this)}else{this.gridStore.baseParams.compress_sources=Ext.encode(C);this.gridStore.baseParams.archive_name=B;this.gridStore.load({callback:function(){if(!this.gridStore.reader.jsonData.compress_success){Ext.Msg.alert(GO.lang.strError,this.gridStore.reader.jsonData.compress_feedback)}},scope:this});delete this.gridStore.baseParams.archive_name}}},onCutCopy:function(D,A){if(A=="tree"){var B={};B.data={};B.data.extension="folder";B.data.path=this.contextTreePath;this.pasteSelections=[B]}else{var C=this.gridPanel.getSelectionModel();this.pasteSelections=C.getSelections()}this.pasteMode=D;if(this.pasteSelections.length){this.pasteButton.setDisabled(false)}},onPaste:function(){this.paste(this.pasteMode,this.path,this.pasteSelections)},onDelete:function(A){if(A=="tree"){GO.deleteItems({url:GO.settings.modules.files.url+"action.php",params:{task:"delete",local_path:this.local_path,path:this.contextTreePath},count:1,callback:function(C){if(C.success){var B=this.treePanel.getNodeById(this.contextTreePath);if(B){if(this.path.indexOf(this.contextTreePath)>-1||(B.parentNode&&B.parentNode.id==this.path)){this.setPath(B.parentNode.id)}B.remove()}}else{Ext.MessageBox.alert(GO.lang.strError,C.feedback)}},scope:this})}else{this.gridPanel.deleteSelected({callback:function(){var B=this.treePanel.getNodeById(this.path);if(B){while(B.attributes.notreloadable){B=B.parentNode}B.reload()}},scope:this})}},onGridNotifyOver:function(A,E,C){var D=A.getDragData(E);if(C.grid){var B=C.grid.store.data.items[D.rowIndex];if(B){if(B.data.extension=="folder"){return this.dropAllowed}}}return false},onGridNotifyDrop:function(A,F,D){if(D.grid){var G=D.grid.getSelectionModel();var C=G.getSelections();var E=A.getDragData(F);var B=D.grid.store.data.items[E.rowIndex];if(B.data.extension=="folder"){this.paste("cut",B.data.path,D.selections)}}else{return false}},onGridRowContextMenu:function(B,G,E){E.stopEvent();var A=B.getSelectionModel();if(A.isSelected(G)!==true){A.clearSelections();A.selectRow(G)}var F="";var C=A.getSelections();if(C.length=="1"){F=C[0].data.extension}var D=E.getXY();this.filesContextMenu.showAt([D[0],D[1]],F,"grid")},paste:function(E,A,B){var G=Array();var C=false;for(var D=0;D<B.length;D++){G.push(B[D].data.path);if(B[D].data.extension=="folder"){C=true}}var F={task:"paste",paste_sources:Ext.encode(G),paste_destination:A,paste_mode:E,path:this.path};this.sendOverwrite(F)},showUploadDialog:function(){if(!this.uploadDialog){this.uploadFile=new GO.form.UploadFile({inputName:"attachments",addText:GO.lang.smallUpload});this.upForm=new Ext.form.FormPanel({fileUpload:true,items:[this.uploadFile,new Ext.Button({text:GO.lang.largeUpload,handler:function(){if(!deployJava.isWebStartInstalled("1.6.0")){Ext.MessageBox.alert(GO.lang.strError,GO.lang.noJava)}else{var A=this.local_path?"true":false;GO.util.popup(GO.settings.modules.files.url+"jupload/index.php?path="+escape(this.path)+"&local_path="+A,"640","500");this.uploadDialog.hide();GO.currentFilesStore=this.gridStore}},scope:this})],cls:"go-form-panel"});this.uploadDialog=new Ext.Window({title:GO.lang.uploadFiles,layout:"fit",modal:false,height:300,width:300,items:this.upForm,buttons:[{text:GO.files.lang.startTransfer,handler:this.uploadHandler,scope:this},{text:GO.lang.cmdClose,handler:function(){this.uploadDialog.hide()},scope:this}]})}this.uploadDialog.show()},uploadHandler:function(){this.upForm.container.mask(GO.lang.waitMsgUpload,"x-mask-loading");this.upForm.form.submit({url:GO.settings.modules.files.url+"action.php",params:{task:"upload",path:this.path,local_path:this.local_path},success:function(A,B){this.uploadFile.clearQueue();this.uploadDialog.hide();this.sendOverwrite({path:this.path,task:"overwrite",local_path:this.local_path});this.upForm.container.unmask()},failure:function(A,B){this.upForm.container.unmask()},scope:this})},sendOverwrite:function(A){if(!A.command){A.command="ask"}this.overwriteParams=A;Ext.Ajax.request({url:GO.settings.modules.files.url+"action.php",params:this.overwriteParams,callback:function(C,F,B){delete A.paste_sources;delete A.paste_destination;if(!F){Ext.MessageBox.alert(GO.lang.strError,GO.lang.strRequestError)}else{var E=Ext.decode(B.responseText);if(!E.success&&!E.file_exists){Ext.MessageBox.alert(GO.lang.strError,E.feedback)}else{if(E.file_exists){if(!this.overwriteDialog){this.overwriteDialog=new Ext.Window({width:500,autoHeight:true,closeable:false,closeAction:"hide",plain:true,border:false,title:GO.lang.fileExists,modal:false,buttons:[{text:GO.lang.cmdYes,handler:function(){this.overwriteParams.command="yes";this.sendOverwrite(this.overwriteParams)},scope:this},{text:GO.lang.cmdYesToAll,handler:function(){this.overwriteParams.command="yestoall";this.sendOverwrite(this.overwriteParams)},scope:this},{text:GO.lang.cmdNo,handler:function(){this.overwriteParams.command="no";this.sendOverwrite(this.overwriteParams)},scope:this},{text:GO.lang.cmdNoToAll,handler:function(){this.overwriteParams.command="notoall";this.sendOverwrite(this.overwriteParams)},scope:this},{text:GO.lang.cmdCancel,handler:function(){this.gridStore.reload();this.overwriteDialog.hide()},scope:this}]});this.overwriteDialog.render(Ext.getBody())}var D=new Ext.Template(GO.lang.overwriteFile);D.overwrite(this.overwriteDialog.body,{file:E.file_exists});this.overwriteDialog.show()}else{this.gridStore.reload();this.treePanel.getRootNode().reload();if(this.overwriteDialog){this.overwriteDialog.hide()}}}}},scope:this})},promptNewFolder:function(){if(!this.newFolderWindow){this.newFolderNameField=new Ext.form.TextField({id:"new-folder-input",fieldLabel:GO.lang.strName,name:"name",value:"New folder",allowBlank:false,anchor:"100%"});this.newFolderFormPanel=new Ext.form.FormPanel({url:GO.settings.modules.files.url+"action.php",defaultType:"textfield",labelWidth:75,cls:"go-form-panel",items:this.newFolderNameField});this.newFolderWindow=new Ext.Window({title:GO.files.lang.addFolder,width:500,autoHeight:true,modal:false,closeAction:"hide",items:this.newFolderFormPanel,focus:function(){Ext.getCmp("new-folder-input").focus(true)},scope:this,buttons:[{text:GO.lang.cmdOk,handler:function(){this.newFolderFormPanel.form.submit({url:GO.settings.modules.files.url+"action.php",params:{task:"new_folder",path:this.path,local_path:this.local_path},waitMsg:GO.lang.waitMsgSave,success:function(A,B){this.gridStore.reload();var D=this.treePanel.getNodeById(this.path);if(D){var E=function(){var F=this.treePanel.getNodeById(this.path);if(F){F.expand()}};var C=E.createDelegate(this);if(D.parentNode){D.parentNode.reload(C)}else{D.reload(C)}}this.newFolderWindow.hide()},failure:function(B,C){var A="";if(C.failureType=="client"){A=GO.lang.strErrorsInForm}else{A=C.result.feedback}Ext.MessageBox.alert(GO.lang.strError,A)},scope:this})},scope:this},{text:GO.lang.cmdClose,handler:function(){this.newFolderWindow.hide()},scope:this}]})}else{this.newFolderNameField.reset()}this.newFolderWindow.show()},onGridDoubleClick:function(C,D,E){var A=C.getSelectionModel();var B=A.getSelected();if(B.data.extension=="folder"){this.setPath(B.data.path,true)}else{if(this.fileClickHandler){this.fileClickHandler.call(this.scope)}else{GO.files.openFile(B.data.path)}}},setWritePermission:function(A){this.newButton.setDisabled(!A);this.deleteButton.setDisabled(!A);this.uploadButton.setDisabled(!A);this.cutButton.setDisabled(!A);this.pasteButton.setDisabled(!A||!this.pasteSelections.length)},setPath:function(B,A){this.path=B;this.gridStore.baseParams.path=B;this.gridStore.load();this.locationTextField.setValue(this.path);if(A){var C=this.treePanel.getNodeById(B);if(C){C.expand()}}},reload:function(){this.gridStore.load();var A=this.treePanel.getNodeById(this.path);if(A){A.reload()}},showGridPropertiesDialog:function(){var A=this.gridPanel.getSelectionModel();var B=A.getSelections();if(B.length==0){Ext.Msg.alert(GO.lang.strError,GO.lang.noItemSelected)}else{if(B.length>1){Ext.Msg.alert(GO.lang.strError,GO.files.lang.errorOneItem)}else{if(B[0].data.extension=="folder"){this.showFolderPropertiesDialog(B[0].data.path)}else{this.showFilePropertiesDialog(B[0].data.path)}}}}});GO.files.openFile=function(A){var C=A.lastIndexOf(".");var B="";if(C){B=A.substr(C+1)}switch(B){case"doc":case"odt":case"ods":case"xls":case"ppt":case"odp":case"txt":if(GO.settings.modules.gota){if(!GO.files.noJavaNotified&&!deployJava.isWebStartInstalled("1.6.0")){GO.files.noJavaNotified=true;Ext.MessageBox.alert(GO.lang.strError,GO.lang.noJava);window.location.href=GO.settings.modules.files.url+"download.php?mode=download&path="+A}else{window.location.href=GO.settings.modules.gota.url+"jnlp.php?path="+A}}else{window.location.href=GO.settings.modules.files.url+"download.php?mode=download&path="+A}break;default:window.location.href=GO.settings.modules.files.url+"download.php?mode=download&path="+A;break}};GO.files.openFolder=function(A){if(!GO.files.fileBrowser){GO.files.fileBrowser=new GO.files.FileBrowser({border:false,treeRootVisible:true,treeCollapsed:true});GO.files.fileBrowserWin=new Ext.Window({title:GO.files.lang.fileBrowser,height:400,width:600,layout:"fit",border:false,maximizable:true,closeAction:"hide",items:GO.files.fileBrowser,buttons:[{text:GO.lang.cmdClose,handler:function(){GO.files.fileBrowserWin.hide()},scope:this}]})}GO.files.fileBrowser.setRootPath(A,true);GO.files.fileBrowserWin.show()};GO.linkHandlers[6]=function(B,A){GO.files.openFile(A.data.description)};GO.moduleManager.addModule("files",GO.files.FileBrowser,{title:GO.files.lang.files,iconCls:"go-tab-icon-files"});GO.files.filesTemplate='<tpl if="files.length"><table class="display-panel" cellpadding="0" cellspacing="0" border="0"><tr><td colspan="4" class="display-panel-heading">Files</td></tr><tr><td class="table_header_links">'+GO.lang.strName+'</a></td><td class="table_header_links">'+GO.lang.strMtime+'</td></tr><tpl for="files"><tr><tpl if="values.extension==\'folder\'"><td><a href="#" onclick="GO.files.openFolder(\'{[this.getPath(values.path)]}\');">{grid_display}</a></td></tpl><tpl if="values.extension!=\'folder\'"><td><a href="#" onclick="GO.files.openFile(\'{[this.getPath(values.path)]}\');">{grid_display}</a></td></tpl><td>{mtime}</td></tr></tpl></tpl>';GO.files.filesTemplateConfig={getPath:function(A){return A.replace(/\'/g,"\\'")}};