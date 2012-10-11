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
 *
 * Params:
 * 
 * linksStore: store to reload after items are linked
 * gridRecords: records from grid to link. They must have a link_id and link_type
 * fromLinks: array with link_id and link_type to link
 */
 
 /**
 * @class GO.dialog.SelectContact
 * @extends Ext.Window
 * A window to select a number of User-Office user Users.
 * 
 * @cfg {Function} handler A function called when the Add or Ok button is clicked. The grid will be passed as argument.
 * @cfg {Object} scope The scope of the handler
 * 
 * @constructor
 * @param {Object} config The config object
 */
 
GO.addressbook.SelectContactDialog = function(config){
	
	Ext.apply(this, config);
	
	this.searchField = new GO.form.SearchField({
		width:320
  });

	this.addressbooksGrid = new GO.addressbook.AddresbooksGrid({
		region:'west',
		width:180,
		store:new GO.data.JsonStore({
			url: GO.url('addressbook/addressbook/store'),
			baseParams: {
				'auth_type' : 'read'
				},
			root: 'results',
			totalProperty: 'total',
			id: 'id',
			fields: ['id','name','owner','checked'],
			remoteSort: true
		})
	});

	/*this.addressbooksGrid.getSelectionModel().on('rowselect', function(sm, rowIndex, r){
		var record = this.addressbooksGrid.getStore().getAt(rowIndex);
		this.grid.store.baseParams.addressbook_id=record.get("id");
		this.grid.store.load();
	}, this);*/

	this.addressbooksGrid.on('change', function(grid, abooks, records)
	{
		var books = Ext.encode(abooks);
		this.grid.store.baseParams.books=books;
		this.grid.store.load();
		//delete this.grid.store.baseParams.books;

	}, this);


	this.grid = this.contactsGrid = new GO.addressbook.ContactsGrid({
		region:'center',
		tbar: [
    GO.lang['strSearch']+': ', ' ', this.searchField,{
				handler: function()
				{
					if(!this.advancedSearchWindow)
					{
						this.advancedSearchWindow = GO.addressbook.advancedSearchWindow = new GO.addressbook.AdvancedSearchWindow();
						this.advancedSearchWindow.on('ok', function(win){

//						this.grid.store.baseParams.advancedQuery=this.searchField.getValue();
						this.searchField.setValue("[ "+GO.addressbook.lang.advancedSearch+" ]");
						this.searchField.setDisabled(true);
						this.grid.store.load();

						}, this)
					}
					this.advancedSearchWindow.show({dataType:'contacts',masterPanel : this});
				},
				text: GO.addressbook.lang.advancedSearch,
				scope: this,
				style:'margin-left:5px;'
			},{
				handler: function()
				{
					this.searchField.setValue("");
//					delete this.grid.store.baseParams.advancedQuery;
					this.searchField.setDisabled(false);
					this.grid.store.load();
				},
				text: GO.lang.cmdReset,
				scope: this
			}
    ]});
    
  //dont filter on address lists when selecting
  delete this.grid.store.baseParams.enable_mailings_filter;

  //don't save filter but send it each time
  this.grid.store.baseParams.disable_filter_save="1";
		
	this.searchField.store=this.grid.store;
	
	var focusSearchField = function(){
		this.searchField.focus(true);
	};
	
	GO.addressbook.SelectContactDialog.superclass.constructor.call(this, {
    layout: 'border',
		modal:false,
		focus: focusSearchField.createDelegate(this),
		height:400,
		width:750,
		closeAction:'hide',
		title: GO.addressbook.lang['strSelectContact'],
		items: [this.addressbooksGrid, this.grid],
		buttons: [
			{
				text: GO.lang['cmdOk'],
				handler: function (){
					this.callHandler(true);
				},
				scope:this
			},
			{
				text: GO.lang['cmdAdd'],
				handler: function (){
					this.callHandler(false);
				},
				scope:this
			},{
				text: GO.addressbook.lang.addAllSearchResults,
				handler: function (){
					if(confirm(GO.addressbook.lang.confirmAddAllSearchResults)){
						this.callHandler(true, true);
					}
				},
				scope:this
			},
			{
				text: GO.lang['cmdClose'],
				handler: function(){this.hide();},
				scope: this
			}
		]
    });
};

Ext.extend(GO.addressbook.SelectContactDialog, Ext.Window, {

	show : function(){		
		GO.addressbook.SelectContactDialog.superclass.show.call(this);
		
		//if(!this.grid.store.loaded)
		//{
		if(!this.addressbooksGrid.store.loaded)
			this.addressbooksGrid.store.load({
				callback:function(){
					var books = this.addressbooksGrid.getSelected();
					this.grid.store.baseParams.books=Ext.encode(books);
					this.grid.store.load();
				},
				scope:this
			});
		else
			this.grid.store.load();
		
		//}
	},
	
	
	//private
	callHandler : function(hide, allResults){
		if(this.handler)
		{
			if(!this.scope)
			{
				this.scope=this;
			}
			
			var handler = this.handler.createDelegate(this.scope, [this.grid, allResults]);
			handler.call();
		}
		if(hide)
		{
			this.hide();
		}
	}	
	
});