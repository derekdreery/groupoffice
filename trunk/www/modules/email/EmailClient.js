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
	
	var messagesGridConfig = {};

	if(screen.width>1024)
	{
		messagesGridConfig.region = 'west';
		messagesGridConfig.width=420;
	}else
	{
		messagesGridConfig.region = 'north';
		messagesGridConfig.height=250;
	}
	
	messagesGridConfig.id='email-grid-panel-'+messagesGridConfig.region;
	
	this.messagesGrid = new GO.email.MessagesGrid(messagesGridConfig);

	this.messagesGrid.store.on('load',function(){
		
		var unseen = this.messagesGrid.store.reader.jsonData.unseen;
		for(var folder_id in unseen)
			this.updateFolderStatus(folder_id,unseen[folder_id]);
		
		if(this.messagesGrid.store.baseParams['query'] && this.messagesGrid.store.baseParams['query']!=''){
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
	}, this);	

	this.messagesGrid.on("rowdblclick", function(){		
		if(this.messagesGrid.store.reader.jsonData.drafts)
		{
			GO.email.Composer.show({
				uid: this.previewedUid, 
				task: 'opendraft',
				template_id: 0,
				mailbox: this.mailbox,
				account_id: this.account_id
			});
		}else
		{	
			this.messagesGrid.collapse();
		}
	}, this);

	this.messagesGrid.on('collapse', function(){
		this.closeMessageButton.setVisible(true);
	}, this);
	
	this.messagesGrid.on('expand', function(){
		this.closeMessageButton.setVisible(false);
	}, this);	

	
	this.messagesGrid.on("delayedrowselect",function(grid, rowIndex, r){
		if(r.data['uid']!=this.previewedUid)
		{
			this.previewedUid=r.data['uid'];			
			this.messagePanel.loadMessage(r.data.uid, this.mailbox, this.account_id);			
		}
	}, this);


	var gridContextMenu = new Ext.menu.Menu({
		shadow: "frame",
		minWidth: 180,
		items: [
		{ 
			text: GO.email.lang.markAsRead, 
			handler: function(){
					this.doTaskOnMessages('mark_as_read');
				},
			scope:this
			
		},
		{ 
			text: GO.email.lang.markAsUnread, 
			handler: function(){
					this.doTaskOnMessages('mark_as_unread');
				},
			scope: this
		},
		{ 
			text: GO.email.lang.flag, 
			handler: function(){
				this.doTaskOnMessages('flag');
			},
			scope: this
		},
		{ 
			text: GO.email.lang.unflag, 
			handler: function(){
				this.doTaskOnMessages('unflag');
			},
			scope: this
		},
		'-',
		{
			text: GO.email.lang.viewSource,
			handler: function(){
				
				var record = this.messagesGrid.selModel.getSelected();
				if(record)
				{				
					var win = window.open(GO.settings.modules.email.url+'source.php?account_id='+this.account_id+'&mailbox='+escape(this.mailbox)+'&uid='+escape(record.data.uid));
					win.focus();
				}
				
			},
			scope: this
		},'-',
		{
			iconCls: 'btn-delete',
			text: GO.lang.cmdDelete,
			cls: 'x-btn-text-icon',
			handler: this.deleteMessages,
			scope: this
		}
		]
	});


	this.messagesGrid.on("rowcontextmenu", function(grid, rowIndex, e) {
		var coords = e.getXY();
		gridContextMenu.showAt([coords[0], coords[1]]);
	},this);

	this.treePanel = new GO.email.AccountsTree({
		id:'email-tree-panel',
		region:'west'
	});

	// set the root node
	var root = new Ext.tree.AsyncTreeNode({
		text: GO.email.lang.accounts,
		draggable:false
	});
	this.treePanel.setRootNode(root);
	
	
	this.treeContextMenu = new Ext.menu.Menu({		
		
		items: [{
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
										//remove preloaded children otherwise it won't request the server
										delete node.attributes.children;
										node.reload();
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
		},'-',
		{
			iconCls: 'btn-delete', 
			text: GO.email.lang.emptyFolder, 
			handler: function(){
				
				var sm = this.treePanel.getSelectionModel();		 
		 		var node = sm.getSelectedNode();
					
				var t = new Ext.Template(GO.email.lang.emptyFolderConfirm);
				
				Ext.MessageBox.confirm(GO.lang['strConfirm'], t.applyTemplate(node.attributes), function(btn){
					if(btn=='yes')
					{
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
								}
								this.updateFolderStatus(node.attributes.folder_id);
								this.updateNotificationEl();
							},
						scope: this
						});
					}
				}, this);
			},
			scope:this			
		},{
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
    }]
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
		
		this.treeContextMenu.showAt([coords[0], coords[1]]);
	}, this);
	


	this.treePanel.on('beforenodedrop', function(e){
		var s = e.data.selections, messages = [];

		for(var i = 0, len = s.length; i < len; i++){

			if(this.account_id != e.target.attributes['account_id'])
			{
				Ext.MessageBox.alert(GO.lang['strError'], GO.email.lang['cross_account_move']);
				return false
			}else if(this.mailbox == e.target.mailbox)
			{
				return false;
			}else{
				messages.push(s[i].id);
			}
		}

		if(messages.length>0)
		{			
			this.messagesGrid.store.baseParams['action']='move';
			this.messagesGrid.store.baseParams['from_mailbox']=this.mailbox;
			this.messagesGrid.store.baseParams['to_mailbox']=e.target.attributes['mailbox'];
			this.messagesGrid.store.baseParams['messages']=Ext.encode(messages);
			
			this.messagesGrid.store.reload();
	
			delete this.messagesGrid.store.baseParams['action'];
			delete this.messagesGrid.store.baseParams['from_mailbox'];
			delete this.messagesGrid.store.baseParams['to_mailbox'];
			delete this.messagesGrid.store.baseParams['messages'];	
		}
	},
	this);
	
	//select the first inbox to be displayed in the messages grid
	root.on('load', function(node)
	{		
		this.body.unmask();
		if(node.childNodes[0])
		{
			var firstAccountNode = node.childNodes[0];
			
			this.updateNotificationEl();
			
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
					this.checkMail.defer(this.checkMailInterval, this);
				}
			},this, {single: true});
			
			
		}
	}, this);
	
	this.treePanel.on('beforeclick', function(node){
		if(node.attributes.folder_id==0)
			return false;
	}, this);

	this.treePanel.on('click', function(node)	{		
		if(node.attributes.folder_id>0)
		{		
			var usage=false;
			var cnode = node;
			while(cnode.parentNode && usage===false)
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
	
	this.searchDialog = new GO.email.SearchDialog({store:this.messagesGrid.store});
	
	var tbar =[{
					iconCls: 'btn-compose',
					text: GO.email.lang['compose'],
					cls: 'x-btn-text-icon',
					handler: function(){
						
						GO.email.Composer.show({
							account_id: this.account_id
						});
					},
					scope: this
				},{
					iconCls: 'btn-delete',
					text: GO.lang.cmdDelete,
					cls: 'x-btn-text-icon',
					handler: this.deleteMessages,
					scope: this
				},new Ext.Toolbar.Separator(),
				{
					iconCls: 'btn-accounts',
					text: GO.email.lang.accounts,
					cls: 'x-btn-text-icon',
					handler: function(){
						this.showAccountsDialog();
					},
					scope: this
				},{					
					iconCls: 'btn-refresh',
					text: GO.lang.cmdRefresh,
					cls: 'x-btn-text-icon',
					handler: function(){
						this.refresh();
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
						this.messagesGrid.store.baseParams['query']='';	
						this.messagesGrid.store.load({params:{start:0}});							
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
						
						GO.email.Composer.show({
							uid: this.previewedUid, 
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
						GO.email.Composer.show({
							uid: this.previewedUid, 
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
						GO.email.Composer.show({
							uid: this.previewedUid, 
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
						var popup = window.open('about:blank');
        		if (!popup.opener) popup.opener = self;        		
        		
        		popup.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">\n'+
							'<html>\n'+
							'<head>\n'+
							'<title>Group-Office</title>'+							
							'<link href="'+GO.settings.config.theme_url+'images/favicon.ico" rel="shotcut icon" />'+
							'<link href="'+BaseHref+'ext/resources/css/ext-all.css" type="text/css" rel="stylesheet" />'+
							'<link href="'+GO.settings.config.theme_url+'style.css" type="text/css" rel="stylesheet" />'+
							'<link href="'+GO.settings.modules.email.url+'themes/'+GO.settings.theme+'/style.css" type="text/css" rel="stylesheet" />'+
							'</head><body>'+this.messagePanel.body.dom.innerHTML+'</body></html>');
						popup.document.close();
						popup.focus();						
												
					},
					scope: this
				})];
				
				
				if(GO.mailings)
				{
					tbar.push({
					iconCls: 'btn-link',
					text: GO.lang.cmdLink,
					cls: 'x-btn-text-icon',
					handler: function(){
						
						if(!this.messagesGrid.selModel.selections.keys.length)
						{
							Ext.MessageBox.alert(GO.lang['strError'], GO.lang['noItemSelected']);
						}else
						{
							
							this.linksDialog = new GO.email.LinksDialog({
								messagesGrid : this.messagesGrid							
							});
							
							this.linksDialog.show();
						}
					},
					scope: this
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
				
				
	config.items=[
		this.treePanel,
		{
      region:'center',
      titlebar: false,
      layout:'border',														
			items: [
				this.messagesGrid,
				this.messagePanel = new GO.email.MessagePanel({
					id:'email-message-panel',
					region:'center',
					autoScroll:true,
					titlebar: false,
					border:true
				})
			]
  	}];
  	
  this.messagePanel.on('load', function(options, success, response){
  	if(!success)
		{
			this.previewedUid=0;
		}else
		{
			//this.previewedUid=record.data['uid'];
			
			this.replyAllButton.setDisabled(false);
			this.replyButton.setDisabled(false);
			this.forwardButton.setDisabled(false);
			this.printButton.setDisabled(false);
			
			var record = this.messagesGrid.store.getById(this.previewedUid);			
			if(record.data['new']==1)
			{		
				this.incrementFolderStatus(this.folder_id, -1);
				record.set('new','0');									
			}
		}		
  	
  }, this);
  	
  
  this.messagePanel.on('linkClicked', function(href){
  	var win = window.open(href);
  	win.focus();
  }, this);
  
  this.messagePanel.on('attachmentClicked', this.openAttachment, this);
  this.messagePanel.on('zipOfAttachmentsClicked', this.openZipOfAttachments, this);
  
  
  this.messagePanel.on('emailClicked', function(email){
  	this.showComposer({to: email});
  }, this);
  
  /*
   * for email seaching on sender from message panel
   */
  GO.email.searchSender=function(sender)
	{
		if(this.rendered)
		{
			this.messagesGrid.store.baseParams.query='FROM "'+sender+'"';
			this.messagesGrid.store.load({params:{start:0}});
			
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
	checkMailInterval : 300000,
	//checkMailInterval : 10000,
	
	justMarkedUnread : 0, 
	
	deleteMessages : function(){ 
		this.messagesGrid.deleteSelected({
			callback : function(config){
				var keys = Ext.decode(config.params.delete_keys);				
				for(var i=0;i<keys.length;i++)
				{
					if(this.previewedUid==keys[i])
					{
						this.messagePanel.reset();
					}
				}
			},
			scope: this
		}); 
	},
	
	afterRender : function(){
		GO.email.Composer.on('send', function(composer){			
			if(composer.sendParams.reply_uid && composer.sendParams.reply_uid>0)
			{
				var record = this.messagesGrid.store.getById(composer.sendParams.reply_uid);
				if(record)
				{
					record.set('answered',true);
				}
			}
		}, this);
		
		GO.email.EmailClient.superclass.afterRender.call(this);		
		
		this.body.mask(GO.lang.waitMsgLoad);
		
		//create notify icon
		
		var notificationArea = Ext.get('notification-area');		
		if(notificationArea)
		{
			this.notificationEl = notificationArea.createChild({
				id: 'ml-notify',
				tag:'div',
				html:'',
				style:'display:none'				
			});
		}
	},
	
	onShow : function(){
		
		if(this.notificationEl){
			this.notificationEl.setDisplayed(false);
		}
		
		GO.email.EmailClient.superclass.onShow.call(this);	
	},	
	
	updateNotificationEl : function(){
		
		if(this.notificationEl)
		{
			var node = this.treePanel.getRootNode();
			
	
			var inbox_new=0;
			for(var i=0;i<node.childNodes.length;i++)
			{			
				inbox_new += node.childNodes[i].attributes.inbox_new;			
			}
			
			var current = this.notificationEl.dom.innerHTML;
			
			if(current!='' && inbox_new-this.justMarkedUnread>current)
			{
				GO.playAlarm();
				this.notificationEl.setDisplayed(!this.isVisible());
			}
			
			this.notificationEl.update(inbox_new);
			
			this.justMarkedUnread=0;
		}
	},
	
	openAttachment :  function(attachment, panel)
	{
		
		if(attachment.mime.indexOf('message')>-1)
  	{
  		GO.linkHandlers[9].call(this, 0, {
  			uid: this.previewedUid, 
  			mailbox: this.mailbox, 
  			part: attachment.number,
  			transfer: this.transfer,
  			mime: attachment.mime,
  			account_id: this.account_id
  			});	
  	}else
  	{	
			switch(attachment.extension)
			{
				
				case 'dat':
					document.location.href=GO.settings.modules.email.url+
						'tnef.php?account_id='+this.account_id+
						'&mailbox='+escape(this.mailbox)+
						'&uid='+this.previewedUid+
						'&part='+attachment.number+
						'&transfer='+attachment.transfer+
						'&mime='+attachment.mime+
						'&filename='+escape(attachment.name);
				break;
				
				case 'bmp':
				case 'png':
				case 'gif':
				case 'jpg':
				case 'jpeg':
				
				if(GO.files)
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
									src: GO.settings.modules.email.url+
									'attachment.php?account_id='+this.account_id+
									'&mailbox='+escape(this.mailbox)+
									'&uid='+this.previewedUid+
									'&part='+r.number+
									'&transfer='+r.transfer+
									'&mime='+r.mime+
									'&filename='+escape(r.name)
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
					document.location.href=GO.settings.modules.email.url+
						'attachment.php?account_id='+this.account_id+
						'&mailbox='+escape(this.mailbox)+
						'&uid='+this.previewedUid+
						'&part='+attachment.number+
						'&transfer='+attachment.transfer+
						'&mime='+attachment.mime+
						'&filename='+escape(attachment.name);
					break;
			}
  	}
	},
	
	openZipOfAttachments : function()
	{
		document.location.href=GO.settings.modules.email.url+
		'zip_attachments.php?account_id='+this.account_id+
		'&mailbox='+escape(this.mailbox)+
		'&uid='+this.previewedUid;	
	},
	
	showComposer : function(values)
	{
		GO.email.Composer.show(
		{
			account_id: this.account_id,
			values : values
		});
	},
	
	setAccount : function(account_id,folder_id,mailbox, usage)
	{
		if(account_id!=this.account_id)
		{
			this.messagePanel.reset();
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
		this.messagesGrid.store.load({params:{start:0}});
		//this.messagesGrid.store.load();
		
		this.treePanel.setUsage(usage);
	},
	/**
	 * Returns true if the current folder needs to be refreshed in the grid
	 */
	updateFolderStatus : function(folder_id, unseen)
	{
		var statusEl = Ext.get('status_'+folder_id);
		
		var isGridFolder = false;
		
		var node = this.treePanel.getNodeById('folder_'+folder_id);
		if(node && node.attributes.mailbox=='INBOX')
		{
			node.parentNode.attributes.inbox_new=unseen;
		}
		
		if(statusEl && statusEl.dom)
		{
			var statusText = statusEl.dom.innerHTML;
			var current = parseInt(statusText.substring(1, statusText.length-1));
			
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
	
	
	checkMail : function(){		
		Ext.Ajax.request({
			url: GO.settings.modules.email.url+'action.php',
			params: {
				task: 'check_mail'
			},
			callback: function(options, success, response)
			{
				if(success)
				{
					var responseParams = Ext.decode(response.responseText);
					
					for(var folder_id in responseParams.status)
					{
						var changed = this.updateFolderStatus(folder_id, responseParams.status[folder_id].unseen);
						if(changed && this.messagesGrid.store.baseParams.folder_id==folder_id)
						{
							this.messagesGrid.store.reload();
						}
					}					
					this.updateNotificationEl();
					
					this.checkMail.defer(this.checkMailInterval, this);
				}
			},
			scope: this
		});	
	},


	refresh : function()
	{		
		this.treePanel.loader.baseParams.refresh=true;
		this.treePanel.root.reload();
		delete this.treePanel.loader.baseParams.refresh;
	},

	showAccountsDialog : function()
	{
		if(!this.accountsDialog)
		{
			this.accountsDialog = new GO.email.AccountsDialog();
			this.accountsDialog.accountsGrid.store.on('load',function(){
				this.accountsDialog.accountsGrid.store.on('load', this.refresh, this);
			}, this);
			
			this.accountsDialog.accountsGrid.store.load();				
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
									
									this.justMarkedUnread=-records.length;									
									break;
								case 'mark_as_unread':
									field='new';
									value=true;
									
									this.justMarkedUnread=records.length;									
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
	}
	
});

GO.mainLayout.onReady(function(){
	GO.email.Composer = new GO.email.EmailComposer();
});


GO.moduleManager.addModule('email', GO.email.EmailClient, {
	title : GO.lang.strEmail,
	iconCls : 'go-tab-icon-email'
});



GO.linkHandlers[9] = function(id, remoteMessage){
	
	
	
	var messagePanel = new GO.email.MessagePanel({
			border:false,
			autoScroll:true
		});
		
		
	messagePanel.on('linkClicked', function(href){
  	var win = window.open(href);
  	win.focus();
  }, this);
  
  messagePanel.on('attachmentClicked', function(attachment, panel){
  	
  	if(attachment.mime.indexOf('message')>-1)
  	{
  		remoteMessage.part_number=attachment.number+".0";
  		GO.linkHandlers[9].call(this, id, remoteMessage);
  	}else
  	{
	  	if(panel.data.path)
	  	{
	  		document.location.href=GO.settings.modules.email.url+
	  			'mimepart.php?path='+
	  			escape(panel.data.path)+'&part_number='+attachment.number;
	  	}else
	  	{
	  		document.location.href=GO.settings.modules.email.url+
	  			'mimepart.php?uid='+remoteMessage.uid+'' +
	  			'&account_id='+remoteMessage.account_id+'' +
	  			'&transfer='+attachment.transfer+'' +
	  			'&mailbox='+escape(remoteMessage.mailbox)+'' +
	  			'&part='+remoteMessage.part+'' +
	  			'&part_number='+attachment.number;
	  	}
  	}
  	
  	
  }, this);
  messagePanel.on('zipOfAttachmentsClicked', function(){}, this);
  
  messagePanel.on('emailClicked', function(email){
  	GO.email.Composer.show({ 
  		values : {to: email} 
  		});
  }, this);
	
	var win = new Ext.Window({
			maximizable:true,
			collapsible:true,
			title: GO.email.lang.emailMessage,
			height: 400,
			width: 600,
			layout:'fit',
			items: messagePanel,			
			buttons:[{
				text:GO.lang.cmdClose,
				handler:function(){
					win.close();
				}
			}]
		});
		
	
	win.show();
	messagePanel.el.mask(GO.lang.strWaitMsgLoad);
	
/*	
	if(id>0)
	{
		var params = {
				id: id,
				task:'linked_message'
			};
	}else
	{
		var params = {
				uid: remoteMessage.uid,
				mailbox: remoteMessage.mailbox,
				account_id: remoteMessage.account_id,
				part: remoteMessage.part,
				transfer: remoteMessage.transfer,
				task:'linked_message'
			}
	}*/
	
	if(!remoteMessage)
		remoteMessage={};
	
	remoteMessage.id=id;
	
	var url = '';
	if(remoteMessage.account_id)
	{
		remoteMessage.task='message_attachment';
		url = GO.settings.modules.email.url+'json.php';
	}else
	{
		remoteMessage.task='linked_message';
		url = GO.settings.modules.mailings.url+'json.php';
	}
	

	Ext.Ajax.request({
			url: url,
			params: remoteMessage,
			scope: this,
			callback: function(options, success, response)
			{
				var data = Ext.decode(response.responseText);				
				messagePanel.setMessage(data);
				
				messagePanel.el.unmask();				
			}
		});
		
}