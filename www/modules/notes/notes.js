Notes = function(){
	var layout;
	var previewPanel;
	var grid;
	var ds;
	var note_id;
	var note_form;
	var save_button;


	return {

		init : function(){

			// initialize state manager, we will use cookies
			Ext.state.Manager.setProvider(new Ext.state.CookieProvider());

			layout = new Ext.BorderLayout(document.body, {
				east: {
					split:true,
					initialSize: 400,
					autoScroll:true,
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
			
			
			
			
			

	        
	        	    
		    
		  


			note_form = new Ext.form.Form({
			        labelWidth: 75, // label settings here cascade unless overridden
			        url:'save-form.php',
			        
			        reader: new Ext.data.JsonReader({
						root: 'note',
						id: 'id'
						}, [
						{name: 'name', mapping: 'name'},
						{name: 'content', mapping: 'content'}						
						])
		    });
		    note_form.add(
		        new Ext.form.TextField({
		            fieldLabel: 'Name',
		            name: 'name',
		            allowBlank:false,
		            style:'width:100%'
		        }),
		
		        new Ext.form.TextArea({
		            fieldLabel: 'Text',
		            name: 'content',
		            style:'width:100%;height:400px'
		        })			
		        
		    );
		
			note_form.render('noteform');	
		    
		    
		    var notetb = new Ext.Toolbar('notetb');
		    
		    save_button =notetb.addButton({
				id: 'save',
				icon: GOimages['save'],
				text: GOlang['cmdSave'],					
				cls: 'x-btn-text-icon',
				handler: this.onButtonClick
			}
			);
			
						
			
			previewPanel = new Ext.ContentPanel('noteproperties', 
			{
				title: NotesLang['note'], 
				toolbar: notetb, 
				resizeEl: 'noteform', 
 				autoScroll:true, 
 				fitToFrame:true });
			layout.add('east', previewPanel);
			
			
			
			//Section for links
			
			links_ds = new Ext.data.Store({

				proxy: new Ext.data.HttpProxy({
					url: '../../links_json.php'
				}),

				reader: new Ext.data.JsonReader({
					root: 'results',
					totalProperty: 'total',
					id: 'link_id'
				}, [
				{name: 'link_id', mapping: 'link_id'},
				{name: 'name', mapping: 'name'},
				{name: 'mtime', mapping: 'mtime'}
				]),

				// turn on remote sorting
				remoteSort: true
			});
			links_ds.setDefaultSort('mtime', 'desc');
			
			
			
			// the column model has information about grid columns
			// dataIndex maps the column to the specific data field in
			// the data store
			var links_cm = new Ext.grid.ColumnModel([{
				header: "Name",
				dataIndex: 'name',
				css: 'white-space:normal;'
			},{
				header: "Modified at",
				dataIndex: 'mtime'
			}]);

			// by default columns are sortable
			links_cm.defaultSortable = true;

			// create the editor grid
			var links_grid = new Ext.grid.Grid('links', {
				ds: links_ds,
				cm: links_cm,
				selModel: new Ext.grid.RowSelectionModel(),
				enableColLock:false,
				loadMask: true
			});

			//grid.addListener("rowclick", this.rowClicked, this);
			//grid.addListener("rowdblclick", this.rowDoubleClicked, this);


			// render it
			links_grid.render();

			var linksGridFoot = links_grid.getView().getFooterPanel(true);

			// add a paging toolbar to the grid's footer
			var links_paging = new Ext.PagingToolbar(linksGridFoot, links_ds, {
				pageSize: GOsettings['max_rows_list'],
				displayInfo: true,
				displayMsg: 'Displaying notes {0} - {1} of {2}',
				emptyMsg: "No topics to display"
			});

			// trigger the data store load
			links_ds.load({params:{start:0, limit: GOsettings['max_rows_list']}});
			
			
			
			

			var linksPanel = new Ext.GridPanel(links_grid, 
			{
				title: 'Links', 
			});
			layout.add('east', linksPanel);
			
			layout.getRegion('east').showPanel('noteproperties');

			//Ext.QuickTips.init();



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

			grid.addListener("rowclick", this.rowClicked, this);
			//grid.addListener("rowdblclick", this.rowDoubleClicked, this);


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
			//layout.add('center', new Ext.ContentPanel('no-center', {title: NotesLang['notes'], toolbar: tb}));
			
			this.toggleForm(false);

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
						callback: function(options, success, response)
						{
							if(!success)
							{
								Ext.MessageBox.alert('Failed', response.result.errors);
							}else
							{
								note_form.load('notes_json.php?note_id=0');
								ds.reload();
							}
						},
						scope: Notes
					});
				}
				break;

				case 'add':
				var conn = new Ext.data.Connection();
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
							note_form.load({url : 'notes_json.php?note_id='+reponseParams['note_id']});
							note_id=reponseParams['note_id'];
							note_form.findField('name').focus(true);
							this.toggleForm(true);
							ds.reload();
						}
					},
					scope: Notes
				});
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
		
		toggleForm : function(enabled)
		{
			if(enabled)
			{
				save_button.enable();
			}else
			{
				save_button.disable();
			}

			if(enabled)
			{
				note_form.findField('name').enable();
				note_form.findField('content').enable();
			}else
			{
				note_form.findField('name').disable();
				note_form.findField('name').setRawValue('');
				note_form.findField('content').disable();
				note_form.findField('content').setRawValue('');
			}
			
		},

		rowClicked : function(grid, rowClicked, e) {
			
			
			var selectionModel = grid.getSelectionModel();
			var record = selectionModel.getSelected();
			
			if(note_id!=record.data['id'])
			{		
				note_id=record.data['id'];	
				note_form.load({url: 'notes_json.php?note_id='+record.data['id'], waitMsg:'Loading...'});
				this.toggleForm(true);
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

