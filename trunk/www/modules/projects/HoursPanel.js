/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @version $Id: HoursPanel.js 2635 2008-07-18 11:00:31Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 */


 
GO.projects.HoursPanel = function(config){
	
	
	if(!config)
	{
		config={};
	}
	
	var saveButton = new Ext.Button({
		text: GO.lang['cmdAdd'],
		handler: function(){
			this.addHoursPanel.submit();
		},
		scope:this
	});
	
	this.addHoursPanel =GO.projects.addHoursPanel= new GO.projects.AddHoursPanel({
		region:'north',
		height:180,
		title:GO.projects.lang.timeTracking,
		split:true,
		border:true,
		collapsible:true,
		disabled:true,
		saveButton:saveButton
	});
	
	this.addHoursPanel.on('save', function(){this.hoursGrid.store.reload();}, this);
	
	this.hoursGrid = new GO.projects.HoursGrid({
		region:'center',
		border:true
	});
	
			
	config.layout='border';
	config.items=[this.addHoursPanel,this.hoursGrid];
	
	
	GO.projects.HoursPanel.superclass.constructor.call(this, config);	
}

Ext.extend(GO.projects.HoursPanel, Ext.Panel);