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
	config.enableDragDrop= true;
	config.ddGroup = 'EmailAccountsDD';
	config.autoScroll=true;
	config.store = new GO.data.JsonStore({
		url: GO.settings.modules.email.url+'json.php',
		baseParams: {		
			"task": 'accounts'
		},
		root: 'results',
		totalProperty: 'total',
		id: 'id',
		fields:['id','email','host', 'user_name'],
		remoteSort: true
	});	
	config.store.setDefaultSort('utime', 'asc');
	config.paging=true;
	
	var columnModel = new Ext.grid.ColumnModel([
		{
			header:GO.lang.strEmail,
			dataIndex: 'email'
		},{
			header:GO.lang.strOwner,
			dataIndex: 'user_name',
			sortable: false
		},{
			header:GO.email.lang.host,
			dataIndex: 'host'
		}]
	);
	columnModel.defaultSortable = false;
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
	afterRender : function(){
		
		GO.email.AccountsDialog.superclass.afterRender.call(this);
		
		var DDtarget = new Ext.dd.DropTarget(this.getView().mainBody, {
			ddGroup : 'EmailAccountsDD',
			copy:false,
			notifyDrop : this.onNotifyDrop.createDelegate(this)
		});
	},
	onNotifyDrop : function(dd, e, data)
	{
	
		var rows=this.selModel.getSelections();
		var dragData = dd.getDragData(e);
		var cindex=dragData.rowIndex;
		if(cindex=='undefined')
		{
			cindex=this.store.data.length-1;
		}					
		
		for(i = 0; i < rows.length; i++) 
		{								
			var rowData=this.store.getById(rows[i].id);
		
			if(!this.copy){
				this.store.remove(this.store.getById(rows[i].id));
			}
			
			this.store.insert(cindex,rowData);
		}
		
		//save sort order							
		var accounts = {};

  	for (var i = 0; i < this.store.data.items.length;  i++)
  	{			    	
			accounts[this.store.data.items[i].get('id')] = i;
  	}
		
		Ext.Ajax.request({
			url: GO.settings.modules.email.url+'action.php',
			params: {
				task: 'save_accounts_sort_order',
				sort_order: Ext.encode(accounts)
			}
		});								
		
	}
});