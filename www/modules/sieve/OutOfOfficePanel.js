GO.sieve.OutOfOfficePanel = Ext.extend(Ext.Panel,{
	
	title:GO.sieve.lang.outOfOffice,
	layout:'form',
	autoScroll:true,
	
	accountId:0,
		
	initComponent : function(config){
		
		this.scheduleText = new GO.form.HtmlComponent({
			html:GO.sieve.lang.scheduleText,
			style:'padding:5px 0px'
		});
		
		this.scheduleActivateField = new Ext.form.DateField({
			name : 'ooo_activate',
			format : GO.settings['date_format'],
			width: 180,
			allowBlank : false,
			fieldLabel: GO.sieve.lang.activateAt
		});
		
		this.scheduleDeactivateField = new Ext.form.DateField({
			name : 'ooo_deactivate',
			format : GO.settings['date_format'],
			width: 180,
			allowBlank : false,
			fieldLabel: GO.sieve.lang.deactivateAt
		});
		
		this.scheduleFieldset = new Ext.form.FieldSet({
			title: GO.sieve.lang.schedule,
			autoHeight: true,
			border: true,
			collapsed: false,
			items:[this.scheduleText,this.scheduleActivateField,this.scheduleDeactivateField]
		});
		
		
		this.messageText = new GO.form.HtmlComponent({
			html:GO.sieve.lang.messageText,
			style:'padding:5px 0px'
		});
		
		this.messageField = new Ext.form.TextArea({
			name: 'ooo_message',
			allowBlank:false,
			anchor:'100%',
			height:140,
			width: 300,
			hideLabel: true
		});
		
		this.messageFieldset = new Ext.form.FieldSet({
			title: GO.sieve.lang.message,
			autoHeight: true,
			border: true,
			collapsed: false,
			items:[this.messageText,this.messageField]
		});
		
		
		this.aliassesText = new GO.form.HtmlComponent({
			html:GO.sieve.lang.aliassesText,
			style:'padding:5px 0px'
		});
		
		this.aliassesField = new Ext.form.TextArea({
			name: 'ooo_aliasses',
			allowBlank:true,
			anchor:'100%',
			height:80,
			width: 300,
			hideLabel: true
		});
		
		this.aliassesFieldset = new Ext.form.FieldSet({
			title: GO.sieve.lang.aliasses,
			autoHeight: true,
			border: true,
			collapsed: false,
			items:[this.aliassesText,this.aliassesField]
		});
			
		this.scriptNameField = new Ext.form.Hidden({
			name: 'ooo_script_name',
		});
		
		this.ruleNameField = new Ext.form.Hidden({
			name: 'ooo_rule_name',
		});
		
		this.activeField = new Ext.form.Hidden({
			name: 'ooo_script_active',
		});
			
		this.indexField = new Ext.form.Hidden({
			name: 'ooo_script_index',
		});
			
		this.items = [this.scriptNameField,this.ruleNameField,this.activeField,this.indexField,this.scheduleFieldset,this.messageFieldset,this.aliassesFieldset];

		GO.sieve.OutOfOfficePanel.superclass.initComponent.call(this,config);
	},
	
	disableFields : function(disable){
		this.scheduleActivateField.setDisabled(disable);
		this.scheduleDeactivateField.setDisabled(disable);
		this.messageField.setDisabled(disable);
		this.aliassesField.setDisabled(disable);
		this.scriptNameField.setDisabled(disable);
		this.ruleNameField.setDisabled(disable);
		this.activeField.setDisabled(disable);
		this.indexField.setDisabled(disable);
	},
	
	setAccountId : function(account_id){
		this.setDisabled(!account_id);
		this.accountId=account_id;
	}

});