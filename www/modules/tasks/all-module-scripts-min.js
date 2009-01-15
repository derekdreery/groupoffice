GO.tasks.TaskPanel=Ext.extend(GO.DisplayPanel,{link_type:12,loadParams:{task:"task_with_items"},idParam:"task_id",loadUrl:GO.settings.modules.tasks.url+"json.php",editHandler:function(){if(!GO.tasks.taskDialog){GO.tasks.taskDialog=new GO.tasks.TaskDialog()}this.addSaveHandler(GO.tasks.taskDialog);GO.tasks.taskDialog.show({task_id:this.data.id})},initComponent:function(){this.template='<div><table class="display-panel" cellpadding="0" cellspacing="0" border="0"><tr><td colspan="2" class="display-panel-heading">{name}</td></tr><tr><td>'+GO.tasks.lang.startsAt+":</td><td>{start_date}</td></tr><tr><td>"+GO.tasks.lang.dueAt+":</td><td>{due_date}</td></tr><tr><td>"+GO.lang.strStatus+':</td><td>{status_text}</td></tr><tpl if="this.notEmpty(description)"><tr><td colspan="2" class="display-panel-heading">'+GO.lang.strDescription+'</td></tr><tr><td colspan="2">{description}</td></tr></tpl></table>';this.template+=GO.linksTemplate;if(GO.files){Ext.apply(this.templateConfig,GO.files.filesTemplateConfig);this.template+=GO.files.filesTemplate}Ext.apply(this.templateConfig,GO.linksTemplateConfig);if(GO.comments){this.template+=GO.comments.displayPanelTemplate}GO.tasks.TaskPanel.superclass.initComponent.call(this)},loadTask:function(A){this.body.mask(GO.lang.waitMsgLoad);Ext.Ajax.request({url:GO.settings.modules.tasks.url+"json.php",params:{task:"task_with_items",task_id:A},callback:function(C,E,B){this.body.unmask();if(!E){Ext.MessageBox.alert(GO.lang.strError,GO.lang.strRequestError)}else{var D=Ext.decode(B.responseText);this.setData(D.data)}},scope:this})}});GO.tasks.ScheduleCallDialog=function(B){if(!B){B={}}this.buildForm();var A=function(){this.formPanel.items.items[0].focus()};B.layout="fit";B.modal=false;B.width=500;B.autoHeight=true;B.closeAction="hide";B.title=GO.tasks.lang.scheduleCall;B.items=this.formPanel;B.focus=A.createDelegate(this);B.buttons=[{text:GO.lang.cmdOk,handler:function(){this.submitForm()},scope:this},{text:GO.lang.cmdClose,handler:function(){this.hide()},scope:this}];GO.tasks.ScheduleCallDialog.superclass.constructor.call(this,B);this.addEvents({save:true})};Ext.extend(GO.tasks.ScheduleCallDialog,Ext.Window,{link_config:{},show:function(A){this.linkConfig=A;if(!this.rendered){this.render(Ext.getBody())}this.formPanel.form.reset();this.selectTaskList.setValue(GO.tasks.defaultTasklist.id);this.selectTaskList.setRemoteText(GO.tasks.defaultTasklist.name);GO.tasks.ScheduleCallDialog.superclass.show.call(this)},submitForm:function(){this.formPanel.form.submit({url:GO.settings.modules.tasks.url+"action.php",params:{task:"schedule_call",links:Ext.encode(this.linkConfig.links),name:GO.tasks.lang.call+": "+this.linkConfig.name},waitMsg:GO.lang.waitMsgSave,success:function(A,B){if(this.linkConfig.callback){this.linkConfig.callback.call(this.linkConfig.scope)}this.fireEvent("save",this);this.hide()},failure:function(A,B){if(B.failureType=="client"){Ext.MessageBox.alert(GO.lang.strError,GO.lang.strErrorsInForm)}else{Ext.MessageBox.alert(GO.lang.strError,B.result.feedback)}},scope:this})},buildForm:function(){var C=new Date();var A=C.add(Date.DAY,1);var B=Date.parseDate(A.format("Y-m-d")+" 08:00","Y-m-d G:i");var D=new Ext.DatePicker({xtype:"datepicker",name:"remind_date",format:GO.settings.date_format,fieldLabel:GO.lang.strDate});D.setValue(A);D.on("select",function(E,F){this.formPanel.baseParams.date=F.format(GO.settings.date_format)},this);this.formPanel=new Ext.form.FormPanel({url:GO.settings.modules.tasks.url+"action.php",border:false,baseParams:{task:"note",date:A.format(GO.settings.date_format)},cls:"go-form-panel",waitMsgTarget:true,autoHeight:true,items:[{items:D,width:220,style:"margin:auto;"},new GO.form.HtmlComponent({html:"<br />"}),{xtype:"timefield",name:"remind_time",format:GO.settings.time_format,value:B.format(GO.settings.time_format),fieldLabel:GO.lang.strTime,anchor:"100%"},{xtype:"textarea",name:"description",anchor:"100%",height:100,fieldLabel:GO.lang.strDescription},this.selectTaskList=new GO.tasks.SelectTasklist({fieldLabel:GO.tasks.lang.tasklist,anchor:"100%"})]})}});GO.tasks.ScheduleCallMenuItem=Ext.extend(Ext.menu.Item,{linkConfig:{name:"",links:[{link_id:0,link_type:0}]},initComponent:function(){this.iconCls="tasks-call";this.text=GO.tasks.lang.scheduleCall;this.cls="x-btn-text-icon";this.disabled=true;this.handler=function(){if(!GO.tasks.scheduleCallDialog){GO.tasks.scheduleCallDialog=new GO.tasks.ScheduleCallDialog()}GO.tasks.scheduleCallDialog.show(this.linkConfig)};GO.tasks.ScheduleCallMenuItem.superclass.initComponent.call(this)},setLinkConfig:function(A){this.linkConfig=A;this.setDisabled(false)}});GO.tasks.TasklistDialog=function(A){if(!A){A={}}this.propertiesTab=new Ext.form.FormPanel({waitMsgTarget:true,url:GO.settings.modules.tasks.url+"action.php",title:GO.lang.strProperties,layout:"form",anchor:"100% 100%",defaultType:"textfield",autoHeight:true,cls:"go-form-panel",waitMsgTarget:true,labelWidth:75,items:[this.selectUser=new GO.form.SelectUser({fieldLabel:GO.lang.strUser,disabled:!GO.settings.modules.email["write_permission"],value:GO.settings.user_id,anchor:"100%"}),{fieldLabel:GO.lang.strName,name:"name",allowBlank:false,anchor:"100%"},this.exportButton=new Ext.Button({text:GO.lang.cmdExport,disabled:true,handler:function(){document.location=GO.settings.modules.tasks.url+"export.php?tasklist_id="+this.tasklist_id},scope:this})]});this.readPermissionsTab=new GO.grid.PermissionsPanel({title:GO.lang.strReadPermissions});this.writePermissionsTab=new GO.grid.PermissionsPanel({title:GO.lang.strWritePermissions});var B=new GO.form.UploadFile({inputName:"ical_file",max:1});B.on("filesChanged",function(D,C){this.importButton.setDisabled(C.getCount()==1)},this);this.importTab=new Ext.form.FormPanel({fileUpload:true,waitMsgTarget:true,disabled:true,title:GO.lang.cmdImport,items:[{xtype:"panel",html:GO.calendar.lang.selectIcalendarFile,border:false},B,this.importButton=new Ext.Button({xtype:"button",disabled:true,text:GO.lang.cmdImport,handler:function(){this.importTab.form.submit({url:GO.settings.modules.tasks.url+"action.php",params:{task:"import",tasklist_id:this.tasklist_id},success:function(C,D){B.clearQueue();if(D.result.success){Ext.MessageBox.alert(GO.lang.strSuccess,D.result.feedback)}else{Ext.MessageBox.alert(GO.lang.strError,D.result.feedback)}},failure:function(C,D){Ext.MessageBox.alert(GO.lang.strError,D.result.feedback)},scope:this})},scope:this})],cls:"go-form-panel"});this.tabPanel=new Ext.TabPanel({hideLabel:true,deferredRender:false,xtype:"tabpanel",activeTab:0,border:false,anchor:"100% 100%",items:[this.propertiesTab,this.readPermissionsTab,this.writePermissionsTab,this.importTab]});GO.tasks.TasklistDialog.superclass.constructor.call(this,{title:GO.tasks.lang.tasklist,layout:"fit",modal:false,height:500,width:400,items:this.tabPanel,buttons:[{text:GO.lang.cmdOk,handler:function(){this.save(true)},scope:this},{text:GO.lang.cmdApply,handler:function(){this.save(false)},scope:this},{text:GO.lang.cmdClose,handler:function(){this.hide()},scope:this}]})};Ext.extend(GO.tasks.TasklistDialog,Ext.Window,{initComponent:function(){this.addEvents({save:true});GO.tasks.TasklistDialog.superclass.initComponent.call(this)},show:function(A){if(!this.rendered){this.render(Ext.getBody())}if(A>0){if(A!=this.tasklist_id){this.loadTasklist(A)}else{GO.tasks.TasklistDialog.superclass.show.call(this)}}else{this.tasklist_id=0;this.propertiesTab.form.reset();this.propertiesTab.show();this.readPermissionsTab.setDisabled(true);this.writePermissionsTab.setDisabled(true);this.exportButton.setDisabled(true);this.importTab.setDisabled(true);GO.tasks.TasklistDialog.superclass.show.call(this)}},loadTasklist:function(A){this.propertiesTab.form.load({url:GO.settings.modules.tasks.url+"json.php",params:{tasklist_id:A,task:"tasklist"},success:function(B,C){this.tasklist_id=A;this.selectUser.setRemoteText(C.result.data.user_name);this.readPermissionsTab.setAcl(C.result.data.acl_read);this.writePermissionsTab.setAcl(C.result.data.acl_write);this.exportButton.setDisabled(false);this.importTab.setDisabled(false);GO.tasks.TasklistDialog.superclass.show.call(this)},failure:function(B,C){Ext.Msg.alert(GO.lang.strError,C.result.feedback)},scope:this})},save:function(A){this.propertiesTab.form.submit({url:GO.settings.modules.tasks.url+"action.php",params:{task:"save_tasklist",tasklist_id:this.tasklist_id},waitMsg:GO.lang.waitMsgSave,success:function(B,C){if(C.result.tasklist_id){this.tasklist_id=C.result.tasklist_id;this.readPermissionsTab.setAcl(C.result.acl_read);this.writePermissionsTab.setAcl(C.result.acl_write);this.exportButton.setDisabled(false);this.importTab.setDisabled(false)}this.fireEvent("save");if(A){this.hide()}},failure:function(C,D){var B="";if(D.failureType=="client"){B=GO.lang.strErrorsInForm}else{B=D.result.feedback}Ext.MessageBox.alert(GO.lang.strError,B)},scope:this})}});GO.tasks.TaskDialog=function(){this.buildForm();var A=function(){this.nameField.focus()};this.win=new Ext.Window({layout:"fit",modal:false,resizable:false,width:560,height:400,closeAction:"hide",title:GO.tasks.lang.task,items:this.formPanel,focus:A.createDelegate(this),buttons:[{text:GO.lang.cmdOk,handler:function(){this.submitForm(true)},scope:this},{text:GO.lang.cmdApply,handler:function(){this.submitForm()},scope:this},{text:GO.lang.cmdClose,handler:function(){this.win.hide()},scope:this}]});this.win.render(Ext.getBody());GO.tasks.TaskDialog.superclass.constructor.call(this);this.addEvents({save:true})};Ext.extend(GO.tasks.TaskDialog,Ext.util.Observable,{show:function(A){if(!A){A={}}propertiesPanel.show();if(!A.task_id){A.task_id=0}this.setTaskId(A.task_id);if(A.task_id>0){this.formPanel.load({url:GO.settings.modules.tasks.url+"json.php",success:function(B,C){this.win.show();this.changeRepeat(C.result.data.repeat_type);this.setValues(A.values);this.selectTaskList.setRemoteText(C.result.data.tasklist_name);this.setWritePermission(C.result.data.write_permission)},failure:function(B,C){Ext.Msg.alert(GO.lang.strError,C.result.feedback)},scope:this})}else{delete this.formPanel.form.baseParams.exception_task_id;delete this.formPanel.form.baseParams.exceptionDate;this.lastTaskListId=this.selectTaskList.getValue();this.formPanel.form.reset();this.selectTaskList.setValue(this.lastTaskListId);this.setWritePermission(true);this.win.show();this.setValues(A.values);if(!A.tasklist_id){A.tasklist_id=GO.tasks.defaultTasklist.id;A.tasklist_name=GO.tasks.defaultTasklist.name}this.selectTaskList.setValue(A.tasklist_id);if(A.tasklist_name){this.selectTaskList.setRemoteText(A.tasklist_name)}}if(A.link_config){this.link_config=A.link_config;if(A.link_config.type_id){this.selectLinkField.setValue(A.link_config.type_id);this.selectLinkField.setRemoteText(A.link_config.text)}}else{delete this.link_config}},setWritePermission:function(A){this.win.buttons[0].setDisabled(!A);this.win.buttons[1].setDisabled(!A)},setValues:function(A){if(A){for(var B in A){var C=this.formPanel.form.findField(B);if(C){C.setValue(A[B])}}}},setTaskId:function(A){this.formPanel.form.baseParams.task_id=A;this.task_id=A},setCurrentDate:function(){var B={};var A=new Date();B.start_date=B.remind_date=A.format(GO.settings.date_format);B.start_hour=A.format("H");B.start_min="00";B.end_date=A.format(GO.settings.date_format);B.end_hour=A.add(Date.HOUR,1).format("H");B.end_min="00";this.formPanel.form.setValues(B)},submitForm:function(A){this.formPanel.form.submit({url:GO.settings.modules.tasks.url+"action.php",params:{task:"save_task"},waitMsg:GO.lang.waitMsgSave,success:function(B,C){if(C.result.task_id){this.setTaskId(C.result.task_id)}if(this.link_config&&this.link_config.callback){this.link_config.callback.call(this)}this.fireEvent("save",this,this.task_id);if(A){this.win.hide()}},failure:function(B,C){if(C.failureType=="client"){Ext.MessageBox.alert(GO.lang.strError,GO.lang.strErrorsInForm)}else{Ext.MessageBox.alert(GO.lang.strError,C.result.feedback)}},scope:this})},buildForm:function(){this.nameField=new Ext.form.TextField({name:"name",allowBlank:false,fieldLabel:GO.lang.strSubject});this.selectLinkField=new GO.form.SelectLink();var L=new Ext.form.TextArea({name:"description",height:100,allowBlank:true,fieldLabel:GO.lang.strDescription});var D=function(R){if(R.name=="due_date"){if(B.getValue()>M.getValue()){B.setValue(M.getValue())}}else{if(B.getValue()>M.getValue()){M.setValue(B.getValue())}}C.form.findField("remind_date").setValue(B.getValue());if(N.getValue()>0){if(G.getValue()==""){H.setValue(true)}else{var Q=M.getValue();if(G.getValue()<Q){G.setValue(Q.add(Date.DAY,1))}}}};var A=new Date();var B=new Ext.form.DateField({name:"start_date",format:GO.settings.date_format,allowBlank:false,fieldLabel:GO.tasks.lang.startsAt,value:A.format(GO.settings.date_format),listeners:{change:{fn:D,scope:this}}});var M=new Ext.form.DateField({name:"due_date",format:GO.settings.date_format,allowBlank:false,fieldLabel:GO.tasks.lang.dueAt,value:A.format(GO.settings.date_format),listeners:{change:{fn:D,scope:this}}});var I=new Ext.form.ComboBox({name:"status_text",hiddenName:"status",triggerAction:"all",editable:false,selectOnFocus:true,forceSelection:true,fieldLabel:GO.lang.strStatus,mode:"local",value:"ACCEPTED",valueField:"value",displayField:"text",store:new Ext.data.SimpleStore({fields:["value","text"],data:[["NEEDS-ACTION",GO.tasks.lang.needsAction],["ACCEPTED",GO.tasks.lang.accepted],["DECLINED",GO.tasks.lang.declined],["TENTATIVE",GO.tasks.lang.tentative],["DELEGATED",GO.tasks.lang.delegated],["COMPLETED",GO.tasks.lang.completed],["IN-PROCESS",GO.tasks.lang.inProcess]]})});this.selectTaskList=new GO.tasks.SelectTasklist({fieldLabel:GO.tasks.lang.tasklist});propertiesPanel=new Ext.Panel({hideMode:"offsets",title:GO.lang.strProperties,defaults:{anchor:"-20"},bodyStyle:"padding:5px",layout:"form",autoScroll:true,items:[this.nameField,this.selectLinkField,L,B,M,I,this.selectTaskList]});this.repeatEvery=new Ext.form.ComboBox({fieldLabel:GO.tasks.lang.repeatEvery,name:"repeat_every_text",hiddenName:"repeat_every",triggerAction:"all",editable:false,selectOnFocus:true,width:50,forceSelection:true,mode:"local",value:"1",valueField:"value",displayField:"text",store:new Ext.data.SimpleStore({fields:["value","text"],data:[["1","1"],["2","2"],["3","3"],["4","4"],["5","5"],["6","6"],["7","7"],["8","8"],["9","9"],["10","10"],["11","11"],["12","12"]]})});var N=this.repeatType=new Ext.form.ComboBox({hiddenName:"repeat_type",triggerAction:"all",editable:false,selectOnFocus:true,width:200,forceSelection:true,mode:"local",value:"0",valueField:"value",displayField:"text",store:new Ext.data.SimpleStore({fields:["value","text"],data:[["0",GO.lang.noRecurrence],["1",GO.lang.strDays],["2",GO.lang.strWeeks],["3",GO.lang.monthsByDate],["4",GO.lang.monthsByDay],["5",GO.lang.strYears]]}),hideLabel:true,listeners:{change:{fn:D,scope:this}}});this.repeatType.on("select",function(R,Q){this.changeRepeat(Q.data.value)},this);this.monthTime=new Ext.form.ComboBox({hiddenName:"month_time",triggerAction:"all",selectOnFocus:true,disabled:true,width:80,forceSelection:true,fieldLabel:GO.tasks.lang.atDays,mode:"local",value:"1",valueField:"value",displayField:"text",store:new Ext.data.SimpleStore({fields:["value","text"],data:[["1",GO.lang.strFirst],["2",GO.lang.strSecond],["3",GO.lang.strThird],["4",GO.lang.strFourth]]})});var E=[];for(var K=0;K<7;K++){E[K]=new Ext.form.Checkbox({boxLabel:GO.lang.shortDays[K],name:"repeat_days_"+K,disabled:true,checked:false,width:"auto",hideLabel:true,laelSeperator:""})}var G=this.repeatEndDate=new Ext.form.DateField({name:"repeat_end_date",width:100,disabled:true,format:GO.settings.date_format,allowBlank:true,fieldLabel:GO.tasks.lang.repeatUntil,listeners:{change:{fn:D,scope:this}}});var H=this.repeatForever=new Ext.form.Checkbox({boxLabel:GO.tasks.lang.repeatForever,name:"repeat_forever",checked:true,disabled:true,width:"auto",hideLabel:true,laelSeperator:""});var F=new Ext.Panel({title:GO.tasks.lang.recurrence,bodyStyle:"padding: 5px",layout:"form",hideMode:"offsets",autoScroll:true,items:[{border:false,layout:"table",defaults:{border:false,layout:"form",bodyStyle:"padding-right:3px"},items:[{items:this.repeatEvery},{items:this.repeatType}]},{border:false,layout:"table",defaults:{border:false,layout:"form",bodyStyle:"padding-right:3px;white-space:nowrap"},items:[{items:this.monthTime},{items:E[0]},{items:E[1]},{items:E[2]},{items:E[3]},{items:E[4]},{items:E[5]},{items:E[6]}]},{border:false,layout:"table",defaults:{border:false,layout:"form",bodyStyle:"padding-right:3px"},items:[{items:this.repeatEndDate},{items:this.repeatForever}]}]});var P=Date.parseDate(A.format("Y-m-d")+" 08:00","Y-m-d G:i");var O=new Ext.Panel({title:GO.tasks.lang.options,defaults:{anchor:"100%"},bodyStyle:"padding:5px",layout:"form",hideMode:"offsets",autoScroll:true,items:[{xtype:"checkbox",boxLabel:GO.tasks.lang.remindMe,hideLabel:true,name:"remind",listeners:{check:function(R,Q){this.formPanel.form.findField("remind_date").setDisabled(!Q);this.formPanel.form.findField("remind_time").setDisabled(!Q)},scope:this}},{xtype:"datefield",name:"remind_date",format:GO.settings.date_format,value:A.format(GO.settings.date_format),fieldLabel:GO.lang.strDate,disabled:true},{xtype:"timefield",name:"remind_time",format:GO.settings.time_format,value:P.format(GO.settings.time_format),fieldLabel:GO.lang.strTime,disabled:true}]});var J=[propertiesPanel,F,O];this.tabPanel=new Ext.TabPanel({activeTab:0,deferredRender:false,border:false,anchor:"100% 100%",hideLabel:true,items:J});var C=this.formPanel=new Ext.form.FormPanel({waitMsgTarget:true,url:GO.settings.modules.tasks.url+"action.php",border:false,baseParams:{task:"task"},items:this.tabPanel})},changeRepeat:function(B){var A=this.formPanel.form;switch(B){case"0":this.disableDays(true);this.monthTime.setDisabled(true);this.repeatForever.setDisabled(true);this.repeatEndDate.setDisabled(true);this.repeatEvery.setDisabled(true);break;case"1":this.disableDays(true);this.monthTime.setDisabled(true);this.repeatForever.setDisabled(false);this.repeatEndDate.setDisabled(false);this.repeatEvery.setDisabled(false);break;case"2":this.disableDays(false);this.monthTime.setDisabled(true);this.repeatForever.setDisabled(false);this.repeatEndDate.setDisabled(false);this.repeatEvery.setDisabled(false);break;case"3":this.disableDays(true);this.monthTime.setDisabled(true);this.repeatForever.setDisabled(false);this.repeatEndDate.setDisabled(false);this.repeatEvery.setDisabled(false);break;case"4":this.disableDays(false);this.monthTime.setDisabled(false);this.repeatForever.setDisabled(false);this.repeatEndDate.setDisabled(false);this.repeatEvery.setDisabled(false);break;case"5":this.disableDays(true);this.monthTime.setDisabled(true);this.repeatForever.setDisabled(false);this.repeatEndDate.setDisabled(false);this.repeatEvery.setDisabled(false);break}},disableDays:function(B){for(var A=0;A<7;A++){this.formPanel.form.findField("repeat_days_"+A).setDisabled(B)}}});GO.tasks.TasksPanel=function(A){if(!A){A={}}A.store=new GO.data.JsonStore({url:GO.settings.modules.tasks.url+"json.php",baseParams:{task:"tasks"},root:"results",totalProperty:"total",id:"id",fields:["id","name","completed","due_time","late","description"]});var B=new GO.grid.CheckColumn({header:"",dataIndex:"completed",width:30,header:'<div class="tasks-complete-icon"></div>'});B.on("change",function(D,E){this.store.baseParams.completed_task_id=D.data.id;this.store.baseParams.checked=E;this.store.reload();delete this.store.baseParams.completed_task_id;delete this.store.baseParams.checked},this);var C=new Ext.Template('<table border="0" cellspacing="0" cellpadding="0" style="{tstyle}">','<thead><tr class="x-grid3-hd-row">{cells}</tr></thead>','<tbody><tr class="new-task-row">','<td><div id="tasks-new-task-icon"></div></td>','<td><div class="x-small-editor" id="new-task-name"></div></td>','<td><div class="x-small-editor" id="new-task-due"></div></td>',"</tr></tbody>","</table>");A.paging=true,A.plugins=B;A.autoExpandColumn=1;A.autoExpandMax=2500;A.enableColumnHide=false;A.enableColumnMove=false;A.columns=[B,{header:GO.lang.strName,dataIndex:"name",renderer:function(E,F,D){F.attr='ext:qtip="'+D.data.description+'"';return E}},{header:GO.tasks.lang.dueDate,dataIndex:"due_time",width:100}];A.view=new Ext.grid.GridView({emptyText:GO.tasks.lang.noTask,templates:{header:C},getRowClass:function(D,G,F,E){if(D.data.late){return"tasks-late"}}}),A.sm=new Ext.grid.RowSelectionModel();A.loadMask=true;GO.tasks.TasksPanel.superclass.constructor.call(this,A)};Ext.extend(GO.tasks.TasksPanel,GO.grid.GridPanel,{saveListenerAdded:true,afterRender:function(){GO.tasks.TasksPanel.superclass.afterRender.call(this);this.ntName=new Ext.form.TextField({renderTo:"new-task-name",emptyText:GO.tasks.lang.addTask,width:200});this.ntDue=new Ext.form.DateField({renderTo:"new-task-due",value:new Date(),disabled:true,format:GO.settings.date_format});this.editing=false;this.focused=false;this.userTriggered=false;var A={focus:function(){this.focused=true},blur:function(){this.focused=false;this.doBlur.defer(250,this)},specialkey:function(B,C){if(C.getKey()==C.ENTER){this.userTriggered=true;C.stopEvent();B.el.blur();if(B.triggerBlur){B.triggerBlur()}}},scope:this};this.ntName.on(A,this);this.ntDue.on(A,this);this.ntName.on("focus",function(){this.focused=true;if(!this.editing){this.ntDue.enable();this.syncFields();this.editing=true}},this);this.syncFields.defer(200,this)},syncFields:function(){var A=this.getColumnModel();this.ntName.setSize(A.getColumnWidth(1)-4);this.ntDue.setSize(A.getColumnWidth(2)-4)},doBlur:function(){if(this.editing&&!this.focused){var A=this.ntName.getValue();var B=this.ntDue.getValue();if(!Ext.isEmpty(A)){Ext.Ajax.request({url:GO.settings.modules.tasks.url+"action.php",params:{task:"save_task",tasklist_id:this.store.baseParams.tasklist_id,name:A,start_date:B.format(GO.settings.date_format),due_date:B.format(GO.settings.date_format)},callback:function(D,E,C){if(!E){Ext.MessageBox.alert(GO.lang.strError,GO.lang.strRequestError)}else{this.store.reload()}},scope:this});this.ntName.setValue("");if(this.userTriggered){this.userTriggered=false;this.ntName.focus.defer(100,this.ntName)}}this.ntDue.disable();this.editing=false}}});GO.tasks.SelectTasklist=function(A){Ext.apply(this,A);if(!this.company_name){this.company_name=""}this.store=new GO.data.JsonStore({url:GO.settings.modules.tasks.url+"json.php",baseParams:{task:"tasklists",auth_type:"write"},root:"results",totalProperty:"total",id:"id",fields:["id","name","user_name"],remoteSort:true});GO.tasks.SelectTasklist.superclass.constructor.call(this,{displayField:"name",hiddenName:"tasklist_id",valueField:"id",triggerAction:"all",mode:"remote",editable:true,selectOnFocus:true,forceSelection:true,typeAhead:true,emptyText:GO.lang.strPleaseSelect,fieldLabel:GO.tasks.lang.tasklist,pageSize:parseInt(GO.settings.max_rows_list)})};Ext.extend(GO.tasks.SelectTasklist,GO.form.ComboBox,{});GO.tasks.MainPanel=function(C){if(!C){C={}}this.taskListsStore=new GO.data.JsonStore({url:GO.settings.modules.tasks.url+"json.php",baseParams:{task:"tasklists"},root:"results",totalProperty:"total",id:"id",fields:["id","dom_id","name"]});this.taskListsPanel=new GO.grid.GridPanel({region:"center",store:this.taskListsStore,cls:"go-grid3-hide-headers",title:GO.tasks.lang.tasklists,items:this.tasksLists,loadMask:true,autoScroll:true,border:true,split:true,sm:new Ext.grid.RowSelectionModel({singleSelect:true}),viewConfig:{forceFit:true,autoFill:true},columns:[{header:GO.lang.strName,dataIndex:"name"}]});this.taskListsPanel.on("rowclick",function(F,E){this.tasklist_id=F.store.data.items[E].data.id;this.tasklist_name=F.store.data.items[E].data.name;this.gridPanel.store.baseParams.tasklist_id=this.tasklist_id;this.gridPanel.store.load()},this);var B=new Ext.form.Checkbox({boxLabel:GO.tasks.lang.showCompletedTasks,hideLabel:true,checked:GO.tasks.showCompleted});B.on("check",function(E,F){this.gridPanel.store.baseParams.show_completed=F?"1":"0";this.gridPanel.store.reload();delete this.gridPanel.store.baseParams.show_completed},this);var A=new Ext.form.Checkbox({boxLabel:GO.tasks.lang.showInactiveTasks,hideLabel:true,checked:GO.tasks.showInactive});A.on("check",function(E,F){this.gridPanel.store.baseParams.show_inactive=F?"1":"0";this.gridPanel.store.reload();delete this.gridPanel.store.baseParams.show_inactive},this);var D=new Ext.form.FormPanel({title:GO.tasks.lang.filter,height:85,cls:"go-form-panel",waitMsgTarget:true,region:"north",border:true,split:true,items:[B,A]});this.gridPanel=new GO.tasks.TasksPanel({region:"center"});this.gridPanel.on("delayedrowselect",function(E,G,F){this.taskPanel.load(F.data.id)},this);this.taskPanel=new GO.tasks.TaskPanel({title:GO.tasks.lang.task,region:"east",width:400,border:true});C.layout="border";C.items=[new Ext.Panel({region:"north",height:32,baseCls:"x-plain",tbar:new Ext.Toolbar({cls:"go-head-tb",items:[{iconCls:"btn-add",text:GO.lang.cmdAdd,cls:"x-btn-text-icon",handler:function(){if(!GO.tasks.taskDialog){GO.tasks.taskDialog=new GO.tasks.TaskDialog()}if(!GO.tasks.taskDialog.hasListener("save")){GO.tasks.taskDialog.on("save",function(){this.gridPanel.store.reload()},this)}GO.tasks.taskDialog.show({tasklist_id:this.tasklist_id,tasklist_name:this.tasklist_name})},scope:this},{iconCls:"btn-delete",text:GO.lang.cmdDelete,cls:"x-btn-text-icon",handler:function(){this.gridPanel.deleteSelected()},scope:this},{iconCls:"btn-settings",text:GO.lang.cmdSettings,cls:"x-btn-text-icon",handler:function(){this.showAdminDialog()},scope:this}]})}),new Ext.Panel({region:"west",titlebar:false,autoScroll:false,closeOnTab:true,width:210,split:true,resizable:true,layout:"border",baseCls:"x-plain",items:[this.taskListsPanel,D]}),this.gridPanel,this.taskPanel];GO.tasks.MainPanel.superclass.constructor.call(this,C)};Ext.extend(GO.tasks.MainPanel,Ext.Panel,{afterRender:function(){GO.tasks.MainPanel.superclass.afterRender.call(this);GO.tasks.taskDialog.on("save",function(){this.gridPanel.store.reload()},this);this.taskListsStore.load({callback:function(){this.tasklist_id=GO.tasks.defaultTasklist.id;this.tasklist_name=GO.tasks.defaultTasklist.name;this.gridPanel.store.baseParams.tasklist_id=GO.tasks.defaultTasklist.id;this.gridPanel.store.load({callback:function(){var B=this.taskListsPanel.getSelectionModel();var A=this.taskListsStore.getById(GO.tasks.defaultTasklist.id);B.selectRecords([A])},scope:this})},scope:this})},showAdminDialog:function(){if(!this.adminDialog){this.tasklistDialog=new GO.tasks.TasklistDialog();this.tasklistDialog.on("save",function(){GO.tasks.writableTasklistsStore.load();this.taskListsStore.load()},this);this.tasklistsGrid=new GO.grid.GridPanel({paging:true,border:false,store:GO.tasks.writableTasklistsStore,deleteConfig:{callback:function(){this.taskListsStore.load()},scope:this},columns:[{header:GO.lang.strName,dataIndex:"name"},{header:GO.lang.strOwner,dataIndex:"user_name"}],view:new Ext.grid.GridView({autoFill:true}),sm:new Ext.grid.RowSelectionModel(),loadMask:true,tbar:[{iconCls:"btn-add",text:GO.lang.cmdAdd,cls:"x-btn-text-icon",handler:function(){this.tasklistDialog.show()},scope:this},{iconCls:"btn-delete",text:GO.lang.cmdDelete,cls:"x-btn-text-icon",handler:function(){this.tasklistsGrid.deleteSelected()},scope:this}]});this.tasklistsGrid.on("rowdblclick",function(A,B,C){this.tasklistDialog.show(A.selModel.selections.keys[0])},this);this.adminDialog=new Ext.Window({title:GO.tasks.lang.tasklists,layout:"fit",modal:false,minWidth:300,minHeight:300,height:400,width:600,closeAction:"hide",items:this.tasklistsGrid,buttons:[{text:GO.lang.cmdClose,handler:function(){this.adminDialog.hide()},scope:this}]})}if(!GO.tasks.writableTasklistsStore.loaded){GO.tasks.writableTasklistsStore.load()}this.adminDialog.show()}});GO.mainLayout.onReady(function(){GO.tasks.taskDialog=new GO.tasks.TaskDialog()});GO.tasks.writableTasklistsStore=new GO.data.JsonStore({url:GO.settings.modules.tasks.url+"json.php",baseParams:{task:"tasklists",auth_type:"write"},root:"results",totalProperty:"total",id:"id",fields:["id","name","user_name"],remoteSort:true});GO.moduleManager.addModule("tasks",GO.tasks.MainPanel,{title:GO.tasks.lang.tasks,iconCls:"go-tab-icon-tasks"});GO.linkHandlers[12]=function(D,B){var A=new GO.tasks.TaskPanel();var C=new GO.LinkViewWindow({title:GO.tasks.lang.task,items:A});A.load(D);C.show()};GO.newMenuItems.push({text:GO.tasks.lang.task,iconCls:"go-link-icon-12",handler:function(A,B){if(!GO.tasks.taskDialog){GO.tasks.taskDialog=new GO.tasks.TaskDialog()}GO.tasks.taskDialog.show({link_config:A.parentMenu.link_config})}});GO.tasks.SimpleTasksPanel=function(A){if(!A){A={}}A.store=new GO.data.JsonStore({url:GO.settings.modules.tasks.url+"json.php",baseParams:{task:"tasks",user_id:GO.settings.user_id,active_only:true},root:"results",totalProperty:"total",id:"id",fields:["id","name","completed","due_time","description"]});var B=new GO.grid.CheckColumn({header:"",dataIndex:"completed",width:30,header:'<div class="tasks-complete-icon"></div>'});B.on("change",function(C,D){this.store.baseParams.completed_task_id=C.data.id;this.store.baseParams.checked=D;this.store.reload();delete this.store.baseParams.completed_task_id;delete this.store.baseParams.checked},this);A.paging=false,A.plugins=B;A.autoExpandColumn=1;A.autoExpandMax=2500;A.enableColumnHide=false;A.enableColumnMove=false;A.columns=[B,{header:GO.lang.strName,dataIndex:"name",renderer:function(D,E,C){E.attr='ext:qtip="'+C.data.description+'"';return D}},{header:GO.tasks.lang.dueDate,dataIndex:"due_time",width:100}];A.view=new Ext.grid.GridView({emptyText:GO.tasks.lang.noTask}),A.sm=new Ext.grid.RowSelectionModel();A.loadMask=true;A.autoHeight=true;GO.tasks.SimpleTasksPanel.superclass.constructor.call(this,A)};Ext.extend(GO.tasks.SimpleTasksPanel,GO.grid.GridPanel,{saveListenerAdded:false,afterRender:function(){GO.tasks.SimpleTasksPanel.superclass.afterRender.call(this);this.on("rowdblclick",function(A,B,C){if(!GO.tasks.taskDialog){GO.tasks.taskDialog=new GO.tasks.TaskDialog()}if(!this.saveListenerAdded){this.saveListenerAdded=true;GO.tasks.taskDialog.on("save",function(){this.store.reload()},this)}GO.tasks.taskDialog.show({task_id:A.selModel.selections.keys[0]})},this);Ext.TaskMgr.start({run:this.store.load,scope:this.store,interval:960000})}});GO.mainLayout.onReady(function(){if(GO.summary){var A=new GO.tasks.SimpleTasksPanel();GO.summary.portlets["portlet-tasks"]=new GO.summary.Portlet({id:"portlet-tasks",title:GO.tasks.lang.tasks,layout:"fit",tools:[{id:"close",handler:function(D,C,B){B.removePortlet()}}],items:A,autoHeight:true})}});