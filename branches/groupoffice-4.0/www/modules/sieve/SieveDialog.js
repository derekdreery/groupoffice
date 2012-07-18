/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: SieveDialog.js 9261 2010-12-15 10:37:31Z wsmits $
 * @copyright Copyright Intermesh
 * @author Wesley Smits <wsmits@intermesh.nl>
 * @author WilmarVB <wilmar@intermesh.nl>
 */
GO.sieve.SieveDialog = function(config) {
	if (!config) {
		config = {};
	}
	
	this.rgMethod = new Ext.form.RadioGroup({
		fieldLabel: '<b>'+GO.sieve.lang.ruletext+'</b>',
		columns: 1,
		vertical: true,
		anchor: '100%',
		value:'anyof',
		items: [
				{
					boxLabel: GO.sieve.lang.allfollowingrules, name: 'join', inputValue: 'allof'
				},
				{
					boxLabel: GO.sieve.lang.somefollowingrules, name: 'join', inputValue: 'anyof'
				},
				{
					boxLabel: GO.sieve.lang.allmessages, name: 'join', inputValue: 'any'
				}
		],
		listeners:{
			scope:this,
			change:function(){
				if(this.rgMethod.getValue()){
					if(this.rgMethod.getValue().inputValue == 'any')
					{
						this.criteriaLabel.hide();
						this.criteriumGrid.hide();
					}
					else
					{
						if(this.criteriumGrid.store.getCount() > 0)
						{
							if(this.criteriumGrid.store.getAt(0).data.test == 'true')
							{
								this.criteriumGrid.store.removeAll();
							}
						}
						this.criteriaLabel.show();
						this.criteriumGrid.show();
					}
				}
			}
		}
	})

	this.formPanel = new Ext.FormPanel({
		style:'padding:5px;',
		autoHeight:true,
		border:false,
		url: GO.url('sieve/sieve/rule'),
		baseParams:{},
		items:[{
				fieldLabel:GO.lang.strName,
				name:'rule_name',
				xtype:'textfield',
				width: 360,
				allowBlank:false
			},{
				name:'active',
				checked:true,
				xtype:'checkbox',
				fieldLabel:GO.sieve.lang.activateFilter
			},
			this.rgMethod,
			this.criteriaLabel = new Ext.form.Label({text: '...'+GO.sieve.lang.meetingTheseCriteria+':',	width:'100%',	style: 'padding-bottom: 10px; font-weight:bold;'}),
		]
	});

	// Make tests Grid and Panel
	this.criteriumGrid = new GO.sieve.CriteriumGrid();

	// Make action Grid and Panel
	this.actionGrid = new GO.sieve.ActionGrid();
	this.actionGrid.on('rowdblclick', function(grid, index, e){
		var record = this.actionGrid.store.getAt(index);
		this.actionGrid.showActionCreatorDialog(index);
	},this);
	
	this.currentScriptName = '';
	this.currentRuleName = '';
	this.currentScriptIndex = 0;
	this.currentAccountId = 0;

	config.items = {
		autoScroll:true,
		layout:'anchor',
		items:[
				this.formPanel,
				this.criteriumGrid,
				new Ext.form.Label({text:GO.sieve.lang.actiontext, width:'100%', style: 'padding-bottom: 10px; margin: 5px; font-weight:bold;'}),
				this.actionGrid
			]
		};
			
	config.collapsible = true;
	config.maximizable = true;
	config.layout = 'fit';
	config.modal = false;
	config.resizable = true;
	config.width = 700;
	config.height = 640;
	config.closeAction = 'hide';
	config.title = GO.sieve.lang.sieverules;
	config.buttons = [{
		text : GO.sieve.lang.cmdSaveChanges,
		handler : function() {
			if(this.actionGrid.store.getCount() == 0 || (this.criteriumGrid.store.getCount() == 0 && this.rgMethod.getValue().inputValue != 'any'))
				alert(GO.sieve.lang.erroremptygrids);
			else
				this.saveAll();
		},
		scope : this
	}, {
		text : GO.lang['cmdCancel'],
		handler : function() {
			this.hide();
		},
		scope : this
	}];

	GO.sieve.SieveDialog.superclass.constructor.call(this, config);
	
	this.addEvents({
		'save' : true
	});
}
Ext.extend(GO.sieve.SieveDialog, GO.Window, {
	show : function(script_index,script_name, account_id) {

			GO.sieve.SieveDialog.superclass.show.call(this);
			
			this.formPanel.baseParams.script_index = script_index;
			this.formPanel.baseParams.account_id = account_id;
			this.formPanel.baseParams.script_name = script_name;

			if(script_index > -1)
			{	
				this.title = GO.sieve.lang.editsieve;
	
				this.formPanel.load({
					success:function(form, action)
					{
						this.rgMethod.setValue(action.result.data.join);
						this.actionGrid.store.loadData(action.result);
						this.criteriumGrid.store.loadData(action.result);
					},
					failure:function(form, action)
					{
						Ext.Msg.alert(GO.lang['strError'], action.result.feedback)
					},
					scope: this
				});		
			}
			else
			{
				this.title = GO.sieve.lang.newsieverule;
				this.formPanel.form.setValues({
					'rule_name' : '',
					'disabled' : false
				});
				this.resetGrids();
				this.rgMethod.setValue('anyof');
			}
	},
	
	saveAll : function() {

		this.formPanel.form.submit({
			url: GO.url('sieve/sieve/submitRules'),
			params : {
//				'task' : 'save_sieve_rules',
				'criteria' : Ext.encode(this.criteriumGrid.getGridData()),
				'actions' : Ext.encode(this.actionGrid.getGridData())
			},
			success : function(form, action) {
					this.hide();
					this.body.unmask();
			},
			failure: function(form, action) {
				if(action.failureType == 'client')
				{					
					Ext.MessageBox.alert(GO.lang['strError'], GO.lang['strErrorsInForm']);			
				} else {
					Ext.MessageBox.alert(GO.lang['strError'], action.result.feedback);
				}
				this.body.unmask();
			},
			scope : this
		});
	},

	resetGrids : function(){
		this.actionGrid.store.removeAll();
		this.criteriumGrid.store.removeAll();   
	}	
});
