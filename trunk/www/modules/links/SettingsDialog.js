/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: MainPanel.tpl 1858 2008-04-29 14:09:19Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

GO.links.SettingsDialog = function(config){


	if(!config)
	{
		config={};
	}

	this.defaultFoldersPanel = new Ext.FormPanel({
		title:'Default link folders',
		cls:'go-form-panel',
		url:GO.settings.modules.links.url+'action.php',
		baseParams:{task:'save_default_link_folders'},
		autoScroll:true,
		buttons:[{
			text: GO.lang.cmdSave,
			handler: function(){
				this.defaultFoldersPanel.form.submit();
			},
			scope:this
		}]
	});

	for(var i=0;i<GO.linkTypes.length;i++){
		this.defaultFoldersPanel.add({
			xtype:'textarea',
			fieldLabel:GO.linkTypes[i].name,
			name: 'default_folders['+GO.linkTypes[i].id+']',
			anchor:'100%',
			height:40
		});
	}

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
	show : function(){


		GO.links.SettingsDialog.superclass.show.call(this);
	}

});