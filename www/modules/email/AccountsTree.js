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
 
GO.email.AccountsTree = function(config){
	if(!config)
	{
		config = {};
	}
	config.layout='fit';
  config.split=true;
	config.autoScroll=true;
	config.width=200;
	
	config.animate=true;
	config.loader=new GO.base.tree.TreeLoader(
	{
		dataUrl:GO.settings.modules.email.url+'json.php',
		baseParams:{task: 'tree'},
		preloadChildren:true
	});

	config.loader.on("load", function(treeLoader, node)
	{
		node.attributes.parentExpanded=true;
	}, this);

	config.containerScroll=true;
	config.rootVisible=false;
	config.collapseFirst=false;
	config.collapsible=true;
	config.collapseMode='mini';
	config.header=false;
	config.ddAppendOnly=true;
	config.containerScroll=true;	
	config.enableDD=true;
	config.ddGroup='EmailDD';
	
	config.bbar=new Ext.Toolbar({cls:'go-paging-tb',items:[this.statusBar = new Ext.Panel({height:20, baseCls:'em-statusbar',border:false, plain:true})]});

	GO.email.AccountsTree.superclass.constructor.call(this, config);	
	
	
	// set the root node
	var rootNode = new Ext.tree.AsyncTreeNode({
		text: 'Root',
		id:'bs-folder-0',
		draggable:false,
		iconCls : 'folder-default',
		expanded:false
	});
	this.setRootNode(rootNode);

	this.on('nodedragover', function(e)
	{		
		if(e.dropNode)
		{
			//drag within tree
			if(e.source.dragData.node.id.indexOf('account')>-1 && e.target.id.indexOf('account')>-1){
				if(e.point!='append')
					return true;
				else
					e.target.collapse();
			}
			if(e.point!='append'){
				return false;
			}
			return ((this.getNodeById(e.dropNode.id).parentNode.id != e.target.id) &&
					(e.source.dragData.node.attributes.account_id == e.target.attributes.account_id));
		}else
		{
			//drag from grid
			if(e.point!='append'){
				return false;
			}else
			{
				return true;
			}
		}		
	}, this);	
}

Ext.extend(GO.email.AccountsTree, Ext.tree.TreePanel, {	
	setUsage : function(usage){		
			this.statusBar.body.update(usage);
	},
	moveFolder : function(account_id, target_id, node)
	{
		Ext.Ajax.request({
			url:GO.settings.modules.email.url+'action.php',
			params:{
				task:'move_folder',
				account_id:account_id,
				source_id:node.id,
				target_id:target_id
			},
			callback:function(options, success, response)
			{
				var responseParams = Ext.decode(response.responseText);
				if(responseParams.success)
				{
					//remove preloaded children otherwise it won't request the server
					delete node.parentNode.attributes.children;
					node.parentNode.reload();
				}else
				{
					var accountNode = this.getNodeById('account_'+account_id)
					if(accountNode)
						accountNode.reload();
					
					Ext.MessageBox.alert(GO.lang.strError,responseParams.feedback);
				}								
			},
			scope:this
		});
	}
});