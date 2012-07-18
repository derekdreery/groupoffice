/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @copyright Copyright Intermesh
 * @author Wesley Smits <wsmits@intermesh.nl>
 * @author WilmarVB <wilmar@intermesh.nl>
 */

GO.sieve.ActionRecord = Ext.data.Record.create([
	{
		name: 'id',
		type: 'integer'
	},
	{
		name: 'type',
		type: 'string'
	},
	{
		name: 'copy',
		type: 'string'
	},
	{
		name: 'target',
		type: 'string'
	},
	{
		name: 'days',
		type: 'string'
	},
	{
		name: 'addresses',
		type: 'string'
	},
	{
		name: 'reason',
		type: 'string'
	}]);

GO.sieve.ActionCreatorDialog = function(config){
	config = config || {};

	this._buildForm();

//	this.btnAddAction = new Ext.Button({
//		text: GO.lang.cmdAdd,
//		handler : function() {
//			this._prepareValuesForStorage();
//			this.resetForm();
//		},
//		scope : this
//	})
//
//	this.btnClearAction = new Ext.Button({
//		text: GO.sieve.lang.clear,
//		handler : function() {
//			this.resetForm();
//		},
//		scope : this
//	})

	config.title=GO.sieve.lang.setAction;
	config.border=false;
	config.layout= 'fit';
	config.height=400;
	config.width=480;
	config.baseParams={
		task : 'addAction',
		account_id : 0,
		script_name : '',
		rule_name : '',
		script_index : 0
	};
	config.items=[this.formPanel];
	
	config.buttons = [{
		text : GO.lang['cmdOk'],
		handler : function() {
			if (this.formPanel.getForm().isValid()) {
				this.fireEvent('actionPrepared',this._prepareValuesForStorage());
				this.hide();
				this._resetForm();
			}
		},
		scope : this
	}, {
		text : GO.lang['cmdCancel'],
		handler : function() {
			this.hide();
			this._resetForm();
		},
		scope : this
	}];
	
	GO.sieve.ActionCreatorDialog.superclass.constructor.call(this, config);
	
	this.addEvents({'actionPrepared':true});
}

