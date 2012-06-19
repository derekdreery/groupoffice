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
		baseParams:{
			expandedNodes:""
		},
		dataUrl:GO.url("email/account/tree"),
		preloadChildren:true
	});
	
	config.loader.on('beforeload', function(loader, node){
		loader.baseParams.expandedNodes = Ext.encode(this.getExpandedNodes());
	}, this);
	
	

//	config.loader.on("load", function(treeLoader, node)
//	{
//		node.attributes.parentExpanded=true;
//	}, this);

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
//	var rootNode = new Ext.tree.AsyncTreeNode({
//		text: 'Root',
//		id:'bs-folder-0',
//		draggable:false,
//		iconCls : 'folder-default',
//		expanded:false
//	});
//	this.setRootNode(rootNode);
	
	// set the root node
	var root = new Ext.tree.AsyncTreeNode({
		text: GO.email.lang.accounts,
		draggable:false,
		id:'root'
	});
	
	root.on("beforeload", function(){
		//stop state saving when loading entire tree
		this.saveTreeState=false;
	}, this);
	
	this.setRootNode(root);
	
	
	this.on('collapsenode', function(node)
	{
		if(this.saveTreeState && node.childNodes.length)
			this.updateState();		
	},this);

	this.on('expandnode', function(node)
	{
		//if root node is expanded then we are done loading the entire tree. After that we must start saving states
		if(node.id=="root")
			this.saveTreeState=true;
		
		if(node.id!="root" && this.saveTreeState && node.childNodes.length)
			this.updateState();
	},this);

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
	
	
	
	
	this.treeContextMenu = new GO.email.TreeContextMenu({		
		treePanel:this,
		messagesGrid:this.messagesGrid
	});
	
	
	this.on('contextmenu', function(node, e){
		e.stopEvent();

		var selModel = this.getSelectionModel();
		
		if(!selModel.isSelected(node))
		{
			selModel.clearSelections();
			selModel.select(node);
		}
		
		var coords = e.getXY();

		this.treeContextMenu.setNode(node);

		this.treeContextMenu.showAt([coords[0], coords[1]]);
	}, this);
	
	
	
	
	
	
	
	this.on('startdrag', function(tree, node, e){
		if(node.id.indexOf('account')>-1){
			tree.dropZone.appendOnly=false;
		}else
		{
			tree.dropZone.appendOnly=true;
		}
	}, this);

	this.on('beforenodedrop', function(e){
		if(!e.dropNode)
		{
			var s = e.data.selections, messages = [];

			for(var i = 0, len = s.length; i < len; i++){
				messages.push(s[i].id);
			}

			if(messages.length>0)
			{
				
				var firstDraggedMessage = s[0].data;

				if(firstDraggedMessage["account_id"] != e.target.attributes['account_id'])
				{
					var params = {
						task:'move',
						from_account_id:this.account_id,
						to_account_id:e.target.attributes['account_id'],
						from_mailbox:this.mailbox,
						to_mailbox:e.target.attributes['mailbox'],
						messages:Ext.encode(messages)
					}
					Ext.MessageBox.progress(GO.email.lang.moving, '', '');
					Ext.MessageBox.updateProgress(0, '0%', '');

				

					var moveRequest = function(newMessages){

						if(!newMessages)
						{
							params.total=messages.length;
						}else
						{
							params.messages=Ext.encode(newMessages);
						}

						GO.request({
							timeout:300000,
							url:"email/message/move",
							params:params,
							callback:function(options, success, response){
								var responseParams = Ext.decode(response.responseText);
								if(!responseParams.success)
								{
									alert(responseParams.feedback);
									Ext.MessageBox.hide();
								}else if(responseParams.messages && responseParams.messages.length>0)
								{
									Ext.MessageBox.updateProgress(responseParams.progress, (responseParams.progress*100)+'%', '');
									moveRequest.call(this, responseParams.messages);
								}else
								{
									this.messagesGrid.store.reload({
										callback:function(){

											if(this.messagePanel.uid && !this.messagesGrid.store.getById(this.messagePanel.uid))
											{
												this.messagePanel.reset();
											}

											Ext.MessageBox.hide();
										},
										scope:this
									});
								}

							},
							scope:this
						});
					}
					moveRequest.call(this);

				}else	if(firstDraggedMessage.mailbox == e.target.mailbox)
				{
					return false;
				}else
				{
					this.mainPanel.messagesGrid.store.baseParams['action']='move';
//					this.messagesGrid.store.baseParams['from_account_id']=this.account_id;
//					this.messagesGrid.store.baseParams['to_account_id']=e.target.attributes['account_id'];
//					this.messagesGrid.store.baseParams['from_mailbox']=this.mailbox;
					this.mainPanel.messagesGrid.store.baseParams['to_mailbox']=e.target.attributes['mailbox'];
					this.mainPanel.messagesGrid.store.baseParams['messages']=Ext.encode(messages);

					this.mainPanel.messagesGrid.store.reload({
						callback:function(){
							if(this.mainPanel.messagePanel.uid && !this.mainPanel.messagesGrid.store.getById(this.mainPanel.messagePanel.uid))
							{
								this.mainPanel.messagePanel.reset();
							}
						},
						scope:this
					});

					delete this.mainPanel.messagesGrid.store.baseParams['action'];
//					delete this.messagesGrid.store.baseParams['from_mailbox'];
					delete this.mainPanel.messagesGrid.store.baseParams['to_mailbox'];
					delete this.mainPanel.messagesGrid.store.baseParams['messages'];
//					delete this.messagesGrid.store.baseParams['to_account_id'];
//					delete this.messagesGrid.store.baseParams['from_account_id'];
				}

			}
		}
	},
	this);

	this.on('nodedrop', function(e){
		if(e.dropNode)
		{
			if(e.source.dragData.node.id.indexOf('account')>-1 && e.target.id.indexOf('account')>-1 && e.point!='append'){
				var sortorder=[];
				var c = this.getRootNode().childNodes;

				for(var i=0;i<c.length;i++){
					sortorder.push(c[i].attributes.account_id);
				}
				GO.request({
					url: "email/account/saveSort",
					params: {
						sort_order: Ext.encode(sortorder)
					}
				});
			}else
			{
				this.moveFolder(e.target.attributes['account_id'], e.target , e.data.node);
			}
		}

		this.dropZone.appendOnly=true;
	},
	this);
	
	

	
	
	
