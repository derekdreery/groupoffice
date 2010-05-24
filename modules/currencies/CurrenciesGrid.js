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
GO.currencies.CurrenciesGrid = function(config){
	if(!config)
	{
		config = {};
	}
	config.title = GO.currencies.lang.currencies;
	config.layout='fit';
	config.autoScroll=true;		
	config.store = GO.currencies.currenciesStore;	

	var columnModel =  new Ext.grid.ColumnModel({
		defaults:{
			sortable:true
		},
		columns:[{
		header: GO.currencies.lang.code,
		dataIndex: 'code',
		editor:new Ext.form.TextField()
	},{
		header: GO.currencies.lang.symbol,
		dataIndex: 'symbol',
		editor:new Ext.form.TextField()
	},{
		header: GO.currencies.lang.value,
		dataIndex: 'value',
		editor:new GO.form.NumberField()
	}]
	});

	
	config.cm=columnModel;
	config.view=new Ext.grid.GridView({
		autoFill: true,
		forceFit: true,
		emptyText: GO.lang['strNoItems']		
	});
	config.sm=new Ext.grid.RowSelectionModel();
	config.loadMask=true;
	
	config.tbar=[{
		iconCls: 'btn-add',
		text: GO.lang['cmdAdd'],
		cls: 'x-btn-text-icon',
		handler: function(){
			var r = new Ext.data.Record({
				code: '',
				value: 1,
				symbol: ''
			});
			this.stopEditing();
			this.store.insert(0, r);
			this.startEditing(0, 0);
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
	},{
		iconCls: 'btn-save',
		text: GO.lang['cmdSave'],
		cls: 'x-btn-text-icon',
		handler: function(){

			this.getEl().mask(GO.lang.waitMsgLoad);

			Ext.Ajax.request({
				url:GO.settings.modules.currencies.url+'action.php',
				params:{
					'task' : 'save_currencies',
					'currencies':Ext.encode(this.getGridData())
				},
				callback:function(options, success, response){
										
					this.store.commitChanges();
					this.getEl().unmask();					
				},
				scope:this
			});
	
		},
		scope: this
	}];
	GO.currencies.CurrenciesGrid.superclass.constructor.call(this, config);
};
Ext.extend(GO.currencies.CurrenciesGrid, Ext.grid.EditorGridPanel,{
	afterRender : function(){
		GO.currencies.CurrenciesGrid.superclass.afterRender.call(this);
		this.store.load();
	},
	getGridData : function(){

		var data = {};

		for (var i = 0; i < this.store.data.items.length;  i++)
		{
			var r = this.store.data.items[i].data;

			data[i]={};

			for(var key in r)
			{
				data[i][key]=r[key];
			}
		}

		return data;
	},
	setIds : function(ids)
	{
		for(var index in ids)
		{
			if(index!="remove")
			{
				this.store.getAt(index).set('id', ids[index]);
			}
		}
	}
});
