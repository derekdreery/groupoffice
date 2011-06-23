/**
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 * @author Boy Wijnmaalen <bwijnmaalen@intermesh.nl>
 */

GO.modules.MainPanel = function(config) {
	if (!config) {
		config = {};
	}

	this.installedModulesDS = new GO.data.JsonStore({
		url : GO.settings.modules.modules.url + 'json.php',
		baseParams : {
			task : 'installed_modules'
		},
		root : 'results',
		id : 'id',
		fields : ['name', 'description', 'id', 'sort_order',
		'admin_menu', 'acl_id'],
		remoteSort : true
	});

	this.availableModulesDS = new GO.data.JsonStore({
		url : GO.settings.modules.modules.url + 'json.php',
		baseParams : {
			task : 'available_modules'
		},
		sortInfo: {
			field: 'name',
			direction: 'ASC' // or 'DESC' (case sensitive for local sorting)
		},
		root : 'results',
		id : 'id',
		fields : ['name', 'description', 'id', 'sort_order',
		'admin_menu', 'acl_id']
	});

	config.tbar = new Ext.Toolbar({
		cls : 'go-head-tb',
		items : [{
			iconCls : 'btn-add',
			text : GO.modules.lang['cmdInstall'],
			cls : 'x-btn-text-icon',
			handler : this.showavailableModules,
			scope : this
		}, {
			iconCls : 'btn-delete',
			text : GO.modules.lang['cmdUninstall'],
			cls : 'x-btn-text-icon',
			handler : this.uninstallModule,
			scope : this
		}, {
			iconCls : 'btn-permissions',
			text : GO.lang.strPermissions,
			cls : 'x-btn-text-icon',
			handler : this.showPermissions,
			scope : this
		}]
	});

	config.cm = new Ext.grid.ColumnModel([{
		header : GO.lang['strName'],
		dataIndex : 'name',
		id:'name',
		renderer : this.iconRenderer,
		width:250
	}]);

	config.view=new Ext.grid.GridView({
		enableRowBody:true,
		showPreview:true,
		autoFill:true,
		emptyText:GO.lang.strNoItems,
		getRowClass : function(record, rowIndex, p, store){
			if(this.showPreview && record.data.description.length){
				p.body = '<div class="mo-description">'+record.data.description+'</div>';
				return 'x-grid3-row-expanded';
			}
			return 'x-grid3-row-collapsed';
		}
	});

	config.ddGroup = 'ModulesGridDD';

	config.enableDragDrop = true;

	config.layout = 'fit';
	config.store = this.installedModulesDS;
	config.sm = new Ext.grid.RowSelectionModel({
		singleSelect : false
	});
	config.paging = false;

	GO.modules.MainPanel.superclass.constructor.call(this, config);

	this.on("rowdblclick", this.showPermissions, this);

};

