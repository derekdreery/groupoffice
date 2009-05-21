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


Ext.extend(GO.addressbook.AddresbooksGrid, GO.grid.GridPanel);
