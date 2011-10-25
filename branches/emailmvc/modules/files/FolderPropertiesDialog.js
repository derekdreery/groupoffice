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
 
GO.files.FolderPropertiesDialog = function(config){
	
	if(!config)
		config={};

	this.goDialogId='folder';

	this.propertiesPanel = new Ext.Panel({
		layout:'form',
		title:GO.lang['strProperties'],
		cls:'go-form-panel',
		waitMsgTarget:true,
		defaultType: 'textfield',
		labelWidth:100, 
		border:false,   
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
			xtype: 'htmlcomponent',
			html:'<hr />'
		},
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
		}),
		this.applyStateCheckbox = new Ext.form.Checkbox({
			boxLabel: GO.files.lang.applyState,
			name: 'apply_state',
			checked: false,
			hideLabel:true
		})
		]
	});
	
	this.readPermissionsTab = new GO.grid.PermissionsPanel({
							
		});
	
	this.commentsPanel = new Ext.Panel({
		layout:'form',
		labelWidth: 70,
		title: GO.files.lang.comments,
		border:false,
		items: new Ext.form.TextArea({
			name: 'comment',
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
		items:[this.propertiesPanel, this.commentsPanel, this.readPermissionsTab]
	});
	
	if(GO.customfields){
		this.disableCategoriesPanel = new GO.customfields.DisableCategoriesPanel();
		this.tabPanel.add(this.disableCategoriesPanel);
	}

	if(GO.workflow)
	{
		this.workflowPanel = new GO.workflow.FolderPropertiesPanel();
		this.tabPanel.insert(2,this.workflowPanel);
	}
		
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
		width:500,
		height:400,
		closeAction:'hide',
		items:this.formPanel,
		buttons:[
		{
			text:GO.lang['cmdOk'],
			handler: function(){
				this.save(true)
				},
			scope: this
		},
		{
			text:GO.lang['cmdApply'],
			handler: function(){
				this.save(false)
				},
			scope: this
		},
			
		{
			text:GO.lang['cmdClose'],
			handler: function(){
				this.hide()
				},
			scope: this
		}
		]		
	});


	this.addEvents({
		'rename' : true
	});
}

Ext.extend(GO.files.FolderPropertiesDialog, GO.Window, {
	parent_id : 0,
	show : function(folder_id)
	{
		this.folder_id = folder_id;
		
		if(!this.rendered)
			this.render(Ext.getBody());
		
		this.formPanel.form.load({
			url: GO.url('files/folder/load'),
			params: {
				id: folder_id
			},			
			success: function(form, action) {

				var shareField = this.formPanel.form.findField('share');
				shareField.setValue(action.result.data.acl_id>0);
				
				this.parent_id=action.result.data.parent_id;
								
				this.readPermissionsTab.setAcl(action.result.data.acl_id);
				
				this.setPermission(action.result.data.is_someones_home_dir, action.result.data.permission_level);

				this.tabPanel.setActiveTab(0);
				if(GO.customfields)
					this.disableCategoriesPanel.setModel(folder_id,"GO_Files_model_File");
				
				GO.files.FolderPropertiesDialog.superclass.show.call(this);
			},
			failure: function(form, action) {
				Ext.MessageBox.alert(GO.lang['strError'], action.result.feedback);
			},
			scope: this
		});
		
		
	},
	
	setPermission : function(is_someones_home_dir, permission_level)
	{
		var form = this.formPanel.form;
		form.findField('name').setDisabled(is_someones_home_dir || permission_level<GO.permissionLevels.write);
		form.findField('share').setDisabled(is_someones_home_dir || permission_level<GO.permissionLevels.manage);
		form.findField('apply_state').setDisabled(permission_level<GO.permissionLevels.manage && !GO.settings.has_admin_permission);
		this.readPermissionsTab.setDisabled(this.readPermissionsTab.acl_id==0);
	},
	
	save : function(hide)
	{
		this.formPanel.form.submit({
						
			url: GO.url('files/folder/submit'),
			params: {
				id: this.folder_id
			},
			waitMsg:GO.lang['waitMsgSave'],
			success:function(form, action){

				if(typeof(action.result.acl_id) != 'undefined')
				{
					this.readPermissionsTab.setAcl(action.result.acl_id);
				}
				
				if(action.result.new_path)
				{
					this.formPanel.form.findField('path').setValue(action.result.new_path);
					this.fireEvent('rename', this, this.parent_id);				
				}
				this.fireEvent('save', this, this.folder_id);
				
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
