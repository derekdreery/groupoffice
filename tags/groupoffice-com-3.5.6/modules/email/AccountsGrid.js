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
 
GO.email.AccountsGrid = function(config){
	
	if(!config)
	{
		config = {};
	}
	
	config.layout='fit';
	config.border=false;
	config.autoScroll=true;
	config.store = new GO.data.JsonStore({
		url: GO.settings.modules.email.url+'json.php',
		baseParams: {		
			"task": 'accounts'
		},
		root: 'results',
		totalProperty: 'total',
		id: 'id',
		fields:['id','email','host', 'user_name', 'username','smtp_host'],
		remoteSort: true,
		sortInfo:{field: 'email', direction: "ASC"}
	});	
	config.paging=true;
	
	var columnModel = new Ext.grid.ColumnModel({
		defaults:{
			sortable:true
		},
		columns:[
		{
			header:GO.lang.strEmail,
			dataIndex: 'email'
		},{
			header:GO.lang.strUsername,
			dataIndex: 'username'
		},{
			header:GO.lang.strOwner,
			dataIndex: 'user_name',
			sortable: false
		},{
			header:GO.email.lang.host,
			dataIndex: 'host'
		},{
			header:'SMTP',
			dataIndex: 'smtp_host'
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
	
	GO.email.AccountsGrid.superclass.constructor.call(this, config);	

	this.addEvents({'delete':true});

};

Ext.extend(GO.email.AccountsGrid, GO.grid.GridPanel,{

});