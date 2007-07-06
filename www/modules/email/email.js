email = function(){

	var account_id;
	var folder_id;
	var mailbox;

	var layout;
	var previewPanel;
	var grid;
	var ds;
	var messagesPanel;
	var previewedUid;

	var btnForward;
	var btnReply;
	var btnReplyAll;
	var btnCloseMessage;
	var btnAccounts;

	var accountsDialog;
	var tree;




	return {

		init : function(account_id,folder_id,mailbox){



			// initialize state manager, we will use cookies
			Ext.state.Manager.setProvider(new Ext.state.CookieProvider());

			layout = new Ext.BorderLayout(document.body, {
				north: {
					initialSize:30,
					resizable:false,
					split: false,
					titlebar: false,
					collapsible: false
				},
				west: {
					titlebar: true,
					autoScroll:true,
					closeOnTab: true,
					initialSize: 200,
					split:true
				},
				center: {
					titlebar: false,
					autoScroll:true,
					closeOnTab: true,
					split:true
				}
			});

			var innerLayout = new Ext.BorderLayout('inner-layout', {
				west: {
					split:true,
					initialSize: 450,
					minSize: 200,
					autoScroll:true,
					collapsible:false,
					titlebar: true
				},
				center: {
					autoScroll:true,
					titlebar: false
				}
			});



			layout.beginUpdate();
			innerLayout.beginUpdate();


			function renderMessage(value, p, record){
				if(record.data['new'])
				{
					return String.format('<p id="sbj_'+record.data['uid']+'" class="NewSubject">{0}</p>{1}', value, record.data['subject']);
				}else
				{
					return String.format('<p id="sbj_'+record.data['uid']+'" class="Subject">{0}</p>{1}', value, record.data['subject']);
				}
			}
			function renderIcon(src){
				return '<img src=\"' + src +' \" />';
			}

			function renderFlagged(value, p, record){

				var str = '';

				if(record.data['flagged']==1)
				{
					str += '<img src=\"' + GOimages['flag'] +' \" style="display:block" />';
				}
				if(record.data['attachments'])
				{
					str += '<img src=\"' + GOimages['attach'] +' \" style="display:block" />';
				}
				return str;

			}

			ds = new Ext.data.Store({

				proxy: new Ext.data.HttpProxy({
					url: BaseHref+'modules/email/json.php'
				}),
				baseParams: {
				"node": '',
				"type": 'messages'
				}
				,

				reader: new Ext.data.JsonReader({
					root: 'results',
					totalProperty: 'total',
					id: 'uid'
				}, [
				{name: 'uid'},
				{name: 'icon'},
				{name: 'flagged'},
				{name: 'attachments'},
				{name: 'new'},
				{name: 'subject'},
				{name: 'from'},
				{name: 'size'},
				{name: 'date'}

				]),

				// turn on remote sorting
				remoteSort: true
			});
			ds.setDefaultSort('utime', 'asc');

			ds.on('loadexception', loadexception);

			function loadexception(param1, param2, response)
			{
				var reponseParams = Ext.util.JSON.decode(response.responseText);

				Ext.MessageBox.alert('Failed', reponseParams['errors']);

			}

			// the column model has information about grid columns
			// dataIndex maps the column to the specific data field in
			// the data store
			var cm = new Ext.grid.ColumnModel([
			{
				header:"",
				width:22,
				dataIndex: 'icon',
				renderer: renderIcon
			},{
				header:"",
				width:22,
				dataIndex: 'flagged',
				renderer: renderFlagged
			},{
				header: emailLang['Message'],
				dataIndex: 'from',
				renderer: renderMessage,
				css: 'white-space:normal;',
				width:300
			},{
				header: GOlang['strDate'],
				dataIndex: 'date',
				width:100
			}]);

			// by default columns are sortable
			cm.defaultSortable = true;

			// create the editor grid
			grid = new Ext.grid.Grid('email-grid', {
				ds: ds,
				cm: cm,
				selModel: new Ext.grid.RowSelectionModel(),
				enableColLock:false,
				loadMask: true,
				enableDragDrop: true,
				ddGroup : 'TreeDD'
			});


			grid.addListener("rowdblclick", function(){
				innerLayout.getRegion('west').hide();
				layout.getRegion('west').hide();
				btnCloseMessage.show();
			});



			grid.addListener("rowclick", function(grid, rowClicked, e) {
				var selectionModel = grid.getSelectionModel();
				var record = selectionModel.getSelected();

				if(record.data['new']==1)
				{
					//update subject as read
					var sbj = Ext.get('sbj_'+record.data['uid']);
					sbj.set({'class': 'Subject'});

					//decrease treeview status id is defined in tree_json.php
					var status = Ext.get('status_'+this.folder_id);

					var unseen = parseInt(status.dom.innerHTML)-1;
					status.dom.innerHTML = unseen;
				}

				if(record.data['uid']!=previewedUid)
				{
					//load message
					previewPanel.load(
					{
						url: 'message.php',
						params: {
							uid: record.data['uid'],
							mailbox: this.mailbox
						},
						scripts: true
					});

					previewedUid=record.data['uid'];

					btnForward.enable();
					btnReply.enable();
					btnReplyAll.enable();
				}
			}, this);



			var gridContextMenu = new Ext.menu.Menu({
				shadow: "frame",
				minWidth: 180,
				id: 'ContextMenu',
				items: [
				{ text: emailLang['mark_as_read'], handler: function(){
					email.doTaskOnMessages('mark_as_read');
				}
				},
				{ text: emailLang['mark_as_unread'], handler: function(){
					email.doTaskOnMessages('mark_as_unread');
				}
				},
				{ text: emailLang['flag'], handler: function(){
					email.doTaskOnMessages('flag');
				}
				},
				{ text: emailLang['unflag'], handler: function(){
					email.doTaskOnMessages('unflag');
				}
				}
				]
			});


			grid.addListener("rowcontextmenu", function(grid, rowIndex, e) {
				e.stopEvent();
				var coords = e.getXY();
				gridContextMenu.showAt([coords[0], coords[1]]);
			},
			this
			);



			// render it
			grid.render();

			var gridFoot = grid.getView().getFooterPanel(true);

			// add a paging toolbar to the grid's footer
			var paging = new Ext.PagingToolbar(gridFoot, ds, {
				pageSize: parseInt(GOsettings['max_rows_list']),
				displayInfo: true,
				displayMsg: GOlang['displayingItems'],
				emptyMsg: GOlang['strNoItems']
			});

			// trigger the data store load
			//ds.load({params:{start:0, limit: parseInt(GOsettings['max_rows_list'])}});


			var tb = new Ext.Toolbar('emailtb');


			tb.add(new Ext.Toolbar.Button({
				id: 'compose',
				icon: GOimages['compose'],
				text: emailLang['compose'],
				cls: 'x-btn-text-icon',
				handler: function(){this.composer();},
				scope: this
			})
			);

			tb.add(new Ext.Toolbar.Button({
				id: 'delete',
				icon: GOimages['delete'],
				text: GOlang['cmdDelete'],
				cls: 'x-btn-text-icon',
				handler: function(){ this.doTaskOnMessages('delete') },
				scope: this
			})
			);

			tb.add(new Ext.Toolbar.Button({
				id: 'link',
				icon: GOimages['link'],
				text: GOlang['cmdLink'],
				cls: 'x-btn-text-icon',
				handler: this.onButtonClick,
				scope: this
			})
			);

			tb.add(new Ext.Toolbar.Separator());


			btnReply = tb.addButton({
				id: 'reply',
				icon: GOimages['reply'],
				text: emailLang['reply'],
				cls: 'x-btn-text-icon',
				handler: function(){
					this.composer('reply','','','', previewedUid);
				},
				scope: this
			}
			);

			btnReplyAll = tb.addButton({
				id: 'reply_all',
				icon: GOimages['reply_all'],
				text: emailLang['reply_all'],
				cls: 'x-btn-text-icon',
				handler: function(){
					this.composer('reply_all','','','', previewedUid);
				},
				scope: this
			}
			);

			btnForward = tb.addButton({
				id: 'forward',
				icon: GOimages['forward'],
				text: emailLang['forward'],
				cls: 'x-btn-text-icon',
				handler: function(){
					this.composer('forward','','','', previewedUid);
				},
				scope: this
			}
			);
			tb.add(new Ext.Toolbar.Separator());



			btnAccounts = tb.addButton({
				id: 'accounts',
				icon: GOimages['accounts'],
				text: emailLang['accounts'],
				cls: 'x-btn-text-icon',
				handler: function(){
					this.showAccountsDialog();
				},
				scope: this
			}
			);

			tb.addButton({
				id: 'refresh',
				icon: GOimages['refresh'],
				text: emailLang['refresh'],
				cls: 'x-btn-text-icon',
				handler: function(){
					this.refresh();
				},
				scope: this
			}
			);



			btnCloseMessage = tb.addButton({
				id: 'close',
				icon: GOimages['close'],
				text: GOlang['cmdClose'],
				cls: 'x-btn-text-icon',
				handler: function(){
					innerLayout.getRegion('west').show();
					layout.getRegion('west').show();
					btnCloseMessage.hide();
				},
				scope: this
			}
			);

			btnCloseMessage.hide();
			btnForward.disable();
			btnReply.disable();
			btnReplyAll.disable();



			var Tree = Ext.tree;

			tree = new Tree.TreePanel('email-tree', {
				ddGroup : 'TreeDD',
				animate:true,
				loader: new Tree.TreeLoader(
				{
					dataUrl:'json.php',
					baseParams:{type: 'tree'}

				}),
				enableDrop:true,
				dropConfig : {
					appendOnly:true
				},
				containerScroll: true
			});

			// set the root node
			var root = new Tree.AsyncTreeNode({
				text: emailLang['accounts'],
				draggable:false,
				id:'source'
			});
			tree.setRootNode(root);


			tree.on('beforenodedrop', function(e){
				var s = e.data.selections, messages = [];

				for(var i = 0, len = s.length; i < len; i++){

					if(this.account_id != e.target.attributes['account_id'])
					{
						Ext.MessageBox.alert(GOlang['strError'], emailLang['cross_account_move']);
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
					var conn = new Ext.data.Connection();
					conn.request({
						url: 'action.php',
						params: {
							task: 'move',
							from_mailbox: this.mailbox,
							to_mailbox: e.target.attributes['mailbox'],
							account_id: e.target.attributes['account_id'],
							messages: Ext.encode(messages)
						},
						callback: function(options, success, response)
						{
							if(!success)
							{
								Ext.MessageBox.alert(GOlang['strError'], response.result.errors);
							}else
							{
								ds.reload();
							}
						}
					});
				}


			},
			this);



			tree.on('click', function(node)	{

				this.setAccount(
				node.attributes.account_id,
				node.attributes.folder_id,
				node.attributes.mailbox
				);

			}, this);



			var toolbarPanel = new Ext.ContentPanel('north',{toolbar: tb});
			layout.add('north', toolbarPanel);

			var treePanel = new Ext.ContentPanel('west',{title: 'E-mail'});
			layout.add('west', treePanel);

			messagesPanel = new Ext.GridPanel(grid, {title: emailLang['inbox']});
			innerLayout.add('west', messagesPanel);

			previewPanel = new Ext.ContentPanel('preview');
			innerLayout.add('center', previewPanel);

			layout.add('center', new Ext.NestedLayoutPanel(innerLayout));


			this.setAccount(account_id, folder_id, mailbox);

			layout.restoreState();
			layout.endUpdate();

			innerLayout.restoreState();
			innerLayout.endUpdate();





			// render the tree has to be done after grid loads. Don't know why but otherwise
			// it doesn't load.
			tree.render();
			root.expand();
		},

		refresh : function()
		{
			//sync folders
			var conn = new Ext.data.Connection();
			conn.request({
				url: 'action.php',
				params: {
					task: 'syncfolders',
					account_id: this.account_id
				},
				callback: function(options, success, response)
				{
					if(!success)
					{
						Ext.MessageBox.alert(GOlang['strError'], response.result.errors);
					}else
					{
						//ds.reload();
						tree.root.reload();
					}
				},
				scope: email
			});


		},

		showAccountsDialog : function()
		{
			if(!accountsDialog){
				accountsDialog = new Ext.LayoutDialog("accounts-dialog", {
					modal:true,
					shadow:false,
					minWidth:300,
					minHeight:300,
					height:400,
					width:600,
					proxyDrag: true,
					collapsible: false,
					center: {
						autoScroll:true,
						tabPosition: 'top',
						closeOnTab: true,
						titlebar: false
					}
				});
				accountsDialog.addKeyListener(27, accountsDialog.hide, accountsDialog);

				accountsDialog.addButton(GOlang['cmdClose'],accountsDialog.hide, accountsDialog);

				var accountsLayout = accountsDialog.getLayout();
				accountsLayout.beginUpdate();




				var accountsDS = new Ext.data.Store({

					proxy: new Ext.data.HttpProxy({
						url: BaseHref+'modules/email/json.php'
					}),
					baseParams: {
					"type": 'accounts'
					}
					,
					reader: new Ext.data.JsonReader({
						root: 'results',
						totalProperty: 'total',
						id: 'id'
					}, [
					{name: 'id'},
					{name: 'email'},
					{name: 'host'},
					{name: 'standard'}
					]),

					// turn on remote sorting
					remoteSort: false
				});
				accountsDS.on('loadexception',function(param1, param2, response)
				{
					var reponseParams = Ext.util.JSON.decode(response.responseText);
					Ext.MessageBox.alert(GOlang['strError'], reponseParams['errors']);
				});



				// the column model has information about grid columns
				// dataIndex maps the column to the specific data field in
				// the data store
				var cm = new Ext.grid.ColumnModel([
				{
					header:"E-mail",
					dataIndex: 'email'
				},{
					header:"Host",
					dataIndex: 'host'
				},{
					header: 'Standard',
					dataIndex: 'standard'
				}]);

				// by default columns are sortable
				cm.defaultSortable = true;

				// create the editor grid
				var accountsGrid = new Ext.grid.Grid('accounts-grid', {
					ds: accountsDS,
					cm: cm,
					selModel: new Ext.grid.RowSelectionModel(),
					enableColLock:false,
					loadMask: true
				});


				accountsGrid.addListener("rowdblclick", function(grid, rowClicked, e){

					var selectionModel = grid.getSelectionModel();
					var record = selectionModel.getSelected();

					account.showDialog(record.id);
				});


				accountsDS.on('load', function (){accountsGrid.getView().autoSizeColumns();});

				accountsDS.load();
				// render it
				accountsGrid.render();


				var emailtb = new Ext.Toolbar('accounts-toolbar');


				emailtb.addButton({
					id: 'add',
					icon: GOimages['add'],
					text: GOlang['cmdAdd'],
					cls: 'x-btn-text-icon',
					handler: function(){}
				}
				);

				emailtb.addButton({
					id: 'delete',
					icon: GOimages['delete'],
					text: GOlang['cmdDelete'],
					cls: 'x-btn-text-icon',
					handler: function(){}
				}
				);

				var accountsPanel = new Ext.GridPanel(accountsGrid,{
					toolbar: emailtb
				});

				accountsLayout.add('center', accountsPanel);










				accountsLayout.endUpdate();
			}
			accountsDialog.show('accounts');


		},

		setAccount : function(account_id,folder_id,mailbox)
		{
			this.account_id = account_id;
			this.folder_id = folder_id;
			this.mailbox = mailbox;


			messagesPanel.setTitle(mailbox);
			ds.baseParams = {
			"type": 'messages',
			"account_id": account_id,
			"folder_id": folder_id,
			"mailbox": mailbox
			};
			ds.load({params:{start:0, limit: parseInt(GOsettings['max_rows_list'])}});
		},

		getDataSource : function()
		{
			return ds;
		},

		doTaskOnMessages : function (task){
			var selectedRows = grid.selModel.selections.keys;

			var selectionModel = grid.getSelectionModel();
			var records = selectionModel.getSelections();

			if(selectedRows.length)
			{

				var conn = new Ext.data.Connection();
				conn.request({
					url: 'action.php',
					params: {
						task: task,
						messages: Ext.encode(selectedRows),
						account_id: this.account_id,
						mailbox: this.mailbox
					},
					callback: function(options, success, response)
					{
						if(!success)
						{
							Ext.MessageBox.alert(GOlang['strError'], response.result.errors);
						}else
						{
							ds.reload();
						}
					},
					scope: email
				});
			}
		},

		onButtonClick : function(btn){
			switch(btn.id)
			{
				case 'link':
				var selectionModel = grid.getSelectionModel();
				var records = selectionModel.getSelections();



				parent.GroupOffice.showLinks({ 'url': '../../search.html', 'records': records});
				break;


			}
		},

		composer : function(action,mail_to,subject,body, uid)
		{
			if(typeof(mail_to) == "undefined")
			{
				mail_to='';
			}
			if(typeof(subject) == "undefined")
			{
				subject='';
			}
			if(typeof(body) == "undefined")
			{
				body='';
			}
			if(typeof(action) == 'undefined')
			{
				action = '';
			}
			if(typeof(uid) == 'undefined')
			{
				uid = '';
			}

			if(action != '')
			{
				var url = 'send.php?mail_from='+this.account_id+
				'&uid='+uid+'&mailbox='+
				escape(this.mailbox)+'&action='+action;
			}else
			{
				var url = 'send.php?mail_from='+this.account_id;
			}

			url += '&mail_to='+mail_to+'&mail_subject='+subject+'&mail_body='+escape(body);

			var height='580';
			var width='780';
			var centered;

			x = (screen.availWidth - width) / 2;
			y = (screen.availHeight - height) / 2;
			centered =',width=' + width + ',height=' + height + ',left=' + x + ',top=' + y + ',scrollbars=yes,resizable=yes,status=yes';

			var popup = window.open(url, '_blank', centered);
			if (!popup.opener) popup.opener = self;
			popup.focus();
		}
	};

}();


account = function(){

	var linksPanel;
	var dialog;

	var account_form;

	var layout;

	var loaded_account_id=0;
	var loaded_link_id=0;
	var linkButton;
	var moduleBase;

	var foldersTree;

	return {



		init : function(){

			moduleBase = BaseHref+'modules/email/';

			dialog = new Ext.LayoutDialog('account-dialog', {
				modal:true,
				shadow:false,
				resizable:true,
				proxyDrag: true,
				width:500,
				height:500,
				collapsible:false,
				center: {
					autoScroll:true,
					tabPosition: 'top',
					closeOnTab: true,
					alwaysShowTabs: true
				}

			});
			dialog.addKeyListener(27, dialog.hide, dialog);


			layout = dialog.getLayout();


			layout.beginUpdate();




			var accountPanel = new Ext.ContentPanel('properties',{
				title: GOlang['strProperties'],
				autoScroll:true,
				background: true
			});


			accountPanel.on('activate', function() {

				accountPanel.load({
					scripts: true,
					url: moduleBase+'account.php',
					params: {
						account_id: loaded_account_id
					}

				});
			});
			layout.add('center', accountPanel);






			//start of folders panel

			var folderstb = new Ext.Toolbar('folders-toolbar');


			folderstb.addButton({
				id: 'add',
				icon: GOimages['add'],
				text: GOlang['cmdAdd'],
				cls: 'x-btn-text-icon',
				handler: function(){

					var selModel = foldersTree.getSelectionModel();

					if(typeof(selModel.selNode)=='undefined')
					{
						Ext.MessageBox.alert(GOlang['strError'], emailLang['selectFolderAdd']);
					}else
					{

						Ext.MessageBox.prompt('Name', 'Enter the folder name:', function(button, text){

							if(button=='ok')
							{
								var conn = new Ext.data.Connection();
								conn.request({
									url: 'action.php',
									params: {
										task: 'add_folder',
										folder_id: selModel.selNode.attributes.folder_id,
										account_id: selModel.selNode.attributes.account_id,
										new_folder_name: text
									},
									callback: function(options, success, response)
									{
										if(!success)
										{
											Ext.MessageBox.alert(GOlang['strError'], response.result.errors);
										}else
										{
											if(typeof(selModel.selNode.parentNode)=='undefined')
											{
												selModel.selNode.parentNode.reload();
											}else
											{
												foldersTree.getRootNode().reload();
											}
										}
									}
								});
							}

						});
					}
				}
			}
			);

			folderstb.addButton({
				id: 'delete',
				icon: GOimages['delete'],
				text: GOlang['cmdDelete'],
				cls: 'x-btn-text-icon',
				handler: function(){

					var selModel = foldersTree.getSelectionModel();

					if(typeof(selModel.selNode)=='undefined' || selModel.selNode.attributes.folder_id<1)
					{
						Ext.MessageBox.alert(GOlang['strError'], emailLang['selectFolderDelete']);
					}else
					{
						var conn = new Ext.data.Connection();
						conn.request({
							url: 'action.php',
							params: {
								task: 'delete_folder',
								folder_id: selModel.selNode.attributes.folder_id

							},
							callback: function(options, success, response)
							{
								if(!success)
								{
									Ext.MessageBox.alert(GOlang['strError'], response.result.errors);
								}else
								{
									selModel.selNode.parentNode.reload();
								}
							}
						});
					}

				}
			}
			);




			var foldersPanel = new Ext.ContentPanel('folders',{
				title: emailLang['folders'],
				autoScroll:true,
				background: true,
				toolbar: folderstb
			});


			foldersPanel.on('activate', function() {

				if(foldersTree)
				{
					var root = foldersTree.getRootNode();
					if(root.id!='account_'+loaded_account_id)
					{
						root.id='account_'+loaded_account_id;
						root.reload();
					}
				}else
				{
					var Tree = Ext.tree;

					foldersTree = new Tree.TreePanel('folders-tree', {
						ddGroup : 'TreeDD',
						animate:true,
						loader: new Tree.TreeLoader(
						{
							dataUrl:'json.php',
							baseParams:{type: 'tree-edit'}

						}),
						enableDrop:true,
						dropConfig : {
							appendOnly:true
						},
						containerScroll: true
					});

					// set the root node
					var root = new Tree.AsyncTreeNode({
						text: emailLang['root'],
						draggable:false,
						id:'account_'+loaded_account_id,
						folder_id: 0
					});
					foldersTree.setRootNode(root);


					var treeEdit = new Tree.TreeEditor(foldersTree, {
						ignoreNoChange:true
					});

					treeEdit.on('beforestartedit', function(editor, boundEl, value){
						if(editor.editNode.attributes.folder_id==0)
						{
							alert("You can't edit this node!");
							return false;
						}
					});

					treeEdit.on('beforecomplete', function(editor, boundEl, value){

						var conn = new Ext.data.Connection();
						conn.request({
							url: 'action.php',
							params: {
								task: 'rename_folder',
								folder_id: editor.editNode.attributes.folder_id,
								new_name: boundEl
							},
							callback: function(options, success, response)
							{
								if(!success)
								{
									Ext.MessageBox.alert(GOlang['strError'], response.result.errors);
								}else
								{
									return true;
								}
							}
						});

					});





					foldersTree.render();
					root.expand();


				}



			});
			layout.add('center', foldersPanel);



			layout.endUpdate();
		},


		hidePanels : function()
		{
			var region = layout.getRegion('center');
			for (var i = 1;i<region.panels.items.length;i++)
			{
				region.hidePanel(region.panels.items[i].getId());
			}
		},
		showPanels : function()
		{
			var region = layout.getRegion('center');
			for (var i = 1;i<region.panels.items.length;i++)
			{
				region.showPanel(region.panels.items[i].getId());
			}
		},
		getDialog : function()
		{
			return dialog;
		},
		destroyDialogButtons : function()
		{
			if(typeof(dialog.buttons) != 'undefined')
			{
				for (var i = 0;i<dialog.buttons.length;i++)
				{
					dialog.buttons[i].destroy();
				}
			}
		},
		setAccountID : function(account_id)
		{
			if(loaded_account_id>0 && account_id!=loaded_account_id)
			{
				if(account_id==0)
				{
					this.hidePanels();
				}else
				{
					this.showPanels();
				}
			}



			loaded_account_id=account_id;
		},

		showDialog : function(account_id){

			this.setAccountID(account_id);

			var region = layout.getRegion('center');
			var activePanel = region.getActivePanel();
			if(activePanel && activePanel.getId()=='properties')
			{
				activePanel.fireEvent('activate');
			}else
			{
				region.showPanel('properties');
			}
			dialog.show();
		},
	}
}();



function change_port()
{
	if (document.forms['properties-form'].type.value == "imap")
	{
		if(document.forms['properties-form'].use_ssl.checked)
		{
			document.forms['properties-form'].port.value = "993";
		}else
		{
			document.forms['properties-form'].port.value = "143";
		}
		document.forms['properties-form'].mbroot.disabled = false;
	}else
	{
		if(document.forms['properties-form'].use_ssl.checked)
		{
			document.forms['properties-form'].port.value = "995";
		}else
		{
			document.forms['properties-form'].port.value = "110";
		}
		document.forms['properties-form'].mbroot.disabled = true;
	}
}
