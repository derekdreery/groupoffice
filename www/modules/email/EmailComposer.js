/**
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

GO.email.EmailComposer = function(config) {
	Ext.apply(config);
	
	var priorityGroup = Ext.id();
	
	var optionsMenuItems = [
	this.notifyCheck = new Ext.menu.CheckItem({
		text : GO.email.lang.notification,
		checked : false,
		checkHandler : function(check, checked) {
			this.sendParams['notification'] = checked
			? 'true'
			: 'false';
		},
		scope : this
	}),
	'-',
	'<div class="menu-title">'
	+ GO.email.lang.priority + '</div>', {
		text : GO.email.lang.high,
		checked : false,
		group : priorityGroup,
		checkHandler : function() {
			this.sendParams['priority'] = '1';
		},
		scope : this
	}, this.normalPriorityCheck = new Ext.menu.CheckItem({
		text : GO.email.lang.normal,
		checked : true,
		group : priorityGroup,
		checkHandler : function() {
			this.sendParams['priority'] = '3';
		},
		scope : this
	}), {
		text : GO.email.lang.low,
		checked : false,
		group : priorityGroup,
		checkHandler : function() {
			this.sendParams['priority'] = '5';
		},
		scope : this
	},'-',this.htmlCheck = new Ext.menu.CheckItem({
		text:GO.email.lang.htmlMarkup,
		checked:GO.email.useHtmlMarkup,
		listeners : {
			checkchange: function(check, checked) {
								 	
				if(this.bodyContentAtWindowOpen==this.editor.getValue() || confirm(GO.email.lang.confirmLostChanges))
				{
					this.setContentTypeHtml(checked);
					/**
					 * reload dialog for text or html
					 */
					this.showConfig.keepEditingMode=true;
					var v = this.formPanel.form.getValues();
					
					if(!this.showConfig.values) this.showConfig.values={};
						Ext.apply(this.showConfig.values, v);

					
					delete this.showConfig.move;
					this.show(this.showConfig);
				}else
				{
					check.setChecked(!checked, true);
				}
			},
			scope:this
		}
	})];
						
	if(GO.gnupg)
	{
		optionsMenuItems.push('-');
			
		optionsMenuItems.push(this.encryptCheck = new Ext.menu.CheckItem({
			text:GO.gnupg.lang.encryptMessage,
			checked: false,
			listeners : {
				checkchange: function(check, checked) {
					if(this.formPanel.baseParams.content_type=='html')
					{
						if(!confirm(GO.gnupg.lang.confirmChangeToText))
						{
							check.setChecked(!checked, true);
							return false;
						}else
						{
							this.setContentTypeHtml(false);
							this.htmlCheck.setChecked(false, true);
							this.showConfig.keepEditingMode=true;
							this.show(this.showConfig);
						}
					}
						
					this.htmlCheck.setDisabled(checked);
						
					this.sendParams['encrypt'] = checked
					? '1'
					: '0';
								
					return true;
				},
				scope:this
			}
		}));
	}

	this.optionsMenu = new Ext.menu.Menu({
		items : optionsMenuItems
	});

	this.showMenu = new Ext.menu.Menu({
				
		items : [this.formFieldCheck = new Ext.menu.CheckItem({
			text : GO.email.lang.sender,
			checked : true,
			checkHandler : this.onShowFieldCheck,
			scope : this
		}),
		this.ccFieldCheck = new Ext.menu.CheckItem({
			text : GO.email.lang.ccField,
			checked : false,
			checkHandler : this.onShowFieldCheck,
			scope : this
		}),
		this.bccFieldCheck = new Ext.menu.CheckItem({
			text : GO.email.lang.bccField,
			checked : false,
			checkHandler : this.onShowFieldCheck,
			scope : this
		})
		]
	});

	var imageInsertPlugin = new GO.plugins.HtmlEditorImageInsert();

	imageInsertPlugin.on('insert', function(plugin) {
		this.inline_attachments.push({
			tmp_file : plugin.selectedRecord.get('id'),
			url : plugin.selectedUrl
		});
	}, this);
			
			
	var items = [
	this.fromCombo = new Ext.form.ComboBox({
		store : GO.email.aliasesStore,
		fieldLabel : GO.email.lang.from,
		name : 'alias_name',
		anchor : '100%',
		displayField : 'name',
		valueField : 'id',
		hiddenName : 'alias_id',
		forceSelection : true,
		triggerAction : 'all',
		mode : 'local',
		tpl: '<tpl for="."><div class="x-combo-list-item">{name:htmlEncode}</div></tpl>',
		listeners:{
			beforeselect: function(cb, newAccountRecord){
				var oldAccountRecord = cb.store.getById(cb.getValue());
											
				var oldSig = oldAccountRecord.get(this.formPanel.baseParams.content_type+"_signature");
				var newSig = newAccountRecord.get(this.formPanel.baseParams.content_type+"_signature");

				var editorValue = this.editor.getValue();
				if(GO.util.empty(oldSig))
				{
					this.addSignature(newAccountRecord);
				}else
				{
					this.editor.setValue(editorValue.replace(oldSig,newSig));
				}
			},
			scope:this
		}
	}),

	this.toCombo = new GO.form.ComboBoxMulti({
		sep : ',',
		fieldLabel : GO.email.lang.sendTo,
		name : 'to',
		anchor : '100%',
		height : 50,
		store : new Ext.data.JsonStore({
			url : BaseHref + 'json.php',
			baseParams : {
				task : "email"
			},
			fields : ['full_email'],
			root : 'persons'
		}),
		displayField : 'full_email'
	}),

	this.ccCombo = new GO.form.ComboBoxMulti({
		sep : ',',
		fieldLabel : GO.email.lang.cc,
		name : 'cc',
		anchor : '100%',
		height : 50,
		store : new Ext.data.JsonStore({
			url : BaseHref + 'json.php',
			baseParams : {
				task : "email"
			},
			fields : ['full_email'],
			root : 'persons'
		}),
		displayField : 'full_email',
		hideTrigger : true,
		minChars : 2,
		triggerAction : 'all',
		selectOnFocus : false

	}),

	this.bccCombo = new GO.form.ComboBoxMulti({
		sep : ',',
		fieldLabel : GO.email.lang.bcc,
		name : 'bcc',
		anchor : '100%',
		height : 50,
		store : new Ext.data.JsonStore({
			url : BaseHref + 'json.php',
			baseParams : {
				task : "email"
			},
			fields : ['full_email'],
			root : 'persons'
		}),
		displayField : 'full_email',
		hideTrigger : true,
		minChars : 2,
		triggerAction : 'all',
		selectOnFocus : false

	})];
								
	var anchor = -113;
						
	if(GO.mailings)
	{
		this.selectLinkField = new GO.form.SelectLink({
			anchor : '100%'
		});
					
		anchor+=26;
		items.push(this.selectLinkField);
	}
				
	items.push(this.subjectField = new Ext.form.TextField({
		fieldLabel : GO.email.lang.subject,
		name : 'subject',
		anchor : '100%'
	}));
				
	items.push(this.htmlEditor = new Ext.form.HtmlEditor({
		hideLabel : true,
		name : 'body',
		anchor : '100% '+anchor,
		plugins : imageInsertPlugin
	}));
				
	items.push(this.textEditor = new Ext.form.TextArea({
		name: 'textbody',
		anchor : '100% '+anchor,
		hideLabel : true
	}));
						

	this.formPanel = new Ext.form.FormPanel({
		border : false,
		labelWidth : 100,
		waitMsgTarget : true,
		baseParams: {
			content_type:'html'
		},
		cls : 'go-form-panel',
		defaultType : 'textfield',
		items : items
	});

	//Set a long timeout for large attachments
	this.formPanel.form.timeout=3000;

	//the html markup from a signature changes when the editor is initialized. The initialize event fires too soon.
	//The first push event does the trick of changing the html.
	this.htmlEditor.on('push', function(){
		this.bodyContentAtWindowOpen=this.htmlEditor.getValue();
	}, this, {
		single:true
	});

	
		

	// store for attachments needs to be created here because a forward action
	// might attachments
	this.attachmentsStore = new Ext.data.JsonStore({
		url : GO.settings.modules.email.url + 'json.php',
		baseParams : {
			task : 'attachments'
		},
		root : 'results',
		fields : ['tmp_name', 'name', 'size', 'type'],
		id : 'tmp_name'
	});

	this.attachmentsStore.on('remove', this.updateAttachmentsButton, this);
	this.attachmentsStore.on('load', this.updateAttachmentsButton, this);

	if (GO.mailings) {
		this.templatesStore = new GO.data.JsonStore({
			url : GO.settings.modules.mailings.url + 'json.php',
			baseParams : {
				'task' : 'authorized_templates'
			},
			root : 'results',
			totalProperty : 'total',
			id : 'id',
			fields : ['id', 'name', 'default_template'],
			remoteSort : true
		});

		this.templatesList = new GO.email.TemplatesList({
			store : this.templatesStore
		});

		this.templatesList.on('click', function(dataview, index){

			this.showConfig.template_id = index > 0
			? dataview.store.data.items[index - 1].id
			: 0;
					
			if(this.templateDefault.checked)
			{
				this.templatesStore.baseParams.default_template_id=this.showConfig.template_id;
				this.templatesStore.load();
				delete this.templatesStore.baseParams.default_template_id;

				this.templateDefault.setValue(false);
			}
			
			this.show(this.showConfig);
			this.templatesWindow.hide();
			this.templatesList.clearSelections();
		}, this);

		
				
		
		this.defaultTemplatePanel = new Ext.FormPanel({
			autoHeight:true,
			cls:'go-form-panel',
			region:'south',
			items:[
			this.templateDefault = new Ext.form.Checkbox({
				name:'templateDefault',
				boxLabel:GO.email.lang.defaultTemplate,
				anchor:'100%',
				hideLabel:true,
				checked:false
			})]
		});
				
		this.templatesWindow = new Ext.Window({
			title : GO.email.lang.selectTemplate,
			layout : 'border',
			modal : false,
			height : 400,
			width : 600,
			closable : true,
			closeAction : 'hide',
			items : [new Ext.Panel({
				region:'center',
				autoScroll : true,
				items : [this.templatesList],
				cls : 'go-form-panel'
			}),this.defaultTemplatePanel]
		});
	}

	var tbar = [this.sendButton = new Ext.Button({
		text : GO.email.lang.send,
		iconCls : 'btn-send',
		handler : function() {
			this.sendMail();
		},
		scope : this
	}), {
		text : GO.email.lang.extraOptions,
		iconCls : 'btn-settings',
		menu : this.optionsMenu
	// assign menu by instance
	}	, this.showMenuButton = new Ext.Button({
		text : GO.email.lang.show,
		iconCls : 'btn-show',
		menu : this.showMenu
	// assign menu by instance
	}), this.attachmentsButton = new Ext.Button({
		text : GO.email.lang.attachments,
		iconCls : 'btn-attach',
		handler : this.showAttachmentsDialog,
		scope : this
	}), this.saveButton = new Ext.Button({
		iconCls : 'btn-save',
		text : GO.lang.cmdSave,
		handler : function() {
			this.sendMail(true);
		},
		scope : this
	})];

	if (GO.addressbook) {
		tbar.push({
			text : GO.addressbook.lang.addressbook,
			iconCls : 'btn-addressbook',
			handler : function() {
				if (!this.addressbookDialog) {
					this.addressbookDialog = new GO.email.AddressbookDialog();
					this.addressbookDialog.on('addrecipients',
						function(fieldName, selections) {
							var field = this.formPanel.form.findField(fieldName);
										
							var currentVal = field.getValue();
							if (currentVal != '' && currentVal.substring(currentVal.length-1,currentVal.length) != ',' && currentVal.substring(currentVal.length-2,currentVal.length-1)!=',')
								currentVal += ', ';

							currentVal += selections;

							field.setValue(currentVal);

							if (fieldName == 'cc') {
								this.ccFieldCheck.setChecked(true);
							} else if (fieldName == 'bcc') {
								this.bccFieldCheck.setChecked(true);
							}

						}, this);
				}

				this.addressbookDialog.show();
			},
			scope : this
		});
	}

	var focusFn = function() {
		this.toCombo.focus();
	};

	GO.email.EmailComposer.superclass.constructor.call(this, {
		title : GO.email.lang.composeEmail,
		width : 700,
		height : 500,
		minWidth : 300,
		minHeight : 200,
		layout : 'fit',
		maximizable : true,
		collapsible : true,
		animCollapse : false,
		plain : true,
		closeAction : 'hide',
		buttonAlign : 'center',
		focus : focusFn.createDelegate(this),
		tbar : tbar,
		items : this.formPanel
	});

	this.addEvents({
		'send' : true
	});
};

