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
						'<div class="menu-title"><img class="x-menu-item-icon" src="'
								+ Ext.BLANK_IMAGE_URL + '" />'
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
			
			optionsMenuItems.push({
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
			});
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
							tmp_file : plugin.selectedPath,
							url : plugin.selectedUrl
						});
			}, this);

	this.selectLinkField = new GO.form.SelectLink({
				anchor : '100%'
			});

	this.formPanel = new Ext.form.FormPanel({
				border : false,
				labelWidth : 100,
				waitMsgTarget : true,
				baseParams: {content_type:'html'},
				cls : 'go-form-panel',
				url : 'save-form.php',
				defaultType : 'textfield',
				items : [
						this.fromCombo = new Ext.form.ComboBox({
									store : new GO.data.JsonStore({
												url : BaseHref
														+ 'modules/email/json.php',
												baseParams : {
													"task" : 'accounts',
													personal_only : true
												},
												fields : ['id', 'email', 'signature'],
												root : 'results',
												totalProperty : 'total',
												id : 'id'
											}),
									fieldLabel : GO.email.lang.from,
									name : 'account_name',
									anchor : '100%',
									displayField : 'email',
									valueField : 'id',
									hiddenName : 'account_id',
									forceSelection : true,
									triggerAction : 'all',
									mode : 'local'
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

								}), this.selectLinkField,
						this.subjectField = new Ext.form.TextField({
									fieldLabel : GO.email.lang.subject,
									name : 'subject',
									anchor : '100%'
								}), this.htmlEditor = new Ext.form.HtmlEditor({
									hideLabel : true,
									name : 'body',
									anchor : '100% -130',
									plugins : imageInsertPlugin
								}), this.textEditor = new Ext.form.TextArea({
									name: 'textbody',
									anchor : '100% -130',
									hideLabel : true
								})]
			});
			
	this.htmlEditor.on('change', function(){this.changesMadeForAutoSave=true}, this);


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
					fields : ['id', 'name'],
					remoteSort : true
				});

		this.templatesList = new GO.email.TemplatesList({
					store : this.templatesStore
				});

		this.templatesList.on('click', function(dataview, index) {

					this.showConfig.template_id = index > 0
							? dataview.store.data.items[index - 1].id
							: 0;
					this.show(this.showConfig);
					this.templatesWindow.hide();
					this.templatesList.clearSelections();

				}, this);

		this.templatesWindow = new Ext.Window({
					title : GO.email.lang.selectTemplate,
					layout : 'fit',
					modal : false,
					height : 400,
					width : 600,
					closable : true,
					closeAction : 'hide',
					items : new Ext.Panel({
								autoScroll : true,
								items : this.templatesList,
								cls : 'go-form-panel'
							})
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
		if(this.lastAutoSave && this.lastAutoSave!=this.editor.getValue())
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

		
		this.on('resize', this.setEditorHeight, this);
		
		this.autoSaveTask={
		    run: this.autoSave,
		    scope:this,
		    interval:120000
		    // interval:5000
		};
		
		this.on('hide', this.stopAutoSave, this);
		
	},

	toComboVisible : true,

	updateAttachmentsButton : function() {

		var text = this.attachmentsStore.getCount() > 0
				? GO.email.lang.attachments + ' ('
						+ this.attachmentsStore.getCount() + ')'
				: GO.email.lang.attachments;

		this.attachmentsButton.setText(text);
	},

	reset : function() {

		this.sendParams = {
			'task' : 'sendmail',
			notification : 'false',
			priority : '3',
			draft_uid : 0,
			inline_attachments : {}
		};
		this.inline_attachments = Array();
		this.formPanel.form.reset();

		if (this.defaultAcccountId) {
			this.fromCombo.setValue(this.defaultAcccountId);
		}

		this.notifyCheck.setChecked(false);
		this.normalPriorityCheck.setChecked(true);

		if (this.attachmentsGrid) {
			this.attachmentsGrid.store.loadData({
						results : []
					});
		}
	},

	show : function(config) {
		
		this.showConfig=config;

		if (!this.rendered) {

			this.fromCombo.store.on('load', function() {
						var records = this.fromCombo.store.getRange();
						if (records.length) {
							if (!config.account_id) {
								config.account_id = records[0].data.id;
							}

							this.render(Ext.getBody());
							
							
							
							this.ccCombo.getEl().up('.x-form-item').setDisplayed(false);
							this.bccCombo.getEl().up('.x-form-item').setDisplayed(false);

							this.show(config);
							return;

						} else {

							Ext.Msg.alert(GO.email.lang.noAccountTitle,
									GO.email.lang.noAccount);
						}
					}, this, {
						single : true
					});

			if (!GO.mailings) {
				config.template_id = 0;
				this.fromCombo.store.load();
			} else {

				this.templatesStore.load({
							callback : function() {
								this.fromCombo.store.load();
							},
							scope : this
						});
			}

		} else if (config.template_id == undefined && this.templatesStore
				&& this.templatesStore.getTotalCount() > 1) {
			this.showConfig = config;
			this.templatesWindow.show();
		} else {
			this.updateAttachmentsButton();

			this.toComboVisible = true;
			this.showMenuButton.setDisabled(false);
			this.toCombo.getEl().up('.x-form-item').setDisplayed(true);
			this.sendURL = GO.settings.modules.email.url + 'action.php';
			this.saveButton.setDisabled(false);

			if (config.template_id == undefined && this.templatesStore
					&& this.templatesStore.getTotalCount() == 1) {
				config.template_id = this.templatesStore.data.items[0]
						.get('id');
			}

			this.attachmentsStore.removeAll();
			this.inline_attachments = [];
			this.reset();
			if (config.account_id) {
				this.fromCombo.setValue(config.account_id);
			} else {
				this.fromCombo.setValue(this.fromCombo.store.data.items[0].id);
			}

			if (config.values) {
				this.formPanel.form.setValues(config.values);
			}
			
			if(!config.keepEditingMode)
			{
				this.setContentTypeHtml(GO.email.useHtmlMarkup);
				this.htmlCheck.setChecked(GO.email.useHtmlMarkup, true);
			}
			
			GO.email.EmailComposer.superclass.show.call(this);
			
			if(config.move)
			{
				var pos = this.getPosition();
		 		this.setPagePosition(pos[0]+config.move, pos[1]+config.move);
			}			
			
			this.startAutoSave();

			if (config.uid || config.template_id || config.loadUrl) {
				if (!config.task) {
					config.task = 'template';
				}
				
				if(config.task=='opendraft')
					this.sendParams.draft_uid = config.uid; 

				var params = config.loadParams ? config.loadParams : {
					uid : config.uid,
					account_id : this.fromCombo.getValue(),
					task : config.task,
					mailbox : config.mailbox,
					template_id : config.template_id
				};

				if (config.template_id > 0) {
					params.to = this.toCombo.getValue();
				}

				var url = config.loadUrl
						? config.loadUrl
						: GO.settings.modules.email.url + 'json.php';

				// for mailings plugin
				if (config.mailing_group_id > 0) {
					this.sendURL = GO.settings.modules.mailings.url
							+ 'action.php';

					this.toComboVisible = false;
					this.showMenuButton.setDisabled(true);
					this.toCombo.getEl().up('.x-form-item').setDisplayed(false);
					this.ccCombo.getEl().up('.x-form-item').setDisplayed(false);
					this.bccCombo.getEl().up('.x-form-item')
							.setDisplayed(false);

					this.sendParams.mailing_group_id = config.mailing_group_id;

					// so that template loading won't replace fields
					params.mailing_group_id = config.mailing_group_id;

					this.saveButton.setDisabled(true);
				}

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

						/*
						 * if (action.result.replace_personal_fields) {
						 * this.sendParams['replace_personal_fields'] = '1'; }

						this.bccFieldCheck.setChecked(this.bccCombo.getValue()!='');
						this.ccFieldCheck.setChecked(this.ccCombo.getValue()!='');
					
						
						if(params.task!='opendraft')
						{
							var accountRecord = this.fromCombo.store.getById(this.fromCombo.getValue());
							this.editor.setValue(accountRecord.data.signature+this.editor.getValue());
						}
						
						if (this.toCombo.getValue() == '') {
							this.toCombo.focus();
						} else {
							this.editor.focus();
						}
						 */
						
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

		this.bccFieldCheck.setChecked(this.bccCombo.getValue()!='');
		this.ccFieldCheck.setChecked(this.ccCombo.getValue()!='');
				
		if(addSignature)
		{
			var accountRecord = this.fromCombo.store.getById(this.fromCombo.getValue());
			this.editor.setValue(accountRecord.data.signature+this.editor.getValue());
		}
		this.bodyContentAtWindowOpen=this.editor.getValue();	
		
		if (this.toCombo.getValue() == '') {
			this.toCombo.focus();
		} else {
			this.editor.focus();
		}
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
									dataIndex : 'size'
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
				modal : false,
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
						id : selections[i].data.path,
						tmp_name : selections[i].data.path,
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
		
		this.saveButton.setDisabled(true);
		this.sendButton.setDisabled(true);
		

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
						Ext.MessageBox.alert(GO.lang.strError,
							action.result.feedback);
					}
					this.saveButton.setDisabled(false);
					this.sendButton.setDisabled(false);
				},
				scope : this

			});
		} else {
			this.saveButton.setDisabled(false);
			this.sendButton.setDisabled(false);
			this.subjectField.focus();
		}
	},

	onShowFieldCheck : function(check, checked) {
		switch (check.id) {
			case this.formFieldCheck.id :
				this.fromCombo.getEl().up('.x-form-item').setDisplayed(checked);
				break;

			case this.ccFieldCheck.id :
				this.ccCombo.getEl().up('.x-form-item').setDisplayed(checked);
				break;

			case this.bccFieldCheck.id :
				this.bccCombo.getEl().up('.x-form-item').setDisplayed(checked);
				break;
		}
		// this.doLayout();
		this.setEditorHeight();
	},

	setEditorHeight : function() {
		var height = this.subjectField.el.getHeight()
				+ this.selectLinkField.el.getHeight();
		// this.tbar.getHeight()
		if (this.toComboVisible) {
			height += this.toCombo.el.getHeight() + 3;
		}

		for (var i = 0; i < this.showMenu.items.items.length; i++) {
			if (this.showMenu.items.items[i].checked) {
				if(i==0)
				{
					height += this.fromCombo.el.getHeight() + 3;
				}else
				{
					height += this.toCombo.el.getHeight() + 3;
				}
			}
		}

		var innerHeight = this.getInnerHeight();
		var newHeight = innerHeight - height;

		this.htmlEditor.setHeight(newHeight);
		this.htmlEditor.setWidth(this.getInnerWidth() - 10);
		this.htmlEditor.syncSize();
		
		this.textEditor.setHeight(newHeight);
		this.textEditor.setWidth(this.getInnerWidth() - 10);
		this.textEditor.syncSize();
	}
});

GO.email.TemplatesList = function(config) {

	Ext.apply(config);
	var tpl = new Ext.XTemplate(
			'<div id="template-0" class="go-item-wrap">No template</div>',
			'<tpl for=".">',
			'<div id="template-{id}" class="go-item-wrap">{name}</div>',
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
