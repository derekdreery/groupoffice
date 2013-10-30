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
		enableDD:true,
		layout:'fit',
		split:true,
		autoScroll:true,
		width: 200,
		animate:true,
		rootVisible:false,
		containerScroll: true,
		selModel:new Ext.tree.DefaultSelectionModel()
	});
	
	GO.site.SiteTreePanel.superclass.constructor.call(this, config);

	// set the root node
	this.rootNode = new Ext.tree.AsyncTreeNode({
		draggable:false,
		id: 'root',
		iconCls : 'folder-default'
	});

	this.rootNode.on("beforeload", function(){
		//stop state saving when loading entire tree
		this.disableStateSave();
	}, this);

	this.setRootNode(this.rootNode);
	
	this.on('collapsenode', function(node)
	{		
		if(this.saveTreeState && node.childNodes.length)
			this.updateState();		
	},this);

	this.on('expandnode', function(node)
	{		
		if(node.id!="root" && this.saveTreeState && node.childNodes.length)
			this.updateState();
		
		
		//if root node is expanded then we are done loading the entire tree. After that we must start saving states
		if(node.id=="root"){			
			this.enableStateSave();
		}
	},this);

	this.on('contextmenu',this.onContextMenu, this);
	this.on('click',this.onTreeNodeClick, this);
	this.on('nodedrop',this.onNodeDrop, this);
}
	
	
Ext.extend(GO.site.SiteTreePanel, Ext.tree.TreePanel,{

	saveTreeState : false,
	loadingDone : false,

	// When clicked on a treenode
	onTreeNodeClick: function(node){
		
		node.select();
		
		GO.site.currentSiteId = node.attributes.site_id;
		
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
			this.siteContextMenu.setSelected(this,node,'GO_Site_Model_Site');
			this.siteContextMenu.showAt(event.xy);
		}else if(this.isRootContentNode(node)){
			this.contentRootContextMenu.setSelected(this,node,'GO_Site_Model_Content');
			this.contentRootContextMenu.showAt(event.xy);
		}else if(this.isContentNode(node)){
			this.contentPanel.load(node.attributes.content_id); // Load the panel
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
	},
	
	// e.dropNode:	The node that is moved
	// e.target:		The node where it is dropped to.
	onNodeDrop : function(e){
			
		if(e.dropNode){			
			var sortorder = [];
			var parent = false;
			var parentId = 0;
			
			if(e.point === "append"){ // The node is dropped on an item
				parent = e.target;
			}else{ // The node is dropped between two items
				parent = e.target.parentNode;
			}
			
			if(parent.attributes.content_id)
					parentId = parent.attributes.content_id;

			var children = parent.childNodes;
			
			for(var i=0;i<children.length;i++){
				if(children[i].attributes.content_id)
					sortorder.push(children[i].attributes.content_id);
			}
			
			var isDropNodeInArray = sortorder.indexOf(e.dropNode.attributes.content_id);
			if(isDropNodeInArray === -1)
				sortorder.push(e.dropNode.attributes.content_id);

			GO.request({
				url: "site/site/treeSort",
				params: {
					parent_id: parentId,
					sort_order: Ext.encode(sortorder)
				}
			});
		}
	},
	getExpandedNodes : function(){
		var expanded = new Array();
		this.getRootNode().cascade(function(n){
			if(n.expanded){
			expanded.push(n.attributes.id);
			}
		});
		
		return expanded;
	},
					
	enableStateSave : function(){
		if(Ext.Ajax.isLoading(this.getLoader().transId)){
			this.enableStateSave.defer(100, this);
			this.loadingDone=false;
		}else
		{
			if(!this.loadingDone){
				this.loadingDone=true;
				this.enableStateSave.defer(100, this);
			}else{
				this.saveTreeState=true;
			}
		}
	},
	
	disableStateSave : function(){
		this.loadingDone=false;
		this.saveTreeState=false;
	},
	
	updateState : function(){
		GO.request({
			url:"site/site/saveTreeState",
			params:{
				expandedNodes:Ext.encode(this.getExpandedNodes())
			}
		});
	}					
});
	
	
