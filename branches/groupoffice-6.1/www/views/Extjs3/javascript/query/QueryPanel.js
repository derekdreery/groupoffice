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

Ext.ns('GO.query');

GO.query.QueryPanel = function(config){
	if(!config)
	{
		config = {};
	}
	
	
	
	this.typesStore = new GO.data.JsonStore({
		//url: GO.url("core/modelAttributes"),
		url:config.modelAttributesUrl,
		id:'name',
		baseParams:{
			modelName:config.modelName,
			exclude: config.modelExcludeAttributes
		},
		fields: ['name','label','gotype'],
		remoteSort: true
	});
	
	var idRecord = new Ext.data.Record({'name':'t.id','label':'ID','gotype':'numberfield'},'id');
	this.typesStore.on('load',function(){
		this.typesStore.insert(0,[idRecord]);
	},this);
	
	config.layout='fit';
	config.autoScroll=true;
	config.split=true;
	
//	var checkColumn = new GO.grid.CheckColumn({
//		header: '&nbsp;',
//		dataIndex: 'close_group',
//		width: 20	
//	});

	var fields ={
		fields:['andor','field','comparator', 'value','start_group','gotype','rawValue','rawFieldLabel'],
		columns:[	{
			menuDisabled:true,
			width: 40,
			header: GO.lang.queryAnd+' / '+GO.lang.queryOr,
			dataIndex: 'andor',
			editor:new GO.form.ComboBox({
				store: new Ext.data.ArrayStore({
					idIndex:0,
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
				
				mode: 'local',
				triggerAction: 'all',
				editable: false,
				selectOnFocus:true,
				forceSelection:true
			})
		},{
			id:'field',
			menuDisabled:true,
			width:150,
			header: GO.lang.queryField,
			dataIndex: 'field',
			renderer:function(v, meta, record){
			
				if(!GO.util.empty(record.data.rawFieldLabel)){
					return record.data.rawFieldLabel;
				}
			},
			editor: new GO.form.ComboBox({
					store: this.typesStore,
					valueField:'name',
					displayField:'label',
					mode: 'local',
					triggerAction: 'all',
					editable: true,
					selectOnFocus:true,
					forceSelection:true,
					listeners:{
						scope:this,
						select:function(combo,record){
							var gridRecord = this.store.getAt(this.lastEdit.row);
							
							gridRecord.set('gotype',record.get('gotype'));							
							gridRecord.set('rawFieldLabel',record.get('label'));
							
						}
					}
				})
		},{
			menuDisabled:true,
			width:50,
			header: GO.lang.queryComparator,
			dataIndex: 'comparator',
			editor: new GO.form.ComboBox({
				store: new Ext.data.ArrayStore({
					idIndex:0,
					fields: ['value'],
					data : [
					['LIKE'],
					['NOT LIKE'],
					['='],
					['!='],
					['>'],
					['<']
					]
				}),
				value: 'LIKE',
				valueField:'value',
				displayField:'value',				
				width: 60,
				mode: 'local',
				triggerAction: 'all',
				editable: false,
				selectOnFocus:true,
				forceSelection:true
			})
		},{
			menuDisabled:true,
			width:200,
			header: GO.lang.queryValue,
			dataIndex: 'value',
			renderer:function(v, meta, record){
				if(!GO.util.empty(record.data.rawValue)){
					return record.data.rawValue;
				}else
				{
					return "";
				}
			},
			editor: new Ext.form.TextField({
				
			})
		},
		new GO.grid.CheckColumn({
			menuDisabled:true,
			header: GO.lang.queryStartGroup,
			width:100,
			dataIndex: 'start_group'
		})
		]
	};
	config.store = new GO.data.JsonStore({
		fields: fields.fields,
		remoteSort: true
	});

	var columnModel =  new Ext.grid.ColumnModel({
		defaults:{
			sortable:false
		},
		columns:fields.columns
	});
	
	config.cm=columnModel;
	config.sm=new Ext.grid.RowSelectionModel();
	config.loadMask=true;
	config.autoExpandColumn='field';

	config.clicksToEdit=1;

	this.criteriaRecord = Ext.data.Record.create([
	{
		name: 'andor',
		type: 'string'
	},
	{
		name: 'gotype',
		type: 'string'
	},
	{
		name: 'field',
		type: 'string'
	},{
		name: 'comparator',
		type: 'string'
	},

	{
		name: 'value',
		type:'string'
	},{
		name: 'start_group',
		type:'string'
	}]);

	config.tbar=[this.titleField = new GO.form.PlainField({
		style: 'marginLeft:3px;marginRight:10px;',
		value: '<b>'+GO.lang['strNew']+'</b>'
	}),{
		iconCls: 'btn-add',
		text: GO.lang['cmdAdd'],
		cls: 'x-btn-text-icon',
		handler: function(){
			this.insertRow();
		},
		scope: this
	},
	{
		iconCls: 'btn-delete',
		text: GO.lang['cmdDelete'],
		cls: 'x-btn-text-icon',
		handler: function(){
			var selectedRows = this.selModel.getSelections();
			for(var i=0;i<selectedRows.length;i++)
			{
				selectedRows[i].commit();
				this.store.remove(selectedRows[i]);
			}
		},
		scope: this
	},'-',{
		iconCls: 'btn-delete',
		text: GO.lang['cmdReset'],
		cls: 'x-btn-text-icon',
		handler: function(){
			this.titleField.setValue('<b>'+GO.lang['strNew']+'</b>');
			this.fireEvent('reset',this);
			this.setCriteriaStore();
		},
		scope: this
	}
	];

	config.listeners={
		render:function(){
			this.typesStore.load();
			this.insertRow();
		},
		beforeedit:function(e){
			if(e.column==this.valueCol) {
				this.setEditor(e.record.get('gotype'), e.record.get('field'));
			}
			return true;
		},
		afteredit:function(e){
			if(e.column==this.valueCol) {				
				var rawValue;
				if(typeof(this.lastActiveEditor.field.checked)!='undefined'){
					rawValue=this.lastActiveEditor.field.checked ? 1 : 0;
				}else
				{
					rawValue=this.lastActiveEditor.field.getRawValue();
				}	
				e.record.set('rawValue',rawValue)
			}
		}
	}


	GO.query.QueryPanel.superclass.constructor.call(this, config);
	
	this.addEvents({'reset':true});

};
Ext.extend(GO.query.QueryPanel, GO.grid.EditorGridPanel,{
	
	valueCol : 3,
	
	editors : {},
	
	insertRow : function(){
		var e = new this.criteriaRecord({
			andor:'AND',
			comparator:'LIKE',
			start_group:false
		});
		this.stopEditing();
		var count = this.store.getCount();
		this.store.insert(count, e);
		this.startEditing(count, 1);
	},
	
	renderSelect : function(value, p, record, rowIndex, colIndex, ds) {
		var cm = this.getColumnModel();
		var ce = cm.getCellEditor(colIndex, rowIndex);

		var val = '';
		if (ce.field.store.getById(value) !== undefined) {
			val = ce.field.store.getById(value).get("label");
		}
		return val;
	},
	
	setEditor : function(gotype, colName){
		
		var col = this.getColumnModel().getColumnAt(this.valueCol);
		
		if(this.editors[colName])
			editor = new this.editors[colName];
		else {
			var editor = GO.base.form.getFormFieldByType(gotype, colName);
		}
		col.setEditor(editor);
	},
	
	setCriteriaStore : function(queryRecord) {
		if (!GO.util.empty(queryRecord)) {
			var data = Ext.decode(queryRecord.data.data);
			this.store.loadData({results: data});
		} else {
			this.store.removeAll();
		}
	}
});
