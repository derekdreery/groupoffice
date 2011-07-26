/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: ActionPanel.php 0000 2010-12-16 09:40:00Z wsmits $
 * @copyright Copyright Intermesh
 * @author Wesley Smits <wsmits@intermesh.nl>
 */

GO.sieve.ActionRecord = Ext.data.Record.create([
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

GO.sieve.ActionPanel = function(config){
	config = config || {};

	this.cmbAction = new GO.form.ComboBox({
		hiddenName: 'type',
		fieldLabel:GO.sieve.lang.action,
		valueField:'value',
		displayField:'field',
		store: GO.sieve.cmbActionStore,
		mode:'local',
		value: 'fileinto',
		triggerAction:'all',
		editable:false,
		selectOnFocus:true,
		forceSelection:true,
		allowBlank:false,
		width:150,
		emptyText:GO.sieve.lang.action
	});

	this.cmbFolder = new GO.form.ComboBox({
		hiddenName:'target',
		fieldLabel:GO.sieve.lang.folder,
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
		width:150,
		emptyText:GO.sieve.lang.folder
	});

	this.txtEmailAddress = new Ext.form.TextField({
		name: 'email',
		hidden:true,
		allowBlank:true,
		width:180,
		emptyText:GO.sieve.lang.addresses
	});

	this.txtMessage = new Ext.form.TextArea({
		name: 'message',
		hidden:true,
		allowBlank:false,
		hideLabel:true,
		anchor:'100%',
		height:80,
		emptyText:GO.sieve.lang.reason,
		listeners:{
			scope:this,
			focus: function(){
				this.setHeight(100);
			}
		}
	});

	this.txtDays = new Ext.form.TextField({
		name: 'days',
		hidden:true,
		allowBlank:true,
		width:170,
		emptyText:GO.sieve.lang.days
	});

	this.cmbAction.on('select', function(combo, record){
		this.setVisibleFields(record, false);
	},this);

	

	this.btnAddAction = new Ext.Button({
		text: GO.lang.cmdAdd,
		handler : function() {

			// Build up the data before adding the data to the grid.
			var _copy = false;
			var _type = '';
			var _target = '';
			var _days = '';
			var _addresses = '';
			var _reason = '';

			switch(this.cmbAction.getValue())
			{
				case 'fileinto':
					_copy		= false;
					_type		= 'fileinto';
					_target = this.cmbFolder.getValue();
					break;
				case 'copyto':
					_copy		= true;
					_type		= 'fileinto';
					_target = this.cmbFolder.getValue();
					break;
				case 'redirect':
					_copy		= false;
					_type		= 'redirect';
					_target = this.txtEmailAddress.getValue();
					break;
				case 'redirect_copy':
					_copy		= true;
					_type		= 'redirect';
					_target = this.txtEmailAddress.getValue();
					break;
				case 'reject':
					_copy		= '';
					_type		= 'reject';
					_target = this.txtMessage.getValue();
					break;
				case 'vacation':
					_copy = '';
					_type = 'vacation';
					_target = '';
					_days = this.txtDays.getValue();
					_addresses = this.txtEmailAddress.getValue();
					_reason = this.txtMessage.getValue();
					break;
				case 'discard':
					_copy		= '';
					_type		= 'discard';
					_target = '';
					break;
				case 'stop':
					_copy		= '';
					_type		= 'stop';
					_target = '';
					break;
			}

			var values = {
					type:_type,
					copy: _copy,
					target:_target,
					days:_days,
					addresses: _addresses,
					reason: _reason
				};

			var record;

			if(this.btnAddAction.getText() == GO.lang.cmdAdd)
			{
				record = new GO.sieve.ActionRecord(values);

				var insertId = this.grid.store.getCount();

				// Let the Stop stay on the end of the grid
//				if(this.grid.store.getCount() > 0)
//				{
					if(this.grid.store.getCount() > 0 && this.grid.store.getAt(this.grid.store.getCount()-1).data.type == 'stop'){
						insertId = this.grid.store.getCount()-1;
          }else
          {
            switch(this.cmbAction.getValue()){
              case 'redirect_copy':
              case 'vacation':
              break;
              default:
                var stopRecord = new GO.sieve.ActionRecord({
                      type:"stop",
                      copy: false,
                      target:"",
                      days:"",
                      addresses:"",
                      reason:""
                    });

                this.grid.store.insert(insertId, stopRecord);
                break;
            }
          }
				//}

				if(this.cmbAction.getValue() == 'vacation')
					insertId = 0;

				this.grid.store.insert(insertId, record);
			}
			else
			{
				record = this.grid.store.getAt(this.index);
				Ext.apply(record.data,values);
				record.commit();
			}
			this.resetForm();
		},
		scope : this
	})

	this.btnClearAction = new Ext.Button({
		text: GO.sieve.lang.clear,
		handler : function() {
			this.resetForm();
		},
		scope : this
	})

	config.bodyStyle='padding:5px';
	config.border=false;
	config.autoHeight=true;
	config.region='center';
	config.baseParams={
		task : 'addAction',
		account_id : 0,
		script_name : '',
		rule_name : '',
		script_index : 0
	};
	config.items=[
		{
			xtype:'compositefield',
			items:[
				this.cmbAction,
				this.cmbFolder,
				this.txtDays,
				this.txtEmailAddress,
				this.btnAddAction,
				this.btnClearAction
			],
			hideLabel:true
		},
		this.txtMessage];
	
	GO.sieve.ActionPanel.superclass.constructor.call(this, config);
}

Ext.extend(GO.sieve.ActionPanel, Ext.FormPanel,{

	index : 0,

	setVisibleFields : function(record, editmode, index){
		
		this.index=index ? index : 0;

		this.cmbFolder.reset();
		this.txtEmailAddress.reset();
		this.txtDays.reset();
		this.txtMessage.reset();
		
		var recVal = '';
		this.form.setValues(record.data);

		if(editmode)
		{
			this.btnAddAction.setText(GO.lang.cmdSave);
			this.btnClearAction.setText(GO.lang.cmdCancel);
			recVal = record.data.type;

			if(record.data.type == 'redirect' && record.data.copy == true)
			{
				recVal = 'redirect_copy';
				this.cmbAction.setValue(recVal);
				this.txtEmailAddress.setValue(record.data.target);
			}
			else if(record.data.type == 'fileinto' && record.data.copy == true)
			{
				recVal = 'copyto';
				this.cmbAction.setValue(recVal);
			}
			else if(record.data.type == 'vacation')
			{
				this.txtEmailAddress.setValue(record.data.addresses);
				this.txtMessage.setValue(record.data.reason);
			}
		}
		else
		{
			this.btnAddAction.setText(GO.lang.cmdAdd);
			this.btnClearAction.setText(GO.sieve.lang.clear);
			recVal = record.data.value;
		}

		switch(recVal)
		{
			case 'fileinto':
				this.cmbFolder.show();
				this.txtEmailAddress.hide();
				this.txtMessage.hide();
				this.txtDays.hide();
				break;
			case 'copyto':
				this.cmbFolder.show();
				this.txtEmailAddress.hide();
				this.txtMessage.hide();
				this.txtDays.hide();
				break;
			case 'redirect':
				this.cmbFolder.hide();
				this.txtEmailAddress.show();
				this.txtMessage.hide();
				this.txtDays.hide();
				break;
			case 'redirect_copy':
				this.cmbFolder.hide();
				this.txtEmailAddress.show();
				this.txtMessage.hide();
				this.txtDays.hide();
				break;
			case 'reject':
				this.cmbFolder.hide();
				this.txtEmailAddress.hide();
				this.txtMessage.show();
				this.txtDays.hide();
				break;
			case 'vacation':
				this.cmbFolder.hide();
				this.txtDays.show();
				this.txtEmailAddress.show();
				this.txtMessage.show();
				break;
			case 'discard':
				this.cmbFolder.hide();
				this.txtDays.hide();
				this.txtEmailAddress.hide();
				this.txtMessage.hide();
				break;
			case 'stop':
				this.cmbFolder.hide();
				this.txtEmailAddress.hide();
				this.txtDays.hide();
				this.txtMessage.hide();
				break;
		}
		this.doLayout();
	},
	resetForm : function(){
		this.btnAddAction.setText(GO.lang.cmdAdd);
		this.btnClearAction.setText(GO.sieve.lang.clear);
		this.form.reset();
		this.cmbFolder.show();
		this.txtEmailAddress.hide();
		this.txtMessage.hide();
		this.txtDays.hide();
		this.doLayout();
	},
	onShow : function(){
		GO.sieve.ActionPanel.superclass.onShow.call(this);
		this.actionGrid.store.load();
	}
});