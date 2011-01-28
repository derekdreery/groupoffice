Ext.namespace('GO.emailportlet');

GO.mainLayout.onReady(function(){

	if(GO.summary && GO.email)
	{
		this.store = new GO.data.JsonStore({
			url: GO.settings.modules.email.url+'json.php',
			baseParams: {
				task: 'messages'
			},
			root: 'results',
			totalProperty: 'total',
			id: 'uid',
			fields:['uid','icon','flagged','attachments','new','subject','from','sender','size','date', 'priority','answered','forwarded'],
			remoteSort: true
		});

		this.emailTabPanel = new Ext.TabPanel({
			region:'north',
			title:'test',
			border:false
		});

		GO.emailportlet.messagesGrid = new GO.email.MessagesGrid({
			id:'emp-messagesgrid',
			store:this.store,
			hideSearch:true
		});

		GO.emailportlet.messagesGrid.on('rowdblclick', function(grid, rowIndex)
		{
			var record = grid.getStore().getAt(rowIndex);

			if(!GO.emailportlet.messageDialog)
			{
				GO.emailportlet.messageDialog = new GO.email.MessageDialog({});
			}
			
			GO.emailportlet.messageDialog.show(record.id, record.store.baseParams.mailbox, record.store.baseParams.account_id);
		
		}, this);

		GO.summary.portlets['portlet-email'] = new GO.summary.Portlet({
			id: 'portlet-email',
			title: GO.email.lang.email,
			layout:'border',
			tools: [
			{
				id:'close',
				handler: function(e, target, panel)
				{
					panel.removePortlet();
				}
			}],
			items: [
				this.emailTabPanel,
				new Ext.Panel({
					region:'center',
					id:'email-portlet-grid',
					layout:'fit',
					items:GO.emailportlet.messagesGrid
				})
			],
			height:300
		});

		GO.emailportlet.foldersStore = new GO.data.JsonStore({
			url: GO.settings.modules.emailportlet.url+'json.php',
			baseParams: {
				task: 'get_folders'
			},
			root: 'data',
			totalProperty: 'total',
			id: 'fid',
			waitMsg: GO.lang['waitMsgLoad'],
			waitMsgTarget: 'portlet-email',
			fields:['name','account_id','id','title','fid'],
			remoteSort: true
		});

		GO.emailportlet.foldersStore.on('load', function()
		{
			this.emailTabPanel.removeAll(true);

			var data = GO.emailportlet.foldersStore.data;
			GO.emailportlet.messagesGrid.setDisabled(data.length == 0);
			
			if(data.length == 0)
			{
				this.emailTabPanel.add(new Ext.Panel({
					title:GO.emailportlet.lang.noEmailFolders
				}));
			}else
			{
				for(var i=0; i<data.length; i++)
				{
					var folder = data.items[i].data;
					var panel = new Ext.Panel({
						id:'account_'+folder.account_id+':'+folder.name,
						account_id:folder.account_id,
						folder_id:folder.id,
						title:folder.title,
						mailbox:folder.name,
						layout:'fit',
						closable:true
					})
					
					panel.on('show', function(e)
					{
						GO.emailportlet.messagesGrid.store.baseParams.account_id = e.account_id;
						GO.emailportlet.messagesGrid.store.baseParams.folder_id = e.folder_id;
						GO.emailportlet.messagesGrid.store.baseParams.mailbox = e.mailbox;

						GO.emailportlet.messagesGrid.store.load();
					},this);

					panel.on('close', function(e)
					{
						var record = GO.emailportlet.foldersStore.getById(e.id);
						GO.emailportlet.foldersStore.remove(record);
						
						Ext.Ajax.request({
							url: GO.settings.modules.emailportlet.url+'json.php',
							params: {
								folder_id: e.folder_id,
								task: 'hide_folder'
							},
							scope: this,
							callback: function(options, success, response)
							{
								var data = Ext.decode(response.responseText);
								if(!data.success)
								{
									Ext.MessageBox.alert(GO.lang['strError'], GO.lang['strRequestError']);
								}
								
								if(GO.emailportlet.foldersStore.data.items.length==0)
								{
									this.emailTabPanel.add(new Ext.Panel({
										title:GO.emailportlet.lang.noEmailFolders
									}));

									GO.emailportlet.messagesGrid.store.removeAll();
									GO.emailportlet.messagesGrid.disable();

									this.emailTabPanel.setActiveTab(0);
								}
							}
						});
					},this);

					this.emailTabPanel.add(panel);
				}				
			}

			this.emailTabPanel.setActiveTab(0);
			this.emailTabPanel.doLayout();
			
		}, this);

		GO.summary.portlets['portlet-email'].on('render',function()
		{
			GO.emailportlet.foldersStore.load();
		});
	}
});

GO.moduleManager.onModuleReady('email', function(moduleName)
{
	if(GO.summary)
	{
		GO.email.extraTreeContextMenuItems.push('-');
		GO.email.extraTreeContextMenuItems.push({
			iconCls: 'btn-refresh',
			text: GO.emailportlet.lang.showOnSummary,
			cls: 'x-btn-text-icon',
			handler: function()
			{
				var sm = GO.email.treePanel.getSelectionModel();
				var node = sm.getSelectedNode();

				Ext.Ajax.request({
					url: GO.settings.modules.emailportlet.url+'json.php',
					params: {
						folder_id: node.attributes.folder_id,
						task: 'show_folder'
					},
					scope: this,
					callback: function(options, success, response)
					{
						var data = Ext.decode(response.responseText);
						if(!data.success)
						{
							Ext.MessageBox.alert(GO.lang['strError'], GO.lang['strRequestError']);
						}else
						{
							Ext.MessageBox.alert(GO.lang['strSuccess'], GO.emailportlet.lang.folderAdded);

							GO.emailportlet.foldersStore.reload();
						}
					}
				});
			},
			scope: this
		});
	}
});
