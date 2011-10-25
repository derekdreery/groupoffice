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
 
GO.PersonalSettingsDialog = Ext.extend(GO.dialog.TabbedFormDialog , {

	initComponent : function(){
		
		Ext.apply(this, {
			goDialogId:'settings',
			title:GO.lang.settings,
			formControllerUrl: 'settings',
			width:900
		});
		
		GO.PersonalSettingsDialog.superclass.initComponent.call(this);	
	},
	
	buildForm : function(){
		var panels =GO.moduleManager.getAllSettingsPanels();
		
		for(i=0;i<panels.length;i++)
			this.addPanel(panels[i]);
	},

	show : function (remoteModelId, config) {
		
		remoteModelId = GO.settings.user_id;
		GO.PersonalSettingsDialog.superclass.show.call(this, remoteModelId, config);	
	}
});
