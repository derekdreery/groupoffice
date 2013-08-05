GO.site.ContentContextMenu = function(config){

	if(!config)
		config = {};

	config.items=[];
	
	this.actionView = new Ext.menu.Item({
		iconCls: 'btn-view',
		text: GO.lang.strView,
		cls: 'x-btn-text-icon',
		handler:function(){
			console.log("View");
//			window.open(GO.url('site/content/redirectToFront', {id: this.selected[0].id}));			
		},
		scope:this
	});

	
	this.actionAdvanced = new Ext.menu.Item({
		iconCls: 'btn-settings',
		text: GO.site.lang.advanced,
		cls: 'x-btn-text-icon',
		scope:this,
		handler: function()
		{
			this.treePanel.contentPanel.showContentDialog(this.selected.attributes.content_id);
		}
	});
	
	this.actionAdd = new Ext.menu.Item({
		iconCls: 'btn-add',
		text: GO.site.lang.addContent,
		cls: 'x-btn-text-icon',
		handler:function(){
			// Load an empty contentPanel and set the parent id
			this.treePanel.contentPanel.create(this.selected.attributes.site_id,this.selected.attributes.content_id);
		},
		scope:this
	});
	
	this.actionDelete = new Ext.menu.Item({
		iconCls: 'btn-delete',
		text: GO.site.lang.deleteContent,
		cls: 'x-btn-text-icon',
		scope:this,
		handler: function()
		{
			this.deleteContent();
		}
	});
	
	config.items.push(this.actionView);
	config.items.push(this.actionAdvanced);
	config.items.push(this.actionAdd);
	config.items.push(this.actionDelete);
		
	GO.site.ContentContextMenu.superclass.constructor.call(this,config);
}

Ext.extend(GO.site.ContentContextMenu, Ext.menu.Menu, {
	model_name : false,
	selected  : false,
	treePanel : false,
	
	setSelected : function (treePanel, node, model_name) {
		this.selected = node;
		this.model_name=model_name;
		this.treePanel = treePanel;
	},

	getSelected : function () {
		if (typeof(this.selected)=='undefined')
			return false;
		else
			return this.selected;
	},
	deleteContent : function() {
		
		if(this.selected.attributes.hasChildren){
			if(!this.errorDialog){
				this.errorDialog = new GO.ErrorDialog();
			}
			this.errorDialog.show(GO.site.lang.deleteContentHasChildren, GO.site.lang.deleteContent);
		} else {
			var contentId = this.selected.attributes.content_id;

			Ext.MessageBox.confirm(GO.site.lang.deleteContent, GO.site.lang.deleteContentConfirm, function(btn){
				if(btn == 'yes'){
					GO.request({
						url: 'site/content/delete',
						params: {
							id: contentId
						},
						success: function(){
							GO.mainLayout.getModulePanel('site').rebuildTree();
						},
						failure: function(){

						},
						scope: this
					});
				}
			});
		}
	}
});