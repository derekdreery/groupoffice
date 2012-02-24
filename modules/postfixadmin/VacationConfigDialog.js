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
 
GO.postfixadmin.VacationConfigDialog = function(config){
	
	
	if(!config)
	{
		config={};
	}
	
	
	this.buildForm();
	
	var focusFirstField = function(){
		this.propertiesPanel.items.items[0].focus();
	};
	
	
	config.maximizable=true;
	config.layout='fit';
	config.modal=false;
	config.resizable=false;
	config.width=700;
	config.height=500;
	config.closeAction='hide';
	config.title= GO.postfixadmin.lang.vacationConfig;					
	config.items= this.formPanel;
	config.focus= focusFirstField.createDelegate(this);
	config.buttons=[{
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
	];
	
	GO.postfixadmin.VacationConfigDialog.superclass.constructor.call(this, config);
	this.addEvents({'save' : true});	
}
Ext.extend(GO.postfixadmin.VacationConfigDialog, Ext.Window,{
	
	show : function (vacation_config_id, config) {
		if(!this.rendered)
		{
			this.render(Ext.getBody());
		}
		
		this.tabPanel.setActiveTab(0);
		
		
		
		if(!vacation_config_id)
		{
			vacation_config_id=0;			
		}
			
		this.setVacationConfigId(vacation_config_id);
		
		if(this.vacation_config_id>0)
		{
			this.formPanel.load({
				url : GO.settings.modules.postfixadmin.url+'json.php',
				
				success:function(form, action)
				{
					
					
						
									
					
					GO.postfixadmin.VacationConfigDialog.superclass.show.call(this);
				},
				failure:function(form, action)
				{
					Ext.Msg.alert(GO.lang['strError'], action.result.feedback)
				},
				scope: this
				
			});
		}else 
		{
			
			this.formPanel.form.reset();
			
			
				
			
			
			GO.postfixadmin.VacationConfigDialog.superclass.show.call(this);
		}
		
		
	},
	
	
		
		
	
	
	setVacationConfigId : function(vacation_config_id)
	{
		this.formPanel.form.baseParams['vacation_config_id']=vacation_config_id;
		this.vacation_config_id=vacation_config_id;
		
	},
	
	submitForm : function(hide){
		this.formPanel.form.submit(
		{
			url:GO.settings.modules.postfixadmin.url+'action.php',
			params: {'task' : 'save_vacation_config'},
			waitMsg:GO.lang['waitMsgSave'],
			success:function(form, action){
				
				this.fireEvent('save', this);
				
				if(hide)
				{
					this.hide();	
				}else
				{
				
					if(action.result.vacation_config_id)
					{
						this.setVacationConfigId(action.result.vacation_config_id);
						
						
											
					}
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
	
	
	buildForm : function () {
		
		
		this.propertiesPanel = new Ext.Panel({
			title:GO.lang['strProperties'],			
			cls:'go-form-panel',waitMsgTarget:true,			
			layout:'form',
			autoScroll:true,
			items:[{
				xtype: 'textfield',
			  name: 'email',
				anchor: '-20',
			  allowBlank:false,
			  fieldLabel: GO.postfixadmin.lang.email
			},{
				xtype: 'textfield',
			  name: 'subject',
				anchor: '-20',
			  allowBlank:false,
			  fieldLabel: GO.postfixadmin.lang.subject
			},{
				xtype: 'textarea',
			  name: 'body',
				anchor: '-20',
			  allowBlank:true,
			  fieldLabel: GO.postfixadmin.lang.body
			}
,{
				xtype: 'textarea',
			  name: 'cache',
				anchor: '-20',
			  allowBlank:true,
			  fieldLabel: GO.postfixadmin.lang.cache
			}
,{
				xtype: 'textfield',
			  name: 'domain',
				anchor: '-20',
			  allowBlank:false,
			  fieldLabel: GO.postfixadmin.lang.domain
			},{
				xtype: 'textfield',
			  name: 'active',
				anchor: '-20',
			  allowBlank:false,
			  fieldLabel: GO.postfixadmin.lang.active
			}]
				
		});
		var items  = [this.propertiesPanel];
		
    
    
    
		
		
		
 
    this.tabPanel = new Ext.TabPanel({
      activeTab: 0,      
      deferredRender: false,
    	border: false,
      items: items,
      anchor: '100% 100%'
    }) ;    
    
    
    this.formPanel = new Ext.form.FormPanel({
    	waitMsgTarget:true,
			url: GO.settings.modules.postfixadmin.url+'action.php',
			border: false,
			baseParams: {task: 'vacation_config'},				
			items:this.tabPanel				
		});
    
    
	}
});