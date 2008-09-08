/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: PersonalSettingsDialog.js 2732 2008-08-18 14:12:29Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */
 
 
GO.PersonalSettingsDialog = function(config){
	
	
	if(!config)
	{
		config={};
	}
	
	this.buildForm();
	
	var focusName = function(){
		this.nameField.focus();		
	};
	
		
	
	
	//config.iconCls='go-module-icon-users';
	config.layout='fit';
	config.modal=false;
	config.resizable=false;
	config.width=600;
	config.height=400;
	config.closeAction='hide';
	config.title= GO.lang.settings;					
	config.items= this.formPanel;
	//config.focus= focusName.createDelegate(this);
	config.buttons=[{
			text: GO.lang['cmdOk'],
			handler: function(){
				this.submitForm();
				this.hide();
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
	];

	
	GO.PersonalSettingsDialog.superclass.constructor.call(this, config);
	
	this.render(Ext.getBody());
	
	this.addEvents({'save' : true});	
};

Ext.extend(GO.PersonalSettingsDialog, Ext.Window,{

	
	show : function () {
		
		//this.maximize();
		
		
		
		//this.formPanel.show();

		this.formPanel.form.baseParams['user_id']=GO.settings.user_id;
		this.user_id=GO.settings.user_id;
		//this.loaded=true;
		
		if(!this.loaded)
		{
			this.formPanel.load({
				url : BaseHref+'json.php',
				waitMsg:GO.lang['waitMsgLoad'],
				success:function(form, action)
				{				
					this.loaded=true;
					GO.PersonalSettingsDialog.superclass.show.call(this);
					
					
					
					for(var i=0;i<this.tabPanel.items.getCount();i++)
					{
						var panel = this.tabPanel.items.itemAt(i);
						if(panel.onLoadSettings)
						{
							var func = panel.onLoadSettings.createDelegate(panel, [action]);
							func.call();
							 
						}
					}
					
			},
				failure:function(form, action)
				{
					Ext.Msg.alert(GO.lang['strError'], action.result.feedback)
				},
				scope: this
				
			});
		}else
		{
			GO.PersonalSettingsDialog.superclass.show.call(this);
		}

	},
	
	setWritePermission : function(writePermission)
	{
		this.buttons[0].setDisabled(!writePermission);
		this.buttons[1].setDisabled(!writePermission);
		this.linksPanel.setWritePermission(writePermission);
	},
	

	submitForm : function(){
		this.formPanel.form.submit(
		{
			url : BaseHref+'action.php',
			params: {'task' : 'save_settings'},
			waitMsg:GO.lang['waitMsgSave'],
			success:function(form, action){
				
				this.fireEvent('save', this);					
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
	
	
	buildForm : function () {
 
    this.tabPanel = new Ext.TabPanel({
      activeTab: 0,      
      deferredRender: false,
			anchor:'100% 100%',
      //layoutOnTabChange:true,
    	border: false,
      items: GO.moduleManager.getAllSettingsPanels()
    }) ;    
    
    this.formPanel = new Ext.form.FormPanel({
			items:this.tabPanel,
			baseParams:{task:'settings'},
	    waitMsgTarget:true,
	    border:false
		});
    
    
	}
});


