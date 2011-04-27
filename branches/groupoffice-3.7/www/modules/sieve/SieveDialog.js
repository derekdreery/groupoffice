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
 */
GO.sieve.SieveDialog = function(config) {
	if (!config) {
		config = {};
	}
	
	this.rgMethod = new Ext.form.RadioGroup({
		columns: 3,
		anchor: '100%',
		value:'anyof',
		items: [
				{
					columnWidth:.4,
					items:{boxLabel: GO.sieve.lang.allfollowingrules, name: 'join', inputValue: 'allof'}
				},
				{
					columnWidth:.4,
					items:{boxLabel: GO.sieve.lang.somefollowingrules, name: 'join', inputValue: 'anyof'}
				},
				{
					columnWidth:.2,
					items:{boxLabel: GO.sieve.lang.allmessages, name: 'join', inputValue: 'any'}
				}
		],
		listeners:{
			scope:this,
			change:function(){
				if(this.rgMethod.getValue()){
					if(this.rgMethod.getValue().inputValue == 'any')
					{
						this.testsGrid.hide();
						this.testsPanel.hide();
					}
					else
					{
						if(this.testsGrid.store.getCount() > 0)
						{
							if(this.testsGrid.store.getAt(0).data.test == 'true')
							{
								this.testsGrid.store.removeAll();
							}
						}
						this.testsGrid.show();
						this.testsPanel.show();
					}
				}
			}
		},
		hideLabel:true
	})

	this.formPanel = new Ext.FormPanel({
		style:'padding:5px;',
		autoHeight:true,
		border:false,
		url:GO.settings.modules.sieve.url+'fileIO.php',
		baseParams:{task:'load_rule'},
		items:[{
			fieldLabel:GO.lang.strName,
			name:'rule_name',
			xtype:'textfield',
			allowBlank:false
		},{
			name:'disabled',
			checked:false,
			xtype:'checkbox',
			fieldLabel:GO.sieve.lang.disablefilter
		},
		new Ext.form.Label({text: GO.sieve.lang.ruletext,	width:'100%',	style: 'padding-bottom: 10px; font-weight:bold;'}),
		this.rgMethod
		]
	});

	// Make tests Grid and Panel
	this.testsGrid = new GO.sieve.TestsGrid();
	this.testsPanel = new GO.sieve.TestsPanel({
		grid:this.testsGrid
	});
	this.testsGrid.on('rowdblclick', function(grid, index, e){
		var record = this.testsGrid.store.getAt(index);
		this.testsPanel.setFormValues(record, index);
	},this);

	// Make action Grid and Panel
	this.actionGrid = new GO.sieve.ActionGrid();
	this.actionPanel = new GO.sieve.ActionPanel({
		grid:this.actionGrid
	});
	this.actionGrid.on('rowdblclick', function(grid, index, e){
		var record = this.actionGrid.store.getAt(index);
		this.actionPanel.setVisibleFields(record ,true, index);
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
				this.testsPanel,
				this.testsGrid,
				new Ext.form.Label({text:GO.sieve.lang.actiontext, width:'100%', style: 'padding-bottom: 10px; margin: 5px; font-weight:bold;'}),
				this.actionPanel,
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
		text : GO.lang['cmdOk'],
		handler : function() {
			if(this.actionGrid.store.getCount() == 0 || (this.testsGrid.store.getCount() == 0 && this.rgMethod.getValue().inputValue != 'any'))
				alert(GO.sieve.lang.erroremptygrids);
			else if(this.actionPanel.btnAddAction.getText() == GO.lang.cmdEdit || this.testsPanel.btnAddRule.getText() == GO.lang.cmdEdit)
				alert(GO.sieve.lang.errorineditmode);
			else
				this.saveAll();
		},
		scope : this
	}, {
		text : GO.lang['cmdClose'],
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
							this.testsGrid.store.loadData(action.result);
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
				this.resetForms();
				this.resetGrids();
				this.rgMethod.setValue('anyof');
			}
	},
	saveAll : function() {

		this.formPanel.form.submit({
			params : {
				'task' : 'save_sieve_rules',
				'tests' : Ext.encode(this.testsGrid.getGridData()),
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
	resetForms : function(){
		this.formPanel.form.reset();
		this.testsPanel.resetForm();
		this.actionPanel.resetForm();
	},
	resetGrids : function(){
		this.actionGrid.store.removeAll();
		this.testsGrid.store.removeAll();   
	}	
});
