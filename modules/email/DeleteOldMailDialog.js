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
			this.folderNameField.setValue(this.node.attributes.mailbox);
		}
		this.untilDate.setValue(this.getDefaultDate());
	},

	buildForm : function() {
		this.formPanel = new Ext.form.FormPanel({
			timeout:120000,
			url : GO.url("email/message/deleteOld"),
			waitMsgTarget : true,
			border : false,
			cls : 'go-form-panel',
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

	submitForm : function(hide) {
		Ext.Msg.show({
			title: GO.email.lang.deleteOldMails,
			icon: Ext.MessageBox.WARNING,
			msg: GO.email.lang.deleteOldMailsSure1+' '+this.untilDate.value+GO.email.lang.deleteOldMailsSure2,
			buttons: Ext.Msg.YESNO,
			animEl: 'elId',
			fn: function(btn) {
				if (btn=='yes') {
					this.formPanel.form.submit({
						url : GO.url("email/message/deleteOld"),
						params : {
							'account_id' : this.account_id,
							'mailbox' : this.node.attributes.mailbox
						},
						waitMsg : GO.lang['waitMsgSave'],
						success : function(form, action) {

							GO.email.messagesGrid.store.load({
								callback:function(){
									Ext.MessageBox.alert(GO.lang.strSuccess, GO.email.lang.nDeletedMailsTxt+": "+action.result.total);
									this.hide();
								},
								scope:this
							});

						},

						failure : function(form, action) {
							var error = '';
							if (action.failureType == 'client') {
								error = GO.lang.strErrorsInForm;
							} else if (action.result) {
								error = action.result.feedback;
							} else {
								error = GO.lang.strRequestError;
							}

							Ext.MessageBox.alert(GO.lang.strError, error);
						},
						scope : this

					});

				}
			},
			scope : this
		});
	}
});