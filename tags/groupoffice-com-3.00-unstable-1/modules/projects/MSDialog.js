/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @version $Id: MSDialog.js 2635 2008-07-18 11:00:31Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 */


 
GO.projects.MSDialog = function(config){
	
	
	if(!config)
	{
		config={};
	}
	
	
	this.msPanel = new GO.projects.AddMSPanel();
	this.msPanel.on('save', function(){
		this.hide();
		this.fireEvent('save');   			
	}, this);
	
	var focusName = function(){
		this.unitsField.focus();		
	};
	
	
		
	
	

	config.maximizable=true;
	config.layout='fit';
	config.modal=false;
	config.resizable=false;
	config.width=600;
	config.autoHeight=true;
	config.closeAction='hide';
	config.title= GO.projects.lang.milestone;					
	config.items= this.msPanel;
	//config.focus= focusName.createDelegate(this);
	config.buttons=[{
			text: GO.lang['cmdOk'],
			handler: function(){
				this.msPanel.submit();
			},
			scope: this
		},{
			text: GO.lang['cmdClose'],
			handler: function(){
				this.hide();
			},
			scope:this
		}					
	];

	
	GO.projects.MSDialog.superclass.constructor.call(this, config);
	
	
	
	this.addEvents({'save' : true});	
}

Ext.extend(GO.projects.MSDialog, Ext.Window,{

	
	show : function (config) {
		
		if(!this.rendered)
			this.render(Ext.getBody());
				
		if(!config)
		{
			config={};
		}
		
		
		if(!config.milestone_id)
		{
			config.milestone_id=0;			
		}

		this.msPanel.setMSId(config.milestone_id);
		GO.projects.MSDialog.superclass.show.call(this);
	}
});