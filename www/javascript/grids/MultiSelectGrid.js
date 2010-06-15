/**
 * Store needs an id, name and checked field
 */

GO.grid.MultiSelectGrid = function (config){
	config = config || {};

	var checkColumn = new GO.grid.CheckColumn({
		header: '&nbsp;',
		dataIndex: 'checked',
		width: 30,
		listeners:{
			scope:this,
			change:function(record){
				record.commit();
				this.lastRecordClicked = record;
				this.applyFilter();
			}
		}
	});

	config.tbar = [ new Ext.Button({
		text:GO.tasks.lang.selectAllTasklists,
		handler:function()
		{
			this.selectAllTasklists();
		},
		scope: this
	})];

	if(config.allowNoSelection)
	    this.allowNoSelection = true;

	Ext.apply(config, {
		border: false,
		plugins: [checkColumn],
		tbar : [
		    this.selectButton = new Ext.Button({
			text:GO.lang.selectAll,
			handler:function()
			{							
				if(this.allowNoSelection || !this.selectedAll)
				{
					var select_records = (this.selectedAll && this.allowNoSelection) ? 'clear' : 'all';
				        this.applyFilter(select_records);
				}
			},			
			scope: this
		})],
		layout:'fit',
		cls: 'go-grid3-hide-headers',
		autoScroll:true,
		columns:[checkColumn,{
			header:GO.lang.strName,
			dataIndex: 'name',
			id:'name'
		}],
		listeners:{
			scope:this,
			delayedrowselect:function(grid, rowIndex, r){				
				this.applyFilter([r.id]);
			}
		},
		autoExpandColumn:'name',
		view:new Ext.grid.GridView({
			autoFill: true,
			forceFit: true,
			emptyText: GO.lang['strNoItems']
		}),
		sm: new Ext.grid.RowSelectionModel()
	});

	GO.grid.MultiSelectGrid.superclass.constructor.call(this, config);


	this.store.on('load', function()
	{
	    this.selectButton.setDisabled(!this.store.data.items.length);

	    var num_selected = 0;
	    for(var i=0; i<this.store.data.items.length; i++)
	    {
		    if(this.store.data.items[i].data.checked)
		    {
			    num_selected++;
		    }
	    }

	    this.selectedAll = (num_selected == this.store.data.items.length) ? true : false;

	    if(this.allowNoSelection)
	    {
		    var text = (this.selectedAll) ? GO.lang.deselectAll : GO.lang.selectAll;
		    this.selectButton.setText(text);
	    }
	    
	},this);

	this.addEvents({
		change : true
	});
}

Ext.extend(GO.grid.MultiSelectGrid, GO.grid.GridPanel,{

	allowNoSelection : false,
	lastRecordClicked : false,
	lastSelectedIndex : -1,
	selectedAll : false,
	applyFilter : function(select_records, suppressEvent){

		this.lastSelectedIndex=-1;
		var records = [], ids=[], checked, current_record_id, will_be_checked;

		for (var i = 0, max=this.store.data.items.length; i < max;  i++)
		{
			current_record_id = this.store.data.items[i].id;
			will_be_checked= select_records && select_records!='clear' && (select_records=='all' || select_records.indexOf(current_record_id)>-1);

			if(select_records && !will_be_checked){
				checked=false;
				if(this.store.data.items[i].data.checked){
					if(select_records!='clear'){
						this.store.data.items[i].set('checked',"0");
						this.store.data.items[i].commit();
					}else
					{
						this.store.data.items[i].data.checked="0";
					}
				}
			}else
			{
				if(will_be_checked){
					checked="1";
					if(GO.util.empty(this.store.data.items[i].data.checked)){
						if(select_records!='all'){
							this.store.data.items[i].set('checked',"1");
							this.store.data.items[i].commit();
						}else
						{
							this.store.data.items[i].data.checked="1";
						}
					}

					this.lastSelectedIndex = i;

				}else
				{
					checked = this.store.data.items[i].data.checked;
				}
			}
			if(checked=="1")
			{
				ids.push(this.store.data.items[i].id);
				records.push(this.store.data.items[i]);
			}
		}

		if(!this.allowNoSelection && (ids.length == 0))
		{
			alert(GO.lang.noItemSelectedWarning);

			if(this.lastRecordClicked){
				this.lastRecordClicked.set('checked', "1");
				this.lastRecordClicked.commit();
			}

			this.lastRecordClicked = false;
			this.store.rejectChanges();
		}else
		{
			if(!suppressEvent)
			{
				this.fireEvent('change', this, ids, records);
			}

			this.store.commitChanges();

			if(select_records=='all' || select_records=='clear')
			    this.getView().refresh();

			this.getSelectionModel().clearSelections();
		}
		if(this.lastSelectedIndex>-1)
		{
			this.getView().focusRow(this.lastSelectedIndex);
		}

		this.selectedAll = (records.length == this.store.data.items.length) ? true : false;
		if(this.allowNoSelection)
		{						
			var text = (this.selectedAll) ? GO.lang.deselectAll : GO.lang.selectAll;			
			this.selectButton.setText(text);		
		}
		
	}
});
