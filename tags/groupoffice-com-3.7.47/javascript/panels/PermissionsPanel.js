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
 */

/**
 * @class GO.grid.PermissionsPanel
 * @extends Ext.Panel
 * 
 * A panel that can be used to set permissions for a Group-Office ACL. It will
 * use an anchor layout with 100% width and 100% height automatically.
 * 
 * @constructor
 * @param {Object}
 *            config The config object
 */

GO.grid.PermissionsPanel = Ext.extend(Ext.Panel, {

	changed : false,
	loaded : false,
	managePermission : false,

	// private
	initComponent : function() {

		if(!this.title){
			this.title=GO.lang.strPermissions;
		}

		this.showLevel = (this.hideLevel) ? false : true;			

		var permissionLevelConfig ={
					store : new Ext.data.SimpleStore({
						id:0,
						fields : ['value', 'text'],
						data : [
							[1, GO.lang.permissionRead],
							[2, GO.lang.permissionWrite],
							[3, GO.lang.permissionDelete],
							[4, GO.lang.permissionManage]
						]
					}),
					valueField : 'value',
					displayField : 'text',
					mode : 'local',
					triggerAction : 'all',
					editable : false,
					selectOnFocus : true,
					forceSelection : true
				};

		var selectUsersPermissionLevel = new GO.form.ComboBox(permissionLevelConfig);
		var selectGroupsPermissionLevel = new GO.form.ComboBox(permissionLevelConfig);

		this.header = false;
		this.layout = 'anchor';
		this.border = false;
		this.anchor = '100% 100%';
		this.disabled = true;
		// this.hideMode='offsets';

		this.aclGroupsStore = new GO.data.JsonStore({
			url : BaseHref + 'json.php',
			baseParams : {
				task : "groups_in_acl",
				acl_id : 0
			},
			root : 'results',
			totalProperty : 'total',
			id : 'id',
			fields : ['id', 'name', 'level'],
			remoteSort : true
		});
		this.aclGroupsStore.setDefaultSort('name', 'ASC');

		var renderLevel = function(v){
			var r = permissionLevelConfig.store.getById(v);
			return r.get('text');
		}

		var groupColumns = [{
			header : GO.lang['strName'],
			dataIndex : 'name',
			menuDisabled:true
		}];
		if(this.showLevel)
		{
			groupColumns.push({
				header : GO.lang.permissionsLevel,
				dataIndex : 'level',
				menuDisabled:true,
				editor : selectUsersPermissionLevel,
				renderer:renderLevel
			});
		}

		var action = new Ext.ux.grid.RowActions({
			header : '-',
			autoWidth:true,
			align : 'center',
			actions : [{
				iconCls : 'btn-users',
				qtip: GO.lang.users
			}]
		});

		groupColumns.push(action);


		this.aclGroupsStore.on("load", function(){
			this.managePermission = this.aclGroupsStore.reader.jsonData.manage_permission;

			this.aclGroupsGrid.getTopToolbar().setDisabled(!this.managePermission);
			this.aclUsersGrid.getTopToolbar().setDisabled(!this.managePermission);

		}, this);

		this.aclGroupsGrid = new GO.grid.EditorGridPanel({
			plugins : action,
			clicksToEdit : 1,
			anchor : '100% 50%',
			title : GO.lang['strAuthorizedGroups'],
			store : this.aclGroupsStore,
			border : false,
			columns : groupColumns,
			view : new Ext.grid.GridView({
				autoFill : true,
				forceFit : true
			}),
			loadMask : {
				msg : GO.lang['waitMsgLoad']
			},
			sm : new Ext.grid.RowSelectionModel({}),
			// paging:true,
			layout : 'fit',
			tbar : [{
				iconCls : 'btn-add',
				text : GO.lang['cmdAdd'],
				cls : 'x-btn-text-icon',
				handler : function() {
					this.showAddGroupsDialog();
				},
				scope : this
			}, {
				iconCls : 'btn-delete',
				text : GO.lang['cmdDelete'],
				cls : 'x-btn-text-icon',
				handler : function() {
					this.aclGroupsGrid.deleteSelected();
				},
				scope : this
			}]

		});

		action.on({
			scope:this,
			action:function(grid, record, action, row, col) {

				this.aclGroupsGrid.getSelectionModel().selectRow(row);

				switch(action){
					case 'btn-users':
						this.showUsersInGroupDialog(record.data.id);
						break;
				}
			}
		}, this);

		this.aclGroupsGrid.on('beforeedit', function(e) {
			return this.managePermission;
		},this);

		this.aclGroupsGrid.on('afteredit', function(e) {

			Ext.Ajax.request({
				url:GO.settings.config.host+'action.php',
				params:{
					task:'update_level',
					acl_id: this.store.baseParams.acl_id,
					group_id: e.record.get("id"),
					level:e.record.get("level")
				},
				success: function(response, options)
				{
					var responseParams = Ext.decode(response.responseText);
					if(!responseParams.success)
					{
						alert(responseParams.feedback);
						this.store.rejectChanges();
					}else
					{
						this.store.commitChanges();
					}
				},
				scope:this
			})

		}, this.aclGroupsGrid);

		this.aclUsersStore = new GO.data.JsonStore({

			url : BaseHref + 'json.php',
			baseParams : {
				task : "users_in_acl",
				acl_id : 0
			},
			root : 'results',
			totalProperty : 'total',
			id : 'id',
			fields : ['id', 'name', 'level'],
			remoteSort : true
		});
		this.aclUsersStore.setDefaultSort('name', 'ASC');

		var userColumns = [{
			header : GO.lang['strName'],
			dataIndex : 'name',
			menuDisabled:true
		}];
		if(this.showLevel)
		{
			userColumns.push({
				header : GO.lang.permissionsLevel,
				dataIndex : 'level',
				menuDisabled:true,
				editor : selectGroupsPermissionLevel,
				renderer:renderLevel
			})
		}

		this.aclUsersGrid = new GO.grid.EditorGridPanel({
			anchor : '100% 50%',
			title : GO.lang['strAuthorizedUsers'],
			store : this.aclUsersStore,
			paging:true,
			border : false,
			columns : userColumns,
			view : new Ext.grid.GridView({
				autoFill : true,
				forceFit : true
			}),
			loadMask : {
				msg : GO.lang['waitMsgLoad']
			},
			sm : new Ext.grid.RowSelectionModel({}),
			// paging:true,
			layout : 'fit',
			tbar : [{
				iconCls : 'btn-add',
				text : GO.lang['cmdAdd'],
				cls : 'x-btn-text-icon',
				handler : function() {
					this.showAddUsersDialog();
				},
				scope : this
			}, {
				iconCls : 'btn-delete',
				text : GO.lang['cmdDelete'],
				cls : 'x-btn-text-icon',
				handler : function() {
					this.aclUsersGrid.deleteSelected();
				},
				scope : this
			}]

		});

		this.aclUsersGrid.on('beforeedit', function(e) {
			return this.managePermission;
		},this);
		
		this.aclUsersGrid.on('afteredit', function(e) {

			Ext.Ajax.request({
				url:GO.settings.config.host+'action.php',
				params:{
					task:'update_level',
					acl_id: this.store.baseParams.acl_id,
					user_id: e.record.get("id"),
					level:e.record.get("level")
				},
				success: function(response, options)
				{
					var responseParams = Ext.decode(response.responseText);
					if(!responseParams.success)
					{
						alert(responseParams.feedback);
						this.store.rejectChanges();
					}else
					{
						this.store.commitChanges();
					}
				},
				scope:this
			})

		}, this.aclUsersGrid);


		this.items = [this.aclGroupsGrid, this.aclUsersGrid];

		GO.grid.PermissionsPanel.superclass.initComponent.call(this);
	},

	/**
	 * Sets Access Control List to load in the panel
	 * 
	 * @param {Number}
	 *            The Group-Office acl ID.
	 */
	setAcl : function(acl_id) {

		this.acl_id = acl_id ? acl_id : 0;
		this.loaded = false;
		this.aclGroupsStore.baseParams['acl_id'] = acl_id;
		this.aclUsersStore.baseParams['acl_id'] = acl_id;
		this.setDisabled(GO.util.empty(acl_id));

		if (this.isVisible()) {
			this.aclGroupsStore.load();
			this.aclUsersStore.load();
			this.loaded = true;
		}
	},

	onShow : function() {

		GO.grid.PermissionsPanel.superclass.onShow.call(this);

		if (!this.loaded) {
			this.loadAcl();
		}

	},

	loadAcl : function(){
		this.aclGroupsStore.load();
		this.aclUsersStore.load();
		this.loaded = true;
	},

	afterRender : function() {

		GO.grid.PermissionsPanel.superclass.afterRender.call(this);

		var v = this.isVisible();

		if (v && !this.loaded) {
			this.loadAcl();
		}
	},

	// private
	showUsersInGroupDialog : function(groupId) {
		if (!this.usersInGroupDialog) {
			this.usersInGroupDialog = new GO.dialog.UsersInGroup();
		}
		this.usersInGroupDialog.setGroupId(groupId);
		this.usersInGroupDialog.show();
	},

	// private
	showAddGroupsDialog : function() {
		if (!this.addGroupsDialog) {
			this.addGroupsDialog = new GO.dialog.SelectGroups({
				handler : function(groupsGrid) {
					if (groupsGrid.selModel.selections.keys.length > 0) {
						this.aclGroupsStore.baseParams['add_groups'] = Ext
						.encode(groupsGrid.selModel.selections.keys);
						this.aclGroupsStore.load({
							callback : function() {
								if (!this.reader.jsonData.addSuccess) {
									alert(this.reader.jsonData.addFeedback);
								}
							}
						});
						delete this.aclGroupsStore.baseParams['add_groups'];
					// this.aclGroupsStore.add(groupsGrid.selModel.getSelections());
					// this.changed=true;
					}
				},
				scope : this
			});
		}
		this.addGroupsDialog.show();
	},

	// private
	showAddUsersDialog : function() {
		if (!this.addUsersDialog) {
			this.addUsersDialog = new GO.dialog.SelectUsers({
				handler : function(usersGrid) {
					if (usersGrid.selModel.selections.keys.length > 0) {
						this.aclUsersStore.baseParams['add_users'] = Ext
						.encode(usersGrid.selModel.selections.keys);
						this.aclUsersStore.load({
							callback : function() {
								if (!this.reader.jsonData.addSuccess) {
									alert(this.reader.jsonData.addFeedback);
								}
							}
						});
						delete this.aclUsersStore.baseParams['add_users'];
					}
				},
				scope : this
			});
		}
		this.addUsersDialog.show();
	}

});