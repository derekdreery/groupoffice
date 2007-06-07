email = function(){
	var layout;
	var previewPanel;
	var grid;
	var ds;
	var messagesPanel;



	return {

		init : function(){



			// initialize state manager, we will use cookies
			Ext.state.Manager.setProvider(new Ext.state.CookieProvider());

			layout = new Ext.BorderLayout(document.body, {
				west: {
					titlebar: true,
					autoScroll:true,
					closeOnTab: true,
					initialSize: 200,
					split:true
				},
				center: {
					titlebar: true,
					autoScroll:true,
					closeOnTab: true,
					split:true
				},
				east: {
					titlebar: false,
					autoScroll:true,
					closeOnTab: true,
					split:true,
					initialSize: 350,
					minSize: 205,
					maxSize: 700
				}
			});



			layout.beginUpdate();


			function renderMessage(value, p, record){
				if(record.data['new'])
				{
					return String.format('<p style="margin:0px;margin-bottom:4px;font-weight:bold;">{0}</p>{1}', value, record.data['subject']);
				}else
				{
					return String.format('<p style="margin:0px;margin-bottom:4px;">{0}</p>{1}', value, record.data['subject']);
				}
			}
			function renderIcon(src){
				return '<img src=\"' + src +' \" />';
			}

			ds = new Ext.data.Store({

				proxy: new Ext.data.HttpProxy({
					url: BaseHref+'modules/email/messages_json.php'
				}),
				baseParams: {"node": ''},

				reader: new Ext.data.JsonReader({
					root: 'results',
					totalProperty: 'total',
					id: 'uid'
				}, [
				{name: 'uid'},
				{name: 'icon'},
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
				width:28,
				dataIndex: 'icon',
				renderer: renderIcon
			},{
				header: "Message",
				dataIndex: 'from',
				renderer: renderMessage,
				css: 'white-space:normal;',
				width:300
			},{
				header: "Date",
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


			grid.addListener("rowdblclick", this.rowDoubleClicked, this);
			grid.addListener("rowclick", this.rowClicked, this);

			// render it
			grid.render();

			var gridFoot = grid.getView().getFooterPanel(true);

			// add a paging toolbar to the grid's footer
			var paging = new Ext.PagingToolbar(gridFoot, ds, {
				pageSize: parseInt(GOsettings['max_rows_list']),
				displayInfo: true,
				displayMsg: 'Displaying messages {0} - {1} of {2}',
				emptyMsg: "No messages to display"
			});

			// trigger the data store load
			ds.load({params:{start:0, limit: parseInt(GOsettings['max_rows_list'])}});


			var tb = new Ext.Toolbar('emailtb');
			tb.add(new Ext.Toolbar.Button({
				id: 'delete',
				icon: GOimages['delete'],
				text: GOlang['cmdDelete'],
				cls: 'x-btn-text-icon',
				handler: this.onButtonClick
			})
			);

			tb.add(new Ext.Toolbar.Button({
				id: 'link',
				icon: GOimages['link'],
				text: GOlang['cmdLink'],
				cls: 'x-btn-text-icon',
				handler: this.onButtonClick
			})
			);

			var Tree = Ext.tree;

			var tree = new Tree.TreePanel('email-tree', {
				ddGroup : 'TreeDD',
				animate:true,
				loader: new Tree.TreeLoader({dataUrl:'tree_json.php'}),
				enableDrop:true,
				dropConfig : {
				    appendOnly:true
				},
				containerScroll: true
			});

			// set the root node
			var root = new Tree.AsyncTreeNode({
				text: 'Accounts',
				draggable:false,
				id:'source'
			});
			tree.setRootNode(root);


			tree.on('beforenodedrop', function(e){
				var s = e.data.selections, r = [];
				for(var i = 0, len = s.length; i < len; i++){
					
					
				}
				e.dropNode = r;  // return the new nodes to the Tree DD
				e.cancel = r.length < 1; // cancel if all nodes were duplicates
			});
			
			tree.on('nodedrop', function(e){
				alert(e);
			});


			//tree.on('click',nodeClick);

			// render the tree
			tree.render();
			root.expand();

			var treePanel = new Ext.ContentPanel('west',{title: 'E-mail'});
			layout.add('west', treePanel);

			messagesPanel = new Ext.GridPanel(grid, {title: emailLang['messages'], toolbar: tb});
			layout.add('center', messagesPanel);

			previewPanel = new Ext.ContentPanel('east');
			layout.add('east', previewPanel);

			layout.restoreState();
			layout.endUpdate();
		},

		getDataSource : function()
		{
			return ds;
		},


		onButtonClick : function(btn){
			switch(btn.id)
			{
				case 'link':
				var selectionModel = grid.getSelectionModel();
				var records = selectionModel.getSelections();

				parent.GroupOffice.showLinks({ 'url': '../../search.html', 'records': records});
				break;
				case 'delete':
				var selectedRows = grid.selModel.selections.keys;

				if(selectedRows.length)
				{

					var conn = new Ext.data.Connection();
					conn.request({
						url: 'action.php',
						params: {task: 'delete', selectedRows: Ext.encode(selectedRows)},
						callback: function(options, success, response)
						{
							if(!success)
							{
								Ext.MessageBox.alert('Failed', response.result.errors);
							}else
							{
								ds.reload();
							}
						},
						scope: email
					});
				}
				break;

				case 'add':
				/*var conn = new Ext.data.Connection();
				conn.request({
				url: 'action.php',
				params: {task: 'add'},
				callback: function(options, success, response)
				{
				if(!success)
				{
				Ext.MessageBox.alert('Failed', response.result.errors);
				}else
				{
				var reponseParams = Ext.util.JSON.decode(response.responseText);
				//email_form.load({url : 'email_json.php?email_id='+reponseParams['email_id']});
				email_id=reponseParams['email_id'];
				email_form.findField('name').focus(true);
				this.toggleForm(true);
				ds.reload();
				}
				},
				scope: email
				});*/
				Ext.get('dialog').load({url: 'email.php?email_id=0', scripts: true });
				break;


			}
		},
		loadMessages : function(node)
		{
			messagesPanel.setTitle(node.text);
			ds.baseParams = {"node": node.id};
			ds.load({params:{start:0, limit: parseInt(GOsettings['max_rows_list'])}});

		},
		rowDoubleClicked : function(grid, rowClicked, e) {
			var selectionModel = grid.getSelectionModel();
			var record = selectionModel.getSelected();

			//showDialog('dialog', {url: 'email.php?email_id='+record.data['id']});
			Ext.get('dialog').load({url: 'email.php?email_id='+record.data['id'], scripts: true });
		},
		rowClicked : function(grid, rowClicked, e) {

			var selectionModel = grid.getSelectionModel();
			var record = selectionModel.getSelected();
			previewPanel.load({url: 'message.php?uid='+record.data['uid'], scripts: true });
		}
	};

}();
Ext.EventManager.onDocumentReady(email.init, email, true);

