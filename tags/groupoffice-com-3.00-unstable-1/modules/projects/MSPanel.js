/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @version $Id: MSPanel.js 2635 2008-07-18 11:00:31Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 */

GO.projects.MSPanel = function(config){
	
	
	if(!config)
	{
		config={};
	}
	var saveButton = new Ext.Button({
		text: GO.lang['cmdAdd'],
		handler: function(){
			this.addMSPanel.submit();
		},
		scope:this
	});
	
	
	this.addMSPanel = new GO.projects.AddMSPanel({
		region:'north',
		//height:140,
		autoHeight:true,
		split:true,
		border:true,
		saveButton:saveButton
	});
	
	this.addMSPanel.on('save', function(){this.msGrid.store.reload();}, this);
	
	this.msGrid = new GO.projects.MSGrid({
		region:'center',
		border:true
	});
	
	
	
			
	config.layout='border';
	config.items=[this.addMSPanel,this.msGrid];
	GO.projects.MSPanel.superclass.constructor.call(this, config);	
}

Ext.extend(GO.projects.MSPanel, Ext.Panel,{
	setProjectId : function(project_id)
	{
		this.project_id=project_id;
		this.addMSPanel.setProjectId(project_id);
		this.msGrid.setProjectId(project_id);
	} 
	
	
});