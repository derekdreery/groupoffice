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

	this.goDialogId='file';
	
	this.propertiesPanel = new Ext.Panel({
		layout:'form',
		title:GO.lang['strProperties'],
		cls:'go-form-panel',
		waitMsgTarget:true,
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


	if(GO.customfields && GO.customfields.types["6"])
	{
		for(var i=0;i<GO.customfields.types["6"].panels.length;i++)
		{
			items.push(GO.customfields.types["6"].panels[i]);
		}
	}

	
	this.tabPanel =new Ext.TabPanel({
		activeTab: 0,
		deferredRender:false,
		doLayoutOnTabChange:true,
		enableTabScroll:true,
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


	var tbar = [this.linkBrowseButton = new Ext.Button({
		iconCls : 'btn-link',
		cls : 'x-btn-text-icon',
		text : GO.lang.cmdBrowseLinks,
		disabled : true,
		handler : function() {
			if(!GO.linkBrowser){
				GO.linkBrowser = new GO.LinkBrowser();
			}
				
			GO.linkBrowser.show({
				link_id : this.file_id,
				link_type : "6",
				folder_id : "0"
			});
		},
		scope : this
	}),{
		iconCls: 'btn-save',
		text: GO.lang.download,
		cls: 'x-btn-text-icon',
		handler: function(){
			window.location.href=GO.settings.modules.files.url+'download.php?mode=download&id='+this.file_id;
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
					window.location.href=GO.settings.modules.gota.url+'jnlp.php?id='+this.file_id;
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
		}]
	});

	this.formPanel.form.on('actioncomplete',function(form, action){
		if(action.type=='load'){
			this.updateCfTabs(action.result.data.folder_id,action.result.data.cf_permissions);
		}
	},this);

	this.addEvents({
		'rename' : true,
		'save':true
	});
}

Ext.extend(GO.files.FilePropertiesDialog, GO.Window, {
	folder_id : 0,
	show : function(file_id, config)
	{
		config = config || {};
		
		this.setFileID(file_id);
		
		if(!this.rendered)
			this.render(Ext.getBody());
			
		this.formPanel.form.reset();
		this.tabPanel.setActiveTab(0);
		
		var params = {
			file_id: file_id,
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

				this.fireEvent('fileCommentsEdit',this);
				
				if(action.result.data.file_id)
				{
					this.setFileID(action.result.data.file_id);
				}
				
				this.folder_id=action.result.data.folder_id;
				
				GO.files.FilePropertiesDialog.superclass.show.call(this);
			},
			failure: function(form, action) {
				Ext.MessageBox.alert(GO.lang['strError'], action.result.feedback);
			},
			scope: this
		});
	},
	
	setFileID : function(file_id)
	{
		this.file_id = file_id;
		this.versionsGrid.setFileID(file_id);
		this.linkBrowseButton.setDisabled(file_id < 1);
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
				file_id: this.file_id, 
				task: 'file_properties'
			},
			waitMsg:GO.lang['waitMsgSave'],
			success:function(form, action){
				if(action.result.path)
				{
					this.formPanel.form.findField('path').setValue(action.result.path);
					this.fireEvent('rename', this, this.folder_id);					
				}
				
				this.fireEvent('save', this, this.file_id, this.folder_id);

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
	
	updateCfTabs : function(folder_id,permissions) {
		for (var i=0; i<this.tabPanel.items.items.length; i++) {
			var cat_id = this.tabPanel.items.items[i].category_id;
			if(cat_id>0){
				if (permissions.allowed_from_cf_module
					&& typeof(permissions.allowed_for_folder)!='undefined'
					&& permissions.allowed_for_folder.limit) {
					var visible = permissions.allowed_for_folder.categories.indexOf(this.tabPanel.items.items[i].category_id.toString())>=0;
					if (visible)
						this.tabPanel.unhideTabStripItem(this.tabPanel.items.items[i]);
					else
						this.tabPanel.hideTabStripItem(this.tabPanel.items.items[i]);
				} else if (typeof(permissions.allowed_for_folder)=='undefined') {
					this.tabPanel.hideTabStripItem(this.tabPanel.items.items[i]);
				} else {
					this.tabPanel.unhideTabStripItem(this.tabPanel.items.items[i]);
				}
			}
		}
	}
});
