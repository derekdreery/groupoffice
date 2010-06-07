GO.tasks.CategoriesGrid = function(config)
{
        config = config || {};

	var checkColumn = new GO.grid.CheckColumn({
		header: '&nbsp;',
		dataIndex: 'checked',
		width: 30,
		listeners:{
			scope:this,
			change:function()
                        {
				this.applyFilter();
			}
		}
	});

	Ext.apply(config, {                 
		border: true,
		plugins: [checkColumn],
		layout:'fit',
		cls: 'go-grid3-hide-headers',
		autoScroll:true,
                split:true,
		columns:[checkColumn,{
			header:GO.lang.strName,
			dataIndex: 'name',
			id:'name',
			width:188
		}],
		listeners:{
			scope:this,
			delayedrowselect:function(grid, rowIndex, r)
                        {
				this.applyFilter([r.id]);
			}
		},
		autoExpandColumn:'name',
		sm: new Ext.grid.RowSelectionModel()
	});

	GO.tasks.CategoriesGrid.superclass.constructor.call(this, config);
	
	this.addEvents({change : true});
}

Ext.extend(GO.tasks.CategoriesGrid, GO.grid.GridPanel,{
    
    applyFilter : function(select_records, suppressEvent){

		var records = [], ids=[], checked, current_record_id, will_be_checked;

		for (var i = 0; i < this.store.data.items.length;  i++)
		{
			current_record_id = this.store.data.items[i].get('id');
			will_be_checked= select_records && select_records.indexOf(current_record_id)>-1;

			if(select_records && !will_be_checked){
				checked=false;
				this.store.data.items[i].set('checked', "0");
			}else
			{
				if(will_be_checked){
					checked="1";
					this.store.data.items[i].set('checked', "1");
				}else
				{
					checked = this.store.data.items[i].get('checked');
				}
			}
			if(checked=="1")
			{
				ids.push(this.store.data.items[i].get('id'));
				records.push(this.store.data.items[i]);
			}
		}

		if(!suppressEvent)
			this.fireEvent('change', this, ids, records);

		this.store.commitChanges();
		this.getSelectionModel().clearSelections();
	}
});