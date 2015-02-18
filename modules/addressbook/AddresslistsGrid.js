/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @version $Id$
 * @author Merijn Schering <mschering@intermesh.nl>
 * @author Wilmar van Beusekom <wilmar@intermesh.nl>
 */

GO.addressbook.AddresslistsGrid = function(config)
{
	if(!config)
	{
		config = {};
	}
	
	config.title= GO.addressbook.lang['cmdPanelMailings'];
	config.layout= 'fit';
	config.border=false;
	
	config.store=new GO.data.JsonStore({
			url: GO.url("addressbook/addresslist/store"),
			baseParams: {
					permissionLevel: GO.permissionLevels.write
			},
			fields: ['id', 'name', 'user_name','acl_id'],
			remoteSort: true
	});
	
	config.tbar=[
	{
		iconCls: 'btn-add',
		text: GO.lang.cmdAdd,
		cls: 'x-btn-text-icon',
		handler: function(){
			this.mailingDialog.show();
		},
		disabled: !GO.settings.modules.addressbook.write_permission,
		scope: this
	},
	{
		iconCls: 'btn-delete',
		text: GO.lang.cmdDelete,
		cls: 'x-btn-text-icon',
		handler: function(){
			this.deleteSelected();
		},
		disabled: !GO.settings.modules.addressbook.write_permission,
		scope: this
	},
	'-'
	,
		this.searchField = new GO.form.SearchField({
			store: config.store,
			width:150,
			emptyText: GO.lang.strSearch
		})
	];
	config.paging=true;
	//	config.id= 'ab-mailings-grid';
	//config.store=GO.addressbook.writableAddresslistsStore;

	config.view=new Ext.grid.GridView({
		autoFill: true,
		forceFit: true,
		emptyText: GO.lang.strNoItems
	});
	
	config.store.setDefaultSort('name', 'ASC');
	
	var columnModel =  new Ext.grid.ColumnModel([
	{
		header: GO.lang['strName'],
		dataIndex: 'name'
	},
	{
		header: GO.addressbook.lang['cmdOwner'],
		dataIndex: 'user_name' ,
		sortable: false
	}
	]);
	
	config.cm= columnModel;
	config.sm= new Ext.grid.RowSelectionModel({
		singleSelect: false
	});
    

	GO.addressbook.AddresslistsGrid.superclass.constructor.call(this, config);
	
	this.on('rowdblclick', function(grid, rowIndex){
		var record = grid.getStore().getAt(rowIndex);
		
		this.mailingDialog.show(record.data.id);

	}, this);
	
}

Ext.extend(GO.addressbook.AddresslistsGrid, GO.grid.GridPanel,{
	afterRender : function()
	{
		GO.addressbook.AddresslistsGrid.superclass.afterRender.call(this);

		if(!this.store.loaded)
		{
			this.store.load();
		}

		this.mailingDialog = new GO.addressbook.MailingDialog();
		this.mailingDialog.on('save', function(){
			this.store.load();
			
			GO.addressbook.writableAddresslistsStore.load();
		}, this);

	}
});