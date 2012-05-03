/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */


Ext.namespace("GO.email");

GO.email.EmailClient = function(config){

	if(!config)
	{
		config = {};
	}
	
	this.messagesStore = new GO.data.JsonStore({
		url: GO.settings.modules.email.url+'json.php',
		baseParams: {
			//"node": '',
			"task": 'messages'
		},
		root: 'results',
		totalProperty: 'total',
		id: 'uid',
		fields:['uid','icon','flagged','attachments','new','subject','from','sender','size','date', 'priority','answered','forwarded'],
		remoteSort: true
	});
	
	this.messagesStore.setDefaultSort('date', 'DESC');

	var messagesAtTop = Ext.state.Manager.get('em-msgs-top');
	if(messagesAtTop)
	{
		messagesAtTop = Ext.decode(messagesAtTop);
	}else
	{
		messagesAtTop =screen.width<1024;
	}

	var deleteConfig = {
		callback:function(){
			if(this.messagePanel.uid && !this.messagesGrid.store.getById(this.messagePanel.uid))
			{
				this.messagePanel.reset();
			}
		},
		scope: this
	};

	this.leftMessagesGrid = new GO.email.MessagesGrid({
		id:'em-pnl-west',
		store:this.messagesStore,
		width: 420,
		region:'west',
		hidden:messagesAtTop,
		deleteConfig : deleteConfig,
		floatable:false,
		header:false,
		collapsible:true,
		collapseMode:'mini',
		split:true
	});
	this.addGridHandlers(this.leftMessagesGrid);
	
	this.topMessagesGrid = new GO.email.MessagesGrid({
		id:'em-pnl-north',
		store:this.messagesStore,
		height: 250,
		region:'north',
		hidden:!messagesAtTop,
		deleteConfig : deleteConfig,
		floatable:false,
		collapsible:true,
		collapseMode:'mini',
		split:true
	});
	this.addGridHandlers(this.topMessagesGrid);
	
	if(!this.topMessagesGrid.hidden)
	{
		this.messagesGrid=this.topMessagesGrid;
	}else
	{
		this.messagesGrid=this.leftMessagesGrid;
	}
  
	//for global access by composers
	GO.email.messagesGrid=this.messagesGrid;


	this.messagesGrid.store.on("beforeload", function()
	{

		if(this.messagesGrid.store.baseParams['search'] != undefined)
		{
			GO.email.search_query = this.messagesGrid.store.baseParams['search'];
			this.searchDialog.hasSearch = false;
			delete(this.messagesGrid.store.baseParams['search']);
		}else
		if(this.searchDialog.hasSearch)
		{
			this.messagesGrid.resetSearch();
		}

		if(GO.email.search_query)
		{
			this.searchDialog.hasSearch = false;
			var search_type = (GO.email.search_type)
			? GO.email.search_type : GO.email.search_type_default;
		
			var query;

			if(search_type=='any'){
				query='OR OR OR FROM "' + GO.email.search_query + '" SUBJECT "' + GO.email.search_query + '" TO "' + GO.email.search_query + '" CC "' + GO.email.search_query + '"';
			}else
			{
				query=search_type.toUpperCase() + ' "' + GO.email.search_query + '"';
			}

			this.messagesGrid.store.baseParams['query'] = query;
		}else
		if(!this.searchDialog.hasSearch && this.messagesGrid.store.baseParams['query'])
		{
			this.messagesGrid.resetSearch();
			delete(this.messagesGrid.store.baseParams['query']);
		}
                
	}, this);
              
	this.messagesGrid.store.on('load',function(){
		
		var cm = this.topMessagesGrid.getColumnModel();
		var header = this.messagesGrid.store.reader.jsonData.sent || this.messagesGrid.store.reader.jsonData.drafts ? GO.email.lang.to : GO.email.lang.from;
		cm.setColumnHeader(cm.getIndexById('from'), header);
		
		var unseen = this.messagesGrid.store.reader.jsonData.unseen;
		for(var folder_id in unseen)
			this.updateFolderStatus(folder_id,unseen[folder_id]);
		
		if(this.messagesGrid.store.baseParams['query'] && this.messagesGrid.store.baseParams['query']!='' && this.searchDialog.hasSearch){
			this.resetSearchButton.setVisible(true);
		}else
		{
			this.resetSearchButton.setVisible(false);
		}
		
		var selModel = this.treePanel.getSelectionModel();
		if(!selModel.getSelectedNode())
		{
			var node = this.treePanel.getNodeById('folder_'+this.messagesGrid.store.reader.jsonData.folder_id);
			if(node)
			{
				selModel.select(node);
			}
		}

		/*
		 *This method is annoying when searching for unread mails
		if(this.messagePanel.uid && !this.messagesGrid.store.getById(this.messagePanel.uid))
		{
			this.messagePanel.reset();
		}*/

		//don't confirm delete to trashfolder
		this.messagesGrid.deleteConfig.noConfirmation=!this.messagesGrid.store.reader.jsonData.trash && !GO.util.empty(this.messagesGrid.store.reader.jsonData.trash_folder);
	}, this);

	GO.email.saveAsItems = GO.email.saveAsItems || [];
	
	for(var i=0;i<GO.email.saveAsItems.length;i++)
	{
		GO.email.saveAsItems[i].scope=this;
	}
	
	var addSendersItems = [{
		text:GO.email.lang.to,
		field:'to',
		handler:this.addSendersTo,
		scope:this
	},{
		text:'CC',
		field:'cc',
		handler:this.addSendersTo,
		scope:this
	},{
		text:'BCC',
		field:'bcc',
		handler:this.addSendersTo,
		scope:this
	}];
	
	if (GO.addressbook) {
		addSendersItems.push({
			text: GO.addressbook.lang.addresslist,
			cls: 'x-btn-text-icon',
			menu: this.addresslistsMenu = new GO.menu.JsonMenu({
				store: new GO.data.JsonStore({
					url: GO.url("addressbook/addresslist/store"),
					baseParams: {
						permissionLevel: GO.permissionLevels.write,
						forContextMenu: true
					},
					fields: ['id', 'text'],
					remoteSort: true
				}),
				listeners:{
					scope:this,
					itemclick : function(item, e ) {
						this.addSendersToAddresslist(item.id);
						return false;							
					}
				}
			}),
			multiple:true,
			scope: this
		});
	}
	
	var contextItems = [
	{
		text: GO.email.lang.markAsRead,
		handler: function(){
			this.doTaskOnMessages('mark_as_read');
		},
		scope:this,
		multiple:true
	},
	{
		text: GO.email.lang.markAsUnread,
		handler: function(){
			this.doTaskOnMessages('mark_as_unread');
		},
		scope: this,
		multiple:true
	},
	{
		text: GO.email.lang.flag,
		handler: function(){
			this.doTaskOnMessages('flag');
		},
		scope: this,
		multiple:true
	},
	{
		text: GO.email.lang.unflag,
		handler: function(){
			this.doTaskOnMessages('unflag');
		},
		scope: this,
		multiple:true
	},
	'-',
	{
		text: GO.email.lang.viewSource,
		handler: function(){
				
			var record = this.messagesGrid.selModel.getSelected();
			if(record)
			{
				var win = window.open(GO.settings.modules.email.url+'source.php?account_id='+this.account_id+'&mailbox='+encodeURIComponent(this.mailbox)+'&uid='+encodeURIComponent(record.data.uid));
				win.focus();
			}
				
		},
		scope: this
	},'-',
	{
		iconCls: 'btn-delete',
		text: GO.lang.cmdDelete,
		cls: 'x-btn-text-icon',
		handler: function(){
			this.messagesGrid.deleteSelected();
		},
		scope: this,
		multiple:true
	},'-',{
		iconCls: 'btn-add',
		text: GO.email.lang.addSendersTo,
		cls: 'x-btn-text-icon',
		menu: {
			items: addSendersItems
		},
		multiple:true
	}];
		
	
	if(GO.email.saveAsItems && GO.email.saveAsItems.length)
	{
		this.saveAsMenu = new Ext.menu.Menu({
			items:GO.email.saveAsItems
		});
		
		this.saveAsMenu.on('show', function(menu){
			var sm = this.messagesGrid.getSelectionModel();
			var multiple = sm.getSelections().length>1;
			var none = sm.getSelections().length==0;
	
			for(var i=0;i<menu.items.getCount();i++)
			{
				var item = menu.items.get(i);
				item.setDisabled(none || (!item.multiple && multiple));
			}
		}, this);
		
		contextItems.push({
			iconCls: 'btn-save',
			text:GO.lang.cmdSaveAs,
			menu:this.saveAsMenu,
			multiple:true
		});
	}

	this.gridContextMenu = new GO.menu.RecordsContextMenu({
		shadow: "frame",
		minWidth: 180,
		items: contextItems
	});

	GO.email.treePanel = this.treePanel = new GO.email.AccountsTree({
		id:'email-tree-panel',
		region:'west'
	});

	// set the root node
	var root = new Ext.tree.AsyncTreeNode({
		text: GO.email.lang.accounts,
		draggable:false
	});
	this.treePanel.setRootNode(root);

	var items = [
	this.addFolderButton = new Ext.menu.Item({
		iconCls: 'btn-add',
		text: GO.email.lang.addFolder,
		handler: function(){
			Ext.MessageBox.prompt(GO.lang.strName, GO.email.lang.enterFolderName, function(button, text){
				if(button=='ok')
				{
					var sm = this.treePanel.getSelectionModel();
					var node = sm.getSelectedNode();

					Ext.Ajax.request({
						url: GO.settings.modules.email.url+'action.php',
						params: {
							task: 'add_folder',
							folder_id: node.attributes.folder_id,
							account_id: node.attributes.account_id,
							new_folder_name: text
						},
						callback: function(options, success, response)
						{
							if(!success)
							{
								Ext.MessageBox.alert(GO.lang.strError, response.result.errors);
							}else
							{
								var responseParams = Ext.decode(response.responseText);
								if(responseParams.success)
								{
									//TODO: Check for mb mode
									if(responseParams.is_mbroot){
										delete node.parentNode.attributes.children;
										node.parentNode.reload();
									} else {									
										//remove preloaded children otherwise it won't request the server
										delete node.attributes.children;
										node.reload();
									}
								}else
								{
									Ext.MessageBox.alert(GO.lang.strError,responseParams.feedback);
								}
							}
						},
						scope: this
					});
				}

			}, this);
		},
		scope:this
	}),
	this.renameFolderButton = new Ext.menu.Item({
		iconCls: 'btn-edit',
		text: GO.email.lang.renameFolder,
		handler: function()
		{
			var sm = this.treePanel.getSelectionModel();
			var node = sm.getSelectedNode();

			if(!node|| node.attributes.folder_id<1)
			{
				Ext.MessageBox.alert(GO.lang.strError, GO.email.lang.selectFolderRename);
			}else if(node.attributes.mailbox=='INBOX')
			{
				Ext.MessageBox.alert(GO.lang.strError, GO.email.lang.cantRenameInboxFolder);
			}else
			{
				Ext.MessageBox.prompt(GO.lang.strName, GO.email.lang.enterFolderName, function(button, text){
					if(button=='ok')
					{
						var sm = this.treePanel.getSelectionModel();
						var node = sm.getSelectedNode();

						this.el.mask(GO.lang.waitMsgLoad);

						Ext.Ajax.request({
							url: GO.settings.modules.email.url+'action.php',
							params: {
								task: 'rename_folder',
								folder_id: node.attributes.folder_id,
								new_name: text
							},
							callback: function(options, success, response)
							{
								if(!success)
								{
									Ext.MessageBox.alert(GO.lang.strError, response.result.errors);
									this.el.unmask();
								}else
								{
									var responseParams = Ext.decode(response.responseText);
									if(responseParams.success)
									{
										//remove preloaded children otherwise it won't request the server
										delete node.parentNode.attributes.children;

										var updateFolderName = function(){
											var node = this.treePanel.getNodeById('folder_'+this.folder_id);
											if(node){
												if(this.folder_id==node.attributes.folder_id){
													this.mailbox = node.attributes.mailbox;
													this.treePanel.getSelectionModel().select(node);
												}
											}
											this.el.unmask();
										}
										node.parentNode.reload(updateFolderName.createDelegate(this));
									}else
									{
										Ext.MessageBox.alert(GO.lang.strError,responseParams.feedback);
										this.el.unmask();
									}
								}
							},
							scope: this
						});
					}
				}, this, false, node.attributes.name);
			}
		},
		scope:this
	}),'-',	new Ext.menu.Item({
		iconCls: 'btn-delete',
		text:GO.email.lang.deleteOldMails,
		cls: 'x-btn-text-icon',
		scope:this,
		handler: function()
		{
			if (typeof(this.deleteOldMailDialog)=='undefined') {
				this.deleteOldMailDialog = new GO.email.DeleteOldMailDialog();
			}
			this.deleteOldMailDialog.setNode(this.treePanel.getSelectionModel().getSelectedNode());
			this.deleteOldMailDialog.show();
		}
	}),this.emptyFolderButton = new Ext.menu.Item({
		iconCls: 'btn-delete',
		text: GO.email.lang.emptyFolder,
		handler: function(){

			var sm = this.treePanel.getSelectionModel();
			var node = sm.getSelectedNode();

			var t = new Ext.Template(GO.email.lang.emptyFolderConfirm);

			Ext.MessageBox.confirm(GO.lang['strConfirm'], t.applyTemplate(node.attributes), function(btn){
				if(btn=='yes')
				{
					this.getEl().mask(GO.lang.waitMsgLoad);
					Ext.Ajax.request({
						url: GO.settings.modules.email.url+'action.php',
						params:{
							task:'empty_folder',
							account_id: node.attributes.account_id,
							mailbox: node.attributes.mailbox
						},
						callback:function(){
							if(node.attributes.mailbox==this.mailbox)
							{
								this.messagesGrid.store.removeAll();
								this.messagePanel.reset();
							}
							this.updateFolderStatus(node.attributes.folder_id);
							this.updateNotificationEl();
							this.getEl().unmask();
						},
						scope: this
					});
				}
			}, this);
		},
		scope:this
	}),this.deleteFolderButton = new Ext.menu.Item({
		iconCls: 'btn-delete',
		text: GO.lang.cmdDelete,
		cls: 'x-btn-text-icon',
		scope: this,
		handler: function(){
			var sm = this.treePanel.getSelectionModel();
			var node = sm.getSelectedNode();

			if(!node|| node.attributes.folder_id<1)
			{
				Ext.MessageBox.alert(GO.lang.strError, GO.email.lang.selectFolderDelete);
			}else if(node.attributes.mailbox=='INBOX')
			{
				Ext.MessageBox.alert(GO.lang.strError, GO.email.lang.cantDeleteInboxFolder);
			}else
			{
				GO.deleteItems({
					url: GO.settings.modules.email.url+'action.php',
					params: {
						task: 'delete_folder',
						folder_id: node.attributes.folder_id
					},
					callback: function(responseParams)
					{
						if(responseParams.success)
						{
							node.remove();

							if(node.attributes.mailbox==this.messagesGrid.store.baseParams.mailbox){
								this.messagesGrid.store.removeAll();
							}

							if(GO.emailportlet){
								GO.emailportlet.foldersStore.load();
							}
						}else
						{
							Ext.MessageBox.alert(GO.lang.strError,responseParams.feedback);
						}
					},
					count: 1,
					scope: this
				});
			}
		}
	}),this.shareBtn = new Ext.menu.Item({
		iconCls:'em-btn-share-mailbox ',
		text: GO.email.lang.shareFolder,
		handler:function(){
			if(!this.imapAclDialog)
				this.imapAclDialog = new GO.email.ImapAclDialog();

			var sm = this.treePanel.getSelectionModel();
			var node = sm.getSelectedNode();

			this.imapAclDialog.setParams(node.attributes.account_id,node.attributes.mailbox, node.text);
			this.imapAclDialog.show();
		},
		scope:this

	})];

	
	for(i=0;i<GO.email.extraTreeContextMenuItems.length;i++)
	{
		items.push(GO.email.extraTreeContextMenuItems[i]);
	}

	this.treeContextMenu = new Ext.menu.Menu({		
		items: items
	});
	
	
	
	this.treePanel.on('contextmenu', function(node, e){
		e.stopEvent();

		var selModel = this.treePanel.getSelectionModel();
		
		if(!selModel.isSelected(node))
		{
			selModel.clearSelections();
			selModel.select(node);
		}
		
		var coords = e.getXY();

		this.addFolderButton.setDisabled(!node.attributes.canHaveChildren);
		this.shareBtn.setVisible(node.attributes.aclSupported);

		if (GO.settings.modules.email.write_permission) {
			var node_id_type = node.attributes.id.substring(0,6);
			this.treeContextMenu.items.get(5).setDisabled(node_id_type!='folder');
		}

		this.treeContextMenu.showAt([coords[0], coords[1]]);
	}, this);

	this.treePanel.on('startdrag', function(tree, node, e){
		if(node.id.indexOf('account')>-1){
			tree.dropZone.appendOnly=false;
		}else
		{
			tree.dropZone.appendOnly=true;
		}
	}, this);

	this.treePanel.on('beforenodedrop', function(e){
		if(!e.dropNode)
		{
			var s = e.data.selections, messages = [];

			for(var i = 0, len = s.length; i < len; i++){
				messages.push(s[i].id);
			}

			if(messages.length>0)
			{

				if(this.account_id != e.target.attributes['account_id'])
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

					var conn = new Ext.data.Connection({
						timeout:300000
					});

					var moveRequest = function(newMessages){

						if(!newMessages)
						{
							params.total=messages.length;
						}else
						{
							params.messages=Ext.encode(newMessages);
						}

						conn.request({
							url:GO.settings.modules.email.url+'action.php',
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

				}else	if(this.mailbox == e.target.mailbox)
				{
					return false;
				}else
				{
					this.messagesGrid.store.baseParams['action']='move';
					this.messagesGrid.store.baseParams['from_account_id']=this.account_id;
					this.messagesGrid.store.baseParams['to_account_id']=e.target.attributes['account_id'];
					this.messagesGrid.store.baseParams['from_mailbox']=this.mailbox;
					this.messagesGrid.store.baseParams['to_mailbox']=e.target.attributes['mailbox'];
					this.messagesGrid.store.baseParams['messages']=Ext.encode(messages);

					this.messagesGrid.store.reload({
						callback:function(){
							if(this.messagePanel.uid && !this.messagesGrid.store.getById(this.messagePanel.uid))
							{
								this.messagePanel.reset();
							}
						},
						scope:this
					});

					delete this.messagesGrid.store.baseParams['action'];
					delete this.messagesGrid.store.baseParams['from_mailbox'];
					delete this.messagesGrid.store.baseParams['to_mailbox'];
					delete this.messagesGrid.store.baseParams['messages'];
					delete this.messagesGrid.store.baseParams['to_account_id'];
					delete this.messagesGrid.store.baseParams['from_account_id'];
				}

			}
		}
	},
	this);

	this.treePanel.on('nodedrop', function(e){
		if(e.dropNode)
		{
			if(e.source.dragData.node.id.indexOf('account')>-1 && e.target.id.indexOf('account')>-1 && e.point!='append'){
				var sortorder=[];
				var c = this.treePanel.getRootNode().childNodes;

				for(var i=0;i<c.length;i++){
					sortorder.push(c[i].attributes.account_id);
				}
				Ext.Ajax.request({
					url: GO.settings.modules.email.url+'action.php',
					params: {
						task: 'save_accounts_sort_order',
						sort_order: Ext.encode(sortorder)
					}
				});
			}else
			{
				this.treePanel.moveFolder(e.target.attributes['account_id'], e.target.id , e.data.node);
			}
		}

		this.treePanel.dropZone.appendOnly=true;
	},
	this);

	
	//select the first inbox to be displayed in the messages grid
	root.on('load', function(node)
	{
		this.body.unmask();		
		if(node.childNodes[0])
		{
			var firstAccountNode=false;

			this.updateNotificationEl();

			for(var i=0;i<node.childNodes.length;i++){
				firstAccountNode = node.childNodes[i];

				if(firstAccountNode.expanded){

					firstAccountNode.on('load', function(node){

						if(node.childNodes[0])
						{
							var firstInboxNode = node.childNodes[0];

							this.setAccount(
								firstInboxNode.attributes.account_id,
								firstInboxNode.attributes.folder_id,
								firstInboxNode.attributes.mailbox,
								firstInboxNode.parentNode.attributes.usage
								);
						//if(!this.checkMailStarted)
						//this.checkMail.defer(this.checkMailInterval, this);
						}
					},this, {
						single: true
					});
					break;
				}
			}
			
			
		}
	}, this);
	
	this.treePanel.on('beforeclick', function(node){
		if(node.attributes.folder_id==0)
			return false;
	}, this);

	this.treePanel.on('beforeclick', function(node){
			if(node.attributes.noSelect==1)
			{
				return false;
			}
	});

	this.treePanel.on('click', function(node)	{
		if(node.attributes.folder_id>0)
		{
			var usage='';
			var cnode = node;
			while(cnode.parentNode && usage=='')
			{
				if(cnode.attributes.usage)
				{
					usage=cnode.attributes.usage;
				}else
				{
					cnode=cnode.parentNode;
				}
			}
			
			this.setAccount(
				node.attributes.account_id,
				node.attributes.folder_id,
				node.attributes.mailbox,
				usage
				);
		}
	}, this);

	this.treePanel.on('collapsenode', function(node)
	{
		if(node.attributes.folder_id && (node.attributes.folder_id != 'undefined') && node.childNodes.length)
		{
			this.updateState(node, false, true);
		//this.updateFolderState(node, false);
		}else
		if(!node.attributes.folder_id && node.attributes.account_id)
		{
			this.updateState(node, false, false);
		//this.updateAccountState(node, false);
		}
	},this);

	this.treePanel.on('expandnode', function(node)
	{
		if(node.attributes.folder_id && (node.attributes.folder_id != 'undefined') && node.childNodes.length)
		{
			this.updateState(node, true, true);
		//this.updateFolderState(node, true);
		}else
		if(!node.attributes.folder_id && node.attributes.account_id)
		{
			this.updateState(node, true, false);
		//this.updateAccountState(node, true);
		}
	},this);
	
	this.searchDialog = new GO.email.SearchDialog({
		store:this.messagesGrid.store
	});
	
	this.settingsMenu = new Ext.menu.Menu({
		items:[{
			iconCls: 'btn-accounts',
			text: GO.email.lang.accounts,
			cls: 'x-btn-text-icon',
			handler: function(){
				this.showAccountsDialog();
			},
			scope: this
		},{
			iconCls:'btn-toggle-window',
			text: GO.email.lang.toggleWindowPosition,
			cls: 'x-btn-text-icon',
			handler: function(){
				this.moveGrid();
			},
			scope: this
		}]
	});
	
	if(GO.gnupg)
	{
		this.settingsMenu.add('-');
		this.settingsMenu.add({
			iconCls:'gpg-btn-settings',
			cls: 'x-btn-text-icon',
			text:GO.gnupg.lang.encryptionSettings,
			handler:function(){
				if(!this.securityDialog)
				{
					this.securityDialog = new GO.gnupg.SecurityDialog();
				}
				this.securityDialog.show();
			},
			scope:this
		});
	}
	
	var tbar =[{
		iconCls: 'btn-compose',
		text: GO.email.lang['compose'],
		cls: 'x-btn-text-icon',
		handler: function(){
						
			GO.email.showComposer({
				account_id: this.account_id
			});
		},
		scope: this
	},{
		iconCls: 'btn-delete',
		text: GO.lang.cmdDelete,
		cls: 'x-btn-text-icon',
		handler: function(){
			this.messagesGrid.deleteSelected();
		},
		scope: this
	},new Ext.Toolbar.Separator(),
	{
		iconCls: 'btn-settings',
		text:GO.lang.administration,
		menu: this.settingsMenu
	},{
		iconCls: 'btn-refresh',
		text: GO.lang.cmdRefresh,
		cls: 'x-btn-text-icon',
		handler: function(){
			this.refresh(true);
		},
		scope: this
	},
	{
		iconCls: 'btn-search',
		text: GO.lang.strSearch,
		cls: 'x-btn-text-icon',
		handler: function(){
			this.searchDialog.show();
		},
		scope: this
	},
	this.resetSearchButton = new Ext.Button({
		iconCls: 'btn-delete',
		text: GO.email.lang.resetSearch,
		cls: 'x-btn-text-icon',
		hidden:true,
		handler: function(){
			this.searchDialog.hasSearch = false;
			this.messagesGrid.store.baseParams['query']='';
			this.messagesGrid.store.load({
				params:{
					start:0
				}
			});
		},
		scope: this
	})
	,
	'-',
	this.replyButton=new Ext.Button({
		disabled:true,
		iconCls: 'btn-reply',
		text: GO.email.lang.reply,
		cls: 'x-btn-text-icon',
		handler: function(){
						
			GO.email.showComposer({
				uid: this.messagePanel.uid,
				task: 'reply',
				mailbox: this.mailbox,
				account_id: this.account_id
			});
		},
		scope: this
	}),this.replyAllButton=new Ext.Button({
		disabled:true,
		iconCls: 'btn-reply-all',
		text: GO.email.lang.replyAll,
		cls: 'x-btn-text-icon',
		handler: function(){
			GO.email.showComposer({
				uid: this.messagePanel.uid,
				task: 'reply_all',
				mailbox: this.mailbox,
				account_id: this.account_id
							
			});
		},
		scope: this
	}),this.forwardButton=new Ext.Button({
		disabled:'true',
		iconCls: 'btn-forward',
		text: GO.email.lang.forward,
		cls: 'x-btn-text-icon',
		handler: function(){
			GO.email.showComposer({
				uid: this.messagePanel.uid,
				task: 'forward',
				mailbox: this.mailbox,
				account_id: this.account_id
			});
		},
		scope: this
	}),
				
	this.printButton = new Ext.Button({
		disabled: true,
		iconCls: 'btn-print',
		text: GO.lang.cmdPrint,
		cls: 'x-btn-text-icon',
		handler: function(){
			this.messagePanel.body.print();
		},
		scope: this
	})];
				
				
	if(GO.email.saveAsItems && GO.email.saveAsItems.length)
	{
		tbar.push({
			iconCls: 'btn-save',
			text:GO.lang.cmdSaveAs,
			menu:this.saveAsMenu
		});
	}
				
	tbar.push(new Ext.Toolbar.Separator());
				
				
	tbar.push(this.closeMessageButton = new Ext.Button({
		hidden:true,
		iconCls: 'btn-close',
		text: GO.lang.cmdClose,
		cls: 'x-btn-text-icon',
		handler: function(){
			this.messagesGrid.expand();
								
		},
		scope: this
	}));

	
	config.layout='border';
	config.tbar=new Ext.Toolbar({
		cls:'go-head-tb',
		items: tbar
	});

	this.messagePanel = new GO.email.MessagePanel({
		id:'email-message-panel',
		region:'center',
		autoScroll:true,
		titlebar: false,
		border:true,
		attachmentContextMenu: new GO.email.AttachmentContextMenu({
			emailClient:this
		})
	});

	config.items=[
	this.treePanel,
	{
		region:'center',
		titlebar: false,
		layout:'border',
		items: [
		this.messagePanel,
		this.topMessagesGrid,
		this.leftMessagesGrid
		]
	}];

  	
	this.messagePanel.on('load', function(options, success, response){
		if(!success)
		{
			this.messagePanel.uid=0;
		}else
		{
			//this.messagePanel.uid=record.data['uid'];
			
			this.replyAllButton.setDisabled(false);
			this.replyButton.setDisabled(false);
			this.forwardButton.setDisabled(false);
			this.printButton.setDisabled(false);
			
			var record = this.messagesGrid.store.getById(this.messagePanel.uid);
			if(record.data['new']==1)
			{
				this.incrementFolderStatus(this.folder_id, -1);
				record.set('new','0');
			}
		}
  	
	}, this);
  
	this.messagePanel.on('reset', function(){
		this.replyAllButton.setDisabled(true);
		this.replyButton.setDisabled(true);
		this.forwardButton.setDisabled(true);
		this.printButton.setDisabled(true);
	}, this);
  	
  
	this.messagePanel.on('linkClicked', function(href){
		var win = window.open(href);
		win.focus();
	}, this);
  
	this.messagePanel.on('attachmentClicked', this.openAttachment, this);
	//this.messagePanel.on('zipOfAttachmentsClicked', this.openZipOfAttachments, this);
  
  
	/*this.messagePanel.on('emailClicked', function(email){
  	this.showComposer({to: email});
  }, this);*/
  
	/*
   * for email seaching on sender from message panel
   */
	GO.email.searchSender=function(sender)
	{
		if(this.rendered)
		{
			GO.email.search_type = 'from';
			this.messagesGrid.showUnreadButton.toggle(false, true);
			this.messagesGrid.store.baseParams['search'] = sender;
			GO.email.messagesGrid.store.baseParams['unread']=false;
			this.messagesGrid.setSearchFields('from', sender);

			this.messagesGrid.store.load({
				params:{
					start:0
				}
			});

			if(GO.mainLayout.tabPanel)
				GO.mainLayout.tabPanel.setActiveTab(this.id);
		}else
		{
			alert(GO.email.lang.loadEmailFirst);
		}
	}
	GO.email.searchSender = GO.email.searchSender.createDelegate(this);
  
	GO.email.EmailClient.superclass.constructor.call(this, config);
};

Ext.extend(GO.email.EmailClient, Ext.Panel,{	
	
	moveGrid : function(){
		if(this.topMessagesGrid.isVisible())
		{
			this.messagesGrid=this.leftMessagesGrid;
			this.topMessagesGrid.hide();
	    
		}else
		{
			this.messagesGrid=this.topMessagesGrid;
			this.leftMessagesGrid.hide();
		}
		//this.messagesGridContainer.add(this.messagesGrid);
		this.messagesGrid.show();
		this.messagesGrid.ownerCt.doLayout();
    
		Ext.state.Manager.set('em-msgs-top', Ext.encode(this.topMessagesGrid.isVisible()));
	},
	
	addGridHandlers : function(grid)
	{
		grid.on("rowcontextmenu", function(grid, rowIndex, e) {
			var coords = e.getXY();
			this.gridContextMenu.showAt([coords[0], coords[1]], grid.getSelectionModel().getSelections());
		},this);
		
		grid.on('collapse', function(){
			this.closeMessageButton.setVisible(true);
		}, this);
		
		grid.on('expand', function(){
			this.closeMessageButton.setVisible(false);
		}, this);
		
		grid.on("rowdblclick", function(){
			if(this.messagesGrid.store.reader.jsonData.drafts)
			{
				GO.email.showComposer({
					uid: this.messagePanel.uid,
					task: 'opendraft',
					template_id: 0,
					mailbox: this.mailbox,
					account_id: this.account_id
				});
			}else
			{				
				//var messageDialog = new GO.email.MessageDialog();
				//messageDialog.show(this.messagePanel.uid, this.mailbox, this.account_id);
				this.messagesGrid.collapse();
			}			
		}, this);

		//this.messagesGrid.getSelectionModel().on("rowselect",function(sm, rowIndex, r){
		grid.on("delayedrowselect",function(grid, rowIndex, r){
			if(r.data['uid']!=this.messagePanel.uid)
			{
				//this.messagePanel.uid=r.data['uid'];
				this.messagePanel.loadMessage(r.data.uid, this.mailbox, this.account_id);
			}
		}, this);
	},
	
	afterRender : function(){
		GO.email.EmailClient.superclass.afterRender.call(this);

		GO.email.notificationEl.setDisplayed(false);
		
		this.body.mask(GO.lang.waitMsgLoad);
	},
	
	onShow : function(){
		
		GO.email.notificationEl.setDisplayed(false);

		GO.email.EmailClient.superclass.onShow.call(this);
	},
	
	updateNotificationEl : function(){
		var node = this.treePanel.getRootNode();

		GO.email.totalUnseen=0;
		for(var i=0;i<node.childNodes.length;i++)
		{
			GO.email.totalUnseen += node.childNodes[i].attributes.inbox_new;
		}

	},
	
	saveAttachment : function(attachment)
	{
		if(!GO.files.saveAsDialog)
		{
			GO.files.saveAsDialog = new GO.files.SaveAsDialog();
		}
		GO.files.saveAsDialog.show({
			filename: attachment.name,
			handler:function(dialog, folder_id, filename){
			
				GO.request({
					maskEl:dialog.el,
					url: 'email/message/saveAttachment',
					params:{
						//task:'save_attachment',
						uid: this.messagePanel.uid,
						mailbox: this.mailbox,
						number: attachment.number,
						encoding: attachment.encoding,
						type: attachment.type,
						subtype: attachment.subtype,
						account_id: this.account_id,
						uuencoded_partnumber: attachment.uuencoded_partnumber,
						folder_id: folder_id,
						filename: filename,
						charset:attachment.charset,
						sender:this.messagePanel.data.sender,
						filepath:this.messagePanel.data.path//smime message are cached on disk
					},
					success: function(options, response, result)
					{			
						dialog.hide();						
					},
					scope:this
				});
			},
			scope:this
		});
	},
	
	openAttachment :  function(attachment, panel, forceDownload)
	{
		if(!attachment)
			return false;
		
		var params = {
			action:'attachment',
			account_id: this.account_id,
			mailbox: this.mailbox,
			uid: this.messagePanel.uid,
			number: attachment.number,
			uuencoded_partnumber: attachment.uuencoded_partnumber,
			encoding: attachment.encoding,
			type: attachment.type,
			subtype: attachment.subtype,
			filename:attachment.name,
			charset:attachment.charset,
			sender:this.messagePanel.data.sender, //for gnupg and smime,
			filepath:this.messagePanel.data.path ? this.messagePanel.data.path : '' //In some cases encrypted messages are temporary stored on disk so the handlers must use that to fetch the data.
		}

		var url_params = '?';
		for(var name in params){
			url_params+= name+'='+encodeURIComponent(params[name])+'&';
		}
		url_params = url_params.substring(0,url_params.length-1);
		
		if(!forceDownload && attachment.mime=='message/rfc822')
		{
			GO.email.showMessageAttachment(0, params);
		}else
		{
			switch(attachment.extension)
			{
//				case 'dat':
//					document.location.href=GO.settings.modules.email.url+
//					'tnef.php'+url_params;
//					break;			
				
//				case 'vcs':
//				case 'ics':
//					if(!forceDownload)
//					{
//						params.task='icalendar_attachment';
//						Ext.Ajax.request({
//							url: GO.settings.modules.email.url+'json.php',
//							params: params,
//							callback: function(options, success, response)
//							{
//								if(success)
//								{
//									var values = Ext.decode(response.responseText);
//									
//									if(!values.success)
//									{
//										alert(values.feedback);
//									}else
//									{
//										GO.calendar.showEventDialog({
//											values: values
//										});
//									}
//								}else
//								{
//									alert( GO.lang.strRequestError);
//								}
//							},
//							scope: this
//						});
//						break;
//					}
				
				case 'png':
				case 'bmp':
				case 'png':
				case 'gif':
				case 'jpg':
				case 'jpeg':
				
					if(GO.files && !forceDownload)
					{
						if(!this.imageViewer)
						{
							this.imageViewer = new GO.files.ImageViewer({
								closeAction:'hide'
							});
						}
					
						var index = 0;
						var images = Array();
						if(panel)
						{
							for (var i = 0; i < panel.data.attachments.length;  i++)
							{
								var r = panel.data.attachments[i];
								var ext = GO.util.getFileExtension(r.name);
							
								if(ext=='jpg' || ext=='png' || ext=='gif' || ext=='bmp' || ext=='jpeg')
								{
									images.push({
										name: r.name,
										src: r.url+'&inline=0'
									});
								}
								if(r.name==attachment.name)
								{
									index=images.length-1;
								}
							}
							this.imageViewer.show(images, index);
							break;
						}
					}	
				
				default:
					if(forceDownload)
						attachment.url+='&inline=0';
					window.open(attachment.url);
					break;
			}
		}
	},
	
//	openZipOfAttachments : function()
//	{
//		document.location.href=GO.settings.modules.email.url+
//		'zip_attachments.php?account_id='+this.account_id+
//		'&mailbox='+encodeURIComponent(this.mailbox)+
//		'&uid='+this.messagePanel.uid+'&filename='+encodeURIComponent(this.messagePanel.data.subject);
//	},
//	
	showComposer : function(values)
	{
		GO.email.showComposer(
		{
			account_id: this.account_id,
			values : values
		});
	},
	
	setAccount : function(account_id,folder_id,mailbox, usage)
	{
		if(folder_id!=this.folder_id)
		{
			this.messagePanel.reset();
			this.messagesGrid.getSelectionModel().clearSelections();
		}
		
		this.messagesGrid.expand();
		
		this.account_id = account_id;
		this.folder_id = folder_id;
		this.mailbox = mailbox;

		//messagesPanel.setTitle(mailbox);
		this.messagesGrid.store.baseParams['task']='messages';
		this.messagesGrid.store.baseParams['account_id']=account_id;
		this.messagesGrid.store.baseParams['folder_id']=folder_id;
		this.messagesGrid.store.baseParams['mailbox']=mailbox;
		this.messagesGrid.store.load({
			params:{
				start:0
			}
		});
		//this.messagesGrid.store.load();
		
		this.treePanel.setUsage(usage);
	},
	/**
	 * Returns true if the current folder needs to be refreshed in the grid
	 */
	updateFolderStatus : function(folder_id, unseen)
	{
		var statusEl = Ext.get('status_'+folder_id);

		var node = this.treePanel.getNodeById('folder_'+folder_id);
		if(node && node.attributes.mailbox=='INBOX')
		{
			node.parentNode.attributes.inbox_new=unseen;
		}
		
		if(statusEl && statusEl.dom)
		{
			var statusText = statusEl.dom.innerHTML;
			var current = statusText=='' ? 0 : parseInt(statusText.substring(1, statusText.length-1));
			
			if(current != unseen)
			{
				if(unseen>0)
				{
					statusEl.dom.innerHTML = "("+unseen+")";
				}else
				{
					statusEl.dom.innerHTML = "";
				}
				return true;
			}
		}
		return false;
	},
	
	incrementFolderStatus : function(folder_id, increment)
	{
		//decrease treeview status id is defined in tree_json.php
		var statusEl = Ext.get('status_'+folder_id);
		
		var statusText = statusEl.dom.innerHTML;
		
		var status = 0;
		if(statusText!='')
		{
			var status = parseInt(statusText.substring(1, statusText.length-1));
		}
		status+=increment;
		
		this.updateFolderStatus(folder_id, status);
		this.updateNotificationEl();
	},
	



	refresh : function(refresh)
	{
		if(refresh)
			this.treePanel.loader.baseParams.refresh=true;
			
		this.treePanel.root.reload();
		this.messagesStore.removeAll();
		
		if(refresh)
			delete this.treePanel.loader.baseParams.refresh;
	},

	showAccountsDialog : function()
	{
		if(!this.accountsDialog)
		{
			this.accountsDialog = new GO.email.AccountsDialog();
			this.accountsDialog.accountsGrid.accountDialog.on('save', function(dialog, result){
				if(result.refreshNeeded){
					this.refresh();					
				}
			}, this);
				
			this.accountsDialog.accountsGrid.on('delete', function(){
				this.refresh();
				if(GO.emailportlet)
					GO.emailportlet.foldersStore.load();
			}, this);
		}
		this.accountsDialog.show();
	},
	
	doTaskOnMessages : function (task){
		var selectedRows = this.messagesGrid.selModel.selections.keys;

		if(selectedRows.length)
		{
			
			Ext.Ajax.request({
				url: GO.settings.modules.email.url+'action.php',
				params: {
					task: 'flag_messages',
					account_id: this.account_id,
					mailbox: this.mailbox,
					action: task,
					messages: Ext.encode(selectedRows)
				},
				callback: function(options, success, response)
				{

					if(!success)
					{
						Ext.MessageBox.alert(GO.lang['strError'], GO.lang['strRequestError']);
					}else
					{
						
						var responseParams = Ext.decode(response.responseText);
						if(!responseParams.success)
						{
							Ext.MessageBox.alert(GO.lang['strError'], responseParams.feedback);
						}else
						{
							var field;
							var value;
							
							var records = this.messagesGrid.selModel.getSelections();
							
							switch(task)
							{
								case 'mark_as_read':
									field='new';
									value=false;

									for(var i=0;i<records.length;i++){
										if(records[i].get('new')=='1')
											GO.email.totalUnseen--;
									}
									
									break;
								case 'mark_as_unread':
									field='new';
									value=true;
									
									for(var i=0;i<records.length;i++){
										if(records[i].get('new')!='1')
											GO.email.totalUnseen++;
									}
									break;
									
								case 'flag':
									field='flagged';
									value=true;
									break;
								case 'unflag':
									field='flagged';
									value=false;
									break;
							}
							
							
							for(var i=0;i<records.length;i++)
							{
								records[i].set(field, value);
								records[i].commit();
							}
							
							this.updateFolderStatus(this.folder_id, responseParams.unseen);
							this.updateNotificationEl();
						}
					}
				},
				scope:this
			});
		
		}
	},
	
	addSendersTo : function(menuItem){
		var records = this.messagesGrid.getSelectionModel().getSelections();
		
		var emails=[];
		for(var i=0;i<records.length;i++)
		{
			emails.push('"'+records[i].get('from')+'" <'+records[i].get('sender')+'>');
		}
		
		var activeComposer=false;
		if(GO.email.composers)
		{
			for(var i=GO.email.composers.length-1;i>=0;i--)
			{
				if(GO.email.composers[i].isVisible())
				{
					activeComposer=GO.email.composers[i];
					break;
				}
			}
		}
		
		if(activeComposer)
		{
			var f = activeComposer.formPanel.form.findField(menuItem.field);
			var v = f.getValue();
			if(v!='')
			{
				v+=', ';
			}
			v+=emails.join(', ');
			f.setValue(v);
			activeComposer.focus();
		}else
		{
			var config={
				values:{}
			}
			config.values[menuItem.field]=emails.join(', ');
			GO.email.showComposer(config);
		}
	},

	addSendersToAddresslist : function(addresslistId) {
		var records = this.messagesGrid.getSelectionModel().getSelections();
		var senderNames = new Array();
		var senderEmails = new Array();
		for (var i=0;i<records.length;i++) {
			senderNames.push(records[i].data.from);
			senderEmails.push(records[i].data.sender);
		}
		
		senderNames.push('Testpersoon, Testje');
		senderEmails.push('foobar@testpersoon.dev');
		senderNames.push('Testpersoon1, Testje');
		senderEmails.push('foobar1@testpersoon.dev');
		senderNames.push('Testpersoon2, Testje');
		senderEmails.push('foobar2@testpersoon.dev');
		
		Ext.Ajax.request({
			url: GO.url('addressbook/addresslist/addContactsToAddresslist'),
			params: {
				senderNames: Ext.encode(senderNames),
				senderEmails: Ext.encode(senderEmails),
				addresslistId: addresslistId
			},
			callback: function(options, success, response)
			{
				if(!success)
				{
					Ext.MessageBox.alert(GO.lang.strError, response.result.errors);
				}else
				{
					var responseParams = Ext.decode(response.responseText);
					if(responseParams.success)
					{

					}else
					{
						if (!GO.util.empty(responseParams.unknownSenders)) {
							
							if (!this.unknownRecipientsDialogForAddresslist) {
								this.unknownRecipientsDialogForAddresslist = new GO.email.UnknownRecipientsDialog();
								this.unknownRecipientsDialogForAddresslist.on('hide',function(){
									if (!GO.util.empty(this.unknownRecipientsDialogForAddresslist.addresslistId))
										delete this.unknownRecipientsDialogForAddresslist.addresslistId;
								},this);
							}

							this.unknownRecipientsDialogForAddresslist.store.loadData({
								recipients : Ext.decode(responseParams.unknownSenders)
							});

							this.unknownRecipientsDialogForAddresslist.addresslistId = addresslistId;

							this.unknownRecipientsDialogForAddresslist.show({
								title : GO.email.lang.addUnknownSenders,
								descriptionText : GO.email.lang.addUnknownSendersText,
								disableSkipUnknownCheckbox : true
							});

						} else {
							Ext.MessageBox.alert(GO.lang.strError,responseParams.feedback);
						}
					}
				}
			},
			scope: this
		});
	},

	updateState : function(node, open, folder)
	{
		var id = (folder) ? node.attributes.folder_id : node.attributes.account_id;
		if(node.attributes.parentExpanded != open)
		{
			Ext.Ajax.request({
				url: GO.settings.modules.email.url+'action.php',
				params: {
					task: 'update_state',
					id: id,
					open: open,
					folder: folder
				},
				callback: function(options, success, response)
				{
					if(!success)
					{
						Ext.MessageBox.alert(GO.lang.strError, response.result.errors);
					}else
					{
						var responseParams = Ext.decode(response.responseText);
						if(responseParams.success)
						{
							node.attributes.parentExpanded = open;
						}else
						{
							Ext.MessageBox.alert(GO.lang.strError,responseParams.feedback);
						}
					}
				},
				scope: this
			});
		}
	}
	
});

GO.mainLayout.onReady(function(){
	//GO.email.Composer = new GO.email.EmailComposer();
	
	//contextmenu when an e-mail address is clicked
	GO.email.addressContextMenu=new GO.email.AddressContextMenu();

	GO.email.search_type_default = 'any';



	//GO.checker is not available in some screens like accept invitation from calendar
	if(true){
		//create notify icon
		var notificationArea = Ext.get('notification-area');
		if(notificationArea)
		{
			GO.email.notificationEl = notificationArea.createChild({
				id: 'ml-notify',
				tag:'a',
				href:'#',
				style:'display:none'
			});
			GO.email.notificationEl.on('click', function(){
				GO.mainLayout.openModule('email');
			}, this);
		}
		
		Ext.TaskMgr.start({
			run: function(){
				GO.request({
					url:'email/account/checkUnseen',
					scope:this,
					success:function(options, response, data){
						var ep = GO.mainLayout.getModulePanel('email');

						var totalUnseen = 0;
						for(var folder_id in data.email_status)
						{
							if(ep){
								var changed = ep.updateFolderStatus(folder_id, data.email_status[folder_id].unseen);
								if(changed && ep.messagesGrid.store.baseParams.folder_id==folder_id)
								{
									ep.messagesGrid.store.reload();
								}
							}
							totalUnseen += data.email_status[folder_id].unseen;
						}

						if(totalUnseen!=GO.email.totalUnseen && totalUnseen>0)
						{
							data.reminderText='<p>'+GO.email.lang.youHaveNewMails.replace('{new}', totalUnseen)+'</p>';

							if(!ep || !ep.isVisible())
								GO.email.notificationEl.setDisplayed(true);
							
							GO.playAlarm();
							if(!GO.hasFocus && !GO.util.empty(GO.settings.popup_reminders)){
								GO.reminderPopup = GO.util.popup({
									width:400,
									height:300,
									url:BaseHref+'reminder.php?reminder_text='+encodeURIComponent(data.reminderText)+"&count=0",
									target:'groupofficeReminderPopup',
									position:'br',
									closeOnFocus:false
								});
							}
						}

						GO.email.notificationEl.update(totalUnseen);
						GO.email.totalUnseen=totalUnseen;
					}
				});
			},
			scope:this,
			interval:120000
		});

		
	}
});

GO.email.aliasesStore = new GO.data.JsonStore({
	url: GO.settings.modules.email.url+ 'json.php',
	baseParams: {
		task: 'all_aliases'
	},
	root: 'results',
	id: 'id',
	totalProperty:'total',
	fields: ['id','account_id','name','email','html_signature', 'plain_signature'],
	remoteSort: true
});



	
/**
 * Function that will open an email composer. If a composer is already open it will create a new one. Otherwise it will reuse an already created one.
 */
GO.email.showComposer = function(config){
	
	config = config || {};
               
	GO.email.composers = GO.email.composers || [];
	
	var availableComposer;
        this.selectFiles = config.selectFilesFromFolderID;

	for(var i=0;i<GO.email.composers.length;i++)
	{
		if(!GO.email.composers[i].isVisible())
		{
			availableComposer=GO.email.composers[i];
			break;
		}
	}

	
	if(!availableComposer)
	{
		config.move=30*GO.email.composers.length;
		
		availableComposer = new GO.email.EmailComposer();
		availableComposer.on('send', function(composer){
			if(composer.sendParams.reply_uid && composer.sendParams.reply_uid>0)
			{
				var record = GO.email.messagesGrid.store.getById(composer.sendParams.reply_uid);
				if(record)
				{
					record.set('answered',true);
				}
			}

			if(composer.sendParams.forward_uid && composer.sendParams.forward_uid>0)
			{
				var record = GO.email.messagesGrid.store.getById(composer.sendParams.forward_uid);
				if(record)
				{
					record.set('forwarded',true);
				}
			}
			
			if(GO.email.messagesGrid && GO.email.messagesGrid.store.loaded && (GO.email.messagesGrid.store.reader.jsonData.sent || (GO.email.messagesGrid.store.reader.jsonData.drafts && composer.sendParams.draft_uid && composer.sendParams.draft_uid>0)))
			{
				GO.email.messagesGrid.store.reload();
			}
		});
		
		availableComposer.on('save', function(composer){

			if(GO.email.messagesGrid && GO.email.messagesGrid.store.loaded && GO.email.messagesGrid.store.reader.jsonData.drafts)
			{
				GO.email.messagesGrid.store.reload();
			}
		});

                availableComposer.on('dialog_ready', function(composer)
                {
                        if(this.selectFiles)
                        {                              
                                GO.files.selectFilesDialog.show(this.selectFiles);
                        }
                },this);
		
		GO.email.composers.push(availableComposer);
	}

	availableComposer.show(config);
	
	return availableComposer;
}

GO.email.extraTreeContextMenuItems = [];

GO.moduleManager.addModule('email', GO.email.EmailClient, {
	title : GO.lang.strEmail,
	iconCls : 'go-tab-icon-email'
});


GO.email.showAddressMenu = function(e, email, name)
{
	var e = Ext.EventObject.setEvent(e);
	e.preventDefault();
	GO.email.addressContextMenu.showAt(e.getXY(), email, name);
}

GO.newMenuItems.push({
	text: GO.email.lang.email,
	iconCls: 'go-model-icon-GO_Email_Model_ImapMessage',
	handler:function(item, e){
		var taskShowConfig = item.parentMenu.taskShowConfig || {};
		taskShowConfig.link_config=item.parentMenu.link_config
		taskShowConfig.values={};
		if(typeof(item.parentMenu.panel)!='undefined' && typeof(item.parentMenu.panel.data.email)!='undefined'){
			var to='';
			if(item.parentMenu.panel.data.full_name){
				to='"'+item.parentMenu.panel.data.full_name+'" <'+item.parentMenu.panel.data.email+'>';
			}else if(item.parentMenu.panel.data.name){
				to='"'+item.parentMenu.panel.data.name+'" <'+item.parentMenu.panel.data.email+'>';
			}

			taskShowConfig.values.to=to;
		}

//		if(GO.settings.modules.savemailas.read_permission)
//			taskShowConfig.values.subject='[id:'+item.parentMenu.link_config.modelNameAndId+'] ';
		
		GO.email.showComposer(taskShowConfig);
	}
});


GO.email.showMessageAttachment = function(id, remoteMessage){

	if(!GO.email.linkedMessagePanel){
		GO.email.linkedMessagePanel = new GO.email.LinkedMessagePanel();

		GO.email.linkedMessageWin = new GO.Window({
			maximizable:true,
			collapsible:true,
			stateId:'em-linked-message-panel',
			title: GO.email.lang.emailMessage,
			height: 500,
			width: 800,
			closeAction:'hide',
			layout:'fit',
			items: GO.email.linkedMessagePanel
		});
	}
	
	if(!remoteMessage)
		remoteMessage={};
	
	GO.email.linkedMessagePanel.remoteMessage=remoteMessage;
	GO.email.linkedMessageWin.show();
	GO.email.linkedMessagePanel.load(id, remoteMessage);
}

//GO.newMenuItems.push({
//	text: GO.email.lang.emailFiles,
//	iconCls: 'go-model-icon-GO_Email_Model_LinkedEmail',
//	handler:function(item, e)
//        {               
//                var taskShowConfig = item.parentMenu.taskShowConfig || {};
//                //taskShowConfig.link_config=item.parentMenu.link_config
//                taskShowConfig.values={};
//                if(item.parentMenu.panel.data.email){
//                        var to='';
//                        if(item.parentMenu.panel.data.full_name){
//                                to='"'+item.parentMenu.panel.data.full_name+'" <'+item.parentMenu.panel.data.email+'>';
//                        }else if(item.parentMenu.panel.data.name){
//                                to='"'+item.parentMenu.panel.data.name+'" <'+item.parentMenu.panel.data.email+'>';
//                        }
//
//                        taskShowConfig.values.to=to;
//                }
//
////                if(GO.settings.modules.savemailas.read_permission)
////                        taskShowConfig.values.subject='[id:'+item.parentMenu.link_config.modelNameAndId+'] ';
//
//                taskShowConfig.selectFilesFromFolderID = item.parentMenu.panel.data.files_folder_id;
//                this.availableComposer = GO.email.showComposer(taskShowConfig);
//                
//                if(!GO.files.selectFilesDialog)
//                {
//                    GO.files.selectFilesDialog = new GO.files.SelectFilesDialog();
//
//                    GO.files.selectFilesDialog.on('save', function(obj, files)
//                    {
//                            for(var i=0; i<files.length; i++)
//                            {
//                                    files[i] = files[i].substr(2);
//                            }                          
//
//                            Ext.Ajax.request({
//                                    url:GO.settings.modules.files.url+'json.php',
//                                    params:{
//                                            task:'attachments',
//                                            file_ids: Ext.encode(files)
//                                    },
//                                    callback:function(options, success, response){
//
//                                            var data = Ext.decode(response.responseText);
//
//                                            if(!data.success)
//                                                {
//                                                        Ext.Msg.alert(GO.lang['strError'], data.feedback);
//                                                }else
//                                                {
//                                                        this.availableComposer.addAttachments(data.results);
//                                                }
//                                    },
//                                    scope:this
//                            });
//                    },this)
//                }                
//        }
//
//});
