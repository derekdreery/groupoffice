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

GO.addressbook.SearchQueryPanel = function(config)
	{
		if(!config){
			config = {};
		}

		config.title = GO.addressbook.lang.newSearch;

		this.typesStore = new GO.data.JsonStore({
			url: GO.settings.modules.addressbook.url+'json.php',
			baseParams: {
				task: "ab_fields",
				type:""
			},
			root: 'results',
			id: 'field',
			fields: ['field', 'label', 'value', 'type', 'options'],
			remoteSort: true
		});

		this.typesStore.on('load', function(){
			this.typesBox.selectFirst();
		},this);

		config.border=false;
		config.layout='form';
		config.defaults={hideLabel:true};
		config.bodyStyle='padding:5px;';

		//this.typesStore.load();

		var comparators = this.getComparators();

		GO.addressbook.queryField = this.queryField = new Ext.form.TextArea({
			anchor:'100%',
			height : 150
		});

		this.queryMakerPanel = new Ext.Panel({
			layout: 'table',
			border:false,
			defaults: {
				// applied to each contained panel
				bodyStyle:'padding-right:4px',
				border:false
			},

			items: [{
				items:this.operatorBox = new Ext.form.ComboBox({
					store: new Ext.data.SimpleStore({
						fields: ['value'],
						data : [
						['AND'],
						['OR']
						]
					}),
					value: 'AND',
					valueField:'value',
					displayField:'value',
					width: 60,
					mode: 'local',
					triggerAction: 'all',
					editable: true,
					selectOnFocus:true,
					forceSelection:true
				})
			},{
				items:this.typesBox = new GO.form.ComboBox({
					store: this.typesStore,
					valueField:'field',
					displayField:'label',
					mode: 'local',
					triggerAction: 'all',
					editable: true,
					selectOnFocus:true,
					forceSelection:true,
					emptyText: GO.addressbook.lang.selectField,
					width:180
				})
			},{
				items:this.comparatorBox = new Ext.form.ComboBox({
					store: new Ext.data.SimpleStore({
						fields: ['value'],
						data : comparators
					}),
					valueField:'value',
					displayField:'value',
					value: 'LIKE',
					width: 60,
					mode: 'local',
					triggerAction: 'all',
					editable: true,
					selectOnFocus:true,
					forceSelection:true
				})
			},{
				items:this.criteriumField = new Ext.form.TextField({
					//fieldLabel: GO.lang['strFirstName'],
					name: 'criterium',
					emptyText: GO.lang.keyword,
					width: 180,
					panel: this
				})
			},{
				items:new Ext.Button({
					handler: function()
					{
						var text = this.queryField.getValue();
						if (this.queryField.getValue() && this.operatorBox.value) text = text + ' ' + this.operatorBox.value;
						if (this.typesBox.value) text = text + ' ' + this.typesBox.value;
						if (this.comparatorBox.value) text = text + ' ' + this.comparatorBox.value;
						text = text + ' \'' + this.criteriumField.getValue() + '\'';
						this.queryField.setValue(text);
					},
					text: GO.lang.cmdAdd,
					scope: this
				})
			}]
		});

		this.buttonsPanel = new Ext.Panel({			
			buttonAlign: 'left',
			border:false,
			buttons: [new Ext.Button({
				handler: function()
				{
					Ext.Msg.prompt(GO.addressbook.lang.searchQueryName, GO.addressbook.lang.enterSearchQueryName, function(btn, text){
						Ext.Ajax.request({
							url:GO.settings.modules.addressbook.url +'action.php',
							params:{
								task:'save_sql',
								sql: GO.addressbook.queryField.getValue(),
								name: text,
								companies:this.typesStore.baseParams.type=='companies' ? '1' : '0'
							},
							success: function(response, options)
							{
								var responseParams = Ext.decode(response.responseText);
								if(!responseParams.success)
								{
									alert(responseParams.feedback);
								}else
								{
								}
							},
							scope:this
						})
					},this)
					},
				text: GO.lang.cmdSave,
				scope: this
			}),new Ext.Button({
				handler: function()
				{
					this.queryField.setValue('');
				},
				text: GO.lang.cmdReset,
				scope: this
			})]
		});

		// 	config.defaults={border: false, cls:'ab-search-form-panel'};
		config.items= [this.queryField,this.buttonsPanel,this.queryMakerPanel];

		GO.addressbook.SearchQueryPanel.superclass.constructor.call(this, config);
	}

Ext.extend(GO.addressbook.SearchQueryPanel, Ext.Panel, {

	getComparators : function() {
		return [
		['LIKE'],
		['NOT LIKE'],
		['='],
		['!='],
		['>'],
		['>='],
		['<'],
		['<=']
		];
	}

});