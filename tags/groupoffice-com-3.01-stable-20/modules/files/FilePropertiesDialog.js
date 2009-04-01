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

GO.files.FilePropertiesDialog = function(config){	
	
	if(!config)
		config={};

	
	this.propertiesPanel = new Ext.Panel({
		layout:'form',
		title:GO.lang['strProperties'],
    cls:'go-form-panel',waitMsgTarget:true,
    labelWidth: 100,
    defaultType: 'textfield',
    items: [
    		{
          fieldLabel: GO.lang['strName'],
          name: 'name',
          anchor: '100%',
					validator:function(v){
						return !v.match(/[&\/:\*\?"<>|\\]/);
					} 
        },{
        	xtype: 'plainfield',
        	fieldLabel: GO.lang.strLocation,
        	name: 'path'
        },
        new GO.form.HtmlComponent({
        	html:'<hr />'        	
        }),
        {
        	xtype: 'plainfield',
        	fieldLabel: GO.lang.strCtime,
        	name: 'ctime'
        },
        {
        	xtype: 'plainfield',
        	fieldLabel: GO.lang.strMtime,
        	name: 'mtime'
        },
        {
        	xtype: 'plainfield',
        	fieldLabel: GO.lang.Atime,
        	name: 'atime'
        },
        new GO.form.HtmlComponent({
        	html:'<hr />'        	
        }),
        {
        	xtype: 'plainfield',
        	fieldLabel: GO.lang.strType,
        	name: 'type'
        },
        {
        	xtype: 'plainfield',
        	fieldLabel: GO.lang.strSize,
        	name: 'size'
        }
    ]
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
	
	var items = [this.propertiesPanel, this.commentsPanel];

	
	if(GO.workflow)
	{
		this.workflowPanel = new GO.workflow.FilePropertiesPanel();
		items.push(this.workflowPanel);
	}
	
	this.tabPanel =new Ext.TabPanel({
			activeTab: 0,
			deferredRender:false,
			doLayoutOnTabChange:true,
		  border:false,
		  anchor:'100% 100%',
		  hideLabel:true,
			items:items
		});
		
	this.formPanel = new Ext.form.FormPanel(
	{
		waitMsgTarget:true,
		border:false,
		defaultType: 'textfield',
		items:this.tabPanel
	});
		
	GO.files.FilePropertiesDialog.superclass.constructor.call(this,{
		title:GO.lang['strProperties'],
		layout:'fit',
		width:440,
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
			}]
	});
	
	this.addEvents({'rename' : true});
}

Ext.extend(GO.files.FilePropertiesDialog, Ext.Window, {
	
	show : function(path)
	{
		this.path = path;
		
		if(!this.rendered)
			this.render(Ext.getBody());
			
		this.formPanel.form.reset();
		
		this.formPanel.form.load({
			url: GO.settings.modules.files.url+'json.php', 
			params: {
				path: path, 
				task: 'file_properties'
			},			
			success: function(form, action) {				
				this.setWritePermission(action.result.data.write_permission);
				this.tabPanel.setActiveTab(0);
				
		    GO.files.FilePropertiesDialog.superclass.show.call(this);
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
	},
	
	save : function(hide)
	{
		this.formPanel.form.submit({
						
			url:GO.settings.modules.files.url+'action.php',
			params: {
				path: this.path, 
				task: 'file_properties'
			},
			waitMsg:GO.lang['waitMsgSave'],
			success:function(form, action){
				if(action.result.path)
				{
					var oldpath = this.path;
					this.path=action.result.path;
					this.fireEvent('rename', this, oldpath, this.path);
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
