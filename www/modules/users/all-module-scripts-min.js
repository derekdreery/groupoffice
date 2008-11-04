GO.users.UserDialog=function(A){if(!A){A={}}this.buildForm();A.tbar=[this.linkBrowseButton=new Ext.Button({iconCls:"btn-link",cls:"x-btn-text-icon",text:GO.lang.cmdBrowseLinks,disabled:true,handler:function(){GO.linkBrowser.show({link_id:this.user_id,link_type:"8",folder_id:"0"})},scope:this})];if(GO.files){A.tbar.push(this.fileBrowseButton=new Ext.Button({iconCls:"go-menu-icon-files",cls:"x-btn-text-icon",text:GO.files.lang.files,handler:function(){GO.files.openFolder(this.files_path)},scope:this,disabled:true}))}A.layout="fit";A.modal=false;A.resizable=false;A.width=750;A.collapsible=true;A.height=400;A.closeAction="hide";A.title=GO.users.lang.userSettings;A.items=this.formPanel;A.buttons=[{text:GO.lang.cmdOk,handler:function(){this.submitForm(true)},scope:this},{text:GO.lang.cmdSavePlusNew,handler:function(){this.submitForm(false,true)},scope:this},{text:GO.lang.cmdApply,handler:function(){this.submitForm()},scope:this},{text:GO.lang.cmdClose,handler:function(){this.hide()},scope:this}];GO.users.UserDialog.superclass.constructor.call(this,A);this.addEvents({save:true})};Ext.extend(GO.users.UserDialog,Ext.Window,{files_path:"",setUserId:function(A){this.formPanel.form.baseParams.user_id=A;this.user_id=A;this.permissionsTab.setUserId(A);this.accountTab.setUserId(A);if(this.serverclientFieldSet){var B=A>0;this.serverclientFieldSet.setVisible(!B)}this.linkBrowseButton.setDisabled(A<1);if(GO.files){this.fileBrowseButton.setDisabled(A<1)}},serverclientDomainCheckboxes:[],setDefaultEmail:function(){if(this.rendered){for(var B=0;B<this.serverclientDomainCheckboxes.length;B++){if(this.serverclientDomainCheckboxes[B].getValue()){var C=this.formPanel.form.findField("username").getValue();var A=this.formPanel.form.findField("email");if(A){this.formPanel.form.findField("email").setValue(C+"@"+GO.serverclient.domains[B])}break}}}},show:function(A){if(!this.rendered){if(GO.serverclient&&GO.serverclient.domains){this.serverclientFieldSet=new Ext.form.FieldSet({title:"Mailboxes",autoHeight:true,items:new GO.form.HtmlComponent({html:'<p class="go-form-text">Create a mailbox for domain:</p>'})});for(var B=0;B<GO.serverclient.domains.length;B++){this.serverclientDomainCheckboxes[B]=new Ext.form.Checkbox({checked:(B==0),name:"serverclient_domains[]",autoCreate:{tag:"input",type:"checkbox",value:GO.serverclient.domains[B]},hideLabel:true,boxLabel:GO.serverclient.domains[B]});this.serverclientDomainCheckboxes[B].on("check",this.setDefaultEmail,this);this.serverclientFieldSet.add(this.serverclientDomainCheckboxes[B])}this.accountTab.add(this.serverclientFieldSet)}this.render(Ext.getBody())}if(GO.serverclient&&GO.serverclient.domains){this.formPanel.form.findField("username").on("change",this.setDefaultEmail,this)}this.accountTab.show();this.setUserId(A);if(A>0){this.formPanel.load({url:GO.settings.modules.users.url+"json.php",success:function(C,D){this.loaded=true;GO.users.UserDialog.superclass.show.call(this);this.files_path=D.result.data.files_path;this.lookAndFeelTab.startModuleField.setRemoteText(D.result.data.start_module_name)},failure:function(C,D){Ext.Msg.alert(GO.lang.strError,D.result.feedback)},scope:this})}else{this.formPanel.form.reset();GO.users.UserDialog.superclass.show.call(this);this.setUserId(0)}},submitForm:function(A,B){var C=this.permissionsTab.getPermissionParameters();C.task="save_user";this.formPanel.form.submit({url:GO.settings.modules.users.url+"action.php",params:C,waitMsg:GO.lang.waitMsgSave,success:function(F,G){this.fireEvent("save",this);if(A){this.hide()}else{if(B){this.setUserId(0);var D=["username","password1","password2","first_name","middle_name","last_name","title","initials","sex","birthday","address","address_no","city","zip","email","home_phone","fax","cellular","department","function"];for(var E=0;E<D.length;E++){this.formPanel.form.findField(D[E]).reset()}this.permissionsTab.onShow()}else{if(G.result.user_id){this.setUserId(G.result.user_id);this.files_path=G.result.files_path}}}},failure:function(D,E){if(E.failureType=="client"){Ext.MessageBox.alert(GO.lang.strError,GO.lang.strErrorsInForm)}else{Ext.MessageBox.alert(GO.lang.strError,E.result.feedback)}},scope:this})},buildForm:function(){this.accountTab=new GO.users.AccountPanel();this.personalTab=new GO.users.PersonalPanel();this.companyTab=new GO.users.CompanyPanel();this.loginTab=new GO.users.LoginPanel();this.permissionsTab=new GO.users.PermissionsPanel();this.regionalSettingsTab=new GO.users.RegionalSettingsPanel();this.lookAndFeelTab=new GO.users.LookAndFeelPanel();this.tabPanel=new Ext.TabPanel({deferredRender:false,anchor:"100% 100%",layoutOnTabChange:true,border:false,items:[this.accountTab,this.personalTab,this.companyTab,this.loginTab,this.permissionsTab,this.regionalSettingsTab,this.lookAndFeelTab]});this.formPanel=new Ext.form.FormPanel({items:this.tabPanel,baseParams:{task:"user"},waitMsgTarget:true,border:false})}});GO.users.PermissionsPanel=function(D){if(!D){D={}}D.autoScroll=false;D.border=false;D.hideLabel=true;D.title=GO.lang.strPermissions;D.layout="columnfit";D.anchor="100% 100%";D.defaults={border:true,height:280,autoScroll:true};var F=new GO.grid.CheckColumn({header:GO.users.lang.cmdCheckColumnRead,dataIndex:"read_permission",width:55});var H=new GO.grid.CheckColumn({header:GO.users.lang.cmdCheckColumnWrite,dataIndex:"write_permission",width:55});this.modulePermissionsStore=new GO.data.JsonStore({url:GO.settings.modules.users.url+"json.php",baseParams:{user_id:0,task:"modules"},fields:["id","name","disabled","read_permission","write_permission"],root:"results"});var C=new GO.grid.GridPanel({columnWidth:0.34,title:GO.users.lang.moduleAccess,layout:"fit",columns:[{header:GO.users.lang.cmdHeaderColumnName,dataIndex:"name",renderer:this.iconRenderer},F,H],ds:this.modulePermissionsStore,plugins:[F,H],autoExpandColumn:0});var A=new GO.grid.CheckColumn({header:"",dataIndex:"group_permission",width:55});this.groupMemberStore=new GO.data.JsonStore({url:GO.settings.modules.users.url+"json.php",baseParams:{user_id:0,task:"groups"},fields:["id","disabled","group","group_permission"],root:"results"});var B=new GO.grid.GridPanel({columnWidth:0.33,layout:"fit",title:GO.users.lang.userIsMemberOf,columns:[{header:GO.users.lang.group,dataIndex:"group"},A],ds:this.groupMemberStore,plugins:A,autoExpandColumn:0});var G=new GO.grid.CheckColumn({header:"",dataIndex:"visible_permission",width:55});this.groupVisibleStore=new GO.data.JsonStore({url:GO.settings.modules.users.url+"json.php",baseParams:{user_id:0,task:"visible"},fields:["id","disabled","group","visible_permission"],root:"results"});var E=new GO.grid.GridPanel({columnWidth:0.33,layout:"fit",title:GO.users.lang.userVisibleTo,columns:[{header:GO.users.lang.group,dataIndex:"group"},G],ds:this.groupVisibleStore,plugins:G,autoExpandColumn:0});D.items=[C,B,E];GO.users.PermissionsPanel.superclass.constructor.call(this,D)};Ext.extend(GO.users.PermissionsPanel,Ext.Panel,{iconRenderer:function(C,B,A){return'<div class="go-module-icon-'+A.data.id+'" style="height:16px;padding-left:22px;background-repeat:no-repeat;">'+C+"</div>"},setUserId:function(A){this.user_id=A},onShow:function(){GO.users.PermissionsPanel.superclass.onShow.call(this);this.modulePermissionsStore.baseParams.user_id=this.user_id;this.groupMemberStore.baseParams.user_id=this.user_id;this.groupVisibleStore.baseParams.user_id=this.user_id;this.groupMemberStore.load();this.modulePermissionsStore.load();this.groupVisibleStore.load()},getPermissionParameters:function(){var D=new Array();var A=new Array();var B=new Array();for(var C=0;C<this.modulePermissionsStore.data.items.length;C++){D[C]={id:this.modulePermissionsStore.data.items[C].get("id"),name:this.modulePermissionsStore.data.items[C].get("name"),read_permission:this.modulePermissionsStore.data.items[C].get("read_permission"),write_permission:this.modulePermissionsStore.data.items[C].get("write_permission")}}for(var C=0;C<this.groupMemberStore.data.items.length;C++){A[C]={id:this.groupMemberStore.data.items[C].get("id"),group:this.groupMemberStore.data.items[C].get("name"),group_permission:this.groupMemberStore.data.items[C].get("group_permission")}}for(var C=0;C<this.groupVisibleStore.data.items.length;C++){B[C]={id:this.groupVisibleStore.data.items[C].get("id"),group:this.groupVisibleStore.data.items[C].get("name"),visible_permission:this.groupVisibleStore.data.items[C].get("visible_permission")}}return{modules:Ext.encode(D),groups_visible:Ext.encode(B),group_member:Ext.encode(A)}}});GO.users.LoginPanel=function(A){if(!A){A={}}A.autoScroll=true;A.border=false;A.hideLabel=true;A.title=GO.users.lang.loginInfo;A.layout="form";A.defaults={anchor:"100%"};A.defaultType="textfield";A.cls="go-form-panel";A.labelWidth=140;A.items=[{xtype:"plainfield",fieldLabel:GO.users.lang.cmdFormLabelRegistrationTime,name:"registration_time"},{xtype:"plainfield",fieldLabel:GO.users.lang.cmdFormLabelLastLogin,name:"lastlogin"},{xtype:"plainfield",fieldLabel:GO.users.lang.numberOfLogins,name:"logins"}];GO.users.LoginPanel.superclass.constructor.call(this,A)};Ext.extend(GO.users.LoginPanel,Ext.Panel,{});GO.users.PersonalPanel=function(B){if(!B){B={}}B.autoScroll=true;B.border=false;B.hideLabel=true;B.title=GO.users.lang.profile;B.layout="column";B.cls="go-form-panel";B.labelWidth=120;B.height=600;var A=[{fieldLabel:GO.lang.strAddress,name:"address"},{fieldLabel:GO.lang.strAddressNo,name:"address_no"},{fieldLabel:GO.lang.strZip,name:"zip"},{fieldLabel:GO.lang.strCity,name:"city"},{fieldLabel:GO.lang.strState,name:"state"},new GO.form.SelectCountry({fieldLabel:GO.lang.strCountry,id:"countryCombo",hiddenName:"country",value:GO.settings.country}),new GO.form.HtmlComponent({html:"<br />"})];A.push({fieldLabel:GO.lang.strEmail,name:"email",allowBlank:false});A.push({fieldLabel:GO.lang.strPhone,name:"home_phone"});A.push({fieldLabel:GO.lang.strFax,name:"fax"});A.push({fieldLabel:GO.lang.strCellular,name:"cellular"});B.items=[{columnWidth:0.5,layout:"form",border:false,cls:"go-form-panel",waitMsgTarget:true,defaults:{anchor:"100%"},defaultType:"textfield",items:[{fieldLabel:GO.lang.strFirstName,name:"first_name",allowBlank:false},{fieldLabel:GO.lang.strMiddleName,name:"middle_name"},{fieldLabel:GO.lang.strLastName,name:"last_name",allowBlank:false},new GO.form.HtmlComponent({html:"<br />"}),{fieldLabel:GO.lang.strTitle,name:"title"},{fieldLabel:GO.lang.strInitials,name:"initials"},new Ext.form.ComboBox({fieldLabel:GO.lang.strSex,hiddenName:"sex",store:new Ext.data.SimpleStore({fields:["value","text"],data:[["M",GO.lang.strMale],["F",GO.lang.strFemale]]}),value:"M",valueField:"value",displayField:"text",mode:"local",triggerAction:"all",editable:false,selectOnFocus:true,forceSelection:true}),new Ext.form.DateField({fieldLabel:GO.lang.strBirthday,name:"birthday",format:GO.settings.date_format})]},{columnWidth:0.5,layout:"form",border:false,cls:"go-form-panel",waitMsgTarget:true,defaults:{anchor:"100%",allowBlank:true},defaultType:"textfield",items:A}];GO.users.PersonalPanel.superclass.constructor.call(this,B)};Ext.extend(GO.users.PersonalPanel,Ext.Panel,{});GO.users.CompanyPanel=function(A){if(!A){A={}}A.autoScroll=true;A.border=false;A.hideLabel=true;A.title=GO.users.lang.companyProfile;A.layout="column";A.cls="go-form-panel";A.labelWidth=120;A.items=[{columnWidth:0.5,layout:"form",border:false,cls:"go-form-panel",waitMsgTarget:true,defaults:{anchor:"100%"},defaultType:"textfield",items:[{fieldLabel:GO.lang.strCompany,name:"company"},{fieldLabel:GO.lang.strDepartment,name:"department"},{fieldLabel:GO.lang.strFunction,name:"function"},{fieldLabel:GO.lang.strWorkAddress,name:"work_address"},{fieldLabel:GO.lang.strWorkAddressNo,name:"work_address_no"},{fieldLabel:GO.lang.strWorkZip,name:"work_zip"}]},{columnWidth:0.5,layout:"form",border:false,cls:"go-form-panel",waitMsgTarget:true,defaults:{anchor:"100%"},defaultType:"textfield",items:[{fieldLabel:GO.lang.strWorkCity,name:"work_city"},{fieldLabel:GO.lang.strWorkState,name:"work_state"},new GO.form.SelectCountry({fieldLabel:GO.lang.strWorkCountry,id:"work_countryCombo",hiddenName:"work_country",value:GO.settings.country}),{fieldLabel:GO.lang.strWorkPhone,name:"work_phone"},{fieldLabel:GO.lang.strWorkFax,name:"work_fax"},{fieldLabel:GO.users.lang.cmdFormLabelHomepage,name:"homepage"}]}];GO.users.CompanyPanel.superclass.constructor.call(this,A)};Ext.extend(GO.users.CompanyPanel,Ext.Panel,{});GO.users.AccountPanel=function(A){if(!A){A={}}A.autoScroll=true;A.border=false;A.hideLabel=true;A.title=GO.users.lang.account;A.layout="form";A.defaults={anchor:"100%"};A.defaultType="textfield";A.cls="go-form-panel";A.labelWidth=140;this.passwordField1=new Ext.form.TextField({inputType:"password",fieldLabel:GO.users.lang.cmdFormLabelPassword,name:"password1"});this.passwordField2=new Ext.form.TextField({inputType:"password",fieldLabel:GO.users.lang.confirmPassword,name:"password2"});this.usernameField=new Ext.form.TextField({fieldLabel:GO.lang.strUsername,name:"username"});this.enabledField=new Ext.form.Checkbox({boxLabel:GO.users.lang.cmdBoxLabelEnabled,name:"enabled",checked:true,hideLabel:true});A.items=[this.usernameField,this.passwordField1,this.passwordField2,this.enabledField];GO.users.AccountPanel.superclass.constructor.call(this,A)};Ext.extend(GO.users.AccountPanel,Ext.Panel,{setUserId:function(A){this.usernameField.setDisabled(A>0);this.passwordField2.allowBlank=(A>0);this.passwordField1.allowBlank=(A>0)}});GO.users.ImportDialog=Ext.extend(Ext.Window,{initComponent:function(){this.title=GO.lang.cmdImport;this.width=500;this.autoHeight=true;this.closeAction="hide";this.uploadFile=new GO.form.UploadFile({inputName:"importfile",max:1});this.upForm=new Ext.form.FormPanel({fileUpload:true,waitMsgTarget:true,items:[new GO.form.HtmlComponent({html:GO.users.lang.importText+"<br /><br />"}),this.uploadFile],cls:"go-form-panel"});this.items=[this.upForm];this.buttons=[{text:GO.lang.cmdOk,handler:this.uploadHandler,scope:this},{text:GO.lang.cmdClose,handler:function(){this.hide()},scope:this},{text:GO.users.lang.downloadSampleCSV,handler:function(){window.open(GO.settings.modules.users.url+"importsample.csv")},scope:this}];this.addEvents({"import":true});GO.users.ImportDialog.superclass.initComponent.call(this)},uploadHandler:function(){this.upForm.form.submit({waitMsg:GO.lang.waitMsgUpload,url:GO.settings.modules.users.url+"action.php",params:{task:"import"},success:function(B,C){this.uploadFile.clearQueue();this.hide();this.fireEvent("import");var A=C.result.feedback.replace(/BR/g,"<br />");Ext.MessageBox.alert(GO.lang.strSuccess,A)},failure:function(B,C){if(C.failureType=="client"){Ext.MessageBox.alert(GO.lang.strError,GO.lang.strErrorsInForm)}else{var A=C.result.feedback.replace(/BR/g,"<br />");Ext.MessageBox.alert(GO.lang.strError,A)}},scope:this})}});GO.users.MainPanel=function(A){if(!A){A={}}A.store=new GO.data.JsonStore({url:GO.settings.modules.users.url+"json.php",baseParams:{task:"users"},id:"id",totalProperty:"total",root:"results",fields:["id","username","name","company","logins","lastlogin","registration_time","address","zip","city","state","country","phone","email","waddress","wzip","wcity","wstate","wcountry","wphone"],remoteSort:true});A.store.setDefaultSort("username","ASC");this.searchField=new GO.form.SearchField({store:A.store,width:320});A.view=new Ext.grid.GridView({forceFit:true,autoFill:true});A.cm=new Ext.grid.ColumnModel([{header:GO.lang.strUsername,dataIndex:"username"},{header:GO.lang.strName,dataIndex:"name",width:250},{header:GO.lang.strCompany,dataIndex:"company",width:200},{header:GO.users.lang.cmdFormLabelTotalLogins,dataIndex:"logins",width:100},{header:GO.users.lang.cmdFormLabelLastLogin,dataIndex:"lastlogin",width:100},{header:GO.users.lang.cmdFormLabelRegistrationTime,dataIndex:"registration_time",width:100},{header:GO.lang.strAddress,dataIndex:"address",width:100,hidden:true},{header:GO.lang.strZip,dataIndex:"zip",width:100,hidden:true},{header:GO.lang.strCity,dataIndex:"city",width:100,hidden:true},{header:GO.lang.strState,dataIndex:"state",width:100,hidden:true},{header:GO.lang.strCountry,dataIndex:"country",width:100,hidden:true},{header:GO.lang.strPhone,dataIndex:"phone",width:100,hidden:true},{header:GO.lang.strEmail,dataIndex:"email",width:100,hidden:true},{header:GO.lang.strWorkAddress,dataIndex:"waddress",width:100,hidden:true},{header:GO.lang.strWorkZip,dataIndex:"wzip",width:100,hidden:true},{header:GO.lang.strWorkCity,dataIndex:"wcity",width:100,hidden:true},{header:GO.lang.strWorkState,dataIndex:"wstate",width:100,hidden:true},{header:GO.lang.strWorkCountry,dataIndex:"wcountry",width:100,hidden:true},{header:GO.lang.strWorkPhone,dataIndex:"wphone",width:100,hidden:true}]);A.cm.defaultSortable=true;A.tbar=new Ext.Toolbar({cls:"go-head-tb",items:[{iconCls:"btn-add",text:GO.lang.cmdAdd,cls:"x-btn-text-icon",handler:function(){if(GO.settings.config.max_users>0&&this.store.totalLength>=GO.settings.config.max_users){Ext.Msg.alert(GO.lang.strError,GO.users.lang.maxUsersReached)}else{GO.users.userDialog.show()}},scope:this},{iconCls:"btn-delete",text:GO.lang.cmdDelete,cls:"x-btn-text-icon",handler:this.deleteSelected,scope:this},{iconCls:"btn-upload",text:GO.lang.cmdImport,handler:function(){if(!this.importDialog){this.importDialog=new GO.users.ImportDialog();this.importDialog.on("import",function(){this.store.reload()},this)}this.importDialog.show()},scope:this},"-",GO.lang.strSearch+":",this.searchField]});if(GO.settings.config.max_users>0){A.bbar=new Ext.PagingToolbar({store:A.store,pageSize:parseInt(GO.settings.max_rows_list),displayInfo:true,displayMsg:GO.lang.displayingItems+". "+GO.lang.strMax+" "+GO.settings.config.max_users,emptyMsg:GO.lang.strNoItems})}A.sm=new Ext.grid.RowSelectionModel();A.paging=true;GO.users.MainPanel.superclass.constructor.call(this,A)};Ext.extend(GO.users.MainPanel,GO.grid.GridPanel,{afterRender:function(){GO.users.MainPanel.superclass.afterRender.call(this);this.on("rowdblclick",this.rowDoubleClick,this);this.store.load();if(!GO.users.userDialog.hasListener("save")){GO.users.userDialog.on("save",function(){this.store.reload()},this)}},rowDoubleClick:function(C,E,D){var A=C.getSelectionModel();var B=A.getSelected();GO.users.userDialog.show(B.data.id)}});GO.moduleManager.addAdminModule("users",GO.users.MainPanel,{title:GO.lang.users,iconCls:"go-tab-icon-users",closable:true});GO.mainLayout.onReady(function(){GO.users.userDialog=new GO.users.UserDialog()});GO.linkHandlers[8]=function(A){GO.users.userDialog.show(A)};