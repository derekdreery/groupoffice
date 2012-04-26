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
 
GO.postfixadmin.FetchmailConfigDialog = function(config){
	
	
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
	config.title= GO.postfixadmin.lang.fetchmailConfig;					
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
	
	GO.postfixadmin.FetchmailConfigDialog.superclass.constructor.call(this, config);
	this.addEvents({'save' : true});	
}
Ext.extend(GO.postfixadmin.FetchmailConfigDialog, Ext.Window,{
	
	show : function (fetchmail_config_id, config) {
		if(!this.rendered)
		{
			this.render(Ext.getBody());
		}
		
		this.tabPanel.setActiveTab(0);
		
		
		
		if(!fetchmail_config_id)
		{
			fetchmail_config_id=0;			
		}
			
		this.setFetchmailConfigId(fetchmail_config_id);
		
		if(this.fetchmail_config_id>0)
		{
			this.formPanel.load({
				url : GO.settings.modules.postfixadmin.url+'json.php',
				
				success:function(form, action)
				{
					
					
						
									
					
					GO.postfixadmin.FetchmailConfigDialog.superclass.show.call(this);
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
			
			
				
			
			
			GO.postfixadmin.FetchmailConfigDialog.superclass.show.call(this);
		}
		
		
	},
	
	
		
		
	
	
	setFetchmailConfigId : function(fetchmail_config_id)
	{
		this.formPanel.form.baseParams['fetchmail_config_id']=fetchmail_config_id;
		this.fetchmail_config_id=fetchmail_config_id;
		
	},
	
	submitForm : function(hide){
		this.formPanel.form.submit(
		{
			url:GO.settings.modules.postfixadmin.url+'action.php',
			params: {'task' : 'save_fetchmail_config'},
			waitMsg:GO.lang['waitMsgSave'],
			success:function(form, action){
				
				this.fireEvent('save', this);
				
				if(hide)
				{
					this.hide();	
				}else
				{
				
					if(action.result.fetchmail_config_id)
					{
						this.setFetchmailConfigId(action.result.fetchmail_config_id);
						
						
											
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
			  name: 'mailbox',
				anchor: '-20',
			  allowBlank:false,
			  fieldLabel: GO.postfixadmin.lang.mailbox
			},{
				xtype: 'textfield',
			  name: 'src_server',
				anchor: '-20',
			  allowBlank:false,
			  fieldLabel: GO.postfixadmin.lang.srcServer
			},{
				xtype: 'textfield',
			  name: 'src_auth',
				anchor: '-20',
			  allowBlank:false,
			  fieldLabel: GO.postfixadmin.lang.srcAuth
			},{
				xtype: 'textfield',
			  name: 'src_user',
				anchor: '-20',
			  allowBlank:false,
			  fieldLabel: GO.postfixadmin.lang.srcUser
			},{
				xtype: 'textfield',
			  name: 'src_password',
				anchor: '-20',
			  allowBlank:false,
			  fieldLabel: GO.postfixadmin.lang.srcPassword
			},{
				xtype: 'textfield',
			  name: 'src_folder',
				anchor: '-20',
			  allowBlank:false,
			  fieldLabel: GO.postfixadmin.lang.srcFolder
			},{
				xtype: 'textfield',
			  name: 'poll_time',
				anchor: '-20',
			  allowBlank:false,
			  fieldLabel: GO.postfixadmin.lang.pollTime
			},{
				xtype: 'textfield',
			  name: 'fetchall',
				anchor: '-20',
			  allowBlank:false,
			  fieldLabel: GO.postfixadmin.lang.fetchall
			},{
				xtype: 'textfield',
			  name: 'keep',
				anchor: '-20',
			  allowBlank:false,
			  fieldLabel: GO.postfixadmin.lang.keep
			},{
				xtype: 'textfield',
			  name: 'protocol',
				anchor: '-20',
			  allowBlank:false,
			  fieldLabel: GO.postfixadmin.lang.protocol
			},{
				xtype: 'textarea',
			  name: 'extra_options',
				anchor: '-20',
			  allowBlank:true,
			  fieldLabel: GO.postfixadmin.lang.extraOptions
			}
,{
				xtype: 'textarea',
			  name: 'returned_text',
				anchor: '-20',
			  allowBlank:true,
			  fieldLabel: GO.postfixadmin.lang.returnedText
			}
,{
				xtype: 'textfield',
			  name: 'mda',
				anchor: '-20',
			  allowBlank:false,
			  fieldLabel: GO.postfixadmin.lang.mda
			},{
				xtype: 'textfield',
			  name: 'date',
				anchor: '-20',
			  allowBlank:false,
			  fieldLabel: GO.postfixadmin.lang.date
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
			baseParams: {task: 'fetchmail_config'},				
			items:this.tabPanel				
		});
    
    
	}
});