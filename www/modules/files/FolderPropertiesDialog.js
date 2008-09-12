GO.files.FolderPropertiesDialog = function(config){
	
	if(!config)
		config={};
		
	this.local_path=config.local_path;
	
	this.propertiesPanel = new Ext.Panel({
		layout:'form',
		title:GO.lang['strProperties'],
    cls:'go-form-panel',waitMsgTarget:true,
    defaultType: 'textfield',
		labelWidth:70, 
		border:false,   
    items: [
    		{
            fieldLabel: GO.lang['strName'],
            name: 'name',
            anchor: '100%'
        },new GO.form.PlainField({
        	type: 'plainfield',
        	fieldLabel: 'Path',
        	id: 'path'
        }),
        new GO.form.HtmlComponent({
        	html:'<hr />'        	
        }),
        new GO.form.PlainField({
        	fieldLabel: GO.lang.strCtime,
        	id: 'ctime'
        }),
        new GO.form.PlainField({
        	fieldLabel: GO.lang.strMtime,
        	id: 'mtime'
        }),
        new GO.form.PlainField({
        	fieldLabel: GO.lang.Atime,
        	id: 'atime'
        }),
        new GO.form.HtmlComponent({
        	html:'<hr />'        	
        }),
        new GO.form.PlainField({
        	fieldLabel: GO.lang.strType,
        	id: 'type'
        }),
        new GO.form.PlainField({
        	fieldLabel: GO.lang.strSize,
        	id: 'size'
        }),
        new Ext.form.Checkbox({		                	
            boxLabel: GO.files.lang.activateSharing,
            name: 'share',
            checked: false,
	          hideLabel:true		   
        }),
        new Ext.form.Checkbox({		                	
            boxLabel: GO.files.lang.notifyChanges,
            name: 'notify',
            checked: false,
	          hideLabel:true		   
        })
    ]
	});
	
	this.readPermissionsTab = new GO.grid.PermissionsPanel({
		title: GO.lang['strReadPermissions']					
	});
	
	this.writePermissionsTab = new GO.grid.PermissionsPanel({
		title: GO.lang['strWritePermissions']
	});
	
	this.commentsPanel = new Ext.Panel({
		layout:'form',
		labelWidth: 70,
		title: GO.files.lang.comments,
		border:false,
		items: new Ext.form.TextArea({
			name: 'comments',
			fieldLabel: '',
			hideLabel: true,
			anchor:'100% 100%'
		})
		
	});
	
	this.tabPanel =new Ext.TabPanel({
			activeTab: 0,
			deferredRender:false,
		  border:false,
		  anchor:'100% 100%',
		  hideLabel:true,
			items:[this.propertiesPanel, this.commentsPanel, this.readPermissionsTab, this.writePermissionsTab]		
		});
		
	this.formPanel = new Ext.form.FormPanel(
	{
		waitMsgTarget:true,
		border:false,
		defaultType: 'textfield',
		items:this.tabPanel
	});
	GO.files.FolderPropertiesDialog.superclass.constructor.call(this,{
		title:GO.lang['strProperties'],
		layout:'fit',
		width:400,
		height:400,
		closeAction:'hide',
		items:this.formPanel,
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
	
	this.addEvents({'rename' : true});
}

Ext.extend(GO.files.FolderPropertiesDialog, Ext.Window, {
	show : function(path)
	{
		this.path = path;
		
		if(!this.rendered)
			this.render(Ext.getBody());
		
		this.formPanel.form.load({
			url: GO.settings.modules.files.url+'json.php', 
			params: {
				path: path, 
				task: 'folder_properties',
				local_path: this.local_path
			},
			
			success: function(form, action) {


				this.formPanel.form.findField('share').setValue(action.result.data.acl_read>0);
				this.readPermissionsTab.setAcl(action.result.data.acl_read);
				this.writePermissionsTab.setAcl(action.result.data.acl_write);
				
				
				this.setWritePermission(action.result.data.write_permission);

				this.tabPanel.setActiveTab(0);
				
		    GO.files.FolderPropertiesDialog.superclass.show.call(this);
	    },
	    failure: function(form, action) {
				Ext.MessageBox.alert(GO.lang['strError'], action.result.feedback);
			},
	    scope: this
		});
		
		
	},
	
	setWritePermission : function(writePermission)
	{
		var form = this.formPanel.form;
		form.findField('name').setDisabled(!writePermission);
		form.findField('share').setDisabled(!writePermission);
		this.readPermissionsTab.setDisabled(!writePermission || this.readPermissionsTab.acl_id==0);
		this.writePermissionsTab.setDisabled(!writePermission || this.writePermissionsTab.acl_id==0);		
	},
	
	save : function(hide)
	{
		this.formPanel.form.submit({
						
			url:GO.settings.modules.files.url+'action.php',
			params: {
				path: this.path, 
				task: 'folder_properties',
				local_path: this.local_path
			},
			waitMsg:GO.lang['waitMsgSave'],
			success:function(form, action){

				if(action.result.acl_read)
				{
					this.readPermissionsTab.setAcl(action.result.acl_read);
					this.writePermissionsTab.setAcl(action.result.acl_write);			
				}
				
				if(action.result.path)
				{
					this.path=action.result.path;
					this.fireEvent('rename', this);
				}
				
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
