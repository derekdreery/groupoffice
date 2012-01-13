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

//GO.dialog.TabbedFormDialog = function(config) {
//	
//	config = config | {};
//	
//	if (config.title)
//		this.baseTitle = config.title;
//	
//	GO.dialog.TabbedFormDialog.superclass.constructor(this,config);
//}
GO.dialog.TabbedFormDialog = Ext.extend(GO.Window, {
	
	/**
	 * Set to false if you don't want to load the form on show when creating a new
	 * model.
	 */
	loadOnNewModel : true,
	
	remoteModelId : 0,
	
	titleField : false,
	
	submitAction : 'submit',
	
	loadAction : 'load',

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
	
	/**
	 * Config variable that is passed on the show function of this dialog.
	 */
	showConfig : false,
	
	/**
	 * Set to true when files needed to be uploaded
	 */
	fileUpload : false,
	

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
			fileUpload: this.fileUpload,
			baseParams : {},
			layout:'fit'
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
//			this._panels[0].items.each(function(item){
//				this.formPanel.add(item);
//			}, this);
//			
//			if(this._panels[0].cls)
//				this.formPanel.cls=this._panels[0].cls;
//			
//			if(this._panels[0].bodyStyle)
//				this.formPanel.bodyStyle=this._panels[0].bodyStyle;
//			
//			delete this._panels[0];

			delete this._panels[0].title;
			this._panels[0].header=false;
			this._panels[0].elements=this._panels[0].elements.replace(',header','');

			this.formPanel.add(this._panels[0]);
		}
		
		this.items=this.formPanel;				
		
		GO.dialog.TabbedFormDialog.superclass.initComponent.call(this); 
		
		this.addEvents({
			'submit' : true
		});
	},
	focus : function(){		
		var firstField = this.formPanel.form.items.first();
		if(firstField)
			firstField.focus();		
	},
	
	refreshActiveDisplayPanels : function(){
		var activeTab = GO.mainLayout.tabPanel.getActiveTab();			
		var dp = activeTab.findBy(function(comp){
			if(comp.isDisplayPanel)
				return true;
		});
						
		if(dp.length)
			dp[0].reload();		
		
		Ext.WindowMgr.each(function(win){
			if(win.isVisible()){
				var dp = win.findBy(function(comp){
					if(comp.isDisplayPanel)
						return true;
				});
				
				if(dp.length)
					dp[0].reload();	
			}
		});
		
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
		return {};
	},
	
	beforeSubmit : function(params){
		
	},
	
	submitForm : function(hide){
		
		var params=this.getSubmitParams();
		if(this.beforeSubmit(params)===false)
			return false;
		
		this.formPanel.form.submit(
		{
			url:GO.url(this.formControllerUrl+'/'+this.submitAction),
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
				
				this.refreshActiveDisplayPanels();
				
				if(this.link_config && this.link_config.callback)
				{	
					if(!this.link_config.scope)
						this.link_config.scope = this;
					
					this.link_config.callback.call(this.link_config.scope);						
				}
				this.updateTitle();
			},		
			failure: function(form, action) {
				if(action.failureType == 'client')
				{					
					Ext.MessageBox.alert(GO.lang['strError'], GO.lang['strErrorsInForm']);			
				} else {
					Ext.MessageBox.alert(GO.lang['strError'], action.result.feedback);
					
					if(action.result.validationErrors){
						for(var field in action.result.validationErrors){
							form.findField(field).markInvalid(action.result.validationErrors[field]);
						}
					}
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
		
		this.showConfig = config;
		
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
		
		if(this.loadOnNewModel)
		{
			
			this.formPanel.load({
				params:config.loadParams,
				url:GO.url(this.formControllerUrl+'/'+this.loadAction),
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
					
					if(config && config.values)
						this.formPanel.form.setValues(config.values);

					GO.dialog.TabbedFormDialog.superclass.show.call(this);
					this.afterLoad(remoteModelId, config, action);
					
					this.formPanel.form.clearInvalid();
					
					this.updateTitle();
				},
				failure:function(form, action)
				{
					Ext.Msg.alert(GO.lang['strError'], action.result.feedback)
				},
				scope: this				
			});
		} else {
			GO.dialog.TabbedFormDialog.superclass.show.call(this);
		}
		
		//if the newMenuButton from another passed a linkTypeId then set this value in the select link field
		if(config && config.link_config)
		{	
			this.link_config=config.link_config;
			if(this.selectLinkField){
				this.selectLinkField.container.up('div.x-form-item').setDisplayed(remoteModelId==0);
				if(config.link_config.modelNameAndId)
				{
					this.selectLinkField.setValue(config.link_config.modelNameAndId);
					this.selectLinkField.setRemoteText(config.link_config.text);
				}
			}
		}
	},

	updateTitle : function() {
		
		if(this.titleField)
		{
			var f=this.formPanel.form.findField(this.titleField);
			
			if(!this.origTitle)
				this.origTitle=this.title;
					
			var titleSuffix = this.remoteModelId > 0 ? f.getValue() : GO.lang.cmdNew;

			this.setTitle(this.origTitle+": "+titleSuffix);
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
	 * Use this function add an GO.grid.PermissionsPanel to the form.
	 * 
	 * @var GO.grid.PermissionsPanel panel
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