//	this.treeEditor = new Ext.tree.TreeEditor(
//		this,
//		new Ext.form.TextField({
//			cancelOnEsc:true,
//			completeOnEnter:true,
//			maskRe:/[^:]/
//		}),
//		{
//			listeners:{
//				complete  : this.afterEdit,
//				startedit : function( editor, boundEl, value )
//				{
//					console.log(editor.editNode.attributes.mailbox);
//					editor.setValue(editor.editNode.attributes.mailbox);
//				},
//				beforecomplete  : function( editor, value, startValue){
//					value=value.trim();
//					if(GO.util.empty(value)){
//						editor.focus();
//						return false;
//					}
//				},
//				scope:this
//			}
//		});
}

Ext.extend(GO.email.AccountsTree, Ext.tree.TreePanel, {	
	
	saveTreeState : false,
	
	updateState : function(){
		GO.request({
			url:"email/account/saveTreeState",
			params:{
				expandedNodes:Ext.encode(this.getExpandedNodes())
			}
		});
	},
	setUsage : function(usage){		
			this.statusBar.body.update(usage);
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
	
	moveFolder : function(account_id, targetNode, node)
	{
	
		GO.request({
			url:"email/folder/move",
			params:{				
				account_id:account_id,
				sourceMailbox:node.attributes.mailbox,
				targetMailbox:targetNode.attributes.mailbox
			},
			fail : function(){
				this.refresh();
			},
			success:function(options, response, result)
			{
				
				//this.refresh(node.parentNode);
				
//				var responseParams = Ext.decode(response.responseText);
//				if(responseParams.success)
//				{
//					//remove preloaded children otherwise it won't request the server
//					delete node.parentNode.attributes.children;
//					node.parentNode.reload();
//				}else
//				{
//					var accountNode = this.getNodeById('account_'+account_id)
//					if(accountNode)
//						accountNode.reload();
//					
//					Ext.MessageBox.alert(GO.lang.strError,responseParams.feedback);
//				}								
			},
			scope:this
		});
	},
	
	refresh : function(node){
		//todo only reload current node.
		if(node){
			//remove preloaded children otherwise it won't request the server
			delete node.attributes.children;
			node.reload();
		}else
		{
			this.getRootNode().reload();
		}
	}
	
	
	
//	afterEdit : function(editor, text, oldText ){
//
//		GO.request({
//			url:'email/folder/submit',
//			params:{				
//				parent:editor.editNode.parentNode.id=='root' ? "" : editor.editNode.parentNode.attributes.mailbox,
//				account_id:editor.editNode.parentNode.attributes.account_id,
//				oldmailbox:oldText,
//				newmailbox:text
//			},
//			success: function(response, options, result)
//			{
//				if(!result.success)
//				{					
//					editor.editNode.destroy();
//					alert(result.feedback);
//				}				
//			},
//			scope:this
//		});
//	}
});