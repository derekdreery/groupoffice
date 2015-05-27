GO.backupmanager.MainPanel = function(config){

	if(!config)
	{
		config = {};
	}

	this.formPanel = new Ext.form.FormPanel({
		url:GO.settings.modules.backupmanager.url+'json.php',
		baseParams:{
			task:'get_settings'
		},
		waitMsgTarget:true,
		border:false,
		bodyStyle:'padding:10px',
		anchor:'50% 50%',
		defaults:{
			anchor:'50%'
		},
		defaultType:'textfield',
		autoHeight:true,
		labelWidth:150,
		items:[{
			border:false,
			xtype:'panel',
			bodyStyle:'font-size: 12px;padding-bottom:10px',
			html:GO.backupmanager.lang.helpText
		},
		{
			fieldLabel:GO.backupmanager.lang.rmachine,
			name:'rmachine',
			allowBlank:false
		},{
			fieldLabel:GO.backupmanager.lang.rport,
			name:'rport',
			allowBlank:false,
			maskRe:/([0-9\s]+)$/
		},{
			fieldLabel:GO.backupmanager.lang.rtarget,
			name:'rtarget',
			allowBlank:false
		},{
			fieldLabel:GO.backupmanager.lang.sources,
			name:'sources',
			allowBlank:false
		},{
			fieldLabel:GO.lang['strUsername'],
			name:'ruser',
			allowBlank:false
		},{
			fieldLabel:GO.backupmanager.lang.emailaddresses,
			name:'emailaddress',
			allowBlank:false
		},{
			fieldLabel:GO.backupmanager.lang.emailsubject,
			name:'emailsubject',
			allowBlank:false
		},{
			fieldLabel:GO.backupmanager.lang.rotations,
			name:'rotations',
			allowBlank:false,
			maskRe:/([0-9\s]+)$/
		}
		],
		buttons:[
		{
			text:GO.lang['cmdSave'],
			handler: function()
			{
				this.submitForm();
			},
			scope: this
		},this.publishButton = new Ext.Button({
			text:GO.lang.waitMsgLoad,
			disabled:true,
			handler: function()
			{
				this.submitForm();
				if(this.publishButton.text == GO.backupmanager.lang.enablebackup)
				{
					if(!this.publishDialog)
					{
						this.publishDialog = new GO.backupmanager.PublishDialog();
						this.publishDialog.on('publish', function(){
							this.publishButton.setText(GO.backupmanager.lang.disablebackup);
						},this);
					}

					var values = this.formPanel.getForm().getValues();

					this.publishDialog.show(values);
				}
				else
				{
					Ext.Ajax.request({
						url: GO.settings.modules.backupmanager.url+'action.php',
						params: {
							task: 'disable_running',
							running: false
						},
						callback: function(options, success, response)
						{
							str = response.responseText;
							this.publishButton.setText(GO.backupmanager.lang.enablebackup);
							Ext.MessageBox.alert(GO.backupmanager.lang.disablebackup,GO.backupmanager.lang.successdisabledbackup);
						},
						scope:this
					});
				}
			},
			scope: this
		})],
		buttonAlign:'left'
	});

	config.layout='fit';
	config.anchor='50% 50%';
	config.items=[this.formPanel];
        
	GO.backupmanager.MainPanel.superclass.constructor.call(this, config);

}

Ext.extend(GO.backupmanager.MainPanel, Ext.Panel,{

	afterRender : function()
	{
		this.show();

		GO.backupmanager.MainPanel.superclass.afterRender.call(this);
	},
	onShow : function()
	{
		this.loadForm();

		GO.backupmanager.MainPanel.superclass.onShow.call(this);
	},
	loadForm : function()
	{
		this.formPanel.form.load({
			url : GO.settings.modules.backupmanager.url+'json.php',
			success:function(form, action)
			{
				if(action.result.enable_publish)
				{
					this.publishButton.enable();
				}
								
				if(action.result.data.running == 1)
				{
					this.publishButton.setText(GO.backupmanager.lang.disablebackup);
				}
				else
				{
					this.publishButton.setText(GO.backupmanager.lang.enablebackup);
				}
			},
			failure:function(form, action)
			{
			//GO.errorDialog.show(action.result.feedback)
			},
			scope: this
		});
	},
	submitForm : function()
	{
		this.formPanel.form.submit({
			url:GO.settings.modules.backupmanager.url+'action.php',
			params: {
				task:'save_settings'
			},
			waitMsg:GO.lang['waitMsgSave'],
			success:function(form, action)
			{
				this.publishButton.enable();
			},
			failure: function(form, action)
			{
				var error = (action.failureType=='client') ? GO.lang['strErrorsInForm'] : action.result.feedback;

				this.publishButton.disable();

				Ext.MessageBox.alert(GO.lang['strError'], error);
			},
			scope:this
		})
	}

});	

GO.moduleManager.addModule('backupmanager', GO.backupmanager.MainPanel,
{
	title:GO.backupmanager.lang.backupmanager,
	admin:true,
	iconCls : 'go-tab-icon-backupmanager'
});