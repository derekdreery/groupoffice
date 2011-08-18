GO.email.DeleteOldMailDialog = function(config){
	
	if(!config)
	{
		config={};
	}

	this.buildForm();

	config.layout='fit';
	config.title=GO.email.lang.deleteOldMails;
//	config.stateId='email-message-dialog';
	config.maximizable=true;
	config.modal=false;
	config.width=500;
	config.height=170;
	config.resizable=true;
	config.minizable=true;
	config.closeAction='hide';	
	config.items=this.formPanel;
	config.buttons=[{
		text: GO.lang['cmdOk'],
		handler: function()
		{
			this.submitForm();
		},
		scope:this
	},{
		text: GO.lang['cmdClose'],
		handler: function()
		{
			this.hide();
		},
		scope:this
	}];
	
	GO.email.DeleteOldMailDialog.superclass.constructor.call(this, config);

}

Ext.extend(GO.email.DeleteOldMailDialog, Ext.Window,{

	onShow : function() {
		GO.email.DeleteOldMailDialog.superclass.onShow.call(this);
		if (typeof(this.node)=='object') {
			this.folderNameField.setValue(this.node.attributes.name);
		}
		this.untilDate.setValue(this.getDefaultDate());
	},

	buildForm : function() {
		this.formPanel = new Ext.form.FormPanel({
			waitMsgTarget : true,
			url : GO.settings.modules.email.url + 'action.php',
			border : false,
			baseParams : {
				task : 'alias',
				account_id : 0
			},
			cls : 'go-form-panel',
			autoHeight : true,
			items : [this.folderNameField = new GO.form.PlainField({
					anchor : '100%',
					allowBlank:false,
					fieldLabel : GO.email.lang.folder
				}),{
					xtype : 'plainfield',
					anchor : '100%',
					allowBlank:false,
					hideLabel : true,
					value : GO.email.lang.deleteOldMailsInstructions
				}, this.untilDate = new Ext.form.DateField({
					name : 'until_date',
					width : 100,
					format : GO.settings['date_format'],
					allowBlank : false,
					fieldLabel : GO.email.lang.everythingBefore
				})
//                                ,this.applyToChildren = new Ext.form.Checkbox({
//					boxLabel : GO.email.lang.alsoChildren,
//					hideLabel : true,
//					checked : false,
//					name : 'apply_to_children'
//				})
			]
		});
	},

	setNode : function(node) {
		this.node = node;
		this.account_id = node.attributes.account_id;
	},

	getDefaultDate : function() {
		var date = new Date();
		date.setFullYear(date.getFullYear()-2);
		return date;
	},

	getNode : function() {
		if (typeof(this.node)=='undefined')
			return {};
		else
			return this.node;
	},

	deleteOldMails : function(totalMails,nDeleted,uids) {
		var conn = new Ext.data.Connection();
		conn.request({
			url : GO.settings.modules.email.url + 'action.php',
			params : {
				'task' : 'delete_old_mails',
				type:'imap',
				'account_id' : this.account_id,
				'id' : this.node.attributes.id,
				'mailbox' : this.node.attributes.mailbox,
				'total' : totalMails,
				'n_deleted' : nDeleted,
				'uids' : Ext.encode(uids),
				'until_date' : this.untilDate.value
//				,
//				'apply_to_children' : this.applyToChildren.getValue()
			},
			callback:function(options, success, response){
				var responseParams = Ext.decode(response.responseText);
				var uids = Ext.decode(responseParams.uids);
				if(!responseParams.success)
				{
					Ext.MessageBox.alert(GO.lang.strError, responseParams.feedback);
				} else if(uids && uids.length>0) {
					Ext.MessageBox.updateProgress(responseParams.progress, (responseParams.progress*100)+'%', '');
					this.deleteOldMails(responseParams.total,responseParams.nDeleted,uids);
				} else {
					GO.email.messagesGrid.store.reload({
						callback:function(){
							Ext.MessageBox.alert(GO.lang.strSuccess, GO.email.lang.nDeletedMailsTxt+": "+responseParams.nDeleted+".");
							this.hide();
						},
						scope:this
					});
					Ext.Ajax.request({
						url : GO.settings.modules.email.url + 'action.php',
						params : {
							'task' : 'log_deletion',
							'account_id' : this.account_id,
							'mailbox' : this.node.attributes.mailbox,
							'n_deleted' : responseParams.nDeleted,
							'until_date' : this.untilDate.value
//							,
//							'apply_to_children' : this.applyToChildren.getValue()
						},
						scope: this
					});
				}
			},
			scope : this
		});
	},

	submitForm : function(hide) {
		Ext.Msg.show({
			title: GO.email.lang.deleteOldMails,
			icon: Ext.MessageBox.WARNING,
			msg: GO.email.lang.deleteOldMailsSure1+' '+this.untilDate.value+GO.email.lang.deleteOldMailsSure2,
			buttons: Ext.Msg.YESNO,
			animEl: 'elId',
			fn: function(btn) {
				if (btn=='yes') {
					Ext.MessageBox.progress(GO.email.lang.deletingEmails, '', '');
					Ext.MessageBox.updateProgress(0, '0%', '');
					this.deleteOldMails(null,0,new Array());
				}
			},
			scope : this
		});
	}
});