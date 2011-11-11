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
 *
 * If you extend this class, you MUST use the addPanel method to add at least
 * one panel to this dialog. A tabPanel is automatically created if and only if
 * more than one panel is added to the dialog in this way.
 */
 
GO.dialog.TabbedFormDialog = Ext.extend(GO.Window, {
	
	remoteModelId : 0,

	/**
	 * The controller will be called with this post parameter.
	 *
	 * $_POST['id'];
	 *
	 * This should not be changed.
	 */
	remoteModelIdName : 'id',

	/**
	 * This variable must point to a Form controller on the remoteModel that will be
	 * called with /load and /submit.
	 *
	 * eg. GO.url('notes/note');
	 */
	formControllerUrl : 'undefined',

	/**
	 * Set this if your item supports custom fields.
	 */
	customFieldType : 0,
	
	
	/**
	 * If set this panel will automatically listen to an acl_id field in the model.
	 */
	permissionsPanel : false,
	

	_panels : false,
	
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
		
		this._panels=[];

		this.buildForm();

		

		this.addCustomFields();
		
		this.formPanel = new Ext.form.FormPanel({
			waitMsgTarget:true,			
			border: false,			
			baseParams : {}
		});

		if(this._panels.length > 1) {		    
			this._tabPanel = new Ext.TabPanel({
				activeTab: 0,
				enableTabScroll:true,
				deferredRender: false,
				border: false,
				anchor: '100% 100%',
				items: this._panels
			});
		    
			this.formPanel.add(this._tabPanel);
		} else if (this._panels.length==1) {
			
			this._panels[0].items.each(function(item){
				this.formPanel.add(item);
			}, this);
			
			if(this._panels[0].cls)
				this.formPanel.cls=this._panels[0].cls;
			
			if(this._panels[0].bodyStyle)
				this.formPanel.bodyStyle=this._panels[0].bodyStyle;
			
			this.formPanel.autoScroll=true;
			
			delete this._panels[0];
		}
		
		this.items=this.formPanel;				
		
		GO.dialog.TabbedFormDialog.superclass.initComponent.call(this); 
		
		this.addEvents({
			'submit' : true
		});
	},
	focus : function(){
		var firstTab = this._tabPanel ? this._tabPanel.items.items[0] : this.formPanel;
		if(firstTab){
			var firstField = firstTab.items.items[0];
			if(firstField)
				firstField.focus();
		}
	},

	addCustomFields : function(){
		if(this.customFieldType && GO.customfields && GO.customfields.types[this.customFieldType])
		{
			for(var i=0;i<GO.customfields.types[this.customFieldType].panels.length;i++)
			{			  	
				this.addPanel(GO.customfields.types[this.customFieldType].panels[i]);
			}
		}
	},
	
	getSubmitParams : function(){
		
	},
	
	beforeSubmit : function(params){
		
	},
	
	submitForm : function(hide){
		
		var params=this.getSubmitParams();
		this.beforeSubmit(params);
		
		this.formPanel.form.submit(
		{
			url:GO.url(this.formControllerUrl+'/submit'),
			params: params,
			waitMsg:GO.lang['waitMsgSave'],
			success:function(form, action){		
				
				if(action.result[this.remoteModelIdName])
					this.setRemoteModelId(action.result[this.remoteModelIdName]);
				
				if(this.permissionsPanel && action.result[this.permissionsPanel.fieldName])
					this.permissionsPanel.setAcl(action.result[this.permissionsPanel.fieldName]);
				
				this.afterSubmit(action);
				
				if(hide)
				{
					this.hide();	
				}
				
				this.fireEvent('submit', this, this.remoteModelId);
				this.fireEvent('save', this, this.remoteModelId);
				
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
  
	beforeLoad : function(remoteModelId, config){},
	afterLoad : function(remoteModelId, config, action){},
	afterSubmit : function(action){},
	
	show : function (remoteModelId, config) {

		config = config || {};
    
		this.beforeLoad(remoteModelId, config);

		//tmpfiles on the remoteModel ({name:'Name',tmp_file:/tmp/name.ext} will be attached)
		this.formPanel.baseParams.tmp_files = config.tmp_files ? Ext.encode(config.tmp_files) : '';
				
		if(!this.rendered)
			this.render(Ext.getBody());
		
		if(!remoteModelId)
		{
			remoteModelId=0;
		}
		
		delete this.link_config;
		this.formPanel.form.reset();	

		if(this._tabPanel)
			this._tabPanel.items.items[0].show();
			
		this.setRemoteModelId(remoteModelId);
		
//		if(this.remoteModelId>0)
//		{
			this.formPanel.load({
				params:config.loadParams,
				url:GO.url(this.formControllerUrl+'/load'),
				success:function(form, action)
				{					
					if(action.result.remoteComboTexts){
						var t = action.result.remoteComboTexts;
						for(var fieldName in t){

							var f = this.formPanel.form.findField(fieldName);
							if(f)
								f.setRemoteText(t[fieldName]);
						}
					}
					
					if(this.permissionsPanel && action.result.data[this.permissionsPanel.fieldName])
						this.permissionsPanel.setAcl(action.result.data[this.permissionsPanel.fieldName]);

					GO.dialog.TabbedFormDialog.superclass.show.call(this);
					this.afterLoad(remoteModelId, config, action);
					
					this.formPanel.form.clearInvalid();
				},
				failure:function(form, action)
				{
					Ext.Msg.alert(GO.lang['strError'], action.result.feedback)
				},
				scope: this				
			});
//		}else 
//		{			
//			this.formPanel.form.setValues(config.values);
//			
//			if(this.permissionsPanel)
//				this.permissionsPanel.setAcl(0);
//			
//			this.afterLoad(remoteModelId, config);
//			
//			GO.dialog.TabbedFormDialog.superclass.show.call(this);
//		}
		
		//if the newMenuButton from another passed a linkTypeId then set this value in the select link field
		if(this.selectLinkField && config && config.link_config)
		{
			this.selectLinkField.container.up('div.x-form-item').setDisplayed(remoteModelId==0);
			
			this.link_config=config.link_config;
			if(config.link_config.modelNameAndId)
			{
				this.selectLinkField.setValue(config.link_config.modelNameAndId);
				this.selectLinkField.setRemoteText(config.link_config.text);
			}
		}
	},

	setRemoteModelId : function(remoteModelId)
	{
		this.formPanel.form.baseParams[this.remoteModelIdName]=remoteModelId;
		this.remoteModelId=remoteModelId;
	},

	/**
	 * Use this function to add panels to the window.
	 */
	addPanel : function(panel){
		this._panels.push(panel);
	},
	
	/**
	 * Use this function add an GO.panels.PermissionPanel to the form.
	 */
	addPermissionsPanel : function(panel){
		this.permissionsPanel = panel;
		this.addPanel(panel);
	},


	/**
	 * Override this function to build your form. Call addPanel to add panels.
	 */
	buildForm : function () {

	}
});