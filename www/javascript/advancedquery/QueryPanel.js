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

GO.advancedquery.SearchQueryPanel = function(config)
{
	if(!config){
		config = {};
	}
	this.typesStore = new GO.data.JsonStore({
		url: config.fieldsUrl,
		baseParams: {
			task: 'advanced_query_fields'
		},
		root: 'results',
		id: 'name',
		fields: ['name','value','type','fields', 'custom'],
		remoteSort: true
	});

	config.border=false;
	config.layout='form';
	config.defaults={
		hideLabel:true,
		border:false,
		bodyStyle:'padding-left:4px'
	};
	config.bodyStyle='padding:5px;';

	this.queryField = new Ext.form.TextArea({
		name: 'query',
		anchor:'100%',
		height : 130
	});

	config.items= [this.queryField,{
			layout: 'table',
			anchor:'100%',
			items:
			[this.operatorBox = new GO.form.ComboBox({
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
				name:'query_operator',
				width: 60,
				mode: 'local',
				triggerAction: 'all',
				editable: true,
				selectOnFocus:true,
				forceSelection:true
			}),this.typesBox = new GO.form.ComboBox({
					store: this.typesStore,
					valueField:'value',
					displayField:'name',
					mode: 'local',
					triggerAction: 'all',
					editable: true,
					selectOnFocus:true,
					forceSelection:true,
					name:'query_type',
					//anchor:'100%'
					width:183
				})
			,this.comparatorBox = new Ext.form.ComboBox({
					store: new Ext.data.SimpleStore({
						fields: ['value'],
						data : this.getComparators()
					}),
					name:'query_comparator',
					valueField:'value',
					displayField:'value',
					width: 85,
					mode: 'local',
					triggerAction: 'all',
					editable: true,
					selectOnFocus:true,
					forceSelection:true
				})
			]
		},
		{
			layout: 'table',
			defaults:{border:false},
			items:
			[{
				defaults:{border:false},
				items:this.criteriumPanel = new Ext.Panel({
					border: false,
					items: [this.currentCriteriumField =
					this.criteriumComboBox = new GO.form.ComboBox({
						store: new Ext.data.SimpleStore({
							fields: ['value'],
							data: new Array()
						}),
						valueField:'value',
						displayField:'value',
						width: 295,
						mode: 'local',
						triggerAction: 'all',
						editable: true,
						selectOnFocus:true,						
						hideLabel: true
					}),this.criteriumTextField = new Ext.form.TextField({
						hidden: true,
						name: 'textfield',
						hideLabel: true,
						emptyText: GO.lang.keyword,
						width: 295,
						panel: this
					}),this.criteriumDatePanel = new Ext.Panel({
						border: false,
						style: 'padding:0px;',
						hidden: true,
						width: 295,
						items: [GO.customfields.dataTypes.date.getFormField({
							name:'',
							dataname:'date'
						})],
						hidden:true
					}),this.criteriumNumberPanel = new Ext.Panel({
						border: false,
						style: 'padding:0px;',
						hidden: true,
						width: 295,
						items: [
							GO.customfields.dataTypes.number.getFormField({dataname:''})
						],
						hidden:true
					}),this.criteriumCheckboxPanel = new Ext.Panel({						
						border: false,
						style: 'padding:0px;',
						hidden: true,
						width: 295,
						items: [
							GO.customfields.dataTypes.checkbox.getFormField({dataname:'',name:'checked'})
						],
						hidden:true
					}),this.criteriumFileField = new GO.files.SelectFile({
						filesFilter:'foldersonly',
						width: 295,
						hidden: true
						//fieldLabel:GO.filesearch.lang.searchOneFolder
					}),this.criteriumUserField = new GO.form.SelectUser({
						allowBlank:true,
						width: 295,
						hidden: true,
						listeners: {
							scope: this,
							select: function(combobox,record){
									combobox.setValue(record.data.id+':'+record.data.name);
								}
						}
					}),this.criteriumContactField = new GO.addressbook.SelectContact({
						width: 295,
						hidden: true,
						listeners: {
							scope: this,
							select: function(combobox,record){
									combobox.setValue(record.data.id+':'+record.data.name);
								}
						}
					})
					]
				})
			},{
				items:new Ext.Button({
					handler: function()
					{
						var text = this.queryField.getValue();
						if (this.queryField.getValue() && this.operatorBox.value) text = text + '\r\n' + this.operatorBox.value;
						if (this.typesBox.value) text = text + ' ' + this.typesBox.value;
						if (this.comparatorBox.value) text = text + ' ' + this.comparatorBox.value;
						if (this.currentCriteriumField.name=='textfield') {
							var string = this.currentCriteriumField.getValue();
							if (string.substring(string.length-1)!='%');
								string = string+'%';
							if (string.substring(0,1)!='%');
								string = '%'+string;
							this.currentCriteriumField.setValue(string);
						}
						if (typeof(this.currentCriteriumField.getValue)!='undefined') {
							if (this.currentCriteriumField.value)
								text = text + ' \'' + this.currentCriteriumField.value + '\'';
							else
								text = text + ' \'' + this.currentCriteriumField.items.items[0].getValue() + '\'';
						} else if (this.currentCriteriumField.name=='checkbox') {
							if (this.currentCriteriumField.items.items[0].getValue())
								text = text + ' \'1\'';
							else
								text = text + ' \'0\'';
						} else if (this.typesBox.getValue()=='`creation_time`' || this.typesBox.getValue()=='`last_modified_time`') {
							text = text + ' unix_timestamp(\'' + this.currentCriteriumField.items.items[0].getValue().dateFormat('Y-m-d') + '\')';
						} else if (this.currentCriteriumField.items.items[0].name=='date') {
							text = text + ' \'' + this.currentCriteriumField.items.items[0].getValue().dateFormat('Y-m-d') + '\'';
						} else {
							text = text + ' \'' + this.currentCriteriumField.items.items[0].getValue() + '\'';
						}
						this.queryField.setValue(text);

					},
					text: '+',
					scope: this
				})
			}]
		}];
	

	GO.advancedquery.SearchQueryPanel.superclass.constructor.call(this, config);

	

	this.typesBox.on('select', function(combo, record,index){
		this.typeChange(record);
	},this);

	//this.typesStore.load();

	this.typesBox.store.on('load', function(){
		this.typesBox.selectFirst();
		
		this.typeChange(this.typesBox.store.getAt(0));

		//for the reset action
		this.typesBox.originalValue=this.typesBox.getValue();
		
	},this);

	this.on('render', function(){
		this.typesStore.load();
	}, this);
}

