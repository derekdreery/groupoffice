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

GO.links.SettingsDialog = function(config){


	if(!config)
	{
		config={};
	}

	
	var formItems = [{
			xtype:'htmlcomponent',			
			html: GO.links.lang.defaultLinkFolderText
	}];

	//GO.linkTypes is defined in /default_scripts.inc.php
	for(var i=0;i<GO.linkTypes.length;i++){
		formItems.push({
			xtype:'textarea',
			fieldLabel:GO.linkTypes[i].name,
			name: 'default_folders_'+GO.linkTypes[i].id,
			anchor:'-20',
			height:40
		});
	}

	this.defaultFoldersPanel = new Ext.FormPanel({
		title:GO.links.lang.linkFolders,
		cls:'go-form-panel',
		url:GO.settings.modules.links.url+'action.php',
		baseParams:{task:'save_default_link_folders'},
		autoScroll:true,
		items:formItems,
		buttonAlign:'right',
		buttons:[{
			text: GO.lang.cmdSave,
			handler: function(){
				this.defaultFoldersPanel.form.submit();
			},
			scope:this
		}]
	});

	config.layout='fit';
	config.modal=false;
	config.resizable=false;
	config.width=750;
	config.height=500;
	config.closeAction='hide';
	config.title= GO.lang.cmdSettings;
	config.items={
		xtype:'tabpanel',
		activeTab: 0,
		border:false,
    deferredRender: false,
		enableTabScroll:true,
		items:[
			new GO.links.LinkDescriptionsGrid(),
			this.defaultFoldersPanel
		]
	};	

	GO.links.SettingsDialog.superclass.constructor.call(this, config);
}
Ext.extend(GO.links.SettingsDialog, Ext.Window,{

	afterRender : function(){
		GO.links.SettingsDialog.superclass.afterRender.call(this);
		this.defaultFoldersPanel.load({
			url:GO.settings.modules.links.url+'json.php',
			params:{task:'default_link_folders'}
		});
	}

});