/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: ModulePermissionsGrid.js 8116 2011-09-20 15:14:08Z wilmar1980 $
 * @copyright Copyright Intermesh
 * @author Wilmar van Beusekom <wilmar@intermesh.nl>
 */
 
GO.groups.ModulePermissionsGrid = function(config)
{
    if(!config)
    {
        config={};
    }
	
    config.title = GO.groups.lang['modulePermissions'];

		config.width = '100%';
		config.height = '100%';

    var radioPermissionNoneColumn = new GO.grid.RadioColumn({
			header: GO.groups.lang['permissionNone'],
			dataIndex: 'groupPermissionNone',
			width: 50,
			onMouseDown: function(e,t) {}
    });
		
		var radioPermissionUseColumn = new GO.grid.RadioColumn({
			header: GO.groups.lang['permissionUse'],
			dataIndex: 'groupPermissionUse',
			width: 50,
			onMouseDown: function(e,t) {}
    });
		
		var radioPermissionManageColumn = new GO.grid.RadioColumn({
			header: GO.groups.lang['permissionManage'],
			dataIndex: 'groupPermissionManage',
			width: 50,
			onMouseDown: function(e,t) {}
    });
	
    config.store = new GO.data.JsonStore({
			url: GO.url('groups/group/modulePermissionsStore'),//GO.settings.modules.users.url+'json.php',
			baseParams: {
				groupId : -1
			},
			fields: ['name', 'groupPermissionNone', 'groupPermissionUse', 'groupPermissionManage'],
			root: 'results',
			menuDisabled:true
    });
		
		config.cm = new Ext.grid.ColumnModel({
			defaults:{
				sortable:true
			},
			columns:[
				{
						id:'name',
						header: GO.modules.lang['module'],
						dataIndex: 'name'
	//					,
	//					menuDisabled:true
				},
				radioPermissionNoneColumn,
				radioPermissionUseColumn,
				radioPermissionManageColumn
			]
		});
		
		config.view=new Ext.grid.GridView({
			autoFill: true,
			forceFit: true,
			emptyText: GO.lang['strNoItems']		
		});
		config.sm=new Ext.grid.RowSelectionModel();
		
		config.plugins = [
			radioPermissionNoneColumn,
			radioPermissionUseColumn,
			radioPermissionManageColumn
		];

    GO.groups.ModulePermissionsGrid.superclass.constructor.call(this, config);
		
		this.on('show',function(){
			this.store.load();
		},this);
		
		this.on('cellclick',function( grid, rowIndex, columnIndex, e ) {
			switch(columnIndex) {
				case 1:
					this.setRecordPermission(rowIndex,'groupPermissionNone');
					break;
				case 2:
					this.setRecordPermission(rowIndex,'groupPermissionUse');
					break;
				case 3:
					this.setRecordPermission(rowIndex,'groupPermissionManage');
					break;
				default:
					break;
			}
		},this);
		
}


Ext.extend(GO.groups.ModulePermissionsGrid, GO.grid.GridPanel,{
	setGroupId : function(groupId) {
		this._groupId = groupId;
		this.store.baseParams['groupId'] = groupId;
	},
	
	setRecordPermission : function(rowIndex,permissionType) {
		this.store.getAt(rowIndex).set('groupPermissionNone',permissionType=='groupPermissionNone');
		this.store.getAt(rowIndex).set('groupPermissionUse',permissionType=='groupPermissionUse');
		this.store.getAt(rowIndex).set('groupPermissionManage',permissionType=='groupPermissionManage');
	}
});