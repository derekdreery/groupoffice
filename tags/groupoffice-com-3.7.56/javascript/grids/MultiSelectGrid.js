/**
 * Store needs an id, name and checked field
 */

GO.grid.MultiSelectGrid = function (config){
	config = config || {};

	var checkColumn = new GO.grid.CheckColumn({
		header: '&nbsp;',
		dataIndex: 'checked',
		width: 20,
		listeners:{
			scope:this,
			change:function(record){

				record.commit();
				this.lastRecordClicked = record;

				if(this.timeoutNumber)
					clearTimeout(this.timeoutNumber);			
				
				this.timeoutNumber=this.applyFilter.defer(750, this,[]);
			}
		}
	});

	if(config.allowNoSelection)
		this.allowNoSelection = true;
	
	if(!config.tools)
		config.tools=[];
	
	config.tools.push(
		{
			text:GO.lang.selectAll,
			id:'plus',
			qtip:GO.lang.selectAll,
			handler:function(){this.selectAll();},
			scope: this
		});

	Ext.apply(config, {
		plugins: [checkColumn],
		layout:'fit',
		cls: 'go-grid3-hide-headers',
		autoScroll:true,
		columns:[checkColumn,{
			header:GO.lang.strName,
			dataIndex: 'name',
			id:'name'
		}],		
		autoExpandColumn:'name',
		view:new Ext.grid.GridView({
			emptyText: GO.lang['strNoItems']
		})
	});



	GO.grid.MultiSelectGrid.superclass.constructor.call(this, config);

	this.on('rowclick',function(grid, rowIndex){
				this.applyFilter([grid.store.getAt(rowIndex).id]);
			}, this);


	this.store.on('load', function()
	{
		var num_selected = 0;
		for(var i=0; i<this.store.data.items.length; i++)
		{
			if(this.store.data.items[i].data.checked)
			{
				num_selected++;
			}
		}

		this.selectedAll = (num_selected == this.store.data.items.length) ? true : false;

//		if(this.allowNoSelection)
//		{
//			var text = (this.selectedAll) ? GO.lang.deselectAll : GO.lang.selectAll;
//			this.selectButton.setText(text);
//		}
	    
	},this);

	this.addEvents({
		change : true
	});
}

Ext.extend(GO.grid.MultiSelectGrid, GO.grid.GridPanel,{
	timeoutNumber : false,
	
	allowNoSelection : false,

	lastRecordClicked : false,

	lastSelectedIndex : -1,

	selectedAll : false,

	selectAll : function()
	{	
		if(this.allowNoSelection || !this.selectedAll)
		{
			var select_records = (this.selectedAll && this.allowNoSelection) ? 'clear' : 'all';
			this.applyFilter(select_records);
		}
	},

	getSelected : function(){
		var types = [];
		for (var i = 0; i < this.store.data.items.length;  i++)
		{
			if( this.store.data.items[i].get('checked'))
			{
				types.push(this.store.data.items[i].get('id'));
			}
		}
		return types;
	},

	applyFilter : function(select_records, suppressEvent){

		this.timeoutNumber=false;

		this.lastSelectedIndex=-1;
		var records = [], ids=[], checked, current_record_id, will_be_checked;

		var changedRecords=[];

		var max = this.store.data.items.length;
		
		if(select_records=='all' && max>50){
			
			if(!confirm(GO.lang.confirmSelectLotsOfItems.replace('{count}', max).replace('Group-Office', GO.settings.config.product_name))){
				return;
			}
		}


		for (var i=0; i < max;  i++)
		{
			current_record_id = this.store.data.items[i].id;
			will_be_checked= select_records && select_records!='clear' && (select_records=='all' || select_records.indexOf(current_record_id)>-1);

			if(select_records && !will_be_checked){
				checked="0";
				if(this.store.data.items[i].data.checked=="1"){
					this.store.data.items[i].data.checked="0";
					changedRecords.push(this.store.data.items[i]);
				}
			}else
			{
				if(will_be_checked){
					checked="1";
					if(GO.util.empty(this.store.data.items[i].data.checked)){					
						this.store.data.items[i].data.checked="1";
						changedRecords.push(this.store.data.items[i]);
					}
				}else
				{
					checked = this.store.data.items[i].data.checked;
				}
			}
			if(checked=="1")
			{
				this.lastSelectedIndex = i;
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

			if(changedRecords.length>10){
				this.getView().refresh();
			}else
			{
				for (var i = 0, max=changedRecords.length; i < max;  i++)
					this.getView().refreshRow(changedRecords[i]);
			}

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
			//this.selectButton.setText(text);
		}
		
	}
});
