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

	this.westPanel = new GO.notes.CategoriesGrid({
    region:'west',
    id:'no-west-panel',
    title:GO.lang.menu,
		autoScroll:true,				
		width: 150,
		split:true
	});
	
	this.westPanel.on('rowclick', function(grid, rowIndex)
	{
		var record = grid.getStore().getAt(rowIndex);	
		this.centerPanel.store.baseParams.category_id = record.data.id;
		this.category_id=record.data.id;
		this.category_name=record.data.name;
		
		this.centerPanel.store.load();		
	}, this);
	
	this.westPanel.store.on('load', function(){
		var sm = this.westPanel.selModel;		
		
		var defaultRecord = this.westPanel.store.getById(GO.notes.defaultCategory.id);
		if(!defaultRecord)
		{
			defaultRecord = this.westPanel.store.getAt(0);
			GO.notes.defaultCategory = defaultRecord.data;
		}
		sm.selectRecords([defaultRecord]);

		if(defaultRecord)
		{
			this.centerPanel.store.baseParams.category_id = defaultRecord.data.id;
			this.category_id=defaultRecord.data.id;
			this.category_name=defaultRecord.data.name;		
			
			this.centerPanel.store.load();
		}		
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
	
	var northPanel = new Ext.Panel({
		region: 'north',
		
		baseCls:'x-plain',
		split: true,
		resizable:false,
		tbar: new Ext.Toolbar({		
			cls:'go-head-tb',
			items: [{
			iconCls: 'btn-add',							
			text: GO.lang['cmdAdd'],
			cls: 'x-btn-text-icon',
			handler: function(){
	    	GO.notes.showNoteDialog(0, {category_id: this.centerPanel.store.baseParams.category_id, category_name: this.category_name});
			},
			scope: this
		},{
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
						this.categoriesDialog.on('change', function(){this.westPanel.store.reload();GO.notes.writableCategoriesStore.reload();}, this);
					}
					this.categoriesDialog.show();
				},
				scope: this
				
			}]})
	});

	config.items=[
		northPanel,
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
		GO.notes.noteDialogListeners={
			scope:this,
			save:function(){
				this.centerPanel.store.reload();
			}
		}		
		GO.notes.MainPanel.superclass.afterRender.call(this);
	}
});


/*GO.notes.writableCategoriesStore = new GO.data.JsonStore({
	    url: GO.settings.modules.notes.url+ 'json.php',
	    baseParams: {
	    	auth_type:'write',
	    	task: 'categories'
	    	},
	    root: 'results',
	    id: 'id',
	    totalProperty:'total',
	    fields: ['id', 'name', 'user_name'],
	    remoteSort: true
	});*/



GO.notes.showNoteDialog = function(note_id, config){

	if(!GO.notes.noteDialog)
		GO.notes.noteDialog = new GO.notes.NoteDialog();

	if(GO.notes.noteDialogListeners){
		GO.notes.noteDialog.on(GO.notes.noteDialogListeners);
		delete GO.notes.noteDialogListeners;
	}

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
	var notePanel = new GO.notes.NotePanel();
	var linkWindow = new GO.LinkViewWindow({
		title: GO.notes.lang.note,
		items: notePanel
	});
	notePanel.load(id);
	linkWindow.show();
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


