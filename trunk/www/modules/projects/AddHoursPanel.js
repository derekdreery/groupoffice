/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @version $Id: AddHoursPanel.js 2635 2008-07-18 11:00:31Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 */


 
GO.projects.AddHoursPanel = function(config){
	
	
	if(!config)
	{
		config={};
	}
	

	this.unitsField = new GO.form.NumberField({
      name: 'units',
    	width: 40,
      allowBlank:false,
      fieldLabel: GO.projects.lang.units,
      value: GO.util.numberFormat("0",2,GO.settings.decimal_seperator, GO.settings.thousands_seperator)
	});  	      		

  var comments = new Ext.form.TextArea({
      name: 'comments',
    	anchor:'100%',
    	height:'100',
      allowBlank:true,
      fieldLabel: GO.lang.strDescription
  	});
  	
  var now = new Date();
  var date = new Ext.form.DateField({    	
			name: 'date',
			anchor:'100%',
			format: GO.settings['date_format'],
			allowBlank:false,
			fieldLabel: GO.lang['strDate'],
			value: now.format(GO.settings.date_format)
			});
  	
  
  this.selectProject = new GO.projects.SelectProject({
  	anchor:'100%'
  });
  this.selectFee = new GO.projects.SelectFee({
  	anchor:'100%'
  });
	
	

	
	config.layout='column',
	//config.border=false;
	config.cls='go-form-panel';
	config.baseParams={task: 'hours_entry'};
	//config.autoHeight=true;
	config.defaults={'border':false};
  config.items=[{
	  	layout: 'form',
	  	columnWidth:.5,
	  	items:[
	  		this.unitsField,
	    	date,
	    	this.selectProject,
	    	this.selectFee
    	]
  	},{
	  	layout: 'form',
	  	columnWidth:.5,
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
	
	GO.projects.AddHoursPanel.superclass.constructor.call(this, config);
	
	this.addEvents({'save' : true});	
}

Ext.extend(GO.projects.AddHoursPanel, Ext.form.FormPanel,{

	submit : function(){
		this.form.submit({
			url:GO.settings.modules.projects.url+'action.php',
			params: {'task' : 'save_hours'},
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
		
		GO.projects.AddHoursPanel.superclass.load.call(this, {
				url : GO.settings.modules.projects.url+'json.php',
				waitMsg:GO.lang['waitMsgLoad'],
				success:function(form, action)
				{
					this.selectProject.setRemoteText(action.result.data.project_name);	
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
		this.selectProject.selectFirst();
		this.selectFee.selectFirst();		
	},
	setHoursId : function(hours_id)
	{
		this.form.baseParams['hours_id']=hours_id;
		this.hours_id=hours_id;
		this.load();	
	}		
});