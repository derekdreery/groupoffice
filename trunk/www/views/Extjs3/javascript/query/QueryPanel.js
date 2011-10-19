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
		url: GO.url(config.fieldsControllerRoute),
		fields: ['name','value','type','fields', 'custom','id'],
		remoteSort: true
	});
	
	config.layout='fit';
	config.autoScroll=true;
	config.split=true;
	
	var checkColumn = new GO.grid.CheckColumn({
		header: '&nbsp;',
		dataIndex: 'close_group',
		width: 20	
	});

	var fields ={
		fields:['andor','field','operator', 'value','close_group'],
		columns:[	{
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
				width: 60,
				mode: 'local',
				triggerAction: 'all',
				editable: false,
				selectOnFocus:true,
				forceSelection:true
			})
		},{
			header: 'Field',
			dataIndex: 'field',
			editor: new GO.form.ComboBox({
					store: this.typesStore,
					valueField:'value',
					displayField:'name',
					mode: 'remote',
					triggerAction: 'all',
					editable: true,
					selectOnFocus:true,
					forceSelection:true					
				}),
			align:'right'
		},{
			header: 'Operator',
			dataIndex: 'operator',
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
			}),
			align:'right'
		},{
			header: 'Value',
			dataIndex: 'value',
			editor: new Ext.form.TextField({
				
			}),
			align:'right'
		},
		checkColumn]
	};
	config.store = new GO.data.JsonStore({
		fields: fields.fields,
		remoteSort: true
	});

	var columnModel =  new Ext.grid.ColumnModel({
		defaults:{
			sortable:true
		},
		columns:fields.columns
	});
	
	config.cm=columnModel;
	config.view=new Ext.grid.GridView({
		autoFill: true,
		forceFit: true,
		emptyText: GO.lang['strNoItems']
	});
	config.sm=new Ext.grid.RowSelectionModel();
	config.loadMask=true;

	config.clicksToEdit=1;

	var Criteria = Ext.data.Record.create([
	{
		name: 'andor',
		type: 'string'
	},

	{
		name: 'field',
		type: 'string'
	},{
		name: 'operator',
		type: 'string'
	},

	{
		name: 'value',
		type:'string'
	},{
		name: 'close_group',
		type:'string'
	}]);

	config.tbar=[{
		iconCls: 'btn-add',
		text: GO.lang['cmdAdd'],
		cls: 'x-btn-text-icon',
		handler: function(){
			var e = new Criteria({
				andor:'AND',
				operator:'LIKE',
				close_group:0
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


	GO.query.QueryPanel.superclass.constructor.call(this, config);

};
Ext.extend(GO.query.QueryPanel, Ext.grid.EditorGridPanel,{

});
