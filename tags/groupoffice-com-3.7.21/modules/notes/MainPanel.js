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

 
GO.notes.MainPanel = function(config){
	
	if(!config)
	{
		config = {};
	}

	this.westPanel= new GO.grid.MultiSelectGrid({
		region:'west',
		id:'no-west-panel',
		title:GO.notes.lang.categories,
		loadMask:true,
		store: GO.notes.readableCategoriesStore,
		width: 210,
		split:true,
		allowNoSelection:true,
		bbar: new GO.SmallPagingToolbar({
			items:[this.searchField = new GO.form.SearchField({
				store: GO.notes.readableCategoriesStore,
				width:120,
				emptyText: GO.lang.strSearch
			})],
			store:GO.notes.readableCategoriesStore,
			pageSize:GO.settings.config.nav_page_size
		})
	});

	this.westPanel.on('change', function(grid, categories, records)
	{
		if(records.length){
			this.centerPanel.store.baseParams.categories = Ext.encode(categories);
			this.centerPanel.store.reload();
			this.category_ids = categories;

			if(records.length)
			{
				this.category_id = records[0].data.id;
				this.category_name = records[0].data.name;
			}

			delete this.centerPanel.store.baseParams.categories;
		}
	}, this);
	
	this.westPanel.store.on('load', function()
	{
		for(var i=0, found=false; i<this.westPanel.store.data.length && !found; i++)
		{
			var item = this.westPanel.store.data.items[i];
			if(item.data.checked)
			{
				this.category_id = item.data.id;
				this.category_name = item.data.name;
				
				found = true;
			}
		}
		
		this.centerPanel.store.load();
		
	}, this);

	this.centerPanel = new GO.notes.NotesGrid({
		region:'center',
		id:'no-center-panel',
		border:true
	});
	
	this.centerPanel.on("delayedrowselect",function(grid, rowIndex, r){
		this.eastPanel.load(r.data.id);		
	}, this);

	this.centerPanel.on('rowdblclick', function(grid, rowIndex){
		this.eastPanel.editHandler();
	}, this);

	this.centerPanel.store.on('load', function(){

		this.centerPanel.setTitle(this.centerPanel.store.reader.jsonData.grid_title);

		this.getTopToolbar().items.get('add').setDisabled(!this.centerPanel.store.reader.jsonData.data.write_permission);
		this.getTopToolbar().items.get('delete').setDisabled(!this.centerPanel.store.reader.jsonData.data.write_permission);

		if(this.eastPanel.data.category_id!=this.category_id)
		{
			this.eastPanel.reset();
		}
	}, this);
	
	this.eastPanel = new GO.notes.NotePanel({
		region:'east',
		id:'no-east-panel',
		width:440,
		border:true
	});
	
	config.tbar=new Ext.Toolbar({
		cls:'go-head-tb',
		items: [{
			iconCls: 'btn-add',
			itemId:'add',
			disabled:true,
			text: GO.lang['cmdAdd'],
			cls: 'x-btn-text-icon',
			handler: function(){
				this.eastPanel.reset();

				GO.notes.showNoteDialog(0, {
					category_id: this.category_id,
					category_name: this.category_name
					});

			},
			scope: this
		},{
			disabled:true,
			itemId:'delete',
			iconCls: 'btn-delete',
			text: GO.lang['cmdDelete'],
			cls: 'x-btn-text-icon',
			handler: function(){
				this.centerPanel.deleteSelected({
					callback : this.eastPanel.gridDeleteCallback,
					scope: this.eastPanel
				});
			},
			scope: this
		},{
			iconCls: 'no-btn-categories',
			text: GO.notes.lang.manageCategories,
			cls: 'x-btn-text-icon',
			handler: function(){
				if(!this.categoriesDialog)
				{
					this.categoriesDialog = new GO.notes.ManageCategoriesDialog();
					this.categoriesDialog.on('change', function(){
						this.westPanel.store.reload();
						GO.notes.writableCategoriesStore.reload();
					}, this);
				}
				this.categoriesDialog.show();
			},
			scope: this
				
		}]
		});

	config.items=[
	this.westPanel,
	this.centerPanel,
	this.eastPanel
	];	
	
	config.layout='border';
	GO.notes.MainPanel.superclass.constructor.call(this, config);	
};


Ext.extend(GO.notes.MainPanel, Ext.Panel, {
	afterRender : function()
	{
		GO.dialogListeners.add('note',{
			scope:this,
			save:function(){
				this.centerPanel.store.reload();
			}
		});

		GO.notes.readableCategoriesStore.load();
		
		GO.notes.MainPanel.superclass.afterRender.call(this);
	}
});

GO.notes.showNoteDialog = function(note_id, config){

	if(!GO.notes.noteDialog)
		GO.notes.noteDialog = new GO.notes.NoteDialog();
	
	GO.notes.noteDialog.show(note_id, config);
}


/*
 * This will add the module to the main tabpanel filled with all the modules
 */
 
GO.moduleManager.addModule('notes', GO.notes.MainPanel, {
	title : GO.notes.lang.notes,
	iconCls : 'go-tab-icon-notes'
});
/*
 * If your module has a linkable item, you should add a link handler like this. 
 * The index (no. 1 in this case) should be a unique identifier of your item.
 * See classes/base/links.class.inc for an overview.
 * 
 * Basically this function opens a project window when a user clicks on it from a 
 * panel with links. 
 */

GO.linkHandlers[4]=function(id){
	if(!GO.notes.linkWindow){
		var notePanel = new GO.notes.NotePanel();
		GO.notes.linkWindow= new GO.LinkViewWindow({
			title: GO.notes.lang.note,
			items: notePanel,
			notePanel: notePanel,
			closeAction:"hide"
		});
	}
	GO.notes.linkWindow.notePanel.load(id);
	GO.notes.linkWindow.show();
}

GO.linkPreviewPanels[4]=function(config){
	config = config || {};
	return new GO.notes.NotePanel(config);
}


/* {LINKHANDLERS} */


GO.newMenuItems.push({
	text: GO.notes.lang.note,
	iconCls: 'go-link-icon-4',
	handler:function(item, e){		
		GO.notes.showNoteDialog(0, {
			link_config: item.parentMenu.link_config			
		});
	}
});
/* {NEWMENUITEMS} */


