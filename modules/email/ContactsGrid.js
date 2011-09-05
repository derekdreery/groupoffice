GO.email.ContactsGrid = function(config){

	if(!config)
	{
		config = {};
	}

	config.layout='fit';
	config.autoScroll=true;
	config.split=true;	
	config.paging=true;
	config.border=false;

	config.store = new GO.data.JsonStore({
		url : GO.settings.modules.addressbook.url + 'json.php',
		baseParams : {
			task : 'search_email_contacts'
		},
		root : 'results',
		id : 'email',
		totalProperty : 'total',
		fields : ['id', 'name',  'email', 'ab_name', 'company_name'],
		remoteSort : true
	});

	var columnModel =  new Ext.grid.ColumnModel({
		defaults:{
			sortable:true,
			css : 'white-space:normal;'
		},
		columns : [{
			header : GO.lang['strName'],
			dataIndex : 'name'			
		}, {
			header : GO.lang['strEmail'],
			dataIndex : 'email'			
		},{
			header : GO.lang.strCompany,
			dataIndex : 'company_name',
			css : 'white-space:normal;',
			sortable : true
		},{
			header : GO.addressbook.lang.addressbook,
			dataIndex : 'ab_name',
			css : 'white-space:normal;',
			sortable : false
		}]
	});
	config.cm=columnModel;

	config.view=new Ext.grid.GridView({
		autoFill: true,
		forceFit: true,
		emptyText: GO.lang['strNoItems']		
	});
	config.sm=new Ext.grid.RowSelectionModel({
		singleSelect:config.singleSelect
	});
	
	
	this.contactsSearchField = new GO.form.SearchField({
		store : config.store,
		width : 320
	});

	config.tbar=[GO.lang['strSearch'] + ': ', ' ', this.contactsSearchField];

	GO.email.ContactsGrid.superclass.constructor.call(this, config);

};

Ext.extend(GO.email.ContactsGrid, GO.grid.GridPanel,{

	});