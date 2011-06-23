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
 
GO.users.PermissionsPanel = function(config)
{
    if(!config)
    {
        config={};
    }
	
    config.autoScroll=false;
    config.border=false;
    config.hideLabel=true;
    config.title = GO.lang['strPermissions'];
    config.layout='columnfit';
    config.anchor='100% 100%';
	
    config.defaults={
        border:true,
        height:280,
        autoScroll:true
    };
	
	
    /* module permissions grid */
	
    var moduleReadPermissionColumn = new GO.grid.CheckColumn({
        header: GO.users.lang.useModule,
        dataIndex: 'read_permission',
        width: 55,
        disabled_field:'read_disabled',
        menuDisabled:true
    });

    var moduleWritePermissionColumn = new GO.grid.CheckColumn({
        header: GO.users.lang.manageModule,
        dataIndex: 'write_permission',
        width: 55,
        disabled_field:'write_disabled',
        menuDisabled:true
    });
	
    this.modulePermissionsStore = new GO.data.JsonStore({
        url:GO.settings.modules.users.url+'json.php',
        baseParams: {
            user_id: -1,
            task: 'modules'
        },
        fields: ['id', 'name', 'read_disabled', 'write_disabled', 'read_permission', 'write_permission'],
        root: 'results',
        menuDisabled:true
    });
	
    var moduleAccessGrid = new GO.grid.GridPanel({
        columnWidth: .34,
        title: GO.users.lang.moduleAccess,
        layout:'fit',
        columns: [
        {
            id:'name',
            header: GO.users.lang['cmdHeaderColumnName'],
            dataIndex: 'name',
            renderer: this.iconRenderer,
            menuDisabled:true
        },
        moduleReadPermissionColumn,
        moduleWritePermissionColumn
        ],
        ds: this.modulePermissionsStore,
        //sm: new Ext.grid.RowSelectionModel({singleSelect:singleSelect}),
        plugins: [moduleReadPermissionColumn, moduleWritePermissionColumn],
        autoExpandColumn:'name'
    });
	
	
    /* end module permissions grid */
	
	
    /* group member grid */
	
    var groupsMemberOfColumn = new GO.grid.CheckColumn({
        header: '',
        dataIndex: 'group_permission',
        width: 55,
        menuDisabled:true
    });
	
	
    this.groupMemberStore = new GO.data.JsonStore({
        url:GO.settings.modules.users.url+'json.php',
        baseParams: {
            user_id: -1,
            task: 'groups'
        },
        fields: ['id', 'disabled', 'group', 'group_permission'],
        root: 'results'
    });
	
    var groupMemberGrid = new GO.grid.GridPanel({
        columnWidth: .33,
        layout:'fit',
        title: GO.users.lang.userIsMemberOf,
        columns: [
        {
            id:'name',
            header: GO.users.lang.group,
            dataIndex: 'group',
            menuDisabled:true
        },
        groupsMemberOfColumn
        ],
        ds: this.groupMemberStore,
        //sm: new Ext.grid.RowSelectionModel({singleSelect:singleSelect}),
        plugins: groupsMemberOfColumn,
        autoExpandColumn:'name'
    });
	
	
	
    /* end group member grid */
	
	
	
    /* group visible grid */
	
    var groupsVisibleToColumn = new GO.grid.CheckColumn({
        header: '',
        dataIndex: 'visible_permission',
        width: 55,
        menuDisabled:true
    });
	
	
	
    this.groupVisibleStore = new GO.data.JsonStore({
        url:GO.settings.modules.users.url+'json.php',
        baseParams: {
            user_id: -1,
            task: 'visible'
        },
        fields: ['id', 'disabled', 'group', 'visible_permission'],
        root: 'results'
    });
	
    var groupVisibleGrid = new GO.grid.GridPanel({
        columnWidth: .33,
        layout:'fit',
        title: GO.users.lang.userVisibleTo,
        columns: [
        {
            id:'name',
            header: GO.users.lang.group,
            dataIndex: 'group',
            menuDisabled:true
        },
        groupsVisibleToColumn
        ],
        ds: this.groupVisibleStore,
        plugins: groupsVisibleToColumn,
        autoExpandColumn:'name'
    });
	
	
    /* end group visible grid */

    config.items=[
    moduleAccessGrid,
    groupMemberGrid,
    groupVisibleGrid];
	

    GO.users.PermissionsPanel.superclass.constructor.call(this, config);
}


Ext.extend(GO.users.PermissionsPanel, Ext.Panel,{
    iconRenderer : function(name, cell, reader)
    {
        return '<div class="go-module-icon-'+reader.data.id+'" style="height:16px;padding-left:22px;background-repeat:no-repeat;">'+name+'</div>';
    },
	
    setUserId : function(user_id, reset)
    {
        if(!this.isVisible() && user_id!=this.user_id)
        {
            this.groupMemberStore.removeAll();
            this.modulePermissionsStore.removeAll();
            this.groupVisibleStore.removeAll();
			
            this.modulePermissionsStore.baseParams.user_id=-1;
            this.groupMemberStore.baseParams.user_id=-1;
            this.groupVisibleStore.baseParams.user_id=-1;
        }
        this.user_id=user_id;
    //this.setDisabled(this.user_id==0);
    },
	
    onShow : function(){
        GO.users.PermissionsPanel.superclass.onShow.call(this);
				
        if(this.groupMemberStore.baseParams.user_id!=this.user_id)
        {
            this.modulePermissionsStore.baseParams.user_id=this.user_id;
            this.groupMemberStore.baseParams.user_id=this.user_id;
            this.groupVisibleStore.baseParams.user_id=this.user_id;
			
            this.groupMemberStore.load();
            this.modulePermissionsStore.load();
            this.groupVisibleStore.load();
        }
    },

		commit : function(){
			this.modulePermissionsStore.commitChanges();
			this.groupMemberStore.commitChanges();
			this.groupVisibleStore.commitChanges();
		},
	
    getPermissionParameters : function(){

		
        var modulePermissions = new Array();
        var memberGroups = new Array();
        var visibleGroups = new Array();
		 
        for (var i = 0; i < this.modulePermissionsStore.data.items.length;  i++)
        {
            modulePermissions[i] =
            {
                id: this.modulePermissionsStore.data.items[i].get('id'),
                name: this.modulePermissionsStore.data.items[i].get('name'),
                read_permission: this.modulePermissionsStore.data.items[i].get('read_permission'),
                write_permission: this.modulePermissionsStore.data.items[i].get('write_permission')
            };
        }
		 
        for (var i = 0; i < this.groupMemberStore.data.items.length;  i++)
        {
            memberGroups[i] =
            {
                id: this.groupMemberStore.data.items[i].get('id'),
                group: this.groupMemberStore.data.items[i].get('name'),
                group_permission: this.groupMemberStore.data.items[i].get('group_permission')
            };
        }

        for (var i = 0; i < this.groupVisibleStore.data.items.length;  i++)
        {
            visibleGroups[i] =
            {
                id: this.groupVisibleStore.data.items[i].get('id'),
                group: this.groupVisibleStore.data.items[i].get('name'),
                visible_permission: this.groupVisibleStore.data.items[i].get('visible_permission')
            };
        }
	
	
        return {
            modules : Ext.encode(modulePermissions),
            groups_visible : Ext.encode(visibleGroups),
            group_member : Ext.encode(memberGroups)
        };
    }

});			