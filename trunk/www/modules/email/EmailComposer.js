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

	this.cls='em-composer';
	
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
								 	
				if(this.emailEditor.isDirty() || confirm(GO.email.lang.confirmLostChanges))
				{
					this.emailEditor.setContentTypeHtml(checked);
					/**
					 * reload dialog for text or html
					 */
					this.showConfig.keepEditingMode=true;
					var v = this.formPanel.form.getValues();
					delete v.body;
					delete v.textbody;
					
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
							this.emailEditor.setContentTypeHtml(false);
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
		tpl: '<tpl for="."><div class="x-combo-list-item">{name:htmlEncode}</div></tpl>'
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
			fields : ['full_email','info'],
			root : 'persons'
		}),
		valueField : 'full_email',
		displayField : 'info'
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
						
	if(GO.settings.modules.savemailas && GO.settings.modules.savemailas.read_permission)
	{
		if (!this.selectLinkField) {
			this.selectLinkField = new GO.form.SelectLink({
				anchor : '100%'
			});					
			anchor+=26;
			items.push(this.selectLinkField);
		}
	}

	try {
		if(config && config.links)
		{
			if (!this.selectLinkField) {
				this.selectLinkField = new GO.form.SelectLink({
					anchor : '100%'
				});
				anchor+=26;
				items.push(this.selectLinkField);
			}
		}
	} catch(e) {}

	items.push(this.subjectField = new Ext.form.TextField({
		fieldLabel : GO.email.lang.subject,
		name : 'subject',
		anchor : '100%'
	}));

	this.emailEditor = new GO.base.email.EmailEditorPanel({
		region:'center'
	});
	
	this.formPanel = new Ext.form.FormPanel({
		border : false,		
		waitMsgTarget : true,
		cls : 'go-form-panel',		
		layout:"border",
		items : [{
			region:"north",
			layout:'form',
			labelWidth : 100,
			defaultType : 'textfield',
			autoHeight:true,
			border:false,
			items: items
		},this.emailEditor],
		keys:[{
			key: Ext.EventObject.ENTER,
			ctrl:true,
			fn: function(key, e){
				this.sendMail(false,false);
			},
			scope:this
		}]
	});

	//Set a long timeout for large attachments
	this.formPanel.form.timeout=3000;

	this.templatesStore = new GO.data.JsonStore({
		url : GO.url("addressbook/template/emailSelection"),
		baseParams : {
			'type':"0"
		},
		root : 'results',
		totalProperty : 'total',
		id : 'id',
		fields : ['id', 'name', 'group', 'text','template_id','checked'],
		remoteSort : true
	});

	var tbar = [this.sendButton = new Ext.Button({
		text : GO.email.lang.send,
		iconCls : 'btn-send',
		handler : function() {
			this.sendMail();
		},
		scope : this
	}), this.saveButton = new Ext.Button({
		iconCls : 'btn-save',
		text : GO.lang.cmdSave,
		handler : function() {
			this.sendMail(true);
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
	})];

	tbar.push(this.emailEditor.getAttachmentsButton());

	if (GO.addressbook) {
		tbar.push({
			text : GO.addressbook.lang.addressbook,
			iconCls : 'btn-addressbook',
			handler : function() {
				if (!this.addressbookDialog) {
					this.addressbookDialog = new GO.email.AddressbookDialog();
					this.addressbookDialog.on('addrecipients',
						function(fieldName, selections) {
							this.addRecipients(fieldName,selections);
						}, this);
				}

				this.addressbookDialog.show();
			},
			scope : this
		});
	}

	if(GO.addressbook){
		tbar.push(this.templatesBtn = new Ext.Button({

			iconCls:'ml-btn-mailings',
			text:GO.addressbook.lang.emailTemplate,
			menu:this.templatesMenu = new GO.menu.JsonMenu({
				store:this.templatesStore,
				listeners:{
					scope:this,
					itemclick : function(item, e ) {
						if(item.template_id=='default'){
							this.templatesStore.baseParams.default_template_id=this.showConfig.template_id;
							this.templatesStore.load();
							delete this.templatesStore.baseParams.default_template_id;
						}else if(this.emailEditor.isDirty() || confirm(GO.email.lang.confirmLostChanges))
						{
							this.showConfig.template_id=item.template_id;
							this.showConfig.keepEditingMode=true;
							this.show(this.showConfig);
						}else
						{
							return false;							
						}
					}
				}
			})
		}));
	}

	var focusFn = function() {
		this.toCombo.focus();
	};

	GO.email.EmailComposer.superclass.constructor.call(this, {
		title : GO.email.lang.composeEmail,
		width : 750,
		height : 500,
		minWidth : 300,
		minHeight : 200,
		layout : 'fit',
		maximizable : true,
		collapsible : true,
		animCollapse : false,
		//plain : true,
		closeAction : 'hide',
		buttonAlign : 'center',
		focus : focusFn.createDelegate(this),
		tbar : tbar,
		items : this.formPanel
	});

	this.addEvents({
		'dialog_ready' :true,
		//		attachmentDblClicked : true,
		zipOfAttachmentsDblClicked : true,
		'send' : true,
		'reset' : true,
		afterShowAndLoad:true,
		beforesendmail:true

	});
};

