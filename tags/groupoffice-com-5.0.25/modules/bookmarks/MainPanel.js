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
 * @author Twan Verhofstad
 */

Ext.namespace('GO.bookmarks');

GO.bookmarks.getThumbUrl= function(logo, pub){
	
	if(GO.util.empty(pub)){
		return GO.url('core/thumb', {src:logo, h:16,w:16,pub:0});
	}else
	{
		return GO.settings.modules.bookmarks.url+logo;
	}

}

GO.bookmarks.MainPanel = function(config){

	if(!config)
	{
		config = {};
	}

	this.selectCategory = new GO.form.ComboBoxReset({
		fieldLabel: 'Category',
		hiddenName:'category_id',
		store: GO.bookmarks.comboCategoriesStore,
		displayField:'name',
		valueField:'id',
		triggerAction: 'all',
		editable: true,
		width:200,
		emptyText:GO.bookmarks.lang.showAll,
		selectOnFocus :false,
		listeners:{
			clear:function(){
				GO.bookmarks.groupingStore.baseParams['category']=0;
				GO.bookmarks.groupingStore.load();
			},
			select: function(combo,record) {
				GO.bookmarks.groupingStore.baseParams['category']=record.data.id;
				GO.bookmarks.groupingStore.load();
			//this.setValue(record.data[this.displayField]);
			}
		}
	});

	this.searchField = new GO.form.SearchField({
		store: GO.bookmarks.groupingStore ,
		width:220
	});



	// Dataview & Grid

	/*this.bmGrid=new GO.bookmarks.BookmarksGrid({
		store:GO.bookmarks.groupingStore
	});*/

	this.bmView=new GO.bookmarks.BookmarksView({
		store:GO.bookmarks.groupingStore,
		tbar: [GO.bookmarks.lang.category+':',this.selectCategory,'-',GO.lang.strSearch+':',this.searchField]
	});



	config.tbar=new Ext.Toolbar({
		cls:'go-head-tb',
		items: [{
                xtype:'htmlcomponent',
                html:GO.bookmarks.lang.name,
                cls:'go-module-title-tbar'
        },

		{  //  bookmark toevoegen
			iconCls: 'btn-add',
			text: GO.lang['cmdAdd'],
			cls: 'x-btn-text-icon',
			handler: function(){
				GO.bookmarks.showBookmarksDialog({
					edit:0
				});
			},
			scope:this
		},/*,
			{ //  bookmark verwijderen (alleen in grid)
				iconCls: 'btn-delete',
				text: GO.lang['cmdDelete'],
				cls: 'x-btn-text-icon',
				handler: function(){
					if (this.centerPanel.items.items[0].selModel.selections.length) // geselecteerde rij ;)
					{  // items.items :(
						GO.bookmarks.removeBookmark(this.centerPanel.items.items[0].selModel.selections.items[0]);
					}
				},
				scope:this
			},*/

		// categorieen beheren
		{
			iconCls: 'no-btn-categories',
			text: GO.bookmarks.lang.administrateCategories,
			cls: 'x-btn-text-icon',
			hidden: !GO.settings.modules.bookmarks.write_permission,
			handler: function(){
				if(!this.categoriesDialog)
				{
					this.categoriesDialog = new GO.bookmarks.ManageCategoriesDialog({
						listeners:{
							close:function(){
							},
							scope:this
						}
					});
					this.categoriesDialog.on('change', function(){
						}, this);
				}
				this.categoriesDialog.show();
			},
			scope: this
		}/*,
			{ // schakelen tussen dataview en grid
				text: GO.bookmarks.lang.thumbnails,
				iconCls: 'btn-thumbnails',
				enableToggle: true,
				cls: 'x-btn-text-icon',
				handler: function(){
					this.toggleLayout();
				},
				scope:this
			}*/
		]
	});
 
	config.layout='fit';
	config.items=this.bmView;

	GO.bookmarks.MainPanel.superclass.constructor.call(this, config);
}

//-----------------------------------------------------------------------------


Ext.extend(GO.bookmarks.MainPanel, Ext.Panel, {

	// wisselen van dataview naar grid of andersom

	toggleLayout : function()
	{
		this.centerPanel.tab = !this.centerPanel.tab
		var t = (this.centerPanel.tab)?1:0;
		this.centerPanel.getLayout().setActiveItem(t); // switch naar andere card
		
		if (t==0) {
			this.northPanel.topToolbar.items.items[1].enable(); // verwijder knop aan
		}
		if (t==1) {
			this.northPanel.topToolbar.items.items[1].disable(); //verwijder knop uit
		}
		
	}
});

//----------------------------------------------------------------------------

//
// GLOBAL
//

// Bookmarks toevoegen of editten
GO.bookmarks.showBookmarksDialog = function(config){
	
	if(!this.bookmarksDialog){

		this.bookmarksDialog = new GO.bookmarks.BookmarksDialog({
			edit:config.edit, // leeg of bestaand record?
			listeners:{
				save:function(){
					GO.bookmarks.groupingStore.load();
				},
				scope:this
			}
		});
	}
	this.bookmarksDialog.show(config);
}

// Bookmark hyperlink openen, in GO tab of in browser tab
GO.bookmarks.openBookmark = function(record)
{
	if(record.data.behave_as_module == '1')
	{
		var panel = GO.mainLayout.openModule('bookmarks-id-'+record.id);
		if(panel)
		{
			return true;
		}
	}

	if(record.data.open_extern==0){
		var websiteTab = new GO.panel.IFrameComponent( {
			title : record.data.name,
			url:    record.data.content,
			border:false,
			closable:true
		})

		GO.mainLayout.tabPanel.add(websiteTab) // open nieuwe tab in group-office
		websiteTab.show();
	}
	else{
		window.open(record.data.content) // open in nieuw browser tab
	}
	
}

// bookmark verwijderen
GO.bookmarks.removeBookmark = function(record)
{
	if(confirm(GO.bookmarks.lang.confirmDelete))
	{

		GO.request({
			url : 'bookmarks/bookmark/delete', 
			params: {
				id: record.data.id
			},
			scope:this,

			callback: function(options, success, response){
				var responseParams = Ext.decode(response.responseText);
				if(!responseParams.success)
				{
					Ext.MessageBox.alert(GO.lang['strError'],responseParams.feedback);
				}
				else
				{
					GO.bookmarks.groupingStore.remove(record);
					GO.bookmarks.groupingStore.load();
				}
			}
		})
	}
}

// bookmark module toevoegen aan modulemanager
GO.moduleManager.addModule('bookmarks', GO.bookmarks.MainPanel, {
	title : GO.bookmarks.lang.bookmarks,
	iconCls : 'go-tab-icon-bookmarks'
});
