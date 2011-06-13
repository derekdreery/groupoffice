/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: NoteDialog.js 7429 2011-05-16 13:15:10Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */
 
GO.dialog.TabbedFormDialog = Ext.extend(GO.Window, {
	
	serverId : 0,
	
	serverIdName : 'id',
	
	formControllerUrl : 'undefined',
	
	customFieldType : 0,
	
	initComponent : function(){
		
		Ext.applyIf(this, {
			collapsible:true,
			layout:'fit',
			modal:false,
			resizable:true,
			maximizable:true,
			width:600,
			height:400,
			closeAction:'hide',
			buttons:[{
				text: GO.lang['cmdOk'],
				handler: function(){
					this.submitForm(true);
				},
				scope: this
			},{
				text: GO.lang['cmdApply'],
				handler: function(){
					this.submitForm();
				},
				scope:this
			},{
				text: GO.lang['cmdClose'],
				handler: function(){
					this.hide();
				},
				scope:this
			}
			]
		});
		
		this.tabPanel = new Ext.TabPanel({
			activeTab: 0,
			deferredRender: false,
			border: false,
			anchor: '100% 100%'
		});
    
		this.formPanel = new Ext.form.FormPanel({
			waitMsgTarget:true,
			url: GO.settings.modules.notes.url+'action.php',
			border: false,
			baseParams: {
				task: 'note'
			},
			items:this.tabPanel				
		});  
		
		this.buildForm();
		this.addCustomFields();
		
		this.items=this.formPanel;
		
		
		GO.dialog.TabbedFormDialog.superclass.initComponent.call(this); 
		
		this.addEvents({
			'save' : true
		});
	},
	focus : function(){
		var firstTab = this.tabPanel.items.items[0];
		if(firstTab){
			var firstField = firstTab.items.items[0];
			if(firstField)
				firstField.focus();
		}
	},
	
	buildForm : function(){},
	
	addCustomFields : function(){
		if(this.customFieldType && GO.customfields && GO.customfields.types[this.customFieldType])
		{
			for(var i=0;i<GO.customfields.types[this.customFieldType].panels.length;i++)
			{			  	
				this.tabPanel.add(GO.customfields.types[this.customFieldType].panels[i]);
			}
		}
	},
	
	submitForm : function(hide){
		this.formPanel.form.submit(
		{
			url:this.formControllerUrl+'/save',
			
			waitMsg:GO.lang['waitMsgSave'],
			success:function(form, action){		
				
				this.setServerId(action.result[this.serverIdName]);	
				
				this.afterSave(action);
				
				if(hide)
				{
					this.hide();	
				}
				
				this.fireEvent('save', this, this.serverId);
				
				if(this.link_config && this.link_config.callback)
				{					
					this.link_config.callback.call(this);					
				}	
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
	afterLoad : function(action, config){},
	afterLoadNew : function(config){},
	afterSave : function(action){},
	
	show : function (serverId, config) {

		config = config || {};
		
		//tmpfiles on the server ({name:'Name',tmp_file:/tmp/name.ext} will be attached)
		this.formPanel.baseParams.tmp_files = config.tmp_files ? Ext.encode(config.tmp_files) : '';
				
		if(!this.rendered)
			this.render(Ext.getBody());
		
		if(!serverId)
		{
			serverId=0;			
		}
		
		delete this.link_config;
		this.formPanel.form.reset();	
		
		this.tabPanel.items.items[0].show();
			
		this.setServerId(serverId);
		
		if(this.serverId>0)
		{
			this.formPanel.load({
				url:this.formControllerUrl+'/load',
				success:function(form, action)
				{
					this.afterLoad(action, config);
					GO.dialog.TabbedFormDialog.superclass.show.call(this);
				},
				failure:function(form, action)
				{
					Ext.Msg.alert(GO.lang['strError'], action.result.feedback)
				},
				scope: this				
			});
		}else 
		{			
			this.formPanel.form.setValues(config.values);
			
			this.afterLoadNew(config);
			
			GO.dialog.TabbedFormDialog.superclass.show.call(this);
		}
		
		//if the newMenuButton from another passed a linkTypeId then set this value in the select link field
		if(this.selectLinkField && config && config.link_config)
		{
			this.selectLinkField.container.up('div.x-form-item').setDisplayed(serverId==0);
			
			this.link_config=config.link_config;
			if(config.link_config.type_id)
			{
				this.selectLinkField.setValue(config.link_config.type_id);
				this.selectLinkField.setRemoteText(config.link_config.text);
			}
		}
	},

	setServerId : function(serverId)
	{
		this.formPanel.form.baseParams[this.serverIdName]=serverId;
		this.serverId=serverId;
	},
	
	
	buildForm : function () {}
});