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
			xtype: 'plainfield',
			fieldLabel: GO.lang.Atime,
			name: 'atime'
		},
		{
			xtype: 'htmlcomponent',
			html:'<hr />'
		},
		{
			xtype: 'plainfield',
			fieldLabel: GO.lang.strType,
			name: 'type'
		},
		{
			xtype: 'plainfield',
			fieldLabel: GO.lang.strSize,
			name: 'size'
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
		items:[this.propertiesPanel, this.commentsPanel, this.readPermissionsTab]
	});

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

	if (GO.customfields) {
		Ext.Ajax.request({
			url:GO.settings.modules.files.url+'json.php',
			params:{
				task:'is_admin'
			},
			success: function(response, options)
			{
				var responseParams = Ext.decode(response.responseText);
				if(responseParams.success && responseParams.has_admin)
				{
					this.customfieldCategoryPanel = new Ext.FormPanel({
					waitMsgTarget:true,
					title: GO.files.lang.categoriesFiles,
					labelWidth: 150,
					width: 800,
					defaultType: 'checkbox',
					border: false,
					layout: 'form',
					defaults: {
						anchor:'100%'
					},
					cls:'go-form-panel',
					waitMsgTarget:true,
					items:[
							this.limitCB = new Ext.form.Checkbox({
								name: 'limit',
								hideLabel: true,
								boxLabel: GO.files.lang.applyLimits
							}),this.cfCategoriesFieldset = new Ext.form.FieldSet({
								title: GO.customfields.lang.categories,
								layout: 'form',
								style: 'margin:3px;padding:0;padding-left:3px;',
								autoHeight: true
							}),this.applyRecursivelyCB = new Ext.form.Checkbox({
								name: 'recursive',
								hideLabel: true,
								boxLabel: GO.files.lang.applyRecursively,
								checked : false
							})
						]
					});
					for (var i=0; i<GO.customfields.types['6'].panels.length; i++) {
						this.cfCategoriesFieldset.add(new Ext.form.Checkbox({
								name: 'cat_'+GO.customfields.types['6'].panels[i].category_id,
								hideLabel: true,
								boxLabel: GO.customfields.types['6'].panels[i].title,
								checked: false
							}));
					}

					this.limitCB.on('check',function(cbox,checked) {
						if (checked)
							this.cfCategoriesFieldset.enable();
						else
							this.cfCategoriesFieldset.disable();
					},this);

					this.applyRecursivelyCB.on('check',function(cbox,checked) {
						if (checked) {
							Ext.MessageBox.show({
							 title: GO.files.lang.pleaseConfirm,
							 msg: GO.files.lang.applyRecursivelyRUSure,
							 buttons: Ext.MessageBox.YESNO,
							 fn: function(x) {
								 this.applyRecursivelyCB.setValue(x=='yes');
							 },
							 animEl: 'mb4',
							 icon: Ext.MessageBox.QUESTION,
							 scope: this
						 });
						}
					},this);

					this.customfieldCategoryPanel.on('show',this.customfieldCategoriesPanelShownHandler,this);
					this.tabPanel.add(this.customfieldCategoryPanel);
					this.setSize(800,400);
				}else if (!responseParams.success)
				{
					this.reload();
					alert(responseParams.feedback);
				}
			},
			scope:this
		});
	}

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
			url: GO.settings.modules.files.url+'json.php', 
			params: {
				folder_id: folder_id, 
				task: 'folder_properties'
			},			
			success: function(form, action) {

				var shareField = this.formPanel.form.findField('share');
				shareField.setValue(action.result.data.acl_id>0);
				
				this.parent_id=action.result.data.parent_id;
								
				this.readPermissionsTab.setAcl(action.result.data.acl_id);
				
				this.setWritePermission(action.result.data.is_home_dir, action.result.data.write_permission, action.result.data.manage_permission);

				this.tabPanel.setActiveTab(0);
				
				GO.files.FolderPropertiesDialog.superclass.show.call(this);
			},
			failure: function(form, action) {
				Ext.MessageBox.alert(GO.lang['strError'], action.result.feedback);
			},
			scope: this
		});
		
		
	},
	
	setWritePermission : function(is_home_dir, writePermission, managePermission)
	{
		var form = this.formPanel.form;
		form.findField('name').setDisabled(is_home_dir || !writePermission);
		form.findField('share').setDisabled(is_home_dir || !managePermission);
		form.findField('apply_state').setDisabled(!( managePermission || GO.settings.has_admin_permission));
		this.readPermissionsTab.setDisabled(this.readPermissionsTab.acl_id==0);
	},
	
	save : function(hide)
	{
		this.formPanel.form.submit({
						
			url:GO.settings.modules.files.url+'action.php',
			params: {
				folder_id: this.folder_id, 
				task: 'folder_properties'
			},
			waitMsg:GO.lang['waitMsgSave'],
			success:function(form, action){

				if(action.result.acl_id)
				{
					this.readPermissionsTab.setAcl(action.result.acl_id);
				}
				
				if(action.result.path)
				{
					this.formPanel.form.findField('path').setValue(action.result.path);
					this.fireEvent('rename', this, this.parent_id);				
				}
				this.fireEvent('save', this, this.folder_id);

				if (GO.customfields) {
					this.submitAllowedCfCategories(hide);
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
			
	},

	submitAllowedCfCategories : function(hide) {
		this.customfieldCategoryPanel.form.submit({
			waitMsg:GO.lang.waitMsgSave,
			url:GO.settings.modules.files.url+ 'action.php',
			params:
			{
				task : 'save_folder_limits',
				folder_id : this.folder_id
			},
			success:function(form, action){
				if (hide)
					this.hide();
			},
			failure: function(form, action) {
				if(action.failureType == 'client')
				{
					Ext.MessageBox.alert(GO.lang['strError'], GO.lang['strErrorsInForm']);
				} else {
					Ext.MessageBox.alert(GO.lang['strError'], action.result.feedback);
				}
			},
			scope: this
		});
	},
	uncheckCategoryCBs : function() {
		for(var i=0; i<this.cfCategoriesFieldset.items.items.length;i++)
			this.cfCategoriesFieldset.items.items[i].setValue(false);
	},
	loadCustomfieldCategoriesForm : function() {
		this.uncheckCategoryCBs();
		this.customfieldCategoryPanel.form.load({
			url : GO.settings.modules.files.url + 'json.php',
			params : {
				task : 'folder_cf_categories',
				folder_id : this.folder_id
			},
			waitMsg : GO.lang.waitMsgLoad,
			success : function(form, action) {
				var response = Ext.decode(action.response.responseText);
				if (response.data.limit)
					this.cfCategoriesFieldset.enable();
				else
					this.cfCategoriesFieldset.disable();

				this.applyRecursivelyCB.setValue(false);
			},
			failure: function(form, action) {
				if(action.failureType == 'client')
				{
					Ext.MessageBox.alert(GO.lang['strError'], GO.lang['strErrorsInForm']);
				} else {
					Ext.MessageBox.alert(GO.lang['strError'], action.result.feedback);
				}
			},
			scope : this
		});
	},
	customfieldCategoriesPanelShownHandler : function() {
		this.loadCustomfieldCategoriesForm();
	}
});
