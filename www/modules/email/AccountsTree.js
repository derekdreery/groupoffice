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
	config.loader=new Ext.tree.TreeLoader(
	{
		dataUrl:GO.settings.modules.email.url+'json.php',
		baseParams:{task: 'tree'},
		preloadChildren:true
	});
	config.containerScroll=true;
	config.rootVisible=false;
	config.collapseFirst=false;
	config.collapsible=true;
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
			return ((this.getNodeById(e.dropNode.id).parentNode.id != e.target.id) &&
					(e.source.dragData.node.attributes.account_id == e.target.attributes.account_id));
		}
		return true;
		
	}, this);
	
}

Ext.extend(GO.email.AccountsTree, Ext.tree.TreePanel, {	
	setUsage : function(usage){		
			this.statusBar.body.update(usage);
	},
	moveFolder : function(account_id, dest, src)
	{
		Ext.Ajax.request({
			url:GO.settings.modules.email.url+'action.php',
			params:{
				task:'move_folder',
				account_id:account_id,
				source_id:src,
				target_id:dest
			},
			callback:function(options, success, response)
			{
				var responseParams = Ext.decode(response.responseText);
				if(!responseParams.success)
				{
					alert(responseParams.feedback);
					Ext.MessageBox.hide();
				}else
				{
				}
			},
			scope:this
		});
	}
});