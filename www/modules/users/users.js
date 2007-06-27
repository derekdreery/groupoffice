Users = function(){
	var layout;

	return {

		init : function(){

			// initialize state manager, we will use cookies
			Ext.state.Manager.setProvider(new Ext.state.CookieProvider());

			layout = new Ext.BorderLayout(document.body, {
				center: {

					titlebar: false,
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
					user.showDialog(0);					
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



			user.showDialog(record.data['id']);

		}
	};

}();




user = function(){

	var linksPanel;
	var dialog;

	var user_form;

	var layout;

	var loaded_user_id=0;
	var loaded_link_id=0;
	var linkButton;
	var moduleBase;

	return {
		
		

		init : function(){
			
			moduleBase = BaseHref+'modules/users/';

			dialog = new Ext.LayoutDialog('userdialog', {
				modal:true,
				shadow:false,
				resizable:true,
				proxyDrag: true,
				width:700,
				height:550,
				collapsible:false,
				center: {
					autoScroll:true,
					tabPosition: 'top',
					closeOnTab: true,
					alwaysShowTabs: true
				}

			});
			dialog.addKeyListener(27, dialog.hide, this);


			layout = dialog.getLayout();
			
			

		},
		createTabs : function()
		{
			layout.beginUpdate();

			if(!layout.findPanel('properties'))
			{
				var usertb = new Ext.Toolbar('toolbar');
				
				linkButton = usertb.addButton({
					id: 'link',
					icon: GOimages['link'],
					text: GOlang['cmdLink'],
					cls: 'x-btn-text-icon',
					handler: function(){
						var fromlinks = [];
						fromlinks.push({ 'link_id' : loaded_link_id, 'link_type' : 8 });

						parent.GroupOffice.showLinks({ 'fromlinks': fromlinks, 'callback': function(){links_ds.load()}});

					}
				}
				);
				linkButton.disable();

				


				userPanel = new Ext.ContentPanel('properties',{
					title: GOlang['strProperties'],
					autoScroll:true,
					toolbar: usertb,
					resizeEl: 'profileContent',
					fitToFrame:true,
					background: true
				});

				
				userPanel.on('activate',
				function() {
					userPanel.resizeEl.load({
						scripts: true,
						url: moduleBase+'profile.php',						
						params: {
							user_id: loaded_user_id
						}

					});
				});
				layout.add('center', userPanel);
				
			}

			if(loaded_user_id>0 && !layout.findPanel('access'))
			{

				linksPanel = links.getGridPanel('linkstoolbar','links_grid_div');
				layout.add('center', linksPanel);
				linksPanel.on('activate', function() {

					user.destroyDialogButtons();
					var dialog = user.getDialog();

					dialog.addButton('Close', dialog.hide, dialog);
				});


				linksPanel.on('activate',function() {

					links.loadLinks(loaded_link_id, 8);

				});
				
	

				var permissionsPanel = new Ext.ContentPanel('access',
				{
					title: 'Permissions',
					autoScroll:true
				});

				layout.add('center', permissionsPanel);
				permissionsPanel.on('activate',
				function() {

					permissionsPanel.load({
						scripts: true,
						url: moduleBase+'permissions.php',
						params: {
							user_id: loaded_user_id
						}

					});

				});

				var lookAndFeelPanel = new Ext.ContentPanel('lookandfeel',
				{
					title: 'Look and feel',
					autoScroll:true,
					background: true,
					url:{
						scripts: true,
						url: moduleBase+'look_and_feel.php',
						params: {
							user_id: loaded_user_id
						}
					}
				});
				layout.add('center', lookAndFeelPanel);


				var regionalPanel = new Ext.ContentPanel('regional',
				{
					title: 'Regional settings',
					autoScroll:true,
					background: true,
					url:{
						scripts: true,
						url: moduleBase+'regional.php',
						params: {
							user_id: loaded_user_id
						}
					}
				});
				
			
				
				
				layout.add('center', regionalPanel);
				linkButton.enable();
			}
			var region = layout.getRegion('center');
			var activePanel = region.getActivePanel();
			if(activePanel && activePanel.getId()=='properties')
			{
				activePanel.fireEvent('activate');
			}else
			{
				region.showPanel('properties');
			}

			layout.endUpdate();
		},
		removePanels : function()
		{
			var region = layout.getRegion('center');
			
			var panels = [];
			for (var i = 1;i<region.panels.items.length;i++)
			{				
				panels.push(region.panels.items[i].getId());
			}
			for (var i = 0;i<panels.length;i++)
			{				
				region.remove(panels[i],true);
			}
			if(typeof(linkButton)!='undefined')
			{
				linkButton.disable();
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
		setUserID : function(user_id)
		{
			if(loaded_user_id>0 && user_id!=loaded_user_id)
			{
				if(user_id==0)
				{
					this.removePanels();
				}				
			}
			
			
			
			loaded_user_id=user_id;
			
			this.createTabs();
			
			/*if(loaded_user_id==0 && user_id==0)
			{
				userPanel.resizeEl.load({
					scripts: true,
					url: 'profile.php',						
					params: {
						user_id: user_id,
						uniqid: '<?php echo $uniqid; ?>'
					}

				});
			}*/
		},

		showDialog : function(user_id, link_id){
			
			
			
			this.setUserID(user_id);
			
			//user_form.load({url: 'users_json.php?user_id='+user_id, waitMsg:'Loading...'});
			dialog.show();

		},
		setLinkID : function(link_id)
		{
			loaded_link_id=link_id;
		}
	}
}();


Ext.EventManager.onDocumentReady(Users.init, Users, true);
Ext.EventManager.onDocumentReady(user.init, Users, true);

//for the Group-Office search function
function showSearchResult(record)
{
	user.showDialog(record.data['id']);
}