Ext.extend(GO.email.EmailComposer, Ext.Window, {
	
	showConfig : {},

	autoSaveTask : {},
	
	lastAutoSave : false,
	
	bodyContentAtWindowOpen : false,
	
	setContentTypeHtml : function(checked){
		this.formPanel.baseParams.content_type = checked
		? 'html'
		: 'plain';
					
		//this.htmlCheck.setChecked(checked, true);

		this.htmlEditor.getEl().up('.x-form-item').setDisplayed(checked);
		this.textEditor.getEl().up('.x-form-item').setDisplayed(!checked);

		this.editor = checked ? this.htmlEditor : this.textEditor;
	},
	
	autoSave : function(){
		if(GO.util.empty(this.sendParams.mailing_group_id) && this.lastAutoSave && this.lastAutoSave!=this.editor.getValue())
		{
			this.sendMail(true,true);
		}
		this.lastAutoSave=this.editor.getValue();
	},
	
	startAutoSave : function(){
		this.lastAutoSave=false;
		Ext.TaskMgr.start(this.autoSaveTask);
	},
	
	stopAutoSave : function(){
		Ext.TaskMgr.stop(this.autoSaveTask);
	},
	
	afterRender : function() {
		GO.email.EmailComposer.superclass.afterRender.call(this);

		this.autoSaveTask={
			run: this.autoSave,
			scope:this,
			interval:120000
		//interval:5000
		};
		
		this.on('hide', this.stopAutoSave, this);

	/*this[this.collapseEl].hideMode='offsets';

		this.on('beforecollapse', function(){
			console.log(this[this.collapseEl]);
			this[this.collapseEl].hideMode='offsets';
		}, this);		*/
	},

	toComboVisible : true,

	updateAttachmentsButton : function() {

		var text = this.attachmentsStore.getCount() > 0
		? GO.email.lang.attachments + ' ('
		+ this.attachmentsStore.getCount() + ')'
		: GO.email.lang.attachments;

		this.attachmentsButton.setText(text);
	},

	reset : function(keepAttachments) {

		this.showCC(false);
		this.showBCC(false);
		this.bccFieldCheck.setChecked(false);
		this.ccFieldCheck.setChecked(false);
		
		this.sendParams = {
			'task' : 'sendmail',
			notification : 'false',
			priority : '3',
			draft_uid : 0,
			inline_attachments : {}
		};
		this.inline_attachments = [];
		this.formPanel.form.reset();

		if (this.defaultAcccountId) {
			this.fromCombo.setValue(this.defaultAcccountId);
		}

		this.notifyCheck.setChecked(false);
		this.normalPriorityCheck.setChecked(true);

		if (!keepAttachments) {
			this.attachmentsStore.removeAll();
			this.updateAttachmentsButton();
		}
	},

	showCC : function(show){
		this.ccCombo.getEl().up('.x-form-item').setDisplayed(show);
		if(show)
		{
			this.ccCombo.onResize();
		}
	},
	
	showBCC : function(show){	
		this.bccCombo.getEl().up('.x-form-item').setDisplayed(show);
		if(show)
		{
			this.bccCombo.onResize();
		}
	},

	show : function(config) {

		Ext.getBody().mask(GO.lang.waitMsgLoad);
		
		this.showConfig=config;

		if (!this.rendered) {

			var loadCb = function() {
				var records = this.fromCombo.store.getRange();
				if (records.length) {
					if (!config.account_id) {
						this.showConfig.account_id = records[0].data.account_id;
					}

					this.render(Ext.getBody());
					this.show(this.showConfig);
					
					return;

				} else {
					Ext.getBody().unmask();
					Ext.Msg.alert(GO.email.lang.noAccountTitle,
						GO.email.lang.noAccount);
				}
			};

			if (!GO.mailings) {
				config.template_id = 0;
				this.fromCombo.store.load({
					callback:loadCb,
					scope:this
				});
			} else {

				this.templatesStore.load({
					callback : function() {
						this.fromCombo.store.load({
							callback:loadCb,
							scope:this
						});
					},
					scope : this
				});
			}

		} else if (config.template_id == undefined && this.templatesStore
			&& this.templatesStore.getTotalCount() > 1) {
			//this.showConfig = config;
			Ext.getBody().unmask();
			this.templatesWindow.show();
		} else {

			if (config.template_id == undefined && this.templatesStore
				&& this.templatesStore.getTotalCount() == 1) {
				config.template_id = this.templatesStore.data.items[0]
				.get('id');
			}

			
			//this.inline_attachments = [];

			//keep attachments when switchting from text <> html
			this.reset(config.keepEditingMode);

			var index=-1;
			if (config.account_id) {
				index = this.fromCombo.store.findBy(function(record, id){
					return record.get('account_id')==config.account_id;
				});
			}
			if(index==-1)
			{
				index=0;
			}
			this.fromCombo.setValue(this.fromCombo.store.data.items[index].id);

			if (config.values) {
				this.formPanel.form.setValues(config.values);
			}

			//this will be true when swithing from html to text or vice versa
			if(!config.keepEditingMode)
			{
				//remove attachments if not switching edit mode
				//this.attachmentsStore.removeAll();
				
				this.setContentTypeHtml(GO.email.useHtmlMarkup);
				this.htmlCheck.setChecked(GO.email.useHtmlMarkup, true);
				if(this.encryptCheck)
					this.encryptCheck.setChecked(false, true);
			}			

			this.toComboVisible = true;
			this.showMenuButton.setDisabled(false);
			this.toCombo.getEl().up('.x-form-item').setDisplayed(true);
			this.sendURL = GO.settings.modules.email.url + 'action.php';
			this.saveButton.setDisabled(false);
			
			if(config.move)
			{
				var pos = this.getPosition();
				this.setPagePosition(pos[0]+config.move, pos[1]+config.move);
			}
			
			
			// for mailings plugin
			if (config.mailing_group_id > 0) {
				this.sendURL = GO.settings.modules.mailings.url
				+ 'action.php';

				this.toComboVisible = false;
				this.showMenuButton.setDisabled(true);
				this.toCombo.getEl().up('.x-form-item').setDisplayed(false);
				this.showCC(false);
				this.showBCC(false);

				this.sendParams.mailing_group_id = config.mailing_group_id;

				this.saveButton.setDisabled(true);
			}

			if (config.uid || config.template_id || config.loadUrl) {
				if (!config.task) {
					config.task = 'template';
				}
				
				if(config.task=='opendraft')
					this.sendParams.draft_uid = config.uid;
				
				var fromRecord = this.fromCombo.store.getById(this.fromCombo.getValue());

				var params = config.loadParams ? config.loadParams : {
					uid : config.uid,
					account_id : fromRecord.get('account_id'),
					task : config.task,
					mailbox : config.mailbox
				};
				
				if (config.mailing_group_id > 0) {
					// so that template loading won't replace fields
					params.mailing_group_id = config.mailing_group_id;
				}

				if (config.template_id > 0) {
					params.template_id=config.template_id;
					params.to = this.toCombo.getValue();
				}

				var url = config.loadUrl
				? config.loadUrl
				: GO.settings.modules.email.url + 'json.php';

				//sometimes this is somehow copied from the baseparams
				delete params.content_type;

				this.formPanel.form.load({
					url : url,
					params : params,
					waitMsg : GO.lang.waitMsgLoad,
					failure:function(form, action)
					{
						Ext.Msg.alert(GO.lang['strError'], action.result.feedback)
					},
					success : function(form, action) {

						if (config.task == 'reply'
							|| config.task == 'reply_all') {
							this.sendParams['reply_uid'] = config.uid;
							this.sendParams['reply_mailbox'] = config.mailbox;
						}

						if (action.result.data.inline_attachments) {
							this.inline_attachments = action.result.data.inline_attachments;
						}
						if (action.result.data.attachments) {
							this.attachmentsStore.loadData({
								results : action.result.data.attachments
							}, true);
						}
						
						this.afterShowAndLoad(params.task!='opendraft');
					},
					scope : this
				});

			}else
			{
				this.afterShowAndLoad(true);
			}
			
		}
	},
	
	afterShowAndLoad : function(addSignature){
		if(addSignature)
		{
			this.addSignature();
		}
		
		if(this.formPanel.baseParams.content_type=='plain')
		{
			//set cursor at top
			this.editor.selectText(0,0);
		}
		
		if (this.toCombo.getValue() == '') {
			this.toCombo.focus();
		} else {
			this.editor.focus();
		}

		this.setEditorHeight();
		this.startAutoSave();
		this.bodyContentAtWindowOpen=this.editor.getValue();		

		this.bccFieldCheck.setChecked(this.bccCombo.getValue()!='');
		this.ccFieldCheck.setChecked(this.ccCombo.getValue()!='');

		Ext.getBody().unmask();
		GO.email.EmailComposer.superclass.show.call(this);
	},
	
	addSignature : function(accountRecord){
		accountRecord = accountRecord || this.fromCombo.store.getById(this.fromCombo.getValue());
			
		var sig = accountRecord.get(this.formPanel.baseParams.content_type+"_signature");
		
		if(!GO.util.empty(sig))
		{
			if(this.formPanel.baseParams.content_type=='plain')
			{
				sig = "\n"+sig+"\n";
			}else
			{
				sig = '<br />'+sig+'<br />';
			}
		}
		
		this.editor.setValue(sig+this.editor.getValue());
	},

	showAttachmentsDialog : function() {
		if (!this.attachmentsDialog) {
			var tbar = [];

			tbar.push({
				// id : 'add-local',
				iconCls : 'btn-add',
				text : GO.email.lang.attachFilesPC,
				cls : 'x-btn-text-icon',
				handler : function() {

					this.uploadDialog.show();
				},
				scope : this
			});

			if (GO.files) {
				tbar.push({
					// id : 'add-remote',
					iconCls : 'btn-add',
					text : GO.email.lang.attachFilesGO,
					cls : 'x-btn-text-icon',
					handler : function() {
						if (!this.fileBrowser) {
							this.fileBrowser = new GO.files.FileBrowser({
								border : false,
								fileClickHandler : this.addRemoteFiles,
								scope : this
							});

							this.fileBrowserWindow = new Ext.Window({
								title : GO.lang.strSelectFiles,
								height : 480,
								width : 680,
								layout : 'fit',
								border : false,
								closeAction : 'hide',
								items : this.fileBrowser,
								buttons : [{
									text : GO.lang.cmdOk,
									handler : this.addRemoteFiles,
									scope : this
								}, {
									text : GO.lang.cmdClose,
									handler : function() {
										this.fileBrowserWindow
										.hide();
									},
									scope : this
								}]
							});
						}
						this.fileBrowserWindow.show();
					},
					scope : this
				});
			}

			tbar.push({
				// id : 'delete',
				iconCls : 'btn-delete',
				text : GO.lang.cmdDelete,
				cls : 'x-btn-text-icon',
				handler : function() {

					var rows = this.attachmentsGrid.selModel
					.getSelections();
					for (var i = 0; i < rows.length; i++)
						this.attachmentsStore.remove(rows[i]);

				},
				scope : this
			});

			this.attachmentsGrid = new GO.grid.GridPanel({
				// id : 'groups-grid-overview-users',
				store : this.attachmentsStore,
				loadMask:true,
				columns : [{
					header : GO.lang.strName,
					dataIndex : 'name'
				}, {
					header : GO.lang.strSize,
					dataIndex : 'size',
					renderer: function(v){
						return  Ext.util.Format.fileSize(v);
					}
				}, {
					header : GO.lang.strType,
					dataIndex : 'type'
				}],

				sm : new Ext.grid.RowSelectionModel({
					singleSelect : false
				}),
				view : new Ext.grid.GridView({
					forceFit : true,
					autoFill : true
				}),
				tbar : tbar
			});

			this.attachmentsDialog = new Ext.Window({
				title : GO.email.lang.attachments,
				layout : 'fit',
				modal : false,
				closeAction : 'hide',
				minWidth : 300,
				minHeight : 300,
				height : 400,
				width : 600,
				items : this.attachmentsGrid,
				buttons : [{
					text : GO.lang.cmdClose,
					handler : function() {
						this.attachmentsDialog.hide()
					},
					scope : this
				}]
			});

			var uploadFile = new GO.form.UploadFile({
				inputName : 'attachments',
				addText : GO.lang.smallUpload
			});

			this.upForm = new Ext.form.FormPanel({
				fileUpload : true,
				waitMsgTarget : true,
				items : [uploadFile, new Ext.Button({
					text : GO.lang.largeUpload,
					handler : function() {
						if (!deployJava.isWebStartInstalled('1.5.0')) {
							Ext.MessageBox.alert(GO.lang.strError,
								GO.lang.noJava);
						} else {
							var local_path = this.local_path
							? 'true'
							: false;

							/*
									 * crashes firefox in ubuntu GO.util.popup({
									 * url : GO.settings.modules.email.url +
									 * 'jupload/index.php', width : 640, height :
									 * 500 });
									 */
							window.open(GO.settings.modules.email.url+'jupload/index.php');

							this.uploadDialog.hide();
							// for refreshing by popup
							GO.attachmentsStore = this.attachmentsStore;
						}
					},
					scope : this
				})],
				cls : 'go-form-panel'
			});

			this.uploadDialog = new Ext.Window({
				title : GO.email.lang.uploadAttachments,
				layout : 'fit',
				modal : true,
				height : 300,
				width : 300,
				items : this.upForm,
				closeAction:'hide',
				buttons : [{
					text : GO.email.lang.startTransfer,
					handler : function() {
						this.upForm.form.submit({
							waitMsg : GO.lang.waitMsgUpload,
							url : GO.settings.modules.email.url
							+ 'action.php',
							params : {
								task : 'attach_file'
							},
							success : function(form, action) {

								this.attachmentsStore.loadData({
									'results' : action.result.files
								}, true);

								uploadFile.clearQueue();

								this.uploadDialog.hide();

							},
							scope : this
						});
					},
					scope : this
				}, {
					text : GO.lang.cmdClose,
					handler : function() {
						this.uploadDialog.hide()
					},
					scope : this
				}]
			});
		}
		this.attachmentsDialog.show();
	},

	addRemoteFiles : function() {

		var AttachmentRecord = Ext.data.Record.create([{
			name : 'tmp_name'
		},{
			name : 'id'
		}, {
			name : 'name'
		}, {
			name : 'type'
		}, {
			name : 'size'
		}]);

		var selections = this.fileBrowser.getSelectedGridRecords();

		for (var i = 0; i < selections.length; i++) {
			var newRecord = new AttachmentRecord({
				id : selections[i].data.id,
				tmp_name : selections[i].data.id,
				name : selections[i].data.name,
				type : selections[i].data.type,
				size : selections[i].data.size
			});
			newRecord.id = selections[i].data.path;
			this.attachmentsStore.add(newRecord);
		}
		this.updateAttachmentsButton();
		this.fileBrowserWindow.hide();

	},

	sendMail : function(draft, autoSave) {

		if (this.uploadDialog && this.uploadDialog.isVisible()) {
			alert(GO.email.lang.closeUploadDialog);
			this.attachmentsDialog.show();
			this.uploadDialog.show();
			return false;
		}

		if (autoSave || this.subjectField.getValue() != ''
			|| confirm(GO.email.lang.confirmEmptySubject)) {
			if (this.attachmentsDialog && this.attachmentsDialog.isVisible()) {
				this.attachmentsDialog.hide();
			}

			if (this.attachmentsStore && this.attachmentsStore.data.keys.length) {
				
				var attachments = [];
				
				var records = this.attachmentsStore.getRange();
				for(var i=0;i<records.length;i++)
				{
					attachments.push(Ext.util.Format.htmlDecode(records[i].get('tmp_name')));
				}
				
				this.sendParams['attachments'] = Ext.encode(attachments);
			}

			this.sendParams['inline_attachments'] = Ext
			.encode(this.inline_attachments);

			this.sendParams.draft = draft;

			// extra sync to make sure all is in there.
			this.htmlEditor.syncValue();
			
			this.saveButton.setDisabled(true);
			this.sendButton.setDisabled(true);

			this.formPanel.form.submit({
				url : this.sendURL,
				params : this.sendParams,
				waitMsg : autoSave ? null : GO.lang.waitMsgSave,
				waitMsgTarget : autoSave ? null : this.formPanel.body,
				success : function(form, action) {
					
					this.saveButton.setDisabled(false);
					this.sendButton.setDisabled(false);
					
					if (action.result.account_id) {
						this.account_id = action.result.account_id;
					}

					if(!draft)
					{
						if (this.callback) {
							if (!this.scope) {
								this.scope = this;
							}
	
							var callback = this.callback.createDelegate(this.scope);
							callback.call();
						}
						// this.reset();
	
						if (GO.addressbook && action.result.unknown_recipients
							&& action.result.unknown_recipients.length) {
							if (!GO.email.unknownRecipientsDialog)
								GO.email.unknownRecipientsDialog = new GO.email.UnknownRecipientsDialog();
	
							GO.email.unknownRecipientsDialog.store.loadData({
								recipients : action.result.unknown_recipients
							});
	
							GO.email.unknownRecipientsDialog.show();
						}
	
						this.fireEvent('send', this);
					
						this.hide();
					}else
					{
						this.sendParams.draft_uid = action.result.draft_uid;
						
						this.fireEvent('save', this);
					}
				},

				failure : function(form, action) {
					if(!autoSave)
					{
						var fb = action.result && action.result.feedback ? action.result.feedback : GO.lang.strRequestError;
						Ext.MessageBox.alert(GO.lang.strError, fb);
					}
					this.saveButton.setDisabled(false);
					this.sendButton.setDisabled(false);
				},
				scope : this

			});
		} else {
			this.subjectField.focus();
		}
	},

	onShowFieldCheck : function(check, checked) {
		
		switch (check.id) {
			case this.formFieldCheck.id :
				this.fromCombo.getEl().up('.x-form-item').setDisplayed(checked);
				break;

			case this.ccFieldCheck.id :
				this.showCC(checked);
				break;

			case this.bccFieldCheck.id :
				this.showBCC(checked);
				break;
		}
		this.setEditorHeight();
	},

	setEditorHeight : function() {
		
		var subjectEl = this.subjectField.getEl().up('.x-form-item');
		var height = subjectEl.getHeight()+subjectEl.getMargins('tb');
		
		if(GO.mailings)
		{
			var slEl = this.selectLinkField.getEl().up('.x-form-item');
			height += slEl.getHeight()+slEl.getMargins('tb');
		}
	
		if (this.toComboVisible) {
			var toEl = this.toCombo.getEl().up('.x-form-item');
			height += toEl.getHeight()+toEl.getMargins('tb');
		}

		var el;
		for (var i = 0; i < this.showMenu.items.items.length; i++) {
			if (this.showMenu.items.items[i].checked) {
				if(i==0)
				{
					el=this.fromCombo.getEl().up('.x-form-item');
				}else
				{
					el=this.toCombo.getEl().up('.x-form-item');
				}
				height += el.getHeight()+el.getMargins('tb');
			}
		}
		height+=4;
		
		var newAnchor = "100% -"+height;

		//reset anchor and delete cached anchorSpec
		this.htmlEditor.anchor=newAnchor;
		delete this.htmlEditor.anchorSpec;
		this.textEditor.anchor=newAnchor;
		delete this.textEditor.anchorSpec;
		
		this.htmlEditor.syncSize();
		this.formPanel.doLayout();
	}
});

GO.email.TemplatesList = function(config) {

	Ext.apply(config);
	var tpl = new Ext.XTemplate(
		'<div id="template-0" class="go-item-wrap">'+GO.mailings.lang.noTemplate+'</div>',
		'<tpl for=".">',
		'<div id="template-{id}" class="go-item-wrap"">{name}</div>',
		'<tpl if="!GO.util.empty(default_template)"><div class="ml-template-default-spacer"></div></tpl>',
		'</tpl>');

	GO.email.TemplatesList.superclass.constructor.call(this, {
		store : config.store,
		tpl : tpl,
		singleSelect : true,
		autoHeight : true,
		overClass : 'go-view-over',
		itemSelector : 'div.go-item-wrap',
		selectedClass : 'go-view-selected'
	});
}

Ext.extend(GO.email.TemplatesList, Ext.DataView, {
	onRender : function(ct, position) {
		this.el = ct.createChild({
			tag : 'div',
			cls : 'go-select-list'
		});

		GO.email.TemplatesList.superclass.onRender.apply(this,
			arguments);
	}

});
