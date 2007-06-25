Users = function(){
	var layout;

	return {

		init : function(){

			// initialize state manager, we will use cookies
			Ext.state.Manager.setProvider(new Ext.state.CookieProvider());

			layout = new Ext.BorderLayout(document.body, {
				center: {

					titlebar: true,
					autoScroll:true,
					closeOnTab: true
				}
			});



			layout.beginUpdate();




			ds = new Ext.data.Store({

				proxy: new Ext.data.HttpProxy({
					url: 'users_json.php'
				}),

				reader: new Ext.data.JsonReader({
					root: 'results',
					totalProperty: 'total',
					id: 'id'
				}, [
				{name: 'id'},
				{name: 'link_id'},
				{name: 'link_type'},
				{name: 'name'},
				{name: 'email'}
				]),

				// turn on remote sorting
				remoteSort: true
			});
			ds.setDefaultSort('name', 'asc');



			// the column model has information about grid columns
			// dataIndex maps the column to the specific data field in
			// the data store
			var cm = new Ext.grid.ColumnModel([{
				header: GOlang['strName'],
				dataIndex: 'name',
				css: 'white-space:normal;'
			},{
				header: GOlang['strEmail'],
				dataIndex: 'email'
			}]);

			// by default columns are sortable
			cm.defaultSortable = true;

			// create the editor grid
			grid = new Ext.grid.Grid('grid', {
				ds: ds,
				cm: cm,
				selModel: new Ext.grid.RowSelectionModel(),
				enableColLock:false,
				loadMask: true
			});


			grid.addListener("rowdblclick", this.rowDoubleClicked, this);



			// render it
			grid.render();

			ds.on('load', function (){grid.getView().autoSizeColumns();});



			var gridFoot = grid.getView().getFooterPanel(true);

			// add a paging toolbar to the grid's footer
			var paging = new Ext.PagingToolbar(gridFoot, ds, {
				pageSize: parseInt(GOsettings['max_rows_list']),
				displayInfo: true,
				displayMsg: GOlang['displayingItems'],
				emptyMsg: GOlang['strNoItems']
			});

			// trigger the data store load
			ds.load({params:{start:0, limit: parseInt(GOsettings['max_rows_list'])}});





			var tb = new Ext.Toolbar('toolbar');
			tb.add(new Ext.Toolbar.Button({
				id: 'delete',
				icon: GOimages['delete'],
				text: GOlang['cmdDelete'],
				cls: 'x-btn-text-icon',
				handler: function(){
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
							}
						});
					}
				}
			})
			);

			tb.add(new Ext.Toolbar.Button({
				id: 'add',
				icon: GOimages['add'],
				text: GOlang['cmdAdd'],
				cls: 'x-btn-text-icon',
				handler: function(){
					
					if(typeof(user)!='undefined')
					{
					user.showDialog(0);
					}else
					{
					Ext.get('dialogloader').load({url: 'user.php?user_id=0', scripts: true });
					}
				}
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




			layout.add('center', new Ext.GridPanel(grid, {title: UsersLang['users'], toolbar: tb}));


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
			}
		},




		rowDoubleClicked : function(grid, rowClicked, e) {
			var selectionModel = grid.getSelectionModel();
			var record = selectionModel.getSelected();


		
			if(typeof(user)!='undefined')
			{
			user.showDialog(record.data['id']);
			}else
			{
			Ext.get('dialogloader').load({url: 'user.php?user_id='+record.data['id'], scripts: true });
			}
		}
	};

}();
Ext.EventManager.onDocumentReady(Users.init, Users, true);


function showSearchResult(record)
{
	if(typeof(user)!='undefined')
	{
		user.showDialog(record.data['id']);
	}else
	{
		Ext.get('dialogloader').load({url: BaseHref+'modules/users/user.php?user_id='+record.data['id'], scripts: true });
	}
}

