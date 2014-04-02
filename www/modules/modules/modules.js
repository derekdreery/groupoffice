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

	this.store = new GO.data.JsonStore({
		url: GO.url('modules/module/store'),
		fields: ['name', 'description', 'id', 'sort_order', 'admin_menu', 'acl_id', 'icon', 'enabled', 'warning', 'buyEnabled'],
		remoteSort: true
	});

	config.tbar = new Ext.Toolbar({
		cls: 'go-head-tb',
		items: [
			{
				xtype: 'htmlcomponent',
				html: GO.modules.lang.name,
				cls: 'go-module-title-tbar'
			}, {
				iconCls: 'btn-refresh',
				text: GO.lang.cmdRefresh,
				cls: 'x-btn-text-icon',
				handler: function() {
					this.store.load();
				},
				scope: this
			}]
	});

	var checkColumn = new GO.grid.CheckColumn({
		header: GO.modules.lang.enabled,
		dataIndex: 'enabled',
		width: 20,
		listeners: {
			scope: this,
			change: function(record, checked) {
				GO.request({
					url: 'modules/module/update',
					params: {
						id: record.id,
						enabled: checked
					},
					scope: this,
					success: function(response, options, result) {

						if (result.acl_id) {
							record.set('acl_id', result.acl_id);

							if (record.data.enabled) {
								this.showPermissions(record.data.id, record.data.name, record.data.acl_id);
							}
						}
						record.commit();
					}
				});
			}
		}
	});

	config.cm = new Ext.grid.ColumnModel([{
			header: GO.lang['strName'],
			dataIndex: 'name',
			id: 'name',
			renderer: this.iconRenderer,
			width: 250
		}, {
//		header:'-',
//		dataIndex : "warning",
//		id:"warning",
//		renderer:this.warningRenderer,
//		width:20
//	},{
			header: '-',
			renderer: this.buyRenderer,
			width: 60
		},
		checkColumn
//	,{
//		header : GO.lang.users,
//		dataIndex:'user_count',
//		width:80,
//		align:'right'
//	}
	]);
	
	config.loadMask=true;

	config.view = new Ext.grid.GridView({
		enableRowBody: true,
		showPreview: true,
		autoFill: true,
		emptyText: GO.lang.strNoItems,
		getRowClass: function(record, rowIndex, p, store) {
			if (this.showPreview && record.data.description.length) {
				p.body = '<div class="mo-description">' + record.data.description + '</div>';
				return 'x-grid3-row-expanded';
			}
			return 'x-grid3-row-collapsed';
		}
	});

//	config.ddGroup = 'ModulesGridDD';
//
//	config.enableDragDrop = true;

	config.layout = 'fit';
	config.sm = new Ext.grid.RowSelectionModel({
		singleSelect: false
	});
	config.paging = false;

	GO.modules.MainPanel.superclass.constructor.call(this, config);

	this.on("rowdblclick", function(grid, rowIndex, event) {
		var moduleRecord = grid.store.getAt(rowIndex);

		if (moduleRecord.data.acl_id) {
			this.showPermissions(moduleRecord.data.id, moduleRecord.data.name, moduleRecord.data.acl_id);
		}
	}, this);

};

Ext.extend(GO.modules.MainPanel, GO.grid.GridPanel, {
	afterRender: function() {

		GO.modules.MainPanel.superclass.afterRender.call(this);
//
//		var notifyDrop = function(dd, e, data) {
//			var sm = this.getSelectionModel();
//			var rows = sm.getSelections();
//			var cindex = dd.getDragData(e).rowIndex;
//			if (cindex == 'undefined') {
//				cindex = this.store.data.length - 1;
//			}
//			for (var i = 0; i < rows.length; i++) {
//				var rowData = this.store.getById(rows[i].id);
//
//				if (!this.copy) {
//					this.store.remove(this.store.getById(rows[i].id));
//				}
//
//				this.store.insert(cindex, rowData);
//			}
//			;
//
//			this.save();
//
//		};
//
//		var ddrow = new Ext.dd.DropTarget(this.getView().mainBody, {
//			ddGroup: 'ModulesGridDD',
//			copy: false,
//			notifyDrop: notifyDrop.createDelegate(this)
//		});

		this.store.load();

	},
//	save: function() {
//		var modules = new Array();
//
//		for (var i = 0; i < this.store.data.items.length; i++) {
//			modules[i] = {
//				id: this.store.data.items[i].get('id'),
//				sort_order: i,
//				admin_menu: this.store.data.items[i].get('admin_menu')
//			};
//		}
//
//		GO.request({
//			maskEl: this.container,
//			url: 'modules/module/saveSortOrder',
//			params: {
//				modules: Ext.encode(modules)
//			},
//			scope: this
//		});
//	},
//	uninstallModule : function() {
//
//		var uninstallModules = Ext.encode(this.selModel.selections.keys);
//
//		switch (this.selModel.selections.keys.length) {
//			case 0 :
//				Ext.MessageBox.alert(GO.lang['strError'],
//					GO.lang['noItemSelected']);
//				return false;
//				break;
//
//			case 1 :
//				var strConfirm = GO.lang['strDeleteSelectedItem'];
//				break;
//
//			default :
//				var t = new Ext.Template(GO.lang['strDeleteSelectedItems']);
//				var strConfirm = t.applyTemplate({
//					'count' : this.selModel.selections.keys.length
//				});
//				break;
//		}
//
//		Ext.MessageBox.confirm(GO.lang['strConfirm'], strConfirm,
//			function(btn) {
//				if (btn == 'yes') {
//					this.store.baseParams.uninstall_modules = uninstallModules;
//
//					this.store.reload({
//						callback : function() {
//							if (!this.store.reader.jsonData.uninstallSuccess) {
//								Ext.MessageBox
//								.alert(
//									GO.lang['strError'],
//									this.store.reader.jsonData.uninstallFeedback);
//							}
//
//							this.store.reload();
//						},
//						scope : this
//					});
//
//					delete this.store.baseParams.uninstall_modules;
//				}
//			}, this);
//
//	},

	showPermissions: function(moduleId, name, acl_id) {
		if (!this.permissionsWin) {
			this.permissionsWin = new GO.modules.ModulePermissionsWindow();
			this.permissionsWin.on('hide', function() {
				// Loop through the recently installed modules, allowing the user to
				// set the permissions, module by module.
				if (this.installedModules && this.installedModules.length) {
					var r = this.installedModules.shift();
					this.permissionsWin.show(r.id, r.name, r.acl_id);
				}
			}, this);
		}
		this.permissionsWin.show(moduleId, name, acl_id);
	},
	iconRenderer: function(name, cell, record) {
		return '<div class="mo-title" style="background-image:url(' + record.data["icon"] + ')">'
						+ name + '</div>';
	},
	warningRenderer: function(name, cell, record) {
		return record.data.warning != '' ?
						'<div class="go-icon go-warning-msg" ext:qtip="' + Ext.util.Format.htmlEncode(record.data.warning) + '"></div>' : '';
	},
	buyRenderer: function(name, cell, record) {
		return record.data.buyEnabled ? '<a href="#" class="normal-link" onclick="GO.modules.showBuyDialog(\'' + record.data.id + '\');">Buy</a>' : '';
	}



});

GO.moduleManager.addModule('modules', GO.modules.MainPanel, {
	title: GO.modules.lang.modules,
	iconCls: 'go-tab-icon-modules',
	admin: true
});