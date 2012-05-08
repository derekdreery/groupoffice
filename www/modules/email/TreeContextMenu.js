GO.email.TreeContextMenu = Ext.extend(Ext.menu.Menu,{
	
	setNode : function(node){
		this.addFolderButton.setDisabled(node.attributes.noinferiors);
		this.shareBtn.setVisible(node.attributes.aclSupported);

//		if (GO.settings.modules.email.write_permission) {
//			var node_id_type = node.attributes.id.substring(0,6);
//			this.items.get(5).setDisabled(node_id_type!='folder');
//		}
	},
	initComponent : function(){
		
		this.items=[
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

	
		for(var i=0;i<GO.email.extraTreeContextMenuItems.length;i++)
		{
			this.items.push(GO.email.extraTreeContextMenuItems[i]);
		}
		
		GO.email.TreeContextMenu.superclass.initComponent.call(this);
	}
}
);
