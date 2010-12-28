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
 */

GO.mailings.MailingsGrid = function(config)
{
	if(!config)
	{
		config = {};
	}
	
	config.title= GO.mailings.lang['cmdPanelMailings'];
	config.layout= 'fit';
	config.border=false;
	config.tbar=[
	{
		iconCls: 'btn-add',
		text: GO.lang.cmdAdd,
		cls: 'x-btn-text-icon',
		handler: function(){
			this.mailingDialog.show();
		},
		disabled: !GO.settings.modules.mailings.write_permission,
		scope: this
	},
	{
		iconCls: 'btn-delete',
		text: GO.lang.cmdDelete,
		cls: 'x-btn-text-icon',
		handler: function(){
			this.deleteSelected();
		},
		disabled: !GO.settings.modules.mailings.write_permission,
		scope: this
	}
	];
	config.paging=false;
	//	config.id= 'ab-mailings-grid';
	config.store=GO.mailings.writableMailingsStore;

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
		header: GO.mailings.lang['cmdOwner'],
		dataIndex: 'owner' ,
		sortable: false
	}
	]);
	
	config.cm= columnModel;
	config.sm= new Ext.grid.RowSelectionModel({
		singleSelect: false
	});
    

	GO.mailings.MailingsGrid.superclass.constructor.call(this, config);
	
	this.on('rowdblclick', function(grid, rowIndex){
		var record = grid.getStore().getAt(rowIndex);
		
		this.mailingDialog.show(record.data.id);

	}, this);
	
}

Ext.extend(GO.mailings.MailingsGrid, GO.grid.GridPanel,{
	afterRender : function()
	{
		GO.mailings.MailingsGrid.superclass.afterRender.call(this);

		if(!this.store.loaded)
		{
			this.store.load();
		}

		this.mailingDialog = new GO.mailings.MailingDialog();
		this.mailingDialog.on('save', function(){
			this.store.reload();
		}, this);

	}
});