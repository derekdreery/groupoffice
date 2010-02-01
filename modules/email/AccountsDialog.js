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
 
GO.email.AccountsDialog = function(config){
	
	
	if(!config)
	{
		config={};
	}
	
	this.accountsGrid = new GO.email.AccountsGrid({
		region:'center'
	});
	this.accountDialog = new GO.email.AccountDialog();

	config.maximizable=true;
	config.layout='fit';
	config.modal=false;
	config.resizable=true;
	config.border=false;
	config.width=600;
	config.height=400;
	config.closeAction='hide';
	config.title=GO.email.lang.accounts;	


	config.tbar = [{
		iconCls: 'btn-add',
		text: GO.lang.cmdAdd,
		cls: 'x-btn-text-icon',
		handler: function(){
			this.accountDialog.show();
		},
		scope: this,
		disabled: !GO.settings.modules.email.write_permission
	},{
		iconCls: 'btn-delete',
		text: GO.lang.cmdDelete,
		cls: 'x-btn-text-icon',
		handler: function(){
			this.accountsGrid.deleteSelected({
				callback: function(){
					if(GO.email.aliasesStore.loaded)
					{
						GO.email.aliasesStore.reload();
					}
					this.accountsGrid.fireEvent('delete', this);
				},
				scope: this
			});
		},
		scope:this,
		disabled: !GO.settings.modules.email.write_permission
	}];

	config.buttons=[{
		text: GO.lang.cmdClose,
		handler: function(){this.hide();},
		scope: this
	}];

	config.items=this.accountsGrid;

	this.accountDialog.on('save', function(){
		this.accountsGrid.store.reload();
		if(GO.email.aliasesStore.loaded)
		{
			GO.email.aliasesStore.reload();
		}
	}, this);

	this.accountsGrid.on('rowdblclick', function(grid, rowIndex){
		var record = grid.getStore().getAt(rowIndex);

		this.accountDialog.show(record.data.id);

	}, this);

	GO.email.AccountsDialog.superclass.constructor.call(this, config);	

}
Ext.extend(GO.email.AccountsDialog, Ext.Window,{
	
	

});