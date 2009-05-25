GO.addressbook.ContactsGrid = function(config){
	
	if(!config)
	{
		config = {};
	}
	


	
	config.paging=true;
	config.border=false;
	
	
	var fields ={
		fields : ['id', 'name', 'company_name', 'email', 'home_phone', 'work_phone', 'work_fax','cellular','function','department','ctime','mtime'],
		columns : [
		  {
		  	header: GO.lang['strName'], 
		  	dataIndex: 'name'
		  },
		  {
		  	header: GO.lang['strCompany'], 
		  	dataIndex: 'company_name'
		  },
		  {
		  	header: GO.lang['strEmail'], 
		  	dataIndex: 'email' , 
		  	width: 150,
		  	hidden:true
		  },
		  {
		  	header: GO.lang['strPhone'], 
		  	dataIndex: 'home_phone' , 
		  	width: 100,
		  	hidden:true
		  },
		  {
		  	header: GO.lang['strWorkPhone'], 
		  	dataIndex: 'work_phone' , 
		  	width: 100,
		  	hidden:true
		  },
		  {
		  	header: GO.lang['strWorkFax'], 
		  	dataIndex: 'work_fax' , 
		  	width: 100,
		  	hidden:true
		  },
		  {
		  	header: GO.lang['strCellular'], 
		  	dataIndex: 'cellular' , 
		  	width: 100,
		  	hidden:true
		  },
		  {
		  	header: GO.lang['strFunction'], 
		  	dataIndex: 'function' , 
		  	width: 100,
		  	hidden:true
		  },
		  {
		  	header: GO.lang['strDepartment'], 
		  	dataIndex: 'department', 
		  	width: 100,
		  	hidden:true
		  },{
		  	header: GO.lang.strMtime,
		  	dataIndex:'mtime',
		  	hidden:true,
		  	width:80		  	
		  },{
		  	header: GO.lang.strCtime,
		  	dataIndex:'ctime',
		  	hidden:true,
		  	width:80
		  }
		]
	}
	
	if(GO.customfields)
	{
		GO.customfields.addColumns(2, fields);
	}
	config.store = new GO.data.JsonStore({
	    url: GO.settings.modules.addressbook.url+ 'json.php',
	    baseParams: {task: 'contacts', enable_mailings_filter:true},
	    root: 'results',
	    id: 'id',
	    totalProperty:'total',
	    fields: fields.fields,
	    remoteSort: true
	});
	
	var contactsColumnModel =  new Ext.grid.ColumnModel(fields.columns);
	contactsColumnModel.defaultSortable = true;
	config.cm=contactsColumnModel;
	
	config.view=new Ext.grid.GridView({
		autoFill: true,
		forceFit: true,
		emptyText: GO.lang.strNoItems		
	}),
	config.sm=new Ext.grid.RowSelectionModel();
	config.loadMask=true;
	
	config.enableDragDrop=true;
	config.ddGroup='AddressBooksDD';
	
	GO.addressbook.ContactsGrid.superclass.constructor.call(this, config);
	
	this.on('rowdblclick', function(grid, rowIndex){
		var record = grid.getStore().getAt(rowIndex);			
		
		GO.addressbook.contactDialog.show(record.data.id);
		}, this);
	
};


Ext.extend(GO.addressbook.ContactsGrid, GO.grid.GridPanel, {
	
	loaded : false,
	
	afterRender : function()
	{
		GO.addressbook.ContactsGrid.superclass.afterRender.call(this);
		
		if(this.isVisible())
		{
			this.onGridShow();
		}
	},
	onGridShow : function()
	{
		if(!this.loaded && this.rendered)
		{
			this.store.load();
			this.loaded=true;
		}
	}
});