Ext.extend(GO.modules.MainPanel, GO.grid.GridPanel, {

	afterRender : function() {

		GO.modules.MainPanel.superclass.afterRender.call(this);

		var notifyDrop = function(dd, e, data) {
			var sm = this.getSelectionModel();
			var rows = sm.getSelections();
			var cindex = dd.getDragData(e).rowIndex;
			if (cindex == 'undefined') {
				cindex = this.store.data.length - 1;
			}
			for (i = 0; i < rows.length; i++) {
				var rowData = this.store.getById(rows[i].id);

				if (!this.copy) {
					this.store.remove(this.store.getById(rows[i].id));
				}

				this.store.insert(cindex, rowData);
			};

			this.save();

		};

		var ddrow = new Ext.dd.DropTarget(this.getView().mainBody, {
			ddGroup : 'ModulesGridDD',
			copy : false,
			notifyDrop : notifyDrop.createDelegate(this)
		});

		this.store.load();

	},

	showavailableModules : function() {

		if (!this.availableModulesWin) {
			this.availableModulesDS.load();

			var grid = new GO.grid.GridPanel({
				layout : 'fit',
				store : this.availableModulesDS,
				border:false,
				sm : new Ext.grid.RowSelectionModel({
					singleSelect : false
				}),
				view:new Ext.grid.GridView({
					enableRowBody:true,
					showPreview:true,
					autoFill:true,
					emptyText:GO.lang.strNoItems,
					getRowClass : function(record, rowIndex, p, store){
						if(this.showPreview && record.data.description.length){
							p.body = '<div class="mo-description">'+record.data.description+'</div>';
							return 'x-grid3-row-expanded';
						}
						return 'x-grid3-row-collapsed';
					}
				}),

				columns : [{
					header : GO.lang['strName'],
					dataIndex : 'name',
					renderer: this.iconRenderer
				}],
				paging : false
                
			});

			this.availableModulesWin = new Ext.Window({
				layout : 'fit',
				modal : false,
				shadow : false,
				minWidth : 300,
				minHeight : 300,
				height : 400,
				width : 600,
				plain : true,
				closeAction : 'hide',
				title : GO.modules.lang['cmdAvailableModules'],
				items : grid,
				buttons : [{
					text : GO.modules.lang['cmdInstall'],
					handler : function() {
						this.installModule(grid);
					},
					scope : this
				}, {
					text : GO.lang['cmdClose'],
					handler : function() {
						this.availableModulesWin.hide();
					},
					scope : this
				}]
			});
		}
		this.availableModulesWin.show();

	},

	save : function() {
		var modules = new Array();

		for (var i = 0; i < this.installedModulesDS.data.items.length; i++) {
			modules[i] = {
				id : this.installedModulesDS.data.items[i].get('id'),
				sort_order : i,
				admin_menu : this.installedModulesDS.data.items[i].get('admin_menu')
			};
		}

		this.container.mask(GO.lang.waitMsgLoad, 'x-mask-loading');
		Ext.Ajax.request({
			url : GO.settings.modules.modules.url + 'action.php',
			params : {
				task : 'update',
				modules : Ext.encode(modules)
			},
			callback : function(options, success, response) {
				this.container.unmask();
			},
			scope : this
		});
	},

	installModule : function(grid) {
		grid.container.mask(GO.lang['waitMsgLoad']);

		var selectionModel = grid.getSelectionModel();
		var records = selectionModel.getSelections();
		
		var keys = [];
		for(var i=0;i<records.length;i++)
		{
			keys.push(records[i].data.id);
		}

		if (records.length > 0) {
			Ext.Ajax.request({
				url : GO.settings.modules.modules.url + 'action.php',
				params : {
					task : 'install',
					modules : keys.join(',')
				},
				callback : function(options, success, response) {
					grid.container.unmask();
					grid.store.reload();
					this.store.reload();
					this.availableModulesWin.hide();
				},
				scope : this
			});
		}
	},

	uninstallModule : function() {

		var uninstallModules = Ext.encode(this.selModel.selections.keys);

		switch (this.selModel.selections.keys.length) {
			case 0 :
				Ext.MessageBox.alert(GO.lang['strError'],
					GO.lang['noItemSelected']);
				return false;
				break;

			case 1 :
				var strConfirm = GO.lang['strDeleteSelectedItem'];
				break;

			default :
				var t = new Ext.Template(GO.lang['strDeleteSelectedItems']);
				var strConfirm = t.applyTemplate({
					'count' : this.selModel.selections.keys.length
				});
				break;
		}

		Ext.MessageBox.confirm(GO.lang['strConfirm'], strConfirm,
			function(btn) {
				if (btn == 'yes') {
					this.store.baseParams.uninstall_modules = uninstallModules;

					this.store.reload({
						callback : function() {
							if (!this.store.reader.jsonData.uninstallSuccess) {
								Ext.MessageBox
								.alert(
									GO.lang['strError'],
									this.store.reader.jsonData.uninstallFeedback);
							}

							this.availableModulesDS.reload();
						},
						scope : this
					});

					delete this.store.baseParams.uninstall_modules;
				}
			}, this);

	},

	showPermissions : function() {
		var selectionModel = this.getSelectionModel();
		var record = selectionModel.getSelections();

		if (record.length > 0) {
			if (!this.permissionsWin) {
				this.readPermissionsTab = new GO.grid.PermissionsPanel({
					title : GO.users.lang.useModule
				});

                

				this.permissionsWin = new Ext.Window({
					title : GO.lang['strPermissions'],
					layout : 'fit',
					modal : false,
					height : 500,
					width : 400,
					closeAction:'hide',
					items : [this.readPermissionsTab],
					buttons : [{
						text : GO.lang['cmdClose'],
						handler : function() {
							this.permissionsWin.hide()
						},
						scope : this
					}]
				});
			}

			this.permissionsWin.show();
			this.permissionsWin.setTitle(GO.lang['strPermissions'] + ' '
				+ record[0].data.name);
							
			this.readPermissionsTab.setAcl(record[0].data.acl_id);
		}
	},

	iconRenderer : function(name, cell, record) {
		return '<div class="mo-title" style="background-image:url('+BaseHref+'modules/'+record.data.id+'/themes/Default/images/'+record.data.id+'.png)">'
		+ name + '</div>';
	}
});

GO.moduleManager.addModule('modules', GO.modules.MainPanel, {
	title : GO.modules.lang.modules,
	iconCls : 'go-tab-icon-modules',
	admin :true
});