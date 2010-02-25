/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version 
 * @copyright Copyright Intermesh
 * @author Wilmar van Beusekom <wilmar@intermesh.nl>
 */

GO.cms.WritingUsersPanel = function(config) {
	if(!config)
	{
		config={};
	}

	config.title = GO.cms.lang.writingUsers;

	this.writingUsersStore = new Ext.data.SimpleStore(
		{ fields : ['id','name'] }
		);

	this.writingUsersGrid = new GO.grid.GridPanel({
		anchor : '100% 50%',
		title : GO.lang['strAuthorizedUsers'],
		store : this.writingUsersStore,
		border : false,
		columns : [{
				header : GO.lang['strName'],
				dataIndex : 'name',
				menuDisabled:true
			}],
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

	GO.cms.foldersDialog = new GO.cms.FoldersDialog();

	config.items = [this.writingUsersGrid];

	GO.cms.WritingUsersPanel.superclass.constructor.call(this, config);

	this.on('show',function(a,b,c) {
		this.setWritingUserIds();
	});

	this.writingUsersGrid.on('rowdblclick', function(grid,rowIndex,e) {
		GO.cms.foldersDialog.show(grid.store.data.items[rowIndex].data.id, GO.cms.foldersDialog.site_id);
	});

};

Ext.extend(GO.cms.WritingUsersPanel, Ext.Panel, {
	contains : function(array,element) {
		for (var i=0; i<array.length; i++) {
			if (array[i]==element) {
				return true;
			}
		}
		return false;
	}

	,
	setWritingUserIds : function() {

		var groupsStore = this.permissionsTab.aclGroupsStore;
		var usersStore = this.permissionsTab.aclUsersStore;

		this.writingUserIds = new Array();

		/* Put writing users into array */

		for (var i=0; i<usersStore.data.items.length; i++) {
			if (usersStore.data.items[i].data.level != 1) {
				this.writingUserIds.push(usersStore.data.items[i].data.id);
			}
		}

		this.writingGroupIds = new Array();

		/* Put writing groups into array */

		for (var i=0; i<groupsStore.data.items.length; i++) {
			if (groupsStore.data.items[i].data.level != 1) {
				this.writingGroupIds.push(groupsStore.data.items[i].data.id);
			}
		}

		Ext.Ajax.request({
			url:GO.settings.modules.cms.url+'json.php',
			params:{
				task:'writing_users',
				group_ids: Ext.encode(this.writingGroupIds),
				user_ids: Ext.encode(this.writingUserIds)
			},
			success: function(response, options)
			{
				var responseParams = Ext.decode(response.responseText);
				if(!responseParams.success)
				{
					alert(responseParams.feedback);
				}else
				{
					var data = new Array();
					for (var i in responseParams.results) {
						data[i] = [ responseParams.results[i].id,
												responseParams.results[i].name
											]
					}

					this.writingUsersStore.loadData(data);
					//this.writingUsersStore.load();

			}
		},
		scope:this
	})

}
});