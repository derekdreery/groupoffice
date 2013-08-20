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
 
GO.notes.CategoryDialog = Ext.extend(GO.dialog.TabbedFormDialog , {
	initComponent : function(){
		
		Ext.apply(this, {
			titleField:'name',
			title:GO.notes.lang.category,
			formControllerUrl: 'notes/category',
			height:600
		});
		
		GO.notes.CategoryDialog.superclass.initComponent.call(this);	
	},
	buildForm : function () {

		this.propertiesPanel = new Ext.Panel({
			url: GO.settings.modules.notes.url+'action.php',
			border: false,
			baseParams: {task: 'category'},			
			title:GO.lang['strProperties'],			
			cls:'go-form-panel',waitMsgTarget:true,			
			layout:'form',
			autoScroll:true,
			items:[{
				xtype: 'textfield',
			  name: 'name',
				anchor: '100%',
			  allowBlank:false,
			  fieldLabel: GO.lang.strName
			},this.selectUser = new GO.form.SelectUser({
				fieldLabel: GO.lang['strUser'],
				disabled : !GO.settings.has_admin_permission,
				value: GO.settings.user_id,
				anchor: '100%'
			})]
				
		});

		this.addPanel(this.propertiesPanel);	
 
    this.addPermissionsPanel(new GO.grid.PermissionsPanel());    
	}
});