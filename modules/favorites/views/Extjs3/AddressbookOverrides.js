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
 * @author Wesley Smits <wsmits@intermesh.nl>
 */

GO.moduleManager.onModuleReady('addressbook',function(){
	
	Ext.override(GO.addressbook.MainPanel, {	
		
		initComponent : GO.addressbook.MainPanel.prototype.initComponent.createSequence(function(){
			this.addressbookFavoritesList = new GO.favorites.AddressbookFavoritesList({
				id:'addressbookFavoritesList'
			});
			
			this.addressbookFavoritesList.on('change', function(grid, abooks, records){
				var books = Ext.encode(abooks);
				var panel = this.tabPanel.getActiveTab();

				this.companiesGrid.store.baseParams.books = books;
				this.contactsGrid.store.baseParams.books = books;

				if(panel.id=='ab-contacts-grid')
				{
					this.contactsGrid.store.load();
					delete this.contactsGrid.store.baseParams.books;
				}else
				{

					this.companiesGrid.store.load();
					delete this.companiesGrid.store.baseParams.books;
				}

				if(records.length)
				{
					GO.addressbook.defaultAddressbook = records[0];
				}
				
				// Clear the checkbox selection of the addressbooksGrid
				this.addressbooksGrid.applyFilter([],true);
			}, this);

			this.addressbooksGrid.on('change', function(grid, abooks, records){
				// Clear the checkbox selection of the addressbookFavoritesList
				this.addressbookFavoritesList.applyFilter([],true);
			}, this);

			this.westPanel.insert(0,this.addressbookFavoritesList);
			
			GO.favorites.favoritesAddressbookStore.load();
		})
	});
});