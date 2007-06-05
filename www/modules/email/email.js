email = function(){
	var layout;
	var previewPanel;
	var grid;
	var ds;



	return {

		init : function(){

			// initialize state manager, we will use cookies
			Ext.state.Manager.setProvider(new Ext.state.CookieProvider());

			layout = new Ext.BorderLayout(document.body, {
				center: {

					titlebar: true,
					autoScroll:true,
					closeOnTab: true,
					split:true,
                    initialSize: 400,
                    minSize: 205,
                    maxSize: 700
					
				},
				east: {

					titlebar: true,
					autoScroll:true,
					closeOnTab: true,
					split:true,
                    initialSize: 400,
                    minSize: 205,
                    maxSize: 700
					
					
				}
			});
			
			
			
			layout.beginUpdate();
			
			


			ds = new Ext.data.Store({

				proxy: new Ext.data.HttpProxy({
					url: 'messages_json.php'
				}),

				reader: new Ext.data.JsonReader({
					root: 'results',
					totalProperty: 'total',
					id: 'uid'
				}, [
				{name: 'uid', mapping: 'uid'},
				{name: 'subject'},				
				{name: 'from'},	
				{name: 'size'},
				{name: 'date'}
				]),

				// turn on remote sorting
				remoteSort: true
			});
			ds.setDefaultSort('utime', 'asc');



			// the column model has information about grid columns
			// dataIndex maps the column to the specific data field in
			// the data store
			var cm = new Ext.grid.ColumnModel([{
				header: "From",
				dataIndex: 'from',
				css: 'white-space:normal;'
			},{
				header: "Subject",
				dataIndex: 'subject'
			},{
				header: "Size",
				dataIndex: 'size'
			},{
				header: "Date",
				dataIndex: 'date'
			}]);

			// by default columns are sortable
			cm.defaultSortable = true;

			// create the editor grid
			grid = new Ext.grid.Grid('email-grid', {
				ds: ds,
				cm: cm,
				selModel: new Ext.grid.RowSelectionModel(),
				enableColLock:false,
				loadMask: true
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




			layout.add('center', new Ext.GridPanel(grid, {title: emailLang['messages'], toolbar: tb}));
			
			previewPanel = new Ext.ContentPanel('east', {title: emailLang['message']});
			layout.add('east', previewPanel);
			

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