Ext.extend(GO.email.EmailComposer, GO.Window, {

	stateId : 'email-composer',
	
	showConfig : {},

	autoSaveTask : {},
	
	lastAutoSave : false,

	/*
	 *handles ctrl+enter from html editor
	 */
	fireSubmit : function(e) {
		if (e.ctrlKey && Ext.EventObject.ENTER == e.getKey()) {
			//e.stopEvent();
			this.sendMail(false, false);
		}
	},
	
	autoSave : function(){
		if(GO.util.empty(this.sendParams.addresslist_id) && this.lastAutoSave && this.lastAutoSave!=this.editor.getValue())
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

	reset : function(keepAttachmentsAndOptions) {
		if(!keepAttachmentsAndOptions){
			this.sendParams = {
				task : 'sendmail',
				inline_attachments : {},
				inline_temp_attachments : {},
				notification : 'false',
				priority : '3',
				draft_uid : 0
			};

			this.showCC((GO.email.showCCfield == '1') ? true : false);
			this.showBCC((GO.email.showBCCfield == '1') ? true : false);			
			this.ccFieldCheck.setChecked((GO.email.showCCfield == '1') ? true : false);
			this.bccFieldCheck.setChecked((GO.email.showBCCfield == '1') ? true : false);
			
			if (this.defaultAcccountId) {
				this.fromCombo.setValue(this.defaultAcccountId);
			}
			this.notifyCheck.setChecked(false);
			this.normalPriorityCheck.setChecked(true);
	
		}else
		{
			//keep options when switching from text <> html
			this.sendParams={
				notification : this.sendParams.notification,
				priority : this.sendParams.priority,
				draft_uid : this.sendParams.draft_uid,
				reply_uid : this.sendParams.reply_uid,
				reply_mailbox : this.sendParams.reply_mailbox,
				in_reply_to : this.sendParams.in_reply_to,
				forward_uid : this.sendParams.forward_uid,
				forward_mailbox : this.sendParams.forward_mailbox
			};
			
		}

		this.formPanel.form.reset();
		
		this.fireEvent("reset", this);
	},

	showCC : function(show){
		this.ccCombo.getEl().up('.x-form-item').setDisplayed(show);
		if(show)
		{
			this.ccCombo.onResize();
		}		
		this.doLayout();
	},
	
	showBCC : function(show){
		this.bccCombo.getEl().up('.x-form-item').setDisplayed(show);		
		if(show)
		{
			this.bccCombo.onResize();
		}
		this.doLayout();
	},

	addRecipients : function(fieldName,selections) {
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
	},
//
//	setRecipients : function(fieldName,selections) {
//		var field = this.formPanel.form.findField(fieldName);
//		field.setValue(selections);
//		field.store.load();
//	},

	initTemplateMenu :  function(config){
		if (typeof(config.template_id) == 'undefined' && this.templatesStore){
			var templateRecordIndex = this.templatesStore.findBy(function(record,id){
				return record.get('checked');
			});

			if(templateRecordIndex>-1)
				config.template_id=this.templatesStore.getAt(templateRecordIndex).get('template_id');
		}

		//check the right template menu item.
		if(this.templatesStore && config.template_id && this.templatesMenu.items){
			var item = this.templatesMenu.items.find(function(item){
				return item.template_id==config.template_id;
			});
			item.setChecked(true);
		}
	},
	
	initFrom : function(config){
		var index=-1;
		if (config.account_id) {
			index = this.fromCombo.store.findBy(function(record, id){
				return record.get('account_id')==config.account_id;
			});
		}

		//find by e-mail
		if(config.from){
			index = this.fromCombo.store.findBy(function(record, id){
				return record.get('email')==config.from;
			});
		}
		if(index==-1)
		{
			index=0;
		}
		this.fromCombo.setValue(this.fromCombo.store.data.items[index].id);
	},

	show : function(config) {

		//TODO enable after testing
		//Ext.getBody().mask(GO.lang.waitMsgLoad);

		delete this.link_config;

		this.showConfig=config;
		
		if (!this.rendered) {
				
			GO.request({
				url: 'core/multiRequest',
				params:{
					requests:Ext.encode({
						templates:{r:'addressbook/template/emailSelection'},
						aliases:{r:'email/alias/store'}
					})
				},
				success: function(options, response, result)
				{
					this.fromCombo.store.loadData(result.aliases);

					if(this.templatesStore)
						this.templatesStore.loadData(result.templates);              
					
					Ext.getBody().unmask();

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
					
				},
				scope:this
			});
			
			//this.htmlEditor.SpellCheck = false;
		} else {

			this.initTemplateMenu(config);
			
			//keep attachments when switchting from text <> html
			this.reset(config.keepEditingMode);
			
			//save the mail to a file location
			if(config.saveToPath){
				this.sendParams.save_to_path=config.saveToPath;
				this.sendButton.hide();
			}else
			{
				this.sendButton.show();
			}

			this.initFrom(config);

			if (config.values) {
				this.formPanel.form.setValues(config.values);
			}

			//this will be true when swithing from html to text or vice versa
			if(!config.keepEditingMode)
			{
				//remove attachments if not switching edit mode
				this.emailEditor.setAttachments();				
				this.emailEditor.setContentTypeHtml(GO.email.useHtmlMarkup);
				
				this.htmlCheck.setChecked(GO.email.useHtmlMarkup, true);
				if(this.encryptCheck)
					this.encryptCheck.setChecked(false, true);
			}			

			this.toComboVisible = true;
			this.showMenuButton.setDisabled(false);
			this.toCombo.getEl().up('.x-form-item').setDisplayed(true);
			this.sendURL = GO.url('email/message/send');
			this.saveButton.setDisabled(false);
		
			this.notifyCheck.setChecked(GO.email.alwaysRequestNotification);
			
			if(config.move)
			{
				var pos = this.getPosition();
				this.setPagePosition(pos[0]+config.move, pos[1]+config.move);
			}			
			
			// for mailings plugin
			if (config.addresslist_id > 0) {
				this.sendURL = GO.url("addressbook/sentMailing/send");

				this.toComboVisible = false;
				this.showMenuButton.setDisabled(true);
				this.toCombo.getEl().up('.x-form-item').setDisplayed(false);
				this.showCC(false);
				this.showBCC(false);

				this.sendParams.addresslist_id = config.addresslist_id;

				this.saveButton.setDisabled(true);
			}else
			{
//				this.ccFieldCheck.setChecked(GO.email.showCCfield == '1');
//				this.bccFieldCheck.setChecked(GO.email.showBCCfield == '1');
			}

			if (config.uid || config.template_id || config.loadUrl) {
		
//				if(config.task=='opendraft')
//					this.sendParams.draft_uid = config.uid;
//				
				var fromRecord = this.fromCombo.store.getById(this.fromCombo.getValue());

				var params = config.loadParams ? config.loadParams : {
					uid : config.uid,
					account_id : fromRecord.get('account_id'),
					task : config.task,
					mailbox : config.mailbox
				};

				//for directly loading a contact in a template
				if(config.contact_id)
					params.contact_id=config.contact_id;
				
				if (config.addresslist_id > 0) {
					// so that template loading won't replace fields
					params.addresslist_id = config.addresslist_id;
				}

				//if (config.template_id>0) {
				params.template_id=config.template_id;
				params.to = this.toCombo.getValue();
				//}
				
				var url;
				
				if(!config.task)
					config.task='template';
				
				if(config.loadUrl)
				{
					url = config.loadUrl;
				}else if(config.task=='reply_all'){
					url = GO.url("email/message/reply");				
					params.replyAll=true;
				}else
				{
					url = GO.url("email/message/"+config.task);				
				}

				//sometimes this is somehow copied from the baseparams
				params.content_type = this.emailEditor.getContentType();

				if (typeof(config.values)!='undefined' && typeof(config.values.body)!='undefined')
					params.body = config.values.body;

				this.formPanel.form.load({
					url : url,
					params : params,
					waitMsg : GO.lang.waitMsgLoad,
					failure:function(form, action)
					{
						Ext.getBody().unmask();
						Ext.Msg.alert(GO.lang['strError'], action.result.feedback)
					},
					success : function(form, action) {

						if (config.task == 'reply'
							|| config.task == 'reply_all') {
							this.sendParams['reply_uid'] = config.uid;
							this.sendParams['reply_mailbox'] = config.mailbox;
							this.sendParams['in_reply_to']=action.result.data.in_reply_to;
						}else if (config.task == 'forward'){
							this.sendParams['forward_uid'] = config.uid;
							this.sendParams['forward_mailbox'] = config.mailbox;
						}

//						if (!config.keepEditingMode && action.result.data.attachments) {
//							this.attachmentsStore.loadData({
//								results : action.result.data.attachments
//							}, true);
//						}

						this.afterShowAndLoad(params.task!='opendraft', config);

						this.fireEvent('dialog_ready', this);
					},
					scope : this
				});

			}else
			{
				this.afterShowAndLoad(true, config);
			}
			if (config.link_config) {
				this.link_config = config.link_config;
				if (config.link_config.modelNameAndId) {
					this.selectLinkField.setValue(config.link_config.modelNameAndId);
					this.selectLinkField.setRemoteText(config.link_config.text);
				}
			}
		}
	},
	

	
	afterShowAndLoad : function(addSignature, config){

		//this.startAutoSave();


		this.ccFieldCheck.setChecked(this.ccCombo.getValue()!='');
		this.bccFieldCheck.setChecked(this.bccCombo.getValue()!='');
	
		if(config.afterLoad)
		{
			if(!config.scope)
				config.scope=this;
			config.afterLoad.call(config.scope);
		}

		Ext.getBody().unmask();
		GO.email.EmailComposer.superclass.show.call(this);


		if (this.toCombo.getValue() == '') {
			this.toCombo.focus();
		} else {
			this.emailEditor.focus();
		}
		
		this.fireEvent('afterShowAndLoad',this);
	},
	

	HandleResult : function (btn){
		if (btn == 'yes'){
			//this.htmlEditor.SpellCheck = true;
			this.sendMail();
		}else{
			//this.editor.plugins[1].spellcheck();
		}
	},

	submitForm : function(hide){
		this.sendMail(false, false);
	},

	sendMail : function(draft, autoSave) {
		//prevent double send with ctrl+enter
		if(this.sendButton.disabled){
			return false;
		}		

		/*if (this.isHTML() && this.htmlEditor.SpellCheck == false && !draft){
			//Ask if they want to run a spell check
			Ext.MessageBox.confirm(GO.lang.strConfirm, GO.lang.spellcheckAsk, function (btn){self.HandleResult(btn,self);});
			return false;
		}*/
		
		
		if(!draft && !autoSave && !this.fireEvent('beforesendmail', this))
			return false;
		

		if (this.uploadDialog && this.uploadDialog.isVisible()) {

			if(autoSave)
				return false;

			alert(GO.email.lang.closeUploadDialog);
			this.attachmentsDialog.show();
			this.uploadDialog.show();
			return false;
		}

		this.saveButton.setDisabled(true);
		this.sendButton.setDisabled(true);

		if (autoSave || this.subjectField.getValue() != ''
			|| confirm(GO.email.lang.confirmEmptySubject)) {
			if (this.attachmentsDialog && this.attachmentsDialog.isVisible()) {
				this.attachmentsDialog.hide();
			}

			this.sendParams.draft = draft;

			// extra sync to make sure all is in there.
			//this.htmlEditor.syncValue();

			var waitMsg=null;
			if(!autoSave){
				waitMsg = draft ? GO.lang.waitMsgSave : GO.email.lang.sending;
			}
			
			if(!autoSave && !draft){
				this.stopAutoSave();
			}

			this.formPanel.form.submit({
				url : this.sendURL,
				params : this.sendParams,
				waitMsg : waitMsg,
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

						if (this.link_config && this.link_config.callback) {
							this.link_config.callback.call(this);
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
			this.saveButton.setDisabled(false);
			this.sendButton.setDisabled(false);
		}
	},

	onShowFieldCheck : function(check, checked) {
		
		switch (check.id) {
			case this.formFieldCheck.id :
				this.fromCombo.getEl().up('.x-form-item').setDisplayed(checked);
				this.doLayout();
				break;

			case this.ccFieldCheck.id :
				this.showCC(checked);				
				break;

			case this.bccFieldCheck.id :
				this.showBCC(checked);
				break;
		}
	}
});

GO.email.TemplatesList = function(config) {

	Ext.apply(config);
	var tpl = new Ext.XTemplate(
		'<div id="template-0" class="go-item-wrap">'+GO.addressbook.lang.noTemplate+'</div>',
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
