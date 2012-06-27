GO.email.TreeContextMenu = Ext.extend(Ext.menu.Menu,{
	
	hasAcl : function(node){
		var p = node.parentNode;
		while(p){
				if(p.attributes.acl_supported){
						return true;
				}
				p = p.parentNode;
		}
		return false;
	},
	
	setNode : function(node){
		this.addFolderButton.setDisabled(node.attributes.noinferiors);
		
		this.shareBtn.setVisible(this.hasAcl(node));

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
				
						GO.request({
							url: "email/folder/create",
							maskEl: Ext.getBody(),
							params: {
								parent: node.attributes.mailbox,
								account_id: node.attributes.account_id,
								name: text
							},
							success: function(options, response, result)
							{								
								this.treePanel.refresh(node);
							},
							fail : function(){
								this.treePanel.refresh();
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

				if(!node || !node.attributes.mailbox)
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

							GO.request({								
								maskEl: Ext.getBody(),
								url: "email/folder/rename",
								params: {
									account_id: node.attributes.account_id,
									mailbox: node.attributes.mailbox,
									name: text
								},
								success: function(options, response, result)
								{
									this.treePanel.refresh(node.parentNode);
									
										//remove preloaded children otherwise it won't request the server
//										delete node.parentNode.attributes.children;
//
//										var updateFolderName = function(){
//											var node = this.treePanel.getNodeById('folder_'+this.folder_id);
//											if(node){
//												if(this.folder_id==node.attributes.folder_id){
//													this.mailbox = node.attributes.mailbox;
//													this.treePanel.getSelectionModel().select(node);
//												}
//											}
//											this.el.unmask();
//										}
//										node.parentNode.reload(updateFolderName.createDelegate(this));
										
									
								},
								fail : function(){
								this.treePanel.refresh();
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

				Ext.MessageBox.confirm(GO.lang['strConfirm'], t.applyTemplate({name:node.attributes.text}), function(btn){
					if(btn=='yes')
					{
						GO.request({
							maskEl:GO.mainLayout.getModulePanel("email").getEl(),
							url: "email/folder/truncate",
							params:{
								account_id: node.attributes.account_id,
								mailbox: node.attributes.mailbox
							},
							success:function(){
								if(node.attributes.mailbox==GO.mainLayout.getModulePanel("email").messagesGrid.store.baseParams.mailbox)
								{
									GO.mainLayout.getModulePanel("email").messagesGrid.store.removeAll();
									GO.mainLayout.getModulePanel("email").messagePanel.reset();
								}
								GO.mainLayout.getModulePanel("email").updateFolderStatus(node.attributes.mailbox, 0);
//								GO.mainLayout.getModulePanel("email").updateNotificationEl();
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
						url: GO.url("email/folder/delete"),
						params: {					
							account_id:node.attributes.account_id,
							mailbox: node.attributes.mailbox
						},
						callback: function(responseParams)
						{
							if(responseParams.success)
							{
								node.remove();

								if(node.attributes.mailbox==GO.mainLayout.getModulePanel("email").messagesGrid.store.baseParams.mailbox){
									GO.mainLayout.getModulePanel("email").messagesGrid.store.removeAll();
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
