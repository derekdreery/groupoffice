GO.addressbook.AddresbooksGrid = function(config){
	
	if(!config)
	{
		config = {};
	}
	
	config.title = GO.addressbook.lang.cmdPanelAddressbook;
	config.layout='fit';
	config.autoScroll=true;
	config.split=true;
	config.store = GO.addressbook.readableAddressbooksStore;
	config.ddGroup='AddressBooksDD';
	config.enableDD=true;
	
	GO.addressbook.readableAddressbooksStore.on('load', function(){
		this.selModel.selectFirstRow();
	}, this);
	
	config.paging=false;

	var companiesColumnModel =  new Ext.grid.ColumnModel([
	  {
	  	header: GO.lang['strName'], 
	  	dataIndex: 'name'
	  }
	]);
	companiesColumnModel.defaultSortable = true;
	config.cm=companiesColumnModel;
	
	config.view=new Ext.grid.GridView({
		autoFill: true,
		forceFit: true,
		emptyText: GO.addressbook.lang.noAddressbooks		
	}),
	config.sm=new Ext.grid.RowSelectionModel();
	config.loadMask=true;

	
	GO.addressbook.AddresbooksGrid.superclass.constructor.call(this, config);
};


Ext.extend(GO.addressbook.AddresbooksGrid, GO.grid.GridPanel, {
	
	afterRender : function()
	{	
		GO.addressbook.AddresbooksGrid.superclass.afterRender.call(this);		

		var DDtarget = new Ext.dd.DropTarget(this.getView().mainBody, {
			ddGroup : 'AddressBooksDD',
			notifyDrop : this.onNotifyDrop.createDelegate(this)
		});	
	},
	onNotifyDrop : function(source, e, data)
	{	
		var selections = source.dragData.selections;
        var dropRowIndex = this.getView().findRowIndex(e.target);
        var book_id = this.getView().grid.store.data.items[dropRowIndex].id;

		var company_id = 0;
		var move_items = [];
		var task = 'save_companies';
		for(var i=0; i<selections.length; i++)
		{
			move_items.push(selections[i].id);
			if(selections[i].json.company_id > 0)
			{
				company_id = selections[i].json.company_id;
			}
			if(selections[i].json.id > 0)
			{
				contact_id = selections[i].json.id;
				task = 'save_contacts';
			}			
		}
			
		if(book_id > 0 && (company_id == 0 || confirm(GO.addressbook.lang.moveAll)))
		{
			Ext.Ajax.request({
				url: GO.settings.modules.addressbook.url+'action.php',
				params: {
					task:task,
					book_id:book_id,
					items:Ext.encode(move_items)
				}
			});
			return true;
		}else
		{
			return false;
		}	
	}
	
});