Ext.extend(GO.advancedquery.SearchQueryPanel, Ext.Panel, {

	criteriumFields : {},
	customCriteriumPanels : {},

	dynamicCriteriumPanels : {},

	typeChange : function(record){
		if (record.data.type=='combobox') {
			this.criteriumComboBox.store = new Ext.data.SimpleStore({
				fields: ['value'],
				data: record.data.fields
			});

			this.criteriumComboBox.setValue(record.data.fields[0]);
		}
		this.currentCriteriumField.hide();
		this.currentCriteriumField = this.getCriteriumField(record.data);
		if (this.currentCriteriumField.name=='textfield')
			this.currentCriteriumField.reset();
		this.currentCriteriumField.show();
		var comparators = this.getComparators(record.data);
		this.comparatorBox.store.loadData(comparators);

		this.comparatorBox.setValue(comparators[0][0]);
		
		//for the reset action
		this.comparatorBox.originalValue=this.comparatorBox.getValue();

	},

	getComparators : function(type_data) {
		if (!type_data)
			var type_data = {
				type: ''
			};

		switch(type_data.type) {
			case 'combobox':
			case 'checkbox':
			case 'file':
			case 'user':
			case 'contact':
				return [['='],['!=']];
				break;
//			case 'textarea':
//				return [['LIKE'],
//					['NOT LIKE'],
//					['='],
//					['!=']];
//				break;
			case 'number':
			return [
				['='],
				['!='],
				['>'],
				['>='],
				['<'],
				['<=']
				];
				break;
			case 'date':
				return [
				['>='],
				['<'],
				];
				break;
			default: //textfield
				return [
				['LIKE'],
				['NOT LIKE'],
				['='],
				['!=']
				];
				break;
		}
	},

	getCriteriumField : function(type_data) {
		if (!type_data)
			var type_data = {
				type: 'textfield'
			};

		switch(type_data.type) {
			case 'combobox':
				return this.criteriumComboBox;
				break;
			//case 'textarea':
				//return this.criteriumTextAreaPanel;
				//break;
			case 'file':
				return this.criteriumFileField;
				break;
			case 'contact':
				return this.criteriumContactField;
				break;
			case 'user':
				return this.criteriumUserField;
				break;
			case 'date':
				return this.criteriumDatePanel;
			break;
			case 'datetime':
				//this.criteriumDatePanel.removeAll();
				//this.criteriumDatePanel.add(GO.customfields.dataTypes.datetime.getFormField({dataname:type_data.name,required:false}));

				//Still buggy piece of code, probably will not be used

				this.criteriumDatePanel.items.items[0].items.items[0].on('select',function(datefield,date){
					var hour = this.criteriumDateTimePanel.items.items[0].items.items[1].value;
					var min = this.criteriumDateTimePanel.items.items[0].items.items[2].value;
					this.criteriumDateTimePanel.value=date;
				},this);
				this.criteriumDateTimePanel.items.items[0].items.items[1].on('select',function(combobox,record){
					var date = this.criteriumDateTimePanel.items.items[0];
					var hour = this.criteriumDateTimePanel.items.items[0].items.items[1].value;
					var min = this.criteriumDateTimePanel.items.items[0].items.items[2].value;
					date.add(Date.HOUR,hour);
					date.add(Date.MINUTE,min);
					this.criteriumDateTimePanel.value=date;
				},this);
				this.criteriumDateTimePanel.items.items[0].items.items[2].on('select',function(combobox,record){
					var date = this.criteriumDateTimePanel.items.items[0];
					var hour = this.criteriumDateTimePanel.items.items[0].items.items[1].value;
					var min = this.criteriumDateTimePanel.items.items[0].items.items[2].value;
					date.add(Date.HOUR,hour);
					date.add(Date.MINUTE,min);
					this.criteriumDateTimePanel.value=date;
				},this);
				return this.criteriumDateTimePanel;
				break;
			case 'number':
				return this.criteriumNumberPanel;
				break;
			case 'checkbox':
				return this.criteriumCheckboxPanel;
				break;

			case 'textfield':
				return this.criteriumTextField;
				break;

			default:

				if(type_data.custom){
					
					if(!this.customCriteriumPanels[type_data.type]){
						this.customCriteriumPanels[type_data.type] = this.criteriumPanel.add(GO.customfields.dataTypes[type_data.type].getFormField({name:type_data.type,dataname:''}));
						this.criteriumPanel.doLayout();
					}

					return this.customCriteriumPanels[type_data.type];
				}else
				{
				
				if(!this.dynamicCriteriumPanels[type_data.type]){
					this.dynamicCriteriumPanels[type_data.type] = this.criteriumPanel.add({
						id: type_data.type,
						items:{
							xtype: type_data.type
						}
					});
					this.criteriumPanel.doLayout();
				}

				return this.dynamicCriteriumPanels[type_data.type];
				}

				
				break;
		}
	}
});