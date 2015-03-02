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
			fieldLabel: GO.sieve.lang.activateAt,
			listeners : {
				change : {
					fn : function(){
						this.scheduleDeactivateField.setValue(this.scheduleActivateField.getValue());
					},
					scope : this
				}
			}
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
			height:130,
			border: true,
			collapsed: false,
			items:[this.scheduleText,this.scheduleActivateField,this.scheduleDeactivateField],
			style: 'margin-right:10px; margin-bottom:5px;'
		});
		
		this.activateText = new GO.form.HtmlComponent({
			html:GO.sieve.lang.activateText,
			style:'padding:5px 0px'
		});
		
		this.activateCheck = new Ext.ux.form.XCheckbox({
				hideLabel: true,
				boxLabel: GO.sieve.lang.activate,
				name: 'ooo_script_active'
			});
		
		this.activateFieldset = new Ext.form.FieldSet({
			title: GO.sieve.lang.activate,
			height:130,
			border: true,
			collapsed: false,
			items:[this.activateText,this.activateCheck]
		});
		
		this.messageText = new GO.form.HtmlComponent({
			html:GO.sieve.lang.messageText,
			style:'padding:5px 0px'
		});
		
		this.messageField = new Ext.form.TextArea({
			name: 'ooo_message',
			allowBlank:false,
			anchor:'100%',
			height:130,
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
			height:70,
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
					
		this.indexField = new Ext.form.Hidden({
			name: 'ooo_script_index',
		});
			
		this.items = [
			this.scriptNameField,
			this.ruleNameField,
			this.indexField,
			{
				layout:'column',
				defaults:{columnWidth:.5, cls: 'go-form-panel', padding:'10'},
				items:[
					this.scheduleFieldset,
					this.activateFieldset
				]
			},
			this.messageFieldset,
			this.aliassesFieldset
		];

		GO.sieve.OutOfOfficePanel.superclass.initComponent.call(this,config);
	},
	
	disableFields : function(disable){
		this.scheduleActivateField.setDisabled(disable);
		this.scheduleDeactivateField.setDisabled(disable);
		this.messageField.setDisabled(disable);
		this.aliassesField.setDisabled(disable);
		this.scriptNameField.setDisabled(disable);
		this.ruleNameField.setDisabled(disable);
		this.activateCheck.setDisabled(disable);
		this.indexField.setDisabled(disable);
	},
	
	setAccountId : function(account_id){
		this.setDisabled(!account_id);
		this.accountId=account_id;
	}

});