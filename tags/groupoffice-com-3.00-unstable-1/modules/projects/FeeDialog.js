/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @version $Id: FeeDialog.js 2635 2008-07-18 11:00:31Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 */

 
GO.projects.FeeDialog = function(config){
	
	
	if(!config)
	{
		config={};
	}
	
	
	this.buildForm();
	
	
	this.permissionsTab = new GO.grid.PermissionsPanel({
			title: GO.lang['strPermissions']
		});
		
	this.tabPanel = new Ext.TabPanel({
	    activeTab: 0,
	    layoutOnTabChange:true,
	  	border: false,    	
	    items: [
	    	this.formPanel,
	    	this.permissionsTab
	    ]
	  }) ;    
	
	var focusName = function(){
		this.nameField.focus();		
	};

	config.maximizable=true;
	config.layout='fit';
	config.modal=false;
	config.resizable=false;
	config.width=400;
	config.height=400;
	config.closeAction='hide';
	config.title= GO.projects.lang.fee;					
	config.items= this.tabPanel;
	config.focus= focusName.createDelegate(this);
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

	
	GO.projects.FeeDialog.superclass.constructor.call(this, config);
	
	
	this.addEvents({'save' : true});	
}

Ext.extend(GO.projects.FeeDialog, Ext.Window,{

	
	show : function (config) {
		
		if(!this.rendered)
			this.render(Ext.getBody());
		
		if(!config)
		{
			config={};
		}
		
		
		if(!config.fee_id)
		{
			config.fee_id=0;			
		}		
		this.setFeeId(config.fee_id);
		
		
		if(config.fee_id>0)
		{
			this.formPanel.load({
				url : GO.settings.modules.projects.url+'json.php',
				waitMsg:GO.lang['waitMsgLoad'],
				success:function(form, action)
				{
					this.permissionsTab.setAcl(action.result.data.acl_id);
					GO.projects.FeeDialog.superclass.show.call(this);
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
			this.permissionsPanel.setDisabled(true);
			GO.projects.FeeDialog.superclass.show.call(this);
		}
	},
	
	setFeeId : function(fee_id)
	{
		this.formPanel.form.baseParams['fee_id']=fee_id;
		this.fee_id=fee_id;	
	},
	
	submitForm : function(){
		this.formPanel.form.submit(
		{
			url:GO.settings.modules.projects.url+'action.php',
			params: {'task' : 'save_fee'},
			waitMsg:GO.lang['waitMsgSave'],
			success:function(form, action){
				
				if(action.result.fee_id)
				{
					this.permissionsTab.setAcl(action.result.acl_id);
					this.setFeeId(action.result.fee_id);					
				}
				
				this.fireEvent('save', this);					
			},		
			failure: function(form, action) {
				Ext.MessageBox.alert(GO.lang['strError'], action.result.feedback);
			},
			scope: this
		});
		
	},
	
	
	buildForm : function () {
		

		this.nameField = new Ext.form.TextField({
		    name: 'name',
		    fieldLabel: GO.lang['strName'],
		    anchor:'100%',
		    allowBlack:false
		});
		
		var timeField = new GO.form.NumberField({
        name: 'time',
      	width: 40,
        allowBlank:false,
        fieldLabel: GO.projects.lang.valueMinutes,
        decimals:0,
        value: GO.util.numberFormat("60",0,GO.settings.decimal_seperator, GO.settings.thousands_seperator)
  	});
  	
  	var intFeeField = new GO.form.NumberField({
        name: 'internal_value',
      	width: 40,
        allowBlank:false,
        fieldLabel: GO.projects.lang.internalFee,
        value: GO.util.numberFormat("0",2,GO.settings.decimal_seperator, GO.settings.thousands_seperator)
  	});  	      		
  	var extFeeField = new GO.form.NumberField({
        name: 'external_value',
      	width: 40,
        allowBlank:false,
        fieldLabel: GO.projects.lang.externalFee,
        value: GO.util.numberFormat("0",2,GO.settings.decimal_seperator, GO.settings.thousands_seperator)
  	});  	      		
  	  	      		
		
    this.formPanel = new Ext.form.FormPanel(
		{
			title: GO.lang['strProperties'],
			border: false,
			cls:'go-form-panel',
			baseParams: {task: 'fee'},
			autoHeight:true,
      items: [
      	this.nameField,
      	timeField,
      	intFeeField,
      	extFeeField
      ]
		});
	}
});