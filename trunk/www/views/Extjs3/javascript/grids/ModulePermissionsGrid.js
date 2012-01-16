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
 
GO.grid.ModulePermissionsGrid = function(config)
{
    if(!config)
    {
        config={};
    }
	
		var missingParams = '';
	
		if (GO.util.empty(config.title))
			missingParams = missingParams + '- title<br />';
		if (GO.util.empty(config.storeUrl))
			missingParams = missingParams + '- storeUrl<br />';
		if (!GO.util.empty(missingParams))
			Ext.alert('',GO.lang['gridMissingParams']+'<br />');

		
		config.width = '100%';
		config.height = '100%';
		config.loadMask=true;

    var radioPermissionNoneColumn = new GO.grid.RadioColumn({
			horizontal:true,
			value:0,
			header: GO.lang['permissionNone'],
			dataIndex: 'permissionLevel',
			width: 50
    });
		
		var radioPermissionUseColumn = new GO.grid.RadioColumn({
			horizontal:true,
			value:GO.permissionLevels.read,
			header: GO.lang['permissionUse'],
			dataIndex: 'permissionLevel',
			width: 50
    });
		
		var radioPermissionManageColumn = new GO.grid.RadioColumn({
			horizontal:true,
			value:GO.permissionLevels.manage,
			header: GO.lang['permissionManage'],
			dataIndex: 'permissionLevel',
			width: 50
    });
	
    config.store = new GO.data.JsonStore({
			url: config.storeUrl,
			baseParams: {
				groupId : -1
			},
			fields: ['id','name', 'permissionLevel'],
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

    GO.grid.ModulePermissionsGrid.superclass.constructor.call(this, config);
		
		this.on('show',function(){
			this.store.load();
		},this);
		
}


Ext.extend(GO.grid.ModulePermissionsGrid, GO.grid.GridPanel,{
		
	setIdParam : function(id) {
		this.store.baseParams['id'] = id;
		this.store.commitChanges();
	},
	
	getPermissionData : function(){
		if(this.store.getModifiedRecords().length){
			this.store.commitChanges();
			return Ext.encode(this.getGridData());			
		}else
		{
			return null;
		}
	}
	
});