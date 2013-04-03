GO.site.SiteTreePanel = function (config){
	config = config || {};
	
	config.loader =  new GO.base.tree.TreeLoader(
	{
		dataUrl:GO.url('site/site/tree'),
		preloadChildren:true
	});

	config.loader.on('beforeload', function(){
		var el =this.getEl();
		if(el)
			el.mask(GO.lang.waitMsgLoad);
	}, this);

	config.loader.on('load', function(){
		var el =this.getEl();
		if(el)
			el.unmask();
	}, this);
	
	this.siteContextMenu = new GO.site.SiteContextMenu({treePanel:this});
	this.contentContextMenu = new GO.site.ContentContextMenu({treePanel:this});
	this.contentRootContextMenu = new GO.site.ContentRootContextMenu({treePanel:this});
	
	Ext.applyIf(config, {
		layout:'fit',
		split:true,
		autoScroll:true,
		width: 200,
		animate:true,
		rootVisible:false,
		containerScroll: true,
		selModel:new Ext.tree.MultiSelectionModel()		
	});
	
	GO.site.SiteTreePanel.superclass.constructor.call(this, config);

	// set the root node
	this.rootNode = new Ext.tree.AsyncTreeNode({
		draggable:false,
		id: 'root',
		iconCls : 'folder-default'
	});

	this.setRootNode(this.rootNode);

	this.on('contextmenu',this.onContextMenu, this);
	this.on('click',this.onTreeNodeClick, this);
}
	
	
Ext.extend(GO.site.SiteTreePanel, Ext.tree.TreePanel,{

	// When clicked on a treenode
	onTreeNodeClick: function(node){
		
		node.select();
		
		if(this.isSiteNode(node)){
			// DO NOTHING
		}else if(this.isRootContentNode(node)){
			// DO NOTHING
		}else if(this.isContentNode(node)){
			this.contentPanel.load(node.attributes.content_id);
		}
	},
	
	// When right clicked on a treenode
	onContextMenu: function(node,event){
		node.select();
		
		if(this.isSiteNode(node)){
//			this.siteContextMenu.setSelected(this,'GO_Site_Model_Site');
//			this.siteContextMenu.showAt(event.xy);
		}else if(this.isRootContentNode(node)){
			this.contentRootContextMenu.setSelected(this,node,'GO_Site_Model_Content');
			this.contentRootContextMenu.showAt(event.xy);
		}else if(this.isContentNode(node)){
			this.contentContextMenu.setSelected(this,node,'GO_Site_Model_Content');
			this.contentContextMenu.showAt(event.xy);
		}
	},
	
	isSiteNode: function(node){
		var id = node.id;
		var parts = id.split("_"); // site_{id}
		var type = parts[0];
		
		if(type == 'site')
			return true;
		else
			return false;
	},
	
	isRootContentNode: function(node){
		var id = node.id;
		var parts = id.split("_");// {siteID}_content_{id}
		var type = parts[1];
		var content_id = parts[2];
		
		if(type == 'content' && GO.util.empty(content_id))
			return true;
		else
			return false;
	},
	
	isContentNode: function(node){
		var id = node.id;
		var parts = id.split("_");// {siteID}_content_{id}
		var type = parts[1];
		var content_id = parts[2];
		
		if(type == 'content' && !GO.util.empty(content_id))
			return true;
		else
			return false;
	},

	getRootNode: function(){
		return this.rootNode;
	}
});
	
	
