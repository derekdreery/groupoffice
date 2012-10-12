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
 * @author Michael de Hart <mdhart@intermesh.nl>
 */
GO.servermanager.ModulePriceDialog = function(config){

	config = config || {};

	this.buildForm();

	var focusFirstField = function(){
		this.formPanel.items.items[0].focus();
	};
    
	config.layout='fit';
	config.title=GO.tickets.lang.modulePrice;
	config.modal=false;
	config.width=400;
	config.autoHeight=true;
	config.resizable=false;

	config.items=this.formPanel;
	config.focus=focusFirstField.createDelegate(this);
	config.buttons=[{
		text:GO.lang['cmdOk'],
		handler: function()
		{
			this.submitForm(true)
		},
		scope: this
	},{
		text:GO.lang['cmdApply'],
		handler: function()
		{
			this.submitForm(false)
		},
		scope: this
	},{
		text:GO.lang['cmdClose'],
		handler: function()
		{
			this.hide()
		},
		scope: this
	}];
		
	GO.servermanager.ModulePriceDialog.superclass.constructor.call(this,config);
	
	this.addEvents({'save' : true});
}

Ext.extend(GO.servermanager.ModulePriceDialog, Ext.Window, {
	
	show : function (record)
	{		
		if(!this.rendered)
			this.render(Ext.getBody());
			
		if(record)
		{
			this.status_id=record.data.id;
		}else
		{
			this.status_id=0;
		}
		
		if(this.status_id > 0)
		{
			this.formPanel.form.findField('name').setValue(record.data.name);
		}else
		{
			this.formPanel.form.reset();
		}
		GO.servermanager.ModulePriceDialog.superclass.show.call(this);
	},
	submitForm : function(hide)
	{
		this.formPanel.form.submit(
		{		
			url:GO.url("servermanager/price/moduleSubmit"),
			params: {
				id:this.module_name
			},
			waitMsg:GO.lang['waitMsgSave'],
			success:function(form, action)
			{
				if(action.result.id)
				{
					this.module_name=action.result.id;
				}
			
				this.fireEvent('save');
				
				if(hide)
				{
					this.hide();
				}
			},
			failure: function(form, action) 
			{
				var error = '';
				if(action.failureType=='client')
				{
					error = GO.lang['strErrorsInForm'];
				}
				else
				{
					error = action.result.feedback;
				}
				Ext.MessageBox.alert(GO.lang['strError'], error);
			},
			scope:this
		});		
	},
	buildForm : function () 
	{		
		this.formPanel = new Ext.FormPanel({
			autoHeight:true,
			labelWidth:100,
			defaultType: 'textfield',
			items: [
				{
					xtype: 'combo',
					fieldLabel: GO.servermanager.lang['moduleName'],
					mode: 'remote',
					autoLoad: true,
					triggerAction: 'all',
					hiddenName: 'module_name',
					store: new GO.data.JsonStore({
						id: 'module_name',
						url : GO.url('servermanager/installation/modules'),
						fields : ['id','name'],
						remoteSort : true,
						root : 'results'
					}),
					valueField: 'id',
					displayField: 'name'
				},
				{
					xtype: 'numberfield',
					fieldLabel: GO.servermanager.lang['modulePrice'],
					name: 'price_per_month',
					allowBlank:false
				}
			]
		});	
	}	
});