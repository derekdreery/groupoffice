Notes = function(){
	var layout;
	var previewPanel;
	var grid;
	var ds;
	var note_id;
	var link_id;
	var note_form;
	var save_button;
	var linksPanel;
var linksGrid;

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
					url: 'notes_json.php'
				}),

				reader: new Ext.data.JsonReader({
					root: 'results',
					totalProperty: 'total',
					id: 'id'
				}, [
				{name: 'id', mapping: 'id'},
				{name: 'link_id', mapping: 'link_id'},				
				{name: 'link_type', mapping: 'link_type'},	
				{name: 'name', mapping: 'name'},
				{name: 'mtime', mapping: 'mtime'}
				]),

				// turn on remote sorting
				remoteSort: true
			});
			ds.setDefaultSort('name', 'asc');



			// the column model has information about grid columns
			// dataIndex maps the column to the specific data field in
			// the data store
			var cm = new Ext.grid.ColumnModel([{
				header: "Name",
				dataIndex: 'name',
				css: 'white-space:normal;'
			},{
				header: "Modified at",
				dataIndex: 'mtime'
			}]);

			// by default columns are sortable
			cm.defaultSortable = true;

			// create the editor grid
			grid = new Ext.grid.Grid('notes-grid', {
				ds: ds,
				cm: cm,
				selModel: new Ext.grid.RowSelectionModel(),
				enableColLock:false,
				loadMask: true
			});

	
			grid.addListener("rowdblclick", this.rowDoubleClicked, this);


			// render it
			grid.render();

			var gridFoot = grid.getView().getFooterPanel(true);

			// add a paging toolbar to the grid's footer
			var paging = new Ext.PagingToolbar(gridFoot, ds, {
				pageSize: GOsettings['max_rows_list'],
				displayInfo: true,
				displayMsg: 'Displaying notes {0} - {1} of {2}',
				emptyMsg: "No topics to display"
			});

			// trigger the data store load
			ds.load({params:{start:0, limit: GOsettings['max_rows_list']}});


			var tb = new Ext.Toolbar('notestb');
			tb.add(new Ext.Toolbar.Button({
				id: 'delete',
				icon: GOimages['delete'],
				text: GOlang['cmdDelete'],
				tooltip: {text:'Delete the selected items', title:'Tip Title'},
				cls: 'x-btn-text-icon',
				handler: this.onButtonClick
			})
			);

			tb.add(new Ext.Toolbar.Button({
				id: 'add',
				icon: GOimages['add'],
				text: GOlang['cmdAdd'],
				tooltip: {text:'Add a new note', title:'Tip Title'},
				cls: 'x-btn-text-icon',
				handler: this.onButtonClick
			})
			);
			
			tb.add(new Ext.Toolbar.Button({
				id: 'link',
				icon: GOimages['link'],
				text: GOlang['cmdLink'],
				tooltip: {text:'Add a new note', title:'Tip Title'},
				cls: 'x-btn-text-icon',
				handler: this.onButtonClick
			})
			);




			layout.add('center', new Ext.GridPanel(grid, {title: NotesLang['notes'], toolbar: tb}));
			

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
						scope: Notes
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
							//note_form.load({url : 'notes_json.php?note_id='+reponseParams['note_id']});
							note_id=reponseParams['note_id'];
							note_form.findField('name').focus(true);							
							this.toggleForm(true);
							ds.reload();
						}
					},
					scope: Notes
				});*/
					Ext.get('dialog').load({url: 'note.php?note_id=0', scripts: true });
				break;

				case 'save':

				note_form.submit({
					url:'./action.php',
					params: {'task' : 'save','note_id' : note_id},

					success:function(form, action){
						//reload grid
						ds.reload();
					},

					failure: function(form, action) {
						Ext.MessageBox.alert('Failed', action.result.errors);
					}
				});
				break;
			}
		},
		


		
		rowDoubleClicked : function(grid, rowClicked, e) {
			var selectionModel = grid.getSelectionModel();
			var record = selectionModel.getSelected();

			//showDialog('dialog', {url: 'note.php?note_id='+record.data['id']});
			Ext.get('dialog').load({url: 'note.php?note_id='+record.data['id'], scripts: true });
		}
	};

}();
Ext.EventManager.onDocumentReady(Notes.init, Notes, true);

