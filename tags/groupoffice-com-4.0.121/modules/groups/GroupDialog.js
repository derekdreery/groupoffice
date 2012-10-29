/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: GroupDialog.js 7689 2011-08-11 13:44:39Z wsmits $
 * @copyright Copyright Intermesh
 * @author Wesley Smits <wsmits@intermesh.nl>
 */
 
GO.groups.GroupDialog = Ext.extend(GO.dialog.TabbedFormDialog , {
	
	initComponent : function(){
		
		Ext.apply(this, {
			goDialogId:'group',
			title:GO.groups.lang.group,
			formControllerUrl: 'groups/group',
			titleField:'name',
			height:440
		});
		
		GO.groups.GroupDialog.superclass.initComponent.call(this);
	},

	beforeLoad : function(remoteModelId, config){
		this.userGrid.setGroupId(remoteModelId);
		if(this.modulePermissionsGrid)
			this.modulePermissionsGrid.setIdParam(remoteModelId);
    if(remoteModelId <= 0)
      this.userGrid.setDisabled(true);
    else
      this.userGrid.setDisabled(false);
	},
	
	beforeSubmit : function(params) {
		if(this.modulePermissionsGrid)
			this.formPanel.form.baseParams['permissions'] = this.modulePermissionsGrid.getPermissionData();		
	},
	
	buildForm : function () {
    
    this.propertiesPanel = new Ext.Panel({
      region:'north',
      height:35,
      border:false,
			cls:'go-form-panel',
			layout:'form',
			items:[{
				xtype: 'textfield',
				name: 'name',
				width:300,
				anchor: '100%',
				maxLength: 100,
				allowBlank:false,
				fieldLabel: GO.lang.strName
			}
			]				
		});

    if(GO.settings.has_admin_permission) {
      this.adminOnlyCheckBox = new Ext.ux.form.XCheckbox({
          name: 'admin_only',
          checked: false,
          boxLabel: GO.groups.lang.adminOnlyLabel,
          hideLabel:true
      });
      this.propertiesPanel.height=60;
      this.propertiesPanel.add(this.adminOnlyCheckBox);
    }
    
    this.userGrid = new GO.groups.UsersGrid({
      region:'center'
    });
    
    this.borderPanel = new Ext.Panel({
      layout:'border',
      title:GO.lang['strProperties'],	
      items:[this.propertiesPanel, this.userGrid]
    });

    this.permissionsPanel = new GO.grid.PermissionsPanel({
      title:GO.groups.lang.managePermissions,
			addLevel: GO.permissionLevels.manage,
      hideLevel:true
    });

		

		this.addPanel(this.borderPanel);
    this.addPanel(this.permissionsPanel);
		
		if(GO.settings.modules.modules.permission_level){
			this.modulePermissionsGrid = new GO.grid.ModulePermissionsGrid({
				title: GO.groups.lang['modulePermissions'],
				storeUrl: GO.url('modules/module/permissionsStore'),
				paramIdType: 'groupId'
			});
			this.addPanel(this.modulePermissionsGrid);
		}
	}
});