GO.tasks.TasklistDialog = function(config)
{
	if(!config)
	{
		config = {};
	}
	
	this.propertiesTab = new Ext.Panel({
		title:GO.lang['strProperties'],
		layout:'form',
		anchor: '100% 100%',
		defaults: {anchor: '100%'},
		defaultType: 'textfield',
		autoHeight:true,
		cls:'go-form-panel',waitMsgTarget:true,
		labelWidth: 75,
   
		items: [
		this.selectUser = new GO.form.SelectUser({
			fieldLabel: GO.lang['strUser'],
			disabled: !GO.settings.modules['email']['write_permission'],
			value: GO.settings.user_id
		}),
		{
			fieldLabel: GO.lang['strName'],
			name: 'name',
			allowBlank:false

		}
		]
	});


	this.readPermissionsTab = new GO.grid.PermissionsPanel({
		title: GO.lang['strReadPermissions']
	});

	this.writePermissionsTab = new GO.grid.PermissionsPanel({
		title: GO.lang['strWritePermissions']
	});

	//this.readPermissionsTab.render(document.body);
	//this.writePermissionsTab.render(document.body);

	this.formPanel = new Ext.form.FormPanel({
		waitMsgTarget:true,
		url: GO.settings.modules.tasks.url+'action.php',
		//labelWidth: 75, // label settings here cascade unless overridden
		defaultType: 'textfield',
		border:false,
		items:[{
			hideLabel:true,
			deferredRender:false,
			xtype:'tabpanel',
			activeTab: 0,
			border:false,
			anchor: '100% 100%',
			items:[
			this.propertiesTab,
			this.readPermissionsTab,
			this.writePermissionsTab
			]
		}]
	});
	
	
	GO.tasks.TasklistDialog.superclass.constructor.call(this,{
					title: GO.tasks.lang.tasklist,
					layout:'fit',
					modal:false,
					height:500,
					width:400,
						
					items: this.formPanel,
					buttons:[
					{
						text:GO.lang['cmdOk'],
						handler: function(){this.save(true)},
						scope: this
					},
					{
						text:GO.lang['cmdApply'],
						handler: function(){this.save(false)},
						scope: this
					},

					{
						text:GO.lang['cmdClose'],
						handler: function(){this.hide()},
						scope: this
					}
					]
				});
}

Ext.extend(GO.tasks.TasklistDialog, Ext.Window, {
	
	initComponent : function(){
		
		this.addEvents({'save' : true});
		
		GO.tasks.TasklistDialog.superclass.initComponent.call(this);
		
		
	},
				
	show : function (tasklist_id){		
		if(!this.rendered)
			this.render(Ext.getBody());
			
		if(tasklist_id > 0)
		{
			if(tasklist_id!=this.tasklist_id)
			{
				this.loadTasklist(tasklist_id);
			}
		}else
		{
			this.tasklist_id=0;
			this.formPanel.form.reset();
			this.propertiesTab.show();

			this.readPermissionsTab.setDisabled(true);
			this.writePermissionsTab.setDisabled(true);

			//this.selectUser.setValue(GO.settings['user_id']);
			//this.selectUser.setRawValue(GO.settings['name']);

			GO.tasks.TasklistDialog.superclass.show.call(this);
		}
	},
	loadTasklist : function(tasklist_id)
	{
		this.formPanel.form.load({
			url: GO.settings.modules.tasks.url+'json.php',
			params: {
				tasklist_id:tasklist_id,
				task: 'tasklist'
			},
			
			success: function(form, action) {
				this.tasklist_id=tasklist_id;
				this.selectUser.setRemoteText(action.result.data.user_name);
				this.readPermissionsTab.setAcl(action.result.data.acl_read);
				this.writePermissionsTab.setAcl(action.result.data.acl_write);
				GO.tasks.TasklistDialog.superclass.show.call(this);
			},
			failure:function(form, action)
			{
				Ext.Msg.alert(GO.lang['strError'], action.result.feedback)
			},
			scope: this
		});
	},
	save : function(hide)
	{
		this.formPanel.form.submit({
				
			url:GO.settings.modules.tasks.url+'action.php',
			params: {
					'task' : 'save_tasklist', 
					'tasklist_id': this.tasklist_id
			},
			waitMsg:GO.lang['waitMsgSave'],
			success:function(form, action){
										
				if(action.result.tasklist_id)
				{
					this.tasklist_id=action.result.tasklist_id;
					this.readPermissionsTab.setAcl(action.result.acl_read);
					this.writePermissionsTab.setAcl(action.result.acl_write);
					//this.loadAccount(this.tasklist_id);
				}
				
				this.fireEvent('save');
				
				if(hide)
				{
					this.hide();
				}
					
					
			},

			failure: function(form, action) {
				var error = '';
				if(action.failureType=='client')
				{
					error = GO.lang['strErrorsInForm'];
				}else
				{
					error = action.result.feedback;
				}
					
				Ext.MessageBox.alert(GO.lang['strError'], error);
			},
			scope:this

		});
			
	}
});
