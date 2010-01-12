GO.addressbook.SearchQueryPanel = function(config)
	{
		if(!config){
			config = {};
		}

		config.title = 'Nieuwe zoekopdracht';

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

		//config.layout='table';
		config.split=true;

		this.typesStore.load();

		var comparators = this.getComparators();

		this.queryField = new Ext.form.TextArea({
			fieldLabel : 'Zoekopdracht',
			width : 585,
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
						fields: ['value','text'],
						data : [
						['','--'],
						['AND','AND'],
						['OR','OR']
						]
					}),
					valueField:'value',
					displayField:'text',
					width: 60,
					mode: 'local',
					triggerAction: 'all',
					editable: true,
					selectOnFocus:true,
					forceSelection:true
				})
			},{
				items:this.typesBox = new Ext.form.ComboBox({
					store: this.typesStore,
					valueField:'field',
					displayField:'label',
					mode: 'local',
					triggerAction: 'all',
					editable: true,
					selectOnFocus:true,
					forceSelection:true,
					emptyText: 'kies type'
				})
			},{
				items:this.comparatorBox = new Ext.form.ComboBox({
					store: new Ext.data.SimpleStore({
						fields: ['value'],
						data : comparators
					}),
					valueField:'value',
					displayField:'value',
					value: '=',
					width: 50,
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
					emptyText: 'criterium',
					width: 180,
					panel: this
				})
			},{
				items:new Ext.Button({
					handler: function()
					{
						var text = this.queryField.getValue();
						if (this.typesBox.value) text = text + ' ' + this.typesBox.value;
						if (this.queryField.getValue() && this.operatorBox.value) text = text + ' ' + this.operatorBox.value;
						if (this.comparatorBox.value) text = text + ' ' + this.comparatorBox.value;
						if (this.criteriumField.getValue()) text = text + ' \'' + this.criteriumField.getValue() + '\'';
						this.queryField.setValue(text);
					},
					text: 'voeg toe',
					scope: this
				})
			}]
		});

		this.buttonsPanel = new Ext.Panel({
			layout: 'table',
			buttonAlign: 'left',
			buttons: [new Ext.Button({
				handler: function()
				{
					Ext.Ajax.request({
						url:GO.settings.modules.addressbook.url +'action.php',
						params:{
							task:'save_sql',
							sql: this.queryField.getValue()
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
				},
				text: 'opslaan',
				scope: this
			}),new Ext.Button({
				handler: function()
				{
					this.queryField.setValue('');
				},
				text: 'reset',
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