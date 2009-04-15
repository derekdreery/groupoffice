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
	
	this.versionsGrid = new GO.files.VersionsGrid();
	
	var items = [this.propertiesPanel, this.commentsPanel, this.versionsGrid];

	
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
	
	var tbar = [{
					iconCls: 'btn-save',
					text: GO.lang.download,
					cls: 'x-btn-text-icon',
					handler: function(){
						window.location.href=GO.settings.modules.files.url+'download.php?mode=download&path='+encodeURIComponent(this.path);
					},
					scope: this
				}];
				
	if(GO.settings.modules.gota && GO.settings.modules.gota.read_permission)
	{
		tbar.push({
					iconCls: 'btn-edit',
					text: GO.files.lang.downloadGOTA,
					cls: 'x-btn-text-icon',
					handler: function(){
						if(!deployJava.isWebStartInstalled('1.6.0'))
						{
							Ext.MessageBox.alert(GO.lang.strError, GO.lang.noJava);
						}else
						{		
							window.location.href=GO.settings.modules.gota.url+'jnlp.php?path='+encodeURIComponent(this.path);
						}
					},
					scope: this
				});
	}
		
	GO.files.FilePropertiesDialog.superclass.constructor.call(this,{
		title:GO.lang['strProperties'],
		layout:'fit',
		width:650,
		height:550,
		closeAction:'hide',
		items:this.formPanel,
		maximizable:true,
		collapsible:true,
		tbar:tbar,
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
	
	show : function(path, config)
	{
		config = config || {};
		
		this.setPath(path);
		
		if(!this.rendered)
			this.render(Ext.getBody());
			
		this.formPanel.form.reset();
		this.tabPanel.setActiveTab(0);
		
		var params = {
				path: path, 
				task: 'file_properties'
			};
			
		if(config.loadParams)
		{
			Ext.apply(params, config.loadParams);
		}
		
		
		
		this.formPanel.form.load({
			url: GO.settings.modules.files.url+'json.php', 
			params: params,			
			success: function(form, action) {				
				this.setWritePermission(action.result.data.write_permission);		
				
				if(action.result.data.path)
				{
					this.setPath(action.result.data.path);
				}
				
		    GO.files.FilePropertiesDialog.superclass.show.call(this);
	    },
	    failure: function(form, action) {
				Ext.MessageBox.alert(GO.lang['strError'], action.result.feedback);
			},
	    scope: this
		});		
	},
	
	setPath : function(path)
	{
		this.path = path;
		this.versionsGrid.setPath(path);
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
					this.setPath(action.result.path);
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
