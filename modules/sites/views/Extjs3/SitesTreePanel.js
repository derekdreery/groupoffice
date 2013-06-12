GO.sites.SitesTreePanel = function (config){
	config = config || {};
	
	config.loader =  new GO.base.tree.TreeLoader(
	{
		dataUrl:GO.url('sites/siteBackend/siteTree'),
		preloadChildren:true
	});

	config.loader.on('beforeload', function(){
		var el =this.getEl();
		if(el){
			el.mask(GO.lang.waitMsgLoad);
		}
	}, this);

	config.loader.on('load', function(){
		var el =this.getEl();
		if(el){
			el.unmask();
		}
	}, this);
	
	this.sitesContextMenu = new GO.sites.SitesContextMenu();
	this.pagesContextMenu = new GO.sites.PagesContextMenu();

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
	

		
	GO.sites.SitesTreePanel.superclass.constructor.call(this, config);
	
	// set the root node
	this.rootNode = new Ext.tree.AsyncTreeNode({
		text: '',
		draggable:false,
		id: 'root',
		iconCls : 'folder-default'
	});

	this.setRootNode(this.rootNode);
	
	
	this.on('contextmenu',this.onContextMenu, this);
}
	
	
Ext.extend(GO.sites.SitesTreePanel, Ext.tree.TreePanel,{
	isSiteNode: function(node){
		var id = node.id;
		if(id.length < 6){ // If id is smaller than 5 chars then is it a page node.
			return false;
		}
		else if(id.substring(0,5) == 'site_'){
			return true;
		}
		else{
			return false;
		}
	},
	getRootNode: function(){
		return this.rootNode;
	},
	onContextMenu: function(node,event){
		node.select();
				
		if(this.isSiteNode(node)){				
			this.sitesContextMenu.setSelected(this,'GO_Sites_Model_Site');
			this.sitesContextMenu.showAt(event.xy);
		}
	//				else {
	//					this.pagesContextMenu.setSelected(this,'GO_Sites_Model_Page');
	//					this.pagesContextMenu.showAt(event.xy);
	//				}
	}
});
	
	
