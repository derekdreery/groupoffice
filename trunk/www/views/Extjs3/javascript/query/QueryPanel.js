/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: ProjectFeesPanel.js 11552 2011-10-12 11:08:43Z mschering $
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
			modelName:config.modelName
		},
		fields: ['name','label','gotype'],
		remoteSort: true
	});
	
	config.layout='fit';
	config.autoScroll=true;
	config.split=true;
	
//	var checkColumn = new GO.grid.CheckColumn({
//		header: '&nbsp;',
//		dataIndex: 'close_group',
//		width: 20	
//	});

	var fields ={
		fields:['andor','field','comparator', 'value','start_group','gotype'],
		columns:[	{
			width: 40,
			header: 'AND / OR',
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
			width:150,
			header: 'Field',
			dataIndex: 'field',
			renderer : this.renderSelect.createDelegate(this),
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
							
						}
					}
				})
		},{
			width:50,
			header: 'Comparator',
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
			width:100,
			header: 'Value',
			dataIndex: 'value',
			editor: new Ext.form.TextField({
				
			})
		},
		new GO.grid.CheckColumn({
			header: 'Start group',
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

	config.clicksToEdit=1;

	var Criteria = Ext.data.Record.create([
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

	config.tbar=[{
		iconCls: 'btn-add',
		text: GO.lang['cmdAdd'],
		cls: 'x-btn-text-icon',
		handler: function(){
			var e = new Criteria({
				andor:'AND',
				comparator:'LIKE',
				start_group:false
			});
			this.stopEditing();
			var count = this.store.getCount();
			this.store.insert(count, e);
			this.startEditing(count, 0);
		},
		scope: this
	},{
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
	}];

	config.listeners={
		render:function(){
			this.typesStore.load();
		},
		beforeedit:function(e){			
			if(e.column==this.valueCol)
				this.setEditor(e.record.get('gotype'), e.record.get('field'));
			
			return true;
		}
	}


	GO.query.QueryPanel.superclass.constructor.call(this, config);

};
Ext.extend(GO.query.QueryPanel, GO.grid.EditorGridPanel,{
	
	valueCol : 3,
	
	editors : {},
	
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
		
		var editor = GO.base.form.getFormFieldByType(gotype, colName);
		
		col.setEditor(editor);
	}
});
