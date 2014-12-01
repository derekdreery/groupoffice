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


GO.addressbook.ManageDialog = function(config)
{
	if(!config)
	{
		config = {};
	}	
	this.addressbooksGrid = new GO.addressbook.ManageAddressbooksGrid();	
	
	var items = [
			this.addressbooksGrid			
		];
		
	this.templatesGrid = new GO.addressbook.TemplatesGrid();
	this.addresslistsGrid = new GO.addressbook.AddresslistsGrid();
	items.push(this.templatesGrid);
	items.push(this.addresslistsGrid);

	if(GO.settings.has_admin_permission)
	{
		this.exportPermissionsTab = new GO.grid.PermissionsPanel({
			title:GO.addressbook.lang.exportPermission,
			hideLevel:true
		});
		items.push(this.exportPermissionsTab);
	}
	
	config.layout= 'fit';
	config.modal= false;
	config.shadow= false;
	config.border= false;
	config.height= 600;
	config.width= 900;
	config.closeAction= 'hide';
	config.title= GO.addressbook.lang['cmdManageDialog'];
	config.items= [{
		xtype: 'tabpanel',
		activeTab: 0,
		border: true,
		deferredRender: false,
		items: items
	}];
	config.buttons=[{ 
		text: GO.lang['cmdClose'], 
		handler: function(){ 
			this.hide(); 
		}, 
		scope: this 
	}];
	
	GO.addressbook.ManageDialog.superclass.constructor.call(this, config);
}
	
Ext.extend(GO.addressbook.ManageDialog, GO.Window,{

	show : function()
	{
		if(!this.rendered)
		{
			this.render(Ext.getBody());
		}

		if(GO.settings.has_admin_permission)
		{
			this.exportPermissionsTab.setAcl(GO.addressbook.export_acl_id);
		}
		
		GO.addressbook.ManageDialog.superclass.show.call(this);
	}
});	
