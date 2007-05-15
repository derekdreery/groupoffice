Notes = function(){
	var layout;
	var previewPanel;
	var grid;
	var ds;
	var preview_id;


	return {

		init : function(){

			// initialize state manager, we will use cookies
			Ext.state.Manager.setProvider(new Ext.state.CookieProvider());

			layout = new Ext.BorderLayout('container', {
				east: {
					split:true,
					initialSize: 300,
					minSize: 200,
					maxSize: 500,
					autoScroll:false,
					collapsible:false,
					titlebar: true,
					animate: false
				},
				center: {

					titlebar: true,
					autoScroll:true,
					closeOnTab: true
				}
			});
			
			
			
			layout.beginUpdate();
		    
		    
		  


			var note_form = new Ext.form.Form({
			        labelWidth: 75, // label settings here cascade unless overridden
			        url:'save-form.php'
		    });
		    note_form.add(
		        new Ext.form.TextField({
		            fieldLabel: 'Name',
		            name: 'name',
		            allowBlank:false
		        }),
		
		        new Ext.form.TextArea({
		            fieldLabel: 'Text',
		            name: 'content'
		        })			
		        
		    );
		
		
		    
		    
		    var notetb = new Ext.Toolbar('notetb');
			notetb.add(new Ext.Toolbar.Button({
				id: 'save',
				icon: GOimages['save'],
				text: GOlang['cmdSave'],					
				cls: 'x-btn-text-icon',
				handler: this.onButtonClick
			})
			);
		
			note_form.render('noteform');	
			
			previewPanel = new Ext.ContentPanel('no-east', {title: NotesLang['note'], toolbar: notetb, fitToFrame:true, reziseEl: 'noteform'});
			layout.add('east', previewPanel);




			Ext.QuickTips.init();



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
				width: 420,
				css: 'white-space:normal;'
			},{
				header: "Modified at",
				dataIndex: 'mtime'
			}]);

			// by default columns are sortable
			cm.defaultSortable = true;

			// create the editor grid
			grid = new Ext.grid.Grid('no-center', {
				ds: ds,
				cm: cm,
				selModel: new Ext.grid.RowSelectionModel(),
				enableColLock:false,
				loadMask: true
			});

			grid.addListener("rowclick", this.rowClicked, this);
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




			layout.add('center', new Ext.GridPanel(grid, {title: NotesLang['notes'], toolbar: tb}));

			//layout.getRegion('east').collapse();
			layout.endUpdate();
		},

		onButtonClick : function(btn){
			switch(btn.id)
			{
				case 'delete':
				var selectedRows = grid.selModel.selections.keys;

				if(selectedRows.length)
				{

					var conn = new Ext.data.Connection();
					conn.request({
						url: 'action.php',
						params: {task: 'delete', selectedRows: Ext.encode(selectedRows)},
						callback: Notes.handleDeleteResponse,
						scope: Notes
					});
				}
				break;

				case 'add':
				document.location='note.php?return_to='+document.location;
				break;

				case 'save':
				var frm = new Ext.BasicForm(Ext.get('note_form'), {});
				var bSuccessful = false;

				frm.submit({
					url:'./action.php',

					success:function(form, action){
						alert('Succes');
					},

					failure: function(form, action) {
						Ext.MessageBox.alert('Failed', 'Search Failed');
					}
				});
				break;
			}
		},

		handleDeleteResponse : function(options, success, response)
		{

			if(!success)
			{
				alert('Failed to connect to the server!');
			}else
			{
				var GOresponse=Ext.util.JSON.decode(response['responseText']);

				if(GOresponse['success']!='true')
				{
					alert(GOresponse['message']);
				}

				//var east = layout.getRegion('east');
				//east.collapse();

				ds.reload();
			}
		},

		rowClicked : function(grid, rowClicked, e) {
			
			
			var selectionModel = grid.getSelectionModel();
			var record = selectionModel.getSelected();
			
			if(preview_id!=record.data['id'])
			{
	
						
				
				
			}

		},
		
		rowDoubleClicked : function(grid, rowClicked, e) {
			var selectionModel = grid.getSelectionModel();
			var record = selectionModel.getSelected();

			var east = layout.getRegion('east');
			
			document.location='note.php?note_id='+record.data['id']+'&return_to='+escape(document.location);

		}
	};

}();
Ext.EventManager.onDocumentReady(Notes.init, Notes, true);

