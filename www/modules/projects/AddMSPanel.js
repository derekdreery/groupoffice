/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @version $Id: AddMSPanel.js 2635 2008-07-18 11:00:31Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 */

 
GO.projects.AddMSPanel = function(config){
	
	
	if(!config)
	{
		config={};
	}
	
	
	var nameField = new Ext.form.TextField({
      name: 'name',
    	anchor:'100%',
      allowBlank:true,
      fieldLabel: GO.lang['strName']
  	});
  	
	
  var comments = new Ext.form.TextArea({
      name: 'description',
    	anchor:'100%',
    	height:'70',
      allowBlank:true,
      fieldLabel: GO.lang.strDescription
  	});
  	
  var now = new Date();
  var date = new Ext.form.DateField({    	
			name: 'due_date',
			anchor:'100%',
			format: GO.settings['date_format'],
			allowBlank:false,
			fieldLabel: 'Due',
			value: now.format(GO.settings.date_format)
			});
			
	this.selectUser = new GO.form.SelectUser({
		fieldLabel: GO.projects.lang.responsible,
		anchor:'100%'
	});
  	

	
	config.layout='column',
	//config.border=false;
	//config.cls='go-form-panel';
	config.baseParams={task: 'milestone'};
	//config.autoHeight=true;
	config.defaults={'border':false};
	config.autoHeight=true;
	
	
	
  config.items=[{
	  	layout: 'form',
	  	columnWidth:.5,
	  	cls:'go-form-panel',
	  	items:[
	  		nameField,
	  		this.selectUser,
	    	date
    	]
  	},{
	  	layout: 'form',
	  	columnWidth:.5,
	  	cls:'go-form-panel',
	  	items:[
	  		comments
    	]
  	}   	
    ];
    
  if(config.saveButton)
	{
		config.items.push({columnWidth:1, layout: 'form', bodyStyle:'padding:0 0 5px 5px', items:config.saveButton});
	}
	
	config.waitMsgTarget=true;
	
	GO.projects.AddMSPanel.superclass.constructor.call(this, config);
	
	this.addEvents({'save' : true});	
}

Ext.extend(GO.projects.AddMSPanel, Ext.form.FormPanel,{

	submit : function(){
		this.form.submit({
			url:GO.settings.modules.projects.url+'action.php',
			params: {'task' : 'save_milestone'},
			waitMsg:GO.lang['waitMsgSave'],
			success:function(form, action){
				this.reset();
				this.fireEvent('save', this);					
			},		
			failure: function(form, action) {
				Ext.MessageBox.alert(GO.lang['strError'], action.result.feedback);
			},
			scope: this
		});		
	},
	
	
	load : function(){
		
		GO.projects.AddMSPanel.superclass.load.call(this, {
				url : GO.settings.modules.projects.url+'json.php',
				waitMsg:GO.lang['waitMsgLoad'],
				success:function(form, action)
				{
					this.form.baseParams.project_id=action.result.data.project_id;
					this.selectUser.setRemoteText(action.result.data.user_name);
				},
				failure:function(form, action)
				{
					Ext.Msg.alert(GO.lang['strError'], action.result.feedback)
				},
				scope: this				
			});
		
	},
	
	reset : function(){
		
		this.form.reset();
		//this.selectProject.selectFirst();
		//this.selectFee.selectFirst();		
	},
	setMSId : function(ms_id)
	{
		this.form.baseParams['milestone_id']=ms_id;
		this.ms_id=ms_id;
		if(this.ms_id>0)
		{
			this.load();
		}	
	},
	
	setProjectId : function(project_id)
	{
		this.form.baseParams['project_id']=project_id;
		this.project_id=project_id;	
	}		
});