GO.addressbook.CompaniesGrid = function(config){
	
	if(!config)
	{
		config = {};
	}
	config.border=false;
	config.paging=true;
	
	var fields ={
		fields:['id','name','homepage','email','phone','fax','address','address_no','zip','city','state','country','post_address','post_address_no','post_city','post_state','post_country','post_zip','bank_no','vat_no','ctime','mtime'],
		columns:[
		  {
		  	header: GO.lang['strName'], 
		  	dataIndex: 'name'
		  },
		  {
		  	header: GO.lang['strEmail'], 
		  	dataIndex: 'email', 
		  	width: 150,
		  	hidden:true
		  },
		  {
		  	header: GO.lang['strHomepage'], 
		  	dataIndex: 'homepage', 
		  	width: 100,
		  	hidden:true
		  },
		  {
		  	header: GO.lang['strPhone'], 
		  	dataIndex: 'phone', 
		  	width: 100
		  },
		  {
		  	header: GO.lang['strFax'], 
		  	dataIndex: 'fax', 
		  	width: 80,
		  	hidden:true
		  },
		  {
		  	header: GO.lang['strAddress'], 
		  	dataIndex: 'address', 
		  	hidden:true
		  },
		  {
		  	header: GO.lang['strAddressNo'], 
		  	dataIndex: 'address_no', 
		  	hidden:true
		  },
		  {
		  	header: GO.lang['strZip'], 
		  	dataIndex: 'zip', 
		  	hidden:true
		  },
		  {
		  	header: GO.lang['strCity'], 
		  	dataIndex: 'city', 
		  	hidden:true
		  },
		  {
		  	header: GO.lang['strState'], 
		  	dataIndex: 'state', 
		  	width: 80,
		  	hidden:true
		  },
		  {
		  	header: GO.lang['strCountry'], 
		  	dataIndex: 'country', 
		  	hidden:true
		  },
		  {
		  	header: GO.lang['strPostAddress'], 
		  	dataIndex: 'post_address', 
		  	hidden:true
		  },
		  {
		  	header: GO.lang['strPostAddressNo'], 
		  	dataIndex: 'post_address_no', 
		  	hidden:true
		  },
		  {
		  	header: GO.lang['strPostZip'], 
		  	dataIndex: 'post_zip', 
		  	hidden:true
		  },
		  {
		  	header: GO.lang['strPostCity'], 
		  	dataIndex: 'post_city',
		  	hidden:true
		  },
		  {
		  	header: GO.lang['strPostState'], 
		  	dataIndex: 'post_state',
		  	width: 80,
		  	hidden:true
		  },
		  {
		  	header: GO.lang['strPostCountry'], 
		  	dataIndex: 'post_country',
		  	hidden:true
		  },
		  {
		  	header: GO.addressbook.lang['cmdFormLabelBankNo'], 
		  	dataIndex: 'bank_no',
		  	hidden:true
		  },
		  {
		  	header: GO.addressbook.lang['cmdFormLabelVatNo'], 
		  	dataIndex: 'vat_no',
		  	hidden:true
		  },
		  {
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
	};

	
	if(GO.customfields)
	{
		GO.customfields.addColumns(3, fields);
	}
	
	config.store = new GO.data.JsonStore({
	    url: GO.settings.modules.addressbook.url+ 'json.php',
	    baseParams: {task: 'companies', enable_mailings_filter:true},
	    root: 'results',
	    id: 'id',
	    totalProperty:'total',
	    fields: fields.fields,
	    remoteSort: true
	});

	var companiesColumnModel =  new Ext.grid.ColumnModel(fields.columns);
	companiesColumnModel.defaultSortable = true;
	config.cm=companiesColumnModel;
	
	config.view=new Ext.grid.GridView({
		autoFill: true,
		forceFit: true,
		emptyText: GO.lang.strNoItems	
	}),
	config.sm=new Ext.grid.RowSelectionModel();
	config.loadMask=true;
	
	config.enableDragDrop=true;
	config.ddGroup='AddressBooksDD';
	
	GO.addressbook.CompaniesGrid.superclass.constructor.call(this, config);	
};


Ext.extend(GO.addressbook.CompaniesGrid, GO.grid.GridPanel, {
	
	loaded : false,
	
	
	
	afterRender : function()
	{
		GO.addressbook.CompaniesGrid.superclass.afterRender.call(this);
		
		if(this.isVisible())
		{
			this.onGridShow();
		}
	},
	
	onGridShow : function(){
		if(!this.loaded && this.rendered)
		{
			this.store.load();
			this.loaded=true;
		}
	}
});
