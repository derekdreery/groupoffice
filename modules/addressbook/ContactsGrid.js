GO.addressbook.ContactsGrid = function(config){
	
	if(!config)
	{
		config = {};
	}
		
	config.paging=true;
	config.border=false;
	
	var fields ={
		fields : ['id','name','company_name','first_name','middle_name','last_name','title','initials','sex','birthday','age','email','email2','email3','home_phone','work_phone','work_fax','cellular','fax','address','address_no','zip','city','state','country','function','department','salutation','ab_name','ctime','mtime'],
		columns : [
		  {
			header: GO.addressbook.lang.id,
			dataIndex: 'id',
			width:20,
			hidden:true
	          },{
		  	header: GO.lang['strName'], 
		  	dataIndex: 'name'
		  },
		  {
		  	header: GO.lang['strCompany'], 
		  	dataIndex: 'company_name'
		  },
		  {
		  	header: GO.lang['strFirstName'], 
		  	dataIndex: 'first_name',
		  	hidden:true
		  },
		  {
		  	header: GO.lang['strMiddleName'], 
		  	dataIndex: 'middle_name',
		  	hidden:true
		  },
		  {
		  	header: GO.lang['strLastName'], 
		  	dataIndex: 'last_name',
		  	hidden:true
		  },
		  {
		  	header: GO.lang['strTitle'], 
		  	dataIndex: 'title',
		  	hidden:true
		  },
		  {
		  	header: GO.lang['strInitials'], 
		  	dataIndex: 'initials',
		  	hidden:true
		  },
		  {
		  	header: GO.lang['strSex'], 
		  	dataIndex: 'sex',
		  	hidden:true
		  },
		  {
		  	header: GO.lang['strBirthday'], 
		  	dataIndex: 'birthday',
		  	hidden:true
		  },{
		  	header: GO.lang.age,
		  	dataIndex: 'age',
		  	hidden:true
		  },
		  {
		  	header: GO.lang['strEmail'], 
		  	dataIndex: 'email', 
		  	width: 150,
		  	hidden:true
		  },
		  {
		  	header: GO.lang['strEmail'] + ' 2', 
		  	dataIndex: 'email2', 
		  	width: 150,
		  	hidden:true
		  },
		  {
		  	header: GO.lang['strEmail'] + ' 3', 
		  	dataIndex: 'email3', 
		  	width: 150,
		  	hidden:true
		  },
		  {
		  	header: GO.lang['strPhone'], 
		  	dataIndex: 'home_phone', 
		  	width: 100,
		  	hidden:true
		  },
		  {
		  	header: GO.lang['strWorkPhone'], 
		  	dataIndex: 'work_phone', 
		  	width: 100,
		  	hidden:true
		  },
		  {
		  	header: GO.lang['strWorkFax'], 
		  	dataIndex: 'work_fax', 
		  	width: 100,
		  	hidden:true
		  },
		  {
		  	header: GO.lang['strCellular'], 
		  	dataIndex: 'cellular', 
		  	width: 100,
		  	hidden:true
		  },
		  {
		  	header: GO.lang['strFax'], 
		  	dataIndex: 'fax', 
		  	width: 100,
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
		  	hidden:true
		  },
		  {
		  	header: GO.lang['strCountry'], 
		  	dataIndex: 'country', 
		  	hidden:true
		  },		  
		  {
		  	header: GO.lang['strFunction'], 
		  	dataIndex: 'function', 
		  	hidden:true
		  },
		  {
		  	header: GO.lang['strDepartment'], 
		  	dataIndex: 'department', 
		  	hidden:true
		  },
		  {
		  	header: GO.lang['strSalutation'], 
		  	dataIndex: 'salutation', 
		  	hidden:true
		  },{
		  	header: GO.addressbook.lang.addressbook,
		  	dataIndex: 'ab_name',
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
	
	config.cm=new Ext.grid.ColumnModel({
		defaults:{
			sortable:true
		},
		columns:fields.columns
	});
	
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