Ext.extend(GO.sieve.ActionCreatorDialog, GO.Window,{

	_recordId : -1,

	show : function(record) {
		this._recordId=-1;
		this._resetForm();
		if (typeof(record)=='object') {
			this._recordId = record.get('id');
			this.cmbAction.setValue(record.get('type'));
			this._transForm(record.get('type'));
			switch (record.get('type')) {
				case 'fileinto':
				case 'copyto':
					this.cmbFolder.setValue(record.get('target'));
					break;
				case 'redirect':
				case 'redirect_copy':
					this.txtEmailAddress.setValue(record.get('target'));
					break;
				case 'reject':
					this.txtMessage.setValue(record.get('value'));
					break;
				case 'vacation':
					this.txtEmailAddressOptional.setValue(record.get('addresses'));
					this.txtDays.setValue(record.get('days'));
					this.txtMessage.setValue(record.get('reason'));
					break;
				default:
					break;
			}
		}
		GO.sieve.ActionCreatorDialog.superclass.show.call(this);
	},

	_prepareValuesForStorage : function() {
		// Build up the data before adding the data to the grid.
		var _copy = false;
		var _type = '';
		var _target = '';
		var _days = '';
		var _addresses = '';
		var _reason = '';
		var _text = '';

		switch(this.cmbAction.getValue())
		{
			case 'fileinto':
				_copy		= false;
				_type		= 'fileinto';
				_target = this.cmbFolder.getValue();
				_text		= GO.sieve.lang.fileinto+': '+_target;
				
				break;
			case 'copyto':
				_copy		= true;
				_type		= 'fileinto';
				_target = this.cmbFolder.getValue();
				_text		= GO.sieve.lang.copyto+': '+_target;
				break;
			case 'redirect':
				_copy		= false;
				_type		= 'redirect';
				_target = this.txtEmailAddress.getValue();
				_text		= GO.sieve.lang.forwardto+': '+_target;
				break;
			case 'redirect_copy':
				_copy		= true;
				_type		= 'redirect';
				_target = this.txtEmailAddress.getValue();
				_text		= GO.sieve.lang.sendcopyto+': '+_target;
				break;
			case 'reject':
				_copy		= '';
				_type		= 'reject';
				_target = this.txtMessage.getValue();
				_text		= GO.sieve.lang.reject+': "'+_target+'"';
				break;
			case 'vacation':
				_copy = '';
				_type = 'vacation';
				_target = '';
				_days = this.txtDays.getValue();
				_addresses = this.txtEmailAddressOptional.getValue();
				_reason = this.txtMessage.getValue();
				if (!GO.util.empty(_addresses))
					var addressesText = GO.sieve.lang.vacAlsoMailTo+': '+_addresses+'. ';
				else
					var addressesText = '';
				_text = GO.sieve.lang.vacsendevery+' '+_days+' '+GO.sieve.lang.vacsendevery2+'. '+addressesText+GO.sieve.lang.vacationmessage+' "'+_reason+'"';
				break;
			case 'discard':
				_copy		= '';
				_type		= 'discard';
				_target = '';
				_text		= GO.sieve.lang.discard;
				break;
			case 'stop':
				_copy		= '';
				_type		= 'stop';
				_target = '';
				_text		= GO.sieve.lang.stop;
				break;
			default:
				break;
		}

		return {
			id : this._recordId,
			type:_type,
			copy: _copy,
			target:_target,
			days: _days,
			reason: _reason,
			addresses: _addresses,
			text: _text
		};
	},

	_resetForm : function(){
		this.formPanel.getForm().reset();
		this._toggleComponentUse(this.cmbAction,true);
		this._toggleComponentUse(this.cmbFolder,false);
		this._toggleComponentUse(this.txtEmailAddressOptional,false);
		this._toggleComponentUse(this.txtEmailAddress,false);
		this._toggleComponentUse(this.txtMessage,false);
		this._toggleComponentUse(this.txtDays,false);
		this.doLayout();
	},

	_transForm : function(type) {
		switch (type) {
			case 'fileinto':
			case 'copyto':
				this._toggleComponentUse(this.cmbFolder,true);
				this._toggleComponentUse(this.txtEmailAddressOptional,false);
				this._toggleComponentUse(this.txtEmailAddress,false);
				this._toggleComponentUse(this.txtDays,false);
				this._toggleComponentUse(this.txtMessage,false);
				break;
			case 'redirect':
			case 'redirect_copy':
				this._toggleComponentUse(this.cmbFolder,false);
				this._toggleComponentUse(this.txtEmailAddressOptional,false);
				this._toggleComponentUse(this.txtEmailAddress,true);
				this._toggleComponentUse(this.txtDays,false);
				this._toggleComponentUse(this.txtMessage,false);
				break;
			case 'reject':
				this._toggleComponentUse(this.cmbFolder,false);
				this._toggleComponentUse(this.txtEmailAddressOptional,false);
				this._toggleComponentUse(this.txtEmailAddress,false);
				this._toggleComponentUse(this.txtDays,false);
				this._toggleComponentUse(this.txtMessage,true);
				break;
			case 'vacation':
				this._toggleComponentUse(this.cmbFolder,false);
				this._toggleComponentUse(this.txtEmailAddressOptional,true);
				this._toggleComponentUse(this.txtEmailAddress,false);
				this._toggleComponentUse(this.txtDays,true);
				this._toggleComponentUse(this.txtMessage,true);
				break;
			case 'discard':
			case 'stop':
				this._toggleComponentUse(this.cmbFolder,false);
				this._toggleComponentUse(this.txtEmailAddressOptional,false);
				this._toggleComponentUse(this.txtEmailAddress,false);
				this._toggleComponentUse(this.txtDays,false);
				this._toggleComponentUse(this.txtMessage,false);
				break;
		}
	},

	_buildForm : function() {
		this.cmbAction = new GO.form.ComboBox({
			hiddenName: 'type',
			fieldLabel:GO.sieve.lang.action,
			valueField:'value',
			displayField:'field',
			store: GO.sieve.cmbActionStore,
			mode:'local',
			triggerAction:'all',
			editable:false,
			selectOnFocus:true,
			forceSelection:true,
			allowBlank:false,
			width:300,
			emptyText:GO.sieve.lang.action
		});

		this.cmbFolder = new GO.form.ComboBox({
			hiddenName:'target',
			fieldLabel:GO.sieve.lang.toFolder,
			valueField:'name',
			value: 'INBOX',
			displayField:'name',
			store: GO.email.subscribedFoldersStore,
			mode:'local',
			triggerAction:'all',
			editable:false,
			selectOnFocus:true,
			forceSelection:true,
			allowBlank:false,
			width:300,
			hidden: true,
			disabled: true
		});

		this.txtEmailAddressOptional = new Ext.form.TextArea({
			name: 'email',
			allowBlank:true,
			width:300,
			fieldLabel:GO.sieve.lang.addressesLabelOptional,
			hidden: true,
			disabled: true
		});

		this.txtEmailAddress = new Ext.form.TextArea({
			name: 'email',
			allowBlank:false,
			width:300,
			fieldLabel:GO.sieve.lang.addressesLabel,
			hidden: true,
			disabled: true
		});

		this.txtMessage = new Ext.form.TextArea({
			name: 'message',
			allowBlank:false,
//			hideLabel:true,
			anchor:'100%',
			height:80,
			fieldLabel:GO.sieve.lang.reason,
//			listeners:{
//				scope:this,
//				focus: function(){
//					this.setHeight(100);
//				}
//			},
			disabled: true,
			hidden: true
		});

		this.txtDays = new GO.form.NumberField({
			name: 'days',
			value: 3,
			hidden:true,
			allowBlank:false,
			width:70,
			decimals:0,
			fieldLabel:GO.sieve.lang.days,
			disabled: true,
			hidden: true
		});
			
		this.formPanel = new Ext.form.FormPanel({
			bodyStyle: 'padding: 5px;',
			layout: 'form',
			border:false,
			items: [
				this.cmbAction,
				this.cmbFolder,
				this.txtDays,
				this.txtMessage,
				this.txtEmailAddressOptional,
				this.txtEmailAddress
			]
		});
		
		this.cmbAction.on('select',function(combo,record,index){
			this.cmbFolder.reset();
			this.txtEmailAddress.reset();
			this.txtDays.reset();
			this.txtMessage.reset();
			this._transForm(record.data.value);
		},this);
	},
	
	_toggleComponentUse : function (component,use) {
		if (use==true) {
			component.show();
			component.setDisabled(false);
		} else {
			component.hide();
			component.setDisabled(true);
		}
	}
});